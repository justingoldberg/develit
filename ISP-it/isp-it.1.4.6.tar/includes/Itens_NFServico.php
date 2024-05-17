<?
################################################################################
#       Criado por: Lis
#  Data de criação: 31/05/2007
# Ultima alteração: 31/05/2007
#    Alteração No.: 1
#
# Função:
#    Nota Fiscal Fatura Servico - Funções para gerenciamento de cadastro de Nota Fiscal Fatura de Serviço
################################################################################

/**
 * Gerencia o cadastro de item de Nota Fiscal 
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function ItensNFServico( $modulo, $sub, $acao, $registro, $matriz ){
	global $tb;
	
	if( $matriz['bntConfirmarItem'] || $acao == 'excluir_item' ) {
		if( $acao == 'excluir_item' || ItensNFServicoValida( $matriz, $acao ) ) {
			// verifica as ações para gravar no BD e mostrar a mensagem corretamente
			$subAcao = explode( "_", $acao );
			switch( $subAcao[0] ) {
				case 'alterar':
					$matriz['discriminacao'] = eregi_replace( "\n", " ", $matriz['discriminacao'] );
					$matriz['discriminacao'] = eregi_replace( "\t", " ", $matriz['discriminacao'] );
					$matriz['discriminacao'] = ereg_replace( " +", " ", $matriz['discriminacao'] );	
					$matriz['isento'] = ($matriz['isento'] == 'S' ? $matriz['isento'] : 'N');
					dbItensNFServico( 'incluir', $matriz['id'], '', $matriz );
					$matriz['isento'] = '';
					$matriz['discriminacao'] = '';
					$matriz['valor'] = '';
					break;
				case 'excluir':
					dbItensNFServico( 'excluir', $matriz['id'] );				
					break;
				default:
					$matriz['isento'] = ($matriz['isento'] ? $matriz['isento'] : 'N');	
					$matriz['discriminacao'] = eregi_replace( "\n", " ", $matriz['discriminacao'] );
					$matriz['discriminacao'] = eregi_replace( "\t", " ", $matriz['discriminacao'] );
					$matriz['discriminacao'] = ereg_replace( " +", " ", $matriz['discriminacao'] );					
					dbItensNFServico('incluir','', $registro, $matriz );
					$matriz['isento'] = '';	
					$matriz['discriminacao'] = '';	
					$matriz['bntConfirmarItem'] = '';						
					break;
			}
			$acao = 'novo_item';
		}
		else {
			avisoNOURL("Aviso", "Não foi possível gravar os dados! Verifique se todos os campos foram preenchidos corretamente.", 400);
			echo "<br />";			
		}
	}
	if( $acao == 'alterar_item' ) {
		$consulta = dbItensNFServico( 'custom', $matriz['id'], $registro, $matriz );
		if( count( $consulta ) ) {
			$dados = get_object_vars( $consulta[0] );
			$id = $matriz['id'];
			$matriz = $dados;
			$matriz['idItem'] = $id;
			$matriz['botao'] = 'Alterar';
		}
	}
	NotaFiscalServicoVisualizar( $modulo, $sub, 'ver', $registro, $matriz );	
}


function dbItensNFServico($tipo, $id= '', $idNFS = '', $matriz = '' ) {
	global $conn, $tb;

	$objItensNFS = new ItensNFServico();
	$objItensNFS->ItensNFServico();
	
	$objItensNFS->setConnection( $conn );
	
	if ( $id != '' ) $objItensNFS->setId( $id );
		else $objItensNFS->setId( 0 );
		
	if ( $idNFS != '' ) $objItensNFS->setIdNFS( $idNFS );
		else $objItensNFS->setIdNFS( 0 );
	
	if ( $tipo == 'incluir' ){
		$objItensNFS->setId( $id );
		$objItensNFS->setIdNFS( $matriz['idNFS'] );
		$objItensNFS->setDiscriminacao( $matriz['discriminacao'] );
		$objItensNFS->setValor( ereg_replace( ',', '.', $matriz['valor']) );
		$objItensNFS->setIsento( $matriz['isento'] );
		
		$objItensNFS->salva();
	}
	elseif ( $tipo == 'excluir' ){
		$objItensNFS->id = $id;
		
		$objItensNFS->exclui();
	}
	elseif ( $tipo == 'listar' ){
		$where = array( "$tb[ItensNFServico].idNFS = ".$objItensNFS->getidNFS() );
		return $objItensNFS->seleciona('','',$where);
	}
	elseif ( $tipo == 'custom' ){
		$where = array( "id = '".$id."'" )	;
		return $objItensNFS->seleciona('', '', $where );
	}
	elseif ('maxId' ){
		return $objItensNFS->getMaxField();
	}
}



/**
 * Lista os itens de Nota Fiscal Fatura de Serviço junto ao formulário
 *
 * @param string  $modulo
 * @param string  $sub
 * @param string  $acao
 * @param integer $registro
 * @param array   $matriz
 */
function ItensNFServicoListar( $modulo, $sub, $acao, $registro, $matriz ){
	global $corFundo, $corBorda, $tb;

	$corDetalhe = 'tabfundo1';
	
	$largura 				= array( '10%',	  '45%',						'25%',	  	'20%'  );
	$gravata['cabecalho']   = array( 'Isento','Discriminação do Serviço',	'Valor', 	'Opções' );
	$alinhamento			= array( 'center','left', 						'right',	'center' );
	
	$qtdColunas = count( $largura );
	
	// exibe os dados do cabeçalho da NF
	$itens = dbItensNFServico( 'listar', '', $registro  );
	$totalItens = count( $itens );
	$matriz['id'] = $registro;
	$consulta = dbNotaFiscalServico( $matriz, 'custom');
	$ICMS = $consulta[0]->ICMS;
	if($acao != 'listarItens' && $consulta[0]->status == 'A'){
		ItensNFServicoFormulario( $modulo, $sub, $acao, $registro, $matriz );
	}
	
	novaTabela2( '[Itens da Nota Fiscal Fatura de Serviço]<a name="ancora"></a>', 'center', "100%", 0, 2, 1, $corFundo, $corBorda, $qtdColunas );
		
		if( $totalItens > 0 ) {
			htmlAbreLinha($corFundo);
				for( $i = 0; $i < $qtdColunas; $i++ ){
					itemLinhaTMNOURL( $gravata['cabecalho'][$i], $alinhamento[$i], 'middle', $largura[$i], $corFundo, 0, 'tabfundo0' );
				}
			htmlFechaLinha();
			// valor total da NF
			$vlTotalNF = $vlTotalICMS = 0;
			foreach( $itens as $item ) {
				$cc = 0;
				$def = "<a href=\"?modulo=$modulo&sub=$sub&registro=$registro&matriz[id]=$item->id&matriz[idNFS]=".$registro;
				$fnt = "<font size=\"2\">";
				if( $consulta[0]->status == 'A' ){
				$opcoes =htmlMontaOpcao($def."&acao=alterar_item\">".$fnt."Alterar</font></a>",'alterar');
				$opcoes .=htmlMontaOpcao($def."&acao=excluir_item\">".$fnt."Excluir</font></a>",'excluir');
				}
				$vlTotalICMS += ($item->isento !='S' ? $item->valor :'');
				$vlTotalNF += $item->valor;
				novaLinhaTabela( $corFundo, '100%');
					itemLinhaTMNOURL( ($item->isento =='S'? '<span class="txtaviso">Sim</span>':'<span class="txtok">Não</span>'), $alinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $corDetalhe );
					itemLinhaTMNOURL( $item->discriminacao, $alinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $corDetalhe );
					itemLinhaTMNOURL( number_format( ( $item->valor ), 2, ',','.' ), $alinhamento[$cc], 'middle', 
										$largura[$cc++], $corFundo, 0, $corDetalhe );
					itemLinhaTMNOURL( $opcoes, $alinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $corDetalhe );
				fechaLinhaTabela();
			}
			novaLinhaTabela( $corFundo, '100%');
				itemLinhaTMNOURL( '<b class=bold10>Valor Total da Prestação: </b><span class="txtaviso">R$ '.number_format( $vlTotalNF, 2, ',', '.').'</span>', 'right', 'middle', '', $corFundo, 3, 'tabfundo2' );
				itemLinhaTMNOURL( '&nbsp;', 'right', 'middle', '', $corFundo, 0, 'tabfundo2' );
			fechaLinhaTabela();
			novaLinhaTabela( $corFundo, '100%');
				itemLinhaTMNOURL( '<b>Base de Cálculo do ICMS: </b> <span class="txtok">R$ '.number_format( $vlTotalICMS, 2, ',', '.').'</span>', 'left', 'middle', '', $corFundo, 2, $corDetalhe );
				itemLinhaTMNOURL( '<b>Valor do ICMS: </b><span class="txttrial">R$ '.number_format( $vlTotalICMS*$ICMS/100, 2, ',', '.').'</span>', 'right', 'middle', '', $corFundo, 0, $corDetalhe );					
				itemLinhaTMNOURL( '&nbsp;', 'right', 'middle', '', $corFundo, 0, $corDetalhe );
			fechaLinhaTabela();
		}
		else {
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL( '<span class="txtaviso"><i>Não há itens cadastrados para esta Nota Fiscal Fatura de Serviço.</i></span>', 'left', 'middle', '100%', $corFundo, 4, 'normal10');
			fechaLinhaTabela();
		}		
	fechaTabela();
}


# inclusao de itens
function ItensNFServicoFormulario( $modulo, $sub, $acao, $registro, $matriz ){
	global $corFundo, $corBorda, $tb;
	$css_ = 'tabfundo1';

	$largura = array( "45%", "20%", "20%", "15%" );
	$alinhamento = array( "center", "left", "right", "center");
	
	if ( $matriz['botao'] == 'Alterar' ){
		$botao = 'Alterar';
		$acao = 'alterar_item';
		$modulo = 'nf_faturaservico';
		$sub = '';
		
	}
	else{
		$botao = 'Incluir';
		$acao = 'incluir_item';
		$modulo = 'nf_faturaservico';
		$sub = '';	
		$matriz['idNFS'] = $registro;
	}
	
	$matriz['consulta'] = "SELECT $tb[ServicosPlanos].valor, $tb[ServicosPlanos].id, $tb[Servicos].nome, $tb[Servicos].descricao, $tb[Servicos].valor as vl
							FROM $tb[ServicosPlanos] 
							INNER JOIN $tb[Servicos] 
								ON ($tb[Servicos].id = $tb[ServicosPlanos].idServico)
							INNER JOIN $tb[PlanosPessoas] 
								ON ($tb[PlanosPessoas].id = $tb[ServicosPlanos].idPlano)
							INNER JOIN $tb[NotaFiscalServico] 
								ON ($tb[NotaFiscalServico].idPessoaTipo = $tb[PlanosPessoas].idPessoaTipo)
							WHERE $tb[NotaFiscalServico].id = $matriz[idNFS] 
								AND $tb[ServicosPlanos].idStatus = 4" ;
	$consulta = dbServicosPlano($matriz,'consultar');
	novaTabela2( '[Adicionar Itens à Nota Fiscal Fatura de Serviço]<a name="ancora"></a>', 'center', "100%", 0, 2, 1, $corFundo, $corBorda, 5 );
		novaLinhaTabela($corFundo, '100%');
			abreFormularioComCabecalho( $modulo, $sub, $acao, $registro );

				$ocultosNomes = array( 'status', 'matriz[idNFS]', 'matriz[discriminacao]', 'matriz[id]' );
				$ocultosValores = array( $_REQUEST['status'], $matriz['idNFS'], $matriz['discriminacao'], $matriz['idItem'] );
				getCamposOcultos( $ocultosNomes, $ocultosValores );

				novaLinhaTabela($corFundo, '100%');
					itemLinhaForm('<span class=bold10>  Isento</span>', 'left', 'middle', $corFundo, 0, $css_);
					itemLinhaForm( '<span class=bold10>  Discriminação do Serviço</span>', 'left', 'middle', $corFundo, 0, $css_);
					itemLinhaForm( '&nbsp;', 'left', 'middle', $corFundo, 0, $css_);
					itemLinhaForm( '<span class=bold10>Valor</span>', 'left', 'middle', $corFundo, 0, $css_);
					itemLinhaForm( '&nbsp;', 'left', 'middle', $corFundo, 0, $css_);					
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$checked = ($matriz['isento'] == 'S' ? 'checked' : '');
					$texto = "<input type='checkbox' name='matriz[isento]' value='S' $checked >";
					itemLinhaForm( $texto, 'left', 'middle', $corFundo, 0, $css_);
					$texto = "<input id='discriminacao' type='text' name='matriz[discriminacao]' value='$matriz[discriminacao]' size='50'>";
					itemLinhaForm( $texto, 'left', 'middle', $corFundo, 0, $css_);
					if( contaConsulta($consulta) > 0 ){
						$texto = "<input id=selectServico onClick='showdiv(\"servico\");hidediv(\"descricao\")' type=button name=matriz[bntServico] value='Serviço' class=submit> ";
						$texto.= "&nbsp; <input id=selectDescricao onClick='showdiv(\"descricao\"); hidediv(\"servico\")' type=button name=matriz[bntDescricao] value='Descrição' class=submit>";
					}
					else{
						$texto = '<span class=bold10>  Este cliente não possui nenhum serviço cadastrado.</span>';
					}
					itemLinhaForm( $texto, 'left', 'middle', $corFundo, 0, $css_);					
					$texto = "<input id='valor' type='text' name='matriz[valor]' value='".($matriz['valor']?number_format($matriz['valor'],2,',','.'):"")."' size='10' style='text-align:right' >";
					itemLinhaForm( $texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					$texto = "<input type=submit name=matriz[bntConfirmarItem] value='$botao' class=submit>";
					itemLinhaForm( $texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('100%', 'left', $corFundo, 5, 'tabfundo1');
							echo '<div id="servico" style="display:none;">';
							novaTabela2SH('left','100%',0,2,0,$corFundo,$corBorda,1);
								novaLinhaTabela($corFundo, '100%');
									$texto = "<b class=bold10>Serviço: </b>";
									$texto.= FormSelectItemNFSServico( $consulta, 'servico' );
									$texto.= "&nbsp; <input id=okServico onClick='preencheCampo(\"discriminacao\",selectservico.value, 1); preencheCampo(\"valor\",selectservico.value, 3); hidediv(\"servico\");' type=button name=matriz[okServico] value='OK' class=submit>";
									itemLinhaForm( $texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();			
							fechaTabela();
							echo '</div>';
							echo '<div id="descricao" style="display:none;">';
							novaTabela2SH('left','100%',0,2,0,$corFundo,$corBorda,1);
								novaLinhaTabela($corFundo, '100%');
									$texto = "<b class=bold10>Descrição: </b>";
									$texto.= FormSelectItemNFSServico( $consulta, 'descricao' );
									$texto.= "&nbsp; <input id=okDescricao onClick='preencheCampo(\"discriminacao\",selectdescricao.value, 2); preencheCampo(\"valor\",selectdescricao.value, 3); hidediv(\"descricao\");' type=button name=matriz[okDescricao] value='OK' class=submit>";
									itemLinhaForm( $texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();			
							fechaTabela();
							echo '</div>';
					htmlFechaColuna();
				fechaLinhaTabela();
			fechaFormulario();
		fechaLinhaTabela();
	fechaTabela();
}

function ItensNFServicoValida( $matriz, $acao ){
	$retorno = true;
	
	if ( !$matriz['discriminacao'] )  {
		$retorno = false;
	}
	if ( empty($matriz['valor']) || !is_numeric( formatarValores( $matriz['valor'] ) ) || $matriz['valor'] <= 0 )  {
		$retorno = false;
	}
	return $retorno;
}

function FormSelectItemNFSServico( $consulta, $opcao ){
	$retorno ="";
	
	if( $consulta ){
		for($i = 0; $i<contaConsulta($consulta); $i++){
			$id[$i] = resultadoSQL($consulta, $i, 'id'); 
 			$discriminacao[$i] = ( $opcao == 'servico' ? resultadoSQL($consulta, $i, 'nome') : resultadoSQL($consulta, $i, 'descricao'));
			$valor[$i] =number_format(resultadoSQL($consulta, $i, 'valor'),2,',','.');
			if( $valor[$i] == 0.00 ){ 
				$valor[$i] =number_format(resultadoSQL($consulta, $i, 'vl'),2,',','.');
			}
			$retorno.="<input type=hidden id='$opcao$i' name='$opcao' value='$discriminacao[$i]'>";
			$retorno.="<input type=hidden id='valor$i' name='valor' value='$valor[$i]'>";
		}
		$retorno.= "<select id=\"select$opcao\" name=\"$opcao\" style=\"width: 250px;\">\n";

			if ( !is_array( $discriminacao ) ) {
				$itemsValores = $discriminacao;
			}
			
			foreach ( $discriminacao as $x => $items ){
					$select = ( $itemsValores[$x] == $selected ? 'selected="selected"' : "");
					$retorno .= "<option value=\"".$x."\" ".$select.">".substr($items, 0, 50)." ..."."</option>\n";
			}
		
		$retorno .= "</select>";
	   //return  $texto;
		
		//$retorno = getComboArray("discriminacao", $discriminacao ,$id, '','','onchange="javascript:submit()" style="width: 250px;"');
	}
	else{
		$retono = "<span class=txtaviso>O cliente selecionado não possui nenhum Serviço cadastrado.</span>";
	}
	return $retorno;
}

?>