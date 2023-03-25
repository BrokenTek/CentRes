<!-- DISPLAY TEMPLATE
This template includes starter code that allows
you to use display.php and displayInterface.js -->


<!-- ensures you are logged in before rendering page, and are logged in under the correct role.
If you aren't logged in, it will reroute to the login page.
If you are logged in but don't have the correct role to view this page,
you'll be routed to whatever the home page is for your specified role level -->
<?php require_once '../Resources/php/sessionLogic.php'; restrictAccess(8, $GLOBALS['role']); ?>
<!-- CHANGE 255 TO THE ALLOWED ROLE LEVEL FOR THE PAGE -->

<!DOCTYPE html>
<?php require_once '../Resources/php/connect_disconnect.php'; ?>
<html>
    <head>
        <link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
        <link rel="stylesheet" href="../Resources/CSS/ticketStyle.css">
        <style>
            #sessionBody {
                background-color: black;
            }
            #employeeRoster {
                display: grid;
                grid-template-columns: 1.5rem .125rem 1.5rem .125rem 1.5rem .125rem min-content min-content min-content min-content min-content 4.875rem;
                grid-auto-rows: 1.5 rem;
            }

            .hidden {
                display: none;
            }

            .colLastName, #lblLastname, #txtLastName {
                grid-column: 7;
            }

            .colFirstName, #lblFirstName, #txtFirstName {
                grid-column: 8;
            }

            .colUsername, #lblUsername, #txtUsername {
                grid-column: 9;
            }

            .colRole, #lblRole, #cboRole {
                grid-column: 10;
            }

            .colPwd, #lblPwd, #pwdPwd {
                grid-column: 11;
            }

            #lblLastName {

            }

            #lblFirstName {

            }

            #lblUsername {

            }

            #lblRole {

            }

            #lblPwd {

            }

           #btnRemove, #btnCancel, #btnCommit {
                width: 1.5rem;
                max-width: 1.5rem;
                height: 1.5rem;
                margin: 0;
                padding: 0;
           }

            #btnRemove {
                grid-column: 1;
            }

            #btnCancel {
                grid-column: 3;
            }

            #btnCommit {
                grid-column: 5;
            }

            .
        </style>

        <!-- gives you access to setVar, getVar, removeVar, 
        clearVars, updateDisplay, rememberScrollPosition, and forgetScrollPosition -->
        <script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script> 
        
        <script>
            function allElementsLoaded() {
                // ADD EVENT LISTENERS HERE
            }

            //Place your other JavaScript Code here
        </script>
    </head>
    <body onload="allElementsLoaded()">
        <!-- this form submits to itself -->
        <form  id="sessionForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <?php require_once "../Resources/php/sessionHeader.php"; ?>
            <div id="sessionBody">
                <!-- PLACE YOUR PHP STRUCTURE LAYOUT CODE HERE -->
                <div class="banner">Employee Roster</div>
                <br>
                <div id="employeeRoster">
                    <!-- CHANGE CLASS _hidden to hidden. Made visible so you can see then when coding -->
                    <button id="btnRemove" type="button" command="remove" class="_hidden">🗑</button>
                    <button id="btnCancel" type="button" command="cancel" class="_hidden">✗</button>
                    <button id="btnCommit" type="button" command="commit" class="_hidden">✓</button>
                    
                    <label id="lblLastName" for="txtLastName" class="colLastName">Last&nbsp;Name</label>
                    <label id="lblFirstName" for="txtFirstName" class="colFirstName">First&nbsp;Name</label>
                    <label id="lblUsername" for="txtUsername" class="colUsername">Username</label>
                    <label id="lblRole" for="cboRole" class="colRole">Role</label>
                    <label id="lblPwd" for="pwdPwd" class="colPwd">Password</label>

                    <input id="pwdPwd" type="password" class="_hidden colPwd">
                    <input id="txtLastName" type="text" class="_hidden colLastName">
                    <input id="txtFirstName" type="text" class="_hidden colFirstName">
                    <input id="txtUsername" type="text" class="_hidden colUsername">
                    <select id="cboRole" class="_hidden colRole">

                    <?php
                    // INSERT THE CODE TO GRAB EMPLOYEES FROM DATABASE HERE

                    // POPULATE THE EMPLOYEES HERE
                    ?>
                    
                        <?php
                            // POPULATE EMPLOYEE ROLES HERE
                        ?>
                    </select>
                </div>
                
            </div>
            <!-- If you want to forget/not carry over variables, use PHP unset function
            to remove these variables -->
            <?php unset($_POST['thisVariableIWantToForget'], $_POST['thisOtherVariableIDontNeed']) ?>

            <!-- retain any POST vars. When updateDisplay() is called or the form is submitted,
            these variables will be carried over -->
            <?php require_once '../Resources/php/display.php'; ?>
        </form>
    </body>
</html>