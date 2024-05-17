<?php
################################################################################
#       Criado por: Josй Roberto Kerne - joseroberto@kerne.org
#  Data de criaзгo: 26/02/2003
# Ultima alteraзгo: 10/02/2004
#    Alteraзгo No.: 011
#
# Funзгo:
#    Configuraзхes utilizadas pela aplicaзгo - Validaзгo de Formulбrios



# Funзгo para validaзгo de dados de formulбrios
function validaForm($matriz, $sub) {

	global $configDominio; 
	
	# Validaзгo de documentos
	if($sub=='documentos') {
		# Verificar se й pessoa fнsica ou jurнdica
		if($matriz[tipoPessoa]=='F') {
		
			### VALIDAЗГO DE PESSOA FНSICA
			# validar CPF
			return(validaCPF($matriz[cpf]));
		}
		elseif($matriz[tipo_pessoa]=='J') {
			# Validar CNPJ
		}
	} # fecha validaзгo de documentos
	
	
	# Validaзгo de Endereзo
	if($sub=='endereco') {
		# Veriricar parametros
		if(!$matriz[endereco] || !$matriz[bairro] || !$matriz[cidade] || !$matriz[uf] || !$matriz[cep] 
			|| !validaCEP($matriz[cep])) return(1);
		else return(0);
	}

	# Validaзгo de Endereзo de cobranзa
	if($sub=='endereco_cob') {
		# Veriricar parametros
		if(!$matriz[endereco_cob] || !$matriz[bairro_cob] || !$matriz[cidade_cob] || !$matriz[uf_cob] || !$matriz[cep_cob] 
			|| !validaCEP($matriz[cep_cob])) return(1);
		else return(0);
	}


	# Validaзгo de Informaзoes para Contato
	if($sub=='contato') {
		# Veriricar parametros
		if(!$matriz[contato] || !$matriz[ddd] || !$matriz[fone]
			|| !validaDDD($matriz[ddd]) || !validaFONE($matriz[fone])) return(1);
		else return(0);
	}
	
	# Validaзгo de Informaзoes para Contato
	if($sub=='como_conheceu') {
		# Veriricar parametros
		if($matriz[como_conheceu]=='outros' && !$matriz[como_conheceu_txt]) return(1);
		else return(0);
	}


	# Validaзгo de Informaзoes para Contato
	if($sub=='verifica_conta') {
		# Veriricar parametros
		if(!$matriz[conta] || !$matriz[senha] || !$matriz[confirma_senha]
			|| !comparaSenha($matriz[senha], $matriz[confirma_senha])) return(1);
		elseif(!mailValidaConta($matriz[conta])
			|| mailBuscaConta(mailValidaConta($matriz[conta]), $configDominio) 
			|| radiusBuscaConta(mailValidaConta($matriz[conta]))) return(1);
		else return(0);
	}


} # fecha funcao de validaзгo de formulбrio



# Validaзгo de CEP
function validaCEP($cep) {

	$cep=cpfVerificaFormato($cep);
	
	if(!is_numeric($cep) || strlen($cep)!=8) return(0);
	else return(1);
	
} #fecha validaзгo de CEP



# Validaзгo de DDD
function validaDDD($ddd) {

	$cep=cpfVerificaFormato($ddd);
	
	if(!is_numeric($ddd) || strlen($ddd)!=2) return(0);
	else return(1);
	
} #fecha validaзгo de DDD




# Validaзгo de FONE
function validaFONE($fone) {

	$fone=cpfVerificaFormato($fone);	
	
	if(!is_numeric($fone) || strlen($fone)<6) return(0);
	else return(1);
	
} #fecha validaзгo de FONE



# Verificaзгo de senha
function comparaSenha($senha, $confirmacao) {
	if(!$senha || !$confirmacao || ($senha != $confirmacao) ) return(0);
	else return(1);
}


# Funзгo para conversгo de valores de formulбrio
function formatarString($texto, $tipo){

	# Converter acentuaзгo para maiъscula
	$matMinuscula=array('з'
	,'в','г','а','б','д'
	,'й','и','к','л'
	,'н','м','о','п'
	,'у','т','ф','х','ц'
	,'ъ','щ','ы','ь');
	
	$matMaiuscula=array('З'
	,'В','Г','Б','А','Д'
	,'Й','И','К','Л'
	,'Н','М','О','П'
	,'У','Т','Х','Ф','Ц'
	,'Ъ','Щ','Ы','Ь');


	if($tipo=='minuscula') {
		# Converter para maiъscula
		$texto=strtolower($texto);
		for($i=0;$i<count($matMinuscula);$i++) {
			$texto=str_replace($matMinuscula[$i], $matMaiuscula[$i], $texto);
		}
	} 
	elseif($tipo=='maiuscula') {
		# Converter para maiъscula
		$texto=strtoupper($texto);
		for($i=0;$i<count($matMinuscula);$i++) {
			$texto=str_replace($matMinuscula[$i], $matMaiuscula[$i], $texto);
		}
	}
	
	return($texto);
}



/**
 * @return unknown
 * @param String $valor
 * @desc Formatar valores para padrгo americano
 Exemplo: 1,000.00
*/
function formatarValores($valor) {
	# Converter valores antes de gravar
	$valor=str_replace('.','',$valor);
	
	if(strstr($valor,',')) $valor=str_replace(',','.',$valor);
	else {
		$valor=str_replace(',','',$valor);
		$valor=$valor/100;
	}
	
	return($valor);
}


function formatarValoresArquivoRemessa($valor) {
	# Converter valores antes de gravar
	$valor=str_replace('.','',$valor);
	$valor=str_replace(',','',$valor);
	
	return($valor);
}


function formatarValoresArquivoRetorno($valor) {


	$valor=($valor/100);

	return(formatarValoresForm($valor));
}



/**
 * @return unknown
 * @param unknown $data
 * @desc Remover formatos de data e separadores, retornando apenas numeros da data
*/
function formatarData($data) {
	# Converter valores antes de gravar
	$data=str_replace('/','',$data);
	$data=str_replace('-','',$data);
	
	return($data);
}

/**
 * @return unknown
 * @param unknown $valor
 * @desc Formatar valores para moeda (Brasil
 Exemplo: 1.000,00
*/
function formatarValoresForm($valor) {
	# Converter valores antes de gravar
	
	return(number_format($valor,2,',','.'));
}

# Funзгo para formataзгo de telefone
function formatarBytes($bytes) {

	
	return(number_format(($bytes/1024),2,',','.'));
}


# Funзгo para remover formatos de telefone
/**
 * @return unknown
 * @param unknown $fone
 * @desc Remover formataзгo de telefone retornando apenas os numeros
*/
function formatarFoneNumeros($fone) {

	$fone=str_replace(".","",$fone);
	$fone=str_replace(",","",$fone);
	$fone=str_replace("/","",$fone);
	$fone=str_replace("\\","",$fone);
	$fone=str_replace("=","",$fone);
	$fone=str_replace("-","",$fone);
	$fone=str_replace("(","",$fone);
	$fone=str_replace(")","",$fone);
	$fone=str_replace("*","",$fone);
	$fone=str_replace("+","",$fone);
	$fone=str_replace(" ","",$fone);

	if(is_numeric($fone)) return($fone);
	else return("");
}


# Funзгo para formataзгo de telefone
/**
 * @return unknown
 * @param unknown $fone
 * @desc Formatar Numero de Telefone para (99) 9999-9999 ou (99) 999-9999
*/
function formatarFone($fone) {

	# formatar telefone, verificar o tamanho do numero
	$fone=formatarFoneNumeros($fone);
	
	if(strlen($fone) <= 8) {
		# Numero sem DDD
		$retorno=substr($fone, 0, strlen($fone)%4);
		$retorno.="-".substr($fone, strlen($fone)-4, 4);
	}
	else {
		# Numero com DDD
		
		# Verificar o DDD
		$retorno=substr($fone, 0, strlen($fone)-4);
		
		if((strlen($retorno)-2)%4 == 0) {
			# DDD + 4 digitos
			$ddd="(".substr($retorno, 0, 2).")&nbsp;";
			$prefixo=substr($retorno, 2, 4);
		}
		elseif((strlen($retorno)-2)%4 != 0) {
			# DDD + 3 digitos
			$ddd="(".substr($retorno, 0, 2).")&nbsp;";
			$prefixo=substr($retorno, 2, 3);
		}
		
		# Verificar o numero do fone
		$retorno=$ddd.$prefixo;
		$retorno.="-".substr($fone, strlen($fone)-4, 4);
	}
	
	return($retorno);
}

/**
 * Verifica se nгo existe registro duplicado em $consulta. Deve ser sempre a primeira 
 * verificaзгo a ser feita, para nгo sobrescrever uma outra irregularidade encontrada
 * anteriormente
 *
 * $consulta = array de objetos com a consulta
 * $acao = utiliza a aзгo para saber se aзгo for alterar ele deverб ter 
 * apenas uma ocorrencia, senгo nгo poderб encontrar uma ocorrкncia
 * 
 * @return boolean
 * @param array  $consulta
 * @param string $acao
 */
function verificaRegistroDuplicado( $consulta, $acao ) {
	$retorno = true;
	if( is_array( $consulta ) ) {
		if( substr( $acao, 0, 7) == 'alterar' ) {
			if( count( $consulta ) > 0 ) {
				$retorno = false;
			}	
		}
		else {
			if(count( $consulta ) > 0 ) {
				$retorno = false;
			}
		}
	}
	return $retorno;
}

/**
 * Valida uma data no formato DD/MM/AAAA
 *
 * @param string $data
 * @return boolean
 */
function validaData( $data ) {
	
	if( $data && preg_match( '/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{1,4}/', $data ) ) {
		$dt = explode( '/', $data );
		$retorno = checkdate( $dt[1], $dt[0], $dt[2] );
	}
	else {
		$retorno = false;
	}
	return $retorno;
}

function formataValor( $valor ) {
	
	//$valor = str_replace(",",".",$valor);
	$valor = ( $valor * 100 );

	return $valor;
}
?>