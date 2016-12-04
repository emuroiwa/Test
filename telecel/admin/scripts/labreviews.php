
 <?php
	$rs=mysql_query("select * from document where  user='$_SESSION[username]' and level='review' order by doctime desc") or die(mysql_error());	
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
         <td bgcolor="grey" width="3" ><font color="#000000">Document Version</font></td>  <td bgcolor="grey" width="103" ><font color="#000000">Document Name</font></td>
         <td bgcolor="grey" width="103" ><font color="#000000">Document Barcode</font></td>
                                   <td bgcolor="grey" width="103"><font color="#000000">Document Time</font></td>
                                   <td bgcolor="grey" width="103"><font color="#000000">Review</font></td>
                                   <td bgcolor="grey" width="103"><font color="#000000">ReUpload</font></td>
                                   
  
    </tr>
    </thead>
    <tbody><?php  
	$a=1;
while($row = mysql_fetch_array($rs)){
		$name = $row['name'];
		$surname = $row['doctime'];
		$review = $row['review'];
	
	 echo "<tr><td>".$a."</td><td>".$name."</td><td>".$row['barcode']."</td><td>".$surname."</td><td>".$review."</td><td><a href='index.php?page=uploadpdf2.php&id=$row[did]' onclick='return confirm(\"Are you sure you ?\")'>[click to reupload]</a></font></td></tr>";
  $a++;
    
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
