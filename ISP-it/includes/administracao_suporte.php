<?


################################################################################
#       Criado por: Leandro Barros Grandinetti - leandro@seumadruga.com.br
#  Data de criação: 29/05/2007
# Ultima alteração: 29/05/2007
#    Alteração No.: 001
#
# Função:
#    Painel - Funções para controle de serviço de suporte
#
function administracaoSuporte($modulo, $sub, $acao, $registro, $matriz) {
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin;
	# Permissão do usuario
	$permissao = buscaPermissaoUsuario($sessLogin[login], 'login', 'igual', 'login');

	if (!$permissao[admin] && !$permissao[abrir] && !$permissao[visualizar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg = "ATENÇÃO: Você não tem permissão para executar esta função";
		$url = "?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	elseif ($acao == 'config') {
		suporteListarTipos($modulo, $sub, $acao, $registro, $matriz);
	}
	elseif ($acao == 'adicionar') {
		adicionarSuporte($modulo, $sub, $acao, $registro, $matriz);
	}
	elseif ($acao == 'excluir') {
		excluirSuporte($modulo, $sub, $acao, $registro, $matriz);
	}
	elseif ($acao == 'alterar') {
		alterarSuporte($modulo, $sub, $acao, $registro, $matriz);
	} else {
		suporteListarTipos($modulo, $sub, $acao, $registro, $matriz);
	}
}

function suporteListarTipos($modulo, $sub, $acao, $registro, $matriz) {
	global $conn, $tb, $corFundo, $corBorda, $limite, $html;

	$idPessoaTipo = $matriz['idPessoaTipo'];
	$idModulo = $matriz['idModulo'];

	if ($matriz['txtProcurar']) {
		$sqlADD = " AND (
								$tb[Maquinas].nome like '%$matriz[txtProcurar]%'
								OR $tb[Maquinas].ip like '%$matriz[txtProcurar]%'
								)";
	}

	if ($idPessoaTipo) {
		# Grava os dados relacionados entre Suporte e Máquinas
		$bd = new BDIT();
		$bd->setConnection($conn);

		$campos = array (
		"$tb[Suporte].* ",
		"$tb[ServicosPlanos].id as idServicosPlanos ",
		"$tb[Servicos].nome as nomeServico",
		"$tb[PlanosPessoas].idPessoaTipo as idPessoaTipo"
		);

		$tabelas = "{$tb['Suporte']} " .
		"INNER JOIN {$tb['ServicosPlanos']} " .
		"ON ({$tb['Suporte']}.idServicoPlano = {$tb['ServicosPlanos']}.id) " .
		"INNER JOIN {$tb['Servicos']} " .
		"ON ({$tb['ServicosPlanos']}.idServico = {$tb['Servicos']}.id) " .
		"INNER JOIN {$tb['PlanosPessoas']} " .
		"ON ({$tb['ServicosPlanos']}.idPlano = {$tb['PlanosPessoas']}.id)";

		$condicao = "$tb[PlanosPessoas].idPessoaTipo = $idPessoaTipo";

		$consulta = $bd->seleciona($tabelas, $campos, $condicao, '', '');
		# Gera um novo array com o registro de suporte unico
		$indiceNovoArray = 0;
		for ($indice = 0; $indice < count($consulta); $indice++) {
			if ($ultimoId != $consulta[$indice]->id) {
				$arraySuporte[$indiceNovoArray++] = $consulta[$indice];
			}
			$ultimoId = $consulta[$indice]->id;
		}

		$rows = count($arraySuporte);
		novaTabela("Suporte", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 4);
		novaLinhaTabela($corFundo, '100%');
		htmlAbreColuna('100%', 'right', $corFundo, 4, 'tabfundo1');
		novaTabela2SH("center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		novaLinhaTabela($corFundo, '100%');
		$texto = "
								<form method=post name=matriz action=index.php>
								<input type=hidden name=modulo value=$modulo>
								<input type=hidden name=sub value=$sub>
								<input type=hidden name=acao value=config>
								<input type=hidden name=registro value=$idPessoaTipo>
								<b>Procurar por:</b> <input type=text name=matriz[txtProcurar] size=25 value='$matriz[txtProcurar]'>
								<input type=submit name=matriz[bntProcurar] value=Procurar class=submit>";
		itemLinhaForm($texto, 'center', 'middle', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		fechaTabela();
		htmlFechaColuna();
		fechaLinhaTabela();

		# Verifica suporte por servico configurados para mostrar ou nao o link de "adicionar"
		$total = suporteTotal($idPessoaTipo);
		$totalEmUso = suporteTotalEmUso($idPessoaTipo);
		# Totais adicionados para verificar se todas as máquinas do cliente estão relacionadas à algum
		# suporte - por Felipe Assis - 10/06/2008
		$totalMaquinas = getMaquinasEmpresa($idPessoaTipo);
		$totalMaquinasSuporte = maquinasTotalEmUso($idPessoaTipo);
		
		# A opção 'Adicionar' só poderá ser exibida caso todas as máquinas do cliente ainda não
		# tenham sido usadas
		if($totalMaquinas > $totalMaquinasSuporte){
			if ($total > $totalEmUso) {	
				$opcoes = htmlMontaOpcao("<a href=?modulo=administracao&sub=suporte&acao=adicionar&registro=$idPessoaTipo>Adicionar</a>", 'incluir');
				itemTabelaNOURL($opcoes, 'right', $corFundo, 4, 'tabfundo1');
			}
		}
	}
	
	if ($consulta && $rows > 0) {

		$matriz['registro'] = $idPessoaTipo;
		paginador2($arraySuporte, $rows, $limite['lista']['maquinas'], $matriz, 'normal8', 3, "");

		novaLinhaTabela($corFundo, '100%');
		itemLinhaTabela("Detalhes", 'center', '40%', 'tabfundo0');
		itemLinhaTabela('Status', 'center', '10%', 'tabfundo0');
		itemLinhaTabela('Máquina(s)', 'center', '30%', 'tabfundo0');
		itemLinhaTabela('Opções', 'center', '20%', 'tabfundo0');
		fechaLinhaTabela();

		# Setar registro inicial
		if (!$matriz['pagina']) {
			$i = 0;
		}
		elseif ($matriz['pagina'] && is_numeric($matriz['pagina'])) {
			$i = $matriz['pagina'];
		} else {
			$$i = 0;
		}

		$limite = $i + $limite['lista']['maquinas'];
		for ($a = $i; $a < $rows && $a < $limite; $a++) {

			$opcoes = htmlMontaOpcao("<a href=?modulo=administracao&sub=suporte&acao=alterar&registro=$idPessoaTipo&idSuporte={$arraySuporte[$a]->id}&idServicoPlano={$arraySuporte[$a]->idServicosPlanos}>Alterar</a>", 'alterar');
			$opcoes .= htmlMontaOpcao("<a href=?modulo=administracao&sub=suporte&acao=excluir&registro=$idPessoaTipo:{$arraySuporte[$a]->id}>Excluir</a>", 'excluir');

			#visualizar servico/plano.
			$opcoes .= "<br>";
			$opcoes .= htmlMontaOpcao("<a href=?modulo=lancamentos&sub=planos&acao=verservico&registro={$arraySuporte[$a]->idServicosPlanos}>Visualizar Plano</a>", 'planos');
			// opção extra para bloqueio de suporte by Felipe Assis (07/11/2007)
			$parametros = carregaParametrosConfig();
			if (strtoupper($parametros['integrarTicket']) == 'S') {
				$opcoes .= opcaoBloquearSuporte($modulo, $acao, $acao, $matriz, $idPessoaTipo, $arraySuporte[$a]->id);
			}

			# Recuperando lista de máquinas para a listagem
			$conMaquina = dbMaquinasSuporte($consulta[$a]->id, $matriz, 'buscarMaquinaSuporte');
			$listaMaquinas = "";
			for ($j = 0; $j < mysql_num_rows($conMaquina); $j++) {
				$maquinas = mysql_fetch_array($conMaquina);
				$maquina[$j] = $maquinas['nome'];
				$listaMaquinas .= $maquina[$j] . "<br>";
			}

			novaLinhaTabela($corFundo, '100%');
			$texto = "<img src=" . $html['imagem']['suporte'] . " border=0 align=left vspace=25>{$arraySuporte[$a]->nomeServico}<br><span class=normal8>" .
			"<b>Expediente:</b> {$arraySuporte[$a]->horasExpediente} Hs<br>" .
			"<b>Fora Expediente:</b> {$arraySuporte[$a]->horasForaExpediente} Hs<br>" .
			"<b>Suporte Adicional:</b> " . (($arraySuporte[$a]->suporteForaExpediente == 'S') ? "<span class=txtok>Sim</span>" : "<span class=txtaviso>Não</span>");
			// Por Felipe Assis - 16/01/2008
			// Adição do atributo prioridade na listagem do suporte
			$prioridade = '';
			$corFonte = '';
			if($arraySuporte[$a]->prioridade == 'B'){
				$corFonte = '#aeadff';
				$prioridade = 'Baixa';
			}
			elseif ($arraySuporte[$a]->prioridade == 'M'){
				$corFonte = '#ffb159';
				$prioridade = 'Média';
			}
			else{
				$corFonte = '#ff723f';
				$prioridade = 'Alta';
			}
			$texto .= "<br><b>Prioridade: </b></span><span class='bold10'><font color=$corFonte>{$prioridade}</font></span>";
			itemLinhaTMNOURL($texto, 'left', 'middle', '40%', $corFundo, 0, 'normal10');
			itemLinhaTMNOURL(formSelectStatusSuporte($arraySuporte[$a]->status, '', 'check'), 'center', 'middle', '10%', $corFundo, 0, 'normal8');
			itemLinhaTMNOURL($listaMaquinas, 'center', 'middle', '30%', $corFundo, 0, 'normal8');
			itemLinhaTMNOURL($opcoes, 'left', 'middle', '20%', $corFundo, 0, 'normal8');
			fechaLinhaTabela();
		}
	} else {
		$texto = "<span class=txtaviso>Não existe suporte cadastrado para o cliente!</span>";
		itemTabelaNOURL($texto, 'left', $corFundo, 3, 'normal10');
	}
	fechaTabela();
}

function adicionarSuporte($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;

	# Form de inclusao
	if (!$matriz['bntAdicionar'] || !$matriz['idServicoPlano']) {
		# Motrar tabela de adição
		novaTabela2("[Adicionar Suporte]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		novaLinhaTabela($corFundo, '100%');
		//Adicionar validação de suporte aqui
		# Consultando total de máquinas disponíveis
		# Obtendo total de máquinas em uso
		$totalMaquinas = maquinasTotal($registro);
		$totalEmUso = maquinasTotalEmUso($registro);
		$texto = "<form method=post name=matriz action=index.php>" .
		"<input type=hidden name=modulo value=$modulo>" .
		"<input type=hidden name=sub value=$sub>" .
		"<input type=hidden name=acao value=$acao>" .
		"<input type=hidden name=registro value=$registro>" .
		"<input type=hidden name=matriz[idPessoasTipos] value=$registro>" .
		"<input type=hidden name=matriz[idSuporte] value=$suporte[id]>" .
		"<input type=hidden name=matriz[totalMaquinas] value=$totalMaquinas>" .
		"<input type=hidden name=matriz[totalEmUso] value=$totalEmUso>";
		itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
		htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
		echo "<b class=bold10>Serviço: </b><br>" .
		"<span class=normal10>Serviço a atribuir o suporte</span>";
		htmlFechaColuna();
		$servicos = suporteServicosDisponivel($matriz['idPessoaTipo'], '');
		$texto = "<select name='matriz[idServicoPlano]' onchange='JavaScript:submit()'>\n";
		$texto .= "<option value=''>Selecione um serviço</option>\n";
		for ($i = 0; $i < count($servicos); $i++) {
			if ($matriz['idServicoPlano'] == $servicos[$i]->idServicoPlano) {
				$texto .= "<option value='" . $servicos[$i]->idServicoPlano . "' selected>" . $servicos[$i]->nomePlano . " - " . $servicos[$i]->nomeServico . "</option>\n";
			} else {
				$texto .= "<option value='" . $servicos[$i]->idServicoPlano . "'>" . $servicos[$i]->nomePlano . " - " . $servicos[$i]->nomeServico . "</option>\n";
			}
		}
		$texto .= "</select>";
		itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();

		# Mostra o restante do form caso o plano seja selecionado:
		if ($matriz['idServicoPlano'] || $matriz['idServicoPlano'] != "") {
			novaLinhaTabela($corFundo, '100%');
			//Caixa de seleção da máquina a atribuir o suporte
			//by Felipe Assis (06/11/2007)
			htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
			echo "<b class='bold10'>Máquina(s):</b><br>";
			echo "<span class='normal10'>Computador(es) a atribuir o suporte</span>";
			htmlFechaColuna();
			$texto = selectMultiMaquinaEmpresa($matriz['idPessoaTipo'], $acao);
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
			echo "<b class=bold10>Horas expediente: </b><br>
																											<span class=normal10>Horas de suporte no horário comercial</span>";
			htmlFechaColuna();
			$texto = "<input type=text name=matriz[horasExpediente] size=4 value='$matriz[horasExpediente]'> Horas";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
			echo "<b class=bold10>Horas fora expediente: </b><br>
																											<span class=normal10>Horas de suporte fora do horário comercial</span>";
			htmlFechaColuna();
			$texto = "<input type=text name=matriz[horasForaExpediente] size=4 value='$matriz[horasForaExpediente]'> Horas";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
			echo "<b class=bold10>Suporte fora expediente: </b><br>" .
			"<span class=normal10>Serviço possui suporte adicional fora do horário comercial</span>";
			htmlFechaColuna();
			$texto = "<select name='matriz[suporteForaExpediente]'>" .
			"<option value='S'>Sim</option>" .
			"<option value='N'>Não</option>" .
			"</select>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			$maquinas = maquinasCadastradasPorServico($matriz['idServicoPlano']);
			novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
			echo "<b class=bold10>Prioridade: </b><br>
																											<span class=normal10>Prioridade do suporte</span>";
			htmlFechaColuna();
			itemLinhaForm(formSelectStatusPrioridade($matriz['prioridade'], 'prioridade', 'form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
			echo "<b class=bold10>Status: </b><br>
																											<span class=normal10>Status do suporte</span>";
			htmlFechaColuna();
			itemLinhaForm(formSelectStatusAtivoInativo($matriz['status'], 'status', 'form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
			echo "&nbsp;";
			htmlFechaColuna();
			$texto = "<input type=submit name=matriz[bntAdicionar] value=Adicionar class=submit>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		} # fecha o condicional do plano selecionado
		fechaTabela();
	} #fecha form
	elseif ($matriz['bntAdicionar']) {
		$totalMaquinasSelecionadas = count($_REQUEST['idMaquinas']);
		# Conferir disponibilidade das máquinas
		//Consultando limite de máquinas para cada serviço

		// Obtendo quantidade de máquinas permitidas para suporte
		$conServicoParametro = dbParametroServico($matriz, 'retornaValor');
		$qtdeMaquinas = resultadoSQL($conServicoParametro, 0, "valor");

		if (($totalMaquinasSelecionadas > $qtdeMaquinas) && ($totalMaquinasSelecionadas > $matriz['totalMaquinas'] ) && !( is_null($matriz['totalEmUso']) ) ){

			# acusar indisponibilidade de máquinas
			# Mensagem de aviso
			$msg = "Não há máquinas disponíveis para este suporte. Verifique a quantidade de máquinas cadastradas.";
			$url = "?modulo=administracao&sub=suporte&acao=config&registro=$matriz[idPessoaTipo]";
			aviso("Atenção: Máquinas Indisponíveis", $msg, $url, 400);
		}
		else {
			# Conferir campos
			if ($matriz['horasExpediente']) {

				$grava = dbSuporte($matriz, 'incluir');

				# Verificar inclusão de registro
				if ($grava) {
					if ($_REQUEST['idMaquinas']) {
						# Gravando relacionamento Suporte-Máquina
						dbMaquinasSuporte('', $matriz, 'inserir');
					}
					# Mensagem de aviso
					$msg = "Registro Gravado com Sucesso!";
					avisoNOURL("Aviso", $msg, 400);

					echo "<br>";
					$matriz['idPessoaTipo'] = $matriz['idPessoasTipos'];
					suporteListarTipos($modulo, $sub, 'listar', $registro, $matriz);
				}
			}
			# falta de parametros
			else {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg = "Falta de parâmetros necessários. Informe os campos obrigatórios e tente novamente.";
				avisoNOURL("Aviso: Ocorrência de erro.", $msg, 400);
			}
		}

	}
}

// Função para efetuar a alteração dos registros
// By Felipe Assis (12/11/2007)
function alterarSuporte($modulo, $sub, $acao, $registro, $matriz) {


	global $corFundo, $corBorda, $html, $conn, $tb, $sessLogin;

	if (!$matriz['bntAlterar']) {

		# Montar form de alteração
		novaTabela2("[Alterar Suporte]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		novaLinhaTabela($corFundo, '100%');
		# Obtendo total de máquinas em usos

		$totalMaquinas = maquinasTotal($registro);
		$totalEmUso = maquinasTotalEmUso($registro);

		$registro = $_REQUEST['registro'];
		$idSuporte = $_REQUEST['idSuporte'];
		$idServicoPlano = $_REQUEST['idServicoPlano'];

		$texto = "<form method=post name=matriz action=index.php?registro=$registro>" .
		"<input type=hidden name=modulo value=$modulo>" .
		"<input type=hidden name=sub value=$sub>" .
		"<input type=hidden name=acao value=$acao>" .
		"<input type=hidden name=registro value=$registro>" .
		"<input type=hidden name=matriz[idPessoasTipos] value=$registro>" .
		"<input type=hidden name=matriz[idServicoPlano] value=$idServicoPlano>" .
		"<input type=hidden name=matriz[idModulo] value=$matriz[idModulo]>" .
		"<input type=hidden name=matriz[idSuporte] value=$idSuporte>" .
		"<input type=hidden name=matriz[totalMaquinas] value=$totalMaquinas>" .
		"<input type=hidden name=matriz[totalEmUso] value=$totalEmUso>";

		itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
		htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
		echo "<b class=bold10>Serviço: </b><br>" .
		"<span class=normal10>Serviço a ser alterado</span>";
		htmlFechaColuna();
		$servicos = suporteServicosDisponivel($registro, '');
		//Consultando serviço por máquina
		$sql = "SELECT $tb[PlanosPessoas].nome as PlanoPessoa, $tb[Servicos].nome as ServicoPlano " .
		"FROM $tb[Servicos] " .
		"INNER JOIN $tb[ServicosPlanos] " .
		"ON ($tb[Servicos].id = $tb[ServicosPlanos].idServico) " .
		"INNER JOIN PlanosPessoas " .
		"ON ($tb[ServicosPlanos].idPlano = $tb[PlanosPessoas].id) " .
		"WHERE $tb[ServicosPlanos].id = $idServicoPlano";
		$consulta = consultaSQL($sql, $conn);

		$planoPessoa = resultadoSQL($consulta, 0, 'PlanoPessoa');
		$servicoPlano = resultadoSQL($consulta, 0, 'ServicoPlano');

		$texto =  $planoPessoa." - ".$servicoPlano;
		//alteração na listagem
		itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();

		#Consultar campos do registro a ser alterado
		# Procurar registro
		# sql1 -> Consultar os dados do suporte e o nome do serviço que está
		# recebendo o suporte

		$sql1 = "SELECT $tb[Suporte].id as idSuporte, " .
		"$tb[Suporte].idServicoPlano as idServicoPlano, " .
		"$tb[Servicos].id as idServico, " .
		"$tb[Servicos].nome as nomeServico, " .
		"$tb[Suporte].horasExpediente as horasExpediente, " .
		"$tb[Suporte].horasForaExpediente as horasForaExpediente, " .
		"$tb[Suporte].suporteForaExpediente as suporteForaExpediente," .
		"$tb[Suporte].prioridade as prioridade, " .
		"$tb[Suporte].status as status " .
		"FROM $tb[Suporte] " .
		"INNER JOIN $tb[ServicosPlanos] " .
		"ON ($tb[Suporte].idServicoPlano = $tb[ServicosPlanos].id) " .
		"INNER JOIN $tb[Servicos] " .
		"ON (ServicosPlanos.idServico = $tb[Servicos].id) " .
		"WHERE $tb[Suporte].id = $idSuporte";

		#sql2 -> Consultar o nome da máquina que está recebendo o suporte
		$sql2 = "SELECT $tb[Maquinas].nome as nomeMaquina " .
		"FROM $tb[Maquinas] " .
		"INNER JOIN $tb[MaquinasSuporte] " .
		"ON ($tb[Maquinas].id = $tb[MaquinasSuporte].idMaquina) " .
		"INNER JOIN $tb[Suporte] " .
		"ON ($tb[MaquinasSuporte].idSuporte = $tb[Suporte].id) " .
		"WHERE $tb[Suporte].id = $idSuporte";

		# Mostrará o restante do form caso o plano seja selecionado:
		# Executando consultas
		$consulta = consultaSQL($sql1, $conn);
		$consulta2 = consultaSQL($sql2, $conn);
		$idSuporte = resultadoSQL($consulta, 0, 'idSuporte');
		$idServicoPlano = resultadoSQL($consulta, 0, 'idServicoPlano');
		$nomeMaquina = resultadoSQL($consulta2, 0, 'nomeMaquina');
		$prioridade = resultadoSQL($consulta, 0, 'prioridade');
		$idServico = resultadoSQL($consulta, 0, 'idServico');
		$nomeServico = resultadoSQL($consulta, 0, 'nomeServico');
		$horasExpediente = resultadoSQL($consulta, 0, 'horasExpediente');
		$horasForaExpediente = resultadoSQL($consulta, 0, 'horasForaExpediente');
		$suporteForaExpediente = resultadoSQL($consulta, 0, 'suporteForaExpediente');
		$status = resultadoSQL($consulta, 0, 'status');

		novaLinhaTabela($corFundo, '100%');
		//Caixa de seleção da máquina a atribuir o suporte
		//by Felipe Assis (06/11/2007)
		htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
		echo "<b class='bold10'>Máquina(s):</b><br>";
		echo "<span class='normal10'>Computador(es) a atribuir o suporte</span>";
		htmlFechaColuna();
		$texto = selectMultiMaquinaEmpresa($registro, $acao, $idSuporte);
		itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
		htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
		echo "<b class=bold10>Horas expediente: </b><br>
																													<span class=normal10>Horas de suporte no horário comercial</span>";
		htmlFechaColuna();
		$texto = "<input type=text name=matriz[horasExpediente] value=$horasExpediente size=4 value='$matriz[horasExpediente]'> Horas";
		itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
		htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
		echo "<b class=bold10>Horas fora expediente: </b><br>
																													<span class=normal10>Horas de suporte fora do horário comercial</span>";
		htmlFechaColuna();
		$texto = "<input type=text name=matriz[horasForaExpediente] value=$horasForaExpediente size=4 value='$matriz[horasForaExpediente]'> Horas";
		itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
		htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
		echo "<b class=bold10>Suporte fora expediente: </b><br>
																												<span class=normal10>Serviço possui suporte adicional fora do horário comercial</span>";
		htmlFechaColuna();
		$texto = "<select name='matriz[suporteForaExpediente]'>";
		if (strtoupper($suporteForaExpediente) == 'S') {
			$texto .= "<option value='S' selected>Sim</option>" .
			"<option value='N'>Não</option>";
		}
		elseif (strtoupper($suporteForaExpediente == 'N')) {
			$texto .= "<option value='N' selected>Não</option>" .
			"<option value='S'>Sim</option>";
		}
		itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		$maquinas = maquinasCadastradasPorServico($idServicoPlano);
		if (count($maquinas) > 0) {
			novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
			echo "<b class=bold10>Máquinas: </b><br>
																																					<span class=normal10>Servidores associados ao suporte</span>";
			htmlFechaColuna();

			$texto = "<select name=idMaquinas[] multiple>\n";
			for ($i = 0; $i < count($maquinas); $i++) {
				$texto .= "<option selected value='" . $maquinas[$i]->id . "'>" . $maquinas[$i]->nome . "</option>\n";
			}
			$texto .= "</select>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		}
		novaLinhaTabela($corFundo, '100%');
		htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
		echo "<b class=bold10>Prioridade: </b><br>
																											<span class=normal10>Prioridade do suporte</span>";
		htmlFechaColuna();
		itemLinhaForm(formSelectStatusPrioridade($prioridade, 'prioridade', 'form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
		htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
		echo "<b class=bold10>Status: </b><br>
																											<span class=normal10>Status do suporte</span>";
		htmlFechaColuna();
		itemLinhaForm(formSelectStatusAtivoInativo($status, 'status', 'form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
		htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
		echo "&nbsp;";
		htmlFechaColuna();
		$texto = "<input type=submit name=matriz[bntAlterar] value=Alterar class=submit>";
		itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		echo "</form>";

		fechaTabela();
	} # fecha o form
	elseif ($matriz['bntAlterar']) {
		# Conferir disponibilidade das máquinas
		# Obtendo total de máquinas selecionadas
		$totalMaquinasSelecionadas = count($_REQUEST['idMaquinas']);
		# Conferir disponibilidade das máquinas
		//Consultando limite de máquinas para cada serviço

		// Obtendo quantidade de máquinas permitidas para suporte
		$conServicoParametro = dbParametroServico($matriz, 'retornaValor');
		$qtdeMaquinas = resultadoSQL($conServicoParametro, 0, "valor");
		# Conferir disponibilidade das máquinas
		if (($totalMaquinasSelecionadas > $qtdeMaquinas) && ($totalMaquinasSelecionadas > $matriz['totalMaquinas'] ) && !( is_null($matriz['totalEmUso']) ) ){

			# acusar indisponibilidade de máquinas
			# Mensagem de aviso
			$msg = "Não há máquinas disponíveis para este suporte. Verifique a quantidade de máquinas cadastradas.";
			$url = "?modulo=administracao&sub=suporte&acao=config&registro=$matriz[idPessoaTipo]";
			aviso("Atenção: Máquinas Indisponíveis", $msg, $url, 400);
		}
		else {
			# Conferir campos
			if ($matriz['horasExpediente']) {
				$altera = dbSuporte($matriz, 'alterar');
				# Verificar a alteração do registro
				if ($altera) {
					if ($_REQUEST['idMaquinas']) {
						$idMaquinas = $_REQUEST['idMaquinas'];
						# Alterando relacionamento Suporte-Máquina
						$excMaquinas = dbMaquinasSuporte($matriz['idSuporte'], $matriz, 'excluir');
						if ($excMaquinas) {
							for ($i = 0; $i < count($idMaquinas); $i++) {
								dbMaquinasSuporte($idMaquinas[$i], $matriz, 'alterar');
							}
						}
					}
					# Mensagem de aviso
					$msg = "Registro Alterado com Sucesso!";
					avisoNOURL("Aviso", $msg, 400);
					echo "<br>";
					$matriz['idPessoaTipo'] = $matriz['registro'];
					$matriz['idPessoaTipo'] = $registro;
					suporteListarTipos($modulo, $sub, 'listar', $registro, $matriz);
				}
			}
			# falta de parametros
			else {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg = "Falta de parâmetros necessários. Informe os campos obrigatórios e tente novamente.";
				avisoNOURL("Aviso: Ocorrência de erro.", $msg, 400);
			}
		}
	}
}

function excluirSuporte($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $html, $conn, $tb, $sessLogin;

	# Permissão do usuario
	$permissao = buscaPermissaoUsuario($sessLogin['login'], 'login', 'igual', 'login');

	if (!$permissao['admin'] && !$permissao['adicionar']) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg = "ATENÇÃO: Você não tem permissão para executar esta função";
		$url = "?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	} else {
		if (!$matriz['bntExcluir']) {
			# Procurar registro
			# sql1 -> Consultar os dados do suporte e o nome do serviço que está
			# recebendo o suporte
			$sql1 = "SELECT $tb[Suporte].id idSuporte, " .
			"$tb[Suporte].idServicoPlano idServicoPlano, " .
			"$tb[Servicos].id idServico, " .
			"$tb[Servicos].nome nomeServico, " .
			"$tb[Suporte].horasExpediente horasExpediente, " .
			"$tb[Suporte].horasForaExpediente horasForaExpediente, " .
			"$tb[Suporte].suporteForaExpediente suporteForaExpediente," .
			"$tb[Suporte].prioridade prioridade, " .
			"$tb[Suporte].status status " .
			"FROM $tb[Suporte] " .
			"INNER JOIN $tb[ServicosPlanos] " .
			"ON ($tb[Suporte].idServicoPlano = $tb[ServicosPlanos].id) " .
			"INNER JOIN $tb[Servicos] " .
			"ON (ServicosPlanos.idServico = $tb[Servicos].id) " .
			"WHERE $tb[Suporte].id = $matriz[id]";

			#sql2 -> Consultar o nome da máquina que está recebendo o suporte
			$sql2 = "SELECT $tb[Maquinas].nome nomeMaquina " .
			"FROM $tb[Maquinas] " .
			"INNER JOIN $tb[MaquinasSuporte] " .
			"ON ($tb[Maquinas].id = $tb[MaquinasSuporte].idMaquina) " .
			"INNER JOIN $tb[Suporte] " .
			"ON ($tb[MaquinasSuporte].idSuporte = $tb[Suporte].id) " .
			"WHERE $tb[Suporte].id = $matriz[id]";

			$consulta = consultaSQL($sql1, $conn);
			$consulta2 = consultaSQL($sql2, $conn);

			# Form de exclusao
			if (($consulta && contaConsulta($consulta) > 0) && ($consulta2 && contaConsulta($consulta2) > 0)) {

				$idSuporte = resultadoSQL($consulta, 0, 'idSuporte');
				$idServicoPlano = resultadoSQL($consulta, 0, 'idServicoPlano');
				$nomeMaquina = resultadoSQL($consulta2, 0, 'nomeMaquina');
				$prioridade = resultadoSQL($consulta, 0, 'prioridade');
				$idServico = resultadoSQL($consulta, 0, 'idServico');
				$nomeServico = resultadoSQL($consulta, 0, 'nomeServico');
				$idServicoPlano = resultadoSQL($consulta, 0, 'idServicoPlano');
				$horasExpediente = resultadoSQL($consulta, 0, 'horasExpediente');
				$horasForaExpediente = resultadoSQL($consulta, 0, 'horasForaExpediente');
				$suporteForaExpediente = resultadoSQL($consulta, 0, 'suporteForaExpediente');
				$status = resultadoSQL($consulta, 0, 'status');

				novaTabela2("[Excluir Suporte]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);

				novaLinhaTabela($corFundo, '100%');
				$texto = "<form method=post name=excluirsuporte action=index.php>" .
				"<input type=hidden name=modulo value=$modulo>" .
				"<input type=hidden name=sub value=$sub>" .
				"<input type=hidden name=acao value=$acao>" .
				"<input type=hidden name=registro value=$registro:$idSuporte>" .
				"<input type=hidden name=matriz[idSuporte] value=$idSuporte>" .
				"<input type=hidden name=matriz[idPessoasTipos] value=$matriz[idPessoaTipo]>";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b class=bold10>Serviço: </b>";
				htmlFechaColuna();
				itemLinhaForm($nomeServico, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b class=bold10>Máquina(s): </b>";
				htmlFechaColuna();

				// montado lista das máquinas relacionadas ao suporte
				$listaMaquinas = "";
				for ($i = 0; $i < mysql_num_rows($consulta2); $i++) {
					$maquina[$i] = resultadoSQL($consulta2, $i, 'nomeMaquina');
					$listaMaquinas .= $maquina[$i] . "<br>";
				}
				//alterar listagem de máquinas aqui
				itemLinhaForm($listaMaquinas, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b class=bold10>Horas expediente: </b>";
				htmlFechaColuna();
				itemLinhaForm($horasExpediente, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b class=bold10>Horas fora expediente: </b>";
				htmlFechaColuna();
				itemLinhaForm($horasForaExpediente, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
				echo "&nbsp;";
				htmlFechaColuna();
				$texto = "<input type=submit name=matriz[bntExcluir] value=Excluir class=submit></form>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				fechaTabela();
			}
		} #fecha form
		elseif ($matriz['bntExcluir']) {
			# Excluir Relação Suporte-Máquina no Ticket
			$excMaquinaSuporte = dbMaquinasSuporte($matriz['idSuporte'], $matriz, 'excluir');

			if ($excMaquinaSuporte) {
				# Excluir Suporte
				$excSuporte = dbSuporte($matriz, 'excluir', $matriz['idSuporte']);
				if ($excSuporte) {
					# Mensagem de aviso
					$msg = "Registro Excluído com Sucesso!";
					avisoNOURL("Aviso", $msg, 400);
					echo "<br>";
					$matriz['idPessoaTipo'] = $matriz['idPessoasTipos'];
					suporteListarTipos($modulo, $sub, 'listar', $registro, $matriz);
					//maquinasListarPessoas($modulo, $sub, $acao, $registro, $matriz);
				} else {
					$msg = "Ocorreu um erro na tentativa de excluir o registro (Suporte)!";
					avisoNOURL("Aviso", $msg, 400);
				}
			} else {
				$msg = "Ocorreu um erro na tentativa de excluir o registro (MaquinasSuporte)!";
				avisoNOURL("Aviso", $msg, 400);
			}

		}
	}

}

function opcaoBloquearSuporte($modulo, $acao, $sub, $matriz, $idPessoaTipo, $registro) {
	# Consultando id do suporte

	$conStatus = dbSuporte('', 'checarStatus', $registro);
	$status = mysql_fetch_array($conStatus);
	$acao = 'ver';
	$sub = 'suporte';

	if (strtoupper($status['status']) == 'A') { //se suporte está ativo
		$acaoBloqueio = "bloquearSuporte";
		$opcao = "Bloquear Suporte";
		$icone = 'desativar';
	}
	elseif (strtoupper($status['status'] == 'I')) { //se suporte está inativo
		$acaoBloqueio = "desbloquearSuporte";
		$opcao = "Desbloquear Suporte";
		$icone = 'ativar';
	}

	// montando link e opção. O ícone é definido pela ação
	$link = "?modulo=$modulo&sub=$sub&acao=$acao&acaoBloqueio=$acaoBloqueio" .
	"&registro=$idPessoaTipo&idSuporte=$registro";
	$retorno = htmlMontaOpcao("<a href='$link'>$opcao</a>", $icone);
	return $retorno;
}

?>