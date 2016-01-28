<?php

/*
	Google Recaptcha

	- query's Recaptcha-API to check captcha response
    - saves used captchas to prevent multiple use 
	
	dev: womd ( chk.mailbox@gmail.com )

	
	Instructions:
		beovre using, set used_captchas_path and api_secret
	
*/

class Recaptcha {

var $apiurl;

//path where used captchas are stores eg: /my/folder/
var $used_captchas_path;

//your secret from recaptcha - developer console
var $api_secret;

function __construct() {

	$this->apiurl = "https://www.google.com/recaptcha/api/siteverify";

}

/***
does captcha-request to the api, checking the given captcha response
***/
public function check($response,$ip)
{

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
	
	$json_response = curl_exec($curl);
	$curl_errorno = curl_errno($curl);
	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	
	if(isset($json_response['success']) && $json_response['success'] == true) {
	
		$this->save($response);
		return true;
	}
	return false;
	
}

/***
saves the used captcha to file ( filename is md5 of content )
***/
private  function save($grecaptcharesponse) {
		
	if(is_writeable($this->used_captchas_path)) {
		$rdatafile = $this->used_captchas_path . md5($grecaptcharesponse);
		
		file_put_contents($rdatafile, $grecaptcharesponse);
		return true;
	}
	else {
		return false;
	}
}

/***
checks if captcha is used ( file exists )
***/
private function is_used($grecaptcharesponse){
		
	if(file_exists($this->used_captchas_path . md5($grecaptcharesponse))) {
	
		return true;
	}
	return false;
}

}
