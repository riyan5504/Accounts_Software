<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Accounts LogIn</title>
    <link rel="stylesheet" href="style.css" />
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: "Segoe UI", sans-serif;
            background: #e0e5ec;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: #e0e5ec;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 10px 10px 30px #c2c8d0, -10px -10px 30px #ffffff;
            width: 300px;
        }

        .tabs {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .tabs button {
            flex: 1;
            padding: 10px;
            border: none;
            background: none;
            font-weight: bold;
            cursor: pointer;
            border-radius: 10px;
            transition: 0.3s;
        }

        .tabs button.active {
            background: #d1d9e6;
            box-shadow: inset 2px 2px 5px #bec4cb, inset -2px -2px 5px #f0f5fa;
        }

        .form {
            display: none;
            flex-direction: column;
        }

        .form.active {
            display: flex;
        }

        input {
            margin: 10px 0;
            padding: 12px;
            border-radius: 10px;
            border: none;
            background: #e0e5ec;
            box-shadow: inset 4px 4px 6px #c8ccd1, inset -4px -4px 6px #f0f5fa;
        }

        .btn {
            margin-top: 10px;
            padding: 12px;
            background: #e0e5ec;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: bold;
            box-shadow: 6px 6px 10px #c2c8d0, -6px -6px 10px #ffffff;
            transition: 0.3s;
        }

        .btn:hover {
            background: #d6dce4;
        }

        .or {
            text-align: center;
            margin: 15px 0 10px;
            font-size: 0.85em;
            color: #666;
        }

        .socials {
            display: flex;
            justify-content: space-around;
        }

        .social {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            background: #e0e5ec;
            box-shadow: 6px 6px 10px #c2c8d0, -6px -6px 10px #ffffff;
            font-size: 1.2em;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="tabs">
            <button id="loginTab" class="active">Log In</button>
            <button id="registerTab">Register</button>
        </div>

        <!-- Login Form -->
        <form id="loginForm" class="form active" action="{{route('admin.login')}}" method="POST">
            @csrf
            <input type="email" name="email" placeholder="Email" required/>
            <input type="password" name="password" placeholder="Password" required />
            <button type="submit" class="btn">
                <i class="fa fa-sign-in-alt"></i> Login
            </button>
            
        </form>

        <!-- Register Form -->
        <form id="registerForm" class="form" action="{{route('register')}}" method="POST">
            @csrf
            <input type="text" name="company_name" placeholder="Company Name" required />
            <input type="text" name="name" placeholder="Full Name" required />
            <input type="email" name="email" placeholder="Email" required />
            <input type="password" name="password" placeholder="Password" required />
            <input type="password" name="password_confirmation" placeholder="Confirm Password" required />
            <button type="submit" class="btn">
                <i class="fa fa-user-plus"></i> Register
            </button>
        </form>
    </div>

    <script>
        const loginTab = document.getElementById("loginTab");
        const registerTab = document.getElementById("registerTab");
        const loginForm = document.getElementById("loginForm");
        const registerForm = document.getElementById("registerForm");
        loginTab.addEventListener("click", () => {
            loginTab.classList.add("active");
            registerTab.classList.remove("active");
            loginForm.classList.add("active");
            registerForm.classList.remove("active");
        });
        registerTab.addEventListener("click", () => {
            registerTab.classList.add("active");
            loginTab.classList.remove("active");
            registerForm.classList.add("active");
            loginForm.classList.remove("active");
        });
    </script>


</body>

</html>