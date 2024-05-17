<?
################################################################################
#       Criado por: Desenvolvimento
#  Data de criação: 06/10/2006
# Ultima alteração: 09/10/2006
#    Alteração No.: 000
#
# Função:
#    Painel - Funções para gerenciamento dos produtos para controle de estoque

/**
 * Construtor de módulo Estoque de Produtos
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function ProdutosEstoque( $modulo, $sub, $acao, $registro, $matriz ) {
	
	global $corFundo, $corBorda, $sessLogin, $html;

	# Permissão do usuario
	$permissao = buscaPermissaoUsuario($sessLogin['login'],'login','igual','login');
	
	if(!$permissao['admin']) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		// sistema de permissao diferente. por funcao ao inves de modulo.
		$titulo    = "<b>Estoque de Produtos</b>";
		$subtitulo = "Cadastro de Estoque de Produtos por POP"; 
		$itens  = Array( 'Novo', 'Procurar', 'Listar', 'Fracionar' );
		getHomeModulo( $modulo, $sub, $titulo, $subtitulo, $itens );
		echo "<br />";
		
		if( $acao == 'adicionar' ) {
			produtosEstoqueAdicionar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( $acao == 'alterar' ) {
			produtosEstoqueAlterar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( $acao == 'procurar' ) {
			produtosEstoqueProcurar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( strstr( $acao, '_fracionar' ) ) {
			produtosEstoqueFracionar( $modulo, $sub, $acao, $registro, $matriz );
		}
		else {		
			produtosEstoqueListar( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
}

/**
 * Função de gerenciamento da tabela Produto de Estoque
 *
 * @return unknown
 * @param array   $matriz
 * @param string  $tipo
 * @param string  $subTipo
 * @param unknown $condicao
 * @param unknown $ordem
 */
function dbProdutosEstoque( $matriz, $tipo, $subTipo='', $condicao='', $ordem = '' ) {
	global $conn, $tb;
		
	$bd = new BDIT();
	$bd->setConnection( $conn );
	$tabelas = $tb['ProdutosEstoque'];
	$campos  = array( 	'id',		'idProduto',			'idPop', 			'quantidade' );
	$valores = array(	'NULL', 	$matriz['idProduto'], 	$matriz['idPop'],	$matriz['quantidade'] );
	if ( $tipo == 'inserir' ){
		$retorno = $bd->inserir($tabelas, $campos, $valores);
	}
		
	if ( $tipo == 'alterar' ){
		array_shift( $campos ); //retira o campo id da lista de campos
		array_shift( $valores ); //retira o elemento NULL da lista de valores
		if ( $subTipo == 'entrada' ){
			$campos = array( 'quantidade');
			$valores = array( $matriz['quantidade'] );
		}
		$retorno = $bd->alterar( $tabelas, $campos, $valores, $condicao );
	}
	
	if ( $tipo == 'consultar' ){
		$retorno = $bd->seleciona( $tabelas, $campos, $condicao, '', $ordem );
	}
	
	if( $tipo == 'excluir' ){
		
		$retorno = $bd->excluir( $tabelas, $condicao );
	}
	
	if( $tipo == 'consultaCompleta' ){
		$tabelas = "{$tb['ProdutosEstoque']} 
						LEFT JOIN {$tb['Produtos']}	ON ({$tb['ProdutosEstoque']}.idProduto = {$tb['Produtos']}.id) 
						LEFT JOIN {$tb['Unidades']} ON ({$tb['Produtos']}.idUnidade =  {$tb['Unidades']}.id ) 
						LEFT JOIN {$tb['POP']}		ON ({$tb['ProdutosEstoque']}.idPop = {$tb['POP']}.id)";
		$campos  = array( $tb['ProdutosEstoque'].".*", $tb['Produtos'].".nome", $tb['Produtos'].".qtdeMinima",
					$tb['Produtos'].".fracionavel", "{$tb['Unidades']}.unidade", "{$tb['POP']}.nome as pop");
		$retorno = $bd->seleciona( $tabelas, $campos, $condicao, '', $ordem );
	}
	if( $tipo == 'consultaEstoque' ){
		$tabelas = "{$tb['ProdutosEstoque']} 
						LEFT JOIN {$tb['Produtos']}	ON ({$tb['ProdutosEstoque']}.idProduto = {$tb['Produtos']}.id)
						LEFT JOIN {$tb['ProdutosFracionado']}	ON ({$tb['ProdutosEstoque']}.idProduto = {$tb['ProdutosFracionado']}.idProduto) 
						LEFT JOIN {$tb['Unidades']} ON ({$tb['Produtos']}.idUnidade =  {$tb['Unidades']}.id )
						LEFT JOIN {$tb['POP']}		ON ({$tb['ProdutosEstoque']}.idPop = {$tb['POP']}.id)";
		$campos  = array( $tb['ProdutosEstoque'].".*", $tb['Produtos'].".nome", $tb['ProdutosFracionado'].".quantidade as qtde",
					$tb['ProdutosFracionado'].".idProdutoFracionado", "{$tb['Unidades']}.unidade", "{$tb['POP']}.nome as pop");
		$retorno = $bd->seleciona( $tabelas, $campos, $condicao, '', $ordem );
	}
	
	return ($retorno);
}

/**
 * Adiciona um produto ao estoque
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function produtosEstoqueAdicionar( $modulo, $sub, $acao, $registro, $matriz ) {
	global $sessCadastro;
	
	if( $matriz['bntConfirmar'] ) {
		if( $sessCadastro[$modulo.$sub.$acao] || produtosEstoqueValida( $matriz, $acao ) ){
			$matriz['quantidade'] = formatarValores( $matriz['quantidade'] );
			if( $sessCadastro[$modulo.$sub.$acao] || dbProdutosEstoque( $matriz, 'inserir' ) ){ //grava o produto na forma primária
				$sessCadastro[$modulo.$sub.$acao] = "gravado";
				avisoNOURL("Aviso", "Produto adicionado ao estoque com sucesso!", 400);
				echo "<br />";
				produtosEstoqueListar( $modulo, $sub, 'listar', $registro, $matriz );
			}
			else {
				avisoNOURL("Aviso", "Erro ao gravar os dados!", 400);
				echo "<br />";
				produtosEstoqueFormulario( $modulo, $sub, $acao, $registro, $matriz );
			}
		}
		else {
			avisoNOURL("Aviso", "Não foi possível gravar os dados! Verifique se todos os campos foram preenchidos corretamente. <br /> 
						Ou verifique se o POP especificado já possui este produto registrado em estoque.", 400);
			echo "<br />";
			produtosEstoqueFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
	else {
		produtosEstoqueFormulario( $modulo, $sub, $acao, $registro, $matriz );
	}
}

/**
 * Realiza a alteração de produtos no estoque
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function produtosEstoqueAlterar( $modulo, $sub, $acao, $registro, $matriz ) {
	if( $matriz["bntConfirmar"] ){ 
		if( produtosEstoqueValida( $matriz, $acao ) ){ // se clicou no botão confirmar e os dados são validos
			$matriz['quantidade'] = formatarValores( $matriz['quantidade'] );
			// grava os dados atualizados
			$gravar = dbProdutosEstoque( $matriz, 'alterar', "", "id='" . $registro . "'" ); 
			if( $gravar ) { // se gravou avisa que gravou com sucesso e exibe a listagem
				avisoNOURL( 'Aviso', 'Estoque do Produto alterado com sucesso!', 400 );
				echo "<br />";
				produtosEstoqueListar( $modulo, $sub, 'listar', '', $matriz );
			}
			else { // senão avisa que teve um erro e exibe o formulario de alteração
				avisoNOURL( "Erro", "Não foi possível gravar dados!", 400 );
				echo "<br />";
				produtosEstoqueFormulario( $modulo, $sub, $acao, $registro, $matriz );
			}
		}		
		else {
			avisoNOURL("Aviso", "Não foi possível gravar os dados! Verifique se todos os campos foram preenchidos corretamente. <br /> 
						Ou verifique se o  POP especificado já possui este produto registrado em estoque.", 400);
			echo "<br />";
			produtosEstoqueFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}		
	}
	else{
		// busca os dados do produto cadastrado
		$conta = dbProdutosEstoque( '', "consultar","", "id='" . $registro . "'" );
		if ( count( $conta ) ){ // se encontrou transfere os dados para matriz
			$matriz["idProduto"] 	= $conta[0]->idProduto;
			$matriz["id"] 			= $conta[0]->id;
			$matriz["idPop"] 		= $conta[0]->idPop;
			$matriz["quantidade"] 	= formatarValoresForm( $conta[0]->quantidade );
			produtosEstoqueFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}
		else{
			avisoNOURL("Erro", "Não foi possível localizar o Produto!", 400);
			echo "<br />";
			produtosEstoqueListar( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
}

/**
 * Exibe a listagem de produtos de estoque
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function produtosEstoqueListar( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda, $html, $sessLogin, $limite, $tb;
	
	$largura 				= array('35%',	'26%',	'15%', 			'14%',			'10%' );
	$gravata['cabecalho']   = array('Nome', 'POP', 	'Quantidade',	'Qtde Mínima',	'Opções');
	$gravata['alinhamento'] = array('left', 'left', 'right',	    'right',		'center');
	
	$qtdColunas = count( $largura );
	
	novaTabela("[Listagem de Produtos em Estoque]", 'center', '100%', 0, 2, 1, $corFundo, $corBorda, $qtdColunas );
	
	menuOpcAdicional( $modulo, $sub, $acao, $registro, $matriz, $qtdColunas);
	
	if( $acao == 'procurar' ) {
		$condicao = "{$tb['Produtos']}.nome LIKE '%{$matriz['nome']}%'";
	}
	elseif( $acao == 'listar_emfalta' ) {
		$condicao = "{$tb['ProdutosEstoque']}.quantidade < {$tb['Produtos']}.qtdeMinima 
						AND ProdutosEstoque.idProduto = Produtos.id";
	}
	else {
		$condicao = '';
	}
	
	$produtosEstoque = dbProdutosEstoque( "", "consultaCompleta", "", $condicao, "{$tb['Produtos']}.nome" );
	$totalProdutosEstoque = count($produtosEstoque);
	
	if( $totalProdutosEstoque ){
		paginador( '', $totalProdutosEstoque, $limite['lista']['produtosEstoque'], $registro, 'normal10', $qtdColunas, '' );


			
		# Setar registro inicial
		if( !$registro ) {
			$j = 0;
		}
		elseif( $registro && is_numeric($registro) ) {
			$j = $registro;
		}
		else {
			$j = 0;
		}
		
		htmlAbreLinha($corFundo);
			for( $i = 0; $i < $qtdColunas; $i++ ){
				itemLinhaTMNOURL( $gravata['cabecalho'][$i], $gravata['alinhamento'][$i], 'middle', $largura[$i], $corFundo, 0, 'tabfundo0' );
			}
		htmlFechaLinha();
		
		$limite = $j + $limite['lista']['produtosEstoque'];
		
		while( ( $j < $totalProdutosEstoque ) && ( $j < $limite ) ) {
			
			$default = '<a href="?modulo=' . $modulo . '&sub=' . $sub . '&registro=' . $produtosEstoque[$j]->id;
			
			$opcoes = htmlMontaOpcao( $default . "&acao=alterar\">Alterar</a>", 'alterar' );
			//verifica se o produto está relacionado com algum registro na tabela produto fracionado, se tiver exibe a opção fracionar na listagem
			$Fracionado = dbProdutosFracionado( "", "consultar", "", "{$tb['ProdutosFracionado']}.idProduto = {$produtosEstoque[$j]->idProduto}", "" );
			if( $produtosEstoque[$j]->quantidade > 0 && $produtosEstoque[$j]->fracionavel == 'N' && $Fracionado ) {
				$matriz['idPop'] = $produtosEstoque[$j]->idPop;
				$opcoes .= htmlMontaOpcao( $default . "&acao=novo_fracionar&matriz[idPop]={$matriz['idPop']}\">Fracionar</a>", 'modulo' );
			}
			$i = 0;
			htmlAbreLinha( $corFundo );
				itemLinhaTMNOURL( $produtosEstoque[$j]->nome ,	$gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( $produtosEstoque[$j]->pop ,	$gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				$quantidade = produtosEstoqueGetQuantidade( $produtosEstoque[$j]->quantidade, $produtosEstoque[$j]->qtdeMinima, $produtosEstoque[$j]->unidade );
				itemLinhaTMNOURL( $quantidade , $gravata['alinhamento'][$i], 
								  'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( formatarValoresForm($produtosEstoque[$j]->qtdeMinima),	$gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( $opcoes , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
			htmlFechaLinha();
			$j++;
		}
	}
	else {
		htmlAbreLinha($corFundo);
		($acao == "listar_emfalta" ? 
			$msg = '<span class="txtaviso"><i>Nenhum produto abaixo do mínimo!</i></span>' : 
			$msg = '<span class="txtaviso"><i>Nenhum produto encontrado no estoque!</i></span>') ;
			itemLinhaTMNOURL( $msg, 'center', 'middle', $largura[$i], $corFundo, $qtdColunas, 'normal10' );
		htmlFechaLinha();
	}
	fechaTabela();
}

/**
 * Realiza a busca de produtos em estoque
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function produtosEstoqueProcurar( $modulo, $sub, $acao, $registro, $matriz ) {
	if( !$matriz['bntProcurar'] ){
		$matriz['nome']='';
	}
	getFormProcurar( $modulo, $sub, $acao, $matriz, "Produtos em Estoque" );
	
	if( $matriz['nome'] && $matriz['bntProcurar'] ){
		produtosEstoqueListar( $modulo, $sub, $acao, $registro, $matriz );
	}
}

/**
 * Exibe o formulário de inclusão e alteração de produto de estoque
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function produtosEstoqueFormulario( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda, $html, $tb;
	
	novaTabela2( '['.( $acao == 'adicionar' ? 'Adicionar' : 'Alterar' ).' Estoque de Produto]<a name="ancora"></a>', 'center', '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );
			getCampo('', '', '', '&nbsp;');
			#primeiro filtra o produto
			if ( !$matriz['idProduto'] && $acao == 'adicionar' ){
				$condicao = " AND status = 'A'";	
				procurarProdutosSelect( $modulo, $sub, $acao, $registro, $matriz, $condicao );
			}
			else {
				$i = 7; // indice de formulario
				$extraItem 		= array( 'matriz[idProduto]', 'matriz[id]' );
				$extraConteudo	= array( $matriz['idProduto'], $matriz['id'] );
				getCamposOcultos( $extraItem, $extraConteudo );
				$produtos = dbProdutos( "", "consultar", "", "{$tb['Produtos']}.id='{$matriz['idProduto']}'", '' );
				$matriz['nomeProduto'] = $produtos[0]->nome;
				getCampo( 'combo', 'Produto', '', $matriz['nomeProduto'] );
				getCampo( 'combo', _("POP"), '', formSelectPOP( $matriz['idPop'], "idPop", 'form' ) );
				getCampo( 'text',  _('Quantidade'),		'matriz[quantidade]', $matriz['quantidade'], 
						' onblur="verificarValor(0,this.value);formataValor(this.value,'.$i++.')"', '', 13 ) ;
				getBotao( 'matriz[bntConfirmar]', 			'Confirmar' );
		fechaFormulario();
			}
	fechaTabela();
}

/**
 * Verifica se os dados foram preenchidos corretamente
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 * @return boolean
 */
function produtosEstoqueValida( $matriz, $acao ) {
	$retorno = true;
	//verifica se este produto já não existe neste mesmo pop
	$consulta = dbProdutosEstoque( '', 'consultar', '', "idProduto='". intval( $matriz['idProduto'] )."' AND idPop='". intval( $matriz['idPop'] )."' AND id <>'". intval( $matriz['id'] )."'" );
	$retorno = verificaRegistroDuplicado( $consulta, $acao );
	$matriz['quantidade'] = formatarValores( $matriz['quantidade'] );
	if ( !is_numeric( $matriz['quantidade'] ) ) {
		$retorno = false;
	}
	return $retorno;
}

/**
 * Formata a quantidade de estoque de um produto de acordo com a sua quantidade mínima
 *
 * @param decimal $quantidade
 * @param integer $qtdeMinima
 * @param integer $unidade
 * @return string
 */
function produtosEstoqueGetQuantidade( $quantidade, $qtdeMinima, $unidade='' ){
	if( $quantidade < $qtdeMinima ){
		$classe = 'txtaviso';
	}
	elseif( $quantidade == $qtdeMinima ){
		$classe = 'txttrial';
	}
	else {
		$classe = 'txtok';
	}
	return '<span class="' . $classe . '">' . formatarValoresForm( $quantidade ) . ' '. $unidade . '</span>';
}

/**
 * Monta tabela com relação de produtos com quantidade em estoque insuficiente.
 *
 * @param array $produtos
 */
function produtosEstoqueInsuficiente( $produtos, $idPop ) {
	global $corFundo, $corBorda, $tb;
	
	$largura 				= array('50%',		'25%', 			'25%'				);
	$gravata['cabecalho']   = array('Produto',	'Qtde Estoque', 'Qtde Requisição'	);
	$gravata['alinhamento'] = array('left',		'right',			'right'			);	
	
	$qtdColunas = count( $largura );
	
	
	novaTabela( "Itens com quantidade insuficiente em Estoque", 'center', '70%', 0, 2, 1, $corFundo, $corBorda, $qtdColunas );
		htmlAbreLinha($corFundo);
			for( $i = 0; $i < $qtdColunas; $i++ ){
				itemLinhaTMNOURL( $gravata['cabecalho'][$i], $gravata['alinhamento'][$i], 'middle', $largura[$i], $corFundo, 0, 'tabfundo0' );
			}
		htmlFechaLinha();
		$condicao = array( "{$tb['ProdutosEstoque']}.idProduto IN (". implode( ',' , $produtos['idProduto'] ) . ")", 
							"{$tb['ProdutosEstoque']}.idPop=".$idPop );
		$produtosEstoque = dbProdutosEstoque( "", "consultaCompleta", "", $condicao, "{$tb['Produtos']}.nome" );

		foreach ( $produtosEstoque as $j => $item ) {
			$i = 0;
			$z = array_search( $item->idProduto, $produtos['idProduto']);
			$qtdeRequerida = $produtos['qtdeRequirida'][$z];
			htmlAbreLinha( $corFundo );
				itemLinhaTMNOURL( $item->nome ,	$gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				$qtdeEstoque = produtosEstoqueGetQuantidade( $item->quantidade, $produtosEstoque[$j]->qtdeMinima, $produtosEstoque[$j]->unidade );
				itemLinhaTMNOURL( $qtdeEstoque , $gravata['alinhamento'][$i], 
								  'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( formatarValoresForm( $qtdeRequerida) ,	$gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
			htmlFechaLinha();
		}
	fechaTabela();
}

/**
 * Verifica quais os produtos em estoque que estão cadastrados e com quantidade insuficiente
 *
 * @param array $produtosEstoque
 * @param array $itens
 * @return array
 */
function produtosEstoqueGetInsuficientes( $produtosEstoque, $itens ) {
	$totalItens = count( $produtosEstoque );
	$produtos = array( 'idProduto' => array(), 'qtdeRequirida' => array() );
	
	for ( $i = 0; $i < $totalItens; $i++ ){
		if ( $produtosEstoque[$i]->quantidade < $itens['quantidade'][$i] ) {
			//exibe a quantidade de estoque insuficiente
			$produtos['idProduto'][] = $produtosEstoque[$i]->idProduto;
			$produtos['qtdeRequirida'][] = $itens['quantidade'][$i];					
		}
	}
	return $produtos;
}

/**
 * Verifica parametro que permite negativar estoque
 * ou seja, realizar a baixa de ordem de serviços ou requisições mesmo que o estoque
 * seja infuciente
 */
function liberaEstoqueNegativo() {
	$retorno = false;
	
	$parametro = carregaParametrosConfig();
	$verifica = $parametro['estoque_negativo'];
	if ( $verifica == 'S' ) {
		$retorno = true;	
	}
	return $retorno;
}

/**
 * Função que exibe listagem de produtos para fracionamento de acordo com um
 *determinado pop selecionado
 * 
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
 function produtosEstoqueFormFracionar( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda, $html, $tb;
	
	$largura 				= array('5%',		'45%',		'10%', 		'20%',			'20%'		 );
	$gravata['cabecalho']   = array('', 		'Produto', 	'Unidade',	'Qtde Estoque',	'Qtde Fracionar');
	$gravata['alinhamento'] = array('center',	'left', 	'center',	'center',		'center');
	$qtdColunas = count( $largura );
	
	$css_ = 'tabfundo1';
	novaTabela2( '[Fracionamento de Produtos]<a name="ancora"></a>', 'center', '100%', 0, 2, 1, $corFundo, $corBorda, 5);
		abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );
			#primeiro filtra o pop
			if( !$matriz['idPop'] ) {
				getCampo('', '', '', '&nbsp;');
				getCampo( 'combo', 'POP', '', formSelectPOP( $matriz['idPop'], "idPop", 'form','<option value=0> Selecione um Pop: </option>','onchange="form.submit();"' ) );
			}
			else {
				( $registro ? 
				  $condicao = "{$tb['ProdutosEstoque']}.id  = {$registro}" :
				  $condicao = "{$tb['ProdutosEstoque']}.idPop  = {$matriz['idPop']} 
							AND {$tb['ProdutosEstoque']}.quantidade > '0'
							AND {$tb['ProdutosFracionado']}.idProduto = {$tb['ProdutosEstoque']}.idProduto");
				$consulta = dbProdutosEstoque( "", "consultaEstoque", "", $condicao, "{$tb['Produtos']}.nome");
				$totalProdutosEstoque = count($consulta);
				if( $totalProdutosEstoque ) {
					htmlAbreLinha($corFundo);
						itemLinhaTMNOURL( $consulta[0]->pop, 'center', 'middle', '100%', $corFundo, 5, 'tabfundo0' );
					htmlFechaLinha();
					htmlAbreLinha($corFundo);
						for( $i = 0; $i < $qtdColunas; $i++ ){
							itemLinhaTMNOURL( '<b>'.$gravata['cabecalho'][$i].'</b>', $gravata['alinhamento'][$i], 'middle', $largura[$i], $corFundo, 0, 'tabfundo2' );
						}
					htmlFechaLinha();
					$j = 0;
					while( $j < $totalProdutosEstoque ) {
						htmlAbreLinha( $corFundo );
							$id =  $consulta[$j]->id;
							itemLinhaForm( getInput( 'checkbox', "matriz[id".$j."]", $id, "onclick=\"checaproduto(this.id);\"",5, 'textbox',false,$j), 'center', 'middle', $corFundo, 0, $css_ );
							itemLinhaForm( getInput('text', 'matriz[nome]',$consulta[$j]->nome,'', 40, 'textbox', true), 'left', 'middle', $corFundo, 0, $css_ );
							itemLinhaForm( getInput('text', 'matriz[unidade]',$consulta[$j]->unidade, '', 10, 'textbox', true), 'center', 'middle', $corFundo, 0, $css_ );
							itemLinhaForm( getInput('text', "matriz[$j][quantidade]", number_format($consulta[$j]->quantidade, 2,',',''),
									'onblur="formataValor(this.value, this.name)" style="text-align: right;"', 13, 'textbox', true), 
									'center', 'middle', $corFundo, 0, $css_ );
							itemLinhaForm( getCampoNumero( "matriz[qtde".$j."]", '', 13, 'textbox', true, 
									"onblur=verificarValorPermitido(document.forms[0].elements['matriz[$j][quantidade]'].value, this.value, this.name);", "campo".$j ), 
									'center', 'middle', $corFundo, 0, $css_ );
						htmlFechaLinha();
						$j++;
					}
					htmlAbreLinha( $corFundo );
						$item = array('matriz[total]');
						$conteudo = array($totalProdutosEstoque);
						itemLinhaForm(getCamposOcultos( $item, $conteudo ), 'center', 'middle',$corFundo, 1, $css_ );
						itemLinhaForm( getSubmit( 'matriz[bntFracionar]', 'Fracionar' ), 'center', 'middle', $corFundo, 4, $css_ );
					htmlFechaLinha();
				}
				else {
					htmlAbreLinha($corFundo);
						itemLinhaTMNOURL( '<span class="txtaviso"><i>Não há produtos em estoque suficientes para o Pop selecionado.</i></span>',
										 'center', 'middle', '100%', $corFundo, 5, 'tabfundo1' );
					htmlFechaLinha();
					$matriz['idPop'] = '';
				}
		fechaFormulario();
			} 
	fechaTabela();	
}

/**
 * Realiza o fracionamento dos produtos
 *
* @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function produtosEstoqueFracionar( $modulo, $sub, $acao, $registro, $matriz ) {
	global $sessCadastro, $tb;
	
	if( $matriz['bntFracionar'] ) {
		$fracionados = produtosEstoqueVerificaFracionamento($matriz);
		if( $sessCadastro[$modulo.$sub.$acao] || $fracionados ) {
			$alterados = 0;
			for( $j=0; $j < $fracionados['registros']; $j++) {
				$estoque['quantidade'] = formatarValores( $fracionados['qtdeEstoque'][$j] ) ;
				$condicao = "{$tb['ProdutosEstoque']}.id = {$fracionados['idEstoque'][$j]}";
				if( $sessCadastro[$modulo.$sub.$acao] || dbProdutosEstoque( $estoque, 'alterar', 'entrada', $condicao, '') ) {
					$dados['quantidade'] = formatarValores( $fracionados['qtdeFracao'][$j] );
					$condicao = "{$tb['ProdutosEstoque']}.idPop  = {$fracionados['idPop']} AND {$tb['ProdutosEstoque']}.idProduto = {$fracionados['idProdutoFracionado'][$j]}";
					$consulta = dbProdutosEstoque('', 'consultar', '', $condicao, '');
					if( $consulta ) {
						$quantidade = formataValor( $consulta[0]->quantidade ) + $fracionados['qtdeFracao'][$j] ;
						$fracionado['quantidade'] = formatarValores( $quantidade );
						$condicaoFracionado = "{$tb['ProdutosEstoque']}.id = {$consulta[0]->id}";
						if( $sessCadastro[$modulo.$sub.$acao] || dbProdutosEstoque( $fracionado, 'alterar', 'entrada', $condicaoFracionado, '') ) {
							$alterados++;
						}
					}
					else {
						$dados['idProduto']	= $fracionados['idProdutoFracionado'][$j];		
						$dados['idPop']		= $fracionados['idPop'];
						if( $sessCadastro[$modulo.$sub.$acao] || dbProdutosEstoque( $dados, 'inserir' ) ) {
							$alterados++;
						}										
					}
				}
				
			}
			$sessCadastro[$modulo.$sub.$acao] = 'gravado';
			$msg = ( $fracionados['registros'] == $alterados ? 
					'Produtos em estoque fracionados com sucesso!' :
					'Não foi possível gravar os dados!');
			avisoNOURL("Aviso", $msg, 400);
			echo "<br />";
			produtosEstoqueListar( $modulo, $sub, 'listar', '', $matriz );
		}
		else {
			avisoNOURL("Aviso", "Não foi possível gravar os dados! Verifique se todos os campos foram preenchidos corretamente.", 410);
			echo "<br />";
			produtosEstoqueFormFracionar( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
	else {
		unset( $sessCadastro[$modulo.$sub.$acao] );
		produtosEstoqueFormFracionar( $modulo, $sub, $acao, $registro, $matriz );
	}
}

/**
 * Função que verifica quais produtos foram selecionados no checkbox de fracionamento
 *
 * @param array $matriz
 * @return array
 */
function produtosEstoqueVerificaFracionamento( $matriz ) {
	global $tb;
	
	$selecao = array();
	$i = 0;
	for( $j=0; $j < $matriz['total']; $j++ ) { //laço para verificar quais dos produtos listados foram selecionados
		if( $matriz['id'.$j] ) { // se o produto foi selecionado grava os dados do produto na matriz
			$selecao['idEstoque'][$i] = $matriz['id'.$j];
			$consulta = dbProdutosEstoque( '', 'consultaEstoque', '', "{$tb['ProdutosEstoque']}.id = {$selecao['idEstoque'][$i]}", '' );
			$selecao['idProdutoFracionado'][$i] = $consulta[0]->idProdutoFracionado;
			$quantidade = formataValor( $consulta[0]->quantidade );
			$qtde = formataValor( $consulta[0]->qtde );
			$matrizQtde = str_replace(",",".",$matriz['qtde'.$j]);
			$selecao['qtdeEstoque'][$i] = $quantidade - formataValor($matrizQtde);
			$selecao['qtdeFracao'][$i] = ($qtde * formataValor($matrizQtde)) / 100 ;	
			$i++;
		}
	}
	if( $selecao ) { //se existir algum produto selecionado guarda o idPop e a quantidade de produtos selecionados na matriz
		$selecao['idPop'] = $matriz['idPop'];
		$selecao['registros'] = $i;
	}
	return $selecao;	
}

function produtosEstoqueEmFalta( ){
	global $tb;
	
	$retorno = false;
	$condicao = "{$tb['ProdutosEstoque']}.quantidade < {$tb['Produtos']}.qtdeMinima 
						AND ProdutosEstoque.idProduto = Produtos.id";
	$consulta = dbProdutosEstoque("", "consultaCompleta", "", $condicao, "" );
	if( $consulta ) {
		$retorno = true;
	}
	return $retorno;
}
?>