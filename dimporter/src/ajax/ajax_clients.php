<?php
/* 
 * Importa clientes por ajax
 * @importCategories recibe la llamada de ajax y procesa
 * @createClientLog crea el log de clientes por ajax
 * @addClienteLog añade registro al log
 */
add_action('wp_ajax_importClient', 'importClient');
function importClient() {

	if (!isset($_POST['client_data'])) {
        wp_send_json_error(array(
            'result' => false,
            'client' => $_POST['client_data']['CLICODI'].' - '.$_POST['client_data']['CLINOMB'],
            'message' => 'No client data provided.'
        ));
    }

	$client_data = $_POST['client_data'];
	$importClient = new dimporter();
	$clientImported = $importClient->importClient($client_data);
	
	if (is_wp_error($clientImported)) {
        wp_send_json_error(array(
            'result' => false,
            'client' => $_POST['client_data']['CLICODI'].' - '.$_POST['client_data']['CLINOMB'],
            'message' => $clientImported->get_error_message(),
        ));
    }

	$clicodi = get_field('clicodi','user_'.$clientImported);
	$clinomb = get_field('clinomb','user_'.$clientImported);
	$client = $clicodi.' - '.$clinomb;

	wp_send_json_success(array(
        'result' => true,
        'client' => $client,
    ));

}

/*
 * CREAMOS EL SISTEMA DE LOGS
 */
add_action('wp_ajax_createClientLog', 'createClientLog');
function createClientLog() {
    global $wpdb;
    
    $log_name = sanitize_text_field($_POST['log_name']);
    $upload_dir = wp_upload_dir();
    $log_dir = $upload_dir['basedir'] . '/logs/clients/';
    if (!file_exists($log_dir)) {
        wp_mkdir_p($log_dir);
    }

    $log_file = $log_dir . $log_name;
    $log_url = site_url('/wp-content/uploads/logs/clients/' . $log_name);

    $current_user = wp_get_current_user();
    $user_name = (0 != $current_user->ID) ? $current_user->user_login : 'guest';

    // Insertar registro en la base de datos
    $table_name = $wpdb->prefix . 'dimporter_logs';
    $inserted = $wpdb->insert(
        $table_name,
        array(
            'log_type'    => 'clientImport',
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


add_action('wp_ajax_addClienteLog', 'addClienteLog');
function addClienteLog() {
    

    $log_name = sanitize_text_field($_POST['log_name']);
    $log_entry =  mb_convert_encoding(sanitize_text_field($_POST['log_entry']), 'UTF-8', 'auto');
    
    $upload_dir = wp_upload_dir();
    $log_dir = $upload_dir['basedir'] . '/logs/clients/';
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