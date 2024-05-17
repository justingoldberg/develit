<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 10/04/2003
# Ultima alteração: 22/03/2004
#    Alteração No.: 029
#
# Função:
#    Página principal (index) da aplicação - formulários

# Função para alimentar valores de formulário
function alimentaForm($matriz, $sessCadastro) {
	# Alimentar sessção (retornar valor) com dados de formulário alterados
	
	
	# Alimentar Sessão com variáveis já contidas
	if(is_array($sessCadastro)) $keys=array_keys($sessCadastro);
	
		for($i=0;$i<count($keys);$i++) {
			$tmpVariavel[$keys[$i]]=$sessCadastro[$keys[$i]];
		}
		
		
		# Alimentar variável para retorno
		if($matriz) {
		$keys=array_keys($matriz);
		for($i=0;$i<count($keys);$i++) {
			# alimentar variável
			//echo "gravando: $keys[$i]\n<br>";
			if($keys[$i]=='cpf') $tmpVariavel[$keys[$i]]=cpfFormatar($matriz[$keys[$i]]);
			if($keys[$i]=='cnpj') $tmpVariavel[$keys[$i]]=cnpjFormatar($matriz[$keys[$i]]);
			else $tmpVariavel[$keys[$i]]=$matriz[$keys[$i]];
		}
	}
	
	
	# retornar valor
	return($tmpVariavel);

} # fecha funcao de alimentação de sessao



# Função para alimentar valores de formulário
function visualizaForm($matriz) {
	# Alimentar sessção (retornar valor) com dados de formulário alterados
	
	$keys=array_keys($matriz);
	
	# Alimentar variável para retorno
	for($i=0;$i<count($keys);$i++) {
		# alimentar variável
		echo "Visualizando: $keys[$i]: ".$matriz[$keys[$i]]."\n<br>";
	}

} # fecha funcao de alimentação de sessao


# Calcular Totais
function calculaTotal($consulta, $campo, $limite, $decimal) {

	for($i=0;$i<$limite;$i++) {
		$retorno+=resultadoSQL($consulta, $i, $campo);
	}
	
	if($decimal) {
		$retorno=number_format($retorno,$decimal,',','.');
	}
	
	return($retorno);
}


# Calcular Totais
function calculaTotalDesconto($consulta, $valor, $desconto, $limite, $decimal) {
	
	for($i=0;$i<$limite;$i++) {
		$tmpValor=resultadoSQL($consulta, $i, $valor);
		$tmpDesconto=resultadoSQL($consulta, $i, $desconto);
		$retorno+=$tmpValor-($tmpValor*($tmpDesconto/100));
	}
	
	if($decimal) {
		$retorno=number_format($retorno,$decimal,',','.');
	}
	
	return($retorno);
}


# Calcular Totais
function calculaDesconto($consulta, $valor, $desconto, $limite, $decimal) {

	for($i=0;$i<$limite;$i++) {
		$tmpValor=resultadoSQL($consulta, $i, $valor);
		$tmpDesconto=resultadoSQL($consulta, $i, $desconto);
		$retorno+=($tmpValor*($tmpDesconto/100));
	}
	
	if($decimal) {
		$retorno=number_format($retorno,$decimal,',','.');
	}
	
	return($retorno);
}



# Verifica formato informado para CPF
function formFormatarDoc($doc) {

	$doc=str_replace(".","",$doc);
	$doc=str_replace("-","",$doc);
	$doc=str_replace("/","",$doc);
	$doc=str_replace("\\","",$doc);
	
	return($doc);
}


# Função para conversão de valores de formulário
function formFormatarStringArquivoRemessa($texto, $tipo){

	$matMinuscula=array(
		'à','á','â','ã','ä','å',
		'ç',
		'è','é','ê','ë',
		'ì','í','î','ï',
		'ñ',
		'ò','ó','ô','õ','ö','ø',
		'ù','ú','û','ü',
		'ý'
	);
	
	$matMinusculaNormal=array(
		'a','a','a','a','a','a',
		'c',
		'e','e','e','e',
		'i','i','i','i',
		'n',
		'o','o','o','o','o','o',
		'u','u','u','u',
		'y'
	);
	
	$matMaiuscula=array(
		'À','Á','Â','Ã','Ä','Å',
		'Ç',
		'È','É','Ê','Ë',
		'Ì','Í','Î','Ï',
		'Ñ',
		'Ò','Ó','Ô','Õ','Ö','Ø',
		'Ù','Ú','Û','Ü',
		'Ý'
	);
	
	$matMaiusculaNormal=array(
		'A','A','A','A','A','A',
		'C',
		'E','E','E','E',
		'I','I','I','I',
		'N',
		'O','O','O','O','O','O',
		'U','U','U','U',
		'Y'
	);
	

	if($tipo=='minuscula') {
		# Converter para maiúscula
		for($a=0;$a<count($matMinuscula);$a++) {
			$texto=strtolower($texto);
			$texto=str_replace($matMaiuscula[$a],$matMinusculaNormal[$a],$texto);
			$texto=str_replace($matMinuscula[$a],$matMinusculaNormal[$a],$texto);
		}
	} 
	elseif($tipo=='maiuscula') {
		# Converter para maiúscula
		for($a=0;$a<count($matMinuscula);$a++) {
			$texto=strtoupper($texto);
			$texto=str_replace($matMinuscula[$a],$matMaiusculaNormal[$a],$texto);
			$texto=str_replace($matMaiuscula[$a],$matMaiusculaNormal[$a],$texto);
		}
	}
	
	return($texto);
}



# Função para conversão de valores de formulário
function formFormatarString($texto, $tipo){

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


# Função para montar select de cores
function formSelectCor ($selected,$campo) {
	global $cores;
	$texto="<select name=matriz[$campo]>";
	while(list($key, $value) = each($cores)) {
		if($value == $selected) {
			$texto.="<option value=$value selected>$key</option>";
		} else {
			$texto.="<option value=$value>$key</option>";
		}
	}
	$texto.="</select>";
	return($texto);
}

# Função para montar select de números/prioridades
function formSelectNumeros ($selected,$campo) {
	$texto="<select name=matriz[$campo]>";
	for($i=0;$i<10;$i++) {
		if($i == $selected) {
			$texto.="<option value=$i selected>$i</option>";
		} else {
		$texto.="<option value=$i>$i</option>";
		}
	}
	$texto.="</select>";
	return($texto);
}

# Função para montar select de números/prioridades
function formSelectPorcentagem ($selected,$campo) {
	$texto="<select name=matriz[$campo]>";
	for($i=0;$i<=90;$i++) {
		if($i == $selected) {
			$texto.="<option value=$i selected>$i%</option>";
		} else {
		$texto.="<option value=$i>$i%</option>";
		}
	}
	$texto.="</select>";
	return($texto);
}

# Função para montar select de números/prioridades
function formSelectParcelas($selected,$campo) {
	$texto="<select name=matriz[$campo]>";
	for($i=1;$i<=12;$i++) {
		if($i==1) $valor='Única/A Vista';
		else $valor="$i parcelas";
		
		if($i == $selected) {
			$texto.="<option value=$i selected>$valor</option>";
		} else {
		$texto.="<option value=$i>$valor</option>";
		}
	}
	$texto.="</select>";
	return($texto);
}


# Função para montar select de números/prioridades
function formSelectLetras ($selected,$campo) {
  $texto="<select name=matriz[$campo]>";
  for($i="A";$i<="Z" && strlen($i)==1;$i++) {
			 if($i == $selected) {
						$texto.="<option value=$i selected>$i</option>";
			 } else {
						$texto.="<option value=$i>$i</option>";
			 }
  }
  $texto.="</select>";
  return($texto);
}


#  Função para mostrar form de seleçao Sim/Não
function formSelectSimNao($valor, $campo, $tipo) {
	
	if( $tipo=='form' ) {
		$select = ' selected="select"';
		if( $valor == 'S' ) $opcSelectSim = $select;
		if( $valor == 'N' ) $opcSelectNao = $select;
		$texto = "<select name=\"matriz[$campo]\">\n".
				 " <option value=\"S\" $opcSelectSim>Sim</option>\n".
				 " <option value=N $opcSelectNao>Não</option>\n".
				 "</select>";
	}
	elseif( $tipo=='check' ) {
		if( $valor == 'S' ) {
			$texto = '<span class="txtok">Sim</span>';
		}
		elseif( $valor == 'N' ) {
			$texto = '<span class="txtcheck">Não</span>';
		}
	}
	return($texto);
	
}

#  Função para mostrar form de seleçao Sim/Não
function formSelectCobranca($valor, $campo, $tipo) {

	if($valor=='S') $opcSelectSim='selected';
	elseif($valor=='N') $opcSelectNao='selected';
	elseif($valor=='A') $opcSelectRet='selected';
	
	if($tipo=='form') {
		$texto="<select name=matriz[$campo]>
			<option value=S $opcSelectSim>Sim\n 
			<option value=N $opcSelectNao>Não\n 
			<option value=A $opcSelectRet>Retroativo\n 
		</select>";
	}
	elseif($tipo=='check') {
		if($valor=='S') $texto="<span class=txtok>Sim</span>";
		elseif($valor=='N') $texto="<span class=txtcheck>Não</span>";
		elseif($valor=='A') $texto="<span class=txtcheck>Retroativo</span>";
	}
	
	return($texto);
	
}



#  Função para mostrar form de seleçao Sim/Não
function formSelectStatus($valor, $campo, $tipo) {

//	if($valor=='A') $opcSelectAtivo='selected';
//	if($valor=='I') $opcSelectInativo='selected';
//	if($valor=='C') $opcSelectCancelado='selected';
//	if($valor=='N') $opcSelectNovo='selected';
//	if($valor=='T') $opcSelectTrial='selected';
//	if($valor=='F') $opcSelectTrial='selected';
	
	$statusVariados['valores'] = array( 'A',		'I',		'C',			'N',				'T',		'F' );
	$statusVariados['nome']  = array( 'Ativo',	'Inativo',	'Cancelado',	'Novo/Aguardando',	'Trial',	'Fechado' );
	$statusVariados['css']   = array( 'txtok',	'txtaviso',	'txtaviso',		'txtcheck',			'txtrial',	'txttrial' );
	
	$statusPBC['valores'] = array( 'P',			'B',		'C' );
	$statusPBC['nome']  = array( 'Pendente',	'Baixado',	'Cancelado' );
	$statusPBC['css']   = array( 'txttrial',	'txtok',	'txtaviso' );

	$statusES['valores'] 	= array( 'E',		 'S' );
	$statusES['nome'] 		= array( 'Entrada - Retorno', 'Saída - Requisição' );
	$statusES['css']		= array( 'txtok', 	'txttrial' );
	
	if($tipo=='form') {
		$texto = getComboArray("matriz[$campo]", $statusVariados['nome'], $statusVariados['valores'], $valor );
//		$texto="< select name=matriz[$campo]>
//			<option value="A" $opcSelectAtivo>Ativado\n
//			<option value=I $opcSelectInativo>Inativo\n
//			<option value=C $opcSelectCancelado>Cancelado\n
//			<option value=N $opcSelectNovo>Novo/Aguardando\n
//			<option value=T $opcSelectNovo>Trial\n
//			<option value=T $opcSelectNovo>Fechado\n
//		</select>";
	}
	elseif($tipo=='check') {
		$i = array_search($valor, $statusVariados['valores'] );
		$texto = '<span class="' . $statusVariados['css'][$i] . '">' . $statusVariados['nome'][$i] . "</span>\n";
//		if($valor=='A') 	$texto="<span class=txtok>Ativo</span>";
//		elseif($valor=='I') $texto="<span class=txtaviso>Inativo</span>";
//		elseif($valor=='C') $texto="<span class=txtaviso>Cancelado</span>";
//		elseif($valor=='N') $texto="<span class=txtcheck>Novo/Aguardando</span>";
//		elseif($valor=='T') $texto="<span class=txttrial>Período de Trial</span>";
//		elseif($valor=='F') $texto="<span class=txttrial>Fechado</span>";
	}
	elseif( $tipo == 'form_pbc' ){
		array_pop( $statusPBC['nome'] ); // retira o ultimo elemento (cancelado)
		array_pop( $statusPBC['valores'] );
		$texto = getComboArray( "matriz[$campo]", $statusPBC['nome'], $statusPBC['valores'], $valor );	
	}
	elseif( $tipo == 'check_pbc' ) {
		$i = array_search($valor, $statusPBC['valores'] );
		$texto = '<span class="' . $statusPBC['css'][$i] . '">' . $statusPBC['nome'][$i] . "</span>\n";		
	}
	elseif( $tipo == 'form_es' ){
		$texto = getComboArray("matriz[$campo]", $statusES['nome'], $statusES['valores'], $valor );
	}
	elseif( $tipo == 'check_es') {
		$i = array_search($valor, $statusES['valores'] );
		$texto = '<span class="' . $statusES['css'][$i] . '">' . $statusES['nome'][$i] . "</span>\n";	
	}
	return($texto);
	
}



#  Função para mostrar form de seleçao Sim/Não
function formSelectStatusAtivoInativo($valor, $campo, $tipo, $evento='') {

	if($tipo=='form') {
		if($valor=='A') $opcSelectAtivo=' selected';
		if($valor=='I') $opcSelectInativo=' selected';
		$texto ="<select name=\"matriz[$campo]\"$evento>\n".
				" <option value=\"A\"".$opcSelectAtivo.">Ativado</option>\n".
				" <option value=\"I\"".$opcSelectInativo.">Inativo</option>\n".
				"</select>";
	}
	
	elseif( $tipo == 'form_sim_nao' ) {
		if($valor=='S') {
			$opcSelectAtivo=' selected';
		}
		else {
			$opcSelectInativo=' selected';
		}
		$texto ="<select name=\"matriz[$campo]\"$evento>\n".
				" <option value=\"S\"".$opcSelectAtivo.">Sim</option>\n".
				" <option value=\"N\"".$opcSelectInativo.">Não</option>\n".
				"</select>";
	}
	elseif($tipo=='check') {
		if($valor=='A') $texto="<span class=txtok>Ativo</span>";
		elseif($valor=='I') $texto="<span class=txtaviso>Inativo</span>";
	}
	elseif( $tipo == 'sim_nao' ) {
		$texto = ( $valor == 'S' ? '<span class="txtok">Sim</span>' : '<span class="txtaviso">Não</span>' );
	}
	return($texto);
	
}

#  Função para mostrar form de seleçao Sim/Não
/**
 * @return unknown
 * @param unknown $valor
 * @param unknown $campo
 * @param unknown $tipo
 * @desc Formulário de seleção de Status de Ocorrência
 Parametros:
 <B>valor</B> valor do campo atual
 <B>campo</B> nome do campo utilizado (para tipo=form)
 <B>tipo</B> tipo de retorno (check = string, form=selectbox, multi=multiseleção)
*/
function formSelectStatusOcorrencia($valor, $campo, $tipo) {

	if($tipo=='form') {
		
		if($valor=='N') $opcSelectNovo='selected';
		elseif($valor=='A') $opcSelectAberto='selected';
		elseif($valor=='P') $opcSelectProcesso='selected';
		elseif($valor=='F') $opcSelectFechado='selected';
		elseif($valor=='R') $opcSelectReAberto='selected';
		elseif($valor=='C') $opcSelectCancelado='selected';
		
		$texto="<select name=matriz[$campo]>
			<option value=N $opcSelectNovo>Novo\n
			<option value=A $opcSelectAberto>Aberto\n
			<option value=P $opcSelectProcesso>Em Processo\n
			<option value=F $opcSelectFechado>Fechado\n
			<option value=R $opcSelectReAberto>Re-Aberto\n
			<option value=C $opcSelectCancelado>Cancelado\n
		</select>";
	}
	elseif($tipo=='multi') {
		$texto="<select name=matriz[$campo][] multiple size=6>
			<option value=N>Novo\n
			<option value=A>Aberto\n
			<option value=P>Em Processo\n
			<option value=F>Fechado\n
			<option value=R>Re-Aberto\n
			<option value=C>Cancelado\n
		</select>";
	}
	elseif($tipo=='check') {
		if($valor=='N' || !$valor) $texto="<span class=txtcheck>Novo</span>";
		elseif($valor=='A') $texto="<span class=txttrial>Aberto</span>";
		elseif($valor=='P') $texto="<span class=txtcheck>Em Processo</span>";
		elseif($valor=='F') $texto="<span class=txtok>Fechado</span>";
		elseif($valor=='R') $texto="<span class=txttrial>Re-Aberto</span>";
		elseif($valor=='C') $texto="<span class=txtaviso>Cancelado</span>";
	}
	
	return($texto);
	
}


#  Função para mostrar form de seleçao Sim/Não
function formSelectStatusRadius($valor, $campo, $tipo) {

	if($valor=='A') $opcSelectAtivo='selected';
	if($valor=='I') $opcSelectInativo='selected';
	if($valor=='C') $opcSelectCancelado='selected';
	if($valor=='T') $opcSelectTrial='selected';
	
	if($tipo=='form') {
		$texto="<select name=matriz[$campo]>
			<option value=A $opcSelectAtivo>Ativado\n
			<option value=I $opcSelectInativo>Inativo\n
			<option value=C $opcSelectCancelado>Cancelado\n
			<option value=T $opcSelectTrial>Trial\n
		</select>";
	}
	elseif($tipo=='check') {
		if($valor=='A') $texto="<span class=txtok>Ativo</span>";
		elseif($valor=='I') $texto="<span class=txtaviso>Inativo</span>";
		elseif($valor=='C') $texto="<span class=txtaviso>Cancelado</span>";
		elseif($valor=='T') $texto="<span class=txttrial>Período de Trial</span>";
	}
	
	return($texto);
	
}




#  Função para mostrar form de seleçao Sim/Não
function formSelectStatusDescontos($valor, $campo, $tipo) {

	if($valor=='A') $opcSelectAtivo='selected';
	if($valor=='I') $opcSelectInativo='selected';
	if($valor=='B') $opcSelectBaixado='selected';
	if($valor=='C') $opcSelectCancelado='selected';
	
	if($tipo=='form') {
		$texto="<select name=matriz[$campo]>
			<option value=A $opcSelectAtivo>Ativado\n
			<option value=I $opcSelectInativo>Inativo\n
			<option value=B $opcSelectBaixado>Baixado\n
			<option value=C $opcSelectCancelado>Cancelado\n
		</select>";
	}
	elseif($tipo=='check') {
		if($valor=='A') $texto="<span class=txtok>Ativo</span>";
		elseif($valor=='I') $texto="<span class=txtaviso>Inativo</span>";
		elseif($valor=='B') $texto="<span class=txtaviso>Baixado</span>";
		elseif($valor=='C') $texto="<span class=txtaviso>Cancelado</span>";
	}
	
	return($texto);
	
}





#  Função para mostrar form de seleçao Sim/Não
function formSelectStatusDominios($valor, $campo, $tipo) {

	if($valor=='A') $opcSelectAtivo='selected';
	if($valor=='I') $opcSelectInativo='selected';
	if($valor=='B') $opcSelectBloqueado='selected';
	if($valor=='C') $opcSelectCongelado='selected';
	
	if($tipo=='form') {
		$texto="<select name=matriz[$campo]>
			<option value=A $opcSelectAtivo>Ativado\n
			<option value=I $opcSelectInativo>Inativo\n
			<option value=B $opcSelectBloqueado>Bloqueado\n
			<option value=C $opcSelectCongelado>Congelado\n
		</select>";
	}
	elseif($tipo=='check') {
		if($valor=='A') $texto="<span class=txtok>Ativo</span>";
		elseif($valor=='I') $texto="<span class=txtaviso>Inativo</span>";
		elseif($valor=='B') $texto="<span class=txtaviso>Bloqueado</span>";
		elseif($valor=='C') $texto="<span class=txtaviso>Congelado</span>";
	}
	
	return($texto);
	
}

#  Fun?o para mostrar form de sele?o Sim/N?
function formSelectStatusEmails($valor, $campo, $tipo) {

	if($valor=='A') $opcSelectAtivo='selected';
	if($valor=='I') $opcSelectInativo='selected';
	if($valor=='B') $opcSelectBloqueado='selected';
	
	if($tipo=='form') {
		$texto="<select name=matriz[$campo]>
			<option value=A $opcSelectAtivo>Ativado\n
			<option value=I $opcSelectInativo>Inativo\n
			<option value=B $opcSelectBloqueado>Bloqueado\n
		</select>";
	}
	elseif($tipo=='check') {
		if($valor=='A') $texto="<span class=txtok>Ativo</span>";
		elseif($valor=='I') $texto="<span class=txtaviso>Inativo</span>";
		elseif($valor=='B') $texto="<span class=txtaviso>Bloqueado</span>";
	}
	
	return($texto);
	
}

function formSelectStatusSuporte($valor, $campo, $tipo) {

	if($valor=='A') $opcSelectAtivo='selected';
	if($valor=='I') $opcSelectInativo='selected';
	if($valor=='B') $opcSelectBloqueado='selected';
	
	if($tipo=='form') {
		$texto="<select name=matriz[$campo]>
			<option value=A $opcSelectAtivo>Ativado\n
			<option value=I $opcSelectInativo>Inativo\n
			<option value=B $opcSelectBloqueado>Bloqueado\n
		</select>";
	}
	elseif($tipo=='check') {
		if($valor=='A') $texto="<span class=txtok>Ativo</span>";
		elseif($valor=='I') $texto="<span class=txtaviso>Inativo</span>";
		elseif($valor=='B') $texto="<span class=txtaviso>Bloqueado</span>";
	}
	
	return($texto);
	
}

function formSelectStatusPrioridade($valor, $campo, $tipo) {


	if( $valor=='M' ){
		$opcSelectMedia = 'selected';
	}
	if( $valor=='A' ){
		$opcSelectAlta = 'selected';
	}
	if( $valor=='B' ){
		$opcSelectBaixa = 'selected';
	}
	
	if( $tipo=='form' ){
		$texto = "<select name=matriz[$campo]>
					<option value=M $opcSelectMedia>Média\n
					<option value=B $opcSelectBaixa>Baixa\n
					<option value=A $opcSelectAlta>Alta\n					
					
				  </select>";
	}
	elseif( $tipo=='check' ){
		if( $valor=='A' ){
			$texto="<span class=txtok>Alta</span>";
		}
		elseif( $valor=='M' ){
			$texto="<span class=txtaviso>Média</span>";
		}
		elseif( $valor=='B' ){
			$texto="<span class=txtaviso>Baixa</span>";
		}
	}
	return $texto;	
}


#  Função para mostrar form de seleçao Sim/Não
function formSelectStatusRadiusTelefones($valor, $campo, $tipo) {

	if($valor=='A') $opcSelectAtivo='selected';
	if($valor=='I') $opcSelectInativo='selected';
	
	if($tipo=='form') {
		$texto="<select name=matriz[$campo]>
			<option value=A $opcSelectAtivo>Ativado\n
			<option value=I $opcSelectInativo>Inativo\n
		</select>";
	}
	elseif($tipo=='check') {
		if($valor=='A') $texto="<span class=txtok>Ativo</span>";
		elseif($valor=='I') $texto="<span class=txtaviso>Inativo</span>";
	}
	return($texto);
}


#  Função para mostrar form de seleçao Sim/Não
function formSelectStatusPOP($valor, $campo, $tipo) {

	if($valor=='A') $opcSelectAtivo='selected';
	if($valor=='I') $opcSelectInativo='selected';
	
	if($tipo=='form') {
		$texto="<select name=matriz[$campo]>
			<option value=A $opcSelectAtivo>Ativado\n
			<option value=I $opcSelectInativo>Inativo\n
		</select>";
	}
	elseif($tipo=='check') {
		if($valor=='A') $texto="<span class=txtok>Ativo</span>";
		elseif($valor=='I') $texto="<span class=txtaviso>Inativo</span>";
	}
	return($texto);
}


#  Função para mostrar form de seleçao de status de contratos
/**
 * @return unknown
 * @param unknown $valor
 * @param unknown $campo
 * @param unknown $tipo
 * @desc Form de Seleção/Retorno de nome do Status do Contrato
 <b>valor</b> Valor a ser repassado/checado
 <b>campo</b> Nome do campo em caso de tipo=form
 <b>tipo</b> Tipo do retorno:
  -> form = formulário de seleção de dados:
  -> multi = form multipla seleção,
  -> check = retorno com nome do status)
*/
function formSelectStatusContratos($valor, $campo, $tipo) {

	if($tipo=='form') {
		if($valor=='A') $opcSelectAtivo='selected';
		if($valor=='I') $opcSelectInativo='selected';
		if($valor=='C') $opcSelectCancelado='selected';
		if($valor=='R') $opcSelectRenovado='selected';
		$texto="<select name=matriz[$campo]>
			<option value=A $opcSelectAtivo>Ativado\n
			<option value=I $opcSelectInativo>Inativo\n
			<option value=C $opcSelectCancelado>Cancelado\n
			<option value=R $opcSelectRenovado>Renovado\n
		</select>";
	}
	if($tipo=='multi') {
		$texto="<select name=matriz[$campo][] multiple>
			<option value=A>Ativado\n
			<option value=I>Inativo\n
			<option value=C>Cancelado\n
			<option value=R>Renovado\n
		</select>";
	}
	elseif($tipo=='check') {
		if($valor=='A') $texto="<span class=txtok>Ativo</span>";
		elseif($valor=='I') $texto="<span class=txtcheck>Inativo</span>";
		elseif($valor=='C') $texto="<span class=txtaviso>Cancelado</span>";
		elseif($valor=='R') $texto="<span class=txttrial>Renovado</span>";
	}
	return($texto);
}


#  Função para mostrar form de seleçao Sim/Não
function formSelectStatusContasReceber($valor, $campo, $tipo) {

	if($valor=='P') $opcSelectPendente='selected';
	if($valor=='B') $opcSelectBaixado='selected';
	if($valor=='C') $opcSelectCancelado='selected';
	
	if($tipo=='form') {
		$texto="<select name=matriz[$campo]>
			<option value=P $opcSelectPendente>Pendente\n
			<option value=B $opcSelectBaixado>Baixado\n
			<option value=C $opcSelectCancelado>Cancelado\n
		</select>";
	}
	elseif($tipo=='check') {
		if($valor=='P') $texto="<span class=txtaviso>Pendente</span>";
		elseif($valor=='B') $texto="<span class=txtok>Baixado</span>";
		elseif($valor=='C') $texto="<span class=txtcanc>Cancelado</span>";
	}
	
	return($texto);
}


#  Função para mostrar form de seleçao Sim/Não
function formSelectStatusContasPagar($valor, $campo, $tipo) {

	if($valor=='P') $opcSelectPendente='selected';
	if($valor=='B') $opcSelectBaixado='selected';
	if($valor=='C') $opcSelectCancelado='selected';
	
	if($tipo=='form') {
		$texto="<select name=matriz[$campo]>
			<option value=P $opcSelectPendente>Pendente\n
			<option value=B $opcSelectBaixado>Baixado\n
			<option value=C $opcSelectCancelado>Cancelado\n
		</select>";
	}
	elseif($tipo=='check') {
		if($valor=='P') $texto="<span class=txtaviso>Pendente</span>";
		elseif($valor=='B') $texto="<span class=txtok>Baixado</span>";
		elseif($valor=='C') $texto="<span class=txtcanc>Cancelado</span>";
	}
	
	return($texto);
}


#  Função para mostrar form de seleçao Sim/Não
function formSelectStatusRemessa($valor, $campo, $tipo) {

	if($valor=='N') $opcSelectPendente='selected';
	if($valor=='A') $opcSelectGerado='selected';
	
	if($tipo=='form') {
		$texto="<select name=matriz[$campo]>
			<option value=N $opcSelectPendente>Pendente\n
			<option value=A $opcSelectBaixado>Gerado\n
		</select>";
	}
	elseif($tipo=='check') {
		if($valor=='N') $texto="<span class=txtaviso>Pendente</span>";
		elseif($valor=='A') $texto="<span class=txtok>Gerado</span>";
	}
	
	return($texto);
}



#  Função para mostrar form de seleçao Sim/Não
function formSelectStatusRetorno($valor, $campo, $tipo) {

	if($valor=='N') $opcSelectPendente='selected';
	if($valor=='P') $opcSelectProcessado='selected';
	
	if($tipo=='form') {
		$texto="<select name=matriz[$campo]>
			<option value=N $opcSelectPendente>Pendente\n
			<option value=A $opcSElectProcessado>Processado\n
		</select>";
	}
	elseif($tipo=='check') {
		if($valor=='N') $texto="<span class=txtaviso>Pendente</span>";
		elseif($valor=='A') $texto="<span class=txtok>Processado</span>";
	}
	
	return($texto);
	
}


#  Função para mostrar form de seleçao Sim/Não
function formSelectStatusServicoAdicional($valor, $campo, $tipo) {

	if($valor=='A') $opcSelectAtivo='selected';
	if($valor=='I') $opcSelectInativo='selected';
	if($valor=='B') $opcSelectBaixado='selected';
	if($valor=='C') $opcSelectCancelado='selected';
	
	if($tipo=='form') {
		$texto="<select name=matriz[$campo]>
			<option value=A $opcSelectAtivo>Ativado\n
			<option value=I $opcSelectInativo>Inativo\n
			<option value=B $opcSelectBaixado>Baixado\n
			<option value=C $opcSelectCancelado>Cancelado\n
		</select>";
	}
	elseif($tipo=='check') {
		if($valor=='A') $texto="<span class=txtok>Ativo</span>";
		elseif($valor=='I') $texto="<span class=txtaviso>Inativo</span>";
		elseif($valor=='B') $texto="<span class=txtaviso>Baixado</span>";
		elseif($valor=='C') $texto="<span class=txtaviso>Cancelado</span>";
	}
	
	return($texto);
	
}




#  Função para mostrar form de seleçao Sim/Não
function formSelectNumero($valor, $inicio, $fim, $campo, $tipo) {

	if($tipo=='form') {
		$texto="<select name=matriz[$campo]>\n";
	
		for($i=$inicio;$i<=$fim;$i++) {
			if($valor==$i) $opcSelect='selected';
			else $opcSelect='';
			$texto.="<option value=$i $opcSelect>$i\n";
		}
		
		$texto.="</select>";
	}
	elseif($tipo=='check') {
		$texto=$valor;
	}
	return($texto);
}



# função para seleção de tipo de parametro
function formSelectTipoParametro($valor, $campo, $tipo) {

	if($tipo=='form') {
	
		if($valor=='sn') $opcSN='selected';
		if($valor=='nr') $opcNR='selected';
		
		$retorno="<select name=matriz[$campo] onChange='javascript:submit()'>
			<option value=sn $opcSN>Caixa de seleção [Sim/Não]\n
			<option value=nr $opcNR>Campo Numérico\n
		</select>";
		#formulário
	}
	elseif($tipo=='check') {
		# visualização
		if($valor=='sn') $retorno='Seleção [Sim/Não]';
		elseif($valor=='nr') $retorno='Numérico';
	}
	
	return($retorno);
}




# função para seleção de tipo de parametro
function formSelectTipoTipoCobranca($valor, $campo, $tipo) {


	global $configCobranca;
	
	$keys=array_keys($configCobranca);
	
	if($tipo=='form') {

		$retorno="<select name=matriz[$campo]>";
		#formulário

		for($a=0;$a<count($keys);$a++) {
	
			if($valor==$keys[$a]) $opcSelect='selected';
			else $opcSelect='';
			
			$retorno.="<option value=$keys[$a] $opcSelect>".$configCobranca[$keys[$a]];
		}
		
		$retorno.="</select>";
		
		
	}
	elseif($tipo=='check') {
		# visualização
		$retorno=$configCobranca[$valor];
	}
	
	return($retorno);
}



# função para seleção de tipo de parametro
function formSelectTipoFormaCobranca($valor, $campo, $tipo) {


	global $configFormaCobranca;
	
	$keys=array_keys($configFormaCobranca);
	
	if($tipo=='form') {

		$retorno="<select name=matriz[$campo]>";
		#formulário

		for($a=0;$a<count($keys);$a++) {
	
			if($valor==$keys[$a]) $opcSelect='selected';
			else $opcSelect='';
			
			$retorno.="<option value=$keys[$a] $opcSelect>".$configFormaCobranca[$keys[$a]];
		}
		
		$retorno.="</select>";
		
		
	}
	elseif($tipo=='check') {
		# visualização
		$retorno=$configFormaCobranca[$valor];
	}
	
	return($retorno);
}



# Formulário para preenchimento/seleção de valor de campo
function formInputValorParametro($parametro, $valor, $campo, $tipo) {

	if($tipo=='form') {
	
		# Verificar o tipo de parametro
		$consulta=buscaParametros($parametro, 'id','igual','id');
		
		if($consulta) {
		
			$tipoParametro=resultadoSQL($consulta, 0, 'tipo');
			
			# Verificar tipo de form
			if($tipoParametro=='nr') {
				$retorno="<input type=text name=matriz[valor] size=6 value='$valor'>";
			}
			elseif($tipoParametro=='sn') {
				$retorno=formSelectSimNao($valor, 'valor', 'form');
			}	
		}
		else {
			$retorno="ERRO ao consultar parâmetro";
		}
	}
	elseif($tipo=='check') {
		
	}
	
	return($retorno);
}



# Formulário para preenchimento/seleção de valor de campo
function formInputParametro($parametro, $valor, $campo, $tipo) {

	if($tipo=='form') {
	
		# Verificar o tipo de parametro
		$consulta=buscaParametros($parametro, 'id','igual','id');
		
		if($consulta) {
		
			$tipoParametro=resultadoSQL($consulta, 0, 'tipo');
			$idUnidade=resultadoSQL($consulta, 0, 'idUnidade');
			
			# Verificar tipo de form
			if($tipoParametro=='nr') {
				# Buscar Unidade
				
				$retorno="<input type=text name=matriz[$campo] size=6 value='$valor'> <b>" . formSelectUnidades($idUnidade, '','check') . "</b>";
			}
			elseif($tipoParametro=='sn') {
				$retorno=formSelectSimNao($valor, "$campo", 'form');
			}	
		}
		else {
			$retorno="ERRO ao consultar parâmetro";
		}
	}
	elseif($tipo=='check') {
		
	}
	
	return($retorno);
}



# Fomulário para seleção de desconto
function formSelectDesconto($desconto, $campo, $tipo, $valor, $objetoForm) {

	global $configDescontos;
	
	# Mostrar lista de descontos
	$retorno="<select name=matriz[$campo] onChange=calculaDesconto(\"$valor\",this.value,$objetoForm);>\n
	<option value=0>Selecione o desconto";
	
	for($a=0;$a<count($configDescontos);$a++) {
	
		if($desconto==$configDescontos[$a]) $opcSelect='selected';
		else $opcSelect='';
	
		$retorno.="<option value='$configDescontos[$a]'>$configDescontos[$a]%";	
	}
	
	$retorno.="</select>";
	
	return($retorno);
}



# Fomulário para seleção de desconto
function formSelectDescontoAplicado($desconto, $campo, $tipo, $valor, $objetoForm) {

	global $configDescontos;
	
	# Mostrar lista de descontos
	$retorno="<select name=matriz[$campo] onChange=calculaDescontoAplicado(\"$valor\",this.value,$objetoForm);>\n
	<option value=0>Selecione o desconto";
	
	for($a=0;$a<count($configDescontos);$a++) {
	
		if($desconto==$configDescontos[$a]) $opcSelect='selected';
		else $opcSelect='';
	
		$retorno.="<option value='$configDescontos[$a]'>$configDescontos[$a]%";	
	}
	
	$retorno.="</select>";
	
	return($retorno);
}



# Fomulário para seleção de desconto
function formSelectTipoDesconto($desconto, $campo, $tipo) {

	global $configTipoDescontos;
	
	$keys=array_keys($configTipoDescontos);
	
	if($tipo=='form') {

		$retorno="<select name=matriz[$campo] onChange=form.submit();>";
		#formulário

		for($a=0;$a<count($keys);$a++) {
	
			if($desconto==$keys[$a]) $opcSelect='selected';
			else $opcSelect='';
			
			$retorno.="<option value=$keys[$a] $opcSelect>".$configTipoDescontos[$keys[$a]];
		}
		
		$retorno.="</select>";
		
		
	}
	elseif($tipo=='check') {
		# visualização
		$retorno=$configTipoDescontos[$desconto];
	}
	
	return($retorno);
}



# Fomulário para seleção de mes/ano referência
function formSelectMes($mes, $campo, $tipo) {

	global $configMeses;
	
	$data=dataSistema();
	
	$keys=array_keys($configMeses);
	
	if($tipo=='form') {

		$retorno="<select name=matriz[$campo]>";
		#formulário

		for($a=0;$a<count($keys);$a++) {

			if($mes==$keys[$a]) $opcSelect='selected';
			elseif(!$mes && $data[mes] == $keys[$a]) $opcSelect='selected';
			else $opcSelect='';
			
			$retorno.="<option value=$keys[$a] $opcSelect>".$configMeses[$keys[$a]];
		}
		
		$retorno.="</select>";
		
		
	}
	elseif($tipo=='check') {
		# visualização
		$retorno=$configMeses[$mes];
	}
	
	return($retorno);
}



# Fomulário para seleção de mes/ano referência
function formSelectAno($ano, $campo, $tipo) {

	$data=dataSistema();
	
	if($tipo=='form') {

		$retorno="<select name=matriz[$campo]>";
		#formulário

		for($a=($data[ano]-3);$a<($data[ano]+3);$a++) {
	
			if($ano==$a) $opcSelect='selected';
			elseif(!$ano && $data[ano]==$a) $opcSelect='selected';
			else $opcSelect='';
			
			$retorno.="<option value=$a $opcSelect> $a";
		}
		
		$retorno.="</select>";
		
		
	}
	elseif($tipo=='check') {
		# visualização
		$retorno=$ano;
	}
	
	return($retorno);
}



# Fomulário para seleção de dado da consultas
function formSelectConsulta($consulta, $campo_descricao, $campo_chave, $campo_form, $registro, 
							$evento = '' ) {

	if($consulta && contaConsulta($consulta)>0) {
		$retorno = "<select name=\"matriz[$campo_form]\" class=\"normal8\" ".
		( $evento ? $evento : 'onchange="javascript:submit()"' ) . ">\n";
		$retorno.="<option value=\"0\">Selecione</option>\n";
		for($a=0;$a<contaConsulta($consulta);$a++) {
			# campos
			$id=resultadoSQL($consulta, $a, $campo_chave);
			$descricao=resultadoSQL($consulta, $a, $campo_descricao);
			# Listar registros
			
			if($id==$registro) $opcSelect='selected="selected"';
			else $opcSelect='';
			
			$retorno.="<option value=\"$id\" $opcSelect>$descricao</option>\n";
			
		}
		
		$retorno.="</select>\n";
	}
	
	return($retorno);
}

/**
 * Função de forma para seleção de Faturamentos
 *
 * @param string $faturamento
 * @param string $campo
 * @param string $tipo
 * @return string
 */
function formSelectFaturamento( $faturamento, $campo, $tipo) {

	if($tipo=='check') {
	
	
	}
	elseif($tipo=='form_descricao') {
	
		$consulta=buscaFaturamentos( 'A', 'status', 'igual', 'id DESC');
		
		if($consulta && contaConsulta($consulta)>0) {
			
			$retorno="<select name=matriz[$campo] >";  /*onChange=javascript:submit();*/ 
			
			for($i=0;$i<contaConsulta($consulta);$i++) {
			
				$id=resultadoSQL($consulta, $i, 'id');
				$descricao=resultadoSQL($consulta, $i, 'descricao');
				
				if($faturamento==$id) $opcSelect='selected';
				else $opcSelect='';
				
				$retorno.="\n<option value=$id $opcSelect>$descricao";
			}
			
			$retorno.="</select>";
		}
		else {
			$retorno = "<span class=txtaviso>Não há faturamentos ativos.<span>";
		}		
	}
	
	return($retorno);
}

/**
 * @return void
 * @param array 	$matriz
 * @param boolean 	$opcDe
 * @param boolean 	$opcAte
 * @desc Exibe 2 linhas apara receber a Data de Inicio e a data Final de um periodo.
*/
function formPeriodoMesAno($matriz, $opcDe, $opcAte) {
	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL('<b>Período inicial:</b><br>
		<span class=normal10>Selecione o mês/ano inicial</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		if($matriz[inclui_De]) $opcDe='checked';
		$texto="<input type=checkbox name=matriz[inclui_De] value=S $opcDe><b>Incluir esta data</b>";
		itemLinhaForm(formSelectMes($matriz[mesDe],'mesDe','form').formSelectAno($matriz[anoDe],'anoDe','form').$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL('<b>Período final:</b><br>
		<span class=normal10>Selecione o mês/ano final</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		if($matriz[inclui_Ate]) $opcAte='checked';
		$texto="<input type=checkbox name=matriz[inclui_Ate] value=S $opcAte><b>Incluir esta data</b>";			
		itemLinhaForm(formSelectMes($matriz[mesAte],'mesAte','form').formSelectAno($matriz[anoAte],'anoAte','form').$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
}


/**
 * Abre Formulario.
 *
 * @param unknown_type $nome
 * @param unknown_type $acao
 * @param unknown_type $methodo
 */
function abreFormulario($nome="matriz", $acao = "index.php", $metodo="post"){
	global $retornaHtml;
	
	$saida="<form method=\"$metodo\" name=\"$nome\" action=\"$acao\">\n";
	
	if($retornaHtml) 
		return ($saida);
	else 
		echo $saida;
}

/**
 * Abre o forumulário contendo o cabeçalho necessário para a navegação
 * por padrao insere 3 campos hiddens, caso haja registro 4, e caso haja o array extra somar 
 *
 * @param unknown_type $modulo
 * @param unknown_type $sub
 * @param unknown_type $acao
 * @param unknown_type $registro
 * @return unknown
 */
function abreFormularioComCabecalho( $modulo, $sub, $acao, $registro = "", $extraItem = array(), $extraConteudo = array() ){
	global $retornaHtml;
	
	$saida = abreFormulario();
	
	$saida .= "<input type=\"hidden\" name=\"modulo\" value=\"$modulo\" />\n";
	$saida .= "<input type=\"hidden\" name=\"sub\" value=\"$sub\" />\n";
	$saida .= "<input type=\"hidden\" name=\"acao\" value=\"$acao\" />\n";
	$saida .= "<input type=\"hidden\" name=\"registro\" value=\"$registro\" />\n";
	
	if ( is_array( $extraItem) && count($extraItem) == count($extraConteudo) ){
		for ( $i = 0; $i< count( $extraItem ); $i++){
			$saida .= "<input type=\"hidden\" name=\"". $extraItem[$i]."\" value=\"".$extraConteudo[$i]."\">\n";
		} 
	}
	
	if($retornaHtml) {
		return ($saida);
	} else {
		echo $saida;
	}
}

/**
 * Fecha o formulário.
 *
 * @return string
 */
function fechaFormulario() {
	global $retornaHtml;
	
	$saida = "</form>";
	
	if( $retornaHtml ) {
		return ($saida);
	} else {
		echo $saida;
	}
}

/**
 * @return void
 * @param unknown $tipo
 * @param unknown $name
 * @param unknown $value
 * @param unknown $event
 * @param unknown $tamanho
 * @desc Retorna um campo de formulario input
*/
function getInput( $tipo="text", $name="", $value="", $event="", $tamanho=60, $class="", $disabled = "", $id = '' ){
	
	if( $disabled ) {
		$disabled = 'disabled="disabled"';
		if( $class ) {
			$classe = ' class="'.$class.'disabled"';
		}
	}
	else {
		if( $class ) {
			$classe = ' class="'.$class.'"';
		}
	}
	
	return '<input type="'.$tipo.'" id="'.$id.'" name="'.$name.'" value="'.$value.
		   '" maxlenght="'.$tamanho.'" size="'.$tamanho.'" '.$event.$classe." ".$disabled." />\n";
}

function getTextArea($name, $valor='', $cols=60, $rows=3, $event='', $class='', $disabled=''){
	if( $class ) $class = ' class="'.$class.'" ';
	return '<textarea name="'.$name.'" cols="'.$cols.'" rows="'.$rows.'" '.$event. $class.' >'.$valor.'</textarea>';
}

function getCamposOcultos( $nomes = array(), $valores = array() ) {
	global $retornaHtml;
	
	$totalItens = count( $nomes );
	if ( is_array( $nomes ) && $totalItens == count( $valores ) ){	
		for ( $i = 0; $i< $totalItens; $i++){
			$saida .= "<input type=\"hidden\" name=\"". $nomes[$i] . "\" value=\"" . $valores[$i] . "\" />\n";
		} 
	}
	
	if($retornaHtml) {
		return ($saida);
	} else {
		echo $saida;
	}
}
/**
 * Retorna o campo do tipo numerico.
 *
 * @param unknown_type $nome
 * @param unknown_type $valor
 * @param unknown_type $tamanho
 * @param unknown_type $classe
 * @param unknown_type $desabilitado
 * @return unknown
 */
function getCampoNumero( $nome, $valor, $tamanho=20, $classe='textbox', $desabilitado = false, $evento='',$id='' ) {
	
	if( $classe == '' ) $classe == 'textbox';
	if( $desabilitado ) {
		$disabled 	= 'disabled="disabled"';
		$class		= $classe.'disabled';
	}
	else {
		$disabled 	= "";
		$class		= $classe;
	}

	if( $evento && strstr( strtolower( $evento ), 'onblur' ) ) {
		$evento = str_replace( 'onblur=', '', $evento );
		$evento = str_replace( '"', '', $evento );

		$event = ' onblur="formataValor(this.value, this.name);'.$evento.'" style="text-align: right;"';
	}
	else {
		$event = ' onblur="formataValor(this.value, this.name)" style="text-align: right;" ' . $evento;
	}
	return getInput('text', $nome, ( $valor ? number_format( $valor,2,',','' ) : ''), $event, $tamanho, $class, $disabled, $id );
}

/**
 * Retorna um campo do tipo data
 *
 * @param unknown_type $nome
 * @param unknown_type $valor
 * @param unknown_type $classe
 * @param unknown_type $desabilitado
 * @param unknown_type $evento
 * @return unknown
 */
function getCampoData( $nome, &$valor, $classe='textbox', $desabilitado=false, $evento='' ) {
	if( $classe == '' ) $classe == 'textbox';
	if( $desabilitado ) {
		$disabled 	= 'disabled="disabled"';
		$class		= $classe.'disabled';
	}
	else {
		$disabled 	= "";
		$class		= $classe;
	}
	// verifica se o evento onblur não está sendo usado no evento. Se tiver ele funde ao onblur
	if( $evento && strstr( strtolower( $evento ), 'onblur' ) ) {
		$evento = str_replace( 'onblur=', '', $evento );
		$evento = str_replace( '"', '', $evento );

		$event = ' onblur="verificaData(this.value, this.name);'.$evento.'"';
	}
	else {
		$event = ' onblur="verificaData(this.value, this.name)" ' . $evento;
	}
	// verifica a formatação da data
	if( $valor ) {
		if( preg_match( '/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $valor ) ){ // se formato de banco de dados
			$data = converteData( &$valor, 'banco', 'formdata' );
		}
		elseif( preg_match( '/[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{2,4}/', $valor ) ) { // se formato d/m/a, entao não faz nada
			$data = $valor;
		}
	}
	else {
		$data = '';
	}
	return getInput( 'text', $nome, &$data, &$event, 10, &$class, &$disabled );
}

?>