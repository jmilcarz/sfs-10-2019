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
      $userdata = DB::query('SELECT fullName, profileImg, content FROM users INNER JOIN posts ON userId = :userid AND content = :content WHERE users_id = :userid', [':userid' => Auth::loggedin(), ':content' => $content])[0];
      
      $userdata['profileImg'] = Auth::checkProfilePhoto($userdata['profileImg']);

      return ['type' => 'success', 'm' => 'Post published successfully.', 'userimg' => $userdata['profileImg'], 'userName' => $userdata['fullName'], 'content' => self::link_add($userdata['content'])];
   }

   public static function link_add($text) {
      $text = explode(" ", $text);
      $newstring = "";
      $wordplus = " ";
      $specials = ['@', '#', '!', '*', '**', '***'];
      foreach ($text as $word) {
         if (strpos(substr($word, 0, strlen($word)), '!') || strpos(substr($word, strlen($word)), '?')) {
            // $wordplus = substr($word, -1, 1);
            $word = substr($word, 0, -1);
         }
         if (substr($word, 0, 1) == "@") {
            $newstring .= "<a style='text-decoration: none; color: #3498db; cursor: pointer;' data-link='/u/".substr($word, 1)."'>". htmlspecialchars($word) . "</a>" . $wordplus;
         } else if (substr($word, 0, 1) == "#") {
            $newstring .= "<a style='text-decoration: none; color: #3498db; cursor: pointer;' data-link='/tag/".substr($word, 1)."'>".htmlspecialchars($word)."</a>" . $wordplus . "  ";
         } else if (substr($word, 0, 1) == "!") { # SUPER!
            $newstring .= "<span style='color: orange; font-weight: bold;'>" . substr(substr(htmlspecialchars($word), 1), 0, -1) . "</span>" . $wordplus . "  ";
         } else if (substr($word, 0, 3) == "***") { # UNDERLINE
            $newstring .= "<span style='text-decoration: underline;'>" . substr(substr(htmlspecialchars($word), 3), 0, -3) . "</span>" . $wordplus . "  ";
         } else if (substr($word, 0, 2) == "**") { # ITALIC
            $newstring .= "<span style='font-style: italic;'>" . substr(substr(htmlspecialchars($word), 2), 0, -2) . "</span>" . $wordplus . "  ";
         } else if (substr($word, 0, 1) == "*") { # BOLD
            $newstring .= "<span style='font-weight: bold;'>" . substr(substr(htmlspecialchars($word), 1), 0, -1) . "</span>" . $wordplus . "  ";
         } else if (strtoupper($word) == "CONGRATS" || strtoupper($word) == "CONGRATULATIONS") {
            $newstring .= "<span style='color: #eb4f4f; font-weight: bold'>" . htmlspecialchars($word) . "</span>" . $wordplus . "  ";
         } else {
            $newstring .= htmlspecialchars($word)." ";
         }
      }
      return $newstring;
   }

}
