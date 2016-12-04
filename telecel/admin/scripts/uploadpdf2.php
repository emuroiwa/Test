
 
    <hr>
    <h4>ReUpload Document</h4>
    
  
	<form method="post" enctype="multipart/form-data">
		<table align="center" border="1" bgcolor="#CCCCCC" class="table table-striped table-bordered table-hover">
			<!--<Tr>
				<td>BarCode</td>
				<td><input type="text" name="barcode" required/></td>
			</Tr>-->
          <!--  	<Tr>
				<td>Name Of File</td>
				<td><input type="text" name="name" required/></td>
			</Tr>-->
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
	
	             $fn = "review".$_FILES['file']['name'];
				 
				 
				 					 		  $query = mysql_query("SELECT * FROM users where access='3' ");
while($rw = mysql_fetch_array($query, MYSQL_ASSOC)){
SendEmail("Telecel","Document System","Document Upload","A lab tech has uploaded a document Please Login to view the document",$rw['email']);
//SendSMS("A lab tech has uploaded a document Please Login to view the document",$rw['idnumber'],"Telecel");
}
				 
				 SetDocuReview($_SESSION['username'],"documents/".$fn,$a->output(),"review","review",$_GET['id']);
			    move_uploaded_file($_FILES['file']['tmp_name'],"documents/".$fn);
					echo "<center><p style='color:green;text-align:center'>$_POST[name] Has Been Created</p>";
echo ("<SCRIPT LANGUAGE='JavaScript'> window.alert('$_POST[name] Has Been Created')
		 window.location='index.php?page=uploadpdf2.php'
		 	</SCRIPT>");  
			
	}
	//if file types is not pdf
	else
	{
	echo "<p style='color:red;text-align:center'>Wrong file format</p>";
	}
}	

?>