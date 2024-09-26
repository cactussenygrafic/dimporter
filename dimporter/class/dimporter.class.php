<?php
/**
 * Importa o actualiza Productos, Categorías, Clientes y Marcas
 *
 * @importProduct Importa o actualiza productos
 * @importClient Importa o actualiza clientes
 * @importCategory Importa o actualiza categorías
 * @importBrand Importa o actualiza las marcas
 */

class dimporter {

    
    /**
     * Importa o actualiza un producto en WooCommerce.
     *
     * @param array $data Array asociativo con los datos del cliente.
     * @return int|WP_Error Devuelve el ID del cliente insertado o actualizado, o un error en caso de fallo.
     */
    public function importProduct($data) {
        global $iclTranslationManagement;
        $product_id = wc_get_product_id_by_sku($data['ARTCODI']);

        if (!$product_id) {
            $product = new WC_Product_Simple();
        } else {
            $product = wc_get_product($product_id);
        }

        if($data['ARTDESCES']==""){ 
            return array( "product_id" => false, "image_message" => 'Error al crear producto', "image_status" => 404 );
        }//evitamos crear productos vacíos

        // Configuración básica del producto
        $product->set_name($data['ARTDESCES']);
        $product->set_sku($data['ARTCODI']);
        $product->set_regular_price($data['ARTPREC']);

        // Gestión del inventario
        $product->set_manage_stock(true);
        $product->set_stock_quantity($data['ARTSTOC']);
        $product->set_backorders('no');

        // Configuración del IVA
        $tax_class = '';
        if ($data['ARTTIVA'] == '21') {
            $tax_class = ''; // IVA 21%
        } elseif ($data['ARTTIVA'] == '10') {
            $tax_class = 'iva-reducido'; // Clase de impuesto para el IVA reducido
        } elseif ($data['ARTTIVA'] == '04') {
            $tax_class = 'iva-superreducido'; // Clase de impuesto para el IVA superreducido
        }

        $product->set_tax_class($tax_class);
        $product_id = $product->save();

        $image_result = $this->handleProductImage($product_id, $data['ARTCODI']);
        $categories = $this->getProductCategories($data);
        if (!empty($categories)) { wp_set_object_terms($product_id, $categories, 'product_cat'); }

        $brand = $this->getProductBrand($data['ARTCODM']);
        if (!empty($brand)) { wp_set_object_terms($product_id, $brand, 'marca'); }

        // Campos personalizados (ACF)
        update_field('artvend', $data['ARTVEND'], $product_id);
        update_field('artpver', $data['ARTPVER'], $product_id);
        update_field('artvolu', $data['ARTVOLU'], $product_id);
        update_field('artnove', $data['ARTNOVE'], $product_id);
        update_field('artibee', $data['ARTIBEE'], $product_id);


        /** IDIOMAS **/
        if (!empty($data['ARTDESCCA'])) {
            $product_cat_id = apply_filters('wpml_object_id', $product_id, 'product', false, 'ca');
            if ($product_cat_id) {
                // Si ya existe la traducción en catalán, actualizar
                $product_cat = wc_get_product($product_cat_id);
                $product_cat->set_name($data['ARTDESCCA']);
                $product_cat->save();
            } else {
                // Si no existe, duplicar el producto y crear la traducción
                $product_cat_id = $iclTranslationManagement->make_duplicate($product_id, 'ca');
                delete_post_meta($product_cat_id, '_icl_lang_duplicate_of');
                $product_cat = wc_get_product($product_cat_id);
                $product_cat->set_name($data['ARTDESCCA']);
                $product_cat->save();
            }
        }

        if (!empty($data['ARTDESCEN'])) {
            $product_eng_id = apply_filters('wpml_object_id', $product_id, 'product', false, 'en');
            if ($product_eng_id) {
                // Si ya existe la traducción en catalán, actualizar
                $product_eng = wc_get_product($product_eng_id);
                $product_eng->set_name($data['ARTDESCEN']);
                $product_eng->save();
            } else {
                // Si no existe, duplicar el producto y crear la traducción
                $product_eng_id = $iclTranslationManagement->make_duplicate($product_id, 'en');
                delete_post_meta($product_eng_id, '_icl_lang_duplicate_of');
                $product_eng = wc_get_product($product_eng_id);
                $product_eng->set_name($data['ARTDESCEN']);
                $product_eng->save();
            }
        }
        

        return array(
            "product_id" => $product_id,
            "image_message" => $image_result['message'],
            "image_status" => $image_result['status']
        );
    }



        


    


    /**
     * Importa o actualiza un cliente en WooCommerce.
     *
     * @param array $data Array asociativo con los datos del cliente.
     * @return int|WP_Error Devuelve el ID del cliente insertado o actualizado, o un error en caso de fallo.
     */
    public function importClient($data) {

        if (empty($data['CLICODI']) || empty($data['CLINOMB']) || empty($data['CLICIF'])) {
            return new WP_Error('missing_data', 'El código, nombre o CIF del cliente están vacíos.');
        }

        // Verificar si el CLICIF empieza por una letra para asignar el rol
        $role = 'customer';
        if (!empty($data['CLICIF']) && ctype_alpha(substr($data['CLICIF'], 0, 1))) {
            $role = 'customer_b2b';
        }

        // Buscar cliente existente mediante el CLICIF
        $existingClient = get_user_by('login', $data['CLICIF']);

        // Si el cliente ya existe, actualizamos sus datos
        // Si no existe creamos
        if ($existingClient) {
            $user_id = wp_update_user([
                'ID' => $existingClient->ID,
                'user_email' => !empty($data['CLIMAIL']) ? sanitize_email($data['CLIMAIL']) : $existingClient->user_email,
                'display_name' => $data['CLINOMB'],
            ]);
        } else {
            $user_id = wp_insert_user([
                'user_login' => $data['CLICIF'],
                'user_email' => sanitize_email($data['CLIMAIL']),
                'first_name' => $data['CLINOMB'],
                'role' => $role,
                'user_pass' => wp_generate_password(),
            ]);
        }

        // Verificar si hubo un error al actualizar o insertar el usuario
        if (is_wp_error($user_id)) {
            return $user_id;
        }

        // Actualizar los campos personalizados del cliente usando ACF
        update_field('clicodi', $data['CLICODI'], 'user_' . $user_id);
        update_field('clinomb', $data['CLINOMB'], 'user_' . $user_id);
        update_field('clitari', $data['CLITARI'], 'user_' . $user_id);
        update_field('cliruta', $data['CLIRUTA'], 'user_' . $user_id);
        update_field('clioroe', $data['CLIORDE'], 'user_' . $user_id);
        update_field('cliest', $data['CLIESTA'], 'user_' . $user_id);
        update_field('clitiva', $data['CLITIVA'], 'user_' . $user_id);
        update_field('clidto1', $data['CLIDTO1'], 'user_' . $user_id);
        update_field('clidto2', $data['CLIDTO2'], 'user_' . $user_id);
        update_field('clidto3', $data['CLIDTO3'], 'user_' . $user_id);
        update_field('clicif', $data['CLICIF'], 'user_' . $user_id);
        update_field('clifopa', $data['CLIFOPA'], 'user_' . $user_id);
        update_field('clicc', $data['CLICC'], 'user_' . $user_id);
        update_field('clicont', $data['CLICONT'], 'user_' . $user_id);
        update_field('clidtopv', $data['CLIDTOPV'], 'user_' . $user_id);
        update_field('cliclidiat', $data['CLICLIDIAT'], 'user_' . $user_id);
        update_field('clitest', $data['CLITEST'], 'user_' . $user_id);
        update_field('cliibee', $data['CLIIBEE'], 'user_' . $user_id);
        update_field('emailcomercial', $data['EMAILCOMERCIAL'], 'user_' . $user_id);

        // Agregar campos a la información de envio y facturación de WooCommerce
        update_user_meta($user_id, 'billing_address_1', $data['DIRDIRE']);
        update_user_meta($user_id, 'billing_city', $data['DIRPOBL']);
        update_user_meta($user_id, 'billing_postcode', $data['CLICOPO']);
        update_user_meta($user_id, 'billing_phone', $data['TELNOM']);
        update_user_meta($user_id, 'billing_country', 'ES');

        update_user_meta($user_id, 'shipping_address_1', $data['DIRDIRE']);
        update_user_meta($user_id, 'shipping_city', $data['DIRPOBL']);
        update_user_meta($user_id, 'shipping_postcode', $data['CLICOPO']);
        update_user_meta($user_id, 'shipping_country', 'ES');
        
        return $user_id;
    }


    /**
     * Importa o actualiza una Categoria de WooCommerce
     *
     * @param array $data Array asociativo con los datos de la marca ('CODIGO' y 'NOMBRE').
     * @return int|WP_Error Devuelve el ID del término insertado o actualizado, o un error en caso de fallo.
     */
    public function importCategory($data) {
        if (empty($data['CODIGO']) || empty($data['NOMBRE'])) { return new WP_Error('missing_data', 'El código o el nombre de la categoría están vacíos.'); }
        $existingCategory = get_term_by('slug', sanitize_title($data['CODIGO'].'-'.$data['NOMBRE']), 'product_cat');

        if ($existingCategory) {
            $term_id = wp_update_term($existingCategory->term_id, 'product_cat', ['name' => mb_convert_encoding($data['NOMBRE'], 'UTF-8', 'ISO-8859-1')]);
            $category_term_id = $existingCategory->term_id;
        } else {
            $term_id = wp_insert_term(mb_convert_encoding($data['NOMBRE'], 'UTF-8', 'ISO-8859-1'), 'product_cat', ['slug' => sanitize_title($data['CODIGO'].'-'.$data['NOMBRE'])]);
            $category_term_id = $term_id['term_id'];
        }

        if (is_wp_error($term_id)) { return $term_id; }
        update_field('codfamilia', $data['CODIGO'], 'term_' . $category_term_id);

        // Procesar las subcategorías y sub-subcategorías
        $subcategory_ids = [];
        if (!empty($data['SUBFAMILIAS'])) {
            foreach ($data['SUBFAMILIAS'] as $subcategory) {
                if (!empty($subcategory['CODIGO']) && !empty($subcategory['NOMBRE'])) {

                    // Verificar si la subcategoría existe
                    $existingSubcategory = get_term_by('slug', sanitize_title($subcategory['CODIGO'].'-'.$subcategory['NOMBRE']), 'product_cat');
                    if ($existingSubcategory) {
                        $updated_subcategory = wp_update_term($existingSubcategory->term_id, 'product_cat', [
                            'name' => mb_convert_encoding($subcategory['NOMBRE'], 'UTF-8', 'ISO-8859-1'),
                            'parent' => $category_term_id,
                        ]);
                        if (!is_wp_error($updated_subcategory)) { $subcategory_ids[] = $existingSubcategory->term_id; }
                    } else {
                        $inserted_subcategory = wp_insert_term(
                            mb_convert_encoding($subcategory['NOMBRE'], 'UTF-8', 'ISO-8859-1'),
                            'product_cat', [
                                'slug' => sanitize_title($subcategory['CODIGO'].'-'.$subcategory['NOMBRE']),
                                'parent' => $category_term_id
                            ]
                        );
                        if (!is_wp_error($inserted_subcategory)) { $subcategory_ids[] = $inserted_subcategory['term_id']; }
                    }

                    $subcategory_term_id = $existingSubcategory ? $existingSubcategory->term_id : $inserted_subcategory['term_id'];
                    update_field('codfamilia', $subcategory['CODIGO'], 'term_' . $subcategory_term_id);

                    // Procesar las sub-subcategorías
                    if (!empty($subcategory['SUBSUBFAMILIAS'])) {
                        foreach ($subcategory['SUBSUBFAMILIAS'] as $subsubcategory) {
                            if (!empty($subsubcategory['CODIGO']) && !empty($subsubcategory['NOMBRE'])) {

                                $existingSubsubcategory = get_term_by('slug', sanitize_title($subsubcategory['CODIGO'].'-'.$subsubcategory['NOMBRE']), 'product_cat');
                                if ($existingSubsubcategory) {
                                    $updated_subsubcategory = wp_update_term($existingSubsubcategory->term_id, 'product_cat', [
                                        'name' => mb_convert_encoding($subsubcategory['NOMBRE'], 'UTF-8', 'ISO-8859-1'),
                                        'parent' => $subcategory_term_id,
                                    ]);
                                } else {
                                    wp_insert_term(
                                        mb_convert_encoding($subsubcategory['NOMBRE'], 'UTF-8', 'ISO-8859-1'),
                                        'product_cat', [
                                            'slug' => sanitize_title($subsubcategory['CODIGO'].'-'.$subsubcategory['NOMBRE']),
                                            'parent' => $subcategory_term_id
                                        ]
                                    );
                                }
                            }//if codigo&nombre not empty
                        }//foreach subsubfam
                    }//if subsubfam
                }//if codigo&nombre not empty
            }//foreach subfamilias
        }//if subfam
        return array('category' => $category_term_id, 'subcategories' => $subcategory_ids);
    }



    /**
     * Importa o actualiza una marca en WooCommerce.
     *
     * @param array $data Array asociativo con los datos de la marca ('CODIGO' y 'NOMBRE').
     * @return int|WP_Error Devuelve el ID del término insertado o actualizado, o un error en caso de fallo.
     */
    public function importBrand($data) {
        // Validar que los datos necesarios están presentes
        if (empty($data['CODIGO']) || empty($data['NOMBRE'])) {
            return new WP_Error('missing_data', 'El código o el nombre de la marca están vacíos.');
        }

        // Comprobar si la marca ya existe en la taxonomía personalizada 'marca'
        $existingBrand = get_term_by('slug', sanitize_title($data['CODIGO'].'-'.$data['NOMBRE']), 'marca');

        // Si ya existe, actualizar el nombre de la marca
        // Si no existe, crear la nueva marca
        if ($existingBrand) {
            $term_id = wp_update_term($existingBrand->term_id, 'marca', [
                'name' => $data['NOMBRE']
            ]);
        } else {
            $term_id = wp_insert_term($data['NOMBRE'], 'marca', [
                'slug' => sanitize_title($data['CODIGO'].'-'.$data['NOMBRE'])
            ]);
        }
        update_field('codfamilia', $data['CODIGO'], 'term_' . $brand_term_id);
        if (is_wp_error($term_id)) {
            return $term_id; 
        }

        /* COMENTEM TRADUCCIÓ
        global $sitepress;
        $languages = $sitepress->get_active_languages();

        foreach ($languages as $lang_code => $lang_details) {
            if ($lang_code != 'es') {
                $translated_term_id = apply_filters('wpml_object_id', $brand_term_id, 'marca', false, $lang_code);
                if (!$translated_term_id) { $translated_term_id = $this->duplicateBrandForLanguage($brand_term_id, $lang_code, $data['NOMBRE']); }
            }
        }
        */
        return $brand_term_id;
    }


    /**
     * Funciones traducciones
     * duplicateBrandForLanguage() traduïm les brands aplicant el mateix nom que la Brand original
     * 
     * */
    public function duplicateBrandForLanguage($brand_term_id, $lang_code, $name) {
        global $sitepress, $iclTranslationManagement;
        $term_data = [ 'name' => $name, 'slug' => sanitize_title($name), 'description' => '', 'parent' => 0 ];
        $translated_term = wp_insert_term($name, 'marca', $term_data);
        
        if (!is_wp_error($translated_term)) {
            $translated_term_id = $translated_term['term_id'];
            $iclTranslationManagement->make_duplicate($brand_term_id, $lang_code);

            // Establecer la traducción en WPML
            do_action('wpml_set_element_language_details', [
                'element_id' => $translated_term_id,
                'element_type' => 'tax_' . 'marca',
                'trid' => wpml_get_content_trid('tax_' . 'marca', $brand_term_id),
                'language_code' => $lang_code,
                'source_language_code' => 'es' // Suponiendo que el idioma original sea español
            ]);
        return $translated_term_id;
    }

    return null;
    }
    /**
     * Funciones auxiliares
     * 
     * 
     * */
        public function getProductCategories($data) {
            $categories = [];
            $external_codes = [];
            if (!empty($data['ARTFAMI'])) { $external_codes[] = $data['ARTFAMI']; }
            if (!empty($data['ARTSUBF'])) { $external_codes[] = $data['ARTSUBF']; }
            if (!empty($data['ARTFAMI2'])) { $external_codes[] = $data['ARTFAMI2']; }
            if (!empty($data['ARTSUBFAMI2'])) { $external_codes[] = $data['ARTSUBFAMI2']; }

            // Obtener todas las categorías de WooCommerce
            $all_categories = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]);
            foreach ($all_categories as $category) {
                $codfamilia = get_field('codfamilia', 'term_' . $category->term_id); // Obtener el campo ACF 'codfamilia'
                if (in_array($codfamilia, $external_codes)) {
                    $categories[] = $category->term_id; // Añadir el ID de la categoría si hay coincidencia
                }
            }
            return $categories; // Devolver las categorías coincidentes
        }


        public function getProductBrand($artcodm) {
            if (empty($artcodm)) { return null; }
            $brand = get_terms([ 'taxonomy' => 'marca', 'meta_key' => 'codfamilia', 'meta_value' => $artcodm, 'fields' => 'ids' ]);
            return !empty($brand) ? $brand[0] : null;
        }

        // Función para manejar la imagen del producto
        
        public function handleProductImage($product_id, $sku) {

            // Verificar si el producto tiene una imagen destacada
            $current_image_id = get_post_thumbnail_id($product_id);

            $upload_dir = wp_upload_dir(); // Obtiene las rutas de uploads
            $new_image_path = $upload_dir['basedir'] . '/import-images/' . $sku . '.jpg';

            /*echo $new_image_path;
            echo '<br>'.realpath($new_image_path).'<br>';
            $image_url = $upload_dir['baseurl'] . '/import-images/' . $sku . '.jpg';
            $response = wp_remote_get($image_url);
            var_dump( wp_remote_retrieve_response_code($response) ); echo '<br>';*/


            if (!file_exists($new_image_path)) {
                $placeholder_id = get_option('woocommerce_placeholder_image', false);
                if ($placeholder_id) {  set_post_thumbnail($product_id, $placeholder_id); }
                return array("message" => "<strong>Imagen no encontrada</strong>. Placeholder asignado para el producto: $sku. <a href='".site_url()."/wp-content/import-images/$sku.jpg' target='_blank'><i class='fi fi-rr-picture'></i> Ver</a>","status" => 404);
            }

            if ($current_image_id) {

                $current_image_path = get_attached_file($current_image_id);
                if (file_exists($current_image_path) && $this->areImagesEqual($current_image_path, $new_image_path)) {

                    update_post_meta($current_image_id, '_wp_attachment_image_alt', get_the_title($product_id));
                    return array("message" => "Las imágenes son iguales. No se realizó ningún cambio para el producto: $sku. <a href='".site_url()."/wp-content/import-images/$sku.jpg' target='_blank'><i class='fi fi-rr-picture'></i> Ver</a>","status" => 200);
                }else{
                    wp_delete_attachment($current_image_id, true);
                }
            }

            $attachment_id = $this->uploadImageToMediaLibrary($new_image_path);
            if ($attachment_id) {
                update_post_meta($attachment_id, '_wp_attachment_image_alt', get_the_title($product_id));
                set_post_thumbnail($product_id, $attachment_id);
                return array("message" => "Imagen actualizada correctamente para el producto: $sku. <a href='".site_url()."/wp-content/import-images/$sku.jpg' target='_blank'><i class='fi fi-rr-picture'></i> Ver</a>","status" => 200);
            }

        }//handleProductImage

        // Función para comparar si dos imágenes son iguales
        private function areImagesEqual($image_path_1, $image_path_2) {
            // Obtener las hash de ambas imágenes
            $hash1 = md5_file($image_path_1);
            $hash2 = md5_file($image_path_2);

            // Comparar los hashes
            return $hash1 === $hash2;
        }

        // Función para subir la imagen a la mediateca
        private function uploadImageToMediaLibrary($image_path) {
            $upload = wp_upload_bits(basename($image_path), null, file_get_contents($image_path));

            if (!$upload['error']) {
                $wp_filetype = wp_check_filetype($upload['file']);
                $attachment = array(
                    'guid'           => $upload['url'],
                    'post_mime_type' => $wp_filetype['type'],
                    'post_title'     => sanitize_file_name(basename($upload['file'])),
                    'post_content'   => '',
                    'post_status'    => 'inherit'
                );

                $attachment_id = wp_insert_attachment($attachment, $upload['file']);
                if (is_wp_error($attachment_id)) {
                    return false; // Error al insertar el archivo en la mediateca
                }

                require_once(ABSPATH . 'wp-admin/includes/image.php');
                $attach_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
                wp_update_attachment_metadata($attachment_id, $attach_data);


                return $attachment_id;
            }

            return false;
        }

}//class

    ?>