<?
################################################################################
#       Criado por: Hugo Ribeiro
#  Data de criação: 19/04/2004
# Ultima alteração: 04/05/2004
#    Alteração No.: 004
#
# Função:
#    Painel - Funções para gerenciamento de ordem de servicos

# Cadastro
//function ordemdeservico($modulo, $sub, $acao, $registro, $matriz) {
//
//	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessCadastro, $titulo;
//	
//	$titulo = "Ordem de Serviço";
//	
//	# Permissão do usuario
//	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
//	
//	if(!$permissao[admin] && !$permissao[adicionar]) {
//		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
//		$msg="ATENÇÃO: Você não tem permissão para executar esta função!";
//		$url="?modulo=$modulo&sub=$sub";
//		aviso("Acesso Negado", $msg, $url, 760);
//	}
//	else {
//		# Topo da tabela - Informações e menu principal do Cadastro
//		novaTabela2("[$titulo]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 4);
//			novaLinhaTabela($corFundo, '100%');
//				htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
//					echo "<br><img src=".$html[imagem][cadastro]." border=0 align=left><b class=bold>$tipoPessoa[descricao]</b>
//					<br><span class=normal10>$titulo</b>.</span>";
//				htmlFechaColuna();			
//				$texto=htmlMontaOpcao("<br>Adicionar", 'incluir');
//				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=adicionar", 'center', $corFundo, 0, 'normal');
//				$texto=htmlMontaOpcao("<br>Procurar", 'procurar');
//				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=procurar", 'center', $corFundo, 0, 'normal');
//				$texto=htmlMontaOpcao("<br>Listar", 'listar');
//				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=listarTodos", 'center', $corFundo, 0, 'normal');
//			fechaLinhaTabela();
//		fechaTabela();
//		
//		if($acao=="adicionar") {
//			ordemdeservicoProcurar($modulo, $sub, $acao, $registro, $matriz);
//			if ($matriz[bntAdicionar] || ($matriz[idPessoaTipo] && $matriz[bntConfirmar]) )
//				ordemdeservicoAdicionar($modulo, $sub, $acao, $registro, $matriz);
//		}
//		elseif($acao=="alterar") {
//			echo "<br>";
//			ordemdeservicoAlterar($modulo, $sub, $acao, $registro, $matriz);
//		}
//		elseif($acao=='procurar') {
//			ordemdeservicoProcurar($modulo, $sub, $acao, $registro, $matriz);
//		}
//		elseif($acao=='ver') {
//			echo "<br>";
//			ordemdeservicoVer($modulo, $sub, $acao, $registro, $matriz);
//		}
//		elseif($acao=='excluir') {
//			echo "<br>";
//			ordemdeservicoExcluir($modulo, $sub, $acao, $registro, $matriz);
//		}
//		elseif($acao=='fechar') {
//			echo "<br>";
//			ordemdeservicoFechar($modulo, $sub, $acao, $registro, $matriz);
//		}
//		elseif($acao=='imprimir') {
//			echo "<br>";
//			relatorioOrdemServico($modulo, 'ordemservico', $acao, $registro, $matriz);
//		}
//		elseif(substr($acao, 0, 6)=='listar') {
//			echo "<br>";
//			ordemdeservicoListar($modulo, $sub, $acao, $registro, $matriz);
//		}
//		elseif($acao=='detalhar') {
//			echo "<br>";
//			ordemdeservicodetalheDetalhar($modulo, 'ordemservicodetalhe', $acao, $registro, $matriz);
//		}
//		if($acao=="adicionarDetalhe") {
//			ordemdeservicodetalheAdicionar($modulo, 'ordemservicodetalhe', $acao, $registro, $matriz);
//		}
//		elseif($acao=="alterarDetalhe") {
//			echo "<br>";
//			ordemdeservicodetalheAlterar($modulo, 'ordemservicodetalhe', $acao, $registro, $matriz);
//		}
//		elseif($acao=='excluirDetalhe') {
//			echo "<br>";
//			ordemdeservicodetalheExcluir($modulo, 'ordemservicodetalhe', $acao, $registro, $matriz);
//		}
//
//	}
//	echo "<script>location.href='#ancora';</script>";
//}
//
//function ordemdeservicoExcluir($modulo, $sub, $acao, $registro, $matriz) {
//	$msg="Esta opção não está habilitada";
//	avisoNOURL("Aviso: Exclusão", $msg, 400);
//	echo "<br>";
//	ordemdeservicoVer($modulo, $sub, 'ver', $registro, $matriz);
//}
//
//
//function ordemdeservicoProcurar($modulo, $sub, $acao, $registro, $matriz) {
//	
//	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessCadastro, $titulo;
//	
//	if( ( ($acao=='procurar') || ($acao=='adicionar') ) && (!$matriz[bntConfirmar]) ) {
//		echo "<br>";
//		novaTabela2("[$acao - Procurar Cliente]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
//			# Opcoes Adicionais
//			//menuOpcAdicional($modulo, $sub, $acao, $registro);
//			#fim das opcoes adicionais
//			novaLinhaTabela($corFundo, '100%');
//			$texto="			
//				<form method=post name=matriz action=index.php>
//				<input type=hidden name=modulo value=$modulo>
//				<input type=hidden name=sub value=$sub>
//				<input type=hidden name=acao value=$acao>
//				<input type=hidden name=registro>";
//				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
//			fechaLinhaTabela();
//			itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
//			
//			novaLinhaTabela($corFundo, '100%');
//				itemLinhaTMNOURL('<b>Busca por Cliente:</b><br><span class=normal10>Informe nome ou dados do cliente para busca</span>', 'right', 'middle', '35%', $corFundo, 0, 'tabfundo1');
//				$texto="<input type=text name=matriz[txtProcurar] size=60 value='$matriz[txtProcurar]'><br><input type=submit value=Procurar name=matriz[bntProcurar] class=submit>";
//				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
//			fechaLinhaTabela();
//			
//			if($matriz[txtProcurar]) {
//				# Procurar Cliente
//				$tipoPessoa=checkTipoPessoa('cli');
//				$consulta=buscaPessoas("
//					((upper(nome) like '%$matriz[txtProcurar]%' 
//						OR upper(razao) like '%$matriz[txtProcurar]%' 
//						OR upper(site) like '%$matriz[txtProcurar]%' 
//						OR upper(mail) like '%$matriz[txtProcurar]%')) 
//					AND idTipo=$tipoPessoa[id]", $campo, 'custom','nome');
//				
//				if($consulta && contaConsulta($consulta)>0) {
//					# Selecionar cliente
//					novaLinhaTabela($corFundo, '100%');
//						itemLinhaTMNOURL('<b>Clientes encontrados:</b><br>
//						<span class=normal10>Selecione o Cliente</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
//						itemLinhaForm(formSelectConsulta($consulta, 'nome', 'idPessoaTipo', 'idPessoaTipo', $matriz[idPessoaTipo]), 'left', 'top', $corFundo, 0, 'tabfundo1');
//					fechaLinhaTabela();
//					itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
//					novaLinhaTabela($corFundo, '100%');
//						$texto="<input type=submit name=matriz[bntConfirmar] value='Selecionar' class=submit>";
//						itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
//					fechaLinhaTabela();
//				}
//			}
//			htmlFechaLinha();
//		fechaTabela();
//	}
//	# realizar consulta
//	elseif( ($matriz[bntConfirmar] || $matriz[idPessoaTipo]) && (! $matriz[bntAdicionar]) ) {
//		# Prosseguir com consulta
//		echo "<br>";
//		$matriz[tipoPessoa]='cli';
//		verPessoas($modulo, $sub, $acao, $matriz[idPessoaTipo], $matriz);
//		if ($acao=='procurar') 
//			ordemdeservicoListar($modulo, $sub, 'listar', $matriz[idPessoaTipo], $matriz);
//	}
//}
//
//# função para adicionar
//function ordemdeservicoAdicionar($modulo, $sub, $acao, $registro, $matriz) {
//
//	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $tb;
//	
//	if($matriz[idPessoaTipo] && !$matriz[bntAdicionar]) {
//		
//		echo "<br>";
//		# Motrar tabela de busca
//		novaTabela2("[Adicionar Ordem de Serviço]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
//			# Opcoes Adicionais
//			menuOpcAdicional($modulo, $sub, $acao, $registro);
//			#fim das opcoes adicionais
//			novaLinhaTabela($corFundo, '100%');
//			$texto="			
//				<form method=post name=matriz action=index.php>
//				<input type=hidden name=modulo value=$modulo>
//				<input type=hidden name=sub value=$sub>
//				<input type=hidden name=acao value=$acao>
//				<input type=hidden name=matriz[bntConfirmar] value=$matriz[bntConfirmar]>
//				<input type=hidden name=matriz[idPessoaTipo] value=$matriz[idPessoaTipo]>&nbsp;";
//				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
//			fechaLinhaTabela();
//			
//			$dados[idServico]=$matriz[idServico];
//			
//			ordemdeservicoMostra($dados);
//			
//			#botao
//			novaLinhaTabela($corFundo, '100%');
//				$texto="<input type=submit name=matriz[bntAdicionar] value='Adicionar' class=submit>";
//				itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
//			fechaLinhaTabela();
//		fechaTabela();	
//	}
//	elseif($matriz[bntAdicionar]) {
//		
//		$matriz[id]=buscaNovoID($tb[OrdemServico]);
//		$grava=dbOrdemdeServico($matriz, 'incluir');
//		
//		# Verificar inclusão de registro
//		echo "<br>";
//		if($grava) {
//			# Visualizar Pessoa
//			$msg="Registro gravado com sucesso!";
//			avisoNOURL("Aviso: ", $msg, 400);
//			echo "<br>";
//			ordemdeservicoVer($modulo, $sub, 'ver', $matriz[id], $matriz);
//		} 
//		else {
//			$msg="Ocorreram erros durante a gravação.";
//			avisoNOURL("Aviso: Ocorrência de erro", $msg, 400);
//		}
//	}
//}
//
//
//
//# Função para alteração de dados da Pessoa - apenas cadastro
//function ordemdeservicoAlterar($modulo, $sub, $acao, $registro, $matriz) {
//
//	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $tb, $sessCadastro;
//	
//	if($registro && !$matriz[bntConfirmar]) {
//	
//		# Buscar Valores
//		$dados=dadosOrdemdeServico($registro, 'id', 'igual', 'id');
//			
//		# Motrar tabela de busca
//		novaTabela2("[Alterar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
//			# Opcoes Adicionais
//			menuOpcAdicional($modulo, $sub, $acao, $registro);
//			#fim das opcoes adicionais
//			novaLinhaTabela($corFundo, '100%');
//			$texto="			
//				<form method=post name=matriz action=index.php>
//				<input type=hidden name=modulo value=$modulo>
//				<input type=hidden name=sub value=$sub>
//				<input type=hidden name=matriz[idPessoaTipo] value=$idPessoaTipo>
//				<input type=hidden name=matriz[id] value=$registro>
//				<input type=hidden name=acao value=$acao>&nbsp;";
//				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
//			fechaLinhaTabela();
//			
//			ordemdeservicoMostra($dados);
//			
//			#botao
//			novaLinhaTabela($corFundo, '100%');
//				$texto="<input type=submit name=matriz[bntConfirmar] value='Alterar' class=submit>";
//				itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
//			fechaLinhaTabela();
//		fechaTabela();
//	} #fecha form
//	
//	# Alteração - bntAlterar pressionado
//	elseif($matriz[bntConfirmar]) {
//		# Conferir campos
//		if($matriz[id]) {
//			# continuar
//			# Cadastrar em banco de dados
//			$grava=dbOrdemdeServico($matriz, 'alterar');
//			# Verificar inclusão de registro
//			if($grava) {
//				# OK
//				# Visualizar Pessoa
//				$msg="Registro alterado com sucesso!";
//				avisoNOURL("Aviso: ", $msg, 400);
//				echo "<br>";
//				ordemdeservicoVer($modulo, $sub, 'ver', "$matriz[id]", $matriz);
//			} else {
//				echo "<br>";
//				$msg="Ocorreram erros durante a gravação.";
//				avisoNOURL("Aviso: Ocorrência de erro", $msg, 400);
//			}
//		}
//		# falta de parametros
//		else {
//			# acusar falta de parametros
//			# Mensagem de aviso
//			$msg="Falta de parâmetros necessários. Informe os campos obrigatórios e tente novamente";
//			$url="?modulo=$modulo&sub=$sub&acao=$acao";
//			aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
//		}
//	} #fecha bntAlterar
//}
//
//
//function ordemdeservicoMostra($dados) {
//	# Servico
//	novaLinhaTabela($corFundo, '100%');
//		htmlAbreColuna('30%', 'right', $corFundo, 0, 'tabfundo1');
//			echo "<b>Serviço: </b>";
//		htmlFechaColuna();
//		$texto=formSelectServicos($dados[idServico],'idServico', 'form');
//		itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
//	fechaLinhaTabela();
//	#nome
//	novaLinhaTabela($corFundo, '100%');
//		itemLinhaTMNOURL("<b>Título: </b>", 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
//		$texto="<input type=text name=matriz[nome] size=60 value='$dados[nome]'>";
//		itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
//	fechaLinhaTabela();
//	#descricao
//	novaLinhaTabela($corFundo, '100%');
//		itemLinhaTMNOURL('<b>Descrição: </b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
//		$texto="<input type=text name=matriz[descricao] size=40 value='$dados[descricao]'>";
//		itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
//	fechaLinhaTabela();
//	# Valor
//	novaLinhaTabela($corFundo, '100%');
//		itemLinhaTMNOURL('<b>Valor: </b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
//		$texto="<input type=text name=matriz[valor] size=40 value='$dados[valor]' onBlur=formataValor(this.value,8)>";
//		itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
//	fechaLinhaTabela();
//	#Prioridade
//	novaLinhaTabela($corFundo, '100%');
//		itemLinhaTMNOURL('<b>Prioridade: </b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
//		$texto=formSelectPrioridade($dados[prioridade], 'prioridade', 'form');
//		itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
//	fechaLinhaTabela();
//}
//
//
//# Função para buscar o NOVO ID da Pessoa
//function ordemdeservicoBuscaIDNovo() {
//
//	global $conn, $tb;
//	
//	$sql="SELECT count(id) qtde from $tb[OrdemServico]";
//	$consulta=consultaSQL($sql, $conn);
//	
//	if($consulta && resultadoSQL($consulta, 0, 'qtde')>0) {
//	
//		$sql="SELECT MAX(id)+1 id from $tb[OrdemServico]";
//		$consulta=consultaSQL($sql, $conn);
//		
//		if($consulta && contaConsulta($consulta)>0) {
//			$retorno=resultadoSQL($consulta, 0, 'id');
//			if(!is_numeric($retorno)) $retorno=1;
//		}
//		else $retorno=1;
//	}
//	else {
//		$retorno=resultadoSQL($consulta, 0, 'qtde')+1;
//	}
//	return($retorno);
//}
//
//
//
//# Função de banco de dados - Pessoas
//function dbOrdemdeServico($matriz, $tipo) {
//
//	global $sessLogin, $sessCadastro, $conn, $tb, $modulo, $sub, $acao;
//	
//	$matriz[idUsuario]=buscaIDUsuario($sessLogin[login], 'login', 'igual', 'id');
//	$valor=formatarValores($matriz[valor]);
//	/* Cria uma matriz com os campos ja formatados para o SQL */
//	$campos[id]="id=$matriz[id]";
//	$campos[idPessoaTipo]="idPessoaTipo=$matriz[idPessoaTipo]";
//	$campos[nome]="nome='$matriz[nome]'";
//	$campos[descricao]="descricao='$matriz[descricao]'";
//	$campos[valor]="valor=$valor";
//	$campos[idPrioridade]="idPrioridade=$matriz[prioridade]";
//	$campos[idUsuario]="idUsuario=$matriz[idUsuario]";
//	$campos[idServico]="idServico=$matriz[idServico]";
//	$campos[status]="status='$matriz[status]'";
//	
//	# Sql de inclusão
//	if($tipo=='incluir') {
//		/*
//		+--------------+--------------+------+-----+---------+----------------+
//		| id           | int(11)      |      | PRI | NULL    | auto_increment |
//		| idPessoaTipo | int(11)      | YES  |     | NULL    |                |
//		| idServico    | int(11)      | YES  |     | NULL    |                |
//		| idUsuario    | int(11)      | YES  |     | NULL    |                |
//		| idPrioridade | int(11)      | YES  |     | NULL    |                |
//		| dtCriacao    | datetime     | YES  |     | NULL    |                |
//		| dtExecusao   | datetime     | YES  |     | NULL    |                |
//		| dtConclusao  | datetime     | YES  |     | NULL    |                |
//		| nome         | varchar(200) | YES  |     | NULL    |                |
//		| descricao    | text         | YES  |     | NULL    |                |
//		| status       | char(1)      | YES  |     | NULL    |                |
//		| valor        | float(12,2)  | YES  |     | NULL    |                |
//		+--------------+--------------+------+-----+---------+----------------+
//		*/
//		$campos[id]='id='.buscaNovoID($tb[OrdemServico]);
//		
//		$sql="INSERT INTO   $tb[OrdemServico] 
//		             VALUES (0,
//							'$matriz[idPessoaTipo]',
//							'$matriz[idServico]',
//							'$matriz[idUsuario]',
//							'$matriz[idPrioridade]',
//						    now(),
//							'',
//							'',
//						 	'$matriz[nome]',
//						 	'$matriz[descricao]',
//						 	'A',
//							'$valor'
//						 	)";
//	} #fecha inclusao
//	
//	# Alterar
//	elseif($tipo=='alterar') {
//		
//		$sql="
//			UPDATE $tb[OrdemServico] 
//			SET
//				$campos[nome],
//				$campos[descricao],
//				$campos[valor],
//				$campos[idPrioridade]
//			WHERE
//				$campos[id]";
//	}
//	
//	# fechar
//	elseif($tipo=='fechar') {
//		
//		$sql="
//			UPDATE $tb[OrdemServico] 
//			SET
//				$campos[status]
//			WHERE
//				$campos[id]";
//	}
//	
//	elseif($tipo=='excluir') {
//		$sql="DELETE FROM $tb[OrdemServico] WHERE $campos[id]";
//	}
//	
//	//echo "SQL: $sql";
//	
//	if($sql) { 
//		$retorno=consultaSQL($sql, $conn);
//		return($retorno); 
//	}
//	
//}
//
//
//# função Exibição
//function ordemdeservicoVer($modulo, $sub, $acao, $registro, $matriz) {
//
//	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb, $titulo;
//
//	# Permissão do usuario
//	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
//	
//	if(!$permissao[admin] && !$permissao[visualizar]) {
//		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
//		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
//		$url="?modulo=$modulo&sub=$sub";
//		aviso("Acesso Negado", $msg, $url, 760);
//	}
//	else {
//		
//		# Procurar dados3
//		$objeto=dadosOrdemdeServico($registro);
//		
//		if(is_array($objeto)) {
//
//			# Motrar tabela de busca
//			novaTabela2("[$titulo - Visualização]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
//				
//				# Opcoes Adicionais
//				menuOpcAdicional($modulo, $sub, 'ver', $registro);
//				
//				$bgLabel='tabfundo1';
//				$bgCampo='tabfundo1';
//				
//				itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
//				
//				#id
//				novaLinhaTabela($corFundo, '100%');
//					itemLinhaTMNOURL('<b>ID: </b>', 'right', 'middle', '30%', $corFundo, 0, $bgLabel);
//					itemLinhaTMNOURL($objeto[id], 'left', 'middle', '30%', $corFundo, 0, $bgCampo);
//				fechaLinhaTabela();
//				#idPessoaTipo
//				novaLinhaTabela($corFundo, '100%');
//					itemLinhaTMNOURL('<b>Cliente: </b>', 'right', 'middle', '30%', $corFundo, 0, $bgLabel);
//					itemLinhaTMNOURL($objeto[nomeCliente], 'left', 'middle', '30%', $corFundo, 0, $bgCampo);
//				fechaLinhaTabela();
//				
//				#idServico
//				novaLinhaTabela($corFundo, '100%');
//					itemLinhaTMNOURL('<b>Serviço: </b>', 'right', 'middle', '30%', $corFundo, 0, $bgLabel);
//					itemLinhaTMNOURL($objeto[nomeServico], 'left', 'middle', '30%', $corFundo, 0, $bgCampo);
//				fechaLinhaTabela();
//				#nome
//				novaLinhaTabela($corFundo, '100%');
//					itemLinhaTMNOURL('<b>Titulo: </b>', 'right', 'middle', '30%', $corFundo, 0, $bgLabel);
//					itemLinhaTMNOURL($objeto[nome], 'left', 'middle', '30%', $corFundo, 0, $bgCampo);
//				fechaLinhaTabela();
//				#descricao
//				novaLinhaTabela($corFundo, '100%');
//					itemLinhaTMNOURL('<b>Descrição: </b>', 'right', 'middle', '30%', $corFundo, 0, $bgLabel);
//					itemLinhaTMNOURL($objeto[descricao], 'left', 'middle', '30%', $corFundo, 0, $bgCampo);
//				fechaLinhaTabela();
//				#valor
//				novaLinhaTabela($corFundo, '100%');
//					itemLinhaTMNOURL('<b>Valor: </b>', 'right', 'middle', '30%', $corFundo, 0, $bgLabel);
//					itemLinhaTMNOURL(formatarValoresForm($objeto[valor]), 'left', 'middle', '30%', $corFundo, 0, $bgCampo);
//				fechaLinhaTabela();
//				#status
//				novaLinhaTabela($corFundo, '100%');
//					itemLinhaTMNOURL('<b>Status: </b>', 'right', 'middle', '30%', $corFundo, 0, $bgLabel);
//					itemLinhaTMNOURL(formSelectStatus($objeto[status],'','check'), 'left', 'middle', '30%', $corFundo, 0, $bgCampo);
//				fechaLinhaTabela();
//		
//				#Usuario
//				novaLinhaTabela($corFundo, '100%');
//					itemLinhaTMNOURL('<b>Usuário: </b>', 'right', 'middle', '30%', $corFundo, 0, $bgLabel);
//					itemLinhaTMNOURL($objeto[login], 'left', 'middle', '30%', $corFundo, 0, $bgCampo);
//				fechaLinhaTabela();
//				
//				#Prioridade
//				novaLinhaTabela($corFundo, '100%');
//					itemLinhaTMNOURL('<b>Prioridade: </b>', 'right', 'middle', '30%', $corFundo, 0, $bgLabel);
//					$prioridade=formSelectPrioridade($objeto[idPrioridade],'','check');
//					itemLinhaTMNOURL($prioridade[nomeFormatado], 'left', 'middle', '30%', $corFundo, 0, $bgCampo);
//				fechaLinhaTabela();
//				
//				#dtCriacao
//				if ($objeto[dtCriacao]) {
//					novaLinhaTabela($corFundo, '100%');
//						itemLinhaTMNOURL('<b>Data da Criação: </b>', 'right', 'middle', '30%', $corFundo, 0, $bgLabel);
//						itemLinhaTMNOURL($objeto[dtCriacao], 'left', 'middle', '30%', $corFundo, 0, $bgCampo);
//					fechaLinhaTabela();
//				}
//				#dtExecusao
//				if ($objeto[dtExecusao]) {
//					novaLinhaTabela($corFundo, '100%');
//						itemLinhaTMNOURL('<b>Data da Execução: </b>', 'right', 'middle', '30%', $corFundo, 0, $bgLabel);
//						itemLinhaTMNOURL($objeto[dtExecusao], 'left', 'middle', '30%', $corFundo, 0, $bgCampo);
//					fechaLinhaTabela();
//				}
//				
//				#dtConclusao
//				if ($objeto[dtConclusao]) {
//					novaLinhaTabela($corFundo, '100%');
//						itemLinhaTMNOURL('<b>Data da Conclusão: </b>', 'right', 'middle', '30%', $corFundo, 0, $bgLabel);
//						itemLinhaTMNOURL($objeto[dtConclusao], 'left', 'middle', '30%', $corFundo, 0, $bgCampo);
//					fechaLinhaTabela();
//				}
//			fechaTabela();
//			
//		}
//	}
//}
//
//
//#Lista todas OS do cliente selecionado
//function ordemdeservicoListar($modulo, $sub, $acao, $cliente, $matriz) {
//	
//	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb, $titulo;
//
//	# Permissão do usuario
//	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
//	
//	if(!$permissao[admin] && !$permissao[visualizar]) {
//		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
//		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
//		$url="?modulo=$modulo&sub=$sub";
//		aviso("Acesso Negado", $msg, $url, 760);
//	}
//	else {
//		# Procurar dados
//		if($acao=='listar') {
//			$consulta=buscaRegistros($cliente, 'idPessoaTipo', 'igual','id', $tb[OrdemServico]);
//		}
//		elseif($acao=='listarTodos') {
//			$consulta=buscaRegistros('', 'id', 'todos','id', $tb[OrdemServico]);
//		}
//		elseif($acao=='listarAtivas') {
//			if($cliente) 
//				$consulta=buscaRegistros("status='A' and idPessoaTipo=$cliente", 'id', 'custom','id', $tb[OrdemServico]);
//			else
//				$consulta=buscaRegistros('A', 'status', 'igual','id', $tb[OrdemServico]);
//		}
//		elseif($acao=='listarInativas') {
//			if($cliente) 
//				$consulta=buscaRegistros("status='F' and idPessoaTipo=$cliente", 'id', 'custom','id', $tb[OrdemServico]);
//			else 
//				$consulta=buscaRegistros('F', 'status', 'igual','id', $tb[OrdemServico]);
//		}
//		
//		# Cabeçalho
//		echo "<br>";
//		novaTabela("Lista de OS", "left", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
//			# Opcoes Adicionais
//			menuOpcAdicional($modulo, $sub, $acao, $cliente);
//		fechaTabela();
//		novaTabelaSH("left", '100%', 0, 2, 1, $corFundo, $corBorda, 7);
//		
//		if ($consulta && contaConsulta($consulta)>0) {
//			
//			$largura             =array('1%',     '3%',     '25%',    '28%',       '10%',   '3%',     '30%');
//			$gravata[cabecalho]  =array('&nbsp;', 'OS',     'Titulo', 'Descrição', 'Valor', 'Status', 'Opções');
//			$gravata[alinhamento]=array('center', 'center', 'left',   'left',      'right', 'center', 'left');
//			
//			$cor='tabfundo0';
//			htmlAbreLinha($corFundo);
//				for($i=0;$i<count($largura); $i++)
//					itemLinhaTMNOURL($gravata[cabecalho][$i], $gravata[alinhamento][$i], 'middle', $largura[$i], $corFundo, 0, $cor);
//			htmlFechaLinha();
//			
//			$qtd=contaConsulta($consulta);
//			for($reg=0;$reg<$qtd;$reg++) {
//				
//				$id=resultadoSQL($consulta, $reg, 'id');
//				$propriedade=formSelectPrioridade(resultadoSQL($consulta, $reg, 'idPrioridade'),'','check');
//				
//				$i=0;
//				$campo[$i++]='&nbsp;';
//				$campo[$i++]=$id;
//				$campo[$i++]=resultadoSQL($consulta, $reg, 'nome');
//				$campo[$i++]=resultadoSQL($consulta, $reg, 'descricao');
//				$campo[$i++]=formatarValoresForm(resultadoSQL($consulta, $reg, 'valor'));
//				$campo[$i++]=resultadoSQL($consulta, $reg, 'status');
//				
//				#opcoes
//				$def="<a href=?modulo=$modulo&sub=$sub&registro=$id";
//				$fnt="<font size='2'>";
//				$opcoes =htmlMontaOpcao($def."&acao=detalhar>".$fnt."Detalhar</font></a>",'consultas');
//				$opcoes.=htmlMontaOpcao($def."&acao=alterar>".$fnt."Alterar</font></a>",'alterar');
//				$opcoes.=htmlMontaOpcao($def."&acao=fechar>".$fnt."Fechar</font></a>",'fechar');
//				
//				$cor='normal10';
//				htmlAbreLinha($corFundo);
//					itemLinhaTMNOURL('&nbsp;', 'center bgcolor='.$propriedade[cor], 'middle', $largura[0], $corFundo, 0, $cor);
//					for($i=1;$i<count($largura)-1; $i++)
//						itemLinhaTMNOURL($campo[$i], $gravata[alinhamento][$i].' '.$cor, 'middle', $largura[$i], $corFundo, 0, $cor);
//					itemLinhaTMNOURL($opcoes, 'left', 'middle', $largura[$i], $corFundo, 0, $cor);
//				htmlFechaLinha();
//			}
//		}
//		else {
//			fechaTabela();
//			novaTabelaSH("left", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
//				$cor='normal10';
//				htmlAbreLinha($corFundo);
//					itemLinhaTMNOURL( "Não há registros para sua solicitação.", 'left', 'middle', $largura[0], $corFundo, 0, $cor);
//				htmlFechaLinha();
//		}
//		fechaTabela();
//	}
//
//}
//
//# Função para Dados Pessoas Tipos
///**
// * @return unknown
// * @param unknown $id
// * @desc Retorna um array com todos um objeto completo da ordem de servico.<BR>
//Este array possui um outro com os dados do cliente: Pessoa[]
//*/
//function dadosOrdemdeServico($id) {
//
//	global $tb;
//
//	$consulta=buscaRegistros($id, 'id', 'igual', 'id', $tb[OrdemServico]);
//	
//	if(contaConsulta($consulta)>0) {
//		
//		$retorno[id]=resultadoSQL($consulta, 0, 'id');
//		$retorno[idPessoaTipo]=resultadoSQL($consulta, 0, 'idPessoaTipo');
//		$retorno[idServico]=resultadoSQL($consulta, 0, 'idServico');
//		$retorno[idUsuario]=resultadoSQL($consulta, 0, 'idUsuario');
//		$retorno[idPrioridade]=resultadoSQL($consulta, 0, 'idPrioridade');
//		$retorno[dtCriacao]=resultadoSQL($consulta, 0, 'dtCriacao');
//		$retorno[dtExecusao]=resultadoSQL($consulta, 0, 'dtExecusao');
//		$retorno[dtConclusao]=resultadoSQL($consulta, 0, 'dtConclusao');
//		$retorno[nome]=resultadoSQL($consulta, 0, 'nome');
//		$retorno[descricao]=resultadoSQL($consulta, 0, 'descricao');
//		$retorno[status]=resultadoSQL($consulta, 0, 'status');
//		$retorno[valor]=resultadoSQL($consulta, 0, 'valor');
//		
//		#nome da Pessoa
//		$pt=dadosPessoasTipos($retorno[idPessoaTipo]);
//		$retorno[Pessoa]=dadosPessoas($pt[idPessoa]);
//		$retorno[nomeCliente]=$retorno[Pessoa][nome];
//		
//		#nome do servico
//		if($retorno[idServico]) {
//			$ps=checkServico($retorno[idServico]);
//		}
//		$retorno[nomeServico]=$ps[nome];
//		
//		#Login
//		if($retorno[idUsuario]) {
//			$us=buscaLoginUsuario($retorno[idUsuario], 'id', 'igual', 'id');
//		}
//		$retorno[login]=$us;
//		
//		#Prioridade
//		$pr=formSelectPrioridade($retorno[idPrioridade], '', 'check');
//		$retorno[nomePrioridade]=$pr[nomeFormatado];
//		
//	}
//	
//	return($retorno);
//}
//
//
//# Função para fechar a OS
//function ordemdeservicoFechar($modulo, $sub, $acao, $registro, $matriz) {
//
//	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $tb, $sessCadastro;
//	if($registro && !$matriz[bntConfirmar]) {
//	
//		# Buscar Valores
//		#$dados=dadosOrdemdeServico($registro, 'id', 'igual', 'id');
//		echo "<br>";
//		ordemdeservicoVer($modulo, $sub, $acao, $registro, $matriz);
//		
//		echo "<br>";
//		# Motrar tabela de busca
//		novaTabela2("[Fechar OS]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
//			# Opcoes Adicionais
//			menuOpcAdicional($modulo, $sub, $acao, $registro);
//			#fim das opcoes adicionais
//			novaLinhaTabela($corFundo, '100%');
//			$texto="			
//				<form method=post name=matriz action=index.php>
//				<input type=hidden name=modulo value=$modulo>
//				<input type=hidden name=sub value=$sub>
//				<input type=hidden name=matriz[idPessoaTipo] value=$idPessoaTipo>
//				<input type=hidden name=matriz[id] value=$registro>
//				<input type=hidden name=acao value=$acao>&nbsp;";
//				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
//			fechaLinhaTabela();
//
//			#botao
//			novaLinhaTabela($corFundo, '100%');
//				$texto="<input type=submit name=matriz[bntConfirmar] value='Confirma' class=submit>";
//				itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
//			fechaLinhaTabela();
//		fechaTabela();
//	} #fecha form
//	
//	# Alteração - bntAlterar pressionado
//	elseif($matriz[bntConfirmar]) {
//		# Conferir campos
//		if($matriz[id]) {
//			# Cadastrar em banco de dados
//			$matriz[status]='F';	
//			$grava=dbOrdemdeServico($matriz, 'fechar');
//			
//			# Verificar inclusão de registro
//			if($grava) {
//				# OK
//				# Visualizar Pessoa
//				$msg="OS Fechada.";
//				avisoNOURL("Aviso: ", $msg, 400);
//				echo "<br>";
//				ordemdeservicoVer($modulo, $sub, 'ver', $matriz[id], $matriz);
//			} else {
//				echo "<br>";
//				$msg="Ocorreram erros durante o fechamento.";
//				avisoNOURL("Aviso: Ocorrência de erro", $msg, 400);
//			}
//		}
//		# falta de parametros
//		else {
//			# acusar falta de parametros
//			# Mensagem de aviso
//			$msg="Falta de parâmetros necessários. Informe os campos obrigatórios e tente novamente";
//			$url="?modulo=$modulo&sub=$sub&acao=$acao";
//			aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
//		}
//	} #fecha bntAlterar
//}
?>
