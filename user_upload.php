<?php

getInputCommand();

/* * ***************************
 * Function to database connection
 * @param : server name
 *          user name
 *          password
 * *********************** */

function databaseConnection($serverName = "localhost", $userName = "root", $password = "") {
// Create connection
    $conn = new mysqli($serverName, $userName, $password);

// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    echo "Connected successfully \n";
}

/* * ***************************
 * Function to handle commands from command line
 * *********************** */

function getInputCommand() {
    databaseConnection();

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
                executeFile($fileName);
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
 */

function executeFile($fileName = 'users.csv') {

    //create table
    createTable();
}

/* * ****************************
 * Function to create tabel using database connections
 */

function createTable() {
    $dbConnection = databaseConnection();

    //create database if its not exists
    $sql = "CREATE DATABASE IF NOT EXISTS userDetails";
    if ($dbConnection->query($sql) === TRUE) {
        echo "Database created successfully";
    } else {
        echo "Error creating database: " . $dbConnection->error;
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
    getInputCommand();
}
