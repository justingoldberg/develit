<?
################################################################################
#       Criado por: Devel IT
#  Data de criação: 28/06/2004
# Ultima alteração: 28/06/2004
#    Alteração No.: 001
#
# Função:
#    Painel - Funções para cadastramento de Intterfaces


# abertura do modulo
function interfaces($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessCadastro, $titulo;
	
	$titulo = "<b>Interfaces</b>";
	$subtitulo = "Cadastro de Interfaces de Rede";
	
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
				interfacesAdicionar($modulo, $sub, $acao, $registro, $matriz);
				break;
			case "alterar":
				interfacesAlterar($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'procurar':
				interfacesProcurar($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'ver':
				interfacesVer($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'excluir':
				if(!$matriz['btExcluir']){
					formConfirmExcInterface($modulo, $sub, $acao, $registro, $matriz);
				}
				else{
					interfacesExcluir($modulo, $sub, $acao, $registro, $matriz);
				}
				break;
			default:
				interfacesListar($modulo, $sub, $acao, $registro, $matriz);
				break;
		}
	}
	echo "<script>location.href='#ancora';</script>";
}

function interfacesExcluir($modulo, $sub, $acao, $registro, $matriz) {
	
	global $sessLogin, $sessCadastro, $conn, $tb, $modulo, $sub, $acao;
	
	# Obtém as bases vinculadas à Interface
	$temBase = getBaseInterface($registro);
	
	if($temBase == true){
		$msg="Esta Interface não pode ser excluída porque há Bases vinculadas a ela!";
		avisoNOURL("Aviso: Exclusão", $msg, 400);
		echo "<br>";
	}
	else{
		$sql = "DELETE FROM $tb[Interfaces] WHERE $tb[Interfaces].id = ".$registro;
		$excluir = consultaSQL($sql, $conn);
		
		if($excluir){
			$msg="Interface excluída com sucesso!";
			avisoNOURL("Aviso: Exclusão", $msg, 400);
			echo "<br>";
		}
	}
	
	interfacesVer($modulo, $sub, 'ver', $registro, $matriz);
}

#procurar
function interfacesProcurar($modulo, $sub, $acao, $registro, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessCadastro, $titulo, $tb;
	
	if( !$matriz[bntProcurar] ) {

		getFormProcurar($modulo, $sub, $acao, $matriz, $titulo);
		
	} else {
		# realizar consulta
		$lista=buscaRegistros($matriz[nome], 'nome', 'contem', 'nome', $tb[Interfaces]);
		interfacesListar($modulo, $sub, 'ver', $lista, $matriz);
	}
}


# função para adicionar
function interfacesAdicionar($modulo, $sub, $acao, $registro, $matriz) {

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
			
			$dados=dadosinterfaces(0);
			
			interfacesMostra($dados);
			
			#botao
			getBotao('matriz[bntAdicionar]', 'Adicionar');
						
		fechaTabela();	
	}
	else {
		
		$grava=dbinterfaces($matriz, 'incluir');
		
		# Verificar inclusão de registro
		echo "<br>";
		if($grava) {
			# Visualizar
			$msg="Registro gravado com sucesso!";
			avisoNOURL("Aviso: ", $msg, 400);
			echo "<br>";
			interfacesListar($modulo, $sub, 'listar', $matriz[id], $matriz);
		} 
		else {
			$msg="Ocorreram erros durante a gravação.";
			avisoNOURL("Aviso: Ocorrência de erro", $msg, 400);
		}
	}
}



# Função para alteração de dados da Pessoa - apenas cadastro
function interfacesAlterar($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $tb, $sessCadastro;
	
	if($registro && !$matriz[bntConfirmar]) {
	
		# Buscar Valores
		$dados=dadosinterfaces($registro, 'id', 'igual', 'id');
			
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
			
			interfacesMostra($dados);
			
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
			$grava=dbinterfaces($matriz, 'alterar');
			
			# Verificar inclusão de registro
			if($grava) {
				# OK
				# Visualizar
				$msg="Registro alterado com sucesso!";
				avisoNOURL("Aviso: ", $msg, 400);
				echo "<br>";
				interfacesVer($modulo, $sub, 'ver', "$matriz[id]", $matriz);
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


function interfacesMostra($dados="") {
	
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	#nome
	getCampo('text', 'Nome', 'matriz[nome]', $dados[nome]);
	
	#Servico
	
	#Servidor
	$serv=getSelectDados($dados[idServidor], '', 'matriz[idServidor]', 'formnochange', $tb[Servidores], 'nome');
	getCampo('combo', 'Servidor', 'matriz[idServidor]', $serv);
	
	#Iface
	getCampo('text', 'Interface', 'matriz[iface]', $dados[iface], "", "", 10);
	
	#Status
	getCampo('status', 'Status', 'matriz[status]', $dados[status]);
	
}


# Função para buscar o NOVO ID da Pessoa
function interfacesBuscaIDNovo() {

	global $conn, $tb;
	
	$sql="SELECT count(id) qtde from $tb[Interfaces]";
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && resultadoSQL($consulta, 0, 'qtde')>0) {
	
		$sql="SELECT MAX(id)+1 id from $tb[Interfaces]";
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
function interfacesListar($modulo, $sub, $acao, $lista, $matriz) {
	
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
			$consulta=buscaRegistros('', '', 'todos', 'nome', $tb[Interfaces]);
		else 
			$consulta=$lista;

		if ($consulta && contaConsulta($consulta)>0) {
			
			$largura             =array('35%',  '25%',      '10%',    '10%',    '20%');
			$gravata[cabecalho]  =array('Nome', 'Servidor', 'IFace',  'Status', 'Opções');
			$gravata[alinhamento]=array('left', 'left',     'center', 'center', 'left');
			
			$cor='tabfundo0';
			htmlAbreLinha($corFundo);
				for($i=0;$i<count($largura); $i++)
					itemLinhaTMNOURL($gravata[cabecalho][$i], $gravata[alinhamento][$i], 'middle', $largura[$i], $corFundo, 0, $cor);
			htmlFechaLinha();
			
			$qtd=contaConsulta($consulta);
			for($reg=0;$reg<$qtd;$reg++) {
				
				$id=resultadoSQL($consulta, $reg, 'id');
				$servidor=resultadoSQL($consulta, $reg, 'idServidor');
				$nomeServer=dadosServidor($servidor);
				
				#opcoes
				$def="<a href=?modulo=$modulo&sub=$sub&registro=$id";
				$fnt="<font size='2'>";
				$opcoes =htmlMontaOpcao($def."&acao=ver>".$fnt."Ver</font></a>",'ver');
				$opcoes.=htmlMontaOpcao($def."&acao=alterar>".$fnt."Alterar</font></a>",'alterar');
				// Criação da opção de Exclusão - por Felipe Assis - 20/03/2008
				$opcoes .= htmlMontaOpcao($def."&acao=excluir>".$fnt."Excluir</font></a>", 'excluir');
							
				$i=0;
				$campo[$i++]=resultadoSQL($consulta, $reg, 'nome');
				$campo[$i++]=$nomeServer[nome];
				$campo[$i++]=resultadoSQL($consulta, $reg, 'iface');
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
function interfacesVer($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb, $titulo;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		exibeSemPermissao($modulo, $acao);
	}
	else {
		# Procurar dados3
		$objeto=dadosinterfaces($registro);
		
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
				getCampo("", "Servidor", "", $objeto[servidor][nome]);
				
				#iface
				getCampo("", "IFace", "", $objeto[iface]);
				
				#status
				getCampo("", "Status", "", getComboStatus($objeto[status], "", "check"));
				
			fechaTabela();
		}
	}
	
	echo "<br>";
	listarBasesInterface($registro);
}


# Função para Dados
/**
 * @return array
 * @param int $id
 * @desc Retorna um array com os dados do servidor
 Extra:
 dados[servidor] = dados do servidor
*/
function dadosinterfaces($id) {

	global $tb;

	$consulta=buscaRegistros($id, 'id', 'igual', 'id', $tb[Interfaces]);
	
	if( contaConsulta($consulta)>0 ) {
		$retorno[id]=resultadoSQL($consulta, 0, 'id');
		$retorno[nome]=resultadoSQL($consulta, 0, 'nome');
		$retorno[idServidor]=resultadoSQL($consulta, 0, 'idservidor');
		$retorno[iface]=resultadoSQL($consulta, 0, 'iface');
		$retorno[status]=resultadoSQL($consulta, 0, 'status');
		
		#extras
		$retorno[servidor]=dadosServidor($retorno[idServidor]);
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
function formSelectinterfaces($item, $campo, $retorno, $tipo) {
	
	global $conn, $tb, $corFundo, $modulo, $sub;

	$tabela=$tb[Interfaces];
	
	if($tipo=='check') {
		$campo=dadosinterfaces($item);
		$retorno=$campo[nome];
	} else {
		$retorno = getSelecDados($item, $campo, $retorno, $tipo, $tabela);
	}
	return($retorno);
}


#Função de banco de dados
function dbinterfaces($matriz, $tipo) {

	global $sessLogin, $sessCadastro, $conn, $tb, $modulo, $sub, $acao;
	
	$tabela=$tb[Interfaces];
		
	# Sql de inclusão
	if($tipo=='incluir') {
		
		$sql="INSERT INTO $tabela 
					 (iface, nome, idServidor, status, dtCadastro)
		      VALUES ('$matriz[iface]',
					  '$matriz[nome]',
					  '$matriz[idServidor]',
					  '$matriz[status]',
					  now()
					   )";
	} #fecha inclusao
	
	# Alterar
	elseif($tipo=='alterar') {
		/* Cria uma matriz com os campos ja formatados para o SQL */
		$campos[id]="id=$matriz[id]";
		$campos[nome]="nome='$matriz[nome]'";
		$campos[idServidor]="idServidor='$matriz[idServidor]'";
		$campos[iface]="iface='$matriz[iface]'";
		$campos[status]="status='".$matriz[status]."'";
		
		$sql="
			UPDATE $tabela 
			SET
				$campos[nome],
				$campos[idServidor],
				$campos[iface],
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
 * @desc Função para obter bases vinculados à interface
 * @version 1.0.0
 * @since 24/03/2008
 */
function getBaseInterface($idInterface){
	
	global $sessLogin, $sessCadastro, $conn, $tb, $modulo, $sub, $acao;
	
	$sql = "SELECT $tb[Interfaces].id FROM $tb[Interfaces] INNER JOIN $tb[Bases] 
			ON ($tb[Interfaces].id = $tb[Bases].idIFaceServidor) 
			WHERE $tb[Interfaces].id = $idInterface GROUP BY Interfaces.id";
	
	$consulta = consultaSQL($sql, $conn);
	
	if(mysql_num_rows($consulta) > 0){
		return true;
	}
	else{
		return false;
	}
}

/**
 * @author Felipe dos S. Assis
 * @desc Função para exibir um formulário de confirmação para excluir o registro de Interface
 * @version 1.0.0
 * @since 24/03/2008
 */
function formConfirmExcInterface($modulo, $sub, $acao, $registro, $matriz){
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $conn, $tb;
	
	# consultando dados da Interface selecionada
	$sql= "SELECT $tb[Servidores].nome as servidor, $tb[Interfaces].nome, $tb[Interfaces].iface, $tb[Interfaces].status,
				$tb[Interfaces].dtCadastro FROM $tb[Interfaces] INNER JOIN $tb[Servidores] 
				ON ($tb[Interfaces].idServidor = $tb[Servidores].id) WHERE $tb[Interfaces].id = ".$registro;
	$consulta = consultaSQL($sql, $conn);
	
	//Obtendo resultado
	$servidor = resultadoSQL($consulta, 0, 0);
	$nome = resultadoSQL($consulta, 0, 1);
	$iFace = resultadoSQL($consulta, 0, 2);
	$status = resultadoSQL($consulta, 0, 3);
	$dtCadastro = resultadoSQL($consulta, 0, 4);
	
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
				echo "<b>Servidor: </b>";
			htmlFechaColuna();
			htmlAbreColuna('50%', 'left', $corFundo, 0, 'tabfundo1');
				echo "<b>$servidor</b>";
			htmlFechaColuna();
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('50%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b>Nome: </b>";
			htmlFechaColuna();
			htmlAbreColuna('50%', 'left', $corFundo, 0, 'tabfundo1');
				echo "<b>$nome</b>";
			htmlFechaColuna();
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('50%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b>IFace: </b>";
			htmlFechaColuna();
			htmlAbreColuna('50%', 'left', $corFundo, 0, 'tabfundo1');
				echo "<b>$iFace</b>";
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
 * @desc Função para exibir uma listagem de Bases vinculadas a um respectiva Interface
 * @param int $idInterface
 * @version 1.0.0
 * @since 27/03/2008
 */

function listarBasesInterface($idInterface){
	global $sessLogin, $sessCadastro, $conn, $tb, $modulo, $sub, $acao, $corFundo, $corBorda;
		
	$sql = "SELECT $tb[Bases].nome, $tb[Bases].descricao, $tb[Bases].status 
			FROM $tb[Interfaces] INNER JOIN $tb[Bases] 
			ON ($tb[Interfaces].id = $tb[Bases].idIFaceServidor) 
			WHERE $tb[Interfaces].id = $idInterface GROUP BY $tb[Bases].nome";
	
	$consulta = consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta) > 0){
		
		# Montando listagens
		$corGravata = "tabfundo0";
		$corDetalhe = "normal10";
		$gravata = 		array("Base", 	"Descrição","Status");
		$largura = 		array("40%", 		"40%", 	"20%");
		$alinhamento =  array("left", 	"left", 	"center");
		
		novaTabela("Bases que utilizam esta Interface", "left", "100%", 0, 2, 1, $corFundo, $corBorda, 3);
			novaLinhaTabela($corFundo, '100%');
				for($i = 0; $i < count($gravata); $i ++){
					itemLinhaTMNOURL($gravata[$i], $alinhamento[$i], 'middle', $largura[$i], $corFundo, 0, $corGravata, 'tabfundo1');
				}
			fechaLinhaTabela();
			#montando ítens para retorno da consulta
			for($i = 0; $i < contaConsulta($consulta); $i ++){
				novaLinhaTabela($corFundo, '100%');
					$nome = resultadoSQL($consulta, $i, 0);
					$descricao = resultadoSQL($consulta, $i, 1);
					$status = resultadoSQL($consulta, $i, 2);
					$status = ($status == 'A' ? "<span class='txtok'>Ativo</span>" : "<span class='txtaviso'>Inativo</a>");
					
					itemLinhaNOURL($nome, "left", $corDetalhe, 0, $corDetalhe);
					itemLinhaNOURL($descricao, "left", $corDetalhe, 0, $corDetalhe);
					itemLinhaNOURL($status, "center", $corDetalhe, 0, $corDetalhe);
				fechaLinhaTabela();
			}
		fechaTabela();
	}
	else{
		$msg="Esta Interface não está sendo utilizada em nenhuma Base!";
			avisoNOURL("Aviso:", $msg, 400);
			echo "<br>";
	}
}

?>