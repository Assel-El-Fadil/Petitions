<?php

$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'petition';

$conn = @new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    http_response_code(500);
    die('Database connection failed');
}

$petitions = [];
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
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $petitions[] = $row;
    }
    $result->free();
}
$topPetition = null;
$topSql = "
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
$topResult = $conn->query($topSql);
if ($topResult && $topResult->num_rows > 0) {
    $topPetition = $topResult->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetitionHub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="background-gradient"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <header class="top-nav">
        <div class="nav-container">
            <h1 class="logo">
                <a href="#">PetitionHub</a>
            </h1>
            <nav class="nav-links">
                <a href="#">Home</a>
                <a href="CreatePetition.php" class="create-btn">Create Petition</a>
            </nav>
        </div>
    </header>
    <div class="container">
        <div class="content-grid">
            <div class="glass-card signature-form">
                <?php if ($topPetition): ?>
                    <div class="section-header">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 19l7-7 3 3-7 7-3-3z"></path>
                            <path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"></path>
                            <path d="M2 2l7.586 7.586"></path>
                            <circle cx="11" cy="11" r="2"></circle>
                        </svg>
                        <h2>Most Signed Petition</h2>
                    </div>

                    <h1 class="main-title"><?= htmlspecialchars($topPetition['TitreP']) ?></h1>

                    <p class="main-description">
                        <?= nl2br(htmlspecialchars($topPetition['DescriptionP'])) ?>
                    </p>

                    <p><strong>By:</strong> <?= htmlspecialchars($topPetition['NomPorteurP']) ?></p>
                    <p><strong>Signatures:</strong> <?= (int)$topPetition['numSign'] ?></p>
                    <p><strong>Date Added:</strong> <?= htmlspecialchars($topPetition['DateAjoutP']) ?></p>

                    <div class="petition-actions" style="margin-top: 1.5rem;">
                        <a href="Signature.php?id=<?= $topPetition['IDP'] ?>" class="submit-button">
                            Sign This Petition
                        </a>
                    </div>
                <?php else: ?>
                    <p>No petitions found to display.</p>
                <?php endif; ?>
            </div>

            <div class="glass-card signature-list">
                <div class="section-header">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    <h2>Recent Signatures</h2>
                </div>

                <div id="signaturesList" class="signatures-container">
                </div>
            </div>
        </div>    
        <div class="petition-grid">
            <?php if (!empty($petitions)): ?>
                <?php foreach ($petitions as $petition): ?>
                    <div class="glass-card petition-header">
                        <div class="header-top">
                            <div class="icon-badge">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                            </div>
                            <span class="status-badge">Active Petition</span>
                        </div>

                        <h1 class="main-title"><?= htmlspecialchars($petition['TitreP']) ?></h1>

                        <p class="main-description">
                            <?= nl2br(htmlspecialchars($petition['DescriptionP'])) ?>
                        </p>

                        <div class="petition-meta">
                            <ul>
                                <li><p><strong>By:</strong> <?= htmlspecialchars($petition['NomPorteurP']) ?></p></li>
                                <li><p><strong>Date Added:</strong> <?= htmlspecialchars($petition['DateAjoutP']) ?></p></li>
                                <li><p><strong>Signatures:</strong> <?= (int)$petition['numSign'] ?></p></li>
                            </ul>
                        </div>

                        <div class="petition-actions">
                            <a href="Signature.php?id=<?= $petition['IDP'] ?>" class="submit-button">
                                Sign
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-data">No petitions found.</p>
            <?php endif; ?>
        </div>
        <div id="newPetitionAlert" class="new-petition-alert hidden">
            <span id="alertText"></span>
            <button id="dismissAlert">X</button>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const listContainer = document.getElementById('signaturesList');

            function loadRecentSignatures() {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', 'GetSignatures.php', true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            try {
                                const data = JSON.parse(xhr.responseText);
                                if (Array.isArray(data) && data.length > 0) {
                                    listContainer.innerHTML = '';
                                    data.forEach(sig => {
                                        const div = document.createElement('div');
                                        div.classList.add('signature-item');
                                        div.textContent = `${sig.PrenomS} ${sig.NomS} (${sig.PaysS}) - ${sig.DateS}`;
                                        listContainer.appendChild(div);
                                    });
                                } else {
                                    listContainer.innerHTML = '<p>No signatures found yet.</p>';
                                }
                            } catch (e) {
                                listContainer.innerHTML = '<p>Error loading signatures.</p>';
                            }
                        } else {
                            listContainer.innerHTML = '<p>Failed to load data.</p>';
                        }
                    }
                };
                xhr.send();
            }

            loadRecentSignatures();

            setInterval(loadRecentSignatures, 3000);
        });

        document.addEventListener('DOMContentLoaded', function() {
            const petitionCard = document.querySelector('.signature-form');
            const titleEl = petitionCard.querySelector('.main-title');
            const descriptionEl = petitionCard.querySelector('.main-description');
            const authorEl = petitionCard.querySelector('p strong:nth-of-type(1)')?.parentNode;
            const signaturesEl = petitionCard.querySelector('p strong:nth-of-type(2)')?.parentNode;
            const dateEl = petitionCard.querySelector('p strong:nth-of-type(3)')?.parentNode;
            const signButton = petitionCard.querySelector('.petition-actions a');

            function updateTopPetition() {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', 'GetTopPetition.php', true);

                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        try {
                            const data = JSON.parse(xhr.responseText);
                            if (data && data.TitreP) {
                                titleEl.textContent = data.TitreP;
                                descriptionEl.innerHTML = data.DescriptionP.replace(/\n/g, '<br>');
                                authorEl.innerHTML = `<strong>By:</strong> ${data.NomPorteurP}`;
                                signaturesEl.innerHTML = `<strong>Signatures:</strong> ${data.numSign}`;
                                dateEl.innerHTML = `<strong>Date Added:</strong> ${data.DateAjoutP}`;
                                signButton.href = `Signature.php?id=${data.IDP}`;
                            }
                        } catch (err) {
                            console.error('Error parsing JSON:', err);
                        }
                    }
                };

                xhr.send();
            }

            updateTopPetition();
            setInterval(updateTopPetition, 3000);
        });

        document.addEventListener('DOMContentLoaded', function() {
            const alertBox = document.getElementById('newPetitionAlert');
            const alertText = document.getElementById('alertText');
            const dismissBtn = document.getElementById('dismissAlert');

            let lastPetitionId = 0;

            function checkNewPetitions() {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', 'CheckNewPetitions.php', true);

                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        try {
                            const data = JSON.parse(xhr.responseText);
                            if (data && data.IDP) {
                                if (lastPetitionId === 0) {
                                    lastPetitionId = data.IDP;
                                } 
                                else if (data.IDP > lastPetitionId) {
                                    lastPetitionId = data.IDP;
                                    showNotification(data.TitreP);
                                }
                            }
                        } catch (err) {
                            console.error('Error parsing response:', err);
                        }
                    }
                };

                xhr.send();
            }

            function showNotification(title) {
                alertText.textContent = ` New Petition Added: "${title}"`;
                alertBox.classList.remove('hidden');

                setTimeout(() => {
                    alertBox.classList.add('hidden');
                }, 3000);
            }

            dismissBtn.addEventListener('click', () => {
                alertBox.classList.add('hidden');
            });

            setInterval(checkNewPetitions, 3000);
            checkNewPetitions();
        });

        document.addEventListener('DOMContentLoaded', function() {
            const petitionGrid = document.querySelector('.petition-grid');

            function loadPetitions() {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', 'GetPetitions.php', true);

                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        try {
                            const data = JSON.parse(xhr.responseText);
                            if (Array.isArray(data)) {
                                petitionGrid.innerHTML = '';
                                data.forEach(petition => {
                                    const card = document.createElement('div');
                                    card.classList.add('glass-card', 'petition-header');
                                    card.innerHTML = `
                                        <div class="header-top">
                                            <div class="icon-badge">
                                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                    <polyline points="14 2 14 8 20 8"></polyline>
                                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                                    <polyline points="10 9 9 9 8 9"></polyline>
                                                </svg>
                                            </div>
                                            <span class="status-badge">Active Petition</span>
                                        </div>
                                        <h1 class="main-title">${petition.TitreP}</h1>
                                        <p class="main-description">${petition.DescriptionP.replace(/\n/g, '<br>')}</p>
                                        <div class="petition-meta">
                                            <ul>
                                                <li><p><strong>By:</strong> ${petition.NomPorteurP}</p></li>
                                                <li><p><strong>Date Added:</strong> ${petition.DateAjoutP}</p></li>
                                                <li><p><strong>Signatures:</strong> ${petition.numSign}</p></li>
                                            </ul>
                                        </div>
                                        <div class="petition-actions">
                                            <a href="Signature.php?id=${petition.IDP}" class="submit-button">Sign</a>
                                        </div>
                                    `;
                                    petitionGrid.appendChild(card);
                                });
                            } else {
                                petitionGrid.innerHTML = '<p class="no-data">No petitions found.</p>';
                            }
                        } catch (err) {
                            console.error('JSON parse error:', err);
                            petitionGrid.innerHTML = '<p class="no-data">Error loading petitions.</p>';
                        }
                    }
                };
                xhr.send();
            }

            loadPetitions();
            setInterval(loadPetitions, 3000);
        });
    </script>
</body>
</html>