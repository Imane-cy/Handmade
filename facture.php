<?php
session_start();

if (!isset($_SESSION['facture'])) {
    header("Location: paiement.php");
    exit;
}
$facture = $_SESSION['facture'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Facture</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 40px;
      background-color: #f8f9fa;
    }
    .facture-box {
      background: white;
      padding: 30px;
      border: 1px solid #eee;
      max-width: 700px;
      margin: auto;
      box-shadow: 0 0 10px rgba(0,0,0,0.15);
    }
    h2 {
      color: #6f4e37;
      text-align: center;
    }
    .info-ligne {
      margin: 8px 0;
    }
    .total {
      font-size: 1.5rem;
      font-weight: bold;
      margin-top: 20px;
      text-align: right;
      color: #333;
    }
    pre {
      background-color: #f1f1f1;
      padding: 10px;
      border-radius: 5px;
      overflow-x: auto;
    }
  </style>
</head>
<body>
  <div class="facture-box">
    <h2>Facture</h2>

    <div class="info-ligne"><strong>Nom :</strong> <?= htmlspecialchars($facture['nom']) ?></div>
    <div class="info-ligne"><strong>Email :</strong> <?= htmlspecialchars($facture['email']) ?></div>
    <div class="info-ligne"><strong>Téléphone :</strong> <?= htmlspecialchars($facture['telephone'] ?? 'Non renseigné') ?></div>
    <div class="info-ligne"><strong>Adresse :</strong> <?= nl2br(htmlspecialchars($facture['adresse'])) ?></div>
    <div class="info-ligne"><strong>Mode de paiement :</strong> <?= htmlspecialchars($facture['mode_paiement']) ?></div> 
     <div class="info-ligne"><strong>Date :</strong><?= htmlspecialchars($facture['date_achat'])?></div>
    <hr>

    <pre><?= htmlspecialchars($facture['details']) ?></pre>

    <p class="total">Total : <?= number_format($facture['total'], 2, ',', ' ') ?> DA</p>
  </div>
</body>
</html>