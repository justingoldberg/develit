<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 12/12/2003
# Ultima alteração: 23/12/2003
#    Alteração No.: 003
#
# Função:
#    Painel - Funções para controle de usuarios radius por pessoas
# 

# Função para busca de Contas por PessoaTipo
function buscaEmailForward($texto, $campo, $tipo, $ordem) {

	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[EmailForward] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[EmailForward] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[EmailForward] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[EmailForward] WHERE $texto ORDER BY $ordem";
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
function dbEmailForward($matriz, $tipo) {

	global $conn, $tb, $modulo, $sub, $acao;
	
	$data=dataSistema();
	
	# Sql de inclusão
	if($tipo=='incluir') {
		$sql="INSERT INTO $tb[EmailForward] VALUES (
			'$matriz[idEmail]', 
			'$matriz[forward]',
			'$matriz[copia]',
			'$data[dataBanco]',
			'$matriz[status]'
		)";
	} #fecha inclusao
	
	# Excluir
	elseif($tipo=='excluir') {
		$sql="DELETE FROM $tb[EmailForward] WHERE idEmail=$matriz[id]";
	}
	
	# Alterar
	elseif($tipo=='alterar') {
		$sql="
			UPDATE 
				$tb[EmailForward]
			SET
				forward='$matriz[forward]',
				copia='$matriz[copia]'
			WHERE
				idEmail='$matriz[id]'
		";
	}

	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
	
} # fecha função de gravação em banco de dados



# Função para listagem de contas  Radius por Pessoa Tipo
function emailForwardContasDominio($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $tb, $corFundo, $corBorda, $limite;
	
	$idPessoaTipo=$matriz[idPessoaTipo];
	$idModulo=$matriz[idModulo];
	$idEmail=$matriz[id];
	
	$email=dadosEmail($idEmail);
	$idDominio=$email[idDominio];
	
	if($matriz[txtProcurar]) {
		$sqlADD=" AND ( 
			$tb[EmailForward].forward LIKE '%$matriz[txtProcurar]%'
		)";
	}

	if($idPessoaTipo) {
	
		$sql="
			SELECT 
				$tb[EmailForward].idEmail idEmail,
				$tb[EmailForward].forward forward,
				$tb[EmailForward].copia copia,
				$tb[EmailForward].dtCadastro dtCadastro,
				$tb[EmailForward].status status
			FROM 
				$tb[EmailForward]
			WHERE
				$tb[EmailForward].idEmail=$idEmail
				$sqlADD
			ORDER BY
				$tb[EmailForward].dtCadastro
		";
	
		$consulta=consultaSQL($sql, $conn);
		
		if($consulta && contaConsulta($consulta)>0) {
		
			if(!$matriz[bntConfirmar] && !$matriz[bntVerificar]) {
				# Alterar
				if(!$matriz[forward]) $matriz[forward]=vpopmailFormataForward(resultadoSQL($consulta, 0, 'forward'),'form');
				else $matriz[forward]=vpopmailFormataForward($matriz[forward],'form');
				$matriz[copia]=resultadoSQL($consulta, 0, 'copia');
				$matriz[status]=resultadoSQL($consulta, 0, 'status');
				$matriz[idEmail]=resultadoSQL($consulta, 0, 'idEmail');
			}
		}
		
		# Alterar Forward
		if(!$matriz[bntExcluir] && !$matriz[bntExcluirForward]) 
			emailAdicionarForwardContasDominio($modulo, $sub, $acao, $registro, $matriz);
		else 
			emailExcluirForwardContasDominio($modulo, $sub, 'forwardexcluir', $registro, $matriz);
	}
}



# Funcao para cadastro de usuarios
function emailAdicionarForwardContasDominio($modulo, $sub, $acao, $registro, $matriz) {

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
			|| !$matriz[forward] 
		))	 
	{

		# Visualizar Dominio
		verEmails($modulo, $sub, $acao, $matriz[id], $matriz);
		echo "<br>";
		
		$matriz[forward]=htmlentities($matriz[forward]);
		
		# Motrar tabela de busca
		novaTabela2("[Adicionar Forward]<a href=# name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			menuOpcAdicional($modulo, $sub, $acao, "$registro:$email[idDominio]");
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=matriz[idDominio] value=$email[idDominio]>
				<input type=hidden name=matriz[idPessoasTipos] value=$idPessoaTipo>
				<input type=hidden name=matriz[idEmail] value=$idEmail>
				<input type=hidden name=registro value=$registro:$idEmail>
				&nbsp;$msg";
				itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Email: </b>";
				htmlFechaColuna();
				itemLinhaForm($email[login], 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Destinos: </b><br>
					<span class=normal10>(Apenas 1 destinatário por linha)</span>";
				htmlFechaColuna();
				$texto="<textarea name=matriz[forward] rows=3 cols=40>$matriz[forward]</textarea>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Cópia: </b>";
				htmlFechaColuna();
				if($matriz[copia]=='S') $opcSelect='checked';
				$texto="<input type=checkbox name=matriz[copia] value='S' $opcSelect>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Status: </b><br>
					<span class=normal10>Status do forward</span>";
				htmlFechaColuna();
				itemLinhaForm(formSelectStatusEmails($matriz[status],'status','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
					echo "&nbsp;";
				htmlFechaColuna();
				$texto="<input type=submit name=matriz[bntVerificar] value=Verificar class=submit>";
				# Botão de exclusão
				if($matriz[forward]) $texto.="&nbsp;<input type=submit name=matriz[bntExcluir] value=Excluir class=submit2>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		fechaTabela();
	} #fecha form
	elseif($matriz[bntVerificar]) {
		# Visualizar Dominio
		verEmails($modulo, $sub, $acao, $matriz[id], $matriz);
		echo "<br>";

		# Motrar tabela de busca
		novaTabela2("[Confirmação de Forward]<a href=# name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			menuOpcAdicional($modulo, $sub, $acao, "$registro:$matriz[idDominio]");				
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro value=$registro:$matriz[idEmail]>
				<input type=hidden name=matriz[forward] value='$matriz[forward]'>
				<input type=hidden name=matriz[idPessoasTipos] value='$matriz[idPessoasTipos]'>
				<input type=hidden name=matriz[idDominio] value=$matriz[idDominio]>
				<input type=hidden name=matriz[idEmail] value=$matriz[idEmail]>
				<input type=hidden name=matriz[copia] value=$matriz[copia]>
				<input type=hidden name=matriz[status] value=$matriz[status]>
				&nbsp;$msg";
				itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Forward: </b>";
				htmlFechaColuna();
				itemLinhaForm(nl2br($matriz[forward]), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Copia: </b>";
				htmlFechaColuna();
				if(!$matriz[copia]) $matriz[copia]='N';
				itemLinhaForm(formSelectSimNao($matriz[copia],'','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
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

		# Buscar forward - identificação de inclusão ou alteração
		$consulta=buscaEmailForward($matriz[idEmail], 'idEmail','igual','idEmail');
		
		if($consulta && contaConsulta($consulta)>0) {
			# Alterar
			$grava=dbEmailForward($matriz, 'alterar');
		}
		else {
			# Cadastrar em banco de dados
			$grava=dbEmailForward($matriz, 'incluir');
		}
		
		# Verificar inclusão de registro
		if($grava) {
			
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Registro Gravado com Sucesso!";
			avisoNOURL("Aviso", $msg, 400);
			echo "<br>";

			# Manager
			# Incluir Forward
			$dominio=dadosDominio($matriz[idDominio]);
			$email=dadosEmail($matriz[idEmail]);
			$matriz[dominio]=$dominio[nome];
			$matriz[login]=$email[login];
			$matriz[forward]=vpopmailFormataForward($matriz[forward],'texto');
			if(!$matriz[copia]) $matriz[copia]='N';
			$gravaManager=managerComando($matriz, 'emailforwardadicionar');
			

			$matriz[id]=$matriz[idDominio];
			emailListarContasDominios($modulo, 'mail', 'listar', "$matriz[idPessoasTipos]:$matriz[idDominio]", $matriz);
		}
		else {
			# Mensagem de aviso
			$msg="Erro ao atualizar informações de forward!";
			avisoNOURL("Aviso", $msg, 400);
		}
	}
} # fecha funcao de inclusao de grupos



# Função para procurar conta no dominio
function checkForwardDominio($alias, $idDominio) {

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
function emailExcluirForwardContasDominio($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;

	$idPessoaTipo=$matriz[idPessoaTipo];
	$idModulo=$matriz[idModulo];
	$idEmail=$matriz[id];

	# Procurar detalhes sobre Email
	$consulta=buscaEmailForward($matriz[idEmail],'idEmail','igual','idEmail');
	
	if($consulta && contaConsulta($consulta)>0) {
		# Dados do email
		$idEmail=resultadoSQL($consulta, 0, 'idEmail');
		$forward=resultadoSQL($consulta, 0, 'forward');
		$copia=resultadoSQL($consulta, 0, 'copia');
		$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
		$status=resultadoSQL($consulta, 0, 'status');
		
		$email=dadosEmail($idEmail);
		
		if(!$matriz[bntExcluirForward]) {
			# Visualizar Dominio
			verEmails($modulo, $sub, $acao, $idEmail, $matriz);
			echo "<br>";
		
		
			# Motrar tabela de busca
			novaTabela2("[Excluir Forward]<a href=# name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, "$registro:$matriz[idDominio]");				
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro:$matriz[idEmail]>
					<input type=hidden name=matriz[idPessoasTipos] value='$matriz[idPessoasTipos]'>
					<input type=hidden name=matriz[idDominio] value=$matriz[idDominio]>
					<input type=hidden name=matriz[idEmail] value=$matriz[idEmail]>
					&nbsp;$msg";
					itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('35%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Forward: </b>";
					htmlFechaColuna();
					itemLinhaForm($forward, 'left', 'top', $corFundo, 0, 'tabfundo1');
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
					$texto="<input type=submit name=matriz[bntExcluirForward] value=Excluir class=submit>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		}
		elseif($matriz[bntExcluirForward]) {	
			# Cadastrar em banco de dados
			$grava=dbEmailForward($matriz, 'excluir');
			
			# Verificar inclusão de registro
			if($grava) {
				
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Excluído com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);
				
				# Manager
				# Excluir Forward
				$dominio=dadosDominio($matriz[idDominio]);
				$email=dadosEmail($matriz[idEmail]);
				$matriz[dominio]=$dominio[nome];
				$matriz[login]=$email[login];
				$matriz[forward]=vpopmailFormataForward($matriz[forward],'texto');
				if(!$matriz[copia]) $matriz[copia]='N';
				$gravaManager=managerComando($matriz, 'emailforwardremover');
				
				echo "<br>";
				$matriz[id]=$matriz[idDominio];
				emailListarContasDominios($modulo, 'mail', 'listar', $registro, $matriz);
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
