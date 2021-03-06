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
            
            <p align="center">
            <img src="http://thisheart.co:8000/images/logo.png" style="vertical-align:middle;max-width:45px;height:auto;border-width:0;margin:0px auto;" alt="ThisHeart" >
            </p>
            <h2 align="center">{{"Forgot your password"}}</h2>
        </header>
        <div class="body">
            
			<p>Oops, forgot your password, huh? No worries. We’re here to help.</p>

            <p> You’ll need this token to reset your password. Go ahead and copy it now. <br>
                <b>{{$reset_token}}</b> </p>

            <p>Now, let’s head back to ThisHeart and complete the process.<br> 

            <br>
            <a href={{ $url }} class="ResetButton">Click here to get back.</a>    
            <br>

            <p>If you did not request a password reset, dont worry about it. 
                Just take no further action. Our security features will keep you covered.</p>

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