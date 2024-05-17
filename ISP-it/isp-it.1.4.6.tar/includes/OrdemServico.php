<?
################################################################################
#       Criado por: Desenvolvimento
#  Data de criação: 26/10/2006
# Ultima alteração: 26/10/2006
#    Alteração No.: 000
#
# Função:
#    Painel - Funções para gerenciamento de Ordem de Serviço

/**
 * Construtor de módulo Ordem de Serviço
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function OrdemServico( $modulo, $sub, $acao, $registro, $matriz ) {
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
		$titulo    = "<b>Ordem de Serviço</b>";
		$subtitulo = "Permite o cadastro de Ordem de Serviço";
		$itens  = Array( 'Novo', 'Procurar', 'Listar' );
		getHomeModulo( $modulo, $sub, $titulo, $subtitulo, $itens );
		echo "<br />";
		
		if( $acao == 'adicionar' || $acao == 'novo' ) {
			OrdemServicoAdicionar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( $acao == 'procurar' ) {
			OrdemServicoProcurar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( strstr( $acao, '_item' ) ) {
			switch( $acao ) {
				case "adicionar_item":
				case "novo_item":
				case 'alterar_item':
				case 'excluir_item':
				case 'cadastrar_itens':
					itensMovimentoEstoqueRequisicao( $modulo, $sub, $acao, $registro, $matriz );
					break;
			}
		}
		elseif( $acao == 'cancelar' ) {
			OrdemServicoCancelar( $modulo, $sub, $acao, $registro, $matriz);
		}
		elseif( $acao == 'ver' ) {	
				OrdemServicoVisualizar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( $acao == 'baixar' ) {
				OrdemServicoBaixar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( substr( $acao, 0, 6 ) == 'listar' ) {
				OrdemServicoListar( $modulo, $sub, $acao, $registro, $matriz );
		}
		else {
			OrdemServicoAdicionar( $modulo, $sub, 'adicionar', $registro, $matriz );
		}
	}
}

/**
 * Função de gerenciamento da tabela OrdemServico
 *
 * @return unknown
 * @param array   $matriz
 * @param string  $tipo
 * @param string  $subTipo
 * @param unknown $condicao
 * @param unknown $ordem
 */
function dbOrdemServico( $matriz, $tipo, $subTipo='', $condicao='', $ordem = '' ) {
	global $conn, $tb;
	$data = dataSistema();

	$bd = new BDIT();
	$bd->setConnection( $conn );
	$tabelas = $tb['OrdemServico'];
	$campos  = array( 'id',		'idUsuario',		  'idCliente', 				'idServicoPlano',
		'idPop',		  'descricao',			'responsavel',			'data',				'dataPrevisao', 
		'dataExecucao',				'status' );
	$valores = array( 'NULL', 	$matriz['idUsuario'], $matriz['idPessoaTipo'],	$matriz['idServicoPlano'],
		$matriz['idPop'], $matriz['descricao'], $matriz['responsavel'],	$data['dataBanco'], $matriz['dataPrevisao'],
		$matriz['dataExecucao'], 	'P' );

	if( $tipo == 'inserir' ){
		$retorno = $bd->inserir($tabelas, $campos, $valores);
	}

	if( $tipo == 'alterar' ){
		array_shift( $campos ); //retira o campo id da lista de campos
		array_shift( $valores ); //retira o elemento NULL da lista de valores
		if( $subTipo == 'status' ){
			$campos = array( 'dataExecucao', 'status' );
			$valores = array( $matriz['dataExecucao'], $matriz['status'] );
		}
		$retorno = $bd->alterar( $tabelas, $campos, $valores, $condicao );
	}

	if ( $tipo == 'consultar' ){
		if( $subTipo == 'completa' ) {
			$tabelas = "{$tb['OrdemServico']}
						LEFT JOIN {$tb['PessoasTipos']} ON ({$tb['OrdemServico']}.idCliente = {$tb['PessoasTipos']}.id) 
						LEFT JOIN {$tb['Pessoas']} ON ({$tb['PessoasTipos']}.idPessoa = {$tb['Pessoas']}.id ) 
						LEFT JOIN {$tb['POP']} ON ({$tb['OrdemServico']}.idPop = {$tb['POP']}.id) 
						LEFT JOIN {$tb['Usuarios']} ON ({$tb['OrdemServico']}.idUsuario = {$tb['Usuarios']}.id)
						LEFT JOIN {$tb['MovimentoEstoque']} ON ({$tb['OrdemServico']}.id = {$tb['MovimentoEstoque']}.idOrdemServico)
						LEFT JOIN {$tb['ServicosPlanos']} ON ({$tb['OrdemServico']}.idServicoPlano = {$tb['ServicosPlanos']}.id)
						LEFT JOIN {$tb['Servicos']} ON ({$tb['ServicosPlanos']}.idServico = {$tb['Servicos']}.id)";
			$campos  = array( "{$tb['OrdemServico']}.*", "{$tb['Pessoas']}.nome as nomeCliente",
				"{$tb['POP']}.nome as pop", "{$tb['Usuarios']}.login as usuario", 
				"{$tb['MovimentoEstoque']}.id as idMovimentoEstoque", "{$tb['Servicos']}.nome as servico");
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
			$condicao[] = "{$tb['MovimentoEstoque']}.tipo='" . MovimentoEstoqueGetTipoSaida() . "'";
		}
		if( $subTipo == 'detalhada' ) {
			$tabelas = "{$tb['OrdemServico']}
						LEFT JOIN {$tb['PessoasTipos']} ON ({$tb['OrdemServico']}.idCliente = {$tb['PessoasTipos']}.id) 
						LEFT JOIN {$tb['Pessoas']} ON ({$tb['PessoasTipos']}.idPessoa = {$tb['Pessoas']}.id ) 
						LEFT JOIN {$tb['POP']} ON ({$tb['OrdemServico']}.idPop = {$tb['POP']}.id) 
						LEFT JOIN {$tb['MovimentoEstoque']} ON ({$tb['OrdemServico']}.id = {$tb['MovimentoEstoque']}.idOrdemServico)
						LEFT JOIN {$tb['ItensMovimentoEstoque']} ON ({$tb['ItensMovimentoEstoque']}.idMovimentoEstoque = {$tb['MovimentoEstoque']}.id)
						LEFT JOIN {$tb['Produtos']} ON ({$tb['Produtos']}.id = {$tb['ItensMovimentoEstoque']}.idProduto)
						LEFT JOIN {$tb['ServicosPlanos']} ON ({$tb['OrdemServico']}.idServicoPlano = {$tb['ServicosPlanos']}.id)
						LEFT JOIN {$tb['Servicos']} ON ({$tb['ServicosPlanos']}.idServico = {$tb['Servicos']}.id)";
			$campos  = array( "{$tb['OrdemServico']}.*", "{$tb['Pessoas']}.nome as nomeCliente",
				"{$tb['POP']}.nome as pop", "{$tb['MovimentoEstoque']}.id as idMovimentoEstoque", "{$tb['Servicos']}.nome as servico",
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
 * Cadastra um nova Ordem de Serviço junto ao Movimento de Entrada de Estoque
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function OrdemServicoAdicionar( $modulo, $sub, $acao, $registro, $matriz ) {
	global $tb, $sessLogin, $sessCadastro;

	if( $matriz['bntConfirmar'] ) {
		if( $sessCadastro[$modulo.$sub.$acao] || OrdemServicoValida( $matriz ) ) {
			$matriz['usuario']		= $sessLogin['login'];
			$matriz['idUsuario']	= buscaIDUsuario( $sessLogin['login'], 'login','igual','login' );
			$matriz['dataPrevisao']	= converteData( $matriz['dataPrevisao'], 'form', 'banco');
			if( $sessCadastro[$modulo.$sub.$acao] || dbOrdemServico( $matriz, 'inserir' ) ) { // se gravou a ordem de serviço
				$registro = $matriz['idOrdemServico'] = buscaUltimoID( $tb['OrdemServico'] ); // busca a id desta ordem
				MovimentoEstoqueOrdemServico( $modulo, $sub, $acao, $registro, $matriz ); // e realiza o movimento
			}
			else {
				avisoNOURL("Aviso", "Não foi possível gravar os dados! Verifique se todos os campos foram preenchidos corretamente.", 400);
				echo "<br />";
			}
			
		}
		else {
			avisoNOURL("Aviso", "Não foi possível gravar os dados! Verifique se todos os campos foram preenchidos corretamente, <br />
			           ou se esta ordem já foi cadastrada.", 400);
			echo "<br />";
			OrdemServicoFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
	else {
		unset( $sessCadastro[$modulo.$sub.$acao] );
		OrdemServicoFormulario( $modulo, $sub, $acao, $registro, $matriz );
	}	
}

/**
 * Exibe o formulário para preenchimento da Ordem de Serviço de Cliente
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function OrdemServicoFormulario( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda, $html;

	novaTabela2( '['.( $acao == 'alterar' ? 'Alterar' : 'Nova' ).' Ordem de Serviço de Cliente]<a name="ancora"></a>',
	'center', '100%', 0, 2, 1, $corFundo, $corBorda, 2 );
	abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );
	getCampo('', '', '', '&nbsp;');
	#primeiro busca o cliente
	if ( !$matriz['idPessoaTipo'] && $acao == 'adicionar' ){
		$tipoPessoa 		  = checkTipoPessoa( 'cli' );
		$matriz['idTipoPessoa'] = $tipoPessoa['id'];
		procurarPessoasSelect( $modulo, $sub, $acao, $registro, $matriz );
	}
	else {
		$c = 11; // indice de contagens
		//campos ocultos
		$dadosPessoa = dadosPessoasTipos( $matriz['idPessoaTipo'] );
		$matriz['nomeCliente'] = $dadosPessoa['pessoa']['nome'];
		$ocultosNomes = array( 'matriz[idPessoaTipo]', 'matriz[nomeCliente]', 'matriz[nomePop]' );
		$ocultosValores = array( $matriz['idPessoaTipo'], $matriz['nomeCliente'], $matriz['nomePop'] );
		getCamposOcultos( $ocultosNomes, $ocultosValores );
		//nome do Cliente
		getCampo( 'combo', 'Cliente', '', $matriz['nomeCliente'] );
		// plano de Serviço
		getCampo( 'combo',	'Serviço',	'',pessoasTiposFormSelectServicosPlanos($matriz[idPessoaTipo], '', "idServicoPlano") );
		// pop
		getCampo( 'combo',	'POP',		'', formSelectPOP( $matriz['idPop'], "idPop", 'form', '', 'onblur="recolheNomeOpcaoSelect(8, 6)"') );
		// Nome Responsavel
		getCampo( 'text',	'Responsável', 		'matriz[responsavel]', $matriz['responsavel'], '', '', 40 );
		// Descrição
		getCampo( 'area', 	'Descrição',	'matriz[descricao]', 	   $matriz['descricao'], '', '', 38 );
		// confirmar
		getCampo( 'text', 	'Previsão de Execução',	'matriz[dataPrevisao]', $matriz['dataPrevisao'],
		"onblur=\"verificaData(this.value,".$c++.")\"",'', 10, '', '', 'Data que será executada a Ordem' );
		getBotao( 'matriz[bntConfirmar]', 			'Confirmar' );
		fechaFormulario();
	}
	fechaTabela();
}

/**
 * Exibe a listagem de Ordens de Serviços
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function OrdemServicoListar( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda, $html, $sessLogin, $limite, $tb, $imagensPBC, $statusPBC;

	$largura 				= array( '2%', 		'27%',			'20%',	'21%', 		'9%',		'21%'   );
	$gravata['cabecalho']   = array( '',		'Cliente', 		'POP', 	'Serviço',	'Data',		'Opções');
	$gravata['alinhamento'] = array( 'center',	'left', 		'left', 'left',		'center',	'center');

	$qtdColunas = count( $largura );
	novaTabela("[Listagem de Ordens de Serviços de Clientes]", 'center', '100%', 0, 2, 1, $corFundo, $corBorda, $qtdColunas );

	menuOpcAdicional( $modulo, $sub, $acao, $registro, $matriz, $qtdColunas);
	// define os tipos de filtros da listagem
	if( $acao == 'procurar' ) {
		$condicao = "{$tb['Pessoas']}.nome LIKE '%{$matriz['nome']}%'";
	}
	elseif( $acao == 'listar_pendentes' ) {
		$condicao = "{$tb['OrdemServico']}.status = 'P'";
	}
	elseif( $acao == 'listar_baixados' ) {
		$condicao = "{$tb['OrdemServico']}.status = 'B'";
	}
	elseif( $acao == 'listar_cancelados' ) {
		$condicao = "{$tb['OrdemServico']}.status = 'C'";
	}
	else {
		$condicao = '';
	}
	// realiza a consulta
	$ordemServico = dbOrdemservico( "", "consultar", "completa", $condicao, "{$tb['OrdemServico']}.data DESC" );
	$totalOrdemServico = count($ordemServico);
	
	if( $totalOrdemServico ){
		paginador( '', $totalOrdemServico, $limite['lista']['ordemServico'], $registro, 'normal10', $qtdColunas, '' );

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

		$limite = $j + $limite['lista']['ordemServico'];

		while( ( $j < $totalOrdemServico ) && ( $j < $limite ) ) {

			$default = '<a style="font-size:11px" href="?modulo=' . $modulo . '&sub=' . $sub . '&registro=' . $ordemServico[$j]->id;

			$opcoes = '';
			// realiza as opções conforme os status 
			if( $ordemServico[$j]->status == 'P' ) {
				$opcoes.= htmlMontaOpcao( $default . "&acao=adicionar_item&matriz[idOrdemServico]=" . $ordemServico[$j]->id . 
					"&matriz[idMovimentoEstoque]=" . $ordemServico[$j]->idMovimentoEstoque . "\">Ver/Adicionar Itens</a>", 'ver' );
				$opcoes.= htmlMontaOpcao( $default . "&acao=baixar&matriz[idOrdemServico]=" . $ordemServico[$j]->id . 
					"&matriz[idMovimentoEstoque]=" . $ordemServico[$j]->idMovimentoEstoque . "\">Baixar</a>", 'baixar' );
				$opcoes.= htmlMontaOpcao( $default . "&acao=cancelar&matriz[idOrdemServico]=" . $ordemServico[$j]->id . 
					"&matriz[idMovimentoEstoque]=" . $ordemServico[$j]->idMovimentoEstoque . "\">Cancelar</a>", 'cancelar' );
			}
			else {
				$opcoes .= htmlMontaOpcao( $default . "&acao=ver&matriz[idOrdemServico]=" . $ordemServico[$j]->id . 
							"&matriz[idMovimentoEstoque]=" . $ordemServico[$j]->idMovimentoEstoque . "\">Ver</a>", 'ver' );
			}
			
			$i = 0;
			htmlAbreLinha( $corFundo );
				itemLinhaTMNOURL( htmlMontaOpcao($statusPBC[$ordemServico[$j]->status],$imagensPBC[$ordemServico[$j]->status], false ),	$gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( $ordemServico[$j]->nomeCliente ,	$gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( $ordemServico[$j]->pop ,	$gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( $ordemServico[$j]->servico,	$gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				if( $ordemServico[$j]->status == 'P' ) { 
					itemLinhaTMNOURL( converteData( $ordemServico[$j]->dataPrevisao, 'banco', 'formdata' ), $gravata['alinhamento'][$i],'middle', $largura[$i++], $corFundo, 0, 'txtaviso' );
				}
				elseif( $ordemServico[$j]->status == 'B' ) {
					itemLinhaTMNOURL( converteData( $ordemServico[$j]->dataExecucao, 'banco', 'formdata' ), $gravata['alinhamento'][$i],'middle', $largura[$i++], $corFundo, 0, 'txtok' );
				}
				else {
					itemLinhaTMNOURL( converteData( $ordemServico[$j]->data, 'banco', 'formdata' ), $gravata['alinhamento'][$i],'middle', $largura[$i++], $corFundo, 0, 'txttrial' );
				}
				itemLinhaTMNOURL( $opcoes , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
			htmlFechaLinha();
			$j++;
		}
	}
	else {
		htmlAbreLinha($corFundo);
		itemLinhaTMNOURL( '<span class="txtaviso"><i>Nenhuma Ordem de Serviço de Cliente encontrada!</i></span>', 'center', 'middle', $largura[$i], $corFundo, $qtdColunas, 'normal10' );
		htmlFechaLinha();
	}
	fechaTabela();	
}

/**
 * Procura Ordens de Serviços de Clientes
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function OrdemServicoProcurar( $modulo, $sub, $acao, $registro, $matriz ) {
	if( !$matriz['bntProcurar'] ){
		$matriz['nome']='';
	}
	getFormProcurar( $modulo, $sub, $acao, $matriz, "Ordem de Serviço de Cliente" );
	
	if( $matriz['nome'] && $matriz['bntProcurar'] ){
		OrdemServicoListar( $modulo, $sub, $acao, $registro, $matriz );
	}	
}

/**
 * Apenas exibe os dados da Ordem de Serviço do Cliente
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function OrdemServicoVisualizar( $modulo, $sub, $acao, $registro, $matriz ) {
	( $acao == 'baixar' ? 
		OrdemServicoFormularioBaixar( $modulo, $sub, $acao, $registro, $matriz ) : 
		OrdemServicoVer( $modulo, $sub, $acao, $registro, $matriz ) );
	itensMovimentoEstoqueRequisicaoListagem( $modulo, $sub, $acao, $registro, $matriz );	
}

/**
 * Cancela uma Ordem de Serviço de Cliente
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function OrdemServicoCancelar( $modulo, $sub, $acao, $registro, $matriz ) {
	if( $matriz['bntConfirmar'] ) {
		$matriz = array( 'status' => 'C' ); 
	 	$msg = ( dbOrdemServico( $matriz, 'alterar', 'status', 'id='.$registro ) 
	 			? 'Ordem de Serviço de Cliente cancelada com sucesso!' 
	 			: 'Não foi possível cancelar a Ordem de Serviço de Cliente.' );
		avisoNOURL("Aviso", $msg, 410);
		echo "<br />";
		OrdemServicoListar( $modulo, $sub, 'listar', '', $matriz );	
	}
	else{
		OrdemServicoVisualizar( $modulo, $sub, $acao, $registro, $matriz );
	}	
}

/**
 * Realiza a baixa da Ordem de Serviço de Cliente, exibindo a tela confirmação
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function OrdemServicoBaixar( $modulo, $sub, $acao, $registro, $matriz ) {
	global $sessCadastro;
	if( isset( $matriz['bntConfirmar'] ) && $matriz['bntConfirmar'] == 'Baixar' && validaData( $matriz['dataExecucao'] ) ) {

	 	if( $sessCadastro[$modulo.$sub.$acao] || OrdemServicoDarBaixa( $modulo, $sub, $acao, $registro, $matriz ) ) {
			$msg = 'Ordem de Serviço de Cliente baixada com sucesso!';
	 		avisoNOURL( "Aviso", $msg, 400 );
			echo "<br />";
			OrdemServicoListar( $modulo, $sub, 'listar', '', $matriz );
	 	}

	}
	else{
		unset( $sessCadastro[$modulo.$sub.$acao] );
		OrdemServicoVisualizar( $modulo, $sub, $acao, $registro, $matriz );
	}
}

/**
 * Realiza as ações de backend de baixa da Ordem de Serviço
 * 
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array $matriz
 * @return boolean
 */
function OrdemServicoDarBaixa( $modulo, $sub, $acao, $registro, $matriz ) {
	global $tb, $sessCadastro;
	$url['modulo'] = $modulo;
	$url['sub'] = $sub;
	$url['registro'] = $registro;
	$url['matriz'] = $matriz;
	$url['acao'] = $acao;
	
	$retorno = true;
	$ordemServico = dbOrdemServico( '', 'consultar', 'completa', $tb['OrdemServico'].'.id='.$registro );
	
	//verifica o parametro de liberação de estoque negativo
	if ( !liberaEstoqueNegativo() ) {
		// realiza a saída dos itens no Estoque
		$movimento = MovimentoEstoqueSaida( $ordemServico[0]->idMovimentoEstoque, $ordemServico[0]->idPop, $url );
	}
	else {
		//realiza a saída dos itens no Estoque mesmo se a quantidade for insuficiente 
		$movimento = MovimentoEstoqueNegativoSaida( $ordemServico[0]->idMovimentoEstoque, $ordemServico[0]->idPop, $url );
	}
	if( $movimento ) {
		$dados['status'] = 'B';
		$dados['dataExecucao'] = converteData( $matriz['dataExecucao'], 'form', 'banco');
		$retorno = dbOrdemServico( $dados, 'alterar', 'status', 'id='.$registro );
		$sessCadastro[$modulo.$sub.$acao] = "gravado";
	}
	else{
		$retorno = false;
	}

	return $retorno;
}

/**
 * Valida se o campo responsável não foi preenchido corretamente
 *
 * @param array $matriz
 * @return boolean
 */
function OrdemServicoValida( $matriz ) {
	$retorno = true;
	if( !validaData( $matriz['dataPrevisao'] ) ) {
		$retorno = false;
	}
	if( empty( $matriz['responsavel'] ) ) {	
		$retorno = false;	
	}
	if( empty( $matriz['idServicoPlano'] ) ) {	
		$retorno = false;	
	}
	return $retorno;
}

/**
 * Visualiza os dados da Ordem de Serviço de Cliente
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function OrdemServicoVer( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda, $html, $tb;

	$OrdemServico = dbOrdemServico( '', "consultar", 'completa', "{$tb['OrdemServico']}.id='" . $registro . "'" );
	if( count( $OrdemServico ) > 0 ) {
		$dadosOS = get_object_vars( $OrdemServico[0] );
		$dadosOS['idPessoaTipo'] = $dadosOS['idCliente'];
		$Acao = ucfirst( $acao );
		novaTabela2("[" . $Acao . " Ordem de Serviço de Cliente]" . "<a name=\"ancora\"></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		if ($dadosOS['status'] == 'P' ){
			menuOpcAdicional( $modulo, $sub, 'ver', $registro, $matriz, 2 );
		}
		else {
			getCampo('', '', '', '&nbsp;');
		}
		getCampo( 'combo', _('Cliente'), 				'',	$dadosOS['nomeCliente'] );
		getCampo( 'combo', _('Usuário'),					'',	$dadosOS['usuario'] );
		getCampo( 'combo', _('Serviço'),					'',	$dadosOS['servico'] );
		getCampo( 'combo', _('POP'), 						'',	$dadosOS['pop'] );
		getCampo( 'combo', _('Responsável'), 				'',	$dadosOS['responsavel'] );
		getCampo( 'combo', _('Previsão de Execução'), 		'',	converteData( $dadosOS['dataPrevisao'], 'banco', 'formdata' ) );
		getCampo( 'combo', _('Descrição'), 					'',	$dadosOS['descricao'] );
		getCampo( 'combo', _("Status"), 					"",	formSelectStatus( $dadosOS['status'], 'status', 'check_pbc' ) );
		if( $acao == 'cancelar' || $acao == 'baixar' ) {
			getBotao( 'matriz[bntConfirmar]', $Acao, 'submit','button', 'onclick="window.location=\'?modulo='.$modulo.
				      '&sub='.$sub.'&acao='.$acao.'&registro='.$registro.'&matriz[bntConfirmar]='.$Acao.'\'"' );
		}
		else {
			getCampo('', '', '', '&nbsp;');
		}
		fechaTabela();
	}
	else {
		avisoNOURL("Erro", "Não foi possível localizar a Ordem de Serviço!", 400);
		echo "<br />";
		OrdemServicoListar( $modulo, $sub, 'listar', $registro, $matriz );
	}
}

/**
 * Visualiza os dados da Ordem de Serviço de Cliente e exibe o campo de data de Execução para alteração
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function OrdemServicoFormularioBaixar( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda, $html, $tb;
	$data = dataSistema();
	
	$OrdemServico = dbOrdemServico( '', "consultar", 'completa', "{$tb['OrdemServico']}.id='" . $registro . "'" );
	if( count( $OrdemServico ) > 0 ) {
		$Acao = ucfirst( $acao );
		novaTabela2("[" . $Acao . " Ordem de Serviço de Cliente]" . "<a name=\"ancora\"></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );
				$ocultosNomes = array( 'matriz[idMovimentoEstoque]', 'matriz[idOrdemServico]' );
				$ocultosValores = array( $matriz['idMovimentoEstoque'], $matriz['idOrdemServico'] );
				getCamposOcultos( $ocultosNomes, $ocultosValores );
				$matriz = get_object_vars( $OrdemServico[0] );
				$matriz['idPessoaTipo'] = $matriz['idCliente'];
				$matriz['dataExecucao'] = $data['dataNormalData'];
				$matriz['idOrdemServico'] = $registro;
				menuOpcAdicional( $modulo, $sub, 'ver', $registro, $matriz, 2 );
				getCampo( 'combo', _('Cliente'), '',	$matriz['nomeCliente'] );
				getCampo( 'combo', _('Usuário'), '',	$matriz['usuario'] );
				getCampo( 'combo', _('Serviço'), 				'',	$matriz['servico'] );
				getCampo( 'combo', _('POP'), 					'',	$matriz['pop'] );
				getCampo( 'combo', _('Responsável'), 			'',	$matriz['responsavel'] );
				getCampo( 'combo', _('Previsão de Execução'), 	'',	converteData( $matriz['dataPrevisao'], 'banco', 'formdata' ) );
				getCampo( 'combo', 	'Data de Execução',	'', getCampoData( 'matriz[dataExecucao]', $matriz['dataExecucao'] ) );
				getCampo( 'combo', _('Descrição'), 				'',	$matriz['descricao'] );
				getCampo( 'combo', _("Status"), 				"",	formSelectStatus( $matriz['status'], 'status', 'check_pbc' ) );
				getBotao( 'matriz[bntConfirmar]', 'Baixar' );
			fechaFormulario();
		fechaTabela();
	}
}
?>