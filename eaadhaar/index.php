<?php
function xml2array ( $xmlObject, $out = array () )
{
    foreach ( (array) $xmlObject as $index => $node )
        $out[$index] = ( is_object ( $node ) ) ? xml2array ( $node ) : $node;

    return $out;
}
$message = "";
if($_POST){
$email = $_POST['email'];
$phone = $_POST['phone'];
$pass =  $_POST['passcode'];
if(empty($email) or empty($phone)){
	$message = "Input Email and Phone Number.<br> Please try again.";
}
else if(empty($pass)){
	$message = "Input Passcode. <br> Please try again.";
}
else if(empty($_FILES["zip_file"]["name"])){
	$message = "Select Aadhaar Zip FIle. <br> Please try again.";
}
else{
if($_FILES["zip_file"]["name"]) {
	$filename = $_FILES["zip_file"]["name"];
	$source = $_FILES["zip_file"]["tmp_name"];
	$type = $_FILES["zip_file"]["type"];
	
	$name = explode(".", $filename);
	$accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
	foreach($accepted_types as $mime_type) {
		if($mime_type == $type) {
			$okay = true;
			break;
		} 	
	}
	
	$continue = strtolower($name[1]) == 'zip' ? true : false;
	if(!$continue) {
		$message = "The file you are trying to upload is not a .zip file. Please try again.";
	}

	$target_path = "tmp-xml/".$filename; 
	if(move_uploaded_file($source, $target_path)) {
		$zip = new ZipArchive();
		$x = $zip->open($target_path);
		if ($x === true) {
			$zip->setPassword($pass);
			$code = $zip->extractTo("extracted-xml/");	
			echo $code;
			if($code === true){				
				$zip->close();	
				unlink($target_path);
				$message = "Your .zip file was uploaded and unpacked.";
				$target_path = "extracted-xml/";
				$filename = substr($filename, 0, -4);
				$xmlFile = $target_path.$filename.".xml";
				$xmlDoc = new DOMDocument();
				$xmlDoc->load($xmlFile);
				$xml_data = simplexml_import_dom($xmlDoc) or die("Failed to load");
				$xml_array = xml2array($xml_data);
				$reference_id = $xml_array['@attributes']['referenceId'];
				rename($xmlFile,$target_path.$reference_id.".xml");
				header("Location: verify.php?xml=" . $reference_id . "&email=" . $email . "&phone=". $phone . "&p=" . $pass);
				exit();
			}
			else{
				$message = "Passcode is incorrect. <br> Please try again.";
			}		
		}		
	} else {	
		$message = "There was a problem with the upload. Please try again.";
	}
}
}
}
?>
<!DOCTYPE html>
<html lang="en">

	<!-- begin::Head -->
	<head>
		<base href="">
		<meta charset="utf-8" />
		<title>Aadhaar Offline EKYC Verify</title>
		<meta name="description" content="Latest updates and statistic charts">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<!--begin::Fonts 
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700|Roboto:300,400,500,600,700">
		<link rel="shortcut icon" href="assets/media/logos/favicon.ico" />
        -->
</head>
<style>
@import url(https://fonts.googleapis.com/css?family=Roboto:300);

.login-page {
  width: 360px;
  padding: 8% 0 0;
  margin: auto;
}
.form {
  position: relative;
  z-index: 1;
  background: #FFFFFF;
  max-width: 360px;
  margin: 0 auto 100px;
  padding: 45px;
  text-align: center;
  box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2), 0 5px 5px 0 rgba(0, 0, 0, 0.24);
}
.form input {
  font-family: "Roboto", sans-serif;
  outline: 0;
  background: #f2f2f2;
  width: 100%;
  border: 0;
  margin: 0 0 15px;
  padding: 15px;
  box-sizing: border-box;
  font-size: 14px;
}
.form button {
  font-family: "Roboto", sans-serif;
  text-transform: uppercase;
  outline: 0;
  background: #4CAF50;
  width: 100%;
  border: 0;
  padding: 15px;
  color: #FFFFFF;
  font-size: 14px;
  -webkit-transition: all 0.3 ease;
  transition: all 0.3 ease;
  cursor: pointer;
}
.form button:hover,.form button:active,.form button:focus {
  background: #43A047;
}
.form .message {
  margin: 15px 0 0;
  color: red;
  font-size: 18px;
}
.form .message a {
  color: #4CAF50;
  text-decoration: none;
}
.form .register-form {
  display: none;
}
.container {
  position: relative;
  z-index: 1;
  max-width: 300px;
  margin: 0 auto;
}
.container:before, .container:after {
  content: "";
  display: block;
  clear: both;
}
.container .info {
  margin: 50px auto;
  text-align: center;
}
.container .info h1 {
  margin: 0 0 15px;
  padding: 0;
  font-size: 36px;
  font-weight: 300;
  color: #1a1a1a;
}
.container .info span {
  color: #4d4d4d;
  font-size: 12px;
}
.container .info span a {
  color: #000000;
  text-decoration: none;
}
.container .info span .fa {
  color: #EF3B3A;
}
body {
  background: #76b852; /* fallback for old browsers */
  background: -webkit-linear-gradient(right, #76b852, #8DC26F);
  background: -moz-linear-gradient(right, #76b852, #8DC26F);
  background: -o-linear-gradient(right, #76b852, #8DC26F);
  background: linear-gradient(to left, #76b852, #8DC26F);
  font-family: "Roboto", sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;      
}
</style>
<body>
<div class="login-page">
  <div class="form">
	<h1 class="message"><?php echo "Offline Aadhaar EKYC Verification" ?></h1></br>
    <form class="login-form" enctype="multipart/form-data" method="post" action="">
      <input type="email" name="email" required placeholder="E-mail ID"/>
      <input type="text" name="phone" placeholder="Phone Number"/>
	  <input type="password" name="passcode" placeholder="Shared Passcode"/>
	  <input type="file" name="zip_file" accept=".zip" />
      <button>Validate</button>
	  <p class="message"><?php if($message) echo $message; ?></p>
    </form>
  </div>
</div>
</form>
</body>
</html>