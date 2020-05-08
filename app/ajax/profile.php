<?php

if (isset($_GET['view'])) {
   require '../autoload.php';

   $view = '/'.$_GET['view']; // adding '/' to match with app::$profilePages
   if (!in_array($view, App::$profilePages)) {
      echo 'eror - wrong page name';
      exit();
   }

   require '../views/user/profile/' . substr($view, 1) . '.php';

}
