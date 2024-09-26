<?php
/*
 * Plugin assets include
 * @dimporter_assets importa los assets CSS y JS
 * @dimporter_menu_page monta las páginas del menú
 * @dimporter_main carga la sección de 'Opcions'
 * @dimporter_woocommerce carga la sección de 'Importador'
 */

add_action('admin_enqueue_scripts', 'dimporter_assets');

function dimporter_assets() {
    wp_enqueue_script('csg-js', plugins_url().'/dimporter/assets/js/csg-dimporter.js', array('jquery'), DIMPORTER_V, false);
    wp_enqueue_style('csg-css', plugins_url().'/dimporter/assets/css/csg-dimporter.css', array(), DIMPORTER_V, 'all');

    wp_localize_script('csg-js', 'csgAjax', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}


add_action('admin_menu', 'dimporter_menu_page');
function dimporter_menu_page() {
    $main_page_hook = add_menu_page(
    	"Importador arxius",
    	"Importador Disterri",
    	'manage_options',
    	'dimporter',
    	'dimporter_main',
    	plugins_url().'/dimporter/assets/icons/menu_icon.svg', 
    	0
    );

    
    add_submenu_page(
    	'dimporter',
    	'Opcions',
    	'Opcions',
    	'manage_options',
    	'dimporter',
    	'dimporter_main'
    );

    if(DIMPORTER_WOOCOMMERCE){
	    $woo_page_hook = add_submenu_page(
	    	'dimporter',
	    	'Importador',
	    	'Importador',
	    	'manage_options',
	    	'dimporter-woocommerce',
	    	'dimporter_woocommerce'
	    );
	}
	

}


function dimporter_main() { 
    include_once( DIMPORTER_PLUGIN_DIR . '/includes/main.php');
    exit;
}

function dimporter_woocommerce() { 
	include_once( DIMPORTER_PLUGIN_DIR . '/includes/woocommerce.php');
}