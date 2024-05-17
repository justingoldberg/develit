<?php
//include "planos_contas.php";

/**
 * Construtor do módulo
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function contasAPagar( $modulo, $sub, $acao, $registro, $matriz ){
 
	global $corFundo, $corBorda, $sessLogin;
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin['login'],'login','igual','login');
	
	if(!$permissao['admin']) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		// sistema de permissao diferente. por funcao ao inves de modulo.
		$titulo = "<b>Contas à Pagar</b>";
		//$subtitulo = "<b> das Agências Bancárias</b>" ;
		$itens = Array('Adicionar', 'Procurar', 'Listar');
		getHomeModulo($modulo, $sub, $titulo, $subtitulo, $itens);
		
		echo "<br>";
		switch ($acao) {
			case "adicionar":
				contasAPagarAdicionar($modulo, $sub, $acao, $registro, $matriz);
				break;
			case "cancelar":
				contasAPagarCancelar($modulo, $sub, $acao, $registro, $matriz);
				break;
			case "info":
				contasAPagarVer($modulo, $sub, $acao, $registro, $matriz);
				break;
			case "baixar":
				contasAPagarBaixar($modulo, $sub, $acao, $registro, $matriz);
				break;
			default:
				contasAPagarListar($modulo, $sub, $acao, $registro, $matriz);
				break;
		}
	}
}

/**
 * Gerencia a Consulta/manipulação dos dados da  contas à pagar
 *
 * @param array   $matriz
 * @param unknown $tipo
 * @param unknown $subTipo
 * @param unknown $condicao
 * @param unknown $tipoData
 * @return unknown
 */
function dbContasAPagar($matriz, $tipo, $subTipo='', $condicao='', $tipoData='' ) {
	global $conn, $tb, $modulo, $sub, $acao;
	$data = dataSistema();
	
	$bd = new BDIT();
	$bd->setConnection($conn);
	
	$tabelas = $tb['ContasAPagar'];

	$campos = Array('id',	'idFornecedor',				'idPlanoDeContasDetalhes',			'idPop',			
					'valor',			'dtCadastro',		'dtVencimento',		'obs',			'status',	
					'idNFE' );
	
	if ($tipo == 'incluir'){


		$valores= Array("NULL",	$matriz['idPessoaTipo'],	$matriz['idPlanoDeContasDetalhes'],	$matriz['idPop'],	
						$matriz['valor'],	$data['dataBanco'],	$matriz['data'],	$matriz['obs'],	'P', 
						$matriz['idNFE'] );
		
		$retorno = $bd->inserir($tabelas, $campos, $valores);
	}
	
	if($tipo == 'consultar'){
		$tabelas = array (	$tb['ContasAPagar'] .
							' INNER JOIN ' . $tb['PessoasTipos'] .
				          	'	On ('.$tb['ContasAPagar'].'.idFornecedor='.$tb['PessoasTipos'].'.id) ' .
				          	' INNER JOIN '. $tb['Pessoas'] .
				          	'	On ('. $tb['PessoasTipos'] .'.idPessoa = '.$tb['Pessoas'].'.id) '.
				          	' INNER JOIN '.$tb['PlanoDeContasDetalhes'].
				          	'   ON ('.$tb['ContasAPagar'].'.idPlanoDeContasDetalhes = '.$tb['PlanoDeContasDetalhes'].'.id) '.
				          	' INNER JOIN '. $tb['PlanoDeContasSub'] .
				          	'   ON ('.$tb['PlanoDeContasDetalhes'].'.idPlanoDeContasSub = '.$tb['PlanoDeContasSub'].'.id) '.
				          	' INNER JOIN '. $tb['PlanoDeContas'] .
				          	'   ON ('.$tb['PlanoDeContasSub'] .'.idPlanoDeContas = '.$tb['PlanoDeContas'] .'.id) '
					     );
		
		if($subTipo == 'unico'){
			$campos = array($tb['Pessoas'].'.nome nomePessoa', $tb['ContasAPagar'].'.idPop', 
							'CONCAT( '.$tb['PlanoDeContas'].'.nome, " -> ", '.$tb['PlanoDeContasSub'].'.nome, " -> ", '.$tb['PlanoDeContasDetalhes'].'.nome) as planoDeContas', 
							$tb['ContasAPagar'].'.obs', $tb['ContasAPagar'].'.dtCadastro', $tb['ContasAPagar'].'.dtVencimento', 
							$tb['ContasAPagar'].'.dtBaixa', $tb['ContasAPagar'].'.valor', $tb['ContasAPagar'].'.valorPago', 
							$tb['ContasAPagar'].'.status', $tb['ContasAPagar'].'.id');
		
			$condicao = array($tb['ContasAPagar'].'.id="' . $matriz['id'] .'"');
		}
		elseif( $subTipo == 'unicoBasico' ) {
			$tabelas =  $tb['ContasAPagar'];
			//$campos = Array('id',	'idFornecedor',				'idPlanoDeContasDetalhes',			'idPop',			'valor',			'dtCadastro',		'dtVencimento',		'obs',			'status');
			$condicao = array($tb['ContasAPagar'].'.id="' . $matriz['id'] .'"');
		}
		else{

			$campos = array($tb['Pessoas'].'.nome nomePessoa', 'CONCAT( '.$tb['PlanoDeContas'].'.nome, " -> ", '.$tb['PlanoDeContasSub'].'.nome, " -> ", '.$tb['PlanoDeContasDetalhes'].'.nome) as planoDeContas', 
							$tb['ContasAPagar'].'.'.$tipoData,$tb['ContasAPagar'].'.valor', $tb['ContasAPagar'].'.status', 
							$tb['ContasAPagar'].'.id');
			$ordem = ( ($tipoData != "") ? array( $tipoData ) : "" );
		}

		$retorno = $bd->seleciona($tabelas, $campos, $condicao, '', $ordem);
		
	}
	if ($tipo == 'alterar'){
		$condicao = 'id='.$matriz['id'];
		if ($subTipo == 'baixar'){
			$campos= Array('valorPago', 'dtBaixa', 'obs', 'status');
			$valores= Array($matriz['valorPago'], $matriz['dtBaixa'], $matriz['obs'], 'B');
		}
		elseif ( $subTipo == 'cancelar'){
			$campos= 'status';
			$valores= 'C';
		}
		elseif( $subTipo == 'parcela' ) {
			$campos = array( 'dtVencimento', 'valor' );
			$valores = array( $matriz['data'], $matriz['valor'] );
		}
		$retorno = $bd->alterar($tabelas, $campos, $valores, $condicao);
	}
	if( $tipo == 'excluir' ){
		$retorno = $bd->excluir( $tabelas, $condicao );
	}
	
	return ($retorno);	
}


/**
 * Insere o formulário do contas a pagar
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function formContasAPagar ($modulo, $sub, $acao, $registro, $matriz) {
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	$data=dataSistema();
	
	switch ( $acao ) {
		case "baixar":
			$labels['valor'] = "Valor Pago";
			$labels['data']  = "Data do Pagamento";
			$c = 7;
			break;
	
		default:
			$labels['valor'] = "Valor";
			$labels['data']  = "Data de Vencimento";
			$c = 9;
			break;
	}
	
	novaTabela2("[ Contas à Pagar - Cadastrar ]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	abreFormularioComCabecalho( $modulo, $sub, $acao, $registro, array("matriz[idPessoaTipo]"), array($matriz['idPessoaTipo']) ) ;
	htmlAbreLinha( "#EEEEEE" );
	$opcoes = htmlMontaOpcao("<a href=\"?modulo=cadastros&sub=fornecedores&acao=novo\" target=\"_blank\">Adicionar</a>",'incluir');
	itemLinhaNOURL( $opcoes, 'right', "#EEEEEE", 2, 'normal10' );
	htmlFechaLinha();
	#primeiro busca o fornecedor
	if ( !$matriz['idPessoaTipo'] && $acao == 'adicionar' ){
		$tipoPessoa 		  = checkTipoPessoa( 'for' );
		$matriz['idTipoPessoa'] = $tipoPessoa['id'];
		procurarPessoasSelect( $modulo, $sub, $acao, $registro, $matriz );
	}
	else {	
		$pessoa = dadosPessoasTipos( $matriz["idPessoaTipo"]);
		getCampo("combo", "Fornecedor", "", $pessoa["pessoa"]["nome"]);
		
		$evento = "onchange=\"javascript:form.submit();\"";
		
		#plano de conta pai
		$lista = dbPlanoDeContas( "", "consultar", "",  "status='A'" );
		$combo = getSelectObjetos( "matriz[idPlanoDeContas]", $lista , "nome", "id", $matriz["idPlanoDeContas"], $evento, true, "[Nenhum Plano de Contas Cadastrado]" );
		getCampo( 'combo', "Plano de Conta", '',  $combo );
		
		#plano de conta sub
		if( $matriz["idPlanoDeContas"] ){
						
			$lista = dbPlanoDeContasSub("", "consultar", "", "idPlanoDeContas='".$matriz["idPlanoDeContas"]."' and status='A'");
			$combo = getSelectObjetos( "matriz[idPlanoDeContasSub]", $lista, "nome", "id", $matriz["idPlanoDeContasSub"], $evento, true );
			getCampo('combo', "Sub-Plano de Conta", '', $combo, '');
			
			#Verifica se foi selecionado sub-plano
			if( !strpos( $combo, 'selected' ) ){
				$matriz["idPlanoDeContasSub"] = 0;
			}
			#plano de conta filho
			if( $matriz["idPlanoDeContasSub"] ){
				$lista = dbPlanoDeContasDetalhes("", "consultar", "", "idPlanoDeContasSub='".$matriz["idPlanoDeContasSub"]."'  and status='A'");
				$combo = getSelectObjetos( "matriz[idPlanoDeContasDetalhes]", $lista, "nome", "id", $matriz["idPlanoDeContasDetalhes"], "", true );
				getCampo('combo', "Detalhe do Plano de Conta", '', $combo, '');
					
				getCampo('combo', "POP", '',formSelectPOP('', 'idPop', 'form'), '');

				$tipo = "<input type=text name='matriz[valor]' size=4 value='".$cons[0]->valor ."' onBlur=verificarValor(0,this.value);formataValor(this.value,9)>, ";
				$tipo .= "<span class=txtaviso> (Formato: 999,00)</span>";
				getCampo('combo',$labels['valor'], 'matriz[valor]', $tipo/*$matriz['valor']*/, "onblur=\"formataValor(this.value,".$c++.")\"",'', 8);
				
				
				$tipo = "<input type=text name=matriz[data] size=10 value='$matriz[data]' onBlur=verificaData(this.value,10)>, ";
				$tipo .= "<span class=txtaviso>(Formato: $data[dia]/$data[mes]/$data[ano])</span>";
				getCampo('combo',$labels['data'], 'matriz[data]',  $tipo /*$matriz['data']*/, "onblur=\"verificaData(this.value,".$c++.")\"", '',10);
				
				getCampo('area', 'Observações', 'matriz[obs] ',  $matriz['obs'], '', '', 40);
				
				getBotao('matriz[bntConfirmar]', 'Confirmar');
				
			}
		}
	}
	
	fechaFormulario();
	fechaTabela();
	
}

/**
 * Mostra os dados da conta à pagar especificado por $registro
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function contasAPagarVer($modulo, $sub, $acao, $registro, $matriz) {
	$data = dataSistema();
	$matriz['id'] = $registro;
	$conta = dbContasAPagar($matriz, 'consultar', 'unico');
	
	$tabela['exibe']['titulo']=1;
	
	if($acao == 'cancelar' || $acao == 'baixar' )
		$tabela['exibe']['bntConfirmar']=1;
	
	if ( $acao == 'baixar' ){
		$tipoEspecial = "Campo";
		$dtBaixa = $data['dataNormalData'];
		$valorPago = number_format($conta[0]->valor, "2", ",", "" );
	}
	else{
		$tipoEspecial = "";
		$dtBaixa = $conta[0]->dtBaixa;
		$valorPago = $conta[0]->valorPago;
	}
	
	$tabela['titulo'] = "Contas à Pagar - " . ucfirst( $acao ) ;
	
	$tabela['indice'] = -2;
	
	$tabela['gravata']  = Array('Fornecedor',			'Plano De Conta',			'Pop',				'Valor',			'Data De Cadastro',		'Data de Vencimento',		'Status',			'Data de Baixa', 		'Valor Pago',			'Observações'			);
	$tabela['valores'] 	= Array($conta[0]->nomePessoa,	$conta[0]->planoDeContas,	$conta[0]->idPop,	$conta[0]->valor,	$conta[0]->dtCadastro,	$conta[0]->dtVencimento,	$conta[0]->status,	$dtBaixa,				$valorPago,				$conta[0]->obs			);
	$tabela['formatos']	= Array('',						'',							'pop',				'moeda',			'data',					'data',						'statusPgto',		'data'.$tipoEspecial,	'moeda'.$tipoEspecial,	'area'.$tipoEspecial	);	
	$tabela['campos']	= Array('idFornecedor',			'',							'',					'',					'',						"",							'',					'dtBaixa',				'valorPago',			"obs"					);
	exibeFormulario($tabela, $tipo, $modulo, $sub, $acao, $registro);
	
}


/**
 * Lança uma nova conta à pagar
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function contasAPagarAdicionar ($modulo, $sub, $acao, $registro, $matriz) {
	
	//verifica se recebeu os dados preenchid no formulario e faz o cadastro ou exibe o mesmo 
	if($matriz['bntConfirmar'] && $matriz['idPessoaTipo'] && $matriz['valor'] && $matriz['data'] && $matriz['idPlanoDeContasDetalhes']){
		
		$matriz['data']  = converteData($matriz['data'], 'form', 'banco');
		$matriz['valor'] = formatarValores($matriz['valor']);
		$grava 		   = dbContasAPagar($matriz, 'incluir');  
		
		if ($grava)
			avisoNOURL("Aviso:", "Registro Gravado com Sucesso.", "100%");
		else
			avisoNOURL("Aviso:", "Falha na tentativa de salvar registro. $grava", "100%" );
		
		echo "<br>";
		contasAPagarListar($modulo, $sub, "", $registro, $matriz);	
		
	}
	else{
		if ($matriz['bntConfirmar'])
			avisoNOURL("Atenção:", "Todos os Campos são obrigatórios", "100%" );
		
		echo "<br>";
		formContasAPagar($modulo, $sub, $acao, $registro, $matriz);
	}
}


/**
 * Cancela uma conta à pagar
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function contasAPagarCancelar ($modulo, $sub, $acao, $registro, $matriz) {
	if ( !$matriz['bntConfirmar'] )
		contasAPagarVer($modulo, $sub, $acao, $registro, $matriz);
	
	elseif($registro){
		$matriz['id'] = $registro; 
		$grava = dbContasAPagar($matriz, 'alterar', 'cancelar');
		
		
		if($grava){
			avisoNOURL('<font color="#FFFFFF">Aviso</font>', "Conta Cancelada com Sucesso", "400");
		}
		else {
			avisoNOURL('<font color="#FFFFFF">Erro:</font>', "Erro ao Cancelar Conta", "400");
		}
		
		echo '<br>';
		contasAPagarListar($modulo, $sub, "", $registro, $matriz);
	}	
	
}


/**
 * Realiza a baixa das conta à pagar 
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function contasAPagarBaixar($modulo, $sub, $acao, $registro, $matriz){
	if ( !$matriz['bntConfirmar'] || (!$matriz['valorPago'] && !$matriz['dtBaixa'])){
		
		contasAPagarVer($modulo, $sub, $acao, $registro, $matriz);
		echo "<br>";

	}
	else{
		$matriz['id']=$registro;
		$matriz['dtBaixa'] = converteData($matriz['dtBaixa'], 'form', 'banco');
		$matriz['valorPago'] = formatarValores($matriz['valorPago']);
		
		$grava = dbContasAPagar($matriz, 'alterar', 'baixar');  
		
		if ( $grava ) {
			avisoNOURL('<font color="#FFFFFF">Aviso</font>', "Registro Gravado com Sucesso.", "400");
			
			# Lança debito no fluxo de caixa
			fluxoDeCaixaDebitar( $matriz['id'], $matriz['valorPago'], $matriz['dtBaixa'] );	
		}
		else {
			avisoNOURL('<font color="#FFFFFF">Aviso</font>', "Falha na tentativa de salvar registro. $grava", "400" );
		}
		echo "<br />";
		contasAPagarListar($modulo, $sub, "", $registro, $matriz);
	}
}	

/**
 * Lista as contas a pagar existentes
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */

function contasAPagarListar ($modulo, $sub, $acao, $registro, $matriz) {
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessPlanos, $conn, $tb;
	
	$condicao = Array();
	// data a ser exibida
	$matriz['tipoData'] = ( !$matriz['tipoData'] ? 'dtVencimento' : $matriz['tipoData']);
	
	//nome da data para gravata
	if ($matriz['tipoData']=="dtVencimento") $dtGravata = "Data de Vencimento";
	elseif ($matriz['tipoData']=="dtBaixa") $dtGravata = "Data da Baixa";
	else $dtGravata="Data de Cadastro";
	
	//filtro por pops
	if(!empty($matriz['pop']))
		$condicao[] = " {$tb['ContasAPagar']}.idPop = '".$matriz['pop'] ."' ";
	
	if( $matriz['status'] != 'T' ) {
		$condicao[] = " {$tb['ContasAPagar']}.status = '".( $matriz['status'] ? $matriz['status'] : "P" )."' ";
	}
	
//	pendentes ou baixadas
//	if($acao != "listar_todas"){
//		if ( $acao == "listar_baixadas" ) {
//			$condicao[] = " {$tb['ContasAPagar']}.status = 'B' ";
//		}
//		elseif( $acao == "listar_semana" ) {
//			$diaSemana = intval(getdate("wday"));
//			$diasRestantes = 6 - $diaSemana;
//			$domingo = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")-$diaSemana-1, 	date("Y")))." 00:00:00";
//			$sabado  = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")+$diasRestantes-1,	date("Y")))." 23:59:59";
//			$condicao[] = " {$tb['ContasAPagar']}.dtVencimento BETWEEN '$domingo' AND '$sabado'";			
//		}
//		elseif( $acao == "listar_mes" ) {
//			$inicio = date("Y-m-d", mktime(0, 0, 0, date("m"), 1, 			date("Y")))." 00:00:00";
//			$fim    = date("Y-m-d", mktime(0, 0, 0, date("m"), date("t"),	date("Y")))." 23:59:59";
//			$condicao[] = " {$tb['ContasAPagar']}.dtVencimento BETWEEN '$inicio' AND '$fim'";			
//		}
//		elseif( $acao == "listar_pendentes" || !$acao ) {
//			$condicao[] = " {$tb['ContasAPagar']}.status = 'P' ";
//		}
		
//		switch ( $acao ) {
//			case "listar_baixadas":
//				$condicao[] = " {$tb['ContasAPagar']}.status = 'B' ";
//				break;
//			case "listar_semana":
//				$diaSemana = intval(getdate("wday"));
//				$diasRestantes = 6 - $diaSemana;
//				$domingo = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")-$diaSemana-1, 	date("Y")))." 00:00:00";
//				$sabado  = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")+$diasRestantes-1,	date("Y")))." 23:59:59";
//				$condicao[] = " {$tb['ContasAPagar']}.dtVencimento BETWEEN '$domingo' AND '$sabado'";
//				break;
//			case "listar_mes":
//				$inicio = date("Y-m-d", mktime(0, 0, 0, date("m"), 1, 			date("Y")))." 00:00:00";
//				$fim    = date("Y-m-d", mktime(0, 0, 0, date("m"), date("t"),	date("Y")))." 23:59:59";
//				$condicao[] = " {$tb['ContasAPagar']}.dtVencimento BETWEEN '$inicio' AND '$fim'";
//			default:
//				$condicao[] = " {$tb['ContasAPagar']}.status = 'P' ";
//				break;
//		}
//	}
	
	//periodos pre determinados
	$periodo = $matriz['periodo'];
	if(!empty($periodo)){
		if( $periodo=="hoje" )
			$condicao[] = " left(".$tb['ContasAPagar'].".".$matriz['tipoData'].",10) = CURDATE() ";
		elseif($periodo == "semana")
			$condicao[] = " WEEK(CURDATE()) = WEEK(LEFT(".$tb['ContasAPagar'].".".$matriz['tipoData'].",10)) AND  YEAR(".$tb['ContasAPagar'].".".$matriz['tipoData'].") =  YEAR(CURDATE()) ";
		elseif($periodo == "mes")
			$condicao[] = " MONTH(".$tb['ContasAPagar'].".".$matriz['tipoData'].") =  MONTH(CURDATE()) ";
	}
	
	//formata as datas postadas
	if ( !empty($matriz['dtInicio']) )	$dtInicio = converteData($matriz['dtInicio'], 'form', 'banco') . "00:00:00";
	if ( !empty($matriz['dtFim']) )		$dtFim	  = converteData($matriz['dtFim'], 'form', 'banco') . "23:59:59" ;

	//filtro por periodo
	if(!empty($dtInicio) && !empty($dtFim) )
		$condicao[] = " {$tb['ContasAPagar']}.".$matriz['tipoData']." between '".$dtInicio ."' and '".$dtFim ."' ";
	elseif(!empty($dtInicio) && empty($dtFim) )
		$condicao[] = " {$tb['ContasAPagar']}.".$matriz['tipoData']." > '".$dtInicio ."' ";
	elseif(empty($dtInicio) && !empty($dtFim) )
		$condicao[] = " {$tb['ContasAPagar']}.".$matriz['tipoData']." < '".$dtFim ."' ";
		
	if(!empty($matriz['filtroCom']) && $matriz['filtroPor'] == 'Pessoas')
			$condicao[] = " $tb[Pessoas].nome like '%".$matriz['filtroCom']."%' ";
	
	if(!empty($matriz['planoPai'])){
		$condicao[] = " {$tb['PlanoDeContas']}.id = '" . intval( $matriz['planoPai'] ) . "'";
	}
			
	$data=dataSistema();
		
	$tabela['exibe']['titulo']=1;
//	$tabela['exibe']['filtros']=1;
	$tabela['exibe']['subMenu']=1;
	$tabela['exibe']['filtros']['clientes']	= true;
	$tabela['exibe']['filtros']['planos']	= 1;
	$tabela['exibe']['filtros']['status']	= 1;
	$tabela['exibe']['filtros']['periodo']	= 1;
	$tabela['exibe']['total']=4;
	
	
	$tabela['exibe']['menuOpc']= getRadioArray('matriz[status]', array('P', 'B', 'C', 'T'),									 
									array('Pendentes', 'Baixados', 'Cancelados', 'Todos'),
									($matriz['status'] ? $matriz['status'] : 'P' ), 
									'onchange="javascript:form.submit()"');
	
	$tabela['titulo'] = "Contas à Pagar";
		
	$tabela['gravata']		= Array('Fornecedor',	'Plano De Contas',	$dtGravata,		'Valor',	'Status',			'Opções');
	$tabela['formatos']		= Array('',				'',					'data',			'moeda',	'statusPgtoPagar',	'opcoes');
	$tabela['tamanho']		= Array('25%',			'25%',	 			'11%',			'8%',		'8%',				'23%');
	$tabela['alinhamento']	= Array('left',			'left',				'center',		'right',	'center',			'center');

	$tabela['detalhe'] = dbContasAPagar('', 'consultar','listar', $condicao, $matriz['tipoData']);
		
	exibeNovaTabela($tabela, $modulo, $sub, $acao, $registro, $matriz);
	
	
}

/**
 * Verifica se tem alguma conta com vencimento dentro do periodo configurado em configuracoes.
 *
 * @return bolean
 */
function temContasAPagar(){
	global $tb;
	$parametros = carregaParametrosConfig();
	
	$diasAviso = ( !empty($parametros['dias_aviso_cap']) ? $parametros['dias_aviso_cap'] : 2 );
	
	$dtFinal = strtotime( "+".$diasAviso." day");
	
	$condicao = " dtVencimento between '". date("Y-m-d 00:00:00") ."' and '" . date("Y-m-d 23:59:59", $dtFinal). "' and ".$tb['ContasAPagar'].".status='P'" ;
	$cons = dbContasAPagar("", "consultar", "", $condicao, 'dtVencimento' );
		
	return ( ( count($cons) > 0 ) ? true : false );
}

/**
 * busca dados de uma conta a pagar a partir de seu id
 *
 * @param int $id
 * @return array Dados da conta a pagar
 */
function dadosContasPagarId( $id ){
	
	$consulta = buscaContasPagar( $id, 'id', 'igual', 'id');
	
	$ret = dadosContasAPagar( $consulta );
	
	if( !$ret ){
		echo "<br>";
		avisoNOURL( "AVISO", "Sem Contas A Receber para o ID " . $id, 400 );
	}
	
	return $ret;
}

# função de busca 
function buscaContasPagar($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[ContasAPagar] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[ContasAPagar] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[ContasAPagar] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[ContasAPagar] WHERE $texto ORDER BY $ordem";
	}
	
	# Verifica consulta
	if($sql){
		$consulta=consultaSQL($sql, $conn);
		# Retornvar consulta
		return($consulta);
	}
	else {	
		# Mensagem de aviso
		$msg="Consulta não pode ser realizada por falta de parâmetros";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
	}
} # fecha função de busca


/**
 * Função que retorna um array com os dados do contas a pagar
 *
 * @param mysql_link $consulta
 * @return  array $ret
 */
function dadosContasAPagar( $consulta ){
	$ret = array();
	
	if ( contaConsulta( $consulta ) == 1 ){
		$ret["id"]	 					= resultadoSQL($consulta, 0, "id");
		$ret["idPlanoDeContasDetalhes"] = resultadoSQL($consulta, 0, "idPlanoDeContasDetalhes");
		$ret["idFornecedor"]	 		= resultadoSQL($consulta, 0, "idFornecedor");
		$ret["idPop"]	 				= resultadoSQL($consulta, 0, "idPop");
		$ret["valor"]					= resultadoSQL($consulta, 0, "valor");
		$ret["valorPago"]	 			= resultadoSQL($consulta, 0, "valorPago");
		$ret["dtCadastro"]	 			= resultadoSQL($consulta, 0, "dtCadastro");
		$ret["dtVencimento"]			= resultadoSQL($consulta, 0, "dtVencimento");
		$ret["dtBaixa"]	 				= resultadoSQL($consulta, 0, "dtBaixa");
		$ret["obs"]	 					= resultadoSQL($consulta, 0, "obs");
		$ret["status"]					= resultadoSQL($consulta, 0, "status");
	}
	return $ret;
}

/**
 * Recolhe os dados em comum da contas Á Pagar da Nota Fiscal do Fornecedor para o cadastro
 *
 * @param unknown_type $modulo
 * @param unknown_type $sub
 * @param unknown_type $acao
 * @param unknown_type $registro
 * @param unknown_type $matriz
 */
function contasAPagarNFEAdicionar( $modulo, $sub, $acao, $registro, $matriz ) {
	global $sessCadastro;
	
	if( $matriz['bntConfirmar'] ) {
		if( $sessCadastro[$modulo.$sub.$acao] || contasAPagarNFEValida( $matriz ) ) {
			contasAPagarNFEParcelas( $modulo, $sub, 'incluir_parcelas', $registro, $matriz );		
		}
		else {
			avisoNOURL("Aviso", "Não foi possível gravar os dados! Verifique se todos os campos foram preenchidos corretamente.", 400);
			echo "<br />";
			EntradaNotaFiscalListar( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
	else {
		unset( $sessCadastro[$modulo.$sub.$acao] );
		if( !isset( $matriz['idNFE'] ) || empty( $matriz['idNFE'] ) ) {
			$matriz['idNFE'] = $registro;
		}
		contasAPagarNFEFormulario( $modulo, $sub, $acao, $registro, $matriz );
	}
}

/**
 * Exibe o Formulário de cadastro de Contas a Pagar de NF do Fornecedor
 *
 * @param unknown_type $modulo
 * @param unknown_type $sub
 * @param unknown_type $acao
 * @param unknown_type $registro
 * @param unknown_type $matriz
 */
function contasAPagarNFEFormulario( $modulo, $sub, $acao, $registro, $matriz ){
	global $corFundo, $corBorda, $html, $tb;
	
	$evento = 'onchange="form:submit();"';
	
	EntradaNotaFiscalVer( $modulo, $sub, 'ver', $registro, $matriz );
	novaTabela2('[Lançar Contas à Pagar]<a name="ancora"></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);

		abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );
		$ocultosNomes = array( 'matriz[idNFE]' );
		$ocultosValores = array( $matriz['idNFE'] );
		getCamposOcultos( $ocultosNomes, $ocultosValores );
		$def = "<a href=\"?modulo=$modulo&sub=$sub&registro=''";
		$opcoes = '<div align="right">'.htmlMontaOpcao( $def."&acao=listar\">Não lançar Contas à Pagar para esta Nota</a>",'listar' ).'</div>';
		getCampo('combo', '', '', $opcoes);
		getCampo('combo', _('Entrada'), '', 'Entrada de Nota Fiscal' );

		$lista = dbPlanoDeContas( "", "consultar", "",  "status='A'" );
		$combo = getSelectObjetos( "matriz[idPlanoDeContas]", $lista , "nome", "id", $matriz["idPlanoDeContas"], $evento, true, "[Nenhum Plano de Contas Cadastrado]" );
		getCampo( 'combo', "Plano de Conta", '',  $combo );
		#plano de conta sub
		if( $matriz["idPlanoDeContas"] ){
						
			$lista = dbPlanoDeContasSub("", "consultar", "", "idPlanoDeContas='".$matriz["idPlanoDeContas"]."' and status='A'");
			$combo = getSelectObjetos( "matriz[idPlanoDeContasSub]", $lista, "nome", "id", $matriz["idPlanoDeContasSub"], $evento, true );
			getCampo('combo', "Sub-Plano de Conta", '', $combo, '');
			
			#Verifica se foi selecionado sub-plano
			if( !strpos( $combo, 'selected' ) ){
				$matriz["idPlanoDeContasSub"] = 0;
			}
			#plano de conta filho
			if( $matriz["idPlanoDeContasSub"] ){
				$lista = dbPlanoDeContasDetalhes("", "consultar", "", "idPlanoDeContasSub='".$matriz["idPlanoDeContasSub"]."'  and status='A'");
				$combo = getSelectObjetos( "matriz[idPlanoDeContasDetalhes]", $lista, "nome", "id", $matriz["idPlanoDeContasDetalhes"], "", true );
				getCampo('combo', "Detalhe do Plano de Conta", '', $combo, '');
				getBotao( 'matriz[bntConfirmar]', 'Confirmar' );
			}
		}
		fechaFormulario();
	fechaTabela();
}

/**
 * Exibe as Dados da Nota Fiscal de Fornecedor
 *
 * @param array $matriz
 */
function contasAPagarNFEVer( $matriz ) {
	global $corFundo, $corBorda, $html, $tb;

	$planos = dbPlanoDeContasDetalhes( '', 'consultar', 'tresNiveis', $tb['PlanoDeContasDetalhes'].'.id='.$matriz['idPlanoDeContasDetalhes'] );

	if( count( $planos ) ) {
		novaTabela2('[Contas à Pagar]<a name="ancora"></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			$planosConta = $planos[0]->pai.'<br />'.
							str_repeat( '&nbsp;', 20).' ---> '.$planos[0]->sub.'<br />'.
							str_repeat( '&nbsp;', 40).' ---> '.$planos[0]->detalhe;
			getCampo( 'combo', 'Plano de Contas', '', $planosConta );	
		fechaTabela();
	}
}

function contasAPagarNFEParcelas( $modulo, $sub, $acao, $registro, $matriz ) {
	global $tb, $sessCadastro;

	if( $matriz['bntConfirmarParcela'] || $acao == 'excluir_parcelas' ) {
		if( $acao == 'excluir_parcelas' || contasAPagarNFEValidaParcela( $matriz, $acao ) ) {
			// verifica as ações para gravar no BD e mostrar a mensagem corretamente
			$subTipo = ''; // subtipo da acao
			$subAcao = explode( "_", $acao );
			switch( $subAcao[0] ) {
				case 'alterar':
					$tipo = 'alterar';
					$msg  = 'alterada';
					$condicao = "";
					$subTipo = 'parcela';
					break;
				case 'excluir':
					$tipo = 'excluir';
					$msg  = 'excluída';
					$condicao = "id='" . $registro . "'";
					break;
				default:
					$tipo = 'incluir';
					$msg  = 'cadastrada';
					$condicao = "";
					break;
			}
			// se for inclusão ou alteração recolhe os dados
			if( $subAcao[0] != 'excluir' ) { 

				$nfe = dbEntradaNotaFiscal( '', 'consultar', 'completa', $tb['EntradaNotaFiscal'].'.id='.$matriz['idNFE'] );

				if( count( $nfe ) ) {

					$nfe = $nfe[0];
					$obs =	'Compra realizada para o POP '.$nfe->pop.
							', com a Nota Fiscal de Número '.$nfe->numNF.
							', do fornecedor '.$nfe->nomeFornecedor.
							', emitida no dia '.converteData( $nfe->dataEmissao, 'banco', 'formdata' ).'.';
					$dados = array(
						'idPlanoDeContasDetalhes'	=> $matriz['idPlanoDeContasDetalhes'],
						'idPessoaTipo'				=> $nfe->idFornecedor,
						'idPop'						=> $nfe->idPop,
						'valor'						=> formatarValores( $matriz['valor'] ),
						'data'						=> converteData( $matriz['dtVencimento'], 'form', 'banco' ),
						'obs'						=> $obs,
						'idNFE'						=> $nfe->id,
					);
					if( $subAcao[0] == 'alterar' ) {
						$dados['id'] = $registro;
					}
				}
			}
			if( $acao = 'incluir_parcela' && $matriz['parcelas'] > 1) {
				$gravados = 0; 
				$matriz['obs'] = $dados['obs'];
				for( $a=1; $a<=$matriz['parcelas']; $a++ ) {
					$dados['data'] =  converteData( $matriz[$a]['data'], 'form', 'banco' );
					$dados['valor'] = formatarValores($matriz[$a]['valor']);
					$dados['obs'] = $matriz['obs']."(Parcela: ".$a."/".$matriz['parcelas'].").";
					if( dbContasAPagar( $dados, $tipo, $subTipo, $condicao ) ){
						++$gravados;  
					}						
				}
				if( $gravados == $matriz['parcelas'] ) {
					avisoNOURL( 'Aviso', 'Parcelas de Contas à Pagar Nota Fiscal do Fornecedor ' . $msg . ' com sucesso!', 410 );
					$matriz['dtVencimento'] = $matriz['valor'] ='';
					$acao = 'incluir_parcelas';
				}
				else { // senão avisa que teve um erro e exibe o formulario de alteração
					avisoNOURL( "Erro", "Não foi possível " . $tipo . " dados!", 410 );
				}
			}
			elseif( dbContasAPagar( $dados, $tipo, $subTipo, $condicao ) ) {
				avisoNOURL( 'Aviso', 'Parcela da Contas à Pagar Nota Fiscal do Fornecedor ' . $msg . ' com sucesso!', 410 );
				$matriz['dtVencimento'] = $matriz['valor'] = '';
				$acao = 'incluir_parcelas';
			}
			else { // senão avisa que teve um erro e exibe o formulario de alteração
				avisoNOURL( "Erro", "Não foi possível " . $tipo . " dados!", 410 );
			}
			echo "<br />";
		}
		else {
			avisoNOURL("Aviso", "Não foi possível gravar os dados! Verifique se todos os campos foram preenchidos corretamente. <br /> 
						Ou verifique se esta parcela já está cadastrada.", 410);
			echo "<br />";			
		}
	}

	if( $acao == 'alterar_parcelas' ) {
		$cp['id'] = $registro;
		$consulta = dbContasAPagar( $cp, 'consultar', 'unicoBasico', $tb['ContasAPagar'].'.id='.$registro, 'dtVencimento' );
		if( count( $consulta ) ) {
			$dados = get_object_vars( $consulta[0] );
			$matriz = $dados;
		}
	}
	unset( $sessCadastro[$modulo.$sub.$acao] );
	contasAPagarNFEListar( $modulo, $sub, $acao, $registro, $matriz );
}

/**
 * Lista as Contas a Pagar de uma Nota Fiscal de fornecedor, com um formulário para 
 * cadastrar as parcelas
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function contasAPagarNFEListar( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda;
	
	$corDetalhe = 'tabfundo1';
	
	$largura 				= array( '35%',					'25%',	  	'40%'  );
	$gravata['cabecalho']   = array( 'Data de Vencimento',	'Valor',	'Opções' );
	$alinhamento		 	= array( 'center', 				'center', 	'center' );
	
	$qtdColunas = count( $largura );
	// exibe os dados do cabeçalho da NF
	EntradaNotaFiscalVer( $modulo, $sub, 'ver', $matriz['idNFE'], $matriz );
	// e os dados em comum das contas a pagar
	contasAPagarNFEVer( $matriz );
	//busca as parcelas cadastradas
	$parcelas = dbContasAPagar( '', 'consultar', '', "idNFE=".intval( $matriz['idNFE'] ), 'dtVencimento' );
	$j = $totalParcelas = count( $parcelas );
	
	$vlTotalParcela = 0; $i = 0;
	while($j > 0) { //se tiverem parcelas cadastradas  calcula o  valor Total
		$vlTotalParcela += $parcelas[$i]->valor;
		$i++;
		$j--;
	}
	$matriz['valorTotalParcela']= number_format( $vlTotalParcela, 2, ',','');

	$vlTotalNF = itensMovimentoEstoqueVlTotalNFE( $matriz['idNFE'] );
	$matriz['valorTotal']= number_format( $vlTotalNF - $vlTotalParcela, 2, ',','');
	if( $acao == 'alterar_parcelas' ) {
		contasAPagarNFEFormularioAlterarParcelas($modulo, $sub, $acao, $registro, $matriz);//exibe form de alteração
	}
	elseif( $vlTotalNF > $vlTotalParcela ) { //se o valor total da nota for maior q o valor total de parcelas 
		contasAPagarNFEFormularioParcelas($modulo, $sub, $acao, $registro, $matriz);  //exibe form para cadastro de parcelas
	}		
	
	//começa o quadro da listagem
	
	novaTabela2( '[Parcelas]<a name="ancora"></a>', 'center', "100%", 0, 2, 1, $corFundo, $corBorda, $qtdColunas );
		htmlAbreLinha($corFundo);
			
		if($totalParcelas) {
			for( $i = 0; $i < $qtdColunas; $i++ ){
				itemLinhaTMNOURL( $gravata['cabecalho'][$i], 'center', 'middle', $largura[$i], $corFundo, 0, 'tabfundo0' );
			}
		
			if( $totalParcelas > 0 ) {
				foreach( $parcelas as $parcela ) {
					$cc = 0;
					$def = '<a href="?modulo='.$modulo.'&sub='.$sub.'&registro='.$parcela->id.'&matriz[idNFE]='.$matriz['idNFE'].'&matriz[idPlanoDeContasDetalhes]='.$matriz['idPlanoDeContasDetalhes'];
					$opcoes  = htmlMontaOpcao( $def."&acao=alterar_parcelas\">Alterar</a>",'alterar' );
					$url = '"?modulo='.$modulo.'&sub='.$sub.'&registro='.$parcela->id.'&matriz[idNFE]='.$matriz['idNFE'].
						'&matriz[idPlanoDeContasDetalhes]='.$matriz['idPlanoDeContasDetalhes'].'&acao=excluir_parcelas"';
					$opcoes .= htmlMontaOpcao( "<a href=# onclick=exibeLayerExcluirParcela($parcela->id,$url);>Excluir</a></div>",'excluir' );					
					novaLinhaTabela( $corFundo, '100%');
						$dtVencimento = "<span id=data$parcela->id>".converteData( $parcela->dtVencimento, 'banco', 'formdata' )."</span>";
						$valorParcela = "<span id=valor$parcela->id>".number_format( ( $parcela->valor ), 2, ',','.' )."</span>";
						itemLinhaTMNOURL( $dtVencimento, $alinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $corDetalhe );
						itemLinhaTMNOURL( $valorParcela, $alinhamento[$cc], 'middle', 
											$largura[$cc++], $corFundo, 0, $corDetalhe );
						itemLinhaTMNOURL( $opcoes, $alinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $corDetalhe );
					fechaLinhaTabela();
				}
			}
			
		htmlFechaLinha();
		
		htmlAbreLinha($corFundo);
			itemLinhaTMNOURL( '<b>Valor Total das Parcelas: R$ '.number_format( $vlTotalParcela, 2, ',', '.').'</b>', 'right', 'middle', '', $corFundo, 3, 'tabfundo1' );   						
   		htmlFechaLinha();	
		htmlAbreLinha($corFundo);
			itemLinhaTMNOURL( '<b>Valor Total da Nota Fiscal: R$ '.number_format( $vlTotalNF, 2, ',', '.').'</b>', 'right', 'middle', '', $corFundo, 3, 'tabfundo1' );		
		htmlFechaLinha();

		$botoes = getSubmit( 'matriz[bntConcluir]', 'Concluir Lançamento', 'submit2','button', 'onclick="window.location=\'?modulo='.$modulo.
   			 		'&sub='.$sub.'&acao=finaliza_lancarCP&registro='.$matriz['idNFE'].'&matriz[bntConcluir]=Concluir'.
   			  		'&matriz[idNFE]=' . $matriz['idNFE'] . '\'"' );
   		htmlAbreLinha($corFundo);
   			itemLinhaTMNOURL( $botoes, 'center', 'middle', '100%', $corFundo, $qtdColunas, 'tabfundo0' );
   		htmlFechaLinha();
   		}
   		else {
			itemLinhaTMNOURL( '<span class="txtaviso"><i>Nenhuma Parcela encontrada para a Nota Fiscal de Fornecedor!</i></span>', 'center', 'middle', $largura[$i], $corFundo, $qtdColunas, 'normal10' );
		htmlFechaLinha();
   		}
	fechaTabela();
	
	//janela para confirmação de exclusão de parcelas
	$novaCorFundo = "#FFFFFF";
	abreDiv("janela","display:none; position:absolute; left:174px; text-align:center; top:250px; height:600px;  z-index:2");
	novaTabela2('<center>[Aviso]</center>', 'center', 410, 0, 2, 1, $novaCorFundo, '#990000', 2);
		htmlAbreLinha($corFundo);
   			itemLinhaTMNOURL( '&nbsp;', 'center', 'middle', '100%', $corFundo, 2, 'tabfundo1b' );
   		htmlFechaLinha();
		htmlAbreLinha($corFundo);
   			itemLinhaTMNOURL( '<span class="bold10">Deseja realmente excluir esta parcela?</span>', 'center', 'middle', '100%', $corFundo, 2, 'tabfundo1b' );
   		htmlFechaLinha();
   		htmlAbreLinha($corFundo);
   			itemLinhaTMNOURL( "<span class='bold10'>Data: </span><span id='dataLabel'> </span>", 'center', 'middle', '100%', $corFundo, 2, 'tabfundo1b' );
   		htmlFechaLinha();
   		htmlAbreLinha($corFundo);
   			itemLinhaTMNOURL( "<span class='bold10'>Valor: </span><span id='valorLabel'> </span>", 'center', 'middle', '100%', $corFundo, 2, 'tabfundo1b' );
   		htmlFechaLinha();
		htmlAbreLinha($corFundo);
   			itemLinhaTMNOURL( '&nbsp;', 'center', 'middle', '100%', $corFundo, 2, 'tabfundo1b' );
   		htmlFechaLinha();
		$botoes = getSubmit( 'matriz[bntCancelar]', 'Cancelar', 'submit2','button', 'onclick="ocultaLayerExcluirParcela()"' );
   		htmlAbreLinha($corFundo);
   			itemLinhaTMNOURL( "<span id='botao'> </span>", 'right', 'middle',150,  $novaCorFundo, 1, 'tabfundo1b' );
   			itemLinhaTMNOURL( $botoes, 'left', 'middle', 150, $novaCorFundo, 1, 'tabfundo1b' );
   		htmlFechaLinha();
	fechaTabela();
	fechaDiv();		
}

/**
 * Formulario de alteração de parcelas de nota fiscal de entrada
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function contasAPagarNFEFormularioAlterarParcelas( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $tb, $corBorda;
	
	$css_ = 'tabfundo1';
	novaTabela2("[Alterar Parcelas]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
		htmlAbreLinha($corFundo);
   			itemLinhaTMNOURL( '&nbsp;', 'center', 'middle', '100%', $corFundo, 3, $css_ );
   		htmlFechaLinha();
		htmlAbreLinha( $corFundo );
			abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );
				$ocultosNomes	= array( 'matriz[idNFE]',	'matriz[idPlanoDeContasDetalhes]' );
				$ocultosValores = array( $matriz['idNFE'],	$matriz['idPlanoDeContasDetalhes'] );
				getCamposOcultos( $ocultosNomes, $ocultosValores );
				itemLinhaForm( getCampoData( 'matriz[dtVencimento]', $matriz['dtVencimento'] ), 'center', 'middle', $corFundo, 0, $css_ );
				itemLinhaForm( getCampoNumero( 'matriz[valor]', $matriz['valor'], 13, 'textbox' ), 'center', 'middle', $corFundo, 0, $css_ );
				$botoes	= getSubmit( 'matriz[bntConfirmarParcela]', ( ( $acao == 'alterar_parcelas' ) ? 'Alterar' : 'Incluir' ) );
				itemLinhaForm( $botoes, 'center', 'middle', $corFundo, 2, $css_ );
			fechaFormulario();
		htmlFechaLinha();
		htmlAbreLinha($corFundo);
   			itemLinhaTMNOURL( '&nbsp;', 'center', 'middle', '100%', $corFundo, 3, $css_ );
   		htmlFechaLinha();
	fechaTabela();
}

/**
 * Realiza a verificação do cadastro de contas a pagar por nota fiscal do fornecedor
 *
 * @param array $matriz
 * @return boolean
 */
function contasAPagarNFEValida( &$matriz ){
	$retorno = true;
	if( !$matriz['idPlanoDeContasDetalhes'] ) {
		$retorno = false;
	}
	return $retorno;
}

/**
 * Verifica se Os dados da parcela da Nota Fiscal do Forncedor estão corretos 
 *
 * @param array $matriz
 * @param string $acao
 * @return boolean
 */
function contasAPagarNFEValidaParcela( &$matriz, $acao='' ) {
	$retorno = true;
	//verifica se este produto já não existe neste mesmo pop
	$consulta = dbContasAPagar( '', 'consultar', '', "idNFE='". intval( $matriz['idNFE'] )."' AND dtVencimento='". 
									converteData( $matriz['dtVencimento'], 'form', 'banco' )."'
									AND id <>'". intval( $matriz['id'] )."'", 'dtVencimento' );

	$retorno = verificaRegistroDuplicado( $consulta, $acao );
	if ( empty( $matriz['dtVencimento']) || !validaData( $matriz['dtVencimento'] ) ) {
		$retorno = false;
	}
	if ( empty( $matriz['valor']) || !is_numeric( formatarValores( $matriz['valor'] ) ) || $matriz['valor'] <= 0)  {
		$retorno = false;
	}
	return $retorno;
}

/**
 * Exibe formulário de inclusão de parcelas de nota fiscal de entrada
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function contasAPagarNFEFormularioParcelas( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda;
	$data=dataSistema();
	novaTabela2("[Adicionar Parcelas]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
		novaLinhaTabela($corFundo, '100%');
			$texto="<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=registro value=$registro>
			<input type=hidden name=matriz[idNFE] value=$matriz[idNFE]>
			<input type=hidden name=matriz[idPlanoDeContasDetalhes] value=$matriz[idPlanoDeContasDetalhes]>";
			itemLinhaNOURL($texto, 'left', $corFundo, 3, 'tabfundo1');
		fechaLinhaTabela();

		$botoes="<input type=submit name=matriz[bntCalcular] value='Calcular Novamente' class=submit>";

		if( $matriz['bntCalcular'] && formatarValores($matriz['valor'])==0 ) {
			# Mostrar aviso de valor zerado
			$texto="<span class=txtaviso>ATENÇÃO: Informar Valor Total das Parcelas!</span> ";
			itemTabelaNOURL($texto, 'center', $corFundo, 3, 'tabfundo1');
		}
		if( $matriz['bntCalcular'] && strlen(trim($matriz['dtVencimento']))<6) {
			# Mostrar aviso de valor zerado
			$texto="<span class=txtaviso>ATENÇÃO: Informar Data de Vencimento!</span> ";
			itemTabelaNOURL($texto, 'center', $corFundo, 3, 'tabfundo1');
		}
		else {
			$diaVencimento=substr($matriz[dtVencimento],0,2);
			$mesVencimento=substr($matriz[dtVencimento],2,4);
			$anoVencimento=substr($matriz[dtVencimento],4,6);
			$botoes="<input type=submit name=matriz[bntCalcular] value='Calcular Novamente' class=submit>";
			$botoes.=" <input type=submit name=matriz[bntConfirmarParcela] value=Confirmar class=submit>";
		}
		if( !$matriz['bntCalcular'] ) {
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Data de  vencimento:</b>', 'right', 'middle', '30%', $corFundo, 1, 'tabfundo1');
				$texto="<input type=text name=matriz[dtVencimento] value='$matriz[dtVencimento]' size=10 onBlur=verificaData(this.value,6);><span class=txtaviso> (Formato: ".$data[dia]."/".$data[mes]."/".$data[ano].")</span>";
				itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Valor Total:</b>', 'right', 'middle', '30%', $corFundo, 1, 'tabfundo1');
				$texto="<input type=text name=matriz[valor] value='$matriz[valorTotal]' size=10 onBlur=formataValor(this.value,7)>";
				itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Quantidade de Parcelas:</b>', 'right', 'middle', '30%', $corFundo,1, 'tabfundo1');
				itemLinhaTMNOURL(formSelectParcelas($matriz['parcelas'],'parcelas'), 'left', 'middle', '70%', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntCalcular] value=Calcular class=submit>";
				itemLinhaTMNOURL($texto, 'center', 'middle', '70%', $corFundo, 3, 'tabfundo1');
			fechaLinhaTabela();
		}
		#### opção de parcelamento - mostrar parcelas
		elseif( $matriz['bntCalcular'] ) {
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Data de vencimento:</b>', 'right', 'middle', '30%', $corFundo, 2, 'tabfundo1');
				$texto="<input type=text name=matriz[dtVencimento] value='$matriz[dtVencimento]' size=10 onBlur=verificaData(this.value,6);><span class=txtaviso> (Formato: ".$data[dia]."/".$data[mes]."/".$data[ano].")</span>";
				itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 1, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Valor Total:</b>', 'right', 'middle', '30%', $corFundo, 2, 'tabfundo1');
				$texto="<input type=text name=matriz[valor] value='$matriz[valor]' size=10 onBlur=formataValor(this.value,7)>";
				itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 1, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Quantidade de Parcelas:</b>', 'right', 'middle', '30%', $corFundo, 2, 'tabfundo1');
				itemLinhaTMNOURL(formSelectParcelas($matriz['parcelas'],'parcelas'), 'left', 'middle', '70%', $corFundo, 1, 'tabfundo1');
			fechaLinhaTabela();
			# Mostrar Parcelas
			if( $mesVencimento ) {
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('100%', 'center', $corFundo, 3, 'tabfundo1');
						$valor_parcelas=calculaParcelasContasPagar($matriz['valor'], $matriz['parcelas'], $matriz['dtVencimento']);
						parcelasServicoAdicional($valor_parcelas);
					htmlFechaColuna();
				fechaLinhaTabela();
			}
			novaLinhaTabela($corFundo, '100%');
				$texto=$botoes;
				# gravar parcelas na form
				for($a=1;$a<=$matriz['parcelas'];$a++) {
					$texto.="\n<input type=hidden name=matriz[$a][data] value='".$valor_parcelas[$a]['data']."'>";
					$texto.="\n<input type=hidden name=matriz[$a][valor] value='".formatarValoresForm($valor_parcelas[$a]['valor'])."'>";
				}
				itemLinhaTMNOURL($texto, 'center', 'middle', '70%', $corFundo, 3, 'tabfundo1');
			fechaLinhaTabela();
		
		}
	fechaTabela();
}

/**
 * Gerencia o cancelamento de contas a pagar lançadas para a nota fiscal de fornecedor
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function contasAPagarNFECancelar( $modulo, $sub, $acao, $registro, $matriz ) {
	global $tb, $sessCadastro;
	
	if( $matriz['bntSim'] ){ //se deseja cancelar, realiza a consulta das contas pendentes e altera o status para cancelada
		$condicao = "{$tb['ContasAPagar']}.idNFE = $registro AND {$tb['ContasAPagar']}.status = 'P'";
		$consulta = dbContasAPagar( '', 'consultar', '', $condicao, 'dtVencimento' );
		$total = count( $consulta );
		$alterados = 0;
		for( $i= 0; $i<$total; $i++){
			$dados['id'] = $consulta[$i]->id;
			if( dbContasAPagar( $dados, 'alterar','cancelar', '','') )
				$alterados++;
		}
		if( $total && ( $alterados == $total)) {
			$matriz['status'] = 'C';
			dbEntradaNotaFiscal( $matriz['status'] , 'alterar', 'status', "{$tb['EntradaNotaFiscal']}.id  = {$registro}", '' );
			$msg = 'Cancelamento de Conta(s) à Pagar realizada com sucesso!';
		}
		else {
			$msg ='Não foi possível o cancelamento da(s) Conta(s) à Pagar!';
		}
		avisoNOURL("Aviso", $msg, 400);
		echo "<br />";
		EntradaNotaFiscalListar( $modulo, $sub, 'listar', '', $matriz );
	}
	elseif( $matriz['bntNao'] ) { //se não desejar cancelar as contas a pagar lançadas exibe a listagem das notas fiscais
		EntradaNotaFiscalListar( $modulo, $sub, 'listar', '', $matriz );
	}
	else {
		// exibe formulário para verificação se deseja cancelar as contas a pagar lançadas para a nota fiscal do fornecedor
		contasAPagarNFEFormCancelar( $modulo, $sub, $acao, $registro, $matriz );
	}
}

/**
 * formulário que exibe as parcelas lançadas no contas a pagar referente a nota fiscal de fornecedor
 * e verifica se o usuário deseja cancelar as parcelas que estão pendentes
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function contasAPagarNFEFormCancelar( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda, $html, $tb;
	
	$largura 				= array(	'25%', 				'10%',		'25%'	);
	$gravata['cabecalho']   = array(	'Dt Vencimento',	'Valor',	'Status');
	$gravata['alinhamento'] = array(	'center',			'right',	'center');
	$qtdColunas = count( $largura );
	
	$consultaNF = dbEntradaNotaFiscal( '', 'consultar', '', "{$tb['EntradaNotaFiscal']}.id  = {$registro}",'');
	if( $consultaNF ) {
		$numNF = $consultaNF[0]->numNF;
	}
	
	$css_ = 'tabfundo1';
	novaTabela2( '[Cancelamento de Contas à Pagar de Nota Fiscal de Fornecedor ]<a name="ancora"></a>', 'center', '100%', 0, 2, 1, $corFundo, $corBorda, 5);
		abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );
			htmlAbreLinha($corFundo);
	   			itemLinhaTMNOURL( '&nbsp;', 'center', 'middle', '100%', $corFundo, 5, $css_ );
	   		htmlFechaLinha();
			htmlAbreLinha($corFundo);
	   			itemLinhaTMNOURL( '<span class="bold10">Existem parcelas lançadas no Contas à Pagar referente a Nota Fiscal de Fornecedor n&ordm; '.$numNF.'. 
	   								<br>Deseja cancelar as parcelas lançadas que estão pendentes?</span>', 'center', 'middle', '100%', $corFundo, 5, $css_);
	   		htmlFechaLinha();
	   		htmlAbreLinha($corFundo);
	   			itemLinhaTMNOURL( "&nbsp;", 'center', 'middle', '100%', $corFundo, 5, $css_ );
	   		htmlFechaLinha();
		
			$condicao = "{$tb['ContasAPagar']}.idNFE = $registro";
			$consulta = dbContasAPagar( '', 'consultar', '', $condicao, 'dtVencimento' );
			$total = count( $consulta );
			
			htmlAbreLinha($corFundo);
				itemLinhaTMNOURL( '&nbsp;', 'center', 'middle', '20%', $corFundo, 1, $css_ );
				for( $i = 0; $i < $qtdColunas; $i++ ){
					itemLinhaTMNOURL( $gravata['cabecalho'][$i], 'center', 'middle', $largura[$i], $corFundo, 0, 'tabfundo0' );
				}
				itemLinhaTMNOURL( '&nbsp;', 'center', 'middle', '20%', $corFundo, 1, $css_ );
			htmlFechaLinha();
			for( $i= 0; $i<$total; $i++){
				htmlAbreLinha( $corFundo );
					itemLinhaTMNOURL( '&nbsp;', 'center', 'middle', '20%', $corFundo, 1, $css_ );
					itemLinhaTMNOURL( converteData( $consulta[$i]->dtVencimento, 'banco', 'formdata' ) , $gravata['alinhamento'][0], 'middle', $largura[0], $corFundo, 0, 'normal10' );
					itemLinhaTMNOURL( number_format($consulta[$i]->valor, 2, ',', '') , $gravata['alinhamento'][1], 'middle', $largura[1], $corFundo, 0, 'normal10' );
					itemLinhaTMNOURL( formSelectStatusContasPagar($consulta[$i]->status, 'status', 'check') , $gravata['alinhamento'][2], 'middle', $largura[2], $corFundo, 0, 'normal10' );				
					itemLinhaTMNOURL( '&nbsp;', 'center', 'middle', '20%', $corFundo, 1, $css_ );
				htmlFechaLinha();			
			}
		
	   		htmlAbreLinha($corFundo);
	   			itemLinhaTMNOURL( '&nbsp;', 'center', 'middle', '100%', $corFundo, 5, $css_ );
	   		htmlFechaLinha();
			$confirmar = getSubmit( 'matriz[bntSim]', 'Sim', 'submit','button', 
				'onclick="window.location=\'?modulo='.$modulo.'&sub='.$sub.'&acao='.$acao.'&registro='.$registro.'&matriz[bntSim]=Sim'.'\'"' );
			$cancelar = getSubmit( 'matriz[bntNao]', 'Não', 'submit2','button', 
				'onclick="window.location=\'?modulo='.$modulo.'&sub='.$sub.'&acao='.$acao.'&registro='.$registro.'&matriz[bntNao]=Nao'.'\'"' );
	   		htmlAbreLinha($corFundo);
	   			itemLinhaTMNOURL( $confirmar. "&nbsp;". $cancelar, 'center', 'middle',150,  $novaCorFundo, 5, $css_ );
	   		htmlFechaLinha();
		
		
		fechaFormulario();			
	fechaTabela();	
}
?>