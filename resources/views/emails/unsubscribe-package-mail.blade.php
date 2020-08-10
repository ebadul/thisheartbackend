<!DOCTYPE html>
<html>
<head>
	<title>ThisHeart</title>
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
            <h2 align="center">{{"Package Unsubscription"}}</h2>
        </header>
        <div class="body">
            
			<p><b>Hi, {{Crypt::decryptString($user->name)}},</b></p><br>
				<p> You're unsubscribed as :<b><i>{{$user->email}}</i></b>.<br>
                    We want to stay in touch but only in ways that work for you.  <br>
                    If you choose to leave, we will miss out on subscriber only<br>
                    offers and email about ThisHeart. 
					<br>
					<br>
					
                    <br>
                    Thanks again for your payment and if you have any question, please write <br> 
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