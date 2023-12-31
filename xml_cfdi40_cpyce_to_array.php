<?php 

/*
	XML_CFDI40_CPYCE_TO_ARRAY_PHP
	Autor: Angel Ramirez, PHP Soluciones SA de CV
	Créditos: goedecke / readcfdi.php (https://gist.github.com/goedecke/03e9c7c178ff947b1e9d9eaea4bbe369)
*/

libxml_use_internal_errors(true);
$xml= new \SimpleXMLElement('cfdi.ml', null, true);

$cfdi = [];

foreach ($xml->xpath('//cfdi:Comprobante') as $Comprobante){

  $ComprobanteAttributes = (array)$Comprobante->attributes();
	$ComprobanteAttributes = $ComprobanteAttributes['@attributes'];
  $cfdi['Comprobante'] = $ComprobanteAttributes;

}

foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Emisor') as $Emisor){
  $Emisor = (array)$Emisor->attributes();
	$Emisor = $Emisor['@attributes'];
  $cfdi['Comprobante']['Emisor'] = $Emisor;
}

foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Receptor') as $Receptor){
  $Receptor = (array)$Receptor->attributes();
	$Receptor = $Receptor['@attributes'];
  $cfdi['Comprobante']['Receptor'] = $Receptor;
}


foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Conceptos//cfdi:Concepto') as $Concepto){

  $_concepto = (json_decode(json_encode($Concepto), true))['@attributes'];

  $_concepto['Impuestos']['Traslados'] = [];

  $_Concepto = $Concepto->children('cfdi', true);
	$Impuestos = $_Concepto->Impuestos->children('cfdi', true);
  $Traslados = $Impuestos->Traslados->children('cfdi', true);

  foreach ($Traslados as $key => $values) {
    $values = (array)$values->attributes();
  	$values = $values['@attributes'];
    $_concepto['Impuestos']['Traslados'][] = [$key => $values];
  }

  $cfdi['Comprobante']['Conceptos'][]=['Concepto' => $_concepto];
}


foreach ($xml->xpath('//cfdi:Comprobante') as $Comprobante){

  $_Comprobante = $Comprobante->children('cfdi', true);

  $ImpuestosAttributes = (array)$_Comprobante->Impuestos->attributes();
  $ImpuestosAttributes = $ImpuestosAttributes['@attributes'];
  $cfdi['Comprobante']['Impuestos'] = $ImpuestosAttributes;

  $Impuestos = $_Comprobante->Impuestos->children('cfdi', true);
  $Traslados = $Impuestos->Traslados->children('cfdi', true);

  foreach ($Traslados as $key => $values) {
    $values = (array)$values->attributes();
    $values = $values['@attributes'];
    $cfdi['Comprobante']['Impuestos']['Traslados'][] = [$key => $values];
  }
}


foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Complemento//cartaporte20:CartaPorte') as $CartaPorte){
  $CartaPorteAttributes = (array)$CartaPorte->attributes();
  $CartaPorteAttributes = $CartaPorteAttributes['@attributes'];
  $cfdi['Comprobante']['Complemento']['CartaPorte'] = $CartaPorteAttributes;

  $_CartaPorte = $CartaPorte->children('cartaporte20', true);

  $Ubicaciones = $_CartaPorte->Ubicaciones->children('cartaporte20', true);

  foreach ($Ubicaciones as $key => $values) {

    $Domicilio = $values->children('cartaporte20', true);

    $Domicilio = (array)$Domicilio->attributes();
    $Domicilio = $Domicilio['@attributes'];

    $values = (array)$values->attributes();
    $values = $values['@attributes'];

    $values['Domicilio'] = $Domicilio;
    $cfdi['Comprobante']['Complemento']['CartaPorte']['Ubicaciones'][]=[$key => $values];

  }

  $Mercancias = $_CartaPorte->Mercancias->children('cartaporte20', true);
  foreach ($Mercancias as $key => $values) {
    $nodo = $values->children('cartaporte20', true);
    if($key == "Mercancia"):
      $CantidadTransporta = (array)$nodo->attributes();
      $CantidadTransporta = $CantidadTransporta['@attributes'];
      $values = (array)$values->attributes();
      $values = $values['@attributes'];
      $values['CantidadTransporta'] = $CantidadTransporta;
    elseif($key == "Autotransporte"):
      $IdentificacionVehicular = (array)$nodo->IdentificacionVehicular->attributes();
      $IdentificacionVehicular = $IdentificacionVehicular['@attributes'];
      $Seguros = (array)$nodo->Seguros->attributes();
      $Seguros = $Seguros['@attributes'];
      $values = (array)$values->attributes();
      $values = $values['@attributes'];
      $values['IdentificacionVehicular']=$IdentificacionVehicular;
      $values['Seguros']=$Seguros;
    endif;
    $cfdi['Comprobante']['Complemento']['CartaPorte']['Mercancias'][]=[$key => $values];
  }

  $FiguraTransporte = $_CartaPorte->FiguraTransporte->children('cartaporte20', true);
  foreach ($FiguraTransporte as $key => $values) {
    $values = (array)$values->attributes();
    $values = $values['@attributes'];
    $cfdi['Comprobante']['Complemento']['CartaPorte']['FiguraTransporte'][]=[$key => $values];
  }

}

foreach ($xml->xpath('//cfdi:Comprobante//cfdi:Complemento//cce11:ComercioExterior') as $ComercioExterior){

  $ComercioExteriorAttributes = (array)$ComercioExterior->attributes();
  $ComercioExteriorAttributes = $ComercioExteriorAttributes['@attributes'];
  $cfdi['Comprobante']['Complemento']['ComercioExterior'] = $ComercioExteriorAttributes;


  $_ComercioExterior = $ComercioExterior->children('cce11', true);

  $Emisor = $_ComercioExterior->Emisor->children('cce11', true);

  $DomicilioEmisor = (array)$Emisor->Domicilio->attributes();
  $DomicilioEmisor = $DomicilioEmisor['@attributes'];

  $cfdi['Comprobante']['Complemento']['ComercioExterior']['Emisor']['Domicilio']=$DomicilioEmisor;

  $Receptor = (array)$_ComercioExterior->Receptor->attributes();
  $Receptor = $Receptor['@attributes'];
  $cfdi['Comprobante']['Complemento']['ComercioExterior']['Receptor'] = $Receptor;

  $DomicilioReceptor = $_ComercioExterior->Receptor->children('cce11', true);
  $DomicilioReceptor = (array)$DomicilioReceptor->attributes();
  $DomicilioReceptor = $DomicilioReceptor['@attributes'];
  $cfdi['Comprobante']['Complemento']['ComercioExterior']['Receptor']['Domicilio'] = $DomicilioReceptor;

  $Mercancias = $_ComercioExterior->Mercancias->children('cce11', true);
  foreach ($Mercancias as $key => $values) {
    $values = (array)$values->attributes();
    $values = $values['@attributes'];
    $cfdi['Comprobante']['Complemento']['ComercioExterior']['Mercancias'][]=[$key => $values];
  }

}
echo "<pre>";
print_r($cfdi);
exit;


?>