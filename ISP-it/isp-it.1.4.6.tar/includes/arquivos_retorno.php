<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 19/01/2004
# Ultima alteração: 14/04/2004
#    Alteração No.: 007
#
# Função:
#    Funções para arquivos remessa


# função de busca 
function buscaArquivosRetorno($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[ArquivoRetorno] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[ArquivoRetorno] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[ArquivoRetorno] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[ArquivoRetorno] WHERE $texto ORDER BY $ordem";
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



# função para criação de novo arquivo remessa
function arquivoRetornoNovoArquivo() {

	global $conn, $tb;
	
	$sql="SELECT MAX(idArquivo)+1 numero from $tb[ArquivoRetorno]";
	$consulta=consultaSQL($sql, $conn);
	
	# Data do sistema
	$data=dataSistema();
	
	# Buscar numero
	$numero=resultadoSQL($consulta, 0, 'numero5908-6787 ');
	
	if(!$numero || !is_numeric($numero)) {
		$matRetorno[numeroArquivo]=1;
	}
	else {
		$matRetorno[numeroArquivo]=$numero;
	}
	
	# Criar novo arquivo para URL de download
	$matRetorno[nomeArquivo]="REM".$matRetorno[numeroArquivo].'.TRM';
	
	return($matRetorno);

}



# função para gravação de arquivo remessa
function dbArquivoRetorno($matriz,$tipo) {

	global $conn, $tb, $sessLogin, $arquivo;
	
	$data=dataSistema();
	$matriz[conteudo]=addslashes($matriz[conteudo]);
	$matriz[idUsuario]=buscaIDUsuario($sessLogin[login],'login','igual','id');
	
	
	if($tipo=='incluir') {
	
		$sql="
			INSERT INTO 
				$tb[ArquivoRetorno] 
				VALUES (
					0,
					0, 
					$matriz[idBanco], 
					$matriz[idUsuario], 
					'$data[dataBanco]', 
					'$matriz[nomeArquivo]', 
					'$matriz[conteudo]',
					'N'
				)";
	}
	elseif($tipo=='excluir') {
		$sql="DELETE FROM $tb[ArquivoRetorno] WHERE id=$matriz[id]";
	}
	elseif($tipo=='ativar') {
		$sql="UPDATE $tb[ArquivoRetorno] SET status='A' where id=$matriz[id]";
	}
	
	if($sql) {
		$consulta=consultaSQL($sql, $conn);
		return($consulta);
	}
	
}

# Lançamentos
function arquivosRetorno($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessLogin, $sessPlanos;
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	if(!$permissao[admin] && !$permissao[adicionar]) {
		# SEM PERMISSÃO DE EXECUTAR A FUNÇÃO
		$msg="ATENÇÃO: Você não tem permissão para executar esta função";
		$url="?modulo=$modulo&sub=$sub";
		aviso("Acesso Negado", $msg, $url, 760);
	}
	else {
		# Topo da tabela - Informações e menu principal do Cadastro
		novaTabela2("[Retorno de Remessas Bancárias]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 4);
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
					echo "<br><img src=".$html[imagem][arquivos]." border=0 align=left><b class=bold>Geração de Arquivos Retorno</b><br>
					 <span class=normal10>A <b>importação de arquivos retorno</b> permite a transferência de informações
					 financeiras, retornadas pela entidade bancária, diretamente para o sistema, realizando as
					 baixas automáticas de títulos, bem como, verificações de valores faturados e recebidos.</span>";
				htmlFechaColuna();
				$texto=htmlMontaOpcao("<br>Importar arquivo", 'arquivo');
				itemLinha($texto, "?modulo=faturamento&sub=arquivoretorno&acao=importar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Listar Retornos", 'arquivo');
				itemLinha($texto, "?modulo=faturamento&sub=arquivoretorno&acao=listar", 'center', $corFundo, 0, 'normal');
				$texto=htmlMontaOpcao("<br>Listar Todos", 'arquivo');
				itemLinha($texto, "?modulo=faturamento&sub=arquivoretorno&acao=listartodos", 'center', $corFundo, 0, 'normal');
			fechaLinhaTabela();
		fechaTabela();
		
		if($sub=="arquivoretorno") {
		
			echo "<br>";
			
			if($acao=='listar' || $acao=='listartodos' || $acao=='listargerados' || !$acao) {
				# Listar faturamentos ativos
				listarArquivosRetorno($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='importar') {
				# Gerar arquivos
				importarArquivosRetorno($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='excluir') {
				# Excluir arquivos
				excluirArquivosRetorno($modulo, $sub, $acao, $registro, $matriz);
			}
			elseif($acao=='processar') {
				# Excluir arquivos
				processarArquivoRetorno($modulo, $sub, $acao, $registro, $matriz);
			}
		}
		
		echo "<script>location.href='#ancora';</script>";
		
	}
	
}




# Listar 
function listarArquivosRetorno($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $moduloApp, $corFundo, $corBorda, $html, $limite;

	# Cabeçalho		
	# Motrar tabela de busca
	novaTabela("[Listar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 6);
		# Seleção de registros
		if($acao=='listartodos') {
			$consulta=buscaArquivosRetorno($texto, $campo, 'todos','dtArquivo');
		}
		elseif($acao=='listar') {
			$consulta=buscaArquivosRetorno('N', 'status', 'igual','dtArquivo DESC');
		}
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Não há registros
			itemTabelaNOURL('Não há registros cadastrados', 'left', $corFundo, 6, 'txtaviso');
		}
		else {
		
			# Paginador
			paginador($consulta, contaConsulta($consulta), $limite[lista][arquivosretorno], $registro, 'normal10', 7, $urlADD);
		
			# Cabeçalho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Banco', 'center', '25%', 'tabfundo0');
				itemLinhaTabela('Títulos', 'center', '5%', 'tabfundo0');
				itemLinhaTabela('Arquivo', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Usuário', 'center', '15%', 'tabfundo0');
				itemLinhaTabela('Data', 'center', '15%', 'tabfundo0');
				itemLinhaTabela('Status', 'center', '10%', 'tabfundo0');
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

			$limite=$i+$limite[lista][arquivosretorno];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$idBanco=resultadoSQL($consulta, $i, 'idBanco');
				$idUsuario=resultadoSQL($consulta, $i, 'idUsuario');
				$data=resultadoSQL($consulta, $i, 'dtArquivo');
				$conteudo=resultadoSQL($consulta, $i, 'conteudo');
				$nomeArquivo=resultadoSQL($consulta, $i, 'nomeArquivo');
				$status=resultadoSQL($consulta, $i, 'status');
				$conteudo=explode("\n", $conteudo);
				
				$banco=dadosBanco($idBanco);
				if ($banco[numero] == '001' || $banco[numero] == '104') {
					#descarta 2 headers + 2 trailer
					#divide por 2 pq sao 2 segmentos por registro
					$qtdeTitulos=ceil( (count($conteudo)-4)/2 ) ;
				}
				else {
					$qtdeTitulos=count($conteudo)-3; 
				}
				$opcoes=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=processar&registro=$id>Processar</a>",'processar');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela(formSelectBancos($idBanco,'','check'), 'left', '25%', 'normal10');
					itemLinhaTabela($qtdeTitulos, 'center', '5%', 'normal10');
					itemLinhaTabela($nomeArquivo, 'center', '10%', 'normal10');
					itemLinhaTabela(checaUsuario($idUsuario), 'center', '15%', 'normal10');
					itemLinhaTabela(converteData($data,'banco','form'), 'center', '15%', 'normal8');
					itemLinhaTabela(formSelectStatus($status,'','check'), 'center', '10%', 'normal8');
					itemLinhaTabela($opcoes, 'left nowrap', '20%', 'normal8');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem

	fechaTabela();
	
} # fecha função de listagem



# Função para cancelar faturamentos
function importarArquivosRetorno($modulo, $sub, $acao, $registro, $matriz) {

	# Mostrar detalhes do faturamento
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $limite, $sessPlanos, $conn, $tb, $configMensagemArquivo;
	
	if($matriz[bntConfirmar]) {
		# Gerar arquivo e mostrar link para download
		
		# carregar imagem do arquivo
		$arquivo=$_REQUEST["arquivo"];
		
		$matriz[conteudo]=(fread
                 (fopen ($_FILES["arquivo"]["tmp_name"], "r"),
                 filesize ($_FILES["arquivo"]["tmp_name"])));
					  
		$matriz[nomeArquivo]=$_FILES[arquivo][name];
		
		# Validar arquivo
		$validarArquivo=validaLayout($matriz,$matriz[idBanco]);
		
		if($validarArquivo) {
			# Ocorreu algum erro na leitura do arquivo
			avisoNOURL("Aviso", $validarArquivo, 400);
			echo "<br>";
		}
		else {
		
			$gravaArquivo=dbArquivoRetorno($matriz, 'incluir');
		
			if($gravaArquivo) {
				# OK
				$msg="Arquivo importado com sucesso!";
				avisoNOURL("Importação de arquivo retorno", $msg, 400);
				echo "<br>";
			}
			else {
				# erro
				$msg="ERRO: Ocorreu um erro ao importar o arquivo de retorno!";
				avisoNOURL("Aviso: Ocorrência de erro", $msg, 400);
				echo "<br>";
			}
		}
	
		listarArquivosRetorno($modulo, $sub, 'listar',$registro, '');

	}
	else {
		formImportarArquivoRetorno($modulo, $sub, $acao, $registro, $matriz);
	}

}



# função para form de seleção de filtros de faturamento
function formImportarArquivoRetorno($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $configMensagemArquivo;
	
	
	if($matriz[sem_mensagem]=='S') {
		$opcSemMensagem='checked';
		$matriz[mensagem]='';
		$matriz[mensagem2]='';
	}
	
	# Motrar tabela de busca
	novaTabela2("[Importação de Arquivo de Retorno]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		novaLinhaTabela($corFundo, '100%');
		$texto="			
			<form method=post name=matriz action=index.php enctype='multipart/form-data'>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=registro value=$registro>&nbsp;";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Banco:</b><br>
			<span class=normal10>Selecione o banco referente ao arquivo</span>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm(formSelectBancos($matriz[idBanco],'idBanco','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Arquivo:</b><br>
			<span class=normal10>Selecione o arquivo a importar</span>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
			$texto="<input type=file name=arquivo>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
		
		if(!$matriz[bntConfirmar]) formPessoaSubmit($modulo, $sub, $acao, $registro, $matriz);
	fechaTabela();
	
}




# Função para validação de layout retornado
function validaLayout($matriz, $tipo) {

	$matRetorno=explode("\n",$matriz[conteudo]);
	# numero do banco
	$banco=dadosBanco($matriz[idBanco]);
	
	if(is_array($matRetorno)) {
		# valida para itau
		if( substr($matRetorno[0],0,2)=='02' && substr($matRetorno[0],2,7)=='RETORNO') {		
			if(substr($matRetorno[0],76,3)!=$banco[numero]) {
				# Layout invalido com o banco informado
				$retorno="Este arquivo não é retorno do ITAU [".substr($matRetorno[0],76,3)."]";
			}
		} 
		# Banco do Brasil
		elseif(substr($matRetorno[0],0,3)==$banco[numero]) {
			if (substr($matRetorno[1],8,1)!='T') {
				# Layout invalido com o banco informado
				$retorno="Este arquivo não é retorno do BB $banco[numero] [" . substr($matRetorno[0],0,3) . "]";
			}
		}
		# Banco do Brasil - debito
		elseif(substr($matRetorno[0],0,1)=='A') {
			if (strlen(trim($matRetorno[1]))!=150) {
				# Layout invalido com o banco informado
				$retorno="Este arquivo não é retorno de debito automatico padrão FEBRABAN[" . strlen(trim($matRetorno[1]))!=150 . "]";
			}
		}
		# CEF
		elseif(substr($matRetorno[0],0,3)==$banco[numero]) {
			if (substr($matRetorno[1],8,1)!='T') {
				# Layout invalido com o banco informado
				$retorno="Este arquivo não é retorno da CEF [" . substr($matRetorno[0],0,3) . "]";
			}
		}
		else {
			$retorno="Arquivo não apresenta layout de retorno";
		}
	}
	else {
		$retorno="Arquivo não contém registros";
	}
	
	return($retorno);
}



# Função para processamento de Arquivo Retorno
function excluirArquivosRetorno($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $html;
	
	# Receber informação sobre arquivo retorno a ser tratado
	if(is_numeric($registro)) {
	
		if(!$matriz[bntExcluir]) {
			$consulta=buscaArquivosRetorno($registro, 'id','igual','id');
			
			if($consulta && contaConsulta($consulta)>0) {
			
				$id=resultadoSQL($consulta, 0, 'id');
				$idArquivo=resultadoSQL($consulta, 0, 'idArquivo');
				$idBanco=resultadoSQL($consulta, 0, 'idBanco');
				$idUsuario=resultadoSQL($consulta, 0, 'idUsuario');
				$dtArquivo=resultadoSQL($consulta, 0, 'dtArquivo');
				$nomeArquivo=resultadoSQL($consulta, 0, 'nomeArquivo');
				$conteudo=resultadoSQL($consulta, 0, 'conteudo');
				$status=resultadoSQL($consulta, 0, 'status');
				
				# Motrar tabela de busca
				novaTabela2("[Excluir Arquivos Retorno]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
					novaLinhaTabela($corFundo, '100%');
					$texto="			
						<form method=post name=matriz action=index.php>
						<input type=hidden name=modulo value=$modulo>
						<input type=hidden name=sub value=$sub>
						<input type=hidden name=acao value=$acao>
						<input type=hidden name=registro value=$registro>
						<input type=hidden name=matriz[id] value=$registro>
						&nbsp;";
						itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>Banco:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
						itemLinhaForm(formSelectBancos($idBanco,'','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>Usuário:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
						itemLinhaForm(checaUsuario($idUsuario), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>Data:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
						itemLinhaForm(converteData($dtArquivo,'banco','form'),'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>Status:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
						itemLinhaForm(formSelectStatusRetorno($status,'','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
					novaLinhaTabela($corFundo, '100%');
						$texto="<input type=submit name=matriz[bntExcluir] value=Excluir class=submit>";
						itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
					fechaLinhaTabela();
				fechaTabela();
			}
			
		}
		else {
			# Excluir
			dbArquivoRetorno($matriz, 'excluir');
			listarArquivosRetorno($modulo, $sub, 'listar', 0, $matriz);
		}
	}
}

# Função para processamento de Arquivo Retorno
function processarArquivoRetorno($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $html;
	
	# Receber informação sobre arquivo retorno a ser tratado
	if(is_numeric($registro)) {
	
		$consulta=buscaArquivosRetorno($registro, 'id','igual','id');
		
		if($consulta && contaConsulta($consulta)>0) {
		
			$id=resultadoSQL($consulta, 0, 'id');
			$idArquivo=resultadoSQL($consulta, 0, 'idArquivo');
			$idBanco=resultadoSQL($consulta, 0, 'idBanco');
			$idUsuario=resultadoSQL($consulta, 0, 'idUsuario');
			$dtArquivo=resultadoSQL($consulta, 0, 'dtArquivo');
			$nomeArquivo=resultadoSQL($consulta, 0, 'nomeArquivo');
			$conteudo=resultadoSQL($consulta, 0, 'conteudo');
			$status=resultadoSQL($consulta, 0, 'status');
			
			# Motrar tabela de busca
			novaTabela2("[Processamento de Arquivos Retorno]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				novaLinhaTabela($corFundo, '100%');
				$texto="			
					<form method=post name=matriz action=index.php>
					<input type=hidden name=modulo value=$modulo>
					<input type=hidden name=sub value=$sub>
					<input type=hidden name=acao value=$acao>
					<input type=hidden name=registro value=$registro>&nbsp;";
					itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Arquivo:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
					itemLinhaForm($nomeArquivo, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Banco:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
					itemLinhaForm(formSelectBancos($idBanco,'','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Usuário:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
					itemLinhaForm(checaUsuario($idUsuario), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Data:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
					itemLinhaForm(converteData($dtArquivo,'banco','form'),'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Status:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
					itemLinhaForm(formSelectStatusRetorno($status,'','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				if($status=='N') {
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>Efetivar processamento:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
						if($matriz[efetivar]=='S') $opcEfetivar='checked';
						$texto="<input type=checkbox name=matriz[efetivar] value=S $opcEfetivar> <span class=txtaviso>(Efetivar Lançamentos de retorno)</span>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				if(!$matriz[bntProcessar] || !$matriz[efetivar]) {
					itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
					novaLinhaTabela($corFundo, '100%');
						$texto="<input type=submit name=matriz[bntProcessar] value='Processar Arquivo' class=submit>";
						itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
					fechaLinhaTabela();
				}
				
				if($matriz[bntProcessar]) {
					# Processar o arquivo
					if($registro && $matriz[bntProcessar])  {
						novaLinhaTabela($corFundo, '100%');
							htmlAbreColuna('100%', 'center', $corFundo, 2, 'tabfundo1');
								processarLayoutArquivo($modulo, $sub, $acao, $registro, $matriz);	
							htmlFechaColuna();
						fechaLinhaTabela();
					}
				}
			fechaTabela();
		}
	}
}




# Processar layout do arquivo para dar entrada dos documentos
function processarLayoutArquivo($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $tb, $conn;

//	#codigo para o debito automatico
//	$listaCodigoRetorno=array();
//	$listaCodigoRetorno["00"]="Débito efetuado";
//	$listaCodigoRetorno["01"]="Insuficência de fundos";
//	$listaCodigoRetorno["02"]="Conta Corrente não cadastrada";
//	$listaCodigoRetorno["04"]="Outras restrições";
//	$listaCodigoRetorno["10"]="Agência em regime encerramento";
//	$listaCodigoRetorno["12"]="Valor inválido";
//	$listaCodigoRetorno["13"]="Data do lançamento inválido";
//	$listaCodigoRetorno["14"]="Agência invalida";
//	$listaCodigoRetorno["15"]="DAC da conta corrente inválido";
//	$listaCodigoRetorno["18"]="Data do débito anterior a do processamento";
//	$listaCodigoRetorno["30"]="cliente sem contrato de Débito Automático";
//	$listaCodigoRetorno["96"]="Manutenção de cadastro";
//	$listaCodigoRetorno["97"]="Cancelamento não encontrado";
//	$listaCodigoRetorno["98"]="Cancelamento não efetuado(fora do prazo)";
//	$listaCodigoRetorno["99"]="Cancelamento efetuado";
	
	# Verificar banco
	if($registro && is_numeric($registro)) {
		# Buscar layout
		$consulta=buscaArquivosRetorno($registro, 'id','igual','id');
		
		if($consulta && contaConsulta($consulta)>0) {
		
			$id=resultadoSQL($consulta, 0, 'id');
			$idArquivo=resultadoSQL($consulta, 0, 'idArquivo');
			$idBanco=resultadoSQL($consulta, 0, 'idBanco');
			$idUsuario=resultadoSQL($consulta, 0, 'idUsuario');
			$dtArquivo=resultadoSQL($consulta, 0, 'dtArquivo');
			$nomeArquivo=resultadoSQL($consulta, 0, 'nomeArquivo');
			$conteudo=resultadoSQL($consulta, 0, 'conteudo');
			$status=resultadoSQL($consulta, 0, 'status');


			# Quebrar conteúdo
			$tmpDados=explode("\n",$conteudo);

			# Debito Automatico de acordo com os padroes da Febraban
			if(strlen(trim($tmpDados[1]))==150 && strtoupper(arquivoLerDados($tmpDados,'matriz', 0, 82, 17)) == "DEBITO AUTOMATICO"){
				echo "<br>";
				novaTabela2("[Detalhe do Processamento]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
				
				for($a=0;$a<count($tmpDados);$a++) {
					$tipoRegistro=$tipoServico=arquivoLerDados($tmpDados,'matriz', $a, 1, 1);
				
						# tipo A - header
						if($tipoRegistro == 'A') {
							#codigo do registro					001-001 - x(001)
							$codigoRegistro=arquivoLerDados($tmpDados,'matriz',$a, 1, 1);
							#codigo do remessa					002-002 - 9(001)
							# 1 - remessa
							# 2 - retorno
							$codigoRemessa=arquivoLerDados($tmpDados,'matriz',$a, 2, 1);
							#codigo do convenio					003-022 - x(020)
							$codigoConvenio=arquivoLerDados($tmpDados,'matriz',$a, 3, 20);
							#Nome da empresa					023-042 - x(020)
							$nomeEmpresa=arquivoLerDados($tmpDados,'matriz',$a, 23, 20);
							#codigo do banco					043-045 - 9(003)
							$codigoBanco=arquivoLerDados($tmpDados,'matriz',$a, 43, 3);
							#Nome do banco						046-065 - x(020)
							$nomeBanco=arquivoLerDados($tmpDados,'matriz',$a, 46, 20);
							#Data da gravacao					066-073 - 9(008)
							$dataGravacao=arquivoLerDados($tmpDados,'matriz',$a, 66, 8);
							#Numero sequencial do arquivo		074-079 - 9(006)
							#numero deve evoluir de 1 em 1 para cada arquivo gerado
							$numeroSeqArquivo=arquivoLerDados($tmpDados,'matriz',$a, 74,6);
							#Versao do layout					080-081 - 9(002)
							$versaoLayout=arquivoLerDados($tmpDados,'matriz',$a, 80, 2);
							#Produto							082-098 - x(017)
							$produto=arquivoLerDados($tmpDados,'matriz',$a, 82, 17);
							
							#exibe os resultados
							novaLinhaTabela($corFundo, '100%');
								itemLinhaTMNOURL('<b>Código do Banco:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
								itemLinhaForm($codigoBanco, 'left', 'top', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
							novaLinhaTabela($corFundo, '100%');
								itemLinhaTMNOURL('<b>Banco:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
								itemLinhaForm($nomeBanco, 'left', 'top', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
							novaLinhaTabela($corFundo, '100%');
								itemLinhaTMNOURL('<b>Nome Empresa:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
								itemLinhaForm($nomeEmpresa, 'left', 'top', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
							novaLinhaTabela($corFundo, '100%');
								itemLinhaTMNOURL('<b>Data Gravação:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
								#itemLinhaForm(converteData($dtGeracao,'arquivo','formdata'), 'left', 'top', $corFundo, 0, 'tabfundo1');
								itemLinhaForm( converteData( $dataGravacao,'arquivo','form' ), 'left', 'top', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
							novaLinhaTabela($corFundo, '100%');
								itemLinhaTMNOURL('<b>Numero do Arquivo:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
//								itemLinhaForm($numeroSeqRetorno, 'left', 'top', $corFundo, 0, 'tabfundo1');
								itemLinhaForm($numeroSeqArquivo, 'left', 'top', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
							
							novaLinhaTabela($corFundo, '100%');
								htmlAbreColuna('100%', 'center', $corFundo, 2, 'tabfundo1');
								novaTabela("[Listagem de Documentos Retornardos]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 9);
									novaLinhaTabela($corFundo, '100%');
										itemLinhaTMNOURL('Documento', 'center', 'top', '10%', $corFundo, 0, 'tabfundo0');
										itemLinhaTMNOURL('Sacado', 'center', 'top', '30%', $corFundo, 0, 'tabfundo0');
										itemLinhaTMNOURL('Pessoa', 'center', 'top', '10%', $corFundo, 0, 'tabfundo0');
										itemLinhaTMNOURL('Ocorrência', 'center', 'top', '15%', $corFundo, 0, 'tabfundo0');
										itemLinhaTMNOURL('Dt Cred', 'center nowrap', 'top', '5%', $corFundo, 0, 'tabfundo0');
										itemLinhaTMNOURL('Valor Tit.', 'center nowrap', 'top', '10%', $corFundo, 0, 'tabfundo0');
										itemLinhaTMNOURL('Recebido', 'center', 'top', '10%', $corFundo, 0, 'tabfundo0');
									fechaLinhaTabela();
							
						}// fim do header
						
						# tipo B - Cadastramento do debito automatico
						else if($tipoRegistro == 'B') {
							#codigo do registro					001-001 - x(001)
							$codigoRegistro=arquivoLerDados($tmpDados,'matriz',$a, 1, 1);
							#Identif. do cliente na empresa		002-026 - x(025)
							$clienteEmpresa=arquivoLerDados($tmpDados,'matriz',$a, 2, 25);
							#Agencia de debito					027-030 - x(004)
							$agenciaDebito=arquivoLerDados($tmpDados,'matriz',$a, 27, 04);
							#Identif. do cliente no banco		031-044 - x(014)
							$clienteBanco=arquivoLerDados($tmpDados,'matriz',$a, 31, 14);
							#data da opcao ou exclusao			045-052 - 9(008)
							$dataOpcao=arquivoLerDados($tmpDados,'matriz',$a, 45, 8);
							#Codigo Movimento					150-150 - x(001)
							# 1 - exclusao de optante pelo debito automatico
							# 2 - inclusao de optante pelo debito automatico
							$codigoMovimento=arquivoLerDados($tmpDados,'matriz',$a, 150, 1);
							
							#exibe os resultados
							novaLinhaTabela($corFundo, '100%');
								itemLinhaTMNOURL('<b>Cliente:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
								itemLinhaForm($clienteEmpresa, 'left', 'top', $corFundo, 0, 'tabfundo1');
							fechaLinhaTabela();
						}// fim do cadastro
						

						elseif($tipoRegistro == 'F') {
						
							$numProcessados++;
							
							if($tipoServico) {
								# Transação
								
								$agenciaCliente = arquivoLerDados($tmpDados,'matriz',$a, 27, 4);
								$contaCliente = arquivoLerDados($tmpDados,'matriz',$a, 31, 14);
								$dtVencimento = arquivoLerDados($tmpDados,'matriz',$a, 45, 8);
								$valorRecebido = arquivoLerDados($tmpDados,'matriz',$a, 53, 15);
								$codigoOcorrencia = arquivoLerDados($tmpDados,'matriz',$a, 68, 2);
								$numeroDocumento = arquivoLerDados($tmpDados,'matriz',$a, 70, 60);
								$codigoMovimento = arquivoLerDados($tmpDados,'matriz',$a, 150, 1);
								
								if ($codigoOcorrencia != "00")
									$valorRecebido = 0;
								
								if($numeroDocumento && strlen(trim($numeroDocumento)>0) ) {
									
									$numeroDocumento = intval($numeroDocumento);
									
									# Buscar informação do documento
									$sqlDocumentos="
										SELECT 
											$tb[DocumentosGerados].id,
											$tb[DocumentosGerados].idFaturamento,
											$tb[DocumentosGerados].idPessoaTipo,
											$tb[ContasReceber].id idContasAReceber,
											$tb[ContasReceber].status status,
											$tb[ContasReceber].valor valor,
											$tb[Pessoas].nome nomePessoa
										FROM 
											$tb[Pessoas],
											$tb[PessoasTipos],
											$tb[DocumentosGerados],
											$tb[ContasReceber]
										WHERE
											$tb[DocumentosGerados].idPessoaTipo=$tb[PessoasTipos].id
											AND $tb[PessoasTipos].idPessoa=$tb[Pessoas].id
											AND $tb[DocumentosGerados].id=$tb[ContasReceber].idDocumentosGerados
											AND $tb[DocumentosGerados].id=$numeroDocumento";

										
									$consulta=consultaSQL($sqlDocumentos, $conn);
									
									if($consulta && contaConsulta($consulta)>0) {
										
										$nomePessoa=resultadoSQL($consulta, 0, 'nomePessoa');
										$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
										$idContasAReceber=resultadoSQL($consulta, 0, 'idContasAReceber');
										$valor=resultadoSQL($consulta, 0, 'valor');
										$status=resultadoSQL($consulta, 0, 'status');
					
										
										# Se ValorPrincipal for recebido, creditar valor e baixar boleto
										
										# Verificar status do documento
										if($status) {
											# Mostrar dados do titulo
											novaLinhaTabela($corFundo, '100%');
												$DescTituloEmpresa=htmlMontaOpcao("<a href=?modulo=faturamento&sub=clientes&acao=dados_cobranca&registro=$idContasAReceber target=_BLANK>$tituloEmpresa</a></a>",'lancamento');
												itemLinhaTMNOURL($DescTituloEmpresa, 'center', 'top', '10%', $corFundo, 0, 'normal10');
												itemLinhaTMNOURL($numeroDocumento, 'center', 'top', '10%', $corFundo, 0, 'normal10');
												itemLinhaTMNOURL($nomePessoa, 'left', 'top', '33%', $corFundo, 0, 'normal8');
												itemLinhaTMNOURL(layoutOcorrenciasDebitoPadrao($codigoOcorrencia), 'center', 'top', '15%', $corFundo, 0, 'normal8');
												itemLinhaTMNOURL(converteData($dtVencimento,'retorno','formdata'), 'center', 'top', '5%', $corFundo, 0, 'normal8');
												itemLinhaTMNOURL(formatarValoresForm($valor), 'right', 'top', '10%', $corFundo, 0, 'normal10');
												itemLinhaTMNOURL(formatarValoresArquivoRetorno($valorRecebido), 'right', 'top', '10%', $corFundo, 0, 'normal10');
											fechaLinhaTabela();
											# Mostrar cabecalho
											
											# Incrementar valor total
											$valorTotal+=formatarValores($valor);
											$valorTotalRecebido+=formatarValores($valorRecebido);
																				
											# Em caso de efetivação - validar dados para BD
											if($matriz[efetivar] && $codigoOcorrencia=="00") {
												# Importar dados para banco
												$matriz[id]=$idContasAReceber;
												$matriz[valorRecebido]=formatarValores($valorRecebido);
												$matriz[dtBaixa]=formatarData(converteData($dtVencimento,'retorno','formdata'));
												$matriz[obs]="Arquivo Retorno: Ocorrencia: " . layoutOcorrenciasBansicredi($codigoOcorrencia);
												
												dbContasReceber($matriz, 'baixar');
												
												$numRetornados++;
											}
										}
									}
								}
							}
						} // tipo F			


						# tipo H - Ocorrencia de alteração do controle da empresa
						else if($tipoRegistro == 'H') {
							#codigo do registro					001-001 - x(001)
							$codigoRegistro=arquivoLerDados($tmpDados,'matriz',$a, 1, 1);
							#Identif. do cliente na empresa		002-026 - x(025)
							$clienteEmpresa=arquivoLerDados($tmpDados,'matriz',$a, 2, 25);
							#Agencia de debito					027-030 - x(004)
							$agenciaDebito=arquivoLerDados($tmpDados,'matriz',$a, 27, 04);
							#Identif. do cliente no banco		031-044 - x(014)
							$clienteBanco=arquivoLerDados($tmpDados,'matriz',$a, 31, 14);
							#Nova identificacao do cliente		045-069 - x(025)
							$novaIdentificacao=arquivoLerDados($tmpDados,'matriz',$a, 45, 25);
							#Ocorrencia							070-127 - x(058)
							$clienteBanco=arquivoLerDados($tmpDados,'matriz',$a, 70, 58);
							#Codigo Movimento					150-150 - x(001)
							$nomeBanco=arquivoLerDados($tmpDados,'matriz',$a, 150, 1);
						} //tipo H
						
						# tipo X - Relação de agencias (so é enviado quando solicitado pela empresa)
						else if($tipoRegistro == 'X') {
							#codigo do registro					001-001 - x(001)
							$codigoRegistro=arquivoLerDados($tmpDados,'matriz',$a, 1, 1);
							#Codigo da Agencia					002-005 - x(004)
							$codigoAgencia=arquivoLerDados($tmpDados,'matriz',$a, 2, 4);
							#Nome da Agencia					006-035 - x(030)
							$nomeAgencia=arquivoLerDados($tmpDados,'matriz',$a, 6, 30);
							#Endereco da Agencia				036-065 - x(030)
							$enderecoAgencia=arquivoLerDados($tmpDados,'matriz',$a, 36, 30);
							#Numero								066-070 - x(005)
							$numeroEnderecoAgencia=arquivoLerDados($tmpDados,'matriz',$a, 66, 5);
							#CEP								071-075 - x(005)
							$cepEnderecoAgencia=arquivoLerDados($tmpDados,'matriz',$a, 71, 5);
							#Sufixo CEP							076-078 - x(005)
//							$sufCepEnderecoAgencia=arquivoLerDados($tmpDados,'matriz',$a, 76, 5);
							$sufCepEnderecoAgencia=arquivoLerDados($tmpDados,'matriz',$a, 76, 3);
							#Cidade								079-098 - x(020)
//							$cidadeEnderecoAgencia=arquivoLerDados($tmpDados,'matriz',$a, 79, 98);
							$cidadeEnderecoAgencia=arquivoLerDados($tmpDados,'matriz',$a, 79, 20);
							#Sigla do estado					099-100 - x(002)
							$ufEnderecoAgencia=arquivoLerDados($tmpDados,'matriz',$a, 99, 2);
							#Situação da agencia				101-101 - x(001)
							# A - ativa
							# B - em regime de encerramento
							$situacaoAgencia=arquivoLerDados($tmpDados,'matriz',$a, 101,1);
						}//X
						
						# tipo Z - trailler de arquivo
						else if($tipoRegistro == 'Z') {
							#codigo do registro					001-001 - x(001)
							$codigoRegistro=arquivoLerDados($tmpDados,'matriz',$a, 1, 1);
							#Quantidade de registros			002-007 - 9(006)
							$quantidadeRegistro=arquivoLerDados($tmpDados,'matriz',$a, 2, 6);
							#Valor Total						008-024 - 9(017)
							$valorTotalRetorno=arquivoLerDados($tmpDados,'matriz',$a, 8, 17);
						}//Z


					}// fim da repeticao						
									
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL("Processados: $numProcessados / Retornados: $numRetornados", 'center', 'top', '10%', $corFundo, 3, 'tabfundo0');
						itemLinhaTMNOURL('Totais:', 'right', 'top', '10%', $corFundo, 2, 'bold10');
						itemLinhaTMNOURL(formatarValoresForm($valorTotal), 'right', 'top', '10%', $corFundo, 0, 'txtok');
						itemLinhaTMNOURL(formatarValoresForm($valorTotalRecebido), 'right', 'top', '5%', $corFundo, 0, 'txtok');
					fechaLinhaTabela();
					fechaTabela();
					htmlFechaColuna();
					fechaLinhaTabela();
					
					fechaTabela();
					
					# Efetivar arquivo
					if($matriz[efetivar]=='S') {
						$matriz[id]=$id;
						dbArquivoRetorno($matriz, 'ativar');
					}
			
			}// Debito Automatico
			else { //nao é debito automatico
				# Identificar tipo
				$banco=dadosBanco($idBanco);
				
				# Banco Itau
				if($banco[numero]=='341') {
					# Layout Itau
					# Mostrar cabecalho
					echo "<br>";
					novaTabela2("[Detalhe do Processamento]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
					itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
	
	//				# Quebrar conteúdo
	//				$tmpDados=explode("\n",$conteudo);
					
					# Zerar valor total
					$valorTotal=0;
					
					# Contadores
					$numRetornados;
					$numProcessados;
					# Layout do itau começa da 2a. linha
					for($a=0;$a<count($tmpDados);$a++) {
						# Verificar Tipo de registro
						$tipoRegistro=$tipoServico=arquivoLerDados($tmpDados,'matriz', $a, 1, 1);
						
						if($tipoRegistro == '0') {                                                            
							# Header
							# Identificação de tipo de arquivo
							$tipoArquivo=arquivoLerDados($tmpDados, 'matriz', $a, 2, 1);
							
							if($tipoArquivo == '2') {
								# Arquivo é retorno
								$tipoServico=arquivoLerDados($tmpDados,'matriz',$a, 10, 2);
								$agencia=arquivoLerDados($tmpDados,'matriz',$a, 27, 4);
								$contaNumero=arquivoLerDados($tmpDados,'matriz',$a, 33, 5);
								$contaDig=arquivoLerDados($tmpDados,'matriz',$a, 38, 1);
								$nomeEmpresa=arquivoLerDados($tmpDados,'matriz',$a, 47, 30);
								$codigoBanco=arquivoLerDados($tmpDados,'matriz',$a, 77, 3);
								$nomeBanco=arquivoLerDados($tmpDados,'matriz',$a, 80, 15);
								$dtGeracao=arquivoLerDados($tmpDados,'matriz',$a, 95, 6);
								$numeroSeqRetorno=arquivoLerDados($tmpDados,'matriz',$a, 109, 5);
								$dtCredito=arquivoLerDados($tmpDados,'matriz',$a, 114, 6);
								$numeroSeqArquivo=arquivoLerDados($tmpDados,'matriz',$a, 395, 6);
								
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Código do Banco:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($banco[numero], 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Agência:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($agencia, 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Conta:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($contaNumero.'-'.$contaDig, 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Nome Empresa:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($nomeEmpresa, 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Data Geração:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm(converteData($dtGeracao,'arquivo','formdata'), 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Data de Crédito:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm(converteData($dtCredito,'arquivo','formdata'), 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Numero do Arquivo:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($numeroSeqRetorno, 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									htmlAbreColuna('100%', 'center', $corFundo, 2, 'tabfundo1');
									novaTabela("[Listagem de Documentos Retornardos]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 9);
										novaLinhaTabela($corFundo, '100%');
											itemLinhaTMNOURL('Documento', 'center', 'top', '10%', $corFundo, 0, 'tabfundo0');
											itemLinhaTMNOURL('Sacado', 'center', 'top', '30%', $corFundo, 0, 'tabfundo0');
											itemLinhaTMNOURL('Ocorrência', 'center', 'top', '15%', $corFundo, 0, 'tabfundo0');
											itemLinhaTMNOURL('Dt Ocor.', 'center nowrap', 'top', '5%', $corFundo, 0, 'tabfundo0');
											itemLinhaTMNOURL('Dt Cred', 'center nowrap', 'top', '5%', $corFundo, 0, 'tabfundo0');
											itemLinhaTMNOURL('Valor Tit.', 'center nowrap', 'top', '10%', $corFundo, 0, 'tabfundo0');
											itemLinhaTMNOURL('Juros', 'center', 'top', '5%', $corFundo, 0, 'tabfundo0');
											itemLinhaTMNOURL('Desc', 'center', 'top', '5%', $corFundo, 0, 'tabfundo0');
											itemLinhaTMNOURL('Recebido', 'center', 'top', '10%', $corFundo, 0, 'tabfundo0');
										fechaLinhaTabela();
									
							}
						}
						elseif($tipoRegistro=='1') {
						
							$numProcessados++;
							
							if($tipoServico) { 
								# Transação
								$codigoInscricao=arquivoLerDados($tmpDados,'matriz',$a, 2, 2);
								$numeroInscricao=arquivoLerDados($tmpDados,'matriz',$a, 4, 14);
								$agenciaMantenedora=arquivoLerDados($tmpDados,'matriz',$a, 18, 4);
								$contaNumero=arquivoLerDados($tmpDados,'matriz',$a, 24, 5);
								$contaDigitoconta=arquivoLerDados($tmpDados,'matriz',$a, 29, 1);
								$tituloEmpresa=arquivoLerDados($tmpDados,'matriz',$a, 38, 25);
								$carteira=arquivoLerDados($tmpDados,'matriz',$a, 83, 3);
								$codigoOcorrencia=arquivoLerDados($tmpDados,'matriz',$a, 109, 2);
								$dtOcorrencia=arquivoLerDados($tmpDados,'matriz',$a, 111, 6);
								$numeroDocumento=arquivoLerDados($tmpDados,'matriz',$a, 117, 10);
								$nossoNumero=arquivoLerDados($tmpDados,'matriz',$a, 127, 8);
								$dtVencimento=arquivoLerDados($tmpDados,'matriz',$a, 147, 6);
								$valorTitulo=arquivoLerDados($tmpDados,'matriz',$a, 153, 13);
								$tarifaCobranca=arquivoLerDados($tmpDados,'matriz',$a, 176, 13);
								$valorIOF=arquivoLerDados($tmpDados,'matriz',$a, 215, 13);
								$valorAbatimento=arquivoLerDados($tmpDados,'matriz',$a, 228, 13);
								$valorDescontos=arquivoLerDados($tmpDados,'matriz',$a, 241, 13);
								$valorPrincipal=arquivoLerDados($tmpDados,'matriz',$a, 254, 13);
								$jurosMoraMulta=arquivoLerDados($tmpDados,'matriz',$a, 267, 13);
								$outrosCreditos=arquivoLerDados($tmpDados,'matriz',$a, 280, 13);
								$dtCredito=arquivoLerDados($tmpDados,'matriz',$a, 296, 6);
								$InstrCancelada=arquivoLerDados($tmpDados,'matriz',$a, 302, 4);
								$nomeSacado=arquivoLerDados($tmpDados,'matriz',$a, 325, 30);
								$erros=arquivoLerDados($tmpDados,'matriz',$a, 378, 8);
								$codLiquidacao=arquivoLerDados($tmpDados,'matriz',$a, 393, 2);
								$numeroSeqArquivo=arquivoLerDados($tmpDados,'matriz',$a, 395, 6);
								
								if($tituloEmpresa && strlen(trim($tituloEmpresa)>0) && is_numeric(trim($tituloEmpresa))) {
									
									$tituloEmpresa=intval($tituloEmpresa);
									
									# Buscar informação do documento
									$sqlDocumentos="
										SELECT 
											$tb[DocumentosGerados].id,
											$tb[DocumentosGerados].idFaturamento,
											$tb[DocumentosGerados].idPessoaTipo,
											$tb[ContasReceber].id idContasAReceber,
											$tb[ContasReceber].status status,
											$tb[Pessoas].nome nomePessoa
										FROM 
											$tb[Pessoas],
											$tb[PessoasTipos],
											$tb[DocumentosGerados],
											$tb[ContasReceber]
										WHERE
											$tb[DocumentosGerados].idPessoaTipo=$tb[PessoasTipos].id
											AND $tb[PessoasTipos].idPessoa=$tb[Pessoas].id
											AND $tb[DocumentosGerados].id=$tb[ContasReceber].idDocumentosGerados
											AND $tb[DocumentosGerados].id=$tituloEmpresa";
											
									$consulta=consultaSQL($sqlDocumentos, $conn);
									
									if($consulta && contaConsulta($consulta)>0) {
										
										$nomePessoa=resultadoSQL($consulta, 0, 'nomePessoa');
										$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
										$idContasAReceber=resultadoSQL($consulta, 0, 'idContasAReceber');
										$status=resultadoSQL($consulta, 0, 'status');
										
										# Se ValorPrincipal for recebido, creditar valor e baixar boleto
										
										# Verificar status do documento
										if($status) {
											# Mostrar dados do titulo
											novaLinhaTabela($corFundo, '100%');
												$DescTituloEmpresa=htmlMontaOpcao("<a href=?modulo=faturamento&sub=clientes&acao=dados_cobranca&registro=$idContasAReceber target=_BLANK>$tituloEmpresa</a></a>",'lancamento');
												itemLinhaTMNOURL($DescTituloEmpresa, 'center', 'top', '10%', $corFundo, 0, 'normal10');
												itemLinhaTMNOURL($nomePessoa, 'left', 'top', '33%', $corFundo, 0, 'normal8');
												itemLinhaTMNOURL(layoutOcorrenciasItau($codigoOcorrencia), 'center', 'top', '15%', $corFundo, 0, 'normal8');
												itemLinhaTMNOURL(converteData($dtOcorrencia,'arquivo','formdata'), 'center', 'top', '5%', $corFundo, 0, 'normal8');
												itemLinhaTMNOURL(converteData($dtCredito,'arquivo','formdata'), 'center', 'top', '5%', $corFundo, 0, 'normal8');
												itemLinhaTMNOURL(formatarValoresArquivoRetorno($valorTitulo), 'right', 'top', '10%', $corFundo, 0, 'normal10');
												itemLinhaTMNOURL(formatarValoresArquivoRetorno($jurosMoraMulta), 'right', 'top', '5%', $corFundo, 0, 'normal10');
												itemLinhaTMNOURL(formatarValoresArquivoRetorno($valorDescontos), 'right', 'top', '5%', $corFundo, 0, 'normal10');
												itemLinhaTMNOURL(formatarValoresArquivoRetorno($valorPrincipal), 'right', 'top', '10%', $corFundo, 0, 'normal10');
											fechaLinhaTabela();
											# Mostrar cabecalho
											
											# Incrementar valor total
											$valorTotal+=formatarValores($valorTitulo);
											$valorTotalRecebido+=formatarValores($valorPrincipal);
											$valorTotalJuros+=formatarValores($jurosMoraMulta);
											$valorTotalDescontos+=formatarValores($valorDescontos);
											
											# Em caso de efetivação - validar dados para BD
											if($matriz[efetivar] && formatarValores($valorPrincipal)>0) {
												# Importar dados para banco
												$matriz[id]=$idContasAReceber;
												$matriz[valorRecebido]=formatarValores($valorPrincipal);
												$matriz[valorJuros]=formatarValores($jurosMoraMulta);
												$matriz[valorDesconto]=formatarValores($valorDescontos);
												$matriz[dtBaixa]=converteData($dtOcorrencia,'arquivo','banco');
												
												$matriz[obs]="Arquivo Retorno: Ocorrencia: " . layoutOcorrenciasItau($codigoOcorrencia);
												
												dbContasReceber($matriz, 'baixar');
												
												$numRetornados++;
											}
										}
									}
									
									# Retornar para banco de dados
								}
	
							}
							elseif($tipoRegistro=='9') {
								# Trailler de arquivo
							}
						}
						else {
							//fechaTabela();
							//htmlFechaColuna();
							//htmlFechaLinha();
							
						}
							
					}
					
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL("Processados: $numProcessados / Retornados: $numRetornados", 'center', 'top', '10%', $corFundo, 3, 'tabfundo0');
						itemLinhaTMNOURL('Totais:', 'right', 'top', '10%', $corFundo, 2, 'bold10');
						itemLinhaTMNOURL(formatarValoresForm($valorTotal), 'right', 'top', '10%', $corFundo, 0, 'txtok');
						itemLinhaTMNOURL(formatarValoresForm($valorTotalJuros), 'right', 'top', '5%', $corFundo, 0, 'txtok');
						itemLinhaTMNOURL(formatarValoresForm($valorTotalDescontos), 'right', 'top', '5%', $corFundo, 0, 'txtok');
						itemLinhaTMNOURL(formatarValoresForm($valorTotalRecebido), 'right', 'top', '5%', $corFundo, 0, 'txtok');
					fechaLinhaTabela();
					fechaTabela();
					htmlFechaColuna();
					fechaLinhaTabela();
					
					fechaTabela();
					
					# Efetivar arquivo
					if($matriz[efetivar]=='S') {
						$matriz[id]=$id;
						dbArquivoRetorno($matriz, 'ativar');
					}
				}
				#
				#-------------------------------------------------------------------------
				#
				elseif($banco[numero]=='001') {
					# Layout BB
					
					# Mostrar cabecalho
					echo "<br>";
					novaTabela2("[Detalhe do Processamento]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
					itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
	
					# Zerar valor total
					$valorTotal=0;
					
					
					# Contadores
					$numRetornados=0;
					$numProcessados=0;
					# primeira linha (0) header de arquivo
					# ultima linha (count-1) trailer de arquivo
					
					for($a=0;$a<count($tmpDados);$a++) {
						
						/* determina o tamanho do registro
						150 = debito automatico
						240 = cobranca normal
						*/
						
	//					if(strlen($tmpDados[$a])==150) {
	//						$numProcessados++;
	//						
	//						# tipo A - header
	//						if(arquivoLerDados($tmpDados, 'matriz', $a, 1, 1) == 'A') {
	//							#codigo do registro					001-001 - x(001)
	//							$codigoRegistro=arquivoLerDados($tmpDados,'matriz',$a, 1, 1);
	//							#codigo do remessa					002-002 - 9(001)
	//							# 1 - remessa
	//							# 2 - retorno
	//							$codigoRemessa=arquivoLerDados($tmpDados,'matriz',$a, 2, 1);
	//							#codigo do convenio					003-022 - x(020)
	//							$codigoConvenio=arquivoLerDados($tmpDados,'matriz',$a, 3, 20);
	//							#Nome da empresa					023-042 - x(020)
	//							$nomeEmpresa=arquivoLerDados($tmpDados,'matriz',$a, 23, 20);
	//							#codigo do banco					043-045 - 9(003)
	//							$codigoBanco=arquivoLerDados($tmpDados,'matriz',$a, 43, 3);
	//							#Nome do banco						046-065 - x(020)
	//							$nomeBanco=arquivoLerDados($tmpDados,'matriz',$a, 46, 20);
	//							#Data da gravacao					066-073 - 9(008)
	//							$dataGravacao=arquivoLerDados($tmpDados,'matriz',$a, 66, 8);
	//							#Numero sequencial do arquivo		074-079 - 9(006)
	//							#numero deve evoluir de 1 em 1 para cada arquivo gerado
	//							$numeroSeqArquivo=arquivoLerDados($tmpDados,'matriz',$a, 74,6);
	//							#Versao do layout					080-081 - 9(002)
	//							$versaoLayout=arquivoLerDados($tmpDados,'matriz',$a, 80, 2);
	//							#Produto							082-098 - x(017)
	//							$produto=arquivoLerDados($tmpDados,'matriz',$a, 82, 17);
	//							
	//							#exibe os resultados
	//							novaLinhaTabela($corFundo, '100%');
	//								itemLinhaTMNOURL('<b>Código do Banco:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
	//								itemLinhaForm($codigoBanco, 'left', 'top', $corFundo, 0, 'tabfundo1');
	//							fechaLinhaTabela();
	//							novaLinhaTabela($corFundo, '100%');
	//								itemLinhaTMNOURL('<b>Banco:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
	//								itemLinhaForm($nomeBanco, 'left', 'top', $corFundo, 0, 'tabfundo1');
	//							fechaLinhaTabela();
	//							novaLinhaTabela($corFundo, '100%');
	//								itemLinhaTMNOURL('<b>Nome Empresa:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
	//								itemLinhaForm($nomeEmpresa, 'left', 'top', $corFundo, 0, 'tabfundo1');
	//							fechaLinhaTabela();
	//							novaLinhaTabela($corFundo, '100%');
	//								itemLinhaTMNOURL('<b>Data Gravação:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
	//								#itemLinhaForm(converteData($dtGeracao,'arquivo','formdata'), 'left', 'top', $corFundo, 0, 'tabfundo1');
	//								itemLinhaForm(converteData($dataGravacao,'arquivo','formdata'), 'left', 'top', $corFundo, 0, 'tabfundo1');
	//							fechaLinhaTabela();
	//							novaLinhaTabela($corFundo, '100%');
	//								itemLinhaTMNOURL('<b>Numero do Arquivo:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
	//								itemLinhaForm($numeroSeqRetorno, 'left', 'top', $corFundo, 0, 'tabfundo1');
	//							fechaLinhaTabela();
	//						}
	//						# tipo B - Cadastramento do debito automatico
	//						else if(arquivoLerDados($tmpDados, 'matriz', $a, 1, 1) == 'B') {
	//							#codigo do registro					001-001 - x(001)
	//							$codigoRegistro=arquivoLerDados($tmpDados,'matriz',$a, 1, 1);
	//							#Identif. do cliente na empresa		002-026 - x(025)
	//							$clienteEmpresa=arquivoLerDados($tmpDados,'matriz',$a, 2, 25);
	//							#Agencia de debito					027-030 - x(004)
	//							$agenciaDebito=arquivoLerDados($tmpDados,'matriz',$a, 27, 04);
	//							#Identif. do cliente no banco		031-044 - x(014)
	//							$clienteBanco=arquivoLerDados($tmpDados,'matriz',$a, 31, 14);
	//							#data da opcao ou exclusao			045-052 - 9(008)
	//							$dataOpcao=arquivoLerDados($tmpDados,'matriz',$a, 45, 8);
	//							#Codigo Movimento					150-150 - x(001)
	//							# 1 - exclusao de optante pelo debito automatico
	//							# 2 - inclusao de optante pelo debito automatico
	//							$codigoMovimento=arquivoLerDados($tmpDados,'matriz',$a, 150, 1);
	//							
	//							#exibe os resultados
	//							novaLinhaTabela($corFundo, '100%');
	//								itemLinhaTMNOURL('<b>Cliente:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
	//								itemLinhaForm($clienteEmpresa, 'left', 'top', $corFundo, 0, 'tabfundo1');
	//							fechaLinhaTabela();
	//							
	//							
	//							
	//						}
	//						# tipo F - Retorno do debito
	//						else if(arquivoLerDados($tmpDados, 'matriz', $a, 1, 1) == 'F') {
	//							#codigo do registro					001-001 - x(001)
	//							$codigoRegistro=arquivoLerDados($tmpDados,'matriz',$a, 1, 1);
	//							#Identif. do cliente na empresa		002-026 - x(025)
	//							$clienteEmpresa=arquivoLerDados($tmpDados,'matriz',$a, 2, 25);
	//							#Agencia de debito					027-030 - x(004)
	//							$agenciaDebito=arquivoLerDados($tmpDados,'matriz',$a, 27, 04);
	//							#Identif. do cliente no banco		031-044 - x(014)
	//							$clienteBanco=arquivoLerDados($tmpDados,'matriz',$a, 31, 14);
	//							#data vencimento/debito				045-052 - 9(008)
	//							# se o codigo de retorno for igual a zeros contera a data real do debito,
	//							# se nao, contera a data do vencimento
	//							$dataVencimento=arquivoLerDados($tmpDados,'matriz',$a, 45, 8);
	//							#Valor original/debitado			053-067 - 9(015)
	//							# se o codigo de retorno for 00, vira o VALOR DEBITADO
	//							# se nao, virá o VALOR ORIGINAL
	//							$valorDebitado=arquivoLerDados($tmpDados,'matriz',$a, 53, 15);
	//							#Codigo de Retorno					068-069 - x(002)
	//							$codigoRetorno=arquivoLerDados($tmpDados,'matriz',$a, 68, 2);
	//							#Uso da empresa						070-129 - x(060)
	//							$usoEmpresa=arquivoLerDados($tmpDados,'matriz',$a, 70,60);
	//							#Codigo Movimento					150-150 - x(001)
	//							$nomeBanco=arquivoLerDados($tmpDados,'matriz',$a, 150, 1);
	//							
	//							if ($a==1) {
	//								novaLinhaTabela($corFundo, '100%');
	//									htmlAbreColuna('100%', 'center', $corFundo, 2, 'tabfundo1');
	//									novaTabela("[Listagem de Documentos Retornardos]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 9);
	//									novaLinhaTabela($corFundo, '100%');
	//										itemLinhaTMNOURL('Documento', 'center', 'top', '10%', $corFundo, 0, 'tabfundo0');
	//										itemLinhaTMNOURL('Sacado', 'center', 'top', '30%', $corFundo, 0, 'tabfundo0');
	//										itemLinhaTMNOURL('Ocorrência', 'center', 'top', '15%', $corFundo, 0, 'tabfundo0');
	//										itemLinhaTMNOURL('Dt Ocor.', 'center nowrap', 'top', '5%', $corFundo, 0, 'tabfundo0');
	//										itemLinhaTMNOURL('Dt Cred', 'center nowrap', 'top', '5%', $corFundo, 0, 'tabfundo0');
	//										itemLinhaTMNOURL('Valor Tit.', 'center nowrap', 'top', '10%', $corFundo, 0, 'tabfundo0');
	//										itemLinhaTMNOURL('Juros', 'center', 'top', '5%', $corFundo, 0, 'tabfundo0');
	//										itemLinhaTMNOURL('Desc', 'center', 'top', '5%', $corFundo, 0, 'tabfundo0');
	//										itemLinhaTMNOURL('Recebido', 'center', 'top', '10%', $corFundo, 0, 'tabfundo0');
	//									fechaLinhaTabela();
	//							}
	//							
	//							$tituloEmpresa=intval($tituloEmpresa);
	//							# Buscar informação do documento
	//							$sqlDocumentos="
	//								SELECT 
	//									$tb[DocumentosGerados].id,
	//									$tb[DocumentosGerados].idFaturamento,
	//									$tb[DocumentosGerados].idPessoaTipo,
	//									$tb[ContasReceber].id idContasAReceber,
	//									$tb[ContasReceber].status status,
	//									$tb[Pessoas].nome nomePessoa
	//								FROM 
	//									$tb[Pessoas],
	//									$tb[PessoasTipos],
	//									$tb[DocumentosGerados],
	//									$tb[ContasReceber]
	//								WHERE
	//									$tb[DocumentosGerados].idPessoaTipo=$tb[PessoasTipos].id
	//									AND $tb[PessoasTipos].idPessoa=$tb[Pessoas].id
	//									AND $tb[DocumentosGerados].id=$tb[ContasReceber].idDocumentosGerados
	//									AND $tb[DocumentosGerados].id=$clienteEmpresa
	//							";
	//									
	//							$consulta=consultaSQL($sqlDocumentos, $conn);
	//								
	//							if($consulta && contaConsulta($consulta)>0) {
	//								
	//								$nomePessoa=resultadoSQL($consulta, 0, 'nomePessoa');
	//								$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
	//								$idContasAReceber=resultadoSQL($consulta, 0, 'idContasAReceber');
	//								$status=resultadoSQL($consulta, 0, 'status');
	//								
	//								# Se ValorPrincipal for recebido, creditar valor e baixar boleto
	//								
	//								# Verificar status do documento
	//								if($status) {
	//									# Mostrar dados do titulo
	//									novaLinhaTabela($corFundo, '100%');
	//										$DescTituloEmpresa=htmlMontaOpcao("<a href=?modulo=faturamento&sub=clientes&acao=dados_cobranca&registro=$idContasAReceber target=_BLANK>$clienteEmpresa</a></a>",'lancamento');
	//										itemLinhaTMNOURL($DescTituloEmpresa, 'center', 'top', '10%', $corFundo, 0, 'normal10');
	//										itemLinhaTMNOURL($nomePessoa, 'left', 'top', '33%', $corFundo, 0, 'normal8');
	//										itemLinhaTMNOURL($listaCodigoRetorno[$codigoRetorno], 'center', 'top', '15%', $corFundo, 0, 'normal8');
	//										itemLinhaTMNOURL(converteData($dataVencimento,'retorno','formdata'), 'center', 'top', '5%', $corFundo, 0, 'normal8');
	//										itemLinhaTMNOURL(converteData($dataVencimento,'retorno','formdata'), 'center', 'top', '5%', $corFundo, 0, 'normal8');
	//										itemLinhaTMNOURL(formatarValoresArquivoRetorno($valorDebitado), 'right', 'top', '10%', $corFundo, 0, 'normal10');
	//										itemLinhaTMNOURL("0,00", 'right', 'top', '5%', $corFundo, 0, 'normal10');
	//										itemLinhaTMNOURL("0,00", 'right', 'top', '5%', $corFundo, 0, 'normal10');
	//										itemLinhaTMNOURL(formatarValoresArquivoRetorno($valorDebitado), 'right', 'top', '10%', $corFundo, 0, 'normal10');
	//									fechaLinhaTabela();
	//									# Mostrar cabecalho
	//									
	//									# Incrementar valor total formatarValoresForm
	//									$valorTotal+=formatarValores($valorDebitado);
	//									$valorTotalRecebido+=formatarValores($valorDebitado);
	//									#$valorTotalJuros+=formatarValores($jurosMoraMulta);
	//									#$valorTotalDescontos+=formatarValores($valorDescontos);
	//									
	//									# Em caso de efetivação - validar dados para BD
	//									if($matriz[efetivar] && formatarValores($valorDebitado)>0) {
	//										# Importar dados para banco
	//										$matriz[id]=$idContasAReceber;
	//										$matriz[valorRecebido]=formatarValores($valorDebitado);
	//										#$matriz[valorJuros]=formatarValores($jurosMoraMulta);
	//										#RM $matriz[valorDesconto]=formatarValores($valorDescontos);
	//										$matriz[dtBaixa]=converteData($dataVencimento,'retorno','banco');
	//										$matriz[obs]="Arquivo Retorno: Ocorrencia: " .$listaCodigoRetorno[$codigoRetorno];
	//										
	//										dbContasReceber($matriz, 'baixar');
	//										
	//										$numRetornados++;
	//									}
	//								}
	//							}
	//						}
	//						# tipo H - Ocorrencia de alteração do controle da empresa
	//						else if(arquivoLerDados($tmpDados, 'matriz', $a, 1, 1) == 'H') {
	//							#codigo do registro					001-001 - x(001)
	//							$codigoRegistro=arquivoLerDados($tmpDados,'matriz',$a, 1, 1);
	//							#Identif. do cliente na empresa		002-026 - x(025)
	//							$clienteEmpresa=arquivoLerDados($tmpDados,'matriz',$a, 2, 25);
	//							#Agencia de debito					027-030 - x(004)
	//							$agenciaDebito=arquivoLerDados($tmpDados,'matriz',$a, 27, 04);
	//							#Identif. do cliente no banco		031-044 - x(014)
	//							$clienteBanco=arquivoLerDados($tmpDados,'matriz',$a, 31, 14);
	//							#Nova identificacao do cliente		045-069 - x(025)
	//							$novaIdentificacao=arquivoLerDados($tmpDados,'matriz',$a, 45, 25);
	//							#Ocorrencia							070-127 - x(058)
	//							$clienteBanco=arquivoLerDados($tmpDados,'matriz',$a, 70, 58);
	//							#Codigo Movimento					150-150 - x(001)
	//							$nomeBanco=arquivoLerDados($tmpDados,'matriz',$a, 150, 1);
	//						}
	//						# tipo X - Relação de agencias (so é enviado quando solicitado pela empresa)
	//						else if(arquivoLerDados($tmpDados, 'matriz', $a, 1, 1) == 'X') {
	//							#codigo do registro					001-001 - x(001)
	//							$codigoRegistro=arquivoLerDados($tmpDados,'matriz',$a, 1, 1);
	//							#Codigo da Agencia					002-005 - x(004)
	//							$codigoAgencia=arquivoLerDados($tmpDados,'matriz',$a, 2, 4);
	//							#Nome da Agencia					006-035 - x(030)
	//							$nomeAgencia=arquivoLerDados($tmpDados,'matriz',$a, 6, 30);
	//							#Endereco da Agencia				036-065 - x(030)
	//							$enderecoAgencia=arquivoLerDados($tmpDados,'matriz',$a, 36, 30);
	//							#Numero								066-070 - x(005)
	//							$numeroEnderecoAgencia=arquivoLerDados($tmpDados,'matriz',$a, 66, 5);
	//							#CEP								071-075 - x(005)
	//							$cepEnderecoAgencia=arquivoLerDados($tmpDados,'matriz',$a, 71, 5);
	//							#Sufixo CEP							076-078 - x(005)
	//							$sufCepEnderecoAgencia=arquivoLerDados($tmpDados,'matriz',$a, 76, 5);
	//							#Cidade								079-098 - x(020)
	//							$cidadeEnderecoAgencia=arquivoLerDados($tmpDados,'matriz',$a, 79, 98);
	//							#Sigla do estado					099-100 - x(002)
	//							$ufEnderecoAgencia=arquivoLerDados($tmpDados,'matriz',$a, 99, 2);
	//							#Situação da agencia				101-101 - x(001)
	//							# A - ativa
	//							# B - em regime de encerramento
	//							$situacaoAgencia=arquivoLerDados($tmpDados,'matriz',$a, 101,1);
	//							
	//						}
	//						# tipo Z - trailler de arquivo
	//						else if(arquivoLerDados($tmpDados, 'matriz', $a, 1, 1) == 'Z') {
	//							#codigo do registro					001-001 - x(001)
	//							$codigoRegistro=arquivoLerDados($tmpDados,'matriz',$a, 1, 1);
	//							#Quantidade de registros			002-007 - 9(006)
	//							$quantidadeRegistro=arquivoLerDados($tmpDados,'matriz',$a, 2, 6);
	//							#Valor Total						008-024 - 9(017)
	//							$valorTotalRetorno=arquivoLerDados($tmpDados,'matriz',$a, 8, 17);
	//						}
	//					} else {  // para protocolo 240
							if(arquivoLerDados($tmpDados, 'matriz', $a, 9, 1) == 'T') {
								# HEADER de retorno
								# Tipo de Servico 10-11=2
								$tipoServico=arquivoLerDados($tmpDados,'matriz',$a, 10, 2);
								# Agencia mantenedora da conta 54-58=5
								$agencia=arquivoLerDados($tmpDados,'matriz',$a, 54, 5);
								# Numero da conta corrente 60-71=12
								$contaNumero=arquivoLerDados($tmpDados,'matriz',$a, 60, 12);
								# Digito verificador da conta 72-72=1
								$contaDig=arquivoLerDados($tmpDados,'matriz',$a, 72, 1);
								# Nome da Empresa 74-103=30
								$nomeEmpresa=arquivoLerDados($tmpDados,'matriz',$a, 74, 30);
								# Codigo do banco na compensação 1-3=3 
								$codigoBanco=arquivoLerDados($tmpDados,'matriz',$a, 1, 3);
								# Nome do banco
								#$nomeBanco=arquivoLerDados($tmpDados,'matriz',$a, 80, 15);
								$nomeBanco="Banco do Brasil";
								# data de gravação remessa/retorno 192-199=8
								$dtGeracao=arquivoLerDados($tmpDados,'matriz',$a, 192, 8);
								#numero remessa/retorno 184-191=8
								$numeroSeqRetorno=arquivoLerDados($tmpDados,'matriz',$a, 184, 5);
								# data do credito 200-207=8
								$dtCredito=arquivoLerDados($tmpDados,'matriz',$a, 200, 8);
								#$numeroSeqArquivo=arquivoLerDados($tmpDados,'matriz',$a, 395, 6);
								$numeroSeqArquivo="000000";
								
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Código do Banco:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($banco[numero], 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Agência:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($agencia, 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Conta:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($contaNumero.'-'.$contaDig, 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Nome Empresa:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($nomeEmpresa, 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Data Geração:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									#itemLinhaForm(converteData($dtGeracao,'arquivo','formdata'), 'left', 'top', $corFundo, 0, 'tabfundo1');
									itemLinhaForm(converteData($dtGeracao,'arquivo','formdata'), 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Numero do Arquivo:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($numeroSeqRetorno, 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									htmlAbreColuna('100%', 'center', $corFundo, 2, 'tabfundo1');
									novaTabela("[Listagem de Documentos Retornardos]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 9);
									novaLinhaTabela($corFundo, '100%');
										itemLinhaTMNOURL('Documento', 'center', 'top', '10%', $corFundo, 0, 'tabfundo0');
										itemLinhaTMNOURL('Sacado', 'center', 'top', '30%', $corFundo, 0, 'tabfundo0');
										itemLinhaTMNOURL('Ocorrência', 'center', 'top', '15%', $corFundo, 0, 'tabfundo0');
										itemLinhaTMNOURL('Dt Ocor.', 'center nowrap', 'top', '5%', $corFundo, 0, 'tabfundo0');
										itemLinhaTMNOURL('Dt Cred', 'center nowrap', 'top', '5%', $corFundo, 0, 'tabfundo0');
										itemLinhaTMNOURL('Valor Tit.', 'center nowrap', 'top', '10%', $corFundo, 0, 'tabfundo0');
										itemLinhaTMNOURL('Juros', 'center', 'top', '5%', $corFundo, 0, 'tabfundo0');
										itemLinhaTMNOURL('Desc', 'center', 'top', '5%', $corFundo, 0, 'tabfundo0');
										itemLinhaTMNOURL('Recebido', 'center', 'top', '10%', $corFundo, 0, 'tabfundo0');
									fechaLinhaTabela();
									
							} //fim header
							elseif(arquivoLerDados($tmpDados, 'matriz', $a, 14, 1) == 'T') {
								#Segmento T
								$numProcessados++;
								# identificacao do titulo na empresa 106-130=25
								$tituloEmpresa=arquivoLerDados($tmpDados,'matriz',$a, 106, 25);
								# Tipo de Inscricao 133-133=1
								$codigoInscricao=arquivoLerDados($tmpDados,'matriz',$a, 133, 1);
								# Numero de Inscrição 134-148=15
								$numeroInscricao=arquivoLerDados($tmpDados,'matriz',$a, 134, 15);
								# Agencia Mantenedora da conta 18-22=5
								$agenciaMantenedora=arquivoLerDados($tmpDados,'matriz',$a, 18, 5);
								# Numero da conta corrente 24-35=12
								$contaNumero=arquivoLerDados($tmpDados,'matriz',$a, 24, 12);
								# Digito verificador da conta 36-36=1
								$contaDigitoconta=arquivoLerDados($tmpDados,'matriz',$a, 36, 1);
								# Nome 149-188=40
								#$tituloEmpresa=arquivoLerDados($tmpDados,'matriz',$a, 149, 40);
								# Codigo da Carteira 58-58=1
								$carteira=arquivoLerDados($tmpDados,'matriz',$a, 58, 1);
								# 
								#$codigoOcorrencia=arquivoLerDados($tmpDados,'matriz',$a, 109, 2);
								#
								#$dtOcorrencia=arquivoLerDados($tmpDados,'matriz',$a, 111, 6);
								# Numero do documento de cobranca 59-73=15
								$numeroDocumento=arquivoLerDados($tmpDados,'matriz',$a, 59, 15);
								# Numero do banco 97-99=3
								$nossoNumero=arquivoLerDados($tmpDados,'matriz',$a, 97, 3);
								# Data de vencimento do titulo 82-96=13
								$dtVencimento=arquivoLerDados($tmpDados,'matriz',$a, 82, 13);
								# Valor nominal do titulo 82-96=14
								$valorTitulo=arquivoLerDados($tmpDados,'matriz',$a, 82, 15);
								# Valor da tarifa/custas 199-213=13
								$tarifaCobranca=arquivoLerDados($tmpDados,'matriz',$a, 199, 13);
								#
		
								#----------------------
								# Verifica o segmento U
								#----------------------
								#incrementa pra pegar o segmento U
								$a++;
								if(arquivoLerDados($tmpDados, 'matriz', $a, 14, 1) == 'U') {
									#Valor do IOF 63-77=13
									$valorIOF=arquivoLerDados($tmpDados,'matriz',$a, 63, 13);
									# Valor do abat. concedido/re
									$valorAbatimento=arquivoLerDados($tmpDados,'matriz',$a, 48, 15);
									# Valor do desconto concedido  33-47=13
									$valorDescontos=arquivoLerDados($tmpDados,'matriz',$a, 33, 15);
									# Valor a ser creditado
									$valorPrincipal=arquivoLerDados($tmpDados,'matriz',$a, 93, 15);	
									# Juros multa encargos 18-32=13
									$jurosMoraMulta=arquivoLerDados($tmpDados,'matriz',$a, 18, 15);
									# Valor de outros creditos 123-137=13
									$outrosCreditos=arquivoLerDados($tmpDados,'matriz',$a, 123, 15);
									# Data da efetivação do credito 146-153=8
									$dtCredito=arquivoLerDados($tmpDados,'matriz',$a, 146, 8);
									#
									#$InstrCancelada=arquivoLerDados($tmpDados,'matriz',$a, 302, 4);
									$InstrCancelada=' ';
									# 
									#$nomeSacado=arquivoLerDados($tmpDados,'matriz',$a, 325, 30);
									$nomeSacado='Sacado';
									#
									#$erros=arquivoLerDados($tmpDados,'matriz',$a, 378, 8);
									$erros=' ';
									#
									#$codLiquidacao=arquivoLerDados($tmpDados,'matriz',$a, 393, 2);
									$codLiquidacao='0';
									# Numero sequencial do registro 9-13=5
									$numeroSeqArquivo=arquivoLerDados($tmpDados,'matriz',$a, 9, 5);
									# Data da ocorrencia 138-145=8
									$dtOcorrencia=arquivoLerDados($tmpDados, 'matriz', $a, 138, 8);
									# Codigo da ocorrencia 154-157=4
									$codigoOcorrencia=arquivoLerDados($tmpDados,'matriz',$a, 16, 2);
									
								} 
								else {
									$a--;
								}
								
								$tituloEmpresa=intval($tituloEmpresa);
								# Buscar informação do documento
								$sqlDocumentos="
									SELECT 
										$tb[DocumentosGerados].id,
										$tb[DocumentosGerados].idFaturamento,
										$tb[DocumentosGerados].idPessoaTipo,
										$tb[ContasReceber].id idContasAReceber,
										$tb[ContasReceber].status status,
										$tb[Pessoas].nome nomePessoa
									FROM 
										$tb[Pessoas],
										$tb[PessoasTipos],
										$tb[DocumentosGerados],
										$tb[ContasReceber]
									WHERE
										$tb[DocumentosGerados].idPessoaTipo=$tb[PessoasTipos].id
										AND $tb[PessoasTipos].idPessoa=$tb[Pessoas].id
										AND $tb[DocumentosGerados].id=$tb[ContasReceber].idDocumentosGerados
										AND $tb[DocumentosGerados].id=$tituloEmpresa";
										
								$consulta=consultaSQL($sqlDocumentos, $conn);
									
								if($consulta && contaConsulta($consulta)>0) {
									
									$nomePessoa=resultadoSQL($consulta, 0, 'nomePessoa');
									$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
									$idContasAReceber=resultadoSQL($consulta, 0, 'idContasAReceber');
									$status=resultadoSQL($consulta, 0, 'status');
									
									# Se ValorPrincipal for recebido, creditar valor e baixar boleto
									
									# Verificar status do documento
									if($status) {
										# Mostrar dados do titulo
										novaLinhaTabela($corFundo, '100%');
											$DescTituloEmpresa=htmlMontaOpcao("<a href=?modulo=faturamento&sub=clientes&acao=dados_cobranca&registro=$idContasAReceber target=_BLANK>$tituloEmpresa</a></a>",'lancamento');
											itemLinhaTMNOURL($DescTituloEmpresa, 'center', 'top', '10%', $corFundo, 0, 'normal10');
											itemLinhaTMNOURL($nomePessoa, 'left', 'top', '33%', $corFundo, 0, 'normal8');
											itemLinhaTMNOURL(layoutOcorrenciasItau($codigoOcorrencia), 'center', 'top', '15%', $corFundo, 0, 'normal8');
											itemLinhaTMNOURL(converteData($dtOcorrencia,'arquivo','formdata'), 'center', 'top', '5%', $corFundo, 0, 'normal8');
											itemLinhaTMNOURL(converteData($dtCredito,'arquivo','formdata'), 'center', 'top', '5%', $corFundo, 0, 'normal8');
											itemLinhaTMNOURL(formatarValoresArquivoRetorno($valorTitulo), 'right', 'top', '10%', $corFundo, 0, 'normal10');
											itemLinhaTMNOURL(formatarValoresArquivoRetorno($jurosMoraMulta), 'right', 'top', '5%', $corFundo, 0, 'normal10');
											itemLinhaTMNOURL(formatarValoresArquivoRetorno($valorDescontos), 'right', 'top', '5%', $corFundo, 0, 'normal10');
											itemLinhaTMNOURL(formatarValoresArquivoRetorno($valorPrincipal), 'right', 'top', '10%', $corFundo, 0, 'normal10');
										fechaLinhaTabela();
										# Mostrar cabecalho
										
										# Incrementar valor total
										$valorTotal+=formatarValores($valorTitulo);
										$valorTotalRecebido+=formatarValores($valorPrincipal);
										$valorTotalJuros+=formatarValores($jurosMoraMulta);
										$valorTotalDescontos+=formatarValores($valorDescontos);
										
										# Em caso de efetivação - validar dados para BD
										if($matriz[efetivar] && formatarValores($valorPrincipal)>0) {
											# Importar dados para banco
											$matriz[id]=$idContasAReceber;
											$matriz[valorRecebido]=formatarValores($valorPrincipal);
											$matriz[valorJuros]=formatarValores($jurosMoraMulta);
											$matriz[valorDesconto]=formatarValores($valorDescontos);
											$matriz[dtBaixa]=converteData($dtOcorrencia,'arquivo','banco');
											$matriz[obs]="Arquivo Retorno: Ocorrencia: " . layoutOcorrenciasItau($codigoOcorrencia);
											
											dbContasReceber($matriz, 'baixar');
											
											$numRetornados++;
										}
									}
								}
							}
	//					}
					}
					
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL("Processados: $numProcessados / Retornados: $numRetornados", 'center', 'top', '10%', $corFundo, 3, 'tabfundo1');
						itemLinhaTMNOURL('Totais:', 'right', 'top', '10%', $corFundo, 2, 'bold10');
						itemLinhaTMNOURL(formatarValoresForm($valorTotal), 'right', 'top', '10%', $corFundo, 0, 'txtok');
						itemLinhaTMNOURL(formatarValoresForm($valorTotalJuros), 'right', 'top', '5%', $corFundo, 0, 'txtok');
						itemLinhaTMNOURL(formatarValoresForm($valorTotalDescontos), 'right', 'top', '5%', $corFundo, 0, 'txtok');
						itemLinhaTMNOURL(formatarValoresForm($valorTotalRecebido), 'right', 'top', '5%', $corFundo, 0, 'txtok');
					fechaLinhaTabela();
					fechaTabela();
					htmlFechaColuna();
					fechaLinhaTabela();
					
					fechaTabela();
					
					# Efetivar arquivo
					if($matriz[efetivar]=='S') {
						$matriz[id]=$id;
						dbArquivoRetorno($matriz, 'ativar');
					}
				}
				#
				#------------------------------------------------------------------
				#
				elseif($banco[numero]=='237') {
					# Layout Bradesco
					# Mostrar cabecalho
					echo "<br>";
					novaTabela2("[Detalhe do Processamento]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
					itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
	
	//				# Quebrar conteúdo
	//				$tmpDados=explode("\n",$conteudo);
					
					# Zerar valor total
					$valorTotal=0;
					
					# Contadores
					$numRetornados;
					$numProcessados;
					
					# Layout do itau começa da 2a. linha
					
					$exibir = array();
					$exibirEstornados = array();
					$l = 0;
					
					for($a=0;$a<count($tmpDados);$a++) {
						# Verificar Tipo de registro
						$tipoRegistro=$tipoServico=arquivoLerDados($tmpDados,'matriz', $a, 1, 1);
						
						if($tipoRegistro == '0') {
							# Header
							# Identificação de tipo de arquivo
							$tipoArquivo=arquivoLerDados($tmpDados, 'matriz', $a, 2, 1);
							
							if($tipoArquivo == '2') {
								# Arquivo é retorno
								$tipoServico=arquivoLerDados($tmpDados,'matriz',$a, 10, 2);
								$agencia=arquivoLerDados($tmpDados,'matriz',$a, 27, 4);
								$contaNumero=arquivoLerDados($tmpDados,'matriz',$a, 33, 5);
								$contaDig=arquivoLerDados($tmpDados,'matriz',$a, 38, 1);
								$nomeEmpresa=arquivoLerDados($tmpDados,'matriz',$a, 47, 30);
								$codigoBanco=arquivoLerDados($tmpDados,'matriz',$a, 77, 3);
								$nomeBanco=arquivoLerDados($tmpDados,'matriz',$a, 80, 15);
								$dtGeracao=arquivoLerDados($tmpDados,'matriz',$a, 95, 6);
								$numeroSeqRetorno=arquivoLerDados($tmpDados,'matriz',$a, 109, 5);
								$dtCredito=arquivoLerDados($tmpDados,'matriz',$a, 114, 6);
								$numeroSeqArquivo=arquivoLerDados($tmpDados,'matriz',$a, 395, 6);
								
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Código do Banco:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($banco[numero], 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Agência:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($agencia, 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Conta:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($contaNumero.'-'.$contaDig, 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Nome Empresa:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($nomeEmpresa, 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Data Geração:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm(converteData($dtGeracao,'arquivo','formdata'), 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Data de Crédito:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm(converteData($dtCredito,'arquivo','formdata'), 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Numero do Arquivo:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($numeroSeqRetorno, 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								fechaTabela();
									
							}
						}
						elseif($tipoRegistro=='1') {
						
							$numProcessados++;
							
							if($tipoServico) {				
								# Transação
								$codigoInscricao=arquivoLerDados($tmpDados,'matriz',$a, 2, 2);
								$numeroInscricao=arquivoLerDados($tmpDados,'matriz',$a, 4, 14);
	//							$agenciaMantenedora=arquivoLerDados($tmpDados,'matriz',$a, 18, 4);
	//							#
	//							$contaNumero=arquivoLerDados($tmpDados,'matriz',$a, 30, 6);
	//							$contaDigitoconta=arquivoLerDados($tmpDados,'matriz',$a, 36, 1);
	//							#
	//							$tituloEmpresa=arquivoLerDados($tmpDados,'matriz',$a, 38, 25);
								$carteira=arquivoLerDados($tmpDados,'matriz',$a, 108, 1);
								$codigoOcorrencia=arquivoLerDados($tmpDados,'matriz',$a, 109, 2);
								$dtOcorrencia=arquivoLerDados($tmpDados,'matriz',$a, 111, 6);
								$numeroDocumento=arquivoLerDados($tmpDados,'matriz',$a, 117, 10);
								$nossoNumero=arquivoLerDados($tmpDados,'matriz',$a, 127, 20);
								$dtVencimento=arquivoLerDados($tmpDados,'matriz',$a, 147, 6);
								$valorTitulo=arquivoLerDados($tmpDados,'matriz',$a, 153, 13);
								$tarifaCobranca=arquivoLerDados($tmpDados,'matriz',$a, 176, 13);
								$valorIOF=arquivoLerDados($tmpDados,'matriz',$a, 215, 13);
								$valorAbatimento=arquivoLerDados($tmpDados,'matriz',$a, 228, 13);
								$valorDescontos=arquivoLerDados($tmpDados,'matriz',$a, 241, 13);
								$valorPrincipal=arquivoLerDados($tmpDados,'matriz',$a, 254, 13);
								$jurosMoraMulta=arquivoLerDados($tmpDados,'matriz',$a, 267, 13);
								$outrosCreditos=arquivoLerDados($tmpDados,'matriz',$a, 280, 13);
								$dtCredito=arquivoLerDados($tmpDados,'matriz',$a, 296, 6);
								$InstrCancelada=arquivoLerDados($tmpDados,'matriz',$a, 319, 10);
								//$nomeSacado=arquivoLerDados($tmpDados,'matriz',$a, 325, 30);
								$erros=arquivoLerDados($tmpDados,'matriz',$a, 319, 10);
								//$codLiquidacao=arquivoLerDados($tmpDados,'matriz',$a, 393, 2);
														
								$numeroSeqArquivo=arquivoLerDados($tmpDados,'matriz',$a, 395, 6);
								
								if($numeroDocumento && strlen(trim($numeroDocumento)>0) && is_numeric(trim($numeroDocumento))) {
									
									$numeroDocumento=intval($numeroDocumento);
									
									# Buscar informação do documento
									$sqlDocumentos="
										SELECT 
											$tb[DocumentosGerados].id,
											$tb[DocumentosGerados].idFaturamento,
											$tb[DocumentosGerados].idPessoaTipo,
											$tb[ContasReceber].id idContasAReceber,
											$tb[ContasReceber].status status,
											$tb[Pessoas].nome nomePessoa
										FROM 
											$tb[Pessoas],
											$tb[PessoasTipos],
											$tb[DocumentosGerados],
											$tb[ContasReceber]
										WHERE
											$tb[DocumentosGerados].idPessoaTipo=$tb[PessoasTipos].id
											AND $tb[PessoasTipos].idPessoa=$tb[Pessoas].id
											AND $tb[DocumentosGerados].id=$tb[ContasReceber].idDocumentosGerados
											AND $tb[DocumentosGerados].id=$numeroDocumento";
											
									$consulta=consultaSQL($sqlDocumentos, $conn);
									
									if($consulta && contaConsulta($consulta)>0) {
									
										$nomePessoa=resultadoSQL($consulta, 0, 'nomePessoa');
										$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
										$idContasAReceber=resultadoSQL($consulta, 0, 'idContasAReceber');
										$status=resultadoSQL($consulta, 0, 'status');
										
										# Se ValorPrincipal for recebido, creditar valor e baixar boleto
										
										# Verificar status do documento
										if($status) {
											# Mostrar dados do titulo
											//estorno
											if ($status == 'B' && $codigoOcorrencia == '02'){
																								$c=0;
												$exibirEstornados[$l][$c++] = htmlMontaOpcao("<a href=?modulo=faturamento&sub=clientes&acao=dados_cobranca&registro=$idContasAReceber target=_BLANK>$idContasAReceber</a></a>",'lancamento');
												$exibirEstornados[$l][$c++] = $nomePessoa;
												$exibirEstornados[$l][$c++] = "<div class=txtaviso8>Entrada Extornada: Boleto Pendente, não recebido.</div>";
												$exibirEstornados[$l][$c++] = converteData($dtOcorrencia,'arquivo','formdata');
												$exibirEstornados[$l++][$c++] = formatarValoresArquivoRetorno($valorTitulo);
												
												$valor=formatarValores($valorTitulo);
												
												if($matriz[efetivar]) {
	
													# Importar dados para banco
													$matriz[id]=$idContasAReceber;
													$matriz[valor] = $valor;
													$matriz[valorRecebido]=0;
													$matriz[valorJuros]=0;
													$matriz[valorDesconto]=0;
													$matriz[dtBaixa]='';
													$matriz[status] = 'P';
													$matriz[obs]="Entrada Extornada: Boleto Pendente, não recebido.";
													
													dbContasReceber($matriz, 'estornar');
													
													$numRetornados++;
												}
											}
											else{
											
												$c=0;
												$exibir[$l][$c++] = htmlMontaOpcao("<a href=?modulo=faturamento&sub=clientes&acao=dados_cobranca&registro=$idContasAReceber target=_BLANK>$idContasAReceber</a></a>",'lancamento');
												$exibir[$l][$c++] = $nomePessoa;
												$exibir[$l][$c++] = getLabelOcorrenciaBradesco($codigoOcorrencia, $erros);
												$exibir[$l][$c++] = converteData($dtOcorrencia,'arquivo','formdata');
												$exibir[$l][$c++] = converteData($dtCredito,'arquivo','formdata');
												$exibir[$l][$c++] = formatarValoresArquivoRetorno($valorTitulo);
												$exibir[$l][$c++] = formatarValoresArquivoRetorno($jurosMoraMulta);
												$exibir[$l][$c++] = formatarValoresArquivoRetorno($valorDescontos);
												$exibir[$l++][$c++] = formatarValoresArquivoRetorno($valorPrincipal);
										
	
												##conta baixada e transferida para desconto.
												if ( $codigoOcorrencia ==  '10' && substr_count($erros, '20')){
													$valorPrincipal = $valorTitulo;
													$obs = "ATENCAO, titulo transferido para desconto.";
												}
												##
												
												# Incrementar valor total
												$valorTotal+=formatarValores($valorTitulo);
												$valorTotalRecebido+=formatarValores($valorPrincipal);
												$valorTotalJuros+=formatarValores($jurosMoraMulta);
												$valorTotalDescontos+=formatarValores($valorDescontos);
												
												# Em caso de efetivação - validar dados para BD
												if($matriz[efetivar] && formatarValores($valorPrincipal)>0) {
	
													# Importar dados para banco
													$matriz[id]=$idContasAReceber;
													$matriz[valorRecebido]=formatarValores($valorPrincipal);
													$matriz[valorJuros]=formatarValores($jurosMoraMulta);
													$matriz[valorDesconto]=formatarValores($valorDescontos);
													$matriz[dtBaixa]=converteData($dtOcorrencia,'arquivo','banco');
													$matriz[obs]="Arquivo Retorno: Ocorrencia: " . layoutOcorrenciasBradesco($codigoOcorrencia, $erros);
													
													dbContasReceber($matriz, 'baixar');
													
													$numRetornados++;
												}
											} //registro normal
										}//com status
									}
									
									# Retornar para banco de dados
								}
	
							}
							elseif($tipoRegistro=='9') {
								# Trailler de arquivo
							}
						}	
					} // fim do loop de registros.
					
					#exibicao na tela
					
					if (count($exibirEstornados)){
						echo "<br>";
						novaTabela("[Atenção: Boletos transferidos de carteira e estornadas pelo sistema]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 9);
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL('Documento', 'center', 'top', '10%', $corFundo, 0, 'tabfundo0');
							itemLinhaTMNOURL('Sacado', 'center', 'top', '25%', $corFundo, 0, 'tabfundo0');
							itemLinhaTMNOURL('Ocorrência', 'center', 'top', '25%', $corFundo, 0, 'tabfundo0');
							itemLinhaTMNOURL('Dt Ocor.', 'center nowrap', 'top', '5%', $corFundo, 0, 'tabfundo0');
							itemLinhaTMNOURL('Valor', 'center nowrap', 'top', '5%', $corFundo, 0, 'tabfundo0');
						fechaLinhaTabela();
						foreach ($exibirEstornados as $linha){
							$c = 0;
							novaLinhaTabela($corFundo, '100%');
								itemLinhaTMNOURL($linha[$c++], 'center', 'top', '10%', $corFundo, 0, 'normal10');
								itemLinhaTMNOURL($linha[$c++], 'left', 'top', '33%', $corFundo, 0, 'normal8');
								itemLinhaTMNOURL($linha[$c++], 'center', 'top', '15%', $corFundo, 0, 'normal8');
								itemLinhaTMNOURL($linha[$c++], 'center', 'top', '5%', $corFundo, 0, 'normal8');
								itemLinhaTMNOURL($linha[$c++], 'right', 'top', '10%', $corFundo, 0, 'normal10');
							fechaLinhaTabela();
							# Mostrar cabecalho	
						}
						fechaTabela();
					}
					echo "<br>";
					

					novaTabela("[Listagem de Documentos Retornardos]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 9);
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('Documento', 'center', 'top', '10%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Sacado', 'center', 'top', '25%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Ocorrência', 'center', 'top', '25%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Dt Ocor.', 'center nowrap', 'top', '5%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Dt Cred', 'center nowrap', 'top', '5%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Valor', 'center nowrap', 'top', '5%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Juros', 'center', 'top', '5%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Desc', 'center', 'top', '5%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Recebido', 'center', 'top', '10%', $corFundo, 0, 'tabfundo0');
					fechaLinhaTabela();
					
					foreach ($exibir as $linha){
						$c = 0;
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL($linha[$c++], 'center', 'top', '10%', $corFundo, 0, 'normal10');
							itemLinhaTMNOURL($linha[$c++], 'left', 'top', '33%', $corFundo, 0, 'normal8');
							itemLinhaTMNOURL($linha[$c++], 'center', 'top', '15%', $corFundo, 0, 'normal8');
							itemLinhaTMNOURL($linha[$c++], 'center', 'top', '5%', $corFundo, 0, 'normal8');
							itemLinhaTMNOURL($linha[$c++], 'center', 'top', '5%', $corFundo, 0, 'normal8');
							itemLinhaTMNOURL($linha[$c++], 'right', 'top', '10%', $corFundo, 0, 'normal10');
							itemLinhaTMNOURL($linha[$c++], 'right', 'top', '5%', $corFundo, 0, 'normal10');
							itemLinhaTMNOURL($linha[$c++], 'right', 'top', '5%', $corFundo, 0, 'normal10');
							itemLinhaTMNOURL($linha[$c++], 'right', 'top', '10%', $corFundo, 0, 'normal10');
						fechaLinhaTabela();
						# Mostrar cabecalho	
					}
					
					
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL("Processados: $numProcessados / Retornados: $numRetornados", 'center', 'top', '10%', $corFundo, 3, 'tabfundo0');
						itemLinhaTMNOURL('Totais:', 'right', 'top', '10%', $corFundo, 2, 'bold10');
						itemLinhaTMNOURL(formatarValoresForm($valorTotal), 'right', 'top', '10%', $corFundo, 0, 'txtok');
						itemLinhaTMNOURL(formatarValoresForm($valorTotalJuros), 'right', 'top', '5%', $corFundo, 0, 'txtok');
						itemLinhaTMNOURL(formatarValoresForm($valorTotalDescontos), 'right', 'top', '5%', $corFundo, 0, 'txtok');
						itemLinhaTMNOURL(formatarValoresForm($valorTotalRecebido), 'right', 'top', '5%', $corFundo, 0, 'txtok');
					fechaLinhaTabela();
					fechaTabela();
					htmlFechaColuna();
					fechaLinhaTabela();
					
//					fechaTabela();
					
					# Efetivar arquivo
					if($matriz[efetivar]=='S') {
						$matriz[id]=$id;
						dbArquivoRetorno($matriz, 'ativar');
					}
				}
				#
				#-------------------------------------------------------------------------------
				#
				elseif($banco[numero]=='033') {
					# Layout Banespa
					# Mostrar cabecalho
					echo "<br>";
					novaTabela2("[Detalhe do Processamento]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
					itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
	
	//				# Quebrar conteúdo
	//				$tmpDados=explode("\n",$conteudo);
					
					# Zerar valor total
					$valorTotal=0;
					
					# Contadores
					$numRetornados;
					$numProcessados;
			
					if (strtoupper(arquivoLerDados($tmpDados,'matriz', 0, 82, 17)) != "DEBITO AUTOMATICO"){
						// boleto bancario.
						# Layout do itau começa da 2a. linha
						for($a=0;$a<count($tmpDados);$a++) {
							# Verificar Tipo de registro
							$tipoRegistro=$tipoServico=arquivoLerDados($tmpDados,'matriz', $a, 1, 1);
							
							if($tipoRegistro == '0') {
								# Header
								# Identificação de tipo de arquivo
								$tipoArquivo=arquivoLerDados($tmpDados, 'matriz', $a, 2, 1);
								
								if($tipoArquivo == '2') {
									# Arquivo é retorno
									$tipoServico=arquivoLerDados($tmpDados,'matriz',$a, 10, 2);
									$agencia=arquivoLerDados($tmpDados,'matriz',$a, 27, 4);
									//$contaNumero=arquivoLerDados($tmpDados,'matriz',$a, 33, 5);
									//$contaDig=arquivoLerDados($tmpDados,'matriz',$a, 38, 1);
									$nomeEmpresa=arquivoLerDados($tmpDados,'matriz',$a, 47, 30);
									$codigoBanco=arquivoLerDados($tmpDados,'matriz',$a, 77, 3);
									$nomeBanco=arquivoLerDados($tmpDados,'matriz',$a, 80, 7);
									$dtGeracao=arquivoLerDados($tmpDados,'matriz',$a, 95, 6);
									#$numeroSeqRetorno=arquivoLerDados($tmpDados,'matriz',$a, 109, 5);
									$dtCredito=arquivoLerDados($tmpDados,'matriz',$a, 380, 6);
									$numeroSeqArquivo=arquivoLerDados($tmpDados,'matriz',$a, 395, 6);
									
									novaLinhaTabela($corFundo, '100%');
										itemLinhaTMNOURL('<b>Código do Banco:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
										itemLinhaForm($banco[numero], 'left', 'top', $corFundo, 0, 'tabfundo1');
									fechaLinhaTabela();
									novaLinhaTabela($corFundo, '100%');
										itemLinhaTMNOURL('<b>Agência:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
										itemLinhaForm($agencia, 'left', 'top', $corFundo, 0, 'tabfundo1');
									fechaLinhaTabela();
									novaLinhaTabela($corFundo, '100%');
										itemLinhaTMNOURL('<b>Conta:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
										itemLinhaForm($contaNumero.'-'.$contaDig, 'left', 'top', $corFundo, 0, 'tabfundo1');
									fechaLinhaTabela();
									novaLinhaTabela($corFundo, '100%');
										itemLinhaTMNOURL('<b>Nome Empresa:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
										itemLinhaForm($nomeEmpresa, 'left', 'top', $corFundo, 0, 'tabfundo1');
									fechaLinhaTabela();
									novaLinhaTabela($corFundo, '100%');
										itemLinhaTMNOURL('<b>Data Geração:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
										itemLinhaForm(converteData($dtGeracao,'arquivo','formdata'), 'left', 'top', $corFundo, 0, 'tabfundo1');
									fechaLinhaTabela();
									novaLinhaTabela($corFundo, '100%');
										itemLinhaTMNOURL('<b>Data de Crédito:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
										itemLinhaForm(converteData($dtCredito,'arquivo','formdata'), 'left', 'top', $corFundo, 0, 'tabfundo1');
									fechaLinhaTabela();
									novaLinhaTabela($corFundo, '100%');
										itemLinhaTMNOURL('<b>Numero do Arquivo:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
										itemLinhaForm($numeroSeqRetorno, 'left', 'top', $corFundo, 0, 'tabfundo1');
									fechaLinhaTabela();
									novaLinhaTabela($corFundo, '100%');
										htmlAbreColuna('100%', 'center', $corFundo, 2, 'tabfundo1');
										novaTabela("[Listagem de Documentos Retornardos]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 9);
											novaLinhaTabela($corFundo, '100%');
												itemLinhaTMNOURL('Documento', 'center', 'top', '10%', $corFundo, 0, 'tabfundo0');
												itemLinhaTMNOURL('Sacado', 'center', 'top', '30%', $corFundo, 0, 'tabfundo0');
												itemLinhaTMNOURL('Ocorrência', 'center', 'top', '15%', $corFundo, 0, 'tabfundo0');
												itemLinhaTMNOURL('Dt Ocor.', 'center nowrap', 'top', '5%', $corFundo, 0, 'tabfundo0');
												itemLinhaTMNOURL('Dt Cred', 'center nowrap', 'top', '5%', $corFundo, 0, 'tabfundo0');
												itemLinhaTMNOURL('Valor Tit.', 'center nowrap', 'top', '10%', $corFundo, 0, 'tabfundo0');
												itemLinhaTMNOURL('Juros', 'center', 'top', '5%', $corFundo, 0, 'tabfundo0');
												itemLinhaTMNOURL('Desc', 'center', 'top', '5%', $corFundo, 0, 'tabfundo0');
												itemLinhaTMNOURL('Recebido', 'center', 'top', '10%', $corFundo, 0, 'tabfundo0');
											fechaLinhaTabela();
										
								}
							}
							elseif($tipoRegistro=='1') {
							
								$numProcessados++;
								
								if($tipoServico) {
									# Transação
									$codigoInscricao=arquivoLerDados($tmpDados,'matriz',$a, 2, 2);
									$numeroInscricao=arquivoLerDados($tmpDados,'matriz',$a, 4, 14);
									$agenciaMantenedora=arquivoLerDados($tmpDados,'matriz',$a, 169, 5);
									//$contaNumero=arquivoLerDados($tmpDados,'matriz',$a, 24, 5);
									//$contaDigitoconta=arquivoLerDados($tmpDados,'matriz',$a, 29, 1);
									$tituloEmpresa=arquivoLerDados($tmpDados,'matriz',$a, 38, 25);
									$carteira=arquivoLerDados($tmpDados,'matriz',$a, 108, 1);
									$codigoOcorrencia=arquivoLerDados($tmpDados,'matriz',$a, 109, 2);
									$dtOcorrencia=arquivoLerDados($tmpDados,'matriz',$a, 111, 6);
									$numeroDocumento=arquivoLerDados($tmpDados,'matriz',$a, 117, 10);
									$nossoNumero=arquivoLerDados($tmpDados,'matriz',$a, 127, 10);
									$dtVencimento=arquivoLerDados($tmpDados,'matriz',$a, 147, 6);
									$valorTitulo=arquivoLerDados($tmpDados,'matriz',$a, 153, 13);
									$tarifaCobranca=arquivoLerDados($tmpDados,'matriz',$a, 176, 13);
									$valorIOF=arquivoLerDados($tmpDados,'matriz',$a, 215, 13);
									$valorAbatimento=arquivoLerDados($tmpDados,'matriz',$a, 228, 13);
									$valorDescontos=arquivoLerDados($tmpDados,'matriz',$a, 241, 13);
									$valorPrincipal=arquivoLerDados($tmpDados,'matriz',$a, 254, 13);
									$jurosMoraMulta=arquivoLerDados($tmpDados,'matriz',$a, 267, 13);
									$outrosCreditos=arquivoLerDados($tmpDados,'matriz',$a, 280, 13);
									$dtCredito=arquivoLerDados($tmpDados,'matriz',$a, 296, 6);
									$InstrCancelada=arquivoLerDados($tmpDados,'matriz',$a, 302, 2);
									//$nomeSacado=arquivoLerDados($tmpDados,'matriz',$a, 325, 30);
									//$erros=arquivoLerDados($tmpDados,'matriz',$a, 378, 8);
									//$codLiquidacao=arquivoLerDados($tmpDados,'matriz',$a, 393, 2);
									$numeroSeqArquivo=arquivoLerDados($tmpDados,'matriz',$a, 395, 6);
									
									if($tituloEmpresa && strlen(trim($tituloEmpresa)>0) && is_numeric(trim($tituloEmpresa))) {
									
										$tituloEmpresa=intval($tituloEmpresa);
										
										# Buscar informação do documento
										$sqlDocumentos="
											SELECT 
												$tb[DocumentosGerados].id,
												$tb[DocumentosGerados].idFaturamento,
												$tb[DocumentosGerados].idPessoaTipo,
												$tb[ContasReceber].id idContasAReceber,
												$tb[ContasReceber].status status,
												$tb[Pessoas].nome nomePessoa
											FROM 
												$tb[Pessoas],
												$tb[PessoasTipos],
												$tb[DocumentosGerados],
												$tb[ContasReceber]
											WHERE
												$tb[DocumentosGerados].idPessoaTipo=$tb[PessoasTipos].id
												AND $tb[PessoasTipos].idPessoa=$tb[Pessoas].id
												AND $tb[DocumentosGerados].id=$tb[ContasReceber].idDocumentosGerados
												AND $tb[DocumentosGerados].id=$tituloEmpresa";
												
										$consulta=consultaSQL($sqlDocumentos, $conn);
										
										if($consulta && contaConsulta($consulta)>0) {
											
											$nomePessoa=resultadoSQL($consulta, 0, 'nomePessoa');
											$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
											$idContasAReceber=resultadoSQL($consulta, 0, 'idContasAReceber');
											$status=resultadoSQL($consulta, 0, 'status');
											
											# Se ValorPrincipal for recebido, creditar valor e baixar boleto
											
											# Verificar status do documento
											if($status) {
												# Mostrar dados do titulo
												novaLinhaTabela($corFundo, '100%');
													$DescTituloEmpresa=htmlMontaOpcao("<a href=?modulo=faturamento&sub=clientes&acao=dados_cobranca&registro=$idContasAReceber target=_BLANK>$tituloEmpresa</a></a>",'lancamento');
													itemLinhaTMNOURL($DescTituloEmpresa, 'center', 'top', '10%', $corFundo, 0, 'normal10');
													itemLinhaTMNOURL($nomePessoa, 'left', 'top', '33%', $corFundo, 0, 'normal8');
													itemLinhaTMNOURL(layoutOcorrenciasBanespa($codigoOcorrencia), 'center', 'top', '15%', $corFundo, 0, 'normal8');
													itemLinhaTMNOURL(converteData($dtOcorrencia,'arquivo','formdata'), 'center', 'top', '5%', $corFundo, 0, 'normal8');
													itemLinhaTMNOURL(converteData($dtCredito,'arquivo','formdata'), 'center', 'top', '5%', $corFundo, 0, 'normal8');
													itemLinhaTMNOURL(formatarValoresArquivoRetorno($valorTitulo), 'right', 'top', '10%', $corFundo, 0, 'normal10');
													itemLinhaTMNOURL(formatarValoresArquivoRetorno($jurosMoraMulta), 'right', 'top', '5%', $corFundo, 0, 'normal10');
													itemLinhaTMNOURL(formatarValoresArquivoRetorno($valorDescontos), 'right', 'top', '5%', $corFundo, 0, 'normal10');
													itemLinhaTMNOURL(formatarValoresArquivoRetorno($valorPrincipal), 'right', 'top', '10%', $corFundo, 0, 'normal10');
												fechaLinhaTabela();
												# Mostrar cabecalho
												
												# Incrementar valor total
												$valorTotal+=formatarValores($valorTitulo);
												$valorTotalRecebido+=formatarValores($valorPrincipal);
												$valorTotalJuros+=formatarValores($jurosMoraMulta);
												$valorTotalDescontos+=formatarValores($valorDescontos);
												
												# Em caso de efetivação - validar dados para BD
												if($matriz[efetivar] && formatarValores($valorPrincipal)>0) {
													# Importar dados para banco
													$matriz[id]=$idContasAReceber;
													$matriz[valorRecebido]=formatarValores($valorPrincipal);
													$matriz[valorJuros]=formatarValores($jurosMoraMulta);
													$matriz[valorDesconto]=formatarValores($valorDescontos);
													$matriz[dtBaixa]=converteData($dtOcorrencia,'arquivo','banco');
													$matriz[obs]="Arquivo Retorno: Ocorrencia: " . layoutOcorrenciasBanespa($codigoOcorrencia);
													
													dbContasReceber($matriz, 'baixar');
													
													$numRetornados++;
												}
											}
										}
										
										# Retornar para banco de dados
									}
		
								}
								elseif($tipoRegistro=='9') {
									# Trailler de arquivo
								}
							}
							else {
								//fechaTabela();
								//htmlFechaColuna();
								//htmlFechaLinha();
								
							}
								
						}
					}	
					else{	
						//debito automatico
						for($a=0;$a<count($tmpDados);$a++) {
							# Verificar Tipo de registro
							$tipoRegistro=$tipoServico=arquivoLerDados($tmpDados,'matriz', $a, 1, 1);
							
							if($tipoRegistro == 'A') {
								# Header
								# Identificação de tipo de arquivo
								$tipoArquivo=arquivoLerDados($tmpDados, 'matriz', $a, 2, 1);
								
								if($tipoArquivo == '2') {
									# Arquivo é retorno
									
									$nomeEmpresa=arquivoLerDados($tmpDados,'matriz',$a, 23, 20);
									$tipoServico=arquivoLerDados($tmpDados,'matriz',$a, 10, 2);
									$agencia=arquivoLerDados($tmpDados,'matriz',$a, 27, 4);
									
									$codigoBanco=arquivoLerDados($tmpDados,'matriz',$a, 43, 3);
									$nomeBanco=arquivoLerDados($tmpDados,'matriz',$a, 46, 20);
									$dtGeracao=arquivoLerDados($tmpDados,'matriz',$a, 66, 8);
									$numeroSeqArquivo=arquivoLerDados($tmpDados,'matriz',$a, 74, 9);
									
									novaLinhaTabela($corFundo, '100%');
										itemLinhaTMNOURL('<b>Código do Banco:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
										itemLinhaForm($banco[numero], 'left', 'top', $corFundo, 0, 'tabfundo1');
									fechaLinhaTabela();
									novaLinhaTabela($corFundo, '100%');
										itemLinhaTMNOURL('<b>Nome Empresa:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
										itemLinhaForm($nomeEmpresa, 'left', 'top', $corFundo, 0, 'tabfundo1');
									fechaLinhaTabela();
									novaLinhaTabela($corFundo, '100%');
										itemLinhaTMNOURL('<b>Data Geração:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
										itemLinhaForm(converteData($dtGeracao,'arquivo','formdata'), 'left', 'top', $corFundo, 0, 'tabfundo1');
									fechaLinhaTabela();
									novaLinhaTabela($corFundo, '100%');
										itemLinhaTMNOURL('<b>Numero do Arquivo:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
										itemLinhaForm($numeroSeqRetorno, 'left', 'top', $corFundo, 0, 'tabfundo1');
									fechaLinhaTabela();
									novaLinhaTabela($corFundo, '100%');
										htmlAbreColuna('100%', 'center', $corFundo, 2, 'tabfundo1');
										novaTabela("[Listagem de Documentos Retornardos]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 9);
											novaLinhaTabela($corFundo, '100%');
												itemLinhaTMNOURL('Documento', 'center', 'top', '10%', $corFundo, 0, 'tabfundo0');
												itemLinhaTMNOURL('Sacado', 'center', 'top', '30%', $corFundo, 0, 'tabfundo0');
												itemLinhaTMNOURL('Pessoa', 'center', 'top', '10%', $corFundo, 0, 'tabfundo0');
												itemLinhaTMNOURL('Ocorrência', 'center', 'top', '15%', $corFundo, 0, 'tabfundo0');
												itemLinhaTMNOURL('Dt Cred', 'center nowrap', 'top', '5%', $corFundo, 0, 'tabfundo0');
												itemLinhaTMNOURL('Valor Tit.', 'center nowrap', 'top', '10%', $corFundo, 0, 'tabfundo0');
												itemLinhaTMNOURL('Recebido', 'center', 'top', '10%', $corFundo, 0, 'tabfundo0');
											fechaLinhaTabela();
										
								}
							}
							elseif($tipoRegistro=='F') {
							
								$numProcessados++;
								
								if($tipoServico) {
									# Transação
									$numeroDocumento = arquivoLerDados($tmpDados,'matriz',$a, 2, 26);
									$agenciaCliente = arquivoLerDados($tmpDados,'matriz',$a, 27, 4);
									$contaCliente = arquivoLerDados($tmpDados,'matriz',$a, 31, 14);
									$dtVencimento = arquivoLerDados($tmpDados,'matriz',$a, 45, 8);
									$valorRecebido = arquivoLerDados($tmpDados,'matriz',$a, 53, 15);
									$codigoOcorrencia = arquivoLerDados($tmpDados,'matriz',$a, 68, 2);
									$codigoMovimento = arquivoLerDados($tmpDados,'matriz',$a, 150, 1);
									
									if($numeroDocumento && strlen(trim($numeroDocumento)>0) ) {
										
										$numeroDocumento = intval($numeroDocumento);
										
										# Buscar informação do documento
										$sqlDocumentos="
											SELECT 
												$tb[DocumentosGerados].id,
												$tb[DocumentosGerados].idFaturamento,
												$tb[DocumentosGerados].idPessoaTipo,
												$tb[ContasReceber].id idContasAReceber,
												$tb[ContasReceber].status status,
												$tb[ContasReceber].valor valor,
												$tb[Pessoas].nome nomePessoa
											FROM 
												$tb[Pessoas],
												$tb[PessoasTipos],
												$tb[DocumentosGerados],
												$tb[ContasReceber]
											WHERE
												$tb[DocumentosGerados].idPessoaTipo=$tb[PessoasTipos].id
												AND $tb[PessoasTipos].idPessoa=$tb[Pessoas].id
												AND $tb[DocumentosGerados].id=$tb[ContasReceber].idDocumentosGerados
												AND $tb[DocumentosGerados].id=$numeroDocumento";
	
											
										$consulta=consultaSQL($sqlDocumentos, $conn);
										
										if($consulta && contaConsulta($consulta)>0) {
											
											$nomePessoa=resultadoSQL($consulta, 0, 'nomePessoa');
											$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
											$idContasAReceber=resultadoSQL($consulta, 0, 'idContasAReceber');
											$valor=resultadoSQL($consulta, 0, 'valor');
											$status=resultadoSQL($consulta, 0, 'status');
						
											
											# Se ValorPrincipal for recebido, creditar valor e baixar boleto
											
											# Verificar status do documento
											if($status) {
												# Mostrar dados do titulo
												novaLinhaTabela($corFundo, '100%');
													$DescTituloEmpresa=htmlMontaOpcao("<a href=?modulo=faturamento&sub=clientes&acao=dados_cobranca&registro=$idContasAReceber target=_BLANK>$tituloEmpresa</a></a>",'lancamento');
													itemLinhaTMNOURL($DescTituloEmpresa, 'center', 'top', '10%', $corFundo, 0, 'normal10');
													itemLinhaTMNOURL($numeroDocumento, 'center', 'top', '10%', $corFundo, 0, 'normal10');
													itemLinhaTMNOURL($nomePessoa, 'left', 'top', '33%', $corFundo, 0, 'normal8');
													itemLinhaTMNOURL(layoutOcorrenciasBanespaDebito($codigoOcorrencia), 'center', 'top', '15%', $corFundo, 0, 'normal8');
													itemLinhaTMNOURL(converteData($dtVencimento,'retorno','formdata'), 'center', 'top', '5%', $corFundo, 0, 'normal8');
													itemLinhaTMNOURL(formatarValoresForm($valor), 'right', 'top', '10%', $corFundo, 0, 'normal10');
													itemLinhaTMNOURL(formatarValoresArquivoRetorno($valorRecebido), 'right', 'top', '10%', $corFundo, 0, 'normal10');
												fechaLinhaTabela();
												# Mostrar cabecalho
												
												# Incrementar valor total
												$valorTotal+=formatarValores($valor);
												$valorTotalRecebido+=formatarValores($valorRecebido);
																					
												# Em caso de efetivação - validar dados para BD
												if($matriz[efetivar] && $codigoOcorrencia="00") {
													# Importar dados para banco
													$matriz[id]=$idContasAReceber;
													$matriz[valorRecebido]=formatarValores($valorRecebido);
													$matriz[dtBaixa]=converteData($dtVencimento,'arquivo','banco');
													$matriz[obs]="Arquivo Retorno: Ocorrencia: " . layoutOcorrenciasBanespa($codigoOcorrencia);
													
													dbContasReceber($matriz, 'baixar');
													
													$numRetornados++;
												}
											}
										}
										
										# Retornar para banco de dados
									}
		
								}
								elseif($tipoRegistro=='Z') {
									# Trailler de arquivo
								}
							}
							else {
								//fechaTabela();
								//htmlFechaColuna();
								//htmlFechaLinha();
								
							}
								
						}
					
					} //fim do retorno banespa debito automatico////////////////////////////////////
					
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL("Processados: $numProcessados / Retornados: $numRetornados", 'center', 'top', '10%', $corFundo, 3, 'tabfundo0');
						itemLinhaTMNOURL('Totais:', 'right', 'top', '10%', $corFundo, 2, 'bold10');
						itemLinhaTMNOURL(formatarValoresForm($valorTotal), 'right', 'top', '10%', $corFundo, 0, 'txtok');
						itemLinhaTMNOURL(formatarValoresForm($valorTotalRecebido), 'right', 'top', '5%', $corFundo, 0, 'txtok');
					fechaLinhaTabela();
					fechaTabela();
					htmlFechaColuna();
					fechaLinhaTabela();
					
					fechaTabela();
					
					# Efetivar arquivo
					if($matriz[efetivar]=='S') {
						$matriz[id]=$id;
						dbArquivoRetorno($matriz, 'ativar');
					}
				}
				#
				#------------------------------------------------------------------
				#
				elseif($banco[numero]=='748') {
					# Layout Bansicredi
					# Mostrar cabecalho
					echo "<br>";
					novaTabela2("[Detalhe do Processamento]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
					itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
	
	//				# Quebrar conteúdo
	//				$tmpDados=explode("\n",$conteudo);
					
					# Zerar valor total
					$valorTotal=0;
					
					# Contadores
					$numRetornados;
					$numProcessados;
					
					# Layout do itau começa da 2a. linha
					
					$exibir = array();
					$exibirEstornados = array();
					$l = 0;
					
					for($a=0;$a<count($tmpDados);$a++) {
						# Verificar Tipo de registro
						$tipoRegistro=$tipoServico=arquivoLerDados($tmpDados,'matriz', $a, 1, 1);
						
						if($tipoRegistro == '0') {
							# Header
							# Identificação de tipo de arquivo
							$tipoArquivo=arquivoLerDados($tmpDados, 'matriz', $a, 2, 1);
							
							if($tipoArquivo == '2') {
								# Arquivo é retorno
								$tipoServico=arquivoLerDados($tmpDados,'matriz',$a, 10, 2);
								$contaNumero=arquivoLerDados($tmpDados,'matriz',$a, 27, 5);

								$codigoBanco=arquivoLerDados($tmpDados,'matriz',$a, 77, 3);
								$nomeBanco=arquivoLerDados($tmpDados,'matriz',$a, 80, 15);
								$dtGeracao=arquivoLerDados($tmpDados,'matriz',$a, 95, 8);
								$numeroSeqRetorno=arquivoLerDados($tmpDados,'matriz',$a, 109, 5);

								$numeroSeqArquivo=arquivoLerDados($tmpDados,'matriz',$a, 395, 6);
								
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Código do Banco:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($banco['numero'], 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();

								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Conta:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($contaNumero, 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Data Geração:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm(converteData($dtGeracao,'arquivo','formdata'), 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Numero do Arquivo:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($numeroSeqRetorno, 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								fechaTabela();
									
							}
						}
						elseif($tipoRegistro=='1') {
						
							$numProcessados++;
							
							if($tipoServico) {				
								# Transação
								$codigoInscricao=arquivoLerDados($tmpDados,'matriz',$a, 2, 2);
								$numeroInscricao=arquivoLerDados($tmpDados,'matriz',$a, 4, 14);
	//							$agenciaMantenedora=arquivoLerDados($tmpDados,'matriz',$a, 18, 4);
	//							#
	//							$contaNumero=arquivoLerDados($tmpDados,'matriz',$a, 30, 6);
	//							$contaDigitoconta=arquivoLerDados($tmpDados,'matriz',$a, 36, 1);
	//							#
	//							$tituloEmpresa=arquivoLerDados($tmpDados,'matriz',$a, 38, 25);
//								$carteira			= arquivoLerDados($tmpDados, 'matriz', $a, 108,  1);
								$codigoOcorrencia	= arquivoLerDados($tmpDados, 'matriz', $a, 109,  2);
								$dtOcorrencia		= arquivoLerDados($tmpDados, 'matriz', $a, 111,  6);
								$numeroDocumento	= arquivoLerDados($tmpDados, 'matriz', $a, 117, 10);
//								$nossoNumero		= arquivoLerDados($tmpDados, 'matriz', $a, 127, 20);
								$dtVencimento		= arquivoLerDados($tmpDados, 'matriz', $a, 147,  6);
								$valorTitulo		= arquivoLerDados($tmpDados, 'matriz', $a, 153, 13);
								$tarifaCobranca		= arquivoLerDados($tmpDados, 'matriz', $a, 176, 13);
//								$valorIOF			= arquivoLerDados($tmpDados, 'matriz', $a, 215, 13);
								$valorAbatimento	= arquivoLerDados($tmpDados, 'matriz', $a, 228, 13);
								$valorDescontos		= arquivoLerDados($tmpDados, 'matriz', $a, 241, 13);
								$valorPrincipal		= arquivoLerDados($tmpDados, 'matriz', $a, 254, 13);
								$jurosMoraMulta		= arquivoLerDados($tmpDados, 'matriz', $a, 267, 13);
//								$outrosCreditos		= arquivoLerDados($tmpDados, 'matriz', $a, 280, 13);
								$dtCredito			= arquivoLerDados($tmpDados, 'matriz', $a, 329,  8);
								$InstrCancelada		= arquivoLerDados($tmpDados, 'matriz', $a, 319, 10);
								//$nomeSacado=arquivoLerDados($tmpDados,'matriz',$a, 325, 30);
								$erros=arquivoLerDados($tmpDados,'matriz',$a, 319, 10);
								//$codLiquidacao=arquivoLerDados($tmpDados,'matriz',$a, 393, 2);
														
								$numeroSeqArquivo=arquivoLerDados($tmpDados,'matriz',$a, 395, 6);
								
								if($numeroDocumento && strlen(trim($numeroDocumento)>0) && is_numeric(trim($numeroDocumento))) {
									
									$numeroDocumento=intval($numeroDocumento);
									
									# Buscar informação do documento
									$sqlDocumentos="
										SELECT 
											$tb[DocumentosGerados].id,
											$tb[DocumentosGerados].idFaturamento,
											$tb[DocumentosGerados].idPessoaTipo,
											$tb[ContasReceber].id idContasAReceber,
											$tb[ContasReceber].status status,
											$tb[Pessoas].nome nomePessoa
										FROM 
											$tb[Pessoas],
											$tb[PessoasTipos],
											$tb[DocumentosGerados],
											$tb[ContasReceber]
										WHERE
											$tb[DocumentosGerados].idPessoaTipo=$tb[PessoasTipos].id
											AND $tb[PessoasTipos].idPessoa=$tb[Pessoas].id
											AND $tb[DocumentosGerados].id=$tb[ContasReceber].idDocumentosGerados
											AND $tb[DocumentosGerados].id=$numeroDocumento";
											
									$consulta=consultaSQL($sqlDocumentos, $conn);
									
									if($consulta && contaConsulta($consulta)>0) {
									
										$nomePessoa=resultadoSQL($consulta, 0, 'nomePessoa');
										$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
										$idContasAReceber=resultadoSQL($consulta, 0, 'idContasAReceber');
										$status=resultadoSQL($consulta, 0, 'status');
										
										# Se ValorPrincipal for recebido, creditar valor e baixar boleto
										
										# Verificar status do documento
										if($status) {
											# Mostrar dados do titulo
											//estorno
//											if ($status == 'B' && $codigoOcorrencia == '02'){
//																								$c=0;
//												$exibirEstornados[$l][$c++] = htmlMontaOpcao("<a href=?modulo=faturamento&sub=clientes&acao=dados_cobranca&registro=$idContasAReceber target=_BLANK>$idContasAReceber</a></a>",'lancamento');
//												$exibirEstornados[$l][$c++] = $nomePessoa;
//												$exibirEstornados[$l][$c++] = "<div class=txtaviso8>Entrada Extornada: Boleto Pendente, não recebido.</div>";
//												$exibirEstornados[$l][$c++] = converteData($dtOcorrencia,'arquivo','formdata');
//												$exibirEstornados[$l++][$c++] = formatarValoresArquivoRetorno($valorTitulo);
//												
//												$valor=formatarValores($valorTitulo);
//												
//												if($matriz[efetivar]) {
//	
//													# Importar dados para banco
//													$matriz[id]=$idContasAReceber;
//													$matriz[valor] = $valor;
//													$matriz[valorRecebido]=0;
//													$matriz[valorJuros]=0;
//													$matriz[valorDesconto]=0;
//													$matriz[dtBaixa]='';
//													$matriz[status] = 'P';
//													$matriz[obs]="Entrada Extornada: Boleto Pendente, não recebido.";
//													
//													dbContasReceber($matriz, 'estornar');
//													
//													$numRetornados++;
//												}
//											}
//											else{
											if( $codigoOcorrencia == "06" || $codigoOcorrencia == "09" ){
												$c=0;
												$exibir[$l][$c++] = htmlMontaOpcao("<a href=?modulo=faturamento&sub=clientes&acao=dados_cobranca&registro=$idContasAReceber target=_BLANK>$idContasAReceber</a></a>",'lancamento');
												$exibir[$l][$c++] = $nomePessoa;
												$exibir[$l][$c++] = getLabelOcorrenciaBradesco($codigoOcorrencia, $erros);
												$exibir[$l][$c++] = converteData($dtOcorrencia,'arquivo','formdata');
												$exibir[$l][$c++] = converteData($dtCredito,'retorno','formdata');
												$exibir[$l][$c++] = formatarValoresArquivoRetorno($valorTitulo);
												$exibir[$l][$c++] = formatarValoresArquivoRetorno($jurosMoraMulta);
												$exibir[$l][$c++] = formatarValoresArquivoRetorno($valorDescontos);
												$exibir[$l++][$c++] = formatarValoresArquivoRetorno($valorPrincipal);
										
	
												##conta baixada e transferida para desconto.
												if ( $codigoOcorrencia ==  '10' && substr_count($erros, '20')){
													$valorPrincipal = $valorTitulo;
													$obs = "ATENCAO, titulo transferido para desconto.";
												}
												##
												
												# Incrementar valor total
												$valorTotal+=formatarValores($valorTitulo);
												$valorTotalRecebido+=formatarValores($valorPrincipal);
												$valorTotalJuros+=formatarValores($jurosMoraMulta);
												$valorTotalDescontos+=formatarValores($valorDescontos);
												
												# Em caso de efetivação - validar dados para BD
												if($matriz[efetivar] && formatarValores($valorPrincipal)>0) {
	
													# Importar dados para banco
													$matriz[id]=$idContasAReceber;
													$matriz[valorRecebido]=formatarValores($valorPrincipal);
													$matriz[valorJuros]=formatarValores($jurosMoraMulta);
													$matriz[valorDesconto]=formatarValores($valorDescontos);
													$matriz[dtBaixa]=converteData($dtOcorrencia,'arquivo','banco');
													$matriz[obs]="Arquivo Retorno: Ocorrencia: " . layoutOcorrenciasBradesco($codigoOcorrencia, $erros);
													
													dbContasReceber($matriz, 'baixar');
													
													$numRetornados++;
												}
											} //registro normal
										}//com status
									}
									
									# Retornar para banco de dados
								}
	
							}
							elseif($tipoRegistro=='9') {
								# Trailler de arquivo
							}
						}	
					} // fim do loop de registros.
					
					#exibicao na tela
					
					if (count($exibirEstornados)){
						echo "<br>";
						novaTabela("[Atenção: Boletos transferidos de carteira e estornadas pelo sistema]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 9);
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL('Documento', 'center', 'top', '10%', $corFundo, 0, 'tabfundo0');
							itemLinhaTMNOURL('Sacado', 'center', 'top', '25%', $corFundo, 0, 'tabfundo0');
							itemLinhaTMNOURL('Ocorrência', 'center', 'top', '25%', $corFundo, 0, 'tabfundo0');
							itemLinhaTMNOURL('Dt Ocor.', 'center nowrap', 'top', '5%', $corFundo, 0, 'tabfundo0');
							itemLinhaTMNOURL('Valor', 'center nowrap', 'top', '5%', $corFundo, 0, 'tabfundo0');
						fechaLinhaTabela();
						foreach ($exibirEstornados as $linha){
							$c = 0;
							novaLinhaTabela($corFundo, '100%');
								itemLinhaTMNOURL($linha[$c++], 'center', 'top', '10%', $corFundo, 0, 'normal10');
								itemLinhaTMNOURL($linha[$c++], 'left', 'top', '33%', $corFundo, 0, 'normal8');
								itemLinhaTMNOURL($linha[$c++], 'center', 'top', '15%', $corFundo, 0, 'normal8');
								itemLinhaTMNOURL($linha[$c++], 'center', 'top', '5%', $corFundo, 0, 'normal8');
								itemLinhaTMNOURL($linha[$c++], 'right', 'top', '10%', $corFundo, 0, 'normal10');
							fechaLinhaTabela();
							# Mostrar cabecalho	
						}
						fechaTabela();
					}
					echo "<br>";
					

					novaTabela("[Listagem de Documentos Retornardos]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 9);
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('Documento', 'center', 'top', '10%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Sacado', 'center', 'top', '25%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Ocorrência', 'center', 'top', '25%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Dt Ocor.', 'center nowrap', 'top', '5%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Dt Cred', 'center nowrap', 'top', '5%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Valor', 'center nowrap', 'top', '5%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Juros', 'center', 'top', '5%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Desc', 'center', 'top', '5%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Recebido', 'center', 'top', '10%', $corFundo, 0, 'tabfundo0');
					fechaLinhaTabela();
					
					foreach ($exibir as $linha){
						$c = 0;
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL($linha[$c++], 'center', 'top', '10%', $corFundo, 0, 'normal10');
							itemLinhaTMNOURL($linha[$c++], 'left', 'top', '33%', $corFundo, 0, 'normal8');
							itemLinhaTMNOURL($linha[$c++], 'center', 'top', '15%', $corFundo, 0, 'normal8');
							itemLinhaTMNOURL($linha[$c++], 'center', 'top', '5%', $corFundo, 0, 'normal8');
							itemLinhaTMNOURL($linha[$c++], 'center', 'top', '5%', $corFundo, 0, 'normal8');
							itemLinhaTMNOURL($linha[$c++], 'right', 'top', '10%', $corFundo, 0, 'normal10');
							itemLinhaTMNOURL($linha[$c++], 'right', 'top', '5%', $corFundo, 0, 'normal10');
							itemLinhaTMNOURL($linha[$c++], 'right', 'top', '5%', $corFundo, 0, 'normal10');
							itemLinhaTMNOURL($linha[$c++], 'right', 'top', '10%', $corFundo, 0, 'normal10');
						fechaLinhaTabela();
						# Mostrar cabecalho	
					}
					
					
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL("Processados: $numProcessados / Retornados: $numRetornados", 'center', 'top', '10%', $corFundo, 3, 'tabfundo0');
						itemLinhaTMNOURL('Totais:', 'right', 'top', '10%', $corFundo, 2, 'bold10');
						itemLinhaTMNOURL(formatarValoresForm($valorTotal), 'right', 'top', '10%', $corFundo, 0, 'txtok');
						itemLinhaTMNOURL(formatarValoresForm($valorTotalJuros), 'right', 'top', '5%', $corFundo, 0, 'txtok');
						itemLinhaTMNOURL(formatarValoresForm($valorTotalDescontos), 'right', 'top', '5%', $corFundo, 0, 'txtok');
						itemLinhaTMNOURL(formatarValoresForm($valorTotalRecebido), 'right', 'top', '5%', $corFundo, 0, 'txtok');
					fechaLinhaTabela();
					fechaTabela();
					htmlFechaColuna();
					fechaLinhaTabela();
					
//					fechaTabela();
					
					# Efetivar arquivo
					if($matriz[efetivar]=='S') {
						$matriz[id]=$id;
						dbArquivoRetorno($matriz, 'ativar');
					}
				}
				
				
				#
				#------------------------------------------------------------------
				#
				elseif($banco[numero]=='399') {
					# Layout HSBC
					# Mostrar cabecalho
					echo "<br>";
					novaTabela2("[Detalhe do Processamento]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
					itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
	
	//				# Quebrar conteúdo
	//				$tmpDados=explode("\n",$conteudo);
					
					# Zerar valor total
					$valorTotal=0;
					
					# Contadores
					$numRetornados;
					$numProcessados;
					
					# Layout do itau começa da 2a. linha
					
					$exibir = array();
					$exibirEstornados = array();
					$l = 0;
					
					for($a=0;$a<count($tmpDados);$a++) {
						# Verificar Tipo de registro
						$tipoRegistro=$tipoServico=arquivoLerDados($tmpDados,'matriz', $a, 1, 1);
						
						if($tipoRegistro == '0') {
							# Header
							# Identificação de tipo de arquivo
							$tipoArquivo = arquivoLerDados($tmpDados, 'matriz', $a, 2, 1);
							
							if($tipoArquivo == '2') {
								# Arquivo é retorno
								$tipoServico		= arquivoLerDados($tmpDados,'matriz',$a, 10, 2);
								$agencia			= arquivoLerDados($tmpDados,'matriz',$a, 28, 4);
								$contaNumero		= arquivoLerDados($tmpDados,'matriz',$a, 38, 5);
								$contaDig			= arquivoLerDados($tmpDados,'matriz',$a, 43, 2);
								$nomeEmpresa		= arquivoLerDados($tmpDados,'matriz',$a, 47, 30);
								$codigoBanco		= arquivoLerDados($tmpDados,'matriz',$a, 77, 3);
								$nomeBanco			= arquivoLerDados($tmpDados,'matriz',$a, 80, 15);
								$dtGeracao			= arquivoLerDados($tmpDados,'matriz',$a, 95, 6);
								$dtCredito			= arquivoLerDados($tmpDados,'matriz',$a, 120, 6);
								$numeroSeqRetorno	= arquivoLerDados($tmpDados,'matriz',$a, 389, 5);
								$numeroSeqArquivo	= arquivoLerDados($tmpDados,'matriz',$a, 395, 6);
								
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Código do Banco:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($banco[numero], 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Agência:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($agencia, 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Conta:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($contaNumero.'-'.$contaDig, 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Nome Empresa:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($nomeEmpresa, 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Data Geração:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm(converteData($dtGeracao,'arquivo','formdata'), 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Data de Crédito:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm(converteData($dtCredito,'arquivo','formdata'), 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Numero do Arquivo:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($numeroSeqRetorno, 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								fechaTabela();
									
							}
						}
						elseif($tipoRegistro=='1') {
						
							$numProcessados++;
							
							if($tipoServico) {
								# Transação
								$codigoInscricao	= arquivoLerDados($tmpDados,'matriz',$a, 2, 2);
								$numeroInscricao	= arquivoLerDados($tmpDados,'matriz',$a, 4, 14);
								$carteira			= arquivoLerDados($tmpDados,'matriz',$a, 108, 1);
								$codigoOcorrencia	= arquivoLerDados($tmpDados,'matriz',$a, 109, 2);
								$dtOcorrencia		= arquivoLerDados($tmpDados,'matriz',$a, 111, 6);
								$numeroDocumento	= arquivoLerDados($tmpDados,'matriz',$a, 117, 10);
								$nossoNumero		= arquivoLerDados($tmpDados,'matriz',$a, 127, 11);
								$dtVencimento		= arquivoLerDados($tmpDados,'matriz',$a, 147, 6);
								$valorTitulo		= arquivoLerDados($tmpDados,'matriz',$a, 153, 13);
								$tarifaCobranca		= arquivoLerDados($tmpDados,'matriz',$a, 176, 13);
								$valorAbatimento	= arquivoLerDados($tmpDados,'matriz',$a, 228, 13);
								$valorDescontos		= arquivoLerDados($tmpDados,'matriz',$a, 241, 13);
								$valorPrincipal		= arquivoLerDados($tmpDados,'matriz',$a, 254, 13);
								$jurosMoraMulta		= arquivoLerDados($tmpDados,'matriz',$a, 267, 13);
								$dtCredito			= $dtOcorrencia;
								# ???? $dtCredito			= arquivoLerDados($tmpDados,'matriz',$a, 296, 6);
								# ???? $erros=arquivoLerDados($tmpDados,'matriz',$a, 319, 10);

								$numeroSeqArquivo=arquivoLerDados($tmpDados,'matriz',$a, 395, 6);
								
								if( $numeroDocumento && strlen( trim($numeroDocumento) > 0 ) && is_numeric( trim( $numeroDocumento ) ) ) {
									
									$numeroDocumento = intval($numeroDocumento);
									
									# Buscar informação do documento
									$sqlDocumentos="
										SELECT 
											$tb[DocumentosGerados].id,
											$tb[DocumentosGerados].idFaturamento,
											$tb[DocumentosGerados].idPessoaTipo,
											$tb[ContasReceber].id idContasAReceber,
											$tb[ContasReceber].status status,
											$tb[Pessoas].nome nomePessoa
										FROM 
											$tb[Pessoas],
											$tb[PessoasTipos],
											$tb[DocumentosGerados],
											$tb[ContasReceber]
										WHERE
											$tb[DocumentosGerados].idPessoaTipo=$tb[PessoasTipos].id
											AND $tb[PessoasTipos].idPessoa=$tb[Pessoas].id
											AND $tb[DocumentosGerados].id=$tb[ContasReceber].idDocumentosGerados
											AND $tb[DocumentosGerados].id=$numeroDocumento";
											
									$consulta=consultaSQL($sqlDocumentos, $conn);
									
									if($consulta && contaConsulta($consulta)>0) {
									
										$nomePessoa=resultadoSQL($consulta, 0, 'nomePessoa');
										$idPessoaTipo=resultadoSQL($consulta, 0, 'idPessoaTipo');
										$idContasAReceber=resultadoSQL($consulta, 0, 'idContasAReceber');
										$status=resultadoSQL($consulta, 0, 'status');
										
										# Se ValorPrincipal for recebido, creditar valor e baixar boleto
										
										# Verificar status do documento
										if($status) {
											# Mostrar dados do titulo
											$ocorrenciasEfetivacao = array('06', '07', '15', '16', '31', '32', '33', '36', '38', '39');
											
											if(in_array($codigoOcorrencia, $ocorrenciasEfetivacao)) {
												$c=0;
												$exibir[$l][$c++] = htmlMontaOpcao("<a href=?modulo=faturamento&sub=clientes&acao=dados_cobranca&registro=$idContasAReceber target=_BLANK>$idContasAReceber</a></a>",'lancamento');
												$exibir[$l][$c++] = $nomePessoa;
												$exibir[$l][$c++] = layoutOcorrenciasHSBC( $codigoOcorrencia );
												$exibir[$l][$c++] = converteData($dtOcorrencia,'arquivo','formdata');
												$exibir[$l][$c++] = converteData($dtCredito,'arquivo','formdata');
												$exibir[$l][$c++] = formatarValoresArquivoRetorno($valorTitulo);
												$exibir[$l][$c++] = formatarValoresArquivoRetorno($jurosMoraMulta);
												$exibir[$l][$c++] = formatarValoresArquivoRetorno($valorDescontos);
												$exibir[$l++][$c++] = formatarValoresArquivoRetorno($valorPrincipal);

												# Incrementar valor total
												$valorTotal+=formatarValores($valorTitulo);
												$valorTotalRecebido+=formatarValores($valorPrincipal);
												$valorTotalJuros+=formatarValores($jurosMoraMulta);
												$valorTotalDescontos+=formatarValores($valorDescontos);
												
												# Em caso de efetivação - validar dados para BD
												if($matriz[efetivar] && formatarValores($valorPrincipal)>0) {
	
													# Importar dados para banco
													$matriz[id]=$idContasAReceber;
													$matriz[valorRecebido]=formatarValores($valorPrincipal);
													$matriz[valorJuros]=formatarValores($jurosMoraMulta);
													$matriz[valorDesconto]=formatarValores($valorDescontos);
													$matriz[dtBaixa]=converteData($dtOcorrencia,'arquivo','banco');
													$matriz[obs]="Arquivo Retorno: Ocorrencia: " . layoutOcorrenciasHSBC($codigoOcorrencia);
													
													dbContasReceber($matriz, 'baixar');
													
													$numRetornados++;
												}
											}

										}//com status
									}
									
									# Retornar para banco de dados
								}
	
							}
							elseif($tipoRegistro=='9') {
								# Trailler de arquivo
							}
						}	
					} // fim do loop de registros.
					
					echo "<br>";
					

					novaTabela("[Listagem de Documentos Retornardos]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 9);
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('Documento', 'center', 'top', '10%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Sacado', 'center', 'top', '25%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Ocorrência', 'center', 'top', '25%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Dt Ocor.', 'center nowrap', 'top', '5%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Dt Cred', 'center nowrap', 'top', '5%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Valor', 'center nowrap', 'top', '5%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Juros', 'center', 'top', '5%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Desc', 'center', 'top', '5%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Recebido', 'center', 'top', '10%', $corFundo, 0, 'tabfundo0');
					fechaLinhaTabela();
					
					foreach ($exibir as $linha){
						$c = 0;
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL($linha[$c++], 'center', 'top', '10%', $corFundo, 0, 'normal10');
							itemLinhaTMNOURL($linha[$c++], 'left', 'top', '33%', $corFundo, 0, 'normal8');
							itemLinhaTMNOURL($linha[$c++], 'center', 'top', '15%', $corFundo, 0, 'normal8');
							itemLinhaTMNOURL($linha[$c++], 'center', 'top', '5%', $corFundo, 0, 'normal8');
							itemLinhaTMNOURL($linha[$c++], 'center', 'top', '5%', $corFundo, 0, 'normal8');
							itemLinhaTMNOURL($linha[$c++], 'right', 'top', '10%', $corFundo, 0, 'normal10');
							itemLinhaTMNOURL($linha[$c++], 'right', 'top', '5%', $corFundo, 0, 'normal10');
							itemLinhaTMNOURL($linha[$c++], 'right', 'top', '5%', $corFundo, 0, 'normal10');
							itemLinhaTMNOURL($linha[$c++], 'right', 'top', '10%', $corFundo, 0, 'normal10');
						fechaLinhaTabela();
						# Mostrar cabecalho	
					}
					
					
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL("Processados: $numProcessados / Retornados: $numRetornados", 'center', 'top', '10%', $corFundo, 3, 'tabfundo0');
						itemLinhaTMNOURL('Totais:', 'right', 'top', '10%', $corFundo, 2, 'bold10');
						itemLinhaTMNOURL(formatarValoresForm($valorTotal), 'right', 'top', '10%', $corFundo, 0, 'txtok');
						itemLinhaTMNOURL(formatarValoresForm($valorTotalJuros), 'right', 'top', '5%', $corFundo, 0, 'txtok');
						itemLinhaTMNOURL(formatarValoresForm($valorTotalDescontos), 'right', 'top', '5%', $corFundo, 0, 'txtok');
						itemLinhaTMNOURL(formatarValoresForm($valorTotalRecebido), 'right', 'top', '5%', $corFundo, 0, 'txtok');
					fechaLinhaTabela();
					fechaTabela();
					htmlFechaColuna();
					fechaLinhaTabela();
					
//					fechaTabela();
					
					# Efetivar arquivo
					if($matriz[efetivar]=='S') {
						$matriz[id]=$id;
						dbArquivoRetorno($matriz, 'ativar');
					}
				}

				#
				#------------------------------------------------------------------
				#
				elseif($banco[numero]=='104') {
					# Layout CEF
					# Mostrar cabecalho
					echo "<br>";
					novaTabela2("[Detalhe do Processamento]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
					itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
	
	//				# Quebrar conteúdo
	//				$tmpDados=explode("\n",$conteudo);
					
					# Zerar valor total
					$valorTotal=0;
					
					# Contadores
					$numRetornados;
					$numProcessados;
					
					# Layout da cef começa da 2a. linha
					
					$exibir = array();
					$exibirEstornados = array();
					$l = 0;
					
					for($a=0;$a<count($tmpDados);$a++) {
						# Verificar Tipo de registro
						$tipoRegistro=$tipoServico=arquivoLerDados($tmpDados,'matriz', $a, 1, 1);
						
						if($tipoRegistro == '0') {
							# Header
							# Identificação de tipo de arquivo
							$tipoArquivo = arquivoLerDados($tmpDados, 'matriz', $a, 2, 1);
							
							if($tipoArquivo == '2') {
								# Arquivo é retorno
								
								//código da empresa AAAAAOOOCCCCCCCCD, onde: A= agencia, O=operação, C conta, D=digito
								$codigoEmpresa = arquivoLerDados($tmpDados,'matriz',$a, 27, 16);
								
								$tipoServico		= arquivoLerDados($tmpDados,'matriz',$a, 10, 2);
								$agencia			= substr($codigoEmpresa,  0, 4);
								$contaNumero		= substr($codigoEmpresa,  8, 8);
								$contaDig			= substr($codigoEmpresa, 16, 1);
								$nomeEmpresa		= arquivoLerDados($tmpDados,'matriz',$a,  47, 30);
								$codigoBanco		= arquivoLerDados($tmpDados,'matriz',$a,  77,  3);
								$nomeBanco			= arquivoLerDados($tmpDados,'matriz',$a,  80, 15);
								$dtGeracao			= arquivoLerDados($tmpDados,'matriz',$a,  95,  6);
								//$dtCredito			= arquivoLerDados($tmpDados,'matriz',$a, 120,  6); //socorro
								$numeroSeqRetorno	= arquivoLerDados($tmpDados,'matriz',$a, 390,  5);
								$numeroSeqArquivo	= arquivoLerDados($tmpDados,'matriz',$a, 395,  6);
								
								
								
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Código do Banco:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($banco[numero], 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Agência:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($agencia, 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Conta:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($contaNumero.'-'.$contaDig, 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Nome Empresa:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($nomeEmpresa, 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Data Geração:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm(converteData($dtGeracao,'arquivo','formdata'), 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
//								novaLinhaTabela($corFundo, '100%');
//									itemLinhaTMNOURL('<b>Data de Crédito:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
//									itemLinhaForm(converteData($dtCredito,'arquivo','formdata'), 'left', 'top', $corFundo, 0, 'tabfundo1');
//								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Numero do Arquivo:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($numeroSeqRetorno, 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								fechaTabela();
									
							}
						}
						elseif($tipoRegistro=='1') {
						
							$numProcessados++;

							if( $tipoServico ) {
								# Transação
								$codigoInscricao	= arquivoLerDados($tmpDados, 'matriz', $a,   2,  2);
								$numeroInscricao	= arquivoLerDados($tmpDados, 'matriz', $a,   4, 14);
								$carteira			= arquivoLerDados($tmpDados, 'matriz', $a, 107,  2);
								$codigoOcorrencia	= arquivoLerDados($tmpDados, 'matriz', $a, 109,  2);
								$dtOcorrencia		= arquivoLerDados($tmpDados, 'matriz', $a, 111,  6);
								//$numeroDocumento	= arquivoLerDados($tmpDados, 'matriz', $a, 117, 10); // Help me, help me
								$nossoNumero		= arquivoLerDados($tmpDados, 'matriz', $a,  63, 11);
								$dtVencimento		= arquivoLerDados($tmpDados, 'matriz', $a, 147,  6);
								$valorTitulo		= arquivoLerDados($tmpDados, 'matriz', $a, 153, 13);
								$tarifaCobranca		= arquivoLerDados($tmpDados, 'matriz', $a, 176, 13);
								$valorAbatimento	= arquivoLerDados($tmpDados, 'matriz', $a, 228, 13);
								$valorDescontos		= arquivoLerDados($tmpDados, 'matriz', $a, 241, 13);
								$valorPrincipal		= arquivoLerDados($tmpDados, 'matriz', $a, 254, 13);
								$jurosMoraMulta		= arquivoLerDados($tmpDados, 'matriz', $a, 267, 13);
								$dtCredito			= arquivoLerDados($tmpDados, 'matriz', $a, 294,  6);

								/*
								o estranho é que a coordenada indicada na documentação da CEF não está tudo em branco, 
								e achei o valor em branco, no entanto, achei uns valores referente a fatura em "nosso numero".
								*/
								$numeroDocumento = substr($nossoNumero, 2, 8); 
								
								$numeroSeqArquivo=arquivoLerDados($tmpDados,'matriz',$a, 395, 6);
								
								if( $numeroDocumento && strlen( trim($numeroDocumento) > 0 ) && is_numeric( trim( $numeroDocumento ) ) ) {

									$numeroDocumento = intval($numeroDocumento);

									# Buscar informação do documento
									$sqlDocumentos="
										SELECT 
											$tb[DocumentosGerados].id,
											$tb[DocumentosGerados].idFaturamento,
											$tb[DocumentosGerados].idPessoaTipo,
											$tb[ContasReceber].id idContasAReceber,
											$tb[ContasReceber].status status,
											$tb[Pessoas].nome nomePessoa
										FROM 
											$tb[Pessoas],
											$tb[PessoasTipos],
											$tb[DocumentosGerados],
											$tb[ContasReceber]
										WHERE
											$tb[DocumentosGerados].idPessoaTipo=$tb[PessoasTipos].id
											AND $tb[PessoasTipos].idPessoa=$tb[Pessoas].id
											AND $tb[DocumentosGerados].id=$tb[ContasReceber].idDocumentosGerados
											AND $tb[DocumentosGerados].id='$numeroDocumento'";

									$consulta=consultaSQL($sqlDocumentos, $conn);

									if($consulta && contaConsulta($consulta)>0) {
									
										$nomePessoa			= resultadoSQL($consulta, 0, 'nomePessoa');
										$idPessoaTipo		= resultadoSQL($consulta, 0, 'idPessoaTipo');
										$idContasAReceber	= resultadoSQL($consulta, 0, 'idContasAReceber');
										$status				= resultadoSQL($consulta, 0, 'status');
										
										# Se ValorPrincipal for recebido, creditar valor e baixar boleto
										
										# Verificar status do documento
										if($status) {
											# Mostrar dados do titulo
											$ocorrenciasEfetivacao = array( '21', '22' );
											
											if(in_array($codigoOcorrencia, $ocorrenciasEfetivacao)) {
												$c=0;
												$exibir[$l][$c++] = htmlMontaOpcao("<a href=?modulo=faturamento&sub=clientes&acao=dados_cobranca&registro=$idContasAReceber target=_BLANK>$idContasAReceber</a></a>",'lancamento');
												$exibir[$l][$c++] = $nomePessoa;
												$exibir[$l][$c++] = layoutOcorrenciasCEF( $codigoOcorrencia );
												$exibir[$l][$c++] = converteData($dtOcorrencia,'arquivo','formdata');
												$exibir[$l][$c++] = converteData($dtCredito,'arquivo','formdata');
												$exibir[$l][$c++] = formatarValoresArquivoRetorno($valorTitulo);
												$exibir[$l][$c++] = formatarValoresArquivoRetorno($jurosMoraMulta);
												$exibir[$l][$c++] = formatarValoresArquivoRetorno($valorDescontos);
												$exibir[$l++][$c++] = formatarValoresArquivoRetorno($valorPrincipal);

												# Incrementar valor total
												$valorTotal+=formatarValores($valorTitulo);
												$valorTotalRecebido+=formatarValores($valorPrincipal);
												$valorTotalJuros+=formatarValores($jurosMoraMulta);
												$valorTotalDescontos+=formatarValores($valorDescontos);
												
												# Em caso de efetivação - validar dados para BD
												if($matriz[efetivar] && formatarValores($valorPrincipal)>0) {
	
													# Importar dados para banco
													$matriz[id]=$idContasAReceber;
													$matriz[valorRecebido]=formatarValores($valorPrincipal);
													$matriz[valorJuros]=formatarValores($jurosMoraMulta);
													$matriz[valorDesconto]=formatarValores($valorDescontos);
													$matriz[dtBaixa]=converteData($dtOcorrencia,'arquivo','banco');
													$matriz[obs]="Arquivo Retorno: Ocorrencia: " . layoutOcorrenciasCEF($codigoOcorrencia);
													
													dbContasReceber($matriz, 'baixar');
													
													$numRetornados++;
												}
											}

										}//com status
									}
									
									# Retornar para banco de dados
								}
	
							}
							elseif($tipoRegistro=='9') {
								# Trailler de arquivo
							}
						}	
					} // fim do loop de registros.
					
					echo "<br>";
					

					novaTabela("[Listagem de Documentos Retornardos]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 9);
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('Documento',	'center', 		 'top', '10%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Sacado',		'center', 		 'top', '25%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Ocorrência',	'center', 		 'top', '25%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Dt Ocor.', 	'center nowrap', 'top',  '5%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Dt Cred',		'center nowrap', 'top',  '5%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Valor',		'center nowrap', 'top',  '5%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Juros',		'center', 		 'top',  '5%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Desc',		'center', 		 'top',  '5%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Recebido',	'center', 		 'top', '10%', $corFundo, 0, 'tabfundo0');
					fechaLinhaTabela();
					
					foreach ($exibir as $linha){
						$c = 0;
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL($linha[$c++], 'center', 'top', '10%', $corFundo, 0, 'normal10');
							itemLinhaTMNOURL($linha[$c++], 'left',	 'top', '33%', $corFundo, 0, 'normal8' );
							itemLinhaTMNOURL($linha[$c++], 'center', 'top', '15%', $corFundo, 0, 'normal8' );
							itemLinhaTMNOURL($linha[$c++], 'center', 'top',  '5%', $corFundo, 0, 'normal8' );
							itemLinhaTMNOURL($linha[$c++], 'center', 'top',  '5%', $corFundo, 0, 'normal8' );
							itemLinhaTMNOURL($linha[$c++], 'right',  'top', '10%', $corFundo, 0, 'normal10');
							itemLinhaTMNOURL($linha[$c++], 'right',  'top',  '5%', $corFundo, 0, 'normal10');
							itemLinhaTMNOURL($linha[$c++], 'right',  'top',  '5%', $corFundo, 0, 'normal10');
							itemLinhaTMNOURL($linha[$c++], 'right',  'top', '10%', $corFundo, 0, 'normal10');
						fechaLinhaTabela();
						# Mostrar cabecalho	
					}
					
					
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL("Processados: $numProcessados / Retornados: $numRetornados", 'center', 'top', '10%', $corFundo, 3, 'tabfundo0');
						itemLinhaTMNOURL('Totais:', 'right', 'top', '10%', $corFundo, 2, 'bold10');
						itemLinhaTMNOURL(formatarValoresForm($valorTotal), 'right', 'top', '10%', $corFundo, 0, 'txtok');
						itemLinhaTMNOURL(formatarValoresForm($valorTotalJuros), 'right', 'top', '5%', $corFundo, 0, 'txtok');
						itemLinhaTMNOURL(formatarValoresForm($valorTotalDescontos), 'right', 'top', '5%', $corFundo, 0, 'txtok');
						itemLinhaTMNOURL(formatarValoresForm($valorTotalRecebido), 'right', 'top', '5%', $corFundo, 0, 'txtok');
					fechaLinhaTabela();
					fechaTabela();
					htmlFechaColuna();
					fechaLinhaTabela();
					
//					fechaTabela();
					
					# Efetivar arquivo
					if($matriz[efetivar]=='S') {
						$matriz[id]=$id;
						dbArquivoRetorno($matriz, 'ativar');
					}
				} // fim processa layout cef
				#
				#--------------------------------------------------------------
				#
				
				elseif($banco[numero]=='756') {
					# Layout Sicoob
					# Mostrar cabecalho
					echo "<br>";
					novaTabela2("[Detalhe do Processamento]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
					itemTabelaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
	
	//				# Quebrar conteúdo
	//				$tmpDados=explode("\n",$conteudo);
					
					# Zerar valor total
					$valorTotal=0;
					
					# Contadores
					$numRetornados;
					$numProcessados;
					
					# Layout da cef começa da 2a. linha
					
					$exibir = array();
					$exibirEstornados = array();
					$l = 0;
					
					for($a=0;$a<count($tmpDados);$a++) {
						# Verificar Tipo de registro
						$tipoRegistro=$tipoServico=arquivoLerDados($tmpDados,'matriz', $a, 1, 1);
						
						if($tipoRegistro == '0') {
							# Header
							# Identificação de tipo de arquivo
							$tipoArquivo = arquivoLerDados($tmpDados, 'matriz', $a, 2, 1);
							
							if($tipoArquivo == '2') {
								# Arquivo é retorno
								
								//código da empresa AAAAAOOOCCCCCCCCD, onde: A= agencia, O=operação, C conta, D=digito
								$codigoEmpresa = arquivoLerDados($tmpDados,'matriz',$a, 27, 16);
								
								$tipoServico		= arquivoLerDados($tmpDados,'matriz',$a, 10, 2);
								$agencia			= substr($codigoEmpresa,  0, 4);
								$contaNumero		= substr($codigoEmpresa,  9, 4);
								$contaDig			= substr($codigoEmpresa, 13, 1);
								$nomeEmpresa		= arquivoLerDados($tmpDados,'matriz',$a,  47, 30);
								$codigoBanco		= arquivoLerDados($tmpDados,'matriz',$a,  77,  3);
								$nomeBanco			= arquivoLerDados($tmpDados,'matriz',$a,  83, 10);
								$dtGeracao			= arquivoLerDados($tmpDados,'matriz',$a,  95,  6);
								//$dtCredito			= arquivoLerDados($tmpDados,'matriz',$a, 120,  6); //socorro
								$numeroSeqRetorno	= arquivoLerDados($tmpDados,'matriz',$a, 101,  7);
								$numeroSeqArquivo	= arquivoLerDados($tmpDados,'matriz',$a, 395,  6);
								
								
								
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Código do Banco:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($banco[numero], 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Agência:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($agencia, 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Conta:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($contaNumero.'-'.$contaDig, 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Nome Empresa:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($nomeEmpresa, 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Data Geração:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm(converteData($dtGeracao,'arquivo','formdata'), 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
//								novaLinhaTabela($corFundo, '100%');
//									itemLinhaTMNOURL('<b>Data de Crédito:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
//									itemLinhaForm(converteData($dtCredito,'arquivo','formdata'), 'left', 'top', $corFundo, 0, 'tabfundo1');
//								fechaLinhaTabela();
								novaLinhaTabela($corFundo, '100%');
									itemLinhaTMNOURL('<b>Numero do Arquivo:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
									itemLinhaForm($numeroSeqRetorno, 'left', 'top', $corFundo, 0, 'tabfundo1');
								fechaLinhaTabela();
								fechaTabela();
									
							}
						}
						elseif($tipoRegistro=='1') {
						
							$numProcessados++;

							if( $tipoServico ) {
								# Transação
								$codigoInscricao	= arquivoLerDados($tmpDados, 'matriz', $a,   2,  2); // 01 Física, 02 Jurídica
								$numeroInscricao	= arquivoLerDados($tmpDados, 'matriz', $a,   4, 14);
								$carteira			= arquivoLerDados($tmpDados, 'matriz', $a, 107,  2);
								$codigoOcorrencia	= arquivoLerDados($tmpDados, 'matriz', $a, 109,  2);
								$dtOcorrencia		= arquivoLerDados($tmpDados, 'matriz', $a, 111,  6);
								//$numeroDocumento	= arquivoLerDados($tmpDados, 'matriz', $a, 117, 10); // Help me, help me
								$nossoNumero		= arquivoLerDados($tmpDados, 'matriz', $a,  63, 11);
								$dtVencimento		= arquivoLerDados($tmpDados, 'matriz', $a, 147,  6);
								$valorTitulo		= arquivoLerDados($tmpDados, 'matriz', $a, 153, 13);
								$tarifaCobranca		= arquivoLerDados($tmpDados, 'matriz', $a, 176, 13);
								$valorAbatimento	= arquivoLerDados($tmpDados, 'matriz', $a, 228, 13);
								$valorDescontos		= arquivoLerDados($tmpDados, 'matriz', $a, 241, 13);
								$valorPrincipal		= arquivoLerDados($tmpDados, 'matriz', $a, 254, 13);
								$jurosMoraMulta		= arquivoLerDados($tmpDados, 'matriz', $a, 267, 13);
								$dtCredito			= arquivoLerDados($tmpDados, 'matriz', $a, 176,  6);

								/*
								o estranho é que a coordenada indicada na documentação da CEF não está tudo em branco, 
								e achei o valor em branco, no entanto, achei uns valores referente a fatura em "nosso numero".
								*/
								$numeroDocumento = substr($nossoNumero, 2, 9); 
								
								$numeroSeqArquivo=arquivoLerDados($tmpDados,'matriz',$a, 395, 6);
								
								if( $numeroDocumento && strlen( trim($numeroDocumento) > 0 ) && is_numeric( trim( $numeroDocumento ) ) ) {

									$numeroDocumento = intval($numeroDocumento);

									# Buscar informação do documento
									$sqlDocumentos="
										SELECT 
											$tb[DocumentosGerados].id,
											$tb[DocumentosGerados].idFaturamento,
											$tb[DocumentosGerados].idPessoaTipo,
											$tb[ContasReceber].id idContasAReceber,
											$tb[ContasReceber].status status,
											$tb[Pessoas].nome nomePessoa
										FROM 
											$tb[Pessoas],
											$tb[PessoasTipos],
											$tb[DocumentosGerados],
											$tb[ContasReceber]
										WHERE
											$tb[DocumentosGerados].idPessoaTipo=$tb[PessoasTipos].id
											AND $tb[PessoasTipos].idPessoa=$tb[Pessoas].id
											AND $tb[DocumentosGerados].id=$tb[ContasReceber].idDocumentosGerados
											AND $tb[DocumentosGerados].id='$numeroDocumento'";

									$consulta=consultaSQL($sqlDocumentos, $conn);

									if($consulta && contaConsulta($consulta)>0) {
									
										$nomePessoa			= resultadoSQL($consulta, 0, 'nomePessoa');
										$idPessoaTipo		= resultadoSQL($consulta, 0, 'idPessoaTipo');
										$idContasAReceber	= resultadoSQL($consulta, 0, 'idContasAReceber');
										$status				= resultadoSQL($consulta, 0, 'status');
										
										# Se ValorPrincipal for recebido, creditar valor e baixar boleto
										
										# Verificar status do documento
										if($status) {
											# Mostrar dados do titulo
											$ocorrenciasEfetivacao = array( '05', '06', '15' );
											
											if(in_array($codigoOcorrencia, $ocorrenciasEfetivacao)) {
												
												$c=0;
												$exibir[$l][$c++] = htmlMontaOpcao("<a href=?modulo=faturamento&sub=clientes&acao=dados_cobranca&registro=$idContasAReceber target=_BLANK>$idContasAReceber</a></a>",'lancamento');
												$exibir[$l][$c++] = $nomePessoa;
												$exibir[$l][$c++] = layoutOcorrenciasSicoob($codigoOcorrencia);
												$exibir[$l][$c++] = converteData($dtOcorrencia,'arquivo','formdata');
												$exibir[$l][$c++] = converteData($dtCredito,'arquivo','formdata');
												$exibir[$l][$c++] = formatarValoresArquivoRetorno($valorTitulo);
												$exibir[$l][$c++] = formatarValoresArquivoRetorno($jurosMoraMulta);
												$exibir[$l][$c++] = formatarValoresArquivoRetorno($valorDescontos);
												$exibir[$l++][$c++] = formatarValoresArquivoRetorno($valorPrincipal);

												# Incrementar valor total
												$valorTotal+=formatarValores($valorTitulo);
												$valorTotalRecebido+=formatarValores($valorPrincipal);
												$valorTotalJuros+=formatarValores($jurosMoraMulta);
												$valorTotalDescontos+=formatarValores($valorDescontos);
												
												# Em caso de efetivação - validar dados para BD
												if($matriz[efetivar] && formatarValores($valorPrincipal)>0) {
	
													# Importar dados para banco
													$matriz[id]=$idContasAReceber;
													$matriz[valorRecebido]=formatarValores($valorPrincipal);
													$matriz[valorJuros]=formatarValores($jurosMoraMulta);
													$matriz[valorDesconto]=formatarValores($valorDescontos);
													$matriz[dtBaixa]=converteData($dtOcorrencia,'arquivo','banco');
													$matriz[obs]="Arquivo Retorno: Ocorrencia: " . layoutOcorrenciasSicoob($codigoOcorrencia);
													
													dbContasReceber($matriz, 'baixar');
													
													$numRetornados++;
												}
											}

										}//com status
									}
									
									# Retornar para banco de dados
								}
	
							}
							elseif($tipoRegistro=='9') {
								# Trailler de arquivo
							}
						}	
					} // fim do loop de registros.
					
					echo "<br>";
					

					novaTabela("[Listagem de Documentos Retornardos]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 9);
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('Documento',	'center', 		 'top', '10%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Sacado',		'center', 		 'top', '25%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Ocorrência',	'center', 		 'top', '25%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Dt Ocor.', 	'center nowrap', 'top',  '5%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Dt Cred',		'center nowrap', 'top',  '5%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Valor',		'center nowrap', 'top',  '5%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Juros',		'center', 		 'top',  '5%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Desc',		'center', 		 'top',  '5%', $corFundo, 0, 'tabfundo0');
						itemLinhaTMNOURL('Recebido',	'center', 		 'top', '10%', $corFundo, 0, 'tabfundo0');
					fechaLinhaTabela();
					
					foreach ($exibir as $linha){
						$c = 0;
						novaLinhaTabela($corFundo, '100%');
							itemLinhaTMNOURL($linha[$c++], 'center', 'top', '10%', $corFundo, 0, 'normal10');
							itemLinhaTMNOURL($linha[$c++], 'left',	 'top', '33%', $corFundo, 0, 'normal8' );
							itemLinhaTMNOURL($linha[$c++], 'center', 'top', '15%', $corFundo, 0, 'normal8' );
							itemLinhaTMNOURL($linha[$c++], 'center', 'top',  '5%', $corFundo, 0, 'normal8' );
							itemLinhaTMNOURL($linha[$c++], 'center', 'top',  '5%', $corFundo, 0, 'normal8' );
							itemLinhaTMNOURL($linha[$c++], 'right',  'top', '10%', $corFundo, 0, 'normal10');
							itemLinhaTMNOURL($linha[$c++], 'right',  'top',  '5%', $corFundo, 0, 'normal10');
							itemLinhaTMNOURL($linha[$c++], 'right',  'top',  '5%', $corFundo, 0, 'normal10');
							itemLinhaTMNOURL($linha[$c++], 'right',  'top', '10%', $corFundo, 0, 'normal10');
						fechaLinhaTabela();
						# Mostrar cabecalho	
					}
					
					
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL("Processados: $numProcessados / Retornados: $numRetornados", 'center', 'top', '10%', $corFundo, 3, 'tabfundo0');
						itemLinhaTMNOURL('Totais:', 'right', 'top', '10%', $corFundo, 2, 'bold10');
						itemLinhaTMNOURL(formatarValoresForm($valorTotal), 'right', 'top', '10%', $corFundo, 0, 'txtok');
						itemLinhaTMNOURL(formatarValoresForm($valorTotalJuros), 'right', 'top', '5%', $corFundo, 0, 'txtok');
						itemLinhaTMNOURL(formatarValoresForm($valorTotalDescontos), 'right', 'top', '5%', $corFundo, 0, 'txtok');
						itemLinhaTMNOURL(formatarValoresForm($valorTotalRecebido), 'right', 'top', '5%', $corFundo, 0, 'txtok');
					fechaLinhaTabela();
					fechaTabela();
					htmlFechaColuna();
					fechaLinhaTabela();
					
//					fechaTabela();
					
					# Efetivar arquivo
					if($matriz[efetivar]=='S') {
						$matriz[id]=$id;
						dbArquivoRetorno($matriz, 'ativar');
					}
				} // fim processa layout Sicoob
				#
				#--------------------------------------------------------------
				#
				
				else {
					# erro
					$msg="Layout não pode ser processado";
					avisoNOURL("Ocorrência de erro", $msg, 400);
				}
			}
		}
		else {
			# erro
			$msg="Registro não encontrado";
			avisoNOURL("Ocorrência de erro", $msg, 400);
		}
	}
}

?>
