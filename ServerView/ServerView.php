<html>
   <head>
	<link rel="stylesheet" href="serverStructure.css">
		
  	  <script type="text/javascript">
		function templateFunction( a ) { 
			alert("You override the template function in your file"); 
		}
		
		function reloadPage() {
			document.getElementById("form-container").submit();
		}
		
		function maxSeatNumber() { return 100; }
		function maxSplitNumber() { return 9; }
		function createMenuSelectEventHandlers() {}
		function selectMenuItem( id ) {}
		function selectTicketItem( id ) {}
		function moveTicketItem() {}
		function removeTicketItem() {}
		function stateChanged() { alert('State Changed');}
		function editTicketItem() {}
		function configureModificationWindow() {}
		function updateMenuItem() {}
  </script>
  <script src="../InDev/cwpribble.js"></script>
  <script src="../InDev/dbutshudiema.js"></script>
  <script src="../InDev/dlmahan.js"></script>
  <script src="../InDev/kcdine.js"></script>
  <script src="../InDev/sashort.js"></script>
  <script src="../InDev/OVERRIDEEXAMPLE.js"></script>
  
  <script>templateFunction("Hello World");
	alert(FIRSTNAME + ' ' + LASTNAME);</script>
   <body>
   <form id="serverContainer" action="ServerView.php" method="POST">
		<div id="serverViewHeader">
			<select id="cboTable" name="tableNumber" onchange="reloadPage()">
				<option value="">Select Table</option>
				<option value="1">Table 1</option>
				<option value="2">Table 2</option>
				<option value="3">Table 3</option>
			</select>
			<select id="cboSeat" name="seatNumber" onchange="stateChanged()">
				<option value="">Select Seat</option>
				<option value="1">Table 1</option>
				<option value="2">Table 2</option>
				<option value="3">Table 3</option>
			</select>
			<div id="headerButtonGroup">
				<input type="submit" value="SUBMIT">
				<button>CANCEL</button>
				<button>PRINT RECIEPT</button>
			</div>
		</div>
		<span id="serverViewContainer">
		
			<span id="menuTitle">Menu</span>
			<span id="menuContainer">
				<?php include '../Resources/php/session.php'; require "loadServerMenu.php"; ?>
			</span>
			<span id="ticketHeader">
				<span id="ticketHeaderText">asdfasdfadf</span>
				<select id="cboSplit">
					<option value="">Select Split</option>
					<option value="Split 1">Split 1</option>
					<option value="2">Split 2</option>
					<option value="3" style="background-color: red;">Split 3</option>
				</select>
			</span>
			<span id="ticketContainer">
				
				<?php require "loadTicket.php"; 	 
						if (!empty($_POST['tableNumber'])) {
							loadTicketItems($_POST['tableNumber']);
						}
				?>
				
			</span>
			<span id="ticketFooter">
				<button>Edit</button>
				<span></span>
				<button>Move To</button>
				<button>Share With</button>
				<select id="cboMoveTicketItem">
					<option value="">Select Split</option>
					<option value="Split 1">Split 1</option>
					<option value="2">Split 2</option>
					<option value="3" style="background-color: red;">Split 3</option>
				</select>
			</span>
			<span id="optionsContainer">

			</span>
		</span>
	  </form>

   </body>
</html>