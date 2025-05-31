<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Page d'Accueil</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    a {
      text-decoration: none;
    }

    html {
      scroll-behavior: smooth;
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

    .nav-link-custom.active {
      color: #6f4e37 !important;
      font-weight: bold;
      border-bottom: 2px solid #6f4e37;
    }

   .hero {
  background-image: url('handmade.jpg');
  height: 400px;
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center;
  position: relative; /* important for absolute positioning */
}

.btn-container {
  position: absolute;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
}



    .categories {
      padding: 60px 20px;
      background-color: #f5f0eb;
    }

    .category-circle {
      width: 150px;
      height: 150px;
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

    .services {
      padding: 60px 20px;
      background-color: #fffaf5;
    }

    .card {
      background-color: #f1e3d3;
    }

    .card-title {
      color: #5c4033;
    }

    .footer {
      background-color:rgb(197, 171, 144);
      color:rgb(0, 0, 0);
      padding: 20px 0;
      text-align: center;
    }
    .footer a {
      color:rgb(0, 0, 0);
      text-decoration: underline; /* facultatif, ajoute un soulignement */
    }

    .btn-light {
      background-color: #8b5e3c;
      color: white;
      border: none;
    }

    .btn-light:hover {
      background-color: #6f4e37;
    }

    /* Réduction de la taille des champs du formulaire de contact */
    section.py-5 {
      padding: 30px 20px; /* Réduire le padding de la section */
    }

    form .form-label {
      font-size: 0.8rem; /* Réduire la taille des labels */
    }

    form .form-control {
      height: 35px; /* Réduire la hauteur des champs de saisie */
      font-size: 0.8rem; /* Réduire la taille du texte dans les champs */
    }

    form textarea.form-control {
      height: 100px; /* Réduire la hauteur de la zone de texte */
    }

    button.btn-light {
      padding: 8px 16px; /* Réduire le padding du bouton */
      font-size: 0.8rem; /* Réduire la taille du texte du bouton */
    }
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
          <li class="nav-item">
            <a class="nav-link nav-link-custom active" href="#">Accueil</a>
          </li>
          <li class="nav-item">
            <a class="nav-link nav-link-custom" href="#produits">Produits</a>
          </li>
          <li class="nav-item">
            <a class="nav-link nav-link-custom" href="#contact">Contact</a>
          </li>
          <li class="nav-item">
             <a class="nav-link d-flex align-items-center nav-link-custom" href="login.php?origin=accueil">
               <i class="bi bi-person-circle me-1"></i> Connexion
               </a>
          </li>



          <li class="nav-item">
            <a class="nav-link d-flex align-items-center nav-link-custom" href="panier.php">
              <i class="bi bi-cart me-1"></i> Panier
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Section Hero -->
  <section class="hero">
  </section>
 

  <!-- Catégories -->
  <section id="produits" class="categories text-center">
    <div class="container" id="produit">
      <h2 class="mb-5">Nos Catégories</h2>
      <div class="row justify-content-center g-4">
        <div class="col-md-3">
          <a href="decor.php">
            <div class="category-circle">
              <img src="decor.png" alt="Crochet">
            </div>
            <div class="category-title">Maison et décor</div>
          </a>
        </div>
        <div class="col-md-3">
          <a href="bijoux.php">
            <div class="category-circle">
              <img src="bijoux.png" alt="Rugs">
            </div>
            <div class="category-title">Bijoux</div>
          </a>
        </div>
        <div class="col-md-3">
          <a href="sacs.php">
            <div class="category-circle">
              <img src="sacs.png" alt="Embroidery">
            </div>
            <div class="category-title">Sacs</div>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Services -->
  <section class="services text-center">
    <div class="container">
      <h2 class="mb-5">Nos Services</h2>
      <div class="row">
        <div class="col-md-4 mb-4">
          <div class="card h-100 shadow-sm">
            <div class="card-body">
              <h5 class="card-title">Produits variés</h5>
              <p class="card-text">Une large gamme de produits sélectionnés pour vous satisfaire.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card h-100 shadow-sm">
            <div class="card-body">
              <h5 class="card-title">Livraison rapide</h5>
              <p class="card-text">Nous vous garantissons une livraison rapide et sécurisée.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card h-100 shadow-sm">
            <div class="card-body">
              <h5 class="card-title">Support 24/7</h5>
              <p class="card-text">Une assistance disponible à tout moment pour vos questions.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

 <footer class="footer" id="contact">
  <div class="container">
    <div class="row">
      <!-- Section About -->
      <div class="col-md-6 mb-3">
        <h5>À propos de Handmade</h5>
        <p>
       Handmade propose des créations faites main, uniques et authentiques. Nos produits allient passion et savoir-faire artisanal.
       </p>

      </div>
      
      <!-- Section Contact -->
      <div class="col-md-6 mb-3">
        <h5>Contactez-nous</h5>
        <p>
          <i class="bi bi-envelope"></i> Email : <a href="mailto:contact@byhande.com" style="color:white;">contact@byhande.com</a><br>
          <i class="bi bi-whatsapp"></i> WhatsApp : <a href="https://wa.me/33612345678" style="color:white;" target="_blank">+213 6997089</a>
        </p>
      </div>
    </div>
    <div class="text-center mt-3">
      &copy; 2025 Handmade - Tous droits réservés
    </div>
  </div>
</footer>
      
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
