<div id="explore-container">
   <div class="row">
      <div class="col-md-3 d-none d-lg-block">
         <div class="card">
            <div class="card-body loading-el-animation" id="feed-left-sidebar"></div>
         </div>
      </div>
      <div class="col-md-6">
         <div class="card">
         <div class="card-body">
            <div class="page-header">
                <div class="titles">
                    <h5 class="card-title">Explore</h5>
                </div>
            </div>
            <div id="bookmarks-loader">
                <div class="spinner-grow" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </div>
   </div>
</div>
<script>
    $(function() {
        refreshLinks(window.location.href.substring(24));
    });
</script>