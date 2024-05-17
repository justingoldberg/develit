<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 23/06/2003
# Ultima alteração: 16/02/2004
#    Alteração No.: 015
#
# Função:
#    Painel - Funções para formulários - pessoas


# Cadastro de Pessoas
/**
 * @return unknown
 * @param unknown $tipo_pessoa
 * @param unknown $campo
 * @param unknown $tipo
 * @desc Form de seleção de Tipo de Pessoa (Física/Jurídica)
*/
function formSelectPessoaTipo($tipo_pessoa, $campo, $tipo) {

	global $pessoas;
	
	
	if($tipo=='form') {
	
		$retorno="<select name=matriz[$campo] onChange=javascript:submit()>\n";
		
		$keys=array_keys($pessoas[tipos]);
		
		for($a=0;$a<count($keys);$a++) {
		
			if($keys[$a]==$tipo_pessoa) $opcSelect='selected';
			else $opcSelect='';
			
			$retorno.="<option value=$keys[$a] $opcSelect> $keys[$a] - ".$pessoas[tipos][$keys[$a]]."\n";
		}
		
		
		$retorno.="</select>";
	}
	elseif ($tipo=='form_alt'){
		$retorno="<select name=matriz[$campo]>\n";
		
		$keys=array_keys($pessoas[tipos]);
		
		for($a=0;$a<count($keys);$a++) {
		
			if($keys[$a]==$tipo_pessoa) $opcSelect='selected';
			else $opcSelect='';
			
			$retorno.="<option value=$keys[$a] $opcSelect> $keys[$a] - ".$pessoas[tipos][$keys[$a]]."\n";
		}
		
		
		$retorno.="</select>";
	}
	elseif($tipo=='check') {
		$retorno=$pessoas[tipos][$tipo_pessoa];
	}
	
	return($retorno);
}



# form de seleção de tipo de pessoa
function formPessoaTipoPessoa($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;

	novaLinhaTabela($corFundo, '100%');
		htmlAbreColuna('30%', 'right', $corFundo, 0, 'tabfundo1');
			echo "<b>Nome: </b>";
		htmlFechaColuna();
		$texto="<input type=text name=matriz[nome] size=60 value='$matriz[nome]'>";
		itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
	fechaLinhaTabela();
	
	# Seleção de POP
	if($matriz[tipoPessoa]=='cli') {
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>POP de Acesso:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			itemLinhaForm(formSelectPOP($matriz[pop],'pop','form'), 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
	}
	
	novaLinhaTabela($corFundo, '100%');
		htmlAbreColuna('30%', 'right', $corFundo, 0, 'tabfundo1');
			echo "<b>Tipo de Pessoa: </b>";
		htmlFechaColuna();
		if($acao=='excluir' || $acao=='ver') itemLinhaForm(formSelectPessoaTipo($matriz[pessoaTipo],'pessoaTipo','check'), 'left', 'top', $corFundo, 0, 'tabfundo1');
		elseif($acao=='alterar' || $acao=='adicionar') {
			$texto="<input type=submit name=matriz[bntSelecionar] value=Selecionar class=submit>";
			itemLinhaForm(formSelectPessoaTipo($matriz[pessoaTipo],'pessoaTipo','form').$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		}
	fechaLinhaTabela();

}



# formulário de dados cadastrais
function formPessoaDadosCadastrais($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $sessCadastro;
	
	$tmpTipoPessoa=formSelectPessoaTipo($matriz[pessoaTipo], '', 'check');

	# Pessoa física
	novaLinhaTabela($corFundo, '100%');
	htmlAbreColuna('100%', 'right', $corFundo, 2, 'tabfundo1');
		novaTabela2("[Dados Cadastrais - $tmpTipoPessoa]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);

		itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');

		if($matriz[pessoaTipo]=='F') {
			if($matriz[cpf]) {
				# Validar CPF
				$matriz[cpf]=validaCPF($matriz[cpf]);
			}
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>CPF:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[cpf] size=14 value='$matriz[cpf]' onBlur=javascript:submit()> <span class=txtaviso>(Ex: 012.469.253-60 ou 01246925360)</span>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			if(!$matriz[cpf]) {
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit value='Confirmar' class=submit>";
					itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
			}
			
			$parametros=carregaParametrosConfig();
			
			if($matriz[cpf] && ( $parametros[documento_unico] == 'N' || contaConsulta(buscaDocumentosPessoas($matriz[cpf],'documento','igual','documento'))==0) ) {
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>RG:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					$texto="<input type=text name=matriz[rg] size=20 value='$matriz[rg]'> <span class=txtaviso>(Ex: 2.453.986 SSP-SC)</span>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Data de Nascimento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					$texto="<input type=text name=matriz[dtNascimento] size=10 value='$matriz[dtNascimento]' onBlur=verificaData(this.value,10)> <span class=txtaviso>(Ex: 01/03/1983 ou 01031983)</span>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>E-Mail:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					$texto="<input type=text name=matriz[email] size=40 value='$matriz[email]'> <span class=txtaviso>(Ex: joao@tdkom.com.br)</span>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Site:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					$texto="<input type=text name=matriz[site] size=40 value='$matriz[site]'  onBlur=verificaURL(this.value,12)> <span class=txtaviso>(Ex: http://www.tdkom.com.br)</span>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Contato:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					$texto="<input type=text name=matriz[contato] size=60 value='$matriz[contato]'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
			}
		}
		
		elseif($matriz[pessoaTipo]=='J') {
			$parametros=carregaParametrosConfig();
			
			if($matriz[cnpj] && ( $parametros[documento_unico] == 'N' || contaConsulta(buscaDocumentosPessoas($matriz[cnpj],'documento','igual','documento'))==0) ) {
				# Validar CPF
				$matriz[cnpj]=validaCNPJ($matriz[cnpj]);
			}
			novaLinhaTabela($corFundo, '100%');
				if(!$matriz[razao]) $matriz[razao]=$matriz[nome];
				itemLinhaTMNOURL('<b>Razão Social:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[razao] size=60 value='$matriz[razao]'>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
					
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>CNPJ:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[cnpj] size=18 value='$matriz[cnpj]' onBlur=javascript:submit()> <span class=txtaviso>(Ex: 12.469.253/0001-60 ou 12469253000160)</span>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			if(!$matriz[cnpj]) {
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit value='Confirmar' class=submit>";
					itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
			}

			$parametros=carregaParametrosConfig();
			
			if($matriz[cnpj] && ( $parametros[documento_unico] == 'N' || contaConsulta(buscaDocumentosPessoas($matriz[cnpj],'documento','igual','documento'))==0) ) {
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Inscrição Estadual:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					$texto="<input type=text name=matriz[ie] size=20 value='$matriz[ie]'> <span class=txtaviso>(Ex: 125.672.324 ou ISENTO)</span>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				#
				if($matriz['tipoPessoa'] == 'cli'){
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>Recolher ISSQN:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
						if( empty($matriz['recolherISS']))	$matriz['recolherISS']='N';
						$texto=formSelectSimNao($matriz['recolherISS'], 'recolherISS', 'form');
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
				#
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>E-Mail:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					$texto="<input type=text name=matriz[email] size=40 value='$matriz[email]'> <span class=txtaviso>(Ex: joao@tdkom.com.br)</span>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Site:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					$texto="<input type=text name=matriz[site] size=40 value='$matriz[site]'> <span class=txtaviso>(Ex: http://www.tdkom.com.br)</span>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Contato:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					$texto="<input type=text name=matriz[contato] size=60 value='$matriz[contato]'>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				
			}
		}

		fechaTabela();
	htmlFechaColuna();
	fechaLinhaTabela();

}



# formulário de dados cadastrais
function formPessoaEndereco($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;

	# Pessoa física
	novaLinhaTabela($corFundo, '100%');
	htmlAbreColuna('100%', 'right', $corFundo, 2, 'tabfundo1');
		novaTabela2("[Endereço]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);

		itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');

		if(!$matriz[tipoEndereco]) {
			# Selecionar tipo endereço
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Tipo de Endereço:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=submit name=matriz[bntSelecionar] value=Selecionar class=submit>";
				itemLinhaForm(formSelectTipoEndereco($matriz[tipoEndereco], 'tipoEndereco','form').$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		}
		else {
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>Tipo de Endereço:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=submit name=matriz[bntSelecionar] value=Selecionar class=submit>";
				itemLinhaForm(formSelectTipoEndereco($matriz[tipoEndereco], 'tipoEndereco','form').$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			# Demais campos do endereço
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>UF:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=submit name=matriz[bntSelecionar] value=Selecionar class=submit>";
				itemLinhaForm(formSelectUF($matriz[uf], 'uf','form').$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			if($matriz[uf]) {
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Cidade:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					$texto="<input type=submit name=matriz[bntSelecionar] value=Selecionar class=submit>";
					itemLinhaForm(formSelectCidade($matriz[cidade], $matriz[uf], 'cidade','form').$texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				
				if($matriz[cidade]) {
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>Logradouro (Rua, No.):</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
						$texto="<input type=text name=matriz[endereco] size=70 value='$matriz[endereco]'>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>Complemento:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
						$texto="<input type=text name=matriz[complemento] size=70 value='$matriz[complemento]'>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>Bairro:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
						$texto="<input type=text name=matriz[bairro] size=40 value='$matriz[bairro]'>";
						$texto.=" <b>País:</b> <input type=text name=matriz[pais] size=22 value='$matriz[pais]'> ";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>CEP:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
						if($matriz[pop]) {
							$indice=25;
						}
						elseif($matriz[tipoPessoa]=='ban') {
							$indice=18;
						}
						elseif($matriz[tipoPessoa]=='pop') {
							$indice=19;
						}
						else {
							$indice=24;
						}

						if($matriz['tipoPessoa'] != 'cli'||($matriz['tipoPessoa'] == 'cli' && $matriz['pessoaTipo'] != 'J' ) ){
							$indice--;//nao tem isssqn; 
						}
						$texto="<input type=text name=matriz[cep] size=10 value='$matriz[cep]' onBlur=verificaCEP(this.value,$indice)> ";
						$texto.=" <b>Caixa Postal:</b> <input type=text name=matriz[caixaPostal] size=10 value='$matriz[caixaPostal]'> ";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>DDD/Fone:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
						$texto="<input type=text name=matriz[ddd_fone1] size=3 value='$matriz[ddd_fone1]'> ";
						$texto.="<input type=text name=matriz[fone1] size=9 value='$matriz[fone1]'> ";
						$texto.="<b>DDD/Fone:</b> <input type=text name=matriz[ddd_fone2] size=3 value='$matriz[ddd_fone2]'> ";
						$texto.="<input type=text name=matriz[fone2] size=9 value='$matriz[fone2]'> ";
						$texto.=" <b>DDD/Fax:</b> <input type=text name=matriz[ddd_fax] size=3 value='$matriz[ddd_fax]'> ";
						$texto.="<input type=text name=matriz[fax] size=9 value='$matriz[fax]'> ";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>E-mail:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
						$texto="<input type=text name=matriz[email] size=60 value='$matriz[email]'> ";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				}
			}
			
		}

		fechaTabela();
	htmlFechaColuna();
	fechaLinhaTabela();


}

/**
 * Monta campo para inserção de instruções personalizadas no boleto
 * 
 * @param unknown_type $modulo
 * @param unknown_type $sub
 * @param unknown_type $acao
 * @param unknown_type $registro
 * @param unknown_type $matriz
 */
function formInstrucaoBoleto($modulo, $sub, $acao, $registro, $matriz) {
	global $corFundo, $corBorda;
	
	novaLinhaTabela($corFundo, '100%');
	htmlAbreColuna('100%', 'right', $corFundo, 2, 'tabfundo1');
		novaTabela2("[Instrução Personalizada - Boleto]<a name=ancora></a>", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
			
			novaLinhaTabela($corFundo, "100%");
				$texto = "<b>Instrução do Boleto:</b><br>(Max 4 linhas ou 280 caracteres)<br><FONT SIZE=1>Esta instrução será adicionada no boleto bancário ao gerar o faturamento.</FONT>";
				itemLinhaTMNOURL($texto, 'right', 'TOP', '30%', $corFundo, 0, 'tabfundo1');
				$texto = "<TEXTAREA name=matriz[instrucaoBoleto] rows=5 cols=50>$matriz[instrucaoBoleto]</TEXTAREA>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
			
			novaLinhaTabela($corFundo, "100%");
				$texto = "<center><input type=submit name=matriz[btnOk] value=Ok class=submit></center>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 2, 'tabfundo1');
			fechaLinhaTabela();
			
			itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
		
		fechaTabela();
	htmlFechaColuna();
	fechaLinhaTabela();
}

# Função para envio de dados e validação de form
function formPessoaSubmit($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	# Selecionar tipo endereço
	novaLinhaTabela($corFundo, '100%');
		itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
		$texto="<input type=submit name=matriz[bntConfirmar] value='Confirmar Dados'  class=submit>";
		itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
	fechaLinhaTabela();	

}



# função de forma para seleção de tipo de pessoas
/**
 * @return unknown
 * @param unknown $tipoPessoa
 * @param unknown $campo
 * @param unknown $tipo
 * @desc Form de seleção de Tipo de Pessoa (Cliente, Fornecedor, Prospect, etc)
*/
function formSelectTipoPessoa($tipoPessoa, $campo, $tipo, $noChange=true, $idTipoPessoa='', $todos=true) {

	if($tipo=='check') {
	
		$consulta=buscaTipoPessoas($tipoPessoa,'id','igual','descricao');
		
		if($consulta && contaConsulta($consulta)>0) {
		
			$retorno=resultadoSQL($consulta, 0, 'descricao');
		}
	
	}
	elseif($tipo=='form') {
	
		$consulta=buscaTipoPessoas('','','todos','descricao');
		
		if($consulta && contaConsulta($consulta)>0) {
			
			$retorno="<select name=matriz[$campo]";
			
			if(!$noChange)
				$retorno .= ' onChange=javascript:submit() ';
			
			$retorno .= ">";
			
			if ($todos)
				$retorno .= "<option value=*>Todos";
			
			for($i=0;$i<contaConsulta($consulta);$i++) {
			
				$id=resultadoSQL($consulta, $i, 'id');
				$descricao=resultadoSQL($consulta, $i, 'descricao');
				$valor=resultadoSQL($consulta, $i, 'valor');
				
				if($tipoPessoa==$valor) $opcSelect='selected';
				elseif(!empty($idTipoPessoa) && $idTipoPessoa ==  $id)  $opcSelect='selected';
				else $opcSelect='';
				
				
				$retorno.="<option value=$id $opcSelect>$descricao";
			}
			
			$retorno.="</select>";
		}
	
	}
	
	return($retorno);
	
}


# formulário de dados cadastrais - bancos
function formDadosBanco($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $sessCadastro;
	
	# Banco
	novaLinhaTabela($corFundo, '100%');
	htmlAbreColuna('100%', 'right', $corFundo, 2, 'tabfundo1');
		novaTabela2("[Dados Cadastrais do Banco]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);

		itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
		
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Nome do Banco:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			$texto="<input type=text name=matriz[nome] size=60 value='$matriz[nome]'>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Número do Banco:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			$texto="<input type=text name=matriz[numero] size=5 value='$matriz[numero]'>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>E-Mail:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			$texto="<input type=text name=matriz[email] size=40 value='$matriz[email]'> <span class=txtaviso>(Ex: joao@tdkom.com.br)</span>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Site:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
			$texto="<input type=text name=matriz[site] size=40 value='$matriz[site]' onBlur=verificaURL(this.value,7)> <span class=txtaviso>(Ex: http://www.tdkom.com.br)</span>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();

		fechaTabela();
	htmlFechaColuna();
	fechaLinhaTabela();

}

# formulário de dados cadastrais - pop
function formDadosPessoaPop($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $sessCadastro;
	
	# Banco
	novaLinhaTabela($corFundo, '100%');
	htmlAbreColuna('100%', 'right', $corFundo, 2, 'tabfundo1');
		novaTabela2("[Dados Cadastrais do Pop]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);

		itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');

			$parametros=carregaParametrosConfig();
			
//			if($matriz[cnpj] && ( $parametros[documento_unico] == 'N' || contaConsulta(buscaDocumentosPessoas($matriz[cnpj],'documento','igual','documento'))==0) ) {
//				# Validar CPF
//				$matriz[cnpj]=validaCNPJ($matriz[cnpj]);
//			}
			novaLinhaTabela($corFundo, '100%');
				if(!$matriz[razao]) $matriz[razao]=$matriz[nome];
				itemLinhaTMNOURL('<b>Razão Social:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[razao] size=60 value='$matriz[razao]'>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
					
			novaLinhaTabela($corFundo, '100%');
				itemLinhaTMNOURL('<b>CNPJ:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
				$texto="<input type=text name=matriz[cnpj] size=18 value='$matriz[cnpj]' onBlur=\"javascript:verificaCNPJ(this.value,7)\"> <span class=txtaviso>(Ex: 12.469.253/0001-60 ou 12469253000160)</span>";
				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
			fechaLinhaTabela();
		
//			novaLinhaTabela($corFundo, '100%');
//				itemLinhaTMNOURL('<b>E-Mail:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
//				$texto="<input type=text name=matriz[email] size=40 value='$matriz[email]'> <span class=txtaviso>(Ex: joao@tdkom.com.br)</span>";
//				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
//			fechaLinhaTabela();
//			novaLinhaTabela($corFundo, '100%');
//				itemLinhaTMNOURL('<b>Site:</b>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
//				$texto="<input type=text name=matriz[site] size=40 value='$matriz[site]' onBlur=verificaURL(this.value,9)> <span class=txtaviso>(Ex: http://www.tdkom.com.br)</span>";
//				itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
//			fechaLinhaTabela();

		fechaTabela();
	htmlFechaColuna();
	fechaLinhaTabela();

}


?>
