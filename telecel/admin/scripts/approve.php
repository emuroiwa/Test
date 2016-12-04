<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body>
<?php
$sql="update document set level='approved',review='Approved',reviewtime=now() where did='$_GET[id]'";
//echo $sql;exit;
				 	 WriteToLog("Document Approved",$_SESSION['username']);

 mysql_query($sql);
  ?>
  <script language="javascript">
  alert("Approved")
location = 'index.php?page=og.php'  </script>
  <?php
  

  
?>
</body>
</html>