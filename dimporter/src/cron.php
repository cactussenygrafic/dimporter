<?php

/**
 * Realiza la importación por CRON 
 * @param task (category|product|brand|client) & key igual a la del PHP
 */

$wp_load_path = __DIR__ . '/../../../wp-load.php';
if (file_exists($wp_load_path)) { require_once $wp_load_path; } else { die('Error al cargar'); }

$secret_key = '7ff98ab7e98a78d2b1c285011169ae1eb42f4739e4d17e8db928f8b1784f8ca7';
if (!isset($_GET['key']) || $_GET['key'] !== $secret_key) { exit('Acceso denegado'); }

if (isset($_GET['task'])) {
    switch ($_GET['task']) {
        case 'category':
           $file_1 = get_dimporter_option('category_path');
           $file_2 = get_dimporter_option('subcategory_path');
           importData('category', $file_1, $file_2);
        break;
        case 'brand':
           $file_1 = get_dimporter_option('brands_path');
           importData('brand', $file_1);
        break;
        case 'client':
        	$file_1 = get_dimporter_option('clients_path');
        	importData('client', $file_1);
        break;
        case 'product':
        	$file_1 = get_dimporter_option('products_path');
        	importData('product', $file_1);
        break;
        default:
            echo "No se ha definido ninguna tarea.";
        break;
    }
} else {
    echo "No task parameter provided.";
}