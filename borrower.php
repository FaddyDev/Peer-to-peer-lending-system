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

       <?php if(isset($_SESSION["is_logged"])){?>
           <?php if($_SESSION["usertype"] == "Sponsor"){ ?>
        <li class="active"><a href="sponsor.php">Dashboard</a></li>
        <?php } else{?>
        <li class="active"><a href="borrower.php">Dashboard</a></li>
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
		<li><a href="receipt.php">Download Statement</a></li> 
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
 <div class="panel-heading head">Loanee's Dashboard</div>
      <div class="panel-body container">

      <?php include("includes/dbconn.php"); //DB
      $acbal = 0; $loanbal = 0; $comment = ""; $status = ""; $guara = ""; $newby = 0; $duedate = ""; $now = ""; $overdue = "";
      $lid = 0; $bantill = ""; $dateexpctd = "";$dateout = ""; $tid = ""; $newbal = 0; 
try { 
 
  $sql = "SELECT * FROM users WHERE uid = ?"; 
    $stmt = $conn->prepare($sql);
	if($stmt->execute(array($_SESSION['uid'])))
	{
	 while($row = $stmt -> fetch())
	 {
	   $borrower[] = $row;
     $acbal = $row['balance'];
	 }
	} 
  }
catch(PDOException $e)
    {
    echo "<div style='height:auto; width:50%; color:#000000; margin:auto;top:100px; background-color:#cc7a00; border-radius:5px;border-style: solid; border-width:thin;border-color: red;'>".$sql . "<br>" . $e->getMessage()." <br>Please reload page.</div>";
   }

   $guarantor = array();
try { 
 
  $sql = "SELECT * FROM guarantors WHERE uid = ? AND approval = 1 "; 
    $stmt = $conn->prepare($sql);
	if($stmt->execute(array($_SESSION['uid'])))
	{
	 while($row = $stmt -> fetch())
	 {
	   $guarantor[] = $row;
	 }
   
   if($stmt->rowCount() > 0){ 
     $guara = "Exists";
   }
	} 
  }
catch(PDOException $e)
    {
    echo "<div style='height:auto; width:50%; color:#000000; margin:auto;top:100px; background-color:#cc7a00; border-radius:5px;border-style: solid; border-width:thin;border-color: red;'>".$sql . "<br>" . $e->getMessage()." <br>Please reload page.</div>";
   }

   //get loan bal...
try { 
 
  $sql = "SELECT * FROM loans WHERE uid = ? AND lid = (SELECT MAX(lid) FROM loans WHERE uid=?)"; 
    $stmt = $conn->prepare($sql);
	if($stmt->execute(array($_SESSION['uid'],$_SESSION['uid'])))
	{
     $amount = 0; $datein = ""; $dtin1 = ""; $dtexpct1 = "";
	 while($row = $stmt -> fetch())
	 {
    $loanbal = $row['balance'];
    $comment = $row['comment'];
    $status = $row['status'];
    $dateexpctd = $row['dateexpected'];
    $datein = $row['datein'];
    $amount = $row['amount'];
    $lid = $row['lid'];     
    $tid = $row['tid'];
    $dateout = $row['dateout'];      
 }

//if fined but had cleared
if($comment === "Defaultor" && $loanbal == 0){
  //in case he/she payed late
$dt = DateTime::createFromFormat('d/m/Y H:i', $dateexpctd)->format('Y-m-d H:i');
$dtin = DateTime::createFromFormat('d/m/Y H:i', $datein)->format('Y-m-d H:i');

$dtexpct1 = new DateTime($dt); 
$dtin1 = new DateTime($dtin);

  $pendays = $dtin1->diff($dtexpct1)->format('%a');

  $ban = (new DateTime($dtin))->add(new DateInterval('P'.$pendays.'D'))->format('Y-m-d H:i');

  $tdy = date('d/m/Y H:i');
$leo = DateTime::createFromFormat('d/m/Y H:i', $tdy)->format('Y-m-d H:i');

$bantill = new DateTime($ban); 
$now = new DateTime($leo);

            if($now > $bantill){ //Punishment period ended
            $com = "Cleared to loan"; $st = "Eligible";
              $sql = "UPDATE loans SET comment=?, status=? WHERE lid=?";
              $stmt = $conn->prepare($sql);
              $stmt -> bindParam(1, $com);
	            $stmt -> bindParam(2, $st);
	            $stmt -> bindParam(3, $lid);
              $stmt->execute();

              $comment = $com; $status = $st;
            }
        } 

   if($stmt->rowCount() == 0){ //If no record in loans table means the loanee has never loaned, so is eligible
     $status = "Eligible";
     $newby = 1; //Newbies qualify for lowest loan alone
   }
   if($loanbal > 0){ 
$tdy = date('d/m/Y H:i');
$dt = DateTime::createFromFormat('d/m/Y H:i', $dateexpctd)->format('Y-m-d H:i');
$leo = DateTime::createFromFormat('d/m/Y H:i', $tdy)->format('Y-m-d H:i');

$duedate = new DateTime($dt); 
$now = new DateTime($leo);

$overdue = $now->diff($duedate)->format('%a day(s) and %h hour(s) and %i mins');

//If the loan is overdue then add interest..new interest every new late week
   if($now > $duedate){
       $rate = 0; $weeks = 0; $newcom = "";
        $extradays = $now->diff($duedate)->format('%a');

    $sql = "SELECT * FROM loan_terms TRY WHERE range_from <=? AND range_to >= ?";
            $stmt = $conn->prepare($sql);
            $stmt -> bindParam(1, $amount);
  	       $stmt -> bindParam(2, $amount);
          if($stmt->execute())
	        {       
         while($row = $stmt -> fetch()){
         $rate = $row['rate']; }
         }
          if($extradays >=1 && $extradays <=7 && $comment !== "Fined Once"){  //later to use hours to avoid the lost extra day
            $newcom = "Fined Once";
          }
          if($extradays >7 && $extradays <=14 && $comment !== "Fined Twice"){  
            $newcom = "Fined Twice";
          }
          if($extradays >14 && $extradays <=21 && $comment !== "Fined Thrice"){  
            $newcom = "Fined Thrice";
          }
          if($extradays >21 && $extradays <=28 && $comment !== "Fined Fourth"){  
            $newcom = "Fined Fourth";
          }
          if($extradays >28 && $extradays <=35 && $comment !== "Fined Fifth"){  
            $newcom = "Fined Fifth";
          }
          if($extradays >35 && $extradays <=42 && $comment !== "Fined Sixth"){  
            $newcom = "Fined Sixth";
          }

          if($newcom !== ""){
              //update balance
         $newbal = $loanbal + ($amount * ($rate/100)); 
          
         $sql = "INSERT INTO loans (tid, uid, amount, dateout, dateexpected, balance, comment, status)
         VALUES (?,?,?,?,?,?,?,?)";
         $stmt = $conn->prepare($sql);
         $stmt -> bindParam(1, $tid);
       $stmt -> bindParam(2, $_SESSION['uid']);
       $stmt -> bindParam(3, $amount);
       $stmt -> bindParam(4, $dateout);
         $stmt -> bindParam(5, $dateexpctd);
       $stmt -> bindParam(6, $newbal);
         $stmt -> bindParam(7, $newcom);
         $stmt -> bindParam(8, $status);
         $stmt->execute();
          }
        }
        //End of if overdue
   }
	} 
  }
catch(PDOException $e)
    {
    echo "<div style='height:auto; width:50%; color:#000000; margin:auto;top:100px; background-color:#cc7a00; border-radius:5px;border-style: solid; border-width:thin;border-color: red;'>".$sql . "<br>" . $e->getMessage()." <br>Please reload page.</div>";
   }

//get loan terms
     $terms = array();
try { 
 
  $sql = "SELECT * FROM loan_terms"; 
    $stmt = $conn->prepare($sql);
	if($stmt->execute())
	{
	 while($row = $stmt -> fetch())
	 {
	   $terms[] = $row;
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
              <h4>Bio Data</h4>
               <?php foreach ($borrower as $bor){?>
		          Name: <em><?php echo $bor['name'];?></em><br/>
		          ID: <em><?php echo $bor['ID'];?></em><br/>
		          Phone: <em><?php echo $bor['phone'];?></em><br/>
		          Email: <em><?php echo $bor['email'];?></em><br/>
		          Address: <em><?php echo $bor['address'];?></em><br/>
		          Zip Code: <em><?php echo $bor['zip'];?></em><br/>
              <a class="btn btn-xs btn-warning" href="editprofile.php?uid=<?php echo $bor['uid'];?>">Edit</a>
		          <?php }?>
               </div> 
               <!-- End bio data div -->

<!-- Begin deposit div -->
<div class="bg-success col-md-12 dash"> 
              <h4>Deposit</h4>
             <span class="label label-default">To deposit cash, send the money via Mpesa to 0726279601 (some paybill number) then submit 
             the transaction id below.</span><br/><br/>

            <?php if(isset($_SESSION["success"])){
  $echo = $_SESSION["success"];
    echo '<div class="alert alert-info" role="alert"><span class="glyphicon glyphicon-ok"></span> '.$echo.'</div>';    
unset($_SESSION['success']);
	}?>

   <?php if(isset($_SESSION["fail"])){//echo $fail;} ?>
 <?php $echo = $_SESSION["fail"]; ?>
    <div class="alert alert-warning" role="alert"><span class="glyphicon glyphicon-warning-sign"></span> <?php echo $echo; ?> </div>
  <?php  unset($_SESSION['fail']); } ?>

 <fieldset><form action="ops/queries.php" class="form-group col-md-4" method="post">
<input type="hidden" name="uid" value="<?php echo $_SESSION['uid'];?>" />
      <input type="hidden" name="prevbalance" value="<?php echo $acbal;?>" />

<input type="text" name="tid" class="form-control add-todo" placeholder="Transaction ID e.g. LII2A7RXVE"  required /></br>
<input type="submit" name="deposit" class="btn btn-xs btn-info" style="float: left;" value="Submit">	
</form></fieldset>
</div> 
<!-- End deposit div -->

<!-- Begin guarantor div -->
               <div class="bg-info col-md-12 dash"> 
              <h4>Guarantor</h4>

              <?php if(isset($_SESSION["success"])){
  $echo = $_SESSION["success"];
    echo '<div class="alert alert-success" role="alert"><span class="glyphicon glyphicon-ok"></span> '.$echo.'</div>';    
unset($_SESSION['success']);
	}?>

   <?php if(isset($_SESSION["fail"])){//echo $fail;} ?>
 <?php $echo = $_SESSION["fail"]; ?>
    <div class="alert alert-warning" role="alert"><span class="glyphicon glyphicon-warning-sign"></span> <?php echo $echo; ?> </div>
  <?php  unset($_SESSION['fail']); } ?>

              <?php if(count($guarantor) > 0){ //if guarantor exists, display details
              foreach ($guarantor as $gua){ ?>
		          Name: <em><?php echo $gua['name'];?></em><br/>
		          ID: <em><?php echo $gua['ID'];?></em><br/>
		          Phone: <em><?php echo $gua['phone'];?></em><br/>
              Occupation: <em><?php echo $gua['occupation'];?></em><br/>
             <!-- <a class="btn btn-xs btn-warning" href="editprofile.php?uid=<?php //echo $gua['uid'];?>">Edit</a> -->
		         <?php } 
             
      } else{ //if guarantor does not exist, add one ?>
<fieldset><legend>Add Guarantor</legend><form action="ops/queries.php" class="form-group col-md-4" method="post">
<input type="hidden" name="uid" value="<?php echo $_SESSION['uid'];?>" />
<label for="name">Name</label>
<input type="text" name="name" class="form-control add-todo" placeholder="Enter the guarantor's name"  required /></br>
<label for="idno">ID Number</label>
<input type="text" name="idno" class="form-control add-todo"  onKeyPress="return numbersonly(event)" placeholder="Enter the guarantor's national id or passport number"  required /></br>
<label for="phone">Phone Number</label>
<input type="text" name="phone" class="form-control add-todo" onKeyPress="return numbersonly(event)" placeholder="Enter the guarantor's mobile phone number"  required /></br>

<p>
<label for="h_adddress">Home Address</label>
<input type="text" name="address" class="form-control add-todo" id="hdr1" placeholder="Enter the guarantor's home address" />
</p>

<p>
<label for="occupation">Occupation</label>
<input type="text" name="occupation" class="form-control add-todo" placeholder="Provide the guarantor's occupation" />
</p>

<input type="submit" name="add" class="btn btn-xs btn-info" style="float: left;" value="Submit">	
</form></fieldset>  
<?php } ?>       
 </div> 
 <!-- End guarantor div -->

 <!-- Begin balance div -->

               <div class="bg-success col-md-12 dash"> 
               <?php if($newbal != 0){$loanbal = $newbal;} ?>
              <h4>Balance</h4>
             Your current account balance is <span class="label label-primary">KShs. <?php echo $acbal; ?></span><br/>
             <?php if($loanbal > 0) { ?>
               Your current loan balance is <span class="label label-primary">KShs. <?php echo $loanbal; ?></span><br/><br/>
              <?php if($now <= $duedate){ ?>
                Due date is <span class="label label-primary"><?php echo $duedate->format('d/m/Y H:i'); ?></span><br/><br/>
             <?php } else { ?>
             <p class="col-md-8 bg-danger"> Your loan is overdue by  <?php echo $overdue; ?>. Kindly pay now to avoid additional charges and  penalties.</p><br/><br/>
              <p class="col-md-8 bg-warning"> The due date was <?php echo $duedate->format('d/m/Y H:i'); ?></p><br/><br/>
             <?php }} else{
               if($bantill === ""){
              echo "You have no outstanding loan balance";                 
               }
              else{ echo "<br/><div class='bg-warning col-md-2'> You have been barred from requesting loans till ".$bantill->format('d/m/Y H:i')." due to a previous delayed repayment</div>"; }
            } ?>
               </div>   
   <!-- End balance div -->    

 <!-- Begin loans div -->
               <div class="bg-warning col-md-12 dash"> 
              <h4>Loans</h4>

              <?php if(isset($_SESSION["loansuccess"])){
  $echo = $_SESSION["loansuccess"];
    echo '<div class="alert alert-success" role="alert"><span class="glyphicon glyphicon-ok"></span> '.$echo.'</div>';    
unset($_SESSION['loansuccess']);
	}?>

   <?php if(isset($_SESSION["loanfail"])){//echo $fail;} ?>
 <?php $echo = $_SESSION["loanfail"]; ?>
    <div class="alert alert-warning" role="alert"><span class="glyphicon glyphicon-warning-sign"></span> <?php echo $echo; ?> </div>
  <?php  unset($_SESSION['loanfail']); } ?>

              <h5>Loan Terms</h5>

     <table class="table table-striped ttable-bordered table-condensed table-responsive" id="lntermstb">          
		<thead>
          <tr cclass="text-primary">
        <th>Category</th>
		    <th>Range (KShs)</th>
			<th>Period (days)</th>
            <th>Rate (% per week)</th>
          </tr>
		  </thead>

		  <tbody>
		  <?php foreach ($terms as $tms){?>
		  <tr><td><?php echo $tms['category'];?></td>
      <td><?php echo $tms['range_from']. ' - '.$tms['range_to'];?></td>
		  <td><?php echo $tms['period'];?></td>
		  <td><?php echo $tms['rate'];?></td>
      </tr>
		  <?php }?>
		  </tbody>
        </table>
        
             <?php if($status === "Eligible" && $guara === "Exists") { //Apply?>
  <h5>Request Loan</h5>
  <?php if($newby == 1){ ?> <p class="col-md-8 bg-danger">You are a first timer here, so please enter amount within the lowest range</p><br/> <br/> <?php } ?>

 <fieldset><form action="ops/queries.php" class="form-group col-md-4" method="post">
 
<input type="hidden" name="uid" value="<?php echo $_SESSION['uid'];?>" />
<input type="hidden" name="newby" value="<?php echo $newby;?>" />
<input type="hidden" name="balprev" value="<?php echo $acbal;?>" /><?php 
$guarphone = "";
foreach ($guarantor as $gua){ $guarphone = $gua['phone'];  }?>
<input type="hidden" name="guarphone" value="<?php echo $guarphone;?>" />
<label for="amount">Amount</label>
<input type="text" name="amount" class="form-control add-todo"  onKeyPress="return numbersonly(event)" placeholder="Enter the amount of loan you are requesting"  required /></br>


<input type="submit" name="request" class="btn btn-xs btn-info" style="float: left;" value="Submit">	
</form></fieldset> 
     <?php  } ?>

     

            <?php if(isset($_SESSION["reploansuccess"])){
  $echo = $_SESSION["reploansuccess"];
    echo '<div class="alert alert-info" role="alert"><span class="glyphicon glyphicon-ok"></span> '.$echo.'</div>';    
unset($_SESSION['reploansuccess']);
	}?>

   <?php if(isset($_SESSION["reploanfail"])){//echo $fail;} ?>
 <?php $echo = $_SESSION["reploanfail"]; ?>
    <div class="alert alert-warning" role="alert"><span class="glyphicon glyphicon-warning-sign"></span> <?php echo $echo; ?> </div>
  <?php  unset($_SESSION['reploanfail']); } ?>

     <?php if($loanbal > 0){ ?>
      <h5>Repay Loan</h5>
    <p class="col-md-12 bg-default">To repay loan, send the money via Mpesa to 0726279601 (some paybill number) then submit 
             the transaction id below.</p><br/><br/>

 <fieldset><form action="ops/queries.php" class="form-group col-md-4" method="post">
<input type="hidden" name="uid" value="<?php echo $_SESSION['uid'];?>" />
<input type="hidden" name="prevloanbalance" value="<?php echo $loanbal;?>" />
<input type="hidden" name="prevacbalance" value="<?php echo $acbal;?>" />
<input type="hidden" name="prevcom" value="<?php echo $comment;?>" />
<input type="hidden" name="lid" value="<?php echo $lid;?>" />
<input type="hidden" name="termid" value="<?php echo $tid;?>" />
<input type="hidden" name="loanamount" value="<?php echo $amount;?>" />
<input type="hidden" name="dtout" value="<?php echo $dateout;?>" />
<input type="hidden" name="dtexpt" value="<?php echo $dateexpctd;?>" />

<input type="text" name="tid" class="form-control add-todo" placeholder="Transaction ID e.g. LII2A7RXVE"  required /></br>
<input type="submit" name="reploan" class="btn btn-xs btn-info" style="float: left;" value="Submit">	
</form></fieldset>
     <?php } ?>
</div>  
   <!-- End loans div -->           
               
	  </div> <!--End of panel body -->
     
</div> <!-- End of  panel -->

 <?php include("includes/footer.php"); ?>

 <?php include("includes/signlogin.php"); ?>

</body>
</html>
