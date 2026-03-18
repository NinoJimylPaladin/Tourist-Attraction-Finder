<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tourist Attraction Finder</title>

  <link rel="stylesheet" href="../assets/css/login.css">

  <!-- Font Awesome -->
  <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body>

  <div class="background">

    <div class="login-box">

      <img src="../assets/img/logo.png" alt="Tourist Attraction Finder Logo" class="logo">
      <h2>Sign In To Your Account</h2>

      <form>

        <div class="input-group">
          <i class="fa fa-user"></i>
          <input type="text" placeholder="Email or UserName">
        </div>

        <div class="input-group">
          <i class="fa fa-lock"></i>
          <input type="password" placeholder="Password">
        </div>

        <div class="options">
          <label><input type="checkbox"> Remember Me</label>
          <a href="../pages/forgot-password.php">Forgot Password?</a>
        </div>

        <button class="login-btn">Sign in</button>

        <p class="continue">Or continue with</p>

        <div class="social">
          <i class="fab fa-google"></i>
          <i class="fab fa-facebook-f"></i>
        </div>

        <p class="signup">
          Don't have an account?
          <a href="../pages/register.php">[Sign Up]</a>
        </p>

      </form>

    </div>
  </div>
  <script src="../assets/js/auth.js"></script>
  <script src="../assets/js/login.js"></script>
  <script>
    AOS.init();
    document.querySelectorAll('.smooth-link').forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        document.body.classList.add('fade-out');
        setTimeout(() => {
          window.location.href = this.href;
        }, 500);
      });
    });
  </script>

</body>

</html>