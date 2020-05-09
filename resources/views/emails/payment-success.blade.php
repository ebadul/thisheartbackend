<!DOCTYPE html>
<html>
<head>
	<title>Welcome to This Heart</title>
</head>

<body>

	<table cellpadding="10" cellspacing="0" style="width:600px;margin:0px auto">
		<tr>
			<td style="text-align: center;padding:20px;">
				<a style="color:#009ac7;text-decoration:none" href="https://thisheart.co/" target="_blank">
                    <img src="https://thisheart.co/static/media/logo.8cc0788f.png" 
                    style="vertical-align:middle;width:auto;height:auto;max-width:100%;border-width:0" 
                    alt="This Heart" data-image-whitelisted="" class="CToWUd">
				</a>
			</td>
		</tr>
	</table>
	<table cellpadding="10" cellspacing="0" style="background:#eee;width:600px;margin:0px auto">
		<tr>
			<td style="text-align: center;background:lightblue;padding:50px;">
				<h2>Thank you for your payment</h2>
			</td>
		</tr>
			<td>
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
					Best Regards<br>
                    <b>This Heart Team</b><br>
                    
					<a href='https://thisheart.co' target="_blank">www.thisheart.co</a>
			</td>
		</tr>
	</table>


</body>
</html>