<?php

// specify data format
header('Content-Type: application/json');

// establish DB connection
include ('conn.php');

// default parameters
$fields = "film_id, film_title, release_year, film_poster";
$table = "bs_films";
$conditional = "";
$sort="";
$order="";
$upperBound = 20;
$lowerBound = 00;

// search all films
if(isset($_GET["search"])) {
    $search_term = $_GET["search"];
    $conditional = "WHERE CONVERT(`film_title` USING utf8) LIKE '$search_term%'";
}

// sort results
if(isset($_GET["sort"])) {
    $sortColumn = $_GET["sort"];
    if(isset($_GET["order"])) {
        $order = $_GET["order"];
    }
    $order = "ORDER BY $sortColumn $order";
}

// return all genre tags used in the database
if(isset($_GET["genres"])) {
    $fields = "genre_name";
    $table = "bs_genres";
    $upperBound = 10000;
    $lowerBound = 0;
}

// return detailed information for a specific film
if(isset($_GET["filmId"])){

    $filmId = $_GET["filmId"];

    $fields = "film_title, release_year, film_poster, director_name, GROUP_CONCAT(actor_name SEPARATOR ', ') AS actor_list";

    $table = "bs_films
        INNER JOIN bs_films_directors
        USING (film_id)
        INNER JOIN bs_directors
        USING (director_id)
            
        INNER JOIN bs_films_actors
        USING (film_id)
        INNER JOIN bs_actors
        USING (actor_id)"
    ;

    $conditional = "WHERE bs_films.film_id = $filmId";
}

// return films of a specific genre
if(isset($_GET["genre"])) {

    $genre = $_GET["genre"];

    $fields = "bs_films.film_id, film_title, release_year, film_poster";
    
    $table = "bs_films
        INNER JOIN bs_films_genres
        ON bs_films.film_id = bs_films_genres.film_id
        INNER JOIN bs_genres
        ON bs_films_genres.genre_id = bs_genres.genre_id
    ";

    $conditional = "WHERE genre_name IN ('$genre')";
}

// calculate range of rows to display based on page number
if(isset($_GET["page"])){
    $page = $_GET["page"];
    $lowerBound = 20 * ($page - 1);
}

// return reviews for specific film
if(isset($_GET["review"])){
    $filmId = $_GET["review"];
    $fields = "username, timestamp, thumbs_up, review_text";
    $table = "bs_reviews
        INNER JOIN bs_accounts
        USING (account_id)
    ";
    $conditional = "WHERE film_id = $filmId";
    $upperBound = 5;
    $order="ORDER BY review_id DESC";
}

// calculate audience rating
if(isset($_GET["rating"])){
    $filmId = $_GET["rating"];
    $fields = "AVG(thumbs_up) AS rating";
    $table = "bs_reviews";
    $conditional = "WHERE film_id = $filmId";
}

// construct MYSQL query
$query = "SELECT DISTINCT $fields FROM $table $conditional $order LIMIT $lowerBound, $upperBound"; 

// dispatch query, process and return response
$res = $conn->query($query);
$data = array();
while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}
echo json_encode($data);

?>