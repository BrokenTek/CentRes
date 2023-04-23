



<?php require_once '../Resources/PHP/sessionLogic.php'; restrictAccess(8, $GLOBALS['role']); ?>


<!DOCTYPE html>
<?php require_once '../Resources/PHP/dbConnection.php'; ?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
        <style>
            #sessionForm {
            height: 95%;
            width: 100%;
		    }
            #sessionBody {
            
                display: grid;
                grid-template-areas: ". tabHeader ."
                                     ". fields    ."
                                     ". buttons   ."
                                     ". messages    .";
                grid-template-columns: 1fr min-content 1fr;
                grid-template-rows: min-content min-content min-content min-content;
                background-color: black;
                color: white;
                padding-bottom: 1rem;
                font-size: 1rem;
            }
            #sessionBody * {
                font-weight: bold;
                font-size: 1.25rem;
            }
            #tabHeader {
                grid-area: tabHeader;
                font-size: 2rem;
                font-weight: bold;
                margin: 1rem auto 3rem auto;
            }
            input[type='text'] {
                background-color: #777 !important;
                background-image: none !important;
            }
            #employeeRoster {
                grid-area:rosterTable;
            }
            #buttonSet{
                display:grid;
                grid-template-columns: min-content min-content min-content;
                
                grid-area:buttons;
                border:none;
                margin-inline: auto;
            }
            .hidden {
                display: none;
            }
            .highlighted, .message{
                grid-area:messages;
                margin-block: 1rem;
                
            }
            fieldset{
                grid-area:fields;
                align-content:end;
                display: grid;
                grid-template-columns: min-content min-content;
                grid-auto-rows: min-content;
                grid-gap: .25rem;
            }

            #roleField {
                width: 100%;
                margin: 0;
                padding: 0;
            }

            button, .button {
                max-width: 6rem;
                margin: auto auto auto auto;
            }


           

            
        </style>
        
        <script src="../Resources/JavaScript/display.js" type="text/javascript"></script> 
        

        <script>
            function allElementsLoaded() {
                if(varGet("defaultRole")){
                    let valueTarget = varGet("defaultRole");
                    let optionList = document.getElementsByTagName("option");
                    for(let i = 0; i < optionList.length; i++){
                        if(optionList[i].value==valueTarget){
                            optionList[i].setAttribute("selected", true);
                        }
                        else{
                            optionList[i].removeAttribute("selected");
                        }
                    }
                }
                document.getElementById("btnBack").addEventListener('pointerdown', function(){
                    window.location.href="EmployeeRoster.php";
                });

                setTitle("CentRes POS: Management Tools - Employee Account Management", "Management Tools");
            }



            //Place your JavaScript Code here
        </script>
    </head>
    <body onload="allElementsLoaded()">
        
        <form id="sessionForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <?php require_once "../Resources/PHP/sessionHeader.php"; ?>
            
            <div id="sessionBody">
            <?php
                if($_POST['mode'] == "Edit"){
                    $revertButtonText = 'Reset';
                    $commitButtonText = 'Update';
                    }
                else {
                    $revertButtonText = 'Clear';
                    $commitButtonText = 'Create';
                }
            //the string checked for using strpbrk, lists off all prohibited characters for certain fields
            $prohibitedChars = '"\\&;{}()[]<>)';
            if(isset($_POST['finished'])){
                try{
                    if(strpbrk($_POST['lastNameField'], $prohibitedChars)||
                        strpbrk($_POST['firstNameField'], $prohibitedChars)||
                        strpbrk($_POST['usernameField'], $prohibitedChars)){
                        throw new Exception('Names and Usernames cannot include any of the following characters: '.$prohibitedChars);
                    }
                    $firstClause='';
                    $actionClause='';
                    $conditionClause = '';
                    if($_POST['mode'] == "Edit"){
                        $firstClause = "UPDATE employees ";
                        $actionClause = "SET lastname = ?, firstname = ?, username = ?, rolelevel = ?";
                        if(isset($_POST['passphraseField'])){
                            $passphraseHash = strrev(password_hash($_POST['passphraseField'], PASSWORD_BCRYPT));
                            $actionClause = $actionClause.", passwordbcrypt = ?";
                        }
                        $conditionClause = " WHERE id = ".$_POST['selectedEmp'];
                        $successMessage = "Profile&nbsp;Updated&nbsp;:&nbsp;" . $_POST['usernameField'];
                    }
                    else{
                        $firstClause = "INSERT INTO employees (lastname, firstname, username, rolelevel, passwordbcrypt) ";
                        $passphraseHash = strrev(password_hash($_POST['passphraseField'], PASSWORD_BCRYPT));
                        $actionClause = "VALUES (?, ?, ?, ?, ?)";
                        $successMessage = "Profile&nbsp;Created&nbsp;:&nbsp;" . $_POST['usernameField'];
                    }
                    $sql = $firstClause.$actionClause.$conditionClause.";";
                    $sql = connection()->prepare($sql);
                    if(isset($_POST['passphraseField'])){
                        $sql->bind_param("sssis",$_POST['lastNameField'], $_POST['firstNameField'], $_POST['usernameField'], $_POST['roleField'], $passphraseHash);
                    }
                    else{
                        $sql->bind_param("sssi",$_POST['lastNameField'], $_POST['firstNameField'], $_POST['usernameField'], $_POST['roleField']);
                    }
                    if(!$sql->execute()){
                        throw new Exception('SQL statement failed silently, investigate.');
                        unset($successMessage);
                    }

                }
                catch(mysqli_sql_exception $e){
                    echo("<h1>".$e->getmessage()."</h1>");
                    if(str_contains($e->getmessage(), "null")){
                        $_POST['errorState'] = "Sorry, we can't add a user without a role.";
                    }
                    else{
                    $_POST['errorState'] = "this change cannot be made to the database. The username may already be in use.";
                    }
                }
                catch(Exception $e){
                    $_POST['errorState'] = $e->getMessage();
                }
                finally{
                unset($_POST['lastNameField'], $_POST['firstNameField'], $_POST['usernameField'], $_POST['roleField'], $_POST['passphraseField']);
                unset($_POST['finished']);
                }
            }
            
            if($_POST['mode'] == 'Edit'){
                $sql = "SELECT lastname, firstname, username, rolelevel FROM employees WHERE id = ".$_POST['selectedEmp'].";";
                $theEmployee = connection()->query($sql)->fetch_assoc();
                $_POST['defaultRole'] = $theEmployee['rolelevel'];
                echo("<div id='tabHeader'>Edit Employee Account</div>
                    <fieldset>
                    <label for='firstNameField'>First&nbsp;Name</label><input name='firstNameField' id='firstNameField' value='".$theEmployee['firstname']."' required></input>
                    <label for='lastNameField'>Last&nbsp;Name</label><input name='lastNameField' id='lastNameField' value='".$theEmployee['lastname']."' required></input>
                    <label for='usernameField'>Username</label><input name='usernameField' id='usernameField' value='".$theEmployee['username']."' required></input>
                    <label for='roleField'>Role</label><select name='roleField' id='roleField' value='".$theEmployee['rolelevel']."' required>");
                //generalize the list to an arbitrary number of role options
                $sql = "SELECT id, title FROM loginroutetable;";
                $result = connection()->query($sql);
                while($roleOption = $result->fetch_assoc()){
                    echo("<option value='".$roleOption['id']."'>".$roleOption['title']."</option>");
                }
                echo("</select>
                <label for='passphraseField'>Passphrase</label><input name='passphraseField' id='passphraseField' placeholder='Temporary Password' type='password'></input>
                </fieldset>"
                );
            }
            else{
                echo("<div id='tabHeader'>New Employee Account</div>
                    <fieldset>
                    <label for='firstNameField'>First&nbsp;Name</label><input name='firstNameField' id='firstNameField' required>
                    <label for='lastNameField'>Last&nbsp;Name</label><input name='lastNameField' id='lastNameField' required>
                    <label for='usernameField'>Username</label><input name='usernameField' id='usernameField' required>
                    <label for='roleField'>Role</label><select name='roleField' id='roleField' required>
                    <option value='' selected disabled hidden>Select a Role</option>");
                $sql = "SELECT id, title FROM loginroutetable;";
                $result = connection()->query($sql);
                while($roleOption = $result->fetch_assoc()){
                    echo("<option value='".$roleOption['id']."'>".$roleOption['title']."</option>");
                }
                echo("</select>
                    <label for='passphraseField'>Passphrase</label><input type='password' name='passphraseField' id='passphraseField'  placeholder='Temporary Password' required></input>
                    </fieldset>"
                );
            }
            
            disconnect();
            ?>
            <div id="buttonSet">
                <input type="submit" id="btnFinish" class="button" name="finished" value="<?php echo $commitButtonText; ?>">
                <input type="reset" id="btnReset" class="button" value="<?php echo $revertButtonText; ?>">
                <button id="btnBack">Back</button>
                
            </div>
            <?php
                if(isset($_POST['errorState'])){
                    echo('<div class="highlighted">'.$_POST['errorState']."</div>");
                    unset($_POST['errorState']);
                }
                if(isset($successMessage)){
                    echo('<div class="message">'.$successMessage."</div>");
                    unset($successMessage);
                }
            ?>
            </div>

            
            <?php require_once '../Resources/PHP/display.php'; ?>
           
        </form>
    </body>
</html>