<?php

if (!isset($_GET['p'])) {
   echo 'An error occured!';
   exit();
}

$url = $_GET['p'];
// $url = substr($url, 1);

$urls = [
   "/" => 'home',
   "/feed" => 'home',
   "/profile" => 'user/profile',
   "/logout" => 'logout',
   '/profile/settings' => 'user/settings'
];

// echo $url;
//
// if (!in_array($url, $urls)) {
//    echo '404. Page not found.';
//    exit();
// }

if (isset($_GET['specials'])) { # configure specials such as load nav or sidebar etc.
   $specials = $_GET['specials'];
   $specials = explode(',', $specials);
   print_r($specials);
   if (in_array("loadNav", $specials)) {
      require '../modules/essentials/nav.php';
   }
   if (in_array("loadsidebar", $specials)) {
      echo 'sidebar here!';
   }
} else { # if we're not loading any specials
   // echo $url . '<br>';
   echo '';
}




echo '<div id="body-container">';
   require '../views/' . $urls[$url] . '.php';
echo '</div>';
