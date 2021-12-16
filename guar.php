<?php if(session_status()==PHP_SESSION_NONE){
session_start();} ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>PTPLS | Dashboard</title>
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
.dash{
    border-radius: 5px;
    margin-bottom: 10px;
    padding: 5px;
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



<div class="panel panel-info">
 <div class="panel-heading head">Guarantor's Dashboard</div>
      <div class="panel-body container">

      <?php include("includes/dbconn.php"); //DB
  if(isset($_GET['u'])){ 
      $client = array(); 
try { 
 
  $sql = "SELECT * FROM users WHERE uid = ?"; 
    $stmt = $conn->prepare($sql);
	if($stmt->execute(array($_GET['u'])))
	{
	 while($row = $stmt -> fetch())
	 {
	   $client[] = $row;
	 }
	} 
  }
catch(PDOException $e)
    {
    echo "<div style='height:auto; width:50%; color:#000000; margin:auto;top:100px; background-color:#cc7a00; border-radius:5px;border-style: solid; border-width:thin;border-color: red;'>".$sql . "<br>" . $e->getMessage()." <br>Please reload page.</div>";
   }


  
	
$stmt = null;
 ?>
<!-- Begin bio data div -->
              <div class="bg-primary col-md-12 dash"> 
              <span class="label label-info">Welcome to your dashboard</span><br/>
              <h4>The person described by the bio data below added you as a guarantor</h4>
               <?php 
               $phone = ""; $name = "";
               foreach ($client as $bor){?>
		          Name: <em><?php echo $bor['name'];?></em><br/>
		          ID: <em><?php echo $bor['ID'];?></em><br/>
		          Phone: <em><?php echo $bor['phone'];?></em><br/>
		          Email: <em><?php echo $bor['email'];?></em><br/>
		          Address: <em><?php echo $bor['address'];?></em><br/>
		          Zip Code: <em><?php echo $bor['zip'];?></em><br/><br/>
              <?php 
            $phone = $bor['phone']; $name = $bor['name']; }?>

              <fieldset><form action="ops/queries.php" class="form-group col-md-4" method="post">
<input type="hidden" name="uid" value="<?php echo $_GET['u'];?>" />
      <input type="hidden" name="name" value="<?php echo $name;?>" />
      <input type="hidden" name="phone" value="<?php echo $phone;?>" />
      <input type="hidden" name="guarphone" value="<?php echo $_GET["gfn"];?>" />

      <span class="label label-info">Approve or Reject</span>
      <select name="apr" class="form-control add-todo">
      <option value="1">Approve</option>
      <option value="0">Reject</option>
      </select></br>
<input type="submit" name="confguar" class="btn btn-xs btn-info" style="float: left;" value="Submit">	
</form></fieldset>
               </div> 
  <?php } else{?>
            <?php if(isset($_SESSION["success"])){
  $echo = $_SESSION["success"];
    echo '<div class="alert alert-info" role="alert"><span class="glyphicon glyphicon-ok"></span> '.$echo.'</div>';    
unset($_SESSION['success']);
	}}?>     
               
	  </div> <!--End of panel body -->
     
</div> <!-- End of  panel -->

 <?php include("includes/footer.php"); ?>

 <?php include("includes/signlogin.php"); ?>

</body>
</html>
