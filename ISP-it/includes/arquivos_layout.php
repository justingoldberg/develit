<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 03/02/2004
# Ultima altera��o: 03/02/2004
#    Altera��o No.: 001
#
# Fun��o:
#    Fun��es para verifica��o de layouts de arquivos


function layoutOcorrenciasItau($idOcorrencia) {
	$ocorrencia['02']="Entrada Confirmada";
	$ocorrencia['03']="Entrada Rejeitada";
	$ocorrencia['04']="Altera��o de Dados - Nova Entrada";
	$ocorrencia['05']="Altera��o de Dados - Baixa";
	$ocorrencia['06']="Liquida��o Normal";
	$ocorrencia['08']="Liquida��o em Cart�rio";
	$ocorrencia['09']="Baixa Simples";
	$ocorrencia['10']="Baixa por Ter Sido Liquidado";
	$ocorrencia['11']="Em Ser";
	$ocorrencia['12']="Abatimento Concedido";
	$ocorrencia['13']="Abatimento Cancelado";
	$ocorrencia['14']="Vencimento Alterado";
	$ocorrencia['15']="Baixas Rejeitadas";
	$ocorrencia['16']="Instru��es Rejeitadas";
	$ocorrencia['17']="Altera��es de Dados Rejeitados";
	$ocorrencia['18']="Cobran�a Contratual - Abatimento e Baixa Bloqueados";
	$ocorrencia['19']="Confirma Recebimento de Instru��o de Protesto";
	$ocorrencia['20']="Confirma Recebimento de Instru��o de Susta��o de Protesto/Tarifa";
	$ocorrencia['21']="Confirma Recebimento de Instru��o de N�o Protestar";
	$ocorrencia['23']="Protesto Enviado a Cat�rio/Tarifa";
	$ocorrencia['24']="Instru��es de Protesto Rejeitada/Sustada Pendente";
	$ocorrencia['25']="Alega��es do Sacado";
	$ocorrencia['26']="Tarifa de Aviso de Cobran�a";
	$ocorrencia['27']="Tarifa de Extrato Posi��o";
	$ocorrencia['28']="Tarifa de Rela��o das Liquida��es";
	$ocorrencia['29']="Tarifa de Manuten��o de T�tulos Vencidos";
	$ocorrencia['30']="D�bito Mensal de Tarifas";
	$ocorrencia['32']="Baixa por Ter Sido Protestado";
	$ocorrencia['33']="Cestas de Protesto";
	$ocorrencia['34']="Custas de Susta��o";
	$ocorrencia['35']="Custas de Cart�rio Distribuidor";
	$ocorrencia['36']="Custas de Edital";
	$ocorrencia['37']="Tarifa de Emiss�o de Bloqueto/Tarifa de Envio de Duplicata";
	$ocorrencia['38']="Tarifa de Instru��o";
	$ocorrencia['39']="Tarifa de Ocorr�ncias";
	$ocorrencia['40']="Tarifa Mensal de Emiss�o de Bloqeto/Tarifa Mensal de Envio de Duplicata";
	$ocorrencia['41']="D�bito Mensal de Tarifas - Extrato de Posi��o";
	$ocorrencia['42']="B�dito Mensal de Tarifas - Outras Instru��es";
	$ocorrencia['43']="D�bito Mensal de Tarifas - Manuten��o de T�tulos Vencidos";
	$ocorrencia['44']="D�bito Mensal de Tarifas - Outras Ocorr�ncias";
	$ocorrencia['45']="D�bito Mensal de Tarifas - Protesto";
	$ocorrencia['46']="D�bito mensal de Tarifas - Susta��o de Protesto";
	$ocorrencia['47']="Baixa Transfer�ncia para Desconto";
	$ocorrencia['48']="Custas de Susta��o Judicial";
	$ocorrencia['51']="Tarifa Mensal Ref Entradas Bancos Correspondentes na Carteira";
	$ocorrencia['52']="Tarifa Mensal Baixas na Carteira";
	$ocorrencia['53']="Tarifa Mensal BAixas em Bancos Correspondentes na Carteira";
	$ocorrencia['54']="Tarifa Mensal de Liquida��es na Carteira";
	$ocorrencia['55']="Tarifa Mensal de Liquida��es em Bancos Correspondentes na Carteira";
	$ocorrencia['56']="Custas de Irregularidades";
	$ocorrencia['57']="Instru��o Cancelada";
	$ocorrencia['60']="Entrada Rejeitada Carn�";
	$ocorrencia['61']="Tarifa Emiss�o Aviso de Movimenta��o de T�tulos";
	$ocorrencia['62']="D�bito Mensal de Tarifa - Aviso de Movimenta��o T�tulos";
	
	return($ocorrencia["$idOcorrencia"]);

}


function getMotivoOcorrencia($idOcorrencia, $motivos='N'){
	$ret = array();
	if($idOcorrencia == '10'){
		if(substr_count($motivos, '20')) $ret[] = ' - Titulo Baixado e transferido para desconto';
		if(substr_count($motivos, '16')) $ret[] = ' - Titulo Baixado pelo banco por decurso do Prazo';
		if(substr_count($motivos, '15')) $ret[] = ' - Titulo Exclu�do';
		if(substr_count($motivos, '14')) $ret[] = ' - Titulo Protestado';
	}
	
	return(implode('  ',$ret) );
		
}
	
function layoutOcorrenciasBradesco($idOcorrencia, $motivos='N') {
	$ocorrencia['02']="Entrada Confirmada";
	$ocorrencia['03']="Entrada Rejeitada";
	$ocorrencia['06']="Liquida��o Normal";
	$ocorrencia['09']="Baixa Automatica via Arquivo";
	$ocorrencia['10']="Baixa conforme instru��es da Ag�ncia";
	$ocorrencia['11']="Em Ser";
	$ocorrencia['12']="Abatimento Concedido";
	$ocorrencia['13']="Abatimento Cancelado";
	$ocorrencia['14']="Vencimento Alterado";
	$ocorrencia['15']="Liquida��o em cart�rio";
	$ocorrencia['16']="Titulo Pago em Cheque - Vinculado";
	$ocorrencia['17']="Liquida��o ap�s baixa ou T�tulo n�o registrado";
	$ocorrencia['18']="Acerto de Deposit�ria";
	$ocorrencia['19']="Confirma Recebimento de Instru��o de Protesto";
	$ocorrencia['20']="Confirma Recebimento de Instru��o de Susta��o de Protesto";
	$ocorrencia['21']="Acerto do Controle do Participante";
	$ocorrencia['22']="T�tulo com Pagamento Cancelado";
	$ocorrencia['23']="Entrada do T�tulo em Cart�rio";
	$ocorrencia['24']="Entrada Rejeitada por CEP irregular";
	$ocorrencia['27']="Baixa Rejeitda";
	$ocorrencia['28']="D�bito de Tarifas/Custos";
	$ocorrencia['30']="Altera��o de Outros Dados Rejeitados";
	$ocorrencia['32']="Instru��o Rejeitada";
	$ocorrencia['33']="Confirmada Pedido Altera��o Outros Dados";
	$ocorrencia['34']="Retirado de Cart�rio e Manuten��o de Carteira";
	$ocorrencia['35']="Desagendamento do d�bito autom�tico";
	$ocorrencia['68']="Acerto dos dados do rateio de Cr�dito";
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
	$ocorrencia['04']="Transfer�ncia de carteira-entrada";
	$ocorrencia['05']="Transferencia de carteira-baixa";
	$ocorrencia['06']="Liquida��o Normal";
	$ocorrencia['07']="Liquida��o por Conta";
	$ocorrencia['08']="Liquida��o por saldo";
	$ocorrencia['10']="Baixado conforme comando via arquivo - remessa";
	$ocorrencia['11']="Baixado conforme comando ag�ncia";
	$ocorrencia['12']="Abatimento Concedido";
	$ocorrencia['13']="Abatimento Cancelado";
	$ocorrencia['14']="Vencimento Alterado";
	$ocorrencia['15']="Liquida��o em cart�rio";
	$ocorrencia['23']="Remessa a Cart�rio";
	$ocorrencia['23']="Remessa a Cart�rio";
	$ocorrencia['24']="Retirada a Cart�rio";
	$ocorrencia['25']="baixado por ter sido protestado";
	$ocorrencia['28']="D�bito de Tarifas/Custos";
	$ocorrencia['30']="Desconto concedido";
	return($ocorrencia["$idOcorrencia"]);

}

function layoutOcorrenciasBansicredi($idOcorrencia) {
	$oc['02']='Entrada Confirmada';
	$oc['03']='Entrada Rejeitada';
	$oc['06']='Liquida��o Normal';
	$oc['09']='Baixado Automaticamente via Arquivo';
	$oc['10']='Baixado Conforme Instru��es da Ag�ncia';
	$oc['12']='Abatimento concedido';
	$oc['13']='Abatimento Cancelado';
	$oc['14']='Vencimento Alterado';
	$oc['15']='Liquida��o em Cart�rio';
	$oc['17']='Liquida��o Ap�s Baixa';
	$oc['19']='Confirma��o de Recebimento de Instru��o de Protesto';
	$oc['20']='Confirma��o de Recebimento de Instru��o de Susta��o de Protesto';
	$oc['23']='Entrada de T�tulo em Cart�rio';
	$oc['24']='Entrada Rejeitada por CEP irregular';
	$oc['27']='Baixa Rejeitada';
	$oc['28']='Tarifa';
	$oc['30']='Altera��o Rejeitada';
	$oc['32']='Instru��o Rejeitada';
	$oc['33']='Confirma��o de Pedido de Altera��o de Outros Dados';
	$oc['34']='Retirado de Cart�rio e Manuten��o em Carteira';
	return $oc[$idOcorrencia];
}

function layoutOcorrenciasHSBC($idOcorrencia) {
	$ocorrencia['02']="Entrada confirmada";
	$ocorrencia['03']="Entrada rejeitada ou Instru��o rejeitada";
	$ocorrencia['06']="Liquida��o normal em dinheiro";
	$ocorrencia['07']="Liquida��o por conta em dinheiro";
	$ocorrencia['09']="Baixa autom�tica";
	$ocorrencia['10']="Baixado conforme instru��es";
	$ocorrencia['11']="T�tulos em ser (Concilia��o Mensal)";
	$ocorrencia['12']="Abatimento concedido";
	$ocorrencia['13']="Abatimento cancelado";
	$ocorrencia['14']="Vencimento prorrogado";
	$ocorrencia['15']="Liquida��o em cart�rio em dinheiro";
	$ocorrencia['16']="Liquida��o - baixado/devolvido em data anterior dinheiro";
	$ocorrencia['17']="Entregue em cart�rio";
	$ocorrencia['18']="Instru��o autom�tica de protesto";
	$ocorrencia['21']="Instru��o de altera��o de mora";
	$ocorrencia['22']="Instru��o de protesto processada/re-emitida";
	$ocorrencia['23']="Cancelamento de protesto processado";
	$ocorrencia['27']="N�mero do cedente ou controle do participante alterado.";
	$ocorrencia['31']="Liquida��o normal em cheque/compensa��o/banco correspondente";
	$ocorrencia['32']="Liquida��o em cart�rio em cheque";
	$ocorrencia['33']="Liquida��o por conta em cheque";
	$ocorrencia['36']="Liquida��o - baixado/devolvido em data anterior em cheque";
	$ocorrencia['37']="Baixa de t�tulo protestado";
	$ocorrencia['38']="Liquida��o de t�tulo n�o registrado - em dinheiro";
	$ocorrencia['39']="Liquida��o de t�tulo n�o registrado - em cheque";
	$ocorrencia['49']="Vencimento alterado";
	$ocorrencia['69']="Despesas/custas de cart�rio";
	$ocorrencia['70']="Ressarcimento sobre t�tulos.";
	$ocorrencia['71']="Ocorr�ncia/Instru��o n�o permitida para t�tulo em garantia de opera��o.";
	$ocorrencia['72']="Concess�o de Desconto Aceito.";
	$ocorrencia['73']="Cancelamento Condi��o de Desconto Fixo Aceito";
	$ocorrencia['74']="Cancelamento de Desconto Di�rio Aceito.";
	
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
	$ocorrencia['08'] = 'Prazo de Devolu��o Alterado';
	$ocorrencia['09'] = 'Altera��o Confirmada';
	$ocorrencia['10'] = 'Altera��o com Reemiss�o de Boquete Confirmada';
	$ocorrencia['11'] = 'Altera��o da Op��o de Protesto para Devolu��o Confirmada';
	$ocorrencia['12'] = 'Altera��o da Op��o de Devolu��o para protesto Confirmada';
	$ocorrencia['20'] = 'Em Ser';
	$ocorrencia['21'] = 'Liquida��o';
	$ocorrencia['22'] = 'Liquida��o em Cart�rio';
	$ocorrencia['23'] = 'Baixa por Devolu��o';
	$ocorrencia['24'] = 'Baixa Franco Pagamento';
	$ocorrencia['25'] = 'Baixa por Protesto';
	$ocorrencia['26'] = 'T�tulo enviado para Cart�rio';
	$ocorrencia['27'] = 'Susta��o de Protesto';
	$ocorrencia['28'] = 'Estorno de Protesto';
	$ocorrencia['29'] = 'Estorno de Susta��o de Protesto';
	$ocorrencia['30'] = 'Altera��o de T�tulo';
	$ocorrencia['31'] = 'Tarifa sobre T�tulo Vencido';
	$ocorrencia['32'] = 'Outras Tarifas de Altera��o';
	$ocorrencia['33'] = 'Estorno de Baixa /Liquida��o';
	$ocorrencia['99'] = 'Rejei��o do T�tulo - C�d. rejei��o informado nas POS 80 a 82';
	return( $ocorrencia[$idOcorrencia] );
}

function layoutOcorrenciasDebitoPadrao($idOcorrencia) {
	$ocorrencia['00']="D�bito Efetuado.";
	$ocorrencia['01']="D�bito nao efetuado. Insuficiencia de fundos";
	$ocorrencia['02']="D�bito nao efetuado. Conta corrente nao cadastrada";
	$ocorrencia['04']="D�bito nao efetuado. Outras restricoes";
	$ocorrencia['10']="D�bito nao efetuado. Agencia em regime de encerramento";
	$ocorrencia['12']="D�bito nao efetuado. Valor invalido";
	$ocorrencia['13']="D�bito nao efetuado. Data de lancamento invalida";
	$ocorrencia['14']="D�bito nao efetuado. Agencia invalida";
	$ocorrencia['15']="D�bito nao efetuado. DAC da conta corrente invalido";
	$ocorrencia['18']="D�bito nao efetuado. Data do debito anterior � o processamento";
	$ocorrencia['30']="D�bito nao efetuado. Sem contrato de debito automatico";
	$ocorrencia['96']="Manutencao do Cadastro";
	$ocorrencia['97']="Cancelamento. Nao encontrado";
	$ocorrencia['98']="Cancelamento. Nao efetuado, fora do tempo habil";
	$ocorrencia['99']="Cancelamento. cancelada conforme solicitacao";
	
	return($ocorrencia["$idOcorrencia"]);

}

function layoutOcorrenciasSicoob($idOcorrencia) {
	$ocorrencia["02"] = "CONFIRMA��O ENTRADA T�TULO";
	$ocorrencia["03"] = "COMANDO RECUSADO";
	$ocorrencia["04"] = "TRANSFERENCIA DE CARTEIRA - ENTRADA";
	$ocorrencia["05"] = "LIQUIDA��O SEM REGISTRO";
	$ocorrencia["06"] = "LIQUIDA��O NORMAL";
	$ocorrencia["09"] = "BAIXA DE T�TULO";
	$ocorrencia["10"] = "BAIXA SOLICITADA";
	$ocorrencia["11"] = "T�TULOS EM SER";
	$ocorrencia["12"] = "ABATIMENTO CONCEDIDO";
	$ocorrencia["13"] = "ABATIMENTO CANCELADO";
	$ocorrencia["14"] = "ALTERA��O DE VENCIMENTO";
	$ocorrencia["15"] = "LIQUIDA��O EM CART�RIO";
	$ocorrencia["19"] = "CONFIRMA��O INSTRU��O PROTESTO";
	$ocorrencia["20"] = "D�BITO EM CONTA";
	$ocorrencia["21"] = "ALTERA��O DE NOME DO SACADO";
	$ocorrencia["22"] = "ALTERA��O DE ENDERE�O SACADO";
	$ocorrencia["23"] = "ENCAMINHADO A PROTESTO";
	$ocorrencia["24"] = "SUSTAR PROTESTO";
	$ocorrencia["25"] = "DISPENSAR JUROS";
	$ocorrencia["26"] = "INSTRU��O REJEITADA";
	$ocorrencia["27"] = "CONFIRMA��O ALTERA��O DADOS";
	$ocorrencia["28"] = "MANUTEN��O T�TULO VENCIDO";
	$ocorrencia["30"] = "ALTERA��O DADOS REJEITADA";
	$ocorrencia["96"] = "DESPESAS DE PROTESTO";
	$ocorrencia["97"] = "DESPESAS DE SUSTA��O DE PROTESTO";
	$ocorrencia["98"] = "DESPESAS DE CUSTAS ANTECIPADAS";
	
	return $ocorrencia["$idOcorrencia"];
}

?>
