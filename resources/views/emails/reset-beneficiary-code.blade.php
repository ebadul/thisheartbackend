<!DOCTYPE html>
<html>
<head>
	<title>Reset Invite Code</title>
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
            <h2 align="center">{{"Reset beneficiary code email"}}</h2>
        </header>
        <div class="body">
            
			<p>Hi {{ $b_first_name }},</p>
			<p><b> {{ $user_first_name }}</b> just reset your existing beneficiary code.</p>
			<p>To access the account, click <a href={{$url}} >Go Access Code Page</a> and use your new access code: {{ $beneficiary_code }}</p>
			<br>
			<p>This is your login URL, click <a href={{$login_url}} > LogIn </a>
			
			


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