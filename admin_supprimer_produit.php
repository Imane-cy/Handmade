<?php
require_once 'connexion.php';

if (!isset($_GET['id'])) {
    header("Location: admin_gestion_produits.php");
    exit();
}

$id = intval($_GET['id']);

// Optionnel : supprimer aussi l’image du serveur
$stmt = $conn->prepare("SELECT image FROM produits WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    $imagePath = 'images/' . $row['image'];
    if (file_exists($imagePath)) {
        unlink($imagePath); // supprime l'image physique
    }
}

$stmt = $conn->prepare("DELETE FROM produits WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: admin_gestion_produits.php");
exit();
?>