<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 15/04/4004
# Ultima alteração: 15/04/2004
#    Alteração No.: 001
#
# Função:
#      Includes de Consultas



/**
 * @return void
 * @param string $modulo
 * @param string $sub
 * @param string $acao
 * @param int  $registro
 * @param array $matriz
 * @desc Form para filtros de Faturamento por POP
*/
function formBaixaPOP($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $sessLogin;
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		
		$data=dataSistema();
		
		# Motrar tabela de busca
		novaTabela2("[Consulta de Baixas por POP]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		
			#cabecalho com campos hidden
			abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );
			
			#pop de acesso
			$combo = formSelectPOP( $matriz[pop], 'pop', 'multi' ) . 
					'<input type="checkbox" name="matriz[pop_todos]" value="S"' . ( $matriz['pop_todos'] ? ' checked="checked"' : '' ) ." /><b>Todos</b>";
			getCampo( "combo", '<b>POP:</b><br /><span class="normal10">Selecione o POP de Acesso</span>', "", $combo );
			
			#Detalhar
			getDetalharCliente( $matriz );
			
			#periodo do relatorio
//			getPeriodoDias( 6, 7, $matriz, "Vencimento" );
			getPeriodoDias( 7, 8, $matriz, "Vencimento" );

			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Só inadimplentes:</b><br>
				<span class=\"normal10\">Lista somente os que pagaram no mês posterior ao vencimento.</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				if($matriz['inadp']) $opcInadp='checked';
				$texto="<input type=checkbox name=matriz[inadp] value='S' $opcInadp>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntConfirmar] value='Consultar' class=submit>";
				itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		fechaTabela();
	}
	
}


/**
 * @return void
 * @param string $modulo
 * @param string $sub
 * @param string $acao
 * @param int $registro
 * @param array $matriz
 * @desc Consulta de Faturamento por POP (Total, Recebido, Inadimplente)
 Ja gera o relatorio automaticamente
*/
function relatorioFaturamentoPOPBaixa($modulo, $sub, $acao, $registro, $matriz) {
	
	global $corFundo, $corBorda, $html, $sessLogin, $conn, $tb, $limites, $configMeses;
	
	# Formatar Datas
	$dtInicial 	= converteData( $matriz['dtInicial']." 00:00:00", 'form', 'banco' );
	$dtFinal	= converteData( $matriz['dtFinal']  ." 23:59:59", 'form', 'banco' );	
	
	$dtBase = 'dtBaixa';
	$ttBase = ' - Por Baixa';
	$grvMes = 'Baixa';
	$grvRec = 'Baixado';
	$numColunas = 6;
		
	# Montar 
	if(!$matriz[pop_todos] && $matriz[pop]) {
		$i=0;
		$sqlADDPOP="$tb[POP].id in (";
		while($matriz[pop][$i]) {
			
			$sqlADDPOP.="'".$matriz[pop][$i]."'";
			
			if($matriz[pop][$i+1]) $sqlADDPOP.=",";
			$i++;
		}
		$sqlADDPOP.=")";
		
		$consultaPOP=buscaPOP($sqlADDPOP, '','custom','nome');
		
	}
	elseif($matriz[pop_todos]) {
		# consultar todos os POP
		$consultaPOP=buscaPOP('','','todos','nome');
	}

	$titulo = "Baixas por POP";
	
	if($matriz[inadp]) {
		$inadp="and LEFT(ContasAReceber.dtBaixa,7) > LEFT(ContasAReceber.dtVencimento,7)";
		$titulo.=" - Só Inadimplentes";
	}	
	
	echo "<br>";
	novaTabela("[ $titulo ]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, $numColunas);

	# Consultar POPs
	if($consultaPOP && contaConsulta($consultaPOP)>0) {
		
		for($x=0;$x<contaConsulta($consultaPOP);$x++) {
			
			$matResultado=array();
			$totalGeral=array();
			$total=array();
			
			$idPOP=resultadoSQL($consultaPOP, $x, 'id');
			$nomePOP=resultadoSQL($consultaPOP, $x, 'nome');

			# SQL para consulta de emails por dominios do cliente informado
			# Consultas de Pendentes
			$sql="
				SELECT
					LEFT($tb[ContasReceber].dtBaixa,7) mes,  
					
					SUM($tb[ContasReceber].valor) valor, 
					SUM($tb[ContasReceber].valorRecebido) recebido, 
					SUM($tb[ContasReceber].valorJuros) juros, 
					SUM($tb[ContasReceber].valorDesconto) desconto, 
					$tb[ContasReceber].status status
				FROM 
					$tb[POP], 
					$tb[Pessoas], 
					$tb[PessoasTipos], 
					$tb[ContasReceber], 
					$tb[DocumentosGerados]
				WHERE 
					$tb[ContasReceber].idDocumentosGerados = $tb[DocumentosGerados].id 
					AND $tb[DocumentosGerados].idPessoaTipo = $tb[PessoasTipos].id 
					AND $tb[PessoasTipos].idPessoa = $tb[Pessoas].id 
					AND $tb[Pessoas].idPOP = $tb[POP].id 
					AND $tb[ContasReceber].status in ('B', 'P') 
					AND $tb[ContasReceber].dtBaixa  
						BETWEEN '$dtInicial' AND '$dtFinal' 
					AND $tb[POP].id = '$idPOP'
					$inadp
				GROUP BY 
					$tb[POP].id, 
					LEFT($tb[ContasReceber].$dtBase,7),
					$tb[ContasReceber].status
			";
			
			$consultaBaixados=consultaSQL($sql, $conn);
			
			if($consultaBaixados && contaConsulta($consultaBaixados)>0) {
				# Totalizar
				for($i=0;$i<contaConsulta($consultaBaixados);$i++) {
					
					$valor=resultadoSQL($consultaBaixados, $i, 'valor');
					$recebido=resultadoSQL($consultaBaixados, $i, 'recebido');
					$juros=resultadoSQL($consultaBaixados, $i, 'juros');
					$desconto=resultadoSQL($consultaBaixados, $i, 'desconto');
					$mes=resultadoSQL($consultaBaixados, $i, 'mes');
					$status=resultadoSQL($consultaBaixados, $i, 'status');
					
					$liquido = $recebido - $juros;
					
					$total["$mes"][valor] += $valor;
					$total["$mes"][recebido] += $recebido;
					$total["$mes"][juros] += $juros;
					$total["$mes"][desconto] += $desconto;
					$total["$mes"][liquido] += $liquido;
					$total["$mes"][pendente] += $valor - $liquido;
					
				}
				
			}
			
			if(is_array($total)) {
				
				# Cabeçalho
				
				$matCabecalho=array($grvMes, "Liquido", "Juros", "Descontos", "Bruto");
				
				$keys=array_keys($total);
				# Cabeçalho
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela('POP', 'center', '40%', 'tabfundo0');
					itemLinhaTabela($grvMes, 'center', '10%', 'tabfundo0');
					itemLinhaTabela("$grvRec Liquido", 'center', '10%', 'tabfundo0');
					itemLinhaTabela('Juros', 'center', '10%', 'tabfundo0');
					itemLinhaTabela('Descontos', 'center', '10%', 'tabfundo0');
					itemLinhaTabela("$grvRec Bruto", 'center', '10%', 'tabfundo0');
				fechaLinhaTabela();
				
				for($a=0;$a<count($keys);$a++) {
					
					$ano=substr($keys[$a],0,4);
					$mesFormatado=$configMeses[intval(substr($keys[$a], 5, 2))]."/".$ano;
					
					$totalValor=$total[$keys[$a]][valor];
					$totalRecebido=$total[$keys[$a]][recebido];
					$totalPendente=$total[$keys[$a]][pendente];
					$totalJuros=$total[$keys[$a]][juros];
					$totalDesconto=$total[$keys[$a]][desconto];
					$totalLiquido=$total[$keys[$a]][liquido];
					$totalPendente=$total[$keys[$a]][pendente];
					
					#matrizes para o relatorio
					$c=0;
					//$matResultado[$matCabecalho[$c++]][$a]=$nomePOP;
					$matResultado[$matCabecalho[$c++]][$a]=$mesFormatado;
					$matResultado[$matCabecalho[$c++]][$a]=formatarValoresForm($totalLiquido);
					$matResultado[$matCabecalho[$c++]][$a]=formatarValoresForm($totalJuros);
					$matResultado[$matCabecalho[$c++]][$a]=formatarValoresForm($totalDesconto);
					$matResultado[$matCabecalho[$c++]][$a]=formatarValoresForm($totalRecebido);
					
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTabela($nomePOP, 'left', '40%', 'normal10');
						itemLinhaTabela($mesFormatado, 'center', '10%', 'normal10');
						itemLinhaTabela(formatarValoresForm($totalLiquido), 'right', '10%', 'normal10');
						itemLinhaTabela(formatarValoresForm($totalJuros), 'right', '10%', 'normal10');
						itemLinhaTabela(formatarValoresForm($totalDesconto), 'right', '10%', 'normal10');
						itemLinhaTabela(formatarValoresForm($totalRecebido), 'right', '10%', 'normal10');
					fechaLinhaTabela();
					
					$totalGeral[faturado]+=$totalValor;
					$totalGeral[recebido]+=$totalRecebido;
					$totalGeral[liquido]+=$totalLiquido;
					$totalGeral[pendente]+=$totalPendente;
					$totalGeral[juros]+=$totalJuros;
					$totalGeral[desconto]+=$totalDesconto;
					
				} #fecha laco de montagem de tabela
				
				//calcula o total de todos os pops selecionados.
					$totalTodos[faturado] += $totalGeral[faturado];
					$totalTodos[recebido] += $totalGeral[recebido];
					$totalTodos[liquido]  += $totalGeral[liquido];
					$totalTodos[pendente] += $totalGeral[pendente];
					$totalTodos[juros]    += $totalGeral[juros];
					$totalTodos[desconto] += $totalGeral[desconto];
				
				# Totalizar
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('Totais', 'right', 'middle', '50%', $corFundo, 2, 'tabfundo0');
					itemLinhaTabela(formatarValoresForm($totalGeral[liquido]), 'right', '10%', 'txtok');
					itemLinhaTabela(formatarValoresForm($totalGeral[juros]), 'right', '10%', 'txttrial');
					itemLinhaTabela(formatarValoresForm($totalGeral[desconto]), 'right', '10%', 'txttrial');
					itemLinhaTabela(formatarValoresForm($totalGeral[recebido]), 'right', '10%', 'txtok');
				fechaLinhaTabela();
				
				if($x+1<contaConsulta($consultaPOP)) itemTabelaNOURL('&nbsp;','left',$corFundo,$numColunas,'normal10');
				
				# Alimentar Array de Detalhe com mais um campo - totais
				$c=0;
				//$matResultado[$matCabecalho[$c++]][$a]='&nbsp;';
				$matResultado[$matCabecalho[$c++]][$a]='<b>Total</b>';
				$matResultado[$matCabecalho[$c++]][$a]='<b>'.formatarValoresForm($totalGeral[liquido]).'</b>';
				$matResultado[$matCabecalho[$c++]][$a]='<b>'.formatarValoresForm($totalGeral[juros]).'</b>';
				$matResultado[$matCabecalho[$c++]][$a]='<b>'.formatarValoresForm($totalGeral[desconto]).'</b>';
				$matResultado[$matCabecalho[$c++]][$a]='<b>'.formatarValoresForm($totalGeral[recebido]).'</b>';
				
				// caso seja a ultima pagina do relatorio exibe o total geral de todos pops. 20050407 gustavo
				if ($x+1 == contaConsulta($consultaPOP) ){
					$c = 0; $z = ++$a;
					$matResultado[$matCabecalho[$c++]][$z]='<br><b>Total de todos POP:</b>';
					$matResultado[$matCabecalho[$c++]][$z]='<b>'.formatarValoresForm($totalTodos[liquido]).'</b>';
					$matResultado[$matCabecalho[$c++]][$z]='<b>'.formatarValoresForm($totalTodos[juros]).'</b>';
					$matResultado[$matCabecalho[$c++]][$z]='<b>'.formatarValoresForm($totalTodos[desconto]).'</b>';
					$matResultado[$matCabecalho[$c++]][$z]='<b>'.formatarValoresForm($totalTodos[recebido]).'</b>';
				}
				
				# Alimentar Matriz Geral
				$matrizRelatorio[detalhe]=$matResultado;
				
				# Alimentar Matriz de Header
				$matrizRelatorio[header][TITULO]=strtoupper("Total de $titulo")."<br>".converteData($dtInicial,'banco','formdata')." até ".converteData($dtFinal,'banco','formdata');;
				$matrizRelatorio[header][POP]=$nomePOP;
				$matrizRelatorio[header][IMG_LOGO]=$html[imagem][logoRelatorio];
				
				# Configurações
				$matrizRelatorio[config][linhas]=20;
				$matrizRelatorio[config][layout]='landscape';
								
				$matrizGrupo[]=$matrizRelatorio;
				
			}
			else {
				# Não há registros
				itemTabelaNOURL('Não foram encontrados faturamentos neste período', 'left', $corFundo, $numColunas, 'txtaviso');
			}
		}//for
		
		// Exibir valor total do relatorio. adicionado em 20050406 por gustavo
		 		itemTabelaNOURL('&nbsp;','left',$corFundo,$numColunas,'normal10');
				novaLinhaTabela($corFundo, '100%');
					novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('Total de todos POP', 'right', 'middle', '50%', $corFundo, 2, 'tabfundo0');
					itemLinhaTabela(formatarValoresForm($totalTodos[liquido]), 'right', '10%', 'txtok');
					itemLinhaTabela(formatarValoresForm($totalTodos[juros]), 'right', '10%', 'txttrial');
					itemLinhaTabela(formatarValoresForm($totalTodos[desconto]), 'right', '10%', 'txttrial');
					itemLinhaTabela(formatarValoresForm($totalTodos[recebido]), 'right', '10%', 'txtok');
				fechaLinhaTabela();
		//thats all
		
		if(is_array($matrizRelatorio) && count($matrizRelatorio)>0) {
			itemTabelaNOURL('&nbsp;','left',$corFundo,$numColunas,'normal10');
			novaTabela("Arquivos Gerados<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, $numColunas);
				# Converter para PDF:
				$arquivo=k_reportHTML2PDF(k_report($matrizGrupo, 'html','faturamento_popbaixa'),'faturamento_popbaixa',$matrizRelatorio[config]);
				itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>Relatório $titulo</a>",'pdf'), 'center', $corFundo, $numColunas, 'txtaviso');
			fechaTabela();
		}
	}
	else {
		# Não há registros
		itemTabelaNOURL('Não há lançamentos disponíveis', 'left', $corFundo, $numColunas, 'txtaviso');
	}
		
	fechaTabela();
}



#
# Faturamento Detalhado de Baixas Por POP
#
function relatorioBaixaClientesPOP($modulo, $sub, $acao, $registro, $matriz) {
	
	global $corFundo, $corBorda, $html, $sessLogin, $conn, $tb, $limites, $configMeses;
	
	# Formatar Datas
//	$matriz[dtInicial]=formatarData($matriz[dtInicial]);
//	$matriz[dtFinal]=formatarData($matriz[dtFinal]);
//	$dtInicial=substr($matriz[dtInicial],2,4)."/".substr($matriz[dtInicial],0,2).'/01 00:00:00';
//	$dtFinal=substr($matriz[dtFinal],2,4)."/".substr($matriz[dtFinal],0,2).'/'.dataDiasMes(substr($matriz[dtFinal],0,2))." 23:59:59";	

	$dtInicial 	= converteData( $matriz['dtInicial']." 00:00:00", 'form', 'banco' );
	$dtFinal	= converteData( $matriz['dtFinal']  ." 23:59:59", 'form', 'banco' );
	
	$dtBase='dtBaixa';
	$ttBase=' - Por Baixa';
	$grvMes='Baixa';
		
	# Montar 
	if(!$matriz[pop_todos] && $matriz[pop]) {
		$i=0;
		$sqlADDPOP="$tb[POP].id in (";
		while($matriz[pop][$i]) {
			
			$sqlADDPOP.="'".$matriz[pop][$i]."'";
			
			if($matriz[pop][$i+1]) $sqlADDPOP.=",";
			$i++;
		}
		$sqlADDPOP.=")";
		
		$consultaPOP=buscaPOP($sqlADDPOP, '','custom','nome');
		
	}
	elseif($matriz[pop_todos]) {
		# consultar todos os POP
		$consultaPOP=buscaPOP('','','todos','nome');
	}
	
	$gravata=array("Nome do Cliente", $grvMes, "Faturado", "Líquido", "Juros", "Desconto", "Bruto", "Pendente");
	$largura=array("30%",    "10%",    "10%",      "10%",     "10%",   "10%",      "10%",   "10%");
	$alinhar=array("left",   "center",   "right",    "right",   "right", "right",    "right", "right");
	$numCol=count($gravata);
	
	$titulo="Faturamento Detalhado por Clientes - Baixas";
	
	if($matriz[inadp]) {
		$inadp="and LEFT(ContasAReceber.dtBaixa,7) > LEFT(ContasAReceber.dtVencimento,7)";
		$titulo.=" - Só Inadimplentes";
	}	
	
	echo "<br>";
	novaTabela("[$titulo]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, $numCol);
	
	# Consultar POPs
	if($consultaPOP && contaConsulta($consultaPOP)>0) {
		
		
		
		#conta os pop
		for($x=0;$x<contaConsulta($consultaPOP);$x++) {
		
			$totalGeral=array();
			$total=array();
			$matResultado=array();
			$matCabecalho=$gravata;
			$l=0;
			
			$idPOP=resultadoSQL($consultaPOP, $x, 'id');
			$nomePOP=resultadoSQL($consultaPOP, $x, 'nome');
			
			# Cabeçalho
			itemTabelaNOURL("POP: $nomePOP", 'center', $corFundo, $numCol, 'tabfundo0');
			
			
			#Consulta os Baixados
			$sql="
				SELECT 
					$tb[PessoasTipos].id idPessoaTipo, 
					$tb[Pessoas].nome nomePessoa, 
					LEFT($tb[ContasReceber].dtBaixa,10) mes, 
					SUM($tb[ContasReceber].valor) valor, 
					SUM($tb[ContasReceber].valorRecebido) recebido, 
					SUM($tb[ContasReceber].valorJuros) juros, 
					SUM($tb[ContasReceber].valorDesconto) desconto, 
					$tb[ContasReceber].status status
				FROM 
					$tb[POP], 
					$tb[Pessoas], 
					$tb[PessoasTipos], 
					$tb[ContasReceber], 
					$tb[DocumentosGerados] 
				WHERE 
					$tb[ContasReceber].idDocumentosGerados = $tb[DocumentosGerados].id 
					AND $tb[DocumentosGerados].idPessoaTipo = $tb[PessoasTipos].id 
					AND $tb[PessoasTipos].idPessoa = $tb[Pessoas].id 
					AND $tb[Pessoas].idPOP = $tb[POP].id 
					AND $tb[ContasReceber].status = 'B'
					AND $tb[ContasReceber].dtBaixa 
						BETWEEN '$dtInicial' AND '$dtFinal' 
					AND $tb[POP].id='$idPOP'
					$inadp
				GROUP BY 
					$tb[PessoasTipos].id,
					LEFT($tb[ContasReceber].dtBaixa,7)
				ORDER BY 
					$tb[Pessoas].nome, 
					$tb[ContasReceber].dtBaixa	
			";
			
			$consultaBaixados=consultaSQL($sql, $conn);
			
			if($consultaBaixados && contaConsulta($consultaBaixados)>0) {
				# Totalizar
				$matRecebido=array();
				$matJuros=array();
				$matDesconto=array();
				$matTotal=array();
				for($i=0;$i<contaConsulta($consultaBaixados);$i++) {
					$idPessoaTipo=resultadoSQL($consultaBaixados, $i, 'idPessoaTipo');
					$nomePessoa=resultadoSQL($consultaBaixados, $i, 'nomePessoa');
					$valor=resultadoSQL($consultaBaixados, $i, 'valor');
					$recebido=resultadoSQL($consultaBaixados, $i, 'recebido');
					$juros=resultadoSQL($consultaBaixados, $i, 'juros');
					$desconto=resultadoSQL($consultaBaixados, $i, 'desconto');
					$mes=resultadoSQL($consultaBaixados, $i, 'mes');
					
					$matDetalhe["$idPessoaTipo"][nomePessoa] = $nomePessoa;
					$matTotal["$idPessoaTipo"]["$mes"] += $valor;
					$matRecebido["$idPessoaTipo"]["$mes"] += $recebido;
					$matJuros["$idPessoaTipo"]["$mes"] += $juros;
					$matDesconto["$idPessoaTipo"]["$mes"] += $desconto;
				}
			}

			# SQL para consulta de emails por dominios do cliente informado
			# Consultas de Pendentes
			$sql="
				SELECT 
					$tb[PessoasTipos].id idPessoaTipo, 
					$tb[Pessoas].nome nomePessoa, 
					LEFT($tb[ContasReceber].dtBaixa,10) mes, 
					SUM($tb[ContasReceber].valor) valor, 
					SUM($tb[ContasReceber].valorRecebido) recebido, 
					SUM($tb[ContasReceber].valorJuros) juros, 
					SUM($tb[ContasReceber].valorDesconto) desconto, 
					$tb[ContasReceber].status status 
				FROM 
					$tb[POP], 
					$tb[Pessoas], 
					$tb[PessoasTipos], 
					$tb[ContasReceber], 
					$tb[DocumentosGerados] 
				WHERE 
					$tb[ContasReceber].idDocumentosGerados = $tb[DocumentosGerados].id 
					AND $tb[DocumentosGerados].idPessoaTipo = $tb[PessoasTipos].id 
					AND $tb[PessoasTipos].idPessoa = $tb[Pessoas].id 
					AND $tb[Pessoas].idPOP = $tb[POP].id 
					AND $tb[ContasReceber].status = 'P'
					AND $tb[ContasReceber].dtBaixa 
						BETWEEN '$dtInicial' AND '$dtFinal' 
					AND $tb[POP].id='$idPOP' 
					$inadp
				GROUP BY 
					$tb[PessoasTipos].id,
					LEFT($tb[ContasReceber].dtBaixa,7)
				ORDER BY 
					$tb[Pessoas].nome,
					$tb[ContasReceber].dtBaixa				
			";

			$consultaPendentes=consultaSQL($sql, $conn);
			
			if($consultaPendentes && contaConsulta($consultaPendentes)>0) {
				# Totalizar
				$matPendente=array();
				for($i=0;$i<contaConsulta($consultaPendentes);$i++) {
					
					$idPessoaTipo=resultadoSQL($consultaPendentes, $i, 'idPessoaTipo');
					$nomePessoa=resultadoSQL($consultaPendentes, $i, 'nomePessoa');
					$valor=resultadoSQL($consultaPendentes, $i, 'valor');
					$mes=resultadoSQL($consultaPendentes, $i, 'mes');
					
					$matDetalhe["$idPessoaTipo"][nomePessoa] = $nomePessoa;
					$matPendente["$idPessoaTipo"]["$mes"] = $valor;
					$matTotal["$idPessoaTipo"]["$mes"] += $valor;
					
				}
				
			}

			if(is_array($matTotal)) {
				
				$keys=array_keys($matTotal);

				# Cabeçalho
				$cc=0;
				novaLinhaTabela($corFundo, '100%');
					foreach ($gravata as $label) 
						itemLinhaTabela($label, $alinhar["$cc"], $largura[$cc++], 'tabfundo0');
				fechaLinhaTabela();
				
				for($a=0;$a<count($keys);$a++) {

					$meses=array_keys($matTotal[$keys[$a]]);
					
					if(is_array($meses)) {
						
						for($m=0;$m<count($meses);$m++) {
							
							$mes=$meses[$m];
							$ano=substr($mes,0,4);
							#$mesFormatado=$configMeses[intval(substr($mes, 5, 2))]."/".$ano;
							$mesFormatado=substr(converteData($meses[$m], 'banco','form'), 0, 10);
						
							$nomePessoa=$matDetalhe[$keys[$a]][nomePessoa];
							$totalPendente=$matPendente[$keys[$a]][$mes];
							$totalValor=$matTotal[$keys[$a]][$mes];
							$totalRecebido=$matRecebido[$keys[$a]][$mes];
							$totalJuros=$matJuros[$keys[$a]][$mes];
							$totalDesconto=$matDesconto[$keys[$a]][$mes];
							
							$totalLiquido=$totalRecebido-$totalJuros;
							
							$cc=0;
							novaLinhaTabela($corFundo, '100%');
								itemLinhaTabela($nomePessoa, $alinhar["$cc"], $largura[$cc++], 'normal10');
								itemLinhaTabela($mesFormatado, $alinhar["$cc"], $largura[$cc++], 'normal10');
								itemLinhaTabela(formatarValoresForm($totalValor), $alinhar["$cc"], $largura[$cc++], 'normal10');
								itemLinhaTabela(formatarValoresForm($totalLiquido), $alinhar["$cc"], $largura[$cc++], 'normal10');
								itemLinhaTabela(formatarValoresForm($totalJuros), $alinhar["$cc"], $largura[$cc++], 'normal10');
								itemLinhaTabela(formatarValoresForm($totalDesconto), $alinhar["$cc"], $largura[$cc++], 'normal10');
								itemLinhaTabela(formatarValoresForm($totalRecebido), $alinhar["$cc"], $largura[$cc++], 'normal10');
								itemLinhaTabela(formatarValoresForm($totalPendente), $alinhar["$cc"], $largura[$cc++], 'normal10');
							fechaLinhaTabela();
							
							$totalGeral[faturado]+=$totalValor;
							$totalGeral[recebido]+=$totalRecebido;
							$totalGeral[pendente]+=$totalPendente;
							$totalGeral[juros]+=$totalJuros;
							$totalGeral[desconto]+=$totalDesconto;
							$totalGeral[liquido]+=$totalLiquido;
							
							
							
							$c=0;
							$matResultado[$matCabecalho[$c++]][$l]=$nomePessoa;
							$matResultado[$matCabecalho[$c++]][$l]=$mesFormatado;
							$matResultado[$matCabecalho[$c++]][$l]=formatarValoresForm($totalValor);
							$matResultado[$matCabecalho[$c++]][$l]=formatarValoresForm($totalLiquido);
							$matResultado[$matCabecalho[$c++]][$l]=formatarValoresForm($totalJuros);
							$matResultado[$matCabecalho[$c++]][$l]=formatarValoresForm($totalDesconto);
							$matResultado[$matCabecalho[$c++]][$l]=formatarValoresForm($totalRecebido);
							$matResultado[$matCabecalho[$c++]][$l]=formatarValoresForm($totalPendente);
							
							$l++;
							
						}
					}
					
				} #fecha laco de montagem de tabela
				//calcula o total de todos os pops selecionados.
					$totalTodos[faturado] += $totalGeral[faturado];
					$totalTodos[recebido] += $totalGeral[recebido];
					$totalTodos[liquido]  += $totalGeral[liquido];
					$totalTodos[pendente] += $totalGeral[pendente];
					$totalTodos[juros]    += $totalGeral[juros];
					$totalTodos[desconto] += $totalGeral[desconto];
				# Totalizar
				$cc=2;
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('Totais', 'right', 'middle', '50%', $corFundo, 2, 'tabfundo0');
					itemLinhaTabela(formatarValoresForm($totalGeral[faturado]), $alinhar["$cc"], $largura[$cc++], 'txtaviso');
					itemLinhaTabela(formatarValoresForm($totalGeral[liquido]), $alinhar["$cc"], $largura[$cc++], 'txtok');
					itemLinhaTabela(formatarValoresForm($totalGeral[juros]), $alinhar["$cc"], $largura[$cc++], 'txttrial');
					itemLinhaTabela(formatarValoresForm($totalGeral[desconto]), $alinhar["$cc"], $largura[$cc++], 'txttrial');
					itemLinhaTabela(formatarValoresForm($totalGeral[recebido]), $alinhar["$cc"], $largura[$cc++], 'txtok');
					itemLinhaTabela(formatarValoresForm($totalGeral[pendente]), $alinhar["$cc"], $largura[$cc++], 'txtok');
				fechaLinhaTabela();
				
				if($x+1<contaConsulta($consultaPOP)) itemTabelaNOURL('&nbsp;','left',$corFundo,$numCol,'normal10');
				
				# Alimentar Array de Detalhe com mais um campo - totais
				$c=0;
				$matResultado[$matCabecalho[$c++]][$l]='&nbsp;';
				$matResultado[$matCabecalho[$c++]][$l]='<b>Total</b>';
				$matResultado[$matCabecalho[$c++]][$l]='<b>'.formatarValoresForm($totalGeral[faturado]).'</b>';
				$matResultado[$matCabecalho[$c++]][$l]='<b>'.formatarValoresForm($totalGeral[liquido]).'</b>';
				$matResultado[$matCabecalho[$c++]][$l]='<b>'.formatarValoresForm($totalGeral[juros]).'</b>';
				$matResultado[$matCabecalho[$c++]][$l]='<b>'.formatarValoresForm($totalGeral[desconto]).'</b>';
				$matResultado[$matCabecalho[$c++]][$l]='<b>'.formatarValoresForm($totalGeral[recebido]).'</b>';
				$matResultado[$matCabecalho[$c++]][$l]='<b>'.formatarValoresForm($totalGeral[pendente]).'</b>';
				
				# Alimentar Matriz Geral
				$matrizRelatorio[detalhe]=$matResultado;
				
				# Alimentar Matriz de Header
				$matrizRelatorio[header][TITULO]=$titulo;
				$matrizRelatorio[header][POP]=$nomePOP;
				$matrizRelatorio[header][IMG_LOGO]=$html[imagem][logoRelatorio];
				
				# Configurações
				$matrizRelatorio[config][linhas]=25;
				$matrizRelatorio[config][layout]='landscape';
				$matrizRelatorio[config][marginleft]='1.0cm;';
				$matrizRelatorio[config][marginright]='1.0cm;';
								
				$matrizGrupo[]=$matrizRelatorio;
			}
			else {
				# Não há registros
				itemTabelaNOURL('Não foram encontrados faturamentos neste período', 'left', $corFundo, $numCol, 'txtaviso');
			}
		}
		
		// Exibir valor total do relatorio. adicionado em 20050406 por gustavo
		novaTabela2SH("left", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
			htmlAbreLinha($corFundo);
				itemLinhaTMNOURL('<b>Total GERAL: '.formatarValoresForm($totalGeral[faturado]).'</br>', 'center', 'middle', '100%', $corFundo, $col, "txtAviso");
			htmlFechaLinha();
		fechaTabela();
		
		$c=0;
		$matResultado[$matCabecalho[$c++]][$l]='<b>Total Geral</b>';
		$matResultado[$matCabecalho[$c++]][$l]='<b>'.formatarValoresForm($totalGeral[faturado]).'</b>';
		//thats all
		
		if (count($matrizGrupo)>0) {
			itemTabelaNOURL('&nbsp;','left',$corFundo,$numCol,'normal10');
			novaTabela("Arquivos Gerados<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, $numColunas);
				# Converter para PDF:
				$arquivo=k_reportHTML2PDF(k_report($matrizGrupo, 'html','faturamento_pop'),'faturamento_pop',$matrizRelatorio[config]);
				itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>Relatório $titulo</a>",'pdf'), 'center', $corFundo, $numCol, 'txtaviso');
			fechaTabela();
		}
		
	}
	else {
		# Não há registros
		itemTabelaNOURL('Não há lançamentos disponíveis', 'left', $corFundo, $numCol, 'txtaviso');
	}
		
	fechaTabela();
}

?>
