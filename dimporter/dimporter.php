<?php
/*
Plugin Name: Disterri Importer by Èmfasi
Plugin URI: https://cactussenygrafic.com/
Description: Importa contenidos a la página web de Disterri
Version: 1.0
Author: Èmfasi
Author URI: https://cactussenygrafic.com/
*/
define( 'DIMPORTER_V', time());
define( 'DIMPORTER', __FILE__ );
define( 'DIMPORTER_BASENAME', plugin_basename( DIMPORTER ) );
define( 'DIMPORTER_PLUGIN_NAME', trim( dirname( DIMPORTER_BASENAME ), '/' ) );
define( 'DIMPORTER_PLUGIN_DIR', untrailingslashit( dirname( DIMPORTER ) ) );

if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
    define( 'DIMPORTER_WOOCOMMERCE', true );
}else{
    define( 'DIMPORTER_WOOCOMMERCE', false );
}

if ( is_plugin_active( 'wordpress-seo/wp-seo.php' ) ) {
    define( 'DIMPORTER_YOAST', true );
}else{
    define( 'DIMPORTER_YOAST', false );
}

/*
 * Cargamos assets internos
 */
require_once DIMPORTER_PLUGIN_DIR . '/src/setup.php';
require_once DIMPORTER_PLUGIN_DIR . '/src/assets.php';
require_once DIMPORTER_PLUGIN_DIR . '/src/system.php';


/*
 * Cargamos las clases que se ejecutarán los procesos complejos
 * @formatData convierte los formatos .DAT en array entendibles
 * @dimporter importa productos, categorias, clientes y marcas, además de incluir funciones necesarias para ello
 * @exportOrders realiza la exportación en .DAT
 */

require_once DIMPORTER_PLUGIN_DIR . '/class/dimporter.class.php';
require_once DIMPORTER_PLUGIN_DIR . '/class/formatData.class.php';
require_once DIMPORTER_PLUGIN_DIR . '/class/exportOrders.class.php';


/*
 * Cargamos los PHPs que ejecutan las cargas diferidas de la importación
 */

require_once DIMPORTER_PLUGIN_DIR . '/src/ajax/ajax_productes.php';
require_once DIMPORTER_PLUGIN_DIR . '/src/ajax/ajax_clients.php';
require_once DIMPORTER_PLUGIN_DIR . '/src/ajax/ajax_marques.php';
require_once DIMPORTER_PLUGIN_DIR . '/src/ajax/ajax_categories.php';


/*
 * Al instalar creamos la tabla de opciones y la de logs con valores por defecto
 */

global $DIMPORTER_version;
$DIMPORTER_version = '1.0';

function DIMPORTER_install() {
    global $wpdb;
    global $DIMPORTER_version;

    $table_name_options = $wpdb->prefix . 'dimporter_options';
    $charset_collate = $wpdb->get_charset_collate();

    $sql_options = "CREATE TABLE $table_name_options (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        option_name varchar(55) NOT NULL,
        option_value varchar(800) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";


    $table_name_logs = $wpdb->prefix . 'dimporter_logs';
    $sql_logs = "CREATE TABLE $table_name_logs (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        log_type varchar(55) NOT NULL,
        log_name varchar(255) NOT NULL,
        user varchar(255) NOT NULL,
        log_content LONGTEXT NOT NULL,
        date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql_options );
    dbDelta( $sql_logs );

    set_dimporter_option('products_path', site_url().'/arxius/entrada/WEBARTI.DAT');
    set_dimporter_option('category_path', site_url().'/arxius/entrada/WEBFAMI.DAT');
    set_dimporter_option('subcategory_path', site_url().'/arxius/entrada/WEBSUBFAMI.DAT');
    set_dimporter_option('clients_path', site_url().'/arxius/entrada/WEBCLIE.DAT');
    set_dimporter_option('brands_path', site_url().'/arxius/entrada/WEBMARCA.DAT');
    set_dimporter_option('orderheader_path',ABSPATH . 'arxius/sortida/');
    set_dimporter_option('orderline_path',ABSPATH . 'arxius/sortida/');

    add_option( 'DIMPORTER_version', $DIMPORTER_version );
}

register_activation_hook( __FILE__, 'DIMPORTER_install' );

?>
