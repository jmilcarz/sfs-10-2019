<?php

class App {

   public static $APP_DIR = "http://localhost/fb/";
   public static $APP_HOME_DIR = "http://localhost/fb/";
   public static $profilePages = ['/home', '/followers', '/friends'];
   public static $IMG_STORAGE = "http://localhost/sfstrue/storage/pictures/%E2%81%A9/";

   public static function PrintProfileURL($username, $view) {
      return self::$APP_DIR . '/u/' . $username . '/' . $view;
   }

   public static function PrintTagURL($tag) {
      return self::$APP_DIR . '/tag/' . $tag;
   }

   public static $LANGUAGES = ['en', 'es', 'pl'];

}
