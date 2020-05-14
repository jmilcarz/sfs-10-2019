<?php

require '../autoload.php';

if (Auth::loggedin()) {
   if (!isset($_GET['a'])) {
      // load feed view
      require '../views/feed/feed.php';
   } else {
      // handle all ajax requests
      $a = $_GET['a'];
      if (in_array($a, Posts::$availableActions)) {
         if ($a == "sendForm") {
            $content = $_POST['content'];
            $userid = $_POST['userid'];
            $privacy = $_POST['privacy'];

            if ($userid == Auth::loggedin()) {
               $r = Posts::create($content, $userid, $privacy);
               http_response_code(200);
               echo json_encode($r);
               // if ($r['t'] == 'success') {
               //    echo '<span style="color: green;">'.$r['m'].'</span>';
               // } else {
               //    echo '<span style="color: red;">'.$r['m'].'</span>';
               // }
            }
         } else if ($a == "fetchFeed") {
            $start = (int)$_GET['start'];
            if (isset($_GET['mode'])) {
               $mode = Security::check($_GET['mode']);
               if ($mode == 'tag' && isset($_GET['tag'])) {
                  $tag = Security::check($_GET['tag']);
                  $posts = DB::query('SELECT posts.id, posts.userId, posts.content, posts.likes, posts.comments, posts.shares, posts.createdAt, posts.privacy, users.profileImg, users.users_id AS users_id FROM posts, users, followers WHERE users.users_id = posts.userId AND posts.privacy = 1 AND followers.userid = :userid AND posts.userid = followers.followerid AND posts.tags LIKE CONCAT("%", :tag, "%") ORDER BY posts.created_at DESC LIMIT 10 OFFSET '.$start, [':userid' => $user['id'], ':tag'=>$tag]);
               } else if ($mode == 'profile' && isset($_GET['userid'])) {
                  $profile_id = Security::check($_GET['userid']);
                  $posts = DB::query('SELECT posts.id, posts.userid, posts.content, posts.likes, posts.comments, posts.created_at, posts.privacy, users.profileImg, users.users_id AS users_id FROM posts, users WHERE posts.userid = :profileid AND users.id = :profileid ORDER BY posts.created_at DESC LIMIT 5 OFFSET '.$start, [':profileid' => $profile_id]);
               }
            } else {
               // $posts = DB::query('SELECT posts.id, posts.userId, posts.content, posts.likes, posts.comments, posts.createdAt, posts.privacy, users.profileId, users.users_id AS users_id FROM posts, users, followers WHERE users.users_id = posts.posts_userId AND posts.privacy = 1 AND followers.followers_userid = :userid AND posts.posts_authorid = followers.follower_id ORDER BY posts.createdAt DESC LIMIT 5 OFFSET '.$start, [':userid' => $user['id']]);
               $posts = DB::query('SELECT posts.id, posts.userId, posts.content, posts.likes, posts.comments, posts.createdAt, posts.privacy, users.profileImg, users.users_id AS users_id FROM posts, users WHERE users_id = userId AND privacy = 1 OR (userId = users_id AND privacy LIKE "%") ORDER BY posts.createdAt DESC LIMIT 5 OFFSET '.$start, [':userid' => $user['id']]);
            }

            $response = array();
            foreach ($posts as $post) {
               // profile img
               $post['avatar'] = Auth::checkProfilePhoto($post['avatar']);

               // author info
               $author = DB::query('SELECT fullName, userName FROM users WHERE users_id = :id', [':id'=>$post['users_id']])[0];

               // post likes
               if ($post['likes'] == 1) {
                  $likes = '1 like';
               } else {
                  $likes = $post['likes'] . ' likes';
               }

               // number of comments
               if ($post['comments'] == 1) {
                  $Ncomment = '1 Comment';
               } else {
                  $Ncomment = $post['comments'] . ' comments';
               }

               $row = [
                  'PostId'=> $post['id'],'PostBody'=>Posts::link_add($post['content']),
                  'AuthorName'=> $author['fullName'], 'PostDate'=>$post['createdAt'],
                  'PostLikes'=> $likes, 'CommentsNumber'=>$Ncomment,
                  'AuthorImg'=> $post['avatar'], 'AuthorId'=>$post['users_id'],
                  'ProfileURL' => App::PrintProfileURL($author['username'], 'home')
               ];
               array_push($response, $row);
            }

            http_response_code(200);
            echo json_encode($response);
         }
      }


   }

} else {
   require '../views/auth/home.php';
}
