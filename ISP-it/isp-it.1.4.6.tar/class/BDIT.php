<?
################################################################################
#       Criado por: Rogério aka Popó
#  Data de criação: 11/01/2005
# Ultima alteração: 11/01/2005
#    Alteração No.: 001
#
# Função:
#    BD-IT - Classe para manipulação de Banco de Dados
################################################################################

Class BDIT{ // Inicia a classe BDIT

	var $sql='';
	var $conexao = '';
	var $tabela= '';
	
	
	function BDIT(){
		
	}
	
	/**
	* @return unknown
	* @param unknown $tabela
	* @param unknown $campos
	* @param unknown $condicao
	* @param unknown $agrupar
	* @param unknown $ordem
	* @param unknown $limiteId
	* @param unknown $limiteLen
	* @desc metodo que realiza uma consulta simples customizada ou não em determinada tabela
	*/
	function seleciona( $tabela, $campos = '*', $condicao = '', $agrupar = '', $ordem = '', $limiteId = '', $limiteLen = ''){

		$this->sql = "SELECT ";
		
		if ( !is_array( $campos ) ) $campos = array( $campos );
		
		$this->sql .= implode( ",", $campos );
					
		$this->sql .= " FROM ";
		
		if ( !is_array( $tabela ) ) $tabela = array( $tabela );
				
		$this->sql .= implode( ",", $tabela );
		
		// Inclui a cláusula WHERE na instrucao SQL
		if ( !is_null( $condicao ) && !empty( $condicao ) ){

			$this->sql .= " WHERE ";
			
			if ( !is_array( $condicao ) ) $condicao = array( $condicao );
			
			$this->sql .= implode( " AND ", $condicao );
			
		}
				
		// Inclui a cláusula GROUP na instrucao SQL
		if ( !is_null( $agrupar ) && !empty( $agrupar ) ){
			
			$this->sql .= " GROUP BY ";
			
			if ( !is_array( $agrupar ) ) $agrupar = array( $agrupar );
			
			$this->sql .= implode( ",", $agrupar );
			
		}
		
		// Inclui a cláusula ORDER na instrucao SQL
		if ( !is_null( $ordem ) && !empty( $ordem ) ){
			
			$this->sql .= " ORDER BY ";
			
			if ( !is_array( $ordem ) ) $ordem = array( $ordem);
			
			$this->sql .= implode( ",", $ordem );
			
		}
		
		// Inclui a cláusula LIMIT na instrucao SQL
		if ( ( !is_null( $limiteId ) && !is_null( $limiteLen ) ) && ( !empty( $limiteId ) && !empty( $limiteLen ) ) ){
			$this->sql .= " LIMIT ".$limiteId.",".$limiteLen;	
		}
		//chama o metodo executa() para executar a instrucao contida em $this->sql
		$consulta  = $this->executa( $this->sql, $this->conexao );
		$resultado = array();

		//Verifica se a variavel consulta nao esta vazia
		if ($consulta){
			while( $rows = mysql_fetch_object( $consulta ) ){
				$resultado[] = $rows;	
			}
		}
		
		return $resultado;
		
	} // fim do metodo seleciona() //
	
	function inserir( $tabela, $campos, $valores ){
		
		if( is_array( $campos ) ){
			if( count( $campos ) != count( $valores ) ){
				$titulo = "Erro - Inserir";
				$msg = "A quantidade de  campos definidos é diferentes dos valores especificados!";
			}
		}
		else if( is_null( $campos )	|| is_null( $valores ) || $campos == '' || $valores == '' ){
			$titulo = "Erro - Inserir";
			$msg = "O(s) campo(s) e/ou valor(es) definidos estão incorretos!";
		}
		
		if ( $msg != '' ){
			$this->erroSql( $titulo, $msg );
			return false;
		}
		else{
			$this->sql = "INSERT INTO ".$tabela." ( ";
			
			if( !is_array( $campos ) ) $campos = array( $campos );
			
			$this->sql .= implode( ",", $campos );
						
			$this->sql .= " ) VALUES (' ";
			
			if( !is_array( $valores ) ) $valores = array( $valores );
			
			$this->sql .= implode( "','", $valores );
			
			$this->sql .= " ')";
			
			return $this->executa( $this->sql, $this->conexao );
		}
				
	} // fim do metodo inserir() //
	
	function alterar( $tabela, $campos, $valores, $condicao ){
		if( is_array( $campos ) && is_array( $valores ) ){
			if( count( $campos ) != count( $valores ) ){
				$titulo = "Erro - Alterar";
				$msg = "A quantidade de  campos definidos é diferentes dos valores especificados!";
			}
		}
		elseif( empty( $campos )	|| empty( $valores ) || $campos == '' || $valores == ''  ){
			$titulo = "Erro - Alterar";
			$msg = "O(s) campo(s) e/ou valor(es) definidos estão incorretos!";
		}
		
		if ( $msg != '' ){
			$this->erroSql( $titulo, $msg );
			return false;
		}
		else{
			$this->sql = "UPDATE ".$tabela." SET ";
			
			if( !is_array( $campos ) ) $campos = array( $campos );
			
			if( !is_array( $valores ) ) $valores = array( $valores );
			
			for( $x = 0; $x < count( $campos ); $x++ ){
				if ( $campos[$x] != "id" ){ // muito incomum ou quase nulo atualizar o "id" de uma tabela, por isso naum incluimos no UPDATE
					if( $x < ( count( $campos ) - 1)  )
						$this->sql .= $campos[$x]." = '".$valores[$x]."', ";
					else 
						$this->sql .= $campos[$x]." = '".$valores[$x]."'";
				}
			}
			
			$this->sql .= " WHERE ";
			
			if( !is_array( $condicao ) ) $condicao = array( $condicao );
			
			$this->sql .= implode( " AND ", $condicao );
		
			$resultado = $this->executa( $this->sql, $this->conexao );

			return $resultado;
		}
	} // fim do metodo alterar() //
	
	function excluir( $tabela, $condicao ){
		if( is_null( $condicao ) ){
			$titulo = "Erro - Excluir";
			$msg = "É necessário definir uma condição pra excluir registros da tabela ".$tabela;
		}
		else{
			$this->sql = "DELETE FROM ".$tabela." WHERE ";
			
			if( !is_array( $condicao ) ) $condicao = array( $condicao );
			
			$this->sql .= implode( " AND ", $condicao);
		}
		$resultado = $this->executa( $this->sql, $this->conexao );
		
		if ( $msg != '' ){
			$this->erroSql( $titulo, $msg );
			return false;
		}
		else{
			return $resultado;
		}
	} // fim do metodo excluir() //
	
	function executa( $instrucao, $conexao ){
		$consultaSQL = mysql_query( $instrucao, $conexao );
		return $consultaSQL;
	} // fim do metodo executa() //
	
	function contaConsulta( $fonte ){
			$numRegistro = mysql_numrows( $fonte );
			return $numRegistro;
	}
	
	function erroSql( $titulo, $msg ){
		echo "
			<table width='100%' border='0' cellpadding='2' cellspacing='0'>
			  <th align='center'>$titulo</th>
			  <tr><td>
			  	<table width='100%' border='0' cellpadding='2' cellspacing='0'>
					<tr><td align='center'>$msg</td></tr>
				</table>
			  </td></tr>
			</table>
		";	
	}
	
	/// Getters e Setter \\\
	
	function setConnection( $_conn ){
		$this->conexao = $_conn;	
	}
	
	function getConnection(){
		return $this->conexao;
	}
	
} // fim da classe