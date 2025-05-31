<?php
session_start();
$conn = new mysqli("localhost", "root", "", "byhande");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Ne pas inclure les utilisateurs dont le rôle est 'admin'
$result = $conn->query("SELECT * FROM utilisateurs WHERE role != 'admin' ORDER BY date_inscription DESC");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des Comptes</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #fdf6f0;
      font-family: 'Segoe UI', sans-serif;
    }
    .navbar { background-color: white !important; position: sticky; top: 0; z-index: 1030; }
        .navbar .nav-link, .navbar .navbar-brand { color: #5c4033 !important; transition: all 0.3s ease; }
        .nav-link-custom:hover, .nav-link-custom:focus { color: #8b5e3c !important; font-weight: bold; }
        .nav-link-custom.active { color: #6f4e37 !important; font-weight: bold; border-bottom: 2px solid #6f4e37; }
    .card {
      background-color: #fff9f4;
      border: none;
      border-radius: 15px;
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.05);
    }
    .table thead {
      background-color: #a97457;
      color: #fff;
    }
    .table tbody tr:hover {
      background-color: #f5e9df;
    }
    .title-icon {
      color: #a97457;
      margin-right: 10px;
    }
    h2 {
      color: #a97457;
    }
    .btn-facture {
      background-color: #a97457;
      color: white;
      border-radius: 20px;
      padding: 5px 15px;
    }
    .btn-facture:hover {
      background-color: #8a5f45;
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
  <div class="container my-5">
    <div class="card p-4">
      <div class="d-flex align-items-center mb-4">
        <i class="bi bi-people-fill fs-3 title-icon"></i>
        <h2 class="m-0">Utilisateurs inscrits</h2>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th scope="col">Prénom</th>
              <th scope="col">Nom</th>
              <th scope="col">Email</th>
              <th scope="col">Date d'inscription</th>
              <th scope="col">Factures</th>
            </tr>
          </thead>
          <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
              <?php
                // Vérifier s'il existe au moins une facture pour cet utilisateur
                $id_user = $row['id'];
                $facture_check = $conn->query("SELECT COUNT(*) AS total FROM factures WHERE id_user = $id_user");
                $has_facture = $facture_check && $facture_check->fetch_assoc()['total'] > 0;
              ?>
              <tr>
                <td><?= htmlspecialchars($row['prenom']) ?></td>
                <td><?= htmlspecialchars($row['nom']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= $row['date_inscription'] ?></td>
                <td>
                  <?php if ($has_facture): ?>
                    <a href="facture_utilisateur.php?id_user=<?= $id_user ?>" class="btn btn-facture btn-sm">Voir</a>
                  <?php else: ?>
                    <span class="text-muted">Aucune facture</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>
