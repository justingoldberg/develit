<?
################################################################################
#       Criado por: Desenvolvimento
#  Data de criação: 20/10/2006
# Ultima alteração: 26/10/2006
#    Alteração No.: 008
#
# Função:
#    Painel - Funções para gerenciamento de Requisição e Retorno de Produtos

/**
 * Construtor de módulo Requisição/Retorno de Produtos
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function RequisicaoRetorno( $modulo, $sub, $acao, $registro, $matriz ) {
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
		$titulo    = "<b>Requisição/Retorno de Produtos</b>";
		$subtitulo = "Permite a requisição ou retorno de produtos";
		$itens  = Array( 'Novo', 'Procurar', 'Listar' );
		getHomeModulo( $modulo, $sub, $titulo, $subtitulo, $itens );
		echo "<br />";
		if( $acao == 'adicionar' || $acao == 'novo' ) {
			RequisicaoRetornoAdicionar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( $acao == 'procurar' ) {
			RequisicaoRetornoProcurar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( strstr( $acao, '_item' ) ) {
			switch( $acao ) {
				case "adicionar_item":
				case "novo_item":
				case "alterar_item":
				case "excluir_item":
					itensMovimentoEstoqueRequisicao( $modulo, $sub, $acao, $registro, $matriz );
					break;
			}
		}
		elseif( $acao == 'cancelar' ) {
			RequisicaoRetornoCancelar( $modulo, $sub, $acao, $registro, $matriz);
		}
		elseif( $acao == 'alterar' ) {
			RequisicaoRetornoAlterar( $modulo, $sub, $acao, $registro, $matriz);
		}
		elseif( $acao == 'ver' ) {
			RequisicaoRetornoVisualizar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( $acao == 'baixar' ) {
			RequisicaoRetornoBaixar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( substr( $acao, 0, 6 ) == 'listar' ) {
			RequisicaoRetornoListar( $modulo, $sub, $acao, $registro, $matriz );
		}
		else {
			RequisicaoRetornoAdicionar( $modulo, $sub, 'adicionar', $registro, $matriz );
		}
	}
}

/**
 * Função de gerenciamento da tabela RequisicaoRetorno
 *
 * @return unknown
 * @param array   $matriz
 * @param string  $tipo
 * @param string  $subTipo
 * @param unknown $condicao
 * @param unknown $ordem
 */
function dbRequisicaoRetorno( $matriz, $tipo, $subTipo='', $condicao='', $ordem = '' ) {
	global $conn, $tb;
	$data = dataSistema();

	$bd = new BDIT();
	$bd->setConnection( $conn );
	$tabelas = $tb['RequisicaoRetorno'];
	$campos  = array( 'id',		'idUsuario',			'idPop', 			'responsavel', 
	'descricao', 			'data',	 			'tipo', 			'status' );
	$valores = array( 'NULL',	$matriz['idUsuario'],	$matriz['idPop'],	$matriz['responsavel'],
	$matriz['descricao'], 	$data['dataBanco'],  $matriz['tipo'], 	'P' );
	if ( $tipo == 'inserir' ){

		$retorno = $bd->inserir($tabelas, $campos, $valores);
	}
	
	if ( $tipo == 'alterar' ){
		array_shift( $campos ); //retira o campo id da lista de campos
		array_shift( $valores ); //retira o elemento NULL da lista de valores
		if( $subTipo == 'status' ){
			$campos = array( 'status' );
			$valores = array( $matriz['status'] );
		}
		$retorno = $bd->alterar( $tabelas, $campos, $valores, $condicao );
	}
	
	if ( $tipo == 'consultar' ){
		if( $subTipo == 'completa' ) {
			$tabelas = "{$tb['RequisicaoRetorno']}
						LEFT JOIN {$tb['POP']} ON ({$tb['RequisicaoRetorno']}.idPop = {$tb['POP']}.id) 
						LEFT JOIN {$tb['Usuarios']} ON ({$tb['RequisicaoRetorno']}.idUsuario = {$tb['Usuarios']}.id)
						LEFT JOIN {$tb['MovimentoEstoque']} ON ({$tb['RequisicaoRetorno']}.id = {$tb['MovimentoEstoque']}.idRequisicao)";
			$campos  = array( "{$tb['RequisicaoRetorno']}.*", "{$tb['POP']}.nome as pop", "{$tb['Usuarios']}.login as usuario", "{$tb['MovimentoEstoque']}.id as idMovimentoEstoque");
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
		//	$condicao[] = "{$tb['MovimentoEstoque']}.tipo='" . MovimentoEstoqueGetTipoEntrada() . "'";
		}
		if( $subTipo == 'detalhada' ) {
			$tabelas = "{$tb['RequisicaoRetorno']}
						LEFT JOIN {$tb['POP']} ON ({$tb['RequisicaoRetorno']}.idPop = {$tb['POP']}.id) 
						LEFT JOIN {$tb['MovimentoEstoque']} ON ({$tb['RequisicaoRetorno']}.id = {$tb['MovimentoEstoque']}.idRequisicao)
						LEFT JOIN {$tb['ItensMovimentoEstoque']} ON ({$tb['ItensMovimentoEstoque']}.idMovimentoEstoque = {$tb['MovimentoEstoque']}.id)
						LEFT JOIN {$tb['Produtos']} ON ({$tb['Produtos']}.id = {$tb['ItensMovimentoEstoque']}.idProduto)";
			$campos  = array( "{$tb['RequisicaoRetorno']}.*", "{$tb['POP']}.nome as pop", "{$tb['MovimentoEstoque']}.id as idMovimentoEstoque", 
				"{$tb['ItensMovimentoEstoque']}.idProduto", "{$tb['ItensMovimentoEstoque']}.quantidade as qtde", "{$tb['Produtos']}.nome as produto");
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
	if( $tipo == 'excluir' ){
		$retorno = $bd->excluir( $tabelas, $condicao );
	}
	return ($retorno);
}

/**
 * Cadastra um nova requisição ou retorno de produtos junto ao Movimento de Estoque
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function RequisicaoRetornoAdicionar( $modulo, $sub, $acao, $registro, $matriz ) {
	global $tb, $sessLogin, $sessCadastro;

	if( $matriz['bntConfirmar'] ) {
		if( $sessCadastro[$modulo.$sub.$acao] || RequisicaoRetornoValida( $matriz ) ) {
			$matriz['usuario']		= $sessLogin['login'];
			$matriz['data']			= converteData( $matriz['data'], 'form', 'banco');
			$matriz['idUsuario']	= buscaIDUsuario( $sessLogin['login'], 'login','igual','login' );
			if( $sessCadastro[$modulo.$sub.$acao] || dbRequisicaoRetorno( $matriz, 'inserir' ) ) { // se gravou a nota
				$registro = $matriz['idRequisicao'] = buscaUltimoID( $tb['RequisicaoRetorno'] ); // busca a id desta nota
				MovimentoEstoqueRequisicaoRetorno( $modulo, $sub, $acao, $registro, $matriz ); // e realiza o movimento
			}
			else {
				avisoNOURL("Aviso", "Não foi possível gravar os dados! Verifique se todos os campos foram preenchidos corretamente.", 400);
				echo "<br />";
			}
			
		}
		else {
			avisoNOURL("Aviso", "Não foi possível gravar os dados! Verifique se todos os campos foram preenchidos corretamente.", 400);
			echo "<br />";
			RequisicaoRetornoFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
	else {
		unset( $sessCadastro[$modulo.$sub.$acao] );
		RequisicaoRetornoFormulario( $modulo, $sub, $acao, $registro, $matriz );
	}
}

/**
 * Exibe o formulário da Requisicao/Retorno do Produto
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function RequisicaoRetornoFormulario( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda, $html;

	novaTabela2( '['.( $acao == 'alterar' ? 'Alterar' : 'Nova' ).' Requisição/Retorno de Produtos]<a name="ancora"></a>',
	'center', '100%', 0, 2, 1, $corFundo, $corBorda, 2 );
		abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );
			getCampo('', '', '', '&nbsp;');
	
			//campos ocultos
			$dadosPessoa = dadosPessoasTipos( $matriz['idPessoaTipo'] );
			$ocultosNomes = array( 'matriz[idPessoaTipo]', 'matriz[nomePop]' );
			$ocultosValores = array( $matriz['idPessoaTipo'], $matriz['nomePop'] );
			getCamposOcultos( $ocultosNomes, $ocultosValores );
			// tipo
			getCampo( 'combo', 	'Tipo', '', formSelectStatus( $matriz['tipo'], 'tipo', 'form_es' ) );
			// pop
			getCampo( 'combo',	'POP', '', formSelectPOP( $matriz['idPop'], "idPop", 'form', '', 'onblur="recolheNomeOpcaoSelect(7, 5)"') );
			// Nome Responsavel
			getCampo( 'text',	'Responsável', 		'matriz[responsavel]', $matriz['responsavel'], '', '', 40 );
			// Descrição
			getCampo( 'area', 	'Descrição',	'matriz[descricao]', 	   $matriz['descricao'], '', '', 38 );
			// confirmar		
			getBotao( 'matriz[bntConfirmar]', 'Confirmar' );
		fechaFormulario();
	fechaTabela();
}

/**
 * Valida se o campo responsável não foi preenchido corretamente
 *
 * @param array $matriz
 * @return boolean
 */
function RequisicaoRetornoValida( $matriz ) {
	$retorno = true;
	
	if( empty( $matriz['responsavel'] ) ) {	
		$retorno = false;	
	}
	return $retorno;
}

/**
 * Exibe a listagem de Requisição/Retorno de produtos
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function RequisicaoRetornoListar( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda, $html, $sessLogin, $limite, $tb, $imagensPBC, $statusPBC;

	$largura 				= array('2%', 		'9%',		'34%',	'24%', 			'10%',		'21%'   );
	$gravata['cabecalho']   = array('',			'Tipo', 	'POP', 	'Responsável',	'Data',		'Opções');
	$gravata['alinhamento'] = array('center', 	'center',	'left', 'left',	    	'center',	'center');

	$qtdColunas = count( $largura );
	novaTabela("[Listagem de Requisição/Retorno de Produtos]", 'center', '100%', 0, 2, 1, $corFundo, $corBorda, $qtdColunas );

	menuOpcAdicional( $modulo, $sub, $acao, $registro, $matriz, $qtdColunas);

	if( $acao == 'listar_entrada' ) {
		$condicao = "{$tb['RequisicaoRetorno']}.tipo='E' ";
	}
	elseif( $acao == 'listar_saida' ) {
		$condicao = "{$tb['RequisicaoRetorno']}.tipo='S'";
	}
	elseif( $acao == 'listar_pendentes' ) {
		$condicao = "{$tb['RequisicaoRetorno']}.status='P'";
	}
	elseif( $acao == 'listar_baixados' ) {
		$condicao = "{$tb['RequisicaoRetorno']}.status='B'";
	}
	elseif( $acao == 'listar_cancelados' ) {
		$condicao = "{$tb['RequisicaoRetorno']}.status='C'";
	}
	elseif( $acao == 'procurar' ) {
		$condicao = "{$tb['RequisicaoRetorno']}.responsavel LIKE '%{$matriz['nome']}%'";
	}
	else {
		$condicao = '';
	}

	$requisicao = dbRequisicaoRetorno( "", "consultar", "completa", $condicao, "{$tb['RequisicaoRetorno']}.data DESC" );
	$totalRequisicao = count($requisicao);
	if( $totalRequisicao ){
		paginador( '', $totalRequisicao, $limite['lista']['requisicao'], $registro, 'normal10', $qtdColunas, '' );

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

		$limite = $j + $limite['lista']['requisicao'];

		while( ( $j < $totalRequisicao ) && ( $j < $limite ) ) {

			$default = '<a style="font-size:11px" href="?modulo=' . $modulo . '&sub=' . $sub . '&registro=' . $requisicao[$j]->id;

			$opcoes = '';
			if( $requisicao[$j]->status == 'P' ) {
				$opcoes.= htmlMontaOpcao( $default . "&acao=adicionar_item&matriz[idRequisicao]=" . $requisicao[$j]->id . 
					"&matriz[idMovimentoEstoque]=" . $requisicao[$j]->idMovimentoEstoque . "\">Ver/Adicionar Itens</a>", 'ver' );
				$opcoes.= htmlMontaOpcao( $default . "&acao=alterar&matriz[idRequisicao]=" . $requisicao[$j]->id . 
					"&matriz[idMovimentoEstoque]=" . $requisicao[$j]->idMovimentoEstoque . "\">Alterar</a>", 'alterar' );
				$opcoes.= htmlMontaOpcao( $default . "&acao=baixar&matriz[idRequisicao]=" . $requisicao[$j]->id . 
					"&matriz[idMovimentoEstoque]=" . $requisicao[$j]->idMovimentoEstoque . "\">Baixar</a>", 'baixar' );
				$opcoes.= htmlMontaOpcao( $default . "&acao=cancelar&matriz[idRequisicao]=" . $requisicao[$j]->id . 
					"&matriz[idMovimentoEstoque]=" . $requisicao[$j]->idMovimentoEstoque . "\">Cancelar</a>", 'cancelar' );
			}
			else {
				$opcoes .= htmlMontaOpcao( $default . "&acao=ver&matriz[idRequisicao]=" . $requisicao[$j]->id . 
							"&matriz[idMovimentoEstoque]=" . $requisicao[$j]->idMovimentoEstoque . "\">Ver</a>", 'ver' );
			}
			
			$i = 0;
			htmlAbreLinha( $corFundo );
				itemLinhaTMNOURL( htmlMontaOpcao($statusPBC[$requisicao[$j]->status],$imagensPBC[$requisicao[$j]->status], false ),	$gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( formSelectStatus( $requisicao[$j]->tipo, 'tipo', 'check_es' ), $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( $requisicao[$j]->pop ,		$gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( $requisicao[$j]->responsavel,	$gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( converteData( $requisicao[$j]->data, 'banco', 'formdata' ), $gravata['alinhamento'][$i],'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( $opcoes , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
			htmlFechaLinha();
			$j++;
		}
	}
	else {
		htmlAbreLinha($corFundo);
		itemLinhaTMNOURL( '<span class="txtaviso"><i>Nenhuma Requisição/Retorno encontrada!</i></span>', 'center', 'middle', $largura[$i], $corFundo, $qtdColunas, 'normal10' );
		htmlFechaLinha();
	}
	fechaTabela();
}

/**
 * Procura Requisição/Retorno de produtos
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function RequisicaoRetornoProcurar( $modulo, $sub, $acao, $registro, $matriz ) {
	if( !$matriz['bntProcurar'] ){
		$matriz['nome']='';
	}
	getFormProcurar( $modulo, $sub, $acao, $matriz, "Requisição/Retorno" );
	
	if( $matriz['nome'] && $matriz['bntProcurar'] ){
		RequisicaoRetornoListar( $modulo, $sub, $acao, $registro, $matriz );
	}
}

/**
 * Visualiza os dados da Requisicao/Retorno de produto
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function RequisicaoRetornoVer( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda, $html, $tb;

	$requisicao = dbRequisicaoRetorno( '', "consultar", 'completa', "{$tb['RequisicaoRetorno']}.id='" . $registro . "'" );
	if( count( $requisicao ) > 0 ) {
		$dadosRequisicao = get_object_vars( $requisicao[0] );
		$Acao = ucfirst( $acao );
		novaTabela2("[" . $Acao .' '. ( $dadosRequisicao['tipo'] == 'E' ? 'Retorno' : 'Requisição' ) ." de Produtos]" . 
					"<a name=\"ancora\"></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2 );

		if ($dadosRequisicao['status'] == 'P' ){
			menuOpcAdicional( $modulo, $sub, 'ver', $registro, $matriz, 2 );
		}
		else {
			getCampo('', '', '', '&nbsp;');
		}
		getCampo( 'combo', _('Usuário'),		'',	$dadosRequisicao['usuario'] );
		getCampo( 'combo', _('POP'), 			'',	$dadosRequisicao['pop'] );
		getCampo( 'combo', _('Responsável'),	'',	$dadosRequisicao['responsavel'] );
		getCampo( 'combo', _('Descrição'),		'',	$dadosRequisicao['descricao'] );
		getCampo( 'combo', _("Status"), 		"",	formSelectStatus( $dadosRequisicao['status'], 'status', 'check_pbc' ) );
		if( $acao == 'cancelar' || $acao == 'baixar' ) {
			getBotao( 'matriz[bntConfirmar]', $Acao, 'submit','button', 'onclick="window.location=\'?modulo='.$modulo.
				      '&sub='.$sub.'&acao='.$acao.'&registro='.$registro.'&matriz[bntConfirmar]='.$Acao.
				      '&matriz[tipo]='.$dadosRequisicao['tipo'].'\'"' );
		}
		else {
			getCampo('', '', '', '&nbsp;');
		}
		fechaTabela();
	}
	else {
		avisoNOURL("Erro", "Não foi possível localizar a Requisição ou o Retorno!", 400);
		echo "<br />";
		RequisicaoRetornoListar( $modulo, $sub, 'listar', $registro, $matriz );
	}
}

/**
 * Apenas exibe os dados da Requisição/Retorno de produtos
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function RequisicaoRetornoVisualizar( $modulo, $sub, $acao, $registro, $matriz ){
	RequisicaoRetornoVer( $modulo, $sub, $acao, $registro, $matriz );
	itensMovimentoEstoqueRequisicaoListagem( $modulo, $sub, $acao, $registro, $matriz );	
}

/**
 * Cancela uma Requisição/Retorno de produtos
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function RequisicaoRetornoCancelar( $modulo, $sub, $acao, $registro, $matriz ){
	if( $matriz['bntConfirmar'] ) {
		$matriz = array( 'status' => 'C' );
		if( $matriz['tipo'] == 'E' ) {
			$msgSucesso = 'Retorno de Produtos cancelado com sucesso!';
			$msgErro	= 'Não foi possível cancelar o Retorno de Produtos.';
		}
		else{
			$msgSucesso = 'Requisição de Produtos cancelada com sucesso!';
			$msgErro	= 'Não foi possível cancelar a Requisição de Produtos.';
		}

	 	$msg = ( dbRequisicaoRetorno( $matriz, 'alterar', 'status', 'id='.$registro ) ? $msgSucesso : $msgErro );
		avisoNOURL("Aviso", $msg, 400);
		echo "<br />";
		RequisicaoRetornoListar( $modulo, $sub, 'listar', '', $matriz );	
	}
	else{
		RequisicaoRetornoVisualizar( $modulo, $sub, $acao, $registro, $matriz );
	}	
}

/**
 * Realiza a baixa de Requisição/Retorno de Produtos, exibindo a tela confirmação
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function RequisicaoRetornoBaixar ( $modulo, $sub, $acao, $registro, $matriz ){
	global $sessCadastro;
	if( isset( $matriz['bntConfirmar'] ) && $matriz['bntConfirmar'] == 'Baixar' ) {

	 	if( $sessCadastro[$modulo.$sub.$acao] || RequisicaoRetornoDarBaixa( $modulo, $sub, $acao, $registro, $matriz ) ) {
			$msg = ( $matriz['tipo'] == 'E' ? 'Retorno de Produtos baixado com sucesso!' : 'Requisição de Produtos baixada com sucesso!' );
	 		avisoNOURL( "Aviso", $msg, 400 );
			echo "<br />";
			RequisicaoRetornoListar( $modulo, $sub, 'listar', '', $matriz );
	 	}

	}
	else{
		unset( $sessCadastro[$modulo.$sub.$acao] );
		RequisicaoRetornoVisualizar( $modulo, $sub, $acao, $registro, $matriz );
	}
}

/**
 * Realiza as ações de backend de baixa da Requisicao/Retorno
 * 
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array $matriz
 * @return boolean
 */
function RequisicaoRetornoDarBaixa( $modulo, $sub, $acao, $registro, $matriz ) {
	global $tb, $sessCadastro;
	$url['modulo'] = $modulo;
	$url['sub'] = $sub;
	$url['registro'] = $registro;
	$url['matriz'] = $matriz;
	$url['acao'] = $acao;
	
	$retorno = true;
	$requisicao = dbRequisicaoRetorno( '', 'consultar', 'completa', $tb['RequisicaoRetorno'].'.id='.$registro );
	
	//verifica se é entrada ou saída de produtos
	if( $requisicao[0]->tipo == 'E' ) {
		// realiza a entrada dos itens no Estoque
		$movimento = MovimentoEstoqueEntrada( $requisicao[0]->idMovimentoEstoque, $requisicao[0]->idPop, false, $url );
	}
	else {
		//verifica o parametro de liberação de estoque negativo
		if ( !liberaEstoqueNegativo() ) {
			// realiza a saída dos itens no Estoque
			$movimento = MovimentoEstoqueSaida( $requisicao[0]->idMovimentoEstoque, $requisicao[0]->idPop, $url );
		}
		else {
			//realiza a saída dos itens no Estoque mesmo se a quantidade for insuficiente 
			$movimento = MovimentoEstoqueNegativoSaida( $requisicao[0]->idMovimentoEstoque, $requisicao[0]->idPop, $url );
		}
	}
	if( $movimento ) {
		$status['status'] = 'B';
		$retorno = dbRequisicaoRetorno( $status, 'alterar', 'status', 'id='.$registro );
		$sessCadastro[$modulo.$sub.$acao] = "gravado";
	}
	else{
		$retorno = false;
	}

	return $retorno;
}

/**
 * Realiza a devolução de Requisição/Retorno de Produtos, exibindo a tela confirmação
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function RequisicaoRetornoDevolver( $modulo, $sub, $acao, $registro, $matriz ){
	global $sessCadastro;
	
	if( isset( $matriz['bntConfirmar'] ) && $matriz['bntConfirmar'] == 'Devolver' ) {

	 	if( $sessCadastro[$modulo.$sub.$acao] || RequisicaoRetornoDevolucao( $modulo, $sub, $acao, $registro, $matriz ) ) {
	 		$msg = "Devolução de Produtos por Requisição/Retorno efetuada com sucesso!";
	 		avisoNOURL( "Aviso", $msg, 400 );
			echo "<br />";
	 	}
		RequisicaoRetornoListar( $modulo, $sub, 'listar', $registro, $matriz );	
	}
	else{
		unset( $sessCadastro[$modulo.$sub.$acao] );
		RequisicaoRetornoVisualizar( $modulo, $sub, $acao, $registro, $matriz );
	}
}

/**
 * Realiza as ações de backend de devolução da Requisicao/Retorno
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array $matriz
 * @return boolean
 */
function RequisicaoRetornoDevolucao( $modulo, $sub, $acao, $registro, $matriz ){
	global $tb, $sessCadastro;
	
	$retorno = true;
	$requisicao = dbRequisicaoRetorno( '', 'consultar', 'completa', $tb['RequisicaoRetorno'].'.id='.$registro );
	// realiza a entrana dos itens no Estoque
	$movimento = ( ($requisicao[0]->tipo == 'E' ) ? 
		MovimentoEstoqueSaida( $requisicao[0]->idMovimentoEstoque, $requisicao[0]->idPop ) : 
		MovimentoEstoqueEntrada( $requisicao[0]->idMovimentoEstoque, $requisicao[0]->idPop, false ) );
	if( $movimento ) {
		$status['status'] = 'P';
		$retorno = dbRequisicaoRetorno( $status, 'alterar', 'status', 'id='.$registro );
		$sessCadastro[$modulo.$sub.$acao] = "gravado";
	}
	else{
		$retorno = false;
	}

	return $retorno;
}

/**
 * Realiza a alteração de requisição/retorno
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function RequisicaoRetornoAlterar( $modulo, $sub, $acao, $registro, $matriz ) {
	global $sessLogin;
	
	if( $matriz["bntConfirmar"] ){ 
		if( RequisicaoRetornoValida( $matriz, $acao ) ){ // se clicou no botão confirmar e os dados são validos
			// grava os dados atualizados
			$matriz['idUsuario']	= buscaIDUsuario( $sessLogin['login'], 'login','igual','login' );
			$gravar = dbRequisicaoRetorno( $matriz, 'alterar', "", "id='" . $registro . "'" ); 
			if( $gravar ) { // se gravou avisa que gravou com sucesso e exibe a listagem
				($matriz['tipo'] =='E' ? 
					$msg = 'Retorno de Produtos alterado com sucesso!':
					$msg = 'Requisição de Produtos alterada com sucesso!' );
				avisoNOURL( 'Aviso', $msg, 400 );
				echo "<br />";
				RequisicaoRetornoListar( $modulo, $sub, 'listar', '', $matriz );
			}
			else { // senão avisa que teve um erro e exibe o formulario de alteração
				avisoNOURL( "Erro", "Não foi possível gravar dados!", 400 );
				echo "<br />";
				RequisicaoRetornoFormulario( $modulo, $sub, $acao, $registro, $matriz );
			}
		}		
		else {
			avisoNOURL("Aviso", "Não foi possível gravar os dados! Verifique se todos os campos foram preenchidos corretamente.", 400);
			echo "<br />";
			RequisicaoRetornoFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}		
	}
	else{
		// busca os dados do produto cadastrado
		$conta = dbRequisicaoRetorno( '', "consultar","", "id='" . $registro . "'" );
		if ( count( $conta ) ){ // se encontrou transfere os dados para matriz
			$matriz["idPop"] 		= $conta[0]->idPop;
			$matriz["tipo"] 		= $conta[0]->tipo;
			$matriz["id"] 			= $conta[0]->id;
			$matriz["responsavel"] 	= $conta[0]->responsavel;
			$matriz["descricao"] 	= $conta[0]->descricao;
			RequisicaoRetornoFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}
		else{
			avisoNOURL("Erro", "Não foi possível localizar a Requisição ou Retorno!", 400);
			echo "<br />";
			RequisicaoRetornoListar( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
}
?>