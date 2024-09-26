<?php
/* 
 * Importador
 * Montamos el importador de categorias con la funcion importData (/src/setup.php)
 */
?>

<h2>Importador Categories</h2>

<form method="post">
	<div class="row">
		<div class="col-md-5">
			<strong>Families</strong><br>
			<input type="url" name="file_path" class="width" value="<?php echo get_dimporter_option('category_path'); ?>" />
		</div>
		<div class="col-md-5">
			<strong>Subfamilies</strong><br>
			<input type="url" name="file_path_2" class="width" value="<?php echo get_dimporter_option('subcategory_path'); ?>" />
		</div>
		<div class="col-md-2"><br>
			<input type="submit" value="Importar" class="button action" />
		</div>
	</div>
</form>

<?php 

	if(isset($_POST['file_path']) && isset($_POST['file_path_2'])){
		importData('category', $_POST['file_path'], $_POST['file_path_2']);
	}
?>