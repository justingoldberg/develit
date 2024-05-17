<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 10/09/2003
# Ultima alteração: 22/03/2004
#    Alteração No.: 010
#
# Função:
#    Painel - Funções para cadastro Documentos Gerados


# Função para geração de documentos de cobrança
function gerarDocumentosCobranca($matriz) {

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
			and $tb[Pessoas].idPOP = $tb[POP].id
			AND $tb[PlanosPessoas].dtCadastro <= '$dtCadastroPlano'
			AND $tb[POP].status='A'
			AND $tb[PlanosPessoas].status='A'
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
		novaTabela2("[Documentos Gerados]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		
		# Listagem de Planos com servicos e totais por serviço
		for($a=0;$a<contaConsulta($consultaPlanosAtivos);$a++) {
			# Consultar Planos da pessoa
			$idPessoaTipo=resultadoSQL($consultaPlanosAtivos, $a, 'idPessoaTipo');
			$nomePessoa=resultadoSQL($consultaPlanosAtivos, $a, 'nome');
			$nomePOP=resultadoSQL($consultaPlanosAtivos, $a, 'pop');
			# Mostrar Cliente
			
			# Incluir Documentos gerados
			$matriz[idPessoaTipo]=$idPessoaTipo;
			$matriz[idDocumentoGerado]=novoIDDocumentoGerado();
			$documentoGerado=dbDocumentoGerado($matriz, 'incluir');
			
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

						# Status do plano a ser repassado
						$matriz[status]=$status;

						# Variáveis para geração de planos dos documentos gerados
						$matriz[idPlanoDocumentoGerado]=novoIDPlanoDocumentoGerado();
						$matriz[idPlano]=$idPlano;
						$matriz[idVencimento]=$idVencimento;
						$matriz[idFormaCobranca]=$idFormaCobranca;

						# Totalizar plano
						$totalPlano=valorPlanoDocumentosGerados($idPlano, $especial, $idVencimento, $sqlADDPlano, $matriz[mes], $matriz[ano], $matriz);

						# Data de vencimento
						if($vencimento[diaVencimento] < $vencimento[diaFaturamento]) {
							$dtVencimentoDocumento=mktime(0,0,0,($matriz[mes]+1),$vencimento[diaVencimento],$matriz[ano]);
							$dtVencimentoDocumento=date('Y-m-d',$dtVencimentoDocumento);
						}
						else {
							$dtVencimentoDocumento=mktime(0,0,0,($matriz[mes]),$vencimento[diaVencimento],$matriz[ano]);
							$dtVencimentoDocumento=date('Y-m-d',$dtVencimentoDocumento);
						}
						$matriz[dtVencimentoPlanoDocumentoGerado]=$dtVencimentoDocumento;
						# Incluir plano do Documento Gerado
						$gravaPlanoDocumentoGerado=dbPlanoDocumentoGerado($matriz, 'incluir');						
						
						$totalCliente+=$totalPlano;
					}
			
					# Contabilizar Total geral	
					$totalGeral+=$totalCliente;
				}
			}
			
		}
		
		# Totalizar Documento Gerado
		//$totalGeral=valorDocumentosGerados($matriz[idDocumentoGerado]);
		$totalGeral=valorFaturamento($matriz[idFaturamento]);
		
		# Rodapé com totais
		htmlAbreLinha($corFundo);
			htmlAbreColunaForm('100%', 'center', 'middle', $corFundo, 2, 'normal10');		
				novaTabelaSH("center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
					htmlAbreLinha($corFundo);
						itemLinhaTMNOURL("<b>Total Geral de Faturamento </b>", 'right', 'middle', '50%', $corFundo, 0, 'tabfundo1');
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




# Função para geração de documentos de cobrança
function gerarDocumentosCobrancaCliente($matriz) {

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
	
	
	# Cabeçalho
	echo "<br>";
	novaTabela2("[Documentos Gerados]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	
	# Consultar Planos da pessoa
	$idPessoaTipo=$matriz[idPessoaTipo];
	# Mostrar Cliente
	
	# Incluir Documentos gerados
	$matriz[idPessoaTipo]=$idPessoaTipo;
	$matriz[idDocumentoGerado]=novoIDDocumentoGerado();
	$documentoGerado=dbDocumentoGerado($matriz, 'incluir');
	
	# Consultar Planos ativos para a pessoa
	if($idPessoaTipo) {
		$sql="
			SELECT
				$tb[PlanosPessoas].idPessoaTipo idPessoaTipo, 
				$tb[PlanosPessoas].id idPlano, 
				$tb[PlanosPessoas].nome nome,
				$tb[PlanosPessoas].nome nome,
				$tb[POP].nome nomePOP,
				$tb[PlanosPessoas].idFormaCobranca idFormaCobranca, 
				$tb[PlanosPessoas].idVencimento idVencimento, 
				$tb[PlanosPessoas].especial especial,
				$tb[PlanosPessoas].status status
			FROM 
				$tb[PlanosPessoas],
				$tb[Pessoas],
				$tb[POP],
				$tb[PessoasTipos],
				$tb[ServicosPlanos]
			WHERE
				$tb[ServicosPlanos].idPlano = $tb[PlanosPessoas].id
				AND $tb[Pessoas].idPOP = $tb[POP].id
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
				$nomePOP=resultadoSQL($consulta, $b, 'nomePOP');
				$nome=htmlMontaOpcao("<a href=?modulo=lancamentos&sub=planos&acao=abrir&registro=$idPlano>$nome</a>",'planos');
				$especial=resultadoSQL($consulta, $b, 'especial');
				if(!$especial || $especial=='N') $tipoPlano="<span class=txtok>Plano Normal</span>";
				else $tipoPlano="<span class=txtaviso>Plano Especial</span>";
				$idFormaCobranca=resultadoSQL($consulta, $b, 'idFormaCobranca');
				$idVencimento=resultadoSQL($consulta, $b, 'idVencimento');
				$vencimento=dadosVencimento($idVencimento);
				$idPessoaTipo=resultadoSQL($consulta, $b, 'idPessoaTipo');

				# Status do plano a ser repassado
				$matriz[status]=$status;

				# Variáveis para geração de planos dos documentos gerados
				$matriz[idPlanoDocumentoGerado]=novoIDPlanoDocumentoGerado();
				$matriz[idPlano]=$idPlano;
				$matriz[idVencimento]=$idVencimento;
				$matriz[idFormaCobranca]=$idFormaCobranca;

				# Totalizar plano
				$totalPlano=valorPlanoDocumentosGerados($idPlano, $especial, $idVencimento, $sqlADDPlano, $matriz[mes], $matriz[ano], $matriz);

				# Data de vencimento
				if($vencimento[diaVencimento] < $vencimento[diaFaturamento]) {
					$dtVencimentoDocumento=mktime(0,0,0,($matriz[mes]+1),$vencimento[diaVencimento],$matriz[ano]);
					$dtVencimentoDocumento=date('Y-m-d',$dtVencimentoDocumento);
				}
				else {
					$dtVencimentoDocumento=mktime(0,0,0,($matriz[mes]),$vencimento[diaVencimento],$matriz[ano]);
					$dtVencimentoDocumento=date('Y-m-d',$dtVencimentoDocumento);
				}					
					
				$matriz[dtVencimentoPlanoDocumentoGerado]=$dtVencimentoDocumento;
				
				# Incluir plano do Documento Gerado
				$gravaPlanoDocumentoGerado=dbPlanoDocumentoGerado($matriz, 'incluir');
				
				$totalCliente+=$totalPlano;
			}
	
			# Contabilizar Total geral	
			$totalGeral+=$totalCliente;
		}
		
		# Totalizar Documento Gerado
		//$totalGeral=valorDocumentosGerados($matriz[idDocumentoGerado]);
		$totalGeral=valorFaturamento($matriz[idFaturamento]);
		
		# Rodapé com totais
		htmlAbreLinha($corFundo);
			htmlAbreColunaForm('100%', 'center', 'middle', $corFundo, 2, 'normal10');		
				novaTabelaSH("center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
					htmlAbreLinha($corFundo);
						itemLinhaTMNOURL("<b>Total Faturado </b>", 'right', 'middle', '50%', $corFundo, 0, 'tabfundo1');
						itemLinhaTMNOURL(formatarValoresForm($totalGeral), 'center', 'middle', '40%', $corFundo, 0, 'txtok');
					htmlFechaLinha();
				fechaTabela();
			htmlFechaColuna();
		fechaLinhaTabela();
		fechaTabela();


		return($totalGeral);
	
		
	}
	
}



# Função para manuteção de Documentos Gerados
function dbDocumentoGerado($matriz, $tipo) {

	global $conn, $tb, $sessLogin;
	
	$data=dataSistema();
	
	$idUsuario=buscaIDUsuario($sessLogin[login],'login','igual','id');
	
	if($tipo=='incluir') {
		$seqGeracao=0;
		$seqDocumento=0;
		
		# incluir registro
		$sql="
			INSERT INTO 
				$tb[DocumentosGerados]
			VALUES (
				$matriz[idDocumentoGerado],
				$matriz[idFaturamento],
				$seqGeracao,
				$seqDocumento,
				$matriz[idPessoaTipo],
				$idUsuario,
				'$data[dataBanco]',
				'',
				'I'
			)";
	}
	
	if($tipo=='incluirAvulso') {
		$seqGeracao=0;
		$seqDocumento=0;
		
		# incluir registro
		$sql="
			INSERT INTO 
				$tb[DocumentosGerados]
			VALUES (
				$matriz[idDocumentoGerado],
				$matriz[idFaturamento],
				$seqGeracao,
				$seqDocumento,
				$matriz[idPessoaTipo],
				$idUsuario,
				'$data[dataBanco]',
				'',
				'$matriz[status]'
			)";
	}
	
	elseif($tipo=='excluir') {
		$seqGeracao=0;
		$seqDocumento=0;
		
		# incluir registro
		$sql="
			DELETE FROM
				$tb[DocumentosGerados]
			WHERE
				id=$matriz[id]
		";
	}
	
	
	if($sql) {
		$consulta=consultaSQL($sql, $conn);
	}
	
	return($consulta);
}


# Funçao para novo ID de Faturamento
function novoIDDocumentoGerado() {

	global $conn, $tb;
	
	$sql="
		SELECT 
			MAX($tb[DocumentosGerados].id)+1 id
		FROM
			$tb[DocumentosGerados]";
	
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




# função de busca 
function buscaDocumentosGerados($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[DocumentosGerados] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[DocumentosGerados] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[DocumentosGerados] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[DocumentosGerados] WHERE $texto ORDER BY $ordem";
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



# Função para listagem de documentos gerados
function listarDocumentosGerados($modulo, $sub, $acao, $registro, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessPlanos, $conn, $tb;

	$consulta=buscaDocumentosGerados($matriz[idFaturamento], 'idFaturamento','igual','dtGeracao');
	
	# Cabeçalho		
	# Motrar tabela de busca
	novaTabela("[Documentos Gerados]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
	
	# Caso não hajam servicos para o servidor
	if(!$consulta || contaConsulta($consulta)==0) {
		# Não há registros
		itemTabelaNOURL('Não há documentos gerados neste faturamento', 'left', $corFundo, 5, 'txtaviso');
	}
	else {

		# Paginador
		$urlADD="&matriz[idFaturamento]=$matriz[idFaturamento]";
		paginador($consulta, contaConsulta($consulta), $limite[lista][documentos_gerados], $registro, 'normal10', 5, $urlADD);
	
		# Cabeçalho
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTabela('Nome do Cliente', 'center', '35%', 'tabfundo0');
			itemLinhaTabela('Valor', 'center', '15%', 'tabfundo0');
			itemLinhaTabela('Status', 'center', '10%', 'tabfundo0');
			itemLinhaTabela('Geração', 'center', '20%', 'tabfundo0');
			itemLinhaTabela('Opções', 'center', '20%', 'tabfundo0');
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

		$limite=$i+$limite[lista][documentos_gerados];
		
		//se exibe o impressao de boleto
		$bolImpressao = imprimirBoletoFaturamento( $matriz[idFaturamento] );

		while($i < contaConsulta($consulta) && $i < $limite) {

			# Mostrar registro
			$id=resultadoSQL($consulta, $i, 'id');
			$idFaturamento=resultadoSQL($consulta, $i, 'idFaturamento');
			$idPessoaTipo=resultadoSQL($consulta, $i, 'idPessoaTipo');
			$consultaPessoa=buscaPessoas($idPessoaTipo,"$tb[PessoasTipos].id",'igual','id');
			if($consultaPessoa && contaConsulta($consultaPessoa)>0) $nomePessoa=resultadoSQL($consultaPessoa, 0, 'nome');
			$idUsuario=resultadoSQL($consulta, $i, 'idUsuario');
			$dtGeracao=resultadoSQL($consulta, $i, 'dtGeracao');
			$status=resultadoSQL($consulta, $i, 'status');
			
			# Valor
			$valor=valorDocumentosGerados($id);
			$total+=$valor;

			if($status=='I') { 
				$opcoes = htmlMontaOpcao("<a href=?modulo=$modulo&sub=documentos&acao=cancelar&registro=$id>Cancelar</a>",'cancelar');
				if ($bolImpressao ) $opcoes.= htmlMontaOpcao("<a href=?modulo=faturamento&sub=boletobancario&acao=gerar&matriz[tipo]=documentosGerados&registro=$id>Imprimir Boleto</a>",'imprimir');
			}
			else {
				$opcoes="&nbsp;";
			}
			
			# Valor Formatado
			$valorFormatado=formatarValoresForm($valor);
			$valorFormatado=htmlMontaOpcao("<a href=?modulo=$modulo&sub=documentos&acao=detalhe&registro=$id&matriz[idFaturamento]=$idFaturamento>$valorFormatado</a>",'lancamento');
			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela($nomePessoa, 'left', '35%', 'normal10');
				itemLinhaTabela($valorFormatado, 'center', '15%', 'normal10');
				itemLinhaTabela(formSelectStatus($status,'','check'), 'center', '10%', 'normal10');
				itemLinhaTabela(converteData($dtGeracao,'banco','form'), 'center', '20%', 'normal10');
				itemLinhaTabela($opcoes, 'left', '20%', 'normal8');
			fechaLinhaTabela();
			
			# Incrementar contador
			$i++;
			
		} #fecha laco de montagem de tabela
		
	} #fecha servicos encontrados
	
	# Sub-Total
	novaLinhaTabela($corFundo, '100%');
		itemLinhaTabela("Sub-Total", 'right', '35%', 'tabfundo0');
		itemLinhaTabela(formatarValoresForm($total), 'center', '15%', 'txtaviso');
		itemLinhaForm('&nbsp;', 'center', 'middle', $corFundo, 3, 'tabfundo0');
	fechaLinhaTabela();	
	
	# Total
	$total=0;
	for($a=0;$a<contaConsulta($consulta);$a++) {
		# Mostrar registro
		$id=resultadoSQL($consulta, $a, 'id');
		
		# Valor
		$valor=valorDocumentosGerados($id);
		$total+=$valor;		
	}
	
	# Total
	novaLinhaTabela($corFundo, '100%');
		itemLinhaTabela("Total", 'right', '35%', 'tabfundo0');
		itemLinhaTabela(formatarValoresForm($total), 'center', '15%', 'txtok');
		itemLinhaForm('&nbsp;', 'center', 'middle', $corFundo, 3, 'tabfundo0');
	fechaLinhaTabela();	
	
	fechaTabela();
}


# Função para totalização de valor do documento
function valorDocumentosGerados($idDocumento) {

	global $conn, $tb;
	
	$sql="
		SELECT
			SUM($tb[ServicosPlanosDocumentosGerados].valor) valor
		FROM
			$tb[ServicosPlanosDocumentosGerados],
			$tb[PlanosDocumentosGerados],
			$tb[DocumentosGerados]
		WHERE
			$tb[DocumentosGerados].id=$tb[PlanosDocumentosGerados].idDocumentoGerado
			AND $tb[PlanosDocumentosGerados].id=$tb[ServicosPlanosDocumentosGerados].idPlanoDocumentoGerado
			AND $tb[DocumentosGerados].id=$idDocumento
	";
	
	
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
		$valor=resultadoSQL($consulta, 0, 'valor');
	}
	
	return($valor);
}




# Função para detalhar o Faturamento
function detalhesDocumentosGerados($modulo, $sub, $acao, $registro, $matriz) {

	# Mostrar detalhes do faturamento
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessPlanos, $conn, $tb;
	
	# Detalhes do Faturamento
	verFaturamento($modulo, $sub, $acao, $matriz[idFaturamento], $matriz);
	
	echo "<br>";
	
	# Informações sobre documento
	verDocumentosGerados($modulo, $sub, $acao, $registro, $matriz);
	
	echo "<br>";

	# Listar Planos do Documento
	listarPlanosDocumentosGerados($modulo, $sub, $acao, $registro, $matriz);
	$matriz[idDocumentoGerado]=$registro;
	
	
}



# Função para visualização de documentos
function verDocumentosGerados($modulo, $sub, $acao, $registro, $matriz) {

	# Mostrar detalhes do faturamento
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessPlanos, $conn, $tb;
	
	# Informações sobre documento
	
	# Cabeçalho		
	# Motrar tabela de busca
	novaTabela2("[Detalhes do Documento Gerado]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=geracao&acao=detalhes&registro=&matriz[idFaturamento]=$matriz[idFaturamento]>Listar Documentos Gerados</a>",'listar');
	itemTabelaNOURL($opcoes, 'right', $corFundo, 2, 'tabfundo1');

	if($registro) {
		$consulta=buscaDocumentosGerados($registro, 'id','igual','id');
		
		# Caso não hajam servicos para o servidor
		if(!$consulta || contaConsulta($consulta)==0) {
			# Não há registros
			itemTabelaNOURL('Não há documentos gerados neste faturamento', 'left', $corFundo, 2, 'txtaviso');
		}
		else {
		
			#dados do faturamento
			$id=resultadoSQL($consulta, 0, 'id');
			$idFaturamento=resultadoSQL($consulta, 0, 'idFaturamento');
			$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
			$consultaPessoa=buscaPessoas($idPessoaTipo,"$tb[PessoasTipos].id",'igual','id');
			if($consultaPessoa && contaConsulta($consultaPessoa)>0) $nomePessoa=resultadoSQL($consultaPessoa, 0, 'nome');
			$idUsuario=resultadoSQL($consulta, 0, 'idUsuario');
			$dtGeracao=resultadoSQL($consulta, 0, 'dtGeracao');
			$dtAtivacao=resultadoSQL($consulta, 0, 'dtAtivacao');
			$status=resultadoSQL($consulta, 0, 'status');
			
			# Cabeçalho
			itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('<b>Cliente:</b>', 'right', '30%', 'tabfundo1');
				itemLinhaTabela($nomePessoa, 'left', '70%', 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('<b>Valor do Documento:</b>', 'right', '30%', 'tabfundo1');
				itemLinhaTabela(formatarValoresForm(valorDocumentosGerados($id)), 'left', '70%', 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('<b>Status:</b>', 'right', '30%', 'tabfundo1');
				itemLinhaTabela(formSelectStatus($status, '','check'), 'left', '70%', 'tabfundo1');
			fechaLinhaTabela();
			
		} #fecha servicos encontrados
	}
	else {
		# Não há registros
		itemTabelaNOURL('Documento não informado!', 'left', $corFundo, 2, 'txtaviso');
	}
	
	fechaTabela();
}

function documentosGeradosDadosId( $id ){
	$consulta=buscaDocumentosGerados($id, 'id','igual','id');
	
	$ret = documentosGeradosDocumentos( $consulta );
	
	return $ret;
}

/**
 * carrega os dados da consulta em uma matriz
 *
 * @param unknown_type $consulta
 * @return array
 */
function documentosGeradosDocumentos( $consulta ){
	global $tb, $conn;
	$ret = array();
	
	if( $consulta && contaConsulta( $consulta ) > 0 ){
		$ret['id']				=resultadoSQL($consulta, 0, 'id');
		$ret['idFaturamento']	=resultadoSQL($consulta, 0, 'idFaturamento');
		$ret['idPessoaTipo']	=resultadoSQL($consulta, 0, 'idPessoaTipo');
//		$ret['consultaPessoa']	=buscaPessoas($ret['idPessoaTipo'],"$tb[PessoasTipos].id",'igual','id');
		$ret['idUsuario']		=resultadoSQL($consulta, 0, 'idUsuario');
		$ret['dtGeracao']		=resultadoSQL($consulta, 0, 'dtGeracao');
		$ret['dtAtivacao']		=resultadoSQL($consulta, 0, 'dtAtivacao');
		$ret['status']			=resultadoSQL($consulta, 0, 'status');
	}
	
	return $ret;
}

# Funçao para busca de informações do vencimento
function dadosDocumentoGerado( $idDocumentoGerado ) {

	$consulta=buscaDocumentosGerados($idDocumentoGerado, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		# dados do vencimento
		$retorno[id]			= resultadoSQL($consulta, 0, 'id');
		$retorno[idFaturamento]	= resultadoSQL($consulta, 0, 'idFaturamento');
		$retorno[seqGeracao]	= resultadoSQL($consulta, 0, 'seqGeracao');
		$retorno[seqDocumento]	= resultadoSQL($consulta, 0, 'seqDocumento');
		$retorno[idPessoaTipo]	= resultadoSQL($consulta, 0, 'idPessoaTipo');
		$retorno[idUsuario]		= resultadoSQL($consulta, 0, 'idUsuario');
		$retorno[dtGeracao]	= resultadoSQL($consulta, 0, 'dtGeracao');
		$retorno[dtAtivacao]			= resultadoSQL($consulta, 0, 'dtAtivacao');
		$retorno[status]			= resultadoSQL($consulta, 0, 'status');
	}
	
	return($retorno);
}

function calculaValorDescontoGerarCobranca ($idDocumentoGerado, $porcentagemDesconto) {
	
	$consultaDocumentosGerados = buscaPlanosDocumentosGerados($idDocumentoGerado, 'idDocumentoGerado', 'igual', 'id');
		
	for($i=0;$i<contaConsulta($consultaDocumentosGerados);$i++) {
		$temDesconto = 0;
		$idPlano = resultadoSQL($consultaDocumentosGerados, $i , 'idPlano');
		$consultPlanos = buscaPlanos($idPlano, 'id', 'igual', 'id');
		
		$temDesconto = resultadoSQL($consultPlanos, 0, 'desconto');
		
		if ($temDesconto) {
			$valorDesconto += valorPlanosDocumentosGerados(resultadoSQL($consultaDocumentosGerados, $i, 'id')) * $porcentagemDesconto;
		}
		
	}
	
	return $valorDesconto;
}

?>