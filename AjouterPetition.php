<?php
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'petition';

$conn = @new mysqli($dbHost, $dbUser, $dbPass, $dbName);

$title = isset($_POST['TitreP']) ? trim($_POST['TitreP']) : '';
$description = isset($_POST['DescriptionP']) ? trim($_POST['DescriptionP']) : '';
$nomPorteur = isset($_POST['NomPorteurP']) ? trim($_POST['NomPorteurP']) : '';
$email = isset($_POST['Email']) ? trim($_POST['Email']) : '';
$dateIn = isset($_POST['DateFinP']) && $_POST['DateFinP'] !== '' ? $_POST['DateFinP'] : null;
$dateAjout = date('Y-m-d');

if ($stmt = $conn->prepare('INSERT INTO Petition (TitreP, DescriptionP, DateAjoutP, DateFinP, NomPorteurP, Email) VALUES (?, ?, ?, ?, ?, ?)')) {
    $stmt->bind_param('ssssss', $title, $description, $dateAjout, $dateIn, $nomPorteur, $email);
    $stmt->execute();
    $stmt->close();
}

$conn->close();

header('Location: ListePetition.php');
exit;
?>


