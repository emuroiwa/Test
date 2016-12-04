
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 <!-- Include Core Datepicker Stylesheet -->
<link rel="stylesheet" href="ui.datepicker.css" type="text/css" media="screen" title="core css file" charset="utf-8" />


<!-- Include jQuery -->
<script src="jquery.js" type="text/javascript" charset="utf-8"></script>

<!-- Include Core Datepicker JavaScript -->
<script src="ui.full_datepicker.js" type="text/javascript" charset="utf-8"></script>

<!-- Attach the datepicker to dateinput after document is ready -->
<script type="text/javascript" charset="utf-8">
jQuery(function($){
$("#date").datepicker();
});
</script>
<script type="text/javascript" charset="utf-8">
jQuery(function($){
$("#date1").datepicker();
});
</script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title></title>
<meta name="" content="" />
<meta name="keywords" content="" />
<meta name="description" content="" />
<style type="text/css">
<!--
.style1 {font-size: 12px}
.style2 {font-size: 14px}
.style4 {font-size: 12px; font-weight: bold; }
-->
</style>
</head>
<body>
<div align="center">
	<div class="main_div">
		<div class="main">
			<div class="main_site">
			  <div class="header">Report Of Documents Being Reviewed By The Manager<br />

					<h1>
					 <center> <table width="70%" border="0" >
					   
					<form action="" method="post" onSubmit="return ValidateForm()" name="frmSample">
					<tr><td width="216">Start Date</td>
					<td width="224">
					  <input type="text" name="date" id="date" />				   </td>
					<td width="176">End Date</td>
					<td width="304">
                    <input type="text" name="date1" id="date1" />
					<td width="6%"><input name="Submit" type="submit" value="Search" /></td></tr>
					</form>
					</table>
				  <br />
</h1>
					
					 
								 
                                    
						
                      <?php
					 
	  if (isset($_POST['Submit'])){
		  ?> <table width="100%" border="1">
					  <tr bgcolor="white"> 
                     <td bgcolor="grey" width="103"><font color="#000000">Authour</font></td>
                              <td bgcolor="grey" width="103" ><font color="#000000">Document Name</font></td>
                                   <td bgcolor="grey" width="103"><font color="#000000">Document Time</font></td>
                                   <td bgcolor="grey" width="103"><font color="#000000">View</font></td>
   								 <?php
	
	  $date =$_POST['date'];
	  $date1 =$_POST['date1'];
	   $nguva=date('d/m/Y');
	  if($date == '' OR $date1 == ''){
	  	 echo ("<SCRIPT LANGUAGE='JavaScript'> window.alert('Enter All dates fields')
		  javascript:history.go(-1)
		 	</SCRIPT>");  
		  }
	  if($date > $date1)
{
echo ("<SCRIPT LANGUAGE='JavaScript'> window.alert('Invalid Ranges')
		 javascript:history.go(-1)
		 	</SCRIPT>"); 
			exit; 
}
elseif($date1 < $nguva)
{
echo ("<SCRIPT LANGUAGE='JavaScript'> window.alert('Invalid Range')
		 javascript:history.go(-1)
		 	</SCRIPT>");  
			}
			else
{
	  $result ="";
	 $result = mysql_query("select * from document where level='1' AND doctime  BETWEEN '$date' AND '$date1' order by doctime desc  ")or die(mysql_query());
		 
	   if(!$result)
{
	die( "\n\ncould'nt send the query because".mysql_error());
	exit;
}
	$rows = mysql_num_rows($result);
	if($rows==0)
 {
 	echo("<SCRIPT LANGUAGE='JavaScript'> window.alert('No report for this period')
		  javascript:history.go(-1)
		 	</SCRIPT>");  
			exit;

 }
 	
	
  while($row = mysql_fetch_array($result, MYSQL_ASSOC))
	  	  
{

		$name=$row['name']." ".$row['surname'];
		
echo "<tr><td>{$row['user']}</td><td>{$row['name']}</td><td>{$row['doctime']}</td><td><a href='index.php?page=viewdocu.php&id=$row[did]' onclick='return confirm(\"Are you sure you ?\")'>[click to view]</a></font></td></tr>";
}
}

}
?>        </tr></table>     

							</div>
			</div>
	  </div>
  </div>

	
</div>
