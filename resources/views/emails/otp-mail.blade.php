<!DOCTYPE html>
<html>
<head>
	<title>OTP Mail</title>
</head>

<body>
    

<style>
	header{
		text-align: center;
	}
</style>
<div class="container">
	<header>
		<h1>{{"Two Factor Authentication Set up"}}</h1>
		<p align="center">
		<img src="http://thisheart.co:8000/images/logo.png" style="vertical-align:middle;max-width:45px;height:auto;border-width:0;margin:0px auto;" alt="This Heart" >
		</p>
		<h2 align="center">{{"Two Factor Authentication"}}</h2>
	</header>
	<div class="body">
		<p>Hi {{ Crypt::decryptString($user->name) }}, you’ve set up Two Factor Authentication, making your account even  more secure.</p>
		 <br>
		<p>Here’s the code you’ll need to move forward. Copy it and head back to ThisHeart.</p>
		<h1>{{$otp}}</h1>
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
		<p align="center"><a href="">Terms of use</a> | <a href="">Privacy Policy</a></p>
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