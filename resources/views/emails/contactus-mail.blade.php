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
            <h1>{{"Thank you for writing to us"}}</h1>
            <p align="center">
            <img src="http://thisheart.co:8000/images/logo.png" style="vertical-align:middle;max-width:45px;height:auto;border-width:0;margin:0px auto;" alt="This Heart" >
            </p>
            <h2 align="center">{{"Contact us email"}}</h2>
        </header>
        <div class="body">
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