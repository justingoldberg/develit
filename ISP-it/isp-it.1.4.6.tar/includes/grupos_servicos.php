<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 23/03/2004
# Ultima altera��o: 24/03/2004
#    Altera��o No.: 002
#     Alterado por: Hugo Ribeiro - hugo@devel-it
#
# Fun��o:
#    Fun��es para Agrupamento de Servi�os

function dbGruposServicos($matriz, $tipo) {
	
	global $conn, $tb, $modulo, $sub, $acao;
	$tabela="GruposServicos";
	# Sql de inclus�o
	if($tipo=='incluir') {
		$sql="INSERT INTO $tb[$tabela] VALUES (0,
		'$matriz[nome]',
		'$matriz[descricao]',
		'$matriz[dtCadastro]'
		)";
	} 
	# Exclus�o
	elseif($tipo=='excluir') {
		#apaga os servi�os
		$matriz[idGrupos]=$matriz[id];
		$filhos=buscaServicoGruposServicos($matriz[id], 'idGrupos', 'igual', 'id');
		if($filhos && contaConsulta($filhos)>0) {
			
			for($i=0;$i<contaConsulta($filhos);$i++) {
				$matriz[idServico]=resultadoSQL($filhos, $i, 'id');
				dbServicoGruposServicos($matriz, $tipo);
			}
			
		} 
		#apaga o grupo
		$sql="DELETE FROM $tb[$tabela] where id=$matriz[id]";	
	} 
	# Altera��o
	elseif($tipo=='alterar') {
		$sql="UPDATE $tb[$tabela] 
			SET 
				nome='$matriz[nome]',
				descricao='$matriz[descricao]' 
			WHERE 
				id=$matriz[id]";
	}
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
}


function GruposServicos($modulo, $sub, $acao, $registro, $matriz) {

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
		novaTabela2("[Cadastro de Grupos de Servi�os]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 4);
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
					echo "<br><img src=".$html[imagem][configuracoes]." border=0 align=left><b class=bold>Grupos de Servi�o</b>
					<br><span class=normal10>Agrupamentos de Servi�os para melhor visualiza��o e extra��o de dados para 
					relat�rios e estat�sticas.</span>";
				htmlFechaColuna();			
				$texto=htmlMontaOpcao("<br>Adicionar", 'incluir');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=adicionar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Procurar", 'procurar');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=procurar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Listar", 'listar');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=listar", 'center', $corFundo, 0, 'normal');
			fechaLinhaTabela();
		fechaTabela();
		
		
		if(!$acao) {
			# Mostrar listagem
			echo "<br>";
			procurarGruposServicos($modulo, $sub, $acao, $registro, $matriz);			
		}
		
		# Inclus�o
		if($acao=="adicionar") {
			echo "<br>";
			adicionarGruposServicos($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($acao=='procurar') {
			echo "<br>";
			procurarGruposServicos($modulo, $sub, $acao, $registro, $matriz);
		}
		# Listar
		elseif($acao=="listar") {
			echo "<br>";
			listarGruposServicos($modulo, $sub, $acao, $registro, $matriz);
		}
		# Excluir
		elseif($acao=="excluir") {
			echo "<br>";
			excluirGruposServicos($modulo, $sub, $acao, $registro, $matriz);
		}
		# ver
		elseif($acao=="ver") {
			echo "<br>";
			verGruposServicos($modulo, $sub, $acao, $registro, $matriz);
		}
		# Alterar
		elseif($acao=="alterar") {
			echo "<br>";
			alterarGruposServicos($modulo, $sub, $acao, $registro, $matriz);
		}
		# Servicos
		elseif($acao=="servicos") {
			echo "<br>";
			servicoGruposServicos($modulo, $sub, $acao, $registro, $matriz);
		}
		# Servicos Adicionar
		elseif($acao=="servicosadicionar") {
			echo "<br>";
			adicionarServicoGruposServicos($modulo, $sub, $acao, $registro, $matriz);
		}
		# Servicos Excluir
		elseif($acao=="servicosexcluir") {
			echo "<br>";
			excluirServicoGruposServicos($modulo, $sub, $acao, $registro, $matriz);
		}
	}	
}

/**
Lista todos os grupos de servi�os existentes no banco de dados.
*/
function listarGruposServicos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $moduloApp, $corFundo, $corBorda, $html, $limite;

	# Cabe�alho		
	# Motrar tabela de busca
	novaTabela("[Listar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
		# Sele��o de registros
		$consulta=buscaGruposServicos($texto, $campo, 'todos','descricao');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# N�o h� registros
			itemTabelaNOURL('N�o h� grupos cadastrados', 'left', $corFundo, 5, 'txtaviso');
		}
		else {
			# Paginador
			paginador($consulta, contaConsulta($consulta), $limite[lista][servicos], $registro, 'normal10', 3, $urlADD);
		
			#monta o cabecalho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Grupo', 'center', '25%', 'tabfundo0');
				itemLinhaTabela('Descri��o', 'center', '30%', 'tabfundo0');
				itemLinhaTabela('Data do Cadastro', 'center', '15%', 'tabfundo0');
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

			$limite=$i+$limite[lista][servicos];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$nome=resultadoSQL($consulta, $i, 'nome');
				$descricao=resultadoSQL($consulta, $i, 'descricao');
				$dt=resultadoSQL($consulta, $i, 'dtCadastro');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=servicos&registro=$id>Servi�os</a>",'servicos');

				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($nome, 'left', '25%', 'normal10');
					itemLinhaTabela($descricao, 'left', '30%', 'normal8');
					itemLinhaTabela(converteData($dt, 'banco','formdata'), 'center', '15%', 'normal8');
					itemLinhaTabela($opcoes, 'left', '30%', 'normal8');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem

	fechaTabela();
} # fecha fun��o de listagem


/**
Faz uma busca nos grupos de servi�os total ou parcialmente.
*/
function buscaGruposServicos($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	$tabela="GruposServicos";
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[$tabela] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[$tabela] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[$tabela] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[$tabela] WHERE $texto ORDER BY $ordem";
	}
	
	# Verifica consulta
	if($sql){
		$consulta=consultaSQL($sql, $conn);
		# Retornar consulta
		return($consulta);
	}
	else {	
		# Mensagem de aviso
		$msg="Consulta n�o pode ser realizada por falta de par�metros";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
	}
	
} # fecha fun��o de busca


# Fun��o para procura de servi�o
function procurarGruposServicos($modulo, $sub, $acao, $registro, $matriz)
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
		$consulta=buscaGruposServicos("upper(descricao) like '%$matriz[txtProcurar]%' OR nome like '%$matriz[txtProcurar]%' ",$campo, 'custom','nome');

		echo "<br>";

		novaTabela("[Resultados]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
	
		if(!$consulta || contaConsulta($consulta)==0 ) {
			# N�o h� registros
			itemTabelaNOURL('N�o foram encontrados grupos cadastrados', 'left', $corFundo, 5, 'txtaviso');
		}
		elseif($consulta && contaConsulta($consulta)>0 && (!$registro || is_numeric($registro)) ) {	
		
			itemTabelaNOURL('Registros encontrados procurando por ('.$matriz[txtProcurar].'): '.contaConsulta($consulta).' registro(s)', 'left', $corFundo, 5, 'txtaviso');

			# Paginador
			$urlADD="&textoProcurar=".$matriz[txtProcurar];
			
			# Paginador
			paginador($consulta, contaConsulta($consulta), $limite[lista][servicos], $registro, 'normal10', 5, $urlADD);
		
			# Cabe�alho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Grupo', 'center', '20%', 'tabfundo0');
				itemLinhaTabela('Descri��o', 'center', '30%', 'tabfundo0');
				itemLinhaTabela('Data do Cadastro', 'center', '15%', 'tabfundo0');
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

			$limite=$i+$limite[lista][servicos];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$nome=resultadoSQL($consulta, $i, 'nome');
				$descricao=resultadoSQL($consulta, $i, 'descricao');
				$dtCadastro=resultadoSQL($consulta, $i, 'dtCadastro');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=servicos&registro=$id>Servi�os</a>",'servicos');

				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($nome, 'left', '15%', 'normal10');
					itemLinhaTabela($descricao, 'left', '30%', 'normal8');
					itemLinhaTabela($dtCadastro, 'center', '15%', 'normal8');
					itemLinhaTabela($opcoes, 'left', '40%', 'normal8');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;			
			} #fecha laco de montagem de tabela
		} #fecha listagem
	} # fecha bot�o procurar
} #fecha funcao de  procurar 



# Funcao para cadastro 
function adicionarGruposServicos($modulo, $sub, $acao, $registro, $matriz)
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
				<input type=hidden name=acao value=$acao>&nbsp;
				<input type=hidden name=registro>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b>Nome: </b><br>
					<span class=normal10>Nome para identifica��o do grupo</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[nome] size=60>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b>Descri��o: </b><br>
					<span class=normal10>Descri��o do grupo</span>";
				htmlFechaColuna();
				$texto="<textarea name=matriz[descricao] rows=3 cols=60></textarea>";
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
		if($matriz[nome] && $matriz[descricao]) {
		
			# Cadastrar em banco de dados
			$dt=dataSistema();
			$matriz[dtCadastro]=$dt[dataBanco];
			$grava=dbGruposServicos($matriz, 'incluir');
			
			# Verificar inclus�o de registro
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				$url="?modulo=$modulo&sub=$sub&acao=listar&registro=$registro";
				avisoNOURL("Aviso", $msg, 760);
				
				echo "<br>";
				$acao='listar';
				listarGruposServicos($modulo, $sub, $acao, $registro, $matriz);
			}
			else {
				# Mensagem de aviso
				$msg="Erro ao gravar Servi�o!";
				$url="?modulo=$modulo&sub=$sub&acao=listar&registro=$registro";
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

} # fecha funcao de inclusao


# Funcao para altera��o
function alterarGruposServicos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# ERRO - Registro n�o foi informado
	if(!$registro) {
		# ERRO
	}
	# Form de inclusao
	elseif($registro && !$matriz[bntAlterar]) {
	
		# Buscar Valores
		$consulta=buscaGruposServicos($registro, 'id', 'igual', 'id');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Mostrar Erro
			$msg="Registro n�o foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso", $msg, $url, 760);
		}
		else {
			#atribuir valores
 			$descricao=resultadoSQL($consulta, 0, 'descricao');
 			$nome=resultadoSQL($consulta, 0, 'nome');
			$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
			
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
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro>
					<input type=hidden name=matriz[id] value=$registro>
					<input type=hidden name=acao value=$acao>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Nome: </b>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[nome] size=60 value='$nome'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Descri��o: </b>";
					htmlFechaColuna();
					$texto="<textarea name=matriz[descricao] rows=3 cols=60>$descricao</textarea>";
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
		if($matriz[nome] && $matriz[descricao]) {
			
			# Cadastrar em banco de dados
			$grava=dbGruposServicos($matriz, 'alterar');
			
			# Verificar inclus�o de registro
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro alterado com sucesso!";
				$url="?modulo=$modulo&sub=$sub&acao=listar";
				aviso("Aviso", $msg, $url, 400);
				
				itemTabelaNOURL("&nbsp;", 'left', $corFundo, 0, 'normal');
				listarGruposServicos($modulo, $sub, 'listar', 0, $matriz);
			}
			else {
				# Mensagem de aviso
				$msg="Erro ao gravar Servi�o!";
				$url="?modulo=$modulo&sub=$sub&acao=listar";
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



# Funcao para exclus�o
function excluirGruposServicos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# ERRO - Registro n�o foi informado
	if(!$registro) {
		# ERRO
	}
	# Form de inclusao
	elseif($registro && !$matriz[bntExcluir]) {
	
		# Buscar Valores
		$consulta=buscaGruposServicos($registro, 'id', 'igual', 'id');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Mostrar Erro
			$msg="Registro n�o foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso", $msg, $url, 760);
		}
		else {
			#atribuir valores
			$id=resultadoSQL($consulta, 0, 'id');
 			$descricao=resultadoSQL($consulta, 0, 'descricao');
 			$nome=resultadoSQL($consulta, 0, 'nome');
			$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
			
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
					<input type=hidden name=matriz[id] value=$registro>
					<input type=hidden name=acao value=$acao>&nbsp;";
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
						echo "<b>Descri��o: </b>";
					htmlFechaColuna();
					itemLinhaForm($descricao, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Data de Cadastro: </b>";
					htmlFechaColuna();
					itemLinhaForm(converteData($dtCadastro,'banco','formdata'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();
					$texto="<input type=submit name=matriz[bntExcluir] value=Excluir class=submit>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();				

		} #fecha 
	} #fecha form - !$bntExcluir
	
	# Exclusao
	elseif($matriz[bntExcluir]) {
		# Conferir campos
		
		# Cadastrar em banco de dados
		$grava=dbGruposServicos($matriz, 'excluir');
		echo "<br>";
		# Verificar 
		if($grava) {
			# Mensagem de aviso
			$msg="Registro Exclu�do com Sucesso!";
			$url="?modulo=$modulo&sub=$sub&acao=listar";
			aviso("Aviso", $msg, $url, 400);
				
			itemTabelaNOURL("&nbsp;", 'left', $corFundo, 0, 'normal');
			listarGruposServicos($modulo, $sub, 'listar', 0, $matriz);
		}
		else {
			# Mensagem de aviso
			$msg="Erro ao excluir Servi�o!";
			$url="?modulo=$modulo&sub=$sub&acao=listar";
			aviso("Aviso", $msg, $url, 760);
		}
	} #fecha bntExclusao
	
} # fecha funcao de Exclus�o


# Funcao para visualiza��o
function verGruposServicos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# ERRO - Registro n�o foi informado
	if(!$registro) {
		# ERRO
	}
	# Form de inclusao
	elseif($registro) {
	
		# Buscar Valores
		$consulta=buscaGruposServicos($registro, 'id', 'igual', 'id');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Mostrar Erro
			$msg="Registro n�o foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso", $msg, $url, 760);
		}
		else {
			#atribuir valores
 			$descricao=resultadoSQL($consulta, 0, 'descricao');
 			$nome=resultadoSQL($consulta, 0, 'nome');
			$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
			
			# Motrar tabela de busca
			novaTabela2("[Informa��es dos Grupos de Servi�os]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, 'ver', $registro);				
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
					itemLinhaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Nome: </b>";
					htmlFechaColuna();
					itemLinhaForm($nome, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Descri��o: </b>";
					htmlFechaColuna();
					itemLinhaForm($descricao, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Data: </b>";
					htmlFechaColuna();
					itemLinhaForm(converteData($dtCadastro, 'banco', 'form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();

			fechaTabela();				

		} #fecha alteracao
	} #fecha 
} # fecha funcao de Exclus�o



# fun��o de forma para sele��o de tipo de pessoas
function formSelectGruposServicos($servico, $campo, $tipo) {

	if($tipo=='check') {
	
		$consulta=buscaGruposServicos($servico,'id','igual','nome');
		
		if($consulta && contaConsulta($consulta)>0) {
			$id=resultadoSQL($consulta, 0, 'id');
				$nome=resultadoSQL($consulta, 0, 'nome');
				$descricao=resultadoSQL($consulta, 0, 'descricao');
				$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');

			$retorno=$nome;
		}
	
	}
	elseif($tipo=='form') {
	
		$consulta=buscaGruposServicos('','','todos','nome');
		
		if($consulta && contaConsulta($consulta)>0) {
			
			$retorno="<select name=matriz[$campo] onChange=javascript:submit();>";
			
			for($i=0;$i<contaConsulta($consulta);$i++) {
			
				$id=resultadoSQL($consulta, $i, 'id');
				$nome=resultadoSQL($consulta, $i, 'nome');
				$descricao=resultadoSQL($consulta, $i, 'descricao');
				$dtCadastro=resultadoSQL($consulta, $i, 'dtCadastro');
				
				if($servico==$id) $opcSelect='selected';
				else $opcSelect='';
				
				$retorno.="\n<option value=$id $opcSelect>$nome";
			}
			
			$retorno.="</select>";
		}
	
	}
	
	elseif($tipo=='multi') {
	
		$consulta=buscaGruposServicos('','','todos','nome');
		
		if($consulta && contaConsulta($consulta)>0) {
			
			$retorno="<select multiple size=4 name=matriz[$campo][]>";
			
			for($i=0;$i<contaConsulta($consulta);$i++) {
			
				$id=resultadoSQL($consulta, $i, 'id');
				$nome=resultadoSQL($consulta, $i, 'nome');
				$descricao=resultadoSQL($consulta, $i, 'descricao');
				$dtCadastro=resultadoSQL($consulta, $i, 'dtCadastro');
				
				if($servico==$id) 
					$opcSelect='selected';
				else 
					$opcSelect='';
				
				$retorno.="\n<option value=$id $opcSelect>$nome";
			}
			
			$retorno.="</select>";
		}
	
	}
	
	return($retorno);
	
}

?>