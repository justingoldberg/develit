
<?
################################################################################
#       Criado por: Rogério aka Popó
#  Data de criação: 19/01/2005
# Ultima alteração: 26/09/2007
# Alteração No.: 016
#
# Função:
# Nota Fiscal - Funções para criação, alteração e impressão de nota fiscal
################################################################################
function notaFiscal( $modulo, $sub, $acao, $registro, $matriz ){
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessPlanos, $conn, $tb, $arquivo;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		# Topo da tabela - Informações e menu principal do Cadastro
		novaTabela2("[Notas Fiscais]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 4);
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
					echo "<br><img src=".$html[imagem][documentos]." border=0 align=left><b class=bold>$tipoPessoa[descricao]</b>
					<br><span class=normal10>Notas Fiscais</b>.</span>";
				htmlFechaColuna();			
				$texto=htmlMontaOpcao("<br>Adicionar", 'incluir');
				itemLinha($texto, "?modulo=$modulo&sub=notafiscal&acao=adicionar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Procurar", 'procurar');
				itemLinha($texto, "?modulo=$modulo&sub=notafiscal&acao=procurar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Listar", 'listar');
				itemLinha($texto, "?modulo=$modulo&sub=notafiscal&acao=listar", 'center', $corFundo, 0, 'normal');		
			fechaLinhaTabela();
		fechaTabela();
		
		if ( $sub == "notafiscal" ){
			
			if ( $acao == "adicionar" ){
				notaFiscalAdicionar( $modulo, $sub, $acao, $registro, $matriz );
			}
			elseif ( $acao == 'alterar' ){
				notaFiscalAlterar( $modulo, $sub, $acao, $registro, $matriz );
			}
			elseif ( $acao == "procurar" ){
				notaFiscalProcurar( $modulo, $sub, $acao, $registro, $matriz );
			}
			elseif ( substr( $acao, 0, 6 ) == "listar" ){
				notaFiscalListar( $modulo, $sub, $acao, $registro, $matriz );
			}
			elseif ( $acao == "excluir" ){
				notaFiscalExcluir( $modulo, $sub, $acao, $registro, $matriz );
			}
			elseif ( $acao == "cancelar" ){
				notaFiscalCancelar( $modulo, $sub, $acao, $registro, $matriz );
			}
			elseif ( $acao == "imprimir" ){
				if ( $matriz['print'] == false )
					notaFiscalImprimir( $modulo, $sub, $acao, $registro, $matriz );
			}
			elseif ($acao == "alterarDtEmissao"){
				alterarDtEmissao( $modulo, $sub, $acao, $registro, $matriz );
			}
			elseif ($acao == "aplicarDescontos"){
				aplicarDescontos( $modulo, $sub, $acao, $registro, $matriz );
			}
			elseif ( $acao == "incluirItens" ){
				incluirItens( $modulo, $sub, $acao, $registro, $matriz );
			}
			elseif ( $acao == "ver" || $acao == 'verSemItem' ){
				notaFiscalVer( $modulo, $sub, $acao, $registro, $matriz );

//				if ( $acao == 'ver' && ( $_REQUEST['status'] != 'I' && $_REQUEST['status'] != 'C' ) ){
//					mostrarItensNF( $modulo, $sub, $acao, $registro, $matriz);
//					// AGUARDANDO DECISAO SOBRE DESCONTOS
//					//mostrarDescontosNF( 'descontonotafiscal', 'descontos', 'ver', $registro, $matriz );
//					
//				}
			}
			/*
			  Ação para chamar a tela de confirmação de inclusão dos tributos
			  caso o valor total da nota seja superior à R$ 5000,00
			*/
			elseif ($acao == "confirmTributosNF"){
				confirmTributosNF($modulo, $sub, $acao, $registro, $matriz);
			}
			/*
			* Caso o usuário tenha usuário tenha impresso a Nota Fiscal e confirmado a aplicação
			* do total do desconto dos impostos no faturamento, exibir formulário para realizar 			* a aplicação do desconto
			*/
			elseif($acao == 'aplicarDesconto'){
				if($matriz['bntAplicarDesconto'] == 'Sim'){
					aplicarDescontoFaturamento($modulo,$sub,$acao,$registro,$matriz);		
				}
				else{
					notaFiscalListar($modulo, $sub, 'listar', '', $matriz);
				}
			}
			
			# fim das condições adicionais
		}
		
		
	} // fim do else de permissao de usuario

} // fim da funcao NotaFiscal


# função para adicionar
function notaFiscalAdicionar($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro;
	
	if($matriz['idPessoaTipo']){
		$dadosPessoaTipo = dadosPessoasTipos($matriz['idPessoaTipo']);
	}	
	
	if(!$matriz[bntConfirmar] && !$matriz[bntConfirmar2]) {
		# Motrar tabela de busca
		echo "<br>";
		novaTabela2("[Adicionar]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			#menuOpcAdicional($modulo, $sub, $acao, $registro);
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=matriz[idPessoaTipo] value=$matriz[idPessoaTipo]>
				<input type=hidden name=matriz[bntConfirmar] value=$matriz[bntAdicionar]>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			if (!$matriz[idPessoaTipo])
				clienteProcurar( $modulo, $sub, $acao, $registro, $matriz, 0 );
			else {
				clienteProcurar( $modulo, $sub, $acao, $registro, $matriz, 0 );
				$campo = formSelectTipoEndereco($matriz[tipoEndereco], 'tipoEndereco','formnochange');
				$label = '<b>Endereço:</b><br><span class=normal10>Selecione o tipo de endereço preferencial</span>';
				getCampo('combo', $label, '', $campo);
				
				$pop = buscarPopPessoaTipo( $matriz[idPessoaTipo] );
				$campo = formSelectPOP( $pop['id'], 'idPop', 'form');
				$label = '<b>POP:<b><br><span class=normal10>Selecione o POP de cobrança.</span>';
				getCampo('combo', $label, '', $campo);
				
				$campo = getCampoForm('botao', '', 'bntConfirmar', "Confirmar' class='submit");
				getCampo('combo', 'Dados estão Corretos','', $campo );
			}
							
			#botao
			//novaLinhaTabela($corFundo, '100%');
				//$texto="<input type=submit name=matriz[bntAdicionar] value='Confirmar' class=submit>";
				//itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
			//fechaLinhaTabela();
		fechaTabela();
	}elseif(!$matriz[bntConfirmar2] && getImpostoPessoa(getIdTipoImposto("ISSQN"), $matriz[idPessoaTipo] )) {
		
		formNotaFiscalDescontarISS($modulo, $sub, $acao, $registro, $matriz);
		
	}
	else {
		if ($matriz[recolherISS] == 'S' )
			$matriz['ISSQN'] = getImpostoPessoa(getIdTipoImposto("ISSQN"), $matriz[idPessoaTipo]);
			
		$grava = dbNotaFiscal( $matriz, 'incluir' );
		
		# Verificar inclusão de registro
		if($grava) {
			$dadosNF = dbNotaFiscal( $matriz, 'lastId' );
			
			$registro = $dadosNF[0]->id;
			# Visualizar Pessoa
			$msg="Registro gravado com sucesso!";
			echo "<br>";
			avisoNOURL("Aviso: ", $msg, 400);
			//menuOpcAdicional( $modulo, $sub, $acao, $registro, $matriz);
			notaFiscalVer( $modulo, $sub, 'ver', $registro, $matriz );
		//	mostrarItensNF( 'itensnotafiscal', 'itens', 'ver', $registro, $matriz );		
		} 
		else {
			$msg="Ocorreram erros durante a gravação.";
			avisoNOURL("Aviso: Ocorrência de erro", $msg, 400);
		}
	}
}

# função para Alterar
function notaFiscalAlterar($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $conn, $tb;
	
	if(!$matriz[bntAlterar]) {
		
		echo "<br>";
		$cssFundo1 = 'tabfundo1';
		
		# Motrar tabela de busca
		novaTabela2("[Alterar]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			#menuOpcAdicional($modulo, $sub, $acao, $registro);
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=matriz[bntAlterar] value=$matriz[bntAlterar]>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			novaLinhaTabela( $corFundo, '100%' );
				itemLinhaTMNOURL( "Data Emissão", 'right', 'middle', '30%', $corFundo, 0, $cssFundo1 );
				$texto = "<input type=type name=matriz[dtEmissao] value=$matriz[dtEmissao]>";
				itemLinhaTMNOURL( $texto, 'left', 'middle', '70%', $corFundo, 0, $cssFundo1 );
			fechaLinhaTabela();
			novaLinhaTabela( $corFundo, '100%' );
				itemLinhaTMNOURL( "Observações", 'right', 'middle', '30%', $corFundo, 0, $cssFundo1 );
				$texto = "<textarea name=matriz[obs] rows=5 style='width: 100%'>$matriz[dtEmissao]</textarea>";
				itemLinhaTMNOURL( $texto, 'left', 'middle', '70%', $corFundo, 0, $cssFundo1 );				
			fechaLinhaTabela();
			
			#botao
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntAlterar] value='Alterar' class=submit>";
				itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		fechaTabela();	
	}
	else {
		
		//$matriz[id]=buscaNovoID($tb[Produto]);
		/*$grava=dbProduto($matriz, 'incluir');
		
		# Verificar inclusão de registro
		echo "<br>";
		if($grava) {
			# Visualizar Pessoa
			$msg="Registro gravado com sucesso!";
			avisoNOURL("Aviso: ", $msg, 400);
			echo "<br>";
			produtoListar($modulo, $sub, 'listar', $matriz[id], $matriz);
		} 
		else {
			$msg="Ocorreram erros durante a gravação.";
			avisoNOURL("Aviso: Ocorrência de erro", $msg, 400);
		}*/
	}
}

# função para Procurar
/*function notaFiscalProcurar($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $conn, $tb;
	
	if(!$matriz[bntAdicionar]) {
		
		# Motrar tabela de busca
		novaTabela2("[Procurar]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			#menuOpcAdicional($modulo, $sub, $acao, $registro);
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=matriz[bntConfirmar] value=$matriz[bntConfirmar]>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			//$dados=dadosProduto(0); // busca dados da Nota se existir
			
			//produtoMostra($dados); // mostra a tela de inclusao da nota
			
			#botao
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntAdicionar] value='Adicionar' class=submit>";
				itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		fechaTabela();	
	}
	else {
		
		//$matriz[id]=buscaNovoID($tb[Produto]);
		/*$grava=dbProduto($matriz, 'incluir');
		
		# Verificar inclusão de registro
		echo "<br>";
		if($grava) {
			# Visualizar Pessoa
			$msg="Registro gravado com sucesso!";
			avisoNOURL("Aviso: ", $msg, 400);
			echo "<br>"; 
			produtoListar($modulo, alterarDtEmissao$sub, 'listar', $matriz[id], $matriz);
		} 
		else {
			$msg="Ocorreram erros durante a gravação.";
			avisoNOURL("Aviso: Ocorrência de erro", $msg, 400);
		}
	}
}*/

function notaFiscalProcurar($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $conn, $tb;
	
//	if(!$matriz[bntAdicionar]) {
		$data=dataSistema();
		# Motrar tabela de busca
		echo "<br>";
		novaTabela2("[Procurar]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			#menuOpcAdicional($modulo, $sub, $acao, $registro);
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>	
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=matriz[bntConfirmar] value=$matriz[bntConfirmar]>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			if ($matriz[bntProcurar]){
				$registro[idPessoaTipo] = '';
				$registro[dtInicial] = '';
				$registro[dtFinal] = '';
			}
						
			clienteProcurar($modulo, $sub, $acao, $registro, $matriz, 0);
			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Mes/Ano Inicial:</b><br>
				<span class=normal10>Informe o mes/ano inicial para consulta</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input id=\"ab\" type=text name=registro[dtInicial] size=7 value='$registro[dtInicial]' onBlur=verificaDataMesAno2(this.value,\"ab\")>&nbsp;<span class=txtaviso>(Formato: $data[mes]/$data[ano])</span>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Mes/Ano Final:</b><br>
				<span class=normal10>Informe o mes/ano final para consulta</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input id=\"cd\" type=text name=registro[dtFinal] size=7 value='$registro[dtFinal]'  onBlur=verificaDataMesAno2(this.value,\"cd\")>&nbsp;<span class=txtaviso>(Formato: $data[mes]/$data[ano])</span>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		
			
			#botao
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntEnviar] value='Enviar' class=submit>";
				itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			
			#somente para ser compativel com o clienteProcurar() existente
			if  (($registro[idPessoaTipo] == '' || $registro[idPessoaTipo] != $matriz[idPessoaTipo]) && (!$matriz[bntProcurar]) ){
				echo "<input type=hidden name=registro[idPessoaTipo] value=$matriz[idPessoaTipo]>";
				$registro[idPessoaTipo] = $matriz[idPessoaTipo];
			}
			#para salvar o id do usuario mesmo quando ele for vizualizar os detalhes.
			elseif ((!empty($matriz[txtProcurar]) && $matriz[idPessoaTipo] == $registro[idPessoaTipo] ) && (!$matriz[bntProcurar]) ){
				echo "<input type=hidden name=registro[idPessoaTipo] value=$registro[idPessoaTipo]>" ;
			}
			#para exibir o selectbox ao retornar dos detalhes do cliente.
			if ($matriz[bntSelecionar]){
				echo "<input type=hidden name=matriz[txtProcurar] value=\"$matriz[txtProcurar]\">" ;			
				echo "<input type=hidden name=matriz[idPessoaTipo] value=$matriz[idPessoaTipo]>";
			}

			if ((!empty($registro[idPessoaTipo]) ) || ( !empty($registro[dtInicial]) || !empty($registro[dtFinal])))
					notaFiscalListar($modulo, $sub, $acao, $registro, $matriz);
			
		fechaTabela();	
		
}



# função para Listar
function notaFiscalListar($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $limite;
	
	$largura =     array( "6%",   "40%" , "10%", "14%", "30%");
	$alinhamento = array( "center", "left", "left", "left", "left" );
	$gravata =     array( "N° NF", "Cliente", "Valor", "Status", "Opções" );
	$corGravata = 'tabfundo0';
	$corDetalhe = 'tabfundo1';
	
	# Motrar tabela de busca
		echo "<BR>";
		novaTabela2("[Listar]<a name=ancora></a>", "center", '100%', 1, 2, 1, $corFundo, $corBorda, 5);

			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=matriz[bntConfirmar] value=$matriz[bntConfirmar]>";
				$texto .= menuOpcAdicional( $modulo, $sub, $acao, $registro, $matriz, 5 );
				itemLinhaNOURL($texto, 'left', $corFundo, 5, 'tabfundo1');
			fechaLinhaTabela();
			
			# Implementei esta validação para que o formulário apareça em todas as telas de listagem do módulo
			# Nota Fiscal
			if(substr($acao, 0, 6) == "listar"){
				# Formuário para filtragem de dados da listagem para facilitar a busca de registro de notas específicas
				# por Felipe Assis - 08/10/2008
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('100%', 'center', $corFundo, 5, 'tabfundo1');
						if($_POST['btOk'] && (empty($_POST['criterio']) || empty($_POST['pesquisar'])) ){
							$msg = "Para buscar um registro espec&iacute;fico, voc&ecirc; deve informar uma busca e " .
									"selecionar um crit&eacute;rio.";
							avisoNOURL("Aviso", $msg, 450);
							echo "<br>";
						}
						formFiltrarNF($modulo, $sub, $acao, $registro, $matriz);
					htmlFechaColuna();
				fechaLinhaTabela();
			}
			
			# Caso o usuário tenha clicado no botão ok para fazer a listagem
			if($_POST['btOk'] && (!empty($_POST['criterio']) && !empty($_POST['pesquisar'])) ){
				$dados = dbNotaFiscal($registro, "listarFiltro");
			}
			else{
				$dados = dbNotaFiscal( $registro, $acao ); // busca dados da Nota se existir
			}
			
	//inserindo paginador para exibição dos registros
			
			if(!$dados || count($dados)==0) {
				# Não há registros
				itemTabelaNOURL('Nenhuma Nota a Listar !', 'left', $corFundo, 3, 'txtaviso');
			}
			else {
				# Paginador
				paginador($dados, count($dados), $limite[lista][notafiscal], $registro, 'normal10', 5, $urlADD);
			
				novaLinhaTabela( $corFundo, '100%' );
				for ( $x = 0; $x < count( $largura ); $x++ )
					itemLinhaTMNOURL( $gravata[$x], 'center', 'middle', $largura[$x], $corFundo, 0, $corGravata);
				fechaLinhaTabela();
			
				$cc = 0; //contador
				if (!empty( $dados ) ){	
				
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
					
					$limite=$i+$limite[lista][notafiscal];
				
					while($i < count($dados) && $i < $limite) {
							
						novaLinhaTabela( $corFundo, '100%');
							itemLinhaTMNOURL( $dados[$i]->numNF, $alinhamento[$cc++], 'middle', '10%', $corFundo, 0, $corDetalhe)	;
							itemLinhaTMNOURL( $dados[$i]->cliente.$texto, $alinhamento[$cc++], 'middle', '10%', $corFundo, 0, $corDetalhe)	;
						
							#valores
							$reg['id']=$dados[$i]->id;
							$val = dbNotaFiscal($reg, "calcularNota");
							$moeda = "R$ ";
							itemLinhaTMNOURL($moeda . number_format($val[0]->valor,2,',','.'), $alinhamento[$c++], 'middle', '10', $corFundo, 0, $corDetalhe);
						
							#status
							if (strtoupper(c) == 'A') $txt = htmlMontaOpcao("Aberta", "pasta");
							elseif (strtoupper($dados[$i]->status) == 'I') $txt = htmlMontaOpcao("Impressa", "imprimir");
							elseif (strtoupper($dados[$i]->status) == 'C') $txt = htmlMontaOpcao("Cancelada", "cancelar");
							itemLinhaTMNOURL($txt, $alinhamento[$cc++], 'middle', '10', $corFundo, 0, $corDetalhe);
							
							$status=$dados[$i]->status;
							$id=$dados[$i]->id;
							
							#opcoes
							$def="<a href=?modulo=$modulo&sub=$sub&registro=$id&status=$status";
							$fnt="<font size='2'>";
							$opcoes =htmlMontaOpcao($def."&acao=ver>".$fnt."Ver</font></a>",'ver');
							if ( strtoupper( $dados[$i]->status ) == 'A' ){
								$opcoes.=htmlMontaOpcao($def."&acao=excluir>".$fnt."Excluir</font></a>",'excluir');
								
								$opcoes.=htmlMontaOpcao($def."&acao=alterarDtEmissao>".$fnt."Imprimir</font></a>",'imprimir');
								$opcoes.='<br>'.htmlMontaOpcao($def."&acao=aplicarDescontos>".$fnt."Aplicar Descontos</font></a>",'financeiro');
							}	
							elseif ( strtoupper( $status ) == 'I' )
								$opcoes.=htmlMontaOpcao($def."&acao=cancelar>".$fnt."Cancelar<font></a>",'cancelar');
														
							itemLinhaTMNOURL( $opcoes, $alinhamento[$cc++], 'middle', '10%', $corFundo, 0, $corDetalhe)	;
						fechaLinhaTabela();
						$cc= 0;					
						# Incrementar contador
						$i++;
					} #fecha laco de montagem de tabela
												
				}
				else{
					novaLinhaTabela( $corFundo, '100%');
						itemLinhaTMNOURL( "Nenhuma Nota a Listar !", 'center', 'middle', '100%', $corFundo, 5, $corDetalhe );
				}
			}	
		fechaTabela();	
}

#funcao de exclusao
function notaFiscalExcluir( $modulo, $sub, $acao, $registro, $matriz ){
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $conn, $tb;
	
	echo "<br>";
	if(!$matriz[bntExcluir]) {
		
		# Motrar tabela de busca
		novaTabela2("[Excluir]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);

			#opcoes
			$def="<a href=?modulo=$modulo&sub=$sub&registro=$registro";
			$fnt="<font size='2'>";
			$opcoes =htmlMontaOpcao($def."&acao=listar>".$fnt."Listar</font></a>",'listar');
			novaLinhaTabela( $corFundo, '100%' );
				itemLinhaTMNOURL( $opcoes, 'right', 'middle', '100%', $corFundo, 2, 'tabfundo1' );
			fechaLinhaTabela();
			
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro value=$registro>
				<input type=hidden name=matriz[bntExcluir] value=$matriz[bntExcluir]>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			$matriz['id'] = $registro;
			$dadosNota = dbNotaFiscal( $matriz, 'custom' );
			mostraCliente( $dadosNota[0]->idPessoaTipo );
			#botao
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntExcluir] value='Excluir' class=submit>";
				itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		fechaTabela();
		
	}
	else {
		
		$matriz['id'] = $registro;
		
		$grava=dbNotaFiscal( $matriz, 'excluir');
		
		# Verificar inclusão de registro
		//echo "<br>";
		if($grava) {
			# Visualizar Pessoa
			$msg="Registro excluído com sucesso!";
			avisoNOURL("Aviso: ", $msg, 400);
			//echo "<br>";
			notaFiscalListar( $modulo, $sub, 'listar', '', $matriz );
		} 
		else {
			$msg="Ocorreram erros durante a exclusão.";
			avisoNOURL("Aviso: Ocorrência de erro", $msg, 400);
		}
	}	
}

#funcao de cancelamento nota
function notaFiscalCancelar( $modulo, $sub, $acao, $registro, $matriz ){
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $conn, $tb;
	
	echo "<br>";
	if(!$matriz[bntCancelar]) {
		
		# Motrar tabela de busca
		novaTabela2("[Cancelar]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);

			#opcoes
			$def="<a href=?modulo=$modulo&sub=$sub&registro=$registro";
			$fnt="<font size='2'>";
			$opcoes =htmlMontaOpcao($def."&acao=listar>".$fnt."Listar</font></a>",'listar');
			novaLinhaTabela( $corFundo, '100%' );
				itemLinhaTMNOURL( $opcoes, 'right', 'middle', '100%', $corFundo, 2, 'tabfundo1' );
			fechaLinhaTabela();
			
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro value=$registro>
				<input type=hidden name=matriz[bntCancelar] value=$matriz[bntCancelar]>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			$matriz['id'] = $registro;
			$dadosNota = dbNotaFiscal( $matriz, 'custom' );
			mostraCliente( $dadosNota[0]->idPessoaTipo );
			#botao
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntCancelar] value='Cancelar' class=submit>";
				itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		fechaTabela();
		
	}
	else {
		
		$matriz['id'] = $registro;
		
		$grava=dbNotaFiscal( $matriz, 'cancelar');
		
		# Verificar inclusão de registro
	//	echo "<br>";
		if($grava) {
			# Visualizar Pessoa
			$msg="Registro cancelado com sucesso!";
			avisoNOURL("Aviso: ", $msg, 400);
		//	echo "<br>";
			notaFiscalListar( $modulo, $sub, 'listar', '', $matriz );
		} 
		else {
			$msg="Ocorreram erros durante o cancelamento.";
			avisoNOURL("Aviso: Ocorrência de erro", $msg, 400);
		}
	}	
}

# função para imprimir
/*function notaFiscalImprimir($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $arquivo, $conn, $tb;
	
	if(!$matriz[bntImprimir]) {

		//grava dados do cliente na nota
		$matriz['id'] = $registro;
		dbNotaFiscal( $matriz, 'imprimir' );
		
		//rotina de impressão da Nota Fiscal
		$objNF = new NotaFiscal();
		$objNF->NotaFiscal( $conn );
		$objNF->preparaNota( $registro );

		$nomeArquivo = $arquivo['tmpNota']."nota".$registro.".txt";

		if (!file_exists( $nomeArquivo ) ){
			$nota = fopen( $nomeArquivo, 'w' );
			fwrite( $nota, $objNF->imprimirNota() );
	
			fclose( $nota );
			//chmod( $nomeArquivo, 0666);
	
			$sql = "SELECT valor FROM ".$tb['ParametrosConfig']." where parametro = 'path_impressora'";
			$consulta = consultaSQL( $sql, $conn);
			if ( $consulta && contaConsulta( $consulta ) > 0 )
				$impressora = resultadoSQL( $consulta, '0', 'valor' );
			
			if (!empty( $impressora ) && !is_null( $impressora ) && isset( $impressora ) )
				exec( "lpr -P".$impressora." ".$nomeArquivo );
			else
				exec( "lpr ".$nomeArquivo );

		}
		else{
			unlink( $nomeArquivo );
		}		
		# Motrar tabela de busca
		echo "<BR>";
		novaTabela2("[Imprimir]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			#menuOpcAdicional($modulo, $sub, $acao, $registro);
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro value = $registro
				<input type=hidden name=matriz[print] = 'true'>
				<input type=hidden name=matriz[bntImprimir] value=$matriz[bntImprimir]>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela( $corFundo, '100%' );
				$texto = "A Nota Fiscal foi impressa corretamente?";
				itemLinhaTMNOURL( $texto, 'center', 'middle', '100%', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			#botao
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntImprimir] value='Confirmar' class=submit>";
				itemLinhaForm($texto, 'right', 'top', $corFundo, 1, 'tabfundo1');
				$texto="<input type=submit name=matriz[bntImprimir] value='Cancelar' class=submit>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 1, 'tabfundo1');
			fechaLinhaTabela();
		fechaTabela();
	}
	else {
		
		$matriz['id'] = $registro;

		if ( $matriz['bntImprimir'] == 'Confirmar' ){
			$grava = dbNotaFiscal( $matriz, 'imprimirOk' );
		
			# Verificar inclusão de registro
			echo "<br>";
			if($grava) {
				# Visualizar Pessoa
				$msg="Nota Fiscal impressa com sucesso!";
				avisoNOURL("Aviso: ", $msg, 400);
				//echo "<br>";
				
				$nota = dbNotaFiscal($matriz, 'custom');
				if ($nota[0]->recolherISS == 'S')
					notaFiscalDescontarISS($modulo, $sub, $acao, $registro, $matriz);
				else
					notaFiscalListar( $modulo, $sub, 'listar', '', $matriz );
			} 
			else {
				$msg="Ocorreram erros durante a impressão.";
				avisoNOURL("Aviso: Ocorrência de erro", $msg, 400);
			}
		}
		elseif ( $matriz['bntImprimir'] == 'Cancelar' ){
			notaFiscalListar( $modulo, $sub, 'listar', '', $matriz );	
		}
		
	}
	

}*/	

####FUNÇÃO IMPRIMIR MODIFICADA####

# função para imprimir
function notaFiscalImprimir($modulo, $sub, $acao, $registro, $matriz){

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $arquivo, $conn, $tb;

		if(!$matriz[bntImprimir]){
			
			//gravando dados do cliente na nota
			$matriz['id'] = $registro;
			dbNotaFiscal($matriz, 'imprimir');
			
			//rotina de impressão da nota fiscal
			$objNF = new NotaFiscal();
			# ação para desconto da NF
			
			# Caso o usuário tenha clicado em "Sim", gravar tributos que serão impressos
			# na Nota Fiscal
			$descontar = 'Não';
			if(($matriz['botao'] == 'Confirmar') && !(empty($matriz['impostosNF']))){
				$objNF->tributos = true;
				# definindo se o desconto vai ser feito na nota ou não
				$descontar = 'Sim';

				# Verificando se existem registros de Impostos na Nota Fiscal				
				$consultaImpostos = dbImpostosNF($matriz, 'pesquisarImpostos');
				if(!$consultaImpostos){
					dbImpostosNF($matriz, 'inserir');
				}
				else{ 
					/*
						Caso tenha impostos cadastrados para determinada nota, por cancelamento
						ou problemas com a impressão, fazer exclusão dos ítens de impostos e inserir os novos ítens
					*/
					dbImpostosNF($matriz, 'excluir');
					dbImpostosNF($matriz, 'inserir');
				}
			}
			$objNF->NotaFiscal($conn);
			$objNF->preparaNota($registro);
			
			$nomeArquivo = $arquivo['tmpNota']."nota".$registro.".txt";
			
			if(!file_exists($nomeArquivo)){
				$nota = fopen($nomeArquivo, 'w');
				fwrite($nota, $objNF->imprimirNota());
				
				fclose($nota);
				
				$sql = "SELECT valor FROM ".$tb['ParametrosConfig']." WHERE parametro = 'path_impressora'";
				$consulta = consultaSQL($sql, $conn);
				if($consulta && contaConsulta($consulta) > 0){
					$impressora = resultadoSQL($consulta, '0', 'valor');
				}
				if(!empty($impressora) && is_null($impressora) && isset($impressora)){
					exec("lpr -P".$impressora." ".$nomeArquivo);
				}
				else{
					exec("lpr ".$nomeArquivo);
				}
			}
			else{
				unlink($nomeArquivo);
			}
			
			#mostrar Tabela de Confirmação da Impressão
			//echo "<br>";
			novaTabela2("[Imprimir]<a name='ancora'></a>", 'center','100%', 0, 2, 1, $corFundo, $corBorda, 2);
				novaLinhaTabela($corFundo, '100%');
					$texto = "
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro>
					<input type=hidden name=matriz[print] = 'true'>
					<input type=hidden name=matriz[desconto] value=$descontar>
					<input type=hidden name=matriz[bntImprimir] value=$matriz[bntImprimir]>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela( $corFundo, '100%' );
					$texto = "A Nota Fiscal foi impressa corretamente?";
					itemLinhaTMNOURL( $texto, 'center', 'middle', '100%', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				#espaço dos botões
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit name=matriz[bntImprimir] value='Confirmar' class=submit>";
					itemLinhaForm($texto, 'right', 'top', $corFundo, 1, 'tabfundo1');
					$texto="<input type=submit name=matriz[bntImprimir] value='Cancelar' class=submit>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 1, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		}
		else{
			$matriz['id'] = $registro;
			if($matriz['bntImprimir'] == 'Confirmar'){
				$grava = dbNotaFiscal($matriz, 'imprimirOk');
				# Verificar inclusão do registro
				if($grava){
					# Visualizar Pessoa
					echo "<br>";
					$msg="Nota Fiscal impressa com sucesso!";
					avisoNOURL("Aviso: ", $msg, 400);
					
					$nota = dbNotaFiscal($matriz, 'custom');
											
					if($nota[0]->recolherISS == 'S'){
						notaFiscalDescontarISS($modulo, $sub, $acao, $registro, $matriz);
					}
					else{
						# Caso os descontos tenham sido calculados na nota,
						# perguntar ao usuário se o total dos descontos seja aplicado
						# no faturamento
						if($matriz['desconto'] == 'Sim'){
							confirmAplicarDesconto($modulo, $sub, $matriz);
						}
						else{
							notaFiscalListar($modulo, $sub, 'listar', '', $matriz);
							# Excluindo registros temporários dos impostos na NF
							$exclui = dbImpostosNF($matriz, 'excluir');
					
							if(!$exclui){
								$msg = "Erro ao excluir registro temporário dos Impostos";
								avisoNOURL("Aviso: Ocorrência de Erro:", $msg, 400);
							}
						}	
					}
				}
				else{
					$msg = "Ocorreram erros durante a Impressão";
					avisoNOURL("Aviso: Ocorrência de Erro", $msg, 400);
				}
			}
			elseif($matriz['bntImprimir'] == 'Cancelar'){	
				notaFiscalListar( $modulo, $sub, 'listar', '', $matriz );
				# Excluindo registros temporários dos impostos na NF
				$exclui = dbImpostosNF($matriz, 'excluir');
				
				if(!$exclui){
					$msg = "Erro ao excluir registro temporário dos Impostos";
					avisoNOURL("Aviso: Ocorrência de Erro:", $msg, 400);
				}
			}
		}

}// fim da função imprimir	
	
function clienteProcurar($modulo, $sub, $acao, $registro, $matriz, $exibeBotao = 1) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessCadastro, $titulo;
	
	if( ( ($acao=='procurar') || ($acao=='adicionar') ) && (!$matriz[bntSelecionar]) ) {
		
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro value=$registro>";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<span class=bold10>Busca por Cliente:</span><br><span class=normal10>Informe nome ou dados do cliente para busca</span>', 'right', 'middle', '35%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[txtProcurar] size=50 value='$matriz[txtProcurar]'>&nbsp;<input type=submit value=Procurar name=matriz[bntProcurar] class=submit>";
				itemLinhaForm($texto, 'left', 'middle', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			if($matriz[txtProcurar]) {
				# Procurar Cliente
				$tipoPessoa=checkTipoPessoa('cli');
				$consulta=buscaPessoas("
					((upper(nome) like '%$matriz[txtProcurar]%' 
						OR upper(razao) like '%$matriz[txtProcurar]%' 
						OR upper(site) like '%$matriz[txtProcurar]%' 
						OR upper(mail) like '%$matriz[txtProcurar]%')) 
					AND idTipo=$tipoPessoa[id]", $campo, 'custom','nome');
				
				if($consulta && contaConsulta($consulta)>0) {
					# Selecionar cliente
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<span class=bold10>Clientes encontrados:</span><br>
						<span class=normal10>Selecione o Cliente</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
						$texto = formSelectConsulta($consulta, 'nome', 'idPessoaTipo', 'idPessoaTipo', $matriz[idPessoaTipo]);
						$texto .= "&nbsp;<input type=submit name=matriz[bntSelecionar] value='Selecionar' class=submit>";
						itemLinhaForm($texto, 'left', 'middle', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				elseif ( contaConsulta( $consulta ) <= 0){
					$texto = "Nenhum registro encontrado!";
					novaLinhaTabela( $corFundo, '100%');
					itemLinhaTMNOURL($texto, 'center','middle','10%', $corFundo, '2', 'tabfundo1');
					fechaLinhaTabela();
				}
			}
			htmlFechaLinha();
		//fechaTabela();
	}
	# realizar consulta
	elseif( ($matriz['bntSelecionar'] && $matriz['idPessoaTipo']) && (! $matriz['bntAdicionar']) ) {	
		menuOpcAdicional( $modulo, $sub, $acao, $registro, $matriz);	
		mostraCliente( $matriz['idPessoaTipo'] );
		
		#botao
		
		novaLinhaTabela($corFundo, '100%');
			//$texto = "<input type=hidden name=status value='A'";
			if ( $exibeBotao)
				$texto = "<input type=submit name=matriz[bntAdicionar] value='Confirmar' class=submit>";
			
			itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		
		
	}
}

function dadosCliente( $idPessoaTipo, $idTipoEndereco='' ){
	global $tb, $conn;
	
	$sql = "SELECT id, idPessoa FROM ".$tb['PessoasTipos']." WHERE id ='".$idPessoaTipo."'";
	$consulta = consultaSQL( $sql, $conn );
	if ( $consulta && contaConsulta( $consulta ) > 0 ){
		$idPessoa = resultadoSQL( $consulta, 0, 'idPessoa');
	}
	#dados da pessoa;
	$pessoas = dadosPessoas( $idPessoa );
	
	#dados do endereco
	$enderecos = dadosEnderecoCustom( $idPessoaTipo, $idTipoEndereco );
	
//	#
//	if (!$enderecos)
//		$enderecos = dadosEndereco( $idPessoaTipo );
	
	#documentos da pessoa		
	$documentos = dadosDocumentosPessoas( $idPessoa );
	
	#retorna tudo
	$retorno[pessoas] = $pessoas;
	$retorno[enderecos] = $enderecos;
	$retorno[documentos] = $documentos;
	
	return $retorno;
	
}

/**
 * @return void
 * @param unknown $id
 * @desc Funcao que mostra os campos da Nota com os dados do cliente
*/
function mostraCliente( $idPessoaTipo, $dadosNF='' ){
	global $tb, $conn;
	
	//busca os dados a serem exibidos
	$dados = dadosCliente( $idPessoaTipo);
	
	if($dadosNF){
		$dados['enderecos']['enderecoCompleto']=$dadosNF->endereco;
		$dados['enderecos']['bairro']=$dadosNF->bairro;
		$dados['enderecos']['cep']=$dadosNF->cep;
		$dados['enderecos']['cidade']=$dadosNF->cidade;
		$dados['enderecos']['fone1DDD']=$dadosNF->fone;
		$dados['enderecos']['uf']=$dadosNF->uf;
	}

	#nome
	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL("<b>Razão: </b>", 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		( $dados['pessoas']['tipoPessoa'] == 'F' ? $razao = $dados['pessoas']['nome'] : $razao = $dados['pessoas']['razao'] );
		itemLinhaTMNOURL( $razao, 'left', 'top', '70%', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
	#cnpj
	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL("<b>CNPJ / CPF: </b>", 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		// verifica se o documento eh de pessoas juridica ou fisica
		( $dados['documentos']['cnpj'] != '' ? $cnpj = $dados[documentos][cnpj] : $cnpj  = $dados[documentos][cpf]);
		itemLinhaTMNOURL( $cnpj, 'left', 'top', '70%', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
	#endereco
	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL("<b>Endereço: </b>", 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		itemLinhaTMNOURL( $dados['enderecos']['enderecoCompleto'], 'left', 'top', '70%', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
	#bairro
	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL("<b>Bairro: </b>", 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		itemLinhaTMNOURL( $dados['enderecos']['bairro'], 'left', 'top', '70%', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
	#cep
	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL("<b>Cep: </b>", 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		itemLinhaTMNOURL( $dados['enderecos']['cep'], 'left', 'top', '70%', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
	#cidade
	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL("<b>Cidade: </b>", 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		itemLinhaTMNOURL( $dados['enderecos']['cidade'], 'left', 'top', '70%', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
	#fone
	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL("<b>Fone: </b>", 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		itemLinhaTMNOURL($dados['enderecos']['fone1DDD'], 'left', 'top', '70%', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();	
	#uf
	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL("<b>UF: </b>", 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		itemLinhaTMNOURL( $dados['enderecos']['uf'], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1' );
	fechaLinhaTabela();	

}


function dbNotaFiscal( $matriz, $tipo ){
	global $conn, $tb;

	$objNF = new NotaFiscal( $conn );
	//$objNF->NotaFiscal( $conn );

	

	$objNF->setId( $matriz['id'] );
	$objNF->setIdPessoaTipo( $matriz['idPessoaTipo'] );
	
	if ( $tipo == 'incluir' ){
		//$objNF->setNovoNumNF(); // incluir numero da nota somente apos ser impressa.
		
		$dados = dadosCliente( $matriz['idPessoaTipo'], $matriz[tipoEndereco]);
		
		$objNF->razao = ( $dados['pessoas']['razao'] != '' ? $razao = $dados['pessoas']['razao'] : $razao = $dados['pessoas']['nome'] );
		$objNF->cnpj = ( $dados['documentos']['cnpj'] != '' ? $cnpj = $dados[documentos][cnpj] : $cnpj  = $dados[documentos][cpf]);
		$data = dataSistema();
		$objNF->dtEmissao = $data['dataBanco'];
		$objNF->endereco = $dados['enderecos']['enderecoCompleto'];
		$objNF->bairro = $dados['enderecos']['bairro'];
		$objNF->cep = $dados['enderecos']['cep'];
		$objNF->cidade = $dados['enderecos']['cidade'];
		$objNF->fone = $dados['enderecos']['fone1DDD'];
		$objNF->uf = $dados['enderecos']['uf'];
		$objNF->inscrEst = ( $dados['enderecos']['ie'] != '' ? $ie = $dados['documentos']['ie'] : $ie = $dados['documentos']['rg'] );
		$objNF->obs = "";
		$objNF->status = "A";
		$objNF->natOper = "";
		$objNF->ISSQN = $matriz['ISSQN'];
		$objNF->idPop = $matriz['idPop'];

		
		return $objNF->gravarNota();
	}
	elseif ( $tipo == 'excluir' ){
		$objItensNF = new ItemNF();
		$objItensNF->ItemNF();
		$objItensNF->setConnection( $conn );
		$objItensNF->setIdNF( $matriz['id'] );
		$grava = $objItensNF->excluiRelacionamento( $objItensNF->tabela, array( "idNF = '".$objItensNF->getidNF()."'" ) );
		if ( $grava ){
			return $objNF->exclui();
		}
	}
	elseif ( $tipo == 'cancelar' ){
		return $objNF->gravaStatus( $objNF->getId(), 'C' );	
	}
	elseif ( $tipo == 'imprimirOk'){
		return  $objNF->gravaStatus( $objNF->getId(), 'I' );
	}
	elseif ( $tipo == 'imprimir' ){
		
		//faz atualizacao dos dados do cliente no cadastro da nota fiscal e da data de impressao (para contemplar alteracoes de cadastro entre o inclusao da nota e sua impressao )
		$where = array( "id ='".$objNF->getId()."'" );
		$dadosNota = $objNF->seleciona('','', $where );
		$dados = dadosCliente( $dadosNota[0]->idPessoaTipo );
		
		//$objNF->numNF = $dadosNota[0]->numNF; //seta o numero da nf somente agora, momento em que se imprime as notas.
		$objNF->idPop = $dadosNota[0]->idPop;
		if ( !$dadosNota[0]->numNF )
			$objNF->setNovoNumNF();
		else
			$objNF->numNF = $dadosNota[0]->numNF;
			
		$objNF->idPessoaTipo = $dadosNota[0]->idPessoaTipo;
		$objNF->razao = ( $dados['pessoas']['razao'] != '' ? $razao = $dados['pessoas']['razao'] : $razao = $dados['pessoas']['nome'] );
		$objNF->cnpj = ( $dados['documentos']['cnpj'] != '' ? $cnpj = $dados[documentos][cnpj] : $cnpj  = $dados[documentos][cpf]);
		if ($matriz[emissao] == 'manual')
			$data['dataBanco'] = converteData($matriz[dtEmissao],"form", "bancodata");
		else
			$data = dataSistema();
		$objNF->dtEmissao = $data['dataBanco'];
		
// alterado por que nao pode sobrecrever o endereco escolhido
		$objNF->endereco = $dadosNota[0]->endereco;
		$objNF->bairro = $dadosNota[0]->bairro;
		$objNF->cep = $dadosNota[0]->cep;
		$objNF->cidade = $dadosNota[0]->cidade;
		$objNF->fone = $dadosNota[0]->fone;
		$objNF->uf = $dadosNota[0]->uf;
//

//		$objNF->endereco = $dados['enderecos']['enderecoCompleto'];
//		$objNF->bairro = $dados['enderecos']['bairro'];
//		$objNF->cep = $dados['enderecos']['cep'];
//		$objNF->cidade = $dados['enderecos']['cidade'];
//		$objNF->fone = $dados['enderecos']['fone1DDD'];
//		$objNF->uf = $dados['enderecos']['uf'];
//		$objNF->inscrEst = ( $dados['enderecos']['ie'] != '' ? $ie = $dados['documentos']['ie'] : $ie = $dados['documentos']['rg'] );
		$objNF->inscrEst = ( $dados['documentos']['ie'] != '' ?  $dados['documentos']['ie'] : $dados['documentos']['rg'] );

		$objNF->obs = "";
		$objNF->status = "A";
		$objNF->natOper = "";
		$objNF->ISSQN = $dadosNota[0]->ISSQN;
			
		$objNF->gravarNota();
	}
	elseif ( substr( $tipo, 0, 6) == 'listar' ){
//		$tab = array( $tb['NotaFiscal'], $tb['Pessoas'], $tb['PessoasTipos'] );
		$tab = array("$tb[NotaFiscal] INNER JOIN $tb[PessoasTipos] ON ($tb[NotaFiscal].idPessoaTipo = $tb[PessoasTipos].id) " .
					 "INNER JOIN $tb[Pessoas] ON ($tb[PessoasTipos].idPessoa = $tb[Pessoas].id)");
		$fields = array( "$tb[NotaFiscal].id", "$tb[NotaFiscal].idPessoaTipo", "$tb[NotaFiscal].numNF", "IF($tb[Pessoas].razao != '',$tb[Pessoas].razao, $tb[Pessoas].nome) cliente, $tb[NotaFiscal].status status" );
		
		if ($tipo == "listar" )
			$where = array( "$tb[NotaFiscal].idPessoaTipo = $tb[PessoasTipos].id", "$tb[PessoasTipos].idPessoa = $tb[Pessoas].id", "$tb[NotaFiscal].status = 'A'");
		elseif ( $tipo == "listarImpressa" )
			$where = array( "$tb[NotaFiscal].idPessoaTipo = $tb[PessoasTipos].id", "$tb[PessoasTipos].idPessoa = $tb[Pessoas].id", "$tb[NotaFiscal].status = 'I'");
		elseif ( $tipo == "listarCancelada")
			$where = array( "$tb[NotaFiscal].idPessoaTipo = $tb[PessoasTipos].id", "$tb[PessoasTipos].idPessoa = $tb[Pessoas].id", "$tb[NotaFiscal].status = 'C'");
		elseif ( $tipo == "listarTodas")
			$where = array( "$tb[NotaFiscal].idPessoaTipo = $tb[PessoasTipos].id", "$tb[PessoasTipos].idPessoa = $tb[Pessoas].id");
		elseif ($tipo == "listarFiltro"){
			
			if($_POST['criterio'] == "nf"){
				$where = array("$tb[NotaFiscal].numNF = " . $_POST['pesquisar']);
			}
			elseif($_POST['criterio'] == "cliente"){
				$where = array("IF($tb[Pessoas].razao != '',$tb[Pessoas].razao, $tb[Pessoas].nome) LIKE '%" . $_POST['pesquisar'] . "%'");
			}
			else{
				$where = array();
			}
		}
		return $objNF->seleciona($tab, $fields, $where, '', array( "numNF" ) );
	}
	elseif ($tipo == 'procurar'){
		$matriz[dtInicial]=formatarData($matriz[dtInicial]);
		$matriz[dtFinal]=formatarData($matriz[dtFinal]);
		if(!empty ($matriz[dtInicial])) $dtInicial=substr($matriz[dtInicial],2,4)."-".substr($matriz[dtInicial],0,2).'-01 00:00:00';
		if(!empty ($matriz[dtFinal])) $dtFinal=substr($matriz[dtFinal],2,4)."-".substr($matriz[dtFinal],0,2).'-'.dataDiasMes(substr($matriz[dtFinal],0,2))." 23:59:59";	
		
		$fields = array( "$tb[NotaFiscal].id", "$tb[NotaFiscal].idPessoaTipo", "$tb[NotaFiscal].numNF", "IF($tb[Pessoas].razao != '',$tb[Pessoas].razao, $tb[Pessoas].nome) cliente, $tb[NotaFiscal].status status" );
		$tab = array( $tb['NotaFiscal'], $tb['Pessoas'], $tb['PessoasTipos'] );
		$where = array( "$tb[NotaFiscal].idPessoaTipo = $tb[PessoasTipos].id", "$tb[PessoasTipos].idPessoa = $tb[Pessoas].id");
		if (!empty($dtInicial) && !empty ($dtFinal)) $where[] = "$tb[NotaFiscal].dtEmissao BETWEEN '$dtInicial'  AND  '$dtFinal' ";
		elseif (!empty($dtInicial)) $where[] = " $tb[NotaFiscal].dtEmissao >= '$dtInicial' ";
		elseif (!empty($dtFinal)) $where[] = " $tb[NotaFiscal].dtEmissao <= '$dtFinal'";
		if (!empty($matriz[idPessoaTipo])) $where[] = " $tb[NotaFiscal].idPessoaTipo = $matriz[idPessoaTipo]";
 					
		return $objNF->seleciona($tab, $fields, $where, '', array( "numNF" ) );
	}
	
	elseif ( $tipo == 'ver'){
		return $objNF->seleciona();
	}
	elseif ( $tipo == 'custom' ){
		return $objNF->seleciona('','', array( "id = ".$objNF->getId() ) );	
	}
	elseif ( $tipo == 'lastId' ){
		return $objNF->seleciona('', array( "Max(id) id" ), array( "idPessoaTipo = ".$matriz['idPessoaTipo'] ) );
	}
	elseif ( $tipo == 'calcularNota'){
		return $objNF->calculaTotalNota();
	}
	elseif($tipo == 'consultarISSQN'){
		$sql = "SELECT $tb[NotaFiscal].ISSQN FROM $tb[NotaFiscal] 
				WHERE $tb[NotaFiscal].id = $objNF->id";
		$resultado = consultaSQL($sql, $conn);
		$ISSQN = mysql_fetch_row($resultado);
		return $ISSQN;
	}
}

function notaFiscalVer ( $modulo, $sub, $acao, $registro, $matriz ){
	global $corFundo, $corBorda;
	
	$matriz['id'] = $registro;
	$dadosNF = dbNotaFiscal( $matriz, 'custom' );
	
	echo "<br>";
	novaTabela2( "[Nota Fiscal - ".($acao == 'verSemItem'? 'Ver' : $acao)."]", "center", "100%", 0, 2,1, $corFundo, $corBorda, 2);
		menuOpcAdicional( $modulo, $sub, $acao, $registro );
		novaLinhaTabela( $corFundo, '100%' );
			itemLinhaTMNOURL( " Nota Fiscal:", 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1' );
			itemLinhaTMNOURL( $dadosNF[0]->numNF, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1' );
		fechaLinhaTabela();
		mostraCliente( $dadosNF[0]->idPessoaTipo, $dadosNF[0] );
		
		getCampo( 'combo', 'Pop de Faturamento', '', formSelectPOP( $dadosNF[0]->idPop, '', 'check') );
		
		novaLinhaTabela( $corFundo, '100%' );
			itemLinhaTMNOURL( "Situação:", 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1' );
			if ( $dadosNF[0]->status == "A" ) $situacao = "Aberta";
			if ( $dadosNF[0]->status == "C" ) $situacao = "Cancelada";
			if ( $dadosNF[0]->status == "I" ) $situacao = "Impressa";
			
			itemLinhaTMNOURL( $situacao, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1' );
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL("Data de Emissão:", 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1' );
				itemLinhaTMNOURL( converteData($dadosNF[0]->dtEmissao, "banco", "form"), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1' );
		fechaLinhaTabela();
	fechaTabela();
	if ($acao != 'verSemItem'){
		if ($dadosNF[0]->status != 'C' && $dadosNF[0]->status != 'I' ){
			mostrarItensNF( $modulo, $sub, $acao, $registro, $matriz);
		}
		else 
			mostrarItensNF( $modulo, $sub, 'listarItens', $registro, $matriz);
	}
}

function alterarDtEmissao($modulo, $sub, $acao, $registro, $matriz){
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $conn, $tb;
	$matriz['consulta'] = "
		SELECT SUM($tb[ItensNF].qtde * $tb[ItensNF].valorUnit) FROM $tb[ItensNF] 
		WHERE idNF = $registro;
	";
	# obtendo total do ítem da nota para fazer o teste de comparação
	$consulta = consultaSQL($matriz['consulta'], $conn);
	$valorTotalNF = mysql_fetch_array($consulta);
	
	# verificando se o cliente possui os impostos cadastrados
	$impostos = listarImpostosNF($registro);
	
	# Se valor da nota é maior que R$ 5000,00, chamar formulário de confirmação de inclusão
	# dos impostos da nota fiscal. Caso contrário, solicitar a impressão da nota sem imprimir
	$parametros = carregaParametrosConfig();
	if(($valorTotalNF[0] >= $parametros['valor_min_imposto_nf']) && (mysql_num_rows($impostos) > 0)){
		$acao = 'confirmTributosNF';
	}
	else{
		$acao = 'imprimir';
	}
	
	novaTabela2( "[Data de Emissão]", "center", "100%", 0, 2,1, $corFundo, $corBorda, 2);
		
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro value=$registro>
				<input type=hidden name=matriz value=$matriz>";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
			
			novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<input type=radio name=matriz[emissao] value=auto checked>
				Utilizar data do sistema. &nbsp;', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					$texto="";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<input type=radio name=matriz[emissao] value=manual> Especificar Manualmente: ', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					$texto="<input type=text name=matriz[dtEmissao] onBlur=verificaData(this.value,7)>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit name=matriz[bntEnviar] value='Enviar' class=submit>";
					itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
			
}

// Função para definir o lançamento das notas de acordo com as condições de tributação

function confirmTributosNF($modulo, $sub, $acao, $registro, $matriz){
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $conn, $tb;
		
	#########################################################################################
	# CONSULTAR TRIBUTOS ARRECADADOS PELO CLIENTE											#
	# MONTAR TABELA COM A LISTAGEM DE TODOS O TRIBUTOS QUE O CLIENTE DESEJA CADASTRAR		#
	# DEFINIR CASO O CLIENTE DESEJE INCLUIR E DESCONTAR OS IMPOSTOS NA NOTA, ENVIAR NOVA	#
	# AÇÃO. CASO CONTRÁRIO, SOLICITAR IMPRESSÃO DA NOTA USANDO O MODELO DE NOTA FISCAL D	#
	# INCLUSO NA CLASSE ImpressaoNFModeloD													#
	#########################################################################################
	echo "<br>";
	$acao = 'imprimir';
	novaTabela2("[Confirmar Tributos]", 'center', '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		
		$texto ="
			<form method='post' name='matriz' action='index.php'>
				<input type='hidden' name='modulo' value='$modulo'>
				<input type='hidden' name='sub' value='$sub'>
				<input type='hidden' name='acao' value='$acao'>
				<input type='hidden' name='registro' value='$registro'>
				<input type='hidden' name='matriz' value='$registro'>
		";
		novaLinhaTabela($corFundo, '100%');
			itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1', 1);
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			$texto = "
			<span class='bold10'>
			Selecione os impostos que deseja aplicar na Nota Fiscal.
			</span>
			";
			itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1', 1);		
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			echo "<td align='center' colspan='3' class='tabfundo1'>";
			tabListarTributosNF($registro, 'listar');
			echo "</td>";
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1', 1);
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			$texto = "<input type='submit' name='matriz[botao]' class='submit' value='Confirmar'>";
			itemLinhaForm($texto, 'center', 'middle', $corFundo, 1, 'tabfundo1');
		fechaLinhaTabela();
		fechaFormulario();
	fechaTabela();
}// fim da função confirmTributosNF

### FUNÇÃO ADICIONAL PARA LISTAR OS IMPOSTOS A SEREM IMPRESSOS E DESCONTADOS NA NOTA FISCAL ###

function tabListarTributosNF($registro){
	
	global $corFundo, $corBorda, $html, $tb, $conn;
	$corGravata = 'tabfundo0';
	$corDetalhe = 'tabfundo1';
	$gravata = array("Incluir", "Imposto", "Taxa (%)");
	$largura = array('33%', '34%', '33%');
	$alinhamento = array('center', 'center', 'center');
	
	novaTabela('[Tributos]', 'center', '30%', 0, 2, 1, $corFundo, $corBorda, 3);
		novaLinhaTabela('100%', $corFundo);
			for($x = 0; $x < count($largura); $x ++){
				itemLinhaTMNOURL($gravata[$x], $alinhamento[$x], 'middle', $largura[$x], $corFundo, 0, $corGravata, 'tabfundo1');
			}
			fechaLinhaTabela();
				#consultando tributos
				$consulta = listarImpostosNF($registro);
				
				#montando lista com os resultados da consulta
				for($i = 0; $i < mysql_num_rows($consulta); $i ++){
					$dados = mysql_fetch_array($consulta);
					echo "<tr bgcolor='$corFundo'>";
					echo "<td align='center' class='tabfundo01' valign='middle'>";
					echo "<input type='checkbox' name='matriz[impostosNF][$i]' value='$dados[tipo]'>";
					echo "</td>";
					echo "<td align='center' class='tabfundo01' valing='middle'>$dados[tipo]</td>";
					echo "<td align='center' class='tabfundo01' valing='middle'>".formatarValoresForm($dados['valor'])."</td>";
					echo "</tr>";
				}
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				echo "<td align='left'class='tabfundo01' valign='middle' colspan='3'>";
				echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<input type='checkbox' name='todas' id='todas' onClick='selecionarTodos(this.checked)'>";
				echo "&nbsp;";
				echo "<font size='2'>Incluir Todos</font>";
				echo "</td>";
			fechaLinhaTabela();
		fechaLinhaTabela();
	fechaTabela();
}


function formNotaFiscalDescontarISS ($modulo, $sub, $acao, $registro, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro;
	echo "<br>";
	novaTabela2("[Atenção]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			#menuOpcAdicional($modulo, $sub, $acao, $registro);
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
				$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=matriz[tipoEndereco] value=$matriz[tipoEndereco]>
				<input type=hidden name=matriz[idPessoaTipo] value=$matriz[idPessoaTipo]>
				<input type=hidden name=matriz[idPop] value=$matriz[idPop]>
				<input type=hidden name=matriz[bntConfirmar] value=$matriz[bntAdicionar]>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			getCampo('combo', '<b>Atenção</b>', '', 'As informações cadastrais deste cliente informam que ele recolhe o imposto ISSQN.');
			
			$campo = $texto=formSelectSimNao($matriz['recolherISS'], 'recolherISS', 'form');
			getCampo('combo', 'Adicionar Imposto', '', $campo);
			
			getBotao('matriz[bntConfirmar2]', 'Continuar', 'submit');
	
		fechaTabela();
}

# função adicional para retornar todos os registros de impostos de uma NF específica

function listarImpostosNF($registro){
	
	global $conn, $tb;
	
	#verificando o tipo de listagem a ser executada
	$sql = "
			SELECT $tb[TiposImpostos].tipo, $tb[ImpostosPessoas].valor 
			FROM $tb[NotaFiscal] INNER JOIN $tb[PessoasTipos] 
			ON ($tb[PessoasTipos].id = $tb[NotaFiscal].idPessoaTipo) 
			INNER JOIN $tb[Pessoas] 
			ON ($tb[Pessoas].id = $tb[PessoasTipos].idPessoa) 
			INNER JOIN $tb[ImpostosPessoas] 
			ON ($tb[ImpostosPessoas].idPessoa = $tb[Pessoas].id) 
			INNER JOIN $tb[TiposImpostos] 
			ON ($tb[TiposImpostos].id = $tb[ImpostosPessoas].idTipoImposto) 
			WHERE $tb[NotaFiscal].id = $registro 
			AND ($tb[TiposImpostos].tipo = 'PIS' OR 
			$tb[TiposImpostos].tipo = 'COFINS' OR 
			$tb[TiposImpostos].tipo = 'CSSL' OR 
			$tb[TiposImpostos].tipo = 'IRRF')";
	echo "<br>";
	$resultado = consultaSQL($sql, $conn);
	return $resultado;
}
// FIM DA FUNÇÃO ADICIONAL

	/*
function formAplicarDescontos($modulo, $sub, $acao, $registro, $matriz) {
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro;
	
	$x['id'] = $registro;
	$nota = dbNotaFiscal($x, 'custom');
	
	echo "<br>";
		novaTabela2("[Atenção]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro value=$registro>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				
				$campo = formSelectPlanos('form', 'plano', 0, "idPessoaTipo='".$nota[0]->idPesssoaTipo."'");
				$txt = '<b>Selecione o plano</b><br> Selecione o plano em que será aplicado o desconto';
				getCampo('combo', $txt, '', $campo );
				
				getCampo('text', 'Data Vencimento Desconto', 'dtVencimento', '', 'onBlur=verificaData(this.value,3)' );
				
						
		fechaTabela();
}*/
	# Função para confirmação de aplicação de descontos da Nota Fical recém-impressa
	function confirmAplicarDesconto($modulo, $sub, $matriz){
		global $corFundo, $corBorda, $html;
		
			# Montando formulário de confirmação da aplicação de desconto no faturamento
			echo "<br>";
			novaTabela("[Confirmar Aplicação dos Descontos]<a name='ancora'></a>", 'center', '100%', 0, 2, 1, $corFundo, $corBorda, 1);
				$texto = "<form method=post  name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=matriz[id] value=$matriz[id]>
					<input type=hidden name=acao value=aplicarDesconto>";
				novaLinhaTabela($corFundo, '100%');
					$texto .= "Deseja aplicar o desconto no faturamento da Nota recém-impressa?
					<br>
					<p align='center'>
					<input type='submit' name=matriz[bntAplicarDesconto] value='Sim' class='submit'>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp&nbsp;
					<input type='submit' name=matriz[bntAplicarDesconto] value='Não' class='submit'>
					</p>";
					itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				fechaFormulario();
			fechaTabela();
		
	} // fim da função 
	
# Função para aplicar o desconto no faturamento caso o usuário tenha confirmado
function formAplicarDescontoNF($modulo, $sub, $acao, $registro, $matriz){
		
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $tb, $conn;
		
	dbNotaFiscal($matriz, 'custom');
		
	# Consultando itens da nota e seus respectivos impostos para realizar os cálculos
	# necessários
		
	$sql = "SELECT ($tb[ItensNF].qtde * $tb[ItensNF].valorUnit)
			FROM $tb[ItensNF] WHERE $tb[ItensNF].idNF = $matriz[id]";
	$resultado = mysql_query($sql, $conn);
	$totalItens = mysql_fetch_row($resultado);
	$tributo = dbImpostosNF($matriz, 'consultarDescricaoValor');
	// Buscando impostos registrados na NF
	$totalDesconto = 0;
		
	# listando impostos inclusive com IRRF
	for($i = 0; $i < mysql_num_rows($tributo); $i ++){
			
		$dadosTributos = mysql_fetch_array($tributo);
		$descricao = str_replace('Desconto do ', '', $dadosTributos['descricao']);
		$espaco = strlen($descricao) - 8;
		$descricao = substr_replace($descricao, ' ', $espaco, strlen($descricao));
		$itemDesconto = $totalItens[0] * ($dadosTributos['porcentagem'] / 100);
		$tributos .= $descricao.": R$ ".formatarValoresForm($itemDesconto)."<br>";
		$totalDesconto += $itemDesconto;
	}
		
	# Obtendo e valor total da Nota e ISSQN
	$valorNota = dbNotaFiscal($matriz, 'calcularNota');
	$ISSQN = dbNotaFiscal($matriz, 'consultarISSQN');
		
	$descISSQN = $valorNota[0]->valor * ($ISSQN[0] / 100);
	$totalDesconto += $descISSQN;
	$tributos .= "ISSQN: R$ ".formatarValoresForm($descISSQN);
		
	if($matriz['bntConfDesconto']){
		echo "<br>";
		avisoNOURL("Atenção", "Favor preencher todos os campos.", 400);
	}
			
	#montando tabela de aplicação dos impostos
	echo "<br>";
		novaTabela2("[Aplicar Descontos ao Faturamento]<a name='ancora'></a>", 'center', '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			novaLinhaTabela($corFundo, '100%');
				$texto = "<form name='matriz' method='post' action='index.php'>
						  <input type='hidden' name='modulo' value=$modulo>
						  <input type='hidden' name='sub' value=$sub>
						  <input type='hidden' name='acao' value=$acao>
						  <input type='hidden' name='matriz[bntAplicarDesconto]' value='Sim'>
						  <input type='hidden' name='matriz[id]' value=$matriz[id]
						  <input type='hidden' name='matriz[totalDesconto]' value=$totalDesconto>";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
				getCampo('combo', 'Valor da nota', '', 'R$ '.formatarValoresForm($valorNota[0]->valor));
				getCampo('combo', 'Descontos Ativos', '', $tributos);
				getCampo('combo', '<b>Total Descontos</b>', '', 'R$ '.formatarValoresForm($totalDesconto));
				novaLinhaTabela($corFundo, '100%');
					itemLinhaNOURL('&nbsp;','left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				if($totalDesconto > 0){
					$campo = formSelectPlanosNF('form', 'idPlano', 0, $matriz['id']);
					$txt = '<b>Selecione o plano</b><br>Selecione o plano em que será aplicado o desconto';
					getCampo('combo', $txt, '', $campo);
					getCampo('text', 'Mês/Ano para o Desconto', 'matriz[dtVencimento]', '', 'onBlur=verificaDataMesAno(this.value, 5)', 'form', '10');
					getBotao('matriz[bntConfDesconto]', 'Confirmar');
				}
		fechaTabela();		
}

function aplicarDescontoFaturamento( $modulo, $sub, $acao, $registro, $matriz ){
	
	/*
	* Caso o usuário tenha feito a aplicação dos descontos, executar as rotinas
	* necessárias para aplicar o desconto
	*/
	if($matriz['bntConfDesconto'] == 'Confirmar' && $matriz['idPlano'] && $matriz['dtVencimento']){
		$salva = salvarDescontoFaturamento($matriz['idPlano'], $matriz['totalDesconto'], $matriz['dtVencimento'], $desc);
		if($salva){
			echo "<br>";
			avisoNOURL("Atençao:", "Desconto aplicado com sucesso", 400);
		}
		else{
			echo "<br>";
			avisoNOURL("Erro:", "Erro ao aplicar desconto", 400);
		}
		notaFiscalListar($modulo, $sub, 'listar', '', $matriz);		
	}
	else{		
		formAplicarDescontoNF( $modulo, $sub, $acao, $registro, $matriz);
	}
}

/**
 * Função responsável em montar um formulário de filtragem para buscar registros específicos de 
 * notas fiscais em uma lista
 * 
 * @author Felipe dos S. Assis
 * @since 08/10/2008
 */
function formFiltrarNF($modulo, $sub, $acao, $registro, $matriz){
	
	global $corFundo, $corBorda;
	
	novaTabela2("[Procurar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
			novaLinhaTabela($corFundo, '30%');
				htmlAbreColuna('100%', 'center', $corFundo, 5, 'normal');
				novaTabela2SH("center", '100%', 0, 2, 0, $corFundo, $corBorda, 5);
					novaLinhaTabela($corFundo, '100%');
						$form = "<form name=\"matriz\" id=\"matriz\" action=\"index.php\" method=\"post\">" .
								"<input type=\"hidden\" name=\"modulo\" id=\"modulo\" value=\"$modulo\">" .
								"<input type=\"hidden\" name=\"sub\" id=\"sub\" value=\"$sub\">" .
								"<input type=\"hidden\" name=\"acao\" id=\"acao\" value=\"$acao\">" .
								"<input type=\"hidden\" name=\"registro\" id=\"registro\" value=\"$registro\">";
						itemLinhaTabela("Procurar: ", "right", '10%', 'normal');
						$form = "<input type=\"text\" name=\"pesquisar\" id=\"pesquisar\" size=\"60\">";
						itemLinhaTabela($form, "right", '50%', 'normal');
						itemLinhaTabela("Por:", "right", '5%', 'normal');
						$form = "<select name=\"criterio\" id=\"criterio\">" .
								"<option value=\"\">Selecione um crit&eacute;rio</option>" .
								"<option value=\"nf\">Nº da Nota</option>" .
								"<option value=\"cliente\">Cliente</option>" .
								"</select>";
						itemLinhaTabela($form, "left", '30%', 'normal');
						$form = "<input type=\"submit\" name=\"btOk\" id=\"btOk\" class=\"submit\" value=\"OK\">" .
								"</form>";
						itemLinhaTabela($form, "center", '5%', 'normal');
					fechaLinhaTabela();
				fechaTabela();
				htmlFechaColuna();
			fechaLinhaTabela();
	fechaTabela();
	echo "<br>";
}
?>