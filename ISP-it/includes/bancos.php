<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 05/07/2003
# Ultima alteração: 03/02/2004
#    Alteração No.: 006
#
# Função:
#    Painel - Funções para cadastro de bancos


#Dia 18/09/2006, 11:00h, comentado por Damiao: Dentro da function listarBancos, foi comentado uma linha que exibia o link de Documento, 
#a mesma dava o opçcao para adicionar o documento, a tabela de DocumentosPessoasTipos exige o campo idPessoa, porem banco não tem 
#idPessoa


# Função de banco de dados - Pessoas
function dbBanco($matriz, $tipo) {

	global $conn, $tb, $modulo, $sub, $acao;
	
	# Sql de inclusão
	if($tipo=='incluir') {
	
		if($matriz[id]) {
		
			$data=dataSistema();
			$dataNascimento=converteData($matriz[dtNascimento],'form','bancodata');
		
			$sql="INSERT INTO $tb[Bancos] VALUES (0,
			$matriz[idPessoaTipo],
			'$matriz[numero]')";
		}
	} #fecha inclusao
	elseif($tipo=='excluir') {
		$sql="DELETE FROM $tb[Bancos] where id=$matriz[id]";
	}
	elseif($tipo=='alterar') {
		$sql="UPDATE $tb[Bancos] SET numero='$matriz[numero]' where id=$matriz[id]";
	}
	
	if($sql) { 
		$retorno=consultaSQL($sql, $conn);
		return($retorno); 
	}
}




# função de busca 
function buscaBancos($texto, $campo, $tipo, $ordem)
{
	global $conn, $tb, $corFundo, $modulo, $sub;
	
	if($tipo=='todos') {
		$sql="SELECT * FROM $tb[Bancos] ORDER BY $ordem";
	}
	elseif($tipo=='contem') {
		$sql="SELECT * from $tb[Bancos] WHERE $campo LIKE '%$texto%' ORDER BY $ordem";
	}
	elseif($tipo=='igual') {
		$sql="SELECT * from $tb[Bancos] WHERE $campo='$texto' ORDER BY $ordem";
	}
	elseif($tipo=='custom') {
		$sql="SELECT * from $tb[Bancos] WHERE $texto ORDER BY $ordem";
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




function bancos($modulo, $sub, $acao, $registro, $matriz) {
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
	
		# Verificar tipo de Pessoa
		if($sub=='bancos') $matriz[tipoPessoa]='ban';
		
		# Buscar Tipo Pessoa
		$tipoPessoa=checkTipoPessoa($matriz[tipoPessoa]);
		
		
		# Topo da tabela - Informações e menu principal do Cadastro
		novaTabela2("[Cadastro de $tipoPessoa[descricao]]", "center", '100%', 0, 3, 1, $corFundo, $corBorda, 4);
			novaLinhaTabela($corFundo, '100%');
				htmlAbreColuna('65%', 'left', $corFundo, 0, 'tabfundo1');
					echo "<br><img src=".$html[imagem][bancos]." border=0 align=left><b class=bold>$tipoPessoa[descricao]</b>
					<br><span class=normal10>Cadastro de <b>$tipoPessoa[descricao]</b>.</span>";
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
			echo "<br>";
			procurarPessoas($modulo, $sub, $acao, $registro, $matriz);			
		}
		
		# Inclusão
		if($acao=="adicionar") {
			itemTabelaNOURL("&nbsp;", 'left', $corFundo, 0, 'normal');
			adicionarBancos($modulo, $sub, $acao, $registro, $matriz);
		}
		elseif($acao=='procurar') {
			echo "<br>";
			procurarPessoas($modulo, $sub, $acao, $registro, $matriz);
		}
		# Listagem
		elseif($acao=="listar") {
			echo "<br>";
			listarBancos($modulo, $sub, $acao, $registro, $matriz);
		}
		# Excluir
		elseif($acao=="excluir") {
			echo "<br>";
			excluirBancos($modulo, $sub, $acao, $registro, $matriz);
		}
		# Excluir
		elseif($acao=="ver") {
			echo "<br>";
			verBancos($modulo, $sub, $acao, $registro, $matriz);
		}
		# Alterar
		elseif($acao=="alterar") {
			echo "<br>";
			alterarBancos($modulo, $sub, $acao, $registro, $matriz);
		}
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
	}	


}



# função para adicionar bancos
function adicionarBancos($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $sessCadastro;


	if(!$matriz[bntConfirmar]) {
	
		# Tipo de Pessoa
		$tipoPessoa=checkTipoPessoa($matriz[tipoPessoa]);
		
		# Motrar tabela de busca
		novaTabela2("[$tipoPessoa[descricao] - Adicionar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			menuOpcAdicional($modulo, $sub, $acao, $registro);
			#fim das opcoes adicionais
			novaLinhaTabela($corFundo, '100%');
			$texto="			
				<form method=post name=matriz action=index.php>
				<input type=hidden name=modulo value=$modulo>
				<input type=hidden name=sub value=$sub>
				<input type=hidden name=matriz[tipoPessoa] value=$matriz[tipoPessoa]>
				<input type=hidden name=acao value=$acao>&nbsp;";
				itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();

			# Dados Cadastrais
			formDadosBanco($modulo, $sub, $acao, $registro, $matriz);
			
			formPessoaEndereco($modulo, $sub, $acao, $registro, $matriz);
				
			formPessoaSubmit($modulo, $sub, $acao, $registro, $matriz);
					
			htmlFechaLinha();
		fechaTabela();
	}
	else {
		# Motrar tabela
		
		# Tipo de Pessoa
		$tipoPessoa=checkTipoPessoa($matriz[tipoPessoa]);
		
		novaTabela2("[$tipoPessoa[descricao] - Confirmação de Cadastro]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			# Opcoes Adicionais
			menuOpcAdicional($modulo, $sub, $acao, $registro);		
			itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Nome:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaForm($matriz[nome], 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();			
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Número do Banco:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				itemLinhaForm($matriz[numero], 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();	
		
			if( $matriz[nome] && $matriz[endereco] && $matriz[numero]) {

				# Gravar
				$data=dataSistema();
				
				# Buscar ID para Novo Cadastro
				$matriz[id]=buscaIDNovoPessoa();
				$matriz[dtCadastro]=$data[dataBanco];
				
				if(!$matriz[id]) {
					# ERRO - Pessoa nova impossível de ser criada
					$msg="Ocorreu um erro na tentativa de buscar novo codigo para cadastro! - ID";
					$url="?modulo=$modulo&sub=$sub&acao=$acao";
					aviso("Aviso", $msg, $url, 760);
				}
				else {
				
					# Incluir Pessoa
					$gravaPessoa=dbPessoa($matriz, 'incluir');
					
					if($gravaPessoa) {
						
						$matriz[idPessoaTipo]=buscaIDNovoPessoaTipo();
						
						# Incluir Pessoa Tipo
						$gravaPessoaTipo=dbPessoaTipo($matriz, 'incluir');
						
						# Verificar se PessoaTipo foi gravado
						if($gravaPessoaTipo) {
						
							# Gravar Banco
							$gravaBanco=dbBanco($matriz, 'incluir');
							
							if($gravaBanco) {
								# Gravar Endereços
								$gravaEndereco=dbEndereco($matriz, 'incluir');
								
								if($gravaEndereco) {
									# OK
									$msg="Cadastro efetuado!";
									$url="?modulo=$modulo&sub=$sub&acao=novo";
									aviso2("Aviso", $msg, $url, 760, 2, 0, 'center');
								}
								else {
									# Excluir pessoa
									dbPessoa($matriz, 'excluir');
									$msg="Ocorreu um erro na tentativa de gravar o Cadastro de $tipoPessoa[descricao]! - Endereço";
									$url="?modulo=$modulo&sub=$sub&acao=$acao";
									aviso("Aviso", $msg, $url, 760);
								}	
							}
							else {
								# Excluir pessoa
								dbPessoa($matriz, 'excluir');
								$msg="Ocorreu um erro na tentativa de gravar o Cadastro de $tipoPessoa[descricao]! - Endereço";
								$url="?modulo=$modulo&sub=$sub&acao=$acao";
								aviso("Aviso", $msg, $url, 760);
							}
						}
						else {
							# Excluir pessoa
							dbPessoa($matriz, 'excluir');							
							$msg="Ocorreu um erro na tentativa de gravar o Cadastro de $tipoPessoa[descricao]! - Pessoa Tipo";
							$url="?modulo=$modulo&sub=$sub&acao=$acao";
							aviso("Aviso", $msg, $url, 760);
						}
						
					}
					else {
						# ERRO - Pessoa nova impossível de ser criada
						$msg="Ocorreu um erro na tentativa de gravar o Cadastro de $tipoPessoa[descricao]! - Pessoa";
						$url="?modulo=$modulo&sub=$sub&acao=$acao";
						aviso("Aviso", $msg, $url, 760);
					}
					
				}
			}
			else {
				# Falta de parâmetros
				# Mensagem de aviso
				$msg="Campos obrigatórios não preenchidos!<br> Preencha todos os campos antes de prosseguir com o cadastro! ";
				$url="?modulo=$modulo&sub=$sub&acao=$acao";
				aviso("Aviso", $msg, $url, 760);
			}
			
		fechaTabela();
	}
	
}


# Listar 
function listarBancos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $moduloApp, $corFundo, $corBorda, $html, $limite, $conn, $tb;

	# Cabeçalho		
	# Motrar tabela de busca
	novaTabela("[Listar]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 5);
		# Seleção de registros
		
		$sql="
			SELECT 
				$tb[Bancos].id,
				$tb[Bancos].numero numero,
				$tb[Pessoas].id idPessoa,
				$tb[Pessoas].nome nome, 
				$tb[Pessoas].dtCadastro
			FROM 
				$tb[PessoasTipos], 
				$tb[TipoPessoas], 
				$tb[Bancos], 
				$tb[Pessoas] 
			WHERE 
				$tb[Pessoas].id=$tb[PessoasTipos].idPessoa 
				AND $tb[Bancos].idPessoaTipo = $tb[PessoasTipos].id 
				AND $tb[PessoasTipos].idTipo=$tb[TipoPessoas].id";
		
		$consulta=consultaSQL($sql, $conn);
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Não há registros
			itemTabelaNOURL('Não há registros cadastrados', 'left', $corFundo, 4, 'txtaviso');
		}
		else {
		
			# Paginador
			paginador($consulta, contaConsulta($consulta), $limite[lista][bancos], $registro, 'normal10', 4, $urlADD);
		
			# Cabeçalho
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTabela('Nome', 'center', '30%', 'tabfundo0');
				itemLinhaTabela('Número', 'center', '10%', 'tabfundo0');
				itemLinhaTabela('Data Cadastro', 'center', '20%', 'tabfundo0');
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

			$limite=$i+$limite[lista][bancos];
			
			while($i < contaConsulta($consulta) && $i < $limite) {
				# Mostrar registro
				$id=resultadoSQL($consulta, $i, 'id');
				$idPessoa=resultadoSQL($consulta, $i, 'idPessoa');
				$nome=resultadoSQL($consulta, $i, 'nome');
				$numero=resultadoSQL($consulta, $i, 'numero');
				$dtCadastro=resultadoSQL($consulta, $i, 'dtCadastro');
				
				$opcoes="";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=alterar&registro=$id>Alterar</a>",'alterar');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=excluir&registro=$id>Excluir</a>",'excluir');
				$opcoes.="&nbsp;";
				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=enderecos&registro=$idPessoa>Enderecos</a>",'endereco');
				$opcoes.="&nbsp;";
//acima a explicação do comentario:				
//				$opcoes.=htmlMontaOpcao("<a href=?modulo=$modulo&sub=$sub&acao=documentos&registro=$idPessoa>Documentos</a>",'documento');
				
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTabela($nome, 'left', '30%', 'normal10');
					itemLinhaTabela($numero, 'center', '10%', 'normal10');
					itemLinhaTabela(converteData($dtCadastro, 'banco','form'), 'center', '20%', 'normal10');
					itemLinhaTabela($opcoes, 'center nowrap', '40%', 'normal10');
				fechaLinhaTabela();
				
				# Incrementar contador
				$i++;
			} #fecha laco de montagem de tabela
		} #fecha listagem

	fechaTabela();
	
} # fecha função de listagem



# Exclusão 
function excluirBancos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $conn, $tb;
	
	# ERRO - Registro não foi informado
	if(!$registro) {
		# Mostrar Erro
		$msg="Registro não foi encontrado!";
		$url="?modulo=$modulo&sub=$sub&acao=$acao";
		aviso("Aviso", $msg, $url, 760);
	}
	# Form de inclusao
	elseif($registro && !$matriz[bntExcluir]) {
	
		$sql="
			SELECT 
				$tb[Bancos].id,
				$tb[Bancos].numero numero,
				$tb[Pessoas].id idPessoa,
				$tb[Pessoas].nome nome, 
				$tb[Pessoas].dtCadastro
			FROM 
				$tb[PessoasTipos], 
				$tb[TipoPessoas], 
				$tb[Bancos], 
				$tb[Pessoas] 
			WHERE 
				$tb[Pessoas].id=$tb[PessoasTipos].idPessoa 
				AND $tb[Bancos].idPessoaTipo = $tb[PessoasTipos].id 
				AND $tb[PessoasTipos].idTipo=$tb[TipoPessoas].id
				AND $tb[Bancos].id=$registro";
		
		# Buscar Valores
		$consulta=consultaSQL($sql, $conn);
		
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
			$numero=resultadoSQL($consulta, 0, 'numero');
			
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
						echo "<b class=bold>Nome: </b>";
					htmlFechaColuna();
					itemLinhaForm($nome, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Número: </b>";
					htmlFechaColuna();
					itemLinhaForm($numero, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
	
				# Botão de confirmação
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "&nbsp;";
					htmlFechaColuna();
					$texto="<input type=submit name=matriz[bntExcluir] value=Excluir class=submit>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		} #fecha alteracao
	} #fecha form - !$bntExcluir
	
	# Alteração - bntExcluir pressionado
	elseif($matriz[bntExcluir]) {
		# Cadastrar em banco de dados
		$grava=dbBanco($matriz, 'excluir');
				
		# Verificar inclusão de registro
		if($grava) {
			# Mensagem de aviso
			$msg="Registro excluído com Sucesso!";
			$url="?modulo=$modulo&sub=$sub&acao=listar";
			aviso("Aviso", $msg, $url, 760);
		}
		
	} #fecha bntExcluir
	
} #fecha exclusao 



# Visualizar
function verBancos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $conn, $tb;
	
	# ERRO - Registro não foi informado
	if(!$registro) {
		# Mostrar Erro
		$msg="Registro não foi encontrado!";
		$url="?modulo=$modulo&sub=$sub&acao=$acao";
		aviso("Aviso", $msg, $url, 760);
	}
	# Form de inclusao
	elseif($registro && !$matriz[bntExcluir]) {
	
		# Quebrar Registro
		$matRegistro=explode(":", $registro);
		$idPessoaTipo=$matRegistro[0];
		$idPessoa=$matRegistro[1];
	
		$sql="
			SELECT 
				$tb[Bancos].id,
				$tb[Bancos].numero numero,
				$tb[Pessoas].id idPessoa,
				$tb[Pessoas].nome nome, 
				$tb[Pessoas].dtCadastro
			FROM 
				$tb[PessoasTipos], 
				$tb[TipoPessoas], 
				$tb[Bancos], 
				$tb[Pessoas] 
			WHERE 
				$tb[Pessoas].id=$tb[PessoasTipos].idPessoa 
				AND $tb[Bancos].idPessoaTipo = $tb[PessoasTipos].id 
				AND $tb[PessoasTipos].idTipo=$tb[TipoPessoas].id
				AND $tb[PessoasTipos].id=$idPessoa";
		
		# Buscar Valores
		$consulta=consultaSQL($sql, $conn);
		
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
			$numero=resultadoSQL($consulta, 0, 'numero');
			
			# Motrar tabela de busca
			novaTabela2("[Excluir]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
				# Opcoes Adicionais
				menuOpcAdicional($modulo, $sub, $acao, $registro);				
				#fim das opcoes adicionais
				novaLinhaTabela($corFundo, '100%');
					itemLinhaNOURL('&nbsp;', 'left', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Nome: </b>";
					htmlFechaColuna();
					itemLinhaForm($nome, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('40%', 'right', $corFundo, 0, 'tabfundo1');
						echo "<b class=bold>Número: </b>";
					htmlFechaColuna();
					itemLinhaForm($numero, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			fechaTabela();
		} #fecha alteracao
	} #fecha form - !$bntExcluir
	
	# Alteração - bntExcluir pressionado
	elseif($matriz[bntExcluir]) {
		# Cadastrar em banco de dados
		$grava=dbBanco($matriz, 'excluir');
				
		# Verificar inclusão de registro
		if($grava) {
			# Mensagem de aviso
			$msg="Registro excluído com Sucesso!";
			$url="?modulo=$modulo&sub=$sub&acao=listar";
			aviso("Aviso", $msg, $url, 760);
		}
		
	} #fecha bntExcluir
	
} #fecha ver



# Funcao para alteração
function alterarBancos($modulo, $sub, $acao, $registro, $matriz)
{
	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html, $conn, $tb;
	
	# ERRO - Registro não foi informado
	if(!$registro) {
		# ERRO
	}
	# Form de inclusao
	elseif($registro && !$matriz[bntAlterar]) {
	
		$sql="
			SELECT 
				$tb[Bancos].id,
				$tb[Bancos].numero numero,
				$tb[Pessoas].id idPessoa,
				$tb[Pessoas].nome nome, 
				$tb[Pessoas].dtCadastro
			FROM 
				$tb[PessoasTipos], 
				$tb[TipoPessoas], 
				$tb[Bancos], 
				$tb[Pessoas] 
			WHERE 
				$tb[Pessoas].id=$tb[PessoasTipos].idPessoa 
				AND $tb[Bancos].idPessoaTipo = $tb[PessoasTipos].id 
				AND $tb[PessoasTipos].idTipo=$tb[TipoPessoas].id
				AND $tb[Bancos].id=$registro";
		
		# Buscar Valores
		$consulta=consultaSQL($sql, $conn);
		
		if(!$consulta || contaConsulta($consulta)==0) {
			# Mostrar Erro
			$msg="Registro não foi encontrado!";
			$url="?modulo=$modulo&sub=$sub&acao=$acao";
			aviso("Aviso", $msg, $url, 760);
		}
		else {
			#atribuir valores
 			$id=resultadoSQL($consulta, 0, 'id');
			$numero=resultadoSQL($consulta, 0, 'numero');
			$idPessoa=resultadoSQL($consulta, 0, 'idPessoa');
			$nome=resultadoSQL($consulta, 0, 'nome');
			
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
					<input type=hidden name=matriz[idPessoa] value=$idPessoa>
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
						echo "<b>Número: </b><br>";
					htmlFechaColuna();
					$texto="<input type=text name=matriz[numero] size=4 value='$numero'>";
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
		if($matriz[nome] && $matriz[numero]) {
			# continuar
			# Cadastrar em banco de dados
			$grava=dbBanco($matriz, 'alterar');
			
			# Verificar inclusão de registro
			if($grava) {
				# acusar falta de parametros
				
				$sql="UPDATE $tb[Pessoas] SET nome='$matriz[nome]' WHERE id=$matriz[idPessoa]";
				$gravaPessoa=consultaSQL($sql, $conn);
				
				if($gravaPessoa) {
					# Mensagem de aviso
					$msg="Registro Gravado com Sucesso!";
					$url="?modulo=$modulo&sub=$sub&acao=listar";
					aviso("Aviso", $msg, $url, 760);
				}
				else {
					$msg="Erro ao gravar registro";
					$url="?modulo=$modulo&sub=$sub&acao=listar";
					aviso("Aviso", $msg, $url, 760);
				}
			}
			else {
				$msg="Erro ao alterar dados do Banco";
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



# função de forma para seleção de tipo de pessoas
function formSelectBancos($banco, $campo, $tipo, $onChange = false) {

	global $conn, $tb;

	if($tipo=='check') {

		$sql="
			SELECT 
				$tb[Bancos].id,
				$tb[Bancos].numero numero,
				$tb[Pessoas].id idPessoa,
				$tb[Pessoas].nome nome, 
				$tb[Pessoas].dtCadastro
			FROM 
				$tb[PessoasTipos], 
				$tb[TipoPessoas], 
				$tb[Bancos], 
				$tb[Pessoas] 
			WHERE 
				$tb[Pessoas].id=$tb[PessoasTipos].idPessoa 
				AND $tb[Bancos].idPessoaTipo = $tb[PessoasTipos].id 
				AND $tb[PessoasTipos].idTipo=$tb[TipoPessoas].id
				AND $tb[Bancos].id=$banco";
				
		$consulta=consultaSQL($sql, $conn);
		
		if($consulta && contaConsulta($consulta)>0) {
			$retorno=resultadoSQL($consulta, 0, 'nome');
		}
	
	}
	elseif($tipo=='form') {
	
		$sql="
			SELECT 
				$tb[Bancos].id,
				$tb[Bancos].numero numero,
				$tb[Pessoas].id idPessoa,
				$tb[Pessoas].nome nome, 
				$tb[Pessoas].dtCadastro
			FROM 
				$tb[PessoasTipos], 
				$tb[TipoPessoas], 
				$tb[Bancos], 
				$tb[Pessoas] 
			WHERE 
				$tb[Pessoas].id=$tb[PessoasTipos].idPessoa 
				AND $tb[Bancos].idPessoaTipo = $tb[PessoasTipos].id 
				AND $tb[PessoasTipos].idTipo=$tb[TipoPessoas].id";
				
		$consulta=consultaSQL($sql, $conn);
		
		if($consulta && contaConsulta($consulta)>0) {
			
			$onChange = $onChange ? "onChange=javascript:submit()>\n" : "";
			
			$retorno="<select name=matriz[$campo] $onChange";
			
			for($i=0;$i<contaConsulta($consulta);$i++) {
			
				$id=resultadoSQL($consulta, $i, 'id');
				$nome=resultadoSQL($consulta, $i, 'nome');
				
				if($banco==$id) $opcSelect='selected';
				else $opcSelect='';
				
				
				$retorno.="<option value=$id $opcSelect>$nome";
			}
			
			$retorno.="</select>";
		}
	
	}
	return($retorno);
}



# Funçao para busca de informações do vencimento
function dadosBanco($idBanco) {

	global $conn, $tb;
	
	$sql="
		SELECT
			$tb[Bancos].id, 
			$tb[Bancos].idPessoaTipo,
			$tb[Bancos].numero, 
			$tb[Pessoas].nome
		FROM
			$tb[Bancos],
			$tb[Pessoas],
			$tb[PessoasTipos]
		WHERE
			$tb[Bancos].idPessoaTipo=$tb[PessoasTipos].id
			AND $tb[PessoasTipos].idPessoa=$tb[Pessoas].id
			AND $tb[Bancos].id=$idBanco
	";
	
	$consulta=consultaSQL($sql, $conn);
	
	if($consulta && contaConsulta($consulta)>0) {
		# dados do vencimento
		$retorno[id]=resultadoSQL($consulta, 0, 'id');
		$retorno[idPessoaTipo]=resultadoSQL($consulta, 0, 'idPessoaTipo');
		$retorno[numero]=resultadoSQL($consulta, 0, 'numero');
		$retorno[nome]=resultadoSQL($consulta, 0, 'nome');
	}
	
	return($retorno);
}

?>
