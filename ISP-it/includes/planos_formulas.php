<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 31/07/2003
# Ultima alteração: 25/02/2004
#    Alteração No.: 019
#
# Função:
#    Painel - Funções para cadastro de pessoas Tipos


# calcular data de vencimento
function calculaVencimento($dtAtivacao, $diasTrial, $vencimento, $mes, $ano) {

	# Data atual do sistema
	$data=dataSistema();
	
	# Calcular o dia de vencimento do faturamento para o cliente
	$diaVencimento=$vencimento[diaFaturamento];

	# Verificar o mes de faturamento
	if(!$mes || !$ano) {
		if( ($data[dia] <= $diaVencimento) && ($data[mes] <= $mesVencimento) ) {
			$dtVencimento=mktime(0,0,0,$mesVencimento, $diaVencimento, $anoVencimento);
		}
		else {
			$dtVencimento=mktime(0,0,0,$mesVencimento+1, $diaVencimento, $anoVencimento);
		}
	}
	else {
		$mesVencimento=$mes;
		
		$anoVencimento=$ano;
		$dtVencimento=mktime(0,0,0,$mesVencimento, $diaVencimento, $anoVencimento);
	}
	
	return($dtVencimento);
}



# calcular data de vencimento
function calculaDataInicioServico($ativacao, $diasTrial, $vencimento) {

	$dtInicioBase=mktime(0,0,0,$ativacao[mes],$ativacao[dia]+$diasTrial,$ativacao[ano]);
	$dtVencimentoBase=mktime(0,0,0,$vencimento[mes],$vencimento[dia],$vencimento[ano]);
	
	$qtdeDias=intval(($dtVencimentoBase - $dtInicioBase)/60/60/24);
	
	# Quantidade de dias do mes de vencimento
	$qtdeDiasMes=dataDiasMes($vencimento[mes]);
	
	if($qtdeDias > $qtdeDiasMes) {
	
		if($data[mes] == $mesVencimento) {
			if($vencimento[diaVencimento] < $vencimento[diaFaturamento]) $mesBase=$vencimento[mes]-1;
			else $mesBase=$vencimento[mes]-1;
		}
		else {
		
		
			# Se dia de ativação do cliente, for maior do que o dia de faturamento
			# e o dia de ativação for maior do que o dia de vencimento base
			if($ativacao[dia] >= $vencimento[diaFaturamento]) {
			
				# Se diaAtivacao > diaFaturamento 
				if( $ativacao[dia] >= $vencimento[diaFaturamento] && date('m',$dtVencimentoBase) >= $vencimento[mes] ) {
					$mesBase=$vencimento[mes];
				}
				else $mesBase=$vencimento[mes]-1;  
			}
			else {
				# se diaAtivacao < diaFaturamento
				$mesBase=$vencimento[mes]-1;
			}
		}
		
		$diaBase=$vencimento[diaFaturamento];
	}
	else {
		# data de inicio deve ser calculada normalmente
		# utilizando data de ativação por base de data de inicio
		# Se o mes de vencimento for o mesmo do mes de faturamento, verificar dias referentes ao mes
		# anterior, onde o dia de vencimento é inferior ao dia de faturamento
		#
		# Verificar se dia de ativação < dia de faturamento 
		# se diaAtivacao < diaFaturamento -> diaBase = diaFaturamento
		# se diaAtivacao > diaFaturamento -> diaBase = diaAtivacao
		if($ativacao[dia] < $vencimento[diaFaturamento]) $diaBase=$vencimento[diaFaturamento];
		else $diaBase=$ativacao[dia];
		
		$mesBase=$ativacao[mes];
		
	}
	
	//echo "<br>DtInicioBase: ".date('d-m-Y',$dtInicioBase)." dtVencimentoBase: ".date('d-m-Y',$dtVencimentoBase)." QtdeDias: $qtdeDias - dia: $diaBase - mes: $mesBase<br>";
	
	$dtInicio=mktime(0,0,0,$mesBase,$diaBase,$vencimento[ano]);
	
	return($dtInicio);
}



# calcular data de vencimento
function calculaVencimentoCobranca($dtAtivacao, $diasTrial, $vencimento, $mes, $ano) {

	# Data atual do sistema
	$data=dataSistema();
	
	# Calcular o dia de vencimento do faturamento para o cliente
	$diaVencimento=$vencimento[diaVencimento];
	$diaFaturamento=$vencimento[diaFaturamento];

	# Verificar o mes de faturamento
	if(!$mes || !$ano) {
//		echo "mes e ano nao informados<br>";
		$mesVencimento=$mes;
		$anoVencimento=$ano;
		
		if( ($data[dia] <= $diaFaturamento) && ($data[mes] <= $mesVencimento) ) {
			$dtVencimento=mktime(0,0,0,$mesVencimento, $diaVencimento, $anoVencimento);
		}
		else {
			$dtVencimento=mktime(0,0,0,$mesVencimento, $diaVencimento, $anoVencimento);
		}
	}
	else {
		
		//echo "mes e anos informados<br>";
		
		$mesVencimento=$mes;
		$anoVencimento=$ano;
		
		if($vencimento[diaVencimento] < $vencimento[diaFaturamento]) {
			$mesVencimento+=1;
			
//			echo "teste";
			//echo "vencimento[diaVencimento] < vencimento[diaFaturamento] - $mesVencimento<br>";
		}
		
		else {
			
			//echo "else<br>";
			$mesVencimento;
		}
		
//		if ($mesIncrementado){
//			$dtVencimento= mktime(0,0,0,$mes,$diaVencimento,$ano);
//		}
//		else {
			$dtVencimento= mktime(0,0,0,$mesVencimento, $diaVencimento, $anoVencimento);	
//		}
		
	}
//	echo '<br>'.date("d-m-Y",$dtVencimento).'data de vencimento';
	return($dtVencimento);
}

/**
 * @desc Função para checar a data de vencimento de um cliente, e comparar o ultimo faturamento gerado
 * <B>dtVencimento</B> - Timestamp da data de vencimento
 * <B>idPessoaTipo</B> - ID da PessoaTipo para checagem de Contas a Receber na Data de Vencimento
 * @return dtVencimento (timestamp)
 * @param timestamp $dtVencimento
 * @param int $idPessoaTipo
*/
function calculaVencimentoCancelamentoPessoa($dtVencimento, $idPessoaTipo) {
	
	global $conn, $tb;
	
	$data=date("Y-m-d", $dtVencimento);
	
	$sql="
		SELECT 
			$tb[ContasReceber].id, 
			$tb[ContasReceber].valor, 
			$tb[ContasReceber].dtVencimento 
		FROM 
			$tb[ContasReceber], 
			$tb[DocumentosGerados] 
		WHERE 
			$tb[ContasReceber].idDocumentosGerados = $tb[DocumentosGerados].id 
			AND $tb[DocumentosGerados].idPessoaTipo = $idPessoaTipo
			AND $tb[ContasReceber].dtVencimento='$data'
	";
	
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
		# Encontrou lançamento em contas a receber para esta data
		# incrementar mes no vencimento
		$dia = date("d", $dtVencimento);
		$mes = date("m", $dtVencimento);
		$ano = date("Y", $dtVencimento);
		
		$dtVencimento = mktime(0,0,0,$mes+1,$dia,$ano);
		
	}
	
	//echo "Vencimento: " . date("d-m-Y", $dtVencimento);;

	return $dtVencimento;
	
}


function calculaValorProporcional($dtAtivacao, $diasTrial, $vencimento, $valor, $tipoCobranca) {

	$data=dataSistema();

	# Informações sobre data de vencimento do cliente
	$anoAtivacao=substr($dtAtivacao, 0, 4);
	$mesAtivacao=substr($dtAtivacao, 5, 2);
	$diaAtivacao=substr($dtAtivacao, 8, 2);
	
	$dtInicioCliente=mktime(0,0,0,$mesAtivacao, $diaAtivacao+$diasTrial, $anoAtivacao);
	
	if(!$vencimento[mes] || !$vencimento[ano]) {
		$dtVencimento=calculaVencimento($dtAtivacao, $diasTrial, $vencimento, $data[mes], $data[ano]);
	}
	else {
		$dtVencimento=calculaVencimento($dtAtivacao, $diasTrial, $vencimento, $vencimento[mes], $vencimento[ano]);
	}
	

	# Calcular qtde de dias para fazer cobrança
	if($dtVencimento > $dtInicioCliente ) {
		$qtdeDias=round(($dtVencimento - $dtInicioCliente)/60/60/24);
		
//		$qtdeDiasMes=dataDiasMes($mesVencimento);

		if ($tipoCobranca == 'pos'){
			$qtdeDiasMes = dataDiasMes( strtotime ( "-1 month ", $dtVencimento) );
		}
		else{
			$qtdeDiasMes = dataDiasMes( $mesVencimento );		
		}
//	
//		print "<br>ativacao:" .$dtAtivacao .
//			"<br>iniciacao" . date('Y-m-d', $dtInicioCliente) .
//			"<br>vencimentacao" .date('Y-m-d', $dtVencimento) ;	
//		print "<br> qtde dias:".$qtdeDiasMes;

		if($qtdeDias >= $qtdeDiasMes) {
			$obs='full';
			$retorno[valor]=round($valor,2);
			$retorno[dias]=$qtdeDias;
			$retorno[descricao]=$obs;
			$retorno[dtVencimento]=$dtVencimento;
		}
		else {
			$obs='proporcional';
			$retorno[valor]=round(($valor/$qtdeDiasMes)*$qtdeDias,2);
			$retorno[dias]=$qtdeDias;
			$retorno[descricao]="$obs $qtdeDias dias";
			$retorno[dtVencimento]=$dtVencimento;
		}
	}
	else {
		$obs='nao cobrar';
	}
	
	
	//echo "<span class=titulo>$obs: $qtdeDias - Venc: ".date('d-m-Y',$dtVencimento)." / Inicio: ".date('d-m-Y',$dtInicioCliente)." $retorno ";
	
	return($retorno);
}



function calculaValorProporcionalCancelamento($ativacao, $diasTrial, $vencimento, $valor) {

	$data=dataSistema();

	# Verificar data de ativação e Data de Cancelamento para obter diferença de
	# dias e verificar se data de ativação (base) deve ser utilizada de acordo
	# com o dia de faturamento, no caso da diferença entre DATA DE ATIVACAO e
	# DATA DE CANCELAMENTO for maior do que o NUMERO DE DIAS
	#
	# Algoritmo
	# QTDEDIAS=(DATA_CANCELAMENTO - DATA_ATIVACAO)
	# QTDEDIASMES=(MESCANCELAMENTO - 1)
	# SE(QTDEDIAS > QTDEDIASMES) 
	#    DIAINICIO=DIAFATURAMENTO


	# Verificar se data informada de cancelamento não é anterior ao faturamento
	# já gerado
	$dtAtivacao="$ativacao[ano]/$ativacao[mes]/$ativacao[dia]";
	
	# Calcular data de inicio do serviço do cliente
	$dtInicioCliente=calculaDataInicioServico($ativacao, $diasTrial, $vencimento);
	
	$dtVencimento=calculaVencimentoCobranca($dtAtivacao, $diasTrial, $vencimento, $vencimento[mes], $vencimento[ano]);
	
	$dtVencimentoCalculo=mktime(0,0,0,$vencimento[mes], $vencimento[dia], $vencimento[ano]);
	
	$qtdeDias=($dtVencimentoCalculo - $dtInicioCliente)/60/60/24;
	
	##alteracao, ticket EZ5227J , gustavo, 20050908
	if($vencimento['diaFaturamento'] == $vencimento['diaVencimento']){
//
//		echo "dias:" . $qtdeDias;
//		echo "<br>inicio:". date('d-m-Y', $dtInicioCliente);
//		echo "<br>vencimento inf.:". date('d-m-Y', $dtVencimentoCalculo);
//		echo "<br>vencimento final:". date('d-m-Y', $dtVencimento);
		
		$tmpDia=date('d',$dtVencimentoCalculo);
		$tmpMes=date('m',$dtVencimentoCalculo);
		$tmpAno=date('Y',$dtVencimentoCalculo);
		
		$parametros = buscaDadosParametro('diasFaturamento', 'parametro', 'igual', 'id');
		if (!is_numeric($parametros['valor']) || !$parametros['valor'] > 0)
				$parametros['valor'] = 15;

		$dtFaturamento = strtotime('-'.$parametros['valor']. 'days', $dtVencimento);
		
		#virada de mes.
		if (date('d', $dtFaturamento ) < $vencimento['diaFaturamento']){
			$tmpMes++;
		}
		
//		$dtFaturamento = mktime(0,0,0, $tmpMes, $vencimento['diaFaturamento']-$parametros['valor'], $tmpAno);
//		$dtVencimentoSistema = mktime(0,0,0, $tmpMes, $vencimento['diaVencimento'] , date('Y', $dtFaturamento));
			
//		echo "faturamento:".date('d-m-Y', $dtFaturamento) . "<br>data info.:".date('d-m-Y', $dtVencimentoCalculo)."<br>data vencimento sistema.:".date('d-m-Y', $dtVencimento); 
		
		if ( $tmpDia < date('d',$dtFaturamento) && $tmpDia > date('d',$dtVencimento) || 
			 (date('m',$dtFaturamento) == date('m',$dtVencimento)) && ( $tmpDia < date('d',$dtFaturamento) || $tmpDia > date('d',$dtVencimento) ) ) {
			
			$dtVencimento=mktime(0, 0, 0, $tmpMes, $vencimento[diaVencimento], $tmpAno);
			$qtdeDiasMes=dataDiasMes( mktime(0, 0, 0,$tmpMes-1, $vencimento[diaVencimento], $tmpAno));

			if ($qtdeDias > $qtdeDiasMes) {
				$dtInicioCliente=mktime(0,0,0,date('m',$dtInicioCliente)+1, date('d',$dtInicioCliente), $tmpAno);
				$qtdeDias=($dtVencimentoCalculo - $dtInicioCliente)/60/60/24;
			}
//			echo "cobranca gerada.:".$qtdeDias." cobrados";
					
		}
		else{
			#nao cobra, porem se deixar decorrer a funcao como se nao fosse dtFat==dtVenc vira uma salada so
			#entao ja retorno agora e ficamos livres de futuros problemas.
			return(0);
		}

	}
	#fim da alteracao
	
	# Utilizar mes anterior por base de quantidade de dias utilizados
	elseif($vencimento[dia] < $vencimento[diaFaturamento] && $vencimento[diaFaturamento] < $vencimento[diaVencimento]) {
		$tmpDia=date('d',$dtVencimentoCalculo);
		$tmpMes=date('m',$dtVencimentoCalculo);
		$tmpAno=date('Y',$dtVencimentoCalculo);
		$qtdeDiasMes=dataDiasMes( mktime(0,0,0,$tmpMes-1,$tmpDia, $tmpAno));
		
		//echo "dia < diaFaturamento e diaFaturamento < diaVencimento<br>";
		if($vencimento[mes] > $tmpMes=date('m',$dtInicioCliente) ) 
			$dtVencimento=mktime(0,0,0,date('m',$dtInicioCliente)+1,date('d',$dtInicioCliente),date('Y',$dtInicioCliente));
	}
	else {
		$tmpDia=date('d',$dtInicioCliente);
		$tmpMes=date('m',$dtInicioCliente);
		$tmpAno=date('Y',$dtInicioCliente);
		
		if($vencimento[dia] >= $vencimento[diaFaturamento]) {
			//echo "vencimento[dia] > vencimento[diaFaturamento]<br>";
			$qtdeDiasMes=dataDiasMes( mktime(0,0,0,$tmpMes+1,$tmpDia, $tmpAno) );
			
			if($vencimento[dia] == $vencimento[diaFaturamento]) {
				//echo "vencimento[dia] == vencimento[diaFaturamento]<br>";
				$tmpDia=date('d',$dtVencimentoCalculo);
				$tmpMes=date('m',$dtVencimentoCalculo);
				$tmpAno=date('Y',$dtVencimentoCalculo);
				$dtVencimento=mktime(0,0,0,date('m',$dtVencimento)+1,$vencimento[diaVencimento],date('Y',$dtVencimento));
				$qtdeDiasMes=dataDiasMes( mktime(0,0,0,$tmpMes,$tmpDia, $tmpAno) );
			}
			
		}
		else {
		
			//echo "else<br>";
			
			if($vencimento[dia] > $vencimento[diaVencimento] && date('m',$dtVencimento) == $data[mes]) {
				$tmpDia=date('d',$dtVencimentoCalculo);
				$tmpMes=date('m',$dtVencimentoCalculo);
				$tmpAno=date('Y',$dtVencimentoCalculo);
				$dtVencimento=mktime(0,0,0,date('m',$dtVencimento)+1,$vencimento[diaVencimento],date('Y',$dtVencimento));
			}
			
			$qtdeDiasMes=dataDiasMes($dtInicioCliente);
		}
	}
	
	if($qtdeDias > $qtdeDiasMes) {
	
		//echo "qtdeDias > qtdeDiasMes - $qtdeDias<br>";
		$dtInicioCliente=mktime(0,0,0,$tmpMes+1, $tmpDia, $tmpAno);
		$dtVencimentoCalculo=mktime(0,0,0,$vencimento[mes],$vencimento[dia],$vencimento[ano]);
		$qtdeDias=($dtVencimentoCalculo - $dtInicioCliente)/60/60/24;
		
		if($vencimento[dia] > $vencimento[diaFaturamento]) {
			$dtVencimento=mktime(0,0,0,date('m',$dtInicioCliente)+1,date('d',$dtInicioCliente),date('Y',$dtInicioCliente));
		}
		elseif($vencimento[dia] == $vencimento[diaFaturamento]) {
			$dtInicioCliente=mktime(0,0,0,$tmpMes-1, $tmpDia, $tmpAno);
			$dtVencimentoCalculo=mktime(0,0,0,$vencimento[mes],$vencimento[dia],$vencimento[ano]);
			$qtdeDias=($dtVencimentoCalculo - $dtInicioCliente)/60/60/24;
		}
		else {
			$dtVencimento=mktime(0,0,0,date('m',$dtInicioCliente),date('d',$dtInicioCliente),date('Y',$dtInicioCliente));
		}
	}

	
	//echo "Dia Vencimento: $vencimento[diaFaturamento] Mes: $mesVencimento dias: $qtdeDias dias mes: $qtdeDiasMes Inicio: ".date('d-m-Y',$dtInicioCliente)." Vencimento: ".date('d-m-Y',$dtVencimento)." Calculado: ".date('d-m-Y',$dtVencimentoCalculo);

	# Calcular qtde de dias para fazer cobrança
	if($dtVencimentoCalculo >= $dtInicioCliente ) {
		
		if($qtdeDias > $qtdeDiasMes) {
			$obs='full';
			$retorno[valor]=round($valor,2);
			$retorno[dias]=round($qtdeDias);
			$retorno[descricao]=$obs;
			$retorno[dtVencimento]=$dtVencimento;
		}
		else {
			$obs='proporcional';
			$retorno[valor]=round(($valor/$qtdeDiasMes)*$qtdeDias,2);
			$retorno[dias]=round($qtdeDias);
			$retorno[descricao]="$obs $qtdeDias dias";
			$retorno[dtVencimento]=$dtVencimento;
			//$retorno[dtVencimento]=calculaVencimento($dtAtivacao, $diasTrial, $vencimento, $vencimento[mes], $vencimento[ano]);
		}
	}
	else {
		$obs='nao cobrar';
	}
#	echo "<br>qtde dias: $qtdeDias <br>vecimento: ".date('d-m-Y',$dtVencimento);
	return($retorno);
}



# Calcular valor de servico nao proporcional
function calculaValorNaoProporcional($dtAtivacao, $diasTrial, $vencimento, $valor) {

	$data=dataSistema();

	# Informações sobre data de vencimento do cliente
	$anoAtivacao=substr($dtAtivacao, 0, 4);
	$mesAtivacao=substr($dtAtivacao, 5, 2);
	$diaAtivacao=substr($dtAtivacao, 8, 2);
	
	$dtInicioCliente=mktime(0,0,0,$mesAtivacao, $diaAtivacao+$diasTrial, $anoAtivacao);
	
	if(!$vencimento[mes] || !$vencimento[ano]) {
		$dtVencimento=calculaVencimento($dtAtivacao, $diasTrial, $vencimento, $data[mes], $data[ano]);
	}
	else {
		$dtVencimento=calculaVencimento($dtAtivacao, $diasTrial, $vencimento, $vencimento[mes], $vencimento[ano]);
	}
	
	# Calcular qtde de dias para fazer cobrança
	if($dtVencimento > $dtInicioCliente ) {
		$qtdeDias=($dtVencimento - $dtInicioCliente)/60/60/24;

		$qtdeDiasMes=dataDiasMes($mesVencimento);
		
		if(($qtdeDias+($qtdeDiasMes - $diasTrial)) >= $qtdeDiasMes) {
			$obs='full';
			$retorno[valor]=round($valor,2);
			$retorno[dias]=$qtdeDias;
			$retorno[descricao]=$obs;
			$retorno[dtVencimento]=$dtVencimento;
		}
		else {
			$obs='nao cobrar';
			$retorno[valor]=0;
			$retorno[dias]=$qtdeDias;
			$retorno[descricao]="$obs $qtdeDias dias";
			$retorno[dtVencimento]=$dtVencimento;
		}
	}
	else {
		$obs='nao cobrar';
		$retorno[valor]=0;
		$retorno[dias]=$qtdeDias;
		$retorno[descricao]=$obs;
		$retorno[dtVencimento]=$dtVencimento;
	}
	
	
	//echo "<span class=titulo>$obs: $qtdeDias - Venc: ".date('d-m-Y',$dtVencimento)." / Inicio: ".date('d-m-Y',$dtInicioCliente)." $retorno ";
	
	
	return($retorno);
}




# servicos adicionais
function calculaServicosAdicionais($idServicoPlano, $dtVencimento) {
	global $tb, $conn;
	
	$dtVencimento=date('Y-m-d', $dtVencimento);
	
	$sql="
		SELECT 
			$tb[ServicosAdicionais].nome, 
			$tb[ServicosAdicionais].valor, 
			$tb[ServicosAdicionais].dtCadastro, 
			$tb[ServicosAdicionais].dtVencimento, 
			$tb[ServicosAdicionais].dtEspecial, 
			$tb[ServicosAdicionais].status
		FROM 
			$tb[ServicosAdicionais]
		WHERE 
			idServicoPlano=$idServicoPlano 
			AND status='A' 
			AND dtVencimento='$dtVencimento'";
	
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
		# Totalizar servicos adicionais
		$retorno=0;
		for($a=0;$a<contaConsulta($consulta);$a++) {
			$nome=resultadoSQL($consulta, $a, 'nome');
			$dtCadastro=resultadoSQL($consulta, $a, 'dtCadastro');
			$dtVencimento=resultadoSQL($consulta, $a, 'dtVencimento');
			$dtEspecial=resultadoSQL($consulta, $a, 'dtEspecial');
			$status=resultadoSQL($consulta, $a, 'status');
			$valor=resultadoSQL($consulta, $a, 'valor');
			
			$retorno+=$valor;
			
			//echo "Serv. Adic.: $nome - $valor - $dtVencimento<br>";
		}
	}
	
	return($retorno);
}


# descontos
function calculaDescontos($idServicoPlano, $dtVencimento,$mesIncrementado="") {
	global $tb,$conn;
	
	$dtVencimento=date('Y-m-d', $dtVencimento);
	/*correção do faturamento*/
	if ($mesIncrementado){
		$mes= explode("-",$dtVencimento);
		$mesAnterior= $mes[1]-1;
		$dtVencimento= $mes[0].'-'.$mesAnterior.'-'.$mes[2];
	}
	$sql="
		SELECT 
			$tb[DescontosServicosPlanos].descricao, 
			$tb[DescontosServicosPlanos].dtDesconto, 
			$tb[DescontosServicosPlanos].valor, 
			$tb[DescontosServicosPlanos].status  
		FROM 
			$tb[DescontosServicosPlanos]
		WHERE 
			idServicoPlano=$idServicoPlano 
			AND status='A' 
			AND dtDesconto='$dtVencimento'";
	
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
		# Totalizar servicos adicionais
		$retorno=0;
		for($a=0;$a<contaConsulta($consulta);$a++) {
			$descricao=resultadoSQL($consulta, $a, 'descricao');
			$dtCobranca=resultadoSQL($consulta, $a, 'dtDesconto');
			$status=resultadoSQL($consulta, $a, 'status');
			$valor=resultadoSQL($consulta, $a, 'valor');
			
			if($status=='A') $retorno+=$valor;
			
			//echo "Desconto: $descricao - $valor ";
		}
	}
	
	return($retorno);
}




# Função para calculo de parcelas - retornar matriz
function calculaParcelas($valor, $parcelas, $vencimento, $dtVencimento, $dtEspecial) {

	$valor=formatarValores($valor);
	//$dtVencimento=formatarData($dtVencimento);

	# Data atual do sistema
	$data=dataSistema();
	# Calcular o dia de vencimento do faturamento para o cliente
	$tmpVencimento=dadosVencimento($vencimento);
	$diaVencimento=$tmpVencimento[diaVencimento];
	$mesVencimento=substr($dtVencimento,0,2);
	$anoVencimento=substr($dtVencimento,2,4);
		
	if($valor>0) {
		# Calcular
		for($a=1;$a<=$parcelas;$a++) {
			# Montar Parcelas
			$retorno[$a][valor]=round($valor/$parcelas,2);
			if($a == 1) {
				$retorno[$a][data]=date('d-m-Y',mktime(0,0,0,$mesVencimento, $diaVencimento, $anoVencimento));
			}
			else {
				$retorno[$a][data]=date('d-m-Y',mktime(0,0,0,$mesVencimento+($a)-1, $diaVencimento, $anoVencimento));
			}
			
			$total+=$retorno[$a][valor];
		}
		
		$retorno[$a-1][valor]+=($valor - $total);
	}

	return($retorno);

}

# Função para calculo de parcelas - retornar matriz
function calculaParcelasContasPagar($valor, $parcelas, $dtVencimento) {

	$valor=formatarValores($valor);
	//$dtVencimento=formatarData($dtVencimento);

	# Data atual do sistema
	$data=dataSistema();
	# Calcular o dia de vencimento das Contas a Pagar
	
	$diaVencimento=substr($dtVencimento,0,2);
	$mesVencimento=substr($dtVencimento,3,2);
	$anoVencimento=substr($dtVencimento,6,8);
	
	if($valor>0) {
		# Calcular
		for($a=1;$a<=$parcelas;$a++) {
			# Montar Parcelas
			$retorno[$a][valor]=round($valor/$parcelas,2);
			if($a == 1) {
				$retorno[$a][data]=date('d-m-Y',mktime(0,0,0,$mesVencimento, $diaVencimento, $anoVencimento));
			}
			else {
				$retorno[$a][data]=date('d-m-Y',mktime(0,0,0,$mesVencimento+($a)-1, $diaVencimento, $anoVencimento));
			}
			
			$total+=$retorno[$a][valor];
		}
		
		$retorno[$a-1][valor]+=($valor - $total);
	}

	return($retorno);

}


function calculaValorServicoPeriodico($idServicoPlano, $dtAtivacao, $diasTrial, $vencimento, $valor, $formaCobranca) {

	$data=dataSistema();

	# Informações sobre data de vencimento do cliente
	$anoAtivacao=intval(substr($dtAtivacao, 0, 4));
	$mesAtivacao=intval(substr($dtAtivacao, 5, 2));
	$diaAtivacao=intval(substr($dtAtivacao, 8, 2));
	
	
	//if( !checkLancamentoCobrancaServico($idServicoPlano, $vencimento[mes]) ) {
	
		if($formaCobranca=='mensal') {
			# Cobrar valor full
			
			$dtInicioCliente=mktime(0,0,0,$mesAtivacao, $diaAtivacao+$diasTrial, $anoAtivacao);
			
			if(!$vencimento[mes] || !$vencimento[ano]) {
				$dtVencimento=calculaVencimento($dtAtivacao, $diasTrial, $vencimento, $data[mes], $data[ano]);
			}
			else {
				$dtVencimento=calculaVencimento($dtAtivacao, $diasTrial, $vencimento, $vencimento[mes], $vencimento[ano]);
			}
			
			# Calcular qtde de dias para fazer cobrança
			if($dtVencimento > $dtInicioCliente ) {
				$qtdeDias=($dtVencimento - $dtInicioCliente)/60/60/24;
				
				$qtdeDiasMes=dataDiasMes($mesVencimento);
				
				if($qtdeDias >= $qtdeDiasMes) {
					$obs='full';
					$retorno[valor]=$valor;
					$retorno[dias]=$qtdeDias;
					$retorno[descricao]=$obs;
					$retorno[dtVencimento]=$dtVencimento;
				}
				else {
					$obs='proporcional';
					$retorno[valor]=($valor/$qtdeDiasMes)*$qtdeDias;
					$retorno[dias]=$qtdeDias;
					$retorno[descricao]="$obs $qtdeDias dias";
					$retorno[dtVencimento]=$dtVencimento;
				}
			}
			else {
				$obs='nao cobrar';
			}
		}
		elseif($formaCobranca=='anual') {
		
			$dtInicioCliente=mktime(0,0,0, $mesAtivacao, $diaAtivacao, $anoAtivacao);
			
			if(!$vencimento[mes] || !$vencimento[ano]) {
				$dtVencimento=calculaVencimento($dtAtivacao, $diasTrial, $vencimento, $data[mes], $data[ano]);
			}
			else {
				$dtVencimento=calculaVencimento($dtAtivacao, $diasTrial, $vencimento, $vencimento[mes], $vencimento[ano]);
			}
			
			$tmp1=date('d-m-Y',$dtAtivacao);
			$tmp2=date('d-m-Y',$dtVencimento);
			//echo "Ativacao: $dtInicioCliente $tmp1 - Vencimento: $dtVencimento $tmp2";
			
			# Calcular qtde de dias para fazer cobrança
			if($dtVencimento > $dtInicioCliente && ( ( $mesAtivacao<12 && $vencimento[mes] == $mesAtivacao+1) OR ($mesAtivacao==12 && $vencimento[mes] == 1)) ) {
				# Cobrar valor full
				$obs='full';
				$retorno[valor]=$valor;
				$retorno[dias]=$qtdeDias;
				$retorno[descricao]=$obs;
				$retorno[dtVencimento]=$dtVencimento;
			}
		}
		
		elseif($formaCobranca=='semestral') {
		
			$dtInicioCliente=mktime(0,0,0, $mesAtivacao, $diaAtivacao, $anoAtivacao);
			
			if(!$vencimento[mes] || !$vencimento[ano]) {
				$dtVencimento=calculaVencimento($dtAtivacao, $diasTrial, $vencimento, $data[mes], $data[ano]);
			}
			else {
				$dtVencimento=calculaVencimento($dtAtivacao, $diasTrial, $vencimento, $vencimento[mes], $vencimento[ano]);
			}
			
			$tmp1=date('d-m-Y',$dtAtivacao);
			$tmp2=date('d-m-Y',$dtVencimento);
			//echo "Ativacao: $dtInicioCliente $tmp1 - Vencimento: $dtVencimento $tmp2";
			
			# Calcular qtde de dias para fazer cobrança
			if($dtVencimento > $dtInicioCliente && ( ( $mesAtivacao<12 && ($vencimento[mes]%6 == ($mesAtivacao+1)%6 )) OR ($mesAtivacao==12 && $vencimento[mes]%6 == 1)) ) {
				# Cobrar valor full
				$obs='full';
				$retorno[valor]=$valor;
				$retorno[dias]=$qtdeDias;
				$retorno[descricao]=$obs;
				$retorno[dtVencimento]=$dtVencimento;
			}
		}
		
				
		//adicionado 
		elseif($formaCobranca=='trimestral') {
		
			$dtInicioCliente=mktime(0,0,0, $mesAtivacao, $diaAtivacao, $anoAtivacao);
			
			if(!$vencimento[mes] || !$vencimento[ano]) {
				$dtVencimento=calculaVencimento($dtAtivacao, $diasTrial, $vencimento, $data[mes], $data[ano]);
			}
			else {
				$dtVencimento=calculaVencimento($dtAtivacao, $diasTrial, $vencimento, $vencimento[mes], $vencimento[ano]);
			}
			
			if ( $vencimento['diaVencimento'] < $diaAtivacao ){
				$mesAtivacao = ( $mesAtivacao < 12 ? $mesAtivacao + 1 : 1);
			}
			$tmp1=date('d-m-Y',$dtAtivacao);
			$tmp2=date('d-m-Y',$dtVencimento);
			//echo "Ativacao: $dtInicioCliente $tmp1 - Vencimento: $dtVencimento $tmp2";
			
			# Calcular qtde de dias para fazer cobrança
			
			//caso ainda nao tenha feito um mes de ou esteje em seu divisor... faz a cobranca.
			if($dtVencimento > $dtInicioCliente && 
					( $vencimento[mes]%3 == $mesAtivacao%3  ) ) {
				# Cobrar valor full
				$obs='full';
				$retorno[valor]=$valor;
				$retorno[dias]=$qtdeDias;
				$retorno[descricao]=$obs;
				$retorno[dtVencimento]=$dtVencimento; 
			}
		}
		
		
		//echo "<span class=titulo>$obs: $qtdeDias - Venc: ".date('d-m-Y',$dtVencimento)." / Inicio: ".date('d-m-Y',$dtInicioCliente)." $retorno ";
		
		return($retorno);
	//}
}


function verificarVencimento2($idVencimento, $dia, $mes, $ano) {

	$data=dataSistema();
	
	$flag=0;
	
	$vencimento=dadosVencimento($idVencimento);
	
	if(!$idVencimento || !$mes || !$ano) $flag=1;
	elseif(!$dia) {
		
		if($mes < $data[mes]){
			$flag=1;
		}
		elseif($data[ano] > $ano) {
			$flag=1;
		}
		elseif($data[mes] > $mes && $data[ano] >= $ano) {
			$flag=1;
		}
		elseif($data[dia] > $vencimento[diaFaturamento] && $data[mes] > $mes && $data[ano] && $data[ano] >= $ano) {
			$flag=1;
		}
		elseif($data[dia] > $vencimento[diaFaturamento]) {
			# Verificar o mes de vencimento
			$dtMinima=mktime(0,0,0,$data[mes], $vencimento[diaFaturamento],$data[ano]);
			$dtCliente=mktime(0,0,0,$mes+1,$vencimento[diaFaturamento], $data[ano]);
			
			if($data[mes] >= $mes && $data[ano] >= $ano) $flag=1;
			//elseif($dtMinima > $dtCliente) $flag=1;
		}
		
	}
	
	return($flag);

}


# Funçao para validação de data de Vencimento
function verificarVencimento($idVencimento, $dia, $mes, $ano) {

	$data=dataSistema();
//	$data[mes]= "02";
	$flag=0;
	
	$vencimento=dadosVencimento($idVencimento);
	
	if(!$idVencimento || !$mes || !$ano) $flag=1;
	
	    elseif(!$dia) {
		
//        if($mes < $data[mes]){
//			$flag=1;
//		}
	    if($data[ano] > $ano) {
			$flag=1;
		}
		elseif($data[mes] > $mes && $data[ano] >= $ano) {
			$flag=1;
		}
		elseif($data[mes] < $mes && $data[ano] >= $ano) {
			$flag=1;
		}
		elseif($data[mes] >= $mes && $data[ano] >= $ano) {
			$flag=1;
		}
		elseif($data[dia] > $vencimento[diaFaturamento] && $data[mes] > $mes && $data[ano] && $data[ano] >= $ano) {
			$flag=1;
		}
	
		elseif($data[dia] > $vencimento[diaFaturamento]) {
			# Verificar o mes de vencimento
			$dtMinima=mktime(0,0,0,$data[mes], $vencimento[diaFaturamento],$data[ano]);
			$dtCliente=mktime(0,0,0,$mes+1,$vencimento[diaFaturamento], $data[ano]);
			
			if($data[mes] >= $mes && $data[ano] >= $ano) $flag=1;
			//elseif($dtMinima > $dtCliente) $flag=1;
		}
	}

	return($flag);

}

?>
