<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 10/06/2003
# Ultima altera��o: 08/07/2003
#    Altera��o No.: 004
#
# Fun��o:
#    Painel - Fun��es para cadastro


# fun��o de busca 
function buscaParametrosModulos($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[ParametrosModulos] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[ParametrosModulos] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[ParametrosModulos] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[ParametrosModulos] WHERE $texto ORDER BY $ordem";
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



# Fun��o para retornar o ID do modulo para parametro
function buscaIDParametrosModulos($texto, $campo, $tipo, $retorno) {

	$consulta=buscaParametrosModulos($texto, $campo, $tipo, '');
	
	if($consulta && contaConsulta($consulta)>0) {
		# retornar
		$retorno=resultadoSQL($consulta, 0, $retorno);
	}
	
	return($retorno);

}


# Fun��o para listagem 
function listarParametrosModulos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite;

	# Sele��o de registros
	$consulta=buscaModulos($registro, 'id', 'igual', 'id');
	
	if(!$consulta || contaConsulta($consulta)==0) {
		# Servidor n�o encontrado
		itemTabelaNOURL('N�o h� registros cadastrados!', 'left', $corFundo, 3, 'txtaviso');
	}
	else {
		# Mostrar Informa��es sobre Servidor
		verModulo($registro);
		
		$consulta=buscaParametrosModulos($registro, 'idModulo','igual','idModulo');
		
		# Cabe�alho		
		# Motrar tabela de busca
		novaTabela("[Par�metros do M�dulo]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
		$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=$acao"."adicionar&registro=$registro>Adicionar</a>",'incluir');
		itemTabelaNOURL($opcoes, 'right', $corFundo, 5, 'tabfundo1');
		
		
		# Caso n�o hajam servicos para o servidor
		if(!$consulta || contaConsulta($consulta)==0) {
			# N�o h� registros
			itemTabelaNOURL('N�o h� par�metros configurados para este m�dulo', 'left', $corFundo, 5, 'txtaviso');
		}
		else {

			# Cabe�alho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Descri��o', 'center', '40%', 'tabfundo0');
				itemLinhaTabela('Tipo', 'center', '20%', 'tabfundo0');
				itemLinhaTabela('Unidade', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Par�metro', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Op��es', 'center', '20%', 'tabfundo0');
			fechaLinhaTabela();

			$i=0;
			
			while($i < contaConsulta($consulta)) {
				# Mostrar registro
				$idParametro=resultadoSQL($consulta, $i, 'idParametro');
				
				# Buscar parametro
				$consultaParametro=buscaParametros($idParametro, 'id','igual','id');

				$descricao=resultadoSQL($consultaParametro, 0, 'descricao');
				$tipo=resultadoSQL($consultaParametro, 0, 'tipo');
				$unidade=resultadoSQL($consultaParametro, 0, 'idUnidade');
				$parametro=resultadoSQL($consultaParametro, 0, 'parametro');
				
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=$acao".excluir."&registro=$registro:$idParametro>Excluir</a>",'excluir');

				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($descricao, 'center', '40%', 'normal10');
					itemLinhaTabela(formSelectTipoParametro($tipo, '', 'check'), 'center', '20%', 'normal10');
					itemLinhaTabela(formSelectUnidades($unidade, '','check'), 'center', '10%', 'normal10');
					itemLinhaTabela($parametro, 'center', '10%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '20%', 'normal10');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
			
			fechaTabela();
		} #fecha servicos encontrados
	} #fecha listagem

	
}#fecha fun��o de listagem



# Funcao para cadastro de servicos
function adicionarParametrosModulos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# Form de inclusao
	if(!$matriz[bntAdicionar]) {

		# Sele��o de registros
		$consulta=buscaModulos($registro, 'id', 'igual', 'id');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Servidor n�o encontrado
			itemTabelaNOURL('M�dulo n�o encontrado!', 'left', $corFundo, 3, 'txtaviso');
		}
		else {
			# Mostrar Informa��es sobre Servidor
			verModulo($registro);
	
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
					<input type=hidden name=matriz[modulo] value=$registro>
					<input type=hidden name=acao value=$acao>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Par�metro: </b><br>
						<span class=normal10>Selecione o par�metro do m�dulo</span>";
					htmlFechaColuna();
					itemLinhaForm(formSelectParametros($registro, $matriz[parametro], 'parametro'), 'left', 'top', $corFundo, 0, 'tabfundo1');
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
	} #fecha form
	elseif($matriz[bntAdicionar]) {
		# Conferir campos
		if($matriz[modulo] && $matriz[parametro]) {
			# Cadastrar em banco de dados
			$grava=dbParametrosModulo($matriz, 'incluir');
				
			# Verificar inclus�o de registro
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$registro";
				aviso("Aviso", $msg, $url, 760);
			}
		}
		
		# falta de parametros
		else {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Falta de par�metros necess�rios. Informe os campos obrigat�rios e tente novamente";
			$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$registro";
			aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
		}
	}
} # fecha funcao de inclusao de servicos


# Funcao para exclus�o de servicos
function excluirParametrosModulos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;

	$matTMP=explode(":",$registro);
	$matriz[modulo]=$matTMP[0];
	$matriz[parametro]=$matTMP[1];
	
	$consultaParametro=buscaParametros($matriz[parametro], 'id', 'igual', 'id');
	$descricao=resultadoSQL($consultaParametro, 0, 'descricao');
	
	$consultaParametrosModulo=buscaParametrosModulos('idModulo='.$matriz[modulo].' AND idParametro='.$matriz[parametro], $campo, 'custom', 'idModulo');
	
	# Form de exclus�o
	if(!$matriz[bntRemover]) {

		# Sele��o de registros
		$consulta=buscaModulos($matriz[modulo], 'id', 'igual', 'id');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Servidor n�o encontrado
			itemTabelaNOURL('M�dulo n�o encontrado!', 'left', $corFundo, 3, 'txtaviso');
		}
		else {
			# Mostrar Informa��es sobre Servidor
			verModulo($matriz[modulo]);
	
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
					<input type=hidden name=matriz[modulo] value=$matriz[modulo]>
					<input type=hidden name=matriz[parametro] value=$matriz[parametro]>
					<input type=hidden name=acao value=$acao>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Par�metro: </b>";
					htmlFechaColuna();
					itemLinhaForm($descricao, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();
					$texto="<input type=submit name=matriz[bntRemover] value=Remover class=submit2>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		} # fecha servidor informado para cadastro
	} #fecha form
	elseif($matriz[bntRemover]) {
		# Conferir campos
		if($matriz[modulo] && $matriz[parametro]) {
		
			# Cadastrar em banco de dados
			$grava=dbParametrosModulo($matriz, 'excluir');
				
			# Verificar inclus�o de registro
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Exclu�do com Sucesso!";
				$url="?modulo=$modulo&sub=$sub&acao=parametros&registro=$matriz[modulo]";
				aviso("Aviso", $msg, $url, 760);
			}
		}
		
		# falta de parametros
		else {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Falta de par�metros necess�rios. Informe os campos obrigat�rios e tente novamente";
			$url="?modulo=$modulo&sub=$sub&acao=grupos";
			aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
		}
	}
} # fecha funcao de exclus�o




# Fun��o para grava��o em banco de dados
function dbParametrosModulo($matriz, $tipo)
{
	global $conn, $tb, $modulo, $sub, $acao;
	
	# Sql de inclus�o
	if($tipo=='incluir') {
		# Verificar se servi�o existe
		$tmpConsulta=buscaParametrosModulos("idModulo='$matriz[modulo]' AND idParametro='$matriz[parametro]'", $campo, 'custom', 'idModulo');
		
		# Registro j� existe
		if($tmpConsulta && contaConsulta($tmpConsulta)>0) {
			# Mensagem de aviso
			$msg="Registro j� existe no banco de dados";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Erro ao incluir registro", $msg, $url, 760);
		}
		else {
			$sql="INSERT INTO $tb[ParametrosModulos] VALUES ('$matriz[modulo]', '$matriz[parametro]')";
		}
	} #fecha inclusao
	
	elseif($tipo=='excluir') {
		# Verificar se servi�o existe
		$tmpConsulta=buscaParametrosModulos("idModulo='$matriz[modulo]' AND idParametro='$matriz[parametro]'", $campo, 'custom', 'idModulo');
		
		# Registro j� existe
		if(!$tmpConsulta|| contaConsulta($tmpConsulta)==0) {
			# Mensagem de aviso
			$msg="Registro n�o existe no banco de dados";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Erro ao alterar registro", $msg, $url, 760);
		}
		else {
			$sql="DELETE FROM $tb[ParametrosModulos] WHERE idModulo=$matriz[modulo] AND idParametro=$matriz[parametro]";
		}
	}
	
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
	
} # fecha fun��o de grava��o em banco de dados



# Checar o modulo do parametro
function checkParametroModulo($idParametro, $modulo) {

	global $conn, $tb;
	
	$sql="
		SELECT 
			$tb[Modulos].modulo, 
			$tb[Parametros].parametro 
		FROM 
			$tb[Modulos], 
			$tb[Parametros], 
			$tb[ParametrosModulos] 
		WHERE 
			$tb[Modulos].id=$tb[ParametrosModulos].idModulo 
			AND $tb[ParametrosModulos].idParametro = $tb[Parametros].id 
			AND $tb[Parametros].id=$idParametro
			ANd $tb[Modulos].modulo='$modulo'
	";
	
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
		return(1);
	}
	else {
		return(0);
	}

}

?>
