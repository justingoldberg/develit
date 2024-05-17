<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 10/01/2005
# Ultima alteração: 11/01/2005
#    Alteração No.: 002
#
# Função:
#    Painel - Funções para cadastro

# Função para cadastro
function empresas($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessPassPhrase;
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');


	if(!$permissao[admin] && !$permissao[visualizar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		# Topo da tabela - Informações e menu principal do Cadastro
		novaTabela2("[Cadastro de Empresas]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 4);
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
					echo "<br><img src=".$html[imagem][cadastro]." border=0 align=left><b class=bold>Empresas</b>
					<br><span class=normal10>Cadastro de <b>empresas</b> e controle de relação máquinas/empresas.</span>";
				htmlFechaColuna();			
				$texto=htmlMontaOpcao("<br>Adicionar", 'incluir');
				itemLinha($texto, "?modulo=$modulo&sub=&acao=adicionar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Procurar", 'procurar');
				itemLinha($texto, "?modulo=$modulo&sub=&acao=procurar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Listar", 'listar');
				itemLinha($texto, "?modulo=$modulo&sub=&acao=listar", 'center', $corFundo, 0, 'normal');
			fechaLinhaTabela();
		fechaTabela();
	
		if(!$sub) {
			# Inclusão
			if($acao=="adicionar") {
				echo "<br>";
				adicionarEmpresas($modulo, $sub, $acao, $registro, $matriz);
			}
			
			# Alteração
			elseif($acao=="alterar") {
				echo "<br>";
				alterarEmpresas($modulo, $sub, $acao, $registro, $matriz);
			}
			
			# Exclusão
			elseif($acao=="excluir") {
				echo "<br>";
				excluirEmpresas($modulo, $sub, $acao, $registro, $matriz);
			}
		
			# Listar / Buscar
			elseif($acao=="listar" || $acao=='procurar' || !$acao) {
				echo "<br>";
				procurarEmpresas($modulo, $sub, $acao, $registro, $matriz);
			} #fecha listagem de servicos
			
			# Detalhes
			elseif($acao=='detalhes') {
				echo "<br>";
				verEmpresa($modulo, $sub, $acao, $registro, $matriz);
			}
		}
	}
} #fecha menu principal 


# função de busca 
function buscaEmpresas($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[Empresas] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[Empresas] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[Empresas] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[Empresas] WHERE $texto ORDER BY $ordem";
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




# Funcao para cadastro 
function adicionarEmpresas($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;

	# Form de inclusao
	if(!$matriz[bntAdicionar] || !$matriz[nome] || !$matriz[idPessoaTipo]) {
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
				<input type=hidden name=registro value=$registro>&nbsp;
				";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			#Inicio com interação
			if($_REQUEST['integraIsp']==true){
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Empresa do ISP-IT: </b><br>
						<span class=normal10>Nome da Empresa cadastrada no ISP-IT</span>";
					htmlFechaColuna();
					itemLinhaForm(formSelectAddPessoaTipo($matriz[idPessoaTipo],'idPessoaTipo','form', 4), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			#Fim com interação
			}else{
			#Inicio sem interação
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Nome da Empresa: </b><br>
						<span class=normal10>Nome da Empresa</span>";
					htmlFechaColuna();
					itemLinhaForm(formInputNomeEmpresa($matriz[nome],'nome','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Tipo da Empresa: </b><br>
						<span class=normal10>Tipo da Empresa</span>";
					htmlFechaColuna();
					itemLinhaForm(formSelectTipoPessoa($matriz[idPessoaTipo], 'idPessoaTipo','form',4), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			#Fim sem interação
			}
			if($matriz[bntSelecionar] && $matriz[idPessoaTipo]) {
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Empresa do Ticket-IT: </b><br>
						<span class=normal10>Nome da máquina cadastrada no Ticket-IT</span>";
					htmlFechaColuna();
					if($matriz[idPessoaTipo]) $matriz[nome]=$matriz[nome];
					$texto="<input type=text name=matriz[nome] size=60 value='$matriz[nome]'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit name=matriz[bntAdicionar] value=Adicionar class=submit>";
					itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
			}
			else {
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit name=matriz[bntSelecionar] value=Selecionar class=submit>";
					itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
			}
		fechaTabela();
	} #fecha form
	elseif($matriz[bntAdicionar]) {
		# Conferir campos
		if($matriz[nome]) {
			# Buscar por prioridade
			# Cadastrar em banco de dados
			$grava=dbEmpresa($matriz, 'incluir');
			
			# Verificar inclusão de registro
			if($grava) {
				# acusar falta de parametros
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				avisoNOURL("Aviso", $msg, 400);
				
				echo "<br>";
				procurarEmpresas($modulo, $sub, 'listar', $matriz[id], '');
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
function dbEmpresa($matriz, $tipo) {

	global $conn, $tb, $modulo, $sub, $acao;
	
	# Sql de inclusão
	if($tipo=='incluir') {
		$sql="INSERT INTO $tb[Empresas] (idPessoaTipo, nome) 
		VALUES
		('$matriz[idPessoaTipo]', '$matriz[nome]')";
	} #fecha inclusao
	
	elseif($tipo=='excluir') {
		$sql="DELETE FROM $tb[Empresas] WHERE id=$matriz[id]";
	}
	
	# Alterar
	elseif($tipo=='alterar') {
		# Verificar se prioridade existe
		$sql="
			UPDATE 
				$tb[Empresas] 
			SET 
				nome='$matriz[nome]',
				idPessoaTipo='$matriz[idPessoaTipo]'
			WHERE 
				id=$matriz[id]";
	}
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
	
} # fecha função de gravação em banco de dados



# Função para procura de serviço
function procurarEmpresas($modulo, $sub, $acao, $registro, $matriz)
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
			<input type=hidden name=nulo value=nulo>
			<input type=text name=matriz[txtProcurar] size=40 value='$matriz[txtProcurar]'>
			<input type=submit name=matriz[bntProcurar] value=Procurar class=submit>
			";
			itemLinhaForm($texto, 'left','middle', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
	fechaTabela();

	# Caso botão procurar seja pressionado
	if( ($matriz[txtProcurar] && $matriz[bntProcurar]) || $acao=='listar' || !$acao) {
		#buscar registros
		if($acao=='listar' || !$acao) $consulta=buscaEmpresas('','','todos','nome ASC');
		else $consulta=buscaEmpresas("upper(nome) like '%$matriz[txtProcurar]%'",$campo, 'custom','nome');

		echo "<br>";

		novaTabela("[Resultados]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
	
		if(!$consulta || contaConsulta($consulta)==0 ) {
			# Não há registros
			itemTabelaNOURL('Não foram encontrados registros cadastrados', 'left', $corFundo, 3, 'txtaviso');
		}
		elseif($consulta && contaConsulta($consulta)>0 && (!$registro || is_numeric($registro)) ) {	
		
			if($acao != 'listar' && $acao) itemTabelaNOURL('Registros encontrados procurando por ('.$matriz[txtProcurar].'): '.contaConsulta($consulta).' registro(s)', 'left', $corFundo, 3, 'txtaviso');

			# Paginador
			$urlADD="&textoProcurar=".$matriz[txtProcurar];
			# Paginador
			paginador($consulta, contaConsulta($consulta), $limite[lista][empresas], $registro, 'normal10', 3, $urlADD);
		
			# Cabeçalho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Nome', 'center', '50%', 'tabfundo0');
				if($_REQUEST['integraIsp']==true) itemLinhaTabela('Cliente', 'center', '30%', 'tabfundo0');
				itemLinhaTabela('Opções', 'center', '50%', 'tabfundo0');
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

			$limite=$i+$limite[lista][empresas];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$nome=resultadoSQL($consulta, $i, 'nome');
				$idPessoaTipo=resultadoSQL($consulta, $i, 'idPessoaTipo');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($nome, 'left', '50%', 'normal10');
					if($_REQUEST['integraIsp']==true)
						itemLinhaTabela(formSelectAddPessoaTipo($idPessoaTipo,'','check'), 'left', '30%', 'normal10');
					//else
					//	itemLinhaTabela(formSelectPessoaEdita($id,'','check'), 'left', '30%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '20%', 'normal8');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem
	} # fecha botão procurar
} #fecha funcao de  procurar 



# Funcao para alteração
function alterarEmpresas($modulo, $sub, $acao, $registro, $matriz) {
	
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $isp;
	
	# ERRO - Registro não foi informado
	if(!$registro) {
		# ERRO
	}
	# Form de inclusao
	elseif(!$matriz[bntAlterar] || !$matriz[nome]) {
		
		$consulta=buscaEmpresas($registro, 'id', 'igual', 'id');	
		if(!$consulta || contaConsulta($consulta)==0) {
			# Mostrar Erro
			$msg="Registro não foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso", $msg, $url, 760);
		}
		else {
			#atribuir valores
			if(!$matriz[bntAlterar]) {
				if(!$matriz[idPessoaTipo]) {
					$matriz[nome]=resultadoSQL($consulta, 0, 'nome');
					$matriz[idPessoaTipo]=resultadoSQL($consulta, 0, 'idPessoaTipo');
				}
				else {
					if($_REQUEST['integraIsp']==true){
					#Inicio com interação
						$connISP=conectaISP();
						$sql="SELECT
							$isp[db].PessoasTipos.id, 
							$isp[db].Pessoas.nome
							FROM 
							$isp[db].Pessoas, 
							$isp[db].PessoasTipos 
							WHERE
							$isp[db].Pessoas.id = $isp[db].PessoasTipos.idPessoa
							AND
							$isp[db].PessoasTipos.id = $matriz[idPessoaTipo]";
			
						$consulta=consultaSQL($sql, $connISP);
						//$matriz[nome]=resultadoSQL($consulta, 0, 'nome');
						$matriz[idPessoaTipo]=resultadoSQL($consulta, 0, 'id');
					#Fim com interação
					}else{
					#Inicio sem interação
						$sql="SELECT
							Empresas.id, 
							Empresas.nome,
							Empresa.idPessoaTipo
							FROM 
							Empresas
							WHERE
							Empresas.id = $registro";
						$consulta=consultaSQL($sql, $connISP);
						//$matriz[nome]=resultadoSQL($consulta, 0, 'nome');
						$matriz[idPessoaTipo]=resultadoSQL($consulta, 0, 'idPessoaTipo');
					#Fim sem interação
					}
				}
					
			}
			
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
					<input type=hidden name=registro value=$registro>&nbsp;
					";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				if($_REQUEST['integraIsp']==true){
				#Inicio com interação
					novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Empresa do ISP-IT: </b><br>
						<span class=normal10>Nome da Empresa cadastrada no ISP-IT</span>";
					htmlFechaColuna();
					itemLinhaForm(formSelectAddPessoaTipo($matriz[idPessoaTipo],'idPessoaTipo','form', 4), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Empresa do Ticket-IT: </b><br>
							<span class=normal10>Nome da máquina cadastrada no Ticket-IT</span>";
						htmlFechaColuna();
	//					if($matriz[idPessoaTipo]) $matriz[nome]=formSelectAddPessoaTipo($matriz[idPessoaTipo],'','check');
						$texto="<input type=text name=matriz[nome] size=60 value='$matriz[nome]'>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				#Fim com interação
				}else{
				#Inicio sem interação
						novaLinhaTabela($corFundo, '100%');
							htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
								echo "<b class=bold10>Nome da Empresa: </b><br>
								<span class=normal10>Nome da Empresa</span>";
							htmlFechaColuna();
							itemLinhaForm(formInputNomeEmpresa($matriz[nome],'nome','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
						novaLinhaTabela($corFundo, '100%');
							htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
								echo "<b class=bold10>Tipo da Empresa: </b><br>
								<span class=normal10>Tipo da Empresa</span>";
							htmlFechaColuna();
							itemLinhaForm(formSelectTipoPessoa($matriz[idPessoaTipo], 'idPessoaTipo','form',0), 'left', 'top', $corFundo, 0, 'tabfundo1');
						fechaLinhaTabela();
				#Fim sem interação
				}
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit name=matriz[bntAlterar] value=Alterar class=submit>";
					itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();				

		} #fecha alteracao
	} #fecha form - !$bntAlterar
	
	# Alteração - bntAlterar pressionado
	elseif($matriz[bntAlterar]) {
		# Conferir campos
		# continuar
		# Cadastrar em banco de dados
		$matriz[id]=$registro;
		$grava=dbEmpresa($matriz, 'alterar');
		
		# Verificar inclusão de registro
		if($grava) {
			# Mensagem de aviso
			$msg="Registro Gravado com Sucesso!";
			$url="?modulo=$modulo&sub=$sub&acao=listar";
			avisoNOURL("Aviso", $msg, 400);
			
			echo "<br>";
			$acao=listar;
			procurarEmpresas($modulo, $sub, $acao, 0, '');
			
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
function excluirEmpresas($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# ERRO - Registro não foi informado
	if(!$registro) {
		# Mostrar Erro
		$msg="Registro não foi encontrado!";
		avisoNOURL("Aviso", $msg, 400);
	}
	# Form de inclusao
	elseif($registro && !$matriz[bntExcluir]) {
	
		# Buscar Valores
		$consulta=buscaEmpresas($registro, 'id', 'igual', 'id');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Mostrar Erro
			$msg="Registro não foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso", $msg, $url, 760);
		}
		else {
			#atribuir valores
 			$id=resultadoSQL($consulta, 0, 'id');
 			$nome=resultadoSQL($consulta, 0, 'nome');
			$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
			
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
					<input type=hidden name=registro value=$registro>&nbsp;
					";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();				
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Nome: </b>";
					htmlFechaColuna();
					itemLinhaForm($nome, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Empresa: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectAddPessoaTipo($idPessoaTipo,'','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit name=matriz[bntExcluir] value=Excluir class=submit>";
					itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		} #fecha alteracao
	} #fecha form - !$bntExcluir
	
	# Alteração - bntExcluir pressionado
	elseif($matriz[bntExcluir]) {
		# Cadastrar em banco de dados
		$matriz[id]=$registro;
		$grava=dbEmpresa($matriz, 'excluir');
				
		# Verificar inclusão de registro
		if($grava) {
			# Mensagem de aviso
			$msg="Registro excluído com Sucesso!";
			$url="?modulo=$modulo&sub=$sub&acao=listar";
			avisoNOURL("Aviso", $msg, 400);
			
			echo "<br>";
			procurarEmpresas($modulo, $sub, 'listar', 0, $matriz);
		}
		
	} #fecha bntExcluir
	
} #fecha exclusao 



# Visualização de maquinas
function verEmpresa($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $html;

	# ERRO - Registro não foi informado
	if(!$registro) {
		# Mostrar Erro
		$msg="Registro não foi encontrado!";
		$url="?modulo=$modulo&sub=$sub&acao=$acao";
		aviso("Aviso", $msg, $url, 760);
	}
	# Form de inclusao
	elseif($registro) {
	
		# Buscar Valores
		$consulta=buscaMaquinas($registro, 'id', 'igual', 'id');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Mostrar Erro
			$msg="Registro não foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso", $msg, $url, 760);
		}
		else {
			#atribuir valores
 			$nome=resultadoSQL($consulta, 0, 'nome');
			$cliente=resultadoSQL($consulta, 0, 'cliente');
			$ip=resultadoSQL($consulta, 0, 'ip');
			$data=resultadoSQL($consulta, 0, 'data');
			$obs=resultadoSQL($consulta, 0, 'obs');
			
			# Motrar tabela de busca
			novaTabela2("[Visualização de Máquina]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, $registro);				
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro>&nbsp;
					";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();				
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Nome: </b>";
					htmlFechaColuna();
					itemLinhaForm($nome, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<b class=bold10>IP: </b>";
					itemLinhaForm($texto, 'right', 'top', $corFundo, 0, 'tabfundo1');
					itemLinhaForm($ip, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<b class=bold10>Cliente: </b>";
					itemLinhaForm($texto, 'right', 'top', $corFundo, 0, 'tabfundo1');
					itemLinhaForm($cliente, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					$texto="<b class=bold10>Data de Cadastro: </b>";
					itemLinhaForm($texto, 'right', 'top', $corFundo, 0, 'tabfundo1');
					itemLinhaForm(converteData($data,'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				if(strlen(trim($obs))>0) {
					novaLinhaTabela($corFundo, '100%');
						$texto="<b class=bold10>Observações: </b>";
						itemLinhaForm($texto, 'right', 'top', $corFundo, 0, 'tabfundo1');
						itemLinhaForm(nl2br($obs), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
			fechaTabela();
		} #fecha alteracao
	} #fecha form - !$bntExcluir
}

function formSelectEmpresas($registro, $campo, $tipo, $indexform=0) {
	global $isp;
	
	if($tipo=='form') {
		$consulta=buscaEmpresas('','','todos','nome');
	}
	elseif($tipo=='check') {
		$consulta=buscaEmpresas($registro,'id','igual','nome');
	}
	
	if($consulta && contaConsulta($consulta)>0) {
		if($tipo=='form') {
			
			if($indexform>0) {
				$tmpJS="onChange=javascript:submit();";
			}
			
			$retorno="<select name=matriz[$campo] $tmpJS>\n";
			
			for($a=0;$a<contaConsulta($consulta);$a++) {
				
				$id=resultadoSQL($consulta, $a, 'id');
				$nome=substr(resultadoSQL($consulta, $a, 'nome'),0,40);
				
				if($registro==$id) $opcSelect='selected';
				else $opcSelect='';
				
				# Verificar se registro já está em banco de dados de Empresas
				$retorno.="<option value=$id $opcSelect>$nome\n";
			}
			
			$retorno.="</select>";
		}
		elseif($tipo=='check') {
			$retorno=resultadoSQL($consulta, 0, 'nome');
		}
	}
	
	return($retorno);
}

function formInputNomeEmpresa($valor, $campo, $tipo) {

	if($tipo=='form') {
		$retorno="<input type=text name=matriz[$campo] size=20 value='$valor'> <b>"."</b>";
	}
	elseif($tipo=='check') {
		
	}
	return($retorno);
}

function formSelectTipoPessoa($registro,$campo, $tipo, $indexform=0){
	
	if($indexform>0) {
		$tmpJS="onChange=javascript:submit();";
	}
	
	$retorno="<select name=matriz[$campo] $tmpJS>\n";
		
	if($registro=="1") $opcSelect='selected';
		else $opcSelect='';
		
	$retorno.="<option value=1 $opcSelect>Clientes</option>";
		
	if($registro=="2") $opcSelect='selected';
		else $opcSelect='';
		
	$retorno.="<option value=2 $opcSelect>Fornecedores</option>";
		
	if($registro=="3") $opcSelect='selected';
		else $opcSelect='';
		
	$retorno.="<option value=3 $opcSelect>Prospects</option>";
		
	if($registro=="4") $opcSelect='selected';
		else $opcSelect='';
	
	$retorno.="<option value=4 $opcSelect>Parceiros</option>";
	
	if($registro=="5") $opcSelect='selected';
		else $opcSelect='';
	
	$retorno.="<option value=5 $opcSelect>Bancos</option>";
	
	if($registro=="6") $opcSelect='selected';
		else $opcSelect='';

	$retorno.="<option value=6 $opcSelect>Condominios</option>";
	
	if($registro=="7") $opcSelect='selected';
		else $opcSelect='';

	$retorno.="<option value=7 $opcSelect>POP</option>";
	
	$retorno.="</select>";

	return $retorno;
}

function formSelectPessoaEdita($registro, $campo, $tipo, $indexform=0) {

	global $conn;

		$sql="
			SELECT
				Empresas.id,
				Empresas.nome,
				Empresas.idPessoaTipo
			FROM 
				Empresas
			WHERE
				Empresas.id = $registro
		";

	
	$consulta=consultaSQL($sql, $conn);
	$retorno=resultadoSQL($consulta, 0, 'nome');

	return($retorno);
}

?>
