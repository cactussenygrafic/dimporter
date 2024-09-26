<?php
/* 
 * Opciones principales del plugin
 *
 */
?>

<h1>Adjustos importador Woocommerce</h1>

    <nav class="nav-tab-wrapper woo-nav-tab-wrapper">
        <a href="admin.php?page=dimporter&option=" class="nav-tab <?php if(!isset($_GET['option'])){ echo 'nav-tab-active'; } ?>">Opcions</a>
        <a href="admin.php?page=dimporter&option=logs" class="nav-tab <?php if(isset($_GET['option']) && $_GET['option'] =='logs'){ echo 'nav-tab-active'; } ?>">Logs</a>
    </nav>

<?php 
  if(isset($_GET['option'])){
    switch ($_GET['option']) {
        case 'logs':
          include_once( DIMPORTER_PLUGIN_DIR . '/includes/main/logs.php');
        break;
        default:
          include_once( DIMPORTER_PLUGIN_DIR . '/includes/main/main.php');
        break;
  }
}else{
   include_once( DIMPORTER_PLUGIN_DIR . '/includes/main/main.php');
}

?>