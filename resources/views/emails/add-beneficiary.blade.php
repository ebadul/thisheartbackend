<!DOCTYPE html>
<html>
<head>
	<title>Add New Beneficiary</title>
</head>

<body>
    
<p>Hi {{ $b_first_name }},</p>
<p><b> {{ $user_first_name }}</b> just made you a beneficiary of their thisheart account.</p>
<p>To access the account, click <a href={{$url}} >here</a> and use your access code: {{ $beneficiary_code }}.</p>
<p>thisheart helps you leave behind the things that matter.</p><br>

</body>
</html>