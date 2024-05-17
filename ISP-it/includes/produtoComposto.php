<?
################################################################################
#       Criado por: Desenvolvimento
#  Data de criação: 29/09/2006
# Ultima alteração: 29/09/2006
#    Alteração No.: 000
#
# Função:
#    Painel - Funções para gerenciamento dos produtos para controle de estoque

/**
 * Construtor de módulo ProdutoComposto
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function ProdutoComposto($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $sessLogin, $html;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin['login'],'login','igual','login');
	
	if(!$permissao['admin']) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		// sistema de permissao diferente. por funcao ao inves de modulo.
		$titulo    = "<b>Produto Composto</b>";
		$subtitulo = "Cadastro de Produtos Compostos"; 
		$itens  = Array( 'Novo', 'Procurar', 'Listar' );
		getHomeModulo( $modulo, $sub, $titulo, $subtitulo, $itens );
		echo "<br>";
		
		if( $acao == 'adicionar' ) {
			produtoCompostoAdicionar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif ( $acao == 'alterar' ) {
			produtoCompostoAlterar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( $acao == 'excluir' ) {
			produtoCompostoExcluir( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( $acao == 'ver' ) {
			produtoCompostoVisualizar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( $acao == 'procurar' ) {
			produtoCompostoProcurar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( strstr( $acao, '_item' ) ) {
			switch( $acao ) {
				case "novo_item":
				case "adicionar_item":
					itemProdutoCompostoAdicionar( $modulo, $sub, $acao, $registro, $matriz );
					break;	
				case "alterar_item":
					itemProdutoCompostoAlterar( $modulo, $sub, $acao, $registro, $matriz );
					break;
				case "excluir_item":
					itemProdutoCompostoExcluir( $modulo, $sub, $acao, $registro, $matriz );
					break;
				default:
					itemProdutoCompostoListar( $modulo, $sub, 'listar', $registro, $matriz );
					break;
			}

		}
		else {
			produtoCompostoListar( $modulo, $sub, $acao, $registro, $matriz );
		}

	}
}

/**
 * Gerencia a tabela de ProdutoComposto
 *
 * @param unknown_type $matriz
 * @param unknown_type $tipo
 * @param unknown_type $subTipo
 * @param unknown_type $condicao
 * @param unknown_type $ordem
 * @return unknown
 */
function dbProdutoComposto( $matriz, $tipo, $subTipo='', $condicao='', $ordem = '' ) {
	global $conn, $tb;
		
	$bd = new BDIT();
	$bd->setConnection( $conn );
	$tabelas = $tb['ProdutoComposto'];
	$campos  = array( 	'id',		'nome',		'status' );
	$valores = array(	'NULL', 	$matriz['nome'], 	$matriz['status'] );	
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
	
	return ($retorno);
}

/**
 * Cadastra um novo ProdutoComposto
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function produtoCompostoAdicionar( $modulo, $sub, $acao, $registro, $matriz ){
	global $tb, $sessLogin, $sessCadastro;
	
	if( $matriz['bntConfirmar'] ) {
		if( $sessCadastro[$modulo.$sub.$acao] || produtoCompostoValida( $matriz, $acao ) ){
	
			if( $sessCadastro[$modulo.$sub.$acao] || dbProdutoComposto( $matriz, 'inserir' ) ){ //grava o produto na forma primária
				$sessCadastro[$modulo.$sub.$acao] = "gravado";
				avisoNOURL("Aviso", "Produto Composto cadastrado com sucesso!", 400);
				echo "<br />";
				$registro = $matriz['idProdutoComposto'] = buscaUltimoID( $tb['ProdutoComposto'] ); // busca a id desta nota
				itemProdutoCompostoAdicionar( $modulo, $sub, 'novo_item', $registro, $matriz );
			}
			else {
				avisoNOURL("Aviso", "Erro ao gravar os dados!", 400);
				echo "<br />";
				produtoCompostoFormulario( $modulo, $sub, $acao, $registro, $matriz );
			}
		}
		else {
			avisoNOURL("Aviso", "Não foi possível gravar os dados! Verifique se todos os campos foram preenchidos corretamente,
				ou se o Produto Composto já está cadastrado.", 410);
			echo "<br />";
			produtoCompostoFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
	else {
		produtoCompostoFormulario( $modulo, $sub, $acao, $registro, $matriz );
	}
}

/**
 * Altera os dados cadastrais do ProdutoComposto
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function produtoCompostoAlterar( $modulo, $sub, $acao, $registro, $matriz ){
	
	if( $matriz["bntConfirmar"] ){ 
	// se clicou no botão confirmar e os dados são validos
		if( produtoCompostoValida( $matriz, $acao ) ) {
			// grava os dados atualizados
			$gravar = dbProdutoComposto( $matriz, 'alterar', "", "id='" . $registro . "'" ); 
			if( $gravar ) { // se gravou avisa que gravou com sucesso e exibe a listagem
				avisoNOURL( "Aviso", "Produto Composto alterado com sucesso!", 400 );
				echo "<br>";
				produtoCompostoListar( $modulo, $sub, 'listar', '', $matriz );
			}
			else { // senão avisa que teve um erro e exibe o formulario de alteração
				avisoNOURL( "Erro", "Não foi possível gravar dados!", 400 );
				echo "<br>";
				produtoCompostoFormulario( $modulo, $sub, $acao, $registro, $matriz );
			}
		}
		else {
			avisoNOURL("Aviso", "Não foi possível gravar os dados! Verifique se todos os campos foram preenchidos corretamente", 400);
			echo "<br />";
			produtoCompostoFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}

	}
	else{
		// busca os dados do produto cadastrado
		$conta = dbProdutoComposto( '', "consultar","", "id='" . $registro . "'" );
		if ( count( $conta ) ){ // se encontrou transfere os dados para matriz
			$matriz["nome"]		  = $conta[0]->nome;
			$matriz["status"] = $conta[0]->status;
			$matriz["id"] = $conta[0]->id;
			produtoCompostoFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}
		else{
			avisoNOURL("Erro", "Não foi possível localizar o Produto Composto!", 400);
			echo "<br>";
			produtoCompostoListar( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
}

/**
 * Realiza a listagem dos produtos
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function produtoCompostoListar( $modulo, $sub, $acao, $registro, $matriz ){
	global $corFundo, $corBorda, $html, $sessLogin, $limite, $tb;
	
	$largura 				= array('40%',	'15%',		'45%' );
	$gravata['cabecalho']   = array('Nome', 'Status',	'Opções');
	$gravata['alinhamento'] = array('left', 'center',	'left');
	
	$qtdColunas = count( $largura );
	
	novaTabela("[Listagem de Produtos Compostos]", 'center', '100%', 0, 2, 1, $corFundo, $corBorda, $qtdColunas );
	
	menuOpcAdicional( $modulo, $sub, $acao, $registro, $matriz, $qtdColunas);
	
	if( $acao == 'listar_ativos' ) {
		$condicao = "{$tb['ProdutoComposto']}.status='A' ";
	}
	elseif( $acao == 'listar_inativos' ) {
		$condicao = "{$tb['ProdutoComposto']}.status='I'";
	}
	elseif( $acao == 'procurar' ) {
		$condicao = "{$tb['ProdutoComposto']}.nome LIKE '%{$matriz['nome']}%'";
	}
	else {
		$condicao = '';
	}
	$produtos = dbProdutoComposto( "", "consultar", "", $condicao, "nome, id DESC" );
	$totalProdutos = count($produtos);
	if( $totalProdutos ){
		paginador( '', $totalProdutos, $limite['lista']['produtoComposto'], $registro, 'normal10', $qtdColunas, '' );

		htmlAbreLinha($corFundo);
			for( $i = 0; $i < $qtdColunas; $i++ ){
				itemLinhaTMNOURL( $gravata['cabecalho'][$i], $gravata['alinhamento'][$i], 'middle', $largura[$i], $corFundo, 0, 'tabfundo0' );
			}
		htmlFechaLinha();
		
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

		$limite = $j + $limite['lista']['produtoComposto'];
		
		while( ( $j < $totalProdutos ) && ( $j < $limite ) ) {
			
			$default = '<a href="?modulo=' . $modulo . '&sub=' . $sub . '&registro=' . $produtos[$j]->id;
			
			$opcoes  = htmlMontaOpcao( $default . "&acao=ver\">Ver</a>", 'ver' );
			$opcoes .= htmlMontaOpcao( $default . "&acao=alterar\">Alterar</a>", 'alterar' );
			$opcoes .= htmlMontaOpcao( $default . "&acao=excluir\">Excluir</a>", 'excluir' );
			$opcoes .= htmlMontaOpcao( $default . "&acao=novo_item\">Adicionar Item</a>", 'incluir' );
			$i = 0;
			htmlAbreLinha( $corFundo );
				itemLinhaTMNOURL( $produtos[$j]->nome , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( formSelectStatusAtivoInativo( $produtos[$j]->status, "status", "check" ) , $gravata['alinhamento'][$i], 
								  'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( $opcoes , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
			htmlFechaLinha();
			$j++;
		}
	}
	else {
		htmlAbreLinha($corFundo);
			itemLinhaTMNOURL( '<span class="txtaviso"><i>Nenhum Produto Composto encontrado!</i></span>', 'center', 'middle', $largura[$i], $corFundo, $qtdColunas, 'normal10' );
		htmlFechaLinha();
	}
	
	fechaTabela();
}

/**
 * Formulário de cadastro de ProdutoComposto
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function produtoCompostoFormulario( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda, $html;

	$i = 7; // indice de formulario
	novaTabela2("[".( $acao == "adicionar" ? "Adicionar" : "Alterar").' Produto Composto]<a name="ancora"></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		if ( $acao =='alterar' ) {
			menuOpcAdicional( $modulo, $sub, 'ver', $registro, $matriz, 2 );
		}
		else {
			getCampo('', '', '', '&nbsp;');
		}
		abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );
		$extraItem 		= array( 'matriz[id]' );
		$extraConteudo	= array( $matriz['id'] );
		getCamposOcultos( $extraItem, $extraConteudo );
		getCampo('text', _('Nome'), 'matriz[nome]', $matriz['nome'],'','',20 );
		getCampo( 'combo', _("Status"), "", 		formSelectStatusAtivoInativo($matriz['status'], "status", "form") );
		getBotao( 'matriz[bntConfirmar]', 			'Confirmar');
		
		fechaFormulario();
	fechaTabela();
		
}

/**
 * Valida se os dados de cadastro são válidos
 *
 * @param array $matriz
 * @return boolean
 */
function produtoCompostoValida( $matriz, $acao ) {
	global $tb;

	$retorno = true;
	if ( empty( $matriz['nome'] ) ) {
		$retorno = false;
	}
	else {
		$condicao = "{$tb['ProdutoComposto']}.nome = '{$matriz['nome']}' AND id <>'". intval( $matriz['id'] )."'";	
		$consulta = dbProdutoComposto( "", "consultar", "", $condicao, '' );
		$retorno = verificaRegistroDuplicado( $consulta, $acao );
	}
	return $retorno;
}

/**
 * Realiza a filtragem de ProdutoComposto
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function produtoCompostoProcurar( $modulo, $sub, $acao, $registro, $matriz ){
	
	if( !$matriz['bntProcurar'] ){
		$matriz['nome']='';
	}
	getFormProcurar( $modulo, $sub, $acao, $matriz, "Produto Composto" );
	
	if( $matriz['nome'] && $matriz['bntProcurar'] ){
		produtoCompostoListar( $modulo, $sub, $acao, $registro, $matriz );
	}
}

/**
 * Exibe os dados de um ProdutoComposto
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function produtoCompostoVer( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda, $html, $tb;
	
	$produtos = dbProdutoComposto( '', "consultar", '', "{$tb['ProdutoComposto']}.id='" . $registro . "'" );
	if( count( $produtos ) == 1 ) {
		$matriz["nome"]		  = $produtos[0]->nome;
		$matriz["status"] = $conta[0]->status;
		novaTabela2("[" . ( $acao  == "excluir" ? "Excluir" : "Vizualizar" ) . " Produto Composto]" . "<a name='ancora'></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			menuOpcAdicional( $modulo, $sub, 'ver', $registro, $matriz, 2 );
			getCampo( 'combo', _('Nome'), '', 	$produtos[0]->nome );
			getCampo( 'combo', _("Status"), "", formSelectStatusAtivoInativo( $produtos[0]->status, "status", "check") );
			if ( $acao == 'excluir' ){
				getBotao('matriz[bntExcluir]', 'Excluir', 'submit','button', 'onclick="window.location=\'?modulo='.$modulo.
					     '&sub='.$sub.'&acao='.$acao.'&registro='.$registro.'&matriz[id]='.$matriz['id'].
					     '&matriz[bntExcluir]=excluir\'"');
			}
			else{
				getCampo('', '', '', '&nbsp;');	
			}
		fechaTabela();		
		//produtoCompostoVisualizar( $modulo, $sub, 'listar', $registro, $matriz );
	}
	else {
		avisoNOURL("Erro", "Não foi possível localizar o Produto Composto!", 400);
		echo "<br />";
		produtoCompostoListar( $modulo, $sub, 'listar', '', $matriz );
	}
	
}

/**
 * Exibe os dados do Produto composto junto aos seus itens.
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function produtoCompostoVisualizar( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda, $html, $tb;

	produtoCompostoVer( $modulo, $sub, $acao, $registro, $matriz );
	itemProdutoCompostoListagem( $modulo, $sub, 'listar', $registro, $matriz );
}

/**
 * Exclui registro de Produto Composto
 *
 * @param string $modulo
 * @param string $sub
 * @param string $acao
 * @param integer $registro
 * @param array $matriz
 */
function produtoCompostoExcluir ( $modulo, $sub, $acao, $registro, $matriz  ){
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $tb, $sessCadastro;
	
	if( $matriz["bntExcluir"] ){ 
	// se clicou no botão excluir
	// excluir o produtocomposto e seus itens
		$exclusao_itens = dbItensProdutoComposto( $matriz, 'excluir', "", "idProdutoComposto='" . $registro . "'" );
		$exclusao_produtos = dbProdutoComposto( $matriz, 'excluir', "", "id='" . $registro . "'" ); 
		if( $exclusao_produtos && $exclusao_itens) { // se gravou avisa que gravou com sucesso e exibe a listagem
			avisoNOURL( "Aviso", "Produto Composto excluído com sucesso!", 400 );
			echo "<br>";
			$sessCadastro = '';
			produtoCompostoListar( $modulo, $sub, 'listar', '', array() );
		}
		else { // senão avisa que teve um erro e exibe o formulario de exclusão
			avisoNOURL( "Erro", "Não foi possível excluir dados!", 400 );
			echo "<br>";
			produtoCompostoVer( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
	else{
	// busca os dados do produto cadastrado
		$conta = dbProdutoComposto( '', "consultar","", "id='" . $registro . "'" );
		if ( count( $conta ) ){ // se encontrou transfere os dados para matriz
			$matriz["nome"]		= $conta[0]->nome;
			$matriz["status"] 	= $conta[0]->status;
			produtoCompostoVer( $modulo, $sub, $acao, $registro, $matriz );
		}
		else{
			avisoNOURL("Erro", "Não foi possível localizar o Produto Composto!", 400);
			echo "<br>";
			produtoCompostoListar( $modulo, $sub, 'listar', '', $matriz );
		}
	}

}

/**
 * Exibe formulário para preenchimento de Requisição/Retorno de Produto Composto
 *
 * @param string $modulo
 * @param string $sub
 * @param string $acao
 * @param integer $registro
 * @param array $matriz
 */
function produtoCompostoGetFormulario( $modulo, $sub, $acao, $registro, $matriz ){
	global $corFundo, $tb, $corBorda;

	$largura 				= array( '45%',		'25',			'30%'  	 );
	$gravata['cabecalho']   = array( 'Produto Composto', 'Quantidade',	'Opções' );
	$alinhamento		 	= array( 'left', 	'center', 		'center' );
	
	$qtdColunas = count( $largura );
	
	novaTabela2( '[Adicionar Produto Composto]<a name="ancora"></a>', center, "100%", 0, 2, 1, $corFundo, $corBorda, $qtdColunas );
	htmlAbreLinha($corFundo);
		for( $i = 0; $i < $qtdColunas; $i++ ){
			itemLinhaTMNOURL( $gravata['cabecalho'][$i], $alinhamento[$i], 'middle', $largura[$i], $corFundo, 0, 'tabfundo0' );
		}
	
	$css_ = 'tabfundo1';
	$limite = 20;
	htmlAbreLinha( $corFundo );
		abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );
			$ocultosNomes = array( 'matriz[idRequisicao]', 'matriz[idMovimentoEstoque]', 'matriz[idOrdemServico]', 'matriz[id]' );
			$ocultosValores = array( $matriz['idRequisicao'], $matriz['idMovimentoEstoque'], $matriz['idOrdemServico'], $matriz['id']  );
			getCamposOcultos( $ocultosNomes, $ocultosValores );
			itemLinhaForm( produtoCompostoGetComboEstreito( $matriz['idProdutoComposto'], 60 ), 'left', 'middle', $corFundo, 0, $css_ );
			itemLinhaForm( 
				getCampoNumero( 'matriz[qtdePC]', $matriz['qtdePC'], 7, 'textbox', false ), 'center', 
								'middle', $corFundo, 0, $css_ );
			$botao = getSubmit( 'matriz[bntConfirmarPC]', 'Incluir' );
	
			itemLinhaForm( $botao, 'center', 'middle', $corFundo, 2, $css_ );
			
		fechaFormulario();
	htmlFechaLinha();
}

/**
 * Retorno o Combo de produtos c/ a largura limitada de acordo com o $limite especificado.
 *
 * @param integer $idProduto
 * @param integer $limite
 * @param string $js
 * @return string
 */
function produtoCompostoGetComboEstreito( $idProduto, $limite = 20, $js = '"') {
	$consulta = dbProdutoComposto( '', 'consultar', '', "status='A'");
	foreach( $consulta as $i => $linha ){
		$consulta[$i]->nome = ( strlen( $linha->nome ) > $limite ? substr( $linha->nome, 0, $limite ).'...' : $linha->nome );
	}
	return getSelectObjetos( 'matriz[idProdutoComposto]', $consulta, 'nome', 'id', $idProdutoComposto, $js, true );
}

/**
 * Verifica e insere itens de Produto Composto da Requisição/Retorno ou Ordem de Serviço 
 * em Itens Movimento de Estoque
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function getProdutoComposto( $modulo, $sub, $acao, $registro, $matriz ){
	global $tb, $sessCadastro;

	if( $matriz['bntConfirmarPC'] ) {
		if( $acao == 'inserir_pc' || produtoCompostoRequisicaoValida( $matriz, $acao ) ) {
			// verifica as ações para gravar no BD e mostrar a mensagem corretamente
			$subAcao = explode( "_", $acao );
			switch( $subAcao[0] ) {
				default:
					$tipo = 'inserir';
					$msg  = 'cadastrados';
					$condicao = "";
					break;
			}

			$condicao = "{$tb['ItensProdutoComposto']}.idProdutoComposto=".$matriz['idProdutoComposto'];
			//consulta os itens do produto composto selecionado
			$itensProdutoComposto = dbItensProdutoComposto( $matriz, 'consultarNomeProduto', '', $condicao, '' );
			$totalItens = count($itensProdutoComposto);
			$gravados = 0; //quantidade de registros atualizados
			foreach ( $itensProdutoComposto as $itemPC ) {
				$matriz['idProduto']		= $itemPC->idProduto;
				$matriz['quantidade']	= $itemPC->quantidade * $matriz['qtdePC'];
				//verifica se este item já está cadastrado no movimento de estoque especificado
				$item = itensMovimentoEstoqueGet( $matriz, $acao );
				
				if( count( $item ) ) { 
					//se encontrou o item cadastrado apenas altera a quantidade
					$id = $item[0]->id;
					$matriz['quantidade'] = $matriz['quantidade'] + $item[0]->quantidade; 
					if( dbItensMovimentoEstoque( $matriz, 'alterar', "", "id='" . $id . "'" ) ) {
						$gravados++;
					}
				}
				else {
					//se não encontrou, cadastra um novo item
					if( dbItensMovimentoEstoque( $matriz, $tipo, '', $condicao ) ) {
						$gravados++;
					}
				}
			}
			if( $totalItens == $gravados ) {
				avisoNOURL( 'Aviso', 'Itens ' . $msg . ' com sucesso!', 400 );
				echo "<br />";	
			}
			else {
				avisoNOURL( 'Aviso', 'Houve erro durante a gravação de alguns itens do Produto Composto!', 400 );
				echo "<br />";	
			}
			$matriz['idProdutoComposto'] = ''; 
			$acao = 'cadastrar_itens';
		}
		else {
			avisoNOURL("Aviso", "Não foi possível gravar os dados! Verifique se todos os campos do Produto Composto foram preenchidos corretamente.", 400);
			echo "<br />";			
		}
	}
}

/**
 * Valida se os dados de cadastro são válidos
 *
 * @param array $matriz
 * @param string $acao
 * @return boolean
 */
function produtoCompostoRequisicaoValida( $matriz, $acao ){
	$retorno = true;

	if ( !$matriz['idProdutoComposto'] || empty($matriz['qtdePC']) || !is_numeric( formatarValores( $matriz['qtdePC'] ) ) || $matriz['qtdePC'] <= 0 ) { 
		$retorno = false;
	}
	return $retorno;
}
?>