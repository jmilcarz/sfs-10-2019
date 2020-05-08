<?php
session_start();

if (isset($_SESSION['user'])) {
   echo 'logged in!';
} else {
   echo 'not logged in!';
}

if (!isset($_SESSION['LoginToken'])) {
   $cstrong = true;
   $_SESSION['LoginToken'] = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
}

?>
<div id="body-container">
   <!-- <?php //echo password_hash('kubakuba06', PASSWORD_DEFAULT) . "<br>"; ?> -->
   <div class="container">
      <div class="row justify-content-center">
         <div class="col-md-8 col-lg-5">
            <h1>Login</h1>
            <p>if you do not have account, <a href="/register" data-link="/register">create one</a>!</p>
            <form id="login-form">
               <div class="alert alert-danger d-none" id="login-form-errors" role="alert"></div>
               <input type="hidden" name="token" id="loginFormToken" value="<?php echo $_SESSION['LoginToken']; ?>">
               <div class="form-group">
                  <input type="text" class="form-control" name="email" id="loginFormEmail" placeholder="Email">
               </div>
               <div class="form-group">
                  <input type="password" class="form-control" name="password" id="loginFormPassword" placeholder="Password">
               </div>
               <div class="form-group">
                  <input type="submit" class="form-control btn btn-warning" value="Continue">
               </div>
            </form>
         </div>
      </div>
   </div>

   <script>
      $("#login-form").on('submit', function(e) {
         e.preventDefault();
         let email = $("#loginFormEmail").val();
         let password = $("#loginFormPassword").val();
         let token = $("#loginFormToken").val();
         $.ajax({
            url: "/fb/app/ajax/auth.php?t=login",
            type: 'POST',
            data: { email: email, password: password, token: token },
            success: function(data) {
               data = JSON.parse(data)
               let url = window.location.href.substring(24);
               if (data.type == "error") {
                  $("#login-form-errors").removeClass("d-none").html(data.m);
               } else if (data.type == "success") {
                  $("#login-form-errors").removeClass("d-none alert-danger").addClass("alert-success").html(data.m);
                  window.history.pushState(null, 'SFS', '/fb/');
                  reloadHomePage(url);
               }
               refreshLinks(url);
            }
         })
      });
      $(function() {
         setTitle("/Login", ['SFS']);
      });
   </script>
</div>
