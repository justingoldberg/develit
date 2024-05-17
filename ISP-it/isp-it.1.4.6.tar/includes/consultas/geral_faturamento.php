<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 15/04/2004
# Ultima alteração: 15/04/2004
#    Alteração No.: 001
#
# Função:
#    Consulta Geral de Faturamento



# função para form de seleção de filtros de faturamento
function formGeralFaturamento($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $sessLogin;
	
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		# Motrar tabela de busca
		novaTabela2("[Consulta Geral de Faturamento]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			menuOpcAdicional($modulo, $sub, $acao, $registro);
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
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Serviço:</b><br>
				<span class=normal10>Selecione o Tipo de Serviço</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				if($matriz[servico_todos]) $opcServico='checked';
				$texto="<input type=checkbox name=matriz[servico_todos] value=S $opcServico><b>Todos</b>";
				itemLinhaForm(formSelectServicos($matriz[servico],'servico','multi').$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>POP:</b><br>
				<span class=normal10>Selecione o POP de Acesso</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				if($matriz[pop_todos]) $opcPOP='checked';
				$texto="<input type=checkbox name=matriz[pop_todos] value=S $opcPOP><b>Todos</b>";
				itemLinhaForm(formSelectPOP($matriz[pop],'pop','multi').$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Forma de Cobrança:</b><br>
				<span class=normal10>Selecione a Forma de Cobrança</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				if($matriz[forma_cobranca_todos]) $opcFormaCobranca='checked';
				$texto="<input type=checkbox name=matriz[forma_cobranca_todos] value=S $opcFormaCobranca><b>Todas</b>";
				itemLinhaForm(formSelectFormaCobranca($matriz[forma_cobranca],'forma_cobranca','form').$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Vencimento:</b><br>
				<span class=normal10>Selecione o Vencimento</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				if($matriz[vencimento_todos]) $opcVencimento='checked';
				$texto="<input type=checkbox name=matriz[vencimento_todos] value=S $opcVencimento><b>Todos</b>";
				itemLinhaForm(formSelectVencimento($matriz[vencimento],'vencimento','form').$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Mês/Ano de Referência:</b><br>
				<span class=normal10>Selecione o Mês de referência</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaForm(formSelectMes($matriz[mes],'mes','form').formSelectAno($matriz[ano],'ano','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Detalhar Consulta:</b><br>
				<span class=normal10>Selecione esta opção para obter o detalhamento dos serviços</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				if($matriz[detalhar]=='S') $opcDetalhar='checked';
				$texto="<input type=checkbox name=matriz[detalhar] value='S' $opcDetalhar>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
			formPessoaSubmit($modulo, $sub, $acao, $registro, $matriz);
		
			htmlFechaLinha();
		fechaTabela();
	}
	
}



# Função para consultar de Simulação de Faturamento
function consultaGeralFaturamento($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $corFundo, $corBorda, $html, $tb;
	# Procedimentos
	# 1-Consultar todos os planos Ativos 
	# 2--> Consultar Servicos cadastrados/ativos com dtInicial>=mes/ano informados
	# 2--> Consultar Servicos ativos no plano
	# 3---> Consultar Servicos Adicionais do Serviço do Plano (ativos)
	# 4---> Consultar Descontos do Serviço do Plano (ativos)
	
	# Calcular a data inicial para consulta
	$tmpData=mktime(0,0,0,$matriz[mes],31,$matriz[ano]);
	$dtCadastroPlano=date('Y-m-d',$tmpData);
	
	# 1-Consultar planos ativos
	if(!$matriz[forma_cobranca_todos]) $sqlADD.=" AND $tb[PlanosPessoas].idFormaCobranca = '$matriz[forma_cobranca]' ";
	if(!$matriz[vencimento_todos]) {
		$sqlADD.=" AND $tb[PlanosPessoas].idVencimento = '$matriz[vencimento]' ";
		$sqlADDPlano.=" AND $tb[PlanosPessoas].idVencimento = '$matriz[vencimento]' ";
	}
	
	# Montar parametros de tipo de serviço
	$a=0;
	if($matriz[servico]) {
		$sqlADDServico="AND (";
		while($matriz[servico][$a]) {
			$sqlADDServico.=" $tb[ServicosPlanos].idServico = ".$matriz[servico][$a];
			
			if($matriz[servico][$a+1]) $sqlADDServico .= " OR ";
			
			$a++;
		}
		$sqlADDServico.=")";
	}
	
	$a=0;
	if($matriz[pop]) {
		$sqlADDPOP="AND (";
		while($matriz[pop][$a]) {
			$sqlADDPOP.=" $tb[Pessoas].idPOP = ".$matriz[pop][$a];
			
			if($matriz[pop][$a+1]) $sqlADDPOP.= " OR ";
			
			$a++;
		}
		$sqlADDPOP.=")";
	}


	$sql="
		SELECT
			$tb[PlanosPessoas].idPessoaTipo idPessoaTipo, 
			$tb[PlanosPessoas].dtCadastro dtCadastro, 
			$tb[PlanosPessoas].especial especial, 
			$tb[PlanosPessoas].status status,
			$tb[Pessoas].nome nome,
			$tb[Pessoas].tipoPessoa tipoPessoa,
			$tb[POP].nome pop
		FROM 
			$tb[PlanosPessoas], 
			$tb[PessoasTipos],
			$tb[Pessoas],
			$tb[POP],
			$tb[ServicosPlanos]
		WHERE
			$tb[Pessoas].id = $tb[PessoasTipos].idPessoa
			AND $tb[PlanosPessoas].idPessoaTipo = $tb[PessoasTipos].id 
			AND $tb[PlanosPessoas].id = $tb[ServicosPlanos].idPlano
			and $tb[Pessoas].idPOP = $tb[POP].id
			$sqlADD
			$sqlADDServico
			$sqlADDPOP
		GROUP BY
			$tb[PessoasTipos].id
		ORDER BY
			$tb[Pessoas].idPOP,
			$tb[Pessoas].nome";
			
			
	if($sql) $consultaPlanosAtivos=consultaSQL($sql, $conn);
	
	if($consultaPlanosAtivos && contaconsulta($consultaPlanosAtivos) ) {
	
		if($matriz[arquivotexto]) {
		}
		else {
			# Cabeçalho
			echo "<br>";
			novaTabela("[Faturamento por Cliente]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 4);
			htmlAbreLinha($corFundo);
				itemLinhaTMNOURL("POP", 'center', 'middle', '20%', $corFundo, 0, 'tabfundo0');
				itemLinhaTMNOURL("Nome do Cliente", 'center', 'middle', '60%', $corFundo, 0, 'tabfundo0');
				itemLinhaTMNOURL("Tipo Pessoa", 'center', 'middle', '10%', $corFundo, 0, 'tabfundo0');
				itemLinhaTMNOURL("Valor Faturado", 'center', 'middle', '10%', $corFundo, 0, 'tabfundo0');
			htmlFechaLinha();
		}

		
		# Listagem de Planos com servicos e totais por serviço
		for($a=0;$a<contaConsulta($consultaPlanosAtivos);$a++) {
			# Consultar Planos da pessoa
			$idPessoaTipo=resultadoSQL($consultaPlanosAtivos, $a, 'idPessoaTipo');
			$nomePessoa=resultadoSQL($consultaPlanosAtivos, $a, 'nome');
			$tipoPessoa=resultadoSQL($consultaPlanosAtivos, $a, 'tipoPessoa');
			$nomePOP=resultadoSQL($consultaPlanosAtivos, $a, 'pop');
			# Mostrar Cliente
			
			# Consultar Planos ativos para a pessoa
			if($idPessoaTipo) {
				$sql="
					SELECT
						$tb[PlanosPessoas].idPessoaTipo idPessoaTipo, 
						$tb[PlanosPessoas].id idPlano, 
						$tb[PlanosPessoas].nome nome,
						$tb[PlanosPessoas].idFormaCobranca idFormaCobranca, 
						$tb[PlanosPessoas].idVencimento idVencimento, 
						$tb[PlanosPessoas].especial especial,
						$tb[PlanosPessoas].status status
					FROM 
						$tb[PlanosPessoas],
						$tb[Pessoas],
						$tb[PessoasTipos],
						$tb[ServicosPlanos]
					WHERE
						$tb[ServicosPlanos].idPlano = $tb[PlanosPessoas].id
						AND $tb[Pessoas].id=$tb[PessoasTipos].idPessoa
						AND $tb[PessoasTipos].id=$tb[PlanosPessoas].idPessoaTipo
						AND $tb[PlanosPessoas].idPessoaTipo=$idPessoaTipo
						$sqlADD
						$sqlADDServico
						$sqlADDPOP
					GROUP BY
						$tb[PlanosPessoas].id";
						
				$consulta=consultaSQL($sql, $conn);
				
				if($consulta && contaConsulta($consulta)>0) {
					# Zerar total do cliente
					$totalCliente=0;

					$matDetalhe='';
					# Procurar os serviços do plano para totalização
					for($b=0;$b<contaConsulta($consulta);$b++) {
						# Plano a ser selecionado e detalhado
						$idPlano=resultadoSQL($consulta, $b, 'idPlano');
						$status=resultadoSQL($consulta, $b, 'status');
						$nome=resultadoSQL($consulta, $b, 'nome');
						$nome=htmlMontaOpcao("<a href=?modulo=lancamentos&sub=planos&acao=abrir&registro=$idPlano>$nome</a>",'planos');
						$especial=resultadoSQL($consulta, $b, 'especial');
						if(!$especial || $especial=='N') $tipoPlano="<span class=txtok>Plano Normal</span>";
						else $tipoPlano="<span class=txtaviso>Plano Especial</span>";
						$idFormaCobranca=resultadoSQL($consulta, $b, 'idFormaCobranca');
						$idVencimento=resultadoSQL($consulta, $b, 'idVencimento');
						$vencimento=dadosVencimento($idVencimento);
						$idPessoaTipo=resultadoSQL($consulta, $b, 'idPessoaTipo');
						
						# Totalizar plano
						if($status=='A') {
							$matriz[colunas]=5;
							$totalPlano=valorPlano($idPlano, $especial, $idVencimento, $sqlADDPlano, $matriz[mes], $matriz[ano], $matriz);
							if(!$totalPlano) $totalPlano=valorPlanoCanceladoInativo($idPlano, $especial, $idVencimento, $sqlADDPlano, $matriz[mes], $matriz[ano], $matriz);
						}
						else {
							$totalPlano=valorPlanoCanceladoInativo($idPlano, $especial, $idVencimento, $sqlADDPlano, $matriz[mes], $matriz[ano], $matriz);
						}
						
						# Montar tabela de detalhamento
						if($matriz[detalhar]=='S') {
							$matDetalhe[$b][nome]=$nome;
							$matDetalhe[$b][status]=$status;
							$matDetalhe[$b][valor]=$totalPlano;
						}
						
						# Formatar valor com URL de calculo do plano
						$valorFormatado=formatarValoresForm($totalPlano);
						if(!$matriz[arquivotexto])
							$valorFormatado=htmlMontaOpcao("<a href=?modulo=lancamentos&sub=planos&acao=calculo&registro=$idPlano&matriz[mes]=$matriz[mes]&matriz[ano]=$matriz[ano]>$valorFormatado</a>",'lancamento');
						
						$totalCliente+=$totalPlano;
						
					}

					if($totalCliente>0) {
					
						if($matriz[arquivotexto]) {
							# arquivo texto
							htmlAbreLinha($corFundo);
								itemLinhaTMNOURL($nomePOP, 'center nowrap', 'middle', '20%', $corFundo, 0, 'bold8');
								itemLinhaTMNOURL($nomePessoa, 'left', 'middle', '60%', $corFundo, 0, 'normal10');
								itemLinhaTMNOURL(formSelectPessoaTipo($tipoPessoa,'','check'), 'center', 'middle', '10%', $corFundo, 0, 'normal8');
								itemLinhaTMNOURL(formatarValoresForm($totalCliente), 'center', 'middle', '20%', $corFundo, 0, 'txtok');
							htmlFechaLinha();
							if($matriz[detalhar]=='S') {
								$keys=array_keys($matDetalhe);
								htmlAbreLinha($corFundo);
									htmlAbreColunaForm('100%', 'center', 'middle', $corFundo, 4, 'normal10');		
										novaTabelaSH("center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
										for($c=0;$c<count($keys);$c++) {
											if($matDetalhe[$c][valor]>0) {
													htmlAbreLinha($corFundo);
														itemLinhaTMNOURL("<b>Plano:</b> ".$matDetalhe[$c][nome], 'left', 'middle', '60%', $corFundo, 0, 'normal10');
														itemLinhaTMNOURL(formSelectStatus($matDetalhe[$c][status],'','check'), 'center', 'middle', '20%', $corFundo, 0, 'normal10');
														itemLinhaTMNOURL(formatarValoresForm($matDetalhe[$c][valor]), 'center', 'middle', '20%', $corFundo, 0, 'txtok');
													htmlFechaLinha();
											}
										}
										fechaTabela();
									htmlFechaColuna();
								htmlFechaLinha();
								itemTabelaNOURL('&nbsp;', 'center', $corFundo, 4, 'tabfundo1');
							}
						}
						else {
							htmlAbreLinha($corFundo);
								itemLinhaTMNOURL($nomePOP, 'center nowrap', 'middle', '20%', $corFundo, 0, 'bold8');
								itemLinhaTMNOURL($nomePessoa, 'left', 'middle', '60%', $corFundo, 0, 'normal10');
								itemLinhaTMNOURL(formSelectPessoaTipo($tipoPessoa,'','check'), 'center', 'middle', '10%', $corFundo, 0, 'normal8');
								itemLinhaTMNOURL(formatarValoresForm($totalCliente), 'center', 'middle', '20%', $corFundo, 0, 'txtok');
							htmlFechaLinha();
							if($matriz[detalhar]=='S') {
								$keys=array_keys($matDetalhe);
								htmlAbreLinha($corFundo);
									htmlAbreColunaForm('100%', 'center', 'middle', $corFundo, 4, 'normal10');		
										novaTabelaSH("center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
										for($c=0;$c<count($keys);$c++) {
											if($matDetalhe[$c][valor]>0) {
													htmlAbreLinha($corFundo);
														itemLinhaTMNOURL("<b>Plano:</b> ".$matDetalhe[$c][nome], 'left', 'middle', '60%', $corFundo, 0, 'normal10');
														itemLinhaTMNOURL(formSelectStatus($matDetalhe[$c][status],'','check'), 'center', 'middle', '20%', $corFundo, 0, 'normal10');
														itemLinhaTMNOURL(formatarValoresForm($matDetalhe[$c][valor]), 'center', 'middle', '20%', $corFundo, 0, 'txtok');
													htmlFechaLinha();
											}
										}
										fechaTabela();
									htmlFechaColuna();
								htmlFechaLinha();
								itemTabelaNOURL('&nbsp;', 'center', $corFundo, 4, 'tabfundo1');
							}
						}
					}
							
					# Contabilizar Total geral	
					$totalGeral+=$totalCliente;
				}
			}

		}

		if($matriz[arquivotexto]) {
			# Rodapé com totais
			htmlAbreLinha($corFundo);
				htmlAbreColunaForm('100%', 'center', 'middle', $corFundo, 4, 'normal10');		
					novaTabelaSH("center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
						htmlAbreLinha($corFundo);
							itemLinhaTMNOURL("<b>Total Geral de Faturamento</b>", 'right', 'middle', '60%', $corFundo, 0, 'tabfundo1');
							itemLinhaTMNOURL(formatarValoresForm($totalGeral), 'center', 'middle', '40%', $corFundo, 0, 'txtok');
						htmlFechaLinha();
					fechaTabela();
				htmlFechaColuna();
			fechaLinhaTabela();
		}
		else {
			# Rodapé com totais
			htmlAbreLinha($corFundo);
				htmlAbreColunaForm('100%', 'center', 'middle', $corFundo, 4, 'normal10');		
					novaTabelaSH("center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
						htmlAbreLinha($corFundo);
							itemLinhaTMNOURL("<b>Total Geral de Faturamento</b>", 'right', 'middle', '60%', $corFundo, 0, 'tabfundo1');
							itemLinhaTMNOURL(formatarValoresForm($totalGeral), 'center', 'middle', '40%', $corFundo, 0, 'txtok');
						htmlFechaLinha();
					fechaTabela();
				htmlFechaColuna();
			fechaLinhaTabela();
		}		
	}
	else {
		# Verificar faturamento dos clientes
		itemTabelaNOURL('&nbsp;', 'right', $corFundo, 0, 'normal10');
		novaTabela("[Faturamento de Clientes]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 4);
			# Mensagem de alerta de faturamento não encontrado
			$msg="<span class=txtaviso>Não foram encontrados registros para processamento!</span>";
			itemTabelaNOURL($msg, 'left', $corFundo, 4, 'normal10');
		fechaTabela();
	}
	return(0);
}

?>
