<!DOCTYPE html>
<html>
<head>
	<title>Add New Beneficiary</title>
</head>

<body>
    
<p>Hi {{ $b_first_name }},</p>
<p><b> {{ $user_first_name }}</b> just made you a beneficiary of their thisheart account.</p>
<p>To access the account, <a href={{$url}} >Go Access Code Page</a> and use your access code: {{ $beneficiary_code }} and your last 4 social code: {{$last4_social}}.</p>
<p>thisheart helps you leave behind the things that matter.</p><br>

<p>This is your login URL, click <a href={{$login_url}} > LogIn </a>

</body>
</html>