 <style type="text/css" title="currentStyle">
      @import "datatable/media/css/demo_page.css";
      @import "datatable/media/css/demo_table.css";
</style>
 <?php
	$rs=mysql_query("select * from site order by units desc") or die(mysql_error());	
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


<center><p><strong><h4>List Sites</h4></strong></p></center>

<table width="100%"  style="border:1px solid #000000" border="1" bordercolor="#000000" >
    <thead>
                                        <tr bgcolor="">
         <td bgcolor="grey" width="3" ><font color="#000000">Site</font></td>  
         <td bgcolor="grey" width="103" ><font color="#000000">Rate Per Hour</font></td>
                                   <td bgcolor="grey" width="103"><font color="#000000">Units Level</font></td>
                                  
                                   
  
    </tr>
    </thead>
    <tbody><?php  
	$a=1;
while($row = mysql_fetch_array($rs)){
		$units = $row['units'];
		$name = $row['name'];
		$rate = $row['rate'];
  if($units<"30"){

    $c='red';
  } if($units>"31" and $units<"50"){

    $c='orange';
  }
   if($units>"51"){

    $c='green';
  }

	 echo "<tr bgcolor='$c'><td>".$name."</td><td>".$rate."</td><td>".$units."</td></tr>";
  $a++;
    
   }
	
?>        </tbody> </tr></table></center>

<hr>
    <script>
    $(document).ready(function() {
        $('#dataTables-example').dataTable();
    });
    </script><br>
<br>
<br>
<br>
<br>

 <?php
  $rs=mysql_query("select * from genset order by units desc") or die(mysql_error());  
 if(mysql_num_rows($rs)==0){echo "No results  ";}
?>
<style type="text/css">
<!--
.style2 {font-size: 12}
-->
</style>
<center><p><strong><h4>List GenSets</h4></strong></p></center>
<div class="table-responsive">

<table width="100%" style="border:1px solid #000000" border="1" bordercolor="#000000" >
    <thead>
                                        <tr bgcolor="">
         <td bgcolor="grey" width="3" ><font color="#000000">Site</font></td>  
         <td bgcolor="grey" width="103" ><font color="#000000">Rate Per Hour</font></td>
                                   <td bgcolor="grey" width="103"><font color="#000000">Units Level</font></td>
                                  
                                   
  
    </tr>
    </thead>
    <tbody><?php  
  $a=1;
while($row = mysql_fetch_array($rs)){
    $units = $row['units'];
    $name = $row['name'];
    $rate = $row['rate'];
  if($units<"30"){

    $c='red';
  } if($units>"31" and $units<"50"){

    $c='orange';
  }
   if($units>"51"){

    $c='green';
  }

   echo "<tr bgcolor='$c'><td>".$name."</td><td>".$rate."</td><td>".$units."</td></tr>";
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


