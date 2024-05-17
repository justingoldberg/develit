<?
################################################################################
#       Criado por: Devel IT
#  Data de criação: 24/06/2004
# Ultima alteração: 28/06/2004
#    Alteração No.: 002
#
# Função:
#    Painel - Funções para cadastramento de tipoCarteiraes


# abertura do modulo
function tipoCarteira($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessCadastro, $titulo;
	
	$titulo = "<b>Tipos de Carteiras</b>";
	$subtitulo = "Cadastro dos tipos de carteiras de cobrança";
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[adicionar]) {
		exibeSemPermissao($modulo, $acao);
	}
	else {
		
		# Topo da tabela - Informações e menu principal do Cadastro
		$itens=array('Adicionar', 'Procurar', 'Listar');
		
		#Monta a Tela Padrão
		getHomeModulo($modulo, $sub, $titulo, $subtitulo, $itens);
		
		# case das acoes
		echo "<br>";
		switch ($acao) {
			case "adicionar":			
				tipoCarteiraAdicionar($modulo, $sub, $acao, $registro, $matriz);
				break;
			case "alterar":
				tipoCarteiraAlterar($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'procurar':
				tipoCarteiraProcurar($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'ver':
				tipoCarteiraVer($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'excluir':
				tipoCarteiraExcluir($modulo, $sub, $acao, $registro, $matriz);
				break;
			default:
				tipoCarteiraListar($modulo, $sub, $acao, $registro, $matriz);
				break;
		}
	}
	echo "<script>location.href='#ancora';</script>";
}

function tipoCarteiraExcluir($modulo, $sub, $acao, $registro, $matriz) {
	$msg="Esta opção não está habilitada";
	avisoNOURL("Aviso: Exclusão", $msg, 400);
	echo "<br>";
	tipoCarteiraVer($modulo, $sub, 'ver', $registro, $matriz);
}

#procurar
function tipoCarteiraProcurar($modulo, $sub, $acao, $registro, $matriz) {
	
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
			getCampo('text', "Nome", 'matriz[nome]');
			
			#botao
			getBotao('matriz[bntProcurar]', 'Procurar');
			
		fechaTabela();
	} else {
		# realizar consulta
		$lista=buscaRegistros($matriz[nome], 'nome', 'contem', 'nome', "TipoCarteira");
		tipoCarteiraListar($modulo, $sub, 'ver', $lista, $matriz);
	}
}


# função para adicionar
function tipoCarteiraAdicionar($modulo, $sub, $acao, $registro, $matriz) {

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
			
			$dados=dadosTipoCarteira(0);
			
			tipoCarteiraMostra($dados);
			
			#botao
			getBotao('matriz[bntAdicionar]', 'Adicionar');
						
		fechaTabela();	
	}
	else {
		
		$grava=dbTipoCarteira($matriz, 'incluir');
		
		# Verificar inclusão de registro
		echo "<br>";
		if($grava) {
			# Visualizar
			$msg="Registro gravado com sucesso!";
			avisoNOURL("Aviso: ", $msg, 400);
			echo "<br>";
			tipoCarteiraListar($modulo, $sub, 'listar', $matriz[id], $matriz);
		} 
		else {
			$msg="Ocorreram erros durante a gravação.";
			avisoNOURL("Aviso: Ocorrência de erro", $msg, 400);
		}
	}
}



# Função para alteração de dados da Pessoa - apenas cadastro
function tipoCarteiraAlterar($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $tb, $sessCadastro;
	
	if($registro && !$matriz[bntConfirmar]) {
	
		# Buscar Valores
		$dados=dadosTipoCarteira($registro, 'id', 'igual', 'id');
			
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
			
			tipoCarteiraMostra($dados);
			
			#botao
			getBotao('matriz[bntConfirmar]', 'Alterar');
			
		fechaTabela();
	} #fecha form
	
	# Alteração - bntAlterar pressionado
	elseif($matriz[bntConfirmar]) {
		# Conferir campos
		if($matriz[id]) {
			# continuar
			# Cadastrar em banco de dados
			$grava=dbTipoCarteira($matriz, 'alterar');
			
			# Verificar inclusão de registro
			if($grava) {
				# OK
				# Visualizar
				$msg="Registro alterado com sucesso!";
				avisoNOURL("Aviso: ", $msg, 400);
				echo "<br>";
				tipoCarteiraVer($modulo, $sub, 'ver', "$matriz[id]", $matriz);
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
			$msg="Falta de parâmetros necessários. Informe os campos obrigatórios e tente novamente";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
		}
	} #fecha bntAlterar
}


function tipoCarteiraMostra($dados="") {
	
	#nome
	getCampo('text', 'Nome', 'matriz[nome]', $dados[nome]);
	
	#Descricao
	getCampo('text', 'Descrição', 'matriz[descricao]', $dados[descricao]);
	
	#valor
	getCampo('text', 'Tipo', 'matriz[valor]', $dados[valor]);
}


# Função para buscar o NOVO ID da Pessoa
function tipoCarteiraBuscaIDNovo() {

	global $conn, $tb;
	
	$sql="SELECT count(id) qtde from ".$tb[TipoCarteira];
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && resultadoSQL($consulta, 0, 'qtde')>0) {
	
		$sql="SELECT MAX(id)+1 id from ".$tb[TipoCarteira];
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
function tipoCarteiraListar($modulo, $sub, $acao, $lista, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb, $titulo;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		exibeSemPermissao($modulo, $acao);
	}
	else {
		
		# Cabeçalho
		novaTabela("Lista de ".$titulo, "left", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
			# Opcoes Adicionais
			#menuOpcAdicional($modulo, $sub, $acao, $cliente);
		fechaTabela();
		
		
		novaTabelaSH('left', '100%', 0, 2, 1, $corFundo, $corBorda, 6);
		
		if (!$lista)
			$consulta=buscaRegistros('', '', 'todos', 'nome', "TipoCarteira");
		else 
			$consulta=$lista;

		if ($consulta && contaConsulta($consulta)>0) {
			
			$largura             =array('25%',  '50%',        '5%',     '20%');
			$gravata[cabecalho]  =array('Nome', 'Descrição',  'Valor',  'Opções');
			$gravata[alinhamento]=array('left', 'left',       'center', 'left');
			
			$cor='tabfundo0';
			htmlAbreLinha($corFundo);
				for($i=0;$i<count($largura); $i++)
					itemLinhaTMNOURL($gravata[cabecalho][$i], $gravata[alinhamento][$i], 'middle', $largura[$i], $corFundo, 0, $cor);
			htmlFechaLinha();
			
			$qtd=contaConsulta($consulta);
			for($reg=0;$reg<$qtd;$reg++) {
				
				$id=resultadoSQL($consulta, $reg, 'id');
				
				#opcoes
				$def="<a href=?modulo=$modulo&sub=$sub&registro=$id";
				$fnt="<font size='2'>";
				$opcoes =htmlMontaOpcao($def."&acao=ver>".$fnt."Ver</font></a>",'ver');
				$opcoes.=htmlMontaOpcao($def."&acao=alterar>".$fnt."Alterar</font></a>",'alterar');
							
				$i=0;
				$campo[$i++]=resultadoSQL($consulta, $reg, 'nome');
				$campo[$i++]=resultadoSQL($consulta, $reg, 'descricao');
				$campo[$i++]=resultadoSQL($consulta, $reg, 'valor');
				$campo[$i++]=$opcoes;
				
				$cor='normal10';
				htmlAbreLinha($corFundo);
					for($i=0;$i<count($largura); $i++)
						itemLinhaTMNOURL($campo[$i], $gravata[alinhamento][$i].' '.$cor, 'middle', $largura[$i], $corFundo, 0, $cor);
				htmlFechaLinha();
			}
		}
		else {
			fechaTabela();
			novaTabelaSH("left", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
				$cor='txtaviso';
				htmlAbreLinha($corFundo);
					itemLinhaTMNOURL( "Não há registros para sua solicitação.", 'left', 'middle', $largura[0], $corFundo, 0, $cor);
				htmlFechaLinha();
		}
		fechaTabela();
	}
}

# função Exibição
function tipoCarteiraVer($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb, $titulo;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		exibeSemPermissao($modulo, $acao);
	}
	else {
		# Procurar dados3
		$objeto=dadosTipoCarteira($registro);
		
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
				
				#descricao
				getCampo("", "Descrição", "", $objeto[descricao]);
				
				#valor
				getCampo("", "Tipo", "", $objeto[valor]);
				
			fechaTabela();
		}
	}
}


# Função para Dados
/**
 * @return array
 * @param int $id
 * @desc retorna um array com os dados do tipoCarteira
*/
function dadosTipoCarteira($id) {

	global $tb;

	$consulta=buscaRegistros($id, 'id', 'igual', 'id', $tb[TipoCarteira]);
	
	if( contaConsulta($consulta)>0 ) {
		$retorno[id]=resultadoSQL($consulta, 0, 'id');
		$retorno[nome]=resultadoSQL($consulta, 0, 'nome');
		$retorno[descricao]=resultadoSQL($consulta, 0, 'descricao');
		$retorno[valor]=resultadoSQL($consulta, 0, 'valor');
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
function formSelectTipoCarteira($item, $campo, $retorno, $tipo) {
	
	global $conn, $tb, $corFundo, $modulo, $sub;

	$tabela=$tb[TipoCarteira];
	
	if($tipo=='check') {
		$campo=dadosTipoCarteira($item);
		$retorno=$campo[nome];
	} else {
		$retorno = getSelecDados($item, $campo, $retorno, $tipo, $tabela);
	}
	return($retorno);
}


#Função de banco de dados
function dbTipoCarteira($matriz, $tipo) {

	global $sessLogin, $sessCadastro, $conn, $tb, $modulo, $sub, $acao;
	
	$tabela=$tb[TipoCarteira];
		
	# Sql de inclusão
	if($tipo=='incluir') {
		
		$sql="INSERT INTO $tabela 
					 (nome, descricao, valor)
		      VALUES ('$matriz[nome]',
					  '$matriz[descricao]',
					  '$matriz[valor]'
					   )";
	} #fecha inclusao
	
	# Alterar
	elseif($tipo=='alterar') {
		/* Cria uma matriz com os campos ja formatados para o SQL */
		$campos[id]="id=$matriz[id]";
		$campos[nome]="nome='$matriz[nome]'";
		$campos[descricao]="descricao='$matriz[descricao]'";
		$campos[valor]="valor='".$matriz[valor]."'";
		
		$sql="
			UPDATE $tabela 
			SET
				$campos[nome],
				$campos[descricao],
				$campos[valor]
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


function imprimirBoletoFaturamento( $idFaturamento ){
	global $tb, $conn;
	
	$sql = "Select 
				$tb[TipoCarteira].valor 
			FROM
				$tb[Faturamentos]
			INNER JOIN
				$tb[FormaCobranca]
				On( $tb[Faturamentos].idFormaCobranca = $tb[FormaCobranca].id )
	 		INNER JOIN $tb[TipoCarteira]
	 		  	On( $tb[FormaCobranca].idTipoCarteira =  $tb[TipoCarteira].id )
	 		WHERE $tb[Faturamentos].id = '$idFaturamento'";
	
	$consulta = consultaSQL( $sql, $conn);
	
	if ( $consulta && contaConsulta( $consulta ) ){
		$valor = resultadoSQL( $consulta, 0, "valor");
	}
	
	return ( $valor == "S" ? true : false );
}
?>