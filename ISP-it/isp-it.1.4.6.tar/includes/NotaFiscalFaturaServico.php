<?
################################################################################
#       Criado por: Lis
#  Data de criação: 28/05/2007
# Ultima alteração: 28/05/2007
#    Alteração No.: 1
#
# Função:
#    Nota Fiscal Fatura Servico - Funções para gerenciamento de cadastro de Nota Fiscal Fatura de Serviço
################################################################################

/**
 * Construtor de módulo Nota Fiscal Fatura de Serviço
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function NotaFiscalServico( $modulo, $sub, $acao, $registro, $matriz ) {

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
		$titulo    = "<b>Nota Fiscal Fatura de Serviço</b>";
		$subtitulo = "Cadastro de Nota Fiscal Fatura de Serviço de Comunicação";
		$itens  = Array( 'Novo', 'Procurar', 'Listar' );
		getHomeModulo( $modulo, $sub, $titulo, $subtitulo, $itens );
		echo "<br />";

		if( $acao == 'adicionar' || $acao == 'novo' ) {
			NotaFiscalServicoAdicionar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( strstr( $acao, '_itens' ) || strstr( $acao, '_item' ) ) {
			ItensNFServico( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( $acao == 'procurar' ) {
			NotaFiscalServicoProcurar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( $acao == 'ver' ) {
			NotaFiscalServicoVisualizar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif( $acao == 'cancelar' ) {
			NotaFiscalServicoCancelar( $modulo, $sub, $acao, $registro, $matriz);
   	    }
   	    elseif( $acao == 'excluir' ) {
			NotaFiscalServicoExcluir( $modulo, $sub, $acao, $registro, $matriz);
   	    }
   	    elseif ( $acao == "imprimir" ){
			if ( $matriz['print'] == false )
				NotaFiscalServicoImprimir( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif ($acao == "alterarDtEmissao"){
			AlterarDadosNFS($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif( substr( $acao, 0, 6 ) == 'listar' ) {
			NotaFiscalServicoListar( $modulo, $sub, $acao, $registro, $matriz );
		}
	}
}

/**
 * Função de gerenciamento da tabela Nota Fiscal Fatura de Serviço
 *
 * @return boolean
 * @param array   $matriz
 * @param string  $tipo
 */
function dbNotaFiscalServico( $matriz, $tipo ) {
	global $conn, $tb;
	$objNFS = new NotaFiscalServico( $conn );	

	$objNFS->setId( $matriz['id'] );
	$objNFS->setIdPessoaTipo( $matriz['idPessoaTipo'] );
	
	if ( $tipo == 'incluir' ){
		$dados = dadosCliente( $matriz['idPessoaTipo'], $matriz['tipoEndereco']);
		
		$objNFS->razao = ( $dados['pessoas']['razao']   != '' ? $razao = $dados['pessoas']['razao']   : $razao = $dados['pessoas']['nome'] );
		$objNFS->cnpj  = ( $dados['documentos']['cnpj'] != '' ? $cnpj  = $dados['documentos']['cnpj'] : $cnpj  = $dados['documentos']['cpf']);
		$data = dataSistema();
		$objNFS->dtEmissao = $data['dataBanco'];
		$objNFS->endereco = $dados['enderecos']['enderecoCompleto'];
		$objNFS->cep = $dados['enderecos']['cep'];
		$objNFS->cidade = $dados['enderecos']['cidade'];
		$objNFS->uf = $dados['enderecos']['uf'];
		$objNFS->inscrEst = ( $dados['documentos']['ie'] != '' ? $ie = $dados['documentos']['ie'] : $ie = $dados['documentos']['rg'] );
		$objNFS->obs = $matriz['obs'];
		$objNFS->status = "A";
		$objNFS->idNatPrestacao = $matriz['idNatPrestacao'];;
		$objNFS->ICMS = $matriz['ICMS'];
		$objNFS->idPop = $matriz['idPop'];
		$objNFS->dtPrestacao = $matriz['dtPrestacao'];
	
		return $objNFS->gravarNota();
	}
	elseif ( $tipo == 'excluir' ){
		$objItensNFS = new ItensNFServico();
		$objItensNFS->ItensNFServico();
		$objItensNFS->setConnection( $conn );
		$objItensNFS->setIdNFS( $matriz['id'] );
		$grava = $objItensNFS->excluiRelacionamento( $objItensNFS->tabela, array( "idNFS = '".$objItensNFS->getIdNFS()."'" ) );
		if ( $grava ){
			return $objNFS->exclui();
		}
	}
	elseif ( $tipo == 'cancelar' ){
		return $objNFS->gravaStatus( $objNFS->getId(), 'C' );	
	}
	elseif ( $tipo == 'imprimirOk'){
		return  $objNFS->gravaStatus( $objNFS->getId(), 'I' );
	}
	elseif ( $tipo == 'imprimir' ){
		
		//faz atualizacao dos dados do cliente no cadastro da nota fiscal e da data de impresao (para contemplar alteracoes de cadastro entre o inclusao da nota e sua impressao )
		$where = array( "id ='".$objNFS->getId()."'" );
		$dadosNota = $objNFS->seleciona('','', $where );
		$dados = dadosCliente( $dadosNota[0]->idPessoaTipo );
		
		//seta o numero da nf somente agora, momento em que se imprime as notas.
		$objNFS->idPop = $dadosNota[0]->idPop;
		if( !$dadosNota[0]->numNF ){
			$objNFS->setNovoNumNFS();
		}
		else {
			$objNFS->numNF = $dadosNota[0]->numNF;
		}
			
		$objNFS->idPessoaTipo = $dadosNota[0]->idPessoaTipo;
		$objNFS->razao = ( $dados['pessoas']['razao']   != '' ? $razao = $dados['pessoas']['razao']   : $razao = $dados['pessoas']['nome'] );
		$objNFS->cnpj  = ( $dados['documentos']['cnpj'] != '' ? $cnpj  = $dados['documentos']['cnpj'] : $cnpj  = $dados['documentos']['cpf']);
		if ($matriz['emissao'] == 'manual'){
			$data['dataBanco'] = converteData($matriz['dtEmissao'],"form", "bancodata");
		}
		else {
			$data = dataSistema();
		}
		$objNFS->dtEmissao = $data['dataBanco'];
		
		$objNFS->endereco = $dadosNota[0]->endereco;
		$objNFS->cep = $dadosNota[0]->cep;
		$objNFS->cidade = $dadosNota[0]->cidade;
		$objNFS->uf = $dadosNota[0]->uf;
		$objNFS->inscrEst = ( $dados['documentos']['ie'] != '' ?  $dados['documentos']['ie'] : $dados['documentos']['rg'] );
		$objNFS->obs = $matriz['obs'];
		$objNFS->status = "A";
		$objNFS->idNatPrestacao = $dadosNota[0]->idNatPrestacao;
		$objNFS->dtPrestacao = $matriz['dtPrestacao'];
		$objNFS->ICMS = $dadosNota[0]->ICMS;
			
		$objNFS->gravarNota();		

	}
	elseif ( substr( $tipo, 0, 6) == 'listar' ){
		$tab = array( $tb['NotaFiscalServico'], $tb['Pessoas'], $tb['PessoasTipos'] );
		$fields = array( "$tb[NotaFiscalServico].id", "$tb[NotaFiscalServico].idPessoaTipo", "$tb[NotaFiscalServico].numNF", "IF($tb[Pessoas].razao != '',$tb[Pessoas].razao, $tb[Pessoas].nome) cliente, $tb[NotaFiscalServico].status status" );
		
		if ($tipo == "listar" )
			$where = array( "$tb[NotaFiscalServico].idPessoaTipo = $tb[PessoasTipos].id", "$tb[PessoasTipos].idPessoa = $tb[Pessoas].id", "$tb[NotaFiscalServico].status = 'A'");
		elseif ( $tipo == "listarImpressa" )
			$where = array( "$tb[NotaFiscalServico].idPessoaTipo = $tb[PessoasTipos].id", "$tb[PessoasTipos].idPessoa = $tb[Pessoas].id", "$tb[NotaFiscalServico].status = 'I'");
		elseif ( $tipo == "listarCancelada")
			$where = array( "$tb[NotaFiscalServico].idPessoaTipo = $tb[PessoasTipos].id", "$tb[PessoasTipos].idPessoa = $tb[Pessoas].id", "$tb[NotaFiscalServico].status = 'C'");
		elseif ( $tipo == "listarTodas")
			$where = array( "$tb[NotaFiscalServico].idPessoaTipo = $tb[PessoasTipos].id", "$tb[PessoasTipos].idPessoa = $tb[Pessoas].id");

		return $objNFS->seleciona($tab, $fields, $where, '', array( "numNF" ) );
	}
	elseif ($tipo == 'procurar'){
		$matriz['dtInicial'] = formatarData( $matriz['dtInicial'] );
		$matriz['dtFinal'] = formatarData( $matriz['dtFinal'] );
		if( !empty($matriz['dtInicial']) ){
			$dtInicial = substr($matriz['dtInicial'],2,4)."-".substr($matriz['dtInicial'],0,2).'-01 00:00:00';
		}
		if( !empty($matriz['dtFinal']) ){
			$dtFinal = substr($matriz['dtFinal'],2,4)."-".substr($matriz['dtFinal'],0,2).'-'.dataDiasMes(substr($matriz['dtFinal'],0,2))." 23:59:59";	
		}
		
		$fields = array( "$tb[NotaFiscalServico].id", "$tb[NotaFiscalServico].idPessoaTipo", "$tb[NotaFiscalServico].numNF", "IF($tb[Pessoas].razao != '',$tb[Pessoas].razao, $tb[Pessoas].nome) cliente, $tb[NotaFiscalServico].status status" );
		$tab = array( $tb['NotaFiscalServico'], $tb['Pessoas'], $tb['PessoasTipos'] );
		$where = array( "$tb[NotaFiscalServico].idPessoaTipo = $tb[PessoasTipos].id", "$tb[PessoasTipos].idPessoa = $tb[Pessoas].id");
		if( !empty($dtInicial) && !empty($dtFinal) ){
			$where[] = "$tb[NotaFiscalServico].dtEmissao BETWEEN '$dtInicial'  AND  '$dtFinal' ";
		}
		elseif( !empty($dtInicial) ){
			$where[] = " $tb[NotaFiscalServico].dtEmissao >= '$dtInicial' ";
		}
		elseif( !empty($dtFinal) ){
			$where[] = " $tb[NotaFiscalServico].dtEmissao <= '$dtFinal'";
		}
		if( !empty($matriz['idPessoaTipo']) ){
			$where[] = " $tb[NotaFiscalServico].idPessoaTipo = $matriz[idPessoaTipo]";
		}
 					
		return $objNFS->seleciona($tab, $fields, $where, '', array( "numNF" ) );
	}
	elseif( $tipo == 'ver'){
		return $objNFS->seleciona();
	}
	elseif ( $tipo == 'custom' ){
		return $objNFS->seleciona('','', array( "id = ".$objNFS->getId() ) );	
	}
	elseif ( $tipo == 'lastId' ){
		return $objNFS->seleciona('', array( "Max(id) id" ), array( "idPessoaTipo = ".$matriz['idPessoaTipo'] ) );
	}
	elseif ( $tipo == 'calcularNota'){
		return $objNFS->calculaTotalNota();
	}		
}

function NotaFiscalServicoAdicionar( $modulo, $sub, $acao, $registro, $matriz ) {
	global $tb, $sessCadastro;

	if( $matriz['bntConfirmar'] ) {
//		if( !$matriz['bntConfirmar2'] && getImpostoPessoa(getIdTipoImposto("ICMS"), $matriz['idPessoaTipo'] )) {
//			formNotaFiscalServicoDescontarICMS($modulo, $sub, $acao, $registro, $matriz);
//		}
//		elseif( $matriz['bntConfirmar2'] ){
//			if($matriz['recolherICMS'] == 'S' ){
//				$matriz['ICMS'] = getImpostoPessoa(getIdTipoImposto("ICMS"), $matriz['idPessoaTipo']);
//			}
//		}
//		elseif( !getImpostoPessoa(getIdTipoImposto("ICMS"), $matriz['idPessoaTipo'] )){	
//			$parametros = carregaParametrosConfig();
//			$matriz['ICMS'] = $parametros['icms_padrao'];
//		}
		if( $sessCadastro[$modulo.$sub.$acao] || dbNotaFiscalServico( $matriz, 'incluir' ) ) { // se gravou a nota
			$registro = $matriz['idNFS'] = buscaUltimoID( $tb['NotaFiscalServico'] ); // busca a id desta nota
			$sessCadastro[$modulo.$sub.$acao] = "gravado";
			NotaFiscalServicoVisualizar( $modulo, $sub, 'ver', $registro, $matriz );
		}
		else {
			avisoNOURL("Aviso", "Não foi possível gravar os dados! Verifique se todos os campos foram preenchidos corretamente.", 400);
			echo "<br />";
			NotaFiscalServicoFormulario( $modulo, $sub, $acao, $registro, $matriz );
		}			
	}
	else {
		unset( $sessCadastro[$modulo.$sub.$acao] );
		NotaFiscalServicoFormulario( $modulo, $sub, $acao, $registro, $matriz );
	}
}

function NotaFiscalServicoFormulario( $modulo, $sub, $acao, $registro, $matriz ){
	global $corFundo, $corBorda, $html;

	novaTabela2( '['.( $acao == 'alterar' ? 'Alterar' : 'Adicionar' ).' Nota Fiscal Fatura de Serviço]<a name="ancora"></a>',
	'center', '100%', 0, 2, 1, $corFundo, $corBorda, 2 );
		abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );
			getCampo('', '', '', '&nbsp;');
			#primeiro busca o fornecedor
			if ( !$matriz['idPessoaTipo'] && $acao == 'adicionar' ){
				$tipoPessoa 		    = checkTipoPessoa( 'cli' );
				$matriz['idTipoPessoa'] = $tipoPessoa['id'];
				procurarPessoasSelect( $modulo, $sub, $acao, $registro, $matriz );
			}
			else {
				$c = 8; // indice de contagens
				//campos ocultos
				$dadosPessoa = dadosPessoasTipos( $matriz['idPessoaTipo'] );
				$matriz['nomeCliente'] = $dadosPessoa['pessoa']['nome'];
				$ocultosNomes = array( 'matriz[idPessoaTipo]', 'matriz[nomeCliente]', 'matriz[nomePop]' );
				$ocultosValores = array( $matriz['idPessoaTipo'], $matriz['nomeCliente'], $matriz['nomePop'] );
				getCamposOcultos( $ocultosNomes, $ocultosValores );
				procurarPessoasSelect( $modulo, $sub, $acao, $registro, $matriz );
				$campo = formSelectTipoEndereco($matriz['tipoEndereco'], 'tipoEndereco','formnochange');
				$label = '<b>Endereço</b>';
				getCampo('combo', $label, '', $campo);
				getCampo( 'combo',	'POP', '', formSelectPOP( $matriz['idPop'], "idPop", 'form', '', 'onblur="recolheNomeOpcaoSelect(7, 6)"') );
				getCampo( 'combo',	'Natureza da Prestação', '', formSelectNatPrestacao( $matriz['idNatPrestacao'], "idNatPrestacao", 'form', '', 'onblur="recolheNomeOpcaoSelect(9, 8)"') );
				$ICMS = getImpostoPessoa(getIdTipoImposto("ICMS"), $matriz['idPessoaTipo']);
				$parametros = carregaParametrosConfig();
				$matriz['ICMS'] = ($ICMS ? $ICMS: $parametros['icms_padrao']);
				getCampo('text', 'ICMS', 'matriz[ICMS]', $matriz['ICMS'],'style="text-align:right"','',5);
				getBotao('matriz[bntConfirmar]', 'Confirmar' );
			}
		fechaFormulario();
	fechaTabela();
}

/**
 * Permite efetuar consulta de nota fiscal de fatura de serviço cadastrada
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function NotaFiscalServicoProcurar( $modulo, $sub, $acao, $registro, $matriz ) {
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $conn, $tb;
	
	$data=dataSistema();
	# Motrar tabela de busca
	novaTabela2("[Procurar]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );
			getCamposOcultos( 'matriz[bntConfirmar]', $matriz['bntConfirmar'] );
			if( $matriz['bntProcurar'] ){
				$registro['idPessoaTipo'] = '';
				$registro['dtInicial'] = '';
				$registro['dtFinal'] = '';
			}
						
			clienteProcurar($modulo, $sub, $acao, $registro, $matriz, 0);
			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<span class=bold10>Mês/Ano Inicial:</span><br>
				<span class=normal10>Informe o mês/ano inicial para consulta</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input id=\"ab\" type=text name=registro[dtInicial] size=7 value='$registro[dtInicial]' onBlur=verificaDataMesAno2(this.value,\"ab\")>&nbsp;<span class=txtaviso>(Formato: $data[mes]/$data[ano])</span>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<span class=bold10>Mês/Ano Final:</span><br>
				<span class=normal10>Informe o mês/ano final para consulta</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input id=\"cd\" type=text name=registro[dtFinal] size=7 value='$registro[dtFinal]'  onBlur=verificaDataMesAno2(this.value,\"cd\")>&nbsp;<span class=txtaviso>(Formato: $data[mes]/$data[ano])</span>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
					
			#botao
			getBotao( 'matriz[bntEnviar]', 'Enviar' );
		
			#somente para ser compativel com o clienteProcurar() existente
			if( ($registro['idPessoaTipo'] == '' || $registro['idPessoaTipo'] != $matriz['idPessoaTipo']) && (!$matriz['bntProcurar']) ){
				getCamposOcultos( 'registro[idPessoaTipo]', $matriz['idPessoaTipo'] );
				$registro['idPessoaTipo'] = $matriz['idPessoaTipo'];
			}
			#para salvar o id do usuario mesmo quando ele for vizualizar os detalhes.
			elseif( (!empty($matriz['txtProcurar']) && $matriz['idPessoaTipo'] == $registro['idPessoaTipo'] ) && (!$matriz['bntProcurar']) ){
				getCamposOcultos( 'registro[idPessoaTipo]', $registro['idPessoaTipo'] );
			}
			#para exibir o selectbox ao retornar dos detalhes do cliente.
			if( $matriz['bntSelecionar'] ){
				getCamposOcultos( 'matriz[txtProcurar]', $matriz['txtProcurar'] );
				getCamposOcultos( 'matriz[idPessoaTipo]', $matriz['idPessoaTipo'] );
			}
			if( (!empty($registro['idPessoaTipo']) ) || ( !empty($registro['dtInicial']) || !empty($registro['dtFinal']))){
				NotaFiscalServicoListar($modulo, $sub, $acao, $registro, $matriz);
			}
		fechaFormulario();
	fechaTabela();
}

/**
 * Exibe a listagem das Notas Fiscais de Fatura de Serviço
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function NotaFiscalServicoListar( $modulo, $sub, $acao, $registro, $matriz ) {
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $limite;
	
	$largura =     array( "6%",   	"40%" , 	"10%", 		"14%", 		"30%");
	$alinhamento = array( "center", "left", 	"left", 	"left", 	"left" );
	$gravata =     array( "N° NF", 	"Cliente", 	"Valor",	"Status", 	"Opções" );
	$corGravata = 'tabfundo0';
	$corDetalhe = 'tabfundo1';
	
	# Motrar tabela de busca
	novaTabela2("[Listar]<a name=ancora></a>", "center", '100%', 1, 2, 1, $corFundo, $corBorda, 5);

		novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=matriz[bntConfirmar] value=$matriz[bntConfirmar]>";
				$texto .= menuOpcAdicional( $modulo, $sub, $acao, $registro, $matriz, 5 );
			itemLinhaNOURL($texto, 'left', $corFundo, 5, 'tabfundo1');
		fechaLinhaTabela();
			
		$dados = dbNotaFiscalServico( $registro, $acao ); // busca dados da Nota Fiscal de Serviço se existir

		//inserindo paginador para exibição dos registros
		if( !$dados || count($dados)==0 ){
			# Não há registros
			itemTabelaNOURL('Nenhuma Nota Fiscal Fatura de Serviço a Listar !', 'left', $corFundo, 3, 'txtaviso');
		}
		else{
			# Paginador
			paginador($dados, count($dados), $limite['lista']['notafiscal'], $registro, 'normal10', 5, $urlADD);
			
			novaLinhaTabela( $corFundo, '100%' );
				for ( $x = 0; $x < count( $largura ); $x++ ){
					itemLinhaTMNOURL( $gravata[$x], 'center', 'middle', $largura[$x], $corFundo, 0, $corGravata);
				}
			fechaLinhaTabela();
			
			$cc = 0; //contador
			if( !empty( $dados ) ){	
				# Setar registro inicial
				if( !$registro ){
					$i=0;
				}
				elseif( $registro && is_numeric($registro) ){
					$i=$registro;
				}
				else {
					$i=0;
				}
					
				$limite = $i + $limite['lista']['notafiscal'];
				
				while( $i < count($dados) && $i < $limite ){
					
					novaLinhaTabela( $corFundo, '100%');
						itemLinhaTMNOURL( $dados[$i]->numNF, $alinhamento[$cc++], 'middle', '10%', $corFundo, 0, $corDetalhe);
						itemLinhaTMNOURL( $dados[$i]->cliente.$texto, $alinhamento[$cc++], 'middle', '10%', $corFundo, 0, $corDetalhe);
						#valores
						$reg['id']=$dados[$i]->id;
						$val = dbNotaFiscalServico($reg, "calcularNota");
						$moeda = "R$ ";
						itemLinhaTMNOURL($moeda . number_format($val[0]->valor,2,',','.'), $alinhamento[$c++], 'middle', '10', $corFundo, 0, $corDetalhe);
						#status
						if( strtoupper($dados[$i]->status) == 'A'){
							$txt = htmlMontaOpcao("Aberta", "pasta");
						}
						elseif( strtoupper($dados[$i]->status) == 'I'){
							$txt = htmlMontaOpcao("Impressa", "imprimir");
						}
						elseif( strtoupper($dados[$i]->status) == 'C'){
							$txt = htmlMontaOpcao("Cancelada", "cancelar");
						}
						itemLinhaTMNOURL($txt, $alinhametno[$c++], 'midle', '10', $corFundo, 0, $corDetalhe);
													
						$status = $dados[$i]->status;
						$id = $dados[$i]->id;
							
						#opcoes
						$def="<a href=?modulo=$modulo&sub=$sub&registro=$id&status=$status";
						$fnt="<font size='2'>";
						$opcoes =htmlMontaOpcao($def."&acao=ver&matriz[idNFS]=$id>".$fnt."Ver</font></a>",'ver');
						if ( strtoupper( $dados[$i]->status ) == 'A' ){
							$opcoes.=htmlMontaOpcao($def."&acao=excluir>".$fnt."Excluir</font></a>",'excluir');
							$opcoes.=htmlMontaOpcao($def."&acao=alterarDtEmissao>".$fnt."Imprimir</font></a>",'imprimir');
							//$opcoes.='<br>'.htmlMontaOpcao($def."&acao=alterarDadosNFS>".$fnt."Aplicar Descontos</font></a>",'financeiro');
						}	
						elseif ( strtoupper( $status ) == 'I' ){
							$opcoes.=htmlMontaOpcao($def."&acao=cancelar>".$fnt."Cancelar<font></a>",'cancelar');
						}
						itemLinhaTMNOURL( $opcoes, $alinhamento[$cc++], 'middle', '10%', $corFundo, 0, $corDetalhe)	;
					fechaLinhaTabela();
					
					$cc= 0;					
					# Incrementar contador
					$i++;
				} #fecha laco de montagem de tabela
			}
			else{
				novaLinhaTabela( $corFundo, '100%');
				itemLinhaTMNOURL( "Nenhuma Nota Fiscal Fatura de Serviço a Listar !", 'center', 'middle', '100%', $corFundo, 5, $corDetalhe );
			}
		}	
	fechaTabela();
}

function NotaFiscalServicoExcluir( $modulo, $sub, $acao, $registro, $matriz){
	
	if( $matriz['bntConfirmar'] ) {
		$matriz['id'] = $registro;
		$grava=dbNotaFiscalServico( $matriz, 'excluir');
		$msg = ( $grava 
	 			? 'Nota Fiscal Fatura de Serviço excluída com sucesso!' 
	 			: 'Não foi possível excluir a Nota Fiscal Fatura de Serviço.' );
		avisoNOURL("Aviso", $msg, 400);
		echo "<br />";
		NotaFiscalServicoListar( $modulo, $sub, 'listar', '', $matriz );	
	}
	else{
		NotaFiscalServicoVer( $modulo, $sub, $acao, $registro, $matriz );
	}
}

function NotaFiscalServicoCancelar( $modulo, $sub, $acao, $registro, $matriz){
	
	if( $matriz['bntConfirmar'] ) {
		$matriz['id'] = $registro;
		$grava=dbNotaFiscalServico( $matriz, 'cancelar');
		$msg = ( $grava 
	 			? 'Nota Fiscal Fatura de Serviço cancelada com sucesso!' 
	 			: 'Não foi possível cancelar a Nota Fiscal Fatura de Serviço.' );
		avisoNOURL("Aviso", $msg, 400);
		echo "<br />";
		NotaFiscalServicoListar( $modulo, $sub, 'listar', '', $matriz );	
	}
	else{
		NotaFiscalServicoVer( $modulo, $sub, $acao, $registro, $matriz );
	}
}

function NotaFiscalServicoVer( $modulo, $sub, $acao, $registro, $matriz){
	global $corFundo, $corBorda, $html;

	$matriz['id'] = $registro;
	$NFS = dbNotaFiscalServico( $matriz, 'custom');
	if( count( $NFS ) > 0 ) {
		$dadosNFS = get_object_vars( $NFS[0] );
		$Acao = ucfirst( $acao );
		novaTabela2("[" . $Acao . " Nota Fiscal Fatura de Serviço]" . "<a name=\"ancora\"></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		if($dadosNFS['status'] == "A" ){
			$_REQUEST['status']="A";
			menuOpcAdicional( $modulo, $sub, $acao, $registro );
		}
		else {
			getCampo('', '', '', '&nbsp;');
		}
		getCampo( 'combo', _('N&ordm; Nota Fiscal'),  '',	$dadosNFS['numNF'] );
		getCampo( 'combo', _('Data de Emissão'),      '',	converteData($dadosNFS['dtEmissao'], 'banco', 'formdata' ));
		getCampo( 'combo', _('Cliente'), 			  '',	$dadosNFS['razao'] );
		getCampo( 'combo', _('CNPJ'), 				  '',	$dadosNFS['cnpj'] );
		getCampo( 'combo', _('Inscrição Estadual'),   '',	$dadosNFS['inscrEst'] );
		getCampo( 'combo', _('Endereço'),             '',	$dadosNFS['endereco'] );
		getCampo( 'combo', _('CEP'),                  '',	$dadosNFS['cep'] );
		getCampo( 'combo', _('Cidade'),               '',	$dadosNFS['cidade'] );
		getCampo( 'combo', _('Estado'),               '',	$dadosNFS['uf'] );
		getCampo( 'combo', _('POP'), 				  '',	formSelectPOP( $dadosNFS['idPop'], '', 'check'));
		getCampo( 'combo', _('ICMS'), 				  '',	$dadosNFS['ICMS']." %");
		getCampo( 'combo', _('Natureza da Prestação'),'',	formSelectNatPrestacao($dadosNFS['idNatPrestacao'], '','check'));
		if( $dadosNFS['status'] == "A" ){
			$situacao = "<span class=txttrial>Aberta</span>";
		}
		elseif( $dadosNFS['status'] == "C" ){
			$situacao = "<span class=txtaviso>Cancelada</span>";
		}
		elseif( $dadosNFS['status'] == "I" ){
			$situacao = "<span class=txtok>Impressa</span>";
		}
		getCampo( 'combo', _("Status"), 			  "",	$situacao );
		if( $acao == 'cancelar' || $acao == 'excluir' ) {
			getBotao( 'matriz[bntConfirmar]', $Acao, 'submit','button', 'onclick="window.location=\'?modulo='.$modulo.
				      '&sub='.$sub.'&acao='.$acao.'&registro='.$registro.'&matriz[bntConfirmar]='.$Acao.'\'"' );
		}
		else {
			getCampo('', '', '', '&nbsp;');
		}
		fechaTabela();
	}
	else {
		avisoNOURL("Erro", "Não foi possível localizar a Nota Fiscal Fatura de Serviço!", 400);
		echo "<br />";
		NotaFiscalServicoListar( $modulo, $sub, 'listar', '', $matriz );
	}
}

function NotaFiscalServicoVisualizar( $modulo, $sub, $acao, $registro, $matriz) {
	NotaFiscalServicoVer( $modulo, $sub, $acao, $registro, $matriz);
	ItensNFServicoListar( $modulo, $sub, $acao, $registro, $matriz);
}

function AlterarDadosNFS($modulo, $sub, $acao, $registro, $matriz) {
	global $corFundo, $corBorda, $html;
	
	$data=dataSistema();

	novaTabela2("[Dados Impressão Nota Fiscal Fatura de Serviço ]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		abreFormularioComCabecalho( $modulo, $sub, 'imprimir', $registro );
			getCampo('', '', '', '&nbsp;');
			//getCampo('combo', '<b>Atenção</b>', '', 'Verifique a data, observações e período para impressão da Nota Fiscal Fatura de Serviço.');
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL("<span class=bold10>Atenção: </span> Verifique a data, observações e período para impressão da Nota Fiscal Fatura de Serviço.", 'center', 'middle', '30%', $corFundo, 2, 'tabfundo1');								
			fechaLinhaTabela();
			getCampo('', '', '', '&nbsp;');
			novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL("<span class=bold10>Data de Emissão</span>", 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					$texto="<input type=radio name=matriz[emissao] value=auto checked>
				Utilizar data do sistema. <span class=txtaviso>( $data[dia]/$data[mes]/$data[ano] )</span>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('&nbsp; ', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					$texto="<input type=radio name=matriz[emissao] value=manual> Especificar Manualmente. <input type=text name=matriz[dtEmissao] onBlur=verificaData(this.value,6)>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			getCampo('text', 'Observações', 'matriz[obs]', $matriz['obs'], '','',46);
			getCampo('text', 'Período da Prestação', 'matriz[dtPrestacao]', $matriz['dtPrestacao'],'','',46);
			getBotao('matriz[bntConfirmar]', 'Imprimir', 'submit');
		fechaFormulario();			
	fechaTabela();
}

# função para imprimir
function NotaFiscalServicoImprimir($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $html, $sessCadastro, $arquivo, $conn, $tb;

	if( !$matriz['bntImprimir'] ) {

		//grava dados do cliente na nota
		$matriz['id'] = $registro;
		dbNotaFiscalServico( $matriz, 'imprimir' );
		
		//rotina de impressão da Nota Fiscal
		$objNFS = new NotaFiscalServico();
		$objNFS->NotaFiscalServico( $conn );
		$objNFS->preparaNota( $registro );

		$nomeArquivo = $arquivo['tmpNota']."nfservico".$registro.".txt";

		if(!file_exists( $nomeArquivo ) ){
			$nota = fopen( $nomeArquivo, 'w' );
			fwrite( $nota, $objNFS->imprimirNota() );
	
			fclose( $nota );
	
			$sql = "SELECT valor FROM ".$tb['ParametrosConfig']." where parametro = 'path_impressora'";
			$consulta = consultaSQL( $sql, $conn);
			if ( $consulta && contaConsulta( $consulta ) > 0 )
				$impressora = resultadoSQL( $consulta, '0', 'valor' );
			if ( !empty( $impressora ) && !is_null( $impressora ) && isset( $impressora ) )
				exec( "lpr -P".$impressora." ".$nomeArquivo );
			else
				exec( "lpr ".$nomeArquivo );

		}
		else{
			unlink( $nomeArquivo );
		}		
		# Motrar tabela de busca
		novaTabela2("[Imprimir]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro value = $registro
				<input type=hidden name=matriz[print] = 'true'>
				<input type=hidden name=matriz[bntImprimir] value=$matriz[bntImprimir]>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela( $corFundo, '100%' );
				$texto = "A Nota Fiscal Fatura de Serviço foi impressa corretamente?";
				itemLinhaTMNOURL( $texto, 'center', 'middle', '100%', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			#botao
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntImprimir] value='Confirmar' class=submit>";
				itemLinhaForm($texto, 'right', 'top', $corFundo, 1, 'tabfundo1');
				$texto="<input type=submit name=matriz[bntImprimir] value='Cancelar' class=submit>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 1, 'tabfundo1');
			fechaLinhaTabela();
		fechaTabela();
	}
	else {
		
		$matriz['id'] = $registro;

		if ( $matriz['bntImprimir'] == 'Confirmar' ){
			$grava = dbNotaFiscalServico( $matriz, 'imprimirOk' );
		
			# Verificar inclusão de registro			
			if( $grava ){
				# Visualizar Pessoa
				$msg="Nota Fiscal Fatura de Serviço impressa com sucesso!";
				avisoNOURL("Aviso: ", $msg, 400);
				echo "<br>";				
				$nota = dbNotaFiscalServico($matriz, 'custom');
				NotaFiscalServicoListar( $modulo, $sub, 'listar', '', $matriz );
			} 
			else {
				$msg="Ocorreram erros durante a impressão.";
				avisoNOURL("Aviso: Ocorrência de erro", $msg, 400);
			}
		}
		elseif ( $matriz['bntImprimir'] == 'Cancelar' ){
			NotaFiscalServicoListar( $modulo, $sub, 'listar', '', $matriz );	
		}		
	}
}	

?>