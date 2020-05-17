<?php require 'app/autoload.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
   <?php include 'app/modules/essentials/header.php'; ?>
   <title id="pageTitleSFS">SFS</title>
</head>
<body>
   <div id="app-container" class="container-fluid"></div>

   <script>
      $(function() {
         let url = window.location.href.substring(24);
         if (url.slice(-1) == "/" && window.location.href.substring(24) != "/") {
            url = url.slice(0, -1);
         }
         console.log(url);
         $.ajax({
            url: "/fb/app/ajax/page.php?p="+url+"&specials=loadNav",
            processData: false,
            contentType: "application/json",
            type: 'GET',
            cache: false,
            beforeSend: function() {
               $("#app-container").html('Loading...');
            },
            success: function(data) {
               if (data == "auth") {
                  window.history.back();
               } else {
                  $("#app-container").append(data);
                  // refreshLinks(url);
               }
            }
         });
      });
   </script>
</body>
</html>
