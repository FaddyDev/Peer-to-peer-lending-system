<?php if(session_status()==PHP_SESSION_NONE){
session_start();} ?>
<?php include("includes/dbconn.php"); //Create db first ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>PTPLS | Home</title>
<link rel="shortcut icon" href="images/favicon2.ico" type="image/x-icon">
<link rel="icon" href="images/favicon2.ico" type="image/x-icon">

 <link href="myassets/mystyles.css" type="text/css" rel="stylesheet" />

 <link href="bootstrap/css/bootstrap.css" type="text/css" rel="stylesheet" />
   <link href="bootstrap/css/bootstrap-theme.css" type="text/css" rel="stylesheet" />
  <script type="text/javascript" src="bootstrap/js/jquery-3.2.1.js"> </script>
  <script type="text/javascript" src="bootstrap/js/bootstrap.js"></script>
  <script type="text/javascript" src="myassets/myscripts.js"></script>
</head>

	<style type="text/css">
 @media (min-width: 1200px) {
  .carousel-inner > .item > img,
  .carousel-inner > .item > a > img {
     width: 100%;
	  height: 500px;
  }
}

/* Landscape tablets and medium desktops */
@media (min-width: 992px) and (max-width: 1199px) {
  .carousel-inner > .item > img,
  .carousel-inner > .item > a > img {
      width: 100%;
	  height: 500px;
  }
}

/* Portrait tablets and small desktops */
@media (min-width: 768px) and (max-width: 991px) {
 .carousel-inner > .item > img,
  .carousel-inner > .item > a > img {
      width: 100%;
	  height: 400px;
  }
}

/* Landscape phones and portrait tablets */
@media (max-width: 767px) {
 .carousel-inner > .item > img,
  .carousel-inner > .item > a > img {
      width: 100%;
      height: 300px;
  }
}

/* Portrait phones and smaller */
@media (max-width: 480px) {
 .carousel-inner > .item > img,
  .carousel-inner > .item > a > img {
      width: 100%;
      height: 300px;
  }
}

.carousel-caption{
    background: rgba(0, 0, 0, .5);
    nwidth: 100%;
    display: inline-block;
}
	</style>

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
        <li class="active bg-warning"> <a href="index.php">Home</a></li>

       <?php if(isset($_SESSION["is_logged"])){?>
           <?php if($_SESSION["usertype"] == "Sponsor"){ ?>
        <li><a href="sponsor.php">Dashboard</a></li>
        <?php } else{?>
        <li><a href="borrower.php">Dashboard</a></li>
        <?php } }?>

    
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


<div class="panel panel-info"><!--Main Panel -->
<?php include("includes/head.php"); //Heading ?>


<div class="panel panel-body container">
<?php if(isset($_SESSION["signed"])){
  $echo = $_SESSION["signed"];
    echo '<div class="alert alert-success" role="alert"><span class="glyphicon glyphicon-ok"></span> '.$echo.'</div>';    
unset($_SESSION['signed']);
	}?>
  <?php if(isset($_SESSION["signfail"])){//echo $fail;} ?>
 <?php $echo = $_SESSION["signfail"]; ?>
    <div class="alert alert-warning" style="text-align:center;" role="alert"><span class="glyphicon glyphicon-warning-sign"></span> <?php echo $echo; ?> </div>
  <?php  unset($_SESSION['signfail']); } ?>

   <?php if(isset($_SESSION["passfail"])){//echo $fail;} ?>
 <?php $echo = $_SESSION["passfail"]; ?>
    <div class="alert alert-warning" style="text-align:center;" role="alert"><span class="glyphicon glyphicon-warning-sign"></span> <?php echo $echo; ?> </div>
  <?php  unset($_SESSION['passfail']); } ?>

   <?php if(isset($_SESSION["phonefail"])){//echo $fail;} ?>
 <?php $echo = $_SESSION["phonefail"]; ?>
    <div class="alert alert-warning" style="text-align:center;" role="alert"><span class="glyphicon glyphicon-warning-sign"></span> <?php echo $echo; ?> </div>
  <?php  unset($_SESSION['phonefail']); } ?>
  
<?php include("includes/slider.php"); //Heading ?>
      </div>

     
</div>

 <?php include("includes/footer.php"); ?>

 <?php include("includes/signlogin.php"); ?>


</body>
</html>
