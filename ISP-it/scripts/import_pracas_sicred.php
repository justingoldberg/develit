<?

require_once('../config/custom.php');
require_once('../class/InterfaceBD.php');
require_once('../class/BDIT.php');
require_once('../class/PracaSicredi.php');
require_once('../includes/db.php');

###########################################
# Conectar com banco de dados
$conn=conectaMySQL($configHostMySQL, $configUserMySQL, $configPasswdMySQL);
$db=selecionaDB($configDBMySQL, $conn);
###########################################
	
$arquivoPracas	= "../config/PRACA106.TXT";
$fp 			= fopen( $arquivoPracas, 'r' );
while( !feof( $fp ) ) {
	$praca = new PracaSicredi();
	$praca->setConnection( $conn );
	$praca->parseLinha( fgets( $fp ) );
	$praca->Salva();
}
fclose( $fp );

?>
