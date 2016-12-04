
 <?php
	$rs=mysql_query("SELECT * FROM `document` WHERE did in(SELECT ogid from document where type='review') order by doctime desc") or die(mysql_error());	
 if(mysql_num_rows($rs)==0){echo "No results  ";}
?>
<style type="text/css">
<!--
.style2 {font-size: 12}
-->
</style>
<style type="text/css" title="currentStyle">
			@import "datatable/media/css/demo_page.css";
			@import "datatable/media/css/demo_table.css";
</style>

<script type="text/javascript" language="javascript" src="datatable/media/js/jquery.js"></script>
<script type="text/javascript" language="javascript" src="datatable/media/js/jquery.dataTables.js"></script>
<center><p><strong><h4>List Documents</h4></strong></p></center>
<div class="table-responsive">
  <table class="table table-striped table-bordered table-hover" id="dataTables-example" width="100%" border="1">
    <thead>
                                        <tr bgcolor="">
         <td bgcolor="grey" width="103" ><font color="#000000">Document Name</font></td>
                  <td bgcolor="grey" width="103" ><font color="#000000">Document Barcode</font></td>
                                   <td bgcolor="grey" width="103"><font color="#000000">Document Time</font></td>
                                   <td bgcolor="grey" width="103"><font color="#000000">View</font></td>
                                   
  
    </tr>
    </thead>
    <tbody><?php  
while($row = mysql_fetch_array($rs)){
		$name = $row['name'];
		$surname = $row['doctime'];

	
	 echo "<tr><td>".$name."</td><td>".$surname."</td><td>".$row['barcode']."</td><td><a href='index.php?page=diff/viewdiff.php&id=$row[did]' onclick='return confirm(\"Are you sure you ?\")'>[click to view]</a></font></td></tr>";
  
    
   }
	
?>        </tbody> </tr></table></center>


    <script>
    $(document).ready(function() {
        $('#dataTables-example').dataTable();
    });
    </script><br>
<br>
<br>
<br>
<br>

</div>
