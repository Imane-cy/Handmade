<?php
session_start();
include("connexion.php");

$user_id = $_SESSION['user_id'] ?? 1; // Remplace 1 par la vraie gestion utilisateur

// --- SUPPRESSION D'UN ARTICLE DU PANIER ---
if (isset($_GET['supprimer'])) {
    $panier_id = (int)$_GET['supprimer'];

    $delete = $conn->prepare("DELETE FROM panier WHERE id = ? AND user_id = ?");
    $delete->bind_param("ii", $panier_id, $user_id);
    $delete->execute();

    header("Location: panier.php");
    exit;
}

// --- MISE À JOUR QUANTITÉ (avec contrôle de stock par couleur si besoin) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'], $_POST['new_quantite'])) {
    $panier_id = (int)$_POST['update_id'];
    $new_quantite = max(1, (int)$_POST['new_quantite']);

    // Vérifier le stock disponible (par couleur si applicable)
    $get_stock = $conn->prepare("
        SELECT 
            pr.stock, 
            COALESCE(pc.stock, pr.stock) AS stock_couleur,
            p.couleur
        FROM panier p
        JOIN produits pr ON p.produit_id = pr.id
        LEFT JOIN produit_couleurs pc ON pc.produit_id = pr.id AND pc.couleur = p.couleur
        WHERE p.id = ? AND p.user_id = ?
    ");
    $get_stock->bind_param("ii", $panier_id, $user_id);
    $get_stock->execute();
    $stock_result = $get_stock->get_result();

    if ($stock_row = $stock_result->fetch_assoc()) {
        $stock_disponible = (int)$stock_row['stock_couleur'];

        if ($new_quantite > $stock_disponible) {
            $_SESSION['erreur_stock'] = "La quantité demandée dépasse le stock disponible pour ce produit.";
        } else {
            $update = $conn->prepare("UPDATE panier SET quantite = ? WHERE id = ? AND user_id = ?");
            $update->bind_param("iii", $new_quantite, $panier_id, $user_id);
            $update->execute();
        }
    }

    header("Location: panier.php");
    exit;
}

// --- RÉCUPÉRATION DES ARTICLES DU PANIER ---
$sql = "
    SELECT 
        p.id AS panier_id, p.quantite, p.couleur, 
        pr.id AS produit_id, pr.nom, pr.prix, pr.stock,
        COALESCE(pc.image, (SELECT image FROM produit_images WHERE produit_id = pr.id LIMIT 1)) AS image,
        COALESCE(pc.stock, pr.stock) AS stock_couleur
    FROM panier p
    JOIN produits pr ON p.produit_id = pr.id
    LEFT JOIN produit_couleurs pc 
        ON pc.produit_id = pr.id AND pc.couleur = p.couleur
    WHERE p.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
$total = 0;
while ($row = $result->fetch_assoc()) {
    $row['sous_total'] = $row['prix'] * $row['quantite'];
    $total += $row['sous_total'];
    $items[] = $row;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
   <style>
        body {
            background-color: #fcf8f3;
            font-family: 'Segoe UI', sans-serif;
        }
        h2 {
            color: #5e3d2c;
        }
        .table {
            background-color: #fff8f1;
            border: 1px solid #e0cfc0;
        }
        .table thead {
            background-color: #e5d0b5;
            color: #3b2a1a;
        }
        .table td, .table th {
            vertical-align: middle;
        }
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #d6bfa8;
        }
        .btn-danger {
            background-color: #8b4b39;
            border-color: #7a3f30;
        }
        .btn-danger:hover {
            background-color: #a35644;
        }
        .btn-success {
            background-color: #a67658;
            border-color: #916248;
        }
        .btn-success:hover {
            background-color: #b28366;
        }
        .form-control-sm {
            border-color: #cbb09e;
        }
        .alert-warning {
            background-color: #fce9dd;
            color: #8a4b3c;
        }
        .alert-info {
            background-color: #f3e5d8;
            color: #6b4b34;
        }
        .total-section h4 {
            color: #5e3d2c;
        }
        .navbar { background-color: white !important; position: sticky; top: 0; z-index: 1030; }
        .navbar .nav-link, .navbar .navbar-brand { color: #5c4033 !important; transition: all 0.3s ease; }
        .nav-link-custom:hover, .nav-link-custom:focus { color: #8b5e3c !important; font-weight: bold; }
        .nav-link-custom.active { color: #6f4e37 !important; font-weight: bold; border-bottom: 2px solid #6f4e37; }
  
    </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light shadow-sm">
    <div class="container position-relative">
      <img src="logo.jpg" alt="logo" width="120" height="auto">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <?php 
$current_page = basename($_SERVER['PHP_SELF']); 
?>

<li class="nav-item">
  <a class="nav-link nav-link-custom <?= $current_page == 'index.php' ? 'active' : '' ?>" href="accueil.php">Accueil</a>
</li>
<li class="nav-item">
  <a class="nav-link nav-link-custom <?= $current_page == 'produits.php' ? 'active' : '' ?>" href="accueil.php#produits">Produits</a>
</li>
<li class="nav-item">
  <a class="nav-link nav-link-custom <?= $current_page == 'contact.php' ? 'active' : '' ?>" href="accueil.php#contact">Contact</a>
</li>
<li class="nav-item">
  <a class="nav-link d-flex align-items-center nav-link-custom <?= $current_page == 'login.php' ? 'active' : '' ?>" href="login.php">
    <i class="bi bi-person-circle me-1"></i> Connexion
  </a>
</li>
<li class="nav-item">
  <a class="nav-link d-flex align-items-center nav-link-custom <?= $current_page == 'panier.php' ? 'active' : '' ?>" href="panier.php">
    <i class="bi bi-cart me-1"></i> Panier
  </a>
</li>
        </ul>
      </div>
    </div>
  </nav>

<div class="container my-4">
    <h2 class="mb-4 text-center">🛒Mon Panier</h2>

    <?php if (!empty($_SESSION['erreur_stock'])): ?>
        <div class="alert alert-warning text-center">
            <?= $_SESSION['erreur_stock']; unset($_SESSION['erreur_stock']); ?>
        </div>
    <?php endif; ?>

    <?php if (count($items) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th>Image</th>
                        <th>Nom</th>
                        <th>Couleur</th>
                        <th>Quantité</th>
                        <th>Prix</th>
                        <th>Sous-total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><img src="images/<?= htmlspecialchars($item['image']) ?>" alt="Produit" class="product-image"></td>
                            <td><?= htmlspecialchars($item['nom']) ?></td>
                            <td><?= htmlspecialchars($item['couleur']) ?></td>
                            <td>
                                <form method="post" class="d-flex justify-content-center align-items-center">
                                    <input type="hidden" name="update_id" value="<?= $item['panier_id'] ?>">
                                    <?php 
                                        $stock_disponible = isset($item['stock_couleur']) ? $item['stock_couleur'] : $item['stock'];
                                    ?>
                                    <input type="number" name="new_quantite" value="<?= $item['quantite'] ?>" 
                                           min="1" max="<?= $stock_disponible ?>" 
                                           class="form-control form-control-sm me-2" style="width: 70px;"
                                           onchange="this.form.submit()">
                                </form>
                            </td>
                            <td><?= number_format($item['prix'], 2, ',', ' ') ?> DA</td>
                            <td class="sous-total"><?= number_format($item['sous_total'], 2, ',', ' ') ?> DA</td>
                            <td>
                                <a href="panier.php?supprimer=<?= $item['panier_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cet article ?');">🗑</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <h4 class="text-end">Total : <span id="total"><?= number_format($total, 2, ',', ' ') ?> DA</span></h4>
        <div class="text-end">
            <a href="paiement.php" class="btn btn-success">Passer au paiement</a>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">Votre panier est vide.</div>
    <?php endif; ?>
</div>

<script>
document.querySelectorAll('input[name="new_quantite"]').forEach(input => {
    input.addEventListener('input', () => {
        const row = input.closest('tr');
        const prix = parseFloat(row.querySelectorAll('td')[4].textContent.replace(' DA', '').replace(/\s/g, '').replace(',', '.'));
        const qte = parseInt(input.value) || 1;
        const sous_total = prix * qte;
        row.querySelector('.sous-total').textContent = sous_total.toFixed(2).replace('.', ',') + ' DA';

        let total = 0;
        document.querySelectorAll('.sous-total').forEach(td => {
            total += parseFloat(td.textContent.replace(' DA', '').replace(/\s/g, '').replace(',', '.'));
        });
        document.getElementById('total').textContent = total.toFixed(2).replace('.', ',') + ' DA';
    });
});
</script>
</body>
</html>
