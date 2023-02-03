<?php 



// echo '
// 			<span class="ticketItem" id="ticketItem22101107" data-pushToDB="">
				
// 				<span class="ticketItemStatus">ýþ6</span>
				
// 				<span class="ticketItemNumber">107</span>
// 				<span class="ticketItemText">Royal Burger</span>
// 				<span class="ticketItemPrice old-info">$8,888.88</span>
// 				<span class="ticketItemOverrideNote">Long Wait</span>
// 				<span class="ticketItemOverridePrice">$3.57</span>
// 				<span class="modText">Egg</span>
// 				<span class="modText">Cheese</span>
// 				<span class="modText">Extra Mayo</span>
// 				<span class="modCustom">Karen wants exactly 1/2 pickle</span>
				
// 				<input type="hidden" name="ticketItemNumber[]" value="2210000"/>
// 				<input type="hidden" name="menuItem[]" value="BRG"/>
// 				<input type="hidden" name="customizationNotes[]" value="EGG,CHZ,MYO+,Karen wants exactly 1/2 pickle"/>
// 				<input type="hidden" name="seat[]" value="1"/>
// 				<input type="hidden" name="overidePrice[]" value="3.57"/>
// 				<input type="hidden" name="overideNote[]" value="Long Wait"/>
//             </span>
			
// 			<span class="ticketItem" id="ticketItem2004">
				
// 				<span class="removeTicketItemBtn">X</span>
				
// 				<span class="ticketItemNumber"></span>
// 				<span class="ticketItemText">Hot Dog</span>
// 				<span class="ticketItemPrice">$9.87</span>
// 				<span class="ticketItemOverrideNote"></span>
// 				<span class="ticketItemOverridePrice"></span>
// 				<span class="modText"></span>
// 				<span class="modCustom"></span>
				
// 				<input type="hidden" name="ticketItemNumber[]" value="4"/>
// 				<input type="hidden" name="menuItem[]" value="HDG"/>
// 				<input type="hidden" name="customizationNotes[]" value=""/>
// 				<input type="hidden" name="seat[]" value="3"/>
// 				<input type="hidden" name="overidePrice[]" value=""/>
// 				<input type="hidden" name="overideNote[]" value=""/>
//             </span>
			
			
// ';
 
function loadticketItems( int $tableNumber = 0 ) {	
	# select all the existing ticket items in the ticket
	# SQL: SELECT * FROM TicketItems WHERE ticketID = $ticketNumber AND status <> 'Out-of-Date' AND status <> 'Removed');
	
	# loop through each TicketItem and echo each menuItem





}

?>