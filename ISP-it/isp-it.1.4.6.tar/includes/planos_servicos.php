<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 10/07/2003
# Ultima alteração: 16/02/2004
#    Alteração No.: 025
#
# Função:
#    Painel - Funções para servicos dos planos


# Função de banco de dados - Pessoas
function dbServicosPlano($matriz, $tipo, $registro = '') {

	global $conn, $tb, $modulo, $sub, $acao;
	
	# Sql de inclusão
	if($tipo=='incluir') {
		$sql = "INSERT INTO $tb[ServicosPlanos] VALUES (0, $matriz[idPlano], $matriz[idServico], '$matriz[valor]',
				'$matriz[dtCadastro]', '$matriz[dtAtivacao]', '$matriz[dtInativacao]', '$matriz[dtCancelamento]',
				'$matriz[status]', '$matriz[trial]')";
		
	} #fecha inclusao
	elseif($tipo=='excluir') {
		$sql = "DELETE FROM $tb[ServicosPlanos] where id=$matriz[id]";
	}
	elseif($tipo=='excluirtodos') {
		$sql = "DELETE FROM $tb[ServicosPlanos] where idPlano=$matriz[id]";
	}
	elseif($tipo=='alterar') {
		
		$sql = "UPDATE $tb[ServicosPlanos] SET diasTrial='$matriz[trial]', dtCadastro='$matriz[dtCadastro]', 
				dtAtivacao='$matriz[dtAtivacao]',
		";
		if ($matriz[idServico]){
			$sql .= " idServico='$matriz[idServico]',";
		}
		
		$sql.= " valor='$matriz[valor]' WHERE id=$matriz[id]";
			
	}
	elseif($tipo=='cancelar') {
		$sql = "UPDATE $tb[ServicosPlanos] SET dtCancelamento='$matriz[dtCancelamento]'," .
			   "idStatus='$matriz[idStatus]' WHERE id=$matriz[id]";

		# Verifica se o ISP está integrado ao Ticket-IT. Caso esteja, ao cancelar um serviço
		# O(s) suporte(s) do(s) serviço(s) deverá(ão) ser bloqueado(s) automaticamente
		# por Felipe Assis - 12/06/2008 
		$parametros = carregaParametrosConfig();
		if(strtoupper($parametros['integrarTicketISP']) != 'S'){
			// implementar aqui bloqueio de suporte automático
			bloqueioSuporteAutomatico($matriz['id']);
		}
	}
	elseif($tipo=='inativar') {
		$sql = "UPDATE $tb[ServicosPlanos] SET dtInativacao='$matriz[dtInativacao]', 
				idStatus='$matriz[idStatus]' WHERE id=$matriz[id]";
	}
	elseif($tipo=='ativar') {
		$data=dataSistema();
		
		$sql = "UPDATE $tb[ServicosPlanos] SET dtInativacao='$matriz[dtInativacao]', 
				dtAtivacao='$matriz[dtAtivacao]',dtCancelamento='', idStatus='$matriz[idStatus]' 
				WHERE id=$matriz[id]";
	}
	elseif ($tipo == "ativarCliente") {
		$data = dataSistema();
		
		$sql = "UPDATE $tb[ServicosPlanos] SET dtInativacao='$matriz[dtInativacao]', 
				dtCancelamento='', idStatus='$matriz[idStatus]' 
				WHERE id=$matriz[id]";
	}
	elseif( $tipo == 'consultar' ){
		$sql = $matriz['consulta'];
	}
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno);
	}
}



# Função de banco de dados - Pessoas
function cancelarTodosServicosPlanos($matriz, $tipo) {

	global $conn, $tb, $modulo, $sub, $acao;
	
	$data=dataSistema();
	
	# Sql de inclusão
	if($tipo=='cancelar') {
		$sql = "UPDATE $tb[ServicosPlanos] SET dtCancelamento='$data[dataBanco]', 
				idStatus='$matriz[statusServico]' WHERE idPlano=$matriz[id]";
	}
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
}


# função de busca 
function buscaServicosPlanos($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub, $conn;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[ServicosPlanos] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[ServicosPlanos] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[ServicosPlanos] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[ServicosPlanos] WHERE $texto ORDER BY $ordem";
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




# Função para listagem 
function listarServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessPlanos, $tb, $conn ;
	
	# Gravar Session de Planos - Informações da Pessoa
	$sessPlanos[idPessoaTipo]=$registro;

	echo "<br>";
	
	if($acao=='abrirtodos') $consulta=buscaServicosPlanos($registro, 'idPlano','igual','idStatus, dtCadastro ASC');
	else {
		//$consulta=buscaServicosPlanos("idPlano=$registro AND NOT dtCancelamento" , '','custom','idStatus, dtCadastro ASC');
	
		$sql="
			SELECT
				$tb[ServicosPlanos].*
			FROM
				$tb[ServicosPlanos],
				$tb[StatusServicos]
			WHERE
				$tb[ServicosPlanos].idStatus = $tb[StatusServicos].id
				AND $tb[StatusServicos].status != 'C'
				AND $tb[ServicosPlanos].idPlano = $registro
			ORDER BY
				$tb[StatusServicos].status,
				$tb[ServicosPlanos].dtCadastro ASC
		";
		
		$consulta=consultaSQL($sql, $conn);
	}
	
	# Checa se plano é especial
	$planoEspecial=checkPlanoEspecial($registro);
	$statusPlano=checkStatusPlano($registro);
	
	# Cabeçalho		
	# Motrar tabela de busca
	novaTabela("[Serviços]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 6);
	$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionarservico&registro=$registro>Adicionar Serviço</a>",'incluir');
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro>Serviços Ativos</a>",'listar');
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=abrirtodos&registro=$registro>Todos os Serviços</a>",'listar');
	itemTabelaNOURL($opcoes, 'right', $corFundo, 6, 'tabfundo1');
	
	# Caso não hajam servicos para o servidor
	if(!$consulta || contaConsulta($consulta)==0) {
		# Não há registros
		itemTabelaNOURL('Não há serviços cadastrados.', 'left', $corFundo, 6, 'txtaviso');
	}
	else {

		# Cabeçalho
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTabela('Serviço', 'center', '30%', 'tabfundo0');
			itemLinhaTabela('Valor', 'center', '10%', 'tabfundo0');
			itemLinhaTabela('Trial', 'center nowrap', '5%', 'tabfundo0');
			itemLinhaTabela('Status', 'center', '10%', 'tabfundo0');
			itemLinhaTabela('Data Cadastro', 'center', '10%', 'tabfundo0');
			itemLinhaTabela('Opções', 'center', '35%', 'tabfundo0');
		fechaLinhaTabela();

		for($i=0;$i<contaConsulta($consulta);$i++) {
			# Mostrar registro
			$id=resultadoSQL($consulta, $i, 'id');
			$idPlano=resultadoSQL($consulta, $i, 'idPlano');
			$idServico=resultadoSQL($consulta, $i, 'idServico');
			$nomeServico=formSelectServicos($idServico, '','check');
			$nomeServico=htmlMontaOpcao("<a href=?modulo=configuracoes&sub=servicos&acao=alterar&registro=$idServico>$nomeServico</a>",'servicos');
			
			if($planoEspecial) {
				$valor=resultadoSQL($consulta, $i, 'valor');
				$class='txtaviso';
			}
			else {
				# procurar valor do serviço
				$dadosServico=checkServico($idServico);
				$valor=$dadosServico[valor];
				$class='txtok';
			}
			
			$trial=resultadoSQL($consulta, $i, 'diasTrial');
			$dtCadastro=resultadoSQL($consulta, $i, 'dtCadastro');
			$dtAtivacao=resultadoSQL($consulta, $i, 'dtAtivacao');
			$dtCancelamento=resultadoSQL($consulta, $i, 'dtCancelamento');
			$idStatus=resultadoSQL($consulta, $i, 'idStatus');
			$status=formSelectStatusServico($idStatus, '','check');
			$statusServico=checkStatusServico($idStatus);
			
			# Checar status
			if($planoEspecial && $statusPlano == 'C') {
				$class='txtaviso';
				$class2='txtaviso8';
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=verservico&registro=$id>Ver</a>",'ver');
				$opcoes.="<br>";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=servicosadicionais&registro=$id>Serviços Adicionais</a>",'lancamento');					
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=descontosservico&registro=$id>Descontos</a>",'desconto');

			}
			else {
				if($statusServico[status]=='A') {
					$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=verservico&registro=$id>Ver</a>",'ver');
					$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterarservico&registro=$id>Alterar</a>",'alterar');
					$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=desativarservico&registro=$id>Inativar</a>",'desativar');
					$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=cancelarservico&registro=$id>Cancelar</a>",'cancelar');
					$opcoes.="<br>";
					$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=servicosadicionais&registro=$id>Serviços Adicionais</a>",'lancamento');
					$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=descontosservico&registro=$id>Descontos</a>",'desconto');
					$opcoes.="<br>";
					$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=mudarservico&registro=$id>Mudar Serviço</a>",'renovar');
					$class='txtok';
					$class2='txtok8';
				}
				elseif($statusServico[status]=='I') {
					$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=verservico&registro=$id>Ver</a>",'ver');
					if($planoEspecial) $opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterarservico&registro=$id>Alterar</a>",'alterar');
					$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=ativarservico&registro=$id>Ativar</a>",'ativar');
					$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=cancelarservico&registro=$id>Cancelar</a>",'cancelar');
					$class='txtaviso';
					$class2='txtaviso8';
				}
				elseif($statusServico[status]=='T') {
					$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=verservico&registro=$id>Ver</a>",'ver');
					$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterarservico&registro=$id>Alterar</a>",'alterar');
					$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=ativarservico&registro=$id>Ativar</a>",'ativar');
					$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=desativarservico&registro=$id>Inativar</a>",'desativar');
					$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=cancelarservico&registro=$id>Cancelar</a>",'cancelar');
					$class='txttrial';
					$class2='txttrial8';
				}
				elseif($statusServico[status]=='N') {
					$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=verservico&registro=$id>Ver</a>",'ver');
					if($planoEspecial) $opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterarservico&registro=$id>Alterar</a>",'alterar');
					$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=ativarservico&registro=$id>Ativar</a>",'ativar');
					$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=desativarservico&registro=$id>Inativar</a>",'desativar');
					$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=cancelarservico&registro=$id>Cancelar</a>",'cancelar');
					$opcoes.="<br>";
					$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=servicosadicionais&registro=$id>Serviços Adicionais</a>",'lancamento');					
					$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=descontosservico&registro=$id>Descontos</a>",'desconto');
					$class='txtcheck';
					$class2='txtcheck8';
				}
				else {
					$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=verservico&registro=$id>Ver</a>",'ver');
					$opcoes.="<br>";
					$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=servicosadicionais&registro=$id>Serviços Adicionais</a>",'lancamento');					
					$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=descontosservico&registro=$id>Descontos</a>",'desconto');
					
					$class='txtaviso';
					$class2='txtaviso8';
				}
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=contrapartidalistar&registro=$id>Contra Partida</a>",'prioridade');
				$opcoes.="<br>".htmlMontaOpcao("<a href=?modulo=pagamento_avulso&sub=&acao=adicionar&idServicoPlano=$id>Lançar Pagamento Avulso</a>",'lancamento');
			}

			// caso exista algum dominio no idServicosPlanos atual, exibe o mesmo conforme solicitado pelos clientes
			$dominio =  buscarNomesDominiosServicosPlanos($id) ; 
			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela($nomeServico . $dominio, 'center', '30%', 'normal10');
				itemLinhaTabela("<span class=$class>".formatarValoresForm($valor)."</span>", 'center', '10%', 'normal10');
				itemLinhaTabela("$trial dias", 'center', '5%', 'normal10');
				itemLinhaTabela($status[descricao], 'center', '10%', $class2);
				itemLinhaTabela(converteData($dtCadastro, 'banco','formdata'), 'center', '10%', 'normal8');
				itemLinhaTabela($opcoes, 'left', '35%', 'normal8');
			fechaLinhaTabela();
			
		} #fecha laco de montagem de tabela
		
		fechaTabela();
	} #fecha servicos encontrados
	
}#fecha função de listagem




# função para adicionar pessoa
function adicionarServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $sessLogin;
	
		# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[adicionar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função.";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		
		# Recebe ID do Plano - Procurar por ID da Pessoa
		$consulta=buscaPlanos($registro, 'id','igual','id');
		
		if($consulta && contaConsulta($consulta)>0) {
			# prosseguir e mostarar pessoa e plano
			$idPessoa=resultadoSQL($consulta, 0, 'idPessoaTipo');
			$status=resultadoSQL($consulta, 0, 'status');
			
			# Ver dados da pessoa
			verPessoas('cadastros', 'clientes', 'ver', $idPessoa, $matriz);
			echo "<br>";
			
			# Ver dados do Plano
			verPlanos($modulo, $sub, $acao, $registro, $matriz);
			echo "<br>";
			
			# Checar Status do Plano
			if($status!='A') {
				$msg="Plano está Inativo ou Cancelado e não pode receber serviços!";
				$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
				avisoNOURL("Aviso", $msg, 600);
			}
			else {
			
				if(!$matriz[bntConfirmar]) {
					# Formulário para adição de Serviço
					formServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
				}
				else {
					# Gravar registro
					
					$data=dataSistema();
					
					if(!$matriz[dtCadastro]) $matriz[dtCadastro]=$data[dataBanco];
					else {
						$matriz[dtCadastro]=formatarData($matriz[dtCadastro]);
						$matriz[dtCadastro]=substr($matriz[dtCadastro],0,2).'/'.substr($matriz[dtCadastro],2,2).'/'.substr($matriz[dtCadastro],4,4);
						$matriz[dtCadastro]=converteData($matriz[dtCadastro],'form','bancodata');
					}
					
					# consultar se serviço gera cobrança (S=setar data de ativação)
					$statusCobranca=checkStatusServico($matriz[status]);
					if($statusCobranca[cobranca]=='S' || $statusCobranca[status]=='A') {
						$matriz[dtAtivacao]=$matriz[dtCadastro];
						$matriz[valor]=formatarValores($matriz[valor]);
					}
					
					$grava=dbServicosPlano($matriz, 'incluir');
					$idNovoServicosPlanos = mysql_insert_id();
					
					if($grava) {
						
						#
						#20050915, gustavo, TICKET FU5426R
						#
						$matriz['idServicoPlano'] = $idNovoServicosPlanos;
						
						$dadosPlano = dadosPlanos($matriz['idPlano']);
						
						$matriz['vencimento'] = dadosVencimento($dadosPlano['idVencimento']);
												
						if ($matriz['vencimento']['diaFaturamento'] == $matriz['vencimento']['diaVencimento']){
							#somente ira gerar cobranca se o novo status for cobrado
							$dadosCob=checkStatusServico($matriz['status']);
							
							$consServico = buscaServicos($matriz['idServico'], 'id', 'igual', 'id');
							$idTipoCobranca =  resultadoSQL($consServico, 0, 'idTipoCobranca');
			
							$consTipoCobranca= buscaTipoCobranca($idTipoCobranca, 'id', 'igual', 'id');
							$matriz['tipoCobranca']['proporcional'] = resultadoSQL($consTipoCobranca, 0, 'proporcional');
							$matriz['tipoCobranca']['tipo'] = resultadoSQL($consTipoCobranca, 0, 'tipo');
							
							if ($dadosCob['cobranca'] == 'S' && $matriz['tipoCobranca']['tipo'] != 'pre'){
								if($dadosPlano['especial'] == 'S')
									$matriz['valorServico'] = $matriz['valor'];
								else{
									
									$matriz['valorServico'] = resultadoSQL($consServico, 0, 'valor');
								}
								$matriz['dtAtivacao'] = converteData($matriz['dtAtivacao'],'banco','formdata'); 
/*								$ativ = adicionarServicoAdicionalServicoPlanoNovo($matriz, true);
								if ($ativ){
									$matriz['dtAtivacao'] = converteData($ativ,'form','bancodata');
									$matriz['id'] = $matriz['idServicoPlano'];
									$matriz['idStatus'] = $matriz['status'];
									#atualizo  a data de ativacao;
									dbServicosPlano($matriz, 'ativar');
								}*/
								adicionarServicoAdicionalServicoPlanoNovo($matriz, true);/**/
																
							}
						}
						#
						#fim da alteracao
						#
						
						# OK
						$msg="Serviço adicionado com sucesso!";
						$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
						avisoNOURL("Aviso", $msg, 600);
						
						#verifica se há contra partidadas. se houver as adiciona agora.
						$parametro = buscaDadosParametro('contra_partida', 'parametro', 'igual', 'id');
						
						if ($parametro){
							$texto =  "idParametro =" . $parametro[id] . " AND idServico = " . $matriz[idServico];
							$parametro = buscaParametrosServico($texto, '', 'custom', 'idParametro');
							
							if (contaConsulta($parametro)>0)
								$parametroContraPartida= resultadoSQL($parametro, 0, 'valor');
						}
						
						if($parametroContraPartida == "S")
							contraPartidaAdicionar($modulo, $sub, 'contrapartidaadicionar', $idNovoServicosPlanos, $matriz);
						else
							listarServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
					}
					else {
						# Erro
						$msg="ERRO ao adicionar serviço! Tente novamente!";
						$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
						avisoNOURL("Aviso", $msg, 600);
					}
				}
			}
		}
		else {
			# Erro
			$msg="ERRO ao selecionar o Plano do Cliente!";
			$url="?modulo=cadastros&sub=clientes";
			aviso("Aviso", $msg, $url, 760);
		}

	}//permissoes
}




# formulário de dados cadastrais
function formServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;

	$data=dataSistema();

	# Pessoa física
	novaTabela2("[Serviços do Plano]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	novaLinhaTabela($corFundo, '100%');
		$texto="			
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=registro value=$registro>
			<input type=hidden name=matriz[idPlano] value=$registro>
			<input type=hidden name=acao value=$acao>&nbsp;";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
	fechaLinhaTabela();

	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL('<b>Serviço:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		$texto="<input type=submit name=matriz[bntSelecionar] value=Selecionar class='submit'>";
		itemLinhaForm(formSelectServicos($matriz[idServico], 'idServico','form').$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
	
	if($matriz[idServico]) {
		# Selecionar Serviços
		$consultaServico=buscaServicos($matriz[idServico], 'id','igual','id');
		if($consultaServico && contaConsulta($consultaServico)>0) {
			# informaçoes do serviço
			$nome=resultadoSQL($consultaServico, 0, 'nome');
			$descricao=resultadoSQL($consultaServico, 0, 'descricao');
			$idTipoCobranca=resultadoSQL($consultaServico, 0, 'idTipoCobranca');
			$valor=resultadoSQL($consultaServico, 0, 'valor');
			$idStatusPadrao=resultadoSQL($consultaServico, 0, 'idStatusPadrao');
			
			$matriz[status]=$idStatusPadrao;
			
			if(!$matriz[valor]) $matriz[valor]=formatarValoresForm($valor);
			
			# Buscar informações sobre o serviço 
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Valor do Serviço:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL($valor, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Tipo de Cobrança:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL(formSelectTipoCobranca($idTipoCobranca, '','check'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			# Verificar se plano é especial - Caso SIM - pedir valor especial do serviço
			if(checkPlanoEspecial($registro)) {
				itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
				novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('100%', 'center', $corFundo, 2, 'tabfundo1');
					novaTabela2("[Plano Especial]", "center", '600', 0, 2, 1, $corFundo, $corBorda, 2);
						novaLinhaTabela($corFundo, '100%');
							$texto="<span class=txtaviso>ATENÇÃO:
								Planos especiais requerem os valores especiais de cada serviço! <br>
								Informe o valor especial para este serviço!</span><br>
								<br>
								<span class=txtaviso>(Formato: ".formatarValores($valor)." => $valor) - (Valor do Serviço: R$ $valor)</span>";
								itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
							fechaLinhaTabela();
						fechaLinhaTabela();
						itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL('<b>Desconto:</b>', 'right', 'middle', '50%', $corFundo, 0, 'tabfundo1');
							$valorCompara=formatarValores($valor);
							itemLinhaTMNOURL(formSelectDesconto($matriz[desconto], 'desconto','form', $valor, 8), 'left', 'middle', '50%', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
						$valorFormatado=formatarValoresForm($valor);
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL('<b>Valor especial do Serviço:</b>', 'right', 'middle', '50%', $corFundo, 0, 'tabfundo1');
							$texto="<input type=text name=matriz[valor] value='$valorFormatado' size=10 onBlur=verificarValor($valorCompara,this.value);formataValor(this.value,8)>";
							itemLinhaTMNOURL($texto, 'left', 'middle', '50%', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
					fechaTabela();
				htmlFechaColuna();
				fechaLinhaTabela();
				itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
			}
			
			# Informar Status do Serviço
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Dias de Trial:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[trial] value='$matriz[trial]' size=3>
				<span class=txtaviso>(ATENÇÃO: Estes dias não serão cobrados após a Ativação do Serviço)</span>";
				itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Status do Serviço:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL(formSelectStatusServico($matriz[status], 'status','form'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Data de Cadastro:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				if(checkPlanoEspecial($registro)) $indice=11;
				else $indice=9;
				$texto="<input name=matriz[dtCadastro] size=10 value=".formatarData(substr($data[dataNormal],0,10))." onBlur=verificaData(this.value,$indice)>";
				itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			formPessoaSubmit($modulo, $sub, $acao, $registro, $matriz);
		}
		else {
			# Mensagem de aviso
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('100%', 'center', $corFundo, 2, 'tabfundo1');
					$msg="Não foi possível localizar o serviço selecionado!<br>";
					$msg.="Consulte a listagem de serviços disponíveis e tente novamente!";
					$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$registro";
					avisoNOURL("Aviso", $msg, '400');
				htmlFechaColuna();
			fechaLinhaTabela();
		}
		
		
	}

	fechaTabela();
}




# formulário de dados cadastrais
function formAlterarServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;

	# Pessoa física
	novaTabela2("[Serviços do Plano - Alteração]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	# Opcoes Adicionais
	menuOpcAdicional($modulo, $sub, $acao, $matriz[idPlano]);
	novaLinhaTabela($corFundo, '100%');
		$texto="			
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=registro value=$registro>
			<input type=hidden name=matriz[idPlano] value=$matriz[idPlano]>
			<input type=hidden name=matriz[idPessoaTipo] value=$matriz[idPessoaTipo]>
			<input type=hidden name=matriz[idStatus] value=$matriz[idStatus]>
			<input type=hidden name=matriz[id] value=$registro>
			<input type=hidden name=acao value=$acao>&nbsp;";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
	fechaLinhaTabela();

	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL('<b>Serviço:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		itemLinhaForm(formSelectServicos($matriz[idServico], 'idServico','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
	
	if($matriz[idServico]) {
		# Selecionar Serviços
		$consultaServico=buscaServicos($matriz[idServico], 'id','igual','id');
		if($consultaServico && contaConsulta($consultaServico)>0) {
			# informaçoes do serviço
			$nome=resultadoSQL($consultaServico, 0, 'nome');
			$descricao=resultadoSQL($consultaServico, 0, 'descricao');
			$idTipoCobranca=resultadoSQL($consultaServico, 0, 'idTipoCobranca');
			$valor=resultadoSQL($consultaServico, 0, 'valor');
			
			if(!$matriz[valor]) $matriz[valor]=formatarValoresForm($valor);
			
			# Buscar informações sobre o serviço 
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Valor do Serviço:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL($valor, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Tipo de Cobrança:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL(formSelectTipoCobranca($idTipoCobranca, '','check'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			
			# Verificar se plano é especial - Caso SIM - pedir valor especial do serviço
			if(checkPlanoEspecial($matriz[idPlano])) {
				itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
				novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('100%', 'center', $corFundo, 2, 'tabfundo1');
					novaTabela2("[Plano Especial]", "center", '600', 0, 2, 1, $corFundo, $corBorda, 2);
						novaLinhaTabela($corFundo, '100%');
							$texto="<span class=txtaviso>ATENÇÃO:
								Planos especiais requerem os valores especiais de cada serviço! <br>
								Informe o valor especial para este serviço!</span><br>
								<br>
								<span class=txtaviso>(Formato: ".formatarValores($valor)." => $valor) - (Valor do Serviço: R$ $valor)</span>";
								itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
							fechaLinhaTabela();
						fechaLinhaTabela();
						itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL('<b>Desconto:</b>', 'right', 'middle', '50%', $corFundo, 0, 'tabfundo1');
							$valorCompara=formatarValores($valor);
							itemLinhaTMNOURL(formSelectDesconto($matriz[desconto], 'desconto','form', $valor, 9), 'left', 'middle', '50%', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL('<b>Valor especial do Serviço:</b>', 'right', 'middle', '50%', $corFundo, 0, 'tabfundo1');
							$texto="<input type=text name=matriz[valor] value='$matriz[valor]' size=10 onBlur=verificarValor($valorCompara,this.value);formataValor(this.value,9)>";
							itemLinhaTMNOURL($texto, 'left', 'middle', '50%', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
					fechaTabela();
				htmlFechaColuna();
				fechaLinhaTabela();
				itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
			}
			
			verDatasServicoPlano($matriz);
			
			# Informar Status do Serviço
			# Checar se o serviço ainda está em trial
			$statusServico=checkStatusServico($matriz[idStatus]);
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Dias de Trial:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[trial] value='$matriz[diasTrial]' size=3>
				<span class=txtaviso>(ATENÇÃO: Estes dias não serão cobrados após a Ativação do Serviço)</span>";
				itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			/*
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Status do Serviço:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL(formSelectStatusServico($matriz[idStatus], 'idStatus','form'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			*/
			# Verificar se serviço está ativo
			if($statusServico[status]=='A') {
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Data de Cadastro:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
//					if(checkPlanoEspecial($registro)) $indice=13;
				if(checkPlanoEspecial($matriz['idPlano'])) $indice=11;
					else $indice=9;
					$texto="<input name=matriz[dtCadastro] size=10 value=".formatarData(converteData($matriz[dtCadastro],'banco','formdata'))." onBlur=verificaData(this.value,$indice)>";
					itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Data de Ativação:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
//					if(checkPlanoEspecial($registro)) $indice=14;
					if(checkPlanoEspecial($matriz['idPlano'])) $indice=12;
					else $indice=10; 
					$texto="<input name=matriz[dtAtivacao] size=10 value=".formatarData(converteData($matriz[dtAtivacao],'banco','formdata'))." onBlur=verificaData(this.value,$indice)>";
					itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			}
			else {
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Data de Cadastro:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					if(checkPlanoEspecial($registro)) $indice=13;
					else $indice=11;
					$texto="<input name=matriz[dtCadastro] size=10 value=".formatarData(converteData($matriz[dtCadastro],'banco','formdata'))." onBlur=verificaData(this.value,$indice)>";
					itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			}
			
			formPessoaSubmit($modulo, $sub, $acao, $registro, $matriz);
			
		}
		else {
			# Mensagem de aviso
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('100%', 'center', $corFundo, 2, 'tabfundo1');
					$msg="Não foi possível localizar o serviço selecionado!<br>";
					$msg.="Consulte a listagem de serviços disponíveis e tente novamente!";
					$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$registro";
					avisoNOURL("Aviso", $msg, '400');
				htmlFechaColuna();
			fechaLinhaTabela();
		}
		
		
	}

	fechaTabela();

}





# formulário de dados cadastrais
function formCancelarServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	# Verificando status do serviço
	$statusCancelado = checkStatusStatusServico('C','status','igual','status');
	
	$data=dataSistema();
	
	# Pessoa física
	novaTabela2("[Serviços do Plano - Cancelamento]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	# Opcoes Adicionais
	menuOpcAdicional($modulo, $sub, $acao, $matriz[idPlano]);
	novaLinhaTabela($corFundo, '100%');
		$texto="			
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=registro value=$registro>
			<input type=hidden name=matriz[id] value=$registro>
			<input type=hidden name=matriz[idPlano] value=$matriz[idPlano]>
			<input type=hidden name=matriz[idPessoaTipo] value=$matriz[idPessoaTipo]>
			<input type=hidden name=matriz[status] value=$statusCancelado[id]>
			<input type=hidden name=acao value=$acao>&nbsp;";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
	fechaLinhaTabela();

// caso o servico contenha dominios, exibilos para facilitar quem esta removendo o plano
	$dominio = buscarNomesDominiosServicosPlanos($registro);

	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL('<b>Serviço:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		itemLinhaForm(formSelectServicos($matriz[idServico], 'idServico','check') . $dominio, 'left', 'top', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
	
	if($matriz[idServico]) {
		# Selecionar Serviços
		$consultaServico=buscaServicos($matriz[idServico], 'id','igual','id');
		if($consultaServico && contaConsulta($consultaServico)>0) {
			# informaçoes do serviço
			$nome=resultadoSQL($consultaServico, 0, 'nome');
			$descricao=resultadoSQL($consultaServico, 0, 'descricao');
			$idTipoCobranca=resultadoSQL($consultaServico, 0, 'idTipoCobranca');
			$valor=resultadoSQL($consultaServico, 0, 'valor');
			$statusServico=formSelectStatusServico($matriz[idStatus], 'idStatus','check');
			
			if(!$matriz[valor]) $matriz[valor]=formatarValoresForm($valor);
			
			
			
			# Buscar informações sobre o serviço 
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Valor do Serviço:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL($valor, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Tipo de Cobrança:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL(formSelectTipoCobranca($idTipoCobranca, '','check'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			
			# Verificar se plano é especial - Caso SIM - pedir valor especial do serviço
			if(checkPlanoEspecial($matriz[idPlano])) {
				itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
				novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('100%', 'center', $corFundo, 2, 'tabfundo1');
					novaTabela2("[Plano Especial]", "center", '600', 0, 2, 1, $corFundo, $corBorda, 2);
						itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL('<b>Valor especial do Serviço:</b>', 'right', 'middle', '50%', $corFundo, 0, 'tabfundo1');
							itemLinhaTMNOURL($matriz[valor], 'left', 'middle', '50%', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
					fechaTabela();
				htmlFechaColuna();
				fechaLinhaTabela();
				itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
			}
			
			verDatasServicoPlano($matriz);
			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Data de Cancelamento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$indice=8;
				$dataNormal=formatarData(substr($data[dataNormal],0,10));
				$texto="<input name=matriz[dtCancelamento] size=10 value='$dataNormal' onBlur=verificaData(this.value,$indice)>";
				itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Status do Serviço:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL(formSelectStatusServicoAtivos('C','idStatus','form'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL("<input type=submit name=matriz[bntCancelar] value='Cancelar' class=submit>", 'center', 'middle', '70%', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
		}
		else {
			# Mensagem de aviso
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('100%', 'center', $corFundo, 2, 'tabfundo1');
					$msg="Não foi possível localizar o serviço selecionado!<br>";
					$msg.="Consulte a listagem de serviços disponíveis e tente novamente!";
					$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$registro";
					avisoNOURL("Aviso", $msg, '400');
				htmlFechaColuna();
			fechaLinhaTabela();
		}
		
		
	}

	fechaTabela();

}



# formulário de dados cadastrais
function formInativarServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	# Pessoa física
	novaTabela2("[Serviços do Plano - Inativação]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	# Opcoes Adicionais
	menuOpcAdicional($modulo, $sub, $acao, $matriz[idPlano]);
	novaLinhaTabela($corFundo, '100%');
		$texto="			
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=registro value=$registro>
			<input type=hidden name=matriz[id] value=$registro>
			<input type=hidden name=matriz[idPlano] value=$matriz[idPlano]>
			<input type=hidden name=matriz[idPessoaTipo] value=$matriz[idPessoaTipo]>
			<input type=hidden name=acao value=$acao>&nbsp;";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
	fechaLinhaTabela();

	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL('<b>Serviço:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		itemLinhaForm(formSelectServicos($matriz[idServico], 'idServico','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
	
	if($matriz[idServico]) {
		# Selecionar Serviços
		$consultaServico=buscaServicos($matriz[idServico], 'id','igual','id');
		if($consultaServico && contaConsulta($consultaServico)>0) {
			# informaçoes do serviço
			$nome=resultadoSQL($consultaServico, 0, 'nome');
			$descricao=resultadoSQL($consultaServico, 0, 'descricao');
			$idTipoCobranca=resultadoSQL($consultaServico, 0, 'idTipoCobranca');
			$valor=resultadoSQL($consultaServico, 0, 'valor');
			$statusServico=formSelectStatusServico($matriz[idStatus], 'idStatus','check');
			
			if(!$matriz[valor]) $matriz[valor]=formatarValoresForm($valor);
			
			# Buscar informações sobre o serviço 
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Valor do Serviço:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL($valor, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Tipo de Cobrança:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL(formSelectTipoCobranca($idTipoCobranca, '','check'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			
			# Verificar se plano é especial - Caso SIM - pedir valor especial do serviço
			/*
			if(checkPlanoEspecial($matriz[idPlano])) {
				itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
				novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('100%', 'center', $corFundo, 2, 'tabfundo1');
					novaTabela2("[Plano Especial]", "center", '600', 0, 2, 1, $corFundo, $corBorda, 2);
						novaLinhaTabela($corFundo, '100%');
							$texto="<span class=txtaviso>ATENÇÃO:
								Planos especiais requerem os valores especiais de cada serviço! <br>
								Informe o valor especial para este serviço!</span><br>
								<br>
								<span class=txtaviso>(Formato: ".formatarValores($valor)." => $valor) - (Valor do Serviço: R$ $valor)</span>";
								itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
							fechaLinhaTabela();
						fechaLinhaTabela();
						itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL('<b>Desconto:</b>', 'right', 'middle', '50%', $corFundo, 0, 'tabfundo1');
							$valorCompara=formatarValores($valor);
							itemLinhaTMNOURL(formSelectDesconto($matriz[desconto], 'desconto','form', $valor, 9), 'left', 'middle', '50%', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL('<b>Valor especial do Serviço:</b>', 'right', 'middle', '50%', $corFundo, 0, 'tabfundo1');
							$texto="<input type=text name=matriz[valor] value='$matriz[valor]' size=10 onBlur=verificarValor($valorCompara,this.value);formataValor(this.value,9)>";
							itemLinhaTMNOURL($texto, 'left', 'middle', '50%', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
					fechaTabela();
				htmlFechaColuna();
				fechaLinhaTabela();
				itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
			}
			*/
			verDatasServicoPlano($matriz);
			
			
			# Informar Status do Serviço
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Dias de Trial:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL($matriz[diasTrial], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Status do Serviço:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL(formSelectStatusServicoAtivos('I','idStatus','form'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL("<input type=submit name=matriz[bntInativar] value='Inativar' class=submit>", 'center', 'middle', '70%', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			
		}
		else {
			# Mensagem de aviso
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('100%', 'center', $corFundo, 2, 'tabfundo1');
					$msg="Não foi possível localizar o serviço selecionado!<br>";
					$msg.="Consulte a listagem de serviços disponíveis e tente novamente!";
					$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$registro";
					avisoNOURL("Aviso", $msg, '400');
				htmlFechaColuna();
			fechaLinhaTabela();
		}
		
		
	}

	fechaTabela();

}




# formulário de dados cadastrais
function formAtivarServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	# Pessoa física
	novaTabela2("[Serviços do Plano - Ativação]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	# Opcoes Adicionais
	menuOpcAdicional($modulo, $sub, $acao, $matriz[idPlano]);
	novaLinhaTabela($corFundo, '100%');
		$texto="			
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=registro value=$registro>
			<input type=hidden name=matriz[id] value=$registro>
			<input type=hidden name=matriz[idPlano] value=$matriz[idPlano]>
			<input type=hidden name=matriz[idPessoaTipo] value=$matriz[idPessoaTipo]>
			<input type=hidden name=acao value=$acao>&nbsp;";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
	fechaLinhaTabela();

	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL('<b>Serviço:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		itemLinhaForm(formSelectServicos($matriz[idServico], 'idServico','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
	
	if($matriz[idServico]) {
		# Selecionar Serviços
		$consultaServico=buscaServicos($matriz[idServico], 'id','igual','id');
		if($consultaServico && contaConsulta($consultaServico)>0) {
			# informaçoes do serviço
			$nome=resultadoSQL($consultaServico, 0, 'nome');
			$descricao=resultadoSQL($consultaServico, 0, 'descricao');
			$idTipoCobranca=resultadoSQL($consultaServico, 0, 'idTipoCobranca');
			$valor=resultadoSQL($consultaServico, 0, 'valor');
			$statusServico=formSelectStatusServico($matriz[idStatus], 'idStatus','check');
			
			if(!$matriz[valor]) $matriz[valor]=formatarValoresForm($valor);
			
			# Buscar informações sobre o serviço 
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Valor do Serviço:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL($valor, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Tipo de Cobrança:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL(formSelectTipoCobranca($idTipoCobranca, '','check'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			
			# Verificar se plano é especial - Caso SIM - pedir valor especial do serviço
			if(checkPlanoEspecial($matriz[idPlano])) {
				itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
				novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('100%', 'center', $corFundo, 2, 'tabfundo1');
					novaTabela2("[Plano Especial]", "center", '600', 0, 2, 1, $corFundo, $corBorda, 2);
						novaLinhaTabela($corFundo, '100%');
							$texto="<span class=txtaviso>ATENÇÃO:
								Planos especiais requerem os valores especiais de cada serviço! <br>
								Informe o valor especial para este serviço!</span><br>
								<br>
								<span class=txtaviso>(Formato: ".formatarValores($valor)." => $valor) - (Valor do Serviço: R$ $valor)</span>";
								itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
							fechaLinhaTabela();
						fechaLinhaTabela();
						itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL('<b>Desconto:</b>', 'right', 'middle', '50%', $corFundo, 0, 'tabfundo1');
							$valorCompara=formatarValores($valor);
							itemLinhaTMNOURL(formSelectDesconto($matriz[desconto], 'desconto','form', $valor, 7), 'left', 'middle', '50%', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL('<b>Valor especial do Serviço:</b>', 'right', 'middle', '50%', $corFundo, 0, 'tabfundo1');
							$texto="<input type=text name=matriz[valor] value='$matriz[valor]' size=10 onBlur=verificarValor($valorCompara,this.value);formataValor(this.value,8)>";
							itemLinhaTMNOURL($texto, 'left', 'middle', '50%', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
					fechaTabela();
				htmlFechaColuna();
				fechaLinhaTabela();
				itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
				
				$indice=9;
			}
			else {
				$indice=7;
			}
			
			verDatasServicoPlano($matriz);
			
			# data de Ativação
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Data de Ativação:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$dataNormal=formatarData(substr($data[dataNormal],0,10));
				$texto="<input name=matriz[dtAtivacao] size=10 value='$dataNormal' onBlur=verificaData(this.value,$indice)>";
				itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();			
			
			# Dias Trial
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Dias de Trial:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL($matriz[diasTrial], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Status do Serviço:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL(formSelectStatusServicoAtivos('A','idStatus','form'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL("<input type=submit name=matriz[bntAtivar] value='Ativar' class=submit>", 'center', 'middle', '70%', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			
		}
		else {
			# Mensagem de aviso
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('100%', 'center', $corFundo, 2, 'tabfundo1');
					$msg="Não foi possível localizar o serviço selecionado!<br>";
					$msg.="Consulte a listagem de serviços disponíveis e tente novamente!";
					$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$registro";
					avisoNOURL("Aviso", $msg, '400');
				htmlFechaColuna();
			fechaLinhaTabela();
		}
		
		
	}

	fechaTabela();

}



# Formulário de seleção de Tipo de Endereço
function formSelectServicoPlano($idPlano, $idServico, $campo, $tipo) {

	global $matriz;

	# Checar os endereços que a pessoa já possui
	$consultaServicosPlano=buscaServicosPlanos($idPlano, 'idPlano','igual','idPlano');
	
	if($consultaServicosPlano) {
	
		$qtde=contaConsulta($consultaServicosPlano);
	
		$consulta=buscaServicos('', 'valor','todos','nome');
		
		if($consulta && contaConsulta($consulta)>0) {
		
			$retorno="<select name=matriz[$campo] onChange=javascript:form.submit()>\n";
			$linha=0;
			for($a=0;$a<contaConsulta($consulta);$a++) {
			
				$tmpID=resultadoSQL($consulta, $a, 'id');
				$tmpNome=resultadoSQL($consulta, $a, 'nome');
				
				if($tipo=='adicionar') {
					$flagCadastrado=0;
					for($b=0;$b<contaConsulta($consultaServicosPlano);$b++) {

						$tmpIDServico=resultadoSQL($consultaServicosPlano, $b, 'idServico');
						
						if($tmpIDServico==$tmpID && $tmpID != $idServico) {
							$flagCadastrado=1;
							break;
						}
					}
					
					if(!$flagCadastrado) {
						if($tmpID==$idServico) $opcSelect='selected';
						else $opcSelect='';
						
						$retorno.="<option value=$tmpID $opcSelect>$tmpNome\n";
						$linha++;
					}
				}
				else {
					if($tmpID==$idServico) $opcSelect='selected';
					else $opcSelect='';
					
					$retorno.="<option value=$tmpID $opcSelect>$tmpNome";
					
					if($qtde==0) $linha++;
				}
				
			}
			
			$retorno.="</select>";

		}
	}


	if($tipo=='adicionar' && $linha)  return($retorno);
	elseif($tipo=='alterar')  return($retorno);
	else return(false);
}





# Formulário de seleção de Tipo de Endereço
function formSelectServicoPlanoDial($idPessoasTipos, $idServicosPlanos, $campo, $tipo) {

	global $conn, $tb;

	if($tipo=='form') {
		# Checar os endereços que a pessoa já possui
		$sql="
			SELECT 
				$tb[ServicosPlanos].id idServicoPlano, 
				$tb[Parametros].id idParametro, 
				$tb[Modulos].modulo modulo, 
				$tb[Parametros].parametro parametro,  
				$tb[ServicosParametros].valor valor,
				$tb[PlanosPessoas].nome nomePlano,
				$tb[Servicos].nome nomeServico
			FROM 
				$tb[StatusServicos], 
				$tb[Parametros], 
				$tb[Modulos], 
				$tb[ParametrosModulos], 
				$tb[Servicos], 
				$tb[ServicosParametros],
				$tb[ServicosPlanos], 
				$tb[PlanosPessoas], 
				$tb[PessoasTipos] 
			WHERE 
				$tb[PlanosPessoas].idPessoaTipo = $tb[PessoasTipos].id 
				AND $tb[ServicosPlanos].idPlano = $tb[PlanosPessoas].id 
				AND $tb[Servicos].id = $tb[ServicosPlanos].idServico 
				AND $tb[Servicos].id = $tb[ServicosParametros].idServico 
				AND $tb[ParametrosModulos].idModulo = $tb[Modulos].id 
				AND $tb[ServicosParametros].idServico = $tb[Servicos].id 
				AND $tb[ServicosParametros].idParametro = $tb[Parametros].id 
				AND $tb[StatusServicos].id = $tb[ServicosPlanos].idStatus 
				AND $tb[ParametrosModulos].idParametro = $tb[Parametros].id 
				AND $tb[ParametrosModulos].idModulo = $tb[Modulos].id
				AND ($tb[StatusServicos].status = 'A' OR $tb[StatusServicos].status = 'T' OR $tb[StatusServicos].status = 'N')
				AND $tb[PlanosPessoas].idPessoaTipo=$idPessoasTipos
				AND $tb[Modulos].modulo = 'dial' 
				AND $tb[Parametros].parametro = 'qtde' 
		";
		
		$consulta=consultaSQL($sql, $conn);
	
		if($consulta && contaConsulta($consulta)>0) {
	
			$retorno="<select name=matriz[$campo]>\n";
			$linha=0;
			for($a=0;$a<contaConsulta($consulta);$a++) {
			
				$idServicoPlano=resultadoSQL($consulta, $a, 'idServicoPlano');
				$idParametro=resultadoSQL($consulta, $a, 'idParametro');
				$modulo=resultadoSQL($consulta, $a, 'modulo');
				$parametro=resultadoSQL($consulta, $a, 'parametro');
				$valor=resultadoSQL($consulta, $a, 'valor');
				$nomePlano=resultadoSQL($consulta, $a, 'nomePlano');
				$nomeServico=resultadoSQL($consulta, $a, 'nomeServico');
				
				if($idServicosPlanos==$idServicoPlano) $opcSelect='selected';
				else $opcSelect='';
				
				# Contabilizar total e contas configuradas para este serviço
				$totalContasServico=radiusTotalContasServico($idPessoasTipos, $idServicoPlano);
				$totalContasServicoEmUso=radiusTotalContasServicoEmUso($idPessoasTipos, $idServicoPlano);
				
				if($totalContasServico > $totalContasServicoEmUso) $retorno.="<option value=$idServicoPlano $opcSelect>Plano: $nomePlano - $nomeServico";
			}
			
			$retorno.="</select>";
		}
	}
	elseif($tipo=='check') {
	
		# Checar os endereços que a pessoa já possui
		$sql="
			SELECT 
				$tb[ServicosPlanos].id idServicoPlano, 
				$tb[Parametros].id idParametro, 
				$tb[Modulos].modulo modulo, 
				$tb[Parametros].parametro parametro,  
				$tb[ServicosParametros].valor valor,
				$tb[PlanosPessoas].nome nomePlano,
				$tb[Servicos].nome nomeServico
			FROM 
				$tb[StatusServicos], 
				$tb[Parametros], 
				$tb[Modulos], 
				$tb[ParametrosModulos], 
				$tb[Servicos], 
				$tb[ServicosParametros],
				$tb[ServicosPlanos], 
				$tb[PlanosPessoas], 
				$tb[PessoasTipos] 
			WHERE 
				$tb[PlanosPessoas].idPessoaTipo = $tb[PessoasTipos].id 
				AND $tb[ServicosPlanos].idPlano = $tb[PlanosPessoas].id 
				AND $tb[Servicos].id = $tb[ServicosPlanos].idServico 
				AND $tb[Servicos].id = $tb[ServicosParametros].idServico 
				AND $tb[ParametrosModulos].idModulo = $tb[Modulos].id 
				AND $tb[ServicosParametros].idServico = $tb[Servicos].id 
				AND $tb[ServicosParametros].idParametro = $tb[Parametros].id 
				AND $tb[StatusServicos].id = $tb[ServicosPlanos].idStatus 
				AND $tb[ParametrosModulos].idParametro = $tb[Parametros].id 
				AND $tb[ParametrosModulos].idModulo = $tb[Modulos].id
				AND $tb[PlanosPessoas].idPessoaTipo=$idPessoasTipos
				AND $tb[ServicosPlanos].id=$idServicosPlanos
				AND $tb[Modulos].modulo = 'dial' 
				AND $tb[Parametros].parametro = 'qtde' 
		";
		
		$consulta=consultaSQL($sql, $conn);
	
		if($consulta && contaConsulta($consulta)>0) {
			$nomePlano=resultadoSQL($consulta, 0, 'nomePlano');
			$nomeServico=resultadoSQL($consulta, 0, 'nomeServico');
			
			$retorno="Plano: $nomePlano - $nomeServico";
		}

	}
	
	
	return($retorno);
}

# Formulário de seleção de Tipo de Endereço
function formSelectServicoPlanoDominio($idPessoasTipos, $idServicosPlanos, $campo, $tipo) {


	global $conn, $tb;

	if($tipo=='form') {
		# Checar os endereços que a pessoa já possui
		$sql="
			SELECT 
				$tb[ServicosPlanos].id idServicoPlano, 
				$tb[Parametros].id idParametro, 
				$tb[Modulos].modulo modulo, 
				$tb[Parametros].parametro parametro,  
				$tb[ServicosParametros].valor valor,
				$tb[PlanosPessoas].nome nomePlano,
				$tb[Servicos].nome nomeServico
			FROM 
				$tb[StatusServicos], 
				$tb[Parametros], 
				$tb[Modulos], 
				$tb[ParametrosModulos], 
				$tb[Servicos], 
				$tb[ServicosParametros],
				$tb[ServicosPlanos], 
				$tb[PlanosPessoas], 
				$tb[PessoasTipos] 
			WHERE 
				$tb[PlanosPessoas].idPessoaTipo = $tb[PessoasTipos].id 
				AND $tb[PlanosPessoas].id = $tb[ServicosPlanos].idPlano
				AND $tb[ServicosPlanos].idServico=$tb[Servicos].id
				AND $tb[Servicos].id = $tb[ServicosParametros].idServico 
				AND $tb[ServicosParametros].idParametro = $tb[Parametros].id 
				AND $tb[Parametros].id=$tb[ParametrosModulos].idParametro
				AND $tb[ParametrosModulos].idModulo=$tb[Modulos].id
				AND $tb[StatusServicos].id = $tb[ServicosPlanos].idStatus 
				AND ($tb[StatusServicos].status = 'A' OR $tb[StatusServicos].status = 'T' OR $tb[StatusServicos].status = 'N')
				AND $tb[PlanosPessoas].idPessoaTipo=$idPessoasTipos
				AND $tb[Modulos].modulo = 'dominio' 
			GROUP BY
				$tb[ServicosPlanos].id
		";
		
		$consulta=consultaSQL($sql, $conn);
	
		if($consulta && contaConsulta($consulta)>0) {
	
			$retorno="<select name=matriz[$campo] class=normal8>\n";
			$linha=0;
			for($a=0;$a<contaConsulta($consulta);$a++) {
			
				$idServicoPlano=resultadoSQL($consulta, $a, 'idServicoPlano');
				$idParametro=resultadoSQL($consulta, $a, 'idParametro');
				$modulo=resultadoSQL($consulta, $a, 'modulo');
				$parametro=resultadoSQL($consulta, $a, 'parametro');
				$valor=resultadoSQL($consulta, $a, 'valor');
				$nomePlano=resultadoSQL($consulta, $a, 'nomePlano');
				$nomeServico=resultadoSQL($consulta, $a, 'nomeServico');
				
				if($idServicosPlanos==$idServicoPlano) $opcSelect='selected';
				else $opcSelect='';
				
				# Contabilizar total e contas configuradas para este serviço
				$totalContasServico=dominioTotalContasServico($idPessoasTipos, $idServicoPlano);
				$totalContasServicoEmUso=dominioTotalContasServicoEmUso($idPessoasTipos, $idServicoPlano);
				
				if($totalContasServico > $totalContasServicoEmUso) $retorno.="<option value=$idServicoPlano $opcSelect>Plano: $nomePlano - $nomeServico";
			}
			
			$retorno.="</select>";
		}
	}
	elseif($tipo=='check') {
	
		# Checar os endereços que a pessoa já possui
		$sql="
			SELECT 
				$tb[ServicosPlanos].id idServicoPlano, 
				$tb[Parametros].id idParametro, 
				$tb[Modulos].modulo modulo, 
				$tb[Parametros].parametro parametro,  
				$tb[ServicosParametros].valor valor,
				$tb[PlanosPessoas].nome nomePlano,
				$tb[Servicos].nome nomeServico
			FROM 
				$tb[StatusServicos], 
				$tb[Parametros], 
				$tb[Modulos], 
				$tb[ParametrosModulos], 
				$tb[Servicos], 
				$tb[ServicosParametros],
				$tb[ServicosPlanos], 
				$tb[PlanosPessoas], 
				$tb[PessoasTipos] 
			WHERE 
				$tb[PlanosPessoas].idPessoaTipo = $tb[PessoasTipos].id 
				AND $tb[PlanosPessoas].id = $tb[ServicosPlanos].idPlano
				AND $tb[ServicosPlanos].idServico=$tb[Servicos].id
				AND $tb[Servicos].id = $tb[ServicosParametros].idServico 
				AND $tb[ServicosParametros].idParametro = $tb[Parametros].id 
				AND $tb[Parametros].id=$tb[ParametrosModulos].idParametro
				AND $tb[ParametrosModulos].idModulo=$tb[Modulos].id
				AND $tb[StatusServicos].id = $tb[ServicosPlanos].idStatus 
				AND ($tb[StatusServicos].status = 'A' OR $tb[StatusServicos].status = 'T' OR $tb[StatusServicos].status = 'N')
				AND $tb[PlanosPessoas].idPessoaTipo=$idPessoasTipos
				AND $tb[Modulos].modulo = 'dominio' 
		";
		
		$consulta=consultaSQL($sql, $conn);
	
		if($consulta && contaConsulta($consulta)>0) {
			$nomePlano=resultadoSQL($consulta, 0, 'nomePlano');
			$nomeServico=resultadoSQL($consulta, 0, 'nomeServico');
			
			$retorno="Plano: $nomePlano - $nomeServico";
		}

	}
	
	
	return($retorno);
}


function formSelectServicoPlanoMaquina($idPessoasTipos, $idServicosPlanos, $campo, $tipo) {


	global $conn, $tb;
	if($tipo=='form') {
		# Checar os endereços que a pessoa já possui
		$sql="
			SELECT 
				$tb[ServicosPlanos].id idServicoPlano, 
				$tb[Parametros].id idParametro, 
				$tb[Modulos].modulo modulo, 
				$tb[Parametros].parametro parametro,  
				$tb[ServicosParametros].valor valor,
				$tb[PlanosPessoas].nome nomePlano,
				$tb[Servicos].nome nomeServico
			FROM 
				$tb[StatusServicos], 
				$tb[Parametros], 
				$tb[Modulos], 
				$tb[ParametrosModulos], 
				$tb[Servicos], 
				$tb[ServicosParametros],
				$tb[ServicosPlanos], 
				$tb[PlanosPessoas], 
				$tb[PessoasTipos] 
			WHERE 
				$tb[PlanosPessoas].idPessoaTipo = $tb[PessoasTipos].id 
				AND $tb[PlanosPessoas].id = $tb[ServicosPlanos].idPlano
				AND $tb[ServicosPlanos].idServico=$tb[Servicos].id
				AND $tb[Servicos].id = $tb[ServicosParametros].idServico 
				AND $tb[ServicosParametros].idParametro = $tb[Parametros].id 
				AND $tb[Parametros].id=$tb[ParametrosModulos].idParametro
				AND $tb[ParametrosModulos].idModulo=$tb[Modulos].id
				AND $tb[StatusServicos].id = $tb[ServicosPlanos].idStatus
				AND ($tb[StatusServicos].status = 'A' OR $tb[StatusServicos].status = 'T' OR $tb[StatusServicos].status = 'N')
				AND $tb[PlanosPessoas].idPessoaTipo=$idPessoasTipos
				AND $tb[Modulos].modulo = 'maquinas' 
			GROUP BY
				$tb[ServicosPlanos].id
		";
		
		$consulta=consultaSQL($sql, $conn);
	
		if($consulta && contaConsulta($consulta)>0) {
	
			$retorno="<select name=matriz[$campo] class=normal8>\n";
			$linha=0;
			for($a=0;$a<contaConsulta($consulta);$a++) {
			
				$idServicoPlano=resultadoSQL($consulta, $a, 'idServicoPlano');
				$idParametro=resultadoSQL($consulta, $a, 'idParametro');
				$modulo=resultadoSQL($consulta, $a, 'modulo');
				$parametro=resultadoSQL($consulta, $a, 'parametro');
				$valor=resultadoSQL($consulta, $a, 'valor');
				$nomePlano=resultadoSQL($consulta, $a, 'nomePlano');
				$nomeServico=resultadoSQL($consulta, $a, 'nomeServico');
				
				if($idServicosPlanos==$idServicoPlano) $opcSelect='selected';
				else $opcSelect='';
				
				# Contabilizar total e contas configuradas para este serviço
				$totalContasServico=maquinaTotalContasServico($idPessoasTipos, $idServicoPlano);
				$totalContasServicoEmUso=maquinaTotalContasServicoEmUso($idPessoasTipos, $idServicoPlano);
				
				if($totalContasServico > $totalContasServicoEmUso) $retorno.="<option value=$idServicoPlano $opcSelect>Plano: $nomePlano - $nomeServico";
			}
			
			$retorno.="</select>";
		}
	}
	elseif($tipo=='check') {
	
		# Checar os endereços que a pessoa já possui
		$sql="
			SELECT 
				$tb[ServicosPlanos].id idServicoPlano, 
				$tb[Parametros].id idParametro, 
				$tb[Modulos].modulo modulo, 
				$tb[Parametros].parametro parametro,  
				$tb[ServicosParametros].valor valor,
				$tb[PlanosPessoas].nome nomePlano,
				$tb[Servicos].nome nomeServico
			FROM 
				$tb[StatusServicos], 
				$tb[Parametros], 
				$tb[Modulos], 
				$tb[ParametrosModulos], 
				$tb[Servicos], 
				$tb[ServicosParametros],
				$tb[ServicosPlanos], 
				$tb[PlanosPessoas], 
				$tb[PessoasTipos] 
			WHERE 
				$tb[PlanosPessoas].idPessoaTipo = $tb[PessoasTipos].id 
				AND $tb[PlanosPessoas].id = $tb[ServicosPlanos].idPlano
				AND $tb[ServicosPlanos].idServico=$tb[Servicos].id
				AND $tb[Servicos].id = $tb[ServicosParametros].idServico 
				AND $tb[ServicosParametros].idParametro = $tb[Parametros].id 
				AND $tb[Parametros].id=$tb[ParametrosModulos].idParametro
				AND $tb[ParametrosModulos].idModulo=$tb[Modulos].id
				AND $tb[StatusServicos].id = $tb[ServicosPlanos].idStatus 
				AND ($tb[StatusServicos].status = 'A' OR $tb[StatusServicos].status = 'T' OR $tb[StatusServicos].status = 'N')
				AND $tb[PlanosPessoas].idPessoaTipo=$idPessoasTipos
				AND $tb[Modulos].modulo = 'maquinas' 
		";
		
		$consulta=consultaSQL($sql, $conn);
	
		if($consulta && contaConsulta($consulta)>0) {
			$nomePlano=resultadoSQL($consulta, 0, 'nomePlano');
			$nomeServico=resultadoSQL($consulta, 0, 'nomeServico');
			
			$retorno="Plano: $nomePlano - $nomeServico";
		}

	}
	
	
	return($retorno);
}

# função para adicionar pessoa
function alterarServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $sessLogin;
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');

	if(!$permissao[alterar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função.";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		
		if(!$matriz[bntConfirmar]) {
			
			# Recebe ID do Serviço do plano
			$consulta=buscaServicosPlanos($registro, 'id','igual','id');
		
			# prosseguir e mostarar pessoa e plano
			$idPlano=resultadoSQL($consulta, 0, 'idPlano');
	
			if($consulta && contaConsulta($consulta)>0) {
	
				# Dados para formulário
				$matriz[idPlano]=$idPlano;
				$matriz[idServico]=resultadoSQL($consulta, 0, 'idServico');
				$matriz[valor]=formatarValoresForm(resultadoSQL($consulta, 0, 'valor'));
				$matriz[dtCadastro]=resultadoSQL($consulta, 0, 'dtCadastro');
				$matriz[dtAtivacao]=resultadoSQL($consulta, 0, 'dtAtivacao');
				$matriz[dtCancelamento]=resultadoSQL($consulta, 0, 'dtCancelamento');
				$matriz[idStatus]=resultadoSQL($consulta, 0, 'idStatus');
				$matriz[diasTrial]=resultadoSQL($consulta, 0, 'diasTrial');
				
				# Buscar ID da Pessoa
				$consultaPlano=buscaPlanos($idPlano, 'id','igual','id');
				
				if($consultaPlano && contaConsulta($consultaPlano)>0) {
				
					$matriz[idPessoaTipo]=resultadoSQL($consultaPlano, 0, 'idPessoaTipo');
				
					# Ver dados da pessoa
					if (!$matriz[modanterior]){
						verPessoas('cadastros', 'clientes', 'ver', $matriz[idPessoaTipo], $matriz);
						echo "<br>";
					}
					
					# Ver dados do Plano
					verPlanos($modulo, $sub, $acao, $idPlano, $matriz);
					echo "<br>";
					
					
					formAlterarServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
					
				}
				else {
					# Erro
					$msg="ERRO ao selecionar o Plano do Cliente!";
					$url="?modulo=cadastros&sub=clientes";
					aviso("Aviso", $msg, $url, 400);
				}
			}
			else {
				# Erro
				$msg="ERRO ao selecionar o Serviço!";
				$url="?modulo=cadastros&sub=clientes";
				aviso("Aviso", $msg, $url, 400);	
			}
		}
		else {
			if(!$matriz[dtCadastro]) $matriz[dtCadastro]=$data[dataBanco];
			else {
				$matriz[dtCadastro]=formatarData($matriz[dtCadastro]);
				$matriz[dtCadastro]=substr($matriz[dtCadastro],0,2).'/'.substr($matriz[dtCadastro],2,2).'/'.substr($matriz[dtCadastro],4,4);
				$matriz[dtCadastro]=converteData($matriz[dtCadastro],'form','bancodata');
			}
	
			if($matriz[dtAtivacao]) {
				$matriz[dtAtivacao]=formatarData($matriz[dtAtivacao]);
				$matriz[dtAtivacao]=substr($matriz[dtAtivacao],0,2).'/'.substr($matriz[dtAtivacao],2,2).'/'.substr($matriz[dtAtivacao],4,4);
				$matriz[dtAtivacao]=converteData($matriz[dtAtivacao],'form','bancodata');
			}
			else {
				# consultar se serviço gera cobrança (S=setar data de ativação)
				$statusCobranca=formSelectStatusServico($matriz[idStatus], '','check');
				if($statusCobranca[cobranca]=='S') {
					$matriz[dtAtivacao]=$matriz[dtCadastro];
				}
			}
		
			# Gravar registro
			$matriz[valor]=formatarValores($matriz[valor]);
			
			$data=dataSistema();
			
			# Checar Status
			/*
			if($matriz[idStatusANT] != $matriz[idStatus]) {
				# checar tipo de status
				# consultar se serviço gera cobrança (S=setar data de ativação)
				$statusCobrancaANT=checkStatusServico($matriz[idStatusANT]);
				$statusCobranca=checkStatusServico($matriz[idStatus]);
				
				if($statusCobrancaANT[cobranca]!='S' && $statusCobranca[cobranca]=='S' && $statusCobranca[status]=='A') {
					$matriz[dtAtivacao]=$data[dataBanco];
				}
				else {
					# Checa se status = cancelado
					if($statusCobranca[status]=='C') {
						# Gravar data de Cancelamento
						$matriz[dtCancelamento]=$data[dataBanco];
					}
				}
			}
			*/
			
			$grava=dbServicosPlano($matriz, 'alterar');
			
			if($grava) {
				# OK
				$msg="Serviço alterado com sucesso!";
				$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
				avisoNOURL("Aviso", $msg, 400);
				echo "<br>";
				
				# Ver dados da pessoa
				verPessoas('cadastros', 'clientes', 'ver', $matriz[idPessoaTipo], $matriz);
				echo "<br>";
				
				# Ver dados do Plano
				verPlanos($modulo, $sub, $acao, $matriz[idPlano], $matriz);
				
				listarServicosPlanos($modulo, $sub, 'abrir', $matriz[idPlano], $matriz);
			}
			else {
				# Erro
				$msg="ERRO ao alterar serviço! Tente novamente!";
				$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$matriz[idPlano]";
				avisoNOURL("Aviso", $msg, 400);
			}
		}

	}//permissoes
}



# função cancelar Serviços de Planos de Pessoas
function cancelarServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $sessLogin;
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[excluir]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função.";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {	
		
		if(!$matriz[bntCancelar]) {
			# Recebe ID do Serviço do plano
			$consulta=buscaServicosPlanos($registro, 'id','igual','id');
		
			# prosseguir e mostarar pessoa e plano
			$idPlano=resultadoSQL($consulta, 0, 'idPlano');
	
			if($consulta && contaConsulta($consulta)>0) {
	
				# Dados para formulário
				$matriz[idPlano]=$idPlano;
				$matriz[idServico]=resultadoSQL($consulta, 0, 'idServico');
				$matriz[valor]=formatarValoresForm(resultadoSQL($consulta, 0, 'valor'));
				$matriz[dtCadastro]=resultadoSQL($consulta, 0, 'dtCadastro');
				$matriz[dtAtivacao]=resultadoSQL($consulta, 0, 'dtAtivacao');
				$matriz[idStatus]=resultadoSQL($consulta, 0, 'idStatus');
				$matriz[diasTrial]=resultadoSQL($consulta, 0, 'diasTrial');
				
				# Buscar ID da Pessoa
				$consultaPlano=buscaPlanos($idPlano, 'id','igual','id');
				
				if($consultaPlano && contaConsulta($consultaPlano)>0) {
				
					$matriz[idPessoaTipo]=resultadoSQL($consultaPlano, 0, 'idPessoaTipo');
				
					# Ver dados da pessoa
					verPessoas('cadastros', 'clientes', 'ver', $matriz[idPessoaTipo], $matriz);
					echo "<br>";
					
					# Ver dados do Plano
					verPlanos($modulo, $sub, $acao, $idPlano, $matriz);
					echo "<br>";
					
					
					formCancelarServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
					
				}
				else {
					# Erro
					$msg="ERRO ao selecionar o Plano do Cliente!";
					$url="?modulo=cadastros&sub=clientes";
					aviso("Aviso", $msg, $url, 760);
				}
			}
			else {
				# Erro
				$msg="ERRO ao selecionar o Serviço!";
				$url="?modulo=cadastros&sub=clientes";
				aviso("Aviso", $msg, $url, 760);	
			}
		}
		else {
			# Gravar registro
			$matriz[valor]=formatarValores($matriz[valor]);
			$data = dataSistema();
			$matriz[dtCancelamento]=converteData($matriz[dtCancelamento],'form','banco');
				
			#busca status atual do servicoPlano.
			$dadosServicosPlanos = dadosServicoPlano($matriz['id']);
			$dadosStatus = checkStatusServico($dadosServicosPlanos['idStatus']);
			
			#gera servico adicional somente se o status do ticket estiver com cobranca = S
			if ( $dadosStatus['cobranca'] == 'S'){
				# Adicionar Serviço adicional - caso serviço gere cobrança
				$gravaServicoAdicional = adicionarServicoAdicionalServicoPlano($matriz, 'ver');					
			}

		
			##fim da alteracao do ticket.
				
			# Cancelar serviço
			$grava = dbServicosPlano($matriz, 'cancelar');
	
			# Excluir conta radius
			$gravaRadius = radiusExcluirContaServico($registro);
			
			# Excluir configurações de dominio
//			$gravaDominios=dominiosExcluirServico($registro);
			$gravaDominios=dominiosCancelamentoServico($registro);
			
			# Verificar se ainda há serviços ativos
			if($grava) {
				# OK
				$msg="Serviço cancelado com sucesso!";
				$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
				avisoNOURL("Aviso", $msg, 600);
				echo "<br>";
				
				# Ver dados da pessoa
				verPessoas('cadastros', 'clientes', 'ver', $matriz[idPessoaTipo], $matriz);
				echo "<br>";
				
				# Ver dados do Plano
				verPlanos($modulo, $sub, $acao, $matriz[idPlano], $matriz);
				
				listarServicosPlanos($modulo, $sub, 'abrir', $matriz[idPlano], $matriz);
			}
			else {
				# Erro
				$msg="ERRO ao alterar serviço! Tente novamente!";
				$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$matriz[idPlano]";
				avisoNOURL("Aviso", $msg, 400);
			}
		}

	}//permissoes	
}

/*
 * funcao criada em 20050315 - louco: cancela um servico como a funcao acima, porem sem nenhuma interacao
 * com o usario, nao exibindo o formulario e principalmente a confirmacao de exclusao uma vez que estes 
 * dados serao passados atraves da funcao que ira chama-la e que nao nao seria interessante as chamadas 
 * para exibir planos e pessoas que ela executa... 
 */
function cancelarServicosPlanosAutomatico($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $sessLogin;
	
	$consulta=buscaServicosPlanos($registro, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {

		# Dados para formulário
		$matriz[idPlano]=resultadoSQL($consulta, 0, 'idPlano');
		$matriz[valor]=formatarValoresForm(resultadoSQL($consulta, 0, 'valor'));
		$matriz[dtCadastro]=resultadoSQL($consulta, 0, 'dtCadastro');
		$matriz[dtAtivacao]=resultadoSQL($consulta, 0, 'dtAtivacao');
		$matriz[valor]=formatarValores($matriz[valor]);
		$data=dataSistema();
		$matriz[dtCancelamento]=converteData($matriz[dtCancelamento],'form','banco');
		
		$consulta = buscaStatusServicos('C', 'status', 'igual', 'descricao');
		$matriz[idStatus] = resultadoSQL($consulta, 0, "id");
		
		# Adicionar Serviço adicional - caso serviço gere cobrança
		$gravaServicoAdicional=adicionarServicoAdicionalServicoPlano($matriz, 'ver');

		# Cancelar serviço
		$grava=dbServicosPlano($matriz, 'cancelar');

		# Excluir conta radius
		$gravaRadius=radiusExcluirContaServico($registro);
		
		# Excluir configurações de dominio
		$gravaDominios=dominiosExcluirServico($registro);
		
		# Verificar se ainda há serviços ativos
		if($grava) {
			# OK
			$msg="Antigo serviço cancelado com sucesso!";
			$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
			avisoNOURL("Aviso", $msg, "100%");
			echo "<br>";
		}
		else {
			# Erro
			$msg="ERRO ao cancelar serviço! Tente novamente!";
			$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$matriz[idPlano]";
			avisoNOURL("Aviso", $msg, "100%");
		}
	}

	
}//fim da funcao introduzida em 20050315


# função para adicionar pessoa
function verServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $sessLogin;
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[visualizar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função.";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {	
	
		# Recebe ID do Serviço do plano
		$consulta=buscaServicosPlanos($registro, 'id','igual','id');
	
		# prosseguir e mostarar pessoa e plano
		$idPlano=resultadoSQL($consulta, 0, 'idPlano');
	
		if($consulta && contaConsulta($consulta)>0) {
	
			# Dados para formulário
			$matriz[idPlano]=$idPlano;
			$matriz[idServico]=resultadoSQL($consulta, 0, 'idServico');
			$matriz[valor]=formatarValoresForm(resultadoSQL($consulta, 0, 'valor'));
			$matriz[dtCadastro]=resultadoSQL($consulta, 0, 'dtCadastro');
			$matriz[dtAtivacao]=resultadoSQL($consulta, 0, 'dtAtivacao');
			$matriz[dtInativacao]=resultadoSQL($consulta, 0, 'dtInativacao');
			$matriz[dtCancelamento]=resultadoSQL($consulta, 0, 'dtCancelamento');
			$matriz[idStatus]=resultadoSQL($consulta, 0, 'idStatus');
			$matriz[diasTrial]=resultadoSQL($consulta, 0, 'diasTrial');
			
			# Buscar ID da Pessoa
			$consultaPlano=buscaPlanos($idPlano, 'id','igual','id');
			
			if($consultaPlano && contaConsulta($consultaPlano)>0) {
			
				$matriz[idPessoaTipo]=resultadoSQL($consultaPlano, 0, 'idPessoaTipo');
			
				# Ver dados da pessoa
				verPessoas('cadastros', 'clientes', 'ver', $matriz[idPessoaTipo], $matriz);
				echo "<br>";
				
				# Ver dados do Plano
				verPlanos($modulo, $sub, $acao, $idPlano, $matriz);
				echo "<br>";
				
				formVerServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
	
			}
			else {
				# Erro
				$msg="ERRO ao selecionar o Plano do Cliente!";
				$url="?modulo=cadastros&sub=clientes";
				aviso("Aviso", $msg, $url, 760);
			}
		}

	}//permissoes
}


# Visualização de dados apenas
function formVerServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	# Verificar o Status a ser utilizados
	$statusCancelado=checkStatusStatusServico('C','status','igual','status');

	# Pessoa física
	novaTabela2("[Serviços do Plano]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	# Opcoes Adicionais
	menuOpcAdicional('lancamentos', 'planos', 'verservico', "$matriz[idPlano]:$registro");
	novaLinhaTabela($corFundo, '100%');
			itemLinhaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
	fechaLinhaTabela();

// caso o servico contenha dominios, exibilos para facilitar quem esta removendo o plano
	$dominio = buscarNomesDominiosServicosPlanos($registro);

	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL('<b>Serviço:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		itemLinhaForm(formSelectServicos($matriz[idServico], 'idServico','check') . $dominio, 'left', 'top', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
	
	if($matriz[idServico]) {
		# Selecionar Serviços
		$consultaServico=buscaServicos($matriz[idServico], 'id','igual','id');
		if($consultaServico && contaConsulta($consultaServico)>0) {
			# informaçoes do serviço
			$nome=resultadoSQL($consultaServico, 0, 'nome');
			$descricao=resultadoSQL($consultaServico, 0, 'descricao');
			$idTipoCobranca=resultadoSQL($consultaServico, 0, 'idTipoCobranca');
			$valor=resultadoSQL($consultaServico, 0, 'valor');
			$statusServico=formSelectStatusServico($matriz[idStatus], 'idStatus','check');
			
			if(!$matriz[valor]) $matriz[valor]=formatarValoresForm($valor);
			
			# Buscar informações sobre o serviço 
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Valor do Serviço:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL($valor, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Tipo de Cobrança:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL(formSelectTipoCobranca($idTipoCobranca, '','check'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			
			# Verificar se plano é especial - Caso SIM - pedir valor especial do serviço
			if(checkPlanoEspecial($matriz[idPlano])) {
				itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
				novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('100%', 'center', $corFundo, 2, 'tabfundo1');
					novaTabela2("[Plano Especial]", "center", '600', 0, 2, 1, $corFundo, $corBorda, 2);
						itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL('<b>Valor especial do Serviço:</b>', 'right', 'middle', '50%', $corFundo, 0, 'tabfundo1');
							itemLinhaTMNOURL($matriz[valor], 'left', 'middle', '50%', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
					fechaTabela();
				htmlFechaColuna();
				fechaLinhaTabela();
				itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
			}
			
			verDatasServicoPlano($matriz);
			
			# Informar Status do Serviço
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Dias de Trial:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL($matriz[diasTrial], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Status do Serviço:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL($statusServico[descricao], 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();

			
		}
		else {
			# Mensagem de aviso
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('100%', 'center', $corFundo, 2, 'tabfundo1');
					$msg="Não foi possível localizar o serviço selecionado!<br>";
					$msg.="Consulte a listagem de serviços disponíveis e tente novamente!";
					$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$registro";
					avisoNOURL("Aviso", $msg, '400');
				htmlFechaColuna();
			fechaLinhaTabela();
		}
	}

	fechaTabela();

}



# função para adicionar pessoa
function inativarServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $sessLogin;
	
		# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[alterar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função.";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {	
	
		if(!$matriz[bntInativar]) {
			# Recebe ID do Serviço do plano
			$consulta=buscaServicosPlanos($registro, 'id','igual','id');
		
			# prosseguir e mostarar pessoa e plano
			$idPlano=resultadoSQL($consulta, 0, 'idPlano');
	
			if($consulta && contaConsulta($consulta)>0) {
	
				# Dados para formulário
				$matriz[idPlano]=$idPlano;
				$matriz[idServico]=resultadoSQL($consulta, 0, 'idServico');
				$matriz[valor]=formatarValoresForm(resultadoSQL($consulta, 0, 'valor'));
				$matriz[dtCadastro]=resultadoSQL($consulta, 0, 'dtCadastro');
				$matriz[dtAtivacao]=resultadoSQL($consulta, 0, 'dtAtivacao');
				$matriz[dtCancelamento]=resultadoSQL($consulta, 0, 'dtCancelamento');
				$matriz[idStatus]=resultadoSQL($consulta, 0, 'idStatus');
				$matriz[diasTrial]=resultadoSQL($consulta, 0, 'diasTrial');
				
				# Buscar ID da Pessoa
				$consultaPlano=buscaPlanos($idPlano, 'id','igual','id');
				
				if($consultaPlano && contaConsulta($consultaPlano)>0) {
				
					$matriz[idPessoaTipo]=resultadoSQL($consultaPlano, 0, 'idPessoaTipo');
				
					# Ver dados da pessoa
					verPessoas('cadastros', 'clientes', 'ver', $matriz[idPessoaTipo], $matriz);
					echo "<br>";
					
					# Ver dados do Plano
					verPlanos($modulo, $sub, $acao, $idPlano, $matriz);
					echo "<br>";
					
					
					formInativarServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
					
				}
				else {
					# Erro
					$msg="ERRO ao selecionar o Plano do Cliente!";
					$url="?modulo=cadastros&sub=clientes";
					aviso("Aviso", $msg, $url, 760);
				}
			}
			else {
				# Erro
				$msg="ERRO ao selecionar o Serviço!";
				$url="?modulo=cadastros&sub=clientes";
				aviso("Aviso", $msg, $url, 760);	
			}
		}
		else {
			# Gravar registro
			$matriz[valor]=formatarValores($matriz[valor]);
			
			$data=dataSistema();
			
			$matriz[dtInativacao]=$data[dataBanco];
			
//#			
			# Adicionar Serviço adicional - caso serviço gere cobrança
//			$matriz[dtCancelamento] = $matriz[dtInativacao];
//			@mysql_query("SA?");
			$matriz['dtCancelamento'] = $data['dataBancoGrapi'];
			$gravaServicoAdicional=adicionarServicoAdicionalServicoPlano($matriz, 'ver', 'Inativação');
//			@mysql_query("#SA?");
//#
			
			$grava=dbServicosPlano($matriz, 'inativar');
			
			if($grava) {
			
				# Verificar contas ligadas ao serviço e nativá-las
				$radiusStatus=radiusStatusContaServico($registro, 'I');
			
			/* adicionado em 20050309 por gustavo conforme requisitado pelo ticket FZ2303U
				# Se gravado, faz a inativacao dos outros servicos tb.*/
				## desativando dominio
//				$matriz[idDominio] = inativarDominio($modulo, $sub, $acao, $registro, $matriz);
				
				$msg="Serviço inativado com sucesso!";
				$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
				avisoNOURL("Aviso", $msg, 600);
				echo "<br>";
				
				# Ver dados da pessoa
				verPessoas('cadastros', 'clientes', 'ver', $matriz[idPessoaTipo], $matriz);
				echo "<br>";
				
				# Ver dados do Plano
				verPlanos($modulo, $sub, $acao, $matriz[idPlano], $matriz);
				
				listarServicosPlanos($modulo, $sub, 'abrir', $matriz[idPlano], $matriz);
			}
			else {
				# Erro
				$msg="ERRO ao alterar serviço! Tente novamente!";
				$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$matriz[idPlano]";
				avisoNOURL("Aviso", $msg, 600);
			}
		}

	}//permissoes	
}




# função para adicionar pessoa
function ativarServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro;

	# Recebe ID do Serviço do plano
	$consulta=buscaServicosPlanos($registro, 'id','igual','id');	
	if(!$matriz[bntAtivar]) {
	
		# prosseguir e mostarar pessoa e plano
		$idPlano=resultadoSQL($consulta, 0, 'idPlano');

		if($consulta && contaConsulta($consulta)>0) {

			# Dados para formulário
			$matriz[idPlano]=$idPlano;
			$matriz[idServico]=resultadoSQL($consulta, 0, 'idServico');
			$matriz[valor]=formatarValoresForm(resultadoSQL($consulta, 0, 'valor'));
			$matriz[dtCadastro]=resultadoSQL($consulta, 0, 'dtCadastro');
			$matriz[dtAtivacao]=resultadoSQL($consulta, 0, 'dtAtivacao');
			$matriz[dtCancelamento]=resultadoSQL($consulta, 0, 'dtCancelamento');
			$matriz[idStatus]=resultadoSQL($consulta, 0, 'idStatus');
			$matriz[diasTrial]=resultadoSQL($consulta, 0, 'diasTrial');
			
			# Buscar ID da Pessoa
			$consultaPlano=buscaPlanos($idPlano, 'id','igual','id');
			
			if($consultaPlano && contaConsulta($consultaPlano)>0) {
			
				$matriz[idPessoaTipo]=resultadoSQL($consultaPlano, 0, 'idPessoaTipo');
			
				# Ver dados da pessoa
				verPessoas('cadastros', 'clientes', 'ver', $matriz[idPessoaTipo], $matriz);
				echo "<br>";
				
				# Ver dados do Plano
				verPlanos($modulo, $sub, $acao, $idPlano, $matriz);
				echo "<br>";
				
				
				formAtivarServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
				
			}
			else {
				# Erro
				$msg="ERRO ao selecionar o Plano do Cliente!";
				$url="?modulo=cadastros&sub=clientes";
				aviso("Aviso", $msg, $url, 760);
			}
		}
		else {
			# Erro
			$msg="ERRO ao selecionar o Serviço!";
			$url="?modulo=cadastros&sub=clientes";
			aviso("Aviso", $msg, $url, 760);	
		}
	}
	else {
		# Gravar registro
		$matriz[valor]=formatarValores($matriz[valor]);




		#20050915, gustavo, TICKET FU5426R
		$matriz['idPlano'] = resultadoSQL($consulta, 0, 'idPlano');
		$matriz['idServicoPlano'] = $matriz['id'];
		
		$dtCancelamento = resultadoSQL($consulta, 0, 'dtCancelamento');
		$dtInativacao = resultadoSQL($consulta, 0, 'dtInativacao');
		$idStatusAtual = resultadoSQL($consulta, 0, 'idStatus');
		$matriz['idServico'] = resultadoSQL($consulta, 0, 'idServico');
		 
		$dadosPlano = dadosPlanos($matriz['idPlano']);
		
		$matriz['vencimento'] = dadosVencimento($dadosPlano['idVencimento']);
		
		if ($matriz['vencimento']['diaFaturamento'] == $matriz['vencimento']['diaVencimento']){
//			echo "cobranca diferenciada<br>";
			
			$consServico = buscaServicos($matriz['idServico'], 'id', 'igual', 'id');
			$idTipoCobranca =  resultadoSQL($consServico, 0, 'idTipoCobranca');
			
			$consTipoCobranca= buscaTipoCobranca($idTipoCobranca, 'id', 'igual', 'id');
			$matriz['tipoCobranca']['proporcional'] = resultadoSQL($consTipoCobranca, 0, 'proporcional');
			$matriz['tipoCobranca']['tipo'] 		= resultadoSQL($consTipoCobranca, 0, 'tipo');

			#somente ira gerar cobranca se o novo status for cobrado
			$dadosCob=checkStatusServico($matriz['idStatus']);
			
			$dadosCobAtual=checkStatusServico($idStatusAtual);
			
			if($dadosPlano['especial'] == 'S')
				$matriz['valorServico'] = resultadoSQL($consulta, 0, 'valor');
			else{
				$matriz['valorServico'] = resultadoSQL($consServico, 0, 'valor');
			}
				
			$dtInativacao = resultadoSQL($consulta, 0, 'dtInativacao');
//			echo "prop".$matriz['tipoCobranca']['proporcional'];
			if ( $dadosCob['cobranca'] == 'S' && $dadosCobAtual['cobranca'] != 'A' && $matriz['tipoCobranca']['tipo'] != 'pre'){
				$matriz['dtInativacao'] = $dtInativacao;
				if ($dtInativacao && $matriz['tipoCobranca']['proporcional'] != 'N' ){ 
					adicionarDescontoServicosPlanosReativar($matriz, true);
				}

//				echo "cobranca ativa, gerando SA";
				$ativ = adicionarServicoAdicionalServicoPlanoNovo($matriz, true);

			}
		  	elseif( $dadosCobAtual['cobranca'] == 'A' && $matriz['tipoCobranca']['tipo'] != 'pre' ){

		  		if ($dtInativacao)
					$matriz['dtInativacao'] = $dtInativacao; 
				
				$ativ = adicionarServicoAdicionalServicoPlanoPeriodoCompleto($matriz, true);					
		  	}
		  	$matriz['dtInativacao'] = '';

		}
		
		#fim da alteracao
		

		
		$data=dataSistema();
		$matriz[dtAtivacao]=converteData($matriz[dtAtivacao],'form','banco');
		
		$grava=dbServicosPlano($matriz, 'ativar');
		
		if($grava) {
		
			# Verificar contas ligadas ao serviço e nativá-las
			$radiusStatus=radiusStatusContaServico($registro, 'A');
		
			$matriz[idDominio] = ativarDominio($modulo, $sub, $acao, $registro, $matriz);
			
			# OK
			$msg="Serviço alterado com sucesso!";
			$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
			avisoNOURL("Aviso", $msg, 400);
			echo "<br>";
			
			# Ver dados da pessoa
			verPessoas('cadastros', 'clientes', 'ver', $matriz[idPessoaTipo], $matriz);
			echo "<br>";
			
			# Ver dados do Plano
			verPlanos($modulo, $sub, $acao, $matriz[idPlano], $matriz);
			
			listarServicosPlanos($modulo, $sub, 'abrir', $matriz[idPlano], $matriz);
		}
		else {
			# Erro
			$msg="ERRO ao alterar serviço! Tente novamente!";
			$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$matriz[idPlano]";
			avisoNOURL("Aviso", $msg, 400);
		}
	}

	
}




# Funcao para visualização de datas
function verDatasServicoPlano($matriz) {

	if(strlen(trim($matriz[dtCadastro])>0)) {
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Data de Cadastro:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL(converteData($matriz[dtCadastro], 'banco','form'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
	}
	if(strlen(trim($matriz[dtAtivacao])>0)) {
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Data de Ativação:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL(converteData($matriz[dtAtivacao], 'banco','form'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
	}
	if(strlen(trim($matriz[dtInativacao])>0)) {
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Data de Inativação:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL(converteData($matriz[dtInativacao], 'banco','form'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
	}
	if(strlen(trim($matriz[dtCancelamento])>0)) {
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Data de Cancelamento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaTMNOURL(converteData($matriz[dtCancelamento], 'banco','form'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
	}
		
}



# Funçao para busca de informações do vencimento
function dadosServicoPlano($idServicoPlano) {

	$consulta=buscaServicosPlanos($idServicoPlano, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		# dados do vencimento
		$retorno[id]=resultadoSQL($consulta, 0, 'id');
		$retorno[idPlano]=resultadoSQL($consulta, 0, 'idPlano');
		$retorno[idServico]=resultadoSQL($consulta, 0, 'idServico');
		$retorno[valor]=resultadoSQL($consulta, 0, 'valor');
		$retorno[dtCadastro]=resultadoSQL($consulta, 0, 'dtCadastro');
		$retorno[dtAtivacao]=resultadoSQL($consulta, 0, 'dtAtivacao');
		$retorno[dtInativacao]=resultadoSQL($consulta, 0, 'dtInativacao');
		$retorno[dtCancelamento]=resultadoSQL($consulta, 0, 'dtCancelamento');
		$retorno[idStatus]=resultadoSQL($consulta, 0, 'idStatus');
		$retorno[diasTrial]=resultadoSQL($consulta, 0, 'diasTrial');
	}
	
	return($retorno);
}

# função para alterar o servico de um plano
/**
 * @return void
 * @param unknown $modulo
 * @param unknown $sub
 * @param unknown $acao
 * @param unknown $registro
 * @param unknown $matriz
 * @desc Altera Servico de um plano, altera o idServico e Pede o valor do Plano caso seja especial
*/
function mudarServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $conn, $tb;
	
	planoEspecialMudancaServico($modulo,$sub, $acao,$registro, $matriz);
	# Busca dados do plano do servico selecionado
	/*$consulta= buscaServicosPlanos($registro,'id','igual','id');
	if ($consulta && contaConsulta($consulta)){
		$id= resultadoSQL($consulta,0,'id');
		$idPlano= resultadoSQL($consulta,0,'idPlano');
		$Servico= resultadoSQL($consulta,0,'idServico');
		$valor= resultadoSQL($consulta,0,'valor');
		$dtCadastro= resultadoSQL($consulta,0,'dtCadastro');
		$dtInativacao= resultadoSQL($consulta,0,'dtInativacao');
		$dtCancelamento= resultadoSQL($consulta,0,'dtCancelamento');
		$idStatus= resultadoSQL($consulta,0,'idStatus');
		$trial= resultadoSQL($consulta,0,'diasTrial');
	}
		
	# Recebe ID do Plano - Procurar por ID da Pessoa
	$consulta=buscaPlanos($idPlano, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		# prosseguir e mostarar pessoa e plano
		$idPessoa=resultadoSQL($consulta, 0, 'idPessoaTipo');
		$status=resultadoSQL($consulta, 0, 'status');
		$especial= resultadoSQL($consulta,0,'especial');
		
		# Ver dados da pessoa
		verPessoas('cadastros', 'clientes', 'ver', $idPessoa, $matriz);
		echo "<br>";
		
		# Ver dados do Plano
		verPlanos($modulo, $sub, $acao, $registro, $matriz);
		echo "<br>";
		
		# Checar Status do Plano
		if($status=='C') {
			$msg="Plano está Cancelado e não pode ser alterado!!!";
			$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
			avisoNOURL("Aviso", $msg, 600);
		}
		else {
		
			if(!$matriz[bntConfirmar]) { 
				# Formulário para adição de Serviço
				formServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
			else {
				# Gravar registro
				
				$data=dataSistema();
				
				if(!$matriz[dtCadastro]) $matriz[dtCadastro]=$data[dataBanco];
				else {
					$matriz[dtCadastro]=formatarData($matriz[dtCadastro]);
					$matriz[dtCadastro]=substr($matriz[dtCadastro],0,2).'/'.substr($matriz[dtCadastro],2,2).'/'.substr($matriz[dtCadastro],4,4);
					$matriz[dtCadastro]=converteData($matriz[dtCadastro],'form','bancodata');
				}
				
				# consultar se serviço gera cobrança (S=setar data de ativação)
				$statusCobranca=checkStatusServico($matriz[status]);
				if($statusCobranca[cobranca]=='S' || $statusCobranca[status]=='A') {
					$matriz[dtAtivacao]=$matriz[dtCadastro];
					$matriz[valor]=formatarValores($matriz[valor]);
				}
				#carrega dados para a matriz necessários pra a alteracao em cascata do plano(email, Radius)
				$matriz['id']= $id;
				$matriz['idPlano']= $idPlano;
				$matriz['idPessoaTipo']= $idPessoa;
				$matriz['dtCancelamento']= $data[dataBanco];
				$sql=   "SELECT
							$tb[StatusServicos].id
						FROM
							$tb[StatusServicos]
						WHERE
							$tb[StatusServicos].status= 'C'
				";
				$consulta= consultaSQL($sql, $conn);
				if ($consulta && contaConsulta($consulta))
					$matriz['idStatus']= resultadoSQL($consulta,0,'id');
				
				#cancela o servico atual
				$grava_cancServ= dbServicosPlano($matriz, 'cancelar');
				#gera servico adicional proporc. para cobrança de um servico cancelado
				$grava_servAdic= adicionarServicoAdicionalServicoPlano($matriz,'');
				
				#gera um novo Servico para o Plano, para onde deve-se migrar os outros servicos (email, etc)
				/*====================== consulta dados servico cancelado ================================*/
				/*$sql=   "SELECT
							$tb[ServicosPlanos].id,
							$tb[ServicosPlanos].idPlano,
							$tb[ServicosPlanos].idServico,
							$tb[ServicosPlanos].valor,
							$tb[ServicosPlanos].dtCancelamento,
							$tb[ServicosPlanos].idStatus,
							$tb[StatusServicos].status
						FROM
							$tb[ServicosPlanos], $tb[StatusServicos]
						WHERE
							$tb[ServicosPlanos].idStatus = $tb[StatusServicos].id AND
							$tb[ServicosPlanos].id=$id;
				";
				
				$consulta= consultaSQL($sql,$conn);
				if ($consulta && contaConsulta($consulta)>0){
					$dtCad= resultadoSQL($consulta,0,'dtCancelamento');
					$statusPlano= resultadoSQL($consulta,0,'idStatus');
					$statusServico= resultadoSQL($consulta, 0, 'status');
					$vlrServico= resultadoSQL($consulta,0,'valor');
				}
				#carrega a matriz de valores para a insercao de um novo Servico para o Plano
				$matriz['dtCadastro']= $dtCad;
				if ($statusServico == 'A')
					$matriz['dtAtivacao']= $dtCad;
				$matriz['dtCancelamento']='';
				$matriz['status']= $idStatus;
				$matriz['valor']= $vlrServico;
				/*======================= fim da consulta para novo plano =============================*/
				/*$grava_newServ= dbServicosPlano($matriz, 'incluir');
				$novoIdServicosPlanos= mysql_insert_id();
				$matriz['idServicosPlanos']= $novoIdServicosPlanos;
				#==================== migra os dominios do serviço anterior para o atual =====================#
				
				$matriz[idPessoaTipo]= $idPessoa;
				$matriz[idServicoPlano]= $novoIdServicosPlanos;
				
				#seleciona os dados do DominioServicosPlanos do Servico Cancelado
				$sql=   "SELECT
							$tb[DominiosServicosPlanos].idDominio
						FROM
							$tb[DominiosServicosPlanos]
						WHERE
							$tb[DominiosServicosPlanos].idServicosPlanos= $id
				";
				$consulta= consultaSQL($sql,$conn);
				#carrega a matriz de dados necessaria para a alteracao da tabela de DominiosServicosPlanos
				if ($consulta && contaConsulta($consulta)){
					$idDominio= resultadoSQL($consulta,0, 'idDominio');
					$matriz[id]= $idDominio;
					$grava_dominios= dbDominiosServicosPlanos($matriz, 'transferir');
				}
				#================================== FIM DOMINIOS SERVICOS PLANOS =============================#				
				#========================= migra os emails do serviço anterior para o atual ==================#
				
				#consulta dados de email relacionado ao Servico cancelado!
				$sql=   "SELECT
							$tb[Emails].id,
							$tb[Emails].senhaTexto
						FROM 
							$tb[Emails]
						WHERE 
							$tb[Emails].idPessoaTipo= $idPessoa	AND
							$tb[Emails].idDominio=  $idDominio
				";
				if ($idDominio) //se nao tem dominio, naum hah como ter email
					$consulta= consultaSQL($sql,$conn);
				if ($consulta && contaConsulta($consulta)){
					$matriz[id]= resultadoSQL($consulta,0, 'id');
					$matriz[senha_conta]= resultadoSQL($consulta,0,'senhaTexto');
					$grava_email= dbEmail($matriz, 'alterar');
				}
				#================================= FIM ALTERACAO DE EMAIL ===================================#
				
				#=================== #migra as confs IVR do serviço anterior para o atual ===================#
				
				#seleciona os dados da tabela ServicoIVR 
				$sql=   "SELECT
							$tb[ServicosIVR].id,
							$tb[ServicosIVR].idServicoPlano,
							$tb[ServicosIVR].idBase,
							$tb[ServicosIVR].nome,
							$tb[ServicosIVR].ip,
							$tb[ServicosIVR].mask,
							$tb[ServicosIVR].mac,
							$tb[ServicosIVR].gw,
							$tb[ServicosIVR].dns1,
							$tb[ServicosIVR].dns2,
							$tb[ServicosIVR].so,
							$tb[ServicosIVR].status,
							$tb[ServicosIVR].obs
						FROM
							$tb[ServicosIVR]
						WHERE
							$tb[ServicosIVR].idServicoPlano= $id
				";
				$consulta= consultaSQL($sql, $conn);
				#carrega a matriz de valores para alteracao da Tabela ServicoIVR
				if ($consulta && contaConsulta($consulta)){
					$matriz[id]= resultadoSQL($consulta,0,'id');
					$matriz[idServicoPlano]= $novoIdServicosPlanos;
					$matriz[idBase]= resultadoSQL($consulta,0,'idBase');
					$matriz[nome]= resultadoSQL($consulta,0,'nome');
					$matriz[ip]= resultadoSQL($consulta,0,'ip');
					$matriz[mask]= resultadoSQL($consulta,0,'mask');
					$matriz[mac]= resultadoSQL($consulta,0,'mac');
					$matriz[gw]= resultadoSQL($consulta,0,'gw');
					$matriz[dns1]= resultadoSQL($consulta,0,'dns1');
					$matriz[dns2]= resultadoSQL($consulta,0,'dns2');
					$matriz[so]= resultadoSQL($consulta,0,'so');
					$matriz[status]= resultadoSQL($consulta,0,'status');					
					$matriz[obs]= resultadoSQL($consulta,0,'obs');
			
					$grava_IVR= dbservicosIVR($matriz, 'alterar');
				}
				#============================ FIM SERVICOSIVR ================================================#
				if($grava_newServ) { //$grava
					$msg="Serviço adicionado com sucesso!!!";
					$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";						
					//$matriz[idStatus]=$statusPlano;
 					//formAlterarServicosPlanos($modulo,$sub,$acao,$novoIdServicosPlanos,$matriz);
 					if ($especial == 'S'){
 						//planoEspecialMudancaServico($modulo, $sub, $acao, $registro, $matriz);
 						print "<script language='javascript'>alert('Eh um plano especial?')</script>";
 						planoEspecialMudancaServico($modulo,$sub, $acao, $idPlano, $matriz);
 					}
 					else
						listarServicosPlanos($modulo, $sub, $acao, $idPlano, $matriz);
				}
				else {
					# Erro
					$msg="ERRO ao modificar serviço! Tente novamente!";
					$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
					avisoNOURL("Aviso", $msg, 600);
				}
			}
		}
	}
	else {
		# Erro
		$msg="ERRO ao selecionar o Plano do Cliente!";
		$url="?modulo=cadastros&sub=clientes";
		aviso("Aviso", $msg, $url, 760);
	}*/	
}
function planoEspecialMudancaServico($modulo, $sub, $acao, $registro, $matriz){
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro;
	
	# Busca Dados dos Servicos do Plano
	if ($matriz['idServicoPlano'])
		$registro= $matriz['idServicoPlano'];
	$consulta= buscaServicosPlanos($registro,'id','igual','id');
	if ($consulta && contaConsulta($consulta)){
		$id= resultadoSQL($consulta,0,'id');
		$idPlano= resultadoSQL($consulta,0,'idPlano');
		$Servico= resultadoSQL($consulta,0,'idServico');
		$valor= resultadoSQL($consulta,0,'valor');
		$dtCadastro= resultadoSQL($consulta,0,'dtCadastro');
		$dtInativacao= resultadoSQL($consulta,0,'dtInativacao');
		$dtCancelamento= resultadoSQL($consulta,0,'dtCancelamento');
		$idStatus= resultadoSQL($consulta,0,'idStatus');
		$trial= resultadoSQL($consulta,0,'diasTrial');
	}
	
	# Recebe ID do Plano - Procurar por ID da Pessoa
	$consulta=buscaPlanos($idPlano, 'id','igual','id');
	if($consulta && contaConsulta($consulta)>0) {
		# prosseguir e mostarar pessoa e plano
		$idPessoa=resultadoSQL($consulta, 0, 'idPessoaTipo');
		$status=resultadoSQL($consulta, 0, 'status');
		
		$registro= $idPlano;
		# Ver dados da pessoa
		verPessoas('cadastros', 'clientes', 'ver', $idPessoa, $matriz);
		echo "<br>";
		# Ver dados do Plano
		verPlanos($modulo, $sub, $acao, $registro, $matriz);
		echo "<br>";
		# Checar Status do Plano
		if($status == 'C') {
			$msg="Plano está Cancelado e não é possível modificar serviços!";
			$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
			avisoNOURL("Aviso", $msg, 600);
		}
		else 	{
			# Carrega alguns valores necessarios para a var $matriz
			$matriz[id]= $id;
			if(!$matriz[bntConfirmar]) {
				# Formulário para adição de Serviço
				formMudarServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
			}
			else {
				# Gravar registro
				
				$data=dataSistema();
				
				if(!$matriz[dtCadastro]) $matriz[dtCadastro]=$data[dataBanco];
				else {
					$matriz[dtCadastro]=formatarData($matriz[dtCadastro]);
					$matriz[dtCadastro]=substr($matriz[dtCadastro],0,2).'/'.substr($matriz[dtCadastro],2,2).'/'.substr($matriz[dtCadastro],4,4);
					$matriz[dtCadastro]=converteData($matriz[dtCadastro],'form','bancodata');
				}
				
				# consultar se serviço gera cobrança (S=setar data de ativação)
				$statusCobranca=checkStatusServico($matriz[status]);
				if($statusCobranca[cobranca]=='S' || $statusCobranca[status]=='A') {
					$matriz[dtAtivacao]=$matriz[dtCadastro];
					$matriz[valor]=formatarValores($matriz[valor]);
				}
				
				//$grava=dbServicosPlano($matriz, 'alterar');
				$matriz['id']= $id;
				$matriz['idPlano']= $idPlano;
				$matriz['idPessoa']= $idPessoa;
				$matriz['status']= $idStatus;
				$matriz['dtCancelamento']= $data[dataBanco];
				$matriz['oldId']= $id;
				$grava= mudaPlano($matriz);
				
				# o retorno de $grava tras o novo id do servico modificado necessário pra as devidas migracoes de email, dominios, servicoIVR
				if($grava) { 
					# OK
					# Se tudo ocorreu ok! Vamos fazer as migraçoes necessarias
					$matriz[idServicosPlanos]= $grava;
					$matriz[idServicoPlano]= $grava;
					## migrando dominios
					$matriz[idDominio]= migra_dominioPlano($matriz);
					## migrando emails
					migra_email($matriz);
					## migrando servicosIVR
					migra_servicoIVR($matriz);
					## migrando RAdiusUsuariosServicosPlanos
					migra_radius_usuarios($matriz);
					
					$msg="Serviço modificado com sucesso!";
					#a linha abaixo parece ser restos de progr. foi comentada para verificacao!
					//$url="?modulo=".$modulo."&sub=".$sub."&acao=abrir&registro=".$idPlano;
					avisoNOURL("Aviso", $msg, 600);
					
					listarServicosPlanos($modulo, $sub, $acao, $idPlano, $matriz);
				}
				else {
					# Erro
					$msg="ERRO ao modificar serviço! Tente novamente!";
					#a linha abaixo parece ser restos de progr. foi comentada para verificacao!
					//$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$registro";
					avisoNOURL("Aviso", $msg, 600);
				}
			}
		}
	}
	else {
		# Erro
		$msg="ERRO ao selecionar o Plano do Cliente!";
		#a linha abaixo parece ser restos de progr. foi comentada para verificacao!
		//$url="?modulo=cadastros&sub=clientes";
		aviso("Aviso", $msg, $url, 760);
	}

	
}

function mudaPlano($matriz){
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $conn, $tb;
	
	$sql=   "SELECT
				$tb[StatusServicos].id,
				$tb[StatusServicos].status
			FROM
				$tb[StatusServicos]
			WHERE
				$tb[StatusServicos].status= 'C' or 
				$tb[StatusServicos].id= $matriz[status]
	";
	# nesta consulta busco os campos id e status com status = C ou id = id plano a ser cancelado
	# o id será usado para cancelar o plano que se deseja cancelar e o status tras o estado deste plano a ser definido no novo plano 
	# ou seja se status for 'A' defino a data de ativacao ou se for 'I' naum defino a data de ativacao
	$consulta= consultaSQL($sql, $conn);
	if ($consulta && contaConsulta($consulta)){
		$status= resultadoSQL($consulta,0,'status');
		if ( $status == 'C' ){
			$matriz['idStatus']= resultadoSQL($consulta,0,'id');
			$statusPlano= resultadoSQL($consulta, 1,'status');
		}
		else{
			$matriz['idStatus']= resultadoSQL($consulta,1,'id');
			$statusPlano= resultadoSQL($consulta, 0,'status');			
		}
	}
	#cancela o servico atual
	$grava_cancServ= dbServicosPlano($matriz, 'cancelar');
	#gera servico adicional proporc. para cobrança de um servico cancelado
	$grava_servAdic= adicionarServicoAdicionalServicoPlano($matriz,'');
	
	#gera um novo Servico para o Plano, para onde deve-se migrar os outros servicos (email, etc)
	/*====================== consulta dados servico cancelado ================================*/
	$sql=   "SELECT
				$tb[ServicosPlanos].id,
				$tb[ServicosPlanos].idPlano,
				$tb[ServicosPlanos].idServico,
				$tb[ServicosPlanos].valor,
				$tb[ServicosPlanos].dtCancelamento,
				$tb[ServicosPlanos].idStatus,
				$tb[StatusServicos].status
			FROM
				$tb[ServicosPlanos], $tb[StatusServicos]
			WHERE
				$tb[ServicosPlanos].idStatus = $tb[StatusServicos].id AND
				$tb[ServicosPlanos].id=$matriz[id];
	";
	
	$consulta= consultaSQL($sql,$conn);
	if ($consulta && contaConsulta($consulta)>0){
		$dtCad= resultadoSQL($consulta,0,'dtCancelamento');
		//$statusPlano= resultadoSQL($consulta,0,'idStatus');
		$statusServico= resultadoSQL($consulta, 0, 'status');
		//$vlrServico= resultadoSQL($consulta,0,'valor');
		$idServico= resultadoSQL($consulta, 0 , 'idServico');
	}
	#carrega a matriz de valores para a insercao de um novo Servico para o Plano
	$matriz['dtCadastro']= $dtCad;
	if ($statusPlano == 'A')
		$matriz['dtAtivacao']= $dtCad;
	$matriz['dtCancelamento']='';
	//$matriz['valor']= $vlrServico;

	/*======================= fim da consulta para novo plano =============================*/
	$grava_newServ= dbServicosPlano($matriz, 'incluir');
	$novoIdServicosPlanos= mysql_insert_id();
	return $novoIdServicosPlanos;	
} //FIM --> mudaPlano

# formulário de dados cadastrais - para Mudanca de plano
function formMudarServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	$data=dataSistema();

	# Pessoa física
	novaTabela2("[Serviços do Plano]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	novaLinhaTabela($corFundo, '100%');
		$texto="			
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=registro value=$registro>
			<input type=hidden name=matriz[idPlano] value=$registro>
			<input type=hidden name=matriz[idServicoPlano] value=$matriz[id]>		
			<input type=hidden name=acao value=$acao>&nbsp;";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
	fechaLinhaTabela();
	
	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL('<b>Serviço:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		$texto="<input type=submit name=matriz[bntSelecionar] value=Selecionar>";
		itemLinhaForm(formSelectServicos($matriz[idServico], 'idServico','form').$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
	if($matriz[idServico]) {
		# Selecionar Serviços
		$consultaServico=buscaServicos($matriz[idServico], 'id','igual','id');
		if($consultaServico && contaConsulta($consultaServico)>0) {
			# informaçoes do serviço
			$nome=resultadoSQL($consultaServico, 0, 'nome');
			$descricao=resultadoSQL($consultaServico, 0, 'descricao');
			$idTipoCobranca=resultadoSQL($consultaServico, 0, 'idTipoCobranca');
			$valor=resultadoSQL($consultaServico, 0, 'valor');
			$idStatusPadrao=resultadoSQL($consultaServico, 0, 'idStatusPadrao');
			
			$matriz[status]=$idStatusPadrao;
			
			if(!$matriz[valor]) $matriz[valor]=formatarValoresForm($valor);
			
			# Buscar informações sobre o serviço 
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Valor do Serviço:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL($valor, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Tipo de Cobrança:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL(formSelectTipoCobranca($idTipoCobranca, '','check'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			# Verificar se plano é especial - Caso SIM - pedir valor especial do serviço
			$registro= $matriz[idPlano];
			if(checkPlanoEspecial($registro)) {
				itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
				novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('100%', 'center', $corFundo, 2, 'tabfundo1');
					novaTabela2("[Plano Especial]", "center", '600', 0, 2, 1, $corFundo, $corBorda, 2);
						novaLinhaTabela($corFundo, '100%');
							$texto="<span class=txtaviso>ATENÇÃO:
								Planos especiais requerem os valores especiais de cada serviço! <br>
								Informe o valor especial para este serviço!</span><br>
								<br>
								<span class=txtaviso>(Formato: ".formatarValores($valor)." => $valor) - (Valor do Serviço: R$ $valor)</span>";
								itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
							fechaLinhaTabela();
						fechaLinhaTabela();
						itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL('<b>Desconto:</b>', 'right', 'middle', '50%', $corFundo, 0, 'tabfundo1');
							$valorCompara=formatarValores($valor);
							itemLinhaTMNOURL(formSelectDesconto($matriz[desconto], 'desconto','form', $valor, 8), 'left', 'middle', '50%', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
						$valorFormatado=formatarValoresForm($valor);
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL('<b>Valor especial do Serviço:</b>', 'right', 'middle', '50%', $corFundo, 0, 'tabfundo1');
							$texto="<input type=text name=matriz[valor] value='$valorFormatado' size=10 onBlur=verificarValor($valorCompara,this.value);formataValor(this.value,8)>";
							itemLinhaTMNOURL($texto, 'left', 'middle', '50%', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
					fechaTabela();
				htmlFechaColuna();
				fechaLinhaTabela();
				itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
			}
			
			# Informar Status do Serviço
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Dias de Trial:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[trial] value='$matriz[trial]' size=3>
				<span class=txtaviso>(ATENÇÃO: Estes dias não serão cobrados após a Ativação do Serviço)</span>";
				itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Status do Serviço:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL(formSelectStatusServico($matriz[status], 'status','form'), 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Data de Cadastro:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				if(checkPlanoEspecial($registro)) $indice=11;
				else $indice=9;
				$texto="<input name=matriz[dtCadastro] size=10 value=".formatarData(substr($data[dataNormal],0,10))." onBlur=verificaData(this.value,$indice)>";
				itemLinhaTMNOURL($texto, 'left', 'middle', '70%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			formPessoaSubmit($modulo, $sub, $acao, $registro, $matriz);
			$registro= $matriz['idServicoPlano'];
		}
		else {
			# Mensagem de aviso
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('100%', 'center', $corFundo, 2, 'tabfundo1');
					$msg="Não foi possível localizar o serviço selecionado!<br>";
					$msg.="Consulte a listagem de serviços disponíveis e tente novamente!";
					$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$registro";
					avisoNOURL("Aviso", $msg, '400');
				htmlFechaColuna();
			fechaLinhaTabela();
		}
		
		
	}

	fechaTabela();
} // FIM --> formMudarServicosPlanos


/**
 * @return void
 * @param unknown $id
 * @desc Funcao que faz a migração dos dominios de um servico Modificado
*/
function migra_dominioPlano($matriz){
	
global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $conn, $tb;
	
#==================== migra os dominios do serviço anterior para o atual =====================#

$matriz[idPessoaTipo]= $matriz[idPessoa];

#seleciona os dados do DominioServicosPlanos do Servico Cancelado
$sql=   "SELECT
			$tb[DominiosServicosPlanos].idDominio
		FROM
			$tb[DominiosServicosPlanos]
		WHERE
			$tb[DominiosServicosPlanos].idServicosPlanos= $matriz[oldId]
";
$consulta= consultaSQL($sql,$conn);
#carrega a matriz de dados necessaria para a alteracao da tabela de DominiosServicosPlanos
if ($consulta && contaConsulta($consulta)){
	for($i=0; $i<contaConsulta($consulta); $i++){
		$idDominio= resultadoSQL($consulta,$i, 'idDominio');
		$matriz[id]= $idDominio;
		$grava_dominios= dbDominiosServicosPlanos($matriz, 'transferir');
	}
	return $idDominio;
}
#================================== FIM DOMINIOS SERVICOS PLANOS =============================#					
}// FIM --> migra_dominioPlano

function migra_email($matriz){
	
global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $conn, $tb;

#========================= migra os emails do serviço anterior para o atual ==================#

#consulta dados de email relacionado ao Servico cancelado!
$sql=   "SELECT
			$tb[Emails].id,
			$tb[Emails].senhaTexto
		FROM 
			$tb[Emails]
		WHERE 
			$tb[Emails].idPessoaTipo= $matriz[idPessoa]	AND
			$tb[Emails].idDominio=  $matriz[idDominio]
";
if ($matriz[idDominio]) //se nao tem dominio, naum hah como ter email
	$consulta= consultaSQL($sql,$conn);
if ($consulta && contaConsulta($consulta)){
	for($i=0; $i<contaConsulta($consulta); $i++){	
		$matriz[id]= resultadoSQL($consulta,$i, 'id');
		$matriz[senha_conta]= resultadoSQL($consulta,0,'senhaTexto');
		$grava_email= dbEmail($matriz, 'alterar');
	}
}
#================================= FIM ALTERACAO DE EMAIL ===================================#
}// FIM --> migra_email

function migra_servicoIVR($matriz){
	
global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $conn, $tb;

#=================== #migra as confs IVR do serviço anterior para o atual ===================#

#seleciona os dados da tabela ServicoIVR 
$sql=   "SELECT
			$tb[ServicosIVR].id,
			$tb[ServicosIVR].idServicoPlano,
			$tb[ServicosIVR].idBase,
			$tb[ServicosIVR].nome,
			$tb[ServicosIVR].ip,
			$tb[ServicosIVR].mask,
			$tb[ServicosIVR].mac,
			$tb[ServicosIVR].gw,
			$tb[ServicosIVR].dns1,
			$tb[ServicosIVR].dns2,
			$tb[ServicosIVR].so,
			$tb[ServicosIVR].status,
			$tb[ServicosIVR].obs
		FROM
			$tb[ServicosIVR]
		WHERE
			$tb[ServicosIVR].idServicoPlano= $matriz[oldId]
";
$consulta= consultaSQL($sql, $conn);
#carrega a matriz de valores para alteracao da Tabela ServicoIVR
if ($consulta && contaConsulta($consulta)){
	$matriz[id]= resultadoSQL($consulta,0,'id');
	$matriz[idServicoPlano]= resultadoSQL($consulta,0,'idServicoPlano');
	$matriz[idBase]= resultadoSQL($consulta,0,'idBase');
	$matriz[nome]= resultadoSQL($consulta,0,'nome');
	$matriz[ip]= resultadoSQL($consulta,0,'ip');
	$matriz[mask]= resultadoSQL($consulta,0,'mask');
	$matriz[mac]= resultadoSQL($consulta,0,'mac');
	$matriz[gw]= resultadoSQL($consulta,0,'gw');
	$matriz[dns1]= resultadoSQL($consulta,0,'dns1');
	$matriz[dns2]= resultadoSQL($consulta,0,'dns2');
	$matriz[so]= resultadoSQL($consulta,0,'so');
	$matriz[status]= resultadoSQL($consulta,0,'status');					
	$matriz[obs]= resultadoSQL($consulta,0,'obs');

	$grava_IVR= dbservicosIVR($matriz, 'alterar');
}
#============================ FIM SERVICOSIVR ================================================#
}// FIM --> migra_servicoIVR

function migra_radius_usuarios($matriz){
	
global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $conn, $tb;

#========================= migra os emails do serviço anterior para o atual ==================#

#consulta dados de RadiusUsuariosServicosPlanos relacionado ao Servico cancelado!
$sql=   "SELECT
			$tb[RadiusUsuariosPessoasTipos].id
		FROM 
			$tb[RadiusUsuariosPessoasTipos]
		WHERE 
			$tb[RadiusUsuariosPessoasTipos].idPessoasTipos= $matriz[idPessoa]	AND
			$tb[RadiusUsuariosPessoasTipos].idServicosPlanos=  $matriz[oldId]
";
$consulta= consultaSQL($sql,$conn);
if ($consulta && contaConsulta($consulta)){
	$matriz[idRadius]= resultadoSQL($consulta,0, 'id');
	$grava_RadiusUsuarios= radiusDBUsuarioPessoaTipo($matriz, 'alterarservico');
}
#================================= FIM ALTERACAO DE EMAIL ===================================#
}// FIM --> migra_radius_usuarios


/**
 * @return void
 * @desc Funcao que faz a inativacao dos dominios ao inativar Servico. 
 * @param unknown $id
*/
function inativarDominio($modulo, $sub, $acao, $registro, $matriz) {
		
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $conn, $tb;
	
	$matriz[idPessoaTipo]= $matriz[idPessoa];
	
	#seleciona os dados do DominioServicosPlanos do Servico Inativado
	$sql=   "SELECT
				$tb[DominiosServicosPlanos].idDominio
			FROM
				$tb[DominiosServicosPlanos]
			WHERE
				$tb[DominiosServicosPlanos].idServicosPlanos= '$registro'
	";
	$consulta= consultaSQL($sql,$conn);
	#carrega a matriz de dados necessaria para a alteracao da tabela de DominiosServicosPlanos
	if ($consulta && contaConsulta($consulta)){
		for($i=0; $i<contaConsulta($consulta); $i++){
			$matriz[id]=resultadoSQL($consulta,$i, 'idDominio');
			$matriz[bntInativar] = 1;
			inativarDominiosServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
		}
		return $idDominio;
	}			
}// FIM 


/**
 * @return void
 * @desc Funcao que faz o inativacao dos dominios ao inativar Servico. 
 * @param unknown $id
*/
function ativarDominio($modulo, $sub, $acao, $registro, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $conn, $tb;
	
	$matriz[idPessoaTipo]= $matriz[idPessoa];
	
	#seleciona os dados do DominioServicosPlanos do Servico Inativado
	$sql=   "SELECT
				$tb[DominiosServicosPlanos].idDominio
			FROM
				$tb[DominiosServicosPlanos]
			WHERE
				$tb[DominiosServicosPlanos].idServicosPlanos= '$registro'
	";
	$consulta= consultaSQL($sql,$conn);
	#carrega a matriz de dados necessaria para a alteracao da tabela de DominiosServicosPlanos
	if ($consulta && contaConsulta($consulta)){
		for($i=0; $i<contaConsulta($consulta); $i++){
			$matriz[id]=resultadoSQL($consulta,$i, 'idDominio');
			$matriz[bntAtivar] = 1;
			ativarDominiosServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
		}
		return $idDominio;
	}
			
}
/**
 * @author sombra
 * @desc faz o calculo do servico dentro de um recebimento, proporcionalmente ao seu valor inicial.
 * 
 */
function calculaProporcional ($valorRecebido, $valorPlano, $valorServico) {
	global $conn, $tb;
	
//	printf("Valor Recebiddo %s , valorPlano %s , valorServico %s", $valorRecebido, $valorPlano, $valorServico);
	
	if ($valorServico > 0 ){		
		$propServico = $valorServico / $valorPlano;
		$valor = $valorRecebido * $propServico;
	}
	else $valor=$valorRecebido;
	
	return ($valor);
	
}

function pessoasTiposFormSelectServicosPlanos($idPessoaTipo, $idServico, $campo) {
	global $conn, $tb;
	
	$sql = "SELECT $tb[ServicosPlanos].id, 
				$tb[Servicos].nome As nomeServico, 
				$tb[PlanosPessoas].id idPlanoPessoa, 
				PlanosPessoas.nome AS nomePlano 
			FROM $tb[PlanosPessoas] 
			INNER JOIN $tb[ServicosPlanos] 
				On($tb[PlanosPessoas].id = $tb[ServicosPlanos].idPlano) 
			INNER JOIN $tb[Servicos] 
				On($tb[ServicosPlanos].idServico = $tb[Servicos].id) 
			WHERE 
				$tb[PlanosPessoas].idPessoaTipo=$idPessoaTipo  
		";
	$consultaServicos = consultaSQL($sql, $conn);
	
	$retorno="<select name=matriz[$campo]>\n <option value=0> Selecione um Servico: </option>";
	for ($i=0; $i<contaConsulta($consultaServicos); $i++){
		$idServicoPlano=resultadoSQL($consultaServicos, $i, "id");
		$idPlanoPessoa=resultadoSQL($consultaServicos, $i, "idPlanoPessoa");
		($idServicoPlano == $idServico ? $check = "selected" : $check = "");
		$retorno.="<option value=$idServicoPlano:$idPlanoPessoa $check >Plano: ". resultadoSQL($consultaServicos, $i, "nomePlano") ." - Servico: ". resultadoSQL($consultaServicos, $i, "nomeServico");
	}		
	$retorno.="</select>";
	
	return ($retorno);
}

/**
 * Função responsável por verificar se um Serviço vinculado à um plano foi bloqueado ou não
 * @author Felipe dos S. Assis
 * @since 13/06/2008
 * @param unknown_type $idServicoPlano
 * @return unknown
 */
function getServicoBloqueado($idSuporte){
	global $tb, $conn;
	
	$sql = "SELECT $tb[Suporte].idServicoPlano FROM $tb[Suporte] WHERE $tb[Suporte].id = " . $idSuporte;
	$consulta = consultaSQL($sql, $conn);
	if(contaConsulta($consulta) > 0){
		$idServicoPlano = resultadoSQL($consulta, 0, 0);
		$sql = "SELECT idStatus FROM $tb[ServicosPlanos] WHERE $tb[ServicosPlanos].id = " . $idServicoPlano;
		$consulta = consultaSQL($sql, $conn);
		
		if(contaConsulta($consulta) > 0){
			$idStatus = resultadoSQL($consulta, 0, 0);
			if($idStatus == 7){
				return true;
			}
			else{
				return false;
			}
		}
	}
	
}

function  calculaValorServicosPlanos($idPlano, $especial, $idVencimento, $sqlADD, $mes, $ano, $matriz) {
	global $conn, $tb;
	
	$sql="
		SELECT
			$tb[ServicosPlanos].id id, 
			$tb[ServicosPlanos].idServico idServico, 
			$tb[ServicosPlanos].valor valor, 
			$tb[ServicosPlanos].dtCadastro dtCadastro, 
			$tb[ServicosPlanos].dtAtivacao dtAtivacao, 
			$tb[ServicosPlanos].diasTrial diasTrial, 
			$tb[StatusServicos].cobranca cobranca, 
			$tb[Servicos].valor valorServico, 
			$tb[Servicos].nome nomeServico,
			$tb[TipoCobranca].proporcional proporcional, 
			$tb[TipoCobranca].forma formaCobranca, 
			$tb[TipoCobranca].tipo tipoCobranca 
		FROM
			$tb[Servicos], 
			$tb[ServicosPlanos], 
			$tb[PlanosPessoas], 
			$tb[TipoCobranca], 
			$tb[StatusServicos]
		WHERE
			$tb[PlanosPessoas].id=$tb[ServicosPlanos].idPlano
			AND $tb[ServicosPlanos].idServico = $tb[Servicos].id 
			AND $tb[Servicos].idTipoCobranca = $tb[TipoCobranca].id 
			AND $tb[ServicosPlanos].idStatus = $tb[StatusServicos].id 
			AND $tb[ServicosPlanos].idPlano=$idPlano
			$sqlADD";
	
	$consulta = consultaSQL($sql, $conn);
	
	$idServicoPlano		= resultadoSQL($consulta, 0, 'id');
	$idServico			= resultadoSQL($consulta, 0, 'idServico');
	$nomeServico		= resultadoSQL($consulta, 0, 'nomeServico');
	$data 				= dataSistema();
	$dtAtivacaoMinima	= "$ano-$mes-31";
	$vencimento			= dadosVencimento($idVencimento);
	$vencimento[mes]	= $mes;
	$vencimento[ano]	= $ano;
	
	if ($especial == "S") {
		$valorServico = resultadoSQL($consulta, 0, "valor");
	} else {
		$valorServico = resultadoSQL($consulta, 0, "valorServico");
	}
	
//	$retorno += $valorServico;
	
	$dtCadastro		= resultadoSQL($consulta, 0, 'dtCadastro');
	$dtAtivacao		= resultadoSQL($consulta, 0, 'dtAtivacao');
	
	if (formatarData($dtAtivacao) <= 0) {
		$dtAtivacao = $dtCadastro;
	}
	
	$diasTrial 		= resultadoSQL($consulta, 0, 'diasTrial');
	$cobranca		= resultadoSQL($consulta, 0, 'cobranca');
	$proporcional	= resultadoSQL($consulta, 0, 'proporcional');
	$formaCobranca	= resultadoSQL($consulta, 0, 'formaCobranca');
	$tipoCobranca	= resultadoSQL($consulta, 0, 'tipoCobranca');

	if ($formaCobranca == 'mensal') {
		if ($cobranca == 'S') {
			/* Verifica se serviço tem valor Proporcional */
			if ($proporcional == 'S') {
				/* Calcular dias e valor proporcional
				 Data de Vencimento com dia de Faturamento não de Vencimento
				 Serviço tem calculo baseado em data de Ativação e data de Faturamento
				 par proporcionalidade */
				$tmpValor = calculaValorProporcional($dtAtivacao, $diasTrial, $vencimento, $valorServico, $tipoCobranca);
				$retorno += $tmpValor[valor];
			} else {
				/* Verificar se servico nao esta em período trial */
				$tmpValor = calculaValorNaoProporcional($dtAtivacao, $diasTrial, $vencimento, $valorServico);
				$retorno += $tmpValor[valor];
			}
		}
	} elseif ($formaCobranca == 'anual' || $formaCobranca == 'semestral' || $formaCobranca == 'trimestral') {
		if ($cobranca == 'S') {
			/* Cobrar servico - verificando anuidade */
			$tmpValor = calculaValorServicoPeriodico($idServicoPlano, $dtAtivacao, $diasTrial, $vencimento, $valorServico, $formaCobranca);
			$retorno += $tmpValor["valor"];
		}
	}
	
	if (!$tmpValor["valor"]) {
		$retorno += $valorServico;
	}
	
	/* Informações sobre data de vencimento do cliente */
	$anoAtivacao =substr($dtAtivacao, 0, 4);
	$mesAtivacao = substr($dtAtivacao, 5, 2);
	$diaAtivacao = substr($dtAtivacao, 8, 2);

	/* Data de Vencimento com dia de Vencimento não de faturamento */
	$dtVencimento = calculaVencimentoCobranca($dtAtivacao, $diasTrial, $vencimento, $mes, $ano);
	/* Verificar Servicos Adicionais */
	$retorno += calculaServicosAdicionais($idServicoPlano, $dtVencimento);
	/* Verificar Descontos */
	/* Data de Vencimento com dia de Vencimento não de faturamento */
	$desconto = calculaDescontos($idServicoPlano, $dtVencimento);
	if ($retorno > 0 && $tmpValor[valor] >= $desconto ) {
		$retorno -= $desconto;
	}

	/* Verificar se valor do serviço é valido (maior do que valor minimo de faturamento) */
//	if ($retorno) {
//		$matriz["idServicoPlano"] = $idServicoPlano;
//		//$matriz[valor]=($tmpValor[valor] + $servicoAdicional - $desconto);
//		$matriz["valor"] = $retorno;
////		$gravaServicosPlanosDocumentosGerados=dbServicoPlanoDocumentoGerado($matriz, 'incluir');
//	}
	return $retorno;
}
?> 