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
				<h2>Thank you for writing to us</h2>
			</td>
		</tr>
			<td>
				<p><b>Hi, {{ $user->userName}},</b></p>
                <p> We have received your inquiry about our services and would like	to<br>
                    thank you for your messaging to us. 
					<br>
					<br>
                    <b>Your asking information:</b>
					<table width="450" border="1" cellpadding="7" style="border-collapse: collapse;">
                        <tr><td>Name</td><td>{{$user->userName}}</td></tr>
                        <tr><td>Email</td><td>{{$user->userEmail}}</td></tr>
                        <tr><td>Message</td><td>{{$user->userMessage}}</td></tr>
					</table>
				 
					<br>
                    <br>
                    Thanks again for your inquiry and if you have any question, please write <br> 
                    an email to us at info@thisheart.co </p>
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