<?php

$serverName = "localhost";
$userName = "root";
$password = "";
$dbConnection;

// Create connection
$dbConnection = new mysqli($serverName, $userName, $password);

// Check connection
if ($dbConnection->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully \n";


getInputCommand($dbConnection);
/* * ***************************
 * Function to handle commands from command line
 * *********************** */

function getInputCommand($dbConnection) {
    //Get input 
    $getCommand = readline("Enter the command (use --help to see all command line options) : ");

    //exploding command to get all parramters
    $splitCommand = explode(" ", $getCommand);

    //match with entered command
    if (strcmp(trim($getCommand), "--help") == 0) {
        getHelp();
    } elseif (strcmp(trim($splitCommand[0]), "--file") == 0) {
        //check if file name is provided or not
        if (isset($splitCommand[1])) {
            $fileName = trim($splitCommand[1]);
            //check input file is exists or not
            if (file_exists(realpath(trim($fileName) == 1))) {
                executeFile($fileName, $dbConnection);
            }
        } else {
            echo "try --help for more information";
            exit();
        }
    }
}

/* * ***************************
 * Function to execute file
 * store all csv file data to database
 * @param - file Name
 *          database connection
 */

function executeFile($fileName = 'users.csv', $dbConnection) {

    //create table
    createTable($dbConnection);

    //open csv file
    $file = fopen($fileName, "r");
    $i = 1;
    while (!feof($file)) {
        $fileArray = fgetcsv($file);
        $name = ucfirst($fileArray[0]);
        $surname = ucfirst($fileArray[1]);
        //validate email address
        if (validateEmailAddress($fileArray[2]) === True) {
            $email = trim(strtolower($fileArray[2]));
            $insertQuery = 'INSERT INTO userDetails (name, surname, email)
            VALUES ("'.$name.'", "'.$surname.'", "'.$email.'")';
            if ($dbConnection->query($insertQuery) === TRUE) {
                $count = $i++;
            } else {
                //echo "\n Error: " . $insertQuery . "<br>" . $dbConnection->error;
                echo "\n Error: " . $dbConnection->error. "\n";
            }
        }
    }
    echo "\n".$i . "Users inserted successfully";
}

/* * ****************************
 * Function to create tabel using database connections
 */

function createTable($dbConnection) {
    //create database if its not exists
    $sql = "CREATE DATABASE IF NOT EXISTS userInformation";
    if (mysqli_query($dbConnection, $sql)) {
        echo "Database created successfully \n";
    } else {
        echo "Error creating database: " . $dbConnection->error;
    }

    //select database 
    mysqli_select_db($dbConnection, 'userInformation');
    //create user table in database
    $createTableQuery = "CREATE TABLE IF NOT EXISTS userDetails (
                         name VARCHAR(30) NOT NULL,
                         surname VARCHAR(30) NOT NULL,
                         email VARCHAR(50) NOT NULL,
                         UNIQUE (email)
                        )";
    if ($dbConnection->query($createTableQuery) === TRUE) {
        echo "Table userTable created successfully \n";
    } else {
        echo "Error creating table: " . $dbConnection->error;
    }
}

/* * ***********************
 * Function to validate email address
 * @param - email
 */

function validateEmailAddress($email = '') {
    // Remove all illegal characters from email
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    // Validate e-mail
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return true;
    } else {
        echo("$email is not a valid email address");
        return false;
    }
}

/* * ***************************
 * Function to get all command line options
 * *********************** */

function getHelp() {
    echo "--file [csv file name] � this is the name of the CSV to be parsed\n";
    echo "--create_table � this will cause the MySQL users table to be built (and no further action will be taken)\n";
    echo "--dry_run � this will be used with the --file directive in the instance that we want to run the script but not insert into the DB. All other functions will be executed, but the database won't be altered.\n";
    echo "-u � MySQL username\n";
    echo "-p � MySQL password\n";
    echo "-h � MySQL host\n";
    echo "--help � which will output the above list of directives with details.";
    getInputCommand();
}
