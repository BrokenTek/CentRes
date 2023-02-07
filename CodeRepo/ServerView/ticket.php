
<html>
<form id = "ticketForm" action="ticket.php" method="post" class = "ticketForm">
<link rel="stylesheet" href="../Resources/CSS/ticketStructure.css">
<link rel="stylesheet" href="../Resources/CSS/ticketStyle.css">
    <?php
        include '../Resources/php/connect_disconnect.php';
		if (isset($_POST['command'])) {
			try {
				if ($_POST['command'] == 'add' ) {
					$sql = "CALL createTicketItem(" .$_POST['ticket']. ", "
                                                      .$_POST['seat']. ", "
                                                     .$_POST['split']. ", '"
                                                   .$_POST['menuItem']. "');";
				}
				elseif ($_POST['command'] == 'modify') {
					$sql = "CALL modifyTicketItem("        .$_POST['ticketItem']. ", '" 
                                                    .$_POST['modificationNotes']. "');";
				}
				elseif ($_POST['command'] == 'override') {
					$sql = "CALL overrideTicketItemPrice("            .$_POST['ticketItem']. ", " 
                                                                   .$_POST['overrideValue']. ", '"
                                                                    .$_POST['overrideNote']. "','" 
                                                           .$_POST['authorizationUsername']. "');";
				}
				elseif ($_POST['command'] == 'remove') {
					$sql = "CALL removeTicketItem(" .$_POST['ticketItem']. ");";
				}
				elseif ($_POST['command'] == 'cancelPending') {
					$sql = "CALL cancelPendingTicketItems(" .$_POST['ticket']. ");";
				}
				elseif ($_POST['command'] == 'submitPending') {
					$sql = "CALL submitPendingTicketItems(" .$_POST['ticket']. ");";
				}
                elseif ($_POST['command'] == 'moveToSeat') {
					$sql = "CALL moveTicketItemToSeat(" .$_POST['ticketItem']. ", "
                                                           .$_POST['toSeat']. ");";
				}
                elseif ($_POST['command'] == 'moveToSplit') {
					$sql = "CALL moveTicketItemToSplit(" .$_POST['ticketItem']. ", " 
                                                              .$_POST['fromSplit']. ", "
                                                           .$_POST['toSplit']. ");";
				}
                elseif ($_POST['command'] == 'removeFromSplit') {
					$sql = "CALL removeTicketItemFromSplit(" .$_POST['ticketItem']. ", " 
                                                                  .$_POST['fromSplit']. ");";
				}
                elseif ($_POST['command'] == 'addToSplit') {
					$sql = "CALL addTicketItemToSplit(" .$_POST['ticketItem']. ", " 
                                                      .$_POST['toSplit']. ");";
				}
                elseif ($_POST['command'] == 'markAsReady') {
					$sql = "CALL markTicketItemAsReady(" .$_POST['ticketItemNumber']. ");";
				}
                connection()->query($sql);
					
			}
            catch (Exception $e) {
                $errorMessage = $e->getMessage();
            }				
		} 
        
        if (isset($_POST['ticket'])) {
            $sql = "SELECT * FROM ticketItems WHERE ticketId =" .$_POST['ticket'];
            if (isset($_POST['seat']) and isset($_POST['split'])) {
                $sql += " AND seat=" .$_POST['seat']. " AND split=" .$_POST['split']. ";";
            }
            elseif (isset($_POST['seat'])) {
                $sql += " AND seat=" .$_POST['seat']. ";";
            }
            elseif (isset($_POST['split'])) { 
                $sql += " AND split=" .$_POST['split']. ";";
            }
            $ticketItems = connection()->query($sql);

            while($ticketItem = $ticketItems->fetch_assoc()) {
                echo($ticketItem['id']);
            }


        }
        else {
            echo("<H1 id='test'>No Tickets Selected</H1>");
        }

        

    ?>
</form>
</html>