<div class="sidebar">
      <!-- Sidebar user (optional) -->
    <?php
include("kullanicibilgi.php");
    ?> 


      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          
<?php if($row_uyebilgileri['seviyeID']== 1)
{
  ?>
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-lock"></i>
              <p>
                Yönetim
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
          <li class="nav-item has-treeview">
              <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>index.php" class="nav-link">
                  <i class="nav-icon fas fa-home"></i>
                  <p>Ana Sayfa</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>Ayar/index.php" class="nav-link">
                  <i class="nav-icon fas fa-tools"></i>
                  <p>Site Ayarları</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>duyuru/index.php" class="nav-link">
                  <i class="nav-icon fas fa-bullhorn"></i>
                  <p>Duyuru</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>Ayar/uyeler/index.php" class="nav-link">
                  <i class="nav-icon fas fa-users"></i>
                  <p>Üyeler</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>Ayar/seviyeler/index.php" class="nav-link">
                  <i class="nav-icon fas fa-users-cog"></i>
                  <p>Üye Seviyeleri</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>Kategoriler/index.php" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>Kategoriler</p>
                </a>
              </li>

            </ul>
          </li>
          
          <?php }else{

}?>

        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-cogs"></i>
              <p>  
                  <?php echo $dil['hesap_bilgi']; ?>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
          <li class="nav-item has-treeview">
              <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>Hesap/index.php" class="nav-link">
                  <i class="nav-icon fas fa-tools"></i>
                  <p><?php echo $dil['ayarlar']; ?></p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>../sifredegis.php" class="nav-link">
                  <i class="nav-icon fas fa-key"></i>
                  <p><?php echo $dil['sifre_degis']; ?></p>
                </a>
              </li>

              <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>Hesap/yukle/index.php" class="nav-link">
                  <i class="nav-icon fas fa-file-export"></i>
                  <p><?php echo $dil['sirketresim']; ?></p>
                </a>
              </li>

              <li class="nav-item">
                <a href="<?php echo BASE_URL; ?>Hesap/yukle/index1.php" class="nav-link">
                  <i class="nav-icon fas fa-file-export"></i>
                  <p><?php echo $dil['sertifikaresim']; ?></p>
                </a>
              </li>
            </ul>
          </li>

          <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>Dashboard/index.php" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                <?php echo $dil['dashboard']; ?>
                <span class="right badge badge-danger"></span>
              </p>
            </a>
          </li>


          <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>Teklif/verme/index.php" class="nav-link">
              <i class="nav-icon fas fa-download"></i>
              <p>
                <?php echo $dil['teklif_iste']; ?>
                <span class="right badge badge-danger"></span>
              </p>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>Teklif/alma/index.php" class="nav-link">
              <i class="nav-icon fas fa-upload"></i>
              <p>
                <?php echo $dil['teklif_verme']; ?>
                <span class="right badge badge-danger"></span>
              </p>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>Firmalar/index.php" class="nav-link">
              <i class="nav-icon far fa-building"></i>
              <p>
                <?php echo $dil['firmalar']; ?>
                <span class="right badge badge-danger"></span>
              </p>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>Teklif/tekliflerim/index.php" class="nav-link">
              <i class="nav-icon fas fa-shopping-cart"></i>
              <p>
                <?php echo $dil['tekliflerim']; ?>
                <span class="right badge badge-danger"></span>
              </p>
            </a>
          </li>

          <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>Yardim/index.php" class="nav-link">
              <i class="nav-icon far fa-question-circle"></i>
              <p>
                <?php echo $dil['yardim_merkezi']; ?>
                <span class="right badge badge-danger"></span>
              </p>
            </a>
          </li>         

      </nav>
      <!-- /.sidebar-menu -->
    </div>
