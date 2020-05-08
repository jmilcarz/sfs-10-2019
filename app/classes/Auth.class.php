<?php

class Auth
{
   public static $cookie = 'SFS';

   public function logout() {
      DB::query('DELETE FROM login_tokens WHERE lt_userId=:userid', array(':userid'=>self::loggedin()));
      self::deleteCookies();
   }

   public static function loggedin() {
      if (isset($_COOKIE['' . self::$cookie . ''])) {
         if (DB::query('SELECT lt_userId FROM login_tokens WHERE lt_token=:token', [':token'=>sha1($_COOKIE['' . self::$cookie . ''])])) {
            $userid = DB::query('SELECT lt_userId FROM login_tokens WHERE lt_token=:token', [':token'=>sha1($_COOKIE['' . self::$cookie . ''])])[0]['lt_userId'];
            if (isset($_COOKIE['' . self::$cookie . '_'])) {
               return $userid;
            } else {
               $cstrong = true;
               $token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
               DB::query('INSERT INTO login_tokens VALUES (\'\', :user_id, :token, NOW())', [':token'=>sha1($token), ':user_id'=>$userid]);
               ## echo ';
               DB::query('DELETE FROM login_tokens WHERE token=:token', [':token'=>sha1($_COOKIE["" . self::$cookie . ""])]);
               self::setCookies($token);
               return $userid;
            }
         }
      }
      return false;
   }

   public static function login($email, $pass) {
      # auth
         if (empty($email) || empty($pass)) {
            return ['type' => 'error', 'm' => 'All fields must be filled.'];
         } else if (!DB::query('SELECT email FROM users WHERE email=:email', [':email'=>$email])[0]['email']) {
            return ['type' => 'error', 'm' => 'Incorrect email'];
         } else if (!password_verify($pass, DB::query('SELECT password FROM users WHERE email=:email', [':email'=>$email])[0]['password'])) {
            return ['type' => 'error', 'm' => 'Password does not match.'];
         }
      # /auth

      $userId = DB::query('SELECT users_id FROM users WHERE email=:email', [':email'=>$email])[0]['users_id'];
      // create & insert login token
      $cstrong = true;
      $token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
      DB::query('INSERT INTO login_tokens VALUES (NULL, :userId, :token, NOW())', [':token'=>sha1($token), ':userId'=>$userId]);

      // set cookies
      self::setCookies($token);

      return ['type' => 'success', 'm' => 'Success'];

   }

   
   public static function register($fname, $lname, $username, $email, $pass, $passR, $gender) {

      // validate
      $fullname = $fname . " " . $lname;
      $password = password_hash($pass, PASSWORD_BCRYPT);
      $profileImg = 0;
      $dob = date("Y-m-d H:i:s");
      $location = 0;

      DB::query('INSERT INTO users VALUES (NULL, :fullname, :uname, :email, :pass, :gender, :loc, :dob, :profileimg, NOW())', 
      [':fullname'=>$fullname, ':uname'=>$username, ':email'=>$email, ':pass'=>$password, ':gender'=>$gender, ':dob'=>$dob, ':loc'=>$location,':profileimg'=>$profileImg]);

      Self::login($email, $password);
      //Mail::sendMail('Welcome to SFS!', 'Your account has been created. You can now sign in.', $email);
      //self::login($email, $pass);

      return ['type' => 'success', 'm' => 'Account has been created!'];

   }


   public function setCookies($token) {
      setcookie("" . self::$cookie . "", $token, time() + 60 * 60 * 24 * 30, '/', NULL, NULL, TRUE);
      setcookie("" . self::$cookie . "_", '1', time() + 60 * 60 * 24 * 3, '/', NULL, NULL, TRUE);
   }

   public function deleteCookies() {
      setcookie("" . self::$cookie . "", '1', time()-3600);
      setcookie("" . self::$cookie . "_", '1', time()-3600);
   }


   function getIPAddress() {
      if (!empty($_SERVER['HTTP_CLIENT_IP']) && validate_ip($_SERVER['HTTP_CLIENT_IP'])) {
         return $_SERVER['HTTP_CLIENT_IP'];
      }

      // check for IPs passing through proxies
      if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
         // check if multiple ips exist in var
         if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') !== false) {
            $iplist = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($iplist as $ip) {
               if (validate_ip($ip))
                  return $ip;
            }
         } else {
            if (validate_ip($_SERVER['HTTP_X_FORWARDED_FOR']))
               return $_SERVER['HTTP_X_FORWARDED_FOR'];
         }
      }
      if (!empty($_SERVER['HTTP_X_FORWARDED']) && validate_ip($_SERVER['HTTP_X_FORWARDED']))
         return $_SERVER['HTTP_X_FORWARDED'];
      if (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && validate_ip($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
         return $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
      if (!empty($_SERVER['HTTP_FORWARDED_FOR']) && validate_ip($_SERVER['HTTP_FORWARDED_FOR']))
         return $_SERVER['HTTP_FORWARDED_FOR'];
      if (!empty($_SERVER['HTTP_FORWARDED']) && validate_ip($_SERVER['HTTP_FORWARDED']))
         return $_SERVER['HTTP_FORWARDED'];

      // return unreliable ip since all else failed
      return $_SERVER['REMOTE_ADDR'];
   }

   function validate_ip($ip) {
      if (strtolower($ip) === 'unknown') {
         return false;
      }

      $ip = ip2long($ip);

      if ($ip !== false && $ip !== -1) {
         $ip = sprintf('%u', $ip);
         if ($ip >= 0 && $ip <= 50331647) return false;
         if ($ip >= 167772160 && $ip <= 184549375) return false;
         if ($ip >= 2130706432 && $ip <= 2147483647) return false;
         if ($ip >= 2851995648 && $ip <= 2852061183) return false;
         if ($ip >= 2886729728 && $ip <= 2887778303) return false;
         if ($ip >= 3221225984 && $ip <= 3221226239) return false;
         if ($ip >= 3232235520 && $ip <= 3232301055) return false;
         if ($ip >= 4294967040) return false;
      }
      return true;
   }

}
