<?php
include('functions.php');
$hostname="localhost";
$username="root";
$password="cumlaude2018";
$dbname="cbir_rgb";
$connection = mysqli_connect($hostname, $username, $password, $dbname );


$query = "DELETE FROM images";
$result = mysqli_query($connection,$query);
if (!$result) die('Invalid query: ' . mysqli_error());

$query = "DELETE FROM coeffs_y";
$result = mysqli_query($connection,$query);
if (!$result) die('Invalid query: ' . mysqli_error());

$query = "DELETE FROM coeffs_i";
$result = mysqli_query($connection,$query);
if (!$result) die('Invalid query: ' . mysqli_error());

$query = "DELETE FROM coeffs_q";
$result = mysqli_query($connection,$query);
if (!$result) die('Invalid query: ' . mysqli_error());

mysqli_close($connection);

header( 'Location: index.php' ) ;

?>

