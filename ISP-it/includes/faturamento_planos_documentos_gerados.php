<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 12/09/2003
# Ultima alteração: 12/09/2003
#    Alteração No.: 001
#
# Função:
#    Painel - Funções para cadastro de Planos de Documentos Gerados



# Função para manuteção de Documentos Gerados
function dbPlanoDocumentoGerado($matriz, $tipo) {

	global $conn, $tb, $sessLogin;
	
	$data=dataSistema();
	
	$idUsuario=buscaIDUsuario($sessLogin[login],'login','igual','id');
	
	if($tipo=='incluir') {
		$seqGeracao=0;
		$seqDocumento=0;
		
		# incluir registro
		$sql="
			INSERT INTO 
				$tb[PlanosDocumentosGerados]
			VALUES (
				$matriz[idPlanoDocumentoGerado],
				$matriz[idDocumentoGerado],
				$matriz[idPlano],
				$matriz[idFormaCobranca],
				$matriz[idVencimento],
				'$matriz[dtVencimentoPlanoDocumentoGerado]'
			)";
	}
	
	elseif($tipo=='excluir') {
		$sql="
			DELETE FROM
				$tb[PlanosDocumentosGerados]
			WHERE
				id=$matriz[id]
		";
	}
	
	
	if($sql) {
		$consulta=consultaSQL($sql, $conn);
	}
	
	return($consulta);
}


# Funçao para novo ID de Faturamento
function novoIDPlanoDocumentoGerado() {

	global $conn, $tb;
	
	$sql="
		SELECT 
			MAX($tb[PlanosDocumentosGerados].id)+1 id
		FROM
			$tb[PlanosDocumentosGerados]";
	
	$consulta=consultaSQL($sql, $conn);
	
	if(!$consulta || contaConsulta($consulta)==0) {
		$id=1;
	}
	else {
		$id=resultadoSQL($consulta, 0, 'id');
		
		
		if(!$id && contaConsulta($consulta)==1) {
			$id=1;
		}
	}

	return($id);
}



# função de busca 
function buscaPlanosDocumentosGerados($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[PlanosDocumentosGerados] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[PlanosDocumentosGerados] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[PlanosDocumentosGerados] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[PlanosDocumentosGerados] WHERE $texto ORDER BY $ordem";
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




# Função para listagem de documentos gerados
function listarPlanosDocumentosGerados($modulo, $sub, $acao, $registro, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;

	$consulta=buscaPlanosDocumentosGerados($registro, 'idDocumentoGerado','igual','id');
	
	# Cabeçalho		
	# Motrar tabela de busca
	novaTabela("[Planos do Documento Gerado]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
	
	# Caso não hajam servicos para o servidor
	if(!$consulta || contaConsulta($consulta)==0) {
		# Não há registros
		itemTabelaNOURL('Não há planos no documento informado!', 'left', $corFundo, 3, 'txtaviso');
	}
	else {

		# Cabeçalho
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTabela('Nome do Plano', 'center', '45%', 'tabfundo0');
			itemLinhaTabela('Valor', 'center', '20%', 'tabfundo0');
			itemLinhaTabela('Forma de Cobrança', 'center', '35%', 'tabfundo0');
		fechaLinhaTabela();

		
		for($i=0;$i<contaConsulta($consulta);$i++) {

			# Mostrar registro
			$id=resultadoSQL($consulta, $i, 'id');
			$idDocumentoGerado=resultadoSQL($consulta, $i, 'idDocumentoGerado');
			$idPlano=resultadoSQL($consulta, $i, 'idPlano');
			$plano=dadosPlanos($idPlano);
			$idFormaCobranca=resultadoSQL($consulta, $i, 'idFormaCobranca');
			$idVencimento=resultadoSQL($consulta, $i, 'idVencimento');
			$dtVencimento=resultadoSQL($consulta, $i, 'dtVencimento');
			
			# Valor
			$valor=valorPlanosDocumentosGerados($id);
			$total+=$valor;

			# Valor Formatado
			$valorFormatado=formatarValoresForm($valor);
			$valorFormatado=htmlMontaOpcao("<a href=?modulo=$modulo&sub=planos_documentos&acao=detalhe&registro=$id>$valorFormatado</a>",'lancamento');
			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela($plano[nome], 'left', '45%', 'normal10');
				itemLinhaTabela($valorFormatado, 'center', '20%', 'normal10');
				itemLinhaTabela(formSelectFormaCobranca($idFormaCobranca,'','check'), 'center', '35%', 'normal10');
			fechaLinhaTabela();
			
		} #fecha laco de montagem de tabela
		
	} #fecha servicos encontrados
	
	# Total
	novaLinhaTabela($corFundo, '100%');
		itemLinhaTabela("Total", 'right', '35%', 'tabfundo0');
		itemLinhaTabela(formatarValoresForm($total), 'center', '15%', 'txtok');
		itemLinhaForm('&nbsp;', 'center', 'middle', $corFundo, 3, 'tabfundo0');
	fechaLinhaTabela();	
	
	fechaTabela();
}





# Função para detalhar o Faturamento
function detalhesPlanosDocumentosGerados($modulo, $sub, $acao, $registro, $matriz) {

	# Mostrar detalhes do faturamento
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessPlanos, $conn, $tb;
	
	
	# Buscar detalhes do Plano e Faturamento
	$planosDocumentos=buscaPlanosDocumentosGerados($registro, 'id','igual','id');
	
	if($planosDocumentos && contaConsulta($planosDocumentos)>0) {
		# dados do plano documento gerado
		$idDocumentoGerado=resultadoSQL($planosDocumentos, 0, 'idDocumentoGerado');
		$idPlano=resultadoSQL($planosDocumentos, 0, 'idPlano');
		
		# Buscar dados do documento
		$documentoGerado=buscaDocumentosGerados($idDocumentoGerado, 'id','igual','id');
		if($documentoGerado && contaConsulta($documentoGerado)>0) {
			# Dados do documento
			$idFaturamento=resultadoSQL($documentoGerado, 0, 'idFaturamento');
			$idPessoaTipo=resultadoSQL($documentoGerado, 0, 'idPessoaTipo');
			$idUsuario=resultadoSQL($documentoGerado, 0, 'idUsuario');
			$dtGeracao=resultadoSQL($documentoGerado, 0, 'dtGeracao');
			$dtAtivacao=resultadoSQL($documentoGerado, 0, 'dtAtivacao');
			$status=resultadoSQL($documentoGerado, 0, 'status');
		
			# Detalhes do Faturamento
			$matriz[idFaturamento]=$idFaturamento;
			verFaturamento($modulo, $sub, $acao, $idFaturamento, $matriz);
			
			echo "<br>";
			
			# Visualizar documento
			verDocumentosGerados($modulo, $sub, $acao, $idDocumentoGerado, $matriz);

			echo "<br>";

			# Informações sobre documento
			$matriz[idDocumentoGerado]=$idDocumentoGerado;
			verPlanosDocumentosGerados($modulo, $sub, $acao, $registro, $matriz);	
			
			echo "<br>";
			
			# Listar Serviços dos Planos
			listarServicosPlanosDocumentosGerados($modulo, $sub, $acao, $registro, $matriz);
			
		}
	}
	
}



# Função para visualização de documentos
function verPlanosDocumentosGerados($modulo, $sub, $acao, $registro, $matriz) {

	# Mostrar detalhes do faturamento
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessPlanos, $conn, $tb;
	
	# Informações sobre documento
	
	# Cabeçalho		
	# Motrar tabela de busca
	novaTabela2("[Visualização do Plano do Documento Gerado]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=documentos&acao=detalhe&registro=$matriz[idDocumentoGerado]&matriz[idFaturamento]=$matriz[idFaturamento]>Listar Planos do Documento</a>",'listar');
	itemTabelaNOURL($opcoes, 'right', $corFundo, 2, 'tabfundo1');

	if($registro) {
		$consulta=buscaPlanosDocumentosGerados($registro, 'id','igual','id');
		
		# Caso não hajam servicos para o servidor
		if(!$consulta || contaConsulta($consulta)==0) {
			# Não há registros
			itemTabelaNOURL('Não há documentos gerados neste faturamento', 'left', $corFundo, 2, 'txtaviso');
		}
		else {
		
			#dados do faturamento
			$id=resultadoSQL($consulta, 0, 'id');
			$idPlano=resultadoSQL($consulta, 0, 'idPlano');
			$dtVencimento=resultadoSQL($consulta, 0, 'dtVencimento');
			$plano=dadosPlanos($idPlano);
			
			# Cabeçalho
			itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('<b>Plano:</b>', 'right', '30%', 'tabfundo1');
				itemLinhaTabela($plano[nome], 'left', '70%', 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('<b>Vencimento:</b>', 'right', '30%', 'tabfundo1');
				itemLinhaTabela(converteData($dtVencimento,'banco','formdata'), 'left', '70%', 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('<b>Valor:</b>', 'right', '30%', 'tabfundo1');
				itemLinhaTabela(formatarValoresForm(valorPlanosDocumentosGerados($id)), 'left', '70%', 'tabfundo1');
			fechaLinhaTabela();
			
		} #fecha servicos encontrados
	}
	else {
		# Não há registros
		itemTabelaNOURL('Documento não informado!', 'left', $corFundo, 2, 'txtaviso');
	}
	
	fechaTabela();
}


# Função para totalização de valor do documento
function valorPlanosDocumentosGerados($idPlanoDocumentoGerado) {

	global $conn, $tb;
	
	$sql="
		SELECT
			SUM($tb[ServicosPlanosDocumentosGerados].valor) valor
		FROM
			$tb[ServicosPlanosDocumentosGerados],
			$tb[PlanosDocumentosGerados]
		WHERE
			$tb[PlanosDocumentosGerados].id=$tb[ServicosPlanosDocumentosGerados].idPlanoDocumentoGerado
			AND $tb[PlanosDocumentosGerados].id=$idPlanoDocumentoGerado
	";
	
	
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
		$valor=resultadoSQL($consulta, 0, 'valor');
	}

	return($valor);
}



?>
