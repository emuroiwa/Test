<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body>
<?php
if(isset($_POST['Submit'])){
$sql="update document set level='2',review='$_POST[solution]',reviewtime=now() where did='$_GET[id]'";
//echo $sql;exit;
 mysql_query($sql);

 			 //WriteToLog("Deleted client ID $_REQUEST[id]",$_SESSION['username']);
			    if($_SESSION['access']="2"){
$msg="Good Day a document has been sent through from the manager for your approval. Login to check";

	 }
	  if($_SESSION['access']="3"){
	$msg="Good Day a document has been sent Back to you from the QO. Login to check";


	 }
			 
		  $query = mysql_query("SELECT * FROM users where access='4' ");
while($rw = mysql_fetch_array($query, MYSQL_ASSOC)){
 
	SendEmail("Telecel","Document Upload"," $name Document Upload",$msg,$rw['email']);
		 SendSMS($msg,$rw['account'],"Telecel");

 }

  ?>
  <script language="javascript">
  alert("Updated............")
	window.location='index.php?page=og.php'
  </script>
  <?php
  
}
  
?>

<form action="" method="post" name="qualification_form" onSubmit="MM_validateForm('department','','R');return document.MM_returnValue">
<table width="100%" border="1" align="center" class="table table-bordered table-hover">
 
 
      <tr>
        <td><div align="center"><span class="style7">Review Document</span></div></td>
       
      </tr>
      
    </table> 

 <table width="100%">
</table>



  
  <table width="100%" align="center">
<tr>
  <td width="27%"> <span class="style1 style9">Review</span></td>
  <td width="73%">
   <textarea name="solution" required rows="5" cols="100"></textarea></td>
</tr>
      
          
</td></tr>

<tr><td colspan="2"  align="center"><input type="submit" name="Submit" size="30"  value="Save"/></td>
</tr>
</table>
</form>
</body>
</html>