<?php require_once '../Resources/php/sessionLogic.php'; restrictAccess(4, $GLOBALS['role']); ?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
		<style>
			iframe {
				background-color: black;
			}
			#sessionContainer {
				height: 100%;
				width: 100%;
				margin: auto auto auto auto;
			}
			#sessionBody {
				display: grid;
				grid-template-areas: "ifrServerList ifrRestaurantLayout ifrSelectedTable"
									 "ifrWaitList   ifrRestaurantLayout ifrSelectedTable"
									 "ifrWaitTimes  ifrRestaurantLayout ifrSelectedTable";
				grid-template-columns: max-content 1fr max-content;
				grid-template-rows: 2fr 2fr 1fr;
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
				/*max-width: 1300px;
				max-height: 800px; */
				/*set max-height to include the entire iframe
				set max-width to include the entire iframe, both by pixel */
				overflow: auto;
			}
		</style>
		<script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script> 
		<script>
			function allElementsLoaded() {
				setVar("tableIdOnly", "", "serverListener");
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
					
					varCpy("employeeId", "ifrServerList", "serverListener", true, true);
					varCpyRen("employeeId", "ifrServerList", "addEmployeeId", "ifrSelectedTable", true, true);
					
					varCpyRen("ticketId", "ifrWaitList", "addTicketId", "ifrSelectedTable", true, true);
					
					let tableList = getVar("selectedTable", "ifrRestaurantLayout");
					let oldTableList = getVar("tableId", "ifrSelectedTable");
					let updateSelectedTable = !(tableList !== undefined && oldTableList !== undefined && tableList.indexOf(",") > -1 && oldTableList.indexOf(",") > -1);

					varCpyRen("selectedTable", "ifrRestaurantLayout", "tableId", "ifrSelectedTable", updateSelectedTable,true);
										
					if (getVarOnce("update","ifrSelectedTable")) {
						removeVar("tableList","serverListener");
						updateDisplay("ifrWaitList");
						updateDisplay("ifrServerList");
						updateDisplay("ifrWaitTimes");
						highlightTables();
					}
					varXfr("syncHighlightAnimation", "ifrSelectedTable", "ifrRestaurantLayout");

					if (varCpyRen("tableList", "serverListener", "highlightedTables", "ifrRestaurantLayout",false, true)) {
						setVar("highlightedTablesChanged", "yes", "ifrRestaurantLayout");
					}
		
				}
				catch (error) { }
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
		<form id="sessionContainer" action="../ServerView/ServerView.php" method="POST">
			<?php require_once "../Resources/php/sessionHeader.php"; ?>
			<div id="sessionBody">
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