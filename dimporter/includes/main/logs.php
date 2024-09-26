<?php
    /* 
    * Opciones principales del plugin
    * Visualizamos los diferentes logs de las importaciones
    */
?>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.16/datatables.min.css"/> 
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedcolumns/3.2.4/css/fixedColumns.dataTables.min.css" />
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.16/datatables.min.js"></script>
<script type="text/javascript" scr="https://cdn.datatables.net/fixedcolumns/3.2.4/js/dataTables.fixedColumns.min.js"></script>
<script>
jQuery(document).ready(function($) {   
	$('#logs').dataTable( {
		"paging": true,
		"dom": 'Bfrtip',
		"order": [],
		"fixedHeader": true,
	});
});
</script>

<h2>Logs</h2>

<?php

	global $wpdb;
	$table_name = $wpdb->prefix . 'dimporter_logs';
    $logs = $wpdb->get_results("SELECT id, log_type, log_name, user, log_content, date FROM $table_name", ARRAY_A);
    
    // Si hay registros, se genera la tabla
    if (!empty($logs)) {
        echo '<table id="logs" class="display">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>ID</th>';
        echo '<th>Log Type</th>';
        echo '<th>Log Name</th>';
        echo '<th>User</th>';
        echo '<th>Log</th>';
        echo '<th>Date</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        // Iteramos los registros para mostrarlos en filas de la tabla
        foreach ($logs as $log) {
            echo '<tr>';
            echo '<td>' . esc_html($log['id']) . '</td>';
            echo '<td>' . esc_html($log['log_type']) . '</td>';
            echo '<td>' . esc_html($log['log_name']) . '</td>';
            echo '<td>' . esc_html($log['user']) . '</td>';
            echo '<td><a href="' . esc_html($log['log_content']) . '" target="_blank">' . esc_html($log['log_content']) . '</a></td>';
            echo '<td>' . esc_html($log['date']) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>No hay registros disponibles.</p>';
    }

?>


