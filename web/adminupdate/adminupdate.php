<?php
session_start();
include_once("../config/db.php");
require_once("../header/header.php");
if ( !isset($_SESSION['admin_login'])) {
    // Redirect to homepage if not logged in
    header("Location: ../homepage/homepage.php");
    exit; // Stop further execution of the script
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Update Scores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="style.css">
</head>
<body style="background:#222; font-family: Rubik";>
    <div style="color:white;"><?php echo $_SESSION['score_success']. $_SESSION['score_error']. $_SESSION['delete_success']. $_SESSION['delete_error'];
    unset( $_SESSION['score_success']);
    unset( $_SESSION['score_error']);
    unset( $_SESSION['delete_success']);
    unset( $_SESSION['delete_error']);
    ?></div>


<h2 style="color:white;">Not st Match</h2>
<form action="update_match.php" method="POST">
    <table style="color:white;" border="1">
        <div style="color:white";>not start</div>
        <tr>
            <th>Match ID</th>
            <th>Team 1 / Odd</th>
            <th>Team 2 / Odd</th>
            <th>Odd Draw</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Game</th>
            <th>Actions</th>
        </tr>
        <?php
        // Query to fetch matches with 'notstart' status
        $stmt1 = $conn->prepare("SELECT 
            m.match_id,
            t1.team_name AS team1_name,
            t2.team_name AS team2_name,
            m.status, 
            m.odd_team1 AS odd_team1,
            m.odd_team2 AS odd_team2,
            m.odd_draw AS odd_draw,
            DATE_FORMAT(m.start_time, '%Y-%m-%d %H:%i:%s') AS formatted_start_time,
            DATE_FORMAT(m.end_time, '%Y-%m-%d %H:%i:%s') AS formatted_end_time,
            g.game_type
        FROM matches m
        JOIN team t1 ON m.team_id_1 = t1.team_id
        JOIN team t2 ON m.team_id_2 = t2.team_id
        JOIN game g ON m.game_id = g.game_id
        WHERE m.status = 'notstart';");

        $stmt1->execute();
        $matches = $stmt1->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($matches)) {
            foreach ($matches as $row) {
                echo "<tr>";
                echo "<td>" . $row["match_id"] . "</td>";
                echo "<td>" . $row['team1_name'] . " / <input type='text' name='odd_team1[" . $row["match_id"] . "]' value='" . $row["odd_team1"] . "'></td>";
                echo "<td>" . $row['team2_name'] . " / <input type='text' name='odd_team2[" . $row["match_id"] . "]' value='" . $row["odd_team2"] . "'></td>";
                echo "<td><input type='text' name='odd_draw[" . $row["match_id"] . "]' value='" . $row["odd_draw"] . "'></td>";
                echo "<td><input type='datetime-local' name='start_time[" . $row["match_id"] . "]' value='" . str_replace(' ', 'T', $row["formatted_start_time"]) . "'></td>";
                echo "<td><input type='datetime-local' name='end_time[" . $row["match_id"] . "]' value='" . str_replace(' ', 'T', $row["formatted_end_time"]) . "'></td>";
                echo "<td>" . $row['game_type'] . "</td>";
                echo "<td><button type='submit' name='update_match' value='" . $row["match_id"] . "'>Update</button></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No matches found</td></tr>";
        }
        ?>
    </table>
</form>


<h2 style="background:#222; color:white;">Update Match Scores and Odds</h2>
    <form action="update_scores.php" method="POST">
        <table style="color:white;" border="1">
            <tr>
                <th>Match ID</th>
                <th>Team 1 / Odd</th>
                <th>Team 2 / Odd</th>
                <th>Odd Draw</th>
                <th>Team 1 Score</th>
                <th>Team 2 Score</th>
                <th>End Time</th>
                <th>Actions</th>
            </tr>
            <?php
            $stmt = $conn->prepare("SELECT 
                m.match_id,
                t1.team_name AS team1_name,
                t2.team_name AS team2_name,
                m.odd_team1,
                m.odd_team2,
                m.odd_draw,
                m.score_team_1,
                m.score_team_2,
                DATE_FORMAT(m.start_time, '%Y-%m-%d %H:%i:%s') AS formatted_start_time,
                  DATE_FORMAT(m.end_time, '%Y-%m-%d %H:%i:%s') AS formatted_end_time
            FROM matches m
            JOIN team t1 ON m.team_id_1 = t1.team_id
            JOIN team t2 ON m.team_id_2 = t2.team_id
            WHERE m.status IN ('playing')");
            $stmt->execute();
            $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $current_time = time();

            foreach ($matches as $row) {
                date_default_timezone_set('Asia/Bangkok'); 
                $current_time = time(); 
                $start_time = strtotime($row['formatted_start_time']); 
                $time_diff = ($current_time - $start_time) / 60; 
                $disable_odds = ($time_diff > 30) ? "disabled" : ""; 

                echo "<tr>";
                echo "<td>" . $row['match_id'] . "</td>";
                echo "<td>" . $row['team1_name'] . " / <input type='text' name='odd_team1[" . $row['match_id'] . "]' value='" . $row['odd_team1'] . "' $disable_odds></td>";
                echo "<td>" . $row['team2_name'] . " / <input type='text' name='odd_team2[" . $row['match_id'] . "]' value='" . $row['odd_team2'] . "' $disable_odds></td>";
                echo "<td><input type='text' name='odd_draw[" . $row['match_id'] . "]' value='" . $row['odd_draw'] . "' $disable_odds></td>";
                echo "<td><input type='number' name='score_team_1[" . $row['match_id'] . "]' value='" . $row['score_team_1'] . "'></td>";
                echo "<td><input type='number' name='score_team_2[" . $row['match_id'] . "]' value='" . $row['score_team_2'] . "'></td>";
                echo "<td>" . $row['formatted_end_time'] . "</td>";
                echo "<td><button type='submit' name='update_match' value='" . $row['match_id'] . "'>Update</button></td>";
                echo "</tr>";
            }
            ?>
        </table>
    </form>
</body>
</html>

