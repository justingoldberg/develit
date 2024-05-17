<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 15/04/2004
# Ultima alteração: 15/04/2004
#    Alteração No.: 001
#
# Função:
#     Consulta de Detalhamento de Clientes


# função para form de seleção de filtros de faturamento
/**
 * @return void
 * @param string $modulo
 * @param unknown $sub
 * @param unknown $acao
 * @param unknown $registro
 * @param unknown $matriz
 * @desc Formulário para detalhamento de cliente
 Campos para filtros:
 Busca pelo nome
 -> Listbox com clientes encontrados
 -> Checkbox para detalhamento
*/
function formDetalhamentoCliente($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	# Motrar tabela de busca
	novaTabela2("[Consulta Detalhada por Cliente]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
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
					itemLinhaTMNOURL('<b>Detalhar Planos:</b><br>
					<span class=normal10>Selecione esta opção para obter detalhes sobre serviços dos planos</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					if($matriz[detalhar]) $opcDetalhar='checked';
					$texto="<input type=checkbox name=matriz[detalhar] value='S' $opcDetalhar>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit name=matriz[bntConfirmar] value='Consultar' class=submit>";
					itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				
			}
		}
	
		htmlFechaLinha();
	fechaTabela();
}



# Função para consultar de Simulação de Faturamento
function consultaDetalhamentoCliente($modulo, $sub, $acao, $registro, $matriz) {

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
			$tb[PessoasTipos].id idPessoaTipo, 
			$tb[Pessoas].id idPessoa, 
			$tb[Pessoas].nome nomePessoa, 
			$tb[PessoasTipos].dtCadastro dtCadastro, 
			$tb[Pessoas].idPOP, 
			$tb[POP].nome nomePOP, 
			$tb[Pessoas].tipoPessoa 
		FROM
			$tb[POP], 
			$tb[Pessoas], 
			$tb[PessoasTipos] 
		WHERE
			$tb[Pessoas].id = $tb[PessoasTipos].idPessoa 
			AND $tb[Pessoas].idPOP = $tb[POP].id 
			AND $tb[PessoasTipos].id='$matriz[idPessoaTipo]'";
	
	if($sql) $consultaPlanosAtivos=consultaSQL($sql, $conn);
	
	if($consultaPlanosAtivos && contaconsulta($consultaPlanosAtivos) ) {
		
		# Cabeçalho
		itemTabelaNOURL('&nbsp;', 'right', $corFundo, 0, 'normal10');
		# Listagem de Planos com servicos e totais por serviço
		for($a=0;$a<contaConsulta($consultaPlanosAtivos);$a++) {
			# Consultar Planos da pessoa
			$idPessoaTipo=resultadoSQL($consultaPlanosAtivos, $a, 'idPessoaTipo');
			$nomePessoa=resultadoSQL($consultaPlanosAtivos, $a, 'nomePessoa');
			$nomePOP=resultadoSQL($consultaPlanosAtivos, $a, 'nomePOP');

			# Mostrar Cliente
			htmlAbreLinha($corFundo);
				htmlAbreColunaForm('100%', 'center', 'middle', $corFundo, 2, 'normal10');
					$matriz[tipoPessoa]='cli';
					verPessoas($modulo, $sub, $acao, $idPessoaTipo, $matriz);
					echo "<br>";
					
					novaTabela("[Planos do Cliente]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
					
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
								$sqlADD
							GROUP BY
								$tb[PlanosPessoas].id";
								
						$consulta=consultaSQL($sql, $conn);
						
						if($consulta && contaConsulta($consulta)>0) {

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
								
								$sqlServicos="
									SELECT 
										COUNT($tb[ServicosPlanos].id) qtde, 
										$tb[StatusServicos].status 
									FROM 
										$tb[ServicosPlanos], 
										$tb[StatusServicos]
									WHERE 
										$tb[ServicosPlanos].idStatus=$tb[StatusServicos].id 
										AND $tb[ServicosPlanos].idPlano=$idPlano
									GROUP BY $tb[StatusServicos].status";
								
								$consultaServicosPlanos=consultaSQL($sqlServicos, $conn);
								$qtdeServicos='';
								if($consultaServicosPlanos && contaConsulta($consultaServicosPlanos)>0) {
									for($a=0;$a<contaConsulta($consultaServicosPlanos);$a++) {
										$tmpQtde=resultadoSQL($consultaServicosPlanos, $a, 'qtde');
										$tmpStatus=resultadoSQL($consultaServicosPlanos, $a, 'status');
										
										$tmpStatus=formSelectStatus($tmpStatus, '','check');
										$qtdeServicos.="$tmpStatus&nbsp;<b>($tmpQtde)</b><br>";
									}
								}
								else $qtdeServicos="<span class=txtaviso>Nenhum</span>";								
								
								htmlAbreLinha($corFundo);
									itemLinhaTMNOURL("Nome do Plano", 'center', 'middle', '35%', $corFundo, 0, 'tabfundo0');
									itemLinhaTMNOURL("Vencimento", 'center', 'middle', '15%', $corFundo, 0, 'tabfundo0');
									itemLinhaTMNOURL("Forma Cobrança", 'center', 'middle', '20%', $corFundo, 0, 'tabfundo0');
									itemLinhaTMNOURL("Tipo Plano", 'center', 'middle', '15%', $corFundo, 0, 'tabfundo0');
									itemLinhaTMNOURL("Serviços", 'center', 'middle', '15%', $corFundo, 0, 'tabfundo0');
								htmlFechaLinha();
								
								# Mostrar resultado
								htmlAbreLinha($corFundo);
									itemLinhaTMNOURL($nome, 'left', 'middle', '35%', $corFundo, 0, 'tabfundo1');
									itemLinhaTMNOURL("Venc.: ".$vencimento[descricao], 'center', 'middle', '15%', $corFundo, 0, 'tabfundo1');
									itemLinhaTMNOURL(formSelectFormaCobranca($idFormaCobranca,'','check'), 'center', 'middle', '20%', $corFundo, 0, 'tabfundo1');
									itemLinhaTMNOURL($tipoPlano, 'center', 'middle', '15%', $corFundo, 0, 'tabfundo1');
									itemLinhaTMNOURL($qtdeServicos, 'center', 'middle', '15%', $corFundo, 0, 'tabfundo1');
								htmlFechaLinha();
								
								if($matriz[detalhar]) {
									$modulo='lancamentos';
									$sub='planos';
								
									# Detalhar serviços
									htmlAbreLinha($corFundo);
										htmlAbreColunaForm('100%', 'center', 'middle', $corFundo, 5, 'normal10');
											novaTabelaSH("center", '100%', 0, 2, 1, $corFundo, $corBorda, 6);
												$consultaServicos=buscaServicosPlanos("idPlano=$idPlano AND NOT dtCancelamento" , '','custom','idStatus, dtCadastro ASC');
												# Caso não hajam servicos para o servidor
												if(!$consultaServicos || contaConsulta($consultaServicos)==0) {
													# Não há registros
													itemTabelaNOURL('Não há servicos cadastrados', 'left', $corFundo, 6, 'txtaviso');
												}
												else {
											
													for($i=0;$i<contaConsulta($consultaServicos);$i++) {
														# Mostrar registro
														$id=resultadoSQL($consultaServicos, $i, 'id');
														$idPlano=resultadoSQL($consultaServicos, $i, 'idPlano');
														$idServico=resultadoSQL($consultaServicos, $i, 'idServico');
														$nomeServico=formSelectServicos($idServico, '','check');
														$nomeServico=htmlMontaOpcao("<a href=?modulo=configuracoes&sub=servicos&acao=alterar&registro=$idServico>$nomeServico</a>",'servicos');
														
														if($especial) {
															$valor=resultadoSQL($consultaServicos, $i, 'valor');
															$class='txtaviso';
														}
														else {
															# procurar valor do serviço
															$dadosServico=checkServico($idServico);
															$valor=$dadosServico[valor];
															$class='txtok';
														}
														
														$trial=resultadoSQL($consultaServicos, $i, 'diasTrial');
														$dtCadastro=resultadoSQL($consultaServicos, $i, 'dtCadastro');
														$dtAtivacao=resultadoSQL($consultaServicos, $i, 'dtAtivacao');
														$dtCancelamento=resultadoSQL($consultaServicos, $i, 'dtCancelamento');
														$idStatus=resultadoSQL($consultaServicos, $i, 'idStatus');
														$status=formSelectStatusServico($idStatus, '','check');
														$statusServico=checkStatusServico($idStatus);
														
														# Checar status
														if($planoEspecial && $statusPlano == 'C') {
															$class='txtaviso';
															$class2='txtaviso8';
															$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=verservico&registro=$id>Ver</a>",'ver');
														}
														else {
															if($statusServico[status]=='A') {
																$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=verservico&registro=$id>Ver</a>",'ver');
																if($planoEspecial) $opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterarservico&registro=$id>Alterar</a>",'alterar');
																$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=desativarservico&registro=$id>Inativar</a>",'ativar');
																$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=cancelarservico&registro=$id>Cancelar</a>",'cancelar');
																$opcoes.="<br>";
																$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=servicosadicionais&registro=$id>Serviços Adicionais</a>",'lancamento');
																$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=descontosservico&registro=$id>Descontos</a>",'desconto');
																$class='txtok';
																$class2='txtok8';
															}
															elseif($statusServico[status]=='I') {
																$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=verservico&registro=$id>Ver</a>",'ver');
																if($planoEspecial) $opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterarservico&registro=$id>Alterar</a>",'alterar');
																$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=ativarservico&registro=$id>Ativar</a>",'desativar');
																$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=cancelarservico&registro=$id>Cancelar</a>",'cancelar');
																$class='txtaviso';
																$class2='txtaviso8';
															}
															elseif($statusServico[status]=='T') {
																$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=verservico&registro=$id>Ver</a>",'ver');
																$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterarservico&registro=$id>Alterar</a>",'alterar');
																$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=ativarservico&registro=$id>Ativar</a>",'desativar');
																$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=desativarservico&registro=$id>Inativar</a>",'ativar');
																$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=cancelarservico&registro=$id>Cancelar</a>",'cancelar');
																$class='txttrial';
																$class2='txttrial8';
															}
															elseif($statusServico[status]=='N') {
																$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=verservico&registro=$id>Ver</a>",'ver');
																if($planoEspecial) $opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterarservico&registro=$id>Alterar</a>",'alterar');
																$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=ativarservico&registro=$id>Ativar</a>",'desativar');
																$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=desativarservico&registro=$id>Inativar</a>",'ativar');
																$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=cancelarservico&registro=$id>Cancelar</a>",'cancelar');
																$opcoes.="<br>";
																$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=servicosadicionais&registro=$id>Serviços Adicionais</a>",'lancamento');					
																$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=descontosservico&registro=$id>Descontos</a>",'desconto');
																$class='txtcheck';
																$class2='txtcheck8';
															}
															else {
																$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=verservico&registro=$id>Ver</a>",'ver');
																
																$class='txtaviso';
																$class2='txtaviso8';
															}
														}
											
														
											
														novaLinhaTabela($corFundo, '100%');
															itemLinhaTabela($nomeServico, 'left', '30%', 'normal10');
															itemLinhaTabela("<span class=$class>".formatarValoresForm($valor)."</span>", 'center', '10%', 'normal10');
															itemLinhaTabela("$trial dias", 'center', '5%', 'normal10');
															itemLinhaTabela($status[descricao], 'center', '10%', $class2);
															itemLinhaTabela(converteData($dtCadastro, 'banco','formdata'), 'center', '10%', 'normal8');
															itemLinhaTabela($opcoes, 'left', '35%', 'normal10');
														fechaLinhaTabela();
														
													} #fecha laco de montagem de tabela
													
												} #fecha servicos encontrados														
														
											fechaTabela();
										htmlFechaColuna();
									htmlFechaLinha();
								}
								
							}
						}
						else {
							# Mensagem de alerta de faturamento não encontrado
							$msg="<span class=txtaviso>Não foram encontrados registros para processamento!</span>";
							itemTabelaNOURL($msg, 'left', $corFundo, 4, 'normal10');
						}
						
					}
					
					fechaTabela();
				htmlFechaColuna();
			htmlFechaLinha();
		}

		# Rodapé com totais
		fechaTabela();
	
	}
	else {
		# Verificar faturamento dos clientes
		itemTabelaNOURL('&nbsp;', 'right', $corFundo, 0, 'normal10');
		novaTabela("[Detalhamento de Clientes]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 4);
			# Mensagem de alerta de faturamento não encontrado
			$msg="<span class=txtaviso>Não foram encontrados registros para processamento!</span>";
			itemTabelaNOURL($msg, 'left', $corFundo, 4, 'normal10');
		fechaTabela();
	}
	return(0);
}


?>