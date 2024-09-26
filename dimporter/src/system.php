<?php

/**
 * Hook para creación del rol B2B
 *
 * @create_b2b_customer_role crea el Rol
 * @hide_admin_bar_for_b2b_customer Oculta la barra de administracion
 * @restrict_admin_access_for_b2b_customer Desactivar el acceso al área de administración para 'Cliente B2B'
 * @hide_prices_for_b2b_customers Ocultar los precios si el usuario tiene el rol "Cliente B2B"
 * @modify_price_html_for_b2b_customers Eliminamos la visualización del precio en B2B
 * @modify_variation_price_html_for_b2b_customers Eliminamos la visualización del precio en B2B
 */

function create_b2b_customer_role() {
    if (!get_role('customer_b2b')) {
        $customer_capabilities = get_role('customer')->capabilities;
        add_role('customer_b2b', 'Cliente B2B', $customer_capabilities);
    }
}
add_action('init', 'create_b2b_customer_role');

function hide_admin_bar_for_b2b_customer() {
    if (current_user_can('customer_b2b') && !current_user_can('administrator')) {
        show_admin_bar(false); // Desactivar la barra superior
    }
}
add_action('after_setup_theme', 'hide_admin_bar_for_b2b_customer');

function restrict_admin_access_for_b2b_customer() {
    if (current_user_can('customer_b2b') && !current_user_can('administrator') && is_admin()) {
        wp_redirect(home_url()); // Redirigir al homepage
        exit;
    }
}
add_action('admin_init', 'restrict_admin_access_for_b2b_customer');


function modify_price_html_for_b2b_customers($price_html, $product) {
    // Verificar si el usuario está conectado y tiene el rol de "Cliente B2B"
    if (is_user_logged_in() && current_user_can('customer_b2b')) {
        return '<span></span>'; // Mensaje personalizado
    }
    return $price_html; // Devolver el precio normal para otros usuarios
}
add_filter('woocommerce_get_price_html', 'modify_price_html_for_b2b_customers', 10, 2);


function modify_variation_price_html_for_b2b_customers($price, $variation) {
    if (is_user_logged_in() && current_user_can('customer_b2b')) {
        return '<span></span>'; // Mensaje para las variaciones
    }
    return $price;
}
add_filter('woocommerce_variation_get_price_html', 'modify_variation_price_html_for_b2b_customers', 10, 2);



/**
 * Hook para creación del tipos de Order
 *
 * @add_custom_order_statuses Añadir los nuevos estados de pedidos en WooCommerce
 * @add_custom_order_statuses_to_wc Añadir los estados al listado de estados de pedidos de WooCommerce
 * @custom_order_status_colors Añadir colores personalizados para los nuevos estados de pedidos en el backend de WooCommerce
 */

function add_custom_order_statuses() {
    register_post_status('wc-presupuesto_b2b', array(
        'label'                     => 'Presupuesto B2B',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Presupuesto B2B <span class="count">(%s)</span>', 'Presupuesto B2B <span class="count">(%s)</span>'),
        'post_type'                 => array('shop_order'),
    ));

    register_post_status('wc-presupuesto_b2b_procesado', array(
        'label'                     => 'Presupuesto B2B Procesado',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop('Presupuesto B2B Procesado <span class="count">(%s)</span>', 'Presupuesto B2B Procesado <span class="count">(%s)</span>'),
        'post_type'                 => array('shop_order'),
    ));
}
add_action('init', 'add_custom_order_statuses');


function add_custom_order_statuses_to_wc($order_statuses) {
    $new_order_statuses = array();

    foreach ($order_statuses as $key => $status) {
        $new_order_statuses[$key] = $status;

        if ('wc-on-hold' === $key) { // Añadir después del estado 'on-hold'
            $new_order_statuses['wc-presupuesto_b2b'] = 'Presupuesto B2B';
            $new_order_statuses['wc-presupuesto_b2b_procesado'] = 'Presupuesto B2B Procesado';
        }
    }

    return $new_order_statuses;
}
add_filter('wc_order_statuses', 'add_custom_order_statuses_to_wc');

function custom_order_status_colors() {
    echo '<style>
        .order-status.status-presupuesto_b2b {background: #ff4d4d;color: white;}
        .order-status.status-presupuesto_b2b_procesado { background: #32CD32; color: white;}
    </style>';
}
add_action('admin_head', 'custom_order_status_colors');



/**
 * Añadimos el tipo de order B2B y exportamos los nuevos pedidos
 *
 * @woocommerce_new_order Añadimos el estatus wc-presupuesto_b2b si el cliente es B2B y exportamos en .DAT si es B2C
 * @send_custom_order_email Enviamos email al comercial, en el campo ACF emailcomercial de la ficha de cliente
 * @assign_status_onhold El pago por transferencia convierte al pedido en ON-HOLD posteriormente, modificamos el hook para pasarlo a B2B si correpsonde
 */

add_action('woocommerce_new_order', 'assign_status_for_b2b_customers', 20, 1);
function assign_status_for_b2b_customers($order_id) {
    $order = wc_get_order($order_id);
    $user_id = $order->get_user_id();

    // Verificar si el usuario tiene el rol 'customer_b2b'
    if (user_can($user_id, 'customer_b2b')) {
        $order->update_status('wc-presupuesto_b2b', __('Estado cambiado a Presupuesto B2B', 'woocommerce'));
        send_custom_order_email($order);

        /* EXPORT SOLO EN B2C
            $exportOrder = new exportOrders();
            $exportOrder->saveCabecera($order);
            $exportOrder->saveLineas($order);
        */

    } else {
        $exportOrder = new exportOrders();
        $exportOrder->saveCabecera($order);
        $exportOrder->saveLineas($order);
    }
}

function send_custom_order_email($order) {

    $user_id = $order->get_user_id();
    $to = get_field('emailcomercial', 'user_' . $user_id);
    $subject = sprintf(__('Nuevo pedido B2B #%s', 'woocommerce'), $order->get_order_number());

    ob_start();
    wc_get_template('emails/customer-processing-order.php', array('order' => $order));
    $message = ob_get_clean();
    
    $headers = array('Content-Type: text/html; charset=UTF-8');
    wp_mail($to, $subject, $message, $headers);
}


add_action('woocommerce_order_status_on-hold', 'assign_status_onhold', 10, 1);
function assign_status_onhold($order_id) {
    $order = wc_get_order($order_id);
    $user_id = $order->get_user_id();
    if (user_can($user_id, 'customer_b2b')) { $order->update_status('wc-presupuesto_b2b', __('Estado cambiado a Presupuesto B2B', 'woocommerce')); }
}





?>