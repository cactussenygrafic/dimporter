/************************************
 *
 *.  IMPORTACIÓ DE CLIENT
 *
 ************************************/

function runClientImport() {

    var values = jQuery('#values').val();
    if (typeof values === 'string') {
        values = JSON.parse(values);
    }
    var totalClients = values.length;
    var importedCount = 0;
    var timestamp = new Date().getTime();

    // Crear el log inicial
    addLog('createClientLog', 'importClient_' + timestamp + '.txt', '', function() {
        jQuery('#log_return').append('<p>Log creado en <strong>/wp-content/uploads/logs/clients/importClient_' + timestamp + '.txt</strong></p>');
        // Comenzar la importación secuencial de los clientes
        processNextClient(0);
    });

    // Función recursiva para procesar cada cliente de manera secuencial
    function processNextClient(index) {
        if (index >= totalClients) {
            jQuery('#log_return').append('<h3>Todos los clientes han sido importados.</h3>');
            jQuery('.progress_bar_fill').css('width', '100%');
            jQuery('#number_imported').text(totalClients);
            return;
        }

        var client = values[index];
        // Hacer la llamada AJAX para importar el cliente
        jQuery.post(csgAjax.ajax_url, {
            action: 'importClient',
            client_data: client
        }, function(data) {
            if (data.data.result === true) {
                jQuery('#log_return').append('<div class="updated">Cliente <strong>' + data.data.client + '</strong> importado</div>');
                addLog('addClienteLog', 'importClient_' + timestamp + '.txt', 'Cliente ' + data.data.client + ' importado\n');
            } else {
                jQuery('#log_return').append('<div class="error">Error al importar cliente <strong>' + data.data.client + '</strong>: ' + data.data.message + '</div>');
                addLog('addClienteLog', 'importClient_' + timestamp + '.txt', '**ERROR Cliente ' + data.data.client + ' no importado: ' + data.data.message + '**\n');
            }

            // Actualizar UI
            importedCount++;
            jQuery('#number_imported').text(importedCount);
            var progressPercentage = (importedCount / totalClients) * 100;
            jQuery('.progress_bar_fill').css('width', progressPercentage + '%');

            // Llamar a la función para procesar el siguiente cliente
            processNextClient(index + 1);
        });
    }
}

/************************************
 *
 *.  IMPORTACIÓ DE MARCA
 *
 ************************************/

function runBrandsImport() {

    var values = jQuery('#values').val();
    if (typeof values === 'string') {
        values = JSON.parse(values);
    }
    var totalBrands = values.length;
    var importedCount = 0;
    var timestamp = new Date().getTime();

    addLog('createBrandLog', 'importBrands_' + timestamp + '.txt', '', function() {
        jQuery('#log_return').append('<p>Log creado en <strong>/wp-content/uploads/logs/brands/importBrands_' + timestamp + '.txt</strong></p>');
        processNextBrand(0);
    });

    // Función recursiva para procesar cada cliente de manera secuencial
    function processNextBrand(index) {
        console.log('processNextBrand'+index);
        if (index >= totalBrands) {
            jQuery('#log_return').append('<h3>Todas las marcas han sido importadas.</h3>');
            jQuery('.progress_bar_fill').css('width', '100%');
            jQuery('#number_imported').text(totalBrands);
            return;
        }

        var brand = values[index];

        jQuery.post(csgAjax.ajax_url, {
            action: 'importBrand',
            brand_data: brand
        }, function(data) {
            if (data.data.result === true) {
                jQuery('#log_return').append('<div class="updated">Marca <strong>' + data.data.brand + '</strong> importada</div>');
                addLog('addBrandLog', 'importBrands_' + timestamp + '.txt', 'Marca ' + data.data.brand + ' importada\n');
            } else {
                jQuery('#log_return').append('<div class="error">Error al importar Marca <strong>' + data.data.brand + '</strong>: ' + data.data.message + '</div>');
                addLog('addBrandLog', 'importBrands_' + timestamp + '.txt', '**ERROR Marca ' + data.data.brand + ' no importada: ' + data.data.message + '**\n');
            }

            // Actualizar UI
            importedCount++;
            jQuery('#number_imported').text(importedCount);
            var progressPercentage = (importedCount / totalBrands) * 100;
            jQuery('.progress_bar_fill').css('width', progressPercentage + '%');

            // Llamar a la función para procesar el siguiente cliente
            processNextBrand(index + 1);
        });
    }
}

/************************************
 *
 *.  IMPORTACIÓ DE CATEGORIES
 *
 ************************************/

function runCategoriesImport() {
    console.log('runCategoriesImport');
    var values = jQuery('#values').val();
    if (typeof values === 'string') {
        values = JSON.parse(values);
    }
    var totalCategories = values.length;
    var importedCount = 0;
    var timestamp = new Date().getTime();

    addLog('createCategoryLog', 'importCategory_' + timestamp + '.txt', '', function() {
        jQuery('#log_return').append('<p>Log creado en <strong>/wp-content/uploads/logs/category/importCategory_' + timestamp + '.txt</strong></p>');
        processNextCategory(0);
    });

    // Función recursiva para procesar cada cliente de manera secuencial
    function processNextCategory(index) {
        console.log('processNextCategory');
        if (index >= totalCategories) {
            jQuery('#log_return').append('<h3>Todas las marcas han sido importadas.</h3>');
            jQuery('.progress_bar_fill').css('width', '100%');
            jQuery('#number_imported').text(totalCategories);
            return;
        }

        var category = values[index];

        jQuery.post(csgAjax.ajax_url, {
            action: 'importCategories',
            category_data: category
        }, function(data) {
            if (data.data.result === true) {
                jQuery('#log_return').append('<div class="updated">Categoria <strong>' + data.data.category + '</strong> importada</div>');
                addLog('addCategoryLog', 'importCategory_' + timestamp + '.txt', 'Categoria ' + data.data.category + ' importada\n');

                data.data.subcategories.forEach(function(subcategory) {
                    jQuery('#log_return').append('<div class="updated">–– Subcategoría <strong>' + subcategory.name + '</strong> importada con ID ' + subcategory.id + '</div>');
                    addLog('addCategoryLog', 'importCategory_' + timestamp + '.txt', '–– Subcategoría ' + subcategory.name + ' importada\n');
                });

            } else {
                jQuery('#log_return').append('<div class="error">Error al importar cliente <strong>' + data.data.category + '</strong>: ' + data.data.message + '</div>');
                addLog('addCategoryLog', 'importCategory_' + timestamp + '.txt', '**ERROR Categoria ' + data.data.category + ' no importado: ' + data.data.message + '**\n');

                data.data.subcategories.forEach(function(subcategory) {
                    jQuery('#log_return').append('<div class="error">–– Error Subcategoría <strong>' + subcategory.name + '</strong> importada con ID ' + subcategory.id + '</div>');
                    addLog('addCategoryLog', 'importCategory_' + timestamp + '.txt', '****ERROR Subcategoría ' + subcategory.name + ' importada****\n');
                });
            }

            // Actualizar UI
            importedCount++;
            jQuery('#number_imported').text(importedCount);
            var progressPercentage = (importedCount / totalCategories) * 100;
            jQuery('.progress_bar_fill').css('width', progressPercentage + '%');

            // Llamar a la función para procesar el siguiente cliente
            processNextCategory(index + 1);
        });
    }
}


/************************************
 *
 *.  IMPORTACIÓ DE PRODUCTES
 *
 ************************************/

function runProductsImport() {

    var values = jQuery('#values').val();
    if (typeof values === 'string') {
        values = JSON.parse(values);
    }
    var totalClients = values.length;
    var importedCount = 0;
    var timestamp = new Date().getTime();

    // Crear el log inicial
    addLog('createProductLog', 'importProducts_' + timestamp + '.txt', '', function() {
        jQuery('#log_return').append('<p>Log creado en <strong>/wp-content/uploads/logs/clients/importProducts_' + timestamp + '.txt</strong></p>');
        // Comenzar la importación secuencial de los clientes
        processNextProduct(0);
    });

    // Función recursiva para procesar cada cliente de manera secuencial
    function processNextProduct(index) {
        if (index >= totalClients) {
            jQuery('#log_return').append('<h3>Todos los productos han sido importados.</h3>');
            jQuery('.progress_bar_fill').css('width', '100%');
            jQuery('#number_imported').text(totalClients);
            return;
        }

        var product = values[index];
        // Hacer la llamada AJAX para importar el cliente
        jQuery.post(csgAjax.ajax_url, {
            action: 'importProduct',
            product_data: product
        }, function(data) {
            if (data.data.result === true) {
                jQuery('#log_return').append('<div class="updated">Producto <strong>' + data.data.product + '</strong>. <a href="'+data.data.product_link+'" target="_blank"><i class="fi fi-rr-eye"></i> Ver</a></div>');
                addLog('addProductLog', 'importProducts_' + timestamp + '.txt', 'Producto ' + data.data.product + ' importado\n');
            } else {
                jQuery('#log_return').append('<div class="error">Error al importar producto <strong>' + data.data.product + '</strong>: ' + data.data.message + '. <a href="'+data.data.product_link+'" target="_blank"><i class="fi fi-rr-eye"></i> Ver</a></div>');
                addLog('addProductLog', 'importProducts_' + timestamp + '.txt', '**ERROR Producto ' + data.data.product + ' no importado: ' + data.data.message + '**\n');
            }

            if(data.data.image_status==200){
                    jQuery('#log_return').append('<div class="updated"> –– ' + data.data.image_message + '</div>');
                    addLog('addProductLog', 'importProducts_' + timestamp + '.txt', ' –– ' + data.data.image_message + '\n');
            }else if(data.data.image_status==404){
                    jQuery('#log_return').append('<div class="error"> –– ' + data.data.image_message + '</div>');
                    addLog('addProductLog', 'importProducts_' + timestamp + '.txt', ' –– ERROR: ' + data.data.image_message + '\n');
            }


            // Actualizar UI
            importedCount++;
            jQuery('#number_imported').text(importedCount);
            var progressPercentage = (importedCount / totalClients) * 100;
            jQuery('.progress_bar_fill').css('width', progressPercentage + '%');

            // Llamar a la función para procesar el siguiente cliente
            processNextProduct(index + 1);
        });
    }
}


/************************************
 *
 *.  AJAX LOGS IMPORTACIÓ
 *
 ************************************/

function addLog(action, name, entry, callback) {
    jQuery.post(csgAjax.ajax_url, {
        action: action,
        log_name: name,
        log_entry: entry
    }, function(logResponse) {
        if (logResponse.success) {
            console.log('Log entry added: ' + name);
            if (callback && typeof callback === 'function') {
                callback();
            }
        } else {
            console.error('Error writing log entry for log: ' + name);
        }
    });
}
