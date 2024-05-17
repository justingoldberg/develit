<?
################################################################################
#       Criado por: Desenvolvimento
#  Data de criação: 29/09/2006
# Ultima alteração: 29/09/2006
#    Alteração No.: 000
#
# Função:
#    Painel - Funções para gerenciamento dos itens produtos compostos para controle de estoque


/**
 * Gerencia tabela ItensProdutoComposto
 *
 * @param array $matriz
 * @param string $tipo
 * @param string $subTipo
 * @param unknown $condicao
 * @param unknown $ordem
 * @return unknown
 */
function dbItensProdutoComposto( $matriz, $tipo, $subTipo='', $condicao='', $ordem = '' ) {
	global $conn, $tb;
		
	$bd = new BDIT();
	$bd->setConnection( $conn );
	$tabelas = $tb['ItensProdutoComposto'];
	$campos  = array( 	'id',	'idProdutoComposto', 			'idProduto',			'quantidade'  );
	$valores = array(	'NULL', $matriz['idProdutoComposto'], 	$matriz['idProduto'], 	$matriz['quantidade'] );	
	if ( $tipo == 'inserir' ){

		$retorno = $bd->inserir($tabelas, $campos, $valores);
	}
		
	if ( $tipo == 'alterar' ){
		array_shift( $campos ); //retira o campo id da lista de campos
		array_shift( $valores ); //retira o elemento NULL da lista de valores
		$retorno = $bd->alterar( $tabelas, $campos, $valores, $condicao );
	}
	
	if ( $tipo == 'consultar' ){
		$retorno = $bd->seleciona( $tabelas, $campos, $condicao, '', $ordem );
	}
	
	if( $tipo == 'excluir' ){
		
		$retorno = $bd->excluir( $tabelas, $condicao );
	}
	if( $tipo == 'consultarNomeProduto' ){
		$tabelas = "{$tb['ItensProdutoComposto']} LEFT JOIN {$tb['Produtos']} ON ({$tb['ItensProdutoComposto']}.idProduto =  {$tb['Produtos']}.id )";
		$campos  = array( $tb['ItensProdutoComposto'].".*", "{$tb['Produtos']}.nome");
		$retorno = $bd->seleciona( $tabelas, $campos, $condicao, '', $ordem );
	}
	if( $tipo == 'consultarNomeUnidade' ){
		$tabelas = "{$tb['ItensProdutoComposto']} LEFT JOIN {$tb['Produtos']} ON ({$tb['ItensProdutoComposto']}.idProduto =  {$tb['Produtos']}.id )
					LEFT JOIN {$tb['Unidades']} ON ({$tb['Unidades']}.id =  {$tb['Produtos']}.idUnidade )";
		$campos  = array( $tb['ItensProdutoComposto'].".*", "{$tb['Produtos']}.nome", "{$tb['Unidades']}.unidade" );
		$retorno = $bd->seleciona( $tabelas, $campos, $condicao, '', $ordem );
	}
	
	return ($retorno);
}

/**
 * Adiciona itemProdutoComposto
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function itemProdutoCompostoAdicionar( $modulo, $sub, $acao, $registro, $matriz ){
	global $tb, $sessLogin, $sessCadastro;

	if( $matriz['bntConfirmarItem'] ) {
		$matriz['quantidade']		 = formatarValores( $matriz['quantidade'] );
		$matriz['idProdutoComposto'] = $registro; 
		if( $sessCadastro[$modulo.$sub.$acao] || itemProdutoCompostoValida( $matriz, $acao ) ){
			if( $sessCadastro[$modulo.$sub.$acao] || dbItensProdutoComposto( $matriz, 'inserir' ) ){ //grava o produto na forma primária
				avisoNOURL("Aviso", "Item do Produto Composto cadastrado com sucesso!", 410);
				echo "<br />";
				$sessCadastro[$modulo.$sub.$acao] = "gravado";
				produtoCompostoVer( $modulo, $sub, 'ver', $registro, $matriz );
				itemProdutoCompostoFormulario( $modulo, $sub, 'novo_item', $registro, array() );
				itemProdutoCompostoListar($modulo, $sub, 'listar', $registro, $matriz);
			}
			else {
				avisoNOURL("Aviso", "Erro ao gravar os dados!", 400);
				echo "<br />";
				$sessCadastro = '';
				produtoCompostoVer( $modulo, $sub, 'ver', $registro, $matriz );
				itemProdutoCompostoFormulario( $modulo, $sub, 'novo_item', $registro, array() );
				itemProdutoCompostoListar($modulo, $sub, 'listar', $registro, $matriz);
			}
		}
		else {
			avisoNOURL("Aviso", "Não foi possível gravar os dados! Os campos não foram preenchidos corretamente,
								ou o item já está cadastrado para este Produto Composto.", 400);
			echo "<br />";
			$sessCadastro = '';
			produtoCompostoVer( $modulo, $sub, 'ver', $registro, $matriz );
			itemProdutoCompostoFormulario( $modulo, $sub, 'novo_item', $registro, array() );
			itemProdutoCompostoListar($modulo, $sub, 'listar', $registro, $matriz);
		}
	}
	else {
		unset( $sessCadastro[$modulo.$sub.$acao] );
		produtoCompostoVer( $modulo, $sub, 'ver', $registro, $matriz );
		itemProdutoCompostoFormulario( $modulo, $sub, 'adicionar_item', $registro, $matriz );
		itemProdutoCompostoListar($modulo, $sub, 'listar', $registro, $matriz);
	}
}

/**
 * Mostra formulário para cadastro ou alteração de item do produto composto
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function itemProdutoCompostoFormulario( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda, $html, $tb;
	
	$i = 10; // indice de formulario
	novaTabela2("[".( $acao == "alterar_item" ? "Alterar" : "Adicionar").' Item do Produto Composto]<a name="ancora"></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		$extraItem 		= array( 'matriz[id]', 'matriz[idProdutoComposto]',  'matriz[idProduto]');
		$extraConteudo	= array( $matriz['id'], $matriz['idProdutoComposto'], $matriz['idProduto'] );
		abreFormularioComCabecalho( $modulo, $sub, $acao, $registro, $extraItem, $extraConteudo );
			getCampo('', '', '', '&nbsp;');
			#primeiro filtra o produto
			if ( !$matriz['idProduto'] && ( $acao == 'adicionar_item' || $acao == 'novo_item')){
				$condicao = " AND status = 'A'";	
				procurarProdutosSelect( $modulo, $sub, $acao, $registro, $matriz, $condicao );
			}
			else {
				getCamposOcultos( $extraItem, $extraConteudo );
				$produtos = dbProdutos( "", "consultar", "", "{$tb['Produtos']}.id='{$matriz['idProduto']}'", '' );
				$matriz['nomeProduto'] = $produtos[0]->nome;
				getCampo( 'combo', 'Produto', '', $matriz['nomeProduto'] );
				getCampo('text', _('Quantidade'), 'matriz[quantidade]', ( $matriz['quantidade'] ? formatarValoresForm( $matriz['quantidade'] ) : '' ) ,
						 'onblur="verificarValor(0,this.value);formataValor(this.value,'.$i++.')"','',13 );
				( $acao == "alterar_item" ? 
						getBotao( 'matriz[bntAlterarItem]', 			'Alterar') :
						getBotao( 'matriz[bntConfirmarItem]', 			'Confirmar') );		
		fechaFormulario();
			}
	fechaTabela();
		
}

/**
 * Valida se os dados de cadastro são validos
 *
 * @param array $matriz
 * @return boolean
 */
function itemProdutoCompostoValida( $matriz, $acao ) {
	$retorno = true;
	//verifica se este produto já não existe neste mesmo pop
	$consulta = dbItensProdutoComposto( '', 'consultar', '', "idProduto='". intval( $matriz['idProduto'] )."' AND idProdutoComposto='". intval( $matriz['idProdutoComposto'] )."' AND id <>'". intval( $matriz['id'] )."'");
	$retorno = verificaRegistroDuplicado( $consulta, $acao );
	$matriz['quantidade'] = formatarValores( $matriz['quantidade'] );
	if ( ( empty($matriz['quantidade']) ) || ( !is_numeric( $matriz['quantidade'] ) ) )  {
		$retorno = false;
	}
	if ( empty( $matriz['idProduto'] ) ) {
		$retorno = false;
	}
	return $retorno;
}

/**
 * Altera um item de um produto composto
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function itemProdutoCompostoAlterar( $modulo, $sub, $acao, $registro, $matriz ){
	global $sessCadastro;
	
	if( $matriz["bntAlterarItem"] ){// se clicou no botão confirmar e os dados são validos
		$matriz['quantidade']		 = formatarValores( $matriz['quantidade'] );
		if( itemProdutoCompostoValida( $matriz, $acao ) ) {
			// grava os dados atualizados
			$gravar = dbItensProdutoComposto( $matriz, 'alterar', "", "id='" . $matriz['id'] . "'" ); 
			if( $gravar ) { // se gravou avisa que gravou com sucesso e exibe a listagem
				avisoNOURL( "Aviso", "Item do Produto Composto alterado com sucesso!", 400 );
				echo "<br />";
				$matriz['bntAlterarItem'] = '';
				$sessCadastro = '';
				produtoCompostoVer( $modulo, $sub, $acao, $registro, $matriz );
				itemProdutoCompostoListar( $modulo, $sub, 'listar', $registro, array() );
			}
			else { // senão avisa que teve um erro e exibe o formulario de alteração
				avisoNOURL( "Erro", "Não foi possível gravar dados!", 400 );
				echo "<br>";
				$matriz['bntAlterarItem'] = '';
				$sessCadastro = '';
				produtoCompostoVer( $modulo, $sub, $acao, $registro, $matriz );
				itemProdutoCompostoListar( $modulo, $sub, 'listar', $registro, array() );
			}
		}
		else {
			avisoNOURL("Aviso", "Não foi possível gravar os dados! Os campos não foram preenchidos corretamente.", 400);
			echo "<br />";
			$matriz['bntAlterarItem'] = '';
			$sessCadastro = '';
			produtoCompostoVer( $modulo, $sub, $acao, $registro, $matriz );
			itemProdutoCompostoFormulario( $modulo, $sub, 'alterar_item', $registro, $matriz );
			itemProdutoCompostoListar( $modulo, $sub, 'listar', $registro, array() );
		}
	}
	else{
		// busca os dados do produto cadastrado
		$conta = dbItensProdutoComposto( '', "consultar","", "id='" . $matriz['id'] . "'" );
		if ( count( $conta ) ){ // se encontrou transfere os dados para matriz
			$matriz['idProdutoComposto']	= $conta[0]->idProdutoComposto;
			$matriz['idProduto']		  	= $conta[0]->idProduto;
			$matriz['id'] 					= $conta[0]->id;
			$matriz['quantidade'] 			= $conta[0]->quantidade;
			produtoCompostoVer( $modulo, $sub, $acao, $registro, $matriz );
			itemProdutoCompostoFormulario( $modulo, $sub, 'alterar_item', $registro, $matriz );
			itemProdutoCompostoListar( $modulo, $sub, 'listar', $registro, $matriz );
		}
		else{
			avisoNOURL("Erro", "Não foi possível localizar o Item Produto Composto!", 400);
			echo "<br>";
			produtoCompostoVer( $modulo, $sub, $acao, $registro, $matriz );
			itemProdutoCompostoListar( $modulo, $sub, 'listar', $registro, $matriz );
		}
	}
}

/**
 * Lista os itens do produto composto
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function itemProdutoCompostoListar( $modulo, $sub, $acao, $registro, $matriz ) {
	
	global $corFundo, $corBorda, $html, $sessLogin,  $tb;
		
	$largura 				= array('50%',	'20%',			'30%' );
	$gravata['cabecalho']   = array('Nome', 'Quantidade',	'Opções');
	$gravata['alinhamento'] = array('left', 'center',		'center');
	
	$qtdColunas = count( $largura );
	
	novaTabela("[Listagem de Itens do Produto Composto]", 'center', '100%', 0, 4, 1, $corFundo, $corBorda, $qtdColunas );
	
	if( $acao == 'listar' ) {
		$condicao = "{$tb['ItensProdutoComposto']}.idProdutoComposto='$registro' ";
	}
	else {
		$condicao = '';
	}
	
	$itens = dbItensProdutoComposto( "", "consultarNomeProduto", "", $condicao, "nome, id DESC" );
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
			
			$default = '<a href="?modulo=' . $modulo . '&sub=' . $sub . '&registro=' . $registro . '&matriz[id]='.$itens[$j]->id ;
			
			$opcoes = htmlMontaOpcao( $default . "&acao=alterar_item\">Alterar</a>", 'alterar' );
			$opcoes .= htmlMontaOpcao( $default . "&acao=excluir_item\">Excluir</a>", 'excluir' );
			$i = 0;
			htmlAbreLinha( $corFundo );
				itemLinhaTMNOURL( $itens[$j]->nome , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( formatarValoresForm( $itens[$j]->quantidade ) , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( $opcoes , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
			htmlFechaLinha();
			$j++;
		}
	}
	else {
		novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL( '<span class="txtaviso"><i>Não há itens cadastrados para este Produto Composto.</i><span>', 'left', 'middle', '100%', $corFundo, 4, 'normal10');
		fechaLinhaTabela();
	}
	
	fechaTabela();
}

/**
 * Exclui item do produto composto
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function itemProdutoCompostoExcluir( $modulo, $sub, $acao, $registro, $matriz ) {
	global $sessCadastro;
	
	if( !$matriz['bntConfirmar'] ) {
		produtoCompostoVer( $modulo, $sub, $acao, $registro, $matriz );
		itemProdutoCompostoVer( $modulo, $sub, 'excluir_item', $registro, $matriz );
		itemProdutoCompostoListar( $modulo, $sub, 'listar', $registro, $matriz );
	}
	else {
		$excluir = dbItensProdutoComposto( $matriz, 'excluir', "", "id='" . $matriz['id'] . "'");
		if( $excluir ) { // se excluiu exibe que excluiu com sucesso e exibe a listagem
			avisoNOURL( "Aviso", "Item do Produto Composto excluído com sucesso!", 400 );
			echo "<br />";
			$matriz['bntConfirmar'] = '';
			$sessCadastro = '';
			produtoCompostoVer( $modulo, $sub, $acao, $registro, $matriz );
			itemProdutoCompostoListar( $modulo, $sub, 'listar', $registro, $matriz );
		}
		else { // senão avisa que teve um erro e exibe o formulario de alteração
			avisoNOURL( "Erro", "Não foi possível excluir o item!", 400 );
			echo "<br>";
			$matriz['bntConfirmar'] = '';
			$sessCadastro = '';
			produtoCompostoVer( $modulo, $sub, $acao, $registro, $matriz );
			itemProdutoCompostoListar( $modulo, $sub, 'listar', $registro, $matriz );
		}
	}
}

/**
 * Exibe dados do item do Produto Composto
 *
 * @param string $modulo
 * @param string $sub
 * @param string $acao
 * @param integer $registro
 * @param array $matriz
 */
function itemProdutoCompostoVer( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda, $html, $tb;
	
	$itensProduto = dbItensProdutoComposto( '', "consultarNomeProduto", '', "{$tb['ItensProdutoComposto']}.id='" . $matriz['id'] . "'" );
	if( count( $itensProduto ) == 1 ) {
		$matriz['id']				 = $itensProduto[0]->id;
		$matriz['idProdutoComposto'] = $itensProduto[0]->idProdutoComposto;
		$matriz['idProduto']		 = $itensProduto[0]->idProduto;
		$matriz['quantidade'] 		 = $itensProduto[0]->quantidade;
		novaTabela2("[" . ( $acao  == "excluir_item" ? "Excluir" : "Vizualizar" ) . " Item do Produto Composto]" . "<a name='ancora'></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			$extraItem 		= array( 'matriz[id]', 'matriz[idProdutoComposto]' );
			$extraConteudo	= array( $matriz['id'], $matriz['idProdutoComposto'] );
			getCampo('', '', '', '&nbsp;');
			getCampo( 'combo', _('Produto'), '', 	$itensProduto[0]->nome );
			getCampo( 'combo', _('Quantidade'), '', formatarValoresForm( $itensProduto[0]->quantidade ) );
			if ( $acao == 'excluir_item' ){
				getBotao('matriz[bntConfirmar]', 'Excluir', 'submit','button', 'onclick="window.location=\'?modulo='.$modulo.
						 '&sub='.$sub.'&acao='.$acao.'&registro='.$registro.'&matriz[id]='.$matriz['id'].
						 '&matriz[bntConfirmar]=excluir\'"');
			}
			else {
				getCampo('', '', '', '&nbsp;');
			}
		fechaTabela();		
		
	}
	else {
		avisoNOURL("Erro", "Não foi possível localizar o Item do Produto Composto!", 400);
		echo "<br />";
		itemProdutoCompostoListar( $modulo, $sub, 'listar', $registro, $matriz );
	}	
}

/**
 * vizualização dos itens do produto composto
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function itemProdutoCompostoListagem( $modulo, $sub, $acao, $registro, $matriz ) {
	
	global $corFundo, $corBorda, $html, $sessLogin,  $tb;
		
	$largura 				= array('50%',	'30%',			'20%' );
	$gravata['cabecalho']   = array('Nome', 'Unidade',	'Quantidade');
	$gravata['alinhamento'] = array('left', 'left',		'right');
	
	$qtdColunas = count( $largura );
	
	novaTabela("[Visualização de Itens do Produto Composto]", 'center', '100%', 0, 4, 1, $corFundo, $corBorda, $qtdColunas );
	if( $acao == 'listar' ) {
		$condicao = "{$tb['ItensProdutoComposto']}.idProdutoComposto='$registro' ";
	}
	else {
		$condicao = '';
	}
	
	$itens = dbItensProdutoComposto( "", "consultarNomeUnidade", "", $condicao, "nome, id DESC" );
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
			
			$i = 0;
			htmlAbreLinha( $corFundo );
				itemLinhaTMNOURL( $itens[$j]->nome , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( $itens[$j]->unidade , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( formatarValoresForm( $itens[$j]->quantidade ) , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
			htmlFechaLinha();
			$j++;
		}
	}
	else {
		novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL( '<span class="txtaviso"><i>Não há itens cadastrados para este Produto Composto.</i></span>', 'left', 'middle', '100%', $corFundo, 4, 'normal10');
		fechaLinhaTabela();
	}
	
	fechaTabela();
}
?>