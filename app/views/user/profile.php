<h1><?php echo $puser['name']; ?>'s Profile (<?php echo $puser['id']; ?>)</h1>
<?php
foreach ($urls[$params[1]][1] as $v) { // print nav
   echo '<a href="#" data-link="/u/'.$userid.'/' . substr($v, 1) . '">' . substr($v, 1) . '</a> ';
}
?>
<hr>
<div id="profile-container"></div>
<script>
   $(function() {
      let url = window.location.href.substring(24);
      $.ajax({
         url: "/fb/app/ajax/profile.php?view=<?php echo $view; ?>",
         processData: false,
         contentType: "application/json",
         type: 'GET',
         success: function(data) {
            $("#profile-container").html(data);
            refreshLinks(url);
            url = window.location.href.substring(27);
            if (url == '/home') {
               setTitle('/<?php echo $puser['name']; ?>', ['SFS']);
            } else {
               setTitle(url, ['<?php echo $puser['name']; ?>', 'SFS']);
            }
         }
      })
   })
</script>
