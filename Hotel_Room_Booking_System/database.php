<?php

function connection() {
    $db_host     = "localhost";
    $db_user     = "root";
    $db_password = "";
    $db_name     = "hotel_booking";

    $connection = new mysqli($db_host, $db_user, $db_password, $db_name);

    if ($connection->connect_error) {
        die("Could not Connect Database: " . $connection->connect_error);
    }

    return $connection;
}


?>