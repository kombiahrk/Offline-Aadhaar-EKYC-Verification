<?php

$xml = $_GET['xml'];
$email_get = $_GET['email'];
$phone_get = $_GET['phone'];
$passcode = $_GET['p'];

$d=0;
$s=0;

$target_path = "extracted-xml/";
$xmlFile = $target_path.$xml.".xml";
$xmlDoc = new DOMDocument();
$xmlDoc->load($xmlFile);

$xml_data = simplexml_import_dom($xmlDoc) or die("Failed to load");
$xml_array = xml2array($xml_data);

$reference_id = $xml_array['@attributes']['referenceId']; //
$aadhar_id = substr($reference_id, 0, 4); // 
$time_stamp = substr($reference_id, 4,14); //
$photo = $xml_array['UidData']['Pht']; 
$dob = $xml_array['UidData']['Poi']['@attributes']['dob'];
$email_hashed = $xml_array['UidData']['Poi']['@attributes']['e'];
$gender = $xml_array['UidData']['Poi']['@attributes']['gender'];
$mobile_hashed = $xml_array['UidData']['Poi']['@attributes']['m'];
$name = $xml_array['UidData']['Poi']['@attributes']['name'];
$country = $xml_array['UidData']['Poa']['@attributes']['country'];
$district = $xml_array['UidData']['Poa']['@attributes']['dist'];
$address = $xml_array['UidData']['Poa']['@attributes']['house'];
$landmark = $xml_array['UidData']['Poa']['@attributes']['landmark'];
$loc = $xml_array['UidData']['Poa']['@attributes']['loc'];
$pincode = $xml_array['UidData']['Poa']['@attributes']['pc'];
$postoffice = $xml_array['UidData']['Poa']['@attributes']['po'];
$state = $xml_array['UidData']['Poa']['@attributes']['state'];
$street = $xml_array['UidData']['Poa']['@attributes']['street'];
$subdistrict = $xml_array['UidData']['Poa']['@attributes']['subdist'];
$vtc = $xml_array['UidData']['Poa']['@attributes']['vtc'];

$dd_date = date('d-m-Y H:i:s', strtotime($time_stamp));

$from = new DateTime(date('Y-m-d', strtotime($dob)));
$to   = new DateTime('today');
$age = $from->diff($to)->y;

$ddfrom = new DateTime(date('Y-m-d', strtotime($time_stamp)));
$ddto   = new DateTime('today');
$diff = $ddto->diff($ddfrom)->format("%a");

//$age = (date('Y') - date('Y',strtotime(date('Y-m-d', strtotime($dob)))));

$hash_count = substr($aadhar_id, -1); //8

if($hash_count == 0){
    $hash_count = 1;
}

// Getting SignatureValue from XML
$signature = $xml_array['Signature']['SignatureValue'];
$signature = base64_decode($signature);

$signed_info = $xmlDoc->getElementsByTagName('SignedInfo')[0];
$signed_info = $signed_info->C14N();
$signed_info = hash('sha1', $signed_info);

// Getting DigestValue from XML
$digest = $xml_array['Signature']['SignedInfo']['Reference']['DigestValue'];
$digest = bin2hex(base64_decode($digest));

// Removing Signature Part from XML
$xml_child = $xml_data->xpath("/OfflinePaperlessKyc");
$xml_child = get_object_vars($xml_child[0]);
$xml_child = $xml_child['Signature'];
unset($xml_child[0]);

$xml_without_signature = strval($xml_data->asXML());
$xmlDoc->loadXML($xml_without_signature);;
$data = $xmlDoc->C14N();
$digest_test = hash('sha256', $data);

//XML Data is Verified by DigestValue
if($digest == $digest_test){
    
    //echo "Step 1 Verification Completed<br>";
    $d=1;
}
else{
   //echo "Step 1 Verification Failed<br>";
}

$pub_key = openssl_pkey_get_public(file_get_contents('uidai_offline_publickey_19062019.cer'));
$details  = openssl_pkey_get_details($pub_key);
//var_dump($details);
//var_dump($details['key']);

$array = openssl_pkey_get_details ($pub_key);
$hex = array_map("bin2hex", $array["rsa"]);
//var_dump($hex);
 
//For decryption we would use: 
$decrypted = '';

//decode must be done before spliting for getting the binary String
$test_data = str_split($signature, 256);

foreach($test_data as $chunk)
{
    $partial = '';

    //be sure to match padding
    $decryptionOK = openssl_public_decrypt($chunk, $partial, $pub_key, OPENSSL_PKCS1_PADDING);

    if($decryptionOK === false){return false;}//here also processed errors in decryption. If too big this will be false
    $decrypted .= $partial;
}  
$decrypted = bin2hex($decrypted);  

$start = strlen($decrypted) - 40; 
  
// substr returns the new string. 
$decrypted = substr($decrypted, $start); 

//XML Data is Verified by SignatureValue
if($signed_info == $decrypted){
    //echo "Step 2 Verification Completed<br>";
    $s=1;
}
else{
    //echo "Step 2 Verification Failed<br>";
}

openssl_free_key($pub_key);
////////////////////////IMAGE PROCESS//////////////////////////////

$bin = base64_decode($photo);
$im = imageCreateFromString($bin);
if (!$im) {
    die('Base64 value is not a valid image');
}
$img_file = 'extracted-xml/'.$reference_id.'.png';
imagepng($im, $img_file, 0);

/////////////////////////VALIDATION////////////////////////////////
$isMobileValid = $isEmailValid = 0;
if(hash_multi($phone_get.$passcode, $hash_count) == $mobile_hashed){
   $isMobileValid = 1;
}
if(hash_multi($email_get.$passcode, $hash_count) == $email_hashed){
    $isEmailValid = 1;
}



function hash_multi ( $data , $count)
{
    $hashed = $data;
    for ($x = 1; $x <= $count; $x++) {
        $hashed = hash('sha256', $hashed);
        //echo $hashed . "<br>";
        //echo $x . "<br>";
    }
   // echo "<br>";
    return $hashed;
}

function xml2array ( $xmlObject, $out = array () )
{
    foreach ( (array) $xmlObject as $index => $node )
        $out[$index] = ( is_object ( $node ) ) ? xml2array ( $node ) : $node;

    return $out;
}

?>


<!DOCTYPE html>
<html lang="en">

	<!-- begin::Head -->
	<head>
		<base href="">
		<meta charset="utf-8" />
		<title>Aadhaar Offline EKYC Verify</title>
		<meta name="description" content="Aadhaar Offline EKYC Verify">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<!--begin::Fonts 
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700|Roboto:300,400,500,600,700">
		<link rel="shortcut icon" href="assets/media/logos/favicon.ico" />
        -->
</head>
<style>
/* CSS design originally by @jofpin, tweaked by Colt Steele */
@import url(https://fonts.googleapis.com/css?family=Raleway|Varela+Round|Coda);

body {
  background: #ecf0f1;
  padding: 2.23em;
  justify-content: center;
  align-items: center;
}

.title {
  color: #2c3e50;
  font-family: "Coda", sans-serif;
  text-align: center;
}
.user-profile {
  padding: 2em;
  margin: auto;
  width: 38em; 
  height: auto;
  background: #fff;
  border-radius: .3em;
}

.user-profile  #fullname {
  margin: auto;
  margin-top: -6.80em;
  margin-left: 7.65em;
  color: #16a085;
  font-size: 1.53em;
  font-family: "Coda", sans-serif;
  font-weight: bold;
}

#username {
  margin: auto;
  display: inline-block;
  margin-left: 13.5em;
  color: #3498db;
  font-size: .87em;
  font-family: "varela round", sans-serif;
}

.user-profile > .description {
  margin: auto;
  margin-top: 1.35em;
  margin-right: 1.2em;
  width: 29em;
  color: #7f8c8d;
  font-size: .87em;
  font-family: "varela round", sans-serif;
}

.user-profile > img#avatar {
  padding: .7em;
  margin-left: .3em;
  margin-top: 0.3em;
  height: 9.23em;
  width: 9.23em;
  border-radius: 1em;
}


.footer {
  margin: 2em auto;
  height: 3.80em;
  background: #16a085;
  text-align: center;
  border-radius: 0.3em 0.3em .3em .3em;
  display: flex;
  justify-content: center;
  align-items: center;
  transition: background 0.1s;
}

button {
  color: white;
  font-family: "Coda", sans-serif;
  text-align: center;
  font-size: 20px;
  background: none;
  outline: none;
  border: 0;
}

.footer:hover {
  background: #1abc9c;
}

</style>
<body>
<div class="user-profile">
<h1 class="title">KYC Details from Aadhaar</h1>

    <div><h3 class="title">EKYC Downloaded Date & Time: <span><?php echo $dd_date?></span><?php if($diff>0){?></br><span><?php echo $diff?></span> Days Older.<?php }?></h3></div>
<?php if($d && $s) {?>
	<img id="avatar" src="<?php echo 'extracted-xml/'.$reference_id.'.png'; ?>" />
    <div id="fullname"><?php echo $name . " (" . $age . ")" ?> </div>
    <div id="username">XXXX XXXX <?php echo $aadhar_id ?></div>
    <div class="description">
      <div><label style="color:#16a085;">DOB: </label><span><?php echo $dob ?></span></div>
      <div><label style="color:#16a085;">Email: </label><span><?php echo $email_get ?></span></div>
      <div><label style="color:#16a085;">Mobile: </label><span><?php echo $phone_get ?></span></div>
      <div><label style="color:#16a085;">Address: </label><span><?php echo $address . "," . $street . "," . $landmark . "," . $vtc . "," . $loc . "," . $postoffice ?></span></div>
      <div><label style="color:#16a085;">PinCode: </label><span><?php echo $pincode ?></span></div>      
    </div>
    <div class="footer">
    
      <button id="btn">
      <?php if(!$isEmailValid && !$isMobileValid) { ?> Both Email and Mobile Verification Failed <?php } elseif (!$isMobileValid) { ?> Mobile Verification Failed <?php } elseif(!$isEmailValid) {?> Email Verification Failed <?php } else{?>Verification Success<?php }?></button>
    </div>  
    <?php } else { ?>
    <div class="footer">
    <?php if($d==1 && $s==0) {?>
      <button id="btn">XML Signature Verified</button>
      <?php }else{ ?>
      <button id="btn">XML Signature Invalid</button>
      <?php } ?>
    </div>
  <?php } ?>
</div>	
</body>
</html>