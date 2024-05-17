<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 05/08/2003
# Ultima altera��o: 15/08/2003
#    Altera��o No.: 006
#
# Fun��o:
#    Painel - Fun��es para para cadastro de condom�nios


# Fun��o para cadastro
function condominios($modulo, $sub, $acao, $registro, $matriz)
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
		novaTabela2("[Condom�nios]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 4);
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
					echo "<br><img src=".$html[imagem][condominios]." border=0 align=left><b class=bold>Condom�nios</b>
					<br><span class=normal10>Cadastro de <b>condom�nios</b>, utilizados para cadastro de endere�os de
					clientes, e Planos de Servi�os.</span>";
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
			itemTabelaNOURL("&nbsp;", 'left', $corFundo, 0, 'normal');
			adicionarParametros($modulo, $sub, $acao, $registro, $matriz);
		}
		
		# Altera��o
		elseif($acao=="alterar") {
			itemTabelaNOURL("&nbsp;", 'left', $corFundo, 0, 'normal');
			alterarParametros($modulo, $sub, $acao, $registro, $matriz);
		}
		
		# Exclus�o
		elseif($acao=="excluir") {
			itemTabelaNOURL("&nbsp;", 'left', $corFundo, 0, 'normal');
			excluirParametros($modulo, $sub, $acao, $registro, $matriz);
		}
	
		# Busca
		elseif($acao=="procurar") {
			itemTabelaNOURL("&nbsp;", 'left', $corFundo, 0, 'normal');
			procurarParametros($modulo, $sub, $acao, $registro, $matriz);
		} #fecha tabela de busca
		
		# Listar
		elseif($acao=="listar" || !$acao) {
			itemTabelaNOURL("&nbsp;", 'left', $corFundo, 0, 'normal');
			listarParametros($modulo, $sub, $acao, $registro, $matriz);
		} #fecha listagem de servicos
	}


} #fecha menu principal 


# fun��o de busca 
function buscaCondominios($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[Condominios] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[Condominios] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[Condominios] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[Condominios] WHERE $texto ORDER BY $ordem";
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

?>
