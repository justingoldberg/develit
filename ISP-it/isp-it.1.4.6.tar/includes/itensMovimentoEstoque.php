<?
################################################################################
#       Criado por: Desenvolvimento
#  Data de criação: 11/10/2006
# Ultima alteração: 11/10/2006
#    Alteração No.: 000
#
# Função:
#    Painel - Funções para gerenciamento de Itens de Movimento de Estoque

/**
 * Função de gerenciamento da tabela ItensMovimentoEstoque
 *
 * @return unknown
 * @param array   $matriz
 * @param string  $tipo
 * @param string  $subTipo
 * @param unknown $condicao
 * @param unknown $ordem
 */
function dbItensMovimentoEstoque( $matriz, $tipo, $subTipo='', $condicao='', $ordem = '' ) {
	global $conn, $tb;

	$bd = new BDIT();
	$bd->setConnection( $conn );
	$tabelas = $tb['ItensMovimentoEstoque'];
	$campos  = array( 'id',		'idMovimentoEstoque',			'idProduto', 			'quantidade',  			'valor' );
	$valores = array( 'NULL',	$matriz['idMovimentoEstoque'], 	$matriz['idProduto'],	$matriz['quantidade'],	$matriz['valor'] );
	if ( $tipo == 'inserir' ){
		$retorno = $bd->inserir($tabelas, $campos, $valores);
	}
	
	if ( $tipo == 'alterar' ){
		array_shift( $campos ); //retira o campo id da lista de campos
		array_shift( $valores ); //retira o elemento NULL da lista de valores
		$retorno = $bd->alterar( $tabelas, $campos, $valores, $condicao );
	}

	if ( $tipo == 'consultar' ){
		if( $subTipo == 'completa' ) {
			$tabelas =	"{$tb['ItensMovimentoEstoque']}\n".
						"LEFT JOIN {$tb['Produtos']} ON ({$tb['ItensMovimentoEstoque']}.idProduto = {$tb['Produtos']}.id)\n".
						"LEFT JOIN {$tb['Unidades']} ON ({$tb['Produtos']}.idUnidade = Unidades.id)\n";
			$campos =	array("{$tb['ItensMovimentoEstoque']}.*", "{$tb['Produtos']}.nome as produto", "Unidades.unidade" );
		}
		if( $subTipo == 'requisicao' ) {
			$tabelas =	"{$tb['ItensMovimentoEstoque']}\n".
						"INNER JOIN {$tb['Produtos']} ON ({$tb['ItensMovimentoEstoque']}.idProduto = {$tb['Produtos']}.id)\n".
						"INNER JOIN {$tb['MovimentoEstoque']} ON ({$tb['MovimentoEstoque']}.id = {$tb['ItensMovimentoEstoque']}.idMovimentoEstoque)\n".
						"LEFT JOIN {$tb['Unidades']} ON ({$tb['Produtos']}.idUnidade = {$tb['Unidades']}.id)\n".
						"LEFT JOIN {$tb['RequisicaoRetorno']} ON ({$tb['RequisicaoRetorno']}.id = {$tb['MovimentoEstoque']}.idRequisicao)\n".
						"LEFT JOIN {$tb['POP']} ON ({$tb['POP']}.id = {$tb['RequisicaoRetorno']}.idPop)\n";
			$campos =	array("{$tb['ItensMovimentoEstoque']}.*", "{$tb['Produtos']}.nome as produto", "{$tb['Unidades']}.unidade",
						"{$tb['MovimentoEstoque']}.data","IF({$tb['MovimentoEstoque']}.idRequisicao > 0, 'Requisição', 'Ordem de Serviço' ) as tipoSaida",
						"{$tb['POP']}.nome as pop" );
		}
		if( $subTipo == 'ordem' ) {
			$tabelas =	"{$tb['ItensMovimentoEstoque']}\n".
						"INNER JOIN {$tb['Produtos']} ON ({$tb['ItensMovimentoEstoque']}.idProduto = {$tb['Produtos']}.id)\n".
						"INNER JOIN {$tb['MovimentoEstoque']} ON ({$tb['MovimentoEstoque']}.id = {$tb['ItensMovimentoEstoque']}.idMovimentoEstoque)\n".
						"LEFT JOIN {$tb['Unidades']} ON ({$tb['Produtos']}.idUnidade = {$tb['Unidades']}.id)\n".
						"LEFT JOIN {$tb['OrdemServico']} ON ({$tb['OrdemServico']}.id = {$tb['MovimentoEstoque']}.idOrdemServico)\n".
						"LEFT JOIN {$tb['POP']} ON ({$tb['POP']}.id = {$tb['OrdemServico']}.idPop)\n";
			$campos =	array("{$tb['ItensMovimentoEstoque']}.*", "{$tb['Produtos']}.nome as produto", "{$tb['Unidades']}.unidade",
						"{$tb['MovimentoEstoque']}.data","IF({$tb['MovimentoEstoque']}.idRequisicao > 0, 'Requisição', 'Ordem de Serviço' ) as tipoSaida",
						"{$tb['POP']}.nome as pop" );
		}
		if( $subTipo == 'saida' ) {
			$tabelas =	"{$tb['ItensMovimentoEstoque']}\n".
						"INNER JOIN {$tb['Produtos']} ON ({$tb['ItensMovimentoEstoque']}.idProduto = {$tb['Produtos']}.id)\n".
						"INNER JOIN {$tb['MovimentoEstoque']} ON ({$tb['MovimentoEstoque']}.id = {$tb['ItensMovimentoEstoque']}.idMovimentoEstoque)\n".
						"LEFT JOIN {$tb['Unidades']} ON ({$tb['Produtos']}.idUnidade = {$tb['Unidades']}.id)\n".
						"LEFT JOIN {$tb['OrdemServico']} ON ({$tb['OrdemServico']}.id = {$tb['MovimentoEstoque']}.idOrdemServico)\n".
						"LEFT JOIN {$tb['RequisicaoRetorno']} ON ({$tb['RequisicaoRetorno']}.id = {$tb['MovimentoEstoque']}.idRequisicao)\n".
						"LEFT JOIN {$tb['POP']} ON ({$tb['POP']}.id = {$tb['OrdemServico']}.idPop OR {$tb['POP']}.id = {$tb['RequisicaoRetorno']}.idPop)\n";
			$campos =	array("{$tb['ItensMovimentoEstoque']}.*", "{$tb['Produtos']}.nome as produto", "{$tb['Unidades']}.unidade",
						"{$tb['MovimentoEstoque']}.data","IF({$tb['MovimentoEstoque']}.idRequisicao > 0, 'Requisição', 'Ordem de Serviço' ) as tipoSaida",
						"{$tb['POP']}.nome as pop" );
		}
		$retorno = $bd->seleciona( $tabelas, $campos, $condicao, '', $ordem );
	}
	if( $tipo == 'excluir' ){
		$retorno = $bd->excluir( $tabelas, $condicao );
	}

	return ($retorno);
}

/**
 * Gerencia o cadastro de item de Nota Fiscal 
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function itensMovimentoEstoqueNF( $modulo, $sub, $acao, $registro, $matriz ){
	global $tb;
	
	if( $matriz['bntConfirmarItem'] || $acao == 'excluir_itens' ) {
		$matriz['quantidade'] = formatarValores( $matriz['quantidade'] );
		//recupera valor do idMovimentoEstoque
		$movimento = dbMovimentoEstoque( "", "consultar", "", "{$tb['MovimentoEstoque']}.idNFE='".$registro."'", '' );
		if( $movimento ) {
			$matriz['idMovimentoEstoque'] = $movimento[0]->id;
		}
		if( $acao == 'excluir_itens' || itensMovimentoEstoqueNFValida( $matriz, $acao ) ) {
			// verifica as ações para gravar no BD e mostrar a mensagem corretamente
			$subAcao = explode( "_", $acao );
			switch( $subAcao[0] ) {
				case 'alterar':
					$tipo = 'alterar';
					$msg  = 'alterado';
					$condicao = "id='" . $matriz['id'] . "'";
					break;
				case 'excluir':
					$tipo = 'excluir';
					$msg  = 'excluído';
					$condicao = "id='" . $matriz['id'] . "'";
					break;
				default:
					$tipo = 'inserir';
					$msg  = 'cadastrado';
					$condicao = "";
					
					break;
			}
			
			if( dbItensMovimentoEstoque( $matriz, $tipo, '', $condicao ) ) {
				avisoNOURL( 'Aviso', 'Item de Nota Fiscal ' . $msg . ' com sucesso!', 400 );
				$matriz['idProduto'] = $matriz['quantidade'] =	$matriz['valor'] ='';
				$acao = 'novo_item';
			}
			else { // senão avisa que teve um erro e exibe o formulario de alteração
				avisoNOURL( "Erro", "Não foi possível " . $tipo . " dados!", 400 );
			}
			echo "<br />";
		}
		else {
			avisoNOURL("Aviso", "Não foi possível gravar os dados! Verifique se todos os campos foram preenchidos corretamente. <br /> 
						Ou verifique se o item já está registrado nesta Nota Fiscal.", 400);
			echo "<br />";			
		}
	}
	if( $acao == 'alterar_itens' ) {
		$consulta = dbItensMovimentoEstoque( '', 'consultar', '', 'id='.$matriz['id'] );
		if( count( $consulta ) ) {
			$dados = get_object_vars( $consulta[0] );
			$idNFE = $matriz['idNFE'];
			$id = $matriz['id'];
			$matriz = $dados;
			$matriz['id'] = $id;
			$matriz['idNFE'] = $idNFE;
		}
	}
	EntradaNotaFiscalVer( $modulo, $sub, 'ver', $registro, $matriz );
	itensMovimentoEstoqueFormularioNF( $modulo, $sub, $acao, $registro, $matriz );
	itensMovimentoEstoqueNFListar( $modulo, $sub, 'listar', $registro, $matriz );
}

/**
 * Lista os itens de Nota Fiscal junto ao formulário
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function itensMovimentoEstoqueNFListar( $modulo, $sub, $acao, $registro, $matriz ){
	global $corFundo, $corBorda, $tb;
	
	$corDetalhe = 'tabfundo1';
	
	$largura 				= array( '30%',		'8%',	  	'11%',		'13%',		'14%',		'24%'  );
	$gravata['cabecalho']   = array( 'Produto', 'Unidade',	'Qtde',		'Valor', 	'Total',	'Opções' );
	$alinhamento			= array( 'left', 	'left', 	'right',	'right',	'right',	'center' );
	
	$qtdColunas = count( $largura );
	
	$movimento = dbMovimentoEstoque( "", "consultar", "", "{$tb['MovimentoEstoque']}.idNFE='".$registro."' AND {$tb['MovimentoEstoque']}.tipo='E'", '' );
	if( $movimento ) {
		$matriz['idMovimentoEstoque'] = $movimento[0]->id;
	}
	
	// exibe os dados do cabeçalho da NF
	$itens = dbItensMovimentoEstoque( '', 'consultar', 'completa', "idMovimentoEstoque=".intval( $matriz['idMovimentoEstoque'] ) );
	$totalItens = count( $itens );
	
	novaTabela2( '[Itens da Nota Fiscal de Entrada]<a name="ancora"></a>', center, "100%", 0, 2, 1, $corFundo, $corBorda, $qtdColunas );
		htmlAbreLinha($corFundo);
			if( $totalItens > 0 ) {
				for( $i = 0; $i < $qtdColunas; $i++ ){
					itemLinhaTMNOURL( $gravata['cabecalho'][$i], $alinhamento[$i], 'middle', $largura[$i], $corFundo, 0, 'tabfundo0' );
				}
				// valor total da NF
				$vlTotalNF = 0;
			//if( $totalItens > 0 ) {
				foreach( $itens as $item ) {
					$cc = 0;
					$def = "<a href=\"?modulo=$modulo&sub=$sub&registro=$registro&matriz[id]=$item->id&matriz[idNFE]=".$matriz['idNFE'].
						   "&matriz[idMovimentoEstoque]={$matriz['idMovimentoEstoque']}";
					$fnt = "<font size=\"2\">";
					$opcoes =htmlMontaOpcao($def."&acao=alterar_itens\">".$fnt."Alterar</font></a>",'alterar');
					$opcoes .=htmlMontaOpcao($def."&acao=excluir_itens\">".$fnt."Excluir</font></a>",'excluir');
					
					$vlTotalNF += $item->quantidade * $item->valor;
					novaLinhaTabela( $corFundo, '100%');
						itemLinhaTMNOURL( $item->produto, $alinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $corDetalhe );
						itemLinhaTMNOURL( $item->unidade, $alinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $corDetalhe );
						itemLinhaTMNOURL( number_format( $item->quantidade, 2,',','.' ), $alinhamento[$cc], 'middle', 
											$largura[$cc++], $corFundo, 0, $corDetalhe );
						itemLinhaTMNOURL( number_format( ( $item->valor ), 2, ',','.' ), $alinhamento[$cc], 'middle', 
											$largura[$cc++], $corFundo, 0, $corDetalhe );
						itemLinhaTMNOURL( number_format( ( $item->quantidade * $item->valor ), 2, ',','.' ), $alinhamento[$cc], 'middle', 
											$largura[$cc++], $corFundo, 0, $corDetalhe );
						itemLinhaTMNOURL( $opcoes, $alinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $corDetalhe );
					fechaLinhaTabela();
				}
				$btnBaixar = getSubmit( 'matriz[bntBaixar]', 'Lançar em Estoque', 'submit2','button', 'onclick="window.location=\'?modulo='.$modulo.
			   			 '&sub='.$sub.'&acao=baixar&registro='.$registro.'&matriz[bntBaixar]=Baixar'.
			   			  '&matriz[idMovimentoEstoque]=' . $matriz['idMovimentoEstoque'] . '&matriz[idNFE]=' . $matriz['idNFE'] . '\'"' );
				novaLinhaTabela( $corFundo, '100%');
					itemLinhaTMNOURL( '<b>Valor Total da Nota Fiscal: </b>', 'right', 'middle', '', $corFundo, 4, 'tabfundo0' );
					itemLinhaTMNOURL( '<b>R$ '.number_format( $vlTotalNF, 2, ',', '.').'</b>', 'right', 'middle', '', $corFundo, 0, 'tabfundo0' );
					itemLinhaTMNOURL( $btnBaixar, 'center', 'middle', '', $corFundo, 0, 'tabfundo0' );
				fechaLinhaTabela();
			}
			else {
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL( '<span class="txtaviso"><i>Não há itens cadastrados para esta Nota Fiscal de Fornecedor.</i></span>', 'left', 'middle', '100%', $corFundo, 4, 'normal10');
				fechaLinhaTabela();
			}			
		htmlFechaLinha();
	fechaTabela();
}

/**
 * Exibe o formulario de cadastro de item de movimento de estoque para Nota Fiscal
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function itensMovimentoEstoqueFormularioNF( $modulo, $sub, $acao, $registro, $matriz ){
	global $corFundo, $corBorda, $html, $tb;

	novaTabela2("[".( $acao == "alterar_itens" ? "Alterar" : "Adicionar").' Item ]<a name="ancora"></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );
			getCampo('', '', '', '&nbsp;');
			#primeiro filtra o produto
			if ( !$matriz['idProduto'] && ( $acao == 'adicionar_item' || $acao == 'novo_item') ){
				$condicao = " AND status = 'A'";	
				procurarProdutosSelect( $modulo, $sub, $acao, $registro, $matriz, $condicao );
			}
			else {
				$i = 8; // indice de formulario
				$ocultosNomes = array( 'matriz[idNFE]', 'matriz[idMovimentoEstoque]', 'matriz[id]', 'matriz[idProduto]');
				$ocultosValores = array( $matriz['idNFE'], $matriz['idMovimentoEstoque'], $matriz['id'], $matriz['idProduto']  );
				getCamposOcultos( $ocultosNomes, $ocultosValores );
				$produtos = dbProdutos( "", "consultar", "", "{$tb['Produtos']}.id='{$matriz['idProduto']}'", '' );
				$matriz['nomeProduto'] = $produtos[0]->nome; 
				if( $acao <> 'alterar_itens' ) { //verifica verifca se a acao é diferente de alterar
					$matriz['valor'] = $produtos[0]->valorBase; //se for ja exibira o valor base do produto no formulario
				}				
				getCampo( 'combo', 'Produto', '', $matriz['nomeProduto'] );
				getCampo( 'text',  _('Quantidade'),		'matriz[quantidade]', $matriz['quantidade'], 
						' onblur="verificarValor(0,this.value);formataValor(this.value,'.$i++.');
						calculaValor( this.value, document.forms[0].elements[\'matriz[valor]\'].value, \'matriz[valorTotal]\' );"', '', 13 ) ;
				getCampo('text', 'Valor Unitário','matriz[valor]', $matriz['valor'], 
						'onblur="verificarValor(0,this.value);formataValor(this.value,'.$i++.');
						calculaValor( document.forms[0].elements[\'matriz[quantidade]\'].value, this.value, \'matriz[valorTotal]\' );"', '', 13 );						
				getCampo('text', 'Valor Total','matriz[valorTotal]', 
				        formatarValoresForm( $matriz['valorTotal'] = ( $matriz['quantidade']*$matriz['valor'] == 0 ? //verifica se o valor do campo é zero 
				        '' : $matriz['quantidade']*$matriz['valor'] ) ),'class="textboxdisabled" disabled="disabled"','',13 ); //se for exibe o campo vazio
				getBotao( 'matriz[bntConfirmarItem]', 'Confirmar');
				fechaFormulario();
			}
	fechaTabela();
}

/**
 * Verifica se os dados do itens da NF Estão OK
 *
 * @param array  $matriz
 * @param string $acao
 * @return boolean
 */
function itensMovimentoEstoqueNFValida( $matriz, $acao = '' ) {
	$returno = true;
		//verifica se este produto já não existe neste mesmo pop
	$consulta = dbItensMovimentoEstoque( '', 'consultar', '', "idProduto='". intval( $matriz['idProduto'] )."' AND idMovimentoEstoque='". intval( $matriz['idMovimentoEstoque'] )."' AND id <>'". intval( $matriz['id'] )."'" );
	$retorno = verificaRegistroDuplicado( $consulta, $acao );
	if ( !$matriz['idProduto'] || empty($matriz['quantidade']) || !is_numeric( formatarValores( $matriz['quantidade'] ) ) || $matriz['quantidade'] <= 0)  {
		$retorno = false;
	}
	if ( empty($matriz['valor']) || !is_numeric( formatarValores( $matriz['valor'] ) ) || $matriz['valor'] <= 0 )  {
		$retorno = false;
	}
	return $retorno;
}

/**
 * Exibe os itens de movimento de estoque da listagem sem as opções de cadastro	
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function itensMovimentoEstoqueNFListagem( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda;
	
	$corDetalhe = 'tabfundo1';
	
	$largura 				= array( '40%',		'10%',	  	'14%',		'18%',		'18%'	);
	$gravata['cabecalho']   = array( 'Produto', 'Unidade',	'Qtde',		'Valor', 	'Total' );
	$alinhamento			= array( 'left', 	'left', 	'right',	'right',	'right' );
	
	$qtdColunas = count( $largura );

	$movimento = dbMovimentoEstoque( "", "consultar", "", "{$tb['MovimentoEstoque']}.idNFE='".$registro."'", '' );
	if( $movimento ) {
		$matriz['idMovimentoEstoque'] = $movimento[0]->id;
	}
	
	$itens = dbItensMovimentoEstoque( '', 'consultar', 'completa', "idMovimentoEstoque=".intval( $matriz['idMovimentoEstoque'] ) );
	$totalItens = count( $itens );
	
	novaTabela2( '[Itens da Nota Fiscal de Entrada]<a name="ancora"></a>', center, "100%", 0, 2, 1, $corFundo, $corBorda, $qtdColunas );
		htmlAbreLinha($corFundo);
			for( $i = 0; $i < $qtdColunas; $i++ ){
				itemLinhaTMNOURL( $gravata['cabecalho'][$i], $alinhamento[$i], 'middle', $largura[$i], $corFundo, 0, 'tabfundo0' );
			}
			if( $totalItens > 0 ) {
				
				$vlTotalNF = 0; // valor total da NF
				
				foreach( $itens as $item ) {
					$cc = 0;
					$vlTotalNF += $item->valor * $item->quantidade;
					novaLinhaTabela( $corFundo, '100%');
						itemLinhaTMNOURL( $item->produto, $alinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $corDetalhe );
						itemLinhaTMNOURL( $item->unidade, $alinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $corDetalhe );
						itemLinhaTMNOURL( number_format( $item->quantidade, 2,',','.' ), $alinhamento[$cc], 'middle', 
											$largura[$cc++], $corFundo, 0, $corDetalhe );
						itemLinhaTMNOURL( number_format( ( $item->valor ), 2, ',','.' ), $alinhamento[$cc], 'middle', 
											$largura[$cc++], $corFundo, 0, $corDetalhe );
						itemLinhaTMNOURL( number_format( ( $item->quantidade * $item->valor ), 2, ',','.' ), $alinhamento[$cc], 'middle', 
											$largura[$cc++], $corFundo, 0, $corDetalhe );
					fechaLinhaTabela();
				}
				novaLinhaTabela( $corFundo, '100%');
					itemLinhaTMNOURL( '<b>Valor Total da Nota Fiscal: </b>', 'right', 'middle', '', $corFundo, 4, 'tabfundo0' );
					itemLinhaTMNOURL( '<b>R$ '.number_format( $vlTotalNF, 2, ',', '.').'</b>', 'right', 'middle', '', $corFundo, 0, 'tabfundo0' );
				fechaLinhaTabela();
			}
			else {
				htmlAbreLinha($corFundo);
				itemLinhaTMNOURL( '<span class="txtaviso"><i>Nenhum item cadastrado!</i></span>', 'center', 'middle', $largura[$i], $corFundo, $qtdColunas, 'normal10' );
				htmlFechaLinha();
			}
		htmlFechaLinha();
	fechaTabela();
}

/**
 * Cadastra itens da Requisição/Retorno em Itens Movimento de Estoque 
 *
 * @param string $modulo
 * @param string $sub
 * @param string $acao
 * @param integer $registro
 * @param array $matriz
 */
function itensMovimentoEstoqueRequisicao( $modulo, $sub, $acao, $registro, $matriz ){
	global $tb;
	
	if( $matriz['bntConfirmarItem'] || $acao == 'excluir_item' ) {
		$matriz['quantidade'] = formatarValores( $matriz['quantidade'] );
		//recupera valor do idMovimentoEstoque
		$movimento = ( $sub == 'requisicao' ? 
			dbMovimentoEstoque( "", "consultar", "", "{$tb['MovimentoEstoque']}.idRequisicao='".$registro."'", '' ) :
			dbMovimentoEstoque( "", "consultar", "", "{$tb['MovimentoEstoque']}.idOrdemServico='".$registro."'", '' ) );
		if( $movimento ) {
			$matriz['idMovimentoEstoque'] = $movimento[0]->id;
		}
		if( $acao == 'excluir_item' || itensMovimentoEstoqueRequisicaoValida( $matriz, $acao ) ) {
			// verifica as ações para gravar no BD e mostrar a mensagem corretamente
			$subAcao = explode( "_", $acao );
			switch( $subAcao[0] ) {
				case 'alterar':
					$tipo = 'alterar';
					$msg  = 'alterado';
					$condicao = "id='" . $matriz['id'] . "'";
					break;
				case 'excluir':
					$tipo = 'excluir';
					$msg  = 'excluído';
					$condicao = "id='" . $matriz['id'] . "'";
					break;
				default:
					$tipo = 'inserir';
					$msg  = 'cadastrado';
					$condicao = "";
					
					break;
			}
			//executa o cadastro, a alteração ou exclusão de registros
			if( dbItensMovimentoEstoque( $matriz, $tipo, '', $condicao ) ) {
				avisoNOURL( 'Aviso', 'Item ' . $msg . ' com sucesso!', 400 );
				$matriz['idProduto'] = $matriz['quantidade'] =	'';
				$acao = 'novo_item';
			}
			else { // senão avisa que teve um erro e exibe o formulario de alteração
				avisoNOURL( "Erro", "Não foi possível " . $tipo . " dados!", 400 );
			}
			echo "<br />";
		}
		else {
			avisoNOURL("Aviso", "Não foi possível gravar os dados! Verifique se todos os campos foram preenchidos corretamente. <br /> 
						Ou verifique se o item já está cadastrado.", 400);
			echo "<br />";
		}
	}
	if( $acao == 'alterar_item' ) {
		$consulta = dbItensMovimentoEstoque( '', 'consultar', '', 'id='.$matriz['id'] );
		if( count( $consulta ) ) {
			$dados = get_object_vars( $consulta[0] );
			$idRequisicao = $matriz['idRequisicao'];
			$idOrdemServico = $matriz['idOrdemServico'];
			$matriz = $dados;
			$matriz['quantidade'] = formatarValoresForm( $matriz['quantidade'] );
			$matriz['idRequisicao'] 	= $idRequisicao;
			$matriz['idOrdemServico'] 	= $idOrdemServico;
		}
	}
	//verifica se foi pressionado o botão de inclusão de produto composto
	//se foi cadastra os itens
	getProdutoComposto( $modulo, $sub, $acao, $registro, $matriz );
	//deixa vazio o campo de quantidade no formulário de produto composto
	unset( $matriz['qtdePC'] );
	//verifica se a sub é requisição ou ordem de serviço
	if( $sub == 'requisicao' ) { 
		//exibe os dados da requisição
		RequisicaoRetornoVer( $modulo, $sub, 'ver', $registro, $matriz );
		//exibe o formulário para adicionar produto composto
		produtoCompostoGetFormulario( $modulo, $sub, 'novo_item', $registro, $matriz );
		//exibe o formulário para cadastro individual de itens
		itensMovimentoEstoqueFormularioRequisicao( $modulo, $sub, $acao, $registro, $matriz );
		//lista os itens já cadastrados para a requisição
		itensMovimentoEstoqueRequisicaoListar( $modulo, $sub, 'listar', $registro, $matriz );
	}
	else {
		//exibe os dados da ordem de serviço
		OrdemServicoVer( $modulo, $sub, 'ver', $registro, $matriz );
		//exibe o formulário para adicionar produto composto
		produtoCompostoGetFormulario( $modulo, $sub, 'novo_item', $registro, $matriz );
		//exibe o formulário para cadastro individual de itens
		itensMovimentoEstoqueFormularioRequisicao( $modulo, $sub, $acao, $registro, $matriz );
		//lista os itens já cadastrados para a ordem de serviço
		itensMovimentoEstoqueOrdemServicoListar( $modulo, $sub, 'listar', $registro, $matriz );
	}
		 
		
}

/**
 * Verifica se os dados do itens da Requisição/Retorno se estão OK
 *
 * @param array  $matriz
 * @param string $acao
 * @return boolean
 */
function itensMovimentoEstoqueRequisicaoValida( $matriz, $acao = '' ) {
	$retorno = true;
		//verifica se este produto já não existe neste mesmo movimento
	$consulta = dbItensMovimentoEstoque( '', 'consultar', '', "idProduto='". intval( $matriz['idProduto'] )."' AND idMovimentoEstoque='". intval( $matriz['idMovimentoEstoque'] )."' AND id <>'". intval( $matriz['id'] )."'" );
	$retorno = verificaRegistroDuplicado( $consulta, $acao );
	if ( !$matriz['idProduto'] || empty($matriz['quantidade']) || !is_numeric( formatarValores( $matriz['quantidade'] ) ) || $matriz['quantidade'] <= 0 )  {
		$retorno = false;
	}

	return $retorno;
}

/**
 * Lista os itens de Requisição junto ao formulário
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function itensMovimentoEstoqueRequisicaoListar( $modulo, $sub, $acao, $registro, $matriz ){
	global $corFundo, $corBorda, $html, $sessLogin,  $tb;
	
	$largura 				= array('40%',	'25', 		'15%',			'20%' );
	$gravata['cabecalho']   = array('Nome', 'Unidade',	'Quantidade',	'Opções');
	$gravata['alinhamento'] = array('left', 'left',		'right',		'center');
	
	$qtdColunas = count( $largura );
	
	novaTabela("[Listagem de Itens de Requisição/Retorno]", 'center', '100%', 0, 4, 1, $corFundo, $corBorda, $qtdColunas );
	
	$movimento = dbMovimentoEstoque( "", "consultar", "", "{$tb['MovimentoEstoque']}.idRequisicao='".$registro."'", '' );
	if( $movimento ) {
		$matriz['idMovimentoEstoque'] = $movimento[0]->id;
	}

	if( $acao == 'listar' ) {
		$condicao = "{$tb['ItensMovimentoEstoque']}.idMovimentoEstoque='".$matriz['idMovimentoEstoque']."'";
	}
	else {
		$condicao = '';
	}
		
	$itens = dbItensMovimentoEstoque( "", "consultar", "completa", $condicao, "nome, id DESC" );
	$totalItens = count($itens);
	
	if( $totalItens ){
		
		htmlAbreLinha($corFundo);
			for( $i = 0; $i < $qtdColunas; $i++ ){
				itemLinhaTMNOURL( $gravata['cabecalho'][$i], $gravata['alinhamento'][$i], 'middle', $largura[$i], $corFundo, 0, 'tabfundo0' );
			}
		htmlFechaLinha();
		
		# Setar registro inicial
		$j = 0;

		while( ( $j < $totalItens ) ) {
			
			$default = '<a href="?modulo=' . $modulo . '&sub=' . $sub . '&registro=' . $registro . '&matriz[id]='.$itens[$j]->id;
			
			$opcoes = htmlMontaOpcao( $default . "&acao=alterar_item\">Alterar</a>", 'alterar' );
			$opcoes .= htmlMontaOpcao( $default . "&acao=excluir_item\">Excluir</a>", 'excluir' );
			$i = 0;
			htmlAbreLinha( $corFundo );
				itemLinhaTMNOURL( $itens[$j]->produto , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( $itens[$j]->unidade , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( formatarValoresForm( $itens[$j]->quantidade ) , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( $opcoes , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
			htmlFechaLinha();
			$j++;
		}
	}
	else {
		novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL( '<span class="txtaviso"><i>Não há itens cadastrados para esta Requisição/Retorno.</i></span>', 'left', 'middle', '100%', $corFundo, 4, 'normal10');
		fechaLinhaTabela();
	}
	
	fechaTabela();
}

/**
 * Exibe formulário de requisição do itens de movimento de estoque
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function itensMovimentoEstoqueFormularioRequisicao( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda, $html, $tb;

	novaTabela2("[".( $acao == "alterar_item" ? "Alterar" : "Adicionar").' Item ]<a name="ancora"></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );
			getCampo('', '', '', '&nbsp;');
			#primeiro filtra o produto
			if ( !$matriz['idProduto'] && ( $acao == 'adicionar_item' || $acao == 'novo_item') ){
				$condicao = " AND status = 'A'";	
				procurarProdutosSelect( $modulo, $sub, $acao, $registro, $matriz, $condicao );
			}
			else {
				$i = 9; // indice de formulario
				$extraItem = array( 'matriz[idRequisicao]', 'matriz[idMovimentoEstoque]', 'matriz[idOrdemServico]', 'matriz[id]', 'matriz[idProduto]' );
				$extraConteudo = array( $matriz['idRequisicao'], $matriz['idMovimentoEstoque'], $matriz['idOrdemServico'], $matriz['id'], $matriz['idProduto'] );
				getCamposOcultos( $extraItem, $extraConteudo );
				$produtos = dbProdutos( "", "consultar", "", "{$tb['Produtos']}.id='{$matriz['idProduto']}'", '' );
				$matriz['nomeProduto'] = $produtos[0]->nome;
				getCampo( 'combo', 'Produto', '', $matriz['nomeProduto'] );
				getCampo( 'text',  _('Quantidade'),		'matriz[quantidade]', $matriz['quantidade'], 
						' onblur="verificarValor(0,this.value);formataNumero(this.value,'.$i++.',1)"', '', 13 ) ;
				getBotao( 'matriz[bntConfirmarItem]', 			'Confirmar');
				fechaFormulario();
			}
	fechaTabela();	
}

/**
 * Exibe os itens de movimento de estoque da listagem sem as opções de cadastro	
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function itensMovimentoEstoqueRequisicaoListagem( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda;
	$corDetalhe = 'tabfundo1';

	$largura 				= array( '50%',		'25%',	  	'25%'	);
	$gravata['cabecalho']   = array( 'Produto', 'Unidade',	'Qtde'	);
	$alinhamento		 	= array( 'left', 	'center', 	'center'	);
	
	$qtdColunas = count( $largura );

	$itens = dbItensMovimentoEstoque( '', 'consultar', 'completa', "idMovimentoEstoque=".intval( $matriz['idMovimentoEstoque'] ), "nome, id DESC" );
	$totalItens = count( $itens );
	
	novaTabela2( '[Itens de '. ( $sub == 'requisicao' ? 'Requisição/Retorno de Produtos' : 'Ordem de Serviço' ) . ']<a name="ancora"></a>', center, "100%", 0, 2, 1, $corFundo, $corBorda, $qtdColunas );
		htmlAbreLinha($corFundo);
			for( $i = 0; $i < $qtdColunas; $i++ ){
				itemLinhaTMNOURL( $gravata['cabecalho'][$i], $alinhamento[$i], 'middle', $largura[$i], $corFundo, 0, 'tabfundo0' );
			}
			if( $totalItens > 0 ) {
				foreach( $itens as $item ) {
					$cc = 0;
					novaLinhaTabela( $corFundo, '100%');
						itemLinhaTMNOURL( $item->produto, $alinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $corDetalhe );
						itemLinhaTMNOURL( $item->unidade, $alinhamento[$cc], 'middle', 
											$largura[$cc++], $corFundo, 0, $corDetalhe );
						itemLinhaTMNOURL( number_format( ( $item->quantidade ), 2, ',','.' ), $alinhamento[$cc], 'middle', 
											$largura[$cc++], $corFundo, 0, $corDetalhe );
					fechaLinhaTabela();
				}
			}
			else {
				htmlAbreLinha($corFundo);
				itemLinhaTMNOURL( '<span class="txtaviso"><i>Nenhum item cadastrado!</i></span>', 'center', 'middle', $largura[$i], $corFundo, $qtdColunas, 'normal10' );
				htmlFechaLinha();
			}
		htmlFechaLinha();
	fechaTabela();
}

/**
 * Enter description here...
 *
 * @param array $matriz
 * @param string $acao
 * @return unknown
 */
function itensMovimentoEstoqueGet( $matriz, $acao = '' ) {
	// busca os itens	
	$itens = dbItensMovimentoEstoque( '', 'consultar', '', "idProduto='". intval( $matriz['idProduto'] )."' AND idMovimentoEstoque='". intval( $matriz['idMovimentoEstoque'] )."'" );

	return $itens;
}

/**
 * Lista os itens da Ordem de Serviço junto ao formulário
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function itensMovimentoEstoqueOrdemServicoListar( $modulo, $sub, $acao, $registro, $matriz ){
	global $corFundo, $corBorda, $html, $sessLogin,  $tb;
	
	$largura 				= array('40%',	'25', 		'15%',			'20%' );
	$gravata['cabecalho']   = array('Nome', 'Unidade',	'Quantidade',	'Opções');
	$gravata['alinhamento'] = array('left', 'left',		'right',		'center');
	
	$qtdColunas = count( $largura );
	
	novaTabela("[Listagem de Itens de Ordem de Serviço]", 'center', '100%', 0, 4, 1, $corFundo, $corBorda, $qtdColunas );
	
	$movimento = dbMovimentoEstoque( "", "consultar", "", "{$tb['MovimentoEstoque']}.idOrdemServico='".$registro."'", '' );
	if( $movimento ) {
		$matriz['idMovimentoEstoque'] = $movimento[0]->id;
	}

	if( $acao == 'listar' ) {
		$condicao = "{$tb['ItensMovimentoEstoque']}.idMovimentoEstoque='".$matriz['idMovimentoEstoque']."'";
	}
	else {
		$condicao = '';
	}
		
	$itens = dbItensMovimentoEstoque( "", "consultar", "completa", $condicao, "nome, id DESC" );
	$totalItens = count($itens);
	
	if( $totalItens ){
		
		htmlAbreLinha($corFundo);
			for( $i = 0; $i < $qtdColunas; $i++ ){
				itemLinhaTMNOURL( $gravata['cabecalho'][$i], $gravata['alinhamento'][$i], 'middle', $largura[$i], $corFundo, 0, 'tabfundo0' );
			}
		htmlFechaLinha();
		
		# Setar registro inicial
		$j = 0;

		while( ( $j < $totalItens ) ) {
			
			$default = '<a href="?modulo=' . $modulo . '&sub=' . $sub . '&registro=' . $registro . '&matriz[id]='.$itens[$j]->id;
			
			$opcoes = htmlMontaOpcao( $default . "&acao=alterar_item\">Alterar</a>", 'alterar' );
			$opcoes .= htmlMontaOpcao( $default . "&acao=excluir_item\">Excluir</a>", 'excluir' );
			$i = 0;
			htmlAbreLinha( $corFundo );
				itemLinhaTMNOURL( $itens[$j]->produto , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( $itens[$j]->unidade , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( formatarValoresForm( $itens[$j]->quantidade ) , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( $opcoes , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
			htmlFechaLinha();
			$j++;
		}
	}
	else {
		novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL( '<span class="txtaviso"><i>Não há itens cadastrados para esta Ordem de Serviço.</i></span>', 'left', 'middle', '100%', $corFundo, 4, 'normal10');
		fechaLinhaTabela();
	}
	
	fechaTabela();
}

/**
 * Calcula o valor total de uma nota fiscal
 *
 * @param integer $id
 * @return float
 */
function itensMovimentoEstoqueVlTotalNFE( $id ) {
	global $tb;
	
	$valorTotal = 0;
	$consulta = dbMovimentoEstoque( '', 'consultar', '', "{$tb['MovimentoEstoque']}.idNFE = '". intval( $id ) ."'",'' );
	if( $consulta ) {
		$idMovimentoEstoque = $consulta[0]->id;
		$itens = dbItensMovimentoEstoque( '', 'consultar', '', "{$tb['ItensMovimentoEstoque']}.idMovimentoEstoque = '". intval( $idMovimentoEstoque ) ."'",'' );
		$totalItens = count($itens);
		if( $totalItens ) {
			for( $i=0; $i< $totalItens ; $i++ ) {
				$valorTotal += $itens[$i]->quantidade * $itens[$i]->valor;
			}
		}		
	}
	return $valorTotal;
}

/**
 * Exibe opção de devolução de itens de nota fiscal e a relação de itens para confirmação
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function itensMovimentoEstoqueFormNFDevolver( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda, $html, $tb;
	
	$largura 				= array('5%',		'45%',		'10%', 		'20%',		'20%'	        );
	$gravata['cabecalho']   = array('', 		'Produto', 	'Unidade',	'Qtde',		'Qtde Devolução');
	$gravata['alinhamento'] = array('center',	'left', 	'center',	'center',	'center'        );
	$qtdColunas = count( $largura );
	
	$css_ = 'tabfundo1';
	novaTabela2( '[Devolução de Itens da Nota Fiscal de Fornecedor]<a name="ancora"></a>', 'center', '100%', 0, 2, 1, $corFundo, $corBorda, 5);
		abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );
			novaLinhaTabela( $corFundo, '100%' );
				itemLinhaTMNOURL('&nbsp;', 'right', 'middle', 1, $corFundo, 5, 'tabfundo1');
			htmlFechaLinha();
			
			//consulta dados da nota fiscal
			$NF = dbEntradaNotaFiscal( '', "consultar", 'completa', "{$tb['EntradaNotaFiscal']}.id='" . $registro . "'" );
			if( count( $NF ) > 0 ) {
				$numNF= $NF[0]->numNF;
				$fornecedor = $NF[0]->nomeFornecedor;
				$idPop = $NF[0]->idPop;
			}			
			
			
			$consulta = EntradaNotaFiscalConsultaDevolucao( $registro );
			$totalS = count($consulta['saida']);
			if( $totalS ) {		//verifica se já há um movimento dessa nota com o tipo de saida
				$idMovimentoEstoqueS = $consulta['saida'][0]->id; //se tiver grava o id do movimento de saida para ser enviado como campo oculto
			}
			$totalE = count($consulta['entrada']);
			
			//verifica consulta para determinar a exibição dos tipos de devolução
			if( EntradaNotaFiscalVerificaDevolucao( $registro ) && !$totalS) {
				$itens  = array(  0,			"T", 		"P",				);
				$labels = array( "Selecione",	"Total",	"Parcial"	);
			}
			else {
				$itens  = array( 0,				"P"			);
				$labels = array( "Selecione:",	"Parcial"	);
			}
				
			#primeiro filtra o tipo de devolução
			if( !$matriz['tipo'] ) {
				#combo status
				novaLinhaTabela( $corFundo, '100%' );
					itemLinhaTMNOURL('<b>Tipo de Devolução:</b>', 'right', 'middle', 1, $corFundo, 2, 'tabfundo1');
					itemLinhaForm( getComboArray( 'matriz[tipo]', $labels, $itens, $matriz['tipo'], '', 'onchange="form.submit();"' ),'left','middle',$corFundo,3,'tabfundo1');
				htmlFechaLinha();				
			}
			else {
				novaLinhaTabela( $corFundo, '100%' );
					itemLinhaTMNOURL('<b>Tipo de Devolução:</b>', 'right', 'middle', 1, $corFundo, 2, 'tabfundo1');
					itemLinhaForm( getComboArray( 'matriz[tipo]', $labels, $itens, $matriz['tipo'], '', 'onchange="form.submit();"' ),'left','middle',$corFundo,3,'tabfundo1');
				htmlFechaLinha();
				novaLinhaTabela( $corFundo, '100%' );
					itemLinhaTMNOURL('&nbsp;', 'right', 'middle', 1, $corFundo, 5, 'tabfundo1');
				htmlFechaLinha();
				htmlAbreLinha($corFundo);
						itemLinhaTMNOURL( "Nota Fiscal: $numNF - $fornecedor", 'center', 'middle', '100%', $corFundo, 5, 'tabfundo0' );
				htmlFechaLinha();
				htmlAbreLinha($corFundo);
					for( $i = 0; $i < $qtdColunas; $i++ ){
						itemLinhaTMNOURL( '<b>'.$gravata['cabecalho'][$i].'</b>', $gravata['alinhamento'][$i], 'middle', $largura[$i], $corFundo, 0, 'tabfundo2' );
					}
				htmlFechaLinha();
				$j = 0;
				if( $matriz['tipo'] == "T" ) {
					while( $j < $totalE ) {
						htmlAbreLinha( $corFundo );
							$id =  $consulta['entrada'][$j]->idProduto;
							itemLinhaForm('&nbsp;','left', 	'middle', $corFundo, 0, $css_ );
							itemLinhaForm( getInput('text', 'matriz[nome]',$consulta['entrada'][$j]->nome,'', 40, 'textbox', true), 'left', 'middle', $corFundo, 0, $css_ );
							itemLinhaForm( getInput('text', 'matriz[unidade]',$consulta['entrada'][$j]->unidade, '', 10, 'textbox', true), 'center', 'middle', $corFundo, 0, $css_ );
							itemLinhaForm( getInput('text', "matriz[$j][quantidade]", number_format($consulta['entrada'][$j]->qtde, 2,',',''),
									'onblur="formataValor(this.value, this.name)" style="text-align: right;"', 13, 'textbox', true), 
									'center', 'middle', $corFundo, 0, $css_ );
							itemLinhaForm('&nbsp;','left', 'middle', $corFundo, 0, $css_ );
						htmlFechaLinha();
						$j++;
					}				
				}
				elseif( $matriz['tipo'] == "P" ) {				
					while( $j < $totalE ) {
						$qtde = $consulta['entrada'][$j]->qtde;
						for( $l=0; $l<$totalS; $l++ ) {
							if( ($consulta['entrada'][$j]->idProduto == $consulta['saida'][$l]->idProduto) ) {
								$qtde = $qtde - $consulta['saida'][$l]->qtde;
							}
						}
						htmlAbreLinha( $corFundo );
							$id =  $consulta['entrada'][$j]->idProduto;
							itemLinhaForm( getInput( 'checkbox', "matriz[id".$j."]", $id, "onclick=\"checaproduto(this.id);\"",5, 'textbox',false,$j), 'center', 'middle', $corFundo, 0, $css_ );
							itemLinhaForm( getInput('text', 'matriz[nome]',$consulta['entrada'][$j]->nome,'', 40, 'textbox', true), 'left', 'middle', $corFundo, 0, $css_ );
							itemLinhaForm( getInput('text', 'matriz[unidade]',$consulta['entrada'][$j]->unidade, '', 10, 'textbox', true), 'center', 'middle', $corFundo, 0, $css_ );
							itemLinhaForm( getInput('text', "matriz[quantidade".$j."]", number_format($qtde, 2,',',''),
									'onblur="formataValor(this.value, this.name)" style="text-align: right;"', 13, 'textbox', true), 
									'center', 'middle', $corFundo, 0, $css_ );
							itemLinhaForm( getCampoNumero( "matriz[qtde".$j."]", $qtde, 13, 'textbox', true, 
									"onblur=verificarValorPermitido(document.forms[0].elements['matriz[quantidade".$j."]'].value, this.value, this.name);", "campo".$j ), 
									'center', 'middle', $corFundo, 0, $css_ );
						htmlFechaLinha();
						$j++;
					}
				}
				htmlAbreLinha( $corFundo );
					$item = array('matriz[totalE]', 'matriz[totalS]','matriz[idMovimentoEstoque]', 'matriz[idPop]' );
					$conteudo = array( $totalE, $totalS, $idMovimentoEstoqueS, $idPop );
					itemLinhaForm(getCamposOcultos( $item, $conteudo ), 'center', 'middle',$corFundo, 1, $css_ );
					itemLinhaForm( getSubmit( 'matriz[bntDevolver]', 'Devolver' ), 'center', 'middle', $corFundo, 4, $css_ );
				htmlFechaLinha();
		fechaFormulario();
			} 
	fechaTabela();	
}

/**
 * Cadastro os itens de devolução do movimento de saída e realiza a baixa no estoque
 *
 * @param integer $registro
 * @param array $matriz
 * @return boolean
 */
function itensMovimentoEstoqueNFDevolver( $registro, $matriz ) {
	global $tb, $sessCadastro; 

	$retorno = false;

	$consulta = dbItensMovimentoEstoque('','consultar','',"{$tb['ItensMovimentoEstoque']}.idMovimentoEstoque = {$matriz['idMovimentoEstoque']}",'');
	$total = count($consulta);
	$alterados = 0;
	if( $total ) {
		for( $j=0; $j < $matriz['registros']; $j++) {
			$dados['idProduto']  		= $matriz['idProduto'][$j];
			$dados['quantidade'] 		= formatarValores( $matriz['quantidade'][$j]);
			$dados['idMovimentoEstoque']= $matriz['idMovimentoEstoque'];
			$alterado = false;
			for( $i=0; $i < $total; $i++ ){
				$idProduto = $consulta[$i]->idProduto;
				$qtde = $consulta[$i]->quantidade;
				if( $idProduto == $dados['idProduto'] ){
					$dados['quantidade'] = $qtde + $dados['quantidade'];
					$condicao = "{$tb['ItensMovimentoEstoque']}.id = {$consulta[$i]->id}";
					if( $sessCadastro[$modulo.$sub.$acao] || dbItensMovimentoEstoque($dados, 'alterar', '', $condicao, '')) {
						$alterado = true; $alterados++;						
					}
				}				
			}
			if( !$alterado ) {
				if( $sessCadastro[$modulo.$sub.$acao] || dbItensMovimentoEstoque( $dados, 'inserir' ) ) {
				$alterados++;
				}
			}	
		}				
	}
	else {
		for( $j=0; $j < $matriz['registros']; $j++) {
			$dados['idProduto']  		= $matriz['idProduto'][$j];
			$dados['quantidade'] 		= formatarValores($matriz['quantidade'][$j]);
			$dados['idMovimentoEstoque']= $matriz['idMovimentoEstoque'];
			if( dbItensMovimentoEstoque( $dados, 'inserir' ) ) {
				$alterados++;		
			}	
		}
	}
	if( $alterados == $matriz['registros']){ 
		$estoque = 0;
		for( $j=0; $j < $matriz['registros']; $j++){
			$dados['idProduto']  		= $matriz['idProduto'][$j];
			$dados['quantidade'] 		= formatarValores($matriz['quantidade'][$j]);
			$condicao = "{$tb['ProdutosEstoque']}.idPop = {$matriz['idPop']} AND {$tb['ProdutosEstoque']}.idProduto = {$dados['idProduto']}";
			$consultaEstoque = dbProdutosEstoque('', 'consultar', '', $condicao, '' );
			if(count($consultaEstoque)) {
				$qtde = $consultaEstoque[0]->quantidade;
				$id	  = $consultaEstoque[0]->id;
				$dados['quantidade'] = $qtde - $dados['quantidade'];
				if( dbProdutosEstoque($dados, 'alterar', 'entrada', "{$tb['ProdutosEstoque']}.id = $id", '')){
					$estoque++;
				}
			}
		}
		if( $estoque == $alterados ) {
			$retorno = true;
			$sessCadastro[$modulo.$sub.$acao] = "gravado";	
		}
		
	}
	return $retorno;
}
?>