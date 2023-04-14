<?php
    function currencyPrint($value, $currencyCharacter = "$", $groupSeparator = ",", $decimalCharacter = ".") {
        if (is_null($value)) {
            return "";
        }
        elseif ($value == 0) {
            return "FREE";
        }
        $value = round($value, 2, PHP_ROUND_HALF_UP);
        $retVal = $value < 0 ? "-" : "";
        $retVal .= $currencyCharacter;

        $centStr = fmod($value, 1);
        $denominations = $value - $centStr;
        if ($centStr != 0) {
            $centStr = substr($centStr, 1);
        }
        $centStr = str_pad(substr(str_replace("0.",".", $centStr), 1),2,"0", STR_PAD_RIGHT);
        
        $denominationStr = "";
        while ( $denominations > 0 ) {
            $frac = fmod($denominations, 1000);
            $denominations = $denominations - $frac;

            $denominationStr =  str_pad($frac,($denominations > 0 ? 3 : 0),"0", STR_PAD_LEFT) . ($denominationStr != "" ? $groupSeparator : "") . $denominationStr;

            $denominations /= 1000;
        }
		return $retVal . ($denominationStr != "" ? $denominationStr : "0") . $decimalCharacter . str_pad($centStr,2,"0", STR_PAD_LEFT);
	}
?>