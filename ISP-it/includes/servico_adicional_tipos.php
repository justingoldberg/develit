<?
################################################################################
#       Criado por: Jos� Roberto Kerne - joseroberto@kerne.org
#  Data de cria��o: 21/05/2003
# Ultima altera��o: 01/12/2003
#    Altera��o No.: 006
#
# Fun��o:
#    Painel - Fun��es para cadastro de tipos de documentos

# Fun��o para cadastro
function tipoServicoAdicional($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin;
	
	# Permiss�o do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin]) {
		# SEM PERMISS�O DE EXECUTAR A FUN��O
		$msg="ATEN��O: Voc� n�o tem permiss�o para executar esta fun��o";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		# Topo da tabela - Informa��es e menu principal do Cadastro
		novaTabela2("[Cadastro de Tipos de Servi�os Adicionais]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 4);
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
					echo "<br><img src=".$html[imagem][servicos_adicionais]." border=0 align=left><b class=bold>Tipos de Servi�os Adicionais</b>
					<br><span class=normal10>Cadastro de <b>tipos de servi�os adicionais</b>, utilizados para categorias
					lan�amentos de servi�os adicionais, como Taxas de Instala��o, Manuten��o, Cancelamentos, etc.</span>";
				htmlFechaColuna();			
				$texto=htmlMontaOpcao("<br>Adicionar", 'incluir');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=adicionar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Procurar", 'procurar');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=procurar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Listar", 'listar');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=listar", 'center', $corFundo, 0, 'normal');
			fechaLinhaTabela();
		fechaTabela();
		
		# Inclus�o
		if($acao=="adicionar") {
			echo "<br>";
			adicionarTipoServicoAdicional($modulo, $sub, $acao, $registro, $matriz);
		}
		
		# Altera��o
		elseif($acao=="alterar") {
			echo "<br>";
			alterarTipoServicoAdicional($modulo, $sub, $acao, $registro, $matriz);
		}
	
		# Busca
		elseif($acao=="procurar" || $acao=="listar" || !$acao) {
			echo "<br>";
			procurarTipoServicoAdicional($modulo, $sub, $acao, $registro, $matriz);
		} #fecha tabela de busca
	}
} #fecha menu principal 



# fun��o de busca 
function buscaTipoServicoAdicional($texto, $campo, $tipo, $ordem) {

	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[TipoServicoAdicional] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[TipoServicoAdicional] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[TipoServicoAdicional] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[TipoServicoAdicional] WHERE $texto ORDER BY $ordem";
	}
	
	# Verifica consulta
	if($sql){
		$consulta=consultaSQL($sql, $conn);
		# Retornvar consulta
		return($consulta);
	}
	else {	
		# Mensagem de aviso
		$msg="Consulta n�o pode ser realizada por falta de par�metros";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
	}
	
} # fecha fun��o de busca




# Funcao para cadastro 
function adicionarTipoServicoAdicional($modulo, $sub, $acao, $registro, $matriz)
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
				<input type=hidden name=registro value=$registro>
				<input type=hidden name=acao value=$acao>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold>Nome: </b><br>
					<span class=normal10>Identifica��o do Tipo de Servi�o Adicional</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[nome] size=60>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold>Descri��o: </b><br>
					<span class=normal10>Descri��o do Tipo de Servi�o Adicional</span>";
				htmlFechaColuna();
				$texto="<textarea name=matriz[descricao] rows=4 cols=60></textarea>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
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
		# Cadastrar em banco de dados
		$grava=dbTipoServicoAdicional($matriz, 'incluir');
		
		# Verificar inclus�o de registro
		if($grava) {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Registro Gravado com Sucesso!";
			avisoNOURL("Aviso", $msg,  400);
			
			echo "<br>";
			procurarTipoServicoAdicional($modulo, $sub, 'listar', $registro, $matriz);
		}
		
	}

} # fecha funcao de inclusao




# Fun��o para grava��o em banco de dados
function dbTipoServicoAdicional($matriz, $tipo)
{
	global $conn, $tb, $modulo, $sub, $acao;
	
	$data=dataSistema();
	
	# Sql de inclus�o
	if($tipo=='incluir') {
		$sql="
			INSERT INTO $tb[TipoServicoAdicional] VALUES (
				0,
				'$matriz[nome]',
				'$matriz[descricao]',
				'$data[dataBanco]'
			)";
	} #fecha inclusao
	
	# Alterar
	elseif($tipo=='alterar') {
		# Verificar se prioridade existe
		$sql="
			UPDATE 
				$tb[TipoServicoAdicional] 
			SET 
				nome='$matriz[nome]', 
				descricao='$matriz[descricao]'
			WHERE 
				id=$matriz[id]";
	}
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
	
} # fecha fun��o de grava��o em banco de dados



# Fun��o para procura de servi�o
function procurarTipoServicoAdicional($modulo, $sub, $acao, $registro, $matriz)
{
	global $conn, $tb, $corFundo, $corBorda, $html, $limite, $textoProcurar;
	
	# Atribuir valores a vari�vel de busca
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


	# Caso bot�o procurar seja pressionado
	if($matriz[txtProcurar] || !$acao || $acao=='listar') {
		#buscar registros
		if($matriz[txtProcurar]) 
			$consulta=buscaTipoServicoAdicional("upper(nome) like '%$matriz[txtProcurar]%' OR upper(descricao) like '%$matriz[txtProcurar]%'",$campo, 'custom','descricao');
		else
			$consulta=buscaTipoServicoAdicional('','','todos','nome');

		echo "<br>";

		novaTabela("[Resultados]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
	
		if(!$consulta || contaConsulta($consulta)==0 ) {
			# N�o h� registros
			itemTabelaNOURL('N�o foram encontrados registros cadastrados', 'left', $corFundo, 3, 'txtaviso');
		}
		elseif($consulta && contaConsulta($consulta)>0 && (!$registro || is_numeric($registro)) ) {	
		
			if($matriz[txtProcurar])
				itemTabelaNOURL('Registros encontrados procurando por ('.$matriz[txtProcurar].'): '.contaConsulta($consulta).' registro(s)', 'left', $corFundo, 3, 'txtaviso');

			# Paginador
			$urlADD="&textoProcurar=".$matriz[txtProcurar];
			paginador($consulta, contaConsulta($consulta), $limite[lista][tipo_servico_adicional], $registro, 'normal', 3, $urlADD);

			# Cabe�alho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Tipo de Servi�o Adicional', 'center', '50%', 'tabfundo0');
				itemLinhaTabela('Data Cadastro', 'center', '20%', 'tabfundo0');
				itemLinhaTabela('Op��es', 'center', '30%', 'tabfundo0');
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

			$limite=$i+$limite[lista][tipo_servico_adicional];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$nome=resultadoSQL($consulta, $i, 'nome');
				$dtCadastro=resultadoSQL($consulta, $i, 'dtCadastro');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($nome, 'left', '50%', 'normal10');
					itemLinhaTabela(converteData($dtCadastro,'banco','form'), 'center', '20%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '30%', 'normal10');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem
	} # fecha bot�o procurar
	fechaTabela();
} #fecha funcao de  procurar 



# Funcao para altera��o
function alterarTipoServicoAdicional($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# ERRO - Registro n�o foi informado
	if(!$registro) {
		# ERRO
	}

	# Buscar Valores
	$consulta=buscaTipoServicoAdicional($registro, 'id', 'igual', 'id');
	if(!$consulta || contaConsulta($consulta)==0) {
		# Mostrar Erro
		$msg="Registro n�o foi encontrado!";
		$url="?modulo=$modulo&sub=$sub&acao=$acao";
		aviso("Aviso", $msg, $url, 760);
	}
	else {
		# Form de inclusao
		if(!$matriz[bntAlterar] || !$matriz[nome] || !$matriz[descricao]) {
	
			#atribuir valores
 			$nome=resultadoSQL($consulta, 0, 'nome');
			$descricao=resultadoSQL($consulta, 0, 'descricao');
			
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
						echo "<b class=bold>Nome: </b></span>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[nome] size=60 value='$nome'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Descri��o: </b></span>";
					htmlFechaColuna();
					$texto="<textarea name=matriz[descricao] rows=4 cols=60>$descricao</textarea>";
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
		
		# Altera��o - bntAlterar pressionado
		elseif($matriz[bntAlterar]) {
			# Conferir campos
			if($matriz[descricao] && $matriz[nome]) {
				# continuar
				# Cadastrar em banco de dados
				$grava=dbTipoServicoAdicional($matriz, 'alterar');
				
				# Verificar inclus�o de registro
				if($grava) {
					# acusar falta de parametros
					# Mensagem de aviso
					$msg="Registro Gravado com Sucesso!";
					avisoNOURL("Aviso", $msg, 400);
					
					echo "<br>";
					procurarTipoServicoAdicional($modulo, $sub, 'listar', 0, $matriz);
				}
			}
			
			# falta de parametros
			else {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Falta de par�metros necess�rios. Informe os campos obrigat�rios e tente novamente";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				aviso("Aviso: Ocorr�ncia de erro", $msg, $url, 760);
			}
		} #fecha bntAlterar		
	} #fecha form - !$bntAlterar
	
} # fecha funcao de altera��o



# Formul�rio de sele��o de Tipo de Endere�o
function formSelectTipoServicoAdicional($tipoServico, $campo, $tipo, $onChange=1) {

	$consulta=buscaTipoServicoAdicional('', 'valor','todos','nome');
	
	if($consulta && contaConsulta($consulta)>0) {
	
		if($tipo=='form') {
		
			if ($onChange) {	
				$retorno="<select name=matriz[$campo] onChange=javascript:submit()>\n";
			}
			else 
				$retorno="<select name=matriz[$campo] >\n";
				
			for($a=0;$a<contaConsulta($consulta);$a++) {
				$tmpID=resultadoSQL($consulta, $a, 'id');
				$tmpNome=resultadoSQL($consulta, $a, 'nome');
			
				if($tmpID==$tipoServico) $opcSelect='selected';
				else $opcSelect='';
				
				$retorno.="<option value=$tmpID $opcSelect>$tmpNome\n";
			}
			
			$retorno.="</select>";
		}
		elseif($tipo=='check') {
			$consulta=buscaTipoServicoAdicional($tipoServico, 'id','igual','nome');
			
			if($consulta && contaConsulta($consulta)>0) {
				$retorno=resultadoSQL($consulta, 0, 'nome');
			}
			else $retorno='Tipo de Servi�o Adicional inv�lido!';
		}
	}

	return($retorno);
}

?>
