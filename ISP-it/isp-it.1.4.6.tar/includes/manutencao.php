<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 04/08/2003
# Ultima alteração: 24/11/2003
#    Alteração No.: 003
#
# Função:
#    Painel - Funções para manutenção


function manutencaoAplicarDescontos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessPlanos;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[adicionar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		# Formulário de aplicação de descontos
		
		if(!$matriz[bntCalcular]) {
			formAplicarDescontos($modulo, $sub, $acao, $registro, $matriz);
		}
		else {
			# Calcular descontos para clientes
			formAplicarDescontos($modulo, $sub, $acao, $registro, $matriz);
			echo "<br>";
			formConfirmaAplicarDescontos($modulo, $sub, $acao, $registro, $matriz);
		}
	}

}



function formAplicarDescontos($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;

	$data=dataSistema();

	# Fomrulário de seleção de parâmetros para aplicação de descontos
	novaTabela2("[Aplicação de Descontos]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 2);
		# Opcoes Adicionais
		menuOpcAdicional($modulo, $sub, $acao, $matriz[idServicoPlano]);
		novaLinhaTabela($corFundo, '100%');
			$texto="<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=registro value=$registro>";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		# Buscar informações sobre o serviço 
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Descrição do Desconto:</b>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
			$texto=" <input type=text size=60 name=matriz[descricao] value='$matriz[descricao]'>";
			itemLinhaTMNOURL($texto, 'left', 'middle', '60%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Serviço:</b>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
			$texto=" <input type=submit name=matriz[bntServico] value=Selecionar class=submit>";
			itemLinhaTMNOURL(formSelectServicos($matriz[servico],'servico','form').$texto, 'left', 'middle', '60%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		if($matriz[servico]) {
			$consultaServico=buscaServicos($matriz[servico], 'id','igual','id');
			if($consultaServico && contaConsulta($consultaServico)>0) {
				# informaçoes do serviço
				$nome=resultadoSQL($consultaServico, 0, 'nome');
				$descricao=resultadoSQL($consultaServico, 0, 'descricao');
				$idTipoCobranca=resultadoSQL($consultaServico, 0, 'idTipoCobranca');
				$valor=resultadoSQL($consultaServico, 0, 'valor');		
			}
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Serviço selecionado:</b>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL(formSelectServicos($matriz[servico],'servico','check')." - R\$ ".formatarValoresForm($valor), 'left', 'middle', '60%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();			
		}
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Aplicar Desconto a Planos Especiais:</b>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
			if($matriz[planos_especiais]) $opcPlanosEspeciais='checked';
			$texto="<input type=checkbox name=matriz[planos_especiais] $opcPlanosEspeciais>";
			itemLinhaTMNOURL($texto, 'left', 'middle', '60%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Ignorar data de ativação:</b>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
			if($matriz[ignorar_ativacao]) $opcAtivacao='checked';
			$texto="<input type=checkbox name=matriz[ignorar_ativacao] $opcAtivacao>";
			itemLinhaTMNOURL($texto, 'left', 'middle', '60%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Planos e Serviços ativos a partir de:</b>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
			$texto="<input type=text name=matriz[dtAtivacao] size=10 value='$matriz[dtAtivacao]' onBlur=javascript:verificaData(this.value,9)> <span class=txtaviso>(Formato: $data[dia]/$data[mes]/$data[ano])</span>";
			itemLinhaTMNOURL($texto, 'left', 'middle', '60%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		
		# Valor de Desconto
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('100%', 'center', $corFundo, 2, 'tabfundo1');
				novaTabela2('Tipo de Desconto à Aplicar',"center", '600', 0, 3, 1, $corFundo, $corBorda, 2);
					novaLinhaTabela($corFundo, '100%');
						itemLinhaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						itemLinhaNOURL('<b>Selecione o tipo de desconto:</b>', 'right', $corFundo, 0, 'tabfundo1');
						itemLinhaNOURL(formSelectTipoDesconto($matriz[tipo_desconto],'tipo_desconto','form'), 'left', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					if($matriz[tipo_desconto]) {
					
						# Verificar se valor do desconto não excede o valor do serviço
						if($matriz[valor] && formatarValores($valor) < formatarValores($matriz[valor])) {
							# altertar
							$matriz[valor]='0';
							novaLinhaTabela($corFundo, '100%');
								itemLinhaNOURL("<span class=txtaviso>ATENÇÃO: </span>Desconto acima do valor do Serviço ($nome - R\$ $valor)", 'center', $corFundo, 2, 'tabfundo1');
							fechaLinhaTabela();		
						}
						
						if($matriz[tipo_desconto]=='perc') {
							# Percentual de desconto
							novaLinhaTabela($corFundo, '100%');
								itemLinhaNOURL('<b>Selecione percentual de desconto:</b>', 'right', $corFundo, 0, 'tabfundo1');
								itemLinhaNOURL(formSelectDescontoAplicado($matriz[percentual], 'percentual', 'form', $valor, 11), 'left', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
							# Valor específico para desconto
							novaLinhaTabela($corFundo, '100%');
								itemLinhaNOURL('<b>Valor para Desconto:</b>', 'right', $corFundo, 0, 'tabfundo1');
								$texto="<input type=text name=matriz[valor] size=10 value='$matriz[valor]' onBlur=formataValor(this.value,12)>";
								itemLinhaNOURL($texto, 'left', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
							novaLinhaTabela($corFundo, '100%');
								itemLinhaTMNOURL('<b>Mês/Ano de Vencimento:</b>', 'right', 'middle', '50%', $corFundo, 0, 'tabfundo1');
								$texto="<input type=text name=matriz[dtVencimento] value='$matriz[dtVencimento]' size=7 onBlur=verificaDataMesAno(this.value,13);><span class=txtaviso> (Formato: ".$data[mes]."/".$data[ano].")</span>";
								itemLinhaTMNOURL($texto, 'left', 'middle', '50%', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
						}
						elseif($matriz[tipo_desconto]=='valor') {
							# Valor específico para desconto
							novaLinhaTabela($corFundo, '100%');
								itemLinhaNOURL('<b>Valor para Desconto:</b>', 'right', $corFundo, 0, 'tabfundo1');
								$texto="<input type=text name=matriz[valor] size=10 value='$matriz[valor]'  onBlur=formataValor(this.value,11)>";
								itemLinhaNOURL($texto, 'left', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
							novaLinhaTabela($corFundo, '100%');
								itemLinhaTMNOURL('<b>Mês/Ano de Vencimento:</b>', 'right', 'middle', '50%', $corFundo, 0, 'tabfundo1');
								$texto="<input type=text name=matriz[dtVencimento] value='$matriz[dtVencimento]' size=7 onBlur=verificaDataMesAno(this.value,12);><span class=txtaviso> (Formato: ".$data[mes]."/".$data[ano].")</span>";
								itemLinhaTMNOURL($texto, 'left', 'middle', '50%', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
						}
						elseif($matriz[tipo_desconto]=='dias') {
							# Valor específico para desconto
							novaLinhaTabela($corFundo, '100%');
								itemLinhaNOURL('<b>Dias de utilização:</b>', 'right', $corFundo, 0, 'tabfundo1');
								$texto="<input type=text name=matriz[dias] size=2 value='$matriz[dias]'>";
								itemLinhaNOURL($texto, 'left', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
							novaLinhaTabela($corFundo, '100%');
								itemLinhaTMNOURL('<b>Mês/Ano de Vencimento:</b>', 'right', 'middle', '50%', $corFundo, 0, 'tabfundo1');
								$texto="<input type=text name=matriz[dtVencimento] value='$matriz[dtVencimento]' size=7 onBlur=verificaDataMesAno(this.value,12);><span class=txtaviso> (Formato: ".$data[mes]."/".$data[ano].")</span>";
								itemLinhaTMNOURL($texto, 'left', 'middle', '50%', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
						}
					}
					
					novaLinhaTabela($corFundo, '100%');
						$texto="<input type=submit name=matriz[bntTipoDesconto] value=Selecionar class=submit>";
						itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
					fechaLinhaTabela();
				fechaTabela();
			htmlFechaColuna();
		fechaLinhaTabela();

		if($matriz[valor] || $matriz[tipo_desconto]=='dias') {
			# Seleção de Estados e Cidades
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('100%', 'center', $corFundo, 2, 'tabfundo1');
					novaTabela2('Filtrar POP',"center", '600', 0, 3, 1, $corFundo, $corBorda, 2);
						novaLinhaTabela($corFundo, '100%');
							itemLinhaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
						fechaLinhaTabela();
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL('<b>Todos os POPs:</b>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
							if($matriz[ignorar_pop]) $opcPOP='checked';
							$texto="<input type=checkbox name=matriz[ignorar_pop] $opcPOP>";
							itemLinhaTMNOURL($texto, 'left', 'middle', '60%', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL('<b>POP:</b>', 'right', 'middle', '50%', $corFundo, 0, 'tabfundo1');
							itemLinhaTMNOURL(formSelectPOP($matriz[pop],'pop','form'), 'left', 'middle', '50%', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
						novaLinhaTabela($corFundo, '100%');
							$texto="<input type=submit name=matriz[bntTipoDesconto] value=Selecionar class=submit>";
							itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
						fechaLinhaTabela();
					fechaTabela();
				htmlFechaColuna();
			fechaLinhaTabela();
			
		}

		# Detalhar consulta
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Detalhar consulta:</b>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
			if($matriz[detalhar]=='S') $opcDetalhar='checked';
			$texto="<input type=checkbox name=matriz[detalhar] value='S' $opcDetalhar> <span class=txtaviso>(Mostrar descontos aplicados)</span>";
			itemLinhaTMNOURL($texto, 'left', 'middle', '60%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Efetivar Descontos:</b>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
			if($matriz[aplicar_descontos]=='S') $opcAplicar='checked';
			$texto="<input type=checkbox name=matriz[aplicar_descontos] value='S' $opcAplicar> <span class=txtaviso>(Selecione esta opção para aplicar os descontos)</span>";
			itemLinhaTMNOURL($texto, 'left', 'middle', '60%', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		
		if($matriz[tipo_desconto]=='dias' || ($matriz[valor] && ($matriz[tipo_desconto]=='perc'  || $matriz[tipo_desconto]=='valor'))) {
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntCalcular] value='Calcular Descontos' class=submit>";
				itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		}
		
	fechaTabela();
}




# Função para calculo de descontos
function formConfirmaAplicarDescontos($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $tb, $conn;
	
	# Fomrulário de seleção de parâmetros para aplicação de descontos
	novaTabela2("[Calculo de Descontos e Confirmação de Aplicação de Descontos]<a href=# name=ancora></a>", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 2);
		# Opcoes Adicionais
		novaLinhaTabela($corFundo, '100%');
			$texto="<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=registro value=$registro>";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		# Buscar informações sobre o serviço 
		if($matriz[servico]) {
			$consultaServico=buscaServicos($matriz[servico], 'id','igual','id');
			if($consultaServico && contaConsulta($consultaServico)>0) {
				# informaçoes do serviço
				$nome=resultadoSQL($consultaServico, 0, 'nome');
				$descricao=resultadoSQL($consultaServico, 0, 'descricao');
				$idTipoCobranca=resultadoSQL($consultaServico, 0, 'idTipoCobranca');
				$valor=resultadoSQL($consultaServico, 0, 'valor');	
			}
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Serviço:</b>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL(formSelectServicos($matriz[servico],'servico','check')." - R\$ ".formatarValoresForm($valor), 'left', 'middle', '60%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			if($matriz[planos_especiais]) {
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Aplicar desconto a Planos Especiais:</b>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
					itemLinhaTMNOURL("<span class=txtok>Sim</span>", 'left', 'middle', '60%', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			}
			if($matriz[ignorar_ativacao]) {
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Ignorar data de Ativação:</b>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
					itemLinhaTMNOURL("<span class=txtok>Sim</span>", 'left', 'middle', '60%', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			}
			else {
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Ignorar data de Ativação:</b>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
					itemLinhaTMNOURL("<span class=txtaviso>Não</span>", 'left', 'middle', '60%', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Data mínima de ativação:</b>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
					itemLinhaTMNOURL($matriz[dtAtivacao], 'left', 'middle', '60%', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			}
			if($matriz[tipo_desconto]=='perc' || $matriz[tipo_desconto]=='valor') {
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Valor do Desconto:</b>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
					itemLinhaTMNOURL("<span class=txtaviso>$matriz[valor]</span>", 'left', 'middle', '60%', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			}
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Vencimento do Desconto:</b>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
				itemLinhaTMNOURL("$matriz[dtVencimento] (Mês para desconto)", 'left', 'middle', '60%', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			if(!$matriz[ignorar_ativacao]) {
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Data de Ativação:</b>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
					itemLinhaTMNOURL("$matriz[dtAtivacao] (Filtrar ativações a partir desta data)", 'left', 'middle', '60%', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			}
			if($matriz[ignorar_pop]) {
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>POP:</b>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
					itemLinhaTMNOURL('(Todos os POPs)', 'left', 'middle', '60%', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			}
			else {
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>POP:</b>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
					itemLinhaTMNOURL(formSelectPOP($matriz[pop],'pop','check'), 'left', 'middle', '60%', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			}
			
			# Calcular descontos
			# Monstar SQL para consulta de servicos e calculo de descontos
			
			# filtrar todos os servicos respectivos aos filtros informados
			
			# Caso POP seja selecionado
			if(!$matriz[ignorar_pop]) $sqlADD.=" AND $tb[POP].id = $matriz[pop] ";
			if(!$matriz[ignorar_ativacao]) {
				# incluir data de ativação minima na consulta
				$dtAtivacaoServico=converteData($matriz[dtAtivacao],'form','banco');
				$sqlADD.= "AND $tb[ServicosPlanos].dtAtivacao >= '$dtAtivacaoServico'";
			}
			
			# Verificar filtros nao obrigatorios ou com dados entrados manualmente
			$sql="
				SELECT
					$tb[Pessoas].nome nomePessoa,
					$tb[Pessoas].tipoPessoa tipoPessoa,
					$tb[Pessoas].razao razaoSocial,
					$tb[PessoasTipos].id idPessoaTipo, 
					$tb[PlanosPessoas].id idPlano, 
					$tb[PlanosPessoas].idVencimento idVencimento, 
					$tb[ServicosPlanos].id idServicoPlano, 
					$tb[Servicos].id idServico, 
					$tb[Servicos].valor valorServico, 
					$tb[ServicosPlanos].valor valorEspecial, 
					$tb[ServicosPlanos].dtAtivacao dtAtivacao, 
					$tb[ServicosPlanos].diasTrial trial,
					$tb[PlanosPessoas].especial especial,
					$tb[POP].nome nomePOP,
					$tb[TipoCobranca].proporcional proporcional,
					$tb[TipoCobranca].tipo tipoCobranca,
					$tb[TipoCobranca].forma formaCobranca
				FROM 
					$tb[POP], 
					$tb[Pessoas], 
					$tb[PlanosPessoas], 
					$tb[PessoasTipos], 
					$tb[Servicos], 
					$tb[ServicosPlanos], 
					$tb[StatusServicos], 
					$tb[TipoCobranca] 
				WHERE 
					$tb[Pessoas].id=$tb[PessoasTipos].idPessoa 
					AND $tb[PessoasTipos].id=$tb[PlanosPessoas].idPessoaTipo 
					AND $tb[PlanosPessoas].id=$tb[ServicosPlanos].idPlano 
					AND $tb[POP].id = $tb[Pessoas].idPOP 
					AND $tb[ServicosPlanos].idServico = $tb[Servicos].id 
					AND $tb[Servicos].idTipoCobranca=$tb[TipoCobranca].id 
					AND $tb[ServicosPlanos].idStatus = $tb[StatusServicos].id 
					AND $tb[TipoCobranca].forma='mensal' 
					AND $tb[StatusServicos].cobranca = 'S' 
					AND $tb[Servicos].id = $matriz[servico]
					$sqlADD
				ORDER BY 
					$tb[POP].nome, 
					$tb[Pessoas].nome
			";
			
			
			# Consultar Serviços e identificar quais os ServiçosPlanos que devem
			# receber desconto. Identificar valor do desconto.
			$consulta=consultaSQL($sql, $conn);
			
			if($consulta && contaConsulta($consulta)>0) {
			
				$totalDesconto=0;
				$descontosAplicados=0;
				for($a=0;$a<contaConsulta($consulta);$a++) {
				
					$nomePessoa=resultadoSQL($consulta, $a, 'nomePessoa');
					$razaoSocial=resultadoSQL($consulta, $a, 'razaoSocial');
					$tipoPessoa=resultadoSQL($consulta, $a, 'tipoPessoa');
					$idPessoaTipo=resultadoSQL($consulta, $a, 'idPessoaTipo');
					$idServicoPlano=resultadoSQL($consulta, $a, 'idServicoPlano');
					$idServico=resultadoSQL($consulta, $a, 'idServico');
					$idPlano=resultadoSQL($consulta, $a, 'idPlano');
					$idVencimento=resultadoSQL($consulta, $a, 'idVencimento');
					$valorServico=resultadoSQL($consulta, $a, 'valorServico');
					$valorEspecial=resultadoSQL($consulta, $a, 'valorEspecial');
					$dtAtivacao=resultadoSQL($consulta, $a, 'dtAtivacao');
					$trial=resultadoSQL($consulta, $a, 'trial');
					$nomePOP=resultadoSQL($consulta, $a, 'nomePOP');
					$especial=resultadoSQL($consulta, $a, 'especial');
					$proporcional=resultadoSQL($consulta, $a, 'proporcional');
					$tipoCobranca=resultadoSQL($consulta, $a, 'tipoCobranca');
					$formaCobranca=resultadoSQL($consulta, $a, 'formaCobranca');
					
					# Identificar tipo de desconto e calcular valor a ser aplicado
					# Verificar se plano é especial e utilizar valor especial para
					# base de desconto
					if($especial=='S') $valor=$valorEspecial;
					else $valor=$valorServico;
					
					# Identificar data de desconto (mes/ano)
					$matriz[dtVencimento]=formatarData($matriz[dtVencimento]);
					$mes=substr($matriz[dtVencimento],0,2);
					$ano=substr($matriz[dtVencimento],2,4);
					
					
					# Verificar tipo de desconto a ser aplicado
					if($matriz[tipo_desconto]=='dias') {
					
						# Calcular data de vencimento
						$vencimento=dadosVencimento($idVencimento);
						$vencimento[mes]=$mes;
						$vencimento[ano]=$ano;
						$dtVencimento=date('Y-m-d',calculaVencimentoCobranca($dtAtivacao, $trial, $vencimento, $mes, $ano));
						
						# Numero de dias do mes informado
						$qtdeDiasMes=dataDiasMes($mes);

						# Verificar tipo de serviço para aplicação de desconto
						if($formaCobranca=='mensal') {
							if($tipoCobranca=='pre') {
								# Calcular valor proporcional para desconto
								$valor=calculaValorNaoProporcional($dtAtivacao, $diasTrial, $vencimento, $valor);
								$valor=$valor[valor];
								$valorDesconto=round((($valor[valor]/$qtdeDiasMes)*$matriz[dias]),2);
							}
							elseif($tipoCobranca=='pos') {
								if($proporcional=='S') {
									# Calcular valor proporcional para desconto
									$valor=calculaValorProporcional($dtAtivacao, $diasTrial, $vencimento, $valor, $tipoCobranca);
									$valorDesconto=round((($valor[valor]/$qtdeDiasMes)*$matriz[dias]),2);
								}
								else {
									# Calcular valor proporcional para desconto
									$valor=calculaValorProporcional($dtAtivacao, $diasTrial, $vencimento, $valor, $tipoCobranca);
									$valor=$valor[valor];
									$valorDesconto=round((($valor[valor]/$qtdeDiasMes)*$matriz[dias]),2);
								}
							}
						
						}
					}
					
					if($valorDesconto>0) {
						
						# Alimentar matriz com valores
						$resultado[nomePessoa][$descontosAplicados]=$nomePessoa;
						$resultado[razaoSocial][$descontosAplicados]=$razaoSocial;
						$resultado[tipoPessoa][$descontosAplicados]=$tipoPessoa;
						$resultado[nomePOP][$descontosAplicados]=$nomePOP;
						$resultado[valor][$descontosAplicados]=$valorDesconto;
						$resultado[valorServico][$descontosAplicados]=$valor[valor];
						$resultado[vencimento][$descontosAplicados]=$dtVencimento;

						# Gravar Desconto
						$matriz[idPlano]=$idPlano;
						$matriz[idServicoPlano]=$idServicoPlano;
						$matriz[dtDesconto]=$dtVencimento;
						$matriz[dtCancelamento]='';
						$matriz[dtCobranca]='';
						$matriz[valor]=$valorDesconto;
						$matriz[descricao]=$matriz[descricao];
						$matriz[status]='A';
						
						# Verificar se descontos deve ser aplicado
						if($matriz[aplicar_descontos]=='S') dbDescontoServicoPlano($matriz, 'incluir');
						
						# Somar ao total
						$totalDesconto+=$valorDesconto;
						
						# Incrementar contador de descontos
						$descontosAplicados++;
					}
					
				}
				
				# Mostrar tados sobre Descontos
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Descontos aplicados:</b>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
					itemLinhaTMNOURL($descontosAplicados, 'left', 'middle', '60%', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Valor total dos Descontos:</b>', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
					itemLinhaTMNOURL("R$ ".formatarValoresForm($totalDesconto), 'left', 'middle', '60%', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				
				if($matriz[detalhar]) {
					# Mostrar resultados obtidos
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('100%', 'center', $corFundo, 2, 'tabfundo1');
							novaTabela('Detalhamento de Descontos',"center", '100%', 0, 3, 1, $corFundo, $corBorda, 5);
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Nome</b>', 'center', 'middle', '50%', $corFundo, 0, 'tabfundo1');
									itemLinhaTMNOURL('<b>Valor Serviço</b>', 'center', 'middle', '15%', $corFundo, 0, 'tabfundo1');
									itemLinhaTMNOURL('<b>Desconto</b>', 'center', 'middle', '10%', $corFundo, 0, 'tabfundo1');
									itemLinhaTMNOURL('<b>Vencimento</b>', 'center', 'middle', '10%', $corFundo, 0, 'tabfundo1');
									itemLinhaTMNOURL('<b>POP</b>', 'center', 'middle', '15%', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								
								for($b=0;$b<$descontosAplicados;$b++) {
								
									if($resultado[tipoPessoa][$b]=='F') $nomePessoa=$resultado[nomePessoa][$b];
									else $nomePessoa=$resultado[razaoSocial][$b];
								
									novaLinhaTabela($corFundo, '100%');
										itemLinhaTMNOURL($nomePessoa, 'left', 'middle', '50%', $corFundo, 0, 'normal10');
										itemLinhaTMNOURL("R$ ".formatarValoresForm($resultado[valorServico][$b]), 'center', 'middle', '15%', $corFundo, 0, 'normal10');
										itemLinhaTMNOURL("R$ ".formatarValoresForm($resultado[valor][$b]), 'center', 'middle', '10%', $corFundo, 0, 'normal10');
										itemLinhaTMNOURL(converteData($resultado[vencimento][$b],'banco','formdata'), 'center', 'middle', '10%', $corFundo, 0, 'normal10');
										itemLinhaTMNOURL($resultado[nomePOP][$b], 'center', 'middle', '25%', $corFundo, 0, 'normal8');
									fechaLinhaTabela();
								}
								
							fechaTabela();
						htmlFechaColuna();
					fechaLinhaTabela();
				}
				
				if($matriz[aplicar_descontos]) {
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('100%', 'center', $corFundo, 2, 'tabfundo1');
							$msg="<center>Descontos foram aplicados com sucesso!<br>
							Para obter melhor detalhamento dos descontos gerados, consulte faturamento por cliente
							e identifique os valores a serem descontados.</center>";
							avisoNOURL("Aplicação de Descontos", $msg, 400);
						htmlFechaColuna();
					fechaLinhaTabela();
				}
				
			}
		}
		

	fechaTabela();
}

?>
