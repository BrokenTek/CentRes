<?php echo '
			<span class="order-item" id="orderItem22101107">
				
				<span class="remove-order-item-btn">X</span>
				
				<span class="order-item-number">107</span>
				<span class="order-item-text">Royal Burger</span>
				<span class="order-item-price old-info">$8,888.88</span>
				<span class="order-item-override-note">Long Wait</span>
				<span class="order-item-override-price">$3.57</span>
				<span class="mod-text">Egg</span>
				<span class="mod-text">Cheese</span>
				<span class="mod-text">Extra Mayo</span>
				<span class="mod-custom">Karen wants exactly 1/2 pickle</span>
				
				<input type="hidden" name="ticketItemNumber[]" value="2210000"/>
				<input type="hidden" name="menuItem[]" value="BRG"/>
				<input type="hidden" name="customizationNotes[]" value="EGG,CHZ,MYO+,Karen wants exactly 1/2 pickle"/>
				<input type="hidden" name="seat[]" value="1"/>
				<input type="hidden" name="overidePrice[]" value="3.57"/>
				<input type="hidden" name="overideNote[]" value="Long Wait"/>
            </span>
			
			<span class="order-item" id="orderItem2004">
				
				<span class="remove-order-item-btn">X</span>
				
				<span class="order-item-number"></span>
				<span class="order-item-text">Hot Dog</span>
				<span class="order-item-price">$9.87</span>
				<span class="order-item-override-note"></span>
				<span class="order-item-override-price"></span>
				<span class="order-item-override-price"></span>
				<span class="mod-text"></span>
				<span class="mod-custom"></span>
				
				<input type="hidden" name="ticketItemNumber[]" value="4"/>
				<input type="hidden" name="menuItem[]" value="HDG"/>
				<input type="hidden" name="customizationNotes[]" value=""/>
				<input type="hidden" name="seat[]" value="3"/>
				<input type="hidden" name="overidePrice[]" value=""/>
				<input type="hidden" name="overideNote[]" value=""/>
            </span>
			
			
';
 
function loadticketItems( int $tableNumber = 0 ) {	
	# select all the existing ticket items in the ticket
	# SQL: SELECT * FROM TicketItems WHERE ticketID = $ticketNumber AND status <> 'Out-of-Date' AND status <> 'Removed');
	
	# loop through each TicketItem and echo each menuItem
}

?>