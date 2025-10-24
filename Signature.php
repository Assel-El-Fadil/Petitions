<?php
$petitionId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$petitionTitle = '';
$success = '';
$errors = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Petition</title>
    <link rel="stylesheet" href="style.css">
<?php if ($petitionTitle) { ?>
    <meta name="description" content="Sign the petition: <?php echo htmlspecialchars($petitionTitle, ENT_QUOTES); ?>">
<?php } ?>
</head>
<body>
    <header class="top-nav">
        <div class="nav-container">
            <h1 class="logo">
                <a href="ListePetition.php">PetitionHub</a>
            </h1>
            <nav class="nav-links">
                <a href="ListePetition.php">Home</a>
                <a href="CreatePetition.php" class="create-btn">Create Petition</a>
            </nav>
        </div>
    </header>
    <div class="container">
        <div class="glass-card signature-form">
            <div class="section-header">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 19l7-7 3 3-7 7-3-3z"></path>
                    <path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"></path>
                    <path d="M2 2l7.586 7.586"></path>
                    <circle cx="11" cy="11" r="2"></circle>
                </svg>
                <h2>Sign the Petition</h2>
            </div>

            <div id="successMessage" class="success-message hidden">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <p>Thank you for signing! Your voice matters.</p>
            </div>

            <form id="petitionForm" action="AjouterSignature.php" method="POST">
                <input type="hidden" name="idp" value="<?php echo htmlspecialchars($petitionId); ?>">

                <div class="form-group">
                    <label for="prenom">First Name *</label>
                    <input type="text" id="prenom" name="prenom" placeholder="Enter your first name" required>
                </div>
                <div class="form-group">
                    <label for="nom">Last Name *</label>
                    <input type="text" id="nom" name="nom" placeholder="Enter your last name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" placeholder="your.email@example.com" required>
                </div>

                <div class="form-group">
                    <label for="pays">Country *</label>
                    <input type="text" id="pays" name="pays" placeholder="Enter your country" required>
                </div>

                <button type="submit" class="submit-button">Sign Petition</button>
            </form>
        </div>
        <div class="form-group">
            <label>Last 5 Signatures</label>
            <textarea id="recentSignatures" rows="6" readonly style="width:100%;"></textarea>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const petitionId = <?php echo intval($petitionId); ?>;
            const textarea = document.getElementById('recentSignatures');

            function loadSignatures() {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', 'GetLastSignatures.php?idp=' + petitionId, true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        try {
                            const data = JSON.parse(xhr.responseText);
                            let text = '';
                            data.forEach(sig => {
                                text += `${sig.PrenomS} ${sig.NomS} (${sig.PaysS}) - ${sig.DateS}\n`;
                            });
                            textarea.value = text || 'No signatures yet.';
                        } catch (e) {
                            textarea.value = 'Error reading signatures.';
                        }
                    } else {
                        textarea.value = 'Failed to load signatures.';
                    }
                };
                xhr.send();
            }

            loadSignatures();
            setInterval(loadSignatures, 3000);
        });
    </script>
</body>
</html>