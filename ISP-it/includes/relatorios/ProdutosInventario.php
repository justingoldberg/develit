<?
/**
 * Exibe o formulário para geração do relatório de Produtos em Estoque dos Pops
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function formProdutosInventario( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda, $sessLogin;
	
	novaTabela2("[Produtos em Estoque - Inventário]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	
		#cabecalho com campos hidden
		abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );
		getCampo('', '', '', '&nbsp;');
		#pop de acesso
		$combo = formSelectPOP($matriz['pop'],'pop','multi')."<input type=checkbox name=matriz[pop_todos] value=S $opcPOP><b>Todos</b>";
		getCampo( "combo", "POP", "", $combo, '','','','','','Selecione o POP de Acesso' );
		getCampo('', '', '', '&nbsp;');
		getBotoesConsRel();
		
		fechaFormulario();
	
	fechaTabela();	
}

/**
 * Retorna uma matriz com os dados para o relatório de Produtos em Estoque - Inventario
 *
 * @param array $matriz
 * @return array
 */
function ProdutosInventarioPreparaRel( $matriz ){
	global $conn, $tb, $html;
	
	$opcQuery = array();
	
	#Seleciona os padrões de filtragem
	
	# Verifica se existem pop selecionados
	if( $matriz['pop'] && count( $matriz['pop'] ) > 0 ) {
		$opcQuery[] = "{$tb['ProdutosEstoque']}.idPop IN (" . implode( ',', $matriz['pop'] ) . ')';
	}
	
	//ordem de exibição da consulta
	$ordem = "{$tb['POP']}.nome, {$tb['Produtos']}.nome;";
	
	$consulta = dbProdutosEstoque( $matriz, 'consultaCompleta', '', $opcQuery, $ordem );
	
	//verifica quantidade de registros na consulta
	$totalOrdens = count( $consulta );
	
	//se houver registros...
	if( $totalOrdens ){
		
		$cabecalho = array( 'Produto', 'Unidade', 'Qtde Mínima', 'Quantidade' );
		
		$l = 0; // numero da linha do relatório referente ao pop
		$agrupaPop = array(); //agrupa produtos em estoque por Pop
		
		foreach( $consulta as $i => $linha ) {
						
			$c = 0; //verifica colunas
			//linha recebe os valores da consulta de cada ordem
			$matrizLinha[$cabecalho[$c++]][$l] = "<b>".$linha->nome."</b>";
			$matrizLinha[$cabecalho[$c++]][$l] = $linha->unidade;
			$matrizLinha[$cabecalho[$c++]][$l] = "<div align='right'>".$linha->qtdeMinima."</div>";
			$matrizLinha[$cabecalho[$c++]][$l] = "<div align='right'>".$linha->quantidade."</div>";
			$l++;

			$j = $i + 1; //pega o indice posterior	
			//se não houver posterior ou idPop for diferente do posterior atribui 
			// as ocorrencias para o agrupa pop 
			if( $j == $totalOrdens || $linha->idPop != $consulta[$j]->idPop ) { 
				$l=0;
				$agrupaPop['detalhe'] = $matrizLinha;

				# Alimentar Matriz de Header
				$agrupaPop['header']['TITULO']   = " PRODUTOS EM ESTOQUE";
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
function ProdutosInventarioExibir( $detalhes, $matriz=array() ) {
	global $corFundo, $corBorda, $sessLogin;
	
	$cabecalho['cabecalho'] 	= array( 'Produto', 'Unidade', 	'Qtde Mínima', 	'Quantidade'  );
	$cabecalho['alinhamento']	= array( 'left',	'center',	'right',		'right' 	  );
	$cabecalho['largura']		= array( '40%',		'10%',		'25%',			'25%'	      );

	echo "<br>";
	novaTabela( "[Listagem de Produtos em Estoque - Inventário]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, count($cabecalho['largura']) );
	
	htmlAbreLinha( $corFundo );
	htmlAbreColuna( '100%', 'left', $corFundo, 0, 'normal10' );
		
	#mostra resultados dos pops
	//verifica se a matriz não está vazia
	if( $detalhes ) {
		exibeRelatorio( $detalhes, $cabecalho);
	}
	else {
		echo "<span class='txtaviso'><i>Nenhum Produto em Estoque para o(s) Pop(s) especificado(s).</i></span>";
	}
		
	htmlFechaColuna();
	htmlFechaLinha();	
	fechaTabela();
}

/**
 * Cria o relatório de produtos em estoque - inventario baseado nos dados de $detalhes
 *
 * @param array $detalhes
 * @param array $matriz
 */
function ProdutosInventarioRelatorio( $detalhes, $matriz ){
	global $corFundo, $corBorda, $sessLogin;
	
	$cabecalho 		= array( 'Produto', 'Unidade', 	'Qtde Mínima', 	'Quantidade'   );
	$alinhamento	= array( 'left',	'center',	'right',		'right'        );
	$largura		= array( '40%',		'10%',		'25%',			'25%'		   );
	
	if( $detalhes ){
		
		# Converter para PDF:
		
		$nome = "produto_estoque_inventario";
		
		criaTemplates( $nome, $cabecalho, $alinhamento );
		
		$arquivo = k_reportHTML2PDF( k_report( $detalhes, 'html' , $nome ) , $nome , $detalhes['config'] );
		
		if ( $arquivo ) {
			echo "<br>";
			novaTabela('Arquivos Gerados<a name=ancora></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
				htmlAbreLinha($corfundo);
					itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>Relatório de Produtos em Estoque - Inventário</a>",'pdf'), 'center', $corFundo, 7, 'txtaviso' );
				htmlFechaLinha();
			fechaTabela();
		}

	}

}
?>