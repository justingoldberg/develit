<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 10/09/2003
# Ultima alteração: 22/03/2004
#    Alteração No.: 015
#
# Função:
#    Painel - Funções para faturamento



# Lançamentos
function faturamento($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessPlanos;
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		if(!$matriz[tipoPessoa]) $matriz[tipoPessoa]='cli';

		if(!$acao) {
			# Topo da tabela - Informações e menu principal do Cadastro
			
			novaTabela2("[Faturamento]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 0);
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'left', $corFundo, 0, 'tabfundo1');
						echo "<br><img src=".$html[imagem][faturamentos]." border=0 align=left><b class=bold>Faturamento</b><br>
						 <span class=normal10>Manutenção de <b>faturamentos</b>. Esta seção possui funções de gerenciamento e
						 controle de faturamentos. Utilize a seção de <b>consultas</b> para obter dados estatísticos e realizar
						 simulações de faturamento.</span>";
					htmlFechaColuna();
				fechaLinhaTabela();
			fechaTabela();
			
			
			# Mostrar menu de faturamento
			itemTabelaNOURL("&nbsp;", 'left', $corFundo, 0, 'normal');
			
			novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
				novaTabela2SH("center", '100%', 0, 0, 0, $corFundo, $corBorda, 3);
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('50%', 'center" valign="top', $corFundo, 0, 'normal10');
							novaTabela2("[Geração de Faturamento]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 0);
								$texto=htmlMontaOpcao("Gerar novo Faturamento", 'faturamento');
								itemTabela($texto, "?modulo=faturamento&sub=geracao&acao=gerar", 'left', $corFundo, 0, 'normal');							
								$texto=htmlMontaOpcao("Faturamentos Gerados", 'listar');
								itemTabela($texto, "?modulo=faturamento&sub=geracao&acao=listar", 'left', $corFundo, 0, 'normal');							
							fechaTabela();
						htmlFechaColuna();
						
						itemLinhaTMNOURL('&nbsp;&nbsp;&nbsp;', 'center', 'middle', '1', $corFundo, 0, 'normal10');
						
						htmlAbreColuna('50%', 'center" valign="top', $corFundo, 0, 'normal10');
							novaTabela2("[Transferência de Arquivos]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 0);
								$texto=htmlMontaOpcao("Gerar Arquivo Remessa", 'arquivo');
								itemTabela($texto, "?modulo=faturamento&sub=arquivoremessa&acao=listar", 'left', $corFundo, 0, 'normal');
								$texto=htmlMontaOpcao("Arquivos Retorno", 'arquivo');
								itemTabela($texto, "?modulo=faturamento&sub=arquivoretorno&acao=listar", 'left', $corFundo, 0, 'normal');
								$texto=htmlMontaOpcao("Manutenção de Clientes em Débito Automático", 'cadastros');
								itemTabela($texto, "?modulo=faturamento&sub=debitoAutomatico&acao=listar", 'left', $corFundo, 0, 'normal');
								$texto=htmlMontaOpcao("Atributos de Configuração dos Arquivos Remessa", 'cadastros');
								itemTabela($texto, "?modulo=faturamento&sub=parametrosBancos&acao=listar", 'left', $corFundo, 0, 'normal');
							fechaTabela();
						htmlFechaColuna();
					fechaLinhaTabela();
				fechaTabela();
			htmlFechaColuna();
			
		}
		
		# Planos
		if($sub=="geracao") {
			
			# Faturamento Geral
			if($acao=='gerar' || !$acao) {

				formCalcularFaturamento($modulo, $sub, $acao, $registro, $matriz);
				
				if($matriz[bntConfirmar] || $matriz[bntGerarCobranca]) {
					$totalFaturamento=faturamentoGerar($modulo, $sub, $acao, $registro, $matriz);

					if($totalFaturamento>0) {
						# Mostrar form de geração de documentos
						echo "<br>";
						
						formGerarFaturamento($modulo, $sub, $acao, $registro, $matriz);
						
						# Verificar se confirmação de geração de cobrança foi selecionada
						if($matriz[bntGerarCobranca] && $matriz[descricao]) {
							$matriz[idFaturamento]=novoIDFaturamento();
							gerarFaturamento($modulo, $sub, $acao, $registro, $matriz);
						}
					}
				}
			}
			elseif($acao=='listar' || $acao=='listartodos' || $acao=='listarcancelados' || $acao=='listarativos') {
				listarFaturamentos($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='detalhes') {
				detalhesFaturamentos($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='cancelar') {
				cancelarFaturamentos($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='ativar') {
				ativarFaturamentos($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		# Documentos Gerados
		elseif($sub=='documentos') {
			
			if($acao=='detalhe') {
				detalhesDocumentosGerados($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		# Planos Documentos Gerados
		elseif($sub=='planos_documentos') {
			
			if($acao=='detalhe') {
				detalhesPlanosDocumentosGerados($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		
		# Faturamento de Clientes
		elseif($sub=='clientes') {
			if($acao=='historico' || $acao=='historico_pendente' || $acao=='historico_pago') {
				historicoFaturamento($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='detalhes' || $acao=='detalhes_planos') {
				detalhesFaturamentoCliente($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='baixar') {
				baixarFaturaCliente($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='cancelar') {
				cancelarFaturaCliente($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='dados_cobranca') {
				dadosFaturaCliente($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='estorno') {
				estornoFaturaCliente($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		
		# Arquivos
		elseif($sub=='arquivoremessa') {
			if($acao=='listar' || $acao=='listartodos' || $acao=='listargerados' || $acao=='download') {
				# Listagem de Arquivos para Geração
				arquivosRemessa($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='gerar') {
				# Geração dos arquivos
				gerarArquivosRemessa($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		elseif($sub=='boletobancario') {
			# Listagem de Arquivos para Geração
			boletoBancario($modulo, $sub, $acao, $registro, $matriz);
		}
				
		elseif($sub=='arquivoretorno') {
			# Listagem de Arquivos para Geração
			arquivosRetorno($modulo, $sub, $acao, $registro, $matriz);
		}
		
		elseif($sub=='debitoAutomatico') {
			# Listagem de Arquivos para Geração
			debitoAutomatico($modulo, $sub, $acao, $registro, $matriz);
		}
		
		elseif($sub=='parametrosBancos') {
			# Listagem de Arquivos para Geração
			parametrosBancos($modulo, $sub, $acao, $registro, $matriz);
		}
		
		echo "<script>location.href='#ancora';</script>";
		
	}
	
}



# função para form de seleção de filtros de faturamento
function formCalcularFaturamento($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	# Motrar tabela de busca
	novaTabela2("[Filtros para Geração de Faturamento]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
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
			itemLinhaForm(formSelectServicos($matriz[servico],'servico','form').$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>POP:</b><br>
			<span class=normal10>Selecione a POP de Acesso</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			if($matriz[pop_todos]) $opcPOP='checked';
			$texto="<input type=checkbox name=matriz[pop_todos] value=S $opcPOP><b>Todos</b>";
			itemLinhaForm(formSelectPOP($matriz[pop],'pop','form').$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Forma de Cobrança:</b><br>
			<span class=normal10>Selecione a Forma de Cobrança</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			//if($matriz[forma_cobranca_todos]) $opcFormaCobranca='checked';
			//$texto="<input type=checkbox name=matriz[forma_cobranca_todos] value=S $opcFormaCobranca><b>Todas</b>";
			itemLinhaForm(formSelectFormaCobranca($matriz[forma_cobranca],'forma_cobranca','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Vencimento:</b><br>
			<span class=normal10>Selecione o Vencimento</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm(formSelectVencimento($matriz[vencimento],'vencimento','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Mês/Ano de Referência:</b><br>
			<span class=normal10>Selecione o Mês de referência</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm(formSelectMes($matriz[mes],'mes','form').formSelectAno($matriz[ano],'ano','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
		formPessoaSubmit($modulo, $sub, $acao, $registro, $matriz);
	fechaTabela();
	
}



# função para form de seleção de filtros de faturamento
function formGerarFaturamento($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $sessLogin;
	
	$data=dataSistema();
	
	$vencimento=dadosVencimento($matriz[vencimento]);
	
	if(!$matriz[descricao] || !$matriz[bntGerarCobranca]) {
		# Alimentar descrição de acordo com padrão
		$matriz[descricao]="Faturamento: $matriz[mes]/$matriz[ano] - Vencimento: $vencimento[diaVencimento] - Usuario: $sessLogin[login]";
	}
	
	# Motrar tabela de busca
	novaTabela2("[Geração de Documentos de Cobrança]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		# Opcoes Adicionais
		menuOpcAdicional($modulo, $sub, $acao, $registro);
		#fim das opcoes adicionais
		novaLinhaTabela($corFundo, '100%');
		$texto="<br>
			<span class=txtaviso>CONFIRMAÇÃO DE GERAÇÃO DE DOCUMENTOS:<br>
			Para Confirmar a geração dos documentos de cobrança, pressione o botão \"Gerar Cobrança\".<br><br>
			&nbsp;</span>";
			itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Descrição do Faturamento:</b><br>
			<span class=normal10>Idenfiticação do Faturamento</span>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
			$texto="<input type=text name=matriz[descricao] size=60 value='$matriz[descricao]'>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Status inicial do Faturamento:</b><br>
			<span class=normal10>A ativação deste faturamento deve ser feita posteriormente a sua geração</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			$texto="<span class=txtaviso>Inativo</span>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		
		if(!$matriz[bntGerarCobranca]) {
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntGerarCobranca] value='Gerar Cobranca' class=submit>";
				itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		}
	fechaTabela();


}


function faturamentoGerar($modulo, $sub, $acao, $registro, $matriz) {

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
	if(!$matriz[pop_todos]) $sqlADD.=" AND $tb[Pessoas].idPOP = '$matriz[pop]' ";
	if(!$matriz[forma_cobranca_todos]) $sqlADD.=" AND $tb[PlanosPessoas].idFormaCobranca = '$matriz[forma_cobranca]' ";
	if(!$matriz[servico_todos]) {
		$sqlADD.=" AND $tb[ServicosPlanos].idServico = '$matriz[servico]' ";
		$sqlADDPlano.=" AND $tb[ServicosPlanos].idServico = '$matriz[servico]' ";
	}
	if(!$matriz[vencimento_todos]) {
		$sqlADD.=" AND $tb[PlanosPessoas].idVencimento = '$matriz[vencimento]' ";
		$sqlADDPlano.=" AND $tb[PlanosPessoas].idVencimento = '$matriz[vencimento]' ";
	}

	$sql="
		SELECT
			$tb[PlanosPessoas].idPessoaTipo idPessoaTipo, 
			$tb[PlanosPessoas].dtCadastro dtCadastro, 
			$tb[PlanosPessoas].especial especial, 
			$tb[PlanosPessoas].status status,
			$tb[Pessoas].nome nome,
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
			AND $tb[Pessoas].idPOP = $tb[POP].id
			AND $tb[POP].status='A'
			and $tb[PlanosPessoas].status='A'
			$sqlADD
		GROUP BY
			$tb[PessoasTipos].id
		ORDER BY
			$tb[Pessoas].idPOP,
			$tb[Pessoas].nome";
	
	if($sql) $consultaPlanosAtivos=consultaSQL($sql, $conn);
	
	if($consultaPlanosAtivos && contaconsulta($consultaPlanosAtivos) ) {
		
		# Cabeçalho
		echo "<br>";
		novaTabela2("[Faturamento de Clientes]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		
			# Listagem de Planos com servicos e totais por serviço
			for($a=0;$a<contaConsulta($consultaPlanosAtivos);$a++) {
				# Consultar Planos da pessoa
				$idPessoaTipo=resultadoSQL($consultaPlanosAtivos, $a, 'idPessoaTipo');
				$nomePessoa=resultadoSQL($consultaPlanosAtivos, $a, 'nome');
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
							$valorFormatado=formatarValoresForm($totalPlano);
							$valorFormatado=htmlMontaOpcao("<a href=?modulo=lancamentos&sub=planos&acao=calculo&registro=$idPlano&matriz[mes]=$matriz[mes]&matriz[ano]=$matriz[ano]>$valorFormatado</a>",'lancamento');
							
							$totalCliente+=$totalPlano;
						}
				
						# Contabilizar Total geral	
						$totalGeral+=$totalCliente;
					}
				}
			}
			
			# Rodapé com totais
			htmlAbreLinha($corFundo);
				htmlAbreColunaForm('100%', 'center', 'middle', $corFundo, 2, 'normal10');		
					novaTabelaSH("center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
						htmlAbreLinha($corFundo);
							itemLinhaTMNOURL("<b>Total Geral de Faturamento</b>", 'right', 'middle', '50%', $corFundo, 0, 'tabfundo1');
							itemLinhaTMNOURL(formatarValoresForm($totalGeral), 'center', 'middle', '40%', $corFundo, 0, 'txtok');
						htmlFechaLinha();
					fechaTabela();
				htmlFechaColuna();
			fechaLinhaTabela();
		fechaTabela();
		
		return($totalGeral);
		
		
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
	
}



# Função para geração de documentos
function gerarFaturamento($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $corFundo, $corBorda, $tb, $html;
	
	if($matriz[idFaturamento]) {
		if($matriz[servico_todos]) $matriz[servico]=0;
		if($matriz[pop_todos]) $matriz[pop]=0;
		
		$gravaFaturamento=dbFaturamento($matriz, 'incluir');
		
		if($gravaFaturamento) {
			# Gravar os documentos de cobrança
			$documentosCobranca=gerarDocumentosCobranca($matriz);
		}
	}
}




# Função para geração de documentos
function gerarFaturamentoCliente($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $corFundo, $corBorda, $tb, $html;
	
	if($matriz[idFaturamento]) {
		if(!$matriz[servico_todos]) $matriz[servico]=0;
		//if(!$matriz[pop_todos]) $matriz[pop]=0; nao salbar idPop vazio. gustavo, 20060517
		//if($matriz[forma_cobranca]) $matriz[forma_cobranca]=0;
		
		$gravaFaturamento=dbFaturamento($matriz, 'incluir');
		
		if($gravaFaturamento) {
			# Gravar os documentos de cobrança
			$documentosCobranca=gerarDocumentosCobrancaCliente($matriz);
		}
	}
}




# Função para manutenção de banco - Faturamentos
function dbFaturamento($matriz, $tipo, $status='I') {

	global $conn, $tb;
	
	$data=dataSistema();
	
	if($tipo=='incluir') {
		$sql="
			INSERT INTO 
				$tb[Faturamentos] 
			VALUES (
				'$matriz[idFaturamento]', 
				'$matriz[descricao]', 
				'$data[dataBanco]',
				'$matriz[servico]',
				'$matriz[pop]',
				'$matriz[forma_cobranca]',
				'$matriz[vencimento]',
				'$matriz[mes]',
				'$matriz[ano]',
				'$status',
				'N'
			)";
	}
	
	
	if($sql) {
		$consulta=consultaSQL($sql, $conn);
	}
	
	return($consulta);

}



# Funçao para novo ID de Faturamento
function novoIDFaturamento() {

	global $conn, $tb;
	
	$sql="
		SELECT 
			MAX($tb[Faturamentos].id)+1 id
		FROM
			$tb[Faturamentos]";
	
	$consulta=consultaSQL($sql, $conn);
	
	if(!$consulta || contaConsulta($consulta)==0) {
		$id=1;
	}
	else {
		$id=resultadoSQL($consulta, 0, 'id');
		
		
		if(!$id && contaConsulta($consulta)==1) {
			$id=1;
		}
	}

	return($id);
}





# Função para listagem 
function listarFaturamentos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessPlanos, $conn, $tb;
	
	$data=dataSistema();
	
	if($acao=='listar') $consulta=buscaFaturamentos("I", 'status','igual','data DESC');
	elseif($acao=='listartodos') $consulta=buscaFaturamentos('', '','todos','data DESC');
	elseif($acao=='listarativos') $consulta=buscaFaturamentos('A', 'status','igual','data DESC');
	elseif($acao=='listarcancelados') $consulta=buscaFaturamentos('C', 'status','igual','data DESC');
	
	# Cabeçalho		
	# Motrar tabela de busca
	novaTabela("[Faturamentos Gerados]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 6);
	$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=gerar>Gerar novo faturamento</a>",'faturamento');
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Faturamentos Inativos</a>",'listar');
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listarativos>Faturamentos Ativos</a>",'listar');
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listarcancelados>Faturamentos Cancelados</a>",'listar');
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listartodos>Todos</a>",'listar');
	itemTabelaNOURL($opcoes, 'center', $corFundo, 6, 'tabfundo1');
	
	$opcoes='';
	
	# Caso não hajam servicos para o servidor
	if(!$consulta || contaConsulta($consulta)==0) {
		# Não há registros
		itemTabelaNOURL('Não há faturamentos gerados', 'left', $corFundo, 6, 'txtaviso');
	}
	else {

		# Paginador
		paginador($consulta, contaConsulta($consulta), $limite[lista][faturamentos], $registro, 'normal10', 6, $urlADD);

		# Cabeçalho
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTabela('Identificação do Faturamento', 'center', '45%', 'tabfundo0');
			itemLinhaTabela('Status', 'center', '10%', 'tabfundo0');
			itemLinhaTabela('Geração', 'center', '10%', 'tabfundo0');
			itemLinhaTabela('Total', 'center', '15%', 'tabfundo0');
			itemLinhaTabela('Qtde', 'center', '5%', 'tabfundo0');
			itemLinhaTabela('Opções', 'center', '15%', 'tabfundo0');
		fechaLinhaTabela();
		
		
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
		$limite=$i+$limite[lista][faturamentos];
		
		$consultaTipoCarteira = buscaRegistros( "S", "valor", "igual", "id", "TipoCarteira" );
		if( $consultaTipoCarteira && contaConsulta( $consultaTipoCarteira ) ) $idTipoCarteiraImpressao = resultadoSQL( $consultaTipoCarteira, 0, "id");
		
		for($i=$i;$i<contaConsulta($consulta) && $i < $limite;$i++) {
			# Mostrar registro
			$id=resultadoSQL($consulta, $i, 'id');
			$descricao=resultadoSQL($consulta, $i, 'descricao');
			$data=resultadoSQL($consulta, $i, 'data');
			$status=resultadoSQL($consulta, $i, 'status');
			$idFormaCobranca = resultadoSQL($consulta, $i, "idFormaCobranca");
			
			$dadosFormaCobranca = dadosFormaCobranca( $idFormaCobranca );
			
			if($status=='I') { 
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=ativar&registro=$id>Ativar</a>",'desativar');
			}
			else {
				$opcoes='';
			}

			# Listar Detalhes do faturamento
			$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=detalhes&registro=$registro&matriz[idFaturamento]=$id>Detalhes</a>",'faturamento');
			
			if($status!='C'){
				$opcoes.="<br>".htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=cancelar&registro=$id>Cancelar</a>",'cancelar');
			}
			
			# Listagem do Cliente/Endereço
			if( $status == 'A' ){
				$opcoes.="<br>".htmlMontaOpcao("<a href=?modulo=consultas&sub=listar_cliente_endereco&acao=listar&matriz[idFaturamento]=$id&registro=$id>
												Listar Cliente/Endereço</a>",'relatorio');
			}
			
			if( $idTipoCarteiraImpressao == $dadosFormaCobranca["idTipoCarteira"] && $status == "A")
				$opcoes.="<br>".htmlMontaOpcao("<a href=?modulo=faturamento&sub=boletobancario&acao=gerar&matriz[tipo]=faturamento&registro=$id>
												Imprimir Boletos</a>",'arquivo');

			# Dados do faturamento
			$dadosFaturamento=totalFaturamento($id);
			$valor=formatarValoresForm($dadosFaturamento[total]);
			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela($descricao, 'left', '45%', 'normal10');
				itemLinhaTabela(formSelectStatus($status, '','check'), 'center', '10%', 'normal10');
				itemLinhaTabela(converteData($data, 'banco','formdata'), 'center', '10%', 'normal10');
				itemLinhaTabela("<span class=txtok>$valor</span>", 'center', '15%', 'normal10');
				itemLinhaTabela($dadosFaturamento[qtde], 'center', '5%', 'normal10');
				itemLinhaTabela($opcoes, 'left', '15%', 'normal8');
			fechaLinhaTabela();
			
		} #fecha laco de montagem de tabela
		
	} #fecha servicos encontrados
	
	fechaTabela();
	
}#fecha função de listagem



# função de busca 
function buscaFaturamentos($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[Faturamentos] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[Faturamentos] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[Faturamentos] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[Faturamentos] WHERE $texto ORDER BY $ordem";
	}
	
	# Verifica consulta
	if($sql){
		$consulta=consultaSQL($sql, $conn);
		# Retornvar consulta
		return($consulta);
	}
	else {	
		# Mensagem de aviso
		$msg="Consulta não pode ser realizada por falta de parâmetros";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
	}
} # fecha função de busca



# Função para totalização de faturamento gerado
function totalFaturamento($idFaturamento) {

	global $conn, $tb;
	
	$sql="
		SELECT
			COUNT($tb[DocumentosGerados].id) qtde, 
			SUM($tb[ServicosPlanosDocumentosGerados].valor) total 
		FROM
			$tb[DocumentosGerados], 
			$tb[PlanosDocumentosGerados], 
			$tb[ServicosPlanosDocumentosGerados],
			$tb[Faturamentos]
		WHERE
			$tb[DocumentosGerados].id=$tb[PlanosDocumentosGerados].idDocumentoGerado 
			AND $tb[PlanosDocumentosGerados].id=$tb[ServicosPlanosDocumentosGerados].idPlanoDocumentoGerado
			AND $tb[Faturamentos].id=$tb[DocumentosGerados].idFaturamento
			AND $tb[Faturamentos].id=$idFaturamento
		GROUP BY
			$tb[DocumentosGerados].id";
	
	if($sql) {
		$consulta=consultaSQL($sql, $conn);
	}
	
	if($consulta && contaConsulta($consulta)>0) {
		for($a=0;$a<contaConsulta($consulta);$a++) {
			$retorno[total]+=resultadoSQL($consulta, $a, 'total');
			$retorno[qtde]++;
		}
	}
	
	return($retorno);
}



# Função para detalhar o Faturamento
function detalhesFaturamentos($modulo, $sub, $acao, $registro, $matriz) {

	# Mostrar detalhes do faturamento
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessPlanos, $conn, $tb;

	# Visualizar Detalhes do faturamento
	verFaturamento($modulo, $sub, $acao, $matriz[idFaturamento], $matriz);
	
	# Data de vencimento
	$matriz[idVencimento]=$idVencimento;
	$matriz[mes]=$mes;
	$matriz[ano]=$ano;
	
	# Listar documentos
	if($matriz[idFaturamento]) {
		echo "<br>";
		listarDocumentosGerados($modulo, $sub, $acao, $registro, $matriz);
	}
}



# Função para cancelar faturamentos
function cancelarFaturamentos($modulo, $sub, $acao, $registro, $matriz) {

	# Mostrar detalhes do faturamento
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessPlanos, $conn, $tb;
	
	# Cabeçalho		
	# Motrar tabela de busca
	novaTabela2("[Cancelamento de Faturamento]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=gerar>Gerar novo faturamento</a>",'faturamento');
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Faturamentos Inativos</a>",'listar');
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listarativos>Faturamentos Ativos</a>",'listar');
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listarcancelados>Faturamentos Cancelados</a>",'listar');
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listartodos>Todos</a>",'listar');
	itemTabelaNOURL($opcoes, 'center', $corFundo, 2, 'tabfundo1');

	if($registro) {
		$consulta=buscaFaturamentos($registro, 'id','igual','id');
		
		# Caso não hajam servicos para o servidor
		if(!$consulta || contaConsulta($consulta)==0) {
			# Não há registros
			itemTabelaNOURL('Faturamento não encontrado!', 'left', $corFundo, 2, 'txtaviso');
		}
		else {
		
			#dados do faturamento
			$idFaturamento=resultadoSQL($consulta, 0, 'id');
			$descricao=resultadoSQL($consulta, 0, 'descricao');
			$data=resultadoSQL($consulta, 0, 'data');
			$status=resultadoSQL($consulta, 0, 'status');
			
			# dados do form
			$idServico=resultadoSQL($consulta, 0, 'idServico');
			$idPOP=resultadoSQL($consulta, 0, 'idPOP');
			$idFormaCobranca=resultadoSQL($consulta, 0, 'idFormaCobranca');
			$idVencimento=resultadoSQL($consulta, 0, 'idVencimento');
			$mes=resultadoSQL($consulta, 0, 'mes');
			$ano=resultadoSQL($consulta, 0, 'ano');
			$remessa=resultadoSQL($consulta, 0, 'remessa');
			
			if(!$matriz[bntCancelar]) {
		
				# Cabeçalho
				itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela('<b>Identificação do Faturamento:</b>', 'right', '30%', 'tabfundo1');
					itemLinhaTabela($descricao, 'left', '70%', 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela('<b>Data de Geração:</b>', 'right', '30%', 'tabfundo1');
					itemLinhaTabela(converteData($data,'banco','form'), 'left', '70%', 'tabfundo1');
				fechaLinhaTabela();
				
				# Serviço
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela('<b>Serviço:</b>', 'right', '30%', 'tabfundo1');
					if($idServico) {
						$texto=formSelectServicos($idServico,'','check');
					}
					else {
						$texto='Todos';
					}
					itemLinhaTabela($texto, 'left', '70%', 'tabfundo1');
				fechaLinhaTabela();
				
				# POP
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela('<b>POP:</b>', 'right', '30%', 'tabfundo1');
					if($idPOP) {
						$texto=formSelectPOP($idPOP,'','check');
					}
					else {
						$texto='Todos';
					}
					itemLinhaTabela($texto, 'left', '70%', 'tabfundo1');
				fechaLinhaTabela();
				
				# Forma de Cobrança
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela('<b>Forma de Cobrança:</b>', 'right', '30%', 'tabfundo1');
					if($idFormaCobranca) {
						$texto=formSelectFormaCobranca($idFormaCobranca,'','check');
					}
					else {
						$texto='Todos';
					}
					itemLinhaTabela($texto, 'left', '70%', 'tabfundo1');
				fechaLinhaTabela();
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela('<b>Vencimento:</b>', 'right', '30%', 'tabfundo1');
					itemLinhaTabela(formSelectVencimento($idVencimento,'','check'), 'left', '70%', 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela('<b>Mês/Ano:</b>', 'right', '30%', 'tabfundo1');
					itemLinhaTabela("$mes/$ano", 'left', '70%', 'tabfundo1');
				fechaLinhaTabela();
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela('<b>Valor do Faturamento:</b>', 'right', '30%', 'tabfundo1');
					itemLinhaTabela(formatarValoresForm(valorFaturamento($idFaturamento)), 'left', '70%', 'tabfundo1');
				fechaLinhaTabela();
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela('<b>Status:</b>', 'right', '30%', 'tabfundo1');
					itemLinhaTabela(formSelectStatus($status,'','check'), 'left', '70%', 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela('<b>Arquivo Remessa:</b>', 'right', '30%', 'tabfundo1');
					itemLinhaTabela(formSelectStatusRemessa($remessa,'','check'), 'left', '70%', 'tabfundo1');
				fechaLinhaTabela();
				itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
	
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro>
					<input type=submit name=matriz[bntCancelar] value=Cancelar class=submit>";
				itemTabelaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
			}
			else {
				# Cancelar o faturamento
				
				$sql="
					UPDATE 
						$tb[Faturamentos]
					SET
						status='C'
					WHERE
						id='$registro'
				";
				
				$consultaFaturamento=consultaSQL($sql, $conn);
				
				if($consulta) {
					# Cancelar documentos gerados
					$sql="
						UPDATE 
							$tb[DocumentosGerados]
						SET
							status='C'
						WHERE
							idFaturamento='$registro'
					";
					
					$consultaDocumentos=consultaSQL($sql, $conn);
				}
				
				# Verificar o status do faturamento, para cancelamento de documentos
				# jah enviados para o contas a receber
				if($status=='A') {
					# Cancelar Contas a receber dos documentos Gerados
					excluirDocumentosGeradosContasReceber($idFaturamento);
				}
				
				# Mensagem
				if($consultaFaturamento && $consultaDocumentos) {
					# OK
					itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
					$texto="<span class=txtaviso>Faturamento Cancelado com sucesso!</span>";
					itemTabelaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
					itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
				}
			}
			
		} #fecha servicos encontrados
	}
	else {
		# Não há registros
		itemTabelaNOURL('Faturamento não informado!', 'left', $corFundo, 2, 'txtaviso');
	}
	
	fechaTabela();

}



# Função para calculo de valor do faturamento
function valorFaturamento($idFaturamento) {

	# Calcular o valor do faturamento
	$consultaDocumentos=buscaDocumentosGerados($idFaturamento, 'idFaturamento','igual','id');
	if($consultaDocumentos && contaConsulta($consultaDocumentos)>0) {
		# totalizar documentos
		for($a=0;$a<contaConsulta($consultaDocumentos);$a++) {
			# somar
			$total+=valorDocumentosGerados(resultadoSQL($consultaDocumentos, $a, 'id'));
		}
	}
	
	return($total);
}




# Função para visualização de detalhes do faturamento gerado
function verFaturamento($modulo, $sub, $acao, $registro, $matriz) {

	# Mostrar detalhes do faturamento
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessPlanos, $conn, $tb;

	# Cabeçalho		
	# Motrar tabela de busca
	novaTabela2("[Detalhes do Faturamento]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	# Opcoes Adicionais
	menuOpcAdicional($modulo, $sub, $acao, $registro);
	#fim das opcoes adicionais
	
	if($registro) {
		$consulta=buscaFaturamentos($registro, 'id','igual','id');
		
		# Caso não hajam servicos para o servidor
		if(!$consulta || contaConsulta($consulta)==0) {
			# Não há registros
			itemTabelaNOURL('Não há faturamentos gerados', 'left', $corFundo, 2, 'txtaviso');
		}
		else {
		
			#dados do faturamento
			$idFaturamento=resultadoSQL($consulta, 0, 'id');
			$descricao=resultadoSQL($consulta, 0, 'descricao');
			$data=resultadoSQL($consulta, 0, 'data');
			$status=resultadoSQL($consulta, 0, 'status');
			
			# dados do form
			$idServico=resultadoSQL($consulta, 0, 'idServico');
			$idPOP=resultadoSQL($consulta, 0, 'idPOP');
			$idFormaCobranca=resultadoSQL($consulta, 0, 'idFormaCobranca');
			$idVencimento=resultadoSQL($consulta, 0, 'idVencimento');
			$mes=resultadoSQL($consulta, 0, 'mes');
			$ano=resultadoSQL($consulta, 0, 'ano');
			$status=resultadoSQL($consulta, 0, 'status');
			$remessa=resultadoSQL($consulta, 0, 'remessa');
	
			# Cabeçalho
			itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('<b>Identificação do Faturamento:</b>', 'right', '30%', 'tabfundo1');
				itemLinhaTabela($descricao, 'left', '70%', 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('<b>Data de Geração:</b>', 'right', '30%', 'tabfundo1');
				itemLinhaTabela(converteData($data,'banco','form'), 'left', '70%', 'tabfundo1');
			fechaLinhaTabela();
			
			# Serviço
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('<b>Serviço:</b>', 'right', '30%', 'tabfundo1');
				if($idServico) {
					$texto=formSelectServicos($idServico,'','check');
				}
				else {
					$texto='Todos';
				}
				itemLinhaTabela($texto, 'left', '70%', 'tabfundo1');
			fechaLinhaTabela();
			
			# POP
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('<b>POP:</b>', 'right', '30%', 'tabfundo1');
				if($idPOP) {
					$texto=formSelectPOP($idPOP,'','check');
				}
				else {
					$texto='Todos';
				}
				itemLinhaTabela($texto, 'left', '70%', 'tabfundo1');
			fechaLinhaTabela();
			
			# Forma de Cobrança
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('<b>Forma de Cobrança:</b>', 'right', '30%', 'tabfundo1');
				if($idFormaCobranca) {
					$texto=formSelectFormaCobranca($idFormaCobranca,'','check');
				}
				else {
					$texto='Todos';
				}
				itemLinhaTabela($texto, 'left', '70%', 'tabfundo1');
			fechaLinhaTabela();
			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('<b>Vencimento:</b>', 'right', '30%', 'tabfundo1');
				itemLinhaTabela(formSelectVencimento($idVencimento,'','check'), 'left', '70%', 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('<b>Mês/Ano:</b>', 'right', '30%', 'tabfundo1');
				itemLinhaTabela("$mes/$ano", 'left', '70%', 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('<b>Status:</b>', 'right', '30%', 'tabfundo1');
				itemLinhaTabela(formSelectStatus($status,'','check'), 'left', '70%', 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('<b>Arquivo Remessa:</b>', 'right', '30%', 'tabfundo1');
				itemLinhaTabela(formSelectStatusRemessa($remessa,'','check'), 'left', '70%', 'tabfundo1');
			fechaLinhaTabela();
			
		} #fecha servicos encontrados
	}
	else {
		# Não há registros
		itemTabelaNOURL('Faturamento não informado!', 'left', $corFundo, 2, 'txtaviso');
	}
	
	fechaTabela();
	
	return($registro);
}




# Função para cancelar faturamentos
function ativarFaturamentos($modulo, $sub, $acao, $registro, $matriz) {

	# Mostrar detalhes do faturamento
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessPlanos, $conn, $tb;
	
	# Cabeçalho		
	# Motrar tabela de busca
	novaTabela2("[Ativação de Faturamento]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=gerar>Gerar novo faturamento</a>",'faturamento');
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Faturamentos Inativos</a>",'listar');
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listarativos>Faturamentos Ativos</a>",'listar');
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listarcancelados>Faturamentos Cancelados</a>",'listar');
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listartodos>Todos</a>",'listar');
	itemTabelaNOURL($opcoes, 'center', $corFundo, 2, 'tabfundo1');

	if($registro) {
		$consulta=buscaFaturamentos($registro, 'id','igual','id');
		
		# Caso não hajam servicos para o servidor
		if(!$consulta || contaConsulta($consulta)==0) {
			# Não há registros
			itemTabelaNOURL('Faturamento não encontrado!', 'left', $corFundo, 2, 'txtaviso');
		}
		else {
		
			#dados do faturamento
			$idFaturamento=resultadoSQL($consulta, 0, 'id');
			$descricao=resultadoSQL($consulta, 0, 'descricao');
			$data=resultadoSQL($consulta, 0, 'data');
			$status=resultadoSQL($consulta, 0, 'status');
			
			# dados do form
			$idServico=resultadoSQL($consulta, 0, 'idServico');
			$idPOP=resultadoSQL($consulta, 0, 'idPOP');
			$idFormaCobranca=resultadoSQL($consulta, 0, 'idFormaCobranca');
			$idVencimento=resultadoSQL($consulta, 0, 'idVencimento');
			$mes=resultadoSQL($consulta, 0, 'mes');
			$ano=resultadoSQL($consulta, 0, 'ano');
			$remessa=resultadoSQL($consulta, 0, 'remessa');
			
			if(!$matriz[bntAtivar]) {
		
				# Cabeçalho
				itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela('<b>Identificação do Faturamento:</b>', 'right', '30%', 'tabfundo1');
					itemLinhaTabela($descricao, 'left', '70%', 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela('<b>Data de Geração:</b>', 'right', '30%', 'tabfundo1');
					itemLinhaTabela(converteData($data,'banco','form'), 'left', '70%', 'tabfundo1');
				fechaLinhaTabela();
				
				# Serviço
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela('<b>Serviço:</b>', 'right', '30%', 'tabfundo1');
					if($idServico) {
						$texto=formSelectServicos($idServico,'','check');
					}
					else {
						$texto='Todos';
					}
					itemLinhaTabela($texto, 'left', '70%', 'tabfundo1');
				fechaLinhaTabela();
				
				# POP
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela('<b>POP:</b>', 'right', '30%', 'tabfundo1');
					if($idPOP) {
						$texto=formSelectPOP($idPOP,'','check');
					}
					else {
						$texto='Todos';
					}
					itemLinhaTabela($texto, 'left', '70%', 'tabfundo1');
				fechaLinhaTabela();
				
				# Forma de Cobrança
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela('<b>Forma de Cobrança:</b>', 'right', '30%', 'tabfundo1');
					if($idFormaCobranca) {
						$texto=formSelectFormaCobranca($idFormaCobranca,'','check');
					}
					else {
						$texto='Todos';
					}
					itemLinhaTabela($texto, 'left', '70%', 'tabfundo1');
				fechaLinhaTabela();
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela('<b>Vencimento:</b>', 'right', '30%', 'tabfundo1');
					itemLinhaTabela(formSelectVencimento($idVencimento,'','check'), 'left', '70%', 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela('<b>Mês/Ano:</b>', 'right', '30%', 'tabfundo1');
					itemLinhaTabela("$mes/$ano", 'left', '70%', 'tabfundo1');
				fechaLinhaTabela();
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela('<b>Valor do Faturamento:</b>', 'right', '30%', 'tabfundo1');
					itemLinhaTabela(formatarValoresForm(valorFaturamento($idFaturamento)), 'left', '70%', 'tabfundo1');
				fechaLinhaTabela();
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela('<b>Status:</b>', 'right', '30%', 'tabfundo1');
					itemLinhaTabela(formSelectStatus($status,'','check'), 'left', '70%', 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela('<b>Arquivo Remessa:</b>', 'right', '30%', 'tabfundo1');
					itemLinhaTabela(formSelectStatusRemessa($remessa,'','check'), 'left', '70%', 'tabfundo1');
				fechaLinhaTabela();
				itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
				
				
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro>
					<input type=submit name=matriz[bntAtivar] value=Ativar class=submit>";
				
				itemTabelaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
				
			}
			else {
				# Vencimento
				$vencimento=dadosVencimento($idVencimento);
					
				if($vencimento[diaVencimento] < $vencimento[diaFaturamento]) {
					$dtVencimento=mktime(0,0,0,($mes+1),$vencimento[diaVencimento],$ano);
					$dtVencimento=date('Y-m-d',$dtVencimento);
				}
				else {
					$dtVencimento=mktime(0,0,0,($mes),$vencimento[diaVencimento],$ano);
					$dtVencimento=date('Y-m-d',$dtVencimento);
				}
				
				$matriz[dtVencimento]=$dtVencimento;
				
				$matriz[idFaturamento]=$idFaturamento;
				$matriz[dtVencimento]=$dtVencimento;
				
				# Adicionar Documentos
				$ativarDocumentos=adicionarDocumentosGeradosContasReceber($matriz);
				if($ativarDocumentos) {
					# Mostrar informações sobre documentos gerados e documentos
					# gravados em contas a receber, juntamente com a informação
					# se um documento foi direcionado como serviço adicional
					# para algum cliente
					
					$sql="
						UPDATE 
							$tb[Faturamentos]
						SET
							status='A'
						WHERE
							id='$idFaturamento'";
					
					if($ativarDocumentos[contasReceber]>0 || $ativarDocumentos[servicosAdicionais]>0) $consultaFaturamento=consultaSQL($sql, $conn);
					
					itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
					$texto="<span class=txtaviso>Faturamento Ativado com sucesso!</span>";
					itemTabelaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
					itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTabela('<b>Documentos lançados no contas a Receber:</b>', 'right', '60%', 'tabfundo1');
						itemLinhaTabela($ativarDocumentos[contasReceber], 'left', '40%', 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTabela('<b>Documentos lançados como serviço adicional:</b>', 'right', '60%', 'tabfundo1');
						itemLinhaTabela($ativarDocumentos[servicosAdicionais], 'left', '40%', 'tabfundo1');
					fechaLinhaTabela();
					
					# Listar Servicos Adicionais lançados 
					if(is_array($ativarDocumentos[lancamentos]) && count($ativarDocumentos[lancamentos])>0) {
						# Listagem de lançamentos
						itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
						htmlAbreLinha($corFundo);
							htmlAbreColuna('100%','center',$corFundo, 2, 'tabfundo1');
								novaTabela("[Listagem de Serviços Adicionais Gerados por Faturamento Inferior]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 4);
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL("<B>Cliente</B>", 'center', 'middle', '55%', $corFundo, 0, 'tabfundo1');
									itemLinhaTMNOURL("<B>Vencimento</B>", 'center', 'middle', '10%', $corFundo, 0, 'tabfundo1');
									itemLinhaTMNOURL("<B>Valor</B>", 'center', 'middle', '15%', $corFundo, 0, 'tabfundo1');
									itemLinhaTMNOURL("<B>Tipo</B>", 'center', 'middle', '20%', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								for($x=0;$x<count($ativarDocumentos[lancamentos]);$x++) {
									formVerDadosServicoAdicional($ativarDocumentos[lancamentos][$x]);
								}
								fechaTabela();
							htmlFechaColuna();
						htmlFechaLinha();
					}
					
				}
				else {
					# Não existem documentos
					itemTabelaNOURL('ERRO: Não há documentos para ativação deste Faturamento!', 'center', $corFundo, 2, 'txtaviso');
				}
				
				# Mensagem
				if($consultaFaturamento && $consultaDocumentos) {
					# OK
					itemTabelaNOURL('Faturamento Ativado com sucesso!', 'center', $corFundo, 2, 'txtaviso');
				}
				
			}
			
		} #fecha servicos encontrados
	}
	else {
		# Não há registros
		itemTabelaNOURL('Faturamento não informado!', 'left', $corFundo, 2, 'txtaviso');
	}
	
	fechaTabela();

}





# Função para visualização de histórico de faturamento do cliente
function historicoFaturamento($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $tb, $conn, $limite;

	verPessoas('cadastros', 'clientes', 'ver', $matriz[idPessoaTipo], $matriz);
	echo "<br>";
	
	$data=dataSistema();
	
	# Motrar tabela de busca
	novaTabela2("[Histórico de Faturamento]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		# Opcoes Adicionais
		menuOpcAdicional($modulo, $sub, $acao, $matriz[idPessoaTipo]);
		
		#fim das opcoes adicionais
		itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
		
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('100%', 'left', $corFundo, 2, 'tabfundo1');
				novaTabela("Relação de Faturas","center", '100%', 0, 2, 1, $corFundo, $corBorda, 7);
					# Consultar Contas a Receber
					if($acao=='historico_pendente') $sqlADD="AND $tb[ContasReceber].status='P'";
					elseif($acao=='historico_pago') $sqlADD="AND $tb[ContasReceber].status='B'";
					
					$sql="
						SELECT
							$tb[ContasReceber].id,
							$tb[ContasReceber].idDocumentosGerados,
							$tb[ContasReceber].valor,
							$tb[ContasReceber].valorRecebido,
							$tb[ContasReceber].valorJuros,
							$tb[ContasReceber].valorDesconto,
							$tb[ContasReceber].dtCadastro,
							$tb[ContasReceber].dtVencimento,
							$tb[ContasReceber].dtBaixa,
							$tb[ContasReceber].status,
							$tb[DocumentosGerados].idFaturamento
						FROM
							$tb[ContasReceber],
							$tb[DocumentosGerados]
						WHERE
							$tb[ContasReceber].idDocumentosGerados=$tb[DocumentosGerados].id
							AND $tb[DocumentosGerados].idPessoaTipo=$matriz[idPessoaTipo]
							$sqlADD
						ORDER BY
							$tb[ContasReceber].dtBaixa DESC,
							$tb[ContasReceber].dtVencimento ASC
					";
					
					
					$consulta=consultaSQL($sql, $conn);
					
					
					if($consulta && contaConsulta($consulta)>0) {
						# Paginador
						$urlADD="&matriz[idPessoaTipo]=$matriz[idPessoaTipo]";
						paginador($consulta, contaConsulta($consulta), $limite[lista][cobrancas], $registro, 'normal', 10, $urlADD);
						
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL('Docto.', 'center', 'middle', '10%', $corFundo, 0, 'tabfundo0');
							itemLinhaTMNOURL('Data Lançamento', 'center', 'middle', '10%', $corFundo, 0, 'tabfundo0');
							itemLinhaTMNOURL('Data Vencimento', 'center', 'middle', '10%', $corFundo, 0, 'tabfundo0');
							itemLinhaTMNOURL('Data Baixa', 'center', 'middle', '10%', $corFundo, 0, 'tabfundo0');
							itemLinhaTMNOURL('Valor', 'center', 'middle', '13%', $corFundo, 0, 'tabfundo0');
							itemLinhaTMNOURL('Status', 'center', 'middle', '10%', $corFundo, 0, 'tabfundo0');
							itemLinhaTMNOURL('Opções', 'center', 'middle', '37%', $corFundo, 0, 'tabfundo0');
						fechaLinhaTabela();
					
					
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
						
						$limite=$i+$limite[lista][cobrancas];
						
						# Listar
						for($a=$i;$a<contaConsulta($consulta) && $a < $limite;$a++) {
							$idContasReceber=resultadoSQL($consulta, $a, 'id');
							$idDocumentosGerados=resultadoSQL($consulta, $a, 'idDocumentosGerados');
							$valor=resultadoSQL($consulta, $a, 'valor');
							$valorRecebido=resultadoSQL($consulta, $a, 'valorRecebido');
							$valorJuros=resultadoSQL($consulta, $a, 'valorJuros');
							$valorDesconto=resultadoSQL($consulta, $a, 'valorDesconto');
							$dtCadastro=resultadoSQL($consulta, $a, 'dtCadastro');
							$dtVencimento=resultadoSQL($consulta, $a, 'dtVencimento');
							$dtBaixa=resultadoSQL($consulta, $a, 'dtBaixa');
							$status=resultadoSQL($consulta, $a, 'status');
							$idFaturamento=resultadoSQL($consulta, $a, 'idFaturamento');
							
							$valorFormatado=formatarValoresForm($valor);
							
							# Montar opções
							if($status=='P') {
								$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=clientes&acao=baixar&registro=$idContasReceber>Baixar</a>",'baixar');
								$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=clientes&acao=cancelar&registro=$idContasReceber>Cancelar</a>",'cancelar');
								$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=clientes&acao=detalhes&registro=$idContasReceber>Detalhes</a>",'relatorio');
								$valorFormatado=htmlMontaOpcao("<a href=?modulo=$modulo&sub=clientes&acao=detalhes&registro=$idContasReceber>$valorFormatado</a>",'desconto');
								if(imprimirBoletoFaturamento( $idFaturamento )) {
									 $opcoes.= htmlMontaOpcao("<a href=?modulo=faturamento&sub=boletobancario&acao=gerar&matriz[tipo]=documentosGerados&registro=$idDocumentosGerados>Imprimir Boleto</a>",'imprimir');
								}
								$calculaDias=calculaDiasDiferenca($data[dataBanco],$dtVencimento);								
								if($calculaDias[tipo]=='atrasado') $obs="<span class=txtaviso>(em atraso)</span>";
								else $obs='<span class=txtok>(a vencer)</span>';
							}
							elseif($status=='B') {
								$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=clientes&acao=dados_cobranca&registro=$idContasReceber>Dados da Cobrança</a>",'info');
								$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=clientes&acao=detalhes&registro=$idContasReceber>Detalhes</a>",'relatorio');
								$valorFormatado=htmlMontaOpcao("<a href=?modulo=$modulo&sub=clientes&acao=detalhes&registro=$idContasReceber>$valorFormatado</a>",'lancamento');
								$obs='';
							}
							elseif($status=='C') {
								$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=clientes&acao=dados_cobranca&registro=$idContasReceber>Dados da Cobrança</a>",'info');
								$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=clientes&acao=detalhes&registro=$idContasReceber>Detalhes</a>",'relatorio');
								$valorFormatado=htmlMontaOpcao("<a href=?modulo=$modulo&sub=clientes&acao=detalhes&registro=$idContasReceber>$valorFormatado</a>",'desconto');
								$obs='';
							}
							
							$opcoes.="<br>";
							$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=clientes&acao=estorno&registro=$idContasReceber>Estorno</a>",'estorno');
							
							if($obs) $vf="$valorFormatado<br>$obs";
							else $vf=$valorFormatado;
							
							novaLinhaTabela($corFundo, '100%');
								itemLinhaTMNOURL($idContasReceber, 'center', 'middle', '10%', $corFundo, 0, 'normal10');
								itemLinhaTMNOURL(converteData($dtCadastro, 'banco','formdata'), 'center', 'middle', '10%', $corFundo, 0, 'normal10');
								itemLinhaTMNOURL(converteData($dtVencimento, 'banco','formdata'), 'center', 'middle', '10%', $corFundo, 0, 'normal10');
								itemLinhaTMNOURL(converteData($dtBaixa, 'banco','formdata'), 'center', 'middle', '10%', $corFundo, 0, 'normal10');
								itemLinhaTMNOURL($vf, 'left', 'middle', '13%', $corFundo, 0, 'normal10');
								itemLinhaTMNOURL(formSelectStatusContasReceber($status,'','check'), 'center', 'middle', '10%', $corFundo, 0, 'normal10');
								itemLinhaTMNOURL($opcoes, 'left', 'middle', '27%', $corFundo, 0, 'normal8');
							fechaLinhaTabela();
						}
					}
					else {
						itemTabelaNOURL('Não foram encontradas faturas cadastradas', 'center', $corFundo, 6, 'txtaviso');
					}
				fechaTabela();
			htmlFechaColuna();
		fechaLinhaTabela();
	fechaTabela();
	
}


# Funçao para busca de informações do vencimento
function dadosFaturamento($idFaturamento) {

	$consulta=buscaFaturamentos($idFaturamento, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		# dados do vencimento
		$retorno[id]=resultadoSQL($consulta, 0, 'id');
		$retorno[descricao]=resultadoSQL($consulta, 0, 'descricao');
		$retorno[data]=resultadoSQL($consulta, 0, 'data');
		$retorno[idServico]=resultadoSQL($consulta, 0, 'idServico');
		$retorno[idPOP]=resultadoSQL($consulta, 0, 'idPOP');
		$retorno[idFormaCobranca]=resultadoSQL($consulta, 0, 'idFormaCobranca');
		$retorno[idVencimento]=resultadoSQL($consulta, 0, 'idVencimento');
		$retorno[mes]=resultadoSQL($consulta, 0, 'mes');
		$retorno[ano]=resultadoSQL($consulta, 0, 'ano');
		$retorno[status]=resultadoSQL($consulta, 0, 'status');
		$retorno[remessa]=resultadoSQL($consulta, 0, 'remessa');
	}
	
	return($retorno);
}

/**
 * Busca os dados de um faturamente pela id retornando-os em um array
 *
 * @param unknown_type $id
 * @return array
 */
function faturamentosBuscaDadosPorId( $id ){
	$consulta = buscaFaturamentos( $id, 'id','igual','id' );
	
	$ret = faturamentoDadosArray( $consulta );
	
	return $ret;
}

/**
 * carrega os dados da consulta em uma matriz
 *
 * @param unknown_type $consulta
 * @return array
 */
function faturamentoDadosArray( $consulta ){
	global $tb, $conn;
	$ret = array();
	
	if( $consulta && contaConsulta( $consulta ) > 0 ){
		$ret['id']				= resultadoSQL($consulta, 0, 'id');
		$ret['descricao']		= resultadoSQL($consulta, 0, 'descricao');
		$ret['data']			= resultadoSQL($consulta, 0, 'data');
		$ret['idServico']		= resultadoSQL($consulta, 0, 'idServico');
		$ret['idPOP']			= resultadoSQL($consulta, 0, 'idPOP');
		$ret['idFormaCobranca']	= resultadoSQL($consulta, 0, 'idFormaCobranca');
		$ret['idVencimento']	= resultadoSQL($consulta, 0, 'idVencimento');
		$ret['mes']				= resultadoSQL($consulta, 0, 'mes');
		$ret['ano']				= resultadoSQL($consulta, 0, 'ano');
		$ret['status']			= resultadoSQL($consulta, 0, 'status');
		$ret['remessa']			= resultadoSQL($consulta, 0, 'remessa');
	}
	
	return $ret;
}

/**
 * Busca o id do ultimo faturamento com determinada descricão, afim de juntar todos os
 * faturamentos do dia em um único arquivo remessa.
 *
 * @param dataSistema() $data
 * @return int
 */
function buscaIdFaturamento($data) {
	global $conn;
//						  "Registro referente ao faturamento avulso lancado em";
	$sql 				= "SELECT MAX(id) FROM Faturamentos WHERE descricao like 'Faturamento avulso gerado referente % data $data[dataNormalData]' AND remessa != 'A'";
	$consulta 			= consultaSQL($sql, $conn);
	$resultado 			= resultadoSQL($consulta, 0, 'MAX(id)');
	$remessa			= resultadoSQL($consulta, 0, 'remessa');
	

	$idFaturamento = $resultado ? $resultado : novoIDFaturamento();

	
	return $idFaturamento;
}
?>
