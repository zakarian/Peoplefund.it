<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="author" content="" />
		<meta name="copyright" content="" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<meta name="robots" content="noindex, nofollow" />
		<title>Administration - Login</title>
		<link type="text/css" rel="stylesheet" href="/css/admin/login.css"/>
		<script type="text/javascript" src="/js/jquery/jquery-1.5.2.js"></script>
		<script type="text/javascript" src="/js/admin/admin.login.js"></script>
	</head>
	<body>
		<form action="/administration/login/" method="post" id="login-form">
			<h1>Peoplefund</h1>
			<h2>Login</h2>
			<fieldset <?php if(!empty($error)){ ?>style="padding-top: 80px;"<?php } ?>>
				<?php if(!empty($error)){ ?>
					<center>
						<font color="red"><b><?php echo $error?></b></font>
					</center>
					<br><b>
				<?php } ?>
				<label for="username">
					<span>Administrator</span>
					<input type="text" name="username" id="username" value="" autocomplete="off" />
				</label>
				<label for="password">
					<span>Password</span>
					<input type="password" name="password" id="password" value="" autocomplete="off" />
				</label>
				<input type="image" name="submitted" class="submitLogin" src="/img/buttonLoginOff.gif" title="Login" />
			</fieldset>
		</form>
	</body>
</html>