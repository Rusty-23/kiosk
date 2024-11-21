<?php
session_start();

// Initialize the coin credit session variable if it doesn't exist
if (!isset($_SESSION['coin_credit'])) {
    $_SESSION['coin_credit'] = 0;
}

// Function to add coins to the credit
function addCoinCredit($amount) {
    $_SESSION['coin_credit'] += $amount;
}

// Function to deduct coins from the credit (if necessary)
function deductCoinCredit($amount) {
    if ($_SESSION['coin_credit'] >= $amount) {
        $_SESSION['coin_credit'] -= $amount;
        return true;
    }
    return false;
}

// Return the current credit
function getCoinCredit() {
    return $_SESSION['coin_credit'];
}
?>
