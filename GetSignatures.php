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

$topSql = "
    SELECT p.IDP
    FROM Petition p
    LEFT JOIN (
        SELECT IDP, COUNT(*) AS numSign
        FROM Signature
        GROUP BY IDP
    ) s ON s.IDP = p.IDP
    ORDER BY numSign DESC
    LIMIT 1
";
$topResult = $conn->query($topSql);
if (!$topResult || $topResult->num_rows === 0) {
    echo json_encode([]);
    exit;
}

$topPetition = $topResult->fetch_assoc();
$idp = (int)$topPetition['IDP'];

$sql = "
    SELECT PrenomS, NomS, PaysS, DateS
    FROM Signature
    WHERE IDP = ?
    ORDER BY DateS DESC, HeureS DESC
    LIMIT 5
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $idp);
$stmt->execute();
$result = $stmt->get_result();

$signatures = [];
while ($row = $result->fetch_assoc()) {
    $signatures[] = $row;
}

echo json_encode($signatures);
$stmt->close();
$conn->close();
?>
