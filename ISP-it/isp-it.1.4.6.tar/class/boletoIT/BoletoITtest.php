<?
require_once( "BoletoIT.php");

$boleto = new BoletoIT(  "Bertoldo  Aparecido.", "426000122", "Valentin Gentil 157,", "Ourinhos SP- SAO PAULO", "20,00", "25/03/2006", array( "instrucao1", "instrucao2") );

print $boleto->getHtml();

?>