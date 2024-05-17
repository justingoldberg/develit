<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 10/06/2003
# Ultima alteração: 11/12/2003
#    Alteração No.: 006
#
# Função:
#    Painel - Funções para cadastro

# Função para cadastro
function parametros($modulo, $sub, $acao, $registro, $matriz)
{
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
		# Topo da tabela - Informações e menu principal do Cadastro
		novaTabela2("[Cadastro de Parâmetros]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 4);
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
					echo "<br><img src=".$html[imagem][cadastro]." border=0 align=left><b class=bold>Parâmetros</b>
					<br><span class=normal10>Cadastro de <b>parâmetros</b>, utilizados para customização dos módulos
					de integração.</span>";
				htmlFechaColuna();			
				$texto=htmlMontaOpcao("<br>Adicionar", 'incluir');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=adicionar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Procurar", 'procurar');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=procurar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Listar", 'listar');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=listar", 'center', $corFundo, 0, 'normal');
			fechaLinhaTabela();
		fechaTabela();
		
		# Inclusão
		if($acao=="adicionar") {
			echo "<br>";
			adicionarParametros($modulo, $sub, $acao, $registro, $matriz);
		}
		
		# Alteração
		elseif($acao=="alterar") {
			echo "<br>";
			alterarParametros($modulo, $sub, $acao, $registro, $matriz);
		}
		
		# Exclusão
		elseif($acao=="excluir") {
			echo "<br>";
			excluirParametros($modulo, $sub, $acao, $registro, $matriz);
		}
	
		# Busca
		elseif($acao=="procurar") {
			echo "<br>";
			procurarParametros($modulo, $sub, $acao, $registro, $matriz);
		} #fecha tabela de busca
		
		# Listar
		elseif($acao=="listar" || !$acao) {
			echo "<br>";
			listarParametros($modulo, $sub, $acao, $registro, $matriz);
		} #fecha listagem de servicos
	}


} #fecha menu principal 


# função de busca 
function buscaParametros($texto, $campo, $tipo, $ordem) {

	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[Parametros] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[Parametros] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[Parametros] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[Parametros] WHERE $texto ORDER BY $ordem";
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



# Busca ID do parametro
function buscaDadosParametro($texto, $campo, $tipo, $ordem) {
	
	$consulta=buscaParametros($texto, $campo, $tipo, $ordem);
	
	if($consulta && contaConsulta($consulta)>0) {
		$retorno[id]=resultadoSQL($consulta, 0, 'id');
		$retorno[parametro]=resultadoSQL($consulta, 0, 'parametro');
		$retorno[descricao]=resultadoSQL($consulta, 0, 'descricao');
		$retorno[tipo]=resultadoSQL($consulta, 0, 'tipo');
		$retorno[idUnidade]=resultadoSQL($consulta, 0, 'idUnidade');
	}
	
	return($retorno);

}





# Funcao para cadastro 
function adicionarParametros($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;

	# Form de inclusao
	if(!$matriz[bntAdicionar]) {
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
				<input type=hidden name=acao value=$acao>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Descrição: </b><br>
					<span class=normal10>Descrição do parâmetro</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[descricao] size=60 value='$matriz[descricao]'>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Tipo: </b><br>
					<span class=normal10>Tipo de parâmetro</span>";
				htmlFechaColuna();
				itemLinhaForm(formSelectTipoParametro($matriz[tipo],'tipo','form')."<input type=submit name=matriz[bntSelecionar] value=Selecionar class=submit>", 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
				
			if($matriz[tipo]) {

				if($matriz[tipo]=='nr') {
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Unidade: </b><br>
							<span class=normal10>Unidade utilizada no parâmetro</span>";
						htmlFechaColuna();
						itemLinhaForm(formSelectUnidades($matriz[unidade],'unidade','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}

				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Parâmetro: </b><br>
						<span class=normal10>Parâmetro único</span>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[parametro] size=20 value='$matriz[parametro]'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();

				if($matriz[descricao] || $matriz[tipo]=='nr') {
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "&nbsp;";
						htmlFechaColuna();
						$texto="<input type=submit name=matriz[bntAdicionar] value=Adicionar class=submit>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				
			}
		fechaTabela();
	} #fecha form
	elseif($matriz[bntAdicionar]) {
		# Conferir campos
		if($matriz[descricao] && $matriz[tipo]) {
			# Buscar por prioridade
			# Cadastrar em banco de dados
			$matriz[parametro]=formatarString($matriz[parametro],'minuscula');
			$grava=dbParametro($matriz, 'incluir');
			
			# Verificar inclusão de registro
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				aviso("Aviso", $msg, $url, 760);
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
	}

} # fecha funcao de inclusao




# Função para gravação em banco de dados
function dbParametro($matriz, $tipo)
{
	global $conn, $tb, $modulo, $sub, $acao;
	
	# Sql de inclusão
	if($tipo=='incluir') {
		if(!$matriz[unidade] && $matriz[tipo]=='sn') $matriz[unidade]=0;
			
		$sql="INSERT INTO $tb[Parametros] VALUES (0,
		'$matriz[descricao]',
		'$matriz[tipo]',
		'$matriz[unidade]',
		'$matriz[parametro]')";
	} #fecha inclusao
	
	elseif($tipo=='excluir') {
		# Verificar se a prioridade existe
		$tmpBusca=buscaParametros($matriz[id], 'id', 'igual', 'id');
		
		# Registro já existe
		if(!$tmpBusca|| contaConsulta($tmpBusca)==0) {
			# Mensagem de aviso
			$msg="Registro não existe no banco de dados";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Erro ao alterar registro", $msg, $url, 760);
		}
		else {
			$sql="DELETE FROM $tb[Parametros] WHERE id=$matriz[id]";
		}
	}
	
	# Alterar
	elseif($tipo=='alterar') {
		# Verificar se prioridade existe
		$sql="UPDATE $tb[Parametros] SET descricao='$matriz[descricao]', 
				tipo='$matriz[tipo]', 
				idUnidade='$matriz[unidade]',
				parametro='$matriz[parametro]'
			WHERE id=$matriz[id]";
	}
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
	
} # fecha função de gravação em banco de dados



# Listar 
function listarParametros($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $moduloApp, $corFundo, $corBorda, $html, $limite;

	# Cabeçalho		
	# Motrar tabela de busca
	novaTabela("[Listar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
		# Seleção de registros
		$consulta=buscaParametros($texto, $campo, 'todos','descricao');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Não há registros
			itemTabelaNOURL('Não há registros cadastrados', 'left', $corFundo, 5, 'txtaviso');
		}
		else {
		
			# Paginador
			paginador($consulta, contaConsulta($consulta), $limite[lista][parametros], $registro, 'normal10', 5, $urlADD);
		
			# Cabeçalho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Descrição', 'center', '40%', 'tabfundo0');
				itemLinhaTabela('Tipo', 'center', '20%', 'tabfundo0');
				itemLinhaTabela('Unidade', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Parâmetro', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Opções', 'center', '20%', 'tabfundo0');
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

			$limite=$i+$limite[lista][parametros];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$descricao=resultadoSQL($consulta, $i, 'descricao');
				$tipo=resultadoSQL($consulta, $i, 'tipo');
				$unidade=resultadoSQL($consulta, $i, 'idUnidade');
				$parametro=resultadoSQL($consulta, $i, 'parametro');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($descricao, 'left', '40%', 'normal10');
					itemLinhaTabela(formSelectTipoParametro($tipo,'tipo','check'), 'center', '20%', 'normal10');
					itemLinhaTabela(formSelectUnidades($unidade,'unidade','check'), 'center', '10%', 'normal10');
					itemLinhaTabela($parametro, 'center', '10%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '20%', 'normal10');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem

	fechaTabela();
	
} # fecha função de listagem


# Função para procura de serviço
function procurarParametros($modulo, $sub, $acao, $registro, $matriz)
{
	global $conn, $tb, $corFundo, $corBorda, $html, $limite, $textoProcurar;
	
	# Atribuir valores a variável de busca
	if($textoProcurar) {
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
	echo "</form>";


	# Caso botão procurar seja pressionado
	if($matriz[txtProcurar] && $matriz[bntProcurar]) {
		#buscar registros
		$consulta=buscaParametros("upper(descricao) like '%$matriz[txtProcurar]%' OR upper(parametro) like '%$matriz[txtProcurar]%'",$campo, 'custom','descricao');

		echo "<br>";

		novaTabela("[Resultados]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
	
		if(!$consulta || contaConsulta($consulta)==0 ) {
			# Não há registros
			itemTabelaNOURL('Não foram encontrados registros cadastrados', 'left', $corFundo, 5, 'txtaviso');
		}
		elseif($consulta && contaConsulta($consulta)>0 && (!$registro || is_numeric($registro)) ) {	
		
			itemTabelaNOURL('Registros encontrados procurando por ('.$matriz[txtProcurar].'): '.contaConsulta($consulta).' registro(s)', 'left', $corFundo, 5, 'txtaviso');

			# Paginador
			$urlADD="&textoProcurar=".$matriz[txtProcurar];
			paginador($consulta, contaConsulta($consulta), $limite[lista][parametros], $registro, 'normal', 5, $urlADD);

			# Cabeçalho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela("Descrição", 'left', '40%', 'tabfundo0');
				itemLinhaTabela("Tipo", 'center', '20%', 'tabfundo0');
				itemLinhaTabela("Unidade", 'center', '10%', 'tabfundo0');
				itemLinhaTabela("Parâmetro", 'center', '10%', 'tabfundo0');
				itemLinhaTabela("Opções", 'center', '20%', 'tabfundo0');
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

			$limite=$i+$limite[lista][parametros];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$descricao=resultadoSQL($consulta, $i, 'descricao');
				$tipo=resultadoSQL($consulta, $i, 'tipo');
				$unidade=resultadoSQL($consulta, $i, 'idUnidade');
				$parametro=resultadoSQL($consulta, $i, 'parametro');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($descricao, 'left', '40%', 'normal10');
					itemLinhaTabela(formSelectTipoParametro($tipo,'','check'), 'center', '20%', 'normal10');
					itemLinhaTabela(formSelectUnidades($unidade,'','check'), 'center', '10%', 'normal10');
					itemLinhaTabela($parametro, 'center', '10%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '20%', 'normal10');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem
	} # fecha botão procurar
} #fecha funcao de  procurar 



# Funcao para alteração
function alterarParametros($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# ERRO - Registro não foi informado
	if(!$registro) {
		# ERRO
	}
	# Form de inclusao
	elseif($registro && !$matriz[bntAlterar]) {
	
		# Buscar Valores
		$consulta=buscaParametros($registro, 'id', 'igual', 'id');
		if(!$consulta || contaConsulta($consulta)==0) {
			# Mostrar Erro
			$msg="Registro não foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso", $msg, $url, 760);
		}
		else {
			#atribuir valores
 			$descricao=resultadoSQL($consulta, 0, 'descricao');
			$tipo=resultadoSQL($consulta, 0, 'tipo');
			$unidade=resultadoSQL($consulta, 0, 'idUnidade');
			$parametro=resultadoSQL($consulta, 0, 'parametro');
			
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
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro>
					<input type=hidden name=matriz[id] value=$registro>
					<input type=hidden name=acao value=$acao>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Descrição: </b>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[descricao] size=60 value='$descricao'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Tipo: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectTipoParametro($tipo,'tipo','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				if($tipo!='sn') {
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Unidade: </b>";
						htmlFechaColuna();
						itemLinhaForm(formSelectUnidades($unidade,'unidade','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Parâmetro: </b>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[parametro] size=20 value='$parametro'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();
					$texto="<input type=submit name=matriz[bntAlterar] value=Alterar class=submit>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();				

		} #fecha alteracao
	} #fecha form - !$bntAlterar
	
	# Alteração - bntAlterar pressionado
	elseif($matriz[bntAlterar]) {
		# Conferir campos
		if($matriz[descricao] && $matriz[tipo] && $matriz[parametro]) {
			# continuar
			# Cadastrar em banco de dados
			$grava=dbParametro($matriz, 'alterar');
			
			# Verificar inclusão de registro
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				$url="?modulo=$modulo&sub=$sub&acao=listar";
				aviso("Aviso", $msg, $url, 760);
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
	
} # fecha funcao de alteração


# Exclusão de servicos
function excluirParametros($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# ERRO - Registro não foi informado
	if(!$registro) {
		# Mostrar Erro
		$msg="Registro não foi encontrado!";
		$url="?modulo=$modulo&sub=$sub&acao=$acao";
		aviso("Aviso", $msg, $url, 760);
	}
	# Form de inclusao
	elseif($registro && !$matriz[bntExcluir]) {
	
		# Buscar Valores
		$consulta=buscaParametros($registro, 'id', 'igual', 'id');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Mostrar Erro
			$msg="Registro não foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso", $msg, $url, 760);
		}
		else {
			#atribuir valores
			$id=resultadoSQL($consulta, 0, 'id');
			$descricao=resultadoSQL($consulta, 0, 'descricao');
			$tipo=resultadoSQL($consulta, 0, 'tipo');
			$unidade=resultadoSQL($consulta, 0, 'idUnidade');
			
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
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro>
					<input type=hidden name=matriz[id] value=$registro>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Descrição: </b>";
					htmlFechaColuna();
					itemLinhaForm($descricao, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Tipo: </b>";
					htmlFechaColuna();
					itemLinhaForm($tipo, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Unidade: </b>";
					htmlFechaColuna();
					itemLinhaForm($unidade, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
	
				# Botão de confirmação
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();
					$texto="<input type=submit name=matriz[bntExcluir] value=Excluir class=submit2>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		} #fecha alteracao
	} #fecha form - !$bntExcluir
	
	# Alteração - bntExcluir pressionado
	elseif($matriz[bntExcluir]) {
		# Cadastrar em banco de dados
		$grava=dbParametro($matriz, 'excluir');
				
		# Verificar inclusão de registro
		if($grava) {
			# Mensagem de aviso
			$msg="Registro excluído com Sucesso!";
			$url="?modulo=$modulo&sub=$sub&acao=listar";
			aviso("Aviso", $msg, $url, 760);
		}
		
	} #fecha bntExcluir
	
} #fecha exclusao 



# Função para montar campo de formulario
function formSelectParametros($modulo, $parametro, $campo) {

	global $conn, $tb;
	
	# Buscar Serviços de servidor (ja cadastrados)
	$tmpConsulta=buscaParametrosModulos($modulo, 'idModulo','igual','idModulo');
	
	$consulta=buscaParametros($texto, $campo, 'todos', 'descricao');
	
	$item="<select name=matriz[$campo]>\n";
	
	# Listargem
	$i=0;
	while($i < contaConsulta($consulta)) {
		# Zerar flag de registro já cadastrado
		$flag=0;
		
		# Valores dos campos
		$descricao=resultadoSQL($consulta, $i, 'descricao');
		$idUnidade=resultadoSQL($consulta, $i, 'idUnidade');
		$idTipo=resultadoSQL($consulta, $i, 'tipo');
		$unidade=formSelectUnidades($idUnidade, '','check');
		$tipo=formSelectTipoParametro($idTipo, '','check');
		$id=resultadoSQL($consulta, $i, 'id');

		# Verificar se serviço já está cadastrado
		$x=0;
		while($x < contaConsulta($tmpConsulta) ) {
		
			# Verificar
			$idTmp=resultadoSQL($tmpConsulta, $x, 'idParametro');
			
			if($idTmp == $id) {
				# Setar Flag de registro já cadastrado
				$flag=1;
				break;
			}

			# Incrementar contador
			$x++;
		}

		if(!$flag) {
			# Mostrar serviço		
			$item.= "<option value=$id>$descricao - $tipo";
			
			if($idUnidade)  $item.=" - $unidade";
		}

		#Incrementar contador
		$i++;
	}
	
	$item.="</select>";

	
	return($item);
	
} #fecha funcao de montagem de campo de form



# funcao para checagem de tipo de pessoa
function checkParametro($idParametro) {

	$consulta=buscaParametros($idParametro, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		$retorno[descricao]=resultadoSQL($consulta, 0, 'descricao');
		$retorno[tipo]=resultadoSQL($consulta, 0, 'tipo');
		$retorno[idUnidade]=resultadoSQL($consulta, 0, 'idUnidade');
		$retorno[parametro]=resultadoSQL($consulta, 0, 'parametro');
	}
	
	return($retorno);

}

?>
