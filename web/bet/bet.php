<?php
session_start();
require_once("../header/header.php");
if (!isset($_SESSION['user_login']) && !isset($_SESSION['admin_login'])) {
    // Redirect to homepage if not logged in
    header("Location: ../homepage/homepage.php");
    exit; // Stop further execution of the script
}
// Assuming the user's ID is stored in the session
$user_id = $_SESSION['user_login'] ?? null;
$gamename=$_GET['game'];
                    if(!isset($_GET['game'])){
                    $gamename="Football";
                }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve POST data
    $teamidbeton = $_POST['teamidbeton'] ?? null;
    $matchId = $_POST['matchId'] ?? null;
    $team1 = $_POST['team1'] ?? null;
    $team2 = $_POST['team2'] ?? null;
    $odd = $_POST['odd'] ?? null;
    $betType = $_POST['betType'] ?? null;
    $gameType = $_POST['gameType'] ?? null;

    // Store POST data in session for retrieval after redirection
    $_SESSION['post_data'] = [
        'teamidbeton' => $teamidbeton,
        'matchId' => $matchId,
        'team1' => $team1,
        'team2' => $team2,
        'odd' => $odd,
        'betType' => $betType
        
    ];
   
    // Redirect to the same page
    header("Location: " . $_SERVER['PHP_SELF'] . "?game=" . urlencode($gameType));
    exit;
}

// Fetch POST data from session if available
$postData = $_SESSION['post_data'] ?? null;
unset($_SESSION['post_data']); // Clear session after use

// Fetch user balance
$stmt = $conn->prepare("SELECT balance FROM user WHERE userID = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $balance = $user['balance'];
}
?>

<script>

userBalance= <?php echo $balance;?>;</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        <?php if ($postData): ?>
            // Pass POST data to the addMatchBet function
            addMatchBet(
                '<?php echo $user_id; ?>',
                '<?php echo $postData['teamidbeton']; ?>',
                '<?php echo $postData['matchId']; ?>',
                '<?php echo $postData['team1']; ?>',
                '<?php echo $postData['team2']; ?>',
                '<?php echo $postData['odd']; ?>',
                '<?php echo $postData['betType']; ?>'
            );
        <?php endif; ?>
    });
</script>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSS 888</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
</head>
<body style="background-color: #111; font-family: Rubik;">
<br>
<br>
<br>
    <div class="row justify-content-around mt-5">
        <div class="col-5 ">
            <h1 class="bg-warning fs-1 rounded fw-bold">Recommend Matches</h1>
            <div class="row justify-content-around my-5">
                <div class="col-5 bg-warning rounded d-flex align-items-center justify-content-center fw-bold " style="">
                    <div>
                       

                    <?php
                    $stmt123 = $conn->prepare("SELECT 
                    m.match_id,
                    t1.team_name AS team1_name,
                    t2.team_name AS team2_name, 
                    t1.team_id AS team1_id,  
                    t2.team_id AS team2_id,  
                    t1.team_logo AS teamlogo1,
                    t2.team_logo AS teamlogo2,
                    m.odd_team1, 
                    m.odd_draw, 
                    m.odd_team2, 
                    g.game_type AS game_type,
                    DATE_FORMAT(m.start_time, '%Y-%m-%d %H:%i:%s') AS formatted_start_time,
                    DATE_FORMAT(m.end_time, '%Y-%m-%d %H:%i:%s') AS formatted_end_time,
                    m.score_team_1,
                    m.score_team_2,
                    m.status
                FROM 
                    matches m 
                JOIN 
                    team t1 ON t1.team_id = m.team_id_1 
                JOIN 
                    team t2 ON t2.team_id = m.team_id_2
                JOIN 
                    game g ON g.game_id = m.game_id
                WHERE 
                    m.status != 'ended' AND game_type=:gamename
                    AND TIMESTAMPDIFF(MINUTE, m.start_time, NOW()) <= 30 
                ORDER BY 
                    m.start_time ASC
                LIMIT 2");
                $stmt123->bindParam(":gamename",$gamename);
                if ($stmt123->execute()) {
                    $live_matches = $stmt123->fetchAll(PDO::FETCH_ASSOC);
                }if (count($live_matches) >= 2) {
                    $live_match1 = $live_matches[0]; // First match
                    $live_match2 = $live_matches[1];
                }
                    ?>
                        <br>
                        <div class="text-center fs-5"><?php echo $live_match1['status']; ?></div>
                        <br>
                        <div class="text-center fs-5">Start Time: <?php echo $live_match1['formatted_start_time']; ?></div>
                        <br>
                        <br>

                        <div class="row justify-content-around">
                            <img src="../image/<?php echo $live_match1['teamlogo1'];?>" alt="" class="col-5 img-fluid">
                            <img src="../image/<?php echo $live_match1['teamlogo2'];?>" alt="" class="col-5 img-fluid">
                        </div>
                        <div class="row justify-content-around text-center">
                            <div class="col-5 fw-bold "><?php echo $live_match1['team1_name'];?></div>
                            <div class="col-5 fw-bold"><?php echo $live_match1['team2_name']; ?></div>
                        </div>
                        <br>
                        <div class="row justify-content-around">
                            <a href="#" class="col-2 my-auto text-center bg-warning-subtle rounded border border-black link-underline link-underline-opacity-0 link-dark link-opacity-50-hover " onclick=" <?php echo isset($_SESSION['user_login']) || isset($_SESSION['admin_login']) ? "addMatchBet('{$_SESSION['user_login']}', '{$live_match1['team1_id']}', '{$live_match1['match_id']}', '{$live_match1['team1_name']}', '{$live_match1['team2_name']}', '{$live_match1['odd_team1']}', '1')" : "openModal('loginModal')" ?> ">1 <?php echo $live_match1['odd_team1']; ?></a>
                            <a href="#" class="col-2 my-auto text-center bg-warning-subtle rounded border border-black link-underline link-underline-opacity-0 link-dark link-opacity-50-hover" onclick=" <?php echo isset($_SESSION['user_login']) || isset($_SESSION['admin_login']) ? "addMatchBet('{$_SESSION['user_login']}', '0', '{$live_match1['match_id']}', '{$live_match1['team1_name']}', '{$live_match1['team2_name']}', '{$live_match1['odd_draw']}', 'X')" : "openModal('loginModal')" ?> ">Draw <?php echo $live_match1['odd_draw']; ?></a>
                            <a href="#" class="col-2 my-auto text-center bg-warning-subtle rounded border border-black link-underline link-underline-opacity-0 link-dark link-opacity-50-hover" onclick=" <?php echo isset($_SESSION['user_login']) || isset($_SESSION['admin_login']) ? "addMatchBet('{$_SESSION['user_login']}', '{$live_match1['team2_id']}', '{$live_match1['match_id']}', '{$live_match1['team1_name']}', '{$live_match1['team2_name']}', '{$live_match1['odd_team2']}', '2')" : "openModal('loginModal')" ?> ">2 <?php echo $live_match1['odd_team2']; ?></a>

                        </div>
                        <br>
                        <br>
                    </div>
                </div>
                <div class="col-5 bg-warning rounded d-flex align-items-center justify-content-center fw-bold" style="">
                    <div>
                        <br>
                        <div class="text-center fs-5"><?php echo $live_match2['status']; ?></div>
                        <br>
                        <div class="text-center fs-5">Start Time: <?php echo $live_match2['formatted_start_time']; ?></div>
                        <br>
                        <br>
                        <div class="row justify-content-around">
                            <img src="../image/<?php echo $live_match2['teamlogo1'];?>" alt="" class="col-5 img-fluid">
                            <img src="../image/<?php echo $live_match2['teamlogo2'];?>" alt="" class="col-5 img-fluid">
                        </div>
                        <div class="row justify-content-around text-center">
                            <div class="col-5 fw-bold "><?php echo $live_match2['team1_name'];?></div>
                            <div class="col-5 fw-bold"><?php echo $live_match2['team2_name'];?></div>
                        </div>
                        <br>



                        
                        <div class="row justify-content-around">
                            <a href="#" class="col-2 my-auto text-center bg-warning-subtle rounded border border-black link-underline link-underline-opacity-0 link-dark link-opacity-50-hover " onclick=" <?php echo isset($_SESSION['user_login']) || isset($_SESSION['admin_login']) ? "addMatchBet('{$_SESSION['user_login']}', '{$live_match2['team1_id']}', '{$live_match2['match_id']}', '{$live_match2['team1_name']}', '{$live_match2['team2_name']}', '{$live_match2['odd_team1']}', '1')" : "openModal('loginModal')" ?> ">1 <?php echo $live_match2['odd_team1']; ?></a>
                            <a href="#" class="col-2 my-auto text-center bg-warning-subtle rounded border border-black link-underline link-underline-opacity-0 link-dark link-opacity-50-hover" onclick=" <?php echo isset($_SESSION['user_login']) || isset($_SESSION['admin_login']) ? "addMatchBet('{$_SESSION['user_login']}', '0', '{$live_match2['match_id']}', '{$live_match2['team1_name']}', '{$live_match2['team2_name']}', '{$live_match2['odd_draw']}', 'X')" : "openModal('loginModal')" ?> ">Draw <?php echo $live_match2['odd_draw']; ?></a>
                            <a href="#" class="col-2 my-auto text-center bg-warning-subtle rounded border border-black link-underline link-underline-opacity-0 link-dark link-opacity-50-hover" onclick=" <?php echo isset($_SESSION['user_login']) || isset($_SESSION['admin_login']) ? "addMatchBet('{$_SESSION['user_login']}', '{$live_match2['team2_id']}', '{$live_match2['match_id']}', '{$live_match2['team1_name']}', '{$live_match2['team2_name']}', '{$live_match2['odd_team2']}', '2')" : "openModal('loginModal')" ?> ">2 <?php echo $live_match2['odd_team2']; ?></a>

                        </div>
                        <br>
                        <br>
                    </div>
                </div>
                <script>
                    // Pass PHP session user_id to JavaScript
                    const userId = <?php echo json_encode($_SESSION['user_id']); ?>;
                </script>
            </div>
            <div class="bg-warning p-3 rounded">
                <div class="row bg-warning ">
                    <a href="bet.php?game=Football" class="col  link-underline link-underline-opacity-0 link-dark link-opacity-50-hover border border-black  fw-bold">Football</a>
                    <a href="bet.php?game=Basketball" class="col  link-underline link-underline-opacity-0 link-dark link-opacity-50-hover border border-black border-start-0 fw-bold">Basketball</a>
                    <a href="bet.php?game=Valorant" class="col  link-underline link-underline-opacity-0 link-dark link-opacity-50-hover border border-black border-start-0 fw-bold">Valorant</a>
                </div>


                    <?php  
                try {
                    
                $stmt = $conn->prepare("SELECT 
                    m.match_id,
                    t1.team_name AS team1_name,
                    t2.team_name AS team2_name, 
                    t1.team_id AS team1_id,  
                    t1.team_logo AS teamlogo1,
                    t2.team_logo AS teamlogo2,
                    t2.team_id AS team2_id,  
                    m.odd_team1, 
                    m.odd_draw, 
                    m.odd_team2, 
                    g.game_type,
                    DATE_FORMAT(m.start_time, '%Y-%m-%d %H:%i:%s') AS formatted_start_time,
                    DATE_FORMAT(m.end_time, '%Y-%m-%d %H:%i:%s') AS formatted_end_time,
                    m.score_team_1,
                    m.score_team_2,
                    m.status
                FROM 
                    matches m 
                JOIN 
                    team t1 ON t1.team_id = m.team_id_1 
                JOIN 
                    team t2 ON t2.team_id = m.team_id_2
                JOIN 
                    game g ON g.game_id = m.game_id
                WHERE 
                    m.status != 'ended' AND  game_type=:gamename
                    AND TIMESTAMPDIFF(MINUTE, m.start_time, NOW()) <= 30 
                ORDER BY 
                    m.start_time ASC");
                $stmt->bindParam(":gamename",$gamename);
                if ($stmt->execute()) {
                    $live_matches = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $_SESSION['live_matches'] = $live_matches; 
                    $_SESSION['live_success'] = "Fetched live matches successfully!"; 
                } else { 
                    $_SESSION['live_error'] = "Error fetching live matches.";
                }
                } catch (PDOException $e) { 
                $_SESSION['live_error'] = "Error: " . $e->getMessage();
                } ?>
            
            
                <div class="">
                    <?php foreach ($live_matches as $livematch) { ?>
                        <div class="row justify-content-around">
                    <div class="col-6 "><?php echo ($livematch['status']=='playing') ? 'Playing: End Time'. $livematch['formatted_end_time'] : ' Not started:Start Time'. $livematch['formatted_start_time']; ?></div>
                    <div class="col-1 text-center">1</div>
                    <div class="col-1 text-center">X</div>
                    <div class="col-1 text-center">2</div>
                </div>
                <div class="row justify-content-around">
                    <div class="col-6">
                        <div class="row">
                            <div class="col-10"> <img src="../image/<?php echo $livematch['teamlogo1'];?>" alt="Description of image" style="width: 3rem; height: 3rem;"> <?php echo $livematch['team1_name']; ?></div>
                            <div class="col-2"><?php echo $livematch['score_team_1']; ?></div>
                        </div>
                        <div class="row">
                            <div class="col-10"> <img src="../image/<?php echo $livematch['teamlogo2'];?>" alt="Description of image" style="width: 3rem; height: 3rem;"> <?php echo $livematch['team2_name']; ?></div>
                            <div class="col-2"><?php echo $livematch['score_team_2']; ?></div>
                        </div>
                    </div>
                    <a href="#" class="col-1 my-auto text-center bg-warning-subtle rounded border border-black link-underline link-underline-opacity-0 link-dark link-opacity-50-hover" onclick="addMatchBet('<?php echo $_SESSION['user_login']; ?>', '<?php echo $livematch['team1_id']; ?>', '<?php echo $livematch['match_id']; ?>', '<?php echo $livematch['team1_name']; ?>', '<?php echo $livematch['team2_name']; ?>', '<?php echo $livematch['odd_team1']; ?>', '1')"><?php echo $livematch['odd_team1']; ?></a>
                    <a href="#" class="col-1 my-auto text-center bg-warning-subtle rounded border border-black link-underline link-underline-opacity-0 link-dark link-opacity-50-hover" onclick="addMatchBet('<?php echo $_SESSION['user_login']; ?>', '<?php echo '0'; ?>', '<?php echo $livematch['match_id']; ?>', '<?php echo $livematch['team1_name']; ?>', '<?php echo $livematch['team2_name']; ?>', '<?php echo $livematch['odd_draw']; ?>', 'X')"><?php echo $livematch['odd_draw']; ?></a>
                    <a href="#" class="col-1 my-auto text-center bg-warning-subtle rounded border border-black link-underline link-underline-opacity-0 link-dark link-opacity-50-hover" onclick="addMatchBet('<?php echo $_SESSION['user_login']; ?>', '<?php echo $livematch['team2_id']; ?>', '<?php echo $livematch['match_id']; ?>', '<?php echo $livematch['team1_name']; ?>', '<?php echo $livematch['team2_name']; ?>', '<?php echo $livematch['odd_team2']; ?>', '2')"><?php echo $livematch['odd_team2']; ?></a>
                </div>
                
                

            
            
            <?php } ?>
        </div>
        </div>
        </div>
        
        <div class="col-5">
            <div class="bg-warning rounded">
                <h1 class="fs-2 fw-bold pt-3">Your Bet</h1>
        
                <!-- Bet Matches List -->
                <div class="matchbet-container">
                    <!-- Matches will be added here dynamically -->
                </div>
        
                <!-- Displaying the updated Overall Odds -->
                <h3 style="float:right;">Overall odds: <span id="overall-odds">1.00</span></h3>
                <div></div>
                <h3>Potential earning: <span id="potential-earnings">0.00</span> (THB)</h3>
        
                <!-- Bet Amount and Place Bet Button -->
                <div class="bet-container">
                    <input type="number" class="amount-input rounded" id="bet-amount" value="0.00" min="0" step="0.01" onchange="updatePotentialEarnings()" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                    <button class="btn btn-success mt-3 fs-3" onclick="placeBet()" type="button" name="subbet">Place a bet</button>
                </div>
                
           
        </div>
        </div>
    </div>
    



 
</body>
</html>