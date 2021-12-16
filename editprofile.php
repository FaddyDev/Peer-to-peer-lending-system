<?php if(session_status()==PHP_SESSION_NONE){
session_start();} ?>
<?php
if(isset($_GET['uid'])){
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>PTPLS | Edit Profile</title>
<link rel="shortcut icon" href="images/favicon2.ico" type="image/x-icon">
<link rel="icon" href="images/favicon2.ico" type="image/x-icon">

 <link href="myassets/mystyles.css" type="text/css" rel="stylesheet" />

 <link href="bootstrap/css/bootstrap.css" type="text/css" rel="stylesheet" />
   <link href="bootstrap/css/bootstrap-theme.css" type="text/css" rel="stylesheet" />
  <script src="bootstrap/js/jquery-3.2.1.js"> </script>
  <script src="bootstrap/js/bootstrap.js"></script>
  <script src="myassets/myscripts.js"></script>

  <link rel="stylesheet" href="datepicker/css/jquery-ui.css">
  <script src="datepicker/js/jquery-1.10.2.js"></script>
  <script src="datepicker/js/jquery-ui.js"></script>

 <script>
  $(function() {
    $( ".datepicker" ).datepicker({dateFormat: "dd/mm/yy",maxDate: new Date()});
  });
  </script>

          <style type="text/css">
/* Large desktops and laptops */
@media (min-width: 1200px) {
.mydiv{
    width:50%; margin:auto;margin-top:0.5%;
}
}

/* Landscape tablets and medium desktops */
@media (min-width: 992px) and (max-width: 1199px) {
.mydiv{
    width:50%; margin:auto;margin-top:0.5%;
}
}

/* Portrait tablets and small desktops */
@media (min-width: 768px) and (max-width: 991px) {
.mydiv{
    width:70%; margin:auto;margin-top:0.5%;
}
}

/* Landscape phones and portrait tablets */
@media (max-width: 767px) {
.mydiv{
    width:90%; margin:auto;margin-top:0.5%;
}
}

/* Portrait phones and smaller */
@media (max-width: 480px) {
.mydiv{
    width:99%; margin:auto;margin-top:0.5%;
}
}
</style>
</head>

<body>
 <nav class="navbar ppanel-primary navbar-default navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand bbg-info" href="#"><img src="images/Counting-money-animation.gif" height="35px" /><b></b></a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav">
        <li> <a href="index.php">Home</a></li>

       <?php if(isset($_SESSION["is_logged"])){?>
           <?php if($_SESSION["usertype"] == "Sponsor"){ ?>
        <li><a href="sponsor.php">Dashboard</a></li>
        <?php } else{?>
        <li><a href="borrower.php">Dashboard</a></li>
        <?php } ?>
       <li class="active"> <a href="editprofile.php?uid=<?php echo $_GET['uid'];?>">Edit Profile</a> </li>
       <?php }?>

    
		<!--  <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Page 4
        <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <li><a href="#">Page 4-1</a></li>
          <li><a href="#">Page 4-2</a></li>
          <li><a href="#">Page 4-3</a></li>
        </ul>
		<li><a href="#">Contact Us</a></li>
      </li> -->
      </ul>
      <ul class="nav navbar-nav navbar-right">
	  <?php if(isset($_SESSION["is_logged"])){?>
	     <li><a href=""><span class="glyphicon glyphicon-user"></span> <?php echo $_SESSION["phone"];?></a></li>
		<li><a href="includes/signlogin.php?out"> <span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
	  <?php } else{ ?>
        <li><a data-toggle="modal" data-target="#ModalSign"><span class="glyphicon glyphicon-user"></span> Sign Up</a></li>
		<li><a data-toggle="modal" data-target="#ModalLogin"> <span class="glyphicon glyphicon-log-in"></span> Login</a></li>
		<?php } ?>
      </ul>
    </div>
  </div>
</nav>
<div class="panel panel-warning"> <!--Main Panel -->

<?php include("includes/dbconn.php"); //DB
$users = array();
try { 
 
  $sql = "SELECT * FROM users WHERE uid = ?"; 
    $stmt = $conn->prepare($sql);
	if($stmt->execute(array($_GET['uid'])))
	{
	 while($row = $stmt -> fetch())
	 {
	   $users[] = $row;
	 }
	} 
  }
catch(PDOException $e)
    {
    echo "<div style='height:auto; width:50%; color:#000000; margin:auto;top:100px; background-color:#cc7a00; border-radius:5px;border-style: solid; border-width:thin;border-color: red;'>".$sql . "<br>" . $e->getMessage()." <br>Please reload page.</div>";
   }
	
$stmt = null;
 ?>
  <div class="container head"> </div><!--formatter -->
     <?php if(isset($_SESSION["updated"])){
  $echo = $_SESSION["updated"];
    echo '<div class="alert alert-success mydiv" role="alert"><span class="glyphicon glyphicon-ok"></span> '.$echo.'</div>';    
unset($_SESSION['updated']);
	}?>

   <?php if(isset($_SESSION["phonefail"])){//echo $fail;} ?>
 <?php $echo = $_SESSION["phonefail"]; ?>
    <div class="alert alert-warning mydiv" style="text-align:center;" role="alert"><span class="glyphicon glyphicon-warning-sign"></span> <?php echo $echo; ?> </div>
  <?php  unset($_SESSION['phonefail']); } ?>


<div class="panel panel-info mydiv">
    <div class="panel-heading">Update Profile</div>
    <div class="panel-body">

 <?php foreach ($users as $user){?>
<form action="editprofile.php" class="form-group" method="post">
<input type="hidden" name="uid" value="<?php echo $user['uid'];?>" />

<label for="name">Name</label>
<input type="text" name="name" value="<?php echo $user['name'];?>"  class="form-control add-todo" placeholder="Enter your name"  required /></br>
<label for="idno">ID Number</label>
<input type="text" name="idno" value="<?php echo $user['ID'];?>"  class="form-control add-todo"  onKeyPress="return numbersonly(event)" placeholder="Enter your national id or passport number"  required /></br>
<label for="phone">Phone Number</label>
<input type="hidden" name="oldphone" value="<?php echo $user['phone'];?>" />
<input type="text" name="phone" value="<?php echo $user['phone'];?>"  class="form-control add-todo" onKeyPress="return numbersonly(event)" placeholder="Enter your mobile phone number"  required /></br>
<label for="email">Email</label>
<input type="text" name="email" value="<?php echo $user['email'];?>" class="form-control add-todo" placeholder="Enter your email"  required /></br>

<p id="hdrrr">
<label for="h_adddress">Home Address</label>
<input type="text" name="address" value="<?php echo $user['address'];?>"  class="form-control add-todo" id="hdr1" placeholder="Enter your home address" /></br>
<label for="zip">Zip Code</label>
<input type="text" name="zip" value="<?php echo $user['zip'];?>" class="form-control add-todo" onKeyPress="return numbersonly(event)" placeholder="E.g. 10100"  required /></br>
</p>

<div class="registrationFormAlert" id="divCheckPasswordMatch"></div>
<div class="alert alert-warning" role="alert"> <span class="glyphicon glyphicon-exclamation-sign"></span> There's an existing password, leave the password fields blank if you do not wish to change it</div>
<label for="password">Password</label>
<input type="text" name="password" class="form-control add-todo" id="pass" placeholder="Enter a password" onkeyup="checkPasswordMatch1();" /></br>
<input type="hidden" name="oldpass" value="<?php echo $user['password'];?>" />
<label for="confpass">Re-Enter Password</label>
<input type="password1" class="form-control add-todo" name="pass2" id="pass2" onkeyup="checkPasswordMatch();" placeholder="Re-Enter The Password"/></br>
<input type="submit" name="editprof" class="btn btn-info" style="float: right;" value="Update" id="submit1">	
</div>
<?php } ?>
</form>
               
	  </div> <!--End of panel body -->
</div> <!-- End of  panel -->

 <?php include("includes/footer.php"); ?>

 <?php include("includes/signlogin.php"); ?>

</body>
</html>
<?php } ?> 

<?php
if(isset($_POST['editprof'])){
  include("includes/dbconn.php"); //DB

$uid = $_POST["uid"];

$hashed_pass = $_POST['oldpass'];
if(isset($_POST['password']) && !(empty($_POST['password']))){
$hashed_pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
}

/*$address = 'N/A';
if(isset($_POST['address']) && !(empty($_POST['address']))){*/
    $address = $_POST['address'];
//}

$phoneexists = 0;
if(!($_POST['phone'] === $_POST['oldphone'])){
 $sql = "SELECT * FROM users TRY WHERE phone = ?";     
    $stmt = $conn->prepare($sql);
	if($stmt->execute(array($_POST['phone'])))
	{
    if($stmt->rowCount()>0){
      $phoneexists = 1;
    }
}
}

if($phoneexists == 1){
        $_SESSION['phonefail'] = "The phone number exists, please use another";
        header("Location: editprofile.php?uid=$uid");
}
else{
try {    $sql = "UPDATE users SET name=?, ID=?, phone=?, email=?, zip=?, address=?, password=? WHERE uid=?";
    $stmt = $conn->prepare($sql);
	$stmt -> bindParam(1, $_POST['name']);
	$stmt -> bindParam(2, $_POST['idno']);
	$stmt -> bindParam(3, $_POST['phone']);
	$stmt -> bindParam(4, $_POST['email']);
	$stmt -> bindParam(5, $_POST['zip']);
  $stmt -> bindParam(6, $address);
	$stmt -> bindParam(7, $hashed_pass);
  $stmt -> bindParam(8, $uid);
    $stmt->execute();
    $_SESSION['updated'] = "Profile updated successfully";
    header("Location: editprofile.php?uid=$uid");
    }
catch(PDOException $e)
    {
   echo "<div style='height:auto; width:50%; color:#000000; margin:auto;top:100px; background-color:#cc7a00; border-radius:5px;border-style: solid; border-width:thin;border-color: red;'>".$sql . "<br>" . $e->getMessage()." <br>Go back and retry.</div>";
     }
}

}
?>
