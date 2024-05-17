<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 09/07/2003
# Ultima alteração: 29/01/2004
#    Alteração No.: 010
#
# Função:
#    Painel - Funções para cadastro de tipos de documentos



# Lançamentos
function lancamentos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessPlanos, $modulos;
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao['admin'] && !$permissao['adicionar']) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		# Topo da tabela - Informações e menu principal do Cadastro
		novaTabela2("[Lançamentos]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 0);
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
					echo "<br><img src=".$html[imagem][lancamentos]." border=0 align=left><b class=bold>Lançamentos</b>
					<br><span class=normal10>Movimentações do sistema, controle de planos de serviços, relatórios,
					estatísticas e informações financeiras.</span>";
				htmlFechaColuna();
			fechaLinhaTabela();
		fechaTabela();
		
		if(!$matriz[tipoPessoa]) $matriz[tipoPessoa]='cli';
		
		if(!$acao) {
			# Mostrar menu de lançamentos
			echo "<br>";
			procurarPessoas('cadastros', 'clientes', 'procurar', $registro, $matriz);
			echo "<br>";
			novaTabela2SH("center", '100%', 0, 0, 0, $corFundo, $corBorda, 3);
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('50%', 'center" valign="top', $corFundo, 0, 'normal10');
						novaTabela2("[Cadastros Principais]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 0);
							$texto=htmlMontaOpcao("Cadastrar novo Cliente", 'incluir');
							itemTabela($texto, "?modulo=cadastros&sub=clientes&acao=novo", 'left', $corFundo, 0, 'normal');							
							$texto=htmlMontaOpcao("Cadastrar novo Serviço", 'servicos');
							itemTabela($texto, "?modulo=configuracoes&sub=servicos&acao=adicionar", 'left', $corFundo, 0, 'normal');							
						fechaTabela();
						echo "<br>";
						novaTabela2("[Rotinas]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 0);
							# esperar amadurecer o codigo para a proxima versao 1.0.28
							$texto = htmlMontaOpcao("Nota Fiscal", 'comentar');
							itemTabela( $texto, "?modulo=notafiscal",'left', $corFundo, 0, 'normal' );
							#
							$texto = htmlMontaOpcao("Nota Fiscal Fatura de Serviço", 'comentar');
							itemTabela( $texto, "?modulo=nf_faturaservico",'left', $corFundo, 0, 'normal' );
							#
							$texto=htmlMontaOpcao("Pagamentos Avulsos", 'lancamento');
							itemTabela($texto, "?modulo=pagamento_avulso", 'left', $corFundo, 0, 'normal');
							#
							$texto=htmlMontaOpcao("Faturamento", 'financeiro');
							itemTabela($texto, "?modulo=faturamento", 'left', $corFundo, 0, 'normal');
							$texto=htmlMontaOpcao("Arquivos Remessa", 'arquivo');
							itemTabela($texto, "?modulo=faturamento&sub=arquivoremessa&acao=listar", 'left', $corFundo, 0, 'normal');
							$texto=htmlMontaOpcao("Aplicar Descontos por Serviço", 'desconto');
							itemTabela($texto, "?modulo=lancamentos&sub=manutencao&acao=aplicardescontos", 'left', $corFundo, 0, 'normal');							
							# comentado pois as funções ainda não foram implementadas
							//$texto=htmlMontaOpcao("Aplicar Descontos por Cliente", 'desconto');
							//itemTabela($texto, "?modulo=lancamentos&sub=manutencao&acao=aplicardescontoscliente", 'left', $corFundo, 0, 'normal');							
						fechaTabela();
						if( $modulos['controleEstoque'] ) {
							echo "<br />";
							novaTabela2("[Controle de Estoque]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 0);
								# esperar amadurecer o codigo para a proxima versao 1.0.28
								$texto=htmlMontaOpcao("Nota Fiscal de Fornecedor", 'comentar');
								itemTabela($texto, "?modulo=movimentoEstoque&sub=entrada_nf", 'left', $corFundo, 0, 'normal');
								$texto=htmlMontaOpcao("Requisição / Retorno", 'comentar');
								itemTabela($texto, "?modulo=movimentoEstoque&sub=requisicao", 'left', $corFundo, 0, 'normal');
								$texto=htmlMontaOpcao("Ordem de Serviço", 'comentar');
								itemTabela($texto, "?modulo=movimentoEstoque&sub=ordemServico", 'left', $corFundo, 0, 'normal');
							fechaTabela();
						}
					htmlFechaColuna();
				
					itemLinhaTMNOURL('&nbsp;&nbsp;&nbsp;', 'center ', 'middle', '1', $corFundo, 0, 'normal10');
					htmlAbreColuna('50%', 'center" valign="top', $corFundo, 0, 'normal10');
						novaTabela2("[Lançamentos Principais]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 0);
							$texto=htmlMontaOpcao("Adicionar Plano de Serviços", 'planos');
							itemTabela($texto, "?modulo=cadastros&sub=clientes&acao=procurar", 'left', $corFundo, 0, 'normal');
							$texto=htmlMontaOpcao("Adicionar Serviço ao Plano do Cliente", 'incluir');
							itemTabela($texto, "?modulo=cadastros&sub=clientes&acao=procurar", 'left', $corFundo, 0, 'normal');
							$texto=htmlMontaOpcao("Adicionar Serviços Adicionais para Cliente", 'lancamento');
							itemTabela($texto, "?modulo=cadastros&sub=clientes&acao=procurar", 'left', $corFundo, 0, 'normal');
							$texto=htmlMontaOpcao("Adicionar Descontos de Serviços", 'desconto');
							itemTabela($texto, "?modulo=cadastros&sub=clientes&acao=procurar", 'left', $corFundo, 0, 'normal');
							#$texto=htmlMontaOpcao("Adicionar Ordem de Serviço", 'ordemdeservico');
							#itemTabela($texto, "?modulo=ordemdeservico&sub=ordemdeservico&acao=adicionar", 'left', $corFundo, 0, 'normal');
							# comentado aguardando correcao, previsao proxima versao 1.0.28
							#$texto=htmlMontaOpcao("Adicionar Produto", 'produto');
							#itemTabela($texto, "?modulo=produto&sub=produto&acao=adicionar", 'left', $corFundo, 0, 'normal');							
							fechaTabela();
						
						if( $permissao['admin'] ) {
							echo "<br>";
							novaTabela2("[Contas à Pagar]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 0);
								# esperar amadurecer o codigo para a proxima versao 1.0.28
								$texto=htmlMontaOpcao("Centros de Custo", 'lancamento');
								itemTabela($texto, "?modulo=centro_custo", 'left', $corFundo, 0, 'normal');
								$texto=htmlMontaOpcao("Plano de Contas", 'lancamento');
								itemTabela($texto, "?modulo=planos_contas", 'left', $corFundo, 0, 'normal');
								$texto=htmlMontaOpcao("Contas à Pagar", 'lancamento');
								itemTabela($texto, "?modulo=contas_a_pagar", 'left', $corFundo, 0, 'normal');
							fechaTabela();
						}
						echo "<br>";
						novaTabela2("[Informações e Relatórios]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 0);
							$texto=htmlMontaOpcao("Fluxo de Caixa", 'lancamento');
							itemTabela($texto, "?modulo=fluxo_caixa", 'left', $corFundo, 0, 'normal');
							$texto=htmlMontaOpcao("Consultas", 'consultas');
							itemTabela($texto, "?modulo=consultas", 'left', $corFundo, 0, 'normal');
						fechaTabela();
					htmlFechaColuna();
				fechaLinhaTabela();
			fechaTabela();
			# Mostrar Menus
			
		}
		
		# Planos
		if($sub=="planos") {
			
			# Planos
			if($acao=='listar' || $acao=='listartodos' || !$acao) {
				echo "<br>";
				listarPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='adicionar' || !$acao) {
				echo "<br>";
				adicionarPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='alterar') {
				echo "<br>";
				alterarPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='cancelar') {
				echo "<br>";
				cancelarPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='ativar') {
				echo "<br>";
				ativarPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='desativar') {
				echo "<br>";
				desativarPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='abrir' || $acao=='abrirtodos') {
				echo "<br>";
				abrirPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='ver') {
				echo "<br>";
				visualizarPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='calculo') {
				echo "<br>";
				calculosPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
			
			# Serviços dos Planos
			elseif($acao=='adicionarservico') {
				echo "<br>";
				adicionarServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='alterarservico') {
				echo "<br>";
				alterarServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='cancelarservico') {
				echo "<br>";
				cancelarServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='verservico') {
				echo "<br>";
				verServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='desativarservico') {
				echo "<br>";
				inativarServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='ativarservico') {
				echo "<br>";
				ativarServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif ($acao== 'mudarservico'){
				echo "<br>";
				planoEspecialMudancaServico($modulo,$sub,$acao,$registro,$matriz);
			}
			
			# Descontos
			elseif($acao=='descontosservico' || $acao=='descontosservicotodos') {
				echo "<br>";
				descontosServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao =='adicionardesconto') {
				echo "<br>";
				adicionarDescontoServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='alterardesconto') {
				echo "<br>";
				alterarDescontoServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='desativardesconto') {
				echo "<br>";
				desativarDescontoServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='ativardesconto') {
				echo "<br>";
				ativarDEscontoServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='cancelardesconto') {
				echo "<br>";
				cancelarDescontoServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='verdesconto') {
				echo "<br>";
				verDescontoServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
			
			# Serviços Adicionais
			elseif($acao=='servicosadicionais' || $acao=='servicosadicionaistodos') {
				echo "<br>";
				servicosAdicionais($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='adicionarservicoadicional') {
				echo "<br>";
				adicionarServicoAdicional($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='alterarservicoadicional') {
				echo "<br>";
				alterarServicosAdicionais($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='desativarservicoadicional') {
				echo "<br>";
				desativarServicosAdicionais($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='ativarservicoadicional') {
				echo "<br>";
				ativarServicosAdicionais($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='verservicoadicional') {
				echo "<br>";
				verServicosAdicionais($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='cancelarservicoadicional') {
				echo "<br>";
				cancelarServicosAdicionais($modulo, $sub, $acao, $registro, $matriz);
			}
			/* contra partidas*/
			elseif(substr($acao, 0, 13)=='contrapartida') {
				echo "<br>";
				contraPartida($modulo, $sub, $acao, $registro, $matriz);
			}
			
			/* Ativa todos os planos e servicos do cliente selecionados pelo usuário */
			elseif ($acao == 'ativarInativarClienteServico') {
				ativarInativarClienteServico($modulo, $sub, $acao, $registro, $matriz);
			}
			
			/* Inativa todos os planos e servicos do cliente
			elseif ($acao == 'inativarCliente') {
				inativarCliente($modulo, $sub, $acao, $registro, $matriz);
			}
			*/
		}
		
		# Manutenção
		elseif($sub=='manutencao') {
		
			if($acao=='aplicardescontos') {
				echo "<br>";
				manutencaoAplicarDescontos($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		
		# Ordem de Servico
		elseif($sub=='ordemdeservico') {
				ordemdeservico($modulo, $sub, $acao, $registro, $matriz);
		}

		echo "<script>location.href='#ancora';</script>";


	}
}

?>
