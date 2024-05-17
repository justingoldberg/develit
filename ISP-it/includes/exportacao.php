<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 11/04/2003
# Ultima alteração: 11/04/2003
#    Alteração No.: 001
#
# Função:
#    Funções para exportação de dados


# Função para geração de caracteres e exportação de arquivos
/**
 * @return unknown
 * @param unknown $texto
 * @param unknown $tipo
 * @param unknown $alinhamento
 * @param unknown $limite
 * @param unknown $complemento
 * @desc Rotina pega os dados e formata conforme seu alinhamento, fazendo o preenchimento
 *       com o dados informado no parametro $complemento
*/
function exportaDados($texto, $tipo, $alinhamento, $limite, $complemento) {

	# Dados do valor recebido
	$tamanho=strlen($texto);
	
	# Formatar saída - alinhamento
	if($alinhamento && $alinhamento=='right') {
		for($i=strlen($texto);$i<$limite;$i++) {
			$retorno.=$complemento;
		}
		$retorno.=substr($texto,0,$limite);
	}
	elseif($alinhamento && $alinhamento=='left') {
		$retorno.=substr($texto,0,$limite);
		for($i=strlen($texto);$i<$limite;$i++) {
			$retorno.=$complemento;
		}
	}
	
	return($retorno);
}

function exportaDadosMensagem ($mensagem, $linha, $codFlash, $ultimaMsg = false){

	global $cont, $sequenciaArquivo, $formaCobranca;
	
	if ($mensagem != "") {
	
		if ($cont==0){
	
			# TIPO DO REGISTRO
			$conteudoArquivo.=exportaDados(7, '', 'right', 1, 0);
			# CODIGO FLASH
			$conteudoArquivo.=exportaDados($codFlash, '', 'right', 3, ' ');
	
		}
	
		# NUMERO DA LINHA A SER IMPRESSA
		$conteudoArquivo.=exportaDados($linha, '', 'right', 2, 0);
		# ESPACOS EM BRANCOS
		$conteudoArquivo.=exportaDados('', '', 'left', 4, ' ');
		# CONTEUDO DA LINHA
		$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($mensagem,'maiuscula'),'' ,'left', 96,' ');
		# ESPACOS EM BRANCOS
		$conteudoArquivo.=exportaDados('', '', 'left', 28, ' ');
		$cont++;
	
		if ($cont==3){
			# NUMERO SEQUENCIAL DO REGISTRO NO ARQUIVO
			$conteudoArquivo.=exportaDados($sequenciaArquivo++, '', 'right', 6, 0);
			# QUEBRA DE LINHA
			$conteudoArquivo.="\r\n";
			$cont=0;
		}

	}
	
	if ($ultimaMsg) {
		switch ($cont) {
			case "1":
			# ESPACOS EM BRANCOS
			$conteudoArquivo.=exportaDados('','','left',260,' ');
			# NUMERO SEQUENCIAL DO REGISTRO NO ARQUIVO
			$conteudoArquivo.=exportaDados($sequenciaArquivo++, '', 'right', 6, 0);
			# QUEBRA DE LINHA
			$conteudoArquivo.="\r\n";
			$cont=0;
			break;
			case "2":
			# ESPACOS EM BRANCOS
			$conteudoArquivo.=exportaDados('','','left',130,' ');
			# NUMERO SEQUENCIAL DO REGISTRO NO ARQUIVO
			$conteudoArquivo.=exportaDados($sequenciaArquivo++, '', 'right', 6, 0);
			# QUEBRA DE LINHA
			$conteudoArquivo.="\r\n";
			$cont=0;
			break;
		}
	}

	return $conteudoArquivo;

}

function explodeDadosMensagem ($mensagem) {
	
	$array = explode("\r\n",$mensagem);

	foreach ($array as $str) 
		for ($i = 0; $i <= strlen($str) ; $i += 96)
			$msn[] = substr($str, $i, $i+96);
		
	return $msn;
}

function contaLinhasMensagem ($mensagem) {
	
	$ret = false;
	$linhas = 0;
	
	$array = explodeDadosMensagem($mensagem);
	foreach ($array as $i)
		if ($i != "") $linhas++;
	
	if (count($array) <= 75 && $linhas <= 33)
		return true;
	else
		return false;
}



?>
