<?php

session_start();

// film data
$filmId = $_GET["filmId"];
$api = "http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/api/?filmId=$filmId";
$response = file_get_contents($api);
$data = json_decode($response, true);

// review data
$api = "http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/api/?review=$filmId";
$response = file_get_contents($api);
$review_data = json_decode($response, true);

// rating data
if(!empty($review_data)){
    $api = "http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/api/?rating=$filmId";
    $response = file_get_contents($api);
    $rating_data = json_decode($response, true);
    foreach ($rating_data as $rating_array) {
        $rating=$rating_array['rating'];
    }
    $rating = round($rating * 100);
}

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
        <div class="columns">
            <div class="column is-one-quarter"></div>
            <div class="column is-half">
                <div class="columns">
                    <?php
                        foreach ($data as $filmArray) {
                            
                            $title = $filmArray['film_title'];
                            $year = $filmArray['release_year'];
                            $img = $filmArray['film_poster'];
                            $director = $filmArray['director_name'];
                            $actor_list = $filmArray['actor_list'];

                            echo " 
                                <div class='column is-two-fifths'>
                                    <img src='$img'>
                                </div>

                                <div class='column is-three-fifths has-background-light'>

                                    <h1 class='title is-1'>$title</h1>

                                    <p class='title is-3'>Released:</p>
                                    <p class='subtitle is-5'>$year</p>
                                    <p></p>
                                    <p class='title is-3'>Directed by:</p>
                                    <p class='subtitle is-5'>$director</p>
                                    <p></p>
                                    <p class='title is-3'>Starring:</p>
                                    <p class='subtitle is-5'>$actor_list</p>
                            ";
                            if (isset($rating)) {
                                echo "
                                    <p></p>
                                    <div class='is-divider' data-content='OR'></div>
                                    <p class='title is-3'>Audience Rating:</p>
                                    <p class='subtitle is-1 has-text-info'>$rating%</p>
                                </div>
                                ";
                            } else {
                                echo "
                                <p></p>
                                <div class='is-divider' data-content='OR'></div>
                                <p class='title is-3'>Audience Rating:</p>
                                <p class='subtitle is-5'>Not yet rated</p>
                                </div>
                                ";
                            }
                        }
                    ?>
                </div>
            </div>
        </div>

        <div class="columns is-multiline is-centered">
            <div class="column is-half">
                <p class='title is-3'>Reviews</p>
                <?php
                    if (empty($review_data)) {
                        echo "
                            <div class='notification is-warning'>
                                There are no reviews for this film yet.
                            </div>
                        ";
                    } else {
                        foreach ($review_data as $row) { 
                            $username = $row["username"];
                            $timestamp = $row["timestamp"];
                            $thumbs_up = $row["thumbs_up"];
                            $review_text = $row["review_text"];
                            if ($thumbs_up) {
                                echo "
                                    <article class='media'>
                                        <div class='media-content'>
                                            <div class='content'>
                                            <p>
                                                <strong>@$username</strong> <small>$timestamp</small>
                                                <br>
                                                👍 $review_text
                                            </p>
                                            </div>
                                        </div>
                                    </article>
                                ";
                            } else {
                                echo "
                                    <article class='media'>
                                        <div class='media-content'>
                                            <div class='content'>
                                            <p>
                                                <strong>@$username</strong> <small>$timestamp</small>
                                                <br>
                                                💩 $review_text
                                            </p>
                                            </div>
                                        </div>
                                    </article>
                                ";
                            }
                        }
                    }

                    if (isset($_SESSION['account_id'])) {
                        $account_id = $_SESSION['account_id'];
                        echo "
                            <form method='POST' action='http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/api/process.php?review'>
                        
                                <input type='hidden' name='account_id' value='$account_id' />
                                <input type='hidden' name='film_id' value='$filmId' />

                                <label class='label'>Post a new review:</label>
                                <textarea class='textarea' name='review_text' placeholder='Type your review here...'></textarea>

                                <button class='button is-primary mt-4' name='thumbs_up'>
                                    I liked it!
                                </button>

                                <button class='button is-danger mt-4' name='thumbs_down'>
                                    I didn't like it.
                                </button>
                            </form>
                        ";
                    } else {
                        echo "
                            <label class='label'>Post a new review:</label>
                            <div class='notification is-warning'>
                                You must be logged in to post a review.
                            </div>
                        ";
                    }
                ?>
            </div>
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