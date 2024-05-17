<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 09/01/2004
# Ultima alteração: 10/01/2005
#    Alteração No.: 002
#
# Função:
#    Painel - Funções para cadastro


# função de busca 
function buscaUsers($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[Users] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[Users] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[Users] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[Users] WHERE $texto ORDER BY $ordem";
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



# Função para retornar o ID do modulo para parametro
function buscaIDUser($texto, $campo, $tipo, $retorno) {

	$consulta=buscaUsers($texto, $campo, $tipo, '');
	
	if($consulta && contaConsulta($consulta)>0) {
		# retornar
		$retorno=resultadoSQL($consulta, 0, $retorno);
	}
	
	return($retorno);

}


# Função para listagem 
function listarUsers($modulo, $sub, $acao, $registro, $matriz)
{

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite;

	# Seleção de registros
	$consulta=buscaMaquinas($registro, 'id', 'igual', 'id');
	
	echo "<br>";
	
	if(!$consulta || contaConsulta($consulta)==0) {
		# Servidor não encontrado
		itemTabelaNOURL('Não há registros cadastrados!', 'left', $corFundo, 3, 'txtaviso');
	}
	else {
		# Mostrar Informações sobre Servidor
		verMaquina($modulo, $sub, $acao, $registro, $matriz);
		echo "<br>";
		
		$consulta=buscaUsers($registro, 'idMaquina','igual','idMaquina');
		
		# Cabeçalho		
		# Motrar tabela de busca
		novaTabela("[Usuários de Acesso]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
		$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar&registro=$registro>Adicionar</a>",'incluir');
		itemTabelaNOURL($opcoes, 'right', $corFundo, 3, 'tabfundo1');
		
		
		# Caso não hajam servicos para o servidor
		if(!$consulta || contaConsulta($consulta)==0) {
			# Não há registros
			itemTabelaNOURL('Não há usuários configurados nesta  máquina', 'left', $corFundo, 3, 'txtaviso');
		}
		else {

			# Cabeçalho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Usuário', 'center', '50%', 'tabfundo0');
				itemLinhaTabela('Senha', 'center', '20%', 'tabfundo0');
				itemLinhaTabela('Opções', 'center', '30%', 'tabfundo0');
			fechaLinhaTabela();

			for($i=0;$i<contaConsulta($consulta);$i++) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$idMaquina=resultadoSQL($consulta, $i, 'idMaquina');
				$usuario=resultadoSQL($consulta, $i, 'usuario');
				$senha=resultadoSQL($consulta, $i, 'senha');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=usuarios&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=usuarios&acao=excluir&registro=$id>Excluir</a>",'excluir');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($usuario, 'center', '40%', 'normal10');
					itemLinhaTabela(base64_decode($senha), 'center', '30%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '30%', 'normal10');
				fechaLinhaTabela();
				
			} #fecha laco de montagem de tabela
			
			# Timeout de visualização da pagina
			$parametros=carregaParametros();
			$timeout=$parametros[passphrase_timeout];
			
			echo "<meta http-equiv=refresh content=$timeout;URL=index.php?modulo=maquina&sub=programas&acao=listar&registro=$registro>";
			
			fechaTabela();
			
		} #fecha servicos encontrados
	} #fecha listagem

	
}#fecha função de listagem



# Funcao para cadastro de servicos
function adicionarUsers($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin;
	
	echo "<br>";
	
	# Form de inclusao
	if(!$matriz[bntAdicionar] || !$matriz[user] || !$matriz[passwd]) {

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
			novaTabela2("[Adicionar Usuário]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				//menuOpcAdicional($modulo, $sub, $acao, $registro);				
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
						echo "<b>Login: </b><br>
						<span class=normal10>Login do usuário</span>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[user] size=40>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Senha: </b><br>
						<span class=normal10>Senha de acesso</span>";
					htmlFechaColuna();
					$texto="<input type=password name=matriz[passwd] size=40>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit name=matriz[bntAdicionar] value=Adicionar class=submit>";
					itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		} # fecha servidor informado para cadastro
	}
	elseif($matriz[bntAdicionar]) {
		# Cadastrar em banco de dados
		$grava=dbUser($matriz, 'incluir');
			
		# Verificar inclusão de registro
		if($grava) {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Registro Gravado com Sucesso!";
			avisoNOURL("Aviso", $msg, 400);
			
			listarUsers($modulo, $sub, 'listar', $matriz[maquina], '');
		}
	}

} # fecha funcao de inclusao de servicos


# Funcao para exclusão de servicos
function excluirUsers($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	$consulta=buscaUsers($registro, 'id', 'igual', 'id');
	
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
			$usuario=resultadoSQL($consulta, 0, 'usuario');
	
			# Motrar tabela de busca
			novaTabela2("[Excluir Usuário]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
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
						echo "<b>Login: </b>";
					htmlFechaColuna();
					itemLinhaForm($usuario, 'left', 'top', $corFundo, 0, 'tabfundo1');
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
			$grava=dbUser($matriz, 'excluir');
				
			# Verificar inclusão de registro
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Excluído com Sucesso!";
				$url="?modulo=$modulo&sub=usuarios$sub&acao=listar&registro=$matriz[idMaquina]";
				avisoNOURL("Aviso", $msg, 400);
			}
			
			listarUsers($modulo, $sub, 'listar', $matriz[idMaquina], '');
		}
	}
	# Programa não encontrado
	else {
		# Mensagem de aviso
		$msg="Usuário não encontrado!";
		$url="?modulo=maquina&sub=&acao=listar";
		aviso("Aviso: Ocorrência de erro", $msg, $url, 400);
	}
} # fecha funcao de exclusão




# Funcao para exclusão de servicos
function alterarUsers($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	$consulta=buscaUsers($registro, 'id', 'igual', 'id');
	
	echo "<br>";
	
	if($consulta && contaConsulta($consulta)>0) {
		
		$idMaquina=resultadoSQL($consulta, 0, 'idMaquina');
		
		# Form de exclusão
		if(!$matriz[bntAlterar]) {
	
			# Visualizar Maquina
			verMaquina($modulo, $sub, $acao, $idMaquina, '');
			echo "<br>";
	
			$id=resultadoSQL($consulta, 0, 'id');
			$idMaquina=resultadoSQL($consulta, 0, 'idMaquina');
			
			if(!$matriz[bntAlterar]) {
				$matriz[user]=resultadoSQL($consulta, 0, 'usuario');
				$matriz[passwd]=resultadoSQL($consulta, 0, 'senha');
			}
	
			# Motrar tabela de busca
			novaTabela2("[Alterar Usuário]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
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
						echo "<b>Login: </b>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[user] value='$matriz[user]' size=40>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Senha: </b>";
					htmlFechaColuna();
					$texto="<input type=password name=matriz[passwd] size=40>";
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
			$grava=dbUser($matriz, 'alterar');
				
			# Verificar inclusão de registro
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Alterado com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);
			}
			
			listarUsers($modulo, $sub, 'listar', $matriz[idMaquina], '');
		}
	}
	# Programa não encontrado
	else {
		# Mensagem de aviso
		$msg="Programa não encontrado!";
		$url="?modulo=maquina&sub=&acao=listar";
		aviso("Aviso: Ocorrência de erro", $msg, $url, 400);
	}
} # fecha funcao de exclusão


# Função para gravação em banco de dados
function dbUser($matriz, $tipo)
{
	global $conn, $tb, $modulo, $sub, $acao, $sessLogin;
	
	$data=dataSistema();
	$idUsuario=buscaIDUsuario($sessLogin[login],'login','igual','id');
	$matriz[passwd]=base64_encode($matriz[passwd]);
	
	# Sql de inclusão
	if($tipo=='incluir') {
		$sql="
			INSERT INTO 
				$tb[Users] 
			VALUES (
				'0',
				'$matriz[maquina]',
				'$matriz[user]', 
				'$matriz[passwd]'
			)";
	} #fecha inclusao
	
	elseif($tipo=='excluir') {
		$sql="
			DELETE FROM 
				$tb[Users] 
			WHERE 
				id=$matriz[id]";
	}
	
	elseif($tipo=='excluirmaquina') {
		$sql="
			DELETE FROM 
				$tb[Users] 
			WHERE 
				idMaquina=$matriz[id]";
	}

	elseif($tipo=='alterar') {
		$sql="
			UPDATE 
				$tb[Users] 
			SET
				usuario='$matriz[user]',
				senha='$matriz[passwd]'
			WHERE 
				id=$matriz[id]";
	}
	
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
	
} # fecha função de gravação em banco de dados

?>
