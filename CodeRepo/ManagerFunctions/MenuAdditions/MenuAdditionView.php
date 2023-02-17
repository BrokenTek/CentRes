<!DOCTYPE html>
<html>
<head>
    <title>Add Items To Menu</title>
    <meta charset="utf-8">
    <script src='MenuAdditionEntryPopulation.js'></script>
</head>
<body>
<!-- Add root so that this script works IF ROOT DOES NOT EXIST, NOTHING CAN BE ADDED. This is done below -->
<?php
    // INCLUDE connect() FOR USE IN FILE(s)
    include '..\..\Resources\php\connect_disconnect.php';

    // LOOK FOR EXISTINCE OF 'root' IN THE 'quickcodes' TABLE.
    $exists = false;
    $sql = "SELECT id FROM quickcodes WHERE FIND_IN_SET('root', id) < 0";
    $result = connection()->query($sql);

    // SET $EXISTS TO true IF ANYTHING IS RETURNED
    if ($result->num_rows == 0) {
        $exists = true;
    }

    // ADD 'root' TO 'menucategories' WITH A 'title' OF 'root' AND 
    //  'quickcode' OF 'root' ONLY IF IT DOES NOT EXIST ALREADY.
    //  THIS VALUE MUST EXIST TO CREATE ROOT CATEGORIES!!!
    if ($exists != true) {
        $sql = "INSERT INTO menucategories (quickCode, title)
        VALUES ('root', 'root');";
        connection()->query($sql);
    }

?>


<!-- Populate Hidden Field With String Of Existing Categories -->
    <?php
        // include '../Resources/php/connect_disconnect.php';

        // Get All Menu Categories
        $sql = "SELECT * FROM menucategories WHERE quickcode != 'root';";
        $result = connection()->query($sql);

        echo "<p id='list-of-categories' style='display: none;'>";
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {  
                echo $row['title']. ",";              
            }
        }
        echo "</p>";
    ?>

    <?php
        // Get ALL Menu Items
        $sql = "SELECT quickCode, title FROM menuitems";
        $result = connection()->query($sql);

        echo "<p id='list-of-items' style='display:none;'>";
        if ($result->num_rows >0) {
            while ($row = $result->fetch_assoc()) {
                echo $row['title']. ",";
            }
        }
        echo "</p>";
    ?>

<h3 id='header-text'>Add New Menu Categories, Sub-Categories and Menu Items Here</h3>


<!-- *THIS IS THE FIRST BLOCK ON PAGE LOAD THAT DISPLAYS* Input Type Selection: Menu Category or Menu Item -->
<div id='selection-category-or-item'>
<h5 id='dropdown-prompt'><em>Choose An Option From The Dropdown List</em></h5>
    <select id='category-or-item'>
        <option value='not-chosen'>Select Type</option>
        <option id='cat_sub_select' value='category'>Menu Category/Sub-Category</option>
        <option id='item_select' value='item'>Menu Item</option>
    </select>
    <br><br>
    <button type='button' id='category-or-item-choice'>Enter Data For This Field</button>

</div>

<div id='get-items-btn'>
<br><hr><br>
<button type='button' id='mod-choice'>Enter New Modifications</button>
</div>

<div id='selection-item-type' style='display: none;'>
<!-- THIS IS A BUTTON FOR ADDING MODIFICATIONS -->
    <span id='mod' value='mod'></span>
    <h3 id='mod-header-text'>Add New Modifications Here</h3>

<!-- This is a dropdown of all items -->   
    <p><em>Existing Item List</em></p>
    <select id='root_item-type'>
        <!-- populated via sql query above and applied by JS function: GetModAssociationToItem() -->
    </select>
    <button type='button' id='create-mods-for-item'>Create Mods For This Item</button>
</div>


<div id='root_or_sub_div' style='display: none;'>
    <select id='root_or_sub_input' name='root_or_sub_name'>
    <!-- Populated With JavaScript. Options are Root/Main Category or Sub Category -->
    </select>
    <button type='button' id='category-type'>Select This Category Type</button>
</div>


<!-- Category Selection Block: Shows after selection of 'menu item (item)' or 'sub category (sub)' from first block -->
<div id='selection-entry-type' style='display: none;'>
    <p><em>Existing Category List</em></p>   
        <select id='root_entry-type'>
        </select>
    <button type='button' id='category-selected' value='cat'>Select This Category</button>
</div>

<!-- Number Of Items To Add  -->
<div id='selection-populatable-fields' style='display: none;'>
    <p><em>Select A Number To Display Multiple Entry Fields</em></p>
        <select id='entry-count' style='margin:5px;'>
        </select>
        <button type='button' id='entry-count-chosen' value='Create Entries'>Create Entries</button>
        <button type='button' id='entry-count-chosen_mod' value='Create Entries Mods' style='display:none;'>Create Mod Entries</button>
        <br>
</div>

<!-- Number Of Entries Shown Is Based On The 'Select The Number Of Items You Would Like To Add' Choice -->
<div id='form-for-entries' style='display:none;'>
    <form id='root_entry-field' action='WriteCategoryAndItemDataToDB.php' method='post'>
        <!-- Populated Via JS Script 'MenuAdditionEntryPopulation' -->
        <input id='num-of-entries-form' type='text' style='display:none;' name=''>
        <input id='entry-type-from_root' type='text' style='display:none;' name=''>
        <input id='submit-form-btn' type='submit' value='Add These Entries'>
    </form>
<div>

<script>
    document.getElementById('entry-count-chosen').addEventListener('pointerdown', EntryPopulation);
    document.getElementById('category-type').addEventListener('pointerdown', GetCategoryChoice);
    document.getElementById('category-or-item-choice').addEventListener('pointerdown', GetTypeDataToInput)
    document.getElementById('category-selected').addEventListener('pointerdown', ShowEntryNumSelection);   
    document.getElementById('mod-choice').addEventListener('pointerdown', GetModAssociationToItem);
    document.getElementById('create-mods-for-item').addEventListener('pointerdown', GetEntryPopNum);
    document.getElementById('entry-count-chosen_mod').addEventListener('pointerdown', ModPopulation);
    
    for (var i=1; i<101; i++) {
        var EntriesNumber = document.getElementById('entry-count');
        var Entries = document.createElement('option');
        Entries.setAttribute('value', i);
        Entries.appendChild(document.createTextNode(i));
        EntriesNumber.appendChild(Entries);
    }

      
</script>

</body>
</html>