<?
################################################################################
#       Criado por: Rogйrio aka Popу
#  Data de criaзгo: 12/01/2005
# Ultima alteraзгo: 12/01/2005
#    Alteraзгo No.: 001
#
# Funзгo:
#    Item Nota Fiscal - Classe para inclusao de Itens а NF
################################################################################

Class ItemNF extends InterfaceBD{ // inicio da Classe

	var $tabela = 'ItensNF';
	var $campos = array( "id", "idNF", "qtde", "unid", "descricao", "valorUnit" );

	// Att da classe
	var $id;
	var $idNF;
	var $qtde;
	var $unid;
	var $descricao;
	var $valorUnit;
	
	function ItemNF(){
		$this->InterfaceBD();
	}
	
	function getId(){
		return $this->id;
	}
		
	function setId( $_id){
		$this->id =	$_id;
	}
	
	function getidNF(){
		return $this->idNF;
	}
		
	function setIdNF( $_idNF){
		$this->idNF = 	$_idNF;
	}
	
	function getQtde(){
		return $this->qtde;
	}
		
	function setQtde( $_qtde){
		$this->qtde = 	$_qtde;
	}
	
	function getUnid(){
		return $this->unid;
	}
		
	function setUnid( $_unid){
		$this->unid = 	$_unid;
	}
	
	function getDescricao(){
		return $this->descricao;
	}
		
	function setDescricao( $_descr){
		$this->descricao = 	$_descr;
	}
	
	function getValorUnit(){
		return $this->valorUnit;
	}
		
	function setValorUnit( $_vlUnit){
		$this->valorUnit = 	$_vlUnit;
	}
	
} // fim da classe

?>