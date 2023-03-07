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
									 "ifrWaitList   ifrRestaurantLayout ifrSelectedTable"
									 "ifrWaitTimes  ifrRestaurantLayout ifrSelectedTable";
				grid-template-columns: max-content 1fr max-content;
				grid-template-rows: 40% 40% 20%;
			}

			.sessionContainer {
				height: 100vh;
			}

			#ifrServerList {
				grid-area: ifrServerList;
				background-color: black;
				overflow: auto;
			}

			#ifrWaitList {
				grid-area: ifrWaitList;
				background-color: black;
				overflow: auto;
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
			}
		</style>
		<script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script> 
		<script>
			function allElementsLoaded() {
				setVar("showAllTables", "", "serverListener");
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
					
					varCpyRen("goToTable", "ifrRestaurantLayout", "staticTableId", null, true);

					if (varCpyRen("employeeId", "ifrServerList", "addEmployeeId", "ifrSelectedTable", true)) {
						varCpy("employeeId", "ifrServerList", "serverListener", true);
					}
					
					varCpyRen("ticketId", "ifrWaitList", "addTicketId", "ifrSelectedTable", true);
					varCpyRen("selectedTable", "ifrRestaurantLayout", "tableId", "ifrSelectedTable", true);
					varCpyRen("tableList", "serverListener", "highlightedTables", "ifrRestaurantLayout");
					
					if (getVarOnce("flag","ifrSelectedTable")) {
						removeVar("tableList","serverListener");
						updateDisplay("ifrWaitList");
						updateDisplay("ifrServerList");
						updateDisplay("ifrWaitTimes");
						highlightTables();
					}
		
				}
				catch (error) {
					
				}
				startEventLoopTimer();
			}

			function stopEventLoopTimer() {
				clearTimeout(eventLoopTimer);
			}
			function startEventLoopTimer() {
				eventLoopTimer = setTimeout(eventLoop, 100);
			}
			<!-- event loop goes here -->
		</script>
	</head>
	<body onload="allElementsLoaded()" class="intro">
		<form id="hostViewSession" class="sessionContainer" action="../ServerView/ServerView.php" method="POST">
			<?php require_once "../Resources/php/sessionHeader.php"; ?>
			<div class="sessionBody">
				<iframe id="ifrServerList" src="ServerList.php" frameborder='0' width="100%" height="100%"></iframe>
				<iframe id="ifrWaitList" src="WaitList.php" frameborder='0' width="100%" height="100%"></iframe>
				<iframe id="ifrWaitTimes" src="WaitTimes.php" frameborder='0' width="100%" height="100%"></iframe>
				<iframe id="ifrSelectedTable" src="SelectedTable.php" frameborder='0' width="100%" height="100%"></iframe>
				<iframe id="ifrRestaurantLayout" src="RestaurantLayout.php" frameborder='0' width="100%" height="100%"></iframe>
				<iframe id="serverListener" src="../Resources/php/serverListener.php" style="display: none;"></iframe>
			</div>
		</form>
	</body>
</html>