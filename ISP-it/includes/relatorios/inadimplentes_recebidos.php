<?
/**
 * Exibe o formulário para geração do relatório de Inadimplentes Recebidos
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function formInadimplentesRecebidos( $modulo, $sub, $acao, $registro, $matriz ){
	global $corFundo, $corBorda, $sessLogin;
	
	novaTabela2("[Inadimplentes Recebidos]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	
		#cabecalho com campos hidden
		abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );
		getCampo('', '', '', '&nbsp;');
		#pop de acesso
		$combo = formSelectPOP($matriz[pop],'pop','multi')."<input type=checkbox name=matriz[pop_todos] value=S $opcPOP><b>Todos</b>";
		getCampo( "combo", "POP", "", $combo, '','','','','','Selecione o POP de Acesso' );
		getCampo('', '', '', '&nbsp;');
		#periodo do relatorio
		getPeriodoDias( 6, 7, $matriz, "Vencimento" );
		getCampo('', '', '', '&nbsp;');
		getBotoesConsRelPDF_CSV();
		
		fechaFormulario();
	
	fechaTabela();
}


/**
 * Retorna uma matriz com os dados para o relatório de inadimplentes recebidos
 *
 * @param array $matriz
 * @return array
 */
function InadimplentesRecebidosPreparaRel( $matriz ){
	global $conn, $tb, $html;
	
	#Seleciona os padrões de filtragem

	//ordem de exibição da consulta
	$ordem = "{$tb['POP']}.nome, {$tb['Pessoas']}.nome, {$tb['ContasReceber']}.dtBaixa ; ";
	$opcQuery[] = "{$tb['ContasReceber']}.status = 'B'";	
		
	
	if( $matriz['pop'] && count( $matriz['pop'] ) > 0 ) {
		$opcQuery[] = "{$tb['Pessoas']}.idPop IN (" . implode( ',', $matriz['pop'] ) . ')';
	}
	# Verifica se existe período
	if( $matriz['dtInicial'] && $matriz['dtFinal'] ){
		$opcQuery[] = "{$tb['ContasReceber']}.dtVencimento BETWEEN '".
				 converteData( $matriz['dtInicial']." 00:00:00", 'form', 'banco' )."' AND '".
				 converteData( $matriz['dtFinal']  ." 23:59:59", 'form', 'banco' ) . "'";
	}
	$opcQuery[] = "LEFT({$tb['ContasReceber']}.dtBaixa,10) > LEFT({$tb['ContasReceber']}.dtVencimento,10)";
	$consulta = consultaContasReceber( $matriz, 'consultar', '', $opcQuery, $ordem ); 
	
	//verifica quantidade de registros na consulta
	$total = count( $consulta );
	//se houver registros...
	
	if( $total ){
		
		$cabecalho	= array( 'Nome', 'Vencimento', 'Recebido Em', 'Valor Recebido', 'Juros' );
		
		$l = 0; // numero da linha do relatório referente ao pop
		$agrupaPop = array(); //agrupa contas de cliente por Pop
		$totalRecebido = 0;
		$totalJuros = 0;
		$Juros = 0;
		$Recebido =0;
		
		
		foreach( $consulta as $i => $linha ) {
						
			$c = 0; //verifica colunas
			//linha recebe os valores da consulta de cada CONTA À RECEBER
			$matrizLinha[$cabecalho[$c++]][$l] = $linha->pessoa;
			$matrizLinha[$cabecalho[$c++]][$l] = "<div align='center'>".converteData( $linha->dtVencimento, 'banco', 'formdata' )."</div>";
			$matrizLinha[$cabecalho[$c++]][$l] = "<div align='center'>".converteData( $linha->dtBaixa, 'banco', 'formdata' )."</div>";
			$matrizLinha[$cabecalho[$c++]][$l] = "<div align='right'>".number_format($linha->valorRecebido,2,',','.')."</div>";
			$matrizLinha[$cabecalho[$c]][$l]   = "<div align='right'>".number_format($linha->valorJuros,2,',','.')."</div>";
			$l++;
	
			$totalRecebido += $linha->valorRecebido;
			$totalJuros += $linha->valorJuros;
			
			$j = $i + 1; //pega o indice posterior	
			//se não houver posterior ou idPop for diferente do posterior atribui 
			// as ocorrencias para o agrupa pop 
			if( $j == $total || $linha->pop != $consulta[$j]->pop ) { 
				$c = 0; 
				$matrizLinha[$cabecalho[$c++]][$l] = "<span class=txtaviso><b>Total</b></span>";
				$matrizLinha[$cabecalho[$c++]][$l] = "&nbsp;&nbsp;";
				$matrizLinha[$cabecalho[$c++]][$l] = "&nbsp;&nbsp;";
				$matrizLinha[$cabecalho[$c++]][$l] = "<div align='right'><span class='bold10'><b>".number_format($totalRecebido,2,',','.')."</b></span></div>";
				$matrizLinha[$cabecalho[$c]][$l]   = "<div align='right'><span class='bold10'><b>".number_format($totalJuros,2,',','.')."</b></span></div>";
				
				$Juros += $totalJuros;
		        $Recebido += $totalRecebido;
				
		        if($j==$total){
		        	$l++; $c = 0;
		        	$matrizLinha[$cabecalho[$c++]][$l] = "<span class=txtok><b>Total Geral</b></span>";
					$matrizLinha[$cabecalho[$c++]][$l] = "&nbsp;&nbsp;";
					$matrizLinha[$cabecalho[$c++]][$l] = "&nbsp;&nbsp;";
					$matrizLinha[$cabecalho[$c++]][$l] = "<div align='right'><span class='bold10'><b>".number_format($Recebido,2,',','.')."</b></span></div>";
					$matrizLinha[$cabecalho[$c]][$l]   = "<div align='right'><span class='bold10'><b>".number_format($Juros,2,',','.')."</b></span></div>";
		        }
		        		        
				$totalRecebido = 0;
		        $totalJuros = 0;
				
				$l=0;
								
				$agrupaPop['detalhe'] = $matrizLinha;
	
				# Alimentar Matriz de Header
				$agrupaPop['header']['TITULO']   = "INADIMPLENTES RECEBIDOS";
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
function InadimplentesRecebidosExibir( $detalhes, $matriz=array() ) {
	global $corFundo, $corBorda, $sessLogin;
	
	$cabecalho['cabecalho'] 	= array( 'Nome', 'Vencimento', 'Recebido Em', 'Valor Recebido', 'Juros'    );
	$cabecalho['alinhamento']	= array( 'left', 'center',	   'center',	      'center',	        'center'   );
	$cabecalho['largura']		= array( '40%',	 '15%',	       '15%',		  '15%',    	    '15%'  );

	echo "<br>";
	novaTabela( "[Listagem de Clientes Inadimplentes Recebidos]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, count($cabecalho['largura']) );
	
	htmlAbreLinha( $corFundo );
	htmlAbreColuna( '100%', 'left', $corFundo, 0, 'normal10' );
		
	#mostra resultados dos pops
	//verifica se a matriz não está vazia
	if( $detalhes ) {
		exibeRelatorio( $detalhes, $cabecalho);
	}
	else {
		echo "<span class='txtaviso'><i>Nenhum inadimplente recebido existente com os dados especificados.</i></span>";
	}
		
	htmlFechaColuna();
	htmlFechaLinha();	
	fechaTabela();
}

/**
 * Cria o relatório de Inadimplentes Recebidos baseado nos dados de $detalhes
 *
 * @param array $detalhes
 * @param array $matriz
 */
function InadimplentesRecebidosRelatorioPDF( $detalhes, $matriz ){
	global $corFundo, $corBorda, $sessLogin;
	
	$cabecalho 		= array( 'Nome', 'Vencimento', 'Recebido Em', 'Valor Recebido', 'Juros'    );
	$alinhamento	= array( 'left', 'left',	   'right',	      'center',	        'left'   );
	$largura		= array( '40%',		'10%',	   '15%',		  '15%',    	    '20%'  );

	if( $detalhes ){
		
		# Converter para PDF:
		
		$nome = "inadimplentes_recebidos";
		
		criaTemplates( $nome, $cabecalho, $alinhamento );
		
		$arquivo = k_reportHTML2PDF( k_report( $detalhes, 'html' , $nome ) , $nome , $detalhes['config'] );
		
		if ( $arquivo ) {
			echo "<br>";
			novaTabela('Arquivos Gerados<a name=ancora></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
				htmlAbreLinha($corfundo);
					itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>Relatório de Clientes Inadimplentes Recebidos</a>",'pdf'), 'center', $corFundo, 7, 'txtaviso' );
				htmlFechaLinha();
			fechaTabela();
		}
	}
}
	
 /**
 * Cria o relatório em formato CSV baseado em $matriz
 *
 * @param array $matriz
 */
function InadimplentesRecebidosRelatorioCSV( $matriz ){
	global $conn, $tb, $html, $arquivo, $corFundo, $corBorda, $sessLogin;
	
	#Seleciona os padrões de filtragem

	//ordem de exibição da consulta
	$ordem = "{$tb['POP']}.nome, {$tb['Pessoas']}.nome, {$tb['ContasReceber']}.dtBaixa ; ";
	$opcQuery[] = "{$tb['ContasReceber']}.status = 'B'";	
		
	if( $matriz['pop'] && count( $matriz['pop'] ) > 0 ) {
		$opcQuery[] = "{$tb['Pessoas']}.idPop IN (" . implode( ',', $matriz['pop'] ) . ')';
	}
	# Verifica se existe período
	if( $matriz['dtInicial'] && $matriz['dtFinal'] ){
		$opcQuery[] = "{$tb['ContasReceber']}.dtVencimento BETWEEN '".
				 converteData( $matriz['dtInicial']." 00:00:00", 'form', 'banco' )."' AND '".
				 converteData( $matriz['dtFinal']  ." 23:59:59", 'form', 'banco' ) . "'";
	}
	$opcQuery[] = "LEFT({$tb['ContasReceber']}.dtBaixa,10) > LEFT({$tb['ContasReceber']}.dtVencimento,10)";
	$consulta = consultaContasReceber( $matriz, 'consultar', '', $opcQuery, $ordem ); 
	
	//verifica quantidade de registros na consulta
	$total = count( $consulta );
	//se houver registros...
	$arquivoCSV = '';
	
	if( $total ){
		
		$arquivoCSV .= "POP; Cliente; Vencimento; Recebido Em; Valor Recebido; Juros\n";
		
		$totalRecebido = 0;
		$totalJuros = 0;
		$Juros = 0;
		$Recebido =0;
		
		
		foreach( $consulta as $i => $linha ) {

			$arquivoCSV .= $linha->pop.";";
			$arquivoCSV .= $linha->pessoa.";";
			$arquivoCSV .= converteData( $linha->dtVencimento, 'banco', 'formdata' ).";";
			$arquivoCSV .= converteData( $linha->dtBaixa, 'banco', 'formdata' ).";";
			$arquivoCSV .= number_format($linha->valorRecebido,2,',','.').";";
			$arquivoCSV .= number_format($linha->valorJuros,2,',','.')."\n";

	
			$totalRecebido += $linha->valorRecebido;
			$totalJuros += $linha->valorJuros;
			
			$j = $i + 1;
			
			if( $j == $total || $linha->pop != $consulta[$j]->pop ) { 
				
				$arquivoCSV .= "Total;";
				$arquivoCSV .= ";";
				$arquivoCSV .= ";";
				$arquivoCSV .= ";";
				$arquivoCSV .= number_format($totalRecebido,2,',','.').";";
				$arquivoCSV .= number_format($totalJuros,2,',','.')."\n";
				
				$Juros += $totalJuros;
		        $Recebido += $totalRecebido;
				
		        if($j==$total){

		        	$arquivoCSV .= "Total Geral;";
					$arquivoCSV .= ";";
					$arquivoCSV .= ";";
					$arquivoCSV .= ";";
					$arquivoCSV .= number_format($Recebido,2,',','.').";";
					$arquivoCSV .= number_format($Juros,2,',','.');
		        }
		        else{
		        	$arquivoCSV .="\n";
		        }
		        		        
				$totalRecebido = 0;
		        $totalJuros = 0;
			}				
		}		
	}
	
	if ( $arquivoCSV ) {
		$data = dataSistema();
		
		$nome = $arquivo['tmpCSV']."inadimplentes_recebidos_$sessLogin[login]_$data[dataBancoGrapi].csv";
		
		criaArquivoCSV( $nome, $arquivoCSV );
		
		echo "<br />";
		novaTabela2('Arquivos Gerados<a name=ancora></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			htmlAbreLinha($corfundo);
				itemTabelaNOURL(htmlMontaOpcao("<a href=$nome>Relatório de Clientes Inadimplentes Recebidos</a>",'relatorio'), 'center', $corFundo, 7, 'txtaviso' );
			htmlFechaLinha();
			htmlAbreLinha($corfundo);
				itemTabelaNOURL('Atenção: Clique com o botão direito sobre o link e selecione "Salvar link como" para fazer o download do arquivo.', 'center', $corFundo, 7, 'txtaviso' );
			htmlFechaLinha();
		fechaTabela();
	}
}

?>