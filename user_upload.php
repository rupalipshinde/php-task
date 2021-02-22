<?php

//MySql Connection Details
$serverName = "localhost";
$userName = "root";
$password = "root";

getInputCommand();

/* * ***************************
 * Function to database connection
 * *********************** */

function databaseConnection() {
// Create connection
    $conn = new mysqli($servername, $username, $password);

// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    echo "Connected successfully";
}

/* * ***************************
 * Function to handle commands from command line
 * *********************** */

function getInputCommand() {
    $dbConnection = databaseConnection();
}
