<!DOCTYPE html>
<html>
<head>
	<title>Send New Beneficiary</title>
</head>

<body>
    
<p>Hi {{ $b_first_name }},</p>
<p><b> {{ $user_first_name }}</b> just send you new beneficiary code.</p>
<p>To access the account, click <a href={{$url}} >Go Access Code Page</a> and use your new access code: {{ $beneficiary_code }}.</p><br>

<p>This is your login URL, click <a href={{$login_url}} > LogIn </a>

</body>
</html>
