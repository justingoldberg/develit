<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 15/04/2004
# Ultima alteração: 15/06/2004
#    Alteração No.: 003
#
# Função:
#      Includes de Relatórios


/**
 * @return void
 * @param string $modulo
 * @param string $sub
 * @param string $acao
 * @param int  $registro
 * @param array $matriz
 * @desc Form para filtros de Faturamento por POP
*/
function formFaturamentoPOP($modulo, $sub, $acao, $registro, $matriz) {

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
		novaTabela2("[Consulta de Faturamento por POP]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			menuOpcAdicional($modulo, $sub, $acao, $registro);
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro>";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>POP:</b><br>
				<span class=normal10>Selecione o POP de Acesso</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				if($matriz[pop_todos]) $opcPOP='checked';
				$texto="<input type=checkbox name=matriz[pop_todos] value=S $opcPOP><b>Todos</b>";
				itemLinhaForm(formSelectPOP($matriz[pop],'pop','multi').$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Detalhar:</b><br>
				<span class=normal10>Detalhar relatório por cliente</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				if($matriz[detalhar]) $opcDetalhar='checked';
				$texto="<input type=checkbox name=matriz[detalhar] value='S' $opcDetalhar>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
//			novaLinhaTabela($corFundo, '100%');
//				itemLinhaTMNOURL('<b>Mes/Ano Inicial:</b><br>
//				<span class=normal10>Informe o mes/ano inicial para consulta</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
//				$texto="<input type=text name=matriz[dtInicial] size=7 value='$matriz[dtInicial]' onBlur=verificaDataMesAno2(this.value,7)>&nbsp;<span class=txtaviso>(Formato: $data[mes]/$data[ano])</span>";
//				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
//			fechaLinhaTabela();
//			novaLinhaTabela($corFundo, '100%');
//				itemLinhaTMNOURL('<b>Mes/Ano Final:</b><br>
//				<span class=normal10>Informe o mes/ano final para consulta</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
//				$texto="<input type=text name=matriz[dtFinal] size=7 value='$matriz[dtFinal]'  onBlur=verificaDataMesAno2(this.value,8)>&nbsp;<span class=txtaviso>(Formato: $data[mes]/$data[ano])</span>";
//				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
//			fechaLinhaTabela();
			getPeriodoDias(7, 8, $matriz);
			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Recebido no período:</b><br>
				<span class=normal10>Considerar baixado somentes as baixas no mesmo mês</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				if($matriz[noperiodo]) $opcNoperiodo = 'checked';
				$texto="<input type=checkbox name=matriz[noperiodo] value='1' $opcNoperiodo>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
//			separando o botao de consulta / geracao relatorio 20050415 gustavo
//			novaLinhaTabela($corFundo, '100%');
//				$texto="<input type=submit name=matriz[bntConfirmar] value='Consultar' class=submit>";
//				itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
			getBotoesConsRel();
			
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
function consultaFaturamentoPOP($modulo, $sub, $acao, $registro, $matriz) {
	global $corFundo, $corBorda, $html, $sessLogin, $conn, $tb, $limites, $configMeses;
	
	# Formatar Datas
//	$matriz[dtInicial]=formatarData($matriz[dtInicial]);
//	$matriz[dtFinal]=formatarData($matriz[dtFinal]);
//	$dtInicial=substr($matriz[dtInicial],2,4)."/".substr($matriz[dtInicial],0,2).'/01 00:00:00';
//	$dtFinal=substr($matriz[dtFinal],2,4)."/".substr($matriz[dtFinal],0,2).'/'.dataDiasMes(substr($matriz[dtFinal],0,2))." 23:59:59";	
	
	$dtBase='dtVencimento';
	$grvMes='Vencimento';
	$grvRec='Recebido';
	$numColunas = 9;

//
			# Formatar Datas
		if ($matriz[dtInicial]) {
			$data=formatarData($matriz[dtInicial]);
			$dtInicial=substr($data,4,4)."-".substr($data,2,2).'-'.substr($data,0,2).' 00:00:00';
		}
	
		if ($matriz[dtFinal]) {
			$data=formatarData($matriz[dtFinal]);
			$dtFinal=substr($data,4,4)."-".substr($data,2,2).'-'.substr($data,0,2).' 23:59:59';
		}
	
		// Ajusta o sql para determinar o periodo escolhido
		$sqlDT="";
		if($matriz[dtInicial] && $matriz[dtFinal]) {
			$sqlDT=" AND $tb[ContasReceber].$dtBase between '$dtInicial' and '$dtFinal' ";
			$periodo="de ".$dtInicial." até ".$dtFinal;
		} 
		elseif ($matriz[dtInicial]) {
			$sqlDT=" AND $tb[ContasReceber].$dtBase.dtBaixa >= '$dtInicial' ";
			$periodo="a partir de ".$dtInicial;
		} 
		elseif ($matriz[dtFinal])  {
			$sqlDT=" AND $tb[ContasReceber].$dtBase <= '$dtFinal' ";
			$periodo="até ".$dtFinal;
		}	
//

	#
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

	echo "<br>";
	$titulo="[Faturamento Total por POP]";
	if ($matriz[noperiodo])
		$titulo="[Faturamento Total por POP - Recebido no período]";
		
	
	novaTabela($titulo."<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 8);

	# Consultar POPs
	if($consultaPOP && contaConsulta($consultaPOP)>0) {
		
		for($x=0;$x<contaConsulta($consultaPOP);$x++) {
		
			$idPOP=resultadoSQL($consultaPOP, $x, 'id');
			$nomePOP=resultadoSQL($consultaPOP, $x, 'nome');
			$totalGeral=array();
			$total=array();

			
			# SQL para consulta de emails por dominios do cliente informado
			# Consultas de Pendentes
			$sql="
				SELECT 
					LEFT($tb[ContasReceber].dtVencimento,7) mes, 
					LEFT($tb[ContasReceber].dtBaixa,7) baixa, 
					($tb[ContasReceber].valor) valor, 
					($tb[ContasReceber].valorRecebido) recebido, 
					($tb[ContasReceber].valorJuros) juros, 
					($tb[ContasReceber].valorDesconto) desconto, 
					$tb[ContasReceber].status
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
					AND $tb[ContasReceber].$dtBase 
						BETWEEN '$dtInicial' AND '$dtFinal' 
					AND $tb[POP].id = '$idPOP'
				
			";

			
			$consultaBaixados=consultaSQL($sql, $conn);
			
			if($consultaBaixados && contaConsulta($consultaBaixados)>0) {
				# Totalizar
				for($i=0;$i<contaConsulta($consultaBaixados);$i++) {
					$mes=resultadoSQL($consultaBaixados, $i, 'mes');
					$status=resultadoSQL($consultaBaixados, $i, 'status');
					if (strtoupper($status) == "C"){
						$cancelado = resultadoSQL($consultaBaixados, $i, 'valor');
						$total["$mes"][cancelado] += $cancelado;
					}
					else {
						$valor=resultadoSQL($consultaBaixados, $i, 'valor');
						$baixa=resultadoSQL($consultaBaixados, $i, 'baixa');
						#$noPeriodo=resultadoSQL($consultaBaixados, $i, 'noPeriodo');
					
						$recebido=resultadoSQL($consultaBaixados, $i, 'recebido');
						$juros=resultadoSQL($consultaBaixados, $i, 'juros');
						$desconto=resultadoSQL($consultaBaixados, $i, 'desconto');	
						
						if ($matriz[noperiodo]) {
						
							if ($baixa > $mes) {
								#echo "$mes / $baixa<br>";
								$recebido=0;
								$juros=0;
								$desconto=0;
							}
						}
					
						#$mes="$mes / $baixa";
						$liquido = $recebido - $juros;
					
						$total["$mes"][valor] += $valor;
						$total["$mes"][recebido] += $recebido;
						$total["$mes"][juros] += $juros;
						$total["$mes"][desconto] += $desconto;
						$total["$mes"][liquido] += $liquido;
						$total["$mes"][pendente] += $valor - $liquido;
					
					}
				
				}
			}
			
			if(is_array($total)) {
				
				# Cabeçalho
				$matResultado=array();
				$matCabecalho=array($grvMes, "Faturado", "Liquido", "Juros", "Descontos", "Bruto", "Pendente", "Cancelado");
								
				$keys=array_keys($total);
				# Cabeçalho
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela('POP', 'center', '30%', 'tabfundo0');
					itemLinhaTabela($grvMes, 'center', '8%', 'tabfundo0');
					itemLinhaTabela('Faturado no Mês', 'center', '8%', 'tabfundo0');
					itemLinhaTabela("$grvRec Liquido", 'center', '8%', 'tabfundo0');
					itemLinhaTabela('Juros', 'center', '8%', 'tabfundo0');
					itemLinhaTabela('Descontos', 'center', '8%', 'tabfundo0');
					itemLinhaTabela("$grvRec Bruto", 'center', '8%', 'tabfundo0');
					itemLinhaTabela('Pendente (Fat-Liq)', 'center', '8%', 'tabfundo0');
					itemLinhaTabela('Cancela dos', 'center', '8%', 'tabfundo0');
				fechaLinhaTabela();
				
				for($a=0;$a<count($keys);$a++) {
					
					$ano=substr($keys[$a],0,4);
					$mesFormatado=$configMeses[intval(substr($keys[$a], 5, 2))]."/".$ano;
					
					$totalValor=$total[$keys[$a]][valor];
					$totalRecebido=$total[$keys[$a]][recebido];
					$totalPendente=$total[$keys[$a]][pendente];
					$totalJuros=$total[$keys[$a]][juros];
					$totalDesconto=$total[$keys[$a]][desconto];
					$totalCancelado=$total[$keys[$a]][cancelado];
					$totalLiquido=$total[$keys[$a]][liquido];
					$totalPendente=$total[$keys[$a]][pendente];
					
					#matrizes para o relatorio
					$c=0;
					//$matResultado[$matCabecalho[$c++]][$a]=$nomePOP;
					$matResultado[$matCabecalho[$c++]][$a]=$mesFormatado;
					$matResultado[$matCabecalho[$c++]][$a]=formatarValoresForm($totalValor);
					$matResultado[$matCabecalho[$c++]][$a]=formatarValoresForm($totalLiquido);
					$matResultado[$matCabecalho[$c++]][$a]=formatarValoresForm($totalJuros);
					$matResultado[$matCabecalho[$c++]][$a]=formatarValoresForm($totalDesconto);
					$matResultado[$matCabecalho[$c++]][$a]=formatarValoresForm($totalRecebido);
					$matResultado[$matCabecalho[$c++]][$a]=formatarValoresForm($totalPendente);
					$matResultado[$matCabecalho[$c++]][$a]=formatarValoresForm($totalCancelado); //
					
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTabela($nomePOP, 'left', '40%', 'normal10');
						itemLinhaTabela($mesFormatado, 'center', '10%', 'normal10');
						itemLinhaTabela(formatarValoresForm($totalValor), 'right', '10%', 'normal10');
						itemLinhaTabela(formatarValoresForm($totalLiquido), 'right', '10%', 'normal10');
						itemLinhaTabela(formatarValoresForm($totalJuros), 'right', '10%', 'normal10');
						itemLinhaTabela(formatarValoresForm($totalDesconto), 'right', '10%', 'normal10');
						itemLinhaTabela(formatarValoresForm($totalRecebido), 'right', '10%', 'normal10');
						itemLinhaTabela(formatarValoresForm($totalPendente), 'right', '10%', 'normal10');
						itemLinhaTabela(formatarValoresForm($totalCancelado), 'right', '10%', 'normal10'); //						
					fechaLinhaTabela();
					
					$totalGeral[faturado]+=$totalValor;
					$totalGeral[recebido]+=$totalRecebido;
					$totalGeral[liquido]+=$totalLiquido;
					$totalGeral[pendente]+=$totalPendente;
					$totalGeral[juros]+=$totalJuros;
					$totalGeral[desconto]+=$totalDesconto;
					$totalGeral[cancelado]+=$totalCancelado; //
					
				} #fecha laco de montagem de tabela
				
				//calcula o total de todos os pops selecionados.
				$totalTodos[faturado] += $totalGeral[faturado];
				$totalTodos[recebido] += $totalGeral[recebido];
				$totalTodos[liquido]  += $totalGeral[liquido];
				$totalTodos[pendente] += $totalGeral[pendente];
				$totalTodos[juros]    += $totalGeral[juros];
				$totalTodos[desconto] += $totalGeral[desconto];
				$totalTodos[cancelado] += $totalGeral[cancelado];
				
				# Totalizar
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('Totais', 'right', 'middle', '50%', $corFundo, 2, 'tabfundo0');
					itemLinhaTabela(formatarValoresForm($totalGeral[faturado]), 'right', '10%', 'txtaviso');
					itemLinhaTabela(formatarValoresForm($totalGeral[liquido]), 'right', '10%', 'txtok');
					itemLinhaTabela(formatarValoresForm($totalGeral[juros]), 'right', '10%', 'txttrial');
					itemLinhaTabela(formatarValoresForm($totalGeral[desconto]), 'right', '10%', 'txttrial');
					itemLinhaTabela(formatarValoresForm($totalGeral[recebido]), 'right', '10%', 'txtok');
					itemLinhaTabela(formatarValoresForm($totalGeral[pendente]), 'right', '10%', 'txtaviso');
					itemLinhaTabela(formatarValoresForm($totalGeral[cancelado]), 'right', '10%', 'txtaviso');
				fechaLinhaTabela();
				
				if($x+1<contaConsulta($consultaPOP)) itemTabelaNOURL('&nbsp;','left',$corFundo, $numColunas,'normal10');
				
				# Alimentar Array de Detalhe com mais um campo - totais
				$c=0;
				//$matResultado[$matCabecalho[$c++]][$a]='&nbsp;';
				$matResultado[$matCabecalho[$c++]][$a]='<b>Total</b>';
				$matResultado[$matCabecalho[$c++]][$a]='<b>'.formatarValoresForm($totalGeral[faturado]).'</b>';
				$matResultado[$matCabecalho[$c++]][$a]='<b>'.formatarValoresForm($totalGeral[liquido]).'</b>';
				$matResultado[$matCabecalho[$c++]][$a]='<b>'.formatarValoresForm($totalGeral[juros]).'</b>';
				$matResultado[$matCabecalho[$c++]][$a]='<b>'.formatarValoresForm($totalGeral[desconto]).'</b>';
				$matResultado[$matCabecalho[$c++]][$a]='<b>'.formatarValoresForm($totalGeral[recebido]).'</b>';
				$matResultado[$matCabecalho[$c++]][$a]='<b>'.formatarValoresForm($totalGeral[pendente]).'</b>';
				$matResultado[$matCabecalho[$c++]][$a]='<b>'.formatarValoresForm($totalGeral[cancelado]).'</b>';
				
				// caso seja a ultima pagina do relatorio exibe o total geral...
				if ($x+1 == contaConsulta($consultaPOP) ){
					$c = 0; $a++;
					$matResultado[$matCabecalho[$c++]][$a]='<br><b>Total de todos POP:</b>';
					$matResultado[$matCabecalho[$c++]][$a]='<b>'.formatarValoresForm($totalTodos[faturado]).'</b>';
					$matResultado[$matCabecalho[$c++]][$a]='<b>'.formatarValoresForm($totalTodos[liquido]).'</b>';
					$matResultado[$matCabecalho[$c++]][$a]='<b>'.formatarValoresForm($totalTodos[juros]).'</b>';
					$matResultado[$matCabecalho[$c++]][$a]='<b>'.formatarValoresForm($totalTodos[desconto]).'</b>';
					$matResultado[$matCabecalho[$c++]][$a]='<b>'.formatarValoresForm($totalTodos[recebido]).'</b>';
					$matResultado[$matCabecalho[$c++]][$a]='<b>'.formatarValoresForm($totalTodos[pendente]).'</b>';
					$matResultado[$matCabecalho[$c++]][$a]='<b>'.formatarValoresForm($totalTodos[cancelado]).'</b>';
				}
				
				# Alimentar Matriz Geral
				$matrizRelatorio[detalhe]=$matResultado;
				
				# Alimentar Matriz de Header
				$matrizRelatorio[header][TITULO]="$titulo<br>".converteData($dtInicial,'banco','formdata')." até ".converteData($dtFinal,'banco','formdata');;
				$matrizRelatorio[header][POP]=$nomePOP;
				$matrizRelatorio[header][IMG_LOGO]=$html[imagem][logoRelatorio];
				
				# Configurações
				$matrizRelatorio[config][linhas]=20;
				$matrizRelatorio[config][layout]='landscape';
								
				$matrizGrupo[]=$matrizRelatorio;
				
			}
			else {
				# Não há registros
				itemTabelaNOURL('Não foram encontrados faturamentos neste período', 'left', $corFundo, 8, 'txtaviso');
			}
		}
		
		// Exibir valor total do relatorio. adicionado em 20050406 por gustavo

		//exibe o hmtl
		 itemTabelaNOURL('&nbsp;','left',$corFundo,$numColunas ,'normal10');
			novaLinhaTabela($corFundo, '100%');
				novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('Total de todos POP', 'right', 'middle', '50%', $corFundo, 2, 'tabfundo0');
				itemLinhaTabela(formatarValoresForm($totalTodos[faturado]), 'right', '10%', 'txtaviso');
				itemLinhaTabela(formatarValoresForm($totalTodos[liquido]), 'right', '10%', 'txtok');
				itemLinhaTabela(formatarValoresForm($totalTodos[juros]), 'right', '10%', 'txttrial');
				itemLinhaTabela(formatarValoresForm($totalTodos[desconto]), 'right', '10%', 'txttrial');
				itemLinhaTabela(formatarValoresForm($totalTodos[recebido]), 'right', '10%', 'txtok');
				itemLinhaTabela(formatarValoresForm($totalTodos[pendente]), 'right', '10%', 'txtaviso');
				itemLinhaTabela(formatarValoresForm($totalTodos[cancelado]), 'right', '10%', 'txtaviso');
			fechaLinhaTabela();
		//thats all
		
		if(is_array($matrizRelatorio) && count($matrizRelatorio)>0) {
			itemTabelaNOURL('&nbsp;','left',$corFundo, $numColunas,'normal10');
			novaTabela("Arquivos Gerados<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 8);
				# Converter para PDF:
				$arquivo=k_reportHTML2PDF(k_report($matrizGrupo, 'html','faturamento_poptt'),'faturamento_poptt',$matrizRelatorio[config]);
				itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>$titulo</a>",'pdf'), 'center', $corFundo, 8, 'txtaviso');
			fechaTabela();
		}
	}
	else {
		# Não há registros
		itemTabelaNOURL('Não há lançamentos disponíveis', 'left', $corFundo, 7, 'txtaviso');
	}
		
	fechaTabela();
}



function consultaFaturamentoClientesPOP($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $html, $sessLogin, $conn, $tb, $limites, $configMeses;

	$dtBase='dtVencimento';
	$ttBase=' - Por Vencimento';
	$grvMes='Vencimento';

	# Formatar Datas
//	$matriz[dtInicial]=formatarData($matriz[dtInicial]);
//	$matriz[dtFinal]=formatarData($matriz[dtFinal]);
//	$dtInicial=substr($matriz[dtInicial],2,4)."/".substr($matriz[dtInicial],0,2).'/01 00:00:00';
//	$dtFinal=substr($matriz[dtFinal],2,4)."/".substr($matriz[dtFinal],0,2).'/'.dataDiasMes(substr($matriz[dtFinal],0,2))." 23:59:59";	
			# Formatar Datas
		if ($matriz[dtInicial]) {
			$data=formatarData($matriz[dtInicial]);
			$dtInicial=substr($data,4,4)."-".substr($data,2,2).'-'.substr($data,0,2).' 00:00:00';
		}
	
		if ($matriz[dtFinal]) {
			$data=formatarData($matriz[dtFinal]);
			$dtFinal=substr($data,4,4)."-".substr($data,2,2).'-'.substr($data,0,2).' 23:59:59';
		}
	
		// Ajusta o sql para determinar o periodo escolhido
		$sqlDT="";
		if($matriz[dtInicial] && $matriz[dtFinal]) {
			$sqlDT=" AND $tb[ContasReceber].$dtBase between '$dtInicial' and '$dtFinal' ";
			$periodo="de ".$dtInicial." até ".$dtFinal;
		} 
		elseif ($matriz[dtInicial]) {
			$sqlDT=" AND $tb[ContasReceber].$dtBase.dtBaixa >= '$dtInicial' ";
			$periodo="a partir de ".$dtInicial;
		} 
		elseif ($matriz[dtFinal])  {
			$sqlDT=" AND $tb[ContasReceber].$dtBase <= '$dtFinal' ";
			$periodo="até ".$dtFinal;
		}	
//	

	
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
	$alinhar=array("left",   "left",   "right",    "right",   "right", "right",    "right", "right");
	$numColunas=count($gravata);
	
	$matResultado=array();
	$matCabecalho=$gravata;
	$l=0;
	
	$titulo="Faturamento Detalhado por Clientes";
	if ($matriz[noperiodo]) $titulo.=" - Recebido no período";
	
	
	echo "<br>";
	novaTabela("[$titulo]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, $numColunas);
	
	# Consultar POPs
	if($consultaPOP && contaConsulta($consultaPOP)>0) {
		
		for($x=0;$x<contaConsulta($consultaPOP);$x++) {
		
			$idPOP=resultadoSQL($consultaPOP, $x, 'id');
			$nomePOP=resultadoSQL($consultaPOP, $x, 'nome');
			$totalGeral=array();
			$total=array();
			
			
			//Totalizar
			$matRecebido=array(); 
			$matJuros=array();   
			$matDesconto=array();
			$matTotal=array();   
			$matPendente=array();
			
			# Cabeçalho
			itemTabelaNOURL("POP: $nomePOP", 'center', $corFundo, $numColunas, 'tabfundo0');
			
		
			$sql="
				SELECT 
					$tb[PessoasTipos].id idPessoaTipo, 
					$tb[Pessoas].nome nomePessoa, 
					LEFT($tb[ContasReceber].dtVencimento,7) mes, 
					LEFT($tb[ContasReceber].dtBaixa,7) baixa, 
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
					 $sqlDT 
					AND $tb[POP].id='$idPOP'
				GROUP BY 
					$tb[PessoasTipos].id,
					LEFT($tb[ContasReceber].$dtBase,7)
				ORDER BY 
					$tb[Pessoas].nome, 
					$tb[ContasReceber].$dtBase 	
			";
			
			$consultaBaixados=consultaSQL($sql, $conn);
			
			if($consultaBaixados && contaConsulta($consultaBaixados)>0) {
				# Totalizar
//				$matRecebido=array(); //movido para antes do if, pelo motivo que se vc
//				$matJuros=array();    //possuir um pop sem $consultaBaixados, nao vai zerar
//				$matDesconto=array(); //seus valores resultando em um valor totalmente errado
//				$matTotal=array();    // gustavo 20050415
//				$matPendente=array();
				
				for($i=0;$i<contaConsulta($consultaBaixados);$i++) {
					$idPessoaTipo=resultadoSQL($consultaBaixados, $i, 'idPessoaTipo');
					$nomePessoa=resultadoSQL($consultaBaixados, $i, 'nomePessoa');
					$valor=resultadoSQL($consultaBaixados, $i, 'valor');
					$recebido=resultadoSQL($consultaBaixados, $i, 'recebido');
					$juros=resultadoSQL($consultaBaixados, $i, 'juros');
					$desconto=resultadoSQL($consultaBaixados, $i, 'desconto');
					$mes=resultadoSQL($consultaBaixados, $i, 'mes');
					$baixa=resultadoSQL($consultaBaixados, $i, 'baixa');
					$liquido=$recebido-$juros;
					
					if ($matriz[noperiodo]) {
						if ($baixa > $mes) {
							#echo "$mes / $baixa<br>";
							$matPendente["$idPessoaTipo"]["$mes"] += $valor;
							$recebido=0;
							$juros=0;
							$desconto=0;
							$liquido=0;
						}
					}
					
					$matDetalhe["$idPessoaTipo"][nomePessoa] = $nomePessoa;
					$matTotal["$idPessoaTipo"]["$mes"] += $valor;
					$matRecebido["$idPessoaTipo"]["$mes"] += $recebido;
					$matLiquido["$idPessoaTipo"]["$mes"] += $liquido;
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
					LEFT($tb[ContasReceber].$dtBase,7) mes, 
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
					AND $tb[ContasReceber].$dtBase 
						BETWEEN '$dtInicial' AND '$dtFinal' 
					AND $tb[POP].id='$idPOP'
				GROUP BY 
					$tb[PessoasTipos].id,
					LEFT($tb[ContasReceber].$dtBase,7)
				ORDER BY 
					$tb[Pessoas].nome,
					$tb[ContasReceber].$dtBase				
			";

			$consultaPendentes=consultaSQL($sql, $conn);
			
			if($consultaPendentes && contaConsulta($consultaPendentes)>0) {
				# Totalizar
				
				for($i=0;$i<contaConsulta($consultaPendentes);$i++) {
					$idPessoaTipo=resultadoSQL($consultaPendentes, $i, 'idPessoaTipo');
					$nomePessoa=resultadoSQL($consultaPendentes, $i, 'nomePessoa');
					$valor=resultadoSQL($consultaPendentes, $i, 'valor');
					$mes=resultadoSQL($consultaPendentes, $i, 'mes');
//					$baixa=resultadoSQL($consultaBaixados, $i, 'baixa');
					$liquido=$recebido-$juros;
					
					$matDetalhe["$idPessoaTipo"][nomePessoa] = $nomePessoa;
					$matPendente["$idPessoaTipo"]["$mes"] += $valor;
					$matTotal["$idPessoaTipo"]["$mes"] += $valor;
				}
				
			}

			if(is_array($matTotal)) {
				
				$keys=array_keys($matTotal);
				
				# Cabeçalho
				
				novaLinhaTabela($corFundo, '100%');
					$cc=0;
					foreach ($gravata as $label) {
						itemLinhaTabela($label, $alinhar["$cc"], $largura[$cc], 'tabfundo0');
						$cc++;
					}
				fechaLinhaTabela();
				
				for($a=0;$a<count($keys);$a++) {

					$meses=array_keys($matTotal[$keys[$a]]);
					
					if(is_array($meses)) {
						
						for($m=0;$m<count($meses);$m++) {
							
							$mes=$meses[$m];
							$ano=substr($mes,0,4);
							$mesFormatado=$configMeses[intval(substr($mes, 5, 2))]."/".$ano;
						
							$nomePessoa=$matDetalhe[$keys[$a]][nomePessoa];
							$totalPendente=$matPendente[$keys[$a]][$mes];
							$totalValor=$matTotal[$keys[$a]][$mes];
							$totalRecebido=$matRecebido[$keys[$a]][$mes];
							$totalJuros=$matJuros[$keys[$a]][$mes];
							$totalDesconto=$matDesconto[$keys[$a]][$mes];
							$totalLiquido=$matLiquido[$keys[$a]][$mes];
							
							if($matriz[bntConfirmar]){
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
							}
							
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
				$totalTodos[cancelado] += $totalGeral[cancelado];
				
				# Totalizar
				if($matriz[bntConfirmar]){
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
				}
				if($x+1<contaConsulta($consultaPOP)) itemTabelaNOURL('&nbsp;','left',$corFundo,	$numColunas ,'normal10');
				
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
				
				// caso seja a ultima pagina do relatorio exibe o total geral... 20050407 gustavo
				if ($x+1 == contaConsulta($consultaPOP) ){
					$c = 0;
					$matResultado[$matCabecalho[$c++]][$y]='<br><b>Total de todos POP:</b>';
					$matResultado[$matCabecalho[$c++]][$y]='<b>'.formatarValoresForm($totalTodos[faturado]).'</b>';
					$matResultado[$matCabecalho[$c++]][$y]='<b>'.formatarValoresForm($totalTodos[liquido]).'</b>';
					$matResultado[$matCabecalho[$c++]][$y]='<b>'.formatarValoresForm($totalTodos[juros]).'</b>';
					$matResultado[$matCabecalho[$c++]][$y]='<b>'.formatarValoresForm($totalTodos[desconto]).'</b>';
					$matResultado[$matCabecalho[$c++]][$y]='<b>'.formatarValoresForm($totalTodos[recebido]).'</b>';
					$matResultado[$matCabecalho[$c++]][$y]='<b>'.formatarValoresForm($totalTodos[pendente]).'</b>';
					$matResultado[$matCabecalho[$c++]][$y]='<b>'.formatarValoresForm($totalTodos[cancelado]).'</b>';
				}//
				
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
				if(!$matriz[bntRelatorio]){
					 itemTabelaNOURL('Não foram encontrados faturamentos neste período', 'center', $corFundo, $numColunas, 'txtaviso');
					itemTabelaNOURL('&nbsp;','left',$corFundo,	$numColunas ,'normal10');
				}
			}
		}
		 //exibe o hmtl
		 itemTabelaNOURL('&nbsp;','left',$corFundo,$numColunas ,'normal10');
			novaLinhaTabela($corFundo, '100%');
				novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('Total de todos POP', 'right', 'middle', '50%', $corFundo, 2, 'tabfundo0');
				itemLinhaTabela(formatarValoresForm($totalTodos[faturado]), 'right', '10%', 'txtaviso');
				itemLinhaTabela(formatarValoresForm($totalTodos[liquido]), 'right', '10%', 'txtok');
				itemLinhaTabela(formatarValoresForm($totalTodos[juros]), 'right', '10%', 'txttrial');
				itemLinhaTabela(formatarValoresForm($totalTodos[desconto]), 'right', '10%', 'txttrial');
				itemLinhaTabela(formatarValoresForm($totalTodos[recebido]), 'right', '10%', 'txtok');
				itemLinhaTabela(formatarValoresForm($totalTodos[pendente]), 'right', '10%', 'txtaviso');
			fechaLinhaTabela();
		//thats all
		
		if (count($matrizGrupo)>0 && $matriz[bntRelatorio]) {
			itemTabelaNOURL('&nbsp;','left',$corFundo,$numColunas,'normal10');
			novaTabela("Arquivos Gerados<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, $numColunas);
				# Converter para PDF:
				$arquivo=k_reportHTML2PDF(k_report($matrizGrupo, 'html','faturamento_pop'),'faturamento_pop',$matrizRelatorio[config]);
				itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>Relatório $titulo</a>",'pdf'), 'center', $corFundo, $numColunas, 'txtaviso');
			fechaTabela();
		}
	}
	else {
		# Não há registros
		itemTabelaNOURL('Não há lançamentos disponíveis', 'left', $corFundo, 7, 'txtaviso');
	}
		
	fechaTabela();
}


/*

SELECT LEFT(ContasAReceber.dtVencimento,7) mes, SUM(ContasAReceber.valor) valor, SUM(ContasAReceber.valorRecebido) recebido, SUM(ContasAReceber.valorJuros) juros, SUM(ContasAReceber.valorDesconto) desconto  FROM Pop, Pessoas, PessoasTipos, ContasAReceber, DocumentosGerados WHERE ContasAReceber.idDocumentosGerados = DocumentosGerados.id AND DocumentosGerados.idPessoaTipo = PessoasTipos.id AND PessoasTipos.idPessoa = Pessoas.id AND Pessoas.idPOP = Pop.id AND ContasAReceber.status in ('B', 'P') AND ContasAReceber.dtVencimento BETWEEN '2004/02/01 00:00:00' AND '2004/03/31 23:59:59' AND Pop.id = '7' GROUP BY Pop.id, LEFT(ContasAReceber.dtVencimento,7), ContasAReceber.status  
$sql="
				SELECT 
					LEFT($tb[ContasReceber].$dtBase,7) mes, 
					SUM($tb[ContasReceber].valor) valor, 
					SUM($tb[ContasReceber].valorRecebido) recebido, 
					SUM($tb[ContasReceber].valorJuros) juros, 
					SUM($tb[ContasReceber].valorDesconto) desconto
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
					AND $tb[ContasReceber].$dtBase 
						BETWEEN '$dtInicial' AND '$dtFinal' 
					AND $tb[POP].id = '$idPOP'
				GROUP BY 
					$tb[POP].id, 
					LEFT($tb[ContasReceber].$dtBase,7),
					$tb[ContasReceber].status
			";	
*/
?>
