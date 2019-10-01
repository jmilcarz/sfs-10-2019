<?php
/*
 * Name: Nav.php
 * Created: 2019 October 1
 */
?>
<style>
   nav {
      height: 30px;
      background: rgb(92, 219, 189);
      display: flex;
      justify-content: space-around;
      align-items: center;
      width: 360px;
      position: fixed;
      top: 0;
      left: 0;
   }
   #app-container {
      margin-top: 50px;
   }
</style>
<nav>
   <a href="#" data-link="/">home</a>
   <a href="#" data-link="/u/4">profile</a>
   <a href="#" data-link="/logout">logout</a>
</nav>
