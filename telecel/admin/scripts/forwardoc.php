
<?php

$sql="update document set level='2',reviewtime=now() where did='$_GET[id]'";
//echo $sql;exit;
 mysql_query($sql);


	$msg="Good Day a document has been Logged from the manager. Login to check";


			 
		  $query = mysql_query("SELECT * FROM users where access='4' ");
while($rw = mysql_fetch_array($query, MYSQL_ASSOC)){
 
SendEmail("Telecel","Document System","Document Upload","The manager has uploaded a document Please Login to view the document",$rw['email']);
//SendSMS("The manager has uploaded a document Please Login to view the document",$rw['idnumber'],"Telecel");

 }

  ?>
  <script language="javascript">
  alert("Updated............")
	window.location='index.php?page=ogalldocu.php'
  </script>
  <?php
  

  
?>

