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
            <br>
            <br>
            
            Hi  {{Crypt::decryptString($user->name)}},<br>
            Thank you for using the ThisHeart {{$billing->payment_type}} business plan from {{$billing->created_at}}! <br>
            We have successfully processed your payment of ${{$billing->package_cost}}. <br>
            Please find your invoice details to this email.
            <br><br>

            Payment Information:<br>
            ------------------------------<br>
            Date: {{$billing->created_at}} <br>
            TransactionID: {{$billing->id}} <br>
            Payment type: card <br>
            Package: {{$billing->package_info->package}} <br>
            Subscription: {{$billing->payment_type}} <br>
            Amount: ${{$billing->package_cost}} <br>

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