<?php
// DB connection (adjust if needed)
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'tp3';

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Petition Manager</title>
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --success-color: #27ae60;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --gray-color: #95a5a6;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: var(--dark-color);
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background-color: var(--primary-color);
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
        }
        
        nav ul {
            display: flex;
            list-style: none;
        }
        
        nav ul li {
            margin-left: 20px;
        }
        
        nav ul li a {
            color: white;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        nav ul li a:hover, nav ul li a.active {
            background-color: rgba(255,255,255,0.2);
        }
        
        .main-content {
            display: flex;
            margin-top: 20px;
            gap: 20px;
        }
        
        .sidebar {
            flex: 1;
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .content {
            flex: 3;
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        h1, h2, h3 {
            margin-bottom: 15px;
            color: var(--dark-color);
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: var(--secondary-color);
        }
        
        .btn-success {
            background-color: var(--success-color);
        }
        
        .btn-success:hover {
            background-color: #219955;
        }
        
        .btn-danger {
            background-color: var(--danger-color);
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
        }
        
        .btn-warning {
            background-color: var(--warning-color);
        }
        
        .btn-warning:hover {
            background-color: #d35400;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        input, textarea, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        textarea {
            min-height: 150px;
            resize: vertical;
        }
        
        .petition-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-left: 4px solid var(--primary-color);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-left-color 0.2s ease;
        }
        .petition-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,0.12); border-left-color: var(--secondary-color); }
        
        .petition-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }
        
        .petition-title {
            font-size: 20px;
            font-weight: bold;
            color: var(--dark-color);
            margin-bottom: 5px;
        }
        
        .petition-meta {
            color: var(--gray-color);
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .petition-description {
            margin-bottom: 15px;
        }
        
        .petition-stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        
        .signature-count {
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .petition-actions {
            display: flex;
            gap: 10px;
        }
        
        .signature-form {
            margin-top: 15px;
            padding: 15px;
            background-color: var(--light-color);
            border-radius: 4px;
        }
        
        .signature-list {
            margin-top: 20px;
        }
        
        .signature-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .signature-name {
            font-weight: 500;
        }
        
        .signature-date {
            color: var(--gray-color);
            font-size: 14px;
        }
        
        .hidden {
            display: none;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .search-bar {
            margin-bottom: 20px;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        
        .page-btn {
            padding: 8px 12px;
            margin: 0 5px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .page-btn.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        @media (max-width: 768px) {
            .main-content {
                flex-direction: column;
            }
            
            .petition-header {
                flex-direction: column;
            }
            
            .petition-actions {
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">Petition Manager</div>
                <nav>
                    <ul>
                        <li><a href="#" class="nav-link active" data-page="home">Home</a></li>
                        <li><a href="#" class="nav-link" data-page="create">Create Petition</a></li>
                        <li><a href="#" class="nav-link" data-page="my-petitions">My Petitions</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
    
    <div class="container">
        <div id="alert-container"></div>
        
        <div class="main-content">
            <div class="sidebar">
                <h3>Filter Petitions</h3>
                <div class="form-group">
                    <label for="category-filter">Category</label>
                    <select id="category-filter">
                        <option value="all">All Categories</option>
                        <option value="environment">Environment</option>
                        <option value="social">Social Justice</option>
                        <option value="education">Education</option>
                        <option value="healthcare">Healthcare</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="status-filter">Status</label>
                    <select id="status-filter">
                        <option value="all">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
                <button id="apply-filters" class="btn">Apply Filters</button>
                
                <h3 style="margin-top: 30px;">Quick Stats</h3>
                <div id="stats-container">
                    <p>Total Petitions: <span id="total-petitions">0</span></p>
                    <p>Total Signatures: <span id="total-signatures">0</span></p>
                    <p>Your Petitions: <span id="user-petitions">0</span></p>
                </div>
            </div>
            
            <div class="content">
                <!-- Home Page -->
                <div id="home-page" class="page">
                    <h2>All Petitions</h2>
                    <div class="search-bar">
                        <input type="text" id="search-input" placeholder="Search petitions...">
                    </div>
                    <div id="petitions-list">
                        <?php if (empty($petitions)) { ?>
                            <p>No petitions found.</p>
                        <?php } else { ?>
                            <?php foreach ($petitions as $p) { ?>
                                <a href="Signature.php?idp=<?php echo (int)$p['IDP']; ?>" style="text-decoration:none;color:inherit;">
                                    <div class="petition-card">
                                        <div class="petition-header">
                                            <div>
                                                <div class="petition-title"><?php echo htmlspecialchars($p['TitreP']); ?></div>
                                                <div class="petition-meta">
                                                    Created by <?php echo htmlspecialchars($p['NomPorteurP']); ?> • <?php echo htmlspecialchars(date('Y-m-d', strtotime($p['DateAjoutP']))); ?>
                                                </div>
                                            </div>
                                            <div class="petition-actions">
                                                <span class="btn">Sign</span>
                                            </div>
                                        </div>
                                        <div class="petition-description"><?php echo nl2br(htmlspecialchars($p['DescriptionP'])); ?></div>
                                        <div class="petition-stats">
                                            <div class="signature-count"><?php echo (int)$p['numSign']; ?> signatures</div>
                                            <div class="petition-status">Status: <span style="color: var(--success-color)">Active</span></div>
                                        </div>
                                    </div>
                                </a>
                            <?php } ?>
                        <?php } ?>
                    </div>
                    <div class="pagination" id="pagination">
                        <!-- Pagination will be generated here -->
                    </div>
                </div>
                
                <!-- Create Petition Page -->
                <div id="create-page" class="page hidden">
                    <h2>Create New Petition</h2>
                    <form id="create-petition-form">
                        <div class="form-group">
                            <label for="petition-title">Title</label>
                            <input type="text" id="petition-title" required>
                        </div>
                        <div class="form-group">
                            <label for="petition-description">Description</label>
                            <textarea id="petition-description" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="petition-category">Category</label>
                            <select id="petition-category" required>
                                <option value="environment">Environment</option>
                                <option value="social">Social Justice</option>
                                <option value="education">Education</option>
                                <option value="healthcare">Healthcare</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="petition-goal">Signature Goal</label>
                            <input type="number" id="petition-goal" min="1" value="100" required>
                        </div>
                        <button type="submit" class="btn btn-success">Create Petition</button>
                    </form>
                </div>
                
                <!-- My Petitions Page -->
                <div id="my-petitions-page" class="page hidden">
                    <h2>My Petitions</h2>
                    <div id="my-petitions-list">
                        <!-- User's petitions will be loaded here -->
                    </div>
                </div>
                
                <!-- Petition Detail Page -->
                <div id="petition-detail-page" class="page hidden">
                    <div id="petition-detail">
                        <!-- Petition details will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>