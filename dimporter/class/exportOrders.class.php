<?php

class exportOrders{

  public function formatLine($order, $line_item, $line_index) {
      $fields = [];
      $product = $line_item->get_product();

      $regular_price = $product ? $product->get_regular_price() : 0;
      $sale_price = $product ? $product->get_sale_price() : 0;
      $discount = ($regular_price && $sale_price) ? ($regular_price - $sale_price) : 0;

      if($product->get_tax_class() == ''){
        $iva = "21";
      }elseif($product->get_tax_class()=='iva-reducido'){
        $iva = "10";
      }elseif($product->get_tax_class()=='iva-superreducido'){
        $iva = "04";
      }

      // Definir los campos y aplicar el formato correspondiente
      $fields['PEDLVISI']  = str_pad($order->get_id(), 4, " ", STR_PAD_RIGHT); // Número de visita (4 caracteres)
      $fields['PEDLARTI']  = str_pad($product ? $product->get_sku() : '', 20, " ", STR_PAD_RIGHT); // Código de Artículo (20 caracteres)
      $fields['PEDLPDTO']  = str_pad('', 4, " ", STR_PAD_RIGHT); // % Dto1 * 100 (4 caracteres)
      $fields['PEDLPDT2']  = str_pad('', 4, " ", STR_PAD_RIGHT); // % Dto2 * 100 (4 caracteres)
      $fields['PEDLCDTO']  = str_pad($discount * 100, 8, "0", STR_PAD_LEFT); // Importe de descuento * 100 (8 caracteres)
      $fields['PEDLCANT']  = str_pad(intval($line_item->get_quantity()) * 100, 7, "0", STR_PAD_LEFT); // Cantidad de Artículo * 100 (7 caracteres)
      $fields['PEDLPREC']  = str_pad(intval($line_item->get_subtotal()) * 1000, 9, "0", STR_PAD_LEFT); // Precio unitario * 1000 (9 caracteres)
      $fields['PEDLAREG']  = str_pad('', 20, " ", STR_PAD_RIGHT); // Artículo de regalo (20 caracteres)
      $fields['PEDLCREG']  = str_pad('', 7, " ", STR_PAD_RIGHT); // Cantidad de regalo * 100 (7 caracteres)
      $fields['PEDLTIPO']  = str_pad('U', 1, " ", STR_PAD_RIGHT); // Tipo de unidad C / U / K (1 caracter)
      $fields['PEDLTIVA']  = str_pad($iva, 2, " ", STR_PAD_RIGHT); // Tipo de IVA (2 caracteres)
      $fields['PEDLPROM']  = str_pad('', 1, " ", STR_PAD_RIGHT); // Tiene promoción S/N/M (1 caracter)
      $fields['PEDLTARI']  = str_pad('', 10, " ", STR_PAD_RIGHT); // Tarifa aplicada (10 caracteres)
      $fields['PEDLNUML']  = str_pad($line_index + 1, 3, "0", STR_PAD_LEFT); // Numero de linea (3 caracteres)
      $fields['TARUNIDAD'] = str_pad($product ? $product->get_attribute('tarunidad') : '', 10, " ", STR_PAD_RIGHT); // Unidad medida tarifa (10 caracteres)
      $fields['TARPVER']   = str_pad('', 9, " ", STR_PAD_RIGHT); // Importe punto verde * 10000 (9 caracteres)
      $fields['PEDLEQUI']  = str_pad('', 10, " ", STR_PAD_RIGHT); // Unidad de equivalencia * 1000000 (10 caracteres)
      $fields['PEDLVOLU']  = str_pad('', 8, " ", STR_PAD_RIGHT); // Volumen * 100 (8 caracteres)
      $fields['PEDLTVAL']  = str_pad('', 20, " ", STR_PAD_RIGHT); // Tipo de vale (20 caracteres)
      $fields['PEDLCVAL']  = str_pad('', 20, " ", STR_PAD_RIGHT); // Clase de vale (20 caracteres)
      $fields['PEDLNUMV']  = str_pad('', 10, " ", STR_PAD_RIGHT); // Número de vale (10 caracteres)
      $fields['PEDLMARC']  = str_pad('', 10, " ", STR_PAD_RIGHT); // Marca del vale (10 caracteres)
      $fields['PEDLMPRO']  = str_pad('', 1, " ", STR_PAD_RIGHT); // Modificar promoción S/N (1 caracter)
      $fields['PEDLTIPT']  = str_pad('', 7, " ", STR_PAD_RIGHT); // Tipo tarifa <CAMPA> campaña / <CLIENTE> / <TARIFA> / <TODOS> (7 caracteres)
      $fields['PEDLNUMP']  = str_pad('', 20, " ", STR_PAD_RIGHT); // Número de promoción (20 caracteres)
      $fields['DtoWeb']    = str_pad($discount, 8, "0", STR_PAD_LEFT); // Descuento web (8 caracteres)

      // Concatenar los valores para generar la línea final
      $line = implode('', $fields);

      return $line;
  }



  public function formatCabecera($order) {
      $user_id = $order->get_user_id();
      $clicodi = get_field('clicodi', 'user_' . $user_id);
      $clicodi = !empty($clicodi) ? $clicodi : '';
      $order_date = $order->get_date_created();

      // Inicializamos los campos con sus valores
      $fields = [];
      $fields['PEDVISI'] = str_pad($order->get_id(), 4, " ", STR_PAD_RIGHT); // Número de orden visita (4)
      $fields['PEDCLIE'] = str_pad($clicodi, 20, " ", STR_PAD_RIGHT); // Código de Cliente (20)
      $fields['PEDDOMI'] = str_pad(0, 20, " ", STR_PAD_RIGHT); // Código de Domicilio de entrega (20)
      $fields['PEDPROD'] = str_pad('P', 1, " ", STR_PAD_RIGHT); // <P> Pedido (1)
      $fields['PEDFECH'] = str_pad($order_date->date('dmY'), 6, " ", STR_PAD_RIGHT); // Fecha de pedido DDMMYY (6)
      $fields['PEDHORA'] = str_pad($order_date->date('Hi'), 4, " ", STR_PAD_RIGHT); // Hora de pedido HHMM (4)
      $fields['PEDFSER'] = str_pad('', 6, " ", STR_PAD_RIGHT); // Fecha de Servicio DDMMYY (6)
      $fields['PEDHSEI'] = str_pad('', 4, " ", STR_PAD_RIGHT); // Hora de Servicio Inicial HHMM (4)
      $fields['PEDHSEF'] = str_pad('', 4, " ", STR_PAD_RIGHT); // Hora de Servicio Final HHMM (4)
      $fields['PEDCOBS'] = str_pad('PEDIDO WEB', 20, " ", STR_PAD_RIGHT); // Código de Observaciones (20)
      $fields['PEDCOBS1'] = str_pad('PEDIDO WEB', 20, " ", STR_PAD_RIGHT); // Observaciones 1 (20)
      $fields['PEDCOBS2'] = str_pad('PEDIDO WEB', 60, " ", STR_PAD_RIGHT); // Observaciones 2 (60)
      $fields['PEDSUBT'] = str_pad(number_format($order->get_subtotal(), 2, '', ''), 8, "0", STR_PAD_LEFT); // Subtotal (8)
      $fields['PEDPDT1'] = str_pad(0, 4, "0", STR_PAD_LEFT); // % Dto1 * 100 (4)
      $fields['PEDPDT2'] = str_pad(0, 4, "0", STR_PAD_LEFT); // % Dto2 * 100 (4)
      $fields['PEDPDT3'] = str_pad(0, 4, "0", STR_PAD_LEFT); // % Dto3 * 100 (4)
      $fields['PEDIDTO'] = str_pad(number_format($order->get_total_discount(), 2, '', ''), 8, "0", STR_PAD_LEFT); // Importe Descuentos (8)
      $fields['PEDBASE'] = str_pad(number_format($order->get_subtotal() - $order->get_total_discount(), 2, '', ''), 8, "0", STR_PAD_LEFT); // Base de IVA = PEDSUBT - PEDIDTO (8)
      $fields['PEDIIVA'] = str_pad(number_format($order->get_total_tax(), 2, '', ''), 8, "0", STR_PAD_LEFT); // Importe de IVA (8)
      $fields['PEDIREC'] = str_pad(0, 8, "0", STR_PAD_LEFT); // Importe de RECARGO Equivalencia (8)
      $fields['PEDTOTA'] = str_pad(number_format($order->get_total(), 2, '', ''), 8, "0", STR_PAD_LEFT); // TOTAL = PEDBASE + PEDIIVA + PEDIREC (8)
      $fields['PEDTIPO'] = str_pad(0, 1, "0", STR_PAD_LEFT); // <0> Pedido normal <1> Pedido B (1)
      $fields['PEDCOBR'] = str_pad(0, 8, "0", STR_PAD_LEFT); // Importe cobrado * 100 (8)
      $fields['PEDCTCOB'] = str_pad('', 1, " ", STR_PAD_RIGHT); // Tipo de cobro <1> efectivo / <2> Talón (1)
      $fields['PEDSERI'] = str_pad('', 2, " ", STR_PAD_RIGHT); // Serie del documento para autoventa (2)
      $fields['PEDNUME'] = str_pad('', 8, " ", STR_PAD_RIGHT); // Número del documento para autoventa (8)
      $fields['PEDHOR2'] = str_pad('', 4, " ", STR_PAD_RIGHT); // Hora de fin de grabación de pedido (4)

      return implode("", $fields);
  }


  public function saveCabecera($order) {
      $cabecera = $this->formatCabecera($order);
      $path = get_dimporter_option('orderheader_path');
      $file_name = 'WEBPEDC' . $order->get_id() . '.DAT';

      // Crear el directorio si no existe
      if (!file_exists($path)) {  mkdir($path, 0755, true);  }

      $file_path = $path . $file_name;
      $result = file_put_contents($file_path, $cabecera);
      if ($result === false) { throw new \RuntimeException(sprintf('Error al escribir en el archivo "%s"', $file_path)); }
      return $file_path;
  }

  public function saveLineas($order) {
      $items = $order->get_items();
      $path = get_dimporter_option('orderline_path');
      $file_name = 'WEBPEDL' . $order->get_id() . '.DAT';
      
      // Si el directorio no existe, crearlo
      if (!file_exists($path)) {  mkdir($path, 0755, true);  }

      $file_path = $path . $file_name;
      $lines = '';
      foreach ($items as $line_index => $line_item) {
          $formatted_line = $this->formatLine($order, $line_item, $line_index);
          $lines .= $formatted_line . "\n";
      }
      file_put_contents($file_path, $lines);
      return $file_path;
  }


  

}//class

?>