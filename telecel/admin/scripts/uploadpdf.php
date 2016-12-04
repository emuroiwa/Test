<html>
	<head>
		<title>Read pdf php</title>
	</head>
    
     
	  <?php $rs=mysql_query("select * from document where  user='$_SESSION[username]' and type='orginal' order by doctime desc") or die(mysql_error());	
	  ?>
<center><h4>List of original documents</h4><table width="100%" class="table table-striped table-bordered table-hover" >
					  <tr> 
                                   
                                   <td bgcolor="grey" width="103" ><font color="#000000">Document Name</font></td>        
                                         
                                   <td bgcolor="grey" width="103" ><font color="#000000">Document Barcode</font></td>
                                   <td bgcolor="grey" width="103"><font color="#000000">Document Time</font></td>
                                   <td bgcolor="grey" width="103"><font color="#000000">View</font></td>
                                   
                                   
                                  
                                  
								 
<?php  
while($row = mysql_fetch_array($rs)){
		$name = $row['name'];
		$surname = $row['doctime'];
		$did = $row['did'];
	
	 echo "<tr><td>".$name."</td><td>".$row['barcode']."</td><td>".$surname."</td><td><a href='index.php?page=alldocu.php&id=$did' onclick='return confirm(\"Are you sure you ?\")'>[click to view]</a></font></td></tr>";
  
    
   }
	
?>        </tr></table></center>
 
    <hr>
    <h4>Upload Document</h4>
    
    
	<form method="post" enctype="multipart/form-data">
		<table align="center" border="1" bgcolor="#CCCCCC" class="table table-striped table-bordered table-hover">
			<Tr>
				<td>BarCode</td>
				<td><input type="text" name="barcode" required/></td>
			</Tr>
            	<Tr>
				<td>Name Of File</td>
				<td><input type="text" name="name" required/></td>
			</Tr>
            <Tr>
				<td>Choose Your File</td>
				<td><input type="file" name="file" required/></td>
			</Tr>
			<tr>
				<td align="center" colspan="2"><input type="submit" value="Read PDF" name="readpdf"/></td>
			</tr>
		</table>
	</form>
</html>


<?php
//first include an external class.
require('class.pdf2text.php');
extract($_POST);
if(isset($readpdf))
{
	//check the types of file
	if($_FILES['file']['type']=="application/pdf")
	{
	$a = new PDF2Text();
	$a->setFilename($_FILES['file']['tmp_name']); 
	$a->decodePDF();
	//echo $a->output(); 
	
	             $fn = $_POST['name'].$_FILES['file']['name'];
				 	 WriteToLog("Document uploaded",$_SESSION['username']);
					 		  $query = mysql_query("SELECT * FROM users where access='3' ");
while($rw = mysql_fetch_array($query, MYSQL_ASSOC)){
SendEmail("Telecel","Document System","Document Upload","A lab tech has uploaded a document Please Login to view the document",$rw['email']);
//SendSMS("A lab tech has uploaded a document Please Login to view the document",$rw['idnumber'],"Telecel");
}
				 SetDocu($_SESSION['username'],"documents/".$fn,$a->output(),$_POST['name'],"orginal",$_POST['barcode'],$_POST['version1']);
			    move_uploaded_file($_FILES['file']['tmp_name'],"documents/".$fn);
					echo "<center><p style='color:green;text-align:center'>$_POST[name] $_POST[version] Has Been Created</p>";
echo ("<SCRIPT LANGUAGE='JavaScript'> window.alert('$_POST[name] Has Been Created')
		 window.location='index.php?page=uploadpdf.php'
		 	</SCRIPT>");  
			
	}
	//if file types is not pdf
	else
	{
	echo "<p style='color:red;text-align:center'>Wrong file format</p>";
	}
}	

?>