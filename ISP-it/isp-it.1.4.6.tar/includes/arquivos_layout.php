<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 03/02/2004
# Ultima alteração: 03/02/2004
#    Alteração No.: 001
#
# Função:
#    Funções para verificação de layouts de arquivos


function layoutOcorrenciasItau($idOcorrencia) {
	$ocorrencia['02']="Entrada Confirmada";
	$ocorrencia['03']="Entrada Rejeitada";
	$ocorrencia['04']="Alteração de Dados - Nova Entrada";
	$ocorrencia['05']="Alteração de Dados - Baixa";
	$ocorrencia['06']="Liquidação Normal";
	$ocorrencia['08']="Liquidação em Cartório";
	$ocorrencia['09']="Baixa Simples";
	$ocorrencia['10']="Baixa por Ter Sido Liquidado";
	$ocorrencia['11']="Em Ser";
	$ocorrencia['12']="Abatimento Concedido";
	$ocorrencia['13']="Abatimento Cancelado";
	$ocorrencia['14']="Vencimento Alterado";
	$ocorrencia['15']="Baixas Rejeitadas";
	$ocorrencia['16']="Instruções Rejeitadas";
	$ocorrencia['17']="Alterações de Dados Rejeitados";
	$ocorrencia['18']="Cobrança Contratual - Abatimento e Baixa Bloqueados";
	$ocorrencia['19']="Confirma Recebimento de Instrução de Protesto";
	$ocorrencia['20']="Confirma Recebimento de Instrução de Sustação de Protesto/Tarifa";
	$ocorrencia['21']="Confirma Recebimento de Instrução de Não Protestar";
	$ocorrencia['23']="Protesto Enviado a Catório/Tarifa";
	$ocorrencia['24']="Instruções de Protesto Rejeitada/Sustada Pendente";
	$ocorrencia['25']="Alegações do Sacado";
	$ocorrencia['26']="Tarifa de Aviso de Cobrança";
	$ocorrencia['27']="Tarifa de Extrato Posição";
	$ocorrencia['28']="Tarifa de Relação das Liquidações";
	$ocorrencia['29']="Tarifa de Manutenção de Títulos Vencidos";
	$ocorrencia['30']="Débito Mensal de Tarifas";
	$ocorrencia['32']="Baixa por Ter Sido Protestado";
	$ocorrencia['33']="Cestas de Protesto";
	$ocorrencia['34']="Custas de Sustação";
	$ocorrencia['35']="Custas de Cartõrio Distribuidor";
	$ocorrencia['36']="Custas de Edital";
	$ocorrencia['37']="Tarifa de Emissão de Bloqueto/Tarifa de Envio de Duplicata";
	$ocorrencia['38']="Tarifa de Instrução";
	$ocorrencia['39']="Tarifa de Ocorrências";
	$ocorrencia['40']="Tarifa Mensal de Emissão de Bloqeto/Tarifa Mensal de Envio de Duplicata";
	$ocorrencia['41']="Débito Mensal de Tarifas - Extrato de Posição";
	$ocorrencia['42']="Bédito Mensal de Tarifas - Outras Instruções";
	$ocorrencia['43']="Débito Mensal de Tarifas - Manutenção de Títulos Vencidos";
	$ocorrencia['44']="Débito Mensal de Tarifas - Outras Ocorrências";
	$ocorrencia['45']="Débito Mensal de Tarifas - Protesto";
	$ocorrencia['46']="Débito mensal de Tarifas - Sustação de Protesto";
	$ocorrencia['47']="Baixa Transferência para Desconto";
	$ocorrencia['48']="Custas de Sustação Judicial";
	$ocorrencia['51']="Tarifa Mensal Ref Entradas Bancos Correspondentes na Carteira";
	$ocorrencia['52']="Tarifa Mensal Baixas na Carteira";
	$ocorrencia['53']="Tarifa Mensal BAixas em Bancos Correspondentes na Carteira";
	$ocorrencia['54']="Tarifa Mensal de Liquidações na Carteira";
	$ocorrencia['55']="Tarifa Mensal de Liquidações em Bancos Correspondentes na Carteira";
	$ocorrencia['56']="Custas de Irregularidades";
	$ocorrencia['57']="Instrução Cancelada";
	$ocorrencia['60']="Entrada Rejeitada Carnê";
	$ocorrencia['61']="Tarifa Emissão Aviso de Movimentação de Títulos";
	$ocorrencia['62']="Débito Mensal de Tarifa - Aviso de Movimentação Títulos";
	
	return($ocorrencia["$idOcorrencia"]);

}


function getMotivoOcorrencia($idOcorrencia, $motivos='N'){
	$ret = array();
	if($idOcorrencia == '10'){
		if(substr_count($motivos, '20')) $ret[] = ' - Titulo Baixado e transferido para desconto';
		if(substr_count($motivos, '16')) $ret[] = ' - Titulo Baixado pelo banco por decurso do Prazo';
		if(substr_count($motivos, '15')) $ret[] = ' - Titulo Excluído';
		if(substr_count($motivos, '14')) $ret[] = ' - Titulo Protestado';
	}
	
	return(implode('  ',$ret) );
		
}
	
function layoutOcorrenciasBradesco($idOcorrencia, $motivos='N') {
	$ocorrencia['02']="Entrada Confirmada";
	$ocorrencia['03']="Entrada Rejeitada";
	$ocorrencia['06']="Liquidação Normal";
	$ocorrencia['09']="Baixa Automatica via Arquivo";
	$ocorrencia['10']="Baixa conforme instruções da Agência";
	$ocorrencia['11']="Em Ser";
	$ocorrencia['12']="Abatimento Concedido";
	$ocorrencia['13']="Abatimento Cancelado";
	$ocorrencia['14']="Vencimento Alterado";
	$ocorrencia['15']="Liquidação em cartório";
	$ocorrencia['16']="Titulo Pago em Cheque - Vinculado";
	$ocorrencia['17']="Liquidação após baixa ou Título não registrado";
	$ocorrencia['18']="Acerto de Depositária";
	$ocorrencia['19']="Confirma Recebimento de Instrução de Protesto";
	$ocorrencia['20']="Confirma Recebimento de Instrução de Sustação de Protesto";
	$ocorrencia['21']="Acerto do Controle do Participante";
	$ocorrencia['22']="Título com Pagamento Cancelado";
	$ocorrencia['23']="Entrada do Título em Cartório";
	$ocorrencia['24']="Entrada Rejeitada por CEP irregular";
	$ocorrencia['27']="Baixa Rejeitda";
	$ocorrencia['28']="Débito de Tarifas/Custos";
	$ocorrencia['30']="Alteração de Outros Dados Rejeitados";
	$ocorrencia['32']="Instrução Rejeitada";
	$ocorrencia['33']="Confirmada Pedido Alteração Outros Dados";
	$ocorrencia['34']="Retirado de Cartório e Manutenção de Carteira";
	$ocorrencia['35']="Desagendamento do débito automático";
	$ocorrencia['68']="Acerto dos dados do rateio de Crédito";
	$ocorrencia['69']="Cancelamento dos dados do rateio"; 
	return($ocorrencia["$idOcorrencia"] . getMotivoOcorrencia($idOcorrencia, $motivos));

}

function getLabelOcorrenciaBradesco($idOcorrencia, $motivos='N'){
	switch ($idOcorrencia) {
		case '02':
		case '06':
		case '09':
			$class = "txtok8";
			break;
		
		case '10':
		case '15':
		case '16':
		case '17':
			$class = "txttrial8";
			break;
	
		default:
			$class = "txtaviso8";
			break;
	}
	
	return ("<div class=".$class.">".layoutOcorrenciasBradesco($idOcorrencia, $motivos)."</div>");
	
}


function layoutOcorrenciasBanespa($idOcorrencia) {
	$ocorrencia['02']="Entrada Confirmada";
	$ocorrencia['03']="Entrada Rejeitada";
	$ocorrencia['04']="Transferência de carteira-entrada";
	$ocorrencia['05']="Transferencia de carteira-baixa";
	$ocorrencia['06']="Liquidação Normal";
	$ocorrencia['07']="Liquidação por Conta";
	$ocorrencia['08']="Liquidação por saldo";
	$ocorrencia['10']="Baixado conforme comando via arquivo - remessa";
	$ocorrencia['11']="Baixado conforme comando agência";
	$ocorrencia['12']="Abatimento Concedido";
	$ocorrencia['13']="Abatimento Cancelado";
	$ocorrencia['14']="Vencimento Alterado";
	$ocorrencia['15']="Liquidação em cartório";
	$ocorrencia['23']="Remessa a Cartório";
	$ocorrencia['23']="Remessa a Cartório";
	$ocorrencia['24']="Retirada a Cartório";
	$ocorrencia['25']="baixado por ter sido protestado";
	$ocorrencia['28']="Débito de Tarifas/Custos";
	$ocorrencia['30']="Desconto concedido";
	return($ocorrencia["$idOcorrencia"]);

}

function layoutOcorrenciasBansicredi($idOcorrencia) {
	$oc['02']='Entrada Confirmada';
	$oc['03']='Entrada Rejeitada';
	$oc['06']='Liquidação Normal';
	$oc['09']='Baixado Automaticamente via Arquivo';
	$oc['10']='Baixado Conforme Instruções da Agência';
	$oc['12']='Abatimento concedido';
	$oc['13']='Abatimento Cancelado';
	$oc['14']='Vencimento Alterado';
	$oc['15']='Liquidação em Cartório';
	$oc['17']='Liquidação Após Baixa';
	$oc['19']='Confirmação de Recebimento de Instrução de Protesto';
	$oc['20']='Confirmação de Recebimento de Instrução de Sustação de Protesto';
	$oc['23']='Entrada de Título em Cartório';
	$oc['24']='Entrada Rejeitada por CEP irregular';
	$oc['27']='Baixa Rejeitada';
	$oc['28']='Tarifa';
	$oc['30']='Alteração Rejeitada';
	$oc['32']='Instrução Rejeitada';
	$oc['33']='Confirmação de Pedido de Alteração de Outros Dados';
	$oc['34']='Retirado de Cartório e Manutenção em Carteira';
	return $oc[$idOcorrencia];
}

function layoutOcorrenciasHSBC($idOcorrencia) {
	$ocorrencia['02']="Entrada confirmada";
	$ocorrencia['03']="Entrada rejeitada ou Instrução rejeitada";
	$ocorrencia['06']="Liquidação normal em dinheiro";
	$ocorrencia['07']="Liquidação por conta em dinheiro";
	$ocorrencia['09']="Baixa automática";
	$ocorrencia['10']="Baixado conforme instruções";
	$ocorrencia['11']="Títulos em ser (Conciliação Mensal)";
	$ocorrencia['12']="Abatimento concedido";
	$ocorrencia['13']="Abatimento cancelado";
	$ocorrencia['14']="Vencimento prorrogado";
	$ocorrencia['15']="Liquidação em cartório em dinheiro";
	$ocorrencia['16']="Liquidação - baixado/devolvido em data anterior dinheiro";
	$ocorrencia['17']="Entregue em cartório";
	$ocorrencia['18']="Instrução automática de protesto";
	$ocorrencia['21']="Instrução de alteração de mora";
	$ocorrencia['22']="Instrução de protesto processada/re-emitida";
	$ocorrencia['23']="Cancelamento de protesto processado";
	$ocorrencia['27']="Número do cedente ou controle do participante alterado.";
	$ocorrencia['31']="Liquidação normal em cheque/compensação/banco correspondente";
	$ocorrencia['32']="Liquidação em cartório em cheque";
	$ocorrencia['33']="Liquidação por conta em cheque";
	$ocorrencia['36']="Liquidação - baixado/devolvido em data anterior em cheque";
	$ocorrencia['37']="Baixa de título protestado";
	$ocorrencia['38']="Liquidação de título não registrado - em dinheiro";
	$ocorrencia['39']="Liquidação de título não registrado - em cheque";
	$ocorrencia['49']="Vencimento alterado";
	$ocorrencia['69']="Despesas/custas de cartório";
	$ocorrencia['70']="Ressarcimento sobre títulos.";
	$ocorrencia['71']="Ocorrência/Instrução não permitida para título em garantia de operação.";
	$ocorrencia['72']="Concessão de Desconto Aceito.";
	$ocorrencia['73']="Cancelamento Condição de Desconto Fixo Aceito";
	$ocorrencia['74']="Cancelamento de Desconto Diário Aceito.";
	
	return( $ocorrencia[$idOcorrencia]);
}

/**
 * Retorna a mensagem de ocorrencia da $idOcorrencia
 *
 * @param integer $idOcorrencia
 * @return string
 */
function layoutOcorrenciasCEF( $idOcorrencia ){
	$ocorrencia['01'] = 'Entrada Confirmada';
	$ocorrencia['02'] = 'Baixa Confirmada';
	$ocorrencia['03'] = 'Abatimento Concedido';
	$ocorrencia['04'] = 'Abatimento Cancelado';
	$ocorrencia['05'] = 'Vencimento Alterado';
	$ocorrencia['06'] = 'Uso da Empresa Alterado';
	$ocorrencia['07'] = 'Prazo de Protesto Alterado';
	$ocorrencia['08'] = 'Prazo de Devolução Alterado';
	$ocorrencia['09'] = 'Alteração Confirmada';
	$ocorrencia['10'] = 'Alteração com Reemissão de Boquete Confirmada';
	$ocorrencia['11'] = 'Alteração da Opção de Protesto para Devolução Confirmada';
	$ocorrencia['12'] = 'Alteração da Opção de Devolução para protesto Confirmada';
	$ocorrencia['20'] = 'Em Ser';
	$ocorrencia['21'] = 'Liquidação';
	$ocorrencia['22'] = 'Liquidação em Cartório';
	$ocorrencia['23'] = 'Baixa por Devolução';
	$ocorrencia['24'] = 'Baixa Franco Pagamento';
	$ocorrencia['25'] = 'Baixa por Protesto';
	$ocorrencia['26'] = 'Título enviado para Cartório';
	$ocorrencia['27'] = 'Sustação de Protesto';
	$ocorrencia['28'] = 'Estorno de Protesto';
	$ocorrencia['29'] = 'Estorno de Sustação de Protesto';
	$ocorrencia['30'] = 'Alteração de Título';
	$ocorrencia['31'] = 'Tarifa sobre Título Vencido';
	$ocorrencia['32'] = 'Outras Tarifas de Alteração';
	$ocorrencia['33'] = 'Estorno de Baixa /Liquidação';
	$ocorrencia['99'] = 'Rejeição do Título - Cód. rejeição informado nas POS 80 a 82';
	return( $ocorrencia[$idOcorrencia] );
}

function layoutOcorrenciasDebitoPadrao($idOcorrencia) {
	$ocorrencia['00']="Débito Efetuado.";
	$ocorrencia['01']="Débito nao efetuado. Insuficiencia de fundos";
	$ocorrencia['02']="Débito nao efetuado. Conta corrente nao cadastrada";
	$ocorrencia['04']="Débito nao efetuado. Outras restricoes";
	$ocorrencia['10']="Débito nao efetuado. Agencia em regime de encerramento";
	$ocorrencia['12']="Débito nao efetuado. Valor invalido";
	$ocorrencia['13']="Débito nao efetuado. Data de lancamento invalida";
	$ocorrencia['14']="Débito nao efetuado. Agencia invalida";
	$ocorrencia['15']="Débito nao efetuado. DAC da conta corrente invalido";
	$ocorrencia['18']="Débito nao efetuado. Data do debito anterior à o processamento";
	$ocorrencia['30']="Débito nao efetuado. Sem contrato de debito automatico";
	$ocorrencia['96']="Manutencao do Cadastro";
	$ocorrencia['97']="Cancelamento. Nao encontrado";
	$ocorrencia['98']="Cancelamento. Nao efetuado, fora do tempo habil";
	$ocorrencia['99']="Cancelamento. cancelada conforme solicitacao";
	
	return($ocorrencia["$idOcorrencia"]);

}

function layoutOcorrenciasSicoob($idOcorrencia) {
	$ocorrencia["02"] = "CONFIRMAÇÃO ENTRADA TÍTULO";
	$ocorrencia["03"] = "COMANDO RECUSADO";
	$ocorrencia["04"] = "TRANSFERENCIA DE CARTEIRA - ENTRADA";
	$ocorrencia["05"] = "LIQUIDAÇÃO SEM REGISTRO";
	$ocorrencia["06"] = "LIQUIDAÇÃO NORMAL";
	$ocorrencia["09"] = "BAIXA DE TÍTULO";
	$ocorrencia["10"] = "BAIXA SOLICITADA";
	$ocorrencia["11"] = "TÍTULOS EM SER";
	$ocorrencia["12"] = "ABATIMENTO CONCEDIDO";
	$ocorrencia["13"] = "ABATIMENTO CANCELADO";
	$ocorrencia["14"] = "ALTERAÇÃO DE VENCIMENTO";
	$ocorrencia["15"] = "LIQUIDAÇÃO EM CARTÓRIO";
	$ocorrencia["19"] = "CONFIRMAÇÃO INSTRUÇÃO PROTESTO";
	$ocorrencia["20"] = "DÉBITO EM CONTA";
	$ocorrencia["21"] = "ALTERAÇÃO DE NOME DO SACADO";
	$ocorrencia["22"] = "ALTERAÇÃO DE ENDEREÇO SACADO";
	$ocorrencia["23"] = "ENCAMINHADO A PROTESTO";
	$ocorrencia["24"] = "SUSTAR PROTESTO";
	$ocorrencia["25"] = "DISPENSAR JUROS";
	$ocorrencia["26"] = "INSTRUÇÃO REJEITADA";
	$ocorrencia["27"] = "CONFIRMAÇÃO ALTERAÇÃO DADOS";
	$ocorrencia["28"] = "MANUTENÇÃO TÍTULO VENCIDO";
	$ocorrencia["30"] = "ALTERAÇÃO DADOS REJEITADA";
	$ocorrencia["96"] = "DESPESAS DE PROTESTO";
	$ocorrencia["97"] = "DESPESAS DE SUSTAÇÃO DE PROTESTO";
	$ocorrencia["98"] = "DESPESAS DE CUSTAS ANTECIPADAS";
	
	return $ocorrencia["$idOcorrencia"];
}

?>
