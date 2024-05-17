<?
/**
 * Exibe o formulário para geração do relatório de Ordens de Serviço de Cliente
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function formOrdemServico( $modulo, $sub, $acao, $registro, $matriz ){
	global $corFundo, $corBorda, $sessLogin;
	
	novaTabela2("[Ordens de Serviço - Cliente]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	
		#cabecalho com campos hidden
		abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );
		getCampo('', '', '', '&nbsp;');
		#pop de acesso
		$combo = formSelectPOP($matriz[pop],'pop','multi')."<input type=checkbox name=matriz[pop_todos] value=S $opcPOP><b>Todos</b>";
		getCampo( "combo", "POP", "", $combo, '','','','','','Selecione o POP de Acesso' );
		
		#periodo do relatorio
		getPeriodoDias( 6, 7, $matriz, "Vencimento" );
		
		#combo status
		$itens  = array(  0, 		"P",		"B",		"C"			);
		$labels = array( "Todos",	"Pendente",	"Baixada", 	"Cancelada"	);
		novaLinhaTabela( $corFundo, '100%' );
			itemLinhaTMNOURL('<b>Status:</b>', 'right', 'middle', 1, $corFundo, 1, 'tabfundo1');
			itemLinhaForm( getComboArray( 'matriz[status]', $labels, $itens, $matriz['status'] ),'left','middle',$corFundo,1,'tabfundo1');
		fechaLinhaTabela();
		
		
		
		if( !$matriz['detalhar'] && !( $matriz['bntConfirmar'] || $matriz['bntRelatorio'] ) ) {
			$matriz['detalhar'] = "S";
		}
		#verifica detalhar relatório
		getDetalharCliente($matriz, ' por Ordem de Serviço');
		
		getBotoesConsRel();
		
		fechaFormulario();
	
	fechaTabela();
}

/**
 * Retorna uma matriz com os dados para o relatório de ordens de serviço de cliente
 *
 * @param array $matriz
 * @return array
 */
function OrdemServicoPreparaRel( $matriz ){
	global $conn, $tb, $html;
	
	$opcQuery = array();
	
	#Seleciona os padrões de filtragem
	
	# Verifica se existem pop selecionados
	if( $matriz['pop'] && count( $matriz['pop'] ) > 0 ) {
		$opcQuery[] = "{$tb['OrdemServico']}.idPop IN (" . implode( ',', $matriz['pop'] ) . ')';
	}
	
	# Verifica se existe período
	if( $matriz['dtInicial'] && $matriz['dtFinal'] ){
		$opcQuery[] = "{$tb['OrdemServico']}.data BETWEEN '".
					 converteData( $matriz['dtInicial']." 00:00:00", 'form', 'banco' )."' AND '".
					 converteData( $matriz['dtFinal']  ." 23:59:59", 'form', 'banco' ) . "'";
	}
	# Verifica se existe status selecionado
	if( $matriz['status'] ){
		$opcQuery[] = "{$tb['OrdemServico']}.status='".$matriz['status']."'";
	}
	//ordem de exibição da consulta
	$ordem = "{$tb['POP']}.nome, {$tb['Pessoas']}.nome, {$tb['Servicos']}.nome, {$tb['OrdemServico']}.status;";
	
	$consulta = (  $matriz['detalhar'] ? 
		dbOrdemServico( $matriz, 'consultar', 'detalhada', $opcQuery, $ordem ) :
		dbOrdemServico( $matriz, 'consultar', 'completa',  $opcQuery, $ordem ) );
	
	$vetorStatus = array('B' => 'Baixada', 'P' => 'Pendente', 'C' => 'Cancelada' );
	//verifica quantidade de registros na consulta
	$totalOrdens = count( $consulta );
	//se houver registros...
	
	if( $totalOrdens ){
		
		$cabecalho = array( 'Cliente', 'Data Execução', 'Serviço', 'Responsável', 'Status');
		
		$l = 0; // numero da linha do relatório referente ao pop
		$agrupaPop = array(); //agrupa ordens de serviço de cliente por Pop
		
		if( $matriz['detalhar']) {
			foreach( $consulta as $i => $linha ) {
				
							
				$h = $i - 1; //pega o indice posterior
				if( $linha->idCliente != $consulta[$h]->idCliente || $l == 0 ) { 
					$c = 0; //verifica colunas
					//linha recebe os valores da consulta de cada ordem
					$matrizLinha[$cabecalho[$c++]][$l] = "<b>".$linha->nomeCliente."</b>";
					$matrizLinha[$cabecalho[$c++]][$l] = converteData( $linha->dataExecucao, 'banco', 'formdata' );
					$matrizLinha[$cabecalho[$c++]][$l] = $linha->servico;
					$matrizLinha[$cabecalho[$c++]][$l] = $linha->responsavel;
					$matrizLinha[$cabecalho[$c]][$l]   = $vetorStatus[$linha->status];
					$l++;
					$detalhes = 1;
				}
				
				if( $linha->produto ) {
					if( $detalhes ) {
						$c = 0;
						$matrizLinha[$cabecalho[$c++]][$l] = "<div align='center'><span class='bold10'>Produtos</span></div>";
						$matrizLinha[$cabecalho[$c++]][$l] = "<div align='center'><span class='bold10'>Qtde</span></div>";
						$matrizLinha[$cabecalho[$c++]][$l] = "&nbsp;&nbsp; ";
						$matrizLinha[$cabecalho[$c++]][$l] = "&nbsp;&nbsp; ";
						$matrizLinha[$cabecalho[$c]][$l++] = "&nbsp;&nbsp; ";
					}
					$c = 0;
					$matrizLinha[$cabecalho[$c++]][$l] = "&nbsp;&nbsp; " . $linha->produto;
					$matrizLinha[$cabecalho[$c++]][$l] = "<div align='right'>".$linha->qtde."</div>";
					$matrizLinha[$cabecalho[$c++]][$l] = "&nbsp;&nbsp; ";
					$matrizLinha[$cabecalho[$c++]][$l] = "&nbsp;&nbsp; ";
					$matrizLinha[$cabecalho[$c]][$l] = "&nbsp;&nbsp; ";
					$l++;
					$detalhes = 0;
				}
				$j = $i + 1; //pega o indice posterior	
				//se não houver posterior ou idPop for diferente do posterior atribui 
				// as ocorrencias para o agrupa pop 
				if( $j == $totalOrdens || $linha->idPop != $consulta[$j]->idPop ) { 
					$l=0;
					$agrupaPop['detalhe'] = $matrizLinha;
	
					# Alimentar Matriz de Header
					$agrupaPop['header']['TITULO']   = "ORDENS DE SERVIÇO DE CLIENTE";
					$agrupaPop['header']['POP']	   = $linha->pop;
					$agrupaPop['header']['IMG_LOGO'] = $html['imagem']['logoRelatorio'];
				
					# Configurações
					$agrupaPop['config']['linhas']	  = 35; //25
					$agrupaPop['config']['layout']	  = 'portrait';
					$agrupaPop['config']['marginleft']  = '1.0cm;';
					$agrupaPop['config']['marginright'] = '1.0cm;';
				
					$matrizGrupo[] = $agrupaPop;
					
					
					
					$matrizLinha = array();
				}	
				
			}
		}
		else {
			foreach( $consulta as $i => $linha ) {
							
				$c = 0; //verifica colunas
				//linha recebe os valores da consulta de cada ordem
				$matrizLinha[$cabecalho[$c++]][$l] = "<b>".$linha->nomeCliente."</b>";
				$matrizLinha[$cabecalho[$c++]][$l] = converteData( $linha->dataExecucao, 'banco', 'formdata' );
				$matrizLinha[$cabecalho[$c++]][$l] = $linha->servico;
				$matrizLinha[$cabecalho[$c++]][$l] = $linha->responsavel;
				$matrizLinha[$cabecalho[$c]][$l]   = $vetorStatus[$linha->status];
				$l++;

				$j = $i + 1; //pega o indice posterior	
				//se não houver posterior ou idPop for diferente do posterior atribui 
				// as ocorrencias para o agrupa pop 
				if( $j == $totalOrdens || $linha->idPop != $consulta[$j]->idPop ) { 
					$l=0;
					$agrupaPop['detalhe'] = $matrizLinha;
	
					# Alimentar Matriz de Header
					$agrupaPop['header']['TITULO']   = "ORDENS DE SERVIÇO POR CLIENTE";
					$agrupaPop['header']['POP']	   = $linha->pop;
					$agrupaPop['header']['IMG_LOGO'] = $html['imagem']['logoRelatorio'];
				
					# Configurações
					$agrupaPop['config']['linhas']	  = 35; //25
					$agrupaPop['config']['layout']	  = 'portrait';
					$agrupaPop['config']['marginleft']  = '1.0cm;';
					$agrupaPop['config']['marginright'] = '1.0cm;';
				
					$matrizGrupo[] = $agrupaPop;
										
					$matrizLinha = array();
				}	
				
			}
			
		}
	}
	return $matrizGrupo;
}

/**
 * Monta array de exibição com o resultado da consulta
 *
 * @param array $detalhes
 * @param array $matriz
 */
function OrdemServicoExibir( $detalhes, $matriz=array() ) {
	global $corFundo, $corBorda, $sessLogin;
	
	$cabecalho['cabecalho'] 	= array( 'Cliente', 'Data Execução', 'Serviço', 'Responsável', 	'Status'   );
	$cabecalho['alinhamento']	= array( 'left',	'center',		 'left',	'left',			'center'   );
	$cabecalho['largura']		= array( '40%',		'10%',			 '25%',		'15%',			'10%'	   );

	echo "<br>";
	novaTabela( "[Listagem de Ordem de Serviço de Cliente]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, count($cabecalho['largura']) );
	
	htmlAbreLinha( $corFundo );
	htmlAbreColuna( '100%', 'left', $corFundo, 0, 'normal10' );
		
	#mostra resultados dos pops
	//verifica se a matriz não está vazia
	if( $detalhes ) {
		exibeRelatorio( $detalhes, $cabecalho);
	}
	else {
		echo "<span class='txtaviso'><i>Nenhuma Ordem de Serviço existente com os dados especificados.</i></span>";
	}
		
	htmlFechaColuna();
	htmlFechaLinha();	
	fechaTabela();
}

/**
 * Cria o relatório de ordens de serviço do cliente baseado nos dados de $detalhes
 *
 * @param array $detalhes
 * @param array $matriz
 */
function OrdemServicoRelatorio( $detalhes, $matriz ){
	global $corFundo, $corBorda, $sessLogin;
	
	$cabecalho 		= array( 'Cliente', 'Data Execução', 'Serviço', 'Responsável', 	'Status'   );
	$alinhamento	= array( 'left',	'center',		 'left',	'left',			'center'   );
	$largura		= array( '40%',		'10%',			 '25%',		'15%',			'10%'	   );
	
	if( $detalhes ){
		
		# Converter para PDF:
		
		$nome = "ordem_servico_cliente";
		
		criaTemplates( $nome, $cabecalho, $alinhamento );
		
		$arquivo = k_reportHTML2PDF( k_report( $detalhes, 'html' , $nome ) , $nome , $detalhes['config'] );
		
		if ( $arquivo ) {
			echo "<br>";
			novaTabela('Arquivos Gerados<a name=ancora></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
				htmlAbreLinha($corfundo);
					itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>Relatório de Ordens de Serviço de Cliente</a>",'pdf'), 'center', $corFundo, 7, 'txtaviso' );
				htmlFechaLinha();
			fechaTabela();
		}

	}

}
?>