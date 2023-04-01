<!-- DISPLAY TEMPLATE
This template includes starter code that allows
you to use display.php and displayInterface.js -->


<!-- ensures you are logged in before rendering page, and are logged in under the correct role.
If you aren't logged in, it will reroute to the login page.
If you are logged in but don't have the correct role to view this page,
you'll be routed to whatever the home page is for your specified role level -->
<?php require_once '../Resources/php/sessionLogic.php'; restrictAccess(8, $GLOBALS['role']); ?>

<!DOCTYPE html>
<?php require_once '../Resources/php/connect_disconnect.php'; ?>
<html>
    <head>
        <link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
        <style>
            body * {
                background-color: black;
                color: white;
                font-size: 1.25rem;
            }
            #frmEditor {
                width: 100vw;
                height: 100vh;
            }
            legend {
                font-size: 2rem;
                margin-inline: auto;
            }
        </style>
        <!-- gives you access to varSet, varGet, varRem, 
        varClr, updateDisplay, rememberScrollPosition, and forgetScrollPosition -->
        <script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script> 
        
        <!-- demonstration on how to use varGet, varSet, updateDisplay for just this page -->
        <!-- remove this script tag -->
        <script>
            function allElementsLoaded() {
            
            }

            //Place your JavaScript Code here
        </script>
    </head>
    <body id="sessionForm" onload="allElementsLoaded()">
        <!-- this form submits to itself -->
        <form id="frmEditor" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <fieldset>
                <?php if (isset($_POST['selectedMenuCategory'])): ?>
                    <legend>Menu&nbsp;Category</legend>
                    <?php
                        if (isset($_POST['commit'])) {

                        }

                    ?>
                    <label for="titleList">Title</label>
                    <select name="titleList" id="titleList">
                        <?php
                            // check if you added/edited a category
                            if (isset($_POST['titleList'])) {
                                if ($_POST['titleList'] == "newMenuCategory") {
                                    //$sql = "INSERT INTO MenuCategories "
                                }
                                else {

                                }
                            }

                        ?>
                    </select>
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" <?php if (isset($_POST['title'])) { echo("value='".$_POST['title']."'"); } ?> required><br><br>
                    <label for="parentCategory">Root Category:</label>
                    <select name="parentMenuCategory">
                    <?php
                        $sql = "SELECT * FROM MenuCategories";
                        $result = connection()->query($sql);
                        if (mysqli_num_rows($result) > 0) {
                            while($category = $result->fetch_assoc()) {
                                if (isset($_POST['parentMenuCategory']) && $_POST['parentMenuCategory'] == $category['quickCode']) {
                                    echo("<option value='" .$category['quickCode']. "' selected>" .$category['title']. "</option>");
                                }
                                else {
                                    echo("<option value='" .$category['quickCode']. "'>" .$category['title']. "</option>");
                                }
                            }
                        }
                    ?>
                    </select>
                    <label for="catRoute">Route</label>
                    <input type="text" name="catRoute" id="catRoute">
                    <label for="hidden">Hidden</label>
                    <input type="checkbox" name="hidden" id="hidden"><br><br>
                    <input type="submit" class="button">
                <?php endif; ?>
            </fieldset>
            <?php unset($_POST['thisVariableIWantToForget'], $_POST['thisOtherVariableIDontNeed']) ?>

            <!-- retain any POST vars. When updateDisplay() is called or the form is submitted,
            these variables will be carried over -->
            <?php require_once '../Resources/php/display.php'; ?>
           
        </form>
    </body>
</html>