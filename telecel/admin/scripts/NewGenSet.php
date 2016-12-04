
<?php
//include 'opendb.php';
if(isset($_POST['Submit'])){
$department = clean($_POST['department']); 
/*
   if(ifexisits('district','district','$department') == 1){
	   msg('Department already in use');
	   link('index.php?page=dept.php'); }  
	   */
	   
    $rs1 = mysql_query("select * from site where name = '$department'");
   $rw = mysql_num_rows($rs1);
   if($rw == 1){
   ?>
  <script language="javascript">
 alert("site already in use");

  </script>
  <?php
  exit;
 
 }

$result = mysql_query("INSERT INTO genset(name,sitedate,rate,units)VALUES('$department',now(),'$_POST[rate]','$_POST[fuel]')") or die (mysql_error());
if ($result )
{
 ?>
<script language="javascript">
 alert("Successfully Saved");
</script>
<?php
				 }
			 else
			  {
			      $msg= "Error Occured";
		}	   
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="staff_infor.css" rel="stylesheet" type="text/css" />
<title>Untitled Document</title>
<style type="text/css">
<!--
.style1 {font-size: 12px}
-->
</style>



<body>

<form action="" method="post" name="qualification_form" >
<table width="100%" border="0" align="center" style="border-bottom:3px solid #000000;" class="table table-bordered table-hover">
 
 
      <tr>
        <td><div align="center"><span class="style7">Add New Genset
        </span></div></td>
       
      </tr>
      
    </table> 

 <table width="100%">
</table>



  
  <table width="100%" align="center">
  

<tr>
  <td width="27%"> <span class="style1 style9">Genset Serial</span></td>
  <td width="73%">
    <input type="text" name="department" id="department" size="30"  required="required"  /></td>
</tr><tr>
  <td width="27%"> <span class="style1 style9">Initial Fuel</span></td>
  <td width="73%">
    <input type="number" name="fuel" id="fuel" size="30" min="0"   step="0.01" required="required"  /></td>
</tr>
      
 <tr>
  <td width="27%"> <span class="style1 style9">Consumption Rate</span></td>
  <td width="73%">
    <input type="number" min="0" name="rate" id="rate" size="30"   step="0.01" required="required"  />Per Hour</td>
</tr>         
</td></tr>

<tr><td colspan="2"  align="center"><input type="submit" name="Submit" size="30"  value="Save"/></td>
</tr>
</table>
</form>

</body>

</html>
