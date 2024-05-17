<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 03/02/2004
# Ultima altera��o: 03/02/2004
#    Altera��o No.: 001
#
# Fun��o:
# 		Fun��es de Template


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
