<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 19/09/2003
# Ultima alteração: 08/03/2004
#    Alteração No.: 009
#
# Função:
#    Funções para arquivos remessda


# função de busca 
function buscaArquivosRemessa($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[ArquivoRemessa] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[ArquivoRemessa] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[ArquivoRemessa] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[ArquivoRemessa] WHERE $texto ORDER BY $ordem";
	}
	
	# Verifica consulta
	if($sql){
		$consulta=consultaSQL($sql, $conn);
		# Retornvar consulta
		return($consulta);
	}
	else {	
		# Mensagem de aviso
		$msg="Consulta não pode ser realizada por falta de parâmetros";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
	}
} # fecha função de busca



# função para criação de novo arquivo remessa
function arquivoRemessaNovoArquivo($idBanco, $conta = '') {

	global $conn, $tb;
	
	if($idBanco && $conta==''){
		$sqlBanco = "where idBanco='". $idBanco."'";
	}
	elseif($idBanco && $conta){
		$sqlBanco ="LEFT JOIN $tb[Faturamentos] ON ($tb[Faturamentos].id = $tb[ArquivoRemessa].idFaturamento)";
		$sqlBanco .="LEFT JOIN $tb[FormaCobranca] ON ($tb[FormaCobranca].id =$tb[Faturamentos].idFormaCobranca)";
		$sqlBanco .= "where $tb[ArquivoRemessa].idBanco='". $idBanco."' and FormaCobranca.conta = '".$conta."'";
	}
	
	$sql="SELECT MAX(idArquivo)+1 numero from $tb[ArquivoRemessa] $sqlBanco";
	
	$consulta=consultaSQL($sql, $conn);
	
	# Data do sistema
	$data=dataSistema();
	
	# Buscar numero
	$numero=resultadoSQL($consulta, 0, 'numero');
	
	if(!$numero || !is_numeric($numero)) {
		$matRetorno[numeroArquivo]=1;
	}
	else {
		$matRetorno[numeroArquivo]=$numero;
	}
	
	# Criar novo arquivo para URL de download
	$matRetorno[nomeArquivo]="REM".$matRetorno[numeroArquivo].'.TRM';
	
	return($matRetorno);

}



# função para gravação de arquivo remessa
function arquivoRemessaGravaArquivo($matriz) {

	global $conn, $tb, $sessLogin, $arquivo;
	
	$data=dataSistema();
	$matriz[conteudo]=addslashes($matriz[conteudo]);
	
	$sql="
		INSERT INTO 
			$tb[ArquivoRemessa] 
			VALUES (
				0,
				$matriz[idArquivo], 
				$matriz[idBanco], 
				$matriz[idFaturamento],
				$matriz[idUsuario], 
				'$data[dataBanco]', 
				'$matriz[nomeArquivo]', 
				'$matriz[conteudo]'
			)";
			
	$consulta=consultaSQL($sql, $conn);

	$tmpArquivo=$arquivo[tmpDir] . $matriz[nomeArquivo];

	# Gravar arquivo em disco fisico
	$fp=fopen("$tmpArquivo","w+");
	
	if($fp) {
		fputs($fp, $matriz[conteudo]);
		fclose($fp);
	}
	
	return($tmpArquivo);
	
}

# Lançamentos
function arquivosRemessa($modulo, $sub, $acao, $registro, $matriz) {

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
		novaTabela2("[Geração de Arquivos Remessa]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 0);
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'left', $corFundo, 0, 'tabfundo1');
					echo "<br><img src=".$html[imagem][arquivos]." border=0 align=left><b class=bold>Geração de Arquivos Remessa</b><br>
					 <span class=normal10>A <b>geração de arquivos remessa</b> permite a transferência de informações
					 financeiras, extraídas do sistema para faturamento e lançamentos de cobrança, para bancos de acordo
					 informações devidamente configuradas nas <b>formas de cobrança</b>.</span>";
				htmlFechaColuna();
			fechaLinhaTabela();
		fechaTabela();
		
		echo "<br>";
		
		if($sub=="arquivoremessa") {
			if($acao=='listar' || $acao=='listartodos' || $acao=='listargerados' || !$acao) {
				# Listar faturamentos ativos
				arquivosListarFaturamentos($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='gerar') {
				# Gerar arquivos
				gerarArquivosRemessa($modulo, $sub, $cao, $registro, $matriz);
			}
			elseif($acao=='download') {
				# Gerar arquivos
				downloadArquivoRemessa($modulo, $sub, $cao, $registro, $matriz);
			}
		}
		
		echo "<script>location.href='#ancora';</script>";
		
		
	}
	
}


# Função para listagem 
function arquivosListarFaturamentos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessPlanos, $conn, $tb;
	
	$data=dataSistema();
	
	if($acao=='listar') $consulta=buscaFaturamentos("remessa='N' AND status='A'", '','custom','data DESC');
	elseif($acao=='listartodos') $consulta=buscaFaturamentos('A', 'status','todos','data DESC');
	elseif($acao=='listargerados') $consulta=buscaFaturamentos("remessa='A' AND status='A'", '','custom','data DESC');
	
	# Cabeçalho		
	# Motrar tabela de busca
	novaTabela("[Listagem de Arquivos Remessa Baseado em Faturamentos Gerados e Ativos]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 7);
	$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Arquivos pendentes</a>",'arquivo');
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listargerados>Arquivos gerados</a>",'arquivo');
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listartodos>Todos</a>",'arquivo');
	itemTabelaNOURL($opcoes, 'center', $corFundo, 7, 'tabfundo1');
	
	$opcoes='';
	
	# Caso não hajam servicos para o servidor
	if(!$consulta || contaConsulta($consulta)==0) {
		# Não há registros
		itemTabelaNOURL('Não há arquivos remessa disponíveis', 'left', $corFundo, 7, 'txtaviso');
	}
	else {
	
		# Paginador
		paginador($consulta, contaConsulta($consulta), $limite[lista][arquivosremessa], $registro, 'normal10', 7, $urlADD);

		# Cabeçalho
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTabela('Identificação do Faturamento', 'center', '45%', 'tabfundo0');
			itemLinhaTabela('Status', 'center', '10%', 'tabfundo0');
			itemLinhaTabela('Arquivo', 'center', '10%', 'tabfundo0');
			itemLinhaTabela('Geração', 'center', '10%', 'tabfundo0');
			itemLinhaTabela('Total', 'center', '15%', 'tabfundo0');
			itemLinhaTabela('Qtde', 'center', '5%', 'tabfundo0');
			itemLinhaTabela('Opções', 'center', '15%', 'tabfundo0');
		fechaLinhaTabela();
		
		# Setar registro inicial
		if(!$registro) {
			$i=0;
		}
		elseif($registro && is_numeric($registro) ) {
			$i=$registro;
		}
		else {
			$i=0;
		}
		$limite=$i+$limite[lista][arquivosremessa];

		for($i=$i;$i<contaConsulta($consulta) && $i < $limite;$i++) {
			# Mostrar registro
			$id=resultadoSQL($consulta, $i, 'id');
			$descricao=resultadoSQL($consulta, $i, 'descricao');
			$data=resultadoSQL($consulta, $i, 'data');
			$remessa=resultadoSQL($consulta, $i, 'remessa');
			$status=resultadoSQL($consulta, $i, 'status');

			if($remessa=='N' && ($status != 'I' && $status != 'C') ) { 
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=gerar&registro=$id>Gerar</a>",'arquivo');
			}
			elseif($remessa=='A') { 
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=download&registro=$id>Download</a>",'arquivo');
			}
			else {
				$opcoes='&nbsp;';
			}

			# Dados do faturamento
			$dadosFaturamento=totalFaturamento($id);
			$valor=formatarValoresForm($dadosFaturamento[total]);
			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela($descricao, 'left', '45%', 'normal10');
				itemLinhaTabela(formSelectStatus($status, '','check'), 'center', '10%', 'normal10');
				itemLinhaTabela(formSelectStatusRemessa($remessa, '','check'), 'center', '10%', 'normal10');
				itemLinhaTabela(converteData($data, 'banco','formdata'), 'center', '10%', 'normal10');
				itemLinhaTabela("<span class=txtok>$valor</span>", 'center', '15%', 'normal10');
				itemLinhaTabela($dadosFaturamento[qtde], 'center', '5%', 'normal10');
				itemLinhaTabela($opcoes, 'left', '15%', 'normal8 nowrap');
			fechaLinhaTabela();
			
		} #fecha laco de montagem de tabela
		
	} #fecha servicos encontrados
	
	fechaTabela();
	
}#fecha função de listagem




# Função gerar arquivos de remessa
function gerarArquivosRemessa($modulo, $sub, $acao, $registro, $matriz) {

	# Mostrar detalhes do faturamento
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessPlanos, $conn, $tb, $configMensagemArquivo;
	
	verFaturamento($modulo, $sub, $acao, $registro, $matriz);
	echo "<br>";
	
	$dadosFaturamento=dadosFaturamento($registro);
	$dadosCobranca = dadosFormaCobranca($dadosFaturamento[idFormaCobranca]);
	$matriz[idBanco] = $dadosCobranca[idBanco];
	
	$parametros = parametrosBancosCarregar($matriz[idBanco]);
	
	if($matriz[sem_mensagem]=='S') {
		$matriz[mensagem]='';
		$matriz[mensagem2]='';
	}
	elseif(!$matriz[bntConfirmar]){
		if (isset($parametros[arq_remessa_msg1]))
			$matriz[mensagem] = $parametros[arq_remessa_msg1];
//		else{
//			avisoNOURL("Atenção", "Parametro de configuracao arq_remessa_msg1 ausente.<br>", "400");
//			echo "<br>";
//		}	
			
		if (isset($parametros[arq_remessa_msg2]))
			$matriz[mensagem2] = $parametros[arq_remessa_msg2];
//		else{
//			avisoNOURL("Atenção", "Parametro de configuracao arq_remessa_msg2 ausente.", "400");
//			echo "<br>";
//		}
	}
	
	formGerarArquivoRemessa($modulo, $sub, $acao, $registro, $matriz);
	
	if($matriz[bntConfirmar]) {
		# Gerar arquivo e mostrar link para download
		$gravaArquivo=gravaArquivoRemessa($modulo, $sub, $acao, $registro, $matriz);
	}

}



# Função para cancelar faturamentos
function downloadArquivoRemessa($modulo, $sub, $acao, $registro, $matriz) {

	# Mostrar detalhes do faturamento
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessPlanos, $conn, $tb, $configMensagemArquivo, $arquivo;
	
	verFaturamento($modulo, $sub, 'download', $registro, $matriz);
	
	# Buscar Arquivo de Remessa do Faturamento
	$consulta=buscaArquivosRemessa($registro, 'idFaturamento','igual','idFaturamento');
	
	# Motrar tabela de busca
	echo "<br>";
	novaTabela2("[Download do Arquivo]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	
	if($consulta && contaConsulta($consulta)>0) {
		
		$nomeArquivo=resultadoSQL($consulta, 0, 'nomeArquivo');
		
		$arquivo=$arquivo[tmpDir]."$nomeArquivo";
		
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Arquivo Remessa gerado:</b>', 'right', 'top', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm($arquivo, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Download:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			$texto="<a href=$arquivo target=_BLANK><img src=".$html[imagem][arquivo]." border=0>Clique aqui para fazer o DOWNLOAD do Arquivo</a>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
	}
	fechaTabela();

}



# função para form de seleção de filtros de faturamento
function formGerarArquivoRemessa($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $configMensagemArquivo;
	
	# Obtem número do banco da forma de cobraça
	$faturamento 	= dadosFaturamento($registro);
	$formaCobranca	= dadosFormaCobranca($faturamento['idFormaCobranca']);
	$banco			= dadosBanco($formaCobranca['idBanco']);
	$tipoCarteira	= dadosTipoCarteira($formaCobranca['idTipoCarteira']);
	$indexCampoData = 6;
	
	# Motrar tabela de busca
	novaTabela2("[Informações para geração de Arquivo Remessa]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		novaLinhaTabela($corFundo, '100%');
		$texto="			
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=registro value=$registro>&nbsp;";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		
		$indexCampoDataDesconto = 5;
		$indexCampoData = 8;
		
		if ($banco['numero'] == 341 && $tipoCarteira['valor'] == 'M') {
			$indexCampoDataDesconto = 6;
			$indexCampoData = 9;
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Mensagem para os Clientes:</b><BR>
					<span class=normal8>Mensagem que será impresa no boleto com informções para o cliente</span><br>
					<span class=txtaviso8>Aviso: A mensagem não pode conter mais que 33 linhas.', 'right', 'top', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<textarea name=matriz[msn] rows=10 cols=63>$matriz[msn]</textarea>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		}
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<BR><span class=normal10><b>Mensagem de Corpo dos Boletos:</b></span><BR>
				<span class=normal8> Mensagem com instruções de combrança (preencher o campo caso necessária mensagem adicional)</span>', 'right', 'top', '30%', $corFundo, 0, 'tabfundo1');
			$texto="<BR><input type=text name=matriz[mensagem] size=40 value='$matriz[mensagem]' maxlength=40>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		
		// so no caso do banco da forma de cobrança for o ITAU
		if ($banco['numero'] == 341) {
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
		}
		
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Endereço de preferência de envio:</b><br>
			<span class=normal8>Selecione o tipo de endereço preferencial</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			if(!$matriz[tipoEndereco]) $matriz[tipoEndereco]='cob';
			itemLinhaForm(formSelectTipoEndereco($matriz[tipoEndereco], 'tipoEndereco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Data Especial para Vencimento:</b><br>
			<span class=normal8>Informe uma data especial a ser utilizada como vencimento</span>', 'right', 'top', '30%', $corFundo, 0, 'tabfundo1');
			$texto="<input id='data' type=text name=matriz[data_vencimento] size=10 value='$matriz[data_vencimento]' onBlur=verificaData(this.value,$indexCampoData)><span class=txtaviso> (Formato: 12/03/2003)</span>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();

		# Se banco for SICREDI Mostra o campo para inserir o número de remessa
		if( $banco['numero'] == '748' ) {
			getCampo( 'text', 'Número da remessa', 'matriz[numero_remessa]', $matriz['numero_remessa'], '', '', '5' );
		}
		
//		itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
		
		if(!$matriz[bntConfirmar]) formPessoaSubmit($modulo, $sub, $acao, $registro, $matriz);
	fechaTabela();
	
}




# Função para gravação de arquivo Remessa
function gravaArquivoRemessa($modulo, $sub, $acao, $registro, $matriz) {
	
	global $conn, $tb, $corFundo, $corBorda, $sessLogin, $html;
	
	if (contaLinhasMensagem($matriz[msn])) {
	
		$sql="SELECT
				$tb[DocumentosGerados].id 
			FROM
				$tb[DocumentosGerados], 
				$tb[Faturamentos] 
			WHERE
				$tb[DocumentosGerados].idFaturamento=$tb[Faturamentos].id 
				AND $tb[Faturamentos].id=$registro";
		
		$consulta=consultaSQL($sql, $conn);
		
		# Relacionar registros
		if($consulta && contaConsulta($consulta)>0) {
		
			# data do sistema
			$data=dataSistema();
			
			# Buscar dados do faturamento para enviar para a geração de arquivo remessa
			$faturamento=dadosFaturamento($registro);
			
			if($faturamento && is_array($faturamento)) {
				# Prosseguir com detalhamento de documentos do faturamento
				# Dados da Forma de Cobranca
				$formaCobranca=dadosFormaCobranca($faturamento[idFormaCobranca]);
				
				#tipo da carteira
				$tipoCarteira=dadosTipoCarteira($formaCobranca[idTipoCarteira]);
				
				# Dados do Banco - Identificação de numero do banco para seleção de layout
				$banco=dadosBanco($formaCobranca[idBanco]);
				
				# Organizar dados
				# Informações sobre o Banco
				$matriz[bancoNome]=$banco[nome];
				$matriz[bancoNumero]=$banco[numero];
				$matriz[bancoID]=$banco[id];
				
				# Informações sobre Cobrança e Titularidade das Cobranças
				$matriz[cobrancaTitular]=$formaCobranca[titular];
				$matriz[cobrancaCNPJ]=$formaCobranca[cnpj];
				$matriz[cobrancaConvenio]=$formaCobranca[convenio];
				$matriz[cobrancaAgencia]=$formaCobranca[agencia];
				$matriz[cobrancaAgenciaDig]=$formaCobranca[digAgencia];
				$matriz[cobrancaConta]=$formaCobranca[conta];
				$matriz[cobrancaContaDig]=$formaCobranca[digConta];
				$matriz[cobrancaTipoCarteira]=$tipoCarteira[valor];
				$matriz['arquivoremessa'] = $formaCobranca['arquivoremessa'];
				
				# Informações de Faturamento
				$matriz[idFaturamento]=$registro;
				
				# Gerar o arquivo aqui
	//echo "<br>[".$banco[numero]."]";
				
				if($banco[numero]=='001') {
					# Layout do Banco do Brasil
	
					$dadosNovoArquivo=arquivoRemessaNovoArquivo($matriz[idBanco]);
					$matriz[idArquivo]=$dadosNovoArquivo[numeroArquivo];				
					
					#verifica o tipo de remessa a ser criado pelo TipoCarteira da tabela FormaCobranca
	//echo "<br>[".$matriz[cobrancaTipoCarteira]."]";
					if($matriz[cobrancaTipoCarteira]=="D") $conteudoArquivo=gerarArquivoRemessaDebitoPadrao($matriz);
					else $conteudoArquivo=gerarArquivoRemessaBB($matriz);
					
					# Gravar Conteúdo
					$matriz[idBanco]=$matriz[bancoID];
					$matriz[idFaturamento]=$registro;
					$matriz[idUsuario]=buscaIDUsuario($sessLogin[login], 'login','igual','id');
					$matriz[conteudo]=$conteudoArquivo;
	//				$dadosNovoArquivo=arquivoRemessaNovoArquivo($matriz[idBanco]);
	//				$matriz[idArquivo]=$dadosNovoArquivo[numeroArquivo];
					$matriz[nomeArquivo]=$dadosNovoArquivo[nomeArquivo];
					
					$arquivo=arquivoRemessaGravaArquivo($matriz);
					
					# Ativar arquivo remessa
					arquivoRemessaAtivar($matriz[idFaturamento]);
					
				}
				elseif($banco[numero]=='341') {
					# Layout do Itau
					$conteudoArquivo=gerarArquivoRemessaITAU($matriz);
					
					# Gravar Conteúdo
					$matriz[idBanco]=$matriz[bancoID];
					$matriz[idFaturamento]=$registro;
					$matriz[idUsuario]=buscaIDUsuario($sessLogin[login], 'login','igual','id');
					$matriz[conteudo]=$conteudoArquivo;
					$dadosNovoArquivo=arquivoRemessaNovoArquivo($matriz[idBanco]);
					$matriz[idArquivo]=$dadosNovoArquivo[numeroArquivo];
					$matriz[nomeArquivo]=$dadosNovoArquivo[nomeArquivo];
					
					$arquivo=arquivoRemessaGravaArquivo($matriz);				
					
					# Ativar arquivo remessa
					arquivoRemessaAtivar($matriz[idFaturamento]);
				}
				# Layout do CEF
				elseif($banco[numero]=='104') {
					$conteudoArquivo=gerarArquivoRemessaCEF($matriz);
					
					# Gravar Conteúdo
					$matriz[idBanco]=$matriz[bancoID];
					$matriz[idFaturamento]=$registro;
					$matriz[idUsuario]=buscaIDUsuario($sessLogin[login], 'login','igual','id');
					$matriz[conteudo]=$conteudoArquivo;
					$dadosNovoArquivo=arquivoRemessaNovoArquivo($matriz[idBanco]);
					$matriz[idArquivo]=$dadosNovoArquivo[numeroArquivo];
					$matriz[nomeArquivo]=$dadosNovoArquivo[nomeArquivo];
					
					$arquivo=arquivoRemessaGravaArquivo($matriz);
					
					# Ativar arquivo remessa
					arquivoRemessaAtivar($matriz[idFaturamento]);
				}
				# Layout do Bradesco
				elseif($banco[numero]=='237') {
					
					$matriz[idBanco]=$matriz[bancoID];
					$matriz[idFaturamento]=$registro;
					$matriz[idUsuario]=buscaIDUsuario($sessLogin[login], 'login','igual','id');
					//linha abaixo alterada devido a sequêcia separada para carteiras diferentes com contas diferentes 
					//mas pertencente ao mesmo banco
					//$dadosNovoArquivo=arquivoRemessaNovoArquivo($matriz[idBanco]);
					$dadosNovoArquivo=arquivoRemessaNovoArquivo($matriz[idBanco], $matriz[cobrancaConta]);
					$matriz[idArquivo]=$dadosNovoArquivo[numeroArquivo];
					$matriz[nomeArquivo]=$dadosNovoArquivo[nomeArquivo];
					
					$parametrosBancos = parametrosBancosCarregar($matriz[idBanco]);
					if ($parametrosBancos[dias_protesto] > 0){
						$matriz[instrucao1] = "06";
						$matriz[instrucao2] = $parametrosBancos[dias_protesto];
					}
					else{
						$matriz[instrucao1] = "00";
						$matriz[instrucao2] = "00";
					}
					
					//if ( $matriz['arquivoremessa']=="150") $conteudoArquivo = gerarArquivoRemessaDebitoBradesco( $matriz );
					if ( $matriz['arquivoremessa']=="150") $conteudoArquivo = gerarArquivoRemessaDebitoBradesco( $matriz );
					else $conteudoArquivo=gerarArquivoRemessaBradesco($matriz);
					
					# Gravar Conteúdo
					$matriz[conteudo]=$conteudoArquivo;
					
					$arquivo=arquivoRemessaGravaArquivo($matriz);				
					
					# Ativar arquivo remessa
					arquivoRemessaAtivar($matriz[idFaturamento]);
				}
				# BANESPA
				elseif($banco[numero]=='033') {
					
					$matriz[idBanco]=$matriz[bancoID];
					$matriz[idFaturamento]=$registro;
					$matriz[idUsuario]=buscaIDUsuario($sessLogin[login], 'login','igual','id');
					
					$dadosNovoArquivo=arquivoRemessaNovoArquivo($matriz[idBanco]);
					$matriz[idArquivo]=$dadosNovoArquivo[numeroArquivo];
					$matriz[nomeArquivo]=$dadosNovoArquivo[nomeArquivo];
					
					$parametrosBancos = parametrosBancosCarregar($matriz[idBanco]);
					if ($parametrosBancos[instrucao_multa_atraso]){
						$matriz['instrucao1'] = $parametrosBancos[instrucao_multa_atraso];
					}
					else{
						$matriz['instrucao1'] = "00";
					}
					
					if ($parametrosBancos[juros_atraso]){
						$matriz['juros_atraso'] = $parametrosBancos[juros_atraso];
					}
					else{
						$matriz['juros_atraso'] = "";
					}
					
					# Layout do Banespa
					//$conteudoArquivo=gerarArquivoRemessaITAU($matriz);
					if($matriz['arquivoremessa']=="150") $conteudoArquivo=gerarArquivoRemessaDebitoPadrao( $matriz );
					else $conteudoArquivo= gerarArquivoRemessaBanespa($matriz);
					
					# Gravar Conteúdo
					$matriz[conteudo]=$conteudoArquivo;
					$arquivo=arquivoRemessaGravaArquivo($matriz);				
					
					# Ativar arquivo remessa
					arquivoRemessaAtivar($matriz[idFaturamento]);
				}
				# SICREDI
				elseif( $banco['numero'] == '748' ) {
					$matriz['idBanco']			= $matriz['bancoID'];
					$matriz['idFaturamento']	= $registro;
					$matriz['idUsuario']		= buscaIDUsuario($sessLogin['login'], 'login','igual','id');
					$dadosNovoArquivo			= arquivoRemessaNovoArquivo($matriz['idBanco']);
					$matriz['idArquivo']		= $dadosNovoArquivo['numeroArquivo'];
					$matriz['nomeArquivo']		= $dadosNovoArquivo['nomeArquivo'];
					
					
					$conteudoArquivo = gerarArquivoRemessaBansicredi($matriz);
					
					# Gravar Conteúdo
					$matriz['conteudo'] = $conteudoArquivo;
					
					$arquivo = arquivoRemessaGravaArquivo($matriz);				
					
					# Ativar arquivo remessa
					arquivoRemessaAtivar($matriz['idFaturamento']);
						 
				}
				# Layout do HSBC
				elseif($banco[numero]=='399') {
					
					$matriz[idBanco]=$matriz[bancoID];
					$matriz[idFaturamento]=$registro;
					$matriz[idUsuario]=buscaIDUsuario($sessLogin[login], 'login','igual','id');
					$dadosNovoArquivo=arquivoRemessaNovoArquivo($matriz[idBanco]);
					$matriz[idArquivo]=$dadosNovoArquivo[numeroArquivo];
					$matriz[nomeArquivo]=$dadosNovoArquivo[nomeArquivo];
					
					$parametrosBancos = parametrosBancosCarregar($matriz[idBanco]);
					if ($parametrosBancos[dias_protesto] > 0){
						$matriz[instrucao1] = "06";
						$matriz[instrucao2] = $parametrosBancos[dias_protesto];
					}
					else{
						$matriz[instrucao1] = "00";
						$matriz[instrucao2] = "00";
					}
					
					//if ( $matriz['arquivoremessa']=="150") $conteudoArquivo = gerarArquivoRemessaDebitoBradesco( $matriz );
					$conteudoArquivo=gerarArquivoRemessaHSBC($matriz);
					
					# Gravar Conteúdo
					$matriz[conteudo]=$conteudoArquivo;
					
					$arquivo=arquivoRemessaGravaArquivo($matriz);				
					
					# Ativar arquivo remessa
					arquivoRemessaAtivar($matriz[idFaturamento]);
				}
				
				
				if($conteudoArquivo) {
					# Motrar tabela de busca
					echo "<br>";
					novaTabela2("[Download do Arquivo]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL('<b>Arquivo Remessa gerado:</b>', 'right', 'top', '30%', $corFundo, 0, 'tabfundo1');
							itemLinhaForm($arquivo, 'left', 'top', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL('<b>Download:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
							$texto="<a href=$arquivo target=_BLANK><img src=".$html[imagem][arquivo]." border=0>Clique aqui para fazer o DOWNLOAD do Arquivo</a>";
							itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
					fechaTabela();
				}
			}
			else {
				# Cabeçalho		
				# Motrar tabela de busca
				echo "<br>";
				novaTabela("[Lista de Faturas para Geração de Cobrança]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 4);
					itemTabelaNOURL('Não há registros cadastrados', 'left', $corFundo, 4, 'txtaviso');
				fechaTabela();
			}
		}
		else { # sem regitros
			# Motrar tabela de busca
			echo "<br>";
			novaTabela("[Lista de Faturas para Geração de Cobrança]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 4);
				itemTabelaNOURL('Não há registros cadastrados', 'left', $corFundo, 4, 'txtaviso');
			fechaTabela();
		}
	}else {
		# Motrar tabela de busca
		echo "<br>";
		novaTabela("[Não foi possível gerar o arquivo]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 4);
			itemTabelaNOURL('Mensagem ultrapassou a quantidade máxima de linhas permitido para geração do arquivo', 'left', $corFundo, 4, 'txtaviso');
		fechaTabela();
	}
}


# Funcão para geração de arquivos - Banco do Brasil
function gerarArquivoRemessaDebitoBB($matriz) {
	
	global $conn, $tb, $corFundo, $corBorda;
	
	# Data Atual
	$data=dataSistema();

	# sequencia de registros
	$sequenciaArquivo=1;
	
	# Variável de conteúdo de arquivo
	$conteudoArquivo='';

	### HEADER DE ARQUIVO
	# Codigo do registro
	$conteudoArquivo ='A';
	# Codigo de remessa
	$conteudoArquivo.='1';
	# Codigo do Convenio - 20
	$conteudoArquivo.=exportaDados($matriz[cobrancaConvenio], "", "left", 20, " ");
	# Nome da empresa
	$conteudoArquivo.=exportaDados($matriz[cobrancaTitular], "", "left", 20, " ");
	# Codigo do banco
	$conteudoArquivo.=exportaDados($matriz[bancoNumero], "N", "right", 3, "0");
	# Nome do banco
	$conteudoArquivo.=exportaDados($matriz[bancoNome], "X", "left", 20, " ");
	# Data da Gravacao
	$conteudoArquivo.=exportaDados(converteData($data, "sistema", "remessa"), "N", "right", 8, "0");
	# numero sequencial do arquivo
	$conteudoArquivo.=exportaDados($sequenciaArquivo++, "N", "right", 6, "0");
	# versao do layout
	$conteudoArquivo.="04";
	# Produto
	$conteudoArquivo.="DÉBITO AUTOMATICO";
	# Livre
	$conteudoArquivo.=exportaDados("", "", "left", 52, " ")."\n";
	
	# Registro tipo E (remessa)
	$sql="SELECT
			$tb[DocumentosGerados].id idDocumentoGerado, 
			$tb[ContasReceber].valor valor, 
			$tb[ContasReceber].dtVencimento dtVencimento, 
			$tb[PessoasTipos].id idPessoaTipo, 
			$tb[Pessoas].nome nomePessoa, 
			$tb[Pessoas].razao razaoSocial, 
			$tb[Pessoas].id idPessoa, 
			$tb[Pessoas].tipoPessoa tipoPessoa 
		FROM
			$tb[Pessoas], 
			$tb[PessoasTipos], 
			$tb[DocumentosGerados], 
			$tb[ContasReceber], 
			$tb[Faturamentos] 
		WHERE
			$tb[ContasReceber].idDocumentosGerados = $tb[DocumentosGerados].id 
			AND $tb[DocumentosGerados].idPessoaTipo = $tb[PessoasTipos].id 
			AND $tb[PessoasTipos].idPessoa = $tb[Pessoas].id 
			AND $tb[DocumentosGerados].idFaturamento = $tb[Faturamentos].id
			AND $tb[Faturamentos].id = ".$matriz[idFaturamento];
	
	$consulta=consultaSQL($sql, $conn);
	
	$qtdRegistros=0;
	$valorTT=0;
	
	for($i=0;$i<contaConsulta($consulta);$i++) {
		$idPessoa=resultadoSQL($consulta, $i, "idPessoa");
		$dtVencimento=converteData(resultadoSQL($consulta, $i, "dtVencimento"), "banco", "remessa");
		$valor=resultadoSQL($consulta, $i, "valor");
		$nomeCliente=resultadoSQL($consulta, $i, "nomePessoa");
		
		# codigo do registro
		$conteudoArquivo.="E";
		# identificacao do cliente na empresa
		$conteudoArquivo.=exportaDados($idPessoa, "", "left", 25, " ");
		# Agencia para debito
		$conteudoArquivo.=exportaDados($matriz[cobrancaAgencia], "N", "right", 4, "0");
		# Identificacao do Cliente no banco
		//$conteudoArquivo.=exportaDados($getCodigoClienteBanco($idPessoa), "X", "left", 14, " ");
$conteudoArquivo.=exportaDados("[codigo]", "X", "left", 14, " ");
		# Data do vencimento
		$conteudoArquivo.=exportaDados($dtVencimento, "N", "left", 8, "0");
		# Valor do débito
		$conteudoArquivo.=exportaDados($valor, "N", "right", 15, "0");
		# codigo da moeda 01 UFIR (5 casas decimais) / 03 REAL (2 casas decimais)
		$conteudoArquivo.="03";
		# uso da empresa
		$conteudoArquivo.=exportaDados($nomeCliente, "X", "left", 60, " ");
		# livre
		$conteudoArquivo.=exportaDados('', "X", "left", 20, " ");
		# codigo do movimento: 0 debito normal / 1 cancelamento ou lancamento anterior ainda nao efetivado
		$conteudoArquivo.="0\n";
		
		$valorTT+=$valor;
		$qtdRegistros++;
	}
	
	### TRAILER DE ARQUIVO
	# codigo do registro
	$conteudoArquivo.="Z";
	# Quantidade de registro
	$conteudoArquivo.=exportaDados($qtdRegistros, "N", "right", 6, "0");
	# Valor total
	$conteudoArquivo.=exportaDados($valorTT, "N", "right", 17, "0");
	# livre
	$conteudoArquivo.=exportaDados('', "X", "left", 126, " ")."\n";

	return($conteudoArquivo);
}

# Funcão para geração de arquivos - Banco do Brasil
function gerarArquivoRemessaBB($matriz) {

	global $conn, $tb, $corFundo, $corBorda;
	
	# Data Atual
	$data=dataSistema();

	# sequencia de registros
	$sequenciaArquivo=1;
	
	# Variável de conteúdo de arquivo
	$conteudoArquivo='';

	### HEADER DE ARQUIVO
	# Numero do banco 1-3=3
	$conteudoArquivo.=exportaDados($matriz[bancoNumero], 'N', 'right', 3, 0);
	# Lote de Serviço 4-7=4
	$conteudoArquivo.="0000";
	# Registro Header de arquivo 8-8=1
	$conteudoArquivo.="0";
	# Uso FEBRABAN 9-17=9
	$conteudoArquivo.=exportaDados('', '', 'right', 9, ' ');
	# Tipo de inscrição da empresa 18-18=1 [1-CPF 2-CGC]
	$conteudoArquivo.="2";
	# Numero de inscrição da empresa 19-32=14
	$conteudoArquivo.=cpfVerificaFormato($matriz[cobrancaCNPJ]);
	# Numero convenio com o banco 33-52=20
	$conteudoArquivo.=exportaDados('', '', 'right', 20, ' ');
	# Agencia mantenedora da conta 53-57=5
	$conteudoArquivo.=exportaDados($matriz[cobrancaAgencia], '', 'right', 5, '0');
	# Digito da agencia 58-58=1
	$conteudoArquivo.=exportaDados($matriz[cobrancaAgenciaDig], '', 'right', 1, ' ');
	# Numero da conta correte 59-70=12
	$conteudoArquivo.=exportaDados($matriz[cobrancaConta], '', 'right', 12, '0');
	# Digito da conta corrente 71-71=1
	$conteudoArquivo.=exportaDados($matriz[cobrancaContaDig], '', 'right', 1, ' ');;
	# Digito Verificador Agencia/Conta 72-72=1
	$conteudoArquivo.=exportaDados($matriz[cobrancaAgenciaContaDig], '', 'right', 1, ' ');;
	# Nome da empresa 73-102=30
	$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($matriz[cobrancaTitular],'maiuscula'), '', 'left', 30, ' ');
	# Nome do Banco 103-132=30
	$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($matriz[bancoNome],'maiuscula'), '', 'left', 30, ' ');
	# Uso exclusivo Febraban 133-142=10
	$conteudoArquivo.=exportaDados('', '', 'left', 10, ' ');
	# Codigo Remessa/Retorno 143-143=1 [1-REM 2-RET]
	$conteudoArquivo.=1;
	# Data de geração do arquivo 144-151=8 [DDMMAAAA]
	$conteudoArquivo.=exportaDados($data[dia].$data[mes].$data[ano], '', 'right', 8, '0');
	# Hora de geração do arquivo 152-157=6 [HHMMSS]
	$conteudoArquivo.=exportaDados($data[hora].$data[min].$data[seg], '', 'right', 6, '0');
	# Numero sequencial do arquivo 158-163=6
	$conteudoArquivo.=exportaDados($matriz[numeroArquivo], '', 'right', 6, '0');
	# Numero da versão do Layout 164-166=3 [fixo: 030]
	$conteudoArquivo.=exportaDados('030', '', 'left', 3, ' ');
	# Densidade de gravação do arquivo 167-171=5 [fixo: 00000]
	$conteudoArquivo.=exportaDados('00000', '', 'left', 5, ' ');
	# Para uso reservado do banco 172-191=20
	$conteudoArquivo.=exportaDados('', '', 'left', 20, ' ');
	# Para uso reservado da empresa 192-211=20
	$conteudoArquivo.=exportaDados('', '', 'left', 20, ' ');
	# Uso exclusivo Febraban/CNAB	212-222=11
	$conteudoArquivo.=exportaDados('', '', 'left', 11, ' ');
	# Identificação cobrançca s/papel 223-225=3
	$conteudoArquivo.=exportaDados('', '', 'left', 3, ' ');
	# Uso exclusivo das VANS 226-228=3
	$conteudoArquivo.=exportaDados('', '', 'left', 3, ' ');
	# Tipo de serviço 229-230=2
	$conteudoArquivo.=exportaDados('', '', 'left', 2, ' ');
	# Codigos das ocorrências 231-240=10
	$conteudoArquivo.=exportaDados('', '', 'left', 10, ' ');
	# QUEBRA LINHA
	$conteudoArquivo.="\n";
	##### FIM DE HEADER DE ARQUIVO


	# Incrementar sequencia do Arquivo
	$sequenciaArquivo++;


	### HEADER DE LOTE
	# Codigo do banco na compensação 1-3=3
	$conteudoArquivo.=exportaDados($matriz[bancoNumero], 'N', 'right', 3, 0);
	# Lote de servico 4-7=4
	$conteudoArquivo.=exportaDados(1, 'N', 'right', 4, 0);
	# Registro Header do lote 8-8=1
	$conteudoArquivo.='1';
	# Tipo de operacao 9-9=1 [R=Arquivo Remessa T=Arquivo Retorno]
	$conteudoArquivo.='R';
	# Tipo de servicos 10-11=2 (fixo: 01)
	$conteudoArquivo.='01';
	# Forma de lançamento 12-13=2 (ZEROS)
	$conteudoArquivo.=exportaDados('', 'N', 'right', 2, 0);
	# Numero da Versao do layout do lote 14-16=3 (fixo: 020)
	$conteudoArquivo.='020';
	# Uso exclusivo FEBRABAN/CNAB 17-17=1 (BRANCOS)
	$conteudoArquivo.=exportaDados('', 'N', 'right', 1, ' ');
	# Tipo de Inscricao da empresa 18-18=1 [1-CPF 2-CGC]
	$conteudoArquivo.='2';
	# Numero de inscricao da empresa 19-33=15
	$conteudoArquivo.=exportaDados(cpfVerificaFormato($matriz[cobrancaCNPJ]), 'N', 'right', 15, 0);
	# Numero convenio com o banco 34-53=20
	$conteudoArquivo.=exportaDados($matriz[cobrancaConvenio], '', 'left', 20, ' ');
	# Agencia mantenedora da conta 54-58=5
	$conteudoArquivo.=exportaDados($matriz[cobrancaAgencia], '', 'right', 5, '0');
	# Digito da agencia 59-59=1
	$conteudoArquivo.=exportaDados($matriz[cobrancaAgenciaDig], '', 'left', 1, ' ');
	# Numero da conta correte 60-71=12
	$conteudoArquivo.=exportaDados($matriz[cobrancaConta], '', 'right', 12, '0');
	# Digito da conta corrente 72-72=1
	$conteudoArquivo.=exportaDados($matriz[cobrancaContaDig], '', 'left', 1, ' ');
	# Digito Verificador Agencia/Conta 73-73=1
	$conteudoArquivo.=exportaDados($matriz[cobrancaAgenciaContaDig], '', 'left', 1, ' ');
	# Nome da empresa 74-103=30
	$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($matriz[cobrancaTitular],'maiuscula'), '', 'left', 30, ' ');
	# Mensagem 1 104-143=40
	$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($matriz[mensagem],'maiuscula'),'','left','40',' ');
	# Mensagem 2 144-183=40
	$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($matriz[mensagem2],'maiuscula'),'','left','40',' ');
	# Numero Remessa/Retorno 184-191=8 -- BUSCAR NUMERO
	$conteudoArquivo.=exportaDados('','','right','8','0');
	# Data de Gravacao Remessa/Retorno 192-199=8
	$conteudoArquivo.=$data[dia].$data[mes].$data[ano];
	# Data de Credito 200-207=8
	$conteudoArquivo.=exportaDados('','','right','8','0');
	# Uso exclusivo FEBRABAN/CNAB 208-240=33
	$conteudoArquivo.=exportaDados('','','right','33',' ');
	# QUEBRA LINHA
	$conteudoArquivo.="\n";
	### FIM DE HEADER DE LOTE
	
	# Incrementar sequencia do arquivo
	$sequenciaArquivo++;
	
	# Iniciar sequencia de lote
	$sequenciaLote=1;
	
	
	$sql="SELECT
			$tb[DocumentosGerados].id idDocumentoGerado, 
			$tb[ContasReceber].valor valor, 
			$tb[ContasReceber].dtVencimento dtVencimento, 
			$tb[PessoasTipos].id idPessoaTipo, 
			$tb[Pessoas].nome nomePessoa, 
			$tb[Pessoas].razao razaoSocial, 
			$tb[Pessoas].id idPessoa, 
			$tb[Pessoas].tipoPessoa tipoPessoa 
		FROM
			$tb[Pessoas], 
			$tb[PessoasTipos], 
			$tb[DocumentosGerados], 
			$tb[ContasReceber], 
			$tb[Faturamentos] 
		WHERE
			$tb[ContasReceber].idDocumentosGerados = $tb[DocumentosGerados].id 
			AND $tb[DocumentosGerados].idPessoaTipo = $tb[PessoasTipos].id 
			AND $tb[PessoasTipos].idPessoa = $tb[Pessoas].id 
			AND $tb[DocumentosGerados].idFaturamento = $tb[Faturamentos].id
			AND $tb[Faturamentos].id = $matriz[idFaturamento]";
	
	$consulta=consultaSQL($sql, $conn);
	
	for($i=0;$i<contaConsulta($consulta);$i++) {
	
		$idDocumentoGerado=resultadoSQL($consulta, $i, 'idDocumentoGerado');
		$valor=formatarValoresArquivoRemessa(resultadoSQL($consulta, $i, 'valor'));
		$dtVencimento=formatarData(converteData(resultadoSQL($consulta, $i, 'dtVencimento'),'banco','formdata'));
		$idPessoa=resultadoSQL($consulta, $i, 'idPessoa');
		$idPessoaTipo=resultadoSQL($consulta, $i, 'idPessoaTipo');
		$tipoPessoa=resultadoSQL($consulta, $i, 'tipoPessoa');
		if($tipoPessoa=='F') $nomePessoa=formFormatarStringArquivoRemessa(resultadoSQL($consulta, $i, 'nomePessoa'),'maiuscula');
		elseif($tipoPessoa=='J') $nomePessoa=formFormatarStringArquivoRemessa(resultadoSQL($consulta, $i, 'razaoSocial'),'maiuscula');

		# Verificar Endereço Preferencial ou existente
		$enderecosPessoa=buscaEnderecosPessoas($idPessoaTipo, 'idPessoaTipo','igual','idTipo');
		
		# Selecionar Endereço
		$endereco=enderecoSelecionaPreferencial($enderecosPessoa, $matriz[tipoEndereco]);
		
		# Selecionar Documentos
		$documento=documentoSelecionaPreferencial($idPessoa, $tipoPessoa);
		
		### REMESSA - Registro P
		# Codigo do Banco 1-3=3
		$conteudoArquivo.=exportaDados($matriz[bancoNumero], 'N', 'right', 3, 0);
		# Lote de Servico 4-7=4
		$conteudoArquivo.=exportaDados(1, 'N', 'right', 4, 0);
		# Registro Detalhe 8-8=1
		$conteudoArquivo.='3';
		# Numero sequencial do registro no lote 9-13=5
		$conteudoArquivo.=exportaDados($sequenciaLote, 'N', 'right', 5, 0);
		# Codigo segmento do registro detalhe 14-14=1 (fixo: P)
		$conteudoArquivo.='P';
		# Uso excluviso do FEBRABAN 15-15=1
		$conteudoArquivo.=' ';
		# Codigo de Movimento 16-17=2
		$conteudoArquivo.='01';
		# Agencia Mantenedora da Conta 18-22=5
		$conteudoArquivo.=exportaDados($matriz[cobrancaAgencia], 'N', 'right', 5, 0);
		# Digito Verificador da Agencia 23-23=1
		$conteudoArquivo.=exportaDados($matriz[cobrancaAgenciaDig],'N', 'right', 1, 0);
		# Numero da Conta Corrente 24-35=12
		$conteudoArquivo.=exportaDados($matriz[cobrancaConta],'N', 'right', 12, 0);
		# Digito Verificador da Conta 36-36=1
		$conteudoArquivo.=exportaDados($matriz[cobrancaContaDig],'N', 'right', 1, 0);
		# Digito Verificador da agencia/conta 37-37=1
		$conteudoArquivo.=' ';
		# Identificação do título no banco 38-57=20
		$conteudoArquivo.=exportaDados('','N', 'right', 20, 0);
		# Codigo da carteira 58-58=1 
		$conteudoArquivo.='1';
		# Forma de cadastramento do titulo no banco 59-59=1 (1-com cadast. 2-sem cadast.)
		$conteudoArquivo.='1';
		# Tipo de documento 60-60=1 (1-tradicional 2-escritural)
		$conteudoArquivo.='1';
		# Identificação da emissao do bloqueto 61-61=1
		$conteudoArquivo.='1';
		# Identificação da distribuicao 62-62=1 (1-banco 2-cliente)
		$conteudoArquivo.='1';
		# Numero do documento de cobranca 63-77=15
		$conteudoArquivo.=exportaDados("0000000000",'N', 'left', 15, ' ');
		# Data de vencimento do titulo 78-85=8
		$conteudoArquivo.=exportaDados($dtVencimento,'N', 'right', 8, 0);
		# Valor nominal do titulo 86-100=15
		$conteudoArquivo.=exportaDados($valor,'N', 'right', 15, 0);
		# Agencia encarregada da cobranca 101-105=5
		$conteudoArquivo.=exportaDados(0,'N', 'right', 5, 0);
		# Digito verificador da agencia 106-106=1
		$conteudoArquivo.='0';
		# Especie do titulo 107-108=2
		$conteudoArquivo.='02';
		# Identif. de titulo aceito/nao aceito 109-109=1
		$conteudoArquivo.='N';
		# Data de emissão do titulo 110-117=8
		$conteudoArquivo.=exportaDados($data[dia].$data[mes].$data[ano],'N', 'right', 8, 0);
		# Codigo dos juros de mora 118-118=1
		$conteudoArquivo.='3';
		# Data dos juros de mora 119-126=8
		$conteudoArquivo.=exportaDados(0,'N', 'right', 8, 0);
		# Juros de mora por dia/taxa 127-141=15
		$conteudoArquivo.=exportaDados(0,'N', 'right', 15, 0);
		# Codigo do desconto 1 142-142=1
		$conteudoArquivo.='0';
		# Data do desconto 1 143-150=8
		$conteudoArquivo.=exportaDados(0,'N', 'right', 8, 0);
		# Valor/Percentual a ser concedido 151-165=15
		$conteudoArquivo.=exportaDados(0,'N', 'right', 15, 0);
		# Valor do IOF a ser recolhido 166-180=15
		$conteudoArquivo.=exportaDados(0,'N', 'right', 15, 0);
		# Valor do abatimento 181-195=15
		$conteudoArquivo.=exportaDados(0,'N', 'right', 15, 0);
		# Identificacao do titulo na empresa 196-220=25
		$conteudoArquivo.=exportaDados($idDocumentoGerado,'N', 'right', 25, ' ');
		# Codigo para protesto 221-221=1 (1-dias corrid, 2-dias uteis, 3-nao protestar)
		$conteudoArquivo.='3';
		# Numero de dias para protesto 222-223=2
		$conteudoArquivo.='00';
		# Codigo para baixa/devolucao 224-224=1 (1-baixar/dev, 2-nao baixar/nao devolver)
		$conteudoArquivo.='2';
		# Numero de dias para baixa/devolucao 225-227=3 (dias corridos)
		$conteudoArquivo.=exportaDados('','N', 'right', 3, 0);
		# Codigo da moeda 228-229=2
		$conteudoArquivo.='09';
		# Numero do contr. da operacao d cred 230-239=10
		$conteudoArquivo.=exportaDados('','N', 'right', 10, 0);
		# Uso exclusivo FEBRABAN 240-240=1
		$conteudoArquivo.=' ';
		# QUERBRA DE LINHA
		$conteudoArquivo.="\n";
		### FIM DE REMESSA - Registro P
		
		
		# Incrementar numero do registro da remessa
		$sequenciaLote++;
		
		# Incrementar sequencia do arquivo
		$sequenciaArquivo++;
		
		### REMESSA - Registro Q
		# Codigo do Banco na compensacao 1-3=3
		$conteudoArquivo.=exportaDados($matriz[bancoNumero], 'N', 'right', 3, 0);
		# Lote de servico 4-7=4
		$conteudoArquivo.=exportaDados(1, 'N', 'right', 4, 0);
		# Registro detalhe 8-8=1
		$conteudoArquivo.='3';
		# Numero sequencial do reg. no lote 9-13=5
		$conteudoArquivo.=exportaDados($sequenciaLote, 'N', 'right', 5, 0);
		# Cod. segmento do reg. detalhe 14-14=1 (fixo: Q)
		$conteudoArquivo.='Q';
		# Uso exclusivo FEBRABAN 15-15=1 (BRANCOS)
		$conteudoArquivo.=' ';
		# Codigo de movimento 16-17=2 (01 entrada de titulo)
		$conteudoArquivo.='01';
		# Tipo de Inscricao 18-18=1
		if($tipoPessoa=='F') 	$conteudoArquivo.='1';
		elseif($tipoPessoa=='J') $conteudoArquivo.='2';
		# Numero de inscricao 19-33=15
		$documento=trim(cpfVerificaFormato($documento));
		$conteudoArquivo.=exportaDados($documento, 'N', 'right', 15, 0);
		# Nome 34-73=40
		$conteudoArquivo.=exportaDados($nomePessoa, 'N', 'left', 40, ' ');
		# Endereco 74-113=40
		$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($endereco[endereco]." ".$endereco[complemento],'maiuscula'), 'N', 'left', 40, ' ');
		# Bairro 114-128=15
		$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($endereco[bairro],'maiuscula'), 'N', 'left', 15, ' ');
		# CEP 129-133=5
		$conteudoArquivo.=exportaDados($endereco[cep_prefix], 'N', 'right', 5, ' ');
		# Sufixo do CEP 134-136=3
		$conteudoArquivo.=exportaDados($endereco[cep_sufix], 'N', 'right', 3, ' ');
		# Cidade 137-151=15
		$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($endereco[cidade],'maiuscula'), 'N', 'left', 15, ' ');
		# Unidade da Federecao 152-153=2
		$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($endereco[uf],'maiuscula'), 'N', 'left', 2, ' ');
		# Tipo de Inscricao 154-154=1
		$conteudoArquivo.=exportaDados('0', 'N', 'right', 1, '0');
		# Tipo de Inscricao 155-169=15
		$conteudoArquivo.=exportaDados('0', 'N', 'right', 15, '0');
		# Nome do sacador/avalista 170-209=40
		$conteudoArquivo.=exportaDados(' ', 'N', 'left', 40, ' ');
		# Cod. bco. corresp. na compensacao 210-212=3 (fixo: 000)
		$conteudoArquivo.='000';
		# Nosso Num. no Bco. Correspondente 213-232=20
		$conteudoArquivo.=exportaDados(' ', 'N', 'left', 20, ' ');
		# Uso exclusivo FEBRABAN 233-240=8 (BRANCOS)
		$conteudoArquivo.=exportaDados(' ', 'N', 'left', 8, ' ');
		# QUEBRA DE LINHA
		$conteudoArquivo.="\n";
		### FIM REMESSA - Registro Q
		
		# Incrementar numero do registro da remessa
		$sequenciaLote++;
		
		# Incrementar sequencia do arquivo
		$sequenciaArquivo++;
	} #fim for
	
	
	#### TRAILER DE LOTE
	# Lote de servico 4-7=4
	# Codigo do Banco na compensacao 1-3=3
	$conteudoArquivo.=exportaDados($matriz[bancoNumero], 'N', 'right', 3, 0);
	# Lote de servico 4-7=4
	$conteudoArquivo.=exportaDados(1, 'N', 'right', 4, 0);
	# Registro trailer do lote 8-8=1 (fixo: 5)
	$conteudoArquivo.='5';
	# Uso exclusivo do FEBRABAN 9-17=9 (BRANCOS)
	$conteudoArquivo.=exportaDados(' ', 'N', 'left', 9, ' ');
	# Quantidade de registros do lote 18-23=6
	$conteudoArquivo.=exportaDados(++$sequenciaLote, 'N', 'right', 6, 0);
	# Quantidade de Titulos em cobranca 24-29=6
	$conteudoArquivo.=exportaDados('0', 'N', 'right', 6, 0);
	# Valor Total dos titulos em carteira 30-46=17
	$conteudoArquivo.=exportaDados('0', 'N', 'right', 17, 0);
	# aki ele juntou
	# Quantidade de titulos em cobranca 47-69=23
	$conteudoArquivo.=exportaDados('', '', 'right', 23, ' ');
	# Quantidade de Titulos em Cobranca 70-75=6
	$conteudoArquivo.=exportaDados('0', 'N', 'right', 6, 0);
	# Valor Total dos Titulos em carteira 76-92=17
	$conteudoArquivo.=exportaDados('0', 'N', 'right', 17, 0);
	# ele juntou aki
	# Quantidade de Titulos em cobranca 93-123=31
	$conteudoArquivo.=exportaDados('', '', 'right', 31, ' ');
	# Uso exclusivo FEBRABAN 124-240=117
	$conteudoArquivo.=exportaDados(' ', '', 'right', 117, ' ');
	# QUEBRA DE LINHA
	$conteudoArquivo.="\n";
	### FIM DE TRAILER DE LOTE
	
	
	# Incrementar sequencia do arquivo
	$sequenciaArquivo++;

	
	### TRAILER DE ARQUIVO
	# Codigo do banco na compensasao 1-3=3
	$conteudoArquivo.=exportaDados($matriz[bancoNumero], '', 'right', 3, '0');
	# Lote de Servico 4-7=4 (fixo: 0000)
	$conteudoArquivo.="9999";
	# Registro trailer de arquvo 8-8=1 (fixo: 9)
	$conteudoArquivo.="9";
	# Uso exclusivo FEBRABAN 9-17=9 (fixo: BRANCOS)
	$conteudoArquivo.=exportaDados('', '', 'left', 9, ' ');
	# Quantidade de lotes do arquivo 18-23=6 (Numero de registros tipo - 1)
	$conteudoArquivo.=exportaDados('1', '', 'right', 6, '0');; #### SOMAR QUANTIDADE
	# Quantidade de registros do arquivo 24-29=6 (Numero de registros tipo 0+1+3+5+9)
	$conteudoArquivo.=exportaDados($sequenciaArquivo, '', 'right', 6, '0'); #### SOMAR QUANTIDADE
	# Quantidade de contas p/ conc. - lotes 30-35=6 (Numero de registros tipo -1 oper-E)
	$conteudoArquivo.=exportaDados('', '', 'right', 6, '0'); #### SOMAR QUANTIDADE
	# Uso exclusivo FEBRABAN/CNAB 26-240=205
	$conteudoArquivo.=exportaDados('', '', 'left', 205, ' ');
	# QUEBRA DE LINHA
	$conteudoArquivo.="\n";
	### FIM DE TRAILER DE ARQUIVO

	return($conteudoArquivo);
}





# Funcão para geração de arquivos - ITAU
function gerarArquivoRemessaITAU($matriz) {

	global $conn, $tb, $corFundo, $corBorda, $sequenciaArquivo;


	# sequencia de registros
	$sequenciaArquivo=1;
	
	# Variável de conteúdo de arquivo
	$conteudoArquivo='';

	# data do sistema
	$data=dataSistema();
	
	// por Jose Ambiel - 23/03/09
	// recuperar data de desconto e porcentagem do desconto para aplicar no arquivo remessa
	$dtDesconto = formatarData($matriz[data_desconto]);
	$dtDesconto = substr($dtDesconto,0,4).substr($dtDesconto, -2);
	$porcentagemDesconto = $matriz[porcentagem]/100;
	
	### REGISTRO HEADER
	# TIPO DO REGISTRO
	$conteudoArquivo.=exportaDados(0, '', 'right', 1, 0);
	# TIPO DA OPERACAO
	$conteudoArquivo.=exportaDados(1, '', 'right', 1, 0);
	# LITERAL DO TIPO DA OPERACAO
	$conteudoArquivo.=exportaDados("REMESSA", '', 'left', 7, ' ');
	# TIPO DE SERVICO
	$conteudoArquivo.=exportaDados(1, '', 'right', 2, 0);
	# LITERAL DO TIPO DE SERVICO
	$conteudoArquivo.=exportaDados("COBRANCA", '', 'left', 15, ' ');
	# AGENCIA MANTEDORA DA CONTA
	$conteudoArquivo.=exportaDados($matriz[cobrancaAgencia], '', 'right', 4, '0');
	# ZEROS COMPLEMENTO
	$conteudoArquivo.=exportaDados(0, '', 'right', 2, '0');
	# NUMERO DA CONTA
	$conteudoArquivo.=exportaDados($matriz[cobrancaConta], '', 'right', 5, '0');
	# DAC VERIFICADOR
	$conteudoArquivo.=exportaDados($matriz[cobrancaContaDig], '', 'right', 1, '0');
	# BRANCOS COMPLEMENTO
	$conteudoArquivo.=exportaDados('', '', 'left', 8, ' ');
	# NOME DA EMPRESA
	$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($matriz[cobrancaTitular],'maiuscula'), '', 'left', 30, ' ');
	# NUMERO DO BANCO
	$conteudoArquivo.=exportaDados($matriz[bancoNumero], '', 'right', 3, 0);
	# NOME DO BANCO
	$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($matriz[bancoNome],'maiuscula'), '', 'left', 15, ' ');
	# DATA DA GERACAO [DDMMAA]
	$conteudoArquivo.=exportaDados($data[dia].$data[mes].substr($data[ano], 2, 2), '', 'right', 6, '0');
	# BRANCOS COMPLEMENTO
	$conteudoArquivo.=exportaDados('', '', 'left', 294, ' ');
	# NUMERO SEQUENCIAL DO REGISTRO NO ARQUIVO
	$conteudoArquivo.=exportaDados($sequenciaArquivo++, '', 'right', 6, 0);
	# QUEBRA DE LINHA
	$conteudoArquivo.="\r\n";
	
	$sql="SELECT
			$tb[DocumentosGerados].id idDocumentoGerado, 
			$tb[ContasReceber].valor valor, 
			$tb[ContasReceber].dtVencimento dtVencimento, 
			$tb[PessoasTipos].id idPessoaTipo, 
			$tb[Pessoas].nome nomePessoa, 
			$tb[Pessoas].razao razaoSocial, 
			$tb[Pessoas].id idPessoa,
			$tb[Pessoas].site site,
			$tb[Pessoas].tipoPessoa tipoPessoa,
			$tb[Faturamentos].id idFaturamentos
		FROM
			$tb[Pessoas], 
			$tb[PessoasTipos], 
			$tb[DocumentosGerados], 
			$tb[ContasReceber], 
			$tb[Faturamentos] 
		WHERE
			$tb[ContasReceber].idDocumentosGerados = $tb[DocumentosGerados].id 
			AND $tb[DocumentosGerados].idPessoaTipo = $tb[PessoasTipos].id 
			AND $tb[PessoasTipos].idPessoa = $tb[Pessoas].id 
			AND $tb[DocumentosGerados].idFaturamento = $tb[Faturamentos].id
			AND $tb[Faturamentos].id = $matriz[idFaturamento]";
	
	$consulta=consultaSQL($sql, $conn);
			
	for($i=0;$i<contaConsulta($consulta);$i++) {
	
		$idDocumentoGerado=resultadoSQL($consulta, $i, 'idDocumentoGerado');
		
		//por jose ambiel - 24/10/2008
		// adicionado novo campo na pesquisa para utilizar na cobrança mensagem $valorMensagem 
		$valorMensagem = resultadoSQL($consulta, $i, 'valor');
		$valorMensagem = banco2real($valorMensagem);
		$valor=formatarValoresArquivoRemessa($valorMensagem);
		//$valor=formatarValoresArquivoRemessa(resultadoSQL($consulta, $i, 'valor'));
		
		if(strlen(trim($matriz[data_vencimento]))>0) {
			# Utilizar data de vencimento informada em form
			$dtVencimento=formatarData($matriz[data_vencimento]);
		}
		else {
			# Utilizar data de vencimento do documento
			$dtVencimento=formatarData(converteData(resultadoSQL($consulta, $i, 'dtVencimento'),'banco','formdata'));
		}
		
		$dtVencimentoRegistro=substr($dtVencimento,0,4).substr($dtVencimento, -2);
		$dtVencimentoMensagem=substr($dtVencimento,0,2)."/".substr($dtVencimento, 2, 2)."/".substr($dtVencimento, -4);
		
		$idPessoa=resultadoSQL($consulta, $i, 'idPessoa');
		$idPessoaTipo=resultadoSQL($consulta, $i, 'idPessoaTipo');
		$tipoPessoa=resultadoSQL($consulta, $i, 'tipoPessoa');
		$idFaturamentos= resultadoSQL($consulta,$i,'idFaturamentos');
		
		// por Jose Ambiel - 29/01/2009
		// consultas para que na hora da mensagem possamos saber qual o tipo de cobrança
		// se terá o layout da mensagem ou não
		$faturamento = dadosFaturamento($idFaturamentos);
		$formaCobranca = dadosFormaCobranca($faturamento[idFormaCobranca]);
		$tipoCarteira = dadosTipoCarteira($formaCobranca[idTipoCarteira]);
		
		if($tipoPessoa=='F') $nomePessoa=formFormatarStringArquivoRemessa(resultadoSQL($consulta, $i, 'nomePessoa'),'maiuscula');
		elseif($tipoPessoa=='J') $nomePessoa=formFormatarStringArquivoRemessa(resultadoSQL($consulta, $i, 'razaoSocial'),'maiuscula');

		# Verificar Endereço Preferencial ou existente
		$enderecosPessoa=buscaEnderecosPessoas($idPessoaTipo, 'idPessoaTipo','igual','idTipo');
		
		# Selecionar Endereço
		$endereco=enderecoSelecionaPreferencial($enderecosPessoa, $matriz[tipoEndereco]);
		
		# Selecionar Documentos
		$documento=documentoSelecionaPreferencial($idPessoa, $tipoPessoa);	
		
		### REGISTRO TRANSAÇÃO (obrigatorio)
		# IDENTIFICAÇÃO DO REGISTRO - 1
		#================================= pega os dados ClienteBanco =====================================#
		
		// ESTA CONSULTA TRAS O "ID" DI TIPO DE PESSOA PARA SELECIONAR NA TABELA DE RELACIONAMENTO CLIENTEBANCO
		$sql="SELECT
			  	$tb[PlanosPessoas].id, $tb[PlanosPessoas].desconto
			  FROM
				$tb[PlanosPessoas], $tb[Faturamentos]
			  WHERE
				$tb[PlanosPessoas].idFormaCobranca = $tb[Faturamentos].idFormaCobranca AND
				$tb[Faturamentos].id= $idFaturamentos AND
				$tb[PlanosPessoas].idPessoaTipo= $idPessoaTipo;
		";
		$qry= consultaSQL($sql, $conn);
		$idPessoasPlanos= resultadoSQL($qry,0,'id');
		$cliBan= '';
		#==================================================================================================#
		### REGISTRO DETALHE (obrigatorio)
		# TIPO DO REGISTRO
		$conteudoArquivo.=exportaDados(1, '', 'right', 1, 0);
		# CODIGO DE INSCRICAO 
		$conteudoArquivo.=exportaDados(2, '', 'right', 2, 0);
		# NUMERO DE INSCRICAO
		$conteudoArquivo.=exportaDados(cpfVerificaFormato($matriz[cobrancaCNPJ]), '', 'right', 14, 0);
		# AGENCIA MANTEDORA DA CONTA
		$conteudoArquivo.=exportaDados($matriz[cobrancaAgencia], '', 'right', 4, '0');
		# ZEROS COMPLEMENTO
		$conteudoArquivo.=exportaDados(0, '', 'right', 2, '0');
		# NUMERO DA CONTA
		$conteudoArquivo.=exportaDados($matriz[cobrancaConta], '', 'right', 5, '0');
		# DAC VERIFICADOR
		$conteudoArquivo.=exportaDados($matriz[cobrancaContaDig], '', 'right', 1, '0');
		# BRANCOS COMPLEMENTO
		$conteudoArquivo.=exportaDados('', '', 'left', 4, ' ');
		# CODIGO DA INSTRUCAO/ALEGACAO 
		$conteudoArquivo.=exportaDados(0, '', 'right', 4, 0);
		# USO DA EMPRESA 
		$conteudoArquivo.=exportaDados($idDocumentoGerado, '', 'left', 25, ' ');
		# NOSSO NUMERO -> NOTA3 
		$conteudoArquivo.=exportaDados(0, '', 'right', 8, '0');
		# QUANTIDADE DE MOEDA 
		$conteudoArquivo.=exportaDados(0, '', 'right', 8, '0');
		# QUANTIDADE DE MOEDA 
		$conteudoArquivo.=exportaDados(0, '', 'right', 5, '0');
		# NUMERO DA CARTEIRA 
		$conteudoArquivo.=exportaDados(112, '', 'right', 3, '0');
		# USO DO BANCO
		$conteudoArquivo.=exportaDados('', '', 'left', 21, ' ');
		# CODIGO DA CARTEIRA
		$conteudoArquivo.=exportaDados('I', '', 'left', 1, ' ');
		# CODIGO DA OCORRENCIA 
		$conteudoArquivo.=exportaDados(1, '', 'right', 2, 0);
		# NUMERO DO DOCUMENTO -> NOTA18
		$conteudoArquivo.=exportaDados($idDocumentoGerado, '', 'left', 10, ' ');
		# DATA DO VENCIMENTO
		$conteudoArquivo.=exportaDados($dtVencimentoRegistro, '', 'right', 6, '0');
		# VALOR DO TITULO 
		$conteudoArquivo.=exportaDados($valor, '', 'right', 13, '0');
		# NUMERO DO BANCO
		$conteudoArquivo.=exportaDados($matriz[bancoNumero], '', 'right', 3, 0);
		# AGENCIA COBRADORA
		$conteudoArquivo.=exportaDados(0, '', 'right', 5, '0');
		# ESPECIE 
		$conteudoArquivo.=exportaDados('01', '', 'left', 2, 0);
		# ACEITE 
		$conteudoArquivo.=exportaDados('A', '', 'left', 1, ' ');
		# DATA DE EMISSAO
		$conteudoArquivo.=exportaDados($data[dia].$data[mes].substr($data[ano], -2), '', 'right', 6, '0');
		# INSTRUCAO1 94 = MENSAGEM NOS BLOQUETOS COM 40 POSICOES
		$conteudoArquivo.=exportaDados(94, '', 'left', 2, ' ');
		# INSTRUCAO2
		$conteudoArquivo.=exportaDados(0, '','left', 2, ' ');
		#$conteudoArquivo.=exportaDados(0, '', 'left', 2, ' '); // original
		# JUROS DE 1 DIA 
		$conteudoArquivo.=exportaDados(0, '', 'right', 11, '0');
		# JUROS DE 1 DIA
		$conteudoArquivo.=exportaDados(0, '', 'right', 2, '0');
		
		//por Jose Ambiel - 01/04/2009
		// verificar se foi selecionado algum desconto e setado uma data para fazer verificacoes
		// de clientes com planos que permitem o desconto com pagamento antecipado
		$valorDesconto = 0;
		if($porcentagemDesconto && $dtDesconto) {
			$valorDesconto = calculaValorDescontoGerarCobranca($idDocumentoGerado, $porcentagemDesconto);
		}
		
		if ($valorDesconto){
			# DESCONTO ATE
			$conteudoArquivo.=exportaDados($dtDesconto, '', 'right', 6, '0');
			# VALOR DO DESCONTO 
			$conteudoArquivo.=exportaDados(formatarValoresArquivoRemessa($valorDesconto), '', 'right', 13, '0');
		} else {
			# DESCONTO ATE
			$conteudoArquivo.=exportaDados(0, '', 'right', 6, '0');
			# VALOR DO DESCONTO 
			$conteudoArquivo.=exportaDados(0, '', 'right', 13, '0');
		}
		
		#$conteudoArquivo.=exportaDados(0, '', 'right', 2, '0');
		# VALOR DO IOF
		$conteudoArquivo.=exportaDados(0, '', 'right', 11, '0');
		# VALOR DO IOF
		$conteudoArquivo.=exportaDados(0, '', 'right', 2, '0');
		# ABATIMENTO
		$conteudoArquivo.=exportaDados(0, '', 'right', 11, '0');
		# ABATIMENTO
		$conteudoArquivo.=exportaDados(0, '', 'right', 2, '0');
		# CODIGO DE INSCRICAO (01 = CPF, 02 = CNPJ)
		if($tipoPessoa=='F') 	$conteudoArquivo.=exportaDados(1, '', 'right', 2, '0');
		elseif($tipoPessoa=='J') $conteudoArquivo.=exportaDados(2, '', 'right', 2, '0');
		# NUMERO DE INSCRICAO (CPF / CNPJ)
		$conteudoArquivo.=exportaDados(cpfVerificaFormato($documento), '', 'right', 14, '0');
		# NOME SACADO
		$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($nomePessoa,'maiuscula'), '', 'left', 30, ' ');
		# BRANCOS COMPLEMENTO
		$conteudoArquivo.=exportaDados('', '', 'left', 10, ' ');
		# ENDERECO
		$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($endereco[endereco]." ".$endereco[complemento],'maiuscula'), '', 'left', 40, ' ');
		# BAIRRO
		$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($endereco[bairro],'maiuscula'), '', 'left', 12, ' ');
		# CEP
		$conteudoArquivo.=exportaDados(cpfVerificaFormato($endereco[cep]), '', 'right', 8, '0');
		# CIDADE
		$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($endereco[cidade],'maiuscula'), '', 'left', 15, ' ');
		# ESTADO
		$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($endereco[uf],'maiuscula'), '', 'left', 2, ' ');
		
		if ($matriz[sem_mensagem] != "S" ){
			#INSTRUCAO DE COBRANCA
			$mens = $matriz[mensagem];
			$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($mens,'maiuscula'), '', 'left', 40, ' ');
		}	
		else {
			# NOME DO SACADOR/AVALISTA
			#$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($matriz[cobrancaTitular],'maiuscula'), '', 'left', 30, ' ');
			# DEIXA EM BRANCO - JOSE AMBIEL - 03/11/2008
			$conteudoArquivo.=exportaDados('', '', 'left', 40, ' ');
		}
		
		# BRANCOS COMPLEMENTO
		#$conteudoArquivo.=exportaDados('', '', 'left', 4, ' ');
		# DATA DE MORA
		#$conteudoArquivo.=exportaDados(0, '', 'right', 6, '0');
		# PRAZO
		$conteudoArquivo.=exportaDados(0, '', 'right', 2, '0');
		# BRANCOS COMPLEMENTO
		$conteudoArquivo.=exportaDados('', '', 'left', 1, ' ');
		# NUMERO SEQUENCIAL DO REGISTRO NO ARQUIVO
		$conteudoArquivo.=exportaDados($sequenciaArquivo++, '', 'right', 6, 0);
		# QUEBRA DE LINHA
		$conteudoArquivo.="\r\n";
	
		
		//por jose ambiel - 27/03/09
		//verifico o tipo de carteira antes porque o programa do ITAU é burro e não sabe tratar
		//arquivo remessa com mensagem e/ou sem registro de e-mail :[
		
		if ($tipoCarteira[valor] != 'M') {
			if ($endereco[email] ) {
			
				### REGISTRO DETALHE (opcional)
				# TIPO DO REGISTRO
				$conteudoArquivo.=exportaDados(5, '', 'right', 1, 0);
				# EMAIL
				$conteudoArquivo.=exportaDados($endereco[email], '', 'left', 120, ' ');
				# CODIGO DA INSCRICAO
				$conteudoArquivo.=exportaDados(0, '', 'right', 2, 0);
				# NUMERO DA INSCRICAO
				$conteudoArquivo.=exportaDados(0, '', 'right', 14, 0);
				# ENDERECO
				$conteudoArquivo.=exportaDados('', '', 'left', 40, ' ');
				# BAIRRO
				$conteudoArquivo.=exportaDados('', '', 'left', 12, ' ');
				# CEP
				$conteudoArquivo.=exportaDados(0, '', 'right', 8, '0');
				# CIDADE
				$conteudoArquivo.=exportaDados('', '', 'left', 15, ' ');
				# ESTADO
				$conteudoArquivo.=exportaDados('', '', 'left', 2, ' ');
				# BRANCOS COMPLEMENTO
				$conteudoArquivo.=exportaDados('', '', 'left', 180, ' ');
				# NUMERO SEQUENCIAL DO REGISTRO NO ARQUIVO
				$conteudoArquivo.=exportaDados($sequenciaArquivo++, '', 'right', 6, 0);
				# QUEBRA DE LINHA
				$conteudoArquivo.="\r\n";
		
			}
		}
		else {
		
			// POR JOSE AMBIEL - 20/10/2008
			// Implementação do layout de cobrança mensagem do banco ITAU
					
			#### REGISTRO MENSAGEM (FRENTE)
			# TIPO DO REGISTRO
			$conteudoArquivo.=exportaDados(7, '', 'right', 1, 0);
			# CODIGO FLASH
			$conteudoArquivo.=exportaDados($formaCobranca[codFlash], '', 'right', 3, ' ');
			# NUMERO DA LINHA A SER IMPRESSA
			$conteudoArquivo.=exportaDados(1, '', 'right', 2, 0);
			# CONTEUDO DA LINHA 1
			# ESPACOS EM BRANCOS
			$conteudoArquivo.=exportaDados('', '', 'left', 10, ' ');
			# CEDENTE
			$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($matriz[cobrancaTitular],'maiuscula'), '', 'left', 40, ' ');
			# CNPJ
			$conteudoArquivo.=exportaDados('  CNPJ - '.$matriz[cobrancaCNPJ], '', 'left', 34, ' ');
			# VENCIMENTO
			$conteudoArquivo.=exportaDados($dtVencimentoMensagem, '', 'left', 16, ' ');
			# BRANCOS COMPLEMENTOS
			$conteudoArquivo.=exportaDados('', '', 'right', 28, ' ');
			# NUMERO DA LINHA A SER IMPRESSA
			$conteudoArquivo.=exportaDados(3, '', 'right', 2, 0);
			# CONTEUDO DA LINHA 3
			# ESPACOS EM BRANCOS
			$conteudoArquivo.=exportaDados('', '', 'left', 4, ' ');
			# CPI
			$conteudoArquivo.=exportaDados('', '', 'left', 15, ' ');
			# CARTEIRA
			$conteudoArquivo.=exportaDados(112, '', 'left', 9, ' ');
			# ESPECIE
			$conteudoArquivo.=exportaDados('R$', '', 'left', 51, ' ');
			# AGENCIA/CODIGO DO CEDENTE
			$conteudoArquivo.=exportaDados($matriz[cobrancaAgencia].'/'.$matriz[cobrancaConta].'-'.$matriz[cobrancaContaDig], '', 'left', 21, ' ');
			# BRANCOS COMPLEMENTOS
			$conteudoArquivo.=exportaDados('', '', 'right', 28, ' ');
			# NUMERO DA LINHA A SER IMPRESSA
			$conteudoArquivo.=exportaDados(5, '', 'right', 2, 0);
			# CONTEUDO DA LINHA 5
			# ESPACOS EM BRANCOS
			$conteudoArquivo.=exportaDados('', '', 'left', 4, ' ');
			# DATA DO DOCUMENTO
			$conteudoArquivo.=exportaDados($data[dia].'/'.$data[mes].'/'.$data[ano], '', 'left', 16, ' ');
			# NUMERO DO DOCUMENTO
			$conteudoArquivo.=exportaDados($idDocumentoGerado, '', 'left', 12, ' ');
			# ESPACOS EM BRANCOS
			$conteudoArquivo.=exportaDados('', '', 'left', 8, ' ');
			# ESPECIE DO DOCUMENTO
			$conteudoArquivo.=exportaDados('DM', '', 'left', 14, ' ');
			# ACEITE
			$conteudoArquivo.=exportaDados('A', '', 'left', 8, ' ');
			# DATA DO PROCESSAMENTO
			$conteudoArquivo.=exportaDados('', '', 'left', 18, ' ');
			# VALOR DO DOCUMENTO
			$conteudoArquivo.=exportaDados($valorMensagem, '', 'left', 20, ' ');
			# BRANCOS COMPLEMENTOS
			$conteudoArquivo.=exportaDados('', '', 'right', 28, ' ');
			# NUMERO SEQUENCIAL DO REGISTRO NO ARQUIVO
			$conteudoArquivo.=exportaDados($sequenciaArquivo++, '', 'right', 6, 0);
			# QUEBRA DE LINHA
			$conteudoArquivo.="\r\n";
			
			# QUEBRA A MENSAGEM EM ARRAY
			$dadosMensagem = explodeDadosMensagem($matriz[msn]);
			
			# FOR PARA COLOCAR AS LINHAS NOS LUGARES CORRETOS
			$linha = 7;
			foreach ($dadosMensagem as $msn) {
				$conteudoArquivo.=exportaDadosMensagem($msn, $linha++, $formaCobranca[codFlash]);
			}
			
			# INDICA ULTIMA MENSAGEM DO REGISTRO
			$conteudoArquivo.=exportaDadosMensagem("", $linha++, $formaCobranca[codFlash], true);
			
		}	
		
	}
	
	#### REGISTRO TRAILER
	# TIPO DO REGISTRO
	$conteudoArquivo.=exportaDados(9, '', 'right', 1, 0);
	# BRANCOS COMPLEMENTO
	#$conteudoArquivo.=exportaDados('', '', 'left', 393, ' '); //original
	$conteudoArquivo.=exportaDados('', '', 'left', 350, ' ');
//	#INCLUSAO DA INSTRUCAO 01
//	$sql = "SELECT valor FROM ParametrosConfig WHERE parametro = 'instrucaoItau'";
//	$consulta = consultaSQL( $sql, $conn );
//	if( $consulta && contaConsulta( $consulta ) > 0 ) {
//		$mens = resultadoSQL( $consulta, '0', 'valor' );
//	}
	# por jose ambiel - 03/11/2008
	# comentei linhas abaixo e add uma para sempre inserir espacos em brancos nestes campos
	#$mens = $matriz[mensagem];
	#$conteudoArquivo .= exportaDados( $mens, '', 'left', 40, ' ' );
	#$conteudoArquivo .= exportaDados('', '', 'left', 3, ' ');
	$conteudoArquivo .= exportaDados( '', '', 'left', 43, ' ' );
	
	# NUMERO SEQUENCIAL DO REGISTRO NO ARQUIVO
	$conteudoArquivo.=exportaDados($sequenciaArquivo++, '', 'right', 6, 0);
	# QUEBRA DE LINHA
	$conteudoArquivo.="\r\n";

	return($conteudoArquivo);
} # fim itau


# Funcão para geração de arquivos - CEF
function gerarArquivoRemessaCEF($matriz) {

	global $conn, $tb, $corFundo, $corBorda;
	
	# Data Atual
	$data=dataSistema();

	# sequencia de registros
	$sequenciaArquivo=1;
	
	# Variável de conteúdo de arquivo
	$conteudoArquivo = '';

	### HEADER DE ARQUIVO
	# Numero do banco 1-3=3
	$conteudoArquivo .= exportaDados($matriz['bancoNumero'], 'N', 'right', 3, 0);
	# Lote de Serviço 4-7=4
	$conteudoArquivo .= '0000';
	# Registro Header de arquivo 8-8=1
	$conteudoArquivo .= '0';
	# Uso FEBRABAN 9-17=9
	$conteudoArquivo .= exportaDados('', '', 'right', 9, ' ');
	# Tipo de inscrição da empresa 18-18=1 [1-CPF 2-CGC]
	$conteudoArquivo .= '2';
	# Numero de inscrição da empresa 19-32=14
	$conteudoArquivo .= exportaDados(cpfVerificaFormato($matriz['cobrancaCNPJ']), '', 'right', 14, 0);
	# Codigo convenio com o banco 33-48=16
	$conteudoArquivo .= exportaDados( $matriz['cobrancaConvenio'], 'N', 'right', 16, 0 );
	# Uso exclusivo Caixa 49-52=4
	$conteudoArquivo .= exportaDados( '', '', 'right', 4, ' ' );
	# Agencia mantenedora da conta 53-57=5
	$conteudoArquivo .= exportaDados( $matriz['cobrancaAgencia'], '', 'right', 5, '0' );
	# Digito da agencia 58-58=1
	$conteudoArquivo .= exportaDados( $matriz['cobrancaAgenciaDig'], '', 'right', 1, ' ');
	# Numero da conta correte 59-70=12
	$conteudoArquivo .= exportaDados( $matriz['cobrancaConta'], '', 'right', 12, '0' );
	# Digito da conta corrente 71-71=1
	$conteudoArquivo .= exportaDados($matriz['cobrancaContaDig'], '', 'right', 1, ' ');;
	# Digito Verificador Agencia/Conta 72-72=1
	$conteudoArquivo .= exportaDados($matriz['cobrancaAgenciaContaDig'], '', 'right', 1, ' ');;
	# Nome da empresa 73-102=30
	$conteudoArquivo .= exportaDados( formFormatarStringArquivoRemessa( $matriz['cobrancaTitular'],'maiuscula'), '', 'left', 30, ' ');
	# Nome do Banco 103-132=30
	$conteudoArquivo .= exportaDados( formFormatarStringArquivoRemessa($matriz['bancoNome'],'maiuscula'), '', 'left', 30, ' ');
	# Uso exclusivo Febraban 133-142=10
	$conteudoArquivo .= exportaDados('', '', 'left', 10, ' ');
	# Codigo Remessa/Retorno 143-143=1 [1-REM 2-RET]
	$conteudoArquivo .= '1';
	# Data de geração do arquivo 144-151=8 [DDMMAAAA]
	$conteudoArquivo .= exportaDados( $data['dia'].$data['mes'].$data['ano'], '', 'right', 8, '0' );
	# Hora de geração do arquivo 152-157=6 [HHMMSS]
	$conteudoArquivo .= exportaDados( $data['hora'].$data['min'].$data['seg'], '', 'right', 6, '0');
	# Numero sequencial do arquivo 158-163=6
	$conteudoArquivo .= exportaDados( $matriz['numeroArquivo'], '', 'right', 6, '0');
	# Numero da versão do Layout 164-166=3 [fixo: 030]
	$conteudoArquivo .= exportaDados('30', '', 'left', 3, '0');
	# Densidade de gravação do arquivo 167-171=5 [fixo: 00000]
	$conteudoArquivo .= exportaDados('0', '', 'left', 5, '0');
	# Para uso reservado do banco 172-191=20
	$conteudoArquivo .= exportaDados('RETORNO-TESTE', '', 'left', 20, ' ');
	# Para uso reservado da empresa 192-211=20
	$conteudoArquivo .= exportaDados('REMESSA-TESTE', '', 'left', 20, ' ');
	# Uso exclusivo Febraban/CNAB	212-240=29
	$conteudoArquivo .= exportaDados('', '', 'left', 29, ' ');
	#quebra de linha
	$conteudoArquivo .= "\n";
	##### FIM DE HEADER DE ARQUIVO


	# Incrementar sequencia do Arquivo
	$sequenciaArquivo++;


	### HEADER DE LOTE
	# Codigo do banco na compensação 1-3=3
	$conteudoArquivo .= exportaDados( $matriz['bancoNumero'], 'N', 'right', 3, 0 );
	# Lote de servico 4-7=4
	$conteudoArquivo .= exportaDados(1, 'N', 'right', 4, '0');
	# Registro Header do lote 8-8=1
	$conteudoArquivo .= '1';
	# Tipo de operacao 9-9=1 [R=Arquivo Remessa T=Arquivo Retorno]
	$conteudoArquivo .= 'R';
	# Tipo de servicos 10-11=2 (fixo: 01)
	$conteudoArquivo .= '01';
	# Forma de lançamento 12-13=2 (ZEROS)
	$conteudoArquivo .= exportaDados('', 'N', 'right', 2, 0);
	# Numero da Versao do layout do lote 14-16=3 (fixo: 020)
	$conteudoArquivo .= '020';
	# Uso exclusivo FEBRABAN/CNAB 17-17=1 (BRANCOS)
	$conteudoArquivo .= exportaDados('', 'N', 'right', 1, ' ');
	# Tipo de Inscricao da empresa 18-18=1 [1-CPF 2-CGC]
	$conteudoArquivo .= '2';
	# Numero de inscricao da empresa 19-33=15
	$conteudoArquivo .= exportaDados(cpfVerificaFormato($matriz['cobrancaCNPJ']), 'N', 'right', 15, 0);
	# Numero convenio com o banco 34-49=16
	$conteudoArquivo .= exportaDados($matriz['cobrancaConvenio'], '', 'left', 16, ' ');
	# uso exclusivo caixa 50-53=4
	$conteudoArquivo .= exportaDados('', '', 'left', 4, ' ');
	# Agencia mantenedora da conta 54-58=5
	$conteudoArquivo .= exportaDados('', '', 'right', 5, '0');
	# Digito da agencia 59-59=1
	$conteudoArquivo .= exportaDados($matriz['cobrancaAgenciaDig'], '', 'left', 1, '0');
	# Numero da conta correte 60-71=12
	$conteudoArquivo .= exportaDados($matriz['cobrancaConta'], '', 'right', 12, '0');
	# Digito da conta corrente 72-72=1
	$conteudoArquivo .= exportaDados($matriz['cobrancaContaDig'], '', 'left', 1, ' ');
	# Digito Verificador Agencia/Conta 73-73=1
	$conteudoArquivo .= exportaDados($matriz['cobrancaAgenciaContaDig'], '', 'left', 1, ' ');
	# Nome da empresa 74-103=30
	$conteudoArquivo .= exportaDados(formFormatarStringArquivoRemessa($matriz['cobrancaTitular'],'maiuscula'), '', 'left', 30, ' ');
	# Mensagem 1 104-143=40
	$conteudoArquivo .= exportaDados(formFormatarStringArquivoRemessa($matriz['mensagem'],'maiuscula'),'','left','40',' ');
	# Mensagem 2 144-183=40
	$conteudoArquivo .= exportaDados(formFormatarStringArquivoRemessa($matriz['mensagem2'],'maiuscula'),'','left','40',' ');
	# Numero Remessa/Retorno 184-191=8 -- BUSCAR NUMERO
	$conteudoArquivo .= exportaDados('','','right','8','0');
	# Data de Gravacao Remessa/Retorno 192-199=8
	$conteudoArquivo .= exportaDados( $data['dia'].$data['mes'].$data['ano'], '', 'right', 8, '0' );
	# Data de Credito 200-207=8
	$conteudoArquivo .= exportaDados('','','right','8','0');
	# Uso exclusivo FEBRABAN/CNAB 208-240=33
	$conteudoArquivo .= exportaDados('','','right','33',' ');
	# QUEBRA LINHA
	$conteudoArquivo.="\n";
	### FIM DE HEADER DE LOTE
	
	# Incrementar sequencia do arquivo
	$sequenciaArquivo++;
	
	# Iniciar sequencia de lote
	$sequenciaLote=1;
	
	
	$sql="SELECT
			$tb[DocumentosGerados].id idDocumentoGerado, 
			$tb[ContasReceber].valor valor, 
			$tb[ContasReceber].dtVencimento dtVencimento, 
			$tb[PessoasTipos].id idPessoaTipo, 
			$tb[Pessoas].nome nomePessoa, 
			$tb[Pessoas].razao razaoSocial, 
			$tb[Pessoas].id idPessoa, 
			$tb[Pessoas].tipoPessoa tipoPessoa 
		FROM
			$tb[Pessoas], 
			$tb[PessoasTipos], 
			$tb[DocumentosGerados], 
			$tb[ContasReceber], 
			$tb[Faturamentos] 
		WHERE
			$tb[ContasReceber].idDocumentosGerados = $tb[DocumentosGerados].id 
			AND $tb[DocumentosGerados].idPessoaTipo = $tb[PessoasTipos].id 
			AND $tb[PessoasTipos].idPessoa = $tb[Pessoas].id 
			AND $tb[DocumentosGerados].idFaturamento = $tb[Faturamentos].id
			AND $tb[Faturamentos].id = $matriz[idFaturamento]";
	
	$consulta=consultaSQL($sql, $conn);
	
	for($i=0;$i<contaConsulta($consulta);$i++) {
	
		$idDocumentoGerado=resultadoSQL($consulta, $i, 'idDocumentoGerado');
		$valor=formatarValoresArquivoRemessa(resultadoSQL($consulta, $i, 'valor'));
		$dtVencimento=formatarData(converteData(resultadoSQL($consulta, $i, 'dtVencimento'),'banco','formdata'));
		$idPessoa=resultadoSQL($consulta, $i, 'idPessoa');
		$idPessoaTipo=resultadoSQL($consulta, $i, 'idPessoaTipo');
		$tipoPessoa=resultadoSQL($consulta, $i, 'tipoPessoa');
		if($tipoPessoa=='F') $nomePessoa=formFormatarStringArquivoRemessa(resultadoSQL($consulta, $i, 'nomePessoa'),'maiuscula');
		elseif($tipoPessoa=='J') $nomePessoa=formFormatarStringArquivoRemessa(resultadoSQL($consulta, $i, 'razaoSocial'),'maiuscula');

		# Verificar Endereço Preferencial ou existente
		$enderecosPessoa=buscaEnderecosPessoas($idPessoaTipo, 'idPessoaTipo','igual','idTipo');
		
		# Selecionar Endereço
		$endereco=enderecoSelecionaPreferencial($enderecosPessoa, $matriz[tipoEndereco]);
		
		# Selecionar Documentos
		$documento= trim( documentoSelecionaPreferencial($idPessoa, $tipoPessoa) );
		
		### REMESSA - Segmento P
		# Codigo do Banco 1-3=3
		$conteudoArquivo .= exportaDados( $matriz['bancoNumero'], 'N', 'right', 3, 0 );
		# Lote de Servico 4-7=4
		$conteudoArquivo .= exportaDados( 1, 'N', 'right', 4, 0 );
		# Registro Detalhe 8-8=1
		$conteudoArquivo .= '3';
		# Numero sequencial do registro no lote 9-13=5
		$conteudoArquivo .= exportaDados($sequenciaLote, 'N', 'right', 5, 0);
		# Codigo segmento do registro detalhe 14-14=1 (fixo: P)
		$conteudoArquivo .= 'P';
		# Uso excluviso do FEBRABAN 15-15=1
		$conteudoArquivo .= ' ';
		# Codigo de Movimento 16-17=2
		$conteudoArquivo .= '01';
		# Agencia Mantenedora da Conta 18-22=5
		$conteudoArquivo .= exportaDados($matriz['cobrancaAgencia'], 'N', 'right', 5, 0);
		# Digito Verificador da Agencia 23-23=1
		$conteudoArquivo .= exportaDados($matriz['cobrancaAgenciaDig'],'N', 'right', 1, 0);
		# Numero da Conta Corrente 24-35=12
		$conteudoArquivo .= exportaDados($matriz['cobrancaConta'],'N', 'right', 12, 0);
		# Digito Verificador da Conta 36-36=1
		$conteudoArquivo .= exportaDados('0','N', 'right', 1, 0);
		# Digito Verificador da agencia/conta 37-37=1
		$conteudoArquivo .= ' ';
		# Uso exclusivo Caixa 38-46=9 (brancos)
		$conteudoArquivo .= exportaDados('','', 'right', 9, ' ');
		# Identificação do título no banco 47-57=11
		$conteudoArquivo .= exportaDados('','N', 'right', 11, 0);
		# Codigo da carteira 58-58=1
		$conteudoArquivo .= '1';
		# Forma de cadastramento do titulo no banco 59-59=1 (1-com cadast. 2-sem cadast.)
		$conteudoArquivo .= '1';
		# Tipo de documento 60-60=1 (1-tradicional 2-escritural)
		$conteudoArquivo .= '1';
		# Identificação da emissao do bloqueto 61-61=1
		$conteudoArquivo .= '1';
		# Identificação da distribuicao 62-62=1 (1-banco 2-cliente)
		$conteudoArquivo .= '1';
		# Numero do documento de cobranca 63-73=11
		$conteudoArquivo .= exportaDados( $idDocumentoGerado,'N', 'left', 11, '0' );
		# Uso exclusivo Caixa 74-77=4 (brancos)
		$conteudoArquivo .= exportaDados('','', 'right', 4, ' ');
		# Data de vencimento do titulo 78-85=8
		$conteudoArquivo .= exportaDados($dtVencimento,'N', 'right', 8, 0);
		# Valor nominal do titulo 86-100=15
		$conteudoArquivo .= exportaDados($valor,'N', 'right', 15, 0);
		# Agencia encarregada da cobranca 101-105=5
		$conteudoArquivo .= exportaDados(0,'N', 'right', 5, 0);
		# Digito verificador da agencia 106-106=1
		$conteudoArquivo .= '0';
		# Especie do titulo 107-108=2
		$conteudoArquivo .= '02';
		# Identif. de titulo aceito/nao aceito 109-109=1
		$conteudoArquivo .= 'N';
		# Data de emissão do titulo 110-117=8
		$conteudoArquivo .= exportaDados($data['dia'].$data['mes'].$data['ano'],'N', 'right', 8, 0);
		# Codigo dos juros de mora 118-118=1
		$conteudoArquivo .= '3';
		# Data dos juros de mora 119-126=8
		$conteudoArquivo .= exportaDados(0,'N', 'right', 8, 0);
		# Juros de mora por dia/taxa 127-141=15
		$conteudoArquivo .= exportaDados(0,'N', 'right', 15, 0);
		# Codigo do desconto 1 142-142=1
		$conteudoArquivo .= '0';
		# Data do desconto 1 143-150=8
		$conteudoArquivo .= exportaDados(0,'N', 'right', 8, 0);
		# Valor/Percentual a ser concedido 151-165=15
		$conteudoArquivo .= exportaDados(0,'N', 'right', 15, 0);
		# Valor do IOF a ser recolhido 166-180=15
		$conteudoArquivo .= exportaDados(0,'N', 'right', 15, 0);
		# Valor do abatimento 181-195=15
		$conteudoArquivo .= exportaDados(0,'N', 'right', 15, 0);
		# Identificacao do titulo na empresa 196-220=25
		$conteudoArquivo .= exportaDados('','N', 'right', 25, ' ');
		# Codigo para protesto 221-221=1 (1-dias corrid, 2-dias uteis, 3-nao protestar)
		$conteudoArquivo .= '3';
		# Numero de dias para protesto 222-223=2
		$conteudoArquivo .= '00';
		# Codigo para baixa/devolucao 224-224=1 (1-baixar/dev, 2-nao baixar/nao devolver)
		$conteudoArquivo .= '2';
		# Numero de dias para baixa/devolucao 225-227=3 (dias corridos)
		$conteudoArquivo .= exportaDados('','N', 'right', 3, 0);
		# Codigo da moeda 228-229=2
		$conteudoArquivo .= '09';
		# Numero do contr. da operacao d cred 230-239=10
		$conteudoArquivo .= exportaDados('','N', 'right', 10, ' ');
		# Uso exclusivo FEBRABAN 240-240=1
		$conteudoArquivo.=' ';
		# QUERBRA DE LINHA
		$conteudoArquivo.="\n";
		### FIM DE REMESSA - Registro P
		
		
		# Incrementar numero do registro da remessa
		$sequenciaLote++;
		
		# Incrementar sequencia do arquivo
		$sequenciaArquivo++;
		
		### REMESSA - Segmmento Q
		# Codigo do Banco na compensacao 1-3=3
		$conteudoArquivo .= exportaDados($matriz['bancoNumero'], 'N', 'right', 3, 0);
		# Lote de servico 4-7=4
		$conteudoArquivo .= exportaDados(1, 'N', 'right', 4, 0);
		# Registro detalhe 8-8=1
		$conteudoArquivo .= '3';
		# Numero sequencial do reg. no lote 9-13=5
		$conteudoArquivo .= exportaDados($sequenciaLote, 'N', 'right', 5, 0);
		# Cod. segmento do reg. detalhe 14-14=1 (fixo: Q)
		$conteudoArquivo .= 'Q';
		# Uso exclusivo FEBRABAN 15-15=1 (BRANCOS)
		$conteudoArquivo .= ' ';
		# Codigo de movimento 16-17=2 (01 entrada de titulo)
		$conteudoArquivo .= '01';
		# Tipo de Inscricao 18-18=1
		$conteudoArquivo .= ( ($tipoPessoa == 'F' ) ? '1' : '2');
		# Numero de inscricao 19-33=15
		$conteudoArquivo .= exportaDados(cpfVerificaFormato($documento), '', 'right', 15, '0');
		# Nome 34-73=40
		$conteudoArquivo .= exportaDados( $nomePessoa, 'N', 'left', 40, ' ' );
		# Endereco 74-113=40
		$conteudoArquivo .= exportaDados( formFormatarStringArquivoRemessa($endereco['endereco']." ".$endereco['complemento'],'maiuscula'), 
										  'N', 'left', 40, ' ');
		# Bairro 114-128=15
		$conteudoArquivo .= exportaDados( formFormatarStringArquivoRemessa($endereco['bairro'],'maiuscula'), 'N', 'left', 15, ' ');
		# CEP 129-133=5
		$conteudoArquivo .= exportaDados( $endereco['cep_prefix'], 'N', 'right', 5, ' ');
		# Sufixo do CEP 134-136=3
		$conteudoArquivo .= exportaDados( $endereco['cep_sufix'], 'N', 'right', 3, ' ');
		# Cidade 137-151=15
		$conteudoArquivo .= exportaDados( formFormatarStringArquivoRemessa($endereco['cidade'],'maiuscula'), 'N', 'left', 15, ' ');
		# Unidade da Federecao 152-153=2
		$conteudoArquivo .= exportaDados(formFormatarStringArquivoRemessa($endereco['uf'],'maiuscula'), 'N', 'left', 2, ' ');
		# Tipo de Inscricao 154-154=1
		$conteudoArquivo .= exportaDados('0', 'N', 'right', 1, '0');
		# Número de Inscricao 155-169=15
		$conteudoArquivo .= exportaDados('0', 'N', 'right', 15, '0');
		# Nome do sacador/avalista 170-209=40
		$conteudoArquivo .= exportaDados('', 'N', 'left', 40, ' ');
		# Cod. bco. corresp. na compensacao 210-212=3 (fixo: 000)
		$conteudoArquivo .= exportaDados('', 'N', 'left', 3, ' ');
		# Nosso Num. no Bco. Correspondente 213-232=20
		$conteudoArquivo .= exportaDados('', 'N', 'left', 20, ' ');
		# Uso exclusivo FEBRABAN 233-240=8 (BRANCOS)
		$conteudoArquivo .= exportaDados('', 'N', 'left', 8, ' ');
		# QUEBRA DE LINHA
		$conteudoArquivo.="\n";
		### FIM REMESSA - Registro Q
		
		# Incrementar numero do registro da remessa
		$sequenciaLote++;
		
		# Incrementar sequencia do arquivo
		$sequenciaArquivo++;
	} #for
	
	
	#### TRAILER DE LOTE
	# Lote de servico 4-7=4
	# Codigo do Banco na compensacao 1-3=3
	$conteudoArquivo .= exportaDados( $matriz['bancoNumero'], 'N', 'right', 3, 0);
	# Lote de servico 4-7=4
	$conteudoArquivo .= exportaDados(1, 'N', 'right', 4, 0);
	# Registro trailer do lote 8-8=1 (fixo: 5)
	$conteudoArquivo .= '5';
	# Uso exclusivo do FEBRABAN 9-17=9 (BRANCOS)
	$conteudoArquivo .= exportaDados(' ', 'N', 'left', 9, ' ');
	# Quantidade de registros do lote 18-23=6
	$conteudoArquivo .= exportaDados(++$sequenciaLote, 'N', 'right', 6, 0);
	# Quantidade de Titulos em cobranca 24-29=6
	$conteudoArquivo .= exportaDados('0', 'N', 'right', 6, 0);
	# Valor Total dos titulos em carteira 30-46=17
	$conteudoArquivo .= exportaDados('0', 'N', 'right', 17, 0);
	# Uso exclusivo do FEBRABAN / CNAB 47-69=23
	$conteudoArquivo .= exportaDados('', '', 'left', 23, ' ');
	# Quantidade de titulos em cobranca 70-75=6
	$conteudoArquivo .= exportaDados('0', 'N', 'right', 6, 0);
	# Valor total dos titulos em carteira 76-92=17
	$conteudoArquivo .= exportaDados('0', 'N', 'right', 17, 0);
	# Uso exclusivo do FEBRABAN / CNAB 93-123=6
	$conteudoArquivo .= exportaDados('', 'N', 'right', 31, ' ');
	# Uso exclusivo do FEBRABAN / CNAB 124-240=117
	$conteudoArquivo .= exportaDados('', 'N', 'right', 117, ' ');
	# QUEBRA DE LINHA
	$conteudoArquivo.="\n";
	### FIM DE TRAILER DE LOTE
	
	
	# Incrementar sequencia do arquivo
	$sequenciaArquivo++;

	
	### TRAILER DE ARQUIVO
	# Codigo do banco na compensasao 1-3=3
	$conteudoArquivo .= exportaDados($matriz['bancoNumero'], '', 'right', 3, '0');
	# Lote de Servico 4-7=4 (fixo: 0000)
	$conteudoArquivo .= "9999";
	# Registro trailer de arquvo 8-8=1 (fixo: 9)
	$conteudoArquivo .= "9";
	# Uso exclusivo FEBRABAN 9-17=9 (fixo: BRANCOS)
	$conteudoArquivo .= exportaDados('', '', 'left', 9, ' ');
	# Quantidade de lotes do arquivo 18-23=6 (Numero de registros tipo - 1)
	$conteudoArquivo .= exportaDados('1', '', 'right', 6, '0');; #### SOMAR QUANTIDADE
	# Quantidade de registros do arquivo 24-29=6 (Numero de registros tipo 0+1+3+5+9)
	$conteudoArquivo .= exportaDados($sequenciaArquivo, '', 'right', 6, '0'); #### SOMAR QUANTIDADE
	# Quantidade de contas p/ conc. - lotes 30-35=6 (Numero de registros tipo -1 oper-E)
	$conteudoArquivo .= exportaDados('', '', 'right', 6, ' '); #### SOMAR QUANTIDADE
	# Uso exclusivo FEBRABAN/CNAB 26-240=205
	$conteudoArquivo .= exportaDados('', '', 'left', 205, ' ');
	# QUEBRA DE LINHA
	#$conteudoArquivo.="\n";
	### FIM DE TRAILER DE ARQUIVO

	return($conteudoArquivo);
} #fim CEF




# Funcão para geração de arquivos - BRADESCO
function gerarArquivoRemessaBradesco($matriz) {

	global $conn, $tb, $corFundo, $corBorda;


	# sequencia de registros
	$sequenciaArquivo=1;
	
	# Variável de conteúdo de arquivo
	$conteudoArquivo='';

	# data do sistema
	$data=dataSistema();
	
	### REGISTRO HEADER
	# TIPO DO REGISTRO
	$conteudoArquivo.=exportaDados(0, '', 'right', 1, 0);
	# TIPO DA OPERACAO
	$conteudoArquivo.=exportaDados(1, '', 'right', 1, 0);
	# LITERAL DO TIPO DA OPERACAO
	$conteudoArquivo.=exportaDados("REMESSA", '', 'left', 7, ' ');
	# TIPO DE SERVICO
	$conteudoArquivo.=exportaDados(1, '', 'right', 2, 0);
	# LITERAL DO TIPO DE SERVICO
	$conteudoArquivo.=exportaDados("COBRANCA", '', 'left', 15, ' ');
	# Codigo da Empresa - 20
	#$conteudoArquivo.=exportaDados('302062', '', 'right', 20, '0');
	$conteudoArquivo.=exportaDados($matriz['cobrancaConvenio'], '', 'right', 20, '0');
	# NOME DA EMPRESA - 30
	$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($matriz[cobrancaTitular],'maiuscula'), '', 'left', 30, ' ');
	# NUMERO DO BANCO - 3
	$conteudoArquivo.=exportaDados($matriz[bancoNumero], '', 'right', 3, 0);
	# NOME DO BANCO - 15
	$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($matriz[bancoNome],'maiuscula'), '', 'left', 15, ' ');
	# DATA DA GERACAO [DDMMAA] - 6
	$conteudoArquivo.=exportaDados($data[dia].$data[mes].substr($data[ano], 2, 2), '', 'right', 6, '0');
	# BRANCOS COMPLEMENTO - 8
	$conteudoArquivo.=exportaDados('', '', 'left', 8, ' ');
	# Identificação do Sistema (MX) - 2
	$conteudoArquivo.=exportaDados('MX', '', 'left', 2, ' ');
	# Numero Sequencia de Arquivo - 7
	$conteudoArquivo.=exportaDados($matriz[idArquivo], '', 'right', 7, '0');
	# Brancos - 277
	$conteudoArquivo.=exportaDados('', '', 'left', 277, ' ');
	# NUMERO SEQUENCIAL DO REGISTRO NO ARQUIVO - 6 
	$conteudoArquivo.=exportaDados($sequenciaArquivo++, '', 'right', 6, 0);
	# QUEBRA DE LINHA
	$conteudoArquivo.="\r\n";
	
	$sql="SELECT
			$tb[DocumentosGerados].id idDocumentoGerado, 
			$tb[ContasReceber].valor valor, 
			$tb[ContasReceber].dtVencimento dtVencimento, 
			$tb[PessoasTipos].id idPessoaTipo, 
			$tb[Pessoas].nome nomePessoa, 
			$tb[Pessoas].razao razaoSocial, 
			$tb[Pessoas].id idPessoa, 
			$tb[Pessoas].tipoPessoa tipoPessoa,
			$tb[Faturamentos].id idFaturamentos
		FROM
			$tb[Pessoas], 
			$tb[PessoasTipos], 
			$tb[DocumentosGerados], 
			$tb[ContasReceber], 
			$tb[Faturamentos] 
		WHERE
			$tb[ContasReceber].idDocumentosGerados = $tb[DocumentosGerados].id 
			AND $tb[DocumentosGerados].idPessoaTipo = $tb[PessoasTipos].id 
			AND $tb[PessoasTipos].idPessoa = $tb[Pessoas].id 
			AND $tb[DocumentosGerados].idFaturamento = $tb[Faturamentos].id
			AND $tb[Faturamentos].id = $matriz[idFaturamento]";
	
	$consulta=consultaSQL($sql, $conn);
	for($i=0;$i<contaConsulta($consulta);$i++) {
	
		$idDocumentoGerado=resultadoSQL($consulta, $i, 'idDocumentoGerado');
		$valor=formatarValoresArquivoRemessa(resultadoSQL($consulta, $i, 'valor'));
		if(strlen(trim($matriz[data_vencimento]))>0) {
			# Utilizar data de vencimento informada em form
			$dtVencimento=formatarData($matriz[data_vencimento]);
			$dtVencimento=substr($dtVencimento,0,4).substr($dtVencimento, -2);
		}
		else {
			# Utilizar data de vencimento do documento
			$dtVencimento=formatarData(converteData(resultadoSQL($consulta, $i, 'dtVencimento'),'banco','formdata'));
			$dtVencimento=substr($dtVencimento,0,4).substr($dtVencimento, -2);
		}
		$idPessoa=resultadoSQL($consulta, $i, 'idPessoa');
		$idPessoaTipo=resultadoSQL($consulta, $i, 'idPessoaTipo');
		$tipoPessoa=resultadoSQL($consulta, $i, 'tipoPessoa');
		$idFaturamentos= resultadoSQL($consulta,$i,'idFaturamentos');
		if($tipoPessoa=='F') $nomePessoa=formFormatarStringArquivoRemessa(resultadoSQL($consulta, $i, 'nomePessoa'),'maiuscula');
		elseif($tipoPessoa=='J') $nomePessoa=formFormatarStringArquivoRemessa(resultadoSQL($consulta, $i, 'razaoSocial'),'maiuscula');

		# Verificar Endereço Preferencial ou existente
		$enderecosPessoa=buscaEnderecosPessoas($idPessoaTipo, 'idPessoaTipo','igual','idTipo');
		
		# Selecionar Endereço
		$endereco=enderecoSelecionaPreferencial($enderecosPessoa, $matriz[tipoEndereco]);
		
		# Selecionar Documentos
		$documento=documentoSelecionaPreferencial($idPessoa, $tipoPessoa);	

		### REGISTRO TRANSAÇÃO (obrigatorio)
		# IDENTIFICAÇÃO DO REGISTRO - 1
		#================================= pega os dados ClienteBanco =====================================#
		
		// ESTA CONSULTA TRAS O "ID" DI TIPO DE PESSOA PARA SELECIONAR NA TABELA DE RELACIONAMENTO CLIENTEBANCO
		$sql="SELECT
			  	$tb[PlanosPessoas].id id
			  FROM
				$tb[PlanosPessoas], $tb[Faturamentos]
			  WHERE
				$tb[PlanosPessoas].idFormaCobranca = $tb[Faturamentos].idFormaCobranca AND
				$tb[Faturamentos].id= $idFaturamentos AND
				$tb[PlanosPessoas].idPessoaTipo= $idPessoaTipo
		";
		$qry= consultaSQL($sql, $conn);
		$idPessoasPlanos= resultadoSQL($qry,0,'id');
		//CONSULTA DA TABELA DE RELACIONAMENTO PARA COMPLETAR OS CAMPOS AG, DIG, CC, DIG no arquivo remessa
		$sql="SELECT
			  	$tb[ClienteBanco].agencia,
				$tb[ClienteBanco].digAg,
				$tb[ClienteBanco].contacorrente,
				$tb[ClienteBanco].digCC
			  FROM
				$tb[ClienteBanco]
			  WHERE
				$tb[ClienteBanco].idPlanosPessoas= $idPessoasPlanos
		";

		$qry=consultaSQL($sql,$conn);
		if ( $qry && contaConsulta( $qry ) > 0 ){
			$ag= resultadoSQL($qry,0,'agencia');
			$digAg= resultadoSQL($qry,0,'digAg');
			$cc= resultadoSQL($qry,0,'contacorrente');
			$digCC= resultadoSQL($qry,0,'digCC');
			$cliBan= '';
		}
		#==================================================================================================#
		$conteudoArquivo.=exportaDados(1, '', 'right', 1, 0);
	#  DO 2 AO 20 SO SERA USADO PARA DEBITO EM CONTA CORRENTE
		# Agencia de Debito - 5
		$conteudoArquivo.=exportaDados($ag, '', 'right', 5, 0);
		# Digito da Agencia - 1
		$conteudoArquivo.=exportaDados($digAg, '', 'right', 1, ' ');
		# Razao da Conta Corrente - 5
		$conteudoArquivo.=exportaDados('', '', 'right', 5, '0');
		# Conta Corrente - 7
		$conteudoArquivo.=exportaDados($cc, '', 'right', 7, '0');
		# Digito da Conta Corrente - 1 
		$conteudoArquivo.=exportaDados($digCC, '', 'right', 1, ' ');
	# registro 21
		# Identificação da Empresa Cedente no Banco 17
		# Zero Carteira Agencia C/C Digito
		$conteudoArquivo.='0009';
		$conteudoArquivo.=exportaDados($matriz[cobrancaAgencia], '', 'right', 5, '0');
		$conteudoArquivo.=exportaDados($matriz[cobrancaConta], '', 'right', 7, '0');
		$conteudoArquivo.=exportaDados($matriz[cobrancaContaDig], '', 'right', 1, '0');
		
		# Numero Controle do Participante - 25
		$conteudoArquivo.=exportaDados('', '', 'left', 25, ' ');
		# Codigo do Banco na Compesacao - 3
		$conteudoArquivo.='237';
		# Zeros
		$conteudoArquivo.=exportaDados('0', '', 'left', 5, '0');
		# Identicacao do Titulo no Banco (Nosso Numero) - 11
		$conteudoArquivo.=exportaDados(0, '', 'right', 11, '0');
		# Digito Verificador
		$conteudoArquivo.=exportaDados(0, '', 'right', 1, '0');
		# Desconto Bonificação por dia - 10
		$conteudoArquivo.=exportaDados(0, '', 'right', 10, '0');
		# Condição para Emissao da papeleta de cobranca - 1
		# 1 - Banco emite e processa
		# 2 - Cliente emite e o banco processa
		$conteudoArquivo.='1';
		# Ident. se emnite papeleta para Debito Automatico - 1
		$conteudoArquivo.=' '; // N substituido por campo em branco
		# Identificacao da Operacao do Banco - 10
		$conteudoArquivo.=exportaDados('', '', 'left', 10, ' ');
		# Indicador Rateio Credito 
		# se a empresa participa dse rateio de credito colocar R
		$conteudoArquivo.=' ';
		# Enderecamento para Aviso de debito Automatico
		# 1 - emite aviso
		# 2 - nao emite
		$conteudoArquivo.='2';
		# Brancos - 2
		$conteudoArquivo.='  ';
		# Identificacao ocorrencia - 2
		# 01 - Remessa
		$conteudoArquivo.='01';
		# Numero do documento - 10
		$conteudoArquivo.=exportaDados($idDocumentoGerado, '', 'right', 10, 0);
		# vencimento do titulo
		$conteudoArquivo.=exportaDados($dtVencimento, '', 'left', 6, ' ');
		# Valor do titulo - 13
		$conteudoArquivo.=exportaDados($valor, 'N', 'right', 13, 0);
		# Banco Encarregado da Cobranca - 3
		$conteudoArquivo.=exportaDados($matriz[bancoNumero], '', 'right', 3, 0);
		# Agencia Depositária - 5 
		$conteudoArquivo.=exportaDados($matriz[cobrancaAgencia], '', 'right', 5, '0');
		# Especie do documento - 2
		# 01 - Duplicata
		$conteudoArquivo.='01';
		# Identificacao
		# A - Aceito
		# N - Sem Aceite
		$conteudoArquivo.='N';
		# Data de Emissao do Titulo
		$conteudoArquivo.=exportaDados($data[dia].$data[mes].substr($data[ano], 2, 2), '', 'right', 6, '0');
		# 1a Instrução - 02
		# tabela - nao usado.
		$conteudoArquivo.=exportaDados($matriz[instrucao1], '', 'right', 2, 0);
		# 2a Instrucao
		$conteudoArquivo.=exportaDados($matriz[instrucao2], '', 'right', 2, 0);;
		# Valor a ser cobrado por dia de Atraso - 13
		$conteudoArquivo.=exportaDados(0, '', 'right', 13, '0');
		# Data limite para concessao de desconto - 6
		$conteudoArquivo.=exportaDados(0, '', 'right', 6, '0');
		# VALOR DO Desconto - 13
		$conteudoArquivo.=exportaDados(0, '', 'right', 13, '0');
		# VALOR DO IOF - 13
		$conteudoArquivo.=exportaDados(0, '', 'right', 13, '0');
		# ABATIMENTO
		$conteudoArquivo.=exportaDados(0, '', 'right', 13, '0');
		# Identificacao do tipo de inscricao do sacado - 2
		# 01 - cpf / 02 - cnpj / 03 - PIS / 98 - nao tem / 99 - outros
		if ($tipoPessoa=='J') $conteudoArquivo.='02';
		else $conteudoArquivo.='01';
		# NUMERO DE INSCRICAO (CPF / CNPJ)
		if ( strtolower( $documento )== 'isento' ) $documento = '';
		$conteudoArquivo.=exportaDados(cpfVerificaFormato($documento), '', 'right', 14, '0');
		# NOME SACADO
		$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($nomePessoa,'maiuscula'), '', 'left', 40, ' ');
		# ENDERECO - 40
		$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($endereco[endereco]." ".$endereco[complemento],'maiuscula'), '', 'left', 40, ' ');
		# Mensagem - 12
		//exibe uma mensagem caso pre-cadastrada ou informada manualmente .
		$conteudoArquivo.=exportaDados('', '', 'left', 12, ' ');
		
		# CEP
		$conteudoArquivo.=exportaDados(cpfVerificaFormato($endereco[cep]), '', 'right', 8, '0');
		# NOME DO SACADOR/AVALISTA
		$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($matriz[mensagem],'maiuscula'), '', 'left', 60, ' ');
		# NUMERO SEQUENCIAL DO REGISTRO NO ARQUIVO
		$conteudoArquivo.=exportaDados($sequenciaArquivo++, '', 'right', 6, 0);
		# QUEBRA DE LINHA
		$conteudoArquivo.="\r\n";
	
		
//		if ($endereco[email]) {
//
//			### REGISTRO DETALHE (opcional)
//			# TIPO DO REGISTRO
//			$conteudoArquivo.=exportaDados(5, '', 'right', 1, 0);
//			# EMAIL
//			$conteudoArquivo.=exportaDados($endereco[email], '', 'left', 120, ' ');
//			# CODIGO DA INSCRICAO
//			$conteudoArquivo.=exportaDados(0, '', 'right', 2, 0);
//			# NUMERO DA INSCRICAO
//			$conteudoArquivo.=exportaDados(0, '', 'right', 14, 0);
//			# ENDERECO
//			$conteudoArquivo.=exportaDados('', '', 'left', 40, ' ');
//			# BAIRRO
//			$conteudoArquivo.=exportaDados('', '', 'left', 12, ' ');
//			# CEP
//			$conteudoArquivo.=exportaDados(0, '', 'right', 8, '0');
//			# CIDADE
//			$conteudoArquivo.=exportaDados('', '', 'left', 15, ' ');
//			# ESTADO
//			$conteudoArquivo.=exportaDados('', '', 'left', 2, ' ');
//			# BRANCOS COMPLEMENTO
//			$conteudoArquivo.=exportaDados('', '', 'left', 180, ' ');
//			# NUMERO SEQUENCIAL DO REGISTRO NO ARQUIVO
//			$conteudoArquivo.=exportaDados($sequenciaArquivo++, '', 'right', 6, 0);
//			# QUEBRA DE LINHA
//			$conteudoArquivo.="\r\n";
//	
//		}
	}
	#### REGISTRO TRAILER
	# Identificacao do REGISTRO - 1
	$conteudoArquivo.=exportaDados(9, '', 'right', 1, 0);
	# BRANCOS COMPLEMENTO
	$conteudoArquivo.=exportaDados('', '', 'left', 393, ' ');
	# NUMERO SEQUENCIAL DO REGISTRO NO ARQUIVO
	$conteudoArquivo.=exportaDados($sequenciaArquivo++, '', 'right', 6, 0);
	# QUEBRA DE LINHA
	$conteudoArquivo.="\r\n";

	return($conteudoArquivo);
} 
# fim BRADESCO



# Funcão para geração de arquivos - BANESPA
function gerarArquivoRemessaBanespa($matriz) {

	global $conn, $tb, $corFundo, $corBorda;


	# sequencia de registros
	$sequenciaArquivo=1;
	
	# Variável de conteúdo de arquivo
	$conteudoArquivo='';

	# data do sistema
	$data=dataSistema();
	
	### REGISTRO HEADER
	# Identificacao do registro Header
	$conteudoArquivo.='0';
	# Identificacao do arquivo
	$conteudoArquivo.='1';
	# Identificacao por extenso do arquivo
	$conteudoArquivo.="REMESSA";
	# TIPO DE SERVICO
	$conteudoArquivo.='01';
	# LITERAL DO TIPO DE SERVICO
	$conteudoArquivo.=exportaDados("COBRANCA", '', 'left', 15, ' ');
	# Codigo da Empresa - 11
	$conteudoArquivo.=exportaDados($matriz[cobrancaConvenio], '', 'right', 11, '0');
	# Filler
	$conteudoArquivo.=exportaDados('', '', 'right', 9, ' ');
	# NOME DA EMPRESA - 30
	$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($matriz[cobrancaTitular],'maiuscula'), '', 'left', 30, ' ');
	# NUMERO DO BANCO - 3
	$conteudoArquivo.=exportaDados($matriz[bancoNumero], '', 'right', 3, 0);
	# NOME DO BANCO - 7
	#$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($matriz[bancoNome],'maiuscula'), '', 'left', 7, ' ');
	$conteudoArquivo.=exportaDados('BANESPA', '', 'left', 7, ' ');
	# Filler
	$conteudoArquivo.=exportaDados('', '', 'right', 8, ' ');
	# DATA DA GERACAO [DDMMAA] - 6
	$conteudoArquivo.=exportaDados($data[dia].$data[mes].substr($data[ano], 2, 2), '', 'right', 6, '0');
	# Densidade de Gravacao - 5
	$conteudoArquivo.='01600';
	# Unidade de densidade de gravacao - 3
	$conteudoArquivo.='BPI';
	# Filler - 286
	$conteudoArquivo.=exportaDados('', '', 'left', 286, ' ');
	# NUMERO SEQUENCIAL DO REGISTRO NO ARQUIVO - 6 
	$conteudoArquivo.=exportaDados($sequenciaArquivo++, '', 'right', 6, 0);
	# QUEBRA DE LINHA
	$conteudoArquivo.="\r\n";
	
	$sql="SELECT
			$tb[DocumentosGerados].id idDocumentoGerado, 
			$tb[ContasReceber].valor valor, 
			$tb[ContasReceber].dtVencimento dtVencimento, 
			$tb[PessoasTipos].id idPessoaTipo, 
			$tb[Pessoas].nome nomePessoa, 
			$tb[Pessoas].razao razaoSocial, 
			$tb[Pessoas].id idPessoa, 
			$tb[Pessoas].tipoPessoa tipoPessoa 
		FROM
			$tb[Pessoas], 
			$tb[PessoasTipos], 
			$tb[DocumentosGerados], 
			$tb[ContasReceber], 
			$tb[Faturamentos] 
		WHERE
			$tb[ContasReceber].idDocumentosGerados = $tb[DocumentosGerados].id 
			AND $tb[DocumentosGerados].idPessoaTipo = $tb[PessoasTipos].id 
			AND $tb[PessoasTipos].idPessoa = $tb[Pessoas].id 
			AND $tb[DocumentosGerados].idFaturamento = $tb[Faturamentos].id
			AND $tb[Faturamentos].id = $matriz[idFaturamento]";
	
	$consulta=consultaSQL($sql, $conn);
	
	for($i=0;$i<contaConsulta($consulta);$i++) {
	
		$idDocumentoGerado=resultadoSQL($consulta, $i, 'idDocumentoGerado');
		$valor=formatarValoresArquivoRemessa(resultadoSQL($consulta, $i, 'valor'));
		if(strlen(trim($matriz[data_vencimento]))>0) {
			# Utilizar data de vencimento informada em form
			$dtVencimento=formatarData($matriz[data_vencimento]);
			$dtVencimento=substr($dtVencimento,0,4).substr($dtVencimento, -2);
		}
		else {
			# Utilizar data de vencimento do documento
			$dtVencimento=formatarData(converteData(resultadoSQL($consulta, $i, 'dtVencimento'),'banco','formdata'));
			$dtVencimento=substr($dtVencimento,0,4).substr($dtVencimento, -2);
		}
		$idPessoa=resultadoSQL($consulta, $i, 'idPessoa');
		$idPessoaTipo=resultadoSQL($consulta, $i, 'idPessoaTipo');
		$tipoPessoa=resultadoSQL($consulta, $i, 'tipoPessoa');
		if($tipoPessoa=='F') $nomePessoa=formFormatarStringArquivoRemessa(resultadoSQL($consulta, $i, 'nomePessoa'),'maiuscula');
		elseif($tipoPessoa=='J') $nomePessoa=formFormatarStringArquivoRemessa(resultadoSQL($consulta, $i, 'razaoSocial'),'maiuscula');

		# Verificar Endereço Preferencial ou existente
		$enderecosPessoa=buscaEnderecosPessoas($idPessoaTipo, 'idPessoaTipo','igual','idTipo');
		
		# Selecionar Endereço
		$endereco=enderecoSelecionaPreferencial($enderecosPessoa, $matriz[tipoEndereco]);
		
		# Selecionar Documentos
		$documento=documentoSelecionaPreferencial($idPessoa, $tipoPessoa);	

		### REGISTRO DETALHE (obrigatorio)
		# TIPO DO REGISTRO
		$conteudoArquivo.='1';
		# CODIGO DE INSCRICAO 
		$conteudoArquivo.='02';
		# NUMERO DE INSCRICAO
		$conteudoArquivo.=exportaDados(cpfVerificaFormato($matriz[cobrancaCNPJ]), '', 'right', 14, 0);
		# Identificacao da Empresa no Banespa - 11
		$conteudoArquivo.=exportaDados($matriz[cobrancaConvenio], '', 'right', 11, '0');
		# Filler - 9
		$conteudoArquivo.=exportaDados('', '', 'right', 9, ' ');
		# Identificacao do Titulo na Empresa
		$conteudoArquivo.=exportaDados($idDocumentoGerado, '', 'left', 25, ' ');
		# Numero da Agencia cedente
		$conteudoArquivo.=exportaDados(intval($matriz[cobrancaAgencia]), '', 'right', 3, '0');
		#Identificacao do Titulo no Banespa - 7
		$conteudoArquivo.=exportaDados('', '', 'right', 7, '0');
		# filler - 10
		$conteudoArquivo.=exportaDados('', '', 'right', 10, ' ');
		#Identificacao da Operacao - 25
		$conteudoArquivo.=exportaDados('', '', 'right', 25, ' ');
		/*# Identificacao do Titulo no Banespa - 7
		$conteudoArquivo.=exportaDados('', '', 'right', 7, '0');
		# filler - 10
		$conteudoArquivo.=exportaDados('', '', 'right', 10, '0');
		# Identificacao da Operacao no Banespa - 25
		$conteudoArquivo.=exportaDados('', '', 'right', 25, ' ');*/
		# Codigo da Carteira - 1
		# 1 - Simples / 3 - Caucionada / 6 - Especial
		$conteudoArquivo.='1';
		# Identificacao da Ocorrencia
		# 1 - Remessa / 
		$conteudoArquivo.='01';
		# Numero do Documento
		$conteudoArquivo.=exportaDados($idDocumentoGerado, '', 'left', 10, ' ');
		# Data do Vencimento do Titulo
		# 000001 - A Vista / 000002 - Contra Apresentacao
		$conteudoArquivo.=exportaDados($dtVencimento, '', 'left', 6, ' ');
		# VALOR DO TITULO 
		$conteudoArquivo.=exportaDados($valor, '', 'right', 13, '0');
		# NUMERO DO BANCO
		$conteudoArquivo.=exportaDados($matriz[bancoNumero], '', 'right', 3, 0);
		# AGENCIA COBRADORA
		$conteudoArquivo.=exportaDados(0, '', 'right', 5, '0');
		# ESPECIE 
		$conteudoArquivo.=exportaDados("10", '', 'right', 2, '0');
		# ACEITE 
		$conteudoArquivo.=exportaDados('N', '', 'left', 1, ' ');
		# DATA DE EMISSAO
		$conteudoArquivo.=exportaDados($data[dia].$data[mes].substr($data[ano], -2), '', 'right', 6, '0');
		# INSTRUCAO1
		$conteudoArquivo.=exportaDados($matriz['instrucao1'], '', 'left', 2, '0');
		# INSTRUCAO2
		$conteudoArquivo.=exportaDados(0, '', 'left', 2, '0');
		# JUROS DE 1 DIA 
		if ($matriz['juros_atraso']){
			$comp = '0';
			$juros = $valor * (str_replace(",", ".", $matriz['juros_atraso'])) /100 /100; 
			$matriz['juros'] = str_replace(".", "", number_format( $juros, 2, '', '') ); //mesmo formatando sem ./, (number_format) na versao da ivrbrasil, saia com .
		}
		else			$comp = '9';
		
		$conteudoArquivo.=exportaDados($matriz['juros'], '', 'right', 13, $comp);
		# DESCONTO ATE
		$conteudoArquivo.=exportaDados(0, '', 'right', 6, '0');
		# VALOR DO DESCONTO 
		$conteudoArquivo.=exportaDados(0, '', 'right', 13, '0');
		# VALOR DO IOF
		$conteudoArquivo.=exportaDados(0, '', 'right', 13, '0');
		# ABATIMENTO
		$conteudoArquivo.=exportaDados(0, '', 'right', 13, '0');
		# CODIGO DE INSCRICAO (01 = CPF, 02 = CNPJ)
		if($tipoPessoa=='F' && !empty($documento)  && strtoupper($documento) != "ISENTO") 	$conteudoArquivo.='01';
		elseif($tipoPessoa=='J'&& !empty($documento) && strtoupper($documento) != "ISENTO") $conteudoArquivo.='02';
		else{
			$conteudoArquivo.='98';
			$documento= "0";
		}	
//		if (strtoupper($documento) == "ISENTO"){
//			$documento= "0";
//			
//		}
		# NUMERO DE INSCRICAO (CPF / CNPJ)
		$conteudoArquivo.=exportaDados(cpfVerificaFormato($documento), '', 'right', 14, '0');
		# NOME SACADO
		$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($nomePessoa,'maiuscula'), '', 'left', 40, ' ');
		# ENDERECO
		$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($endereco[endereco]." ".$endereco[complemento],'maiuscula'), '', 'left', 40, ' ');
		# BAIRRO
		$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($endereco[bairro],'maiuscula'), '', 'left', 12, ' ');
		# CEP
		$conteudoArquivo.=exportaDados(cpfVerificaFormato($endereco[cep]), '', 'right', 8, '0');
		# CIDADE
		$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($endereco[cidade],'maiuscula'), '', 'left', 15, ' ');
		# ESTADO
		$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($endereco[uf],'maiuscula'), '', 'left', 2, ' ');
		# NOME DO SACADOR/AVALISTA
		$conteudoArquivo.=exportaDados(formFormatarStringArquivoRemessa($matriz[cobrancaTitular],'maiuscula'), '', 'left', 40, ' ');
		# PRAZO para protesto
		$conteudoArquivo.=exportaDados(0, '', 'right', 2, '0');
		# BRANCOS COMPLEMENTO
		$conteudoArquivo.=' ';
		# NUMERO SEQUENCIAL DO REGISTRO NO ARQUIVO
		$conteudoArquivo.=exportaDados($sequenciaArquivo++, '', 'right', 6, 0);
		# QUEBRA DE LINHA
		$conteudoArquivo.="\r\n";
	
		
		/*if ($endereco[email]) {
			
			### REGISTRO DETALHE (opcional)
			# TIPO DO REGISTRO
			$conteudoArquivo.=exportaDados(5, '', 'right', 1, 0);
			# EMAIL
			$conteudoArquivo.=exportaDados($endereco[email], '', 'left', 120, ' ');
			# CODIGO DA INSCRICAO
			$conteudoArquivo.=exportaDados(0, '', 'right', 2, 0);
			# NUMERO DA INSCRICAO
			$conteudoArquivo.=exportaDados(0, '', 'right', 14, 0);
			# ENDERECO
			$conteudoArquivo.=exportaDados('', '', 'left', 40, ' ');
			# BAIRRO
			$conteudoArquivo.=exportaDados('', '', 'left', 12, ' ');
			# CEP
			$conteudoArquivo.=exportaDados(0, '', 'right', 8, '0');
			# CIDADE
			$conteudoArquivo.=exportaDados('', '', 'left', 15, ' ');
			# ESTADO
			$conteudoArquivo.=exportaDados('', '', 'left', 2, ' ');
			# BRANCOS COMPLEMENTO
			$conteudoArquivo.=exportaDados('', '', 'left', 180, ' ');
			# NUMERO SEQUENCIAL DO REGISTRO NO ARQUIVO
			$conteudoArquivo.=exportaDados($sequenciaArquivo++, '', 'right', 6, 0);
			# QUEBRA DE LINHA
			$conteudoArquivo.="\r\n";
	
		}*/
	}
		
	#### REGISTRO TRAILER
	# Identificacao do REGISTRO - 1
	$conteudoArquivo.=exportaDados(9, '', 'right', 1, 0);
	# BRANCOS COMPLEMENTO
	$conteudoArquivo.=exportaDados('', '', 'left', 393, ' ');
	# NUMERO SEQUENCIAL DO REGISTRO NO ARQUIVO
	$conteudoArquivo.=exportaDados($sequenciaArquivo++, '', 'right', 6, 0);
	# QUEBRA DE LINHA
	$conteudoArquivo.="\r\n";

	return($conteudoArquivo);
} 
# fim BANESPA


# Funcão para geração de arquivos - HSBC
function gerarArquivoRemessaHSBC($matriz) {

	global $conn, $tb, $corFundo, $corBorda;


	# sequencia de registros
	$sequenciaArquivo=1;
	
	# Variável de conteúdo de arquivo
	$conteudoArquivo='';
	# Conta corrente: Agencia + Conta + Dig
	$contaCorrente = $matriz['cobrancaAgencia'].$matriz['cobrancaConta'].$matriz['cobrancaContaDig'];
	# valores das instruções do hsbc
	$dadosBanco = buscaBancos($matriz['bancoNumero'], 'numero', 'igual', 'numero');
	$idBanco	=	resultadoSQL($dadosBanco, 0, 'id');
	$parametrosBancos = parametrosBancosCarregar($idBanco);
	# Instrução usada no arquivo remessa caso a instrução seja 16;
	$instrucao['multa_diaria']	= floatval( ( $parametrosBancos["remessa_hsbc_juros_diario_16"]
									? formatarValores( $parametrosBancos["remessa_hsbc_juros_diario_16"] ) : 0 ) );
	# data do sistema
	$data=dataSistema();
	######################################################################################
	# INICIO DO REGISTRO HEADER
	
	# Código do Registro - 001-001 - N(1) = "0"
	$conteudoArquivo .= exportaDados(0, '', 'right', 1, 0);
	# Código do Arquivo - 002-002 - N(1) = "1"
	$conteudoArquivo .= exportaDados(1, '', 'right', 1, 0);
	# Literal Arquivo - 003-009 - A(7) = "REMESSA"
	$conteudoArquivo .= exportaDados("REMESSA", '', 'left', 7, ' ');
	# Código do Serviço - 010-011 - N(2) = "01"
	$conteudoArquivo .= exportaDados(1, '', 'right', 2, 0);
	# Literal Servico - 012-026 - A(15) = "COBRANCA"
	$conteudoArquivo .= exportaDados("COBRANCA", '', 'left', 15, ' ');
	# Zero - 027-027 - N(1) = "0"
	$conteudoArquivo .= exportaDados(0, '', 'right', 1, 0);
	# Agência Cedente - 028-031 - N(4) = $matriz['cobrancaAgencia']
	$conteudoArquivo .= exportaDados($matriz['cobrancaAgencia'], '', 'right', 4, '0');
	# Sub-conta - 032-033 - N(2) = 55
	$conteudoArquivo .= exportaDados(55, '', 'right', 2, 0);
	# Conta Corrente - 034-044 - N(11) = $matriz['cobrancaAgencia'].$matriz['cobrancaConta'].$matriz['cobrancaContaDig']
	$conteudoArquivo .= exportaDados($contaCorrente, '', 'right', 11, '0');
	# Banco - 045-046 - (?)(2)
	$conteudoArquivo .= exportaDados("", '', 'left', 2, ' ');
	# Nome do Cliente (Cedente) - 047-076 - A(30) = 
	$conteudoArquivo .= exportaDados(formFormatarStringArquivoRemessa($matriz['cobrancaTitular'],'maiuscula'), '', 'left', 30, ' ');
	# Código do Banco - 077-079 - N(3) = 399
	$conteudoArquivo .= exportaDados($matriz['bancoNumero'], '', 'right', 3, 0);
	# Nome do banco - 080-094 - A(15) = "HSBC"
	$conteudoArquivo .= exportaDados(formFormatarStringArquivoRemessa($matriz['bancoNome'],'maiuscula'), '', 'left', 15, ' ');
	# Data de Gravação - 095-100 - [DDMMAA](6)
	$conteudoArquivo .= exportaDados($data['dia'].$data['mes'].substr($data['ano'], 2, 2), '', 'right', 6, '0');
	# Densidade - 101-105 - N(5) = 01600
	$conteudoArquivo .= exportaDados('01600', '', 'right', 5, '0');
	# Literal Densidade - 106-108 - A(3) = "BPI"
	$conteudoArquivo .= exportaDados('BPI', '', 'left', 3, ' ');
	# Banco - 109-110 - (?)(2)
	$conteudoArquivo .= exportaDados(' ', '', 'left', 2, ' ');
	# Sigla Layout - 111-117 - A(7) = 'LANCV08.' (?)
	$conteudoArquivo .= exportaDados('LANCV08.', '', 'right', 7, '0');
	# Banco - 118-394 - (?)(277)
	$conteudoArquivo .= exportaDados('', '', 'left', 277, ' ');
	# Número Sequencial - 395-400 - N(6) 
	$conteudoArquivo .= exportaDados($sequenciaArquivo++, '', 'right', 6, 0);
	# QUEBRA DE LINHA
	$conteudoArquivo.="\r\n";
	
	# FIM DO REGISTRO HEADER
	############################################################################################################
	
	$sql="SELECT
			$tb[DocumentosGerados].id idDocumentoGerado, 
			$tb[ContasReceber].valor valor, 
			$tb[ContasReceber].dtVencimento dtVencimento, 
			$tb[PessoasTipos].id idPessoaTipo, 
			$tb[Pessoas].nome nomePessoa, 
			$tb[Pessoas].razao razaoSocial, 
			$tb[Pessoas].id idPessoa, 
			$tb[Pessoas].tipoPessoa tipoPessoa,
			$tb[Faturamentos].id idFaturamentos
		FROM
			$tb[Pessoas], 
			$tb[PessoasTipos], 
			$tb[DocumentosGerados], 
			$tb[ContasReceber], 
			$tb[Faturamentos] 
		WHERE
			$tb[ContasReceber].idDocumentosGerados = $tb[DocumentosGerados].id 
			AND $tb[DocumentosGerados].idPessoaTipo = $tb[PessoasTipos].id 
			AND $tb[PessoasTipos].idPessoa = $tb[Pessoas].id 
			AND $tb[DocumentosGerados].idFaturamento = $tb[Faturamentos].id
			AND $tb[Faturamentos].id = $matriz[idFaturamento]";
	
	$consulta=consultaSQL($sql, $conn);
	for($i=0;$i<contaConsulta($consulta);$i++) {
	
		$idDocumentoGerado=resultadoSQL($consulta, $i, 'idDocumentoGerado');
		$valorBD = resultadoSQL($consulta, $i, 'valor');
		// calcula o valor que deve ser cobrado diariamente se ouver atraso
		$jurosDiarioMoeda = formatarValoresArquivoRemessa( round( ( $valorBD * $instrucao['multa_diaria'] )/100, 2) );

		$valor=formatarValoresArquivoRemessa(resultadoSQL($consulta, $i, 'valor'));
		if(strlen(trim($matriz[data_vencimento]))>0) {
			# Utilizar data de vencimento informada em form
			$dtVencimento=formatarData($matriz[data_vencimento]);
			$dtVencimento=substr($dtVencimento,0,4).substr($dtVencimento, -2);
		}
		else {
			# Utilizar data de vencimento do documento
			$dtVencimento=formatarData(converteData(resultadoSQL($consulta, $i, 'dtVencimento'),'banco','formdata'));
			$dtVencimento=substr($dtVencimento,0,4).substr($dtVencimento, -2);
		}
		$idPessoa=resultadoSQL($consulta, $i, 'idPessoa');
		$idPessoaTipo=resultadoSQL($consulta, $i, 'idPessoaTipo');
		$tipoPessoa=resultadoSQL($consulta, $i, 'tipoPessoa');
		$idFaturamentos= resultadoSQL($consulta,$i,'idFaturamentos');
		if($tipoPessoa=='F') {
			$nomePessoa=formFormatarStringArquivoRemessa(resultadoSQL($consulta, $i, 'nomePessoa'),'maiuscula');
		}
		elseif($tipoPessoa=='J'){
			$nomePessoa=formFormatarStringArquivoRemessa(resultadoSQL($consulta, $i, 'razaoSocial'),'maiuscula');
		}

		# Verificar Endereço Preferencial ou existente
		$enderecosPessoa=buscaEnderecosPessoas($idPessoaTipo, 'idPessoaTipo','igual','idTipo');
		
		# Selecionar Endereço
		$endereco=enderecoSelecionaPreferencial($enderecosPessoa, $matriz[tipoEndereco]);
		
		# Selecionar Documentos
		$documento=documentoSelecionaPreferencial($idPessoa, $tipoPessoa);	
		if ( strtolower( $documento ) == 'isento' ) $documento = '';
		
		### REGISTRO TRANSAÇÃO (obrigatorio)
		# IDENTIFICAÇÃO DO REGISTRO - 1
		#================================= pega os dados ClienteBanco =====================================#
		
		// ESTA CONSULTA TRAS O "ID" DI TIPO DE PESSOA PARA SELECIONAR NA TABELA DE RELACIONAMENTO CLIENTEBANCO
		$sql="SELECT
			  	$tb[PlanosPessoas].id id
			  FROM
				$tb[PlanosPessoas], $tb[Faturamentos]
			  WHERE
				$tb[PlanosPessoas].idFormaCobranca = $tb[Faturamentos].idFormaCobranca AND
				$tb[Faturamentos].id= $idFaturamentos AND
				$tb[PlanosPessoas].idPessoaTipo= $idPessoaTipo
		";
		$qry= consultaSQL($sql, $conn);
		$idPessoasPlanos= resultadoSQL($qry,0,'id');
		//CONSULTA DA TABELA DE RELACIONAMENTO PARA COMPLETAR OS CAMPOS AG, DIG, CC, DIG no arquivo remessa
		$sql="SELECT
			  	$tb[ClienteBanco].agencia,
				$tb[ClienteBanco].digAg,
				$tb[ClienteBanco].contacorrente,
				$tb[ClienteBanco].digCC
			  FROM
				$tb[ClienteBanco]
			  WHERE
				$tb[ClienteBanco].idPlanosPessoas= $idPessoasPlanos
		";

		$qry=consultaSQL($sql,$conn);
		if ( $qry && contaConsulta( $qry ) > 0 ){
			$ag= resultadoSQL($qry,0,'agencia');
			$digAg= resultadoSQL($qry,0,'digAg');
			$cc= resultadoSQL($qry,0,'contacorrente');
			$digCC= resultadoSQL($qry,0,'digCC');
			$cliBan= '';
		}
		#==================================================================================================#
		
		####################################################################################################
		# INICIO DO REGISTRO DETALHE
		
		# Código do Registro - 001-001 - N(1) = 1
		$conteudoArquivo .= exportaDados(1, '', 'right', 1, 0);
		# Código de Inscrição - 002-003 - N(2) = (01 - cpf / 02 - cnpj / 98 - nao tem / 99 - outros)
		$conteudoArquivo .= exportaDados(( ( $tipoPessoa=='J') ? 2 : 1 ), '', 'right', 2, 0);
		# Leandro - Modificada posição 4 a 17 para cnpj do titular da conta de acordo com especificação do banco:
		# Número de Inscricao (CPF / CNPJ) - 004-017 - N(14)
		#$conteudoArquivo .= exportaDados(cpfVerificaFormato($documento), '', 'right', 14, '0');
		$conteudoArquivo .= exportaDados(cnpjVerificaFormato($matriz['cobrancaCNPJ']), '', 'right', 14, '0');
		# Zero - 018-018 - N(1) = 0
		$conteudoArquivo .= exportaDados(0, '', 'right', 1, 0);
		# Agência Cedente - 019-022 - N(4)
		$conteudoArquivo.=exportaDados($matriz['cobrancaAgencia'], '', 'right', 4, '0');
		# Sub-conta - 023-024 - N(2) = 55
		$conteudoArquivo .= exportaDados(55, '', 'right', 2, 0);
		# Conta Corrente - 025-035 - N(11)
		$conteudoArquivo .= exportaDados($contaCorrente, '', 'right', 11, 0);
		# Brancos - 036-037 - A(2)
		$conteudoArquivo .= exportaDados('', '', 'right', 2, ' ');
		# Controle do Participante -  038-062 - A(25) - Mensagem com maximo de 24 caracteres + '*'
		$conteudoArquivo .= exportaDados( 
								formFormatarStringArquivoRemessa($matriz['mensagem2'],'maiuscula'), 
								'', 'left', 24, ' ') . '*';
		# Nosso Número - 063-073 - N(11) - (?)
		$conteudoArquivo .= exportaDados('', '', 'right', 11, 0);
		# Desconto Data-(2) - 074-079 - N(6)(DDMMAA)
		$conteudoArquivo .= exportaDados(0, '', 'right', 6, 0);
		# Valor Desconto-(2) - 080-090 - N(11) 
		$conteudoArquivo .= exportaDados(0, '', 'right', 11, '0');
		# Desconto Data-(3) - 091-096 - N(6)(DDMMAA)
		$conteudoArquivo .= exportaDados(0, '', 'right', 6, 0);
		# Valor Desconto-(3) - 097-107 - N(11) 
		$conteudoArquivo .= exportaDados(0, '', 'right', 11, '0');
		# Carteira - 108-108 - N(1)
		$conteudoArquivo .= exportaDados(1, '', 'right', 1, '0');
		# Código da Ocorrência  - 109-110 - N(2)
		$conteudoArquivo .= exportaDados(1, '', 'right', 2, '0');
		# Seu Número - 111-120 - A(10)
		$conteudoArquivo .= exportaDados($idDocumentoGerado, '', 'left', 10, ' ' );
		# Vencimento - 121-126 - N(6) = DDMMAA
		$conteudoArquivo .= exportaDados($dtVencimento, '', 'left', 6, ' ');
		# Valor do titulo - 127-139 - N(13)
		$conteudoArquivo .= exportaDados($valor, 'N', 'right', 13, 0);
		# Banco Cobrador - 140-142 - N(3)
		$conteudoArquivo .= exportaDados($matriz['bancoNumero'], '', 'right', 3, 0);
		# Leandro - De acordo com o banco essa informação deve se zerada:
		# Agencia Depositária - 143-147 - N(5) 
		#$conteudoArquivo .= exportaDados($matriz['cobrancaAgencia'], '', 'right', 5, '0');
		$conteudoArquivo .= exportaDados(0, '', 'right', 5, '0');
		# Especie do documento - 148-149 - N(2) = '01' - Duplicata
		$conteudoArquivo .= exportaDados(9, '', 'right', 2, '0');
		# Aceite - 150-150 - A(1) = (A: Aceito | N: Sem Aceite)
		$conteudoArquivo .= exportaDados('N', '', 'left', 1, ' ');
		# Data de Emissao do Titulo - 151-156 - N(6) = DDMMAA
		$conteudoArquivo .= exportaDados($data['dia'].$data['mes'].substr($data['ano'], 2, 2), '', 'right', 6, '0');
		# 1a Instrução - 157-158 - 02 (nao usado) (!).
		$conteudoArquivo .= exportaDados($matriz['mensagem'] , '', 'right', 2, 0); /*$matriz['instrucao1']*/
		# 2a Instrucao - 159-160 - 02 (nao usado) (!).
		$conteudoArquivo .= exportaDados('', '', 'right', 2, 0); /*$matriz['instrucao2']*/
		# Juros de Mora - 161-173 - N(13)
		$conteudoArquivo .= exportaDados( $jurosDiarioMoeda, '', 'right', 13, '0' );
		# Desconto Data - 174-179 - N(6) = DDMMAA
		$conteudoArquivo .= exportaDados(0, '', 'right', 6, '0');
		# Valor do Desconto - 180-192 - N(13)
		$conteudoArquivo .= exportaDados(0, '', 'right', 13, '0');
		# Valor do IOF - 193-205 - N(13)
		$conteudoArquivo .= exportaDados(0, '', 'right', 13, '0');
		# Valor do Abatimento - 206-218 - N(13)
		$conteudoArquivo .= exportaDados(0, '', 'right', 13, '0');
		# Código de Inscrição - 219-220 - N(2)  (01=cpf / 02=cnpj / 03=PIS / 98=nao tem / 99=outros)
		$conteudoArquivo .= exportaDados(( ( $tipoPessoa=='J') ? 2 : 1 ), '', 'right', 2, '0');
		# Número de inscrição - 221-234 - N(14) = (CPF | CNPJ)
		$conteudoArquivo .= exportaDados(cpfVerificaFormato($documento), '', 'right', 14, '0');
		# Nome do sacado - 235-274 - A(40)
		$conteudoArquivo .= exportaDados(formFormatarStringArquivoRemessa($nomePessoa,'maiuscula'), '', 'left', 40, ' ');
		# Endereço do Sacado - 275-312 - A(38)
		$conteudoArquivo .= exportaDados(formFormatarStringArquivoRemessa($endereco[endereco]." ".$endereco[complemento],'maiuscula'), '', 'left', 38, ' ');
		# Instrução de não recebimento do bloqueto - 313-314 - (?)
		$conteudoArquivo .= exportaDados('', '', 'left', 2, ' ');
		# Bairro - 315-326 - A(12)
		$conteudoArquivo .= exportaDados('', '', 'left', 12, ' ');
		# (CEP - 327-331 - N(5)) + (Sufixo do CEP - 332-334 - A(3)) => 327-334 - N(8)
		$conteudoArquivo .= exportaDados(cpfVerificaFormato($endereco['cep']), '', 'right', 8, '0');
		# Cidade do Sacado - 335-349 - A(15)
		$conteudoArquivo .= exportaDados(formFormatarStringArquivoRemessa($endereco['cidade'],'maiuscula'), '', 'left', 15, ' ');
		# Estado do Sacado - 350-351 - A(2)
		$conteudoArquivo .= exportaDados(formFormatarStringArquivoRemessa($endereco['uf'],'maiuscula'), '', 'left', 2, ' ');		
		# Sacador/Avalista - 352 - 390 - A(39)
		$conteudoArquivo .= exportaDados(formFormatarStringArquivoRemessa($matriz[mensagem],'maiuscula'), '', 'left', 39, ' ');
		# Tipo de Bloqueto - 391-391 - A(1)
		$conteudoArquivo .= exportaDados('', '', 'left', 1, ' ');
		# Prazo de Protesto -  392-393 - A(2)
		$conteudoArquivo .= exportaDados('', '', 'left', 2, ' ');
		# Moeda - 394-394 - N(1)
		$conteudoArquivo .= exportaDados(9, '', 'left', 1, 0);
		# Número Sequencial - 395-400 - N(6)
		$conteudoArquivo .= exportaDados($sequenciaArquivo++, '', 'right', 6, 0);
		# QUEBRA DE LINHA
		$conteudoArquivo.="\r\n";
	
		# Fim do Registro detalhe
		######################################################################
		
	}
	
	############################################################################
	# REGISTRO TRAILER
	
	# Código do Registro - 001-001 - N(1)
	$conteudoArquivo .= exportaDados(9, '', 'right', 1, 0);
	# Banco - 002-394 - A(393)
	$conteudoArquivo .= exportaDados('', '', 'left', 393, ' ');
	# Número Sequencial - 395-400 - N(6)
	$conteudoArquivo .= exportaDados($sequenciaArquivo++, '', 'right', 6, 0);
	# QUEBRA DE LINHA
	$conteudoArquivo .= "\r\n";
	# Fim do registro Trailer
	###########################################################################
	
	return($conteudoArquivo);
} 
# fim BRADESCO


# Função de ativação de arquivo remessa
function arquivoRemessaAtivar($idFaturamento) {

	global $conn, $tb;
	
	$sql="
		UPDATE 
			$tb[Faturamentos]
		SET
			remessa='A'
		WHERE
			id=$idFaturamento";
			
	$consulta=consultaSQL($sql, $conn);
	
	return($consulta);
}

# Funcão para geração de arquivos - Padrao FEBRABAN - bradesco
function gerarArquivoRemessaDebitoBradesco($matriz) {
	
	global $conn, $tb, $corFundo, $corBorda;
	
	# Data Atual
	$data=dataSistema();

	# sequencia de registros
	$sequenciaArquivo=1;
	
	# Variável de conteúdo de arquivo
	$conteudoArquivo='';

	### HEADER DE ARQUIVO
	# Codigo do registro
	$conteudoArquivo ='A';
	# Codigo de remessa
	$conteudoArquivo.='1';
	# Codigo do Convenio - 20
	$conteudoArquivo.=exportaDados($matriz[cobrancaConvenio], "X", "left", 20, " ");
	# Nome da empresa
	$conteudoArquivo.=exportaDados($matriz[cobrancaTitular], "N", "left", 20, " ");
	# Codigo do banco
	$conteudoArquivo.=exportaDados($matriz[bancoNumero], "N", "right", 3, "0");
	# Nome do banco
	$conteudoArquivo.=exportaDados($matriz[bancoNome], "X", "left", 20, " ");
	# Data da Gravacao
	$conteudoArquivo.=exportaDados(converteData($data[dataNormalData], "sistema", "remessa"), "N", "right", 8, "0");
	# numero sequencial do arquivo
	$conteudoArquivo.=exportaDados($matriz[idArquivo], "N", "right", 6, "0");
	# versao do layout
	$conteudoArquivo.="04";
	# Produto
	$conteudoArquivo.="DEBITO AUTOMATICO";
	# Livre
	$conteudoArquivo.=exportaDados("", "", "left", 52, " ")."\r\n";
	
	# Registro tipo E (remessa)
	
	//CONSULTA DA TABELA DE RELACIONAMENTO PARA COMPLETAR OS CAMPOS AG, DIG, CC, DIG no arquivo remessa

	# Registro tipo E (remessa)
	$sql="SELECT
			$tb[DocumentosGerados].id idDocumentoGerado, 
			$tb[ContasReceber].valor valor, 
			$tb[ContasReceber].dtVencimento dtVencimento, 
			$tb[PessoasTipos].id idPessoaTipo, 
			$tb[Pessoas].nome nomePessoa, 
			$tb[Pessoas].razao razaoSocial, 
			$tb[Pessoas].id idPessoa, 
			$tb[Pessoas].tipoPessoa tipoPessoa, 
			max($tb[PlanosDocumentosGerados].idPlano) idPlanosPessoas
		FROM
			$tb[Pessoas], 
			$tb[PessoasTipos], 
			$tb[DocumentosGerados],  
			$tb[ContasReceber],  
			$tb[Faturamentos], 
			$tb[PlanosDocumentosGerados]
		WHERE
			$tb[ContasReceber].idDocumentosGerados = $tb[DocumentosGerados].id 
			AND $tb[DocumentosGerados].idPessoaTipo = $tb[PessoasTipos].id 
			AND $tb[PessoasTipos].idPessoa = $tb[Pessoas].id 
			AND $tb[DocumentosGerados].idFaturamento = $tb[Faturamentos].id 
			AND $tb[DocumentosGerados].id = $tb[PlanosDocumentosGerados].idDocumentoGerado
			AND $tb[Faturamentos].id = ".$matriz[idFaturamento] .
			" GROUP by  $tb[DocumentosGerados].id, $tb[PlanosDocumentosGerados].idDocumentoGerado"
		;
	
	$consulta=consultaSQL($sql, $conn);
		
	$qtdRegistros=2; // recebe 2 (dois) por causa do header (A) e trailler (Z) instrucoes banespa verificar outros bancos
	$valorTT=0;
	
	for($i=0;$i<contaConsulta($consulta);$i++) {
		
		$idPlanosPessoas=resultadoSQL($consulta, $i, "idPlanosPessoas");
		
		$sqlClienteBanco =  "Select
							$tb[ClienteBanco].agencia,  
							$tb[ClienteBanco].contacorrente, 
							$tb[ClienteBanco].identificacao,
							$tb[ClienteBanco].digAg,
							$tb[ClienteBanco].digCC, 
							$tb[ClienteBanco].id AS idClienteBanco
						From
							$tb[ClienteBanco]
						WHERE
							$tb[ClienteBanco].idPlanosPessoas = '$idPlanosPessoas'
	
		";	
	
	
		$consultaClienteBanco=consultaSQL($sqlClienteBanco, $conn);
		
		
		
		$idPessoa=resultadoSQL($consulta, $i, "idPessoa");
		$dtVencimento=converteData(resultadoSQL($consulta, $i, "dtVencimento"), "banco", "remessa");
		$valor=str_replace('.', '', resultadoSQL($consulta, $i, "valor"));
		$nomeCliente=resultadoSQL($consulta,$i, "nomePessoa");
		$ag= resultadoSQL($consultaClienteBanco,0,'agencia');//
		$digAg= resultadoSQL($consultaClienteBanco,0,'digAg');//
		$cc= resultadoSQL($consultaClienteBanco,0,'contacorrente');//
		$digCC= resultadoSQL($consultaClienteBanco,0,'digCC');//
		$idDocumentoGerado = resultadoSQL($consulta,$i,'idDocumentoGerado');//
		
		# codigo do registro
		$conteudoArquivo.="E";
		# identificacao do cliente na empresa// identificacao deve ter no minimo 6 caaracters
		$conteudoArquivo.=exportaDados($idPessoa, "", "right", 25, "0");
		# Agencia para debito
		$conteudoArquivo.=exportaDados($ag, "X", "right", 4, "0");
		# Identificacao do Cliente no banco   
		$conteudoArquivo.=exportaDados($cc.$digCC, "X", "right", 14, "0");
		# Data do vencimento
		$conteudoArquivo.=exportaDados($dtVencimento, "N", "left", 8, "0");
		# Valor do débito
		$conteudoArquivo.=exportaDados( $valor, "N", "right", 15, "0");
		# codigo da moeda 01 UFIR (5 casas decimais) / 03 REAL (2 casas decimais)
		$conteudoArquivo.="03";
		# uso da empresa 
		$conteudoArquivo.=exportaDados($idDocumentoGerado, "X", "right", 60, "0");
		# livre
		$conteudoArquivo.=exportaDados('', "X", "left", 20, " ");
		# codigo do movimento: 0 debito normal / 1 cancelamento ou lancamento anterior ainda nao efetivado
		$conteudoArquivo.="0\r\n";

		$valorTT+=$valor;
		$qtdRegistros++;
	}
	
	### TRAILER DE ARQUIVO
	# codigo do registro
	$conteudoArquivo.="Z";
	# Quantidade de registro
	$conteudoArquivo.=exportaDados($qtdRegistros, "N", "right", 6, "0");
	# Valor total
	$conteudoArquivo.=exportaDados( $valorTT, "N", "right", 17, "0");
	# livre
	$conteudoArquivo.=exportaDados('', "X", "left", 126, " ")."\r\n";

	return($conteudoArquivo);
}


# Funcão para geração de arquivos - Padrao FEBRABAN - bradesco
function gerarArquivoRemessaDebitoPadrao($matriz) {
	
	global $conn, $tb, $corFundo, $corBorda;
	
	# Data Atual
	$data=dataSistema();

			
	# Variável de conteúdo de arquivo
	$conteudoArquivo='';

	### HEADER DE ARQUIVO
	# Codigo do registro
	$conteudoArquivo ='A';
	# Codigo de remessa
	$conteudoArquivo.='1';
	# Codigo do Convenio - 20
	$conteudoArquivo.=exportaDados( formFormatarStringArquivoRemessa( $matriz[cobrancaConvenio],'maiuscula'), "X", "left", 20, " ");
	# Nome da empresa
	$conteudoArquivo.=exportaDados( formFormatarStringArquivoRemessa( $matriz[cobrancaTitular],'maiuscula'), "X", "left", 20, " ");
	# Codigo do banco
	$conteudoArquivo.=exportaDados($matriz[bancoNumero], "N", "right", 3, "0");
	# Nome do banco
	$conteudoArquivo.=exportaDados( formFormatarStringArquivoRemessa( $matriz[bancoNome],'maiuscula' ), "X", "left", 20, " ");
	# Data da Gravacao
	$conteudoArquivo.=exportaDados(converteData($data[dataNormalData], "sistema", "remessa"), "N", "right", 8, "0");
	# numero sequencial do arquivo
	$conteudoArquivo.=exportaDados($matriz[idArquivo], "N", "right", 6, "0");
	# versao do layout
	$conteudoArquivo.="04";
	# Produto
	$conteudoArquivo.="DEBITO AUTOMATICO";
	# Livre
	$conteudoArquivo.=exportaDados("", "", "left", 52, " ")."\n";
	
	# Registro tipo E (remessa)
	
	//CONSULTA DA TABELA DE RELACIONAMENTO PARA COMPLETAR OS CAMPOS AG, DIG, CC, DIG no arquivo remessa

	# Registro tipo E (remessa)
	$sql="SELECT
			$tb[DocumentosGerados].id idDocumentoGerado, 
			$tb[ContasReceber].valor valor, 
			$tb[ContasReceber].dtVencimento dtVencimento, 
			$tb[PessoasTipos].id idPessoaTipo, 
			$tb[Pessoas].nome nomePessoa, 
			$tb[Pessoas].razao razaoSocial, 
			$tb[Pessoas].id idPessoa, 
			$tb[Pessoas].tipoPessoa tipoPessoa, 
			max($tb[PlanosDocumentosGerados].idPlano) idPlanosPessoas
		FROM
			$tb[Pessoas], 
			$tb[PessoasTipos], 
			$tb[DocumentosGerados],  
			$tb[ContasReceber],  
			$tb[Faturamentos], 
			$tb[PlanosDocumentosGerados]
		WHERE
			$tb[ContasReceber].idDocumentosGerados = $tb[DocumentosGerados].id 
			AND $tb[DocumentosGerados].idPessoaTipo = $tb[PessoasTipos].id 
			AND $tb[PessoasTipos].idPessoa = $tb[Pessoas].id 
			AND $tb[DocumentosGerados].idFaturamento = $tb[Faturamentos].id 
			AND $tb[PlanosDocumentosGerados].idDocumentoGerado = $tb[DocumentosGerados].id
			AND $tb[Faturamentos].id = ".$matriz[idFaturamento]	.
			" GROUP by  $tb[DocumentosGerados].id, $tb[PlanosDocumentosGerados].idDocumentoGerado"
	;
	
	
	$consulta=consultaSQL($sql, $conn);
	
	
	
	$qtdRegistros=2; // recebe 2 (dois) por causa do header (A) e trailler (Z) instrucoes banespa verificar outros bancos
	$valorTT=0;
	
	for($i=0;$i<contaConsulta($consulta);$i++) {
		
		$idPlanosPessoas=resultadoSQL($consulta, $i, "idPlanosPessoas");
		
		$sqlClienteBanco =  "Select
							$tb[ClienteBanco].agencia,  
							$tb[ClienteBanco].contacorrente, 
							$tb[ClienteBanco].identificacao,
							$tb[ClienteBanco].digAg,
							$tb[ClienteBanco].digCC, 
							$tb[ClienteBanco].id AS idClienteBanco
						From
							$tb[ClienteBanco]
						WHERE
							$tb[ClienteBanco].idPlanosPessoas = '$idPlanosPessoas'
	
		";	
		
		$consultaClienteBanco=consultaSQL($sqlClienteBanco, $conn);
		
		$idPessoa=resultadoSQL($consulta, $i, "idPessoa");
		$dtVencimento=converteData(resultadoSQL($consulta, $i, "dtVencimento"), "banco", "remessa");
		$valor=str_replace('.', '', resultadoSQL($consulta, $i, "valor"));
		$nomeCliente=resultadoSQL($consulta, $i, "nomePessoa");
		$idDocumentoGerado = resultadoSQL($consulta,$i,'idDocumentoGerado');
		
		if ( $consultaClienteBanco && contaConsulta( $consultaClienteBanco )) {
			$ag= resultadoSQL($consultaClienteBanco,0,'agencia');//
			$digAg= resultadoSQL($consultaClienteBanco,0,'digAg');//
			$cc= resultadoSQL($consultaClienteBanco,0,'contacorrente');//
			$digCC= resultadoSQL($consultaClienteBanco,0,'digCC');//
		}
		else{
			$ag = $digAg =  $cc=  $digCC= 0;
			echo "<br>";
			avisoNOURL( "ERRO:", "Cliente $nomeCliente sem dados para debito.", "100%");
		}
		
		# codigo do registro
		$conteudoArquivo.="E";
		# identificacao do cliente na empresa// identificacao deve ter no minimo 6 caaracters
		$conteudoArquivo.=exportaDados( formFormatarStringArquivoRemessa( $idPessoa,'maiuscula' ), "X", "left", 25, " ");
		# Agencia para debito
		$conteudoArquivo.=exportaDados( $ag, "X", "left", 4, " ");
		# Identificacao do Cliente no banco   
		$conteudoArquivo.=exportaDados($cc.$digCC, "X", "left", 14, " ");
		# Data do vencimento
		$conteudoArquivo.=exportaDados($dtVencimento, "N", "right", 8, "0");
		# Valor do débito
		$conteudoArquivo.=exportaDados( $valor, "N", "right", 15, "0");
		# codigo da moeda 01 UFIR (5 casas decimais) / 03 REAL (2 casas decimais)
		$conteudoArquivo.="03";
		# uso da empresa 
		$conteudoArquivo.=exportaDados( formFormatarStringArquivoRemessa( $idDocumentoGerado, 'maiuscula' ), "X", "right", 60, "0");
		# livre
		$conteudoArquivo.=exportaDados('', "X", "left", 20, " ");
		# codigo do movimento: 0 debito normal / 1 cancelamento ou lancamento anterior ainda nao efetivado
		$conteudoArquivo.="0\n";

		$valorTT+=$valor;
		$qtdRegistros++;
	}
	
	### TRAILER DE ARQUIVO
	# codigo do registro
	$conteudoArquivo.="Z";
	# Quantidade de registro
	$conteudoArquivo.=exportaDados($qtdRegistros, "N", "right", 6, "0");
	# Valor total
	$conteudoArquivo.=exportaDados( $valorTT, "N", "right", 17, "0");
	# livre
	$conteudoArquivo.=exportaDados('', "X", "left", 126, " ")."\n";

	return($conteudoArquivo);
}

# Funcão para geração de arquivos - BANSICREDI
function gerarArquivoRemessaBansicredi($matriz) {

	global $conn, $tb, $corFundo, $corBorda;


	# sequencia de registros
	$sequenciaArquivo=1;
	
	# Variável de conteúdo de arquivo
	$conteudoArquivo='';

	# data do sistema
	$data=dataSistema();
	
	### REGISTRO HEADER
	# 001 a 001 - Id do Registro Header - 1 
	$conteudoArquivo .= '0';
	# 002 a 002 - Id do Arquivo Remessa - 1
	$conteudoArquivo .='1';
	# 003 a 009 - Literal Remessa - 7
	$conteudoArquivo .= exportaDados("REMESSA", '', 'left', 7, ' ');
	# 010 a 011 - Código do Serviço de Cobrança - 2
	$conteudoArquivo .= exportaDados( 1, '', 'right', 2, 0 );
	# 012 a 026 - Literal Cobrança - 15
	$conteudoArquivo .= exportaDados("COBRANCA", '', 'left', 15, ' ');
	# 027 a 031 - Codigo do Cedente - 5
	$conteudoArquivo .= exportaDados(trim( $matriz['cobrancaConta'] ), '', 'right', 5, '0');
	# 032 a 045 - CIC/CGC do cedente - 14
	$conteudoArquivo .= exportaDados(cpfVerificaFormato($matriz['cobrancaCNPJ']), '', 'right', 14, '0');
	# 046 a 076 - Filler (Brancos) - 31
	$conteudoArquivo .= exportaDados('', '', 'left', 31, ' ');
	# 077 a 079 - Número do banco - 3
	$conteudoArquivo .= exportaDados( $matriz['bancoNumero'], '', 'right', 3, '0'); //$matriz['bancoNumero']
	# 080 a 094 - Nome do Banco - 15
	$conteudoArquivo .= exportaDados(formFormatarStringArquivoRemessa($matriz['bancoNome'],'maiuscula'), '', 'left', 15, ' ');
	# 095 a 102 - Data da geracao [AAAAMMDD] - 8
	$conteudoArquivo .= exportaDados($data['ano'].$data['mes'].$data['dia'], '', 'right', 8, '0');
	# 103 a 110 - Filler (Brancos) - 8
	$conteudoArquivo .= exportaDados('', '', 'left', 8, ' ');
	# 111 a 117 - Numero de Remessa - 7
	$conteudoArquivo .= exportaDados(( $matriz['numero_remessa'] ? $matriz['numero_remessa'] : $matriz['idArquivo'] ), '', 'right', 7, '0');
	# 118 a 390 - Filler (Brancos) - 273
	$conteudoArquivo .= exportaDados('', '', 'left', 273, ' ');
	# 391 a 394 - Identificação do Sistema (2.00) - 4
	$conteudoArquivo .= exportaDados('2.00', '', 'left', 4, ' ');
	# 395 a 400 - NUMERO SEQUENCIAL DO REGISTRO NO ARQUIVO - 6 
	$conteudoArquivo .= exportaDados($sequenciaArquivo++, '', 'right', 6, 0);
	# QUEBRA DE LINHA
	$conteudoArquivo .= "\r\n";
	
	$sql="SELECT
			$tb[DocumentosGerados].id idDocumentoGerado, 
			$tb[ContasReceber].valor valor, 
			$tb[ContasReceber].dtVencimento dtVencimento, 
			$tb[PessoasTipos].id idPessoaTipo, 
			$tb[Pessoas].nome nomePessoa, 
			$tb[Pessoas].razao razaoSocial, 
			$tb[Pessoas].id idPessoa, 
			$tb[Pessoas].tipoPessoa tipoPessoa,
			$tb[Faturamentos].id idFaturamentos
		FROM
			$tb[Pessoas], 
			$tb[PessoasTipos], 
			$tb[DocumentosGerados], 
			$tb[ContasReceber], 
			$tb[Faturamentos] 
		WHERE
			$tb[ContasReceber].idDocumentosGerados = $tb[DocumentosGerados].id 
			AND $tb[DocumentosGerados].idPessoaTipo = $tb[PessoasTipos].id 
			AND $tb[PessoasTipos].idPessoa = $tb[Pessoas].id 
			AND $tb[DocumentosGerados].idFaturamento = $tb[Faturamentos].id
			AND $tb[Faturamentos].id = $matriz[idFaturamento]";
	
	$consulta=consultaSQL($sql, $conn);
	for($i=0;$i<contaConsulta($consulta);$i++) {
	
		$idDocumentoGerado=resultadoSQL($consulta, $i, 'idDocumentoGerado');
		$valor=formatarValoresArquivoRemessa(resultadoSQL($consulta, $i, 'valor'));
		if(strlen(trim($matriz['data_vencimento']))>0) {
			# Utilizar data de vencimento informada em form
			$dtVencimento=formatarData($matriz['data_vencimento']);
			$dtVencimento=substr($dtVencimento,0,4).substr($dtVencimento, -2);
		}
		else {
			# Utilizar data de vencimento do documento
			$dtVencimento=formatarData(converteData(resultadoSQL($consulta, $i, 'dtVencimento'),'banco','formdata'));
			$dtVencimento=substr($dtVencimento,0,4).substr($dtVencimento, -2);
		}
		$idPessoa=resultadoSQL($consulta, $i, 'idPessoa');
		$idPessoaTipo=resultadoSQL($consulta, $i, 'idPessoaTipo');
		$tipoPessoa=resultadoSQL($consulta, $i, 'tipoPessoa');
		$idFaturamentos= resultadoSQL($consulta,$i,'idFaturamentos');
		if($tipoPessoa=='F') $nomePessoa=formFormatarStringArquivoRemessa(resultadoSQL($consulta, $i, 'nomePessoa'),'maiuscula');
		elseif($tipoPessoa=='J') $nomePessoa=formFormatarStringArquivoRemessa(resultadoSQL($consulta, $i, 'razaoSocial'),'maiuscula');

		# Verificar Endereço Preferencial ou existente
		$enderecosPessoa = buscaEnderecosPessoas($idPessoaTipo, 'idPessoaTipo','igual','idTipo');
		
		# Selecionar Endereço
		$endereco = enderecoSelecionaPreferencial($enderecosPessoa, $matriz['tipoEndereco']);
		
		# Selecionar código da praça do sacado
		$praca = new PracaSicredi();
		$praca->setConnection( $conn );
		$codigoPraca = $praca->getPraca( substr( formFormatarStringArquivoRemessa( $endereco['cidade'], 'maiuscula' ), 0, 25 ) );
		
		# Selecionar Documentos
		$documento = documentoSelecionaPreferencial($idPessoa, $tipoPessoa);	

		### REGISTRO TRANSAÇÃO (obrigatorio)
		# IDENTIFICAÇÃO DO REGISTRO - 1
		#================================= pega os dados ClienteBanco =====================================#
		
		// ESTA CONSULTA TRAS O "ID" DI TIPO DE PESSOA PARA SELECIONAR NA TABELA DE RELACIONAMENTO CLIENTEBANCO
		$sql="SELECT
			  	$tb[PlanosPessoas].id id
			  FROM
				$tb[PlanosPessoas], $tb[Faturamentos]
			  WHERE
				$tb[PlanosPessoas].idFormaCobranca = $tb[Faturamentos].idFormaCobranca AND
				$tb[Faturamentos].id=$idFaturamentos AND
				$tb[PlanosPessoas].idPessoaTipo= $idPessoaTipo
		";
		$qry= consultaSQL($sql, $conn);
		$idPessoasPlanos= resultadoSQL($qry,0,'id');

		#==================================================================================================#
		
		# 001 a 001 - Identificação do registro detalhe - 1
		$conteudoArquivo .= '1';
		# 002 a 002 - Tipo de Cobrança - 1
		$conteudoArquivo .= 'A';
		# 003 a 003 - Tipo de Carteira - 1
		$conteudoArquivo .= 'A';
		# 004 a 016 - Filler (Brancos) - 13
		$conteudoArquivo .= exportaDados('', '', 'left', 13, ' ');
		# 017 a 017 - Tipo de Moeda - 1
		$conteudoArquivo .= 'A';
		# 018 a 018 - Tipo de Desconto - 1 ("A" - Valor; "B" - Percentual)
		$conteudoArquivo .= 'A';
		# 019 a 019 - Tipo de Juros - 1 ("A" - Valor; "B" - Percentual)
		$conteudoArquivo .= 'A';
		# 020 a 023 - Código da 1.a Mensagem para bloqueto - 4
		$conteudoArquivo .= exportaDados('', '', 'left', 4, '0');
		# 024 a 027 - Código da 2.a Mensagem para bloqueto - 4
		$conteudoArquivo .= exportaDados('', '', 'left', 4, '0');
		# Código da 3.a Mensagem para bloqueto - 4
		$conteudoArquivo .= exportaDados('', '', 'left', 4, '0');
		# Filler (Brancos) - 16
		$conteudoArquivo .= exportaDados('', '', 'left', 16, ' ');		
		# Número BANSICREDI sem edição - 15
		$conteudoArquivo .= exportaDados('0', '', 'left', 9, '0');
		$conteudoArquivo .= exportaDados('', '', 'right', 6, ' ');
		# Data da Instrução - 8
		$conteudoArquivo .= exportaDados($data['ano'].$data['mes'].$data['dia'], '', 'right', 8, '0');
		# Campo alterado, quando ocorrência "31" - 1
		$conteudoArquivo .= exportaDados('', '', 'left', 1, ' ');
		# Postagem do Titulo ("S" - Para postar título; "N" Não postar e remeter para o Cedente) - 1
		$conteudoArquivo .= 'S';
		# Protesto do titulo com valor atualizado ("S" - Atualizar; "N" - Não atualizar) - 1
		$conteudoArquivo .= 'N';
		# Impressão do bloqueto ("A" - Pelo BANSICREDI; "B" - Pelo Cedente) - 1
		$conteudoArquivo .= 'A';
		# Num. da Pacerla do Carnê - 2
		$conteudoArquivo .= exportaDados(0, '', 'right', 2, 0);
		# Num. total de parcelas do carnê - 2
		$conteudoArquivo .= exportaDados(0, '', 'right', 2, 0);
		# Filler (brancos) - 4
		$conteudoArquivo .= exportaDados('', '', 'left', 4, ' '); 
		# Valor de Desconto por dia de antecipação - 10
		$conteudoArquivo .= exportaDados(0, '', 'right', 10, 0);
		# % multa por pagamento em atraso - 4
		$conteudoArquivo .= exportaDados(0, '', 'right', 4, 0);
		# Filler (Brancos) - 12
		$conteudoArquivo .= exportaDados('', '', 'left', 12, ' '); 
		# Instrução - 2
		$conteudoArquivo .= exportaDados(1, '', 'right', 2, 0);
		# Seu número (nunca se repete) - 10 
		$conteudoArquivo .= exportaDados($idDocumentoGerado, '', 'right', 10, 0);
		# Data de Vencimento (DDNNAA) - 6
		$conteudoArquivo .= exportaDados($dtVencimento, '', 'left', 6, ' ');
		# Valor do titulo - 13
		$conteudoArquivo .= exportaDados($valor, 'N', 'right', 13, 0);
		# Filler (Brancos) - 9
		$conteudoArquivo .= exportaDados('', '', 'left', 9, ' '); 		
		# Espécie de Documento - 1
		$conteudoArquivo .= 'A';
		# Aceite do Titulo ("S" ou "N") - 1
		$conteudoArquivo .= 'S';
		# Data de Emissão DDMMAA - 6
		$conteudoArquivo .= exportaDados($data['dia'].$data['mes'].substr($data['ano'], 2, 2), '', 'right', 6, '0');
		# Instrução de Protesto Automático ("00" - Não auto; "06" - auto) - 2 
		$conteudoArquivo .= exportaDados(0, '', 'right', 2, '0');
		# Num. de dias p/ Protesto Automático (minimo 5 dias) - 2
		$conteudoArquivo .= exportaDados(0, '', 'right', 2, 0);
		# Valor/% de Juros por dia de atraso - 13
		$conteudoArquivo .= exportaDados(0, '', 'right', 13, 0);
		# Data Limite p/ concessão de Desconto (DDMMAA) - 6 (!)
		$conteudoArquivo .= exportaDados('', '', 'left', 6, '0');
		# Valor/% do Desconto - 13
		$conteudoArquivo .= exportaDados(0, '', 'right', 13, 0);
		# Filler (zeros) - 13
		$conteudoArquivo .= exportaDados('', '', 'left', 13, 0); 		
		# Valor do Abatimento - 13
		$conteudoArquivo .= exportaDados(0, '', 'right', 13, 0);
		# Tipo de Pessoa do Sacado: PF (1) ou PJ (2) - 1 
		$conteudoArquivo .= ( ( $tipoPessoa == 'J' ) ? 2 : 1);
		# Filler (Zeros) - 1
		$conteudoArquivo .= '0';
		# CIC/CGC do Sacado - 14
		if ( strtolower( $documento ) == 'isento' ) $documento = '';
		$conteudoArquivo .= exportaDados(cpfVerificaFormato($documento), '', 'right', 14, '0');
		# Nome do Sacado - 40
		$conteudoArquivo .= exportaDados(formFormatarStringArquivoRemessa($nomePessoa,'maiuscula'), '', 'left', 40, ' ');
		# Endereço do Sacado - 40
		$conteudoArquivo .= exportaDados(formFormatarStringArquivoRemessa($endereco['endereco']." ".$endereco['complemento'], 'maiuscula'), '', 'left', 40, ' ');
		# Código do sacado na Ag. Cedente - 5 (?)
		$conteudoArquivo .= exportaDados('', '', 'right', 5, '0');
		# 320 a 325 - Código da praça do sacado - 6
		$conteudoArquivo .= exportaDados($codigoPraca, '', 'right', 6, '0');
		# 326 a 326 - Filler (Brancos) - 1
		$conteudoArquivo .= ' ';
		# 327 a 334 - CEP do Sacado - 8TDKOM Informática Ltda.
		$conteudoArquivo .= exportaDados(cpfVerificaFormato($endereco['cep']), '', 'right', 8, '0');
		# 335 a 339 - Código do sacado junto ao cliente - 5
		$conteudoArquivo .= exportaDados( $idPessoaTipo, '', 'right', 5, '0');
		# 340 a 353 - CIC/CGC do Sacador Avalista - 14
		$conteudoArquivo .= exportaDados('', '', 'right', 14, ' ');
		# 354 a 394 - Nome do sacador avalisa - 41
		$conteudoArquivo .= exportaDados('', '', 'right', 41, ' ');
		# 395 a 400 - Numero sequalcial do registro - 6
		$conteudoArquivo .= exportaDados($sequenciaArquivo++, '', 'right', 6, '0');		
		# QUEBRA DE LINHA
		$conteudoArquivo.="\r\n";
	}
	
	#### REGISTRO TRAILER
	# 001 a 001 - Identificacao do Registro Trailer - 1
	$conteudoArquivo .= '9';
	# 002 a 002 - Identificação do Arquivo remessa - 1
	$conteudoArquivo .= '1';
	# 003 a 005 - Número do BANSICREDI - 3
	$conteudoArquivo .= exportaDados($matriz['bancoNumero'], '', 'right', 3, '0');
	# 006 a 010 - Código do Cedente - 5
	$conteudoArquivo .= exportaDados(trim( $matriz['cobrancaConta'] ), '', 'right', 5, '0');
	# 011 a 394 - Filler (Brancos) - 384
	$conteudoArquivo.=exportaDados('', '', 'left', 384, ' ');
	# 395 a 400 - Número Sequencial do Registro - 6
	$conteudoArquivo.=exportaDados($sequenciaArquivo++, '', 'right', 6, 0);
	# QUEBRA DE LINHA
	$conteudoArquivo.="\r\n";

	return($conteudoArquivo);
} 
# fim BANSICREDI

function banco2real($numero = "0.0000", $casas = 2) {

	$retorno = $numero;
	if (! $retorno && $retorno != 0 ) $retorno = "";
	if ($numero) {
		if ( strchr($retorno, ',') ) {
			$retorno = str_replace(",",".",$numero);
		}
		if (! strchr($retorno, ".") && $retorno>0) {
			$retorno .= ".".str_repeat("0", $casas);
		}
		$partes = explode(".", $retorno);
		$retorno = $partes[0].",".substr($partes[1].str_repeat("0", $casas), 0, $casas);
	}
	return $retorno;
}

//# Funcão para geração de arquivos - Padrao BRADESCO
//function gerarArquivoRemessaDebitoBradesco($matriz) {
//	
//	global $conn, $tb, $corFundo, $corBorda;
//	
//	# Data Atual
//	$data=dataSistema();
//
//	# sequencia de registros
//	$sequenciaArquivo=1;
//	
//	# Variável de conteúdo de arquivo
//	$conteudoArquivo='';
//
//	### HEADER DE ARQUIVO
//	# Codigo do registro
//	$conteudoArquivo ='A';
//	# Codigo de remessa
//	$conteudoArquivo.='1';
//	# Codigo do Convenio - 20
//	$conteudoArquivo.=exportaDados($matriz[cobrancaConvenio], "", "left", 20, " ");
//	# Nome da empresa
//	$conteudoArquivo.=exportaDados($matriz[cobrancaTitular], "", "left", 20, " ");
//	# Codigo do banco
//	$conteudoArquivo.=exportaDados($matriz[bancoNumero], "N", "right", 3, "0");
//	# Nome do banco
//	$conteudoArquivo.=exportaDados($matriz[bancoNome], "X", "left", 20, " ");
//	# Data da Gravacao
//	$conteudoArquivo.=exportaDados(converteData($data[dataNormalData], "sistema", "remessa"), "N", "right", 8, "0");
//	# numero sequencial do arquivo
//	$conteudoArquivo.=exportaDados($sequenciaArquivo++, "N", "right", 6, "0");
//	# versao do layout
//	$conteudoArquivo.="04";
//	# Produto
//	$conteudoArquivo.="DEBITO AUTOMATICO";
//	# Livre
//	$conteudoArquivo.=exportaDados("", "", "left", 52, " ")."\n";
//	
////CONSULTA DA TABELA DE RELACIONAMENTO PARA COMPLETAR OS CAMPOS AG, DIG, CC, DIG no arquivo remessa
//$sql="SELECT
//			$tb[DocumentosGerados].id idDocumentoGerado, 
//			$tb[ContasReceber].valor valor, 
//			$tb[ContasReceber].dtVencimento dtVencimento, 
//			$tb[PessoasTipos].id idPessoaTipo, 
//			$tb[Pessoas].nome nomePessoa, 
//			$tb[Pessoas].razao razaoSocial, 
//			$tb[Pessoas].id idPessoa, 
//			$tb[Pessoas].tipoPessoa tipoPessoa 
//		FROM
//			$tb[Pessoas], 
//			$tb[PessoasTipos], 
//			$tb[DocumentosGerados], 
//			$tb[ContasReceber], 
//			$tb[Faturamentos] 
//		WHERE
//			$tb[ContasReceber].idDocumentosGerados = $tb[DocumentosGerados].id 
//			AND $tb[DocumentosGerados].idPessoaTipo = $tb[PessoasTipos].id 
//			AND $tb[PessoasTipos].idPessoa = $tb[Pessoas].id 
//			AND $tb[DocumentosGerados].idFaturamento = $tb[Faturamentos].id
//			AND $tb[Faturamentos].id = ".$matriz[idFaturamento];
//	$consulta=consultaSQL($sql, $conn);
//	
//	$qtdRegistros=2; // recebe 2 (dois) por causa do header (A) e trailler (Z) instrucoes banespa verificar outros bancos
//	$valorTT=0;
//	
//	for($i=0;$i<contaConsulta($consulta);$i++) {
//		$idPessoa=resultadoSQL($consulta, $i, "idPessoa");
//		$dtVencimento=converteData(resultadoSQL($consulta, $i, "dtVencimento"), "banco", "remessa");
//		$valor=resultadoSQL($consulta, $i, "valor");
//		$nomeCliente=resultadoSQL($consulta, $i, "nomePessoa");//
//		
//		
//		# codigo do registro
//		$conteudoArquivo.="E";
//		# identificacao do cliente na empresa
//		$conteudoArquivo.=exportaDados($idPessoa, "X", "right", 6, "0");
//		$conteudoArquivo.=exportaDados('','X','left',19,' ' );
//		# Agencia para debito
//		$conteudoArquivo.=exportaDados($matriz['cobrancaAgencia'], "X", "right", 4, "0");
//		# Identificacao do Cliente no banco
//		//$conteudoArquivo.=exportaDados($getCodigoClienteBanco($idPessoa), "X", "left", 14, " ");
//		//$conteudoArquivo.=exportaDados("[codigo]", "X", "left", 14, " ");
//		$conteudoArquivo.=exportaDados($matriz['cobrancaConta'].$matriz['cobrancaContaDig'], "N", "right", 8, "0");
//		$conteudoArquivo.=exportaDados('', "X", "left", 6, " ");
//		# Data do vencimento
//		$conteudoArquivo.=exportaDados($dtVencimento, "N", "left", 8, "0");
//		# Valor do débito
//		$conteudoArquivo.=exportaDados( number_format( $valor, 2, '','' ), "N", "right", 15, "0");
//		# codigo da moeda 01 UFIR (5 casas decimais) / 03 REAL (2 casas decimais)
//		$conteudoArquivo.="03";
//		# uso da empresa
//		$conteudoArquivo.=exportaDados($nomeCliente, "X", "left", 60, " ");
//		# livre
//		$conteudoArquivo.=exportaDados('', "X", "left", 20, " ");
//		# codigo do movimento: 0 debito normal / 1 cancelamento ou lancamento anterior ainda nao efetivado
//		$conteudoArquivo.="0\n";
//
//		$valorTT+=$valor;
//		$qtdRegistros++;
//	}
//	
//	### TRAILER DE ARQUIVO
//	# codigo do registro
//	$conteudoArquivo.="Z";
//	# Quantidade de registro
//	$conteudoArquivo.=exportaDados($qtdRegistros, "N", "right", 6, "0");
//	# Valor total
//	$conteudoArquivo.=exportaDados(number_format( $valorTT,2, '', ''), "N", "right", 17, "0");
//	# livre
//	$conteudoArquivo.=exportaDados('', "X", "left", 126, " ")."\n";
//
//	return($conteudoArquivo);
//}
?>
