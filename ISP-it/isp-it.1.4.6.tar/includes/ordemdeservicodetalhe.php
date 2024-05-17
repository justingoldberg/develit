<?
################################################################################
#       Criado por: Hugo Ribeiro
#  Data de criação: 28/04/2004
# Ultima alteração: 28/04/2004
#    Alteração No.: 001
#
# Função:
#    Painel - Funções para gerenciamento dos detalhes na ordem de servicos


function ordemdeservicodetalheAdicionar($modulo, $sub, $acao, $registro, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb;
	
	if(!$matriz[bntAdicionarDetalhe]) {
		# Cabeçalho
		//exibe a OS
		echo "<br>";
		ordemdeservicoVer($modulo, $sub, $acao, $registro, $matriz);
		
		echo "<br>";
		novaTabela2("Detalhes da OS", "left", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=matriz[idOrdemServico] value=$registro>
					<input type=hidden name=matriz[escolha] value=$matriz[escolha]>
					<input type=hidden name=registro value=$registro>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			$dados=dadosOrdemdeServicoDetalhe($registro);

			ordemdeservicodetalheMostra($dados, $matriz[escolha]);
			
			#botao
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntAdicionarDetalhe] value='Adicionar Detalhe' class=submit>";
				itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
		fechaTabela();
	}
	elseif($matriz[bntAdicionarDetalhe]) {
		
		$matriz[id]=buscaNovoID($tb[OrdemServicoDetalhe]);
		$grava=dbOrdemdeServicoDetalhe($matriz, 'incluir');
		
		# Verificar inclusão de registro
		echo "<br>";
		if($grava) {
			# Visualizar Pessoa
			$msg="Registro gravado com sucesso!";
			avisoNOURL("Aviso: ", $msg, 400);
			echo "<br>";
			ordemdeservicodetalheDetalhar($modulo, $sub, 'detalhar', $matriz[idOrdemServico], $matriz);
		} 
		else {
			$msg="Ocorreram erros durante a gravação.";
			avisoNOURL("Aviso: Ocorrência de erro", $msg, 400);
		}
	}
}



function ordemdeservicodetalheAlterar($modulo, $sub, $acao, $registro, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb;
	
	if(!$matriz[bntAlterarDetalhe]) {
		# Cabeçalho
		//exibe a OS
		echo "<br>";
		ordemdeservicoVer($modulo, $sub, $acao, $matriz[idOrdemServico], $matriz);
		
		echo "<br>";
		novaTabela2("Detalhes da OS", "left", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=matriz[escolha] value=$matriz[escolha]>
					<input type=hidden name=matriz[idOrdemServico] value=$matriz[idOrdemServico]>
					<input type=hidden name=registro value=$registro>&nbsp;";
					
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			$dados=dadosOrdemdeServicoDetalhe($registro);
			
			if($dados[idProduto]>0) $matriz[escolha]=1;
			elseif ($dados[idMaodeObra]>0) $matriz[escolha]=2;
			else $matriz[escolha]=0;

			ordemdeservicodetalheMostra($dados, $matriz[escolha]);
			
			#botao
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntAlterarDetalhe] value='Alterar Detalhe' class=submit>";
				itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
		fechaTabela();
	}
	elseif($matriz[bntAlterarDetalhe]) {
		
		$matriz[id]=$registro;
		$grava=dbOrdemdeServicoDetalhe($matriz, 'alterar');
		
		# Verificar inclusão de registro
		echo "<br>";
		if($grava) {
			# Visualizar Pessoa
			$msg="Registro gravado com sucesso!";
			avisoNOURL("Aviso: ", $msg, 400);
			echo "<br>";
			ordemdeservicodetalheDetalhar($modulo, $sub, 'detalhar', $matriz[idOrdemServico], $matriz);
		} 
		else {
			$msg="Ocorreram erros durante a gravação.";
			avisoNOURL("Aviso: Ocorrência de erro", $msg, 400);
		}
	}	
}



function ordemdeservicodetalheMostra($dados, $escolha) {
	
	# Escolha
	if ($escolha==0) {
		
		$combo="<select name=matriz[escolha] onChange=javascript:submit();>";
		$combo.="\n<option value=0 >Selecione";
		
		if($dados[idProduto]>0) $combo.="\n<option value=1 selected>Produto";
		else $combo.="\n<option value=1>Produto";
		
		if($dados[idMaodeObra]>0) $combo.="\n<option value=2 selected>MaodeObra";
		else $combo.="\n<option value=2>MaodeObra";
		
		$combo.="</select>";
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('Tipo: ', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm($combo, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		
	} else {
		
		if ($escolha==1) {
			# Produto
			$outro ="<input type=hidden name=matriz[idMaodeObra] value='0'>";
			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('Produto: ', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
				$texto=formSelectProduto($dados[idProduto], 'id', 'matriz[idProduto]', 'formnochange');
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		}
		else if($escolha==2) {
			# maodeObra
			$outro ="<input type=hidden name=matriz[idProduto] value='0'>";
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('Serviço: ', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
				$texto=formSelectMaodeObra($dados[idMaodeObra], 'id', 'matriz[idMaodeObra]', 'formnochange');
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		}
		
		#Quantidade
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('Quantidade: ', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
			$valor=formatarValoresForm($dados[quantidade]);
			$texto="<input type=text name=matriz[quantidade] size=15 value='$valor' onBlur=formataValor(this.value,7)>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		# Unitário
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('unitário: ', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
			$valor=formatarValoresForm($dados[valor]);
			$texto="<input type=text name=matriz[valor] size=15 value='$valor' onBlur=formataValor(this.value,8)>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		# Aplicação
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('Aplicação: ', 'right', 'middle', '40%', $corFundo, 0, 'tabfundo1');
			$texto=formSelectAplicacao($dados[idAplicacao], 'id', 'matriz[idAplicacao]', 'formnochange');
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		
		#item hidden
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL($outro, 'right', 'middle', '40%', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
	}	
}


function ordemdeservicodetalheDetalhar($modulo, $sub, $acao, $registro, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb;

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
		//exibe a OS
		ordemdeservicoVer($modulo, $sub, $acao, $registro, $matriz);
		
		echo "<br>";
		novaTabela("Detalhes da OS", "left", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
			# Opcoes Adicionais
			# menuOpcAdicional($modulo, $sub, $acao, $registro);
		fechaTabela();
		novaTabelaSH("left", '100%', 0, 2, 1, $corFundo, $corBorda, 6);

		$consulta=buscaRegistros($registro, 'idOrdemServico', 'igual', 'id', $tb[OrdemServicoDetalhe]);
		$total=0;
		
		if ($consulta && contaConsulta($consulta)>0) {
			
			$largura             =array('25%',  '10%',    '10%',   '10%',   '15%',       '30%');
			$gravata[cabecalho]  =array('Tipo', 'Quant.', 'Unit.', 'Valor', 'Aplicação', 'Opções');
			$gravata[alinhamento]=array('left', 'right',  'right', 'right', 'left',      'left');
			
			$cor='tabfundo0';
			htmlAbreLinha($corFundo);
				for($i=0;$i< count($largura); $i++)
					itemLinhaTMNOURL($gravata[cabecalho][$i], $gravata[alinhamento][$i], 'middle', $largura[$i], $corFundo, 0, $cor);
			htmlFechaLinha();
			
			$qtd=contaConsulta($consulta);
			
			for($reg=0;$reg<$qtd;$reg++) {
				#Busca o registro com todos os campos

				$dados=dadosOrdemdeServicoDetalhe(resultadoSQL($consulta, $reg, 'id'));
				$idProduto=$dados[idProduto];
				$idMaodeObra=$dados[idMaodeObra];
				$valortt=$dados[quantidade]*$dados[valor];
				$total=$total+$valortt;
				$i=0;
				
				//$campo[$i++]=$dados[id];
				
				if($idProduto) $campo[$i++]=$dados[nomeProduto];
				elseif($idMaodeObra) $campo[$i++]=$dados[nomeMaodeObra];
				else $campo[$i++]='NA';
					
				$campo[$i++]=formatarValoresForm($dados[quantidade]);
				$campo[$i++]=formatarValoresForm($dados[valor]);
				$campo[$i++]=formatarValoresForm($valortt);
				$campo[$i++]=$dados[nomeAplicacao];
				
				#opcoes
				$def="<a href=?modulo=$modulo&sub=$sub&matriz[idOrdemServico]=$registro&registro=$dados[id]&";
				$opcoes =htmlMontaOpcao($def."acao=alterarDetalhe>Alterar</a>",'alterar');
				$opcoes.=htmlMontaOpcao($def."acao=excluirDetalhe>Excluir</a>",'excluir');
				
				$i=0;
				$cor='normal10';
				htmlAbreLinha($corFundo);
					for($i=0;$i< count($largura)-1; $i++)
						itemLinhaTMNOURL($campo[$i], $gravata[alinhamento][$i], 'middle', $largura[$i], $corFundo, 0, $cor);
					itemLinhaTMNOURL($opcoes, 'left', 'middle', $largura[$i], $corFundo, 0, $cor);
				htmlFechaLinha();
			}

			htmlAbreLinha($corFundo);
				itemLinhaTMNOURL('<b>Valor Total</b>', 'right', 'middle', $largura[1], $corFundo, 2, 'tabfundo1');
				itemLinhaTMNOURL(formatarValoresForm($total), 'right', 'middle', $largura[3], $corFundo, 2, 'txtAviso');
				itemLinhaTMNOURL('&nbsp;', 'left', 'middle', $largura[5], $corFundo, 2, 'tabfundo1');
			htmlFechaLinha();
			
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


function ordemdeservicodetalheExcluir($modulo, $sub, $acao, $registro, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $tb;
	
	if(!$matriz[bntExcluirDetalhe]) {
		
		//exibe a OS
		echo "<br>";
		ordemdeservicoVer($modulo, $sub, $acao, $matriz[idOrdemServico], $matriz);
		
		echo "<br>";
		novaTabela2("Detalhes da OS", "left", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=matriz[idOrdemServico] value=$matriz[idOrdemServico]>
					<input type=hidden name=registro value=$registro>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			$dados=dadosOrdemdeServicoDetalhe($registro);
			ordemdeservicodetalheMostra($dados, $matriz[escolha]);
			
			#botao
			novaLinhaTabela($corFundo, '100%');
				$texto="<input type=submit name=matriz[bntExcluirDetalhe] value='Excluir Detalhe' class=submit>";
				itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
		fechaTabela();
	}
	elseif($matriz[bntExcluirDetalhe]) {
		
		$matriz[id]=$registro;
		$grava=dbOrdemdeServicoDetalhe($matriz, 'excluir');
		
		# Verificar inclusão de registro
		echo "<br>";
		if($grava) {
			# Visualizar Pessoa
			$msg="Registro excluido.";
			avisoNOURL("Aviso: ", $msg, 400);
			echo "<br>";
			ordemdeservicodetalheDetalhar($modulo, $sub, 'detalhar', $matriz[idOrdemServico], $matriz);
		} 
		else {
			$msg="Ocorreram erros durante a exclusão.";
			avisoNOURL("Aviso: Ocorrência de erro", $msg, 400);
		}
	}
	
}


# Função de banco de dados - Pessoas
function dbOrdemdeServicoDetalhe($matriz, $tipo) {

	global $sessLogin, $sessCadastro, $conn, $tb, $modulo, $sub, $acao;
	
	$tabela=$tb[OrdemServicoDetalhe];
	
	$matriz[idUsuario]=buscaIDUsuario($sessLogin[login], 'login', 'igual', 'id');
	$quantidade=formatarValores($matriz[quantidade]);
	$valor=formatarValores($matriz[valor]);
	
	/* Cria uma matriz com os campos ja formatados para o SQL */
	$campos[id]="id=$matriz[id]";
	$campos[idOrdemServico]="idOrdemServico=$matriz[idOrdemServico]";
	$campos[idProduto]="idProduto=$matriz[idProduto]";
	$campos[idMaodeObra]="idMaodeObra=$matriz[idMaodeObra]";
	$campos[idAplicacao]="idAplicacao=$matriz[idAplicacao]";
	$campos[idUsuario]="idUsuario=$matriz[idUsuario]";
	$campos[quantidade]="quantidade=$quantidade";
	$campos[valor]="valor=$valor";
	
	# Sql de inclusão
	if($tipo=='incluir') {

		$sql="INSERT INTO $tabela 
				VALUES ( 0, 
		                 '$matriz[idOrdemServico]',
						 '$matriz[idProduto]',
						 '$matriz[idMaodeObra]',
						 '$matriz[idUsuario]',
						 '$quantidade',
						 '$valor',
						 '$matriz[idAplicacao]',
						 dtCadastro=now())";
		
	} #fecha inclusao
	
	# Alterar
	elseif($tipo=='alterar') {
		
		$sql="
			UPDATE $tabela 
			SET
				$campos[idOrdemServico],
				$campos[idProduto],
				$campos[idMaodeObra],
				$campos[idAplicacao],
				$campos[idUsuario],
				$campos[quantidade],
				$campos[valor]
			WHERE
				$campos[id]";
	}
	
	elseif($tipo=='excluir') {
		$sql="DELETE 
				FROM $tabela 
			   WHERE $campos[id]";
	}
	
	#echo "SQL: $sql";
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
}


                             
	
# Função para Dados Pessoas Tipos
function dadosOrdemdeServicoDetalhe($id) {

	global $tb;
	
	$consulta=buscaRegistros($id, 'id', 'igual', 'id', $tb[OrdemServicoDetalhe]);
	
	if(contaConsulta($consulta)>0) {
		
		$retorno[id]=resultadoSQL($consulta, 0, 'id');
		$retorno[idOrdemServico]=resultadoSQL($consulta, 0, 'idOrdemServico');
		$retorno[idProduto]=resultadoSQL($consulta, 0, 'idProduto');
		$retorno[idMaodeObra]=resultadoSQL($consulta, 0, 'idMaodeObra');
		$retorno[idAplicacao]=resultadoSQL($consulta, 0, 'idAplicacao');
		$retorno[idUsuario]=resultadoSQL($consulta, 0, 'idUsuario');
		$retorno[quantidade]=resultadoSQL($consulta, 0, 'quantidade');
		$retorno[valor]=resultadoSQL($consulta, 0, 'valor');
		$retorno[dtCadastro]=resultadoSQL($consulta, 0, 'dtCadastro');
		
		#Login
		if($retorno[idUsuario]) {
			$us=buscaLoginUsuario($retorno[idUsuario], 'id', 'igual', 'id');
		}
		$retorno[login]=$us;
		
		#pega o nome do produto
		if($retorno[idProduto]) {
			$pp=dadosProduto($retorno[idProduto]);
		}
		$retorno[nomeProduto]=$pp[descricao];
		
		#pega o nome da maodeobra
		if($retorno[idMaodeObra]) {
			$pm=dadosMaodeObra($retorno[idMaodeObra]);
		}
		$retorno[nomeMaodeObra]=$pm[descricao];
		
		#pega o nome da aplicacao
		if($retorno[idAplicacao]) {
			$pm=dadosAplicacao($retorno[idAplicacao]);
		}
		$retorno[nomeAplicacao]=$pm[descricao];

	}
	
	return($retorno);
}

/*

Criação da Tabela ORDEMSERVICODETALHE:

	CREATE TABLE OrdemServicoDetalhe (
			id BIGINT(20) NOT NULL AUTO_INCREMENT, 
			idOrdemServico BIGINT(20) DEFAULT 0, 
			idProduto BIGINT(20) DEFAULT 0, 
			idMaodeObra BIGINT(20) DEFAULT 0, 
			idUsuario BIGINT(20) DEFAULT 0, 
			quantidade DOUBLE DEFAULT 0, 
			valor DOUBLE DEFAULT 0, 
			idAplicacao BIGINT(20) DEFAULT 0, 
			dtCadastro DATETIME, 
			PRIMARY KEY(id)
	);


	
	CREATE TABLE Produtos (
			id BIGINT(20) DEFAULT 0 NOT NULL AUTO_INCREMENT, 
			descricao VARCHAR(100) DEFAULT '', 
			idUsuario BIGINT(20) DEFAULT 0,
			dtCadastro DATETIME,
			PRIMARY KEY(id)
	);

	CREATE TABLE MaodeObra (
			ID bigint(20) DEFAULT 0 NOT NULL AUTO_INCREMENT, 
			descricao VARCHAR(100) DEFAULT '', 
			idUsuario BIGINT(20) DEFAULT 0,
			dtCadastro DATETIME,
			PRIMARY KEY(id)
	);

	CREATE TABLE Aplicacao (
			id BIGINT(20) DEFAULT 0 NOT NULL AUTO_INCREMENT, 
			descricao VARCHAR(100) DEFAULT '', 
			idUsuario BIGINT(20) DEFAULT 0,
			dtCadastro DATETIME,
			PRIMARY KEY(id)
	);

*/

?>