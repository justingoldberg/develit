<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 24/01/2003
# Ultima altera��o: 23/09/2003
#    Altera��o No.: 007
#
# Fun��o:
#    Fun��es de grupos de usuarios

# Menu principal de grupos
# Fun��o para cadastro de grupos
function cadastroGrupos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;

	# Buscar informa��es sobre usuario - permiss�es
	$sessLogin = $_SESSION["sessLogin"];
	
	$permissao=buscaPermissaoUsuario($sessLogin[login]);

	if(!$permissao[admin]) {
		# SEM PERMISS�O DE EXECUTAR A FUN��O
		$msg="ATEN��O: Voc� n�o tem permiss�o para executar esta fun��o";
		$url="?modulo=$modulo&sub=$sub&acao=$acao";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
	
		# Topo da tabela - Informa��es e menu principal do Cadastro
		novaTabela2("[Cadastro de Grupos]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 4);
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
					echo "<br><img src=".$html[imagem][cadastro]." border=0 align=left><b class=bold>Grupos</b>
					<br><span class=normal10>O cadastro de grupos prov� o controle de grupos e limita��es
					de acesso, para usu�rios de grupos.</span>";
				htmlFechaColuna();			
				$texto=htmlMontaOpcao("<br>Adicionar", 'incluir');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=adicionar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Procurar", 'procurar');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=procurar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Listar", 'listar');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=listar", 'center', $corFundo, 0, 'normal');
			fechaLinhaTabela();
		fechaTabela();
		
		# Mostrar Status para n�o seja informada a a��o
		if(!$acao) {
			# Mostrar Status
			itemTabelaNOURL("&nbsp;", 'left', $corFundo, 0, 'normal');
			listarGrupos($modulo, $sub, $acao, $registro, $matriz);
		}
	
		# Inclus�o
		elseif($acao=="adicionar") {
			echo "<br>";
			adicionarGrupos($modulo, $sub, $acao, $registro, $matriz);
		}
		# Lista
		elseif($acao=="listar") {
			echo "<br>";
			listarGrupos($modulo, $sub, $acao, $registro, $matriz);
		}
		# Procurar
		elseif($acao=="procurar") {
			echo "<br>";
			procurarGrupos($modulo, $sub, $acao, $registro, $matriz);
		}
		# Alterar
		elseif($acao=="alterar") {
			echo "<br>";
			alterarGrupos($modulo, $sub, $acao, $registro, $matriz);
		}
		# Excluir
		elseif($acao=="excluir") {
			echo "<br>";
			excluirGrupos($modulo, $sub, $acao, $registro, $matriz);
		}
		# Usuarios
		elseif($acao=="usuarios") {
			echo "<br>";
			listarUsuariosGrupo($modulo, $sub, $acao, $registro, $matriz);
		}
		# Usuarios Adicionar
		elseif($acao=="usuariosadicionar") {
			echo "<br>";
			adicionarUsuariosGrupo($modulo, $sub, $acao, $registro, $matriz);
		}
		# Usu�rios Excluir 
		elseif($acao=="usuariosexcluir") {
			echo "<br>";
			excluirUsuariosGrupo($modulo, $sub, $acao, $registro, $matriz);
		}
	}
}



# Fu��o para visualiza��o de status
function verStatusGrupos()
{
	global $conn, $tb, $corFundo, $corBorda, $html;

	# Motrar tabela de busca
	novaTabela2("[Informa��es sobre Cadastro de Grupos]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('60%', 'left', $corFundo, 0, 'tabfundo1');
			echo "<br><img src=".$html[imagem][status]." border=0 align=left><b class=bold>Status dos Grupos</b><br>
			<span class=normal10>Status e informa��es sobre o cadastro de grupos.";
			htmlFechaColuna();
			htmlAbreColuna('10', 'left', $corFundo, 0, 'normal');
			echo "&nbsp;";
			htmlFechaColuna();
			
			
			htmlAbreColuna('40%', 'left', $corFundo, 0, 'normal');
				# Mostrar status dos servi�os
				$consulta=buscaGrupos($texto, $campo, 'todos', 'id');
				if($consulta) {
					$numConsulta=contaConsulta($consulta);
				}
				else {
					$numConsulta=0;
				}
				
				htmlAbreTabelaSH('left', '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				novaLinhaTabela($corFundo, '100%');
					itemLinhaNOURL('N�mero de Registros:', 'right', $corFundo, $colunas, 'bold10');
					itemLinhaNOURL("$numConsulta grupos cadastrados", 'left', $corFundo, $colunas, 'normal10');
				fechaLinhaTabela();
				
			
			htmlFechaColuna();
		fechaLinhaTabela();
	fechaTabela();	
} #fecha status do cadastro de usuarios





# fun��o de busca de grupos
function buscaGrupos($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[Grupos] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[Grupos] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[Grupos] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[Grupos] WHERE $texto ORDER BY $ordem";
	}
	
	# Verifica consulta
	if($sql){
		$consulta=consultaSQL($sql, $conn);
		# Retornvar consulta
		return($consulta);
	}
	else {	
		# Mensagem de aviso
		$msg="Consulta n�o pode ser realizada por falta de par�metros";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
	}
	
} # fecha fun��o de busca de grupos


# fun��o de busca de grupos
function buscaIDGrupo($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[Grupos] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[Grupos] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[Grupos] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[Grupos] WHERE $texto ORDER BY $ordem";
	}
	
	# Verifica consulta
	if($sql){
		$consulta=consultaSQL($sql, $conn);
		# Retornvar consulta
		return(resultadoSQL($consulta,0,'id'));
	}
	else {	
		# Mensagem de aviso
		$msg="Consulta n�o pode ser realizada por falta de par�metros";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
	}
	
} # fecha fun��o de busca de grupos




# Funcao para cadastro de usuarios
function adicionarGrupos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# Form de inclusao
	if(!$matriz[bntAdicionar]) {
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
				<input type=hidden name=acao value=$acao>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold>Nome: </b><br>
					<span class=normal10>Nome do grupo</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[nome] size=20>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaForm('&nbsp;', 'left', 'top', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaForm('<b>Permiss�es</b>', 'center', 'top', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold>Administrador: </b>";
				htmlFechaColuna();
				$texto="<input type=checkbox name=matriz[admin] value=S><span class=normal10>(Acesso Total)</span>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold>Incluir: </b>";
				htmlFechaColuna();
				$texto="<input type=checkbox name=matriz[incluir] value=S><span class=normal10>(Acesso a Inclus�o)</span>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold>Alterar: </b>";
				htmlFechaColuna();
				$texto="<input type=checkbox name=matriz[alterar] value=S><span class=normal10>(Acesso Altera��o)</span>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold>Excluir: </b>";
				htmlFechaColuna();
				$texto="<input type=checkbox name=matriz[excluir] value=S><span class=normal10>(Acesso Exclus�o)</span>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold>Visualizar: </b>";
				htmlFechaColuna();
				$texto="<input type=checkbox name=matriz[visualizar] value=S><span class=normal10>(Acesso a Visualiza��o de Detalhes)</span>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold>Abrir: </b>";
				htmlFechaColuna();
				$texto="<input type=checkbox name=matriz[abrir] value=S><span class=normal10>(Acesso a Abrir Tickets)</span>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold>Fechar: </b>";
				htmlFechaColuna();
				$texto="<input type=checkbox name=matriz[fechar] value=S><span class=normal10>(Acesso a Fechar Tickets)</span>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold>Comentar: </b>";
				htmlFechaColuna();
				$texto="<input type=checkbox name=matriz[comentar] value=S><span class=normal10>(Acesso a Comentar Tickets)</span>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
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
		if($matriz[nome]) {
			# Cadastrar em banco de dados
			$grava=dbGrupo($matriz, 'incluir');
			
			# Verificar inclus�o de registro
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
			$msg="Falta de par�metros necess�rios. Informe os campos obrigat�rios e tente novamente";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
		}
	}
} # fecha funcao de inclusao de grupos



# Funcao para alteracao de usuarios
function alterarGrupos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# Form de inclusao
	if(!$matriz[bntAlterar]) {
	
		# Buscar informa��es sobre registro
		$consulta=buscaGrupos($registro, 'id','igual','id');
		if($consulta && contaConsulta($consulta)>0) {
		
			# receber valores
			$id=resultadoSQL($consulta, 0, 'id');
			$nome=resultadoSQL($consulta, 0, 'nome');
			$admin=resultadoSQL($consulta, 0, 'admin');
			if($admin) $opcAdmin='checked';
			$incluir=resultadoSQL($consulta, 0, 'incluir');
			if($incluir) $opcIncluir='checked';
			$alterar=resultadoSQL($consulta, 0, 'alterar');
			if($alterar) $opcAlterar='checked';
			$excluir=resultadoSQL($consulta, 0, 'excluir');
			if($excluir) $opcExcluir='checked';
			$visualizar=resultadoSQL($consulta, 0, 'visualizar');
			if($visualizar) $opcVisualizar='checked';
			$abrir=resultadoSQL($consulta, 0, 'abrir');
			if($abrir) $opcAbrir='checked';
			$fechar=resultadoSQL($consulta, 0, 'fechar');
			if($fechar) $opcFechar='checked';
			$comentar=resultadoSQL($consulta, 0, 'comentar');
			if($comentar) $opcComentar='checked';
		
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
						echo "<b class=bold>Nome: </b><br>
						<span class=normal10>Nome do grupo</span>";
					htmlFechaColuna();
					itemLinhaForm($nome, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaForm('&nbsp;', 'left', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaForm('<b>Permiss�es</b>', 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Administrador: </b>";
					htmlFechaColuna();
					$texto="<input type=checkbox name=matriz[admin] value=S $opcAdmin>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Incluir: </b>";
					htmlFechaColuna();
					$texto="<input type=checkbox name=matriz[incluir] value=S $opcIncluir>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Alterar: </b>";
					htmlFechaColuna();
					$texto="<input type=checkbox name=matriz[alterar] value=S $opcAlterar>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Excluir: </b>";
					htmlFechaColuna();
					$texto="<input type=checkbox name=matriz[excluir] value=S $opcExcluir>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Visualizar: </b>";
					htmlFechaColuna();
					$texto="<input type=checkbox name=matriz[visualizar] value=S $opcVisualizar>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Abrir: </b>";
					htmlFechaColuna();
					$texto="<input type=checkbox name=matriz[abrir] value=S $opcAbrir>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Fechar: </b>";
					htmlFechaColuna();
					$texto="<input type=checkbox name=matriz[fechar] value=S $opcFechar>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Comentar: </b>";
					htmlFechaColuna();
					$texto="<input type=checkbox name=matriz[comentar] value=S $opcComentar>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
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
			$msg="Registro n�o foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=listar";
			aviso("Aviso", $msg, $url, 760);
		}
	} #fecha form
	elseif($matriz[bntAlterar]) {
		# Cadastrar em banco de dados
		$grava=dbGrupo($matriz, 'alterar');
		
		# Verificar inclus�o de registro
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
			aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
		}
	}
} # fecha funcao de alteracao de grupos



# Funcao para exclusao de usuarios
function excluirGrupos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# Form de inclusao
	if(!$matriz[bntExcluir]) {
	
		# Buscar informa��es sobre registro
		$consulta=buscaGrupos($registro, 'id','igual','id');
		if($consulta && contaConsulta($consulta)>0) {
		
			# receber valores
			$id=resultadoSQL($consulta, 0, 'id');
			$nome=resultadoSQL($consulta, 0, 'nome');
			$admin="&nbsp;".resultadoSQL($consulta, 0, 'admin');
			$incluir="&nbsp;".resultadoSQL($consulta, 0, 'incluir');
			$alterar="&nbsp;".resultadoSQL($consulta, 0, 'alterar');
			$excluir="&nbsp;".resultadoSQL($consulta, 0, 'excluir');
			$visualizar="&nbsp;".resultadoSQL($consulta, 0, 'visualizar');
			$abrir="&nbsp;".resultadoSQL($consulta, 0, 'abrir');
			$fechar="&nbsp;".resultadoSQL($consulta, 0, 'fechar');
			$comentar="&nbsp;".resultadoSQL($consulta, 0, 'comentar');

			
			# Motrar tabela de busca
			novaTabela2("[Excluir]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
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
						echo "<b class=bold>Nome: </b><br>
						<span class=normal10>Nome do grupo</span>";
					htmlFechaColuna();
					itemLinhaForm($nome, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaForm('&nbsp;', 'left', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaForm('<b>Permiss�es</b>', 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Administrador: </b>";
					htmlFechaColuna();
					itemLinhaForm($admin, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Incluir: </b>";
					htmlFechaColuna();
					itemLinhaForm($incluir, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Alterar: </b>";
					htmlFechaColuna();					
					itemLinhaForm($alterar, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Excluir: </b>";
					htmlFechaColuna();					
					itemLinhaForm($excluir, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Visualizar: </b>";
					htmlFechaColuna();
					itemLinhaForm($visualizar, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Abrir: </b>";
					htmlFechaColuna();
					itemLinhaForm($abrir, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Fechar: </b>";
					htmlFechaColuna();
					itemLinhaForm($fechar, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Comentar: </b>";
					htmlFechaColuna();
					itemLinhaForm($comentar, 'left', 'top', $corFundo, 0, 'tabfundo1');
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
			$msg="Registro n�o foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=listar";
			aviso("Aviso", $msg, $url, 760);
		}
	} #fecha form
	elseif($matriz[bntExcluir]) {
		# Cadastrar em banco de dados
		$grava=dbGrupo($matriz, 'excluir');
		
		# Verificar inclus�o de registro
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
			aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
		}
	}
} # fecha funcao de exclus�o de grupos



# Fun��o para grava��o em banco de dados
function dbGrupo($matriz, $tipo)
{
	global $conn, $tb, $modulo, $sub, $acao;
	
	# Sql de inclus�o
	if($tipo=='incluir') {
		$tmpNome=strtoupper($matriz[nome]);
		# Verificar se servi�o existe
		$tmpBusca=buscaGrupos("upper(nome)='$tmpNome'", $campo, 'custom', 'id');
		
		# Registro j� existe
		if($tmpBusca && contaConsulta($tmpBusca)>0) {
			# Mensagem de aviso
			$msg="Registro j� existe no banco de dados";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Erro ao incluir registro", $msg, $url, 760);
		}
		else {
			$sql="INSERT INTO $tb[Grupos] VALUES (0, '$matriz[nome]', '$matriz[admin]', '$matriz[incluir]', '$matriz[excluir]',
			'$matriz[visualizar]', '$matriz[alterar]', '$matriz[abrir]', '$matriz[fechar]', '$matriz[comentar]')";
		}
	} #fecha inclusao
	
	elseif($tipo=='alterar') {
		# Verificar se servi�o existe
		$tmpBusca=buscaGrupos($matriz[id], 'id', 'igual', 'id');
				
		# Registro j� existe
		if(!$tmpBusca || contaConsulta($tmpBusca)==0) {
			# Mensagem de aviso
			$msg="Registro n�o encontrado no banco de dados";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Erro ao incluir registro", $msg, $url, 760);
		}
		else {			
			$sql="UPDATE $tb[Grupos] SET 
			admin='$matriz[admin]',
			incluir='$matriz[incluir]',
			visualizar='$matriz[visualizar]',
			alterar='$matriz[alterar]',
			excluir='$matriz[excluir]',
			abrir='$matriz[abrir]',
			fechar='$matriz[fechar]',
			comentar='$matriz[comentar]'
			WHERE id=$matriz[id]";
		}
	}

	elseif($tipo=='excluir') {
		# Verificar se servi�o existe
		$tmpServico=buscaGrupos($matriz[id], 'id', 'igual', 'id');
		
		# Registro j� existe
		if(!$tmpServico || contaConsulta($tmpServico)==0) {
			# Mensagem de aviso
			$msg="Registro n�o existe no banco de dados";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Erro ao alterar registro", $msg, $url, 760);
		}
		else {
			$sql="DELETE FROM $tb[Grupos] WHERE id=$matriz[id]";
			
			# Escluir Usuairos deste Grupo
			dbUsuarioGrupo($matriz, 'excluirgrupo');
		}
	}

	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
	
} # fecha fun��o de grava��o em banco de dados


# Listar grupos
function listarGrupos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite;

	# Cabe�alho		
	# Motrar tabela de busca
	novaTabela("[Listar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 10);
		# Sele��o de registros
		$consulta=buscaGrupos($texto, $campo, 'todos','nome');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# N�o h� registros
			itemTabelaNOURL('N�o h� registros cadastrados', 'left', $corFundo, 10, 'txtaviso');
		}
		else {
		
			# Paginador
			paginador($consulta, contaConsulta($consulta), $limite[lista][grupos], $registro, 'normal', 10, $urlADD);
		
			# Cabe�alho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Nome', 'center', '25%', 'tabfundo0');
				itemLinhaTabela('Admin', 'center', '5%', 'tabfundo0');
				itemLinhaTabela('Incluir', 'center', '5%', 'tabfundo0');
				itemLinhaTabela('Alterar', 'center', '5%', 'tabfundo0');
				itemLinhaTabela('Excluir', 'center', '5%', 'tabfundo0');
				itemLinhaTabela('Ver', 'center', '5%', 'tabfundo0');
				itemLinhaTabela('Abrir', 'center', '5%', 'tabfundo0');
				itemLinhaTabela('Fechar', 'center', '5%', 'tabfundo0');
				itemLinhaTabela('Comentar', 'center', '5%', 'tabfundo0');
				itemLinhaTabela('Op��es', 'center', '40%', 'tabfundo0');
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
			
			$limite=$i+$limite[lista][grupos];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$nome=resultadoSQL($consulta, $i, 'nome');				
				$admin="&nbsp;".resultadoSQL($consulta, $i, 'admin');
				$incluir="&nbsp;".resultadoSQL($consulta, $i, 'incluir');
				$alterar="&nbsp;".resultadoSQL($consulta, $i, 'alterar');
				$excluir="&nbsp;".resultadoSQL($consulta, $i, 'excluir');
				$visualizar="&nbsp;".resultadoSQL($consulta, $i, 'visualizar');
				$abrir="&nbsp;".resultadoSQL($consulta, $i, 'abrir');
				$fechar="&nbsp;".resultadoSQL($consulta, $i, 'fechar');
				$comentar="&nbsp;".resultadoSQL($consulta, $i, 'comentar');
				
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=usuarios&registro=$id>Usu�rios</a>",'usuario');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($nome, 'left', '25%', 'normal');
					itemLinhaTabela($admin, 'center', '5%', 'normal');
					itemLinhaTabela($incluir, 'center', '5%', 'normal');
					itemLinhaTabela($alterar, 'center', '5%', 'normal');
					itemLinhaTabela($excluir, 'center', '5%', 'normal');
					itemLinhaTabela($visualizar, 'center', '5%', 'normal');
					itemLinhaTabela($abrir, 'center', '5%', 'normal');					
					itemLinhaTabela($fechar, 'center', '5%', 'normal');
					itemLinhaTabela($comentar, 'center', '5%', 'normal');
					itemLinhaTabela($opcoes, 'center', '40%', 'normal');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem

	fechaTabela();

} # fecha fun��o de listagem



# Fun��o para procura 
function procurarGrupos($modulo, $sub, $acao, $registro, $matriz)
{
	global $conn, $tb, $corFundo, $corBorda, $html, $limite, $textoProcurar;
	
	# Atribuir valores a vari�vel de busca
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
			<input type=text name=matriz[txtProcurar] size=40 value='$matriz[txtProcurar]'>
			<input type=submit name=matriz[bntProcurar] value=Procurar class=submit>";
			itemLinhaForm($texto, 'left','middle', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
	fechaTabela();
	echo "</form>";


	# Caso bot�o procurar seja pressionado
	if( $matriz[bntProcurar] && $matriz[txtProcurar] ) {
		#buscar registros
		$consulta=buscaGrupos("upper(nome) like '%$matriz[txtProcurar]%'",$campo, 'custom','nome');

		echo "<br>";

		novaTabela("[Listar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 10);
	
		if(!$consulta || contaConsulta($consulta)==0 ) {
			# N�o h� registros
			itemTabelaNOURL('N�o foram encontrados registros cadastrados', 'left', $corFundo, 10, 'txtaviso');
		}
		elseif($consulta && contaConsulta($consulta)>0 && (is_integer($registro) || !$registro)) {	
		
			itemTabelaNOURL('Registros encontrados procurando por ('.$matriz[txtProcurar].'): '.contaConsulta($consulta).' registro(s)', 'left', $corFundo, 10, 'txtaviso');

			# Paginador
			$urlADD="&textoProcurar=".$matriz[txtProcurar];
			paginador($consulta, contaConsulta($consulta), $limite[lista][grupos], $registro, 'normal', 10, $urlADD);


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

			# Cabe�alho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Nome', 'center', '25%', 'tabfundo0');
				itemLinhaTabela('Admin', 'center', '5%', 'tabfundo0');
				itemLinhaTabela('Incluir', 'center', '5%', 'tabfundo0');
				itemLinhaTabela('Alterar', 'center', '5%', 'tabfundo0');
				itemLinhaTabela('Excluir', 'center', '5%', 'tabfundo0');
				itemLinhaTabela('Ver', 'center', '5%', 'tabfundo0');
				itemLinhaTabela('Abrir', 'center', '5%', 'tabfundo0');
				itemLinhaTabela('Fechar', 'center', '5%', 'tabfundo0');
				itemLinhaTabela('Coment', 'center', '5%', 'tabfundo0');
				itemLinhaTabela('Op��es', 'center', '40%', 'tabfundo0');
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
			
			$limite=$i+$limite[lista][grupos];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$nome=resultadoSQL($consulta, $i, 'nome');				
				$admin="&nbsp;".resultadoSQL($consulta, $i, 'admin');
				$incluir="&nbsp;".resultadoSQL($consulta, $i, 'incluir');
				$alterar="&nbsp;".resultadoSQL($consulta, $i, 'alterar');
				$excluir="&nbsp;".resultadoSQL($consulta, $i, 'excluir');
				$visualizar="&nbsp;".resultadoSQL($consulta, $i, 'visualizar');
				$abrir="&nbsp;".resultadoSQL($consulta, $i, 'abrir');
				$fechar="&nbsp;".resultadoSQL($consulta, $i, 'fechar');
				$comentar="&nbsp;".resultadoSQL($consulta, $i, 'comentar');
				
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=usuarios&registro=$id>Usuarios</a>",'usuario');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($nome, 'left', '25%', 'normal');
					itemLinhaTabela($admin, 'center', '5%', 'normal');
					itemLinhaTabela($incluir, 'center', '5%', 'normal');
					itemLinhaTabela($alterar, 'center', '5%', 'normal');
					itemLinhaTabela($excluir, 'center', '5%', 'normal');
					itemLinhaTabela($visualizar, 'center', '5%', 'normal');
					itemLinhaTabela($abrir, 'center', '5%', 'normal');					
					itemLinhaTabela($fechar, 'center', '5%', 'normal');
					itemLinhaTabela($comentar, 'center', '5%', 'normal');
					itemLinhaTabela($opcoes, 'center', '40%', 'normal');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem
	} # fecha bot�o procurar
} #fecha funcao de  procura


# Fun��o para visualizar as informa��es do servidor
function verGrupo($registro)
{
	global $conn, $corFundo, $corBorda, $tb, $html;
	
	# Mostar informa��es sobre Servidor
	$consulta=buscaGrupos($registro, 'id','igual','id');
	
	$idGrupo=resultadoSQL($consulta, 0, 'id');
	$nome=resultadoSQL($consulta, 0, 'nome');
	$admin=resultadoSQL($consulta, 0, 'admin');
	$incluir=resultadoSQL($consulta, 0, 'incluir');
	$alterar=resultadoSQL($consulta, 0, 'alterar');
	$excluir=resultadoSQL($consulta, 0, 'excluir');
	$visualizar=resultadoSQL($consulta, 0, 'visualizar');
	$abrir=resultadoSQL($consulta, 0, 'abrir');
	$fechar=resultadoSQL($consulta, 0, 'fechar');
	$comentar=resultadoSQL($consulta, 0, 'comentar');
	
	#nova tabela para mostrar informa��es
	novaTabela2('Informa��es sobre Grupo', 'center', '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTabela('<b>Nome:</b>', 'right', '30%', 'tabfundo1');
			itemLinhaNOURL($nome, 'left', $corFundo, 0, 'normal');
		fechaLinhaTabela();
		# Vefificar e mostrar permiss�es
		if($admin) {
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('<b>Permiss�o de Administrador:</b>', 'right', '30%', 'tabfundo1');
				itemLinhaNOURL($admin, 'left', $corFundo, 0, 'normal');
			fechaLinhaTabela();
		}
		if($incluir) {
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('<b>Permiss�o de Inclus�o:</b>', 'right', '30%', 'tabfundo1');
				itemLinhaNOURL($incluir, 'left', $corFundo, 0, 'normal');
			fechaLinhaTabela();
		}
		if($excluir) {
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('<b>Permiss�o de Exclus�o:</b>', 'right', '30%', 'tabfundo1');
				itemLinhaNOURL($excluir, 'left', $corFundo, 0, 'normal');
			fechaLinhaTabela();
		}
		if($alterar) {
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('<b>Permiss�o de Altera��o:</b>', 'right', '30%', 'tabfundo1');
				itemLinhaNOURL($alterar, 'left', $corFundo, 0, 'normal');
			fechaLinhaTabela();
		}
		if($visualizar) {
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('<b>Permiss�o de Visualizar Detalhes:</b>', 'right', '30%', 'tabfundo1');
				itemLinhaNOURL($visualizar, 'left', $corFundo, 0, 'normal');
			fechaLinhaTabela();
		}
		if($abrir) {
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('<b>Permiss�o de Abrir Tickets:</b>', 'right', '30%', 'tabfundo1');
				itemLinhaNOURL($abrir, 'left', $corFundo, 0, 'normal');
			fechaLinhaTabela();
		}
		if($fechar) {
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('<b>Permiss�o de Fechar Tickets:</b>', 'right', '30%', 'tabfundo1');
				itemLinhaNOURL($fechar, 'left', $corFundo, 0, 'normal');
			fechaLinhaTabela();
		}
		if($comentar) {
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('<b>Permiss�o de Comentar:</b>', 'right', '30%', 'tabfundo1');
				itemLinhaNOURL($comentar, 'left', $corFundo, 0, 'normal');
			fechaLinhaTabela();
		}
		
		
		# Mostrar lista de usuarios
		$consultaUsuariosGrupos=buscaUsuariosGrupos($registro, 'idGrupo','igual', 'idGrupo');

		if($consultaUsuariosGrupos && contaConsulta($consultaUsuariosGrupos)>0) {
		
			$tmpListaUsuarios="";
			$i=0;
			while($i < contaConsulta($consultaUsuariosGrupos)) {
				# Receber id do usuario
				$idUsuario=resultadoSQL($consultaUsuariosGrupos, $i, 'idUsuario');
				
				# buscar usuario
				$consultaUsuario=buscaUsuarios($idUsuario, 'id','igual','id');
				$login=resultadoSQL($consultaUsuario, 0, 'login');
				
				$tmpListaUsuarios.=$login;
				
				# Incrementar contador
				$i++;

				if($i < contaConsulta($consultaUsuariosGrupos)) $tmpListaUsuarios.=", ";
				
			}
			# mostrar lista de usuarios
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('<b>Usu�rios do Grupo:</b>', 'right', '30%', 'tabfundo1');
				itemLinhaNOURL($tmpListaUsuarios, 'left', $corFundo, 0, 'normal');
			fechaLinhaTabela();
		}
		
	fechaTabela();	
	# fim da tabela

	//htmlFechaColuna();
	//fechaLinhaTabela();
	# fecha linha
	
	echo "<br>";
	
} #fecha visualizacao




# Fun��o para montar campo de formulario
function formListaGrupos($idGrupo)
{
	global $conn, $tb;
	
	$consulta=buscaGrupos($idGrupo, $campo, 'todos', 'id');
	
	$item="<select name=matriz[grupo]>\n";
	
	# Listargem
	$i=0;
	while($i < contaConsulta($consulta)) {
		# Valores dos campos
		$nome=resultadoSQL($consulta, $i, 'nome');
		$id=resultadoSQL($consulta, $i, 'id');
		
		# Verificar se deve selecionar o usuario na lista
		if($idGrupo==$id) $opcSelect="selected";
		else $opcSelect="";

		# Mostrar servi�o		
		$item.= "<option value=$id $opcSelect>$nome\n";

		#Incrementar contador
		$i++;
	}
	
	$item.="</select>";
	return($item);
	
} #fecha funcao de montagem de campo de form


# Fun��o para checagem de grupo
function checaGrupo($idGrupo) {
	
	$consulta=buscaGrupos($idGrupo, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)==1) {
		$retorno=resultadoSQL($consulta, 0, 'nome');
	}
	else 	$retorno=$consulta;
	
	return($retorno);
}

?>
