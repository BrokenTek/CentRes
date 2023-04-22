<?php require_once '../Resources/PHP/sessionLogic.php'; restrictAccess(4, $GLOBALS['role']); ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
		<style>
			iframe {
				background-color: black;
			}
			#sessionForm {
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

			}
		</style>
		<script src="../Resources/JavaScript/display.js" type="text/javascript"></script> 
		<script>
			function allElementsLoaded() {
				varSet("tableIdOnly", "Yes", "serverListener");
				varSet("showAllTables", "Yes", "serverListener");
				varSet("authorizationId", USER_ID, "ifrSelectedTable");
				if ((ROLE & 8) == 8) {
					authorizationId = USER_ID;
            	}
				setTitle("CentRes POS: Host Station", "Host Station");
				startEventLoopTimer();
			}
			
			var authorizationId = undefined;
			var eventLoopTimer;
			function eventLoop() {
				try {
					varSet("authorizationId", authorizationId, "ifrRestaurantLayout");	
					varCpyRen("goToTable", "ifrRestaurantLayout", "staticTableId", null, true);
					
					if (varCpy("employeeId", "ifrServerList", "serverListener", true, true));
					varCpyRen("employeeId", "ifrServerList", "addEmployeeId", "ifrSelectedTable", true, true);
					
					varCpyRen("ticketId", "ifrWaitList", "addTicketId", "ifrSelectedTable", true, true);
					
					let tableList = varGet("selectedTable", "ifrRestaurantLayout");
					let oldTableList = varGet("tableId", "ifrSelectedTable");
					let updateSelectedTable = !(tableList !== undefined && oldTableList !== undefined && tableList.indexOf(",") > -1 && oldTableList.indexOf(",") > -1);

					varCpyRen("selectedTable", "ifrRestaurantLayout", "tableId", "ifrSelectedTable", updateSelectedTable,true);
										
					if (varGetOnce("update","ifrSelectedTable")) {
						varRem("tableList","serverListener");
						updateDisplay("ifrWaitList");
						updateDisplay("ifrServerList");
						updateDisplay("ifrWaitTimes");
						highlightTables();
					}
					varXfr("syncHighlightAnimation", "ifrSelectedTable", "ifrRestaurantLayout");

					if (varCpyRen("tableList", "serverListener", "highlightedTables", "ifrRestaurantLayout",false, true)) {
						varSet("highlightedTablesChanged", "yes", "ifrRestaurantLayout");
					}
		
				}
				catch (error) { }
				startEventLoopTimer();
			}

			function highlightTables() {
				try {
					var selectedServer = varGet("selectedServer");
					var tableList = varGet("tableList", "serverListener");
					if (selectedServer !== undefined && tableList == null) {
						setTimeout(highlightTables, 250);
						return;
					}
					else if (tableList !== undefined && selectedServer === undefined) {
						setTimeout(highlightTables, 250);
						return;
					}
					varSet("highlightedTables", (selectedServer === undefined ? "clear" : tableList) , "ifrRestaurantLayout");
				}
				catch (error) {
					setTimeout(highlightTables, 250);
				}
			}

			function stopEventLoopTimer() {
				clearTimeout(eventLoopTimer);
			}
			function startEventLoopTimer() {
				eventLoopTimer = setTimeout(eventLoop, 100);
			}
		</script>
	</head>
	<body onload="allElementsLoaded()" class="intro">
		<form id="sessionForm" action="../ServerView/ServerView.php" method="POST">
			<?php require_once "../Resources/PHP/sessionHeader.php"; ?>
			<div id="sessionBody">
				<iframe id="ifrServerList" src="serverList.php" frameborder='0' width="100%" height="100%"></iframe>
				<iframe id="ifrWaitList" src="waitList.php" frameborder='0' width="100%" height="100%"></iframe>
				<iframe id="ifrWaitTimes" src="waitTimes.php" frameborder='0' width="100%" height="100%"></iframe>
				<iframe id="ifrSelectedTable" src="selectedTable.php" frameborder='0' width="100%" height="100%"></iframe>
				<iframe id="ifrRestaurantLayout" src="restaurantLayout.php" frameborder='0' width="100%" height="100%"></iframe>
				<iframe id="serverListener" src="../Resources/PHP/serverListener.php" style="display: none;"></iframe>
			</div>
		</form>
	</body>
</html>