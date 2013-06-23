<?php

if ( !empty($_REQUEST['regex']) ) {

	$originalString = $_REQUEST['regex'];

	list($string, $keys) = jumble($originalString);

	list($noisedString, $noisedKeys) = noise($originalString, $string, $keys);

	$regexString = generateRegex($noisedString, $noisedKeys);
}


function jumble ($string) {
	
	$jumbledString = '';
	$stringArray = array();
	
	for ( $c = 0; $c <= strlen($string); $c++ ) {
		
		$stringArray[] = $string[$c];
	}
	
	$keys = array();
	
	for ( $c = 0; $c <= strlen($string); $c++ ) {
		
		$keys[] = array_search($string[$c], $stringArray);
	}
	
	
	$jumbledString = implode('', $stringArray);
	
	
	return array($jumbledString, $keys);
}


function noise ( $originalString, $string, $keys ) {
	
	$newKeys = array();
	$noisedStringArray = array();
	
	$stringArray = array();
	
	for ( $c = 0; $c <= strlen($originalString); $c++ ) {
		
		$stringArray[] = $originalString[$c];
	}
	
	for ( $c = 0; $c <= strlen($string); $c++ ) {
		
		$randomLength = rand(1,3);
		
		$randomString = '';
		
		
		for ( $woot = 0; $woot <= $randomLength; $woot++ ) {
			
			$randomString .= chr(rand(97, 122));
		}
		
		$noisedStringArray[] = $randomString . $string[$c];
		
		$offsetOfCharacter = array_search($string[$c], 
$stringArray);
		
		$newKeys[$offsetOfCharacter] = 
strlen(implode('',$noisedStringArray));
		
		unset($stringArray[$offsetOfCharacter]);
	}
	
	$noisedString = implode('', $noisedStringArray);
	
	return array($noisedString, $newKeys);
}



function reassemblyCheck ( $string, $keys ) {
	
	$realString = '';
	
	for ( $c = 0; $c < count($keys)-1; $c++ ) {
		
		$realString .= $string[$keys[$c]-1];
	}
	
	return $realString;
}



function generateRegex ( $string, $keys ) {
	
	$realString = '';
	$begin = 0;
	$pattern = '';
	$output = '';
	
	for ( $c = 0; $c < count($keys)-1; $c++ ) {
		
		$realCharacter = $keys[$c];
		
		$fillerStart = $begin;
		$fillerEnd = ( $keys[$c] - 1 );
		
		$fillerLength = $fillerEnd - $fillerStart;
		
		if ( $string[$realCharacter-1] == '!' )
			$pattern .= '[a-z\\\\]{' . ($fillerLength+1) . '}';
		else		
			$pattern .= '[a-z]{' . $fillerLength . '}';
		
		if ( ord($string[$realCharacter-1]) >= 97 && ord($string[$realCharacter-1]) <= 122 )
			$pattern .= '([a-z]{1})';
		elseif ( ord($string[$realCharacter-1]) >= 65 && ord($string[$realCharacter-1]) <= 90 )
			$pattern .= '([A-Z]{1})';
		else
			$pattern .= '([' . preg_quote($string[$realCharacter-1]) . ']{1})';

		$output .= '$' . ($c + 1) . '';
		
		$begin = $keys[$c];
	}
	
	return 'echo "' . str_replace('!', '\!', $string) . '" | perl -ne \'if (/' . $pattern . '/) { print "' . $output . '\n" }\'';
}

?>

<form method="post">

<input type="text" name="regex" value="<?=(!empty($_REQUEST['regex']) ? $_REQUEST['regex'] : '' ); ?>" />
<input type="submit" name="woot" value="Regex it!" />

</form>

<?=(!empty($originalString) ? '<p>' . $regexString . '</p>' : ''); ?>
