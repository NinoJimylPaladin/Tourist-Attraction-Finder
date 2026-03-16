<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tourist Attraction Finder - Log In</title>
    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div class="login-container">
        <div class="left-section">
            <img src="../assets/img/Logo.png" alt="Tourist Attraction Finder Logo" class="logo">
        </div>

        <div class="right-section">
            <div class="login-box">
                <h1>LOG IN</h1>

                <form id="loginForm">

                    <label class="input-label">Email</label>
                    <div class="input-group">
                        <icon class="input-icon">
                            <i class="fas fa-envelope"></i>
                        </icon>
                        <input type="email" id="email" name="email" placeholder=" " required>
                    </div>

                    <label class="input-label">Password</label>
                    <div class="input-group">
                        <icon class="input-icon">
                            <i class="fas fa-lock"></i>
                        </icon>
                        <input type="password" id="password" name="password" placeholder=" " required>
                    </div>

                    <div class="forgot-password">
                        <a href="#" onclick="forgotPassword()">Forgot Password?</a>
                    </div>

                    <button type="submit" class="sign-in-btn">Sign in</button>
                </form>
            </div>
        </div>
    </div>

 <script src="../assets/js/style.js"></script>
</body>
</html>

