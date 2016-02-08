<?php

/*
	Google Recaptcha

	- query's Recaptcha-API to check captcha response
    - saves used captchas to file, to prevent multiple use 
	
	dev: womd ( chk.mailbox@gmail.com )

	
	Notes:
		-  set used_captchas_path and api_secret (required)
		-  set debug to true ( optional )
	
*/

class Recaptcha {

var $apiurl;

//path where used captchas are stores eg: /my/folder/
var $used_captchas_path;

//your secret from recaptcha - developer console
var $api_secret;

//store messages in this array
var $messages;

//debug mode logs errors / messsages to stdout
var $debug;

function __construct() {

	$this->apiurl = "https://www.google.com/recaptcha/api/siteverify";
	$this->used_captchas_path = "/just_a_default_value/";
	$this->messages = Array();
	$this->debug = false;
	
}

/***
does captcha-request to the api, checking the given captcha response
***/
public function check($response,$ip)
{
	
	//check if recaptcha save-path is set to something other than default 
	if($this->used_captchas_path == "/just_a_default_value") {
		if($this->debug == true) {
			$this->messages[] = "used_captcha_path is not set, aborting...";
			error_log("used_captcha_path is not set, aborting...");
		}
		return false;
	}
	
	if($this->is_used($response)) {
		return false;
	}
	
	 $data = array("secret" => $this->api_secret,
					"response" => $response,
					"remoteip" => $ip
					); 
															
	 $data_string = http_build_query($data);
	
	 $curl = curl_init($this->apiurl);
	 curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
	 curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	 curl_setopt($curl, CURLOPT_HEADER, 0);
	 curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
	 curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	
	$json_response = json_decode(curl_exec($curl),true);
	$curl_errorno = curl_errno($curl);
	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	
	if(isset($json_response['success']) && ($json_response['success'] == true || $json_response['success'] == 1)) {
	
		$this->save($response);
		return true;
	}
	else {
	
		
	
		if($this->debug == true) {
			error_log("captcha-api said, captcha not solved....");
			$this->messages[] = "curl error: " . $curl_errorno;
			$this->messages[] = "curl status: " . $status;
		}
	return false;
	}
}

/***
saves the used captcha to file ( filename is md5 of content )
***/
private  function save($grecaptcharesponse) {
		
	if(is_writeable($this->used_captchas_path)) {
		$rdatafile = $this->used_captchas_path . md5($grecaptcharesponse);
		
		file_put_contents($rdatafile, $grecaptcharesponse);
		if($this->debug == true){
			error_log($this->used_captchas_path . " response saved successfully.", 0);
			$this->messages[] = $this->used_captchas_path . " response saved successfully.";
		}
		return true;
	}
	else {
		if($this->debug == true) {
			error_log($this->used-captchas_path . " is not writeable", 0);
			$this->messages[] = $this->used_captchas_path . " is not writeable.";
		}
		return false;
	}
}

/***
checks if captcha is used ( file exists )
***/
private function is_used($grecaptcharesponse){
		
	if(is_file($this->used_captchas_path . md5($grecaptcharesponse))) {
		if($this->debug == true) {
			error_log("captcha-file: " . is_file($this->used_captchas_path . md5(grecaptcharesponse)) . " aready exists.");
			$this->messages[] = $this->used_captchas_path . md5(grecaptcharesponse) . " already exists.";
			return true;
		}
	}
	else {
		return false;
	}
}

/***
returns $this->messages array as string
***/
public function get_messages() {

	$flat_message = "";
	foreach($this->messages as $message) {
		$flat_message .= "| " . $message;
	}
	return $flat_message;
}

}
