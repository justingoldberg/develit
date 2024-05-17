<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 12/01/2004
# Ultima alteração: 29/01/2004
#    Alteração No.: 004
#
# Função:
#    Ocorrências - Funções para configurações

function ocorrencias($modulo, $sub, $acao, $registro, $matriz) {

	global $html, $ocorrencias, $corFundo, $corBorda, $sessCadastro;
	
	### Menu principal - usuarios logados apenas
	novaTabela2("[Ocorrências]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 3);
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
				echo "<br><img src=".$html[imagem][ocorrencias]." border=0 align=left >
				<b class=bold>Ocorrências</b>
				<br><span class=normal10>A seção de <b>ocorrências</b> permite a manutenção de 
				suporte ténico, documentação e observações de processos internos, facilitando a
				interação entre as áreas.</span>";
			htmlFechaColuna();			
			$texto=htmlMontaOpcao("<br>Adicionar", 'incluir');
			itemLinha($texto, "?modulo=ocorrencias&acao=adicionar", 'center', $corFundo, 0, 'normal');
			$texto=htmlMontaOpcao("<br>Histórico", 'historico');
			itemLinha($texto, "?modulo=ocorrencias&acao=listar", 'center', $corFundo, 0, 'normal');
		fechaLinhaTabela();
	fechaTabela();	


	if(!$acao || $acao=='listar' || $acao=='listartodos' || $acao=='procurar') {
		# Gravar Sessão
		if(is_numeric($registro)) $sessCadastro[idPessoaTipo]=$registro;
	
		echo "<br>";
		procurarOcorrencias($modulo, $sub, $acao, $registro, $matriz);
	}
	else {
		echo "<br>";
		
		if($acao=='adicionar') adicionarOcorrencias($modulo, $sub, $acao, $registro, $matriz);
		elseif($acao=='alterar') alterarOcorrencias($modulo, $sub, $acao, $registro, $matriz);
		elseif($acao=='excluir') excluirOcorrencias($modulo, $sub, $acao, $registro, $matriz);
		elseif($acao=='abrir') abrirOcorrencias($modulo, $sub, $acao, $registro, $matriz);
		elseif($acao=='reabrir') reabrirOcorrencias($modulo, $sub, $acao, $registro, $matriz);
		elseif($acao=='comentar') comentarOcorrencias($modulo, $sub, $acao, $registro, $matriz);
		elseif($acao=='fechar') fecharOcorrencias($modulo, $sub, $acao, $registro, $matriz);
		elseif($acao=='cancelar') cancelarOcorrencias($modulo, $sub, $acao, $registro, $matriz);
		elseif($acao=='historico') historicoOcorrencias($modulo, $sub, $acao, $registro, $matriz);
	}
	
	
	echo "<script>location.href='#ancora';</script>";	
}



# função de busca 
function buscaOcorrencias($texto, $campo, $tipo, $ordem) {

	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[Ocorrencias] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[Ocorrencias] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[Ocorrencias] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[Ocorrencias] WHERE $texto ORDER BY $ordem";
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




# Função para procura de serviço
function procurarOcorrencias($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $tb, $corFundo, $corBorda, $html, $limite, $textoProcurar, $sessCadastro;
	
	# Mostrar pessoal
	verPessoas($modulo, $sub, $acao, $sessCadastro[idPessoaTipo], $matriz);
	echo "<br>";
	
	# Atribuir valores a variável de busca
	if($textoProcurar) {
		$matriz[bntProcurar]=1;
		$matriz[txtProcurar]=$textoProcurar;
	} #fim da atribuicao de variaveis
	
	# Motrar tabela de busca
	novaTabela2("[Procurar Ocorrências]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('30%', 'right', $corFundo, 0, 'tabfundo1');
			echo "<b>Procurar por:</b>";
			htmlFechaColuna();
			$texto="
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=procurar>
			<input type=hidden name=registro value=$sessCadastro[idPessoaTipo]>
			<input type=text name=matriz[txtProcurar] size=40 value='$matriz[txtProcurar]'>
			<input type=submit name=matriz[bntProcurar] value=Procurar class=submit>";
			itemLinhaForm($texto, 'left','middle', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
	fechaTabela();

	# Caso botão procurar seja pressionado
	if(($matriz[txtProcurar] && $matriz[bntProcurar] && $acao=='procurar') || $acao=='listar' || $acao=='listartodos' || !$acao) {
		#buscar registros
		
		if($acao=='listartodos') {
			$consulta=buscaOcorrencias("idPessoaTipo=$sessCadastro[idPessoaTipo]", '', 'custom','data DESC');
		}
		elseif($acao=='listar' || !$acao) {
			$consulta=buscaOcorrencias("idPessoaTipo=$sessCadastro[idPessoaTipo] AND status!='F' AND status!='C'", '', 'custom','data DESC');
		}
		elseif($acao=='procurar') {
			$consulta=buscaOcorrencias("(upper(descricao) like '%$matriz[txtProcurar]%' OR upper(nome) like '%$matriz[txtProcurar]%') and idPessoaTipo={$sessCadastro['idPessoaTipo']}",$campo, 'custom','data DESC');
		}

		echo "<br>";

		novaTabela("[Resultados]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);

		# Opcoes Adicionais
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('65%', 'left', $corFundo, 5, 'tabfundo1');
				novaTabelaSH("center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
					menuOpcAdicional($modulo, $sub, 'adm', $sessCadastro[idPessoaTipo]);
				fechaTabela();
			htmlFechaColuna();
		fechaLinhaTabela();
	
		if(!$consulta || contaConsulta($consulta)==0 ) {
			# Não há registros
			itemTabelaNOURL('Não foram encontrados ocorrências cadastrados', 'left', $corFundo, 5, 'txtaviso');
		}
		elseif($consulta && contaConsulta($consulta)>0 && (!$registro || is_numeric($registro)) ) {	
		
			if($acao=='procurar') {
				itemTabelaNOURL('Ocorrências encontrados procurando por ('.$matriz[txtProcurar].'): '.contaConsulta($consulta).' registro(s)', 'left', $corFundo, 5, 'txtaviso');
			}

			# Paginador
			$urlADD="&textoProcurar=$matriz[txtProcurar]";
			//paginador($consulta, contaConsulta($consulta), $limite[lista][ocorrencias], $registro, 'normal', 5, $urlADD);
			
			# Cabeçalho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Ocorrência', 'center', '30%', 'tabfundo0');
				itemLinhaTabela('Prioridade', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Status', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Data', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Opções', 'center', '40%', 'tabfundo0');
			fechaLinhaTabela();

			/*# Setar registro inicial
			if(!$registro) {
				$i=0;
			}
			elseif($registro && is_numeric($registro) ) {
				$i=$registro;
			}
			else {
				$i=0;
			}*/

			//$limite=$i+$limite[lista][ocorrencias];
			
			//while($i < contaConsulta($consulta) && $i < $limite) {
			for($i=0;$i<contaConsulta($consulta);$i++) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$nome=resultadoSQL($consulta, $i, 'nome');
				$data=resultadoSQL($consulta, $i, 'data');
				$status=resultadoSQL($consulta, $i, 'status');
				$idPrioridade=resultadoSQL($consulta, $i, 'idPrioridade');
				
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				
				# Verificar status
				if($status=='N') $opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=abrir&registro=$id>Abrir</a>",'abrir');
				else {
					if($status=='A' || $status=='R') {
						$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=fechar&registro=$id>Fechar</a>",'fechar');
						$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=cancelar&registro=$id>Cancelar</a>",'cancelar');
						$opcoes.="<br>";
						$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=comentar&registro=$id>Comentar</a>",'comentar');
					}
					elseif($status=='F') $opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=reabrir&registro=$id>Re-Abrir</a>",'abrir');
					elseif($status=='P') $opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=Fechar&registro=$id>Fechar</a>",'fechar');
				}
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=historico&registro=$id>Historico</a>",'historico');
				
				$prioridade=formSelectPrioridade($idPrioridade,'','check');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($nome, 'left', '40%', 'normal10');
					itemLinhaTabela("<font color=$prioridade[cor]>$prioridade[nome]</font>", 'center', '10%', 'bold10');
					itemLinhaTabela(formSelectStatusOcorrencia($status,'','check'), 'center', '10%', 'normal8');
					itemLinhaTabela(converteData($data,'banco','form'), 'center', '10%', 'normal8');
					itemLinhaTabela($opcoes, 'left nowrap', '30%', 'normal8');
				fechaLinhaTabela();
				
			} #fecha laco de montagem de tabela
		} #fecha listagem
		
		fechaTabela();
	} # fecha botão procurar
} #fecha funcao de  procurar 



# Funcao para cadastro 
function adicionarOcorrencias($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro, $sessLogin;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');

	if(!$permissao[admin] && !$permissao[adicionar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		# Form de inclusao
		if(!$matriz[bntAdicionar] || !$matriz[descricao] || !$matriz[nome]) {
			verPessoas($modulo, $sub, '', $sessCadastro[idPessoaTipo], $matriz);
			echo "<br>";
		
			# Motrar tabela de busca
			novaTabela2("[Adicionar Ocorrência]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, $sessCadastro[idPessoaTipo]);
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$matriz[registro]>
					<input type=hidden name=matriz[idPessoaTipo] value=$sessCadastro[idPessoaTipo]>
					&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Título: </b><br>
						<span class=normal10>Título da ocorrência</span>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[nome] size=60 value='$matriz[nome]'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Descrição: </b><br>
						<span class=normal10>Descrição da ocorrência</span>";
					htmlFechaColuna();
					$texto="<textarea name=matriz[descricao] cols=60 rows=10>$matriz[descricao]</textarea>";
					itemLinhaForm($texto, 'left', 'middle', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Prioridade: </b><br>
						<span class=normal10>Prioridade da ocorrência</span>";
					htmlFechaColuna();
					itemLinhaForm(formSelectPrioridade($matriz[idPrioridade],'idPrioridade','form'), 'left', 'middle', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Abrir Ocorrência: </b><br>
						<span class=normal10>Selecione esta opção para automaticamente abria a ocorrência</span>";
					htmlFechaColuna();
					$texto="<input type=checkbox name=matriz[auto_abrir] value=S>&nbsp;<span class=txtaviso>(Abrir ocorrência)</span>";
					itemLinhaForm($texto, 'left', 'middle', $corFundo, 0, 'tabfundo1');
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
			if($matriz[nome] && $matriz[descricao] && $matriz[idPrioridade]) {
				# Buscar por prioridade
				# Cadastrar em banco de dados
				$matriz[idUsuario]=buscaIDUsuario($sessLogin[login],'login','igual','id');
				$grava=dbOcorrencia($matriz, 'incluir');
				
				# Verificar inclusão de registro
				if($grava) {
					# acusar falta de parametros
					# Mensagem de aviso
					$msg="Registro Gravado com Sucesso!";
					avisoNOURL("Aviso", $msg, 400);
					
					# Listagem de ocorrências
					echo "<br>";
					procurarOcorrencias($modulo, $sub, 'listar', $registro, $matriz);
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
	}
} # fecha funcao de inclusao




# Função para gravação em banco de dados
function dbOcorrencia($matriz, $tipo) {

	global $conn, $tb, $modulo, $sub, $acao;
	
	$data=dataSistema();
	
	# Sql de inclusão
	if($tipo=='incluir') {
	
		if($matriz[auto_abrir]=='S') $status='A';
		else $status='N';
		$sql="INSERT INTO $tb[Ocorrencias] VALUES (0,
			'$matriz[idPessoaTipo]',
			'$matriz[idServicoPlano]',
			'$matriz[idUsuario]',
			'$matriz[idPrioridade]',
			'$data[dataBanco]',
			'$matriz[nome]',
			'$matriz[descricao]',
			'$status'
		)";
	} #fecha inclusao
	
	elseif($tipo=='excluir') {
		$sql="DELETE FROM $tb[Ocorrencias] WHERE id=$matriz[id]";
	}
	
	elseif($tipo=='excluirtodos') {
		$sql="DELETE FROM $tb[Ocorrencias] WHERE idPessoaTipo=$matriz[id]";
	}
	
	# Alterar
	elseif($tipo=='alterar') {
		# Verificar se prioridade existe
		$sql="
			UPDATE 
				$tb[Ocorrencias] 
			SET 
				nome='$matriz[nome]',
				descricao='$matriz[descricao]',
				idPrioridade='$matriz[idPrioridade]'
		WHERE id=$matriz[id]";
	}
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
	
} # fecha função de gravação em banco de dados



# Funcao para alteração
function alterarOcorrencias($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin;

	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');

	if(!$permissao[admin] && !$permissao[alterar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		
		# ERRO - Registro não foi informado
		if(!$registro) {
			# ERRO
		}
		# Form de inclusao
		elseif($registro && !$matriz[bntAlterar]) {
		
			# Buscar Valores
			$consulta=buscaOcorrencias($registro, 'id', 'igual', 'id');
			if(!$consulta || contaConsulta($consulta)==0) {
				# Mostrar Erro
				$msg="Registro não foi encontrado!";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				aviso("Aviso", $msg, $url, 760);
			}
			else {
				 #atribuir valores
	 			 $id=resultadoSQL($consulta, 0, 'id');
	 			 $idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
	 			 $idServicoPlano=resultadoSQL($consulta, 0, 'idServicoPlano');
	 			 $idUsuario=resultadoSQL($consulta, 0, 'idUsuario');
	 			 $status=resultadoSQL($consulta, 0, 'status');
	 			 $idPrioridade=resultadoSQL($consulta, 0, 'idPrioridade');
	 			 $data=resultadoSQL($consulta, 0, 'data');
	 			 $nome=resultadoSQL($consulta, 0, 'nome');
	 			 $descricao=resultadoSQL($consulta, 0, 'descricao');
				 
				 verPessoas($modulo, $sub, '', $idPessoaTipo, $matriz);
				 echo "<br>";
						
				# Motrar tabela de busca
				novaTabela2("[Alterar Ocorrência]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
					# Opcoes Adicionais
					menuOpcAdicional($modulo, $sub, $acao, "$idPessoaTipo:$id");
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
							echo "<b class=bold10>Data de Criação: </b>";
						htmlFechaColuna();
						itemLinhaForm(converteData($data,'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Ocorrência criada por: </b>";
						htmlFechaColuna();
						itemLinhaForm(checaUsuario($idUsuario), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Título: </b>";
						htmlFechaColuna();
						$texto="<input type=text name=matriz[nome] size=60 value='$nome'>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Descrição: </b>";
						htmlFechaColuna();
						$texto="<textarea name=matriz[descricao] cols=60 rows=10>$descricao</textarea>";
						itemLinhaForm($texto, 'left', 'middle', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Prioridade: </b>";
						htmlFechaColuna();
						itemLinhaForm(formSelectPrioridade($idPrioridade,'idPrioridade','form'), 'left', 'middle', $corFundo, 0, 'tabfundo1');
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
			if($matriz[nome] && $matriz[descricao]) {
				# continuar
				# Cadastrar em banco de dados
				$grava=dbOcorrencia($matriz, 'alterar');
				
				# Verificar inclusão de registro
				if($grava) {
					# Mensagem de aviso
					$msg="Registro Gravado com Sucesso!";
					avisoNOURL("Aviso", $msg, 400);
					
					# Listar prioridades
					echo "<br>";
					procurarOcorrencias($modulo, $sub, 'listar', $matriz[registro], '');
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
	} #fecha permissoes
} # fecha funcao de alteração




# Funcao para exclusão
function excluirOcorrencias($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin;
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[excluir]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função.";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
	
		# ERRO - Registro não foi informado
		if(!$registro) {
			# ERRO
			$msg="Ocorrência não informada!";
			avisoNOURL("Aviso", $msg, 400);
			
			# Listar 
			echo "<br>";
			procurarOcorrencias($modulo, $sub, 'listar', $matriz[registro], '');
		}
		# Form de inclusao
		elseif($registro && !$matriz[bntExcluir]) {
		
			# Buscar Valores
			$consulta=buscaOcorrencias($registro, 'id', 'igual', 'id');
			if(!$consulta || contaConsulta($consulta)==0) {
				# Mostrar Erro
				$msg="Registro não foi encontrado!";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				aviso("Aviso", $msg, $url, 760);
			}
			else {
				 #atribuir valores
	 			 $id=resultadoSQL($consulta, 0, 'id');
	 			 $idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
	 			 $idServicoPlano=resultadoSQL($consulta, 0, 'idServicoPlano');
	 			 $idUsuario=resultadoSQL($consulta, 0, 'idUsuario');
	 			 $status=resultadoSQL($consulta, 0, 'status');
	 			 $idPrioridade=resultadoSQL($consulta, 0, 'idPrioridade');
	 			 $data=resultadoSQL($consulta, 0, 'data');
	 			 $nome=resultadoSQL($consulta, 0, 'nome');
	 			 $descricao=resultadoSQL($consulta, 0, 'descricao');
				 
				 verPessoas($modulo, $sub, '', $idPessoaTipo, $matriz);
				 echo "<br>";
						
				# Motrar tabela de busca
				novaTabela2("[Excluir Ocorrência]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
					# Opcoes Adicionais
					menuOpcAdicional($modulo, $sub, $acao, "$idPessoaTipo:$id");
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
							echo "<b class=bold10>Data de Criação: </b>";
						htmlFechaColuna();
						itemLinhaForm(converteData($data,'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Ocorrência criada por: </b>";
						htmlFechaColuna();
						itemLinhaForm(checaUsuario($idUsuario), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Título: </b>";
						htmlFechaColuna();
						itemLinhaForm($nome, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Descrição: </b>";
						htmlFechaColuna();
						itemLinhaForm(nl2br($descricao), 'left', 'middle', $corFundo, 0, 'tabfundo3');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Prioridade: </b>";
						htmlFechaColuna();
						$prioridade=formSelectPrioridade($idPrioridade,'','check');
						itemLinhaForm($prioridade[nome], 'left', 'middle', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "&nbsp;";
						htmlFechaColuna();
						$texto="<input type=submit name=matriz[bntExcluir] value=Excluir class=submit2>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				fechaTabela();				
	
			} #fecha alteracao
		} #fecha form - !$bntAlterar
		
		# Alteração - bntAlterar pressionado
		elseif($matriz[bntExcluir]) {
		
			$grava=dbOcorrencia($matriz, 'excluir');
			
			# Verificar inclusão de registro
			if($grava) {
			
				# Excluir comentarios
				$grava2=dbOcorrenciaComentario($matriz, 'excluirocorrencia');
				
				# Mensagem de aviso
				$msg="Registro Excluído com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);
				
				# Listar prioridades
				echo "<br>";
				procurarOcorrencias($modulo, $sub, 'listar', $matriz[registro], '');
			}
		} #fecha 

	} # com permissao
	
} # fecha funcao 



# função para adicionar pessoa
function verOcorrencia($modulo, $sub, $acao, $registro, $matriz) {

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
	
		# Procurar dados
		$consulta=buscaOcorrencias($registro, "id",'igual','nome');
		
		if($consulta && contaConsulta($consulta)>0) {
		
			$id=resultadoSQL($consulta, 0, 'id');
			$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
			$idServicoPlano=resultadoSQL($consulta, 0, 'idServicoPlano');
			$idUsuario=resultadoSQL($consulta, 0, 'idUsuario');
			$idPrioridade=resultadoSQL($consulta, 0, 'idPrioridade');
			$data=resultadoSQL($consulta, 0, 'data');
			$nome=resultadoSQL($consulta, 0, 'nome');
			$descricao=resultadoSQL($consulta, 0, 'descricao');
			$status=resultadoSQL($consulta, 0, 'status');
			
			# Selecionar PessoaTipo para identificar o Tipo de Pessoa (Cliente, Fornecedor, Pop, Banco, etc)
			$checkTipo=checkTipoPessoa($idTipo);
			
			# Tipo de Pessoa
			if($matriz[tipoPessoa]) $pessoaTipo=checkTipoPessoa($matriz[tipoPessoa]);
			else $pessoaTipo=checkIDTipoPessoa($idTipo);
			
			# Motrar tabela de busca
			novaTabela2("[Visualização de Ocorrência]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, "$idPessoaTipo:$id");
				#fim das opcoes adicionais
				itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Data de Criação: </b>";
					htmlFechaColuna();
					itemLinhaForm(converteData($data,'banco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Ocorrência criada por: </b>";
					htmlFechaColuna();
					itemLinhaForm(checaUsuario($idUsuario), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Título: </b>";
					htmlFechaColuna();
					itemLinhaForm($nome, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Prioridade: </b>";
					htmlFechaColuna();
					$prioridade=formSelectPrioridade($idPrioridade,'','check');
					itemLinhaForm($prioridade[nome], 'left', 'middle', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Status: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectStatusOcorrencia($status,'','check'), 'left', 'middle', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Descricao: </b>";
					htmlFechaColuna();
					itemLinhaForm(nl2br($descricao), 'left', 'middle', $corFundo, 0, 'tabfundo3');
				fechaLinhaTabela();

			fechaTabela();
			
		}
	}
}




# Funcao para exclusão
function abrirOcorrencias($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin;
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[abrir]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		
		# ERRO - Registro não foi informado
		if(!$registro) {
			# ERRO
			$msg="Ocorrência não informada!";
			avisoNOURL("Aviso", $msg, 400);
			
			# Listar 
			echo "<br>";
			procurarOcorrencias($modulo, $sub, 'listar', $matriz[registro], '');
		}
		# Form de inclusao
		elseif($registro && !$matriz[bntAbrir] || !$matriz[descricao]) {
		
			# Buscar Valores
			$consulta=buscaOcorrencias($registro, 'id', 'igual', 'id');
			if(!$consulta || contaConsulta($consulta)==0) {
				# Mostrar Erro
				$msg="Registro não foi encontrado!";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				aviso("Aviso", $msg, $url, 760);
			}
			else {
				#atribuir valores
				$id=resultadoSQL($consulta, 0, 'id');
				$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
				$idServicoPlano=resultadoSQL($consulta, 0, 'idServicoPlano');
				$idUsuario=resultadoSQL($consulta, 0, 'idUsuario');
				$status=resultadoSQL($consulta, 0, 'status');
				$idPrioridade=resultadoSQL($consulta, 0, 'idPrioridade');
				$data=resultadoSQL($consulta, 0, 'data');
				$nome=resultadoSQL($consulta, 0, 'nome');
				$descricao=resultadoSQL($consulta, 0, 'descricao');
				 
				verPessoas($modulo, $sub, '', $idPessoaTipo, $matriz);
				echo "<br>";
				
				# Visualizar Ocorrência
				verOcorrencia($modulo, $sub, 'ver', $id, $matriz);
				echo "<br>";
						
				# Motrar tabela de busca
				novaTabela2("[Abrir Ocorrência]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
					# Opcoes Adicionais
					//menuOpcAdicional($modulo, $sub, $acao, $sessCadastro[idPessoaTipo]);
					#fim das opcoes adicionais
					novaLinhaTabela($corFundo, '100%');
					$texto="			
						<form method=post name=matriz action=index.php>
						<input type=hidden name=modulo value=$modulo>
						<input type=hidden name=sub value=$sub>
						<input type=hidden name=acao value=$acao>
						<input type=hidden name=registro value=$registro>
						<input type=hidden name=matriz[idPessoaTipo] value=$idPessoaTipo>
						&nbsp;";
						itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Comentários: </b><br>
							<span class=normal10>Comentários de abertura da ocorrência</span>";
						htmlFechaColuna();
						$texto="<textarea name=matriz[descricao] cols=60 rows=10></textarea>";
						itemLinhaForm($texto, 'left', 'middle', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "&nbsp;";
						htmlFechaColuna();
						$texto="<input type=submit name=matriz[bntAbrir] value=Abrir class=submit>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				fechaTabela();			
	
			} #fecha alteracao
		} #fecha form - !$bntAlterar
		
		# Alteração - bntAlterar pressionado
		elseif($matriz[bntAbrir]) {
		
			$matriz[id]=$registro;
			$grava=statusOcorrencia($matriz, 'A');
			
			$matriz[idOcorrencia]=$registro;
			$matriz[status]='A';
			$matriz[idUsuario]=buscaIDUsuario($sessLogin[login],'login','igual','id');
			
			$grava2=dbOcorrenciaComentario($matriz,'incluir');
			
			# Verificar inclusão de registro
			if($grava) {
				# Mensagem de aviso
				$msg="Ocorrência foi aberta com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);
				
				# Listar prioridades
				echo "<br>";
				procurarOcorrencias($modulo, $sub, 'listar', $matriz[registro], '');
			}
		} #fecha 
	} #fecha 	
} # fecha funcao 



# Funcao para exclusão
function reabrirOcorrencias($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin;
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[abrir]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		
		# ERRO - Registro não foi informado
		if(!$registro) {
			# ERRO
			$msg="Ocorrência não informada!";
			avisoNOURL("Aviso", $msg, 400);
			
			# Listar 
			echo "<br>";
			procurarOcorrencias($modulo, $sub, 'listar', $matriz[registro], '');
		}
		# Form de inclusao
		elseif($registro && !$matriz[bntAbrir] || !$matriz[descricao]) {
		
			# Buscar Valores
			$consulta=buscaOcorrencias($registro, 'id', 'igual', 'id');
			if(!$consulta || contaConsulta($consulta)==0) {
				# Mostrar Erro
				$msg="Registro não foi encontrado!";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				aviso("Aviso", $msg, $url, 760);
			}
			else {
				#atribuir valores
				$id=resultadoSQL($consulta, 0, 'id');
				$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
				$idServicoPlano=resultadoSQL($consulta, 0, 'idServicoPlano');
				$idUsuario=resultadoSQL($consulta, 0, 'idUsuario');
				$status=resultadoSQL($consulta, 0, 'status');
				$idPrioridade=resultadoSQL($consulta, 0, 'idPrioridade');
				$data=resultadoSQL($consulta, 0, 'data');
				$nome=resultadoSQL($consulta, 0, 'nome');
				$descricao=resultadoSQL($consulta, 0, 'descricao');
				 
				verPessoas($modulo, $sub, '', $idPessoaTipo, $matriz);
				echo "<br>";
				
				# Visualizar Ocorrência
				verOcorrencia($modulo, $sub, 'ver', $id, $matriz);
				echo "<br>";
						
				# Motrar tabela de busca
				novaTabela2("[Re-Abrir Ocorrência]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
					# Opcoes Adicionais
					//menuOpcAdicional($modulo, $sub, $acao, $sessCadastro[idPessoaTipo]);
					#fim das opcoes adicionais
					novaLinhaTabela($corFundo, '100%');
					$texto="			
						<form method=post name=matriz action=index.php>
						<input type=hidden name=modulo value=$modulo>
						<input type=hidden name=sub value=$sub>
						<input type=hidden name=acao value=$acao>
						<input type=hidden name=registro value=$registro>
						<input type=hidden name=matriz[idPessoaTipo] value=$idPessoaTipo>
						&nbsp;";
						itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Comentários: </b><br>
							<span class=normal10>Comentários de abertura da ocorrência</span>";
						htmlFechaColuna();
						$texto="<textarea name=matriz[descricao] cols=60 rows=10></textarea>";
						itemLinhaForm($texto, 'left', 'middle', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "&nbsp;";
						htmlFechaColuna();
						$texto="<input type=submit name=matriz[bntAbrir] value=Abrir class=submit>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				fechaTabela();			
	
			} #fecha alteracao
		} #fecha form - !$bntAlterar
		
		# Alteração - bntAlterar pressionado
		elseif($matriz[bntAbrir]) {
		
			$matriz[id]=$registro;
			$grava=statusOcorrencia($matriz, 'R');
			
			$matriz[idOcorrencia]=$registro;
			$matriz[status]='R';
			$matriz[idUsuario]=buscaIDUsuario($sessLogin[login],'login','igual','id');
			
			$grava2=dbOcorrenciaComentario($matriz,'incluir');
			
			# Verificar inclusão de registro
			if($grava) {
				# Mensagem de aviso
				$msg="Ocorrência foi re-aberta com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);
				
				# Listar prioridades
				echo "<br>";
				procurarOcorrencias($modulo, $sub, 'listar', $matriz[registro], '');
			}
		} #fecha 
	} //fecha permissao
} # fecha funcao 



# Funcao para exclusão
function fecharOcorrencias($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin;
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[abrir]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
	
		# ERRO - Registro não foi informado
		if(!$registro) {
			# ERRO
			$msg="Ocorrência não informada!";
			avisoNOURL("Aviso", $msg, 400);
			
			# Listar 
			echo "<br>";
			procurarOcorrencias($modulo, $sub, 'listar', $matriz[registro], '');
		}
		# Form de inclusao
		elseif($registro && !$matriz[bntFechar] || !$matriz[descricao]) {
		
			# Buscar Valores
			$consulta=buscaOcorrencias($registro, 'id', 'igual', 'id');
			if(!$consulta || contaConsulta($consulta)==0) {
				# Mostrar Erro
				$msg="Registro não foi encontrado!";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				aviso("Aviso", $msg, $url, 760);
			}
			else {
				#atribuir valores
				$id=resultadoSQL($consulta, 0, 'id');
				$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
				$idServicoPlano=resultadoSQL($consulta, 0, 'idServicoPlano');
				$idUsuario=resultadoSQL($consulta, 0, 'idUsuario');
				$status=resultadoSQL($consulta, 0, 'status');
				$idPrioridade=resultadoSQL($consulta, 0, 'idPrioridade');
				$data=resultadoSQL($consulta, 0, 'data');
				$nome=resultadoSQL($consulta, 0, 'nome');
				$descricao=resultadoSQL($consulta, 0, 'descricao');
				 
				verPessoas($modulo, $sub, '', $idPessoaTipo, $matriz);
				echo "<br>";
				
				# Visualizar Ocorrência
				verOcorrencia($modulo, $sub, 'ver', $id, $matriz);
				echo "<br>";
						
				# Motrar tabela de busca
				novaTabela2("[Fechar Ocorrência]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
					# Opcoes Adicionais
					//menuOpcAdicional($modulo, $sub, $acao, $sessCadastro[idPessoaTipo]);
					#fim das opcoes adicionais
					novaLinhaTabela($corFundo, '100%');
					$texto="			
						<form method=post name=matriz action=index.php>
						<input type=hidden name=modulo value=$modulo>
						<input type=hidden name=sub value=$sub>
						<input type=hidden name=acao value=$acao>
						<input type=hidden name=registro value=$registro>
						<input type=hidden name=matriz[idPessoaTipo] value=$idPessoaTipo>
						&nbsp;";
						itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Comentários: </b><br>
							<span class=normal10>Comentários de fechamento da ocorrência</span>";
						htmlFechaColuna();
						$texto="<textarea name=matriz[descricao] cols=60 rows=10></textarea>";
						itemLinhaForm($texto, 'left', 'middle', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "&nbsp;";
						htmlFechaColuna();
						$texto="<input type=submit name=matriz[bntFechar] value=Fechar class=submit>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				fechaTabela();			
	
			} #fecha alteracao
		} #fecha form - !$bntAlterar
		
		# Alteração - bntAlterar pressionado
		elseif($matriz[bntFechar]) {
		
			$matriz[id]=$registro;
			$grava=statusOcorrencia($matriz, 'F');
			
			$matriz[idOcorrencia]=$registro;
			$matriz[status]='F';
			$matriz[idUsuario]=buscaIDUsuario($sessLogin[login],'login','igual','id');
			
			$grava2=dbOcorrenciaComentario($matriz,'incluir');
			
			# Verificar inclusão de registro
			if($grava) {
				# Mensagem de aviso
				$msg="Ocorrência foi fechada com Sucesso!";
				avisoNOURL("Aviso", $msg, 400);
				
				# Listar prioridades
				echo "<br>";
				procurarOcorrencias($modulo, $sub, 'listar', $matriz[registro], '');
			}
		} #fecha 
	} #fecha permissao	
} # fecha funcao 



# Funcao para exclusão
function cancelarOcorrencias($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin;
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[abrir]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
	
		# ERRO - Registro não foi informado
		if(!$registro) {
			# ERRO
			$msg="Ocorrência não informada!";
			avisoNOURL("Aviso", $msg, 400);
			
			# Listar 
			echo "<br>";
			procurarOcorrencias($modulo, $sub, 'listar', $matriz[registro], '');
		}
		# Form de inclusao
		elseif($registro && !$matriz[bntCancelar] || !$matriz[descricao]) {
		
			# Buscar Valores
			$consulta=buscaOcorrencias($registro, 'id', 'igual', 'id');
			if(!$consulta || contaConsulta($consulta)==0) {
				# Mostrar Erro
				$msg="Registro não foi encontrado!";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				aviso("Aviso", $msg, $url, 760);
			}
			else {
				#atribuir valores
				$id=resultadoSQL($consulta, 0, 'id');
				$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
				$idServicoPlano=resultadoSQL($consulta, 0, 'idServicoPlano');
				$idUsuario=resultadoSQL($consulta, 0, 'idUsuario');
				$status=resultadoSQL($consulta, 0, 'status');
				$idPrioridade=resultadoSQL($consulta, 0, 'idPrioridade');
				$data=resultadoSQL($consulta, 0, 'data');
				$nome=resultadoSQL($consulta, 0, 'nome');
				$descricao=resultadoSQL($consulta, 0, 'descricao');
				 
				verPessoas($modulo, $sub, '', $idPessoaTipo, $matriz);
				echo "<br>";
				
				# Visualizar Ocorrência
				verOcorrencia($modulo, $sub, 'ver', $id, $matriz);
				echo "<br>";
						
				# Motrar tabela de busca
				novaTabela2("[Cancelar Ocorrência]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
					# Opcoes Adicionais
					//menuOpcAdicional($modulo, $sub, $acao, $sessCadastro[idPessoaTipo]);
					#fim das opcoes adicionais
					novaLinhaTabela($corFundo, '100%');
					$texto="			
						<form method=post name=matriz action=index.php>
						<input type=hidden name=modulo value=$modulo>
						<input type=hidden name=sub value=$sub>
						<input type=hidden name=acao value=$acao>
						<input type=hidden name=registro value=$registro>
						<input type=hidden name=matriz[idPessoaTipo] value=$idPessoaTipo>
						&nbsp;";
						itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold10>Comentários: </b><br>
							<span class=normal10>Comentários de cancelamento da ocorrência</span>";
						htmlFechaColuna();
						$texto="<textarea name=matriz[descricao] cols=60 rows=10></textarea>";
						itemLinhaForm($texto, 'left', 'middle', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "&nbsp;";
						htmlFechaColuna();
						$texto="<input type=submit name=matriz[bntCancelar] value=Cancelar class=submit2>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				fechaTabela();			
	
			} #fecha alteracao
		} #fecha form - !$bntAlterar
		
		# Alteração - bntAlterar pressionado
		elseif($matriz[bntCancelar]) {
		
			$matriz[id]=$registro;
			$grava=statusOcorrencia($matriz, 'C');
			
			$matriz[idOcorrencia]=$registro;
			$matriz[status]='C';
			$matriz[idUsuario]=buscaIDUsuario($sessLogin[login],'login','igual','id');
			
			$grava2=dbOcorrenciaComentario($matriz,'incluir');
			
			# Verificar inclusão de registro
			if($grava) {
				# Mensagem de aviso
				$msg="Ocorrência foi cancelada!";
				avisoNOURL("Aviso", $msg, 400);
				
				# Listar prioridades
				echo "<br>";
				procurarOcorrencias($modulo, $sub, 'listar', $matriz[registro], '');
			}
		} #fecha 
	} #fecha permissao 	
} # fecha funcao 



# Função para atualização de status da ocorrência
function statusOcorrencia($matriz, $status) {

global $conn, $tb, $modulo, $sub, $acao;
	
	$data=dataSistema();
	
	# Verificar se prioridade existe
	$sql="
		UPDATE 
			$tb[Ocorrencias] 
		SET 
			status='$status'
	WHERE id=$matriz[id]";
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}	

}

# Funcao para exclusão
function historicoOcorrencias($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# ERRO - Registro não foi informado
	if(!$registro) {
		# ERRO
	}
	# Form de inclusao
	elseif($registro && !$matriz[bntExcluir]) {
	
		# Buscar Valores
		$consulta=buscaOcorrencias($registro, 'id', 'igual', 'id');
		if(!$consulta || contaConsulta($consulta)==0) {
			# Mostrar Erro
			$msg="Registro não foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso", $msg, $url, 760);
		}
		else {
			#atribuir valores
			$id=resultadoSQL($consulta, 0, 'id');
			$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
			$idServicoPlano=resultadoSQL($consulta, 0, 'idServicoPlano');
			$idUsuario=resultadoSQL($consulta, 0, 'idUsuario');
			$status=resultadoSQL($consulta, 0, 'status');
			$idPrioridade=resultadoSQL($consulta, 0, 'idPrioridade');
			$data=resultadoSQL($consulta, 0, 'data');
			$nome=resultadoSQL($consulta, 0, 'nome');
			$descricao=resultadoSQL($consulta, 0, 'descricao');
			
			verPessoas($modulo, $sub, '', $idPessoaTipo, $matriz);
			echo "<br>";
			
			# Visualizar Dados da Ocorrência
			verOcorrencia($modulo, $sub, 'adm', $registro, $matriz);
			echo "<br>";
			
			historicoOcorrenciasComentarios($modulo, $sub, $acao, $registro, $matriz);
					
		} #fecha alteracao
	} #fecha form - !$bntAlterar
	
	
} # fecha funcao 



# Função para obter dados da ocorrência
function dadosOcorrencia($idOcorrencia) {

	if(is_numeric($idOcorrencia)) {
		$consulta=buscaOcorrencias($idOcorrencia,'id','igual','id');
		
		if($consulta && contaConsulta($consulta)>0) {
			$retorno[id]=resultadoSQL($consulta, 0, 'id');
			$retorno[idPessoaTipo]=resultadoSQL($consulta, 0, 'idPessoaTipo');
			$retorno[idUsuario]=resultadoSQL($consulta, 0, 'idUsuario');
			$retorno[idPrioridade]=resultadoSQL($consulta, 0, 'idPrioridade');
			$retorno[data]=resultadoSQL($consulta, 0, 'data');
			$retorno[nome]=resultadoSQL($consulta, 0, 'nome');
			$retorno[descricao]=resultadoSQL($consulta, 0, 'descricao');
			$retorno[status]=resultadoSQL($consulta, 0, 'status');
		}
		
		return($retorno);
	}
}



# Função para checar ocorrencias em aberto
function ocorrenciasPessoasStatus($idPessoaTipo, $status) {

	if($status=='A') {
		$consulta=buscaOcorrencias("idPessoaTipo = $idPessoaTipo AND (status='N' OR status='A')",'','custom','data DESC');
	}
	else {
		$consulta=buscaOcorrencias("idPessoaTipo = $idPessoaTipo AND status='$status'",'','custom','data DESC');
	}
	
	if($consulta && contaConsulta($consulta)>0) {
		return($consulta);
	}
	else return(0);
}

?>
