 <?php
 if($_SESSION['access']=="3"){
	 $level=1;
	 }
	  if($_SESSION['access']=="4"){
	 $level=2;
	 }
	 echo $level;
 $rs=mysql_query("select * from document where   type='review' and level ='$level' order by doctime desc") or die(mysql_error());	
	  ?>
<center><h4>List of original documents</h4><table width="100%" class="table table-striped table-bordered table-hover" >
					  <tr> 
                                   
                                   <td bgcolor="grey" width="103" ><font color="#000000">Document Name</font></td>
                                   <td bgcolor="grey" width="103"><font color="#000000">Document Time</font></td>
                                   <td bgcolor="grey" width="103"><font color="#000000">View</font></td>
                                   <td bgcolor="grey" width="103"><font color="#000000">Send To</font></td>
                                   <?php echo $td;?>
                                   
                                  
                                  
								 
<?php  
while($row = mysql_fetch_array($rs)){
		$name = $row['name'];
		$surname = $row['doctime'];
	  if($_SESSION['access']=="4"){
		  $td2="<td><a href='index.php?page=approve.php&id=$row[did]&user=$row[user]' onclick='return confirm(\"Are you sure you ?\")'>[Approve]</a></font></td>";

		  }
	 echo "<tr><td>".$name."</td><td>".$surname."</td><td><a href='index.php?page=viewdocu.php&id=$row[did]' onclick='return confirm(\"Are you sure you ?\")'>[click to view]</a></font></td><td><a href='index.php?page=sendreview.php&id=$row[did]&user=$row[user]' onclick='return confirm(\"Are you sure you ?\")'>[$xxx]</a></font></td>$td2</tr>";
  
    
   }
	
?>        </tr></table></center>