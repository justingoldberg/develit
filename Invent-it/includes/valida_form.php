<?php
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 26/02/2003
# Ultima alteração: 02/10/2003
#    Alteração No.: 009
#
# Função:
#    Configurações utilizadas pela aplicação - Validação de Formulários



# Função para validação de dados de formulários
function validaForm($matriz, $sub) {

	global $configDominio; 
	
	# Validação de documentos
	if($sub=='documentos') {
		# Verificar se é pessoa física ou jurídica
		if($matriz[tipoPessoa]=='F') {
		
			### VALIDAÇÃO DE PESSOA FÍSICA
			# validar CPF
			return(validaCPF($matriz[cpf]));
		}
		elseif($matriz[tipo_pessoa]=='J') {
			# Validar CNPJ
		}
	} # fecha validação de documentos
	
	
	# Validação de Endereço
	if($sub=='endereco') {
		# Veriricar parametros
		if(!$matriz[endereco] || !$matriz[bairro] || !$matriz[cidade] || !$matriz[uf] || !$matriz[cep] 
			|| !validaCEP($matriz[cep])) return(1);
		else return(0);
	}

	# Validação de Endereço de cobrança
	if($sub=='endereco_cob') {
		# Veriricar parametros
		if(!$matriz[endereco_cob] || !$matriz[bairro_cob] || !$matriz[cidade_cob] || !$matriz[uf_cob] || !$matriz[cep_cob] 
			|| !validaCEP($matriz[cep_cob])) return(1);
		else return(0);
	}


	# Validação de Informaçoes para Contato
	if($sub=='contato') {
		# Veriricar parametros
		if(!$matriz[contato] || !$matriz[ddd] || !$matriz[fone]
			|| !validaDDD($matriz[ddd]) || !validaFONE($matriz[fone])) return(1);
		else return(0);
	}
	
	# Validação de Informaçoes para Contato
	if($sub=='como_conheceu') {
		# Veriricar parametros
		if($matriz[como_conheceu]=='outros' && !$matriz[como_conheceu_txt]) return(1);
		else return(0);
	}


	# Validação de Informaçoes para Contato
	if($sub=='verifica_conta') {
		# Veriricar parametros
		if(!$matriz[conta] || !$matriz[senha] || !$matriz[confirma_senha]
			|| !comparaSenha($matriz[senha], $matriz[confirma_senha])) return(1);
		elseif(!mailValidaConta($matriz[conta])
			|| mailBuscaConta(mailValidaConta($matriz[conta]), $configDominio) 
			|| radiusBuscaConta(mailValidaConta($matriz[conta]))) return(1);
		else return(0);
	}


} # fecha funcao de validação de formulário



# Validação de CEP
function validaCEP($cep) {

	$cep=cpfVerificaFormato($cep);
	
	if(!is_numeric($cep) || strlen($cep)!=8) return(0);
	else return(1);
	
} #fecha validação de CEP



# Validação de DDD
function validaDDD($ddd) {

	$cep=cpfVerificaFormato($ddd);
	
	if(!is_numeric($ddd) || strlen($ddd)!=2) return(0);
	else return(1);
	
} #fecha validação de DDD




# Validação de FONE
function validaFONE($fone) {

	$fone=cpfVerificaFormato($fone);	
	
	if(!is_numeric($fone) || strlen($fone)<6) return(0);
	else return(1);
	
} #fecha validação de FONE



# Verificação de senha
function comparaSenha($senha, $confirmacao) {
	if(!$senha || !$confirmacao || ($senha != $confirmacao) ) return(0);
	else return(1);
}


# Função para conversão de valores de formulário
function formatarString($texto, $tipo){

	# Converter acentuação para maiúscula
	$matMinuscula=array('ç'
	,'â','ã','à','á','ä'
	,'é','è','ê','ë'
	,'í','ì','î','ï'
	,'ó','ò','ô','õ','ö'
	,'ú','ù','û','ü');
	
	$matMaiuscula=array('Ç'
	,'Â','Ã','Á','À','Ä'
	,'É','È','Ê','Ë'
	,'Í','Ì','Î','Ï'
	,'Ó','Ò','Õ','Ô','Ö'
	,'Ú','Ù','Û','Ü');


	if($tipo=='minuscula') {
		# Converter para maiúscula
		$texto=strtolower($texto);
		for($i=0;$i<count($matMinuscula);$i++) {
			$texto=str_replace($matMinuscula[$i], $matMaiuscula[$i], $texto);
		}
	} 
	elseif($tipo=='maiuscula') {
		# Converter para maiúscula
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
