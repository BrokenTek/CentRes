<html>
   <head>
		<script type="text/javascript">

		function changeFunc() {
			var selectBox = document.getElementById("cboTable");
			var selectedValue = selectBox.value;
			var url = "?tableNumber=" + selectedValue; 			
			window.location.href = url;
		}
		
		function maxSeatNumber() { return 100; }
		function maxSplitNumber() { return 9; }
		function createMenuSelectEventHandlers() {}
		function selectMenuItem( id ) {}
		function orderItemMoved( id ) {}
		function orderItemRemoved( id ) {}		

  </script>
  <script src="sashort.js"></script>
  <script >helloWorld("ABCD");</script>
  
      <style>
         #menu-ticket-container {
			display: grid;
			grid-template-area: menu order-and-options;
			grid-template-columns: 1fr 1fr;
			border: 2px solid black;
         }
		 
		 
		 /* These 2 styles should be incorporated into the master CSS file */
		 .old-info {
         color: grey;
         text-decoration: line-through;
         }
		 
         .hidden {
         display: none;
         }
		 
		 /* ORDER ITEM STRUCTURE CSS */
         .order-item {
			display: grid;
			grid-template-columns: 2rem  2rem 1fr 5rem;
			border: 1px solid grey;
         }
         .remove-order-item-btn {
			grid-column: 1;
			background-color: red;
			color: white;
         }
         .order-item-number {
         grid-column: 2;
         }
         .order-item-text {
         grid-column: 3;
         font-weight: bold;
         }
         .order-item-price {
         grid-column: 4;
         }
         .order-item-override-note {
         grid-column: 3;
         color: red;
         }
         .order-item-override-price {
         grid-column: 4;
         color: red;
         }
         .mod-text {
         grid-column: 3;
         }	
         .mod-custom {
         grid-column: 3;
         }	
         
      </style>
   <body>
   <select id="cboTable" name="ticketNumber" action="ServerView.php" onchange="changeFunc();">
			<option value="">Select Table</option>
			<option value="1">Table 1</option>
			<option value="2">Table 2</option>
			<option value="3">Table 3</option>
		</select>
      <form id='menu-ticket-container'>
		
			
         <div id="menu-and-options-container">
            <?php require "loadServerMenu.php"; ?>
         </div>
         <div id="order-container">
            <?php require "loadTicket.php"; 
				if (!empty($_GET['tableNumber'])) {
					loadTicketItems($_GET['tableNumber']);
				}
			?>
         </div>
		 <div id="menu-options-container">
		 </div>
      </form>
   </body>
</html>