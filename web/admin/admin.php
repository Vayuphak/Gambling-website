<?php
session_start();
include_once("../config/db.php");
include_once("../header/header.php");
if (!isset($_SESSION['admin_login'])) {
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
    <title>CSS888</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body style="background:#222; font-family: Rubik;">
    <?php if (isset($_SESSION['add_success'])): ?> <div class="add_success"><?php echo $_SESSION['add_success']; unset($_SESSION['add_success']); ?></div> <?php endif; ?>
    <?php if (isset($_SESSION['add_error'])): ?> <div class="add_error"><?php echo $_SESSION['add_error']; unset($_SESSION['add_error']); ?></div> <?php endif; ?>
    <?php if (isset($_SESSION['add_warning'])): ?> <div class="add_warning"><?php echo $_SESSION['add_warning']; unset($_SESSION['add_warning']); ?></div> <?php endif; ?>

    <h2 class="text-light">Team</h2>
    <form action="../add/add.php" method="POST" enctype="multipart/form-data">
        <label>Team</label>
        <input type="text" name="team" required>
        <label>Team Logo</label>
        <input type="file" name="team_logo" accept="image/*" style="color:white;" required>
        <label>For Game</label>
        <select name="game_type">
            <?php
            try {
                $stmt = $conn->prepare("SELECT game_id, game_type FROM game"); 
                $stmt->execute();
                $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($games as $game) {
                    echo '<option value="' . htmlspecialchars($game['game_id']) . '">' . htmlspecialchars($game['game_type']) . '</option>';
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
            ?>
        </select>
        <button type="submit" name="subteam">New Team</button>
    </form>

    <h2 class="text-light">Match</h2>
    <form id="matchForm" action="../add/add.php" method="POST">
        <label>Game Type</label>
        <select name="game_type" id="gameType">
            <option value="">Select Game</option>
            <?php
            try {
                $stmt = $conn->prepare("SELECT game_id, game_type FROM game"); 
                $stmt->execute();
                $games = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($games as $game) {
                    echo '<option value="' . htmlspecialchars($game['game_id']) . '">' . htmlspecialchars($game['game_type']) . '</option>';
                }
            } catch (PDOException $e) {
                echo "Error: " . $e->getMessage();
            }
            ?>
        </select>
        <label>Team 1</label>
        <select name="team1" id="team1">
            <option value="">Select Team 1</option>
        </select>
        <label>Team 1 odd</label>
        <input type="number" step="0.01" max="99.99" placeholder="0.00" name="odd_team1" title="Enter a decimal number with up to 2 decimal places and a maximum value of 99.99">

        <label>Team 2</label>
        <select name="team2" id="team2">
            <option value="">Select Team 2</option>
        </select>
        <label>Team 2 odd</label>
        <input type="number" step="0.01" max="99.99" placeholder="0.00" name="odd_team2" title="Enter a decimal number with up to 2 decimal places and a maximum value of 99.99">

        <label>Draw odd</label>
        <input type="number" step="0.01" max="99.99" placeholder="0.00" name="odd_draw" title="Enter a decimal number with up to 2 decimal places and a maximum value of 99.99">
        <label for="event">Match start</label>
        <input type="datetime-local" name="matchstart" required>
        <label for="event">Match end</label>
        <input type="datetime-local" name="matchend" required>
        <button type="submit" name="submatch">New Match</button>
    </form>

    <script>
        // Validation for match form
        document.getElementById('matchForm').addEventListener('submit', function(event) {
            // Get the start and end time values
            const startTime = new Date(document.querySelector('[name="matchstart"]').value);
            const endTime = new Date(document.querySelector('[name="matchend"]').value);

            // Check if end time is greater than start time
            if (isNaN(startTime.getTime()) || isNaN(endTime.getTime())) {
                alert('Please enter valid dates for both start and end time.');
                event.preventDefault(); // Prevent form submission
                return;
            }

            if (endTime <= startTime) {
                alert('Match end time must be greater than match start time.');
                event.preventDefault(); // Prevent form submission
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#gameType').change(function() {
                var gameId = $(this).val();
                if (gameId) {
                    $.ajax({
                        type: 'POST',
                        url: 'fetch_teams.php',
                        data: 'game_id=' + gameId,
                        success: function(html) {
                            $('#team1').html(html);
                            $('#team2').html(html);
                        }
                    }); 
                } else {
                    $('#team1').html('<option value="">Select Team 1</option>');
                    $('#team2').html('<option value="">Select Team 2</option>'); 
                }
            });
        });
    </script>
</body>
</html>
