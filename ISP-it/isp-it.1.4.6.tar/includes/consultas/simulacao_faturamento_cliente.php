<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 15/04/2004
# Ultima alteração: 15/04/2004
#    Alteração No.: 001
#
# Função:
#    Consulta Simulação de Faturamento por Cliente


# função para form de seleção de filtros de faturamento
function formSimulacaoFaturamentoCliente($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $sessLogin;
	
		
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		# Motrar tabela de busca
		novaTabela2("[Simulação de Faturamento por Cliente]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			menuOpcAdicional($modulo, $sub, $acao, $registro);
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro>";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Busca por Cliente:</b><br>
				<span class=normal10>Informe nome ou dados do cliente para busca</span>', 'right', 'middle', '35%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[txtProcurar] size=60 value='$matriz[txtProcurar]'> <input type=submit value=Procurar name=matriz[bntProcurar] class=submit>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
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
						itemLinhaTMNOURL('<b>Clientes encontrados:</b><br>
						<span class=normal10>Selecione o Cliente</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
						itemLinhaForm(formSelectConsulta($consulta, 'nome', 'idPessoaTipo', 'idPessoaTipo', $matriz[idPessoaTipo]), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>Mês/Ano de Referência:</b><br>
						<span class=normal10>Selecione o mês de referência</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
						itemLinhaForm(formSelectMes($matriz[mes],'mes','form').formSelectAno($matriz[ano],'ano','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
					novaLinhaTabela($corFundo, '100%');
						$texto="<input type=submit name=matriz[bntConfirmar] value='Consultar' class=submit>";
						itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
					fechaLinhaTabela();
					
				}
			}
		
			htmlFechaLinha();
		fechaTabela();
	}
	
}


# Função para consultar de Simulação de Faturamento
function consultaSimulacaoFaturamentoCliente($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $corFundo, $corBorda, $html, $tb;
	# Procedimentos
	# 1-Consultar todos os planos Ativos do Cliente
	# 2--> Consultar Servicos cadastrados/ativos com dtInicial>=mes/ano informados
	# 2--> Consultar Servicos ativos no plano
	# 3---> Consultar Servicos Adicionais do Serviço do Plano (ativos)
	# 4---> Consultar Descontos do Serviço do Plano (ativos)
	
	# Calcular a data inicial para consulta
	$tmpData=mktime(0,0,0,$matriz[mes],dataDiasMes($matriz[mes]),$matriz[ano]);
	$dtCadastroPlano=date('Y-m-d',$tmpData);
	
	# 1-Consultar planos ativos
	$sql="
		SELECT
			$tb[PlanosPessoas].idPessoaTipo idPessoaTipo, 
			$tb[PlanosPessoas].idFormaCobranca idFormaCobranca, 
			$tb[PlanosPessoas].dtCadastro dtCadastro, 
			$tb[PlanosPessoas].especial especial, 
			$tb[PlanosPessoas].status status,
			$tb[Pessoas].nome nome,
			$tb[POP].nome pop,
			$tb[POP].id idPop
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
			AND $tb[PlanosPessoas].idPessoaTipo='$matriz[idPessoaTipo]'
		GROUP BY
			$tb[PessoasTipos].id
		ORDER BY
			$tb[Pessoas].idPOP,
			$tb[Pessoas].nome";
	
	if($sql) $consultaPlanosAtivos=consultaSQL($sql, $conn);
	
	if($consultaPlanosAtivos && contaconsulta($consultaPlanosAtivos) ) {
		
		# Cabeçalho
		echo "<br>";
		novaTabela("[Faturamento de Clientes]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			htmlAbreLinha($corFundo);
				itemLinhaTMNOURL('Detalhamento do Cliente', 'center', 'middle', '60%', $corFundo, 0, 'tabfundo0');
				itemLinhaTMNOURL('POP de Acesso', 'center', 'middle', '40%', $corFundo, 0, 'tabfundo0');
			htmlFechaLinha();
			
			# Listagem de Planos com servicos e totais por serviço
			for($a=0;$a<contaConsulta($consultaPlanosAtivos);$a++) {
				# Consultar Planos da pessoa
				$idPessoaTipo=resultadoSQL($consultaPlanosAtivos, $a, 'idPessoaTipo');
				$idFormaCobranca=resultadoSQL($consultaPlanosAtivos, $a, 'idFormaCobranca');
				$nomePessoa=resultadoSQL($consultaPlanosAtivos, $a, 'nome');
				$nomePOP=resultadoSQL($consultaPlanosAtivos, $a, 'pop');
				$idPop=resultadoSQL($consultaPlanosAtivos, $a, 'idPop');
				
				# Mostrar Cliente
				
				htmlAbreLinha($corFundo);
					itemLinhaTMNOURL("<b>$nomePessoa</b>", 'center', 'middle', '60%', $corFundo, 0, 'tabfundo0');
					itemLinhaTMNOURL($nomePOP, 'center', 'middle', '40%', $corFundo, 0, 'bold10');
				htmlFechaLinha();
				htmlAbreLinha($corFundo);
					htmlAbreColunaForm('100%', 'center', 'middle', $corFundo, 2, 'normal10');
					novaTabelaSH("center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
					
					# Consultar Planos ativos para a pessoa
					if($idPessoaTipo) {
						$sql="
							SELECT
								$tb[PlanosPessoas].idPessoaTipo idPessoaTipo, 
								$tb[PlanosPessoas].id idPlano, 
								$tb[PlanosPessoas].status status, 
								$tb[PlanosPessoas].nome nome,
								$tb[PlanosPessoas].idFormaCobranca idFormaCobranca, 
								$tb[PlanosPessoas].idVencimento idVencimento, 
								$tb[PlanosPessoas].dtCadastro dtCadastro, 
								$tb[PlanosPessoas].especial especial
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
							GROUP BY
								$tb[PlanosPessoas].id";
								
						$consulta=consultaSQL($sql, $conn);
						
						if($consulta && contaConsulta($consulta)>0) {
							# Zerar total do cliente
							$totalCliente=0;
							
							# Procurar os serviços do plano para totalização
							for($b=0;$b<contaConsulta($consulta);$b++) {
								# Plano a ser selecionado e detalhado
								$idPlano=resultadoSQL($consulta, $b, 'idPlano');
								$dtCadastro=resultadoSQL($consulta, $b, 'dtCadastro');
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
									$totalPlano=valorPlano($idPlano, $especial, $idVencimento, $sqlADDPlano, $matriz[mes], $matriz[ano], $matriz);
									if(!$totalPlano) $totalPlano=valorPlanoCanceladoInativo($idPlano, $especial, $idVencimento, $sqlADDPlano, $matriz[mes], $matriz[ano], $matriz);
								}
								else {
									$totalPlano=valorPlanoCanceladoInativo($idPlano, $especial, $idVencimento, $sqlADDPlano, $matriz[mes], $matriz[ano], $matriz);
								}
								
								# Formatar valor com URL de calculo do plano
								if($totalPlano > 0) {
									$valorFormatado=formatarValoresForm($totalPlano);
									$valorFormatado=htmlMontaOpcao("<a href=?modulo=lancamentos&sub=planos&acao=calculo&registro=$idPlano&matriz[mes]=$matriz[mes]&matriz[ano]=$matriz[ano]>$valorFormatado</a>",'lancamento');
									
									$totalCliente+=$totalPlano;
									
									# Mostrar resultado
									htmlAbreLinha($corFundo);
										itemLinhaTMNOURL($nome, 'left', 'middle', '35%', $corFundo, 0, 'normal10');
										itemLinhaTMNOURL("Venc.: ".$vencimento[descricao], 'center', 'middle', '15%', $corFundo, 0, 'normal10');
										itemLinhaTMNOURL(formSelectFormaCobranca($idFormaCobranca,'','check'), 'center', 'middle', '20%', $corFundo, 0, 'normal10');
										itemLinhaTMNOURL($tipoPlano, 'center', 'middle', '15%', $corFundo, 0, 'normal10');
										itemLinhaTMNOURL($valorFormatado, 'center', 'middle', '15%', $corFundo, 0, 'normal10');
									htmlFechaLinha();
								}
								
							}

							# Totalizar Cliente
							htmlAbreLinha($corFundo);
								itemLinhaTMNOURL("<b>Total de Faturamento do Cliente</b>", 'right', 'middle', '75%', $corFundo, 4, 'tabfundo1');
								itemLinhaTMNOURL(formatarValoresForm($totalCliente), 'center', 'middle', '15%', $corFundo, 0, 'txtok');
							htmlFechaLinha();
							
							# Contabilizar Total geral	
							$totalGeral+=$totalCliente;
							
						}
						
					}
					
					fechaTabela();
					htmlFechaColuna();
				htmlFechaLinha();
			}

			# Mostrar Total Geral
			htmlAbreLinha($corFundo);
				itemLinhaTMNOURL("<b>Total Geral de Faturamento</b>", 'right', 'middle', '50%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL(formatarValoresForm($totalGeral), 'center', 'middle', '40%', $corFundo, 0, 'txtok');
			htmlFechaLinha();
			
		# Rodapé com totais
		fechaTabela();
		
		
		##
		# Procedimentos: 
		# OK - passar parametros normais modulo, sub, acao, registro, matriz
		# gerar função de geraçao de documentos de decobrança para 1 cliente específico.
		
		echo "
			<br>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=registro value=$registro>
			<input type=hidden name=matriz[vencimento] value=$idVencimento>
			<input type=hidden name=matriz[forma_cobranca] value=$idFormaCobranca>
		";
		formGerarFaturamento($modulo, $sub, $acao, $registro, $matriz);
		
		if($matriz[bntGerarCobranca]) {
			$matriz[idFaturamento]=novoIDFaturamento();
			$matriz[vencimento]=$idVencimento;
			$matriz[pop]=$idPop;
			gerarFaturamentoCliente($modulo, $sub, $acao, $registro, $matriz);
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