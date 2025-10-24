<?php
header('Content-Type: application/json');

$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'petition';

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$sql = "
    SELECT IDP, TitreP, DescriptionP, DateAjoutP, NomPorteurP
    FROM Petition
    ORDER BY IDP DESC
    LIMIT 1
";

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row);
} else {
    echo json_encode(['error' => 'No petitions found']);
}
$conn->close();
?>