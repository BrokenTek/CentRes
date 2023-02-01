<?php 

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
			<summary>". $title ."</summary>
			<div>";

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

		echo "<span class='menuItemContainer'>";

		$sql = "SELECT childQuickCode, title, price 
		FROM MenuAssociations 
		INNER JOIN MenuItems ON MenuItems.quickCode = MenuAssociations.childQuickCode 
		WHERE MenuAssociations.parentQuickCode = '$qc';";
		$result = connection()->query($sql);

		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				// ** NEEDS TO PASS IN CALCULATED PRICE AND THE CALCULATED MODS STR (COMMA DELIMINATED) **
				printMenuItem($row['childQuickCode'], $row['title'], $row['price']);
			}
		}		
		
		echo "</span>";
		echo "</div>";
		echo "</details>";

		return;		
	}
	function printMenuItem(string $qc, string $title, float $price) {

		// ** NEEDS TO HAVE THE DATA ATTRIBUTE PASSED INTO 'PRICE' BE THE CALCULATED PRICE^ AND CALCULATED MODS STR (COMMA DELIMINATED) **
		// GOING TO NEED TO REVISIT FOR THE DATA-MODS ATTR (X)
		echo "<span id='".$qc."' class='menuItem menuItemTitle' data-text='".$title."' data-price='".$price."' data-mods='X'>".$title."</span>";
		echo "<span class='menuItemPrice'> $". $price ."</span>";

	}
?>