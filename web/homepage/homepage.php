<?php require_once("../header/header.php");?>
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


     

</head>
<body> 
    <br>
    <br>
    <br>
    <div class="row container justify-content-around pt-5 ">
       
    
        <div class="col-5 ">
            <div class="row container h-50 justify-content-around pb-3 ">
                <a href="<?php echo isset($_SESSION['user_login']) ||   isset($_SESSION['admin_login']) ? '../match_history/match_history.php' : '#'; ?>" class="col-5 bg-warning rounded fs-1 d-flex align-items-center justify-content-center link-offset-2 link-underline link-underline-opacity-0 link-dark link-opacity-50-hover fw-bold" <?php echo isset($_SESSION['user_login']) ||   isset($_SESSION['admin_login']) ? '' : 'onclick="openModal(\'loginModal\')"'; ?>><div class="menu">Match History</div></a>
                <a href="<?php echo isset($_SESSION['user_login']) ||   isset($_SESSION['admin_login']) ? '../yourbet/yourbet.php' : '#'; ?>" class="col-5 text-center bg-warning rounded fs-1 d-flex align-items-center justify-content-center link-offset-2 link-underline link-underline-opacity-0 link-dark link-opacity-50-hover fw-bold" <?php echo isset($_SESSION['user_login']) ||   isset($_SESSION['admin_login']) ? '' : 'onclick="openModal(\'loginModal\')"'; ?>><div class="menu">Your Bet</div></a>
                </div>
            <div class="row container h-50 justify-content-around pt-3">
                <a href="<?php echo isset($_SESSION['user_login']) ||   isset($_SESSION['admin_login']) ? '../bet/bet.php' : '#'; ?>" class="col-5 text-center bg-warning rounded fs-1 d-flex align-items-center justify-content-center link-offset-2 link-underline link-underline-opacity-0 link-dark link-opacity-50-hover fw-bold" <?php echo isset($_SESSION['user_login']) ||   isset($_SESSION['admin_login']) ? '' : 'onclick="openModal(\'loginModal\')"'; ?>><div class="menu">Sport</div></a>
                <div class="col-5 bg-warning rounded d-flex align-items-center justify-content-center fw-bold" style="">
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
                         m.status != 'ended' AND  game_type='Football'
                    AND TIMESTAMPDIFF(MINUTE, m.start_time, NOW()) <= 30 
                     ORDER BY 
                         m.start_time ASC
                         LIMIT 1");
                     if ($stmt123->execute()) {
                         $live_match1 = $stmt123->fetch(PDO::FETCH_ASSOC);
                     }
                        
                        
                        ?>
                         <div class="text-center fs-5"><?php echo $live_match1['status']; ?></div>
                        <br>
                        <div class="text-center fs-5">Start Time: <?php echo $live_match1['formatted_start_time']; ?></div>
                        <br>
                        <br>
                        
                        <div class="row justify-content-around">
                            <img src="../image/<?php echo $live_match1['teamlogo1']; ?>" alt="" class="col-5 img-fluid">
                            <img src="../image/<?php echo $live_match1['teamlogo2']; ?>" alt="" class="col-5 img-fluid">
                        </div>
                        <div class="row justify-content-around text-center">
                            <div class="col-5 fw-bold "><?php echo $live_match1['team1_name']; ?></div>
                            <div class="col-5 fw-bold"><?php echo $live_match1['team2_name']; ?></div>
                        </div>
                        <br>
                        <div class="row justify-content-around">
                            <a href="#" class="col-2 my-auto text-center bg-warning-subtle rounded border border-black link-underline link-underline-opacity-0 link-dark link-opacity-50-hover " onclick=" <?php echo isset($_SESSION['user_login']) || isset($_SESSION['admin_login']) ? "placeMatchBet('{$_SESSION['user_login']}', '{$live_match1['team1_id']}', '{$live_match1['match_id']}', '{$live_match1['team1_name']}', '{$live_match1['team2_name']}', '{$live_match1['odd_team1']}', '1','{$live_match1['game_type']}')" : "openModal('loginModal')" ?> ">1 <?php echo $live_match1['odd_team1']; ?></a>
                            <a href="#" class="col-2 my-auto text-center bg-warning-subtle rounded border border-black link-underline link-underline-opacity-0 link-dark link-opacity-50-hover" onclick=" <?php echo isset($_SESSION['user_login']) || isset($_SESSION['admin_login']) ? "placeMatchBet('{$_SESSION['user_login']}', '0', '{$live_match1['match_id']}', '{$$live_match1['team1_name']}', '{$live_match1['team2_name']}', '{$live_match1['odd_draw']}', 'X','{$live_match1['game_type']}')" : "openModal('loginModal')" ?> ">Draw <?php echo $live_match1['odd_draw']; ?></a>
                            <a href="#" class="col-2 my-auto text-center bg-warning-subtle rounded border border-black link-underline link-underline-opacity-0 link-dark link-opacity-50-hover" onclick=" <?php echo isset($_SESSION['user_login']) || isset($_SESSION['admin_login']) ? "placeMatchBet('{$_SESSION['user_login']}', '{$live_match1['team2_id']}', '{$$live_match1['match_id']}', '{$live_match1['team1_name']}', '{$live_match1['team2_name']}', '{$live_match1['odd_team2']}', '2','{$live_match1['game_type']}')" : "openModal('loginModal')" ?> ">2 <?php echo $live_match1['odd_team2']; ?></a>

                        </div>
                        <br>
                        <br>
                        
                    </div>
                </div>
            </div>
        </div>
    
   
    <div class="col-4 bg-warning rounded pb-5">
            <div class="livegames">
                
                    <div class="fw-bold fs-3 py-3"> Live Bets  </div>
                    <div class="row bg-warning fs-4">
                    <a href="homepage.php?game=Football" class="col  link-underline link-underline-opacity-0 link-dark link-opacity-50-hover border border-black border-start-0 fw-bold">Football  </a>
                    <a href="homepage.php?game=Basketball" class="col  link-underline link-underline-opacity-0 link-dark link-opacity-50-hover border border-black border-start-0 fw-bold"> Basketball </a>
                    <a href="homepage.php?game=Valorant" class="col  link-underline link-underline-opacity-0 link-dark link-opacity-50-hover border border-black border-start-0 border-end-0 fw-bold"> Valorant </a>
                    </div>

                <?php  
try {
    $gamename=$_GET['game'];
    if(!isset($_GET['game'])){
        $gamename="Football";
    }
    $stmt = $conn->prepare("SELECT 
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
        m.status AS status
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
        m.start_time ASC
        LIMIT 5");
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
} 
?>
   
<div class="">
    <?php foreach ($live_matches as $livematch) { ?>
        <div class="row bg-light py-2 justify-content-around ">
            <div class="col-6">Start Time: <?php echo $livematch['formatted_start_time']; ?></div>
            <div class="col-1 text-center">1</div>
            <div class="col-1 text-center">X</div>
            <div class="col-1 text-center">2</div>
        </div>
        <div class="row bg-warning justify-content-around py-2">
            <div class="col-6">
                <div class="row">
                    <div class="col-10 fw-bold">  <img src="../image/<?php echo $livematch['teamlogo1'];?>" alt="Description of image" style="width: 3rem; height: 3rem;">  <?php echo $livematch['team1_name']; ?></div>
                    <div class="col-2">0</div>
                </div>
                <div class="row">
                    <div class="col-10 fw-bold"> <img src="../image/<?php echo $livematch['teamlogo2'];?>" alt="Description of image" style="width: 3rem; height: 3rem;">  <?php echo $livematch['team2_name']; ?></div>
                    <div class="col-2">0</div>
                </div>
            </div>


        
            <a href="#" class="col-1 my-auto text-center bg-warning-subtle rounded border border-black link-underline link-underline-opacity-0 link-dark link-opacity-50-hover " onclick=" <?php echo isset($_SESSION['user_login']) || isset($_SESSION['admin_login']) ? "placeMatchBet('{$_SESSION['user_login']}', '{$livematch['team1_id']}', '{$livematch['match_id']}', '{$livematch['team1_name']}', '{$livematch['team2_name']}', '{$livematch['odd_team1']}', '1','{$livematch['game_type']}')" : "openModal('loginModal')" ?> "><?php echo $livematch['odd_team1']; ?></a>

            <a href="#" class="col-1 my-auto text-center bg-warning-subtle rounded border border-black link-underline link-underline-opacity-0 link-dark link-opacity-50-hover" onclick=" <?php echo isset($_SESSION['user_login']) || isset($_SESSION['admin_login']) ? "placeMatchBet('{$_SESSION['user_login']}', '0', '{$livematch['match_id']}', '{$livematch['team1_name']}', '{$livematch['team2_name']}', '{$livematch['odd_draw']}', 'X','{$livematch['game_type']}')" : "openModal('loginModal')" ?> "><?php echo $livematch['odd_draw']; ?></a>

            <a href="#" class="col-1 my-auto text-center bg-warning-subtle rounded border border-black link-underline link-underline-opacity-0 link-dark link-opacity-50-hover" onclick=" <?php echo isset($_SESSION['user_login']) || isset($_SESSION['admin_login']) ? "placeMatchBet('{$_SESSION['user_login']}', '{$livematch['team2_id']}', '{$livematch['match_id']}', '{$livematch['team1_name']}', '{$livematch['team2_name']}', '{$livematch['odd_team2']}', '2','{$livematch['game_type']}')" : "openModal('loginModal')" ?> "><?php echo $livematch['odd_team2']; ?></a>
        
        </div>
    




        
        
    <?php } ?>
</div>
<br>
<br>
</div>
           
            



<form id="betForm" action="../bet/bet.php" method="POST" style="display:none;">
            <input type="hidden" name="user_id" id="user_id">
            <input type="hidden" name="teamidbeton" id="teamidbeton">
            <input type="hidden" name="matchId" id="matchId">
            <input type="hidden" name="team1" id="team1">
            <input type="hidden" name="team2" id="team2">
            <input type="hidden" name="odd" id="odd">
            <input type="hidden" name="betType" id="betType">
            <input type="hidden" name="gameType" id="gameType">
        </form>
    </div>

        
    </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
        function placeMatchBet(user_id, teamidbeton, matchId, team1, team2, odd, betType,gameType) {
            
            // Populate the hidden form fields with the data
            document.getElementById('user_id').value = user_id;
            document.getElementById('teamidbeton').value = teamidbeton;
            document.getElementById('matchId').value = matchId;
            document.getElementById('team1').value = team1;
            document.getElementById('team2').value = team2;
            document.getElementById('odd').value = odd;
            document.getElementById('betType').value = betType;
            document.getElementById('gameType').value = gameType;
            
            // Automatically submit the form
            document.getElementById('betForm').submit();
            
            
        }




</script>
</body>
</html>