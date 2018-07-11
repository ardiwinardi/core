<?php
namespace Core;

Class Terbilang{
	
	private function Intg2Str($iNumber) {
        $sBuf = "";
        switch ($iNumber) {
            case 0 : $sBuf = "nol";
                break;
            case 1 : $sBuf = "satu";
                break;
            case 2 : $sBuf = "dua";
                break;
            case 3 : $sBuf = "tiga";
                break;
            case 4 : $sBuf = "empat";
                break;
            case 5 : $sBuf = "lima";
                break;
            case 6 : $sBuf = "enam";
                break;
            case 7 : $sBuf = "tujuh";
                break;
            case 8 : $sBuf = "delapan";
                break;
            case 9 : $sBuf = "sembilan";
                break;
            case 10 : $sBuf = "sepuluh";
                break;
            case 11 : $sBuf = "sebelas";
                break;
            case 12 : $sBuf = "dua belas";
                break;
            case 13 : $sBuf = "tiga belas";
                break;
            case 14 : $sBuf = "empat belas";
                break;
            case 15 : $sBuf = "lima belas";
                break;
            case 16 : $sBuf = "enam belas";
                break;
            case 17 : $sBuf = "tujuh belas";
                break;
            case 18 : $sBuf = "delapan belas";
                break;
            case 19 : $sBuf = "sembilan belas";
                break;
        }

        return $sBuf;
    }

// end of Intg2Str

    private function SayTens($iNumber) {
        $sBuf = '';

        $iResult = intval($iNumber / 10);
        if ($iNumber >= 20) {
            $sBuf .= sprintf("%s puluh", $this->Intg2Str($iResult));
            $iNumber %= 10;

            if (($iNumber >= 1) && ($iNumber <= 9))
                $sBuf .= sprintf(" %s", $this->Intg2Str($iNumber));
        }
        else if (($iNumber >= 0) && ($iNumber <= 19))
            $sBuf .= $this->Intg2Str($iNumber);

        return trim($sBuf);
    }

	// end of SayTens

    private function SayHundreds($iNumber) {
        $sBuf = '';
        $iResult = 0;

        $iResult = intval($iNumber / 100);
        if (($iResult > 0) && ($iResult != 1))
            $sBuf .= sprintf("%s ratus ", $this->Intg2Str($iResult));
        else if ($iResult == 1)
            $sBuf = "seratus ";
        $iNumber %= 100;

        if ($iNumber > 0)
            $sBuf .= $this->SayTens($iNumber);

        return trim($sBuf);
    }

	// end of SayHundreds

    public function SayInIndonesian($number) {
        $arrNumber = explode(".", $number);
        $iNumber = $arrNumber[0];
        $iResult = 0;
        $sBuf = '';

        if ($iNumber == 0)
            $sBuf = 'nol';
        else {
            // handling large number > 2 milyar
            $sBufL = '';
            $sNumber = strval($iNumber);
            $nNumberLen = strlen($sNumber);
            if ($nNumberLen > 9) { // large number
                $sNewNumber = substr($sNumber, $nNumberLen - 9, 9);
                //echo "sNewNumber [$sNewNumber]\n";
                $iNumber = intval($sNewNumber);
                //echo "iNumber [$iNumber]\n";
                // trilyun
                $iLargeNumber = intval(substr($sNumber, 0, $nNumberLen - 9));
                //echo "iLargeNumber [$iLargeNumber]\n";
                $iResult = intval($iLargeNumber / 1000);
                //echo "iResult [$iResult]\n";
                if ($iResult > 0)
                    $sBufL = sprintf("%s trilyun ", $this->SayHundreds($iResult));

                // milyar
                $iLargeNumber %= 1000;
                $iResult = $iLargeNumber;
                if ($iResult > 0)
                    $sBufL .= sprintf("%s milyar ", $this->SayHundreds($iResult));
            }
            //echo "[$sBufL]\n";
            // miliar
            $iResult = intval($iNumber / 1000000000);
            if ($iResult > 0)
                $sBuf .= sprintf("%s miliar ", $this->SayHundreds($iResult));
            $iNumber %= 1000000000;
            // juta
            $iResult = intval($iNumber / 1000000);
            if ($iResult > 0)
                $sBuf .= sprintf("%s juta ", $this->SayHundreds($iResult));
            $iNumber %= 1000000;
            // ribu
            $iResult = intval($iNumber / 1000);
            if (($iResult > 0) && ($iResult != 1))
                $sBuf .= sprintf("%s ribu ", $this->SayHundreds($iResult));
            else if ($iResult >= 1)
                $sBuf .= "seribu ";
            $iNumber %= 1000;
            // ratus
            if ($iNumber > 0)
                $sBuf .= $this->SayHundreds($iNumber);

            // final
            //echo "[$sBufL] [$sBuf]\n";
            $sBuf = $sBufL . $sBuf;
        }

        $angka = trim($sBuf);
        $koma = trim($this->comma($number));
        $terbilang = empty($angka)?"":$angka." ";
        $terbilang.= empty($koma)?"":$koma." ";
        $terbilang = empty($terbilang)?"":$terbilang."rupiah";
        return $terbilang;
    }

	// end of SayInIndonesian

    private function comma($number) {
		$after_comma = stristr($number, '.');
		$arr_number = array(
			"nol",
			"satu",
			"dua",
			"tiga",
			"empat",
			"lima",
			"enam",
			"tujuh",
			"delapan",
			"sembilan"
		);

        $results = "";
        $length = strlen($after_comma);
        $i = 1;
        while ($i < $length) {
            $get = substr($after_comma, $i, 1);
            $results .= " " . $arr_number[$get];
            $i++;
        }
        $results = trim($results);
        return ($i != 1 && $after_comma != "00") ? $results = " koma {$results}" : "";
    }
}
?>
