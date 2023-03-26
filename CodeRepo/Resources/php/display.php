<!-- ensures you are logged in before rendering page.
Otherwise will reroute to logon page -->
<?php
foreach($_POST as $key => $value)
{
    $value = str_replace("//", "/", $value);
    $value = str_replace("/*", "/", $value);
    $value = str_replace("*/", "*", $value);
    $value = str_replace("-- ", "- ", $value);
    $value = str_replace("#", "", $value);
    $value = str_replace(";", "", $value);
    $value = str_replace("'", "\'", $value);
    echo("<input type='hidden' class='variable' id=" .$key. " name='" .$key. "' value='" .$value. "' style:'display: none;'>");
}
echo("<input type='submit' style='display: none;' id='btnSubmit'>");



if (isset($_POST['scrollX']) OR isset($_POST['scrollY'])) {
    echo("<script>function moveToPreviousScrollPos() { window.scrollBy(");
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
    echo(" addEventListener('load', moveToPreviousScrollPos)</script>");
}

function getSqlOrderByClause($tableId) {
    $sql = "";
    $sortKeyPrefix = tableId + "_SortKey";
    $keyIndex = 1;
    while (true) {
        $key = $sortKeyPrefix + $keyIndex;
        
        if (!isset($_POST[$key])) {
            break;
        }
        $sql .= ", " . $_POST[$key];
        $keyIndex ++;
    }
    if ($sql != "") {
        $sql = " ORDER BY " . substr($sql, 2);
    }
    return $sql;

}

function sortKeyIndex($tableId, $columnId) {
    $sortKeyPrefix = tableId + "_SortKey";
    $keyIndex = 1;
    while (true) {
        $key = $sortKeyPrefix + $keyIndex;
        
        if (isset($_POST[$key])) {
            if(($_POST[$key] == $columnId . " ASC") || ($_POST[$key] == $columnId . " DESC") ) {
                return $keyIndex;
            }
        }
        $keyIndex++;
    }
    return -1;
}
?>