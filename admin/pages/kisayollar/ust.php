<?php
define('BASE_URL', '/worldpurnet/admin/');
?>


<nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="/worldpurnet/index.php" class="nav-link"><?php echo $dil['anasayfa']; ?></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a class="nav-link" href="/worldpurnet/index.php#ozellikler"><?php echo $dil['avantaj']; ?></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a class="nav-link" href="/worldpurnet/kk.php"><?php echo $dil['sss']; ?></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a class="nav-link" href="/worldpurnet/index.php#kimler-kullanmali"><?php echo $dil['kp']; ?></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a class="nav-link" href="/worldpurnet/index.php#iletisim"><?php echo $dil['iletisim']; ?></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a class="nav-link" href="/worldpurnet/hakkimizda.php"><?php echo $dil['hakkimizda']; ?></a>
      </li>
    </ul>

    <!-- SEARCH FORM -->
   <!-- 

   <form class="form-inline ml-3">
      <div class="input-group input-group-sm">
        <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
        <div class="input-group-append">
          <button class="btn btn-navbar" type="submit">
           <i class="fas fa-search"></i>
         </button>
       </div>
     </div>
   </form>

 -->

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Messages Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown1" href="<?php echo BASE_URL; ?>sohbet/index.php">
          <i class="far fa-comments"></i>
          <span class="badge badge-danger navbar-badge"><?php echo $totalRows_mesaj ?></span>
        </a>


    <!-- 

        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
        
            <div class="media">
              <img src="dist/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  Nora Silvester
                  <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                </h3>
                <p class="text-sm">The subject goes here</p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
              </div>
            </div>
        
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
        </div>
    -->

      </li>

 <!-- 

      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <span class="badge badge-warning navbar-badge">15</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header">15 Notifications</span>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-envelope mr-2"></i> 4 new messages
            <span class="float-right text-muted text-sm">3 mins</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-users mr-2"></i> 8 friend requests
            <span class="float-right text-muted text-sm">12 hours</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-file mr-2"></i> 3 new reports
            <span class="float-right text-muted text-sm">2 days</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
        </div>
      </li>

Notifications Dropdown Menu -->

<ul class="nav pull-right top-menu">
                   		<li class="dropdown">
                           <a href="" class="dropdown-toggle" data-toggle="dropdown">
                               <span class="Dil"><?php echo $dil["dil_seciniz"]; ?></span>
                               <b class="caret"></b>
                           </a>
                           <ul class="dropdown-menu">
                               <li><a href="?dil=tr"><i class="icon-user"></i> Türkçe</a></li>
                               <li><a href="?dil=en"><i class="icon-user"></i> English</a></li>
                               <li><a href="?dil=chn"><i class="icon-user"></i> 中國語文</a></li>
                           </ul>
                       </li>
 </ul>


                       <li class="dropdown">
                           <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                               <span class="username"><?php echo $_SESSION['MM_Username']; ?> </span>
                               <b class="caret"></b>
                           </a>
                           <ul class="dropdown-menu">
                               <li><a href="<?php echo BASE_URL; ?>Hesap/index.php"><i class="icon-user"></i> <?php echo $dil['profilim']; ?></a></li>
                               <li class="divider"></li>
                               <li><a href="<?php echo BASE_URL; ?>cikis.php"><i class="icon-key"></i> <?php echo $dil['cikisyap']; ?></a></li>
                           </ul>
                       </li>
                       
                   </ul>

<!-- 
      <li class="nav-item">
        <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#">
          <i class="fas fa-th-large"></i>
        </a>
      </li>

      END USER LOGIN DROPDOWN -->
      
    </ul>
  </nav>
