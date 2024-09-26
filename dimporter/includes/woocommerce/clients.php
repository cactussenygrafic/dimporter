<?php
/* 
 * Importador
 * Montamos el importador de clientes con la funcion importData (/src/setup.php)
 */
?>
<h2>Importador Clients</h2>

<form method="post">
	<div class="row">
		<div class="col-md-5">
			<strong>Clients</strong><br>
			<input type="url" name="file_path" value="<?php echo get_dimporter_option('clients_path'); ?>" class="width" />
		</div>
		<div class="col-md-2"><br>
			<input type="submit" value="Importar" class="button action" />
		</div>
	</div>
</form>

<?php 

	if(isset($_POST['file_path'])){
		importData('client', $_POST['file_path']);
	}
?>