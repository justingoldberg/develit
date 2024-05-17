<?
/**
 * Exibe o formulário para geração do relatório de Saída de Produtos
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function formSaidaProdutos( $modulo, $sub, $acao, $registro, $matriz ){
	global $corFundo, $corBorda, $sessLogin;
	
	novaTabela2("[Saída de Produtos]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	
		#cabecalho com campos hidden
		abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );
		getCampo('', '', '', '&nbsp;');
		#pop de acesso
		$combo = formSelectPOP($matriz[pop],'pop','multi')."<input type=checkbox name=matriz[pop_todos] value=S $opcPOP><b>Todos</b>";
		getCampo( "combo", "POP", "", $combo, '','','','','','Selecione o POP de Acesso' );
		
		#periodo do relatorio
		getPeriodoDias( 6, 7, $matriz, "Vencimento" );
		
		#combo status
		$itens  = array(  0, 		"R",			"O"					);
		$labels = array( "Todos",	"Requisição",	"Ordem de Serviço"	);
		novaLinhaTabela( $corFundo, '100%' );
			itemLinhaTMNOURL('<b>Tipo:</b>', 'right', 'middle', 1, $corFundo, 1, 'tabfundo1');
			itemLinhaForm( getComboArray( 'matriz[tipo]', $labels, $itens, $matriz['tipo'] ),'left','middle',$corFundo,1,'tabfundo1');
		fechaLinhaTabela();
		
		getBotoesConsRel();
		
		fechaFormulario();
	
	fechaTabela();
}

/**
 * Retorna uma matriz com os dados para o relatório de saída de produtos
 *
 * @param array $matriz
 * @return array
 */
function SaidaProdutosPreparaRel( $matriz ){
	global $conn, $tb, $html;
	$opcQueryOrdem = array();
	$opcQueryRequisicao = array();
	
	#Seleciona os padrões de filtragem

	//ordem de exibição da consulta
	$ordem = "{$tb['POP']}.nome, {$tb['Produtos']}.nome, {$tb['MovimentoEstoque']}.data ; ";
	
	if( $matriz['tipo'] == 'R') {
		# Verifica se existem pop selecionados
		if( $matriz['pop'] && count( $matriz['pop'] ) > 0 ) {
			$opcQueryRequisicao[] = "{$tb['RequisicaoRetorno']}.idPop IN (" . implode( ',', $matriz['pop'] ) . ')';
		}
		# Verifica se existe período
		if( $matriz['dtInicial'] && $matriz['dtFinal'] ){
			$opcQueryRequisicao[] = "{$tb['RequisicaoRetorno']}.data BETWEEN '".
						 converteData( $matriz['dtInicial']." 00:00:00", 'form', 'banco' )."' AND '".
						 converteData( $matriz['dtFinal']  ." 23:59:59", 'form', 'banco' ) . "'";
		}
		$opcQueryRequisicao[] = "{$tb['RequisicaoRetorno']}.tipo = 'S'";
		$consulta = dbItensMovimentoEstoque( $matriz, 'consultar', 'requisicao', $opcQueryRequisicao, $ordem ); 
	}
	elseif( $matriz['tipo'] == 'O') {
		# Verifica se existem pop selecionados
		if( $matriz['pop'] && count( $matriz['pop'] ) > 0 ) {
			$opcQueryOrdem[] = "{$tb['OrdemServico']}.idPop IN (" . implode( ',', $matriz['pop'] ) . ')';
		}
		# Verifica se existe período
		if( $matriz['dtInicial'] && $matriz['dtFinal'] ){
			$opcQueryOrdem[] = "{$tb['OrdemServico']}.data BETWEEN '".
						 converteData( $matriz['dtInicial']." 00:00:00", 'form', 'banco' )."' AND '".
						 converteData( $matriz['dtFinal']  ." 23:59:59", 'form', 'banco' ) . "'";
		}
		$consulta = dbItensMovimentoEstoque( $matriz, 'consultar', 'ordem',  $opcQueryOrdem, $ordem );
	}
	else {
		# Verifica se existem pop selecionados
		if( $matriz['pop'] && count( $matriz['pop'] ) > 0 ) {
			$opcQueryOrdem[] = "{$tb['OrdemServico']}.idPop IN (" . implode( ',', $matriz['pop'] ) . ')';
			$opcQueryRequisicao[] = "{$tb['RequisicaoRetorno']}.idPop IN (" . implode( ',', $matriz['pop'] ) . ')';
		}
		# Verifica se existe período
		if( $matriz['dtInicial'] && $matriz['dtFinal'] ){
			$opcQueryOrdem[] = "{$tb['OrdemServico']}.data BETWEEN '".
						 converteData( $matriz['dtInicial']." 00:00:00", 'form', 'banco' )."' AND '".
						 converteData( $matriz['dtFinal']  ." 23:59:59", 'form', 'banco' ) . "'";
			$opcQueryRequisicao[] = "{$tb['RequisicaoRetorno']}.data BETWEEN '".
						 converteData( $matriz['dtInicial']." 00:00:00", 'form', 'banco' )."' AND '".
						 converteData( $matriz['dtFinal']  ." 23:59:59", 'form', 'banco' ) . "'";
		}
		$opcQueryRequisicao[] = "{$tb['RequisicaoRetorno']}.tipo = 'S'";
		$opcQuery = '('.implode(' AND ', $opcQueryRequisicao).') OR ('.implode(' AND ',$opcQueryOrdem).')';
		$consulta = dbItensMovimentoEstoque( $matriz, 'consultar', 'saida',  $opcQuery, $ordem );
	}

	//verifica quantidade de registros na consulta
	$totalOrdens = count( $consulta );
	//se houver registros...
	
	if( $totalOrdens ){
		
		$cabecalho	= array( 'Produto' , 'Unidade' , 'Quantidade', 'Data', 'Tipo'    );
		
		$l = 0; // numero da linha do relatório referente ao pop
		$agrupaPop = array(); //agrupa ordens de serviço de cliente por Pop
		
		foreach( $consulta as $i => $linha ) {
						
			$c = 0; //verifica colunas
			//linha recebe os valores da consulta de cada ordem
			$matrizLinha[$cabecalho[$c++]][$l] = "<b>".$linha->produto."</b>";
			$matrizLinha[$cabecalho[$c++]][$l] = $linha->unidade;
			$matrizLinha[$cabecalho[$c++]][$l] = "<div align='right'>".$linha->quantidade."</div>";
			$matrizLinha[$cabecalho[$c++]][$l] = "<div align='center'>".converteData( $linha->data, 'banco', 'formdata' )."</div>";
			$matrizLinha[$cabecalho[$c]][$l]   = $linha->tipoSaida;
			$l++;
	
			$j = $i + 1; //pega o indice posterior	
			//se não houver posterior ou idPop for diferente do posterior atribui 
			// as ocorrencias para o agrupa pop 
			if( $j == $totalOrdens || $linha->pop != $consulta[$j]->pop ) { 
				$l=0;
				$agrupaPop['detalhe'] = $matrizLinha;
	
				# Alimentar Matriz de Header
				$agrupaPop['header']['TITULO']   = "SAÍDA DE PRODUTOS";
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
	return $matrizGrupo;
}

/**
 * Monta array de exibição com o resultado da consulta
 *
 * @param array $detalhes
 * @param array $matriz
 */
function SaidaProdutosExibir( $detalhes, $matriz=array() ) {
	global $corFundo, $corBorda, $sessLogin;
	
	$cabecalho['cabecalho'] 	= array( 'Produto', 'Unidade', 'Quantidade', 'Data', 	'Tipo'    );
	$cabecalho['alinhamento']	= array( 'left',	'left',	   'right',	     'center',	'left'   );
	$cabecalho['largura']		= array( '40%',		'10%',	   '15%',		 '15%',    	'20%'  );

	echo "<br>";
	novaTabela( "[Listagem de Saída de Produtos]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, count($cabecalho['largura']) );
	
	htmlAbreLinha( $corFundo );
	htmlAbreColuna( '100%', 'left', $corFundo, 0, 'normal10' );
		
	#mostra resultados dos pops
	//verifica se a matriz não está vazia
	if( $detalhes ) {
		exibeRelatorio( $detalhes, $cabecalho);
	}
	else {
		echo "<span class='txtaviso'><i>Nenhuma Saída de Produtos existente com os dados especificados.</i></span>";
	}
		
	htmlFechaColuna();
	htmlFechaLinha();	
	fechaTabela();
}

/**
 * Cria o relatório de Saída de Produtos baseado nos dados de $detalhes
 *
 * @param array $detalhes
 * @param array $matriz
 */
function SaidaProdutosRelatorio( $detalhes, $matriz ){
	global $corFundo, $corBorda, $sessLogin;
	
	$cabecalho 		= array( 'Produto', 'Unidade', 'Quantidade', 'Data', 	'Tipo'    );
	$alinhamento	= array( 'left',	'left',	   'right',	     'center',	'left'   );
	$largura		= array( '40%',		'10%',	   '15%',		 '15%',    	'20%'  );
	
	if( $detalhes ){
		
		# Converter para PDF:
		
		$nome = "saida_de_produtos";
		
		criaTemplates( $nome, $cabecalho, $alinhamento );
		
		$arquivo = k_reportHTML2PDF( k_report( $detalhes, 'html' , $nome ) , $nome , $detalhes['config'] );
		
		if ( $arquivo ) {
			echo "<br>";
			novaTabela('Arquivos Gerados<a name=ancora></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
				htmlAbreLinha($corfundo);
					itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>Relatório de Saída de Produtos</a>",'pdf'), 'center', $corFundo, 7, 'txtaviso' );
				htmlFechaLinha();
			fechaTabela();
		}

	}

}
?>