<?
################################################################################
#       Criado por: José Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 15/04/2004
# Ultima alteração: 15/04/2004
#    Alteração No.: 001
#
# Função:
#      Includes de Consultas


/**
 * @return void
 * @param string $modulo
 * @param string $sub
 * @param string $acao
 * @param int  $registro
 * @param array $matriz
 * @desc Form para parametros de consulta de Dominios por cliente
*/
function formConsultaDominioParceiro($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda;
	
	# Motrar tabela de busca
	novaTabela2("[Consulta Domínios por Parceiro]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		# Opcoes Adicionais
		menuOpcAdicional($modulo, $sub, $acao, $registro);
		#fim das opcoes adicionais
		novaLinhaTabela($corFundo, '100%');
		$texto="			
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=registro>";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		itemTabelaNOURL('&nbsp;', 'center', $corFundo, 2, 'tabfundo1');
		
		novaLinhaTabela($corFundo, '100%');
			itemLinhaTMNOURL('<b>Busca por Cliente:</b><br>
			<span class=normal10>Informe nome ou dados do cliente para busca</span>', 'right', 'middle', '35%', $corFundo, 0, 'tabfundo1');
			$texto="<input type=text name=matriz[txtProcurar] size=60 value='$matriz[txtProcurar]'> <input type=submit value=Procurar name=matriz[bntProcurar] class=submit>";
			itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
		fechaLinhaTabela();
		if($matriz[txtProcurar]) {
			# Procurar Cliente
			$tipoPessoa=checkTipoPessoa('cli');
			$consulta=buscaPessoas("
				((upper(nome) like '%$matriz[txtProcurar]%' 
					OR upper(razao) like '%$matriz[txtProcurar]%' 
					OR upper(site) like '%$matriz[txtProcurar]%' 
					OR upper(mail) like '%$matriz[txtProcurar]%')) 
				AND idTipo=$tipoPessoa[id]", $campo, 'custom','nome');
			
			if($consulta && contaConsulta($consulta)>0) {
				# Selecionar cliente
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Clientes encontrados:</b><br>
					<span class=normal10>Selecione o Cliente</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					itemLinhaForm(formSelectConsulta($consulta, 'nome', 'idPessoaTipo', 'idPessoaTipo', $matriz[idPessoaTipo]), 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				
					novaLinhaTabela($corFundo, '100%');
						itemLinhaTMNOURL('<b>Todos Serviços:</b><br>
						<span class=normal10>Exibir todos Serviços</span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
						if($matriz[todos_servicos]) $opcDetalhar='checked';
						$texto="<input type=checkbox name=matriz[todos_servicos] value='S' $opcDetalhar>";
						itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
					fechaLinhaTabela();
				
				//Periodo
				$data=dataSistema();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Mês/Ano Inicial:</b><br><span class=normal10>Informe o mês/ano inicial </span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					$texto="<input type=text name=matriz[dtInicial] size=7 value='$matriz[dtInicial]' onBlur=verificaDataMesAno2(this.value,8)>&nbsp;<span class=txtaviso>(Formato: $data[mes]/$data[ano])</span>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				novaLinhaTabela($corFundo, '100%');
					itemLinhaTMNOURL('<b>Mês/Ano Final:</b><br><span class=normal10>Informe o mês/ano final </span>', 'right', 'middle', '30%', $corFundo, 0, 'tabfundo1');
					$texto="<input type=text name=matriz[dtFinal] size=7 value='$matriz[dtFinal]'  onBlur=verificaDataMesAno2(this.value,9)>&nbsp;<span class=txtaviso>(Formato: $data[mes]/$data[ano])</span>";
					itemLinhaForm($texto, 'left', 'top', $corFundo, 0, 'tabfundo1');
				fechaLinhaTabela();
				
				novaLinhaTabela($corFundo, '100%');
					$texto="<input type=submit name=matriz[bntConfirmar] value='Consultar' class=submit>";
					$texto.="&nbsp;&nbsp;";
					$texto.="<input type=submit name=matriz[bntRelatorio] value='Relatório' class=submit2>";
					itemLinhaForm($texto, 'center', 'top', $corFundo, 2, 'tabfundo1');
				fechaLinhaTabela();
			}
		}
	
		htmlFechaLinha();
	fechaTabela();
	
}


/**
 * @return void
 * @param string $modulo
 * @param string $sub
 * @param string $acao
 * @param int $registro
 * @param array $matriz
 * @desc Consulta de Dominios por cliente
*/
function consultaDominioParceiro($modulo, $sub, $acao, $registro, $matriz) {

	
	global $corFundo, $corBorda, $html, $sessLogin, $conn, $tb, $limites;

	# Formatar Datas
	if ($matriz[dtInicial]) {
		$matriz[dtInicial]=formatarData($matriz[dtInicial]);
		if ($matriz[diaDe]) $dia=$matriz[diaDe];
		else $dia='01';
		$dtInicial=substr($matriz[dtInicial],2,4)."/".substr($matriz[dtInicial],0,2).'/'.$dia.' 00:00:00';
		$matriz[dtInicial]=substr($matriz[dtInicial],0,2)."/".substr($matriz[dtInicial],2,4);
	}

	if ($matriz[dtFinal]) {
		$matriz[dtFinal]=formatarData($matriz[dtFinal]);
		if ($matriz[diaAte]) $dia=$matriz[diaAte];
		else $dia=dataDiasMes(substr($matriz[dtFinal],0,2));
		$dtFinal=substr($matriz[dtFinal],2,4)."/".substr($matriz[dtFinal],0,2).'/'.$dia.' 23:59:59';
		$matriz[dtFinal]=substr($matriz[dtFinal],0,2)."/".substr($matriz[dtFinal],2,4);
	}
	
	// Ajusta o sql para determinar o periodo escolhido
	$sqlDT="";
	if($matriz[dtInicial] && $matriz[dtFinal]) {
		$sqlDT=" AND $tb[PlanosDocumentosGerados].dtVencimento between '$dtInicial' and '$dtFinal' ";
		$periodo="de ".$matriz[dtInicial]." até ".$matriz[dtFinal];
	} 
	elseif ($matriz[dtInicial]) {
		$sqlDT=" AND $tb[PlanosDocumentosGerados].dtVencimento >= '$dtInicial' ";
		$periodo="a partir de ".$matriz[dtInicial];
	} 
	elseif ($matriz[dtFinal])  {
		$sqlDT=" AND $tb[PlanosDocumentosGerados].dtVencimento <= '$dtFinal' ";
		$periodo="até ".$matriz[dtFinal];
	}
	($matriz[todos_servicos] != "S" ? $lig=" INNER " : $lig = " LEFT ");
	# SQL para consulta de emails por dominios do cliente informado
/*	$sql="
		SELECT DISTINCT 
			$tb[Dominios].id id, 
			$tb[Dominios].nome nome, 
			$tb[Dominios].status status, 
			$tb[Dominios].dtCadastro cadastro,
			$tb[Dominios].dtAtivacao ativacao,
			$tb[DominiosServicosPlanos].idPessoasTipos idPessoaTipo,
			$tb[DominiosServicosPlanos].idServicosPlanos idServicoPlano,
			$tb[Servicos].nome nomeServico,
			$tb[ServicosPlanos].valor valorPlano,
			$tb[ServicosPlanos].id idServicoPlano,
			$tb[ServicosPlanosDocumentosGerados].valor valorFaturado,
			$tb[PlanosDocumentosGerados].dtVencimento as vencimento
		FROM
			$tb[Dominios], 
			$tb[DominiosServicosPlanos],
			$tb[ServicosPlanos],
			$tb[Servicos],
			$tb[ServicosPlanosDocumentosGerados],
			$tb[PlanosDocumentosGerados]
		WHERE 
			$tb[Dominios].id = $tb[DominiosServicosPlanos].idDominio 
			AND $tb[ServicosPlanos].id = $tb[DominiosServicosPlanos].idServicosPlanos
			AND $tb[ServicosPlanos].idServico = $tb[Servicos].id
			AND $tb[ServicosPlanosDocumentosGerados].idServicosPlanos = $tb[ServicosPlanos].id
			AND $tb[PlanosDocumentosGerados].id = $tb[ServicosPlanosDocumentosGerados].idPlanoDocumentoGerado
			AND $tb[DominiosServicosPlanos].idPessoasTipos = $matriz[idPessoaTipo]
			$sqlDT 
		ORDER BY
			$tb[Dominios].nome, $tb[PlanosDocumentosGerados].dtVencimento
	";*/
	$sql = "SELECT DISTINCT 
	$tb[ServicosPlanos].id idServicoPlano,
	$tb[ServicosPlanos].valor valorPlano,
	$tb[ServicosPlanos].dtAtivacao ativacaoServico,  
	$tb[Servicos].nome nomeServico, 
	$tb[ServicosPlanosDocumentosGerados].valor valorFaturado,
	$tb[PlanosDocumentosGerados].dtVencimento AS vencimento, 
	$tb[DominiosServicosPlanos].idPessoasTipos idPessoaTipo,
	$tb[DominiosServicosPlanos].idServicosPlanos idServicoPlano, 
	$tb[Dominios].id id,
	$tb[Dominios].nome nome, 
	$tb[Dominios].status status,
	$tb[Dominios].dtCadastro cadastro, 
	$tb[Dominios].dtAtivacao ativacao
FROM
	$tb[PlanosPessoas]
	INNER JOIN $tb[ServicosPlanos]
		On($tb[PlanosPessoas].id = $tb[ServicosPlanos].idPlano) 
	INNER JOIN $tb[Servicos]
		On($tb[ServicosPlanos].idServico = $tb[Servicos].id)
	INNER JOIN $tb[ServicosPlanosDocumentosGerados]
		On($tb[ServicosPlanos].id = $tb[ServicosPlanosDocumentosGerados].idServicosPlanos)
	INNER JOIN $tb[PlanosDocumentosGerados]
		On($tb[ServicosPlanosDocumentosGerados].idPlanoDocumentoGerado = $tb[PlanosDocumentosGerados].id)
	INNER JOIN ContasAReceber
		On($tb[PlanosDocumentosGerados].idDocumentoGerado = ContasAReceber.idDocumentosGerados)
	$lig JOIN $tb[DominiosServicosPlanos]
		On($tb[ServicosPlanos].id = $tb[DominiosServicosPlanos].idServicosPlanos)
	$lig JOIN $tb[Dominios]
		On($tb[DominiosServicosPlanos].idDominio = $tb[Dominios].id)
WHERE
		$tb[PlanosPessoas].idPessoaTipo = $matriz[idPessoaTipo]
		$sqlDT
	ORDER BY
	$tb[Dominios].nome, $tb[PlanosDocumentosGerados].dtVencimento";
	
	
	$consulta=consultaSQL($sql, $conn);
	
	$l=1;
	$matResultado=array();
	$matCabecalho=array('Dominio', 'Serviço', 'Cadastro', 'Valor Plano', 'Vencimento', 'Valor Faturado', 'Status');
	$gravata=$matCabecalho;
	$gravata[]='Opções';
	$largura=array(     '10%',     '25%',     '12%',      '10%',         '12%',        '10%',            '5%',     '16%');
	$alinhar=array(     'left',    'left',    'center',   'right',       'center',     'right',          'center', 'left');
	
	$qtdItem=count($gravata);
	
	echo "<br>";
	
	# Mostrar Cliente
	htmlAbreLinha($corFundo);
		htmlAbreColunaForm('100%', 'center', 'middle', $corFundo, 1, 'normal10');
			novaTabela2("[ Resultados $periodo]<a name=ancora></a>", "center", '100%', 0, 1, 1, $corFundo, $corBorda, $qtdItem);
			if ( ! $matriz[bntRelatorio] ) {
				# Opcoes Adicionais
				novaLinhaTabela($corFundo, '100%');
					htmlAbreColuna('100%', 'left', $corFundo, $qtdItem, 'tabfundo1');
						novaTabelaSH("center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
							menuOpcAdicional('lancamentos', 'planos', 'listar', $matriz[idPessoaTipo]);
						fechaTabela();
					htmlFechaColuna();
				fechaLinhaTabela();
			}
			if(!$consulta || contaConsulta($consulta)==0 ) {
				# Não há registros
				itemTabelaNOURL('Não foram encontrados domínios cadastrados', 'left', $corFundo, $qtdItem, 'txtaviso');
			}
			
			elseif($consulta && contaConsulta($consulta)>0 && (!$registro || is_numeric($registro)) ) {	
				
				if ( ! $matriz[bntRelatorio]) {
					# Cabeçalho
					novaLinhaTabela($corFundo, '100%');
						for ($i=0; $i<$qtdItem;$i++)
							itemLinhaTabela($gravata[$i],  $alinhar[$i], $largura[$i], 'tabfundo0');
					fechaLinhaTabela();
				}
				
				$dominio="";
				$qtdDominios=0;
				$valortt=0;
				
				for($i=0;$i<contaConsulta($consulta);$i++) {
					# Mostrar registro
					$id=resultadoSQL($consulta, $i, 'id');
					$nome=resultadoSQL($consulta, $i, 'nome');
					$nomeServico=resultadoSQL($consulta, $i, 'nomeServico');
					$idPessoaTipo=resultadoSQL($consulta, $i, 'idPessoaTipo');
					$idServicoPlano=resultadoSQL($consulta, $i, 'idServicoPlano');
					$status=resultadoSQL($consulta, $i, 'status');
					$cadastro=converteData(resultadoSQL($consulta, $i, 'cadastro'), 'banco', 'formdata');
					$ativacao=converteData(resultadoSQL($consulta, $i, 'ativacao'), 'banco', 'formdata');
					$valorPlano=formatarValoresForm(resultadoSQL($consulta, $i, 'valorPlano'));
					$valorFaturado=formatarValoresForm(resultadoSQL($consulta, $i, 'valorFaturado'));
					$vencimento=converteData(resultadoSQL($consulta, $i, 'vencimento'), 'banco', 'formdata');
					$idServicoPlano=resultadoSQL($consulta, $i, 'idServicoPlano');
					$ativacaoServico=converteData(resultadoSQL($consulta, $i, 'ativacaoServico'), 'banco', 'formdata');
					
					$valorft+=resultadoSQL($consulta, $i, 'valorPlano');
					$valortt+=resultadoSQL($consulta, $i, 'valorFaturado');
					
					$matriz[id]=$id;
					
					# Transferência de dominios entre serviços planos / pessoas tipos
					$def="<a href=?modulo=administracao&sub=dominio&acao=alterar";
					$opcoes=htmlMontaOpcao($def."&registro=$idPessoaTipo:$id>Alterar</a>",'alterar');			
					
					if (empty($ativacao))   $ativacao = $ativacaoServico;
					
					if (empty($status))		$status = "sem dominio";  
					
					if ($dominio==$nome && !empty($dominio) && !empty($nome)) {
						$nome='';
						$cadastro='';
						$valorPlano='';
						$nomeServico='';
						$opcoes='';
						$cor = 'normal9';
					} else {
						$dominio=$nome;
						$cor = 'tabfundo81';
						$qtdDominios++;
					}
					
					if ( ! $matriz[bntRelatorio]) {
						novaLinhaTabela($corFundo, '100%');
							$mod=$i % 2;
							if ($mod) $cor='tabfundo1';
							else $cor="normal10";
							
							$cc=0;
							itemLinhaTabela("<b>$nome</b>", $alinhar[$cc], $largura[$cc++], $cor);
							itemLinhaTabela($nomeServico,   $alinhar[$cc], $largura[$cc++], $cor);
							itemLinhaTabela($ativacao,      $alinhar[$cc], $largura[$cc++], $cor);
							itemLinhaTabela($valorPlano,    $alinhar[$cc], $largura[$cc++], $cor);
							itemLinhaTabela($vencimento,    $alinhar[$cc], $largura[$cc++], $cor);
							itemLinhaTabela("<b>$valorFaturado</b>", $alinhar[$cc], $largura[$cc++], $cor);
							itemLinhaTabela(formSelectStatusDominios($status, "", "check"),    $alinhar[$cc], $largura[$cc++], $cor);
							itemLinhaTabela($opcoes,        $alinhar[$cc], $largura[$cc++], $cor);
						fechaLinhaTabela();
					}
					$cc=0;
					$matResultado[$matCabecalho[$cc++]][$l]="<b>".$nome."</b>";
					$matResultado[$matCabecalho[$cc++]][$l]=$nomeServico;
					$matResultado[$matCabecalho[$cc++]][$l]=$ativacao;
					$matResultado[$matCabecalho[$cc++]][$l]=$valorPlano;
					$matResultado[$matCabecalho[$cc++]][$l]=$vencimento;
					$matResultado[$matCabecalho[$cc++]][$l]="<b>$valorFaturado</b>";
					$matResultado[$matCabecalho[$cc++]][$l]=$status;
					$l++;
					
				} #fecha laco da linha detalhe
				
				$nome=dadosPessoasTipos($matriz[idPessoaTipo]);
				$nome=$nome[pessoa][nome];
				$txt="Total de domínios: ";

				if ($matriz[bntRelatorio]) {
					
					for($a=0; $a<$qtdItem-1; $a++)
						$matResultado[$matCabecalho[$a]][$l]="&nbsp;"; 
					$l++;
					
					$c=0;
					$matResultado[$matCabecalho[$c++]][$l]="<b>$txt</b>";
					$matResultado[$matCabecalho[$c++]][$l]="<b>$qtdDominios</b>";
					$matResultado[$matCabecalho[$c++]][$l]="<b>Totais</b>";
					$matResultado[$matCabecalho[$c++]][$l]="<b>".formatarValoresForm($valorft)."</b>";
					$matResultado[$matCabecalho[$c++]][$l]="&nbsp;";
					$matResultado[$matCabecalho[$c++]][$l]="<b>".formatarValoresForm($valortt)."</b>";
					$matResultado[$matCabecalho[$c++]][$l]="&nbsp;";
					$l++;
					
					for($a=0; $a<$qtdItem-1; $a++)
						$matResultado[$matCabecalho[$a]][$l]="&nbsp;"; 
					
					# Alimentar Matriz Geral
					$matrizRelatorio[detalhe]=$matResultado;
					
					# Alimentar Matriz de Header
					$matrizRelatorio[header][TITULO]="Domínios por Parceiro";
					$matrizRelatorio[header][POP]=$nome.'<br>'.$periodo;
					$matrizRelatorio[header][IMG_LOGO]=$html[imagem][logoRelatorio];
					
					# Configurações
					$matrizRelatorio[config][linhas]=38;
					$matrizRelatorio[config][layout]='portrait';
					$matrizRelatorio[config][marginleft]='1.0cm;';
					$matrizRelatorio[config][marginright]='1.0cm;';
					
					$matrizGrupo[]=$matrizRelatorio;
					
					
					# Converter para PDF:
					$arquivo=k_reportHTML2PDF(k_report($matrizGrupo, 'html','dominiosparceiro'),'dominiosparceiro',$matrizRelatorio[config]);
						
					if ($arquivo) {
						
						novaTabela('Arquivo Gerado<a name=ancora></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
							htmlAbreLinha($corfundo);
								itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo target='_blank'>Relatório de Domínios por Parceiros</a>",'pdf'), 'center', $corFundo, 0, 'txtaviso');
							htmlFechaLinha();
						fechaTabela();
					}
				} else {
					if ( ! $matriz[bntRelatorio]) {
						novaLinhaTabela($corFundo, '100%');
							$cor='tabfundo0';
							$cc=0;
							itemLinhaTabela("&nbsp;", 		$alinhar[$cc], $largura[$cc++], $cor);
							itemLinhaTabela("&nbsp;",       $alinhar[$cc], $largura[$cc++], $cor);
							itemLinhaTabela("<b>Totais</b>","center", $largura[$cc++], $cor);
							itemLinhaTabela("<b>".formatarValoresForm($valorft)."</b>",    $alinhar[$cc], $largura[$cc++], $cor);
							itemLinhaTabela("&nbsp;",    	$alinhar[$cc], $largura[$cc++], $cor);
							itemLinhaTabela("<b>".formatarValoresForm($valortt)."</b>", $alinhar[$cc], $largura[$cc++], $cor);
							itemLinhaTabela("&nbsp;",       $alinhar[$cc], $largura[$cc++], $cor);
							itemLinhaTabela("&nbsp;",       $alinhar[$cc], $largura[$cc++], $cor);
						fechaLinhaTabela();
						
						itemTabelaNOURL($txt.$qtdDominios, 'left', $corFundo, $qtdItem, 'txtaviso');
					}
				}
			} #fecha listagem
				
			fechaTabela();
		htmlFechaColuna();
	htmlFechaLinha();	
}


?>