<!DOCTYPE html>
<html>
<head>
	<title>Welcome to This Heart</title>
</head>

<body>

	<table cellpadding="10" cellspacing="0" style="width:600px;margin:0px auto">
		<tr>
			<td style="text-align: center;padding:20px;">
				<a style="color:#009ac7;text-decoration:none" href="https://thisheart.co/" target="_blank">
					<img src="https://thisheart.co/static/media/logo.8cc0788f.png" style="vertical-align:middle;width:auto;height:auto;max-width:100%;border-width:0" alt="This Heart" data-image-whitelisted="" class="CToWUd">
				</a>
			</td>
		</tr>
	</table>
	<table cellpadding="10" cellspacing="0" style="background:#eee;width:600px;margin:0px auto">
		<tr>
			<td style="text-align: center;background:lightblue;padding:50px;">
				<h2>This Heart</h2>
			</td>
		</tr>
			<td>
				<p><b>Hi, {{Crypt::decryptString($user->name)}},</b></p>
				<p> We haven't heard your primary user for a long while. We have noticed that your account is <br>
					inactive for a longer period. We are giving this alert because you have <br>
					been active in thisheat application. Unless you sign in or active account <br>
					we are notifying you that your account will be considered as inactive soon. <br>
					<br>
					<br>
					Hope you will back and continuing with thisheart. Stay tuned and it will be good !! </p>
					<br>
					<a href='https://thisheart.co/login' target="_blank">
						<span style="padding:7px 25px 7px 25px;background:lightgreen;border-radius:3px;border:1px solid #ddd">Login</span>
					</a>
					<br>
					<br>
					Best Regards<br>
					<b>This Heart Team</b><br>
					<a href='https://thisheart.co' target="_blank">www.thisheart.co</a>
			</td>
		</tr>
	</table>


</body>
</html>