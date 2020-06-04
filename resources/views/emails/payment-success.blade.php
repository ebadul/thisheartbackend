<!DOCTYPE html>
<html>
<head>
	<title>This Heart</title>
</head>

<body>

	<style>
        header{
            text-align: center;
        }
    </style>
    <div class="container">
        <header>
            <h1>{{"Thank you for your payment"}}</h1>
            <p align="center">
            <img src="http://thisheart.co:8000/images/logo.png" style="vertical-align:middle;max-width:45px;height:auto;border-width:0;margin:0px auto;" alt="This Heart" >
            </p>
            <h2 align="center">{{"Payment Information"}}</h2>
        </header>
        <div class="body">
            
			<p><b>Hi, {{Crypt::decryptString($user->name)}},</b></p>
				<p> You're now signed for the package plan of <b>{{$payment_session->display_items[0]->custom->name}} - {{$payment_session->metadata['amount']/100}}/Yearly</b>. We have </b><br>
					received your payment and from now you will enjoy the features of this package.  <br>
					<br>
					<br>
					<br>
                    <b>Payment information:</b>
					<table width="450" border="1" cellpadding="7" style="border-collapse: collapse;">
                        <tr><td>Date</td><td>{{$payment_session->date}}</td></tr>
                        <tr><td>Transaction ID</td><td>{{$payment_session->payment_intent}}</td></tr>
                        <tr><td>Payment type</td><td>{{$payment_session->payment_method_types[0]}}</td></tr>
                        <tr><td>Package</td><td>{{$payment_session->display_items[0]->custom->name}}</td></tr>
                        <tr><td>Amount</td><td>${{$payment_session->metadata['amount']/100}}</td></tr>
                        <tr><td>Customer Ref.</td><td>{{$payment_session->customer}}</td></tr>
					</table>
				 
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