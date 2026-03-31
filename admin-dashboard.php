<!--This is the backend part-->
<?php
session_start();
if(!isset($_SESSION["logged_in"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}
?>


<?php
    $result = null;
    $result_search=null;
    $result_delete_search = null;
    include("databaseforbooks.php");
    $sql= "SELECT * FROM booksinfo";
    $result=mysqli_query($conn, $sql);


    if(isset($_POST["submit"]))
    {
        $bookname=$_POST["bookname"];
        $authorname=$_POST["authorname"];
        $publications=$_POST["publications"];

//this part is for all books search
        $sql1= "SELECT * FROM booksinfo WHERE Book_Name='$bookname' or Author_Name='$authorname' or Publications='$publications'";

        try
        {
            $result_search=mysqli_query($conn, $sql1);
            $result= mysqli_query($conn, "SELECT * FROM booksinfo");

           // echo"User is registered in database.";
        }
        catch(mysqli_sql_exception)
        {
           // echo"Could not register.";
        }
//this part is for all books search
    }

    if(isset($_POST["add_books"]))
    {
        $addbookname=$_POST["title"];
        $addauthorname=$_POST["author"];
        $date=$_POST["year"];
        $addpublications=$_POST["publications"];
        $price=$_POST["price"];


        $sql2= "INSERT INTO booksinfo(Book_Name, Author_Name, Date, Publications, Price)
            VALUES ('$addbookname','$addauthorname','$date','$addpublications','$price')";

        try
        {
            $result_add=mysqli_query($conn, $sql2);
           // echo"User is registered in database.";
           $result= mysqli_query($conn, "SELECT * FROM booksinfo");

           header("Location: admin-dashboard.php#tab-add");
            exit();
        }
        catch(mysqli_sql_exception)
        {
           // echo"Could not register.";
        }
    }



    if(isset($_POST["delete_search"]))
    {
        $del_bookname     = $_POST["bookname"];
        $del_authorname   = $_POST["authorname"];
        $del_publications = $_POST["publications"];

        $sql_del = "SELECT * FROM booksinfo WHERE Book_Name='$del_bookname' 
                    OR Author_Name='$del_authorname' 
                    OR Publications='$del_publications'";

        $result_delete_search = mysqli_query($conn, $sql_del);
    }

    // DELETE BOOK by ID
    if(isset($_POST["delete_book"]))
    {
        $book_id = $_POST["book_id"];

        $sql_delete = "DELETE FROM booksinfo WHERE Book_ID='$book_id'";
        mysqli_query($conn, $sql_delete);

        header("Location: admin-dashboard.php#tab-delete");
        exit();
    }

?>








<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Mahin's BookShelf — Admin Dashboard</title>
  <link rel="stylesheet" href="styles.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet"/>

  <style>
    /*
      ADMIN TAB SWITCHING — pure CSS :target technique
      -------------------------------------------------------
      Each tab panel has an id (e.g. id="tab-browse").
      Clicking a nav link sets the URL fragment to #tab-browse,
      which makes CSS :target match that element and show it.

      Default state: only #tab-browse is visible (shown via :not(:target) rule).
      When a fragment is set, the targeted panel shows and others hide.
    */

    /* By default show the browse panel (no fragment in URL yet) */
    /* All panels hidden initially */
    .tab-panel { display: none; }

    /*
      Show browse panel by default when no target is set.
      We use the :not(:target) trick:
      If the body has no :target OR the target is not one of our panels,
      the browse panel acts as the default visible panel.
    */
    #tab-browse:target,
    #tab-search:target,
    #tab-add:target,
    #tab-delete:target { display: block; }

    /*
      Fallback: show browse when nothing is targeted.
      We achieve this by showing #tab-browse always,
      then hiding it when another panel is targeted.
    */
    #tab-browse { display: block; } /* default visible */
    #tab-search:target    ~ * #tab-browse,
    #tab-add:target    ~ * #tab-browse,
    #tab-delete:target ~ * #tab-browse { display: none; }

    /* Simpler approach: show the targeted one, hide others */
    /* Override the default when a different panel is targeted */
    body:has(#tab-search:target) #tab-browse  { display: none; }
    body:has(#tab-search:target) #tab-search  { display: block; }
    body:has(#tab-add:target)    #tab-browse  { display: none; }
    body:has(#tab-add:target)    #tab-add     { display: block; }
    body:has(#tab-delete:target) #tab-browse  { display: none; }
    body:has(#tab-delete:target) #tab-delete  { display: block; }

    /* Active nav link highlight using :has() — highlights the link
       whose href matches the current :target */
    body:has(#tab-search:target)    .nav-link[href="#tab-search"]    { background: var(--amber-glow); color: var(--amber); }
    body:has(#tab-add:target)    .nav-link[href="#tab-add"]    { background: var(--amber-glow); color: var(--amber); }
    body:has(#tab-delete:target) .nav-link[href="#tab-delete"] { background: var(--amber-glow); color: var(--amber); }

    /* Browse link is active when neither add nor delete is targeted */
    body:not(:has(#tab-add:target)):not(:has(#tab-delete:target)):not(:has(#tab-search:target))
      .nav-link[href="#tab-browse"] { background: var(--amber-glow); color: var(--amber); }
  </style>
</head>
<body>

  <!--
    ADMIN DASHBOARD
    Three tabs:
      1. Browse  — search + view all books
      2. Add     — form to add a new book
      3. Delete  — search + delete books

    Tab switching: pure CSS :target (no JavaScript).
    Click a nav link → URL fragment changes → CSS :target matches → panel shows.

    Book cards in Browse link to book-detail.html?id=X
    (Your backend renders the correct detail page).
    Book cards in Delete have a form with method="POST" action="/delete-book".
  -->

  <!-- ===== TOP NAVIGATION BAR ===== -->
  <nav class="topnav">

    <!-- Brand + Admin badge -->
    <a href="admin-dashboard.php" class="brand sm">
      <svg class="brand-icon" viewBox="0 0 48 48" fill="none">
        <path d="M4 8s10-2 20 4c10-6 20-4 20-4v32s-10-2-20 4c-10-6-20-4-20-4V8z"
              stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/>
        <line x1="24" y1="12" x2="24" y2="44"
              stroke="currentColor" stroke-width="2" stroke-dasharray="3 3"/>
      </svg>
      <span class="brand-name">Mahin's BookShelf</span>
    </a>
    <!-- Role badge: amber for admin -->
    <span class="role-badge badge-admin">Admin</span>

    <!-- Tab navigation links — each changes URL fragment to show a panel -->
    <ul class="nav-links">
      <li>
        <a class="nav-link" href="#tab-browse">
          <!-- Grid icon -->
          <svg style="width:14px;height:14px;vertical-align:-2px;margin-right:4px;"
               viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
          </svg>
          Browse
        </a>
      </li>
      <li>
        <a class="nav-link" href="#tab-search">
          <!-- Grid icon -->
          <svg style="width:14px;height:14px;vertical-align:-2px;margin-right:4px;"
               viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
          </svg>
          Search
        </a>
      </li>
      <li>
        <a class="nav-link" href="#tab-add">
          <!-- Plus icon -->
          <svg style="width:14px;height:14px;vertical-align:-2px;margin-right:4px;"
               viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
          </svg>
          Add Book
        </a>
      </li>
      <li>
        <a class="nav-link" href="#tab-delete">
          <!-- Trash icon -->
          <svg style="width:14px;height:14px;vertical-align:-2px;margin-right:4px;"
               viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
          </svg>
          Delete
        </a>
      </li>
    </ul>

    <!-- Greeting + logout -->
    <div class="nav-actions">
      <!--
        BACKEND: Replace "Admin" with the logged-in user's name.
        e.g. in Django: {{ request.user.username }}
      -->
      <span class="nav-user">Hello, Admin</span>
      <!-- Logout: POST to /logout endpoint -->
      <form action="logout.php" method="POST" style="margin:0;">
        <button class="btn-logout" type="submit">Logout</button>
      </form>
    </div>

  </nav>
  <!-- ===== END TOPNAV ===== -->


  <!-- ===== DASHBOARD CONTENT ===== -->
  <div class="dashboard">

    <!-- ============================================================
         TAB 1: BROWSE — Search and view all books
         Visible by default (CSS sets display:block on #tab-browse).
         Book cards link to book-detail.html.
         Search/filter: your backend handles GET query params.
    ============================================================ -->

<!--############################################################################################################################################################### -->
<!-- ===== Start TAB: Browse ===== -->

    <section id="tab-browse" class="tab-panel">

      <div class="panel-header">
        <h2 class="panel-title">Book Catalog</h2>
        <p class="panel-desc">Show all books</p>
      </div>
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
        ?>
<!-- This whole php code is to run a loop to check the books list and add it inside html to show on web -->

    </div><!-- end .book-grid -->

    </section>
    <!-- ===== END TAB: BROWSE ===== -->



<!--############################################################################################################################################################### -->
    <!-- ===== Start TAB: Search ===== -->

    <section id="tab-search" class="tab-panel">

      <div class="panel-header">
        <h2 class="panel-title">Book Catalog</h2>
        <p class="panel-desc">Search, browse, and view detailed info on any book.</p>
      </div>

      <!--
        SEARCH & FILTER FORM
        GET method so search terms appear in the URL.
        action="" → submits to same page; your backend reads ?q= and ?genre=
      -->
      <form class="search-row" method="post">
        <!-- Search text input -->
        <div class="search-bar">
          <svg class="search-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
          </svg>
          <input type="text" class="search-input"
                 placeholder="Search by title"
                 value="" name="bookname" 
                 />
        </div>

        <div class="search-bar">
          <svg class="search-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
          </svg>
          <input type="text" class="search-input"
                 placeholder="Search by author"
                 value=""  name="authorname"
                 />
        </div>

        <div class="search-bar">
          <svg class="search-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
          </svg>
          <input type="text" class="search-input"
                 placeholder="Search by publication" name="publications"
                 value=""  
                 />
        </div>
        
        <!-- Submit search button (visually hidden — pressing Enter works too) -->
        <button type="submit" class="btn-primary" style="width:auto;padding:11px 20px;" name="submit">
          Search
        </button>
      </form>

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
      <?php if($result_search !==null): ?>       
        <?php
            while($row=mysqli_fetch_assoc($result_search)):
        ?>


      <!-- Card 1 -->
      <a class="book-card" href="book-detail.html?id=1">
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
        <?php endwhile; ?>
      <?php else: ?>
          <p>Use the search above to find books.</p>
        <?php  endif; ?>
<!-- This whole php code is to run a loop to check the books list and add it inside html to show on web -->

    </div><!-- end .book-grid -->

    </section>
    <!-- ===== END TAB: Search ===== -->


    <!-- ============================================================
         TAB 2: ADD BOOK — form to add a new book to the catalog
         Hidden until #tab-add is targeted via URL fragment.
         Form submits POST to /admin/add-book on your backend.
    ============================================================ -->



<!--############################################################################################################################################################### -->
    <!-- ===== Start TAB: add ===== -->
    <section id="tab-add" class="tab-panel">

      <div class="panel-header">
        <h2 class="panel-title">Add New Book</h2>
        <p class="panel-desc">Fill in the details below to add a book to the catalog.</p>
      </div>

      <div class="form-card">

        <!--
          ADD BOOK FORM
          method="POST" action="/admin/add-book"
          Backend validates and saves; redirects back with ?added=1 for success banner.
        -->
        <form method="POST">
        <input type="hidden" name="active_tab" value="tab-add"/>
          <!-- Title + Author side by side -->
          <div class="form-row">

            <!-- Book title -->
            <div class="form-group">
              <label class="form-label" for="book-title">Book Title *</label>
              <div class="input-wrap">
                <!-- Book icon -->
                <svg class="input-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                </svg>
                <input class="form-input" type="text" id="book-title" name="title"
                       placeholder="e.g. The Great Gatsby" required/>
              </div>
            </div>

            <!-- Author -->
            <div class="form-group">
              <label class="form-label" for="book-author">Author *</label>
              <div class="input-wrap">
                <svg class="input-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                </svg>
                <input class="form-input" type="text" id="book-author" name="author"
                       placeholder="e.g. F. Scott Fitzgerald" required/>
              </div>
            </div>

          </div><!-- end form-row -->

          <!-- Genre + Year side by side -->
          <div class="form-row">
            <!-- Year published -->
            <div class="form-group">
              <label class="form-label" for="book-year">Year bought</label>
              <div class="input-wrap">
                <!-- Calendar icon -->
                <svg class="input-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                <input class="form-input" type="date" id="book-year" name="year"
                       placeholder="e.g. 1925" min="0" max="2099"/>
              </div>
            <div class="form-group">
              <label class="form-label" for="book-author">Publications</label>
              <div class="input-wrap">
                <svg class="input-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                </svg>
                <input class="form-input" type="text" id="book-author" name="publications"
                       placeholder="e.g. Prothoma" required/>
              </div>
            </div>
            </div>

          </div><!-- end form-row -->

          <!-- ISBN (full width) -->
          <div class="form-group">
            <label class="form-label" for="book-isbn">Price</label>
            <div class="input-wrap">
              <!-- Barcode icon -->
              <svg class="input-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
              </svg>
              <input class="form-input" type="text" id="book-isbn" name="price"
                     placeholder="e.g. 250"/>
            </div>
          </div>

          <!-- Action buttons -->
          <div class="form-actions">
            <button class="btn-primary" type="submit" name="add_books">
              <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
              </svg>
              Add Book
            </button>
            <!-- Reset clears the form fields -->
            <button class="btn-secondary" type="reset">Clear</button>
          </div>

          <!--
            BACKEND SUCCESS / ERROR BANNERS
            After POST, redirect back with a query param, e.g. ?added=1
            Then render the appropriate banner from your template.

            Success example:
            <div class="note-banner note-success">✓ Book added to the catalog.</div>
            Error example:
            <div class="note-banner note-error">✗ Title is required.</div>
          -->

        </form>
      </div><!-- end .form-card -->

    </section>
    <!-- ===== END TAB: ADD BOOK ===== -->


    <!-- ============================================================
         TAB 3: DELETE — search books and delete them
         Hidden until #tab-delete is targeted via URL fragment.
         Each card has a small form that POSTs to /admin/delete-book.
    ============================================================ -->

<!--############################################################################################################################################################### -->
    <!-- ===== Start TAB: delete BOOK ===== -->
<section id="tab-delete" class="tab-panel">

  <div class="panel-header">
    <h2 class="panel-title">Delete a Book</h2>
    <p class="panel-desc">Search for a book then click Delete to remove it.</p>
  </div>

  <!-- Search form — uses delete_search button name so it doesn't clash with the other search -->
  <form class="search-row" method="post">
    <div class="search-bar">
      <svg class="search-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
      </svg>
      <input type="text" class="search-input" placeholder="Search by title" name="bookname"/>
    </div>
    <div class="search-bar">
      <svg class="search-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
      </svg>
      <input type="text" class="search-input" placeholder="Search by author" name="authorname"/>
    </div>
    <div class="search-bar">
      <svg class="search-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
      </svg>
      <input type="text" class="search-input" placeholder="Search by publication" name="publications"/>
    </div>
    <button type="submit" class="btn-primary" style="width:auto;padding:11px 20px;" name="delete_search">
      Search
    </button>
  </form>

  <!-- Results grid — only shows after search -->
  <div class="book-grid">
    <?php if($result_delete_search !== null): ?>
      <?php while($row = mysqli_fetch_assoc($result_delete_search)): ?>

        <div class="book-card">
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
            <h3 class="card-title"><?php echo $row['Book_Name']; ?></h3>
            <p class="card-author"><?php echo $row['Author_Name']; ?></p>
            <p class="card-author"><?php echo $row['Publications']; ?></p>
            <div class="card-footer">
              <span class="card-year"><?php echo $row['Date']; ?></span>
            </div>
            <!-- Each card has its own mini delete form with the book's real ID -->
            <form method="POST" style="margin:0;">
              <input type="hidden" name="book_id" value="<?php echo $row['Book_ID']; ?>"/>
              <button class="btn-danger" type="submit" name="delete_book"
                      onclick="return confirm('Delete this book?')">
                <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                Delete
              </button>
            </form>
          </div>
        </div>

      <?php endwhile; ?>
    <?php else: ?>
      <p class="empty-notice">Search for a book above to delete it.</p>
    <?php endif; ?>
  </div>

</section>
    <!-- ===== END TAB: DELETE ===== -->
<!--############################################################################################################################################################### -->
  </div><!-- end .dashboard -->
        <?php
        mysqli_close($conn);
        ?>
</body>
</html>