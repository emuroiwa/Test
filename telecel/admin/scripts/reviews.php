	  <?php
	  $td="";
	  $td2="";
	   if($_SESSION['access']="2"){
	 $level=1;
	 $xxx="Quality Officer";
	 }
	  if($_SESSION['access']="3"){
	 $level=2;
	 	 $xxx="Lab Officer";
$td='<td bgcolor="grey" width="103"><font color="#000000">Approve</font></td>';
$td2="<td><a href='index.php?page=approve.php&id=$row[did]&user=$row[user]' onclick='return confirm(\"Are you sure you ?\")'>[$xxx]</a></font></td>";
                                   
	 }
	   $rs=mysql_query("select * from document where  user='$_GET[id]' and level ='$level' order by doctime desc") or die(mysql_error());	
	  ?>.
<center><h4>List of reviewed documents</h4><table width="100%" class="table table-striped table-bordered table-hover" >
					  <tr> 
                                   
                                   <td bgcolor="grey" width="103" ><font color="#000000">Document Name</font></td>
                                   <td bgcolor="grey" width="103"><font color="#000000">Document Time</font></td>
                                   <td bgcolor="grey" width="103"><font color="#000000">Review</font></td>
                                   <td bgcolor="grey" width="103"><font color="#000000">Review Time</font></td>
                                   <td bgcolor="grey" width="103"><font color="#000000">Review</font></td>
                                   <?php echo $td;?>
                                   
                                  
                                  
								 
<?php  
while($row = mysql_fetch_array($rs)){
		$name = $row['name'];
		$surname = $row['doctime'];
		$review = $row['review'];
		$reviewtime = $row['reviewtime'];
	
	 echo "<tr><td>".$name."</td><td>".$surname."</td><td>".$review."</td><td>".$reviewtime."</td><td><a href='index.php?page=uploadpdf2.php&id=$row[did]' onclick='return confirm(\"Are you sure you ?\")'>[click to view]</a></font></td></tr>";
  
    
   }
	
?>        </tr></table></center>
 