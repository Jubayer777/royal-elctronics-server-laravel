<!DOCTYPE html>
<html>
<head>
    <title>Verification Email</title>
</head>
<body>

<center>
    <h2 style="padding: 23px;background: #b3deb8a1;border-bottom: 6px green solid;">
        Demo laravel App
    </h2>
</center>

<p>Hello, Please verify your email</p>

<a href="http://localhost:3000/verify-{{$user->email}}-{{$token}}">VerifyEmail</a> <br> <br>

</body>
</html>