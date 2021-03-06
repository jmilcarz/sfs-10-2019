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
      '/', '/feed', '/home', '/explore',
      '/login', '/register', '/forgotpassword', '/logout',
      '/u',
      '/messages', 
      '/notifications', 
      '/bookmarks'
   ],

   // home
      '/' => ['/', []],
      '/feed' => ['/feed', []],
      '/home' => ['/home', []],
      '/explore' => ['/explore', []],
   // auth
      '/login' => ['/login', []],
      '/register' => ['/register', []],
      '/logout' => ['/logout', []],

   // profile
      '/u' => ['/u', App::$profilePages],

   // messages
      '/messages' => ['/messages', []],

   // notifications
      '/notifications' => ['/notifications', []],

   // bookmarks
      '/bookmarks' => ['/bookmarks', []]
];

// which url
$router = [
// home
   "/" => 'home',
   "/feed" => 'home',
   "/home" => 'home',
   "/explore" => 'feed/explore',

// auth
   "/login" => 'auth/login',
   "/register" => 'auth/register',
   "/forgotpassword" => 'auth/forgotpassword',
   "/logout" => 'auth/logout',

// profile
   '/u' => 'user/profile',

// messages
   '/messages' => 'messages/index',

// notifications
   '/notifications' => 'notifications/index',

// bookmarks
   '/bookmarks' => 'feed/bookmarks'
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
      if (isset($_GET['specials'])) { 
         echo '<div id="body-container">';
            echo '<h1>User Doesn\'t exist</h1>';
         echo '</div>';
      } else {
         echo '<h1>User Doesn\'t exist</h1>';
      }
   } else {
      // user data
      $puser = DB::query('SELECT users_id, fullName as name, userName as username, email, gender, location, profileImg as avatar FROM users WHERE users_id = :userid', [':userid' => $userid])[0];
      // printing view
      if (isset($_GET['specials'])) { 
         echo '<div id="body-container">';
            require '../views/user/profile.php';
         echo '</div>';
      } else {
         require '../views/user/profile.php';
      }
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

if (isset($_GET['specials'])) {
   $specials = $_GET['specials'];
   $specials = explode(',', $specials);
   if (in_array("loadNav", $specials)) {
      if (Auth::loggedin()) {
         $userid = Auth::loggedin();
         require '../modules/essentials/nav.php';
      } else {
         require '../modules/essentials/nav-nl.php';
      }
   }
}

