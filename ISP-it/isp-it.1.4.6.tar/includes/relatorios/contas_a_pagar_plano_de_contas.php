<?

function formContasAPagarPlanoDeContas( $modulo, $sub, $acao, $registro, $matriz ){
	global $corFundo, $corBorda, $sessLogin;
	
	novaTabela2("[Contas à Pagar - Plano De Contas]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	
		#cabecalho com campos hidden
		abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );
		
		#pop de acesso
		$combo = formSelectPOP($matriz[pop],'pop','multi')."<input type=checkbox name=matriz[pop_todos] value=S $opcPOP><b>Todos</b>";
		getCampo( "combo", "<b>POP:</b><br><span class=normal10>Selecione o POP de Acesso</span>", "", $combo );
		
		#periodo do relatorio
		getPeriodo( 6, 7, $matriz, "Vencimento" );
		
		getDetalharCliente($matriz, ' por conta paga');
		
		getBotoesConsRel();
		
		fechaFormulario();
	
	fechaTabela();
}


function contasAPagarPlanoDeContasPreparaRel( $matriz ){
	global $conn, $tb;
	
	$where = array();
	$where[] = "ContasAPagar.status != 'C'";
	
	if( $matriz['dtInicial'] || $matriz['dtFinal'] ){
		$dtInicial	=	formatarData($matriz[dtInicial]);
		$dtFinal	=	formatarData($matriz[dtFinal]);
		
		if( $dtInicial && $dtFinal ){
			$cond  = " ContasAPagar.dtVencimento between '".substr( $dtInicial,2, 4 )."-".substr( $dtInicial, 0, 2 ). "-01 00:00:00' ";
			$cond .= "AND '" . substr( $dtFinal,2, 4 )."-".substr( $dtFinal, 0, 2 ). "-31 23:59:59' ";
		}
		elseif( $dtInicial ){
			$cond = "ContaAPagar.dtVencimento > '" . substr( $dtInicial,2, 4 )."-".substr( $dtInicial, 0, 2 ). "-01 00:00:00'";
		}
		else{
			$cond = "ContaAPagar.dtVencimento < '" . substr( $dtFinal,2, 4 )."-".substr( $dtFinal, 0, 2 ). "-31 23:59:59' ";
		}
		
		$where[] = $cond ;
		
	}
	
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
	$l = 0;	
	if( $consultaPOP && contaConsulta( $consultaPOP ) ) {
		
		for( $p=0; $p<contaConsulta( $consultaPOP ); $p++ ){
			
			$matResultado = array();
			
			$idPOP=resultadoSQL($consultaPOP, $p, 'id');
			
			$sqlPop = 'ContasAPagar.idPop = '.$idPOP;			
			$sql = "Select 
					  PlanoDeContas.id AS idPlanoDeContas, 
					  PlanoDeContas.nome AS nomePlanoDeContas, 
					  PlanoDeContasSub.id AS idPlanoDeContasSub,
					  PlanoDeContasSub.nome AS nomePlanoDeContasSub, 
					  PlanoDeContasDetalhes.id AS idPlanoDeContasDetalhes,
					  PlanoDeContasDetalhes.nome AS nomePlanoDeContasDetalhes,
					  Pessoas.nome AS nomeFornecedor,
					  ContasAPagar.id AS idContasAPagar,
					  ContasAPagar.valor,
					  ContasAPagar.valorPago,
					  ContasAPagar.dtVencimento,
					  ContasAPagar.status as statusContasAPagar
					From
					  PlanoDeContas
					  LEFT JOIN PlanoDeContasSub
					    On( PlanoDeContas.id = PlanoDeContasSub.idPlanoDeContas )
					  LEFT JOIN PlanoDeContasDetalhes
					    On( PlanoDeContasSub.id = PlanoDeContasDetalhes.idPlanoDeContasSub )
					  LEFT JOIN ContasAPagar
					    On( PlanoDeContasDetalhes.id = ContasAPagar.idPlanoDeContasDetalhes)
					  LEFT JOIN PessoasTipos
					    On( ContasAPagar.idFornecedor = PessoasTipos.id)
					  LEFT JOIN Pessoas
					    On( PessoasTipos.idPessoa = Pessoas.id)".
					( count($where) ? " where " . implode( " AND ", $where) : ""). " AND ".$sqlPop." 
					ORDER BY 
					  PlanoDeContas.nome, PlanoDeContasSub.nome, PlanoDeContasDetalhes.nome, ContasAPagar.dtVencimento, Pessoas.nome";
			
			$consulta = consultaSQL( $sql, $conn );
			
			
			if( $consulta && contaConsulta( $consulta ) ){
				
				$cabecalho = array( 'Plano', 'Mês', 'Valor');
				
				$planoAnt			= 0;
				$planoSubAnt		= 0;
				$planoDetalheAnt	= 0;
				$mesAnt				= 0;
				
				$ttPop 			= 0;
				$ttPlano		= 0;
				$ttSubPlano 	= 0;
				$ttDetalhePlano = 0;
				
				$matResultado = array();
				$l = 0 ;
				
				$espacador = str_repeat("&nbsp;", 4);
				
				for( $i=0; $i < contaConsulta( $consulta ); $i++){
								
					$idPlanoDeContas			= resultadoSQL( $consulta, $i, 'idPlanoDeContas'			);
					$nomePlanoDeContas			= resultadoSQL( $consulta, $i, 'nomePlanoDeContas'			);
					$idPlanoDeContasSub			= resultadoSQL( $consulta, $i, 'idPlanoDeContasSub'			);
					$nomePlanoDeContasSub 		= resultadoSQL( $consulta, $i, 'nomePlanoDeContasSub'		);
					$idPlanoDeContasDetalhes	= resultadoSQL( $consulta, $i, 'idPlanoDeContasDetalhes'	);
					$nomePlanoDeContasDetalhes	= resultadoSQL( $consulta, $i, 'nomePlanoDeContasDetalhes'	);
					
					$fornecedor					= resultadoSQL( $consulta, $i, 'nomeFornecedor'		);
					$valor						= resultadoSQL( $consulta, $i, 'valor'				);
					$valorPago					= resultadoSQL( $consulta, $i, 'valorPago'			);
					$statusContasAPagar			= resultadoSQL( $consulta, $i, 'statusContasAPagar' );
					$dtVencimento 				= converteData(  resultadoSQL( $consulta, $i, 'dtVencimento'), "banco", "formdata");
								
					if( $planoDetalheAnt != $idPlanoDeContasDetalhes || substr( $dtVencimento, 3, 7) != $mesAnt ){
		
						if( $planoSubAnt != $idPlanoDeContasSub ){
							
							if( $planoSubAnt != 0 ){ //nao eh o primeiro exibe o total do sub anterior
								$c=0;
								$matResultado[$cabecalho[$c++]][$l]	= $espacador."Total Sub: ";
								$matResultado[$cabecalho[$c++]][$l] = "&nbsp;";
								$matResultado[$cabecalho[$c++]][$l++] = formatarValoresForm( $ttSubPlano );	
							}
							
							if( $planoAnt != $idPlanoDeContas ){
								
								if( $planoSubAnt != 0 ){
									$c = 0;
									$matResultado[$cabecalho[$c++]][$l]	= "Total Plano: ";
									$matResultado[$cabecalho[$c++]][$l] = "&nbsp;";
									$matResultado[$cabecalho[$c++]][$l++] = formatarValoresForm( $ttPlano );	
								}
								
								$ttPop += $ttPlano;
								
								$ttPlano = 0;
								$c=0;
								
								$matResultado[$cabecalho[$c++]][$l]	= "Plano: " . $nomePlanoDeContas;
								$matResultado[$cabecalho[$c++]][$l] = "&nbsp;";
								$matResultado[$cabecalho[$c++]][$l++] = "&nbsp;";
								
							}
							
							$ttSubPlano = 0;
							$c=0;
							
							$matResultado[$cabecalho[$c++]][$l]	= $espacador."Sub: " . $nomePlanoDeContasSub;
							$matResultado[$cabecalho[$c++]][$l] = "&nbsp;";
							$matResultado[$cabecalho[$c++]][$l++] = "&nbsp;";		
							
						}
						if( $cd ) $matResultado[$cabecalho[2]][$cd] = formatarValoresForm( $ttDetalhePlano );
						
						$c=0;
						$ttDetalhePlano = 0;
						$cd=$l;
						
						$matResultado[$cabecalho[$c++]][$l]	= $espacador.$espacador."Detalhe: " . $nomePlanoDeContasDetalhes;
						$matResultado[$cabecalho[$c++]][$l] = substr( $dtVencimento, 3, 7);
						$matResultado[$cabecalho[$c++]][$l++] = "&nbsp;";		
						
					}
//					if($planoDetalheAnt != 0 )
						$valorFinal =  ( ( $statusContasAPagar == "B" ) ? $valorPago : $valor );
					
					$ttDetalhePlano += $valorFinal;
					$ttSubPlano 	+= $valorFinal;
					$ttPlano		+= $valorFinal;
					
					if( $matriz["detalhar"] ){
						$c=0;
						
						$matResultado[$cabecalho[$c++]][$l] = $espacador.$espacador.$espacador."Fornecedor: ". $fornecedor;
						$matResultado[$cabecalho[$c++]][$l] = $dtVencimento;
						$matResultado[$cabecalho[$c++]][$l++] = formatarValoresForm($valorFinal);
					}		
					
					$planoAnt 		 = $idPlanoDeContas;
					$planoSubAnt 	 = $idPlanoDeContasSub;
					$planoDetalheAnt = $idPlanoDeContasDetalhes;
					$mesAnt			 = substr( $dtVencimento, 3, 7);
				}// laco da consulta
				
				$matResultado[$cabecalho[2]][$cd] = formatarValoresForm( $ttDetalhePlano );
				$ttPop += $ttPlano;
				
				$c=0;
				$matResultado[$cabecalho[$c++]][$l]	= $espacador."Total Sub: ";
				$matResultado[$cabecalho[$c++]][$l] = "&nbsp;";
				$matResultado[$cabecalho[$c++]][$l++] = formatarValoresForm( $ttSubPlano );
				
				$c=0;
				$matResultado[$cabecalho[$c++]][$l]	= "Total Plano: ";
				$matResultado[$cabecalho[$c++]][$l] = "&nbsp;";
				$matResultado[$cabecalho[$c++]][$l++] = formatarValoresForm( $ttPlano );
			}//com resultados

			$c=0;
			$matResultado[$cabecalho[$c++]][$l]	= "<b class=\"bold10\">Total Pop:</b>";
			$matResultado[$cabecalho[$c++]][$l] = "&nbsp;";
			$matResultado[$cabecalho[$c++]][$l++] = "<b class=\"bold10\">".formatarValoresForm( $ttPop )."</b>";	
			
			$retorno[$idPOP] = $matResultado;

		}//for pop
	}//verifica pop
	

	return ( $retorno );
}


function contasAPagarPlanoDeContasExibr( $relatorio ){

	global $corFundo, $corBorda, $sessLogin;
	
	$cabecalho 		= array( 'Plano',	'Mês', 		'Valor'	);
	$alinhamento	= array( 'left',	'center',	'right'	);
	$largura		= array( '70%',		'15%',		'15%'	);
	
	if( count($relatorio) ) {
		novaTabela( "[RELATÓRIO DE CONTAS À PAGAR POR PLANO DE CONTAS]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, count($largura) );
		htmlAbreLinha( $corFundo );
		htmlAbreColuna( '100%', 'left', $corFundo, 0, 'normal10' );
		foreach ( $relatorio as $idPop => $detalhes ){
			echo "<br>";
			
			$consulta=buscaPOP($idPop, 'id', 'igual', 'id');
			if( $consulta ) $nomePop = resultadoSQL( $consulta, 0, "nome");
			
			novaTabela( $nomePop, "center", '100%', 0, 2, 1, $corFundo, $corBorda, count($largura) );
			htmlAbreLinha($corFundo);
			for( $i=0; $i<count( $cabecalho ); $i++){
				itemLinhaTMNOURL($cabecalho[$i], $alinhamento[$i], 'middle', $largura[$i], $corFundo, 0, 'tabfundo0');
			}
			htmlFechaLinha();
			if( count($detalhes['Plano']) > 1 ) {
				//corpo :
				for($i = 0; $i < count($detalhes['Plano']); $i++){
					htmlAbreLinha( $corFundo );
					foreach( $cabecalho as $j => $celula ) {
						itemLinhaNOURL( $detalhes[$celula][$i], $alinhamento[$j], $corFundo, 0, 'normal10');
					}
					htmlFechaLinha();
				}
			}
			else {
				htmlAbreLinha( $corFundo );
					itemLinhaNOURL('Nenhuma conta à pagar encontrada com os parâmetros especificados', 'center', $corFundo, count($cabecalho), 'normal10');
				htmlFechaLinha();				
			}
			
			fechaTabela();
			
		}
		htmlFechaColuna();
		htmlFechaLinha();
		fechaTabela();
	}
	else{
		# Não há registros
		avisoNOURL('Aviso', 'Não há dados existentes com os parâmetros especificados ou não selecionou o POP.', 400 );		
	}

	
}

function contasAPagarPlanoDeContasGeraPdf( $relatorio ) {
	
	global $corFundo, $corBorda, $sessLogin;
	
	$cabecalho 		= array( 'Plano',	'Mês', 		'Valor'	);
	$alinhamento	= array( 'left',	'center',	'right'	);
	$largura		= array( '70%',		'15%',		'15%'	);
	
	$matrizGrupo = array();
	
	# Organiza a matriz para o relatório
	if( count($relatorio) ){
		foreach ( $relatorio as $idPop => $detalhes ) {
			if( count($detalhes['Plano']) > 1 ){
				$consulta = buscaPOP($idPop, 'id', 'igual', 'id');
				
				if( $consulta ) $nomePop = resultadoSQL( $consulta, 0, "nome");
							
				# Alimentar Matriz Geral
				$matrizRelatorio['detalhe'] = $detalhes;
				
				# Alimentar Matriz de Header
				$matrizRelatorio['header']['TITULO']	= "RELATÓRIO DE CONTAS À PAGAR POR PLANO DE CONTAS<br>";
				$matrizRelatorio['header']['POP']		= $nomePop;
				$matrizRelatorio['header']['IMG_LOGO']	= $html['imagem']['logoRelatorio'];
				
				# Alimentar Matriz de configurações
				$matrizRelatorio['config']['linhas']		= 35;
				$matrizRelatorio['config']['layout']		= 'portrait';
				$matrizRelatorio['config']['marginleft']	= '1.0cm;';
				$matrizRelatorio['config']['marginright']	= '1.0cm;';
				
				$matrizGrupo[] = $matrizRelatorio;
			}
		}

		# Converter para PDF:
		
		$nome = "contas_pagar_plano_contas";
		
		criaTemplates( $nome, $cabecalho, $alinhamento );
		
		$arquivo = k_reportHTML2PDF( k_report( $matrizGrupo, 'html' , $nome ) , $nome , $matrizRelatorio['config'] );
		
		if ( $arquivo ) {
			echo "<!--";
			print_r( $relatorio );
			echo "\n\n\n";
			print_r( $matrizGrupo );			
			echo "-->";
			echo "<br>";
			novaTabela('Arquivos Gerados<a name=ancora></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
				htmlAbreLinha($corfundo);
					itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>Relatório de Contas à pagar por Fornecedor</a>",'pdf'), 'center', $corFundo, 7, 'txtaviso' );
				htmlFechaLinha();
			fechaTabela();
		}
		else {
			# Não consegiu gerar
			avisoNOURL('Aviso', 'Não foi possível gerar arquivo do relatório! Entre em contato com o administrador do sistema!', 400 );
		}
		
	}
	else {
		# Não há registros
		avisoNOURL('Aviso', 'Não há dados existentes com os parâmetros especificados ou não selecionou o POP.', 400 );
	}
	
}

?>