<?php
    function formatMenuTitle($title1, $title2 = null) {
        if (isset($title1)) {
            if (strpos('! ' . $title1, ' .') == 0) {
                $formattedTitle1 = " " . $title1;
            }
            else {
                $titleParts = explode(" ", $title1);
                $formattedTitle1 = "";
                for ($i = 0; $i < sizeof($titleParts); $i++) {
                    if (strpos(" " . $titleParts[$i], "..") == 1) {
                        $formattedTitle1 .= " ". substr($titleParts[$i], 1);
                    }
                    elseif (!strpos("! " . $titleParts[$i], " .") == 1) {
                        $formattedTitle1 .= " ". $titleParts[$i];
                    }
                }
            }   
        }
        else {
            $formattedTitle1 = "";
        }

        if (isset($title2)) {
            if (strpos('! ' . $title2, ' .') == 0) {
                $formattedTitle2 = " " . $title2;
            }
            else {
                $titleParts = explode(" ", $title1);
                $formattedTitle2 = "";
                for ($i = 0; $i < sizeof($titleParts); $i++) {
                    if (strpos(" " . $titleParts[$i], "..") == 1) {
                        $formattedTitle2 .= " ". substr($titleParts[$i], 1);
                    }
                    elseif (!strpos("! " . $titleParts[$i], " .") == 1) {
                        $formattedTitle2 .= " ". $titleParts[$i];
                    }
                }
            }   
        }
        else {
            $formattedTitle2 = "";
        }

        if ($formattedTitle1 != "" && $formattedTitle2 != "") {
            return substr($formattedTitle1, 1)  . ": " . substr($formattedTitle2, 1);
        }
        elseif ($formattedTitle1 != "") {
            return substr($formattedTitle1, 1);
        }
        elseif ($formattedTitle2 != "") {
            return substr($formattedTitle2, 1);
        }
        else {
            return "";
        }
    }
?>