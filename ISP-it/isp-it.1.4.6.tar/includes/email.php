<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 08/12/2003
# Ultima alteração: 07/01/2004
#    Alteração No.: 003
#
# Função:
#    Painel - Funções para controle de serviço de radius (grupos)


# função de busca de grupos
/**
 * @return unknown
 * @param unknown $texto
 * @param unknown $campo
 * @param unknown $tipo
 * @param unknown $ordem
 * @desc Busca todos os registros da tab Email
 $texto -> valor campo a ser buscado
 $campo -> campo a ser buscado
 $tipo  -> tipo de busca (todos, contem, igual, custom)
 $ordem -> campo que deve ordernar o resultado     
*/
function buscaEmails($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[Emails] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[Emails] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[Emails] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[Emails] WHERE $texto ORDER BY $ordem";
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



# função de busca de grupos
function buscaIDEmail($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[Emails] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[Emails] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[Emails] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[Emails] WHERE $texto ORDER BY $ordem";
	}
	
	# Verifica consulta
	if($sql){
		$consulta=consultaSQL($sql, $conn);
		# Retornvar consulta
		return(resultadoSQL($consulta,0,'id'));
	}
	else {	
		# Mensagem de aviso
		$msg="Consulta não pode ser realizada por falta de parâmetros";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Aviso: Ocorrência de erro", $msg, $url, 760);
	}
	
} # fecha função de busca de grupos




# função de busca de grupos
function buscaIDNovoEmail()
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	$sql="SELECT MAX(id)+1 id FROM $tb[Emails]";
	
	$consulta=consultaSQL($sql, $conn);
	
	$id=resultadoSQL($consulta, 0, 'id');
	//if($consulta && contaConsulta($consulta)>0) $id=resultadoSQL($consulta, 0, 'id');
	//else $id=1;
	
	if(!is_numeric($id)) $id=1;
	
	return($id);
	
} # fecha função de busca de grupos


# Funcao para cadastro de usuarios
function adicionarEmail($modulo, $sub, $acao, $registro, $matriz)
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
					echo "<b class=bold10>Domínio: </b><br>
					<span class=normal10>Nome do domínio</span>";
				htmlFechaColuna();
				$texto="<input type=text name=matriz[nome] size=60>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Descricao: </b><br>
					<span class=normal10>Identificação detalhada do dominio</span>";
				htmlFechaColuna();
				$texto="<textarea name=matriz[descricao] rows=3 cols=60></textarea>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Status: </b><br>
					<span class=normal10>Status do dominio</span>";
				htmlFechaColuna();
				itemLinhaForm(formSelectStatusDominios('A','status','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
						novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
					echo "<b class=bold10>Domínio Padrão: </b><br>
					<span class=normal10>Consider domínio como padrão</span>";
				htmlFechaColuna();
				itemLinhaForm(formSelectSimNao('N','padrao','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
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
			$grava=dbDominio($matriz, 'incluir');
			
			# Verificar inclusão de registro
			if($grava) {
				# Mensagem de aviso
				$msg="Registro Gravado com Sucesso!";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				avisoNOURL("Aviso", $msg, 400);
				
				echo "<br>";
				listarDominios($modulo, $sub, 'listar', $registro, $matriz);
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
function excluirEmail($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	# Form de inclusao
	if(!$matriz[bntExcluir]) {
	
		# Buscar informações sobre registro
		$consulta=buscaDominios($registro, 'id','igual','id');
		
		if($consulta && contaConsulta($consulta)>0) {
		
			# receber valores
			$id=resultadoSQL($consulta, 0, 'id');
			$nome=resultadoSQL($consulta, 0, 'nome');
			$descricao=resultadoSQL($consulta, 0, 'descricao');
			$dtCadastro=resultadoSQL($consulta, 0, 'dtCadastro');
			$dtAtivacao=resultadoSQL($consulta, 0, 'dtAtivacao');
			$dtCongelamento=resultadoSQL($consulta, 0, 'dtCongelamento');
			$dtBloqueio=resultadoSQL($consulta, 0, 'dtBloqueio');
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
						echo "<b class=bold10>Domínio: </b>";
					htmlFechaColuna();
					itemLinhaForm($nome, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Descricao: </b>";
					htmlFechaColuna();
					itemLinhaForm($descricao, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Status: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectStatusDominios('A','status','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
							novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold10>Domínio Padrão: </b>";
					htmlFechaColuna();
					itemLinhaForm(formSelectSimNao('N','padrao','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();
					$texto="<input type=submit name=matriz[bntExcluir] value=Excluir class=submit>";
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
		$grava=dbDominio($matriz, 'excluir');
		
		# Verificar inclusão de registro
		if($grava) {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Registro Gravado com Sucesso!";
			avisoNOURL("Aviso", $msg, 400);
			
			echo "<br>";
			listarDominios($modulo, $sub, 'listar', 0, $matriz);
		}
		# falta de parametros
		else {
			# acusar falta de parametros
			# Mensagem de aviso
			$msg="Erro ao gravar registro!";
			avisoNOURL("Aviso: Ocorrência de erro", $msg, 760);

			echo "<br>";
			listarDominios($modulo, $sub, 'listar', 0, $matriz);
		}
	}
} # fecha funcao de exclusão de grupos



# Função para gravação em banco de dados
/**
 * @return unknown
 * @param unknown $matriz
 * @param unknown $tipo
 * @desc Funcao de manipulacao da tabela Email
*/
function dbEmail($matriz, $tipo)
{
	global $conn, $tb, $modulo, $sub, $acao;
	
	$data=dataSistema();
	
	# Sql de inclusão
	if($tipo=='incluir') {
		$tmpNome=strtoupper($matriz[login]);
		# Verificar se serviço existe
		$tmpBusca=buscaEmails("upper(login)='$tmpNome' AND idDominio=$matriz[idDominio]", $campo, 'custom', 'id');
		
		# Registro já existe
		if($tmpBusca && contaConsulta($tmpBusca)>0) {
			# Mensagem de aviso
			$msg="Registro já existe no banco de dados";
			avisoNOURL("Aviso: Erro ao incluir registro", $msg, 400);
			
			echo "<br>";
			emailListarContasDominios($modulo, $sub, 'listar', $registro, $matriz);
			
		}
		
		else {
		
			$matriz[senha_texto]=$matriz[senha_conta];
			$matriz[senha_conta]=crypt($matriz[senha_conta]);
		
			$sql="INSERT INTO $tb[Emails] VALUES (
				'$matriz[idEmail]', 
				'$matriz[idPessoaTipo]', 
				'$matriz[idDominio]', 
				'$matriz[login]', 
				'$matriz[senha_conta]', 
				'$matriz[senha_texto]',
				'$matriz[status]',
				'$matriz[idServicosPlanos]'
			)";
			/*
			+------------------+--------------+------+-----+---------+----------------+
			| Field            | Type         | Null | Key | Default | Extra          |
			+------------------+--------------+------+-----+---------+----------------+
			| id               | int(11)      |      | PRI | NULL    | auto_increment |
			| idPessoaTipo     | int(11)      | YES  |     | NULL    |                |
			| idDominio        | int(11)      | YES  |     | NULL    |                |
			| login            | varchar(200) | YES  |     | NULL    |                |
			| senha            | varchar(200) | YES  |     | NULL    |                |
			| senhaTexto       | varchar(200) | YES  |     | NULL    |                |
			| status           | char(1)      | YES  |     | NULL    |                |
			| idServicosPlanos | int(11)      |      |     | 0       |                |
			+------------------+--------------+------+-----+---------+----------------+
			*/
		}
	} #fecha inclusao
	
	# Excluir
	elseif($tipo=='excluir') {
		# Verificar se serviço existe
		$tmpServico=buscaEmails($matriz[id], 'id', 'igual', 'id');
		
		# Registro não existe
		if(!$tmpServico || contaConsulta($tmpServico)==0) {
			# Mensagem de aviso
			$msg="Registro não existe no banco de dados";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso: Erro ao alterar registro", $msg, $url, 400);
		}
		else {
			$sql="DELETE FROM $tb[Emails] WHERE id=$matriz[id]";
		}
	}
	
	# Alterar
	elseif($tipo=='alterar') {
		$servico= $matriz[idServicosPlanos];
		$senha=crypt($matriz[senha_conta]);
		$sql="
			UPDATE 
				$tb[Emails]
			SET
				senha='$senha',
		";
		if ($servico){ // se houver alteracao de plano, mudamos o id de servico!
			$sql.= " idServicosPlanos= $servico,";
		}
		$sql.= "
				senhaTexto='$matriz[senha_conta]'
			WHERE
				id='$matriz[id]'
		";
	}
	
	# Transferir dominio entre pessoas tipos
	elseif($tipo=='transferir') {
		$sql="
			UPDATE 
				$tb[Emails]
			SET
				idPessoaTipo='$matriz[idPessoaTipo]'
			WHERE
				idDominio='$matriz[idDominio]'
		";
	}

	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
	
} # fecha função de gravação em banco de dados


# Listar grupos
function listarEmails($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite;



	# Cabeçalho
	# Motrar tabela de busca
	novaTabela("[Listar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 6);
	
		$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=adicionar>Adicionar</a>",'incluir');
		$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=listar>Listar</a>",'listar');
		itemTabelaNOURL($opcoes, 'right', $corFundo, 6, 'tabfundo1');
	
		# Seleção de registros
		$consulta=buscaDominios($texto, $campo, 'todos','padrao DESC, nome ASC');
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Não há registros
			itemTabelaNOURL('Não há registros cadastrados', 'left', $corFundo, 6, 'txtaviso');
		}
		else {
		
			# Paginador
			paginador($consulta, contaConsulta($consulta), $limite[lista][dominios], $registro, 'normal', 5, $urlADD);
		
			# Cabeçalho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Nome', 'center', '30%', 'tabfundo0');
				itemLinhaTabela('Data de Cadastro', 'center', '20%', 'tabfundo0');
				itemLinhaTabela('Status', 'center', '15%', 'tabfundo0');
				itemLinhaTabela('Padrão', 'center', '10%', 'tabfundo0');
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
			
			$limite=$i+$limite[lista][dominios];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$nome=resultadoSQL($consulta, $i, 'nome');				
				$dtCadastro=resultadoSQL($consulta, $i, 'dtCadastro');
				$status=resultadoSQL($consulta, $i, 'status');
				$padrao=resultadoSQL($consulta, $i, 'padrao');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=ver&registro=$id>Detalhes</a>",'ver');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($nome, 'left', '30%', 'normal10');
					itemLinhaTabela(converteData($dtCadastro,'banco','form'), 'center', '20%', 'normal10');
					itemLinhaTabela(formSelectStatusDominios($status,'','check'), 'center', '15%', 'normal10');
					itemLinhaTabela(formSelectSimNao($padrao,'','check'), 'center', '10%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '25%', 'normal8');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem

	fechaTabela();

} # fecha função de listagem



# Função para procura 
function procurarEmails($modulo, $sub, $acao, $registro, $matriz)
{
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
	if( $matriz[bntProcurar] && $matriz[txtProcurar] ) {
		#buscar registros
		$consulta=buscaDominios("upper(nome) like '%$matriz[txtProcurar]%' OR upper(descricao) like '%$matriz[txtProcurar]%'",$campo, 'custom','padrao DESC, nome ASC');

		echo "<br>";

		novaTabela("[Listar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 10);
	
		if(!$consulta || contaConsulta($consulta)==0 ) {
			# Não há registros
			itemTabelaNOURL('Não foram encontrados registros cadastrados', 'left', $corFundo, 10, 'txtaviso');
		}
		elseif($consulta && contaConsulta($consulta)>0 && (is_integer($registro) || !$registro)) {	
		
			itemTabelaNOURL('Registros encontrados procurando por ('.$matriz[txtProcurar].'): '.contaConsulta($consulta).' registro(s)', 'left', $corFundo, 10, 'txtaviso');

			# Paginador
			$urlADD="&textoProcurar=".$matriz[txtProcurar];
			# Paginador
			paginador($consulta, contaConsulta($consulta), $limite[lista][dominios], $registro, 'normal', 5, $urlADD);
		
			# Cabeçalho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Nome', 'center', '30%', 'tabfundo0');
				itemLinhaTabela('Data de Cadastro', 'center', '20%', 'tabfundo0');
				itemLinhaTabela('Status', 'center', '15%', 'tabfundo0');
				itemLinhaTabela('Padrão', 'center', '10%', 'tabfundo0');
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
			
			$limite=$i+$limite[lista][dominios];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$nome=resultadoSQL($consulta, $i, 'nome');				
				$dtCadastro=resultadoSQL($consulta, $i, 'dtCadastro');
				$status=resultadoSQL($consulta, $i, 'status');
				$padrao=resultadoSQL($consulta, $i, 'padrao');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=ver&registro=$id>Detalhes</a>",'ver');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($nome, 'left', '30%', 'normal10');
					itemLinhaTabela(converteData($dtCadastro,'banco','form'), 'center', '20%', 'normal10');
					itemLinhaTabela(formSelectStatusDominios($status,'','check'), 'center', '15%', 'normal10');
					itemLinhaTabela(formSelectSimNao($padrao,'','check'), 'center', '10%', 'normal10');
					itemLinhaTabela($opcoes, 'center', '25%', 'normal8');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem
	} # fecha botão procurar
} #fecha funcao de  procura


# Função para visualizar as informações do servidor
function verEmails($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $corFundo, $corBorda, $tb, $html;
	
	# Mostar informações sobre Servidor
	$consulta=buscaEmails($registro, 'id','igual','id');
	
	#nova tabela para mostrar informações
	novaTabela2('Informações sobre Email', 'center', '100%', 0, 2, 1, $corFundo, $corBorda, 2);
	
	if($consulta && contaConsulta($consulta)>0) {
		# receber valores
		$id=resultadoSQL($consulta, 0, 'id');
		$login=resultadoSQL($consulta, 0, 'login');
		$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
		$idDominio=resultadoSQL($consulta, 0, 'idDominio');
		
		# Opcoes Adicionais
		//menuOpcAdicional($modulo, $sub, $acao, $registro);
		
		#fim das opcoes adicionais
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b class=bold10>Email: </b>";
			htmlFechaColuna();
			itemLinhaForm($login, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
				echo "<b class=bold10>Domínio: </b>";
			htmlFechaColuna();
			itemLinhaForm(formSelectDominioEmail($idDominio,'','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('100%', 'right', $corFundo, 2, 'tabfundo1');
				listarEmailConfig($modulo, $sub, $acao, $id, $matriz);
			htmlFechaColuna();
		fechaLinhaTabela();
	}
	else {
		itemTabelaNOURL('Registro não encontrado!', 'left', $corFundo, 2, 'txtaviso');		
	}
	
	fechaTabela();	
	# fim da tabela
	
} #fecha visualizacao



# Função para exclusão de dominio de email
function emailRemoverDominio($idDominio) {

	global $conn, $tb;
	
	# Selecionar emails dos dominios
	$consulta=buscaEmails($idDominio, 'idDominio','igual','id');
	
	if($consulta && contaConsulta($consulta)>0) {
		
		for($a=0;$a<contaConsulta($consulta);$a++) {
			$matriz[id]=resultadoSQL($consulta, $a, 'id');
			
			# Excluir configurações dos emails
			dbEmailConfig($matriz, 'excluiremail');
			
			# Excluir alias dos emails
			dbEmailAlias($matriz, 'excluiremail');
			
			# Excluir forward dos emails
			dbEmailForward($matriz, 'excluiremail');
			
			# Excluir Email
			dbEmail($matriz, 'excluir');
			
		}
		
	}
}


# Função para exclusão de contas de email do serviço - dominio padrao
function emailRemoverContasDominio($idServicoPlano) {

	# Excluir Contas de Email do Dominio
	$contasDominio=buscaEmails($idServicoPlano,'idServicoPlano','igual','id');
	
	if($contasDominio && contaConsulta($contasDominio)>0) {
		for($a=0;$a<contaConsulta($contasDominio);$a++) {
			$tmpConta[id]=resultadoSQL($contasDominio, $a, 'id');
			$tmpConta[login]=resultadoSQL($contasDominio, $a, 'login');
			
			$idDominio=resultadoSQL($contasDominio, $a, 'idDominio');
			$dominio=dadosDominio($idDominio);
			$tmpConta[dominio]=$dominio[nome];
			
			$gravaManager=managerComando($matriz, 'emailremover');
			
			# Excluir Configurações
			dbEmailConfig($matriz, 'excluiremail');
			
			# Excluir Alias
			# Buscar alias para remoção
			$consultaAlias=buscaEmailAlias($tmpConta[id],'idEmail','igual','id');
			if($consultaAlias && contaConsulta($consultaAlias)>0) {
				# Excluir alias do email
				for($b=0;$b<contaConsulta($consultaAlias);$b++) {
					$tmpConta[alias]=resultadoSQL($consultaAlias, $b, 'alias');
					$gravaManager=managerComando($tmpConta, 'emailaliasremover');
				}
					
				dbEmailAlias($tmpConta, 'excluiremail');
			}
		}
	}
}



function enviarEmail($de, $para, $assunto, $mensagem) {
	mail($para, $assunto, $mensagem, "From:".$de);

}

?>
