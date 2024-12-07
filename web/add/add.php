<?php
session_start();
require_once '../config/db.php';



//add TEAM

if (isset($_POST['subteam'])) {
    $team = $_POST['team'];
    $gameType = $_POST['game_type'];

    // Check if file is uploaded
    if (isset($_FILES['team_logo']) && $_FILES['team_logo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../image/';
        $fileTmpPath = $_FILES['team_logo']['tmp_name'];
        $fileName = basename($_FILES['team_logo']['name']);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        // Validate file type
        if (in_array($fileExtension, $allowedExtensions)) {
            // Generate a unique name for the file
            $newFileName = uniqid('team_', true) . '.' . $fileExtension;
            $destPath = $uploadDir . $newFileName;

            // Move the file to the destination directory
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // Insert team data into the database
                try {
                    $stmt = $conn->prepare("INSERT INTO team (team_name, game_id, team_logo) 
                                            VALUES (:team_name, :game_id, :team_logo)");
                    $stmt->bindParam(':team_name', $team);
                    $stmt->bindParam(':game_id', $gameType);
                    $stmt->bindParam(':team_logo', $newFileName);

                    if ($stmt->execute()) {
                        $_SESSION['add_success'] = 'Team added successfully!';
                    } else {
                        $_SESSION['add_error'] = 'Failed to add team to the database.';
                    }
                } catch (PDOException $e) {
                    $_SESSION['add_error'] = 'Database error: ' . $e->getMessage();
                }
            } else {
                $_SESSION['add_error'] = 'Failed to move the uploaded file.';
            }
        } else {
            $_SESSION['add_warning'] = 'Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.';
        }
    } else {
        $_SESSION['add_warning'] = 'No file uploaded or an upload error occurred.';
    }

    header('Location: ../admin/admin.php'); 
    exit();
}

//add Match

if (isset($_POST['submatch'])) {
    $game_id = $_POST['game_type'];
    $team1id = $_POST['team1'];
    $team2id = $_POST['team2'];
    $match_start = $_POST['matchstart'];
    $match_end = $_POST['matchend'];
    $team1_odd=$_POST['odd_team1'];
    $team2_odd=$_POST['odd_team2'];
    $draw_odd=$_POST['odd_draw'];

    if (empty($team1id) || empty($team2id)) {
        $_SESSION['add_error'] = "ERROR: Team is required.";
        header("location: ../admin/admin.php");
        exit();
    } 
    else if (empty($game_id)) {
        $_SESSION['add_error'] = "ERROR: Game for team is required.";
        header("location: ../admin/admin.php");
        exit();
    }
    else if ($team1id == $team2id) {
        $_SESSION['add_error'] = "ERROR: Teams are the same.";
        header("location: ../admin/admin.php");
        exit();
    }
    else if( $team1_odd <= 0 ||  $team2_odd <=0 ||  $draw_odd  <=0 ){
        $_SESSION['add_error'] = "ERROR: Please enter valid odd";
        header("location: ../admin/admin.php");
        exit();
    }
    else if( $team1_odd >= 100 ||  $team2_odd >= 100 ||  $draw_odd  >= 100){
        $_SESSION['add_error'] = "ERROR: Odd out of range (should be less than 100)";
        header("location: ../admin/admin.php");
        exit();
    }
    else {
        try {
            // Insert the new match
            $stmt = $conn->prepare("INSERT INTO matches (team_id_1, team_id_2, start_time, end_time, game_id, odd_team1, odd_team2, odd_draw) VALUES (:team1, :team2, :starttime, :endtime, :game_id,:odd_team1,:odd_team2,:odd_draw)");
            $stmt->bindParam(":team1", $team1id);
            $stmt->bindParam(":team2", $team2id);
            $stmt->bindParam(":starttime", $match_start);
            $stmt->bindParam(":endtime", $match_end);
            $stmt->bindParam(":game_id", $game_id);
            $stmt->bindParam(":odd_team1", $team1_odd);
            $stmt->bindParam(":odd_team2", $team2_odd);
            $stmt->bindParam(":odd_draw", $draw_odd);

            // Check if the insertion was successful
            if ($stmt->execute()) {
                $_SESSION['add_success'] = "Match added successfully!";
            } else {
                $_SESSION['add_error'] = "ERROR: Could not add match.";
            }
            header("location: ../admin/admin.php");
            exit();
        } catch (PDOException $e) {
            $_SESSION['add_error'] = "ERROR: " . $e->getMessage();
            header("location: ../admin/admin.php");
            exit();
        }
    }
}
