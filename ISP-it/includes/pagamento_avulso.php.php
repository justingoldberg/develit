<?php
#p faturamento.php formtemplates.php lancamentos.php pagamento_avulso.php planos_servicos.php menu.php /www/isp-tdkom/includes/
 
  function pagamentoAvulso( $modulo, $sub, $acao, $registro, $matriz ){
 
	global $corFundo, $corBorda, $sessLogin;
	
// sistema de permissao diferente. por funcao ao inves de modulo.
	$titulo = "<b>Pagamentos Avulsos</b>";
	//$subtitulo = "<b> das Agências Bancárias</b>" ;
	$itens=Array('Adicionar', 'Procurar', 'Listar');
	getHomeModulo($modulo, $sub, $titulo, $subtitulo, $itens);
	
	echo "<br>";
	switch ($acao) {
		case "adicionar":
			pagamentoAvulsoAdicionar($modulo, $sub, $acao, $registro, $matriz);
			break;
		case "baixar":
			baixarFaturaCliente($modulo, $sub, $acao, $registro, $matriz); //verificar funcionamento
			break;
		case "dados_cobranca":
			dadosFaturaCliente($modulo, $sub, $acao, $registro, $matriz);
			break;
		case "cancelar":
			pagamentoAvulsoCancelar($modulo, $sub, $acao, $registro, $matriz);
			break;
		case "estorno":
			estornoFaturaCliente($modulo, $sub, $acao, $registro, $matriz);
			break;
		default:
			pagamentoAvulsoListar($modulo, $sub, $acao, $registro, $matriz);
			break;
	}
}

function formPagamentoAvulsoAdicionar($modulo, $sub, $acao, $registro, $matriz){
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
			
		$ServicoPlano=explode(":", $matriz[idServicoPlano]);
		echo "<br>";
		novaTabela2("[Lançar Pagamento]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		novaLinhaTabela($corFundo, '100%');
		$texto="			
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=matriz[idPessoaTipo] value=$matriz[idPessoaTipo]>
			<input type=hidden name=matriz[idServicoPlano] value=$ServicoPlano[0] >
			<input type=hidden name=matriz[idPlano] value=$ServicoPlano[1] >
			<input type=hidden name=matriz[bntConfirmar] value=$matriz[bntAdicionar]>&nbsp;";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		
		novaLinhaTabela($corFundo, '100%');
			mostraCliente($matriz[idPessoaTipo]);
		fechaLinhaTabela();
		
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('&nbsp;', 'right', 'middle', '30%', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		
		$servicoPlano = dadosServicoPlano($matriz[idServicosPlanos]);
		$servico= checkServico($servicoPlano[idServico]);
		 
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<span class=bold10> Serviço: </span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL('<span class=bold10>'.$servico[nome].'</span>' , 'left', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Valor:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			$matriz[valor]=formatarValoresForm($matriz[valor]);
			$texto="<input type=text name=matriz[valor] size=10 value='$matriz[valor]' onBlur=formataValor(this.value,7)>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('Referência<br> <span class=normal8>Mês de Referência da Fatura:', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm(formSelectMes($matriz[mes],'mes','form').formSelectAno($matriz[ano],'ano','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Data de Vencimento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			$texto="<input type=text name=matriz[dtVencimento] size=10 value='$matriz[dtVencimento]' onBlur=verificaData(this.value,10)>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Conta Baixada:</b><span class=nomral8> Selecione a opção "SIM" caso deseje baixar este lançamento neste momento', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm(formSelectSimNao("N", "baixar", "form"), 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
				
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Observações:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			$texto="<textarea name=matriz[obs] rows=3 cols=60>$matriz[obs]</textarea>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		
		getBotao('matriz[bntConfirmar]', 'Confirmar');
	
	
}

function pagamentoAvulsoAdicionar ($modulo, $sub, $acao, $registro, $matriz) {
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro;
	
	//primeiramente seleciona o cliente
	if(!$matriz[idPessoaTipo]) {	
		echo "<br>";
		novaTabela2("[Lançar Pagamento]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		novaLinhaTabela($corFundo, '100%');
		$texto="			
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=matriz[idPessoaTipo] value=$matriz[idPessoaTipo]>
			<input type=hidden name=matriz[idServicoPlano] value=$matriz[idServicoPlano] >
			<input type=hidden name=matriz[bntConfirmar] value=$matriz[bntAdicionar]>&nbsp;";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		clienteProcurar( $modulo, $sub, $acao, $registro, $matriz );
	
	}
	
	elseif (!$matriz[idServicoPlano]) {
		echo "<br>";
		novaTabela2("[Lançar Pagamento]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		novaLinhaTabela($corFundo, '100%');
		$texto="			
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=matriz[idPessoaTipo] value=$matriz[idPessoaTipo]>
			<input type=hidden name=matriz[idServicoPlano] value=$matriz[idServicoPlano] >
			<input type=hidden name=matriz[bntConfirmar] value=$matriz[bntAdicionar]>&nbsp;";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			mostraCliente($matriz[idPessoaTipo]);
		fechaLinhaTabela();
		
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('&nbsp;', 'right', 'middle', '30%', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<span class=bold10> Selecione um Serviço:</span><br><span class=normal9> Selecione um Serviço para lançar fatura</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm(pessoasTiposFormSelectServicosPlanos($matriz[idPessoaTipo], '', "idServicoPlano"), 'left', 'middle', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		fechaTabela();
	}
	
	elseif(!$matriz[bntConfirmar]){
		formPagamentoAvulsoAdicionar($modulo, $sub, $acao, $registro, $matriz );
		fechaTabela();
	}
	
	elseif($matriz[idServicoPlano] && $matriz[idPessoaTipo] && $matriz[bntConfirmar]){
		$matriz[dtVencimento] = converteData($matriz[dtVencimento], 'form', 'banco');
		$matriz[valor] = formatarValores($matriz[valor]);
		
		//busca o id de qual faturamento a ContaAReceber estara embutido
		$matriz[idFaturamento]=checkFaturamento($modulo, $sub, $acao, $registro, $matriz);
		if(!$matriz[idFaturamento])	$matriz[idFaturamento] = gerarFaturamentoPagamentoAvulso($modulo, $sub, $acao, $registro, $matriz);
		
		//grava um documentoGerado para a Conta
		$matriz[idDocumentoGerado]=novoIDDocumentoGerado();
		dbDocumentoGerado($matriz, 'incluir');
		
		//grava a Conta a Receber
		$matriz[idDocumentosGerados] = $matriz[idDocumentoGerado];
		$grava = dbContasReceber($matriz, 'incluir');
		$matriz[idContasAPagar]=mysql_insert_id();

		$matriz[idPlanoDocumentoGerado] = 0;
		$matriz[dtVencimentoPlanoDocumentoGerado]=$matriz[dtVencimento];
		$matriz[idFormaCobranca] = 0;
		$matriz[idVencimento] = 0;
		
		dbPlanoDocumentoGerado($matriz, 'incluir' );
		$matriz[idPlanoDocumentoGerado]=mysql_insert_id();
		
		dbServicoPlanoDocumentoGerado($matriz, 'incluir');
		
			
		fechaTabela();
		if ($grava){
			aviso("Aviso","Conta Lançada com Sucesso", "?modulo=",300);
			echo "<br>";
			if ($matriz[baixar]!='N')
				pagamentoAvulso($modulo, $sub, 'baixar', $matriz[idContasAPagar], $matriz);
			else
				pagamentoAvulsoListar($modulo, $sub, $acao, $registro, $matriz);
			
		}
		else {
			aviso("Erro","A Conta `a Receber não foi lançada", "?modulo=",300);
		}	
					
	}	

}


function pagamentoAvulsoListar ($modulo, $sub, $acao, $registro, $matriz) {
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessPlanos, $conn, $tb;
	$data=dataSistema();
	
	$sqlAD="";
	
	( !$matriz[tipoData] ? $tipoData = 'dtCadastro' : $tipoData = $matriz[tipoData]);
	
	//label da gravata para a coluna data
	if ($matriz[tipoData]=="dtVencimento") $dtGravata = "Data de Vencimento";
	elseif ($matriz[tipoData]=="dtBaixa") $dtGravata = "Data da Baixa";
	else $dtGravata="Data de Cadastro";
	
	//filtro por pop
	if($matriz[filtroPOP]){
		if(!empty($matriz[pop]))
			$sqlAD .= "AND $tb[Faturamentos].idPOP = '".$matriz[idPop] ."' ";
	}	
	
	if(!empty($acao)){
		if ($acao=="listar_baixadas")
			$sqlAD .= "AND $tb[ContasReceber].status = 'B' ";
		elseif($acao=="listar_pendentes")
			$sqlAD .= "AND $tb[ContasReceber].status = 'P' ";
	}
	$periodo = $_REQUEST[periodo];
	if(!empty($periodo)){
		if($periodo=="hoje")
			$sqlAD .= " AND left($tb[ContasReceber].$tipoData,10) = CURDATE() ";
		elseif($periodo=="semana")
			$sqlAD .= " AND WEEK(CURDATE()) = WEEK(LEFT($tb[ContasReceber].$tipoData,10)) AND  YEAR($tb[ContasReceber].$tipoData) =  YEAR(CURDATE()) ";
		elseif($periodo=="mes")
			$sqlAD .= " AND  MONTH($tb[ContasReceber].$tipoData) =  MONTH(CURDATE()) ";
	}
	
	//formata as datas postadas
	$dtInicio=converteData($matriz[dtInicio], 'form', 'banco');
	$dtFim=converteData($matriz[dtFim], 'form', 'banco');
	
	//filtro por periodo
	if($matriz[filtroPeriodo]){
		if(!empty($dtInicio) && !empty($dtFim) )
			$sqlAD .= "AND $tb[ContasReceber].$tipoData between '".$dtInicio ."' and '".$dtFim ."' ";
		elseif(!empty($dtInicio) && empty($dtFim) )
			$sqlAD .= "AND $tb[ContasReceber].$tipoData > '".$dtInicio ."' ";
		elseif(empty($dtInicio) && !empty($dtFim) )
			$sqlAD .= "AND $tb[ContasReceber].$tipoData < '".$dtFim ."' ";
	}
	
	(empty($matriz[filtroOrdem]) ? $ordem = 'status' : $ordem = $matriz[filtroOrdem]) ;
			
	$sql = "SELECT 
		$tb[Pessoas].nome,
		$tb[ContasReceber].id,
		$tb[ContasReceber].valor,
		$tb[ContasReceber].valorRecebido,
		$tb[ContasReceber].$tipoData,
		$tb[ContasReceber].status
	FROM
		$tb[ContasReceber]
		INNER JOIN $tb[DocumentosGerados]
			On($tb[ContasReceber].idDocumentosGerados = $tb[DocumentosGerados].id)
		INNER JOIN $tb[Faturamentos]
			On($tb[DocumentosGerados].idFaturamento = $tb[Faturamentos].id)
		INNER JOIN $tb[PessoasTipos] 
			On ($tb[DocumentosGerados].idPessoaTipo = $tb[PessoasTipos].id) 
		INNER JOIN $tb[Pessoas] 
			On($tb[PessoasTipos].idPessoa = $tb[Pessoas].id) 
	WHERE 
		$tb[Faturamentos].idServico = 9999999 $sqlAD 
	Order By $ordem, $tb[ContasReceber].$tipoData";
	
	$consulta=consultaSQL($sql, $conn);
	 #################################
	# Caso nao exista ContasAReceber#
   #################################
	if(!$consulta || contaConsulta($consulta)==0) {
		# Não há registros
		itemTabelaNOURL('Não há pagamentos lancados', 'left', $corFundo, 5, 'txtaviso');
	}
	else {//exibe pagamento
		$tabela[exibe][titulo]=1;
		$tabela[exibe][filtros]=1;
		$tabela[exibe][subMenu]=1;
		$tabela[exibe][total]=1;
		
		$tabela[titulo] = "Lançamentos Avulsos";
		
		$tabela[gravata]    =	Array("Nome do Cliente", "Valor", $dtGravata, "Status", "Opções");
		$tabela[tamanho]    =	Array("35%",			 "10%",	  "20%",   		  "15%",	"20%");
		$tabela[alinhamento]=   Array("left",			 "right", "center",		  "center",	"center");
		
		for($i=0; $i<contaConsulta($consulta); $i++ ) {

			$status = resultadoSQL($consulta, $i, 'status');
			$valor = resultadoSQL($consulta, $i, 'valor');
				
			# Mostrar registro
			$id=resultadoSQL($consulta, $i, 'id');
			$tabela[detalhe][$i][] = resultadoSQL($consulta, $i, 'nome');
			$tabela[detalhe][$i][] = formatarValoresForm( $valor);
			$tabela[detalhe][$i][] = converteData(resultadoSQL($consulta, $i, $tipoData), "banco", "form");
			$tabela[detalhe][$i][] = formSelectStatusContasReceber($status,'','check'); 
			$tabela[total][valor]+=$valor;
			
			if($status=='P') { 
				$ac="<a href=?modulo=$modulo&sub=$sub&acao=";
				$opcoes=htmlMontaOpcao($ac."baixar&registro=$id>Baixar</a>",'baixar');
				$opcoes.=htmlMontaOpcao($ac."cancelar&registro=$id>Cancelar</a>",'cancelar');
				$opcoes.=htmlMontaOpcao($ac."estorno&registro=$id>Estorno</a>",'estorno');
			}
			else {
				$ac="<a href=?modulo=$modulo&sub=$sub&acao=";
				$opcoes=htmlMontaOpcao($ac."dados_cobranca&registro=$id>Dados da Cobranca</a>",'info');
				$opcoes.=htmlMontaOpcao($ac."estorno&registro=$id>Estorno</a>",'estorno');
			}
			
			$tabela[detalhe][$i][]=$opcoes;
		}
	
		exibeTabela($tabela, $modulo, $sub, $acao, $registro, $matriz);
				
	}//existe pagamento			
}	

function exibeTabela ($tabela, $modulo, $sub, $acao, $registro, $matriz) {
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessPlanos, $conn, $tb;
	
	novaTabela("[ ".$tabela[titulo]." ]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);	

	htmlAbreLinha($corFundo);
	htmlAbreColuna("100%","center", $corFundo, 5, 'tabfundo1');
	
	novaTabelaSH("center", "100%", 0,0,0,$corFundo, $corBorda, 5);

	
	if ($tabela[exibe][filtros]){
		$form="<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>";
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL($form, 'midle','left', '', $corFundo, 5, 'tabfundo1');
			fechaLinhaTabela();
						
		novaLinhaTabela($corFundo, '100%');
			
			//filtro dos pops
			//$adicional="<option>Selecione um POP:\n";
			//$pop = formSelectPOP($matriz[pop], 'pop', 'form', $adicional);
			//$pop .= "<input type=submit name=matriz[filtroPOP] value='Ir'>";
			itemLinhaNOURL($pop, 'left', $corFundo, 2, 'tabfundo1');
			
			(($matriz[tipoData] != "dtVencimento") && ($matriz[tipoData] != "dtBaixa") ?  $opcDe = "checked" : $opcDe = "") ;
			$datas = "<input id=dtCad type=radio name=matriz[tipoData] value=dtCadastro $opcDe onChange=javascript:form.submit()><label for=dtCad>Data de Cadastro </label>";
			
			(($matriz[tipoData] == "dtVencimento") ?  $opcDe = "checked" : $opcDe = "") ;
			$datas .= "<input id=dtVenc type=radio name=matriz[tipoData] value=dtVencimento $opcDe onChange=javascript:form.submit()><label for=dtVenc>Data de Vencimento </label>";
			
			(($matriz[tipoData] == "dtBaixa") ?  $opcDe = "checked" : $opcDe = "") ;
			$datas .= "<input id=dtBaixa type=radio name=matriz[tipoData] value=dtBaixa $opcDe onChange=javascript:form.submit()><label for=dtBaixa>Data de Baixa </label>";		
			
			itemLinhaNOURL($datas , 'right', $corFundo, 3, 'tabfundo1');
		fechaLinhaTabela();
		
		novaLinhaTabela($corFundo, '100%');
			$def = "<a href=?modulo=$modulo&sub=$sub&acao=$acao&";
		
			$opcoes = htmlMontaOpcao($def."periodo=hoje>Hoje </a>",'faturamento');
			$opcoes .= htmlMontaOpcao($def."periodo=semana>Esta semana </a>",'documento'); 	
			$opcoes .= htmlMontaOpcao($def."periodo=mes>Este mês</a>",'paginas');
			itemLinhaNOURL($opcoes, 'left', $corFundo, 2, 'tabfundo1');
			
			$periodo = getPeriodoDatas($matriz, 9, 10);	
			itemLinhaNOURL($periodo, 'right', $corFundo, 3, 'tabfundo1');
		fechaLinhaTabela();
		
		novaLinhaTabela($corFundo, '100%');
				$form="</form>";
				itemLinhaTMNOURL($form, 'midle', 'left', '', $corFundo, 5, 'tabfundo1');
		fechaLinhaTabela();

	}
	fechaTabela();
	htmlFechaColuna();
	htmlFechaLinha();	

		if ($tabela[exibe][subMenu]){
		menuOpcAdicional( $modulo, $sub, $acao, $registro, $matriz, 5 );
	}
	
	// Cabeçalho
	novaLinhaTabela( $corFundo, '100%' );
		for ( $x = 0; $x < count( $tabela[gravata] ); $x++ )
			itemLinhaTMNOURL( $tabela[gravata][$x], 'center', 'middle', $tabela[tamanho][$x], $corFundo, 0, 'tabfundo0');
	fechaLinhaTabela();
	
	//corpo
	foreach($tabela[detalhe] as $linha){
		novaLinhaTabela($corFundo, '100%');
		$c=0;
		foreach($linha as $campo)
			itemLinhaTabela($campo, $tabela[alinhamento][$c], $tabela[tamanho][$c++], 'normal10');
		fechaLinhaTabela();
	}	

	//total
	if ($tabela[exibe][total]){
		novaLinhaTabela($corFundo, '100%');		$c=0;
			itemLinhaTabela("Total:", 'right', $tabela[tamanho][$c++], 'tabfundo0');
			itemLinhaTabela(formatarValoresForm($tabela[total][valor]), 'center', $tabela[tamanho][$c], 'txtok');
			itemLinhaForm('&nbsp;', 'center', 'middle', $corFundo, 3, 'tabfundo0');
		fechaLinhaTabela();	
	}
		
	fechaTabela();
}

function pagamentoAvulsoCancelar ($modulo, $sub, $acao, $registro, $matriz) {
	cancelarFaturaCliente($modulo, $sub, $acao, $registro, $matriz);
}	

/**
 * @author 
 *    busca se  se ja existe algum faturamento de contas avulso para este mes
 */
function checkFaturamento($modulo, $sub, $acao, $registro, $matriz) {
	global $tb;
	// indentifica uma fatura para contas avulsos quando o idServico esta com o cod. 9999999  (sete 9)
	$where= "idServico=9999999 AND mes='". $matriz[mes]."' AND ano='" .$matriz[ano]."'";
	$consultaFat = buscaFaturamentos($where,'', 'custom', 'idServico');
	
	if (contaConsulta($consultaFat)>0)
		$idFaturamento=resultadoSQL($consultaFat,0,'id');
	
	return ($idFaturamento);
}

function gerarFaturamentoPagamentoAvulso($modulo, $sub, $acao, $registro, $matriz) {
	
	$matriz[descricao] = "Faturamento referente ao recebimento de pagamentos a vulso, referente à data  ".$matriz[mes]."/".$matriz[ano];
	$matriz[servico] = 9999999;
	
	$grava = dbFaturamento($matriz, 'incluir', "A");
	
	$idFaturamento= checkFaturamento($modulo, $sub, $acao, $registro, $matriz);
	
	return($idFaturamento);
}

?>
