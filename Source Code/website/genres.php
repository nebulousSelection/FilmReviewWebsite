<?php

session_start();

// request genre tags
$genreApiRequest = "http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/api/?genres";
$response = file_get_contents($genreApiRequest);
$genreTags = json_decode($response, true);

// default parameters
$page = 1;
$genre="";

// process page data
if (isset($_GET["page"])) {
    $page = $_GET["page"];
}
$nextPage = $page + 1;
$previousPage = $page - 1;

// construct API request
if (isset($_GET["genre"])) {
    $genre = $_GET["genre"];
    $genre = str_replace(' ', '%20', $genre);
    $moviesApiRequest = "http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/api/?page=$page&genre=$genre";
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

    <!-- START GENRE TAGS -->
    <section class="section">
        <div class="container">
            <div class="field is-grouped is-grouped-multiline">
                <?php
                    foreach ($genreTags as $genreArray) {

                        $genreTag = $genreArray['genre_name'];
                        if (isset($_GET["genre"])) {
                            $genre = $_GET["genre"];
                        }

                        if($genre == $genreTag) {
                            echo " 
                            
                                <a class='button is-danger is-active m-1' href='http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/website/genres.php';>
                                    $genreTag
                                </a>
                            
                        "; 
                        } else {
                            echo " 
                            
                                <a class='button m-1' href='http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/website/genres.php?genre=$genreTag';>
                                    $genreTag
                                </a>
                            
                        "; 
                        }   
                    }
                ?>
            </div>  
        </div>
    </section>
    <!-- END GENRE TAGS -->

    <!-- START CONTENT -->
    <section class="section">
        <div class="container">
            <div class="columns is-multiline">

                <?php
                foreach ($data as $filmArray) {

                    $id = $filmArray['film_id'];
                    $title = $filmArray['film_title'];
                    $year = $filmArray['release_year'];
                    $img = $filmArray['film_poster'];

                    echo " 
                        <div class='column is-one-quarter'>
                            <a href=display.php?filmId=$id>
                                <div class='card'>

                                    <img src='$img'>

                                    <div class='card-content'>

                                        <p class='title'>
                                            $title
                                        </p>

                                        <p class='subtitle'>
                                            $year
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </div> 
                    
                    ";
                }
                ?>
            </div>

            <nav class="level">

                <!-- Left side -->
                <div class="level-left">
                    <p class="level-item">

                        <?php
                        if (isset($_GET["page"])) {
                            if ($_GET["page"] > 1) {
                                if (isset($_GET["genre"])) {
                                    echo "
                                        <a href='http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/website/genres.php?page=$previousPage&genre=$genre';>
                                            <button class='button is-link is-rounded'>Previous Page</button>
                                        </a>
                                    ";
                                } else {
                                    echo "
                                        <a href='http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/website/genres.php?page=$previousPage';>
                                            <button class='button is-link is-rounded'>Previous Page</button>
                                        </a>
                                    ";
                                }
                            }
                        }
                        ?>
                    </p>
                </div>

                <!-- Right side -->
                <div class="level-right">
                    <p class="level-item">

                        <?php
                        if (isset($_GET["genre"])) {
                            echo "
                                <a href='http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/website/genres.php?page=$nextPage&genre=$genre';>
                                    <button class='button is-link is-rounded'>Next Page</button>
                                </a>
                            ";
                        } else {
                            echo "
                                <a href='http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/website/genres.php?page=$nextPage';>
                                    <button class='button is-link is-rounded'>Next Page</button>
                                </a>
                            ";
                        }
                        ?>
                    </p>
                </div>
            </nav>

        </div>
    </section>
    <!-- END CONTENT -->

    <!-- START FOOTER -->
    <footer class="footer">
        <div class="container">
            <div class="content has-text-centered">
                <p>
                    BingeSpark, created by <strong>Jonathan Foster</strong> using the Bulma framework.
                </p>
            </div>
        </div>
    </footer>
    <!-- END FOOTER -->
</body>

</html>