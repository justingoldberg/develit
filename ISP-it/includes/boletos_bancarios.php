<?
################################################################################
#       Criado por: Devel-IT - desenvolvimento@devel.it
#  Data de criação: 22/03/2006
# Ultima alteração: 
#    Alteração No.: 001
#
# Função:
#    Funções para gerar Boleto Bancário

/**
 * Construtor do Sub-modulo
 *
 * @param string $modulo
 * @param string $sub
 * @param string $acao
 * @param string $registro
 * @param array $matriz
 */

function boletoBancario($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessPlanos;
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[adicionar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		# Topo da tabela - Informações e menu principal do Cadastro
		novaTabela2("[Geração de Boletos Bancários]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 0);
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'left', $corFundo, 0, 'tabfundo1');
					echo "<br><img src=".$html[imagem][arquivos]." border=0 align=left><b class=bold>Geração de Boletos Bancário</b><br>
					 <span class=normal10>A <b>geração de boletos bancário</b> permite a gerar o documento de fatura com informações devidamente configuradas nas <b>formas de cobrança</b>.</span>";
				htmlFechaColuna();
			fechaLinhaTabela();
		fechaTabela();
		
		echo "<br>";
			if($acao=='gerar') {
				# Gerar arquivos
				gerarBoletoBancario($modulo, $sub, $acao, $registro, $matriz);
			}

		echo "<script>location.href='#ancora';</script>";
		
		
	}
	
}


/**
 * Função para gerar boleto bancário
 *
 * @param string $modulo
 * @param string $sub
 * @param string $acao
 * @param string $registro
 * @param array $matriz
 */
function gerarBoletoBancario($modulo, $sub, $acao, $registro, $matriz) {

	# Mostrar detalhes do faturamento
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessPlanos, $conn, $tb, $configMensagemArquivo;

	if( $matriz['tipo'] != "documentosGerados" ) {
		verFaturamento( $modulo, $sub, $acao, $registro, $matriz );
		$idFaturamento = $registro;
	}
	else {
		$dadosDocumentoGerado = dadosDocumentoGerado($registro);
		$idFaturamento = $dadosDocumentoGerado['idFaturamento'];
	}
	echo "<br>";
	
	$dadosFaturamento = dadosFaturamento($idFaturamento);
	$dadosCobranca = dadosFormaCobranca($dadosFaturamento[idFormaCobranca]);
	$dadosBanco = dadosBanco($dadosCobranca[idBanco]);
	$matriz[idBanco] = $dadosCobranca[idBanco];
	$matriz['numeroBanco'] = $dadosBanco['numero'];
	
	formGerarBoletoBancario($modulo, $sub, $acao, $registro, $matriz);
	
	if($matriz[bntConfirmar]) {
		# Gerar arquivo e mostrar link para download
		$gravaArquivo=montaBoletos($modulo, $sub, $acao, $registro, $matriz);
	}

}


function montaBoletos($modulo, $sub, $acao, $registro, $matriz){
	
	$arquivo = gerarBoletos($modulo, $sub, $acao, $registro, $matriz);
	
}


function gerarBoletos($modulo, $sub, $acao, $registro, $matriz){
	global $arquivo, $html,  $corFundo, $corBorda, $sessLogin;
	
	$data=dataSistema();
	
	$boletos = 	"";
	if($matriz["tipo"] == "faturamento"){
		$boletos = gerarBoletosFaturamento( $registro, $matriz );
	}
	elseif( $matriz["tipo"] == "documentosGerados" ){
		$boletos = gerarBoletosDocumentosGerados(  $registro, $matriz  );
	}
	
	
	if ( $boletos ){
		// salva o arquivo e o exibe para download
		
		$boleto = getBoletoBancarioObjeto($matriz['numeroBanco']);
		
		$boletos = $boleto->getCabecalho() . $boletos . $boleto->getRodape();
		
		$nomeArquivo = "boleto-".$sessLogin[login];
		
		$fp=fopen( $arquivo[tmpHTML] . $nomeArquivo . ".html", "w+");
		
		if($fp) {
			fputs($fp, $boletos);
			fclose($fp);
		}
		
		// se checou a opção imprime verso, então também é gerado o verso
		if( $matriz[imprime_verso] ) {
			// busca o conteudo do verso, seja ele de um faturamento ou de um simples documetno
			$boletosVerso = $boleto->getCabecalho(). ( ($matriz["tipo"] == "faturamento") 
								? gerarVersoBoletosFaturamento($registro) 
								: gerarVersoBoletosDocumentosGerados($registro) ) . $boleto->getRodape();

			// caminho+nome do arquivo html
			$caminhoHTMLVerso = $arquivo[tmpHTML] . $nomeArquivo . '-verso.html';
			// abre p arquivo no modo de gravacao
			$fp=fopen( $caminhoHTMLVerso, 'w+');
			
			// se abriu o arquivo, então é gravado e depois fechado
			if( $fp ) {	
				fputs($fp, $boletosVerso);
				fclose($fp);
			}

			// caminho+nome do arquivo pdf
			$caminhoPDFVerso = $arquivo[tmpPDF] . $nomeArquivo . '-verso.pdf';
			
			geraPdfHtmlDoc( $caminhoHTMLVerso, $caminhoPDFVerso );

		}
		
		echo "<br>";
		novaTabela2("[Download do Arquivo]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Arquivo Remessa gerado:</b>', 'right', 'top', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaForm($nomeArquivo, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Abrir (em HTML):</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<a href=".$arquivo[tmpHTML].$nomeArquivo. ".html" . " target=_BLANK>".
				       "<img src=".$html[imagem][arquivo]." border=0>Clique aqui para fazer o DOWNLOAD do Boleto</a>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			// se possui o verso é exibo o seu downlod em html		
			if( $matriz[imprime_verso] ) {
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('&nbsp;', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					$texto="<a href=".$caminhoHTMLVerso . " target=\"_blank\">".
					       "<img src=".$html[imagem][arquivo]." border=0>Clique aqui para fazer o DOWNLOAD do verso do Boleto</a>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();				
			}
			//gera o pdf.
			
			exec( 'htmldoc --webpage --left 1cm --top 0cm -f '.$arquivo[tmpPDF].$nomeArquivo.".pdf " . 
				$arquivo[tmpHTML].$nomeArquivo. ".html");
//			exec( 'htmldoc --webpage --left 1cm --top 0cm fb37b887f4c246254dbfffec3e799cea.html -f '.$arquivo[tmpPDF].$nomeArquivo.".pdf " . 
//				$arquivo[tmpHTML].$nomeArquivo. ".html");			
			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Download (em PDF):</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<a href=".$arquivo[tmpPDF].$nomeArquivo.".pdf  target=_BLANK>".
				       "<img src=".$html[imagem][pdf]." border=0>Clique aqui para fazer o DOWNLOAD do Boleto</a>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			// se possui o verso então exibe o downlod do verso
			if( $matriz[imprime_verso] ) {
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('&nbsp;', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					$texto="<a href=".$caminhoPDFVerso . " target=\"_blank\">".
					       "<img src=".$html[imagem][pdf]." border=0>Clique aqui para fazer o DOWNLOAD do verso do Boleto</a>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();					
			}
			
			
		fechaTabela();
	}
}


/**
 * Gera todos o s boletos de um faturamento.
 *
 * @param int $registro
 * @param array $matriz
 */
function gerarBoletosFaturamento( $registro, $matriz){
	$arquivo="";
	$dadosFaturamento = dadosFaturamento( $registro );
	
	if ( $dadosFaturamento["idFormaCobranca"] > 0){
				
		$consultaDocumentosGerados = buscaDocumentosGerados( $registro, "idFaturamento", "igual", "id" );
		
		for ( $i=0; $i<contaConsulta( $consultaDocumentosGerados ); $i++ ){
			
			$dadosDocumento["id"] 			= resultadoSQL( $consultaDocumentosGerados, $i, "id");
			$dadosDocumento["idPessoaTipo"]	= resultadoSQL( $consultaDocumentosGerados, $i, "idPessoaTipo");
					
			$arquivo .= gerarBoletosDocumentos($dadosFaturamento, $dadosDocumento, $matriz );
		}
		
	}
	
	return $arquivo;
	
}


/**
 * Gera boleto de um documento gerado especifico
 *
 * @param unknown_type $dadosFaturamento
 * @param unknown_type $documentoGerado
 * @param unknown_type $matriz
 * @return unknown
 */
function gerarBoletosDocumentosGerados(  $registro, $matriz  ){
	$arquivo = "";
	
	$consultaDocumentosGerados =  buscaDocumentosGerados( $registro, "id", "igual", "id" );
	
	if ( $consultaDocumentosGerados && contaConsulta( $consultaDocumentosGerados ) ){
		$dadosDocumento["id"] 			= resultadoSQL( $consultaDocumentosGerados, $i, "id");
		$dadosDocumento["idPessoaTipo"]	= resultadoSQL( $consultaDocumentosGerados, $i, "idPessoaTipo");
		$dadosDocumento["idFaturamento"]= resultadoSQL( $consultaDocumentosGerados, $i, "idFaturamento");
		
		$dadosFaturamento = dadosFaturamento( $dadosDocumento["idFaturamento"] );
		
		$arquivo .=  gerarBoletosDocumentos($dadosFaturamento, $dadosDocumento, $matriz );
	}
	
	return $arquivo;
}

/**
 * Gera o boleto de um documento.
 *
 * @param array $dadosFaturamento
 * @param array $documento dados do documentoGerados
 */
function gerarBoletosDocumentos( &$dadosFaturamento, &$documentoGerado, &$matriz ){
	global $bancoSicoob;
	
	$ret = "";
	$data = dataSistema();

	$contasAReceber = dadosContasAReceber( $documentoGerado["id"] );
	
	if( $contasAReceber["id"] > 0 ){
		
		$dadosFormaCobranca = dadosFormaCobranca( $dadosFaturamento["idFormaCobranca"] );
				
		$dadosPessoa = dadosPessoasTipos( $documentoGerado["idPessoaTipo"], $matriz['tipoEndereco']);
	
		$documento = documentoSelecionaPreferencial($dadosPessoa["pessoa"]["id"], $dadosPessoa["pessoa"]["tipoPessoa"]);
	
		$boleto = getBoletoBancarioObjeto($matriz['numeroBanco']);
		
		
		$boleto->set( "sacado",				( ($dadosPessoa["pessoa"]["tipoPessoa"]=="J" && !empty( $dadosPessoa["pessoa"]["razao"]) )
											  ? $dadosPessoa["pessoa"]["razao"]
											  : $dadosPessoa["pessoa"]["nome"]) );
	
		$boleto->set( "endereco1",			$dadosPessoa["endereco"]["endereco"]." ".$dadosPessoa["endereco"]["complemento"]." ".$dadosPessoa["endereco"]["bairro"]);
		$boleto->set( "endereco2",			$dadosPessoa["endereco"]["cidade"]." ".$dadosPessoa["endereco"]["uf"]." ".$dadosPessoa["endereco"]["cep"]);
		
		
		$boleto->set( "data_vencimento",	( !empty( $matriz["data_vencimento"] )  //data customizada?
										   	  ? $matriz["data_vencimento"] 		 //usa data customizada
										      : converteData( $contasAReceber["dtVencimento"], "banco", "formdata" ) ) ); //usa data vencimento do boleto
		
		
		$boleto->set( "agencia",			$dadosFormaCobranca["agencia"] );
		$boleto->set( "digitoAgencia",		$dadosFormaCobranca["digAgencia"]);
		$boleto->set( "conta",				$dadosFormaCobranca["conta"]);
		$boleto->set( "digito_conta",		$dadosFormaCobranca["digConta"]);
		$boleto->set( "nosso_numero",		$documentoGerado["id"] );
		
		/** 
		 * Linha comentada, pois cada banco tem a sua carteira, agora o atributo é settado na própria classe
		 * de cada boleto
		 * por: João Petrelli
		 * since: 06/05/2009
		*/
//		$boleto->set( "carteira", 			"175");//ooohh pai

		$boleto->set( "data_documento",		$data["dataNormalData"] );
		$boleto->set( "valor",				formatarValoresForm( $contasAReceber["valor"] ) );
		$boleto->set( "numero_documento",	$documentoGerado["id"] );
		
		$boleto->set( "cpf_cnpj_cedente",	$dadosFormaCobranca["cnpj"] );
		$boleto->set( "cedente",			$dadosFormaCobranca["titular"] );
		
		if ($matriz['numeroBanco'] == 756) {
			$boleto->set( "modalidade",		$bancoSicoob["modalidade"] );
		}
		
		if(isset($boleto->conta_cedente)) $boleto->set( 'conta_cedente', $dadosFormaCobranca['convenio']);
		
		// por Jose Ambiel - 23/03/09
		// verificar se foi selecionado algum desconto e setado uma data para fazer verificacoes
		// de clientes com planos que permitem o desconto com pagamento antecipad
		$porcentagemDesconto = $matriz[porcentagem]/100;
		if($porcentagemDesconto && $matriz[data_desconto]) {
			$valorDesconto = calculaValorDescontoGerarCobranca($documentoGerado[id], $porcentagemDesconto);
			if ($valorDesconto) {
				$boleto->instrucoes[] = "ATE $matriz[data_desconto] DESCONTO DE ..................................... ". formatarValoresForm($valorDesconto);
			}
		}
		
		/*
		 * Por João Petrelli - 12/01/2010
		 * Verifica se na tabela Pessoas o campo instrucaoBoleto está settado,
		 * e em caso positivo adiciona seu conteudo ao boleto.
		 */
		if (isset($dadosPessoa[pessoa][instrucaoBoleto]) && $dadosPessoa[pessoa][instrucaoBoleto] != "") {
			$instrucoes = explode("\n",$dadosPessoa[pessoa][instrucaoBoleto]);
			$length = count($instrucoes);
			
			for ($i = 0; $i < $length && $i < 4; $i++) {
				if (strlen($instrucoes[$i]) < 71) {
					if (count($boleto->instrucoes) < 4) {
						$boleto->instrucoes[] .=	trim($instrucoes[$i]);
					}
				} else {
					$tamanho = strlen($instrucoes[$i]);
					
					for ($j = 0; $j < $tamanho; $j += 70) {
						if (count($boleto->instrucoes) < 4) {
							$boleto->instrucoes[] .= substr($instrucoes[$i], $j, 70);
						}
					}
				}
			}
		}
		
		$length = count($instrucoes);

		if ( $matriz["mensagem1"] && $length < 4){
			$boleto->instrucoes[] =	$matriz["mensagem1"];
			$length = count($instrucoes);
		}
		if ( $matriz["mensagem2"] && $length < 4){
			$boleto->instrucoes[] = $matriz["mensagem2"];
			$length = count($instrucoes);
		}
		if ( $matriz["mensagem3"] && $length < 4){
			$boleto->instrucoes[] = $matriz["mensagem3"];
			$length = count($instrucoes);
		}
		if ( $matriz["mensagem4"] && $length < 4){
			$boleto->instrucoes[] = $matriz["mensagem4"];
			$length = count($instrucoes);
		}

		$boleto->carregaDadosBoleto();
				
		return $boleto->getHtml();
	}
}



/**
 * função para form de seleção de filtros de faturamento
 *
 * @param string $modulo
 * @param string $sub
 * @param string $acao
 * @param string $registro
 * @param array $matriz
 */
function formGerarBoletoBancario($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $configMensagemArquivo;
	
	# Carrega os parametros
	$parametros = parametrosBancosCarregar($matriz[idBanco]);
	
	if($matriz[sem_mensagem]=='S') {
		$opcSemMensagem='checked';
		$matriz['mensagem1'] = $matriz['mensagem2'] = $matriz['mensagem3'] = $matriz['mensagem4'] = $matriz['mensagem5'] = '';
	}
	elseif(!$matriz[bntConfirmar]){
		# verifica os parametros do banco
		for( $i=1; $i<=5; $i++ ) {
			$matriz["mensagem$i"] = (isset($parametros["boleto_msg$i"]) ? $parametros["boleto_msg$i"] : '');
		}

	}
	# Motrar tabela de busca
	novaTabela2("[Informações para geração do Boleto Bancário]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		novaLinhaTabela($corFundo, '100%');
		$texto="			
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=matriz[tipo] value=".$matriz['tipo'].">
			<input type=hidden name=registro value=$registro>&nbsp;";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Mensagem:</b><br>
			<span class=normal10>Mensagem exibida no corpo dos Boletos</span>', 'right', 'top', '30%', $corFundo, 0, 'tabfundo1');
			$texto="<input type=text name=matriz[mensagem1] maxlength=70 size=40 value='{$matriz['mensagem1']}'><span class=txtaviso> (linha 1)</span><br>
			<input type=text name=matriz[mensagem2] size=40 maxlength=70 value='{$matriz['mensagem2']}'><span class=txtaviso> (linha 2)</span>
			<input type=text name=matriz[mensagem3] size=40 maxlength=70 value='{$matriz['mensagem3']}'><span class=txtaviso> (linha 3)</span>
			<input type=text name=matriz[mensagem4] size=40 maxlength=70 value='{$matriz['mensagem4']}'><span class=txtaviso> (mensagem do recibo)</span>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		
		novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Data Especial para Desconto:</b><br><span class=normal8>Data limite para concessão de desconto</span>', 'right', 'top', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input id='dataDesconto' type=text name=matriz[data_desconto] size=10 value='$matriz[data_desconto]' onBlur=verificaData(this.value,$indexCampoDataDesconto)><span class=txtaviso> (Formato: 19/03/2009)</span>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Valor do Desconto:</b><br>
				<span class=normal8>Escolha uma porcentagem para ser descontada do valor total da fatura caso o usuário pague antes da <b>Data Especial para Desconto</b></span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaForm(formSelectPorcentagem($matriz['porcentagem'],'porcentagem'), 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Endereço de preferência de envio:</b><br>
			<span class=normal10>Selecione o tipo de endereço preferencial</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			if(!$matriz[tipoEndereco]) $matriz[tipoEndereco]='cob';
			itemLinhaForm(formSelectTipoEndereco($matriz[tipoEndereco], 'tipoEndereco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Data Especial para Vencimento:</b><br>
			<span class=normal10>Informe uma data especial a ser utilizada como vencimento)</span>', 'right', 'top', '30%', $corFundo, 0, 'tabfundo1');
			$texto="<input type=text name=matriz[data_vencimento] size=10 value='$matriz[data_vencimento]' onBlur=verificaData(this.value,11)><span class=txtaviso> (Formato: 12/03/2003)</span>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Imprimir o Verso:</b><br>
			<span class="normal10">Marque esta opção caso deseje imprimir o verso</span>', 'right', 'top', '30%', $corFundo, 0, 'tabfundo1');
			$texto='<input type="checkbox" name="matriz[imprime_verso]" value="S" />';
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
		
		if(!$matriz[bntConfirmar]) formPessoaSubmit($modulo, $sub, $acao, $registro, $matriz);
	fechaTabela();
	
}

/**
 * Retorna o objetvo Boleto do banco selecionado
 *
 * @param string $numero
 * @return object
 */
function getBoletoBancarioObjeto($numero) {
	switch ($numero) {
		// Caso seja a CEF
		case '104':
			$boleto = new BoletoCEF();
			break;
		case '756':
			$boleto = new BoletoSicoob();
			break;
		// O Padrão é o Itau
		default:
			$boleto = new BoletoIT();	
	}
	return $boleto;
}

?>