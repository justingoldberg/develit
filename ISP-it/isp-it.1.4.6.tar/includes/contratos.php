<?php
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 09/03/2004
# Ultima alteração: 20/03/2004
#    Alteração No.: 003
#
# Função:
#    Cadastro e Manutenção de Contratos

function contratos($modulo, $sub, $acao, $registro, $matriz) {
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		
		### Menu principal - usuarios logados apenas
		novaTabela2("[Cadasto de Contratos]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 4);
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
					echo "<br><img src=".$html[imagem][contratos]." border=0 align=left >
					<b class=bold>Contratos</b>
					<br><span class=normal10>A seção de <b>contratos</b> permite a manutenção de 
					modelos de contratos, utilizados no <b>cadastro de serviços</b>. Os modelos
					de contratos devem ser escritos em <b>formato HTML</b>, utilizando marcadores
					iniciados entre \"%\" para delimitação e substituição de valores.</span>";
				htmlFechaColuna();			
				$texto=htmlMontaOpcao("<br>Adicionar", 'incluir');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=adicionar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Procurar", 'procurar');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=procurar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Listar", 'listar');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=listar", 'center', $corFundo, 0, 'normal');
			fechaLinhaTabela();
		fechaTabela();
	
		echo "<br>";
		if(!$acao || $acao=='listar' || $acao=='procurar') procurarContratos($modulo, $sub, $acao, $registro, $matriz);
		elseif($acao=='adicionar') adicionarContratos($modulo, $sub, $acao, $registro, $matriz);
		elseif($acao=='alterar') alterarContratos($modulo, $sub, $acao, $registro, $matriz);
		elseif($acao=='excluir') excluirContratos($modulo, $sub, $acao, $registro, $matriz);
		elseif($acao=='ver') verContratos($modulo, $sub, $acao, $registro, $matriz);
		elseif($acao=='paginas') {
			$matriz[id]=$registro;
			listarPaginasContratos($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($acao=='paginasadicionar') {
			$matriz[id]=$registro;
			adicionarPaginasContratos($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($acao=='paginasexcluir') {
			$matriz[id]=$registro;
			excluirPaginasContratos($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($acao=='paginasalterar') {
			$matriz[id]=$registro;
			alterarPaginasContratos($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($acao=='paginasver') {
			$matriz[id]=$registro;
			verPaginasContratos($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($acao=='paginaspdf') {
			$matriz[id]=$registro;
			pdfPaginasContratos($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($acao=='paginasps') {
			$matriz[id]=$registro;
			verPaginasContratos($modulo, $sub, $acao, $registro, $matriz);
		}
	}
}



# função de busca
function buscaContratos($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[Contratos] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[Contratos] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[Contratos] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[Contratos] WHERE $texto ORDER BY $ordem";
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
	
} # fecha função de busca 



# Função para procura 
function procurarContratos($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $tb, $corFundo, $corBorda, $html, $limite, $textoProcurar;
	
	# Atribuir valores a variável de busca
	if(!$matriz) {
		$matriz[bntProcurar]=1;
		$matriz[txtProcurar]=$textoProcurar;
	} #fim da atribuicao de variaveis
	
	# Motrar tabela de busca
	novaTabela2("[Procurar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('30%', 'right', $corFundo, 0, 'tabfundo1');
			echo "<b>Procurar por:</b>";
			htmlFechaColuna();
			$texto="
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=procurar>
			<input type=text name=matriz[txtProcurar] size=40 value='$matriz[txtProcurar]'>
			<input type=submit name=matriz[bntProcurar] value=Procurar class=submit>";
			itemLinhaForm($texto, 'left','middle', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
	fechaTabela();

	# Caso botão procurar seja pressionado
	if( $matriz[bntProcurar] && $matriz[txtProcurar] || $acao=='listar' || !$acao) {
		#buscar registros
		
		if($acao=='listar' || !$acao) 
			$consulta=buscaContratos("","", 'todos','nome ASC');
		elseif($acao=='procurar' || $matriz[txtProcurar])
			$consulta=buscaContratos("upper(nome) like '%$matriz[txtProcurar]%' OR upper(descricao) like '%$matriz[txtProcurar]%'",$campo, 'custom','nome ASC');

		echo "<br>";

		novaTabela("[Contratos]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 4);
	
		if(!$consulta || contaConsulta($consulta)==0 ) {
			# Não há registros
			itemTabelaNOURL('Não foram encontrados registros cadastrados', 'left', $corFundo, 10, 'txtaviso');
		}
		elseif($consulta && contaConsulta($consulta)>0 && (is_integer($registro) || !$registro)) {	
		
			if($matriz[txtProcurar])
				itemTabelaNOURL('Registros encontrados procurando por ('.$matriz[txtProcurar].'): '.contaConsulta($consulta).' registro(s)', 'left', $corFundo, 4, 'txtaviso');

			# Paginador
			if($matriz[txtProcurar])
				$urlADD="&textoProcurar=".$matriz[txtProcurar];
				
			# Paginador
			paginador($consulta, contaConsulta($consulta), $limite[lista][contratos], $registro, 'normal', 4, $urlADD);
		
			# Cabeçalho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Nome', 'center', '45%', 'tabfundo0');
				itemLinhaTabela('Data de Cadastro', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Status', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Opções', 'center', '25%', 'tabfundo0');
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
			
			$limite=$i+$limite[lista][contratos];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$nome=resultadoSQL($consulta, $i, 'nome');				
				$dtCadastro=resultadoSQL($consulta, $i, 'dtCadastro');
				$status=resultadoSQL($consulta, $i, 'status');
				
				# Contar quantas paginas o contrato possui
				$consultaPaginas=buscaPaginasContratos($id, 'idContrato','igual','numeroPagina');
				if($consultaPaginas && contaConsulta($consultaPaginas)>0) {
					$tmpNumero=contaConsulta($consultaPaginas);
					$opcPaginas=" <span class=txtok8>($tmpNumero pág.)</span>";
				}
				else $opcPaginas="";
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=paginas&registro=$id>Páginas</a>",'paginas');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($nome.$opcPaginas, 'left', '45%', 'normal10');
					itemLinhaTabela(converteData($dtCadastro,'banco','formdata'), 'center', '10%', 'normal10');
					itemLinhaTabela(formSelectStatusContratos($status,'','check'), 'center', '10%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '25%', 'normal8');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem
		fechaTabela();
	} # fecha botão procurar
} #fecha funcao de  procura



# Funcao para cadastro de usuarios
function adicionarContratos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# Form de inclusao
	if(!$matriz[bntAdicionar] || !$matriz[nome] || !$matriz[descricao]) {
		# Motrar tabela de busca
		novaTabela2("[Adicionar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			menuOpcAdicional($modulo, $sub, $acao, $registro);				
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=acao value=$acao>
				<input type=hidden name=registro>
				&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Nome: </b><br>
					<span class=normal10>Nome para identificação do contrato</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[nome] size=60>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Descricao: </b><br>
					<span class=normal10>Identificação detalhada do contrato</span>";
				htmlFechaColuna();
				$texto="<textarea name=matriz[descricao] rows=3 cols=60></textarea>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Status: </b><br>
					<span class=normal10>Status do contrato</span>";
				htmlFechaColuna();
				itemLinhaForm(formSelectStatusContratos('A','status','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "&nbsp;";
				htmlFechaColuna();
				$texto="<input type=submit name=matriz[bntAdicionar] value=Adicionar class=submit>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		fechaTabela();
	} #fecha form
	elseif($matriz[bntAdicionar]) {
		# Conferir campos
		if($matriz[nome] && $matriz[descricao]) {
			# Cadastrar em banco de dados
			$grava=dbContrato($matriz, 'incluir');
			
			# Verificar inclusão de registro
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				avisoNOURL("Aviso", $msg, 400);
				
				echo "<br>";
				procurarContratos($modulo, $sub, 'listar', $registro, $matriz);
			}
		}
		
		# falta de parametros
		else {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Falta de parâmetros necessários. Informe os campos obrigatórios e tente novamente";
			avisoNOURL("Aviso: Ocorrência de erro", $msg, $url, 400);
		}
	}
} # fecha funcao de inclusao de grupos




# Funcao para exclusao de usuarios
function excluirContratos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# Form de inclusao
	if(!$matriz[bntExcluir]) {
	
		# Buscar informações sobre registro
		$consulta=buscaContratos($registro, 'id','igual','id');
		
		if($consulta && contaConsulta($consulta)>0) {
		
			# receber valores
			$id=resultadoSQL($consulta, 0, 'id');
			$nome=resultadoSQL($consulta, 0, 'nome');
			$descricao=resultadoSQL($consulta, 0, 'descricao');
			$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
			$status=resultadoSQL($consulta, 0, 'status');
			
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
					<input type=hidden name=id value=$id>
					<input type=hidden name=matriz[id] value=$id>
					<input type=hidden name=acao value=$acao>&nbsp;";
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
						echo "<b class=bold10>Descricao: </b></span>";
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
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();
					$texto="<input type=submit name=matriz[bntExcluir] value=Excluir class=submit2>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		}
		# registro nao encontrado
		else {
			# Mensagem de aviso
			$msg="Registro não foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=listar";
			aviso("Aviso", $msg, $url, 760);
		}
	} #fecha form
	elseif($matriz[bntExcluir]) {
		# Cadastrar em banco de dados
		$grava=dbContrato($matriz, 'excluir');
		
		# Verificar inclusão de registro
		if($grava) {
			# Excluir paginas do contrato
			dbPaginaContrato($matriz, 'excluircontrato');
			
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Registro Gravado com Sucesso!";
			avisoNOURL("Aviso", $msg, 400);
			
			echo "<br>";
			procurarContratos($modulo, $sub, 'listar', 0, $matriz);
		}
	}
} # fecha funcao de exclusão de grupos




# Funcao para exclusao de usuarios
function alterarContratos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# Form de inclusao
	if(!$matriz[bntAlterar] || !$matriz[nome] || !$matriz[descricao]) {
	
		# Buscar informações sobre registro
		$consulta=buscaContratos($registro, 'id','igual','id');
		
		if($consulta && contaConsulta($consulta)>0) {
		
			# receber valores
			$id=resultadoSQL($consulta, 0, 'id');
			$nome=resultadoSQL($consulta, 0, 'nome');
			$descricao=resultadoSQL($consulta, 0, 'descricao');
			$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
			$status=resultadoSQL($consulta, 0, 'status');
			
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
					<input type=hidden name=registro value=$registro>
					<input type=hidden name=matriz[id] value=$id>
					<input type=hidden name=acao value=$acao>&nbsp;";
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
						echo "<b class=bold10>Descricao: </b></span>";
					htmlFechaColuna();
					$texto="<textarea name=matriz[descricao] rows=3 cols=60>$descricao</textarea>";
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
		}
		# registro nao encontrado
		else {
			# Mensagem de aviso
			$msg="Registro não foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=listar";
			aviso("Aviso", $msg, $url, 760);
		}
	} #fecha form
	else {
		# Cadastrar em banco de dados
		$grava=dbContrato($matriz, 'alterar');
		
		# Verificar inclusão de registro
		if($grava) {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Registro Gravado com Sucesso!";
			avisoNOURL("Aviso", $msg, 400);
			
			echo "<br>";
			procurarContratos($modulo, $sub, 'listar', 0, $matriz);
		}
	}
} # fecha funcao de exclusão de grupos



# Função para gravação em banco de dados
function dbContrato($matriz, $tipo) {

	global $conn, $tb, $modulo, $sub, $acao;
	
	$data=dataSistema();
	
	# Sql de inclusão
	if($tipo=='incluir') {
		$sql="INSERT INTO $tb[Contratos] VALUES (
			'0', 
			'$matriz[nome]', 
			'$matriz[descricao]',
			'$data[dataBanco]', 
			'$matriz[status]'
		)";
	} #fecha inclusao
	
	# Excluir
	elseif($tipo=='alterar') {
		$sql="
			UPDATE 
				$tb[Contratos] 
			SET
				nome='$matriz[nome]',
				descricao='$matriz[descricao]',
				status='$matriz[status]'
			WHERE
				id=$matriz[id]";
	}
	
	# Excluir
	elseif($tipo=='excluir') {
		$sql="DELETE FROM $tb[Contratos] WHERE id=$matriz[id]";
	}

	# Inativar
	elseif($tipo=='inativar') {
		$sql="UPDATE $tb[Contratos] SET status='I' WHERE id=$matriz[id]";
	}
	
	# Ativar
	elseif($tipo=='ativar') {
		$sql="UPDATE $tb[Contratos] SET status='A' WHERE id=$matriz[id]";
	}
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
	
} # fecha função de gravação em banco de dados




# Função para visualizar as informações do servidor
function verContratos($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $corFundo, $corBorda, $tb, $html;
	
	# Mostar informações sobre Servidor
	$consulta=buscaContratos($registro, 'id','igual','id');
	
	#nova tabela para mostrar informações
	novaTabela2('Informações sobre Contrato', 'center', '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	
	if($consulta && contaConsulta($consulta)>0) {
		# receber valores
		$id=resultadoSQL($consulta, 0, 'id');
		$nome=resultadoSQL($consulta, 0, 'nome');
		$descricao=resultadoSQL($consulta, 0, 'descricao');
		$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
		$status=resultadoSQL($consulta, 0, 'status');
		
		#fim das opcoes adicionais
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b class=bold10>Nome: </b></span>";
			htmlFechaColuna();
			itemLinhaForm($nome, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b class=bold10>Descricao: </b></span>";
			htmlFechaColuna();
			itemLinhaForm($descricao, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b class=bold10>Status: </b></span>";
			htmlFechaColuna();
			itemLinhaForm(formSelectStatusContratos($status,'status','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
	}
	else {
		itemTabelaNOURL('Registro não encontrado!', 'left', $corFundo, 2, 'txtaviso');		
	}
	
	fechaTabela();	
	# fim da tabela
	
} #fecha visualizacao




# função de forma para seleção de tipo de pessoas
function formSelectContratos($contrato, $campo, $tipo) {

	if($tipo=='check') {
	
		$consulta=buscaContratos($contrato,'id','igual','nome');
		
		if($consulta && contaConsulta($consulta)>0) {
			$id=resultadoSQL($consulta, 0, 'id');
			$nome=resultadoSQL($consulta, 0, 'nome');
			$descricao=resultadoSQL($consulta, 0, 'descricao');
			$status=resultadoSQL($consulta, 0, 'status');

			$retorno=$nome;
		}
	
	}
	elseif($tipo=='form') {
	
		$consulta=buscaContratos('','','todos','nome');
		
		if($consulta && contaConsulta($consulta)>0) {
			
			$retorno="<select name=matriz[$campo] onChange=javascript:submit();>";
			
			for($i=0;$i<contaConsulta($consulta);$i++) {
			
				$id=resultadoSQL($consulta, $i, 'id');
				$nome=resultadoSQL($consulta, $i, 'nome');
				$descricao=resultadoSQL($consulta, $i, 'descricao');
				$status=resultadoSQL($consulta, $i, 'status');
				
				if($contrato==$id) $opcSelect='selected';
				else $opcSelect='';
				
				$retorno.="\n<option value=$id $opcSelect>$nome";
			}
			
			$retorno.="</select>";
		}
	
	}
	
	return($retorno);
}



# Função para buscar dados 
function dadosContratos($id) {

	$consulta=buscaContratos($id, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		$retorno[id]=resultadoSQL($consulta, 0, 'id');
		$retorno[nome]=resultadoSQL($consulta, 0, 'nome');
		$retorno[descricao]=resultadoSQL($consulta, 0, 'descricao');
		$retorno[dtCadastro]=resultadoSQL($consulta, 0, 'dtCadastro');
		$retorno[status]=resultadoSQL($consulta, 0, 'status');
	}
	
	return($retorno);
}


# Função para calculo de validade de contrato
function validadeContrato($data, $meses) {


	$dtEmissao=converteData($data, 'banco','timestamp');
	
	$dia=substr($data,8,2);
	$mes=substr($data,5,2);
	$ano=substr($data,0,4);
	$hora=substr($data,11,2);
	$min=substr($data,14,2);
	$seg=substr($data,17,2);
	
	$dtEmissao=mktime($hora, $min, $seg, $mes+$meses, $dia, $ano);

	return(converteData($dtEmissao,'timestamp','banco'));
	

}

?>
