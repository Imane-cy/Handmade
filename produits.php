<?php
// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "byhande");
if ($conn->connect_error) {
    die("Échec de la connexion: " . $conn->connect_error);
}

// Récupérer la catégorie et sous-catégorie de l'URL
$categorie = isset($_GET['cat']) ? $_GET['cat'] : '';
$sousCategorie = isset($_GET['sous']) ? $_GET['sous'] : '';

// Préparer la requête avec jointure pour récupérer une image de couleur si l’image principale est vide
$query = "
    SELECT p.id, p.nom, p.prix,
           CASE 
               WHEN p.image IS NOT NULL AND p.image != '' THEN p.image
               ELSE pc.image
           END AS image
    FROM produits p
    LEFT JOIN (
        SELECT produit_id, image 
        FROM produit_couleurs 
        WHERE image IS NOT NULL AND image != ''
        GROUP BY produit_id
    ) pc ON p.id = pc.produit_id
    WHERE p.categorie = ? AND p.sous_categorie = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $categorie, $sousCategorie);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Produits</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    a { text-decoration: none; }
    html { scroll-behavior: smooth; }
    body {
      background-color: #f5f0eb;
      font-family: Arial, sans-serif;
    }
    .navbar { background-color: white !important; position: sticky; top: 0; z-index: 1030; }
        .navbar .nav-link, .navbar .navbar-brand { color: #5c4033 !important; transition: all 0.3s ease; }
        .nav-link-custom:hover, .nav-link-custom:focus { color: #8b5e3c !important; font-weight: bold; }
        .nav-link-custom.active { color: #6f4e37 !important; font-weight: bold; border-bottom: 2px solid #6f4e37; }
        
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
    .product-price {
      color: #8b5e3c;
      font-size: 1.1rem;
      margin-top: 10px;
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
  <div class="container py-5">
    <h2 class="text-center mb-4">

Produits - <?= htmlspecialchars($categorie) ?> > <?= htmlspecialchars($sousCategorie) ?>
    </h2>
    <div class="row g-4">
      <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
          <div class="col-md-4">
            <div class="product-card">
              <a href="produit_detail.php?id=<?= $row['id']; ?>">
                <img src="images/<?= htmlspecialchars($row['image']); ?>" alt="<?= htmlspecialchars($row['nom']); ?>">
              </a>
              <div class="product-info">
                <div class="product-title"><?= htmlspecialchars($row['nom']) ?></div>
                <div class="product-price"><?= htmlspecialchars($row['prix']) ?> DZD</div>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="col-12 text-center">
          <p>Aucun produit trouvé pour cette sous-catégorie.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
<script>
    // Function to update the stock max based on the selected color
    function updateStockMax() {
      const select = document.getElementById('product_color');
      const selected = select.options[select.selectedIndex];
      const stock = selected.getAttribute('data-stock');
      const quantiteInput = document.getElementById('quantite');
      quantiteInput.max = stock;
    }

    // Function to update the main image based on the selected thumbnail
    function updateMainImage(imgElement) {
      const imageSrc = imgElement.src;
      const couleur = imgElement.getAttribute('data-couleur');
      const stock = imgElement.getAttribute('data-stock');

      document.getElementById('mainImage').src = imageSrc;

      const select = document.getElementById('product_color');
      for (let i = 0; i < select.options.length; i++) {
        if (select.options[i].value === couleur) {
          select.selectedIndex = i;
          updateStockMax();
          break;
        }
      }

      document.querySelectorAll('.thumbnail').forEach(img => img.classList.remove('selected'));
      imgElement.classList.add('selected');
    }

    // Function to update the main image based on the selected color from the dropdown
    function updateImageFromColorSelect() {
      const select = document.getElementById('product_color');
      const selectedColor = select.options[select.selectedIndex];
      const selectedColorImage = selectedColor.getAttribute('data-image'); // Ensure you store the image path in the data-image attribute

      document.getElementById('mainImage').src = selectedColorImage;
      updateStockMax(); // Ensure the stock max is also updated based on the selected color
    }

    // Event listener for when the color is selected from the dropdown
    document.getElementById('product_color').addEventListener('change', updateImageFromColorSelect);

    // Ensure the stock max is updated on page load based on the selected color
    window.addEventListener('DOMContentLoaded', updateStockMax);
  </script>
</body>
</html>