<?php
session_start();

// Vérification que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


$conn = new mysqli("localhost", "root", "", "byhande");
if ($conn->connect_error) {
    die("Échec de la connexion: " . $conn->connect_error);
}

// Récupération des infos utilisateur
$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT nom, prenom, email FROM utilisateurs WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    // Utilisateur introuvable, on détruit la session et redirige
    session_destroy();
    header("Location: login.php");
    exit();
}

$nomComplet = htmlspecialchars($user['prenom'] . ' ' . $user['nom']);
$email = htmlspecialchars($user['email']);

$totalGeneral = 0;

?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Paiement</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    body { background-color: #f8f9fa; }
    .container {
      display: flex; justify-content: space-between; margin-top: 40px; flex-wrap: wrap;
    }
    .panier-container, .payment-container {
      background-color: white; padding: 20px; border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 48%; max-width: 600px; margin-bottom: 30px;
    }
    .total { font-size: 1.4rem; font-weight: bold; color: #6f4e37; }
    .btn-payer {
      background-color: #8b5e3c; color: white; font-size: 1.2rem;
      padding: 10px 25px; border-radius: 5px;
    }
    .btn-payer:hover { background-color: #6f4e37; }
    .product-image { max-width: 80px; max-height: 80px; }
    .form-control, .form-select { border-radius: 10px; }
    @media (max-width: 768px) {
      .container { flex-direction: column; align-items: center; }
      .panier-container, .payment-container { width: 100%; max-width: 100%; }
    }
    .navbar { background-color: white !important; position: sticky; top: 0; z-index: 1030; }
        .navbar .nav-link, .navbar .navbar-brand { color: #5c4033 !important; transition: all 0.3s ease; }
        .nav-link-custom:hover, .nav-link-custom:focus { color: #8b5e3c !important; font-weight: bold; }
        .nav-link-custom.active { color: #6f4e37 !important; font-weight: bold; border-bottom: 2px solid #6f4e37; }
   td .product-image {
  width: 60px;                
  height: auto;               
  border-radius: 8px;         

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
  <!-- PANIER -->
  <div class="panier-container">
  <h3 class="mb-4">Votre panier</h3>
<?php
$totalGeneral = 0;

// Supposons que $user_id est défini et correspond à l'utilisateur connecté
$user_id = $userId; // Exemple, à remplacer par l'ID réel de l'utilisateur connecté

// Requête pour récupérer les articles du panier de l'utilisateur
$stmt = $conn->prepare("SELECT produit_id, couleur, quantite FROM panier WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0): ?>
  <table>
    <thead>
      <tr>
        <th>Image</th>
        <th style="padding: 15px;">Produit</th>
        <th>Quantité</th>
        <th style="padding: 15px;">Prix</th>
        <th style="padding: 10px;">Couleur</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      <?php
      while ($row = $result->fetch_assoc()):
          $idProduit = $row['produit_id'];
          $couleur = $row['couleur'];
          $quantite = intval($row['quantite']);

          // Récupérer nom et prix du produit
          $stmtProd = $conn->prepare("SELECT nom, prix FROM produits WHERE id = ?");
          $stmtProd->bind_param("i", $idProduit);
          $stmtProd->execute();
          $resProd = $stmtProd->get_result();
          $produit = $resProd->fetch_assoc();

          if ($produit):
              $prix = floatval($produit['prix']);
              $totalProduit = $prix * $quantite;
              $totalGeneral += $totalProduit;

              // Récupérer l'image selon la couleur
              if (!empty($couleur)) {
                $imgStmt = $conn->prepare("SELECT image FROM produit_couleurs WHERE produit_id = ? AND couleur = ?");
                $imgStmt->bind_param("is", $idProduit, $couleur);
              } else {
                $imgStmt = $conn->prepare("SELECT image FROM produit_images WHERE produit_id = ? LIMIT 1");
                $imgStmt->bind_param("i", $idProduit);
              }
              $imgStmt->execute();
              $imgRes = $imgStmt->get_result();
              $imageData = $imgRes->fetch_assoc();
              $imageSrc = $imageData['image'] ?? 'default_image.jpg';
      ?>
        <tr>
          <td><img src="images/<?= htmlspecialchars($imageSrc) ?>" class="product-image" alt="Produit"></td>
          <td style="padding: 15px;"><?= htmlspecialchars($produit['nom']) ?></td>
          <td><?= $quantite ?></td>
          <td style="padding: 15px;"><?= number_format($prix, 2, ',', ' ') ?> DA</td>
          <td style="padding: 10px;"><?= htmlspecialchars($couleur ?: 'Aucune') ?></td>
          <td><?= number_format($totalProduit, 2, ',', ' ') ?> DA</td>
        </tr>
      <?php 
          endif; 
      endwhile; ?>
    </tbody>
  </table>
  <p class="total">Total général : <?= number_format($totalGeneral, 2, ',', ' ') ?> DA</p>
<?php else: ?>
  <p>Votre panier est vide.</p>
<?php endif; ?>

</div>


  <!-- PAIEMENT -->
  <div class="payment-container">
    <h2 class="mb-4">Paiement</h2>
    <form id="paymentForm" action="traitement_paiement.php" method="POST" novalidate>
      <div class="mb-3">
        <label for="nomComplet" class="form-label">Nom complet</label>
        <input type="text" class="form-control" id="nomClient" name="nomClient" value="<?= $nomComplet ?>" readonly>
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Adresse e-mail</label>
        <input type="email" class="form-control" id="emailClient" name="emailClient" value="<?= $email ?>" readonly>
      </div>

      <div class="mb-3">
         <label for="telephoneClient" class="form-label">Numéro de téléphone</label>
         <input type="tel" class="form-control" id="telephoneClient" name="telephoneClient" placeholder="0655123456" pattern="^(05|06|07)[0-9]{8}$" required />
      </div>



      <div class="mb-3">
         <label for="adresseClient" class="form-label">Adresse de livraison</label>
         <input type="text" class="form-control" id="adresseClient" name="adresseClient" />
      </div>


      <div class="mb-3">
        <label for="modePaiement" class="form-label">Mode de paiement</label>
        <select class="form-select" id="modePaiement" name="modePaiement" required>
          <option value="">-- Sélectionner --</option>
          <option value="Carte Bancaire">Carte Bancaire</option>
          <option value="Paiement à la livraison">Paiement à la livraison</option>
        </select>
      </div>

      <!-- Bloc carte bancaire caché par défaut -->
      <div id="carteFields" style="display:none;">
        <div class="mb-3">
          <label for="carte" class="form-label">Numéro de carte bancaire</label>
          <input type="text" pattern="\d{16}" maxlength="16" inputmode="numeric" class="form-control" id="carte" name="carte" placeholder="1234123412341234" />
        </div>
        <div class="mb-3">
          <label for="dateExpiration" class="form-label">Date d'expiration</label>
          <input type="month" class="form-control" id="dateExpiration" name="dateExpiration" placeholder="YYYY-MM" />
        </div>
        <div class="mb-3">
          <label for="cvv" class="form-label">CVV</label>
          <input type="text" pattern="\d{3}" maxlength="3" inputmode="numeric" class="form-control" id="cvv" name="cvv" placeholder="123" />
        </div>
      </div>

      <button type="submit" class="btn btn-payer w-100">Payer <?= number_format($totalGeneral, 2, ',', ' ') ?> DA</button>
    </form>
  </div>
</div>

      <!-- Bloc carte bancaire caché par défaut -->
      <div id="carteFields" style="display:none;">
        <div class="mb-3">
          <label for="carte" class="form-label">Numéro de carte bancaire</label>
          <input type="text" pattern="\d{16}" maxlength="16" inputmode="numeric" class="form-control" id="carte" name="carte" placeholder="1234123412341234" />
        </div>
        <div class="mb-3">
          <label for="dateExpiration" class="form-label">Date d'expiration</label>
          <input type="month" class="form-control" id="dateExpiration" name="dateExpiration" placeholder="YYYY-MM" />
        </div>
        <div class="mb-3">
          <label for="cvv" class="form-label">CVV</label>
          <input type="text" pattern="\d{3}" maxlength="3" inputmode="numeric" class="form-control" id="cvv" name="cvv" placeholder="123" />
        </div>
      </div>

<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-danger">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="errorModalLabel">Erreur de saisie</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body" id="errorModalBody"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fermer</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-success">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="confirmModalLabel">Confirmer le paiement</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        Voulez-vous confirmer votre paiement pour un montant de <?= number_format($totalGeneral, 2, ',', ' ') ?> DA ?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="button" id="confirmBtn" class="btn btn-success">Confirmer</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const modeSelect = document.getElementById('modePaiement');
  const carteFields = document.getElementById('carteFields');

  function toggleCarteFields() {
    if (modeSelect.value === 'Carte Bancaire') {
      carteFields.style.display = 'block';
      // rendre requis
      document.getElementById('carte').setAttribute('required', 'required');
      document.getElementById('dateExpiration').setAttribute('required', 'required');
      document.getElementById('cvv').setAttribute('required', 'required');
    } else {
      carteFields.style.display = 'none';
      // enlever requis
      document.getElementById('carte').removeAttribute('required');
      document.getElementById('dateExpiration').removeAttribute('required');
      document.getElementById('cvv').removeAttribute('required');
    }
  }

  toggleCarteFields();
  modeSelect.addEventListener('change', toggleCarteFields);

  // Validation simple avant soumission et confirmation avec modal Bootstrap
  const form = document.getElementById('paymentForm');
  const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
  const errorModalBody = document.getElementById('errorModalBody');
  const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
  const confirmBtn = document.getElementById('confirmBtn');

  form.addEventListener('submit', function(event) {
    event.preventDefault();
    // Validation simple
    if (!form.checkValidity()) {
      form.classList.add('was-validated');
      errorModalBody.textContent = "Veuillez remplir correctement tous les champs obligatoires.";
      errorModal.show();
      return;
    }
    confirmModal.show();
  });

  confirmBtn.addEventListener('click', function() {
    confirmModal.hide();
    form.submit();
  });
</script>

</body>
</html> 


