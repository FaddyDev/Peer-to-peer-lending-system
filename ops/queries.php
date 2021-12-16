<?php if(session_status()==PHP_SESSION_NONE){
session_start();}
  include("../includes/dbconn.php"); //DB ?>
<?php //SMS
function sendsms($msg, $tophone){
    // Be sure to include the file you've just downloaded
require_once('../includes/AfricasTalkingGateway.php');
// Specify your login credentials
//$username   = "SUZ";
//$apikey     = "fa69ada79cd7bed97198446f7be6e7d37ba588eeade470fbf7454107a92588bd";
$username   = "eesbs";
$apikey     = "9d0d0fe4fdf59c742eb3b11e67782281f42d4de41787f9db20cf56af327e0c6b";
// Specify the numbers that you want to send to in a comma-separated list
// Please ensure you include the country code (+254 for Kenya in this case)
$recipients = "+254".$tophone;
// And of course we want our recipients to know what we really do
$message    = $msg;
// Create a new instance of our awesome gateway class
$gateway    = new AfricasTalkingGateway($username, $apikey);
// Any gateway error will be captured by our custom Exception class below, 
// so wrap the call in a try-catch block
$success = true;
try 
{ 
  // Thats it, hit send and we'll take care of the rest. 
  $results = $gateway->sendMessage($recipients, $message);
            
  foreach($results as $result) {
    // status is either "Success" or "error message"
   // echo " Number: " .$result->number;
    //echo " Status: " .$result->status;
    //echo " MessageId: " .$result->messageId;
    //echo " Cost: "   .$result->cost."\n";
    $success = true;
  }
}
catch ( AfricasTalkingGatewayException $e )
{
  echo "Encountered an error while sending: ".$e->getMessage();
  $success = false;
}
return $success;
}
?>

<?php
//Insert deposit  
if(isset($_POST['deposit'])){

$uid = $_POST["uid"];
 $err = "None";
 $amount = 0;

 $sql = "SELECT * FROM pesapi_payment TRY WHERE receipt = ?";     
    $stmt = $conn->prepare($sql);
	if($stmt->execute(array($_POST['tid'])))
	{
    if($stmt->rowCount() == 1){
      $tidexists = 0;
      while($row = $stmt -> fetch()){  $amount = $row['amount']; }
    }
    if($stmt->rowCount() > 1){
      $err = "There exists more than one deposit with that transaction ID, please contact the admin";
    }
    if($stmt->rowCount() == 0){
      $err = "There is no deposit with that transaction ID, kindly confirm then re-enter it";
    }
}

if($err === "None"){ //if there's no eror, meaning tid exists
$sql = "SELECT * FROM pesapi_payment TRY WHERE receipt = ? AND uid=?";     
    $stmt = $conn->prepare($sql);
	if($stmt->execute(array($_POST['tid'],$_POST['uid'])))
	{
    if($stmt->rowCount() > 0){
      $err = "The deposit with that transaction ID has already been used!";
    }
}
}

if($err !== "None"){
        $_SESSION['fail'] = $err;
        //echo "<meta http-equiv='refresh' content='0;url=../sponsor.php'> ";
    if($_SESSION['usertype'] == "Sponsor"){
        //header("Location: ../sponsor.php");
        echo "<meta http-equiv='refresh' content='0;url=../sponsor.php'> "; } else{
       //header("Location: ../borrower.php");
       echo "<meta http-equiv='refresh' content='0;url=../borrower.php'> ";}
}
else{
try { 
    $prevbal = 0; $newbal = 0;
    $prevbal = $_POST['prevbalance'];      
    $newbal = $prevbal + $amount;

    $sql = "UPDATE users SET balance=? WHERE uid=?";
    $stmt = $conn->prepare($sql);
  	$stmt -> bindParam(1, $newbal);
    $stmt -> bindParam(2, $uid);
    $stmt->execute();

    $sql = "UPDATE pesapi_payment SET uid=? WHERE receipt=?";
    $stmt = $conn->prepare($sql);
    $stmt -> bindParam(1, $uid);
  	$stmt -> bindParam(2, $_POST['tid']);
    $stmt->execute();

//Send sponsor text to confirm receipt
$msg = "PTPLS Notification! Deposit of KShs ".$amount." successfully made. New balance: KShs. ".$newbal;
$tophone = $_SESSION['phone'];
sendsms($msg, $tophone);

    $_SESSION['success'] = "Deposit of KShs ".$amount." successfully made";
        //echo "<meta http-equiv='refresh' content='0;url=../sponsor.php'> ";
    if($_SESSION['usertype'] == "Sponsor"){
        //header("Location: ../sponsor.php");
        echo "<meta http-equiv='refresh' content='0;url=../sponsor.php'> "; } else{
       //header("Location: ../borrower.php");
       echo "<meta http-equiv='refresh' content='0;url=../borrower.php'> ";}
    }
catch(PDOException $e)
    {
   echo "<div style='height:auto; width:50%; color:#000000; margin:auto;top:100px; background-color:#cc7a00; border-radius:5px;border-style: solid; border-width:thin;border-color: red;'>".$sql . "<br>" . $e->getMessage()." <br>Go back and retry.</div>";
     }
}

}

//add guarantor
if(isset($_POST['add'])){
$uid = $_POST["uid"];
try { 
    $sql = "INSERT INTO guarantors (name, uid, ID, phone, address, occupation)
    VALUES (?,?,?,?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt -> bindParam(1, $_POST['name']);
	$stmt -> bindParam(2, $uid);
	$stmt -> bindParam(3, $_POST['idno']);
	$stmt -> bindParam(4, $_POST['phone']);
    $stmt -> bindParam(5, $_POST['address']);
	$stmt -> bindParam(6, $_POST['occupation']);
    $stmt->execute();

//Send guarantor text to confirm...

$phone = $_SESSION['phone']; $name = "";
$sql = "SELECT * FROM users TRY WHERE phone = ?";
$stmt = $conn->prepare($sql);
if($stmt->execute(array($phone)))
{
 while($row = $stmt -> fetch())
 {
     $data[] = $row;
 }
}
 foreach($data as $dt){
   $name = $dt['name']; 
 }

$guarname = $_POST['name'];
$tophone = $_POST['phone'];
$msg = "PTPLS Notification! Hi ".$guarname.", You have been added as a guarantor by ".$name." of ".$phone." Please follow 
this link to approve or reject http://ptpls.000webhostapp.com/ptpls/guar.php?u=".$uid."&gfn=".$tophone;  

sendsms($msg, $tophone);

     $_SESSION['success'] = "Guarantor added successfully. Waiting for the guarantor's approval.";
    //echo "<meta http-equiv='refresh' content='0;url=../borrower.php'> ";
    if($_SESSION['usertype'] == "Sponsor"){
        //header("Location: ../sponsor.php");
        echo "<meta http-equiv='refresh' content='0;url=../sponsor.php'> "; } else{
       //header("Location: ../borrower.php");
       echo "<meta http-equiv='refresh' content='0;url=../borrower.php'> ";}
    }
catch(PDOException $e)
    {
   echo "<div style='height:auto; width:50%; color:#000000; margin:auto;top:100px; background-color:#cc7a00; border-radius:5px;border-style: solid; border-width:thin;border-color: red;'>".$sql . "<br>" . $e->getMessage()." <br>Go back and retry.</div>";
     }
}

//Confirm guarantor
if(isset($_POST['confguar'])){
    $uid = $_POST["uid"];
    $apr = $_POST["apr"];
    try { 
        $sql = "UPDATE guarantors SET approval = ? WHERE uid = ? AND phone = ?";
        $stmt = $conn->prepare($sql);
        $stmt -> bindParam(1, $apr);
        $stmt -> bindParam(2, $uid);
        $stmt -> bindParam(3, $_POST["guarphone"]);
        $stmt->execute();
    
    //Send guarantor text to confirm...
    
    $tophone = $_POST['phone'];  $name = $_POST['name']; 
    $msg = "";
    if($apr == 1){
        $msg = "PTPLS Notification! Hi ".$name.", the guarantor you added has aproved the request. You can now borrow loans.";
    }
    else{
        $msg = "PTPLS Notification! Hi ".$name.", the guarantor you added has declined the request. Add a different one.";
    }
    sendsms($msg, $tophone);
    
         $_SESSION['success'] = "Thank you for your response.";
           echo "<meta http-equiv='refresh' content='0;url=../guar.php'> ";
        }
    catch(PDOException $e)
        {
       echo "<div style='height:auto; width:50%; color:#000000; margin:auto;top:100px; background-color:#cc7a00; border-radius:5px;border-style: solid; border-width:thin;border-color: red;'>".$sql . "<br>" . $e->getMessage()." <br>Go back and retry.</div>";
         }
    }

//request loan
if(isset($_POST['request'])){
$uid = $_POST["uid"]; $newby = $_POST["newby"]; $amount = $_POST["amount"]; $period = 0; $err = "None"; $rate = 0; $tid = 0; $total = 0;

try { 
    if($newby == 1){ //For nebies
    $to = 0;
      $sql = "SELECT * FROM loan_terms TRY WHERE tid = (SELECT MIN(tid) FROM loan_terms)";      
    $stmt = $conn->prepare($sql);
    if($stmt->execute())
	{
    if($stmt->rowCount() > 0){
      $tidexists = 0;
      while($row = $stmt -> fetch()){  
      $period = $row['period']; 
      $tid = $row['tid'];
      $rate = $row['rate'];
      $to = $row['range_to']; }
      if($to < $amount){
      $err = "Sorry, you are not eligible to borrow more than KShs. ". $to;
    }
    }else{
      $err = "Invalid amount";
    }
}
}

//Resident borrowers
   else {
       $sql = "SELECT * FROM loan_terms TRY WHERE range_from <=? AND range_to >= ?";
    $stmt = $conn->prepare($sql);
    $stmt -> bindParam(1, $amount);
	$stmt -> bindParam(2, $amount);
    if($stmt->execute())
	{
    if($stmt->rowCount() > 0){
      $tidexists = 0;
      while($row = $stmt -> fetch()){  
      $period = $row['period']; 
      $tid = $row['tid'];
      $rate = $row['rate']; }
    }else{
      $err = "Invalid amount";
    }
}
} 
    
	

if($err === "None"){ //Check if the system has enough money. Check from every other user except the borrower
  $sql = "SELECT SUM(balance) FROM users TRY WHERE uid != '".$uid."'"; 
    $stmt = $conn->prepare($sql);
	if($stmt->execute())
	{
    
      while($row = $stmt -> fetch())
      {$total = $row['SUM(balance)'];}

     if($total < $amount){
      $err = "Sorry, we do not have enough money to service your request, kindly try again later or take KShs. ". $total;
    }
}
}

if($err !== "None"){
        $_SESSION['loanfail'] = $err;
        //echo "<meta http-equiv='refresh' content='0;url=../borrower.php'> ";
    if($_SESSION['usertype'] == "Sponsor"){
        //header("Location: ../sponsor.php");
        echo "<meta http-equiv='refresh' content='0;url=../sponsor.php'> "; } else{
       //header("Location: ../borrower.php");
       echo "<meta http-equiv='refresh' content='0;url=../borrower.php'> ";}
}
else{
    $dateout = date('d/m/Y H:i');
      $dt = DateTime::createFromFormat('d/m/Y H:i', $dateout)->format('Y-m-d H:i');
    $dateexpt = (new DateTime($dt))->add(new DateInterval('P'.$period.'D'))->format('d/m/Y H:i'); 

    $balance = $amount + ($amount * (($rate/100) * ($period/7))); //X weeks
    $status = "Not Eligible";
    $comment = "Pending Loan";

    $sql = "INSERT INTO loans (tid, uid, amount, dateout, dateexpected, balance, comment, status)
    VALUES (?,?,?,?,?,?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt -> bindParam(1, $tid);
	$stmt -> bindParam(2, $uid);
	$stmt -> bindParam(3, $amount);
	$stmt -> bindParam(4, $dateout);
    $stmt -> bindParam(5, $dateexpt);
	$stmt -> bindParam(6, $balance);
    $stmt -> bindParam(7, $comment);
    $stmt -> bindParam(8, $status);
    $stmt->execute();

//Send text to client with amount, balance, dateout, date expected...
$msg = "PTPLS Notification! Your loan request of KShs. ".$amount." is successful. You'll receive the money via M-PESA shortly. 
Your loan balance is KShs. ".$balance." to be repaid on or before ".$dateexpt; //Please visit the siteto confirm
$tophone = $_SESSION['phone'];
sendsms($msg, $tophone);

//Send text to guarantor with amount, balance, dateout, date expected...
$client = $_SESSION['phone'];
$msg = "PTPLS Notification! Your guarantee of phone ".$client." has successfully requested for a loan of  KShs. ".$amount.". 
Their loan balance is KShs. ".$balance." to be repaid on or before ".$dateexpt; 
$tophone = $_POST['guarphone'];
sendsms($msg, $tophone);

//Send text to paybill with amount and numbe rof client
$msg = "PTPLS Notification! Send KShs. ".$amount." to ".$client." for a successful loan request."; //Please visit the siteto confirm
$tophone = "0726279601"; //The paybill person.
sendsms($msg, $tophone);

//update the balances by subtracting from [previously - sponsors equally] everyone except the borrower
    $sql = "SELECT * FROM users TRY WHERE uid != '".$uid."'";// AND balance != '0'"; //If you have no money you haven't contributed, i may just allow that to have negative values
    $stmt = $conn->prepare($sql);
	if($stmt->execute())
	{if($stmt->rowCount()>0)
	{ 
    $sponsors = $stmt->rowCount();
    $less = 0;
    $less = ($amount / $sponsors);
      $data = array(); $preval = 0; $theuid = 0;  $newbal = 0;
	 while($row = $stmt -> fetch())
	 {
         $data[] = $row;
     }
     foreach($data as $dt){
	   $preval = $dt['balance']; //the previous balance
       $theuid = $dt['uid']; //His/her uid
       $newbal = $preval - $less;
       //we update each record as they loop
    $sql = "UPDATE users SET balance=? WHERE uid=?";
    $stmt = $conn->prepare($sql);
	$stmt -> bindParam(1, $newbal);
	$stmt -> bindParam(2, $theuid);
    $stmt->execute();
    //$stmt = null;
	 }
    }
    }
    
    //Update account balance with loan amount awarded
    $sql = "UPDATE users SET balance=? WHERE uid=?";
    $balnew = $_POST['balprev'] + $amount;
    $stmt = $conn->prepare($sql);
  	$stmt -> bindParam(1, $balnew);
    $stmt -> bindParam(2, $uid);
    $stmt->execute();

     $_SESSION['loansuccess'] = "Loan request successful, You'll receive an SMS notification";
    //echo "<meta http-equiv='refresh' content='0;url=../borrower.php'> ";
    if($_SESSION['usertype'] == "Sponsor"){
        //header("Location: ../sponsor.php");
        echo "<meta http-equiv='refresh' content='0;url=../sponsor.php'> "; } else{
       //header("Location: ../borrower.php");
       echo "<meta http-equiv='refresh' content='0;url=../borrower.php'> ";}
    
      }//No error
    }
catch(PDOException $e)
    {
   echo "<div style='height:auto; width:50%; color:#000000; margin:auto;top:100px; background-color:#cc7a00; border-radius:5px;border-style: solid; border-width:thin;border-color: red;'>".$sql . "<br>" . $e->getMessage()." <br>Go back and retry.</div>";
     }
}

//Repay loan 
if(isset($_POST['reploan'])){

$lid = $_POST["lid"];
$uid = $_POST["uid"];
$loanamount = $_POST["loanamount"];
$dtout = $_POST["dtout"];
$dtexpt = $_POST["dtexpt"];
$termid = $_POST["termid"];
 $err = "None";
 $amount = 0; $did = 0;

 $sql = "SELECT * FROM pesapi_payment TRY WHERE receipt = ?";     
    $stmt = $conn->prepare($sql);
	if($stmt->execute(array($_POST['tid'])))
	{
    if($stmt->rowCount() == 1){
      $tidexists = 0;
      while($row = $stmt -> fetch()){  $amount = $row['amount']; $did = $row['id']; }
    }
    if($stmt->rowCount() > 1){
      $err = "There exists more than one deposit with that transaction ID, please contact the admin";
    }
    if($stmt->rowCount() == 0){
      $err = "There is no deposit with that transaction ID, kindly confirm then re-enter it";
    }
}

if($err === "None"){ //if there's no eror, meaning tid exists
$sql = "SELECT * FROM pesapi_payment TRY WHERE receipt = ? AND uid=?";     
    $stmt = $conn->prepare($sql);
	if($stmt->execute(array($_POST['tid'],$_POST['uid'])))
	{
    if($stmt->rowCount() > 0){
      $err = "The deposit with that transaction ID has already been used!";
    }
}
}

if($err !== "None"){
        $_SESSION['reploanfail'] = $err;
        //echo "<meta http-equiv='refresh' content='0;url=../borrower.php'> ";
    if($_SESSION['usertype'] == "Sponsor"){
        //header("Location: ../sponsor.php");
        echo "<meta http-equiv='refresh' content='0;url=../sponsor.php'> "; } else{
       //header("Location: ../borrower.php");
       echo "<meta http-equiv='refresh' content='0;url=../borrower.php'> ";}
}
else{
try { 
    $prevloanbal = 0; $newloanbal = 0; $prevacbal = 0; $newacbal = 0; $extra = 0; $prevcom = ""; $newcom = ""; $newstate = "";
    $prevloanbal = $_POST['prevloanbalance'];      
    $prevacbal = $_POST['prevacbalance']; 
    $prevcom = $_POST['prevcom']; 

    if($amount > $prevloanbal){ //if the borrower has paid extra, credit* their account balance
        $extra = $amount - $prevloanbal;
        $newloanbal = 0;
        $newacbal = $prevacbal + $extra;
    }else{
        $newloanbal = $prevloanbal - $amount;
        $newacbal = $prevacbal;
    }

    //If loan is cleared, check if there was any fine
    if($newloanbal == 0){ 
        if(strpos($prevcom, 'Fined') !== false){
            $newcom = "Defaultor";
            $newstate = "Not Eligible";
        } else{
            $newcom = "Paid promptly";
            $newstate = "Eligible";
        }
    }else{
        $newcom = $prevcom;
        $newstate = "Not Eligible";
    }

if($extra > 0){  //If there's an extra amount, add to ac bal
    $sql = "UPDATE users SET balance=? WHERE uid=?";
    $stmt = $conn->prepare($sql);
  	$stmt -> bindParam(1, $newacbal);
    $stmt -> bindParam(2, $uid);
    $stmt->execute();
}
//Update deposits table with uid to prevent re-using
    $sql = "UPDATE pesapi_payment SET uid=? WHERE receipt=?";
    $stmt = $conn->prepare($sql);
    $stmt -> bindParam(1, $uid);
  	$stmt -> bindParam(2, $_POST['tid']);
    $stmt->execute();

//Update loans  -Insert a new row
$datein = date('d/m/Y H:i');
//$sql = "UPDATE loans SET did=?, datein=?, balance=?, comment=?, status=? WHERE lid=?";

    $sql = "INSERT INTO loans (tid, uid, amount, dateout, dateexpected, balance, comment, status, datein, did)
    VALUES (?,?,?,?,?,?,?,?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt -> bindParam(1, $termid);
	$stmt -> bindParam(2, $uid);
	$stmt -> bindParam(3, $loanamount);
	$stmt -> bindParam(4, $dtout);
    $stmt -> bindParam(5, $dtexpt);
	$stmt -> bindParam(6, $newloanbal);
    $stmt -> bindParam(7, $newcom);
    $stmt -> bindParam(8, $newstate);
	$stmt -> bindParam(9, $datein);
    $stmt -> bindParam(10, $did);
    $stmt->execute();
    

//update sponsors' balances - removed
//Now: Update balance of those who contributed
$sql = "SELECT * FROM users TRY WHERE uid != '".$uid."'"; // AND balance != '0'"; //If you have no money you haven't contributed'
    $stmt = $conn->prepare($sql);
	if($stmt->execute())
	{if($stmt->rowCount()>0)
	{ 
    $sponsors = $stmt->rowCount();
    $add = 0;
    $add = ($amount / $sponsors);
      $data = array(); $preval = 0; $theuid = 0;  $newbal = 0;
	 while($row = $stmt -> fetch())
	 {
         $data[] = $row;
     }
     foreach($data as $dt){
	   $preval = $dt['balance']; //the previous balance
       $theuid = $dt['uid']; //His/her uid
       $newbal = $preval + $add;
       //we update each record as they loop
    $sql = "UPDATE users SET balance=? WHERE uid=?";
    $stmt = $conn->prepare($sql);
	$stmt -> bindParam(1, $newbal);
	$stmt -> bindParam(2, $theuid);
    $stmt->execute();
    //$stmt = null;
	 }
    }
	}
$msg = "";
if($extra > 0){
   $msg = "Loan repayment of KShs ".$amount." successfully made. Your loan is fully settled and the extra amount of KShs ".$extra." has been deposited into your account";
}else{
 $msg = "Loan repayment of KShs ".$amount." successfully made, the balance is KShs ".$newloanbal;
}

//Send confirmation text to borrower
//$msg = "PTPLS Notification! Deposit of KShs ".$amount." successfully made. New balance: KShs. ".$newbal;
$tophone = $_SESSION['phone'];
sendsms($msg, $tophone);

$_SESSION['reploansuccess'] = $msg;
  //echo "<meta http-equiv='refresh' content='0;url=../borrower.php'> ";
    if($_SESSION['usertype'] == "Sponsor"){
        //header("Location: ../sponsor.php");
        echo "<meta http-equiv='refresh' content='0;url=../sponsor.php'> "; } else{
       //header("Location: ../borrower.php");
       echo "<meta http-equiv='refresh' content='0;url=../borrower.php'> ";}
    }
catch(PDOException $e)
    {
   echo "<div style='height:auto; width:50%; color:#000000; margin:auto;top:100px; background-color:#cc7a00; border-radius:5px;border-style: solid; border-width:thin;border-color: red;'>".$sql . "<br>" . $e->getMessage()." <br>Go back and retry.</div>";
     }
}

}
?>