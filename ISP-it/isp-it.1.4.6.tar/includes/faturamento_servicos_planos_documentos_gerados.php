<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 12/09/2003
# Ultima altera��o: 23/09/2003
#    Altera��o No.: 003
#
# Fun��o:
#    Painel - Fun��es para cadastro de Servicos Planos de Documentos Gerados



# Fun��o para manute��o de Documentos Gerados
function dbServicoPlanoDocumentoGerado($matriz, $tipo) {

	global $conn, $tb, $sessLogin;
	
	$data=dataSistema();
	
	$idUsuario=buscaIDUsuario($sessLogin[login],'login','igual','id');
	
	if($tipo=='incluir') {
		
		# incluir registro
		$sql="
			INSERT INTO 
				$tb[ServicosPlanosDocumentosGerados]
			VALUES (
				0,
				'$matriz[idPlanoDocumentoGerado]',
				'$matriz[idServicoPlano]',
				'$matriz[valor]'
			)";
			
	}
	
	elseif($tipo=='excluirtodos') {
		$sql="
			DELETE FROM
				$tb[ServicosPlanosDocumentosGerados]
			WHERE
				idPlanoDocumentoGerado=$matriz[id]
		";
	}
	
	
	if($sql) {
		$consulta=consultaSQL($sql, $conn);
	}
	
	return($consulta);
}


# Fun�ao para novo ID de Faturamento
function novoIDServicoPlanoDocumentoGerado() {

	global $conn, $tb;
	
	$sql="
		SELECT 
			MAX($tb[ServicosPlanosDocumentosGerados].id)+1 id
		FROM
			$tb[ServicosPlanosDocumentosGerados]";
			
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



# fun��o de busca 
function buscaServicosPlanosDocumentos($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[ServicosPlanosDocumentosGerados] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[ServicosPlanosDocumentosGerados] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[ServicosPlanosDocumentosGerados] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[ServicosPlanosDocumentosGerados] WHERE $texto ORDER BY $ordem";
	}
	
	# Verifica consulta
	if($sql){
		$consulta=consultaSQL($sql, $conn);
		# Retornvar consulta
		return($consulta);
	}
	else {	
		# Mensagem de aviso
		$msg="Consulta n�o pode ser realizada por falta de par�metros";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
	}
} # fecha fun��o de busca




# Fun��o para listagem de documentos gerados
function listarServicosPlanosDocumentosGerados($modulo, $sub, $acao, $registro, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $conn, $tb;

	//$consulta=buscaServicosPlanosDocumentos($registro, 'idPlanoDocumentoGerado','igual','id');
	
	$sql="
		SELECT
			$tb[ServicosPlanosDocumentosGerados].id id,
			$tb[ServicosPlanosDocumentosGerados].idPlanoDocumentoGerado idPlanoDocumentoGerado,
			$tb[ServicosPlanosDocumentosGerados].idServicosPlanos idServicosPlanos,
			$tb[ServicosPlanosDocumentosGerados].valor valor,
			$tb[Servicos].nome nome,
			$tb[Servicos].valor valorServico,
			$tb[ServicosPlanos].valor valorServicoPlano,
			$tb[ServicosPlanos].dtCadastro dtCadastro,
			$tb[ServicosPlanos].dtAtivacao dtAtivacao,
			$tb[PlanosPessoas].especial especial
		FROM
			$tb[ServicosPlanosDocumentosGerados],
			$tb[PlanosDocumentosGerados],
			$tb[DocumentosGerados],
			$tb[Faturamentos],
			$tb[ServicosPlanos],
			$tb[Servicos],
			$tb[PlanosPessoas]
		WHERE
			$tb[Faturamentos].id=$tb[DocumentosGerados].idFaturamento
			AND $tb[DocumentosGerados].id=$tb[PlanosDocumentosGerados].idDocumentoGerado
			AND $tb[PlanosDocumentosGerados].id=$tb[ServicosPlanosDocumentosGerados].idPlanoDocumentoGerado
			AND $tb[ServicosPlanosDocumentosGerados].idServicosPlanos=$tb[ServicosPlanos].id
			AND $tb[ServicosPlanos].idServico=$tb[Servicos].id
			AND $tb[ServicosPlanos].idPlano=$tb[PlanosPessoas].id
			AND $tb[ServicosPlanosDocumentosGerados].idPlanoDocumentoGerado=$registro
		ORDER BY
			$tb[ServicosPlanosDocumentosGerados].id
	";
	
	$consulta=consultaSQL($sql, $conn);
	
	# Cabe�alho		
	# Motrar tabela de busca
	novaTabela("[Servi�os do Plano]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
	
	# Caso n�o hajam servicos para o servidor
	if(!$consulta || contaConsulta($consulta)==0) {
		# N�o h� registros
		itemTabelaNOURL('N�o h� servi�os para o plano gerado!', 'left', $corFundo, 5, 'txtaviso');
	}
	else {

		# Cabe�alho
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTabela('Nome do Servi�o', 'center', '40%', 'tabfundo0');
			itemLinhaTabela('Valor Faturado', 'center', '15%', 'tabfundo0');
			itemLinhaTabela('Valor do Servi�o', 'center', '15%', 'tabfundo0');
			itemLinhaTabela('Data Cadastro', 'center', '15%', 'tabfundo0');
			itemLinhaTabela('Data Ativa��o', 'center', '15%', 'tabfundo0');
		fechaLinhaTabela();
		
		for($i=0;$i<contaConsulta($consulta);$i++) {

			# Mostrar registro
			$id=resultadoSQL($consulta, $i, 'id');
			$idPlanoDocumentoGerado=resultadoSQL($consulta, $i, 'idPlanoDocumentoGerado');
			$idServicosPlanos=resultadoSQL($consulta, $i, 'idServicosPlanos');
			$nomeServico=resultadoSQL($consulta, $i, 'nome');
			$valor=resultadoSQL($consulta, $i, 'valor');
			$especial=resultadoSQL($consulta, $i, 'especial');
			$dtCadastro=resultadoSQL($consulta, $i, 'dtCadastro');
			$dtAtivacao=resultadoSQL($consulta, $i, 'dtAtivacao');
			
			# Verificar valor do servico
			if($especial=='S') $valorServico=resultadoSQL($consulta, $i, 'valorServicoPlano');
			else $valorServico=resultadoSQL($consulta, $i, 'valorServico');

			
			# Valor
			$total+=$valor;

			# Valor Formatado
			$valorFormatado=formatarValoresForm($valor);
			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela($nomeServico, 'left', '40%', 'normal10');
				itemLinhaTabela($valorFormatado, 'center', '15%', 'txtok');
				itemLinhaTabela(formatarValoresForm($valorServico), 'center', '15%', 'txtaviso');
				itemLinhaTabela(converteData($dtCadastro, 'banco', 'formdata'), 'center', '15%', 'normal10');
				itemLinhaTabela(converteData($dtAtivacao, 'banco', 'formdata'), 'center', '15%', 'normal10');
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


?>
