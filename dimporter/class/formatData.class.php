<?php

class formatData {
    

    public function formatArticles($file) {
        //$file = 'WEBARTI.DAT';
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $formattedArticles = [];

        foreach ($lines as $line) {
            //$line = mb_convert_encoding($line, 'UTF-8', 'Windows-1252');
            $article = [
                'ARTCODI'    => rtrim(mb_convert_encoding(substr($line, 0, 20), 'UTF-8', 'Windows-1252')),
                'ARTFAMI'    => rtrim(mb_convert_encoding(substr($line, 20, 10), 'UTF-8', 'Windows-1252')),
                'ARTDESC'    => rtrim(mb_convert_encoding(substr($line, 30, 30), 'UTF-8', 'Windows-1252')),
                'ARTPREC'    => rtrim(mb_convert_encoding(substr($line, 60, 9), 'UTF-8', 'Windows-1252')),
                'ARTTIVA'    => rtrim(mb_convert_encoding(substr($line, 69, 2), 'UTF-8', 'Windows-1252')),
                'ARTSTOC'    => rtrim(mb_convert_encoding(substr($line, 71, 8), 'UTF-8', 'Windows-1252')),
                'ARTSUBF'    => rtrim(mb_convert_encoding(substr($line, 79, 10), 'UTF-8', 'Windows-1252')),
                'ARTVEND'    => rtrim(mb_convert_encoding(substr($line, 89, 6), 'UTF-8', 'Windows-1252')),
                'ARTPVER'    => rtrim(mb_convert_encoding(substr($line, 95, 9), 'UTF-8', 'Windows-1252')),
                'ARTVOLU'    => rtrim(mb_convert_encoding(substr($line, 104, 8), 'UTF-8', 'Windows-1252')),
                'ARTNOVE'    => rtrim(mb_convert_encoding(substr($line, 112, 2), 'UTF-8', 'Windows-1252')),
                'ARTDESCCA'  => rtrim(mb_convert_encoding(substr($line, 114, 250), 'UTF-8', 'Windows-1252')),
                'ARTDESCES'  => rtrim(mb_convert_encoding(substr($line, 364, 250), 'UTF-8', 'Windows-1252')),
                'ARTCODM'    => rtrim(mb_convert_encoding(substr($line, 614, 10), 'UTF-8', 'Windows-1252')),
                'ARTIBEE'    => rtrim(mb_convert_encoding(substr($line, 623, 9), 'UTF-8', 'Windows-1252')),
                'ARTFAMI2'   => rtrim(mb_convert_encoding(substr($line, 633, 10), 'UTF-8', 'Windows-1252')),
                'ARTSUBFAMI2'=> rtrim(mb_convert_encoding(substr($line, 643, 10), 'UTF-8', 'Windows-1252')),
                'variacions' => ''  // Variaciones de producto (0 caracteres, no especificado)
            ];

            // Convertir los valores numéricos que están multiplicados por 100, 1000, 10000 según corresponda
            $article['ARTPREC'] = intval($article['ARTPREC']) / 1000;
            $article['ARTSTOC'] = intval($article['ARTSTOC']) / 100;
            $article['ARTVEND'] = intval($article['ARTVEND']) / 100;
            $article['ARTPVER'] = intval($article['ARTPVER']) / 10000;
            $article['ARTVOLU'] = intval($article['ARTVOLU']) / 100;

            $formattedArticles[] = $article;
        }

        return $formattedArticles;
    }


    public function formatClients($file) {
        //$file = 'WEBCLIE.DAT';
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $formattedClients = [];

        foreach ($lines as $line) {
            $client = [
                'CLICODI'=>rtrim(mb_convert_encoding(substr($line, 0, 20), 'UTF-8', 'Windows-1252')),   // Código de cliente (20 caracteres, posición 1)
                'CLINOMB'=>rtrim(mb_convert_encoding(substr($line, 20, 30), 'UTF-8', 'Windows-1252')),  // Nombre o razón social (30 caracteres, posición 21)
                'CLITARI'=>rtrim(mb_convert_encoding(substr($line, 50, 10), 'UTF-8', 'Windows-1252')),  // Tarifa de cliente (10 caracteres, posición 51)
                'CLIRUTA'=>rtrim(mb_convert_encoding(substr($line, 60, 2), 'UTF-8', 'Windows-1252')),   // Ruta de cliente (2 caracteres, posición 61)
                'CLIORDE'=>intval(rtrim(mb_convert_encoding(substr($line, 62, 3), 'UTF-8', 'Windows-1252'))), // Orden dentro de la ruta (3 caracteres, posición 63)
                'CLIESTA'=>rtrim(mb_convert_encoding(substr($line, 65, 1), 'UTF-8', 'Windows-1252')),   // Estado (1 carácter, posición 66)
                'CLITIVA'=>rtrim(mb_convert_encoding(substr($line, 66, 1), 'UTF-8', 'Windows-1252')),   // Cliente con IVA (1 carácter, posición 67)
                'CLIDTO1'=>intval(rtrim(mb_convert_encoding(substr($line, 67, 4), 'UTF-8', 'Windows-1252'))) / 100, // % descuento 1 * 100 (4 caracteres, posición 68)
                'CLIDTO2'=>intval(rtrim(mb_convert_encoding(substr($line, 71, 4), 'UTF-8', 'Windows-1252'))) / 100, // % descuento 2 * 100 (4 caracteres, posición 72)
                'CLIDTO3'=>intval(rtrim(mb_convert_encoding(substr($line, 76, 5), 'UTF-8', 'Windows-1252'))) / 100, // % descuento 3 * 100 (5 caracteres, posición 77)
                'DIRDIRE'=>rtrim(mb_convert_encoding(substr($line, 80, 30), 'UTF-8', 'Windows-1252')),  // Dirección de cliente (30 caracteres, posición 81)
                'DIRPOBL'=>rtrim(mb_convert_encoding(substr($line, 110, 30), 'UTF-8', 'Windows-1252')), // Población (30 caracteres, posición 111)
                'CLICOPO'=>rtrim(mb_convert_encoding(substr($line, 140, 5), 'UTF-8', 'Windows-1252')),  // Código Postal (5 caracteres, posición 141)
                'TELNOM' =>rtrim(mb_convert_encoding(substr($line, 145, 30), 'UTF-8', 'Windows-1252')), // Nombre fiscal (30 caracteres, posición 146)
                'TELTELE'=>rtrim(mb_convert_encoding(substr($line, 175, 12), 'UTF-8', 'Windows-1252')), // Teléfono (12 caracteres, posición 176)
                'CLICIF' =>rtrim(mb_convert_encoding(substr($line, 187, 12), 'UTF-8', 'Windows-1252')), // CIF del cliente (12 caracteres, posición 188)
                'CLIFOPA'=>rtrim(mb_convert_encoding(substr($line, 199, 30), 'UTF-8', 'Windows-1252')), // Forma de pago (30 caracteres, posición 200)
                'CLICC'  =>rtrim(mb_convert_encoding(substr($line, 229, 20), 'UTF-8', 'Windows-1252')), // Cuenta corriente (20 caracteres, posición 230)
                'CLICONT'=>rtrim(mb_convert_encoding(substr($line, 249, 30), 'UTF-8', 'Windows-1252')), // Persona de contacto (30 caracteres, posición 250)
                'CLIDTOPV'  => rtrim(mb_convert_encoding(substr($line, 279, 1), 'UTF-8', 'Windows-1252')),  // Aplicar el descuento al punto verde (1 carácter, posición 280)
                'CLICLIDIAT'=> rtrim(mb_convert_encoding(substr($line, 280, 1), 'UTF-8', 'Windows-1252')),  // Día de cierre (1 carácter, posición 281)
                'CLITEST'=>rtrim(mb_convert_encoding(substr($line, 281, 2), 'UTF-8', 'Windows-1252')),  // Tipo de establecimiento (2 caracteres, posición 282)
                'CLIMAIL'=>rtrim(mb_convert_encoding(substr($line, 283, 80), 'UTF-8', 'Windows-1252')), // Correo electrónico (80 caracteres, posición 284)
                'CLIIBEE'=>rtrim(mb_convert_encoding(substr($line, 363, 2), 'UTF-8', 'Windows-1252')),  // Calcular IBEE (2 caracteres, posición 364)
                'EMAILCOMERCIAL' => '',
            ];

            // Convertir los valores numéricos que están multiplicados por 100
            $client['CLIDTO1'] = $client['CLIDTO1'] / 100;
            $client['CLIDTO2'] = $client['CLIDTO2'] / 100;
            $client['CLIDTO3'] = $client['CLIDTO3'] / 100;

            $formattedClients[] = $client;
        }
        return $formattedClients;
    }


    public function formatBrands($file) {
        //$file = 'WEBMARCA.DAT';
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $formattedBrands = [];

        foreach ($lines as $line) {
            $brand = [
                'CODIGO' => rtrim(mb_convert_encoding(substr($line, 0, 10), 'UTF-8', 'Windows-1252')),// Código de marca (10 caracteres, posición 1)
                'NOMBRE' => rtrim(mb_convert_encoding(substr($line, 10, 30), 'UTF-8', 'Windows-1252'))// Nombre de la marca (30 caracteres, posición 11)
            ];
            $formattedBrands[] = $brand;
        }

        return $formattedBrands;
    }

    public function formatCategories($categoriesFile, $subcategoriesFile) {
            //$categoriesFile = 'WEBFAMI.DAT';
            //$subcategoriesFile = 'WEBSUBFAMI.DAT';
            $categoryLines = file($categoriesFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $subcategoryLines = file($subcategoriesFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $categories = [];

            // Procesar las categorías principales
            foreach ($categoryLines as $line) {
                $line = mb_convert_encoding($line, 'UTF-8', 'Windows-1252');
                $categoryCode = rtrim(substr($line, 0, 10));    // Código de la familia (10 caracteres, posición 1)
                $categoryName = rtrim(substr($line, 10, 50));   // Nombre de la familia (50 caracteres, posición 11)

                $categories[$categoryCode] = [
                    'CODIGO' => rtrim(mb_convert_encoding($categoryCode, 'UTF-8', 'Windows-1252')),
                    'NOMBRE' => rtrim(mb_convert_encoding($categoryName, 'UTF-8', 'Windows-1252')),
                    'SUBFAMILIAS' => [] // Aquí se agregarán las subfamilias
                ];
            }

            // Procesar las subcategorías y sub-subcategorías en el mismo archivo
            foreach ($subcategoryLines as $line) {
                $familyCode = rtrim(substr($line, 0, 10));   // Código de la familia (10 caracteres, posición 1)
                $subcategoryCode = rtrim(substr($line, 10, 10));  // Código de la subfamilia (10 caracteres, posición 11)
                $subcategoryName = rtrim(substr($line, 20, 50));  // Nombre de la subfamilia (50 caracteres, posición 21)

                // Verificar si es una sub-subcategoría (más de 5 caracteres en el código) y pertenece a una subcategoría
                if (strlen($subcategoryCode) > 5) {
                    // Recorrer las subfamilias para encontrar la subcategoría correspondiente
                    foreach ($categories as $familyCodeKey => &$category) {
                        if (strpos($subcategoryCode, $familyCodeKey) === 0) {
                            // Encontrar la subfamilia correspondiente
                            foreach ($category['SUBFAMILIAS'] as &$subfamilia) {
                                if (strpos($subcategoryCode, $subfamilia['CODIGO']) === 0) {
                                    // Añadir sub-subfamilia a la subfamilia
                                    $subfamilia['SUBSUBFAMILIAS'][] = [
                                        'CODIGO' => rtrim(mb_convert_encoding($subcategoryCode, 'UTF-8', 'Windows-1252')),
                                        'NOMBRE' => rtrim(mb_convert_encoding($subcategoryName, 'UTF-8', 'Windows-1252'))
                                    ];
                                    break;
                                }
                            }
                            break;
                        }
                    }
                } else {
                    // Es una subcategoría de nivel 2 (subfamilia)
                    if (isset($categories[$familyCode])) {
                        $categories[$familyCode]['SUBFAMILIAS'][] = [
                            'CODIGO' => rtrim(mb_convert_encoding($subcategoryCode, 'UTF-8', 'Windows-1252')),
                            'NOMBRE' => rtrim(mb_convert_encoding($subcategoryName, 'UTF-8', 'Windows-1252')),
                            'SUBSUBFAMILIAS' => [] // Aquí se agregarán las sub-subfamilias
                        ];
                    }
                }
            }

            return array_values($categories);
    }
}

?>

