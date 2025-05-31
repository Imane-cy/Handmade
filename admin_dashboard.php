<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Tableau de bord Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f3f1ec;
      font-family: 'Segoe UI', sans-serif;
    }
     .navbar { background-color: white !important; position: sticky; top: 0; z-index: 1030; }
        .navbar .nav-link, .navbar .navbar-brand { color: #5c4033 !important; transition: all 0.3s ease; }
        .nav-link-custom:hover, .nav-link-custom:focus { color: #8b5e3c !important; font-weight: bold; }
        .nav-link-custom.active { color: #6f4e37 !important; font-weight: bold; border-bottom: 2px solid #6f4e37; }
        
    .dashboard-container {
      max-width: 900px;
      margin: 60px auto;
      padding: 40px;
      background-color: #fffdf9;
      border-radius: 20px;
      box-shadow: 0 8px 18px rgba(0,0,0,0.08);
      border: 1px solid #e8e5dc;
    }
    .dashboard-title {
      color: #6f4e37;
      font-weight: bold;
      margin-bottom: 30px;
      text-align: center;
    }
    .admin-option {
      padding: 20px;
      background-color: #fdfaf6;
      border: 1px solid #ddd0c1;
      border-radius: 15px;
      transition: all 0.3s ease;
      text-align: center;
      font-size: 1.2rem;
      color: #5a5244;
      text-decoration: none;
    }
    .admin-option:hover {
      background-color: #f2ebe2;
      color: #8b5e3c;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
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

  <div class="container dashboard-container">
    <h2 class="dashboard-title">Bienvenue, Administrateur</h2>

    <div class="row g-4">
      <div class="col-md-6">
        <a href="admin_ajout.php" class="admin-option d-block">➕ Ajouter un produit</a>
      </div>
      <div class="col-md-6">
        <a href="admin_gestion_produits.php" class="admin-option d-block">🛍 Gérer les produits</a>
      </div>
      <div class="col-md-6">
        <a href="gerercommande.php" class="admin-option d-block">👤 Gérer les comptes utilisateurs et commandes </a>
      </div>
      <div class="col-md-6">
        <a href="admin_logout.php" class="admin-option d-block">🚪 Se déconnecter</a>
      </div>
    </div>
  </div>

</body>
</html>