<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 14/10/2002
# Ultima alteração: 23/04/2003
#    Alteração No.: 023
#
# Função:
#    Funções para formatação de pagina e saidas HTML

# Função para criação de tabelas
/**
 * @return unknown
 * @param unknown $titulo
 * @param unknown $alinhamento
 * @param unknown $tamanho
 * @param unknown $borda
 * @param unknown $spcCel
 * @param unknown $spcCarac
 * @param unknown $corFundo
 * @param unknown $corBorda
 * @param unknown $colunas
 * @desc Enter description here...
*/
function novaTabela($titulo, $alinhamento, $tamanho, $borda, $spcCel, $spcCarac, $corFundo, $corBorda, $colunas)
{
	global $retornaHtml;
	
	if($colunas>0) {
		$colspan=' colspan="'.$colunas.'"';
	}
	# Criar nova tabela
	$saida='<!-- Cria nova tabela ['.$titulo."] -->\n".
	'<table width="'.$tamanho.'" border="'.$borda.'" cellpadding="0" cellspacing="0" bgcolor="'.$corBorda.'">'."\n".
	"<tr><td>\n".
	'<table width="100%" border="'.$borda.'" cellpadding="'.$spcCel.'" cellspacing="'.$spcCarac.'">'."\n".
	'<tr><th'.$colspan.' width="'.$tamanho.'" class="tabtitulo" align="'.$alinhamento.'" background="'.$corFundo.'">'.
	$titulo.'</th></tr>'."\n";
	
	if($retornaHtml)
		return ($saida);
	else
		echo $saida;
	
} # Fecha função tabelanova


# Função para criação de tabelas
function novaTabelaSH($alinhamento, $tamanho, $borda, $spcCel, $spcCarac, $corFundo, $corBorda, $colunas)
{
	global $retornaHtml;
	
	if($colunas>0) {
		$colspan="colspan=".$colunas; 
	}

	# Criar nova tabela
	$saida="<table width=$tamanho border=$borda cellpadding=0 cellspacing=0 bgcolor=$corBorda>\n
	<tr><td>\n
	<table width=$tamanho border=$borda cellpadding=$spcCel cellspacing=$spcCarac>\n";
	
	if($retornaHtml) 
		return ($saida);
	else 
		echo $saida;
		
} # Fecha função tabelanova


# Função para criação de tabelas
/**
 * @return void
 * @param unknown $titulo
 * @param unknown $alinhamento
 * @param unknown $tamanho
 * @param unknown $borda
 * @param unknown $spcCel
 * @param unknown $spcCarac
 * @param unknown $corFundo
 * @param unknown $corBorda
 * @param unknown $colunas
 * @desc Abre 1 tabela pra servir de fundo (com a corFundo) e outra q vai receber o conteudo
*/
function novaTabela2($titulo, $alinhamento, $tamanho, $borda, $spcCel, $spcCarac, $corFundo, $corBorda, $colunas)
{
	global $retornaHtml;
	
	if($colunas>0) {
		$colspan=' colspan="'.$colunas.'"';
	}
	
	# Criar nova tabela
	$saida="<!-- Cria nova tabela [$titulo] -->\n".
	"<table width=\"$tamanho\" border=\"$borda\" cellpadding=\"0\" cellspacing=\"$spcCarac\" bgcolor=\"$corBorda\">\n".
	"<tr><td>\n".
	"<table width=\"$tamanho\" border=\"0\" cellpadding=\"$spcCel\" cellspacing=\"0\">\n".
	"<tr><th width=\"$tamanho\" class=\"tabtitulo\" align=\"$alinhamento\" background=\"$corFundo\"".$colspan.">$titulo</th></tr>\n";

	if($retornaHtml) 
		return ($saida);
	else 
		echo $saida;
} # Fecha função tabelanova


# Função para criação de tabelas
/**
 * @return unknown
 * @param unknown $alinhamento
 * @param unknown $tamanho
 * @param unknown $borda
 * @param unknown $spcCel
 * @param unknown $spcCarac
 * @param unknown $corFundo
 * @param unknown $corBorda
 * @param unknown $colunas
 * @desc Abre uma tabela2 sem titulo
*/
function novaTabela2SH($alinhamento, $tamanho, $borda, $spcCel, $spcCarac, $corFundo, $corBorda, $colunas)
{
	global $retornaHtml;
		
	if($colunas>0) {
		$colspan="colspan=".$colunas; 
	}
	
	# Criar nova tabela
	$saida="<!-- Cria nova tabela [$titulo] -->\n
	<table width=$tamanho border=$borda cellpadding=0 cellspacing=$spcCarac bgcolor=$corBorda>\n
	<tr><td>\n
	<table width=$tamanho border=0 cellpadding=$spcCel cellspacing=0>\n";
	
	if($retornaHtml) 
		return ($saida);
	else 
		echo $saida;
} # Fecha função tabelanova



# Função para criação de tabelas - Sem Header
function htmlAbreTabelaSH($alinhamento, $tamanho, $borda, $spcCel, $spcCarac, $corFundo, $corBorda, $colunas)
{
	global $retornaHtml;
	
	# Criar nova tabela
	$saida="<table width=\"$tamanho\" border=\"$borda\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"$corBorda\">\n".
	"<tr><td>\n".
	"<table width=\"$tamanho\" border=\"$borda\" cellpadding=\"$spcCel\" cellspacing=\"$spcCarac\">\n";
	
	if($retornaHtml) 
		return ($saida);
	else 
		echo $saida;
} # Fecha função tabelanova


/**
 * Função para crição de nova linha de tabela
 *
 * @param string $corFundo
 * @param string $estilo
 * @return unknown
 */
function htmlAbreLinha($corFundo, $estilo = "" ){
	global $retornaHtml;
	
	$parametros = "";
	if($corFundo) $parametros.= " bgcolor=\"$corFundo\"";
	if($estilo)   $parametros.= " class=\"$estilo\"";
	
	$saida = "<tr" . $parametros . ">\n";
	
	if( $retornaHtml ) 
		return ($saida);
	else 
		echo $saida;
} #fecha função de abertura de nova linha

# Função para fechar linha aberta
function htmlFechaLinha()
{
	global $retornaHtml;
	
	$saida="</tr>\n";
	
	if($retornaHtml) 
		return ($saida);
	else 
		echo $saida;
} # fecha função de fechamento de linha

# Função para abrir nova coluna
/**
 * @return void
 * @param $tamanho
 * @param $alinhamento
 * @param $corFundo
 * @param $colunas
 * @param $classeCSS
 * @desc Abre um TR com os parametros
*/
function htmlAbreColuna($tamanho, $alinhamento, $corFundo, $colunas, $classeCSS)
{
	global $retornaHtml;
	
	if( $colunas ) $colspan =' colspan="'.$colunas.'"';
	# Adicionar item
	$saida = '<td' . $colspan . ' width="' . $tamanho . '" align="'.$alinhamento.'" class="'.$classeCSS."\">\n";
	
	if($retornaHtml) 
		return ($saida);
	else 
		echo $saida;
} # fecha função abrir nova coluna


# Função para abrir nova coluna
function htmlAbreColunaForm($tamanho, $alinhamento, $alinhamentov, $corFundo, $colunas, $classeCSS)
{
	global $retornaHtml;
	
	# Adicionar item
	$saida="<td width=$tamanho colspan=$colunas align=$alinhamento valign=$alinhamentov class=$classeCSS>\n";
	
	if($retornaHtml) 
		return ($saida);
	else 
		echo $saida;
} # fecha função abrir nova coluna

# Função para fechar nova coluna
function htmlFechaColuna()
{
	global $retornaHtml;
	
	# Adicionar item
	$saida="</td>\n";
	
	if($retornaHtml) 
		return ($saida);
	else 
		echo $saida;
} # fecha função fecha coluna aberta


# Função para adicionar items a tabela
function itemTabela($item, $url, $alinhamento, $corFundo, $colunas, $classeCSS)
{
	global $retornaHtml;
	
	if($colunas>0) {
		$colspan="colspan=".$colunas; 
	}

	# Adicionar item
	$saida="
	<tr bgcolor=$corFundo>\n
	<td $colspan align=$alinhamento class=$classeCSS><a href=$url>$item</a></td>\n
	</tr>\n";
	
	if($retornaHtml) 
		return ($saida);
	else 
		echo $saida;
} # fecha função para adicionar items a tabela



# Função para adicionar items a tabela
/**
 * @return void
 * @param $tamanho
 * @param $alinhamento
 * @param $corFundo
 * @param $colunas
 * @param $classeCSS
 * @desc Adiciona itens à tabela
*/
function itemTabelaNOURL($item, $alinhamento, $corFundo, $colunas, $classeCSS)
{
	global $retornaHtml;
	
	if($colunas>0) {
		$colspan=' colspan="'.$colunas.'"';
	}

	# Adicionar item
	$saida="<tr bgcolor=\"$corFundo\">\n".
	'<td'.$colspan.' align="'.$alinhamento.'" class="'.$classeCSS.'">'.$item."</td>\n".
	"</tr>\n";
	
	if($retornaHtml) 
		return ($saida);
	else 
		echo $saida;
} # fecha função para adicionar items a tabela



# Função para adicionar items a tabela
function itemLinha($item, $url, $alinhamento, $corFundo, $colunas, $classeCSS)
{
	global $retornaHtml;
	
	if($colunas>0) {
		$colspan=' colspan="'.$colunas.'"';
	}

	# Adicionar item
	$saida='<td'.$colspan.' align="'.$alinhamento.'" class="'.$classeCSS.'"><a href="'.$url.'">'.$item."</a></td>\n";
	
	if($retornaHtml) 
		return ($saida);
	else 
		echo $saida;
} # fecha função para adicionar items a tabela

# Função para adicionar items a tabela
/**
 * @return unknown
 * @param unknown $item
 * @param unknown $alinhamento
 * @param unknown $alinhamentov
 * @param unknown $corFundo
 * @param unknown $colunas
 * @param unknown $classeCSS
 * @desc  Adiciona um item à linha de uma tabela
 */
function itemLinhaForm($item, $alinhamento, $alinhamentov, $corFundo, $colunas, $classeCSS)
{
	global $retornaHtml;
	
	if($colunas>0) {
		$colspan='colspan="'.$colunas.'"';
	}

	# Adicionar item 
	$saida="<td ".$colspan.' align="'.$alinhamento.'" valign="'.$alinhamentov.'" class="'.$classeCSS.'">'.$item."</td>\n";
	
	if($retornaHtml) 
		return ($saida);
	else 
		echo $saida;
} # fecha função para adicionar items a tabela


# Função para adicionar items a tabela
function itemLinhaTM($item, $url, $alinhamento, $alinhamentov, $tamanho, $corFundo, $colunas, $classeCSS)
{
	global $retornaHtml;
	
	if($colunas>0) {
		$colspan="colspan=".$colunas; 
	}

	# Adicionar item 
	$saida="<td $colspan align=$alinhamento valign=$alinhamentov class=$classeCSS width=$tamanho>
			<a href=$url>$item</a></td>\n";
	
	if($retornaHtml) 
		return ($saida);
	else 
		echo $saida;
} # fecha função para adicionar items a tabela



# Função para adicionar items a tabela
/** <b>item:</b> item
	<b>alinhamento:</b> alinhamento (left, right, center)
	<b>alinhamentov:</b> alinhamento vertial (top, middle, baseline)
	<b>tamanho:</b> tamanho
	<b>corFundo:</b> cor do fundo
	<b>colunas:</b> quantidade de colunas
	<b>classeCSS:</b> nome da classe CSS
*/

function itemLinhaTMNOURL($item, $alinhamento, $alinhamentov, $tamanho, $corFundo, $colunas, $classeCSS)
{
	global $retornaHtml;
	
	if($colunas>0) {
		$colspan=' colspan="'.$colunas.'"';
	}

	# Adicionar item 
	$saida = '<td'.$colspan.' align="'.$alinhamento.'" valign="'.$alinhamentov.'" class="'.$classeCSS.'" width="'.$tamanho.'">'.
				$item."</td>\n";
	
	if($retornaHtml) 
		return ($saida);
	else 
		echo $saida;
} # fecha função para adicionar items a tabela



# Função para adicionar items a tabela
function itemLinhaNOURL($item, $alinhamento, $corFundo, $colunas, $classeCSS, $linhas = "" )
{
	global $retornaHtml;
	
	if($colunas>0) {
		$colspan="colspan=".$colunas; 
	}
	$rowspan = ($linhas) ? " rowspan=".$linhas : ""; 

	# Adicionar item
	$saida="<td $colspan align=$alinhamento class=$classeCSS ".$rowspan.">$item</td>\n";
	
	if($retornaHtml) 
		return ($saida);
	else 
		echo $saida;
} # fecha função para adicionar items a tabela


# Função para adicionar items a tabela
function itemLinhaCorNOURL($item, $alinhamento, $corFundo, $colunas, $classeCSS, $cor)
{
	global $retornaHtml;
	
	if($colunas>0) {
		$colspan="colspan=".$colunas; 
	}

	# Adicionar item
	$saida="<td $colspan align=$alinhamento class=$classeCSS bgcolor=$cor>$item</td>\n";
	
	if($retornaHtml) 
		return ($saida);
	else 
		echo $saida;
} # fecha função para adicionar items a tabela


# Função para adicionar items a tabela
function itemLinhaCor($item, $url, $alinhamento, $corFundo, $colunas, $classeCSS, $cor)
{
	global $retornaHtml;
	
	if($colunas>0) {
		$colspan="colspan=".$colunas; 
	}

	# Adicionar item
	$saida="<td $colspan align=$alinhamento class=$classeCSS bgcolor=$cor><a href=$url>$item</a></td>\n";
	
	if($retornaHtml) 
		return ($saida);
	else 
		echo $saida;
} # fecha função para adicionar items a tabela


# Função para criação de linha na tabela (usado para items de coluna)
/**
 * @return unknown
 * @param unknown $fundo
 * @param unknown $tamanho
 * @desc Insere um tr
*/
function novaLinhaTabela($fundo, $tamanho)
{
	global $retornaHtml;
	
	$saida="<tr bgcolor=\"".$fundo."\">\n";
	
	if($retornaHtml) 
		return ($saida);
	else 
		echo $saida;
} # fecha função de criação de linha na tabela


# Função para adicionar itens de coluna
function titLinhaTabela($item, $alinhamento, $tamanho, $classeCSS)
{
	global $retornaHtml;
	
	# Adicionar item
	$saida="<td class=$classeCSS width=$tamanho align=$alinhamento>$item</td>\n";
	
	if($retornaHtml) 
		return ($saida);
	else 
		echo $saida;
} # fecha função para adicionar itens de coluna


# Função para adicionar itens de coluna
function itemLinhaTabela($item, $alinhamento, $tamanho, $classeCSS)
{
	global $retornaHtml;
	
	# Adicionar item
	$saida="<td width=$tamanho align=$alinhamento class=$classeCSS valign=middle>$item</td>\n";
	
	if($retornaHtml) 
		return ($saida);
	else 
		echo $saida;
} # fecha função para adicionar itens de coluna

# Função para adicionar itens de coluna
function itemLinhaTabelaLinhas($item, $alinhamento, $tamanho, $classeCSS, $linhas)
{
	global $retornaHtml;
	
	if ($linhas > 0)
		$r = "rowspan=$linhas";
	
	# Adicionar item
	$saida="<td width=$tamanho align=$alinhamento class=$classeCSS valign=middle $r >$item</td>\n";
	
	if($retornaHtml) 
		return ($saida);
	else 
		echo $saida;
} # fecha função para adicionar itens de coluna


# Função para fechar linha na tabela (usado para itens de coluna)
function fechaLinhaTabela()
{
	global $retornaHtml;
	
	$saida="</tr>\n";
	
	if($retornaHtml) 
		return ($saida);
	else 
		echo $saida;
		
} #fecha função para fechar linha da tabela




# função para finalizar tabela
function fechaTabela()
{
	global $retornaHtml;
	
	$saida="</table></td></tr></table>\n";
	
	if($retornaHtml) 
		return ($saida);
	else 
		echo $saida;
} # Fecha função fechatabela




# Funções para resultados HTML em tela
function aviso2($titulo, $msg, $url, $tamanho, $borda, $fundo, $alinhamento)
{
	global $corFundo, $corBorda;

	novaTabela("$titulo", "left", $tamanho, 0, $borda, $fundo, $corFundo, $corBorda, 0);
	itemTabelaNOURL('&nbsp;',$alinhamento,$corFundo, 0, 'txtaviso');
	itemTabelaNOURL($msg,$alinhamento,$corFundo, 0, 'txtaviso');
	itemTabela("Voltar",$url,'right',$corFundo, 0, 'normal');
	fechaTabela();
	
} # fecha função aviso

	
# Funções para resultados HTML em tela
function aviso($titulo, $msg, $url, $tamanho)
{
	global $corFundo, $corBorda;

	novaTabela("$titulo", "left", $tamanho, 0, 2, 1, $corFundo, $corBorda, 0);
	itemTabelaNOURL($msg,'left',$corFundo, 0, 'txtaviso');
	itemTabela("Voltar",$url,'right',$corFundo, 0, 'normal');
	fechaTabela();
	
} # fecha função aviso



# Funções para resultados HTML em tela
function avisoNOURL($titulo, $msg, $tamanho)
{
	global $corFundo, $corBorda;

	novaTabela("<font color='#FFFFFF'>$titulo</font>", "left", $tamanho, 0, 2, 1, $corFundo, $corBorda, 0);
	itemTabelaNOURL($msg,'left',$corFundo, 0, 'txtaviso');	
	fechaTabela();
	
} # fecha função aviso



# Funções para resultados HTML em tela
function avisoFechar($titulo, $msg, $url, $tamanho)
{
	global $corFundo, $corBorda;

	novaTabela("$titulo", "left", $tamanho, 0, 2, 1, $corFundo, $corBorda, 0);
	itemTabelaNOURL($msg,'left',$corFundo, 0, 'txtaviso');
	itemTabela("Fechar",$url,'right',$corFundo, 0, 'normal');
	fechaTabela();
	
} # fecha função aviso



# Função de cabeçalho
function html_header($titulo, $versao)
{
	global $modulo, $sub, $acao, $retornaHtml, $xajax, $funcoesJs, $data;
	
	$opcFocus = "onLoad=\"javascript:if(document.forms[0]){document.forms[0].elements[4].focus()}\"";
	
	# Mostrar cabeçalho HTML
	$saida= "<html>\n".
			"<head>\n".
			"<title>$titulo - $versao</title>\n".
			$funcoesJs."\n".
//			$xajax->getJavascript("class/xajax").
			"<link rel=\"stylesheet\" type=\"text/css\" href=\"estilos.css\" />\n".
			"</head>\n".
			"<body $opcFocus>\n";
	
	if($retornaHtml) 
		return ($saida);
	else 
		echo $saida;
} # fim da função de cabeçalho




# Função de rodapé
function html_footer($rodape)
{
	global $retornaHtml;
	
	$saida = '<p class="rodape" align="left">'.$rodape."</p>\n".
			"</body>\n".
			"</html>\n";
	
	if($retornaHtml) 
		return ($saida);
	else 
		echo $saida;
} # fim da função de rodapé



# Função para mostrar tipo de Status da Fila de Processos
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
		$retorno="<span class=fila_erro>INVÁLIDO</span>";
	}
	
	return($retorno);
	
} # fim da função de mostragem de tipo de status dos processos




# função para montagem de opções para URLs e Links
/**
 * @return unknown
 * @param $texto
 * @param $tipo
 * @desc Gera itens de opcões
*/
function htmlMontaOpcao($texto, $tipo, $label=true )
{
	global $htmlDirImagem, $html;
 	if( $label ) {
 		return('<span style="white-space:nowrap"><img src="'.$html[imagem][$tipo].'" border="0">'.$texto."&nbsp;</span>");
 	}
 	else {
		return('<img src="'.$html[imagem][$tipo].'" border="0" alt="'.$texto.'" title="'.$texto.'">');
 	}
}


# Função para montar campo de formulario
function formStatus()
{
	
	$item="<select name=matriz[status]>\n";
	$item.= "<option value=D>Desativado\n";
	$item.= "<option value=A>Ativado\n";
	$item.="</select>";
	
	return($item);
	
} #fecha funcao de montagem de campo de form

# Função para montar campo de formulario - selecionando atual situacao
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


# Função para checagem de status
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

/**
 * Abre uma tag HTML div
 *
 * @param string $id
 * @param string $estilo
 **/
function abreDiv( $id, $estilo ){
	echo '<div id="' . $id . '" style="' . $estilo . '">';
}

/**
 * Fecha uma tag HTML div
 **/
function fechaDiv(){
	echo '</div>';
}

/**
 * 	Monta o cabecalho de uma tabela com os campos obtidos
 * do array $campos
 * 
 * @author João Petrelli
 * @since 02/02/2009
 *
 * @param String $titulo
 * @param array $campos
 * @param String $alinhamento
 * @param String $tamanho
 * @param int $borda
 * @param int $spcCel
 * @param int $spcCarac
 * @param String $corFundo
 * @param String $corBorda
 * @param int $coluna
 * @param String $tamanhoLinha
 */
function montaTabela($titulo, $campos = array(), $alinhamento, $tamanho, $borda, $spcCel, $spcCarac, $corFundo, $corBorda, $coluna, $tamanhoLinha) {
	echo "<br>";
	novaTabela($titulo, $alinhamento, $tamanho, $borda, $spcCel, $spcCarac, $corFundo, $corBorda, $coluna);
		novaLinhaTabela($corFundo, $tamanhoLinha);

		for ($i = 0; $i < count($campos['nome']); $i++) {
			itemLinhaTabela($campos['nome'][$i], $campos['alinhamento'][$i], $campos['tamanho'][$i], $campos['classeCSS'][$i]);
		}
		
		fechaLinhaTabela();
}

/**
 * Monta um campo de acordo com os parametros informados
 * com uma quebra de linha em seu início
 *
 * @author João Petrelli
 * @since 02/02/2009
 * 
 * @param String $type
 * @param String $name
 * @param String $value
 * @param String $class
 */
function botaoConfirmar($type, $name, $value, $class) {
	echo "<br> <input type='$type' name ='$name' value='$value' class='$class'>";
}

/**
 * Cria um campo de acordo com os parametros informados
 * 
 * @author João Petrelli
 * @since 02/02/2009
 *
 * @param String $type
 * @param String $name
 * @param String $value
 * @param String $class
 */
function inputType($type, $name, $value, $class = '') {
	echo "<input type='$type' name='$name' value='$value' class='$class'>";
}
?>
