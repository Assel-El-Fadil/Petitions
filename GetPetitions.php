<?php
header('Content-Type: application/json');

$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'petition';

$conn = @new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$sql = "
    SELECT p.IDP, p.TitreP, p.DescriptionP, p.DateAjoutP, p.NomPorteurP,
           COALESCE(s.numSign, 0) AS numSign
    FROM Petition p
    LEFT JOIN (
        SELECT IDP, COUNT(*) AS numSign
        FROM Signature
        GROUP BY IDP
    ) s ON s.IDP = p.IDP
    ORDER BY p.DateAjoutP DESC
";

$result = $conn->query($sql);
$petitions = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $petitions[] = $row;
    }
    $result->free();
}

echo json_encode($petitions);
$conn->close();
?>