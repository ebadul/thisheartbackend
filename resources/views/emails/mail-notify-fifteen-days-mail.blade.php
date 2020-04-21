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
				<h2>Remainder your package subscription</h2>
			</td>
		</tr>
			<td>
				<p><b>Hi, {{Crypt::decryptString($user->name)}},</b></p>
				<p>
					Your package subscription for <b>{{$user_pkg->package_info->package}}</b> is expiring within 15 days. 
					It is time to renew. It is important to keep your subscription up to date in order 
					to continue getting updates for <b>{{$user_pkg->package_info->package}}</b> 
					and continued support.

					If you wish to renew your subscription, simply click the link below and 
					follow the instructions.
					<br>
					<br>
					<br>
                    <b>Package information:</b>
					<table width="450" border="1" cellpadding="7" style="border-collapse: collapse;">
                        <tr><td>Package</td><td>{{$user_pkg->package_info->package}}</td></tr>
                        <tr><td>Remaining</td><td>{{$user_pkg->remaining_days}} Days</td></tr>
                        <tr><td>Subscription Date</td><td>{{$user_pkg->subscription_date}}</td></tr>
                        <tr><td>Subscription Expire</td><td>{{$user_pkg->subscription_expire_date}}</td></tr>
                        <tr><td>Cost</td><td>${{$user_pkg->package_info->price}}/Yearly</td></tr>
						<tr><td></td><td><a href="{{$user_pkg->access_url}}packageSubscription/{{$user_pkg->encryptedString}}">
							Upgrade Package</a></td></tr>
          
					</table>
				 
					<br>
                    <br>
					Thanks again for your package subscription and if you have any question, 
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