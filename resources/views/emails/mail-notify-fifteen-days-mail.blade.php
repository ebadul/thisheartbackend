<!DOCTYPE html>
<html>
<head>
	<title>Welcome to This Heart</title>
</head>

<body>
	   
    <style>
        header{
            text-align: center;
        }
    </style>
    <div class="container">
        <header>
            <h1>{{"Inactivity user notification"}}</h1>
            <p align="center">
            <img src="http://thisheart.co:8000/images/logo.png" style="vertical-align:middle;max-width:45px;height:auto;border-width:0;margin:0px auto;" alt="This Heart" >
            </p>
            <h2 align="center">{{"User notification email"}}</h2>
        </header>
        <div class="body">
            
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
            <p>Thanks</p>
            <p>The ThisHeart Team</p>
            <br>
            <br>
            <p align="center">This email was sent from a notification-only address. Please do not reply</p>
            <br>
            <br>
            <br>
            <p align="center"><a href="https://thisheart.co/termscondition">Terms of use</a> | <a href="https://thisheart.co/privacypolicy">Privacy Policy</a></p>
            <p align="center">ThisHeart</p>
            <br>
            <br>
            <hr style="border:0px;border-bottom: 1px dashed #ccc;">

        </div>
        <footer>

        </footer>

    </div>



</body>
</html>