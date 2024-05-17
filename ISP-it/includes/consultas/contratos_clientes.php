<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 15/04/2004
# Ultima alteração: 15/04/2004
#    Alteração No.: 001
#
# Função:
#      Consulta de Contratos por Cliente

/**
 * @return void
 * @param string $modulo
 * @param string $sub
 * @param string $acao
 * @param int  $registro
 * @param array $matriz
 * @desc Form para parametros de consulta de Contratos por cliente
*/
function formContratosCliente($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	# Motrar tabela de busca
	novaTabela2("[Consulta Contratos por Cliente]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		# Opcoes Adicionais
		menuOpcAdicional($modulo, $sub, $acao, $registro);
		#fim das opcoes adicionais
		novaLinhaTabela($corFundo, '100%');
		$texto="			
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=registro>";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
		
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Busca por Cliente:</b><br>
			<span class=normal10>Informe nome ou dados do cliente para busca</span>', 'right', 'middle', '35%', $corFundo, 0, 'tabfundo1');
			$texto="<input type=text name=matriz[txtProcurar] size=60 value='$matriz[txtProcurar]'> <input type=submit value=Procurar name=matriz[bntProcurar] class=submit>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		if($matriz[txtProcurar]) {
			# Procurar Cliente
			$tipoPessoa=checkTipoPessoa('cli');
			$consulta=buscaPessoas("
				((upper(nome) like '%$matriz[txtProcurar]%' 
					OR upper(razao) like '%$matriz[txtProcurar]%' 
					OR upper(site) like '%$matriz[txtProcurar]%' 
					OR upper(mail) like '%$matriz[txtProcurar]%')) 
				AND idTipo=$tipoPessoa[id]", $campo, 'custom','nome');
			
			if($consulta && contaConsulta($consulta)>0) {
				# Selecionar cliente
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Clientes encontrados:</b><br>
					<span class=normal10>Selecione o Cliente</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					itemLinhaForm(formSelectConsulta($consulta, 'nome', 'idPessoaTipo', 'idPessoaTipo', $matriz[idPessoaTipo]), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Status:</b><br>
					<span class=normal10>Selecione o Status do Contrato</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					itemLinhaForm(formSelectStatusContratos($matriz[status],'status','multi'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit name=matriz[bntConfirmar] value='Consultar' class=submit>";
					itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
			}
		}
	
		htmlFechaLinha();
	fechaTabela();
	
}


/**
 * @return void
 * @param string $modulo
 * @param string $sub
 * @param string $acao
 * @param int $registro
 * @param array $matriz
 * @desc Consulta de Contratos por cliente
*/
function consultaContratosCliente($modulo, $sub, $acao, $registro, $matriz) {

	
	global $corFundo, $corBorda, $html, $sessLogin, $conn, $tb, $limites;
	
	# Montar 
	if($matriz[status]) {
		$i=0;
		$sqlADD="AND $tb[ContratosServicosPlanos].status in (";
		while($matriz[status][$i]) {
			
			$sqlADD.="'".$matriz[status][$i]."'";
			
			if($matriz[status][$i+1]) $sqlADD.=",";
			$i++;
		}
		$sqlADD.=")";
		
	}

	# SQL para consulta de emails por dominios do cliente informado
	$sql="
		SELECT
			$tb[ContratosServicosPlanos].id id, 
			$tb[ContratosServicosPlanos].idContrato idContrato, 
			$tb[ContratosServicosPlanos].idServicoPlano idServicoPlano, 
			$tb[ContratosServicosPlanos].idUsuario idUsuario, 
			$tb[ContratosServicosPlanos].dtEmissao dtEmissao, 
			$tb[ContratosServicosPlanos].dtRenovacao dtRenovacao, 
			$tb[ContratosServicosPlanos].mesValidade mesValidade, 
			$tb[ContratosServicosPlanos].numeroContrato numeroContrato, 
			$tb[ContratosServicosPlanos].numeroSequencia numeroSequencia, 
			$tb[ContratosServicosPlanos].nomePagina nomePagina, 
			$tb[ContratosServicosPlanos].nomeArquivo nomeArquivo, 
			$tb[ContratosServicosPlanos].status status,
			$tb[Contratos].nome nomeContrato, 
			$tb[ServicosPlanos].idServico idServico,
			$tb[Servicos].nome nomeServico
		FROM
			$tb[ContratosServicosPlanos], 
			$tb[Contratos], 
			$tb[ServicosPlanos], 
			$tb[Servicos], 
			$tb[PlanosPessoas] 
		WHERE
			$tb[ServicosPlanos].idServico= $tb[Servicos].id 
			AND $tb[ServicosPlanos].idPlano = $tb[PlanosPessoas].id 
			AND $tb[ContratosServicosPlanos].idContrato = $tb[Contratos].id 
			AND $tb[ServicosPlanos].id = $tb[ContratosServicosPlanos].idServicoPlano 
			AND $tb[PlanosPessoas].idPessoaTipo = '$matriz[idPessoaTipo]'
			$sqlADD
	";
	
	$consulta=consultaSQL($sql, $conn);
	
	# Cabeçalho
	itemTabelaNOURL('&nbsp;', 'right', $corFundo, 0, 'normal10');
	# Mostrar Cliente
	htmlAbreLinha($corFundo);
		htmlAbreColunaForm('100%', 'center', 'middle', $corFundo, 2, 'normal10');
			novaTabela("[Resultados]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 6);
			# Opcoes Adicionais
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('65%', 'left', $corFundo, 6, 'tabfundo1');
					novaTabelaSH("center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
						menuOpcAdicional('lancamentos', 'planos', 'listar', $matriz[idPessoaTipo]);
					fechaTabela();
				htmlFechaColuna();
			fechaLinhaTabela();
			
			if(!$consulta || contaConsulta($consulta)==0 ) {
				# Não há registros
				itemTabelaNOURL('Não foram encontrados Contratos cadastrados', 'left', $corFundo, 6, 'txtaviso');
			}
			elseif($consulta && contaConsulta($consulta)>0 && (!$registro || is_numeric($registro)) ) {	
				
				# Cabeçalho
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela('Contrato', 'center', '10%', 'tabfundo0');
					itemLinhaTabela('Página', 'center', '30%', 'tabfundo0');
					itemLinhaTabela('Status', 'center', '10%', 'tabfundo0');
					itemLinhaTabela('Data Emissão', 'center', '10%', 'tabfundo0');
					itemLinhaTabela('Validade', 'center', '10%', 'tabfundo0');
					itemLinhaTabela('Opções', 'center', '30%', 'tabfundo0');
				fechaLinhaTabela();
		
				$i=0;
				
				while($i < contaConsulta($consulta)) {
					# Mostrar registro
					$id=resultadoSQL($consulta, $i, 'id');
					$idContrato=resultadoSQL($consulta, $i, 'idContrato');
					$nomeContrato=resultadoSQL($consulta, $i, 'nomeContrato');
					$nomeServico=resultadoSQL($consulta, $i, 'nomeServico');
					$nomePagina=resultadoSQL($consulta, $i, 'nomePagina');
					$nomeArquivo=resultadoSQL($consulta, $i, 'nomeArquivo');
					$idServicoPlano=resultadoSQL($consulta, $i, 'idServicoPlano');
					$idUsuario=resultadoSQL($consulta, $i, 'idUsuario');
					$dtEmissao=resultadoSQL($consulta, $i, 'dtEmissao');
					$dtRenovacao=resultadoSQL($consulta, $i, 'dtRenovacao');
					$mesValidade=resultadoSQL($consulta, $i, 'mesValidade');
					$numeroContrato=resultadoSQL($consulta, $i, 'numeroContrato');
					$status=resultadoSQL($consulta, $i, 'status');
		
					if($status=='R') $dtEmissao=$dtRenovacao;
					$dataValidade=validadeContrato($dtEmissao, $mesValidade);
					
					# Verificar se contrato não esta vencido
					if(converteData($data[dataBanco],'banco','timestamp') >= converteData($dataValidade,'banco','timestamp'))
						$opcVencido="<span class=txtaviso>(vencido)</span> ";
					else $opcVencido='';
		
					$opcoes=htmlMontaOpcao("<a href=$nomeArquivo>Visualizar (PDF)</a>",'pdf');
					$opcoes.=htmlMontaOpcao("<a href=?modulo=contratos&sub=&acao=renovar&registro=$matriz[idPessoaTipo]:$id>Renovar</a>",'renovar');
					if($status=='A' || $status=='R' || $status=='I') {
						$opcoes.=htmlMontaOpcao("<a href=?modulo=contratos&sub=&acao=cancelar&registro=$matriz[idPessoaTipo]:$id>Cancelar</a>",'cancelar');
					}
		
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTabela("$numeroContrato", 'left', '10%', 'normal10');
						itemLinhaTabela($opcVencido.$nomePagina, 'left', '40%', 'normal10');
						itemLinhaTabela(formSelectStatusContratos($status,'','check'), 'center', '10%', 'normal10');
						itemLinhaTabela(converteData($dtEmissao, 'banco','formdata'), 'center', '10%', 'normal10');
						itemLinhaTabela(converteData($dataValidade,'banco','formdata'), 'center nowrap', '10%', 'normal10');
						itemLinhaTabela($opcoes, 'left nowrap', '30%', 'normal8');
					fechaLinhaTabela();
					
					# Incrementar contador
					$i++;
				} #fecha laco de montagem de tabela
			}
				
			fechaTabela();
		htmlFechaColuna();
	htmlFechaLinha();	
}

?>