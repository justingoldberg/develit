<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 09/01/2004
# Ultima alteração: 11/01/2005
#    Alteração No.: 002
#
# Função:
#    Painel - Funções para cadastro


# função de busca 
function buscaProgramas($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[Programas] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[Programas] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[Programas] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[Programas] WHERE $texto ORDER BY $ordem";
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
function buscaIDPrograma($texto, $campo, $tipo, $retorno) {

	$consulta=buscaProgramas($texto, $campo, $tipo, '');
	
	if($consulta && contaConsulta($consulta)>0) {
		# retornar
		$retorno=resultadoSQL($consulta, 0, $retorno);
	}
	
	return($retorno);

}


# Função para listagem 
function listarProgramas($modulo, $sub, $acao, $registro, $matriz)
{

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite;

	# Seleção de registros
	$consulta=buscaMaquinas($registro, 'id', 'igual', 'id');
	
	echo "<br>";
	
	if(!$consulta || contaConsulta($consulta)==0) {
		# Servidor não encontrado
		itemTabelaNOURL('Máquina não encontrada!', 'left', $corFundo, 3, 'txtaviso');
	}
	else {
		# Mostrar Informações sobre Servidor
		verMaquina($modulo, $sub, $acao, $registro, $matriz);
		echo "<br>";
		
		$consulta=buscaProgramas($registro, 'idMaquina','igual','idMaquina');
		
		# Cabeçalho		
		# Motrar tabela de busca
		novaTabela("[Programas Instalados]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
		$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar&registro=$registro>Adicionar</a>",'incluir');
		itemTabelaNOURL($opcoes, 'right', $corFundo, 3, 'tabfundo1');
		
		
		# Caso não hajam servicos para o servidor
		if(!$consulta || contaConsulta($consulta)==0) {
			# Não há registros
			itemTabelaNOURL('Não há programas instalados nesta  máquina', 'left', $corFundo, 3, 'txtaviso');
		}
		else {

			# Cabeçalho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Nome', 'center', '50%', 'tabfundo0');
				itemLinhaTabela('Versão', 'center', '20%', 'tabfundo0');
				itemLinhaTabela('Opções', 'center', '30%', 'tabfundo0');
			fechaLinhaTabela();

			for($i=0;$i<contaConsulta($consulta);$i++) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$idMaquina=resultadoSQL($consulta, $i, 'idMaquina');
				$nome=resultadoSQL($consulta, $i, 'nome');
				$versao=resultadoSQL($consulta, $i, 'versao');
				$data=resultadoSQL($consulta, $i, 'data');
				$idUsuario=resultadoSQL($consulta, $i, 'idUsuario');
				$comentarios=resultadoSQL($consulta, $i, 'comentarios');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=programas&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=programas&acao=excluir&registro=$id>Excluir</a>",'excluir');
				
				# Caso encontre comentários, mostrar observação
				if(strlen(trim($comentarios))>0) $opcOBS="<img src=".$html[imagem][comentar]." align=right>";
				else $opcOBS='';
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela("$opcOBS $nome", 'left', '50%', 'normal10');
					itemLinhaTabela($versao, 'left', '20%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '30%', 'normal10');
				fechaLinhaTabela();
				
			} #fecha laco de montagem de tabela
			
			fechaTabela();
		} #fecha servicos encontrados
	} #fecha listagem

	
}#fecha função de listagem



# Funcao para cadastro de servicos
function adicionarProgramas($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin;
	
	echo "<br>";
	
	# Form de inclusao
	if(!$matriz[bntAdicionar] || !$matriz[nome] || !$matriz[versao]) {

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
			novaTabela2("[Adicionar Programa]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
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
						echo "<b>Nome: </b><br>
						<span class=normal10>Nome do programa</span>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[nome] size=60>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Versão: </b><br>
						<span class=normal10>Versão do programa</span>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[versao] size=30>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Comentários: </b><br>
						<span class=normal10>Observações sobre o programa</span>";
					htmlFechaColuna();
					$texto="<textarea name=matriz[comentarios] rows=6 cols=60></textarea>";
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
		} # fecha servidor informado para cadastro
	}
	elseif($matriz[bntAdicionar]) {
		# Cadastrar em banco de dados
		$grava=dbPrograma($matriz, 'incluir');
			
		# Verificar inclusão de registro
		if($grava) {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Registro Gravado com Sucesso!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$registro";
			aviso("Aviso", $msg, $url, 400);
			
			listarProgramas($modulo, $sub, $acao, $matriz[maquina], '');
		}
	}

} # fecha funcao de inclusao de servicos


# Funcao para exclusão de servicos
function excluirProgramas($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	$consulta=buscaProgramas($registro, 'id', 'igual', 'id');
	
	echo "<br>";

	if($consulta && contaConsulta($consulta)>0) {
		
		$idMaquina=resultadoSQL($consulta, 0, 'idMaquina');
		
		# Form de exclusão
		if(!$matriz[bntExcluir]) {
	
			# Seleção de registros
			$consulta=buscaProgramas($idMaquina, 'idMaquina', 'igual', 'data ASC');
			
			if(!$consulta || contaConsulta($consulta)==0) {
				# Servidor não encontrado
				itemTabelaNOURL('Programa não encontrado!', 'left', $corFundo, 3, 'txtaviso');
			}
			else {
				# Visualizar Maquina
				verMaquina($modulo, $sub, $acao, $idMaquina, '');
				echo "<br>";
		
				$id=resultadoSQL($consulta, 0, 'id');
				$idMaquina=resultadoSQL($consulta, 0, 'idMaquina');
				$nome=resultadoSQL($consulta, 0, 'nome');
				$versao=resultadoSQL($consulta, 0, 'versao');
				$data=resultadoSQL($consulta, 0, 'data');
				$idUsuario=resultadoSQL($consulta, 0, 'idUsuario');
				$comentarios=resultadoSQL($consulta, 0, 'comentarios');
		
				# Motrar tabela de busca
				novaTabela2("[Excluir Programa]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
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
							echo "<b>Nome: </b>";
						htmlFechaColuna();
						itemLinhaForm($nome, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b>Versão: </b>";
						htmlFechaColuna();
						itemLinhaForm($versao, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b>Data de Modificação: </b>";
						htmlFechaColuna();
						itemLinhaForm(converteData($data,'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b>Usuário: </b>";
						htmlFechaColuna();
						itemLinhaForm(buscaLoginUsuario($idUsuario,'id','igual','id'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					if(strlen(trim($comentarios))>0) {
						novaLinhaTabela($corFundo, '100%');
							htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
								echo "<b>Comentários: </b>";
							htmlFechaColuna();
							itemLinhaForm(nl2br($comentarios), 'left', 'top', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
					}
					novaLinhaTabela($corFundo, '100%');
						$texto="<input type=submit name=matriz[bntExcluir] value=Excluir class=submit>";
						itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
					fechaLinhaTabela();
				fechaTabela();
			} # fecha servidor informado para cadastro
		} #fecha form
		elseif($matriz[bntExcluir]) {
			# Cadastrar em banco de dados
			$matriz[id]=$registro;
			$grava=dbPrograma($matriz, 'excluir');
				
			# Verificar inclusão de registro
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Excluído com Sucesso!";
				$url="?modulo=$modulo&sub=programas$sub&acao=listar&registro=$matriz[idMaquina]";
				avisoNOURL("Aviso", $msg, 400);
			}
			
			listarProgramas($modulo, $sub, 'listar', $matriz[idMaquina], '');
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




# Funcao para exclusão de servicos
function alterarProgramas($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	$consulta=buscaProgramas($registro, 'id', 'igual', 'id');
	
	echo "<br>";
	
	if($consulta && contaConsulta($consulta)>0) {
		
		$idMaquina=resultadoSQL($consulta, 0, 'idMaquina');
		
		# Form de exclusão
		if(!$matriz[bntAlterar] || !$matriz[nome] || !$matriz[versao]) {
	
			# Seleção de registros
			$consulta=buscaProgramas($registro, 'id', 'igual', 'data ASC');
			
			if(!$consulta || contaConsulta($consulta)==0) {
				# Servidor não encontrado
				itemTabelaNOURL('Programa não encontrado!', 'left', $corFundo, 3, 'txtaviso');
			}
			else {
				# Visualizar Maquina
				verMaquina($modulo, $sub, $acao, $idMaquina, '');
				echo "<br>";
		
				$id=resultadoSQL($consulta, 0, 'id');
				$idMaquina=resultadoSQL($consulta, 0, 'idMaquina');
				$data=resultadoSQL($consulta, 0, 'data');
				$idUsuario=resultadoSQL($consulta, 0, 'idUsuario');
				
				if(!$matriz[bntAlterar]) {
					$matriz[nome]=resultadoSQL($consulta, 0, 'nome');
					$matriz[versao]=resultadoSQL($consulta, 0, 'versao');
					$matriz[comentarios]=resultadoSQL($consulta, 0, 'comentarios');
				}
		
				# Motrar tabela de busca
				novaTabela2("[Alterar Programa]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
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
							echo "<b>Nome: </b>";
						htmlFechaColuna();
						$texto="<input type=text name=matriz[nome] value='$matriz[nome]' size=60>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b>Versão: </b>";
						htmlFechaColuna();
						$texto="<input type=text name=matriz[versao] value='$matriz[versao]' size=30>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b>Data de Modificação: </b>";
						htmlFechaColuna();
						itemLinhaForm(converteData($data,'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b>Usuário: </b>";
						htmlFechaColuna();
						itemLinhaForm(buscaLoginUsuario($idUsuario,'id','igual','id'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b>Comentários: </b>";
						htmlFechaColuna();
						$texto="<textarea name=matriz[comentarios] cols=60 rows=6>$matriz[comentarios]</textarea>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						$texto="<input type=submit name=matriz[bntAlterar] value=Alterar class=submit>";
						itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
					fechaLinhaTabela();
				fechaTabela();
			} # fecha servidor informado para cadastro
		} #fecha form
		elseif($matriz[bntAlterar]) {
			# Cadastrar em banco de dados
			$matriz[id]=$registro;
			$grava=dbPrograma($matriz, 'alterar');
			
			# Verificar inclusão de registro
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Alterado com Sucesso!";
				$url="?modulo=$modulo&sub=programas&acao=listar&registro=$matriz[idMaquina]";
				avisoNOURL("Aviso", $msg, 400);
			}
			
			listarProgramas($modulo, $sub, 'listar', $matriz[idMaquina], '');
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
function dbPrograma($matriz, $tipo)
{
	global $conn, $tb, $modulo, $sub, $acao, $sessLogin;
	
	$data=dataSistema();
	$idUsuario=buscaIDUsuario($sessLogin[login],'login','igual','id');
	
	# Sql de inclusão
	if($tipo=='incluir') {
		$sql="
			INSERT INTO 
				$tb[Programas] 
			VALUES (
				'0',
				'$matriz[maquina]',
				'$matriz[nome]', 
				'$matriz[versao]',
				'$data[dataBanco]',
				'$idUsuario',
				'$matriz[comentarios]'
			)";
	} #fecha inclusao
	
	elseif($tipo=='excluir') {
		$sql="
			DELETE FROM 
				$tb[Programas] 
			WHERE 
				id=$matriz[id]";
	}
	
	elseif($tipo=='excluirmaquina') {
		$sql="
			DELETE FROM 
				$tb[Programas] 
			WHERE 
				idMaquina=$matriz[id]";
	}

	elseif($tipo=='alterar') {
		$sql="
			UPDATE 
				$tb[Programas] 
			SET
				data='$data[dataBanco]',
				nome='$matriz[nome]',
				versao='$matriz[versao]',
				comentarios='$matriz[comentarios]'
			WHERE 
				id=$matriz[id]";
	}
	
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
	
} # fecha função de gravação em banco de dados

?>
