<?php if(session_status()==PHP_SESSION_NONE){
session_start();} ?>
<?php
//PDF USING MULTIPLE PAGES
require('includes/fpdf/fpdf.php');
require('includes/dbconn.php');

//Create new pdf file
$pdf=new FPDF();

//Disable automatic page break
$pdf->SetAutoPageBreak(false);

//Fetch user details
$name = ""; $idno = ""; $phone = "";
try { 
$sql = "SELECT * FROM users WHERE uid = ?"; 
 $stmt = $conn->prepare($sql);
if($stmt->execute(array($_SESSION['uid'])))
{
while($row = $stmt -> fetch())
{
  $name = $row['name'];
  $idno = $row['ID'];
  $phone = $row['phone'];     
}
}
}
catch(PDOException $e)
{
echo "<div style='height:auto; width:50%; color:#000000; margin:auto;top:100px; background-color:#cc7a00; border-radius:5px;border-style: solid; border-width:thin;border-color: red;'>".$sql . "<br>" . $e->getMessage()." <br>Please reload page.</div>";
}
//Add first page
$pdf->AddPage();

//Add title
		$pdf->SetFont("Times","U","14");
		$pdf->SetX(95);
		$pdf->Cell(10,8,"LOAN STATEMENT FOR",0,1,"C");
		$pdf->SetX(95);
		$pdf->Cell(10,8,"".strtoupper($name)." - ID: ".$idno." - PHONE: ".$phone."",0,2,"C");
//set initial y axis position per page
$y_axis_initial = 25;
$row_height = 8;
//print column titles
$pdf->SetY($y_axis_initial);
$pdf->SetFont("","B","12");		
$pdf->Cell(7,8,"Sr.",1,0,"C",FALSE);
$pdf->Cell(18,8,"To Pay/=",1,0,"C",FALSE);
$pdf->Cell(30,8,"Date Out",1,0,"C",FALSE);
$pdf->Cell(30,8,"Date Expected",1,0,"C");
$pdf->Cell(18,8,"Paid/=",1,0,"C",FALSE);
$pdf->Cell(30,8,"Date In",1,0,"C",FALSE);
$pdf->Cell(12,8,"Bal/=",1,0,"C",FALSE);
$pdf->Cell(43,8,"Status",1,0,"C",FALSE);

$y_axis = $y_axis_initial + $row_height;

//initialize counter
$i = 0;

//Set maximum rows per page
$max = 25;

//Set Row Height
$row_height = 8;

$sr = 1; //serial number
$data = array();
$sql = "SELECT l.amount As loanamount, rate, period, pp.amount As amountpaid, balance, dateout, dateexpected, l.balance, comment, l.status, 
datein FROM loans l JOIN pesapi_payment pp ON pp.id = l.did JOIN loan_terms lt ON lt.tid = l.tid WHERE l.uid=?";
$stmt = $conn->prepare($sql);
if($stmt->execute(array($_SESSION['uid'])))
{
 while($row = $stmt -> fetch())
 {
   $data[] = $row;    
}
}

foreach($data as $row)
{
$pdf->SetFillColor(255,255,255);
$pdf->SetFont("","","11");	
	//If the current row is the last one, create new page and print column title
	if ($i == $max)
	{
		$pdf->AddPage();        
	   
		//print column titles for the current page
		$pdf->SetY($y_axis_initial);
		//$pdf->SetX(25);
        $pdf->SetFont("","B","12");		
		$pdf->Cell(7,8,"Sr.",1,0,"C",FALSE);
		$pdf->Cell(18,8,"To Pay/=",1,0,"C",FALSE);
		$pdf->Cell(30,8,"Date Out",1,0,"C",FALSE);
		$pdf->Cell(30,8,"Date Expected",1,0,"C");
		$pdf->Cell(18,8,"Paid/=",1,0,"C",FALSE);
		$pdf->Cell(30,8,"Date In",1,0,"C",FALSE);
		$pdf->Cell(12,8,"Bal/=",1,0,"C",FALSE);
		$pdf->Cell(43,8,"Status",1,0,"C",FALSE);
		
		//Go to next row
		$y_axis = $y_axis + $row_height;
		
		//Set $i variable to 0 (first row)
		$i = 0;
	}	
    $lnamount = $row['loanamount'] + ($row['loanamount'] * (($row['rate']/100) * ($row['period']/7)));
    $dtout = $row['dateout'];
    $dtexpt = $row['dateexpected'];
    $amount = $row['amountpaid'];
    $dtin = $row['dateout'];
    $bal = $row['balance'];
    $state = $row['comment'].' | '.$row['status'];

	$pdf->SetY($y_axis);
	//$pdf->SetX(25);	
	$pdf->Cell(7,8,$sr,1,0,'C',1);
	$pdf->Cell(18,8,$lnamount,1,0,'C',1);
	$pdf->Cell(30,8,$dtout,1,0,'C',1);
	$pdf->Cell(30,8,$dtexpt,1,0,'C',1);
	$pdf->Cell(18,8,$amount,1,0,'C',1);
	$pdf->Cell(30,8,$dtin,1,0,'C',1);
	$pdf->Cell(12,8,$bal,1,0,'C',1);
	$pdf->Cell(43,8,$state,1,0,'C',1);

	//Go to next row
	$y_axis = $y_axis + $row_height;
    $i = $i + 1;
    
    $sr = $sr + 1; //next serial
}
//var_dump($pdf);	
$stmt = null;
//Send file
$pdf->Output();
//} 
?>
