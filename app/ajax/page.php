<?php

if (!isset($_GET['p'])) {
   echo 'An error occured!';
   exit();
}

$url = $_GET['p'];
require '../autoload.php';

// correct urls
$urls = [
   [ # avaible urls
      '/', '/feed', '/home',
      '/login', '/register', '/forgotpassword', '/logout',
      '/u',
      '/messages'
   ],

   // home
      '/' => ['/', []],
      '/feed' => ['/feed', []],
      '/home' => ['/home', []],
   // auth
      '/login' => ['/login', []],
      '/register' => ['/register', []],
      '/logout' => ['/logout', []],

   // profile
      '/u' => ['/u', App::$profilePages],

   // messages
      '/messages' => ['/messages', []]

];

// which url
$router = [
// home
   "/" => 'home',
   "/feed" => 'home',
   "/home" => 'home',

// auth
   "/login" => 'auth/login',
   "/register" => 'auth/register',
   "/forgotpassword" => 'auth/forgotpassword',
   "/logout" => 'auth/logout',

// profile
   '/u' => 'user/profile',

// messages
   '/messages' => 'messages/index'
];

// for not logged
$unlogged = [ "/login", "/register", "/forgotpassword" ];
$forlogged = [ "/logout" ];

if ($url != "/") {
   $params = explode('/', $url);
   $params[1] = '/'.$params[1];
} else {
   $params = [0, '/'];
}


   echo '<div style="position: fixed; bottom: 0; left: 50%; transform: translateX(-50%); background: rgb(233, 159, 61); width: 360px;"><p>url: '.$url.'</p><p>';
   print_r($urls[$params[1]]);
   echo '</p></div>';


if (!in_array($params[1], $urls[0])) {
   require '../views/errors/e404.php';
   exit();
}

if ($params[1] == '/u') { # profile router
   $view = ($params[3] ? $params[3] : "home");

   if (count($params) < 3) {
      echo '<h1>User Id is required!</h1>';
      exit();
   }
   $userid = (is_numeric($params[2]) ? $params[2] : 'error');
   if ($userid == 'error') {
      echo '<h1>User Id is required!</h1>';
      exit();
   }

   if (!DB::query('SELECT users_id FROM users WHERE users_id = :userid', [':userid' => $userid])[0]['users_id']) {
      echo '<div id="body-container">';
         echo '<h1>User Doesn\'t exist</h1>';
      echo '</div>';
   } else {
      // user data
      $puser = DB::query('SELECT users_id, fullName as name, userName as username, email, gender, location, profileImg as avatar FROM users WHERE users_id = :userid', [':userid' => $userid])[0];
      // printing view
      echo '<div id="body-container">';
         require '../views/user/profile.php';
      echo '</div>';
   }


} else { // load view

   if (in_array($url, $unlogged)) {
      if (!Auth::loggedin()) {
         echo '<div id="body-container">';
            require '../views/' . $router[$url] . '.php';
         echo '</div>';
      } else {
         echo 'auth 1';
      }
   } else if (in_array($url, $forlogged)) {
      if (Auth::loggedin()) {
         echo '<div id="body-container">';
            require '../views/' . $router[$url] . '.php';
         echo '</div>';
      } else {
         echo 'auth 2';
      }
   } else {
      echo '<div id="body-container">';
         require '../views/' . $router[$url] . '.php';
      echo '</div>';
   }

}

if (isset($_GET['specials'])) { // configure specials such as load nav or sidebar etc.
   $specials = $_GET['specials'];
   $specials = explode(',', $specials);
   // print_r($specials);
   if (in_array("loadNav", $specials)) {
      if (Auth::loggedin()) {
         $userid = Auth::loggedin();
         require '../modules/essentials/nav.php';
      } else {
         require '../modules/essentials/nav-nl.php';
      }

   }
   if (in_array("loadsidebar", $specials)) {
      echo 'sidebar here!';
   }
}
