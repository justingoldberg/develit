<?
################################################################################
#       Criado por: Devel IT
#  Data de criação: 05/07/2004
# Ultima alteração: 05/07/2004
#    Alteração No.: 001
#
# Função:
#    Painel - Funções para cadastramento de Caracteristicas de equiptoCaracteristicas


# abertura do modulo
function equiptoCaracteristica($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessCadastro, $titulo;
	
	$titulo = "<b>Caracteristicas de Equipamentos/Tipo</b>";
	$subtitulo = "<br>Cadastro de Caracteristicas de Equipamentos/Tipo";
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[adicionar]) {
		exibeSemPermissao($modulo, $acao);
	}
	else {
		
		# Topo da tabela - Informações e menu principal do Cadastro
		$itens=Array('Adicionar', 'Listar', 'Procurar');
		getHomeModulo($modulo, $sub, $titulo, $subtitulo, $itens);
		
		# case das acoes
		echo "<br>";
		switch ($acao) {
			case "adicionar":		
				equiptoCaracteristicaAdicionar($modulo, $sub, $acao, $registro, $matriz);
				break;
			case "alterar":
				equiptoCaracteristicaAlterar($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'procurar':
				equiptoCaracteristicaProcurar($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'ver':
				equiptoCaracteristicaVer($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'excluir':
				equiptoCaracteristicaExcluir($modulo, $sub, $acao, $registro, $matriz);
				break;
			case 'incluirCaracteristicaTipo':
				equiptoCaracteristicaIncluirTipo($modulo, $sub, $acao, $registro, $matriz);
				break;
			default:
				equiptoCaracteristicaListar($modulo, $sub, $acao, $registro, $matriz);
				break;
		}
	}
	echo "<script>location.href='#ancora';</script>";
}


/**
 * @return void
 * @param unknown $modulo
 * @param unknown $sub
 * @param unknown $acao
 * @param unknown $registro
 * @param unknown $matriz
 * @desc Incluir uma caracteristica no tipo.
*/
function equiptoCaracteristicaIncluirTipo($modulo, $sub, $acao, $registro, $matriz) {
	
}


function equiptoCaracteristicaExcluir($modulo, $sub, $acao, $registro, $matriz) {
	$msg="Esta opção não está habilitada";
	avisoNOURL("Aviso: Exclusão", $msg, '400');
	echo "<br>";
	equiptoCaracteristicaVer($modulo, $sub, 'ver', $registro, $matriz);
}


#procurar
function equiptoCaracteristicaProcurar($modulo, $sub, $acao, $registro, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessCadastro, $titulo, $tb;
	
	if( !$matriz[bntProcurar] ) {

		novaTabela2("[Procurar $titulo]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			
			novaLinhaTabela($corFundo, '100%');
			$texto="<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro value=$registro>";
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
		$lista=buscaRegistros($matriz[nome], 'nome', 'contem', 'nome', $tb[EquiptoCaracteristica]);
		equiptoCaracteristicaListar($modulo, $sub, 'ver', $lista, $matriz);
	}
}


# função para adicionar
function equiptoCaracteristicaAdicionar($modulo, $sub, $acao, $registro, $matriz) {

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
				<input type=hidden name=registro value=$registro>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			$dados=dadosequiptoCaracteristica(0);
			
			equiptoCaracteristicaMostra($dados);
			
			#botao
			getBotao('matriz[bntAdicionar]', 'Adicionar');
						
		fechaTabela();	
	}
	else {
		
		$grava=dbEquiptoCaracteristica($matriz, 'incluir');
		
		# Verificar inclusão de registro
		if($grava) {
			# Visualizar
			$msg="Registro gravado com sucesso!";
			avisoNOURL("Aviso: ", $msg, '100%');
			echo "<br>";
			$matriz[naoInclui]=1;
			equiptoCaracteristicaListar($modulo, $sub, $acao, $registro, $matriz, 'ver');
		}
		else {
			$msg="Ocorreram erros durante a gravação.";
			avisoNOURL("Aviso: Ocorrência de erro", $msg, '60%');
		}
	}
}



# Função para alteração de dados da Pessoa - apenas cadastro
function equiptoCaracteristicaAlterar($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $tb, $sessCadastro;
	
	if($registro && !$matriz[bntConfirmar]) {
	
		# Buscar Valores
		$dados=dadosequiptoCaracteristica($registro, 'id', 'igual', 'id');
			
		# Motrar tabela de busca
		novaTabela2("[Alterar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			
			# Opcoes Adicionais
			#menuOpcAdicional('servicoIVR', $sub, 'ver', $registro, $matriz);
				
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=registro value=$registro>
				<input type=hidden name=matriz[id] value=$registro>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=subacao value=alterar>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
						
			equiptoCaracteristicaMostra($dados);
			
			#botao
			getBotao('matriz[bntConfirmar]', 'Alterar');
			
		fechaTabela();
	} #fecha form
	
	# Alteração - bntAlterar pressionado
	elseif($matriz[bntConfirmar]) {
		if($matriz[id]) {
			# continuar
			# Cadastrar em banco de dados
			$grava=dbEquiptoCaracteristica($matriz, 'alterar');
			
			# Verificar inclusão de registro
			if($grava) {
				# OK
				# Visualizar
				$msg="Registro alterado com sucesso!";
				avisoNOURL("Aviso: ", $msg, '100%');
				echo "<br>";
				equiptoCaracteristicaVer($modulo, $sub, $acao, $registro, $matriz);
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


function equiptoCaracteristicaMostra($dados="") {
	global $conn, $tb;
	
	$tamanho=60;
	
	getCampo('text',   'Nome',       'matriz[nome]',     $dados[nome],     "", "", $tamanho);
	getCampo('area',   'Descrição',  'matriz[descricao]',$dados[descricao],"", "", $tamanho); 
	getCampo('status', 'Status',     'matriz[status]',   $dados[status]);
	
}


# Função para buscar o NOVO ID da Pessoa
function equiptoCaracteristicaBuscaIDNovo() {

	global $conn, $tb;
	
	$tabela=$b["EquiptoCaracteristica"];
	
	$sql="SELECT count(id) qtde from $tabela";
	$consulta=consultaSQL($sql, $conn);
	if($consulta && resultadoSQL($consulta, 0, 'qtde')>0) {
	
		$sql="SELECT MAX(id)+1 id from $tabela";
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
function equiptoCaracteristicaListar($modulo, $sub, $acao, $registro, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb, $titulo;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		exibeSemPermissao($modulo, $acao);
	}
	else {
		
		# Cabeçalho
		novaTabela("Lista de $titulo", "left", '100%', 0, 4, 1, $corFundo, $corBorda, 4);
		
			novaLinhaTabela($corFundo, '100%');
				# Opcoes Adicionais
				#menuOpcAdicional('EquiptoCaracteristica', $sub, 'adicionar', $registro, $matriz, 4);
			fechaLinhaTabela();
				
			#monta uma lista de servicosPlanos para o sql
			$consulta=buscaRegistros($matriz[id], 'id',  'todos', 'nome', $tb[EquiptoCaracteristica]);
			
			if ($consulta && contaConsulta($consulta)>0) {
				$largura             =array('25%',  '38%',       '10%',    '27%');
				$gravata[cabecalho]  =array('Nome', 'Descrição', 'Status', 'Opções');
				$gravata[alinhamento]=array('left', 'left',      'center', 'left');
				
				htmlAbreLinha($corFundo);
					for($i=0;$i<count($largura); $i++)
						itemLinhaTMNOURL($gravata[cabecalho][$i], $gravata[alinhamento][$i], 'middle', $largura[$i], $corFundo, 0, 'tabfundo0');
				htmlFechaLinha();
				
				$qtd=contaConsulta($consulta);
				for($reg=0;$reg<$qtd;$reg++) {
					
					$id=resultadoSQL($consulta, $reg, 'id');
					$st=resultadoSQL($consulta, $reg, 'status');
	
					#opcoes
					$def="<a href=?modulo=$modulo&sub=$sub&registro=$id";
					$fnt="<font size='2'>";
					
					$opcoes =htmlMontaOpcao($def."&acao=ver>".$fnt."Ver</font></a>",'ver');
					$opcoes.=htmlMontaOpcao($def."&acao=alterar>".$fnt."Alterar</font></a>",'alterar');
					$opcoes.=htmlMontaOpcao($def."&acao=excluir>".$fnt."Excluir</font></a>",'excluir');
					
					$i=0;
					$campo[$i++]=resultadoSQL($consulta, $reg, 'nome');
					$campo[$i++]=resultadoSQL($consulta, $reg, 'descricao');
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
					itemLinhaTMNOURL( "Não há registros para sua solicitação.", 'left', 'middle', '100%', $corFundo, 4, 'txtaviso');
				fechaLinhaTabela();
			}
		fechaTabela();
	}
	
}

#busca

# função Exibição
function equiptoCaracteristicaVer($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb, $titulo;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		exibeSemPermissao($modulo, $acao);
	}
	else {
		# Procurar dados3
		$objeto=dadosEquiptoCaracteristica($registro);
		
		if(is_array($objeto)) {
			# Motrar tabela de busca
			novaTabela2("[$titulo - Visualização]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				
				# Opcoes Adicionais
				#menuOpcAdicional('equiptoCaracteristica', $sub, 'alterar', $registro, $matriz);
				#itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
				getCampo('', 'Nome', 'matriz[nome]', $objeto[nome]);
				getCampo('', 'Descrição', 'matriz[descricao]', $objeto[descricao]);
				getCampo('', 'Status', 'matriz[status]', getComboStatus($objeto[status], "", 'check'));
				getCampo('', 'Cadastrado', 'matriz[dtCadastro]', converteData($objeto[dtCadastro], 'banco', 'form'));
			fechaTabela();

		}
	}
}


# Função para Dados
/**
 * @return array
 * @param int $id
 * @desc Retorna um array com os dados
 extra
 dados[base] = dados de base
*/
function dadosEquiptoCaracteristica($id) {

	global $tb;

	$tabela=$tb[EquiptoCaracteristica];
	
	$consulta=buscaRegistros($id, 'id', 'igual', 'id', $tabela);
	
	if( contaConsulta($consulta)>0 ) {
		$retorno[id]=resultadoSQL($consulta, 0, 'id');
		$retorno[nome]=resultadoSQL($consulta, 0, 'nome');
		$retorno[descricao]=resultadoSQL($consulta, 0, 'descricao');
		$retorno[status]=resultadoSQL($consulta, 0, 'status');
		$retorno[dtCadastro]=resultadoSQL($consulta, 0, 'dtCadastro');
	}
	
	return($retorno);
	
}

/**
 * @return unknown
 * @param unknown $item
 * @param unknown $campo
 * @param unknown $retorno
 * @param unknown $Caracteristica
 * @desc Retorna 1 ou varios itens do objeto.
 item  = id 
 campo = campo da matriz
 retorno = campo de retorno (nome do componente)
 Caracteristica = check - retorna o nome
 ................ form  - um combo com os nomes
 ................ multi - uma caixa de selecao com os nomes
 Exclusoes = Array de valores que nao entram na lista
*/
function formSelectEquiptoCaracteristica($item, $campo, $retorno, $Caracteristica, $exclusoes="") {
	
	global $conn, $tb, $corFundo, $modulo, $sub;

	$tabela=$tb[EquiptoCaracteristica];
	
	if($Caracteristica=='check') {
		$campo=dadosequiptoCaracteristica($item);
		$retorno=$campo[nome];
	} else {
		$retorno = getSelectDados($item, $campo, $retorno, $Caracteristica, $tabela, '', $exclusoes);
	}
	return($retorno);
}


#Função de banco de dados
/**
 * @return unknown
 * @param unknown $matriz
 * @param unknown $Caracteristica
 * @desc Tratamento da sentenca sql
*/
function dbEquiptoCaracteristica($matriz, $Caracteristica) {

	global $sessLogin, $sessCadastro, $conn, $tb, $modulo, $sub, $acao;
	
	$tabela=$tb[EquiptoCaracteristica];

	# Sql de inclusão
	if($Caracteristica=='incluir') {
		
		$sql="INSERT INTO $tabela 
		      VALUES ('',
					  '$matriz[nome]',
					  '$matriz[descricao]',
					  '$matriz[status]',
					  now()
					   )";
	} #fecha inclusao
	
	# Alterar
	elseif($Caracteristica=='alterar') {
		/* Cria uma matriz com os campos ja formatados para o SQL */
		$campos[id]="id=$matriz[id]";
		$campos[nome]="nome='".$matriz[nome]."'";
		$campos[descricao]="descricao='".$matriz[descricao]."'";
		$campos[status]="status='".$matriz[status]."'";
		
		$sql="UPDATE $tabela SET
					$campos[nome],
					$campos[descricao],
					$campos[status]
				WHERE
					$campos[id]";
	}
	
	elseif($Caracteristica=='excluir') {
		$sql="DELETE FROM $tabela WHERE $campos[id]";
	}
	
	if($sql) { 
		#echo $sql;
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
}

#
/**
 * @return void
 * @param unknown $modulo
 * @param unknown $sub
 * @param unknown $acao
 * @param unknown $registro
 * @param unknown $matriz
 * @desc Lista as caractisticas por Tipo
*/
function equiptoCaracteristicaTipoLista($modulo, $sub, $acao, $registro, $matriz) {
	
	global $conn, $corFundo, $corBorda, $html, $sessLogin, $tb, $titulo;
	
	$exclusoes=array();
	
	#monta uma lista de servicosPlanos para o sql
	$consulta=getCaracteristicaTipo($matriz);
	
	$qtd=contaConsulta($consulta);
	for($reg=0;$reg<$qtd;$reg++) {
		$idCaracteristica=resultadoSQL($consulta, $reg, 'idCaracteristica');
		$exclusoes[]=$idCaracteristica;
	}
	
	# Cabeçalho
	novaTabela("Caracteristica do Tipo", "left", '100%', 0, 4, 1, $corFundo, $corBorda, 4);
		
		#Prepara o form
		novaLinhaTabela($corFundo, '100%');
		$texto="<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=registro value=$registro>
			<input type=hidden name=acao value=incluirCaracteristicaTipo>
			<input type=hidden name=subacao value=alterar>Utilize a linha abaixo para incluir novas características:";
			itemLinhaNOURL($texto, 'left', $corFundo, 3, 'tabfundo1');
		fechaLinhaTabela();
		
		$nome=formSelectEquiptoCaracteristica('id', 'nome', 'matriz[idCaracteristica]', 'formnochange', $exclusoes);
		$valor="<input type=text size=40 name=matriz[valor] value=''>";
		$botao="<input type=submit name=btnIncluir value='Incluir' class=submit>";
		
		$i=0;
		htmlAbreLinha($corFundo);
			itemLinhaTMNOURL($nome,  $gravata[alinhamento][$i].' normal10', 'middle', $largura[$i++], $corFundo, 0, 'normal10');
			itemLinhaTMNOURL($valor, $gravata[alinhamento][$i].' normal10', 'middle', $largura[$i++], $corFundo, 0, 'normal10');
			itemLinhaTMNOURL($botao, $gravata[alinhamento][$i].' normal10', 'middle', $largura[$i++], $corFundo, 0, 'normal10');
		htmlFechaLinha();
		
		$largura             =array('25%',  '60%',      '15%');
		$gravata[cabecalho]  =array('Nome', 'Conteúdo', 'Opções');
		$gravata[alinhamento]=array('left', 'Left',     'left');
		
		htmlAbreLinha($corFundo);
			for($i=0;$i<count($largura); $i++)
				itemLinhaTMNOURL($gravata[cabecalho][$i], $gravata[alinhamento][$i], 'middle', $largura[$i], $corFundo, 0, 'tabfundo0');
		htmlFechaLinha();
		
		
		# Exibe a lista de caracteristicas existentes
		if ($consulta && contaConsulta($consulta)>0) {
		
			for($reg=0;$reg<$qtd;$reg++) {
				
				$id=resultadoSQL($consulta, $reg, 'id');
				
				#opcoes
				$def="<a href=?modulo=$modulo&sub=$sub&registro=$id&matriz[idTipo]=$registro";
				$fnt="<font size='2'>";
				
				$opcoes =htmlMontaOpcao($def."&acao=excluirCaracteristica>".$fnt."Excluir</font></a>",'excluir');
				
				$i=0;
				$campo[$i++]=resultadoSQL($consulta, $reg, 'nome');
				$campo[$i++]=resultadoSQL($consulta, $reg, 'valor');
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


# 
function equiptoCaracteristicaEquipamentoLista($modulo, $sub, $acao, $registro, $matriz) {
	
	global $conn, $corFundo, $corBorda, $html, $sessLogin, $tb, $titulo;
	
	$exclusoes=array();
	
	$consulta=getCaracteristicaEquipamento($registro);
	$qtd=contaConsulta($consulta);
	#pega os id existentes pra nao constarem no combo
	if ($consulta && contaConsulta($consulta)>0) {
		for($reg=0;$reg<$qtd;$reg++) {
			$idCaracteristica=resultadoSQL($consulta, $reg, 'idCaracteristica');
			$exclusoes[]=$idCaracteristica;
		}
	} 
	# se nao tiver caract busca no tipo/caracteristica, pra entrarem como default
	else {
		
		$dados=dadosequipamento($registro);
		$matriz[idTipo]=$dados[idTipo];
		$cnst=getCaracteristicaTipo($matriz);
		
		if ($cnst && contaConsulta($cnst)>0) {	
			
			for($reg=0;$reg<contaConsulta($cnst);$reg++) {
				
				$idCaracteristica=resultadoSQL($cnst, $reg, 'idCaracteristica');
				$valor=resultadoSQL($cnst, $reg, 'valor');
				$exclusoes[]=$idCaracteristica;
				
				$sql="insert into $tb[EquipamentoEquiptoCaracteristica] 
			    		   values('', 
								  '$registro', 
						  		  '$idCaracteristica', 
						  		  '$valor')";
				$ok=consultaSQL($sql, $conn);
			}
			$consulta=getCaracteristicaEquipamento($registro);
		}
	}
	
	# Cabeçalho
	novaTabela("Característica do Equipamento", "left", '100%', 0, 4, 1, $corFundo, $corBorda, 4);
		
		#Prepara o form
		novaLinhaTabela($corFundo, '100%');
		$texto="<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=registro value=$registro>
			<input type=hidden name=acao value=incluirCaracteristicaTipo>
			<input type=hidden name=subacao value=alterar>Utilize a linha abaixo para incluir novas características:";
			itemLinhaNOURL($texto, 'left', $corFundo, 3, 'tabfundo1');
		fechaLinhaTabela();
		
		$nome=formSelectEquiptoCaracteristica('id', 'nome', 'matriz[idCaracteristica]', 'formnochange', $exclusoes);
		$valor="<input type=text size=40 name=matriz[valor] value=''>";
		$botao="<input type=submit name=btnIncluir value='Incluir' class=submit>";
		
		$i=0;
		htmlAbreLinha($corFundo);
			itemLinhaTMNOURL($nome, $gravata[alinhamento][$i].' normal10', 'middle', $largura[$i++], $corFundo, 0, 'normal10');
			itemLinhaTMNOURL($valor, $gravata[alinhamento][$i].' normal10', 'middle', $largura[$i++], $corFundo, 0, 'normal10');
			itemLinhaTMNOURL($botao, $gravata[alinhamento][$i].' normal10', 'middle', $largura[$i++], $corFundo, 0, 'normal10');
		htmlFechaLinha();
		
		$largura             =array('25%',  '60%',      '15%');
		$gravata[cabecalho]  =array('Nome', 'Conteúdo', 'Opções');
		$gravata[alinhamento]=array('left', 'Left',     'left');
		
		htmlAbreLinha($corFundo);
			for($i=0;$i<count($largura); $i++)
				itemLinhaTMNOURL($gravata[cabecalho][$i], $gravata[alinhamento][$i], 'middle', $largura[$i], $corFundo, 0, 'tabfundo0');
		htmlFechaLinha();
		
		
		# Exibe a lista de caracteristicas existentes
		if ($consulta && contaConsulta($consulta)>0) {
		
			for($reg=0;$reg<$qtd;$reg++) {
				
				$id=resultadoSQL($consulta, $reg, 'id');
				
				#opcoes
				$def="<a href=?modulo=$modulo&sub=$sub&registro=$id&matriz[idEquipamento]=$registro";
				$fnt="<font size='2'>";
				
				$opcoes =htmlMontaOpcao($def."&acao=excluirCaracteristica>".$fnt."Excluir</font></a>",'excluir');
				
				$i=0;
				$campo[$i++]=resultadoSQL($consulta, $reg, 'nome');
				$campo[$i++]=resultadoSQL($consulta, $reg, 'valor');
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

function getCaracteristicaTipo($matriz) {
	global $conn, $corFundo, $corBorda, $html, $sessLogin, $tb, $titulo;
	
	$sql = "SELECT  $tb[EquiptoTipoEquiptoCaracteristica].id as id,
					$tb[EquiptoTipo].id idTipo,
					$tb[EquiptoCaracteristica].id idCaracteristica,
					$tb[EquiptoTipoEquiptoCaracteristica].id idTipoCaracteristica,
					$tb[EquiptoCaracteristica].nome nome,
					$tb[EquiptoCaracteristica].descricao descricao,
					$tb[EquiptoTipoEquiptoCaracteristica].valor valor
			FROM	$tb[EquiptoTipo],
					$tb[EquiptoCaracteristica],
					$tb[EquiptoTipoEquiptoCaracteristica]
			WHERE	$tb[EquiptoTipo].id = $tb[EquiptoTipoEquiptoCaracteristica].idEquiptoTipo
					AND $tb[EquiptoCaracteristica].id = $tb[EquiptoTipoEquiptoCaracteristica].idEquiptoCaracteristica
					AND $tb[EquiptoTipo].id = $matriz[idTipo]
		ORDER BY	$tb[EquiptoCaracteristica].nome
	";
	return consultaSQL($sql, $conn);
}

function getCaracteristicaEquipamento($registro) {
	global $conn, $corFundo, $corBorda, $html, $sessLogin, $tb, $titulo;
	#monta uma lista de servicosPlanos para o sql
	$sql = "SELECT  $tb[EquipamentoEquiptoCaracteristica].id as id,
					$tb[Equipamento].id idEquipamento,
					$tb[EquiptoCaracteristica].id idCaracteristica,
					$tb[EquiptoCaracteristica].nome nome,
					$tb[EquiptoCaracteristica].descricao descricao,
					$tb[EquipamentoEquiptoCaracteristica].valor valor
			FROM	$tb[Equipamento],
					$tb[EquiptoCaracteristica],
					$tb[EquipamentoEquiptoCaracteristica]
			WHERE	$tb[Equipamento].id = $tb[EquipamentoEquiptoCaracteristica].idEquipamento
					AND $tb[EquiptoCaracteristica].id = $tb[EquipamentoEquiptoCaracteristica].idEquiptoCaracteristica
					AND $tb[Equipamento].id = $registro
		ORDER BY	$tb[EquiptoCaracteristica].nome
	";
	
	return consultaSQL($sql, $conn);
}
?>