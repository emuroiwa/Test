
<?php
	  ///*******************************************************************************************************************************************************************
	  ///*******************************************************************************************************************************************************************
	  ///*******************************************************FUNCTIONS PAGE**********************************************************************************************
	  ///*******************************************************ERNEST MUROIWA**********************************************************************************************
	  ///************************************************************Telecel***************************************************************************************************
	  ///*******************************************************************************************************************************************************************
	  ///*******************************************************************************************************************************************************************
	  ///*******************************************************************************************************************************************************************
include 'email/class.phpmailer.php';
include 'email/class.smtp.php';
$date = date('Y-m-d H:i:s');
$monthdays=date('t')."days";
		$dt = strtotime("$date + 1month");
		$nextmonth = date('d/m/Y',$dt);
 $Today = date('y:m:d');
       $new = date('l, F d, Y', strtotime($Today));
	   $month = date('F-Y');
			$vv = strtotime($month);
$nextmonth = date("F-Y", strtotime("+1 month", $vv));//echo $final;
$year = date('Y');
$time = date('m/d/Y - H:m:s');
///Get Connection***************************************************************************************************************************************************
 	 function getConnection() {
mysql_connect('localhost','root','');
mysql_select_db('Telecel');
  }

  		function GetIntial($id,$table){
  $lastdate = mysql_query("SELECT * FROM $table where sid='$id'")or die(mysql_query());
while($rw = mysql_fetch_array($lastdate, MYSQL_ASSOC)){
   return $rw['units'];
}
  
	}
	  		function GetRate($id,$table){
  $lastdate = mysql_query("SELECT * FROM $table where sid='$id'")or die(mysql_query());
while($rw = mysql_fetch_array($lastdate, MYSQL_ASSOC)){
   return $rw['rate'];
}
  
	}

  ///WriteToLog***************************************************************************************************************************************************
function SetDocu($user,$document,$text,$name,$type,$barcode,$version){
	global $time;
		 mysql_query("INSERT INTO `document` (`user`, `document`, `text`, `doctime`,`name`,`type`,`level`,`barcode`,`version`) VALUES ('$user', '$document', '$text', now(),'$name','$type','1','$barcode','$version')") or die(mysql_error());
		 $data=GetUserDetails($user);
$email=$data['b'];
$name=$data['a'];
$cell=$data['c'];
$msg="Good Day $name has uploaded a document at $time. Login to check";

//SendEmail("Telecel","Document Upload"," $name Document Upload",$msg,$email);
		// SendSMS($msg,$cell,"Telecel");
	 return true;
	}
	function SetDocuReview($user,$document,$text,$name,$type,$id){
	global $time;
	$bar =GetReviewCode($id);
		 mysql_query("INSERT INTO `document` (`user`, `document`, `text`, `doctime`,`name`,`type`,`level`,`barcode`,ogid) VALUES ('$user', '$document', '$text', now(),'$name','$type','1','$bar','$id')") or die(mysql_error());
		 $data=GetUserDetails($user);
$email=$data['b'];
$name=$data['a'];
$cell=$data['c'];
$msg="Good Day $name has uploaded a document at $time. Login to check";

//SendEmail("Telecel","Document Upload"," $name Document Upload",$msg,$email);
		// SendSMS($msg,$cell,"Telecel");
	 return true;
	}
	
	function GetReview($id){
  $lastdate = mysql_query("SELECT * FROM document where ogid='$id'")or die(mysql_query());

  return mysql_num_rows($lastdate)+1;
	}
		function GetReviewCode($id){
  $lastdate = mysql_query("SELECT * FROM document where did='$id'")or die(mysql_query());
while($rw = mysql_fetch_array($query, MYSQL_ASSOC)){
   return $rw['barcode'];
}
  
	}
		function GetText1($id){
  $lastdate = mysql_query("SELECT * FROM document where did='$id'")or die(mysql_query());
 while($rwdate = mysql_fetch_array($lastdate, MYSQL_ASSOC)){
  $lastmonth=$rwdate['text'];
   }
  return $lastmonth;
	}
		function GetReviewText($id){
  $lastdate = mysql_query("SELECT * FROM document where ogid='$id'")or die(mysql_query());
 while($rwdate = mysql_fetch_array($lastdate, MYSQL_ASSOC)){
  $lastmonth=$rwdate['text'];
   }
  return $lastmonth;
	}
function GetUserDetails($id){
		  $query = mysql_query("SELECT * FROM users where username='$id' ");
while($rw = mysql_fetch_array($query, MYSQL_ASSOC)){
   $out['a'] = $rw['name']." ".$rw['surname'];
    $out['b'] = $rw['email'];
    $out['c'] = $rw['account'];
 }
  return $out;
 
	}
	function GetUserDetailsAccess($id){
		  $query = mysql_query("SELECT * FROM users where access='$id' ");
while($rw = mysql_fetch_array($query, MYSQL_ASSOC)){
   $out['a'] = $rw['name']." ".$rw['surname'];
    $out['b'] = $rw['email'];
    $out['c'] = $rw['account'];
 }
  return $out;
 
	}	function GetEmails($id){
		  $query = mysql_query("SELECT * FROM users where access='$id' ");
while($rw = mysql_fetch_array($query, MYSQL_ASSOC)){
   $out['a'] = $rw['name']." ".$rw['surname'];
    $out['b'] = $rw['email'];
    $out['c'] = $rw['account'];
 }
  return $out;
 
	}
		function data() {
    $out['a'] = "abc";
    $out['b'] = "def";
    $out['c'] = "ghi";
    return $out;
}


function WriteToLog($log,$user){
	 $Today = date('y-m-d');
	 $time = date('m/d/Y - H:m:s');
		$myFile = "logs/$Today-LOG.txt";
$fh = fopen($myFile, 'a') or die("can't open file");
fwrite($fh, $time." - $user - ".$log."\n");
mysql_query("INSERT INTO `systemlogs` (`details`, `detailsdate`, `user`) VALUES ('$log', now(), '$user')");
fclose($fh);
return true;
	}
	  ///*******************************************************************************************************************************************************************

	function ValidateDate($date){ 
    if (preg_match("/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $date, $matches)) { 
        if (checkdate($matches[2], $matches[3], $matches[1])) { 
            return true; 
        } 
    } 
    return false; 
} 
		  ///*******************************************************************************************************************************************************************

function GetInstalmentMonth($purchaseDate,$NumberOfMonths){
		//$dt = strtotime($purchaseDate."+ ".$NumberOfMonths."month");
		$dt = strtotime("$purchaseDate +".$NumberOfMonths."month");
		$InstalmentMonth = date('F-Y',$dt);
		return $InstalmentMonth;
	
	}
		  ///*******************************************************************************************************************************************************************
		  function SendSMS($msgsms,$contact,$callerid){
// Bulk SMS's POST URL
$postUrl = "http://193.105.74.59/api/sendsms/xml";
// XML-formatted data
$xmlString =
"<SMS>
<authentification>
<username>TDInvestment</username>
<password>tS8ff1Cg</password>
</authentification>
";
$xmlString.="
<message>
<sender>".$callerid."</sender>
<text>".$msgsms."</text>
<recipients>
<gsm>".$contact."</gsm>
</recipients>
</message>";
$xmlString.="
</SMS>";
$fields = "XML=" . urlencode($xmlString);
$ch = curl_init($postUrl);
curl_setopt($ch, CURLOPT_URL, $postUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
curl_exec($ch);
curl_close($ch);
return true;
		  }
		  
		  function SendEmail($company,$type,$subject,$body,$email){

 $body1='<style type="text/css">
			
			html { background-color:#E1E1E1; margin:0; padding:0; }
			body, #bodyTable, #bodyCell, #bodyCell{height:100% !important; margin:0; padding:0; width:100% !important;font-family:Helvetica, Arial, "Lucida Grande", sans-serif;}
			table{border-collapse:collapse;}
			table[id=bodyTable] {width:100%!important;margin:auto;max-width:500px!important;color:#7A7A7A;font-weight:normal;}
			img, a img{border:0; outline:none; text-decoration:none;height:auto; line-height:100%;}
			a {text-decoration:none !important;border-bottom: 1px solid;}
			h1, h2, h3, h4, h5, h6{color:#5F5F5F; font-weight:normal; font-family:Helvetica; font-size:20px; line-height:125%; text-align:Left; letter-spacing:normal;margin-top:0;margin-right:0;margin-bottom:10px;margin-left:0;padding-top:0;padding-bottom:0;padding-left:0;padding-right:0;}
			.ReadMsgBody{width:100%;} .ExternalClass{width:100%;} 
			.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div{line-height:100%;} 
			table, td{mso-table-lspace:0pt; mso-table-rspace:0pt;}
			#outlook a{padding:0;} 
			img{-ms-interpolation-mode: bicubic;display:block;outline:none; text-decoration:none;} 
			body, table, td, p, a, li, blockquote{-ms-text-size-adjust:100%; -webkit-text-size-adjust:100%; font-weight:normal!important;}
			.ExternalClass td[class="ecxflexibleContainerBox"] h3 {padding-top: 10px !important;} 
			h1{display:block;font-size:26px;font-style:normal;font-weight:normal;line-height:100%;}
			h2{display:block;font-size:20px;font-style:normal;font-weight:normal;line-height:120%;}
			h3{display:block;font-size:17px;font-style:normal;font-weight:normal;line-height:110%;}
			h4{display:block;font-size:18px;font-style:italic;font-weight:normal;line-height:100%;}
			.flexibleImage{height:auto;}
			.linkRemoveBorder{border-bottom:0 !important;}
			table[class=flexibleContainerCellDivider] {padding-bottom:0 !important;padding-top:0 !important;}
			body, #bodyTable{background-color:#E1E1E1;}
			#emailHeader{background-color:#E1E1E1;}
			#emailBody{background-color:#FFFFFF;}
			#emailFooter{background-color:#E1E1E1;}
			.nestedContainer{background-color:#F8F8F8; border:1px solid #CCCCCC;}
			.emailButton{background-color:#205478; border-collapse:separate;}
			.buttonContent{color:#FFFFFF; font-family:Helvetica; font-size:18px; font-weight:bold; line-height:100%; padding:15px; text-align:center;}
			.buttonContent a{color:#FFFFFF; display:block; text-decoration:none!important; border:0!important;}
			.emailCalendar{background-color:#FFFFFF; border:1px solid #CCCCCC;}
			.emailCalendarMonth{background-color:#205478; color:#FFFFFF; font-family:Helvetica, Arial, sans-serif; font-size:16px; font-weight:bold; padding-top:10px; padding-bottom:10px; text-align:center;}			.emailCalendarDay{color:#205478; font-family:Helvetica, Arial, sans-serif; font-size:60px; font-weight:bold; line-height:100%; padding-top:20px; padding-bottom:20px; text-align:center;}
			.imageContentText {margin-top: 10px;line-height:0;}
			.imageContentText a {line-height:0;}
			#invisibleIntroduction {display:none !important;} 
			span[class=ios-color-hack] a {color:#275100!important;text-decoration:none!important;} 
			span[class=ios-color-hack2] a {color:#205478!important;text-decoration:none!important;}
			span[class=ios-color-hack3] a {color:#8B8B8B!important;text-decoration:none!important;}
		
			.a[href^="tel"], a[href^="sms"] {text-decoration:none!important;color:#606060!important;pointer-events:none!important;cursor:default!important;}
			.mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {text-decoration:none!important;color:#606060!important;pointer-events:auto!important;cursor:default!important;}


			
			@media only screen and (max-width: 480px){
			
				body{width:100% !important; min-width:100% !important;} 
				table[id="emailHeader"],
				table[id="emailBody"],
				table[id="emailFooter"],
				table[class="flexibleContainer"],
				td[class="flexibleContainerCell"] {width:100% !important;}
				td[class="flexibleContainerBox"], td[class="flexibleContainerBox"] table {display: block;width: 100%;text-align: left;}
				
				td[class="imageContent"] img {height:auto !important; width:100% !important; max-width:100% !important; }
				img[class="flexibleImage"]{height:auto !important; width:100% !important;max-width:100% !important;}
				img[class="flexibleImageSmall"]{height:auto !important; width:auto !important;}

	
				table[class="flexibleContainerBoxNext"]{padding-top: 10px !important;}

				
				table[class="emailButton"]{width:100% !important;}
				td[class="buttonContent"]{padding:0 !important;}
				td[class="buttonContent"] a{padding:15px !important;}

			}

		
			@media only screen and (-webkit-device-pixel-ratio:.75){
				
			}

			@media only screen and (-webkit-device-pixel-ratio:1){
				
			}

			@media only screen and (-webkit-device-pixel-ratio:1.5){
				
			}
			
			@media only screen and (min-device-width : 320px) and (max-device-width:568px) {

			}
			
		</style>
		
	</head>
	<body bgcolor="#E1E1E1" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">

		
		<center style="background-color:#E1E1E1;">
			<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable" style="table-layout: fixed;max-width:100% !important;width: 100% !important;min-width: 100% !important;">
				<tr>
					<td align="center" valign="top" id="bodyCell">

						
						<table bgcolor="#E1E1E1" border="0" cellpadding="0" cellspacing="0" width="500" id="emailHeader">

							
							<tr>
								<td align="center" valign="top">
									
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td align="center" valign="top">
												
											</td>
										</tr>
									</table>
									
								</td>
							</tr>
						

						</table>
						
						<table bgcolor="#FFFFFF"  border="0" cellpadding="0" cellspacing="0" width="500" id="emailBody">

						
							<tr>
								<td align="center" valign="top">
								
									<table border="0" cellpadding="0" cellspacing="0" width="100%" style="color:#FFFFFF;" bgcolor="#3498db">
										<tr>
											<td align="center" valign="top">
												
											
												<table border="0" cellpadding="0" cellspacing="0" width="500" class="flexibleContainer">
													<tr>
														<td align="center" valign="top" width="500" class="flexibleContainerCell">

														
															<table border="0" cellpadding="30" cellspacing="0" width="100%">
																<tr>
																	<td align="center" valign="top" class="textContent">
																		<h1 style="color:#FFFFFF;line-height:100%;font-family:Helvetica,Arial,sans-serif;font-size:35px;font-weight:normal;margin-bottom:5px;text-align:center;">'.$company.'</h1>
																		<h2 style="text-align:center;font-weight:normal;font-family:Helvetica,Arial,sans-serif;font-size:23px;margin-bottom:10px;color:#205478;line-height:135%;">'.$type.'</h2>
	
																	</td>
																</tr>
															</table>
															

														</td>
													</tr>
												</table>
												
											</td>
										</tr>
									</table>
									
								</td>
							</tr>
							
							<tr mc:hideable>
								<td align="center" valign="top">
									<!-- CENTERING TABLE // -->
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td align="center" valign="top">
												<!-- FLEXIBLE CONTAINER // --><!-- // FLEXIBLE CONTAINER -->
											</td>
										</tr>
									</table>
									<!-- // CENTERING TABLE -->
								</td>
							</tr>
							<!-- // MODULE ROW -->


							<!-- MODULE ROW // -->
							<tr>
								<td align="center" valign="top">
									<!-- CENTERING TABLE // -->
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr style="padding-top:0;">
											<td align="center" valign="top">
												<!-- FLEXIBLE CONTAINER // --><!-- // FLEXIBLE CONTAINER -->
											</td>
										</tr>
									</table>
									<!-- // CENTERING TABLE -->
								</td>
							</tr>
							<!-- // MODULE ROW -->


							<!-- MODULE ROW // -->
							<tr>
								<td align="center" valign="top">
									<!-- CENTERING TABLE // -->
									<table border="0" cellpadding="0" cellspacing="0" width="100%" bgcolor="#F8F8F8">
										<tr>
											<td align="center" valign="top">
												<!-- FLEXIBLE CONTAINER // -->
												<table border="0" cellpadding="0" cellspacing="0" width="500" class="flexibleContainer">
													<tr>
														<td align="center" valign="top" width="500" class="flexibleContainerCell">
															<table border="0" cellpadding="30" cellspacing="0" width="100%">
																<tr>
																	<td align="center" valign="top">

																		<!-- CONTENT TABLE // -->
																		<table border="0" cellpadding="0" cellspacing="0" width="100%">
																			<tr>
																				<td valign="top" class="textContent">
																				
																	Good Day,<br>
'.$body.'
<br>

																	
																		

																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
												<!-- // FLEXIBLE CONTAINER -->
											</td>
										</tr>
									</table>
									<!-- // CENTERING TABLE -->
								</td>
							</tr>
							<!-- // MODULE ROW -->


							<!-- MODULE ROW // -->
							<tr>
								<td align="center" valign="top">
									<!-- CENTERING TABLE // --><!-- // CENTERING TABLE -->
								</td>
							</tr>
							<!-- // MODULE ROW -->


							<!-- MODULE ROW // -->
							<tr>
								<td align="center" valign="top">
									<!-- CENTERING TABLE // -->
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td align="center" valign="top">
												<!-- FLEXIBLE CONTAINER // -->
												<table border="0" cellpadding="0" cellspacing="0" width="500" class="flexibleContainer">
													<tr>
														<td align="center" valign="top" width="500" class="flexibleContainerCell">

															<!-- CONTENT TABLE // --><!-- // CONTENT TABLE -->

														</td>
													</tr>
												</table>
												<!-- // FLEXIBLE CONTAINER -->
											</td>
										</tr>
									</table>
									<!-- // CENTERING TABLE -->
								</td>
							</tr>
							<!-- // MODULE ROW -->

							<!-- MODULE ROW // -->
							<tr>
								<td align="center" valign="top">
									<!-- CENTERING TABLE // -->
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td align="center" valign="top">
												<!-- FLEXIBLE CONTAINER // -->
												
												<!-- // FLEXIBLE CONTAINER -->
											</td>
										</tr>
									</table>
									<!-- // CENTERING TABLE -->
								</td>
							</tr>
							<!-- // MODULE ROW -->


							<!-- MODULE DIVIDER // -->
							<tr>
								<td align="center" valign="top">
									<!-- CENTERING TABLE // -->
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td align="center" valign="top">
												
														</td>
													</tr>
												</table>
												<!-- // FLEXIBLE CONTAINER -->
											</td>
										</tr>
									</table>
									<!-- // CENTERING TABLE -->
								</td>
							</tr>
							<!-- // END -->


							<!-- MODULE ROW // -->
							<tr>
								<td align="center" valign="top">
									<!-- CENTERING TABLE // -->
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td align="center" valign="top">
												<!-- FLEXIBLE CONTAINER // -->
												<table border="0" cellpadding="30" cellspacing="0" width="500" class="flexibleContainer">
													<tr>
														<td valign="top" width="500" class="flexibleContainerCell">

															<!-- CONTENT TABLE // --><!-- // CONTENT TABLE -->

														</td>
													</tr>
												</table>
												<!-- // FLEXIBLE CONTAINER -->
											</td>
										</tr>
									</table>
									<!-- // CENTERING TABLE -->
								</td>
							</tr>
							<!-- // MODULE ROW -->


							<!-- MODULE DIVIDER // -->
							<tr>
								<td align="center" valign="top">
									<!-- CENTERING TABLE // -->
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td align="center" valign="top">
											
											</td>
										</tr>
									</table>
									<!-- // CENTERING TABLE -->
								</td>
							</tr>
							<!-- // END -->


							<!-- MODULE ROW // -->
							<tr>
								<td align="center" valign="top">
									<!-- CENTERING TABLE // -->
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td align="center" valign="top">
												<!-- FLEXIBLE CONTAINER // -->
												<table border="0" cellpadding="30" cellspacing="0" width="500" class="flexibleContainer">
													<tr>
														<td valign="top" width="500" class="flexibleContainerCell">

															<!-- CONTENT TABLE // --><!-- // CONTENT TABLE -->

														</td>
													</tr>
												</table>
												<!-- // FLEXIBLE CONTAINER -->
											</td>
										</tr>
									</table>
									<!-- // CENTERING TABLE -->
								</td>
							</tr>
							<!-- // MODULE ROW -->


							<!-- MODULE DIVIDER // -->
							<tr>
								<td align="center" valign="top">
									<!-- CENTERING TABLE // -->
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td align="center" valign="top">
												<!-- FLEXIBLE CONTAINER // -->
												<table border="0" cellpadding="0" cellspacing="0" width="500" class="flexibleContainer">
													<tr>
														<td align="center" valign="top" width="500" class="flexibleContainerCell">
															<table class="flexibleContainerCellDivider" border="0" cellpadding="30" cellspacing="0" width="100%">
																<tr>
																	<td align="center" valign="top" style="padding-top:0px;padding-bottom:0px;">

																		

																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
												
											</td>
										</tr>
									</table>
								
								</td>
							</tr>
							
							<tr>
								<td align="center" valign="top">
								
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td align="center" valign="top">
									
											</td>
										</tr>
									</table>
								
								</td>
							</tr>
						
							<tr>
								<td align="center" valign="top">
								
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td align="center" valign="top">
												

																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											
											</td>
										</tr>
									</table>
								
								</td>
							</tr>
						
							<tr>
								<td align="center" valign="top">
									<!-- CENTERING TABLE // --><!-- // CENTERING TABLE -->
								</td>
							</tr>
							<!-- // MODULE ROW -->


							<!-- MODULE DIVIDER // -->
							<tr>
								<td align="center" valign="top">
									<!-- CENTERING TABLE // -->
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td align="center" valign="top">
												
														</td>
													</tr>
												</table>
												<!-- // FLEXIBLE CONTAINER -->
											</td>
										</tr>
									</table>
									<!-- // CENTERING TABLE -->
								</td>
							</tr>
							<!-- // END -->


							<!-- MODULE ROW // -->
							<tr>
								<td align="center" valign="top">
									<!-- CENTERING TABLE // -->
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td align="center" valign="top">
												<!-- FLEXIBLE CONTAINER // --><!-- // FLEXIBLE CONTAINER -->
											</td>
										</tr>
									</table>
									<!-- // CENTERING TABLE -->
								</td>
							</tr>
							<!-- // MODULE ROW -->


							<!-- MODULE DIVIDER // -->
							<tr>
								<td align="center" valign="top">
									<!-- CENTERING TABLE // -->
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td align="center" valign="top">
												<!-- FLEXIBLE CONTAINER // -->
												<table border="0" cellpadding="0" cellspacing="0" width="500" class="flexibleContainer">
													<tr>
														<td align="center" valign="top" width="500" class="flexibleContainerCell">
															
														</td>
													</tr>
												</table>
												<!-- // FLEXIBLE CONTAINER -->
											</td>
										</tr>
									</table>
									<!-- // CENTERING TABLE -->
								</td>
							</tr>
							<!-- // END -->


							<!-- MODULE ROW // -->
							<tr>
								<td align="center" valign="top">
									<!-- CENTERING TABLE // --><!-- // CENTERING TABLE -->
								</td>
							</tr>
							<!-- // MODULE ROW -->


							<!-- MODULE ROW // -->
							<tr>
								<td align="center" valign="top">
									
								</td>
							</tr>
							<!-- // MODULE ROW -->


							<!-- MODULE ROW // -->
							<tr>
								<td align="center" valign="top">
									<!-- CENTERING TABLE // -->
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td align="center" valign="top">
												<!-- FLEXIBLE CONTAINER // -->
												<table border="0" cellpadding="0" cellspacing="0" width="500" class="flexibleContainer">
													<tr>
														<td valign="top" width="500" class="flexibleContainerCell">

															<!-- CONTENT TABLE // -->
														<!--	<table align="left" border="0" cellpadding="0" cellspacing="0" width="100%">
																<tr>
																	<td align="left" valign="top" class="flexibleContainerBox" style="background-color:#5F5F5F;">
																		<table border="0" cellpadding="30" cellspacing="0" width="100%" style="max-width:100%;">
																			<tr>
																				<td align="left" class="textContent">
																					<h3 style="color:#FFFFFF;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:20px;font-weight:normal;margin-top:0;margin-bottom:3px;text-align:left;">Left Column</h3>
																					<div style="text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;color:#FFFFFF;line-height:135%;">Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis.</div>
																				</td>
																			</tr>
																		</table>
																	</td>
																	<td align="right" valign="top" class="flexibleContainerBox" style="background-color:#27ae60;">
																		<table class="flexibleContainerBoxNext" border="0" cellpadding="30" cellspacing="0" width="100%" style="max-width:100%;">
																			<tr>
																				<td align="left" class="textContent">
																					<h3 style="color:#FFFFFF;line-height:125%;font-family:Helvetica,Arial,sans-serif;font-size:20px;font-weight:normal;margin-top:0;margin-bottom:3px;text-align:left;">Right Column</h3>
																					<div style="text-align:left;font-family:Helvetica,Arial,sans-serif;font-size:15px;margin-bottom:0;color:#FFFFFF;line-height:135%;">Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis.</div>
																				</td>
																			</tr>
																		</table>
																	</td>
																</tr>
															</table>-->
															<!-- // CONTENT TABLE -->

														</td>
													</tr>
												</table>
												<!-- // FLEXIBLE CONTAINER -->
											</td>
										</tr>
									</table>
									<!-- // CENTERING TABLE -->
								</td>
							</tr>
							<!-- // MODULE ROW -->


							<!-- MODULE ROW // -->
							<tr>
								<td align="center" valign="top">
									<!-- CENTERING TABLE // -->
									
												<!-- // FLEXIBLE CONTAINER -->
											</td>
										</tr>
									</table>
									<!-- // CENTERING TABLE -->
								</td>
							</tr>
							<!-- // MODULE ROW -->

						</table>
						<!-- // END -->

				
						<table bgcolor="#E1E1E1" border="0" cellpadding="0" cellspacing="0" width="500" id="emailFooter">

							<!-- FOOTER ROW // -->
							<!--
								To move or duplicate any of the design patterns
								in this email, simply move or copy the entire
								MODULE ROW section for each content block.
							-->
							<tr>
								<td align="center" valign="top">
									<!-- CENTERING TABLE // -->
									<table border="0" cellpadding="0" cellspacing="0" width="100%">
										<tr>
											<td align="center" valign="top">
												<!-- FLEXIBLE CONTAINER // -->
												<table border="0" cellpadding="0" cellspacing="0" width="500" class="flexibleContainer">
													<tr>
														<td align="center" valign="top" width="500" class="flexibleContainerCell">
															<table border="0" cellpadding="30" cellspacing="0" width="100%">
																<tr>
																	<td valign="top" bgcolor="#E1E1E1">

																		<div style="font-family:Helvetica,Arial,sans-serif;font-size:13px;color:#828282;text-align:center;line-height:120%;">
																			<div>Copyright &#169; <a href="https://www.facebook.com/muroiwa?fref=ts" target="_blank" style="text-decoration:none;color:#828282;"><span style="color:#828282;">Divine Developers</span></a>. All&nbsp;rights&nbsp;reserved.</div>
																			

																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
												<!-- // FLEXIBLE CONTAINER -->
											</td>
										</tr>
									</table>
									<!-- // CENTERING TABLE -->
								</td>
							</tr>

						</table>
						<!-- // END -->

					</td>
				</tr>
			</table>
		</center>
';

$mail = new PHPMailer(); // create a new object
$mail->IsSMTP(); // enable SMTP
$mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
$mail->SMTPAuth = true; // authentication enabled
$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
$mail->Host = "smtp.gmail.com";
$mail->Port = 465; // or 587
$mail->IsHTML(true);
$mail->addReplyTo('Telecelsystem@gmail.com', 'Telecel');
$mail->setFrom('Telecelsystem@gmail.com', 'Telecel'); 
$mail->Username = "helpdeskecb@gmail.com";
$mail->Password = "password1*";
$mail->Subject = "Telecel`s document control system";
$mail->Body = $body1;
$mail->AddAddress($email);
//$mail->AddAddress("amagobeya@ecbinternational.biz");

 if(!$mail->Send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
 } else {
    echo "Message has been sent";
 }
		  }

		  	  ///*******************************************************************************************************************************************************************
	function GetInstalmentMonthDate($purchaseDate,$NumberOfMonths){
		//$dt = strtotime($purchaseDate."+ ".$NumberOfMonths."month");
		$dt = strtotime("$purchaseDate +".$NumberOfMonths."month");
		$InstalmentMonth = date('Y-m-d',$dt)." 00:00:00";
		return $InstalmentMonth;
	
	}
	  ///*******************************************************************************************************************************************************************
	function FormatDate($date){
				  $dd = substr($date,0,2);
				  $mm = substr($date,3,2);
				  $yy = substr($date,6.4);
				  return trim($mm."/".$dd."/".$yy);
	
	}
		  ///*******************************************************************************************************************************************************************
	//'%Y-%m-%d 00:00:00'
	function FormatDateTime($date){
				  $dd = substr($date,0,2);
				  $mm = substr($date,3,2);
				  $yy = substr($date,6.4);
				  if($mm>12){
					 				  return trim($yy."-".$dd."-".$mm." 00:00:00");
 
					  }
				  
				  return trim($yy."-".$mm."-".$dd." 00:00:00");
	
	}
		  ///*******************************************************************************************************************************************************************

	function GetMonthsPaid($Balance,$Instalments){
				return round($Balance/$Instalments);
	
	}
		  ///*******************************************************************************************************************************************************************

	function start_instalment($id){
  $lastdate = mysql_query("SELECT * FROM stand where id_stand='$id'")or die(mysql_query());
 while($rwdate = mysql_fetch_array($lastdate, MYSQL_ASSOC)){
  $lastmonth=$rwdate['start_instalment'];
  }
  return $lastmonth;
	}
		  ///*******************************************************************************************************************************************************************

		function CheckBalance($id){
  $r = mysql_query("SELECT * FROM payment where stand='$id'")or die(mysql_query());
if(mysql_num_rows($r)==1)
{
  return "Balance Captured";
}else
{
	  return "No Balance Captured";
}
	}
		  ///*******************************************************************************************************************************************************************

function pay($cash,$stand,$month,$pdate,$pd,$vd){
	global $date;
	global $year;
	 mysql_query("insert into payment(date,cash,stand,capturer,month,year,payment_type,payment_date,d,value_date) values('$date','$cash','$stand','$_SESSION[name]','$month','$year','Credit','$pdate','$pd','$vd')") or die(mysql_error());
	 return true;
	}
	
function CashOut($id){
  $qry = mysql_query("SELECT * FROM stand where id_stand='$id'")or die(mysql_query());
   while($row = mysql_fetch_array($qry, MYSQL_ASSOC)){
  mysql_query("INSERT INTO `cashoutstand` (`location`, `area`, `number`, `price`, `date`, `deposit`, `instalments`, `datestatus`, `months_paid`, `start_instalment`, `id_stand`, `vat`, `vatdate`) VALUES ('$row[location]', '$row[area]', '$row[number]', '$row[price]', '$row[date]', '$row[deposit]', '$row[instalments]', '$row[datestatus]', '$row[months_paid]', '$row[start_instalment]', '$row[id_stand]','$row[vat]', '$row[vatdate]')") or die(mysql_error());
  }
    $lastdate = mysql_query("SELECT * FROM payment where stand='$id'")or die(mysql_query());
   while($rwdate = mysql_fetch_array($lastdate, MYSQL_ASSOC)){
  mysql_query("insert into cashoutpayment(date,cash,stand,capturer,month,year,payment_type,payment_date,d,value_date) values('$rwdate[date]','$rwdate[cash]','$rwdate[stand]','$_SESSION[name]','$rwdate[month]','$rwdate[year]','Credit','$rwdate[payment_date]','$rwdate[d]','$rwdate[value_date]')") or die(mysql_error());
  }
   $lastdate1 = mysql_query("SELECT * FROM clients where stand_id='$id'")or die(mysql_query());
   while($rwdate1 = mysql_fetch_array($lastdate1, MYSQL_ASSOC)){
  mysql_query("INSERT INTO `cashoutclients` (`name`, `surname`, `address`, `email`, `contact`, `idnum`, `stand_id`, `sex`, `dob`, `ecnum`, `file_number`, `date`, `id`) VALUES ('$rwdate1[name]', '$rwdate1[surname]', '$rwdate1[address]', '$rwdate1[email]', '$rwdate1[contact]', '$rwdate1[idnum]', '$rwdate1[stand_id]', '$rwdate1[sex]', '$rwdate1[dob]', '$rwdate1[ecnum]', '$rwdate1[file_number]', '$rwdate1[date]', '$rwdate1[id]')") or die(mysql_error());
  }
   $lastdate2 = mysql_query("SELECT * FROM `owners` where stand_id='$id'")or die(mysql_query());
   while($rwdate2 = mysql_fetch_array($lastdate2, MYSQL_ASSOC)){
  mysql_query("INSERT INTO `cashoutowners`  (`client_id`, `owners_date`, `stand_id`) VALUES ('$rwdate2[client_id]', '$rwdate2[owners_date]', '$rwdate2[stand_id]')") or die(mysql_error());
  }
  return true;
}
	  ///*******************************************************************************************************************************************************************

function lastrans($id){
  $lastdate = mysql_query("SELECT * FROM payment where stand='$id'  ORDER BY id DESC LIMIT 1")or die(mysql_query());
  while($rwdate = mysql_fetch_array($lastdate, MYSQL_ASSOC)){
  $lastmonth=$rwdate['payment_type'];
  }
  return $lastmonth;
}
	  ///*******************************************************************************************************************************************************************
function lastmonth($id){
  $lastdate = mysql_query("SELECT * FROM payment where stand='$id' and payment_type='Credit' ORDER BY id DESC LIMIT 1")or die(mysql_query());
  if(mysql_num_rows($lastdate)==0){
  $lastdate = mysql_query("SELECT * FROM payment where stand='$id' and payment_type='Deposit' ORDER BY id DESC LIMIT 1")or die(mysql_query());
 while($rwdate = mysql_fetch_array($lastdate, MYSQL_ASSOC)){
  $lastmonth=$rwdate['month'];
  }
  }
  else{
 while($rwdate = mysql_fetch_array($lastdate, MYSQL_ASSOC)){
  $lastmonth=$rwdate['month'];
  }}
  return $lastmonth;
	}
	  ///**	  ///*******************************************************************************************************************************************************************
function lastmonthdate($id){
  $lastdate = mysql_query("SELECT * FROM payment where stand='$id' and payment_type='Credit' ORDER BY id DESC LIMIT 1")or die(mysql_query());
  if(mysql_num_rows($lastdate)==0){
  $lastdate = mysql_query("SELECT * FROM payment where stand='$id' and payment_type='Deposit' ORDER BY id DESC LIMIT 1")or die(mysql_query());
 while($rwdate = mysql_fetch_array($lastdate, MYSQL_ASSOC)){
  $lastmonth=$rwdate['value_date'];
  }
  }
  else{
 while($rwdate = mysql_fetch_array($lastdate, MYSQL_ASSOC)){
  $lastmonth=$rwdate['value_date'];
  }}
  return $lastmonth;
	}
	  ///*******************************************************************************************************************************************************************

	function nextmonth($current){
				$lastmonth1="20-".$current;
$timestamp = strtotime($lastmonth1); 
$new_date = date('d-m-Y', $timestamp);
				 $month_now = strtotime(date($new_date));
			$nextmonth = date("F-Y", strtotime("+1 month", $month_now));
			return $nextmonth;	
		}
		
		//GetCompanyDetail
		  ///*******************************************************************************************************************************************************************
			
			function GetCompanyDetails(){
		  $query = mysql_query("SELECT * FROM `companydetails` ")or die(mysql_query());
while($rw = mysql_fetch_array($query, MYSQL_ASSOC)){
  $name=$rw['name'];
  $branch=$rw['branch'];
  $address=$rw['address'];
  $contacts=$rw['contacts'];
  $logo=$rw['logo'];
    $banner=$rw['banner'];
    $footer=$rw['footer'];
  $bankingdetails=$rw['bankingdetails'];
  $bankingdetails2=$rw['bankingdetails2'];
  $bankingdetails3=$rw['bankingdetails3'];
      return array($name,$branch,$address,$contacts,$bankingdetails,$bankingdetails2,$bankingdetails3,$logo,$banner,$footer);
  }
	}
			  ///*******************************************************************************************************************************************************************


			function chikwereti($nextmonth){
		  $lastdate = mysql_query("SELECT * FROM `chikwereti` WHERE `month`='$nextmonth'")or die(mysql_query());
$lastcash=mysql_num_rows($lastdate);
  return $lastcash;
	}
			  ///*******************************************************************************************************************************************************************

		function lastmwedzi($current){
				$lastmonth1="20-".$current;
$timestamp = strtotime($lastmonth1); 
$new_date = date('d-m-Y', $timestamp);
				 $month_now = strtotime(date($new_date));
			$nextmonth = date("F-Y", strtotime("-1 month", $month_now));
			return $nextmonth;	
		}
			  ///*******************************************************************************************************************************************************************

		function msgheader($a,$b,$stand){
			 		 if($a==$b){ 
	 mysql_query("update stand set status='Payment_Complete',datestatus='$date' where id_stand='$stand'") or die (mysql_error());
		   	$e="<SCRIPT LANGUAGE='JavaScript'> window.alert('Payment Successful.......')
		index.php?page=sale&id=$stand'
		 	</SCRIPT>" ;				
}else{
		$e="<SCRIPT LANGUAGE='JavaScript'> window.alert('Payment Successful.......')
		index.php?page=sale&id=$stand'
		 	</SCRIPT>";  }
			return $e;
			}
				  ///*******************************************************************************************************************************************************************

	function currentmonth($id){
  $lastdate = mysql_query("SELECT * FROM payment where stand='$id' and payment_type!='Debit' ORDER BY id DESC LIMIT 1")or die(mysql_query());
 while($rwdate = mysql_fetch_array($lastdate, MYSQL_ASSOC)){
  $lastmonth=$rwdate['month'];
   }
  return $lastmonth;
	}
		  ///*******************************************************************************************************************************************************************

	function lastcash($id){
		  $lastdate = mysql_query("SELECT cash AS ss FROM `payment` WHERE  payment.stand = '$id' ORDER BY payment.id desc limit 1")or die(mysql_query());
 while($rwdate = mysql_fetch_array($lastdate, MYSQL_ASSOC)){
  $lastcash=$rwdate['ss'];   
  }
  return $lastcash;
	}
		  ///*******************************************************************************************************************************************************************

		function lastcash2($id,$lastmonth){
			  $lastdate = mysql_query("SELECT Sum(payment.cash) AS ss FROM `payment` WHERE payment.`month` = '$lastmonth' AND payment.stand = '$id' and payment_type='credit'")or die(mysql_query());
 while($rwdate = mysql_fetch_array($lastdate, MYSQL_ASSOC)){
  $lastcash=$rwdate['ss'];   
  }
  return $lastcash;
	}
	
		  ///*******************************************************************************************************************************************************************

	
function zva($number)
{
	$str_num = number_format( $number, 2, ',', ' ' );
	return $str_num;
	}
		  ///*******************************************************************************************************************************************************************

	function getmonth($type,$stand){
	$r1 = mysql_query("SELECT * FROM stand,payment where status='Payment_In_Progress' and stand='$stand' and d='$type' ORDER BY payment.id DESC LIMIT 1")or die(mysql_query());
  while($rw1 = mysql_fetch_array($r1, MYSQL_ASSOC)){
  $instalments=$rw1['month'];
return $instalments;}
	}
	  ///*******************************************************************************************************************************************************************

function getdebit($id){
	$r2=mysql_query("SELECT Sum(payment.cash) AS debit FROM `payment` WHERE payment.stand = '$id' and payment.payment_type = 'Debit' ")or die(mysql_query());
 while($rw2 = mysql_fetch_array($r2, MYSQL_ASSOC)){
  $debit=$rw2['debit'];
    }
	if(mysql_num_rows($r2)==0){$debit=0;}
	
		$r3=mysql_query("SELECT
Sum(payment.cash) AS debit
FROM `payment`
WHERE
payment.stand = '$id' and payment.payment_type = 'Cashout' ")or die(mysql_query());
 while($rw3 = mysql_fetch_array($r3, MYSQL_ASSOC)){
  $debit1=$rw3['debit'];
    }
	if(mysql_num_rows($r3)==0){$debit1=0;}
	return $debit+$debit1;
	}
	  ///*******************************************************************************************************************************************************************
function getcreditcashout($id){
	$r2=mysql_query("SELECT
Sum(cash) AS credit
FROM `cashoutpayment`
WHERE
stand = '$id' and payment_type = 'Credit' ")or die(mysql_query());
 while($rw2 = mysql_fetch_array($r2, MYSQL_ASSOC)){
  $credit=$rw2['credit'];
    }
	if(mysql_num_rows($r2)==0){$credit=0;}
	return $credit;
	}
	function getcredit($id){
	$r2=mysql_query("SELECT
Sum(payment.cash) AS credit
FROM `payment`
WHERE
payment.stand = '$id' and payment.payment_type = 'Credit' ")or die(mysql_query());
 while($rw2 = mysql_fetch_array($r2, MYSQL_ASSOC)){
  $credit=$rw2['credit'];
    }
	if(mysql_num_rows($r2)==0){$credit=0;}
	return $credit;
	}
	  ///*******************************************************************************************************************************************************************

	function GetBeforeVat($id){
	$r2=mysql_query("SELECT Sum(payment.cash) AS credit FROM `payment` WHERE payment.stand = '$id' and payment.d = 'Balance_Before_VAT' ")or die(mysql_query());
 while($rw2 = mysql_fetch_array($r2, MYSQL_ASSOC)){
  $credit=$rw2['credit'];
    }
	if(mysql_num_rows($r2)==0){$credit=0;}
	return $credit;
	}
	 ///*******************************************************************************************************************************************************************

	function getdeposit($id){
	$r2=mysql_query("SELECT
Sum(payment.cash) AS credit
FROM `payment`
WHERE
payment.stand = '$id' and payment.payment_type = 'deposit' ")or die(mysql_query());
 while($rw2 = mysql_fetch_array($r2, MYSQL_ASSOC)){
  $credit=$rw2['credit'];
    }
	if(mysql_num_rows($r2)==0){$credit=0;}
	return $credit;
	}
		  ///*******************************************************************************************************************************************************************

  function debit($type,$mari){
		if($type!='Credit' and $type!='Deposit')	{
			return "";
			}else{
return $mari;}
		}
			  ///*******************************************************************************************************************************************************************

		 function credit($type,$mari){
		if($type!='Debit' and $type!='Cashout')	{
			return "";
			}else{
return "($mari)";}
		}
			  ///*******************************************************************************************************************************************************************

		function months($date,$date2){
		 $start = strtotime($date);
$end = strtotime($date2);
$days_between = ceil(abs($start - $end) / 2635200);
return $days_between;
		}
			  ///*******************************************************************************************************************************************************************

function payment($cash,$last){
		 $date = date('m/d/Y');
		 $start = strtotime($last);
$end = strtotime($date);
$months = ceil(abs($start - $end) / 2592000);
	}
		  ///*******************************************************************************************************************************************************************
  function pinda($cat,$std){
	  	global $date;   
mysql_query("INSERT INTO staff(user,catergory,mark,stage,term,date)
VALUES
('$_SESSION[username]','$cat','$std','1','$_SESSION[term]','$date')") or die (mysql_error());
	  return true;
	  }
	  ///*******************************************************************************************************************************************************************

		 function base($data) {
  return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
	  ///*******************************************************************************************************************************************************************

  /* numeric, decimal passes */
function number($variable) {
	return is_numeric($variable);
}
	  ///*******************************************************************************************************************************************************************

/* digits only, no dots */
function wholenumber($element) {
	return !preg_match ("/[^0-9]/", $element);
}

  function ifexisits($table,$column,$value){
	  	$db = getConnection();
	  $rs1 = mysql_query("select * from $table where $column = '$value'");
 $rw = mysql_num_rows($rs1);
 return $rw;
	  }
  	  ///*******************************************************************************************************************************************************************

  function days($date,$date2){
		 $start = strtotime($date);
$end = strtotime($date2);

$days_between = ceil(abs($start - $end) / 86400);
return $days_between;
		}
		
			  ///*******************************************************************************************************************************************************************

		function deleteRecords($table, $field, $value){
			$db = getConnection();
			$sql = "DELETE FROM $table WHERE $field = '$value'";
			//echo $sql;
			mysql_query($sql)or die (mysql_error());
			
		}
			  ///*******************************************************************************************************************************************************************

		function updateRecords($table, $field, $value){
			$db = getConnection();
			$sql = "update set $table WHERE $field = '$value'";
			//echo $sql;
			mysql_query($sql)or die (mysql_error());
			
		}
		//deleteRecords('final','id',2)
			  ///*******************************************************************************************************************************************************************

		function msg($msg){
			?>
				<script language="javascript">
					alert('<?php echo $msg;?>');
				</script>
			<?php
		}
	  ///*******************************************************************************************************************************************************************
		function link1($link){
			?>
				<script language="javascript">
					location = '<?php echo $link;?>';
				</script>
			<?php
		}
	  ///*******************************************************************************************************************************************************************

		function clean($str) {
                            $str = @trim($str);
                            if (get_magic_quotes_gpc()) {
                                $str = stripslashes($str);
                            }
							$db = getConnection();
                            $new=mysql_real_escape_string($str);
                  			$remove[] = "'";
							$remove[] = '"';
							//$remove[] = "-"; // just as another example
							$new = str_replace($remove, "", $new);
							$new=str_replace(',', '.', $new);
							return $new;
							}
					 ///*******************************************************************************************************************************************************************
	  ///*******************************************************************************************************************************************************************
	  ///*******************************************************FUNCTIONS PAGE**********************************************************************************************
	  ///*******************************************************ERNEST MUROIWA**********************************************************************************************
	  ///************************************************************Telecel***************************************************************************************************
	  ///*******************************************************************************************************************************************************************
	  ///*******************************************************************************************************************************************************************
	  ///*******************************************************************************************************************************************************************	
		
		
		?>
