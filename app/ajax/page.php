<?php

if (!isset($_GET['p'])) {
   echo 'An error occured!';
   exit();
}

$url = $_GET['p'];

// correct urls
$urls = [
   ['/', '/feed', '/u', '/logout'], # <- avaible urls
   '/' => ['/', []],
   '/feed' => ['/feed', []],
   '/u' => ['/u', ['/home', '/notes', '/friends', '/followers', '/settings']],
   '/logout' => ['/logout', []]
];

// which url
$router = [
   "/" => 'home',
   "/feed" => 'home',
   "/logout" => 'logout',
   '/u' => 'user/profile'
];

// echo 'url: ' . $url . '<br>';
// print_r($urls[$url]); echo '<br>';

if ($url != "/") {
   $params = explode('/', $url);
   // echo 'params: '; print_r($params);
   $params[1] = '/'.$params[1];
} else {
   $params = [0, '/'];
}

if (!in_array($params[1], $urls[0])) {
   echo '<h1>404</h1>';
   exit();
}

if ($params[1] == '/u') { # profile router
   if (count($params) < 3) {
      echo '<h1>User Id is required!</h1>';
      exit();
   }
   $userid = (is_numeric($params[2]) ? $params[2] : 'error');
   if ($userid == 'error') {
      echo '<h1>User Id is required!</h1>';
      exit();
   }
   $view = ($params[3] ? $params[3] : "home");

   echo '<div id="body-container">';
      echo '<h1>User Profile ('.$userid.')</h1><hr>';
      foreach ($urls[$params[1]][1] as $v) {
         echo '<a href="#" data-link="/u/'.$userid.'/' . substr($v, 1) . '">' . substr($v, 1) . '</a> ';
      }
      if ($view == 'settings') {
         require '../views/user/settings.php';
      } else {
         echo '<hr><h2>'.$view.'</h2>';
      }
   echo '</div>';

} else { // load view
   echo '<div id="body-container">';
      require '../views/' . $router[$url] . '.php';
   echo '</div>';
}

if (isset($_GET['specials'])) { // configure specials such as load nav or sidebar etc.
   $specials = $_GET['specials'];
   $specials = explode(',', $specials);
   // print_r($specials);
   if (in_array("loadNav", $specials)) {
      require '../modules/essentials/nav.php';
   }
   if (in_array("loadsidebar", $specials)) {
      echo 'sidebar here!';
   }
}
