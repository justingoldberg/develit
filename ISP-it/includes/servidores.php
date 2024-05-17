<?
################################################################################
#       Criado por: Devel IT
#  Data de criação: 24/06/2004
# Ultima alteração: 28/06/2004
#    Alteração No.: 002
#
# Função:
#    Painel - Funções para cadastramento de servidores


# abertura do modulo
function servidores($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessCadastro, $titulo;
	
	$titulo = "<b>Servidores</b>";
	$subtitulo = "Cadastro dos servidores de IVR";
	
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
				servidorAdicionar($modulo, $sub, $acao, $registro, $matriz);
				break;
			case "alterar":
				servidorAlterar($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'procurar':
				servidorProcurar($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'ver':
				servidorVer($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'excluir':
				if(!$matriz['btExcluir']){
					formConfirmExcServidor($modulo, $sub, $acao, $registro, $matriz);
				}
				else{
					servidorExcluir($modulo, $sub, $acao, $registro, $matriz);
				}
				break;
			default:
				servidorListar($modulo, $sub, $acao, $registro, $matriz);
				break;
		}
	}
	echo "<script>location.href='#ancora';</script>";
}

function servidorExcluir($modulo, $sub, $acao, $registro, $matriz) {
	
	global $sessLogin, $sessCadastro, $conn, $tb, $modulo, $sub, $acao;
	
	$temInterface = getInterfaceServidor($registro);
	
	if($temInterface == true){
		$msg="Este Servidor não pode ser excluído porque há Interfaces vinculados a ele!";
		avisoNOURL("Aviso: Exclusão", $msg, 400);
		echo "<br>";
	}
	else{
		$sql = "DELETE FROM $tb[Servidores] WHERE $tb[Servidores].id = ".$registro;
		$excluir = consultaSQL($sql, $conn);
		
		if($excluir){
			$msg="Servidor excluído com sucesso!";
			avisoNOURL("Aviso: Exclusão", $msg, 400);
			echo "<br>";
		}
	}
	
	servidorVer($modulo, $sub, 'ver', $registro, $matriz);
}

#procurar
function servidorProcurar($modulo, $sub, $acao, $registro, $matriz) {
	
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
		$lista=buscaRegistros($matriz[nome], 'nome', 'contem', 'nome', $tb[Servidores]);
		servidorListar($modulo, $sub, 'ver', $lista, $matriz);
	}
}


# função para adicionar
function servidorAdicionar($modulo, $sub, $acao, $registro, $matriz) {

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
			
			$dados=dadosServidor(0);
			
			servidorMostra($dados);
			
			#botao
			getBotao('matriz[bntAdicionar]', 'Adicionar');
						
		fechaTabela();	
	}
	else {
		
		$grava=dbServidor($matriz, 'incluir');
		
		# Verificar inclusão de registro
		echo "<br>";
		if($grava) {
			# Visualizar
			$msg="Registro gravado com sucesso!";
			avisoNOURL("Aviso: ", $msg, 400);
			echo "<br>";
			servidorListar($modulo, $sub, 'listar', $matriz[id], $matriz);
		} 
		else {
			$msg="Ocorreram erros durante a gravação.";
			avisoNOURL("Aviso: Ocorrência de erro", $msg, 400);
		}
	}
}



# Função para alteração de dados da Pessoa - apenas cadastro
function servidorAlterar($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $tb, $sessCadastro;
	
	if($registro && !$matriz[bntConfirmar]) {
	
		# Buscar Valores
		$dados=dadosServidor($registro, 'id', 'igual', 'id');
			
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
			
			ServidorMostra($dados);
			
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
			$grava=dbServidor($matriz, 'alterar');
			
			# Verificar inclusão de registro
			if($grava) {
				# OK
				# Visualizar
				$msg="Registro alterado com sucesso!";
				avisoNOURL("Aviso: ", $msg, 400);
				echo "<br>";
				ServidorVer($modulo, $sub, 'ver', "$matriz[id]", $matriz);
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


function servidorMostra($dados="") {
	
	#nome
	getCampo('text', 'Nome', 'matriz[nome]', $dados[nome]);
	
	#IP
	getCampo('text', 'IP', 'matriz[ip]', $dados[ip]);
	
	#Status
	getCampo('status', 'Status', 'matriz[status]', $dados[status]);
}


# Função para buscar o NOVO ID da Pessoa
function servidorBuscaIDNovo() {

	global $conn, $tb;
	
	$sql="SELECT count(id) qtde from $tb[Servidor]";
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && resultadoSQL($consulta, 0, 'qtde')>0) {
	
		$sql="SELECT MAX(id)+1 id from $tb[Servidor]";
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
function servidorListar($modulo, $sub, $acao, $lista, $matriz) {
	
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
			$consulta=buscaRegistros('', '', 'todos', 'nome', $tb[Servidores]);
		else 
			$consulta=$lista;

		if ($consulta && contaConsulta($consulta)>0) {
			
			$largura             =array('50%',  '20%', '10%',    '20%');
			$gravata[cabecalho]  =array('Nome', 'IP',  'Status', 'Opções');
			$gravata[alinhamento]=array('left', 'left','center', 'left');
			
			$cor='tabfundo0';
			htmlAbreLinha($corFundo);
				for($i=0;$i<count($largura); $i++)
					itemLinhaTMNOURL($gravata[cabecalho][$i], $gravata[alinhamento][$i], 'middle', $largura[$i], $corFundo, 0, $cor);
			htmlFechaLinha();
			
			$qtd=contaConsulta($consulta);
			for($reg=0;$reg<$qtd;$reg++) {
				
				$id=resultadoSQL($consulta, $reg, 'id');
				
				#opcoes
				$def="<a href=?modulo=$modulo&sub=$sub&registro=$id";
				$fnt="<font size='2'>";
				$opcoes =htmlMontaOpcao($def."&acao=ver>".$fnt."Ver</font></a>",'ver');
				$opcoes.=htmlMontaOpcao($def."&acao=alterar>".$fnt."Alterar</font></a>",'alterar');
				// Criação da opção de Exclusão - por Felipe Assis - 24/03/2008
				$opcoes .= htmlMontaOpcao($def."&acao=excluir>".$fnt."Excluir</font></a>", 'excluir');
							
				$i=0;
				$campo[$i++]=resultadoSQL($consulta, $reg, 'nome');
				$campo[$i++]=resultadoSQL($consulta, $reg, 'ip');
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
function servidorVer($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb, $titulo;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		exibeSemPermissao($modulo, $acao);
	}
	else {
		# Procurar dados3
		$objeto=dadosServidor($registro);
		
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
				
				#ip
				getCampo("", "IP", "", $objeto[ip]);
				
				#status
				getCampo("", "Status", "", getComboStatus($objeto[status], "", "check"));
				
			fechaTabela();
		}
	}
	echo "<br>";
	
	listarInterfacesServidor($registro);
}


# Função para Dados
/**
 * @return array
 * @param int $id
 * @desc retorna um array com os dados do servidor
*/
function dadosServidor($id) {

	global $tb;

	$consulta=buscaRegistros($id, 'id', 'igual', 'id', $tb[Servidores]);
	
	if( contaConsulta($consulta)>0 ) {
		$retorno[id]=resultadoSQL($consulta, 0, 'id');
		$retorno[nome]=resultadoSQL($consulta, 0, 'nome');
		$retorno[ip]=resultadoSQL($consulta, 0, 'ip');
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
function formSelectServidor($item, $campo, $retorno, $tipo) {
	
	global $conn, $tb, $corFundo, $modulo, $sub;

	$tabela=$tb[Servidores];
	
	if($tipo=='check') {
		$campo=dadosServidor($item);
		$retorno=$campo[nome];
	} else {
		$retorno = getSelecDados($item, $campo, $retorno, $tipo, $tabela);
	}
	return($retorno);
}


#Função de banco de dados
function dbServidor($matriz, $tipo) {

	global $sessLogin, $sessCadastro, $conn, $tb, $modulo, $sub, $acao;
	
	$tabela=$tb[Servidores];
		
	# Sql de inclusão
	if($tipo=='incluir') {
		
		$sql="INSERT INTO $tabela 
					 (nome, ip, status, dtCadastro)
		      VALUES ('$matriz[nome]',
					  '$matriz[ip]',
					  '$matriz[status]',
					  now()
					   )";
	} #fecha inclusao
	
	# Alterar
	elseif($tipo=='alterar') {
		/* Cria uma matriz com os campos ja formatados para o SQL */
		$campos[id]="id=$matriz[id]";
		$campos[nome]="nome='$matriz[nome]'";
		$campos[ip]="ip='$matriz[ip]'";
		$campos[status]="status='".$matriz[status]."'";
		
		$sql="
			UPDATE $tabela 
			SET
				$campos[nome],
				$campos[ip],
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
 * @desc Função para obter interfaces vinculados ao servidor
 * @version 1.0.0
 * @since 24/03/2008
 */
function getInterfaceServidor($idServidor){
	
	global $sessLogin, $sessCadastro, $conn, $tb, $modulo, $sub, $acao;
	
	$sql = "SELECT $tb[Interfaces].idServidor FROM $tb[Servidores] INNER JOIN $tb[Interfaces] 
			ON ($tb[Servidores].id = $tb[Interfaces].idServidor) 
			WHERE $tb[Servidores].id = $idServidor GROUP BY $tb[Servidores].id";
	
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
 * @desc Função para exibir um formulário de confirmação para excluir o registro de Servidor
 * @version 1.0.0
 * @since 24/03/2008
 */
function formConfirmExcServidor($modulo, $sub, $acao, $registro, $matriz){
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $conn, $tb;
	
	# consultando dados da Interface selecionada
	$sql= "SELECT $tb[Servidores].nome, $tb[Servidores].ip, $tb[Servidores].status,	$tb[Servidores].dtCadastro 
			FROM $tb[Servidores] WHERE $tb[Servidores].id = ".$registro;
	$consulta = consultaSQL($sql, $conn);
	
	//Obtendo resultado
	$nome = resultadoSQL($consulta, 0, 0);
	$enderecoIP = resultadoSQL($consulta, 0, 1);
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
				echo "<b>Servidor:</b>";
			htmlFechaColuna();
			htmlAbreColuna('50%', 'left', $corFundo, 0, 'tabfundo1');
				echo "<b>$nome</b>";
			htmlFechaColuna();
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('50%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b>Endereço IP: </b>";
			htmlFechaColuna();
			htmlAbreColuna('50%', 'left', $corFundo, 0, 'tabfundo1');
				echo "<b>$enderecoIP</b>";
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
 * @desc Função para exibir uma listagem de Interfaces vinculadas a um respectivo servidor
 * @param int $idServidor
 * @version 1.0.0
 * @since 27/03/2008
 */

function listarInterfacesServidor($idServidor){
	global $sessLogin, $sessCadastro, $conn, $tb, $modulo, $sub, $acao, $corFundo, $corBorda;
	
	$sql = "SELECT $tb[Interfaces].nome, $tb[Interfaces].iface, $tb[Interfaces].status 
			FROM $tb[Servidores] INNER JOIN $tb[Interfaces] 
			ON ($tb[Servidores].id = $tb[Interfaces].idServidor) 
			WHERE $tb[Servidores].id = $idServidor 
			GROUP BY $tb[Interfaces].nome";
	
	$consulta = consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta) > 0){
		
		# Montando listagens
		$corGravata = "tabfundo0";
		$corDetalhe = "normal10";
		$gravata = 		array("Interface", 	"IFace","Status");
		$largura = 		array("50%", 		"35%", 	"15%");
		$alinhamento =  array("left", 	"left", 	"center");
		
		novaTabela("Interfaces utilizadas por este Servidor", "left", "100%", 0, 2, 1, $corFundo, $corBorda, 3);
			novaLinhaTabela($corFundo, '100%');
				for($i = 0; $i < count($gravata); $i ++){
					itemLinhaTMNOURL($gravata[$i], $alinhamento[$i], 'middle', $largura[$i], $corFundo, 0, $corGravata, 'tabfundo1');
				}
			fechaLinhaTabela();
			#montando ítens para retorno da consulta
			for($i = 0; $i < contaConsulta($consulta); $i ++){
				novaLinhaTabela($corFundo, '100%');
					$nome = resultadoSQL($consulta, $i, 0);
					$iface = resultadoSQL($consulta, $i, 1);
					$status = resultadoSQL($consulta, $i, 2);
					$status = ($status == 'A' ? "<span class='txtok'>Ativo</span>" : "<span class='txtaviso'>Inativo</a>");
					
					itemLinhaNOURL($nome, "left", $corDetalhe, 0, $corDetalhe);
					itemLinhaNOURL($iface, "left", $corDetalhe, 0, $corDetalhe);
					itemLinhaNOURL($status, "center", $corDetalhe, 0, $corDetalhe);
				fechaLinhaTabela();
			}
		fechaTabela();
	}
	else{
		$msg="Este servidor não está utilizando nenhuma Interface!";
			avisoNOURL("Aviso:", $msg, 400);
			echo "<br>";
	}
}
?>