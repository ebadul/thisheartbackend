<!DOCTYPE html>
<html>
<head>
	<title>Add New Beneficiary</title>
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
            <img src="http://thisheart.co:8000/images/logo.png" style="vertical-align:middle;max-width:45px;height:auto;border-width:0;margin:0px auto;" alt="This Heart" >
            </p>
            <h2 align="center">{{"You’ve been made a beneficiary"}}</h2>
		</header>
		
        <div class="body">

            <p>Hi {{ $b_first_name }},</p>

            <p><b> {{ $user_first_name }} </b> just made you a beneficiary 
                of their ThisHeart account.<br>
                To access the account, simply 
                click here and use your unique access code: <br>
                {{ $beneficiary_code }}</p>
            
                <p>To access the account, <a href={{$url}} >Go Access Code Page</a> </p>
            <p>ThisHeart is an application that helps people leave behind the things 
                that matter most<br>
                to the people who matter most. </p>
                
            <p>See what {{ $user_first_name }} left behind for you by clicking here.</p>

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