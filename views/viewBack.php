<?php
require_once ('core/SidebarMenu.php');

$this->_css = ['sidebar'];
array_unshift($this->_js,'main', 'languages/add');
extract($this->_content) ;
?>

<div class="wrapper">

  <!-- Sidebar -->
  <nav id="sidebar">

    <!-- Sidebar Header-->
    <div class="sidebar-header h5 text-white">
      <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 523.97 537.64" fill="white">
        <defs>
          <style>.cls-1{fill:none;}.cls-1,.cls-2{stroke:white;stroke-miterlimit:10;stroke-width:24px;}</style>
        </defs>
        <g id="Calque_2" data-name="Calque 2">
          <g id="Calque_2-2" data-name="Calque 2">
            <path class="cls-1" d="M477.86,147v0a144.14,144.14,0,0,0-21.72-41.65c-22.52-29.8-56-48.68-93.3-48.68-41.3,0-77.84,23.11-100.09,58.53-22.18-36.09-59.06-59.71-100.82-59.71-41.07,0-77.43,22.84-99.72,57.93a1.86,1.86,0,0,0-.1.2,139.57,139.57,0,0,0-10.92,20.83s0,0,0,0A292.15,292.15,0,0,0,23.72,259c0,118.4,68.61,218.76,163.52,253.45h0c2.7,1,5.41,2,8.15,2.94h0c5.92,1.84,11.95,3.43,18,4.78l.86.19,1.15.25c1,.23,2,.44,3,.64,1.29.26,2.59.51,3.88.74,1.79.32,3.57.63,5.37.9l.21,0,3,.45,2.38.32h0a227.83,227.83,0,0,0,115.94-15.15l.11-.05c88.24-38.71,150.76-135.3,150.76-248.32A293.48,293.48,0,0,0,477.86,147Z"/>
            <path class="cls-2" d="M512,195.3v53.07c0,119.35-68,221.09-163.36,260.16,88.24-38.71,150.76-135.3,150.76-248.32A293.48,293.48,0,0,0,477,147v0a144.14,144.14,0,0,0-21.72-41.65c-22.52-29.8-56-48.68-93.3-48.68-41.3,0-77.84,23.11-100.09,58.53-22.18-36.09-59.06-59.71-100.82-59.71-41.07,0-77.43,22.84-99.72,57.93a1.86,1.86,0,0,0-.1.2,139.57,139.57,0,0,0-10.92,20.83s0,0,0,0A292.15,292.15,0,0,0,22.85,259c0,118.4,68.61,218.76,163.52,253.45h0c2.7,1,5.41,2,8.15,2.94C89.24,482.78,12,375.58,12,248.37V195.3C12,94.36,85.58,12.47,176.48,12H348.37C438.87,13,512,94.68,512,195.3Z"/>
            <g id="face">
              <g id="eyes">
                <ellipse cx="154.44" cy="227.7" rx="43.84" ry="45.69"/>
                <ellipse cx="369.53" cy="227.71" rx="43.84" ry="45.69"/>
              </g>
              <polygon id="nose" points="261.98 481.68 307.38 358.06 331.64 205.3 261.98 344.45 261.99 344.45 192.32 205.3 216.58 358.06 261.99 481.68 261.98 481.68"/>
            </g>
          </g>
        </g>
      </svg>
      <span>QuickBaluchon</span>
    </div>

    <!-- Menu-->
    <?php
      $sidebar = new SidebarMenu($_SESSION['role']);
      $sidebar->display();
    ?>

  </nav>

  <!-- Content -->
  <div id="content">

    <!-- Header -->
    <div class="container-fluid">
      <nav class="navbar navbar-expand-lg navbar-light bg-white">

        <!-- Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav">

            <!-- Dropdown Language-->
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="navbardrop" data-toggle="dropdown">Langue <?= $_SESSION['defaultLang']['flag'] ?></a>
              <div class="dropdown-menu">
                  <?php foreach ($_SESSION['langs'] as $lang => $info) { ?>
                      <button class="dropdown-item" onclick="language('<?= $lang ?>')"><?= $info['flag'] . " " . $lang ?></button>
                  <?php } ?>
              </div>
            </li>
          </ul>
        </div>

        <a href="#" class="h5"><?= $name ?></a>

      </nav>
    </div>


    <!-- Content -->
    <section id="back-content">

      <?= $content ?>

    </section>

  </div>

</div>
