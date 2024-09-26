<?php
/* 
 * Importa marcas por ajax
 * @importCategories recibe la llamada de ajax y procesa
 * @createBrandLog crea el log de marcas por ajax
 * @addBrandLog añade registro al log
 */
add_action('wp_ajax_importBrand', 'importBrand');
function importBrand() {

    if (!isset($_POST['brand_data'])) {
        wp_send_json_error(array(
            'result' => false,
            'brand' => $_POST['brand_data']['CODIGO'].' - '.$_POST['brand_data']['NOMBRE'],
            'message' => 'No brand data provided.'
        ));
    }

    $brand_data = $_POST['brand_data'];
    $importBrand = new dimporter();
    $brandImported = $importBrand->importBrand($brand_data);
    
    if (is_wp_error($brandImported)) {
        wp_send_json_error(array(
            'result' => false,
            'brand' => $_POST['brand_data']['CODIGO'].' - '.$_POST['brand_data']['NOMBRE'],
            'message' => $brandImported->get_error_message(),
        ));
    }

    $brand_name = $brand_data['NOMBRE'];
    $brand = $brand_data['CODIGO'].' - '.$brand_name;

    wp_send_json_success(array( 'result' => true, 'brand' => $brand, ));

}

/*
 * CREAMOS EL SISTEMA DE LOGS
 */
add_action('wp_ajax_createBrandLog', 'createBrandLog');
function createBrandLog() {
    global $wpdb;
    
    $log_name = sanitize_text_field($_POST['log_name']);
    $upload_dir = wp_upload_dir();
    $log_dir = $upload_dir['basedir'] . '/logs/brands/';
    if (!file_exists($log_dir)) {
        wp_mkdir_p($log_dir);
    }

    $log_file = $log_dir . $log_name;
    $log_url = site_url('/wp-content/uploads/logs/brands/' . $log_name);

    $current_user = wp_get_current_user();
    $user_name = (0 != $current_user->ID) ? $current_user->user_login : 'guest';

    $table_name = $wpdb->prefix . 'dimporter_logs';
    $inserted = $wpdb->insert(
        $table_name,
        array(
            'log_type'    => 'brandImport',
            'log_name'    => $log_name,
            'user'        => $user_name,
            'log_content' => $log_url,
        )
    );

    // Crear el archivo de log con un mensaje inicial
    $content = "Inicio de importación de marcas: " . date("Y-m-d H:i:s") . "\n" . 'Usuario: '.$user_name . "\n" ."--------------------------------". "\n";
    $content_utf8 = mb_convert_encoding($content, 'UTF-8', 'auto');
    if (file_put_contents($log_file, $content_utf8) === false) {
        wp_send_json_error('No se pudo crear el archivo de log.');
    }
    wp_send_json_success(array('log_name' => $log_name));
}


add_action('wp_ajax_addBrandLog', 'addBrandLog');
function addBrandLog() {

    $log_name = sanitize_text_field($_POST['log_name']);
    $log_entry = mb_convert_encoding(sanitize_text_field($_POST['log_entry']), 'UTF-8', 'auto');
    
    $upload_dir = wp_upload_dir();
    $log_dir = $upload_dir['basedir'] . '/logs/brands/';
    $log_file = $log_dir . $log_name;

    // Verificar si el archivo de log existe antes de intentar agregar la entrada
    if (file_exists($log_file)) {
        if (file_put_contents($log_file, $log_entry .' '. date("Y-m-d H:i:s") . "\n", FILE_APPEND) !== false) {
            wp_send_json_success();
        } else {
            wp_send_json_error('No se pudo agregar la entrada al archivo de log.');
        }
    } else {
        wp_send_json_error('El archivo de log no existe.');
    }
}