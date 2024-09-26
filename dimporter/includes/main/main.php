<?php
/* 
 * Opciones principales del plugin
 * Guardamos las diferentes rutas por defecto de la búsqueda de los .DAT
 */

    if(isset($_POST['option']) && $_POST['option']=='generales'){

        set_dimporter_option('products_path', $_POST['products_path']);
        set_dimporter_option('category_path', $_POST['category_path']);
        set_dimporter_option('subcategory_path', $_POST['subcategory_path']);
        set_dimporter_option('clients_path', $_POST['clients_path']);
        set_dimporter_option('brands_path', $_POST['brands_path']);

        set_dimporter_option('orderheader_path', $_POST['orderheader_path']);
        set_dimporter_option('orderline_path', $_POST['orderline_path']);

        echo '<div class="updated">Dades actualitzades</div>';
    }


?>

<form class="tab-content active" id="csg_chat_gpt_generales" data-tab="generales" method="POST">
    <h2>Adjustos importació</h2>
    <input type="hidden" name="option" value="generales" />

    <div class="row">
        <div class="col-3">
            <label>Ruta productes</label><br>
        </div>
        <div class="col-4">
            <input type="url" name="products_path" value="<?php echo get_dimporter_option('products_path'); ?>" placeholder="Ruta del .DAT de productes" class="width"/>
        </div>
    </div>
    <hr>

    <div class="row m0-bottom">
        <div class="col-3">
            <label>Ruta categories</label><br>
        </div>
        <div class="col-4">
            <input type="url" name="category_path" value="<?php echo get_dimporter_option('category_path'); ?>" placeholder="Ruta del .DAT de categories" class="width"/>
        </div>
    </div>
    <div class="row">
        <div class="col-3">
            <label>Ruta subcategories</label><br>
        </div>
        <div class="col-4">
            <input type="url" name="subcategory_path" value="<?php echo get_dimporter_option('subcategory_path'); ?>" placeholder="Ruta del .DAT de subcategories" class="width"/>
        </div>
    </div>
    <hr>

    <div class="row">
        <div class="col-3">
            <label>Ruta clients</label><br>
        </div>
        <div class="col-4">
            <input type="url" name="clients_path" value="<?php echo get_dimporter_option('clients_path'); ?>" placeholder="Ruta del .DAT de clients" class="width"/>
        </div>
    </div>
    <hr>

    <div class="row">
        <div class="col-3">
            <label>Ruta marques</label><br>
        </div>
        <div class="col-4">
            <input type="url" name="brands_path" value="<?php echo get_dimporter_option('brands_path'); ?>" placeholder="Ruta del .DAT de clients" class="width"/>
        </div>
    </div>
    


    <h2 class="m2-top">Adjustos exportació</h2>
    <div class="row">
        <div class="col-3">
            <label>Ruta exportació capçalera</label><br>
        </div>
        <div class="col-4">
            <input type="url" name="orderheader_path" value="<?php echo get_dimporter_option('orderheader_path'); ?>" readonly placeholder="Ruta del .DAT de clients" class="width"/>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-3">
            <label>Ruta exportació linies</label><br>
        </div>
        <div class="col-4">
            <input type="url" name="orderline_path" value="<?php echo get_dimporter_option('orderline_path'); ?>" readonly placeholder="Ruta del .DAT de clients" class="width"/>
        </div>
    </div>


    <div class="row m2-top">
        <div class="col-3"><input type="submit" class="button width" value="Guardar adjustos" /></div>
    </div>

</form>