<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="../CSS/baseStyle.css">
        <script src="../JavaScript/displayInterface.js"></script>
        <script>
            function signalStatus(status) {
                setVar("status", status);
            }
        </script>
    </head>
    <body>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <?php
                include 'connect_disconnect.php';
                if(!isset($_POST['selectedItem'])) {
                    //echo("<script> signalStatus('await'); </script>");
                    echo("<H1>Waiting for <b>selectedItem</b> to be injected</H1>");
                    echo(var_dump($_POST));
                }
                else {
                    if (isset($_POST['newModValue'])) {
                        connection();
                        $sql = "CALL modifyTicketItem(" .$_POST['selectedItem']. ", '" .$_POST['newModValue']. "');";
                        echo($sql ."<br><br>");
                        connection()->query($sql);
                        //echo("<script> signalStatus('complete'); </script>");
                        //echo("<H1>Waiting for Server Window to redirect back to ticket item</H1>");
                    }
                    else {
                        
                        //===================================================================================================
                        echo("<h1>You mod window content goes here</h1>");

                        echo("<label for='txtModString'>Mod String w/ commas</label>
                        <input type='text' id='txtModString' name='newModValue'>
                        <input type='submit' value='Update Mods'>
                        <button type='button'>Cancel Update</button>");
                        //===================================================================================================
                    }
                                 
                }
                include 'display.php';
            ?>
        </form>
    </body>
</html>