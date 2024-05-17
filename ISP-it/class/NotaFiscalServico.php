<?
################################################################################
#       Criado por: Lis
#  Data de criaчуo: 30/05/2007
# Ultima alteraчуo: 30/05/2007
#    Alteraчуo No.: 001
#
# Funчуo:
#    Nota Fiscal Fatura Servico - Classe para criaчуo, alteraчуo e impressуo de nota fiscal fatura de serviчo de comunicaчуo
################################################################################
#include('InterfaceBD.php');
Class NotaFiscalServico extends InterfaceBD{ // inicio da Classe

	//att utilizados em operacoes de bd
	var $tabela = 'NotaFiscalServico';
	var $campos = array( 'id', 'numNF', 'razao', 'cnpj', 'dtEmissao', 'endereco', 'cep', 'cidade', 'uf', 'inscrEst', 'obs', 'status', 'idPessoaTipo', 'idNatPrestacao', 'ICMS', 'idPop', 'dtPrestacao' );
	
	// objetos instanciados na classe
	var $Itens = array();
	var $objItem;
	
	//att de classe 
	var $id;
	var $numNF;
	var $razao;
	var $cnpj;
	var $dtEmissao;
	var $endereco;
	var $cep;
	var $cidade;
	var $uf;
	var $inscrEst;
	var $obs;
	var $status;
	var $idPessoaTipo;
	var $idNatPrestacao;
	var $dtPrestacao;
	var $ICMS;
	
	// constante que indica um determinado atributo para ser pesquisado com a funcao SQL Max()
	var $SqlMax;
	
	function NotaFiscalServico( $conn = '' ){ //Construtor
		$this->InterfaceBD();
		
		if ( !empty( $conn ) ) $this->setConnection( $conn );
		$this->sqlMax = "NumNF";

		//instanciamos a classe dos itens
		$this->objItem = new ItensNFServico();
		$this->objItem->ItensNFServico(); 	
		$this->objItem->setConnection( $this->getConnection() );
	}
	
	function setNumNF( $_numNF ){
		$this->numNF = $_numNF;			
	}
	
	function getNumNF(){
		return $this->numNF;
	}
	
	function setNovoNumNFS(){
		if ( $this->idPop ){
			$cond = "idPop = '". $this->idPop ."'";
		}
		
		foreach ( $this->getMaxField( $cond ) as $objField ){
			$this->numNF = $objField->NumNF + 1;
		}
	}
	
	function incluirItem(){
		//definimos este index para controlar um array de objetos (cada elemento do arrayu serс um item dos Itens da Nota)
		$index = 0;
		$where = array( "idNFS = '".$this->getId()."'" );
		$dadosItens = $this->objItem->seleciona('', '', $where );
		
		if( !empty( $dadosItens ) ){
			foreach( $dadosItens as $Itens ){
				$this->Itens[$index] = new ItensNFServico();
				$this->Itens[$index]->setId( $Itens->id );  // o id eh enviado com valor vazio msm pois eh auto-increment
				$this->Itens[$index]->setIdNFS( $Itens->idNFS ); // definir como obter este valor
				$this->Itens[$index]->setDiscriminacao( $Itens->discriminacao );
				$this->Itens[$index]->setValor( $Itens->valor );
				$this->Itens[$index++]->setIsento( $Itens->isento );				
			}
		}
	}
	

	function calculaItens(){
		$resultado = $this->objItem->seleciona('',array( "Sum( ( valor ) ) valor" ), array( "idNFS = $this->id" ));
		return $resultado;
	}
	
	function calculaTotalNota(){
		$totItem = $this->calculaItens();
		$total = $totItem;
		if ($total[0]->valor == ''){
			$total[0]->valor = '0,00';
		}
		return ($total);
	}
	
	function gravaStatus( $id, $status ){ // metodo que grava a situacao das notas cadastradas aberta/fechada/impressa
		$this->setStatus( $status );
		return $this->salva( 'status' );
	} // fim do metodo setStatus()

	function gravarNota(){
		return $resultado = $this->salva();
	}
	
	function preparaNota( $_id ){
		// fazemos uma consulta se houver algum id setado e entaum fazemos tds os setters
		if ( $_id > 0 ){
			$where = array( "id = '". $_id."'" );
			$dadosNota = $this->seleciona('', '', $where );
			
			$this->setId( $dadosNota[0]->id );
			$this->setNumNF( $dadosNota[0]->numNF );
			$this->setRazao( $dadosNota[0]->razao );
			$this->setCnpj( $dadosNota[0]->cnpj );
			$this->setDtEmissao( $dadosNota[0]->dtEmissao );
			$this->setEndereco( $dadosNota[0]->endereco );
			$this->setCep( $dadosNota[0]->cep );
			$this->setCidade( $dadosNota[0]->cidade );
			$this->setUf( $dadosNota[0]->uf );
			$this->setInscrEst( $dadosNota[0]->inscrEst );
			$this->setObs( $dadosNota[0]->obs );
			$this->setStatus( $dadosNota[0]->status );
			$this->setIdPessoaTipo( $dadosNota[0]->idPessoaTipo );
			$this->setIdNatPrestacao( $dadosNota[0]->idNatPrestacao );
			$this->setICMS( $dadosNota[0]->ICMS);
			$this->setIdPop( $dadosNota[0]->idPop);
			$this->setDtPrestacao($dadosNota[0]->dtPrestacao);
			
			$this->incluirItem();
		}	
	}
	
	function imprimirNota(){
		
		$objPrint = new ImpressaoNFServico( $this );					

		$arquivoNota = $objPrint->imprimir();

		return $arquivoNota;

	}
	
	
	// gets e sets //----------------------------------------------------------------------------------------------------------------------//
	
	function getId(){
		return $this->id;
	}
	
	function setId( $_id ){
		$this->id = $_id;
	}
	
	function getRazao(){
		return $this->razao;
	}
	
	function setRazao( $_razao ){
		$this->razao = $_razao;
	}
	
	function getCnpj(){
		return $this->cnpj;
	}
	
	function setCnpj( $_cnpj ){
		$this->cnpj = $_cnpj;
	}	
	
	function getDtEmissao(){
		return $this->dtEmissao;
	}
	
	function setDtEmissao( $_dtEmissao ){
		$this->dtEmissao = $_dtEmissao;
	}

	function getEndereco(){
		return $this->endereco;
	}
	
	function setEndereco( $_endereco ){
		$this->endereco = $_endereco;
	}

	function getCep(){
		return $this->cep;
	}
	
	function setCep( $_cep ){
		$this->cep = $_cep;
	}
	
	function getCidade(){
		return $this->cidade;
	}
	
	function setCidade( $_cidade ){
		$this->cidade = $_cidade;
	}
	
	function getUf(){
		return $this->uf;
	}
	
	function setUf( $_uf ){
		$this->uf = $_uf;
	}

	function getInscrEst(){
		return $this->inscrEst;
	}
	
	function setInscrEst( $_inscrEst){
		$this->inscrEst = $_inscrEst;
	}

	function getObs(){
		return $this->obs;
	}
	
	function setObs( $_obs ){
		$this->obs = $_obs;
	}	
	
	function getStatus(){
		return $this->status;
	}
	
	function setStatus( $_status ){
		$this->status = $_status;
	}
	
	function getIdPessoaTipo(){
		return $this->idPessoaTipo;
	}
	
	function setIdPessoaTipo( $_idPessoasTipo ){
		$this->idPessoaTipo = $_idPessoasTipo;
	}
	
	function getIdNatPrestacao(){
		return $this->idNatPrestacao;
	}
	
	function setIdNatPrestacao( $_idNatPrestacao ){
		$this->idNatPrestacao = $_idNatPrestacao;
	}
	
	function getICMS(){
		return $this->ICMS;
	}
	
	function setICMS( $_ICMS ){
		$this->ICMS = $_ICMS;
	}
	
	function setIdPop( $_idPop ){
		$this->idPop= $_idPop;
	}
	
	function getDtPrestacao(){
		return $this->dtPrestacao;
	}
	
	function setDtPrestacao( $_dtPrestacao ){
		$this->dtPrestacao = $_dtPrestacao;
	}
	
} //fim da Classe NotaFiscal

?>