<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 11/01/2005
# Ultima alteração: 11/01/2005
#    Alteração No.: 001
#
# Função:
#    Funções para manipulação de banco de dados do Ticket-IT

# função de busca 
function buscaTickets($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[Tickets] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[Tickets] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[Tickets] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[Tickets] WHERE $texto ORDER BY $ordem";
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


# Função para gravação em banco de dados
function dbTicket($matriz, $tipo) {
	
	global $conn, $tb, $modulo, $sub, $acao;
	
	$data=dataSistema();
	
	# Sql de inclusão
	if($tipo=='incluir') {
		$sql="INSERT INTO $tb[Tickets] VALUES (0,
		'$matriz[maquina]',
		'$matriz[empresa]',
		'$matriz[idTicket]',
		'$matriz[titulo]',
		'$matriz[data]'
		)";
	} #fecha inclusao
	
	elseif($tipo=='excluir') {
		$sql="DELETE FROM $tb[Tickets] WHERE id=$matriz[id]";
	}
	
	# Alterar
	elseif($tipo=='alterar') {
		# Verificar se prioridade existe
		$sql="
			UPDATE 
				$tb[Tickets] 
			SET 
				titulo='$matriz[titulo]',
				data='$matriz[data]'
			WHERE 
				id=$matriz[id]";
	}
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
	
} # fecha função de gravação em banco de dados



# Função para listagem 
function procurarTickets($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite;
	
	echo "<br>";
	verMaquina($modulo, $sub, $acao, $registro, $matriz);
	echo "<br>";
	
	# Atribuir valores a variável de busca
	if($textoProcurar) {
		$matriz[bntProcurar]=1;
		$matriz[txtProcurar]=$textoProcurar;
	} #fim da atribuicao de variaveis
	
	# Motrar tabela de busca
	novaTabela2("[Tickets]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
		novaLinhaTabela($corFundo, '100%');
			$texto="
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=procurar>
			<input type=hidden name=nulo value=nulo>
			<b>Procurar por:</b>&nbsp;<input type=text name=matriz[txtProcurar] size=30 value='$matriz[txtProcurar]'>
			<b>Data Inicial:</b>&nbsp;<input type=text name=matriz[dtInicial] size=10 value='$matriz[dtInicial]' onBlur=javascript:verificaData(this.value,9);>
			<b>Data Final:</b>&nbsp;<input type=text name=matriz[dtFinal] size=10 value='$matriz[dtFinal]' onBlur=javascript:verificaData(this.value,10);>
			<input type=submit name=matriz[bntProcurar] value=Procurar class=submit>
			";
			itemLinhaForm($texto, 'center','middle', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
	fechaTabela();

	# Caso botão procurar seja pressionado
	if( $matriz[txtProcurar] || $acao=='listar' || $acao=='procurar') {
		#buscar registros
		if(!$matriz[txtProcurar] && strlen(trim($matriz[txtProcurar]))==0 && !$matriz[dtInicial] && !$matriz[dtFinal]) {

			$consulta=buscaTickets($registro,'idMaquina','igual','data DESC');
			
		}
		else {
			$sqlCustom="idMaquina = '$registro'";
			
			if($matriz[txtProcurar]) $sqlCustomTXT="AND titulo like '%$matriz[txtProcurar]%'";
			if($matriz[dtInicial]) {
				$dtInicial=converteData($matriz[dtInicial],'form','bancodata');
				$sqlCustomData="AND data >= '$dtInicial 00:00:00'";
			}
			if($matriz[dtFinal]) {
				$dtFinal=converteData($matriz[dtFinal],'form','bancodata');
				$sqlCustomData="AND data <= '$dtFinal 23:59:59'";
			}
			
			if($matriz[dtInicial] && $matriz[dtFinal]) $sqlCustomData="AND data BETWEEN '$dtInicial 00:00:00' AND '$dtFinal 23:59:59'";
			
			$consulta=buscaTickets("$sqlCustom $sqlCustomTXT $sqlCustomData", '', 'custom', 'data DESC');
		}
		
		echo "<br>";

		novaTabela("[Resultados]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
		itemTabelaNOURL(htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar&registro=$registro>Adicionar</a>",'incluir'), 'right', $corFundo, 3, 'normal10');
	
		if(!$consulta || contaConsulta($consulta)==0 ) {
			# Não há registros
			itemTabelaNOURL('Não foram encontrados registros cadastrados', 'left', $corFundo, 3, 'txtaviso');
		}
		elseif($consulta && contaConsulta($consulta)>0 && (!$registro || is_numeric($registro)) ) {	

			if($matriz[txtProcurar]) itemTabelaNOURL('Tickets encontrados procurando por ('.$matriz[txtProcurar].'): '.contaConsulta($consulta).' registro(s)', 'left', $corFundo, 3, 'txtaviso');

			# Paginador
			$matriz[registro]=$registro;
			$urlADD="&textoProcurar=".$matriz[txtProcurar];
			# Paginador
			paginador2($consulta, contaConsulta($consulta), $limite[lista][tickets], $matriz, 'normal10', 3, $urlADD);
		
			# Cabeçalho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Título', 'center', '50%', 'tabfundo0');
				itemLinhaTabela('Data', 'center', '20%', 'tabfundo0');
				itemLinhaTabela('Opções', 'center', '30%', 'tabfundo0');
			fechaLinhaTabela();

			# Setar registro inicial
			if(!$matriz[pagina]) {
				$i=0;
			}
			elseif($matriz[pagina] && is_numeric($matriz[pagina]) ) {
				$i=$matriz[pagina];
			}
			else {
				$i=0;
			}

			$limite=$i+$limite[lista][tickets];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$idTicket=resultadoSQL($consulta, $i, 'idTicket');
				$titulo=resultadoSQL($consulta, $i, 'titulo');
				$data=converteData(resultadoSQL($consulta, $i, 'data'),'banco','formdata');
				
				$parametros=carregaParametros();
				
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				$opcoes.=htmlMontaOpcao("<a href=$parametros[ticket_url]/?modulo=ticket&acao=ver&registro=$idTicket target=_BLANK>Ver Ticket</a>",'ticket');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($titulo, 'left', '50%', 'normal10');
					itemLinhaTabela($data, 'center', '20%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '30%', 'normal8');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem
	} # fecha botão procurar
		
	
}#fecha função de listagem


# Funcao para cadastro de servicos
function adicionarTickets($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin;
	
	echo "<br>";
	
	# Form de inclusao
	if(!$matriz[bntAdicionar] || !$matriz[maquina] || !$matriz[idTicket] || !$matriz[titulo]) {

		# Seleção de registros
		$consulta=buscaMaquinas($registro, 'id', 'igual', 'id');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Servidor não encontrado
			itemTabelaNOURL('Máquina não encontrada!', 'left', $corFundo, 3, 'txtaviso');
		}
		else {
		
			# Mostrar Informações sobre Servidor
			verMaquina($modulo, $sub, $acao, $registro, $matriz);
			echo "<br>";
	
			# Motrar tabela de busca
			novaTabela2("[Adicionar Ticket]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=registro value=$registro>
					<input type=hidden name=matriz[maquina] value=$registro>
					<input type=hidden name=acao value=$acao>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Procurar por título ou protocolo: </b><br>";
					htmlFechaColuna();
					$texto="<input type=text size=60 name=matriz[txtProcurar] value='$matriz[txtProcurar]'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				if(!$matriz[txtProcurar]) {
					novaLinhaTabela($corFundo, '100%');
						$texto="<input type=submit name=matriz[bntProcurar] value=Procurar class=submit>";
						itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
					fechaLinhaTabela();
				}
				elseif($matriz[txtProcurar] || $matriz[idTicket]) {
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b>Ticket Encontrados: </b><br>";
						htmlFechaColuna();
						itemLinhaForm(formSelectTicket($registro, $matriz[idTicket], $matriz[txtProcurar], 'idTicket','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					
					if(!$matriz[idTicket]) {
						novaLinhaTabela($corFundo, '100%');
							$texto="<input type=submit name=matriz[bntSelecionar] value=Selecionar class=submit>";
							itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
						fechaLinhaTabela();
					}
				}
				if($matriz[idTicket]) {
					
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b>Título do Ticket: </b><br>";
						htmlFechaColuna();
						$dadosTicket=formSelectTicket($registro, $matriz[idTicket],'', '','check');
						$matriz[titulo]=$dadosTicket[titulo];
						$matriz[data]=converteData($dadosTicket[data],'banco','formdata');
						$texto="<input type=text size=60 name=matriz[titulo] value='$matriz[titulo]'>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Data: </b>";
						htmlFechaColuna();
						$texto="<input type=text name=matriz[data] size=10 value='$matriz[data]' onBlur=javascript:verificaData(this.value,13);>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();				
					novaLinhaTabela($corFundo, '100%');
						$texto="<input type=submit name=matriz[bntAdicionar] value=Adicionar class=submit>";
						itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
					fechaLinhaTabela();
				}
			fechaTabela();
		} # fecha servidor informado para cadastro
	}
	elseif($matriz[bntAdicionar]) {
		# Cadastrar em banco de dados
		$matriz[data]=converteData($matriz[data],'form','banco');
		$dadosMaquina=dadosMaquinas($matriz[maquina]);
		$matriz[empresa]=$dadosMaquina[idEmpresa];
		
		$grava=dbTicket($matriz, 'incluir');
			
		# Verificar inclusão de registro
		if($grava) {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Registro Gravado com Sucesso!";
			avisoNOURL("Aviso", $msg, 400);
			
			procurarTickets($modulo, $sub, 'listar', $matriz[maquina], '');
		}
	}

} # fecha funcao de inclusao de servicos


# Funcao para exclusão de servicos
function excluirTickets($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	$consulta=buscaTickets($registro, 'id', 'igual', 'id');
	
	echo "<br>";

	if($consulta && contaConsulta($consulta)>0) {
		
		$idMaquina=resultadoSQL($consulta, 0, 'idMaquina');
		
		
		# Form de exclusão
		if(!$matriz[bntExcluir]) {
	
			# Visualizar Maquina
			verMaquina($modulo, $sub, $acao, $idMaquina, '');
			echo "<br>";
	
			$id=resultadoSQL($consulta, 0, 'id');
			$idMaquina=resultadoSQL($consulta, 0, 'idMaquina');
			$idTicket=resultadoSQL($consulta, 0, 'idTicket');
			$titulo=resultadoSQL($consulta, 0, 'titulo');
			$data=converteData(resultadoSQL($consulta, 0, 'data'),'banco','formdata');
	
			# Motrar tabela de busca
			novaTabela2("[Excluir Ticket]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				//menuOpcAdicional($modulo, $sub, $acao, $registro);				
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro>
					<input type=hidden name=matriz[idMaquina] value=$idMaquina>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Título: </b>";
					htmlFechaColuna();
					itemLinhaForm($titulo, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Ticket: </b>";
					htmlFechaColuna();
					$dadosTicket=formSelectTicket($idMaquina, $idTicket, '', '','check');
					itemLinhaForm($dadosTicket[titulo], 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Data: </b>";
					htmlFechaColuna();
					itemLinhaForm($data, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit name=matriz[bntExcluir] value=Excluir class=submit>";
					itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		} #fecha form
		elseif($matriz[bntExcluir]) {
			# Cadastrar em banco de dados
			$matriz[id]=$registro;
			$grava=dbTicket($matriz, 'excluir');
				
			# Verificar inclusão de registro
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Excluído com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);
			}
			
			procurarTickets($modulo, $sub, 'listar', $matriz[idMaquina], '');
		}
	}
	# Programa não encontrado
	else {
		# Mensagem de aviso
		$msg="Ticket não encontrado!";
		avisoNOURL("Aviso: Ocorrência de erro", $msg, 400);
	}
} # fecha funcao de exclusão




# Funcao para exclusão de servicos
function alterarTickets($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	$consulta=buscaTickets($registro, 'id', 'igual', 'id');
	
	echo "<br>";
	
	if($consulta && contaConsulta($consulta)>0) {
		
		$idMaquina=resultadoSQL($consulta, 0, 'idMaquina');

		if(!$matriz[bntAlterar] || !$matriz[titulo] || !$matriz[data]) {
	
			# Visualizar Maquina
			verMaquina($modulo, $sub, $acao, $idMaquina, '');
			echo "<br>";
	
			$id=resultadoSQL($consulta, 0, 'id');
			$idMaquina=resultadoSQL($consulta, 0, 'idMaquina');
			$titulo=resultadoSQL($consulta, 0, 'titulo');
			$idTicket=resultadoSQL($consulta, 0, 'idTicket');
			$data=resultadoSQL($consulta, 0, 'data');
			
			if(!$matriz[bntAlterar]) {
				$matriz[titulo]=$titulo;
				$matriz[txtProcurar]=$titulo;
				$matriz[idTicket]=$idTicket;
				$matriz[data]=$data;
			}
	
			# Motrar tabela de busca
			novaTabela2("[Alterar Ticket]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=registro value=$registro>
					<input type=hidden name=matriz[idMaquina] value=$idMaquina>
					<input type=hidden name=acao value=$acao>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Título do Ticket: </b><br>";
					htmlFechaColuna();
					$dadosTicket=formSelectTicket($registro, $matriz[idTicket],'', '','check');
					$matriz[titulo]=$dadosTicket[titulo];
					$matriz[data]=converteData($dadosTicket[data],'banco','formdata');
					$texto="<input type=text size=60 name=matriz[titulo] value='$matriz[titulo]'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Ticket: </b>";
					htmlFechaColuna();
					$dadosTicket=formSelectTicket($idMaquina, $idTicket, '', '','check');
					itemLinhaForm($dadosTicket[titulo], 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();				
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Data: </b>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[data] size=10 value='$matriz[data]' onBlur=javascript:verificaData(this.value,10);>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();				
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit name=matriz[bntAlterar] value=Alterar class=submit>";
					itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		} #fecha form
		elseif($matriz[bntAlterar]) {
			# Cadastrar em banco de dados
			$matriz[id]=$registro;
			$matriz[data]=converteData($matriz[data],'form','bancodata');
			$grava=dbTicket($matriz, 'alterar');
			
			# Verificar inclusão de registro
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Alterado com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);
			}
			
			procurarTickets($modulo, $sub, 'listar', $matriz[idMaquina], '');
		}
	}
	# Ticket não encontrado
	else {
		# Mensagem de aviso
		$msg="Ticket não encontrado!";
		avisoNOURL("Aviso: Ocorrência de erro", $msg, 400);
	}
} # fecha funcao de exclusão



?>