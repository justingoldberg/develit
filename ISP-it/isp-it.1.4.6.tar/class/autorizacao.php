<?
	define('FPDF_FONTPATH','class/fpdf/font/');
	require('class/fpdf/fpdf.php');
	
	
	class AutorizacaoDebitoAutomatico{
		var $pdf;
		function AutorizacaoDebitoAutomatico(){
		}
		
		/**
		* @return nomeArquivo PDF gerado
		* @param unknown $cliente
		* @param unknown $valor
		* @desc fun��o que gera arquivo PDF de Atuoriza��o de D�bito Autom�tico!
		*/
		function geraAutorizacao($cliente,$valor,$descricao,$cidade,$uf){
			global $arquivo, $sessLogin;
			$mes=array();
			$mes['01']='Janeiro';
			$mes['02']='Fevereiro';
			$mes['03']='Mar�o';
			$mes['04']='Abril';
			$mes['05']='Maio';		
			$mes['06']='Junho';	
			$mes['07']='Julho';
			$mes['08']='Agosto';
			$mes['09']='Setembro';
			$mes['10']='Outubro';
			$mes['11']='Novembro';
			$mes['12']='Dezembro';
			
			$pdf= new FPDF();
			
			$pdf->SetLeftMargin(25);
			$pdf->AddPage(); // adiciona a p�gina de impressao do PDF			
			$pdf->SetFont('arial','B','16'); // define a fonte inicial
			//Titulo
			$titulo='Autoriza��o de D�bito em Conta Corrente';
			$pdf->Cell('',25,$titulo,0,0,'C'); //seta o titulo em uma celula
			$pdf->Ln(); $pdf->Ln(); // \n new line
			//CABECALHO -->
			//Linha Cliente
			$pdf->SetFont('arial','','10'); // definicao de fonte
			$pdf->SetX(25); //move o ponteiro para a posicao indicada
			$pdf->SetY(30);
			$pdf->Write(10,'Cliente'); //escreve uma linha na posicao indicada posicao Y (vertical)
			$pdf->Line(40,36,210,36); // insere uma linha nas posicoes indicadas (x1,x2,y1,y2)
			$pdf->Ln(); // nova linha
			//Linha CPF
			$pdf->SetX(25); // define a posicao horizontal atual
			$pdf->Write(10,'CPF'); //coloca o conteudo na linha indicada por SetX e a posicao indicada 
			$pdf->Line(40,46,210,46); // oinsere uma linha com as coordenadas definidas
			$pdf->Ln();
			//Linha Banco e Agencia
			$pdf->SetX(25);
			$pdf->Write(10,'Banco');
			$pdf->Line(39,56,100,56);
			$pdf->SetX(101); // muda para a posicao  101
			$pdf->Write(10,'Ag�ncia'); //coloca o conteudo na linha indicada por SetX e a posicao indicada em Write
			$pdf->Line(116,56,210,56); // insere uma linha
			$pdf->Ln();
			//Linha C/C
			$pdf->SetX(25);
			$pdf->Write(10,'Conta Corrente');
			$pdf->Line(52,66,210,66);
			$pdf->Ln();
			//Linha Fone contato
			$pdf->SetX(25);
			$pdf->Write(10,'Fone p/ Contato');
			$pdf->Line(52,76,210,76);
			$pdf->Ln();
			//Linha Identificador Convenio
			$pdf->SetX(25);
			$pdf->Write(10,'(Uso '.$cliente.')N Identificador');
			$posX=($pdf->GetX() + 1);
			$pdf->Line($posX,86,138,86);
			$pdf->SetX(139);
			$pdf->Write(10,'Conv�nio');
			$pdf->Line(156,86,210,86);
			$pdf->Ln(); $pdf->Ln();
	
			//notificacao de valor de taxa!
			$pdf->SetFont('arial','B',12);
			$pdf->MultiCell('0','5','Taxa de acesso ao provedor de Internet  '.$cliente.' Mensalidade : R$ '.$valor.' '.$descricao.'.');
			$pdf->Ln(); $pdf->Ln();
	
			//Corpo da autoriza��o
			$corpo='Visando o pagamento da conta consignada acima, o cliente neste ato, autoriza o Banco a debitar na data do vencimento, a respectiva import�ncia na sua conta corrente, tamb�m especificada acima.
			Para tanto, o cliente dever� prover sua conta corrente com saldo suficiente para a liq�ida��o daquelas contas, declarando-se ciente de que o Banco, quando da eventual insufici�ncia de saldo dispon�vel, poder� n�o efetuar o respectivo d�bito ou, caso o efetue, poder� estorna-lo sem que isso implique em qualquer responsabilidade ao Banco, notadamente aqueles decorrentes de multas e acr�scimos morat�rios. 
			Os servi�os de d�bito em conta corrente, al�m do quanto acima exposto, ser�o executados pelo Banco em conformidade com as disposi��es a baixo relacionadas, as quais o cliente declarar conhecer e aceitar,  a saber :
			Este servi�o � executado pelo banco em decorr�ncia de celebra��o de conv�nio especifico com a empresa em quest�o, raz�o pela qual o d�bito dessas contas ocorrer� somente ap�s o cadastramento do cliente perante aquela empresa, o que poder� demorar at� 10 dias contados da solicita��o efetuada .';
			//A PARTIR DAQUI QUEBRAMOS O CORPO EM PARTES PARA RESPEITAR A FORMATA��O DO TEXTO!
			//TOPICOS 1 ATEH N
			$corpo_slice1='O cliente dever� ser cientificado do seu cadastramento/descadastramento perante a respectiva empresa quando : for efetuado ou deixar de ser efetuado o respectivo d�bito em sua conta corrente.';
			$corpo_slice2='Qualquer pedido de cancelamento de d�bito, inclusive j� agendado, dever� ser efetuado diretamente � empresa, pelo pr�prio cliente, o qual dever� observar o prazo suficiente para que a mesma comunique esse cancelamento ao Banco com no m�nimo, dez dias �teis de anteced�ncia da data prevista para o d�bito.';
			$corpo_slice3='As contas ser�o consideradas quitadas, quando efetivado o respectivo d�bito na conta corrente do cliente, servindo o extrato banc�rio como comprovante daquele pagamento.';
			$corpo_slice4='A presta��o de servi�o de d�bito relativo  � essas contas � isenta do pagamento de qualquer tarifa pelo cliente.';
			$corpo_slice5='O Banco e a empresa em quest�o poder� a qualquer tempo, rescindir o conv�nio acima mencionado, o que acarretar� o autom�tico cancelamento dos servi�os objeto dessa autoriza��o, sem que isso implique, todavia, na obrigatoriedade de pr�via comunica��o ao cliente pelo Banco.';
			$corpo_slice6='A presente autoriza��o viger� por prazo indeterminado.';
			$dataAutorizacao= $cidade.' /'.$uf.' '.date('d').' de '.$mes[date('m')].' de '.date('Y').'.';
			$assinatura='_________________________';
			$assinatura1='Assinatura do Cliente';
			
			$pdf->SetFont('arial','',8);
			$pdf->MultiCell('0','5',$corpo);
			$pdf->SetX(35);
			$pdf->Write(5,'-  ');
			$pdf->SetX(50);
			$pdf->MultiCell('0','5',$corpo_slice1);
			$pdf->SetX(35);
			$pdf->Write(5,'-  ');
			$pdf->SetX(50);
			$pdf->MultiCell('0','5',$corpo_slice2);
			$pdf->SetX(35);
			$pdf->Write(5,'-  ');
			$pdf->SetX(50);
			$pdf->MultiCell('0','5',$corpo_slice3);
			$pdf->SetX(35);
			$pdf->Write(5,'-  ');
			$pdf->SetX(50);
			$pdf->MultiCell('0','5',$corpo_slice4);
			$pdf->SetX(35);
			$pdf->Write(5,'-  ');
			$pdf->SetX(50);
			$pdf->MultiCell('0','5',$corpo_slice5);
			$pdf->SetX(35);
			$pdf->Write(5,'-  ');
			$pdf->SetX(50);
			$pdf->MultiCell('0','5',$corpo_slice6);	
			
			$pdf->Ln(); $pdf->Ln();
			
			//Data da Autoriza��o
			$pdf->SetX(35);
			$pdf->Write(5,$dataAutorizacao);
			$pdf->Ln(); $pdf->Ln(); $pdf->Ln(); $pdf->Ln();
			$pdf->SetX(135);
			$pdf->Cell('','6',$assinatura1,'T',0,'C');
			
			$nomeArquivo=$arquivo[tmpPDF].$sessLogin[login].'Autorizacao.pdf';
			if(file_exists($nomeArquivo)){
				unlink($nomeArquivo);	
			}
			
			$pdf->Output($nomeArquivo,'F');
			return $nomeArquivo;
		}
	}
?>
