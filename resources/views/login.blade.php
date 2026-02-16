<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | School System</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #301934, #5e0094);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}


        .login-box {
            background: #fff;
            padding: 30px;
            width: 320px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .login-box h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .login-box input {
            width: 90%;
            padding: 10px;
            margin-bottom: 12px;
        }

        .login-box button {
    display: block;
    width: 85%;
    padding: 10px;
    background: #301934;
    border: none;
    color: white;
    font-size: 16px;
    cursor: pointer;
    border-radius: 4px;
    
}


        .login-box button:hover {
            background: #1e40af;
        }
    
        .error-box{
            background:#ffe7ea;
            color:#8a1f2d;
            padding:10px 12px;
            border-radius:6px;
            margin-bottom:12px;
            font-size:14px;
        }
</style>
</head>
<body>

<div class="login-box">
    <h2>DCPC IMS</h2>

    <form method="POST" action="{{ route('auth.login') }}">
        @csrf

        @if ($errors->any())
            <div class="error-box">
                {{ $errors->first() }}
            </div>
        @endif


        <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required autofocus>
        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
