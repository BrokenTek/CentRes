<?php
$sql = "SELECT *
FROM MenuModificationCategories;";
$result = connection()->query($sql);

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {

    $sql2 = "SELECT *
        FROM MenuAssociations 
        INNER JOIN MenuModificationItems ON MenuModificationItems.quickCode = MenuAssociations.childQuickCode 
        WHERE MenuAssociations.parentQuickCode = '" .$row['quickCode']. "';";
    $result2 = connection()->query($sql2);

    echo("<fieldset id='" .$row['quickCode']. "' class='modCategory ");
    if ($row['categoryType'] == 'MandatoryAny' or $row['categoryType'] == 'MandatoryOne') {
      echo(" mandatoryMod'>");
    }
    else {
      echo(" optionalMod'>");
    }

    //TODO
    // If mod has a default price tagged with it, display it in the legend
    echo("<legend class='modCategoryTitle'>" .$row['title']. "</legend>");

    if ($row['categoryType'] == 'MandatoryOne' or $row['categoryType'] == 'OptionalOne') {
      echo("<label for='modCategory-" .$row['quickCode']. "'>Select One</label>
      <select id='modCategory-" .$row['quickCode']. "name='" .$row['quickCode']. "'>");
    
      while($row2 = $result2->fetch_assoc()) {
        //TODO
        // if mod price specified for item and not inherited from modCategory,
        // append it to the text to indicate and additional charge.
        echo("<option value='" .$row2['quickCode']. "'>" .$row2['title']. "</option>" );
      }
      echo("</select>");  
    }
    else {
      while($row2 = $result2->fetch_assoc()) {
        echo("<input type='checkbox' id='" .$row['quickCode']."-" .$row2['quickCode']. "' name='" .$row['quickCode']. "' value='" .$row2['quickCode']. "'/>");
        echo("<label for='" .$row['quickCode']."-" .$row2['quickCode']. "'>" .$row2['title']. "</label>");
      }
    }
    echo('</fieldset>');
  }
}

?>