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

header{
            text-align: center;
        }
		
</style>
	<title>Reset Password Mail</title>
</head>

<body>
    
	
 
    <div class="container">
        <header>
            <h1>{{"Your Password Reset Link!"}}</h1>
            <p align="center">
            <img src="http://thisheart.co:8000/images/logo.png" style="vertical-align:middle;max-width:45px;height:auto;border-width:0;margin:0px auto;" alt="This Heart" >
            </p>
            <h2 align="center">{{"Password reset email"}}</h2>
        </header>
        <div class="body">
            
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