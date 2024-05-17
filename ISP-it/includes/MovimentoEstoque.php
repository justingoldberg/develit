<?
################################################################################
#       Criado por: Desenvolvimento
#  Data de criação: 09/10/2006
# Ultima alteração: 09/10/2006
#    Alteração No.: 000
#
# Função:
#    Painel - Funções para gerenciamento de Entrada de Nota Fiscal

/**
 * Função contrutora do Módulo Movimento de Estoque
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function MovimentoEstoque( $modulo, $sub, $acao, $registro, $matriz ) {
	switch( $sub ) {
		case 'entrada_nf':
			EntradaNotaFiscal( $modulo, $sub, $acao, $registro, $matriz );
			break;
		case 'requisicao':
			RequisicaoRetorno( $modulo, $sub, $acao, $registro, $matriz );
			break;
		case 'ordemServico':
			OrdemServico( $modulo, $sub, $acao, $registro, $matriz );
			break;
	}
}

/**
 * Função de gerenciamento da tabela Movimento de Estoque
 *
 * @return unknown
 * @param array   $matriz
 * @param string  $tipo
 * @param string  $subTipo
 * @param unknown $condicao
 * @param unknown $ordem
 */
function dbMovimentoEstoque( $matriz, $tipo, $subTipo='', $condicao='', $ordem = '' ) {
	global $conn, $tb;
	$data = dataSistema();

	$bd = new BDIT();
	$bd->setConnection( $conn );
	$tabelas = $tb['MovimentoEstoque'];
	$campos  = array( 'id',		'idNFE',			 'idOrdemServico', 			'idRequisicao', 			'descricao',
	'tipo',   		'data' );
	$valores = array( 'NULL',	$matriz['idNFE'], $matriz['idOrdemServico'],	$matriz['idRequisicao'],	$matriz['descricao'],
	$matriz['tipo'],	$data['dataBanco'] );
	if ( $tipo == 'inserir' ){

		$retorno = $bd->inserir($tabelas, $campos, $valores);
	}

	if ( $tipo == 'alterar' ){
		array_shift( $campos ); //retira o campo id da lista de campos
		array_shift( $valores ); //retira o elemento NULL da lista de valores
		$retorno = $bd->alterar( $tabelas, $campos, $valores, $condicao );
	}

	if ( $tipo == 'consultar' ){
		if( $subTipo == 'itens' ) {
			$tabelas = "{$tb['MovimentoEstoque']}
						LEFT JOIN {$tb['ItensMovimentoEstoque']} ON ({$tb['ItensMovimentoEstoque']}.idMovimentoEstoque = {$tb['MovimentoEstoque']}.id)
						LEFT JOIN {$tb['Produtos']} ON ({$tb['Produtos']}.id = {$tb['ItensMovimentoEstoque']}.idProduto)
						LEFT JOIN {$tb['Unidades']} ON ({$tb['Unidades']}.id = {$tb['Produtos']}.idUnidade)";
			$campos  = array( "{$tb['MovimentoEstoque']}.*","{$tb['ItensMovimentoEstoque']}.id as idItensMovimentoEstoque", 
				"{$tb['ItensMovimentoEstoque']}.idProduto", "{$tb['ItensMovimentoEstoque']}.quantidade as qtde", "{$tb['Produtos']}.nome",
				"{$tb['Unidades']}.unidade");
			if( !is_array( $condicao ) ) { // verifica se não é array
				if( empty( $condicao ) ) { // se vazio já inicia um array como string
					$condicao = array(); 
				}
				else { // senão ele joga o seu conteudo para o array como o primeiro elemento
					$aux = $condicao;
					$condicao = array();
					$condicao[] = $aux;
				}
			}
		}
		$retorno = $bd->seleciona( $tabelas, $campos, $condicao, '', $ordem );
	}

	return ($retorno);
}

/**
 * Insere um movimento de entrada via nota fiscal
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function MovimentoEstoqueEntradaNF( $modulo, $sub, $acao, $registro, $matriz ) {
	global $sessCadastro, $tb;
	
	//Recolhe os demais dados do Movimento
	$matriz['descricao'] = 	"Entrada de produtos via Nota de Fiscal de n&ordm; " . $matriz['numNF']
	. ", do fornecedor " . $matriz['nomeFornecedor'] . ", no POP ".$matriz['nomePop'].".";
	$matriz['idOrdemServico'] = 0;
	$matriz['idRequisicao']   = 0;
	$matriz['tipo'] 		  = MovimentoEstoqueGetTipoEntrada();

	//insere um novo movimento
	if( $sessCadastro[$modulo.$sub.$acao] || dbMovimentoEstoque( $matriz, 'inserir' ) ) {
		$sessCadastro[$modulo.$sub.$acao] = "gravado";
		$matriz['idMovimentoEstoque'] = buscaUltimoID( $tb['MovimentoEstoque'] ); // busca a id desta nota
		
		// cria as matriz 
		$novaMatriz['idMovimentoEstoque'] = $matriz['idMovimentoEstoque'];
		$novaMatriz['lancarCP'] = $matriz['lancarCP'];
		$novaMatriz['status'] = $matriz['status'];
		$novaMatriz['idNFE'] = $matriz['idNFE'];
		
		itensMovimentoEstoqueNF( $modulo, $sub, 'novo_item', $registro, $novaMatriz );
		
	}
	else {
		avisoNOURL("Aviso", "Erro ao gravar os dados!", 400);
		dbEntradaNotaFiscal( '', 'excluir', '', "id={$matriz['idNFE']}" );
		echo "<br />";
		EntradaNotaFiscalListar( $modulo, $sub, $acao, $registro, $matriz );
	}

}

/**
 * Retorna o tipo de Movimento de Estoque Entrada
 *
 * @return char
 */
function MovimentoEstoqueGetTipoEntrada() {
	return "E";
}

/**
 * Retorna o tipo de Movimento de Estoque Sáida
 *
 * @return char
 */
function MovimentoEstoqueGetTipoSaida() {
	return "S";
}

/**
 * Retorna o tipo de Movimento de Estoque Retorno
 *
 * @return char
 */
function MovimentoEstoqueGetTipoRetorno() {
	return "R";
}

///**
// * Retorna um array com os produtos encontrados no ItensMovimentoEstoque 
// * fracionados (Usado Entrada de NF)
// *
// * @param integer $idMovimentoEstoque
// * @return array
// */
//function MovimentoEstoqueGetItensFracionados( $idMovimentoEstoque ) {
//	// busca os itens	
//	$itens = dbItensMovimentoEstoque( '', 'consultar', '', 'idMovimentoEstoque='.$idMovimentoEstoque );
//	$totalItens = count( $itens );
//	if( $totalItens ) { // se encontrou
//		$produtos = array();
//		for( $i = 0; $i < $totalItens; $i++ ) {
//			$produtos['idProduto'][$i] = $itens[$i]->idProduto;
//			$produtos['quantidade'][$i] = $itens[$i]->quantidade * $itens[$i]->qtdeFracionada; 
//		}
//	}
//
//	return $produtos;
//}

/**
 * Verifica os itens do Movimento de Estoque
 *
 * @param integer $idMovimentoEstoque
 * @return array
 */
function MovimentoEstoqueGetItens( $idMovimentoEstoque ){
	// busca os itens	
	$itens = dbItensMovimentoEstoque( '', 'consultar', '', 'idMovimentoEstoque='.$idMovimentoEstoque );
	$totalItens = count( $itens );
	if( $totalItens ) { // se encontrou
		$produtos = array();
		for( $i = 0; $i < $totalItens; $i++ ) {
			$produtos['idProduto'][$i] = $itens[$i]->idProduto;
			$produtos['quantidade'][$i] = $itens[$i]->quantidade; 
		}
	}

	return $produtos;	
}

/**
 * Lança os produtos no Estoque
 *
 * @param integer $idMovimentoEstoque
 * @param integer $idPop
 * @param boolean $fracionado
 * @return boolean
 */
function MovimentoEstoqueEntrada( $idMovimentoEstoque, $idPop, $fracionado = false, $url='' ) {
	
	$retorno = true;
	// busca os itens dos movimentos

	$itens = ( $fracionado ? MovimentoEstoqueGetItensFracionados( $idMovimentoEstoque ) 
							: MovimentoEstoqueGetItens( $idMovimentoEstoque ) );

	$totalItens = count ( $itens['idProduto'] );
	// busca os produtos no estoque existentes neste item
	if ( $totalItens ){
		// espcifica as condições para buscar os produtos em estoque
		$condicoes = array( 'idProduto IN (' . implode( ', ', $itens['idProduto']) . ')', 'idPop=' . $idPop );
		$produtosEstoque = dbProdutosEstoque( '', 'consultar', '', $condicoes );
		// loop que vai dar entrada de produtos no estoque
		for ( $i = 0; $i < $totalItens; $i++ ){
			// espcifica as condições para buscar os produtos em estoque
			$condicoes = array( 'idProduto=' . $itens['idProduto'][$i], 'idPop=' . $idPop );
			// busca os produtos no estoque existentes neste item
			$produtosEstoque = dbProdutosEstoque( '', 'consultar', '', $condicoes );
			if ( is_array( $produtosEstoque ) && count( $produtosEstoque ) == 1 ){
				// soma o produto do estoque com os dos itens que vão entrar
				$dados['quantidade'] = $produtosEstoque[0]->quantidade + $itens['quantidade'][$i];
				// grava a quantidade atualizada
				dbProdutosEstoque( $dados, 'alterar', 'entrada', 'id='. $produtosEstoque[0]->id );
			}
			else{
				$dados['idProduto']  = $itens['idProduto'][$i];
				$dados['idPop'] 	 = $idPop;
				$dados['quantidade'] = $itens['quantidade'][$i];
				dbProdutosEstoque( $dados, 'inserir' );
			}
		}
	}
	else {
		if( $url ) {
			// passa os dados da array $url para variaveis, cujos os nomes são os seus respectivos itens
			foreach ( $url as $i=>$argumento ) {
				$$i = $argumento;
			}
			// adiciona a idMovimentoEstoque para a $matriz
			$matriz['idMovimentoEstoque'] = $idMovimentoEstoque;
			if( $url['sub'] == 'requisicao' ) {
				$msg = "Não foi possível realizar a Baixa do Retorno de Produtos, pois não possui nenhum item cadastrado.";
				avisoNOURL( "Aviso", $msg, 400 );
				echo "<br />";
				RequisicaoRetornoVisualizar( $modulo, $sub, 'ver', $registro, $matriz );
			}
		}	 
		$retorno = false;
	}
	return $retorno;
}

/**
 * Cadastra Requisão em Movimento de Estoque
 *
 * @param string $modulo
 * @param string $sub
 * @param string $acao
 * @param integer $registro
 * @param array $matriz
 */
function MovimentoEstoqueRequisicaoRetorno( $modulo, $sub, $acao, $registro, $matriz ) {
	global $sessCadastro, $tb;
	//Recolhe os demais dados do Movimento
	$matriz['idNFE']			= 0;
	$matriz['idOrdemServico'] 	= 0;
	if( $matriz['tipo'] == 'E' ) {
		$matriz['descricao'] = 	"Entrada de produtos via Retorno de n&ordm; " . $matriz['idRequisicao']
		. ", pelo responsável " . $matriz['responsavel'] . ", no POP ".$matriz['nomePop'].".";
	}
	else {
		$matriz['descricao'] = 	"Saída de produtos via Requisicao de n&ordm; " . $matriz['idRequisicao']
		. ", pelo responsável " . $matriz['responsavel'] . ", no POP ".$matriz['nomePop'].".";
	}

	//insere um novo movimento
	if( $sessCadastro[$modulo.$sub.$acao] || dbMovimentoEstoque( $matriz, 'inserir' ) ) {
		$sessCadastro[$modulo.$sub.$acao] = "gravado";
		$matriz['idMovimentoEstoque'] = buscaUltimoID( $tb['MovimentoEstoque'] ); // busca a id desta nota
		
		// cria as matriz 
		$novaMatriz['idMovimentoEstoque'] = $matriz['idMovimentoEstoque'];
		$novaMatriz['status'] = $matriz['status'];
		$novaMatriz['idRequisicao'] = $matriz['idRequisicao'];
		
		itensMovimentoEstoqueRequisicao( $modulo, $sub, 'novo_item', $registro, $novaMatriz );
		
	}
	else {
		avisoNOURL("Aviso", "Erro ao gravar os dados!", 400);
		dbRequisicaoRetorno( '', 'excluir', '', "id={$matriz['idRequisicao']}" );
		echo "<br />";
		RequisicaoRetornoListar( $modulo, $sub, $acao, '', $matriz );
	}

}

/**
 * Gerencia a saída de Produtos do Estoque para Requisição e Ordem de Serviço.
 * 
 * $url => array contendo as variaveis da URL ($modulo, $acao, $sub, $registro e $matriz)
 * 
 * @param integer $idMovimentoEstoque
 * @param integer $idPop
 * @param array $url
 * @return boolean
 */
function MovimentoEstoqueSaida( $idMovimentoEstoque, $idPop, $url ) {

	$retorno = true;
	// busca os itens dos movimentos
	$itens = MovimentoEstoqueGetItens( $idMovimentoEstoque );
	$totalItens = count ( $itens['idProduto'] );
		
	// se possui itens de movimento de estoque
	if ( $totalItens ){
		// espcifica as condições para buscar os produtos em estoque
		$condicoes = array( 'idProduto IN (' . implode( ', ', $itens['idProduto']) . ')', 'idPop=' . $idPop );
		// busca os produtos no estoque existentes neste item
		$produtosEstoque = dbProdutosEstoque( '', 'consultar', '', $condicoes );
		$totalProdutosEstoque = count( $produtosEstoque );
		// busca os produtos com quantidade insuficiente que estão registrados para o determinado pop
		$produtosInsuficientes = produtosEstoqueGetInsuficientes( $produtosEstoque, $itens );
		$totalProdutosInsuficientes = count( $produtosInsuficientes['idProduto']);
		// se todos os itens requeridos tiverem as quantidades suficiente, então a baixa é realizada
		if ( $totalProdutosInsuficientes == 0 && $totalItens == $totalProdutosEstoque ){
			for ( $i = 0; $i < $totalItens; $i++ ){
				// soma o produto do estoque com os dos itens que vão entrar
				$dados['quantidade'] = $produtosEstoque[$i]->quantidade - $itens['quantidade'][$i];
				// grava a quantidade atualizada
				dbProdutosEstoque( $dados, 'alterar', 'entrada', 'id='. $produtosEstoque[$i]->id );	
			}
		}
		// senão ele avisa que não foi possivel realizar a baixa (saida) de produtos no estoque 
		// listando os produtos em estoque com quantidade insuficiente
		else{ 
			if( $totalItens != $totalProdutosEstoque ) {
				// reune em uma string todos os produtos que foram encontrados no estoque ...
				$produtos = '0';
				foreach( $produtosEstoque as $produto ){
					$produtos .= ', ' . $produto->idProduto;
				}
				// para então fazer a consulta dos itens que não estão cadastrados em estoque para o determinado POP
				$condicoes = array( 'idProduto NOT IN (' . $produtos . ')', 'idMovimentoEstoque=' . $idMovimentoEstoque );
				$itensNaoCadastrados = dbItensMovimentoEstoque( '', 'consultar', '', $condicoes );
				if( count( $itensNaoCadastrados ) ) {
					foreach( $itensNaoCadastrados as $item ) {
						// reune os dados numa matriz ...
						$dados['idProduto']  = $item->idProduto;
						$dados['idPop'] 	 = $idPop;
						$dados['quantidade'] = 0;
						// para registrar no estoque deste POP com quantidade zerada
						dbProdutosEstoque( $dados, 'inserir' );
						// e também é adiciono no array de produtos insuficientes
						$produtosInsuficientes['idProduto'][] = $item->idProduto;
						$produtosInsuficientes['qtdeRequirida'][] = $item->quantidade;
					}
				}
			}
			$msg = ( $url['sub'] == 'requisicao' ? 
				"Não foi possível realizar a Baixa da Requisição de Produtos." :
				"Não foi possível realizar a Baixa da Ordem de Serviço.");
			avisoNOURL( "Aviso", $msg, 400 );
			echo "<br />";
			// exibe produtos insufientes
			produtosEstoqueInsuficiente( $produtosInsuficientes, $idPop );
			echo "<br />";
			// passa os dados da array $url para variaveis, cujos os nomes são os seus respectivos itens
			foreach ( $url as $i=>$argumento ) {
				$$i = $argumento;
			}
			// adiciona a idMovimentoEstoque para a $matriz
			$matriz['idMovimentoEstoque'] = $idMovimentoEstoque;
			( $url['sub'] == 'requisicao' ?
				RequisicaoRetornoVisualizar( $modulo, $sub, 'ver', $registro, $matriz ) : 
				OrdemServicoVisualizar( $modulo, $sub, 'ver', $registro, $matriz ));
			$retorno = false;
		}
	}
	else {
		$msg = ( $url['sub'] == 'requisicao' ? 
			"Não foi possível realizar a Baixa da Requisição de Produtos, pois ela não possui nenhum item cadastrado." :
			"Não foi possível realizar a Baixa da Ordem de Serviço, pois ela não possui nenhum item cadastrado.");
		avisoNOURL( "Aviso", $msg, 400 );
		echo "<br />";
		// passa os dados da array $url para variaveis, cujos os nomes são os seus respectivos itens
		foreach ( $url as $i=>$argumento ) {
			$$i = $argumento;
		}
		// adiciona a idMovimentoEstoque para a $matriz
		$matriz['idMovimentoEstoque'] = $idMovimentoEstoque;
		( $url['sub'] == 'requisicao' ?
			RequisicaoRetornoVisualizar( $modulo, $sub, 'ver', $registro, $matriz ) : 
			OrdemServicoVisualizar( $modulo, $sub, 'ver', $registro, $matriz ));
		$retorno = false;
	}
	return $retorno;
}

/**
 * Insere um movimento de entrada via ordem de serviço
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function MovimentoEstoqueOrdemServico( $modulo, $sub, $acao, $registro, $matriz ) {
	global $sessCadastro, $tb;
	
	//Recolhe os demais dados do Movimento
	$matriz['descricao'] = 	"Ordem de Serviço de n&ordm; " . $matriz['idOrdemServico']
	. ", do cliente " . $matriz['nomeCliente'] . ", no POP ".$matriz['nomePop']. "."; 
	$matriz['idNFE'] = 0;
	$matriz['idRequisicao']   = 0;
	$matriz['tipo'] 		  = MovimentoEstoqueGetTipoSaida();

	//insere um novo movimento
	if( $sessCadastro[$modulo.$sub.$acao] || dbMovimentoEstoque( $matriz, 'inserir' ) ) {
		$sessCadastro[$modulo.$sub.$acao] = "gravado";
		$matriz['idMovimentoEstoque'] = buscaUltimoID( $tb['MovimentoEstoque'] ); // busca a id desta nota
		
		// cria as matriz 
		$novaMatriz['idMovimentoEstoque'] = $matriz['idMovimentoEstoque'];
		$novaMatriz['status'] = $matriz['status'];
		$novaMatriz['idOrdemServico'] = $matriz['idOrdemServico'];
		
		itensMovimentoEstoqueRequisicao( $modulo, $sub, 'novo_item', $registro, $novaMatriz );
		
	}
	else {
		avisoNOURL("Aviso", "Erro ao gravar os dados!", 400);
		dbOrdemServico( '', 'excluir', '', "id={$matriz['idOrdemServico']}" );
		echo "<br />";
		OrdemServicoListar( $modulo, $sub, $acao, $registro, $matriz );
	}
}

/**
 * Gerencia a saída de Produtos do Estoque para Requisição e Ordem de Serviço.
 * permitindo negativar a quantidade em estoque se a quantidade for insuficiente
 * $url => array contendo as variaveis da URL ($modulo, $acao, $sub, $registro e $matriz)
 * 
 * @param integer $idMovimentoEstoque
 * @param integer $idPop
 * @param array $url
 * @return boolean
 */
function MovimentoEstoqueNegativoSaida( $idMovimentoEstoque, $idPop, $url ) {
	$retorno = true;
	// busca os itens dos movimentos
	$itens = MovimentoEstoqueGetItens( $idMovimentoEstoque );
	$totalItens = count ( $itens['idProduto'] );
	
	// se possui itens de movimento de estoque
	if ( $totalItens ){
		// especifica as condições para buscar os produtos em estoque
		$condicoes = array( 'idProduto IN (' . implode( ', ', $itens['idProduto']) . ')', 'idPop=' . $idPop );
		// busca os produtos no estoque existentes neste item
		$produtosEstoque = dbProdutosEstoque( '', 'consultar', '', $condicoes );
		$totalProdutosEstoque = count( $produtosEstoque );
		// busca os produtos com quantidade insuficiente que estão registrados para o determinado pop
		$produtosInsuficientes = produtosEstoqueGetInsuficientes( $produtosEstoque, $itens );
		$totalProdutosInsuficientes = count( $produtosInsuficientes['idProduto']);
		
		if( $totalItens != $totalProdutosEstoque ) {
			// reune em uma string todos os produtos que foram encontrados no estoque ...
			$produtos = '0';
			foreach( $produtosEstoque as $produto ){
				$produtos .= ', ' . $produto->idProduto;
			}
			// para então fazer a consulta dos itens que não estão cadastrados em estoque para o determinado POP
			$condicoesNC = array( 'idProduto NOT IN (' . $produtos . ')', 'idMovimentoEstoque=' . $idMovimentoEstoque );
			$itensNaoCadastrados = dbItensMovimentoEstoque( '', 'consultar', '', $condicoesNC );
			if( count( $itensNaoCadastrados ) ) {
				foreach( $itensNaoCadastrados as $item ) {
					// reune os dados numa matriz ...
					$dados['idProduto']  = $item->idProduto;
					$dados['idPop'] 	 = $idPop;
					$dados['quantidade'] = 0;
					// para registrar no estoque deste POP com quantidade zerada
					dbProdutosEstoque( $dados, 'inserir' );
					// e também é adiciono no array de produtos insuficientes
					$produtosInsuficientes['idProduto'][] = $item->idProduto;
					$produtosInsuficientes['qtdeRequirida'][] = $item->quantidade;
				}
				$produtosEstoque = dbProdutosEstoque( '', 'consultar', '', $condicoes );
				$totalProdutosEstoque = count( $produtosEstoque );
			}
		}
		
		// verifica se todos os itens possuem cadastro em estoque
		if ( $totalItens == $totalProdutosEstoque ){
			for ( $i = 0; $i < $totalItens; $i++ ){
				// soma o produto do estoque com os dos itens que vão entrar
				$dados['quantidade'] = $produtosEstoque[$i]->quantidade - $itens['quantidade'][$i];
				// grava a quantidade atualizada
				dbProdutosEstoque( $dados, 'alterar', 'entrada', 'id='. $produtosEstoque[$i]->id );	
			}
			if( $totalProdutosInsuficientes ) {
				produtosEstoqueInsuficiente( $produtosInsuficientes, $idPop );
				echo "<br />";
			}
		}
		// senão ele avisa que não foi possivel realizar a baixa (saida) de produtos no estoque 
		// listando os produtos em estoque com quantidade insuficiente
		else{ 
			
			$msg = ( $url['sub'] == 'requisicao' ? 
				"Não foi possível realizar a Baixa da Requisição de Produtos." :
				"Não foi possível realizar a Baixa da Ordem de Serviço.");
			avisoNOURL( "Aviso", $msg, 400 );
			echo "<br />";
			// exibe produtos insufientes
			produtosEstoqueInsuficiente( $produtosInsuficientes, $idPop );
			echo "<br />";
			// passa os dados da array $url para variaveis, cujos os nomes são os seus respectivos itens
			foreach ( $url as $i=>$argumento ) {
				$$i = $argumento;
			}
			// adiciona a idMovimentoEstoque para a $matriz
			$matriz['idMovimentoEstoque'] = $idMovimentoEstoque;
			( $url['sub'] == 'requisicao' ?
				RequisicaoRetornoVisualizar( $modulo, $sub, 'ver', $registro, $matriz ) : 
				OrdemServicoVisualizar( $modulo, $sub, 'ver', $registro, $matriz ));
			$retorno = false;
		}
	}
	else {
		$msg = ( $url['sub'] == 'requisicao' ? 
			"Não foi possível realizar a Baixa da Requisição de Produtos, pois ela não possui nenhum item cadastrado." :
			"Não foi possível realizar a Baixa da Ordem de Serviço, pois ela não possui nenhum item cadastrado.");
		avisoNOURL( "Aviso", $msg, 400 );
		echo "<br />";
		// passa os dados da array $url para variaveis, cujos os nomes são os seus respectivos itens
		foreach ( $url as $i=>$argumento ) {
			$$i = $argumento;
		}
		// adiciona a idMovimentoEstoque para a $matriz
		$matriz['idMovimentoEstoque'] = $idMovimentoEstoque;
		( $url['sub'] == 'requisicao' ?
			RequisicaoRetornoVisualizar( $modulo, $sub, 'ver', $registro, $matriz ) : 
			OrdemServicoVisualizar( $modulo, $sub, 'ver', $registro, $matriz ));
		$retorno = false;
	}
	return $retorno;
	
}
?>