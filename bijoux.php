<?php
// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "byhande");
if ($conn->connect_error) {
    die("Échec de la connexion: " . $conn->connect_error);
}

// Requête pour récupérer tous les produits de la catégorie "Bijoux"
$query = "
    SELECT id, nom, description, prix, image
    FROM produits
    WHERE categorie = 'Bijoux'
";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bijoux - ByHande</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    a {
      text-decoration: none;
    }
    body {
      background-color: #f5f0eb;
    }
    .navbar {
      background-color: white !important;
      position: sticky;
      top: 0;
      z-index: 1030;
    }

    .navbar .nav-link,
    .navbar .navbar-brand {
      color: #5c4033 !important;
      transition: all 0.3s ease;
    }

    .nav-link-custom:hover,
    .nav-link-custom:focus {
      color: #8b5e3c !important;
      font-size: 1.1rem;
      font-weight: bold;
    }
    hr{
      border: none;          /* Supprime la bordure par défaut */
  height: 5px;           /* Épaisseur de la ligne */
  background-color: #6f4e37;  /* Couleur personnalisée */
  margin: 20px 0; 
    }
    .nav-link-custom.active {
      color: #6f4e37 !important;
      font-weight: bold;
      border-bottom: 2px solid #6f4e37;
    }

    .categories {
      padding: 60px 20px;
      background-color: #f5f0eb;
    }

    .category-circle {
      width: 180px;
      height: 180px;
      border-radius: 50%;
      overflow: hidden;
      margin: auto;
      border: 4px solid #8b5e3c;
      transition: transform 0.3s ease;
    }

    .category-circle img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .category-title {
      margin-top: 10px;
      color: #5c4033;
      font-weight: bold;
      font-size: 1.2rem;
    }

    .category-circle:hover {
      transform: scale(1.05);
      cursor: pointer;
    }

    .product-card {
      background-color: white;
      border: 1px solid #ddd;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
    }
    .product-card:hover {
      transform: scale(1.02);
      cursor: pointer;
    }
    .product-card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
    }
    .product-info {
      padding: 15px;
    }
    .product-title {
      font-size: 1.2rem;
      font-weight: bold;
      color: #5c4033;
    }
    .product-description {
      font-size: 0.95rem;
      color: #555;
      margin-top: 5px;
    }
    .product-price {
      color: #8b5e3c;
      font-size: 1.1rem;
      margin-top: 10px;
    }

    .footer {
      background-color: #4b3621;
      color: white;
      padding: 20px 0;
      text-align: center;
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

  <!-- 🔸 Sous-catégories Bijoux -->
  <section class="categories text-center">
    <div class="container">
      <h2 class="mb-5">Bijoux - Sous-catégories</h2>
      <div class="row justify-content-center g-4">
        <div class="col-md-3">
          <a href="produits.php?cat=Bijoux&sous=Bague">
            <div class="category-circle">
              <img src="bague.png" alt="Bague">
            </div>
            <div class="category-title">Bague</div>
          </a>
        </div>
        <div class="col-md-3">
          <a href="produits.php?cat=Bijoux&sous=Bracelet">
            <div class="category-circle">
              <img src="bracelet.png" alt="Bracelet">
            </div>
            <div class="category-title">Bracelet</div>
          </a>
        </div>
        <div class="col-md-3">
          <a href="produits.php?cat=Bijoux&sous=Boucle d'oreille">
            <div class="category-circle">
              <img src="boucle.png" alt="Boucle d'oreille">
            </div>
            <div class="category-title">Boucle d'oreille</div>
          </a>
        </div>
        <div class="col-md-3">
          <a href="produits.php?cat=Bijoux&sous=chaines">
            <div class="category-circle">
              <img src="chaine.png" alt="Chaînes">
            </div>
            <div class="category-title">Chaînes</div>
          </a>
        </div>
      </div>
    </div>
  </section>


  <!-- Produits Bijoux -->
  <section class="container my-5">
    <hr>
    <div class="row g-4">
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="col-md-3">
            <a href="produit_detail.php?id=<?= $row['id'] ?>">
              <div class="product-card">
                <img src="images/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['nom']) ?>">
                <div class="product-info">
                  <div class="product-title"><?= htmlspecialchars($row['nom']) ?></div>
                  <div class="product-description"><?= htmlspecialchars($row['description']) ?></div>
                  <div class="product-price"><?= htmlspecialchars($row['prix']) ?> DZD</div>
                </div>
              </div>
            </a>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="text-center">Aucun produit trouvé dans la catégorie Bijoux.</p>
      <?php endif; ?>
    </div>
  </section>

  

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$conn->close();
?>
