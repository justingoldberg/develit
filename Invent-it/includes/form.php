<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 10/04/2003
# Ultima altera��o: 08/12/2003
#    Altera��o No.: 021
#
# Fun��o:
#    P�gina principal (index) da aplica��o - formul�rios

# Fun��o para alimentar valores de formul�rio
function alimentaForm($matriz, $sessCadastro) {
	# Alimentar sess��o (retornar valor) com dados de formul�rio alterados
	
	
	# Alimentar Sess�o com vari�veis j� contidas
	if(is_array($sessCadastro)) $keys=array_keys($sessCadastro);
	
		for($i=0;$i<count($keys);$i++) {
			$tmpVariavel[$keys[$i]]=$sessCadastro[$keys[$i]];
		}
		
		
		# Alimentar vari�vel para retorno
		if($matriz) {
		$keys=array_keys($matriz);
		for($i=0;$i<count($keys);$i++) {
			# alimentar vari�vel
			//echo "gravando: $keys[$i]\n<br>";
			if($keys[$i]=='cpf') $tmpVariavel[$keys[$i]]=cpfFormatar($matriz[$keys[$i]]);
			if($keys[$i]=='cnpj') $tmpVariavel[$keys[$i]]=cnpjFormatar($matriz[$keys[$i]]);
			else $tmpVariavel[$keys[$i]]=$matriz[$keys[$i]];
		}
	}
	
	
	# retornar valor
	return($tmpVariavel);

} # fecha funcao de alimenta��o de sessao



# Fun��o para alimentar valores de formul�rio
function visualizaForm($matriz) {
	# Alimentar sess��o (retornar valor) com dados de formul�rio alterados
	
	$keys=array_keys($matriz);
	
	# Alimentar vari�vel para retorno
	for($i=0;$i<count($keys);$i++) {
		# alimentar vari�vel
		echo "Visualizando: $keys[$i]: ".$matriz[$keys[$i]]."\n<br>";
	}

} # fecha funcao de alimenta��o de sessao


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


# Fun��o para convers�o de valores de formul�rio
function formFormatarStringArquivoRemessa($texto, $tipo){

	$matMinuscula=array(
		'�','�','�','�','�','�',
		'�',
		'�','�','�','�',
		'�','�','�','�',
		'�',
		'�','�','�','�','�','�',
		'�','�','�','�',
		'�'
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
		'�','�','�','�','�','�',
		'�',
		'�','�','�','�',
		'�','�','�','�',
		'�',
		'�','�','�','�','�','�',
		'�','�','�','�',
		'�'
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
		# Converter para mai�scula
		for($a=0;$a<count($matMinuscula);$a++) {
			$texto=strtolower($texto);
			$texto=str_replace($matMaiuscula[$a],$matMinusculaNormal[$a],$texto);
			$texto=str_replace($matMinuscula[$a],$matMinusculaNormal[$a],$texto);
		}
	} 
	elseif($tipo=='maiuscula') {
		# Converter para mai�scula
		for($a=0;$a<count($matMinuscula);$a++) {
			$texto=strtoupper($texto);
			$texto=str_replace($matMinuscula[$a],$matMaiusculaNormal[$a],$texto);
			$texto=str_replace($matMaiuscula[$a],$matMaiusculaNormal[$a],$texto);
		}
	}
	
	return($texto);
}



# Fun��o para convers�o de valores de formul�rio
function formFormatarString($texto, $tipo){

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


# Fun��o para montar select de cores
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

# Fun��o para montar select de n�meros/prioridades
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


# Fun��o para montar select de n�meros/prioridades
function formSelectParcelas($selected,$campo) {
	$texto="<select name=matriz[$campo]>";
	for($i=1;$i<10;$i++) {
		if($i==1) $valor='�nica/A Vista';
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


# Fun��o para montar select de n�meros/prioridades
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


#  Fun��o para mostrar form de sele�ao Sim/N�o
function formSelectSimNao($valor, $campo, $tipo) {

	if($valor=='S') $opcSelectSim='selected';
	if($valor=='N') $opcSelectNao='selected';
	
	if($tipo=='form') {
		$texto="<select name=matriz[$campo]>
			<option value=S $opcSelectSim>Sim\n
			<option value=N $opcSelectNao>N�o\n
		</select>";
	}
	elseif($tipo=='check') {
		if($valor=='S') $texto="<span class=txtok>Sim</span>";
		elseif($valor=='N') $texto="<span class=txtcheck>N�o</span>";
	}
	
	return($texto);
	
}



#  Fun��o para mostrar form de sele�ao Sim/N�o
function formSelectStatus($valor, $campo, $tipo) {

	if($valor=='A') $opcSelectAtivo='selected';
	if($valor=='I') $opcSelectInativo='selected';
	if($valor=='C') $opcSelectCancelado='selected';
	if($valor=='N') $opcSelectNovo='selected';
	if($valor=='T') $opcSelectTrial='selected';
	
	if($tipo=='form') {
		$texto="<select name=matriz[$campo]>
			<option value=A $opcSelectAtivo>Ativado\n
			<option value=I $opcSelectInativo>Inativo\n
			<option value=C $opcSelectCancelado>Cancelado\n
			<option value=N $opcSelectNovo>Novo/Aguardando\n
			<option value=T $opcSelectNovo>Trial\n
		</select>";
	}
	elseif($tipo=='check') {
		if($valor=='A') $texto="<span class=txtok>Ativo</span>";
		elseif($valor=='I') $texto="<span class=txtaviso>Inativo</span>";
		elseif($valor=='C') $texto="<span class=txtaviso>Cancelado</span>";
		elseif($valor=='N') $texto="<span class=txtcheck>Novo/Aguardando</span>";
		elseif($valor=='T') $texto="<span class=txttrial>Per�odo de Trial</span>";
	}
	
	return($texto);
	
}


#  Fun��o para mostrar form de sele�ao Sim/N�o
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
		elseif($valor=='T') $texto="<span class=txttrial>Per�odo de Trial</span>";
	}
	
	return($texto);
	
}




#  Fun��o para mostrar form de sele�ao Sim/N�o
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





#  Fun��o para mostrar form de sele�ao Sim/N�o
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



#  Fun��o para mostrar form de sele�ao Sim/N�o
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


#  Fun��o para mostrar form de sele�ao Sim/N�o
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
		elseif($valor=='C') $texto="<span class=txtaviso>Cancelado</span>";
	}
	
	return($texto);
}


#  Fun��o para mostrar form de sele�ao Sim/N�o
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


#  Fun��o para mostrar form de sele�ao Sim/N�o
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




#  Fun��o para mostrar form de sele�ao Sim/N�o
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



# fun��o para sele��o de tipo de parametro
function formSelectTipoParametro($valor, $campo, $tipo) {

	if($tipo=='form') {
	
		if($valor=='sn') $opcSN='selected';
		if($valor=='nr') $opcNR='selected';
		
		$retorno="<select name=matriz[$campo] onChange='javascript:submit()'>
			<option value=sn $opcSN>Caixa de sele��o [Sim/N�o]\n
			<option value=nr $opcNR>Campo Num�rico\n
		</select>";
		#formul�rio
	}
	elseif($tipo=='check') {
		# visualiza��o
		if($valor=='sn') $retorno='Sele��o [Sim/N�o]';
		elseif($valor=='nr') $retorno='Num�rico';
	}
	
	return($retorno);
}




# fun��o para sele��o de tipo de parametro
function formSelectTipoTipoCobranca($valor, $campo, $tipo) {


	global $configCobranca;
	
	$keys=array_keys($configCobranca);
	
	if($tipo=='form') {

		$retorno="<select name=matriz[$campo]>";
		#formul�rio

		for($a=0;$a<count($keys);$a++) {
	
			if($valor==$keys[$a]) $opcSelect='selected';
			else $opcSelect='';
			
			$retorno.="<option value=$keys[$a] $opcSelect>".$configCobranca[$keys[$a]];
		}
		
		$retorno.="</select>";
		
		
	}
	elseif($tipo=='check') {
		# visualiza��o
		$retorno=$configCobranca[$valor];
	}
	
	return($retorno);
}



# fun��o para sele��o de tipo de parametro
function formSelectTipoFormaCobranca($valor, $campo, $tipo) {


	global $configFormaCobranca;
	
	$keys=array_keys($configFormaCobranca);
	
	if($tipo=='form') {

		$retorno="<select name=matriz[$campo]>";
		#formul�rio

		for($a=0;$a<count($keys);$a++) {
	
			if($valor==$keys[$a]) $opcSelect='selected';
			else $opcSelect='';
			
			$retorno.="<option value=$keys[$a] $opcSelect>".$configFormaCobranca[$keys[$a]];
		}
		
		$retorno.="</select>";
		
		
	}
	elseif($tipo=='check') {
		# visualiza��o
		$retorno=$configFormaCobranca[$valor];
	}
	
	return($retorno);
}



# Formul�rio para preenchimento/sele��o de valor de campo
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
			$retorno="ERRO ao consultar par�metro";
		}
	}
	elseif($tipo=='check') {
		
	}
	
	return($retorno);
}



# Formul�rio para preenchimento/sele��o de valor de campo
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
			$retorno="ERRO ao consultar par�metro";
		}
	}
	elseif($tipo=='check') {
		
	}
	
	return($retorno);
}



# Fomul�rio para sele��o de desconto
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



# Fomul�rio para sele��o de desconto
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



# Fomul�rio para sele��o de desconto
function formSelectTipoDesconto($desconto, $campo, $tipo) {

	global $configTipoDescontos;
	
	$keys=array_keys($configTipoDescontos);
	
	if($tipo=='form') {

		$retorno="<select name=matriz[$campo] onChange=form.submit();>";
		#formul�rio

		for($a=0;$a<count($keys);$a++) {
	
			if($desconto==$keys[$a]) $opcSelect='selected';
			else $opcSelect='';
			
			$retorno.="<option value=$keys[$a] $opcSelect>".$configTipoDescontos[$keys[$a]];
		}
		
		$retorno.="</select>";
		
		
	}
	elseif($tipo=='check') {
		# visualiza��o
		$retorno=$configTipoDescontos[$desconto];
	}
	
	return($retorno);
}



# Fomul�rio para sele��o de mes/ano refer�ncia
function formSelectMes($mes, $campo, $tipo) {

	global $configMeses;
	
	$data=dataSistema();
	
	$keys=array_keys($configMeses);
	
	if($tipo=='form') {

		$retorno="<select name=matriz[$campo]>";
		#formul�rio

		for($a=0;$a<count($keys);$a++) {

			if($mes==$keys[$a]) $opcSelect='selected';
			elseif(!$mes && $data[mes] == $keys[$a]) $opcSelect='selected';
			else $opcSelect='';
			
			$retorno.="<option value=$keys[$a] $opcSelect>".$configMeses[$keys[$a]];
		}
		
		$retorno.="</select>";
		
		
	}
	elseif($tipo=='check') {
		# visualiza��o
		$retorno=$configMeses[$mes];
	}
	
	return($retorno);
}



# Fomul�rio para sele��o de mes/ano refer�ncia
function formSelectAno($ano, $campo, $tipo) {

	$data=dataSistema();
	
	if($tipo=='form') {

		$retorno="<select name=matriz[$campo]>";
		#formul�rio

		for($a=($data[ano]-3);$a<($data[ano]+3);$a++) {
	
			if($ano==$a) $opcSelect='selected';
			elseif(!$ano && $data[ano]==$a) $opcSelect='selected';
			else $opcSelect='';
			
			$retorno.="<option value=$a $opcSelect> $a";
		}
		
		$retorno.="</select>";
		
		
	}
	elseif($tipo=='check') {
		# visualiza��o
		$retorno=$ano;
	}
	
	return($retorno);
}



# Fomul�rio para sele��o de dado da consultas
function formSelectConsulta($consulta, $campo_descricao, $campo_chave, $campo_form, $registro) {

	if($consulta && contaConsulta($consulta)>0) {
		$retorno="<select name=matriz[$campo_form]>";
	
		for($a=0;$a<contaConsulta($consulta);$a++) {
			# campos
			$id=resultadoSQL($consulta, $a, $campo_chave);
			$descricao=resultadoSQL($consulta, $a, $campo_descricao);
			# Listar registros
			
			if($id==$registro) $opcSelect='selected';
			else $opcSelect='';
			
			$retorno.="\n<option value=$id $opcSelect>$descricao";
			
		}
		
		$retorno.="</select>";
	}
	
	return($retorno);
}

?>
