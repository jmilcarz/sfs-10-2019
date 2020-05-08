<div id="body-container">
   <div class="container-fluid" style="margin-top: 100px;">
      <div class="row justify-content-center">
         <div class="col-md-6">
            <div class="jumbotron row justify-content-center">
               <h1 class="display-5">Do you want to logout?</h1>
               <div class="row justify-content-center col-8" style="margin-top: 25px">
                  <div class="col-sm-6">
                     <button class="btn btn-lg btn-light btn-block" id="logout-cancel">cancel</button>
                  </div>
                  <div class="col-sm-6">
                     <button class="btn btn-lg btn-warning btn-block" id="logout-yes">yes</button>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<script>
   $("#logout-cancel").click(function() {
      window.history.pushState(null, 'SFS', '/fb/home');
      $.ajax({
         url: "/fb/app/ajax/page.php?p=/home",
         processData: false,
         contentType: "application/json",
         beforeSend: function() {
            $("#body-container").html('Loading...');
         },
         success: function(data) {
            $('#body-container').html(data)
            refreshLinks();
         }
      });
   });
   $("#logout-yes").click(function() {
      $.ajax({
         url: "/fb/app/ajax/auth.php?t=logout",
         processData: false,
         contentType: "application/json",
         success: function(data) {
            window.history.pushState(null, 'SFS', '/fb/home');
            $.ajax({
               url: "/fb/app/ajax/page.php?p=/home&specials=nav",
               processData: false,
               contentType: "application/json",
               beforeSend: function() {
                  $("#body-container").append('loggin out...');
               },
               success: function(data) {
                  $('#app-container').html(data)
                  let url = window.location.href.substring(24);
                  reloadHomePage(url);
               }
            });
         }
      });
   });
</script>
