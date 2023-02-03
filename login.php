<?php 
include_once("_sys/check_login_status.php");
if($user_ok ==true){
	header("location:sync&".$_SESSION['user_hash']);
	exit();
}
?><?php
if(isset($_POST["email"])){
	$email = $_POST['email'];
	$p = $_POST['p'];
    $ip = "127.0.0.1";
	

	if($email == "" || $p == ""){
		echo "Please Enter Email and Password";
	} else {
		$sql = "SELECT id, email,e_hash, password FROM user_account WHERE email ='$email' AND activated='1' LIMIT 1";
		$query = mysqli_query($db_connection, $sql);
		$rownum = mysqli_num_rows($query);
		if($rownum>0){
			$row = mysqli_fetch_row($query);
			$db_id = $row[0];
			$db_email = $row[1];
			$db_ehash = $row[2];
    		$db_pass_str = $row[3];
			$_SESSION['userid'] = $db_id;
			$_SESSION['user_hash'] = $db_ehash;
			$_SESSION['password'] = $db_pass_str;
			setcookie("id", $db_id, strtotime('+30 days'), "/", "", TRUE);
			setcookie("e_hash", $db_ehash, strtotime('+30 days'), "/", "", TRUE);
			setcookie("pass", $db_pass_str, strtotime('+30 days'), "/", "", TRUE);

			$sql = "UPDATE user_account SET ip='$ip', last_login_date=now() WHERE e_hash='$db_ehash' LIMIT 1";
			$query = mysqli_query($db_connection, $sql);
		}
		else{
			echo "login_failed";
		}
	}
	
	exit();	
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Sign in to TalentTaps</title>
<?php include_once("_ext/default_head.php");?>
<link href="_css/p.login-register-reset.css" rel="stylesheet">
<link rel="stylesheet" href="Font/css/all.css">
</head>
<body class="login-background">
<?php include_once("_ext/pageloader.php");?>
<?php include_once("_ext/pageloader-starter.php");?>
<div class="account-container">
  <div class="content clearfix">
    <div id="showloader" class="div-loader-cover"><div class="spinner"></div></div>
	<form role="form" method="post" onsubmit="return false;">
	  <div class="register-logo">
		<span><img src="_img/owlphin_log.png" style="width:55px;" /></span>
		<h2 style="color: #3c00a0;">Sign In</h2>	
		<span id="status"></span>
	  </div>
	  <div class="login-fields">
		<div class="field">
		<i class="fa-solid fa-envelope"></i>
		  <input type="text" id="email" name="email" placeholder="Email" class="login email-field" onfocus="emptyElement('status')" onkeyup="restrict('email')" />
		</div>
		<div class="field">
		<i class="fa-solid fa-lock"></i>
		  <input type="password" id="password" name="password" placeholder="Password" onfocus="emptyElement('status')" class="login password-field"/>
		  <i class="fa-solid fa-eye-slash" id="show-password"></i>
		</div>
	  </div>
	  <div class="login-actions">			
		<button class="button btn btn-large" onclick="signin()">Sign In</button>
	  </div>
	  <div class="login-extra" style="margin-bottom: 0;">
		<a  href="javascript:void(0)" onclick="resetpass()">Forgot password?</a> 
	  </div>
	  <div class="login-extra" style="margin-top: 0.5em;">
		<a  href="javascript:void(0)" onclick="register()">No account? Register now</a>
	  </div>
	</form>	
  </div>
</div>
<div class="footer mobile-no-show" style="bottom: 0;position: fixed;right: 0;left: 0;"><?php include_once("_ext/footer.php");?></div>
<?php include_once("_ext/default_js.php");?>
<script type="text/javascript">

var eye = document.getElementById('show-password');
var password = document.getElementById("password");
eye.onclick = function(){
	if(password.type=="password"){
		password.type="text";
		eye.classList.remove("fa-eye-slash");
		eye.classList.add("fa-eye");
	}
	else{
		password.type= "password";
		eye.classList.remove("fa-eye");
		eye.classList.add("fa-eye-slash");
	}
}

function restrict(elem){
	var tf = _(elem);
	var rx = new RegExp;
	if(elem == "email"){
		rx = /[' "]/gi;
	}
	tf.value = tf.value.replace(rx, "");
	
}
function emptyElement(x){
	_(x).innerHTML = "";
	_("password").style.borderColor = "black";
	_("email").style.borderColor = "black";
}
function signin(){
	var email = _("email").value;
	var p = _("password").value;
	var status = _("status");
	var validmail = /^[a-zA-Z0-9._]{3,}@[a-z]{4,}[.]{1}[a-z.]{2,6}$/;
	if(email == ""){
		status.innerHTML = '<h5><div class="alert">Please fill out Email</div></h5>';
		_("email").style.borderColor = "red";
	}else if(p == ""){
		status.innerHTML = '<h5><div class="alert">Please fill out Password</div></h5>';
		_("password").style.borderColor = "red";
	}else if(!validmail.test(email)){
		status.innerHTML = '<h5><div class="alert">Please enter a valid email Address</div></h5>';	
	}
	else{
		_("showloader").style.display = "block";
		var ajax = ajaxObj("POST", "login.php");
        ajax.onreadystatechange = function() {
	        if(ajaxReturn(ajax) == true) {
	            if(ajax.responseText == "login_failed" || ajax.responseText == ""){
					status.innerHTML = '<h5><div class="alert">Wrong email or password</div></h5>';
					_("showloader").style.display = "none";
				}
				else{
					location = "login.php";
				}
	        }
        }
		
       ajax.send("email="+email+"&p="+encodeURIComponent(p));
	}
}
</script>
</body>
</html>