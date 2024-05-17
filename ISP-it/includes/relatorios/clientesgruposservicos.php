<?
################################################################################
#       Criado por: Hugo Ribeiro - Devel-it
#  Data de criação: 12/08/2004
# Ultima alteração: 12/08/2004
#    Alteração No.: 001
#
# Função:
# 		Funções para relatórios personalizado de clientes
#

function formRelatorioClientesGruposServicos($modulo, $sub, $acao, $registro, $matriz) {

	global $corFundo, $corBorda, $sessLogin;
	
	# Permissão do usuario
	$permissao=buscaPermissaoUsuario($sessLogin[login],'login','igual','login');
	
	$data=dataSistema();
	$titulo="Clientes - Relatório por Grupos de Serviços";
	
	# Motrar tabela de busca
	novaTabela2("[$titulo]", "center", '100%', 0, 2, 1, $corFundo, $corBorda, 2);
		# Opcoes Adicionais
		menuOpcAdicional($modulo, $sub, $acao, $registro);
		#fim das opcoes adicionais
		novaLinhaTabela($corFundo, '100%');
		$texto="			
			<form method=post name=matriz action=index.php>
			<input type=hidden name=modulo value=$modulo>
			<input type=hidden name=sub value=$sub>
			<input type=hidden name=acao value=$acao>
			<input type=hidden name=registro value=$registro>&nbsp;";
			itemLinhaNOURL($texto, 'left', $corFundo, 2, 'tabfundo1');
		fechaLinhaTabela();
		//POP
		getPop($matriz);
		// combo Grupo Servico;
		getComboGrupoServico($matriz);
		// clientes
		getComboCamposClientes();
		//Botao
		getBotoesConsRel();
		
		htmlFechaLinha();
	fechaTabela();
	
}


#
# faz a consulta e o relatorio
#
function relatorioClientesGruposServicos($modulo, $sub, $acao, $registro, $matriz) {

	global $conn, $corFundo, $corBorda, $html, $tb, $retornaHtml, $sessLogin, $conn, $tb;
	$titulo="Clientes - Relatório por Grupos de Serviços";
	
	if (is_array($matriz[pop]) && $matriz[idGrupos]) {
			
		// Se forem todos os campos gera a lista na matriz
		$campos=($matriz[campos_todos] ? $campos=getCamposClientes() : $campos=$matriz[campos]);
		$campos[]="Servicos.nome=Serviço";
		
		// Se forem todos os pops gera a lista na matriz
		if($matriz[pop_todos]) {
			$consultaPop=buscaPOP('','','todos', 'id');
			if( $consultaPop && contaconsulta($consultaPop) ) {
				for($a=0;$a<contaConsulta($consultaPop);$a++) {
					$matriz[pop][$a]=resultadoSQL($consultaPop, $a, 'id');
				}
			}
		}
				
		$matriz[campos]="";
		$select=array();
		$largura=array();
		$matCabecalho=array();
		$matAlinhamento=array();
		$pos=0;
		
		foreach ($campos as $coluna) {
			$partes=explode("=", $coluna);
			$matriz[campos][]=stripslashes($partes[0])." ".$partes[1];
			$matCabecalho[]=$partes[1];
			if(!$pos++) $order=$partes[0];
		}
		
		
		$select=implode(", ", $matriz[campos]);
		$numCol=count($matCabecalho);
		
		// pega os grupos
		$sqlGRUPO=getSQLGrupoServicos($matriz);
		
		$pp=0;
		while($matriz[pop][$pp]) {	
			// nome do pop para exbição
			$nomePop=resultadoSQL(buscaPOP($matriz[pop][$pp], 'id', igual, 'nome'), 0, 'nome');
			$sqlPOP=" AND $tb[Pessoas].idPOP = ".$matriz[pop][$pp];
			
			
			$sql="select distinct 	$select
			   		from 	Pessoas, 
			   				PessoasTipos, 
			   				PlanosPessoas, 
					   		TipoPessoa, 
							Enderecos, 
							Cidades, 
					   		ServicosPlanos, 
					   		Servicos, 
					   		ServicosGrupos, 
					   		GruposServicos 
					  where Pessoas.id=PessoasTipos.idPessoa 
					  		AND PessoasTipos.id=PlanosPessoas.idPessoaTipo 
					  		AND Enderecos.idPessoaTipo=PessoasTipos.id
					  		AND TipoPessoa.valor='cli'
					  		AND Cidades.id=Enderecos.idCidade
					  		AND PlanosPessoas.id=ServicosPlanos.idPlano 
					  		AND Servicos.id=ServicosPlanos.idServico 
					  		AND ServicosGrupos.idServico=Servicos.id 
					  		AND GruposServicos.id=ServicosGrupos.idGrupos
					  		$sqlPOP
							$sqlGRUPO
				   order by $order";
			
			$consultaPop=consultaSQL($sql, $conn);
			
			if( $consultaPop && contaconsulta($consultaPop) ) {
				# Cabeçalho
				echo "<br>";
				novaTabela($nomePop." ".$periodo, "left", '100%', 0, 2, 1, $corFundo, $corBorda, $numCol);
				
				# se for consulta exibe o cabecalho
				if ($matriz[bntConfirmar]) {
					$cor='tabfundo0';
					htmlAbreLinha($cor);
						for ($cc=0;$cc<count($matCabecalho);$cc++) 
							itemLinhaTMNOURL($matCabecalho[$cc], $matAlinhamento[$cc], 'middle', $largura[$cc], $corFundo, 0, $cor);					
					htmlFechaLinha();
				}
				
				$matResultado=array();
				$l=0;
				
				#inicia a varredura e joga 
				for($a=0;$a<contaConsulta($consultaPop);$a++) {
					
					$cc=0;
					foreach ($matCabecalho as $mc)
						$campos[$cc++]=resultadoSQL($consultaPop, $a, $mc);
					
					# se for consulta exibe a linha detalhe
					if ($matriz[bntConfirmar]) {
						htmlAbreLinha($corFundo);
							for ($cc=0; $cc<count($campos); $cc++) {
								itemLinhaTMNOURL($campos[$cc], $matAlinhamento[$cc], 'middle', $lagura[$cc], $corFundo, 0, "normal9");
							}
						htmlFechaLinha();
					}
					# soma na matriz
					for ($cc=0; $cc<count($campos); $cc++) {
						$matResultado[$matCabecalho[$cc]][$l]=$campos[$cc];
					}
					$l++;
				}
			} 
			else {
				$vazio = 1;
			}
			if (! $vazio) {
				# Alimentar Matriz Geral
				$matrizRelatorio[detalhe]=$matResultado;
				
				# Alimentar Matriz de Header
				$matrizRelatorio[header][TITULO]=$titulo;
				$matrizRelatorio[header][POP]=$nomePop.'<br>'.$periodo;
				$matrizRelatorio[header][IMG_LOGO]=$html[imagem][logoRelatorio];
				
				# Configurações
				$matrizRelatorio[config][linhas]=25;
				$matrizRelatorio[config][layout]='landscape';
				$matrizRelatorio[config][marginleft]='1.0cm;';
				$matrizRelatorio[config][marginright]='1.0cm;';
				
				$matrizGrupo[]=$matrizRelatorio;
				
			}
			$pp++;
		} // while
		
		if(! $vazio) {
			
			#Se for escolhido Consulta nao gera o pdf
			if (! $matriz[bntConfirmar]) {
				#nome do arquivo
				$nome="clientescustom";
				criaTemplates($nome, $matCabecalho);			
				# Converter para PDF:
				$arquivo=k_reportHTML2PDF(k_report($matrizGrupo, 'html',$nome),$nome,$matrizRelatorio[config]);
				if ($arquivo) {
					echo "<br>";
					novaTabela('Arquivos Gerados<a name=ancora></a>', "center", '100%', 0, 2, 1, $corFundo, $corBorda, 1);
						htmlAbreLinha($corfundo);
							itemTabelaNOURL(htmlMontaOpcao("<a href=$arquivo>$titulo</a>",'pdf'), 'center', $corFundo, 7, 'txtaviso');
						htmlFechaLinha();
					fechaTabela();
				}
			}
		}
		return(0);
	} else {
		echo "<br>";
		$msg="Você esqueceu de selecionar o POP ou o GRUPO.";
		avisoNOURL("Aviso: Relatório<a name=ancora></a>", $msg, 400);
	}	
	
	echo "<script>location.href='#ancora';</script>";
}

?>

