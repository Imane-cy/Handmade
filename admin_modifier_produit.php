<?php 
session_start();
include("connexion.php");

$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID produit manquant.");
}


// Gestion de la photo principale
if (isset($_FILES['image_principale']) && $_FILES['image_principale']['error'] === UPLOAD_ERR_OK) {
    $tmp_name = $_FILES['image_principale']['tmp_name'];
    $filename = uniqid() . "_" . basename($_FILES['image_principale']['name']);
    $target_dir = "images/";
    $target_file = $target_dir . $filename;

    if (move_uploaded_file($tmp_name, $target_file)) {
        // Supprimer l'ancienne image principale (optionnel, à faire si fichier existant et pas image par défaut)
        if (!empty($produit['image']) && file_exists($target_dir . $produit['image'])) {
            unlink($target_dir . $produit['image']);
        }
        // Mettre à jour la table produits avec le nouveau nom de fichier
        $stmt = $conn->prepare("UPDATE produits SET image=? WHERE id=?");
        $stmt->bind_param("si", $filename, $id);
        $stmt->execute();
    }
}

if (!empty($_POST['stock'])) {
    foreach ($_POST['stock'] as $couleur_id => $new_stock) {
        $new_stock = intval($new_stock);
        $stmt = $conn->prepare("UPDATE produit_couleurs SET stock=? WHERE id=? AND produit_id=?");
        $stmt->bind_param("iii", $new_stock, $couleur_id, $id);
        $stmt->execute();
    }
    
    // Calculer le stock total du produit en sommant tous les stocks des couleurs
    $stmt = $conn->prepare("SELECT SUM(stock) as total_stock FROM produit_couleurs WHERE produit_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $total_stock = $row['total_stock'] ?? 0;

    // Mettre à jour le stock total dans la table produits
    $stmt = $conn->prepare("UPDATE produits SET stock=? WHERE id=?");
    $stmt->bind_param("ii", $total_stock, $id);
    $stmt->execute();
}



// Récupération du produit
$stmt = $conn->prepare("SELECT * FROM produits WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$produit = $result->fetch_assoc();
if (!$produit) {
    die("Produit introuvable.");
}

// Récupération des couleurs du produit
$stmt = $conn->prepare("SELECT * FROM produit_couleurs WHERE produit_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$couleurs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Récupération des images du produit (si pas de couleurs)
$stmt = $conn->prepare("SELECT * FROM produit_images WHERE produit_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$cotes_images = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Traitement des modifications globales
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_produit'])) {
    $nom = $_POST['nom'] ?? '';
    $description = $_POST['description'] ?? '';
    $prix = floatval($_POST['prix'] ?? 0);
    $stmt = $conn->prepare("UPDATE produits SET nom=?, description=?, prix=? WHERE id=?");
    $stmt->bind_param("ssdi", $nom, $description, $prix, $id);
    $stmt->execute();

    if (!empty($_POST['stock'])) {
        foreach ($_POST['stock'] as $couleur_id => $new_stock) {
            $new_stock = intval($new_stock);
            $stmt = $conn->prepare("UPDATE produit_couleurs SET stock=? WHERE id=? AND produit_id=?");
            $stmt->bind_param("iii", $new_stock, $couleur_id, $id);
            $stmt->execute();
        }
    }

    if (!empty($couleurs) && !empty($_POST['nom_couleur']) && isset($_FILES['image_couleur'])) {
        $nom_couleur = $_POST['nom_couleur'];
        $stock = intval($_POST['stock_couleur'] ?? 0);
        $image_couleur = null;
        if ($_FILES['image_couleur']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['image_couleur']['tmp_name'];
            $filename = uniqid() . "_" . basename($_FILES['image_couleur']['name']);
            $target_dir = "images/";
            $target_file = $target_dir . $filename;
            move_uploaded_file($tmp_name, $target_file);
            $image_couleur = $filename;
        }
        $stmt = $conn->prepare("INSERT INTO produit_couleurs (produit_id, couleur, image, stock) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("issi", $id, $nom_couleur, $image_couleur, $stock);
        $stmt->execute();
    }

    if (empty($couleurs) && isset($_FILES['image_cote']) && $_FILES['image_cote']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['image_cote']['tmp_name'];
        $filename = uniqid() . "_" . basename($_FILES['image_cote']['name']);
        $target_dir = "images/";
        $target_file = $target_dir . $filename;
        move_uploaded_file($tmp_name, $target_file);
        $stmt = $conn->prepare("INSERT INTO produit_images (produit_id, image) VALUES (?, ?)");
        $stmt->bind_param("is", $id, $filename);
        $stmt->execute();
    }

    header("Location: admin_modifier_produit.php?id=$id");
    exit;
}

// Suppression couleur
if (isset($_POST['supprimer_couleur_id'])) {
    $couleur_id = intval($_POST['supprimer_couleur_id']);
    $stmt = $conn->prepare("DELETE FROM produit_couleurs WHERE id = ? AND produit_id = ?");
    $stmt->bind_param("ii", $couleur_id, $id);
    $stmt->execute();
    header("Refresh:0");
    exit;
}

// Suppression image côté
if (isset($_POST['supprimer_image_id'])) {
    $image_id = intval($_POST['supprimer_image_id']);
    $stmt = $conn->prepare("SELECT image FROM produit_images WHERE id = ? AND produit_id = ?");
    $stmt->bind_param("ii", $image_id, $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $filepath = "images/" . $row['image'];
        if (file_exists($filepath)) unlink($filepath);
    }
    $stmt = $conn->prepare("DELETE FROM produit_images WHERE id=? AND produit_id=?");
    $stmt->bind_param("ii", $image_id, $id);
    $stmt->execute();
    header("Refresh:0");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Produit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
    .navbar { background-color: white !important; position: sticky; top: 0; z-index: 1030; }
        .navbar .nav-link, .navbar .navbar-brand { color: #5c4033 !important; transition: all 0.3s ease; }
        .nav-link-custom:hover, .nav-link-custom:focus { color: #8b5e3c !important; font-weight: bold; }
        .nav-link-custom.active { color: #6f4e37 !important; font-weight: bold; border-bottom: 2px solid #6f4e37; }
        

    .btn-custom {
    background-color: #6f4e37;
    border-color: #6f4e37;
    color: white;
}

.btn-custom:hover {
    background-color: #56392a;
    border-color: #56392a;
    color: white;
}
</style>
</head>
<body class="container my-4">
     <!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light shadow-sm">
    <div class="container-fluid px-5">
        <img src="logo.jpg" alt="logo" width="120" height="auto">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link nav-link-custom <?= ($currentPage == 'accueil.php') ? 'active' : '' ?>" href="accueil.php">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-custom <?= ($currentPage == 'accueil.php#produits') ? 'active' : '' ?>" href="accueil.php#produits">Produits</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-custom <?= ($currentPage == 'accueil.php#contact') ? 'active' : '' ?>" href="accueil.php#contact">Contact</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center nav-link-custom <?= ($currentPage == 'login.php') ? 'active' : '' ?>" href="login.php"><i class="bi bi-person-circle me-1"></i> Connexion</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center nav-link-custom <?= ($currentPage == 'panier.php') ? 'active' : '' ?>" href="panier.php"><i class="bi bi-cart me-1"></i> Panier</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<h1>Modifier le produit : <?= htmlspecialchars($produit['nom']) ?></h1>

<form method="post" enctype="multipart/form-data" class="mb-4">
    <div class="mb-3">
        <label>Nom</label>
        <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($produit['nom']) ?>">
    </div>
    <div class="mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control"><?= htmlspecialchars($produit['description']) ?></textarea>
    </div>
    <div class="mb-3">
        <label>Prix</label>
        <input type="number" step="0.01" name="prix" class="form-control" value="<?= htmlspecialchars($produit['prix']) ?>">
    </div>
    <div class="mb-3">
    <label>Photo principale du produit</label><br>
    <img src="images/<?= htmlspecialchars($produit['image']) ?>" alt="Image actuelle" width="120" class="mb-2"><br>
    <input type="file" name="image_principale" accept="image/*" class="form-control">
</div>


    <?php if (!empty($couleurs)): ?>
        <h3>Couleurs du produit</h3>
        <ul class="list-group mb-3">
            <?php foreach ($couleurs as $c): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong><?= htmlspecialchars($c['couleur']) ?></strong>
                        <?php if (!empty($c['image'])): ?>
                            <img src="images/<?= htmlspecialchars($c['image']) ?>" width="40">
                        <?php endif; ?>
                        <input type="number" name="stock[<?= $c['id'] ?>]" value="<?= $c['stock'] ?>" min="0" style="width:80px;">
                    </div>
                    <button name="supprimer_couleur_id" value="<?= $c['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cette couleur ?');">🗑</button>
                </li>
            <?php endforeach; ?>
        </ul>

        <h4>Ajouter une nouvelle couleur</h4>
        <div class="mb-3">
            <label>Nom de la couleur</label>
            <input type="text" name="nom_couleur" placeholder="Nom de la couleur" class="form-control">
        </div>
        <div class="mb-3">
            <input type="number" name="stock_couleur" placeholder="Stock" class="form-control">
        </div>
        <div class="mb-3">
            <label>Image de la couleur</label>
            <input type="file" name="image_couleur" accept="image/*" class="form-control">
        </div>

    <?php else: ?>
        <h3>Images du produit (pas de couleurs)</h3>
        <ul class="list-group mb-3">
            <?php foreach ($cotes_images as $img): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <img src="images/<?= htmlspecialchars($img['image']) ?>" width="60">
                 <button name="supprimer_couleur_id" value="<?= $c['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette couleur ?')">Supprimer</button>

                </li>
            <?php endforeach; ?>
        </ul>
        <div class="mb-3">
            <label>Ajouter une image côté</label>
            <input type="file" name="image_cote" class="form-control" accept="image/*">
        </div>
    <?php endif; ?>

    <button type="submit" name="modifier_produit" class="btn btn-custom btn-lg">Confirmer toutes les modifications</button>


</form>
</body>
</html>          