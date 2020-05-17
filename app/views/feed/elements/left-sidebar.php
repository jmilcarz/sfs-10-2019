<?php
    require '../../../autoload.php';

    $user = DB::query('SELECT fullName, userName, profileImg FROM users WHERE users_id = :userid', [':userid' => Auth::loggedin()])[0];

?>
<div class="top-bar">
    <a href="#" data-link="/u/<?php echo Auth::loggedin(); ?>"><img class="profile-img" src="<?php echo Auth::checkProfilePhoto($user['profileImg']); ?>" alt="<?php echo $user['userName']; ?>'s Profile Picture"></a>
</div>
<div class="box">
    <div>
        <a class="name" href="#" data-link="/u/<?php echo Auth::loggedin(); ?>"><?php echo $user['fullName']; ?></a>
        <span class="username">@<?php echo $user['userName']; ?></span>
    </div>
    <div class="stats">
        <div>
            <span>Followers</span>
            <span>234</span>
        </div>
        <div>
            <span>Following</span>
            <span>98</span>
        </div>
    </div>
    <ul class="sidebar-links list-group">
        <li class="list-group-item rounded-pill"><a href="#" data-link="/"><i class="fa fa-home"></i><span>Home</span></a></li>
        <li class="list-group-item rounded-pill"><a href="#" data-link="/explore"><i class="fa fa-globe"></i><span>Explore</span></a></li>
        <li class="list-group-item rounded-pill"><a href="#" data-link="/notifications"><i class="fa fa-bell"></i><span>Notifications</span></a></li>
        <li class="list-group-item rounded-pill"><a href="#" data-link="/messages"><i class="fa fa-envelope"></i><span>Messages</span></a></li>
        <li class="list-group-item rounded-pill"><a href="#" data-link="/bookmarks"><i class="fa fa-bookmark"></i><span>Bookmarks</span></a></li>
        <li class="list-group-item rounded-pill"><a href="#" data-link="/u/<?php echo Auth::loggedin(); ?>"><i class="fa fa-user"></i><span>Profile</span></a></li>
    </ul>
</div>
