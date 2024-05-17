<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 08/07/2003
# Ultima alteração: 11/03/2004
#    Alteração No.: 006
#
# Função:
#    Painel - Funções para cadastro de serviços

# Função de banco de dados - Pessoas
function dbServico($matriz, $tipo) {

	global $conn, $tb, $modulo, $sub, $acao;
	
	# Sql de inclusão
	if($tipo=='incluir') {
		$sql="INSERT INTO $tb[Servicos] VALUES (0,
		'$matriz[tipoCobranca]',
		'$matriz[nome]',
		'$matriz[descricao]',
		'$matriz[valor]',
		'$matriz[status]'
		)";
	} #fecha inclusao
	elseif($tipo=='excluir') {
		$sql="DELETE FROM $tb[Servicos] where id=$matriz[id]";
	}
	elseif($tipo=='alterar') {
		$sql="UPDATE $tb[Servicos] 
			SET 
				idTipoCobranca='$matriz[tipoCobranca]',
				nome='$matriz[nome]',
				descricao='$matriz[descricao]',
				valor='$matriz[valor]',
				idStatusPadrao='$matriz[status]'
			WHERE 
				id=$matriz[id]";
	}
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
}




# função de busca 
function buscaServicos($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[Servicos] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[Servicos] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[Servicos] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[Servicos] WHERE $texto ORDER BY $ordem";
	}
	
	# Verifica consulta
	if($sql){
		$consulta=consultaSQL($sql, $conn);
		# Retornar consulta
		return($consulta);
	}
	else {	
		# Mensagem de aviso
		$msg="Consulta não pode ser realizada por falta de parâmetros";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
	}
	
} # fecha função de busca




function servicos($modulo, $sub, $acao, $registro, $matriz) {

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
		novaTabela2("[Cadastro de Serviços]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 4);
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
					echo "<br><img src=".$html[imagem][cadastro]." border=0 align=left><b class=bold>Serviços</b>
					<br><span class=normal10>Cadastro de <b>serviços.</span>";
				htmlFechaColuna();			
				$texto=htmlMontaOpcao("<br>Adicionar", 'incluir');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=adicionar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Procurar", 'procurar');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=procurar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Listar", 'listar');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=listar", 'center', $corFundo, 0, 'normal');
			fechaLinhaTabela();
		fechaTabela();
		
		
		if(!$acao) {
			# Mostrar listagem
			itemTabelaNOURL("&nbsp;", 'left', $corFundo, 0, 'normal');
			procurarServicos($modulo, $sub, $acao, $registro, $matriz);			
		}
		
		# Inclusão
		if($acao=="adicionar") {
			echo "<br>";
			adicionarServicos($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($acao=='procurar') {
			echo "<br>";
			procurarServicos($modulo, $sub, $acao, $registro, $matriz);
		}
		# Listagem
		elseif($acao=="listar") {
			echo "<br>";
			listarServicos($modulo, $sub, $acao, $registro, $matriz);
		}
		# Excluir
		elseif($acao=="excluir") {
			echo "<br>";
			excluirServicos($modulo, $sub, $acao, $registro, $matriz);
		}
		# Excluir
		elseif($acao=="ver") {
			echo "<br>";
			verServicos($modulo, $sub, $acao, $registro, $matriz);
		}
		# Alterar
		elseif($acao=="alterar") {
			echo "<br>";
			alterarServicos($modulo, $sub, $acao, $registro, $matriz);
		}
		# Parametros
		elseif($acao=="parametros") {
			echo "<br>";
			parametrosServicos($modulo, $sub, $acao, $registro, $matriz);
		}
		# Parametros Adicionar
		elseif($acao=="parametrosadicionar") {
			echo "<br>";
			adicionarParametrosServicos($modulo, $sub, $acao, $registro, $matriz);
		}
		# Parametros Excluir
		elseif($acao=="parametrosexcluir") {
			echo "<br>";
			excluirParametrosServicos($modulo, $sub, $acao, $registro, $matriz);
		}
		# Parametros Excluir
		elseif($acao=="contratos" || $acao=='contratosadicionar' || $acao=='contratosexcluir' || $acao=='contratosativar' || $acao=='contratosinativar') {
			echo "<br>";
			servicosContratos($modulo, $sub, $acao, $registro, $matriz);
		}
	}	


}


# Função para procura de serviço
function procurarServicos($modulo, $sub, $acao, $registro, $matriz)
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
		$consulta=buscaServicos("upper(descricao) like '%$matriz[txtProcurar]%' OR nome like '%$matriz[txtProcurar]%' OR valor like '%$matriz[txtProcurar]%'",$campo, 'custom','nome');

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
			
			# Paginador
			paginador($consulta, contaConsulta($consulta), $limite[lista][servicos], $registro, 'normal10', 5, $urlADD);
		
			# Cabeçalho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Serviço', 'center', '30%', 'tabfundo0');
				itemLinhaTabela('Valor', 'center', '15%', 'tabfundo0');
				itemLinhaTabela('Tipo de Cobrança', 'center', '20%', 'tabfundo0');
				itemLinhaTabela('Status Padrão', 'center', '10%', 'tabfundo0');
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

			$limite=$i+$limite[lista][servicos];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$idTipoCobranca=resultadoSQL($consulta, $i, 'idTipoCobranca');
				$nome=resultadoSQL($consulta, $i, 'nome');
				$descricao=resultadoSQL($consulta, $i, 'descricao');
				$valor=resultadoSQL($consulta, $i, 'valor');
				$status=resultadoSQL($consulta, $i, 'idStatusPadrao');
				$status=formSelectStatusServico($status, '','check');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=parametros&registro=$id>Parametros</a>",'parametros');
				$opcoes.="<br>";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=contratos&registro=$id>Contratos</a>",'contrato');

				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($nome, 'left', '30%', 'normal10');
					itemLinhaTabela("R$ ".number_format($valor,2,',','.'), 'center', '15%', 'normal10');
					itemLinhaTabela(formSelectTipoCobranca($idTipoCobranca,'','check'), 'center', '20%', 'normal8');
					itemLinhaTabela($status[descricao], 'center', '10%', 'normal8');
					itemLinhaTabela($opcoes, 'left', '25%', 'normal8');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;			
			} #fecha laco de montagem de tabela
		} #fecha listagem
	} # fecha botão procurar
} #fecha funcao de  procurar 



# Funcao para cadastro 
function adicionarServicos($modulo, $sub, $acao, $registro, $matriz)
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
					echo "<b>Nome: </b><br>
					<span class=normal10>Nome para identificação do serviço</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[nome] size=60>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b>Descrição: </b><br>
					<span class=normal10>Descrição do Serviço</span>";
				htmlFechaColuna();
				$texto="<textarea name=matriz[descricao] rows=3 cols=60></textarea>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b>Tipo de Cobrança: </b><br>
					<span class=normal10>Selecione o tipo de cobrança</span>";
				htmlFechaColuna();
				itemLinhaForm(formSelectTipoCobranca($tipoCobranca, 'tipoCobranca','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b>Valor: </b><br>
					<span class=normal10>Valor do Serviço</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[valor] size=12> <span class=txtaviso>(Formato: 9.999,99)</span>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b>Status Padrão: </b><br>
					<span class=normal10>Utilizado na inclusão de serviço do cliente</span>";
				htmlFechaColuna();
				itemLinhaForm(formSelectStatusServico($matriz[$status],'status','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
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
		if($matriz[nome] && $matriz[descricao] && $matriz[valor]) {
		
			# Converter valores antes de gravar
			$matriz[valor]=formatarValores($matriz[valor]);
			
			# Cadastrar em banco de dados
			$grava=dbServico($matriz, 'incluir');
			
			# Verificar inclusão de registro
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				$url="?modulo=$modulo&sub=$sub&acao=listar&registro=$registro";
				avisoNOURL("Aviso", $msg, 760);
				
				echo "<br>";
				$acao='listar';
				listarServicos($modulo, $sub, $acao, $registro, $matriz);
			}
			else {
				# Mensagem de aviso
				$msg="Erro ao gravar Serviço!";
				$url="?modulo=$modulo&sub=$sub&acao=listar&registro=$registro";
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



# Listar 
function listarServicos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $moduloApp, $corFundo, $corBorda, $html, $limite;

	# Cabeçalho		
	# Motrar tabela de busca
	novaTabela("[Listar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
		# Seleção de registros
		$consulta=buscaServicos($texto, $campo, 'todos','descricao');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Não há registros
			itemTabelaNOURL('Não há registros cadastrados', 'left', $corFundo, 5, 'txtaviso');
		}
		else {
			# Paginador
			paginador($consulta, contaConsulta($consulta), $limite[lista][servicos], $registro, 'normal10', 5, $urlADD);
		
			# Cabeçalho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Serviço', 'center', '30%', 'tabfundo0');
				itemLinhaTabela('Valor', 'center', '15%', 'tabfundo0');
				itemLinhaTabela('Tipo de Cobrança', 'center', '20%', 'tabfundo0');
				itemLinhaTabela('Status Padrão', 'center', '10%', 'tabfundo0');
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

			$limite=$i+$limite[lista][servicos];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$idTipoCobranca=resultadoSQL($consulta, $i, 'idTipoCobranca');
				$nome=resultadoSQL($consulta, $i, 'nome');
				$descricao=resultadoSQL($consulta, $i, 'descricao');
				$valor=resultadoSQL($consulta, $i, 'valor');
				$status=resultadoSQL($consulta, $i, 'idStatusPadrao');
				$status=formSelectStatusServico($status, '','check');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=parametros&registro=$id>Parametros</a>",'parametros');
				$opcoes.="<br>";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=contratos&registro=$id>Contratos</a>",'contrato');

				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($nome, 'left', '30%', 'normal10');
					itemLinhaTabela("R$ ".number_format($valor,2,',','.'), 'center', '15%', 'normal10');
					itemLinhaTabela(formSelectTipoCobranca($idTipoCobranca,'','check'), 'center', '20%', 'normal8');
					itemLinhaTabela($status[descricao], 'center', '10%', 'normal8');
					itemLinhaTabela($opcoes, 'left', '25%', 'normal8');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem

	fechaTabela();
	
} # fecha função de listagem



# Funcao para alteração
function alterarServicos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# ERRO - Registro não foi informado
	if(!$registro) {
		# ERRO
	}
	# Form de inclusao
	elseif($registro && !$matriz[bntAlterar]) {
	
		# Buscar Valores
		$consulta=buscaServicos($registro, 'id', 'igual', 'id');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Mostrar Erro
			$msg="Registro não foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso", $msg, $url, 760);
		}
		else {
			#atribuir valores
 			$descricao=resultadoSQL($consulta, 0, 'descricao');
 			$nome=resultadoSQL($consulta, 0, 'nome');
			$valor=number_format(resultadoSQL($consulta, 0, 'valor'),2,',','.');
			$idTipoCobranca=resultadoSQL($consulta, 0, 'idTipoCobranca');
			$status=resultadoSQL($consulta, 0, 'idStatusPadrao');
			
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
						echo "<b>Nome: </b>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[nome] size=60 value='$nome'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Descrição: </b>";
					htmlFechaColuna();
					$texto="<textarea name=matriz[descricao] rows=3 cols=60>$descricao</textarea>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Tipo de Cobrança: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectTipoCobranca($idTipoCobranca, 'tipoCobranca','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Valor: </b>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[valor] size=12 value='$valor' onBlur=formataValor(this.value,9)> <span class=txtaviso>(Formato: 9.999,99)</span>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Status Padrão: </b><br>
						<span class=normal10>Utilizado na inclusão de serviço do cliente</span>";
					htmlFechaColuna();
					itemLinhaForm(formSelectStatusServico($status,'status','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
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
		if($matriz[nome] && $matriz[descricao] && $matriz[valor]) {
			# formatar valor 
			$matriz[valor]=formatarValores($matriz[valor]);
			
			# Cadastrar em banco de dados
			$grava=dbServico($matriz, 'alterar');
			
			# Verificar inclusão de registro
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				$url="?modulo=$modulo&sub=$sub&acao=listar";
				aviso("Aviso", $msg, $url, 400);
				
				itemTabelaNOURL("&nbsp;", 'left', $corFundo, 0, 'normal');
				listarServicos($modulo, $sub, 'listar', 0, $matriz);
			}
			else {
				# Mensagem de aviso
				$msg="Erro ao gravar Serviço!";
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



# Funcao para exclusão
function excluirServicos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# ERRO - Registro não foi informado
	if(!$registro) {
		# ERRO
	}
	# Form de inclusao
	elseif($registro && !$matriz[bntExcluir]) {
	
		# Buscar Valores
		$consulta=buscaServicos($registro, 'id', 'igual', 'id');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Mostrar Erro
			$msg="Registro não foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso", $msg, $url, 760);
		}
		else {
			#atribuir valores
 			$descricao=resultadoSQL($consulta, 0, 'descricao');
 			$nome=resultadoSQL($consulta, 0, 'nome');
			$valor=number_format(resultadoSQL($consulta, 0, 'valor'),2,',','.');
			$idTipoCobranca=resultadoSQL($consulta, 0, 'idTipoCobranca');
			$status=resultadoSQL($consulta, 0, 'idStatusPadrao');
			$status=formSelectStatusServico($status, '','check');
			
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
					<input type=hidden name=matriz[id] value=$registro>
					<input type=hidden name=acao value=$acao>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Nome: </b>";
					htmlFechaColuna();
					itemLinhaForm($nome, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Descrição: </b>";
					htmlFechaColuna();
					itemLinhaForm($descricao, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Tipo de Cobrança: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectTipoCobranca($idTipoCobranca, 'tipoCobranca','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Valor: </b>";
					htmlFechaColuna();
					itemLinhaForm($valor, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Status Padrão: </b>";
					htmlFechaColuna();
					itemLinhaForm($status[descricao], 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				
				# Verifica se tem Serviços Planos dependentes
				$servicosPlano = contaConsulta( buscaServicosPlanos($registro, 'idServico', 'igual', 'id') );
				# se não tiver registros dependentes dele, então será exibido o botão que permite confirmar a exclusão do mesmo
				if(!$servicosPlano) {
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "&nbsp;";
						htmlFechaColuna();
						$texto="<input type=submit name=matriz[bntExcluir] value=Excluir class=submit>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
			fechaTabela();
			# se tem registros dependentes é exibido um aviso
			if( $servicosPlano ) {
				echo "<br>";
				aviso('Alerta', 'Este serviço não pode ser excluído porque ele está sendo utilizado por algum plano!', "?modulo=$modulo&sub=$sub&acao=listar", 600);
			}
		} #fecha alteracao
	} #fecha form - !$bntAlterar
	
	# Alteração - bntAlterar pressionado
	elseif($matriz[bntExcluir]) {
		# Conferir campos
		
		# Cadastrar em banco de dados
		$grava=dbServico($matriz, 'excluir');
		
		# Verificar 
		if($grava) {
			# Mensagem de aviso
			$msg="Registro Excluído com Sucesso!";
			$url="?modulo=$modulo&sub=$sub&acao=listar";
			aviso("Aviso", $msg, $url, 760);
		}
		else {
			# Mensagem de aviso
			$msg="Erro ao excluir Serviço!";
			$url="?modulo=$modulo&sub=$sub&acao=listar";
			aviso("Aviso", $msg, $url, 760);
		}
	} #fecha bntAlterar
	
} # fecha funcao de Exclusão





# Funcao para visualização
function verServico($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# ERRO - Registro não foi informado
	if(!$registro) {
		# ERRO
	}
	# Form de inclusao
	elseif($registro) {
	
		# Buscar Valores
		$consulta=buscaServicos($registro, 'id', 'igual', 'id');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Mostrar Erro
			$msg="Registro não foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso", $msg, $url, 760);
		}
		else {
			#atribuir valores
 			$descricao=resultadoSQL($consulta, 0, 'descricao');
 			$nome=resultadoSQL($consulta, 0, 'nome');
			$valor=number_format(resultadoSQL($consulta, 0, 'valor'),2,',','.');
			$idTipoCobranca=resultadoSQL($consulta, 0, 'idTipoCobranca');
			
			# Motrar tabela de busca
			novaTabela2("[Informações do Serviço]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, 'ver', $registro);				
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
					itemLinhaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Nome: </b>";
					htmlFechaColuna();
					itemLinhaForm($nome, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Descrição: </b>";
					htmlFechaColuna();
					itemLinhaForm($descricao, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Tipo de Cobrança: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectTipoCobranca($idTipoCobranca, 'tipoCobranca','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b>Valor: </b>";
					htmlFechaColuna();
					itemLinhaForm($valor, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();				

		} #fecha alteracao
	} #fecha 
} # fecha funcao de Exclusão



# função de forma para seleção de tipo de pessoas
function formSelectServicos($servico, $campo, $tipo) {

	if($tipo=='check') {
	
		$consulta=buscaServicos($servico,'id','igual','nome');
		
		if($consulta && contaConsulta($consulta)>0) {
			$id=resultadoSQL($consulta, 0, 'id');
			$idTipoCobranca=resultadoSQL($consulta, 0, 'idTipoCobranca');
			$nome=resultadoSQL($consulta, 0, 'nome');
			$descricao=resultadoSQL($consulta, 0, 'descricao');
			$valor=resultadoSQL($consulta, 0, 'valor');

			$retorno=$nome;
		}
	
	}
	elseif(($tipo=='form') || $tipo=='formnochange') {
	
		$consulta=buscaServicos('','','todos','nome');
		
		if($consulta && contaConsulta($consulta)>0) {
			if ($tipo=='formnochange' || $tipo=='formnulos')
				$retorno="<select name=matriz[$campo]>";
			else 
				$retorno="<select name=matriz[$campo] onChange=javascript:submit();>";
			
			for($i=0;$i<contaConsulta($consulta);$i++) {
			
				$id=resultadoSQL($consulta, $i, 'id');
				$idTipoCobranca=resultadoSQL($consulta, $i, 'idTipoCobranca');
				$nome=resultadoSQL($consulta, $i, 'nome');
				$descricao=resultadoSQL($consulta, $i, 'descricao');
				$valor=resultadoSQL($consulta, $i, 'valor');
				
				if($servico==$id) $opcSelect='selected';
				else $opcSelect='';
				
				$retorno.="\n<option value=$id $opcSelect>$nome";
			}
			
			$retorno.="</select>";
		}
	
	}
	
	elseif($tipo=='multi') {
	
		$consulta=buscaServicos('','','todos','nome');
		
		if($consulta && contaConsulta($consulta)>0) {
			
			$retorno="<select multiple size=6 name=matriz[$campo][]>";
			
			for($i=0;$i<contaConsulta($consulta);$i++) {
			
				$id=resultadoSQL($consulta, $i, 'id');
				$idTipoCobranca=resultadoSQL($consulta, $i, 'idTipoCobranca');
				$nome=resultadoSQL($consulta, $i, 'nome');
				$descricao=resultadoSQL($consulta, $i, 'descricao');
				$valor=resultadoSQL($consulta, $i, 'valor');
				
				if($servico==$id) $opcSelect='selected';
				else $opcSelect='';
				
				$retorno.="\n<option value=$id $opcSelect>$nome";
			}
			
			$retorno.="</select>";
		}
	
	}
	
	return($retorno);
	
}



# Função para checagem de informações do serviço
function checkServico($servico) {

	$consulta=buscaServicos($servico, 'id','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		$retorno[id]=resultadoSQL($consulta, 0, 'id');
		$retorno[idTipoCobranca]=resultadoSQL($consulta, 0, 'idTipoCobranca');
		$retorno[nome]=resultadoSQL($consulta, 0, 'nome');
		$retorno[descricao]=resultadoSQL($consulta, 0, 'descricao');
		$retorno[valor]=resultadoSQL($consulta, 0, 'valor');
	}

	return($retorno);	
}

?>
