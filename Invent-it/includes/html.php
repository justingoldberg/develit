<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 14/10/2002
# Ultima altera��o: 23/04/2003
#    Altera��o No.: 023
#
# Fun��o:
#    Fun��es para formata��o de pagina e saidas HTML

# Fun��o para cria��o de tabelas
function novaTabela($titulo, $alinhamento, $tamanho, $borda, $spcCel, $spcCarac, $corFundo, $corBorda, $colunas)
{

	
	if($colunas>0) {
		$colspan="colspan=".$colunas; 
	}

	# Criar nova tabela
	echo "<!-- Cria nova tabela [$titulo] -->\n
	<table width=$tamanho border=$borda cellpadding=0 cellspacing=0 bgcolor=$corBorda>\n
	<tr><td>\n
	<table width=$tamanho border=$borda cellpadding=$spcCel cellspacing=$spcCarac>\n
	<th $colspan width=$tamanho class=tabtitulo align=$alinhamento background=$corFundo>$titulo</th>\n";
} # Fecha fun��o tabelanova


# Fun��o para cria��o de tabelas
function novaTabelaSH($alinhamento, $tamanho, $borda, $spcCel, $spcCarac, $corFundo, $corBorda, $colunas)
{

	
	if($colunas>0) {
		$colspan="colspan=".$colunas; 
	}

	# Criar nova tabela
	echo "<table width=$tamanho border=$borda cellpadding=0 cellspacing=0 bgcolor=$corBorda>\n
	<tr><td>\n
	<table width=$tamanho border=$borda cellpadding=$spcCel cellspacing=$spcCarac>\n";
} # Fecha fun��o tabelanova


# Fun��o para cria��o de tabelas
function novaTabela2($titulo, $alinhamento, $tamanho, $borda, $spcCel, $spcCarac, $corFundo, $corBorda, $colunas)
{
	if($colunas>0) {
		$colspan="colspan=".$colunas; 
	}
	
	# Criar nova tabela
	echo "<!-- Cria nova tabela [$titulo] -->\n
	<table width=$tamanho border=$borda cellpadding=0 cellspacing=$spcCarac bgcolor=$corBorda>\n
	<tr><td>\n
	<table width=$tamanho border=0 cellpadding=$spcCel cellspacing=0>\n
	<th $colspan width=$tamanho class=tabtitulo align=$alinhamento background=$corFundo>$titulo</th>\n";
} # Fecha fun��o tabelanova


# Fun��o para cria��o de tabelas
function novaTabela2SH($alinhamento, $tamanho, $borda, $spcCel, $spcCarac, $corFundo, $corBorda, $colunas)
{
	if($colunas>0) {
		$colspan="colspan=".$colunas; 
	}
	
	# Criar nova tabela
	echo "<!-- Cria nova tabela [$titulo] -->\n
	<table width=$tamanho border=$borda cellpadding=0 cellspacing=$spcCarac bgcolor=$corBorda>\n
	<tr><td>\n
	<table width=$tamanho border=0 cellpadding=$spcCel cellspacing=0>\n";
} # Fecha fun��o tabelanova



# Fun��o para cria��o de tabelas - Sem Header
function htmlAbreTabelaSH($alinhamento, $tamanho, $borda, $spcCel, $spcCarac, $corFundo, $corBorda, $colunas)
{
	# Criar nova tabela
	echo "
	<table width=$tamanho border=$borda cellpadding=0 cellspacing=0 bgcolor=$corBorda>\n
	<tr><td>\n
	<table width=$tamanho border=$borda cellpadding=$spcCel cellspacing=$spcCarac>\n";
} # Fecha fun��o tabelanova


# Fun��o para cri��o de nova linha
function htmlAbreLinha($corFundo)
{
	echo "<tr bgcolor=$corFundo>\n";
} #fecha fun��o de abertura de nova linha

# Fun��o para fechar linha aberta
function htmlFechaLinha()
{
	echo "</tr>\n";
} # fecha fun��o de fechamento de linha

# Fun��o para abrir nova coluna
function htmlAbreColuna($tamanho, $alinhamento, $corFundo, $colunas, $classeCSS)
{
	# Adicionar item
	echo "<td width=$tamanho colspan=$colunas align=$alinhamento class=$classeCSS>\n";
} # fecha fun��o abrir nova coluna

# Fun��o para abrir nova coluna
function htmlAbreColunaForm($tamanho, $alinhamento, $alinhamentov, $corFundo, $colunas, $classeCSS)
{
	# Adicionar item
	echo "<td width=$tamanho colspan=$colunas align=$alinhamento valign=$alinhamentov class=$classeCSS>\n";
} # fecha fun��o abrir nova coluna

# Fun��o para fechar nova coluna
function htmlFechaColuna()
{
	# Adicionar item
	echo "</td>\n";
} # fecha fun��o fecha coluna aberta


# Fun��o para adicionar items a tabela
function itemTabela($item, $url, $alinhamento, $corFundo, $colunas, $classeCSS)
{
	if($colunas>0) {
		$colspan="colspan=".$colunas; 
	}

	# Adicionar item
	echo "
	<tr bgcolor=$corFundo>\n
	<td $colspan align=$alinhamento class=$classeCSS><a href=$url>$item</a></td>\n
	</tr>\n";
} # fecha fun��o para adicionar items a tabela



# Fun��o para adicionar items a tabela
function itemTabelaNOURL($item, $alinhamento, $corFundo, $colunas, $classeCSS)
{
	if($colunas>0) {
		$colspan="colspan=".$colunas; 
	}

	# Adicionar item
	echo "
	<tr bgcolor=$corFundo>\n
	<td $colspan align=$alinhamento class=$classeCSS>$item</td>\n
	</tr>\n";
} # fecha fun��o para adicionar items a tabela



# Fun��o para adicionar items a tabela
function itemLinha($item, $url, $alinhamento, $corFundo, $colunas, $classeCSS)
{
	if($colunas>0) {
		$colspan="colspan=".$colunas; 
	}

	# Adicionar item
	echo "<td $colspan align=$alinhamento class=$classeCSS><a href=$url>$item</a></td>\n";
} # fecha fun��o para adicionar items a tabela

# Fun��o para adicionar items a tabela
function itemLinhaForm($item, $alinhamento, $alinhamentov, $corFundo, $colunas, $classeCSS)
{
	if($colunas>0) {
		$colspan="colspan=".$colunas; 
	}

	# Adicionar item 
	echo "<td $colspan align=$alinhamento valign=$alinhamentov class=$classeCSS>$item</td>\n";
} # fecha fun��o para adicionar items a tabela


# Fun��o para adicionar items a tabela
function itemLinhaTM($item, $url, $alinhamento, $alinhamentov, $tamanho, $corFundo, $colunas, $classeCSS)
{
	if($colunas>0) {
		$colspan="colspan=".$colunas; 
	}

	# Adicionar item 
	echo "<td $colspan align=$alinhamento valign=$alinhamentov class=$classeCSS width=$tamanho><a href=$url>$item</a></td>\n";
} # fecha fun��o para adicionar items a tabela



# Fun��o para adicionar items a tabela
function itemLinhaTMNOURL($item, $alinhamento, $alinhamentov, $tamanho, $corFundo, $colunas, $classeCSS)
{
	if($colunas>0) {
		$colspan="colspan=".$colunas; 
	}

	# Adicionar item 
	echo "<td $colspan align=$alinhamento valign=$alinhamentov class=$classeCSS width=$tamanho>$item</td>\n";
} # fecha fun��o para adicionar items a tabela



# Fun��o para adicionar items a tabela
function itemLinhaNOURL($item, $alinhamento, $corFundo, $colunas, $classeCSS)
{
	if($colunas>0) {
		$colspan="colspan=".$colunas; 
	}

	# Adicionar item
	echo "<td $colspan align=$alinhamento class=$classeCSS>$item</td>\n";
} # fecha fun��o para adicionar items a tabela


# Fun��o para adicionar items a tabela
function itemLinhaCorNOURL($item, $alinhamento, $corFundo, $colunas, $classeCSS, $cor)
{
	if($colunas>0) {
		$colspan="colspan=".$colunas; 
	}

	# Adicionar item
	echo "<td $colspan align=$alinhamento class=$classeCSS bgcolor=$cor>$item</td>\n";
} # fecha fun��o para adicionar items a tabela


# Fun��o para adicionar items a tabela
function itemLinhaCor($item, $url, $alinhamento, $corFundo, $colunas, $classeCSS, $cor)
{
	if($colunas>0) {
		$colspan="colspan=".$colunas; 
	}

	# Adicionar item
	echo "<td $colspan align=$alinhamento class=$classeCSS bgcolor=$cor><a href=$url>$item</a></td>\n";
} # fecha fun��o para adicionar items a tabela


# Fun��o para cria��o de linha na tabela (usado para items de coluna)
function novaLinhaTabela($fundo, $tamanho)
{
	echo "<tr bgcolor=$fundo>\n";
} # fecha fun��o de cria��o de linha na tabela


# Fun��o para adicionar itens de coluna
function titLinhaTabela($item, $alinhamento, $tamanho, $classeCSS)
{
	# Adicionar item
	echo "<td class=$classeCSS width=$tamanho align=$alinhamento>$item</td>\n";
} # fecha fun��o para adicionar itens de coluna


# Fun��o para adicionar itens de coluna
function itemLinhaTabela($item, $alinhamento, $tamanho, $classeCSS)
{
	# Adicionar item
	echo "<td width=$tamanho align=$alinhamento class=$classeCSS valign=middle>$item</td>\n";
} # fecha fun��o para adicionar itens de coluna



# Fun��o para fechar linha na tabela (usado para itens de coluna)
function fechaLinhaTabela()
{
	echo "</tr>\n";
} #fecha fun��o para fechar linha da tabela




# fun��o para finalizar tabela
function fechaTabela()
{
	echo "</table></td></tr></table>\n";
} # Fecha fun��o fechatabela




# Fun��es para resultados HTML em tela
function aviso2($titulo, $msg, $url, $tamanho, $borda, $fundo, $alinhamento)
{
	global $corFundo, $corBorda;

	novaTabela("$titulo", "left", $tamanho, 0, $borda, $fundo, $corFundo, $corBorda, 0);
	itemTabelaNOURL('&nbsp;',$alinhamento,$corFundo, 0, 'txtaviso');
	itemTabelaNOURL($msg,$alinhamento,$corFundo, 0, 'txtaviso');
	itemTabela("Voltar",$url,'right',$corFundo, 0, 'normal');
	fechaTabela();
	
} # fecha fun��o aviso


# Fun��es para resultados HTML em tela
function aviso($titulo, $msg, $url, $tamanho)
{
	global $corFundo, $corBorda;

	novaTabela("$titulo", "left", $tamanho, 0, 2, 1, $corFundo, $corBorda, 0);
	itemTabelaNOURL($msg,'left',$corFundo, 0, 'txtaviso');
	itemTabela("Voltar",$url,'right',$corFundo, 0, 'normal');
	fechaTabela();
	
} # fecha fun��o aviso



# Fun��es para resultados HTML em tela
function avisoNOURL($titulo, $msg, $tamanho)
{
	global $corFundo, $corBorda;

	novaTabela("$titulo", "left", $tamanho, 0, 2, 1, $corFundo, $corBorda, 0);
	itemTabelaNOURL($msg,'left',$corFundo, 0, 'txtaviso');	
	fechaTabela();
	
} # fecha fun��o aviso



# Fun��es para resultados HTML em tela
function avisoFechar($titulo, $msg, $url, $tamanho)
{
	global $corFundo, $corBorda;

	novaTabela("$titulo", "left", $tamanho, 0, 2, 1, $corFundo, $corBorda, 0);
	itemTabelaNOURL($msg,'left',$corFundo, 0, 'txtaviso');
	itemTabela("Fechar",$url,'right',$corFundo, 0, 'normal');
	fechaTabela();
	
} # fecha fun��o aviso



# Fun��o de cabe�alho
function html_header($titulo, $versao)
{
	global $modulo, $sub, $acao;
	
	$opcFocus="onLoad=javascript:document.forms[0].elements[4].focus()";
	
	# Mostrar cabe�alho HTML
	echo "<html>
<head>
<title>$titulo - $versao</title>
<link rel=stylesheet type=text/css href=estilos.css>
</head>
<body $opcFocus>";
} # fim da fun��o de cabe�alho




# Fun��o de rodap�
function html_footer($rodape)
{
	echo "<p class=rodape align=left>$rodape</p>\n
</body>\n
</html>\n";
} # fim da fun��o de rodap�



# Fun��o para mostrar tipo de Status da Fila de Processos
function fila_chk_status($status)
{
	# Verificar tipo de status recebido
	if($status=="N")
	{
		$retorno="<span class=fila_novo>Novo</span>";
	}
	elseif($status=="L")
	{
		$retorno="<span class=fila_lib>Liberado</span>";
	}
	elseif($status=="E")
	{
		$retorno="<span class=fila_exec>Executando</span>";
	}
	elseif($status=="P")
	{
		$retorno="<span class=fila_proc>Processado</span>";
	}
	elseif($status=="I")
	{
		$retorno="<span class=fila_ig>Ignorado</span>";
	}
	elseif($status=="F")
	{
		$retorno="<span class=fila_falha>FALHOU</span>";
	}
	else
	{
		$retorno="<span class=fila_erro>INV�LIDO</span>";
	}
	
	return($retorno);
	
} # fim da fun��o de mostragem de tipo de status dos processos




# fun��o para montagem de op��es para URLs e Links
function htmlMontaOpcao($texto, $tipo)
{
	global $htmlDirImagem, $html;

	return("<img src=".$html[imagem][$tipo]." border=0>$texto&nbsp;");
	
}


# Fun��o para montar campo de formulario
function formStatus()
{
	
	$item="<select name=matriz[status]>\n";
	$item.= "<option value=D>Desativado\n";
	$item.= "<option value=A>Ativado\n";
	$item.="</select>";
	
	return($item);
	
} #fecha funcao de montagem de campo de form

# Fun��o para montar campo de formulario - selecionando atual situacao
function formChecaStatus($status)
{
	# Status ativo
	if($status=='A') { $opcAtivo='selected'; }
	if($status=='D') { $opcDesativo='selected'; }
	
	$item="<select name=matriz[status]>\n";
	$item.= "<option value=D $opcDesativo>Desativado\n";
	$item.= "<option value=A $opcAtivo>Ativado\n";
	$item.="</select>";
	
	return($item);
	
} #fecha funcao de montagem de campo de form


# Fun��o para checagem de status
function checaStatus($status)
{
	if($status=='A') {
		$retorno="<span class=txtok>Ativado</span>";
	}
	elseif($status=='D') {
		$retorno="<span class=txtaviso>Desativado</span>";
	}
	elseif($status=='N') {
		$retorno="<span class=txtaviso>Novo (desativado)</span>";
	}
	
	return($retorno);
}


?>
