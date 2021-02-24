# php-task

## Script Task
PHP script, that is executed from the command line, which accepts a CSV file as input
(see command line directives below) and processes the CSV file.

### Directives are - 
* --file [csv file name] – this is the name of the CSV to be parsed
* --create_table – this will cause the MySQL users table to be built (and no further
 action will be taken)
8 --dry_run – this will be used with the --file directive in case we want to run the
script but not insert into the DB. All other functions will be executed, but the
database won't be altered
* -u – MySQL username
* -p – MySQL password
* -h – MySQL host
* --help – which will output the above list of directives with details.

### Usage Examples
Open CSV file
```
$file = fopen($fileName, "r");
```

Create Table
```
CREATE TABLE IF NOT EXISTS tableName (
                         name VARCHAR(30) NOT NULL,
                         surname VARCHAR(30) NOT NULL,
                         email VARCHAR(50) NOT NULL,
                         UNIQUE (email)
                        )
```
validate email address
```
// Remove all illegal characters from email
$email = filter_var($email, FILTER_SANITIZE_EMAIL);

// Validate e-mail
if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
    return true;
} else {
    echo("$email is not a valid email address");
    return false;
}
```

Store data in database
```
  'INSERT INTO tableName (name, surname, email)
            VALUES ("' . $name . '", "' . $surname . '", "' . $email . '")';
```