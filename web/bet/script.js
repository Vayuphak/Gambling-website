
 const matchBets = [];


// Add a match bet to the list and update odds
// Add a match bet to the list and update odds
function addMatchBet(user_id, teamidbeton, matchId, team1, team2, odd, betType) {
    // Check if a bet on this match already exists
    if (matchBets.some(match => match.id === matchId)) {
        alert("You have already placed a bet on this match!");
        return;
    }

    // Add match info to the list
    const matchBet = { user_id, teamidbeton, id: matchId, team1, team2, odd: parseFloat(odd), betType };
    matchBets.push(matchBet);

    // Save updated matchBets to sessionStorage
    sessionStorage.setItem('matchBets', JSON.stringify(matchBets));

    updateMatchBetList();
    updateOverallOdds();  // Update overall odds right after adding
}



// Remove a match bet from the list and update odds
function removeMatchBet(matchId, betType) {
    const index = matchBets.findIndex(match => match.id === matchId && match.betType === betType);
    if (index !== -1) {
        matchBets.splice(index, 1); // Remove from array
        updateMatchBetList();
        updateOverallOdds();  // Update overall odds right after removing
    }
}

// Update match bet list display in the DOM
function updateMatchBetList() {
    const matchBetContainer = document.querySelector('.matchbet-container');
    matchBetContainer.innerHTML = ''; // Clear existing list
    matchBets.forEach(match => {
        // Create and insert new match bet div
        const matchDiv = document.createElement('div');
        matchDiv.className = 'matchbet';
        matchDiv.innerHTML = `
            <h1><button onclick="removeMatchBet('${match.id}', '${match.betType}')">X</button></h1>
            <h2>${match.team1} vs ${match.team2}</h2>
            <h2>Bet: ${match.betType}, Odd: ${match.odd}</h2>
        `;
        matchBetContainer.appendChild(matchDiv);
    });
    updatePotentialEarnings(); // Update potential earnings based on updated odds
}

// Calculate and update overall odds based on selected matches
function updateOverallOdds() {
    let overallOdds = matchBets.length ? matchBets.reduce((acc, match) => acc * match.odd, 1) : 1;
    document.getElementById('overall-odds').innerText = overallOdds.toFixed(2); // Update in DOM
    updatePotentialEarnings(); // Update potential earnings based on new odds
}

// Calculate potential earnings based on overall odds and bet amount
function updatePotentialEarnings() {
    const overallOdds = parseFloat(document.getElementById('overall-odds').innerText);
    const betAmount = parseFloat(document.getElementById('bet-amount').value) || 0;
    const potentialEarnings = betAmount * overallOdds; // Calculate earnings
    document.getElementById('potential-earnings').innerText = potentialEarnings.toFixed(2); // Update in DOM
}

// Place the bet by sending the data
function placeBet() {
    const betAmount = parseFloat(document.getElementById('bet-amount').value);
    
    if (betAmount <= 0 || matchBets.length === 0) {
        alert("Please add a bet and enter a valid amount.");
        return;
    }

    // Check if bet amount is within the user's balance
    if (betAmount > userBalance) {
        alert("Insufficient balance.");
    } else {
        // Send bet amount to check_balance.php
        checkAndUpdateBalance(betAmount);
        sendBetData(betAmount);
    }
}

// Function to send bet data to check_balance.php and handle response
function checkAndUpdateBalance(betAmount) {
    fetch('check_balance.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ betAmount })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message); // e.g., "Bet placed and balance updated successfully."
            userBalance -= betAmount; // Update the balance in JS for real-time UI update
        } else {
            alert(data.message); // Show any error messages
        }
    })
    .catch(error => console.error('Error:', error));
}

// Send the bet data via POST request
function sendBetData(betAmount) {
    console.log("Sending bet data:", { matchBets, betAmount });
    console.log("Prepared JSON:", JSON.stringify({ matchBets, betAmount }));
    fetch('placebet.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ matchBets, betAmount })
    })
    .then(response => response.json().catch(() => {
        console.error('Invalid JSON response');
        return { success: false, message: 'Server returned invalid JSON' };
    }))
    .then(data => {
        console.log("Response data:", data);

        if (data.success) {
            alert("Bet placed successfully! ");
            matchBets.length = 0; // Empty the matchBets array
            updateMatchBetList();  // Update the UI to reflect the empty matchBets array
            window.location.reload(); // Refresh the page
        } else {
            alert("Failed to place bet. ID: " + (matchBets.length > 0 ? matchBets[0]['id'] : 'N/A'));
        }
    })
    .catch(error => console.error('Error:', error));
}

// Event listener for bet amount change
document.getElementById('bet-amount').addEventListener('input', updatePotentialEarnings);


