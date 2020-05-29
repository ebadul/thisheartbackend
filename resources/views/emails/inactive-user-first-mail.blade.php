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
			<p> We haven't heard you for a long while. We have noticed that your account is <br>
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