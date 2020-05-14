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
            <div id="feed-posts-container-loader"></div>
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

      if (url == "/") {
         setTimeout(function() {
            loadPosts();
            if ($(".sfs-post").length > 5) {
            }
         }, 500);
         clearTimeout();
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
         writePost($("#post-content").val(), <?php echo Auth::loggedin(); ?>, $("#post-privacy").val());
         
         
      });

   });

   function writePost(content, userid, privacy, bg = false) {
      var xhr = $.ajax({
         url: "/fb/app/ajax/feed.php?a=sendForm",
         type: 'POST',
         data: { content: content, userid: userid, privacy: privacy },
         success: function(data) {
            data = JSON.parse(data)
            if (data.type == "error") {
               fadeIn('<div class="alert alert-danger" role="alert">'+data.m+'</div>');
               setTimeout(function() { 
                  fadeOut("#notifications-floating-box > div");
               }, 3000);
            } else if (data.type == "success") {
               fadeIn('<div class="alert alert-success" role="alert">'+data.m+'</div>');
               $("#post-form").trigger("reset");
               $("#feed-posts-container").prepend('<div class="card sfs-post"><div class="card-body"><div class="row no-gutters"><div class="col-1"><a href="/u/'+userid+'" data-link="/u/'+userid+'"><img src="'+data.userimg+'" class="profile-img" /></a></div><div class="col-11" style="padding-left: 10px; width: calc(100%-10px)"><div><a href="/u/'+userid+'" data-link="/u/'+userid+'" class="authorName">'+data.userName+'</a></div><div class="content">'+data.content+'</div></div></div></div></div>');
               refreshLinks(url);
               setTimeout(function() { 
                  fadeOut("#notifications-floating-box > div");
               }, 3000);
            }
         }
      });
      setTimeout(function() {
         xhr.abort();
      }, 100);
   }

   function loadPosts() {
      let url = window.location.href.substring(24);
      if (url == "/") {
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
                  $("#feed-posts-container").html(" ");
                  $.each(posts, function(index) {
                     $("#feed-posts-container").append('<div class="card sfs-post"><div class="card-body"><div class="row no-gutters"><div class="col-1"><a href="/u/'+posts[index].AuthorId+'" data-link="/u/'+posts[index].AuthorId+'"><img src="'+posts[index].AuthorImg+'" class="profile-img" /></a></div><div class="col-11" style="padding-left: 10px; width: calc(100%-10px)"><div><a href="/u/'+posts[index].AuthorId+'" data-link="/u/'+posts[index].AuthorId+'" class="authorName">'+posts[index].AuthorName+'</a></div><div class="content">'+posts[index].PostBody+'</div></div></div></div></div>');
                  });
               }  
               refreshLinks(url);
               scrollToAnchor(location.hash);
            },
            error: function(r) {
               console.log("Something went wrong!");
            },
         });
      }
      return start;
   }

   $(window).scroll(function() {
      if ($(this).scrollTop() + 1 >= $('body').height() - $(window).height()) {
         if (working == false) {
            working = true;
            var xhr = $.ajax({
               type: "GET",
               url: "/fb/app/ajax/feed.php?a=fetchFeed&start="+start,
               processData: false,
               cache: false,
               contentType: "application/json",
               data: '',
               beforeSend: function() {
                  if (lastcount == counter) {
                     working = false;
                     $("#feed-posts-container-loader").html("<div style='height: 100%; padding: 70px 0; display: flex; justify-content: center; flex-direction: column; align-items: center;'><span style='font-size: 30px; color: var(--hncolor);margin-bottom:5px;'>Congrats!</span><span style='text-align: center'>You've made it. There are no posts left.<br>Wow you've impressed me!<br>It's huge so let me know by clicking <button class='feed-ending-congrats-btn'>here</button>!</span></div>");
                     $(".feed-ending-congrats-btn").click(function() {
                        writePost("I made it @kubamilcarz! #sfsnetworkfeed", <?php echo Auth::loggedin(); ?>, 2, true);
                     });
                     return;
                  }else {
                     $("#feed-posts-container-loader").html('<div class="dots"><span class="dot"></span><span class="dot"></span><span class="dot"></span></div>');
                  }
               },
               success: function(data) {
                  if (lastcount == counter) { // congrats sign stays
                     return;
                  }

                  lastcount = $("#feed-posts-container > div").length;

                  let commentsloaded = false;
                  let posts = JSON.parse(data);

                  $.each(posts, function(index) {

                     $("#feed-posts-container").append('<div class="card sfs-post"><div class="card-body"><div class="row no-gutters"><div class="col-1"><a href="/u/'+posts[index].AuthorId+'" data-link="/u/'+posts[index].AuthorId+'"><img src="'+posts[index].AuthorImg+'" class="profile-img" /></a></div><div class="col-11" style="padding-left: 10px; width: calc(100%-10px)"><div><a href="/u/'+posts[index].AuthorId+'" data-link="/u/'+posts[index].AuthorId+'" class="authorName">'+posts[index].AuthorName+'</a></div><div class="content">'+posts[index].PostBody+'</div></div></div></div></div>');
                     
                  });
                  refreshLinks(url);

                  counter = $("#feed-posts-container > div").length;
                  console.log(counter);
                  scrollToAnchor(location.hash)
                  setTimeout(function() {
                     working = false;
                     $("#feed-posts-container-loader").html('');
                  }, 1500)
                  start+=5;
               },
               error: function(r) {
                  console.log("Something went wrong!");
               }
            })
         }
      }
   });

   

</script>
