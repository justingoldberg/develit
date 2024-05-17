<?
################################################################################
#       Criado por: Devel IT
#  Data de criação: 18/06/2004
# Ultima alteração: 16/09/2004
#    Alteração No.: 002
#
# Função:
#    Funções para serem utilizadas nos forms de consulta


/**
 * @return void
 * @param unknown $p1
 * @param unknown $p2
 * @param unknown $matriz
 * @desc Exibe no formato padrão os dados de data para entrada de dados
 p1 = posicao na tela do mes inicial
 p2 = posicao na tela do mes final
 matriz = matriz de dados para exibir dados padrao
*/

$bla=new FPDF();
//$bla->

function getPeriodo($p1, $p2, $matriz) {
	
	global $corFundo, $corBorda, $html, $sessLogin;
	
	$data=dataSistema();
	
	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL('<b>Mês/Ano Inicial:</b><br><span class=normal10>Informe o mês/ano inicial para consulta</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		$texto="<input type=text name=matriz[dtInicial] size=7 value='$matriz[dtInicial]' onBlur=verificaDataMesAno2(this.value,$p1)>&nbsp;<span class=txtaviso>(Formato: $data[mes]/$data[ano])</span>";
	    itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
	
	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL('<b>Mês/Ano Final:</b><br><span class=normal10>Informe o mês/ano final para consulta</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		$texto="<input type=text name=matriz[dtFinal] size=7 value='$matriz[dtFinal]'  onBlur=verificaDataMesAno2(this.value,$p2)>&nbsp;<span class=txtaviso>(Formato: $data[mes]/$data[ano])</span>";
	    itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
	
}


function getPeriodoUnico( $p1, $matriz ) {
	
	global $corFundo, $corBorda, $html, $sessLogin;
	
	$data=dataSistema();
	
	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL('<b class="bold10">Mês/Ano Inicial:</b><br><span class=normal10>Informe o mes/ano inicial para consulta</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		$texto="<input type=text name=matriz[dtInicial] size=7 value='{$matriz['dtInicial']}' onBlur=verificaDataMesAno2(this.value,$p1)>&nbsp;<span class=txtaviso>(Formato: {$data['mes']}/{$data['ano']})</span>";
	    itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
		
}

/**
 * @return void
 * @param unknown $p1
 * @param unknown $p2
 * @param unknown $matriz
 * @desc Exibe no formato padrão os dados de data para entrada de dados
 p1 = posicao na tela do mes inicial
 p2 = posicao na tela do mes final
 matriz = matriz de dados para exibir dados padrao
*/
function getPeriodoDias($p1, $p2, $matriz, $complemento='') {
	
	global $corFundo, $corBorda, $html, $sessLogin;
	
	$data=dataSistema();
	
	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL('<b>Data '.$complemento.' Inicial:</b><br><span class=normal10>Informe a data inicial para consulta</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		$texto="<input type=text name=matriz[dtInicial] size=10 value='$matriz[dtInicial]' onBlur=verificaData(this.value,$p1)>&nbsp;<span class=txtaviso>(Formato: $data[dia]/$data[mes]/$data[ano])</span>";
	    itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
	
	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL('<b>Data '.$complemento.' Final:</b><br><span class=normal10>Informe a data final para consulta</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		$texto="<input type=text name=matriz[dtFinal] size=10 value='$matriz[dtFinal]'  onBlur=verificaData(this.value,$p2)>&nbsp;<span class=txtaviso>(Formato: $data[dia]/$data[mes]/$data[ano])</span>";
	    itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
	
}

function getValores($matriz){
			//valores
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Valor Inicial:</b><br>
				<span class=normal10>Informe o menor valor para consulta</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[valorInicial] align=right size=7 value='$matriz[valorInicial]'>&nbsp;<span class=txtaviso>,00 (Formato: 999,00)</span>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Valor Final:</b><br>
				<span class=normal10>Informe o maior valor para consulta</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[valorFinal] align=right size=7 value='$matriz[valorFinal]'>&nbsp;<span class=txtaviso>,00 (Formato: 999,00)</span>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
}


/**
 * @return void
 * @param unknown $matriz
 * @desc Retorna uma caixa de selecao dos POPs
*/
function getPop($matriz) {
	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL('<b>POP:</b><br>
		<span class=normal10>Selecione o POP de Acesso</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		if($matriz[pop_todos]) $opcPOP='checked';
		$texto="<input type=checkbox name=matriz[pop_todos] value='S' $opcPOP><b>Todos</b>";
		itemLinhaForm(formSelectPOP($matriz[pop],'pop','multi').$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
}


/**
 * @return void
 * @param unknown $matriz
 * @desc Retorna um botao tipo CONFIRMAR
*/
function getBotaoConfirmar($matriz) {
	getBotao('matriz[bntConfirmar]', 'Consultar');
}


/**
 * @return void
 * @param unknown $matriz
 * @desc Monta um botao generico
*/
function getBotao($name, $value, $class='submit', $tipo='submit', $evento='') {
	
	global $corFundo;
	
	novaLinhaTabela($corFundo, '100%');
		$texto="<input type=$tipo name=$name value='$value' class='$class'".$evento.">";
		itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
	fechaLinhaTabela();	
}

/**
 * @return void
 * @param unknown $matriz
 * @desc Exibe os botoes Consulta e Relatorio com as classes
*/
function getBotoesConsRel() {
	novaLinhaTabela($corFundo, '100%');
		$texto="<input type=submit name=matriz[bntConfirmar] value='Consultar' class=submit>";
		$texto.="&nbsp;<input type=submit name=matriz[bntRelatorio] value='Gerar Relatório' class=submit2>";
		itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
	fechaLinhaTabela();
}

/**
 * @return void
 * @param unknown $matriz
 * @desc Exibe os botoes Consulta e Relatorio com as classes
*/
function getBotoesConsRelPDF_CSV() {
	novaLinhaTabela($corFundo, '100%');
		$texto="<input type=submit name=matriz[bntConfirmar] value='Consultar' class=submit>";
		$texto.="&nbsp;<input type=submit name=matriz[bntRelatorioPDF] value='Gerar Relatório PDF' class=submit2>";
		$texto.="&nbsp;<input type=submit name=matriz[bntRelatorioCSV] value='Gerar Relatório CSV' class=submit3>";
		itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
	fechaLinhaTabela();
}


/**
 * @return void
 * @param unknown $matriz
 * @desc Retorna uma caixa de seleção com os seviços adicionais
*/
function getServicoAdicional($matriz) {
	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL('<b>Serviço Adicional:</b><br>Selecione o tipo do serviço adicional', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		$texto=formSelectTipoServicoAdicional($matriz[idTipoServicoAdicional],'idTipoServicoAdicional','form', 0);
		$texto.="<input type=checkbox name=matriz[sa_todos] value=S $opcSA><b>Todos</b>";
		itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
}


/**
 * @return void
 * @param unknown $matriz
 * @desc Retorna check bos de detalhar cliente
*/
function getDetalharCliente($matriz, $adic=' por cliente') {
	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL('<b>Detalhar:</b><br />
		<span class="normal10">Detalhar relatório'.$adic.'</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		$opcDetalhar = ( $matriz['detalhar'] ? 'checked="checked"' : '' );
		$texto="<input type=\"checkbox\" name=\"matriz[detalhar]\" value=\"S\"".$opcDetalhar." />\n";
		itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
}


/**
 * @return void
 * @param unknown $modulo
 * @param unknown $sub
 * @param unknown $opcao
 * @param unknown $acao
 * @desc Enter description here...
*/
function getOpcao($modulo, $sub, $opcao, $acao="") {
	switch ($opcao) {
		case 'Adicionar':
			$icone='incluir';
			$acao='adicionar';
			break;
		case 'Novo':
			$icone = 'incluir';
			$acao  = 'novo';
			break;
		case 'Procurar':
			$icone='procurar';
			$acao='procurar';
			break;
		case 'Listar':
			$icone='listar';
			$acao='listar';
			break;
		case 'Alterar':
			$icone='alterar';
			$acao='alterar';
			break;
		case 'Ver':
			$icone='ver';
			$acao='ver';
			break;
		case 'Ler Arquivo':
			$icone='importar'; // apenas teste para o item buscar remessa
			$acao='lerArquivo';
			break;
		case 'Enviar Arquivo':
			$icone='forward';
			$acao='enviarArquivo';
			break;
		case 'Fracionar':
			$icone='modulo';
			$acao='novo_fracionar';
			break;
	}
			
	$texto=htmlMontaOpcao( '<br />' . $opcao, $icone );
	itemLinha( $texto, "?modulo=$modulo&sub=$sub&acao=$acao", 'center', $corFundo, 0, 'normal' );
}


/**
 * @return void
 * @param unknown $modulo
 * @param unknown $sub
 * @param unknown $titulo
 * @param unknown $subtitulo
 * @param unknown $itens
 * @desc Monta tela inicial dos modulos
*/
function getHomeModulo($modulo, $sub, $titulo, $subtitulo, $itens) {
	
	global $corFundo, $corBorda, $html;
	
	novaTabela2("[$titulo]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 4);
		
		novaLinhaTabela($corFundo, '100%');
			#coluna de identificação do modulo
			htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
				echo '<br /><img src="'.$html[imagem][cadastro].'" border="0" align="left"><b class="bold">'.$titulo."</b>\n".
				     '<br /><span class="normal10">'.$subtitulo."</b>.</span>\n";
			htmlFechaColuna();
			
			#exibe os icones iniciais
			if(is_array($itens))
				foreach ($itens as $item)
					getOpcao($modulo, $sub, $item);
			else
				getOpcao($modulo, $sub, $itens);
			
		fechaLinhaTabela();
	fechaTabela();
}


/**
 * @return void
 * @param unknown $tipo
 * @param unknown $label
 * @param unknown $name
 * @param unknown $value
 * @param unknown $event
 * @param unknown $forma
 * @param unknown $tamanho
 * @param string  $tabindex
 * @param boolean $checked
 * @param string  $comentario
 * @desc Retorna um campo de formulario
*/
/**
 * Retorna um campo de formulario no estilo que o rótulo vem a esquerda do campo, ou seja,
 * uma linha de tabela com duas colunas
 *
 * @param string $tipo
 * @param string  $label
 * @param string  $name
 * @param unknown $value
 * @param string  $event
 * @param string  $forma
 * @param integer $tamanho
 * @param string  $tabindex
 * @param boolean $checked
 * @param string  $comentario
 */
function getCampo( $tipo, $label, $name="", $value="", $event="", $forma="form", $tamanho=60, $tabindex="", $checked=false, $comentario='' ) {
	global $corFundo, $corBorda, $html;
	
	$indiceTab = ( ( $tabindex != "" ) ? " tabindex=\"".$tabindex."\"" : "" );
	
	$evento = ( $event ? " $event" : "");
	
	echo novaLinhaTabela($corFundo, '100%');
		$rotulo = '<b class="bold10">'.($label ? $label.': ' : '&nbsp;')."</b>";
		if( $comentario ) {
			$rotulo .= '<br /><span class="normal10">'.$comentario.'</span>';
		}
		itemLinhaTMNOURL($rotulo, 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		if($tipo) {
			switch ($tipo) {
				case 'text': 
					$texto="<input type=\"text\" id=\"$name\" name=\"$name\" size=\"$tamanho\" value=\"$value\"".$indiceTab.$event." />";
					break;
				case 'status': 
					$texto=getComboStatus($value, $name, $forma);
					break;
				case 'hidden':
					$texto="<input type=\"hidden\" name=\"$name\" value=\"$value\" />".$value;
					break;
				case 'area':
					$texto="<textarea name=\"$name\" id=\"$name\" cols=\"$tamanho\" rows=\"6\" ".$indiceTab.">$value</textarea>";
					break;
				case 'combo':
					$texto=$value;
					break;
				case 'checkbox':
					$checado = ( $checked ? 'checked="checked"' : '');
					$id = str_replace('matriz[', '', $name );
					$id = str_replace(']', '', $id );
					$texto="<input type=\"checkbox\" id=\"$id\" name=\"$name\" value=\"$value\"".$indiceTab.$event.$checado." />";
					break;
			}
			itemLinhaForm($texto, 'left', 'top',  $corFundo, 0, 'tabfundo1');
		}
		else {
			itemLinhaTMNOURL($value, 'left', 'middle',  '', $corFundo,  0,  'tabfundo1');
		}
	fechaLinhaTabela();
}


/**
 * @return unknown
 * @param unknown $item
 * @param unknown $campo
 * @param unknown $retorno
 * @param unknown $tipo
 * @param unknown $tabela
 * @param unknown $coluna
 * @desc Retorna um Select com os dados da tabela
 item = valor para selected
 campo = campo da matriz para o mult
 retorno = nome do campo select
 tipo = tipo de select (form, formnochange, multi)
 tabela = tabela a ser consultada
 coluna = nome do campo do banco de dados
 exclusoes = Array com valores a serem desconsiderados
*/
function getSelectDados($item, $campo, $retorno, $tipo, $tabela, $coluna="", $exclusoes="", $condicao='', $classe='' ) {
	
	global $conn, $tb, $corFundo, $modulo, $sub;

	if( !$coluna ) $coluna='nome';
	if( !$exclusoes ) $exclusoes=array();
	$class = ( ( $classe ) ?  ' class="'.$classe.'"' : '' );
	
	if(($tipo=='form') || $tipo=='formnochange') {
	
		$consulta = ( $condicao 
						? buscaRegistros( $condicao, '', 'custom', $coluna, $tabela ) 
						: buscaRegistros( '','','todos',$coluna, $tabela ) );
		
		if($consulta && contaConsulta($consulta)>0) {
			
			if ($tipo=='formnochange') $retorno="\n<select name=\"$retorno\"".$class.">";

			else $retorno="\n<select name=\"$retorno\" onChange=\"javascript:submit()\">";
			
			for($i=0;$i<contaConsulta($consulta);$i++) {
			
				$id=resultadoSQL($consulta, $i, 'id');
				if (! in_array($id, $exclusoes)) {
					$nome=resultadoSQL($consulta, $i, $coluna);
				
					if($item==$id) $opcSelect=' selected="selected"';
					else $opcSelect='';
				
					$retorno.="\n<option value=\"$id\"".$opcSelect.">$nome</option>";
				}
			}
			$retorno.="\n</select>\n";
		}
		else {
			$retorno='Não há registros.';
		}
	}
	elseif($tipo=='multi') {
	
		$consulta=buscaRegistros('','','todos',$coluna, $tabela);
		
		if($consulta && contaConsulta($consulta)>0) {
			
			$retorno="<select multiple=\"multiple\" size=\"6\" name=\"matriz[$campo][]\">";
			
			for($i=0;$i<contaConsulta($consulta);$i++) {
				
				$id=resultadoSQL($consulta, $i, 'id');
				$nome=resultadoSQL($consulta, $i, $coluna);
				
				if($item==$id) $opcSelect='selected="select"';
				else $opcSelect='';
				
				$retorno.="\n<option value=$id $opcSelect>$coluna";
				
			}
			$retorno.="</select>";
		}
	}
	else {
		$retorno='';
	}
	
	return($retorno);
}



#  Função para mostrar form de seleçao do STATUS
/**
 * @return unknown
 * @param unknown $valor
 * @param unknown $campo
 * @param unknown $tipo
 * @param unknown $itens
 * @param unknown $labels
 * @desc Retorna um campo Select de form
 valor = valor padrao
 campo = nome do campo
 tipo = tipo de retorno: check ou form
 itens = array com os itens de status A, I, B, C. Default A, I
 labels = array com os labels para os itens
*/
function getComboStatus($valor, $campo="", $tipo, $itens="", $labels="") {
	#define os valores padroes
	if (! $itens) {
		$itens=array('A', 'I');
		$label['A'] = "Ativo";
		$label['I'] = "Inativo";
	}
	
	#pega os itens do array
	foreach ($itens as $item) 
		if($valor==$item) $opcSelect[$item]='selected';
	
	#form ele poe o controle na tela
	if( $tipo == 'form' ) {
		$texto = "<select name=\"$campo\">";
		foreach ( $itens as $item )
			$texto.="<option value=\"$item\" $opcSelect[$item]>$label[$item]</option>\n";
		$texto.="</select>";
	}
	elseif($tipo=='check') {
		if($valor=='A') 	$texto = "<span class=\"txtok\">Ativo</span>";
		elseif($valor=='I') $texto = "<span class=\"txtaviso\">Inativo</span>";
		elseif($valor=='B') $texto = "<span class=\"txtaviso\">Bloqueado</span>";
		elseif($valor=='C') $texto = "<span class=\"txtaviso\">Congelado</span>";
	}
	
	return($texto);
	
}


function exibeSemPermissao($modulo, $acao) {
	# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
	$msg="ATENÇÃO: Você não tem permissão para executar esta função";
	$url="?modulo=$modulo&sub=$sub";
	aviso("Acesso Negado", $msg, $url, 760);
}

/**
 * Retorna formulário de pesquisa por nome
 *
 * @param unknown_type $modulo
 * @param unknown_type $sub
 * @param unknown_type $acao
 * @param unknown_type $matriz
 * @param unknown_type $titulo
 */
function getFormProcurar($modulo, $sub, $acao, $matriz, $titulo) {
	
	global $corFundo, $corBorda, $tb;
	
	novaTabela2("[Procurar $titulo]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		
		novaLinhaTabela($corFundo, '100%');
		$texto="<form method=\"post\" name=\"matriz\" action=\"index.php\" />
			<input type=\"hidden\" name=\"modulo\" value=\"$modulo\" />
			<input type=\"hidden\" name=\"sub\" value=\"$sub\" />
			<input type=\"hidden\" name=\"acao\" value=\"$acao\"  />
			<input type=\"hidden\" name=\"registro\" />
			<p><b class=\"bold\">Nome </b><input type=\"text\" name=\"matriz[nome]\" size=\"40\" value=\"$matriz[nome]\" />
			<input type=\"submit\" name=\"matriz[bntProcurar]\" value=\"Procurar\" class=\"submit\" /></p>";
		
			itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
					
	fechaTabela();
	
	echo "<br />";

}

function getCamposClientes() {
	$campos[]="Pessoas.nome=Nome";
	$campos[]="Pessoas.razao=Razao";
	#$campos[]="Pessoas.site=Site";
	$campos[]="Pessoas.mail=Email";
	#$campos[]="Pessoas.dtNascimento=Nascto";
	$campos[]="Enderecos.endereco=Endereco";
	$campos[]="Enderecos.complemento=Compl";
	$campos[]="Enderecos.bairro=Bairro";
	$campos[]="Enderecos.cep=CEP";
	$campos[]="Cidades.nome=Cidade";
	$campos[]="Cidades.uf=UF";
	$campos[]="Enderecos.pais=Pais";
	$campos[]="Enderecos.caixa_postal=CxPostal";
	$campos[]="concat(ddd_fone1,'-',Enderecos.fone1)=Fone1";
	$campos[]="concat(ddd_fone2,'-',Enderecos.fone2)=Fone2";
	$campos[]="Enderecos.fax=Fax";
	#$campos[]="Pessoas.id=id";
	return $campos;
}

function getCamposGrupoServico() {
	$campos[]="GruposServico.nome=Grupo";
	$campos[]="GruposServico.descricao=Descricao_Grupo";
	return $campos;
}

function getCamposServicos() {
	$campos[]="Servicos.nome=Servico";
	$campos[]="Servicos.descricao=Descricao_Servico";
	$campos[]="Servicos.valor=Valor_Servico";
	return $campos;
}

function getComboCondicao($campo) {
	#form ele poe o controle na tela
	$texto="<select name=$campo>";
	$texto.="<option value='='>=\n";
	$texto.="<option value='>'>>\n";
	$texto.="<option value='<'><\n";
	$texto.="<option value='!='>!=\n";
	$texto.="<option value='contem'>Contém\n";
//	$texto.="<option value='isnull'>Vazio\n";
//	$texto.="<option value='!isnull'>Não Vazio\n";
	$texto.="</select>";
	
	return($texto);
}

/**
 * Retorna um combo baseado em array
 *
 * @param string $nome
 * @param array  $itemsConteudo
 * @param array  $itemsValores
 * @param string $selected
 * @param string $multiple
 * @param string $event
 * @return string
 */
function getComboArray( $nome, $itemsConteudo, $itemsValores, $selected = "", $multiple = "", $event = "" ){
	$texto = "<select name=\"$nome\"".( $multiple ? " multiple" : "" )." ".$event.">\n";

	if ( !is_array( $itemsValores ) ) {
		$itemsValores = $itemsConteudo;
	}
	
	foreach ( $itemsConteudo as $x => $items ){
			$select = ( $itemsValores[$x] == $selected ? 'selected="selected"' : "");
			$texto .= "<option value=\"". $itemsValores[$x]."\" ".$select.">".$items."</option>\n";
	}

	$texto .= "</select>";
	return  $texto;
}

/**
 * Retorna um combo de meses
 *
 * @param string $nome
 * @param string $valor
 * @param string $evento
 * @return string
 */
function getComboMeses( $nome="mes", $valor="", $evento="" ){
	$meses = array("Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
	$numMeses = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
	return getComboArray( $nome, $meses, $numMeses, $valor, "", $evento );
}

/**
 * Retorna um sequencia de radios baseado em array
 *
 * @param string $nome
 * @param array  $itemsValores
 * @param array  $itemsLabels
 * @param string $checked
 * @param string $event
 * @param string $separador
 * @return string
 */
function getRadioArray( $nome, $itemsValores, $itemsLabels, $checked = "", $event = "", $separador = "&nbsp;" ){
	
	$texto = "";
	
	if ( !is_array( $itemsLabels ) ) $itemsLabels = $itemsValores;
	
	foreach ( $itemsValores as $x => $item ){
		$check = ( ( $item == $checked ) ? " checked " : "");
		$texto .= "<input type=\"radio\" name=\"$nome\" id=\"$item\" value=\"$item\" ".$event.$check."/><label for=\"$item\">".$itemsLabels[$x]."</label>$separador\n";
	}

	return  $texto;
	
}

/**
 * Cria um combo com rotulo baseado em array ( da função getComboArray() )
 *
 * @param string $label
 * @param string $nome
 * @param string $itemsConteudo
 * @param string $itemsValores
 * @param string $selected
 * @param string $multiple
 * @param string $evento
 */
function getCampoCombo( $label, $nome, $itemsConteudo, $itemsValores, $selected = "", $multiple = "", $evento = "" ){
	global $corFundo, $corBorda, $html;
	
	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL("<b>$label: </b>", 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		itemLinhaTMNOURL( getComboArray( $nome, $itemsConteudo, $itemsValores, $selected, $multiple, $evento ), 
						  $value, 'left', 'middle',  '', $corFundo,  0,  'tabfundo1');
	fechaLinhaTabela();
	
}

function criaTemplates($nome, $matCabecalho, $matAlinhamento='', $matLargura='') {
	
	global $sessLogin, $template;
	
	#header
	$conteudo ="<table width=100%>\n";
	$conteudo.="<tr>\n";
		$conteudo.="<td><img src=%_IMG_LOGO_% border=0></td>\n";
		$conteudo.="<td align=center>\n<H3>%_TITULO_%</h3><br>\n<p><b>%_POP_%</b><br>\n</td>";
	$conteudo.="</tr>\n</table>";
	$arquivo=fopen($template[dir].$nome."_header.tpl", "w");
	fwrite($arquivo, $conteudo);
	fclose($arquivo);
	
	#detalhe
	$dt=0;
	//$align=0;
	$conteudo="<tr bgcolor=#ffffff>";
	$detalhe="";
	foreach ($matCabecalho as $cab) {
		#width=60%
		$align= ($matAlinhamento[$dt] ? $matAlinhamento[$dt]: "left");
		$larg=  ($matLargura[$dt] ? $matLargura[$dt] : "");
		
		$detalhe.="<td class=normal10 align=".$align." width=".$larg.">%_".$dt++."_%</td>\n";
	}
	$conteudo.=$detalhe."</tr>";
	$arquivo=fopen($template[dir].$nome."_detalhe.tpl", "w");
	fwrite($arquivo, $conteudo);
	fclose($arquivo);
	
	#template
	$qtd=count($matCabecalho);
	$conteudo ="<html>\n<body>%_CABECALHO_%";
	$conteudo.="<table width=100% cellpadding=2 cellspacing=1>";
	$conteudo.="<tr><td colspan=$qtd><hr></td>\n</tr>\n";
	$conteudo.="<tr>".$detalhe."</tr>";
	$conteudo.="<tr><td colspan=$qtd><hr></td>\n</tr>";
	$conteudo.="%_DETALHE_%";
	$conteudo.="</table></body></html>\n";
	$arquivo=fopen($template[dir].$nome.".tpl", "w");
	fwrite($arquivo, $conteudo);
	fclose($arquivo);
}


function getSelectCampos($campos, $nome, $size='0', $multiple = ' multiple="multiple"') {
	$select="<select".$multiple." size=\"$size\" name=\"$nome\"> ";	
	
	if (is_array($campos)) {
		foreach ($campos as $coluna) {
			$partes=explode("=", $coluna);
			$select.="<option value=\"".stripslashes($coluna)."\">".$partes[1]."</option>\n";
		}
	}
	$select.="</select>\n";
	return $select;
}

/**
 * Cria um combo box select de acordo com a lista de objetos. 
 *
 * @param string $nome
 * @param string $lista
 * @param string $label
 * @param string $valor
 * @param string $default
 * @param string $vazio em caso de lista estar vazio, exibe uma opcao com este texto
 * @return string
 */
function getSelectObjetos( $nome, $lista, $label="nome", $valor = "id", $default="", $evento="", $selecione=false, $vazio="", $classe='' ){
	$class = ( ( $classe ) ?  ' class="'.$classe.'"' : '' );
	$select = "<select name=\"$nome\" " . $evento . $class . ">\n";
	
	if ( $selecione ){
		$select .=  "<option value=\"0\" > [Selecione] </option>\n";
	}
	
	if(is_array($lista) && count($lista)){
		foreach ( $lista as $obj ){
			$selected = ( $obj->$valor == $default ) ? ' selected="selected"' : "";
			$select .=  "<option value=\"" . $obj->$valor . '"' . $selected . ">" . $obj->$label . "</option>\n";
		}
	}
	else{
		$select .=  "<option value=\"0\">" .$vazio. "</option>\n";
	}
	$select.="</select>\n";
	return $select;
}

/**
*
*
*/
function getComboCamposClientes($comLinha="1") {
	$campos=getCamposClientes();
	#monta o select
	$select=getSelectCampos($campos, 'matriz[campos][]');
	if ($comLinha) {
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Cadastro de Clientes:</b><br>
			<span class=normal10>Selecione os campos que irão constar no relatório</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm($select, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
	} else {
		return $select;
	}
}


/**
*
*
*/
function getComboCamposGruposServico($comLinha="1") {
	
	$campos=getCamposGrupoServico();
	#monta o select
	$select=getSelectCampos($campos);
	
	if ($comLinha) {
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Grupos de Serviço:</b><br>
			<span class=normal10>Selecione os campos de Grupos_Servico que irão constar no relatório</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm($select, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
	} else {
		return $select;
	}
}


#Exibe em tela o label e a lista com os GruposServicos
function getComboGrupoServico($matriz) {
	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL('<b>Grupo:</b><br><span class=normal10>Selecione o(s) grupo(s) de serviços</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		if($matriz[grupos_todos]) $opcServico='checked';
		$texto2="<input type=checkbox name=matriz[grupos_todos] value=S $opcServico><b>Todos</b>";
		itemLinhaForm(formSelectGruposServicos($matriz[grupos],'idGrupos','multi').$texto2, 'left', 'top', $corFundo, 0, 'tabfundo1');		
	fechaLinhaTabela();
}

# retorna a sentenca sql WHERE para o GrupoServico
function getSQLGrupoServicos($matriz) {
	
	global $tb;
	/*	Se forem todos os grupos gera a lista na matriz	*/
	if($matriz[grupos_todos]) {
		$consultaGrupo=buscaGruposServicos('', '', 'todos', 'id');
		if( $consultaGrupo && contaconsulta($consultaGrupo) ) {
			for($a=0;$a<contaConsulta($consultaGrupo);$a++) {
				$matriz['idGrupos'][$a]=resultadoSQL($consultaGrupo, $a, 'id');
			}
		}
	}
	
	$cg=0;
	$sqlGRUPO="AND $tb[ServicosGrupos].idGrupos in (";
		
	while ($matriz['idGrupos'][$cg]) {
		//zera matriz de totais dos grupos
		$grupoRec[$matriz['idGrupos'][$cg]]=0;
		$grupoFat[$matriz['idGrupos'][$cg]]=0;
		
		if ($cg>0) $sqlGRUPO.=", ";
		$sqlGRUPO.=$matriz['idGrupos'][$cg++];
	}
	
	if ($cg<=0) $sqlGRUPO="";
	else $sqlGRUPO.=") ";
		
	return $sqlGRUPO;	
}

function setStatusOcorrencias() {
	$campos[]="N=Novo";
	$campos[]="A=Aberto";
	$campos[]="F=Fechado";
	$campos[]="C=Cancelado";
	$campos[]="R=Re-Aberto";
	return $campos;
}

function getSelectStatusOcorrencias($campos, $nome, $size='0') {
	$select="<select multiple size=$size name=$nome>";		
	if (is_array($campos)) {
		foreach ($campos as $coluna) {
			$partes=explode("=", $coluna);
			$select.="<option value=".$partes[0].">".$partes[1]."\n";
		}
	}
	$select.="</select>";
	return $select;
}

function getStatusOcorrencias() {
	$campos= setStatusOcorrencias();
	$selectSituacao=getSelectStatusOcorrencias($campos,'matriz[status][]');
	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL('<b>Situação:</b><br>
		<span class=normal10>Selecione o Situação da Ocorrência</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		if($matriz[status_todos]) $opcStatus='checked';
		$texto="<input type=checkbox name=matriz[status_todos] value='S' $opcStatus><b>Todos</b>";
		itemLinhaForm($selectSituacao.$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
}

/**
  *@desc retorna campos de um formulario html
 */
function getCampoForm($tipo, $label, $name="", $value="", $event="", $forma="form", $tamanho=60) {
	global $corFundo, $corBorda, $html;

	switch ($tipo) {
		case 'text': 
			$texto="<input type=text name=matriz[$name] size=$tamanho value='$value' $event>";
			break;
		case 'status': 
			$texto=getComboStatus($value, $name, $forma);
			break;
		case 'hidden':
			$texto="<input type=hidden name=$name value=$value>".$value;
			break;
		case 'area':
			$texto="<textarea name=$name cols=$tamanho rows=6>$value</textarea>";
			break;
		case 'combo':
			$texto=$value;
			break;
		case'botao':
			$texto="<input type=submit name=matriz[$name] value='$value'>";
			break;
			
	}
	return ($texto);
}


/**
 * @author gustavo
 * gera um select, como a funcao getSelectCampo, porem com values e  names corretos.
 * nao sendo alterado na funcao original, para nao causar efeitos colaterais uma vez que este esta sendo
 * chamado de varios pontos do sistema...
 *
 */
function getSelectNovo($campos, $nome, $size='0', $multiple = 1, $default='') {
	$select="<select ";
	if ($multiple)	$select .= "multiple ";
	$select .= 	 "size=$size name=$nome> ";	
	
	if (is_array($campos)) {
		foreach ($campos as $coluna) {
			$partes=explode("=", $coluna);
			$c = ( ($partes[0] == $default) ? 'selected' : '');
			$select.="<option value=".$partes[0]." ".$c.">".$partes[1]."\n";
		}
	}
	$select.="</select>";
	return $select;
}



/**
 * @author sombra
 *retorna duas caixas de textos para datas de inicio e fim 
 */
function getPeriodoDatas ($matriz, $pos1, $pos2) {
	$retorno = "À partir de: ";
	$retorno .= getCampoForm('text', '', 'dtInicio', $matriz[dtInicio], "onBlur=verificaData(this.value,$pos1)",'' , 8 );
	$retorno .= " até: ";
	$retorno .= getCampoForm('text', '', 'dtFim', $matriz[dtFim], "onBlur=verificaData(this.value,$pos2)", '', 9);
	$retorno .= getCampoForm('botao', '', 'filtroPeriodo', "Ir' class='submit");
	
	return ($retorno);
}

/**
 * @author louco
 * @desc exibe uma listagem do conteudo $tabela[detalhe] , de acordo com a configuracoes da mesma
 * $tabela[exibe][filtros] -> determina se deve exibir filtros padroes
 * $tabela[exibe][filtros][pop]-> determina se deve exibir o filtro de pop 
 * $tabela[exibe][filtros][tipoData]-> determina se deve exibir o filtro de tipo de data
 * $tabela[exibe][filtros][planos]-> determina se deve exibir o filtro de planos de Contas existentes
 * 
 * $tabela[exibe][filtros][datas]-> determina se deve exibir o filtro de datas pre definidas
 * $tabela[exibe][filtros][periodo]-> determina se deve exibir o filtro de entrada para a digitacao de 2 dtas
 * 
 * $tabela[exibe]gravata]-> determina as colunas a serem exibidas
 * $tabela[tamanho] -> determina os tamnhos das colunas
 * $tabela[alinhamento] -> determina o alinhamento das colunas
 * $tabela[formatos] -> determina a formatacao de cada coluna
 * $$tabela[detalhe] -> determina as linhas/colunas a serem exibidas
 */
function exibeNovaTabela ($tabela, $modulo, $sub, $acao, $registro, $matriz) {
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessPlanos, $conn, $tb;

	novaTabela("[ ".$tabela[titulo]." ]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, count($tabela[gravata]));	

	htmlAbreLinha($corFundo);
	htmlAbreColuna("100%","center", $corFundo, count($tabela[gravata]), 'tabfundo1');
	
	novaTabelaSH("center", "100%", 0 , 0, 0,$corFundo, $corBorda,  2 );

	if ($tabela[exibe][filtros]){
		
		$form="<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>";
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL($form, 'midle','left', '', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		$ncampo=3;			
		novaLinhaTabela($corFundo, '100%');
			
			//filtro dos pops
			if ($tabela[exibe][filtros][pop] != -1 ){
				$adicional="<option value=''>Selecione um POP:\n";
				$pop = formSelectPOP($matriz[pop], 'pop', 'form', $adicional);
				$ncampo++;
				$pop .=  getCampoForm('botao', '', 'filtroPOP', "Ir' class='submit");
				$ncampo++;
				itemLinhaNOURL($pop, 'center', $corFundo, 0, 'tabfundo1');
			}

			//filtro tiposdatas
			if ($tabela[exibe][filtros][tipoData] != -1){		
//				$opcDe = ( ($matriz[tipoData] == "dtCadastro" ) ?  "checked" : "") ;
//				$datas = "<input id=dtCad type=radio name=matriz[tipoData] value=dtCadastro $opcDe onChange=javascript:form.submit()><label for=dtCad>Data de Cadastro </label>";
//				$ncampo++;
//				
//				(($matriz[tipoData] != "dtCadastro") &&  ($matriz[tipoData] != "dtBaixa") ?  $opcDe = "checked" : $opcDe = "") ;
//				$datas .= "<input id=dtVenc type=radio name=matriz[tipoData] value=dtVencimento $opcDe onChange=javascript:form.submit()><label for=dtVenc>Data de Vencimento </label>";
//				$ncampo++;
//				
//				(($matriz[tipoData] == "dtBaixa") ?  $opcDe = "checked" : $opcDe = "") ;
//				$datas .= "<input id=dtBaixa type=radio name=matriz[tipoData] value=dtBaixa $opcDe onChange=javascript:form.submit()><label for=dtBaixa>Data de Baixa </label>";
//				$ncampo++;		

				$valores = array('dtCadastro', 'dtVencimento', 'dtBaixa');
				$labels  = array('Data de Cadastro', 'Data de Vencimento', 'Data de Baixa');
				
				$datas .= getRadioArray('matriz[tipoData]', $valores, $labels, 
										($matriz['tipoData'] ? $matriz['tipoData'] : "dtVencimento" ), 
										'onchange="javascript:form.submit()"', "");
				$ncampo += count($valores);
				
				itemLinhaNOURL($datas , 'right', $corFundo, 0, 'tabfundo1');
			}
			
			
		fechaLinhaTabela();
				
		if($tabela[exibe][filtros][datas] != -1 && $tabela[exibe][filtros][periodo] != -1){
			novaLinhaTabela($corFundo, '100%');
				$def = "<a href=?modulo=$modulo&sub=$sub&acao=$acao&";
				
				$opcoes  = getSelectNovo(array('Pessoas=Fornecedor:'), 'matriz[filtroPor]', 0, 0);
				$ncampo++;
				$opcoes .= getCampoForm('text', '', 'filtroCom', $matriz['filtroCom'], '', '', 23);
				$ncampo++; 
				$opcoes .= getCampoForm('botao', '', 'filtroCustom', "Ir' class='submit");
				$ncampo++;
				itemLinhaNOURL($opcoes, 'right', $corFundo, 0, 'tabfundo1');
				
				$periodo = getPeriodoDatas($matriz, $ncampo++, $ncampo++);	
				itemLinhaNOURL($periodo, 'right', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		}

		//planos Contas
		if ( $tabela['exibe']['filtros']['planos'] == 1 || $tabela['exibe']['filtros']['periodo'] == 1 ){
			novaLinhaTabela( $corFundo, '100%' );
			if( $tabela['exibe']['filtros']['planos'] == 1 ) {
				$planos ='Plano de Contas: ' . getPlanoDeContas( 'planoPai', $matriz['planoPai'], $tipo='form', $size=0 );
				$ncampo++;
				$planos .= $retorno .= getCampoForm('botao', '', 'filtroPlano', "Ir' class='submit");
				$ncampo++;
				itemLinhaNOURL( $planos , 'right', $corFundo, 0, 'tabfundo1' );
			}
			else {
				itemLinhaNOURL( "&nbsp;" , 'right', $corFundo, 0, 'tabfundo1' );
			}
			if( $tabela['exibe']['filtros']['periodo'] == 1 ) {
				
				$uteis .= getRadioArray('matriz[periodo]', array('todos', 'mes', 'semana', 'hoje'), array('Todos', 'Mês', 'Semana', 'Hoje'), 
										($matriz['periodo'] ? $matriz['periodo'] : 'todos'), 
										'onchange="javascript:form.submit()"');
				$ncampo++;

				itemLinhaNOURL( $uteis, 'right', $corFundo, 0, 'tabfundo1' );
			}
			else {
				itemLinhaNOURL( "&nbsp;" , 'right', $corFundo, 0, 'tabfundo1' );
			}
			fechaLinhaTabela();
		}
//		if ( $tabela['exibe']['filtros']['status'] == 1 ){	
//			novaLinhaTabela( $corFundo, '100%' );
//			$status = getRadioArray('matriz[status]', array('P', 'B', 'C', 'T'),									 
//									array('Pendentes', 'Baixados', 'Cancelados', 'Todos'),
//									($matriz['status'] ? $matriz['status'] : 'P' ), 
//									'onchange="javascript:form.submit()"');
//			$ncampo++;
//			itemLinhaNOURL( $status, 'right', $corFundo, 2, 'tabfundo1' );
//			fechaLinhaTabela();
//		}
			
	}//fim dos filtros
	fechaTabela();
	htmlFechaColuna();
	htmlFechaLinha();	

	if ($tabela[exibe][subMenu]){

		if ($tabela[exibe][menuOpc]) {
			itemTabelaNOURL($tabela[exibe][menuOpc], 'right', $corFundo, count( $tabela[gravata] ), 'tabfundo1');
		}
		else {
			menuOpcAdicional( $modulo, $sub, $acao, $registro, $matriz, count($tabela[gravata]) );
//			$uteis = getRadioArray('matriz[status]', array('P', 'B', 'C', 'T'),									 
//									array('Pendentes', 'Baixados', 'Cancelados', 'Todos'),
//									($matriz['status'] ? $matriz['status'] : 'P' ), 
//									'onchange="javascript:form.submit()"');
//			$ncampo++;
//			itemLinhaNOURL( $uteis, 'right', $corFundo, 6, 'tabfundo1' );
		}
	}
	
	
	if (count($tabela[detalhe]) > 0){
		//Cabeçalho
		novaLinhaTabela( $corFundo, '100%' );
			for ( $x = 0; $x < count( $tabela[gravata] ); $x++ )
				itemLinhaTMNOURL( $tabela[gravata][$x], 'center', 'middle', $tabela[tamanho][$x], $corFundo, 0, 'tabfundo0');
		fechaLinhaTabela();
	
		//corpo
		foreach($tabela[detalhe] as $linha){
			novaLinhaTabela($corFundo, '100%');
			$c=0;
			$col=1;
			foreach($linha as $campo){
				
				if ($col == $tabela[exibe][total]){
					$valorTotal += $campo;
				}
				itemLinhaTabela(getCampoFormatado($campo, $tabela[formatos][$c],'',$modulo, $sub, $linha, $registro, $acao), $tabela[alinhamento][$c], $tabela[tamanho][$c++], 'normal10');
				$col++;
			}	
			fechaLinhaTabela();
		}
	}
	else{
			novaLinhaTabela($corFundo, '100%');
				$texto = '<span class=txtaviso> Nenhum registro encontrado! </span>';
				itemLinhaTMNOURL($texto, 'center', 'midle', '100%', '', count($tabela[gravata]), 'tabfundo1') ;
	}	
	
	//total
	if ($tabela[exibe][total]){
		novaLinhaTabela($corFundo, '100%');		$c=0;
			itemLinhaForm("Total:", 'right', 'midle', $corFundo, $tabela[exibe][total] -1, 'tabfundo0');
			itemLinhaForm(formatarValoresForm($valorTotal), 'center', 'midle', 'txtok', $corFundo, 0, 'tabfundo0');
			itemLinhaForm('&nbsp;', 'center', 'middle', $corFundo, count($tabela[gravata]) -  $tabela[exibe][total], 'tabfundo0');
		fechaLinhaTabela();	
	}
	

	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL(fechaFormulario(), 'midle', 'left', '', $corFundo, count($tabela['gravata']), 'tabfundo1');
	fechaLinhaTabela();
		
	fechaTabela();
}


function exibeFormulario ($tabela, $tipo, $modulo, $sub, $acao, $registro, $registro2='') { //alterar para exibir formulairos tb
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	$c = ( isset($tabela['indice']) ? $tabela['indice'] : 6 ); //"indice dos campos"
	
	#exibe o titulo
	if ($tabela[exibe][titulo]){
			novaTabela2("[ ". $tabela[titulo] ." ]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				novaLinhaTabela($corFundo, '100%');
				$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro value=$registro>
				<input type=hidden name=registro2 value=$registro2>
				";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
	}
	if($tabela[exibe][subMenu]){
		novaLinhaTabela($corFundo, '100%');
			$texto = menuOpcAdicional( $modulo, $sub, $acao, $registro, $matriz, 2 );
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
	}
	

	
	#exibe formulario
	for ($i=0; $i<count($tabela[gravata]); $i++){
		if($tabela[formulario])
			getCampo($tabela[formatos][$i], $tabela[gravata][$i], 'matriz['.$tabela[campos][$i].']', $tabela[valores][$i], '');
		else
			getCampo('combo', $tabela[gravata][$i], $tabela[campos][$i], getCampoFormatado($tabela[valores][$i], $tabela[formatos][$i], 'matriz['.$tabela[campos][$i].']', 'check', '', '', '', '', $c++), '');
	}
		if ($tabela[exibe][bntConfirmar])
		getBotao('matriz[bntConfirmar]', 'Confirmar');
		
		fechaTabela();

}


function getCampoFormatado ($valor='', $tipo, $campo='', $modulo='', $sub='', $linha='', $registro='', $acao='', $idc='') {

	switch ($tipo) {
		case 'data':
			$retorno = converteData($valor, 'banco', 'formdata');
			break;
			
		case 'dataCampo':
			$retorno = getInput('text', $campo, $valor, 'onblur="verificaData(this.value,'.$idc.')"', 12);
			break;
			
		case 'moeda':
			$retorno = formatarValoresForm($valor);
			break;

		case 'moedaCampo':
			$retorno = getInput('text', $campo, $valor, 'onblur="formataValor(this.value,'.$idc.')"', '15');
			break;
			
		case 'statusPgto':
			$retorno = formSelectStatusContasReceber($valor, '', 'check');
			break;
		
		case 'statusPgtoPagar':
			$retorno = formSelectStatusContasPagar($valor, '', 'check');
			break;
			
		case 'status':
			$retorno = formSelectStatus($valor, '', 'check');
			break;
			
		case 'fornecedor':
		case 'pessoa':
			$dados = dadosPessoasTipos($valor);
			$retorno = $dados['pessoa']['nome'];
			break;
		
		case 'pop':
			$retorno = formSelectPOP($valor, '', 'check');
			break;
		
		case 'servicosPlanos':
			$dadosServicoPlano = dadosServicoPlano($valor);
			$retorno= getCampoFormatado($dadosServicoPlano[idServico], 'servico');
			break;	
			
		case 'servico':
			$dadosServico = checkServico($valor);
			$retorno = $dadosServico[nome];
			break;
		
		case 'vencimento':
			$retorno = formSelectVencimento($valor, '', 'check');
			break;
			
		case 'opcoes' :
			$retorno = getOpcoesExibeTabela($modulo, $sub, $acao, $linha, $registro);
			break;
			
		case 'areaCampo':
			$retorno = getTextArea( $campo, $valor);
			break;
			
		case 'planoDeContasDetalhes':
			$plano = dbPlanoDeContasDetalhes('', 'consultar', '', "id='".$valor."'");
			$retorno = $plano[0]->nome;
			break;
			
		case 'texto':
		default:
			$retorno = $valor;
			break;
	}
	
	return ($retorno);
}


function getOpcoesExibeTabela ( $modulo, $sub, $acao,  $linha, $registro='') {

	$def = "<a href=?modulo=$modulo&sub=$sub&";
	
	if ($modulo == 'contas_a_pagar'){
		if($linha->status == 'P'){
			$retorno = htmlMontaOpcao($def."acao=info&registro=$linha->id >Detalhes</a>",'info');
			$retorno .= "<br>";
			$retorno .= htmlMontaOpcao($def."acao=baixar&registro=$linha->id >Baixar</a>",'baixar');
			$retorno .= htmlMontaOpcao($def."acao=cancelar&registro=$linha->id >Cancelar</a>",'cancelar');
		}
		elseif($linha->status == 'B' || $linha->status == 'C'){
			$retorno = htmlMontaOpcao($def."acao=info&registro=$linha->id >Dados Pagamento</a>",'info');
		}
	}
		
	elseif($modulo == 'planos_contas' && $linha[id]){
		if ($linha[status] =='A')
			$retorno = htmlMontaOpcao($def."acao=desativar&registro=$linha[id] > Inativar</a>",'cancelar');
		elseif ($linha[status] =='I')
			$retorno = htmlMontaOpcao($def."acao=ativar&registro=$linha[id] > Ativar</a>",'ativar');
	}

	elseif ($sub == 'contraPartidaPadrao'){
		$retorno = htmlMontaOpcao($def."acao=contrapartidapadraover&registro=$registro&registro2=$linha->id >Ver</a>",'relatorio');
		$retorno .= htmlMontaOpcao($def."acao=contrapartidapadraoalterar&registro=$registro&registro2=$linha->id >Alterar</a>",'alterar');
		$retorno .= htmlMontaOpcao($def."acao=contrapartidapadraoexcluir&registro=$registro&registro2=$linha->id >Excluir</a>",'excluir');
		if ($linha->status =='A')
			$retorno .= htmlMontaOpcao($def."acao=contrapartidadesativar&registro=$registro&registro2=$linha->id >Inativar</a>",'desativar');
		elseif ($linha->status =='I')
			$retorno .= htmlMontaOpcao($def."acao=contrapartidaativar&registro=$registro&registro2=$linha->id >Ativar</a>",'ativar');
	}

	elseif (substr($acao, 0, 13) == 'contrapartida'){
		$retorno = htmlMontaOpcao($def."acao=contrapartidaver&registro=$registro&registro2=$linha->id >Ver</a>",'relatorio');
		if ($linha->status != 'C'){
			$retorno .= htmlMontaOpcao($def."acao=contrapartidaalterar&registro=$registro&registro2=$linha->id >Alterar</a>",'alterar');
			$retorno .= "<br>".htmlMontaOpcao($def."acao=contrapartidacancelar&registro=$registro&registro2=$linha->id >Cancelar</a>",'cancelar');
			if ($linha->status =='A')
				$retorno .= htmlMontaOpcao($def."acao=contrapartidadesativar&registro=$registro&registro2=$linha->id >Inativar</a>",'desativar');
			elseif ($linha->status =='I')
				$retorno .= htmlMontaOpcao($def."acao=contrapartidaativar&registro=$registro&registro2=$linha->id >Ativar</a>",'ativar');
		}	
	}
	elseif ($sub == 'tiposImpostos'){
		$retorno = htmlMontaOpcao($def."acao=ver&registro=".$linha->id." >Ver</a>",'relatorio');
		$retorno .= htmlMontaOpcao($def."acao=alterar&registro=".$linha->id." >Alterar</a>",'alterar');
		$retorno .= htmlMontaOpcao($def."acao=excluir&registro=".$linha->id." >Excluir</a>",'excluir');
	}
	elseif (strstr($acao, 'impostosPessoas' ) ){
		$retorno = htmlMontaOpcao($def."acao=impostosPessoasAlterar&registro=".$linha->id." >Alterar</a>",'alterar');
		$retorno .= htmlMontaOpcao($def."acao=impostosPessoasExcluir&registro=".$linha->id." >Excluir</a>",'excluir');
	}else "echo $acao";
	

	return ($retorno);
}

function getStatusCheckList ($matriz='') {
		$consStatus = buscaStatusServicos('','','todos','status');
			
		$texto = array();
		for ($i=0;$i<contaConsulta($consStatus); $i++){
			$id = resultadoSQL($consStatus,$i,'id');
			$descricao = resultadoSQL($consStatus,$i,'descricao');
			$cobranca = resultadoSQL($consStatus, $i, 'cobranca');
			
			if(is_array($matriz['status'])) $check = ( in_array($id, $matriz['status']) ? 'checked' : '');
			$texto[] = "<input id=$id type=checkbox name=matriz[status][] value=$id $check>" .
					   "<label for=$id>$descricao (cobrança: $cobranca)</label>" ;
		}
		
		return($texto);
	
}

/**
 * Retorna somente o botão
 *
 * @param unknown_type $name
 * @param unknown_type $value
 * @param unknown_type $class
 * @param unknown_type $tipo
 * @param unknown_type $evento
 * @return unknown
 */
function getSubmit($name, $value, $class='submit', $tipo='submit', $evento='') {
	
	$texto='<input type="'.$tipo.'" name="'.$name.'" value="'.$value.'" class="'.$class.'"'.$evento." />\n";
	return $texto;

}


/**
 * Retorna um combo com o padrao de tipos de tributos.
 *
 * @param unknown_type $selected
 */
function getComboTiposTributos( $campo='matriz[tipoTributo]', $selected = '' , $tipo = 'html'){
	global $TRIBUTOS;
	if( $tipo == 'html' ){
		$campo = getComboArray( $campo, $TRIBUTOS['valores'], $TRIBUTOS['tipos'], $selected );
	}
	else{
		$indice = array_key_exists( $TRIBUTOS['tipos'], $selected );
		$campo = $TRIBUTOS['valores'][$indice[0]];
	}
	
	return $campo;
}

/**
 * Cria arquivo CSV e grava conteudo
 *
 * @param string $nome
 * @param string $conteudo
 */
function criaArquivoCSV ( $nome, $conteudo ){
		
	$arquivoCSV = fopen( $nome, "w" );
	fwrite( $arquivoCSV, $conteudo );
	fclose( $arquivoCSV );
}

?>