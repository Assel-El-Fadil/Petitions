<?php
// Simple Signature form + handler
// DB: Petition(IDP, TitreP, DescriptionP, DateAjoutP, DateinP, NomPorteurP, Email)
//     Signature(IDS, IDP, NomS, PrenomS, PaysS, DateS, HeureS, EmailS)

// ---- CONFIGURE THESE FOR YOUR ENV ----
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'tp3'; // change to your database name
// --------------------------------------

$conn = @new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    http_response_code(500);
    die('Database connection failed');
}

// Ensure we have a petition id
$petitionId = isset($_POST['idp']) ? intval($_POST['idp']) : 0;
$petitionTitle = '';
if ($petitionId > 0) {
    // Try to fetch petition title for context (optional)
    if ($stmt = $conn->prepare('SELECT TitreP FROM Petition WHERE IDP = ?')) {
        $stmt->bind_param('i', $petitionId);
        if ($stmt->execute()) {
            $stmt->bind_result($petitionTitle);
            $stmt->fetch();
        }
        $stmt->close();
    }
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $petitionId = isset($_POST['idp']) ? intval($_POST['idp']) : 0;
    $nom = isset($_POST['noms']) ? trim($_POST['noms']) : '';
    $prenom = isset($_POST['prenoms']) ? trim($_POST['prenoms']) : '';
    $pays = isset($_POST['payss']) ? trim($_POST['payss']) : '';
    $email = isset($_POST['emails']) ? trim($_POST['emails']) : '';

    if ($petitionId <= 0) { $errors[] = 'Invalid petition.'; }
    if ($nom === '') { $errors[] = 'Last name is required.'; }
    if ($prenom === '') { $errors[] = 'First name is required.'; }
    if ($pays === '') { $errors[] = 'Country is required.'; }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Valid email is required.'; }

    if (!$errors) {
        $date = date('Y-m-d');
        $time = date('H:i:s');

        if ($stmt = $conn->prepare('INSERT INTO Signature (IDP, NomS, PrenomS, PaysS, DateS, HeureS, EmailS) VALUES (?, ?, ?, ?, ?, ?, ?)')) {
            $stmt->bind_param('issssss', $petitionId, $nom, $prenom, $pays, $date, $time, $email);
            if ($stmt->execute()) {
                $success = 'Thank you! Your signature has been recorded.';
                // refresh title if not loaded
                if ($petitionTitle === '' && $petitionId > 0) {
                    if ($s2 = $conn->prepare('SELECT TitreP FROM Petition WHERE IDP = ?')) {
                        $s2->bind_param('i', $petitionId);
                        if ($s2->execute()) {
                            $s2->bind_result($petitionTitle);
                            $s2->fetch();
                        }
                        $s2->close();
                    }
                }
                // Clear form fields after success
                $nom = $prenom = $pays = $email = '';
            } else {
                $errors[] = 'Failed to save your signature.';
            }
            $stmt->close();
        } else {
            $errors[] = 'Failed to prepare insert statement.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Petition</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f7fa; margin: 0; }
        .container { max-width: 720px; margin: 40px auto; background: #fff; padding: 24px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,.05); }
        h1 { margin: 0 0 8px; }
        .subtitle { color: #666; margin-bottom: 20px; }
        .form-group { margin-bottom: 14px; }
        label { display: block; margin-bottom: 6px; font-weight: 600; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 15px; }
        .row { display: flex; gap: 12px; }
        .row .col { flex: 1; }
        .btn { background: #3498db; color: #fff; border: 0; padding: 10px 16px; border-radius: 4px; cursor: pointer; font-size: 15px; }
        .btn:hover { background: #2980b9; }
        .alert { padding: 12px; border-radius: 4px; margin-bottom: 16px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .muted { color: #888; font-size: 14px; }
        a { color: #3498db; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
<?php if ($petitionTitle) { ?>
    <meta name="description" content="Sign the petition: <?php echo htmlspecialchars($petitionTitle, ENT_QUOTES); ?>">
<?php } ?>
</head>
<body>
    <div class="container">
        <h1>Sign Petition</h1>
        <div class="subtitle">
            <?php if ($petitionTitle) { ?>
                Petition: <strong><?php echo htmlspecialchars($petitionTitle); ?></strong>
            <?php } elseif ($petitionId > 0) { ?>
                Petition ID: <strong><?php echo (int)$petitionId; ?></strong>
            <?php } else { ?>
                Select a petition to sign.
            <?php } ?>
        </div>

        <?php if ($success) { ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php } ?>
        <?php if ($errors) { ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars(implode('\n', $errors)); ?>
            </div>
        <?php } ?>

        <form method="post" action="">
            <input type="hidden" name="idp" value="<?php echo (int)$petitionId; ?>">
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="noms">Last name</label>
                        <input type="text" id="noms" name="noms" value="<?php echo isset($nom) ? htmlspecialchars($nom) : ''; ?>" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="prenoms">First name</label>
                        <input type="text" id="prenoms" name="prenoms" value="<?php echo isset($prenom) ? htmlspecialchars($prenom) : ''; ?>" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="payss">Country</label>
                        <input type="text" id="payss" name="payss" value="<?php echo isset($pays) ? htmlspecialchars($pays) : ''; ?>" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="emails">Email</label>
                        <input type="email" id="emails" name="emails" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn">Add Signature</button>
        </form>

        <p class="muted" style="margin-top:16px;">By submitting, you agree to have your name recorded for this petition.</p>

        <?php if ($petitionId > 0) { ?>
            <p style="margin-top:10px;"><a href="ListePetition.php">Back to petitions</a></p>
        <?php } ?>
    </div>
</body>
</html>
<?php $conn->close(); ?>