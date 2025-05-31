<?php 
session_start(); 

$success = false;
$error = "";
$host = 'localhost';
$dbname = 'byhande';
$user = 'root';
$password_db = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $nom = htmlspecialchars(trim($_POST['nom']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (strlen($password) < 8) {
        $error = "Le mot de passe doit contenir au moins 8 caractères.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "L'email n'est pas valide.";
    } elseif ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $error = "Cet email est déjà utilisé.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $date_inscription = date("Y-m-d H:i:s");
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (prenom, nom, email, mot_de_passe, date_inscription) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$prenom, $nom, $email, $password_hash, $date_inscription])) {
                // Récupérer l'ID de l'utilisateur et le stocker en session
                $user_id = $pdo->lastInsertId();
                $_SESSION['user_id'] = $user_id;
                $_SESSION['prenom'] = $prenom;
                $_SESSION['nom'] = $nom;
                $_SESSION['email'] = $email;

                // Redirection immédiate vers accueil.php
                header("Location: accueil.php");
                exit();
            } else {
                $error = "Erreur lors de l'inscription.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Inscription</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    body { background-color: #f3f1ec; font-family: 'Segoe UI', sans-serif; }
     .navbar { background-color: white !important; position: sticky; top: 0; z-index: 1030; }
        .navbar .nav-link, .navbar .navbar-brand { color: #5c4033 !important; transition: all 0.3s ease; }
        .nav-link-custom:hover, .nav-link-custom:focus { color: #8b5e3c !important; font-weight: bold; }
        .nav-link-custom.active { color: #6f4e37 !important; font-weight: bold; border-bottom: 2px solid #6f4e37; }
    .register-container { max-width: 480px; margin: 80px auto; padding: 35px; background-color: #fffdf9; border-radius: 20px; box-shadow: 0 8px 18px rgba(0, 0, 0, 0.08); }
    .btn-register { background-color: #8b5e3c; color: white; font-weight: bold; }
    .btn-register:hover { background-color: #6f4e37; }
    .eye-icon { cursor: pointer; position: absolute; right: 10px; top: 40px; }
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


<div class="register-container">
    <div class="text-center mb-3"><i class="bi bi-person-plus" style="font-size: 3rem; color: #6f4e37;"></i></div>
    <h3 class="text-center mb-4">Créer un compte</h3>

    <?php if ($error): ?>
        <div class="alert alert-danger text-center"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3 row">
            <div class="col">
                <label class="form-label">Prénom</label>
                <input type="text" name="prenom" class="form-control" required value="<?= isset($prenom) ? htmlspecialchars($prenom) : '' ?>">
            </div>
            <div class="col">
                <label class="form-label">Nom</label>
                <input type="text" name="nom" class="form-control" required value="<?= isset($nom) ? htmlspecialchars($nom) : '' ?>">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Adresse e-mail</label>
            <input type="email" name="email" class="form-control" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
        </div>
        <div class="mb-3 position-relative">
            <label class="form-label">Mot de passe</label>
            <input type="password" id="password" name="password" class="form-control" required>
            <i class="bi bi-eye-slash eye-icon" id="togglePassword" onclick="togglePassword()"></i>
        </div>
        <div class="mb-3">
            <label class="form-label">Confirmer le mot de passe</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-register w-100">S'inscrire</button>
    </form>

    <div class="text-center mt-3">
        <p>Déjà inscrit ? <a href="login.php">Se connecter</a></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePassword() {
  const pw = document.getElementById("password");
  const cpw = document.getElementById("confirm_password");
  const icon = document.getElementById("togglePassword");
  const show = pw.type === "password";
  pw.type = cpw.type = show ? "text" : "password";
  icon.classList.toggle("bi-eye", show);
  icon.classList.toggle("bi-eye-slash", !show);
}
</script>

</body>
</html>
