<?php
################################################################################
#       Criado por: JosÚ Roberto Kerne - joseroberto@kerne.org
#  Data de criação: 02/10/2003
# Ultima alteração: 02/10/2003
#    Alteração No.: 001
#
# Função:
#    Funções JavaScript

### Javascript ###
echo "
<script language=JavaScript>
<!--
function novaJanela(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}

function voltar() {
	document.history.back();
}

function verificaData(data, campo) {
	
	var er = /\//g;
	
	if(data) {
		data=data.replace(er,'');
		
		dia=data.substr(0,2);
		mes=data.substr(2,2);
		ano=data.substr(4,4);
		
		dataFormat=dia+'/'+mes+'/'+ano;
		
		if( !dia || !mes || !ano || (dia <= 0 || dia > 31) || (mes <=0 || mes > 12) || (ano < 1900) ) {
			alert('Data Inválida');
			document.forms[0].elements[campo].value='';
		}
		else {
			document.forms[0].elements[campo].value=dataFormat;
		}
	}
}


function verificaDataSuperior(data, campo) {
	
	var er = /\//g;
	data=data.replace(er,'');
	
	if(data) {
		dia=data.substr(0,2);
		mes=data.substr(2,2);
		ano=data.substr(4,4);
		
		dataFormat=dia+'/'+mes+'/'+ano;
		
		if( !dia || !mes || !ano || (dia <= 0 || dia > 31) 
			|| (mes <=0 || mes > 12) 
			|| (ano < 1900) 
			|| ( (dia <= $data[dia]) && mes <= $data[mes] && ano <= $data[ano]) 
			|| (mes < $data[mes] && ano <= $data[ano]) 
			|| ano < $data[ano] ) {
			alert('Data Inválida');
			document.forms[0].elements[campo].value='';
		}
		else {
			document.forms[0].elements[campo].value=dataFormat;
		}
	}
}


function verificaDataMesAno(data, campo) {
	
	var er = /\//g;
	data=data.replace(er,'');
	
	if(data) {
		mes=data.substr(0,2);
		ano=data.substr(2,4);
		
		dataFormat=mes+'/'+ano;
		
		if( !mes || !ano || 
			(mes < 1 || mes > 12) 
			|| ( mes < $data[mes] && ano <= $data[ano] )  
			|| (ano < $data[ano]) 
			|| ( mes > $data[mes] && ano > $data[ano]+1 ) 
		) {
		
			if ( mes > $data[mes] && ano > $data[ano]+1 ) alert('Data de desconto possui ANO inválido ou superior ao ANO Atual +1');
			else alert('Data Inválida');
			
			document.forms[0].elements[campo].value='';
		}
		else {
			document.forms[0].elements[campo].value=dataFormat;
		}
	}
}


function verificaDataMesAnoPagamento(data, campo) {
	
	var er = /\//g;
	data=data.replace(er,'');
	
	if(data) {
		dia=data.substr(0,2);
		mes=data.substr(2,2);
		ano=data.substr(4,4);
		
		dataFormat=dia+'/'+mes+'/'+ano;
		
		if( !dia || !mes || !ano || (mes < 1 || mes > 12) || (dia < 1 || dia > 31) ) {
		
			alert('Data Inválida');
			
			document.forms[0].elements[campo].value='';
		}
		else {
			document.forms[0].elements[campo].value=dataFormat;
		}
	}
}




function verificaCEP(cep,campo) {
	
	var er = /[^0-9]/g;
	cep=cep.replace(er,'');
	
	if(cep) {
		prefixo=cep.substr(0,5);
		sufixo=cep.substr(5,3);
		
		cepFormat=prefixo+'-'+sufixo;
		
		if( !prefixo || !sufixo ) {
			alert('CEP Inválido');
			document.forms[0].elements[campo].value='';
		}
		else {
			document.forms[0].elements[campo].value=cepFormat;
		}
	}
}


function verificaCNPJ(cnpj,campo) {
	
	var er = /[^0-9]/g;
	cnpj=cnpj.replace(er,'');
	
	if(cnpj) {
		prefixo=cnpj.substr(0,8);
		sufixo=cnpj.substr(8,4);
		digito=cnpj.substr(12,2);
		
		prefixo1=prefixo.substr(0,2);
		prefixo2=prefixo.substr(2,3);
		prefixo3=prefixo.substr(5,3);
		
		cnpjFormat=prefixo1 + '.' + prefixo2 + '.' + prefixo3 + '/' + sufixo + '-' + digito;
		
		if( !prefixo || !sufixo || !digito) {
			alert('CNPJ Inválido');
			document.forms[0].elements[campo].value='';
		}
		else {
			document.forms[0].elements[campo].value=cnpjFormat;
		}
	}
}

function verificaURL(url,campo) {

	if(url) {
		if(url.substr(0,4) != 'http') {
			url='http://'+url;
		}
		
		document.forms[0].elements[campo].value=url;
	}
}


function formataValor(valor,campo) {

	var er = /[^0-9]/g;
	valor=valor.replace(er,'');
	
	if(valor) {
		centavos=valor.substr((valor.length-2),2);
		inteiro=valor.substr(0,(valor.length-2));
		
		valorFormat=inteiro+','+centavos;
		
		document.forms[0].elements[campo].value=valorFormat;
	}
}


function verificarValor(original,novo) {

	if(original > novo) {
		alert(\"ATENÇÃO: Valor informato é INFERIOR ao valor original!\");
	}
	
}

function calculaDesconto(valor,desconto,index) {
	total=(valor - ((valor * desconto)/100));
	total=number_format(total);
	formataValor(total,index);
}


function calculaDescontoAplicado(valor,desconto,index) {
	total=((valor * desconto)/100);
	total=number_format(total);
	formataValor(total,index);
}


function number_format(num) {

	num = num.toString().replace(/\$|\,/g,'');
	
	if(isNaN(num))
		num = '0';
		
	sign = (num == (num = Math.abs(num)));
	num = Math.floor(num*100+0.50000000001);
	cents = num%100;
	num = Math.floor(num/100).toString();
	
	if(cents<10)
		cents = '0' + cents;
	
	for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
		num = num.substring(0,num.length-(4*i+3))+','+num.substring(num.length-(4*i+3));
	
	return (num + cents);
}

//-->
</script>
";
### Fim do Javascript ###
?>
