<?
################################################################################
#       Criado por: Devel IT
#  Data de criação: 28/06/2004
# Ultima alteração: 28/06/2004
#    Alteração No.: 001
#
# Função:
#    Painel - Funções para cadastramento de servicosIVR


# abertura do modulo
function servicosIVR($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessCadastro, $titulo;
	
	$titulo = "<b>Serviço IVR</b>";
	$subtitulo = "<br>Administração de Serviços IVR";
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[adicionar]) {
		exibeSemPermissao($modulo, $acao);
	}
	else {
		
		# Topo da tabela - Informações e menu principal do Cadastro
		$itens=array('Adicionar');
		
		#Monta a Tela Padrão
		novaTabela2("[$titulo]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 4);
			novaLinhaTabela($corFundo, '100%');
				#coluna de identificação do modulo
				htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
					echo "<br><img src=".$html[imagem][cadastro].
					     " border=0 align=left><b class=bold></b>".
					     "<br><span class=normal10>$titulo $subtitulo</b>.</span>";
				htmlFechaColuna();
				
				#exibe os icones iniciais
				$texto=htmlMontaOpcao("<br>Adicionar", 'incluir');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=config&subacao=adicionar&registro=$registro", 'center', $corFundo, 0, 'normal');
			fechaLinhaTabela();
		fechaTabela();
		
		# case das acoes
		echo "<br>";
		switch ($acao) {
			case "adicionar":		
				servicosIVRAdicionar($modulo, $sub, $acao, $registro, $matriz);
				break;
			case "alterar":
				servicosIVRAlterar($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'procurar':
				servicosIVRProcurar($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'ver':
				servicosIVRVer($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'excluir':
				# Validação para definir quando o formulário de exclusão deve ser exibido  - por Felipe Assis - 25/03/2008
				if(!$matriz['btExcluir']){
					formConfirmExcServicoIVR($modulo, $sub, $acao, $registro, $matriz);
				}
				else{
					servicosIVRExcluir($modulo, $sub, $acao, $registro, $matriz);
				}
				break;
			case 'listar':
				servicosIVRListar($modulo, $sub, $acao, $registro, $matriz);
				break;
		}
	}
	echo "<script>location.href='#ancora';</script>";
}
	/**
	 * Função para conectar no wt_netapi e congelar/descongelar clientes
	 * Retorna 0 caso comando é executado com sucesso ou mensagem de erro caso contrario
	 * @param unknown_type $ipServidor
	 * @param unknown_type $porta
	 * @param unknown_type $comando
	 */
	function enviaComandoSocket($ipServidor, $porta, $comando){
		/* Códigos de retorno do socket:
		EXECUTADO_COM_SUCESSO = 0;
		CLIENTE_NAO_ENCONTRADO = 1;
		PARAMETROS_INCORRETOS = 2;
		FALHA_NA_EXECUCAO = 3;		
		*/
		$fp = fsockopen($ipServidor, $porta, $errno, $errstr);
		if (!$fp) {
    		$retorno = "Não foi possível conectar ao wt_netapi IP: $ipServidor Porta: $porta <br>
    		Informações adicionais: $errno - $errstr";
		} else {

    		fwrite($fp, "$comando\r");
    		stream_set_timeout($fp, 4);
    		$res = fread($fp, 200);
    		$info = stream_get_meta_data($fp);
    		fclose($fp);

    		if ($info['timed_out']) {
        		$retorno =  "Timed out ao tentar conectar ao wt_netapi IP: $ipServidor Porta: $porta";
    		} else {
        		if ($res == "0"){
        			$retorno = "0";
        		} elseif ($res == "1"){
        			$retorno = "O wt_netapi IP $ipServidor retornou que o cliente não foi encontrado, Verifique o nome.";
        		} elseif ($res == "2"){
        			$retorno = "O wt_netapi IP $ipServidor acusou parâmetros incorretos. Contacte o suporte.";
        		} elseif ($res == "3"){
        			$retorno = "O wt_netapi IP $ipServidor retornou algum problema na execução.<br>
        			Possíveis causas incluem a tentativa de congelar/descongelar um cliente já congelado/descongelado";
        		} else {
        			$retorno = "O wt_netapi IP $ipServidor retornou um código desconhecido. Contacte o suporte.";
        		}
    		}
		}
		return $retorno;
	}

function servicosIVRExcluir($modulo, $sub, $acao, $registro, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $conn, $tb;
	
	# Consulta de exclusão do registro
	$sql = "DELETE FROM $tb[ServicosIVR] WHERE $tb[ServicosIVR].id = ".$matriz[idServicoIVR];
	
	$excluir = consultaSQL($sql, $conn);
	
	if($excluir){
		$msg="Serviço IVR excluído com sucesso!!";
		avisoNOURL("Aviso: Exclusão", $msg, 400);
		echo "<br>";
	}
	
	servicosIVRVer($modulo, $sub, 'ver', $registro, $matriz);
}

#procurar
function servicosIVRProcurar($modulo, $sub, $acao, $registro, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessCadastro, $titulo, $tb;
	
	if( !$matriz[bntProcurar] ) {

		novaTabela2("[Procurar $titulo]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			
			novaLinhaTabela($corFundo, '100%');
			$texto="<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro>";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
			
			#nome
			getCampo('text', "Host", 'matriz[nome]');
			
			#botao
			getBotao('matriz[bntProcurar]', 'Procurar');
			
		fechaTabela();
	} else {
		# realizar consulta
		$lista=buscaRegistros($matriz[nome], 'nome', 'contem', 'nome', $tb[ServicosIVR]);
		servicosIVRListar($modulo, $sub, 'ver', $lista, $matriz);
	}
}


# função para adicionar
function servicosIVRAdicionar($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro;
	
	if(!$matriz[bntAdicionar]) {
		$dados=dadosservicosIVR(0);
		# Motrar tabela de busca
		novaTabela2("[Adicionar]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			#menuOpcAdicional($modulo, $sub, $acao, $registro);
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=subacao value=adicionar>
				<input type=hidden name=registro value=$registro>
				<input type=hidden name=matriz[id1] value=''>
				<input type=hidden name=matriz[id2] value=$matriz[idServicoIVR]>
				<input type=hidden name=matriz[id3] value=$matriz[idServicoPlano]>
				<input type=hidden name=matriz[status] value='A'>
				&nbsp;";
				
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			
			$combo="<select name=matriz[idServicoPlano]>";
			foreach ($matriz[ServicosPlanos] as $sp) {
				#echo "<br>Procurando $sp";
				$servico=dadosServicoPlano($sp);
				$nomeServico=checkServico($servico[idServico]);
				$combo.="\n<option value=$sp>".$nomeServico[nome];
			}
			$combo.="</select>";
			getCampo("combo", "Serviço","matriz[idServicoPlano]", $combo);
			servicosIVRMostra($dados);
			
			#botao
			getBotao('matriz[bntAdicionar]', 'Adicionar');
						
		fechaTabela();	
	}
	else {
		
		$grava=dbservicosIVR($matriz, 'incluir');
		
		# Verificar inclusão de registro
		echo "<br>";
		if($grava) {
			# Visualizar
			$msg="Registro gravado com sucesso!";
			avisoNOURL("Aviso: ", $msg, '100%');
			echo "<br>";
			$matriz[naoInclui]=1;
			servicosIVRListar($modulo, $sub, $acao, $registro, $matriz, 'ver');

		}
		else {
			$msg="Ocorreram erros durante a gravação.";
			avisoNOURL("Aviso: Ocorrência de erro", $msg, '60%');
		}
	}
}



# Função para alteração de dados da Pessoa - apenas cadastro
function servicosIVRAlterar($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $tb, $sessCadastro;
	
	if($registro && !$matriz[bntConfirmar]) {
	
		# Buscar Valores
		if ($acao=='config' && $matriz[idServicoIVR])
			$dados=dadosservicosIVR($matriz[idServicoIVR], 'id', 'igual', 'id');
		else
			$dados=dadosservicosIVR($registro, 'id', 'igual', 'id');
			
		# Motrar tabela de busca
		novaTabela2("[Alterar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			
			# Opcoes Adicionais
			menuOpcAdicional('servicoIVR', $sub, 'ver', $registro, $matriz);
				
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=registro value=$registro>
				<input type=hidden name=matriz[id] value=$matriz[idServicoIVR]>
				<input type=hidden name=matriz[idServicoIVR] value=$matriz[idServicoIVR]>
				<input type=hidden name=matriz[idServicoPlano] value=$matriz[idServicoPlano]>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=matriz[status] value=$dados[status]>
				<input type=hidden name=subacao value=alterar>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			#echo "[$matriz[idServicoIVR]/$matriz[idServicoPlano]";
			
			servicosIVRMostra($dados);
			
			#botao
			getBotao('matriz[bntConfirmar]', 'Alterar');
			
		fechaTabela();
	} #fecha form
	
	# Alteração - bntAlterar pressionado
	elseif($matriz[bntConfirmar]) {
		# Conferir campos
		#echo "[$matriz[idServicoIVR]/$matriz[idServicoPlano]";
		if ($matriz[idServicoIVR]) $matriz[id]=$matriz[idServicoIVR];
		
		if($matriz[id]) {
			# continuar
			# Cadastrar em banco de dados
			$grava=dbservicosIVR($matriz, 'alterar');
			
			# Verificar inclusão de registro
			if($grava) {
				# OK
				# Visualizar
				$msg="Registro alterado com sucesso!";
				avisoNOURL("Aviso: ", $msg, '100%');
				echo "<br>";
				$matriz[idServicoIVR]=$matriz[id];
				servicosIVRVer($modulo, $sub, $acao, $registro, $matriz);
			} else {
				echo "<br>";
				$msg="Ocorreram erros durante a gravação.";
				avisoNOURL("Aviso: Ocorrência de erro", $msg, '100%');
			}
		}
		# falta de parametros
		else {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Falta de parâmetros necessários. Informe os campos obrigatórios e tente novamente";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
		}
	} #fecha bntAlterar
}

function servicosIVRCongelar($modulo, $sub, $acao, $registro, $matriz) {
	global $conn, $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $tb, $sessCadastro;
	if($registro && !$matriz[bntConfirmar]) {
		novaTabela2("[Inativar Conexão]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			
			# Opcoes Adicionais
			menuOpcAdicional('servicoIVR', $sub, 'ver', $registro, $matriz);
				
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=registro value=$registro>
				<input type=hidden name=matriz[id] value=$matriz[idServicoIVR]>
				<input type=hidden name=matriz[idServicoIVR] value=$matriz[idServicoIVR]>
				<input type=hidden name=matriz[idServicoPlano] value=$matriz[idServicoPlano]>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=subacao value=congelar>&nbsp;";
			
				$texto.= "O cliente será Inativado no WT. Confirma Inativação? <br>";
				itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo9b');
			fechaLinhaTabela();
			
			#echo "[$matriz[idServicoIVR]/$matriz[idServicoPlano]";
			

			
			#botao
			getBotao('matriz[bntConfirmar]', 'Inativar');
			
		fechaTabela();
	}elseif($matriz[bntConfirmar]) {
		$sql = "select s.ip, ivr.nome from Servidores s 
		inner join Interfaces i on (s.id = i.idServidor) 
		inner join Bases b on (i.id = b.idIfaceServidor) 
		inner join ServicoIVR ivr on (b.id = ivr.idBase) 
		where ivr.id = $matriz[idServicoIVR];";
		
		$consulta = consultaSQL($sql, $conn);
		if ($consulta && contaConsulta($consulta) > 0){
			$ipServidor = resultadoSQL($consulta, 0, 'ip');
			$hostWt = resultadoSQL($consulta, 0, 'nome');
			$erro = enviaComandoSocket($ipServidor,"7069","congelar $hostWt");
			if ($erro){
				echo "<br>";
				avisoNOURL("Aviso: Ocorrência de erro", $erro, '100%');
			} else {
				// altera status no banco:
				$sql = "update $tb[ServicosIVR] set status = 'I' where id = $matriz[idServicoIVR]";
				consultaSQL($sql, $conn);
				$msg="Registro alterado com sucesso!";
				avisoNOURL("Aviso: ", $msg, '100%');
			}
		}
		
	}
}

function servicosIVRDescongelar($modulo, $sub, $acao, $registro, $matriz) {
	global $conn, $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $tb, $sessCadastro;
	if($registro && !$matriz[bntConfirmar]) {
		novaTabela2("[Ativar Conexão]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			
			# Opcoes Adicionais
			menuOpcAdicional('servicoIVR', $sub, 'ver', $registro, $matriz);
				
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=registro value=$registro>
				<input type=hidden name=matriz[id] value=$matriz[idServicoIVR]>
				<input type=hidden name=matriz[idServicoIVR] value=$matriz[idServicoIVR]>
				<input type=hidden name=matriz[idServicoPlano] value=$matriz[idServicoPlano]>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=subacao value=descongelar>&nbsp;";
			
				$texto.= "O cliente será Ativado no WT. Confirma ativação? <br>";
				itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo9b');
			fechaLinhaTabela();
			#botao
			getBotao('matriz[bntConfirmar]', 'Ativar');
			
		fechaTabela();
	}elseif($matriz[bntConfirmar]) {
		$sql = "select s.ip, ivr.nome from Servidores s 
		inner join Interfaces i on (s.id = i.idServidor) 
		inner join Bases b on (i.id = b.idIfaceServidor) 
		inner join ServicoIVR ivr on (b.id = ivr.idBase) 
		where ivr.id = $matriz[idServicoIVR];";
		
		$consulta = consultaSQL($sql, $conn);
		if ($consulta && contaConsulta($consulta) > 0){
			$ipServidor = resultadoSQL($consulta, 0, 'ip');
			$hostWt = resultadoSQL($consulta, 0, 'nome');
			$erro = enviaComandoSocket($ipServidor,"7069","descongelar $hostWt");
			if ($erro){
				echo "<br>";
				avisoNOURL("Aviso: Ocorrência de erro", $erro, '100%');
			} else {
				// altera status no banco:
				$sql = "update $tb[ServicosIVR] set status = 'A' where id = $matriz[idServicoIVR]";
				consultaSQL($sql, $conn);
				$msg="Registro alterado com sucesso!";
				avisoNOURL("Aviso: ", $msg, '100%');
			}
		}
		
	}
}

function servicosIVRMostra($dados="") {
	global $conn, $tb;
	
	$tamanho=30;
	$comboBase=getSelectDados($dados[idBase], "", "matriz[idBase]", 'formnochange',  $tb[Bases], 'nome');
	
	getCampo('text',   'Host',       'matriz[nome]',   $dados[nome],   "", "", $tamanho);
	getCampo('combo',  'Base',       'matriz[idBase]', $comboBase);
	getCampo('text',   'IP',         'matriz[ip]',     $dados[ip],     "onBlur='validaIp(this)'", "", $tamanho);
	getCampo('text',   'Máscara',    'matriz[mask]',   $dados[mask],   "onBlur='validaIpMask(this)'", "", $tamanho);
	getCampo('text',   'Gateway',    'matriz[gw]',     $dados[gw],     "onBlur='validaIp(this)'", "", $tamanho);
	getCampo('text',   'Mac Address', 'matriz[mac]',   $dados[mac],    "", "", $tamanho);
	getCampo('text',   'DNS 1',      'matriz[dns1]',   $dados[dns1],   "onBlur='validaIp(this)'", "", $tamanho);
	getCampo('text',   'DNS 2',      'matriz[dns2]',   $dados[dns2],   "onBlur='validaIp(this)'", "", $tamanho);
	getCampo('text',   'S.O.',       'matriz[so]',     $dados[so],     "", "", $tamanho);
	//getCampo('status', 'Status',     'matriz[status]', $dados[status]);
	getCampo('area',   'Observação', 'matriz[obs]',    $dados[obs],    "", "", $tamanho);
	//onkeypress='return validaTecla(this, event)'
}


# Função para buscar o NOVO ID da Pessoa
function servicosIVRBuscaIDNovo() {

	global $conn, $tb;
	
	$sql="SELECT count(id) qtde from $tb[ServicosIVR]";
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && resultadoSQL($consulta, 0, 'qtde')>0) {
	
		$sql="SELECT MAX(id)+1 id from $tb[ServicosIVR]";
		$consulta=consultaSQL($sql, $conn);
		
		if($consulta && contaConsulta($consulta)>0) {
			$retorno=resultadoSQL($consulta, 0, 'id');
			if(!is_numeric($retorno)) $retorno=1;
		}
		else $retorno=1;
	}
	else {
		$retorno=resultadoSQL($consulta, 0, 'qtde')+1;
	}
	return($retorno);
}


#Lista todas OS do cliente selecionado
function servicosIVRListar($modulo, $sub, $acao, $registro, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb, $conn, $titulo;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		exibeSemPermissao($modulo, $acao);
	}
	else {
		
		# Cabeçalho
		novaTabela("Lista de Bases", "left", '100%', 0, 4, 1, $corFundo, $corBorda, 4);
		
			novaLinhaTabela($corFundo, '100%');
				# Opcoes Adicionais
				if (! $matriz[naoInclui]) 
					menuOpcAdicional('servicoIVR', $sub, 'adicionar', $registro, $matriz, 4);
				else 
					"nbsp;";
			fechaLinhaTabela();
				
			#monta uma lista de servicosPlanos para o sql
			if (is_array($matriz[todosServicosPlanos])) {
				$in="idServicoPlano in (".implode(",", $matriz[todosServicosPlanos]).")";
				#consulta as configuracoes que tiverem na lista de ServicoPlanos
				// Alteração na consulta com  a adição do campo Servidores.nome - por Felipe Assis - 24/03/2008
				$sql = "SELECT $tb[ServicosIVR].*, $tb[Servidores].nome as servidor 
						FROM $tb[ServicosIVR] INNER JOIN $tb[Bases] 
						ON ($tb[ServicosIVR].idBase = $tb[Bases].id) 
						INNER JOIN $tb[Interfaces] 
						ON ($tb[Bases].idIFaceServidor = $tb[Interfaces].id) 
						INNER JOIN $tb[Servidores]
						ON ($tb[Interfaces].idServidor = $tb[Servidores].id) 
						WHERE $tb[idServicoPlano] $in 
						GROUP BY $tb[ServicosIVR].id ORDER BY $tb[ServicosIVR].nome";
//				echo $sql."<br>";
				$consulta = consultaSQL($sql, $conn);
//				$consulta=buscaRegistros($in, 'idServicoPlano',  'custom', 'nome', $tb[ServicosIVR]);
			} 
			else{ 
				
				$sql = "SELECT $tb[ServicosIVR].*, $tb[Servidores].nome as servidor 
						FROM $tb[ServicoIVR] 
						ON ($tb[ServicoIVR].idBase = $tb[Bases].id) 
						INNER JOIN $tb[Interfaces] 
						ON ($tb[Bases].idIFaceServidor = $tb[Interfaces].id) 
						INNER JOIN $tb[Servidores] 
						ON ($tb[Interfaces].idServidor = $tb[Servidores].id) 
						WHERE $tb[ServicosIVR].idServicoPlano = $matriz[idServicoPlano] ORDER BY id";
				$consulta = consultaSQL($sql, $conn);
//				$consulta=buscaRegistros($matriz[idServicoPlano], 'idServicoPlano',  'igual', 'nome', $tb[ServicosIVR]);
			}
			// Alteração na consulta com  a adição do campo Servidores.nome - por Felipe Assis - 24/03/2008
			if ($consulta && contaConsulta($consulta)>0) {
				$largura             =array('25%',  '15%', 		'10%',		'2%');
				$gravata[cabecalho]  =array('Nome', 'Servidor',	'Status',  	'Opções');
				$gravata[alinhamento]=array('left', 'left',		'center',  	'left');
				htmlAbreLinha($corFundo);
					for($i=0;$i<count($largura); $i++)
						itemLinhaTMNOURL($gravata[cabecalho][$i], $gravata[alinhamento][$i], 'middle', $largura[$i], $corFundo, 0, 'tabfundo0');
				htmlFechaLinha();
				
				$qtd=contaConsulta($consulta);
				for($reg=0;$reg<$qtd;$reg++) {
					
					$id=resultadoSQL($consulta, $reg, 'id');
					$st=resultadoSQL($consulta, $reg, 'status');
					$sp=resultadoSQL($consulta, $reg, 'idServicoPlano');
	
					#opcoes
					$def="<a href=?modulo=$modulo&sub=$sub&registro=$registro&idServicoPlano=$sp&acao=config";
					$fnt="<font size='2'>";
					
					$opcoes =htmlMontaOpcao($def."&subacao=ver>".$fnt."Ver</font></a>",'ver');
					$opcoes.=htmlMontaOpcao($def."&subacao=alterar>".$fnt."Alterar</font></a>",'alterar');
					$opcoes.=htmlMontaOpcao($def."&subacao=excluir>".$fnt."Excluir</font></a>",'alterar');
					if ($st == "A"){
						$opcoes.=htmlMontaOpcao($def."&subacao=congelar>".$fnt."Inativar Conexão</font></a>",'desativar');
					} elseif ($st == "I"){
						$opcoes.=htmlMontaOpcao($def."&subacao=descongelar>".$fnt."Ativar Conexão</font></a>",'ativar');
					}
					
					$i=0;
					$campo[$i++]=resultadoSQL($consulta, $reg, 'nome');
					$campo[$i++]=resultadoSQL($consulta, $reg, 'servidor');
					$campo[$i++]=getComboStatus($st, "", "check");
					$campo[$i++]=$opcoes;
					
					htmlAbreLinha($corFundo);
						for($i=0;$i<count($campo); $i++)
							itemLinhaTMNOURL($campo[$i], $gravata[alinhamento][$i].' normal10', 'middle', $largura[$i], $corFundo, 0, 'normal10');
					htmlFechaLinha();
				}
			}
			else {
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL( "Não há registros para sua solicitação.", 'left', 'middle', '100%', $corFundo, 4, 'normal10');
				fechaLinhaTabela();
			}
		fechaTabela();
	}
	
}

#busca

# função Exibição
function servicosIVRVer($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb, $titulo;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		exibeSemPermissao($modulo, $acao);
	}
	else {
		# Procurar dados3
		if ($acao=='config' && $matriz[idServicoIVR])
			$objeto=dadosservicosIVR($matriz[idServicoIVR], true);
		else
			$objeto=dadosservicosIVR($registro);
		
		if(is_array($objeto)) {
			# Motrar tabela de busca
			# Adição de informações adicionais (Servidor, base e Interface) vinculados ao Serviço IVR visualizado
			# por Felipe Assis - 24/03/2008
			novaTabela2("[Servicos IVR - Visualização]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				
				# Opcoes Adicionais
				menuOpcAdicional('servicoIVR', $sub, 'alterar', $registro, $matriz);
				
				#itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
				getCampo('', 'Host', 'matriz[nome]', $objeto[nome]);
				# Campos Adicionais (Servidor e Interface e Nome de Interface) - por Felipe Assis - 24/03/2008
				if ($acao=='config' && $matriz[idServicoIVR]){
					getCampo('','Servidor','matriz[servidor]',$objeto[servidor]);
					getCampo('', 'Interface', 'matriz[nomeInterface]', $objeto[iface]." : ".$objeto[nomeInterface]);
				}
				getCampo('', 'Base', 'matriz[idBase]', $objeto[base][nome]);
				getCampo('', 'IP', 'matriz[ip]', $objeto[ip]);			
				getCampo('', 'Máscara', 'matriz[mask]', $objeto[mask]);
				getCampo('', 'Gateway', 'matriz[gw]', $objeto[gw]);
				getCampo('', 'Mac Address', 'matriz[mac]', $objeto[mac]);
				getCampo('', 'DNS 1', 'matriz[dns1]', $objeto[dns1]);
				getCampo('', 'DNS 2', 'matriz[dns2]', $objeto[dns2]);
				getCampo('', 'S.O.', 'matriz[so]', $objeto[so]);
				getCampo('', 'Status', 'matriz[status]', getComboStatus($objeto[status], "", 'check'));
				getCampo('', 'Observações', 'matriz[obs]', $objeto[obs]);
				
				
			fechaTabela();
		}
	}
}


# Função para Dados
/**
 * @return array
 * @param int $id
 * @param bool $adicionais
 * @desc Retorna um array com os dados
 extra
 dados[base] = dados de base
*/
function dadosservicosIVR($id, $adicionais = false) {

	global $tb, $conn;

	$consulta=buscaRegistros($id, 'id', 'igual', 'id', $tb[ServicosIVR]);
	
	if($adicionais == true){
		$sql = "SELECT $tb[Servidores].nome as servidor, $tb[Interfaces].nome as nomeInterface, $tb[Interfaces].iface as iface
				FROM $tb[ServicosIVR] 
				INNER JOIN $tb[Bases] 
				ON ($tb[ServicosIVR].idBase = $tb[Bases].id) 
				INNER JOIN $tb[Interfaces] 
				ON ($tb[Bases].idIFaceServidor = $tb[Interfaces].id) 
				INNER JOIN $tb[Servidores] 
				ON ($tb[Interfaces].idServidor = $tb[Servidores].id) 
				WHERE ServicoIVR.id = ".$id ;
		$conAdicionais = consultaSQL($sql, $conn);
		
		if(contaConsulta($conAdicionais) > 0){
			$retorno[servidor] = resultadoSQL($conAdicionais, 0, 'servidor');
			$retorno[nomeInterface] = resultadoSQL($conAdicionais, 0, 'nomeInterface');
			$retorno[iface] = resultadoSQL($conAdicionais, 0, 'iface');
		}
	}
	
	if( contaConsulta($consulta)>0 ) {
		$retorno[id]=resultadoSQL($consulta, 0, 'id');
		$retorno[idBase]=resultadoSQL($consulta, 0, 'idBase');
		$retorno[nome]=resultadoSQL($consulta, 0, 'nome');
		$retorno[ip]=resultadoSQL($consulta, 0, 'ip');
		$retorno[mask]=resultadoSQL($consulta, 0, 'mask');
		$retorno[gw]=resultadoSQL($consulta, 0, 'gw');
		$retorno[mac]=resultadoSQL($consulta, 0, 'mac');
		$retorno[dns1]=resultadoSQL($consulta, 0, 'dns1');
		$retorno[dns2]=resultadoSQL($consulta, 0, 'dns2');
		$retorno[so]=resultadoSQL($consulta, 0, 'so');
		$retorno[status]=resultadoSQL($consulta, 0, 'status');
		$retorno[obs]=resultadoSQL($consulta, 0, 'obs');
		
		if ($retorno[idBase])
			$retorno[base]=dadosbase($retorno[idBase]);
	}
	
	return($retorno);
	
}

/**
 * @return unknown
 * @param unknown $item
 * @param unknown $campo
 * @param unknown $retorno
 * @param unknown $tipo
 * @desc Retorna 1 ou varios itens do objeto.
 item  = id 
 campo = campo da matriz
 retorno = campo de retorno (nome do componente)
 tipo = check - retorna o nome
        form  - um combo com os nomes
        multi - uma caixa de selecao com os nomes
*/
function formSelectservicosIVR($item, $campo, $retorno, $tipo) {
	
	global $conn, $tb, $corFundo, $modulo, $sub;

	$tabela=$tb[ServicosIVR];
	
	if($tipo=='check') {
		$campo=dadosservicosIVR($item);
		$retorno=$campo[nome];
	} else {
		$retorno = getSelecDados($item, $campo, $retorno, $tipo, $tabela);
	}
	return($retorno);
}


#Função de banco de dados
/**
 * @return unknown
 * @param unknown $matriz
 * @param unknown $tipo
 * @desc Tratamento da sentenca sql
*/
function dbservicosIVR($matriz, $tipo) {

	global $sessLogin, $sessCadastro, $conn, $tb, $modulo, $sub, $acao;
	
	$tabela=$tb[ServicosIVR];

	
	# Sql de inclusão
	if($tipo=='incluir') {
		
		$sql="INSERT INTO $tabela 
					 (idServicoPlano, idBase, nome, ip, mask, mac, gw,
					  dns1, dns2, so, status, obs, dtCadastro
					 )
		      VALUES ('$matriz[idServicoPlano]',
					  '$matriz[idBase]',
					  '$matriz[nome]',
					  '$matriz[ip]',
					  '$matriz[mask]',
					  '$matriz[mac]',
					  '$matriz[gw]',
					  '$matriz[dns1]',
					  '$matriz[dns2]',
					  '$matriz[so]',
					  '$matriz[status]',
					  '$matriz[obs]',
					  now()
					   )";
	} #fecha inclusao
	
	# Alterar
	elseif($tipo=='alterar') {
		/* Cria uma matriz com os campos ja formatados para o SQL */
		$campos[id]="id=$matriz[id]";
		$campos[idServicoPlano]="idServicoPlano='$matriz[idServicoPlano]'";
		$campos[idBase]="idBase='".$matriz[idBase]."'";
		$campos[nome]="nome='".$matriz[nome]."'";
		$campos[ip]="ip='".$matriz[ip]."'";
		$campos[mask]="mask='".$matriz[mask]."'";
		$campos[mac]="mac='".$matriz[mac]."'";
		$campos[gw]="gw='".$matriz[gw]."'";
		$campos[dns1]="dns1='".$matriz[dns1]."'";
		$campos[dns2]="dns2='".$matriz[dns2]."'";
		$campos[so]="so='".$matriz[so]."'";
		$campos[status]="status='".$matriz[status]."'";
		$campos[obs]="obs='".$matriz[obs]."'";
		
		$sql="UPDATE $tabela SET
					$campos[idServicoPlano],
					$campos[idBase],
					$campos[nome],
					$campos[ip],
					$campos[mask],
					$campos[mac],
					$campos[gw],
					$campos[dns1],
					$campos[dns2],
					$campos[so],
					$campos[status],
					$campos[obs]
				WHERE
					$campos[id]";
	}
	
	elseif($tipo=='excluir') {
		$sql="DELETE FROM $tabela WHERE $campos[id]";
	}
	
	if($sql) { 
		#echo $sql;
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
}


function getIDServicoIVR($idServicoPlano) {
	
	global $sessLogin, $sessCadastro, $conn, $tb, $modulo, $sub, $acao;
	

	$rs=buscaRegistros($idServicoPlano, "idServicoPlano", 'igual', 'id', $tb[ServicosIVR]);

	if ($rs && contaConsulta($rs)>0) 
		$ret=resultadoSQL($rs, 0, "id");
	
	return $ret;
}

function procurarServicosIVR($modulo, $sub, $acao, $registro, $matriz){
	global $conn, $tb, $corFundo, $corBorda, $html, $limite, $sessCadastro;
	
	novaTabela2("[Procurar IPs]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 0);
		novaLinhaTabela($corFundo, '100%');
			$texto="
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=procurar>
			<b>Procurar por:</b>&nbsp;<input type=text name=matriz[txtProcurarIP] size=40>";
			
			$texto.="&nbsp;<input type=submit name=matriz[bntProcurarIP] value=Procurar class=submit>";
			itemLinhaForm($texto, 'center','middle', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
	fechaTabela();
	
	# Caso botão procurar seja pressionado
	if($matriz['bntProcurarIP']) {
	
		#buscar registros
		$sql="SELECT 
			$tb[ServicosIVR].ip, 
			$tb[ServicosIVR].nome as host, 
			$tb[Pessoas].nome, 
			$tb[PessoasTipos].id
  			  FROM
				$tb[ServicosIVR]
			  INNER JOIN $tb[ServicosPlanos] 
   				ON $tb[ServicosIVR].idServicoPlano = $tb[ServicosPlanos].id 
              INNER JOIN $tb[PlanosPessoas] 
   				ON $tb[ServicosPlanos].idPlano = $tb[PlanosPessoas].id
   			  INNER JOIN $tb[PessoasTipos]
   				ON $tb[PlanosPessoas].idPessoaTipo = $tb[PessoasTipos].id
   			  INNER JOIN $tb[Pessoas]
   				ON $tb[PessoasTipos].idPessoa = $tb[Pessoas].id
			  WHERE
				$tb[ServicosIVR].ip like '%$matriz[txtProcurarIP]%'
			  OR 
			    $tb[ServicosIVR].nome like '%$matriz[txtProcurarIP]%'";

				
		$consulta=consultaSQL($sql, $conn);

		echo "<br>";
		novaTabela("[Resultados]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
	
		if(!$consulta || contaConsulta($consulta)==0 ) {
			# Não há registros
			itemTabelaNOURL('Não foram encontrados IPs cadastrados.', 'left', $corFundo, 3, 'txtaviso');
		}
		elseif($consulta && contaConsulta($consulta)>0 && (!$registro || is_numeric($registro)) ) {	
		
			itemTabelaNOURL('IPs encontrados procurando por ('.$matriz['txtProcurarIP'].'): '.contaConsulta($consulta).' registro(s)', 'left', $corFundo, 4, 'txtaviso');

			# Paginador
			$urlADD="&matriz[txtProcurarIP]=".$matriz['txtProcurarIP'];
			paginador($consulta, contaConsulta($consulta), $limite['lista']['pessoas'], $registro, 'normal', 3, $urlADD);

			# Cabeçalho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('IP', 'center', '15%', 'tabfundo0');
				itemLinhaTabela('Host', 'center', '20%', 'tabfundo0');
				itemLinhaTabela('Nome', 'center', '35%', 'tabfundo0');
				itemLinhaTabela('Opções', 'center', '30%', 'tabfundo0');
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

			$limite=$i+$limite['lista']['pessoas'];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$idPessoaTipo=resultadoSQL($consulta, $i, 'id');
				$ip=resultadoSQL($consulta, $i, 'ip');
				$host=resultadoSQL($consulta, $i, 'host');
				$nomePessoa=resultadoSQL($consulta, $i, 'nome');
							
				$opcoes=htmlMontaOpcao("<a href=?modulo=cadastros&sub=clientes&acao=ver&registro=$idPessoaTipo>Cadastro</a>",'ver');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=lancamentos&sub=planos&acao=listar&registro=$idPessoaTipo>Planos</a>",'planos');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=faturamento&sub=clientes&acao=historico&registro=0&matriz[idPessoaTipo]=$idPessoaTipo>Financeiro</a>",'lancamento');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=administracao&sub=limites&registro=$idPessoaTipo>Administração</a>",'config');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela("$ip", 'center', '15%', 'normal10');
					itemLinhaTabela("$host", 'left', '20%', 'normal10');
					itemLinhaTabela("$nomePessoa", 'left nowrap', '35%', 'normal10');
					itemLinhaTabela($opcoes, 'left', '30%', 'normal8');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem
		
		# Zerar pesquisa
		$sessCadastro['txtProcurarIP']='';
		$sessCadastro['bntProcurarIP']=0;
		fechaTabela();
	} # fecha botão procurar
	
}

function formConfirmExcServicoIVR($modulo, $sub, $acao, $registro, $matriz){
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $conn, $tb;
	
	# consultando dados da Interface selecionada
	$sql= "SELECT 
			$tb[Servidores].nome as servidor, 
			$tb[Bases].nome as base, 
			$tb[ServicosIVR].nome, 
			$tb[ServicosIVR].id, 
			$tb[ServicosIVR].mask, 
			$tb[ServicosIVR].mac, 
			$tb[ServicosIVR].gw, 
			$tb[ServicosIVR].dns1, 
			$tb[ServicosIVR].dns2, 
			$tb[ServicosIVR].status, 
			$tb[ServicosIVR].so, 
			$tb[ServicosIVR].obs, 
			$tb[ServicosIVR].dtCadastro 
			FROM $tb[ServicosIVR] 
			INNER JOIN $tb[Bases] ON ($tb[ServicosIVR].idBase = $tb[Bases].id) 
			INNER JOIN $tb[Interfaces] ON ($tb[Bases].idIFaceServidor = $tb[Interfaces].id) 
			INNER JOIN $tb[Servidores] ON ($tb[Interfaces].idServidor = $tb[Servidores].id) 
			WHERE $tb[ServicosIVR].id = " . $matriz[idServicoIVR];
	
	$consulta = consultaSQL($sql, $conn);
	
	//Obtendo resultado
	$servidor = resultadoSQL($consulta, 0, 'servidor');
	$base = resultadoSQL($consulta, 0, 'base');
	$nome = resultadoSQL($consulta, 0, 'nome');
	$mask = resultadoSQL($consulta, 0, 'mask');
	$mac = resultadoSQL($consulta, 0, 'mac');
	$gw = resultadoSQL($consulta, 0, 'gw');
	$dns1 = resultadoSQL($consulta, 0, 'dns1');
	$dns2 = resultadoSQL($consulta, 0, 'dns2');
	$status = resultadoSQL($consulta, 0, 'status');
	$so = resultadoSQL($consulta, 0, 'so');
	$obs = resultadoSQL($consulta, 0, 'obs');
	$dtCadastro = resultadoSQL($consulta, 0, 4);
	
	novaTabela2("[Excluir]", "center", "100%", 0, 2, 1, $corFundo, $corBorda, 2);
		novaLinhaTabela($corFundo, '100%');
			$texto ="
					<form method='post' name='matriz' action='index.php'>
					<input type='hidden' name='modulo' value='$modulo'>
					<input type='hidden' name='sub' value='$sub'>
					<input type='hidden' name='acao' value='$acao'>
					<input type='hidden' name='subacao' value='excluir'>
					<input type='hidden' name='matriz[idServicoIVR]' value='$matriz[idServicoIVR]'>
					<input type='hidden' name='registro' value='$registro'>";
			itemLinhaNOURL($texto, 'center', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('50%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b>Servidor: </b>";
			htmlFechaColuna();
			htmlAbreColuna('50%', 'left', $corFundo, 0, 'tabfundo1');
				echo "<b>$servidor</b>";
			htmlFechaColuna();
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('50%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b>Base: </b>";
			htmlFechaColuna();
			htmlAbreColuna('50%', 'left', $corFundo, 0, 'tabfundo1');
				echo "<b>$base</b>";
			htmlFechaColuna();
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('50%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b>Nome: </b>";
			htmlFechaColuna();
			htmlAbreColuna('50%', 'left', $corFundo, 0, 'tabfundo1');
				echo "<b>$nome</b>";
			htmlFechaColuna();
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('50%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b>Máscara: </b>";
			htmlFechaColuna();
			htmlAbreColuna('50%', 'left', $corFundo, 0, 'tabfundo1');
				echo "<b>$mask</b>";
			htmlFechaColuna();
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('50%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b>Mac Address: </b>";
			htmlFechaColuna();
			htmlAbreColuna('50%', 'left', $corFundo, 0, 'tabfundo1');
				echo "<b>$mac</b>";
			htmlFechaColuna();
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('50%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b>Gateway: </b>";
			htmlFechaColuna();
			htmlAbreColuna('50%', 'left', $corFundo, 0, 'tabfundo1');
				echo "<b>$gw</b>";
			htmlFechaColuna();
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('50%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b>DNS 1: </b>";
			htmlFechaColuna();
			htmlAbreColuna('50%', 'left', $corFundo, 0, 'tabfundo1');
				echo "<b>$dns1</b>";
			htmlFechaColuna();
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('50%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b>DNS 2: </b>";
			htmlFechaColuna();
			htmlAbreColuna('50%', 'left', $corFundo, 0, 'tabfundo1');
				echo "<b>$dns2</b>";
			htmlFechaColuna();
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('50%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b>Status: </b>";
			htmlFechaColuna();
			$status = ($status == 'A' ? "<span class=\"txtok\">Ativo</span>" : "<span class=\"txtaviso\">Inativo</span>");
			htmlAbreColuna('50%', 'left', $corFundo, 0, 'tabfundo1');
				echo "<b>$status</b>";
			htmlFechaColuna();
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('50%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b>Sistema Operacional: </b>";
			htmlFechaColuna();
			htmlAbreColuna('50%', 'left', $corFundo, 0, 'tabfundo1');
				echo "<b>$so</b>";
			htmlFechaColuna();
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('50%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b>Observações: </b>";
			htmlFechaColuna();
			htmlAbreColuna('50%', 'left', $corFundo, 0, 'tabfundo1');
				echo "<b>$obs</b>";
			htmlFechaColuna();
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('50%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b>Data de Cadastro: </b>";
			htmlFechaColuna();
			htmlAbreColuna('50%', 'left', $corFundo, 0, 'tabfundo1');
				echo "<b>$dtCadastro</b>";
			htmlFechaColuna();
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, 100);
			$texto = "<input type='submit' name='matriz[btExcluir]' value='Excluir' class='submit'>";
			itemLinhaNOURL($texto, "center", $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
	fechaTabela();
	echo "<br>";
}

/**
 * Seleciona os Servicos IVR de determinado cliente
 * 
 * @author João Petrelli
 * @since 09-02-2009
 *
 * @param int $idPessoaTipo
 * @return array
 */
function selecionaServicosIVR($idPessoaTipo) {
	global $conn;
	
	$sql = 			"SELECT 
						ServicoIVR.status,
						ServicoIVR.id, 
						ServicoIVR.idServicoPlano
					FROM
						PlanosPessoas, ServicosPlanos, ServicoIVR 
					WHERE 
						PlanosPessoas.idPessoaTipo = '$idPessoaTipo' 
						AND ServicosPlanos.idPlano = PlanosPessoas.id 
						AND ServicosPlanos.id = ServicoIVR.idServicoPlano";
	return consultaSQL($sql, $conn);
}

/**
 * 	Pesquisa nas duas consultas se existe o Servico
 * de IVR ativado para determinado cliente.
 *
 * @author João Petrelli
 * @since 09-02-2009
 * 
 * @param array $consultaServicos
 * @param array $consultaServicosIVR
 * @return array
 */
function isServicoIVR($consultaServicos, $consultaServicosIVR) {
	$retorno = false;
	$k = 0;
	if (contaConsulta($consultaServicos) > contaConsulta($consultaServicosIVR)) {
		for ($i = 0; $i < contaConsulta($consultaServicos); $i++) {
			for ($j = 0; $j < contaConsulta($consultaServicosIVR); $j++) {
				if (resultadoSQL($consultaServicos, $i, 'id') == resultadoSQL($consultaServicosIVR, $j, 'idServicoPlano')) {
					$retorno[$k]['id'] = resultadoSQL($consultaServicos, $i, 'id');
					$retorno[$k]['status'] = resultadoSQL($consultaServicosIVR, $j, 'status');
					$k++;
				}
			}
		}
	} else {
		for ($i = 0; $i < contaConsulta($consultaServicosIVR); $i++) {
			for ($j = 0; $i < contaConsulta($consultaServicos); $j++) {
				if (resultadoSQL($consultaServicos, $j, 'id') == resultadoSQL($consultaServicosIVR, $i, 'idServicoPlano')) {
					$retorno[$k]['id'] = resultadoSQL($consultaServicos, $j, 'id');
					$retorno[$k]['status'] = resultadoSQL($consultaServicosIVR, $j, 'status');
					$k++;
				}
			}
		}
	}
	return $retorno;
}

/**
 * Inativa um Servico de IVR de um cliente no WT de acordo com
 * o idServicoIVR.
 * 
 * @author João Petrelli
 * @since 09-02-2009
 * 
 * @param array $matriz
 * @return String
 */
function inativarClienteWT($matriz) {
	global $conn;
	
	$sql = "select s.ip, ivr.nome from Servidores s
		inner join Interfaces i on (s.id = i.idServidor) 
		inner join Bases b on (i.id = b.idIfaceServidor) 
		inner join ServicoIVR ivr on (b.id = ivr.idBase) 
		where ivr.id = $matriz[idServicoIVR];";

	$consulta = consultaSQL($sql, $conn);
	if ($consulta && contaConsulta($consulta) > 0){
		$ipServidor = resultadoSQL($consulta, 0, 'ip');
		$hostWt = resultadoSQL($consulta, 0, 'nome');
		$retorno = enviaComandoSocket($ipServidor,"7069","congelar $hostWt");
		if ($retorno == "" || $retorno != "0"){
			return $retorno;
		} else {
			// altera status no banco:
			$sql = "update ServicoIVR set status = 'I' where id = $matriz[idServicoIVR]";
			$consulta2 = consultaSQL($sql, $conn);
			
			if ($consulta2) {
				return $retorno;
			}
		}
	}
}

/**
 * Ativa um Servico de IVR de um cliente no WT de acordo com
 * o idServicoIVR.
 *
 * @author João Petrelli
 * @since 09-02-2009
 * 
 * @param array $matriz
 * @return String
 */
function ativarClienteWT($matriz) {
	global $conn;
	
	$sql = "select s.ip, ivr.nome from Servidores s
		inner join Interfaces i on (s.id = i.idServidor) 
		inner join Bases b on (i.id = b.idIfaceServidor) 
		inner join ServicoIVR ivr on (b.id = ivr.idBase) 
		where ivr.id = $matriz[idServicoIVR];";

	$consulta = consultaSQL($sql, $conn);
	if ($consulta && contaConsulta($consulta) > 0){
		$ipServidor = resultadoSQL($consulta, 0, 'ip');
		$hostWt = resultadoSQL($consulta, 0, 'nome');
		$retorno = enviaComandoSocket($ipServidor,"7069","descongelar $hostWt");
		if ($retorno == "" || $retorno != "0"){
			return $retorno;
		} else {
			// altera status no banco:
			$sql = "update ServicoIVR set status = 'A' where id = $matriz[idServicoIVR]";
			$consulta2 = consultaSQL($sql, $conn);
			
			if ($consulta2) {
				return $retorno;
			}
		}
	}
}
?>