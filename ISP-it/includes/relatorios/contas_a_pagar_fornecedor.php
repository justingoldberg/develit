<?

/**
 * Exibe o formulário para geração do relatório de Contas a Pagar por fornecedor
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function formContasAPagarFornecedor( $modulo, $sub, $acao, $registro, $matriz ){
	global $corFundo, $corBorda, $sessLogin;
	
	novaTabela2("[Contas à Pagar - Fornecedor]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	
		#cabecalho com campos hidden
		abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );
		
		#pop de acesso
		$combo = formSelectPOP($matriz[pop],'pop','multi')."<input type=checkbox name=matriz[pop_todos] value=S $opcPOP><b>Todos</b>";
		getCampo( "combo", "<b>POP:</b><br><span class=normal10>Selecione o POP de Acesso</span>", "", $combo );
		
		#periodo do relatorio
		getPeriodoDias( 6, 7, $matriz, "Vencimento" );
		
		#combo status
		$itens  = array(  0, 		"B",	"P",		"C"			);
		$labels = array( "Todos",	"Pago",	"Aberto", 	"Cancelado"	);
		novaLinhaTabela( $corFundo, '100%' );
			itemLinhaTMNOURL('Status', 'right', 'middle', 1, $corFundo, 1, 'tabfundo1');
			itemLinhaForm( getComboArray( 'matriz[status]', $labels, $itens, $matriz['status'] ),'left','middle',$corFundo,1,'tabfundo1');
		fechaLinhaTabela();
		
		
		
		if( !$matriz['detalhar'] && !( $matriz['bntConfirmar'] || $matriz['bntRelatorio'] ) ) {
			$matriz['detalhar'] = "S";
		}
		
		getDetalharCliente($matriz, ' por conta paga');
		
		getBotoesConsRel();
		
		fechaFormulario();
	
	fechaTabela();
}


/**
 * Retorna uma matriz com os dados para o relatório de contas a pagar por fornecedor
 *
 * @param array $matriz
 * @return array
 */
function contasAPagarFornecedorPreparaRel( $matriz ){
	global $conn, $tb;
	
	$opcQuery = "";
	
	#Seleciona os padrões de filtragem
	
	# Verifica se existem pop selecionados
	if( $matriz['pop'] && count( $matriz['pop'] ) > 0 ) {
		$opcQuery = " WHERE {$tb['ContasAPagar']}.idPop IN (" . implode( ',', $matriz['pop'] ) . ')';
	}
	
	# Verifica se existe período
	if( $matriz['dtInicial'] && $matriz['dtFinal'] ){
		$opcQuery .= ( $opcQuery ? " AND " : " WHERE " )." (".$tb['ContasAPagar'].".dtVencimento BETWEEN '".
					 converteData( $matriz['dtInicial']." 00:00:00", 'form', 'banco' )."' AND '".
					 converteData( $matriz['dtFinal']  ." 23:59:59", 'form', 'banco' ) . "')";
	}
	
	# Verifica se existe status selecionado
	if( $matriz['status'] ){
		$opcQuery .= ( $opcQuery ? " AND " : " WHERE " ) . $tb['ContasAPagar'] . ".status='".$matriz['status']."'";
	}
	
	$sql = "SELECT 
  				{$tb['POP']}.id AS idPop, 
  				{$tb['POP']}.nome AS nomePop, 
  				{$tb['Pessoas']}.id idFornecedor, 
  				{$tb['Pessoas']}.nome AS nomeFornecedor, 
  				{$tb['PlanoDeContasDetalhes']}.nome AS nomePlanoDeContasDetalhes,
  				{$tb['ContasAPagar']}.id AS idContasAPagar, 
  				{$tb['ContasAPagar']}.idPlanoDeContasDetalhes,
  				{$tb['ContasAPagar']}.valor, 
  				{$tb['ContasAPagar']}.valorPago, 
  				{$tb['ContasAPagar']}.dtCadastro, 
  				{$tb['ContasAPagar']}.dtVencimento, 
  				{$tb['ContasAPagar']}.dtBaixa, 
  				{$tb['ContasAPagar']}.status AS statusContasAPagar
			FROM ContasAPagar 
				INNER JOIN Pop ON ( {$tb['ContasAPagar']}.idPop = {$tb['POP']}.id )
  				INNER JOIN PlanoDeContasDetalhes ON ( {$tb['ContasAPagar']}.idPlanoDeContasDetalhes = {$tb['PlanoDeContasDetalhes']}.id )
  				INNER JOIN PessoasTipos ON ( {$tb['ContasAPagar']}.idFornecedor = {$tb['PessoasTipos']}.id ) 
  				INNER JOIN Pessoas ON ({$tb['PessoasTipos']}.idPessoa = {$tb['Pessoas']}.id)
  			$opcQuery
			ORDER BY {$tb['POP']}.nome, {$tb['Pessoas']}.nome, {$tb['PlanoDeContasDetalhes']}.nome, {$tb['ContasAPagar']}.status;";
	
	$consulta = consultaSQL( $sql, $conn );
	
	$vetorStatus = array('B' => 'Baixado', 'P' => 'Pendente', 'C' => 'Cancelado' );
	
	if( $consulta && contaConsulta( $consulta ) ){
		
		$cabecalho = array( 'Conta à Pagar', 'Data Vencimento', 'Valor', 'Valor Pago', 'Situação');
		
		$idFornecedorAnt	= 0;
		$idPopAnt			= 0;
		$l 					= 0;
		
		$ttPop 				= 0;
		$ttFornecedor		= 0;

		$ttPopPago			= 0;
		$ttFornecedorPago	= 0;
		
		$espacador = str_repeat("&nbsp;", 4);
		
		for( $i=0; $i < contaConsulta( $consulta ); $i++){

			
			
			$idPop						= resultadoSQL( $consulta, $i, 'idPop'						);
			$nomePop					= resultadoSQL( $consulta, $i, 'nomePop'					);
			$idFornecedor				= resultadoSQL( $consulta, $i, 'idFornecedor'				);
			$nomeFornecedor 			= resultadoSQL( $consulta, $i, 'nomeFornecedor'				);
			$nomePlanoDeContasDetalhes	= resultadoSQL( $consulta, $i, 'nomePlanoDeContasDetalhes'	);
			$idPlanoDeContasDetalhes	= resultadoSQL( $consulta, $i, 'idPlanoDeContasDetalhes'	);
			$idContasAPagar				= resultadoSQL( $consulta, $i, 'idContasAPagar'				);
			
			$valor						= resultadoSQL( $consulta, $i, 'valor'				);
			$valorPago					= resultadoSQL( $consulta, $i, 'valorPago'			);
			$dtCadastro					= resultadoSQL( $consulta, $i, 'dtCadastro'			);
			$dtVencimento				= converteData( resultadoSQL( $consulta, $i, 'dtVencimento'		), 'banco', 'formdata' );
			$dtBaixa					= resultadoSQL( $consulta, $i, 'dtBaixa'			);
			$statusContasAPagar			= resultadoSQL( $consulta, $i, 'statusContasAPagar' );
				
			
			# se mudou de pop insere o total na matriz e depois lança o nome do próximo
			if( $idPopAnt != $idPop ){
				
				if( $idPopAnt ) {
					$matResultado[$idPopAnt]['total']		= number_format($ttPop,	    2,",",".");
					$matResultado[$idPopAnt]['totalPago']	= number_format($ttPopPago, 2,",",".");
				}
				
				$ttPop		= 0;
				$ttPopPago	= 0;
				$c 			= 0;
				
				$matResultado[$idPop]['nome'] = $nomePop;
			
			}
			
			# se mudou de fornecedor insere o total na matriz e depois lança o nome do próximo
			if( $idFornecedorAnt != $idFornecedor || $idPopAnt != $idPop ){
								
				if( $idFornecedorAnt ) {
					
					if($idPop == $idPopAnt ){
						$matResultado[$idPop]['filhos'][$idFornecedorAnt]['total'] 		= number_format($ttFornecedor, 	   2,",",".");
						$matResultado[$idPop]['filhos'][$idFornecedorAnt]['totalPago']	= number_format($ttFornecedorPago, 2,",",".");
					}
					else {
						$matResultado[$idPopAnt]['filhos'][$idFornecedorAnt]['total']		= number_format($ttFornecedor,     2,",",".");
						$matResultado[$idPopAnt]['filhos'][$idFornecedorAnt]['totalPago']	= number_format($ttFornecedorPago, 2,",",".");
					}
				
				}
				
				$ttFornecedor 	  = 0;
				$ttFornecedorPago = 0;
				$c = 0;
				
				$matResultado[$idPop]['filhos'][$idFornecedor]['nome'] = $nomeFornecedor;
			
			}

			#Soma os valores das contas para os fornecedores e Pops
			$ttFornecedor 	+= $valor;
			$ttPop 			+= $valor;
			$ttFornecedorPago 	+= $valorPago;
			$ttPopPago 			+= $valorPago;
			
			$c = 0;
			
			#insere na matriz os dados das contas
			$matResultado[$idPop]['filhos'][$idFornecedor]['filhos'][$idContasAPagar] = array(
				$nomePlanoDeContasDetalhes,
				$dtVencimento, 
				number_format($valor    , 2,",","."), 
				number_format($valorPago, 2,",","."), 
				$vetorStatus[$statusContasAPagar]
			);

			$idPopAnt 		 = $idPop;
			$idFornecedorAnt = $idFornecedor;			
			
		}// laco da consulta
		#insere o dado total na matriz do útltimo fornecedor e pop
		$matResultado[$idPop]['filhos'][$idFornecedor]['total']		= number_format($ttFornecedor,		2,",",".");
		$matResultado[$idPop]['filhos'][$idFornecedor]['totalPago']	= number_format($ttFornecedorPago,	2,",",".");
		$matResultado[$idPop]['total']								= number_format($ttPop,     		2,",",".");
		$matResultado[$idPop]['totalPago']							= number_format($ttPopPago,			2,",",".");

	}//com resultados
	
	return ( $matResultado );
}

/**
 * Exibe na tela o relatório de contas à pagar por fornecedor baseado nos dados de $detalhes
 *
 * @param array $detalhes
 * @param array $matriz
 */
function contasAPagarFornecedorExibr( $detalhes, $matriz=array() ){

	global $corFundo, $corBorda, $sessLogin;
	
	$cabecalho 		= array( 'Conta à Pagar',	'Data Vencimento',	'Valor',	'Valor Pago',	'Situação' );
	$alinhamento	= array( 'left',			'center',			'right',	'right',		'center'   );
	$largura		= array( '50%',				'10%',				'15%',		'15%',			'10%'	   );
		
	novaTabela( "[Listagem de contas à pagar por Fornecedor]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, count($largura) );
	
	htmlAbreLinha( $corFundo );
	htmlAbreColuna( '100%', 'left', $corFundo, 0, 'normal10' );
		
	#mostra resultados dos pops
	if( count($detalhes) ) {
		foreach( $detalhes as $pop ) {
			echo "<br>";
			novaTabela( $pop['nome'], "center", '100%', 0, 2, 1, $corFundo, $corBorda, count($largura) );
				
				# mostra o cabeçalho dos detalhes das contas
				htmlAbreLinha($corFundo);
				for( $i=0; $i<count( $cabecalho ); $i++){
					itemLinhaTMNOURL($cabecalho[$i], 'center', 'middle', $largura[$i], $corFundo, 0, 'tabfundo0');
				}
				htmlFechaLinha();
				
				# mostra os detalhes dos fornecedores do pop correspondente
				foreach( $pop['filhos'] as $fornecedor ) {
					
					if( $matriz['detalhar'] ){
						#mostra os detalhes da conta do fornecedor correspondente
						# mostra o nome do fornecedor correspondente
						htmlAbreLinha( $corFundo );
							itemLinhaNOURL($fornecedor['nome'], $alinhamento[0], $corFundo, 5, 'tabfundo1' );
						htmlFechaLinha();
						foreach ($fornecedor['filhos'] as $conta ) {
							
							htmlAbreLinha( $corFundo );
								# mostra os dados da conta
								foreach ( $conta as $i => $celula ) {
									itemLinhaNOURL( $celula, $alinhamento[$i], $corFundo, 0, 'normal10' );
								}
							htmlFechaLinha();										
						
						}
						#mostra o total das contas pelo fornecedor correspondente
						htmlAbreLinha( $corFundo );
							itemLinhaNOURL( 'Total do Fornecedor:',		$alinhamento[2], $corFundo, 2,	'tabfundo1' );
							itemLinhaNOURL( $fornecedor['total'],		$alinhamento[3], $corFundo, 0,	'normal10' );
							itemLinhaNOURL( $fornecedor['totalPago'],	$alinhamento[3], $corFundo, 0,	'normal10' );
							itemLinhaNOURL( '&nbsp;', $alinhamento[3],	$corFundo, 		 0,				'tabfundo1' );
						htmlFechaLinha();	
					}
					else {
						htmlAbreLinha( $corFundo );
							itemLinhaNOURL( $fornecedor['nome'], 		$alinhamento[0], $corFundo, 2, 'tabfundo1' );
							itemLinhaNOURL( $fornecedor['total'],		$alinhamento[3], $corFundo, 0, 'normal10'  );
							itemLinhaNOURL( $fornecedor['totalPago'],	$alinhamento[3], $corFundo, 0, 'normal10'  );
							itemLinhaNOURL( '&nbsp;', $alinhamento[3],	$corFundo, 		 0,			   'tabfundo1' );
						htmlFechaLinha();	
					}
				
				}
				
				#mostra o total das contas pelo pop correspondentre
				htmlAbreLinha( $corFundo );
					itemLinhaNOURL( 'Total deste Pop:', 	$alinhamento[2],	$corFundo,	2,	'tabfundo0' );
					itemLinhaNOURL( $pop['total'],	 		$alinhamento[3], 	$corFundo,	0,	'normal10'  );
					itemLinhaNOURL( $pop['totalPago'],		$alinhamento[3],	$corFundo,	0,	'normal10'  );
					itemLinhaNOURL( '&nbsp;', 				$alinhamento[3], 	$corFundo, 0, 	'tabfundo0' );
				htmlFechaLinha();
			
			fechaTabela();
		}
		
	}
	else {
		echo "Nenhuma conta à pagar existente com os dados especificados";
	}
		
	htmlFechaColuna();
	htmlFechaLinha();	
	fechaTabela();
	
}


/**
 * Cria o relatório de contas à pagar por fornecedor baseado nos dados de $detalhes
 *
 * @param array $detalhes
 * @param array $matriz
 */
function contasAPagarFornecedorRelatorio( $detalhes, $matriz ){
	global $corFundo, $corBorda, $sessLogin;
	
	$cabecalho 		= array( 'Conta à Pagar',	'Data Vencimento',	'Valor',	'Valor Pago',	'Situação' );
	$alinhamento	= array( 'left',			'right',			'right',	'right',		'center'   );
	$largura		= array( '50%',				'10%',				'15%',		'15%',			'10%'	   );
		
	
	$matrizGrupo	= array();
	
	if( count($detalhes) ){
		#mostra resultados dos pops
		foreach( $detalhes as $pop ) {
					
			$matResultado	= array();
			$l 				= 0;
			$ttRecebido		= 0;
			
			# mostra os detalhes dos fornecedores do pop correspondente
			foreach( $pop['filhos'] as $fornecedor ) {
				
				if( $matriz['detalhar'] ){
									
					#mostra os detalhes da conta do fornecedor correspondente
					
					# mostra o nome do fornecedor correspondente
					$i = 0;
					$matResultado[$cabecalho[$i++]][$l] = "<b>".$fornecedor['nome']."</b>";
					$matResultado[$cabecalho[$i++]][$l] = "&nbsp;";
					$matResultado[$cabecalho[$i++]][$l] = "&nbsp;";
					$matResultado[$cabecalho[$i++]][$l] = "&nbsp;";
					$matResultado[$cabecalho[$i]][$l]   = "&nbsp;";
					$l++;
					
					# mostra as contas
					foreach ($fornecedor['filhos'] as $conta ) {
						
						# mostra os dados da conta					
						foreach( $conta as $i => $celula ){
							$matResultado[$cabecalho[$i]][$l] = $celula;
						}
						$l++;
					
					}
	
					#mostra o total das contas pelo fornecedor correspondente
					$i = 0;
					$matResultado[$cabecalho[$i++]][$l] = "&nbsp;";
					$matResultado[$cabecalho[$i++]][$l] = "<b>Total</b>";
					$matResultado[$cabecalho[$i++]][$l] = "<b>".$fornecedor['total']."</b>";
					$matResultado[$cabecalho[$i++]][$l] = "<b>".$fornecedor['totalPago']."</b>";
					$matResultado[$cabecalho[$i]][$l]   = "<b>&nbsp;</b>";
					$l++;
				}
				else {
					$i = 0;
					$matResultado[$cabecalho[$i++]][$l] = "<b>".$fornecedor['nome']."</b>";
					$matResultado[$cabecalho[$i++]][$l] = "&nbsp;";
					$matResultado[$cabecalho[$i++]][$l] = "<b>".$fornecedor['total']."</b>";
					$matResultado[$cabecalho[$i++]][$l] = "<b>".$fornecedor['totalPago']."</b>";
					$matResultado[$cabecalho[$i]][$l]   = "<b>&nbsp;</b>";
					$l++;				
				}
			
			}
	
			#mostra o total das contas pelo pop correspondentre
			$i = 0;
			$matResultado[$cabecalho[$i++]][$l] = "&nbsp;";
			$matResultado[$cabecalho[$i++]][$l] = "<b>Total do POP</b>";
			$matResultado[$cabecalho[$i++]][$l] = "<b>".$pop['total']."</b>";
			$matResultado[$cabecalho[$i++]][$l] = "<b>".$pop['totalPago']."</b>";
			$matResultado[$cabecalho[$i]][$l]   = "<b>&nbsp;</b>";
			
			$l++;
			
			# Alimentar Matriz Geral
			$matrizRelatorio['detalhe'] = $matResultado;
			
			# Alimentar Matriz de Header
			$matrizRelatorio['header']['TITULO']   = "CONTAS À PAGAR POR FORNECEDOR - ";
			$matrizRelatorio['header']['POP']	   = $pop['nome'];
			$matrizRelatorio['header']['IMG_LOGO'] = $html['imagem']['logoRelatorio'];
			
			# Configurações
			$matrizRelatorio['config']['linhas']	  = 35; //25
			$matrizRelatorio['config']['layout']	  = 'portrait';
			$matrizRelatorio['config']['marginleft']  = '1.0cm;';
			$matrizRelatorio['config']['marginright'] = '1.0cm;';
		
			$matrizGrupo[] = $matrizRelatorio;
		}
	
	}
	else {
		echo "<br>";
		$mensagem = "Não foram encontradas contas à pagar com os dados especificados.";
		avisoNOURL( "Aviso", $mensagem, 400 );
	}
	
	if( count($matrizGrupo) ) {

		# Converter para PDF:
		
		$nome = "contas_pagar_fornecedor";
		
		criaTemplates( $nome, $cabecalho, $alinhamento );
		
		$arquivo = k_reportHTML2PDF( k_report( $matrizGrupo, 'html' , $nome ) , $nome , $matrizRelatorio['config'] );
		
		if ( $arquivo ) {
			echo "<br>";
			novaTabela('Arquivos Gerados<a name=ancora></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
				htmlAbreLinha($corfundo);
					itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>Relatório de Contas à pagar por Fornecedor</a>",'pdf'), 'center', $corFundo, 7, 'txtaviso' );
				htmlFechaLinha();
			fechaTabela();
		}

	}

}
?>