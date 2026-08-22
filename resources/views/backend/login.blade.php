<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Accounts LogIn</title>
    <link rel="stylesheet" href="style.css" />
    <style>
        :root {
            --bg: #0a0f1c;
            --card: #111827;
            --cyan: #22f1ff;
            --pink: #ff2d75;
        }

        body {
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: radial-gradient(circle at top, #0f172a, #020617);
            font-family: "Segoe UI", sans-serif;
        }

        /* CARD */
        .container {
            position: relative;
            width: 340px;
            height: 120px;
            border-radius: 20px;
            background: var(--card);
            overflow: hidden;
            cursor: pointer;
            transition: 0.5s;
        }

        /* expand */
        .container.active {
            height: 350px;
        }

        /* animated border */
        .container::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            pointer-events: none;

            background: repeating-conic-gradient(from 0deg,
                    transparent,
                    var(--cyan),
                    transparent,
                    var(--pink),
                    transparent);

            animation: spin 3s linear infinite;
            filter: blur(8px);
        }

        /* inner layer */
        .container::after {
            content: "";
            position: absolute;
            inset: 3px;
            background: var(--card);
            border-radius: 18px;
            z-index: 1;
            pointer-events: none;
        }

        /* content */
        .container>* {
            position: relative;
            z-index: 2;
            padding: 20px;
        }

        /* tabs */
        .tabs {
            display: flex;
            margin-bottom: 10px;
        }

        .tabs button {
            flex: 1;
            padding: 10px;
            border: none;
            background: transparent;
            color: #aaa;
            cursor: pointer;
            font-weight: bold;
            transition: 0.3s;
        }

        .tabs button.active {
            color: #fff;
            border-bottom: 2px solid var(--cyan);
        }

        /* form */
        .form {
            display: none;
            flex-direction: column;
            opacity: 0;
            transform: translateY(20px);
            transition: 0.4s;
        }

        .form.active {
            display: flex;
            opacity: 1;
            transform: translateY(0);
        }

        /* input */
        input {
            margin: 8px 0;
            padding: 12px;
            border-radius: 25px;
            border: 1px solid #2c344a;
            background: #0b1220;
            color: #fff;
            outline: none;
        }

        input:focus {
            border-color: var(--cyan);
            box-shadow: 0 0 10px var(--cyan);
        }

        /* button */
        .btn {
            margin-top: 10px;
            padding: 12px;
            border-radius: 25px;
            border: none;
            background: linear-gradient(45deg, var(--cyan), var(--pink));
            color: #fff;
            font-weight: bold;
            cursor: pointer;
        }

        /* link */
        .links {
            margin-top: 10px;
            font-size: 12px;
        }

        .links a {
            color: #aaa;
            text-decoration: none;
        }

        .links a:hover {
            color: var(--pink);
        }

        /* error */
        .error {
            color: #ff4d6d;
            font-size: 12px;
        }

        /* animation */
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
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
        <form id="loginForm" class="form active" action="{{ route('admin.login.submit') }}" method="POST">
            @csrf
            <input type="email" name="email" placeholder="Email" required />
            @error('email')
                <div class="error">{{ $message }}</div>
            @enderror
            <input type="password" name="password" placeholder="Password" required />
            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror
            <button type="submit" class="btn">
                <i class="fa fa-sign-in-alt"></i> Login
            </button>

            <div class="links">
                <a href="#">Forgot Password</a>
            </div>
        </form>

        <!-- Register Form -->
        <form id="registerForm" class="form" action="{{ route('register') }}" method="POST">
            @csrf
            <input type="text" name="company_name" placeholder="Company Name" required />
            <input type="text" name="name" placeholder="Full Name" required />
            <input type="text" name="address" placeholder="Full Address" />
            <input type="email" name="email" placeholder="Email" required />
            <input type="password" name="password" placeholder="Password" required />
            <input type="password" name="password_confirmation" placeholder="Confirm Password" required />
            <button type="submit" class="btn">
                <i class="fa fa-user-plus"></i> Register
            </button>
        </form>
    </div>
    <script>
        const container = document.querySelector(".container");
        const loginTab = document.getElementById("loginTab");
        const registerTab = document.getElementById("registerTab");

        const loginForm = document.getElementById("loginForm");
        const registerForm = document.getElementById("registerForm");


        // ==========================================
        // CARD CLICK
        // ==========================================
        // Card-এর যেকোনো জায়গায় click করলে expand হবে
        // এবং প্রথমে Login form দেখাবে
        container.addEventListener("click", function(e) {

            // যদি card আগে থেকেই open থাকে,
            // তাহলে নতুন করে কিছু করার দরকার নেই
            if (container.classList.contains("active")) {
                return;
            }

            container.classList.add("active");

            // Login default form
            loginTab.classList.add("active");
            registerTab.classList.remove("active");

            loginForm.classList.add("active");
            registerForm.classList.remove("active");
        });


        // ==========================================
        // STOP CARD CLICK FROM FORM
        // ==========================================
        // Form-এর ভিতরে click করলে card-এর parent click
        // event trigger হবে না
        loginForm.addEventListener("click", function(e) {
            e.stopPropagation();
        });

        registerForm.addEventListener("click", function(e) {
            e.stopPropagation();
        });


        // ==========================================
        // LOGIN TAB
        // ==========================================
        loginTab.addEventListener("click", function(e) {

            e.stopPropagation();

            // Card expand
            container.classList.add("active");

            // Tab active
            loginTab.classList.add("active");
            registerTab.classList.remove("active");

            // Form switch
            loginForm.classList.add("active");
            registerForm.classList.remove("active");
        });


        // ==========================================
        // REGISTER TAB
        // ==========================================
        registerTab.addEventListener("click", function(e) {

            e.stopPropagation();

            // Card expand
            container.classList.add("active");

            // Tab active
            registerTab.classList.add("active");
            loginTab.classList.remove("active");

            // Form switch
            registerForm.classList.add("active");
            loginForm.classList.remove("active");

            // Form reset
            // @csrf token untouched থাকবে
            registerForm.reset();
        });
    </script>
</body>

</html>
