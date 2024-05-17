<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 16/04/2003
# Ultima altera��o: 14/01/2004
#    Altera��o No.: 006
#
# Fun��o:
#    Painel - Fun��es para cadastro de prioridades


# Fun��o para cadastro
function prioridades($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin;
	
	# Permiss�o do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin]) {
		# SEM PERMISS�O DE EXECUTAR A FUN��O
		$msg="ATEN��O: Voc� n�o tem permiss�o para executar esta fun��o";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		# Topo da tabela - Informa��es e menu principal do Cadastro
		novaTabela2("[Prioridades]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 4);
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
					echo "<br><img src=".$html[imagem][cadastro]." border=0 align=left><b class=bold>Prioridades</b>
					<br><span class=normal10>Cadastro de <b>prioridades</b> para sistema de ocorr�ncias.</span>";
				htmlFechaColuna();			
				$texto=htmlMontaOpcao("<br>Adicionar", 'incluir');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=adicionar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Procurar", 'procurar');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=procurar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Listar", 'listar');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=listar", 'center', $corFundo, 0, 'normal');
			fechaLinhaTabela();
		fechaTabela();
		
		# Mostrar Status caso n�o seja informada a a��o
		if(!$acao || $acao=='listar') {
			# Mostrar Status
			echo "<br>";
					# Listar
			listarPrioridades($modulo, $sub, $acao, $registro, $matriz);
		}
	
		# Inclus�o
		if($acao=="adicionar") {
			echo "<br>";
			incluirPrioridades($modulo, $sub, $acao, $registro, $matriz);
		}
		
		# Altera��o
		elseif($acao=="alterar") {
			echo "<br>";
			alterarPrioridades($modulo, $sub, $acao, $registro, $matriz);
		}
		
		# Exclus�o
		elseif($acao=="excluir") {
			echo "<br>";
			excluirPrioridades($modulo, $sub, $acao, $registro, $matriz);
		}
	
		# Busca
		elseif($acao=="procurar") {
			echo "<br>";
			procurarPrioridades($modulo, $sub, $acao, $registro, $matriz);
		} #fecha tabela de busca
		
	}


} #fecha menu principal 


# fun��o de busca 
function buscaPrioridades($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[Prioridades] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[Prioridades] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[Prioridades] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[Prioridades] WHERE $texto ORDER BY $ordem";
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
	
} # fecha fun��o de busca


# Listar 
function listarPrioridades($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $moduloApp, $corFundo, $corBorda, $html, $limite;

	# Cabe�alho		
	# Motrar tabela de busca
	novaTabela("[Listar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
		# Sele��o de registros
		$consulta=buscaPrioridades($texto, $campo, 'todos','nome');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# N�o h� registros
			itemTabelaNOURL('N�o h� registros cadastrados', 'left', $corFundo, 3, 'txtaviso');
		}
		else {
		
			# Paginador
			paginador($consulta, contaConsulta($consulta), $limite[lista][prioridades], $registro, 'normal10', 5, $urlADD);
		
			# Cabe�alho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Prioridade', 'left', '30%', 'tabfundo0');
				itemLinhaTabela('Descri��o', 'left', '30%', 'tabfundo0');
				itemLinhaTabela('Cor', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Valor', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Op��es', 'center', '30%', 'tabfundo0');
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

			$limite=$i+$limite[lista][prioridades];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$nome=resultadoSQL($consulta, $i, 'nome');
				$descricao=resultadoSQL($consulta, $i, 'texto');
				$cor=resultadoSQL($consulta, $i, 'cor');
				$valor=resultadoSQL($consulta, $i, 'valor');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($nome, 'left', '30%', 'normal10');
					itemLinhaTabela($descricao, 'left', '30%', 'normal10');
					itemLinhaTabela($cor, 'center', '10%', 'normal10');
					itemLinhaTabela($valor, 'center', '10%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '30%', 'normal10');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem

	fechaTabela();
	
} # fecha fun��o de listagem



# Fu��o para visualiza��o de status 
function verStatusPrioridades()
{
	global $conn, $tb, $corFundo, $corBorda, $html;

	# Motrar tabela de busca
	novaTabela2("[Informa��es sobre Prioridades]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('60%', 'left', $corFundo, 0, 'tabfundo1');
			echo "<br><img src=".$html[imagem][status]." border=0 align=left><b class=bold>Status de Prioridades</b><br>
			<span class=normal10>Status e informa��es sobre prioridades.";
			htmlFechaColuna();
			htmlAbreColuna('10', 'left', $corFundo, 0, 'normal');
				echo "&nbsp;";
			htmlFechaColuna();
			
			
			htmlAbreColuna('40%', 'left', $corFundo, 0, 'normal');
				# Mostrar status
				$busca=buscaPrioridades($texto, $campo, 'todos', 'id');
				if($busca) {
					$numBusca=contaConsulta($busca);
				}
				else {
					$numBusca=0;
				}
				
				htmlAbreTabelaSH('left', '100%', 0, 2, 1, $corFundo, $corBorda, 2);
					novaLinhaTabela($corFundo, '100%');
						itemLinhaNOURL('N�mero de Registros:', 'right', $corFundo, $colunas, 'bold10');
						itemLinhaNOURL("$numBusca prioridade(s)&nbsp;cadastrada(s)", 'left', $corFundo, $colunas, 'normal10');
					fechaLinhaTabela();
				fechaTabela();	
			htmlFechaColuna();
		fechaLinhaTabela();
	fechaTabela();	
} #fecha status 


# Funcao para cadastro 
function incluirPrioridades($modulo, $sub, $acao, $registro, $matriz)
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
				<input type=hidden name=registro value=$registro>
				&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold>Nome: </b><br>
					<span class=normal10>Nome da prioridade, utilizado para identifica��o</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[nome] size=60>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold>Descri��o: </b><br>
					<span class=normal10>Descri��o detalhada sobre a prioridade</span>";
				htmlFechaColuna();
				$texto="<textarea name=matriz[descricao] rows=4 cols=60></textarea>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold>Cor: </b><br>";
				htmlFechaColuna();
				itemLinhaForm(formSelectCor('#11c411','cor'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold>Valor da priodade: </b><br>";
				htmlFechaColuna();
				itemLinhaForm(formSelectNumeros(0,'valor'), 'left', 'top', $corFundo, 0, 'tabfundo1');
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
		if($matriz[nome] && $matriz[descricao]) {
			# Buscar por prioridade
			if(contaConsulta(buscaPrioridades($matriz[nome], 'nome', 'igual','nome'))>0){
				# Erro - campo inv�lido
				# Mensagem de aviso
				$msg="Prioridade j� cadastrada!";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				aviso("Aviso: Dados incorretos", $msg, $url, 760);
			}
			# continuar - campos OK
			else {
				# Cadastrar em banco de dados
				$grava=dbPrioridade($matriz, 'incluir');
				
				# Verificar inclus�o de registro
				if($grava) {
					# acusar falta de parametros
					# Mensagem de aviso
					$msg="Registro Gravado com Sucesso!";
					avisoNOURL("Aviso", $msg, 400);
					
					# Listar prioridades
					echo "<br>";
					listarPrioridades($modulo, $sub, 'listar',$registro, '');
				}
				
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

} # fecha funcao de inclusao



# Fun��o para grava��o em banco de dados
function dbPrioridade($matriz, $tipo)
{
	global $conn, $tb, $modulo, $sub, $acao;
	
	# Sql de inclus�o
	if($tipo=='incluir') {
		$sql="INSERT INTO $tb[Prioridades] VALUES (0,
		'$matriz[nome]',
		'$matriz[descricao]',
		'$matriz[cor]',
		'$matriz[valor]')";
	} #fecha inclusao
	
	elseif($tipo=='excluir') {
		# Verificar se a prioridade existe
		$tmpBusca=buscaPrioridades($matriz[id], 'id', 'igual', 'id');
		
		# Registro j� existe
		if(!$tmpBusca|| contaConsulta($tmpBusca)==0) {
			# Mensagem de aviso
			$msg="Registro n�o existe no banco de dados";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Erro ao alterar registro", $msg, $url, 760);
		}
		else {
			$sql="DELETE FROM $tb[Prioridades] WHERE id=$matriz[id]";
		}
	}
	
	# Alterar
	elseif($tipo=='alterar') {
		# Verificar se prioridade existe
		$sql="UPDATE $tb[Prioridades] SET texto='$matriz[texto]', nome='$matriz[nome]', cor='$matriz[cor]', valor='$matriz[valor]' WHERE id=$matriz[id]";
	}
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
	
} # fecha fun��o de grava��o em banco de dados



# Exclus�o de servicos
function excluirPrioridades($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# ERRO - Registro n�o foi informado
	if(!$registro) {
		# Mostrar Erro
		$msg="Registro n�o foi encontrado!";
		$url="?modulo=$modulo&sub=$sub&acao=$acao";
		aviso("Aviso", $msg, $url, 760);
	}
	# Form de inclusao
	elseif($registro && !$matriz[bntExcluir]) {
	
		# Buscar Valores
		$consulta=buscaPrioridades($registro, 'id', 'igual', 'id');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Mostrar Erro
			$msg="Registro n�o foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso", $msg, $url, 760);
		}
		else {
			#atribuir valores
			$id=resultadoSQL($consulta, 0, 'id');
			$nome=resultadoSQL($consulta, 0, 'nome');
			$descricao=resultadoSQL($consulta, 0, 'texto');
			$cor=resultadoSQL($consulta, 0, 'cor');
			$valor=resultadoSQL($consulta, 0, 'valor');
			
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
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro>
					<input type=hidden name=matriz[id] value=$registro>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Prioridade: </b>";
					htmlFechaColuna();
					itemLinhaForm($nome, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Descri��o: </b>";
					htmlFechaColuna();
					itemLinhaForm($descricao, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Cor: </b>";
					htmlFechaColuna();
					itemLinhaForm($cor, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Valor: </b>";
					htmlFechaColuna();
					itemLinhaForm($valor, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
	
				# Bot�o de confirma��o
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();
					$texto="<input type=submit name=matriz[bntExcluir] value=Excluir class=submit>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		} #fecha alteracao
	} #fecha form - !$bntExcluir
	
	# Altera��o - bntExcluir pressionado
	elseif($matriz[bntExcluir]) {
		# Cadastrar em banco de dados
		$grava=dbPrioridade($matriz, 'excluir');
				
		# Verificar inclus�o de registro
		if($grava) {
			# Mensagem de aviso
			$msg="Registro Gravado com Sucesso!";
			avisoNOURL("Aviso", $msg, 400);
			
			# Listar prioridades
			echo "<br>";
			listarPrioridades($modulo, $sub, 'listar', '', '');
		}
		
	} #fecha bntExcluir
	
} #fecha exclusao 



# Funcao para altera��o
function alterarPrioridades($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# ERRO - Registro n�o foi informado
	if(!$registro) {
		# ERRO
	}
	# Form de inclusao
	elseif($registro && !$matriz[bntAlterar]) {
	
		# Buscar Valores
		$consulta=buscaPrioridades($registro, 'id', 'igual', 'id');
		if(!$consulta || contaConsulta($consulta)==0) {
			# Mostrar Erro
			$msg="Registro n�o foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso", $msg, $url, 760);
		}
		else {
			 #atribuir valores
 			 $nome=resultadoSQL($consulta, 0, 'nome');
 			 $descricao=resultadoSQL($consulta, 0, 'texto');
 			 $cor=resultadoSQL($consulta, 0, 'cor');
 			 $valor=resultadoSQL($consulta, 0, 'valor');
					
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
					<input type=hidden name=registro value=$registro>
					<input type=hidden name=matriz[id] value=$registro>
					<input type=hidden name=acao value=$acao>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Prioridade: </b><br>
						<span class=normal10>Nome da prioridade, utilizado para classifica��o dos tickets</span>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[nome] size=60 value='$nome'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Descri��o: </b><br>
						<span class=normal10>Descri��o detalhada sobre a prioridade</span>";
					htmlFechaColuna();
					$texto="<textarea name=matriz[texto] rows=4 cols=60>$descricao</textarea>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Cor: </b><br>";
					htmlFechaColuna();
					itemLinhaForm(formSelectCor($cor,'cor'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Valor: </b><br>";
					htmlFechaColuna();
					itemLinhaForm(formSelectNumeros($valor,'valor'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();
					$texto="<input type=submit name=matriz[bntAlterar] value=Alterar class=submit>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();				

		} #fecha alteracao
	} #fecha form - !$bntAlterar
	
	# Altera��o - bntAlterar pressionado
	elseif($matriz[bntAlterar]) {
		# Conferir campos
		if($matriz[texto] && $matriz[nome]) {
			# continuar
			# Cadastrar em banco de dados
			$grava=dbPrioridade($matriz, 'alterar');
			
			# Verificar inclus�o de registro
			if($grava) {
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);
				
				# Listar prioridades
				echo "<br>";
				listarPrioridades($modulo, $sub, 'listar', '', '');
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
	} #fecha bntAlterar
	
} # fecha funcao de altera��o



# Fun��o para procura de servi�o
function procurarPrioridades($modulo, $sub, $acao, $registro, $matriz)
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

	# Caso bot�o procurar seja pressionado
	if( $matriz[bntProcurar] && $matriz[txtProcurar] ) {
		#buscar registros
		$consulta=buscaPrioridades("upper(texto) like '%$matriz[txtProcurar]%' OR upper(nome) like '%$matriz[txtProcurar]%' OR upper(cor) like '%$matriz[txtProcurar]%'",$campo, 'custom','nome');

		echo "<br>";

		novaTabela("[Resultados]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
	
		if(!$consulta || contaConsulta($consulta)==0 ) {
			# N�o h� registros
			itemTabelaNOURL('N�o foram encontrados registros cadastrados', 'left', $corFundo, 3, 'txtaviso');
		}
		elseif($consulta && contaConsulta($consulta)>0 && (!$registro || is_integer($registro)) ) {	
		
			itemTabelaNOURL('Registros encontrados procurando por ('.$matriz[txtProcurar].'): '.contaConsulta($consulta).' registro(s)', 'left', $corFundo, 5, 'txtaviso');

			# Paginador
			$urlADD="&textoProcurar=".$matriz[txtProcurar];
			paginador($consulta, contaConsulta($consulta), $limite[lista][prioridades], $registro, 'normal', 5, $urlADD);

			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Prioridade', 'left', '30%', 'tabfundo0');
				itemLinhaTabela('Descri��o', 'left', '30%', 'tabfundo0');
				itemLinhaTabela('Cor', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Valor', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Op��es', 'center', '30%', 'tabfundo0');
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
			
			
			$limite=$i+$limite[lista][prioridades];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$nome=resultadoSQL($consulta, $i, 'nome');
				$descricao=resultadoSQL($consulta, $i, 'texto');
				$cor=resultadoSQL($consulta, $i, 'cor');
				$valor=resultadoSQL($consulta, $i, 'valor');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.="&nbsp;&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($nome, 'left', '30%', 'normal10');
					itemLinhaTabela($descricao, 'left', '30%', 'normal10');
					itemLinhaTabela($cor, 'center', '10%', 'normal10');
					itemLinhaTabela($valor, 'center', '10%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '20%', 'normal10');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem
		fechaTabela();
	} # fecha bot�o procurar
} #fecha funcao de  procurar de servi�cos (Procurar



# Fun��o para montar campo de formulario
function formSelectPrioridade($prioridade, $campo, $tipo)
{
	global $conn, $tb;
	
	# Buscar Servi�os de servidor (ja cadastrados)
	$consulta=buscaPrioridades('', '','todos','valor ASC');
	
	if($tipo=='form') {
	
		$item="<select name=matriz[$campo]>\n";
		
		# Listargem
		for($i=0;$i<contaConsulta($consulta);$i++) {
			# Valores dos campos
			$id=resultadoSQL($consulta, $i, 'id');
			$nome=resultadoSQL($consulta, $i, 'nome');
			
			$opcSelect='';
			if($id == $prioridade) $opcSelect='selected';
			
			$item.="<option value=$id $opcSelect>$nome\n";
	
		}
		
		$item.="</select>";
	}
	elseif($tipo=='check') {
		$consulta=buscaPrioridades($prioridade,'id','igual','id');
		if($consulta && contaConsulta($consulta)>0) {
			$item[nome]=resultadoSQL($consulta, 0, 'nome');
			$item[cor]=resultadoSQL($consulta, 0, 'cor');
			$item[texto]=resultadoSQL($consulta, 0, 'texto');
			$item[valor]=resultadoSQL($consulta, 0, 'valor');
			$item[nomeFormatado]="<font color=$item[cor]><b>".$item[nome]."</b></font>";
		}
	}
	
	return($item);
	
} #fecha funcao de montagem de campo de form




# Fun��o para checagem de status
function checaPrioridade($prioridade) {
	global $conn;
	
	$consulta=buscaPrioridades($prioridade, 'id','igual','id');
	
	if($consulta) {
		$retorno[nome]=resultadoSQL($consulta, 0, 'nome');
		$retorno[cor]=resultadoSQL($consulta, 0, 'cor');
		$retorno[texto]=resultadoSQL($consulta, 0, 'texto');
	}
	
	return($retorno);
}


# Fun��o para atualiza��o de Prioridade de Ticket
function atualizaPrioridadeTicket($ticket, $prioridade) {
	global $conn, $tb, $modulo, $sub, $acao;
	
	$sql="UPDATE $tb[Ticket] SET idPrioridade=$prioridade WHERE id=$ticket";
	$consulta=consultaSQL($sql, $conn);
	
	if(!$consulta) {
		# Erro
		$msg="Erro ao atualizar Prioridade do Ticket!";
		$url="?modulo=$modulo&sub=$sub&acao=ver&registro=$ticket";
		aviso("Erro", $msg, $url, 760);
	}
}

?>
