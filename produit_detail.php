<?php  
session_start();

if (!isset($_SESSION['user_id'])) {
    $id_produit = $_GET['id'] ?? ''; // récupère l'id du produit si présent
    header("Location: login.php?origin=produit_detail&id=$id_produit");
    exit;
}
$user_id = $_SESSION['user_id'];


$host = "localhost";
$user = "root";
$password = "";
$database = "byhande";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Gestion de l'ajout au panier
$confirmationMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produit_id = intval($_POST['produit_id']);
    $couleur = isset($_POST['couleur']) ? $_POST['couleur'] : '';
    $quantite = max(1, intval($_POST['quantite']));
    $image = $_POST['image'] ?? '';

    // Récupérer stock disponible selon présence couleur
    if ($couleur !== '') {
        // Produit avec couleur
        $stmt_stock = $conn->prepare("SELECT stock FROM produit_couleurs WHERE produit_id = ? AND couleur = ?");
        $stmt_stock->bind_param("is", $produit_id, $couleur);
        $stmt_stock->execute();
        $result_stock = $stmt_stock->get_result();
        $row_stock = $result_stock->fetch_assoc();
        $stock_disponible = $row_stock ? intval($row_stock['stock']) : 0;
    } else {
        // Produit sans couleur -> stock global
        $stmt_stock = $conn->prepare("SELECT stock FROM produits WHERE id = ?");
        $stmt_stock->bind_param("i", $produit_id);
        $stmt_stock->execute();
        $result_stock = $stmt_stock->get_result();
        $row_stock = $result_stock->fetch_assoc();
        $stock_disponible = $row_stock ? intval($row_stock['stock']) : 0;
    }

    // Initialiser le panier en session s'il n'existe pas
    $date_ajout = date('Y-m-d H:i:s');

// Vérifier si l'article existe déjà dans le panier
$stmt_check = $conn->prepare("SELECT quantite FROM panier WHERE user_id = ? AND produit_id = ? AND couleur = ?");
$stmt_check->bind_param("iis", $user_id, $produit_id, $couleur);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($row = $result_check->fetch_assoc()) {
    $quantite_existante = $row['quantite'];
    $quantite_totale = $quantite_existante + $quantite;

    if ($quantite_totale > $stock_disponible) {
        $quantite = $stock_disponible - $quantite_existante;
        if ($quantite <= 0) {
            $confirmationMessage = "Quantité maximale atteinte dans votre panier.";
        } else {
            // Mise à jour quantité
            $stmt_update = $conn->prepare("UPDATE panier SET quantite = quantite + ? WHERE user_id = ? AND produit_id = ? AND couleur = ?");
            $stmt_update->bind_param("iiis", $quantite, $user_id, $produit_id, $couleur);
            $stmt_update->execute();
            $confirmationMessage = "$quantite produit(s) ajouté(s) au panier.";
        }
    } else {
        // Mise à jour quantité normale
        $stmt_update = $conn->prepare("UPDATE panier SET quantite = quantite + ? WHERE user_id = ? AND produit_id = ? AND couleur = ?");
        $stmt_update->bind_param("iiis", $quantite, $user_id, $produit_id, $couleur);
        $stmt_update->execute();
        $confirmationMessage = "Produit ajouté au panier avec succès.";
    }

} else {
    // Insertion si produit pas encore dans le panier
    $stmt_insert = $conn->prepare("INSERT INTO panier (user_id, produit_id, couleur, quantite, date_ajout) VALUES (?, ?, ?, ?, ?)");
    $stmt_insert->bind_param("iisis", $user_id, $produit_id, $couleur, $quantite, $date_ajout);
    $stmt_insert->execute();
    $confirmationMessage = "Produit ajouté au panier.";
}

   
}
//$confirmationMessage = "Le produit a été ajouté au panier avec succès.";



// Récupération du produit à afficher
if (isset($_GET['id'])) {
    $produit_id = intval($_GET['id']);

    $stmt = $conn->prepare("SELECT * FROM produits WHERE id = ?");
    $stmt->bind_param("i", $produit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $produit = $result->fetch_assoc();

    if (!$produit) {
        echo "Produit non trouvé.";
        exit;
    }

    // Images supplémentaires
    $stmt_images = $conn->prepare("SELECT * FROM produit_images WHERE produit_id = ?");
    $stmt_images->bind_param("i", $produit_id);
    $stmt_images->execute();
    $result_images = $stmt_images->get_result();
    $images_supp = $result_images->fetch_all(MYSQLI_ASSOC);

    // Variantes couleur
    $stmt_couleurs = $conn->prepare("SELECT couleur, stock, image FROM produit_couleurs WHERE produit_id = ?");
    $stmt_couleurs->bind_param("i", $produit_id);
    $stmt_couleurs->execute();
    $result_couleurs = $stmt_couleurs->get_result();
    $variantes_couleur = $result_couleurs->fetch_all(MYSQLI_ASSOC);
} else {
    echo "Aucun produit spécifié.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détail du Produit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Vos styles ici, inchangés */
        body { background-color: #f5f0eb; font-family: Arial, sans-serif; }
       .navbar { background-color: white !important; position: sticky; top: 0; z-index: 1030; }
        .navbar .nav-link, .navbar .navbar-brand { color: #5c4033 !important; transition: all 0.3s ease; }
        .nav-link-custom:hover, .nav-link-custom:focus { color: #8b5e3c !important; font-weight: bold; }
        .nav-link-custom.active { color: #6f4e37 !important; font-weight: bold; border-bottom: 2px solid #6f4e37; }
        .container { max-width: 1000px; margin-top: 40px; background-color: #fffaf5; padding: 30px; border-radius: 16px; box-shadow: 0 0 15px rgba(0,0,0,0.08); }
        .product-title { color: #4b2e1f; }
        .product-price { color: #7b4a2d; font-size: 1.4rem; font-weight: bold; }
        .main-image {
            width: 100%;
            max-height: 400px;
            object-fit: contain;
            border-radius: 8px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        .thumbnail {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            margin: 5px;
            border: 2px solid transparent;
            transition: border-color 0.3s;
        }
        .thumbnail:hover {
            border-color: #7b4a2d;
        }
        .thumbnail.selected {
            border-color: #7b4a2d;
        }
        .color-thumbnails {
            margin-top: 10px;
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }
        .supp-images {
            margin-top: 10px;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .alert-confirmation {
            margin-top: 15px;
        }
        .btn-ajout {
            background-color: #a67658;
            border-color: #916248;
        }
        .btn-ajout:hover {
            background-color: #b28366;
        }
    </style>
</head>
<body>
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
<div class="container">
    <?php if ($confirmationMessage): ?>
        <div class="alert alert-success alert-confirmation" role="alert">
            <?= htmlspecialchars($confirmationMessage) ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Colonne image -->
        <div class="col-md-6 d-flex flex-column align-items-center">
            <?php if (!empty($variantes_couleur)): ?>
                <img id="mainImage" src="images/<?= htmlspecialchars($variantes_couleur[0]['image']) ?>" alt="Image principale" class="main-image">
                <div class="color-thumbnails" id="colorThumbnails">
                    <?php foreach ($variantes_couleur as $index => $var): ?>
                        <img 
                            src="images/<?= htmlspecialchars($var['image']) ?>" 
                            class="thumbnail <?= $index === 0 ? 'selected' : '' ?>" 
                            alt="Couleur <?= htmlspecialchars($var['couleur']) ?>" 
                            data-couleur="<?= htmlspecialchars($var['couleur']) ?>" 
                            data-stock="<?= $var['stock'] ?>"
                            onclick="selectColor(this)">
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <img id="mainImage" src="images/<?= htmlspecialchars($produit['image']) ?>" alt="Image principale" class="main-image">
            <?php endif; ?>

            <?php if (!empty($images_supp)): ?>
                <div class="supp-images">
                    <?php foreach ($images_supp as $img): ?>
                        <img src="images/<?= htmlspecialchars($img['image']) ?>" class="thumbnail" onclick="updateMainImage(this.src)" alt="Image supplémentaire">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Colonne détails -->
        <div class="col-md-6">
            <h2 class="product-title"><?= htmlspecialchars($produit['nom']) ?></h2>
            <p><?= nl2br(htmlspecialchars($produit['description'])) ?></p>
            <p class="product-price"><?= number_format($produit['prix'], 2) ?> DA</p>

            <form action="" method="post">
                <input type="hidden" name="produit_id" value="<?= $produit_id ?>">
                
                <?php if (!empty($variantes_couleur)): ?>
                    <div class="mt-3 w-100">
                        <label for="couleurSelect" class="form-label">Choisir une couleur :</label>
                        <select class="form-select" id="couleurSelect" onchange="handleColorChange(this)" name="couleur" required>
                            <option disabled>-- Sélectionner une couleur --</option>
                            <?php foreach ($variantes_couleur as $index => $var): ?>
                                <option
                                    value="<?= htmlspecialchars($var['couleur']) ?>"
                                    data-image="images/<?= htmlspecialchars($var['image']) ?>"
                                    data-stock="<?= $var['stock'] ?>"
                                    <?= $index === 0 ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($var['couleur']) ?> 
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p id="stockInfo" class="mt-2 text-muted">Stock disponible : <?= $variantes_couleur[0]['stock'] ?></p>
                    </div>
                <?php else: ?>
                    <input type="hidden" name="couleur" value="">
                <?php endif; ?>

                <div class="mb-3 mt-3">
                    <label for="quantite" class="form-label">Quantité</label>
                    <input type="number" class="form-control" name="quantite" id="quantite" value="1" min="1" max="<?= !empty($variantes_couleur) ? intval($variantes_couleur[0]['stock']) : 100 ?>" required>
                </div>

                <!-- Champ caché pour l'image sélectionnée -->
                <input type="hidden" name="image" id="selectedImage" value="<?= !empty($variantes_couleur) ? htmlspecialchars($variantes_couleur[0]['image']) : htmlspecialchars($produit['image']) ?>">

                <button type="submit" class="btn btn-ajout">Ajouter au panier</button>
            </form>
        </div>
    </div>
</div>

<script>
    // Mise à jour de l'image principale au clic sur image supplémentaire
    function updateMainImage(src) {
        document.getElementById('mainImage').src = src;
    }

    // Changer couleur sélectionnée dans la liste déroulante
    function handleColorChange(select) {
        const selectedOption = select.options[select.selectedIndex];
        const imageSrc = selectedOption.getAttribute('data-image');
        const stock = selectedOption.getAttribute('data-stock');

        document.getElementById('mainImage').src = imageSrc;
        document.getElementById('stockInfo').textContent = "Stock disponible : " + stock;

        const quantityInput = document.getElementById('quantite');
        quantityInput.max = stock;
        if (parseInt(quantityInput.value) > stock) {
            quantityInput.value = stock;
        }

        // Mettre à jour le champ caché image
        document.getElementById('selectedImage').value = imageSrc;

        // Mettre à jour les miniatures pour la couleur sélectionnée (si vous avez un système)
        updateSelectedThumbnail(selectedOption.value);
    }

    // Gérer la sélection des miniatures couleurs (pour garder visuel cohérent)
    function selectColor(imgElem) {
        const couleur = imgElem.getAttribute('data-couleur');
        const stock = imgElem.getAttribute('data-stock');

        // Mettre à jour la sélection dans le select
        const select = document.getElementById('couleurSelect');
        for (let i = 0; i < select.options.length; i++) {
            if (select.options[i].value === couleur) {
                select.selectedIndex = i;
                handleColorChange(select);
                break;
            }
        }

        // Mettre à jour bordure sélectionnée sur les miniatures
        const thumbnails = document.querySelectorAll('.color-thumbnails .thumbnail');
        thumbnails.forEach(t => t.classList.remove('selected'));
        imgElem.classList.add('selected');
    }

    // Mise à jour bordure miniature couleur sélectionnée (appelée depuis handleColorChange)
    function updateSelectedThumbnail(couleur) {
        const thumbnails = document.querySelectorAll('.color-thumbnails .thumbnail');
        thumbnails.forEach(t => {
            if (t.getAttribute('data-couleur') === couleur) {
                t.classList.add('selected');
            } else {
                t.classList.remove('selected');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const alertBox = document.querySelector('.alert-confirmation');
        if (alertBox) {
            // Cacher le message après 1 seconde (1000 millisecondes)
            setTimeout(() => {
                alertBox.style.display = 'none';
            }, 1000);
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>  