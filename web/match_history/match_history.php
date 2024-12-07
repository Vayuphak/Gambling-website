<?php
session_start();
require_once("../header/header.php"); // Include the database connection
if (!isset($_SESSION['user_login']) && !isset($_SESSION['admin_login'])) {
    // Redirect to homepage if not logged in
    header("Location: ../homepage/homepage.php");
    exit; // Stop further execution of the script
}
$searchResults = [];
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $searchTerm = trim($_GET['search']);

    try {
        // Query to fetch matches with status "ended" for the searched team
        $stmt = $conn->prepare("
            SELECT 
                m.match_id, 
                t1.team_name AS team1_name, 
                t2.team_name AS team2_name,
                t1.team_logo AS teamlogo1,
                t2.team_logo AS teamlogo2,
                m.score_team_1, 
                m.score_team_2, 
                m.end_time
            FROM 
                matches m
            JOIN 
                team t1 ON t1.team_id = m.team_id_1
            JOIN 
                team t2 ON t2.team_id = m.team_id_2
            WHERE 
                m.status = 'ended' 
                AND (t1.team_name LIKE :search OR t2.team_name LIKE :search)
            ORDER BY 
                m.end_time DESC
        ");
        $likeSearchTerm = '%' . $searchTerm . '%';
        $stmt->bindParam(':search', $likeSearchTerm);
        $stmt->execute();
        $searchResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $errorMessage = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Matches</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family:Rubik;
            margin: 0;
            padding: 0;
            background-color: #111;
        }
        .container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: #333;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .search-bar {
            display: flex;
            margin-bottom: 20px;
        }
        .search-bar input[type="text"] {
            flex: 1;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px 0 0 5px;
            background: #555;
            outline: none;
        }
        .search-bar button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .search-bar button:hover {
            background-color: #0056b3;
        }
        .matches {
            margin-top: 20px;
        }
        .match {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            margin-bottom: 10px;
            background: #f1f1f1;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .match .teams {
            flex: 1;
        }
        .match .score {
            font-weight: bold;
            margin: 0 20px;
        }
        .match .end-time {
            font-size: 14px;
            color: #555;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-warning">Search Matches by Team Name</h1>
        <form method="GET" action="" class="search-bar">
            <input type="text" name="search" placeholder="Enter team name..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" required>
            <button type="submit">Search</button>
        </form>

        <?php if ($errorMessage): ?>
            <p class="error"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <?php if ($searchResults): ?>
            <div class="matches">
                <?php foreach ($searchResults as $match): ?>
                    <div class="match">
                        <div class="teams">
                        <img src="../image/<?php echo htmlspecialchars($match['teamlogo1']); ?>" alt="" class="img-fluid" style="width: 5rem; height: 5rem;">
    
                            <strong><?php echo htmlspecialchars($match['team1_name']); ?></strong> 
                            vs 
                            <img src="../image/<?php echo htmlspecialchars($match['teamlogo2']); ?>" alt="" class="img-fluid" style="width: 5rem; height: 5rem;">
                            <strong><?php echo htmlspecialchars($match['team2_name']); ?></strong>
                        </div>
                        <div class="score">
                            
                            <?php echo $match['score_team_1']; ?> - <?php echo $match['score_team_2']; ?>
                        </div>
                        <div class="end-time">
                            Ended on: <?php echo htmlspecialchars($match['end_time']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])): ?>
            <p>No matches found for "<strong><?php echo htmlspecialchars($_GET['search']); ?></strong>".</p>
        <?php endif; ?>
    </div>
</body>
</html>
