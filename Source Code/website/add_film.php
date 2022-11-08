<?php

session_start();

// default parameters
$page = 1;

// process page data
if (isset($_GET["page"])) {
    $page = $_GET["page"];
}
$nextPage = $page + 1;
$previousPage = $page - 1;

// construct API request
if (isset($_GET["sort"])) {
    $sort = $_GET["sort"];
    if (isset($_GET["order"])) {
        $order = $_GET["order"];
        $moviesApiRequest = "http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/api/?page=$page&sort=$sort&order=$order";
    } else {
        $moviesApiRequest = "http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/api/?page=$page&sort=$sort";
    }
} else if (isset($_GET["search"])) {
    $search_term = $_GET["search"];
    $search_term = str_replace(' ', '%20', $search_term);
    $moviesApiRequest = "http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/api/?page=$page&search=$search_term";
} else {
    $moviesApiRequest = "http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/api/?page=$page";
}

// dispatch request and process result
$response = file_get_contents($moviesApiRequest);
$data = json_decode($response, true);

?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BingeSpark</title>
    <!-- <link rel="stylesheet" href="bulma/css/bulma.css"> -->
    <link rel="stylesheet" href="myui.css">
</head>

<body>

    <!-- START NAV -->
    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <a class="navbar-item" href="http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/website">
                <img src="logo.png" width="112" height="28">
            </a>

            <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
            </a>
        </div>

        <div id="navbarBasicExample" class="navbar-menu">
            <div class="navbar-start">

                <a class="navbar-item" href="http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/website">
                    Home
                </a>

                <a class="navbar-item" href="http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/website/genres.php">
                    Genres
                </a>

                <?php 
                    if (isset($_SESSION['admin_status'])) {
                        $admin_status = $_SESSION['admin_status'];
                        if ($admin_status) {
                            echo "
                                <a class='navbar-item' href='http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/website/add_film.php'>
                                    Add Film
                                </a>
                            ";
                        }
                    }
                ?>

                <form method='POST' action='http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/api/process.php?search'>
                    <div class="field has-addons">
                        <div class="control">
                            <input class="input" type="text" name="search_term" placeholder="Search for films...">
                        </div>
                        <div class="control">
                            <button class='button is-info'>
                                Search
                            </button> 
                        </div>
                    </div>
                </form>
            </div>

            <div class="navbar-end">
                <div class="navbar-item">
                        <?php 
                            if (isset($_SESSION['username'])) {
                                $username = $_SESSION['username'];
                                echo "
                                    <div class='dropdown is-hoverable'>
                                        <div class='dropdown-trigger'>
                                            <button class='button is-info' aria-haspopup='true' aria-controls='dropdown-menu4'>
                                                <span>Logged in as <strong>$username</strong> ˅</span>
                                            </button>
                                        </div>
                                        <div class='dropdown-menu' id='dropdown-menu4' role='menu'>
                                            <div class='dropdown-content'>
                                                <a class='dropdown-item' href='http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/api/process.php?logout'>
                                                    Log out
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                ";
                            } else {
                                echo "
                                    <div class='buttons'>
                                        <a class='button is-primary' href='http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/website/sign_up.php'>
                                            <strong>Sign up</strong>
                                        </a>
                                        <a class='button is-info' href='http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/website/login.php'>
                                            Log in
                                        </a>
                                    </div>
                                ";
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <!-- END NAV -->

    <!-- START CONTENT -->
    <section class="section">
        <div class="container">
            <div class="columns is-multiline is-centered">
                <div class="column is-half">
                    <?php
                        if (isset($_SESSION['account_id'])) {
                            $account_id = $_SESSION['account_id'];
                            echo "
                                <form method='POST' action='http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/api/process.php?add_film'>
                            
                                    <label class='label'>Title</label>
                                    <input class='input' type='text' name='title' placeholder='Title'>

                                    <label class='label'>Revenue</label>
                                    <input class='input' type='number' name='revenue' placeholder='Revenue'>
                    
                                    <button class='button is-info mt-4'>
                                        Submit
                                    </button>
                                </form>
                            ";
                        } else {
                            echo "
                                <label class='label'>Upload a new film:</label>
                                <div class='notification is-warning'>
                                    You must be logged in to upload a new film.
                                </div>
                            ";
                        }
                    ?>
                </div>
            </div>
        </div>
    </section>
    <!-- END CONTENT -->

</body>

</html>