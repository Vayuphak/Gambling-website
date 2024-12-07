<?php
include_once("../config/db.php");

if(isset($_POST["game_id"])) {
    $game_id = $_POST["game_id"];
    try {
        $stmt = $conn->prepare("SELECT team_id, team_name FROM team WHERE game_id = :game_id");
        $stmt->bindParam(':game_id', $game_id);
        $stmt->execute();
        $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($teams) {
            foreach($teams as $team) {
                echo '<option value="' . htmlspecialchars($team['team_id']) . '">' . htmlspecialchars($team['team_name']) . '</option>';
            }
        } else {
            echo '<option value="">No teams available</option>';
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
