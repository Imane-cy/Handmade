<?php 
session_start();

$conn = new mysqli("localhost", "root", "", "byhande");
if ($conn->connect_error) {
    die("Erreur de connexion: " . $conn->connect_error);
}

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
if ($user_id == 0) {
    die("Pas d'utilisateur connecté.");
}

// Fonction pour afficher une alerte JS puis revenir en arrière
function erreurPopup($msg) {
    echo "<script>alert('".addslashes($msg)."'); window.history.back();</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et nettoyage des données
    $nom = trim($_POST['nomClient'] ?? '');
    $email = trim($_POST['emailClient'] ?? '');
    $telephone = trim($_POST['telephoneClient'] ?? '');
    $adresse = trim($_POST['adresseClient'] ?? '');
    $mode = trim($_POST['modePaiement'] ?? '');
    $dateAchat = date("Y-m-d H:i:s");

    // Validation minimale
    if (empty($nom) || empty($email) || empty($telephone) || empty($adresse) || empty($mode)) {
        erreurPopup("Veuillez remplir tous les champs obligatoires.");
    }

    if ($mode === "Carte Bancaire") {
        $numCarte = $_POST['carte'] ?? '';
        $dateExp = $_POST['dateExpiration'] ?? '';
        $cvv = $_POST['cvv'] ?? '';

        if (!preg_match('/^\d{16}$/', $numCarte)) {
            erreurPopup("Numéro de carte invalide. Il doit contenir exactement 16 chiffres.");
        }
        if (!preg_match('/^\d{3}$/', $cvv)) {
            erreurPopup("Code CVV invalide. Il doit contenir exactement 3 chiffres.");
        }
        if (empty($dateExp)) {
            erreurPopup("La date d'expiration est obligatoire.");
        }

        list($expYear, $expMonth) = explode('-', $dateExp);
        $expYear = (int)$expYear;
        $expMonth = (int)$expMonth;
        $currentYear = (int)date('Y');
        $currentMonth = (int)date('m');

        if ($expYear < $currentYear || ($expYear === $currentYear && $expMonth < $currentMonth)) {
            erreurPopup("Carte expirée. Veuillez utiliser une carte valide.");
        }
    }

    // Récupération du panier
    $sql = "SELECT * FROM panier WHERE user_id = ?";
    $stmtPanier = $conn->prepare($sql);
    $stmtPanier->bind_param("i", $user_id);
    $stmtPanier->execute();
    $resultPanier = $stmtPanier->get_result();

    if ($resultPanier->num_rows === 0) {
        erreurPopup("Votre panier est vide.");
    }

    $total = 0;
    $detailsProduits = "";

    while ($item = $resultPanier->fetch_assoc()) {
        $id = $item['produit_id'];
        $couleur = $item['couleur'];
        $quantite = $item['quantite'];

        // Récupération du produit
        $stmt = $conn->prepare("SELECT nom, prix FROM produits WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $prod = $res->fetch_assoc();
        $stmt->close();

        if (!$prod) {
            erreurPopup("Produit introuvable (ID $id).");
        }

        $nomProduit = htmlspecialchars($prod['nom']);
        $prix = $prod['prix'];

        $sousTotal = $prix * $quantite;
        $total += $sousTotal;

        if (!empty($couleur)) {
            $detailsProduits .= "$nomProduit ($quantite - $couleur) - $sousTotal DA\n";

            // Mise à jour du stock couleur
            $stmtUpdateCouleur = $conn->prepare("UPDATE produit_couleurs SET stock = stock - ? WHERE produit_id = ? AND couleur = ?");
            $stmtUpdateCouleur->bind_param("iis", $quantite, $id, $couleur);
            $stmtUpdateCouleur->execute();
            $stmtUpdateCouleur->close();

            // Recalcul du stock total produit
            $stmtStockTotal = $conn->prepare("SELECT SUM(stock) as total_stock FROM produit_couleurs WHERE produit_id = ?");
            $stmtStockTotal->bind_param("i", $id);
            $stmtStockTotal->execute();
            $resultStock = $stmtStockTotal->get_result();
            $rowStock = $resultStock->fetch_assoc();
            $totalStock = $rowStock['total_stock'] ?? 0;
            $stmtStockTotal->close();

            // Mise à jour stock produit principal
            $stmtUpdateProduit = $conn->prepare("UPDATE produits SET stock = ? WHERE id = ?");
            $stmtUpdateProduit->bind_param("ii", $totalStock, $id);
            $stmtUpdateProduit->execute();
            $stmtUpdateProduit->close();

        } else {
            $detailsProduits .= "$nomProduit ($quantite) - $sousTotal DA\n";

            // Mise à jour stock produit sans couleur
            $stmtUpdate = $conn->prepare("UPDATE produits SET stock = stock - ? WHERE id = ?");
            $stmtUpdate->bind_param("ii", $quantite, $id);
            $stmtUpdate->execute();
            $stmtUpdate->close();
        }
    }

    // Insertion facture
    $stmtFacture = $conn->prepare("INSERT INTO factures (nom_client, email, telephone, adresse, mode_paiement, total, date_achat, details, id_user) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmtFacture->bind_param("ssssssssi", $nom, $email, $telephone, $adresse, $mode, $total, $dateAchat, $detailsProduits, $user_id);
    $stmtFacture->execute();
    $stmtFacture->close();

    // Vider le panier
    $deletePanier = $conn->prepare("DELETE FROM panier WHERE user_id = ?");
    $deletePanier->bind_param("i", $user_id);
    $deletePanier->execute();
    $deletePanier->close();

    // Stocker la confirmation en session
    $_SESSION['facture'] = [
        'nom' => $nom,
        'email' => $email,
        'telephone' => $telephone,
        'adresse' => $adresse,
        'mode_paiement' => $mode,
        'total' => $total,
        'date_achat' => $dateAchat,
        'details' => $detailsProduits
    ];

    header("Location: facture.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paiement</title>
</head>
<body>
    <form id="formPaiement" method="post" action="">
        <input type="text" name="nomClient" placeholder="Nom" required>
        <input type="email" name="emailClient" placeholder="Email" required>
        <input type="tel" name="telephoneClient" placeholder="Téléphone" required>
        <input type="text" name="adresseClient" placeholder="Adresse" required>
        <select name="modePaiement" required>
            <option value="">Choisir le mode de paiement</option>
            <option value="Carte Bancaire">Carte Bancaire</option>
            <option value="Espèces">Espèces</option>
        </select>

        <div id="carteFields" style="display:none;">
            <input type="text" name="carte" placeholder="Numéro de carte (16 chiffres)">
            <input type="month" name="dateExpiration" placeholder="Date d'expiration">
            <input type="text" name="cvv" placeholder="CVV (3 chiffres)">
        </div>

        <input type="submit" value="Payer">
    </form>

    <script>
    document.querySelector('select[name="modePaiement"]').addEventListener('change', function() {
        const carteFields = document.getElementById('carteFields');
        carteFields.style.display = this.value === 'Carte Bancaire' ? 'block' : 'none';
    });

    document.getElementById('formPaiement').addEventListener('submit', function(e) {
        if (!confirm("Êtes-vous sûr de vouloir procéder au paiement ?")) {
            e.preventDefault();
        }
    });
    </script>
</body>
</html>
