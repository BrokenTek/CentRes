<!DOCTYPE html>
<html>
    <head>
		<script src="../Resources/JavaScript/displayInterface.js"></script>
        <link rel="stylesheet" href="../Resources/CSS/menuStyle.css">
		<link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
		<script>
			function createMenuSelectEventHandlers() {
	    		var menuItemSelected = function() {
					setVar("selectedMenuItem", this.id);
				};

				var elements = document.getElementsByClassName("menuItem");
				if (elements != null) {
					for (var i = 0; i < elements.length; i++) {
	    				elements[i].addEventListener('pointerdown', menuItemSelected);
					}
				}

			}
			addEventListener('load', createMenuSelectEventHandlers);

			

		</script>
        <script src="../InDev/cwpribble.js"></script>
        <script src="../InDev/dbutshudiema.js"></script>
        <script src="../InDev/dlmahan.js"></script>
        <script src="../InDev/kcdine.js"></script>
        <script src="../InDev/sashort.js"></script>
        <script src="../InDev/OVERRIDEEXAMPLE.js"></script>
        <script>templateFunction("Hello World");</script>
	</head>
	<body>
		<form>
<?php 
require_once '../Resources/php/connect_disconnect.php';
connection();

$sql = "SELECT childQuickCode, title
		FROM MenuAssociations 
		INNER JOIN MenuCategories ON MenuCategories.quickCode = MenuAssociations.childQuickCode 
		WHERE MenuAssociations.parentQuickCode = 'ROOT';";
$result = connection()->query($sql);

if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		printMenuCategory($row['childQuickCode'], $row['title']);
	}
}


	function printMenuCategory(string $qc, string $title) {

		echo "<details id='". $qc ."' class='menuCategory'>
			<summary>". $title ."</summary>";

		$sql = "SELECT childQuickCode, title 
		FROM MenuAssociations 
		INNER JOIN MenuCategories ON MenuCategories.quickCode = MenuAssociations.childQuickCode 
		WHERE MenuAssociations.parentQuickCode = '$qc';";
		$result = connection()->query($sql);

		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				printMenuCategory($row['childQuickCode'], $row['title']);
			}
		}

		$sql = "SELECT childQuickCode, title, price 
		FROM MenuAssociations 
		INNER JOIN MenuItems ON MenuItems.quickCode = MenuAssociations.childQuickCode 
		WHERE MenuAssociations.parentQuickCode = '$qc';";
		$result = connection()->query($sql);

		if ($result->num_rows > 0) {
			echo "<div>";
			while($row = $result->fetch_assoc()) {
				// ** NEEDS TO PASS IN CALCULATED PRICE AND THE CALCULATED MODS STR (COMMA DELIMINATED) **
				printMenuItem($row['childQuickCode'], $row['title'], $row['price']);
			}
			echo "</div>";
		}		
		
		echo "</details>";

		return;		
	}
	function printMenuItem(string $qc, string $title, float $price) {

		// ** NEEDS TO HAVE THE DATA ATTRIBUTE PASSED INTO 'PRICE' BE THE CALCULATED PRICE^ AND CALCULATED MODS STR (COMMA DELIMINATED) **
		// GOING TO NEED TO REVISIT FOR THE DATA-MODS ATTR (X)
		echo "<span id='".$qc."' class='menuItem menuItemTitle' data-text='".$title."' data-price='".$price."' data-mods='X'><span class='menuItemPrice'> $". $price ."</span><span class='menuItemTitle'>".$title."</span>";
		echo "</span>";

	}
	require_once "../Resources/php/display.php";
?>
</form>
</body>
</html>