<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Untitled Document</title>
</head>

<body>

	  <?php $rs=mysql_query("select * from document where   did='$_GET[id]' order by doctime desc") or die(mysql_error());	
	  ?>
      <?php  
while($row = mysql_fetch_array($rs)){
		$name = $row['name'];
		$surname = $row['doctime'];
		$document = $row['document'];
	
	 echo "<h3>$name</h3>";
	 echo "<iframe src='$document' width='100%'  height='600px' ></iframe>";
  
    
   }
	
?>        </tr></table></center>


</body>
</html>