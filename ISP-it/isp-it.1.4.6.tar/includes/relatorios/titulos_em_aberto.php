<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 15/04/2004
# Ultima alteração: 15/04/2004
#    Alteração No.: 001
#
# Função:
#      Includes de Relatórios



# função para form de seleção de filtros de faturamento
function formRelatorioTitulosAberto($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $retornaHtml, $sessLogin;
	
	$data=dataSistema();
	
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {	
		
		# Motrar tabela de busca
		novaTabela2("[Titulos em Aberto]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			menuOpcAdicional($modulo, $sub, $acao, $registro);
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro value=$registro>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			//POP
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>POP:</b><br>
				<span class=normal10>Selecione o(s) POP de Acesso</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				if($matriz[pop_todos]) $opcPOP='checked';
				$texto="<input type=checkbox name=matriz[pop_todos] value=S $opcPOP><b>Todos</b>";
				itemLinhaForm(formSelectPOP($matriz[pop],'pop','multi').$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			// Detalhamento
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Por cliente:</b><br>
				<span class=normal10>Detalha a consulta por cliente</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				if($matriz[porCliente]) $opcDetalhe='checked';
				$texto="<input type=checkbox name=matriz[porCliente] value=S $opcDetalhe><b>Detalhar</b>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			//Periodo
			//formPeriodoMesAno($matriz, $opcDe, $opcAte);
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Mês/Ano Inicial:</b><br>
				<span class=normal10>Informe o mês/ano inicial para consulta</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[dtInicial] size=7 value='$matriz[dtInicial]' onBlur=verificaDataMesAno2(this.value,7)>&nbsp;<span class=txtaviso>(Formato: $data[mes]/$data[ano])</span>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Mês/Ano Final:</b><br>
				<span class=normal10>Informe o mês/ano final para consulta</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[dtFinal] size=7 value='$matriz[dtFinal]'  onBlur=verificaDataMesAno2(this.value,8)>&nbsp;<span class=txtaviso>(Formato: $data[mes]/$data[ano])</span>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			//
			itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
			
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntVisualizar] value=Visualizar class=submit>";
				$texto.="&nbsp;&nbsp;&nbsp;<input type=submit name=matriz[bntRelatorio] value='Gerar Relatório' class=submit2>";
				itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
		fechaTabela();
		
		if($matriz[bntVisualizar] || $matriz[bntGerar]) {
			
			if($matriz[bntVisualizar]) {
				$matriz[acao]='visualizar';
				$retornaHtml=0;
				echo "<br>";
				consultaTitulosAberto($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($matriz[bntRelatorio]) {
				# Geração de Relatorio
				relatorioTitulosAberto($modulo, $sub, $acao, $registro, $matriz);
			}
			
			// Gera PDF do relatorio
			/*
			elseif($matriz[bntGerar]) {
				$matriz[acao]='gerar';
				$retornaHtml=1;
				$corpo=consultaInadimplencia($modulo, $sub, $acao, $registro, $matriz);
				$retornaHtml=0;
				$arquivo=htmlGerarRelatorio($matriz, 
											"imagens/logo_pequeno.jpg", 
											":: TDKOM ::", 
											"Relatório de Inadimplência", 
											$corpo, 
											"rodape");
				
				# Converter HTML (arquivo) para PDF (arquivo)
				$pdfFile=pdfConverterArquivoHF($arquivo, $matriz);
		
				$texto=htmlMontaOpcao("<a href=$pdfFile>Relatório de Inadimplência</a>", 'pdf');
		
				# Selecionar parametros do dominio
				echo "<br>";
				novaTabela("Arquivos Gerados", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 0);
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTabela($texto, 'center', '70%', 'normal10');
					fechaLinhaTabela();
				fechaTabela();
			}
			*/	
			$retornaHtml=0;
		}
	}
	
}


/**
 * @return unknown
 * @param unknown $modulo
 * @param unknown $sub
 * @param unknown $acao
 * @param unknown $registro
 * @param unknown $matriz
 * @desc Enter description here...
*/
function relatorioTitulosAberto($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $corFundo, $corBorda, $html, $tb, $retornaHtml;
	
	# Formatar Datas
	if ($matriz[dtInicial]) {
		$matriz[dtInicial]=formatarData($matriz[dtInicial]);
		if ($matriz[diaDe]) $dia=$matriz[diaDe];
		else $dia='01';
		$dtInicial=substr($matriz[dtInicial],2,4)."/".substr($matriz[dtInicial],0,2).'/'.$dia.' 00:00:00';
	}

	if ($matriz[dtFinal]) {
		$matriz[dtFinal]=formatarData($matriz[dtFinal]);
		if ($matriz[diaAte]) $dia=$matriz[diaAte];
		else $dia=dataDiasMes(substr($matriz[dtFinal],0,2));
		$dtFinal=substr($matriz[dtFinal],2,4)."/".substr($matriz[dtFinal],0,2).'/'.$dia.' 23:59:59';
	}

	
	// Ajusta o sql para determinar o periodo escolhido
	$sqlDT="";
	if($matriz[dtInicial] && $matriz[dtFinal]) {
		$sqlDT=" AND $tb[ContasReceber].dtVencimento between '$dtInicial' and '$dtFinal' ";
	} 
	elseif ($matriz[dtInicial]) {
		$sqlDT=" AND $tb[ContasReceber].dtVencimento >= '$dtInicial' ";
	} 
	elseif ($matriz[dtFinal])  {
		$sqlDT=" AND $tb[ContasReceber].dtVencimento <= '$dtFinal' ";
	}

		
	// Se forem todos os pops gera a lista na matriz
	if($matriz[pop_todos]) {
		$consultaPop=buscaPOP('','','todos', 'id');
		if( $consultaPop && contaConsulta($consultaPop) ) {
			for($a=0;$a<contaConsulta($consultaPop);$a++) {
				$matriz[pop][]=resultadoSQL($consultaPop, $a, 'id');
			}
		}
	}
	
	$pp=0;
	if ($matriz[porCliente]) {
		$coluna[1]='55%';
		$coluna[2]='15%';
		$tituloCliente="Cliente";
	}
	else {	
		$coluna[1]='1%';
		$coluna[2]='69%';
		$tituloCliente="&nbsp;";
	}
	$coluna[3]='15%';
	$coluna[4]='15%';
	
	if ($matriz[porCliente]) {
		$alinha='left';
		$corColuna=$corFundo;
		$span=0;
	}
	else {
		$alinha='center';
		$corColuna="#aaaaaa";
		$span=2;
	}
	
	$totalGeral = 0;
	if( count($matriz['pop']) ){
		# Cabeçalho
		echo "<br>";
		novaTabela("[Títulos em Aberto]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
		
		while($matriz[pop][$pp]) {
			
			// zero acumuladores
			$faturado=0.00;
			$recebido=0.00;
			$ttCliente=0.00;
			
			// Inadimplencia por POP sem detalhes
			$sql="SELECT 
						$tb[POP].id as id,
						$tb[POP].nome as POP, 
						$tb[Pessoas].nome as cliente, 
						$tb[PessoasTipos].id as idPessoaTipo, 
						sum($tb[ContasReceber].valor) as valor,
						$tb[ContasReceber].dtVencimento as data
					FROM 
						$tb[POP],
						$tb[Pessoas], 
						$tb[ContasReceber], 
						$tb[PessoasTipos], 
						$tb[DocumentosGerados] 
					WHERE 
						$tb[POP].id = $tb[Pessoas].idPOP AND 
						$tb[Pessoas].id = $tb[PessoasTipos].idPessoa AND 
						$tb[PessoasTipos].id = $tb[DocumentosGerados].idPessoaTipo AND 
						$tb[ContasReceber].idDocumentosGerados = $tb[DocumentosGerados].id AND
						$tb[ContasReceber].status = 'P' AND
						$tb[POP].id = ".$matriz[pop][$pp]."
						$sqlDT 
					GROUP BY 
						$tb[PessoasTipos].id, 
						$tb[ContasReceber].dtVencimento, 
						$tb[POP].id
					ORDER BY
						$tb[POP].nome,
						$tb[Pessoas].nome,
						$tb[ContasReceber].dtVencimento";
			
			if($sql) $consultaPop=consultaSQL($sql, $conn);
		
			if( $consultaPop && contaconsulta($consultaPop) > 0 ) {
	
				$pop=resultadoSQL($consultaPop, 0, "POP");
				$id=resultadoSQL($consultaPop, 0, "id");
				
				$matResultado=array();
				$matCabecalho=array("Cliente", "Data de Vencimento", "Valor");
				
				$tmpCliente='';
				$totalCliente=0;
				$totalPOP=0;
				$linhaRelatorio=0;
				
				for($a=0;$a<contaConsulta($consultaPop);$a++) {
	
					$idPessoaTipo=resultadoSQL($consultaPop, $a, 'idPessoaTipo');
					$cliente=resultadoSQL($consultaPop, $a, 'cliente');
					$valor=resultadoSQL($consultaPop, $a, "valor");
					$data=converteData(resultadoSQL($consultaPop, $a, "data"), 'banco', 'formdata');
					
					$totalCliente+=$valor;
					$totalPOP+=$valor;
	
					$c=0;
					$matResultado[$matCabecalho[$c++]][$linhaRelatorio]=$cliente;
					$matResultado[$matCabecalho[$c++]][$linhaRelatorio]=$data;
					$matResultado[$matCabecalho[$c++]][$linhaRelatorio++]=formatarValoresForm($valor);
					
					# Verificar necessidade de totalizar (se $cliente < $tmpCliente)
					if(($a+1) < contaConsulta($consultaPop) && $idPessoaTipo != resultadoSQL($consultaPop, $a+1, 'idPessoaTipo')) {
						# Mostrar Total
						$c=0;
						$matResultado[$matCabecalho[$c++]][$linhaRelatorio]='&nbsp;';
						$matResultado[$matCabecalho[$c++]][$linhaRelatorio]='&nbsp;';
						$matResultado[$matCabecalho[$c++]][$linhaRelatorio++]='<!--quebra--><b>Total Devido: '.formatarValoresForm($totalCliente).'</b>';
						
						$totalCliente=0;
					}
					else if( ($a+1) == contaConsulta($consultaPop)) {
						# Mostrar Total
						$c=0;
						$matResultado[$matCabecalho[$c++]][$linhaRelatorio]='&nbsp;';
						$matResultado[$matCabecalho[$c++]][$linhaRelatorio]='&nbsp;';
						$matResultado[$matCabecalho[$c++]][$linhaRelatorio++]='<!--quebra--><b>Total Devido: '.formatarValoresForm($totalCliente).'</b>';
						
						$totalCliente=0;
					}
					
					
				} #fecha laco de montagem de tabela
				
				$c=0;
				$matResultado[$matCabecalho[$c++]][$linhaRelatorio]='&nbsp;';
				$matResultado[$matCabecalho[$c++]][$linhaRelatorio]='&nbsp;';
				$matResultado[$matCabecalho[$c++]][$linhaRelatorio++]='<hr><b>Total do POP: '.formatarValoresForm($totalPOP).'</b>';
				
				$totalGeral+=$totalPOP;
				$totalPOP=0;
				
				# Alimentar Matriz Geral
				$matrizRelatorio[detalhe]=$matResultado;
				
				# Alimentar Matriz de Header
				$matrizRelatorio[header][TITULO]="RELATÓRIO DE TÍTULOS EM ABERTO<br>".converteData($dtInicial,'banco','formdata')." até ".converteData($dtFinal,'banco','formdata');
				$matrizRelatorio[header][POP]=$pop;
				$matrizRelatorio[header][IMG_LOGO]=$html[imagem][logoRelatorio];
				
				# Alimentar Matriz de configurações
				$matrizRelatorio[config][linhas]=40;
				$matrizRelatorio[config][layout]='portrait';
				
				$matrizGrupo[]=$matrizRelatorio;
				
			} //sql
			$pp++;
		}
		
		fechaTabela();
		
	}
	else{
		echo "<br>";
		$msg="Você esqueceu de selecionar o pop.";
		avisoNOURL("Aviso: Consulta<a name=ancora></a>", $msg, 400);
	}
		
	if(is_array($matrizRelatorio) && count($matrizRelatorio)>0) {
		# Converter para PDF:
		$arquivo=k_reportHTML2PDF(k_report($matrizGrupo, 'html','inadip'),'inadip', $matrizRelatorio[config]);
		itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>Relatório de Títulos em Aberto</a>",'pdf'), 'center', $corFundo, 3, 'txtaviso');
	}
	else {
		# Não há registros
		itemTabelaNOURL('Não há lançamentos disponíveis', 'left', $corFundo, 7, 'txtaviso');	
	}
	

	return(0);
	
}


?>