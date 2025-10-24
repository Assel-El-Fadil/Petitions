<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Petition</title>
    <link rel="stylesheet" href="style.css">
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
                <h2>Create Petition</h2>
            </div>

            <div id="successMessage" class="success-message hidden">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <p>Thank you for signing! Your voice matters.</p>
            </div>

            <form id="petitionForm" action="AjouterPetition.php" method="POST">
                <div class="form-group">
                    <label for="titre">Title *</label>
                    <input type="text" id="titre" name="TitreP" placeholder="Enter a title for your petition" required>
                </div>

                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="DescriptionP" placeholder="Enter a description for your petition" rows="4"></textarea>
                </div>

                <div class="form-group">
                    <label for="name">Name *</label>
                    <input type="text" id="name" name="NomPorteurP" placeholder="Enter your name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="Email" placeholder="Enter your email" required>
                </div>

                <div class="form-group">
                    <label for="date">Date de fin *</label>
                    <input type="date" id="date" name="DateFinP" placeholder="Enter the date of the end of the petition" required>
                </div>

                <button type="submit" class="submit-button">Create Petition</button>
            </form>
        </div>
    </div>
</body>
</html>