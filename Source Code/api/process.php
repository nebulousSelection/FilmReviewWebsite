<?php

session_start();

// connect to DB
include ('conn.php');

// sign up new account
if (isset($_GET["sign_up"])) {
    
    // sanitise and store POST data
    $username = mysqli_real_escape_string($conn, trim(htmlentities($_POST["username"])));
    $email = mysqli_real_escape_string($conn, trim(htmlentities($_POST["email"])));
    $password = mysqli_real_escape_string($conn, trim(htmlentities($_POST["password"])));

    // check if username taken
    $check = "SELECT `username` FROM `bs_accounts` WHERE `username`='$username'; ";
    $res = $conn->query($check);
    if(!$res){
        echo "<p>$conn->error</p>";
        echo "<p>$check</p>";
        exit();
    }
    if($res->num_rows > 0){
        header("Location: http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/website/sign_up.php?error");
        exit();
    }

    // check if email taken
    $check = "SELECT `email` FROM `bs_accounts` WHERE `email`='$email'; ";
    $res = $conn->query($check);
    if(!$res){
        echo "<p>$conn->error</p>";
        echo "<p>$check</p>";
        exit();
    }
    if($res->num_rows > 0){
        header("Location: http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/website/sign_up.php?error");
        exit();
    }

    // salt and hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // insert form data to bs_accounts table
    $insert = "INSERT INTO `bs_accounts` (`account_id`, `username`, `email`, `password`, `admin`) VALUES (NULL, '$username', '$email', '$hashed_password', 0);";
    $insertRow = $conn->query($insert);   
    if(!$insertRow){
        echo "<p>$conn->error</p>";
        echo "<p>$insert</p>";
        exit();
    }

    // retrieve account_id and admin_status
    $check = "SELECT * FROM `bs_accounts` WHERE `email`='$email'; ";
    $res = $conn->query($check);
    if(!$res){
        echo "<p>$conn->error</p>";
        echo "<p>$check</p>";
        exit();
    }
    while ($row = $res->fetch_assoc()){
        $account_id=$row['account_id'];
        $admin_status=$row['admin'];
    }

    // create new SESSION variable
    $_SESSION['username'] = $username;
    $_SESSION['account_id'] = $account_id;

    // redirect user
    header("Location: http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/website/sign_up.php?success");
    exit();
}

// login to existing account
if (isset($_GET["login"])) {
    
    // sanitise and store POST data
    $email = mysqli_real_escape_string($conn, trim(htmlentities($_POST["email"])));
    $password = mysqli_real_escape_string($conn, trim(htmlentities($_POST["password"])));

    // check for account with matching email
    $check = "SELECT * FROM `bs_accounts` WHERE `email`='$email'; ";
    $res = $conn->query($check);
    if(!$res){
        echo "<p>$conn->error</p>";
        echo "<p>$check</p>";
        exit();
    }

    // fetch username and encrypted password
    while ($row = $res->fetch_assoc()){
        $account_id=$row['account_id'];
        $username=$row['username'];
        $hashed_password=$row['password'];
        $admin_status=$row['admin'];
    }
    
    // no account with matching email
    if($res->num_rows == 0){
        header("Location: http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/website/login.php?account_does_not_exist");
        exit();
    }

    // authenticate password
    if (password_verify($password, $hashed_password)) {

        // create new SESSION variable
        $_SESSION['username'] = $username;
        $_SESSION['account_id'] = $account_id;
        $_SESSION['admin_status'] = $admin_status;

        // redirect user
        header("Location: http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/website/login.php?success");
        
        exit();
    } else {

        // redirect user
        header("Location: http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/website/login.php?incorrect_password");

        exit();
    }
}

// logout of existing account
if (isset($_GET["logout"])) {
    
    session_destroy();

    // redirect user
    header("Location: http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/website");

    exit();
}

// process search-term
if(isset($_GET["search"])) {
    
    $search_term = mysqli_real_escape_string($conn, trim(htmlentities($_POST["search_term"])));

    // redirect user
    header("Location: https://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/website?search=$search_term");

    exit();
}

// upload user review
if(isset($_GET["review"])) {

    // sanitise and store POST data
    $account_id = mysqli_real_escape_string($conn, trim(htmlentities($_POST["account_id"])));
    $film_id = mysqli_real_escape_string($conn, trim(htmlentities($_POST["film_id"])));
    $review_text = mysqli_real_escape_string($conn, trim(htmlentities($_POST["review_text"])));
    if (isset($_POST["thumbs_up"])) {
        $thumbs_up = 1;
    } else {
        $thumbs_up = 0;
    }

    // upload user review to db
    $insert = "INSERT INTO `bs_reviews` 
        (`review_id`, `account_id`, `film_id`, `thumbs_up`, `review_text`) 
        VALUES (NULL, '$account_id', '$film_id', '$thumbs_up', '$review_text');
    ";
    $res = $conn->query($insert);
    if(!$res){
        echo "<p>$conn->error</p>";
        echo "<p>$insert</p>";
        exit();
    }
    
    header("Location: http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/website/display.php?filmId=$film_id");
    exit();
    
}

// include a new film in the db
if(isset($_GET["add_film"])) {

    // sanitise and store POST data
    $title = mysqli_real_escape_string($conn, trim(htmlentities($_POST["title"])));
    $revenue = mysqli_real_escape_string($conn, trim(htmlentities($_POST["revenue"])));
    $string = str_replace(" ", "_", $title);

    echo "<p>$title</p>";

    $apiKey = "dd2a00cb";
    $endp = "http://www.omdbapi.com/?t=$string&apikey=$apiKey";

    echo "<p>$endp</p>";

    $respond = file_get_contents($endp);
    $movie = json_decode($respond, true);

    // parse data
    $year = $movie["Year"];
    $runtime = $movie["Runtime"];
    $director = $movie["Director"];
    $actors = $movie["Actors"];
    $genres = $movie["Genre"];
    $img = $movie["Poster"];

    // remove units from $runtime
    $arr = explode(' ',trim($runtime));
    $runtime = $arr[0];

    echo "<p>$year</p>";
    echo "<p>$runtime</p>";
    echo "<p>$director</p>";
    echo "<p>$actors</p>";
    echo "<p>$genres</p>";

    // bs_films
    $check = "SELECT * FROM bs_films WHERE film_title='$title'";
    $res = $conn->query($check);
    if(!$res){
        echo "<p>$conn->error</p>";
        echo "<p>$check</p>";
        exit();
    }
    if($res->num_rows == 0){
        $insert = "INSERT INTO `bs_films` 
            (`film_id`, `film_title`, `release_year`, `film_runtime`, `boxoffice_revenue`, `film_poster`) 
            VALUES (NULL, '$title', '$year', '$runtime', '$revenue', '$img')
        ";
        $res = $conn->query($insert);
        if(!$res){
            echo "<p>$conn->error</p>";
            echo "<p>$insert</p>";
            exit();
        }
    } else {
        $film = $res->fetch_assoc();
        $film_id = $film['film_id']; 
        header("Location: http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/website/display.php?filmId=$film_id");
        exit();
    }
    
    // bs_directors
    $check = "SELECT * FROM bs_directors WHERE director_name='$director'";
    $res = $conn->query($check);
    if(!$res){
        echo $conn->error;
        echo $check;
        exit();
    }
    if($res->num_rows == 0){
        $insert = "INSERT INTO `bs_directors` 
            (`director_id`, `director_name`) 
            VALUES (NULL, '$director')
        ";
        $res = $conn->query($insert);
        if(!$res){
            echo "<p>$conn->error</p>";
            echo "<p>$insert</p>";
            exit();
        }
    }

    // bs_films_directors
    // fetch film id
    $find_film = "SELECT * FROM bs_films WHERE film_title='$title' ";
    $res = $conn->query($find_film);
    $film = $res->fetch_assoc();
    $film_id = $film['film_id']; 
    // fetch director id
    $find_director = "SELECT * FROM bs_directors WHERE director_name='$director' ";
    $res = $conn->query($find_director);
    $dir = $res->fetch_assoc();
    $director_id = $dir['director_id'];
    $insert = "INSERT INTO bs_films_directors 
        (film_id, director_id) 
        VALUES ('$film_id', '$director_id')
    ";
    $res = $conn->query($insert);
    if(!$res){
        echo "<p>$conn->error</p>";
        echo "<p>$insert</p>";
        exit();
    }

    // bs_actors
    // parse actors array from actors string
    $actor_array = explode(',', $actors);
    foreach($actor_array as $actor){
        $check = "SELECT * FROM bs_actors WHERE actor_name='$actor'";
        $res = $conn->query($check);
        if(!$res){
            echo $conn->error;
            echo $check;
            exit();
        }
        if($res->num_rows == 0){
            $insert = "INSERT INTO `bs_actors` 
                (`actor_id`, `actor_name`) 
                VALUES (NULL, '$actor')
            ";
            $res = $conn->query($insert);
            if(!$res){
                echo "<p>$conn->error</p>";
                echo "<p>$insert</p>";
                exit();
            }
        }
    }

    // bs_films_actors
    foreach($actor_array as $actor){
        // fetch actor id
        $find_actor = "SELECT * FROM bs_actors WHERE actor_name='$actor' ";
        $res = $conn->query($find_actor);
        $act = $res->fetch_assoc();
        $actor_id = $act['actor_id'];
        $insert = "INSERT INTO bs_films_actors 
            (film_id, actor_id) 
            VALUES ('$film_id', '$actor_id')
        ";
        $res = $conn->query($insert);
        if(!$res){
            echo "<p>$conn->error</p>";
            echo "<p>$insert</p>";
            exit();
        }
    }

    // bs_genres
    // parse genres array from genres string
    $genre_array = explode(',', $genres);
    foreach($genre_array as $genre){
        $check = "SELECT * FROM bs_genres WHERE genre_name='$genre'";
        $res = $conn->query($check);
        if(!$res){
            echo "<p>$conn->error</p>";
            echo "<p>$check</p>";
            exit();
        }
        if($res->num_rows == 0){
            $insert = "INSERT INTO `bs_genres` 
                (`genre_id`, `genre_name`) 
                VALUES (NULL, '$genre')
            ";
            $res = $conn->query($insert);
            if(!$res){
                echo "<p>$conn->error</p>";
                echo "<p>$insert</p>";
                exit();
            }
        }
    }

    // bs_films_genres
    foreach($genre_array as $genre){
        // fetch genre id
        $find_genre = "SELECT * FROM bs_genres WHERE genre_name='$genre' ";
        $res = $conn->query($find_genre);
        $gen = $res->fetch_assoc();
        $genre_id = $gen['genre_id'];
        $insert = "INSERT INTO bs_films_genres 
            (film_id, genre_id) 
            VALUES ('$film_id', '$genre_id')
        ";
        $res = $conn->query($insert);
        if(!$res){
            echo "<p>$conn->error</p>";
            echo "<p>$insert</p>";
            exit();
        }
    }
    // redirect user
    header("Location: http://jfoster13.webhosting6.eeecs.qub.ac.uk/bingespark/website/display.php?filmId=$film_id");

    exit();
}

?>