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
            <h2 align="center">{{"Payment Information"}}</h2>
        </header>
        <div class="body">
            
            Hi {{Crypt::decryptString($user->name)}}!<br><br>
            Thank you for registering for ThisHeart {{$payment_session->metadata['package_name']}}. We are looking forward <br>
            to seeing you there and sharing our inbounding application with you. 
            <br><br>
            Info Details:<br>
            --------------<br>
            Date: {{$payment_session->date}}<br>
            Subscribed Plan: {{$payment_session->metadata['package_name']}} <br>
            Payment System: {{ucwords($payment_session->metadata['payment_type'])}}<br><br>

            We have received your info and your charge will be authorized later. 

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