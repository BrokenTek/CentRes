<?php
foreach($_POST as $key => $value)
{
    echo("<input type='hidden' class='variable' name='" .$key. "' value='" .$value. "' style:'display: none;'>");
}
?>