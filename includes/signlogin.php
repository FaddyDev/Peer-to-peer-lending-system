<?php if(session_status()==PHP_SESSION_NONE){
session_start();} ?>
 <script>
function checkPasswordMatch1() {
    var submt = document.getElementById("submit1");
    var password = $("#pass").val();
    var confirmPassword = $("#pass2").val();
	
	if(confirmPassword == ''){ $("#divCheckPasswordMatch").html("");
		submt.style.display = 'none';}
	else{
    if (password != confirmPassword){
        $("#divCheckPasswordMatch").html("<font color='red'>Passwords do not match!</font>");
		submt.style.display = 'none';}
    else{
        $("#divCheckPasswordMatch").html("Passwords match.");
		submt.style.display = 'block';}
		}
}


function checkPasswordMatch() {
    var submt = document.getElementById("submit1");
    var password = $("#pass").val();
    var confirmPassword = $("#pass2").val();
	
	if(password == ''){ $("#divCheckPasswordMatch").html("");
		submt.style.display = 'none';}
	else{
    if (password != confirmPassword){
        $("#divCheckPasswordMatch").html("<font color='red'>Passwords do not match!</font>");
		submt.style.display = 'none';}
    else{
        $("#divCheckPasswordMatch").html("Passwords match.");
		submt.style.display = 'block';}
		}
}

    function checkAdr() {
    var type = $("input[type='radio'][name='type']:checked").val();//$(".ho").val();
    var hdr = $("#hdr1").val();
 
	if(type == 'bor' & hdr == ''){ 
    alert("Kindly provide your home address");
    return false;}
	else{
    return true;}
     }
  </script>

  <script type="text/javascript">
    $(document).ready(
  function(){$('#hdr').hide();
  });
  </script>
  <script type="text/javascript">
    $(document).ready(
  function(){$('.others').hide();
  });
  </script>
  
  <?php include("dbconn.php"); //Establish connection to db ?>
<?php
//Sign up
if(isset($_POST['sign'])){

$hashed_pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
/*$address = 'N/A';
if(isset($_POST['address']) && !(empty($_POST['address']))){*/
    $address = $_POST['address'];
//}
 $sql = "SELECT * FROM users TRY WHERE phone = ?";     
    $stmt = $conn->prepare($sql);
	if($stmt->execute(array($_POST['phone'])))
	{
    if($stmt->rowCount()>0){
        $_SESSION['signfail'] = "The phone number exists, please sign up using another";
        //header("Location: ../index.php");
        echo "<meta http-equiv='refresh' content='0;url=../index.php'> ";
    }
else{

try {    $sql = "INSERT INTO users (type, name, ID, phone, email, zip, address, password)
    VALUES (?,?,?,?,?,?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt -> bindParam(1, $_POST['type']);
	$stmt -> bindParam(2, $_POST['name']);
	$stmt -> bindParam(3, $_POST['idno']);
	$stmt -> bindParam(4, $_POST['phone']);
	$stmt -> bindParam(5, $_POST['email']);
	$stmt -> bindParam(6, $_POST['zip']);
    $stmt -> bindParam(7, $address);
	$stmt -> bindParam(8, $hashed_pass);
    $stmt->execute();
    $_SESSION['signed'] = "Sign up successful. Use your phone number as username with your password to log in";
    //header("Location: ../index.php");
    echo "<meta http-equiv='refresh' content='0;url=../index.php'> ";
    }
catch(PDOException $e)
    {
   echo "<div style='height:auto; width:50%; color:#000000; margin:auto;top:100px; background-color:#cc7a00; border-radius:5px;border-style: solid; border-width:thin;border-color: red;'>".$sql . "<br>" . $e->getMessage()." <br>Go back and retry.</div>";
     }
}
    }

}
else if(isset($_POST['login'])){


try {  
 $sql = "SELECT * FROM users TRY WHERE phone = ?";     
    $stmt = $conn->prepare($sql);
	if($stmt->execute(array($_POST['username'])))
	{
    if($stmt->rowCount()>0){
	 while($row = $stmt -> fetch())
	 {
	  if(password_verify($_POST['password'],$row['password']) == 1)
	   {
	    $_SESSION['is_logged'] = true;
        $_SESSION['uid'] = $row['uid'];
		$_SESSION['phone'] = $row['phone']; 
        $_SESSION['usertype'] = $row['type']; 
        
        if($row['type'] == "Sponsor"){
        //header("Location: ../sponsor.php");
        echo "<meta http-equiv='refresh' content='0;url=../sponsor.php'> "; } else{
       //header("Location: ../borrower.php");
       echo "<meta http-equiv='refresh' content='0;url=../borrower.php'> ";}
	 }
  else{
      $_SESSION['passfail'] = "Login failed, check the password then try again";
        //header("Location: ../index.php");
        echo "<meta http-equiv='refresh' content='0;url=../index.php'> ";
      }
	 }
	  
	} 
    else{
        $_SESSION['phonefail'] = "Login failed, check phone number then try again";
        //header("Location: ../index.php");
        echo "<meta http-equiv='refresh' content='0;url=../index.php'> ";
    }
   }	
  }
catch(PDOException $e)
    {
    echo $sql . "<br>" . $e->getMessage();
    }

$conn = null;
}

else if(isset($_GET['out'])){
unset($_SESSION['is_logged']);
unset($_SESSION['uid']);
unset($_SESSION['phone']);
unset($_SESSION['usertype']);
//header("Location: ../index.php");
echo "<meta http-equiv='refresh' content='0;url=../index.php'> ";
}
else{
?>

<!--Sign up modal-->
<!-- Modal -->
<div id="ModalSign" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">User Sign Up</h4>
      </div>
      <div class="modal-body">
	  <form action="includes/signlogin.php" class="form-group" onsubmit="return checkAdr()" method="post">
<p id="type">
<label for="user">User</label> <br/>
<input type="radio" name="type" id="spo" value="Sponsor" class="type" required>Sponsor
<input type="radio" name="type" id="bor" value="Borrower" class="type" required>Client (Borrower)
</p>
<div class="others">
<label for="name">Name</label>
<input type="text" name="name" class="form-control add-todo" placeholder="Enter your name"  required /></br>
<label for="idno">ID Number</label>
<input type="text" name="idno" class="form-control add-todo"  onKeyPress="return numbersonly(event)" placeholder="Enter your national id or passport number"  required /></br>
<label for="phone">Phone Number</label>
<input type="text" name="phone" class="form-control add-todo" onKeyPress="return numbersonly(event)" placeholder="Enter your mobile phone number"  required /></br>
<label for="email">Email</label>
<input type="text" name="email" class="form-control add-todo" placeholder="Enter your email"  required /></br>

<p idd="hdr">
<label for="h_adddress">Home Address</label>
<input type="text" name="address" class="form-control add-todo" id="hdr1" placeholder="Enter your home address: E.g. P.O. Box 658" /></br>
<label for="zip">Zip Code</label>
<input type="text" name="zip" class="form-control add-todo" onKeyPress="return numbersonly(event)" placeholder="E.g. 10100"  required /></br>
</p>

<div class="registrationFormAlert" id="divCheckPasswordMatch"></div>
<label for="password">Password</label>
<input type="text" name="password" class="form-control add-todo" id="pass" placeholder="Enter a password" onkeyup="checkPasswordMatch1();" required /></br>
<label for="confpass">Re-Enter Password</label>
<input type="password1" class="form-control add-todo" name="pass2" id="pass2" onkeyup="checkPasswordMatch();" placeholder="Re-Enter The Password"  required/></br>
<input type="submit" name="sign" class="btn btn-info" style="float: right;" value="Sign Up" id="submit1">	
</div>

</form>
      
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<!--End of sign up modal -->


<!--Login modal-->
<!-- Modal -->
<div id="ModalLogin" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Login</h4>
      </div>
      <div class="modal-body">
       <form action="includes/signlogin.php" class="form-group" method="post">

<input type="text" name="username" class="form-control add-todo" onKeyPress="return numbersonly(event)" id="log" placeholder="Enter the Phone Number You Registered With"  required /></br>

<div class="" id="failedlogindiv"></div>

<td><input type="password" class="form-control add-todo" name="password" id="log" placeholder="Enter Your Password"  required/></br>
<a href="#">Forgot Password? </a></td>
<input type="submit" name="login" class="btn btn-info" style="float: right;" value="Log In" id="submit">			
</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<!--End of Login modal -->

<?php } ?>

<script>
	/*$("#bor").click(function(){
	
        $("#hdr").show("slow");
    });

    $("#spo").click(function(){
	
        $("#hdr").hide("slow");
    });*/
  
  $("#type").click(function(){
	
        $(".others").show("slow");
    });
	
</script>