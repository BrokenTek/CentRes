<?php require_once '../Resources/php/sessionLogic.php'; restrictAccess(4, $GLOBALS['role']); ?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
		<style>
			iframe {
				background-color: black;
			}
			.sessionBody {
				display: grid;
				grid-template-areas: "ifrServerList ifrRestaurantLayout ifrSelectedTable"
									 "ifrWaitList   ifrRestaurantLayout ifrTicket"
									 "ifrWaitTimes  ifrRestaurantLayout ifrTicket";
				grid-template-columns: max-content 1fr max-content;
				grid-template-rows: min-content 1fr min-content;
			}

			.sessionContainer {
				height: 100vh;
			}

			#ifrServerList {
				grid-area: ifrServerList;
				background-color: black;
			}

			#ifrWaitList {
				grid-area: ifrWaitList;
				background-color: black;
			}

			#ifrWaitTimes {
				grid-area: ifrWaitTimes;
				background-color: black;
			}

			#ifrSelectedTable {
				grid-area: ifrSelectedTable;
				background-color: black;
			}

			#ifrTicket {
				grid-area: ifrTicket;
				background-color: black;
			}

			#ifrRestaurantLayout {
				grid-area: ifrRestaurantLayout;
				background-color: #777;
			}
		</style>
		<script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script> 
		<script>
			function allElementsLoaded() {
				setVar("authorizationId", USER_ID, "ifrSelectedTable");
				if ((ROLE & 8) == 8) {
                	setVar("authorizationId", USER_ID, "ifrRestaurantLayout");
					verifyAuthProcessed = true;
            	}
				startEventLoopTimer();
			}
			
			var verifyAuthProcessed = false;
			var eventLoopTimer;

			function eventLoop() {
				try {
					if (verifyAuthProcessed && getVar("authorizationId", "ifrRestaurantLayout") === undefined) {
						setTimeout(eventLoop, 250);
					}
					verifyAuthProcessed = false;
					let goToTableId = getVar("goToTable", "ifrRestaurantLayout");
					if (goToTableId !== undefined) {
						setVar("staticTableId",goToTableId);
						document.getElementsByTagName("form")[0].setAttribute("action", "../ServerView/ServerView.php");
						updateDisplay();
					}

					let selectionChanged = false;

					let serverExtern = getVar("selectedServer", "ifrServerList");
					let serverLocal = getVar("selectedServer");
					
					let tableExtern = getVar("selectedTable", "ifrRestaurantLayout");
					let tableLocal = getVar("selectedTable");
					
					let selectedTableUpdated = getVar("updated", "ifrSelectedTable");

					let waitListUpdateFlag = getVar("flag", "ifrSelectedTable");
					
					let ticketExtern = getVar("ticketId", "ifrSelectedTable");
					let ticketLocal = getVar("selectedTicket", "ifrWaitList");
					if (ticketLocal !== undefined) {
						ticketLocal = ticketLocal.substring(6);
					}
					
					if (waitListUpdateFlag == "refreshWaitList") {
						removeVar("flag", "ifrSelectedTable");
						updateDisplay("ifrWaitList");
					}

					let updateNeeded = false;
					let highlightNeeded = false;
										
					if (ticketLocal !== undefined && ticketExtern === undefined) {
						setVar("ticketId", ticketLocal, "ifrSelectedTable");
						updateNeeded = true;
					}

					if (serverLocal != serverExtern) {
						selectionChanged = true;
						if (serverExtern === undefined) {
							removeVar("selectedServer");
							removeVar("employeeId", "serverListener");
							removeVar("employeeId", "ifrSelectedTable");
						}
						else {
							setVar("selectedServer", serverExtern);
							setVar("employeeId", serverExtern.substring(6), "serverListener");	
						}
						updateDisplay("serverListener");
						highlightNeeded = true;
						updateNeeded = true;
						
					}

					if (tableLocal != tableExtern) {
						removeVar("employeeId", "ifrSelectedTable");
						removeVar("ticketId", "ifrSelectedTable");
						setVar("selectedTable", tableExtern);
						updateNeeded = true;
					}
					if (updateNeeded || highlightNeeded) {
						updateDisplay("serverListener");
						if (updateNeeded) { updateSelectedTable(); }
						if (highlightNeeded) { highlightTables(); }
					}
						
				}
				catch (error) {
				
				}
				startEventLoopTimer();
			}

			function highlightTables() {
				try {
					var selectedServer = getVar("selectedServer");
					var tableList = getVar("tableList", "serverListener");
					setVar("highlightedTables", tableList , "ifrRestaurantLayout");
					setVar("highlightedTables", (selectedServer === undefined ? "clear" : tableList), "ifrRestaurantLayout");
				}
				catch (error) {
					alert(error);
					setTimeout(highlightTables, 250);
				}
			}

			function updateSelectedTable() {
				try {
					let tableList = getVar("tableList","serverListener");

					let serverLocal = getVar("selectedServer");
					if (serverLocal !== undefined) {
						serverLocal = serverLocal.substring(6);
					}
					let serverExtern = getVar("selectedServer", "ifrSelectedTable");
					
					let tableLocal = getVar("selectedTable");
					let tableExtern = getVar("selectedTable", "ifrSelectedTable");

					let tableNew = undefined;
					let serverNew = undefined;

					let selectedTables = getVar("selectedTable", "ifrRestaurantLayout");

					if (tableLocal !== undefined) {
						tableNew = tableLocal;
					}

					if (serverLocal !== undefined) {
						serverNew = serverLocal;
					}

					let updateRequired = false;
					if (selectedTables !== undefined && selectedTables.indexOf(",") > -1) {
						setVar("tableId", undefined, "ifrSelectedTable");
						removeVar("employeeId", "ifrSelectedTable");
						updateDisplay("ifrSelectedTable");
						return;
					}
					if (tableExtern != tableNew) {
						if (tableNew == "clear") {
							removeVar("tableId", "ifrSelectedTable");
							updateRequired = true;
						}
						else {
							setVar("tableId", tableNew, "ifrSelectedTable");
							updateRequired = true;
						}
					}
					if (serverExtern != serverNew) {
						setVar("employeeId", serverNew, "ifrSelectedTable");
						updateRequired = true;
					}
					if (updateRequired) {
						updateDisplay("ifrSelectedTable");
					}
				}
				catch (err) {
					alert("error");
					setTimeout(updateSelectedTable, 250);
				}
			}

			function stopEventLoopTimer() {
				clearTimeout(eventLoopTimer);
			}
			function startEventLoopTimer() {
				eventLoopTimer = setTimeout(eventLoop, 1000);
			}
			<!-- event loop goes here -->
		</script>
	</head>
	<body onload="allElementsLoaded()" class="intro">
		<form id="hostViewSession" class="sessionContainer" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
			<?php require_once "../Resources/php/sessionHeader.php"; ?>
			<div class="sessionBody">
				<iframe id="ifrServerList" src="ServerList.php" frameborder='0' width="100%" height="100%"></iframe>
				<iframe id="ifrWaitList" src="WaitList.php" frameborder='0' width="100%" height="100%"></iframe>
				<iframe id="ifrWaitTimes" src="WaitTimes.php" frameborder='0' width="100%" height="100%"></iframe>
				<iframe id="ifrSelectedTable" src="SelectedTable.php" frameborder='0' width="100%" height="100%"></iframe>
				<iframe id="ifrTicket" src="../Resources/php/ticket.php" frameborder='0' width="100%" height="100%"></iframe>
				<iframe id="ifrRestaurantLayout" src="RestaurantLayout.php" frameborder='0' width="100%" height="100%"></iframe>
				<iframe id="serverListener" src="../Resources/php/serverListener.php" style="display: none;"></iframe>
			</div>
		</form>
	</body>
</html>