<?php


/*
 * dependecias para utilizcao
 * 		atualizacao de includes/notalfiscal.php   (funcao calculaDescontoNF)-> faz o calculo de descontos
 * 
 * 
 */
function formRelatorioNotaFiscal ($modulo, $sub, $acao, $registro, $matriz) {
	
	global $corFundo, $corBorda, $retornaHtml, $sessLogin;
	
	$data=dataSistema();
	
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[visualizar] && !$permissao[admin]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {	
		
		# Motrar tabela de busca
		novaTabela2("[Nota Fiscal]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
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
			getPop($matriz);
			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Detalhar:</b><br><span class=normal10>Detalhar relatório por Nota Fiscal</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				( $matriz[detalhar] == 'N' ? $check2 = "CHECKED" : $check1 = "CHECKED" );
				$texto="<input type=radio name=matriz[detalhar] value=S $check1><b>Sim</b> <input type=radio name=matriz[detalhar] value=N $check2><b>Não</b>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			//datas
			getPeriodoDias(8, 9, $matriz);
			
			//valores
			getValores($matriz);
					
			// Botoes Consulta Relatorio
			getBotoesConsRel();
			
		fechaTabela();
	}
}

function relatorioNotaFiscal ($modulo, $sub, $acao, $registro, $matriz) {
	
	global $conn, $corFundo, $corBorda, $html, $tb, $retornaHtml;

	// formata valores vindos do form
	if ($matriz[valorInicial]) $valorInicial = intval($matriz[valorInicial]);
	if ($matriz[valorFinal]) $valorFinal = intval($matriz[valorFinal]);
	
	if ($valorInicial > 0 )	$sqlValorInicial = "AND valor > $valorInicial";
	if ($valorFinal > 0) 	$sqlValorFinal = "AND valor < $valorFinal";
	

	$matriz[dtInicial]=formatarData($matriz[dtInicial]);
	$matriz[dtFinal]=formatarData($matriz[dtFinal]);
	if(!empty ($matriz[dtInicial])) $dtInicial=substr($matriz[dtInicial],4,4)."-".substr($matriz[dtInicial],2,2)."-".substr($matriz[dtInicial],0,2).' 00:00:00';
	if(!empty ($matriz[dtFinal])) $dtFinal=substr($matriz[dtFinal],4,4)."-".substr($matriz[dtFinal],2,2).'-'.substr($matriz[dtFinal],0,2)." 23:59:59";	
	
	$sqlDT='';
	if (!empty($dtInicial) && !empty ($dtFinal)) $sqlDT = "AND $tb[NotaFiscal].dtEmissao BETWEEN '$dtInicial'  AND  '$dtFinal' ";
	elseif (!empty($dtInicial)) $sqlDT = " AND $tb[NotaFiscal].dtEmissao >= '$dtInicial' ";
	elseif (!empty($dtFinal)) $sqlDT = " AND $tb[NotaFiscal].dtEmissao <= '$dtFinal'";
	
						

	$titulo = "Nota Fiscal";
	$data=dataSistema();

	
	if ($matriz[detalhar]=='N'){
		$largura=array(       '25%',		'25%',			'25%',			'25%' );
		$matCabecalho=array(  "Cliente",	"Valor Total",	"Descontos",	'Valor Nota');
		$matAlinhamento=array("left",    "right",	"center");
		
		$cols=1;

	}
	else {
		$largura=array(       '11%',     	'15%',			"10%",			'10%',			'10%',			'20%'  );
		$matCabecalho=array(  "Cliente",	"Data Emissao",	"Valor Total",	"Descontos",	'Valor Nota',	"Opções");
		$matAlinhamento=array("left",    	"left",			"right",		"right",		"right",		"center");
		
		$cols=2;	
	}
	
	
	# Cabeçalho
	echo "<br>";
	novaTabela("[$titulo]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, count($matCabecalho));
	

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
	$totalGeral = 0;
	$totalGeralDescontos = 0;
	$totalGeralNotas = 0;
	$linhaRelatorio=0;
	
	while($matriz[pop][$pp]) {
		
		$sqlPOP="AND $tb[Pessoas].idPOP = ".$matriz[pop][$pp];
		
		$sql="	SELECT 
					Pessoas.idPop, 
					Pessoas.nome cliente, 
					NotaFiscal.id idNotaFiscal,
					NotaFiscal.dtEmissao,
					NotaFiscal.status, 
					sum(ItensNF.qtde * ItensNF.valorUnit) valor
				FROM 
					Pessoas
				INNER JOIN
					PessoasTipos
					On(Pessoas.id = PessoasTipos.idPessoa) 
				INNER JOIN 
					NotaFiscal
					On(PessoasTipos.id = NotaFiscal.idPessoaTipo)
				INNER JOIN
					ItensNF
					On(NotaFiscal.id = ItensNF.idNF) 
				GROUP BY NotaFiscal.id
				Having 
					NotaFiscal.status='I' 
					$sqlPOP	
					$sqlDT 
					$sqlValorInicial
					$sqlValorFinal 
					 

				order by Pessoas.nome, NotaFiscal.dtEmissao
";
			
		if($sql) $consulta=consultaSQL($sql, $conn);
	
		if( $consulta && contaconsulta($consulta) > 0 ) {

			$matResultado=array();
			$valor=0;
			$ttCliente=0;
			$anterior=resultadoSQL($consulta, 0, 'cliente');
					
			if ( ! $matriz[bntRelatorio] ){
				$cor='tabfundo0';
				htmlAbreLinha($cor);
					for ($cc=0;$cc<count($matCabecalho);$cc++) 
						itemLinhaTMNOURL($matCabecalho[$cc], $matAlinhamento[$cc], 'middle', $largura[$cc], $corFundo, 0, $cor);					
				htmlFechaLinha();
			
				htmlAbreLinha($corFundo);
						itemLinhaTMNOURL($anterior, 'left', 'middle', 0, $corFundo, count($matCabecalho), "bold8");	
				htmlFechaLinha();
				
			}
			
			for($a=0;$a<contaConsulta($consulta);$a++) {
				
				$idNotaFiscal=resultadoSQL($consulta, $a, 'idNotaFiscal');
				$cliente=resultadoSQL($consulta, $a, 'cliente');
				if($a+1 < contaConsulta($consulta))	$proximoCliente=resultadoSQL($consulta, $a+1, 'cliente');
				
				$dtEmissao =converteData( resultadoSQL($consulta, $a, 'dtEmissao'), 'banco', 'form');
				$valor = resultadoSQL($consulta, $a, 'valor');
				
				$valorDescontos=calculaDescontoNF($valor);
				$valorNota=$valor - $valorDescontos;
				
				$ttCliente += $valor;
				$ttClienteDescontos += $valorDescontos;
				$ttClienteNota += $valorNota;
				
				$opcoes=htmlMontaOpcao("<a href=?modulo=notafiscal&sub=notafiscal&registro=$idNotaFiscal&status=$status"."&acao=ver>Ver</a>",'ver');
				
				$c=0;

				if ($matriz[detalhar]!='N' ){
					if (!$matriz[bntRelatorio]){
						htmlAbreLinha($corFundo);
							$cc=0;
							itemLinhaTMNOURL('&nbsp;', $matAlinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, "normal9");
							itemLinhaTMNOURL($dtEmissao, $matAlinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, "normal9");
							itemLinhaTMNOURL(formatarValoresForm($valor), $matAlinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, "normal9");
							itemLinhaTMNOURL(formatarValoresForm($valorDescontos), $matAlinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, "normal9");
							itemLinhaTMNOURL(formatarValoresForm($valorNota), $matAlinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, "normal9");
							itemLinhaTMNOURL($opcoes, $matAlinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, "normal9");
						htmlFechaLinha();
					}	
					else{
						$matResultado[$matCabecalho[$c++]][$linhaRelatorio]= $cliente;
						$matResultado[$matCabecalho[$c++]][$linhaRelatorio]= $dtEmissao;
						$matResultado[$matCabecalho[$c++]][$linhaRelatorio]= formatarValoresForm($valor);
						$matResultado[$matCabecalho[$c++]][$linhaRelatorio]= formatarValoresForm($valorDescontos);
						$matResultado[$matCabecalho[$c++]][$linhaRelatorio++]= formatarValoresForm($valorNota);				
					}	
				}	
				
				if ($cliente != $proximoCliente){
					if (!$matriz[bntRelatorio]){
						htmlAbreLinha($corFundo);
							$cc=0;
							itemLinhaTMNOURL('<b>Total do Cliente:</b>', 'right', 'middle', $largura[$cc++], $corFundo, $cols, "bold8");
							itemLinhaTMNOURL(formatarValoresForm($ttCliente), $matAlinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, "normal9");
							itemLinhaTMNOURL(formatarValoresForm($ttClienteDescontos), $matAlinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, "normal9");
							itemLinhaTMNOURL(formatarValoresForm($ttClienteNota), $matAlinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, "normal9");
							if ($matriz[detalhar]=='S') itemLinhaTMNOURL('&nbsp;', $matAlinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, "normal9");
						htmlFechaLinha();
						htmlAbreLinha($corFundo);
							itemLinhaTMNOURL($proximoCliente, 'left', 'middle', '100%', $corFundo, count($matCabecalho), "bold8");	
						htmlFechaLinha();
					}
					$matResultado[$matCabecalho[$c++]][$linhaRelatorio]= $cliente;
					if($matriz[detalhar]!='N') $matResultado[$matCabecalho[$c++]][$linhaRelatorio]= $dtEmissao;					
					$matResultado[$matCabecalho[$c++]][$linhaRelatorio]= formatarValoresForm($valor);
					$matResultado[$matCabecalho[$c++]][$linhaRelatorio]= formatarValoresForm($valorDescontos);
					$matResultado[$matCabecalho[$c++]][$linhaRelatorio++]= formatarValoresForm($valorNota);	
					
					$totalGeral += $ttCliente;
					$totalGeralDescontos += $ttClienteDescontos;
					$totalGeralNotas += $ttClienteNota;
					
					$ttCliente = 0 ;
					$ttClienteDescontos = 0;
					$ttClienteNota = 0;
				}	
					
//				if ($matriz[detalhar]!='N'){
//					$matResultado[$matCabecalho[$c]][$linhaRelatorio]=$campos[$c++]=$valor;
//					$matResultado[$matCabecalho[$c]][$linhaRelatorio++]='&nbsp;';
//				}
//				else{
//					$matResultado[$matCabecalho[$c]][$linhaRelatorio]=$campos[$c++]=converteData($dtEmissao, 'banco', 'form');
//					$matResultado[$matCabecalho[$c]][$linhaRelatorio]=$campos[$c++]=formatarValoresForm($valor);
//					$matResultado[$matCabecalho[$c]][$linhaRelatorio++]='&nbsp;';
//				}		
//				$campos[$c++]=htmlMontaOpcao("<a href=?modulo=notafiscal&sub=notafiscal&registro=$idNotaFiscal&status=$status"."&acao=ver>Ver</a>",'ver');

				#exibe a linha detalhe
				
//				if ( ! $matriz[bntRelatorio] ){
//					htmlAbreLinha($corFundo);
//						for ($cc=0; $cc<count($campos); $cc++) {
//							itemLinhaTMNOURL($campos[$cc], $matAlinhamento[$cc], 'middle', $largura[$cc], $corFundo, 0, "normal9");
//						}
//					htmlFechaLinha();
//				}
								
				$anterior=$cliente;		
			} #fecha laco de montagem de tabela
			
			
			if ( !$matriz[bntRelatorio] ){
				$i = 0;
				itemLinhaTMNOURL( "<b>Valor Total:</b>", 'right', 'middle', $largura[$i++],  $corFundo, $cols, 'tabfundo0' );
				itemLinhaTMNOURL( number_format( $totalGeral, 2,',','.' ), 'right', 'middle', $largura[$i++], $corFundo, 0, 'tabfundo0' );
				itemLinhaTMNOURL( number_format( $totalGeralDescontos, 2,',','.' ), 'right', 'middle', $largura[$i++], $corFundo, 0, 'tabfundo0' );
				itemLinhaTMNOURL( number_format( $totalGeralNotas, 2,',','.' ), 'right', 'middle', $largura[$i++], $corFundo, 0, 'tabfundo0' );
				if ($matriz[detalhar]=='S') itemLinhaTMNOURL('&nbsp;', 'right', 'middle', $largura[$i++], $corFundo, 0, 'tabfundo0' );
				
			}
			
			$i = 0;
			$matResultado[$matCabecalho[$i++]][$linhaRelatorio] = "&nbsp;";
			$matResultado[$matCabecalho[$i++]][$linhaRelatorio] = "<b>Valor Total:</b>";
			$matResultado[$matCabecalho[$i++]][$linhaRelatorio] = "<b>".number_format( $totalGeral, 2, ',', '.' )."</b>";
			
			# Alimentar Matriz Geral
			$matrizRelatorio[detalhe]=$matResultado;
			
			# Alimentar Matriz de Header
			$matrizRelatorio[header][TITULO]="RELATÓRIO DE $titulo<br>".converteData($dtInicial,'banco','formdata')." até ".converteData($dtFinal,'banco','formdata');
			$matrizRelatorio[header][POP]=$pop;
			$matrizRelatorio[header][IMG_LOGO]=$html[imagem][logoRelatorio];
			
			# Alimentar Matriz de configurações
			$matrizRelatorio[config][linhas]=40;
			$matrizRelatorio[config][layout]='portrait';
			
			$matrizGrupo[]=$matrizRelatorio;
			
		} //sql
		
		$pp++;
	}
	
	if(is_array($matrizRelatorio) && count($matrizRelatorio)>0) {
		if ($matriz[bntRelatorio]) {
			# Converter para PDF:
			$arquivo=k_reportHTML2PDF(k_report($matrizGrupo, 'html','faturamento_baixaservico'),'notaFiscal', $matrizRelatorio[config]);
			itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>Relatório de Títulos em Aberto</a>",'pdf'), 'center', $corFundo, 3, 'txtaviso');
		}
	}
	else {
		# Não há registros
		itemTabelaNOURL('Não há registros disponíveis que obedecam estes critérios', 'left', $corFundo, 7, 'txtaviso');	
	}
	

	fechaTabela();
	return(0);
	
}
?>
