<?
################################################################################
#       Criado por: Devel IT
#  Data de criação: 24/06/2004
# Ultima alteração: 28/06/2004
#    Alteração No.: 002
#
# Função:
#    Painel - Funções para cadastramento de debitoAutomaticoes


# abertura do modulo
function debitoAutomatico($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessCadastro, $titulo;
	
	$titulo = "Débito Automático";
	$subtitulo = "Clientes optantes por Débito Automático";
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[adicionar]) {
		exibeSemPermissao($modulo, $acao);
	}
	else {
		
		# Topo da tabela - Informações e menu principal do Cadastro
		$itens=array('Procurar', 'Listar', 'Ler Arquivo', 'Enviar Arquivo');
		
		#Monta a Tela Padrão
		getHomeModulo($modulo, $sub, '<b>'.$titulo.'</b>', '<br>'.$subtitulo, $itens);
		
		# case das acoes
		echo "<br>";
		switch ($acao) {
			case "adicionar":			
				debitoAutomaticoAdicionar($modulo, $sub, $acao, $registro, $matriz);
				break;
			case "imprimir":			
				debitoAutomaticoImprimir($modulo, $sub, $acao, $registro, $matriz);
				break;
			case "alterar":
				debitoAutomaticoAlterar($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'procurar':
				debitoAutomaticoProcurar($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'ver':
				debitoAutomaticoVer($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'excluir':
				debitoAutomaticoExcluir($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'imprimirAutorizacao';
				debitoAutomaticoImprimirAutorizacao($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'lerArquivo';
				debitoAutomaticoLerArquivo($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'enviarArquivo':
				debitoAutomaticoEnviarArquivo($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'imprimirLista':
			default:
				debitoAutomaticoListar($modulo, $sub, $acao, $registro, $matriz);
				break;
		}
	}
	echo "<script>location.href='#ancora';</script>";
}

function debitoAutomaticoExcluir($modulo, $sub, $acao, $registro, $matriz) {
	$msg="Esta opção não está habilitada";
	avisoNOURL("Aviso: Exclusão", $msg, 400);
	echo "<br>";
	debitoAutomaticoVer($modulo, $sub, 'ver', $registro, $matriz);
}

#procurar
function debitoAutomaticoProcurar($modulo, $sub, $acao, $registro, $matriz) {
	
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
			
			#ContaCorrente
			getCampo('text', "Conta Corrente", 'matriz[contaCorrente]');
			
			#botao
			getBotao('matriz[bntProcurar]', 'Procurar');
			
		fechaTabela();
	} else {
		# realizar consulta
		$lista=buscaRegistros($matriz[contacorrente], 'contacorrente', 'contem', 'contacorrente', "ClienteBanco");
		debitoAutomaticoListar($modulo, $sub, 'ver', $lista, $matriz);
	}
}


# função para adicionar
function debitoAutomaticoAdicionar($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro;
	
	if(!$matriz[bntAdicionar]) {
		
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
				<input type=hidden name=matriz[bntConfirmar] value=$matriz[bntConfirmar]>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			$dados=dadosdebitoAutomatico(0);
			
			debitoAutomaticoMostra($dados);
			
			#botao
			getBotao('matriz[bntAdicionar]', 'Adicionar');
						
		fechaTabela();	
	}
	else {
		if( $matriz[agencia] && ($matriz[digAg] || $matriz[digAg] === '0') && $matriz[contacorrente] && ($matriz[digCC] || $matriz[digCC] === '0') ){
			$grava=dbdebitoAutomatico($matriz, 'incluir');
			
			# Verificar inclusão de registro
			echo "<br>";
			if($grava) {
				# Visualizar
				$msg="Registro gravado com sucesso!";
				avisoNOURL("Aviso: ", $msg, 400);
				echo "<br>";
				debitoAutomaticoListar($modulo, $sub, 'listar', $matriz[id], $matriz);
			} 
			else {
				$msg="Ocorreram erros durante a gravação.";
				avisoNOURL("Aviso: Ocorrência de erro", $msg, 400);
			}
		}
		
		# falta de parametros
		else {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Informe os campos obrigatórios e tente novamente";
			$url="?modulo=$modulo&sub=$sub&acao=$acao&idPlanosPessoas=$matriz[idPlanosPessoas]";
			aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
		}
	}
}



# Função para alteração de dados da Pessoa - apenas cadastro
function debitoAutomaticoAlterar($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $tb, $sessCadastro;
	
	if($registro && !$matriz[bntConfirmar]) {
	
		# Buscar Valores
		$dados=dadosdebitoAutomatico($registro, 'id', 'igual', 'id');
			
		# Motrar tabela de busca
		novaTabela2("[Alterar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			menuOpcAdicional($modulo, $sub, $acao, $registro);
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=matriz[id] value=$registro>
				<input type=hidden name=acao value=$acao>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			debitoAutomaticoMostra($dados);
			
			#botao
			getBotao('matriz[bntConfirmar]', 'Alterar');
			
		fechaTabela();
	} #fecha form
	
	# Alteração - bntAlterar pressionado
	elseif($matriz[bntConfirmar]) {
		# Conferir campos
		if( $matriz[id] && $matriz[agencia] && $matriz[digAg] && $matriz[contacorrente] && $matriz[digCC] ) {
			# continuar
			# Cadastrar em banco de dados
			$grava=dbdebitoAutomatico($matriz, 'alterar');
			
			# Verificar inclusão de registro
			if($grava) {
				# OK
				# Visualizar
				$msg="Registro alterado com sucesso!";
				avisoNOURL("Aviso: ", $msg, 400);
				echo "<br>";
				debitoAutomaticoVer($modulo, $sub, 'ver', "$matriz[id]", $matriz);
			} else {
				echo "<br>";
				$msg="Ocorreram erros durante a gravação.";
				avisoNOURL("Aviso: Ocorrência de erro", $msg, 400);
			}
		}
		# falta de parametros
		else {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Informe os campos obrigatórios e tente novamente";
			$url="?modulo=$modulo&sub=$sub&acao=$acao&registro=$matriz[id]";
			aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
		}
	} #fecha bntAlterar
}


function debitoAutomaticoMostra($dados="") {
	novaLinhaTabela($corFundo, '100%');
		$texto="<input type=hidden name=matriz[idPlanosPessoas] value=$dados[idPlanosPessoas]>";
		itemLinhaForm($texto, 'left', 'top',  $corFundo, 2, 'tabfundo1');
		//$texto="<input type=hidden name=matriz[idFormaCobranca] value=$dados[idFormaCobranca]>";
		//itemLinhaForm($texto, 'left', 'top',  $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
	
	#nome
	getCampo('hidden', 'Cliente', 'matriz[nome]', $dados[nome]);
	
	#Agencia
	getCampo('text', 'Agência', 'matriz[agencia]', $dados[agencia]);
	
	#Dígito da Agencia
	getCampo('text','Dígito Agência','matriz[digAg]', $dados[digAg]);
	
	#ContaCorrente
	getCampo('text', 'Conta Corrente', 'matriz[contacorrente]', $dados[contacorrente]);
	
	#Dígito da C/C
	getCampo('text','Dígito Conta Corrente','matriz[digCC]',$dados[digCC]);
	
	#Identificacao
	getCampo('hidden', 'Código no Banco', 'matriz[identificacao]', $dados[identificacao]);
}


# Função para buscar o NOVO ID da Pessoa
function debitoAutomaticoBuscaIDNovo() {

	global $conn, $tb;
	
	$sql="SELECT count(id) qtde from ".$tb[ClienteBanco];
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && resultadoSQL($consulta, 0, 'qtde')>0) {
	
		$sql="SELECT MAX(id)+1 id from ".$tb[ClienteBanco];
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
function debitoAutomaticoListar($modulo, $sub, $acao, $lista, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb, $titulo, $conn;
	
	$geraRelatorio= ($acao=='imprimirLista' ? true:false);
	$matCabecalho=array('Nome', 'Agência', 'Conta Corrente', 'Código banco');
	$matAlinhamento=array('left', 'center',  'center','center');
	$matLargura=array('30%',  '10%','10%','10%');
	$matResultado=array();
	
	$gravata[largura]= $matLargura;
	$gravata[cabecalho]= $matCabecalho;
	$gravata[alinhamento] = $matAlinhamento;
	
	$gravata[cabecalho][]="Opções";
	$gravata[alinhamento][]="left";
	$gravata[largura][]="40%";
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		exibeSemPermissao($modulo, $acao);
	}
	else {
		#realiza a consulta no BD
		$sql="SELECT	ClienteBanco.id id,
						PlanosPessoas.id idPlanosPessoas, 
						Pessoas.nome nome, 
						if(isnull(ClienteBanco.agencia), '-', ClienteBanco.agencia) agencia, 
						if(isnull(ClienteBanco.contacorrente), '-', ClienteBanco.contacorrente) contacorrente, 
						if(isnull(ClienteBanco.identificacao), '-', ClienteBanco.identificacao) identificacao 
				FROM 	Pessoas, 
						PessoasTipos, 
						FormaCobranca, 
						TipoCarteira, 
						PlanosPessoas left join ClienteBanco on (PlanosPessoas.id=ClienteBanco.idPlanosPessoas) 
				WHERE	TipoCarteira.valor = 'D' 
						AND FormaCobranca.idTipoCarteira=TipoCarteira.id 
						AND PlanosPessoas.idFormaCobranca = FormaCobranca.id 
						AND PlanosPessoas.idPessoaTipo = PessoasTipos.id 
						AND Pessoas.id=PessoasTipos.idPessoa 
			";
		$lista=consultaSQL($sql, $conn);
		
		# Cabeçalho
		novaTabela("[Lista de ".$titulo."]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 0);
			# Opcoes Adicionais
			#menuOpcAdicional($modulo, $sub, $acao, $cliente);
		fechaTabela();
		
		
		novaTabelaSH('left', '100%', 0, 2, 1, $corFundo, $corBorda, 6);
		menuOpcAdicional($modulo,$sub,$acao,$registro,$matriz,5);
		if (!$lista)
			$consulta=buscaRegistros('', '', 'todos', 'id', "ClienteBanco");
		else 
			$consulta=$lista;

		if ($consulta && contaConsulta($consulta)>0) {
			

			
			$cor='tabfundo0';
			# monta o cabecalhoda tabela
			htmlAbreLinha($corFundo);
				for($i=0;$i<count($gravata[largura]); $i++)
					itemLinhaTMNOURL($gravata[cabecalho][$i], $gravata[alinhamento][$i], 'middle', $gravata[largura][$i], $corFundo, 0, $cor);
			htmlFechaLinha();
			
			$qtd=contaConsulta($consulta);
			$linha=1;
			$matDetalhe= array();
			for($reg=0;$reg<$qtd;$reg++) {				
				$id=resultadoSQL($consulta, $reg, 'id');
				$idPlanosPessoas=resultadoSQL($consulta, $reg, 'idPlanosPessoas');
				//$idPlanosPessoas=resultadoSQL($consulta, $reg, 'idPlanosPessoas');
				
				#opcoes
				if($id) {
					$def="<a href=?modulo=$modulo&sub=$sub&registro=$id";
					$fnt="<font size='2'>";
					$opcoes =htmlMontaOpcao($def."&acao=ver>".$fnt."Ver</font></a>",'ver');
					$opcoes.=htmlMontaOpcao($def."&acao=alterar>".$fnt."Alterar</font></a>",'alterar');
				} else {
					//$def="<a href=?modulo=$modulo&sub=$sub&idFormaCobranca=$idFormaCobranca";
					$def="<a href=?modulo=$modulo&sub=$sub&idPlanosPessoas=$idPlanosPessoas";
					$fnt="<font size='2'>";
					$opcoes =htmlMontaOpcao($def."&acao=adicionar>".$fnt."Adicionar</font></a>",'ver');
				}
				$opcoes.=htmlMontaOpcao($def."&acao=imprimirAutorizacao>".$fnt."Imprimir Autorização</font></a>",'imprimir');
							
				$i=0;
				$campo[$i++]=resultadoSQL($consulta, $reg, 'nome');
				$campo[$i++]=resultadoSQL($consulta, $reg, 'agencia');
				$campo[$i++]=resultadoSQL($consulta, $reg, 'contacorrente');
				$campo[$i++]=resultadoSQL($consulta, $reg, 'identificacao');
				$campo[$i++]=$opcoes;
				
				$cor='normal10';
				htmlAbreLinha($corFundo);
					for($i=0;$i<count($gravata[largura]); $i++){
						$matDetalhe[$i][$linha]= $campo[$i];
						itemLinhaTMNOURL($campo[$i], $gravata[alinhamento][$i].' '.$cor, 'middle', $gravata[largura][$i], $corFundo, 0, $cor);
					}
				htmlFechaLinha();
				$linha++;
			}
		}
		else {
			fechaTabela();
			novaTabelaSH("left", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
				$cor='txtaviso';
				htmlAbreLinha($corFundo);
					itemLinhaTMNOURL( "Não há registros para sua solicitação.", 'left', 'middle', $gravata[largura]	[0], $corFundo, 0, $cor);
				htmlFechaLinha();
		}
		fechaTabela();
		
		if ($geraRelatorio) {
				# Alimentar Matriz Geral
				$matrizRelatorio[detalhe]=$matDetalhe;
				
				# Alimentar Matriz de Header
				$matrizRelatorio[header][TITULO]=$titulo;
				$matrizRelatorio[header][IMG_LOGO]=$html[imagem][logoRelatorio];
				$matrizRelatorio[header][cabecalho]=$matCabecalho;
				
				# Configurações
				$matrizRelatorio[config][layout]='landscape';
				$matrizRelatorio[config][marginleft]='1.0cm;';
				$matrizRelatorio[config][marginright]='1.0cm;';
				$matrizRelatorio[config][alinhamento]=$matAlinhamento;
				$matrizRelatorio[config][largura]=$matLargura;
				
				//gera o PDF
				//instancio a classe de relatorios com o parametro da orientacao da pagina!
				$relatorio= new Relatorio2Pdf();
				//utilizando a classe chamamos o  metodo geraImpressao
				$nomeArquivo=
					$relatorio->geraImpressao($matrizRelatorio);
				exibeLinkPdf($titulo,$nomeArquivo);
			}
	}
}

# função Exibição
function debitoAutomaticoVer($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb, $titulo;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		exibeSemPermissao($modulo, $acao);
	}
	else {
		# Procurar dados3
		$objeto=dadosdebitoAutomatico($registro);
		
		if(is_array($objeto)) {
			# Motrar tabela de busca
			novaTabela2("[$titulo - Visualização]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				
				# Opcoes Adicionais
				#menuOpcAdicional($modulo, $sub, 'ver', $registro);
				
				$bgLabel='tabfundo1';
				$bgCampo='tabfundo1';
				
				itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
				
				#nome
				getCampo("", "Nome", "", $objeto[nome]);
				
				#agencia
				getCampo("", "Agência", "", $objeto[agencia]);
				
				#Dígito da Agencia
				getCampo('','Dígito Agência','', $objeto[digAg]);
				
				#contacorrente
				getCampo("", "Conta Corrente", "", $objeto[contacorrente]);
				
				#Dígito da C/C
				getCampo('','Dígito Conta Corrente','',$objeto[digCC]);
				
				#identificacao
				getCampo("", "Identificação", "", $objeto[identificacao]);
				
			fechaTabela();
		}
	}
}


# Função para Dados
/**
 * @return array
 * @param int $id
 * @desc retorna um array com os dados do debitoAutomatico
*/
function dadosdebitoAutomatico($id) {

	global $tb;

	$consulta=buscaRegistros($id, 'id', 'igual', 'id', $tb[ClienteBanco]);
	
	if( contaConsulta($consulta)>0 ) {
		$retorno[id]=resultadoSQL($consulta, 0, 'id');
		$retorno[idPlanosPessoas]=resultadoSQL($consulta, 0, 'idPlanosPessoas');
		$retorno[agencia]=resultadoSQL($consulta, 0, 'agencia');
		$retorno[digAg]=resultadoSQL($consulta, 0, 'digAg');
		$retorno[contacorrente]=resultadoSQL($consulta, 0, 'contacorrente');
		$retorno[digCC]=resultadoSQL($consulta, 0, 'digCC');
/**/		$retorno[identificacao]=resultadoSQL($consulta, 0, 'identificacao');
	}
	if (!$retorno[idPlanosPessoas]) { //idFormaCobranca trocado por idPlanosPessoas
		$retorno[idPlanosPessoas]=$_REQUEST["idPlanosPessoas"];
	}
	$consulta=buscaRegistros($retorno[idPlanosPessoas], 'id', 'igual','id', $tb[PlanosPessoas]);
	if($consulta) {
		$retorno[idPessoaTipo]=resultadoSQL($consulta, 0, 'idPessoaTipo');
		$retorno[idFormaCobranca] = resultadoSQL( $consulta, 0, 'idFormaCobranca' );
	}
	
	if($retorno[idPessoaTipo]) {
		$pt=dadosPessoasTipos($retorno[idPessoaTipo]);
		$retorno[nome]=$pt[pessoa][nome];
	}
	
	if( $retorno[idFormaCobranca] ){
		$consulta = buscaRegistros( $retorno[idFormaCobranca], 'id', 'igual', 'id', $tb[FormaCobranca] );
		$retorno[idBanco] = resultadoSQL( $consulta, 0, 'idBanco' );
		$consulta = buscaRegistros( $retorno[idBanco], 'id', 'igual', 'id', $tb[Bancos] );
		$retorno[identificacao] = resultadoSQL( $consulta, 0, 'numero' );
	}
	return($retorno);
}


#Função de banco de dados
function dbdebitoAutomatico($matriz, $tipo) {

	global $sessLogin, $sessCadastro, $conn, $tb, $modulo, $sub, $acao;
	
	$tabela=$tb[ClienteBanco];
		
	# Sql de inclusão
	if($tipo=='incluir') {
		
		$sql="INSERT INTO $tabela 
					 (idPlanosPessoas, agencia, contacorrente, identificacao, digAg, digCC)
		      VALUES ('$matriz[idPlanosPessoas]',
					  '$matriz[agencia]',
					  '$matriz[contacorrente]',
					  '$matriz[identificacao]',
					  '$matriz[digAg]',
					  '$matriz[digCC]'
					   )";
	} #fecha inclusao
	
	# Alterar
	elseif($tipo=='alterar') {
		/* Cria uma matriz com os campos ja formatados para o SQL */
		$campos[id]="id=$matriz[id]";
		$campos[idPlanosPessoas]="idPlanosPessoas=$matriz[idPlanosPessoas]";
		$campos[agencia]="agencia='$matriz[agencia]'";
		$campos[digAg]= "digAg='$matriz[digAg]'";
		$campos[contacorrente]="contacorrente='$matriz[contacorrente]'";
		$campos[digCC]= "digCC='$matriz[digCC]'";
		$campos[identificacao]="identificacao='".$matriz[identificacao]."'";
		
		$sql="
			UPDATE $tabela 
			SET
				$campos[idPlanosPessoas],
				$campos[agencia],
				$campos[digAg],
				$campos[contacorrente],
				$campos[digCC],
				$campos[identificacao] 
			WHERE
				$campos[id]";
	}
	
	elseif($tipo=='excluir') {
		$sql="DELETE FROM $tabela WHERE $campos[id]";
	}
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
}

function debitoAutomaticoImprimirAutorizacao($modulo, $sub, $acao, $registro, $matriz){
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $tb, $titulo;
	
	//chamada da funcao 
	$autorizacao= new AutorizacaoDebitoAutomatico();
	
	$dados= dadosdebitoAutomatico($registro);
	$plano= buscaRegistros($dados[idPessoaTipo],'idPessoaTipo','igual','id',$tb[PlanosPessoas]);

	if( $plano && contaConsulta( $plano ) ){
		// depois de definido o plano da Pessoa, localiza o nome e valor do serviço
		$idPlano= resultadoSQL($plano,'0','id');
		
		$servico= buscaRegistros($idPlano,'idPlano','igual','id',$tb[ServicosPlanos]);
		if (contaConsulta($servico)>0){
			$valor= resultadoSQL($servico,'0','valor');
		}
		if (contaConsulta($servico)>0){
			$descricao= resultadoSQL($plano,'0',nome);
		}
		
		//busca os dados do tipo da Pessoa
		$dadosPessoas= dadosPessoasTipos($dados[idPessoaTipo]);
		
		//busca os dados da Pessoa a partir de seu tipo
		$pessoas= buscaRegistros($dadosPessoas[idPessoa],'id','igual','id',$tb[Pessoas]);
		
		//recupera o id do Pop da Pessoa
		$idPop= resultadoSQL($pessoas,'0','idPop');
		
		//A partir dessa identificaca procuro o Pop principal da pessoa
		$popPrincipal=buscaRegistros($idPop,'idPop','igual','idPop',$tb[POPCidade]);
		if( $popPrincipal && contaConsulta( $popPrincipal ) ) {
			while($rs=mysql_result($popPrincipal,'principal')){
				if ($rs != ''){ //por default o valor deve ser 'S' caso contrario '' or 'N'	
					$idPopPrincipal= resultadoSQL($popPrincipal,'0','idCidade');
					break;
				}
			}
			//De posse do id do Pop principal recuperamos os dados necessários CIDADE E UF
			$cidade=buscaRegistros($idPopPrincipal,'id','igual','id',$tb[Cidades]);
			$nomeCidade= resultadoSQL($cidade,'0','nome');
			$ufCidade= resultadoSQL($cidade,'0','uf');
		}

	}
	 $nomeArquivo= $autorizacao->geraAutorizacao($dados[nome],$valor,$descricao,$nomeCidade,$ufCidade);
	exibeLinkPdf($titulo,$nomeArquivo);
}
function exibeLinkPdf($titulo, $nomeArquivo){
	global $corBorda,$corFundo;
	novaTabela('[Arquivo Gerado]<a name=ancora></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
		htmlAbreLinha($corfundo);
			itemTabelaNOURL(htmlMontaOpcao("<a href=$nomeArquivo>$titulo</a>",'pdf'), 'center', $corFundo, 7, 'txtaviso');
		htmlFechaLinha();
	fechaTabela();
}

function debitoAutomaticoLerArquivo($modulo, $sub, $acao, $registro, $matriz){
	
	# Mostrar detalhes do faturamento
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessPlanos, $conn, $tb, $configMensagemArquivo;

	if($matriz[bntConfirmar]) {
		# Gerar arquivo e mostrar link para download
		echo "confirmado";
		# carregar imagem do arquivo
		$arquivo=$_REQUEST["arquivo"];
		
		$matriz[conteudo]=(fread
                 (fopen ($_FILES["arquivo"]["tmp_name"], "r"),
                 filesize ($_FILES["arquivo"]["tmp_name"])));
					  
		$matriz[nomeArquivo]=$_FILES[arquivo][name];
		
		# Validar arquivo
		$validarArquivo=validaLayout($matriz,$matriz[idBanco]);
		
		if($validarArquivo) {
			# Ocorreu algum erro na leitura do arquivo
			avisoNOURL("Aviso", $validarArquivo, 400);
			echo "<br>";
		}
		else {
		
			$gravaArquivo=dbArquivoRetorno($matriz, 'incluir');
		
			if($gravaArquivo) {
				# OK
				$msg="Arquivo importado com sucesso!";
				avisoNOURL("Importação de arquivo retorno", $msg, 400);
				echo "<br>";
			}
			else {
				# erro
				$msg="ERRO: Ocorreu um erro ao importar o arquivo de retorno!";
				avisoNOURL("Aviso: Ocorrência de erro", $msg, 400);
				echo "<br>";
			}
		}
	
		listarArquivosRetorno($modulo, $sub, 'listar',$registro, '');

	}
	else {
		formImportarArquivoRetorno($modulo, $sub, $acao, $registro, $matriz);
	}
}

function debitoAutomaticoEnviarArquivo($modulo, $sub, $acao, $registro, $matriz){
		
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
		# Topo da tabela - Informações e menu principal do Cadastro
		novaTabela2("[Geração de Arquivos Remessa]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 0);
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'left', $corFundo, 0, 'tabfundo1');
					echo "<br><img src=".$html[imagem][arquivos]." border=0 align=left><b class=bold>Geração de Arquivos Remessa</b><br>
					 <span class=normal10>A <b>geração de arquivos remessa</b> permite a transferência de informações
					 financeiras, extraídas do sistema para faturamento e lançamentos de cobrança, para bancos de acordo
					 informações devidamente configuradas nas <b>formas de cobrança</b>.</span>";
				htmlFechaColuna();
			fechaLinhaTabela();
		fechaTabela();
		
		echo "<br>";
		
				arquivosListarFaturamentos($modulo, "arquivoremessa", "listar", $registro, $matriz);
		
		echo "<script>location.href='#ancora';</script>";
		
		
	}
	
}
?>