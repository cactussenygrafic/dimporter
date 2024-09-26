<?php
/* 
 * Importa productos por ajax
 * @importCategories recibe la llamada de ajax y procesa
 * @createProductLog crea el log de productos por ajax
 * @addProductLog añade registro al log
 */
add_action('wp_ajax_importProduct', 'importProduct');
function importProduct() {

	if (!isset($_POST['product_data'])) {
        wp_send_json_error(array(
            'result' => false,
            'product' => $_POST['product_data']['ARTCODI'].' - '.$_POST['product_data']['ARTDESC'],
            'message' => 'No se han aportado datos del producto',
            'image_message' => 'Error al crear producto',
            'image_status' => 404,
        ));
    }

	$product_data = $_POST['product_data'];
	$importProduct = new dimporter();
	$result = $importProduct->importProduct($product_data);
	
	if (is_wp_error($result)) {
        wp_send_json_error(array(
            'result' => false,
            'product' => $_POST['product_data']['ARTCODI'].' - '.$_POST['product_data']['ARTDESC'],
            'message' => $result->get_error_message(),
            'image_message' => 'Error al crear producto',
            'image_status' => 404,
        ));
    }

    if($result['product_id']==false){
        wp_send_json_error(array(
            'result' => false,
            'product' => $_POST['product_data']['ARTCODI'].' - '.$_POST['product_data']['ARTDESC'],
            'message' => 'Error al crear producto. Datos incompletos o mal formateados',
            'image_message' => 'Error al crear producto',
            'image_status' => 404,
        ));
    }
    $product = wc_get_product($result['product_id']);

	wp_send_json_success(array(
        'result' => true,
        'product' => $product->get_sku().' - '.get_the_title($result['product_id']),
        'product_link' => get_the_permalink($result['product_id']),
        'image_message' => $result['image_message'],
        'image_status' => $result['image_status'],
    ));



}

/*
 * CREAMOS EL SISTEMA DE LOGS
 */
add_action('wp_ajax_createProductLog', 'createProductLog');
function createProductLog() {
    global $wpdb;
    
    $log_name = sanitize_text_field($_POST['log_name']);
    $upload_dir = wp_upload_dir();
    $log_dir = $upload_dir['basedir'] . '/logs/products/';
    if (!file_exists($log_dir)) {
        wp_mkdir_p($log_dir);
    }

    $log_file = $log_dir . $log_name;
    $log_url = site_url('/wp-content/uploads/logs/products/' . $log_name);

    $current_user = wp_get_current_user();
    $user_name = (0 != $current_user->ID) ? $current_user->user_login : 'guest';

    // Insertar registro en la base de datos
    $table_name = $wpdb->prefix . 'dimporter_logs';
    $inserted = $wpdb->insert(
        $table_name,
        array(
            'log_type'    => 'productImport',
            'log_name'    => $log_name,
            'user'        => $user_name,
            'log_content' => $log_url,
        )
    );
    if (!$inserted) { wp_send_json_error('No se pudo insertar el log en la base de datos.'); }

    // Crear el archivo de log con un mensaje inicial
    $content = "Inicio de importación de clientes: " . date("Y-m-d H:i:s") . "\n" . 'Usuario: '.$user_name . "\n" ."--------------------------------". "\n";
    $content_utf8 = mb_convert_encoding($content, 'UTF-8', 'auto');
    if (file_put_contents($log_file, $content_utf8) === false) {
        wp_send_json_error('No se pudo crear el archivo de log.');
    }
    wp_send_json_success(array('log_name' => $log_name));
}


add_action('wp_ajax_addProductLog', 'addProductLog');
function addProductLog() {
    

    $log_name = sanitize_text_field($_POST['log_name']);
    $log_entry =  mb_convert_encoding(sanitize_text_field($_POST['log_entry']), 'UTF-8', 'auto');
    
    $upload_dir = wp_upload_dir();
    $log_dir = $upload_dir['basedir'] . '/logs/products/';
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