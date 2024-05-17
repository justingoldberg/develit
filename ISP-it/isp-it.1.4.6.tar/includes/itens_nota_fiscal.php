<?
################################################################################
#       Criado por: Rogério aka Popó
#  Data de criação: 28/01/2005
# Ultima alteração: 28/01/2005
#    Alteração No.: 003
#
# Função:
# Itens Nota Fiscal - Funções para criação, alteração e impressão de nota fiscal
################################################################################

function itensNotaFiscal ( $modulo, $sub, $acao, $registro, $matriz ){
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessPlanos;

	if ( $acao == 'incluirItem' ){
		$idNF = $matriz['idNota'];
	}
	else{
		$dadosItens = dbItensNF('custom', $registro );
		$idNF = $dadosItens[0]->idNF;
	}

	if ( $acao != 'verSemNota' ){
		notaFiscal( 'notafiscal', 'notafiscal', 'verSemItem', $idNF, $matriz );
	}

	if ( $acao == "alterarItem" ){
		if ( $matriz['bntIncluirItem'] == 'Alterar' )
		dbItensNF( 'incluir', $registro, '', $matriz );
		else{
			$matriz['formItem'] = $dadosItens;
			$matriz['botao'] = 'Alterar';
		}
		mostrarItensNF( $modulo, $sub, 'ver', $idNF, $matriz );
		// AGUARDANDO DETALHES SOBRE OS DESCONTOS DE NOTA HARD CODE POR AGORA!!!
		//mostrarDescontosNF( 'descontosnotafiscal', 'descontos', 'ver', $idNF, $matriz );
	}
	elseif ( $acao == "excluirItem" ){
		dbItensNF( 'excluir', $registro );
		//$matriz['formItem'] = $dadosItens;
		mostrarItensNF( $modulo, $sub, 'ver', $idNF , $matriz );
	}
	elseif ( $acao == "procurar" ){
		//notaFiscalProcurar( $modulo, $sub, $acao, $registro, $matriz );
	}
	elseif ( substr( $acao, 0, 6 ) == "listar" ){
		//notaFiscalListar( $modulo, $sub, $acao, $registro, $matriz );

	}
	elseif ( $acao == "ver" ){
		//mostrarItensNF( $modulo, $sub, $acao, $registro, $matriz);
	}
	elseif ( $acao == "incluirItem"){

		dbItensNF( 'incluir', '', '', $matriz );
		// VERIFICANDO ....
		//mostrarItensNF( 'descontosnotafiscal', 'descontos', 'ver', $matriz['idNota'], $matriz );
		mostrarItensNF( 'itensnotafiscal', 'itens', 'ver', $matriz['idNota'], $matriz );

	}


} // fim da funcao itensNotaFiscal

// ROTINAS DOS ITENS DE NOTA FISCAL \\

# mostra itens

function mostrarItensNF( $modulo, $sub, $acao, $registro, $matriz ){
	global $corFundo, $corBorda;

	$corGravata = 'tabfundo0';
	$corDetalhe = 'tabfundo1';
	$gravata = array( "Qtde", "Descrição dos Serviços", "Unitário", "Total", "Opções" );
	$largura = array( "5%", "45%", "10%", "10%", "20%" );
	$alinhamento = array( "center", "left", "right", "right", "center");

	if ( $acao != 'ver' && $acao != 'verSemNota' && $acao != 'listarItens' ){
		$dadosItem = dbItensNF( 'custom', $registro );
		$matriz['formItem'] = $dadosItem;
		$registro = $dadosItem[0]->idNF;
	}
	else{
		$texto = "<form method=post name=matriz action=index.php>
				  <input type=hidden name=registro value=$registro>";
		novaLinhaTabela( $corBorda, '100%');
		itemLinhaTMNOURL( $texto, 'left', 'middle', '100%', $corFundo, 5, $corDetalhe);
		fechaLinhaTabela();
	}
	
	$itens = dbItensNF('listar', '', $registro );

	if($acao != 'listarItens'){
		formItens( $modulo, $sub, $acao, $registro, $matriz );
	}
	
	novaTabela2("[Itens Nota Fiscal]",'center', '100%', 0, 2, 1, $corFundo, $corBorda, 5);

	novaLinhaTabela( $corFundo, '100%');
	for ($x = 0; $x < count( $largura ); $x++ )
	itemLinhaTMNOURL($gravata[$x], 'center', 'middle', $largura[$x], $corFundo, 0, $corGravata );
	fechaLinhaTabela();
	/*
	if ( $acao != 'ver' && $acao != 'verSemNota' && $acao != 'listarItens' ){
		$dadosItem = dbItensNF( 'custom', $registro );
		$matriz['formItem'] = $dadosItem;
		$registro = $dadosItem[0]->idNF;
	}
	else{
		$texto = "<form method=post name=matriz action=index.php>
				  <input type=hidden name=registro value=$registro>";
		novaLinhaTabela( $corBorda, '100%');
		itemLinhaTMNOURL( $texto, 'left', 'middle', '100%', $corFundo, 5, $corDetalhe);
		fechaLinhaTabela();
	}
	
	$itens = dbItensNF('listar', '', $registro );

	if($acao != 'listarItens'){
		formItens( $modulo, $sub, $acao, $registro, $matriz );
	}
	*/

	if (!empty( $itens ) ){
		$cc = 0;
		$total=0;
		foreach ( $itens as $item ){

			#opcoes
			$opcoes = '';
			if($acao != 'listarItens' ){
				$def="<a href=?modulo=itensnotafiscal&sub=itens&registro=$item->id&status=$_REQUEST[status]";
				$fnt="<font size='2'>";
				$opcoes =htmlMontaOpcao($def."&acao=alterarItem>".$fnt."Alterar</font></a>",'alterar');
				$opcoes .=htmlMontaOpcao($def."&acao=excluirItem>".$fnt."Excluir</font></a>",'excluir');
			}

			novaLinhaTabela( $corFundo, '100%');
			itemLinhaTMNOURL( $item->qtde, $alinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $corDetalhe );
			itemLinhaTMNOURL( $item->descricao, $alinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $corDetalhe );
			itemLinhaTMNOURL( number_format( $item->valorUnit, 2,',','.' ), $alinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $corDetalhe );
			itemLinhaTMNOURL( number_format( ( $item->qtde * $item->valorUnit), 2, ',','.' ), $alinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $corDetalhe );
			itemLinhaTMNOURL( $opcoes, $alinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $corDetalhe );
			fechaLinhaTabela();
			$cc = 0;
			$total += $item->qtde * $item->valorUnit;
		}
		//exibe o tottal
		novaLinhaTabela( $corFundo, '100%');
		itemLinhaTMNOURL( 'Valor Total da Nota:', 'right', 'middle', '', $corFundo, 3, $corGravata);
		itemLinhaTMNOURL( number_format($total, 2,',','.' ) , 'right', 'middle', '', $corFundo, 1, $corGravata);
		itemLinhaTMNOURL( '&nbsp;' , 'center', 'middle', '', $corFundo, 1, $corGravata);
		fechaLinhaTabela();
	}
	else{
		novaLinhaTabela( $corFundo, '100%');
		itemLinhaTMNOURL("Nenhum Item Cadastrado", 'center', 'middle', '5%', $corFundo, 5, $corDetalhe );
		fechaLinhaTabela();
	}
	fechaTabela();
}

/*function mostrarItensNF( $modulo, $sub, $acao, $registro, $matriz ){
	global $corFundo, $corBorda;

	$corGravata = 'tabfundo0';
	$corDetalhe = 'tabfundo1';
	$gravata = array( "Qtde", "Descrição dos Serviços", "Unitário", "Total", "Opções" );
	$largura = array( "5%", "45%", "10%", "10%", "20%" );
	$alinhamento = array( "center", "left", "right", "right", "center");

	echo "<br>";
	
	novaLinhaTabela( $corFundo, '100%');
	novaTabela2('[Itens Nota Fiscal]','center','100%',0,0,1,$corFundo, $corBorda, 5);
	fechaLinhaTabela();
	
	novaLinhaTabela($corFundo, '100%');
	for ($x = 0; $x < count( $largura ); $x++ )
	itemLinhaTMNOURL($gravata[$x], 'center', 'middle', $largura[$x], $corFundo, 0, $corGravata );
	fechaLinhaTabela();

	if ( $acao != 'ver' && $acao != 'verSemNota' && $acao != 'listarItens' ){
		$dadosItem = dbItensNF( 'custom', $registro );
		$matriz['formItem'] = $dadosItem;
		$registro = $dadosItem[0]->idNF;
	}
	else{
		$texto = "<form method=post name=matriz action=index.php>
				  <input type=hidden name=registro value=$registro>";
		novaLinhaTabela( $corBorda, '100%');
		itemLinhaTMNOURL( $texto, 'left', 'middle', '100%', $corFundo, 5, $corDetalhe);
		fechaLinhaTabela();
	}
	$itens = dbItensNF('listar', '', $registro );

	if($acao != 'listarItens'){
		formItens( $modulo, $sub, $acao, $registro, $matriz );
	}

	if (!empty( $itens ) ){
		$cc = 0;
		$total=0;
		foreach ( $itens as $item ){

			#opcoes
			$opcoes = '';
			if($acao != 'listarItens' ){
				$def="<a href=?modulo=itensnotafiscal&sub=itens&registro=$item->id&status=$_REQUEST[status]";
				$fnt="<font size='2'>";
				$opcoes =htmlMontaOpcao($def."&acao=alterarItem>".$fnt."Alterar</font></a>",'alterar');
				$opcoes .=htmlMontaOpcao($def."&acao=excluirItem>".$fnt."Excluir</font></a>",'excluir');
			}

			novaLinhaTabela( $nscorFundo, '100%');
			itemLinhaTMNOURL( $item->qtde, $alinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $corDetalhe );
			itemLinhaTMNOURL( $item->descricao, $alinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $corDetalhe );
			itemLinhaTMNOURL( number_format( $itlterar'){
		$botao = 'Alterar';
		$acao = 'alterarItem';
		$modulo = 'itensnotafiscal';
		$sub = 'itens';em->valorUnit, 2,',','.' ), $alinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $corDetalhe );
			itemLinhaTMNOURL( number_format( ( $item->qtde * $item->valorUnit), 2, ',','.' ), $alinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $corDetalhe );
			itemLinhaTMNOURL( $opcoes, $alinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $corDetalhe );
			fechaLinhaTabela();
			$cc = 0;
			$total += $item->qtde * $item->valorUnit;
		}
		//exibe o tottal
		novaLinhaTabela( $corFundo, '100%');
		itemLinhaTMNOURL( 'Valor Total da Nota:', 'right', 'middle', '', $corFundo, 3, $corGravata);
		itemLinhaTMNOURL( number_format($total, 2,',','.' ) , 'right', 'middle', '', $corFundo, 1, $corGravata);
		itemLinhaTMNOURL( '&nbsp;' , 'center', 'middle', '', $corFundo, 1, $corGravata);
		fechaLinhaTabela();
	}
	else{
		novaLinhaTabela( $corFundo, '100%');
		itemLinhaTMNOURL("Nenhum Item Cadastrado", 'center', 'middle', '5%', $corFundo, 5, $corDetalhe );
		fechaLinhaTabela();
	}
	fechaTabela();
}*/

# inclusao de itens


function formItens($modulo, $sub, $acao, $registro, $matriz){
	
	global $corFundo, $corBorda, $tb;
	$css_ = 'tabfundo1';

	$largura = array('5%', '45%', '10%', '30%');
	
	$dadosItens = $matriz['formItem'];
		
	$idNF = $dadosItens[0]->idNF;
	
	if($matriz['botao'] == 'Alterar'){
		$botao = 'Alterar';
		$acao = 'alterarItem';
		$modulo = 'itensnotafiscal';
		$sub = 'itens';
	}
	else{
		$botao = 'Incluir';
		$acao = 'incluirItem';
		$modulo = 'itensnotafiscal';
		$sub = 'itens';
		$idNF = $registro;
	}
		
	#Consulta SQL para selecionar um serviço cadastrado
	$matriz['consulta'] = "SELECT $tb[ServicosPlanos].valor, $tb[ServicosPlanos].id, 
				                  $tb[Servicos].nome, $tb[Servicos].descricao,
				                  $tb[Servicos].valor as vl 
				          FROM $tb[ServicosPlanos] 
				          INNER JOIN $tb[Servicos]  
				          ON ($tb[Servicos].id = $tb[ServicosPlanos].idServico)  
	    				  INNER JOIN $tb[PlanosPessoas] 
	    				  ON ($tb[PlanosPessoas].id = $tb[ServicosPlanos].idPlano) 
						  INNER JOIN $tb[NotaFiscal] 
	    				  ON ($tb[NotaFiscal].idPessoaTipo = $tb[PlanosPessoas].idPessoaTipo) 
	    				  WHERE $tb[NotaFiscal].id = $idNF 
	    				  AND $tb[ServicosPlanos].idStatus = 4";
	
	$consulta = dbServicosPlano($matriz, 'consultar');
	
	#montar formulário dos itens da nota
	novaTabela2("[Adicionar Itens à Nota Fiscal]<a name='ancora'></a>", 'center', '100%', 0, 2, 1, $corFundo, $corBorda, 5);
		novaLinhaTabela($corFundo, '100%');
			abreFormularioComCabecalho($modulo, $sub, $acao, $registro);

			$ocultosNomes = array('status', 'matriz[idNota]', 'registro', 'matriz[id]');
			$ocultosValores = array($_REQUEST['status'], $idNF, $dadosItens[0]->id, $matriz['idItem']);
			getCamposOcultos($ocultosNomes, $ocultosValores);
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaForm("<span class='bold10'>Qtde</span>", 'left', 'middle', $corFundo, 0, $css_);
			itemLinhaForm("<span class='bold10'>Descrição do Serviço</span>", 'left', 'middle', $corFundo, 0, $css_);
			itemLinhaForm("<span class='bold10'>Unitário</span>", 'left', 'middle', $corFundo, 0, $css_);
			itemLinhaForm("<span class='bold10'>Total</span>", 'left', 'middle', $corFundo, 0, $css_);
			itemLinhaForm( '&nbsp;', 'left', 'middle', $corFundo, 0, $css_);
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			$campo = "<input type='text' name='matriz[qtde]' id='qtde' size='11' maxlength='10' value='".$dadosItens[0]->qtde."' onBlur='retornaInteiro(this.value, this.name)'";
			itemLinhaForm($campo, 'left', 'middle', $corFundo, 0, $css_);
			$campo = "<input type='text' name='matriz[descricao]' id='descricao' size='45' value='".$dadosItens[0]->descricao."'>&nbsp;";
			
			#testando retorno de consulta
			if(contaConsulta($consulta) > 0){
				$campo .= "<input type='button' name='matriz[bntServico]' id='selectServico' value='Serviço' class='submit' onClick='showdiv(\"divServico\");'>";
				itemLinhaForm($campo, 'left', 'middle', $corFundo, 0, $css_);
			}
			else{
				itemLinhaForm($campo, 'left', 'middle', $corFundo, 0, $css_);
			}
			
			$campo = "<input name='matriz[vlUnit]' id='vlUnit' size='10' maxlength='12' value='".number_format(($dadosItens[0]->valorUnit), 2, ',', '.')."' onBlur=\"formataValor(this.value, this.name); calculaValorTotal(document.forms[0].elements['matriz[qtde]'].value, document.forms[0].elements['matriz[vlUnit]'].value, 'matriz[vlTotal]')\">";
			itemLinhaForm($campo, 'left', 'middle', $corFundo, 0, $css_);
			
			$campo = "<input name='matriz[vlTotal]' id='vlTotal' size='10' maxlength='12' value='".number_format(($dadosItens[0]->qtde*$dadosItens[0]->valorUnit), 2, ',', '.')."' onBlur='formataValor(this.value, this.name)'>";
			itemLinhaForm($campo, 'left', 'middle', $corFundo, 0, $css_);

			$campo = "<input name='matriz[bntIncluirItem]' type='submit' size='10' value='$botao' class='submit'>";
			itemLinhaForm($campo, 'left', 'middle', $corFundo, 5, 'tabfundo1');
		fechaLinhaTabela();
		#### Div com a opçao de inclusão de Serviço ####
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('100%', 'left', $corFundo, 5, 'tabfundo1');
				echo "<div id='divServico' style='display:none;'>";
				novaTabela2SH('left','100%', 0, 2, 0, $corFundo, $corBorda, 1);
					novaLinhaTabela($corfundo, '100%');
						$texto = "<b class='bold10'>Serviço&nbsp;</b>";
						$texto .= FormSelectItemNFSServico($consulta, 'servico');
						$texto .= "&nbsp;<input type='button' name='matriz[okServico]' id='okServico' class='submit' value='OK' onClick='preencheCampo(\"descricao\", selectservico.value, 1); preencheCampo(\"vlUnit\", selectservico.value, 3); hidediv(\"divServico\"); calculaValorTotal(document.forms[0].elements[\"matriz[qtde]\"].value, document.forms[0].elements[\"matriz[vlUnit]\"].value, \"matriz[vlTotal]\")'>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 5, 'tabfundo1');
					fechaLinhaTabela();
				fechaTabela();
			htmlFechaColuna();
		fechaLinhaTabela();
	fechaTabela();
}

# rotina de bd de itens
function dbItensNF( $tipo, $id= '', $idNF = '', $matriz = '' ) {
		
	global $conn, $tb;

	$objItensNF = new ItemNF();
	$objItensNF->ItemNF();

	$objItensNF->setConnection( $conn );

	if ( $id != '' ) $objItensNF->setId( $id );
	else $objItensNF->setId( 0 );

	if ( $idNF != '' ) $objItensNF->setIdNF( $idNF );
	else $objItensNF->setIdNF( 0 );

	if ( $tipo == 'incluir' ){
		$objItensNF->setId( $id );
		$objItensNF->setIdNF( $matriz['idNota'] );
		$objItensNF->setQtde( $matriz['qtde'] );
		$objItensNF->setUnid( $matriz['unid'] );
		$objItensNF->setDescricao( $matriz['descricao'] );
		$objItensNF->setValorUnit( formatarValores($matriz['vlUnit']) );
//		$objItensNF->setValorUnit(ereg_replace(',', '.',$matriz['vlUnit']));

		$objItensNF->salva();
	}
	elseif ( $tipo == 'excluir' ){
		$objItensNF->id = $id;

		$objItensNF->exclui();
	}
	elseif ( $tipo == 'listar' ){
		@mysql_query('passou no listar dos itens');
		$where = array( "$tb[ItensNF].idNF = ".$objItensNF->getidNF() );
		return $objItensNF->seleciona('','',$where);
	}
	elseif ( $tipo == 'custom' ){
		@mysql_query('passou no custom do itens');
		$where = array( "id = '".$id."'" )	;
		return $objItensNF->seleciona('', '', $where );
	}
	elseif ('maxId' ){
		return $objItensNF->getMaxField();
	}
}

// FIM DA ROTINA DE ITENS DE NOTA FISCAL\\

?>