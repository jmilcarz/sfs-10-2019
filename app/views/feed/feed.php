<script>// posts
   var start = 5;
   var working = false;
   var counter = 5; // to check if all posts have loaded
   var lastcount = 0;</script>

<div id="feed-container" class="container-fluid">
   <div class="row">
      <div class="col-md-3 d-none d-lg-block">
         <div class="card">
            <div class="card-body loading-el-animation" id="feed-left-sidebar"></div>
         </div>
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
      <div class="col-md-3 d-sm-none d-block">
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

      // load left sidebar
      $("#feed-left-sidebar").html("<div style='height: 130px; border-bottom: 5px solid #fff;'><div style='height: 100px; width: 100px; background: #fefefe; border-radius: 50%; position: absolute; left: 50%; top: 50px; transform: translateX(-50%)'></div></div><div style='height: auto; box-sizing: border-box; padding: 50px 15px 30px 15px; display: flex; flex-direction: column; align-items: center'><div style='width: 200px; height: 30px; background: #fff; margin-bottom: 5px;'></div><div style='width: 130px; height: 20px; background: #fff; margin-bottom: 25px;'></div><div style='width: 100%; display: flex; margin-bottom: 25px;'><div style='width: 100%; height: 40px; background: #fff; margin-right: 5px;'></div><div style='width: 100%; height: 40px; background: #fff;'></div></div><div style='width: 100%; height: 30px; background: #fff; margin-bottom: 5px;'></div><div style='width: 100%; height: 30px; background: #fff; margin-bottom: 5px;'></div><div style='width: 100%; height: 30px; background: #fff; margin-bottom: 5px;'></div><div style='width: 100%; height: 30px; background: #fff; margin-bottom: 5px;'></div><div style='width: 100%; height: 30px; background: #fff; margin-bottom: 5px;'></div></div>");
      $.ajax({
         type: "GET",
         url: "/fb/app/views/feed/elements/left-sidebar.php",
         processData: false,
         contentType: "application/json",
         cache: false,
         data: '',
         success: function(data) {
            setTimeout(function() {
               $("#feed-left-sidebar").html(data);
               $("#feed-left-sidebar").removeClass("loading-el-animation");
               refreshLinks(url);
            }, 400)
         }
         
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
               $("#feed-posts-container").prepend('<div class="card sfs-post"><div class="card-body"><div class="row no-gutters"><div class="col-1"><a href="/u/'+userid+'" data-link="/u/'+userid+'"><img src="'+data.userimg+'" class="profile-img" /></a></div><div class="col-11" style="padding-left: 10px; width: calc(100%-10px)"><div><a href="/u/'+userid+'" data-link="/u/'+userid+'" class="authorName">'+data.userName+'</a><span class="float-right font-italic">now</span></div><div class="content">'+data.content+'</div><hr><div class="actions btn-toolbar"><div class="btn-group"><button type="button" class="btn btn-sm rounded-pill"><i class="fa fa-thumbs-up" /> 0</button><button type="button" class="btn btn-sm rounded-pill"><i class="fa fa-share" /> 0</button><button type="button" class="btn btn-sm rounded-pill"><i class="fa fa-comments" /> 0</button></div></div></div></div></div></div>');
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
               counter = 0;
               lastcount = 0;

               console.log(data);
               let commentsloaded = false;
               let posts = JSON.parse(data);
               if (posts.length <= 5) {
                  $("#feed-posts-container").html(" ");
                  $.each(posts, function(index) {
                     counter++;
                     $("#feed-posts-container").append('<div class="card sfs-post" data-counter="'+counter+'"><div class="card-body"><div class="row no-gutters"><div class="col-1"><a href="/u/'+posts[index].AuthorId+'" data-link="/u/'+posts[index].AuthorId+'"><img src="'+posts[index].AuthorImg+'" class="profile-img" /></a></div><div class="col-11" style="padding-left: 10px; width: calc(100%-10px)"><div><a href="/u/'+posts[index].AuthorId+'" data-link="/u/'+posts[index].AuthorId+'" class="authorName">'+posts[index].AuthorName+'</a><span class="float-right font-italic">'+posts[index].PostDate+'</span></div><div class="content">'+posts[index].PostBody+'</div><hr><div class="actions btn-toolbar"><div class="btn-group"><button type="button" class="btn btn-sm rounded-pill"><i class="fa fa-thumbs-up" /> '+posts[index].likes+'</button><button type="button" class="btn btn-sm rounded-pill"><i class="fa fa-share" /> '+posts[index].shares+'</button><button type="button" class="btn btn-sm rounded-pill"><i class="fa fa-comments" /> '+posts[index].comments+'</button></div></div></div></div></div></div>');
                     $("[data-counter="+counter+"]").hide().fadeIn(500);
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
                     $("#feed-posts-container-loader").html('<div class="d-flex justify-content-center" style="padding: 70px 0;"><div class="spinner-grow" role="status"><span class="sr-only">Loading...</span></div></div>');
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
                     counter++;
                     $("#feed-posts-container").append('<div class="card sfs-post" data-counter="'+counter+'"><div class="card-body"><div class="row no-gutters"><div class="col-1"><a href="/u/'+posts[index].AuthorId+'" data-link="/u/'+posts[index].AuthorId+'"><img src="'+posts[index].AuthorImg+'" class="profile-img" /></a></div><div class="col-11" style="padding-left: 10px; width: calc(100%-10px)"><div><a href="/u/'+posts[index].AuthorId+'" data-link="/u/'+posts[index].AuthorId+'" class="authorName">'+posts[index].AuthorName+'</a><span class="float-right font-italic">'+posts[index].PostDate+'</span></div><div class="content">'+posts[index].PostBody+'</div><hr><div class="actions btn-toolbar"><div class="btn-group"><button type="button" class="btn btn-sm rounded-pill"><i class="fa fa-thumbs-up" /> '+posts[index].likes+'</button><button type="button" class="btn btn-sm rounded-pill"><i class="fa fa-share" /> '+posts[index].shares+'</button><button type="button" class="btn btn-sm rounded-pill"><i class="fa fa-comments" /> '+posts[index].comments+'</button></div></div></div></div></div></div>');
                     $("[data-counter="+counter+"]").hide().fadeIn(500);
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
