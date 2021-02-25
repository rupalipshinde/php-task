<?php

//Get input 
$serverName = readline("Enter Server Name : ");
$userName = readline("Enter MySQL Username : ");
$password = readline("Enter MySQL Password : ");

//$serverName = "localhost";
//$userName = "root";
//$password = "";
$dbConnection;

// Create connection
$dbConnection = new mysqli($serverName, $userName, $password);

// Check connection
if ($dbConnection->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully \n";


getInputCommand($dbConnection, $serverName, $userName, $password);
/* * ***************************
 * Function to handle commands from command line
 * *********************** */

function getInputCommand($dbConnection, $serverName = "localhost", $userName = "root", $password = "") {
    //Get input 
    $getCommand = readline("Enter the command (use --help to see all command line options) : ");

    //exploding command to get all parramters
    $splitCommand = explode(" ", $getCommand);

    //match with entered command
    if (strcmp(trim($getCommand), "--help") == 0) {
        getHelp();
    } elseif (strcmp(trim($splitCommand[0]), "--file") == 0) {
        $dryRun = false;
        //check if file name is provided or not
        if (isset($splitCommand[1])) {
            $fileName = trim($splitCommand[1]);
            //check input file is exists or not
            if ((strcmp($fileName, "users.csv") == 0) && file_exists(realpath(trim($fileName))) == 1) {
                if (isset($splitCommand[2])) {
                    if (trim($splitCommand[2]) == '--dry_run') {
                        $dryRun = true;
                    }
                }
                //if file exists save  data to database
                executeFile($fileName, $dbConnection, $dryRun, $serverName, $userName, $password);
            } else
            if (strcmp($fileName, "users.csv") && file_exists(realpath("users.csv") == false)) {
                echo "users.csv does not exist.\n";
                exit();
            } else {
                echo "Invalid filename entered. Please try again\n";
                exit();
            }
        } else {
            echo "try --help for more information";
            exit();
        }
    } elseif (strcmp(trim($splitCommand[0]), "--create_table") == 0) {
        createTable();
    } elseif (strcmp(trim($splitCommand[0]), "-u") == 0) {
        echo "MySQL Username " . $userName . "\n";
        getInputCommand($dbConnection, $serverName, $userName, $password);
    } elseif (strcmp(trim($splitCommand[0]), "-p") == 0) {
        echo "MySQL Password " . $password . "\n";
        getInputCommand($dbConnection, $serverName, $userName, $password);
    } elseif (strcmp(trim($splitCommand[0]), "-h") == 0) {
        echo "MySQL Host " . $serverName . "\n";
        getInputCommand($dbConnection, $serverName, $userName, $password);
    } else {
        echo "Invalid command.Please enter a valid command.\n";
        getInputCommand($dbConnection, $serverName, $userName, $password);
    }
}

/* * ***************************
 * Function to execute file
 * store all csv file data to database
 * @param - file Name
 *          database connection
 *          Dry run - execute file with create table or not
 */

function executeFile($fileName = 'users.csv', $dbConnection, $dryRun = false, $serverName, $userName, $password) {

    //create table
    createTable($dbConnection);
    //open csv file
    $file = fopen($fileName, "r");
    $i = 1;
    while (!feof($file)) {
        $fileArray = fgetcsv($file);
        $name = ucfirst($fileArray[0]);
        $surname = ucfirst($fileArray[1]);
        if ($dryRun == false) {
            //validate email address
            if (validateEmailAddress($fileArray[2]) === True) {
                $email = trim(strtolower($fileArray[2]));
                $insertQuery = 'INSERT INTO userDetails (name, surname, email)
            VALUES ("' . $name . '", "' . $surname . '", "' . $email . '")';
                if ($dbConnection->query($insertQuery) === TRUE) {
                    $count = $i++;
                } else {
                    //echo "\n Error: " . $insertQuery . "<br>" . $dbConnection->error;
                    echo "\n Error: " . $dbConnection->error . "\n";
                }
            }
        } else {
            getInputCommand($dbConnection, $serverName, $userName, $password);
        }
    }
    echo "\n" . $i . "Users inserted successfully";
}

/* * ****************************
 * Function to create tabel using database connections
 * @param - database connection
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
        echo $email ."is not a valid email address";
        return false;
    }
}

/* * ***************************
 * Function to get all command line options
 * *********************** */

function getHelp() {
    echo "--file [csv file name] – this is the name of the CSV to be parsed\n";
    echo "--create_table – this will cause the MySQL users table to be built (and no further action will be taken)\n";
    echo "--dry_run – this will be used with the --file directive in the instance that we want to run the script but not insert into the DB. All other functions will be executed, but the database won't be altered.\n";
    echo "-u – MySQL username\n";
    echo "-p – MySQL password\n";
    echo "-h – MySQL host\n";
    echo "--help – which will output the above list of directives with details.";
//    getInputCommand();
}
