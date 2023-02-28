<?php require_once '../Resources/php/sessionLogic.php'; restrictAccess(4, $GLOBALS['role']); ?>
<!DOCTYPE html>
<html>
	<head>
		<style>
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
			}

			#ifrWaitList {
				grid-area: ifrWaitList;
			}

			#ifrWaitTimes {
				grid-area: ifrWaitTimes;
			}

			#ifrSelectedTable {
				grid-area: ifrSelectedTable;
			}

			#ifrTicket {
				grid-area: ifrTicket;
			}

			#ifrRestaurantLayout {
				grid-area: ifrRestaurantLayout;
			}
		</style>
		<script>
			<!-- event loop goes here -->
		</script>
	</head>
	<body>
		<form id="hostViewSession" class="sessionContainer" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
			<?php require_once "../Resources/php/sessionHeader.php"; ?>
			<div class="sessionBody">
				<iframe id="ifrServerList" src="ServerList.php" frameborder='0' width="100%" height="100%"></iframe>
				<iframe id="ifrWaitList" src="WaitList.php" frameborder='0' width="100%" height="100%"></iframe>
				<iframe id="ifrWaitTimes" src="WaitTimes.php" frameborder='0' width="100%" height="100%"></iframe>
				<iframe id="ifrSelectedTable" src="SelectedTable.php" frameborder='0' width="100%" height="100%"></iframe>
				<iframe id="ifrTicket" src="../Resources/php/ticket.php" frameborder='0' width="100%" height="100%"></iframe>
				<iframe id="ifrRestaurantLayout" src="RestaurantLayout.php" frameborder='0' width="100%" height="100%"></iframe>
			</div>
		</form>
	</body>
</html>