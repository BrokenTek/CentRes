
<html>
<head>
<link rel="stylesheet" href="../CSS/baseStyle.css">
<link rel="stylesheet" href="../CSS/ticketStructure.css">
<link rel="stylesheet" href="../CSS/ticketStyle.css">
<script src="../JavaScript/displayInterface.js"></script>
<script>
    
    function loaded() {
        //initialize update id
        setDisplayVariable("lastUpdate", Date.now());

        //create ticket item select listeners
        var elements = document.getElementsByClassName('ticketItem');
        if (elements != null) {
            for (var i = 0; i < elements.length; i++) {
                elements[i].addEventListener('pointerdown',pointerDown);
                elements[i].addEventListener('pointerup', pointerUp);
            }
        }

        rememberScrollPosition();
    }
    addEventListener('load', loaded);
    
   

    const LONG_TIME_TOUCH_LENGTH = 250;
    var targetTicketItem = null;
    var longTouchEnabled = false;
    var longTouchTimer = null;
	function pointerDown() {
        if (this === undefined) { return; }
        targetTicketItem = this;
        targetTicketItem.classList.add("selected");
        if (getDisplayVariable("selectedTicketItem") != null) {
            longTouchTimer = setTimeout(longTouch, LONG_TIME_TOUCH_LENGTH);
        }
	}

    function longTouch() {
         longTouchEnabled = true;
         targetTicketItem.classList.add("multiselect");

        // if there is exactly 1 other item selected, make it multi-select as well.
         var alreadySelected = getDisplayVariable("selectedTicketItem");
         if (alreadySelected.indexOf(",") == -1) {
            document.getElementById(alreadySelected).classList.add("multiselect");
         }
    }

    function pointerUp() {
        if (targetTicketItem == null) { return; }
        
        if (longTouchTimer != null) {
            clearTimeout(longTouchTimer);
        }

        var oldSelectedItems = document.getElementsByClassName("ticketItem");
        if (!longTouchEnabled) {
    	    /*this iterates through the list returned, if there is no case where multiple items are selected concurrently,
    	    you can just use oldSelectedItems[0].classList.remove("selected"); instead*/
    	    for(let i = 0; i < oldSelectedItems.length; i++){
        	    oldSelectedItems[i].classList.remove("selected");
                oldSelectedItems[i].classList.remove("multiselect");
    	    }
    	    targetTicketItem.classList.add("selected");
            targetTicketItem.classList.remove("multiselect");
            setDisplayVariable("selectedTicketItem", targetTicketItem.id);
        }
        else {
            setDisplayVariable("selectedTicketItem", getDisplayVariable("selectedTicketItem") + "," + targetTicketItem.id); 
        }
        setDisplayVariable("lastUpdate", Date.now()); 
        targetTicketItem = null;
        longTouchEnabled = false;
    }

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
            $sql .= ";";

            $ticketItems = connection()->query($sql);

            while($ticketItem = $ticketItems->fetch_assoc()) {
                $sql = "SELECT * FROM menuItems WHERE quickCode = '" .$ticketItem['menuItemQuickCode']. "'";
                $menuItem = connection()->query($sql)->fetch_assoc();

                echo('<div class="ticketItem" id="ticketItem' .$ticketItem['id']. '">
                        <div class="ticketItemStatus"></div>
                        <div class="ticketItemNumber">' .$ticketItem['itemId']. '</div>
                        <div class="ticketItemText">' .$menuItem['title']. "</div>");
                if (is_null($ticketItem['overridePrice'])) {
                    echo('<div class="ticketItemPrice">' .$ticketItem['calculatedPrice']. '</div>');
                }
                else {
                    echo('<div class="ticketItemPrice old-info">' .$ticketItem['calculatedPrice']. '</div>');
                    echo('<div class="ticketItemOverrideNote">' .$ticketItem['overrideNote']. '</div>');
                    echo('<div class="ticketItemOverridePrice">');
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
                    echo('</div>');
                }
                if (!is_null($ticketItem['modificationNotes'])) {
                    $mods = explode(",", $ticketItem['modificationNotes']);

                    foreach ($mods as $modQuickCode) {
                        $sql = "SELECT * FROM MenuModificationItems WHERE quickCode = '" .str_replace("'", "''",$modQuickCode). "'";
                        $modItemRows = connection()->query($sql);
                        if (mysqli_num_rows($modItemRows) == 0) {
                            // custom mod, deleted mod. Treat as custom mod.
                            echo('<div class="modCustom">' .$modQuickCode. '</div>');
                        }
                        else {
                            $modItem = $modItemRows->fetch_assoc();
                            echo('<div class="modText">' .$modItem['title']. '</div>');
                            if (!is_null($modItem['priceOrModificationValue'])) {
                                echo('<div class="modPrice">');
                              
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
                                echo('</div>');
                            }

                        }
                    }
                }

                echo("</div>");

            }
        }
        else {
            echo("<H1 id='test'>No Ticket Selected</H1>");
        }
    ?>
    </form>
    </body>
    </html>
