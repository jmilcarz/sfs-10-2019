<?php

class Posts
{

   public static $availableActions = ['sendForm', 'fetchFeed'];
   private static $postSettings = [
      'content' => [3, 128],
      'privacy' => [1, 2, 3, 4]
   ];

   public static function create($content, $userid, $privacy) {
      if (strlen($content) >= self::$postSettings['content'][1]) {
         return ['type' => 'error', 'm' => 'Post is too long ('.self::$postSettings['content'][1].' characters max)'];
      } else if (strlen($content) <= self::$postSettings['content'][0]) {
         return ['type' => 'error', 'm' => 'Post is too short ('.self::$postSettings['content'][0].' characters min)'];
      } else if ($userid != Auth::loggedin()) {
         return ['type' => 'error', 'm' => 'Invalid user'];
      } else if (!in_array($privacy, self::$postSettings['privacy'])) {
         return ['type' => 'error', 'm' => 'Invalid privacy'];
      }

      DB::query('INSERT INTO posts VALUES (NULL, :userid, :content, :privacy, NOW(), 0, 0, 0)', [':userid' => $userid, ':content' => $content, ':privacy' => $privacy]);

      return ['type' => 'success', 'm' => 'Post publish successfully.'];
   }

   public static function link_add($text) {
      $text = explode(" ", $text);
      $newstring = "";
      $wordplus = "";
      $specials = ['@', '#', '[s]', '[b]', '[i]', '[u]'];
      foreach ($text as $word) {
         if ((strpos($word, '!') && strlen($word) > 1) || (strpos($word, '?') && strlen($word) > 1) || (strpos($word, '.') && strlen($word) > 1) || (strpos($word, ',') && strlen($word) > 1)) {
            if (in_array($word, $specials)) {
               $wordplus = substr($word, -1);
               $word = substr($word, 0, -1);
            }
         }
         if (substr($word, 0, 1) == "@") {
            $newstring .= "<a style='text-decoration: none; color: #3498db' href='" . App::$APP_DIR . "/profile/".substr($word, 1)."'>".htmlspecialchars($word) . "</a>" . $wordplus;
         } else if (substr($word, 0, 1) == "#") {
            $newstring .= "<a style='text-decoration: none; color: #3498db' href='" . App::$APP_DIR . "/tag/".substr($word, 1)."'>".htmlspecialchars($word)."</a>" . $wordplus . "  ";
         } else if (substr($word, 0, 3) == "[s]") { # SUPER!
            $newstring .= "<span style='color: orange; font-weight: bold;'>" . substr(substr(htmlspecialchars($word), 3), 0, -4) . "</span>" . $wordplus . "  ";
         } else if (substr($word, 0, 3) == "[b]") { # BOLD
            $newstring .= "<span style='font-weight: bold;'>" . substr(substr(htmlspecialchars($word), 3), 0, -4) . "</span>" . $wordplus . "  ";
         } else if (substr($word, 0, 3) == "[i]") { # ITALIC
            $newstring .= "<span style='font-style: italic;'>" . substr(substr(htmlspecialchars($word), 3), 0, -4) . "</span>" . $wordplus . "  ";
         } else if (substr($word, 0, 3) == "[u]") { # UNDERLINE
            $newstring .= "<span style='text-decoration: underline;'>" . substr(substr(htmlspecialchars($word), 3), 0, -4) . "</span>" . $wordplus . "  ";
         } else if (strtoupper($word) == "CONGRATS" || strtoupper($word) == "CONGRATULATIONS") {
            $newstring .= "<span style='color: red'>" . htmlspecialchars($word) . "</span>" . $wordplus . "  ";
         } else {
            $newstring .= htmlspecialchars($word)." ";
         }
      }
      return $newstring;
   }

}
