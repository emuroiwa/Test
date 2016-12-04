<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body>
<?php
if(isset($_POST['Submit'])){
$sql="update document set level='review',review='$_POST[solution]',reviewtime=now() where did='$_GET[id]'";
//echo $sql;exit;
 mysql_query($sql);

	$msg="Good Day a document has been sent Back to you from the QO. Login to check";
	 WriteToLog("Good Day a document has been sent Back to you from the QO. Login to check",$_SESSION['username']);


	 
			 
	 $data=GetUserDetails($_GET['user']);
$email=$data['b'];
$name=$data['a'];
$cell=$data['c'];
/*echo $cell;

exit;*/

SendEmail("Telecel","Document Upload"," $name Document Upload",$msg,$email);
		SendSMS($msg,$cell,"Telecel");
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