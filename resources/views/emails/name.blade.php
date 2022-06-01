<!doctype html>
<html lang="en">
<body>
<div>

<h1>Email Verification Mail</h1>
<p>hello {{$user->name}}</p>
<p>Please verify your email with bellow button: </p>
<a href="{{URL::temporarySignedRoute('verification.verify' ,now()->addMinutes(30) ,['id'=>$user->id])}}">
    verify email
</a>
</div>
</body>
</html>
