<?php
/* 
 * Importador
 * Montamos el importador de productos con la funcion importData (/src/setup.php)
 */
?>
<h2>Importador Productes</h2>

<form method="post">
	<div class="row">
		<div class="col-md-5">
			<strong>Productes</strong><br>
			<input type="url" class="width" name="file_path" value="<?php echo get_dimporter_option('products_path'); ?>" />
		</div>
		<div class="col-md-2"><br>
			<input type="submit" value="Importar" class="button action" />
		</div>
	</div>
</form>
<?php 

	if(isset($_POST['file_path'])){
		importData('product', $_POST['file_path']);
	}
?>