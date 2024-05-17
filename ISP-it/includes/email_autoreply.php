<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 23/12/2003
# Ultima alteração: 23/12/2003
#    Alteração No.: 003
#
# Função:
#    Painel - Funções para controle de usuarios radius por pessoas
# 

# função de busca de grupos
function buscaEmailAutoReply($texto, $campo, $tipo, $ordem) {

	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[EmailAutoReply] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[EmailAutoReply] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[EmailAutoReply] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[EmailAutoReply] WHERE $texto ORDER BY $ordem";
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
} # fecha função de busca de grupos



# Função para gravação em banco de dados
function dbEmailAutoReply($matriz, $tipo)
{
	global $conn, $tb, $modulo, $sub, $acao;
	
	$data=dataSistema();
	
	# Sql de inclusão
	if($tipo=='incluir') {
		$sql="INSERT INTO $tb[EmailAutoReply] VALUES (
			'$matriz[idEmail]', 
			'$matriz[texto]',
			'$data[dataBanco]',
			'$matriz[status]'
		)";
	} #fecha inclusao
	
	# Excluir
	elseif($tipo=='excluir') {
		$sql="DELETE FROM $tb[EmailAutoReply] WHERE idEmail=$matriz[idEmail]";
	}

	# Excluir Email
	elseif($tipo=='excluiremail') {
		$sql="DELETE FROM $tb[EmailAutoReply] WHERE idEmail=$matriz[id]";
	}

	# Alterar
	elseif($tipo=='alterar') {
		$sql="
			UPDATE 
				$tb[EmailAutoReply]
			SET
				texto='$matriz[texto]',
				dtCadastro='$data[dataBanco]'
			WHERE
				idEmail='$matriz[idEmail]'
		";
	}

	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
	
} # fecha função de gravação em banco de dados



# Funcao para cadastro de usuarios
function emailAutoReplyContasDominio($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	$idPessoaTipo=$matriz[idPessoaTipo];
	$idModulo=$matriz[idModulo];
	$idEmail=$matriz[id];
	
	$email=dadosEmail($idEmail);
	
	$matriz[idEmail]=$email[id];
	$matriz[login]=$email[login];
	$matriz[idDominio]=$email[idDominio];
	
	$dominio=dadosDominio($matriz[idDominio]);
	$matriz[dominio]=$dominio[nome];
	
	# Form de inclusao
	if(!$matriz[bntAtualizar] && !$matriz[bntExcluir]) {

		# Visualizar Dominio
		verEmails($modulo, $sub, $acao, $matriz[id], $matriz);
		echo "<br>";

		# Listar parametros em formulário
		# Formulário de postagem de dados de configurações e parametros
		formEmailAutoReply($modulo, $sub, $acao, $registro, $matriz);

	} #fecha form
	else {

		if($matriz[bntExcluir]) {
			# Remover
			$grava=dbEmailAutoReply($matriz, 'excluir');
			
			# Configurar AutoReply no Manager
			$autoreply=managerComando($matriz, 'emailautoreplyremover');
			
			# Mensagem de aviso
			$msg="Auto-Resposta excluída com sucesso!";
			avisoNOURL("Aviso", $msg, 400);

		}
		elseif($matriz[bntAtualizar]) {
			# Gravar auto-resposta
			$grava=dbEmailAutoReply($matriz, $matriz[acao]);
			
			$matriz[texto]=str_replace(' ','&nbsp;',$matriz[texto]);
			$matriz[texto]=str_replace('\t','&nbsp;',$matriz[texto]);
			$matriz[texto].="<br><br>";
			
			# Configurar AutoReply no Manager
			$autoreply=managerComando($matriz, 'emailautoreplyadicionar');
			
			# Mensagem de aviso
			$msg="Auto-Resposta configurada com sucesso!";
			avisoNOURL("Aviso", $msg, 400);
		}
		
		echo "<br>";
		
		$matriz[id]=$matriz[idDominio];
		emailListarContasDominios($modulo, 'mail', 'listar', "$matriz[idPessoasTipos]:$matriz[idDominio]", $matriz);
	}
} # fecha funcao de inclusao de grupos





# Formulário de Dados de Configuração
function formEmailAutoReply($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $tb, $corFundo, $corBorda, $html;

		
	$consulta=buscaEmailAutoReply($matriz[idEmail],'idEmail','igual','idEmail');
	
	if($consulta && contaConsulta($consulta)>0) {
		# Valores
		$matriz[idEmail]=resultadoSQL($consulta, $a, 'idEmail');
		$matriz[texto]=htmlentities(resultadoSQL($consulta, $a, 'texto'));
		$matriz[status]=resultadoSQL($consulta, $a, 'status');
		$matriz[acao]='alterar';
	}
	else $matriz[acao]='incluir';
	

	# Motrar tabela de busca
	novaTabela2("[Auto Resposta]<a href=# name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		# Opcoes Adicionais
		menuOpcAdicional($modulo, $sub, $acao, "$registro:$matriz[idDominio]");
		#fim das opcoes adicionais
		novaLinhaTabela($corFundo, '100%');
		$texto="			
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=matriz[idDominio] value=$matriz[idDominio]>
			<input type=hidden name=matriz[idPessoasTipos] value=$matriz[idPessoaTipo]>
			<input type=hidden name=matriz[idEmail] value=$matriz[idEmail]>
			<input type=hidden name=matriz[acao] value=$matriz[acao]>
			<input type=hidden name=registro value=$registro:$matriz[idEmail]>
			&nbsp;";
			itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			$texto="<b>Auto Resposta</b>";
			itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			$texto="<textarea name=matriz[texto] rows=10 cols=60>$matriz[texto]</textarea>";
			itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b class=bold10>Status: </b><br>
				<span class=normal10>Status da auto-resposta</span>";
			htmlFechaColuna();
			itemLinhaForm(formSelectStatusEmails($matriz[status],'status','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			$texto="
				<input type=submit name=matriz[bntAtualizar] value=Atualizar class=submit>
				&nbsp;<input type=submit name=matriz[bntExcluir] value=Excluir class=submit2>
			";
			# Botão de exclusão
			itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
	fechaTabela();
}

?>
