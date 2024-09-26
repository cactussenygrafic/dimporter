<?php
/* 
 * Importador
 * Montamos el importador de marcas con la funcion importData (/src/setup.php)
 */
?>
<h2>Importador Marques</h2>

<form method="post">
	<div class="row">
		<div class="col-md-5">
			<strong>Marques</strong><br>
			<input type="url" class="width" name="file_path" value="<?php echo get_dimporter_option('brands_path'); ?>" />
		</div>
		<div class="col-md-2"><br>
			<input type="submit" value="Importar" class="button action" />
		</div>
	</div>
</form>
<?php 

	if(isset($_POST['file_path'])){
		importData('brand', $_POST['file_path']);
	}
?>