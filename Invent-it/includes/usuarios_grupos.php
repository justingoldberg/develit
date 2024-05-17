<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 24/01/2003
# Ultima alteração: 04/09/2003
#    Alteração No.: 005
#
# Função:
#    Funções de usuarios de grupos

# Função para listagem de serviços de Servidores
function listarUsuariosGrupo($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite;

	# Seleção de registros
	$consulta=buscaGrupos($registro, 'id', 'igual', 'id');
	
	if(!$consulta || contaConsulta($consulta)==0) {
		# Servidor não encontrado
		itemTabelaNOURL('Grupo não encontrado!', 'left', $corFundo, 3, 'txtaviso');
	}
	else {
		# Mostrar Informações sobre Servidor
		verGrupo($registro);
		
		$consulta=buscaUsuariosGrupos($registro, 'idGrupo','igual','idGrupo');
		
		# Cabeçalho		
		# Motrar tabela de busca
		novaTabela("[Usuários do Grupo]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
		$tmpOpcao=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=".$acao."adicionar&registro=$registro>Adicionar</a>",'incluir');
		itemTabelaNOURL($tmpOpcao, 'right', $corFundo, 3, 'tabfundo1');

		# Caso não hajam servicos para o servidor
		if(!$consulta || contaConsulta($consulta)==0) {
			# Não há registros
			itemTabelaNOURL('Não há usuarios cadastrados para este grupo', 'left', $corFundo, 3, 'txtaviso');
		}
		else {
		
			# Cabeçalho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Login', 'center', '40%', 'tabfundo0');
				itemLinhaTabela('Status', 'center', '20%', 'tabfundo0');
				itemLinhaTabela('Opções', 'center', '40%', 'tabfundo0');
			fechaLinhaTabela();

			$i=0;
			
			while($i < contaConsulta($consulta)) {
				# Mostrar registro
				$idUsuario=resultadoSQL($consulta, $i, 'idUsuario');
				
				# Buscar informações sobre o serviço
				$consultaUsuario=buscaUsuarios($idUsuario, 'id','igual','id');
				
				$login=resultadoSQL($consultaUsuario, 0, 'login');
				$status=resultadoSQL($consultaUsuario, 0, 'status');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=$acao".excluir."&registro=$registro:$idUsuario>Excluir</a>",'excluir');
		
				/*
				# Verificar status para montar opção
				if($status=='A') {
					# Opção desativar
					$opcoes.="&nbsp;";
					$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=$acao".desativar."&registro=$registro:$idUsuario>Desativar</a>",'desativar');
				}
				elseif($status=='D') {
					# Opção ativar
					$opcoes.="&nbsp;";
					$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=$acao".ativar."&registro=$registro:$idUsuario>Ativar</a>",'ativar');
				} */
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($login, 'left', '40%', 'normal');
					itemLinhaTabela(checaStatus($status), 'center', '20%', 'normal');
					itemLinhaTabela($opcoes, 'left', '40%', 'normal');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
			
			fechaTabela();
		} #fecha servicos encontrados
	} #fecha listagem
}#fecha função de listagem


# Funcao para cadastro de usuarios
function adicionarUsuariosGrupo($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# Form de inclusao
	if(!$matriz[bntAdicionar]) {

		# Seleção de registros
		$consulta=buscaGrupos($registro, 'id', 'igual', 'id');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Servidor não encontrado
			itemTabelaNOURL('Grupo não encontrado!', 'left', $corFundo, 3, 'txtaviso');
		}
		else {
			# Mostrar Informações sobre Servidor
			verGrupo($registro);
	
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
					<input type=hidden name=registro value=$registro>
					<input type=hidden name=matriz[grupo] value=$registro>
					<input type=hidden name=acao value=$acao>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Usuário: </b>";
					htmlFechaColuna();
					$item=formListaUsuariosGrupo($registro);
					itemLinhaForm($item, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();
					$texto="<input type=submit name=matriz[bntAdicionar] value=Adicionar>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		} # fecha servidor informado para cadastro
	} #fecha form
	elseif($matriz[bntAdicionar]) {
		# Conferir campos
		if($matriz[grupo] && $matriz[usuario]) {
			# Cadastrar em banco de dados
			$grava=dbUsuarioGrupo($matriz, 'incluir');
				
			# Verificar inclusão de registro
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				$url="?modulo=$modulo&sub=$sub&acao=usuarios&registro=$registro";
				aviso("Aviso", $msg, $url, 400);
				
				echo "<br>";
				listarUsuariosGrupo($modulo, $sub, 'usuarios', $matriz[grupo], $matriz);
			}
		}
		
		# falta de parametros
		else {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Falta de parâmetros necessários. Informe os campos obrigatórios e tente novamente";
			$url="?modulo=$modulo&sub=$sub&acao=usuarios&registro=$registro";
			aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
		}
	}
} # fecha funcao de inclusao de usuarios



# Função para montar campo de formulario
function formListaUsuariosGrupo($grupo)
{
	global $conn, $tb;
	
	# Buscar Serviços de servidor (ja cadastrados)
	$consultaGrupo=buscaUsuariosGrupos($grupo, 'idGrupo','igual','idGrupo');
	
	$consulta=buscaUsuarios($texto, $campo, 'todos', 'login');
	
	$item="<select name=matriz[usuario]>\n";
	
	# Listargem
	$i=0;
	while($i < contaConsulta($consulta)) {
		# Zerar flag de registro já cadastrado
		$flag=0;
		
		# Valores dos campos
		$login=resultadoSQL($consulta, $i, 'login');
		$id=resultadoSQL($consulta, $i, 'id');

		# Verificar se serviço já está cadastrado
		$iGrupo=0;
		while($iGrupo < contaConsulta($consultaGrupo) ) {
		
			# Verificar
			$idUsuario=resultadoSQL($consultaGrupo, $iGrupo, 'idUsuario');
			
			if($idUsuario == $id) {
				# Setar Flag de registro já cadastrado
				$flag=1;
				break;
			}

			# Incrementar contador
			$iGrupo++;
		}

		if(!$flag) {
			# Mostrar serviço		
			$item.= "<option value=$id>$login\n";
		}

		#Incrementar contador
		$i++;
	}
	
	$item.="</select>";
	
	return($item);
	
} #fecha funcao de montagem de campo de form



# Função para gravação em banco de dados
function dbUsuarioGrupo($matriz, $tipo)
{
	global $conn, $tb, $modulo, $sub, $acao;
	
	# Sql de inclusão
	if($tipo=='incluir') {
		# Verificar se serviço existe
		$tmpBusca=buscaUsuariosGrupos("idUsuario=$matriz[usuario] AND idGrupo=$matriz[grupo]", $campo, 'custom', 'idGrupo');
		
		# Registro já existe
		if($tmpBusca && contaConsulta($tmpBusca)>0) {
			# Mensagem de aviso
			$msg="Registro já existe no banco de dados";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Erro ao incluir registro", $msg, $url, 760);
		}
		else {
			$sql="INSERT INTO $tb[UsuariosGrupos] VALUES ($matriz[usuario], $matriz[grupo])";
		}
	} #fecha inclusao
	
	elseif($tipo=='excluir') {
		# Verificar se serviço existe
		$tmpGrupo=buscaUsuariosGrupos("idGrupo=$matriz[grupo] AND idUsuario=$matriz[usuario]", 'id', 'custom', 'idUsuario');
		
		# Registro já existe
		if(!$tmpGrupo || contaConsulta($tmpGrupo)==0) {
			# Mensagem de aviso
			$msg="Registro não existe no banco de dados";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Erro ao alterar registro", $msg, $url, 760);
		}
		else {
			$sql="DELETE FROM $tb[UsuariosGrupos] WHERE idUsuario=$matriz[usuario] AND idGrupo=$matriz[grupo]";
		}
	}

	# Excluir todos os usuarios do grupo
	elseif($tipo=='excluirtodos') {
		# Verificar se serviço existe
		$sql="DELETE FROM $tb[UsuariosGrupos] WHERE idUsuario=$matriz[id]";
	}
	# Excluir todos os usuarios do grupo
	elseif($tipo=='excluirgrupo') {
		# Verificar se serviço existe
		$sql="DELETE FROM $tb[UsuariosGrupos] WHERE idGrupo=$matriz[id]";
	}

	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
	
} # fecha função de gravação em banco de dados


# Funcao para exclusão de usuarios
function excluirUsuariosGrupo($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;

	$matTMP=explode(":",$registro);
	$matriz[grupo]=$matTMP[0];
	$matriz[usuario]=$matTMP[1];
	
	$consultaUsuario=buscaUsuarios($matriz[usuario], 'id', 'igual', 'id');
	$login=resultadoSQL($consultaUsuario, 0, 'login');
	$status=resultadoSQL($consultaUsuario, 0, 'status');
	
	$consultaUsuariosGrupos=buscaUsuariosGrupos('idGrupo='.$matriz[grupo].' AND idUsuario='.$matriz[usuario], $campo, 'custom', 'idGrupo');
	
	
	# Form de exclusão
	if(!$matriz[bntRemover]) {

		# Seleção de registros
		$consulta=buscaGrupos($matriz[grupo], 'id', 'igual', 'id');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Servidor não encontrado
			itemTabelaNOURL('Grupo não encontrado!', 'left', $corFundo, 3, 'txtaviso');
		}
		else {
			# Mostrar Informações sobre Servidor
			verGrupo($matriz[grupo]);
	
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
					<input type=hidden name=registro value=$registro>
					<input type=hidden name=acao value=$acao>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Usuario: </b>";
					htmlFechaColuna();
					itemLinhaForm($login, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Status: </b>";
					htmlFechaColuna();					
					itemLinhaForm(checaStatus($status), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();
					$texto="<input type=submit name=matriz[bntRemover] value=Remover>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		} # fecha  formulario
	} #fecha form
	elseif($matriz[bntRemover]) {
		# Conferir campos
		if($matriz[grupo] && $matriz[usuario]) {
		
			# Cadastrar em banco de dados
			$grava=dbUsuarioGrupo($matriz, 'excluir');
				
			# Verificar inclusão de registro
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Excluído com Sucesso!";
				$url="?modulo=$modulo&sub=$sub&acao=usuarios&registro=$matriz[grupo]";
				aviso("Aviso", $msg, $url, 400);
				
				echo "<br>";
				listarUsuariosGrupo($modulo, $sub, 'usuarios', $matriz[grupo], $matriz);
			}
		}
		
		# falta de parametros
		else {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Falta de parâmetros necessários. Informe os campos obrigatórios e tente novamente";
			$url="?modulo=$modulo&sub=$sub&acao=usuarios&registro=$registro";
			aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
		}
	}
} # fecha funcao de exclusão



# função de busca usuarios do grupo
function buscaUsuariosGrupos($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[UsuariosGrupos] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[UsuariosGrupos] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[UsuariosGrupos] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[UsuariosGrupos] WHERE $texto ORDER BY $ordem";
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
	
} # fecha função de busca usuarios do grupo



?>
