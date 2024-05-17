<?

function contraPartida($modulo, $sub, $acao, $registro, $matriz){
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessPlanos;

	# Recebe ID do Serviço do Plano - Procurar detalhes
	$consulta=buscaServicosPlanos($registro, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		# Prosseguir e procurar detalhes sobre plano
		$idPlano=resultadoSQL($consulta, 0, 'idPlano');
			
		# Ver Plano
		verPlanos($modulo, $sub, 'abrir',$idPlano, $matriz);
			
		# Ver o Serviço
	//	verServicosPlanos($modulo, $sub, $acao, $registro, $matriz);
	}
	echo "<br>";
	
	if (!$matriz[idContraPartida] )$matriz[idContraPartida] = $_REQUEST[registro2];
	$matriz[idServicosPlanos] = $registro;
	if(substr($acao, 0, 19)=='contrapartidalistar') {	
		contraPartidaListar($modulo, $sub, $acao, $registro, $matriz);
	}
	elseif($acao == 'contrapartidaadicionar'){
		contraPartidaAdicionar($modulo, $sub, $acao, $registro , $matriz);
	}
	elseif($acao == 'contrapartidaver'){
		contraPartidaver($modulo, $sub, $acao, $registro , $matriz);
	}
	elseif($acao == 'contrapartidadesativar' || $acao == 'contrapartidaativar' || $acao == 'contrapartidacancelar'){
		contraPartidaAlterarStatus($modulo, $sub, $acao, $registro , $matriz);
	}
	elseif($acao == 'contrapartidaalterar' ){
		contraPartidaAlterar($modulo, $sub, $acao, $registro , $matriz);
	}	
}


//+--------------------+------------------+------+-----+-------------+----------------+
//| Field              | Type             | Null | Key | Default     | Extra          |
//+--------------------+------------------+------+-----+-------------+----------------+
//| id                 | int(11) unsigned |      | PRI | NULL        | auto_increment |
//| idServicosPlanos   | int(11) unsigned |      |     | 0           |                |
//| idPessoaTipo       | int(11) unsigned |      |     | 0           |                |
//| idPlanoDeContasDetalhes | int(11) unsigned |      |     | 0           |                |
//| tipoContraPartida  | varchar(25)      |      |     | pagar       |                |
//| tipoValor          | varchar(15)      |      |     | porcentagem |                |
//| valor              | float(6,2)       |      |     | 0.00        |                |
//| idVencimento       | int(1)           |      |     | 1           |                |
//| descricao          | varchar(255)     |      |     |             |                |
//| status             | char(1)          |      |     | A           |                |
//+--------------------+------------------+------+-----+-------------+----------------+

function dbContraPartida($matriz, $tipo, $subTipo='', $condicao='',$campos = "*" ){
	global $conn, $tb, $modulo, $sub, $acao;
	$data = dataSistema();
	
	$bd = new BDIT();
	$bd->setConnection($conn);
	
	if ($matriz[tabela]) $tabelas = $matriz[tabela];
	else $tabelas = $tb[ContraPartida]; 
	
	if ($tipo == 'incluir'){
		$campos = Array('idServicosPlanos',			'idPessoaTipo',			'idPlanoDeContasDetalhes',	'tipoValor',		'valor',			'idVencimento',			'descricao',		'status');
		$valores= Array($matriz[idServicosPlanos],	$matriz[idPessoaTipo],	$matriz[tipoConta],		$matriz[tipoValor],	$matriz[valor],	$matriz[vencimento],	$matriz[descricao],	$matriz[status]);
		
		$retorno = $bd->inserir($tabelas, $campos, $valores);
	}
		
	if($tipo == 'consultar'){
			
			if ($subTipo == 'unico') {
				$condicao = Array ("id = " . $matriz[idContraPartida]);
			}
			
			if ($subTipo== 'listar') {
				if ($matriz[cpp]){
					$campos = Array ('idpessoaTipo', $tb['PlanoDeContasDetalhes'].'.nome', 'Concat(valor, IF(tipoValor="porcentagem", " (%)", " (R$)")) valor', $tb['ContraPartidaPadrao'].'.status', $tb['ContraPartidaPadrao'].'.id');
					$tabelas = $tb['ContraPartidaPadrao']." LEFT JOIN ".$tb['PlanoDeContasDetalhes']." ON (".$tb['ContraPartidaPadrao'].
							".idPlanoDeContasDetalhes = ".$tb['PlanoDeContasDetalhes'].".id)";					
				}
				else{
					$campos = Array ('idpessoaTipo', $tb['PlanoDeContasDetalhes'].'.nome', 'Concat(valor, IF(tipoValor="porcentagem", " (%)", " (R$)")) valor', $tb['ContraPartida'].'.status', $tb['ContraPartida'].'.id');
					$tabelas = $tb['ContraPartida']." LEFT JOIN ".$tb['PlanoDeContasDetalhes']." ON (".$tb['ContraPartida'].
							".idPlanoDeContasDetalhes = ".$tb['PlanoDeContasDetalhes'].".id)";
				}
				if($matriz[idServicosPlanos])	$condicao = Array ("idServicosPlanos = " . $matriz[idServicosPlanos]);
			}
			
			if ($subTipo == 'checarDocumento' && $matriz[idContasAReceber] ) {
				$campos = Array("idServicosPlanos " ,
								"ContasAReceber.valor ",
								"ContasAReceber.valorRecebido ",
								"ServicosPlanos.valor AS valorServicoEspecial ",
								"Servicos.valor As valorServico ",
								"PlanosPessoas.especial");		
				
				$tabelas = Array("ContasAReceber 
									INNER JOIN PlanosDocumentosGerados 
										On (ContasAReceber.idDocumentosGerados = PlanosDocumentosGerados.idDocumentoGerado) 
									INNER JOIN ServicosPlanosDocumentosGerados 
										On (PlanosDocumentosGerados.id = ServicosPlanosDocumentosGerados.idPlanoDocumentoGerado) 
									INNER JOIN ServicosPlanos 
										On (ServicosPlanosDocumentosGerados.idServicosPlanos = ServicosPlanos.id) 
									INNER JOIN PlanosPessoas 
										On (ServicosPlanos.idPlano = PlanosPessoas.id) 
									INNER JOIN Servicos 
										On (ServicosPlanos.idServico = Servicos.id)");	
				$condicao = $tb[ContasReceber].".id = " . $matriz[idContasAReceber];
				
			}
			
			/*seleciona os dados da contrapartida*/
			if ($subTipo == 'checarServico' && $matriz[idServicoPlano] ) {
				$condicao[] = "idServicosPlanos = " . $matriz[idServicoPlano];
				$condicao[] = "status =  'A'";					
			}
			$retorno = $bd->seleciona($tabelas, $campos, $condicao);
	}
	
	if($tipo == 'alterar'){
		switch ($subTipo) {
			case 'status':
				$campos   = "status";
				$valores  = $matriz[status];
				break;
			case 'registro':
				$campos = Array('idPessoaTipo',			'idPlanoDeContasDetalhes',	'tipoValor',		'valor',			'idVencimento',			'descricao',		'status');
				$valores= Array($matriz[idPessoaTipo],	$matriz[tipoConta],		$matriz[tipoValor],	$matriz[valor],	$matriz[vencimento],	$matriz[descricao],	$matriz[status]);
				break;
		}
		$condicao =  "id = " . $matriz[idContraPartida];

		$retorno = $bd->alterar($tabelas, $campos, $valores, $condicao);
	}
	return ($retorno);
}


function contraPartidaChecarDocumento ($idBaixa, $valor, $baixar = 1) {
	
	$matriz[idContasAReceber] = $idBaixa;
	
	$servicos = dbContraPartida($matriz, 'consultar', 'checarDocumento');
	
	for ($i=0; $i< count($servicos); $i++ ){
		
		#se for um unico servico, nao precisa calcular o valor proporcional :)
		if ( $valor != $servicos[$i]->valorServico)	{
			#calcula o valor de cada servico
			if ($servicos[$i]->especial == "S")
				$valorServico = $servicos[$i]->valorServicoEspecial;
			
			else
				$valorServico = $servicos[$i]->valorServico;

//			$valorServico = calculaProporcional($valor, $servicos[$i]->valor, $valorServico); sem valores proporcionais. valores do servico only.
		}
		else	$valorServico = $valor;
		contraPartidaChecarServico($servicos[$i]->idServicosPlanos, $valorServico, $baixar);
	}
}



/**
 * @author 
 * verifica se existe uma contra partida para determinado servico
 * se sim, e o parametro baixar for verdadeiro, ja executa a contra partida.  
 * 
 * @param int idServicoPlano idServicoPlano a ser consultado.
 * @param float valor valor do pagamento
 * @param int baixar 1 executa a ContraPartida, 0 nao
 * 
 * @return int 0 caso nao haja contra partida
 */
function contraPartidaChecarServico($idServicoPlano, $valor, $baixar){
	
	$matriz[idServicoPlano] = $idServicoPlano;
	
	/*faz a consulta*/
	$contraPartidas = dbContraPartida($matriz, 'consultar', 'checarServico');
	
	if (!empty($contraPartidas) ){
	
		if ($baixar == 1 ){
			$grava = contraPartidaExecutar($contraPartidas, $valor);	
		}
	
		return (1);	
	}
	
	return (0);
}

/**
 * @author gustavo
 * 
 * @desc varre um array de objetos contrapartida chamando as rotinas de execucao das mesmas
 * @param array contraPartidas matriz de objetos de contra partida
 * @param float valor valor base para calculo das contra partidas
 */
function contraPartidaExecutar ($contraPartidas, $valor) {
	
	for ($i = 0; $i < sizeof($contraPartidas); $i++) {
				
		if (strtoupper($contraPartidas[$i]->tipoContraPartida) == "PAGAR" ){
			contraPartidaPagar($contraPartidas[$i], $valor);	
		}
		
		elseif ($contraPartidas[$i]->tipoContraPartida == "RECEBER" ){
			contraPartidaReceber($contraPartidas[$i], $valor);
		}	
	}
	
}


/**
 * @author gustavo
 * 
 * @desc calcula  o valor da contra partida e a lanca em contas a pagar.
 * @param objeto contraPartida objeto contrapartida a ser executado
 * @param float valor valor base para calculo da contra partida
 */
function contraPartidaPagar($contraPartida, $valor) {
	global $conn;
	if ($contraPartida->tipoValor == 'porcentagem'){
		
		$parametros = carregaParametrosConfig();
		$valorImposto = $parametros['abatimento_cp'];
				
		if ( $valorImposto > 0)
			$valor -= $valor * ($valorImposto / 100);
		
		$valorContraPartida = ($valor * $contraPartida->valor)/100;
		
	}
	elseif ($contraPartida->tipoValor == 'valorFixo')
		$valorContraPartida = $contraPartida->valor;
	

	if ($valorContraPartida > 0 ){
		
		/* prepara os dados a serem gravados como uma conta a pagar*/
		$matriz[idPessoaTipo] = $contraPartida->idPessoaTipo;
		$matriz[idPlanoDeContasDetalhes] = $contraPartida->idPlanoDeContasDetalhes;
		$matriz[valor] = $valorContraPartida;
		
		$vencimento = dadosVencimento($contraPartida->idVencimento);
		
		$data = dataSistema();
		if ($vencimento['diaVencimento'] < $data[dia]){
			if ($data[mes] == 12 )	$mes=1;
			else 					$mes = $data[mes] +1;
			
			$dtVencimento = $data[ano]."-".$mes."-".$vencimento['diaVencimento'];
		}else
			$dtVencimento = $data[ano]."-".$data[mes]."-".$vencimento['diaVencimento'];
			
		$sql = "Select Servicos.nome from ServicosPlanos INNER JOIN Servicos On(ServicosPlanos.idServico=Servicos.id) where ServicosPlanos.id='".$contraPartida->idServicosPlanos."'";
		$consulta = consultaSQL($sql, $conn);
		$nomeServico = resultadoSQL($consulta, 0, "nome");
		$dominio = buscarNomesDominiosServicosPlanos($contraPartida->idServicosPlanos);
		
		$matriz['idPop'] = 1; #8-|
		
		$matriz[data] = $dtVencimento;
		$matriz[obs]  = "referente a contra partida gerada pelo pagamento de R$ ". formatarValoresForm($valor).
						" na data " . $data[dataNormal] . "do servico " .
						"<a href=?modulo=lancamentos&sub=planos&acao=verservico&registro=". 
						$contraPartida->idServicosPlanos .">".$nomeServico."</a>".$dominio;
		
		$grava = dbContasAPagar($matriz, 'incluir');
		$idContasAPagar=mysql_insert_id();
		
		if ($grava){
				itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
				$texto = htmlMontaOpcao('<a href=?modulo=contas_a_pagar&acao=info&registro='.$idContasAPagar.'><span class=txtaviso>Contra Partida Lançada!</span></a>', 'desconto');
				itemTabelaNOURL($texto, 'center', $corFundo, 3, 'tabfundo1');
		}
	
	}
		
}


function contraPartidaListar ($modulo, $sub, $acao, $registro, $matriz) {

	$tabela[exibe][titulo]=1;
	$tabela[exibe][filtros]=0;
	$tabela[exibe][subMenu]=1;
	$tabela[exibe][total]=0;
		
	$tabela[titulo] = "Contra Partidas";
		
	$tabela[gravata]    =	Array('Beneficiado',	'Plano de Custo',	'Valor',	'Status',	'Opções');
	$tabela[formatos] 	= 	Array('fornecedor',		'planoDeContasPai',	'texto',		'status',	'opcoes');
	$tabela[tamanho]    =	Array('30%',			'25%',	 			'10%',			'10%',		'25%');
	$tabela[alinhamento]=   Array('left',			'left',				'right',		'center',	'center');

	$tabela[detalhe] = dbContraPartida($matriz, 'consultar', 'listar') ;
	
	echo "<br>";
	exibeNovaTabela($tabela, $modulo, $sub, $acao, $registro, $matriz);
}


function formContraPartida ($modulo, $sub, $acao, $registro, $matriz) {
	global $corFundo, $corBorda, $html;

	if ($matriz[idContraPartida]){
			$cons = dbContraPartida($matriz, 'consultar', 'unico');
	}
	else{
		$dadosServicosPlanos =   dadosServicoPlano($matriz[idServicosPlanos]);
		$cons = contraPartidaPadraoBuscaValores($dadosServicosPlanos[idServico]);	
	}
	
	if ($cons[0]->idPessoaTipo && ! $matriz[idPessoaTipo])
		$matriz[idPessoaTipo] = $cons[0]->idPessoaTipo;
	
	echo "<br>";
	novaTabela2("[Contra Partida]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	novaLinhaTabela($corFundo, '100%');
	$texto="			
		<form method=post name=matriz action='index.php'>
		<input type=hidden name=modulo value='$modulo'>
		<input type=hidden name=sub value='$sub'>
		<input type=hidden name=acao value='$acao'> 
		<input type=hidden name=registro value='$registro'>
		<input type=hidden name=matriz[idPessoaTipo] value='".$matriz[idPessoaTipo]."'>
		<input type=hidden name=matriz[idServicosPlanos] value='$matriz[idServicosPlanos]'>
		<input type=hidden name=matriz[idContraPartida] value='$matriz[idContraPartida]'>";
		
	itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
	fechaLinhaTabela();
	
	getCampo('combo', '<b>Dados da pessoa Beneficiada<b>', '', '' );
	
	getCampo('combo', 'Tipo Pessoa:<br><span class=normal10>Selecione o Tipo de Pessoa Beneficiada</span>', '', formSelectTipoPessoa('', 'idTipoPessoa', 'form', false, ( $matriz['idTipoPessoa'] ? $matriz['idTipoPessoa'] : 1) , false));
	
	procurarPessoasSelect($modulo, $sub, 'procurar', $registro, $matriz, '0');
	
//	getCampo('combo', '', '', '&nbsp;' );

	if ($sub == 'contraPartidaPadrao')
		getCampo('combo', 'Nome do Serviço', '',  formSelectServicos($matriz[idServicosPlanos], idServicosPlanos, 'formnochange'));

	getCampo('combo', '<b>Dados da Contra Partida<b>', '', '' );
	$contasDetalhes = dbPlanoDeContasDetalhes('', 'consultar', '', "status='A'");
	getCampo('combo', "Tipo De Conta", '', getSelectObjetos('matriz[tipoConta]', $contasDetalhes, 'nome', 'id', $cons[0]->idPlanoDeContasDetalhes));
	
	$tipo = "<input type=text name='matriz[valor]' size=4 value='".$cons[0]->valor ."' onBlur=verificarValor(0,this.value);formataValor(this.value,12)>, ";
	$tipo .= getSelectNovo(Array("porcentagem= % ", "valorFixo= R$ "), 'matriz[tipoValor]',0 , '0', $cons[0]->tipoValor);
	$tipo .= "<span class=txtaviso> (Formato: 999,00)</span>";
	getCampo('combo', 'Valor da Contra partida', '', $tipo);
	
//	getCampo('text','Valor', 'matriz[valor] ', $cons[0]->valor, '','', 4);
		
	getCampo('combo','Dia de Vencimento', '', formSelectVencimento( $cons[0]->idVencimento, 'vencimento', 'form'), '');
	
	getCampo('text','Descricao', 'matriz[descricao] ', $cons[0]->descricao, '','', 50);
		
	getCampo('combo', 'Status', 'status', formSelectStatusPOP($cons[0]->status, 'status', 'form')) ; //Ativo ou inativo
		
	getBotao('matriz[bntConfirmar]', 'Confirmar');
	
}


function contraPartidaAdicionar($modulo, $sub, $acao, $registro, $matriz){
	if($matriz[bntConfirmar] && $matriz[idPessoaTipo] && $matriz[tipoConta] && $matriz[valor]){
				
		$matriz[valor] = formatarValores($matriz[valor]);
		
		if (!$matriz[idServicosPlanos])	$matriz[idServicosPlanos] = $registro;
		
		$grava = dbContraPartida($matriz, 'incluir');  
		
		if ($grava)
			avisoNOURL("Aviso:", "Registro Gravado com Sucesso.", "100%");
		else
			avisoNOURL("Aviso:", "Falha na tentativa de salvar registro. $grava", "100%" );
		
		echo "<br>";
		if ($modulo != 'pagamento_avulso')
			contraPartidaListar($modulo, $sub, $acao, $registro, $matriz);			
	}
	else{
		if ($matriz[bntConfirmar])
			avisoNOURL("Atenção:", "Por favor, prencha os dados da Contra Partida deste serviço.", "100%" );
		formContraPartida($modulo, $sub, $acao, $registro, $matriz);
	}
}

function contraPartidaVer ($modulo, $sub, $acao, $registro, $matriz) {

	$cons = dbContraPartida($matriz, 'consultar', 'unico');
	
	$tabela[exibe][titulo]=1;

	$tabela[titulo] = "Contra Partida";
	

	if($acao != 'contrapartidaver' && $acao != 'contrapartidapadraover' ) $tabela[exibe][bntConfirmar] = 1;


	($sub == 'contraPartidaPadrao' ? $tipoServico = 'servico' : $tipoServico = 'servicosPlanos');

	$tabela[exibe][subMenu]=1;
	
	$tipoValor = ( $cons[0]->tipoValor == 'porcentagem' ? "(%)" : "(R$)");
	
	$tabela[gravata]    =   Array('Beneficiado',			'Servico', 					'Plano de Custo', 				'Tipo Contra Partida',			'Valor ' . $tipoValor ,			'Vencimetnos',			'Descrição',			'Status');
	$tabela[valores] 	= 	Array($cons[0]->idPessoaTipo,	$cons[0]->idServicosPlanos,	$cons[0]->idPlanoDeContasDetalhes,	$cons[0]->tipoContraPartida,	$cons[0]->valor,			$cons[0]->idVencimento, $cons[0]->descricao,	$cons[0]->status);
	$tabela[formatos] 	= 	Array('pessoa',					$tipoServico,				'planoDeContasDetalhes',				'',									'moeda',		 			'vencimento',			'',						'status');	
	
	echo "<br>";
	exibeFormulario($tabela, $tipo, $modulo, $sub, $acao, $registro, $matriz[idContraPartida]);
	
}

function contraPartidaAlterarStatus ($modulo, $sub, $acao, $registro, $matriz) {
	
	switch ($acao) {
		case 'contrapartidaativar':
			$matriz[status] = 'A';
			break;
	
		case 'contrapartidadesativar':
			$matriz[status] = 'I';
			break;
				
		case 'contrapartidacancelar':
			$matriz[status] = 'C';
			break;
	}
	
	
	if ( !$matriz[bntConfirmar] )
		contraPartidaVer($modulo, $sub, $acao, $registro, $matriz);
	
	elseif($matriz[idContraPartida]){
		
		$grava = dbContraPartida($matriz, 'alterar', 'status');
		
		echo '<br>';
		if($grava)	avisoNOURL("Aviso:", "Registro Alterado com Sucesso",'');
		else		avisoNOURL("Erro:", "Erro ao Alterar Registro",'');
		
		echo '<br>';
		contraPartidaListar($modulo, $sub, $acao, $registro, $matriz);
	
	
	}
}

function contraPartidaAlterar($modulo, $sub, $acao, $registro, $matriz){
	
	if($matriz[bntConfirmar] && $matriz[idPessoaTipo] && $matriz[tipoConta] && $matriz[valor]){
		
		$matriz[valor] = formatarValores($matriz[valor]);
		
		$grava = dbContraPartida($matriz, 'alterar', 'registro' );
		
		if($grava)	avisoNOURL("Aviso:", "Registro Alterado com Sucesso",'');
		else		avisoNOURL("Erro:", "Erro ao Alterar Registro",'');
		
		contraPartidaListar($modulo, $sub, $acao, $registro, $matriz);
	}
	else{
		if ($matriz[bntConfirmar])
			avisoNOURL("Atenção:", "Por Favor, Preencha todos campos corretamente", "100%" );
		formContraPartida($modulo, $sub, $acao, $registro, $matriz);
	}
	
	
}
?>