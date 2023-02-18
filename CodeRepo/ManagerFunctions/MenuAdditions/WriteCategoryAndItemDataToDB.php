<!-- NOT GETTING THE entry-cat FROM THE FORM! REFER TO CSC289-GITHUB -->


<?php

include '..\..\Resources\php\connect_disconnect.php';

$entryType = $_POST['entry-type-form'];
$numEntries = $_POST['numEntries'];

// Possible characters 'c' = category,'i' = item,'m' = modification
$incrementQC = 0;

for ($i=0; $i<$numEntries; $i++) {
    if ($entryType == 'category') {  
        $title = $_POST['entry-title' . strval($i)];

        // CREATE QUICKCODE
        // INCREMENT QC FOR UNIQUE VALUE
        $sql = "SELECT SUBSTRING(quickCode, 2, LENGTH(quickCode) - 1)
        AS quickCode
        FROM menucategories
        WHERE NOT quickCode = 'root';";
        $result = connection()->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $incrementQC = $row['quickCode'];
            }
        }
        $quickCode = 'c' . intval($incrementQC)+1;

        $category = $_POST['entry-cat'];

        $sql = "SELECT quickCode FROM 
        menucategories WHERE title = '" . $category . "';";
        $result = connection()->query($sql);
      
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $parentQC = $row['quickCode'];
            }
        }
        else {
            echo "No Rows Found";
        }

        $sql = "INSERT INTO menucategories (quickCode, title) 
        VALUES ('" . $quickCode . "', '" . $title . "');";
        connection()->query($sql);

        $sql = "INSERT INTO menuassociations (parentQuickCode, childQuickCode) 
        VALUES ('" . $parentQC . "', '" . $quickCode . "');";
        connection()->query($sql);   
    }

    elseif ($entryType == 'item') {
        $title = $_POST[('entry-title' . strval($i))];
        $price = $_POST[('entry-price' . strval($i))];

        // CREATE QUICKCODE
        // INCREMENT QC FOR UNIQUE VALUE
        $sql = "SELECT SUBSTRING(quickCode, 2, LENGTH(quickCode) - 1)
        AS quickCode
        FROM menuitems
        WHERE NOT quickCode = 'root';";
        $result = connection()->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $incrementQC = $row['quickCode'];
            }
        }
        $quickCode = 'i' . intval($incrementQC) + 1;

        // get category
        $category = $_POST['entry-cat'];

        $sql = "INSERT INTO menuitems (quickCode, title, price) 
        VALUES ('" . $quickCode . "', '" . $title . "', '" . $price . "');";
        connection()->query($sql);

        $category = $_POST['entry-cat'];

        $sql = "SELECT quickCode FROM 
        menucategories WHERE title = '" . $category . "';" ;
        $result = connection()->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $parentQC = $row['quickCode'];
            }
        }

        $sql = "INSERT INTO menuassociations (parentQuickCode, childQuickCode) 
        VALUES ('" . $parentQC . "', '" . $quickCode . "');";
        connection()->query($sql);
    }

    elseif ($entryType == 'mod') {
        $modTitle = $_POST[('entry-title' . strval($i))] ?? null;           
        $itemTitle = $_POST[('entry-item-title' . strval($i))] ?? null;         
        $mandatoryOrOptional = $_POST[('entry-mand_opt' . strval($i))] ?? null;


// $parentQC isnt being defined because $itemTitle is not being passed in. **** FIX THIS ****

        echo "ITEM TITLE" . $itemTitle;

        // CREATE QUICKCODE
        // INCREMENT QC FOR UNIQUE VALUE
        $sql = "SELECT SUBSTRING(quickCode, 2, LENGTH(quickCode) - 1)
        AS quickCode
        FROM menumodificationcategories
        WHERE NOT quickCode = 'root';";
        $result = connection()->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $incrementQC = $row['quickCode'];
            }
        }
        $quickCode = 'm' . intval($incrementQC) + 1;

        $sql = "INSERT INTO menumodificationcategories (quickCode, title, categorytype)
        VALUES ('" . $quickCode . "', '" . $modTitle . "', '" . $mandatoryOrOptional . "');";
        connection()->query($sql);

        $sql = "SELECT quickCode FROM
        menuitems WHERE title = '". $itemTitle ."';";
        $result = connection()->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $parentQC = $row['quickCode'];
                echo $parentQC;
            }
        }


        
        $sql2 = "INSERT into menuassociations (parentQuickCode, childQuickCode)
        VALUES ('" .$parentQC. "', '" .$quickCode. "');";
        connection()->query($sql2);
    }
}
echo " Completed Entries";
?>
