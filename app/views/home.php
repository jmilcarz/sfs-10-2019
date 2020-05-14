<div id="home-container"></div>
<div id="notifications-floating-box"></div>
<script>
   $(function() {
      let url = window.location.href.substring(24);
      $.ajax({
         url: "/fb/app/ajax/feed.php",
         processData: false,
         contentType: "application/json",
         type: 'GET',
         success: function(data) {
            $("#home-container").html(data);
            refreshLinks(url);
            setTitle('/SFS');
         }
      })
   })
</script>
