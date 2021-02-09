<?php
// CONFIGRAÇÕES DO BANCO
define('HOST', '');
define('USER', '');
define('PASS', '');
define('DBSA', '');

setlocale(LC_TIME, 'pt_BR', 'pt_BR.iso-8859-1', 'pt_BR.iso-8859-1', 'portuguese');

// AUTO LOAD DE CLASSES
function __autoload($Class){
    $iDir = null;

    if (!$iDir && file_exists(__DIR__ . DIRECTORY_SEPARATOR . $Class . '.class.php') && !is_dir(__DIR__ . DIRECTORY_SEPARATOR . $Class . '.class.php')):
        include_once (__DIR__ . DIRECTORY_SEPARATOR. $Class . '.class.php');
        $iDir = true;
    endif;

	if (!$iDir):
	    trigger_error("Não foi possível incluir {$Class}.class.php", E_USER_ERROR);
	    die;
	endif;
}

// TRATAMENTO DE ERROS # CSS constantes
define('AD_SUSSESS',	'alert-success');
define('AD_INFO',			'alert-info');
define('AD_ALERT',		'alert-warning');
define('AD_ERROR',		'alert-danger');

/**
 * ADErro :: Exibe erros lançados MYSQL
 *- Addicionar erros assim que sugir no array
 */ 
function ADErro($ErrMsg, $ErrNo, $ErrDie = null){
	$errophp = [
		'23000'	=> 'alert-danger',
		'42000' => 'alert-danger',
		'HY093' => 'alert-warning'
	];

	echo "
	<div class =\"alert {$errophp[$ErrNo]} mt-3 container-menu\" role=\"alert\">
		<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
			<span aria-hidden=\"true\">&times;</span>
		</button>
		{$ErrMsg}
	</div>
	";

	if ($ErrDie):
	    die;
	endif;
}

/**
 * PHPErro :: personaliza o gatilho do PHP
 */
function PHPErro($errno, $errstr, $errfile, $errline){
	$errophp = [
		'1'			=> 'alert-danger',
		'2'			=> 'alert-warning',
		'4'			=> 'alert-info',
		'8'			=> 'alert-info',
		'16'		=> 'alert-danger',
		'32'		=> 'alert-warning',
		'64'		=> 'alert-danger',
		'128'		=> 'alert-warning',
		'256'		=> 'alert-danger',
		'512'		=> 'alert-warning',
		'1024'	=> 'alert-info',
		'6143'	=> 'alert-success',
		'2048'	=> 'alert-success',
		'4096'	=> 'alert-danger'
	];
	$PHP_VERSION= PHP_VERSION;
	$PHP_OS=PHP_OS;
	echo "
	<div class=\"alert {$errophp[$errno]} mt-3 container-menu\" role=\"alert\">
		<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
			<span aria-hidden=\"true\">&times;</span>
		</button>
		<strong>Erro na Linha: #{$errline} ::</strong> {$errstr}<br />
		<small>{$errfile}</small><br />
		PHP {$PHP_VERSION} ({$PHP_OS})<br />
		Erro n: {$errno}
	</div>
	";
  if ($errno == E_USER_ERROR):
	  die;
	endif;
}

/**
 *# Tira acentuação e [ troca espaço por - ]
 *- $tring = texto a ser modificador
 */
function tirarAcentos($string){
	$comAcentos = array(' ', 'à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ü', 'ú', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'O', 'Ù', 'Ü', 'Ú', 'ç', 'Ç');
	$semAcentos = array('-', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', '0', 'U', 'U', 'U', 'c', 'C');
	return str_replace($comAcentos, $semAcentos, $string);
}

/**
 *# Formatação de numeros
 *- $number = numero a ser formatado
 *- $cents  = quantidade de casas decimais
 *- retorno = R$ 1,99
 */
function formatoDinheiro($number, $cents = 1){
  if(is_numeric($number)):
    if(!$number):
      $money = ($cents == 2 ? '0.00' : '0');
		else:
      // if(floor($number) == $number):
      //   $money = number_format($number, ($cents == 2 ? 2 : 0));
			// else:
        $money = number_format(round($number, 2), ($cents == 0 ? 0 : 2));
      // endif;
    endif;
    return 'R$ '.$money;
  endif;
}

/**
 *# Formata numeros
 *- float, decimal, etc...
 * Retorn = 0.10 para 10%
 */
function getPorcerto($numero){
	$Nn = strstr($numero, '.', true) != '0'? $numero.'%': strstr($numero, '.').'%';
	return str_replace('.', "", $Nn);
}

/**
 * # Cria uma janela com uma mensagem sem botão pra fechar
 * - $titulo = Titulo da mensagem
 * - $msg    = A mensagem a ser mostrada
 * - $alert  = Cor da janela:
 * -> alert-success
 * -> alert-info
 * -> alert-warning
 * -> alert-danger
 */
function getMsgAlert($titulo, $msg, $alert){
	echo "<div class=\"alert {$alert} alert-dismissible marg-1\" role=\"alert\">
	<h5 class=\"alert-heading\">{$titulo}</h5>
	<hr>
	<p>{$msg}</p>
	</div>";
}

function ArrumaAcento($vlr, $tipo){
	$rst='';
	switch($tipo){
		case 1: $rst = utf8_decode($vlr); break;
		case 2: $rst = utf8_encode($vlr); break;    
	}
	return $rst;
}	

// Apenas para verificar alguns arrays :: Uso administativo
function getPreA(array $string){
    echo '<pre>';
        print_r($string);
    echo '</pre>';
}

function getPre($string){
    echo '<pre>';
		print_r($string);
    echo '</pre>';
}

/**
 * # Retorna a data formatada
 * - 2020-08-13 = 13 de agosto de 2020, quinta-feira
 */
function getDatas($data){
	$dataR = strftime('%d de %B de %Y, %A', strtotime(date($data)));
	return utf8_encode($dataR);
}

/**
 *# Retorna um numero por extenso
 *- Exp: 1.250,25
 *- Escreve = mil duzentos e cinquenta e vinte cinco
 */
function numeroPorExtenso($number) {

	$hyphen      = '-';
	$conjunction = ' e ';
	$separator   = ', ';
	$negative    = 'menos ';
	$decimal     = ' ponto ';
	$dictionary  = array(
		0                   => 'zero',
		1                   => 'um',
		2                   => 'dois',
		3                   => 'três',
		4                   => 'quatro',
		5                   => 'cinco',
		6                   => 'seis',
		7                   => 'sete',
		8                   => 'oito',
		9                   => 'nove',
		10                  => 'dez',
		11                  => 'onze',
		12                  => 'doze',
		13                  => 'treze',
		14                  => 'quatorze',
		15                  => 'quinze',
		16                  => 'dezesseis',
		17                  => 'dezessete',
		18                  => 'dezoito',
		19                  => 'dezenove',
		20                  => 'vinte',
		30                  => 'trinta',
		40                  => 'quarenta',
		50                  => 'cinquenta',
		60                  => 'sessenta',
		70                  => 'setenta',
		80                  => 'oitenta',
		90                  => 'noventa',
		100                 => 'cento',
		200                 => 'duzentos',
		300                 => 'trezentos',
		400                 => 'quatrocentos',
		500                 => 'quinhentos',
		600                 => 'seiscentos',
		700                 => 'setecentos',
		800                 => 'oitocentos',
		900                 => 'novecentos',
		1000                => 'mil',
		1000000             => array('milhão', 'milhões'),
		1000000000          => array('bilhão', 'bilhões'),
		1000000000000       => array('trilhão', 'trilhões'),
		1000000000000000    => array('quatrilhão', 'quatrilhões'),
		1000000000000000000 => array('quinquilhão', 'quinquilhões')
	);
	
	if (!is_numeric($number)) {
		return false;
	}
	
	if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
		// overflow
		trigger_error(
			'numeroPorExtenso só aceita números entre ' . PHP_INT_MAX . ' à ' . PHP_INT_MAX,
			E_USER_WARNING
		);
		return false;
	}
	
	if ($number < 0) {
		return $negative . numeroPorExtenso(abs($number));
	}
	
	$string = $fraction = null;
	
	if (strpos($number, '.') !== false) {
		list($number, $fraction) = explode('.', $number);
	}
	
	switch (true) {
		case $number < 21:
			$string = $dictionary[$number];
			break;
		case $number < 100:
			$tens   = ((int) ($number / 10)) * 10;
			$units  = $number % 10;
			$string = $dictionary[$tens];
			if ($units) {
				$string .= $conjunction . $dictionary[$units];
			}
			break;
		case $number < 1000:
			$hundreds  = floor($number / 100)*100;
			$remainder = $number % 100;
			$string = $dictionary[$hundreds];
			if ($remainder) {
				$string .= $conjunction . numeroPorExtenso($remainder);
			}
			break;
		default:
			$baseUnit = pow(1000, floor(log($number, 1000)));
			$numBaseUnits = (int) ($number / $baseUnit);
			$remainder = $number % $baseUnit;
			if ($baseUnit == 1000) {
				$string = numeroPorExtenso($numBaseUnits) . ' ' . $dictionary[1000];
			} elseif ($numBaseUnits == 1) {
				$string = numeroPorExtenso($numBaseUnits) . ' ' . $dictionary[$baseUnit][0];
			} else {
				$string = numeroPorExtenso($numBaseUnits) . ' ' . $dictionary[$baseUnit][1];
			}
			if ($remainder) {
				$string .= $remainder < 100 ? $conjunction : $separator;
				$string .= numeroPorExtenso($remainder);
			}
			break;
	}
	/*
	if (null !== $fraction && is_numeric($fraction)) {
		$string .= $decimal;
		$words = array();
		foreach (str_split((string) $fraction) as $number) {
			$words[] = $dictionary[$number];
		}
		$string .= implode(' ', $words);
	}*/
	
	return $string;
}

/**
 *# Retorna um svg
 * $qual = ico de acordo com lista site bootstrapicons 
 *- $tamanho = 32w 32h
 *- $cor = Cor do icon
 */
function getSvg ($qual, $tamanho, $cor){
	$comum = "<svg width=\"{$tamanho}\" height=\"{$tamanho}\" viewBox=\"0 0 16 16\" class=\"{$cor}\" fill=\"currentColor\" xmlns=\"http://www.w3.org/2000/svg\">
		<use xlink:href=\"../app/imgs/bootstrap-icons.svg#{$qual}\"/>
	</svg>";
	return $comum;
}

/**
 *# Menu
 *- fdfsdf sd fd ds sd 
 */
function baseMenu(){
	$financa =	getSvg('newspaper', '30', 'tx-azul');
	$receita =	getSvg('book', '30', 'tx-laranja');
	$estoque =	getSvg('hdd-rack', '30', 'tx-branco');
	$logoff  =  '';
	// $relatorio =	getSvg('journal-text', '30', 'tx-vermelho');
	
	return "
		<a class=\"mr-3\"><img class=\"mr-3\" src=\"../app/imgs/flor.ico\" width=\"48\" height=\"48\"></a>

		<a href=\"http://$_SERVER[HTTP_HOST]/encantosdoflorescer/financas\"
			data-toggle=\"tooltip\" 
			data-placement=\"top\" 
			title=\"Finanças\"
			class=\"mr-3\">{$financa}
		</a>

		<a href=\"http://$_SERVER[HTTP_HOST]/encantosdoflorescer/receitas\"
			data-toggle=\"tooltip\" 
			data-placement=\"top\" 
			title=\"Receitas\" 
			class=\"mr-3\">{$receita}
		</a>

		<a href=\"http://$_SERVER[HTTP_HOST]/encantosdoflorescer/estoque\"
			data-toggle=\"tooltip\" 
			data-placement=\"top\" 
			title=\"Estoque\" 
			class=\"mr-3\">{$estoque}
		</a>

		<a href=\"http://$_SERVER[HTTP_HOST]/encantosdoflorescer/index.php?logoff=true\" title=\"Deslogar do Sistema\">
			<span>Deslogar !</span>
		</a>
	";
}


set_error_handler('PHPErro');