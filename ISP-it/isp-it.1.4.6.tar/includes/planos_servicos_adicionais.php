<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 28/07/2003
# Ultima alteração: 12/03/2004
#    Alteração No.: 019
#
# Função:
#    Painel - Funções para servicos adicionais


# Função de banco de dados - Pessoas
function dbServicoAdicional($matriz, $tipo) {

	global $conn, $tb, $modulo, $sub, $acao;
	
	# Sql de inclusão
	if($tipo=='incluir') {
		$sql="INSERT INTO $tb[ServicosAdicionais] VALUES (0,
		$matriz[idPlano],
		$matriz[idServicoPlano],
		$matriz[idTipoServicoAdicional],
		'$matriz[nome]',
		'$matriz[valor]',
		'$matriz[dtCadastro]',
		'$matriz[dtVencimento]',
		'$matriz[dtCancelamento]',
		'$matriz[dtCobranca]',
		'$matriz[dtEspecial]',
		'$matriz[adicionalFaturamento]',
		'$matriz[status]')";
		
	} #fecha inclusao
	elseif($tipo=='excluir') {
		$sql="DELETE FROM $tb[ServicosAdicionais] where id=$matriz[id]";
	}
	elseif($tipo=='excluirtodos') {
		$sql="DELETE FROM $tb[ServicosAdicionais] where idPlano=$matriz[id]";
	}
	elseif($tipo=='alterar') {
		$sql="UPDATE $tb[ServicosAdicionais] 
			SET 
				dtVencimento='$matriz[dtVencimento]',
				valor='$matriz[valor]',
				nome='$matriz[nome]',
				dtEspecial='$matriz[dtEspecial]',
				status='$matriz[status]'
			WHERE 
				id=$matriz[id]";
	}
	elseif($tipo=='cancelar') {
		$sql="UPDATE $tb[ServicosAdicionais] 
			SET 
				dtCancelamento='$matriz[dtCancelamento]',
				status='C'
			WHERE 
				id=$matriz[id]";
	}
	elseif($tipo=='desativar') {
		$sql="UPDATE $tb[ServicosAdicionais] 
			SET 
				status='I'
			WHERE 
				id=$matriz[id]";
	}
	elseif($tipo=='ativar') {
		$sql="UPDATE $tb[ServicosAdicionais] 
			SET 
				status='A'
			WHERE 
				id=$matriz[id]";
	}
	elseif($tipo=='cobranca') {
		$sql="UPDATE $tb[ServicosAdicionais] 
			SET 
				dtCobranca='$matriz[dtCobranca]',
				status='B'
			WHERE 
				id=$matriz[id]";
	}

	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
}



# função de busca 
function buscaServicosAdicionais($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[ServicosAdicionais] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[ServicosAdicionais] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[ServicosAdicionais] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[ServicosAdicionais] WHERE $texto ORDER BY $ordem";
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





# função para adicionar pessoa
function servicosAdicionais($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessPlanos;
	
	# Recebe ID do Serviço do Plano - Procurar detalhes
	$consulta=buscaServicosPlanos($registro, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		# Prosseguir e procurar detalhes sobre plano
		$idPlano=resultadoSQL($consulta, 0, 'idPlano');
		$idServico=resultadoSQL($consulta, 0, 'idServico');
		
		# Procurar por Pessoa
		$consultaPlanos=buscaPlanos($idPlano, 'id','igual','id');
		
		if($consultaPlanos && contaConsulta($consultaPlanos)>0) {
		
			# prosseguir e mostarar pessoa e plano
			$idPessoa=resultadoSQL($consultaPlanos, 0, 'idPessoaTipo');
			
			# Ver o Serviço
			//verServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
			
			# Ver dados da pessoa
			verPessoas('cadastros', 'clientes', 'ver', $idPessoa, $matriz);
			echo "<br>";
			
			# Ver Plano
			verPlanos($modulo, $sub, 'abrir',$idPlano, $matriz);
			
			# Listar Serviços do Plano
			listarServicosAdicionais($modulo, $sub, $acao, $registro, $matriz);
			
		}
		else {
			# Erro
			$msg="ERRO ao selecionar o Plano do Cliente!";
			$url="?modulo=cadastros&sub=clientes";
			aviso("Aviso", $msg, $url, 760);
		}
	}
	else {
		# Erro
		$msg="ERRO ao selecionar o Serviço do Plano!";
		$url="?modulo=cadastros&sub=clientes";
		aviso("Aviso", $msg, $url, 760);
	}
}



# Função para listagem 
function listarServicosAdicionais($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessPlanos;

	echo "<br>";
	
	if($acao=='servicosadicionais') $consulta=buscaServicosAdicionais("idServicoPlano=$registro AND status != 'C'", '','custom','dtVencimento');
	else $consulta=buscaServicosAdicionais($registro, 'idServicoPlano','igual','dtVencimento');
	
	# Cabeçalho		
	# Motrar tabela de busca
	novaTabela("[Serviços Adicionais]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 6);
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionarservicoadicional&registro=$registro>Adicionar serviço adicional</a>",'incluir');
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=servicosadicionais&registro=$registro>Servicos Ativos</a>",'listar');
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=servicosadicionaistodos&registro=$registro>Mostrar todos</a>",'listar');
	itemTabelaNOURL($opcoes, 'right', $corFundo, 6, 'tabfundo1');
	
	# Caso não hajam servicos para o servidor
	if(!$consulta || contaConsulta($consulta)==0) {
		# Não há registros
		itemTabelaNOURL('Não há serviços adicionais cadastrados neste serviço', 'left', $corFundo, 6, 'txtaviso');
	}
	else {

		# Cabeçalho
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTabela('Descrição do Serviço Adicional', 'center', '35%', 'tabfundo0');
			itemLinhaTabela('Tipo', 'center', '15%', 'tabfundo0');
			itemLinhaTabela('Vencimento', 'center', '10%', 'tabfundo0');
			itemLinhaTabela('Valor', 'center nowrap', '10%', 'tabfundo0');
			itemLinhaTabela('Status', 'center', '10%', 'tabfundo0');
			itemLinhaTabela('Opções', 'center', '20%', 'tabfundo0');
		fechaLinhaTabela();

		for($i=0;$i<contaConsulta($consulta);$i++) {
			
			$id=resultadoSQL($consulta, $i, 'id');
			$idPlano=resultadoSQL($consulta, $i, 'idPlano');
			$idServicoPlano=resultadoSQL($consulta, $i, 'idServicoPlano');
			$idTipoServicoAdicional=resultadoSQL($consulta, $i, 'idTipoServicoAdicional');
			$dtVencimento=resultadoSQL($consulta, $i, 'dtVencimento');
			$valor=resultadoSQL($consulta, $i, 'valor');
			$nome=resultadoSQL($consulta, $i, 'nome');
			$status=resultadoSQL($consulta, $i, 'status');
			
			# Checar status
			if($status=='A') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=verservicoadicional&registro=$id>Ver</a>",'ver');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterarservicoadicional&registro=$id>Alterar</a>",'alterar');
				$opcoes.="<br>";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=cancelarservicoadicional&registro=$id>Cancelar</a>",'cancelar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=desativarservicoadicional&registro=$id>Desativar</a>",'ativar');
				$class='txtok';
				$class2='txtok8';
			}
			elseif($status=='I') {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=verservicoadicional&registro=$id>Ver</a>",'ver');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterarservicoadicional&registro=$id>Alterar</a>",'alterar');
				$opcoes.="<br>";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=cancelarservicoadicional&registro=$id>Cancelar</a>",'cancelar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=ativarservicoadicional&registro=$id>Ativar</a>",'desativar');
				$class='txtaviso';
				$class2='txtaviso8';
			}
			else {
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=verservicoadicional&registro=$id>Ver</a>",'ver');
				$class='txtaviso';
				$class2='txtaviso8';
			}
			

			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela($nome, 'center', '35%', 'normal10');
				itemLinhaTabela(formSelectTipoServicoAdicional($idTipoServicoAdicional,'','check'), 'center', '15%', 'bold8');
				itemLinhaTabela(converteData($dtVencimento,'banco','formdata'), 'center', '10%', 'normal10');
				itemLinhaTabela(formatarValoresForm($valor), 'center', '10%', 'txtaviso');
				itemLinhaTabela(formSelectStatusServicoAdicional($status,'','check'), 'center', '10%', 'normal10');
				itemLinhaTabela($opcoes, 'left', '20%', 'normal8');
			fechaLinhaTabela();
			
		} #fecha laco de montagem de tabela
		
		fechaTabela();
	} #fecha servicos encontrados
	
}#fecha função de listagem




# funcao para listagem de servicos adicionais
function listarServicosAdicionaisVencimento($idServicoPlano, $dtVencimento) {

	global $corFundo, $corBorda, $modulo, $sub;
	
	$dtVencimento=date('Y-m-d',$dtVencimento);
	
	$consulta=buscaServicosAdicionais("idServicoPlano=$idServicoPlano AND dtVencimento='$dtVencimento' and status='A'", '','custom','dtCadastro');
	
	if($consulta && contaConsulta($consulta)>0) {
	
		for($a=0;$a<contaConsulta($consulta);$a++) {
			$nome=resultadoSQL($consulta, $a, 'nome');
			$dtVencimento=resultadoSQL($consulta, $a, 'dtVencimento');
			$valor=resultadoSQL($consulta, $a, 'valor');
			
			$nome=htmlMontaOpcao("<a href=?modulo=lancamentos&sub=planos&acao=servicosadicionais&registro=$idServicoPlano>$nome</a>",'lancamento');
		
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Serviço Adicional', 'center', '20%', 'txtok');
				itemLinhaTabela($nome, 'left', '50%', 'normal10');
				itemLinhaTabela(formatarValoresForm($valor), 'center', '15%', 'txtok');
				itemLinhaTabela(converteData($dtVencimento, 'banco','formdata'), 'center', '15%', 'bold10');
			fechaLinhaTabela();
		}
	}


}



/**
 * @return void
 * @param unknown $matriz
 * @desc Adicionar serviço adicional quando fatura for inferior
*/
function adicionarServicoAdicionalFaturaInferior($matriz) {
	
	global $conn, $tb;
	
	/*
	Buscar por plano ativo
	Buscar por serviço do plano
	Incluir serviço adicional, com tipo = 3
	*/
	
	# Buscar Planos do Documento Gerado
	# Buscar Serviços do Plano Documento Gerado
//	$sql="
//		SELECT
//			$tb[PlanosDocumentosGerados].idPlano,
//			$tb[ServicosPlanosDocumentosGerados].idServicosPlanos
//		FROM
//			$tb[ServicosPlanosDocumentosGerados], 
//			$tb[PlanosDocumentosGerados],
//			$tb[DocumentosGerados]
//		WHERE 
//			$tb[ServicosPlanosDocumentosGerados].idPlanoDocumentoGerado = $tb[PlanosDocumentosGerados].id 
//			AND $tb[PlanosDocumentosGerados].idDocumentoGerado = $tb[DocumentosGerados].id 
//			AND $tb[DocumentosGerados].idPessoaTipo = $matriz[idPessoaTipo] 
//			AND $tb[DocumentosGerados].idFaturamento  = $matriz[idFaturamento]
//	";
//	
//	$consulta=consultaSQL($sql, $conn);
//	
//	if($consulta && contaConsulta($consulta)>0) {
//	
//		# Verificar se no mes anterior de vencimento, houve fatura para este cliente
//		# caso não haja fatura anterior, significa que serviço adicional já havia sido
//		# cobrado. Gerar nova cobrança
//		$dtVencimentoAnterior=dataSomaMes($matriz[dtVencimento], -1);
//		$sql="
//			SELECT 
//				$tb[DocumentosGerados].id, 
//				$tb[ContasReceber].id, 
//				$tb[ContasReceber].dtVencimento 
//			FROM 
//				$tb[ContasReceber],  
//				$tb[DocumentosGerados]
//			WHERE  
//				$tb[ContasReceber].idDocumentosGerados = $tb[DocumentosGerados].id 
//				AND $tb[DocumentosGerados].idPessoaTipo = $matriz[idPessoaTipo]
//				AND $tb[ContasReceber].dtVencimento ='$matriz[dtVencimento]'
//		";
//		
//		$consultaCobrancaAnterior=consultaSQL($sql, $conn);
//		
//		if($consultaCobrancaAnterior && contaConsulta($consultaCobrancaAnterior)>0) {
//			
//			# Dados do Faturamento
//			$faturamento=dadosFaturamento($matriz[idFaturamento]);
//			$vencimento=dadosVencimento($faturamento[idVencimento]);
//	
//			$matriz[idPlano]=resultadoSQL($consulta, 0, 'idPlano');
//			$matriz[idServicoPlano]=resultadoSQL($consulta, 0, 'idServicosPlanos');
//			$matriz[idTipoServicoAdicional]=3;
//			$matriz[nome]="Cobrança abaixo do valor mínimo para faturamento: [$faturamento[mes]/$faturamento[ano]]";
//			$matriz[dtCadastro]=$data[dataBanco];
//			$matriz[dtVencimento]=dataSomaMes($matriz[dtVencimento], 1); # proximo mes
//			$matriz[dtCancelamento]='';
//			$matriz[dtCobranca]='';
//			$matriz[dtEspecial]='';
//			$matriz[adicionalFaturamento]='S';
//			$matriz[status]='A';
//			
//			dbServicoAdicional($matriz, 'incluir');

	# Buscar Planos do Documento Gerado
	# Buscar Serviços do Plano Documento Gerado
	// obs. os 
	$sql = "SELECT 
			$tb[ServicosPlanosDocumentosGerados].idServicosPlanos, 
			$tb[PlanosDocumentosGerados].idPlano,
			$tb[PlanosPessoas].especial
		FROM 
				$tb[ServicosPlanosDocumentosGerados] 
		INNER JOIN $tb[PlanosDocumentosGerados]
			On ($tb[ServicosPlanosDocumentosGerados].idPlanoDocumentoGerado = $tb[PlanosDocumentosGerados].id)
		INNER JOIN $tb[DocumentosGerados] 
			On ($tb[PlanosDocumentosGerados].idDocumentoGerado = $tb[DocumentosGerados].id)
		INNER JOIN $tb[PlanosPessoas]
			On ($tb[PlanosDocumentosGerados].idPlano = $tb[PlanosPessoas].id)
		WHERE 
			$tb[DocumentosGerados].idPessoaTipo = $matriz[idPessoaTipo]
			AND $tb[DocumentosGerados].idFaturamento = $matriz[idFaturamento]
	   ORDER BY $tb[PlanosPessoas].status";
			
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
		$futuroFaturamento=0;
		
		$faturamento=dadosFaturamento($matriz[idFaturamento]);
		
		$vencimento=dadosVencimento($faturamento[idVencimento]);
		$dtVencimento=dataSomaMes($matriz[dtVencimento], 1);
		$mes=substr($dtVencimento, 5, 2);
		$ano=substr($dtVencimento, 0, 4);
		
		// Navega pelos planos do usuario verficando se existira uma cobranca no proximo mes.
		for ($i=0; $i < contaConsulta($consulta); $i++){
			$idPlano = resultadoSQL($consulta, $i, 'idPlano');
			$especial = resultadoSQL($consulta, $i, 'especial');
			//valorPlano($idPlano, $especial, $idVencimento, $sqlADD, $mes, $ano, $parametro) 
			$valor = valorPlano($idPlano, $especial, $vencimento, "", $mes, $ano, "");
			if ($valor > 0){
				$futuroFaturamento = 1;
				break ;
			}		 
		}
		
		// somente  adia a cobranca caso haja uma cobranca posterior...
		if ($futuroFaturamento) {
			$matriz[idPlano]=resultadoSQL($consulta, 0, 'idPlano');
			$matriz[idServicoPlano]=resultadoSQL($consulta, 0, 'idServicosPlanos');
			$matriz[idTipoServicoAdicional]=3;
			$matriz[nome]="Cobrança abaixo do valor mínimo para faturamento: [$faturamento[mes]/$faturamento[ano]]";
			$matriz[dtCadastro]=$data[dataBanco];
			$matriz[dtVencimento]=dataSomaMes($matriz[dtVencimento], 1); # proximo mes
			$matriz[dtCancelamento]='';
			$matriz[dtCobranca]='';
			$matriz[dtEspecial]='';
			$matriz[adicionalFaturamento]='S';
			$matriz[status]='A';
		
			dbServicoAdicional($matriz, 'incluir');
			return($matriz); 
		}
		elseif(!$futuroFaturamento && strtoupper($faturaCancelados) == "S" ){
			//gera faturamento normalmente caso o cliente nao tenha faturamento futuro > 0 E
			//o flag fatura_cancelados estiver setando S
			return (0);
		}
	}
	
	// retorna nao gerando faturamentos	
	return(1);
			
		
			# retorna valor para que não gere contas a receber
			# com os dados do lançamento do serviço adicional
//			return($matriz);
//print_r($matriz); //
			
//	}
//		else {
	# retorna nulo para que serviço gere contas a receber
//			return (0);
//		}
//	}
}


/**
 * @return unknown
 * @param unknown $matriz
 * @param unknown $tipo
 * @desc Adicionar Serviço adicional quando ocorrer um cancelamento de serviço
*/
function adicionarServicoAdicionalServicoPlano($matriz, $tipo, $acao="Cancelamento" ) {

	global $conn, $tb, $corFundo, $corBorda;
	
	$data=dataSistema();
	
	$sql="
		SELECT
			$tb[Servicos].idTipoCobranca idTipoCobranca,
			$tb[Servicos].valor valor,
			$tb[Servicos].id idServico,
			$tb[ServicosPlanos].id idServicoPlano,
			$tb[ServicosPlanos].idPlano,
			$tb[ServicosPlanos].idStatus,
			$tb[ServicosPlanos].valor valorServico,
			$tb[ServicosPlanos].diasTrial,
			$tb[ServicosPlanos].dtAtivacao,
			$tb[StatusServicos].cobranca cobranca,
			$tb[PlanosPessoas].especial,
			$tb[PlanosPessoas].idPessoaTipo,
			$tb[PlanosPessoas].idVencimento,
			$tb[TipoCobranca].forma formaCobranca,
			$tb[TipoCobranca].proporcional proporcional,
			$tb[TipoCobranca].tipo tipoCobranca,
			$tb[StatusServicos].status status
		FROM
			$tb[Servicos],
			$tb[ServicosPlanos],
			$tb[PlanosPessoas],
			$tb[TipoCobranca],
			$tb[StatusServicos]
		WHERE
			$tb[Servicos].id=$tb[ServicosPlanos].idServico
			AND $tb[ServicosPlanos].idPlano=$tb[PlanosPessoas].id
			AND $tb[Servicos].idTipoCobranca=$tb[TipoCobranca].id
			AND $tb[StatusServicos].id=$tb[ServicosPlanos].idStatus
			AND $tb[ServicosPlanos].id=$matriz[id]";
			
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
		$id=resultadoSQL($consulta, 0, 'idServico');
		$tipoCobranca=resultadoSQL($consulta, 0, 'tipoCobranca');
		$idTipoCobranca=resultadoSQL($consulta, 0, 'idTipoCobranca');
		$idVencimento=resultadoSQL($consulta, 0, 'idVencimento');
		$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
		$idStatus=resultadoSQL($consulta, 0, 'idStatus');
		$status=resultadoSQL($consulta, 0, 'status');
		$formaCobranca=resultadoSQL($consulta, 0, 'formaCobranca');
		$especial=resultadoSQL($consulta, 0, 'especial');
		$cobranca=resultadoSQL($consulta, 0, 'cobranca');
		$proporcional=resultadoSQL($consulta, 0, 'proporcional');
		$dtAtivacao=resultadoSQL($consulta, 0, 'dtAtivacao');
		$valorServico=resultadoSQL($consulta, 0, 'valorServico');
		$valor=resultadoSQL($consulta, 0, 'valor');
		$diasTrial=resultadoSQL($consulta, 0, 'diasTrial');
		$idPlano=resultadoSQL($consulta, 0, 'idPlano');
		$idServicoPlano=resultadoSQL($consulta, 0, 'idServicoPlano');
		$idServico=resultadoSQL($consulta, 0, 'idServico'); #20050913
		
		# Dados do Vencimento
		$vencimento=dadosVencimento($idVencimento);
		
		$vencimento[ano]=substr(formatarData($matriz[dtCancelamento]),0,4);
		$vencimento[mes]=substr(formatarData($matriz[dtCancelamento]),4,2);
		$vencimento[dia]=substr(formatarData($matriz[dtCancelamento]),6,2);
		
		# Dados da Ativação
		$ativacao[ano]=substr(formatarData($dtAtivacao),0,4);
		$ativacao[mes]=substr(formatarData($dtAtivacao),4,2);
		$ativacao[dia]=substr(formatarData($dtAtivacao),6,2);
		
		# Calcular Valor do serviço
		if($especial=='S') $valorCalculoServico=$valorServico;
		else $valorCalculoServico=$valor;
		
		# Verificar se serviço tem valor Proporcional
		if($proporcional=='S' && $tipoCobranca != 'pre') {
		
			# Verificar se serviço estava ativo
			//if($status=='A') {
				# Calcular dias e valor proporcional
				$valorCalculado=calculaValorProporcionalCancelamento($ativacao, $diasTrial, $vencimento, $valorCalculoServico);
			//}
		}
	
		/* Codigo comentado, já que servicos pre-pagos tem emissao de faturamento previa
		   e cancelamentos pos data de faturamento nao devem gerar cobrancas
		else {
			if($tipoCobranca == 'pre') {
				if($data[dia] > $vencimento[diaFaturamento]) {
					$valorCalculado[valor]=$valor;
					$ano=$vencimento[ano];
					$mes=$vencimento[mes];
					$dia=$vencimento[diaVencimento];
					
					if($data[dia] > $vencimento[diaFaturamento]) $tmpVencimento=mktime(0,0,0,$mes+2, $dia, $ano);
					else $tmpVencimento=mktime(0,0,0,$mes+1, $dia, $ano);
					
					$valorCalculado[dtVencimento]=$tmpVencimento;
				}
			}
			else {
				$valorCalculado=calculaValorNaoProporcional($dtAtivacao, $diasTrial, $vencimento, $valorCalculoServico);
			}
		}*/

		if($valorCalculado && $valorCalculado[valor] > 0 ) {
			# Adicionar servico Adicional
			$dtVencimento=date('Y-m-d 00:00:00', $valorCalculado[dtVencimento]);
			
			if($data[dataBanco] > $dtVencimento) {
			
				# Adicionar dias ao vencimento
				$ano=date('Y', $valorCalculado[dtVencimento]);
				$mes=date('m', $valorCalculado[dtVencimento]);
				$dia=$vencimento[diaVencimento];
				
				if($data[dia] > $vencimento[diaFaturamento]) $tmpVencimento=mktime(0,0,0,$mes+1, $dia, $ano);
				else $tmpVencimento=mktime(0,0,0,$mes, $vencimento[diaVencimento], $ano);
				
				$dtVencimento=$tmpVencimento;
			}
			else {
				# Adicionar dias ao vencimento
				$ano=date('Y', $valorCalculado[dtVencimento]);
				$mes=date('m', $valorCalculado[dtVencimento]);
				$dia=$vencimento[diaVencimento];
				
				if($data[dia] > $vencimento[diaFaturamento]) $tmpVencimento=mktime(0,0,0,$mes, $dia, $ano);
				else $tmpVencimento=mktime(0,0,0,$mes, $vencimento[diaVencimento], $ano);
				
				$dtVencimento=$tmpVencimento;
			}

			# Verificar se a data de vencimento refere-se a um faturamento já gerado
			$dtVencimento=calculaVencimentoCancelamentoPessoa($dtVencimento, $idPessoaTipo);

			$dtVencimento=date('Y-m-d', $dtVencimento);

			#$matriz[nome]="Valor referente ao cancelamento do serviço";
			$matriz[nome]="Valor referente $acao de serviço"; #20050913
			$matriz[valor]=$valorCalculado[valor];
			$matriz[dtVencimento]=$dtVencimento;
			$matriz[dtCadastro]=$data[dataBanco];
			$matriz[status]='A';
			$matriz[idServicoPlano]=$idServicoPlano;
			$matriz['idServico']=$idServico;  #20050913
			$matriz[idTipoServicoAdicional] = ( $acao != "Cancelamento" ? "3" : "1" );
			
			dbServicoAdicional($matriz, 'incluir');
			
			if($tipo=='ver') {
				# Mostrar Mensagem
				novaTabela2("[Serviço Adicional Incluido ao Faturamento]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
					# Opcoes Adicionais
					novaLinhaTabela($corFundo, '100%');
						itemLinhaNOURL('&nbsp;', 'left', $corFundo, 2, 'normal10');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						itemLinhaNOURL("ATENÇÃO: $acao de Serviço gerou cobrança adicional!<br>Conferir valores abaixo:", 'center', $corFundo, 2, 'txtaviso');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						itemLinhaNOURL('&nbsp;', 'left', $corFundo, 2, 'normal10');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>Descrição:</b>', 'right', 'middle', '40%', $corFundo, 0, 'normal10');
						itemLinhaForm($matriz[nome], 'left', 'top', $corFundo, 0, 'normal10');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>Valor:</b>', 'right', 'middle', '40%', $corFundo, 0, 'normal10');
						itemLinhaForm(formatarValoresForm($matriz[valor]), 'left', 'top', $corFundo, 0, 'normal10');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>Utilização:</b>', 'right', 'middle', '40%', $corFundo, 0, 'normal10');
						itemLinhaForm("$valorCalculado[dias] dias", 'left', 'top', $corFundo, 0, 'normal10');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>Vencimento:</b>', 'right', 'middle', '40%', $corFundo, 0, 'normal10');
						itemLinhaForm(converteData($matriz[dtVencimento],'banco','formdata'), 'left', 'top', $corFundo, 0, 'normal10');
					fechaLinhaTabela();
				fechaTabela();
				echo "<br>";
			}
			
			if ($acao == 'Inativação')
				enviarEmailInativacaoServicoAdicional($matriz);
			elseif($acao == 'Cancelamento')
				enviarEmailCancelamentoServicoAdicional($matriz);
		}
	}

	return($matriz);
}


# função para adicionar pessoa
function adicionarServicoAdicional($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro;
	
	# Recebe ID do Plano
	$consulta=buscaServicosPlanos($registro, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		# Prosseguir e procurar detalhes sobre plano
		$idPlano=resultadoSQL($consulta, 0, 'idPlano');
		$idServico=resultadoSQL($consulta, 0, 'idServico');
		
		# Procurar por Pessoa
		$consultaPlanos=buscaPlanos($idPlano, 'id','igual','id');

		if($consultaPlanos && contaConsulta($consultaPlanos)>0) {
		
			$idPessoa=resultadoSQL($consultaPlanos, 0, 'idPessoaTipo');
			$matriz[idVencimento]=resultadoSQL($consultaPlanos, 0, 'idVencimento');
			
			# Ver dados da pessoa
			verPessoas('cadastros', 'clientes', 'ver', $idPessoa, $matriz);
			echo "<br>";			
			
			# Ver o Serviço
			verPlanos($modulo, $sub, 'abrir',$idPlano,$matriz);
			//verServicosPlanos($modulo, $sub, 'abrir', $registro, $matriz);
			echo "<br>";
			
			$data=dataSistema();

			# Informaçoes sobre vencimento
			$matriz[dtVencimento]=formatarData($matriz[dtVencimento]);
			$consultaVencimento=dadosVencimento($matriz[idVencimento]);
			$matriz[diaVencimento]=$consultaVencimento[diaVencimento];
			$matriz[diaFaturamento]=$consultaVencimento[diaFaturamento];
			
			$mesVencimento=substr($matriz[dtVencimento],0,2);
			$anoVencimento=substr($matriz[dtVencimento],2,4);
			
			if(!$matriz[bntConfirmar] || !$matriz[nome] || !$matriz[dtVencimento] || formatarValores($matriz[valor])==0
				|| verificarVencimento2($matriz[idVencimento], 0, $mesVencimento, $anoVencimento) ) {
				# Formulário para adição de Serviço
				$matriz[idPlano]=$idPlano;
				$matriz[idServico]=$idServico;
				$matriz[idServicoPlano]=$registro;
				$matriz[idPessoaTipo]=$idPessoa;
				
				if(!$matriz[dtEspecial] || $matriz[dtEspecialCobranca]) $matriz[dtEspecialCobranca]='N';
				
				formServicosAdicionais($modulo, $sub, $acao, $registro, $matriz);
			}
			else {
				# Gravar registro
				$data=dataSistema();
				$matriz[dtCadastro]=$data[dataBanco];
				
				$matriz[dtVencimento]=formatarData($matriz[dtVencimento]);
				$mes=substr($matriz[dtVencimento],0,2);
				$ano=substr($matriz[dtVencimento],2,4);
				
				$vencimento=dadosVencimento($matriz[idVencimento]);
				
				$dia=$vencimento[diaVencimento];
				if(strlen($dia)==1) $dia="0".$dia;
				
				$dataVencimento=$dia."/".$mes."/".$ano;
				
				# Enviar todas as parcelas para gravação
				$descricao=$matriz[nome];
				
				if($matriz[parcelas]>1) {
					$parcelas=calculaParcelas($matriz[valor], $matriz[parcelas], $matriz[idVencimento], $matriz[dtVencimento], $matriz[dtEspecial]);
				
					for($a=1;$a<=$matriz[parcelas];$a++) {
						if($matriz[parcelas]==1) $matriz[nome]="$descricao (Parcela Única/A Vista)";
						else $matriz[nome]="$descricao (Parcela $a/$matriz[parcelas])";
						
						$matriz[valor]=$parcelas[$a][valor];
						$matriz[dtVencimento]=converteData($parcelas[$a][data],'form','bancodata');
						
						$grava=dbServicoAdicional($matriz, 'incluir');
						
						if($grava) $confirmaGravacao=1;
						else {
							$confirmaGravacao=0;
							break;;
						}
					}
				}
				else {
					$matriz[valor]=formatarValores($matriz[valor]);
					$matriz[dtVencimento]=converteData($dataVencimento,'form','bancodata');
				
					$grava=dbServicoAdicional($matriz, 'incluir');
			
					if($grava) $confirmaGravacao=1;
					else $confirmaGravacao=0;
				}
				
				if($confirmaGravacao) {
					# OK
					$msg="Servico Adicional adicionado com sucesso!!!";
					$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
					avisoNOURL("Aviso", $msg, 600);
					
					listarServicosAdicionais($modulo, $sub, 'servicosadicionais', $registro, $matriz);
				}
				else {
					# Erro
					$msg="ERRO ao adicionar Serviço Adicional! Tente novamente!";
					$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
					avisoNOURL("Aviso", $msg, 600);
				}
				
			}
		}
		else {
			# Erro
			$msg="ERRO ao selecionar o Serviço do Plano!";
			$url="?modulo=cadastros&sub=clientes";
			aviso("Aviso", $msg, $url, 760);
		}
	}
	else {
		# Erro
		$msg="ERRO ao selecionar o Serviço do Plano!";
		$url="?modulo=cadastros&sub=clientes";
		aviso("Aviso", $msg, $url, 760);
	}

}


# formulário de dados cadastrais
function formServicosAdicionais($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	$data=dataSistema();

	# Pessoa física
	novaTabela2("[Serviço Adicional]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		# Opcoes Adicionais
		menuOpcAdicional($modulo, $sub, 'adicionarservicoadicional', $matriz[idServicoPlano]);
		novaLinhaTabela($corFundo, '100%');
			$texto="<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=registro value=$registro>
			<input type=hidden name=matriz[idServicoPlano] value=$matriz[idServicoPlano]>
			<input type=hidden name=matriz[idVencimento] value=$matriz[idVencimento]>
			<input type=hidden name=matriz[id] value=$matriz[id]>
			<input type=hidden name=matriz[dtEspecial] value=$matriz[dtEspecial]>
			<input type=hidden name=matriz[idPlano] value=$matriz[idPlano]>";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();

		$botoes="<input type=submit name=matriz[bntCalcular] value='Calcular Novamente' class=submit>";

		if($matriz[bntCalcular] && formatarValores($matriz[valor])==0) {
			# Mostrar aviso de valor zerado
			$texto="<span class=txtaviso>ATENÇÃO: Informar Valor do serviço Adicional!</span> ";
			itemTabelaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
		}
		if($matriz[bntCalcular] && strlen(trim($matriz[dtVencimento]))<6) {
			# Mostrar aviso de valor zerado
			$texto="<span class=txtaviso>ATENÇÃO: Informar Data de Vencimento!</span> ";
			itemTabelaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
		}
		else {
			
			# Verificar se Data de Vencimento é informada não esta dentro do faturamento já gerado
			$infoVencimento=dadosVencimento($matriz[idVencimento]);
			$data=dataSistema();
			
			$mesVencimento=substr($matriz[dtVencimento],0,2);
			$anoVencimento=substr($matriz[dtVencimento],2,4);
			
			# Verificar data de vencimento
			if($matriz[dtVencimento] && verificarVencimento2($matriz[idVencimento], $dia, $mesVencimento, $anoVencimento)) {
				$texto="<span class=txtaviso>ATENÇÃO: Faturamento do cliente já foi realizado no mês informado!</span>";
				itemTabelaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
				print $matriz[dtVencimento];////testes 09/02/09
			}
			
			else {
				$botoes="<input type=submit name=matriz[bntCalcular] value='Calcular Novamente' class=submit>";
				$botoes.=" <input type=submit name=matriz[bntConfirmar] value=Confirmar class=submit>";
			}
		}
		
		if(!$matriz[bntCalcular]) {
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Descrição/Identificação:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[nome] value='$matriz[nome]' size=60>";
				itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Tipo do Serviço Adicional:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto=formSelectTipoServicoAdicional($matriz[idTipoServicoAdicional],'idTipoServicoAdicional','form');
				itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Dia vencimento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL(formSelectVencimento($matriz[idVencimento], 'diaVencimento','check'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Mês/Ano para vencimento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[dtVencimento] value='$matriz[dtVencimento]' size=7 onBlur=verificaDataMesAno(this.value,11);><span class=txtaviso> (Formato: ".$data[mes]."/".$data[ano].")</span>";
				itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Valor do Serviço Adicional:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[valor] value='$matriz[valor]' size=10 onBlur=formataValor(this.value,12)>";
				itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Quantidade de Parcelas:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL(formSelectParcelas($matriz[parcelas],'parcelas'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Status:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL(formSelectStatusServicoAdicional($matriz[status],'status','form'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntCalcular] value=Calcular class=submit>";
				itemLinhaTMNOURL($texto, 'center', 'middle', '70%', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		}
		#### opção de parcelamento - mostrar parcelas
		elseif($matriz[bntCalcular]) {
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Descrição/Identificação:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[nome] value='$matriz[nome]' size=60>";
				itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Tipo do Serviço Adicional:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto=formSelectTipoServicoAdicional($matriz[idTipoServicoAdicional],'idTipoServicoAdicional','form');
				itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Dia vencimento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL(formSelectVencimento($matriz[idVencimento], 'diaVencimento','check'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Mês/Ano para vencimento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[dtVencimento] value='$matriz[dtVencimento]' size=7 onBlur=verificaDataMesAno(this.value,11);><span class=txtaviso> (Formato: ".$data[mes]."/".$data[ano].")</span>";
				itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Valor do Serviço Adicional:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[valor] value='$matriz[valor]' size=10 onBlur=formataValor(this.value,12)>";
				itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Quantidade de Parcelas:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL(formSelectParcelas($matriz[parcelas],'parcelas'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			# Mostrar Parcelas
			if($mesVencimento) {
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('100%', 'center', $corFundo, 2, 'tabfundo1');
						$valor_parcelas=calculaParcelas($matriz[valor], $matriz[parcelas], $matriz[idVencimento], $matriz[dtVencimento], $matriz[dtEspecial]);
						parcelasServicoAdicional($valor_parcelas);
					htmlFechaColuna();
				fechaLinhaTabela();
			}
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Status:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL(formSelectStatusServicoAdicional($matriz[status],'status','form'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				$texto=$botoes;
				# gravar parcelas na form
				for($a=1;$a<=$matriz[parcelas];$a++) {
					$texto.="\n<input type=hidden name=matriz[$a][data] value='".$valor_parcelas[$a][data]."'";
					$texto.="\n<input type=hidden name=matriz[$a][valor] value='".formatarValoresForm($valor_parcelas[$a][valor])."'";
				}
				itemLinhaTMNOURL($texto, 'center', 'middle', '70%', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		
		}
	fechaTabela();

}




# formulário de dados cadastrais
function formAlterarServicosAdicionais($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	$data=dataSistema();

	# Pessoa física
	novaTabela2("[Serviço Adicional]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		# Opcoes Adicionais
		menuOpcAdicional($modulo, $sub, 'adicionarservicoadicional', $matriz[idServicoPlano]);
		novaLinhaTabela($corFundo, '100%');
			$texto="<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=registro value=$registro>
			<input type=hidden name=matriz[idServicoPlano] value=$matriz[idServicoPlano]>
			<input type=hidden name=matriz[idVencimento] value=$matriz[idVencimento]>
			<input type=hidden name=matriz[id] value=$matriz[id]>
			<input type=hidden name=matriz[dtEspecial] value=$matriz[dtEspecial]>
			<input type=hidden name=matriz[idPlano] value=$matriz[idPlano]>";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();

		$botoes="<input type=submit name=matriz[bntCalcular] value='Calcular Novamente' class=submit>";

		if($matriz[bntCalcular] && formatarValores($matriz[valor])==0) {
			# Mostrar aviso de valor zerado
			$texto="<span class=txtaviso>ATENÇÃO: Informar Valor do serviço Adicional!</span> ";
			itemTabelaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
		}
		if($matriz[bntCalcular] && strlen(trim($matriz[dtVencimento]))<6) {
			# Mostrar aviso de valor zerado
			$texto="<span class=txtaviso>ATENÇÃO: Informar Data de Vencimento!</span> ";
			itemTabelaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
		}
		else {
			# Verificar se Data de Vencimento é informada não esta dentro do faturamento já gerado
			$infoVencimento=dadosVencimento($matriz[idVencimento]);
			$data=dataSistema();
			
			$mesVencimento=substr($matriz[dtVencimento],0,2);
			$anoVencimento=substr($matriz[dtVencimento],2,4);
			
			# Verificar data de vencimento
			if($matriz[dtVencimento] && verificarVencimento($matriz[idVencimento], $dia, $mesVencimento, $anoVencimento)) {
				$texto="<span class=txtaviso>ATENÇÃO: Faturamento do cliente já foi realizado no mês informado!</span>";
				itemTabelaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
			}
			else {
				$botoes="<input type=submit name=matriz[bntCalcular] value='Calcular Novamente' class=submit>";
				$botoes.=" <input type=submit name=matriz[bntConfirmar] value=Confirmar class=submit>";
			}

		}
		
		
		if(!$matriz[bntCalcular]) {
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Descrição/Identificação:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[nome] value='$matriz[nome]' size=60>";
				itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Dia vencimento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL(formSelectVencimento($matriz[idVencimento], 'diaVencimento','check'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Mês/Ano para vencimento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[dtVencimento] value='$matriz[dtVencimento]' size=7 onBlur=verificaDataMesAno(this.value,10);><span class=txtaviso> (Formato: ".$data[mes]."/".$data[ano].")</span>";
				itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Valor do Serviço Adicional:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[valor] value='$matriz[valor]' size=10 onBlur=formataValor(this.value,11)>";
				itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Quantidade de Parcelas:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL(formSelectParcelas($matriz[parcelas],'parcelas'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Status:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL(formSelectStatusServicoAdicional($matriz[status],'status','form'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntCalcular] value=Calcular class=submit>";
				itemLinhaTMNOURL($texto, 'center', 'middle', '70%', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		}
		#### opção de parcelamento - mostrar parcelas
		elseif($matriz[bntCalcular]) {
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Descrição/Identificação:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[nome] value='$matriz[nome]' size=60>";
				itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Dia vencimento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL(formSelectVencimento($matriz[idVencimento], 'diaVencimento','check'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Mês/Ano para vencimento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[dtVencimento] value='$matriz[dtVencimento]' size=7 onBlur=verificaDataMesAno(this.value,10);><span class=txtaviso> (Formato: ".$data[mes]."/".$data[ano].")</span>";
				itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Valor do Serviço Adicional:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[valor] value='$matriz[valor]' size=10 onBlur=formataValor(this.value,11)>";
				itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Status:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL(formSelectStatusServicoAdicional($matriz[status],'status','form'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				$texto=$botoes;
				# gravar parcelas na form
				for($a=1;$a<=$matriz[parcelas];$a++) {
					$texto.="\n<input type=hidden name=matriz[$a][data] value='".$valor_parcelas[$a][data]."'";
					$texto.="\n<input type=hidden name=matriz[$a][valor] value='".formatarValoresForm($valor_parcelas[$a][valor])."'";
				}
				itemLinhaTMNOURL($texto, 'center', 'middle', '70%', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		
		}
	fechaTabela();

}




# função para adicionar pessoa
function alterarServicosAdicionais($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro;

	# Buscar desconto
	$consulta=buscaServicosAdicionais($registro, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		# Prosseguir e procurar detalhes sobre plano
		$id=resultadoSQL($consulta, 0, 'id');
		$idPlano=resultadoSQL($consulta, 0, 'idPlano');
		$idServicoPlano=resultadoSQL($consulta, 0, 'idServicoPlano');
		$nome=resultadoSQL($consulta, 0, 'nome');
		$valor=formatarValores(resultadoSQL($consulta, 0, 'valor'));
		$dtVencimento=resultadoSQL($consulta, 0, 'dtVencimento');
		$dtEspecial=resultadoSQL($consulta, 0, 'dtEspecial');

		$mesVencimento=substr($matriz[dtVencimento],0,2);
		$anoVencimento=substr($matriz[dtVencimento],2,4);
			
		if(!$matriz[bntConfirmar] || !$matriz[nome] || !$matriz[dtVencimento] || formatarValores($matriz[valor])==0
				|| verificarVencimento($matriz[idVencimento], 0, $mesVencimento, $anoVencimento) ) {	
				
			# Ver o Serviço
			verServicosPlanos($modulo, $sub, 'abrir', $idServicoPlano, $matriz);
			echo "<br>";
		
			
			# Selecionar informações do plano
			$consultaPlano=buscaPlanos($idPlano,'id','igual','id');
			
			if($consultaPlano && contaConsulta($consultaPlano)>0) {
				$matriz[idPessoaTipo]=resultadoSQL($consultaPlano, 0, 'idPessoaTipo');
				$matriz[idVencimento]=resultadoSQL($consultaPlano, 0, 'idVencimento');
				
				# Prosseguir e procurar detalhes sobre plano
				$matriz[id]=resultadoSQL($consulta, 0, 'id');
				$matriz[idPlano]=resultadoSQL($consulta, 0, 'idPlano');
				$matriz[idServicoPlano]=resultadoSQL($consulta, 0, 'idServicoPlano');

				# Converter para formato MM/AAAA
				if(!$matriz[bntCalcular] && !$matriz[bntConfirmar]) {
					$matriz[dtVencimento]=resultadoSQL($consulta, 0, 'dtVencimento');
					$matriz[dtVencimento]=formatarData(converteData($matriz[dtVencimento],'banco','formdata'));
					$matriz[valor]=formatarValoresForm(resultadoSQL($consulta, 0, 'valor'));
					$matriz[nome]=resultadoSQL($consulta, 0, 'nome');
					
					$mesVencimento=substr($matriz[dtVencimento],2,2);
					$anoVencimento=substr($matriz[dtVencimento],4,4);
					
					$matriz[dtVencimento]=$mesVencimento.$anoVencimento;
				}
				else {
					$matriz[dtVencimento]=formatarData($matriz[dtVencimento]);
					$mesVencimento=substr($matriz[dtVencimento],0,2);
					$anoVencimento=substr($matriz[dtVencimento],2,4);
					$matriz[dtVencimento]=$mesVencimento.$anoVencimento;
				}

				formAlterarServicosAdicionais($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		else {
		
			# Ver o Serviço
			verServicosPlanos($modulo, $sub, 'abrir', $idServicoPlano, $matriz);
			echo "<br>";
		
			# Gravar registro
			$data=dataSistema();
			$matriz[dtCadastro]=$data[dataBanco];
			
			$matriz[dtVencimento]=formatarData($matriz[dtVencimento]);
			$mes=substr($matriz[dtVencimento],0,2);
			$ano=substr($matriz[dtVencimento],2,4);
			
			$vencimento=dadosVencimento($matriz[idVencimento]);
			$matriz[dtVencimento]="$ano-$mes-$vencimento[diaVencimento]";

			$matriz[valor]=formatarValores($matriz[valor]);	
			
			$grava=dbServicoAdicional($matriz, 'alterar');
			
			if($grava) {
				# OK
				$msg="Serviço Adicional alterado com sucesso!!!";
				$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
				avisoNOURL("Aviso", $msg, 600);
				
				$sessCadastro[bntSelecionar]='';
				listarServicosAdicionais($modulo, $sub, 'servicosadicionais', $matriz[idServicoPlano], $matriz);
			}
			else {
				# Erro
				$msg="ERRO ao alterar serviço adicional! Tente novamente!";
				$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
				avisoNOURL("Aviso", $msg, 600);
			}
		}
	}
}


# função para adicionar pessoa
function desativarServicosAdicionais($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro;

	# Buscar desconto
	$consulta=buscaServicosAdicionais($registro, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		# Prosseguir e procurar detalhes sobre plano
		$id=resultadoSQL($consulta, 0, 'id');
		$idPlano=resultadoSQL($consulta, 0, 'idPlano');
		$idServicoPlano=resultadoSQL($consulta, 0, 'idServicoPlano');
		$nome=resultadoSQL($consulta, 0, 'nome');
		$valor=formatarValores(resultadoSQL($consulta, 0, 'valor'));
		$dtVencimento=resultadoSQL($consulta, 0, 'dtVencimento');

		if(!$matriz[bntDesativar] ) {
	
			# Ver o Serviço
			verServicosPlanos($modulo, $sub, 'abrir', $idServicoPlano, $matriz);
			echo "<br>";
		
			
			# Selecionar informações do plano
			$consultaPlano=buscaPlanos($idPlano,'id','igual','id');
			
			if($consultaPlano && contaConsulta($consultaPlano)>0) {
				$matriz[idPessoaTipo]=resultadoSQL($consultaPlano, 0, 'idPessoaTipo');
				$matriz[idVencimento]=resultadoSQL($consultaPlano, 0, 'idVencimento');
				
				# Prosseguir e procurar detalhes sobre plano
				$matriz[id]=resultadoSQL($consulta, 0, 'id');
				$matriz[idPlano]=resultadoSQL($consulta, 0, 'idPlano');
				$matriz[idServicoPlano]=resultadoSQL($consulta, 0, 'idServicoPlano');
				$matriz[nome]=resultadoSQL($consulta, 0, 'nome');
				$matriz[valor]=formatarValoresForm(resultadoSQL($consulta, 0, 'valor'));
				$matriz[dtVencimento]=resultadoSQL($consulta, 0, 'dtVencimento');

				# Converter data de desconto
				if($matriz[dtVencimento]) {
					# Converter para formato MM/AAAA
					$matriz[dtVencimento]=formatarData(converteData($matriz[dtVencimento],'banco','formdata'));
					
					$mesVencimento=substr($matriz[dtVencimento],2,2);
					$anoVencimento=substr($matriz[dtVencimento],4,4);
					
					$matriz[dtVencimento]=$mesVencimento."/".$anoVencimento;
				}

				formDesativarServicosAdicionais($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		else {
		
			# Ver o Serviço
			verServicosPlanos($modulo, $sub, 'abrir', $idServicoPlano, $matriz);
			echo "<br>";
		
			# Gravar registro
			$data=dataSistema();
			$matriz[dtCancelamento]=$data[dataBanco];
			
			$grava=dbServicoAdicional($matriz, 'desativar');
			
			
			if($grava) {
				# OK
				$msg="Servico Adicional desativado com sucesso!!!";
				$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
				avisoNOURL("Aviso", $msg, 600);
				
				listarServicosAdicionais($modulo, $sub, 'servicosadicionais', $matriz[idServicoPlano], $matriz);
			}
			else {
				# Erro
				$msg="ERRO ao desativar serviço adicional! Tente novamente!";
				$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
				avisoNOURL("Aviso", $msg, 600);
			}
		}
	}
}



# formulário de dados cadastrais
function formDesativarServicosAdicionais($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	$data=dataSistema();

	# Pessoa física
	novaTabela2("[Visualização de Servicos Adicionais]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		# Opcoes Adicionais
		menuOpcAdicional($modulo, $sub, $acao, $matriz[idServicoPlano]);
		novaLinhaTabela($corFundo, '100%');
			$texto="<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=registro value=$registro>
			<input type=hidden name=matriz[idServicoPlano] value=$matriz[idServicoPlano]>
			<input type=hidden name=matriz[id] value=$matriz[id]>
			<input type=hidden name=matriz[idPlano] value=$matriz[idPlano]>";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		fechaLinhaTabela();
		
		# Buscar informações sobre o serviço 
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Descrição/Identificação:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL($matriz[nome], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Dia vencimento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL(formSelectVencimento($matriz[idVencimento], '','check'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Mês/Ano para desconto:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL($matriz[dtVencimento], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Valor do Desconto:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL($matriz[valor], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			$texto="<input type=submit name=matriz[bntDesativar] value=Desativar class=submit>";
			itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();				

	fechaTabela();

}



# função para adicionar pessoa
function ativarServicosAdicionais($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro;

	# Buscar desconto
	$consulta=buscaServicosAdicionais($registro, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		# Prosseguir e procurar detalhes sobre plano
		$id=resultadoSQL($consulta, 0, 'id');
		$idPlano=resultadoSQL($consulta, 0, 'idPlano');
		$idServicoPlano=resultadoSQL($consulta, 0, 'idServicoPlano');
		$nome=resultadoSQL($consulta, 0, 'nome');
		$valor=formatarValores(resultadoSQL($consulta, 0, 'valor'));
		$dtVencimento=resultadoSQL($consulta, 0, 'dtVencimento');

		if(!$matriz[bntAtivar] ) {
	
			# Ver o Serviço
			verServicosPlanos($modulo, $sub, 'abrir', $idServicoPlano, $matriz);
			echo "<br>";
		
			
			# Selecionar informações do plano
			$consultaPlano=buscaPlanos($idPlano,'id','igual','id');
			
			if($consultaPlano && contaConsulta($consultaPlano)>0) {
				$matriz[idPessoaTipo]=resultadoSQL($consultaPlano, 0, 'idPessoaTipo');
				$matriz[idVencimento]=resultadoSQL($consultaPlano, 0, 'idVencimento');
				
				# Prosseguir e procurar detalhes sobre plano
				$matriz[id]=resultadoSQL($consulta, 0, 'id');
				$matriz[idPlano]=resultadoSQL($consulta, 0, 'idPlano');
				$matriz[idServicoPlano]=resultadoSQL($consulta, 0, 'idServicoPlano');
				$matriz[nome]=resultadoSQL($consulta, 0, 'nome');
				$matriz[valor]=formatarValoresForm(resultadoSQL($consulta, 0, 'valor'));
				$matriz[dtVencimento]=resultadoSQL($consulta, 0, 'dtVencimento');

				# Converter data de desconto
				if($matriz[dtVencimento]) {
					# Converter para formato MM/AAAA
					$matriz[dtVencimento]=formatarData(converteData($matriz[dtVencimento],'banco','formdata'));
					
					$mesVencimento=substr($matriz[dtVencimento],2,2);
					$anoVencimento=substr($matriz[dtVencimento],4,4);
					
					$matriz[dtVencimento]=$mesVencimento."/".$anoVencimento;
				}

				formAtivarServicosAdicionais($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		else {
		
			# Ver o Serviço
			verServicosPlanos($modulo, $sub, 'abrir', $idServicoPlano, $matriz);
			echo "<br>";
		
			# Gravar registro
			$data=dataSistema();
			
			$grava=dbServicoAdicional($matriz, 'ativar');
			
			if($grava) {
				# OK
				$msg="Servico Adicional ativado com sucesso!!!";
				$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
				avisoNOURL("Aviso", $msg, 600);
				
				listarServicosAdicionais($modulo, $sub, 'servicosadicionais', $matriz[idServicoPlano], $matriz);
			}
			else {
				# Erro
				$msg="ERRO ao ativar serviço adicional! Tente novamente!";
				$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
				avisoNOURL("Aviso", $msg, 600);
			}
		}
	}
}



# formulário de dados cadastrais
function formAtivarServicosAdicionais($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	$data=dataSistema();

	# Pessoa física
	novaTabela2("[Visualização de Serviço Adicional]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		# Opcoes Adicionais
		menuOpcAdicional($modulo, $sub, $acao, $matriz[idServicoPlano]);
		novaLinhaTabela($corFundo, '100%');
			$texto="<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=registro value=$registro>
			<input type=hidden name=matriz[idServicoPlano] value=$matriz[idServicoPlano]>
			<input type=hidden name=matriz[id] value=$matriz[id]>
			<input type=hidden name=matriz[idPlano] value=$matriz[idPlano]>";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		fechaLinhaTabela();
		
		# Buscar informações sobre o serviço 
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Descrição/Identificação:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL($matriz[nome], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Dia desconto:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL(formSelectVencimento($matriz[idVencimento], '','check'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Mês/Ano para desconto:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL($matriz[dtVencimento], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Valor do Desconto:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL($matriz[valor], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			$texto="<input type=submit name=matriz[bntAtivar] value=Ativar class=submit>";
			itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();				

	fechaTabela();

}



# função para adicionar pessoa
function verServicosAdicionais($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro;

	# Buscar desconto
	$consulta=buscaServicosAdicionais($registro, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		# Prosseguir e procurar detalhes sobre plano
		$id=resultadoSQL($consulta, 0, 'id');
		$idPlano=resultadoSQL($consulta, 0, 'idPlano');
		$idServicoPlano=resultadoSQL($consulta, 0, 'idServicoPlano');
		$idTipoServicoAdicional=resultadoSQL($consulta, 0, 'idTipoServicoAdicional');
		$nome=resultadoSQL($consulta, 0, 'nome');
		$valor=formatarValores(resultadoSQL($consulta, 0, 'valor'));
		$dtVencimento=resultadoSQL($consulta, 0, 'dtVencimento');

		# Ver o Serviço
		verServicosPlanos($modulo, $sub, 'abrir', $idServicoPlano, $matriz);
		echo "<br>";
	
		
		# Selecionar informações do plano
		$consultaPlano=buscaPlanos($idPlano,'id','igual','id');
		
		if($consultaPlano && contaConsulta($consultaPlano)>0) {
			$matriz[idPessoaTipo]=resultadoSQL($consultaPlano, 0, 'idPessoaTipo');
			$matriz[idVencimento]=resultadoSQL($consultaPlano, 0, 'idVencimento');
			
			# Prosseguir e procurar detalhes sobre plano
			$matriz[id]=resultadoSQL($consulta, 0, 'id');
			$matriz[idPlano]=resultadoSQL($consulta, 0, 'idPlano');
			$matriz[idServicoPlano]=resultadoSQL($consulta, 0, 'idServicoPlano');
			$matriz[idTipoServicoAdicional]=resultadoSQL($consulta, 0, 'idTipoServicoAdicional');
			$matriz[nome]=resultadoSQL($consulta, 0, 'nome');
			$matriz[valor]=formatarValoresForm(resultadoSQL($consulta, 0, 'valor'));
			$matriz[dtVencimento]=resultadoSQL($consulta, 0, 'dtVencimento');
			$matriz[dtCancelamento]=converteData(resultadoSQL($consulta, 0, 'dtCancelamento'),'banco','formdata');
			$matriz[dtCadastro]=converteData(resultadoSQL($consulta, 0, 'dtCadastro'),'banco','form');
			$matriz[dtCobranca]=converteData(resultadoSQL($consulta, 0, 'dtCobranca'),'banco','formdata');

			# Converter data de desconto
			if($matriz[dtVencimento]) {
				# Converter para formato MM/AAAA
				$matriz[dtVencimento]=formatarData(converteData($matriz[dtVencimento],'banco','formdata'));
				
				$mes=substr($matriz[dtVencimento],2,2);
				$ano=substr($matriz[dtVencimento],4,4);
				
				$matriz[dtVencimento]=$mes."/".$ano;
			}

			formVerServicosAdicionais($modulo, $sub, $acao, $registro, $matriz);
		}
	}
}


# formulário de dados cadastrais
function formVerServicosAdicionais($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	$data=dataSistema();

	# Pessoa física
	novaTabela2("[Visualização de Serviço Adicional]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		# Opcoes Adicionais
		menuOpcAdicional($modulo, $sub, $acao, $matriz[idServicoPlano]);
		novaLinhaTabela($corFundo, '100%');
			$texto="<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=registro value=$registro>
			<input type=hidden name=matriz[idServicoPlano] value=$matriz[idServicoPlano]>
			<input type=hidden name=matriz[id] value=$matriz[id]>
			<input type=hidden name=matriz[idPlano] value=$matriz[idPlano]>";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		fechaLinhaTabela();
		
		# Buscar informações sobre o serviço 
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Descrição/Identificação:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL($matriz[nome], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Tipo do Serviço Adicional:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto=formSelectTipoServicoAdicional($matriz[idTipoServicoAdicional],'','check');
				itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Dia vencimento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL(formSelectVencimento($matriz[idVencimento], '','check'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Mês/Ano de vencimento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL($matriz[dtVencimento], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Valor do Serviço Adicional:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL($matriz[valor], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Data Cadastro:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL($matriz[dtCadastro], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		if($matriz[dtCancelamento]) {
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Data Cancelamento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL($matriz[dtCancelamento], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		}
		if(formatarData($matriz[dtCobranca])>0) {
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Data Cobranca:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL($matriz[dtCobranca], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		}

	fechaTabela();

}


# função para adicionar pessoa
function cancelarServicosAdicionais($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro;

	# Buscar desconto
	$consulta=buscaServicosAdicionais($registro, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		# Prosseguir e procurar detalhes sobre plano
		$id=resultadoSQL($consulta, 0, 'id');
		$idPlano=resultadoSQL($consulta, 0, 'idPlano');
		$idServicoPlano=resultadoSQL($consulta, 0, 'idServicoPlano');
		$nome=resultadoSQL($consulta, 0, 'nome');
		$valor=formatarValores(resultadoSQL($consulta, 0, 'valor'));
		$dtVencimento=resultadoSQL($consulta, 0, 'dtVencimento');

		if(!$matriz[bntCancelar] ) {
	
			# Ver o Serviço
			verServicosPlanos($modulo, $sub, 'abrir', $idServicoPlano, $matriz);
			echo "<br>";
		
			
			# Selecionar informações do plano
			$consultaPlano=buscaPlanos($idPlano,'id','igual','id');
			
			if($consultaPlano && contaConsulta($consultaPlano)>0) {
				$matriz[idPessoaTipo]=resultadoSQL($consultaPlano, 0, 'idPessoaTipo');
				$matriz[idVencimento]=resultadoSQL($consultaPlano, 0, 'idVencimento');
				
				# Prosseguir e procurar detalhes sobre plano
				$matriz[id]=resultadoSQL($consulta, 0, 'id');
				$matriz[idPlano]=resultadoSQL($consulta, 0, 'idPlano');
				$matriz[idServicoPlano]=resultadoSQL($consulta, 0, 'idServicoPlano');
				$matriz[nome]=resultadoSQL($consulta, 0, 'nome');
				$matriz[valor]=formatarValoresForm(resultadoSQL($consulta, 0, 'valor'));
				$matriz[dtVencimento]=resultadoSQL($consulta, 0, 'dtVencimento');

				# Converter data de desconto
				if($matriz[dtVencimento]) {
					# Converter para formato MM/AAAA
					$matriz[dtVencimento]=formatarData(converteData($matriz[dtVencimento],'banco','formdata'));
					
					$mes=substr($matriz[dtVencimento],2,2);
					$ano=substr($matriz[dtVencimento],4,4);
					
					$matriz[dtVencimento]=$mes."/".$ano;
				}

				formCancelarServicosAdicionais($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		else {
		
			# Ver o Serviço
			verServicosPlanos($modulo, $sub, 'abrir', $idServicoPlano, $matriz);
			echo "<br>";
		
			# Gravar registro
			$data=dataSistema();
			$matriz[dtCancelamento]=$data[dataBanco];
			
			$grava=dbServicoAdicional($matriz, 'cancelar');
			
			if($grava) {
				# OK
				$msg="Serviço Adicional cancelado com sucesso!!!";
				$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
				avisoNOURL("Aviso", $msg, 600);
				
				listarServicosAdicionais($modulo, $sub, 'servicosadicionais', $matriz[idServicoPlano], $matriz);
			}
			else {
				# Erro
				$msg="ERRO ao cancelar serviço adicional! Tente novamente!";
				$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
				avisoNOURL("Aviso", $msg, 600);
			}
		}
	}
}



# formulário de dados cadastrais
function formCancelarServicosAdicionais($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	$data=dataSistema();

	# Pessoa física
	novaTabela2("[Cancelamento de Serviço Adicional]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		# Opcoes Adicionais
		menuOpcAdicional($modulo, $sub, $acao, $matriz[idServicoPlano]);
		novaLinhaTabela($corFundo, '100%');
			$texto="<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=registro value=$registro>
			<input type=hidden name=matriz[idServicoPlano] value=$matriz[idServicoPlano]>
			<input type=hidden name=matriz[id] value=$matriz[id]>
			<input type=hidden name=matriz[idPlano] value=$matriz[idPlano]>";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		fechaLinhaTabela();
		
		# Buscar informações sobre o serviço 
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Descrição/Identificação:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL($matriz[nome], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Dia desconto:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL(formSelectVencimento($matriz[idVencimento], '','check'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Mês/Ano para desconto:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL($matriz[dtVencimento], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Valor do Desconto:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL($matriz[valor], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			$texto="<input type=submit name=matriz[bntCancelar] value=Cancelar class=submit>";
			itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();				

	fechaTabela();

}




# Função para mostrar as parcelas do serviço adicional
function parcelasServicoAdicional($parcelas) {

	global $corFundo, $corBorda;

	# montar tabela
	novaTabela2("[Parcelas]", "center", '400', 0, 2, 1, $corFundo, $corBorda, 3);
		# Opcoes Adicionais
		menuOpcAdicional($modulo, $sub, $acao, $matriz[idServicoPlano]);
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('Vencimento', 'center', 'middle', '20%', $corFundo, 0, 'tabfundo0');
			itemLinhaTMNOURL('Parcela', 'center', 'middle', '40%', $corFundo, 0, 'tabfundo0');
			itemLinhaTMNOURL('Valor', 'center', 'middle', '40%', $corFundo, 0, 'tabfundo0');
		fechaLinhaTabela();
		
		for($a=1;$a<=count($parcelas);$a++) {
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL($parcelas[$a][data], 'center', 'middle', '40%', $corFundo, 0, 'bold10');
				if(count($parcelas)==1) itemLinhaTMNOURL('Parcela Única', 'center', 'middle', '40%', $corFundo, 0, 'bold10');
				else itemLinhaTMNOURL("Parcela $a/".count($parcelas), 'center', 'middle', '40%', $corFundo, 0, 'bold10');
				itemLinhaTMNOURL(formatarValoresForm($parcelas[$a][valor]), 'center', 'middle', '20%', $corFundo, 0, 'txtok');
			fechaLinhaTabela();
		}
	fechaTabela();
}



# Atualizar data de desconto
function atualizarDataServicoAdicional($idPlano, $dtInicio, $idVencimento) {

	global $conn, $tb;
	
	$vencimento=dadosVencimento($idVencimento);
	$data=dataSistema();
	
	# Selecionar descontos para o serviço informado
	$consulta=buscaServicosAdicionais($idPlano, 'idPlano','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
	
		for($i=0;$i<contaConsulta($consulta);$i++) {
			$idServicoAdicional=resultadoSQL($consulta, $i, 'id');
			$dtServicoAdicional=resultadoSQL($consulta, $i, 'dtVencimento');
			
			if( strtotime( $dtServicoAdicional ) > time() ){
			
				# Quebrar data
				$tmpData=formatarData($dtServicoAdicional);
				$dia=substr($tmpData, 6,2);
				$mes=substr($tmpData, 4,2);
				$ano=substr($tmpData, 0,4);
				
				
				//# faco a comparacao nao com o dia atual e sim com o novo dia de vencimento
				//# se a nova data(dia) de vencimento for menor que a anterior, é preciso colocá-lo para o proximo 
				//# faturamento, porem somente se a fatura ainda nao foi gerada que é vista verificiando se o dia 
				//# atual é maior que a tada de faturamento   
				
				if ($vencimento[diaVencimento] < $dia ){
					if ( $mes > $data[mes] ||  ($mes == $data[mes] && $dia > $data[dia]))
						if ($mes<12)
							$mes++;
						else{
							$ano++;
							$mes=1;
						}
				}
				
				$novaData="$ano-$mes-$vencimento[diaVencimento]";
				
				$sql="
					UPDATE $tb[ServicosAdicionais] SET
						dtVencimento='$novaData'
					WHERE
					id=$idServicoAdicional";
				
				$grava=consultaSQL($sql, $conn);
				
			};
		}
	}
	return(0);
}


# função para adicionar pessoa
function formVerDadosServicoAdicional($matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro;
	
	$pessoaTipo=dadosPessoasTipos($matriz[idPessoaTipo]);
	$pessoa=$pessoaTipo[pessoa];

	# Buscar informações sobre o serviço 
	novaLinhaTabela($corFundo, '100%');
		$texto="<a href=?modulo=lancamentos&sub=planos&acao=servicosadicionais&registro=$matriz[idServicoPlano]>$pessoa[nome]</a>";
		itemLinhaTMNOURL($texto, 'left', 'middle', '55%', $corFundo, 0, 'normal10');
		itemLinhaTMNOURL(converteData($matriz[dtVencimento],'banco','formdata'), 'center', 'middle', '10%', $corFundo, 0, 'normal10');
		itemLinhaTMNOURL(formatarValoresForm($matriz[valor]), 'right', 'middle', '15%', $corFundo, 0, 'normal10');
		itemLinhaTMNOURL(formSelectTipoServicoAdicional($matriz[idTipoServicoAdicional],'','check'), 'left', 'middle', '20%', $corFundo, 0, 'normal10');
	fechaLinhaTabela();
}

/**
 * funcao de envio de email ao responsavel financeiro afim de avisar a ocorrencia do servico adicional
 * ao inativar o serivco.
 */
function enviarEmailInativacaoServicoAdicional ($matriz) {
	$parametros = carregaParametrosConfig();
	
	$para = $parametros['email_financeiro'];
	$assunto = $parametros['msg_inativ_assunto'];
	$mensagem = $parametros['msg_inativacao'];
	unset($parametros);
	$de = "noreply@devel.it";
	
	$mensagem = str_replace('*valor*', formatarValoresForm($matriz[valor]), $mensagem);
	$mensagem = str_replace('*dtVencimento*', converteData($matriz[dtVencimento],'banco','formdata'), $mensagem);
	
	if (substr_count($mensagem, '*servico*') ){
		$servico = checkServico($matriz['idServico']);
		$mensagem = str_replace('*servico*', $servico['nome'], $mensagem);
	}
	
	if (substr_count($mensagem, '*pessoa*')){
		$pessoa = dadosPessoasTipos($matriz['idPessoaTipo']);
		$mensagem = str_replace('*pessoa*', $pessoa['pessoa']['nome'], $mensagem);
	}
	
	enviarEmail($de, $para, $assunto, $mensagem);

}


/**
 * funcao de envio de email ao responsavel financeiro afim de avisar a ocorrencia do servico adicional
 * ao inativar o serivco.
 */
function enviarEmailAtivacaoServicoAdicional ($matriz) {
	$parametros = carregaParametrosConfig();
	
	$para = $parametros['email_financeiro'];
	$assunto = $parametros['msg_ativ_assunto'];
	$mensagem = $parametros['msg_ativacao'];
	unset($parametros);
	$de = "noreply@devel.it";
	
	$mensagem = str_replace('*valor*', formatarValoresForm($matriz[valor]), $mensagem);
	$mensagem = str_replace('*dtVencimento*', converteData($matriz[dtVencimento],'banco','formdata'), $mensagem);
	
	if (substr_count($mensagem, '*servico*') ){
		$servico = checkServico($matriz['idServico']);
		$mensagem = str_replace('*servico*', $servico['nome'], $mensagem);
	}
	
	if (substr_count($mensagem, '*pessoa*')){
		$pessoa = dadosPessoasTipos($matriz['idPessoaTipo']);
		$mensagem = str_replace('*pessoa*', $pessoa['pessoa']['nome'], $mensagem);
	}
	
	enviarEmail($de, $para, $assunto, $mensagem);

}


/**
 * funcao de envio de email ao responsavel financeiro afim de avisar a ocorrencia do servico adicional
 * ao inativar o serivco.
 */
function enviarEmailCancelamentoServicoAdicional ($matriz) {
	$parametros = carregaParametrosConfig();
	
	$para = $parametros['email_financeiro'];
	$assunto = $parametros['msg_cancel_assunto'];
	$mensagem = $parametros['msg_cancelamento'];
	unset($parametros);
	$de = "noreply@devel.it";
	
	$mensagem = str_replace('*valor*', formatarValoresForm($matriz[valor]), $mensagem);
	$mensagem = str_replace('*dtVencimento*', converteData($matriz[dtVencimento],'banco','formdata'), $mensagem);
	
	if (substr_count($mensagem, '*servico*') ){
		$servico = checkServico($matriz['idServico']);
		$mensagem = str_replace('*servico*', $servico['nome'], $mensagem);
	}
	
	if (substr_count($mensagem, '*pessoa*')){
		$pessoa = dadosPessoasTipos($matriz['idPessoaTipo']);
		$mensagem = str_replace('*pessoa*', $pessoa['pessoa']['nome'], $mensagem);
	}
	
	enviarEmail($de, $para, $assunto, $mensagem);

}
/**
 * Adicionar um servico adicional 
 * 
 * */
function adicionarServicoAdicionalServicoPlanoNovo($matriz, $ver) {
	$ret = false;
	$data = dataSistema();
	
	$parametros = carregaParametrosConfig();
	if ($parametros['diasFaturamento'] > 0 )	$diasFaturamento = $parametros['diasFaturamento'];
	else $diasFaturamento = 15;
	unset ( $parametros );
	
	$dtAtivacao = converteData($matriz['dtAtivacao'], 'form', 'timestamp');
	$dtInativacao = converteData($matriz['dtInativacao'], 'banco', 'timestamp');
	$dtVencimento = mktime(0,0,0,date('m', $dtAtivacao), $matriz['vencimento']['diaVencimento'], date('Y', $dtAtivacao));
	
	if ($dtVencimento < $dtAtivacao)
		$dtVencimento = strtotime (' + 1 month', $dtVencimento);
	
	$dtFaturamento = strtotime( "-".$diasFaturamento." days",$dtVencimento);
	//nao deve considerar meses com 31 ou 28 dias
	if (date('t', $dtFaturamento) != 30 ){
		$dtFaturamento = strtotime(30 - date('t',$dtFaturamento ).' day', $dtFaturamento);
	}
	
//		
//		echo "<br> dtFaturamento:".date ('d-m-Y', $dtFaturamento). 
//		"<br> dtInativacao:".date ('d-m-Y', $dtInativacao).
//		"<br> dtAtivacao:".date ('d-m-Y', $dtAtivacao).
//		"<br> dtVencimento:".date ('d-m-Y', $dtVencimento);
	
	if ($dtAtivacao > $dtFaturamento && $dtFaturamento > $dtInativacao){
		
		if ($matriz['tipoCobranca']['proporcional'] == 'S'){
			$periodo = ceil(($dtVencimento - $dtAtivacao) / 86400) ;
	 
			if( date('d',$dtFaturamento ) > date('d', $dtVencimento) )
				$qtdeDiasMes = date('t', $dtFaturamento);
			else
				$qtdeDiasMes = date('t', strtotime(' -1 month ', $dtFaturamento) );
			$valor = $matriz['valorServico']/$qtdeDiasMes * $periodo;
			$periodoTipo = "dias";
		}
		elseif($matriz['tipoCobranca']['proporcional'] == 'N'){
			if ($matriz['dtInativacao']){
				$dtVencimentoInativacao = mktime(0,0,0,date('m', $dtInativacao), $matriz['vencimento']['diaVencimento'], date('Y', $dtInativacao));
				if ($dtVencimentoInativacao < $dtInativacao)
					$dtVencimentoInativacao = strtotime (' + 1 month', $dtVencimentoInativacao);
				$dtFaturamentoInativacao = strtotime( "-".$diasFaturamento." days",$dtVencimentoInativacao);
				
				$dtInicio = ( $dtInativacao > $dtFaturamentoInativacao ? $dtVencimentoInativacao : strtotime(' - 1 month ', $dtVencimentoInativacao));
			}
			else $dtInicio = strtotime(' -1 month ', $dtVencimento);
			
			$dtFinal = ( $dtAtivacao > $dtFaturamento ? $dtVencimento : strtotime(' - 1 month ', $dtVencimento));
			
			$periodo = ceil(($dtFinal - $dtInicio) / (60 * 60 * 24 * 31) );
			$valor =  $matriz['valorServico'] * $periodo ;
			$periodoTipo = "mes(es)";
			
//			echo "<br>dtInicial".date('d-m-Y', $dtInicio).
//			"<br>dtFinal".date('d-m-Y', $dtFinal).
//			"<br>periodo".$periodo.
//			"<br>valor".$valor;
			
		}
		
		#mes de tal calculo.
		$ret = date ('d-m-Y', $dtVencimento);

		#como ja foi o faturamento, depois de tudo calculado com base neste ja realizado, lancar somente para o prox.
		$dtVencimento = strtotime (' + 1 month', $dtVencimento);
		
		$sa[nome]="Valor por $periodo $periodoTipo pela ativacao apos o faturamento"; 
		$sa[valor]=$valor;
		$sa[dtVencimento]=date('Y-m-d', $dtVencimento);
		$sa[dtCadastro]=$data[dataBanco];
		$sa[status]='A';
		$sa[idServicoPlano]=$matriz['idServicoPlano'];
		$sa['idServico']=$matriz['idServico'];  #20050913
		$sa[idTipoServicoAdicional]=3;
		$sa['idPlano'] = $matriz['idPlano'];
		$sa['idServicoPlano'] = $matriz['idServicoPlano'];
			
		if ($sa['valor'] > 0 ){
			dbServicoAdicional($sa, 'incluir');
			
			$sa['periodo'] = $periodo;
			$sa['periodoTipo'] = $periodoTipo;
			if ($ver)
				avisoServicoAdicionalIncluido($sa);
			
			enviarEmailAtivacaoServicoAdicional($sa);
				
		}
	}
	// inativou e reativou no mesmo mes.
	elseif ($dtAtivacao > $dtFaturamento && $dtFaturamento < $dtInativacao)
		$ret = date ('d-m-Y', $dtFaturamento);
		
	return($ret);
}

function adicionarServicoAdicionalServicoPlanoPeriodoCompleto($matriz, $ver) {
	$ret = false;
	$data = dataSistema();
	
	$parametros = carregaParametrosConfig();
	if ($parametros['diasFaturamento'] > 0 )	$diasFaturamento = $parametros['diasFaturamento'];
	else $diasFaturamento = 15;
	unset ( $parametros );
	
	$dtAtivacao = converteData($matriz['dtAtivacao'], 'form', 'timestamp');
	$dtInativacao = converteData($matriz['dtInativacao'], 'banco', 'timestamp');
	$dtVencimento = mktime(0,0,0,date('m', $dtAtivacao), $matriz['vencimento']['diaVencimento'], date('Y', $dtAtivacao));
	
	if ($dtVencimento < $dtAtivacao)
		$dtVencimento = strtotime (' + 1 month', $dtVencimento);
	
	$dtFaturamento = strtotime( "-".$diasFaturamento." days",$dtVencimento);
	
	
	$dtVencimentoInativacao = mktime(0,0,0,date('m', $dtInativacao), $matriz['vencimento']['diaVencimento'], date('Y', $dtInativacao));
	if ($dtVencimentoInativacao < $dtInativacao)
		$dtVencimentoInativacao = strtotime (' + 1 month', $dtVencimentoInativacao);
	$dtFaturamentoInativacao = strtotime( "-".$diasFaturamento." days",$dtVencimentoInativacao);
	
//	echo "<br> dtFaturamento:".date ('d-m-Y', $dtFaturamento). 
//		"<br> dtFaturamentoIn:".date ('d-m-Y', $dtFaturamentoInativacao). 
//		"<br> dtInativacao:".date ('d-m-Y', $dtInativacao).
//		"<br> dtAtivacao:".date ('d-m-Y', $dtAtivacao).
//		"<br> dtVencimentoInativacao:".date ('d-m-Y', $dtVencimentoInativacao).
//		"<br> dtVencimento:".date ('d-m-Y', $dtVencimento);
	
	if ($dtFaturamento > $dtInativacao){
		if ($matriz['tipoCobranca']['proporcional'] == 'S'){
			$dtInicial = ( $dtInativacao > $dtFaturamentoInativacao ? $dtVencimentoInativacao : $dtInativacao);
			$dtFinal   = ( $dtAtivacao   > $dtFaturamento ? $dtVencimento : $dtFinal );
			
			$periodoTipo = 'dias';
			$periodo = ceil( ( $dtFinal - $dtInicial ) / 86400 );
			
			$valor = $matriz['valorServico']/30 * $periodo;
		}
		if ($matriz['tipoCobranca']['proporcional'] == 'N'){
			$dtInicial = ( $dtInativacao > $dtFaturamentoInativacao ? $dtVencimentoInativacao : strtotime( '-1 month ', $dtVencimentoInativacao) );
			$dtFinal   = ( $dtAtivacao   > $dtFaturamento ? $dtVencimento :  strtotime( '-1 month ', $dtVencimento) );
			
			$periodo = ceil(($dtFinal - $dtInicial) / (60 * 60 * 24 * 31) );
			$periodoTipo = 'mes(es)';
			
			$valor = $matriz['valorServico'] * $periodo;
		}
					
//			echo "<br>dtInicial".date('d-m-Y', $dtInicial).
//			"<br>dtFinal".date('d-m-Y', $dtFinal).
//			"<br>periodo".$periodo.
//			"<br>valor".$valor.
//			"<br>periodo tipo". $periodoTipo;
					
			
		if ($dtFaturamento < $dtAtivacao)
			$dtVencimento = strtotime (' + 1 month', $dtVencimento);


		$sa[nome]="Valor de $periodo $periodoTipo, por "; 
		$sa[valor]=$valor;
		$sa[dtVencimento]=date('Y-m-d', $dtVencimento);
		$sa[dtCadastro]=$data[dataBanco];
		$sa[status]='A';
		$sa[idServicoPlano]=$matriz['idServicoPlano'];
		$sa['idServico']=$matriz['idServico'];  #20050913
		$sa[idTipoServicoAdicional]=3;
		$sa['idPlano'] = $matriz['idPlano'];
		$sa['idServicoPlano'] = $matriz['idServicoPlano'];
			
		if ($sa['valor'] > 0 ){
			dbServicoAdicional($sa, 'incluir');
			
			$sa['periodo'] = $periodo;
			$sa['periodoTipo'] = $periodoTipo;
			if ($ver)
				avisoServicoAdicionalIncluido($sa);
		}
	}
	// inativou e reativou no mesmo mes.
//	elseif ($dtAtivacao > $dtFaturamento && $dtFaturamento < $dtInativacao)
//		$ret = date ('d-m-Y', $dtFaturamento);
		
	return($ret);
}


function avisoServicoAdicionalIncluido ($matriz, $titulo='', $msg='') {
	global $corFundo, $corBorda;
	
	# Mostrar Mensagem
	novaTabela2("[Serviço Adicional Incluido ao Faturamento]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		# Opcoes Adicionais
		novaLinhaTabela($corFundo, '100%');
			itemLinhaNOURL('&nbsp;', 'left', $corFundo, 2, 'normal10');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaNOURL("ATENÇÃO: Foi gerado um serviço adicional ao ativar este servico<br>Conferir valores abaixo:", 'center', $corFundo, 2, 'txtaviso');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaNOURL('&nbsp;', 'left', $corFundo, 2, 'normal10');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Descrição:</b>', 'right', 'middle', '40%', $corFundo, 0, 'normal10');
			itemLinhaForm($matriz[nome], 'left', 'top', $corFundo, 0, 'normal10');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Valor:</b>', 'right', 'middle', '40%', $corFundo, 0, 'normal10');
			itemLinhaForm(formatarValoresForm($matriz[valor]), 'left', 'top', $corFundo, 0, 'normal10');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Utilização:</b>', 'right', 'middle', '40%', $corFundo, 0, 'normal10');
			itemLinhaForm("$matriz[periodo] $matriz[periodoTipo]", 'left', 'top', $corFundo, 0, 'normal10');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Vencimento:</b>', 'right', 'middle', '40%', $corFundo, 0, 'normal10');
			itemLinhaForm(converteData($matriz[dtVencimento],'banco','formdata'), 'left', 'top', $corFundo, 0, 'normal10');
		fechaLinhaTabela();
	fechaTabela();
	echo "<br>";
}


?>
