<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 15/10/2003
# Ultima altera��o: 20/10/2003
#    Altera��o No.: 002
#
# Fun��o:
#    Painel - Fun��es para controle de servi�o de radius (grupos)


# fun��o de busca de grupos
function radiusBuscaGrupos($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[RadiusGrupos] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[RadiusGrupos] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[RadiusGrupos] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[RadiusGrupos] WHERE $texto ORDER BY $ordem";
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
function radiusBuscaIDGrupo($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[RadiusGrupos] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[RadiusGrupos] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[RadiusGrupos] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[RadiusGrupos] WHERE $texto ORDER BY $ordem";
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
function radiusAdicionarGrupos($modulo, $sub, $acao, $registro, $matriz)
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
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro>
				&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Nome: </b><br>
					<span class=normal10>Nome do grupo</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[nome] size=20>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Descri��o: </b><br>
					<span class=normal10>Identifica��o detalhada do grupo</span>";
				htmlFechaColuna();
				$texto="<textarea name=matriz[descricao] rows=3 cols=60></textarea>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Acesso Ilimitado: </b><br>
					<span class=normal10>Seleciona esta op��o caso grupo n�o tenha limite de horas de acesso</span>";
				htmlFechaColuna();
				itemLinhaForm(formSelectSimNao('N','ilimitado','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();			
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Horas de Acesso: </b><br>
					<span class=normal10>Nome do grupo</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[horas] size=4>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Status: </b><br>
					<span class=normal10>Status do grupo</span>";
				htmlFechaColuna();
				itemLinhaForm(formSelectStatus('A','status','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Comando: </b><br>
					<span class=normal10>Comando p�s-autentica��o</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[comando] size=60>";
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
			$grava=radiusDBGrupo($matriz, 'incluir');
			
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
function radiusAlterarGrupos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# Form de inclusao
	if(!$matriz[bntAlterar]) {
	
		# Buscar informa��es sobre registro
		$consulta=radiusBuscaGrupos($registro, 'id','igual','id');
		if($consulta && contaConsulta($consulta)>0) {
		
			# receber valores
			$id=resultadoSQL($consulta, 0, 'id');
			$nome=resultadoSQL($consulta, 0, 'nome');
			$descricao=resultadoSQL($consulta, 0, 'descricao');
			$horas=resultadoSQL($consulta, 0, 'horas');
			$ilimitado=resultadoSQL($consulta, 0, 'ilimitado');
			$status=resultadoSQL($consulta, 0, 'status');
			$comando=resultadoSQL($consulta, 0, 'comando');
		
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
						echo "<b class=bold10>Nome: </b><br>
						<span class=normal10>Nome do grupo</span>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[nome] size=20 value='$nome'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Descri��o: </b><br>
						<span class=normal10>Identifica��o detalhada do grupo</span>";
					htmlFechaColuna();
					$texto="<textarea name=matriz[descricao] rows=3 cols=60>$descricao</textarea>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Acesso Ilimitado: </b><br>
						<span class=normal10>Seleciona esta op��o caso grupo n�o tenha limite de horas de acesso</span>";
					htmlFechaColuna();
					itemLinhaForm(formSelectSimNao($ilimitado,'ilimitado','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();			
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Horas de Acesso: </b><br>
						<span class=normal10>Nome do grupo</span>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[horas] size=4 value='$horas'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Status: </b><br>
						<span class=normal10>Status do grupo</span>";
					htmlFechaColuna();
					itemLinhaForm(formSelectStatus($status,'status','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Comando: </b><br>
						<span class=normal10>Comando p�s-autentica��o</span>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[comando] size=60 value='$comando'>";
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
		$grava=radiusDBGrupo($matriz, 'alterar');
		
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
function radiusExcluirGrupos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# Form de inclusao
	if(!$matriz[bntExcluir]) {
	
		# Buscar informa��es sobre registro
		$consulta=radiusBuscaGrupos($registro, 'id','igual','id');
		
		if($consulta && contaConsulta($consulta)>0) {
		
			# receber valores
			$id=resultadoSQL($consulta, 0, 'id');
			$nome=resultadoSQL($consulta, 0, 'nome');
			$descricao=resultadoSQL($consulta, 0, 'descricao');
			$horas=resultadoSQL($consulta, 0, 'horas');
			$ilimitado=resultadoSQL($consulta, 0, 'ilimitado');
			$status=resultadoSQL($consulta, 0, 'status');
			$comando=resultadoSQL($consulta, 0, 'comando');
			
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
						echo "<b class=bold10>Nome: </b>";
					htmlFechaColuna();
					itemLinhaForm($nome, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Descri��o: </b>";
					htmlFechaColuna();
					itemLinhaForm($descricao, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Acesso Ilimitado: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectSimNao($ilimitado,'ilimitado','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();			
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Horas de Acesso: </b>";
					htmlFechaColuna();
					itemLinhaForm($horas, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Status: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectStatus($status,'status','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Comando: </b>";
					htmlFechaColuna();
					itemLinhaForm($comando, 'left', 'top', $corFundo, 0, 'tabfundo1');
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
		$grava=radiusDBGrupo($matriz, 'excluir');
		
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
function radiusDBGrupo($matriz, $tipo)
{
	global $conn, $tb, $modulo, $sub, $acao;
	
	# Sql de inclus�o
	if($tipo=='incluir') {
		$tmpNome=strtoupper($matriz[nome]);
		# Verificar se servi�o existe
		$tmpBusca=radiusBuscaGrupos("upper(nome)='$tmpNome'", $campo, 'custom', 'id');
		
		# Registro j� existe
		if($tmpBusca && contaConsulta($tmpBusca)>0) {
			# Mensagem de aviso
			$msg="Registro j� existe no banco de dados";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Erro ao incluir registro", $msg, $url, 760);
		}
		else {
			$sql="INSERT INTO $tb[RadiusGrupos] VALUES (
				0, 
				'$matriz[nome]', 
				'$matriz[descricao]', 
				'$matriz[horas]', 
				'$matriz[ilimitado]',
				'$matriz[status]', 
				'$matriz[comando]', 
				'$matriz[dtCadastro]', 
				'$matriz[dtAtivacao]', 
				'$matriz[dtInativacao]',
				'$matriz[dtCancelamento]'
			)";
		}
	} #fecha inclusao
	
	elseif($tipo=='alterar') {
		# Verificar se servi�o existe
		$tmpBusca=radiusBuscaGrupos($matriz[id], 'id', 'igual', 'id');
				
		# Registro j� existe
		if(!$tmpBusca || contaConsulta($tmpBusca)==0) {
			# Mensagem de aviso
			$msg="Registro n�o encontrado no banco de dados";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Erro ao incluir registro", $msg, $url, 760);
		}
		else {			
			$sql="
				UPDATE $tb[RadiusGrupos] SET 
					nome='$matriz[nome]',
					descricao='$matriz[descricao]',
					horas='$matriz[horas]',
					ilimitado='$matriz[ilimitado]',
					status='$matriz[status]',
					comando='$matriz[comando]'
				WHERE id=$matriz[id]";
		}
	}

	elseif($tipo=='excluir') {
		# Verificar se servi�o existe
		$tmpServico=radiusBuscaGrupos($matriz[id], 'id', 'igual', 'id');
		
		# Registro j� existe
		if(!$tmpServico || contaConsulta($tmpServico)==0) {
			# Mensagem de aviso
			$msg="Registro n�o existe no banco de dados";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Erro ao alterar registro", $msg, $url, 760);
		}
		else {
			$sql="DELETE FROM $tb[RadiusGrupos] WHERE id=$matriz[id]";
			
			# Excluir usu�rios do grupo
			//dbUsuarioGrupo($matriz, 'excluirgrupo');
		}
	}

	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
	
} # fecha fun��o de grava��o em banco de dados


# Listar grupos
function radiusListarGrupos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite;



	# Cabe�alho
	# Motrar tabela de busca
	novaTabela("[Listar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 6);
	
		$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
		$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
		itemTabelaNOURL($opcoes, 'right', $corFundo, 6, 'tabfundo1');
	
		# Sele��o de registros
		$consulta=radiusBuscaGrupos($texto, $campo, 'todos','nome');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# N�o h� registros
			itemTabelaNOURL('N�o h� registros cadastrados', 'left', $corFundo, 6, 'txtaviso');
		}
		else {
		
			# Paginador
			paginador($consulta, contaConsulta($consulta), $limite[lista][radius_grupos], $registro, 'normal', 6, $urlADD);
		
			# Cabe�alho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Nome', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Descri��o', 'center', '35%', 'tabfundo0');
				itemLinhaTabela('Ilimitado', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Horas', 'center', '5%', 'tabfundo0');
				itemLinhaTabela('Status', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Op��es', 'center', '35%', 'tabfundo0');
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
			
			$limite=$i+$limite[lista][radius_grupos];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$nome=resultadoSQL($consulta, $i, 'nome');				
				$descricao=resultadoSQL($consulta, $i, 'descricao');
				$horas=resultadoSQL($consulta, $i, 'horas');
				$ilimitado=resultadoSQL($consulta, $i, 'ilimitado');
				$status=resultadoSQL($consulta, $i, 'status');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=ver&registro=$id>Ver</a>",'ver');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=servicos&registro=$id>Servi�os</a>",'servicos');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($nome, 'center', '10%', 'normal10');
					itemLinhaTabela($descricao, 'left', '35%', 'normal10');
					itemLinhaTabela(formSelectSimNao($ilimitado,'','check'), 'center', '5%', 'normal10');
					itemLinhaTabela($horas, 'center', '5%', 'normal10');
					itemLinhaTabela(formSelectStatus($status,'','check'), 'center', '10%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '35%', 'normal8');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem

	fechaTabela();

} # fecha fun��o de listagem



# Fun��o para procura 
function radiusProcurarGrupos($modulo, $sub, $acao, $registro, $matriz)
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
			<input type=text name=matriz[txtProcurar] size=30 value='$matriz[txtProcurar]'>
			<input type=submit name=matriz[bntProcurar] value=Procurar class=submit>";
			itemLinhaForm($texto, 'left','middle', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
	fechaTabela();

	# Caso bot�o procurar seja pressionado
	if( $matriz[bntProcurar] && $matriz[txtProcurar] ) {
		#buscar registros
		$consulta=RadiusBuscaGrupos("upper(nome) like '%$matriz[txtProcurar]%' OR upper(descricao) like '%$matriz[txtProcurar]%'",$campo, 'custom','nome');

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
			paginador($consulta, contaConsulta($consulta), $limite[lista][radius_grupos], $registro, 'normal', 10, $urlADD);


			# Cabe�alho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Nome', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Descri��o', 'center', '35%', 'tabfundo0');
				itemLinhaTabela('Ilimitado', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Horas', 'center', '5%', 'tabfundo0');
				itemLinhaTabela('Status', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Op��es', 'center', '35%', 'tabfundo0');
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
			
			$limite=$i+$limite[lista][radius_grupos];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$nome=resultadoSQL($consulta, $i, 'nome');				
				$descricao=resultadoSQL($consulta, $i, 'descricao');
				$horas=resultadoSQL($consulta, $i, 'horas');
				$ilimitado=resultadoSQL($consulta, $i, 'ilimitado');
				$status=resultadoSQL($consulta, $i, 'status');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=ver&registro=$id>Ver</a>",'ver');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($nome, 'center', '10%', 'normal10');
					itemLinhaTabela($descricao, 'left', '35%', 'normal10');
					itemLinhaTabela(formSelectSimNao($ilimitado,'','check'), 'center', '5%', 'normal10');
					itemLinhaTabela($horas, 'center', '5%', 'normal10');
					itemLinhaTabela(formSelectStatus($status,'','check'), 'center', '10%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '35%', 'normal10');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem
	} # fecha bot�o procurar
} #fecha funcao de  procura


# Fun��o para visualizar as informa��es do servidor
function radiusVerGrupo($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $corFundo, $corBorda, $tb, $html;
	
	# Mostar informa��es sobre Servidor
	$consulta=radiusBuscaGrupos($registro, 'id','igual','id');
	
	#nova tabela para mostrar informa��es
	novaTabela2('Informa��es sobre Grupo', 'center', '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	
	if($consulta && contaConsulta($consulta)>0) {
		$id=resultadoSQL($consulta, 0, 'id');
		$nome=resultadoSQL($consulta, 0, 'nome');
		$descricao=resultadoSQL($consulta, 0, 'descricao');
		$horas=resultadoSQL($consulta, 0, 'horas');
		$ilimitado=resultadoSQL($consulta, 0, 'ilimitado');
		$status=resultadoSQL($consulta, 0, 'status');
		$comando=resultadoSQL($consulta, 0, 'comando');
		$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
		$dtAtivacao=resultadoSQL($consulta, 0, 'dtAtivacao');
		$dtInativacao=resultadoSQL($consulta, 0, 'dtInativacao');
		$dtCancelamento=resultadoSQL($consulta, 0, 'dtCancelamento');
		
		# Opcoes Adicionais
		menuOpcAdicional($modulo, $sub, $acao, $registro);				
		
		# Vefificar e mostrar permiss�es
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b class=bold10>Nome: </b>";
			htmlFechaColuna();
			itemLinhaForm($nome, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b class=bold10>Descri��o: </b>";
			htmlFechaColuna();
			itemLinhaForm($descricao, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b class=bold10>Acesso Ilimitado: </b>";
			htmlFechaColuna();
			itemLinhaForm(formSelectSimNao($ilimitado,'ilimitado','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();			
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b class=bold10>Horas de Acesso: </b>";
			htmlFechaColuna();
			itemLinhaForm($horas, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b class=bold10>Status: </b>";
			htmlFechaColuna();
			itemLinhaForm(formSelectStatus($status,'status','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		if($comando) {
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Comando: </b>";
				htmlFechaColuna();
				itemLinhaForm($comando, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		}
	
		//htmlFechaColuna();
		//fechaLinhaTabela();
		# fecha linha
	}
	else {
		itemTabelaNOURL('Registro n�o encontrado!', 'left', $corFundo, 2, 'txtaviso');		
	}
	
	fechaTabela();	
	# fim da tabela
	
} #fecha visualizacao




# Formul�rio de sele��o Grupo
function formRadiusSelectGrupo($idGrupo, $campo, $tipo) {

	$consulta=radiusBuscaGrupos('', '','todos','nome');
	
	if($consulta && contaConsulta($consulta)>0) {
	
		if($tipo=='form') {
		
			$retorno="<select name=matriz[$campo] onChange=javascript:submit()>\n";
			
			for($a=0;$a<contaConsulta($consulta);$a++) {
				$tmpID=resultadoSQL($consulta, $a, 'id');
				$tmpNome=resultadoSQL($consulta, $a, 'nome');
			
				if($tmpID==$idGrupo) $opcSelect='selected';
				else $opcSelect='';
				
				$retorno.="<option value=$tmpID $opcSelect>$tmpNome\n";
			}
			
			$retorno.="</select>";
		}
		elseif($tipo=='check') {
			$consulta=radiusBuscaGrupos($idGrupo, 'id','igual','nome');
			
			if($consulta && contaConsulta($consulta)>0) {
				$retorno=resultadoSQL($consulta, 0, 'nome');
			}
			else $retorno='Grupo inv�lido!';
		}
	}

	return($retorno);
}




# Fun��o para buscar o nome do grupo do radius
function radiusBuscaGrupoServicoPlano($idServicoPlano) {

	global $conn, $tb;
	
	
	$sql="
		SELECT
			$tb[RadiusGrupos].nome nome
		FROM
			$tb[RadiusGrupos], 
			$tb[ServicosRadiusGrupos], 
			$tb[Servicos], 
			$tb[ServicosPlanos] 
		WHERE
			$tb[RadiusGrupos].id=$tb[ServicosRadiusGrupos].idRadiusGrupos  
			AND $tb[ServicosRadiusGrupos].idServicos=$tb[Servicos].id 
			AND $tb[ServicosPlanos].idServico=$tb[Servicos].id 
		AND $tb[ServicosPlanos].id=$idServicoPlano
	";
	
	$consulta=consultaSQL($sql, $conn);
	
	
	if($consulta && contaConsulta($consulta)>0) {
		$retorno=resultadoSQL($consulta, 0, 'nome');
	}
	
	return($retorno);

}


?>
