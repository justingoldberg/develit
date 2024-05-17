<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 15/04/2004
# Ultima alteração: 15/04/2004
#    Alteração No.: 001
#
# Função:
#      Ocorrências por Cliente

/**
 * @return void
 * @param unknown $modulo
 * @param unknown $sub
 * @param unknown $acao
 * @param unknown $registro
 * @param unknown $matriz
 * @desc Formulário para consulta de ocorrências do cliente
 Campos para filtragem
 Busca cliente por nome
 -> Listbox com clientes encontrados
 -> Listbox (multi) para seleção dos status das Ocorrências
*/
function formOcorrenciasCliente($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	# Motrar tabela de busca
	novaTabela2("[Consulta Ocorrências por Cliente]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
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
					itemLinhaTMNOURL('<b>Status da Ocorrência:</b><br>
					<span class=normal10>Selecione o status da ocorrência que deseja procurar</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					$texto=formSelectStatusOcorrencia($matriz[status], 'status', 'multi');
					$texto.="&nbsp;<input type=checkbox name=matriz[status_todos] value='S'><span class=txtaviso>Todos</span>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				//variavel utilizada no exemplo de digitação da data
				$data=dataSistema();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Data Inicial:</b><br>
					<span class=normal10>Informe a data inicial para consulta</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					$texto="<input type=text name=matriz[dtInicial] value='$matriz[dtInicial]' size=10 onBlur=verificaData(this.value,9)>&nbsp;<span class=txtaviso>(Formato: $data[dia]/$data[mes]/$data[ano])</span>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Data Final:</b><br>
					<span class=normal10>Informe a data final para consulta</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					$texto="<input type=text name=matriz[dtFinal] value='$matriz[dtFinal]' size=10 onBlur=verificaData(this.value,10)>&nbsp;<span class=txtaviso>(Formato: $data[dia]/$data[mes]/$data[ano])</span>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
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

# Consulta de Ocorrências
function consultaOcorrenciasCliente($modulo, $sub, $acao, $registro, $matriz) {

	
	global $corFundo, $corBorda, $html, $sessLogin, $conn, $tb, $limites;

	# Procedimentos
	# Consultar Ocorrencias na data informada
	# Em caso de não informada data, buscar todas referente ao status
	
	# Montar SQL adicional de data
	if($matriz[dtInicia] && $matriz[dtFinal])  {
		$matriz[dtInicialBanco]=converteData($matriz[dtInicial], 'form','bancodata');
		$matriz[dtFinalBanco]=converteData($matriz[dtInicial], 'form','bancodata');
		$sqlADDData=" AND ($tb[Ocorrencias].data BETWEEN '$matriz[dtInicialBanco]' AND '$matriz[dtFinalBanco]')";
	}
	elseif($matriz[dtInicial] && !$matriz[dtFinal]) {
		$matriz[dtInicialBanco]=converteData($matriz[dtInicial], 'form','bancodata');
		$sqlADDData=" AND ($tb[Ocorrencias].data >= '$matriz[dtInicialBanco]')";
	}
	elseif(!$matriz[dtInicial] && $matriz[dtFinal]) {
		$matriz[dtFinalBanco]=converteData($matriz[dtFinal], 'form', 'bancodata');
		$sqlADDData=" AND ($tb[Ocorrencias].data <= '$matriz[dtFinalBanco] 23:59:59')";
	}
	
	# Montar SQL adicinal de Status de Ocorrências
	if(!$matriz[status_todos] && $matriz[status]) {
		# SQL adicional de consulta de status
		$sqlADDStatus="AND $tb[Ocorrencias].status in (";
		$i=0;
		while ($matriz[status][$i]) {
			# avaliar status e adicionar a SQL de consulta
			$status=$matriz[status][$i];
			$sqlADDStatus.="'$status'";
			
			# Incrementar contador
			$i++;
			
			if($matriz[status][$i]) $sqlADDStatus.=',';
		}
		
		$sqlADDStatus.=")";
	}
	else {
		# Zerar sql adicional
		$sqlADDStatus='';
	}
		
	
	# 1-Consultar planos ativos
	$sql="
		SELECT
			$tb[Ocorrencias].id id, 
			$tb[Ocorrencias].nome nome, 
			$tb[Ocorrencias].data data, 
			$tb[Ocorrencias].idUsuario idUsuario, 
			$tb[Ocorrencias].idPrioridade idPrioridade, 
			$tb[Ocorrencias].status status
		FROM
			$tb[Ocorrencias]
		WHERE
			$tb[Ocorrencias].idPessoaTipo='$matriz[idPessoaTipo]'
			$sqlADDData
			$sqlADDStatus
		";
	
	if($sql) $consultaPlanosAtivos=consultaSQL($sql, $conn);
	
	$consulta=consultaSQL($sql, $conn);
	
	# Cabeçalho
	itemTabelaNOURL('&nbsp;', 'right', $corFundo, 0, 'normal10');
	# Mostrar Cliente
	htmlAbreLinha($corFundo);
		htmlAbreColunaForm('100%', 'center', 'middle', $corFundo, 2, 'normal10');
			novaTabela("[Resultados]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
			# Opcoes Adicionais
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('65%', 'left', $corFundo, 5, 'tabfundo1');
					novaTabelaSH("center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
						menuOpcAdicional('ocorrencias', '', 'listar', $matriz[idPessoaTipo]);
					fechaTabela();
				htmlFechaColuna();
			fechaLinhaTabela();
			
			if(!$consulta || contaConsulta($consulta)==0 ) {
				# Não há registros
				itemTabelaNOURL('Não foram encontrados ocorrências cadastrados', 'left', $corFundo, 5, 'txtaviso');
			}
			elseif($consulta && contaConsulta($consulta)>0 && (!$registro || is_numeric($registro)) ) {	
				
				if($acao=='procurar') {
					itemTabelaNOURL('Ocorrências encontrados procurando por ('.$matriz[txtProcurar].'): '.contaConsulta($consulta).' registro(s)', 'left', $corFundo, 5, 'txtaviso');
				}
	
				# Paginador
				$urlADD="&textoProcurar=$matriz[txtProcurar]";
				//paginador($consulta, contaConsulta($consulta), $limite[lista][ocorrencias], $registro, 'normal', 5, $urlADD);
				
				# Cabeçalho
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela('Ocorrência', 'center', '30%', 'tabfundo0');
					itemLinhaTabela('Prioridade', 'center', '10%', 'tabfundo0');
					itemLinhaTabela('Status', 'center', '10%', 'tabfundo0');
					itemLinhaTabela('Data', 'center', '10%', 'tabfundo0');
					itemLinhaTabela('Opções', 'center', '40%', 'tabfundo0');
				fechaLinhaTabela();
	
				/*# Setar registro inicial
				if(!$registro) {
					$i=0;
				}
				elseif($registro && is_numeric($registro) ) {
					$i=$registro;
				}
				else {
					$i=0;
				}*/
	
				//$limite=$i+$limite[lista][ocorrencias];
				
				//while($i < contaConsulta($consulta) && $i < $limite) {
				for($i=0;$i<contaConsulta($consulta);$i++) {
					# Mostrar registro
					$id=resultadoSQL($consulta, $i, 'id');
					$nome=resultadoSQL($consulta, $i, 'nome');
					$data=resultadoSQL($consulta, $i, 'data');
					$status=resultadoSQL($consulta, $i, 'status');
					$idPrioridade=resultadoSQL($consulta, $i, 'idPrioridade');
					
					$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
					$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
					
					# Verificar status
					if($status=='N') $opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=abrir&registro=$id>Abrir</a>",'abrir');
					else {
						if($status=='A' || $status=='R') {
							$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=fechar&registro=$id>Fechar</a>",'fechar');
							$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=cancelar&registro=$id>Cancelar</a>",'cancelar');
							$opcoes.="<br>";
							$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=comentar&registro=$id>Comentar</a>",'comentar');
						}
						elseif($status=='F') $opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=reabrir&registro=$id>Re-Abrir</a>",'abrir');
						elseif($status=='P') $opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=Fechar&registro=$id>Fechar</a>",'fechar');
					}
					$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=historico&registro=$id>Historico</a>",'historico');
					
					$prioridade=formSelectPrioridade($idPrioridade,'','check');
					
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTabela($nome, 'left', '40%', 'normal10');
						itemLinhaTabela("<font color=$prioridade[cor]>$prioridade[nome]</font>", 'center', '10%', 'bold10');
						itemLinhaTabela(formSelectStatusOcorrencia($status,'','check'), 'center', '10%', 'normal8');
						itemLinhaTabela(converteData($data,'banco','form'), 'center', '10%', 'normal8');
						itemLinhaTabela($opcoes, 'left nowrap', '30%', 'normal8');
					fechaLinhaTabela();
					
				} #fecha laco de montagem de tabela
			} #fecha listagem
				
			fechaTabela();
		htmlFechaColuna();
	htmlFechaLinha();	
}


?>