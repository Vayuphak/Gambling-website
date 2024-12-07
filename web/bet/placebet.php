<?php
header('Content-Type: application/json');

// Get the JSON input
$data_match = json_decode(file_get_contents('php://input'), true);

$betAmount = $data_match['betAmount'] ?? 0;

$userId = $data_match['matchBets'][0]['user_id'] ?? 4;
$getbet = $data_match['matchBets'];
$response = ['success' => true, 'message' => 'Bet placed successfully' . $userId];

// Validate input
if ($betAmount <= 0 || $userId <= 0) {
    $response['success'] = false;
    $response['message'] = 'Invalid bet data=' . $userId;
} else {
    try {
        // Database connection (use your actual credentials here)
        $pdo = new PDO('mysql:host=localhost;dbname=css888', 'root', 'root');
        
        // Insert into bet table
        $stmt = $pdo->prepare("INSERT INTO bet (user_id, bet_amount) 
                               VALUES (:user_id, :bet_amount )");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':bet_amount', $betAmount);
      





        if (!$stmt->execute()) {
            $response['success'] = false;
            $response['message'] = 'Failed to place the bet.';
        } else {
            $betId = $pdo->lastInsertId();

            // Prepare the statement for match_bet table insertion
            $stmt2 = $pdo->prepare("INSERT INTO match_bet (bet_id, teamid_bet_on, odd, match_id) 
                                    VALUES (:bet_id, :teamid_bet_on, :odd, :match_id)");
            $stmt3 = $pdo->prepare("INSERT INTO matches SET  team1_pool = :team1pool, team2_pool= :team2pool,draw_pool =:drawpool");
    

            // Loop through each entry in matchBets and execute insertion
            foreach ($getbet as $bet_info) {
                $teamidBetOn = (isset($bet_info['teamidbeton']) && $bet_info['teamidbeton'] == 0) ? null : $bet_info['teamidbeton'];
                $odd = isset($bet_info['odd']) ? (float)$bet_info['odd'] : null;
                $matchId = $bet_info['id'] ?? null;

                // Check for required fields before insertion
                if ($odd === null || $matchId === null || $betId === null) {
                    $response['success'] = false;
                    $response['message'] = 'Missing required data for match bet entry';
                    break;
                }

                // Execute insertion into match_bet with an array of values to avoid bindParam issues
                if (!$stmt2->execute([
                    ':bet_id' => $betId,
                    ':teamid_bet_on' => $teamidBetOn,
                    ':odd' => $odd,
                    ':match_id' => $matchId
                ])) {
                    $response['success'] = false;
                    $response['message'] = 'Failed to place match bet for match ID: ' . $matchId;
                    break;
                }
            }

            if ($response['success']) {
                $response['betId'] = $betId;
                $response['message'] = 'Bet placed successfully with ID: ' . $betId;
            }
        }
    } catch (PDOException $e) {
        $response['success'] = false;
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
}

echo json_encode($response);

?>
