<?php
foreach($_POST as $key => $value)
{
    echo("<input type='hidden' class='variable' name='" .$key. "' value='" .$value. "' style:'display: none;'>");
}

if (isset($_POST['scrollX']) OR isset($_POST['scrollY'])) {
    echo("function moveToPreviousScrollPos() { window.scrollBy(");
        if (isset($_POST['scrollX'])) {
            echo($_POST['scrollX']);
        }
        else{
            echo("0");
        }
        echo(", ");
        if (isset($_POST['scrollY'])) {
            echo($_POST['scrollY']);
        }
        else{
            echo("0");
        }
        echo(");}");
    echo(" addEventListener('load', moveToPreviousScrollPos);");
} 
?>
 
?>