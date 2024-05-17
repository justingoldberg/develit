<?
################################################################################
#       Criado por: Rogério aka Popó
#  Data de criação: 29/01/2005
# Ultima alteração: 29/01/2005
#    Alteração No.: 001
#
# Função:
# Descontos Nota Fiscal - Funções para criação, alteração e impressão de nota fiscal
################################################################################

function descontosNotaFiscal ( $modulo, $sub, $acao, $registro, $matriz ){
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessPlanos;
	
		if ( $acao == 'incluirDesconto' ){
			$idNF = $matriz['idNota'];
		}	
		else{
			$dadosDescontos = dbDescontosNF('custom', $registro );
			$idNF = $dadosDescontos[0]->idNF;
		}
		
		notaFiscal( 'notafiscal', 'notafiscal', 'verSemItem', $idNF, $matriz );
		mostrarItensNF( 'itensnotafiscal', 'itens','verSemNota', $idNF, $matriz );
		
		
		if ( $acao == "alterarDesconto" ){
			if ( $matriz['bntIncluirDesconto'] == 'Alterar' )
				dbDescontosNF( 'incluir', $registro, '', $matriz );
			else{
				$matriz['formDesconto'] = $dadosDescontos;
				$matriz['botao'] = 'Alterar';
			}
			mostrarDescontosNF( $modulo, $sub, 'ver', $idNF, $matriz );
		}
		elseif ( $acao == "excluirDesconto" ){
			dbDescontosNF( 'excluir', $registro );
			//$matriz['formDesconto'] = $dadosDescontos;
			mostrarDescontosNF( $modulo, $sub, 'ver', $idNF , $matriz );
		}
		elseif ( $acao == "procurar" ){
			//notaFiscalProcurar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif ( substr( $acao, 0, 6 ) == "listar" ){
			//notaFiscalListar( $modulo, $sub, $acao, $registro, $matriz );
		}
		elseif ( $acao == "ver" ){
			//mostrarDescontosNF( $modulo, $sub, $acao, $registro, $matriz);
		}
		elseif ( $acao == "incluirDesconto"){
			
			dbDescontosNF( 'incluir', '', '', $matriz );
			mostrarDescontosNF( $modulo, $sub, 'ver', $matriz['idNota'], $matriz );
	
		}
		
		
} // fim da funcao DescontosNotaFiscal

// ROTINAS DOS Descontos DE NOTA FISCAL \\

# mostra Descontos
function mostrarDescontosNF( $modulo, $sub, $acao, $registro, $matriz ){
	global $corFundo, $corBorda;

	$corGravata = 'tabfundo0';
	$corDetalhe = 'tabfundo1';
	$gravata = array( "Qtde", "Descrição dos Descontos", "Unitário", "Total", "Opções" );
	$largura = array( "5%", "45%", "10%", "10%", "20%" );
	$alinhamento = array( "center", "left", "right", "right", "center");
	
	echo "<br>";
	novaTabela2("[Descontos Nota Fiscal]",'center', '100%', 0, 2, 1, $corFundo, $corBorda, 5);
	
	novaLinhaTabela( $corFundo, '100%');
		for ($x = 0; $x < count( $largura ); $x++ )
			itemLinhaTMNOURL($gravata[$x], 'center', 'middle', $largura[$x], $corFundo, 0, $corGravata );
	fechaLinhaTabela();	
	
	if ( $acao != 'ver' ){
		$dadosDesconto = dbDescontosNF( 'custom', $registro );
		$matriz['formDesconto'] = $dadosdesconto;
		$registro = $dadosdesconto[0]->idNF;
	}
	else{
		$texto = "<form method=post name=matriz action=index.php>
				  <input type=hidden name=registro value=$registro>";
		novaLinhaTabela( $corBorda, '100%');
			itemLinhaTMNOURL( $texto, 'left', 'middle', '100%', $corFundo, 5, $corDetalhe);
		fechaLinhaTabela();
	}
	$descontos = dbDescontosNF('listar', '', $registro );	
		
	formDescontos( $modulo, $sub, $acao, $registro, $matriz );
	

	
	if (!empty( $descontos ) ){
		$cc = 0;
		foreach ( $descontos as $desconto ){
	
			#opcoes
			$def="<a href=?modulo=descontosnotafiscal&sub=descontos&registro=$desconto->id";
			$fnt="<font size='2'>";
			$opcoes =htmlMontaOpcao($def."&acao=alterarDesconto>".$fnt."Alterar</font></a>",'alterar');
			$opcoes .=htmlMontaOpcao($def."&acao=excluirDesconto>".$fnt."Excluir</font></a>",'excluir');
			
			novaLinhaTabela( $corFundo, '100%');
				itemLinhaTMNOURL( $desconto->qtde, $alinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $corDetalhe );
				itemLinhaTMNOURL( $desconto->descricao, $alinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $corDetalhe );
				itemLinhaTMNOURL( number_format( $desconto->valorUnit, 2,',','.' ), $alinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $corDetalhe );
				itemLinhaTMNOURL( number_format( ( $desconto->qtde * $desconto->valorUnit), 2, ',','.' ), $alinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $corDetalhe );
				itemLinhaTMNOURL( $opcoes, $alinhamento[$cc], 'middle', $largura[$cc++], $corFundo, 0, $corDetalhe );
			fechaLinhaTabela();
			$cc = 0;
		}
	}
	else{
		novaLinhaTabela( $corFundo, '100%');
			itemLinhaTMNOURL("Nenhum Desconto Cadastrado", 'center', 'middle', '5%', $corFundo, 5, $corDetalhe );
		fechaLinhaTabela();
	}
	fechaTabela();		
}

# inclusao de Descontos
function formDescontos( $modulo, $sub, $acao, $registro, $matriz ){
	global $corFundo, $corBorda;
	
	$css_ = 'tabfundo1';
	
	$largura = array( "5%", "45%", "10%", "30%" );
	$alinhamento = array( "center", "left", "right", "center");

	$dadosDescontos = $matriz['formDesconto']; //dbDescontosNF( 'custom', $registro );

	$idNF = $dadosDescontos[0]->idNF;
	
	if ( $matriz['botao'] == 'Alterar' && $_REQUEST['modulo'] == 'descontosnotafiscal' ){
		$botao = 'Alterar';
		$acao = 'alterarDesconto';
		$modulo = 'descontosnotafiscal';
		$sub = 'descontos';
		
	}
	else{
		$botao = 'Incluir';
		$acao = 'incluirDesconto';
		$modulo = 'descontosnotafiscal';
		$sub = 'descontos';	
		$idNF = $registro;
	}
	
	novaLinhaTabela($corFundo, '100%');
		$texto="<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=matriz[idNota] value='".$idNF."'>
			<input type=hidden name=registro value='".$dadosDescontos[0]->id."'>";
	
		$texto .= "<input type=text name=matriz[qtde] size=10 value=".$dadosDescontos[0]->qtde.">";
		itemLinhaForm( $texto, 'left', 'middle', $corFundo, 0, $css_);
	
		$texto = "<input type=text name=matriz[descricao] size=45 value='".$dadosDescontos[0]->descricao."'>";
		itemLinhaForm( $texto, 'left', 'middle', $corFundo, 0, $css_);
		
		$texto = "<input type=text name='matriz[vlUnit]' size=10 value='".number_format( ($dadosDescontos[0]->valorUnit),2,',','.')."' onBlur=\"calculaValorTotal( document.forms[0].elements['matriz[qtde]'].value, document.forms[0].elements['matriz[vlUnit]'].value, 'matriz[vlTotal]' ); formataValor(this.value, this.name)\">";
		//$texto = "<input type=text name=matriz[vlUnit] size=10 value=".number_format( ($dadosDescontos[0]->valorUnit),2,',','.').">";
		itemLinhaForm( $texto, 'left', 'middle', $corFundo, 0, $css_);
	
		$texto = "<input type=text name='matriz[vlTotal]' size=10 readonly value='".number_format( ($dadosDescontos[0]->qtde*$dadosDescontos[0]->valorUnit),2,',','.')."'>";
		//$texto = "<input type=text name=matriz[vltotal] size=10 readonly value=".number_format( ($dadosDescontos[0]->qtde*$dadosDescontos[0]->valorUnit),2,',','.').">";
		itemLinhaForm( $texto, 'left', 'middle', $corFundo, 0, $css_ );
		
		$texto = "<input type=submit name=matriz[bntIncluirDesconto] size=10 value='".$botao."' class=submit>
			</form>";
		itemLinhaForm( $texto, 'left', 'middle', $corFundo, 2, $css_);
	fechaLinhaTabela();
	
}

# rotina de bd de Descontos
function dbDescontosNF( $tipo, $id= '', $idNF = '', $matriz = '' ) {
	global $conn, $tb;

	$objDescontosNF = new DescontoNF();
	$objDescontosNF->DescontoNF();
	
	$objDescontosNF->setConnection( $conn );
	
	if ( $id != '' ) $objDescontosNF->setId( $id );
		else $objDescontosNF->setId( 0 );
		
	if ( $idNF != '' ) $objDescontosNF->setIdNF( $idNF );
		else $objDescontosNF->setIdNF( 0 );
	
	if ( $tipo == 'incluir' ){
		$objDescontosNF->setId( $id );
		$objDescontosNF->setIdNF( $matriz['idNota'] );
		$objDescontosNF->setQtde( $matriz['qtde'] );
		$objDescontosNF->setDescricao( $matriz['descricao'] );
		$objDescontosNF->setValorUnit( $matriz['vlUnit'] );
		
		$objDescontosNF->salva();
	}
	elseif ( $tipo == 'excluir' ){
		$objDescontosNF->id = $id;
		
		$objDescontosNF->exclui();
	}
	elseif ( $tipo == 'listar' ){
		@mysql_query('passou no listar dos Descontos');
		$where = array( "$tb[DescontosNF].idNF = ".$objDescontosNF->getidNF() );
		return $objDescontosNF->seleciona('','',$where);
	}
	elseif ( $tipo == 'custom' ){
		@mysql_query('passou no custom do Descontos');
		$where = array( "id = '".$id."'" )	;
		return $objDescontosNF->seleciona('', '', $where );
	}
	elseif ('maxId' ){
		return $objDescontosNF->getMaxField();
	}
}

// FIM DA ROTINA DE Descontos DE NOTA FISCAL\\



function aplicarDescontos($modulo, $sub, $acao, $registro, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro;
	
	$x['id'] = $registro;
	$nota = dbNotaFiscal($x, 'custom');
	
	$tributo = ucfirst( getTipoTributosPessoaTipo( $nota[0]->idPessoaTipo ) );
	
	$valorNota = dbNotaFiscal($x, 'calcularNota');
	 
	$descIRRF = calculaDescontoNF($valorNota[0]->valor, $tributo);
	$descISSQN = calculaDescontoISS($nota[0]->ISSQN, $valorNota[0]->valor, $tributo);

	$descontos = array();
	if ( $descIRRF > 0 ){
		$desc .= 'IRRF: R$ '.formatarValoresForm($descIRRF). '<br>';
		$descTotal += $descIRRF;
	}
	if ($descISSQN > 0 ){
		$desc .= 'ISSQN: R$ '.formatarValoresForm($descISSQN). '<br>';
		$descTotal += $descISSQN;
	}
	
	if (!$matriz[bntConfirmar] || !$matriz['idPlano'] || !$matriz['dtVencimento']){
		
		if ($matriz['bntConfirmar'])
			avisoNOURL("Atencao", "Favor preencher todos os campos.", '400');
		
		echo "<br>";
		novaTabela2("[Aplicar Descontos ao Faturamento]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				#menuOpcAdicional($modulo, $sub, $acao, $registro);
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro value=$registro>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				
				getCampo('combo', 'Valor da nota', '', 'R$ ' . formatarValoresForm($valorNota[0]->valor) );
							
				if (!$desc) $desc = "Nenhum desconto para esta nota fiscal.";

				getCampo('combo', 'Descontos ativos', '', $desc);
				
				getCampo('combo', '<b>Total Descontos</b>', '', 'R$ '.formatarValoresForm($descTotal ) );
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				
				if ($descTotal > 0){
					$campo = formSelectPlanos('form', 'idPlano', 0, "idPessoaTipo='".$nota[0]->idPessoaTipo."'");
					$txt = '<b>Selecione o plano</b><br> Selecione o plano em que será aplicado o desconto';
					getCampo('combo', $txt, '', $campo );
					
					getCampo('text', 'Mês/Ano para Desconto', 'matriz[dtVencimento]', '', 'onblur=verificaDataMesAno(this.value,5)', 'form', '10' );
					
					getBotao('matriz[bntConfirmar]', "Confirmar");
				}
				
			fechaTabela();
	}
	
	else{
		
		$salva = salvarDescontoFaturamento($matriz['idPlano'], $descTotal, $matriz['dtVencimento'], $desc);
		if ($salva){
			echo "<br>";
			avisoNOURL("Atenção:", "Desconto aplicado com sucesso", 400);
			echo "<br>";
		}
		else{
			echo "<br>";
			avisoNOURL("Erro:", "Erro ao aplicar desconto", 400);
			echo "<br>";
		}
		
		notaFiscalListar( $modulo, $sub, 'listar', '', $matriz );
	}
}

/**
 * retorna o valor do desconto da notafiscal 
 * @param ISSQN double	valor issqn
 * @param valorNota double valor da nota fiscal
 */
function calculaDescontoISS ($ISSQN, $valorNota, $tributo) {
	$desconto = 0;

	$parametros = carregaParametrosConfig();
	
	if ($ISSQN > 0 && $valorNota > 0 && strtoupper($parametros['trib'.$tributo.'IssApB']) == "S")
		$desconto = $valorNota * ($ISSQN / 100 );
		
	return ($desconto);
}


/**
 * funcao para calcular descontos da notafiscao
 *conforme a classe ImpressaoNF.desconto
 */
function calculaDescontoNF ($valor, $tributo) {
	$desconto = 0;
	//$valorIRRF = $valor*0.015;
	$parametros = carregaParametrosConfig();	
	
	if ( $parametros['trib'.$tributo.'IrrfMin'] <= $valor  && strtoupper($parametros['trib'.$tributo.'IrrfApB']) == 'S'){
		$valorIRRF = $valor*0.015;
		$desconto+=$valorIRRF;
	}
		
	return ($desconto);
}

function salvarDescontoFaturamento($idPlano, $valorDesconto, $dtVencimento, $desc=''){
	$grava = false;
	$plano = dadosPlanos($idPlano);
	$vencimentos = dadosVencimento($plano['idVencimento']);
	
	$dtVencimento = converteData( $vencimentos['diaVencimento'].'/'.$dtVencimento, 'form', 'bancodata');
	
	$n=0;
	while ($valorDesconto>0){
		$servico = buscaServicoMaiorValor($idPlano, $n);
		if ( $servico['valor'] > 0 ){
			
			if ($servico['valor'] > $valorDesconto )
				$descontar = $valorDesconto;
			else
				$descontar = $servico['valor'];
			
			$x['idPlano'] = $idPlano;
			$x['idServicoPlano'] = $servico['idServicoPlano'];
			$x['dtDesconto'] = $dtVencimento;
			$x['valor'] = number_format( $descontar, 2);
			$x['descricao'] = 'Desconto adicionado automaticamente ao lancar nota. '. $desc;
			$x['status'] = 'A';
			
			$grava = dbDescontoServicoPlano($x, 'incluir');
			$valorDesconto -= $descontar;
			
			$n++;
		}
		else {//se o servico nao tem valor, e levando em consideracao que a consulta foi ordenada. entrara em loop.
			$grava = false;
			break;
		}
	}
	
	
//	$servico = buscaServicoMaiorValor($idPlano);
	
//	if ($valorDesconto < $servico['valor']){
//		$x['idPlano'] = $idPlano;
//		$x['idServicoPlano'] = $servico['idServicoPlano'];
//		$x['dtDesconto'] = $dtVencimento;
//		$x['valor'] = $valorDesconto;
//		$x['descricao'] = 'Desconto adicionado automaticamente ao lancar nota. '. $desc;
//		$x['status'] = 'A'; 
//		
//		$grava = dbDescontoServicoPlano($x, 'incluir');
//			
//	}
	
	return ($grava);
}

?>