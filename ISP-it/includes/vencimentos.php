<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 10/06/2003
# Ultima altera��o: 01/12/2003
#    Altera��o No.: 010
#
# Fun��o:
#    Painel - Fun��es para cadastro de tipos de documentos

# Fun��o para cadastro
function vencimentos($modulo, $sub, $acao, $registro, $matriz)
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
		novaTabela2("[Cadastro de Vencimentos]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 4);
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
					echo "<br><img src=".$html[imagem][vencimentos]." border=0 align=left><b class=bold>Vencimentos</b>
					<br><span class=normal10>Cadastro de <b>vencimentos</b>, utilizados para o gera��o de cobran�as,
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
		
		# Inclus�o
		if($acao=="adicionar") {
			echo "<br>";
			adicionarVencimentos($modulo, $sub, $acao, $registro, $matriz);
		}
		
		# Altera��o
		elseif($acao=="alterar") {
			echo "<br>";
			alterarVencimentos($modulo, $sub, $acao, $registro, $matriz);
		}
		
		# Exclus�o
		elseif($acao=="excluir") {
			echo "<br>";
			excluirVencimentos($modulo, $sub, $acao, $registro, $matriz);
		}
	
		# Busca
		elseif($acao=="procurar") {
			echo "<br>";
			procurarVencimentos($modulo, $sub, $acao, $registro, $matriz);
		} #fecha tabela de busca
		
		# Listar
		elseif($acao=="listar" || !$acao) {
			echo "<br>";
			listarVencimentos($modulo, $sub, $acao, $registro, $matriz);
		} #fecha listagem de servicos
	}


} #fecha menu principal 


# fun��o de busca 
function buscaVencimentos($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[Vencimentos] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[Vencimentos] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[Vencimentos] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[Vencimentos] WHERE $texto ORDER BY $ordem";
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
function adicionarVencimentos($modulo, $sub, $acao, $registro, $matriz)
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
					echo "<b class=bold>Descri��o: </b><br>
					<span class=normal10>Descri��o do documento</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[descricao] size=60>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold>Dia de Vencimento: </b><br>
					<span class=normal10>Dia indicado para vencimento das faturas</span>";
				htmlFechaColuna();
				itemLinhaForm(formSelectNumero('',1,31,'dia','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold>Dia de Faturamento: </b><br>
					<span class=normal10>Dia indicado para Faturamento</span>";
				htmlFechaColuna();
				itemLinhaForm(formSelectNumero('',1,31,'diaf','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
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
		if($matriz[descricao] && is_numeric($matriz[dia]) && is_numeric($matriz[diaf])) {
			# Buscar por prioridade
			if(contaConsulta(buscaVencimentos($matriz[dia], 'diaVencimento', 'igual','diaVencimento'))>0){
				# Erro - campo inv�lido
				# Mensagem de aviso
				$msg="Vencimento j� cadastrado!";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				aviso("Aviso: Dados incorretos", $msg, $url, 760);
			}
			# continuar - campos OK
			else {
				# Cadastrar em banco de dados
				$grava=dbVencimento($matriz, 'incluir');
				
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
			$msg="Falta de par�metros necess�rios ou Informa��es incorretas!<br>";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
		}
	}

} # fecha funcao de inclusao




# Fun��o para grava��o em banco de dados
function dbVencimento($matriz, $tipo)
{
	global $conn, $tb, $modulo, $sub, $acao;
	
	# Sql de inclus�o
	if($tipo=='incluir') {
		$sql="INSERT INTO $tb[Vencimentos] VALUES (0,
		'$matriz[descricao]',
		'$matriz[dia]',
		'$matriz[diaf]')";
	} #fecha inclusao
	
	elseif($tipo=='excluir') {
		# Verificar se a prioridade existe
		$tmpBusca=buscaVencimentos($matriz[id], 'id', 'igual', 'id');
		
		# Registro j� existe
		if(!$tmpBusca|| contaConsulta($tmpBusca)==0) {
			# Mensagem de aviso
			$msg="Registro n�o existe no banco de dados";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Erro ao alterar registro", $msg, $url, 760);
		}
		else {
			$sql="DELETE FROM $tb[Vencimentos] WHERE id=$matriz[id]";
		}
	}
	
	# Alterar
	elseif($tipo=='alterar') {
		# Verificar se prioridade existe
		$sql="UPDATE $tb[Vencimentos] SET descricao='$matriz[descricao]', diaVencimento='$matriz[dia]', diaFaturamento='$matriz[diaf]'  WHERE id=$matriz[id]";
	}
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
	
} # fecha fun��o de grava��o em banco de dados



# Listar 
function listarVencimentos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $moduloApp, $corFundo, $corBorda, $html, $limite;

	# Cabe�alho		
	# Motrar tabela de busca
	novaTabela("[Listar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 4);
		# Sele��o de registros
		$consulta=buscaVencimentos($texto, $campo, 'todos','descricao');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# N�o h� registros
			itemTabelaNOURL('N�o h� registros cadastrados', 'left', $corFundo, 3, 'txtaviso');
		}
		else {
		
			# Paginador
			paginador($consulta, contaConsulta($consulta), $limite[lista][vencimentos], $registro, 'normal10', 4, $urlADD);
		
			# Cabe�alho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Vencimento', 'center', '50%', 'tabfundo0');
				itemLinhaTabela('Dia Venc.', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Dia Fat.', 'center', '10%', 'tabfundo0');
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

			$limite=$i+$limite[lista][tipo_documentos];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$descricao=resultadoSQL($consulta, $i, 'descricao');
				$dia=resultadoSQL($consulta, $i, 'diaVencimento');
				$diaf=resultadoSQL($consulta, $i, 'diaFaturamento');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($descricao, 'left', '50%', 'normal10');
					itemLinhaTabela($dia, 'center', '10%', 'normal10');
					itemLinhaTabela($diaf, 'center', '10%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '30%', 'normal10');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem

	fechaTabela();
	
} # fecha fun��o de listagem


# Fun��o para procura de servi�o
function procurarVencimentos($modulo, $sub, $acao, $registro, $matriz)
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
		$consulta=buscaVencimentos("upper(descricao) like '%$matriz[txtProcurar]%' OR diaVencimento='$matriz[txtProcurar]' OR diaFaturamento='$matriz[txtProcurar]'",$campo, 'custom','descricao');

		echo "<br>";

		novaTabela("[Resultados]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 4);
	
		if(!$consulta || contaConsulta($consulta)==0 ) {
			# N�o h� registros
			itemTabelaNOURL('N�o foram encontrados registros cadastrados', 'left', $corFundo, 4, 'txtaviso');
		}
		elseif($consulta && contaConsulta($consulta)>0 && (!$registro || is_numeric($registro)) ) {	
		
			itemTabelaNOURL('Registros encontrados procurando por ('.$matriz[txtProcurar].'): '.contaConsulta($consulta).' registro(s)', 'left', $corFundo, 4, 'txtaviso');

			# Paginador
			$urlADD="&textoProcurar=".$matriz[txtProcurar];
			paginador($consulta, contaConsulta($consulta), $limite[lista][vencimentos], $registro, 'normal', 4, $urlADD);

			# Cabe�alho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Documento', 'center', '50%', 'tabfundo0');
				itemLinhaTabela('Dia Venc.', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Dia Fat.', 'center', '10%', 'tabfundo0');
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

			$limite=$i+$limite[lista][vencimentos];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$descricao=resultadoSQL($consulta, $i, 'descricao');
				$dia=resultadoSQL($consulta, $i, 'diaVencimento');
				$diaf=resultadoSQL($consulta, $i, 'diaFaturamento');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($descricao, 'left', '50%', 'normal10');
					itemLinhaTabela($dia, 'center', '10%', 'normal10');
					itemLinhaTabela($diaf, 'center', '10%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '30%', 'normal10');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem
	} # fecha bot�o procurar
} #fecha funcao de  procurar 



# Funcao para altera��o
function alterarVencimentos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# ERRO - Registro n�o foi informado
	if(!$registro) {
		# ERRO
	}
	# Form de inclusao
	elseif($registro && !$matriz[bntAlterar]) {
	
		# Buscar Valores
		$consulta=buscaVencimentos($registro, 'id', 'igual', 'id');
		if(!$consulta || contaConsulta($consulta)==0) {
			# Mostrar Erro
			$msg="Registro n�o foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso", $msg, $url, 760);
		}
		else {
			#atribuir valores
 			$descricao=resultadoSQL($consulta, 0, 'descricao');
			$dia=resultadoSQL($consulta, 0, 'diaVencimento');
			$diaf=resultadoSQL($consulta, 0, 'diaFaturamento');
			
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
						echo "<b class=bold>Descri��o: </b><br>
						<span class=normal10>Descri��o do Documento</span>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[descricao] size=60 value='$descricao'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Dia de Vencimento: </b><br>
						<span class=normal10>Dia indicado para vencimento das faturas</span>";
					htmlFechaColuna();
					itemLinhaForm(formSelectNumero($dia,1,31,'dia','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Dia de Faturamento: </b><br>
						<span class=normal10>Dia indicado para Faturamento</span>";
					htmlFechaColuna();
					itemLinhaForm(formSelectNumero($diaf,1,31,'diaf','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
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
		if($matriz[descricao] && is_numeric($matriz[dia]) && is_numeric($matriz[diaf])) {
			# continuar
			# Cadastrar em banco de dados
			$grava=dbVencimento($matriz, 'alterar');
			
			# Verificar inclus�o de registro
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
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


# Exclus�o de servicos
function excluirVencimentos($modulo, $sub, $acao, $registro, $matriz)
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
		$consulta=buscaVencimentos($registro, 'id', 'igual', 'id');
		
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
			$dia=resultadoSQL($consulta, 0, 'diaVencimento');
			$diaf=resultadoSQL($consulta, 0, 'diaFaturamento');
			
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
						echo "<b class=bold>Descri��o: </b>";
					htmlFechaColuna();
					itemLinhaForm($descricao, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Dia de Vencimento: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectNumero($dia,1,28,'dia','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Dia de Faturamento: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectNumero($diaf,1,28,'diaf','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
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
		# Para excluir um vencimento, deve ser validado se n�o h� nenhum cliente com plano vinculado ao
		# Vencimento - por Felipe Assis - 25/03/2008
		$temCliente = getClienteVencimento($registro);
		if($temCliente == true){
			# Mensagem de aviso
				$msg="Este vencimento n�o pode ser exclu�do porque h� Clientes vinculados a ele!";
				$url="?modulo=$modulo&sub=$sub&acao=listar";
				aviso("Aviso", $msg, $url, 760);
		}
		else{
			# Cadastrar em banco de dados
			$grava=dbVencimento($matriz, 'excluir');
					
			# Verificar inclus�o de registro
			if($grava) {
				# Mensagem de aviso
				$msg="Registro exclu�do com Sucesso!";
				$url="?modulo=$modulo&sub=$sub&acao=listar";
				aviso("Aviso", $msg, $url, 760);
			}
		}
		
	} #fecha bntExcluir
	
} #fecha exclusao 



# fun��o de forma para sele��o
function formSelectVencimento($vencimento, $campo, $tipo) {

	if($tipo=='check') {
	
		$consulta=buscaVencimentos($vencimento,'id','igual','descricao');
		
		if($consulta && contaConsulta($consulta)>0) {
			$id=resultadoSQL($consulta, 0, 'id');
			$descricao=resultadoSQL($consulta, 0, 'descricao');
			$diaVencimento=resultadoSQL($consulta, 0, 'diaVencimento');
			$diaFaturamento=resultadoSQL($consulta, 0, 'diaFaturamento');
			
			#$retorno="$descricao - Vencimento: $diaVencimento - Faturamento: $diaFaturamento";
			$retorno="$descricao [ $diaVencimento ]";
			
		}
	
	}
	elseif($tipo=='form') {
	
		$consulta=buscaVencimentos('','','todos','descricao');
		
		if($consulta && contaConsulta($consulta)>0) {
			
			$retorno="<select name=matriz[$campo]>";
			
			for($i=0;$i<contaConsulta($consulta);$i++) {
			
				$id=resultadoSQL($consulta, $i, 'id');
				$descricao=resultadoSQL($consulta, $i, 'descricao');
				$diaVencimento=resultadoSQL($consulta, $i, 'diaVencimento');
				$diaFaturamento=resultadoSQL($consulta, $i, 'diaFaturamento');
				
				if($vencimento==$id) $opcSelect='selected';
				else $opcSelect='';
				
				$retorno.="\n<option value=$id $opcSelect>$descricao - Vencimento: $diaVencimento - Faturamento: $diaFaturamento";
			}
			
			$retorno.="</select>";
		}
	
	}
	
	return($retorno);
}



# Fun�ao para busca de informa��es do vencimento
function dadosVencimento($idVencimento) {

	$consulta=buscaVencimentos($idVencimento, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		# dados do vencimento
		$retorno[diaVencimento]=resultadoSQL($consulta, 0, 'diaVencimento');
		$retorno[diaFaturamento]=resultadoSQL($consulta, 0, 'diaFaturamento');
		$retorno[descricao]=resultadoSQL($consulta, 0, 'descricao');
		$retorno[id]=resultadoSQL($consulta, 0, 'id');
	}
	
	return($retorno);
}

/**
 * @author Felipe dos S. Assis
 * @desc Fun��o para buscar planos de clientes vinculados ao vencimento
 * @version 1.0.0
 * @param int $id
 * @return bool
 * @since 25/03/2008
 */
function getClienteVencimento($idVencimento){
	
	global $sessLogin, $sessCadastro, $conn, $tb, $modulo, $sub, $acao;
	/*
	SELECT Pessoas.id as idPessoa, Pessoas.nome as nomePessoa 
	FROM Vencimentos INNER JOIN PlanosPessoas 
	ON (Vencimentos.id = PlanosPessoas.idVencimento) 
	INNER JOIN PessoasTipos 
	ON (PlanosPessoas.idPessoaTipo = PessoasTipos.id) 
	INNER JOIN Pessoas 
	ON (PessoasTipos.idPessoa = Pessoas.id) 
	WHERE Vencimentos.id = 8;
	*/
	$sql = "SELECT $tb[Pessoas].id as idPessoa
			FROM $tb[Vencimentos] INNER JOIN $tb[PlanosPessoas] 
			ON ($tb[Vencimentos].id = $tb[PlanosPessoas].idVencimento) 
			INNER JOIN $tb[PessoasTipos] 
			ON ($tb[PlanosPessoas].idPessoaTipo = $tb[PessoasTipos].id) 
			INNER JOIN $tb[Pessoas] 
			ON ($tb[PessoasTipos].idPessoa = $tb[Pessoas].id) 
			WHERE $tb[Vencimentos].id = ".$idVencimento;
		
	$consulta = consultaSQL($sql, $conn);
	
	if(contaConsulta($consulta) > 0){
		return true;
	}
	else{
		return false;
	}
}
?>
