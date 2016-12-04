<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body>
<?php

$sql="update document set level='2',reviewtime=now() where did='$_GET[id]'";
//echo $sql;exit;
 mysql_query($sql);

 			 WriteToLog("Good Day a document has been sent through from the manager for your approval. Login to check",$_SESSION['username']);
			    if($_SESSION['access']="2"){
$msg="Good Day a document has been sent through from the manager for your approval. Login to check";

	 }
	  if($_SESSION['access']="3"){
	$msg="Good Day a document has been Logged from the manager. Login to check";


	 }
			 
			 
		  $query = mysql_query("SELECT * FROM users where access='4' ");
while($rw = mysql_fetch_array($query, MYSQL_ASSOC)){
 
	//SendEmail("Telecel","Document Upload"," $name Document Upload",$msg,$rw['email']);
		// SendSMS($msg,$rw['account'],"Telecel");

 }
  ?>
  <script language="javascript">
  alert("Updated............")
	window.location='index.php?page=og.php'
  </script>
  <?php
  

  
?>
