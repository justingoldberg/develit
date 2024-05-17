<?

################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 23/09/2003
# Ultima alteração: 11/03/2004
#    Alteração No.: 012
#
# Função:
#    Painel - Funções para configurações

# Função de configurações
function administracao($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessCadastro;

	# Permissão do usuario
	$permissao = buscaPermissaoUsuario($sessLogin[login], 'login', 'igual', 'login');

	if (!$permissao[admin] && !$permissao[visualizar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg = "ATENÇÃO: Você não tem permissão para executar esta função";
		$url = "?modulo=home";
		aviso("Acesso Negado", $msg, $url, 760);
	} else {

		# Quebrar registro
		$tmpValor = explode(":", $registro);

		$registro = $tmpValor[0];
		$matriz[id] = $tmpValor[1];

		$sessCadastro[idPessoaTipo] = $registro;

		verPessoas('cadastros', 'clientes', 'ver', $registro, $matriz);

		# verificação dos submodulos
		if ($sub == 'limites') {
			administracaoLimites($modulo, $sub, $acao, $registro, $matriz);
		}
		else {
			administracaoMenu($modulo, $sub, $acao, $registro, $matriz);
		}
	}

	echo "<script>location.href='#ancora';</script>";
}

# Mostrar limites de configuração para usuario
function administracaoLimites($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $html;

	echo "<br>";
	novaTabela2SH("center", '100%', 0, 0, 0, $corFundo, $corBorda, 3);
	novaLinhaTabela($corFundo, '100%');
	htmlAbreColuna('30%', 'center" valign="top', $corFundo, 0, 'normal10');
	novaTabela2("[Opções]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 0);
	$texto = htmlMontaOpcao("Configurações", 'config');
	itemTabela($texto, "?modulo=$modulo&registro=$registro", 'left', $corFundo, 0, 'normal');
	$texto = htmlMontaOpcao("Planos e Serviços", 'servicos');
	itemTabela($texto, "?modulo=lancamentos&sub=planos&acao=listar&registro=$registro", 'left', $corFundo, 0, 'normal');
	$texto = htmlMontaOpcao("Ocorrências", 'ocorrencia');
	itemTabela($texto, "?modulo=ocorrencias&registro=$registro", 'left', $corFundo, 0, 'normal');
	fechaTabela();
	htmlFechaColuna();

	itemLinhaTMNOURL('&nbsp;&nbsp;&nbsp;', 'center', 'middle', '1', $corFundo, 0, 'normal10');
	htmlAbreColuna('70%', 'center" valign="top', $corFundo, 0, 'normal10');
	novaTabela2("[Limites de Configuração]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 0);
	novaLinhaTabela($corFundo, '100%');
	htmlAbreColuna('100%', 'center valign=top', $corFundo, 0, 'normal10');
	listarLimitesAdministracao($modulo, $sub, $acao, $registro, $matriz);
	htmlFechaColuna();
	fechaLinhaTabela();
	fechaTabela();

}

# Mostrar limites de configuração para usuario
function administracaoMenu($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $tb, $corFundo, $corBorda, $html;

	if ($sub == 'dial' && $acao == 'adicionarusuario') {
		# Form de Inclusão
		novaLinhaTabela($corFundo, '100%');
		htmlAbreColuna('100%', 'center valign=top', $corFundo, 2, 'normal10');
		administracaoRadiusAdicionarConta($modulo, $sub, $acao, $registro, $matriz);
		htmlFechaColuna();
		fechaLinhaTabela();
	}
	elseif ($sub == 'dial' && $acao == 'alterar') {
		# Alteração de Senha de Conta de Acesso
		novaLinhaTabela($corFundo, '100%');
		htmlAbreColuna('100%', 'center', 'top', $corFundo, 2, 'normal10');
		administracaoRadiusAlterarConta($modulo, $sub, $acao, $registro, $matriz);
		htmlFechaColuna();
		fechaLinhaTabela();
	}
	elseif ($sub == 'dial' && $acao == 'excluir') {
		# Exclusão de Conta de Acesso
		novaLinhaTabela($corFundo, '100%');
		htmlAbreColuna('100%', 'center valign=top', $corFundo, 2, 'normal10');
		administracaoRadiusExcluirConta($modulo, $sub, $acao, $registro, $matriz);
		htmlFechaColuna();
		fechaLinhaTabela();
	}
	elseif ($sub == 'dial' && $acao == 'inativar') {
		# Inativar de Conta de Acesso
		novaLinhaTabela($corFundo, '100%');
		htmlAbreColuna('100%', 'center valign=top', $corFundo, 2, 'normal10');
		administracaoRadiusInativarConta($modulo, $sub, $acao, $registro, $matriz);
		htmlFechaColuna();
		fechaLinhaTabela();
	}
	elseif ($sub == 'dial' && $acao == 'ativar') {
		# Ativar de Conta de Acesso
		novaLinhaTabela($corFundo, '100%');
		htmlAbreColuna('100%', 'center valign=top', $corFundo, 2, 'normal10');
		administracaoRadiusAtivarConta($modulo, $sub, $acao, $registro, $matriz);
		htmlFechaColuna();
		fechaLinhaTabela();
	}
	elseif ($sub == 'dial' && $acao == 'extrato') {
		# Ativar de Conta de Acesso
		novaLinhaTabela($corFundo, '100%');
		htmlAbreColuna('100%', 'center valign=top', $corFundo, 2, 'normal10');
		radiusExtratoForm($modulo, $sub, $acao, $registro, $matriz);
		htmlFechaColuna();
		fechaLinhaTabela();
	}
	elseif ($sub == 'dial' && $acao == 'telefones') {
		# Ativar de Conta de Acesso
		novaLinhaTabela($corFundo, '100%');
		htmlAbreColuna('100%', 'center valign=top', $corFundo, 2, 'normal10');
		listarRadiusTelefones($modulo, $sub, $acao, $registro, $matriz);
		htmlFechaColuna();
		fechaLinhaTabela();
	}
	elseif ($sub == 'dial' && $acao == 'telefonesadicionar') {
		# Ativar de Conta de Acesso
		novaLinhaTabela($corFundo, '100%');
		htmlAbreColuna('100%', 'center valign=top', $corFundo, 2, 'normal10');
		adicionarRadiusTelefone($modulo, $sub, $acao, $registro, $matriz);
		htmlFechaColuna();
		fechaLinhaTabela();
	}
	elseif ($sub == 'dial' && $acao == 'telefonesalterar') {
		# Ativar de Conta de Acesso
		novaLinhaTabela($corFundo, '100%');
		htmlAbreColuna('100%', 'center valign=top', $corFundo, 2, 'normal10');
		alterarRadiusTelefone($modulo, $sub, $acao, $registro, $matriz);
		htmlFechaColuna();
		fechaLinhaTabela();
	}
	elseif ($sub == 'dial' && $acao == 'telefonesexcluir') {
		# Ativar de Conta de Acesso
		novaLinhaTabela($corFundo, '100%');
		htmlAbreColuna('100%', 'center valign=top', $corFundo, 2, 'normal10');
		excluirRadiusTelefone($modulo, $sub, $acao, $registro, $matriz);
		htmlFechaColuna();
		fechaLinhaTabela();
	}
	elseif ($sub == 'dial' && $acao == 'telefonesativar') {
		# Ativar de Conta de Acesso
		novaLinhaTabela($corFundo, '100%');
		htmlAbreColuna('100%', 'center valign=top', $corFundo, 2, 'normal10');
		ativarRadiusTelefone($modulo, $sub, $acao, $registro, $matriz);
		htmlFechaColuna();
		fechaLinhaTabela();
	}
	elseif ($sub == 'dial' && $acao == 'telefonesinativar') {
		# Ativar de Conta de Acesso
		novaLinhaTabela($corFundo, '100%');
		htmlAbreColuna('100%', 'center valign=top', $corFundo, 2, 'normal10');
		inativarRadiusTelefone($modulo, $sub, $acao, $registro, $matriz);
		htmlFechaColuna();
		fechaLinhaTabela();
	}
	elseif ($sub == 'dominios' && $acao == 'parametrosadicionar') {
		echo "<br>";
		$matriz[id] = $registro;
		adicionarDominiosParametros($modulo, $sub, $acao, $registro, $matriz);
	}
	elseif ($sub == 'dominios' && $acao == 'parametrosexcluir') {
		echo "<br>";
		$matriz[id] = $registro;
		excluirDominiosParametros($modulo, $sub, $acao, $registro, $matriz);
	}
	 else {

		$sql = "
					SELECT 
						$tb[Modulos].id id, 
						$tb[Modulos].modulo modulo ,
						$tb[Modulos].descricao descricao
					FROM 
						$tb[Modulos], 
						$tb[Parametros], 
						$tb[ParametrosModulos], 
						$tb[ServicosParametros], 
						$tb[Servicos], 
						$tb[ServicosPlanos], 
						$tb[PlanosPessoas], 
						$tb[PessoasTipos], 
						$tb[Unidades], 
						$tb[Pessoas] 
					WHERE 
						$tb[Modulos].id=$tb[ParametrosModulos].idModulo  
						AND $tb[ParametrosModulos].idParametro = $tb[Parametros].id 
						AND $tb[Parametros].idUnidade = $tb[Unidades].id 
						AND $tb[Parametros].id = $tb[ServicosParametros].idParametro 
						AND $tb[ServicosParametros].idServico = $tb[Servicos].id 
						AND $tb[Servicos].id = $tb[ServicosPlanos].idServico 
						AND $tb[ServicosPlanos].idPlano = $tb[PlanosPessoas].id 
						AND $tb[PlanosPessoas].idPessoaTipo = $tb[PessoasTipos].id 
						AND $tb[PessoasTipos].idPessoa = $tb[Pessoas].id 
						AND $tb[PessoasTipos].id = $registro
						AND $tb[PlanosPessoas].status='A'
					GROUP BY 
						$tb[Modulos].id 
					ORDER BY 
						$tb[Pessoas].id,  
						$tb[Modulos].modulo";

		$consulta = consultaSQL($sql, $conn);


		
		// Código alterado
		if ($consulta && contaConsulta($consulta) > 0) {
			//nova tabela
			echo "<br>";
			novaTabela2SH('center', '100%', 0, 0, 0, $corFundo, $corBorda, 0);
//			novaTabela2SH($alinhamento, $tamanho, $borda, $spcCel, $spcCarac, $corFundo, $corBorda, $colunas)
				novaLinhaTabela($corFundo, '100%');
					novaTabela2SH('center', '100%', 0, 0, 0, $corFundo, $corBorda, 0);
						novaLinhaTabela($corFundo, '100%');
							htmlAbreColuna('40%', 'center', 'top', $corFundo, 0, 'normal10');
								//colocar tabela opções e acesso rápido aqui
								# Menu de opções
								novaTabela2("Opções", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
								for ($a = 0; $a < contaConsulta($consulta); $a++) {
									$id = resultadoSQL($consulta, $a, 'id');
									$descricao = resultadoSQL($consulta, $a, 'descricao');
									$nomeModulo = resultadoSQL($consulta, $a, 'modulo');

									if ($nomeModulo == 'mail') {
										$texto = htmlMontaOpcao($descricao, $nomeModulo);
										$texto = "<a href=?modulo=$modulo&sub=$nomeModulo&acao=config&registro=$registro>$texto</a>";
										itemTabelaNOURL($texto, 'left', $corFundo, 0, 'normal10');
									}
									elseif ($nomeModulo == 'dial') {
										$texto = htmlMontaOpcao($descricao, $nomeModulo);
										$texto = "<a href=?modulo=$modulo&sub=$nomeModulo&acao=config&registro=$registro>$texto</a>";
										itemTabelaNOURL($texto, 'left', $corFundo, 0, 'normal10');
									}
									elseif ($nomeModulo == 'web') {
										$texto = htmlMontaOpcao($descricao, $nomeModulo);
										$texto = "<a href=?modulo=$modulo&sub=$nomeModulo&acao=config&registro=$registro>$texto</a>";
										itemTabelaNOURL($texto, 'left', $corFundo, 0, 'normal10');
									}
									elseif ($nomeModulo == 'ivr') {
										$texto = htmlMontaOpcao($descricao, $nomeModulo);
										$texto = "<a href=?modulo=$modulo&sub=$nomeModulo&acao=config&registro=$registro>$texto</a>";
										itemTabelaNOURL($texto, 'left', $corFundo, 0, 'normal10');
									}
									elseif ($nomeModulo == 'dominio') {
										$texto = htmlMontaOpcao($descricao, $nomeModulo);
										$texto = "<a href=?modulo=$modulo&sub=$nomeModulo&acao=config&registro=$registro>$texto</a>";
										itemTabelaNOURL($texto, 'left', $corFundo, 0, 'normal10');
									}
									elseif ($nomeModulo == 'suporte') {
										$texto = htmlMontaOpcao($descricao, $nomeModulo);
										$texto = "<a href=?modulo=$modulo&sub=$nomeModulo&acao=config&registro=$registro>$texto</a>";
										itemTabelaNOURL($texto, 'left', $corFundo, 0, 'normal10');
									}
	
									# Modulo a ser visualizado
									if (!$sub) {
										$sub = $nomeModulo;	
										$idModulo = $id;
										$descricaoModulo = $descricao;
									}
									elseif ($nomeModulo == $sub) {
										$sub = $nomeModulo;
										$idModulo = $id;
										$descricaoModulo = $descricao;
										$nomeModuloVer = $nomeModulo;
									}

								}
								fechaTabela();
								echo "<br>";
								
								# Menu de atalhos
								novaTabela2("[Acesso Rápido]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 0);
									$texto = htmlMontaOpcao("Planos e Serviços", 'servicos');
									itemTabela($texto, "?modulo=lancamentos&sub=planos&acao=listar&registro=$registro", 'left', $corFundo, 0, 'normal');
									$texto = htmlMontaOpcao("Ocorrências", 'ocorrencia');
									itemTabela($texto, "?modulo=ocorrencias&registro=$registro", 'left', $corFundo, 0, 'normal');
								fechaTabela();
							htmlFechaColuna();
							htmlAbreColuna('1%', 'center', 'top', $corFundo, 0, 'normal10');
								itemLinhaTabela('&nbsp;', 'center', '1%', 'normal10');
							htmlFechaColuna();
							htmlAbreColuna('60%', 'center', 'top', $corFundo, 0, 'normal10');
								//Visualizando configurações
								# Configuração
								novaTabela2("Visualização de Configurações", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 0);
									novaLinhaTabela($corFundo, '100%');
									# Menu de opções
										htmlAbreColuna('100%', 'left', $corFundo, 0, 'normal10');
											$texto = htmlMontaOpcao($descricaoModulo, $sub);
											echo "<span class=bold10>$texto</span>";
											listarLimitesModulo($registro, $idModulo);
										htmlFechaColuna();
									fechaLinhaTabela();
								fechaTabela();
								echo "<br>";
								echo "<br>";
								echo "<br>";
								echo "<br>";
							htmlFechaColuna();
						fechaLinhaTabela();
					fechaTabela();
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela('&nbsp;', 'center', '100%', 'normal10');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					# Listar mais detalhes sobre configurações do modulo
					$matriz[idModulo] = $idModulo;
					$matriz[idPessoaTipo] = $registro;
					moduloListarConfiguracao($modulo, $sub, $acao, $registro, $matriz);
				fechaLinhaTabela();
			fechaTabela();
			
			//fim da nova tabela
		}
		else {
			# não foram encontrados registros
			echo "<span class=txtaviso>Não foram encontrados parâmetros para serviços configurados.</span>";
		}
		// fim do código alterado
	}
}
?>