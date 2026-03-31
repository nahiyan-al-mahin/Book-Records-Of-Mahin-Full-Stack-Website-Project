<!--Backend part-->

<?php
session_start();

if(isset($_POST["login"]))
{
    $username = $_POST["username"];
    $pass     = $_POST["password"];

    // ADMIN CHECK — fixed username and password
    if($username == 'Mahin Uddin' && $pass == 'Mahin@123')
    {
        $_SESSION["logged_in"] = true;
        $_SESSION["role"]      = "admin";
        header("Location: admin-dashboard.php");
        exit();
    }

    // GUEST CHECK — check username and password from database
    include("database.php");

    $sql = "SELECT * FROM userinfo WHERE Name='$username'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if($user && password_verify($pass, $user['Password']))
    {
        $_SESSION["logged_in"] = true;
        $_SESSION["role"]      = "guest";
        $_SESSION["username"] = $user['Name'];
        header("Location: guest-dashboard.php");
        exit();
    }
    else
    {
        $error = "Invalid username or password.";
    }

    mysqli_close($conn);
}
?>




<!-- HTML PART-->

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Mahin's BookShelf — Sign In</title>
  <!-- Shared stylesheet for all pages -->
  <link rel="stylesheet" href="styles.css"/>
  <!-- Google Fonts: Cormorant Garamond (display) + Nunito (body) -->
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet"/>
</head>
<body>

  <!--
    LOGIN PAGE
  -->

  <div class="auth-layout">

    <!--LEFT PANEL — decorative brand area-->
    <aside class="auth-panel">

      <!-- Brand logo mark -->
      <a href="login.php" class="brand">
        <!-- Open-book SVG icon -->
        <svg class="brand-icon" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M4 8s10-2 20 4c10-6 20-4 20-4v32s-10-2-20 4c-10-6-20-4-20-4V8z"
                stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/>
          <!-- Centre spine of book -->
          <line x1="24" y1="12" x2="24" y2="44"
                stroke="currentColor" stroke-width="2" stroke-dasharray="3 3"/>
        </svg>
        <span class="brand-name">Mahin's BookShelf</span>
      </a>

      <!-- Decorative animated book spine stack -->
      <div class="shelf" aria-hidden="true">
        <!-- Each div is a coloured book spine styled entirely in CSS -->
        <div class="shelf-book"></div>
        <div class="shelf-book"></div>
        <div class="shelf-book"></div>
        <div class="shelf-book"></div>
        <div class="shelf-book"></div>
        <div class="shelf-book"></div>
        <div class="shelf-book"></div>
      </div>

      <!-- Inspirational quote at the bottom of the panel -->
      <blockquote class="auth-quote">
        "A reader lives a thousand lives before he dies. The man who never reads lives only one."
      </blockquote>

    </aside>
    <!-- ===== END LEFT PANEL ===== -->


    <!-- ===== RIGHT CONTENT — login form ===== -->
    <main class="auth-content">
      <div class="auth-card">

        <!-- Form heading -->
        <h1 class="auth-title">Welcome back</h1>
        <p class="auth-subtitle">Sign in to your reading shelf</p>
        <div class="auth-divider"></div>

        <!--
          LOGIN FORM
        -->
        <form method="POST">

          <!-- Username field -->
          <div class="form-group">
            <label class="form-label" for="username">Username</label>
            <div class="input-wrap">
              <!-- User icon -->
              <svg class="input-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
              </svg>
              <input class="form-input" type="text" id="username" name="username"
                     placeholder="Enter your username" required autocomplete="username"/>
            </div>
          </div>

          <!-- Password field -->
          <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <div class="input-wrap">
              <!-- Lock icon -->
              <svg class="input-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/>
              </svg>
              <input class="form-input" type="password" id="password" name="password"
                     placeholder="Enter your password" required autocomplete="current-password"/>
            </div>
          </div>

          <!-- Sign in submit button -->
          <button class="btn-primary" type="submit" name="login">
            <!-- Arrow right icon -->
            <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h10.586L11.293 5.707a1 1 0 111.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414-1.414L14.586 11H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
            </svg>
            Sign In
          </button>

            <?php if(isset($error)): ?>
                <div class="note-banner note-error"><?php echo $error; ?></div>
            <?php endif; ?>

        </form>

        <!-- Navigate to registration page -->
        <p class="auth-switch">
          Don't have an account?
          <a href="register.php">Create one</a>
        </p>

      </div>
    </main>
    <!-- ===== END RIGHT CONTENT ===== -->

  </div>

</body>
</html>
