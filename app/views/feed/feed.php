<script>// posts
   var start = 5;
   var working = false;
   var counter = 5; // to check if all posts have loaded
   var lastcount = 0;</script>

<div id="body-container" class="container-fluid">
   <div class="row">
      <div class="col-md-3">
         <div class="card"><div class="card-body"></div></div>
      </div>
      <div class="col-md-6">
         <div class="card">
            <div class="card-body">
            <button id="post-form-btn" class="btn btn-block btn-light disabled">Write something...</button>
            <div id="post-form-message" class="alert alert-danger d-none" role="alert"></div>
               <form id="post-form" class="d-none"></form>
            </div>
         </div>
         <div style="margin-top: 25px">
            <div id="feed-posts-container"></div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="card"><div class="card-body"></div></div>
      </div>
   </div>
</div>  

<script>
   var loadedPostsCounter = 0;
   $(function() {
      url = window.location.href.substring(24);
      $("#feed-posts-container").empty();
      if (url == "/") {
         setTimeout(function() {
            let r = loadPosts();
            if ($(".sfs-post").length > 5) {
               $("#feed-posts-container").empty();
               // loadPosts();
            }
            if (r > 0) {
               if (r > 5) {
                  $("#feed-posts-container").empty();
                  // loadPosts();
               }
               clearTimeout();
            }
         }, 500);
      }

      // load form
      $.ajax({
         type: "GET",
         url: "/fb/app/views/feed/elements/postform.php",
         processData: false,
         contentType: "application/json",
         data: '',
         success: function(data) {
            $("#post-form").html(data)
         }
      })

      // load posting form
      $("#post-form-btn").click(function(e) {
         e.preventDefault();
         $(this).css("height", "100px").addClass("d-none");
         $("#post-form").removeClass("d-none");
      })

      // send post form
      $("#post-form").submit(function(e) {
         e.preventDefault();
         let content = $("#post-content").val();
         let userid = <?php echo Auth::loggedin(); ?>;
         let privacy = $("#post-privacy").val();
         $.ajax({
            url: "/fb/app/ajax/feed.php?a=sendForm",
            type: 'POST',
            data: { content: content, userid: userid, privacy: privacy },
            success: function(data) {
               data = JSON.parse(data)
               if (data.type == "error") {
                  $("#post-form-message").removeClass("d-none alert-success").addClass("alert-danger").html(data.m);
                  setTimeout(function() { 
                     $("#post-form-message").addClass("d-none");
                  }, 5000);
               } else if (data.type == "success") {
                  $("#post-form-message").removeClass("d-none alert-danger").addClass("alert-success").html(data.m);
                  $("#post-content").val('');
                  setTimeout(function() { 
                     $("#post-form-message").addClass("d-none");
                  }, 5000);
               }
            }
         });
      });

   });

   function loadPosts() {
      let url = window.location.href.substring(24);
      if (url == "/") {
         $("#feed-posts-container").empty();
         $.ajax({
            type: "GET",
            url: "/fb/app/ajax/feed.php?a=fetchFeed&start=0",
            processData: false,
            contentType: "application/json",
            data: '',
            success: function(data) {
               loadedPostsCounter++;
               start = 5;
               working = false;
               counter = 5;
               lastcount = 0;

               console.log(data);
               let commentsloaded = false;
               let posts = JSON.parse(data);
               if (posts.length <= 5) {
                  $("#feed-posts-container")
                  $.each(posts, function(index) {
                     $("#feed-posts-container").append('<div class="card sfs-post"><div class="card-body"><div class="row no-gutters"><div class="col-1"><a href="/u/'+posts[index].AuthorId+'" data-link="/u/'+posts[index].AuthorId+'"><img src="'+posts[index].AuthorImg+'" class="profile-img" /></a></div><div class="col-11" style="padding-left: 10px; width: calc(100%-10px)"><div><a href="/u/'+posts[index].AuthorId+'" data-link="/u/'+posts[index].AuthorId+'" class="authorName">'+posts[index].AuthorName+'</a></div><div class="content">'+posts[index].PostBody+'</div></div></div></div></div>');
                  });
               }  
               refreshLinks(url);
               scrollToAnchor(location.href);
            },
            error: function(r) {
               console.log("Something went wrong!");
            },
         });
      }
      return counter;
   }

   // $(window).scroll(function() {
   // if ($(this).scrollTop() + 1 >= $('body').height() - $(window).height()) {
   //    if (working == false) {
   //       working = true;
   //       $.ajax({
   //          type: "GET",
   //          url: "/fb/app/ajax/feed.php?a=fetchFeed&start="+start,
   //          processData: false,
   //          cache: false,
   //          contentType: "application/json",
   //          data: '',
   //          beforeSend: function() {
   //             if (lastcount == counter) {
   //                working = false;
   //                $("#hn-posts-loader").html("<div style='height: 100%; display: flex; justify-content: center; flex-direction: column; align-items: center;'><span style='font-size: 30px; color: var(--hncolor);margin-bottom:5px;'>Congrats!</span><span>You've made it. There are no posts left.</span></div>");
   //                return;
   //             }else {
   //                $("#hn-posts-loader").html('<div class="dots"><span class="dot"></span><span class="dot"></span><span class="dot"></span></div>');
   //             }
   //          },
   //          success: function(data) {
   //             scrollToAnchor(location.hash)
   //             setTimeout(function() {
   //                working = false;
   //                $("#hn-posts-loader").html('');
   //             }, 1500)
   //             start+=5;
   //          },
   //          error: function(r) {
   //             console.log("Something went wrong!");
   //          }
   //       })
   //    }
   // }
   
   // });

   

</script>
