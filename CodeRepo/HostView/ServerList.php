<!DOCTYPE html>
<?php require_once '../Resources/php/connect_disconnect.php'; ?>
<html>
    <head>
        <title>Active Servers</title>
        <link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
        <script src= '../Resources/JavaScript/displayInterface.js'></script>
        <style>
            .numberCell{
                text-align:right;
            }
            th{
                background-color:gray;
                border-left:none;
                border-right:none;
            }
            td{
                border-left:none;
                border-right:none;
            }
        </style>
    </head>
    <body>
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
                    $sql = "SELECT employees.username as servername, IFNULL(SUM(activetickets.partysize),0) AS pplcount, COUNT(tableassignments.tableID) as tblcount
                            FROM ((Employees 
                            LEFT JOIN tableassignments ON employees.id = tableassignments.employeeId) 
                            LEFT JOIN(SELECT * FROM Tickets WHERE timeSeated IS NOT NULL AND timeClosed IS NULL) AS activetickets 
                            ON tableassignments.tableId = activetickets.tableId)
                            WHERE employees.id IN (SELECT employeeId FROM ActiveEmployees WHERE employeeRole & 2 = 2)
                            GROUP BY servername
                            ORDER BY pplcount ASC, tblcount ASC;";
                    $result = connection()->query($sql);
                    //fetches each record and prints it as a table row in HTML. 
                    while($activeServer = $result->fetch_assoc() ){
                        echo ("<tr onmousedown=\"setVar('selectedServer', '".$activeServer['servername']."')\">
                                <td>".$activeServer['servername']."</td>
                                <td class= \"numberCell\">".$activeServer['pplcount']."</td>
                                <td class= \"numberCell\">".$activeServer['tblcount']."</td></tr>");
                    }

                ?>
            </table>
            <?php require_once '../Resources/php/display.php'; ?>
        </form>
    </body>
</html>