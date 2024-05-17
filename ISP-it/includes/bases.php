<?
################################################################################
#       Criado por: Devel IT
#  Data de criação: 28/06/2004
# Ultima alteração: 28/06/2004
#    Alteração No.: 001
#
# Função:
#    Painel - Funções para cadastramento de bases


# abertura do modulo
function bases($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessCadastro, $titulo;
	
	$titulo = "<b>Bases</b>";
	$subtitulo = "Cadastro de Bases IVR";
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[adicionar]) {
		exibeSemPermissao($modulo, $acao);
	}
	else {
		
		# Topo da tabela - Informações e menu principal do Cadastro
		$itens=array('Adicionar', 'Procurar', 'Listar');
		
		#Monta a Tela Padrão
		getHomeModulo($modulo, $sub, $titulo, $subtitulo, $itens);
		
		# case das acoes
		echo "<br>";
		switch ($acao) {
			case "adicionar":			
				baseAdicionar($modulo, $sub, $acao, $registro, $matriz);
				break;
			case "alterar":
				baseAlterar($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'procurar':
				baseProcurar($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'ver':
				baseVer($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'excluir':
				if(!$matriz['btExcluir']){
					formConfirmExcBase($modulo, $sub, $acao, $registro, $matriz);
				}
				else{
					baseExcluir($modulo, $sub, $acao, $registro, $matriz);
				}
				break;
			case 'verClientes':
				listarClientesBase($registro);
			break;
			default:
				baseListar($modulo, $sub, $acao, $registro, $matriz);
				break;
		}
	}
	echo "<script>location.href='#ancora';</script>";
}

function baseExcluir($modulo, $sub, $acao, $registro, $matriz) {

	global $sessLogin, $sessCadastro, $conn, $tb, $modulo, $sub, $acao;
		
	# Obtém os clientes relacionados vinculados à base
		
	$temCliente = getClientesBase($registro);
	
	if($temCliente == true){
		$msg="Esta base não pode ser excluída porque há planos de clientes vinculadas a ela!";
		avisoNOURL("Aviso: Exclusão", $msg, 400);
		echo "<br>";
	}
	else{
		$sql = "DELETE FROM $tb[Bases] WHERE $tb[Bases].id = ".$_REQUEST['registro'];
		$excluir = consultaSQL($sql, $conn);
		
		if($excluir){
			$msg="Base excluída com sucesso!";
			avisoNOURL("Aviso: Exclusão", $msg, 400);
			echo "<br>";
		}
	}
	
	baseVer($modulo, $sub, 'ver', $registro, $matriz);
	echo "<br>";
	
}

#procurar
function baseProcurar($modulo, $sub, $acao, $registro, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessCadastro, $titulo, $tb;
	
	if( !$matriz[bntProcurar] ) {

		novaTabela2("[Procurar $titulo]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			
			novaLinhaTabela($corFundo, '100%');
			$texto="<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro>";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
			
			#nome
			getCampo('text', "Nome", 'matriz[nome]');
			
			#botao
			getBotao('matriz[bntProcurar]', 'Procurar');
			
		fechaTabela();
	} else {
		# realizar consulta
		$lista=buscaRegistros($matriz[nome], 'nome', 'contem', 'nome', $tb[Bases]);
		baseListar($modulo, $sub, 'ver', $lista, $matriz);
	}
}


# função para adicionar
function baseAdicionar($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro;
	
	if(!$matriz[bntAdicionar]) {
		
		# Motrar tabela de busca
		novaTabela2("[Adicionar]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			#menuOpcAdicional($modulo, $sub, $acao, $registro);
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=matriz[bntConfirmar] value=$matriz[bntConfirmar]>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			$dados=dadosbase(0);
			
			baseMostra($dados);
			
			#botao
			getBotao('matriz[bntAdicionar]', 'Adicionar');
						
		fechaTabela();	
	}
	else {
		
		$grava=dbbase($matriz, 'incluir');
		
		# Verificar inclusão de registro
		echo "<br>";
		if($grava) {
			# Visualizar
			$msg="Registro gravado com sucesso!";
			avisoNOURL("Aviso: ", $msg, 400);
			echo "<br>";
			baseListar($modulo, $sub, 'listar', $matriz[id], $matriz);
		} 
		else {
			$msg="Ocorreram erros durante a gravação.";
			avisoNOURL("Aviso: Ocorrência de erro", $msg, 400);
		}
	}
}



# Função para alteração de dados da Pessoa - apenas cadastro
function baseAlterar($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $tb, $sessCadastro;
	
	if($registro && !$matriz[bntConfirmar]) {
	
		# Buscar Valores
		$dados=dadosbase($registro, 'id', 'igual', 'id');
			
		# Motrar tabela de busca
		novaTabela2("[Alterar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			menuOpcAdicional($modulo, $sub, $acao, $registro);
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=matriz[id] value=$registro>
				<input type=hidden name=acao value=$acao>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			baseMostra($dados);
			
			#botao
			getBotao('matriz[bntConfirmar]', 'Alterar');
			
		fechaTabela();
	} #fecha form
	
	# Alteração - bntAlterar pressionado
	elseif($matriz[bntConfirmar]) {
		# Conferir campos
		if($matriz[id]) {
			# continuar
			# Cadastrar em banco de dados
			$grava=dbbase($matriz, 'alterar');
			
			# Verificar inclusão de registro
			if($grava) {
				# OK
				# Visualizar
				$msg="Registro alterado com sucesso!";
				avisoNOURL("Aviso: ", $msg, 400);
				echo "<br>";
				baseVer($modulo, $sub, 'ver', "$matriz[id]", $matriz);
			} else {
				echo "<br>";
				$msg="Ocorreram erros durante a gravação.";
				avisoNOURL("Aviso: Ocorrência de erro", $msg, 400);
			}
		}
		# falta de parametros
		else {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Falta de parâmetros necessários. Informe os campos obrigatórios e tente novamente";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
		}
	} #fecha bntAlterar
}


function baseMostra($dados="") {
	
	global $conn, $tb;
	
	#nome
	getCampo('text', 'Nome', 'matriz[nome]', $dados[nome]);
	
	#Descricao
	getCampo('text', 'Descrição', 'matriz[descricao]', $dados[descricao]);
	
	#Iface
	$iface=getSelectDados($dados[idIfaceServidor], '', 'matriz[idIfaceServidor]', 'formnochange', $tb[Interfaces], 'nome');
	getCampo('combo', 'Interface', 'matriz[idIfaceServidor]', $iface);
	
	#Status
	getCampo('status', 'Status', 'matriz[status]', $dados[status]);
	
}


# Função para buscar o NOVO ID da Pessoa
function baseBuscaIDNovo() {

	global $conn, $tb;
	
	$sql="SELECT count(id) qtde from $tb[base]";
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && resultadoSQL($consulta, 0, 'qtde')>0) {
	
		$sql="SELECT MAX(id)+1 id from $tb[base]";
		$consulta=consultaSQL($sql, $conn);
		
		if($consulta && contaConsulta($consulta)>0) {
			$retorno=resultadoSQL($consulta, 0, 'id');
			if(!is_numeric($retorno)) $retorno=1;
		}
		else $retorno=1;
	}
	else {
		$retorno=resultadoSQL($consulta, 0, 'qtde')+1;
	}
	return($retorno);
}


#Lista todas OS do cliente selecionado
function baseListar($modulo, $sub, $acao, $lista, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb, $titulo;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		exibeSemPermissao($modulo, $acao);
	}
	else {
		
		# Cabeçalho
		novaTabela("Lista de ".$titulo, "left", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
			# Opcoes Adicionais
			#menuOpcAdicional($modulo, $sub, $acao, $cliente);
		fechaTabela();
		
		
		novaTabelaSH('left', '100%', 0, 2, 1, $corFundo, $corBorda, 6);
		
		if (!$lista)
			$consulta=buscaRegistros('', '', 'todos', 'nome', $tb[Bases]);
		else 
			$consulta=$lista;

		if ($consulta && contaConsulta($consulta)>0) {
			
			$largura             =array('25%',  '30%',           '15%',    '10%',    '20%');
			$gravata[cabecalho]  =array('Nome', 'Descrição',     'IFace',  'Status', 'Opções');
			$gravata[alinhamento]=array('left', 'left','center', 'center', 'left',    'left');
			
			$cor='tabfundo0';
			htmlAbreLinha($corFundo);
				for($i=0;$i<count($largura); $i++)
					itemLinhaTMNOURL($gravata[cabecalho][$i], $gravata[alinhamento][$i], 'middle', $largura[$i], $corFundo, 0, $cor);
			htmlFechaLinha();
			
			$qtd=contaConsulta($consulta);
			for($reg=0;$reg<$qtd;$reg++) {
				
				$id=resultadoSQL($consulta, $reg, 'id');
				$idIface=resultadoSQL($consulta, $reg, 'idIfaceServidor');
				$iface=dadosinterfaces($idIface);
				
				#opcoes
				$def="<a href=?modulo=$modulo&sub=$sub&registro=$id";
				$fnt="<font size='2'>";
				$opcoes =htmlMontaOpcao($def."&acao=ver>".$fnt."Ver</font></a>",'ver');
				$opcoes.=htmlMontaOpcao($def."&acao=alterar>".$fnt."Alterar</font></a>",'alterar');
				// Criação da opção de Exclusão - por Felipe Assis - 20/03/2008
				$opcoes .= htmlMontaOpcao($def."&acao=excluir>".$fnt."Excluir</font></a>", 'excluir');
				// Criação da opção de visualização de Clientes - por Felipe Assis- 27/03/2008
				$opcoes .= htmlMontaOpcao($def."&acao=verClientes>".$fnt."Ver Clientes</font></a>", 'usuario');
															
				$i=0;
				$campo[$i++]=resultadoSQL($consulta, $reg, 'nome');
				$campo[$i++]=resultadoSQL($consulta, $reg, 'descricao');
				$campo[$i++]=$iface[nome];
				$campo[$i++]=getComboStatus(resultadoSQL($consulta, $reg, 'status'), "", 'check');
				$campo[$i++]=$opcoes;
				
				$cor='normal10';
				htmlAbreLinha($corFundo);
					for($i=0;$i<count($largura); $i++)
						itemLinhaTMNOURL($campo[$i], $gravata[alinhamento][$i].' '.$cor, 'middle', $largura[$i], $corFundo, 0, $cor);
				htmlFechaLinha();
			}
		}
		else {
			fechaTabela();
			novaTabelaSH("left", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
				$cor='txtaviso';
				htmlAbreLinha($corFundo);
					itemLinhaTMNOURL( "Não há registros para sua solicitação.", 'left', 'middle', $largura[0], $corFundo, 0, $cor);
				htmlFechaLinha();
		}
		fechaTabela();
	}
}

# função Exibição
function baseVer($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb, $titulo;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		exibeSemPermissao($modulo, $acao);
	}
	else {
		# Procurar dados3
		$objeto=dadosbase($registro);
		
		if(is_array($objeto)) {
			# Motrar tabela de busca
			novaTabela2("[$titulo - Visualização]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				
				# Opcoes Adicionais
				#menuOpcAdicional($modulo, $sub, 'ver', $registro);
				
				$bgLabel='tabfundo1';
				$bgCampo='tabfundo1';
				
				itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
				
				#nome
				getCampo("", "Nome", "", $objeto[nome]);
				
				#descricao
				getCampo("", "Descrição", "", $objeto[descricao]);
				
				#iface
				$iface=dadosinterfaces($objeto[idIfaceServidor]);
				getCampo("", "IFace", "", $iface[nome]);
				
				#status
				getCampo("", "Status", "", getComboStatus($objeto[status], "", "check"));
				
			fechaTabela();
		}
	}
}


# Função para Dados
function dadosbase($id) {

	global $tb;

	$consulta=buscaRegistros($id, 'id', 'igual', 'id', $tb[Bases]);
	
	if( contaConsulta($consulta)>0 ) {
		$retorno[id]=resultadoSQL($consulta, 0, 'id');
		$retorno[nome]=resultadoSQL($consulta, 0, 'nome');
		$retorno[descricao]=resultadoSQL($consulta, 0, 'descricao');
		$retorno[idIfaceServidor]=resultadoSQL($consulta, 0, 'idIfaceServidor');
		$retorno[status]=resultadoSQL($consulta, 0, 'status');
	}
	
	return($retorno);
}

/**
 * @return unknown
 * @param unknown $item
 * @param unknown $campo
 * @param unknown $retorno
 * @param unknown $tipo
 * @desc Retorna 1 ou varios itens do objeto.
 item  = id 
 campo = campo da matriz
 retorno = campo de retorno (nome do componente)
 tipo = check - retorna o nome
        form  - um combo com os nomes
        multi - uma caixa de selecao com os nomes
*/
function formSelectbase($item, $campo, $retorno, $tipo) {
	
	global $conn, $tb, $corFundo, $modulo, $sub;

	$tabela=$tb[Bases];
	
	if($tipo=='check') {
		$campo=dadosbase($item);
		$retorno=$campo[nome];
	} else {
		$retorno = getSelectDados($item, $campo, $retorno, $tipo, $tabela);
	}
	return($retorno);
}


#Função de banco de dados
function dbbase($matriz, $tipo) {

	global $sessLogin, $sessCadastro, $conn, $tb, $modulo, $sub, $acao;
	
	$tabela=$tb[Bases];
		
	# Sql de inclusão
	if($tipo=='incluir') {
		
		$sql="INSERT INTO $tabela 
					 (idIFaceServidor, nome, descricao, status, dtCadastro)
		      VALUES ('$matriz[idIfaceServidor]',
					  '$matriz[nome]',
					  '$matriz[descricao]',
					  '$matriz[status]',
					  now()
					   )";
	} #fecha inclusao
	
	# Alterar
	elseif($tipo=='alterar') {
		/* Cria uma matriz com os campos ja formatados para o SQL */
		$campos[id]="id=$matriz[id]";
		$campos[nome]="nome='$matriz[nome]'";
		$campos[descricao]="descricao='$matriz[descricao]'";
		$campos[idIfaceServidor]="idIfaceServidor='$matriz[idIfaceServidor]'";
		$campos[status]="status='".$matriz[status]."'";
		
		$sql="
			UPDATE $tabela 
			SET
				$campos[nome],
				$campos[descricao],
				$campos[idIfaceServidor],
				$campos[status] 
			WHERE
				$campos[id]";
	}
	
	elseif($tipo=='excluir') {
		$sql="DELETE FROM $tabela WHERE $campos[id]";
	}
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
}

/**
 * @author Felipe dos S. Assis
 * @desc Função para obter clientes com planos vinculados à base
 * @version 1.0.0
 * @since 20/03/2008
 */
function getClientesBase($idBase){
	
	global $sessLogin, $sessCadastro, $conn, $tb, $modulo, $sub, $acao;
	
	$sql = "SELECT $tb[Pessoas].id as idPessoa FROM $tb[Bases] 
			INNER JOIN $tb[ServicosIVR] ON ($tb[Bases].id = $tb[ServicosIVR].idBase) 
			INNER JOIN $tb[ServicosPlanos] ON ($tb[ServicosIVR].idServicoPlano = $tb[ServicosPlanos].id)
			INNER JOIN $tb[PlanosPessoas] ON ($tb[ServicosPlanos].idPlano = $tb[PlanosPessoas].id) 
			INNER JOIN $tb[PessoasTipos] ON ($tb[PlanosPessoas].idPessoaTipo = $tb[PessoasTipos].id) 
			INNER JOIN $tb[Pessoas] ON ($tb[PessoasTipos].idPessoa = $tb[Pessoas].id) 
			WHERE $tb[Bases].id = $idBase GROUP BY $tb[Pessoas].id";
	
	$consulta = consultaSQL($sql, $conn);
	
	# Testa se a consulta retornou ao menos 1 linha
	if(mysql_num_rows($consulta) > 0){
		return true;
	}
	else{
		return false;
	}
}

/**
 * @author Felipe dos S. Assis
 * @desc Função para exibir um formulário de confirmação para excluir o registro da Base
 * @version 1.0.0
 * @since 20/03/2008
 */
function formConfirmExcBase($modulo, $sub, $acao, $registro, $matriz){
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $conn, $tb;
	
	//consultando dados da base selecionada
	
	$sql= "SELECT $tb[Bases].nome, $tb[Bases].descricao, $tb[Bases].status, $tb[Bases].dtCadastro 
				FROM $tb[Bases] WHERE $tb[Bases].id = ".$_REQUEST['registro'];
	$consulta = consultaSQL($sql, $conn);
	
	//Obtendo resultado
	$nome = resultadoSQL($consulta, 0, 0);
	$descricao = resultadoSQL($consulta, 0, 1);
	$status = resultadoSQL($consulta, 0, 2);
	$dtCadastro = resultadoSQL($consulta, 0, 3);
	
	novaTabela2("[Excluir]", "center", "100%", 0, 2, 1, $corFundo, $corBorda, 2);
		novaLinhaTabela($corFundo, '100%');
			$texto ="
					<form method='post' name='matriz' action='index.php'>
					<input type='hidden' name='modulo' value='$modulo'>
					<input type='hidden' name='sub' value='$sub'>
					<input type='hidden' name='acao' value='$acao'>
					<input type='hidden' name='registro' value='$registro'>";
			itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('50%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b>Base: </b>";
			htmlFechaColuna();
			htmlAbreColuna('50%', 'left', $corFundo, 0, 'tabfundo1');
				echo "<b>$nome</b>";
			htmlFechaColuna();
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('50%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b>Descrição: </b>";
			htmlFechaColuna();
			htmlAbreColuna('50%', 'left', $corFundo, 0, 'tabfundo1');
				echo "<b>$descricao</b>";
			htmlFechaColuna();
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('50%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b>Status: </b>";
			htmlFechaColuna();
			$status = ($status == 'A' ? "<span class=\"txtok\">Ativo</span>" : "<span class=\"txtaviso\">Inativo</span>");
			htmlAbreColuna('50%', 'left', $corFundo, 0, 'tabfundo1');
				echo "<b>$status</b>";
			htmlFechaColuna();
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('50%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b>Data de Cadastro: </b>";
			htmlFechaColuna();
			htmlAbreColuna('50%', 'left', $corFundo, 0, 'tabfundo1');
				echo "<b>$dtCadastro</b>";
			htmlFechaColuna();
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, 100);
			$texto = "<input type='submit' name='matriz[btExcluir]' value='Excluir' class='submit'>";
			itemLinhaNOURL($texto, "center", $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
	fechaTabela();
	echo "<br>";
}

/**
 * @author Felipe dos S. Assis
 * @desc Função para exibir uma listagem de Clientes vinculados a um respectiva Base
 * @param int $idBase
 * @version 1.0.0
 * @since 27/03/2008
 */

function listarClientesBase($idBase){
	global $sessLogin, $sessCadastro, $conn, $tb, $modulo, $sub, $acao, $corFundo, $corBorda;
			
	$sql = "SELECT
			$tb[Pessoas].nome, 
			$tb[Pessoas].tipoPessoa,
			$tb[PlanosPessoas].nome as plano, 
			$tb[PlanosPessoas].status as statusPlano, 
			$tb[POP].nome as nomePop, 
			$tb[Bases].nome as nomeBase
			FROM $tb[Bases] INNER JOIN $tb[ServicosIVR] 
			ON ($tb[Bases].id = $tb[ServicosIVR].idBase) 
			INNER JOIN $tb[ServicosPlanos] 
			ON ($tb[ServicosIVR].idServicoPlano = $tb[ServicosPlanos].id) 
			INNER JOIN $tb[PlanosPessoas] 
			ON ($tb[ServicosPlanos].idPlano = $tb[PlanosPessoas].id) 
			INNER JOIN $tb[PessoasTipos] 
			ON ($tb[PlanosPessoas].idPessoaTipo = $tb[PessoasTipos].id) 
			INNER JOIN $tb[Pessoas] 
			ON ($tb[PessoasTipos].idPessoa = $tb[Pessoas].id) 
			INNER JOIN $tb[POP] 
			ON ($tb[Pessoas].idPop = $tb[POP].id) 
			WHERE $tb[Bases].id = $idBase GROUP BY Pessoas.nome";
	
	$consulta = consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta) > 0){
		
		# Montando listagens
		$corGravata = "tabfundo0";
		$corDetalhe = "normal10";
		$gravata = 		array("Nome", 	"Tipo de Pessoa",	"Plano", 	"Status do Plano", "POP de Acesso");
		$largura = 		array("30%", 	"8%", 				"30%", 		"10%", 				"30%");
		$alinhamento =  array("left", 	"center", 			"center",	"center",			"left");
		
		# Obtendo nome da Base
		
		$nomeBase = resultadoSQL($consulta, 0, 5);
		
		novaTabela("Clientes usuários da base ".$nomeBase, "left", "100%", 0, 2, 1, $corFundo, $corBorda, 3);
			novaLinhaTabela($corFundo, '100%');
				for($i = 0; $i < count($gravata); $i ++){
					itemLinhaTMNOURL($gravata[$i], $alinhamento[$i], 'middle', $largura[$i], $corFundo, 0, $corGravata, 'tabfundo1');
				}
			fechaLinhaTabela();
			#montando ítens para retorno da consulta
			for($i = 0; $i < contaConsulta($consulta); $i ++){
				novaLinhaTabela($corFundo, '100%');
					$nome = resultadoSQL($consulta, $i, 0);
					$tipoPessoa = resultadoSQL($consulta, $i, 1);
					$tipoPessoa = ($tipoPessoa == 'J' ? "Pessoa Jurídica" : "Pessoa Física");
					$planoPessoa = resultadoSQL($consulta, $i, 2);
					$statusPlano = resultadoSQL($consulta, $i, 3);
					$statusPlano = ($statusPlano == 'A' ? "<span class='txtok'>Ativo</span>" : "<span class='txtaviso'>Inativo</a>");
					$pop = resultadoSQL($consulta, $i, 4);
					
					itemLinhaNOURL($nome, "left", $corDetalhe, 0, $corDetalhe);
					itemLinhaNOURL($tipoPessoa, "left", $corDetalhe, 0, $corDetalhe);
					itemLinhaNOURL($planoPessoa, "left", $corDetalhe, 0, $corDetalhe);
					itemLinhaNOURL($statusPlano, "center", $corDetalhe, 0, $corDetalhe);
					itemLinhaNOURL($pop, "left", $corDetalhe, 0, $corDetalhe);
				fechaLinhaTabela();
			}
		fechaTabela();
	}
	else{
		$msg="Esta Base não está sendo utilizada por nenhum Cliente!";
			avisoNOURL("Aviso:", $msg, 400);
			echo "<br>";
	}
}
?>