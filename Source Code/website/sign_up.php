<?php

session_start();

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
    <div class="columns is-centered">
        <div class="m-6">
            <?php
                if (isset($_GET["success"])) {
                    echo "
                        <div class='notification'>
                            Account successfully registered. 
                        </div>
                    ";
                } else {
                    echo "
                        <form method='POST' action='http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/api/process.php?sign_up'>
                            
                            <label class='label'>Username</label>
                            <input class='input' type='username' name='username' placeholder='Username'>

                            <label class='label'>Email</label>
                            <input class='input' type='email' name='email' placeholder='Email'>

                            <label class='label'>Password</label>
                            <input class='input' type='password' name='password' placeholder='Password'>

                            <button class='button is-success mt-4' type='submit'>
                                Sign-up
                            </button>
                        </form>
                    ";
                }
                
                if (isset($_GET["error"])) {
                    echo "
                        <div class='notification'>
                            That username and/or email is</br>already in use. Please try another.
                        </div>
                    ";
                }
            ?>
            
        </div>
    </div>
    <!-- END CONTENT -->

</body>

</html>