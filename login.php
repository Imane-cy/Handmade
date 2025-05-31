<?php
session_start();
$user = null;

$origin = isset($_POST['origin']) ? $_POST['origin'] : (isset($_GET['origin']) ? $_GET['origin'] : null);
$id = isset($_POST['id']) ? $_POST['id'] : (isset($_GET['id']) ? $_GET['id'] : null);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Connexion base de données
  $host = 'localhost';
  $dbname = 'byhande';
  $user_db = 'root';
  $pass = '';

  try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user_db, $pass);
  } catch (PDOException $e) {
    die("Erreur de connexion: " . $e->getMessage());
  }

  $email = $_POST['email'];
  $password = $_POST['password'];

  if (strlen($password) < 8) {
    $error = "Le mot de passe doit contenir au moins 8 caractères.";
  } else {
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
      $isAdmin = ($user['email'] === 'admin@gmail.com' && $password === '12345678');

      if ($isAdmin) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        // Admin logique
        if ($origin === 'accueil') {
          header("Location: accueil.php");
        } elseif ($origin === 'produit_detail' && !empty($id)) {
          header("Location: produit_detail.php?id=$id");
        } else {
          header("Location: admin_dashboard.php");
        }
        exit;
      }

      // Utilisateur normal
      if (password_verify($password, $user['mot_de_passe'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        if ($origin === 'produit_detail' && !empty($id)) {
          header("Location: produit_detail.php?id=$id");
        } else {
          header("Location: accueil.php");
        }
        exit;
      } else {
        $error = "Email ou mot de passe incorrect.";
      }
    } else {
      $error = "Email ou mot de passe incorrect.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Connexion</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    body { background-color: #f3f1ec; font-family: 'Segoe UI', sans-serif; }
   .navbar { background-color: white !important; position: sticky; top: 0; z-index: 1030; }
        .navbar .nav-link, .navbar .navbar-brand { color: #5c4033 !important; transition: all 0.3s ease; }
        .nav-link-custom:hover, .nav-link-custom:focus { color: #8b5e3c !important; font-weight: bold; }
        .nav-link-custom.active { color: #6f4e37 !important; font-weight: bold; border-bottom: 2px solid #6f4e37; }
  
    .login-container { max-width: 480px; margin: 80px auto; padding: 35px; background-color: #fffdf9; border-radius: 20px; box-shadow: 0 8px 18px rgba(0, 0, 0, 0.08); border: 1px solid #e8e5dc; }
    .login-title { text-align: center; margin-bottom: 25px; color: #6f4e37; font-weight: bold; }
    .form-label { color: #5a5244; }
    .form-control { border-radius: 10px; border: 1px solid #ccc; }
    .btn-login { background-color: #8b5e3c; color: white; font-weight: bold; border: none; border-radius: 10px; transition: background-color 0.3s ease; }
    .btn-login:hover { background-color: #6f4e37; }
    .text-center a { color: #8b5e3c; text-decoration: none; }
    .text-center a:hover { text-decoration: underline; }
    .eye-icon { cursor: pointer; position: absolute; right: 10px; top: 40px; }
  </style>
</head>
<body>

<?php
// Détecter la page actuelle
$currentPage = basename($_SERVER['PHP_SELF']);
?>
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

<!-- ✅ Formulaire de connexion -->
<div class="login-container">
  <div class="text-center mb-3">
    <i class="bi bi-person-circle" style="font-size: 4rem; color: #6f4e37;"></i>
  </div>
  <h3 class="login-title">Se connecter</h3>

  <?php if (isset($error)) : ?>
    <div class="alert alert-danger text-center"><?= $error ?></div>
  <?php endif; ?>

  <form method="post" action="login.php">
    <input type="hidden" name="origin" value="<?= htmlspecialchars($origin) ?>">
    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
    <div class="mb-3">
      <label for="email" class="form-label">Adresse e-mail</label>
      <input type="email" class="form-control" id="email" name="email" required />
    </div>
    <div class="mb-3 position-relative">
      <label for="password" class="form-label">Mot de passe</label>
      <input type="password" class="form-control" id="password" name="password" required minlength="8" />
      <i class="bi bi-eye-slash eye-icon" id="togglePassword" onclick="togglePassword()"></i>
    </div>
    <button type="submit" class="btn btn-login w-100 py-2">Connexion</button>
  </form>

  <div class="text-center mt-3">
    <p>Pas encore de compte ? <a href="inscription.php">Créer un compte</a></p>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function togglePassword() {
    const passwordField = document.getElementById('password');
    const toggleIcon = document.getElementById('togglePassword');
    if (passwordField.type === "password") {
      passwordField.type = "text";
      toggleIcon.classList.remove("bi-eye-slash");
      toggleIcon.classList.add("bi-eye");
    } else {
      passwordField.type = "password";
      toggleIcon.classList.remove("bi-eye");
      toggleIcon.classList.add("bi-eye-slash");
    }
  }
</script>

</body>
</html>
