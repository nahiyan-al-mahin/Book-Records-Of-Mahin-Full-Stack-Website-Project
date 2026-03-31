<!--Backend part-->
<?php
session_start();
if(isset($_POST["login"]))
{
    $username    = $_POST["username"];
    $pass        = $_POST["password"];
    $confirmpass = $_POST["confirm_password"];
    $full_name =    $_POST["full_name"];
    $email     =  $_POST["email"];

    // Check passwords match FIRST
    if($pass !== $confirmpass)
    {
        $error = "Passwords do not match.";
    }
    // Check username not empty
    elseif(empty($username))
    {
        $error = "Username cannot be empty.";
    }
    else
    {
        $password = password_hash($pass, PASSWORD_DEFAULT);

        include("database.php");

        // Check if username already exists
        $check = mysqli_query($conn, "SELECT * FROM userinfo WHERE Name='$username'");
        if(mysqli_num_rows($check) > 0)
        {
            $error = "Username already taken.";
        }
        else
        {
            $sql = "INSERT INTO userinfo(Name, Password, Full_Name, Email)
                    VALUES ('$username','$password','$full_name','$email')";

            mysqli_query($conn, $sql);
            
            // Redirect to login after successful registration
            header("Location: login.php?registered=1");
            exit();
        }

        mysqli_close($conn);
    }
}
?>


<!-- HTML PART-->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Mahin's BookShelf — Create Account</title>
  <link rel="stylesheet" href="styles.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet"/>
</head>
<body>

  <!--
    REGISTER PAGE
  -->

  <div class="auth-layout">

    <!-- ===== LEFT PANEL ===== -->
    <aside class="auth-panel">
      <a href="login.php" class="brand">
        <svg class="brand-icon" viewBox="0 0 48 48" fill="none">
          <path d="M4 8s10-2 20 4c10-6 20-4 20-4v32s-10-2-20 4c-10-6-20-4-20-4V8z"
                stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/>
          <line x1="24" y1="12" x2="24" y2="44"
                stroke="currentColor" stroke-width="2" stroke-dasharray="3 3"/>
        </svg>
        <span class="brand-name">Mahin's BookShelf</span>
      </a>

      <!-- Decorative shelf of animated book spines -->
      <div class="shelf" aria-hidden="true">
        <div class="shelf-book"></div>
        <div class="shelf-book"></div>
        <div class="shelf-book"></div>
        <div class="shelf-book"></div>
        <div class="shelf-book"></div>
        <div class="shelf-book"></div>
        <div class="shelf-book"></div>
      </div>

      <blockquote class="auth-quote">
        "So many books, so little time. Start your collection today."
      </blockquote>
    </aside>
    <!-- ===== END LEFT PANEL ===== -->


    <!-- ===== RIGHT CONTENT — registration form ===== -->
    <main class="auth-content">
      <div class="auth-card">

        <h1 class="auth-title">Create account</h1>
        <p class="auth-subtitle">Start your reading journey</p>
        <div class="auth-divider"></div>

        <!--
          REGISTRATION FORM
        -->
        <form method="POST">

          <!-- Full name -->
          <div class="form-group">
            <label class="form-label" for="reg-name">Full Name</label>
            <div class="input-wrap">
              <!-- Person icon -->
              <svg class="input-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
              </svg>
              <input class="form-input" type="text" id="reg-name" name="full_name"
                     placeholder="Your full name" required autocomplete="name"/>
            </div>
          </div>

          <!-- Email address -->
          <div class="form-group">
            <label class="form-label" for="reg-email">Email</label>
            <div class="input-wrap">
              <!-- Envelope icon -->
              <svg class="input-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
              </svg>
              <input class="form-input" type="email" id="reg-email" name="email"
                     placeholder="you@example.com" required autocomplete="email"/>
            </div>
          </div>

          <!-- Username + role on same row -->
          <div class="form-row">

            <!-- Username -->
            <div class="form-group">
              <label class="form-label" for="reg-username">Username</label>
              <div class="input-wrap">
                <!-- At-sign icon -->
                <svg class="input-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M14.243 5.757a6 6 0 10-.986 9.284 1 1 0 111.087 1.678A8 8 0 1118 10a3 3 0 01-4.8 2.401A4 4 0 1114 10a1 1 0 102 0c0-1.537-.586-3.07-1.757-4.243zM12 10a2 2 0 10-4 0 2 2 0 004 0z" clip-rule="evenodd"/>
                </svg>
                <input class="form-input" type="text" id="reg-username" name="username"
                       placeholder="Choose a username" required autocomplete="username"/>
              </div>
            </div>

            <!-- Account role dropdown -->
            <div class="form-group">
              <label class="form-label" for="reg-role">Role</label>
              <div class="input-wrap">
                <!-- Tag icon -->
                <svg class="input-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                </svg>
                <select class="form-input form-select" id="reg-role" name="role">
                  <option value="guest" selected>Guest (Read only)</option>
                </select>
              </div>
            </div>

          </div><!-- end .form-row -->

          <!-- Password + confirm password on same row -->
          <div class="form-row">

            <!-- Password -->
            <div class="form-group">
              <label class="form-label" for="reg-password">Password</label>
              <div class="input-wrap">
                <!-- Lock icon -->
                <svg class="input-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/>
                </svg>
                <input class="form-input" type="password" id="reg-password" name="password"
                       placeholder="Create a password" required autocomplete="new-password"
                       minlength="6"/>
              </div>
            </div>

            <!-- Confirm password -->
            <div class="form-group">
              <label class="form-label" for="reg-confirm">Confirm</label>
              <div class="input-wrap">
                <!-- Lock check icon -->
                <svg class="input-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/>
                </svg>
                <input class="form-input" type="password" id="reg-confirm" name="confirm_password"
                       placeholder="Repeat password" required autocomplete="new-password"/>
              </div>
            </div>

          </div><!-- end .form-row -->

          <!-- Create account submit -->
          <button class="btn-primary" type="submit" name="login">
            <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z"/>
            </svg>
            Create Account
          </button>



          <?php if(isset($error)): ?>
              <div class="note-banner note-error"><?php echo $error; ?></div>
          <?php endif; ?>

        </form>

        <p class="auth-switch">
          Already have an account?
          <a href="login.php">Sign in</a>
        </p>

      </div>
    </main>
    <!-- ===== END RIGHT CONTENT ===== -->

  </div>

</body>
</html>
