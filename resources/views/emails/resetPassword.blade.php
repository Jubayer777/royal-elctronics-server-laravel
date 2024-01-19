<!DOCTYPE html>
<html>
<head>
    <title>Reset Password Email</title>
</head>
<body>

<center>
    <h2 style="padding: 23px;background: #b3deb8a1;border-bottom: 6px green solid;">
        Demo laravel App
    </h2>
</center>

<p>Click this link to Reset Your Password. </p>

<a href="http://localhost:3000/reset-password/{{$encodedEmail}}/{{$encodedToken}}"> Reset Password</a>

</body>
</html>