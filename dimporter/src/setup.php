<?php
/*
 * Plugin setup
 * @get_dimporter_option consigue las opciones desde la tabla personalizada dimporter_options
 * @set_dimporter_option guarda opciones en la tabla personalizada dimporter_options
*/

function get_dimporter_option($option_name){
    global $wpdb;
    $sql = "SELECT option_value FROM " . $wpdb->prefix . "dimporter_options WHERE option_name = %s";
    $result = $wpdb->get_var($wpdb->prepare($sql, $option_name)); 
    return $result;
}

function set_dimporter_option($option_name, $option_value){
    global $wpdb;
    $sql = "SELECT id FROM " . $wpdb->prefix . "dimporter_options WHERE option_name = %s";
    $result = $wpdb->get_var($wpdb->prepare($sql, $option_name)); 
    
    if ($result != NULL) {
        $data = array(  'option_value' => $option_value);
        $where = array('id' => $result);
        $result = $wpdb->update($wpdb->prefix.'dimporter_options', $data, $where);
    } else {
        $result = $wpdb->insert( 
            $wpdb->prefix . 'dimporter_options', 
            array( 
                'option_name' => $option_name, 
                'option_value' => $option_value
            )
        );
    }
    return $result;
}


/*
 * Función print de importación
 * @importData Modificamos la llamada al formatData y el javascript que ejecutamos luego para hacer la recursiva con ajax
 */

function importData($type, $path_1, $path_2 = null) {
    $formatData = new formatData();
    $data = [];

    switch ($type) {
        case 'category':
            $data = $formatData->formatCategories($path_1, $path_2);
            $title = 'Importando categorías...';
            $jsFunction = 'runCategoriesImport';
            break;

        case 'client':
            $data = $formatData->formatClients($path_1);
            $title = 'Importando clientes...';
            $jsFunction = 'runClientImport';
            break;

        case 'brand':
            $data = $formatData->formatBrands($path_1);
            $title = 'Importando marcas...';
            $jsFunction = 'runBrandsImport';
            break;

        case 'product':
            $data = $formatData->formatArticles($path_1);
            $title = 'Importando productos...';
            $jsFunction = 'runProductsImport';
            break;

        default:
            echo "Tipo de importación no válido.";
            return;
    }

    echo '<div class="importer_holder">';
        echo '<h2 class="importer_title text-center">' . $title . '</h2>';
        echo '<div class="f2"><span id="number_imported">0</span>/' . count($data) . '</div>';
    echo '</div>';

    echo '<div class="progress_bar_holder">';
        echo '<div class="progress_bar">';
        echo '<div class="progress_bar_fill" data-total="' . count($data) . '"></div>';
        echo '</div>';
    echo '</div>';

    echo '<div id="log_return"></div>';
    echo '<textarea id="values" style="display:none;">' . esc_textarea(json_encode($data)) . '</textarea>';
    echo '<script>jQuery(document).ready(function($){ ' . $jsFunction . '(); });</script>';

    //echo '<pre>'; var_dump($data); echo '</pre>';
}

