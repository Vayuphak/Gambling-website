<?php
session_start();
include_once("../config/db.php");

if (!isset($_SESSION['admin_login'])) {
    header("Location: ../homepage/homepage.php");
    exit;
}

if (isset($_POST['update_match'])) {
    $match_id = $_POST['update_match'];

    // Fetch the match's start time
    $stmt = $conn->prepare("SELECT start_time FROM matches WHERE match_id = :match_id");
    $stmt->bindParam(':match_id', $match_id);
    $stmt->execute();
    $start_time = strtotime($stmt->fetchColumn());
    $current_time = time();
    $time_diff = ($current_time - $start_time) / 60; // Time difference in minutes

    try {
        // Determine the query based on the time difference
        if ($time_diff <= 30) {
            $query = "UPDATE matches SET 
                odd_team1 = :odd_team1,
                odd_team2 = :odd_team2,
                odd_draw = :odd_draw,
                score_team_1 = :score_team_1,
                score_team_2 = :score_team_2
                WHERE match_id = :match_id";
        } else {
            $query = "UPDATE matches SET 
                score_team_1 = :score_team_1,
                score_team_2 = :score_team_2
                WHERE match_id = :match_id";
        }

        $stmt = $conn->prepare($query);

        if ($time_diff <= 30) {
            $stmt->bindParam(':odd_team1', $_POST['odd_team1'][$match_id]);
            $stmt->bindParam(':odd_team2', $_POST['odd_team2'][$match_id]);
            $stmt->bindParam(':odd_draw', $_POST['odd_draw'][$match_id]);
        }
        $stmt->bindParam(':score_team_1', $_POST['score_team_1'][$match_id]);
        $stmt->bindParam(':score_team_2', $_POST['score_team_2'][$match_id]);
        $stmt->bindParam(':match_id', $match_id);

        if ($stmt->execute()) {
            $_SESSION['score_success'] = "Match updated successfully.";
        } else {
            $_SESSION['score_error'] = "Failed to update match.";
        }
    } catch (Exception $e) {
        $_SESSION['score_error'] = "Error: " . $e->getMessage();
    }

    header("Location: adminupdate.php");
    exit;
}
?>
