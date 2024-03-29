<!DOCTYPE html>
<?php require_once '../Resources/PHP/sessionLogic.php'; restrictAccess(4, $GLOBALS['role'])?>
<html>
    <head>
        <title>Active Servers</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
        <link rel="stylesheet" href="../Resources/CSS/waitListStructure.css">
        <script src= '../Resources/JavaScript/display.js'></script>
        <script>
            function allElementsLoaded() {
                let selectedServer = varGet("selectedServer");
                if (selectedServer !== undefined) {
                    let server = document.getElementById(selectedServer);
                    if (server != null) {
                        server.classList.add("selected");
                    }
                    else {
                        varRem("selectedServer");
                    }
                }
                var activeServers = document.getElementsByClassName("activeServer");
                for (var i = 0; i < activeServers.length; i++) {
                    activeServers[i].addEventListener("pointerdown", serverClicked);
                }
                
                // code that allows retention of scrollbar location between refreshes
                let x = varGet("scrollX");
                let y = varGet("scrollY");
                if (x !== undefined) {
                    window.scroll({
                        top: y,
                        left: x,
                        behavior: "smooth",
                    });
                }

                window.addEventListener('scroll', function(event) {
                    varSet("scrollX", window.scrollX);
                    varSet("scrollY", window.scrollY);
                }, true);

                setTimeout(() => {
                    updateDisplay();
                }, 60000);
            }

            function serverClicked() {
                if (this.classList.contains("selected")) {
                    this.classList.remove("selected");
                    varRem("selectedServer");
                    varRem("employeeId");
                }
                else {
                    var selectedServer = document.getElementsByClassName("selected");
                    if (selectedServer.length > 0) {
                        selectedServer[0].classList.remove("selected");
                    } 
                    this.classList.add("selected");
                    varSet("selectedServer", this.id);
                    varSet("employeeId", this.id.substring(6));
                }
            }
        </script>
        <style>
            .numberCell{
                text-align:right;
            }
           
        </style>
    </head>
    <body onload="allElementsLoaded()" class="intro">
        <legend onpointerdown="updateDisplay()">Active&nbsp;Servers</legend>
        <form action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method = "post">
            <table>
                <tr>
                <th>server</th><th class="numberCell">ppl count</th class="numberCell"><th>tbl count</th>
                </tr>
                <?php
                    //this sql statement joins the employees table, the tableassignments table, and a temporary table produced by filtering out all unseated or closed tickets from the tickets table)
                    //it then aggregates the sum of every active ticket's party size and the amount of tables assigned, grouped by the server's name,
                    //afterwards it filters out any employees that aren't currently logged in,
                    //and finally sorts the records first by the amount of customers being served ascending, then by the amount of tables being served ascending.
                    $sql = "SELECT employees.username as servername, employees.id as serverid, IFNULL(SUM(activetickets.partysize),0) AS pplcount, COUNT(activetickets.partysize) as tblcount
                            FROM ((Employees 
                            LEFT JOIN tableassignments ON employees.id = tableassignments.employeeId) 
                            LEFT JOIN(SELECT * FROM Tickets WHERE timeSeated IS NOT NULL AND timeClosed IS NULL) AS activetickets 
                            ON tableassignments.tableId = activetickets.tableId)
                            WHERE employees.id IN (SELECT employeeId FROM ActiveEmployees WHERE employeeRole & 2 = employeeRole)
                            AND loggedIn(employees.username)
                            GROUP BY servername
                            ORDER BY pplcount ASC, tblcount ASC;";
                    $result = connection()->query($sql);
                    //fetches each record and prints it as a table row in HTML. 
                    while($activeServer = $result->fetch_assoc() ){
                        echo ("<tr class='activeServer' id='server" .$activeServer['serverid']. "'>
                               <td>".$activeServer['servername']."</td>
                               <td class= \"numberCell\">".$activeServer['pplcount']."</td>
                               <td class= \"numberCell\">".$activeServer['tblcount']."</td></tr>");
                    }

                ?>
            </table>
            <?php require_once '../Resources/PHP/display.php'; ?>
        </form>
    </body>
</html>