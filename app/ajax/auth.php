<?php
require "../autoload.php";

if (!isset($_GET["t"])) {
   die("access denied!");
}

$type = $_GET["t"];

if ($type == "logout") {
   session_destroy();
   Auth::logout();
   die();

} else if ($type == "login") {
   $email = $_POST["email"];
   $password = $_POST["password"];
   $token = $_POST["token"];

   $r = Auth::login($email, $password);

} else if ($type == "register") {
   $fname = $_POST["fname"];
   $lname = $_POST["lname"];
   $username = $_POST["username"];
   $email = $_POST["email"];
   $password = $_POST["password"];
   $passwordRepeat = $_POST["passwordRepeat"];
   $gender = $_POST["gender"];

   $r = Auth::register($fname, $lname, $username, $email, $password, $passwordRepeat, $gender);

}

echo json_encode($r);