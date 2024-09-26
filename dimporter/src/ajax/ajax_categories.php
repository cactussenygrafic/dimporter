<?php
/* 
 * Importa categorias por ajax
 * @importCategories recibe la llamada de ajax y procesa
 * @createCategoryLog crea el log de categorias por ajax
 * @addCategoryLog añade registro al log
 */
add_action('wp_ajax_importCategories', 'importCategories');
function importCategories() {

    // Verificar si los datos de categorías han sido enviados
    if (!isset($_POST['category_data'])) {
        wp_send_json_error(array(
            'result' => false,
            'message' => 'No category data provided.'
        ));
    }

    $category_data = $_POST['category_data'];
    
    // Instanciar la clase de importación y llamar al método de importación de categorías
    $importCategories = new dimporter();
    $categoryImported = $importCategories->importCategory($category_data);
    
    // Verificar si hubo un error en la importación
    if (is_wp_error($categoryImported)) {
        wp_send_json_error(array(
            'result' => false,
            'category' => $category_data['CODIGO'].' - '.$category_data['NOMBRE'],
            'message' => $categoryImported->get_error_message(),
        ));
    }

    $category_name = $category_data['NOMBRE'];
    $category = $category_data['CODIGO'].' - '.$category_name;
    $subcategories = [];
    if (!empty($category_data['SUBFAMILIAS'])) {
        foreach ($category_data['SUBFAMILIAS'] as $key => $subcategory) {
            $subcategories[] = array(
                'name' => $subcategory['NOMBRE'],
                'id' => isset($categoryImported['subcategories'][$key]) ? $categoryImported['subcategories'][$key] : null
            );
        }
    }

    wp_send_json_success(array(
        'result' => true,
        'category' => $category,
        'subcategories' => $subcategories // Array de IDs de subcategorías
    ));
}


/*
 * CREAMOS EL SISTEMA DE LOGS
 */
add_action('wp_ajax_createCategoryLog', 'createCategoryLog');
function createCategoryLog() {
    global $wpdb;
    
    $log_name = sanitize_text_field($_POST['log_name']);
    $upload_dir = wp_upload_dir();
    $log_dir = $upload_dir['basedir'] . '/logs/category/';
    if (!file_exists($log_dir)) {
        wp_mkdir_p($log_dir);
    }

    $log_file = $log_dir . $log_name;
    $log_url = site_url('/wp-content/uploads/logs/category/' . $log_name);

    $current_user = wp_get_current_user();
    $user_name = (0 != $current_user->ID) ? $current_user->user_login : 'guest';

    $table_name = $wpdb->prefix . 'dimporter_logs';
    $inserted = $wpdb->insert(
        $table_name,
        array(
            'log_type'    => 'categoryImport',
            'log_name'    => $log_name,
            'user'        => $user_name,
            'log_content' => $log_url,
        )
    );

    // Crear el archivo de log con un mensaje inicial
    $content = "Inicio de importación de categorias: " . date("Y-m-d H:i:s") . "\n" . 'Usuario: '.$user_name . "\n" ."--------------------------------". "\n";
    $content_utf8 = mb_convert_encoding($content, 'UTF-8', 'auto');
    if (file_put_contents($log_file, $content_utf8) === false) {
        wp_send_json_error('No se pudo crear el archivo de log.');
    }
    wp_send_json_success(array('log_name' => $log_name));
}


add_action('wp_ajax_addCategoryLog', 'addCategoryLog');
function addCategoryLog() {

    $log_name = sanitize_text_field($_POST['log_name']);
    $log_entry = mb_convert_encoding(sanitize_text_field($_POST['log_entry']), 'UTF-8', 'auto');
    
    $upload_dir = wp_upload_dir();
    $log_dir = $upload_dir['basedir'] . '/logs/category/';
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