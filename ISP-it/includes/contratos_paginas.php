<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 10/03/2004
# Ultima alteração: 20/03/2004
#    Alteração No.: 003
#
# Função:
#    Painel - Funções para controle de usuarios radius por pessoas
# 

# Função para manutenção de banco de dados
function dbPaginaContrato($matriz, $tipo) {

	global $tb, $conn;
	
	$data=dataSistema();

	if($tipo=='incluir') {
		$sql="
			INSERT INTO
				$tb[ContratosPaginas]
			VALUES (
				0,
				'$matriz[idContrato]',
				'$matriz[nome]',
				'$matriz[numero]',
				'$matriz[descricao]',
				'$matriz[conteudo]',
				'$data[dataBanco]',
				'$matriz[status]'
			)
		";
	}
	
	elseif($tipo=='excluir') {
		$sql="DELETE FROM $tb[ContratosPaginas] where id='$matriz[id]'";
	}
	
	elseif($tipo=='excluircontrato') {
		$sql="DELETE FROM $tb[ContratosPaginas] where idContrato='$matriz[id]'";
	}
	
	elseif($tipo=='alterar') {
		$sql="
			UPDATE 
				$tb[ContratosPaginas] 
			SET
				nomePagina='$matriz[nome]',
				numeroPagina='$matriz[numero]',
				descricao='$matriz[descricao]',
				conteudo='$matriz[conteudo]',
				status='$matriz[status]'
			WHERE
				id='$matriz[id]'
		";
		
	}
	
	if($sql) {
		$grava=consultaSQL($sql, $conn);
	}
	
	return($grava);
}




# Função para busca de Contas por PessoaTipo
function buscaPaginasContratos($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[ContratosPaginas] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[ContratosPaginas] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[ContratosPaginas] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[ContratosPaginas] WHERE $texto ORDER BY $ordem";
	}
	
	# Verifica consulta
	if($sql){
		$consulta=consultaSQL($sql, $conn);
		# Retornvar consulta
		return($consulta);
	}
	else {	
		# Mensagem de aviso
		$msg="Consulta não pode ser realizada por falta de parâmetros";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
	}
	
} # fecha função de busca de grupos




# Listar parametros do domínio
function listarPaginasContratos($modulo, $sub, $acao, $registro, $matriz) {

	global $html, $tb, $conn, $corFundo, $corBorda;

	if($acao=='paginas') {
		verContratos($modulo, $sub, $acao, $registro, $matriz);
		echo "<br>";
	}
	
	$consulta=buscaPaginasContratos($registro, 'idContrato','igual','numeroPagina');
	
	# Selecionar parametros do dominio
	novaTabela("Páginas do Contrato", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
	
		if($acao=='paginas' || $acao=='listar') {
			$opcAdicionar=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=paginasadicionar&registro=$registro>Adicionar Páginas</a>",'incluir');
			$opcAdicionar.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar Contratos</a>",'contrato');
			itemTabelaNOURL($opcAdicionar, 'right', $corFundo, 5, 'tabfundo1');
		}
		
		if($consulta && contaConsulta($consulta)>0) {
			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela("Nome da Página", 'center', '30%', 'tabfundo0');
				itemLinhaTabela('Número', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Data Cadastro', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Status', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Opções', 'center', '40%', 'tabfundo0');
			fechaLinhaTabela();
		
			for($a=0;$a<contaConsulta($consulta);$a++) {
			
				$id=resultadoSQL($consulta, $a, 'id');
				$idContrato=resultadoSQL($consulta, $a, 'idContrato');
				$nome=resultadoSQL($consulta, $a, 'nomePagina');
				$numero=resultadoSQL($consulta, $a, 'numeroPagina');
				$dtCadastro=resultadoSQL($consulta, $a, 'dtCadastro');
				$status=resultadoSQL($consulta, $a, 'status');
				
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=paginasalterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.=htmlMontaOpcao("&nbsp;<a href=?modulo=$modulo&sub=$sub&acao=paginasexcluir&registro=$id>Excluir</a>",'excluir');
				$opcoes.=htmlMontaOpcao("&nbsp;<a href=?modulo=$modulo&sub=$sub&acao=paginasver&registro=$id>Visualizar</a>",'ver');
				$opcoes.=htmlMontaOpcao("&nbsp;<a href=?modulo=$modulo&sub=$sub&acao=paginaspdf&registro=$id>PDF</a>",'pdf');
				#$opcoes.=htmlMontaOpcao("&nbsp;<a href=?modulo=$modulo&sub=$sub&acao=paginasps&registro=$id>Imprimir</a>",'ps');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($nome, 'left', '30%', 'normal10');
					itemLinhaTabela($numero, 'center', '10%', 'normal10');
					itemLinhaTabela(converteData($dtCadastro, 'banco','formdata'), 'center', '10%', 'normal10');
					itemLinhaTabela(formSelectStatusContratos($status,'','check'), 'center', '10%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '40%', 'normal8');
				fechaLinhaTabela();
			}
		}
		else {
			$texto="<span class=txtaviso>Não há páginas para este domínio!</span>";
			itemTabelaNOURL($texto, 'left', $corFundo, 4, 'normal10');
		}
	fechaTabela();
}




# Listar parametros do domínio
function adicionarPaginasContratos($modulo, $sub, $acao, $registro, $matriz) {

	global $html, $tb, $conn, $corFundo, $corBorda;

	verContratos($modulo, $sub, $acao, $registro, $matriz);
	echo "<br>";

	if(!$matriz[bntAdicionar] || !$matriz[nome] || !$matriz[descricao] || !$matriz[status]) {
		# Motrar tabela de busca
		novaTabela2("[Adicionar Página do Contrato]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			menuOpcAdicional($modulo, $sub, $acao, $registro);
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php enctype='multipart/form-data'>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=registro value=$registro>
				<input type=hidden name=acao value=$acao>
				&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Nome: </b><br>
					<span class=normal10>Nome para identificação da página</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[nome] size=60>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Número: </b><br>
					<span class=normal10>Número sequencial da página</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[numero] size=2>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Descrição: </b><br>
					<span class=normal10>Descrição e detalhes do conteúdo da página</span>";
				htmlFechaColuna();
				$texto="<textarea name=matriz[descricao] rows=3 cols=60></textarea>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Arquivo:</b><br>
					<span class=normal10>Selecione o arquivo a importar</span>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
					$texto="<input type=file name=arquivo>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Status: </b><br>
					<span class=normal10>Status da página</span>";
				htmlFechaColuna();
				itemLinhaForm(formSelectStatusContratos('A','status','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "&nbsp;";
				htmlFechaColuna();
				$texto="&nbsp;<input type=submit name=matriz[bntAdicionar] value=Adicionar class=submit>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		fechaTabela();
	} #fecha form
	elseif($matriz[bntAdicionar]) {
		# Conferir campos
		$tmpArquivo=$_REQUEST["arquivo"];
	
		$arquivo=addslashes((fread (fopen ($_FILES["arquivo"]["tmp_name"], "r"),
				  filesize ($_FILES["arquivo"]["tmp_name"]))));
				  
		$matriz[conteudo]=$arquivo;
		
		$matriz[idContrato]=$registro;
		$grava=dbPaginaContrato($matriz, 'incluir');
		
		if($grava) {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Registro Gravado com Sucesso!";
			avisoNOURL("Aviso", $msg, 400);				
		}
		else {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Erro ao gravar registro!";
			avisoNOURL("Aviso", $msg, 400);				
		}
		
		echo "<br>";
		listarPaginasContratos($modulo, $sub, 'paginas', $registro, $matriz);
	}
}



# Listar parametros do domínio
function excluirPaginasContratos($modulo, $sub, $acao, $registro, $matriz) {

	global $html, $tb, $conn, $corFundo, $corBorda;
	
	# Buscar informações sobre dominio
	$consulta=buscaPaginasContratos($registro, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		
		$id=resultadoSQL($consulta, 0, 'id');
		$idContrato=resultadoSQL($consulta, 0, 'idContrato');
		$nome=resultadoSQL($consulta, 0, 'nomePagina');
		$numero=resultadoSQL($consulta, 0, 'numeroPagina');
		$descricao=resultadoSQL($consulta, 0, 'descricao');
		$conteudo=resultadoSQL($consulta, 0, 'conteudo');
		$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
		$status=resultadoSQL($consulta, 0, 'status');
	
		verContratos($modulo, $sub, $acao, $idContrato, $matriz);
		echo "<br>";
	
		if(!$matriz[bntExcluir]) {
			# Motrar tabela de busca
			novaTabela2("[Excluir Página do Contrato]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, "$idContrato:$registro");	
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro>
					&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Nome: </b></span>";
					htmlFechaColuna();
					itemLinhaForm($nome, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Número: </b></span>";
					htmlFechaColuna();
					itemLinhaForm($numero, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Descrição: </b></span>";
					htmlFechaColuna();
					itemLinhaForm($descricao, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				/*
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Conteúdo: </b></span>";
					htmlFechaColuna();
					itemLinhaForm(nl2br(htmlentities($conteudo)), 'left', 'top', $corFundo, 0, 'tabfundo3');
				fechaLinhaTabela();
				*/
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Status: </b></span>";
					htmlFechaColuna();
					itemLinhaForm(formSelectStatusContratos($status,'status','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();
					$texto="<input type=submit name=matriz[bntExcluir] value=Excluir class=submit>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		} #fecha form
		elseif($matriz[bntExcluir]) {
			# Conferir campos
			# Graver dominio servicos planos
			$grava=dbPaginaContrato($matriz, 'excluir');
			
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);				
			}
			
			echo "<br>";
			listarPaginasContratos($modulo, $sub, 'listar', $idContrato, $matriz);
		}
	}
	else {
		# acusar falta de parametros
		# Mensagem de aviso
		$msg="Registro não encontrado!";
		avisoNOURL("Aviso: Ocorrência de erro", $msg, $url, 400);
	}
}



# Listar parametros do domínio
function alterarPaginasContratos($modulo, $sub, $acao, $registro, $matriz) {

	global $html, $tb, $conn, $corFundo, $corBorda;
	
	# Buscar informações sobre dominio
	$consulta=buscaPaginasContratos($registro, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		
		$id=resultadoSQL($consulta, 0, 'id');
		$idContrato=resultadoSQL($consulta, 0, 'idContrato');
		$nome=resultadoSQL($consulta, 0, 'nomePagina');
		$numero=resultadoSQL($consulta, 0, 'numeroPagina');
		$descricao=resultadoSQL($consulta, 0, 'descricao');
		$conteudo=resultadoSQL($consulta, 0, 'conteudo');
		$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
		$status=resultadoSQL($consulta, 0, 'status');
	
		verContratos($modulo, $sub, $acao, $idContrato, $matriz);
		echo "<br>";
		
		if(!$matriz[bntAlterar] || !$matriz[nome] || !$matriz[descricao] || !$matriz[numero]) {

			# Motrar tabela de busca
			novaTabela2("[Alterar Página do Contrato]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, "$idContrato:$registro");	
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="
					<form method=post name=matriz action=index.php enctype='multipart/form-data'>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro>
					&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Nome: </b></span>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[nome] size=60 value='$nome'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Número: </b></span>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[numero] size=2 value='$numero'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Descrição: </b></span>";
					htmlFechaColuna();
					$texto="<textarea name=matriz[descricao] rows=3 cols=60>$descricao</textarea>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Arquivo:</b><br>
					<span class=normal10>Selecione o arquivo a importar</span>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
					$texto="<input type=file name=arquivo>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Status: </b></span>";
					htmlFechaColuna();
					itemLinhaForm(formSelectStatusContratos($status,'status','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();
					$texto="<input type=submit name=matriz[bntAlterar] value=Alterar class=submit>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		} #fecha form
		elseif($matriz[bntAlterar]) {
			# carregar imagem do arquivo
			$tmpArquivo=$_REQUEST["arquivo"];
	
			$arquivo=addslashes((fread (fopen ($_FILES["arquivo"]["tmp_name"], "r"),
                 filesize ($_FILES["arquivo"]["tmp_name"]))));
					  
			$matriz[conteudo]=$arquivo;
		
			$grava=dbPaginaContrato($matriz, 'alterar');
			
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);				
			}
			
			echo "<br>";
			listarPaginasContratos($modulo, $sub, 'listar', $idContrato, $matriz);
		}
	}
	else {
		# acusar falta de parametros
		# Mensagem de aviso
		$msg="Registro não encontrado!";
		avisoNOURL("Aviso: Ocorrência de erro", $msg, $url, 400);
	}
}


# Função para visualizar as informações do servidor
function verPaginasContratos($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $corFundo, $corBorda, $tb, $html;
	
	# Mostar informações sobre Servidor
	$consulta=buscaPaginasContratos($registro, 'id','igual','id');
	
	#nova tabela para mostrar informações
	novaTabela2('Visualização de Página de Contrato', 'center', '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	
	if($consulta && contaConsulta($consulta)>0) {
		# receber valores
		$id=resultadoSQL($consulta, 0, 'id');
		$idContrato=resultadoSQL($consulta, 0, 'idContrato');
		$nome=resultadoSQL($consulta, 0, 'nomePagina');
		$numero=resultadoSQL($consulta, 0, 'numeroPagina');
		$descricao=resultadoSQL($consulta, 0, 'descricao');
		$conteudo=resultadoSQL($consulta, 0, 'conteudo');
		$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
		$status=resultadoSQL($consulta, 0, 'status');

		# Opcoes Adicionais
		menuOpcAdicional($modulo, $sub, $acao, "$idContrato:$id");	

		#fim das opcoes adicionais
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b class=bold10>Nome: </b></span>";
			htmlFechaColuna();
			itemLinhaForm($nome, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b class=bold10>Número da Página: </b></span>";
			htmlFechaColuna();
			itemLinhaForm($numero, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b class=bold10>Descrição: </b></span>";
			htmlFechaColuna();
			itemLinhaForm($descricao, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b class=bold10>Status: </b></span>";
			htmlFechaColuna();
			itemLinhaForm(formSelectStatusContratos($status,'status','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaForm('&nbsp;', 'left', 'top', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaForm($conteudo, 'left', 'top', $corFundo, 2, 'normal10');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaForm('&nbsp;', 'left', 'top', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
	}
	else {
		itemTabelaNOURL('Registro não encontrado!', 'left', $corFundo, 2, 'txtaviso');		
	}
	
	fechaTabela();	
	# fim da tabela
	
} #fecha visualizacao



# Função para geração de arquivos PDF com as paginas do contrato
function pdfPaginasContratos($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $html;
	
	$consulta=buscaPaginasContratos($registro, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
	
		$idContrato=resultadoSQL($consulta, 0, 'idContrato');
		$nome=resultadoSQL($consulta, 0, 'nomePagina');
		
		verContratos($modulo, $sub, $acao, $idContrato, $matriz);
		echo "<br>";
		
		listarPaginasContratos($modulo, $sub, $acao, $idContrato, $matriz);
		echo "<br>";

		# Parse de HTML
		$tmpArquivo=htmlPreencheDados($matriz, $registro);
		
		# Converter HTML (arquivo) para PDF (arquivo)
		$pdfFile=pdfConverterArquivo($tmpArquivo);

		$texto=htmlMontaOpcao("<a href=$pdfFile>$nome</a>", 'pdf');

		# Selecionar parametros do dominio
		novaTabela("Arquivos Gerados", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 0);
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela($texto, 'center', '70%', 'normal10');
			fechaLinhaTabela();
		fechaTabela();	
	}
	
}

?>
