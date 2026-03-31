<?php
session_start();
if(!isset($_SESSION["logged_in"]) || $_SESSION["role"] !== "guest") {
    header("Location: login.php");
    exit();
}
?>



<?php

        include("databaseforbooks.php");

        $sql= "SELECT * FROM booksinfo";

        try
        {
            $result=mysqli_query($conn, $sql);
           // echo"User is registered in database.";
        }
        catch(mysqli_sql_exception)
        {
           // echo"Could not register.";
        }

?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Mahin's BookShelf — Browse Books</title>
  <link rel="stylesheet" href="styles.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet"/>
</head>
<body>

  <!--
    GUEST DASHBOARD
    Read-only view of the book catalog.
    Guest can:
      - Search books by title / author / genre (GET form)
      - Filter by genre (GET form)
      - Click a book card to view book-detail.html

    Guest CANNOT: add or delete books (those options are not shown).

    BACKEND:
      - Read the logged-in user's name and render it in .nav-user.
      - Loop over the books queryset and render book cards below.
      - Handle GET ?q= and ?genre= search/filter params.
  -->

  <!-- ===== TOP NAVIGATION BAR ===== -->
  <nav class="topnav">

    <!-- Brand + Guest badge -->
    <a href="guest-dashboard.php" class="brand sm">
      <svg class="brand-icon" viewBox="0 0 48 48" fill="none">
        <path d="M4 8s10-2 20 4c10-6 20-4 20-4v32s-10-2-20 4c-10-6-20-4-20-4V8z"
              stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/>
        <line x1="24" y1="12" x2="24" y2="44"
              stroke="currentColor" stroke-width="2" stroke-dasharray="3 3"/>
      </svg>
      <span class="brand-name">Mahin's BookShelf</span>
    </a>
    <!-- Blue badge for guest role — different from amber admin badge -->
    <span class="role-badge badge-guest">Guest</span>

    <!-- Guest has only one nav item: Browse -->
    <ul class="nav-links">
      <li>
        <a class="nav-link active" href="guest-dashboard.php">
          <svg style="width:14px;height:14px;vertical-align:-2px;margin-right:4px;"
               viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
          </svg>
          Browse
        </a>
      </li>
    </ul>

    <div class="nav-actions">
      <!--
        BACKEND: Replace "Guest" with the logged-in username.
        e.g. {{ request.user.username }}
      -->
      <span class="nav-user">Hello, <?php echo $_SESSION["username"]; ?></span>
      <form action="logout.php" method="POST" style="margin:0;">
        <button class="btn-logout" type="submit" name="logout">Logout</button>
      </form>
    </div>

  </nav>
  <!-- ===== END TOPNAV ===== -->


  <!-- ===== DASHBOARD CONTENT ===== -->
  <div class="dashboard">

    <div class="panel-header">
      <h2 class="panel-title">Discover Books</h2>
      <p class="panel-desc">Browse the catalog and click any book to view details.</p>
    </div>

    <!--
      SEARCH & FILTER FORM
      GET method — query params appear in URL for bookmarking.
      Backend reads ?q= and ?genre= and filters the book queryset.
    -->

    <!--
      BOOK GRID — read-only, no delete button.
      BACKEND: Loop over filtered books and output a .book-card per book.
      Each card is an <a> linking to book-detail.html?id={book.id}

      Cover colours: assign based on book.id % 7:
        0 → #e8a838   1 → #58a6ff   2 → #3fb950   3 → #f78166
        4 → #d4a017   5 → #bc8cff   6 → #e05050

      Sample static cards — replace with your template loop.
    -->
    <div class="book-grid">
<!-- This whole php code is to run a loop to check the books list and add it inside html to show on web -->        
        <?php
            while($row=mysqli_fetch_assoc($result))
            {
        ?>


      <!-- Card 1 -->
      <a class="book-card" href="book-detail.php?id=<?php echo $row['Book_ID']; ?>">
            <?php
              $colors = ['#e8a838', '#58a6ff', '#3fb950', '#f78166', '#d4a017', '#bc8cff', '#e05050'];
              $color = $colors[array_rand($colors)];
              ?>
        <div class="book-cover" style="background:<?php echo $color; ?>">
          <svg class="cover-icon" viewBox="0 0 48 48" fill="none" aria-hidden="true">
            <path d="M4 8s10-2 20 4c10-6 20-4 20-4v32s-10-2-20 4c-10-6-20-4-20-4V8z"
                  stroke="white" stroke-width="2" stroke-linejoin="round" opacity="0.5"/>
          </svg>
        </div>



        <div class="book-info">
          <h3 class="card-title"><?php echo $row['Book_Name'] ?></h3>
          <p class="card-author"><?php echo $row['Author_Name'] ?></p>
          <div class="card-footer">
            <span class="status-badge s-read">Read</span>
          </div>
        </div>



      </a>
        <?php
            }
        mysqli_close($conn);
        ?>
<!-- This whole php code is to run a loop to check the books list and add it inside html to show on web -->

    </div><!-- end .book-grid -->

  </div><!-- end .dashboard -->

</body>
</html>
