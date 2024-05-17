<?
	################################################################################
#       Criado por: Felipe dos S. Assis
#  Data de criação: 11/10/2006
# Ultima alteração: 11/10/2006
#    Alteração No.: 000
#
# Função:
#    Painel - Funções para gerenciamento de Itens de Movimento de Estoque
	#################################################################################
	
	function dbImpostosNF($matriz, $tipo, $subtipo = '', $condicao = '', $ordem = ''){
		
		global $conn, $tb;
		$bd = new BDIT();
		$bd->setConnection($conn);
		$tabela = $tb['ImpostosNF'];
		$campos = array('id','idNF','descricao', 'porcentagem');
		$valores = array('NULL', $matriz['idNF'], $matriz['descricao'], $matriz['porcentagem']);
		
		if($tipo == 'inserir'){
			### Modificar Aqui ###
			/*
				Consultando os impostos necessários para serem inclusos na Nota Fiscal
				caso o valor da mesma seja superior à R$ 5000,00
			*/
			#obtendo impostos selecionados pelo usuário
			$impostos = listarImpostosNF($matriz['id']);
			
			for($i = 0; $i < count($matriz['impostosNF']); $i ++){
				$dadosImpostos = mysql_fetch_array($impostos);
				$sql[$i] = "INSERT INTO $tb[ImpostosNF] ($tb[ImpostosNF].idNF, 
							$tb[ImpostosNF].descricao, $tb[ImpostosNF].porcentagem) 
							VALUES ($matriz[id], 'Desconto do ".$matriz['impostosNF'][$i]." (".formatarValoresForm($dadosImpostos[valor])." %)', 
							$dadosImpostos[valor])";
				mysql_query($sql[$i], $conn);
			}
		}
		elseif($tipo == 'consultar'){
			$sql = "SELECT $tb[ImpostosNF].descricao, $tb[ImpostosNF].porcentagem 
					FROM $tb[ImpostosNF] WHERE $tb[ImpostosNF].idNF = $matriz[idNF]";
			$retorno = consultaSQL($sql, $conn);
		}
		elseif($tipo == 'consultarDescricaoValor'){
			$sql = "SELECT $tb[ImpostosNF].descricao, $tb[ImpostosNF].porcentagem FROM
					$tb[ImpostosNF] WHERE $tb[ImpostosNF].idNF = $matriz[id]";
			$retorno = consultaSQL($sql, $conn);
		}
		elseif($tipo == 'pesquisarImpostos'){
			$registro = $matriz['id'];
			$sql = "SELECT * FROM $tb[ImpostosNF] WHERE $tb[ImpostosNF].idNF = $registro";
			$retorno = mysql_query($sql, $conn);
		}
		elseif($tipo == 'alterar'){
			array_shift($campos); //retira o campo id da lista de campos
			arary_shift($campos); //retira o campo id da lista dos valores
			
			$retorno = $bd->alterar($tabela, $campos, $valores);
		}
		elseif($tipo == 'excluir'){
			$registro = $matriz['id'];
			$condicao = 'idNF = '.$registro;
			$retorno = $bd->excluir($tabela, $condicao);
		}
		return($retorno);
	}
?>