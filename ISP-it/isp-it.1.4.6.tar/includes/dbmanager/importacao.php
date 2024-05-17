<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 20/05/2003
# Ultima alteração: 03/02/2004
#    Alteração No.: 041
#
# Função:
#    Página principal (index) da aplicação


# Menu de Importação
function DBManagerImportacao($modulo, $sub, $acao, $registro, $matriz) {

	global $configAppName, $configAppVersion, $corFundo, $corBorda, $html;
	
	$sessLogin = $_SESSION["sessLogin"];
	
	# Buscar informações sobre usuario - permissões
	$permissao=buscaPermissaoUsuario($sessLogin[login]);

	# Mostrar menu
	novaTabela2("[IMPORTAÇÃO]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 4);
		novaLinhaTabela($corFundo, '100%');
			htmlAbreColuna('55%', 'left', $corFundo, 0, 'tabfundo1');
				echo "<br><img src=".$html[imagem][logoPequeno]." border=0 align=left>
				<b class=bold>$configAppName - IMPORTAÇÃO</b><br>
				<span class=normal10>Importação de dados para o $configAppName.</span>";
			htmlFechaColuna();
			htmlAbreColuna('5%', 'left', $corFundo, 0, 'normal');
				echo "&nbsp;";
			htmlFechaColuna();									
			$texto=htmlMontaOpcao("<br>Importar Clientes<br>e Serviços", 'importar');
			itemLinha($texto, "?modulo=$modulo&sub=clientes", 'center', $corFundo, 0, 'normal');
			$texto=htmlMontaOpcao("<br>Importar Clientes", 'importar');
			itemLinha($texto, "?modulo=$modulo&sub=cadastros", 'center', $corFundo, 0, 'normal');
		fechaLinhaTabela();
	fechaTabela();
	
	# Modulos
	if($sub=='clientes') {
		# Menu de modulos
		DBManagerImportarClientes($modulo, $sub, $acao, $registro, $matriz);	
	}
}


# Importação de Clientes
function DBManagerImportarClientes($modulo, $sub, $acao, $registro, $matriz) {
	
	echo "<br>";
	
	// Formulário para upload de arquivo de importação
	DBManagerFormImportarClientes($modulo, $sub, $acao, $registro, $matriz);
	
	
	if($matriz[bntConfirmar]) {
		# carregar arquivo
		$arquivo=$_REQUEST["arquivo"];
		
		$matriz[conteudo]=(fread
	             (fopen ($_FILES["arquivo"]["tmp_name"], "r"),
	             filesize ($_FILES["arquivo"]["tmp_name"])));

		$matriz[nomeArquivo]=$_FILES[arquivo][name];
	
		// Validar as colunas do arquivo
		$linhas=explode("\n", $matriz[conteudo]);
		
		arquivoQuebrarColunas($linhas, $matriz);
		
	}

}
	

# Formulário de upload de arquivo filtro
function DBManagerFormImportarClientes($modulo, $sub, $acao, $registro, $matriz) {
	
	global $html, $corFundo, $corBorda;
	
	
	# Motrar tabela de busca
	novaTabela2("[Importação de Cadastro de Clientes]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		novaLinhaTabela($corFundo, '100%');
		$texto="			
			<form method=post name=matriz action=dbmanager.php enctype='multipart/form-data'>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=registro value=$registro>&nbsp;";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Arquivo:</b><br>
			<span class=normal10>Selecione o arquivo a importar</span>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
			$texto="<input type=file name=arquivo>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Separador:</b><br>
			<span class=normal10>Caracter separador das colunas</span>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
			$texto="<input type=text  name=matriz[separador] size=1 value='$matriz[separador]'>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			if($matriz[debug]=='S') $opcDebug='checked';
			itemLinhaTMNOURL('<b>Debug:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
			$texto="<input type=checkbox name=matriz[debug] value=S size=1 $opcDebug>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			if($matriz[importar]=='S') $opcImportar='checked';
			itemLinhaTMNOURL('<b>Gravar Dados:</b>', 'right', 'top', '40%', $corFundo, 0, 'tabfundo1');
			$texto="<input type=checkbox name=matriz[importar] value=S size=1 $opcImportar>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
		
		formPessoaSubmit($modulo, $sub, $acao, $registro, $matriz);
	fechaTabela();	
}



# Definir data de vencimento
function DBManagerImportarDataVencimento($data) {
	
	#sobre a data de vencimento:
	#clientes cadastrados entre os dias 1 e 10: vencimento 10
	#clientes cadastrados entre os dias 11 e 20: vencimento 20
	#clientes cadastrados entre os dias 21 e 30: vencimento 30

}


# Tipo de pessoa
function DBManagerImportarTipoPessoa($tipo) {

	if($tipo == 1) return('F');
	elseif($tipo == 2) return('J');
	
}


function DBManagerImportarFones($matriz) {
	
	if($matriz[fone1]) {
		$matriz[fone1]=formatarFoneNumeros($matriz[fone1]);
		if(strlen($matriz[fone1])>8) {
			if(strlen($matriz[fone1])==9) {
				$matriz[ddd_fone1]=substr($matriz[fone1],0,2);
				$matriz[fone1]=substr($matriz[fone1],2,strlen($matriz[fone1]));
			}
			elseif(strlen($matriz[fone1])==10) {
				$matriz[ddd_fone1]=substr($matriz[fone1],0,2);
				$matriz[fone1]=substr($matriz[fone1],2,strlen($matriz[fone1]));
			}
		}
	}
	if($matriz[fone2]) {
		$matriz[fone2]=formatarFoneNumeros($matriz[fone2]);
		if(strlen($matriz[fone2])>8) {
			if(strlen($matriz[fone2])==9) {
				$matriz[ddd_fone2]=substr($matriz[fone2],0,2);
				$matriz[fone2]=substr($matriz[fone2],2,strlen($matriz[fone2]));
			}
			elseif(strlen($matriz[fone2])==10) {
				$matriz[ddd_fone2]=substr($matriz[fone2],0,2);
				$matriz[fone2]=substr($matriz[fone2],2,strlen($matriz[fone2]));
			}
		}
	}
	if($matriz[fax]) {
		$matriz[fax]=formatarFoneNumeros($matriz[fax]);
		if(strlen($matriz[fax])>8) {
			if(strlen($matriz[fax])==9) {
				$matriz[ddd_fax]=substr($matriz[fax],0,2);
				$matriz[fax]=substr($matriz[fax],2,strlen($matriz[fax]));
			}
			elseif(strlen($matriz[fax])==10) {
				$matriz[ddd_fax]=substr($matriz[fax],0,2);
				$matriz[fax]=substr($matriz[fax],2,strlen($matriz[fax]));
			}
		}
	}
	
	return($matriz);
	
	
}


function DBManagerImportarDocumentos($matriz) {
	
	if($matriz[pessoaTipo]=='F') {
		$matriz[cpf]=cpfFormatar($matriz[documento]);
	}
	else {
		$matriz[cnpj]=cnpjFormatar($matriz[documento]);
	}
	
	return($matriz);
}

?>