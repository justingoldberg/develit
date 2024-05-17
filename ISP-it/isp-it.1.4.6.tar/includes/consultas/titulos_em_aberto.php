<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 15/04/2004
# Ultima alteração: 15/04/2004
#    Alteração No.: 001
#
# Função:
#      Consulta de Titulos em Aberto

/**
 * @return unknown
 * @param $modulo
 * @param $sub
 * @param $acao
 * @param $registro
 * @param $matriz
 * @desc Gera o relatorio de inadimplentes, por POP e período, podendo detalhar por cliente.
*/
function consultaTitulosAberto($modulo, $sub, $acao, $registro, $matriz) {

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
		$consultaPop=buscaPOP('A','status','igual', 'id');
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
	
	if( count($matriz['pop']) ){
	
		# Cabeçalho
		novaTabela("[Titulos em Aberto]<a name=ancora></a>", "center", '100%', 0, 0, 0, $corFundo, $corBorda, 0);
		htmlAbreLinha($corFundo);
		htmlAbreColuna('100%', 'center', $corFundo, 0, 'normal9');
		
		while($matriz[pop][$pp]) {
			
			// zero acumuladores
			$faturado=0.00;
			$recebido=0.00;
			$ttCliente=0.00;
			
			// Inadimplencia por POP sem detalhes
			$sql="SELECT 
						$tb[POP].id as id,
						$tb[POP].nome as POP, ";
			
			if ($matriz[porCliente]) {
				 $sql.="$tb[Pessoas].nome as cliente, ".
				 	   "$tb[PessoasTipos].id as pessoa, ";
			}
			
			$sql.="		sum($tb[ContasReceber].valor) as valor,
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
						$tb[POP].id = ".$matriz[pop][$pp];
			$sql.="		$sqlDT ";
			$sql.="GROUP BY ";
			
			if ($matriz[porCliente]) $sql.="$tb[Pessoas].id, ";
			
			$sql.="		$tb[ContasReceber].dtVencimento, 
						$tb[POP].id ";
			$sql.="ORDER BY
						$tb[POP].nome,";
			if ($matriz[porCliente]) {
				$sql.=" $tb[Pessoas].nome,";
			}
			$sql.="	 	$tb[ContasReceber].dtVencimento";
			
			//echo "<br>SQL: $sql";
			
			if($sql) $consultaPop=consultaSQL($sql, $conn);
		
			if( $consultaPop && contaconsulta($consultaPop) > 0 ) {
	
				$pop=resultadoSQL($consultaPop, 0, "POP");
				$id=resultadoSQL($consultaPop, 0, "id");
				
				echo "\n<br>\n";
				
				if ($matriz[porCliente]) novaTabela2($pop,'left', '100%', 0, 2, 1, $corFundo, $corBorda, 4);
				else novaTabela($pop,'left', '100%', 0, 2, 1, $corFundo, $corBorda, 4);
				
				$cor=$corFundo;
				
				htmlAbreLinha($cor);
					if ($matriz[porCliente]) itemLinhaTMNOURL("<b>$tituloCliente</b>", 'left', 'middle', $coluna[1], $cor, 0, 'tabfundo0');
					itemLinhaTMNOURL('<b>Data Vencimento</b>', $alinha, 'middle', $coluna[2], $cor, $span, 'tabfundo0');
					itemLinhaTMNOURL('<b>Valor</b>', 'right', 'middle',$coluna[3], $cor, 0, 'tabfundo0');
					itemLinhaTMNOURL('<b>opções</b>', 'center', 'middle', $coluna[4], $cor, 0, 'tabfundo0');
				htmlFechaLinha();
					
				for($a=0;$a<contaConsulta($consultaPop);$a++) {
					// Verifica se o nome do cliente é repetido
					if ($matriz[porCliente]) {
						if($cliente==resultadoSQL($consultaPop, $a, "cliente")) {
							$clienteVai=0;
						} else {
							$clienteVai=1;
						}
						$cliente=resultadoSQL($consultaPop, $a, "cliente");
					} else {
						$cliente="&nbsp;";
					}
					
					$valor=resultadoSQL($consultaPop, $a, "valor");
					$data=converteData(resultadoSQL($consultaPop, $a, "data"), 'banco', 'formdata');
					$dataBD=resultadoSQL($consultaPop, $a, "data");
					
					// Opcoes
					/*
					http://orca.devel-it.com.br/isp/index.php?modulo=relatorios&
					sub=inadimplencia&
					matriz[pop][0]=7&
					matriz[diaDe]=10&
					matriz[mesDe]=01/2004&
					matriz[diaAte]=10&
					matriz[mesAte]=01/2004&
					matriz[porCliente]=1&
					matriz[bntConfirmar]=1
					*/
					if ($matriz[porCliente]) {
						$pessoa=resultadoSQL($consultaPop, $a, "pessoa");
						$opcoes=htmlMontaOpcao("<a href=?modulo=faturamento&sub=clientes&acao=historico&registro=0&matriz[idPessoaTipo]=$pessoa>Detalhes</a>", 'relatorio');
					} else {
						$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&".
						                                "matriz[pop][0]=$id&".
						                                "matriz[diaDe]=".substr($data, 0, 2)."&".
						                                "matriz[dtInicial]=".substr($data, 3, 2)."/".substr($data, 6, 4)."&".
						                                "matriz[diaAte]=".substr($data, 0, 2)."&".
						                                "matriz[dtFinal]=".substr($data, 3, 2)."/".substr($data, 6, 4)."&".
						                                "matriz[porCliente]=1&".
						                                "matriz[bntConfirmar]=1>".
						                                "Detalhar</a>",'relatorio');				
					}
					
					$recebido+=$valor;
					
					if ($clienteVai && $ttCliente > 0) {
						$cor="#eeeeee";		
						$estilo="txtok";				
						htmlAbreLinha($cor);
							if ($matriz[porCliente]) itemLinhaTMNOURL('&nbsp;', 'right', 'middle', $coluna[1], $cor, 0, $estilo);
							itemLinhaTMNOURL('<b>Valor devido</b>', 'right', 'middle', $coluna[2], $cor, $span, $estilo);
							itemLinhaTMNOURL('<b>'.formatarValoresForm($ttCliente).'</b>', 'right', 'middle', $coluna[3], $cor, 0, $estilo);
							itemLinhaTMNOURL('&nbsp;', 'left', 'middle', $coluna[4], $cor, 0, $estilo);
						htmlFechaLinha();
						$ttCliente=0;
					}
					
					$ttCliente+=$valor;
					
					
					htmlAbreLinha($corFundo);
						if ($matriz[porCliente]) {
							if($clienteVai) itemLinhaTMNOURL("<b>$cliente</b>", 'left', 'middle', $coluna[1], $corFundo, 0, "normal10");		
							else itemLinhaTMNOURL("&nbsp;", 'left', 'middle', $coluna[1], $corColuna, 0, "normal10");		
						}
						itemLinhaTMNOURL($data, $alinha, 'middle', $coluna[2], $corFundo, $span, "normal10");
						itemLinhaTMNOURL(formatarValoresForm($valor), 'right', 'middle', $coluna[3], $corFundo, 0, "normal10");
						itemLinhaTMNOURL("$opcoes", 'left', 'middle', $coluna[4], $corFundo, 0, 'normal10');
					htmlFechaLinha();
						
					
				} //for
	
				$cor="#ffffee";
				htmlAbreLinha($cor);
					if ($matriz[porCliente]) itemLinhaTMNOURL("&nbsp;", 'right', 'middle', $coluna[1], $cor, 0, "normal10");
					itemLinhaTMNOURL("<b>Total do POP</b>&nbsp;", 'right', 'middle', $coluna[2], $cor, $span, "txtaviso");
					itemLinhaTMNOURL("<b>".formatarValoresForm($recebido)."</b>", 'right', 'middle', $coluna[3], $corFundo, 0, "txtaviso");
					itemLinhaTMNOURL('&nbsp;', 'left', 'middle', $coluna[4], $corFundo, 0, 'txtaviso');
				htmlFechaLinha();
				
				fechaTabela();
				
				
			} //sql
			$pp++;
		} //while
		htmlFechaColuna();
		htmlFechaLinha();
		fechaTabela();
	}
	else{
		echo "<br>";
		$msg="Você esqueceu de selecionar o pop.";
		avisoNOURL("Aviso: Consulta<a name=ancora></a>", $msg, 400);
	}
	
	return(0);
	
}

?>
