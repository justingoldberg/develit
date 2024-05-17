<?

//function FluxoDeCaixaPrincipal( $modulo, $sub, $acao, $registro, $matriz ){
//
//	formFluxoDeCaixa( $modulo, $sub, $acao, $registro, $matriz );
//	
//	if ( $matriz['bntConfirmar'] || $matriz['bntRelatorio'] ) {
//
//		if( $rel = fluxoDeCaixaBuscaPeriodo( $matriz ) ) {
//						
//			if( $matriz['bntConfirmar'] ) {
//				fluxoDeCaixaListagem( $rel );
//			}
////			else {
////				contasAPagarCentroDeCustoRelatorio( $rel );
////			}
//
//		}
//		
//	}
//	
//}
//
///**
// * Exibe o formulário para gerar a listagem de Fluxo de caixa
// *
// * @param string  $modulo
// * @param string  $sub
// * @param string  $acao
// * @param integer $registro
// * @param array   $matriz
// */
//function formFluxoDeCaixa( $modulo, $sub, $acao, $registro, $matriz ){
//	global $corFundo, $corBorda, $sessLogin;
//	
//	novaTabela2("[Listagem de Fluxo de Caixa]", "center", "100%", 0, 2, 1, $corFundo, $corBorda, 2);
//		
//		#cabecalho com campos hidden
//		abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );
//
//		#pop de acesso
//		$combo = formSelectPOP($matriz['pop'],'pop','multi')."<input type=checkbox name=matriz[pop_todos] value=S $opcPOP><b>Todos</b>";
//		getCampo( "combo", "<b>POP:</b><br><span class=normal10>Selecione a POP de Acesso</span>", "", $combo );
//		
//		#periodo do relatorio
//		getPeriodoUnico( 6, $matriz, "Vencimento" );
//				
//		getBotoesConsRel();
//		
//		fechaFormulario();
//	
//	fechaTabela();
//	
//}
//
//// Seleciona o periodo
////function fluxoDeCaixaBuscaPeriodo( $mes = '', $ano = '' ){
////	$data = dataSistema();
////	
////	if( !$mes || ( ( $mes < 0 ) || $mes > 12 ) ){
////		$mes = $data['mes'];
////	}
////	
////	if( !$ano || ( ( $ano < 1970 ) || $ano > 2100 ) ){
////		$ano = $data['ano'];
////	}
////	
////	$condicao = " data between '".$ano."-".$mes."-01 00:00:00' and '".$ano."-".$mes."-31 23:59:59'";
////	
////	$fluxo = dbFluxoDeCaixa( '', 'consultar', '', $condicao );
////	
////	/*
////	
////	reservado para colocar o atributo que ira conter os valores do resultado parcial.
////	
////	*/
////	
////	return( $fluxo );
////}
//
//
//
//
//function fluxoDeCaixaBuscaPeriodo( $matriz ){
//	global $conn, $tb;
//	
//	$data = dataSistema();
//	
//	$where = array();
//	
//	if( $matriz['dtInicial'] ){
//		$dtInicial	=	formatarData( $matriz['dtInicial'] );
//		
//		if( $dtInicial ){
//			$cond  = " data between '".substr( $dtInicial,2, 4 )."-".substr( $dtInicial, 0, 2 ). "-01 00:00:00' ";
//			$cond .= "AND '" . substr( $dtInicial,2, 4 )."-".substr( $dtInicial, 0, 2 ). "-31 23:59:59' ";
//			$where[] = $cond ;
//		}
//		
//	}
//	
//	# Montar 
//	if(!$matriz[pop_todos] && $matriz[pop]) {
//		$i=0;
//		$sqlADDPOP="$tb[POP].id in (";
//		while($matriz[pop][$i]) {
//			
//			$sqlADDPOP.="'".$matriz[pop][$i]."'";
//			
//			if($matriz[pop][$i+1]) $sqlADDPOP.=",";
//			$i++;
//		}
//		$sqlADDPOP.=")";
//		
//		$consultaPOP=buscaPOP($sqlADDPOP, '','custom','nome');
//		
//	}
//	elseif($matriz[pop_todos]) {
//		# consultar todos os POP
//		$consultaPOP=buscaPOP('','','todos','nome');
//	}
//	
//	$l = 0;	
//	
//	if( $consultaPOP && contaConsulta( $consultaPOP ) ) {
//		
//		for( $p=0; $p<contaConsulta( $consultaPOP ); $p++ ){
//	
//			$idPOP = resultadoSQL( $consultaPOP, $p, 'id' );
//			
//			if( !$mes || ( ( $mes < 0 ) || $mes > 12 ) ){
//				$mes = $data['mes'];
//			}
//			
//			if( !$ano || ( ( $ano < 1970 ) || $ano > 2100 ) ){
//				$ano = $data['ano'];
//			}
//			
//			$condicao  = " data between '".$ano."-".$mes."-01 00:00:00' and '".$ano."-".$mes."-31 23:59:59'";
//			$condicao .= " AND idPop='".$idPop."'";
//			
//			$fluxo = dbFluxoDeCaixa( '', 'consultar', '', $condicao );
//			
//			
//			
//			/*
//			
//			reservado para colocar o atributo que ira conter os valores do resultado parcial.
//			
//			*/	
//	
//
//		}
//	
//	}
//	
//
//	
//
//	
//	return( $fluxo );
//}
//
//function fluxoDeCaixaListagem( $relatorio ){
//	global $corFundo, $corBorda, $sessLogin;
//
//	# Configuração da listagem
//	$cabecalho		= array( "Doc",		"Descrição",	"Data",		"Crédito",	"Debito");
//	$alinhamento	= array( "center",	"left",			"center",	"right",	"right"	);
//	$largura		= array( "9%",		"50%",			"9%",		"16%",		"16%"	);
//	
//	if( count( $relatorio ) ) {
//		
//		# Abre a tabela do relatorio
//		novaTabela( "[RELATÓRIO DE FLUXO DE CAIXA]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, count($largura) );
//
//		# Exibe a linha de Cabeçalho
//		htmlAbreLinha( $corFundo );
//		for( $i = 0; $i < count( $cabecalho ); $i++ ) {
//			itemLinhaNOURL( $cabecalho[$i], "center", $corFundo, 0, 'tabfundo0' );
//		}
//		htmlFechaLinha();
//		
//		# Inicia o total de debito e credito
//		$ttCredito	= 0;
//		$ttDebito	= 0;
//		
//		#Exibe a listagem dos dados
//		foreach( $relatorio as $linha ) {
//			
//			if( $linha->tipo == fluxoDeCaixaGetTipoCredito() || $linha->tipo == fluxoDeCaixaGetTipoDebito() ){
//				#Verifica se é debito ou credito, e calcula o total de cada um
//				if( $linha->tipo == fluxoDeCaixaGetTipoCredito() ) {
//					$ttCredito += $linha->valor;
//					$credito	= number_format( $linha->valor, 2, ",", "." );
//					$debito		= "0,00";
//					$doc	 	= "<a href=\"#\" onclick=\"window.open('?modulo=faturamento&sub=clientes&acao=dados_cobranca&registro=".$linha->idConta.
//								  "', 'ver', 'width=600,height=400,scrollbars=yes')\">".$linha->idConta."</a>";
//
//				}
//				if( $linha->tipo == fluxoDeCaixaGetTipoDebito() ) {
//					$ttDebito += $linha->valor;
//					$credito	= "0,00";
//					$debito		= number_format( $linha->valor, 2, ",", "." );
//					$doc	 	= "<a href=\"#\" onclick=\"window.open('?modulo=contas_a_pagar&sub=&acao=info&registro=".$linha->idConta.
//								  "', 'ver', 'width=600,height=400,scrollbars=yes')\">".$linha->idConta."</a>";
//				}
//				
//				$c = 0;
//				htmlAbreLinha( $corFundo );
//				itemLinhaNOURL( $doc,	$alinhamento[$c++], $corBorda, 0, 'normal10' );
//				itemLinhaNOURL( $linha->descricao,	$alinhamento[$c++], $corBorda, 0, 'normal10' );
//				itemLinhaNOURL( converteData($linha->data, "banco", "formdata" ),		$alinhamento[$c++], $corBorda, 0, 'normal10' );
//				itemLinhaNOURL( $credito,			$alinhamento[$c++], $corBorda, 0, 'normal10' );
//				itemLinhaNOURL( $debito,			$alinhamento[$c++], $corBorda, 0, 'normal10' );
//				htmlFechaLinha();
//			}
//		}
//		# Exibe o total
//		$c = 3;
//		htmlAbreLinha( $corFundo );
//		itemLinhaNOURL( "Total",									"right", $corBorda, 3, 'bold10' );
//		itemLinhaNOURL( number_format( $ttCredito, 2, ",", "." ),	$alinhamento[$c++], $corBorda, 0, 'bold10' );
//		itemLinhaNOURL( number_format( $ttDebito,  2, ",", "." ),	$alinhamento[$c++], $corBorda, 0, 'bold10' );
//		htmlFechaLinha();
//		
//		fechaTabela();
//		
//	}
//	
//}
//
//function fluxoDeCaixaGeraPDF( $relatorio ) {
//	
//}

?>