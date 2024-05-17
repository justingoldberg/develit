<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 20/05/2003
# Ultima alteração: 02/02/2004
#    Alteração No.: 044
#
# Função:
#    Menus da aplicação


# Função para verificação de menus da aplicação
function menuPrincipal($tipo) {

	global $corFundo, $corBorda, $html;
	
	# Receber ID do Grupo do usuario
	if($grupoUsuario && contaConsulta($grupoUsuario)>0) {
		$idGrupo=resultadoSQL($grupoUsuario, 0, 'idGrupo');	
		
		# Buscar informações do grupo
		# receber informações do grupo
		$infoGrupo=buscaInfoGrupo($idGrupo);
	}
	
	if($tipo=='usuario') {
		htmlAbreTabelaSH("center", 760, 0, 1, 0, $corFundo, $corBorda, 4);
			htmlAbreLinha($corFundo);
				itemLinha("<img src=".$html[imagem][home]." border=0>PRINCIPAL", "?modulo=home", 'center', $corFundo, 0, 'titulo9');
				itemLinha("<img src=".$html[imagem][alterar]." border=0>MANUTENÇÃO DE BANCO DE DADOS", "?modulo=manutencao", 'center', $corFundo, 0, 'titulo9');
				itemLinha("<img src=".$html[imagem][importar]." border=0>IMPORTAÇÃO DE DADOS", "?modulo=importacao", 'center', $corFundo, 0, 'titulo9');
				itemLinha("<img src=".$html[imagem][desativar]." border=0>SAIR", "?modulo=logoff", 'center', $corFundo, 0, 'titulo9');
			htmlFechaLinha();
		fechaTabela();
	}


} # fecha visualizacao de menu



# Função para verificação de menus da aplicação
/**
 * @return void
 * @param unknown $modulo
 * @param unknown $sub
 * @param unknown $acao
 * @param unknown $registro
 * @param unknown $matriz
 * @desc Mostra os menus principais, primeira faixa de menus
*/
function verMenu($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin;

	### Menu principal - usuarios logados apenas
	if($modulo=='login' || !$sessLogin)
	{
		validacao($sessLogin, $modulo, $sub, $acao, $registro);
	}

	
	### MODULOS QUE REQUEREM AUTENTICAÇÃO
	else {
		if(checaLogin($sessLogin, $modulo, $sub, $acao, $registro) ) {
			## PRINCIPAL / HOME
			if(!$modulo || $modulo=='home') {
				dbmanager($modulo, $sub, $acao, $registro, $matriz);
			}
			## ACESSO
			elseif($modulo=='acesso') {
				acesso($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($modulo=='importacao') {
				DBManagerImportacao($modulo, $sub, $acao, $registro, $matriz);
			}
			## ACESSO
			else {
				dbmanager($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		
	}
} # fecha visualizacao de menu




# Menu principal
function dbManager($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	$sessLogin = $_SESSION["sessLogin"];
	
	# Buscar informações sobre usuario - permissões
	$permissao=buscaPermissaoUsuario($sessLogin[login]);

	# Mostrar menu
	novaTabela2("[HOME]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 4);
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('55%', 'left', $corFundo, 0, 'tabfundo1');
				echo "<br><img src=".$html[imagem][logoPequeno]." border=0 align=left>
				<b class=bold>$configAppName - HOME</b><br>
				<span class=normal10>Página principal e visualização de funções
				iniciais para o $configAppName.</span>";
			htmlFechaColuna();
			htmlAbreColuna('5%', 'left', $corFundo, 0, 'normal');
				echo "&nbsp;";
			htmlFechaColuna();									
			$texto=htmlMontaOpcao("<br>Registros por PessoaTipo", 'pessoa');
			itemLinha($texto, "?modulo=manutencao&sub=pessoatipo", 'center', $corFundo, 0, 'normal');
			$texto=htmlMontaOpcao("<br>Registros por POP", 'pops');
			itemLinha($texto, "?modulo=manutencao&sub=pop", 'center', $corFundo, 0, 'normal');
		fechaLinhaTabela();
	fechaTabela();
	
	# Modulos
	if($modulo=='manutencao') {
		# Menu de modulos
		manutencaoDBManager($modulo, $sub, $acao, $registro, $matriz);	
	}
	
}



# Menu de funções DBManager
function manutencaoDBManager($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	$sessLogin = $_SESSION["sessLogin"];
	
	# Buscar informações sobre usuario - permissões
	$permissao=buscaPermissaoUsuario($sessLogin[login]);
	
	if($permissao[admin]=='S') {
	
		# Modulos
		if($sub=='pop') {
			# Menu de modulos
			manutencaoDBManagerPOP($modulo, $sub, $acao, $registro, $matriz);	
		}
		elseif($sub=='pessoatipo') {
			# Menu de modulos
			manutencaoDBManagerPessoaTipo($modulo, $sub, $acao, $registro, $matriz);	
		}
	}
	
}




# Registros por POP
function manutencaoDBManagerPOP($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $html, $conn, $db;
	
	echo "<br>";
	dbManagerFormConsulta($modulo, $sub, $acao, $registro, $matriz);
	
	if($matriz[bntConfirmar]) {
		dbManagerConsultaPOP($modulo, $sub, $acao, $registro, $matriz);
	}
	
}



# função para form de seleção de filtros de faturamento
function dbManagerFormConsulta($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	# Motrar tabela de busca
	novaTabela2("[Filtros]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		#fim das opcoes adicionais
		novaLinhaTabela($corFundo, '100%');
		$texto="			
			<form method=post name=matriz action=$_SERVER[PHP_SELF]>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=registro value=$registro>&nbsp;";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>POP:</b><br>
			<span class=normal10>Selecione a POP de Acesso</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			if($matriz[pop_todos]) $opcPOP='checked';
			$texto="<input type=checkbox name=matriz[pop_todos] value=S $opcPOP><b>Todos</b>";
			itemLinhaForm(formSelectPOP($matriz[pop],'pop','multi').$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Detalhar Consulta:</b><br>
			<span class=normal10>Selecione esta opção para obter o detalhamento dos serviços</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			if($matriz[detalhar]=='S') $opcDetalhar='checked';
			$texto="<input type=checkbox name=matriz[detalhar] value='S' $opcDetalhar>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Eliminar registros encontrados:</b><br>
			<span class=normal10>Remover todos os registros encontrados no Banco de Dados</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			if($matriz[arquivotexto]=='S') $opcArquivoTexto='checked';
			$texto="<input type=checkbox name=matriz[remover] value='S' $opcArquivoTexto>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
		formPessoaSubmit($modulo, $sub, $acao, $registro, $matriz);
	
		htmlFechaLinha();
	fechaTabela();
	
}



# Função para consultar de Simulação de Faturamento
function dbManagerConsultaPOP($modulo, $sub, $acao, $registro, $matriz) {

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
	
	# Montar parametros de tipo de serviço
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
			$tb[PessoasTipos].id idPessoaTipo, 
			$tb[Pessoas].id idPessoa, 
			$tb[Pessoas].nome nome,
			$tb[Pessoas].tipoPessoa tipoPessoa,
			$tb[POP].nome pop
		FROM 
			$tb[PessoasTipos],
			$tb[Pessoas],
			$tb[POP]
		WHERE
			$tb[Pessoas].id = $tb[PessoasTipos].idPessoa
			and $tb[Pessoas].idPOP = $tb[POP].id
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
			novaTabela("[Faturamento por Cliente]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
			htmlAbreLinha($corFundo);
				itemLinhaTMNOURL("POP", 'center', 'middle', '20%', $corFundo, 0, 'tabfundo0');
				itemLinhaTMNOURL("Nome do Cliente", 'center', 'middle', '60%', $corFundo, 0, 'tabfundo0');
				itemLinhaTMNOURL("Tipo Pessoa", 'center', 'middle', '10%', $corFundo, 0, 'tabfundo0');
			htmlFechaLinha();
		}

		
		# Listagem de Planos com servicos e totais por serviço
		for($a=0;$a<contaConsulta($consultaPlanosAtivos);$a++) {
			# Consultar Planos da pessoa
			$idPessoaTipo=resultadoSQL($consultaPlanosAtivos, $a, 'idPessoaTipo');
			$idPessoa=resultadoSQL($consultaPlanosAtivos, $a, 'idPessoa');
			$nomePessoa=resultadoSQL($consultaPlanosAtivos, $a, 'nome');
			$tipoPessoa=resultadoSQL($consultaPlanosAtivos, $a, 'tipoPessoa');
			$nomePOP=resultadoSQL($consultaPlanosAtivos, $a, 'pop');
			# Mostrar Cliente
			
			# Consultar Planos ativos para a pessoa
			if($idPessoaTipo) {
				$sql="
					SELECT
						$tb[PlanosPessoas].idPessoaTipo idPessoaTipo, 
						$tb[Pessoas].id idPessoa, 
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
						$sqlADDPOP
					GROUP BY
						$tb[PlanosPessoas].id";
						
				htmlAbreLinha($corFundo);
					itemLinhaTMNOURL($nomePOP, 'center nowrap', 'middle', '20%', $corFundo, 0, 'bold8');
					itemLinhaTMNOURL("$idPessoaTipo - $nomePessoa", 'left', 'middle', '60%', $corFundo, 0, 'normal10');
					itemLinhaTMNOURL(formSelectPessoaTipo($tipoPessoa,'','check'), 'center', 'middle', '10%', $corFundo, 0, 'normal8');
				htmlFechaLinha();
				
				
				if($matriz[remover]=='S') {
					#### Remover registros
					# PessoasTipos
					//echo "PessoasTipo<br>";
					$matriz[id]=$idPessoaTipo;
					dbPessoaTipo($matriz,'excluir');
	
					# Enderecos
					//echo 'enderecos<br>';
					dbEndereco($matriz,'excluirtodos');
	
					# Pessoas
					//echo 'pessoas<br>';
					$matriz[id]=$idPessoa;
					dbPessoa($matriz,'excluir');
					
					# Documentos
					//echo 'documentos<br>';
					dbDocumento($matriz, 'excluirtodos');
					
					# PlanosPessoas
					//echo 'procurando pessoas<br>';
					$consultaPlanos=buscaPlanos($idPessoaTipo, 'idPessoaTipo','igual','id');
					if($consultaPlanos && contaconsulta($consultaPlanos)>0) {
						for($x=0;$x<contaConsulta($consultaPlanos);$x++)  {
							$idPlano=resultadoSQL($consultaPlanos, $x, 'id');
							
							# ServicosPlanos
							//echo 'procurando servicos planos<br>';
							$consultaServicos=buscaServicosPlanos($idPlano, 'idPlano','igual','id');
							if($consultaServicos && contaConsulta($consultaServicos)>0) {
								for($y=0;$y<contaConsulta($consultaServicos);$y++) {
									$idServicoPlano=resultadoSQL($consultaServicos, $y, 'id');
									
									# DominiosServicosPlanos
									//echo 'procurando dominios servicos planos<br>';
									$consultaDominiosServicos=buscaDominiosServicosPlanos($idServicoPlano, 'idServicosPlanos','igual','id');
									
									if($consultaDominiosServicos && contaConsulta($consultaDominiosServicos)>0) {
										for($z=0;$z<contaConsulta($consultaDominiosServicos);$z++) {
											$idDominio=resultadoSQL($consultaDominiosServicos, $z, 'idDominio');
											$idDominioServicoPlano=resultadoSQL($consultaDominiosServicos, $z, 'idDominio');
											
											# Dominios
											//echo 'dominios<br>';
											$matriz[id]=$idDominio;
											dbDominio($matriz, 'excluir');
											
											# Email
											//echo 'procurando email<br>';
											$consultaEmail=buscaEmails($idDominio, 'idDominio','igual','id');
											if($consultaEmail && contaConsulta($consultaEmail)>0) {
												for($w=0;$w<contaConsulta($consultaEmail);$w++) {
													$idEmail=resultadoSQL($consultaEmail, $w, 'id');
													
													# Email
													//echo 'email<br>';
													$matriz[id]=$idEmail;
													dbEmail($matriz,'excluir');
													
													# EmailAlias
													//echo 'alias<br>';
													dbEmailAlias($matriz,'excluiremail');
													
													# EmailForward
													//echo 'forward<br>';
													dbEmailForward($matriz, 'excluir');
													
													# EmailConfig
													//echo 'emailconfig<br>';
													dbEmailConfig($matriz, 'excluiremail');
													
													# EmailAutoReply
													//echo 'emailautoreply<br>';
													dbEmailAutoReply($matriz,'excluiremail');
													
												}
											}
											
											# DominiosParametros
											//echo 'dominios parametros<br>';
											dbDominiosParametros($matriz, 'excluir');
											
											# DominiosServicosPlanos
											//echo 'dominios servicos planos<br>';
											$matriz[id]=$idDominioServicoPlano;
											dbDominiosServicosPlanos($matriz, 'excluir');
										}
									}
								}
							}
							
							# ServicosPlanos
							//echo 'servicosplanos<br>';
							$matriz[id]=$idPlano;
							dbServicosPlano($matriz, 'excluirtodos');
							
						}
					}
					
					# Deletar Planos
					//echo 'planos<br>';
					$matriz[id]=$idPessoaTipo;
					dbPlano($matriz,'excluirtodos');
	
					# ServicosAdicionais
					//echo 'servicos adicionais<br>';
					dbServicoAdicional($matriz, 'excluirtodos');
					
					# DescontosServicosPlanos
					//echo 'descontos servicos planos<br>';
					dbDescontoServicoPlano($matriz, 'excluirtodos');
	
					# DocumentosGerados
					//echo 'consultando documentos gerados<br>';
					$consultaDocumentosGerados=buscaDocumentosGerados($idPessoaTipo, 'idPessoaTipo','igual','id');
					
					if($consultaDocumentosGerados && contaConsulta($consultaDocumentosGerados)>0) {
						for($x=0;$x<contaConsulta($consultaDocumentosGerados);$x++) {
							$idDocumentoGerado=resultadoSQL($consultaDocumentosGerados, $x, 'id');
							
							# PlanosDocumentosGerados
							//echo 'consultando planos documentos gerados<br>';
							$consultaPlanosDocumentosGerados=buscaPlanosDocumentosGerados($idDocumentoGerado, 'idDocumentoGerado','igual','id');
							if($consultaPlanosDocumentosGerados && contaConsulta($consultaPlanosDocumentosGerados)>0) {
							
								for($y=0;$y<contaConsulta($consultaPlanosDocumentosGerados);$y++) {
									$idPlanosDocumentosGerados=resultadoSQL($consultaPlanosDocumentosGerados, $y, 'id');
									
									# PlanosDocumentosGerados
									//echo 'planos documentos gerados<br>';
									$matriz[id]=$idPlanosDocumentosGerados;
									dbPlanoDocumentoGerado($matriz, 'excluir');
	
								}
								# ServicosPlanosDocumentosGerados
								//echo 'servicos planos documentos gerados<br>';
								$matriz[id]=$idPessoaTipo;
								dbServicoPlanoDocumentoGerado($matriz, 'excluirtodos');
							}
							
							# DocumentosGerados
							//echo 'documentos gerados<br>';
							$matriz[id]=$idDocumentoGerado;
							dbDocumentoGerado($matriz, 'excluir');
							
							# ContasAReceber
							//echo 'contas receber<br>';
							$matriz[idDocumentosGerados]=$idDocumentoGerado;
							dbContasReceber($matriz, 'excluir');
							
						}
					}
					
					# RadiusUsuariosPessoasTipos
					//echo 'radius pessoas tipos<br>';
					$consultaRadiusUsuariosPessoas=radiusBuscaUsuariosPessoasTipos($idPessoaTipo,'idPessoasTipos','igual','id');
					if($consultaRadiusUsuariosPessoas && contaConsulta($consultaRadiusUsuariosPessoas)>0) {
					
						for($x=0;$x<contaConsulta($consultaRadiusUsuariosPessoas);$x++) {
							$idRadiusUsuariosPessoasTipos=resultadoSQL($consultaRadiusUsuariosPessoas, $x, 'id');
							$idRadiusUsuarios=resultadoSQL($consultaRadiusUsuariosPessoas, $x, 'idRadiusUsuarios');
							
							//echo 'radius usuarios<br>';
							# RadiusUsuarios
							$matriz[idRadiusUsuarios]=$idRadiusUsuarios;
							radiusDBUsuario($matriz, 'excluir');
							
						}
						
						# RadiusUsuariosPessoasTipos
						//echo 'radius usuarios pessoas tipo<br>';
						$matriz[id]=$idPessoaTipo;
						radiusDBUsuarioPessoaTipo($matriz,'excluirtodos');
					}
					
					# Ocorrencias
					//echo 'procurando ocorrencias<br>';
					$consultaOcorrencias=buscaOcorrencias($idPessoaTipo,'idPessoaTipo','igual','id');
					if($consultaOcorrencias && contaConsulta($consultaOcorrencias)>0) {
						
						for($x=0;$x<contaConsulta($consultaOcorrencias);$x++) {
							$idOcorrencia=resultadoSQL($consultaOcorrencias, $x, 'id');
							
							# OcorrenciasComentarios
							//echo 'ocorrencias comentarios<br>';
							$matriz[id]=$idOcorrencia;
							dbOcorrenciaComentario($matriz, 'excluirocorrencia');
						}
						
						# Ocorrencias
						//echo 'ocorrencias <br>';
						$matriz[id]=$idPessoaTipo;
						dbOcorrencia($matriz, 'excluirtodos');
					}
				}				
						
				$consulta=consultaSQL($sql, $conn);
				
				if($consulta && contaConsulta($consulta)>0) {
					# Zerar total do cliente
					$matDetalhe='';
					# Procurar os serviços do plano para totalização
					for($b=0;$b<contaConsulta($consulta);$b++) {
						# Plano a ser selecionado e detalhado
						$idPlano=resultadoSQL($consulta, $b, 'idPlano');
						$status=resultadoSQL($consulta, $b, 'status');
						$nome=resultadoSQL($consulta, $b, 'nome');
						$nome=htmlMontaOpcao("<a href=index.php?modulo=lancamentos&sub=planos&acao=abrir&registro=$idPlano>$nome</a>",'planos');
						$especial=resultadoSQL($consulta, $b, 'especial');
						if(!$especial || $especial=='N') $tipoPlano="<span class=txtok>Plano Normal</span>";
						else $tipoPlano="<span class=txtaviso>Plano Especial</span>";
						$idFormaCobranca=resultadoSQL($consulta, $b, 'idFormaCobranca');
						$idVencimento=resultadoSQL($consulta, $b, 'idVencimento');
						$vencimento=dadosVencimento($idVencimento);
						$idPessoaTipo=resultadoSQL($consulta, $b, 'idPessoaTipo');
						
						# Montar tabela de detalhamento
						if($matriz[detalhar]=='S') {
							$matDetalhe[$b][nome]=$nome;
							$matDetalhe[$b][status]=$status;
							$matDetalhe[$b][valor]=$totalPlano;
						}
					}

					if($matriz[detalhar]=='S') {
						$keys=array_keys($matDetalhe);
						htmlAbreLinha($corFundo);
							htmlAbreColunaForm('100%', 'center', 'middle', $corFundo, 3, 'normal10');		
								novaTabelaSH("center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
								for($c=0;$c<count($keys);$c++) {
									htmlAbreLinha($corFundo);
										itemLinhaTMNOURL("<b>Plano:</b> ".$matDetalhe[$c][nome], 'left', 'middle', '60%', $corFundo, 0, 'normal10');
										itemLinhaTMNOURL(formSelectStatus($matDetalhe[$c][status],'','check'), 'center', 'middle', '20%', $corFundo, 0, 'normal10');
									htmlFechaLinha();
								}
								fechaTabela();
							htmlFechaColuna();
						htmlFechaLinha();
						itemTabelaNOURL('&nbsp;', 'center', $corFundo, 3, 'tabfundo1');
					}
					
				}
			}

		}
	}
	else {
		# Verificar faturamento dos clientes
		itemTabelaNOURL('&nbsp;', 'right', $corFundo, 0, 'normal10');
		novaTabela("[Faturamento de Clientes]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 0);
			# Mensagem de alerta de faturamento não encontrado
			$msg="<span class=txtaviso>Não foram encontrados registros para processamento!</span>";
			itemTabelaNOURL($msg, 'left', $corFundo, 0, 'normal10');
		fechaTabela();
	}
	return(0);
}


?>
