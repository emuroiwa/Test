 <?php
 if($_SESSION['access']=="3"){
	 $level=1;
	 }
	  if($_SESSION['access']=="4"){
	 $level=2;
	 }
	 echo $level;
 $rs=mysql_query("select * from document where   type='orginal' and level ='$level' order by doctime desc") or die(mysql_error());	
	  ?>
<center><h4>List of original documents</h4><table width="100%" class="table table-striped table-bordered table-hover" >
					  <tr> 
                                   
                                   <td bgcolor="grey" width="103" ><font color="#000000">Document Name</font></td>
                                   <td bgcolor="grey" width="103"><font color="#000000">Document Time</font></td>
                                   <td bgcolor="grey" width="103"><font color="#000000">View</font></td>
                                   
                                   
                                  
                                  
								 
<?php  
while($row = mysql_fetch_array($rs)){
		$name = $row['name'];
		$surname = $row['doctime'];
	
	 echo "<tr><td>".$name."</td><td>".$surname."</td><td><a href='index.php?page=ogalldocu.php&id=$row[user]' onclick='return confirm(\"Are you sure you ?\")'>[click to view]</a></font></td></tr>";
  
    
   }
	
?>        </tr></table></center>