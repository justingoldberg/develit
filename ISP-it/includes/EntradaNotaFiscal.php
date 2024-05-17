<?
################################################################################
#       Criado por: Desenvolvimento
#  Data de criação: 09/10/2006
# Ultima alteração: 09/10/2006
#    Alteração No.: 000
#
# Função:
#    Painel - Funções para gerenciamento de Entrada de Produtos por Nota Fiscal

/**
 * Construtor de módulo Estoque de Entrada de Produtos por Nota Fiscal
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function EntradaNotaFiscal( $modulo, $sub, $acao, $registro, $matriz ) {

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
		$titulo    = "<b>Entrada de Produtos por Nota Fiscal</b>";
		$subtitulo = "Permite a entrada de produtos no estoque pela Nota Fiscal de Fornecedores";
		$itens  = Array( 'Novo', 'Procurar', 'Listar' );
		getHomeModulo( $modulo, $sub, $titulo, $subtitulo, $itens );
		echo "<br />";

		if( $acao == 'adicionar' || $acao == 'novo' ) {
			EntradaNotaFiscalAdicionar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( $acao == 'lancar_contaPagar' ) {
			contasAPagarNFEAdicionar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( $acao == 'cancelar_contaPagar' ){
			contasAPagarNFECancelar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( $acao == 'finaliza_lancarCP' ) {
			EntradaNotaFiscalFinalizaLancarCP( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( strstr( $acao, '_parcelas' ) ) {
			contasAPagarNFEParcelas( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( strstr( $acao, '_itens' ) || strstr( $acao, '_item' ) ) {
			itensMovimentoEstoqueNF( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( $acao == 'procurar' ) {
			EntradaNotaFiscalProcurar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( $acao == 'ver' ) {
			EntradaNotaFiscalVisualizar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( $acao == 'cancelar' ) {
			EntradaNotaFiscalCancelar( $modulo, $sub, $acao, $registro, $matriz);
		}
		elseif( $acao == 'devolver' ) {
			EntradaNotaFiscalDevolver( $modulo, $sub, $acao, $registro, $matriz);
		}
		elseif( $acao == 'baixar' ) {
			EntradaNotaFiscalBaixar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( substr( $acao, 0, 6 ) == 'listar' ) {
			EntradaNotaFiscalListar( $modulo, $sub, $acao, $registro, $matriz );
		}
		else {
			EntradaNotaFiscalAdicionar( $modulo, $sub, 'adicionar', $registro, $matriz );
		}
	}
}

/**
 * Função de gerenciamento da tabela EntradaNotaFiscal
 *
 * @return unknown
 * @param array   $matriz
 * @param string  $tipo
 * @param string  $subTipo
 * @param unknown $condicao
 * @param unknown $ordem
 */
function dbEntradaNotaFiscal( $matriz, $tipo, $subTipo='', $condicao='', $ordem = '' ) {
	global $conn, $tb;
	$data = dataSistema();

	$bd = new BDIT();
	$bd->setConnection( $conn );
	$tabelas = $tb['EntradaNotaFiscal'];
	$campos  = array( 'id',		'idFornecedor',			 'idUsuario', 			'idPop', 			'dataEmissao',
	'dataLancamento',   'numNF', 			'lancarCP', 			'status' );
	$valores = array( 'NULL',	$matriz['idPessoaTipo'], $matriz['idUsuario'],	$matriz['idPop'],	$matriz['dataEmissao'],
	$data['dataBanco'], $matriz['numNF'], 'N', 	'P' );
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
		if( $subTipo == 'lancarCP' ) {
			$campos = array( 'lancarCP' );
			$valores = array( 'S' );
		}
		$retorno = $bd->alterar( $tabelas, $campos, $valores, $condicao );
	}

	if ( $tipo == 'consultar' ){
		if( $subTipo == 'completa' ) {
			$tabelas = "{$tb['EntradaNotaFiscal']}
						LEFT JOIN {$tb['PessoasTipos']} ON ({$tb['EntradaNotaFiscal']}.idFornecedor = {$tb['PessoasTipos']}.id) 
						LEFT JOIN {$tb['Pessoas']} ON ({$tb['PessoasTipos']}.idPessoa = {$tb['Pessoas']}.id ) 
						LEFT JOIN {$tb['POP']} ON ({$tb['EntradaNotaFiscal']}.idPop = {$tb['POP']}.id) 
						LEFT JOIN {$tb['Usuarios']} ON ({$tb['EntradaNotaFiscal']}.idUsuario = {$tb['Usuarios']}.id)
						LEFT JOIN {$tb['MovimentoEstoque']} ON ({$tb['EntradaNotaFiscal']}.id = {$tb['MovimentoEstoque']}.idNFE)";
			$campos  = array( "{$tb['EntradaNotaFiscal']}.*", "{$tb['Pessoas']}.nome as nomeFornecedor",
			"{$tb['POP']}.nome as pop", "{$tb['Usuarios']}.login as usuario", "{$tb['MovimentoEstoque']}.id as idMovimentoEstoque");
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
			$condicao[] = "{$tb['MovimentoEstoque']}.tipo='" . MovimentoEstoqueGetTipoEntrada() . "'";
		}

		$retorno = $bd->seleciona( $tabelas, $campos, $condicao, '', $ordem );
	}

	if( $tipo == 'excluir' ){
		$retorno = $bd->excluir( $tabelas, $condicao );
	}

	return ($retorno);
}

/**
 * Cadastra um nova Nota Fiscal de Fornecedor junto ao Movimento de Entrada de Estoque
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function EntradaNotaFiscalAdicionar( $modulo, $sub, $acao, $registro, $matriz ) {
	global $tb, $sessLogin, $sessCadastro;

	if( $matriz['bntConfirmar'] ) {
		if( $sessCadastro[$modulo.$sub.$acao] || EntradaNotaFiscalValida( $matriz ) ) {
			$matriz['usuario']		= $sessLogin['login'];
			$matriz['dataEmissao']	= converteData( $matriz['dataEmissao'], 'form', 'banco');
			$matriz['idUsuario']	= buscaIDUsuario( $sessLogin['login'], 'login','igual','login' );
			if( $sessCadastro[$modulo.$sub.$acao] || dbEntradaNotaFiscal( $matriz, 'inserir' ) ) { // se gravou a nota
				$registro = $matriz['idNFE'] = buscaUltimoID( $tb['EntradaNotaFiscal'] ); // busca a id desta nota
				MovimentoEstoqueEntradaNF( $modulo, $sub, $acao, $registro, $matriz ); // e realiza o movimento
			}
			else {
				avisoNOURL("Aviso", "Não foi possível gravar os dados! Verifique se todos os campos foram preenchidos corretamente.", 400);
				echo "<br />";
			}
			
		}
		else {
			avisoNOURL("Aviso", "Não foi possível gravar os dados! Verifique se todos os campos foram preenchidos corretamente, <br />
			           ou se esta nota já foi cadastrada.", 400);
			echo "<br />";
			EntradaNotaFiscalFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
	else {
		unset( $sessCadastro[$modulo.$sub.$acao] );
		EntradaNotaFiscalFormulario( $modulo, $sub, $acao, $registro, $matriz );
	}
}

/**
 * Exibe a listagem de Notas Fiscais de fornecedores
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function EntradaNotaFiscalListar( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda, $html, $sessLogin, $limite, $tb;

	$largura 				= array('30%',			'21%',	'10%', 			'9%',			'10%',		'20%' );
	$gravata['cabecalho']   = array('Fornecedor', 	'POP', 	'Dt Emissão',	'N&ordm; N.F.',	'Status', 	'Opções');
	$gravata['alinhamento'] = array('left', 		'left', 'center',	    'right',		'center',	'center');

	$qtdColunas = count( $largura );
	novaTabela("[Listagem de Notas Fiscais de Fornecedor]", 'center', '100%', 0, 2, 1, $corFundo, $corBorda, $qtdColunas );

	menuOpcAdicional( $modulo, $sub, $acao, $registro, $matriz, $qtdColunas);
	// define os tipos de filtros da listagem
	if( $acao == 'procurar' ) {
		$condicao = "{$tb['Pessoas']}.nome LIKE '%{$matriz['nome']}%'";
	}
	elseif( $acao == 'listar_pendentes' ) {
		$condicao = "{$tb['EntradaNotaFiscal']}.status = 'P'";
	}
	elseif( $acao == 'listar_baixados' ) {
		$condicao = "{$tb['EntradaNotaFiscal']}.status = 'B'";
	}
	elseif( $acao == 'listar_cancelados' ) {
		$condicao = "{$tb['EntradaNotaFiscal']}.status = 'C'";
	}
	else {
		$condicao = '';
	}
	// realiza a consulta
	$entradaNF = dbEntradaNotaFiscal( "", "consultar", "completa", $condicao, "{$tb['EntradaNotaFiscal']}.dataEmissao DESC" );
	$totalEntradaNF = count($entradaNF);
	if( $totalEntradaNF ){
		paginador( '', $totalEntradaNF, $limite['lista']['entradaNF'], $registro, 'normal10', $qtdColunas, '' , "registro", $acao );

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

		$limite = $j + $limite['lista']['entradaNF'];

		while( ( $j < $totalEntradaNF ) && ( $j < $limite ) ) {

			$default = '<a style="font-size:11px" href="?modulo=' . $modulo . '&sub=' . $sub . '&registro=' . $entradaNF[$j]->id;

			$opcoes = '';
			// realiza as opções conforme os status e se já foi lançados em contas a pagar
			if( $entradaNF[$j]->status == 'P' ) {
				$opcoes.= htmlMontaOpcao( $default . "&matriz[idMovimentoEstoque]=" . $entradaNF[$j]->idMovimentoEstoque . 
											"&acao=baixar\">Lançar em Estoque</a>", 'baixar' );
				$opcoes.= htmlMontaOpcao( $default . "&acao=adicionar_item&matriz[idNFE]=" . $entradaNF[$j]->id . 
							"&matriz[idMovimentoEstoque]=" . $entradaNF[$j]->idMovimentoEstoque . "\">Ver</a>", 'ver' );
				if( $entradaNF[$j]->lancarCP == 'N' ) {
					$opcoes .= htmlMontaOpcao( $default . "&acao=cancelar&matriz[idMovimentoEstoque]="
								 . $entradaNF[$j]->idMovimentoEstoque . "\">Cancelar</a>", 'cancelar' );
				}
			}
			else {
				$opcoes .= htmlMontaOpcao( $default . "&acao=ver&matriz[idNFE]=" . $entradaNF[$j]->id . 
							"&matriz[idMovimentoEstoque]=" . $entradaNF[$j]->idMovimentoEstoque . "\">Ver</a>", 'ver' );
				if( $entradaNF[$j]->status == 'B' ) {
					if( EntradaNotaFiscalVerificaDevolucao( $entradaNF[$j]->id ) ) {
						$opcoes .= htmlMontaOpcao( $default . "&acao=devolver&matriz[idNFE]=" . $entradaNF[$j]->id . 
								"&matriz[idMovimentoEstoque]=" . $entradaNF[$j]->idMovimentoEstoque . "\">Devolver</a>", 'sincronizar' );
					}		
				}
			}
			if( $entradaNF[$j]->lancarCP == 'N' && $entradaNF[$j]->status != 'C' ) {
				$opcoes .= htmlMontaOpcao( $default . "&matriz[idMovimentoEstoque]=" . $entradaNF[$j]->idMovimentoEstoque . 
						"&acao=lancar_contaPagar&matriz[idNFE]=" . $entradaNF[$j]->id ."\">Lançar Contas à Pagar</a>", 'desconto' );	
			}
			
			$i = 0;
			htmlAbreLinha( $corFundo );
				itemLinhaTMNOURL( $entradaNF[$j]->nomeFornecedor ,	$gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( $entradaNF[$j]->pop ,	$gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( converteData( $entradaNF[$j]->dataEmissao, 'banco', 'formdata' ), $gravata['alinhamento'][$i],'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( $entradaNF[$j]->numNF,	$gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( formSelectStatus( $entradaNF[$j]->status, 'status', 'check_pbc'),	$gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
				itemLinhaTMNOURL( $opcoes , $gravata['alinhamento'][$i], 'middle', $largura[$i++], $corFundo, 0, 'normal10' );
			htmlFechaLinha();
			$j++;
		}
	}
	else {
		htmlAbreLinha($corFundo);
		itemLinhaTMNOURL( '<span class="txtaviso"><i>Nenhuma Nota Fiscal de Fornecedor encontrada!</i></span>', 'center', 'middle', $largura[$i], $corFundo, $qtdColunas, 'normal10' );
		htmlFechaLinha();
	}
	fechaTabela();
}

/**
 * Procura Notas Fiscais de Entradas de Fornecedore
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function EntradaNotaFiscalProcurar( $modulo, $sub, $acao, $registro, $matriz ) {
	
	if( !$matriz['bntProcurar'] ){
		$matriz['nome'] = '';
	}
	getFormProcurar( $modulo, $sub, $acao, $matriz, "Entrada Nota Fiscal por Fornecedor" );
	
	if( $matriz['nome'] && $matriz['bntProcurar'] ){
		EntradaNotaFiscalListar( $modulo, $sub, $acao, $registro, $matriz );
	}
}

/**
 * Exibe o formulário de Entrada de Nota Fiscal do Fornecedor
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function EntradaNotaFiscalFormulario( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda, $html;

	novaTabela2( '['.( $acao == 'alterar' ? 'Alterar' : 'Nova' ).' Entrada via Nota Fiscal de Fornecedor]<a name="ancora"></a>',
	'center', '100%', 0, 2, 1, $corFundo, $corBorda, 2 );
	abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );
	getCampo('', '', '', '&nbsp;');
	#primeiro busca o fornecedor
	if ( !$matriz['idPessoaTipo'] && $acao == 'adicionar' ){
		$tipoPessoa 		  = checkTipoPessoa( 'for' );
		$matriz['idTipoPessoa'] = $tipoPessoa['id'];
		procurarPessoasSelect( $modulo, $sub, $acao, $registro, $matriz );
	}
	else {
		$c = 8; // indice de contagens
		//campos ocultos
		$dadosPessoa = dadosPessoasTipos( $matriz['idPessoaTipo'] );
		$matriz['nomeFornecedor'] = $dadosPessoa['pessoa']['nome'];
		$ocultosNomes = array( 'matriz[idPessoaTipo]', 'matriz[nomeFornecedor]', 'matriz[nomePop]' );
		$ocultosValores = array( $matriz['idPessoaTipo'], $matriz['nomeFornecedor'], $matriz['nomePop'] );
		getCamposOcultos( $ocultosNomes, $ocultosValores );
		//nome do Fornecedor
		getCampo( 'combo', 'Fornecedor', '', $matriz['nomeFornecedor'] );
		// pop
		getCampo( 'combo',	'POP', '', formSelectPOP( $matriz['idPop'], "idPop", 'form', '', 'onblur="recolheNomeOpcaoSelect(7, 6)"') );
		// data da emissão da nota
		getCampo( 'text', 	'Data Emissão',	'matriz[dataEmissao]', $matriz['dataEmissao'],
		"onblur=\"verificaData(this.value,".$c++.")\"",'', 10 );
		// numero da NF
		getCampo( 'text',	'N&ordm; Nota Fiscal', 		'matriz[numNF]', $matriz['numNF'], "onkeydown=\"retornaInteiro(this.value,".$c++.")\"", '', 10 );
		// confirmar
		getBotao( 'matriz[bntConfirmar]', 			'Confirmar' );
		fechaFormulario();
	}
	fechaTabela();
}

/**
 * Valida se os dados foram preenchidos corretamente
 *
 * @param array $matriz
 * @return boolean
 */
function EntradaNotaFiscalValida( $matriz ) {
	$retorno = true;
	if( !validaData( $matriz['dataEmissao'] ) ) {
		$retorno = false;
	}
	if( empty( $matriz['numNF'] ) ) {
		$retorno = false;
	}
	//verifica se este nota já não existe neste mesmo pop
	if( $matriz['numNF'] || $matriz['idPessoaTipo'] ) {
		$consulta = dbEntradaNotaFiscal( '', 'consultar', '', "numNF='" . intval( $matriz['numNF'] ) .
		"' AND idFornecedor='" . $matriz['idPessoaTipo']."'" );
		if( !verificaRegistroDuplicado( $consulta, $acao ) ) {
			$retorno = false;
		}
	}
	else{
		$retorno = false;
	}
	return $retorno;
}

/**
 * Visualiza os dados da Nota Fiscal de fornecedor
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function EntradaNotaFiscalVer( $modulo, $sub, $acao, $registro, $matriz ) {
	global $corFundo, $corBorda, $html, $tb;

	$NF = dbEntradaNotaFiscal( '', "consultar", 'completa', "{$tb['EntradaNotaFiscal']}.id='" . $registro . "'" );
	if( count( $NF ) > 0 ) {
		$dadosNF = get_object_vars( $NF[0] );
		$dadosNF['idPessoaTipo'] = $dadosNF['idFornecedor'];
		$Acao = ucfirst( $acao );
		novaTabela2("[" . $Acao . " Nota Fiscal do Fornecedor]" . "<a name=\"ancora\"></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		if ($dadosNF['status'] == 'P' ){
			menuOpcAdicional( $modulo, $sub, 'ver', $registro, $matriz, 2 );
		}
		else {
			getCampo('', '', '', '&nbsp;');
		}
		getCampo( 'combo', _('Fornecedor'), 				'',	$dadosNF['nomeFornecedor'] );
		getCampo( 'combo', _('Usuário'),					'',	$dadosNF['usuario'] );
		getCampo( 'combo', _('POP'), 						'',	$dadosNF['pop'] );
		getCampo( 'combo', _('Data de Emissão'), 			'',	converteData( $dadosNF['dataEmissao'], 'banco', 'formdata' ) );
		getCampo( 'combo', _('N&ordm; Nota Fiscal'), 		'',	$dadosNF['numNF'] );
		getCampo( 'combo', _("Lançada em Contas à Pagar"), 	"",	formSelectStatusAtivoInativo($dadosNF['lancarCP'], 'lancarCP', 'sim_nao') );
		getCampo( 'combo', _("Status"), 					"",	formSelectStatus( $dadosNF['status'], 'status', 'check_pbc' ) );
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
		avisoNOURL("Erro", "Não foi possível localizar a Nota Fiscal!", 400);
		echo "<br />";
		EntradaNotaFiscalListar( $modulo, $sub, 'listar', '', $matriz );
	}

}

/**
 * Apenas exibe os dados da Nota Fiscal do fornecedor
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function EntradaNotaFiscalVisualizar( $modulo, $sub, $acao, $registro, $matriz ) {
	EntradaNotaFiscalVer( $modulo, $sub, $acao, $registro, $matriz );
	itensMovimentoEstoqueNFListagem( $modulo, $sub, $acao, $registro, $matriz );
}

/**
 * Cancela uma Nota Fiscal de Fornecedor
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function EntradaNotaFiscalCancelar( $modulo, $sub, $acao, $registro, $matriz ) {
	if( $matriz['bntConfirmar'] ) {
		$matriz = array( 'status' => 'C' ); 
	 	$msg = ( dbEntradaNotaFiscal( $matriz, 'alterar', 'status', 'id='.$registro ) 
	 			? 'Nota Fiscal de Fornecedor cancelada com sucesso!' 
	 			: 'Não foi possível cancelar a Nota Fiscal de Fornecedor.' );
		avisoNOURL("Aviso", $msg, 400);
		echo "<br />";
		EntradaNotaFiscalListar( $modulo, $sub, 'listar', '', $matriz );	
	}
	else{
		EntradaNotaFiscalVisualizar( $modulo, $sub, $acao, $registro, $matriz );
	}
}

/**
 * Realiza a baixa de Nota de Fiscal de Fornecedor, exibindo a tela confirmação
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function EntradaNotaFiscalBaixar( $modulo, $sub, $acao, $registro, $matriz ) {
	global $sessCadastro;
	
	if( isset( $matriz['bntConfirmar'] ) && $matriz['bntConfirmar'] == 'Baixar' ) {
		if( $sessCadastro[$modulo.$sub.$acao] || EntradaNotaFiscalDarBaixa( $modulo, $sub, $acao, $registro, $matriz ) ) {
			$rs = dbEntradaNotaFiscal( '', 'consultar', '', 'id='.$registro ); // verifica se já não tem contas a pagar
			$msg = 'Entrada de Produtos por Nota Fiscal de Fornecedor efetuada com sucesso!';
			avisoNOURL( "Aviso", $msg, 400 );
			echo "<br />";
			if( count( $rs ) && $rs[0]->lancarCP == 'N' ) { // se nao tem contas a pagar, aparece o formulario de lançamento de CP
				$dados['idNFE']=$registro;
				contasAPagarNFEAdicionar( $modulo, $sub, 'lancar_contaPagar', $registro, $dados );
			}
			else { // senao ele nista as NF de fornecedores
				EntradaNotaFiscalListar( $modulo, $sub, 'listar', '', $matriz );				
			}
		}
		else {
			$msg = 'Não foi possível realizar a Entrada de Produtos pela Nota Fiscal de Fornecedor.';
			avisoNOURL( "Aviso", $msg, 400 );
			echo "<br />";
			EntradaNotaFiscalListar( $modulo, $sub, 'listar', '', $matriz );
		}
	}
	else{
		unset( $sessCadastro[$modulo.$sub.$acao] );
		EntradaNotaFiscalVisualizar( $modulo, $sub, $acao, $registro, $matriz );
	}
}

/**
 * Realiza as ações de backend de baixa da nota fiscal, dando a entrada de produtos no estoque
 *
 * @param integer $registro
 * @param array $matriz
 * @return boolean
 */
function EntradaNotaFiscalDarBaixa( $modulo, $sub, $acao, $registro, $matriz ) {
	global $tb, $sessCadastro;
	
	$retorno = true;
	$nFE = dbEntradaNotaFiscal( '', 'consultar', 'completa', $tb['EntradaNotaFiscal'].'.id='.$registro );
	// realiza a entrana dos itens no Estoque
	if( MovimentoEstoqueEntrada( $nFE[0]->idMovimentoEstoque, $nFE[0]->idPop, false ) ) {
		$status['status'] = 'B';
		$retorno = dbEntradaNotaFiscal( $status, 'alterar', 'status', 'id='.$registro );
		$sessCadastro[$modulo.$sub.$acao] = "gravado";
	}
	else{
		$retorno = false;
	}

	return $retorno;
}

/**
 * Realiza a devolução de itens de nota fiscal de fornecedor, gerando ou modificando o movimento de saída
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function EntradaNotaFiscalDevolver( $modulo, $sub, $acao, $registro, $matriz ){
	global $sessCadastro, $tb;
	
	if( $matriz['bntDevolver'] ) {
		$devolucao = EntradaNotaFiscalValidaDevolucao( $registro, $matriz ); //função que verifica quais itens foram selecionados para devolução
		$devolucao['idPop'] = $matriz['idPop'];
		if( $sessCadastro[$modulo.$sub.$acao] || $devolucao ) {
			if( !$matriz['totalS'] ) {  // se a nota fiscal ainda não tem movimento de sáida insere o movimento de saida
				$dados['idNFE'] 		 = $registro;
				$dados['idOrdemServico'] = 0;
				$dados['idRequisicao'] 	 = 0;
				$dados['tipo'] 			 = 'S';
				$dados['descricao']		 = "Saída de produtos via devolução de Nota Fiscal de n&ordm; ".$registro ."."; 
				
				//insere um novo movimento
				if( $sessCadastro[$modulo.$sub.$acao] || dbMovimentoEstoque( $dados, 'inserir' ) ) {
					$devolucao['idMovimentoEstoque'] = buscaUltimoID( $tb['MovimentoEstoque'] ); // busca o id do movimento					
					//$devolvido = itensMovimentoEstoqueNFDevolver( $registro, $devolucao ); //função para registrar os itens que deverão ser devolvidos
					if( $sessCadastro[$modulo.$sub.$acao] || itensMovimentoEstoqueNFDevolver( $registro, $devolucao )){
						$devolvido = true;	
					}
				}
			}
			else { // se já tiver movimento de saída verifica o id e chama função para registrar os itens que deverão ser devolvidos
				$devolucao['idMovimentoEstoque'] = $matriz['idMovimentoEstoque'];
				//$devolvido = itensMovimentoEstoqueNFDevolver( $registro, $devolucao );
				if( $sessCadastro[$modulo.$sub.$acao] || itensMovimentoEstoqueNFDevolver( $registro, $devolucao )){
					$devolvido = true;	
				}
			}
			$sessCadastro[$modulo.$sub.$acao] = "gravado";
			if( $devolvido ) {
				//verifica se a nota fiscal possui parcelas pendentes no contas a pagar
				$condicao = "{$tb['ContasAPagar']}.idNFE = $registro AND {$tb['ContasAPagar']}.status = 'P'";
				$consulta = dbContasAPagar( '', 'consultar', '', $condicao, 'dtVencimento' );
				if(count($consulta)){ //se tiver chama função que exibe o aviso e lista as contas referentes a nota fiscal
					contasAPagarNFECancelar( $modulo, $sub, 'cancelar_contaPagar', $registro, $consulta );
					
				}
				else { //se não tiver contas pendentes exibe a listagem de notas fiscais de fornecedor e finaliza a rotina de devolução
					$msg = 'Devolução de itens da Nota Fiscal realizada com sucesso!';
					avisoNOURL("Aviso", $msg, 400);
					echo "<br />";
					EntradaNotaFiscalListar( $modulo, $sub, 'listar', '', $matriz );
				}
			}
			else {
				$msg ='Não foi possível gravar os dados!';
				avisoNOURL("Aviso", $msg, 400);
				echo "<br />";
				EntradaNotaFiscalListar( $modulo, $sub, 'listar', '', $matriz );
			}			
		}
		else {
			avisoNOURL("Aviso", "Não foi possível gravar os dados! Verifique se todos os campos foram preenchidos corretamente.", 410);
			echo "<br />";
			itensMovimentoEstoqueFormNFDevolver( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
	else {
		unset( $sessCadastro[$modulo.$sub.$acao] );
		//exibe o formulário para verificação de tipo de devolução e listagem dos itens
		itensMovimentoEstoqueFormNFDevolver( $modulo, $sub, $acao, $registro, $matriz );
	}
}

/**
 * Conclui o lançamento de contas a pagar marcando como a contas a pagar
 * e não permitindo mais o lançamento de qualquer outra conta.
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function EntradaNotaFiscalFinalizaLancarCP( $modulo, $sub, $acao, $registro, $matriz ) {
	if( isset( $matriz['bntConcluir'] ) && $matriz['bntConcluir'] == 'Concluir' ) {
		if( dbEntradaNotaFiscal( '', 'alterar', 'lancarCP', 'id='.$registro ) ) {
			$msg = 'Lançamento de Contas à Pagar de Nota Fiscal de Fornecedor efetuada com sucesso!';
			avisoNOURL( "Aviso", $msg, 400 );
			echo "<br />";
			EntradaNotaFiscalListar( $modulo, $sub, 'listar', '', $matriz );
		}
		else {
			$msg =	'Ocorreu um erro ao concluir o lançamento de Contas à Pagar de Nota Fiscal de Fornecedor!<br />';
					'Verifique se os dados foram preenchidos corretamente';
			avisoNOURL( "Aviso", $msg, 400 );
			echo "<br />";
			contasAPagarNFEParcelas( $modulo, $sub, 'incluir_parcelas', $registro, $matriz );
		}
	}
	else{
		contasAPagarNFEParcelas( $modulo, $sub, 'incluir_parcelas', $registro, $matriz );
	}
}

/**
 * Verifica se será exibida a opção de devolução na listagem das notas
 * se o total de itens de entrada for igual ao total de itens de saída
 * e se as quantidades de itens correspondentes forem iguais não será exibida a opção de Devolução
 *
 * @param integer $idNFE
 * @return boolean
 */
function EntradaNotaFiscalVerificaDevolucao( $idNFE ) {

	$retorno = true;
	
	$consulta = EntradaNotaFiscalConsultaDevolucao( $idNFE );
	$totalS = count($consulta['saida']);	//total de itens do movimento de saída da nota fiscal
	$totalE = count($consulta['entrada']); // total de itens do movimento de entrada da nota fiscal
	if( $totalS == $totalE ) { // se a total for igual verifica a quantidade dos itens correspondentes
		$j = 0;
		for( $i=0; $i<$totalE; $i++ ) {
			for( $l=0; $l<$totalS; $l++ ) {
				if( ($consulta['entrada'][$i]->idProduto == $consulta['saida'][$l]->idProduto) &&
					( floatval($consulta['entrada'][$i]->qtde) == floatval($consulta['saida'][$l]->qtde) ) ){
					$j++;
				}
			}
		}
		if( $totalE == $j ) { //total de itens iguais e quantidade equivalentes iguais
			$retorno = false;		//não exibe a opção
		}		
	}
	return $retorno;
}

/**
 * Consulta se a nota fiscal de fornecedor possui movimento de entrada e saída e grava numa array
 *
 * @param integer $idNFE
 * @return array
 */
function EntradaNotaFiscalConsultaDevolucao( $idNFE ) {
	global $tb;
	
	$retorno = array();
	
	//Verifica os dados de movimento de saída da nota fiscal de fornecedor
	$condicaoS = "{$tb['MovimentoEstoque']}.idNFE = $idNFE AND {$tb['MovimentoEstoque']}.tipo = 'S'";
	$consultaS = dbMovimentoEstoque('', 'consultar', 'itens', $condicaoS, "{$tb['ItensMovimentoEstoque']}.idProduto ASC" );
	$retorno['saida'] = $consultaS;
	
	//Verifica os dados de movimento de entrada da nota fiscal de fornecedor
	$condicaoE = "{$tb['MovimentoEstoque']}.idNFE = $idNFE AND {$tb['MovimentoEstoque']}.tipo = 'E'"; 
	$consultaE = dbMovimentoEstoque('', 'consultar', 'itens', $condicaoE, "{$tb['ItensMovimentoEstoque']}.idProduto ASC" );
	$retorno['entrada'] = $consultaE;
	
	return $retorno;
}

/**
 * Verifica quais produtos listados foram selecionados e grava numa array
 *
 * @param integer $registro
 * @param array $matriz
 * @return array
 */
function EntradaNotaFiscalValidaDevolucao( $registro, $matriz ) {
	global $tb; 
	
	$selecao = array();
	$i = 0;
	if( $matriz['tipo'] == 'T' ){
		$consulta = EntradaNotaFiscalConsultaDevolucao( $registro );
		$totalE = count($consulta['entrada']); //verifica a quantidade de itens do movimento de entrada da nota fiscal
		while( $i < $totalE ) {
			$selecao['idProduto'][$i]  = $consulta['entrada'][$i]->idProduto;
			$selecao['quantidade'][$i] = $consulta['entrada'][$i]->qtde;
			$i++;
		}
	}
	else {
		for( $j=0; $j < $matriz['totalE']; $j++ ) { //laço para verificar quais dos produtos listados foram selecionados
			if( $matriz['id'.$j] ) { // se o produto foi selecionado grava os dados do produto na matriz
				$selecao['idProduto'][$i] = $matriz['id'.$j   ];
				$selecao['quantidade'][$i] = $matriz['qtde'.$j];		
				$i++;
			}
		}
	}
	$selecao['registros'] = $i; //grava a quantidade de itens que foram selecionados
	return $selecao;
}
?>