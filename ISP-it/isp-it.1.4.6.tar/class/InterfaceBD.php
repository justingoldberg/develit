<?
################################################################################
#       Criado por: Rogйrio aka Popу
#  Data de criaзгo: 12/01/2005
# Ultima alteraзгo: 12/01/2005
#    Alteraзгo No.: 001
#
# Funзгo:
#    "Interfaceia" os metodos da classe BDIT 
################################################################################

Class InterfaceBD{ // inicio da Classe

	var $tabela;
	var $campos;
	
	var $objBD;
	
	function InterfaceBD(){
		$this->objBD = new BDIT();
		$this->objBD->BDIT();
	}
	
	/**
	* @return unknown
	* @param unknown $id
	* @param unknown $valores
	* @desc Metodo que Inclui e/ou Altera registros de uma tabela
PS:. Para id's auto_increment defina id = ''
	*/
	function salva( $coluna = '' ){
		if ( empty( $coluna ) )
			$cols = $this->campos;
		else
			$cols = $coluna;
		
		$valores = $this->getValores( $cols );
		
		if ( !empty( $this->id ) ){
			$resultado = $this->objBD->alterar( $this->tabela, $cols, $valores, array( "id = $this->id" ) );
		}
		else{
			$resultado = $this->objBD->inserir( $this->tabela, $cols, $valores );
		}
		
		return $resultado;
	}
	
	
	function getValores( $campos ){
		
		if ( !is_array( $campos ) ) $campos = array( $campos );
		
		foreach ( $campos as $att ){
			if ( $att != "id" )	
				$valores[] = $this->{$att};
			else
				$valores[] = "''";
		}
		return $valores;
	}
	
	/**
	* @return void
	* @param unknown $id
	* @desc Metodo que excluir um registro de uma tabela especificado pelo id
	*/
	function exclui(){

			return $this->objBD->excluir( $this->tabela, array( "id = $this->id" ) );
	}
	
	function  excluiRelacionamento( $tabela, $condicao ){
		
		return $this->objBD->excluir( $tabela, $condicao );	
		
	}
	/**
	* @return unknown
	* @param unknown $idNF
	* @desc Metodo que executa uma seleзгo simples, porйm customizбvel, em uma tabela
	*/
	function seleciona( $tabela = '', $campos = '', $condicao = '', $agrupar = '', $ordem = '', $limiteId = '', $limiteLen = '' ){
		if ( is_null( $tabela ) || $tabela == '' ) $tabela = $this->tabela;
		if ( is_null( $campos ) || $campos == '' ) $campos = $this->campos;
		
		$resultado = $this->objBD->seleciona($tabela, $campos, $condicao, $agrupar, $ordem, $limiteId, $limiteLen );
			
		return $resultado;
	}
	
	function getMaxField( $cond = ""){
	
		if ( !isset( $this->sqlMax ) ){
			$resultado = $this->objBD->seleciona( $this->tabela, array( "Max(id) id" ), $cond );
		}
		elseif ( !empty( $this->sqlMax ) ){
			$resultado = $this->objBD->seleciona( $this->tabela, array( "Max($this->sqlMax) $this->sqlMax" ), $cond );
		}
		return $resultado;	
	}
	
	function setConnection( $_conn ){
		$this->objBD->setConnection( $_conn );
	}
	
	function getConnection(){
		return  $this->objBD->getConnection();	
		//return $this->objBD->conexao;
	}
	
} // fim da classe

?>