<div id="bookmarks-container">
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
                    <h5 class="card-title">Bookmarks</h5>
                    <h6 class="card-subtitle mb-2 text-muted">Listing all your bookmarked posts</h6>
                </div>
                <form id="bookmark-search-box" class="input-group input-group-sm search-form">
                    <input type="text" class="form-control" placeholder="Search your bookmarks" aria-label="Search your bookmarks" aria-describedby="Search-your-bookmarks">
                    <div class="input-group-append">
                        <button class="input-group-text" id="Search-your-bookmarks"><i class="fa fa-search"></i></button>
                    </div>
                </form>
            </div>
            <div id="bookmark-loader">
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