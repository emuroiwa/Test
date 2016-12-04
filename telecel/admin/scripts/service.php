
<?php
//include 'opendb.php';
if(isset($_POST['Submit'])){
$gen = $_POST['gen']; 

/*
   if(ifexisits('district','district','$department') == 1){
     msg('Department already in use');
     link('index.php?page=dept.php'); }  
     */ 
     $oldlevel=GetIntial($_POST['gen'],"site");
     $rate=GetRate($_POST['gen'],"site");
     $topup = $_POST['topup']*$rate; 
 $newlevel=$oldlevel-$topup;

   if($newlevel>$oldlevel){
   ?>
  <script language="javascript">
 alert("Rate is impossible");

  </script>
  <?php
  exit;
 
 }
$result = mysql_query("INSERT INTO `topup` (`oldlevel`, `topuplevel`, `topupdate`, `item`, `type`) VALUES ('$oldlevel', '$topup', now(), '$gen', 'units')") or die (mysql_error());
mysql_query("UPDATE `site` SET `units`='$newlevel' WHERE (`sid`='$gen') ") or die (mysql_error());
if ($result )
{

 echo "<font color='green'><h3>Intial Level Where $oldlevel , Topup was $topup , New Level is $newlevel </h3></font>";
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
        <td><div align="center"><span class="style7">Service
        </span></div></td>
       
      </tr>
      
    </table> 

 <table width="100%">
</table>



  
  <table width="100%" align="center">
  
  <tr>
            <td>GenSet</td>
            <td>
             <?php 

 
$sql="select * from site";
$rez=mysql_query($sql);
?>
<select name='gen' id ='gen' required>
<option value="">--- Site ---</option>
<?php
while($row=mysql_fetch_array($rez,MYSQL_ASSOC))
{
 echo "<option value='{$row['sid']}'>{$row['name']}</option>"; 
}

?></span></select>           </td>
          </tr>

<tr>
  <td width="27%"> <span class="style1 style9">Topup </span></td>
  <td width="73%">
    <input type="number" name="topup" id="topup" size="30" min="0"  required="required"  /></td>
</tr>
      
 

<tr><td colspan="2"  align="center"><input type="submit" name="Submit" size="30"  value="Save"/></td>
</tr>
</table>
</form>

</body>

</html>
