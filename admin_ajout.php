<?php 
$conn = new mysqli("localhost", "root", "", "byhande");

$erreur = '';
$showModal = false;

if (isset($_POST['ajouter'])) {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $categorie = $_POST['categorie'];
    $sous = $_POST['sous_categorie'];
    $mode = $_POST['mode'];
    $stock = ($mode === 'multiple') ? intval($_POST['stock_unique']) : 0;

    $imagePrincipale = $_FILES['image']['name'];

    // Vérifier s'il existe déjà un produit avec le même nom ou la même image
    $stmt = $conn->prepare("SELECT id FROM produits WHERE nom = ? OR image = ?");
    $stmt->bind_param("ss", $nom, $imagePrincipale);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $erreur = "Un produit avec ce nom ou cette image existe déjà.";
    } else {
        $tmp = $_FILES['image']['tmp_name'];
        move_uploaded_file($tmp, "images/" . $imagePrincipale);

        $stmt = $conn->prepare("INSERT INTO produits (nom, description, prix, image, categorie, sous_categorie, stock) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdsssi", $nom, $description, $prix, $imagePrincipale, $categorie, $sous, $stock);
        $stmt->execute();
        $produit_id = $stmt->insert_id;
        $stmt->close();

        if ($mode === 'couleur') {
            foreach ($_POST['couleur'] as $i => $couleur) {
                if (empty($couleur)) continue;

                $stockCouleur = intval($_POST['stock_couleur'][$i]);
                $imageNom = $_FILES['image_couleur']['name'][$i];
                $imageTmp = $_FILES['image_couleur']['tmp_name'][$i];
                move_uploaded_file($imageTmp, "images/" . $imageNom);

                $stmt = $conn->prepare("INSERT INTO produit_couleurs (produit_id, couleur, image, stock) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("issi", $produit_id, $couleur, $imageNom, $stockCouleur);
                $stmt->execute();
                $stmt->close();
            }

            // 🔥 Ajoute ici le code pour calculer et mettre à jour le stock total dans la table produits :
            $stmt = $conn->prepare("SELECT SUM(stock) FROM produit_couleurs WHERE produit_id = ?");
            $stmt->bind_param("i", $produit_id);
            $stmt->execute();
            $stmt->bind_result($stock_total_couleur);
            $stmt->fetch();
            $stmt->close();

            // Mettre à jour le stock total dans la table produits
            $stmt = $conn->prepare("UPDATE produits SET stock = ? WHERE id = ?");
            $stmt->bind_param("ii", $stock_total_couleur, $produit_id);
            $stmt->execute();
            $stmt->close();
        } elseif ($mode === 'multiple') {
            foreach ($_FILES['images_multiples']['name'] as $i => $fileName) {
                if (empty($fileName)) continue;

                $tmpName = $_FILES['images_multiples']['tmp_name'][$i];
                move_uploaded_file($tmpName, "images/" . $fileName);

                $stmt = $conn->prepare("INSERT INTO produit_images (produit_id, image) VALUES (?, ?)");
                $stmt->bind_param("is", $produit_id, $fileName);
                $stmt->execute();
                $stmt->close();
            }
        }

        $showModal = true;
    }  
header("Location: admin_ajout.php?success=1");
exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Ajouter un Produit - admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { background-color: #fdf6f0; font-family: 'Segoe UI', sans-serif; }
    .navbar { background-color: white !important; position: sticky; top: 0; z-index: 1030; }
        .navbar .nav-link, .navbar .navbar-brand { color: #5c4033 !important; transition: all 0.3s ease; }
        .nav-link-custom:hover, .nav-link-custom:focus { color: #8b5e3c !important; font-weight: bold; }
        .nav-link-custom.active { color: #6f4e37 !important; font-weight: bold; border-bottom: 2px solid #6f4e37; }
    .container { max-width: 800px; background-color: #fff9f4; padding: 30px; border-radius: 15px; box-shadow: 0 4px 8px rgba(0,0,0,0.05); }
    h2 { color: #a97457; }
    label { color: #5a3e36; }
    .btn-primary { background-color: #a97457; border-color: #a97457; }
    .btn-primary:hover { background-color: #8a5f45; }
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
  <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
  <div class="alert alert-success text-center">
    Le produit a été ajouté avec succès !
  </div>
<?php endif; ?>

  <div class="container my-5">
    <h2 class="text-center mb-4">Ajouter un Produit</h2>

<?php if (!empty($erreur)): ?>
  <div class="alert alert-danger text-center"><?php echo htmlspecialchars($erreur); ?></div>
<?php endif; ?>


    <form method="POST" enctype="multipart/form-data">
      <!-- Infos de base -->
      <div class="mb-3">
        <label class="form-label">Nom du produit</label>
        <input type="text" class="form-control" name="nom" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea class="form-control" name="description" rows="3" required></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Prix</label>
        <input type="number" step="0.01" class="form-control" name="prix" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Image principale</label>
        <input type="file" class="form-control" name="image" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Catégorie</label>
        <select class="form-select" name="categorie" id="categorie" required>
          <option value="Décor">Décor</option>
          <option value="Bijoux">Bijoux</option>
          <option value="Sacs">Sacs</option>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Sous-catégorie</label>
        <select class="form-select" name="sous_categorie" id="sous_categorie" required></select>
      </div>

      <!-- Mode d'ajout -->
      <div class="mb-4">
        <label class="form-label">Type d'ajout</label><br>
        <input type="radio" name="mode" value="couleur" checked onclick="afficherMode('couleur')"> Variantes par couleur
        <input type="radio" name="mode" value="multiple" class="ms-3" onclick="afficherMode('multiple')"> Plusieurs images
      </div>

      <!-- Couleur -->
      <div id="couleur-section">
        <h5>Couleurs disponibles</h5>
        <div id="couleurs-container">
          <div class="row mb-3 couleur-item">
            <div class="col"><input type="text" name="couleur[]" class="form-control" placeholder="Nom ou code couleur"></div>
            <div class="col"><input type="file" name="image_couleur[]" class="form-control"></div>
            <div class="col"><input type="number" name="stock_couleur[]" class="form-control" placeholder="Stock"></div>
          </div>
        </div>
        <button type="button" class="btn btn-outline-secondary mb-3" onclick="ajouterCouleur()">+ Ajouter une couleur</button>
      </div>

      <!-- Multiple -->
      <div id="multiple-section" style="display:none;">
        <div class="mb-3">
          <label class="form-label">Stock total du produit</label>
          <input type="number" class="form-control" name="stock_unique" placeholder="Stock total du produit">
        </div>

        <h5>Images supplémentaires</h5>
        <div id="images-container">
          <div class="mb-3"><input type="file" name="images_multiples[]" class="form-control"></div>
        </div>
        <button type="button" class="btn btn-outline-secondary mb-3" onclick="ajouterImage()">+ Ajouter une image</button>
      </div>

      <button type="submit" name="ajouter" class="btn btn-primary w-100">Ajouter le Produit</button>
    </form>
  </div>

<?php if (isset($showModal) && $showModal): ?>
  <div class="modal fade show" style="display:block; background:rgba(0,0,0,0.5);" id="successModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Succès</h5></div>
        <div class="modal-body text-center"><p>Le produit a été ajouté avec succès !</p></div>
        <div class="modal-footer"><a href="admin_ajout.php" class="btn btn-primary">OK</a></div>
      </div>
    </div>
  </div>
<?php endif; ?>

<script>
  const sousCategories = {
    "Décor": ["Bougie", "Textiles et Tapis", "Broderie", "Béton"],
    "Bijoux": ["Bague", "Bracelet", "Boucle d'oreille", "Chaînes"],
    "Sacs": ["Totbag", "Trousses", "Sac Laptop", "Sac au Cristal"]
  };

  const categorieSelect = document.getElementById("categorie");
  const sousCategorieSelect = document.getElementById("sous_categorie");

  function updateSousCategories() {
    const options = sousCategories[categorieSelect.value] || [];
    sousCategorieSelect.innerHTML = "";
    options.forEach(sous => {
      const option = document.createElement("option");
      option.value = sous;
      option.textContent = sous;
      sousCategorieSelect.appendChild(option);
    });
  }

  categorieSelect.addEventListener("change", updateSousCategories);
  updateSousCategories();

  function afficherMode(mode) {
    document.getElementById("couleur-section").style.display = mode === 'couleur' ? 'block' : 'none';
    document.getElementById("multiple-section").style.display = mode === 'multiple' ? 'block' : 'none';
  }

  function ajouterCouleur() {
    const container = document.getElementById("couleurs-container");
    const item = document.createElement("div");
    item.classList.add("row", "mb-3", "couleur-item");
    item.innerHTML = `
      <div class="col">
        <input type="text" name="couleur[]" class="form-control" placeholder="Nom ou code couleur">
      </div>
      <div class="col">
        <input type="file" name="image_couleur[]" class="form-control">
      </div>
      <div class="col">
        <input type="number" name="stock_couleur[]" class="form-control" placeholder="Stock">
      </div>
    `;
    container.appendChild(item);
  }

  function ajouterImage() {
    const container = document.getElementById("images-container");
    const div = document.createElement("div");
    div.classList.add("mb-3");
    div.innerHTML = `<input type="file" name="images_multiples[]" class="form-control">`;
    container.appendChild(div);
  }
</script>
</body>
</html>
