<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 21/05/2003
# Ultima altera��o: 25/09/2003
#    Altera��o No.: 005
#
# Fun��o:
#    Painel - Fun��es para cadastro de cidades

# Fun��o para cadastro
function cidades($modulo, $sub, $acao, $registro, $matriz)
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
		novaTabela2("[Cadastro de Cidades]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 4);
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
					echo "<br><img src=".$html[imagem][cidade]." border=0 align=left><b class=bold>Cidades</b>
					<br><span class=normal10>Cadastro de <b>cidades</b>.</span>";
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
		/*if(!$acao) {
			# Mostrar Status
			itemTabelaNOURL("&nbsp;", 'left', $corFundo, 0, 'normal');
			verStatusPrioridades();
		}*/
	
		# Inclus�o
		if($acao=="adicionar") {
			echo "<br>";
			adicionarCidades($modulo, $sub, $acao, $registro, $matriz);
		}
		
		# Altera��o
		elseif($acao=="alterar") {
			echo "<br>";
			alterarCidades($modulo, $sub, $acao, $registro, $matriz);
		}
		
		# Exclus�o
		elseif($acao=="excluir") {
			echo "<br>";
			excluirCidades($modulo, $sub, $acao, $registro, $matriz);
		}
	
		# Busca
		elseif($acao=="procurar") {
			echo "<br>";
			procurarCidades($modulo, $sub, $acao, $registro, $matriz);
		} #fecha tabela de busca
		
		# Listar
		elseif($acao=="listar" || !$acao) {
			echo "<br>";
			listarCidades($modulo, $sub, $acao, $registro, $matriz);
		} #fecha listagem de servicos
	}


} #fecha menu principal 


# fun��o de busca 
function buscaCidades($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[Cidades] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[Cidades] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[Cidades] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[Cidades] WHERE $texto ORDER BY $ordem";
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




# Funcao para cadastro 
function adicionarCidades($modulo, $sub, $acao, $registro, $matriz)
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
					<span class=normal10>Nome da cidade</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[nome] size=60>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold>UF: </b><br>
					<span class=normal10>Unidade da Federa��o</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[uf] size=2>";
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
		if($matriz[nome] && $matriz[uf]) {
			# Buscar por prioridade
			if(contaConsulta(buscaCidades($matriz[nome], 'nome', 'igual','nome'))>0){
				# Erro - campo inv�lido
				# Mensagem de aviso
				$msg="Cidade j� cadastrada!";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				aviso("Aviso: Dados incorretos", $msg, $url, 760);
			}
			# continuar - campos OK
			else {
				# Cadastrar em banco de dados
				$matriz[nome]=formatarString($matriz[nome],'maiuscula');
				$matriz[uf]=formatarString($matriz[uf],'maiuscula');
				$grava=dbCidade($matriz, 'incluir');
				
				# Verificar inclus�o de registro
				if($grava) {
					# acusar falta de parametros
					# Mensagem de aviso
					$msg="Registro Gravado com Sucesso!";
					$url="?modulo=$modulo&sub=$sub&acao=$acao";
					aviso("Aviso", $msg, $url, 760);
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
function dbCidade($matriz, $tipo)
{
	global $conn, $tb, $modulo, $sub, $acao;
	
	# Sql de inclus�o
	if($tipo=='incluir') {
		$sql="INSERT INTO $tb[Cidades] VALUES (0,
		'$matriz[nome]',
		'$matriz[uf]')";
	} #fecha inclusao
	
	elseif($tipo=='excluir') {
		# Verificar se a prioridade existe
		$tmpBusca=buscaCidades($matriz[id], 'id', 'igual', 'id');
		
		# Registro j� existe
		if(!$tmpBusca|| contaConsulta($tmpBusca)==0) {
			# Mensagem de aviso
			$msg="Registro n�o existe no banco de dados";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Erro ao alterar registro", $msg, $url, 760);
		}
		else {
			$sql="DELETE FROM $tb[Cidades] WHERE id=$matriz[id]";
		}
	}
	
	# Alterar
	elseif($tipo=='alterar') {
		# Verificar se prioridade existe
		$sql="UPDATE $tb[Cidades] SET nome='$matriz[nome]', uf='$matriz[uf]' WHERE id=$matriz[id]";
	}
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
	
} # fecha fun��o de grava��o em banco de dados



# Listar 
function listarCidades($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $moduloApp, $corFundo, $corBorda, $html, $limite;

	# Cabe�alho		
	# Motrar tabela de busca
	novaTabela("[Listar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
		# Sele��o de registros
		$consulta=buscaCidades($texto, $campo, 'todos','nome');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# N�o h� registros
			itemTabelaNOURL('N�o h� registros cadastrados', 'left', $corFundo, 3, 'txtaviso');
		}
		else {
		
			# Paginador
			paginador($consulta, contaConsulta($consulta), $limite[lista][cidades], $registro, 'normal10', 3, $urlADD);
		
			# Cabe�alho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Cidades', 'center', '60%', 'tabfundo0');
				itemLinhaTabela('UF', 'center', '10%', 'tabfundo0');
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

			$limite=$i+$limite[lista][cidades];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$nome=resultadoSQL($consulta, $i, 'nome');
				$uf=resultadoSQL($consulta, $i, 'uf');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($nome, 'left', '60%', 'normal10');
					itemLinhaTabela($uf, 'center', '10%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '30%', 'normal10');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem

	fechaTabela();
	
} # fecha fun��o de listagem


# Fun��o para procura de servi�o
function procurarCidades($modulo, $sub, $acao, $registro, $matriz)
{
	global $conn, $tb, $corFundo, $corBorda, $html, $limite, $textoProcurar;
	
	# Atribuir valores a vari�vel de busca
	if($textoProcurar) {
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
	if($matriz[txtProcurar] && $matriz[bntProcurar]) {
		#buscar registros
		$consulta=buscaCidades("upper(nome) like '%$matriz[txtProcurar]%' OR upper(uf) like '%$matriz[txtProcurar]%'",$campo, 'custom','nome');

		echo "<br>";

		novaTabela("[Resultados]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
	
		if(!$consulta || contaConsulta($consulta)==0 ) {
			# N�o h� registros
			itemTabelaNOURL('N�o foram encontrados registros cadastrados', 'left', $corFundo, 3, 'txtaviso');
		}
		elseif($consulta && contaConsulta($consulta)>0 && (!$registro || is_numeric($registro)) ) {	
		
			itemTabelaNOURL('Registros encontrados procurando por ('.$matriz[txtProcurar].'): '.contaConsulta($consulta).' registro(s)', 'left', $corFundo, 5, 'txtaviso');

			# Paginador
			$urlADD="&textoProcurar=".$matriz[txtProcurar];
			paginador($consulta, contaConsulta($consulta), $limite[lista][cidades], $registro, 'normal', 5, $urlADD);

			# Cabe�alho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Cidades', 'center', '60%', 'tabfundo0');
				itemLinhaTabela('UF', 'center', '10%', 'tabfundo0');
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

			$limite=$i+$limite[lista][cidades];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$nome=resultadoSQL($consulta, $i, 'nome');
				$uf=resultadoSQL($consulta, $i, 'uf');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($nome, 'left', '60%', 'normal10');
					itemLinhaTabela($uf, 'center', '10%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '30%', 'normal10');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem
	} # fecha bot�o procurar
} #fecha funcao de  procurar 



# Funcao para altera��o
function alterarCidades($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# ERRO - Registro n�o foi informado
	if(!$registro) {
		# ERRO
	}
	# Form de inclusao
	elseif($registro && !$matriz[bntAlterar]) {
	
		# Buscar Valores
		$consulta=buscaCidades($registro, 'id', 'igual', 'id');
		if(!$consulta || contaConsulta($consulta)==0) {
			# Mostrar Erro
			$msg="Registro n�o foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso", $msg, $url, 760);
		}
		else {
			#atribuir valores
 			$nome=resultadoSQL($consulta, 0, 'nome');
			$uf=resultadoSQL($consulta, 0, 'uf');
			
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
						echo "<b class=bold>Cidade: </b><br>
						<span class=normal10>Nome da cidade</span>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[nome] size=60 value='$nome'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>UF: </b><br>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[uf] size=2 value='$uf'>";
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

		} #fecha alteracao
	} #fecha form - !$bntAlterar
	
	# Altera��o - bntAlterar pressionado
	elseif($matriz[bntAlterar]) {
		# Conferir campos
		if($matriz[nome] && $matriz[uf]) {
			# continuar
			# Cadastrar em banco de dados
			$grava=dbCidade($matriz, 'alterar');
			
			# Verificar inclus�o de registro
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				$url="?modulo=$modulo&sub=$sub";
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
	} #fecha bntAlterar
	
} # fecha funcao de altera��o


# Exclus�o de servicos
function excluirCidades($modulo, $sub, $acao, $registro, $matriz)
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
		$consulta=buscaCidades($registro, 'id', 'igual', 'id');
		
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
			$uf=resultadoSQL($consulta, 0, 'uf');
			
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
						echo "<b class=bold>Cidade: </b>";
					htmlFechaColuna();
					itemLinhaForm($nome, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>UF: </b>";
					htmlFechaColuna();
					itemLinhaForm($uf, 'left', 'top', $corFundo, 0, 'tabfundo1');
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
		$grava=dbCidade($matriz, 'excluir');
				
		# Verificar inclus�o de registro
		if($grava) {
			# Mensagem de aviso
			$msg="Registro exclu�do com Sucesso!";
			$url="?modulo=$modulo&sub=$sub";
			aviso("Aviso", $msg, $url, 760);
		}
		
	} #fecha bntExcluir
	
} #fecha exclusao 




# Formul�rio de sele��o de Tipo de Endere�o
function formSelectCidade($cidade, $uf, $campo, $tipo) {

	$consulta=buscaCidades("uf='$uf'", 'id, nome, uf','custom','nome');
	
	if($consulta && contaConsulta($consulta)>0) {
	
		if($tipo=='form') {
		
			$retorno="<select name=matriz[$campo] onChange=javascript:submit()>\n";
			
			for($a=0;$a<contaConsulta($consulta);$a++) {
				$tmpID=resultadoSQL($consulta, $a, 'id');
				$tmpUF=resultadoSQL($consulta, $a, 'uf');
				$tmpNome=resultadoSQL($consulta, $a, 'nome');
			
				if($tmpID==$cidade) $opcSelect='selected';
				else $opcSelect='';
				
				$retorno.="<option value=$tmpID $opcSelect>$tmpNome/$tmpUF\n";
			}
			
			
			$retorno.="</select>";
		}
		elseif($tipo=='check') {
			$consulta=buscaCidades($cidade, 'id','igual','nome');
			
			if($consulta && contaConsulta($consulta)>0) {
				$retorno=resultadoSQL($consulta, 0, 'nome');
			}
			else $retorno='Cidade inv�lida!';
		}
	}

	return($retorno);
	
}



# Formul�rio de sele��o de Tipo de Endere�o
function formSelectUF($uf, $campo, $tipo) {

	global $tb, $conn;
	
	$sql="SELECT distinct uf FROM $tb[Cidades] ORDER BY uf";
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
	
		if($tipo=='form') {
		
			$retorno="<select name=matriz[$campo] onChange=javascript:submit()>\n";
			
			for($a=0;$a<contaConsulta($consulta);$a++) {
				$tmpUF=resultadoSQL($consulta, $a, 'uf');
			
				if($uf && $tmpUF==$uf) $opcSelect='selected';
				elseif(!$uf && $tmpUF=='SP') $opcSelect='selected';
				else $opcSelect='';
				
				$retorno.="<option value=$tmpUF $opcSelect>$tmpUF\n";
			}
			
			
			$retorno.="</select>";
		}
		elseif($tipo=='check') {
			$consulta=buscaCidades($uf,'uf','igual','uf');
			
			if($consulta && contaConsulta($consulta)>0) {
				$retorno=resultadoSQL($consulta, 0, 'uf');
			}
			else $retorno='UF inv�lido!';
		}
	}

	return($retorno);
	
}



/**
 * @return unknown
 * @param unknown $idCidade
 * @desc retorna um array com o nome da cidade e o estado(uf)
*/
function checkCidade($idCidade) {

	# Buscar por cidade
	$consulta=buscaCidades($idCidade, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		$retorno[nome]=resultadoSQL($consulta, 0, 'nome');
		$retorno[uf]=resultadoSQL($consulta, 0, 'uf');
	}
	
	return($retorno);

}


# Buscar ID da cidade
function buscaIDCidade($nome, $uf) {
	
	$nome=strtoupper($nome);
	$uf=strtoupper($uf);
	
	$consulta=buscaCidades("upper(nome) = '$nome' AND upper(uf) = '$uf'", '', 'custom','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		return(resultadoSQL($consulta, 0, 'id'));
	}
}

?>
