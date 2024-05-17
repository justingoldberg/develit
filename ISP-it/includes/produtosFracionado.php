<?
################################################################################
#       Criado por: Desenvolvimento
#  Data de criação: 07/11/2006
# Ultima alteração: 07/11/2006
#    Alteração No.: 000
#
# Função:
#    Painel - Funções para gerenciamento dos itens produtos compostos para controle de estoque


/**
 * Gerencia tabela ProdutosFracionado
 *
 * @param array $matriz
 * @param string $tipo
 * @param string $subTipo
 * @param unknown $condicao
 * @param unknown $ordem
 * @return unknown
 */
function dbProdutosFracionado( $matriz, $tipo, $subTipo='', $condicao='', $ordem = '' ) {
	global $conn, $tb;
		
	$bd = new BDIT();
	$bd->setConnection( $conn );
	$tabelas = $tb['ProdutosFracionado'];
	$campos  = array( 	'id',	'idProduto', 			'idProdutoFracionado',			'quantidade'  );
	$valores = array(	'NULL', $matriz['idProduto'], 	$matriz['idProdutoFracionado'], 	$matriz['quantidade'] );	
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
		$tabelas = "{$tb['ProdutosFracionado']} LEFT JOIN {$tb['Produtos']} ON ({$tb['ProdutosFracionado']}.idProduto =  {$tb['Produtos']}.id )";
		$campos  = array( $tb['ProdutosFracionado'].".*", "{$tb['Produtos']}.nome" );
		$retorno = $bd->seleciona( $tabelas, $campos, $condicao, '', $ordem );
	}
	if( $tipo == 'consultarNomeUnidade' ){
		$tabelas = "{$tb['ProdutosFracionado']} LEFT JOIN {$tb['Produtos']} ON ({$tb['ProdutosFracionado']}.idProduto =  {$tb['Produtos']}.id )
					LEFT JOIN {$tb['Unidades']} ON ({$tb['Unidades']}.id =  {$tb['Produtos']}.idUnidade )";
		$campos  = array( $tb['ProdutosFracionado'].".*", "{$tb['Produtos']}.nome", "{$tb['Unidades']}.unidade" );
		$retorno = $bd->seleciona( $tabelas, $campos, $condicao, '', $ordem );
	}
	return ($retorno);
}

/**
 * Adiciona item ao Produto Fracionado
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function ProdutosFracionadoAdicionar( $modulo, $sub, $acao, $registro, $matriz ){
	global $tb, $sessLogin, $sessCadastro;
	
	if( $matriz['bntConfirmarItem'] ) {
		$matriz['quantidade'] = formatarValores( $matriz['quantidade'] );
		$matriz['idProdutoFracionado']  = $registro; 
		if( $sessCadastro[$modulo.$sub.$acao] || ProdutosFracionadoValida( $matriz, $acao ) ){
			if( $sessCadastro[$modulo.$sub.$acao] || dbProdutosFracionado( $matriz, 'inserir' ) ){ //grava o produto na forma primária
				avisoNOURL("Aviso", "Item do Produto Fracionado cadastrado com sucesso!", 410);
				echo "<br />";
				$sessCadastro[$modulo.$sub.$acao] = "gravado";
				produtosVer( $modulo, $sub, 'ver', $registro, $matriz );
				ProdutosFracionadoFormulario( $modulo, $sub, 'novo_item', $registro, $dados );
				ProdutosFracionadoListar($modulo, $sub, 'listar', $registro, $matriz );
						
			}
			else {
				avisoNOURL("Aviso", "Erro ao gravar os dados!", 400);
				echo "<br />";
				$sessCadastro = '';
				produtosVer( $modulo, $sub, 'ver', $registro, $matriz );
				ProdutosFracionadoFormulario( $modulo, $sub, 'novo_item', $registro, $dados );
				ProdutosFracionadoListar($modulo, $sub, 'listar', $registro, $matriz);
			}
		}
		else {
			avisoNOURL("Aviso", "Não foi possível gravar os dados! Os campos não foram preenchidos corretamente, 
						ou o item já está cadastrado.", 600);
			echo "<br />";
			$sessCadastro = '';
			produtosVer( $modulo, $sub, 'ver', $registro, $matriz );
			ProdutosFracionadoFormulario( $modulo, $sub, 'novo_item', $registro, $dados );
			ProdutosFracionadoListar($modulo, $sub, 'listar', $registro, $matriz);
		}
	}
	else {
		unset( $sessCadastro[$modulo.$sub.$acao] );
		produtosVer( $modulo, $sub, 'ver', $registro, $matriz );
		ProdutosFracionadoFormulario( $modulo, $sub, 'adicionar_item', $registro, $matriz );
		ProdutosFracionadoListar($modulo, $sub, 'listar', $registro, $matriz);
	}
}

/**
 * Mostra formulário para cadastro ou alteração do produto fracionado
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function ProdutosFracionadoFormulario( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda, $html, $tb;

	
	novaTabela2("[".( $acao == "alterar_item" ? "Alterar" : "Adicionar").' Item do Produto Fracionado]<a name="ancora"></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );
			getCampo('', '', '', '&nbsp;');
			#primeiro filtra o produto
			if ( !$matriz['idProduto'] && ( $acao == 'adicionar_item' || $acao == 'novo_item') ){
				$condicao = " AND status = 'A' AND id <> $registro AND fracionavel = 'N'";	
				procurarProdutosSelect( $modulo, $sub, $acao, $registro, $matriz, $condicao );
			}
			else {
				$i = 7; // indice de formulario
				$extraItem 		= array( 'matriz[id]', 'matriz[idProduto]', 'matriz[idProdutoFracionado]' );
				$extraConteudo	= array( $matriz['id'], $matriz['idProduto'], $matriz['idProdutoFracionado'] );
				getCamposOcultos( $extraItem, $extraConteudo );
				$produtos = dbProdutos( "", "consultar", "", "{$tb['Produtos']}.id='{$matriz['idProduto']}'", '' );
				$matriz['nomeProduto'] = $produtos[0]->nome;
				getCampo( 'combo', 'Produto', '', $matriz['nomeProduto'] );
				getCampo( 'text',  _('Quantidade'),		'matriz[quantidade]', $matriz['quantidade'], 
						' onblur="verificarValor(0,this.value);formataValor(this.value,'.$i++.')"', '', 13 ) ;
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
 * @param string  $acao
 * @return boolean
 */
function ProdutosFracionadoValida( $matriz, $acao ) {
	$retorno = true;
	$consulta = dbProdutosFracionado( '', 'consultar', '', "idProduto='". intval( $matriz['idProduto'] )."' AND idProdutoFracionado='". intval( $matriz['idProdutoFracionado'] )."' AND id <>'". intval( $matriz['id'] )."'");
	$retorno = verificaRegistroDuplicado( $consulta, $acao );
	$matriz['quantidade'] = formatarValores( $matriz['quantidade'] );
	if ( ( empty($matriz['quantidade']) ) || ( !is_numeric( $matriz['quantidade'] ) ) )  {
		$retorno = false;
	}
	if ( ( empty($matriz['idProduto']) ) || ( !is_numeric( $matriz['idProduto'] ) ) )  {
		$retorno = false;
	}
	if ( ( empty($matriz['idProdutoFracionado']) ) || ( !is_numeric( $matriz['idProdutoFracionado'] ) ) )  {
		$retorno = false;
	}
	return $retorno;
}

/**
 * Exibe dados do item do Produto Fracionado
 *
 * @param string $modulo
 * @param string $sub
 * @param string $acao
 * @param integer $registro
 * @param array $matriz
 */
function ProdutosFracionadoVer( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda, $html, $tb;
	$fracionado = dbProdutosFracionado( '', "consultarNomeProduto", '', "{$tb['ProdutosFracionado']}.id='" . $matriz['id'] . "'" );
	if( count( $fracionado ) == 1 ) {
		novaTabela2('[Vizualizar Produto Fracionado]<a name="ancora"></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);	
			abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );
				getCampo('', '', '', '&nbsp;');
				getCampo( 'combo', _('Produto'), '', $fracionado[0]->nome );
				getCampo( 'combo', _('Quantidade'), '', formatarValoresForm($fracionado[0]->quantidade) );
				if ( $acao == 'excluir_item' ){
					getBotao('matriz[bntExcluirItem]', 'Excluir', 'submit','button', 'onclick="window.location=\'?modulo='.$modulo.
							 '&sub='.$sub.'&acao='.$acao.'&registro='.$registro.'&matriz[id]='.$matriz['id'].
							 '&matriz[bntExcluirItem]=excluir\'"');
				}
				else {
					getCampo('', '', '', '&nbsp;');
				}
			fechaFormulario();
		fechaTabela();		
	}
}

/**
 * Lista os itens do produto fracionado
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function ProdutosFracionadoListar( $modulo, $sub, $acao, $registro, $matriz ) {
	
	global $corFundo, $corBorda, $html, $sessLogin,  $tb;
		
	$largura 				= array('40%',	'25', 		'15%',			'20%' );
	$gravata['cabecalho']   = array('Nome', 'Unidade',	'Quantidade',	'Opções');
	$gravata['alinhamento'] = array('left', 'left',		'right',		'center');
	
	$qtdColunas = count( $largura );
	
	novaTabela("[Listagem de Itens do Produto Fracionado]", 'center', '100%', 0, 4, 1, $corFundo, $corBorda, $qtdColunas );
	
	if( $acao == 'listar' ) {
		$condicao = "{$tb['ProdutosFracionado']}.idProdutoFracionado='$registro' ";
	}
	else {
		$condicao = '';
	}
	
	$itens = dbProdutosFracionado( "", "consultarNomeUnidade", "", $condicao, "nome, id DESC" );
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
				itemLinhaTMNOURL( $itens[$j]->unidade , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( formatarValoresForm( $itens[$j]->quantidade ) , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( $opcoes , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
			htmlFechaLinha();
			$j++;
		}
	}
	else {
		novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL( '<span class="txtaviso"><i>Não há itens cadastrados para este Produto Fracionado.</i></span>', 'left', 'middle', '100%', $corFundo, 4, 'normal10');
		fechaLinhaTabela();
	}
	
	fechaTabela();
}

/**
 * Exclui item do produto fracionado
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function ProdutosFracionadoExcluir( $modulo, $sub, $acao, $registro, $matriz ) {
	global $sessCadastro;
	
	if( !$matriz['bntExcluirItem'] ) {
		produtosVer( $modulo, $sub, $acao, $registro, $matriz );
		ProdutosFracionadoVer( $modulo, $sub, 'excluir_item', $registro, $matriz );
		ProdutosFracionadoListar( $modulo, $sub, 'listar', $registro, $matriz );
	}
	else {
		$excluir = dbProdutosFracionado( $matriz, 'excluir', "", "id='" . $matriz['id'] . "'");
		if( $excluir ) { // se excluiu exibe que excluiu com sucesso e exibe a listagem
			avisoNOURL( "Aviso", "Item do Produto Fracionado excluído com sucesso!", 400 );
			echo "<br />";
			$matriz['bntExcluirItem'] = '';
			$sessCadastro = '';
			produtosVer( $modulo, $sub, $acao, $registro, $matriz );
			ProdutosFracionadoListar( $modulo, $sub, 'listar', $registro, $matriz );
		}
		else { // senão avisa que teve um erro e exibe o formulario de alteração
			avisoNOURL( "Erro", "Não foi possível excluir o item!", 400 );
			echo "<br>";
			$matriz['bntExcluirItem'] = '';
			$sessCadastro = '';
			produtosVer( $modulo, $sub, $acao, $registro, $matriz );
			ProdutosFracionadoListar( $modulo, $sub, 'listar', $registro, $matriz );
		}
	}
}

/**
 * Altera um item do produto fracionado
 *
 * @param string  $modulo
 * @param string  $sub							
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function ProdutosFracionadoAlterar( $modulo, $sub, $acao, $registro, $matriz ) {
	global $sessCadastro;
	
	if( $matriz["bntAlterarItem"] ){// se clicou no botão confirmar e os dados são validos
		$matriz['quantidade']		 = formatarValores( $matriz['quantidade'] );
		if( ProdutosFracionadoValida( $matriz, $acao ) ) {
			// grava os dados atualizados
			$gravar = dbProdutosFracionado( $matriz, 'alterar', "", "id='" . $matriz['id'] . "'" ); 
			if( $gravar ) { // se gravou avisa que gravou com sucesso e exibe a listagem
				avisoNOURL( "Aviso", "Item do Produto Fracionado alterado com sucesso!", 400 );
				echo "<br />";
				$matriz['bntAlterarItem'] = '';
				$sessCadastro = '';
				produtosVer( $modulo, $sub, $acao, $registro, $matriz );
				ProdutosFracionadoListar( $modulo, $sub, 'listar', $registro, array() );
			}
			else { // senão avisa que teve um erro e exibe o formulario de alteração
				avisoNOURL( "Erro", "Não foi possível gravar dados!", 400 );
				echo "<br>";
				$matriz['bntAlterarItem'] = '';
				$sessCadastro = '';
				produtosVer( $modulo, $sub, $acao, $registro, $matriz );
				ProdutosFracionadoListar( $modulo, $sub, 'listar', $registro, array() );
			}
		}
		else {
			avisoNOURL("Aviso", "Não foi possível gravar os dados! Os campos não foram preenchidos corretamente.", 400);
			echo "<br />";
			$matriz['bntAlterarItem'] = '';
			$sessCadastro = '';
			produtosVer( $modulo, $sub, $acao, $registro, $matriz );
			ProdutosFracionadoFormulario( $modulo, $sub, 'alterar_item', $registro, $matriz );
			ProdutosFracionadoListar( $modulo, $sub, 'listar', $registro, array() );
		}
	}
	else{
		// busca os dados do produto cadastrado
		$conta = dbProdutosFracionado( '', "consultar","", "id='" . $matriz['id'] . "'" );
		if ( count( $conta ) ){ // se encontrou transfere os dados para matriz
			$matriz['id'] 					= $conta[0]->id;
			$matriz["idProdutoFracionado"]	= $conta[0]->idProdutoFracionado;
			$matriz["idProduto"]		  	= $conta[0]->idProduto;
			$matriz["quantidade"] 			= formatarValoresForm( $conta[0]->quantidade) ;
			produtosVer( $modulo, $sub, $acao, $registro, $matriz );
			ProdutosFracionadoFormulario( $modulo, $sub, $acao, $registro, $matriz );
			ProdutosFracionadoListar( $modulo, $sub, 'listar', $registro, $matriz );
		}
		else{
			avisoNOURL("Erro", "Não foi possível localizar o Item Produto Fracionado!", 400);
			echo "<br>";
			produtosVer( $modulo, $sub, $acao, $registro, $matriz );
			ProdutosFracionadoListar( $modulo, $sub, 'listar', $registro, $matriz );
		}
	}
}

/**
 * vizualização dos itens do produto fracionado
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function ProdutosFracionadoListagem( $modulo, $sub, $acao, $registro, $matriz ) {
	
	global $corFundo, $corBorda, $html, $sessLogin,  $tb;
		
	$largura 				= array('50%',	'30%',			'20%' );
	$gravata['cabecalho']   = array('Nome', 'Unidade',	'Quantidade');
	$gravata['alinhamento'] = array('left', 'left',		'right');
	
	$qtdColunas = count( $largura );
	
	novaTabela("[Visualização de Itens do Produto Fracionado]", 'center', '100%', 0, 4, 1, $corFundo, $corBorda, $qtdColunas );
	
	if( $acao == 'listar' ) {
		$condicao = "{$tb['ProdutosFracionado']}.idProdutoFracionado='$registro' ";
	}
	else {
		$condicao = '';
	}
	
	$itens = dbProdutosFracionado( "", "consultarNomeUnidade", "", $condicao, "nome, id DESC" );
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
				itemLinhaTMNOURL( '<span class="txtaviso"><i>Não há itens cadastrados para este Produto Fracionado.</i></span>', 'left', 'middle', '100%', $corFundo, 4, 'normal10');
		fechaLinhaTabela();
	}
	
	fechaTabela();
}
?>