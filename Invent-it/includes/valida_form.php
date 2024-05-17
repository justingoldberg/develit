<?php
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 26/02/2003
# Ultima altera��o: 02/10/2003
#    Altera��o No.: 009
#
# Fun��o:
#    Configura��es utilizadas pela aplica��o - Valida��o de Formul�rios



# Fun��o para valida��o de dados de formul�rios
function validaForm($matriz, $sub) {

	global $configDominio; 
	
	# Valida��o de documentos
	if($sub=='documentos') {
		# Verificar se � pessoa f�sica ou jur�dica
		if($matriz[tipoPessoa]=='F') {
		
			### VALIDA��O DE PESSOA F�SICA
			# validar CPF
			return(validaCPF($matriz[cpf]));
		}
		elseif($matriz[tipo_pessoa]=='J') {
			# Validar CNPJ
		}
	} # fecha valida��o de documentos
	
	
	# Valida��o de Endere�o
	if($sub=='endereco') {
		# Veriricar parametros
		if(!$matriz[endereco] || !$matriz[bairro] || !$matriz[cidade] || !$matriz[uf] || !$matriz[cep] 
			|| !validaCEP($matriz[cep])) return(1);
		else return(0);
	}

	# Valida��o de Endere�o de cobran�a
	if($sub=='endereco_cob') {
		# Veriricar parametros
		if(!$matriz[endereco_cob] || !$matriz[bairro_cob] || !$matriz[cidade_cob] || !$matriz[uf_cob] || !$matriz[cep_cob] 
			|| !validaCEP($matriz[cep_cob])) return(1);
		else return(0);
	}


	# Valida��o de Informa�oes para Contato
	if($sub=='contato') {
		# Veriricar parametros
		if(!$matriz[contato] || !$matriz[ddd] || !$matriz[fone]
			|| !validaDDD($matriz[ddd]) || !validaFONE($matriz[fone])) return(1);
		else return(0);
	}
	
	# Valida��o de Informa�oes para Contato
	if($sub=='como_conheceu') {
		# Veriricar parametros
		if($matriz[como_conheceu]=='outros' && !$matriz[como_conheceu_txt]) return(1);
		else return(0);
	}


	# Valida��o de Informa�oes para Contato
	if($sub=='verifica_conta') {
		# Veriricar parametros
		if(!$matriz[conta] || !$matriz[senha] || !$matriz[confirma_senha]
			|| !comparaSenha($matriz[senha], $matriz[confirma_senha])) return(1);
		elseif(!mailValidaConta($matriz[conta])
			|| mailBuscaConta(mailValidaConta($matriz[conta]), $configDominio) 
			|| radiusBuscaConta(mailValidaConta($matriz[conta]))) return(1);
		else return(0);
	}


} # fecha funcao de valida��o de formul�rio



# Valida��o de CEP
function validaCEP($cep) {

	$cep=cpfVerificaFormato($cep);
	
	if(!is_numeric($cep) || strlen($cep)!=8) return(0);
	else return(1);
	
} #fecha valida��o de CEP



# Valida��o de DDD
function validaDDD($ddd) {

	$cep=cpfVerificaFormato($ddd);
	
	if(!is_numeric($ddd) || strlen($ddd)!=2) return(0);
	else return(1);
	
} #fecha valida��o de DDD




# Valida��o de FONE
function validaFONE($fone) {

	$fone=cpfVerificaFormato($fone);	
	
	if(!is_numeric($fone) || strlen($fone)<6) return(0);
	else return(1);
	
} #fecha valida��o de FONE



# Verifica��o de senha
function comparaSenha($senha, $confirmacao) {
	if(!$senha || !$confirmacao || ($senha != $confirmacao) ) return(0);
	else return(1);
}


# Fun��o para convers�o de valores de formul�rio
function formatarString($texto, $tipo){

	# Converter acentua��o para mai�scula
	$matMinuscula=array('�'
	,'�','�','�','�','�'
	,'�','�','�','�'
	,'�','�','�','�'
	,'�','�','�','�','�'
	,'�','�','�','�');
	
	$matMaiuscula=array('�'
	,'�','�','�','�','�'
	,'�','�','�','�'
	,'�','�','�','�'
	,'�','�','�','�','�'
	,'�','�','�','�');


	if($tipo=='minuscula') {
		# Converter para mai�scula
		$texto=strtolower($texto);
		for($i=0;$i<count($matMinuscula);$i++) {
			$texto=str_replace($matMinuscula[$i], $matMaiuscula[$i], $texto);
		}
	} 
	elseif($tipo=='maiuscula') {
		# Converter para mai�scula
		$texto=strtoupper($texto);
		for($i=0;$i<count($matMinuscula);$i++) {
			$texto=str_replace($matMinuscula[$i], $matMaiuscula[$i], $texto);
		}
	}
	
	return($texto);
}



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



function formatarData($data) {
	# Converter valores antes de gravar
	$data=str_replace('/','',$data);
	$data=str_replace('-','',$data);
	
	return($data);
}

function formatarValoresForm($valor) {
	# Converter valores antes de gravar
	
	return(number_format($valor,2,',','.'));
}


?>
