<?php
session_start();
if (!isset($_SESSION['user_login'])) {
    // Redirect to homepage if not logged in
    header("Location: ../homepage/homepage.php");
    exit; // Stop further execution of the script
}
require_once("../header/header.php");
if (!isset($_SESSION['user_login']) && !isset($_SESSION['admin_login'])) {
    // Redirect to homepage if not logged in
    header("Location: ../homepage/homepage.php");
    exit; // Stop further execution of the script
}
// Ensure the user is logged in
if (!isset($_SESSION['user_login'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

try {
    // Set pagination variables
    $records_per_page = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $records_per_page;

    // Get the total number of unique bet IDs
    $count_sql = "SELECT COUNT(DISTINCT bet_id) FROM bet WHERE user_id = :user_id";
    $stmt = $conn->prepare($count_sql);
    $stmt->bindValue(':user_id', $_SESSION['user_login'], PDO::PARAM_INT);
    $stmt->execute();
    $total_records = $stmt->fetchColumn();
    $total_pages = ceil($total_records / $records_per_page);

    // SQL query to fetch betting details, including pending bets
    $sql = "
    SELECT 
        bet.bet_id, 
        bet.bet_amount,
        GROUP_CONCAT(
            DISTINCT CONCAT(
                COALESCE(team1.team_name, 'N/A'), ' vs ', COALESCE(team2.team_name, 'N/A'),
                ' (Bet on: ', COALESCE(team_bet.team_name, 'N/A'), ', Odd: ', match_bet.odd, ')'
            ) SEPARATOR ' | '
        ) AS match_details,
        CASE 
            WHEN bet.bet_outcome IS NOT NULL THEN 
                (CASE WHEN bet.bet_outcome > 0 THEN 'Win' ELSE 'Lose' END)
            ELSE 'Pending'
        END AS outcome,
        EXP(SUM(LOG(match_bet.odd))) AS total_odds,
        GROUP_CONCAT(`matches`.status SEPARATOR ', ') AS match_statuses
    FROM 
        bet
    JOIN 
        match_bet ON bet.bet_id = match_bet.bet_id
    JOIN 
        `matches` ON match_bet.match_id = `matches`.match_id
    LEFT JOIN 
        team AS team1 ON `matches`.team_id_1 = team1.team_id
    LEFT JOIN 
        team AS team2 ON `matches`.team_id_2 = team2.team_id
    LEFT JOIN 
        team AS team_bet ON match_bet.teamid_bet_on = team_bet.team_id
    WHERE 
        bet.user_id = :user_id
    GROUP BY 
        bet.bet_id
     ORDER BY 
        bet.bet_id DESC
    LIMIT :limit OFFSET :offset";

    // Prepare and execute the paginated query
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $_SESSION['user_login'], PDO::PARAM_INT);
    $stmt->execute();

    // Fetch and display results
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Betting Page</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
</head>
<body style="background-color: #111; font-family: Rubik;">
    <div class="">
        <!-- Profile Content -->
    </div>

    <!-- Betting Table with Pagination -->
     <div class="my-5 row justify-content-center">
     <div class="py-4 col-6 rounded" style="background-color:#333">
     <div class="fs-1 fw-bold text-warning text-center">Your bet</div>
    <div class="row d-flex justify-content-center">
    <table class="col-10">
        <thead>
            <tr>
                <th class="">Bet ID</th>
                <th class="">Bet Amount</th>
                <th class="">Match Details</th>
                <th class="">Overall Odds</th>
                <th class="">Outcome</th>
                <th class="">Action</th>
            </tr>
        </thead>
        <tbody style="color:white;">
            <?php
            if ($results) {
                foreach ($results as $row) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['bet_id']) . "</td>";
                    echo "<td>" . htmlspecialchars(number_format($row['bet_amount'], 2)) . "</td>";
                    echo "<td>" . htmlspecialchars($row['match_details']) . "</td>";
                    echo "<td>" . htmlspecialchars(number_format(round($row['total_odds'], 2), 2)) . "</td>";
                    echo "<td class='outcome'>" . htmlspecialchars($row['outcome']) . "</td>";

                    // Determine if all match statuses are 'notstarted'
                    $statuses = explode(', ', $row['match_statuses']);
                    $can_delete = true;
                    foreach ($statuses as $status) {
                        if ($status != 'notstart') {
                            $can_delete = false;
                            break;
                        }
                    }

                    // Show delete button only if bet is pending and all match statuses are 'notstarted'
                    if ($row['outcome'] == 'Pending' && $can_delete) {
                        echo "<td><a href='delete_bet.php?bet_id=" . htmlspecialchars($row['bet_id']) . "' onclick=\"return confirm('Are you sure you want to delete this bet?');\">Delete</a></td>";
                    } else {
                        echo "<td></td>";
                    }

                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No data available</td></tr>";
            }
            ?>
        </tbody>
    </table>
    </div>
    </div>
    </div>
    <!-- Pagination Links -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>">&laquo; Previous</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="<?php if ($i == $page) echo 'active'; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>">Next &raquo;</a>
        <?php endif; ?>
    
    </div>
    <style>
        body { background-color: black; color: dark; font-family: Rubik; }
        th, td { padding: 10px; text-align: center; border-bottom: 1px solid #555; }
        th { background-color: #555; color: white; }
        tr:hover { background-color: #444; }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .pagination a {
            margin: 0 5px;
            padding: 8px 12px;
            text-decoration: none;
            color: #000;
            border: 1px solid #ddd;
            border-radius: 5px;
            color: white;
        }
        .pagination a.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        .pagination a:hover {
            background-color: #ddd;
        }
    </style>
</body>
</html>
