
<html>
<head>
<link rel="stylesheet" href="../CSS/baseStyle.css">
<link rel="stylesheet" href="../CSS/ticketStructure.css">
<link rel="stylesheet" href="../CSS/ticketStyle.css">
<script>
    function createTicketSelectEventHandlers() {
	var elements = document.getElementsByClassName("ticketItem");

	var myFunction = function() {
		var oldSelectedItems = document.getElementsByClassName("selected");
    	/*this iterates through the list returned, if there is no case where multiple items are selected concurrently,
    	you can just use oldSelectedItems[0].classList.remove("selected"); instead*/
    	for(let i = 0; i < oldSelectedItems.length; i++){
        	oldSelectedItems[i].classList.remove("selected");
    	}
    	this.classList.add("selected");
    	stateChanged();
	};
	for (var i = 0; i < elements.length; i++) {
	    elements[i].addEventListener('pointerdown', myFunction);
	}
}
addEventListener('load', createTicketSelectEventHandlers);
</script>
</head>
<body>
<form id = "ticketForm" action="ticket.php" method="post" class = "ticketForm">

    <?php
        include 'connect_disconnect.php';
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
                                                            .$_POST['toSplit']. ");";
				}
                elseif ($_POST['command'] == 'removeFromSplit') {
					$sql = "CALL removeTicketItemFromSplit(" .$_POST['ticketItem']. ", ";
				}
                elseif ($_POST['command'] == 'addToSplit') {
					$sql = "CALL addTicketItemToSplit(" .$_POST['ticketItem']. ", " 
                                                           .$_POST['toSplit']. ");";
				}
                elseif ($_POST['command'] == 'markAsReady') {
					$sql = "CALL markTicketItemAsReady(" .$_POST['ticketItem']. ");";
				}
                connection()->query($sql);
					
			}
            catch (Exception $e) {
                $errorMessage = $e->getMessage();
				echo('<h1>' .$errorMessage. '</h1>');
            }				
		} 
        
        if (isset($_POST['ticket'])) {
            $sql = "SELECT * FROM ticketItems WHERE ticketId =" .$_POST['ticket'];
            
            
            if (isset($_POST['seat'])) {
                $sql .= " AND seat=" .$_POST['seat'];
            }
            if (isset($_POST['split'])) {
                $bitMask = POW(2,$_POST['split']);
                $sql .= " AND splitFlag & " .$bitMask. " = "  .$bitMask;
            }
            $sql .= " ORDER BY id DESC;";

            $ticketItems = connection()->query($sql);

            while($ticketItem = $ticketItems->fetch_assoc()) {
                $sql = "SELECT * FROM menuItems WHERE quickCode = '" .$ticketItem['menuItemQuickCode']. "'";
                $menuItem = connection()->query($sql)->fetch_assoc();

                echo('<span class="ticketItem" id="ticketItem' .$ticketItem['id']. '">
                        <span class="ticketItemStatus"></span>
                        <span class="ticketItemNumber">' .$ticketItem['itemId']. '</span>
                        <span class="ticketItemText">' .$menuItem['title']. "</span>");
                if (is_null($ticketItem['overridePrice'])) {
                    echo('<span class="ticketItemPrice">' .$ticketItem['calculatedPrice']. '</span>');
                }
                else {
                    echo('<span class="ticketItemPrice old-info">' .$ticketItem['calculatedPrice']. '</span>');
                    echo('<span class="ticketItemOverrideNote">' .$ticketItem['overrideNote']. '</span>');
                    echo('<span class="ticketItemOverridePrice">');
                    if ($ticketItem['overridePrice'] < 0) {
                        // discount applied
                        echo($ticketItem['overridePrice']);
                    }
                    elseif ( $ticketItem['overridePrice'] >= 1 ) {
                         // price set to a value
                         echo($ticketItem['overridePrice']);
                    }
                    elseif ( $ticketItem['overridePrice'] == 0 ) {
                        // free
                        echo('FREE');
                    }
                    else {
                        // percent discount applied
                       echo(((1 - $ticketItem['overridePrice']) * 100) ."% off");
                    }
                    echo('</span>');
                }
                if (!is_null($ticketItem['modificationNotes'])) {
                    $mods = explode(",", $ticketItem['modificationNotes']);

                    foreach ($mods as $modQuickCode) {
                        $sql = "SELECT * FROM MenuModificationItems WHERE quickCode = '" .str_replace("'", "''",$modQuickCode). "'";
                        $modItemRows = connection()->query($sql);
                        if (mysqli_num_rows($modItemRows) == 0) {
                            // custom mod, deleted mod. Treat as custom mod.
                            echo('<span class="modCustom">' .$modQuickCode. '</span>');
                        }
                        else {
                            $modItem = $modItemRows->fetch_assoc();
                            echo('<span class="modText">' .$modItem['title']. '</span>');
                            if (!is_null($modItem['priceOrModificationValue'])) {
                                echo('<span class="modPrice">');
                              
                                if ($modItem['priceOrModificationValue'] < 0) {
                                    // discount applied
                                    echo($modItem['priceOrModificationValue']);
                                }
                                elseif ( $modItem['priceOrModificationValue'] >= 1 ) {
                                     // price set to a value
                                     echo($modItem['priceOrModificationValue']);
                                }
                                elseif ( $modItem['priceOrModificationValue'] == 0 ) {
                                    // free
                                    echo('FREE');
                                }
                                else {
                                    // percent discount applied
                                   echo(((1 - $modItem['priceOrModificationValue']) * 100) ."% off");
                                }
                                echo('</span>');
                            }

                        }
                    }
                }

                echo("</span>");

            }
        }
        else {
            echo("<H1 id='test'>No Ticket Selected</H1>");
        }
    ?>