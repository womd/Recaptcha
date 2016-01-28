# Recaptcha
PHP - class that can query recaptcha - api for validation
( see more at: https://developers.google.com/recaptcha/intro )

- uses CURL to query recaptcha-api
- saves used captcha to prevent multiple use

##Usage:

example  for CodeIgniter:

	$this->load->library("Recaptcha");
	//set the folder where to save used captcha
	$this->recaptcha->used_captchas_path = "/my/writable/folder/";
	//set your api secret ( developer console )
	$this->recaptcha->api_secret = "6LfDXhYTAAAAA___secret___YKXePnPd3-fRVZ_";
	
	//use the submitted value of "g-recaptcha-response" with client's ip to verify
	if($this->recaptcha->check($this->input->post("g-recaptcha-response"),$_SERVER['REMOTE_ADDR'])) {
	  //captcha is valid
	}
	else {
	  //invalid captcha, already used or folder not writeable
	  
	}
