<?php
session_start();
if(!isset($_SESSION["logged_in"])) {
    header("Location: login.php");
    exit();
}

include("databaseforbooks.php");
mysqli_set_charset($conn, "utf8mb4");

// Get book ID from URL
$book_id = $_GET["id"];

// Fetch the book
$sql = "SELECT * FROM booksinfo WHERE Book_ID='$book_id'";
$result = mysqli_query($conn, $sql);
$book = mysqli_fetch_assoc($result);

// If book not found
if(!$book) {
    header("Location: login.php");
    exit();
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo $book['Book_Name']; ?> — Mahin's BookShelf</title>
  <link rel="stylesheet" href="styles.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet"/>
</head>
<body>

  <!-- ===== TOP NAVIGATION BAR ===== -->
  <nav class="topnav">
    <a href="<?php echo $_SESSION['role'] === 'admin' ? 'admin-dashboard.php' : 'guest-dashboard.php'; ?>" class="brand sm">
      <svg class="brand-icon" viewBox="0 0 48 48" fill="none">
        <path d="M4 8s10-2 20 4c10-6 20-4 20-4v32s-10-2-20 4c-10-6-20-4-20-4V8z"
              stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/>
        <line x1="24" y1="12" x2="24" y2="44"
              stroke="currentColor" stroke-width="2" stroke-dasharray="3 3"/>
      </svg>
      <span class="brand-name">Mahin's BookShelf</span>
    </a>

    <?php if($_SESSION['role'] === 'admin'): ?>
      <span class="role-badge badge-admin">Admin</span>
    <?php else: ?>
      <span class="role-badge badge-guest">Guest</span>
    <?php endif; ?>

    <div class="nav-actions">
      <span class="nav-user">
        Hello, <?php echo $_SESSION['role'] === 'admin' ? 'Admin' : $_SESSION['username']; ?>
      </span>
      <form action="logout.php" method="POST" style="margin:0;">
        <button class="btn-logout" type="submit">Logout</button>
      </form>
    </div>
  </nav>

  <!-- ===== BOOK DETAIL CONTENT ===== -->
  <div class="dashboard">
    <div class="form-card" style="max-width:600px;margin:40px auto;">

      <!-- Book Cover -->
      <?php
        $colors = ['#e8a838','#58a6ff','#3fb950','#f78166','#d4a017','#bc8cff','#e05050'];
        $color = $colors[$book['Book_ID'] % 7];
      ?>
      <div class="book-cover" style="background:<?php echo $color; ?>;width:100%;height:200px;border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:24px;">
        <svg viewBox="0 0 48 48" fill="none" style="width:64px;height:64px;" aria-hidden="true">
          <path d="M4 8s10-2 20 4c10-6 20-4 20-4v32s-10-2-20 4c-10-6-20-4-20-4V8z"
                stroke="white" stroke-width="2" stroke-linejoin="round" opacity="0.5"/>
        </svg>
      </div>

      <!-- Book Info -->
      <h2 class="panel-title"><?php echo $book['Book_Name']; ?></h2>

      <table style="width:100%;border-collapse:collapse;margin-top:16px;">
        <tr>
          <td style="padding:10px 0;color:var(--text-muted);width:40%;">Author</td>
          <td style="padding:10px 0;"><?php echo $book['Author_Name']; ?></td>
        </tr>
        <tr>
          <td style="padding:10px 0;color:var(--text-muted);">Publications</td>
          <td style="padding:10px 0;"><?php echo $book['Publications']; ?></td>
        </tr>
        <tr>
          <td style="padding:10px 0;color:var(--text-muted);">Date</td>
          <td style="padding:10px 0;"><?php echo $book['Date']; ?></td>
        </tr>
        <tr>
          <td style="padding:10px 0;color:var(--text-muted);">Price</td>
          <td style="padding:10px 0;"><?php echo $book['Price'] ? '৳'.$book['Price'] : 'N/A'; ?></td>
        </tr>
      </table>

      <!-- Back button -->
      <div style="margin-top:24px;">
        <a href="<?php echo $_SESSION['role'] === 'admin' ? 'admin-dashboard.php' : 'guest-dashboard.php'; ?>"
           class="btn-secondary" style="text-decoration:none;display:inline-block;padding:10px 20px;">
          ← Back to Catalog
        </a>
      </div>

    </div>
  </div>

</body>
</html>