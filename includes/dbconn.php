 <?php
$servername = "localhost";
$username = "root";
$password = "";

//Create db
$sql = "";
try {
    $pdo = new PDO("mysql:host=$servername", $username, $password);
    // set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "CREATE DATABASE IF NOT EXISTS ptpls";
    // use exec() because no results are returned
    $pdo->query($sql);
    //echo "Database created successfully<br>";
    }
catch(PDOException $e)
    {
    echo $sql . "<br>" . $e->getMessage();
    }

//Create tables
$sql2 = "";
try {
    $conn = new PDO("mysql:host=$servername;dbname=ptpls", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     // sql to create table
    $t1 = "CREATE TABLE IF NOT EXISTS users (
    uid INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50) NOT NULL, /*Sponsor or borrower */
    name VARCHAR(50) NOT NULL,
    ID VARCHAR(10) NOT NULL,
	phone VARCHAR(15) NOT NULL, /*Also to act as username*/
	email VARCHAR(20) NOT NULL,
	address VARCHAR(15) NOT NULL,
	zip VARCHAR(15) NOT NULL,
    balance INT(15) NOT NULL, 
	password VARCHAR(255) NOT NULL,
    reg_date TIMESTAMP
    )";

    
   $t2 = "CREATE TABLE IF NOT EXISTS guarantors (
    gid INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uid INT(15) NOT NULL, /*The borrower whose guarantor this is...*/
    name VARCHAR(50) NOT NULL,
    ID VARCHAR(10) NOT NULL,
	phone VARCHAR(15) NOT NULL,
    occupation VARCHAR(50) NOT NULL,
	address VARCHAR(15) NOT NULL,
	approval INT(6) NOT NULL,
    reg_date TIMESTAMP
    )";

    $t3 = "CREATE TABLE IF NOT EXISTS deposit ( /*This table recieves MPesa payment messages*/
    did INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uid INT(15) NOT NULL, /*The user who made the deposit ...*/
    phone VARCHAR(50) NOT NULL, /*Phone number that sent the payment*/
    name VARCHAR(100) NOT NULL, /*obtained from the mpesa transction message*/
    date_time VARCHAR(50) NOT NULL,
    transact_id VARCHAR(50) NOT NULL,
	amount INT(15) NOT NULL,
    reg_date TIMESTAMP
    )";

    $t4 = "CREATE TABLE IF NOT EXISTS loan_terms ( 
    tid INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    range_from INT(15) NOT NULL, 
    range_to INT(15) NOT NULL, 
    period VARCHAR(50) NOT NULL, /*To repay within xx days*/
    rate INT(6) NOT NULL, /*Rate of interest in %, per week*/
    reg_date TIMESTAMP
    )";

    $t5 = "CREATE TABLE IF NOT EXISTS loans ( /*This table holds all loans given out*/
    lid INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uid INT(15) NOT NULL, /*The borrower who took the loan ...*/
    tid INT(15) NOT NULL, /*The matching term ...*/
    did INT(15) NOT NULL, /*For the deposit made ...*/
	amount INT(15) NOT NULL,
    dateout VARCHAR(50) NOT NULL, 
    dateexpected VARCHAR(50) NOT NULL, 
    datein VARCHAR(50) NOT NULL, 
    balance INT(15) NOT NULL,  /*Loanee may pay in installments ...*/
    comment VARCHAR(50) NOT NULL, /*Comments about the paying*/
    status VARCHAR(50) NOT NULL, /*Will be using the latest record to see if borrower is eligible to borrow Eligible or Not Eligible*/
    reg_date TIMESTAMP
    )";
	
	

    // use exec() because no results are returned
    $conn->query($t1);
	$conn->query($t2);
	//$conn->query($t3); //use pesapi_payment
    $conn->query($t4);
	$conn->query($t5);
    //echo "Tables created successfully<br>";
    }
catch(PDOException $e)
    {
    echo $sql . "<br>" . $e->getMessage();
    }
	
	
$conn->query("use ptpls");

date_default_timezone_set("Africa/Nairobi"); //Defaault timezone
?> 