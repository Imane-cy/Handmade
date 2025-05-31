<?php 
session_start();
require_once("connexion.php");

// Récupérer tous les produits
$sql = "SELECT * FROM produits";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Gestion des produits - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f3f1ec;
      font-family: 'Segoe UI', sans-serif;
      padding: 30px;
    }
      .navbar { background-color: white !important; position: sticky; top: 0; z-index: 1030; }
        .navbar .nav-link, .navbar .navbar-brand { color: #5c4033 !important; transition: all 0.3s ease; }
        .nav-link-custom:hover, .nav-link-custom:focus { color: #8b5e3c !important; font-weight: bold; }
        .nav-link-custom.active { color: #6f4e37 !important; font-weight: bold; border-bottom: 2px solid #6f4e37; }
        

    h2 {
      color: #6f4e37;
      margin-bottom: 30px;
      text-align: center;
    }
    table {
      background-color: #fffdf9;
      border-radius: 15px;
      box-shadow: 0 8px 18px rgba(0,0,0,0.07);
      border: 1px solid #e8e5dc;
    }
    th, td {
      vertical-align: middle !important;
      text-align: center;
      padding: 12px 15px;
      color: #5a5244;
    }
    th {
      background-color: #f7f3ec;
      font-weight: 600;
      font-size: 1rem;
    }
    .btn-modifier {
      background-color: #ffae42;
      color: white;
      border-radius: 12px;
      border: none;
      padding: 8px 18px;
      font-weight: 600;
      box-shadow: 0 3px 6px rgba(255, 174, 66, 0.4);
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
      text-decoration: none;
      display: inline-block;
    }
    .btn-modifier:hover {
      background-color: #e69300;
      box-shadow: 0 5px 12px rgba(230, 147, 0, 0.6);
      color: #fff;
    }
    .btn-supprimer {
      background-color: #e55353;
      color: white;
      border-radius: 12px;
      border: none;
      padding: 8px 18px;
      font-weight: 600;
      box-shadow: 0 3px 6px rgba(229, 83, 83, 0.5);
      transition: background-color 0.3s ease, box-shadow 0.3s ease;
      text-decoration: none;
      display: inline-block;
    }
    .btn-supprimer:hover {
      background-color: #b32424;
      box-shadow: 0 5px 12px rgba(179, 36, 36, 0.7);
      color: #fff;
    }
    img.product-img {
      max-width: 100px;
      height: auto;
      border-radius: 10px;
      border: 1px solid #ddd0c1;
    }
    .color-box {
      display: inline-block;
      width: 20px;
      height: 20px;
      border-radius: 50%;
      margin-right: 5px;
      border: 1px solid #aaa;
    }
  </style>
</head>
<body> <!-- Navbar -->
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
    <h2>Gestion des produits</h2>
    <table class="table table-hover align-middle">
      <thead>
        <tr>
          <th>Nom</th>
          <th>Description</th>
          <th>Prix (DA)</th>
          <th>Stock</th>
          <th>Images</th>
          <th>Modifier</th>
          <th>Supprimer</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()) : ?>
          <tr>
            <td><?= htmlspecialchars($row['nom']) ?></td>
            <td><?= htmlspecialchars($row['description']) ?></td>
            <td><?= number_format($row['prix'], 2, ',', ' ') ?></td>
            <td><?= $row['stock'] ?></td>
            <td>
<?php
if (!is_null($row['product_color'])) {
    // Récupérer toutes les couleurs associées
    $stmt = $conn->prepare("SELECT * FROM produit_couleurs WHERE produit_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $row['id']);
        $stmt->execute();
        $result_colors = $stmt->get_result();
        while ($color = $result_colors->fetch_assoc()) {
            echo '<div>';
            echo '<span class="color-box" style="background-color:' . htmlspecialchars($color['couleur']) . ';"></span>';
            if (!empty($color['image'])) {
                echo '<img src="images/' . htmlspecialchars($color['image']) . '" class="product-img" alt="Couleur image" />';
            }
            echo ' Stock : ' . htmlspecialchars($color['stock']);
            echo '</div>';
        }
        $stmt->close();
    } else {
        echo "Erreur lors de la préparation de la requête pour les couleurs.";
    }
} else {
    // Afficher uniquement l'image principale (produits.image)
    if (!empty($row['image'])) {
        echo '<img src="images/' . htmlspecialchars($row['image']) . '" class="product-img" alt="Image principale" />';
    } else {
        echo 'Pas d\'image';
    }
}
?>

</td>

            <td>
              <a href="admin_modifier_produit.php?id=<?= $row['id'] ?>" class="btn-modifier">Modifier</a>
            </td>
            <td>
              <a href="admin_supprimer_produit.php?id=<?= $row['id'] ?>" class="btn-supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">Supprimer</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
