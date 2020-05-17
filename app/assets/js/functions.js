function reloadHomePage(url) {
   $.ajax({
      url: "/fb/app/ajax/page.php?p=/home&specials=loadNav",
      processData: false,
      contentType: "application/json",
      type: 'GET',
      cache: false,
      beforeSend: function() {
         $("#app-container").html('Loading...');
      },
      success: function(data) {
         $("#app-container").html(data);
         refreshLinks(url);
      }
   });
}


function refreshLinks(url) {
   $('[data-link]').click(function(e) {
      e.preventDefault();
      let button = $(this).attr('data-link');
      // console.log('last element: ' + button.slice(-1))
      if (button.slice(-1) == "/") {
         button.substring(-1);
      }
      // console.log('last element after: ' + button.slice(-1))
      if (button != url) {
         window.history.pushState(null, 'SFS', '/fb'+button);
         url = window.location.href.substring(24);
         // console.log(url + "\n" + button)
         $.ajax({
            url: "/fb/app/ajax/page.php?p="+button,
            processData: false,
            contentType: "application/json",
            type: 'GET',
            cache: false,
            success: function(data) {
               if (data == "auth") {
                  window.history.back();
               } else {
                  $("#body-container").html(data);
                  // refreshLinks(url);
                  setTitle(url);
               }
            }
         })
      }
   })
}

function setTitle(title, modifiers = 0) {
   if (title == "/") {
      $("#pageTitleSFS").html("SFS");
   } else {
      title = title.substring(1);
      if (!modifiers) {
         $("#pageTitleSFS").html(title);
      } else {
         let modis = title;
         for (let i = 0; i < modifiers.length; i++) {
            modis += " | " + modifiers[i];
         }
         $("#pageTitleSFS").html(modis);
      }
   }
}

function loadPage() {
   let url = window.location.href.substring(24);
   // console.log('url before: ' + url)
   // console.log('url last element: ' + url.slice(-1))
   if (url.slice(-1) == "/" && window.location.href.substring(24) != "/") {
      url = url.slice(0, -1);
   }
   // console.log('url after: ' + url)
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
            $("#app-container").html(data);
            $("#feed-posts-container").html("");
            // refreshLinks(url);
         }
      }
   });
}

function scrollToAnchor(aid){
	try {
		$('html,body').animate({scrollTop: $(aid).offset().top }, 1000);
	} catch (e) {
		console.log(e)
	}
}

function fadeIn(el, append = "#notifications-floating-box", time = 200) {
   $(el).hide().appendTo(append).fadeIn(time);
}

function fadeOut(el, time = 200) {
   $(el).last().fadeOut(time);
   setTimeout(function() {
      $(el).last().remove();
   }, time);
}
