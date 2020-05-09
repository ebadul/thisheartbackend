<!DOCTYPE html>
<html>
<head>
<style>
a.ResetButton {
    width: 100px;
    padding: 10px 10px 10px 10px;
    cursor: pointer;
    background: #3366cc;
    color: #fff;
    border: 1px solid #3366cc;
    border-radius: 2px;
}
a.ResetButton:hover {
    color: #ffff00;
    background: #000;
    border: 1px solid #fff;
}
</style>
	<title>Reset Password Mail</title>
</head>

<body>
    
	<table cellpadding="10" cellspacing="0" style="width:600px;margin:0px auto">
		<tr>
			<td style="text-align: center;padding:20px;">
                <a style="color:#009ac7;text-decoration:none" href="https://thisheart.co/" 
                     target="_blank">
                    <img src="https://thisheart.co/static/media/logo.8cc0788f.png" 
                    style="vertical-align:middle;width:auto;height:auto;max-width:100%;border-width:0" 
                    alt="This Heart" data-image-whitelisted="" class="CToWUd">
				</a>
			</td>
		</tr>
	</table>
	<table cellpadding="10" cellspacing="0" style="background:#eee;width:600px;margin:0px auto">
		<tr>
			<td style="text-align: center;background:lightblue;padding:50px;">
				<h2>Your Password Reset Link!</h2>
			</td>
		</tr>
			<td>
				<p><b>Hi User,</b></p>
				<p>You are receiving this email because we received a 
                    password reset request for your account.</p>					
                    <br>
					<br>
					<br>
                    
                    <p>This is your seceret token: <br><b>{{$reset_token}}</b><br>
                        Please use while reset password.</p><br>
                     <p>Please click the below Reset Password button for further action.</p><br>
                     <a href={{ $url }} class="ResetButton">Reset Password</a><br>
                     <p>If you did not request a password reset, no further action is required.</p>
                 
				 
					<br>
                    <br>
                    Thanks again for your requesting password reset and if you have any question, 
                    please write <br> an email to us at info@thisheart.co </p>
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