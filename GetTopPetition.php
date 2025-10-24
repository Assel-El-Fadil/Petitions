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
    ORDER BY numSign DESC
    LIMIT 1
";

$result = $conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    echo json_encode($row);
} else {
    echo json_encode([]);
}
$conn->close();
?>