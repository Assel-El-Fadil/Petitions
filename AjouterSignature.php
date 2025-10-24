<?php
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'petition';

$conn = @new mysqli($dbHost, $dbUser, $dbPass, $dbName);

$petitionId = isset($_POST['idp']) ? intval($_POST['idp']) : 0;
$nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
$prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
$pays = isset($_POST['pays']) ? trim($_POST['pays']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';

if ($petitionId < 0 || $nom === '' || $prenom === '' || $pays === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: Signature.php?idp=' . urlencode((string)$petitionId));
    exit;
}

$date = date('Y-m-d');
$time = date('H:i:s');

if ($stmt = $conn->prepare('INSERT INTO Signature (IDP, NomS, PrenomS, PaysS, DateS, HeureS, EmailS) VALUES (?, ?, ?, ?, ?, ?, ?)')) {
    $stmt->bind_param('issssss', $petitionId, $nom, $prenom, $pays, $date, $time, $email);
    $stmt->execute();
    $stmt->close();
}

$conn->close();

header('Location: ListePetition.php');
exit;
?>


