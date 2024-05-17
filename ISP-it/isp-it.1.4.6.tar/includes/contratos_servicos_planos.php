<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 11/03/2004
# Ultima alteração: 20/03/2004
#    Alteração No.: 003
#
# Função:
#    Painel - Funções para cadastro de contratos dos serviços


function buscaContratosServicosPlanos($texto, $campo, $tipo, $ordem) {

	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[ContratosServicosPlanos] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[ContratosServicosPlanos] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[ContratosServicosPlanos] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[ContratosServicosPlanos] WHERE $texto ORDER BY $ordem";
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



# Função de banco de dados - Contratos Servicos
function dbContratoServicoPlano($matriz, $tipo) {

	global $conn, $tb, $modulo, $sub, $acao;
	
	$data=dataSistema();
	
	# Sql de inclusão
	if($tipo=='incluir') {
		$sql="
			INSERT INTO 
				$tb[ContratosServicosPlanos] 
			VALUES (
				0,
				'$matriz[idContrato]',
				'$matriz[idServicoPlano]',
				'$matriz[idusuario]',
				'$data[dataBanco]',
				'',
				'',
				'',
				'',
				'$matriz[validade]',
				'$matriz[nomeArquivo]',
				'$matriz[numeroContrato]',
				'$matriz[numeroSequencia]',
				'$matriz[nomePagina]',
				'$matriz[status]'
		)";
		
	} #fecha inclusao
	elseif($tipo=='cancelar') {
		$sql="UPDATE 
					$tb[ContratosServicosPlanos] 
				SET
					status='C',
					dtCancelamento='$matriz[dtCancelamento]',
					idUsuarioCancelamento='$matriz[idUsuario]'
				WHERE 
					id=$matriz[id]";
	}
	elseif($tipo=='renovar') {
		$sql="UPDATE 
					$tb[ContratosServicosPlanos] 
				SET
					status='R',
					dtRenovacao='$matriz[dtRenovacao]',
					idUsuario='$matriz[idUsuario]',
					mesValidade='$matriz[mesValidade]',
					idUsuarioCancelamento='',
					dtCancelamento=''
				WHERE 
					id=$matriz[id]";
	}
	
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
}


# Listar Contratos Sericos Planos
function listarContratosServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $html, $sessLogin, $conn, $tb;
	
	$sql="
		SELECT
			$tb[Servicos].nome nomeServico, 
			$tb[ServicosPlanos].id idServicoPlano, 
			$tb[ServicosPlanos].idServico idServico, 
			$tb[ServicosPlanos].valor valor, 
			$tb[ServicosPlanos].dtAtivacao, 
			$tb[StatusServicos].descricao statusServico,
			$tb[ServicosContratos].id idServicosContratos,
			$tb[ServicosContratos].idContrato idContrato,
			COUNT($tb[ContratosPaginas].id) qtde
		FROM
			$tb[Servicos], 
			$tb[ServicosPlanos], 
			$tb[PlanosPessoas], 
			$tb[StatusServicos],
			$tb[ServicosContratos],
			$tb[ContratosPaginas]
		WHERE
			$tb[ServicosPlanos].idServico = $tb[Servicos].id 
			AND $tb[Servicos].id = $tb[ServicosContratos].idServico
			AND $tb[ServicosContratos].idContrato = $tb[ContratosPaginas].idContrato
			AND $tb[ServicosPlanos].idPlano = $tb[PlanosPessoas].id 
			AND $tb[ServicosPlanos].idStatus = $tb[StatusServicos].id 
			AND $tb[PlanosPessoas].idPessoaTipo = $registro
			AND $tb[StatusServicos].status='A'
		GROUP BY
			$tb[ServicosPlanos].id
	";
	
	$consulta=consultaSQL($sql, $conn);
	
	# Motrar tabela de busca
	novaTabela("[Contratos Disponíveis para Geração]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
	
	# Caso não hajam servicos para o servidor
	if(!$consulta || contaConsulta($consulta)==0) {
		# Não há registros
		itemTabelaNOURL("Não há serviços disponíveis para geração de contrato!", 'left', $corFundo, 3, 'txtaviso');
	}
	else {
		# Cabeçalho
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTabela('Serviço', 'center', '50%', 'tabfundo0');
			itemLinhaTabela('Paginas', 'center', '20%', 'tabfundo0');
			itemLinhaTabela('Opções', 'center', '30%', 'tabfundo0');
		fechaLinhaTabela();

		$i=0;
		
		while($i < contaConsulta($consulta)) {
			# Mostrar registro
			$idServico=resultadoSQL($consulta, $i, 'idServico');
			$idServicoPlano=resultadoSQL($consulta, $i, 'idServicoPlano');
			$idServicosContratos=resultadoSQL($consulta, $i, 'idServicosContratos');
			$idContrato=resultadoSQL($consulta, $i, 'idContrato');
			$nomeServico=resultadoSQL($consulta, $i, 'nomeServico');
			$qtde=resultadoSQL($consulta, $i, 'qtde');
			$statusServico=resultadoSQL($consulta, $i, 'statusServico');
			$dtAtivacao=resultadoSQL($consulta, $i, 'dtAtivacao');

			$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=gerar&registro=$registro:$idServicoPlano>Gerar Contrato</a>",'pdf');

			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela(formSelectServicos($idServico,'','check'), 'left', '50%', 'normal10');
				itemLinhaTabela("$qtde pagina(s)", 'center', '20%', 'txtok');
				itemLinhaTabela($opcoes, 'left', '30%', 'normal8');
			fechaLinhaTabela();
			
			# Incrementar contador
			$i++;
		} #fecha laco de montagem de tabela
			
	} #fecha listagem		
		
	fechaTabela();
}




# Listar Contratos Sericos Planos
function listarContratosServicosPlanosServico($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $html, $sessLogin, $conn, $tb;
	
	verServico($modulo, $sub, $acao, $matriz[idServicosContratos], $matriz);
	echo "<br>";
	
	# Listar Contratos
	$consulta=buscaServicosContratos($matriz[idServicosContratos],'idServico','igual','id');
	
	# Motrar tabela de busca
	novaTabela("[Contratos Disponíveis para Geração]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 4);
	
	# Caso não hajam servicos para o servidor
	if(!$consulta || contaConsulta($consulta)==0) {
		# Não há registros
		itemTabelaNOURL("Não há contratos disponíveis!", 'left', $corFundo, 4, 'txtaviso');
	}
	else {
		# Cabeçalho
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTabela('Contrato', 'center', '50%', 'tabfundo0');
			itemLinhaTabela('Data Cadastro', 'center', '10%', 'tabfundo0');
			itemLinhaTabela('Validade', 'center', '10%', 'tabfundo0');
			itemLinhaTabela('Opções', 'center', '30%', 'tabfundo0');
		fechaLinhaTabela();

		$i=0;
		
		while($i < contaConsulta($consulta)) {
			# Mostrar registro
			$id=resultadoSQL($consulta, $i, 'id');
			$idServico=resultadoSQL($consulta, $i, 'idServico');
			$idContrato=resultadoSQL($consulta, $i, 'idContrato');
			$dtCadastro=resultadoSQL($consulta, $i, 'dtCadastro');
			$mesValidade=resultadoSQL($consulta, $i, 'mesValidade');
			
			$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=&acao=ver&registro=$registro:$matriz[idServicosContratos]:$id>Visualizar</a>",'ver');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=pdf&registro=$registro:$matriz[idServicosContratos]:$id>Gerar Contrato (PDF)</a>",'pdf');

			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela(formSelectContratos($idContrato,'','check'), 'left', '50%', 'normal10');
				itemLinhaTabela(converteData($dtCadastro, 'banco','formdata'), 'center', '10%', 'normal10');
				itemLinhaTabela("$mesValidade meses", 'center', '10%', 'txtok');
				itemLinhaTabela($opcoes, 'left', '30%', 'normal8');
			fechaLinhaTabela();
			
			# Incrementar contador
			$i++;
		} #fecha laco de montagem de tabela
			
	} #fecha listagem		
		
	fechaTabela();
}



# Visualização de Contrato em HTML
function verContratosServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $html, $sessLogin, $tb, $conn, $configMeses;
	
	$data=dataSistema();
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=home";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
	
		# Buscar o Serviço
		# Dados Servicos Planos
		$servicoPlano=dadosServicoPlano($matriz[id]);
		$idPlano=$servicoPlano[idPlano];
		$idServicoPlano=$servicoPlano[id];
		$valorServicoPlano=$servicoPlano[valor];
		
		## Buscar Contrato para o Serviço
		$consultaContratos=buscaServicosContratos($servicoPlano[idServico], 'idServico','igual','id');
		
		# Numero do Contrato
		$novoContrato=gerarNumeroContratosServicosPlanos();
		$numeroContrato=$novoContrato[mascara];
		$numeroSequencia=$novoContrato[numeroSequencia];
		
		# ID do usuario
		$matriz[idUsuario]=buscaIDUsuario($sessLogin[login],'login','igual','id');
		
		if($consultaContratos && contaConsulta($consultaContratos)>0) {
		
			for($a=0;$a<contaConsulta($consultaContratos);$a++) {
				$idServicosContratos=resultadoSQL($consultaContratos, $a, 'id');
				$idServico=resultadoSQL($consultaContratos, $a, 'idServico');
				$idContrato=resultadoSQL($consultaContratos, $a, 'idContrato');
				$dtCadastro=resultadoSQL($consultaContratos, $a, 'dtCadastro');
				$mesValidade=resultadoSQL($consultaContratos, $a, 'mesValidade');
			
				## Gerar contrato
				### Buscar Paginas do contrato
				$consultaPaginas=buscaPaginasContratos("idContrato=$idContrato AND status='A'", '','custom','numeroPagina ASC');
				
				if($consultaPaginas && contaConsulta($consultaPaginas)>0) {
				
					#nova tabela para mostrar informações
					novaTabela2('Visualização de Contrato', 'center', '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				
					for($b=0;$b<contaConsulta($consultaPaginas);$b++) {
						$idPagina=resultadoSQL($consultaPaginas, $b, 'id');
						$nomePagina=resultadoSQL($consultaPaginas, $b, 'nomePagina');
						$numeroPagina=resultadoSQL($consultaPaginas, $b, 'numeroPagina');
						$descricao=resultadoSQL($consultaPaginas, $b, 'descricao');
						$conteudo=resultadoSQL($consultaPaginas, $b, 'conteudo');
						$dtCadastro=resultadoSQL($consultaPaginas, $b, 'dtCadastro');
						$status=resultadoSQL($consultaPaginas, $b, 'status');
						
						# Mostrar pagina do contrato
						## Busca dados do serviço e da pessoa
						$servico=checkServico($idServico);
						$valorServico=$servico[valor];
						$nomeServico=$servico[nome];
						
						# Dados do Plano
						$plano=dadosPlanos($servicoPlano[idPlano]);
						$idPessoaTipo=$plano[idPessoaTipo];
						
						# Dados do vencimento do Plano Cliente
						$vencimento = dadosVencimento($plano[idVencimento]);
						
						# Valor do serviço
						if(checkPlanoEspecial($idPlano)) $valor=$valorServico;
						else $valor=$valorServicoPlano;
									
						# Parametros do Serviço
						# Velocidade - Unidade da Velocidade
						$parametrosServicoPlano=carregaParametrosServicoPlano($idServicoPlano, 'ivr');
						
						# Dados Pessoa
						$pessoaTipo=dadosPessoasTipos($idPessoaTipo);
						$idPessoa=$pessoaTipo[idPessoa];
						$pessoa=dadosPessoas($idPessoa);
			
						# Documentos
						$documentos=carregaDocumentosPessoaTipo($idPessoaTipo);
			
						# Enderecos
						$endereco=carregaEnderecoPessoaTipo($idPessoaTipo, 'cor');
						
						if( count($endereco) == '0' ){
							$endereco=carregaEnderecoPessoaTipo($idPessoaTipo, 'cob');
						}

						# Cidade principal do POP
						$cidadePOP=cidadePOPPessoaTipo($idPessoaTipo);
			
						# Form para preenchimento de dados do contrato - opciona
						$contrato=dadosContratos($idContrato);
												
						# Menu adicional
						novaLinhaTabela($corFundo, '100%');
							itemLinhaForm("<b>Pagina: $nomePagina - Número: $numeroPagina - Data Cadastro: ".converteData($dtCadastro, 'banco','form')."</b>", 'right', 'top', $corFundo, 2, 'tabfundo0');
						fechaLinhaTabela();
							
						# Parse da Pagina
						if($pessoa[tipoPessoa]=='F') {
							$dadosContrato[NOME]=$pessoa[nome];
							$dadosContrato[DOCUMENTO]=$documentos[cpf];
							$dadosContrato[RG]=$documentos[rg];
						}
						else {
							$dadosContrato[NOME]=$pessoa[razao];
							$dadosContrato[DOCUMENTO]=$documentos[cnpj];
							$dadosContrato[RG]=' ';
						}
						
						$dadosContrato[ENDERECO]=$endereco[endereco];
						$dadosContrato[COMPLEMENTO]=$endereco[complemento];
						$dadosContrato[BAIRRO]=$endereco[bairro];
						$dadosContrato[CEP]=$endereco[cep];
						$dadosContrato[CIDADE]=$endereco[cidade];
						$dadosContrato[TELEFONE]="$endereco[ddd_fone1] $endereco[fone1]";
						$dadosContrato[UF]=$endereco[uf];
						$dadosContrato[CIDADE_CONTRATO]=$cidadePOP[nome];
						$dadosContrato[UF_CONTRATO]=$cidadePOP[UF];
						
						if(!$matriz[dtContrato]) {
							$dadosContrato[DIA_CONTRATO]=$data[dia];
							$dadosContrato[MES_CONTRATO]=$configMeses[intval($data[mes])];
							$dadosContrato[ANO_CONTRATO]=$data[ano];
						}
						else {
							$dadosContrato[DIA_CONTRATO]=substr($matriz[dtContrato],0,2);
							$dadosContrato[MES_CONTRATO]=$configMeses[intval(substr($matriz[dtContrato],3,2))];
							$dadosContrato[ANO_CONTRATO]=substr($matriz[dtContrato],6,4);
						}
						
						$dadosContrato[VALOR_MENSAL]   = formatarValoresForm($valor);
						$dadosContrato[DIA_VENCIMENTO] = $vencimento[diaVencimento];
							
						# Dados de parametros
						$dadosContrato[VELOCIDADE]=$parametrosServicoPlano[velocidade];
						$dadosContrato[UNIDADE_VELOCIDADE]="";
							
						# Numero do contrato
						$dadosContrato[NUMERO_CONTRATO]=$numeroContrato;
						
						# Parcelas
						$parcelas=parcelasServicoPlano($idServicoPlano);
						
						$dadosContrato[VALOR_PARCELA]=$parcelas[valor_parcela];
						$dadosContrato[PARCELAS]=$parcelas[parcelas];
						$dadosContrato[QTDE_PARCELAS]=$parcelas[qtde];
						$dadosContrato[VALOR_INSTALACAO]=$parcelas[valor_total];
						
						# Validade meses do contrato
						$dadosContrato[MES_VALIDADE] = $mesValidade;
							
						# Completar modelo do banco
						$conteudo=k_templateParse($conteudo, $dadosContrato);
						
						if($matriz[acao]=='visualizar') {
							novaLinhaTabela($corFundo, '100%');
								itemLinhaForm($conteudo, 'left', 'top', $corFundo, 2, 'normal10');
							fechaLinhaTabela();
						}
						elseif($matriz[acao]=='gerar') {
							
							# Gravar Contrato Gerado
							# Gravar informações antes de gerar pagina, pios podem
							# ser gerados varios contratos em paralelo, o que causaria
							# problema de duplicidade
							$matriz[idContrato]=$idContrato;
							$matriz[idServicoPlano]=$idServicoPlano;
							$matriz[validade]=$mesValidade;
							$matriz[nomeArquivo]=$pdfFile;
							$matriz[numeroContrato]=$numeroContrato;
							$matriz[numeroSequencia]=$numeroSequencia;
							$matriz[nomePagina]=$nomePagina;
							$matriz[status]='A';
							
							dbContratoServicoPlano($matriz, 'incluir');
							
							# Parse de HTML
							$tmpArquivo=htmlPreencheDados($dadosContrato, $idPagina);
							
							# Converter HTML (arquivo) para PDF (arquivo)
							$pdfFile=pdfConverterArquivo($tmpArquivo);
							$texto=htmlMontaOpcao("<a href=$pdfFile>$nomePagina</a>", 'pdf');
					
							# Selecionar parametros do dominio
							novaLinhaTabela($corFundo, '100%');
								itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'normal10');
							fechaLinhaTabela();
						}
					}
				}
			}
		}
		else {
			itemTabelaNOURL('Registro não encontrado!', 'left', $corFundo, 2, 'txtaviso');		
		}
		
		fechaTabela();	
		# fim da tabela
	}
}



# Listar Contratos Servios Planos
function listarGeradosContratosServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $html, $sessLogin, $conn, $tb;
	
	
	$data=dataSistema();
	
	if($acao=='listartodos') $sqlADD="";
	elseif($acao=='listarcancelados') $sqlADD="AND ($tb[ContratosServicosPlanos].status='C')";
	else $sqlADD="AND ( $tb[ContratosServicosPlanos].status='A' OR $tb[ContratosServicosPlanos].status='R' )";

	$sql="
		SELECT
			$tb[ContratosServicosPlanos].id id, 
			$tb[ContratosServicosPlanos].idContrato idContrato, 
			$tb[ContratosServicosPlanos].idServicoPlano idServicoPlano, 
			$tb[ContratosServicosPlanos].idUsuario idUsuario, 
			$tb[ContratosServicosPlanos].dtEmissao dtEmissao, 
			$tb[ContratosServicosPlanos].dtRenovacao dtRenovacao, 
			$tb[ContratosServicosPlanos].mesValidade mesValidade, 
			$tb[ContratosServicosPlanos].numeroContrato numeroContrato, 
			$tb[ContratosServicosPlanos].numeroSequencia numeroSequencia, 
			$tb[ContratosServicosPlanos].nomePagina nomePagina, 
			$tb[ContratosServicosPlanos].nomeArquivo nomeArquivo, 
			$tb[ContratosServicosPlanos].status status,
			$tb[Contratos].nome nomeContrato, 
			$tb[ServicosPlanos].idServico idServico,
			$tb[Servicos].nome nomeServico
		FROM
			$tb[ContratosServicosPlanos], 
			$tb[Contratos], 
			$tb[ServicosPlanos], 
			$tb[Servicos], 
			$tb[PlanosPessoas] 
		WHERE
			$tb[ServicosPlanos].idServico= $tb[Servicos].id 
			AND $tb[ServicosPlanos].idPlano = $tb[PlanosPessoas].id 
			AND $tb[ContratosServicosPlanos].idContrato = $tb[Contratos].id 
			AND $tb[ServicosPlanos].id = $tb[ContratosServicosPlanos].idServicoPlano 
			AND $tb[PlanosPessoas].idPessoaTipo = $registro
			$sqlADD
	";
	
	$consulta=consultaSQL($sql, $conn);
	
	# Motrar tabela de busca
	novaTabela("[Contratos Gerados]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 6);
	novaLinhaTabela($corFundo, '100%');
		$texto=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar&registro=$registro:$matriz[id]>Listar Ativos</a>",'listar');
		$texto.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listarcancelados&registro=$registro:$matriz[id]>Listar Cancelados</a>",'listar');
		$texto.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listartodos&registro=$registro:$matriz[id]>Listar Todos</a>",'listar');
		itemLinhaNOURL($texto, 'right', $corFundo, 6, 'tabfundo1');
	fechaLinhaTabela();
	
	# Caso não hajam servicos para o servidor
	if(!$consulta || contaConsulta($consulta)==0) {
		# Não há registros
		itemTabelaNOURL("Não há contratos gerados!", 'left', $corFundo, 6, 'txtaviso');
	}
	else {
		# Cabeçalho
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTabela('Contrato', 'center', '10%', 'tabfundo0');
			itemLinhaTabela('Página', 'center', '30%', 'tabfundo0');
			itemLinhaTabela('Status', 'center', '10%', 'tabfundo0');
			itemLinhaTabela('Data Emissão', 'center', '10%', 'tabfundo0');
			itemLinhaTabela('Validade', 'center', '10%', 'tabfundo0');
			itemLinhaTabela('Opções', 'center', '30%', 'tabfundo0');
		fechaLinhaTabela();

		$i=0;
		
		while($i < contaConsulta($consulta)) {
			# Mostrar registro
			$id=resultadoSQL($consulta, $i, 'id');
			$idContrato=resultadoSQL($consulta, $i, 'idContrato');
			$nomeContrato=resultadoSQL($consulta, $i, 'nomeContrato');
			$nomeServico=resultadoSQL($consulta, $i, 'nomeServico');
			$nomePagina=resultadoSQL($consulta, $i, 'nomePagina');
			$nomeArquivo=resultadoSQL($consulta, $i, 'nomeArquivo');
			$idServicoPlano=resultadoSQL($consulta, $i, 'idServicoPlano');
			$idUsuario=resultadoSQL($consulta, $i, 'idUsuario');
			$dtEmissao=resultadoSQL($consulta, $i, 'dtEmissao');
			$dtRenovacao=resultadoSQL($consulta, $i, 'dtRenovacao');
			$mesValidade=resultadoSQL($consulta, $i, 'mesValidade');
			$numeroContrato=resultadoSQL($consulta, $i, 'numeroContrato');
			$status=resultadoSQL($consulta, $i, 'status');

			if($status=='R') $dtEmissao=$dtRenovacao;
			$dataValidade=validadeContrato($dtEmissao, $mesValidade);
			
			# Verificar se contrato não esta vencido
			if(converteData($data[dataBanco],'banco','timestamp') >= converteData($dataValidade,'banco','timestamp'))
				$opcVencido="<span class=txtaviso>(vencido)</span> ";
			else $opcVencido='';

			$opcoes=htmlMontaOpcao("<a href=$nomeArquivo>Visualizar (PDF)</a>",'pdf');
			$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=renovar&registro=$registro:$id>Renovar</a>",'renovar');
			if($status=='A' || $status=='R' || $status=='I') {
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=cancelar&registro=$registro:$id>Cancelar</a>",'cancelar');
			}

			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela("$numeroContrato", 'left', '10%', 'normal10');
				itemLinhaTabela($opcVencido.$nomePagina, 'left', '40%', 'normal10');
				itemLinhaTabela(formSelectStatusContratos($status,'','check'), 'center', '10%', 'normal10');
				itemLinhaTabela(converteData($dtEmissao, 'banco','formdata'), 'center', '10%', 'normal10');
				itemLinhaTabela(converteData($dataValidade,'banco','formdata'), 'center nowrap', '10%', 'normal10');
				itemLinhaTabela($opcoes, 'left nowrap', '30%', 'normal8');
			fechaLinhaTabela();
			
			# Incrementar contador
			$i++;
		} #fecha laco de montagem de tabela
			
	} #fecha listagem		
		
	fechaTabela();
}



# Função para busca Novo Numero Contrato - mes
function novoNumeroContratoMes() { 

	global $conn, $tb;
	
	$data=dataSistema();
	
	$sql="
		SELECT
			MAX(numeroSequencia)+1 qtde 
		FROM
			$tb[ContratosServicosPlanos]
		WHERE
			left(dtEmissao,7)='$data[ano]-$data[mes]'
	";
	
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
		$retorno=resultadoSQL($consulta, 0, 'qtde');
	}
	else $retorno=1;
	
	if(!is_numeric($retorno)) $retorno=1;
		
	return($retorno);
	
}



# Função para geração de numeração de contratos
function gerarNumeroContratosServicosPlanos() {
	
	global $contratos;
	
	$data=dataSistema();
	
	# mascara do numero de contrato
	$mascara=$contratos[mascara];
	
	if(!$mascara) $mascara="%_MES_%%_NUMERO_%/%_ANO_%";
	
	$mascara=str_replace("%_DIA_%",$data[dia],$mascara);
	$mascara=str_replace("%_MES_%",$data[mes],$mascara);
	$mascara=str_replace("%_ANO_%",$data[ano],$mascara);
	
	#Numero
	$numeroSequencia=novoNumeroContratoMes();
	$numero=exportaDados($numeroSequencia, '', 'right', 3, '0');
	$mascara=str_replace("%_NUMERO_%", $numero, $mascara);
	
	$retorno[mascara]=$mascara;
	$retorno[numeroSequencia]=$numeroSequencia;
	
	return($retorno);
	
}



# Função para visualização / geração de contratos de serviço
function gerarContratoServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $html;
	
	
	# Formulário prévio de preenchimento / visualização / geração
	novaTabela2("[Geração de Contratos de Serviço]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		# Opcoes Adicionais
		menuOpcAdicional($modulo, $sub, $acao, $registro);
		#fim das opcoes adicionais
		novaLinhaTabela($corFundo, '100%');
		$texto="			
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=registro value='$registro:$matriz[id]'>
			&nbsp;";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Data do Contrato: </b><br>
					<span class=normal10>Data específica para o contrato</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[dtContrato] value='$matriz[dtContrato]' size=10 onBlur=verificaData(this.value,4)> <span class=txtaviso>(Exemplo: 23022004 = 23/02/2004)</span>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			$texto="<input type=submit name=matriz[bntVisualizar] value=Visualizar class=submit>";
			$texto.="&nbsp;<input type=submit name=matriz[bntGerar] value='Gerar Contrato' class=submit2>";
			itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
	fechaTabela();
	
	if($matriz[bntVisualizar] || $matriz[bntGerar]) {
		echo "<br>";
		
		if($matriz[bntVisualizar]) $matriz[acao]='visualizar';
		elseif($matriz[bntGerar]) $matriz[acao]='gerar';
		
		verContratosServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
	}
}


# Função para cancelamento de Contratos
function cancelarContratoServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $html, $sessLogin;
	
	$data=dataSistema();

	if(!$matriz[bntCancelar]) {
		# Recebe ID do Serviço do plano
		$consulta=buscaContratosServicosPlanos($matriz[id], 'id','igual','id');
	
		if($consulta && contaConsulta($consulta)>0) {
		
			$id=resultadoSQL($consulta, 0, 'id');
			$idContrato=resultadoSQL($consulta, 0, 'idContrato');
			$idServicoPlano=resultadoSQL($consulta, 0, 'idServicoPlano');
			$idUsuario=resultadoSQL($consulta, 0, 'idUsuario');
			$dtEmissao=resultadoSQL($consulta, 0, 'dtEmissao');
			$dtRenovacao=resultadoSQL($consulta, 0, 'dtRenovacao');
			$dtCancelamento=resultadoSQL($consulta, 0, 'dtCancelamento');
			$idUsuarioCancelamento=resultadoSQL($consulta, 0, 'idUsuarioCancelamento');
			$mesValidade=resultadoSQL($consulta, 0, 'mesValidade');
			$nomeArquivo=resultadoSQL($consulta, 0, 'nomeArquivo');
			$numeroContrato=resultadoSQL($consulta, 0, 'numeroContrato');
			$numeroSequencia=resultadoSQL($consulta, 0, 'numeroSequencia');
			$nomePagina=resultadoSQL($consulta, 0, 'nomePagina');
			$status=resultadoSQL($consulta, 0, 'status');
			
			$dataValidade=validadeContrato($dtEmissao, $mesValidade);
			
			$servicoPlano=dadosServicoPlano($idServicoPlano);

			# Formulário prévio de preenchimento / visualização / geração
			novaTabela2("[Cancelamento de Contrato]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, $registro);
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value='$registro:$id'>
					<input type=hidden name=matriz[idContratoServicoPlano] value=$id'>
					&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaForm("<b>Número do Contrato:</b>", 'right', 'top', $corFundo, 0, 'tabfundo1');
					itemLinhaForm($numeroContrato, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaForm("<b>Nome da Página:</b>", 'right', 'top', $corFundo, 0, 'tabfundo1');
					itemLinhaForm($nomePagina, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaForm("<b>Serviço:</b>", 'right', 'top', $corFundo, 0, 'tabfundo1');
					itemLinhaForm(formSelectServicos($servicoPlano[idServico],'','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaForm("<b>Data de Emissão:</b>", 'right', 'top', $corFundo, 0, 'tabfundo1');
					itemLinhaForm(converteData($dtEmissao, 'banco','formdata'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaForm("<b>Validade:</b>", 'right', 'top', $corFundo, 0, 'tabfundo1');
					itemLinhaForm(converteData($dataValidade,'banco','formdata'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaForm('&nbsp;', 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit name=matriz[bntCancelar] value=Cancelar class=submit2>";
					itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		}
	}
	else {
	
		# Data do cancelamento
		if(!$matriz[dtCancelamento] || strlen(trim($matriz[dtCancelamento])<10))
			$matriz[dtCancelamento]=$data[dataBanco];
		else 		
			$matriz[dtCancelamento]=converteData($matriz[dtCancelamento],'form','banco');
		
		# ID do usuario
		$matriz[idUsuario]=buscaIDUsuario($sessLogin[login],'login','igual','id');
		
		$grava=dbContratoServicoPlano($matriz, 'cancelar');
		
		if($grava) {
			# OK
			$msg="Contrato Cancelado com sucesso!";
			$url="?modulo=$modulo&sub=$sub&acao=listar&registro=$registro";
			avisoNOURL("Aviso", $msg, 600);
			echo "<br>";
			
			listarGeradosContratosServicosPlanos($modulo, $sub, 'listar', $registro, $matriz);
		}
		else {
			# Erro
			$msg="ERRO ao alterar serviço! Tente novamente!";
			$url="?modulo=$modulo&sub=$sub&acao=abrir&registro=$matriz[idPlano]";
			avisoNOURL("Aviso", $msg, 400);
		}
	}
}



# Função para renovação de Contratos
function renovarContratoServicosPlanos($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $html, $sessLogin;
	
	$data=dataSistema();

	if(!$matriz[bntRenovar]) {
		# Recebe ID do Serviço do plano
		$consulta=buscaContratosServicosPlanos($matriz[id], 'id','igual','id');
	
		if($consulta && contaConsulta($consulta)>0) {
		
			$id=resultadoSQL($consulta, 0, 'id');
			$idContrato=resultadoSQL($consulta, 0, 'idContrato');
			$idServicoPlano=resultadoSQL($consulta, 0, 'idServicoPlano');
			$idUsuario=resultadoSQL($consulta, 0, 'idUsuario');
			$dtEmissao=resultadoSQL($consulta, 0, 'dtEmissao');
			$dtRenovacao=resultadoSQL($consulta, 0, 'dtRenovacao');
			$dtCancelamento=resultadoSQL($consulta, 0, 'dtCancelamento');
			$idUsuarioCancelamento=resultadoSQL($consulta, 0, 'idUsuarioCancelamento');
			$mesValidade=resultadoSQL($consulta, 0, 'mesValidade');
			$nomeArquivo=resultadoSQL($consulta, 0, 'nomeArquivo');
			$numeroContrato=resultadoSQL($consulta, 0, 'numeroContrato');
			$numeroSequencia=resultadoSQL($consulta, 0, 'numeroSequencia');
			$nomePagina=resultadoSQL($consulta, 0, 'nomePagina');
			$status=resultadoSQL($consulta, 0, 'status');
			
			$dataValidade=validadeContrato($dtEmissao, $mesValidade);
			
			$servicoPlano=dadosServicoPlano($idServicoPlano);
			
			$dataRenovacao=$data[dataNormalData];
			
			# Formulário prévio de preenchimento / visualização / geração
			novaTabela2("[Renovação de Contrato]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, $registro);
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value='$registro:$id'>
					<input type=hidden name=matriz[idContratoServicoPlano] value=$id'>
					&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaForm("<b>Número do Contrato:</b>", 'right', 'middle', $corFundo, 0, 'tabfundo1');
					itemLinhaForm($numeroContrato, 'left', 'middle', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaForm("<b>Nome da Página:</b>", 'right', 'middle', $corFundo, 0, 'tabfundo1');
					itemLinhaForm($nomePagina, 'left', 'middle', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaForm("<b>Serviço:</b>", 'right', 'middle', $corFundo, 0, 'tabfundo1');
					itemLinhaForm(formSelectServicos($servicoPlano[idServico],'','check'), 'left', 'middle', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaForm("<b>Data de Emissão:</b>", 'right', 'middle', $corFundo, 0, 'tabfundo1');
					itemLinhaForm(converteData($dtEmissao, 'banco','formdata'), 'left', 'middle', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaForm("<b>Validade:</b>", 'right', 'middle', $corFundo, 0, 'tabfundo1');
					itemLinhaForm(converteData($dataValidade,'banco','formdata'), 'left', 'middle', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaForm("<b>Data de Renovação:</b>", 'right', 'middle', $corFundo, 0, 'tabfundo1');
					$indice=5;
					$texto="<input type=text name=matriz[dtRenovacao] size=10 value='$dataRenovacao' onBlur=verificaData(this.value,$indice)>";
					itemLinhaForm($texto, 'left', 'middle', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaForm("<b>Validade:</b><br><span class=normal>Validade em meses do contrato</span>", 'right', 'top', $corFundo, 0, 'tabfundo1');
					$indice=5;
					$texto="<input type=text name=matriz[mesValidade] size=3 value='$mesValidade'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaForm('&nbsp;', 'center', 'middle', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit name=matriz[bntRenovar] value=Renovar class=submit>";
					itemLinhaForm($texto, 'center', 'middle', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		}
	}
	else {
	
		# Data do cancelamento
		if(!$matriz[dtRenovacao]) {
			$matriz[dtRenovacao]=$data[dataBanco];
		}
		else {
			$matriz[dtRenovacao]=converteData($matriz[dtRenovacao],'form','banco');
		}
			
		# ID do usuario
		$matriz[idUsuario]=buscaIDUsuario($sessLogin[login],'login','igual','id');
		
		$grava=dbContratoServicoPlano($matriz, 'renovar');
		
		if($grava) {
			# OK
			$msg="Contrato Renovado com sucesso!";
			$url="?modulo=$modulo&sub=$sub&acao=listar&registro=$registro";
			avisoNOURL("Aviso", $msg, 600);
			echo "<br>";
			
			listarGeradosContratosServicosPlanos($modulo, $sub, 'listar', $registro, $matriz);
		}
		else {
			# Erro
			$msg="ERRO ao renovar contrato! Tente novamente!";
			$url="?modulo=$modulo&sub=$sub&acao=listar&registro=$registro";
			avisoNOURL("Aviso", $msg, 400);
		}
	}
}




# Função para montar parcelas do Serviço Plano
function parcelasServicoPlano($idServicoPlano) {

	if($idServicoPlano) {
		# Tipo 2 = Taxas de Instalação
		$consulta=buscaServicosAdicionais("idServicoPlano=$idServicoPlano AND idTipoServicoAdicional=2",'','custom','id');
		
		if($consulta && contaConsulta($consulta)>0) {
			$retorno[qtde]=contaConsulta($consulta);
			
			$total=0;
			for($a=0;$a<contaConsulta($consulta);$a++) {
				
				$valor=resultadoSQL($consulta, $a, 'valor');
				$dtVencimento=resultadoSQL($consulta, $a, 'dtVencimento');
				$status=resultadoSQL($consulta, $a, 'status');
				
				if($status=='A') {
					if(!$retorno[valor_parcela]) $retorno[valor_parcela]=formatarValoresForm(resultadoSQL($consulta, 0, 'valor'));
					
					$total+=$valor;
					$retorno[parcelas].=converteData($dtVencimento, 'banco','formdata');
					$retorno[parcelas].=" - ";
					$retorno[parcelas].=formatarValoresForm($valor);
					$retorno[parcelas].="<br>";
				}
			}
			
			$retorno[valor_total]=formatarValoresForm($total);
			
		}
	}
	
	return($retorno);
}

?>
