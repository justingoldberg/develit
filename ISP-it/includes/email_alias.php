<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 08/12/2003
# Ultima alteração: 22/12/2003
#    Alteração No.: 005
#
# Função:
#    Painel - Funções para controle de usuarios radius por pessoas
# 

# Função para busca de Contas por PessoaTipo
function buscaEmailAlias($texto, $campo, $tipo, $ordem) {

	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[EmailAlias] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[EmailAlias] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[EmailAlias] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[EmailAlias] WHERE $texto ORDER BY $ordem";
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
function dbEmailAlias($matriz, $tipo) {

	global $conn, $tb, $modulo, $sub, $acao;
	
	$data=dataSistema();
	
	# Sql de inclusão
	if($tipo=='incluir') {
		$sql="INSERT INTO $tb[EmailAlias] VALUES (
			0, 
			'$matriz[idEmail]', 
			'$matriz[alias]',
			'$data[dataBanco]',
			'$matriz[status]'
		)";
	} #fecha inclusao
	
	# Excluir
	elseif($tipo=='excluir') {
		$sql="DELETE FROM $tb[EmailAlias] WHERE id=$matriz[id]";
	}
	
	# Excluir
	elseif($tipo=='excluiremail') {
		$sql="DELETE FROM $tb[EmailAlias] WHERE idEmail=$matriz[id]";
	}
	
	# Alterar
	elseif($tipo=='alterar') {
		$sql="
			UPDATE 
				$tb[EmailAlias]
			SET
				valor='$senha'
			WHERE
				id='$matriz[id]'
		";
	}

	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
	
} # fecha função de gravação em banco de dados



# Função para listagem de contas  Radius por Pessoa Tipo
function emailAliasContasDominio($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $tb, $corFundo, $corBorda, $limite;
	
	$idPessoaTipo=$matriz[idPessoaTipo];
	$idModulo=$matriz[idModulo];
	$idEmail=$matriz[id];
	
	$email=dadosEmail($idEmail);
	$idDominio=$email[idDominio];
	
	# Visualizar Dominio
	verEmails($modulo, $sub, $acao, $matriz[id], $matriz);
	echo "<br>";
	
	if($matriz[txtProcurar]) {
		$sqlADD=" AND ( 
			$tb[EmailAlias].alias LIKE '%$matriz[txtProcurar]%'
		)";
	}

	if($idPessoaTipo) {
	
		$sql="
			SELECT 
				$tb[EmailAlias].id id,
				$tb[EmailAlias].idEmail idEmail,
				$tb[EmailAlias].alias alias,
				$tb[EmailAlias].dtCadastro dtCadastro,
				$tb[EmailAlias].status status
			FROM 
				$tb[EmailAlias]
			WHERE
				$tb[EmailAlias].idEmail=$idEmail
				$sqlADD
			ORDER BY
				$tb[EmailAlias].alias
		";
	
		$consulta=consultaSQL($sql, $conn);
		
		novaTabela("Alias - Apelidos de Email<a name=ancora>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
			novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('100%', 'right', $corFundo, 3, 'tabfundo1');
				novaTabela2SH("center", '100%', 0, 2, 1, $corFundo, $corBorda, 0);
					novaLinhaTabela($corFundo, '100%');
						$texto="
						<form method=post name=matriz action=index.php>
						<input type=hidden name=modulo value=$modulo>
						<input type=hidden name=sub value=$sub>
						<input type=hidden name=acao value=$acao>
						<input type=hidden name=registro value=$idPessoaTipo:$idEmail>
						<b>Procurar por:</b> <input type=text name=matriz[txtProcurar] size=25 value='$matriz[txtProcurar]'>
						<input type=submit name=matriz[bntProcurar] value=Procurar class=submit>";
						itemLinhaForm($texto, 'center','middle', $corFundo, 3, 'tabfundo1');
					fechaLinhaTabela();
				fechaTabela();		
			htmlFechaColuna();
			fechaLinhaTabela();
			
			$opcoes=htmlMontaOpcao("<a href=?modulo=administracao&sub=mail&acao=config&registro=$idPessoaTipo>Listar Dominios</a>",'dominio');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=mail&acao=listar&registro=$idPessoaTipo:$idDominio>Listar Contas</a>",'mail');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=mail&acao=aliasadicionar&registro=$idPessoaTipo:$idEmail>Adicionar</a>",'incluir');			itemTabelaNOURL($opcoes, 'right', $corFundo, 3, 'tabfundo1');
			
			if($consulta && contaConsulta($consulta)>0) {
			
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela("Alias", 'center', '40%', 'tabfundo0');
					itemLinhaTabela('Data de Cadastro', 'center', '30%', 'tabfundo0');
					itemLinhaTabela('Opções', 'center', '30%', 'tabfundo0');
				fechaLinhaTabela();
			
				for($a=0;$a<contaConsulta($consulta) && $a < $limite[lista][dominios];$a++) {
				
					$id=resultadoSQL($consulta, $a, 'id');
					$alias=resultadoSQL($consulta, $a, 'alias');
					$dtCadastro=resultadoSQL($consulta, $a, 'dtCadastro');
					
					$opcoes=htmlMontaOpcao("<a href=?modulo=administracao&sub=mail&acao=aliasexcluir&registro=$idPessoaTipo:$id>Excluir</a>",'excluir');
					
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTabela($alias, 'center', '40%', 'normal10');
						itemLinhaTabela(converteData($dtCadastro,'banco','formdata'), 'center', '30%', 'normal10');
						itemLinhaTabela($opcoes, 'left', '30%', 'normal8');
					fechaLinhaTabela();
				}
			}
			else {
				$texto="<span class=txtaviso>Não existem aliases configurados para este email!</span>";
				itemTabelaNOURL($texto, 'left', $corFundo, 3, 'normal10');
			}
		fechaTabela();
	}
}



# Funcao para cadastro de usuarios
function emailAdicionarAliasContasDominio($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	$idPessoaTipo=$matriz[idPessoaTipo];
	$idModulo=$matriz[idModulo];
	$idEmail=$matriz[id];
	
	$email=dadosEmail($idEmail);
	if(!$matriz[bntVerificar] && !$matriz[bntConfirmar]) $matriz[idDominio]=$email[idDominio];
	
	# Form de inclusao
	if(!$matriz[bntConfirmar] && 
		( $matriz[bntAlterar] 	
			|| !$matriz[bntVerificar] 
			|| !$matriz[alias] 
			|| checkContaDominio($matriz[alias], $matriz[idDominio])
			|| checkAliasDominio($matriz[alias], $matriz[idDominio])
		))	 
	{

		# Visualizar Dominio
		verEmails($modulo, $sub, $acao, $matriz[id], $matriz);
		echo "<br>";
		
		if(checkContaDominio($matriz[alias], $matriz[idDominio])) $msg="<br><span class=txtaviso>Conta já cadastrada neste domínio!</span><br><br>";
		elseif(checkAliasDominio($matriz[alias], $matriz[idDominio])) $msg="<br><span class=txtaviso>Alias já cadastrada neste domínio!</span><br><br>";
		
		# Motrar tabela de busca
		novaTabela2("[Adicionar Alias]<a href=# name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			menuOpcAdicional($modulo, $sub, $acao, $registro);				
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=matriz[idDominio] value=$matriz[idDominio]>
				<input type=hidden name=matriz[idEmail] value=$idEmail>
				<input type=hidden name=registro value=$registro:$matriz[id]>
				&nbsp;$msg";
				itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Alias: </b><br>
					<span class=normal10>Alias da conta</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[alias] size=30 value='$matriz[alias]'>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Domínio: </b>";
				htmlFechaColuna();
				itemLinhaForm(formSelectDominioEmail($matriz[idDominio],'idDominio','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Status: </b><br>
					<span class=normal10>Status do dominio</span>";
				htmlFechaColuna();
				itemLinhaForm(formSelectStatusEmails($matriz[status],'status','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
					echo "&nbsp;";
				htmlFechaColuna();
				$texto="<input type=submit name=matriz[bntVerificar] value=Verificar class=submit>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		fechaTabela();
	} #fecha form
	elseif($matriz[bntVerificar]) {
		# Visualizar Dominio
		verEmails($modulo, $sub, $acao, $matriz[id], $matriz);
		echo "<br>";
		
		# Motrar tabela de busca
		novaTabela2("[Confirmação]<a href=# name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			menuOpcAdicional($modulo, $sub, $acao, $registro);				
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro value=$registro:$matriz[id]>
				<input type=hidden name=matriz[alias] value=$matriz[alias]>
				<input type=hidden name=matriz[idDominio] value=$matriz[idDominio]>
				<input type=hidden name=matriz[idEmail] value=$matriz[idEmail]>
				<input type=hidden name=matriz[status] value=$matriz[status]>
				&nbsp;$msg";
				itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Alias: </b>";
				htmlFechaColuna();
				itemLinhaForm($matriz[alias], 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Domínio: </b>";
				htmlFechaColuna();
				itemLinhaForm(formSelectDominioEmail($matriz[idDominio],'idDominio','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Status: </b>";
				htmlFechaColuna();
				itemLinhaForm(formSelectStatusEmails($matriz[status],'status','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
					echo "&nbsp;";
				htmlFechaColuna();
				$texto="<input type=submit name=matriz[bntAlterar] value=Alterar class=submit2>
				<input type=submit name=matriz[bntConfirmar] value=Confirmar class=submit>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		fechaTabela();
	}
	elseif($matriz[bntConfirmar]) {
		# Cadastrar em banco de dados
		$grava=dbEmailAlias($matriz, 'incluir');
		
		# Verificar inclusão de registro
		if($grava) {
			
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Registro Gravado com Sucesso!";
			avisoNOURL("Aviso", $msg, 400);
			
			# Incluir Alias
			$dominio=dadosDominio($matriz[idDominio]);
			$email=dadosEmail($matriz[idEmail]);
			$matriz[dominio]=$dominio[nome];
			$matriz[login]=$email[login];
			$gravaManager=managerComando($matriz, 'emailaliasadicionar');
			
			echo "<br>";
			emailAliasContasDominio($modulo, $sub, 'alias', $registro, $matriz);
		}
	}
} # fecha funcao de inclusao de grupos



# Função para procurar conta no dominio
function checkAliasDominio($alias, $idDominio) {

	global $conn, $tb;
	
	$alias=strtolower(mailValidaConta($alias));
	
	$sql="
		SELECT
			$tb[EmailAlias].alias, 
			$tb[Emails].login 
		FROM
			$tb[EmailAlias], 
			$tb[Emails], 
			$tb[Dominios]
		WHERE 
			$tb[EmailAlias].idEmail=$tb[Emails].id 
			AND $tb[Emails].idDominio=$tb[Dominios].id 
			AND $tb[Dominios].id=$idDominio
			AND $tb[EmailAlias].alias='$alias'
	";

	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) return(1);
	else return(0);
}


# Funcao para cadastro de usuarios
function emailExcluirAliasContasDominio($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# Procurar detalhes sobre Email
	$consulta=buscaEmailAlias($matriz[id],'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		# Dados do email
		$id=resultadoSQL($consulta, 0, 'id');
		$idEmail=resultadoSQL($consulta, 0, 'idEmail');
		$alias=resultadoSQL($consulta, 0, 'alias');
		$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
		$status=resultadoSQL($consulta, 0, 'status');
		
		$email=dadosEmail($idEmail);
		
		if(!$matriz[bntExcluir]) {
			# Visualizar Dominio
			verEmails($modulo, $sub, $acao, $idEmail, $matriz);
			echo "<br>";
		
		
			# Motrar tabela de busca
			novaTabela2("[Excluir Alias]<a href=# name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, $registro);				
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$matriz[idPessoaTipo]:$id>
					<input type=hidden name=matriz[idEmail] value=$idEmail>
					<input type=hidden name=matriz[alias] value=$alias>
					<input type=hidden name=matriz[idDominio] value=$email[idDominio]>
					<input type=hidden name=matriz[id] value=$id>
					&nbsp;$msg";
					itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Alias: </b>";
					htmlFechaColuna();
					itemLinhaForm($alias, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Domínio: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectDominioEmail($email[idDominio],'idDominio','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Status: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectStatusEmails($status,'status','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();
					$texto="<input type=submit name=matriz[bntExcluir] value=Excluir class=submit>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		}
		elseif($matriz[bntExcluir]) {	
			# Cadastrar em banco de dados
			$grava=dbEmailAlias($matriz, 'excluir');
			
			# Verificar inclusão de registro
			if($grava) {
				
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Excluído com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);
				
				# Excluir Alias
				$dominio=dadosDominio($matriz[idDominio]);
				$email=dadosEmail($matriz[idEmail]);
				$matriz[dominio]=$dominio[nome];
				$matriz[login]=$email[login];
				$gravaManager=managerComando($matriz, 'emailaliasremover');
				
				echo "<br>";
				$matriz[id]=$idEmail;
				emailAliasContasDominio($modulo, $sub, 'alias', $registro, $matriz);
			}
		}
	}
	else {
		# nao encontrado
		# Mensagem de aviso
		$msg="Email não encontrado!";
		avisoNOURL("Aviso", $msg, 400);
	}
} # fecha funcao de inclusao de grupos

?>
