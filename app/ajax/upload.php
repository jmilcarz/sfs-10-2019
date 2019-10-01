<?php
session_start();
// session_destroy();
$target_dir = $_SERVER['DOCUMENT_ROOT'] . "/fb/storage/pictures/";
$cstrong = true;
$imgname = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
$target_file = $target_dir . sha1($imgname) . '.' . explode('.', $_FILES["image"]["name"])[1];
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
$check = getimagesize($_FILES["image"]["tmp_name"]);
if($check !== false) {
     echo "File is an image - " . $check["mime"] . ".";
     $uploadOk = 1;
} else {
     echo "File is not an image.";
     $uploadOk = 0;
}
if (!isset($_SESSION['try'])) {
   $_SESSION['try'] = 1;
} else {
   if ($_SESSION['try'] > 2) {
      echo "Sorry, you are uploading too many photos.";
      exit();
   } else {
      $_SESSION['try'] += 1;
   }
}
if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
     echo "The photo ". basename( $_FILES["image"]["name"]). " has been uploaded.";
} else {
     echo "Sorry, there was an error uploading your photo.";
}
