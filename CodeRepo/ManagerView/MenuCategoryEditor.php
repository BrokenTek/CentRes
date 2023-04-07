<?php require_once '../Resources/php/sessionLogic.php'; restrictAccess(8, $GLOBALS['role']); ?>
<?php require_once '../Resources/php/connect_disconnect.php'; ?>

<?php
    /////////////////////////////////////////////////////////
    //
    // NOTE: This page will be called from MenuEditor.php
    // $_POST['quickCode] will be passed in if you are editing
    // and existing menu object. Check if $_POST['menuTitle']
    // is set.
    // 
    // if it isn't, you're going to have to grab your
    // data form the db and set them into the appropriate
    // $_POST[] variables.
    // 
    // See the bottom of the file
    // for a list of needed vars referenced
    // in the unset() call.
    //
    /////////////////////////////////////////////////////////
    //
    // process $_POST['commit'] or $_POST['delete'] here.
    // if any errors occur, set a message in $errorMessage
    // to display at the bottom of the page.
    //
    // if you create a new item, make sure you set
    // $_POST['quickCode'] for what you just created.
    // look up the quickCode >>>> hint: all titles are unique 
    // per table
    //
    /////////////////////////////////////////////////////////

    try {
        if(isset($_POST['quickCode'])&&!isset($_POST['menuTitle'])){
            $sql = "SELECT MenuCategories.title AS 'categoryTitle', MenuAssociations.parentQuickCode AS 'parent'
                    FROM (MenuCategories LEFT JOIN MenuAssociations ON MenuCategories.quickCode = MenuAssociations.childQuickCode)
                    WHERE quickCode = '".$_POST['quickCode']."';";
            $fieldData = connection()->query($sql)->fetch_assoc();
            //just in case the quickCode persists after a deletion, these statements are wrapped in a condition
            //to prevent unwanted warnings from showing up. Note to self: unwrap this if you account for this.
            if(isset($fieldData)){
            $_POST['menuTitle'] = $fieldData['categoryTitle'];
            $_POST['parentCategory'] = $fieldData['parent'];
            }
        }

        if(isset($_POST['commit'])){
            if($_POST['commit'] == 'Update'){
                //attempt to update the category to reflect the changes made in the form
                $sql = "UPDATE MenuCategories SET title = ? WHERE quickCode = ?;
                         UPDATE MenuAssociations SET parentQuickCode = ? WHERE quickCode = ?;";
                $sql = connection()->prepare($sql);
                $sql->bind_param('ssss', $_POST['menuTitle'], $_POST['quickCode'] $_POST['parentCategory'], $_POST['quickCode']);
                $sql->execute();
            }
            else{
                //attempt to create the new category
                $sql = "INSERT INTO MenuCategories (title) VALUES (?);";
                $sql = connection()->prepare($sql);
                $sql->bind_param('s', $_POST['menuTitle']);
                $sql->execute();

                //get its new quick code and bind it to the $_POST variable.
                $sql2 = "SELECT quickCode FROM MenuCategories WHERE title = ?;";
                $sql2 = connection()->prepare($sql2);
                $sql2->bind_param('s', $_POST['menuTitle']);
                $sql2->execute();
                $_POST['quickCode'] = $sql2->get_result()->fetch_assoc()['quickCode'];

                //make the new association
                $sql3 = "INSERT INTO MenuAssociations (parentQuickCode, childQuickCode)
                        VALUES (".$_POST['parentCategory'].", ".$_POST['childCategory'].");";
                connection()->query($sql3);
            }
        }
        if(isset($_POST['delete'])){
            $sql = "UPDATE MenuCategories SET visible = FALSE WHERE quickCode = '".$_POST['quickCode']."';";
            connection()->query($sql);
            unset($_POST['quickCode']);
        }


    }
    catch (mysql_sql_exception $se){
        if($se->getSqlState()=='23000'){
            //sql state for when one attempts to duplicate a field with a UNIQUE or PRIMARY KEY constraint, or causes some other integrity violation.
            //TODO: discuss if the application should restore a hidden record here if adding a category with a title matching it.
            $errorMessage = "Sorry, we can't have multiple categories with the same title. Try using a different title.";
        }
        else{
            $errorMessage = "An unexpected error occurred, please contact your system administrator or developer(s). ".$se->getMessage();
        }
    }
    catch (Exception $e) {
        //in practice, the error message being appended would instead be sent to an error log of some sort.
        $errorMessage = "An unexpected error occurred, please contact your system administrator or developer(s). ".$e->getMessage();
    }
?>

<!DOCTYPE html>

<html>
    <head>
        <link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
        <style>
            #sessionForm {
                height: 100%;
                background-color: black;
            }
            .sessionBody {
                background-color: black;
                margin: auto auto auto auto;
                color: white;
                display: grid;
                grid-template-columns: 1fr min-content 1fr;
                height: 100%;
            }
            fieldset {
                display: grid;
                grid-auto-rows: min-content;
                grid-template-columns: max-content max-content;
                grid-gap: .125rem;
                grid-column: 2;
            }
            legend, #buttonGroup {
                grid-column: 1 / span 2;
            }
            legend {
                margin-inline: auto;
                line-height: 2rem;
                font-size: 2rem !important;
                margin-bottom: 1.5rem;
            }
            #selParentCategory {
                margin: 0;
            }
            #buttonGroup {
                display: grid;
                grid-template-rows: min-content;
                grid-template-columns: 1fr 1fr 1fr;
            }
            input[type="reset"] {
                grid-column: 2;
            }

            .button {
                background-color: #F6941D;
                color: white;
                font-weight: bold;
                margin: .25rem .25rem .25rem .25rem;
                min-width: 5rem;
                min-height: 2rem;
            }

        </style>
        <script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script> 
        <script>
            function allElementsLoaded() {
                // any startup tasks go here after page has fully loaded.
            }

        </script>
        
    </head>
    <body id="sessionForm" onload="allElementsLoaded()">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="sessionBody">
                <fieldset>
                    <legend>Menu&nbsp;Category&nbsp;Editor</legend>
                
                    <label for="txtMenuTitle">Category Title</label>
                    <input id="txtMenuTitle" name="menuTitle" required <?php if(isset($_POST['menuTitle'])) { echo(' value="' . $_POST['menuTitle'] . '"'); } ?>>
                    <label for="selParentCategory">Parent Category</label>
                    <select id="selParentCategory" name="parentCategory" required  <?php if(isset($_POST['parentCategory'])) { echo(' value="' . $_POST['parentCategory'] . '"'); } ?>>
                        <option value="root">None</option>
                        <?php
                            $sql = "SELECT * FROM MenuCategories ORDER BY title";
                            $result = connection()->query($sql);
                            if (mysqli_num_rows($result) == 0) {
                                $errorMessage = "Create a Menu Category Please!";
                            }
                            else {
                                /////////////////////////////////////////////////////////
                                // populate all MenuCategory from database.
                                // the values should be the quick quick code
                                /////////////////////////////////////////////////////////
                                while ($row = $result->fetch_assoc()) {
                                    echo('<option value="' .$row['quickCode']. '">' .$row['title']. '</option>');
                                }
                            }
                        ?>
                    </select>
                    <div id="buttonGroup">
                        <?php if (isset($_POST['quickCode']) && 
                                (!isset($_POST['delete']) || isset($errorMessage))): ?>
                            <input type="submit" name="delete" value="Delete" class="button">
                            <input type="reset" value="Reset" class="button">
                            <input type="submit" name="commit" value="Update" class="button">
                        <?php else: ?>
                            <input type="reset" value="Clear" class="button">
                            <input type="submit" name="commit" value="Create" class="button">
                        <?php endif; ?>
                    </div>
                    <?php if (isset($errorMessage)): ?>
                        <div class="errorMessage">
                            <?php echo $errorMessage; ?>
                        </div>
                    <?php endif; ?>
                </fieldset>
            </div>
            

            <?php unset($_POST['delete'], 
                        $_POST['commit'],
                        $_POST['menuTitle'],
                        $_POST['parentCategory']);  
                     // $_POST['quickCode'] stays ?>

            <!-- must be placed at the bottom of the form to submit values form one refresh to the next -->
            <?php require_once '../Resources/php/display.php'; ?>
           
        </form>
    </body>
</html>