<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $admin_username = "admin123";
    $admin_password = "1212";

    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['admin'] = true;

        // Effacer immédiatement les variables sensibles
        unset($username, $password);

        header("Location: admin_dashboard.php");
        exit();
    } else {
        $erreur = "Nom d'utilisateur ou mot de passe incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Connexion Admin - ByHande</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f3f1ec;
      font-family: 'Segoe UI', sans-serif;
    }

    .login-container {
      max-width: 420px;
      margin: 80px auto;
      padding: 35px;
      background-color: #fffdf9;
      border-radius: 20px;
      box-shadow: 0 8px 18px rgba(0,0,0,0.08);
      border: 1px solid #e8e5dc;
    }

    .login-title {
      text-align: center;
      margin-bottom: 25px;
      color: #6f4e37;
      font-weight: bold;
    }

    .form-label {
      color: #5a5244;
    }

    .form-control {
      border-radius: 10px;
      border: 1px solid #ccc;
    }

    .btn-connexion {
      background-color: #8b5e3c;
      color: white;
      font-weight: bold;
      border: none;
      border-radius: 10px;
      transition: background-color 0.3s ease;
    }

    .btn-connexion:hover {
      background-color: #6f4e37;
    }

    .alert {
      border-radius: 10px;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h3 class="login-title">Connexion Admin</h3>

    <?php if (isset($erreur)): ?>
      <div class="alert alert-danger text-center"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
      <div class="mb-3">
        <label for="username" class="form-label">Nom d'utilisateur</label>
        <input type="text" class="form-control" name="username" id="username" required autocomplete="off">
      </div>
      <div class="mb-4">
        <label for="password" class="form-label">Mot de passe</label>
        <input type="password" class="form-control" name="password" id="password" required autocomplete="off">
      </div>
      <button type="submit" class="btn btn-connexion w-100 py-2">Se connecter</button>
    </form>
  </div>
</body>
</html>