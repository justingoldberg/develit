<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 17/09/2003
# Ultima alteração: 29/01/2004
#    Alteração No.: 007
#
# Função:
#    Painel - Funções para faturamentos



# Função para detalhar faturamento do cliente
function detalhesFaturamentoCliente($modulo, $sub, $acao, $registro, $matriz) {

	# Mostrar detalhes do faturamento
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessPlanos, $conn, $tb;

	if($acao=='detalhes') {

		# Detalhes do documento
		$consulta=buscaContasReceber($registro, 'id','igual','id');
		
		if($consulta && contaConsulta($consulta)>0) {
			
			$idDocumentosGerados=resultadoSQL($consulta, 0, 'idDocumentosGerados');
	
			# Informações sobre faturamento
			$consultaDocumentosGerados=buscaDocumentosGerados($idDocumentosGerados, 'id','igual','id');
			
			if($consultaDocumentosGerados && contaConsulta($consultaDocumentosGerados)>0) {
			
				$idFaturamento=resultadoSQL($consultaDocumentosGerados, 0, 'idFaturamento');
				$idPessoaTipo=resultadoSQL($consultaDocumentosGerados, 0, 'idPessoaTipo');
				
				verPessoas('cadastros', 'clientes', 'ver', $idPessoaTipo, $matriz);
				echo "<br>";
				
				$matriz[idFaturamento]=$idFaturamento;
	
				# Listar Planos do Documento Gerado
				listarPlanosDocumentosGeradosCliente($modulo, $sub, $acao, $idDocumentosGerados, $matriz) ;
			}
		}
	}
	
	# Mostrar servicos do Plano faturado
	elseif($acao=='detalhes_planos') {
	
		$consulta=buscaPlanosDocumentosGerados($registro, 'id', 'igual','id');
		
		if($consulta && contaConsulta($consulta)>0) {
			# Dados do plano
			$idDocumentoGerado=resultadoSQL($consulta, 0, 'idDocumentoGerado');

			# Informações sobre faturamento
			$consultaDocumentosGerados=buscaDocumentosGerados($idDocumentoGerado, 'id','igual','id');
			
			if($consultaDocumentosGerados && contaConsulta($consultaDocumentosGerados)>0) {
			
				$idFaturamento=resultadoSQL($consultaDocumentosGerados, 0, 'idFaturamento');
				$idPessoaTipo=resultadoSQL($consultaDocumentosGerados, 0, 'idPessoaTipo');
				
				verPessoas('cadastros', 'clientes', 'ver', $idPessoaTipo, $matriz);
				echo "<br>";
				
				$matriz[idFaturamento]=$idFaturamento;
	
				# Listar Planos do Documento Gerado
				listarPlanosDocumentosGeradosCliente($modulo, $sub, $acao, $idDocumentoGerado, $matriz) ;
				echo "<br>";
			
				# Buscar informações 
				listarServicosPlanosDocumentosGeradosCliente($modulo, $sub, $acao, $registro, $matriz);
			}
		}
	
	}
}





# Função para listagem de documentos gerados
function listarPlanosDocumentosGeradosCliente($modulo, $sub, $acao, $registro, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;

	$consulta=buscaPlanosDocumentosGerados($registro, 'idDocumentoGerado','igual','id');
	
	# Cabeçalho		
	# Motrar tabela de busca
	novaTabela("[Planos do Documento Gerado]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
	
	# Caso não hajam servicos para o servidor
	if(!$consulta || contaConsulta($consulta)==0) {
		# Não há registros
		itemTabelaNOURL('Não há planos no documento informado!', 'left', $corFundo, 3, 'txtaviso');
	}
	else {

		# Cabeçalho
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTabela('Nome do Plano', 'center', '45%', 'tabfundo0');
			itemLinhaTabela('Valor', 'center', '20%', 'tabfundo0');
			itemLinhaTabela('Forma de Cobrança', 'center', '35%', 'tabfundo0');
		fechaLinhaTabela();

		
		for($i=0;$i<contaConsulta($consulta);$i++) {

			# Mostrar registro
			$id=resultadoSQL($consulta, $i, 'id');
			$idDocumentoGerado=resultadoSQL($consulta, $i, 'idDocumentoGerado');
			$idPlano=resultadoSQL($consulta, $i, 'idPlano');
			$plano=dadosPlanos($idPlano);
			$idFormaCobranca=resultadoSQL($consulta, $i, 'idFormaCobranca');
			$idVencimento=resultadoSQL($consulta, $i, 'idVencimento');
			$dtVencimento=resultadoSQL($consulta, $i, 'dtVencimento');
			
			# Valor
			$valor=valorPlanosDocumentosGerados($id);
			$total+=$valor;

			# Valor Formatado
			$valorFormatado=formatarValoresForm($valor);
			$valorFormatado=htmlMontaOpcao("<a href=?modulo=$modulo&sub=clientes&acao=detalhes_planos&registro=$id>$valorFormatado</a>",'lancamento');
			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela($plano[nome], 'left', '45%', 'normal10');
				itemLinhaTabela($valorFormatado, 'center', '20%', 'normal10');
				itemLinhaTabela(formSelectFormaCobranca($idFormaCobranca,'','check'), 'center', '35%', 'normal10');
			fechaLinhaTabela();
			
		} #fecha laco de montagem de tabela
		
	} #fecha servicos encontrados
	
	# Total
	novaLinhaTabela($corFundo, '100%');
		itemLinhaTabela("Total", 'right', '35%', 'tabfundo0');
		itemLinhaTabela(formatarValoresForm($total), 'center', '15%', 'txtok');
		itemLinhaForm('&nbsp;', 'center', 'middle', $corFundo, 3, 'tabfundo0');
	fechaLinhaTabela();	
	
	fechaTabela();
}




# Função para listagem de documentos gerados
function listarServicosPlanosDocumentosGeradosCliente($modulo, $sub, $acao, $registro, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $conn, $tb;

	//$consulta=buscaServicosPlanosDocumentos($registro, 'idPlanoDocumentoGerado','igual','id');
	
	$sql="
		SELECT
			$tb[ServicosPlanosDocumentosGerados].id id,
			$tb[ServicosPlanosDocumentosGerados].idPlanoDocumentoGerado idPlanoDocumentoGerado,
			$tb[ServicosPlanosDocumentosGerados].idServicosPlanos idServicosPlanos,
			$tb[ServicosPlanosDocumentosGerados].valor valor,
			$tb[Servicos].id idServico,
			$tb[Servicos].nome nome,
			$tb[Servicos].valor valorServico,
			$tb[ServicosPlanos].valor valorServicoPlano,
			$tb[ServicosPlanos].dtCadastro dtCadastro,
			$tb[ServicosPlanos].dtAtivacao dtAtivacao,
			$tb[PlanosPessoas].especial especial,
			$tb[PlanosDocumentosGerados].dtVencimento
		FROM
			$tb[ServicosPlanosDocumentosGerados],
			$tb[PlanosDocumentosGerados],
			$tb[DocumentosGerados],
			$tb[Faturamentos],
			$tb[ServicosPlanos],
			$tb[Servicos],
			$tb[PlanosPessoas]
		WHERE
			$tb[Faturamentos].id=$tb[DocumentosGerados].idFaturamento
			AND $tb[DocumentosGerados].id=$tb[PlanosDocumentosGerados].idDocumentoGerado
			AND $tb[PlanosDocumentosGerados].id=$tb[ServicosPlanosDocumentosGerados].idPlanoDocumentoGerado
			AND $tb[ServicosPlanosDocumentosGerados].idServicosPlanos=$tb[ServicosPlanos].id
			AND $tb[ServicosPlanos].idServico=$tb[Servicos].id
			AND $tb[ServicosPlanos].idPlano=$tb[PlanosPessoas].id
			AND $tb[ServicosPlanosDocumentosGerados].idPlanoDocumentoGerado=$registro
		ORDER BY
			$tb[ServicosPlanosDocumentosGerados].id
	";
	
	$consulta=consultaSQL($sql, $conn);
	
	# Cabeçalho		
	# Motrar tabela de busca
	novaTabela("[Serviços do Plano]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 6);
	
	# Caso não hajam servicos para o servidor
	if(!$consulta || contaConsulta($consulta)==0) {
		# Não há registros
		itemTabelaNOURL('Não há serviços para o plano gerado!', 'left', $corFundo, 5, 'txtaviso');
	}
	else {

		# Cabeçalho
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTabela('Serviço', 'center', '30%', 'tabfundo0');
			itemLinhaTabela('Valor Serviço', 'center', '10%', 'tabfundo0');
			itemLinhaTabela('Valor Cobrado', 'center', '10%', 'tabfundo0');
			itemLinhaTabela('Adicionais', 'center', '10%', 'tabfundo0');
			itemLinhaTabela('Descontos', 'center', '10%', 'tabfundo0');
			itemLinhaTabela('Total', 'center', '10%', 'tabfundo0');
		fechaLinhaTabela();

		# Zerar Totais
		$totalServico=0;
		$totalServicosAdicionais=0;
		$totalDescontos=0;
		$totalTotal=0;
		
		for($i=0;$i<contaConsulta($consulta);$i++) {

			# Mostrar registro
			$id=resultadoSQL($consulta, $i, 'id');
			$idPlanoDocumentoGerado=resultadoSQL($consulta, $i, 'idPlanoDocumentoGerado');
			$idServicosPlanos=resultadoSQL($consulta, $i, 'idServicosPlanos');
			$nomeServico=resultadoSQL($consulta, $i, 'nome');
			$valor=resultadoSQL($consulta, $i, 'valor');
			$especial=resultadoSQL($consulta, $i, 'especial');
			$dtCadastro=resultadoSQL($consulta, $i, 'dtCadastro');
			$dtAtivacao=resultadoSQL($consulta, $i, 'dtAtivacao');
			$dtVencimento=resultadoSQL($consulta, $i, 'dtVencimento');
			
			# Verificar valor do servico
			if($especial=='S') $valorServico=resultadoSQL($consulta, $i, 'valorServicoPlano');
			else $valorServico=resultadoSQL($consulta, $i, 'valorServico');

			
			# Valor
			$total+=$valor;

			# Valor Formatado
			$valorFormatado=formatarValoresForm($valor);
			
			# Data de Vencimento
			$anoVencimento=substr($dtVencimento, 0, 4);
			$mesVencimento=substr($dtVencimento, 5, 2);
			$diaVencimento=substr($dtVencimento, 8, 2);
			$dtVencimentoServico=mktime(0,0,0, $mesVencimento, $diaVencimento, $anoVencimento);
			
			# Serviços Adicionais
			$servicosAdicionais=calculaServicosAdicionais($idServicosPlanos, $dtVencimentoServico);
			$totalServicosAdicionais+=$servicosAdicionais;
			
			# Verificar Descontos
			$descontos=calculaDescontos($idServicosPlanos, $dtVencimentoServico);
			$totalDescontos+=$descontos;
			
			# Sub Total do serviço
			$total=$valor;
			
			$totalTotal+=$total;

			# Cabeçalho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela("<b>$nomeServico</b>", 'left', '30%', 'tabfundo1');
				itemLinhaTabela("<span class=bold10>".formatarValoresForm($valorServico)."</span>", 'center', '10%', 'tabfundo1');
				itemLinhaTabela("<span class=txtcheck>".formatarValoresForm($valor)."</span>", 'center', '10%', 'tabfundo1');
				itemLinhaTabela("<span class=txtok>".formatarValoresForm($servicosAdicionais)."</span>", 'center', '10%', 'tabfundo1');
				itemLinhaTabela("<span class=txtaviso>".formatarValoresForm($descontos)."</span>", 'center', '10%', 'tabfundo1');
				itemLinhaTabela("<span class=txtok>".formatarValoresForm($total)."</span>", 'center', '10%', 'tabfundo1');
			fechaLinhaTabela();
			
			if($servicosAdicionais > 0 || $descontos > 0) {
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('100%', 'left', $corFundo, 6, 'normal10');
						# Listar os servicos Adicionais
						novaTabelaSH("center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
			
						# Verificar se existem serviços Adicionais e listar
						if($servicosAdicionais>0) {
							listarServicosAdicionaisVencimento($idServicosPlanos, $dtVencimentoServico);
						}
						
						# Verificar se existem Descontos e Listar
						if($descontos>0) {
							# listar descontos
							listarDescontosVencimento($idServicosPlanos, $dtVencimentoServico);
						}
						
						fechaTabela();
						
					htmlFechaColuna();
				fechaLinhaTabela();
			}			
			
		} #fecha laco de montagem de tabela
		
	} #fecha servicos encontrados
	
	# Total
	novaLinhaTabela($corFundo, '100%');
		itemLinhaForm('Total', 'right', 'middle', $corFundo, 4, 'tabfundo0');
		itemLinhaForm(formatarValoresForm($totalTotal), 'center', 'middle', $corFundo, 2, 'txtok');
	fechaLinhaTabela();	
	
	fechaTabela();
}




# Função para detalhar faturamento do cliente
function baixarFaturaCliente($modulo, $sub, $acao, $registro, $matriz) {

	# Mostrar detalhes do faturamento
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessPlanos, $conn, $tb;

	# Detalhes do documento
	$consulta=buscaContasReceber($registro, 'id','igual','id');
	
	$data=dataSistema();
	
	if($consulta && contaConsulta($consulta)>0) {
		
		$idDocumentosGerados=resultadoSQL($consulta, 0, 'idDocumentosGerados');
		$idContasReceber=resultadoSQL($consulta, 0, 'id');
		$valor=resultadoSQL($consulta, 0, 'valor');
		$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
		$status=resultadoSQL($consulta, 0, 'status');
		$dtVencimento=resultadoSQL($consulta, 0, 'dtVencimento');
		$obs = resultadoSQL($consulta, 0, 'obs');

		# Informações sobre faturamento
		$consultaDocumentosGerados=buscaDocumentosGerados($idDocumentosGerados, 'id','igual','id');
		
		if($consultaDocumentosGerados && contaConsulta($consultaDocumentosGerados)>0) {
		
			$idFaturamento=resultadoSQL($consultaDocumentosGerados, 0, 'idFaturamento');
			$idPessoaTipo=resultadoSQL($consultaDocumentosGerados, 0, 'idPessoaTipo');
			
			verPessoas('cadastros', 'clientes', 'ver', $idPessoaTipo, $matriz);
			echo "<br>";
			
			$matriz[idPessoaTipo]=$idPessoaTipo;
			$matriz[idFaturamento]=$idFaturamento;
			$matriz[idContasReceber]=$idContasReceber;
			$matriz[idPessoasTipo]=$idPessoasTipo;
			$matriz[idDocumentoGerado]=$idDocumentosGerados;
			$matriz[status]=$status;
			$matriz[dtVencimento]=$dtVencimento;
			$matriz[dtCadastro]=$dtCadastro;
			$matriz[valor]=$valor;
			$matriz[obs] = ( $matriz[obs] ? $matriz[obs] : $obs );
			
			if(!$matriz[valorRecebido]) $matriz[valorRecebido]=formatarValoresForm($matriz[valor]);

			# Formulario de baixa
			formBaixarFaturaCliente($modulo, $sub, $acao, $registro, $matriz);
		}
	}
}


# Função para detalhar faturamento do cliente
function estornoFaturaCliente($modulo, $sub, $acao, $registro, $matriz) {

	# Mostrar detalhes do faturamento
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessPlanos, $conn, $tb;

	# Detalhes do documento
	$consulta=buscaContasReceber($registro, 'id','igual','id');
	
	$data=dataSistema();
	
	if($consulta && contaConsulta($consulta)>0) {
		
		$idDocumentosGerados=resultadoSQL($consulta, 0, 'idDocumentosGerados');
		$idContasReceber=resultadoSQL($consulta, 0, 'id');
		$valor=resultadoSQL($consulta, 0, 'valor');
		$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
		$status=resultadoSQL($consulta, 0, 'status');
		$dtVencimento=resultadoSQL($consulta, 0, 'dtVencimento');
		$obs=resultadoSQL($consulta, 0, 'obs');
		
		$dtBaixa=resultadoSQL($consulta, 0, 'dtBaixa');
		$valorRecebido=resultadoSQL($consulta, 0, 'valorRecebido');

		# Informações sobre faturamento
		$consultaDocumentosGerados=buscaDocumentosGerados($idDocumentosGerados, 'id','igual','id');
		
		if($consultaDocumentosGerados && contaConsulta($consultaDocumentosGerados)>0) {
		
			$idFaturamento=resultadoSQL($consultaDocumentosGerados, 0, 'idFaturamento');
			$idPessoaTipo=resultadoSQL($consultaDocumentosGerados, 0, 'idPessoaTipo');
			
			if(!$matriz[bntConfirmar]) {
				verPessoas('cadastros', 'clientes', 'ver', $idPessoaTipo, $matriz);
				echo "<br>";
				
				$matriz[idPessoaTipo]=$idPessoaTipo;
				$matriz[idFaturamento]=$idFaturamento;
				$matriz[idContasReceber]=$idContasReceber;
				$matriz[idPessoasTipo]=$idPessoasTipo;
				$matriz[idDocumentoGerado]=$idDocumentosGerados;
				$matriz[status]=$status;
				$matriz[dtVencimento]=$dtVencimento;
				$matriz[dtCadastro]=$dtCadastro;
				$matriz[valor]=$valor;
				$matriz[obs]=$obs;
				$matriz[valorRecebido]= $valorRecebido;
				$matriz[dtBaixa]=converteData($dtBaixa, "banco", "formdata");
				if(!$matriz[valorRecebido]) $matriz[valorRecebido]=formatarValoresForm($matriz[valor]);
				
				# Formulario de baixa
				formEstornoFaturaCliente($modulo, $sub, $acao, $registro, $matriz);
				
			}
			else {
				if($matriz[bntConfirmar]) {
					# Juros
					$matriz[valorRecebido] = formatarValores($matriz[valorRecebido]);
					$matriz[valor] =		 formatarValores($matriz[valor]);
					if($matriz[valorRecebido] > $matriz[valor]) {
						$matriz[valorJuros]= $matriz[valorRecebido] - $matriz[valor];
					}
					# Descontos
					elseif($matriz[valorRecebido] < $matriz[valor]) {
						$matriz[valorDesconto] = $matriz[valor] - $matriz[valorRecebido];
					}
		
		
					# Estornar
					if($matriz[status]=='P') {
						$matriz[valorRecebido]=0;
						$matriz[valorJuros]=0;
						$matriz[valorDesconto]=0;
						$matriz[dtBaixa]='';
						$matriz[dtCancelamento]='';
					}
					elseif($matriz[status]=='C') {
						$matriz[valorRecebido]=0;
						$matriz[valorJuros]=0;
						$matriz[valorDesconto]=0;
						$matriz[dtBaixa]='';
						$matriz[dtCancelamento]=$data[dataBanco];
						$matriz[dtBaixa]='';
					}
					elseif($matriz[status]=='B') {
						$matriz[dtBaixa]=converteData($matriz[dtBaixa],'form','banco');
					}
					
					$grava=dbContasReceber($matriz, 'estornar');
					
					if($grava) {
						# Aviso
						dadosFaturaCliente($modulo, $sub, $acao, $matriz[id], $matriz);
						itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'normal10');
						avisoNOURL("Alteração de Lançamento", "Lançamento foi estornado com sucesso!",'100%');
						fluxoDeCaixaEstorno( $matriz['id'], $matriz['valorRecebido'], $data[dataBanco] );
						
						
					}
				}
			}
		}
	}
}





# formulário de estorno de faturas
function formEstornoFaturaCliente($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;

	$data=dataSistema();

	novaLinhaTabela($corFundo, '100%');
	htmlAbreColuna('100%', 'right', $corFundo, 2, 'tabfundo1');
		novaTabela2("[Estorno de Fatura]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		# Opcoes Adicionais
		menuOpcAdicional($modulo, $sub, $acao, $matriz[idPessoaTipo]);
		#fim das opcoes adicionais				

		novaLinhaTabela($corFundo, '100%');
		$texto="			
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=registro value=$registro>
			<input type=hidden name=matriz[id] value=$registro>&nbsp;";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Lançamento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm($matriz[idContasReceber], 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Número do Documento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm($matriz[idDocumentoGerado], 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Valor do Documento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			$matriz[valor]=formatarValoresForm($matriz[valor]);
			$texto="<input type=text name=matriz[valor] size=10 value='$matriz[valor]' onBlur=formataValor(this.value,5)>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Data de Cadastro:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm(converteData($matriz[dtCadastro],'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Data de Vencimento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			$matriz[dtVencimento]=converteData($matriz[dtVencimento],'banco','formdata');
			$texto="<input type=text name=matriz[dtVencimento] size=10 value='$matriz[dtVencimento]' onBlur=verificaData(this.value,6)>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		
		# Entrada de dados
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Valor Recebido:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			$matriz[valorRecebido]=formatarValoresForm($matriz[valorRecebido]);
			$texto="<input type=text name=matriz[valorRecebido] size=10 value='$matriz[valorRecebido]'  onBlur=formataValor(this.value,7)><span class=txtaviso> (Formato: 15000 = 150,00)</span>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Data Pagamento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			if(!$matriz[dtBaixa]) $matriz[dtBaixa]=$data[dataNormalData];
			$texto="<input type=text name=matriz[dtBaixa] value='$matriz[dtBaixa]' size=10 onBlur=verificaDataMesAnoPagamento(this.value,8)><span class=txtaviso> (Formato: 17092003 = 17/09/2003)</span>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Observações:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			$texto="<textarea name=matriz[obs] rows=3 cols=60>$matriz[obs]</textarea>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Status:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm(formSelectStatusContasReceber($matriz[status], 'status','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		
		novaLinhaTabela($corFundo, '100%');
			$texto="<input type=submit name=matriz[bntConfirmar] value=Confirmar class=submit>";
			itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		
		fechaTabela();
	htmlFechaColuna();
	fechaLinhaTabela();
}


# formulário de dados cadastrais
function formBaixarFaturaCliente($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;

	$data=dataSistema();

	novaLinhaTabela($corFundo, '100%');
	htmlAbreColuna('100%', 'right', $corFundo, 2, 'tabfundo1');
		novaTabela2("[Baixa de Fatura]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		# Opcoes Adicionais
		menuOpcAdicional($modulo, $sub, $acao, $matriz[idPessoaTipo]);
		#fim das opcoes adicionais				

		novaLinhaTabela($corFundo, '100%');
		$texto="			
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=registro value=$registro>
			<input type=hidden name=matriz[id] value=$registro>&nbsp;";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Lançamento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm($matriz[idContasReceber], 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Número do Documento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm($matriz[idDocumentoGerado], 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Valor do Documento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm(formatarValoresForm($matriz[valor]), 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Data de Cadastro:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm(converteData($matriz[dtCadastro],'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Data de Vencimento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm(converteData($matriz[dtVencimento],'banco','formdata'), 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		
		# Calcular Dias de atraso/antecipação
		$calculaDias=calculaDiasDiferenca($data[dataBanco],$matriz[dtVencimento]);
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Pagamento está:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm($calculaDias[texto], 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		
		$inativo = ( $matriz[bntBaixar] ? 'disabled="disabled" style="color: #666666"' : "" );
		
		# Entrada de dados
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Valor Recebido:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			$texto="<input type=text name=matriz[valorRecebido] size=10 value='$matriz[valorRecebido]' ".$inativo."  onBlur=formataValor(this.value,5)><span class=txtaviso> (Formato: 15000 = 150,00)</span>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Data Pagamento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			if(!$matriz[dtBaixa]) $matriz[dtBaixa]=$data[dataNormalData];
			$texto="<input type=text name=matriz[dtBaixa] value='$matriz[dtBaixa]' ".$inativo." size=10 onBlur=verificaDataMesAnoPagamento(this.value,6)><span class=txtaviso> (Formato: 17092003 = 17/09/2003)</span>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		
		# Confirmação / Calculo de valores
		if($matriz[bntCalcular]) {
			# Juros
			if(formatarValores($matriz[valorRecebido]) > formatarValores($matriz[valor])) {
				$valorJuros=formatarValoresForm(formatarValores($matriz[valorRecebido]) - formatarValores($matriz[valor]));
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Valor dos juros:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					$texto="<input type=hidden name=matriz[valorJuros] value='$valorJuros'><span class=txtok>$valorJuros</span>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			}
			# Descontos
			elseif(formatarValores($matriz[valorRecebido]) < formatarValores($matriz[valor])) {
				$valorDesconto=formatarValoresForm(formatarValores($matriz[valor]) - formatarValores($matriz[valorRecebido]));
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Valor dos descontos:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					$texto="<input type=hidden name=matriz[valorDesconto] value='$valorDesconto'><span class=txtaviso>$valorDesconto</span>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			}
			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Observações:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<textarea name=matriz[obs] rows=3 cols=60>$matriz[obs]</textarea>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntCalcular] value='Calcula Baixa' class=submit> ";
				$texto.="<input type=submit name=matriz[bntBaixar] value=Baixar class=submit>";
				itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		}
		elseif($matriz[bntBaixar]) {
			# Juros
			if(formatarValores($matriz[valorRecebido]) > formatarValores($matriz[valor])) {
				$matriz[valorJuros]=formatarValoresForm(formatarValores($matriz[valorRecebido]) - formatarValores($matriz[valor]));
			}
			# Descontos
			elseif(formatarValores($matriz[valorRecebido]) < formatarValores($matriz[valor])) {
				$matriz[valorDesconto]=formatarValoresForm(formatarValores($matriz[valor]) - formatarValores($matriz[valorRecebido]));
			}


			# Baixar 
			$matriz[valorRecebido]=formatarValores($matriz[valorRecebido]);
			$matriz[valorJuros]=formatarValores($matriz[valorJuros]);
			$matriz[valorDesconto]=formatarValores($matriz[valorDesconto]);
			$grava=dbContasReceber($matriz, 'baixar');
			
			if($grava) {
				# Aviso
				itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
				itemTabelaNOURL('<span class=txtaviso>Baixa de fatura efetuada com sucesso!</span>', 'center', $corFundo, 3, 'tabfundo1');
				itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
			}
		}
		else {
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntCalcular] value='Calcula Baixa' class=submit>";
				itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		}
		
		fechaTabela();
	htmlFechaColuna();
	fechaLinhaTabela();
}




# Função para detalhar faturamento do cliente
function cancelarFaturaCliente($modulo, $sub, $acao, $registro, $matriz) {

	# Mostrar detalhes do faturamento
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessPlanos, $conn, $tb;

	# Detalhes do documento
	$consulta=buscaContasReceber($registro, 'id','igual','id');
	
	$data=dataSistema();
	
	if($consulta && contaConsulta($consulta)>0) {
		
		$idDocumentosGerados=resultadoSQL($consulta, 0, 'idDocumentosGerados');
		$idContasReceber=resultadoSQL($consulta, 0, 'id');
		$valor=resultadoSQL($consulta, 0, 'valor');
		$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
		$status=resultadoSQL($consulta, 0, 'status');
		$dtVencimento=resultadoSQL($consulta, 0, 'dtVencimento');

		# Informações sobre faturamento
		$consultaDocumentosGerados=buscaDocumentosGerados($idDocumentosGerados, 'id','igual','id');
		
		if($consultaDocumentosGerados && contaConsulta($consultaDocumentosGerados)>0) {
		
			$idFaturamento=resultadoSQL($consultaDocumentosGerados, 0, 'idFaturamento');
			$idPessoaTipo=resultadoSQL($consultaDocumentosGerados, 0, 'idPessoaTipo');
			
			verPessoas('cadastros', 'clientes', 'ver', $idPessoaTipo, $matriz);
			echo "<br>";
			
			$matriz[idPessoaTipo]=$idPessoaTipo;
			$matriz[idFaturamento]=$idFaturamento;
			$matriz[idContasReceber]=$idContasReceber;
			$matriz[idPessoasTipo]=$idPessoasTipo;
			$matriz[idDocumentoGerado]=$idDocumentosGerados;
			$matriz[status]=$status;
			$matriz[dtVencimento]=$dtVencimento;
			$matriz[dtCadastro]=$dtCadastro;
			$matriz[valor]=$valor;
			
			if(!$matriz[valorRecebido]) $matriz[valorRecebido]=formatarValoresForm($matriz[valor]);

			# Formulario de baixa
			formCancelarFaturaCliente($modulo, $sub, $acao, $registro, $matriz);
		}
	}
}





# formulário de dados cadastrais
function formCancelarFaturaCliente($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;

	$data=dataSistema();

	novaLinhaTabela($corFundo, '100%');
	htmlAbreColuna('100%', 'right', $corFundo, 2, 'tabfundo1');
		novaTabela2("[Cancelamento de Fatura]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		# Opcoes Adicionais
		menuOpcAdicional($modulo, $sub, $acao, $matriz[idPessoaTipo]);
		#fim das opcoes adicionais				

		novaLinhaTabela($corFundo, '100%');
		$texto="			
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=registro value=$registro>
			<input type=hidden name=matriz[id] value=$registro>&nbsp;";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Lançamento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm($matriz[idContasReceber], 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Número do Documento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm($matriz[idDocumentoGerado], 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Valor do Documento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm(formatarValoresForm($matriz[valor]), 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Data de Cadastro:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm(converteData($matriz[dtCadastro],'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Data de Vencimento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm(converteData($matriz[dtVencimento],'banco','formdata'), 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		
		# Calcular Dias de atraso/antecipação
		$calculaDias=calculaDiasDiferenca($data[dataBanco],$matriz[dtVencimento]);
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Pagamento está:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm($calculaDias[texto], 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Data de Cancelamento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			if(!$matriz[dtCancelamento]) $matriz[dtCancelamento]=$data[dataNormalData];
			$texto="<input type=text name=matriz[dtCancelamento] value='$matriz[dtCancelamento]' size=10 onBlur=verificaDataMesAnoPagamento(this.value,6)><span class=txtaviso> (Formato: 17092003 = 17/09/2003)</span>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();

		# Observações
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Observações:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			$texto="<textarea name=matriz[obs] rows=3 cols=60>$matriz[obs]</textarea>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			$texto="<input type=submit name=matriz[bntCancelar] value=Cancelar class=submit>";
			itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		
		# Confirmação / Calculo de valores
		if($matriz[bntCancelar]) {
			# Baixar 
			$grava=dbContasReceber($matriz, 'cancelar');
			
			if($grava) {
				# Aviso
				itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
				itemTabelaNOURL('<span class=txtaviso>Cancelamento de fatura efetuado com sucesso!</span>', 'center', $corFundo, 3, 'tabfundo1');
				itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
			}
		}
		fechaTabela();
	htmlFechaColuna();
	fechaLinhaTabela();
}



# Exibição de dados da cobrança
function dadosFaturaCliente($modulo, $sub, $acao, $registro, $matriz) {

	# Mostrar detalhes do faturamento
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessPlanos, $conn, $tb;

	# Detalhes do documento
	$consulta=buscaContasReceber($registro, 'id','igual','id');
	
	$data=dataSistema();
	
	if($consulta && contaConsulta($consulta)>0) {
		
		$idDocumentosGerados=resultadoSQL($consulta, 0, 'idDocumentosGerados');
		$idContasReceber=resultadoSQL($consulta, 0, 'id');
		$valor=resultadoSQL($consulta, 0, 'valor');
		$valorRecebido=resultadoSQL($consulta, 0, 'valorRecebido');
		$valorJuros=resultadoSQL($consulta, 0, 'valorJuros');
		$valorDesconto=resultadoSQL($consulta, 0, 'valorDesconto');
		$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
		$dtCancelamento=resultadoSQL($consulta, 0, 'dtCancelamento');
		$dtVencimento=resultadoSQL($consulta, 0, 'dtVencimento');
		$dtBaixa=resultadoSQL($consulta, 0, 'dtBaixa');
		$obs=resultadoSQL($consulta, 0, 'obs');
		$status=resultadoSQL($consulta, 0, 'status');

		# Informações sobre faturamento
		$consultaDocumentosGerados=buscaDocumentosGerados($idDocumentosGerados, 'id','igual','id');
		
		if($consultaDocumentosGerados && contaConsulta($consultaDocumentosGerados)>0) {
		
			$idFaturamento=resultadoSQL($consultaDocumentosGerados, 0, 'idFaturamento');
			$idPessoaTipo=resultadoSQL($consultaDocumentosGerados, 0, 'idPessoaTipo');
			
			verPessoas('cadastros', 'clientes', 'ver', $idPessoaTipo, $matriz);
			echo "<br>";
			
			$matriz[idFaturamento]=$idFaturamento;
			$matriz[idContasReceber]=$idContasReceber;
			$matriz[idPessoaTipo]=$idPessoaTipo;
			$matriz[idDocumentosGerados]=$idDocumentosGerados;
			$matriz[idContasReceber]=$idContasReceber;
			$matriz[valor]=$valor;
			$matriz[valorRecebido]=$valorRecebido;
			$matriz[valorJuros]=$valorJuros;
			$matriz[valorDesconto]=$valorDesconto;
			$matriz[dtCadastro]=$dtCadastro;
			$matriz[dtVencimento]=$dtVencimento;
			$matriz[dtCancelamento]=$dtCancelamento;
			$matriz[dtBaixa]=$dtBaixa;
			$matriz[obs]=$obs;
			$matriz[status]=$status;
			
			novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('100%', 'right', $corFundo, 2, 'tabfundo1');
				novaTabela2("[Dados da Cobrança da Fatura]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, $matriz[idPessoaTipo]);
				#fim das opcoes adicionais				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Lançamento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'normal10');
					itemLinhaForm($matriz[idContasReceber], 'left', 'top', $corFundo, 0, 'normal10');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Número do Documento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'normal10');
					itemLinhaForm($matriz[idDocumentosGerados], 'left', 'top', $corFundo, 0, 'normal10');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Valor do Documento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'normal10');
					itemLinhaForm(formatarValoresForm($matriz[valor]), 'left', 'top', $corFundo, 0, 'bold10');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Data de Cadastro:</b>', 'right', 'middle', '30%', $corFundo, 0, 'normal10');
					itemLinhaForm(converteData($matriz[dtCadastro],'banco','form'), 'left', 'top', $corFundo, 0, 'normal10');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Data de Vencimento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'normal10');
					itemLinhaForm(converteData($matriz[dtVencimento],'banco','formdata'), 'left', 'top', $corFundo, 0, 'normal10');
				fechaLinhaTabela();
				
				# Entrada de dados
				if($matriz[valorJuros]) {
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>Valor de Juros:</b>', 'right', 'middle', '30%', $corFundo, 0, 'normal10');
						itemLinhaForm(formatarValoresForm($matriz[valorJuros]), 'left', 'top', $corFundo, 0, 'txttrial');
					fechaLinhaTabela();
				}
				if($matriz[valorDesconto]) {
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>Valor de Descontos:</b>', 'right', 'middle', '30%', $corFundo, 0, 'normal10');
						itemLinhaForm(formatarValoresForm($matriz[valorDescontos]), 'left', 'top', $corFundo, 0, 'txtaviso');
					fechaLinhaTabela();
				}
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Valor Recebido:</b>', 'right', 'middle', '30%', $corFundo, 0, 'normal10');
					itemLinhaForm(formatarValoresForm($matriz[valorRecebido]), 'left', 'top', $corFundo, 0, 'txtok');
				fechaLinhaTabela();
				
				if(formatarData($matriz[dtBaixa])>0) {
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>Data Pagamento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'normal10');
						itemLinhaForm(converteData($matriz[dtBaixa],'banco','formdata'), 'left', 'top', $corFundo, 0, 'normal10');
					fechaLinhaTabela();
				}
				if(formatarData($matriz[dtCancelamento])>0) {
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>Data Cancelamento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'normal10');
						itemLinhaForm(converteData($matriz[dtCancelamento],'banco','formdata'), 'left', 'top', $corFundo, 0, 'normal10');
					fechaLinhaTabela();
				}
				if($matriz[obs]) {
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>Observações:</b>', 'right', 'middle', '30%', $corFundo, 0, 'normal10');
						itemLinhaForm($matriz[obs], 'left', 'top', $corFundo, 0, 'normal10');
					fechaLinhaTabela();
				}
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Status:</b>', 'right', 'middle', '30%', $corFundo, 0, 'normal10');
					itemLinhaForm(formSelectStatusContasReceber($matriz[status],'','check'), 'left', 'top', $corFundo, 0, 'normal10');
				fechaLinhaTabela();
				
				fechaTabela();
			htmlFechaColuna();
			fechaLinhaTabela();
			
			
		}
	}

}



?>
