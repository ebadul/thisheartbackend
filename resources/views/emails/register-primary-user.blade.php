<!DOCTYPE html>
<html>
<head>
	<title>User Registration - Active Your Email</title>
</head>

<body>
    
    <style>
        header{
            text-align: center;
        }
    </style>
    <div class="container">
        <header>
         
            <p align="center">
            <img src="http://thisheart.co:8000/images/logo.png" style="vertical-align:middle;max-width:45px;height:auto;border-width:0;margin:0px auto;" alt="ThisHeart" >
            </p>
            <h2 align="center">{{"Verify your email"}}</h2>
        </header>
        <div class="body">
            
            <p>Hi {{ Crypt::decryptString($name) }}, welcome to ThisHeart. You’re almost done</p>
            <p>For your account security please verify your email address.</p>
            <br>
            <p><a href="{{$login_url}}">Click here</a></p>
            <br>
            <p>Or copy this url into your browser.</p>
            <p>Url: {{$login_url}}</p>


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