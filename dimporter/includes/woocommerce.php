<h1>Importador Woocommerce</h1>

    <nav class="nav-tab-wrapper woo-nav-tab-wrapper">
        <a href="admin.php?page=dimporter-woocommerce&option=products" class="nav-tab <?php if(!isset($_GET['option']) || $_GET['option'] =='products'){ echo 'nav-tab-active'; } ?>">Productes Woocommerce</a>
        <a href="admin.php?page=dimporter-woocommerce&option=categories" class="nav-tab <?php if(isset($_GET['option']) && $_GET['option'] =='categories'){ echo 'nav-tab-active'; } ?>">Categories Woocommerce</a>
        <a href="admin.php?page=dimporter-woocommerce&option=clients" class="nav-tab <?php if(isset($_GET['option']) && $_GET['option'] =='clients'){ echo 'nav-tab-active'; } ?>">Clients Woocommerce</a>
        <a href="admin.php?page=dimporter-woocommerce&option=marques" class="nav-tab <?php if(isset($_GET['option']) && $_GET['option'] =='marques'){ echo 'nav-tab-active'; } ?>">Marques Woocommerce</a>
    </nav>

<?php 
  if(isset($_GET['option'])){
    switch ($_GET['option']) {

        case 'products':
          include_once( DIMPORTER_PLUGIN_DIR . '/includes/woocommerce/products.php');
        break;
        case 'categories':
          include_once( DIMPORTER_PLUGIN_DIR . '/includes/woocommerce/categories.php');
        break;
        case 'clients':
          include_once( DIMPORTER_PLUGIN_DIR . '/includes/woocommerce/clients.php');
        break;
        case 'marques':
          include_once( DIMPORTER_PLUGIN_DIR . '/includes/woocommerce/marques.php');
        break;
        default:
          include_once( DIMPORTER_PLUGIN_DIR . '/includes/woocommerce/products.php');
        break;
  }
}else{
   include_once( DIMPORTER_PLUGIN_DIR . '/includes/woocommerce/products.php');
}

?>