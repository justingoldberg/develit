<?php
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 23/06/2003
# Ultima altera��o: 23/06/2003
#    Altera��o No.: 001
#
# Fun��o:
#    Configura��es utilizadas pela aplica��o - Valida��o de Documentos




# Verifica formato informado para CPF
function cpfVerificaFormato($cpf) {

		$cpf=str_replace(".","",$cpf);
		$cpf=str_replace("-","",$cpf);
		$cpf=str_replace("/","",$cpf);
		$cpf=str_replace("\\","",$cpf);
		
		return($cpf);
}



# Verifica formato informado para CPF
function cnpjVerificaFormato($cnpj) {

		$cnpj=str_replace(".","",$cnpj);
		$cnpj=str_replace("-","",$cnpj);
		$cnpj=str_replace("/","",$cnpj);
		$cnpj=str_replace("\\","",$cnpj);
		
		return($cnpj);
}


# Verifica formato informado para CPF
function cpfFormatar($cpf) {

		$cpf=cpfVerificaFormato($cpf);
		
		$cpf=substr($cpf, 0, 3).".".substr($cpf, 3, 3).".".substr($cpf, 6, 3)."-".substr($cpf, 9, 2);
		
		return($cpf);
} #fecha forma��o de cnpj



# Verifica formato informado para CPF
function cnpjFormatar($cnpj) {

		$cnpj=cnpjVerificaFormato($cnpj);
		
		$cnpj=substr($cnpj, 0, 2).".".substr($cnpj, 2, 3).".".substr($cnpj, 5, 3)."/".substr($cnpj, 8, 4)."-".substr($cnpj, 12, 2);
		
		return($cnpj);		
} # fecha forma��o de cnpj



# Fun��o para valida��o de CPF
function validaCPF($cpf) {


	# Vari�vel de Retorno
	$retorno="";
	
	# Verifica��o de CPF
	$nulo[0] = "12345678909";
	$nulo[1] = "11111111111";
	$nulo[2] = "22222222222";
	$nulo[3] = "33333333333";
	$nulo[4] = "44444444444";
	$nulo[5] = "55555555555";
	$nulo[6] = "66666666666";
	$nulo[7] = "77777777777";
	$nulo[8] = "88888888888";
	$nulo[9] = "99999999999";
	$nulo[10] = "00000000000";
	
	
	# Verificar se CPF foi digitado com pontos, barras, hifens,etc...
	$cpfOriginal=$cpf;
	$cpf=cpfVerificaFormato($cpf);
	
	# Verifica��o de CPFs Nulos
	for($i=0;$i<count($nulo);$i++) {
		if($cpf==$nulo[$i]) {
			$retorno=$cpf;
		}
	}

	if(strlen($cpf)!=11 || !$cpf) $retorno="";
	
	# Continuar caso n�o seja encontrado CPF
	if(!$retorno) {
		/* Aloca��o de cada digito digitado no formul�rio, em uma celula de um vetor */
		for ($i=0; $i<11; $i++) {
			$cpf_temp[$i]="$cpf[$i]";
		}
	
		/*Calcula o pen�ltimo d�gito verificador*/
		$acum=0;
		for ($i=0; $i<9; $i++){
			$acum=$acum+($cpf[$i]*(10-$i));
		}
		
		$x="$acum";
		$x %= 11;
		
		if ($x>1) $acum = 11 - $x;
		else $acum = 0;
		
		$cpf_temp[9]="$acum";
	
	
		# Calcula o �ltimo d�gito verificador
		$acum=0;
		for ($i=0; $i<10; $i++) {
			$acum=$acum+($cpf_temp[$i]*(11-$i));
		}
		
		$x="$acum";
		$x%=11;
		
		if ($x>1) $acum=11-$x;
		else $acum=0;
		
		$cpf_temp[10]="$acum";
	
	
		# Este la�o verifica se o cpf original � igual ao cpf gerado pelos dois la�os acima
		for ($i=0; $i<11; $i++) {
			if ($cpf[$i] != $cpf_temp[$i]) {
				$retorno=$cpf;
				$i=10;
				$z=1;
			}
		}
		
		
		if ($z!=1) $retorno=$cpf;
		else $retorno='';
	
	} # fecha veririca��o de CPF



	# Retornar valor
	if($retorno) $retorno=cpfFormatar($retorno);
	return($retorno);
	
} # fecha valida��o de CPF


# Verifica��o de CNPJ
function validaCNPJ($cnpj) {


	if( strlen(trim($cnpj)) > 0 ) {
	
		$cnpjOriginal=$cnpj;
		$cnpj=cnpjVerificaFormato($cnpj);
		if (strlen($cnpj) != 14 ) $retorno='';
		
		$soma1 =	($cnpj[0] * 5) +
					($cnpj[1] * 4) +
					($cnpj[2] * 3) +
					($cnpj[3] * 2) +
					($cnpj[4] * 9) +
					($cnpj[5] * 8) +
					($cnpj[6] * 7) +
					($cnpj[7] * 6) +
					($cnpj[8] * 5) +
					($cnpj[9] * 4) +
					($cnpj[10] * 3) +
					($cnpj[11] * 2);
				
		$resto = $soma1 % 11;
		
		# Obtem o Digito 1
		$digito1 = ( ( $resto < 2 ) ? 0 : 11 - $resto );
		
		$soma2 =	($cnpj[0] * 6) +
					($cnpj[1] * 5) +
					($cnpj[2] * 4) +
					($cnpj[3] * 3) +
					($cnpj[4] * 2) +
					($cnpj[5] * 9) +
					($cnpj[6] * 8) +
					($cnpj[7] * 7) +
					($cnpj[8] * 6) +
					($cnpj[9] * 5) +
					($cnpj[10] * 4) +
					($cnpj[11] * 3) +
					($cnpj[12] * 2);
			
		$resto = $soma2 % 11;
		
		# Obtem o digito 2
		$digito2 = ( ( $resto ) < 2 ? 0 : 11 - $resto  );
		
		# Retorna valor caso digitos estejam corretos
		if($cnpj[12]==$digito1 && $cnpj[13]== $digito2) {
			# OK
			$retorno=$cnpjOriginal;
		}
		else {
			# CNPJ Inv�lido
			$retorno='';
		}
	}
	else {
		$retorno='';
	}
	
	if($retorno) $retorno=cnpjFormatar($retorno);
	return($retorno);

} # fecha fun��o de verifica��o de CNPJ

function retirarAcentos( $string="", $mesma=1 ){

	if($string != ""){      
		$com_acento = "� � � � � � � � � � � � � � � � � � � � � � � � � � � � � � � � � � � � � � � � � � � � � � � ' ` �";   
		$sem_acento = "a a a a a e e e e i i i i o o o o o u u u u A A A A A E E E E I I I O O O O O U U U U c C n N _ _ _";   
		$c = explode(' ',$com_acento);
		$s = explode(' ',$sem_acento);
		
		$i=0;
		foreach($c as $letra){
			if(ereg($letra, $string)){
				$pattern[] = $letra;
				$replacement[] = $s[$i];
			}      
			$i=$i+1;      
		}
		
		if(isset($pattern)){
			$i=0;
			foreach($pattern as $letra){             
				$string = eregi_replace($letra, $replacement[$i], $string);
				$i=$i+1;      
			}
			return $string; # retorna string alterada
		}   
		if ($mesma != 0){
			return $string; # retorna a mesma string se nada mudou
		}
	}
	return ""; # sem mudan�a retorna nada
}

/**
 * Retorna o nome do usu�rio sem os caracteres que afetam na nomea��o de tabelas tempor�rias
 *
 * @param string $nome
 * @return string
 */
function validaNomeUsuario( $nome ) {
	return str_replace( '-', '_', $nome );
}

?>