<?
################################################################################
#       Criado por: Hugo Ribeiro
#  Data de criação: 12/05/2004
# Ultima alteração: 12/05/2004
#    Alteração No.: 001
#
# Função:
#    Painel - Funções para gerenciamento dos produtos


# abertura do modulo
function produto($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessCadastro, $titulo;
	
	$titulo = "Produtos";
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[adicionar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função!";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		# Topo da tabela - Informações e menu principal do Cadastro
		novaTabela2("[$titulo]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 4);
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
					echo "<br><img src=".$html[imagem][cadastro]." border=0 align=left><b class=bold>$tipoPessoa[descricao]</b>
					<br><span class=normal10>$titulo</b>.</span>";
				htmlFechaColuna();			
				$texto=htmlMontaOpcao("<br>Adicionar", 'incluir');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=adicionar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Procurar", 'procurar');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=procurar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Listar", 'listar');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=listar", 'center', $corFundo, 0, 'normal');
			fechaLinhaTabela();
		fechaTabela();
		
		if($acao=="adicionar") {
			echo "<br>";
			produtoAdicionar($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($acao=="alterar") {
			echo "<br>";
			produtoAlterar($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($acao=='procurar') {
			echo "<br>";
			produtoProcurar($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($acao=='ver') {
			echo "<br>";
			produtoVer($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($acao=='excluir') {
			echo "<br>";
			produtoExcluir($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($acao=='listar') {
			echo "<br>";
			produtoListar($modulo, $sub, $acao, $registro, $matriz);
		}
		/*
		elseif($acao=='imprimir') {
			echo "<br>";
			relatorioOrdemServico($modulo, 'ordemservico', $acao, $registro, $matriz);
		}
		*/
	}
	echo "<script>location.href='#ancora';</script>";
}

function 	produtoExcluir($modulo, $sub, $acao, $registro, $matriz) {
	/*$msg="Esta opção não está habilitada";
	avisoNOURL("Aviso: Exclusão", $msg, 400);
	echo "<br>";
	produtoVer($modulo, $sub, 'ver', $registro, $matriz);*/
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $tb, $sessCadastro, $sessLogin, $titulo;
	
	if($registro && !$matriz[bntExcluir]) {
	
		# Buscar Valores
		$dados=dadosProduto($registro);
			
		# Motrar tabela de busca
		novaTabela2("[Excluir]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			menuOpcAdicional($modulo, $sub, $acao, $registro);
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=matriz[id] value=$registro>
				<input type=hidden name=acao value=$acao>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			//================================================================================
			//maodeobraMostra($dados);
			$bgLabel='tabfundo1';
			$bgCampo='tabfundo1';
			
			#nome
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Nome: </b>', 'right', 'middle', '30%', $corFundo, 0, $bgLabel);
				itemLinhaTMNOURL($dados[nome], 'left', 'middle', '30%', $corFundo, 0, $bgCampo);
			fechaLinhaTabela();
							
			#Usuario
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Descrição: </b>', 'right', 'middle', '30%', $corFundo, 0, $bgLabel);
				itemLinhaTMNOURL($dados[descricao], 'left', 'middle', '30%', $corFundo, 0, $bgCampo);
			fechaLinhaTabela();
			
			#Valor
			if ($dados[valor]) {
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Valor: </b>', 'right', 'middle', '30%', $corFundo, 0, $bgLabel);
					itemLinhaTMNOURL($dados[valor], 'left', 'middle', '30%', $corFundo, 0, $bgCampo);
				fechaLinhaTabela();
			}
			//=================================================================================
			#botao
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntExcluir] value='Excluir' class=submit>";
				itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		fechaTabela();
	} #fecha form
	
	# Exclusão - bntExcluir pressionado
	elseif($matriz[bntExcluir]) {
		# Conferir campos
		if($matriz[id]) {
			# continuar
			# Cadastrar em banco de dados
			$grava=dbProduto($matriz, 'excluir');
			
			# Verificar exclusão de registro
			if($grava) {
				# OK
				# Visualizar Pessoa
				$msg="Registro Excluído!";
				avisoNOURL("Aviso: ", $msg, 400);
				echo "<br>";
				maodeobraVer($modulo, $sub, 'ver', "$matriz[id]", $matriz);
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
	} #fecha bntExcluir
}


function produtoProcurar($modulo, $sub, $acao, $registro, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessCadastro, $titulo, $tb;
	
	if( !$matriz[bntProcurar] ) {

		novaTabela2("[Procurar $titulo]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro>";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL("<b>Nome: </b>", 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[nome] size=60 value=''>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			#botao
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntProcurar] value='Procurar' class=submit>";
				itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
		fechaTabela();
	} else {
		# realizar consulta
		$lista=buscaRegistros($matriz[nome], 'nome', 'contem', 'nome', $tb[Produto]);
		produtoListar($modulo, $sub, 'ver', $lista, $matriz);
	}
}

# função para adicionar
function produtoAdicionar($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro;
	
	if(!$matriz[bntAdicionar]) {
		
		# Motrar tabela de busca
		novaTabela2("[Adicionar]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			#menuOpcAdicional($modulo, $sub, $acao, $registro);
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=matriz[bntConfirmar] value=$matriz[bntConfirmar]>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			$dados=dadosProduto(0);
			
			produtoMostra($dados);
			
			#botao
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntAdicionar] value='Adicionar' class=submit>";
				itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		fechaTabela();	
	}
	else {
		
		//$matriz[id]=buscaNovoID($tb[Produto]);
		$grava=dbProduto($matriz, 'incluir');
		
		# Verificar inclusão de registro
		echo "<br>";
		if($grava) {
			# Visualizar Pessoa
			$msg="Registro gravado com sucesso!";
			avisoNOURL("Aviso: ", $msg, 400);
			echo "<br>";
			produtoListar($modulo, $sub, 'listar', $matriz[id], $matriz);
		} 
		else {
			$msg="Ocorreram erros durante a gravação.";
			avisoNOURL("Aviso: Ocorrência de erro", $msg, 400);
		}
	}
}



# Função para alteração de dados da Pessoa - apenas cadastro
function produtoAlterar($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $tb, $sessCadastro;
	
	if($registro && !$matriz[bntConfirmar]) {
	
		# Buscar Valores
		$dados=dadosProduto($registro, 'id', 'igual', 'id');
			
		# Motrar tabela de busca
		novaTabela2("[Alterar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			menuOpcAdicional($modulo, $sub, $acao, $registro);
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=matriz[id] value=$registro>
				<input type=hidden name=acao value=$acao>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			produtoMostra($dados);
			
			#botao
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntConfirmar] value='Alterar' class=submit>";
				itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
		fechaTabela();
	} #fecha form
	
	# Alteração - bntAlterar pressionado
	elseif($matriz[bntConfirmar]) {
		# Conferir campos
		if($matriz[id]) {
			# continuar
			# Cadastrar em banco de dados
			$grava=dbProduto($matriz, 'alterar');
			
			# Verificar inclusão de registro
			if($grava) {
				# OK
				# Visualizar Pessoa
				$msg="Registro alterado com sucesso!";
				avisoNOURL("Aviso: ", $msg, 400);
				echo "<br>";
				produtoVer($modulo, $sub, 'ver', "$matriz[id]", $matriz);
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


function produtoMostra($dados) {
	#nome
	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL("<b>Nome: </b>", 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		$texto="<input type=text name=matriz[nome] size=60 value='$dados[nome]'>";
		itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
	#descricao
	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL('<b>Descrição: </b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		$texto="<input type=text name=matriz[descricao] size=40 value='$dados[descricao]'>";
		itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
	# Valor
	$valor=formatarValoresForm($dados[valor]);
	novaLinhaTabela($corFundo, '100%');
		itemLinhaTMNOURL('<b>Valor: </b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
		$texto="<input type=text name=matriz[valor] size=40 value='$valor' onBlur=formataValor(this.value,6)>";
		itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
	
}


# Função para buscar o NOVO ID da Pessoa
function produtoBuscaIDNovo() {

	global $conn, $tb;
	
	$sql="SELECT count(id) qtde from $tb[Produto]";
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && resultadoSQL($consulta, 0, 'qtde')>0) {
	
		$sql="SELECT MAX(id)+1 id from $tb[Produto]";
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
function produtoListar($modulo, $sub, $acao, $lista, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb, $titulo;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		
		# Cabeçalho
		novaTabela("Lista de ".$titulo, "left", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
			# Opcoes Adicionais
			#menuOpcAdicional($modulo, $sub, $acao, $cliente);
		fechaTabela();
		
		
		novaTabelaSH('left', '100%', 0, 2, 1, $corFundo, $corBorda, 6);
		
		if (!$lista)
			$consulta=buscaRegistros('', '', 'todos', 'nome', $tb[Produto]);
		else 
			$consulta=$lista;

		if ($consulta && contaConsulta($consulta)>0) {
			
			$largura             =array('20%',  '40%',       '10%',   '10%',  '20%');
			$gravata[cabecalho]  =array('Nome', 'Descrição', 'Valor', 'Data', 'Opções');
			$gravata[alinhamento]=array('left', 'left',      'right', 'left', 'left');
			
			$cor='tabfundo0';
			htmlAbreLinha($corFundo);
				for($i=0;$i<count($largura); $i++)
					itemLinhaTMNOURL($gravata[cabecalho][$i], $gravata[alinhamento][$i], 'middle', $largura[$i], $corFundo, 0, $cor);
			htmlFechaLinha();
			
			$qtd=contaConsulta($consulta);
			for($reg=0;$reg<$qtd;$reg++) {
				
				$id=resultadoSQL($consulta, $reg, 'id');
				$Usuario=resultadoSQL($consulta, $reg, 'idUsuario');
				if ($usuario) $usuario=buscaLoginUsuario($usuario,'id','igual', 'id');
				
				#opcoes
				$def="<a href=?modulo=$modulo&sub=$sub&registro=$id";
				$fnt="<font size='2'>";
				$opcoes =htmlMontaOpcao($def."&acao=ver>".$fnt."Ver</font></a>",'ver');
				$opcoes.=htmlMontaOpcao($def."&acao=alterar>".$fnt."Alterar</font></a>",'alterar');
				$opcoes.=htmlMontaOpcao($def."&acao=excluir>".$fnt."Excluir</font></a>",'excluir');
							
				$i=0;
				$campo[$i++]=resultadoSQL($consulta, $reg, 'nome');
				$campo[$i++]=resultadoSQL($consulta, $reg, 'descricao');
				$campo[$i++]=formatarValoresForm(resultadoSQL($consulta, $reg, 'valor'));
				$campo[$i++]=converteData(resultadoSQL($consulta, $reg, 'dtCadastro'), 'banco', 'formdata');
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
				$cor='normal10';
				htmlAbreLinha($corFundo);
					itemLinhaTMNOURL( "Não há registros para sua solicitação.", 'left', 'middle', $largura[0], $corFundo, 0, $cor);
				htmlFechaLinha();
		}
		fechaTabela();
	}

}

# função Exibição
function produtoVer($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb, $titulo;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[visualizar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		
		# Procurar dados3
		$objeto=dadosProduto($registro);
		
		if(is_array($objeto)) {

			# Motrar tabela de busca
			novaTabela2("[$titulo - Visualização]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, 'ver', $registro);
				
				$bgLabel='tabfundo1';
				$bgCampo='tabfundo1';
				
				itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				
				#nome
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Titulo: </b>', 'right', 'middle', '30%', $corFundo, 0, $bgLabel);
					itemLinhaTMNOURL($objeto[nome], 'left', 'middle', '30%', $corFundo, 0, $bgCampo);
				fechaLinhaTabela();
				#descricao
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Descrição: </b>', 'right', 'middle', '30%', $corFundo, 0, $bgLabel);
					itemLinhaTMNOURL($objeto[descricao], 'left', 'middle', '30%', $corFundo, 0, $bgCampo);
				fechaLinhaTabela();
				#valor
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Valor: </b>', 'right', 'middle', '30%', $corFundo, 0, $bgLabel);
					itemLinhaTMNOURL(formatarValoresForm($objeto[valor]), 'left', 'middle', '30%', $corFundo, 0, $bgCampo);
				fechaLinhaTabela();
				
				#Usuario
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Usuário: </b>', 'right', 'middle', '30%', $corFundo, 0, $bgLabel);
					itemLinhaTMNOURL($objeto[login], 'left', 'middle', '30%', $corFundo, 0, $bgCampo);
				fechaLinhaTabela();
				
				#dtCriacao
				if ($objeto[dtCriacao]) {
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>Data da Criação: </b>', 'right', 'middle', '30%', $corFundo, 0, $bgLabel);
						itemLinhaTMNOURL($objeto[dtCriacao], 'left', 'middle', '30%', $corFundo, 0, $bgCampo);
					fechaLinhaTabela();
				}
				
			fechaTabela();
			
		}
	}
}



# Função para Dados
function dadosProduto($id) {

	global $tb;

	$consulta=buscaRegistros($id, 'id', 'igual', 'id', $tb[Produto]);
	
	if( contaConsulta($consulta)>0 ) {
		$retorno[id]=resultadoSQL($consulta, 0, 'id');
		$retorno[idUsuario]=resultadoSQL($consulta, 0, 'idUsuario');
		$retorno[dtCriacao]=resultadoSQL($consulta, 0, 'dtCadastro');
		$retorno[descricao]=resultadoSQL($consulta, 0, 'descricao');
		$retorno[nome]=resultadoSQL($consulta, 0, 'nome');
		$retorno[valor]=resultadoSQL($consulta, 0, 'valor');
		
		#extras
		$usuario='';
		if($retorno[idUsuario])	$usuario=buscaLoginUsuario($retorno[idUsuario], 'id', 'igual', 'id');
		$retorno[login]=$usuario;
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
 item  = id do produto
 campo = campo da matriz
 retorno = campo de retorno (nome do componente)
 tipo = check - retorna o nome
        form  - um combo com os nomes
        multi - uma caixa de selecao com os nomes
 
*/
function formSelectProduto($item, $campo, $retorno, $tipo) {
	
	global $conn, $tb, $corFundo, $modulo, $sub;

	$tabela=$tb[Produto];
	
	if($tipo=='check') {
	
		$campo=dadosProduto($item);
		$retorno=$campo[nome];
	
	}
	elseif(($tipo=='form') || $tipo=='formnochange') {
	
		$consulta=buscaRegistros('status="A"','status','custom','nome', $tabela);
		
		if($consulta && contaConsulta($consulta)>0) {
			
			if ($tipo=='formnochange') $retorno="<select name=$retorno>";
			else $retorno="<select name=$retorno onChange=javascript:submit();>";
			
			for($i=0;$i<contaConsulta($consulta);$i++) {
			
				$id=resultadoSQL($consulta, $i, 'id');
				$nome=resultadoSQL($consulta, $i, 'nome');
				
				if($item==$id) $opcSelect='selected';
				else $opcSelect='';
				
				$retorno.="\n<option value=$id $opcSelect>$nome";
			}
			$retorno.="</select>";
		}
	}
	
	elseif($tipo=='multi') {
	
		$consulta=buscaRegistros('','','todos','nome', $tabela);
		
		if($consulta && contaConsulta($consulta)>0) {
			
			$retorno="<select multiple size=6 name=matriz[$campo][]>";
			
			for($i=0;$i<contaConsulta($consulta);$i++) {
				
				$id=resultadoSQL($consulta, $i, 'id');
				$nome=resultadoSQL($consulta, $i, 'nome');
				
				if($item==$id) $opcSelect='selected';
				else $opcSelect='';
				
				$retorno.="\n<option value=$id $opcSelect>nome";
				
			}
			$retorno.="</select>";
		}
	}
	return($retorno);
}


#Função de banco de dados
function dbProduto($matriz, $tipo) {

	global $sessLogin, $sessCadastro, $conn, $tb, $modulo, $sub, $acao;
	
	$tabela=$tb[Produto];
	$matriz[idUsuario]=buscaIDUsuario($sessLogin[login], 'login', 'igual', 'id');
		
	/* Cria uma matriz com os campos ja formatados para o SQL */
	$campos[id]="id=$matriz[id]";
	$campos[nome]="nome='$matriz[nome]'";
	$campos[descricao]="descricao='$matriz[descricao]'";
	$campos[valor]="valor=".formatarValores($matriz[valor]);
	$campos[idUsuario]="idUsuario=$matriz[idUsuario]";
	
	# Sql de inclusão
	if($tipo=='incluir') {
		
		$campos[id]='id='.buscaNovoID($tabela);
		
		$sql="INSERT INTO $tabela 
		             SET $campos[nome],
						 $campos[descricao],
						 $campos[valor],
						 $campos[idUsuario],
						 dtCadastro=now()";
		
	} #fecha inclusao
	
	# Alterar
	elseif($tipo=='alterar') {
		
		$sql="
			UPDATE $tabela 
			SET
				$campos[nome],
				$campos[descricao],
				$campos[valor],
				$campos[idUsuario]
			WHERE
				$campos[id]";
	}
	
	elseif($tipo=='excluir') {
		$sql="UPDATE $tabela SET status ='I' WHERE $campos[id]";
	}
	
	#echo "SQL: $sql";
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		

		return($retorno); 
	}
}

?>