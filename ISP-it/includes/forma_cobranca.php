<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 08/07/2003
# Ultima altera��o: 01/12/2003
#    Altera��o No.: 005
#
# Fun��o:
#    Painel - Fun��es para cadastro de formas de cobran�a



# Fun��o de banco de dados - Pessoas
function dbFormaCobranca($matriz, $tipo) {

	global $conn, $tb, $modulo, $sub, $acao;
	
	# Sql de inclus�o
	if($tipo=='incluir') {
	
/*
+----------------+--------------+------+-----+---------+----------------+
| Field          | Type         | Null | Key | Default | Extra          |
+----------------+--------------+------+-----+---------+----------------+
| id             | int(11)      | NO   | PRI | NULL    | auto_increment |
| idBanco        | int(11)      | NO   | MUL | 0       |                |
| descricao      | varchar(200) | YES  |     | NULL    |                |
| titular        | varchar(200) | YES  |     | NULL    |                |
| cnpj           | varchar(20)  | YES  |     | NULL    |                |
| convenio       | varchar(100) | YES  |     | NULL    |                |
| agencia        | varchar(10)  | YES  |     | NULL    |                |
| digAgencia     | char(2)      | YES  |     | NULL    |                |
| conta          | varchar(20)  | YES  |     | NULL    |                |
| digConta       | char(2)      | YES  |     | NULL    |                |
| idTipoCarteira | bigint(20)   | NO   | MUL | 0       |                |
| arquivoremessa | int(11)      | NO   |     | 0       |                |
| codFlash       | char(3)      | YES  |     | NULL    |                |
+----------------+--------------+------+-----+---------+----------------
*/
		$sql = "INSERT INTO $tb[FormaCobranca] VALUES (
				'',
				$matriz[idBanco],
				'$matriz[descricao]',
				'$matriz[titular]',
				'$matriz[cnpj]',
				'$matriz[convenio]',
				'$matriz[agencia]',
				'$matriz[digito_agencia]',
				'$matriz[conta]',
				'$matriz[digito_conta]',
				$matriz[idTipoCarteira],
				'$matriz[arquivoremessa]',
				'$matriz[codFlash]'
				)";				
		
	} #fecha inclusao
	elseif($tipo=='excluir') {
		$sql="DELETE FROM $tb[FormaCobranca] where id=$matriz[id]";
	}
	elseif($tipo=='alterar') {
		$sql="UPDATE $tb[FormaCobranca] 
				SET 
				idBanco='$matriz[idBanco]', 
				descricao='$matriz[descricao]', 
				titular='$matriz[titular]', 
				cnpj='$matriz[cnpj]', 
				convenio='$matriz[convenio]', 
				agencia='$matriz[agencia]', 
				digAgencia='$matriz[digito_agencia]', 
				conta='$matriz[conta]',
				digConta='$matriz[digito_conta]',
				idTipoCarteira='$matriz[idTipoCarteira]',
				arquivoremessa='$matriz[arquivoremessa]',
				codFlash='$matriz[codFlash]'
			WHERE id=$matriz[id]";
	}
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
}




# fun��o de busca 
function buscaFormaCobranca($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[FormaCobranca] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[FormaCobranca] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[FormaCobranca] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[FormaCobranca] WHERE $texto ORDER BY $ordem";
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




function forma_cobranca($modulo, $sub, $acao, $registro, $matriz) {

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
		novaTabela2("[Cadastro de Formas de Cobran�a]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 4);
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
					echo "<br><img src=".$html[imagem][forma_cobrancas]." border=0 align=left><b class=bold>Forma de Cobran�a</b>
					<br><span class=normal10>Cadastro de <b>Formas de Cobran�a</b>.</span>";
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
			itemTabelaNOURL("&nbsp;", 'left', $corFundo, 0, 'normal');
			procurarFormaCobranca($modulo, $sub, $acao, $registro, $matriz);			
		}
		
		# Inclus�o
		if($acao=="adicionar") {
			echo "<br>";
			adicionarFormaCobranca($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($acao=='procurar') {
			echo "<br>";
			procurarFormaCobranca($modulo, $sub, $acao, $registro, $matriz);
		}
		# Listagem
		elseif($acao=="listar") {
			echo "<br>";
			listarFormaCobranca($modulo, $sub, $acao, $registro, $matriz);
		}
		# Excluir
		elseif($acao=="excluir") {
			echo "<br>";
			excluirFormaCobranca($modulo, $sub, $acao, $registro, $matriz);
		}
		# Excluir
		elseif($acao=="ver") {
			echo "<br>";
			verFormaCobranca($modulo, $sub, $acao, $registro, $matriz);
		}
		# Alterar
		elseif($acao=="alterar") {
			echo "<br>";
			alterarFormaCobranca($modulo, $sub, $acao, $registro, $matriz);
		}
	}	
}



# Funcao para cadastro 
function adicionarFormaCobranca($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $tb;

	# Form de inclusao
	if(!$matriz[bntAdicionar]) {
		# Motrar tabela de busca
		novaTabela2("[Adicionar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			#Opcoes Adicionais
			#menuOpcAdicional($modulo, $sub, $acao, $registro);				
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
					echo "<b>Titular: </b><br>
					<span class=normal10>Titular da Conta</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[titular] value='$matriz[titular]' size=60>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b>Descri��o: </b><br>
					<span class=normal10>Descri��o da forma de cobran�a</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[descricao] value='$matriz[descricao]' size=60>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>CNPJ:</b><br>
					<span class=normal10>CNPJ utilizado na cobran�a</span>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[cnpj] value='$matriz[cnpj]' size=18 onBlur=javascript:verificaCNPJ(this.value,5)> <span class=txtaviso>(Ex: : 012.469.253-60 ou 01246925360)</span>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Conv�nio:</b><br>
					<span class=normal10>N�mero do conv�nio com o Banco</span>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[convenio] value='$matriz[convenio]' size=30>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL(
					"<b>Banco:</b><br><span class=normal10>Selecione o Banco</span>", 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=submit name=matriz[bntSelecionar] value=Selecionar class=submit>";
				itemLinhaForm(formSelectBancos($matriz[idBanco], 'idBanco','form', true).$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			if ($matriz[idBanco]) {
				$consulta = buscaBancos("id = ".$matriz[idBanco]." AND numero = 341", "", "custom", "id");
				$condicao = resultadoSQL($consulta, 0, 0) ? "" : "valor <> 'M'";
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Tipo da Carteira: </b><br>
						<span class=normal8>Tipo de cobran�a (112, d�bito automatico, etc)</span>";
					htmlFechaColuna();
					itemLinhaForm(getSelectDados($matriz[idTipoCarteira], '', 'matriz[idTipoCarteira]', 'form', $tb[TipoCarteira], '', '', $condicao), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				
				$tipoCarteira = dadosTipoCarteira($matriz[idTipoCarteira]);
			}	
			
			if ($tipoCarteira[valor] == 'M') {
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>C�digo Flash: </b><br>
						<span class=normal8>C�digo fornecido pelo Banco ITA� na solicita��o da Cobran�a Mensagem:</span>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[codFlash] value='$matriz[codFlash]' size=3 maxlength=3>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			}
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b>Agencia: </b><br>
					<span class=normal10>N�mero da ag�ncia:</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[agencia] value='$matriz[agencia]' size=10> <b class=bold10>Dig.:</b> <input type=text name=matriz[digito_agencia] value='$matriz[digito_agencia]' size=2>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b>Conta: </b><br>
					<span class=normal10>N�mero de Conta na Ag�ncia:</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[conta] value='$matriz[conta]' size=20> <b class=bold10>Dig.:</b> <input type=text name=matriz[digito_conta] value='$matriz[digito_conta]' size=2>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			novaLinhaTabela( $corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1' );
					echo "<b>Padr�o do Arquivo:</b><br>
					<span class=normal10>Tamanho arquivo remessa/retorno:</span>";
				htmlFechaColuna();
				$texto = "<input type=text name=matriz[arquivoremessa] value='$matriz[arquivoremessa]' size=10>";
				itemLinhaForm( $texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			if ($matriz[idBanco]) {
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();
						$texto="<input type=submit name=matriz[bntAdicionar] value=Adicionar class=submit>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			}
		fechaTabela();
	} #fecha form
	elseif($matriz[bntAdicionar]) {
		# Conferir campos
		if($matriz[descricao] && $matriz[conta] && $matriz[agencia]) {
			# Buscar por prioridade
			# Cadastrar em banco de dados
			$matriz[cnpj]=validaCNPJ($matriz[cnpj]);
			$grava=dbFormaCobranca($matriz, 'incluir');
			
			# Verificar inclus�o de registro
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				aviso("Aviso", $msg, $url, 760);
			}
			else {
				# Mensagem de aviso
				$msg="Erro ao gravar Forma de Cobran�a!";
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

} # fecha funcao de inclusao



# Listar 
function listarFormaCobranca($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $moduloApp, $corFundo, $corBorda, $html, $limite;

	# Cabe�alho		
	# Motrar tabela de busca
	novaTabela("[Listar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
		# Sele��o de registros
		$consulta=buscaFormaCobranca($texto, $campo, 'todos','descricao');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# N�o h� registros
			itemTabelaNOURL('N�o h� registros cadastrados', 'left', $corFundo, 5, 'txtaviso');
		}
		else {
		
			# Paginador
			
			paginador($consulta, contaConsulta($consulta), $limite[lista][forma_cobranca], $registro, 'normal', 5, $urlADD);

			# Cabe�alho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Forma de Cobran�a', 'center', '30%', 'tabfundo0');
				itemLinhaTabela('Banco', 'center', '20%', 'tabfundo0');
				itemLinhaTabela('Agencia', 'center', '15%', 'tabfundo0');
				itemLinhaTabela('Conta', 'center', '15%', 'tabfundo0');
				itemLinhaTabela('Op��es', 'center', '20%', 'tabfundo0');
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

			$limite=$i+$limite[lista][forma_cobranca];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$descricao=resultadoSQL($consulta, $i, 'descricao');
				$agencia=resultadoSQL($consulta, $i, 'agencia');
				$digAgencia=resultadoSQL($consulta, $i, 'digAgencia');
				$conta=resultadoSQL($consulta, $i, 'conta');
				$digConta=resultadoSQL($consulta, $i, 'digConta');
				$idBanco=resultadoSQL($consulta, $i, 'idBanco');
				$banco=formSelectBancos($idBanco, '','check');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($descricao, 'left', '30%', 'normal10');
					itemLinhaTabela($banco, 'left', '20%', 'normal10');
					itemLinhaTabela("$agencia Dig: $digAgencia", 'center', '15%', 'normal10');
					itemLinhaTabela("$conta Dig: $digConta", 'center', '15%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '20%', 'normal10');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem

	fechaTabela();
	
} # fecha fun��o de listagem


# Fun��o para procura de servi�o
function procurarFormaCobranca($modulo, $sub, $acao, $registro, $matriz)
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
		$consulta=buscaFormaCobranca("upper(descricao) like '%$matriz[txtProcurar]%' OR agencia like '%$matriz[txtProcurar]%' OR conta like '%$matriz[txtProcurar]%'",$campo, 'custom','descricao');

		echo "<br>";

		novaTabela("[Resultados]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
	
		if(!$consulta || contaConsulta($consulta)==0 ) {
			# N�o h� registros
			itemTabelaNOURL('N�o foram encontrados registros cadastrados', 'left', $corFundo, 5, 'txtaviso');
		}
		elseif($consulta && contaConsulta($consulta)>0 && (!$registro || is_numeric($registro)) ) {	
		
			itemTabelaNOURL('Registros encontrados procurando por ('.$matriz[txtProcurar].'): '.contaConsulta($consulta).' registro(s)', 'left', $corFundo, 5, 'txtaviso');

			# Paginador
			$urlADD="&textoProcurar=".$matriz[txtProcurar];
			paginador($consulta, contaConsulta($consulta), $limite[lista][forma_cobranca], $registro, 'normal', 5, $urlADD);

			# Cabe�alho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Forma de Cobran�a', 'center', '30%', 'tabfundo0');
				itemLinhaTabela('Banco', 'center', '20%', 'tabfundo0');
				itemLinhaTabela('Agencia', 'center', '15%', 'tabfundo0');
				itemLinhaTabela('Conta', 'center', '15%', 'tabfundo0');
				itemLinhaTabela('Op��es', 'center', '20%', 'tabfundo0');
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

			$limite=$i+$limite[lista][forma_cobranca];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$descricao=resultadoSQL($consulta, $i, 'descricao');
				$agencia=resultadoSQL($consulta, $i, 'agencia');
				$digAgencia=resultadoSQL($consulta, $i, 'digAgencia');
				$conta=resultadoSQL($consulta, $i, 'conta');
				$digConta=resultadoSQL($consulta, $i, 'digConta');
				$idBanco=resultadoSQL($consulta, $i, 'idBanco');
				$banco=formSelectBancos($idBanco, '','check');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($descricao, 'left', '30%', 'normal10');
					itemLinhaTabela($banco, 'left', '20%', 'normal10');
					itemLinhaTabela("$agencia Dig: $digAgencia", 'center', '15%', 'normal10');
					itemLinhaTabela("$conta Dig: $digConta", 'center', '15%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '20%', 'normal10');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem
	} # fecha bot�o procurar
} #fecha funcao de  procurar 



# Funcao para altera��o
function alterarFormaCobranca($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $tb;
	
	# ERRO - Registro n�o foi informado
	if(!$registro) {
		# ERRO
	}
	# Form de inclusao
	elseif($registro && !$matriz[bntAlterar]) {
	
		# Buscar Valores
		$consulta=buscaFormaCobranca($registro, 'id', 'igual', 'id');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Mostrar Erro
			$msg="Registro n�o foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso", $msg, $url, 760);
		}
		else {
			
			if ($matriz) {
				#atribuir valores
	 			$descricao=$matriz['descricao'];
	 			$titular=$matriz['titular'];
	 			$cnpj=$matriz['cnpj'];
	 			$convenio=$matriz['convenio'];
				$idBanco=$matriz['idBanco'];
				$agencia=$matriz['agencia'];
				$digAgencia=$matriz['digito_agencia'];
				$conta=$matriz['conta'];
				$digConta=$matriz['digito_conta'];
				$idTipoCarteira=$matriz['idTipoCarteira'];
				$arquivoremessa = $matriz['arquivoremessa'];
				$codFlash=$matriz['codFlash'];
			}
			else {
				#atribuir valores
	 			$descricao=resultadoSQL($consulta, 0, 'descricao');
	 			$titular=resultadoSQL($consulta, 0, 'titular');
	 			$cnpj=resultadoSQL($consulta, 0, 'cnpj');
	 			$convenio=resultadoSQL($consulta, 0, 'convenio');
				$idBanco=resultadoSQL($consulta, 0, 'idBanco');
				$agencia=resultadoSQL($consulta, 0, 'agencia');
				$digAgencia=resultadoSQL($consulta, 0, 'digAgencia');
				$conta=resultadoSQL($consulta, 0, 'conta');
				$digConta=resultadoSQL($consulta, 0, 'digConta');
				$idTipoCarteira=resultadoSQL($consulta, 0, 'idTipoCarteira');
				$arquivoremessa = resultadoSQL( $consulta, 0, 'arquivoremessa' );
				$codFlash=resultadoSQL($consulta, 0, 'codFlash');
			}
						
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
						echo "<b>Descri��o: </b>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[descricao] size=60 value='$descricao'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Titular: </b>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[titular] size=60 value='$titular'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>CNPJ:</b>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
					$texto="<input type=text name=matriz[cnpj] size=18 value='$cnpj' onBlur=javascript:verificaCNPJ(this.value,8)> <span class=txtaviso>(Ex: : 012.469.253-60 ou 01246925360)</span>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Conv�nio:</b>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
					$texto="<input type=text name=matriz[convenio] value='$convenio' size=30>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Banco: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectBancos($idBanco,'idBanco','form', true), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				
				if ($idBanco) {
					$consulta = buscaBancos("id = ".$idBanco." AND numero = 341", "", "custom", "id");
					$condicao = resultadoSQL($consulta, 0, 0) ? "" : "valor <> 'M'";
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b>Tipo da Carteira: </b>";
						htmlFechaColuna();
						itemLinhaForm(getSelectDados($idTipoCarteira, '', 'matriz[idTipoCarteira]', 'form', $tb[TipoCarteira], '', '', $condicao), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					
					$tipoCarteira = dadosTipoCarteira($idTipoCarteira);
				}	
				
				if ($tipoCarteira[valor] == 'M') {
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b>C�digo Flash: </b>";
						htmlFechaColuna();
						$texto="<input type=text name=matriz[codFlash] value='$codFlash' size=3 maxlength=3>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Agencia: </b>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[agencia] size=10 value='$agencia'> <b class=bold10>Dig.:</b> <input type=text name=matriz[digito_agencia] size=2 value='$digAgencia'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Conta: </b>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[conta] size=20 value='$conta'> <b class=bold10>Dig.:</b> <input type=text name=matriz[digito_conta] size=2 value='$digConta'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
//				novaLinhaTabela($corFundo, '100%');
//					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
//						echo "<b>Tipo da Carteira: </b><br>
//						<span class=normal8>Tipo de cobran�a (112, d�bito automatico, etc)</span>";
//					htmlFechaColuna();
//					itemLinhaForm(getSelectDados($idTipoCarteira, '', 'matriz[idTipoCarteira]', 'formnochange', "TipoCarteira"), 'left', 'top', $corFundo, 0, 'tabfundo1');
//				fechaLinhaTabela();
				novaLinhaTabela( $corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1' );
						echo "<b>Padr�o do Arquivo:</b><br>
						<span class=normal10>Tamanho arquivo remessa/retorno:</span>";
					htmlFechaColuna();
					$texto = "<input type=text name=matriz[arquivoremessa] size=10 value = '$arquivoremessa'>";
					itemLinhaForm( $texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
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
		if($matriz[descricao] && $matriz[conta] && $matriz[agencia]) {
			# continuar
			# Cadastrar em banco de dados
			$matriz[cnpj]=validaCNPJ($matriz[cnpj]);
			$grava=dbFormaCobranca($matriz, 'alterar');
			
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



# Funcao para altera��o
function excluirFormaCobranca($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# ERRO - Registro n�o foi informado
	if(!$registro) {
		# ERRO
	}
	# Form de inclusao
	elseif($registro && !$matriz[bntExcluir]) {
	
		# Buscar Valores
		$consulta=buscaFormaCobranca($registro, 'id', 'igual', 'id');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Mostrar Erro
			$msg="Registro n�o foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso", $msg, $url, 760);
		}
		else {
			#atribuir valores
 			$descricao=resultadoSQL($consulta, 0, 'descricao');
			$idBanco=resultadoSQL($consulta, 0, 'idBanco');
			$agencia=resultadoSQL($consulta, 0, 'agencia');
			$digAgencia=resultadoSQL($consulta, 0, 'digAgencia');
			$conta=resultadoSQL($consulta, 0, 'conta');
			$digConta=resultadoSQL($consulta, 0, 'digConta');
			
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
						echo "<b>Descri��o: </b>";
					htmlFechaColuna();
					itemLinhaForm($descricao, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Banco: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectBancos($idBanco,'idBanco','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Agencia: </b>";
					htmlFechaColuna();
					itemLinhaForm("$agencia Dig.: $digAgencia", 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Conta: </b>";
					htmlFechaColuna();
					itemLinhaForm("$conta Dig.: $digConta", 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();
					$texto="<input type=submit name=matriz[bntExcluir] value=Excluir class=submit>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();				

		} #fecha alteracao
	} #fecha form - !$bntAlterar
	
	# Altera��o - bntAlterar pressionado
	elseif($matriz[bntExcluir]) {
		# Conferir campos
		$grava=dbFormaCobranca($matriz, 'excluir');
			
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
			$msg="Erro ao excluir forma de cobran�a";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
		}
	} #fecha bntAlterar
	
} # fecha funcao de altera��o





# fun��o de forma para sele��o
function formSelectFormaCobranca($forma_cobranca, $campo, $tipo) {

	if($tipo=='check') {
	
		$consulta=buscaFormaCobranca($forma_cobranca,'id','igual','descricao');
		
		if($consulta && contaConsulta($consulta)>0) {
			$id=resultadoSQL($consulta, 0, 'id');
			$descricao=resultadoSQL($consulta, 0, 'descricao');
			
			$retorno="$descricao";
			
		}
	
	}
	elseif($tipo=='form' || $tipo=='formOnChange') {
	
		$consulta=buscaFormaCobranca('','','todos','descricao');
		
		if($consulta && contaConsulta($consulta)>0) {
			
			if ($tipo!='formOnChange') {
				$retorno="<select name=matriz[$campo]>";
			}
			else {
				$retorno="<select name=matriz[$campo] onChange=\"javascript:submit()\">";
			}
			
			for($i=0;$i<contaConsulta($consulta);$i++) {
			
				$id=resultadoSQL($consulta, $i, 'id');
				$descricao=resultadoSQL($consulta, $i, 'descricao');
				
				if($forma_cobranca==$id) $opcSelect='selected';
				else $opcSelect='';
				
				$retorno.="\n<option value=$id $opcSelect>$descricao";
			}
			
			$retorno.="</select>";
		}
	
	}
	
	return($retorno);
	
}



# Fun�ao para busca de informa��es do vencimento
function dadosFormaCobranca($idFormaCobranca) {

	$consulta=buscaFormaCobranca($idFormaCobranca, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		# dados do vencimento
		$retorno[id]=resultadoSQL($consulta, 0, 'id');
		$retorno[descricao]=resultadoSQL($consulta, 0, 'descricao');
		$retorno[idBanco]=resultadoSQL($consulta, 0, 'idBanco');
		$retorno[titular]=resultadoSQL($consulta, 0, 'titular');
		$retorno[cnpj]=resultadoSQL($consulta, 0, 'cnpj');
		$retorno[convenio]=resultadoSQL($consulta, 0, 'convenio');
		$retorno[agencia]=resultadoSQL($consulta, 0, 'agencia');
		$retorno[digAgencia]=resultadoSQL($consulta, 0, 'digAgencia');
		$retorno[conta]=resultadoSQL($consulta, 0, 'conta');
		$retorno[digConta]=resultadoSQL($consulta, 0, 'digConta');
		$retorno[idTipoCarteira]=resultadoSQL($consulta, 0, 'idTipoCarteira');
		$retorno[arquivoremessa] = resultadoSQL( $consulta, 0, 'arquivoremessa');
		$retorno[codFlash] = resultadoSQL($consulta, 0 , 'codFlash');
	}
	
	return($retorno);
}

/**
 * Retorna uma array de objetos com a Forma de Cobran�a do $tipo especificado, onde:
 * 'S' => duplicata Simples (Boletos Impressos)
 * 'R' => duplicata Registrada (Arquivo Remessa)
 * 'D' => D�bito autom�tico (Arquivo Remessa)
 *  
 * @param unknown_type $tipo
 * @return unknown
 */
function getFormasCobrancasTipos( $tipo ) {
	
	global $tb, $conn;
	
	$sql = "
		SELECT 
			FC.id, 
			FC.descricao 
		FROM 
			{$tb['FormaCobranca']} FC INNER JOIN {$tb['TipoCarteira']} TC 
			ON (FC.idTipoCarteira=TC.id) 
		WHERE 
			TC.valor='$tipo'";
	$consulta = consultaSQL( $sql, $conn);

	return getArrayObjetos( $consulta );
}

?>