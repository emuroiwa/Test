 <style type="text/css" title="currentStyle">
			@import "datatable/media/css/demo_page.css";
			@import "datatable/media/css/demo_table.css";
</style>
	  <?php $rs=mysql_query("select * from users where access!=1 order by name asc,surname") or die(mysql_error());	
	  ?>
<center><h4>List of system users</h4><table width="65%" class="table table-striped table-bordered table-hover" id="dataTables-example" style="border:1px solid #000000" border="1" bordercolor="#000000" >
					  <tr> 
                                   
                                   <td bgcolor="grey" width="80" style='border-style:outset;'><font color="#000000">Name</font></td>
                                   <td bgcolor="grey" width="103" style='border-style:outset;'><font color="#000000">Surname</font></td>
                                   <td bgcolor="grey" width="103"style='border-style:outset;'><font color="#000000">Delete</font></td>

                                   
                                  
                                  
								 
<?php  
while($row = mysql_fetch_array($rs)){
		$name = $row['name'];
		$surname = $row['surname'];
		
		//$nationalid = $row['idnumber'];
		//$state= $row['state'];
			$class = $row['name'];
		$level= $row['level'];
		$id= $row['id'];
		
if($row['access']==2){$a="edit1.php";}
if($row['access']>=3){$a="edit.php";}
	 echo "<tr><td style='border-style:outset;'>".$name."</td><td style='border-style:outset;'>".$surname."</td><td style='border-style:outset;'><a href='index.php?page=delete_user.php&id=$id' onclick='return confirm(\"Are you sure you want to delete?\")'>[click to delete]</a></font></td></tr>";
  
    
   }
	
?>        </tr></table></center>
 
    <script>
    $(document).ready(function() {
        $('#dataTables-example').dataTable();
    });
    </script>