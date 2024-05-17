<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 03/02/2004
# Ultima alteração: 03/02/2004
#    Alteração No.: 001
#
# Função:
# 		Funções de Template


function k_templateLoad($tplFile) {

	global $template;
	$arquivo=k_fileOpen($template[dir] . $tplFile . ".tpl", 'r');
	$retorno=k_fileRead($arquivo);

	return($retorno);

}



function k_templateParse($tpl, $data) {

	$keys=array_keys($data);
	
	for($a=0;$a<count($keys);$a++) {
	
		$tmpData=$data[$keys[$a]];
		$tpl=str_replace("%_".$keys[$a]."_%",$tmpData,$tpl);
	}
	
	return($tpl);
	
}

?>
