<?
################################################################################
#       Criado por: RogГ©rio aka PopГі
#  Data de criaГ§ГЈo: 11/01/2005
# Ultima alteraГ§ГЈoo: 11/01/2005
#    AlteraпїЅпїЅo No.: 001
#
# FunпїЅпїЅo:
#    Nota Fiscal - Classe para criaпїЅпїЅo, alteraпїЅпїЅo e impressпїЅo de nota fiscal
################################################################################
#include('InterfaceBD.php');
Class NotaFiscal extends InterfaceBD{ // inicio da Classe

	//att utilizados em operacoes de bd
	var $tabela = 'NotaFiscal';
	var $campos = array( "id", "numNF", "razao", "cnpj", "dtEmissao", "endereco", "bairro", "cep", "cidade", "fone", "uf", "inscrEst", "obs", "status", "idPessoaTipo", "natOper", "ISSQN", "idPop" );
	
	// objetos instanciados na classe
	var $Itens = array();
	var $Desc = array();
	var $Acresc = array();
	
	var $objItem;
	
	//atributos da classe 
	var $id;
	var $numNF;
	var $razao;
	var $cnpj;
	var $dtEmissao;
	var $endereco;
	var $bairro;
	var $cep;
	var $cidade;
	var $fone;
	var $uf;
	var $inscrEst;
	var $obs;
	var $status;
	var $idPessoaTipo;
	var $natOper;
	var $tributos = false;
	
	// constante que indica um determinado atributo para ser pesquisado com a funcao SQL Max()
	var $SqlMax;
	
	function NotaFiscal( $conn = '' ){ //Construtor
		$this->InterfaceBD();
		
		if ( !empty( $conn ) ) $this->setConnection( $conn );
		$this->sqlMax = "NumNF";

		//instanciamos a classe dos itens
		$this->objItem = new ItemNF();
		$this->objItem->ItemNF(); 	
		$this->objItem->setConnection( $this->getConnection() );
	}
	
	function setNumNF( $_numNF ){
		$this->numNF = $_numNF;			
	}
	
	function getNumNF(){
		
		//$resultado = $this->seleciona( array( "NumNF" ), array( "NumNF = $this->NumNF" ) );
		
		return $this->numNF;
	}
	
	function setNovoNumNF(){
		if ( $this->idPop ){
			$cond = "idPop = '". $this->idPop ."'";
		}
		
		foreach ( $this->getMaxField( $cond ) as $objField ){
			$this->numNF = $objField->NumNF + 1;
		}
	}
	
	function incluirItem(){
		//definimos este index para controlar um array de objetos (cada elemento do arrayu serпїЅ um item dos Itens da Nota)
		$index = 0;
		$where = array( "idNF = '".$this->getId()."'" );
		$dadosItens = $this->objItem->seleciona('', '', $where );
		
		if (!empty( $dadosItens ) ){
			foreach ( $dadosItens as $Itens ){
				$this->Itens[$index] = new ItemNF();
				$this->Itens[$index]->setId( $Itens->id );  // o id eh enviado com valor vazio msm pois eh auto-increment
				$this->Itens[$index]->setIdNF( $Itens->idNF ); // definir como obter este valor
				$this->Itens[$index]->setQtde( $Itens->qtde );
				$this->Itens[$index]->setUnid( $Itens->unid );
				$this->Itens[$index]->setDescricao( $Itens->descricao );
				$this->Itens[$index++]->setValorUnit( $Itens->valorUnit );
			}
		}
	}
	
	function incluirDesconto( $dados ){
		//definimos este index para controlar um array de objetos (cada elemento do arrayu serпїЅ um item de desconto)
		$index = count( $this->Itens );
		$this->Desc[$index] = new DescontoNF();
		$this->Desc[$index]->setId = ''; // o id eh enviado com valor vazio msm pois eh auto-increment
		$this->Desc[$index]->setIdNF = $this->getId(); // definir como obter este valor
		$this->Desc[$index]->setQtde = $dados[qtde];
		$this->Desc[$index]->setDescricao = $dados[descr];
		$this->Desc[$index]->setValor = $dados[valor];		
	}
	
	function incluirAcrescimo( $dados ){
		//definimos este index para controlar um array de objetos (cada elemento do arrayu serпїЅ um item de acrescimo)
		$index = count( $this->Itens );
		$this->Acresc[$index] = new DescontoNF();
		$this->Acresc[$index]->id = ''; // o id eh enviado com valor vazio msm pois eh auto-increment
		$this->Acres[$index]->idNF = $this->getId(); // definir como obter este valor
		$this->Acresc[$index]->qtde = $dados[qtde];
		$this->Acresc[$index]->descricao = $dados[descr];
		$this->Acresc[$index]->valor = $dados[valor];
	}
	
	function calculaAcrescimo(){
		$resultado = $this->Acresc->seleciona(array( "Sum(valor) valor" ), array( "idNF = $this->id" ));
		
		return $resultado;
	}
	
	function calculaDesconto(){
		//$resultado = $this->Desc->seleciona(array( "Sum(valor) valor" ), array( "idNF = $this->id" ));
		$resultado = $this->objItem->seleciona('DescontoNF',array( "Sum( ( valorUnit*qtde ) ) valor" ), array( "idNF = $this->id" ));
		return $resultado;
	}
	
	function calculaItens(){
		$resultado = $this->objItem->seleciona('',array( "Sum( ( valorUnit*qtde ) ) valor" ), array( "idNF = $this->id" ));
		return $resultado;
	}
	
	function calculaTotalNota(){
		$totItem = $this->calculaItens();
		//$totDesc = $this->calculaDesconto();
		//$totAcres = $this->calculaAcrescimo();
		//$total =  ( $totItem->valor + $totAcres->valor ) - $totDesc->valor ;
		$total = $totItem;
		if ($total[0]->valor == '')
			$total[0]->valor = '0.00';
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
			$this->setBairro( $dadosNota[0]->bairro );
			$this->setCep( $dadosNota[0]->cep );
			$this->setCidade( $dadosNota[0]->cidade );
			$this->setFone( $dadosNota[0]->fone );
			$this->setUf( $dadosNota[0]->uf );
			$this->setInscrEst( $dadosNota[0]->inscrEst );
			$this->setObs( $dadosNota[0]->obs );
			$this->setStatus( $dadosNota[0]->status );
			$this->setIdPessoaTipo( $dadosNota[0]->idPessoaTipo );
			$this->setNatOper( $dadosNota[0]->natOper );
			$this->setISSQN( $dadosNota[0]->ISSQN);
			$this->setIdPop( $dadosNota[0]->idPop);
			
			$this->incluirItem();
		}	
	}
	
	function imprimirNota(){
		
		//verifica qual o modelo de nota a ser impresso
		$parametros = carregaParametrosConfig();
		
		//se tiver no parametro o valor B
		if( strtoupper( $parametros['layoutNF'] ) == 'B' ) {
			//formata a impressao da nota conforme modelo B (Usado pelo Cliente Via Local)
			$objPrint = new ImpressaoNFModeloB( $this );					
		}
		elseif( strtoupper( $parametros['layoutNF'] ) == 'C' ){
			//formata a impressao da nota conforme modelo C (Modelo antigo usado pela Devel-IT)
			$objPrint = new ImpressaoNFModeloC( $this );	
		}
  		elseif(strtoupper($parametros['layoutNF'] == 'D')){
  			// formata a impressao da nota conforme modelo D (Modelo com desconto utilizado pela Devel-IT)
 			$objPrint = new ImpressaoNFModeloD($this);
  		}
  		elseif (strtoupper($parametros['layoutNF'] == 'E')){
  			// formata a impressгo da nota conforme modelo E (Modelo com desconto utilizado pela TDKOM)
  			$objPrint = new ImpressaoNFModeloE($this);
  		}
  		// Por Felipe Assis - 01/02/2008
  		// Implementaзгo de modelo de impressгo para notas da Porto Seguros
  		elseif (strtoupper($parametros['layoutNF'] == 'F')){
  			// formata a impressгo da nota conforme modelo F (Modelo com alinhamento de nъmero da nota diferente para
  			// as notas da Porto Seguros)
  			$objPrint = new ImpressaoNFModeloF($this);
  		}
		else {
			//caso contrario imprime modelo padrao (Modelo antigo usado pela Tdkom)
			$objPrint = new ImpressaoNF( $this );
		}
		$arquivoNota = $objPrint->imprimir();

		//$this->gravaStatus( $this->getId(), 'I' );
		
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

	function getBairro(){
		
		return $this->bairro;
	}
	
	function setBairro( $_bairro ){
		$this->bairro = $_bairro;
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
	
	function getFone(){
		
		return $this->fone;
	}
	
	function setFone( $_fone ){
		$this->fone = $_fone;
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
	
	function getNatOper(){
		return $this->natOper;
	}
	
	function setNatOper( $_natOper ){
		$this->natOper = $_natOper;
	}
	
	function setISSQN( $_ISSQN ){
		$this->ISSQN = $_ISSQN;
	}
	
	function setIdPop( $_idPop ){
		$this->idPop= $_idPop;
	}
	
} //fim da Classe NotaFiscal

?>