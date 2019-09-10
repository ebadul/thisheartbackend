<!DOCTYPE html>
<html>
<head>
<style>
a.ResetButton {
    width: 100px;
    padding: 10px 10px 10px 10px;
    cursor: pointer;
    background: #3366cc;
    color: #fff;
    border: 1px solid #3366cc;
    border-radius: 2px;
}
a.ResetButton:hover {
    color: #ffff00;
    background: #000;
    border: 1px solid #fff;
}
</style>
	<title>Reset Password Mail</title>
</head>

<body>
	<h1>Your Password Reset Link!</h1>
    <p>You are receiving this email because we received a password reset request for your account.</p>
    <p>This is your seceret token: {{ $reset_token }} Please use while reset password.</p><br>
    <p>Please click the below Reset Password button for further action.</p><br>
    <a href={{ $url }} class="ResetButton">Reset Password</a><br>
    <p>If you did not request a password reset, no further action is required.</p>
</body>
</html>