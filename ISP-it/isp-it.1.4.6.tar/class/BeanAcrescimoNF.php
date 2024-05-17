<?
################################################################################
#       Criado por: Rog�rio aka Pop�
#  Data de cria��o: 12/01/2005
# Ultima altera��o: 12/01/2005
#    Altera��o No.: 001
#
# Fun��o:
#    Acrescimo Nota Fiscal - Classe para inclusao de Acrescimos de valores em NF
################################################################################

Class AcrescimoNF extends InterfaceBD{ // inicio da Classe

	var $tabela = 'AcrescimoNF';
	var $campos = array( "id", "idNF", "descricao", "valorUnit" );
	
	var $id;
	var $idNF;
	var $descricao;
	var $valorUnit;
	
	function AcrescimoNF(){
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