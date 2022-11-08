<?php

ini_set ( 'max_execution_time', 1200);

include ('database_connection.php');
echo "Connected to database <br>";

$filepath = "data.csv";

//bs_films
$f = fopen($filepath, 'r');
if ($f == false){

    echo "Cannot find file <br>";
    exit();

}else{
    
    echo "File found <br>"; 

    while(($row = fgetcsv($f)) !== false){

        //assign variables
        $title_orig = $row[0];
        $year = $row[4];
        $runtime = $row[5];
        $revenue = $row[6];

        //real escapes for single apostrophes in strings
        $title = mysqli_real_escape_string($mysqli, $title_orig);

        //something to catch multiples e.g. index only the first director


        //statements for catching NULL values
            // if(empty($title)){

            //     $title = "title unknown";

            // }

        $check = "SELECT * FROM bs_films WHERE film_title='$title' ";

        $res = $mysqli->query($check);

        if(!$res){
            echo $mysqli->error;
            echo $check;
            exit();
        }

        if($res->num_rows == 0){

            $string = str_replace(" ", "_", $title_orig);
            $apiKey = "dd2a00cb";

            echo "<p>$string</p>";

            $endp = "http://www.omdbapi.com/?t=$string&apikey=$apiKey";
        
            echo "<p> $endp </p>";

            $respond = file_get_contents($endp);
        
            $movie = json_decode($respond, true);
        
            $img = $movie["Poster"];
        
            $insert = "INSERT INTO `bs_films` (`film_id`, `film_title`, `release_year`, `film_runtime`, `boxoffice_revenue`, `film_poster`) VALUES (NULL, '$title', '$year', '$runtime', '$revenue', '$img')";
            
            echo "<p> $insert </p>";

            $res = $mysqli->query($insert);

            if(!$res){
                echo "$mysqli->error <br>";
                echo $insert;
                exit();
            }
        }
    }
// delete column headers which were ingested as the first row of the table
$delete = "DELETE FROM `bs_films` LIMIT 1";
$res = $mysqli->query($delete);
if(!$res){
    echo "<p>$mysqli->error</p>";
    echo "<p>$insert</p>";
    exit();
}
}

//bs_directors
$f = fopen($filepath, 'r');
if ($f == false){

    echo "Cannot find file <br>";
    exit();

}else{
    
    echo "File found <br>"; 

    while(($row = fgetcsv($f)) !== false){

        //assign variables
        $director = $row[2];

        //real escapes for single apostrophes in strings
        $director = mysqli_real_escape_string($mysqli, $director);

        $check = "SELECT * FROM bs_directors WHERE director_name='$director' ";

        $res = $mysqli->query($check);

        if(!$res){
            echo $mysqli->error;
            echo $check;
            exit();
        }

        if($res->num_rows == 0){
            
            $insert = "INSERT INTO `bs_directors` (`director_id`, `director_name`) VALUES (NULL, '$director')";
            
            $res1 = $mysqli->query($insert);

            if(!$res1){
                echo "$mysqli->error <br>";
                echo $insert;
                exit();
            }

            $inserted_id = $mysqli->insert_id;

            echo "<p>$director added in to director table with id $inserted_id</p>";
        }
    }
}

//bs_films_directors
$f = fopen($filepath, 'r');
if ($f == false){

    echo "Cannot find file <br>";
    exit();

}else{
    
    echo "File found <br>"; 

    while(($row = fgetcsv($f)) !== false){

        //assign variables
        $film_name = $row[0];
        $director_name = $row[2];

        //real escapes for single apostrophes in strings
        $film_name = mysqli_real_escape_string($mysqli, $film_name);
        $director_name = mysqli_real_escape_string($mysqli, $director_name);

        //fetch film id
        $find_film = "SELECT * FROM bs_films WHERE film_title='$film_name' ";
        $res = $mysqli->query($find_film);
        $film = $res->fetch_assoc();
        $film_id = $film['film_id'];

        //fetch director id
        $find_director = "SELECT * FROM bs_directors WHERE director_name='$director_name' ";
        $res1 = $mysqli->query($find_director);
        $director = $res1->fetch_assoc();
        $director_id = $director['director_id'];

        $insert = "INSERT INTO bs_films_directors (film_id, director_id) VALUES ('$film_id', '$director_id')";
        $insert_res = $mysqli->query($insert);

        if(!$insert_res){
            echo $mysqli->error;
        }
    }
}

//bs_actors
$f = fopen($filepath, 'r');
if ($f == false){

    echo "Cannot find file <br>";
    exit();

}else{
    
    echo "File found <br>"; 

    while(($row = fgetcsv($f)) !== false){

        //parse actors string from csv data
        $actor_string = $row[3];

        //eliminate single quotes
        $actor_string_clean = str_replace("'", "", $actor_string);

        //parse actors array from actors string
        $actor_array = explode(',', $actor_string_clean);

        foreach($actor_array as $actor_untrimmed){

            //eliminate whitespace
            $actor = ltrim($actor_untrimmed);

            $check = "SELECT * FROM bs_actors WHERE actor_name='$actor' ";

            $res = $mysqli->query($check);

            if(!$res){
                echo $mysqli->error;
                echo $check;
                exit();
            }

            if($res->num_rows == 0){
                
                $insert = "INSERT INTO `bs_actors` (`actor_id`, `actor_name`) VALUES (NULL, '$actor')";
                
                $res1 = $mysqli->query($insert);

                if(!$res1){
                    echo "$mysqli->error <br>";
                    echo $insert;
                    exit();
                }

                $inserted_id = $mysqli->insert_id;

                echo "<p>$actor added in to actor table with id $inserted_id</p>";
            }
        }
    }
}

//bs_films_actors
$f = fopen($filepath, 'r');
if ($f == false){

    echo "Cannot find file <br>";
    exit();

}else{
    
    echo "File found <br>"; 

    while(($row = fgetcsv($f)) !== false){

        $film_name = $row[0];
        $film_name = mysqli_real_escape_string($mysqli, $film_name);
        $find_film = "SELECT * FROM bs_films WHERE film_title='$film_name' ";
        $res = $mysqli->query($find_film);
        $film = $res->fetch_assoc();
        $film_id = $film['film_id'];
        
        $actor_string = $row[3];
        $actor_string_clean = str_replace("'", "", $actor_string);
        $actor_array = explode(',', $actor_string_clean);
        foreach($actor_array as $actor_untrimmed){
            $actor_name = ltrim($actor_untrimmed);

            $find_actor = "SELECT * FROM bs_actors WHERE actor_name='$actor_name' ";
            $res1 = $mysqli->query($find_actor);
            $actor = $res1->fetch_assoc();
            $actor_id = $actor['actor_id'];

            $insert = "INSERT INTO bs_films_actors (film_id, actor_id) VALUES ('$film_id', '$actor_id')";
            $insert_res = $mysqli->query($insert);

            if(!$insert_res){
                echo "$mysqli->error <br>";
                echo "$film_name <br>";
                echo "$actor_name <br>";
            }
        }
    }
}

//bs_genres
$f = fopen($filepath, 'r');
if ($f == false){

    echo "Cannot find file <br>";
    exit();

}else{
    
    echo "File found <br>"; 

    while(($row = fgetcsv($f)) !== false){

        //parse genres string from csv data
        $genre_string = $row[1];

        //eliminate single quotes
        $genre_string_clean = str_replace("'", "", $genre_string);

        //parse genre array from genres string
        $genre_array = explode(',', $genre_string_clean);

        foreach($genre_array as $genre_untrimmed){

            //eliminate whitespace
            $genre = ltrim($genre_untrimmed);

            $check = "SELECT * FROM bs_genres WHERE genre_name='$genre' ";

            $res = $mysqli->query($check);

            if(!$res){
                echo $mysqli->error;
                echo $check;
                exit();
            }

            if($res->num_rows == 0){
                
                $insert = "INSERT INTO `bs_genres` (`genre_id`, `genre_name`) VALUES (NULL, '$genre')";
                
                $res1 = $mysqli->query($insert);

                if(!$res1){
                    echo "$mysqli->error <br>";
                    echo $insert;
                    exit();
                }

                $inserted_id = $mysqli->insert_id;

                echo "<p>$genre added in to genre table with id $inserted_id</p>";
            }
        }
    }
}

//bs_films_genres
$f = fopen($filepath, 'r');
if ($f == false){

    echo "Cannot find file <br>";
    exit();

}else{
    
    echo "File found <br>"; 

    while(($row = fgetcsv($f)) !== false){

        $film_name = $row[0];
        $film_name = mysqli_real_escape_string($mysqli, $film_name);
        $find_film = "SELECT * FROM bs_films WHERE film_title='$film_name' ";
        $res = $mysqli->query($find_film);
        $film = $res->fetch_assoc();
        $film_id = $film['film_id'];
        
        $genre_string = $row[1];
        $genre_string_clean = str_replace("'", "", $genre_string);
        $genre_array = explode(',', $genre_string_clean);
        foreach($genre_array as $genre_untrimmed){
            $genre_name = ltrim($genre_untrimmed);

            $find_genre = "SELECT * FROM bs_genres WHERE genre_name='$genre_name' ";
            $res1 = $mysqli->query($find_genre);
            $genre = $res1->fetch_assoc();
            $genre_id = $genre['genre_id'];

            $insert = "INSERT INTO bs_films_genres (film_id, genre_id) VALUES ('$film_id', '$genre_id')";
            $insert_res = $mysqli->query($insert);

            if(!$insert_res){
                echo "$mysqli->error <br>";
                echo "$film_name <br>";
                echo "$genre_name <br>";
            }
        }
    }
}

