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
 * Construtor de módulo Produtos
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function Produtos($modulo, $sub, $acao, $registro, $matriz) {

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
		$titulo    = "<b>Produtos</b>";
		$subtitulo = "Cadastro de Produtos para Controle de Estoque"; 
		$itens  = Array( 'Novo', 'Procurar', 'Listar' );
		getHomeModulo( $modulo, $sub, $titulo, $subtitulo, $itens );
		echo "<br>";
		
		if($acao == "adicionar") {
				produtosAdicionar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( $acao == "alterar" )
				produtosAlterar( $modulo, $sub, $acao, $registro, $matriz );
		elseif( strstr( $acao, '_item' ) ) {
			switch( $acao ) {
				case "adicionar_item":
					ProdutosFracionadoAdicionar( $modulo, $sub, $acao, $registro, $matriz );
					break;
				case "novo_item":
				case "fracionar_item":
					produtosFracionar( $modulo, $sub, 'fracionar_item', $registro, $matriz );
				break;
				case "alterar_item":
					ProdutosFracionadoAlterar( $modulo, $sub, $acao, $registro, $matriz );
					break;
				case "excluir_item":
					ProdutosFracionadoExcluir( $modulo, $sub, $acao, $registro, $matriz );
					break;
				default:
					ProdutosFracionadoListar( $modulo, $sub, 'listar', $registro, $matriz );
					break;
			}
		}
		elseif( $acao == "ver" ) {
				produtosVisualizar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( $acao == "procurar" ) {
				produtosProcurar( $modulo, $sub, $acao, $registro, $matriz );
		}
		else {
				produtosListar( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
}

/**
 * Gerencia a tabela de Produtos
 *
 * @param unknown_type $matriz
 * @param unknown_type $tipo
 * @param unknown_type $subTipo
 * @param unknown_type $condicao
 * @param unknown_type $ordem
 * @return unknown
 */
function dbProdutos( $matriz, $tipo, $subTipo='', $condicao='', $ordem = '' ) {
	global $conn, $tb;
		
	$bd = new BDIT();
	$bd->setConnection( $conn );
	$tabelas = $tb['Produtos'];
	$campos  = array( 'id',		'nome',			 'idUnidade', 			'marca', 			'modelo', 
		'qtdeMinima',			'valorBase', 			'valorVenda', 			'status', 		   'fracionavel' );
	$valores = array( 'NULL',	$matriz['nome'], $matriz['idUnidade'],	$matriz['marca'], 	$matriz['modelo'],
		$matriz['qtdeMinima'],	$matriz['valorBase'],	$matriz['valorVenda'],	$matriz['status'], $matriz['fracionavel'] );	
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
	
	if( $tipo == 'consultarComUn' ){
		$tabelas = "{$tb['Produtos']} LEFT JOIN {$tb['Unidades']} ON ({$tb['Produtos']}.idUnidade =  {$tb['Unidades']}.id )";
		$campos  = array( $tb['Produtos'].".*", "{$tb['Unidades']}.unidade, {$tb['Unidades']}.descricao as unidadeNome");
		$retorno = $bd->seleciona( $tabelas, $campos, $condicao, '', $ordem );
	}
	
	return ($retorno);
}

/**
 * Cadastra um novo produto
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function produtosAdicionar( $modulo, $sub, $acao, $registro, $matriz ){
	global $tb, $sessLogin, $sessCadastro;
	
	if( $matriz['bntConfirmar'] ) {
		if( $sessCadastro[$modulo.$sub.$acao] || produtosValida( $matriz ) ){
			
			$matriz['valorBase']		= formatarValores( $matriz['valorBase'] );
			$matriz['valorVenda']		= formatarValores( $matriz['valorVenda'] );
	
			if( $sessCadastro[$modulo.$sub.$acao] || dbProdutos( $matriz, 'inserir' ) ){ //grava o produto na forma primária
				$sessCadastro[$modulo.$sub.$acao] = "gravado";
				avisoNOURL("Aviso", "Produto cadastrado com sucesso!", 400);
				echo "<br />";
				if( $matriz['fracionavel'] == 'N') {
					
					produtosListar($modulo, $sub, 'listar', '', $matriz);
				}
				else { 
					$registro = $matriz['idProdutoFracionado'] = buscaUltimoID( $tb['Produtos'] ); // busca a id desta nota
					ProdutosFracionadoAdicionar( $modulo, $sub, 'novo_item', $registro, $matriz );
				}
			}
			else {
				avisoNOURL("Aviso", "Erro ao gravar os dados!", 400);
				echo "<br />";
				produtosFormulario( $modulo, $sub, $acao, $registro, $matriz );
			}
		}
		else {
			avisoNOURL("Aviso", "Não foi possível gravar os dados! Verifique se todos os campos foram preenchidos corretamente", 400);
			echo "<br />";
			produtosFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
	else {
		unset( $sessCadastro[$modulo.$sub.$acao] );
		produtosFormulario( $modulo, $sub, $acao, $registro, $matriz );
	}
}

/**
 * Altera os dados cadastrais dos produtos
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function produtosAlterar( $modulo, $sub, $acao, $registro, $matriz ){
	
	if( $matriz["bntConfirmar"] ) {
		if( produtosValida( $matriz ) ){ 
			// se clicou no botão confirmar e os dados são validos
			// grava os dados atualizados
			$matriz['valorBase']		= formatarValores( $matriz['valorBase'] );
			$matriz['valorVenda']		= formatarValores( $matriz['valorVenda'] );
			
			$gravar = dbProdutos( $matriz, 'alterar', "", "id='" . $registro . "'" ); 
			if( $gravar ) { // se gravou avisa que gravou com sucesso e exibe a listagem
				avisoNOURL( "Aviso", "Produto alterado com sucesso!", 400 );
				echo "<br>";
				produtosListar( $modulo, $sub, 'listar', '', $matriz );
			}
			else { // senão avisa que teve um erro e exibe o formulario de alteração
				avisoNOURL( "Erro", "Não foi possível gravar dados!", 400 );
				echo "<br>";
				produtosFormulario( $modulo, $sub, $acao, $registro, $matriz );
			}
		}
		else {
			avisoNOURL("Aviso", "Não foi possível gravar os dados! Verifique se todos os campos foram preenchidos corretamente", 400);
			echo "<br />";
			produtosFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
	else{
		// busca os dados do produto cadastrado
		$conta = dbProdutos( '', "consultar","", "id='" . $registro . "'" );
		if ( count( $conta ) ){ // se encontrou transfere os dados para matriz
			$matriz["nome"]		  = $conta[0]->nome;
			$matriz["idUnidade"]  = $conta[0]->idUnidade;
			$matriz["marca"]  	  = $conta[0]->marca;
			$matriz["modelo"]  	  = $conta[0]->modelo;
			$matriz["qtdeMinima"] = $conta[0]->qtdeMinima;
			$matriz["valorBase"]  = formatarValoresForm( $conta[0]->valorBase);
			$matriz["valorVenda"] = formatarValoresForm( $conta[0]->valorVenda );
			$matriz["status"]	  = $conta[0]->status;
			$matriz["fracionavel"] = $conta[0]->fracionavel;
			produtosFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}
		else{
			avisoNOURL("Erro", "Não foi possível localizar o Produto!", 400);
			echo "<br>";
			produtosListar( $modulo, $sub, $acao, $registro, $matriz );
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
function produtosListar( $modulo, $sub, $acao, $registro, $matriz ){
	global $corFundo, $corBorda, $html, $sessLogin, $limite, $tb;
	
	$largura 				= array('30%',	'9%',		'15%',		'14', 		'7%',	 		'7%',		'18%' );
	$gravata['cabecalho']   = array('Nome', 'Unidade', 	'Marca',	'Modelo', 	'Mínimo', 	'Status',	'Opções');
	$gravata['alinhamento'] = array('left', 'center',	'left',		'left', 	'right', 		'center',	'center');
	
	$qtdColunas = count( $largura );
	
	novaTabela("[Listagem de Produtos]", 'center', '100%', 0, 2, 1, $corFundo, $corBorda, $qtdColunas );
	
	menuOpcAdicional( $modulo, $sub, $acao, $registro, $matriz, $qtdColunas);
	
	if( $acao == 'listar_ativos' ) {
		$condicao = "{$tb['Produtos']}.status='A' ";
	}
	elseif( $acao == 'listar_inativos' ) {
		$condicao = "{$tb['Produtos']}.status='I'";
	}
	elseif( $acao == 'listar_fracionados' ) {
		$condicao = "{$tb['Produtos']}.fracionavel='S'";
	}
	elseif( $acao == 'listar_nfracionados' ) {
		$condicao = "{$tb['Produtos']}.fracionavel='N'";
	}
	elseif( $acao == 'procurar' ) {
		$condicao = "{$tb['Produtos']}.nome LIKE '%{$matriz['nome']}%'";
	}
	else {
		$condicao = '';
	}
	$produtos = dbProdutos( "", "consultarComUn", "", $condicao, "nome, id DESC" );
	$totalProdutos = count($produtos);
	if( $totalProdutos ){
		paginador( '', $totalProdutos, $limite['lista']['produtos'], $registro, 'normal10', $qtdColunas, '' );

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

		$limite = $j + $limite['lista']['produtos'];
		
		while( ( $j < $totalProdutos ) && ( $j < $limite ) ) {
			
			$default = '<a href="?modulo=' . $modulo . '&sub=' . $sub . '&registro=' . $produtos[$j]->id;
			
			$opcoes = htmlMontaOpcao( $default . "&acao=ver\">Ver</a>", 'ver' );
			$opcoes .= htmlMontaOpcao( $default . "&acao=alterar\">Alterar</a>", 'alterar' );
			if( $produtos[$j]->fracionavel == 'S' ) {
				$opcoes .= htmlMontaOpcao( $default . "&acao=novo_item\">Fracionar</a>", 'baixar' );	
			}
			
			$i = 0;
			htmlAbreLinha( $corFundo );
				itemLinhaTMNOURL( $produtos[$j]->nome , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( $produtos[$j]->unidadeNome , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( $produtos[$j]->marca,   $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( $produtos[$j]->modelo,   $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( $produtos[$j]->qtdeMinima, $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( formSelectStatusAtivoInativo( $produtos[$j]->status, "status", "check" ) , $gravata['alinhamento'][$i], 
								  'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( $opcoes , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
			htmlFechaLinha();
			$j++;
		}
	}
	else {
		htmlAbreLinha($corFundo);
			itemLinhaTMNOURL( '<span class="txtaviso"><i>Nenhum produto encontrado!</i></span>', 'center', 'middle', $largura[$i], $corFundo, $qtdColunas, 'normal10' );
		htmlFechaLinha();
	}
	
	fechaTabela();
}

/**
 * Formulário de cadastro de Produtos
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function produtosFormulario( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda, $html, $tb;
	
	$i = 9; // indice de formulario
	novaTabela2("[".( $acao == "adicionar" ? "Adicionar" : "Alterar").' Produto]<a name="ancora"></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		
		abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );
		getCampo('', '', '', '&nbsp;');
		getCampo('text', _('Nome'), 'matriz[nome]', $matriz['nome'],'','',20 );
		getCampo( 'combo', _("Unidade"), '', formSelectUnidades($matriz['idUnidade'], 'idUnidade', 'form', true ) );
		getCampo( 'text', _('Marca'), 		'matriz[marca]', $matriz['marca'], '', '', 20 );
		getCampo( 'text', _('Modelo'), 		'matriz[modelo]', $matriz['modelo'], '', '', 20 );
		getCampo( 'text',  _('Quantidade Mínima'),	'matriz[qtdeMinima]', $matriz['qtdeMinima'], '','', 13 );
		getCampo( 'text',  _('Valor Base'),			'matriz[valorBase]',  $matriz['valorBase'], 
					' onblur="verificarValor(0,this.value);formataValor(this.value,'.$i++.')"', '', 13 ) ;
		getCampo( 'text',  _('Valor Venda'),		'matriz[valorVenda]', $matriz['valorVenda'], 
					' onblur="verificarValor(0,this.value);formataValor(this.value,'.$i++.')"', '', 13 ) ;
		getCampo( 'combo', _("Status"), "", 		formSelectStatusAtivoInativo($matriz['status'], "status", "form") );
		getCampo( 'combo', _("Fracionável"), "", 	formSelectStatusAtivoInativo($matriz['fracionavel'], "fracionavel", "form_sim_nao") );
		getBotao( 'matriz[bntConfirmar]', 			'Confirmar');
		
		fechaFormulario();
	fechaTabela();
		
}

/**
 * Valida se os dados de cadastro são validos
 *
 * @param array $matriz
 * @return boolean
 */
function produtosValida( $matriz ) {
	$retorno = true;
	if ( empty( $matriz['nome'] ) ) {
		$retorno = false;
	}
	if( !is_numeric( $matriz['qtdeMinima'] ) ) {
		$retorno = false;
	}
	$matriz['valorBase'] = formatarValores( $matriz['valorBase'] );
	if ( empty( $matriz['valorBase']) || !is_numeric( $matriz['valorBase'] ) || $matriz['valorBase'] <= 0) {
		$retorno = false;
	}
	$matriz['valorVenda'] = formatarValores( $matriz['valorVenda'] );
	if ( empty( $matriz['valorVenda']) || ( !is_numeric( $matriz['valorVenda'] ) ) || $matriz['valorVenda'] <= 0  ) {
		$retorno = false;
	}
	
	return $retorno;
}

/**
 * Realiza a filtragem de produtos
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function produtosProcurar( $modulo, $sub, $acao, $registro, $matriz ){
	
	if( !$matriz['bntProcurar'] ){
		$matriz['nome']='';
	}
	getFormProcurar( $modulo, $sub, $acao, $matriz, "Produtos" );
	
	if( $matriz['nome'] && $matriz['bntProcurar'] ){
		produtosListar( $modulo, $sub, $acao, $registro, $matriz );
	}
}

/**
 * Exibe os dados de um Produto
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function produtosVer( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda, $html, $tb;
	
	$produtos = dbProdutos( '', "consultarComUn", '', "{$tb['Produtos']}.id='" . $registro . "'" );
	if( count( $produtos ) == 1 ) {
		novaTabela2('[Vizualizar Produto]<a name="ancora"></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);	
			$matriz['fracionavel'] = $produtos[0]->fracionavel;
			if( $matriz['fracionavel'] == 'S' ) {
				 menuOpcAdicional( $modulo, $sub, 'ver', $registro, $matriz, 2 );
			}
			else {
				getCampo('', '', '', '&nbsp;');
			}
				getCampo( 'combo', _('Código'), '', $produtos[0]->id );
				getCampo( 'combo', _('Nome'), '', $produtos[0]->nome );
				getCampo( 'combo', _('Unidade'), '', $produtos[0]->unidadeNome );
				getCampo( 'combo', _('Marca'), '', $produtos[0]->marca );
				getCampo( 'combo', _('Modelo'), '', $produtos[0]->modelo );
				getCampo( 'combo', _('Quantidade Mínima'), '', $produtos[0]->qtdeMinima );
				getCampo( 'combo', _('Valor Base'),			'',  formatarValoresForm( $produtos[0]->valorBase ) ) ;
				getCampo( 'combo',  _('Valor Venda'),		'',formatarValoresForm( $produtos[0]->valorVenda ) ) ;
				getCampo( 'combo', _("Status"), "", 		formSelectStatusAtivoInativo( $produtos[0]->status, "status", "check") );
				getCampo( 'combo', _("Fracionável"), "", 		formSelectStatusAtivoInativo( $produtos[0]->fracionavel, "fracionavel", "sim_nao") );
				getCampo('', '', '', '&nbsp;');
		fechaTabela();
	}
	else {
		avisoNOURL("Erro", "Não foi possível localizar o Produto!", 400);
		echo "<br />";
		produtosListar( $modulo, $sub, 'listar', '', $matriz );
	}
	
}

/**
 * Retorno o Combo de produtos c/ a largura limitada de acordo com o $limite especificado.
 *
 * @param integer $idProduto
 * @param integer $limite
 * @return string
 */
function produtosGetComboEstreito( $idProduto, $limite = 20, $js = 'onchange="form.submit();"' ){
	$consulta = dbProdutos( '', 'consultar', '', "status='A'");
	foreach( $consulta as $i => $linha ){
		$consulta[$i]->nome = ( strlen( $linha->nome ) > $limite ? substr( $linha->nome, 0, $limite ).'...' : $linha->nome );
	}
	return getSelectObjetos( 'matriz[idProduto]', $consulta, 'nome', 'id', $idProduto, $js, true );
}

/**
 * Função de ajax para atribuir a unidade no campo
 *
 * @param integer $idProduto
 * @return string (text/xml)
 */
function produtosSetCampoUnidade( $idProduto ) {
	global $tb;
	$produto = dbProdutos( '', 'consultarComUn', '', $tb['Produtos'].'.id='.$idProduto );
	$objResponse = new xajaxResponse();
	if( is_array( $produto) && count( $produto ) > 0 ) {

		
		$objResponse->addAssign( 'unidadeProduto', 'value', $produto[0]->unidade );
		@mysql_query('pasouuuuu '.$produto[0]->unidade);
	}
	else {
		$objResponse->addClear( 'unidadeProduto', 'value' ); 
		@mysql_query('shit');
	}
	$retorno = $objResponse->getXML();
	@mysql_query( $retorno );
	return $objResponse;
}

/**
 * Função usada para retorna um select de produtos de acordo com consulta de nome ou código do produto
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function procurarProdutosSelect( $modulo, $sub, $acao, $registro, $matriz, $condicao ) {
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessCadastro, $titulo, $tb;
	
	if( ( ($acao=='procurar') || substr($acao, 0, 9 ) == 'adicionar' || $acao == 'novo_item') && (!$matriz['bntSelecionar']) ) {
		
		novaLinhaTabela( $corFundo, '100%' );
				itemLinhaTMNOURL('<b class=bold10>Busca por Produtos:</b><br><span class=normal10>Informe o código ou nome do Produto</span>', 'right', 'middle', '35%', $corFundo, 0, 'tabfundo1');
				$texto = "<input type=text name=matriz[txtProcurar] size=50 value='$matriz[txtProcurar]'>&nbsp;<input type=submit value=Procurar name=matriz[bntProcurar] class=submit>";
				itemLinhaForm( $texto, 'left', 'middle', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		if( $matriz['txtProcurar'] || $matriz['idProduto'] ) {
			# Procurar Produto
			if( !is_numeric( $matriz['txtProcurar'] ) ) { 
				$consulta = buscaRegistros( "upper(nome) like '%$matriz[txtProcurar]%' $condicao","$tb[Produtos].nome", 'custom', 'nome', $tb['Produtos'] );
			}
			else {
				$consulta = buscaRegistros( "id = $matriz[txtProcurar] $condicao", "$tb[Produtos].id", 'custom','id', $tb['Produtos'] );
			}	
			if( $consulta && contaConsulta( $consulta ) > 0 ) {
				# Selecionar cliente
				novaLinhaTabela( $corFundo, '100%' );
					itemLinhaTMNOURL('<b class=bold10>Produtos encontrados:</b><br>
					<span class=normal10>Selecione:</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					$texto = formSelectConsulta( $consulta, 'nome', 'id', 'idProduto', $matriz['idProduto']);
					if( $exibeBotoes != '0' ) $texto .= "&nbsp;<input type=\"submit\" name=\"matriz[bntSelecionar]\" value=\"Selecionar\" class=\"submit\" />";
					itemLinhaForm($texto, 'left', 'middle', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			}
			elseif( contaConsulta( $consulta ) <= 0 ){
				$texto = "Nenhum produto encontrado!";
				novaLinhaTabela( $corFundo, '100%');
				itemLinhaTMNOURL( $texto, 'center','middle','10%', $corFundo, '2', 'tabfundo1' );
				fechaLinhaTabela();
			}
		}
	}
}

/**
 * Exibe os dados do Produto e do seu fracionamento
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function produtosVisualizar( $modulo, $sub, $acao, $registro, $matriz ) {
	global $tb;
	
	produtosVer( $modulo, $sub, $acao, $registro, $matriz );
	$fracionado = dbProdutosFracionado( '', "consultarNomeProduto", '', "{$tb['ProdutosFracionado']}.idProdutoFracionado='" . $registro . "'" );
	if( count( $fracionado ) > 0 ) {
		ProdutosFracionadoListagem( $modulo, $sub, 'listar', $registro, $matriz );
	}
}

/**
 * Visualiza os dados do produto e seus itens
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function produtosFracionar( $modulo, $sub, $acao, $registro, $matriz ) {
	produtosVer( $modulo, $sub, 'ver', $registro, $matriz );
	ProdutosFracionadoListar( $modulo, $sub, 'listar', $registro, $matriz );
}

?>