<?php 
	if(!empty($errors)){
		foreach($errors AS $error){
			?><font color="red"><?php echo $error?></font><br><?php 
		}
		echo "<BR>";
	}
?>

<form method="post" action="/user/sign_up/">
	<input type="hidden" name="fbid" value="<?php echo @$post['fbid']?>">
	<table width="300" cellpadding="3">
		<tr>
			<td>Username</td>
			<td>
				<input type="text" name="username" value="<?php echo @$post['username']?>">
			</td>
		</tr>
		<tr>
			<td>Email</td>
			<td>
				<input type="text" name="email" value="<?php echo @$post['email']?>">
			</td>
		</tr>
		<tr>
			<td>Password</td>
			<td>
				<input type="password" name="password">
			</td>
		</tr>
		<tr>
			<td>Password Repeat</td>
			<td>
				<input type="password" name="password_repeat">
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<input type="submit" value="Sign me up">
			</td>
		</tr>
	</table>
</form>