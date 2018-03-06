<html>
	<head>
	/* Google reCaptcha JS */
	<script src='https://www.google.com/recaptcha/api.js'></script>
	</head>

	<body>
		<form action="" method="post">
			Username <input type="text" name="username" class="input" />
			Password <input type="password" name="password" class="input" />
			<div class="g-recaptcha" data-sitekey="6LfxgkoUAAAAAN7qhn0FkKSjf9YBetQ-J5uzEknS"></div>
			<input type="submit"  value="Log In" />
			<span class='msg'><?php echo $msg; ?></span>
		</form>
	</body>
</html>

<?php

include("db.php");
session_start();

$msg = '';

if($_SERVER["REQUEST_METHOD"] == "POST")
{
	$recaptcha = $_POST['g-recaptcha-response'];
	
	if(!empty($recaptcha))
	{
		include("getCurlData.php");
		$google_url = "https://www.google.com/recaptcha/api/siteverify";
		
		$ip = $_SERVER['REMOTE_ADDR'];
		
		$url = $google_url."?secret=".$secret."&response=".$recaptcha."&remoteip=".$ip;
		$res = getCurlData($url);
		$res =  json_decode($res, true);
	
		//reCaptcha success check 
		if($res['success'])
		{
		$username = mysqli_real_escape_string($db,$_POST['username']);
		$password = md5(mysqli_real_escape_string($db,$_POST['password']));
			
			if(!empty($username) && !empty($password))
			{
				$result = mysqli_query($db,"SELECT uid FROM users WHERE username='$username' and passcode='$password'");
				$row = mysqli_fetch_array($result,MYSQLI_ASSOC);
				if(mysqli_num_rows($result)==1)
				{
					$_SESSION['login_user'] = $username;
					//header("location: home.php"); //Success redirection page. 
					echo "login success";
				}
				else
				{
					$msg = "Please give valid Username or Password.";
				}

			}
			else
			{
				$msg = "Please give valid Username or Password.";
			}
		}
		else
		{
			$msg = "Please re-enter your reCAPTCHA.";
		}

	}
	else
	{
		$msg = "Please re-enter your reCAPTCHA.";
	}
}
?>