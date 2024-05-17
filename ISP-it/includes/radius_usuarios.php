<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 16/10/2003
# Ultima alteração: 02/02/2004
#    Alteração No.: 003
#
# Função:
#    Painel - Funções para controle de serviço de radius (grupos)


# função de busca de usuários
function radiusBuscaUsuarios($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[RadiusUsuarios] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[RadiusUsuarios] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[RadiusUsuarios] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[RadiusUsuarios] WHERE $texto ORDER BY $ordem";
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



# função de busca de grupos
function radiusBuscaIDUsuario($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[RadiusUsuarios] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[RadiusUsuarios] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[RadiusUsuarios] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[RadiusUsuarios] WHERE $texto ORDER BY $ordem";
	}
	
	# Verifica consulta
	if($sql){
		$consulta=consultaSQL($sql, $conn);
		# Retornvar consulta
		return(resultadoSQL($consulta,0,'id'));
	}
	else {	
		# Mensagem de aviso
		$msg="Consulta não pode ser realizada por falta de parâmetros";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
	}
	
} # fecha função de busca de grupos




# Função para buscar ID de Novo usuario do Radius
function radiusNovoIDUsuario() {

	global $conn, $tb;
	
	$sql="
		SELECT
			MAX($tb[RadiusUsuarios].id)+1 qtde
		FROM
			$tb[RadiusUsuarios]
	";
	
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
		$retorno=resultadoSQL($consulta, 0, 'qtde');
		
		if(!$retorno || $retorno=='NULL') $retorno=1;
	}
	else {
		$retorno=1;
	}

	return($retorno);
}




# Funcao para cadastro de usuarios
function radiusAdicionarUsuarios($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# Form de inclusao
	if(!$matriz[bntAdicionar] || !$matriz[login] || !$matriz[senha]) {
		# Motrar tabela de busca
		novaTabela2("[Adicionar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			menuOpcAdicional($modulo, $sub, $acao, $registro);				
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro value=$registro>
				&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Login: </b><br>
					<span class=normal10>Login de acesso do usuário</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[login] size=20 value='$matriz[login]'>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Senha: </b><br>
					<span class=normal10>Senha de acesso</span>";
				htmlFechaColuna();
				$texto="<input type=password name=matriz[senha] size=20 value='$matriz[senha]'>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Grupo: </b><br>
					<span class=normal10>Selecione um grupo para o login</span>";
				htmlFechaColuna();
				itemLinhaForm(formRadiusSelectGrupo($matriz[idGrupo],'idGrupo','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();			
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Status: </b><br>
					<span class=normal10>Status inicial para usuário</span>";
				htmlFechaColuna();
				itemLinhaForm(formSelectStatusRadius('A','status','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "&nbsp;";
				htmlFechaColuna();
				$texto="<input type=submit name=matriz[bntAdicionar] value=Adicionar class=submit>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		fechaTabela();
	} #fecha form
	elseif($matriz[bntAdicionar]) {
		# Conferir campos
		if($matriz[login] || !$matriz[senha]) {
		
			# Novo ID de Usuario Radios
			$matriz[idRadiusUsuarios]=radiusNovoIDUsuario();

			# Cadastrar em banco de dados
			$grava=radiusDBUsuario($matriz, 'incluir');
			
			# Verificar inclusão de registro
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				aviso("Aviso", $msg, $url, 760);
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
	}
} # fecha funcao de inclusao de grupos



# Funcao para alteracao de usuarios
function radiusAlterarUsuarios($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# Form de inclusao
	if(!$matriz[bntAlterar]) {
	
		# Buscar informações sobre registro
		$consulta=radiusBuscaUsuarios($registro, 'id','igual','id');
		
		if($consulta && contaConsulta($consulta)>0) {
		
			# receber valores
			$id=resultadoSQL($consulta, $i, 'id');
			$idGrupo=resultadoSQL($consulta, $i, 'idGrupo');
			$login=resultadoSQL($consulta, $i, 'login');
			$senha=resultadoSQL($consulta, $i, 'senha');
			$senhaTexto=resultadoSQL($consulta, $i, 'senha_texto');
			$dtCadastro=resultadoSQL($consulta, $i, 'dtCadastro');
			$dtAtivacao=resultadoSQL($consulta, $i, 'dtAtivacao');
			$dtInativacao=resultadoSQL($consulta, $i, 'dtInativacao');
			$dtCancelamento=resultadoSQL($consulta, $i, 'dtCancelamento');
			$status=resultadoSQL($consulta, $i, 'status');
		
			# Motrar tabela de busca
			novaTabela2("[Alterar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, $registro);				
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=id value=$id>
					<input type=hidden name=matriz[id] value=$id>
					<input type=hidden name=acao value=$acao>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Login: </b><br>
						<span class=normal10>Login de acesso do usuário</span>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[login] size=20 value='$login'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Senha: </b><br>
						<span class=normal10>Senha de acesso</span>";
					htmlFechaColuna();
					$texto="<input type=password name=matriz[senha] size=20>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				if($idGrupo) {
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Grupo: </b><br>
							<span class=normal10>Selecione um grupo para o login</span>";
						htmlFechaColuna();
						itemLinhaForm(formRadiusSelectGrupo($idGrupo,'idGrupo','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Status: </b><br>
						<span class=normal10>Status inicial para usuário</span>";
					htmlFechaColuna();
					itemLinhaForm(formSelectStatusRadius($status,'status','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();
					$texto="<input type=submit name=matriz[bntAlterar] value=Alterar class=submit>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		}
		# registro nao encontrado
		else {
			# Mensagem de aviso
			$msg="Registro não foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=listar";
			aviso("Aviso", $msg, $url, 760);
		}
	} #fecha form
	elseif($matriz[bntAlterar]) {
		# Cadastrar em banco de dados
		$grava=radiusDBUsuario($matriz, 'alterar');
		
		# Verificar inclusão de registro
		if($grava) {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Registro Gravado com Sucesso!";
			$url="?modulo=$modulo&sub=$sub&acao=listar";
			aviso("Aviso", $msg, $url, 760);
		}
		# falta de parametros
		else {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Erro ao gravar registro!";
			$url="?modulo=$modulo&sub=$sub&acao=listar";
			aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
		}
	}
} # fecha funcao de alteracao de grupos


# Funcao para exclusao de usuarios
function radiusExcluirUsuarios($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# Form de inclusao
	if(!$matriz[bntExcluir]) {
	
		# Buscar informações sobre registro
		$consulta=radiusBuscaUsuarios($registro, 'id','igual','id');
		
		if($consulta && contaConsulta($consulta)>0) {
		
			# receber valores
			$id=resultadoSQL($consulta, 0, 'id');
			$idGrupo=resultadoSQL($consulta, 0, 'idGrupo');
			$login=resultadoSQL($consulta, 0, 'login');
			$senha=resultadoSQL($consulta, 0, 'senha');
			$senhaTexto=resultadoSQL($consulta, 0, 'senha_texto');
			$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
			$dtAtivacao=resultadoSQL($consulta, 0, 'dtAtivacao');
			$dtInativacao=resultadoSQL($consulta, 0, 'dtInativacao');
			$dtCancelamento=resultadoSQL($consulta, 0, 'dtCancelamento');
			$status=resultadoSQL($consulta, 0, 'status');
			
			# Motrar tabela de busca
			novaTabela2("[Excluir]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, "$registro:$matriz[id]");
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=id value=$id>
					<input type=hidden name=matriz[id] value=$id>
					<input type=hidden name=acao value=$acao>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Grupo: </b>";
					htmlFechaColuna();
					itemLinhaForm(formRadiusSelectGrupo($idGrupo,'','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Login: </b>";
					htmlFechaColuna();
					itemLinhaForm($login, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Senha Texto: </b>";
					htmlFechaColuna();
					itemLinhaForm($senhaTexto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();			
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Data de Cadastro: </b>";
					htmlFechaColuna();
					itemLinhaForm(converteData($dtCadastro, 'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Data Ativação: </b>";
					htmlFechaColuna();
					itemLinhaForm(converteData($dtAtivacao,'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				if(formatarData($dtInativacao)>0) {
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Data de Inativação: </b>";
						htmlFechaColuna();
						itemLinhaForm(converteData($dtInativacao,'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				if(formatarData($dtCancelamento)>0) {
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Data de Cancelamento: </b>";
						htmlFechaColuna();
						itemLinhaForm(converteData($dtCancelamento,'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Status: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectStatusRadius($status,'','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();
					$texto="<input type=submit name=matriz[bntExcluir] value=Excluir class=submit>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		}
		# registro nao encontrado
		else {
			# Mensagem de aviso
			$msg="Registro não foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=listar";
			aviso("Aviso", $msg, $url, 760);
		}
	} #fecha form
	elseif($matriz[bntExcluir]) {
	
		# Cadastrar em banco de dados
		$grava=radiusDBUsuario($matriz, 'excluir');
		
		# Verificar inclusão de registro
		if($grava) {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Registro Gravado com Sucesso!";
			$url="?modulo=$modulo&sub=$sub&acao=listar";
			aviso("Aviso", $msg, $url, 760);
		}
		# falta de parametros
		else {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Erro ao gravar registro!";
			$url="?modulo=$modulo&sub=$sub&acao=listar";
			aviso("Aviso: Ocorrência de erro", $msg, $url, 400);
		}
	}
} # fecha funcao de exclusão de grupos



# Função para gravação em banco de dados
function radiusDBUsuario($matriz, $tipo) {

	global $conn, $tb, $modulo, $sub, $acao;
	
	$data=dataSistema();
	
	# Sql de inclusão
	if($tipo=='incluir') {
		$tmpNome=strtoupper($matriz[login]);
		# Verificar se serviço existe
		$tmpBusca=radiusBuscaUsuarios("upper(login)='$tmpNome'", $campo, 'custom', 'id');
		
		# Registro já existe
		if($tmpBusca && contaConsulta($tmpBusca)>0) {
			# Mensagem de aviso
			$msg="Registro já existe no banco de dados";
			$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$matriz[idPessoaTipo]";
			aviso("Aviso: Erro ao incluir registro", $msg, $url, 760);
		}
		else {
			$data=dataSistema();
			$matriz[dtCadastro]=$data[dataBanco];
			$matriz[senhaTexto]=$matriz[senha];
			$matriz[senha]=crypt($matriz[senha]);
			
			if($matriz[status]=='A') $matriz[dtAtivacao]=$matriz[dtCadastro];
			
			$sql="INSERT INTO $tb[RadiusUsuarios] VALUES (
				'$matriz[idRadiusUsuarios]', 
				'$matriz[idGrupo]', 
				'$matriz[login]', 
				'$matriz[senha]', 
				'$matriz[senhaTexto]', 
				'$matriz[dtCadastro]', 
				'$matriz[dtAtivacao]', 
				'$matriz[dtInativacao]',
				'$matriz[dtCancelamento]',
				'$matriz[status]'
			)";
		}
	} #fecha inclusao
	
	elseif($tipo=='alterar') {
		# Verificar se serviço existe
		$tmpBusca=radiusBuscaUsuarios($matriz[id], 'id', 'igual', 'id');
				
		# Registro já existe
		if(!$tmpBusca || contaConsulta($tmpBusca)==0) {
			# Mensagem de aviso
			$msg="Registro não encontrado no banco de dados";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Erro ao alterar registro", $msg, $url, 760);
		}
		else {
		
			$data=dataSistema();
			$matriz[senhaTexto]=$matriz[senha];
			$matriz[senha]=crypt($matriz[senha]);
			
			if($matriz[status]=='A') $matriz[dtAtivacao]=$data[dataBanco];
			elseif($matriz[status]=='C') $matriz[dtCancelamento]=$data[dataBanco];
			elseif($matriz[status]=='I') $matriz[Inativacao]=$data[dataBanco];
			
			$sql="
				UPDATE $tb[RadiusUsuarios] SET 
					senha='$matriz[senha]',
					idGrupo='$matriz[idGrupo]',
					senha_texto='$matriz[senhaTexto]',
					status='$matriz[status]'
				WHERE id=$matriz[id]";
		}
	}

	elseif($tipo=='senha') {
		# Verificar se serviço existe
		$tmpBusca=radiusBuscaUsuarios($matriz[idRadiusUsuarios], 'id', 'igual', 'id');
				
		# Registro já existe
		if(!$tmpBusca || contaConsulta($tmpBusca)==0) {
			# Mensagem de aviso
			$msg="Registro não encontrado no banco de dados";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Erro ao alterar registro", $msg, $url, 760);
		}
		else {
		
			$data=dataSistema();
			$matriz[senhaTexto]=$matriz[senha];
			$matriz[senha]=crypt($matriz[senha]);
			
			if($matriz[status]=='A') $matriz[dtAtivacao]=$data[dataBanco];
			elseif($matriz[status]=='C') $matriz[dtCancelamento]=$data[dataBanco];
			elseif($matriz[status]=='I') $matriz[Inativacao]=$data[dataBanco];
			
			$sql="
				UPDATE $tb[RadiusUsuarios] SET 
					senha='$matriz[senha]',
					senha_texto='$matriz[senhaTexto]'
				WHERE id=$matriz[idRadiusUsuarios]";
		}
	}
	
	
	elseif($tipo=='ativar') {
		# Verificar se serviço existe
		$tmpBusca=radiusBuscaUsuarios($matriz[idRadiusUsuarios], 'id', 'igual', 'id');
				
		# Registro já existe
		if(!$tmpBusca || contaConsulta($tmpBusca)==0) {
			# Mensagem de aviso
			$msg="Registro não encontrado no banco de dados";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Erro ao alterar registro", $msg, $url, 760);
		}
		else {
		
			$data=dataSistema();
			$matriz[senhaTexto]=$matriz[senha];
			$matriz[senha]=crypt($matriz[senha]);
			
			if($matriz[status]=='A') $matriz[dtAtivacao]=$data[dataBanco];
			elseif($matriz[status]=='C') $matriz[dtCancelamento]=$data[dataBanco];
			elseif($matriz[status]=='I') $matriz[Inativacao]=$data[dataBanco];
			
			$sql="
				UPDATE $tb[RadiusUsuarios] SET 
					status='A',
					dtCancelamento='',
					dtAtivacao='$matriz[dtAtivacao]'
				WHERE id=$matriz[idRadiusUsuarios]";
		}
	}

	elseif($tipo=='inativar') {
		# Verificar se serviço existe
		$tmpBusca=radiusBuscaUsuarios($matriz[idRadiusUsuarios], 'id', 'igual', 'id');
				
		# Registro já existe
		if(!$tmpBusca || contaConsulta($tmpBusca)==0) {
			# Mensagem de aviso
			$msg="Registro não encontrado no banco de dados";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Erro ao alterar registro", $msg, $url, 760);
		}
		else {
		
			$data=dataSistema();
			$matriz[senhaTexto]=$matriz[senha];
			$matriz[senha]=crypt($matriz[senha]);
			
			if($matriz[status]=='A') $matriz[dtAtivacao]=$data[dataBanco];
			elseif($matriz[status]=='C') $matriz[dtCancelamento]=$data[dataBanco];
			elseif($matriz[status]=='I') $matriz[Inativacao]=$data[dataBanco];
			
			$sql="
				UPDATE $tb[RadiusUsuarios] SET 
					status='I',
					dtCancelamento='',
					dtInativacao='$matriz[dtInativacao]'
				WHERE id=$matriz[idRadiusUsuarios]";
		}
	}
	
	elseif($tipo=='cancelar') {
		# Verificar se serviço existe
		$tmpBusca=radiusBuscaUsuarios($matriz[idRadiusUsuarios], 'id', 'igual', 'id');
				
		# Registro já existe
		if(!$tmpBusca || contaConsulta($tmpBusca)==0) {
			# Mensagem de aviso
			$msg="Registro não encontrado no banco de dados";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Erro ao alterar registro", $msg, $url, 760);
		}
		else {
		
			$data=dataSistema();
			$matriz[senhaTexto]=$matriz[senha];
			$matriz[senha]=crypt($matriz[senha]);
			
			if($matriz[status]=='A') $matriz[dtAtivacao]=$data[dataBanco];
			elseif($matriz[status]=='C') $matriz[dtCancelamento]=$data[dataBanco];
			elseif($matriz[status]=='I') $matriz[Inativacao]=$data[dataBanco];
			
			$sql="
				UPDATE $tb[RadiusUsuarios] SET 
					status='C',
					dtCancelamento='$matriz[dtCancelamento]',
				WHERE id=$matriz[idRadiusUsuarios]";
		}
	}

	elseif($tipo=='excluir') {
		# Verificar se usuario existe
		$tmpServico=radiusBuscaUsuarios($matriz[idRadiusUsuarios], 'id', 'igual', 'id');
		
		# Registro já existe
		if(!$tmpServico || contaConsulta($tmpServico)==0) {
			# Mensagem de aviso
			$msg="Registro não existe no banco de dados";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Erro ao alterar registro", $msg, $url, 760);
		}
		else {
			$sql="DELETE FROM $tb[RadiusUsuarios] WHERE id=$matriz[idRadiusUsuarios]";
			
		}
	}

	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
	
} # fecha função de gravação em banco de dados


# Listar grupos
function radiusListarUsuarios($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite;

	# Cabeçalho
	# Motrar tabela de busca
	novaTabela("[Listar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
	
	# Seleção de registros
	$consulta=radiusBuscaUsuarios($texto, $campo, 'todos','login');
	
	if($consulta && contaConsulta($consulta)>0) {
		$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
		$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
		itemTabelaNOURL($opcoes, 'right', $corFundo, 5, 'tabfundo1');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Não há registros
			itemTabelaNOURL('Não há registros cadastrados', 'left', $corFundo, 6, 'txtaviso');
		}
		else {
		
			# Paginador
			paginador($consulta, contaConsulta($consulta), $limite[lista][radius_usuarios], $registro, 'normal', 5, $urlADD);
		
			# Cabeçalho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Login', 'center', '25%', 'tabfundo0');
				itemLinhaTabela('Grupo', 'center', '20%', 'tabfundo0');
				itemLinhaTabela('Cadastro', 'center', '20%', 'tabfundo0');
				itemLinhaTabela('Status', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Opções', 'center', '25%', 'tabfundo0');
			fechaLinhaTabela();
			
			# Setar registro inicial
			if(!$registro) {
				$i=0;
			}
			elseif($registro && is_numeric($registro) ) {
				$i=$registro;
			}
			else {
				$i=0;
			}
			
			$limite=$i+$limite[lista][radius_usuarios];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$idGrupo=resultadoSQL($consulta, $i, 'idGrupo');
				$login=resultadoSQL($consulta, $i, 'login');
				$dtCadastro=resultadoSQL($consulta, $i, 'dtCadastro');
				$status=resultadoSQL($consulta, $i, 'status');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=ver&registro=$id>Ver</a>",'ver');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($login, 'center', '25%', 'normal10');
					itemLinhaTabela(formRadiusSelectGrupo($idGrupo,'','check'), 'center', '20%', 'normal10');
					itemLinhaTabela(converteData($dtCadastro, 'banco','form'), 'center', '20%', 'normal10');
					itemLinhaTabela(formSelectStatusRadius($status, '','check'),'center', '10%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '25%', 'normal10');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem
	}
	else {
		itemTabelaNOURL('Não foram encontrados registros cadastrados', 'left', $corFundo, 5, 'txtaviso');
	}

	fechaTabela();

} # fecha função de listagem



# Função para procura 
function radiusProcurarUsuarios($modulo, $sub, $acao, $registro, $matriz)
{
	global $conn, $tb, $corFundo, $corBorda, $html, $limite, $textoProcurar;
	
	# Atribuir valores a variável de busca
	if(!$matriz) {
		$matriz[bntProcurar]=1;
		$matriz[txtProcurar]=$textoProcurar;
	} #fim da atribuicao de variaveis
	
	# Motrar tabela de busca
	novaTabela2("[Procurar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('30%', 'right', $corFundo, 0, 'tabfundo1');
			echo "<b>Procurar por:</b>";
			htmlFechaColuna();
			$texto="
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=procurar>
			<input type=text name=matriz[txtProcurar] size=30 value='$matriz[txtProcurar]'>
			<input type=submit name=matriz[bntProcurar] value=Procurar>";
			itemLinhaForm($texto, 'left','middle', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
	fechaTabela();

	# Caso botão procurar seja pressionado
	if( $matriz[bntProcurar] && $matriz[txtProcurar] ) {
		#buscar registros
		$consulta=RadiusBuscaUsuarios("upper(login) like '%$matriz[txtProcurar]%'",$campo, 'custom','login');

		echo "<br>";

		novaTabela("[Listar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 10);
	
		if(!$consulta || contaConsulta($consulta)==0 ) {
			# Não há registros
			itemTabelaNOURL('Não foram encontrados registros cadastrados', 'left', $corFundo, 10, 'txtaviso');
		}
		elseif($consulta && contaConsulta($consulta)>0 && (is_integer($registro) || !$registro)) {	
		
			itemTabelaNOURL('Registros encontrados procurando por ('.$matriz[txtProcurar].'): '.contaConsulta($consulta).' registro(s)', 'left', $corFundo, 10, 'txtaviso');

			# Paginador
			$urlADD="&textoProcurar=".$matriz[txtProcurar];
			paginador($consulta, contaConsulta($consulta), $limite[lista][radius_usuarios], $registro, 'normal', 10, $urlADD);


			# Cabeçalho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Login', 'center', '25%', 'tabfundo0');
				itemLinhaTabela('Grupo', 'center', '20%', 'tabfundo0');
				itemLinhaTabela('dtCadastro', 'center', '20%', 'tabfundo0');
				itemLinhaTabela('Status', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Opções', 'center', '25%', 'tabfundo0');
			fechaLinhaTabela();
			
			# Setar registro inicial
			if(!$registro) {
				$i=0;
			}
			elseif($registro && is_numeric($registro) ) {
				$i=$registro;
			}
			else {
				$i=0;
			}
			
			$limite=$i+$limite[lista][radius_usuarios];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$idGrupo=resultadoSQL($consulta, $i, 'idGrupo');
				$login=resultadoSQL($consulta, $i, 'login');
				$dtCadastro=resultadoSQL($consulta, $i, 'dtCadastro');
				$status=resultadoSQL($consulta, $i, 'status');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=ver&registro=$id>Ver</a>",'ver');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($login, 'center', '25%', 'normal10');
					itemLinhaTabela(formRadiusSelectGrupo($idGrupo,'','check'), 'center', '20%', 'normal10');
					itemLinhaTabela(converteData($dtCadastro, 'banco','form'), 'center', '20%', 'normal10');
					itemLinhaTabela(formSelectStatusRadius($status, '','check'),'center', '10%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '25%', 'normal10');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem
	} # fecha botão procurar
} #fecha funcao de  procura



# Função para visualizar as informações do servidor
function radiusVerUsuario($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $corFundo, $corBorda, $tb, $html, $sessCadastro;
	
	# Mostar informações sobre Servidor
	$consulta=radiusBuscaUsuarios($registro, 'id','igual','id');
	
	#nova tabela para mostrar informações
	novaTabela2('Informações sobre Usuário', 'center', '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	
	if($consulta && contaConsulta($consulta)>0) {
		$id=resultadoSQL($consulta, $i, 'id');
		$idGrupo=resultadoSQL($consulta, $i, 'idGrupo');
		$login=resultadoSQL($consulta, $i, 'login');
		$senha=resultadoSQL($consulta, $i, 'senha');
		$senhaTexto=resultadoSQL($consulta, $i, 'senha_texto');
		$dtCadastro=resultadoSQL($consulta, $i, 'dtCadastro');
		$dtAtivacao=resultadoSQL($consulta, $i, 'dtAtivacao');
		$dtInativacao=resultadoSQL($consulta, $i, 'dtInativacao');
		$dtCancelamento=resultadoSQL($consulta, $i, 'dtCancelamento');
		$status=resultadoSQL($consulta, $i, 'status');
		
		# Opcoes Adicionais
		menuOpcAdicional($modulo, $sub, $acao, "$sessCadastro[idPessoaTipo]:$matriz[id]");
		
		# Vefificar e mostrar permissões
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b class=bold10>Login: </b>";
			htmlFechaColuna();
			itemLinhaForm($login, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b class=bold10>Senha Texto:</b>";
			htmlFechaColuna();
			itemLinhaForm($senhaTexto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b class=bold10>Data de Cadastro: </b>";
			htmlFechaColuna();
			itemLinhaForm(converteData($dtCadastro, 'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();			
		if(formatarData($dtAtivacao)>0) {
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Data de Ativação: </b>";
				htmlFechaColuna();
				itemLinhaForm(converteData($dtAtivacao,'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		}
		if(formatarData($dtInativacao)>0) {
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Data de Inativação: </b>";
				htmlFechaColuna();
				itemLinhaForm(converteData($dtInativacao,'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		}
		if(formatarData($dtCancelamento)>0) {
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Data de Cancelamento: </b>";
				htmlFechaColuna();
				itemLinhaForm(converteData($dtCancelamento,'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		}
	
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b class=bold10>Status: </b>";
			htmlFechaColuna();
			itemLinhaForm(formSelectStatusRadius($status,'','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		
		//htmlFechaColuna();
		//fechaLinhaTabela();
		# fecha linha
	}
	else {
		itemTabelaNOURL('Registro não encontrado!', 'left', $corFundo, 2, 'txtaviso');		
	}
	
	fechaTabela();	
	# fim da tabela
	
} #fecha visualizacao



# Função para busca de dados do usuario
function dadosRadius($id) {

	$consulta=radiusBuscaUsuarios($id,'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		$retorno[id]=resultadoSQL($consulta, 0, 'id');
		$retorno[idGrupo]=resultadoSQL($consulta, 0, 'idGrupo');
		$retorno[login]=resultadoSQL($consulta, 0, 'login');
		$retorno[senha_texto]=resultadoSQL($consulta, 0, 'senha_texto');
		$retorno[dtCadastro]=resultadoSQL($consulta, 0, 'dtCadastro');
		$retorno[dtAtivacao]=resultadoSQL($consulta, 0, 'dtAtivacao');
		$retorno[dtInativacao]=resultadoSQL($consulta, 0, 'dtInativacao');
		$retorno[dtCancelamento]=resultadoSQL($consulta, 0, 'dtCancelamento');
		$retorno[status]=resultadoSQL($consulta, 0, 'status');
	}
	
	return($retorno);
}

?>
