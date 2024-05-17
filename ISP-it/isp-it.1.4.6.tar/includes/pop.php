<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 18/08/2003
# Ultima alteração: 16/02/2004
#    Alteração No.: 003
#
# Função:
#    Painel - Funções para cadastro de cidades

# Função para cadastro
function pop($modulo, $sub, $acao, $registro, $matriz)
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
		novaTabela2("[Cadastro de POPs]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 4);
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
					echo "<br><img src=".$html[imagem][pop]." border=0 align=left><b class=bold>POPs</b>
					<br><span class=normal10>Cadastro de <b>Pops</b>.</span>";
				htmlFechaColuna();			
				$texto=htmlMontaOpcao("<br>Adicionar", 'incluir');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=novo", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Procurar", 'procurar');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=procurar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Listar", 'listar');
				itemLinha($texto, "?modulo=$modulo&sub=$sub&acao=listar", 'center', $corFundo, 0, 'normal');
			fechaLinhaTabela();
		fechaTabela();
	
		# Inclusão
		if($acao=="adicionar") {
			echo "<br>";
			adicionarPOP($modulo, $sub, $acao, $registro, $matriz);
		}
		
		# Alteração
		elseif($acao=="alterar") {
			echo "<br>";
			alterarPOP($modulo, $sub, $acao, $registro, $matriz);
		}
		
		# Exclusão
		elseif($acao=="excluir") {
			echo "<br>";
			excluirPOP($modulo, $sub, $acao, $registro, $matriz);
		}
	
		# Busca
		elseif($acao=="procurar") {
			echo "<br>";
			procurarPOP($modulo, $sub, $acao, $registro, $matriz);
		} #fecha tabela de busca

		# Cidades
		elseif($acao=="cidades" ) {
			echo "<br>";
			cidadesPOP($modulo, $sub, $acao, $registro, $matriz);
		} #fecha listagem de servicos
		
		# Adicionar Cidades do POP
		elseif($acao=="cidadesadicionar" ) {
			echo "<br>";
			adicionarPOPCidade($modulo, $sub, $acao, $registro, $matriz);
		} #fecha listagem de servicos
		
		# Excluir Cidades do POP
		elseif($acao=="cidadesexcluir" ) {
			echo "<br>";
			excluirPOPCidade($modulo, $sub, $acao, $registro, $matriz);
		} #fecha listagem de servicos
//
//		# Excluir Cidades do POP
//		elseif($acao=='enderecos') {
//			echo "<br>";
//			enderecosPessoas($modulo, $sub, $acao, $registro, $matriz);
//		}
//		# Excluir Cidades do POP
//		elseif($acao=='documentos') {
//			echo "<br>";
//			enderecosPessoas($modulo, $sub, $acao, $registro, $matriz);
//		}
		elseif($acao=='enderecos') {
			echo "<br>";
			enderecosPessoas($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($acao=='enderecosvisualizar') {
			echo "<br>";
			verEnderecosPessoas($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($acao=='enderecosadicionar') {
			echo "<br>";
			adicionarEnderecosPessoas($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($acao=='enderecosalterar') {
			echo "<br>";
			alterarEnderecosPessoas($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($acao=='enderecosexcluir') {
			echo "<br>";
			excluirEnderecosPessoas($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($acao=='documentos') {
			echo "<br>";
			documentosPessoas($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($acao=='documentosadicionar') {
			echo "<br>";
			adicionarDocumentosPessoas($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($acao=='documentosexcluir') {
			echo "<br>";
			excluirDocumentosPessoas($modulo, $sub, $acao, $registro, $matriz);
		}
		# adicionar dados de pessoa para este pop
		elseif($acao=='novoPopPessoa') {
			echo "<br>";
			PopPessoaTipoCadastro($modulo, $sub, $acao, $registro, $matriz);	
		}
		
		# Listar
		elseif($acao=="listar" || !$acao) {
			echo "<br>";
			listarPOP($modulo, $sub, $acao, $registro, $matriz);
		} #fecha listagem de servicos
	}


} #fecha menu principal 


# função de busca 
/**
 * @return ResultSet
 * @param String $texto
 * @param String $campo
 * @param String $tipo
 * @param String $ordem
 * @desc Busca no banco de POP
$texto: texto para comparação
$campo: campo da comparacao
$tipo: tipo da consulta (todos,contem, igual,custom). No caso de custo o where se baseia no $texto
$ordem: order by
*/
function buscaPOP($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[POP] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[POP] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[POP] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[POP] WHERE $texto ORDER BY $ordem";
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
function adicionarPOP($modulo, $sub, $acao, $registro, $matriz)
{

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# Form de inclusao
	if(!$matriz[bntConfirmar] || !$matriz[nome]) {
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
					echo "<b class=bold>Nome: </b><br>
					<span class=normal10>Nome do POP</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[nome] size=60>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			/**tipos tributos.*/
			getCampo( 'combo', '<b class=bold>Tributação:</b><br><span class=normal10>Tipo de tributação</span>', '', getComboTiposTributos( "matriz[tipoTributo]", $matriz['tipoTributo'] ) );
			
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold>Status: </b><br>
					<span class=normal10>Status do POP</span>";
				htmlFechaColuna();
				itemLinhaForm(formSelectStatusPOP($matriz[status],'status','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "&nbsp;";
				htmlFechaColuna();
				$texto="<input type=submit name=matriz[bntConfirmar] value=Adicionar class=submit>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		fechaTabela();
	} #fecha form
	elseif($matriz[bntConfirmar]) {
		# Conferir campos
		if($matriz[nome]) {
			# Buscar por prioridade
			if(contaConsulta(buscaPOP($matriz[nome], 'nome', 'igual','nome'))>0){
				# Erro - campo inválido
				# Mensagem de aviso
				$msg="POP já cadastrado!";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				aviso("Aviso: Dados incorretos", $msg, $url, 760);
			}
			# continuar - campos OK
			else {
				# Cadastrar em banco de dados
				$grava=dbPOP($matriz, 'incluir');
				
				# Verificar inclusão de registro
				if($grava) {
					# acusar falta de parametros
					# Mensagem de aviso
					listarPOP($modulo, $sub, 'listar', 0, $matriz);
				}
				
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
function dbPOP($matriz, $tipo)
{
	global $conn, $tb, $modulo, $sub, $acao;
	
	# Sql de inclusão
	if($tipo=='incluir') {
		$sql="INSERT INTO $tb[POP] VALUES (0,
		'$matriz[nome]',
		'$matriz[tipoTributo]',
		'$matriz[status]')";
	} #fecha inclusao
	
	elseif($tipo=='excluir') {
		# Verificar se a prioridade existe
		$tmpBusca=buscaPOP($matriz[id], 'id', 'igual', 'id');
		
		# Registro já existe
		if(!$tmpBusca|| contaConsulta($tmpBusca)==0) {
			# Mensagem de aviso
			$msg="Registro não existe no banco de dados";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Erro ao alterar registro", $msg, $url, 760);
		}
		else {
			$sql="DELETE FROM $tb[POP] WHERE id=$matriz[id]";
		}
	}
	
	# Alterar
	elseif($tipo=='alterar') {
		# Verificar se prioridade existe
		$sql="UPDATE $tb[POP] SET nome='$matriz[nome]', tipoTributo='$matriz[tipoTributo]', status='$matriz[status]' WHERE id=$matriz[id]";
	}
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
	
} # fecha função de gravação em banco de dados



# Listar 
function listarPOP($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $moduloApp, $corFundo, $corBorda, $html, $limite, $conn, $tb;

	# Cabeçalho		
	# Motrar tabela de busca
	novaTabela("[Listar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 3);
		# Seleção de registros
		$sql = "SELECT 
					$tb[POP].*, 
					$tb[PopPessoaTipo].idPessoaTipo,
					$tb[PessoasTipos].idPessoa
				FROM
					$tb[POP] LEFT JOIN $tb[PopPessoaTipo] 
						ON ($tb[POP].id = $tb[PopPessoaTipo].idPop)
					LEFT JOIN $tb[PessoasTipos]
						ON ($tb[PopPessoaTipo].idPessoaTipo = $tb[PessoasTipos].id)
				ORDER BY $tb[POP].nome
		";
		
		$consulta = consultaSQL($sql, $conn);
		//$consulta=buscaPOP($texto, $campo, 'todos','nome');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Não há registros
			itemTabelaNOURL('Não há registros cadastrados', 'left', $corFundo, 3, 'txtaviso');
		}
		else {
		
			# Paginador
			paginador($consulta, contaConsulta($consulta), $limite[lista][pop], $registro, 'normal10', 3, $urlADD);
		
			# Cabeçalho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Nome', 'center', '50%', 'tabfundo0');
				itemLinhaTabela('Status', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Opções', 'center', '40%', 'tabfundo0');
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

			$limite=$i+$limite[lista][cidades];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$nome=resultadoSQL($consulta, $i, 'nome');
				$status=resultadoSQL($consulta, $i, 'status');
				$idPessoaTipo=resultadoSQL($consulta, $i, 'idPessoaTipo');
				$idPessoa=resultadoSQL($consulta, $i, 'idPessoa');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=cidades&registro=$id>Cidades</a>",'cidades');
				$opcoes.="<br>";
//					$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=enderecos&registro=$idPessoaTipo>Cadastro</a>",'pessoa');
//					$opcoes.="&nbsp;";				
				if( $idPessoaTipo ) {
					$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=enderecos&registro=$idPessoaTipo>Enderecos</a>",'endereco');
					$opcoes.="&nbsp;";
				}
				if( $idPessoa ) {
					$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=documentos&registro=$idPessoaTipo:$idPessoa&matriz[idPop]=$id>Documentos</a>",'documento');
				}
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($nome, 'left', '50%', 'normal10');
					itemLinhaTabela(formSelectStatusPOP($status,'','check'), 'center', '10%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '40%', 'normal10');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem

	fechaTabela();
	
} # fecha função de listagem


# Função para procura de serviço
function procurarPOP($modulo, $sub, $acao, $registro, $matriz)
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
			<input type=submit name=matriz[bntProcurar] value=Procurar>";
			itemLinhaForm($texto, 'left','middle', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
	fechaTabela();
	echo "</form>";


	# Caso botão procurar seja pressionado
	if($matriz[txtProcurar] && $matriz[bntProcurar]) {
		#buscar registros
		$consulta=buscaPOP("upper(nome) like '%$matriz[txtProcurar]%'",$campo, 'custom','nome');

		itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
		novaTabela("[Resultados]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	
		if(!$consulta || contaConsulta($consulta)==0 ) {
			# Não há registros
			itemTabelaNOURL('Não foram encontrados registros cadastrados', 'left', $corFundo, 2, 'txtaviso');
		}
		elseif($consulta && contaConsulta($consulta)>0 && (!$registro || is_numeric($registro)) ) {	
		
			itemTabelaNOURL('Registros encontrados procurando por ('.$matriz[txtProcurar].'): '.contaConsulta($consulta).' registro(s)', 'left', $corFundo, 2, 'txtaviso');

			# Paginador
			$urlADD="&textoProcurar=".$matriz[txtProcurar];
			paginador($consulta, contaConsulta($consulta), $limite[lista][pop], $registro, 'normal', 2, $urlADD);

			# Cabeçalho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Cidades', 'center', '60%', 'tabfundo0');
				itemLinhaTabela('Opções', 'center', '40%', 'tabfundo0');
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

			$limite=$i+$limite[lista][cidades];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$nome=resultadoSQL($consulta, $i, 'nome');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=cidades&registro=$id>Cidades</a>",'cidades');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($nome, 'left', '60%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '40%', 'normal10');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem
	} # fecha botão procurar
} #fecha funcao de  procurar 



# Funcao para alteração
function alterarPOP($modulo, $sub, $acao, $registro, $matriz) {
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# ERRO - Registro não foi informado
	if(!$registro) {
		# ERRO
	}
	# Form de inclusao
	elseif($registro && !$matriz[bntConfirmar]) {
	
		# Buscar Valores
		$consulta=buscaPOP($registro, 'id', 'igual', 'id');
		if(!$consulta || contaConsulta($consulta)==0) {
			# Mostrar Erro
			$msg="Registro não foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso", $msg, $url, 760);
		}
		else {
			#atribuir valores
			$matriz[nome]	= resultadoSQL($consulta, 0, 'nome');
			$matriz[status] = resultadoSQL($consulta, 0, 'status');
			 
			# Motrar tabela de busca
			novaTabela2("[Alterar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, $registro);				
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo     value=$modulo>
					<input type=hidden name=sub        value=$sub>
					<input type=hidden name=registro   value=$registro>
					<input type=hidden name=matriz[id] value=$registro>
					<input type=hidden name=acao	   value=$acao>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Nome: </b><br>
						<span class=normal10>Nome do POP</span>";
					htmlFechaColuna();
					$texto='<input type="text" name="matriz[nome]" size="60" value="'.$matriz[nome].'">';
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				
				/**tipos tributos.*/
				getCampo( 'combo', '<b class=bold>Tributação:</b><br><span class=normal10>Tipo de tributação</span>', '', getComboTiposTributos( "matriz[tipoTributo]", $matriz['tipoTributo'] ) );
				
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Status: </b><br>
						<span class=normal10>Status do POP</span>";
					htmlFechaColuna();
					itemLinhaForm(formSelectStatusPOP($matriz[status],'status','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				# verifica se este pop já é visto como pessoa
				$conta = dbPopPessoaTipo('', "consultar",'', "idPop='".$registro."'");
				if(count( $conta ) == 0) {
					novaLinhaTabela($corFundo, '100%');
						htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
							echo "<b class=bold>Cadastar como Pessoa: </b><br>
							<span class=normal10>Permite o cadastro de Documentos e Endereços</span>";
						htmlFechaColuna();
						$texto='<input type="checkbox" name="matriz[tipoPessoa]" size="60" value="1"'.$checked.">\n";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();
					$texto='<input type="submit" name="matriz[bntConfirmar]" value="Confirmar" class="submit">';
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();				

		} #fecha alteracao
	} #fecha form - !$bntAlterar
	
	# Alteração - bntAlterar pressionado
	elseif($matriz[bntConfirmar]) {
		# Conferir campos
		if($matriz[nome]) {
			# continuar
			# Cadastrar em banco de dados
			$grava=dbPOP($matriz, 'alterar');
			
			# Verificar inclusão de registro
			if( $grava ) {
				# se marcou para cadastrar como pessoa, então é exibido o formulário com os dados da pessoa
				if($matriz[tipoPessoa]){
					# reinicia a matriz
					$matriz = array();
					#guarda o idPop
					$matriz[idPop] = $registro;
					PopPessoaTipoCadastro($modulo, $sub, 'novoPopPessoa', 0, $matriz);
				} # senão listar Pops
				else {
					listarPOP($modulo, $sub, 'listar', 0,'');
				}
				
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
function excluirPOP($modulo, $sub, $acao, $registro, $matriz)
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
	elseif($registro && !$matriz[bntConfirmar]) {
	
		# Buscar Valores
		$consulta=buscaPOP($registro, 'id', 'igual', 'id');
		
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
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro>
					<input type=hidden name=matriz[id] value=$registro>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>POP: </b>";
					htmlFechaColuna();
					itemLinhaForm($nome, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Status: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectStatusPOP($status,'','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
	
				# Botão de confirmação
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();
					$texto="<input type=submit name=matriz[bntConfirmar] value=Excluir>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		} #fecha alteracao
	} #fecha form - !$bntExcluir
	
	# Alteração - bntExcluir pressionado
	elseif($matriz[bntConfirmar]) {
		# Cadastrar em banco de dados
		$grava=dbPOP($matriz, 'excluir');
				
		# Verificar inclusão de registro
		if($grava) {
			# OK - listar
			listarPOP($modulo, $sub, 'listar', 0, '');
		}
		
	} #fecha bntExcluir
	
} #fecha exclusao 



# Formulário de seleção de Tipo de Endereço
function formSelectPOP($id, $campo, $tipo, $adic='', $evento='') {

	global $tb, $conn;
	
	if($tipo=='form') {

		$sql="SELECT * FROM $tb[POP] WHERE status='A' ORDER BY nome";
		$consulta=consultaSQL($sql, $conn);
		
		if($consulta && contaConsulta($consulta)>0) {

			$retorno="<select name=\"matriz[$campo]\" style=\"width:300px\" $evento>";
			$retorno .= $adic;
			for($a=0;$a<contaConsulta($consulta);$a++) {
				$tmpID=resultadoSQL($consulta, $a, 'id');
				$tmpNome=resultadoSQL($consulta, $a, 'nome');
			
				if($id && $tmpID==$id) $opcSelect=' selected="selected"';
				else $opcSelect='';
				
				$retorno.="<option value=\"$tmpID\" $opcSelect>$tmpNome</option>\n";
			}
	
			$retorno.="</select>";
		}
	}
	elseif($tipo=='multi') {

		$sql="SELECT * FROM $tb[POP] WHERE status='A' ORDER BY nome";
		$consulta=consultaSQL($sql, $conn);
		
		if($consulta && contaConsulta($consulta)>0) {

			$retorno="<select multiple size=6 name=matriz[$campo][]>";
			
			for($a=0;$a<contaConsulta($consulta);$a++) {
				$tmpID=resultadoSQL($consulta, $a, 'id');
				$tmpNome=resultadoSQL($consulta, $a, 'nome');
			
				if($id) {
				
					if(array_search($tmpID, $id)) $opcSelect='selected';
					else $opcSelect='';
				}
				
				$retorno.="<option value=$tmpID>$tmpNome\n";
			}
	
			$retorno.="</select>";
		}
	}
	elseif($tipo=='check') {
	
		$consulta=buscaPOP($id,'id','igual','id');
		
		if($consulta && contaConsulta($consulta)>0) {
			$retorno=resultadoSQL($consulta, 0, 'nome');
		}
		else $retorno='POP inválido!';
	}

	return($retorno);
	
}


# Funcao para visualização
function verPOP($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# ERRO - Registro não foi informado
	if(!$registro) {
		# ERRO
	}
	# Form de inclusao
	elseif($registro) {
	
		# Buscar Valores
		$consulta=buscaPOP($registro, 'id', 'igual', 'id');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Mostrar Erro
			$msg="Registro não foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso", $msg, $url, 760);
		}
		else {
			#atribuir valores
 			$nome=resultadoSQL($consulta, 0, 'nome');
			
			# Motrar tabela de busca
			novaTabela2("[Informações do POP]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
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
			fechaTabela();				

		} #fecha alteracao
	} #fecha 
} # fecha funcao de Exclusão

function buscarPopPessoaTipo( $idPessoaTipo ){
	global $conn, $tb;
	
	if ( $idPessoaTipo ){
		$sql = "SELECT " .
			   "		$tb[POP].id, $tb[POP].nome " .
			   "FROM $tb[PessoasTipos] " .
			   "INNER JOIN" .
			   "		$tb[Pessoas] On ( $tb[PessoasTipos].idPessoa = $tb[Pessoas].id) " .
			   "INNER JOIN" .
			   "		$tb[POP] On ( $tb[Pessoas].idPOP = $tb[POP].id )" .
			   "WHERE" .
			   "		$tb[PessoasTipos].id = '". $idPessoaTipo ."'";
		
		$cons = consultaSQL($sql, $conn);
		
		if ( contaConsulta( $cons ) ){
			$ret['id'] = resultadoSQL($cons, 0, 'id');
			$ret['nome'] = resultadoSQL($cons, 0, 'nome');	  
		}
	}
	
	return $ret;
}

/**
 * Retorna a Id do novo Pop Cadastrado
 * 
 * @return integer
 */
function PopGetNewId(){
	return mysql_insert_id();
}

/**
 * consulta o tipo de tributo da pessoa.
 *
 * @param unknown_type $idPessoaTipo
 */
function getTipoTributosPessoaTipo( $idPessoaTipo ){
	global $tb, $conn ;
	
	$sql = "SELECT tipoTributo ".
				" FROM ".$tb['POP'].
				" INNER JOIN " . $tb['Pessoas'].
				"	ON (". $tb['Pessoas']. ".idPop = ". $tb['POP'].".id )".
				" INNER JOIN " . $tb['PessoasTipos'] .
				"   ON  (" . $tb['PessoasTipos'] . ".idPessoa = ". $tb['Pessoas']. ".id )".
				" WHERE ". $tb['PessoasTipos'].".id = '". $idPessoaTipo."'";
				
	$cons = consultaSQL( $sql, $conn );
	
	if ( $cons && contaConsulta( $cons ) ){
		$ret = resultadoSQL( $cons, 0, 'tipoTributo' );
	}
	else{
		$ret = "";
	}
	
	return ( $ret );
}

?>