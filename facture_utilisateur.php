<?php
$conn = new mysqli("localhost", "root", "", "byhande");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

if (!isset($_GET['id_user'])) {
    die("Utilisateur non spécifié.");
}

$id_user = intval($_GET['id_user']);

// Récupérer les informations de l'utilisateur
$utilisateur = $conn->query("SELECT nom, email FROM utilisateurs WHERE id = $id_user")->fetch_assoc();
if (!$utilisateur) {
    die("Utilisateur introuvable.");
}

// Récupérer les factures de l'utilisateur
$factures = $conn->query("SELECT * FROM factures WHERE id_user = $id_user ORDER BY date_achat DESC");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <meta charset="UTF-8">
    <title>Factures de <?= htmlspecialchars($utilisateur['nom']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: white !important;
            position: sticky;
            top: 0;
            z-index: 1030;
        }
        .navbar .nav-link, .navbar .navbar-brand {
            color: #5c4033 !important;
            transition: all 0.3s ease;
        }
        .nav-link-custom:hover, .nav-link-custom:focus {
            color: #8b5e3c !important;
            font-weight: bold;
        }
        .nav-link-custom.active {
            color: #6f4e37 !important;
            font-weight: bold;
            border-bottom: 2px solid #6f4e37;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }
        th {
            background-color: #a97457;
            color: white;
        }
        tr:hover {
            background-color: #f2f2f2;
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
<br>
<h4>Factures de l'utilisateur : <?= htmlspecialchars($utilisateur['nom']) ?> (<?= htmlspecialchars($utilisateur['email']) ?>)</h4>
<table>
    <thead>
        <tr>
            <th>Nom</th>
            <th>Email</th>
            <th>Adresse</th>
            <th>Mode de paiement</th>
            <th>Total</th>
            <th>Date</th>
            <th>Détails</th>
        </tr>
    </thead>
    <tbody>
    <?php while($facture = $factures->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($facture['nom_client']) ?></td>
            <td><?= htmlspecialchars($facture['email']) ?></td>
            <td><?= htmlspecialchars($facture['adresse']) ?></td>
            <td><?= htmlspecialchars($facture['mode_paiement']) ?></td>
            <td><?= number_format($facture['total'], 2, ',', ' ') ?> DA</td>
            <td>
                <?php
                if ($facture['date_achat'] && $facture['date_achat'] !== '0000-00-00 00:00:00') {
                    $date = new DateTime($facture['date_achat']);
                    echo $date->format('d/m/Y H:i');
                } else {
                    echo "Date invalide";
                }
                ?>
            </td>
            <td><pre><?= htmlspecialchars($facture['details']) ?></pre></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
