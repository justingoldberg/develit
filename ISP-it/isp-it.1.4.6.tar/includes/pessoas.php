<?


################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 23/06/2003
# Ultima alteração: 11/03/2004
#    Alteração No.: 021
#
# Função:
#    Painel - Funções para cadastro de pessoas

# Cadastro de Pessoas
function pessoas($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessCadastro;

	# Permissão do usuario
	//	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');

	//	if(!$permissao[admin] && !$permissao[adicionar]) {
	//		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
	//		$msg="ATENÇÃO: Você não tem permissão para executar esta função!";
	//		$url="?modulo=$modulo&sub=$sub";
	//		aviso("Acesso Negado", $msg, $url, 760);
	//	}
	//	else {

	$tipoPessoa = checkIDTipoPessoa($matriz[tipoPessoa]);
	if ($tipoPessoa) {
		$matriz[tipoPessoa] = $tipoPessoa[valor];
	}

	# Verificar busca
	if ($matriz[txtProcurar] && $matriz[bntProcurar]) {
		if ($matriz[tipoPessoa] && $matriz[tipoPessoa] != "*") {
			$tmpConsulta = checkIDTipoPessoa($matriz[tipoPessoa]);

			$matriz[tipoPessoa] = $tmpConsulta[valor];
			if (!$matriz[tipoPessoa]) {
				# Verificar tipo de Pessoa
				if ($sub == 'clientes')
					$matriz[tipoPessoa] = 'cli';
				elseif ($sub == 'fornecedores') $matriz[tipoPessoa] = 'for';
				elseif ($sub == 'condominios') $matriz[tipoPessoa] = 'cond';
			}

		} else {
			# Verificar tipo de Pessoa
			if ($sub == 'clientes')
				$matriz[tipoPessoa] = 'cli';
			elseif ($sub == 'fornecedores') $matriz[tipoPessoa] = 'for';
			elseif ($sub == 'condominios') $matriz[tipoPessoa] = 'cond';
		}
	} else {
		# Verificar tipo de Pessoa
		if ($sub == 'clientes')
			$matriz[tipoPessoa] = 'cli';
		elseif ($sub == 'fornecedores') $matriz[tipoPessoa] = 'for';
		elseif ($sub == 'condominios') $matriz[tipoPessoa] = 'cond';
	}

	# Buscar Tipo Pessoa
	$tipoPessoa = checkTipoPessoa($matriz[tipoPessoa]);

	if ($tipoPessoa) {
		# Topo da tabela - Informações e menu principal do Cadastro
		novaTabela2("[Cadastro de $tipoPessoa[descricao]]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 3);
		novaLinhaTabela($corFundo, '100%');
		htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
		echo "<br><img src=" . $html[imagem][cadastro] . " border=0 align=left><b class=bold>$tipoPessoa[descricao]</b>
										<br><span class=normal10>Cadastro de <b>$tipoPessoa[descricao]</b>.</span>";
		htmlFechaColuna();
		$texto = htmlMontaOpcao("<br>Adicionar", 'incluir');
		itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=novo", 'center', $corFundo, 0, 'normal');
		$texto = htmlMontaOpcao("<br>Procurar", 'procurar');
		itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=procurar", 'center', $corFundo, 0, 'normal');
		fechaLinhaTabela();
		fechaTabela();
	} else {
		# Topo da tabela - Informações e menu principal do Cadastro
		novaTabela2("[Cadastro de Pessoas]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 3);
		novaLinhaTabela($corFundo, '100%');
		htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
		echo "<br><img src=" . $html[imagem][cadastro] . " border=0 align=left><b class=bold>Pessoas</b>
										<br><span class=normal10>Cadastro de <b>Pessoas</b>. Este cadastro possibilita a criação de
										Clientes, Fornecedores, Parceiros, e outros, afim de utilização geral no sistema.</span>";
		htmlFechaColuna();
		$texto = htmlMontaOpcao("<br>Adicionar Clientes", 'incluir');
		itemLinha($texto, "?modulo=$modulo&sub=clientes&acao=adicionar", 'center', $corFundo, 0, 'normal');
		$texto = htmlMontaOpcao("<br>Adicionar Fornecedores", 'incluir');
		itemLinha($texto, "?modulo=$modulo&sub=fornecedores&acao=adicionar", 'center', $corFundo, 0, 'normal');
		fechaLinhaTabela();
		fechaTabela();
	}

	if (!$acao) {
		# Mostrar listagem
		echo "<br>";
		procurarPessoas($modulo, $sub, $acao, $registro, $matriz);
	}

	# Inclusão
	if ($acao == "adicionar") {
		echo "<br>";
		adicionarPessoas($modulo, $sub, $acao, $registro, $matriz);
	}
	if ($acao == "alterar") {
		echo "<br>";
		alterarPessoas($modulo, $sub, $acao, $registro, $matriz);
	}

	elseif ($acao == 'procurar') {
		echo "<br>";
		procurarPessoas($modulo, $sub, $acao, $registro, $matriz);
	}
	elseif ($acao == 'ver') {
		echo "<br>";
		verPessoas($modulo, $sub, $acao, $registro, $matriz);
	}
	elseif ($acao == 'documentos') {
		echo "<br>";
		documentosPessoas($modulo, $sub, $acao, $registro, $matriz);
	}
	elseif ($acao == 'enderecos') {
		echo "<br>";
		enderecosPessoas($modulo, $sub, $acao, $registro, $matriz);
	}
	elseif ($acao == 'enderecosvisualizar') {
		echo "<br>";
		verEnderecosPessoas($modulo, $sub, $acao, $registro, $matriz);
	}
	elseif ($acao == 'enderecosadicionar') {
		echo "<br>";
		adicionarEnderecosPessoas($modulo, $sub, $acao, $registro, $matriz);
	}
	elseif ($acao == 'enderecosalterar') {
		echo "<br>";
		alterarEnderecosPessoas($modulo, $sub, $acao, $registro, $matriz);
	}
	elseif ($acao == 'enderecosexcluir') {
		echo "<br>";
		excluirEnderecosPessoas($modulo, $sub, $acao, $registro, $matriz);
	}
	elseif ($acao == 'documentos') {
		echo "<br>";
		documentosPessoas($modulo, $sub, $acao, $registro, $matriz);
	}
	elseif ($acao == 'documentosadicionar') {
		echo "<br>";
		adicionarDocumentosPessoas($modulo, $sub, $acao, $registro, $matriz);
	}
	elseif ($acao == 'documentosexcluir') {
		echo "<br>";
		excluirDocumentosPessoas($modulo, $sub, $acao, $registro, $matriz);
	}
	elseif ($acao == 'excluir') {
		excluirPessoas($modulo, $sub, $acao, $registro, $matriz);
	}
	elseif (strstr($acao, 'impostos')) {
		impostosPessoas($modulo, $sub, $acao, $registro, $matriz);
	}
	elseif($acao == 'bloquear' || $acao == 'desbloquear'){
		bloqueioEmpresa($modulo, $sub, $acao);
		procurarPessoas($modulo, $sub, $acao, $registro, $matriz);	
	}

	echo "<script>location.href='#ancora';</script>";
}

# função para adicionar pessoa
function adicionarPessoas($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $sessLogin;

	# Permissão do usuario
	$permissao = buscaPermissaoUsuario($sessLogin[login], 'login', 'igual', 'login');

	if (!$permissao[admin] && !$permissao[adicionar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg = "ATENÇÃO: Você não tem permissão para executar esta função!";
		$url = "?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	} else {

		if (!$matriz[bntConfirmar]) {

			# Verificar tipo de Pessoa
			if ($sub == 'clientes')
				$matriz[tipoPessoa] = 'cli';
			elseif ($sub == 'fornecedores') $matriz[tipoPessoa] = 'for';
			elseif ($sub == 'condominios') $matriz[tipoPessoa] = 'cond';

			# Tipo de Pessoa
			$tipoPessoa = checkTipoPessoa($matriz[tipoPessoa]);

			# Motrar tabela de busca
			novaTabela2("[$tipoPessoa[descricao] - Adicionar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais Novo/Procurar
			menuOpcAdicional($modulo, $sub, $acao, $registro);
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto = "			
											<form method=post name=matriz action=index.php>
											<input type=hidden name=modulo value=$modulo>
											<input type=hidden name=sub value=$sub>
											<input type=hidden name=matriz[tipoPessoa] value=$matriz[tipoPessoa]>
											<input type=hidden name=acao value=$acao>&nbsp;";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();

			# Mostrar seleção de tipo de pessoa
			formPessoaTipoPessoa($modulo, $sub, $acao, $registro, $matriz);

			if ($matriz[tipoPessoa] && $matriz[nome]) {
				# Espaço
				itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');

				# Dados Cadastrais
				if ($matriz[pessoaTipo] == 'F' || $matriz[pessoaTipo] == 'J') {

					// Linha repetida comentada - João Petrelli - 11/01/10
//					if ($matriz[pessoaTipo] == 'F' || $matriz[pessoaTipo] == 'J') {

						formPessoaDadosCadastrais($modulo, $sub, $acao, $registro, $matriz);

						$parametros = carregaParametrosConfig();

						# Endereço
						if ((($matriz[pessoaTipo] == 'F' && validaCPF($matriz[cpf]) && ($parametros[documento_unico] == 'N' || contaConsulta(buscaDocumentosPessoas(validaCPF($matriz[cpf]), 'documento', 'igual', 'documento')) == 0)) || ($matriz[pessoaTipo] == 'J' && validaCNPJ($matriz[cnpj]) && ($parametros[documento_unico] == 'N' || contaConsulta(buscaDocumentosPessoas(validaCNPJ($matriz[cnpj]), 'documento', 'igual', 'documento')) == 0)))) {
							formPessoaEndereco($modulo, $sub, $acao, $registro, $matriz);
							
							if ($matriz[uf] && $matriz[cidade]) {
								// Adiciona campo para adição de intruções personalizada por cliente.
								formInstrucaoBoleto($modulo,$sub,$acao,$registro,$matriz);
								
								if ($matriz[instrucaoBoleto]) {
									# Submit dos valores Botão Confirmar Dados
									formPessoaSubmit($modulo, $sub, $acao, $registro, $matriz);
								}
							}
							
						} else {
							if ((strlen(trim($matriz[cpf])) > 0 || strlen(trim($matriz[cnpj])) > 0)) {
								# Documento Já cadastrado!
								novaLinhaTabela($corFundo, '100%');
								htmlAbreColuna('100%', 'center', $corFundo, 2, 'tabfundo1');
								echo "<br>";
								echo "<span class=txtaviso>Documento inválido!</span><br><br>";

								# Visualizar pessoa
								if ($matriz[pessoaTipo] == 'F')
									$matriz[documento] = validaCPF($matriz[cpf]);
								elseif ($matriz[pessoaTipo] == 'J') $matriz[documento] = validaCNPJ($matriz[cnpj]);
								$consulta = buscaDocumentosPessoas($matriz[documento], 'documento', 'igual', 'documento');

								$parametros = carregaParametrosConfig();

								if (($consulta && contaConsulta($consulta) > 0) && $parametros[documento_unico] == 'S') {
									# visualizar dados
									$idPessoa = resultadoSQL($consulta, 0, 'idPessoa');
									$consultaPessoaTipo = buscaPessoasTipos($idPessoa, 'idPessoa', 'igual', 'idPessoa');

									if ($consultaPessoaTipo && contaConsulta($consultaPessoaTipo) > 0) {
										$idPessoaTipo = resultadoSQL($consultaPessoaTipo, 0, 'id');
										verPessoas('cadastros', 'clientes', 'ver', "$idPessoaTipo:$idPessoa", $matriz);
									}
								}
								htmlFechaColuna();
								fechaLinhaTabela();
							}
						}
//					}
				}

			}

			htmlFechaLinha();
			fechaTabela();
		} else {
			# Motrar tabela

			# Tipo de Pessoa
			$tipoPessoa = checkTipoPessoa($matriz[tipoPessoa]);

			//novaTabela2("[$tipoPessoa[descricao] - Confirmação de Cadastro]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			//	# Opcoes Adicionais
			//	menuOpcAdicional($modulo, $sub, $acao, $registro);		
			//	itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');

			//	novaLinhaTabela($corFundo, '100%');
			//		itemLinhaTMNOURL('<b>Nome:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			//		itemLinhaForm($matriz[nome], 'left', 'top', $corFundo, 0, 'tabfundo1');
			//	fechaLinhaTabela();			
			//	novaLinhaTabela($corFundo, '100%');
			//		itemLinhaTMNOURL('<b>Tipo de Pessoa:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			//		itemLinhaForm(formSelectPessoaTipo($matriz[pessoaTipo],'','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			//	fechaLinhaTabela();

			//				if( ($matriz[pessoaTipo]=='F' && $matriz[nome] && $matriz[cpf] && $matriz[endereco] && $matriz[cidade] && $matriz[fone1]) 
			//					|| ($matriz[pessoaTipo]=='J' && $matriz[nome] && $matriz[razao] && $matriz[cnpj] ) ) {
			if (validaDadosPessoa($matriz)) {
				# Gravar
				$data = dataSistema();

				# Buscar ID para Novo Cadastro
				$matriz[id] = buscaIDNovoPessoa();
				$matriz[dtCadastro] = $data[dataBanco];

				if (!$matriz[id]) {
					# ERRO - Pessoa nova impossível de ser criada
					$msg = "Ocorreu um erro na tentativa de buscar novo codigo para cadastro! - ID";
					$url = "?modulo=$modulo&sub=$sub&acao=$acao";
					aviso("Aviso", $msg, $url, 760);
				} else {

					# Incluir Pessoa
					$gravaPessoa = dbPessoa($matriz, 'incluir');

					if ($gravaPessoa) {

						$matriz[idPessoaTipo] = buscaIDNovoPessoaTipo();
						$matriz[idPessoa] = $matriz[id];

						# Incluir Pessoa Tipo
						$gravaPessoaTipo = dbPessoaTipo($matriz, 'incluir');

						# Verificar se PessoaTipo foi gravado
						if ($gravaPessoaTipo) {
							# Gravar Endereços
							$gravaEndereco = dbEndereco($matriz, 'incluir');

							if ($gravaEndereco) {
								# OK continuar
								# Incluir Documentos PessoaTipo
								$gravaDocumentos = dbDocumento($matriz, 'incluir');

								if (!$gravaDocumentos) {
									# Excluir pessoa
									dbPessoa($matriz, 'excluir');
									$msg = "Ocorreu um erro na tentativa de gravar o Cadastro de $tipoPessoa[descricao]! - Documentos";
									$url = "?modulo=$modulo&sub=$sub&acao=$acao";
									aviso("Aviso", $msg, $url, 600);
								} else {
									# Confirmação de cadastro
									$msg = "Cadastro efetuado com sucesso!";

									# Verificar se cadastro é de cliente
									if ($matriz[tipoPessoa] == 'cli') {
										# Mostrar planos da pessoa
										$msg = "Cliente cadastrado com sucesso!<br>Adicione os planos do cliente utilizando a tela abaixo!";
										avisoNOURL("Aviso", $msg, 400, 2, 0, 'center');
										echo "<br>";

										listarPlanos('lancamentos', 'planos', 'listar', $matriz[idPessoaTipo], $matriz);
									} else {
										avisoNOURL("Aviso", $msg, 400, 2, 0, 'center');
										echo "<br>";
									}
								}
							} else {
								# Excluir pessoa
								dbPessoa($matriz, 'excluir');
								$msg = "Ocorreu um erro na tentativa de gravar o Cadastro de $tipoPessoa[descricao]! - Endereço";
								$url = "?modulo=$modulo&sub=$sub&acao=$acao";
								aviso("Aviso", $msg, $url, 760);
							}
						} else {
							# Excluir pessoa
							dbPessoa($matriz, 'excluir');
							$msg = "Ocorreu um erro na tentativa de gravar o Cadastro de $tipoPessoa[descricao]! - Pessoa Tipo";
							$url = "?modulo=$modulo&sub=$sub&acao=$acao";
							aviso("Aviso", $msg, $url, 760);
						}

					} else {
						# ERRO - Pessoa nova impossível de ser criada
						$msg = "Ocorreu um erro na tentativa de gravar o Cadastro de $tipoPessoa[descricao]! - Pessoa";
						$url = "?modulo=$modulo&sub=$sub&acao=$acao";
						aviso("Aviso", $msg, $url, 760);
					}

				}
			} else {
				# Falta de parâmetros
				# Mensagem de aviso
				$msg = "Campos obrigatórios não preenchidos!<br> Preencha todos os campos antes de prosseguir com o cadastro! ";
				$url = "?modulo=$modulo&sub=$sub&acao=$acao";
				aviso("Aviso", $msg, $url, 760);
			}

			//fechaTabela();
		}
	} //tem permissao
}

# Função para alteração de dados da Pessoa - apenas cadastro
function alterarPessoas($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $tb, $sessCadastro, $sessLogin;
	/* Por Felipe Assis 28/01/2008
	   Implementação das rotinas de alteração do usuário desde o formulário de alteração
	   até a validação e alteração dos dados no banco
	*/
	
	$sessLogin = $_SESSION['sessLogin'];
	
	# Validando usuário
	$permissao = buscaPermissaoUsuario($sessLogin[login]);
	
	if(!$permissao['admin'] && !$permissao['visualizar']){
		$msg = "ATENÇÃO: Você não tem permissão para para executar esta função.";
		$url = "?modulo=$modulo&sub=$sub;";
		aviso("Acesso negado", $msg, $url, 400);
	}
	else{
		if($registro && !$matriz['bntConfirmar']){
			# Quebra de string com os registros passados
			$tmpRegistro = explode(":", $registro);
			$idPessoaTipo = $tmpRegistro[0];
			$idPessoa = $tmpRegistro[1];
			
			# Consultando registro da Pessoa
			$consulta = buscaPessoas($idPessoa, "$tb[Pessoas].id", 'igual', 'id');
			
			if(!$consulta || contaConsulta($consulta) == 0){
				# Exbir erro
				$msg = "Registro não encontrado!";
				$url = "?modulo=$modulo&sub=$sub";
				aviso("Aviso", $msg, $url, 760);
			}
			else{
				# Atribuindo tuplas do registro
				$nome = resultadoSQL($consulta, 0, 'nome');
				$razao = resultadoSQL($consulta, 0, 'razao');
				$tipoPessoa = resultadoSQL($consulta, 0, 'tipoPessoa');
				$site = resultadoSQL($consulta, 0, 'site');
				$mail = resultadoSQL($consulta, 0, 'mail');
				$dtNascimento = resultadoSQL($consulta, 0, 'dtNascimento');
				$dtCadastro = resultadoSQL($consulta, 0, 'dtCadastro');
				$idPOP = resultadoSQL($consulta, 0, 'idPOP');
				$contato = resultadoSQL($consulta, 0, 'contato');
				$instrucaoBoleto = resultadoSQL($consulta, 0, 'instrucaoBoleto');
				
				if(!$matriz['bntConfirmar']){
					$matriz['nome'] = $nome;
					$matriz['razao'] = $razao;
					$matriz['tipoPessoa'] = $tipoPessoa;
					$matriz['site'] = $site;
					$matriz['mail'] = $mail;
					$matriz['dtNascimento'] = $dtNascimento;
					$matriz['dtCadastro'] = $dtCadastro;
					$matriz['idPOP'] = $idPOP;
					$matriz['contato'] = $contato;
					$matriz['instrucaoBoleto'] = $instrucaoBoleto;
				}
				
				# Mostrar tabela de Alteração do registro
				novaTabela2("[Alterar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
					# Opcoes Adicionais
					menuOpcAdicional($modulo, $sub, $acao, "$idPessoaTipo:$idPessoa");
					#fim das opções adicionais
					novaLinhaTabela($corFundo, '100%');
						$texto = "" .
								 "<form method='post' name='matriz' action='index.php'>"  .
								 "<input type='hidden' name='modulo' value='$modulo'>" .
								 "<input type='hidden' name='sub' value='$sub'>" .
								 "<input type='hidden' name='registro' value='$registro'>" .
								 "<input type='hidden' name='matriz[id]' value='$idPessoa'>" .
								 "<input type='hidden' name='matriz[idPessoaTipo]' value='$idPessoaTipo'>" .
								 "<input type='hidden' name='acao' value='$acao'>&nbsp;";
						itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('30%', 'right', $corFundo, 0, 'tabfundo1');
							echo "Nome:";
						htmlFechaColuna();
						$texto="<input type=text name=matriz[nome] size=60 value='$nome'>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					if($tipoPessoa){						
						novaLinhaTabela($corFundo, '100%');
							htmlAbreColuna('30%', 'right', $corFundo, 0, 'tabfundo1');
								echo "Tipo Pessoa:";
							htmlFechaColuna();
							itemLinhaForm(formSelectPessoaTipo($tipoPessoa,'tipo','form_alt'), 'left', 'top', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
					}
					if(strtoupper($tipoPessoa) == 'F'){
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL('<b>Data de Nascimento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
							$texto="<input type=text name=matriz[dtNascimento] size=10 value='$matriz[dtNascimento]' onBlur=verificaData(this.value,7)> <span class=txtaviso>(Ex: 01/03/1983 ou 01031983)</span>";
							itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
					}
					elseif(strtoupper($tipoPessoa) == 'J'){
						novaLinhaTabela($corFundo, '100%');
							htmlAbreColuna('30%', 'right', $corFundo, 0, 'tabfundo1');
								echo "Razão Social:";
							htmlFechaColuna();
							$texto="<input type=text name=matriz[razao] size=60 value='$razao'>";
							itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
					}
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>E-Mail:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
						$texto="<input type=text name=matriz[mail] size=40 value='$matriz[mail]'> <span class=txtaviso>(Ex: joao@tdkom.com.br)</span>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>Site:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
						$texto="<input type=text name=matriz[site] size=40 value='$matriz[site]'  onBlur=verificaURL(this.value,9)> <span class=txtaviso>(Ex: http://www.tdkom.com.br)</span>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					# Seleção do POP
					
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>POP de Acesso:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
						itemLinhaForm(formSelectPOP($idPOP,'pop','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>Contato:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
						$texto="<input type=text name=matriz[contato] size=60 value='$matriz[contato]'>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					
					/*
					 * Por João Petrelli - 13/01/2010
					 * Adicionado campo de instruções do boleto ao alterar o cadastro de um cliente.
					 * */
					novaLinhaTabela($corFundo, '100%');
						$descricao = "<b>Instrução do Boleto:  <br>(Max 4 linhas ou 280 caracteres)</b><br><FONT SIZE=1>Esta instrução será adicionada no boleto bancário ao gerar o faturamento.</FONT>";
						itemLinhaTMNOURL($descricao, 'right', 'TOP', '30%', $corFundo, 0, 'tabfundo1');
						$texto="<TEXTAREA name=matriz[instrucaoBoleto] COLS=50 ROWS=5>$matriz[instrucaoBoleto]</TEXTAREA>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('30%', 'right', $corFundo, 0, 'tabfundo1');
							echo "&nbsp;";
						htmlFechaColuna();
						$texto="<input type=submit name=matriz[bntConfirmar] value=Alterar class=submit>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				fechaTabela();
			} # Fecha tabela de alteração
		} # fecha formulário
		elseif($matriz['bntConfirmar']){ // executa quando o botão "Alterar" é pressionado
			# tipoPessoa recebe valor novamente por ter seu valor alterado ao confirmar alteração
			$matriz['tipoPessoa'] = $matriz['tipo'];
			
			#Validação dos campos
			if($matriz['nome']){
				$grava = dbPessoa($matriz, 'alterar');
				
				# Verifica se o registro foi gravado
				if($grava){
					$msg = "Registro gravado com sucesso!";
					avisoNOURL("Aviso:", $msg, 400);
					echo "<br>";
					verPessoas($modulo, $sub, 'ver', "$matriz[idPessoaTipo]:$matriz[id]", $matriz);
					$sesCadastro='';
				}
			}
			else{
				$msg="Falta de parâmetros necessários. Informe os campos obrigatórios e tente novamente";
				$url = "?modulo=$modulo&sub=$sub&acao=$acao";
				aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
			}
		} # encerra validação do botão de confirmação
	}
}

# Função para buscar o NOVO ID da Pessoa
function buscaIDNovoPessoa() {

	global $conn, $tb;

	$sql = "SELECT count(id) qtde from $tb[Pessoas]";
	$consulta = consultaSQL($sql, $conn);

	if ($consulta && resultadoSQL($consulta, 0, 'qtde') > 0) {

		$sql = "SELECT MAX(id)+1 id from $tb[Pessoas]";
		$consulta = consultaSQL($sql, $conn);

		if ($consulta && contaConsulta($consulta) > 0) {
			$retorno = resultadoSQL($consulta, 0, 'id');
			if (!is_numeric($retorno))
				$retorno = 1;
		} else
			$retorno = 1;
	} else {
		$retorno = resultadoSQL($consulta, 0, 'qtde') + 1;
	}

	return ($retorno);
}

function dbPessoa($matriz, $tipo, $registro = '') {
	# Função de banco de dados - Pessoas

	global $conn, $tb, $modulo, $sub, $acao;

	# Sql de inclusão
	if ($tipo == 'incluir' || $tipo == 'inserir') {

		if ($matriz[id]) {

			$tipoPessoa = checkTipoPessoa($matriz[tipoPessoa]);
			if ($matriz[dtNascimento])
				$dataNascimento = converteData($matriz[dtNascimento], 'form', 'bancodata');

			$sql = "INSERT INTO $tb[Pessoas] VALUES ($matriz[id],
									'$matriz[pessoaTipo]',
									'$matriz[nome]',
									'$matriz[razao]',
									'$matriz[site]',
									'$matriz[email]',
									'$dataNascimento',
									'$matriz[dtCadastro]',
									'$matriz[pop]',
									'$matriz[contato]',
									'$matriz[instrucaoBoleto]')";
		}
	} #fecha inclusao

	# Alterar
	elseif ($tipo == 'alterar') {

		if ($matriz[dtNascimento])
			$dataNascimento = converteData($matriz[dtNascimento], 'form', 'bancodata');

		$sql = "
							UPDATE $tb[Pessoas] SET
								tipoPessoa='$matriz[tipoPessoa]',
								nome='$matriz[nome]',
								razao='$matriz[razao]',
								site='$matriz[site]',
								mail='$matriz[mail]',
								dtNascimento='$dataNascimento',
								idPOP='$matriz[pop]',
								contato='$matriz[contato]',
								instrucaoBoleto='$matriz[instrucaoBoleto]'
							WHERE
								id=$matriz[id]";
	}

	elseif ($tipo == 'excluir') {
		$sql = "DELETE FROM $tb[Pessoas] WHERE id=$matriz[id]";
	}
	elseif ($tipo == 'tipoPessoa') {
		$sql = "SELECT $tb[Pessoas].id, $tb[TipoPessoas].descricao, $tb[PessoasTipos].id
								FROM $tb[TipoPessoas] 
								INNER JOIN $tb[PessoasTipos] 
								ON ($tb[TipoPessoas].id = $tb[PessoasTipos].idTipo) 
								LEFT JOIN Pessoas 
								ON ($tb[PessoasTipos].idPessoa = $tb[Pessoas].id) 
								WHERE $tb[TipoPessoas].descricao = 'Clientes' AND 
								$tb[Pessoas].id = $registro";
	}
	elseif ($tipo == 'idPessoa') {
		/* SELECT Pessoas.id, Pessoas.nome FROM Pessoas 
		 * INNER JOIN PessoasTipos 
		 * ON (Pessoas.id = PessoasTipos.idPessoa) 
		 * WHERE PessoasTipos.id = 8; */
		$sql = "SELECT $tb[Pessoas].id FROM $tb[Pessoas] " .
			"INNER JOIN PessoasTipos " .
			"ON ($tb[Pessoas].id = $tb[PessoasTipos].idPessoa) " .
			"WHERE PessoasTipos.id = $matriz[registro]";
	}

	if ($sql) {
		$retorno = consultaSQL($sql, $conn);
		return ($retorno);
	}
}

# Função para procura de serviço
function procurarPessoas($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $tb, $corFundo, $corBorda, $html, $limite, $sessCadastro;

	# Tipo de Pessoa
	$tipoPessoa = checkIDTipoPessoa($matriz[idTipoPessoa]);

	# Motrar tabela de busca
	novaTabela2("[Procurar Pessoas]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	novaLinhaTabela($corFundo, '100%');
	//			htmlAbreColuna('20%', 'right', $corFundo, 0, 'tabfundo1');
	//			echo "<b>Procurar por:</b>";
	//			htmlFechaColuna();
	$texto = "
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=procurar>
					<input type=hidden name=matriz[tipoPessoa]=$tipoPessoa[valor]>
					<b>Procurar por:</b><input type=text name=matriz[txtProcurar] size=40>
					&nbsp;&nbsp;<b>Tipo Pessoa:</b>&nbsp;";

	# Listagem de tipos de pessoas
	$texto .= formSelectTipoPessoa($tipoPessoa[valor], 'idTipoPessoa', 'form');

	$texto .= "&nbsp;<input type=submit name=matriz[bntProcurar] value=Procurar class=submit>";
	itemLinhaForm($texto, 'center', 'middle', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
	fechaTabela();

	//alterado em 08/05/07 by Lis exibir todos os registros se não for preenchido campo de pesquisa
	# Caso botão procurar seja pressionado
	if ($matriz[bntProcurar]) { //if($matriz[txtProcurar])
		#buscar registros
		# Verificar ID do tipo de pessoa informada (cli, ban, for ...)
		if ($tipoPessoa && $matriz[idTipoPessoa] != '*') {
			$consulta = buscaPessoas("((upper(nome) like '%$matriz[txtProcurar]%' OR upper(razao) like '%$matriz[txtProcurar]%' OR upper(site) like '%$matriz[txtProcurar]%' OR upper(mail) like '%$matriz[txtProcurar]%') OR upper(contato) like '%$matriz[txtProcurar]%') AND idTipo=$tipoPessoa[id]", $campo, 'custom', 'nome');
		} else {
			$consulta = buscaPessoas("((upper(nome) like '%$matriz[txtProcurar]%' OR upper(razao) like '%$matriz[txtProcurar]%' OR upper(site) like '%$matriz[txtProcurar]%' OR upper(mail) like '%$matriz[txtProcurar]%') OR upper(contato) like '%$matriz[txtProcurar]%')", $campo, 'custom', 'nome');
		}

		echo "<br>";

		novaTabela("[Resultados]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 4);

		if (!$consulta || contaConsulta($consulta) == 0) {
			# Não há registros
			itemTabelaNOURL('Não foram encontrados registros cadastrados', 'left', $corFundo, 4, 'txtaviso');
		}
		elseif ($consulta && contaConsulta($consulta) > 0 && (!$registro || is_numeric($registro))) {

			itemTabelaNOURL('Registros encontrados procurando por (' . $matriz[txtProcurar] . '): ' . contaConsulta($consulta) . ' registro(s)', 'left', $corFundo, 4, 'txtaviso');

			# Paginador
			$urlADD = "&matriz[txtProcurar]=" . $matriz[txtProcurar] . "&matriz[bntProcurar]=1";
			paginador($consulta, contaConsulta($consulta), $limite[lista][pessoas], $registro, 'normal', 4, $urlADD);

			# Cabeçalho
			novaLinhaTabela($corFundo, '100%');
			itemLinhaTabela('Nome', 'center', '35%', 'tabfundo0');
			itemLinhaTabela('Tipo de Pessoa', 'center', '15%', 'tabfundo0');
			itemLinhaTabela('Tipo', 'center nowrap', '10%', 'tabfundo0');
			itemLinhaTabela('Opções', 'center', '40%', 'tabfundo0');
			fechaLinhaTabela();

			# Setar registro inicial
			if (!$registro) {
				$i = 0;
			}
			elseif ($registro && is_numeric($registro)) {
				$i = $registro;
			} else {
				$i = 0;
			}

			$limite = $i + $limite[lista][pessoas];

			while ($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id = resultadoSQL($consulta, $i, 'id');
				$pessoaTipo = resultadoSQL($consulta, $i, 'tipoPessoa');
				$idPessoaTipo = resultadoSQL($consulta, $i, 'idPessoaTipo');
				$nome = resultadoSQL($consulta, $i, 'nome');
				$idTipo = resultadoSQL($consulta, $i, 'idTipo');
				$tmpCheckPessoa = checkIDTipoPessoa($idTipo);

				//Caso o usuario estiver com todos os servicos cancelados, exibe um aviso
				$aviso = "";
				if (alertaCancelamento($id))
					$aviso = "<img src= " . $html[imagem][cancelar] . ">";

				# Checar tipo de pessoa
				if ($tmpCheckPessoa[valor] == 'cli')
					$sub = 'clientes';
				elseif ($tmpCheckPessoa[valor] == 'for') $sub = 'fornecedores';
				elseif ($tmpCheckPessoa[valor] == 'pop') $sub = 'pop';
				elseif ($tmpCheckPessoa[valor] == 'cond') $sub = 'condominios';

				$opcoes = htmlMontaOpcao("<a href=?modulo=cadastros&sub=$sub&acao=enderecos&registro=$idPessoaTipo:$id>Endereços</a>", 'endereco');
				if ($tmpCheckPessoa[valor] == 'cli') {
					$opcoes .= htmlMontaOpcao("<a href=?modulo=lancamentos&sub=planos&acao=listar&registro=$idPessoaTipo>Planos</a>", 'planos');
					$opcoes .= htmlMontaOpcao("<a href=?modulo=faturamento&sub=clientes&acao=historico&registro=0&matriz[idPessoaTipo]=$idPessoaTipo>Financeiro</a>", 'financeiro');
					$opcoes .= htmlMontaOpcao("<a href=?modulo=administracao&sub=limites&registro=$idPessoaTipo>Administração</a>", 'config');
					$opcoes .= htmlMontaOpcao("<a href=?modulo=ocorrencias&sub&registro=$idPessoaTipo>Ocorrências</a>", 'ocorrencia');
					$opcoes .= "<br>";
					$opcoes .= htmlMontaOpcao("<a href=?modulo=cadastros&sub=clientes&acao=ver&registro=$idPessoaTipo:$id>Cadastro</a>", 'ver');
					$opcoes .= htmlMontaOpcao("<a href=?modulo=cadastros&sub=$sub&acao=documentos&registro=$idPessoaTipo:$id>Documentos</a>", 'documento');
					$opcoes .= htmlMontaOpcao("<a href=?modulo=contratos&acao=listar&registro=$idPessoaTipo:$id>Contratos</a>", 'contrato');
					$opcoes .= htmlMontaOpcao("<a href=?modulo=lancamentos&sub=planos&acao=ativarInativarClienteServico&registro=$idPessoaTipo>Ativar/Inativar</a>", 'ativar');

					
					//opção para o bloquear Empresas no Ticket by Felipe Assis (30/10/2007)
					$parametros = carregaParametrosConfig();
					// Verifica se o ISP está configurado para se integrar ao Ticket-IT
					if (strtoupper($parametros['integrarTicket']) == 'S') {
						/*A consulta abaixo retorna o id do módulo, caso o mesmo
						 esteja vinculado à um serviço
						*/
						
						$conModulos = buscaModulos($idPessoaTipo, '', 'buscarModuloCliente', '');
						$resultados = mysql_num_rows($conModulos);
						if($resultados > 0){ // se o cliente tem módulos vinculados
							// selecionado possuem parâmetros 
							$opcoes .= opcaoBloquearCliente($modulo, $sub, $acao, $matriz, $idPessoaTipo, $id);
						}
						
					}
				}

				# Procurar Ocorrências da Pessoa
				$ocorrencias = ocorrenciasPessoasStatus($idPessoaTipo, 'A');
				if ($ocorrencias) {
					$nome = "<a href=?modulo=ocorrencias&registro=$idPessoaTipo alt='Ocorrencias em aberto'><img src=" . $html[imagem][ocorrencia] . " border=0 align=right></a>" . $nome;
				}

				novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela($nome . "  " . $aviso, 'left', '35%', 'normal10');
				itemLinhaTabela(formSelectPessoaTipo($pessoaTipo, '', 'check'), 'center nowrap', '15%', 'normal10');
				itemLinhaTabela(formSelectTipoPessoa($idTipo, '', 'check'), 'center nowrap', '10%', 'normal10');
				itemLinhaTabela($opcoes, 'left nowrap', '40%', 'normal8');
				fechaLinhaTabela();

				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem

		# Zerar pesquisa
		$sessCadastro[txtProcurar] = '';
		$sessCadastro[bntProcurar] = 0;

		fechaTabela();
	} # fecha botão procurar
} #fecha funcao de  procurar 

# função de busca 
function buscaPessoas($texto, $campo, $tipo, $ordem) {
	global $conn, $tb, $corFundo, $modulo, $sub;

	if ($tipo == 'todos') {
		$sql = "SELECT 
							$tb[Pessoas].*, 
							$tb[PessoasTipos].id idPessoaTipo,
							$tb[PessoasTipos].idPessoa, 
							$tb[PessoasTipos].idTipo,
							$tb[PessoasTipos].dtCadastro 
						FROM 
							$tb[Pessoas], 
							$tb[PessoasTipos] 
						WHERE 
							$tb[Pessoas].id = $tb[PessoasTipos].idPessoa 
						ORDER BY $ordem";
	}
	elseif ($tipo == 'contem') {
		$sql = "SELECT 
							$tb[Pessoas].*, 
							$tb[PessoasTipos].id idPessoaTipo,
							$tb[PessoasTipos].idPessoa, 
							$tb[PessoasTipos].idTipo, 
							$tb[PessoasTipos].dtCadastro 
						FROM 
							$tb[Pessoas], 
							$tb[PessoasTipos] 
						WHERE 
							$tb[Pessoas].id = $tb[PessoasTipos].idPessoa 
							AND $tb[Pessoas].$campo LIKE '%$texto%'
						ORDER BY $ordem";
	}
	elseif ($tipo == 'igual') {
		$sql = "SELECT 
							$tb[Pessoas].*, 
							$tb[PessoasTipos].id idPessoaTipo,
							$tb[PessoasTipos].idPessoa idPessoa,
							$tb[PessoasTipos].idTipo idTipo, 
							$tb[PessoasTipos].dtCadastro 
						FROM 
							$tb[Pessoas], 
							$tb[PessoasTipos] 
						WHERE 
							$tb[Pessoas].id = $tb[PessoasTipos].idPessoa 
							AND $campo = '$texto'
						ORDER BY $ordem";
	}
	elseif ($tipo == 'custom') {
		$sql = "SELECT 
							$tb[Pessoas].*, 
							$tb[PessoasTipos].id idPessoaTipo,
							$tb[PessoasTipos].idPessoa, 
							$tb[PessoasTipos].idTipo, 
							$tb[PessoasTipos].dtCadastro 
						FROM 
							$tb[Pessoas], 
							$tb[PessoasTipos] 
						WHERE 
							$tb[Pessoas].id = $tb[PessoasTipos].idPessoa 
							AND $texto
						GROUP BY $tb[Pessoas].id
						ORDER BY $ordem";
	}

	# Verifica consulta
	if ($sql) {
		$consulta = consultaSQL($sql, $conn);
		# Retornvar consulta
		return ($consulta);
	} else {
		# Mensagem de aviso
		$msg = "Consulta não pode ser realizada por falta de parâmetros";
		$url = "?modulo=$modulo&sub=$sub";
		aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
	}

} # fecha função de busca

/**
 * @return void
 * @param unknown $modulo
 * @param unknown $sub
 * @param unknown $acao
 * @param unknown $registro
 * @param unknown $matriz
 * @desc Exibe o objeto PESSOA
*/
function verPessoas($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb;

	# Permissão do usuario
	$permissao = buscaPermissaoUsuario($sessLogin[login], 'login', 'igual', 'login');

	if (!$permissao[visualizar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg = "ATENÇÃO: Você não tem permissão para executar esta função";
		$url = "?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	} else {

		# Quebrar registro
		$tmpRegistro = explode(":", $registro);
		$idPessoaTipo = $tmpRegistro[0];
		$idPessoa = $tmpRegistro[1];

		# Procurar dados
		$consulta = buscaPessoas($idPessoaTipo, "$tb[PessoasTipos].id", 'igual', 'nome');

		if ($consulta && contaConsulta($consulta) > 0) {

			$idPessoa = resultadoSQL($consulta, 0, 'id');
			$nome = resultadoSQL($consulta, 0, 'nome');
			$idTipo = resultadoSQL($consulta, 0, 'idTipo');
			$tipoPessoa = resultadoSQL($consulta, 0, 'tipoPessoa');
			$razao = resultadoSQL($consulta, 0, 'razao');
			$site = resultadoSQL($consulta, 0, 'site');
			$mail = resultadoSQL($consulta, 0, 'mail');
			$dtNascimento = resultadoSQL($consulta, 0, 'dtNascimento');
			$dtCadastro = resultadoSQL($consulta, 0, 'dtCadastro');
			$pessoaTipo = resultadoSQL($consulta, 0, 'idTipo');
			$idPOP = resultadoSQL($consulta, 0, 'idPOP');
			$contato = resultadoSQL($consulta, 0, 'contato');

			# Selecionar PessoaTipo para identificar o Tipo de Pessoa (Cliente, Fornecedor, Pop, Banco, etc)
			$checkTipo = checkTipoPessoa($idTipo);

			# Tipo de Pessoa
			$pessoaTipo = checkIDTipoPessoa($idTipo);
			
			/* Exibe uma mensagem de confirmação caso o cliente tenha sido 
			 * bloqueado ou desbloqueado
			*/
			
			$acaoBloqueio = $_REQUEST['acaoBloqueio'];
			$_REQUEST['idPessoaTipo'] = $_REQUEST['registro'];
			if($acaoBloqueio == 'bloquear' || $acaoBloqueio == 'desbloquear'){
				bloqueioEmpresa($modulo, $sub, $acaoBloqueio);
			}
			elseif($acaoBloqueio == 'bloquearSuporte' || $acaoBloqueio == 'desbloquearSuporte'){
				$idPessoaTipo = $_REQUEST['registro'];
				bloqueioSuporte($modulo, $sub, $acao);
			}

			# Motrar tabela de busca
			novaTabela2("[$pessoaTipo[descricao] - Visualização]", "center", '100%', 0, 2, 1, 'tabfundo1', $corBorda, 2);
			# Opcoes Adicionais

			menuOpcAdicional($modulo, $sub, $acao, "$idPessoaTipo:$idPessoa");
			#fim das opcoes adicionais
			itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
			
			novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
			echo "<b class=bold10>Nome: </b>";
			htmlFechaColuna();
			itemLinhaForm($nome, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			if ($tipoPessoa) {
				novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b class=bold10>Tipo Pessoa: </b>";
				htmlFechaColuna();
				itemLinhaForm(formSelectPessoaTipo($tipoPessoa, '', 'check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			}

			if ($tipoPessoa == 'F') {
				# Pessoa física
				if ($dtNascimento > 0) {
					novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Data Nascimento: </b>";
					htmlFechaColuna();
					itemLinhaForm(converteData($dtNascimento, 'banco', 'formdata'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
			}
			elseif ($tipoPessoa == 'J') {
				# Pessoa Jurídica
				if ($dtNascimento) {
					novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Razão Social: </b>";
					htmlFechaColuna();
					itemLinhaForm($razao, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
			}
			if ($site) {
				novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b class=bold10>Site: </b>";
				htmlFechaColuna();
				itemLinhaForm("$site ", 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			}
			if ($mail) {
				novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b class=bold10>E-mail: </b>";
				htmlFechaColuna();
				itemLinhaForm("$mail ", 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			}

			if ($idPOP) {
				novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b class=bold10>POP de Acesso: </b>";
				htmlFechaColuna();
				itemLinhaForm(formSelectPOP($idPOP, '', 'check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			}

			if ($contato) {
				novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b class=bold10>Contato: </b>";
				htmlFechaColuna();
				itemLinhaForm("$contato", 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			}

			fechaTabela();

		}
	}
}

/**
 * @return array
 * @param int $id
 * @desc retorna um array com todos os campos de pessoas
*/
function dadosPessoas($id) {

	global $tb;

	$consulta = buscaPessoas($id, "$tb[Pessoas].id", 'igual', 'id');

	if ($consulta && contaConsulta($consulta) > 0) {
		$retorno[id] = resultadoSQL($consulta, 0, 'id');
		$retorno[tipoPessoa] = resultadoSQL($consulta, 0, 'tipoPessoa');
		$retorno[nome] = resultadoSQL($consulta, 0, 'nome');
		$retorno[razao] = resultadoSQL($consulta, 0, 'razao');
		$retorno[site] = resultadoSQL($consulta, 0, 'site');
		$retorno[email] = resultadoSQL($consulta, 0, 'mail');
		$retorno[dtNascimento] = resultadoSQL($consulta, 0, 'dtNascimento');
		$retorno[dtCadastro] = resultadoSQL($consulta, 0, 'dtCadastro');
		$retorno[idPOP] = resultadoSQL($consulta, 0, 'idPOP');
		$retorno[instrucaoBoleto] = resultadoSQL($consulta, 0, 'instrucaoBoleto');
	}

	return ($retorno);
}

// verifica se a "pessoa" esta com todos seu planos CANCELADOS
function alertaCancelamento($id) {
	global $conn, $tb;
	$sql = "SELECT 
								$tb[Pessoas].nome, 
								$tb[ServicosPlanos].id, 
								$tb[StatusServicos].status 
							FROM 
								Pessoas 
							INNER JOIN PessoasTipos ON 
								(Pessoas.id = PessoasTipos.idPessoa) 
							LEFT JOIN PlanosPessoas ON 
								(PessoasTipos.id = PlanosPessoas.idPessoaTipo) 
							LEFT JOIN ServicosPlanos ON 
								(PlanosPessoas.id = ServicosPlanos.idPlano ) 
							LEFT JOIN StatusServicosPlanos ON 
								(ServicosPlanos.idStatus = StatusServicosPlanos.id)
						  WHERE
								 Pessoas.id = $id";
	$cons = consultaSQL($sql, $conn);
	$i = 0;
	while ($i < contaConsulta($cons)) {
		if (is_null(resultadoSQL($cons, $i, 'status')) || resultadoSQL($cons, $i, 'status') != "C")
			return (0);
		$i++;
	}
	return (1);
}

//
function excluirPessoas($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $corFundo, $corBorda, $html, $sessLogin, $tb;

	# Permissão do usuario
	$permissao = buscaPermissaoUsuario($sessLogin[login], 'login', 'igual', 'login');

	if (!$permissao[admin] && !$permissao[excluir]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg = "ATENÇÃO: Você não tem permissão para executar esta função!";
		$url = "?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	} else {

		//verifica o id passado para exclusao
		# Quebrar registro
		$tmpRegistro = explode(":", $registro);
		$idPessoaTipo = $tmpRegistro[0];
		$idPessoa = $tmpRegistro[1];

		$sql = "SELECT 
									$tb[Pessoas].*,
									$tb[TipoPessoas].valor,
									$tb[TipoPessoas].descricao,
									$tb[PlanosPessoas].id AS idPlano,
									$tb[ServicosPlanos].id AS idServicosPlanos, 
									$tb[ServicosAdicionais].id AS idServicosAdicionais, 
									$tb[DescontosServicosPlanos].id AS idDescontosServicosPlanos, 
									$tb[DocumentosGerados].id AS idDocumentosGerados, 
									$tb[PlanosDocumentosGerados].id as idPlanosDocumentosGerados,
									$tb[ServicosAdicionais].status
								FROM 
									Pessoas 
								INNER JOIN $tb[PessoasTipos] 
									On (Pessoas.id = $tb[PessoasTipos].idPessoa)
								INNER JOIN $tb[TipoPessoas]
									On ($tb[PessoasTipos].idTipo = $tb[TipoPessoas].id)
								LEFT JOIN $tb[PlanosPessoas]  
									On ($tb[PessoasTipos].id = $tb[PlanosPessoas].idPessoaTipo) 
								LEFT JOIN $tb[ServicosPlanos] 
									On (PlanosPessoas.id = $tb[ServicosPlanos].idPlano )
								LEFT JOIN $tb[DocumentosGerados] 
									On ($tb[PlanosPessoas].idPessoaTipo = $tb[DocumentosGerados].idPessoaTipo)
								LEFT JOIN $tb[ServicosAdicionais] 
									On ($tb[PlanosPessoas].id = $tb[ServicosAdicionais].idPlano)
								LEFT JOIN $tb[DescontosServicosPlanos] 
									On ( $tb[ServicosPlanos].id = $tb[DescontosServicosPlanos].id)
								LEFT JOIN $tb[PlanosDocumentosGerados]
									On ($tb[PlanosPessoas].id = $tb[PlanosDocumentosGerados].idPlano)
								WHERE
									Pessoas.id = $idPessoa 
									LIMIT 1000";
		//// e realizada uma query que varre todas as tabelas relacionadas a pessoa ate seus documentos de cobranca
		//// nao sendo necessaria mais nenhuma query para verificacao se o existe planos, se existem servicos, ou se
		//// existem servicos gerados, sendo
		//// que um servico nao poderia ficar sem cobranca por mais de 30 dias.
		$consulta = consultaSQL($sql, $conn);

		if (contaConsulta($consulta) > 0) {
			$idPessoa = resultadoSQL($consulta, 0, 'id');
			$idPlano = resultadoSQL($consulta, 0, 'idPlano');
			$nome = resultadoSQL($consulta, 0, 'nome');
			$tipoPessoa = resultadoSQL($consulta, 0, 'tipoPessoa');
			$razao = resultadoSQL($consulta, 0, 'razao');
			$dtNascimento = resultadoSQL($consulta, 0, 'dtNascimento');
			$dtCadastro = resultadoSQL($consulta, 0, 'dtCadastro');
			$pessoaTipo[valor] = resultadoSQL($consulta, 0, 'valor');
			$pessoaTipo[descricao] = resultadoSQL($consulta, 0, 'descricao');
			$contato = resultadoSQL($consulta, 0, 'contato');
			if ($pessoaTipo[valor] == "cli") {

				//verifica se o cliente possui algum servico cadastrado. faz o pelo motivo de 
				//poder existir mais de um plano vazio...
				for ($i = 0; $i < contaConsulta($consulta); $i++)
					if ((resultadoSQL($consulta, $i, "idServicosAdicionais") != NULL && resultadoSQL($consulta, $i, "status") == "A") || resultadoSQL($consulta, $i, "idDescontosServicosPlanos") != NULL || resultadoSQL($consulta, $i, "idDocumentosGerados") != NULL || resultadoSQL($consulta, $i, "idPlanosDocumentosGerados") != NULL) {
						$podeExcluir = "sim";
						break;
					}
				if ($podeExcluir != "sim") {
					if (!$matriz[bntConfirmar]) {
						echo "<br>";
						novaTabela2("[$pessoaTipo[descricao] - Exclusão]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
						# Opcoes Adicionais
						menuOpcAdicional($modulo, $sub, "ver", "$idPessoaTipo:$idPessoa");
						#fim das opcoes adicionais
						novaLinhaTabela($corFundo, '100%');
						$texto = "			
																			<form method=post name=matriz action=index.php>
																			<input type=hidden name=modulo value=$modulo>
																			<input type=hidden name=sub value=$sub>
																			<input type=hidden name=registro value=$registro>
																			<input type=hidden name=matriz[tipoPessoa] value=$matriz[tipoPessoa]>
																			<input type=hidden name=acao value=$acao>&nbsp;";
						itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
						fechaLinhaTabela();
						novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Nome: </b>";
						htmlFechaColuna();
						itemLinhaForm($nome, 'left', 'top', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
						if ($tipoPessoa) {
							novaLinhaTabela($corFundo, '100%');
							htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Tipo Pessoa: </b>";
							htmlFechaColuna();
							itemLinhaForm(formSelectPessoaTipo($tipoPessoa, '', 'check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
						}
						if ($tipoPessoa == 'F') {
							# Pessoa física
							if ($dtNascimento > 0) {
								novaLinhaTabela($corFundo, '100%');
								htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
								echo "<b class=bold10>Data Nascimento: </b>";
								htmlFechaColuna();
								itemLinhaForm(converteData($dtNascimento, 'banco', 'formdata'), 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
							}
						}
						if ($dtCadastro > 0) {
							novaLinhaTabela($corFundo, '100%');
							htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Data Cadastro: </b>";
							htmlFechaColuna();
							itemLinhaForm(converteData($dtCadastro, 'banco', 'formdata'), 'left', 'top', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
						}
						elseif ($tipoPessoa == 'J') {
							# Pessoa Jurídica
							if ($dtNascimento) {
								novaLinhaTabela($corFundo, '100%');
								htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
								echo "<b class=bold10>Razão Social: </b>";
								htmlFechaColuna();
								itemLinhaForm($razao, 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
							}
						}
						if ($contato) {
							novaLinhaTabela($corFundo, '100%');
							htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Contato: </b>";
							htmlFechaColuna();
							itemLinhaForm("$contato ", 'left', 'top', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
						}
						novaLinhaTabela($corFundo, '100%');
						itemLinhaForm("<input type=submit name=matriz[bntConfirmar] value='Excluir' class=submit>", 'center', 'middle', $corFundo, 2, 'tabfundo1');
						fechaLinhaTabela();
						fechaTabela();
					}
					elseif ($matriz[bntConfirmar]) {
						$matriz[id] = $idPessoa;
						$grava = dbPessoa($matriz, "excluir");
						if ($grava) {

							//exclui os documentos referente ao louco.
							$gravaDocumento = dbDocumento($matriz, "excluirtodos");

							$matriz[id] = $idPessoaTipo;

							//remove o endereco.
							$gravaEndereco = dbEndereco($matriz, "excluirtodos");

							//remove planoPessoas
							$gravaPlano = dbPlano($matriz, "excluirtodos");

							//remove PessoasTipos
							$gravarPessoasTipos = dbPessoaTipo($matriz, "excluir");

							$matriz[id] = $idPlano;

							//remove ServicosPlanos
							$gravaServicosPlanos = dbServicosPlano($matriz, "excluirtodos");

							if ($gravaDocumento && $gravaEndereco && $gravaPlano && $gravaServicosPlanos) {
								echo "<br>";
								$msg = "Pessoa excluída com sucesso.";
								avisoNOURL("ATENÇÃO", $msg, 400);
								echo "<br>";
							}
						}
						procurarPessoas($modulo, $sub, $acao, $registro, $matriz);

					}
				} else {
					echo "<br>";
					$msg = "Usuário possui informações importantes cadastradas, não podendo ser removido.";
					avisoNOURL("ATENÇÃO", $msg, "100%");
					echo "<br>";
					verPessoas($modulo, $sub, $acao, $registro, $matriz);
				}

			} //fim da verificacao do tipoo pessoa		
			//}

		} else {
			$msg = "Pessoa não Localizada.";
			avisoNOURL("ATENÇÃO:", $msg, "100%");
		}

	} //permissao
}

//funcao para
function procurarPessoasSelect($modulo, $sub, $acao, $registro, $matriz, $exibeBotoes = 1) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessCadastro, $titulo, $tb;

	// ?
	if ($matriz[idTipoPessoa])
		$tipoPessoa = checkIDTipoPessoa($matriz[idTipoPessoa]);
	else
		$tipoPessoa = checkTipoPessoa('cli');

	if ((($acao == 'procurar') || ($acao == 'adicionar')) && (!$matriz[bntSelecionar])) {

		novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL('<b class=bold10>Busca por ' . $tipoPessoa[descricao] . ':</b><br><span class=normal10>Informe nome do ' . $tipoPessoa[descricao] . ' para busca</span>', 'right', 'middle', '35%', $corFundo, 0, 'tabfundo1');
		$texto = "<input type=text name=matriz[txtProcurar] size=50 value='$matriz[txtProcurar]'>&nbsp;<input type=submit value=Procurar name=matriz[bntProcurar] class=submit>";
		itemLinhaForm($texto, 'left', 'middle', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();

		if ($matriz[txtProcurar] || $matriz[idPessoaTipo]) {
			# Procurar Cliente
			if ($matriz[txtProcurar])
				$consulta = buscaPessoas("
														((upper(nome) like '%$matriz[txtProcurar]%' 
															OR upper(razao) like '%$matriz[txtProcurar]%' 
															OR upper(site) like '%$matriz[txtProcurar]%' 
															OR upper(mail) like '%$matriz[txtProcurar]%')) 
														AND idTipo=$tipoPessoa[id]", $campo, 'custom', 'nome');
			elseif ($matriz[idPessoaTipo]) $consulta = buscaPessoas($matriz[idPessoaTipo], "$tb[PessoasTipos].id", 'igual', 'id');

			if ($consulta && contaConsulta($consulta) > 0) {
				# Selecionar cliente
				novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b class=bold10>' . $tipoPessoa[descricao] . ' encontrados:</b><br>
														<span class=normal10>Selecione:</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto = formSelectConsulta($consulta, 'nome', 'idPessoaTipo', 'idPessoaTipo', $matriz['idPessoaTipo']);
				if ($exibeBotoes != '0')
					$texto .= "&nbsp;<input type=\"submit\" name=\"matriz[bntSelecionar]\" value=\"Selecionar\" class=\"submit\" />";
				itemLinhaForm($texto, 'left', 'middle', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			}
			elseif (contaConsulta($consulta) <= 0) {
				$texto = "Nenhum " . $tipoPessoa[descricao] . " Encontrado!";
				novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL($texto, 'center', 'middle', '10%', $corFundo, '2', 'tabfundo1');
				fechaLinhaTabela();
			}
		}
		htmlFechaLinha();
		//fechaTabela();
	}
	# realizar consulta
	elseif (($matriz['bntSelecionar'] && $matriz['idPessoaTipo']) && (!$matriz['bntAdicionar'])) {
		menuOpcAdicional($modulo, $sub, $acao, $registro, $matriz);
		mostraCliente($matriz['idPessoaTipo']);

		#botao

		novaLinhaTabela($corFundo, '100%');
		//$texto = "<input type=hidden name=status value='A'";
		if ($exibeBotoes != '0')
			$texto = "<input type=\"submit\" name=\"matriz[bntAdicionar]\" value=\"Confirmar\" class=\"submit\" />";

		itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();

	}
}

/**
 * Valida os dados da Pessoa
 *
 * @param array $matriz
 * @return boolean
 */
function validaDadosPessoa($matriz) {
	return ((($matriz[pessoaTipo] == 'F' && $matriz[nome] && $matriz[cpf] && $matriz[endereco] && $matriz[cidade] && $matriz[fone1]) || ($matriz[pessoaTipo] == 'J' && $matriz[nome] && $matriz[razao] && $matriz[cnpj])) ? true : false);
}

/**
 * Função para gerar opção de bloqueio da pessoa caso a mesma seja cliente e que
 *  está cadastrada no Ticket e no ISP
	*/
function opcaoBloquearCliente($modulo, $acao, $sub, $matriz, $idPessoaTipo, $registro) {
	# Consultando pessoa cadastrada tanto no ISP quanto no Ticket
	# e que a mesma seja um cliente
	//definindo consulta de acordo com o módulo selecionado

	$dadosTipoPessoa = dbPessoa($matriz, 'tipoPessoa', $registro);
	//obtendo resultados da consulta
	$dados = mysql_fetch_row($dadosTipoPessoa);
	$idPessoa = $dados[0];
	$tipoPessoa = $dados[1];
	$idPessoaTipo = $dados[2];

	if($tipoPessoa == 'Clientes') {
		# verificar se o cliente retornado está ou não bloqueado
		$consultaEmpresa = dbEmpresas($matriz, 'consultaEmpresa', $idPessoaTipo);
		# recuperando valores da consulta
		if(contaConsulta($consultaEmpresa) > 0){
			$dadosEmpresa = mysql_fetch_array($consultaEmpresa);
			$nomeEmpresa = resultadoSQL($consultaEmpresa, 0, 1); // campo nome
			$bloqueio = resultadoSQL($consultaEmpresa, 0, 1); // campo bloqueio
		}
		// definindo valores a serem setados no link
		$sub = "clientes";
	}
	
	# montar opção de acordo com o status do bloqueio
	if (strtoupper($bloqueio) == 'N') {
		$acao = "bloquear";
		$opcao = "Bloquear Cliente";
	}
	elseif (strtoupper($bloqueio) == 'S') {
		$acao = "desbloquear";
		$opcao = "Desbloquear Cliente";
	}
	
	//montando link e opção. O ícone é definido pela ação
	if($acao == 'bloquear'){
		$icone = "desativar";
	}
	elseif($acao == 'desbloquear'){
		$icone = "ativar";
	}
	
	//Montando link e opção
	if($modulo == 'administracao'){
		$sub = "suporte";
		$acaoBloqueio = $acao;
		$acao = "ver";
		$link = "?modulo=$modulo&sub=$sub&acao=$acao&acaoBloqueio=$acaoBloqueio&registro=$idPessoaTipo";
	}
	elseif($modulo = 'cadastros'){
		$link = "?modulo=$modulo&sub=$sub&acao=$acao&idPessoaTipo=$idPessoaTipo";
	}
	if(contaConsulta($consultaEmpresa) > 0){
		$retorno = htmlMontaOpcao("<a href='$link'>$opcao</a>", $icone);
		return $retorno;
	}
	else{
		return false;
	}

	
}

function consultaClienteAtivo($idPessoaTipo) {
	global $conn;

	$sql = "SELECT StatusServicosPlanos.status FROM PlanosPessoas, ServicosPlanos, StatusServicosPlanos WHERE PlanosPessoas.idPessoaTipo = '$idPessoaTipo' AND ServicosPlanos.idPlano = PlanosPessoas.id AND ServicosPlanos.idStatus = StatusServicosPlanos.id AND StatusServicosPlanos.status <> 'C'";
	$consulta = consultaSQL($sql,$conn);
	
	while ($resultado = mysql_fetch_array($consulta)) {
		if ($resultado['status'] == 'A') {
			$statusA++;
		} elseif ($resultado['status'] == 'I') {
			$statusI++;
		}
	}
	
	$sql2 = "SELECT ServicoIVR.status FROM PlanosPessoas, ServicosPlanos, ServicoIVR WHERE PlanosPessoas.idPessoaTipo = '$idPessoaTipo' AND ServicosPlanos.idPlano = PlanosPessoas.id AND ServicosPlanos.id = ServicoIVR.idServicoPlano";
	$consulta2 = consultaSQL($sql2, $conn);

	while ($result = mysql_fetch_array($consulta2)) {
		if ($result['status'] == 'A') {
			$statusA++;
		} else if ($result['status'] == 'I') {
			$statusI++;
		}
	}
	
	if ($statusA) {
		return 'A';
	} else {
		return 'I';
	}
}
?>