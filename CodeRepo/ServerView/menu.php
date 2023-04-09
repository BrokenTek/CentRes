<!-- ensures you are logged in before rendering page.
Otherwise will reroute to logon page -->
<?php require_once '../Resources/php/sessionLogic.php'; restrictAccess(255, $GLOBALS['role']); ?>
<?php require_once '../Resources/php/currencyPrinter.php'; ?>

<!DOCTYPE html>
<html>
    <head>
		<script src="../Resources/JavaScript/displayInterface.js"></script>
        <link rel="stylesheet" href="../Resources/CSS/menuStyle.css">
		<link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
		<script>
			function createMenuSelectEventHandlers() {
				
				var menuItemSelected = function(event) {
					event.stopPropagation();
					varRem("selectedMenuCategory");
					varSet("selectedMenuItem", this.id);
					
				};

				var elements = document.getElementsByClassName("menuItem");
				if (elements != null) {
					for (var i = 0; i < elements.length; i++) {
	    				elements[i].addEventListener('pointerdown', menuItemSelected);
					}
				}

				var menuCategorySelected = function(event) {
					event.stopPropagation();
					varRem("selectedMenuItem");
					varSet("selectedMenuCategory", this.id);
					this.toggleAttribute("open");
					let openDetails = document.querySelectorAll('details[open=""'); 
					if (openDetails.length == 0) {
						varRem("openDetails");
					}
					else if (openDetails.length == 1) {
						varSet("openDetails", openDetails[0].id);
					}
					else {
						let str = openDetails[0].id;
						for (let i = 1; i < openDetails.length; i++) {
							str += "," + openDetails[i].id;
						}
						varSet("openDetails", str);
					}
					this.toggleAttribute("open");
				};

				var openDetails = varGet("openDetails");
				if (openDetails !== undefined) {
					newDetails = ""
					openDetails = openDetails.split(",");
					for (let i = 0; i < openDetails.length; i++) {
						if (document.querySelector("#" + openDetails[i]) != null) {
							document.getElementById(openDetails[i]).setAttribute("open", "");
							newDetails += "," + openDetails[i];
						}
					}
					if (newDetails == "") {
						varRem("openDetails");
					}
					else {
						varSet("openDetails", newDetails.substring(1));
					}
				}

				var elements = document.getElementsByClassName("menuCategory");
				if (elements != null) {
					for (var i = 0; i < elements.length; i++) {
	    				elements[i].addEventListener('pointerdown', menuCategorySelected);
					}
				}

				var clearSelectedVars = function(event) {
					varRem("selectedMenuCategory");
					varRem("selectedMenuItem");
				};

				let x = varGet("scrollX");
                let y = varGet("scrollY");
                if (x !== undefined) {
                    window.scroll({
                    top: y,
                    left: x,
                    behavior: "smooth",
                    });
                }

                window.addEventListener('scroll', function(event) {
                    varSet("scrollX", window.scrollX);
                    varSet("scrollY", window.scrollY);
                }, true);


			}
			addEventListener('load', createMenuSelectEventHandlers);

			

		</script>
	</head>
	<body>
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
<?php 
require_once '../Resources/php/connect_disconnect.php';


$sql = "SELECT childQuickCode, title, visible
		FROM MenuAssociations 
		INNER JOIN MenuCategories ON MenuCategories.quickCode = MenuAssociations.childQuickCode 
		WHERE MenuAssociations.parentQuickCode = 'root' AND visible = true;";
$result = connection()->query($sql);

if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		printMenuCategory($row['childQuickCode'], $row['title']);
	}
}


	function printMenuCategory(string $qc, string $title) {
		$classList = "menuCategory";
		if (isset($_POST['updated']) && $qc == $_POST['updated']) {
			$classList .= " updated";
		}
		if (isset($_POST['selected']) && $qc == $_POST['selected']) {
			$classList .= " selected";
		}
		echo "<details id='". $qc ."' class='$classList'>
			<summary>". $title ."</summary>";

		$sql = "SELECT childQuickCode, title, visible 
		FROM MenuAssociations 
		INNER JOIN MenuCategories ON MenuCategories.quickCode = MenuAssociations.childQuickCode 
		WHERE MenuAssociations.parentQuickCode = '$qc';";
		$result = connection()->query($sql);

		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				if ($row['visible'] == true) {
					printMenuCategory($row['childQuickCode'], $row['title']);
				}
			}
		}

		$sql = "SELECT childQuickCode, title, price, visible 
		FROM MenuAssociations 
		INNER JOIN MenuItems ON MenuItems.quickCode = MenuAssociations.childQuickCode 
		WHERE MenuAssociations.parentQuickCode = '$qc' AND visible = true;";
		$result = connection()->query($sql);

		if ($result->num_rows > 0) {
			echo "<div>";
			while($row = $result->fetch_assoc()) {
				// ** NEEDS TO PASS IN CALCULATED PRICE AND THE CALCULATED MODS STR (COMMA DELIMINATED) **
				if ($row['visible'] == true) {
					printMenuItem($row['childQuickCode'], $row['title'], $row['price']);
				}
			}
			echo "</div>";
		}		
		
		echo "</details>";

		return;		
	}
	function printMenuItem(string $qc, string $title, float $price) {

		// ** NEEDS TO HAVE THE DATA ATTRIBUTE PASSED INTO 'PRICE' BE THE CALCULATED PRICE^ AND CALCULATED MODS STR (COMMA DELIMINATED) **
		// GOING TO NEED TO REVISIT FOR THE DATA-MODS ATTR (X)
		$classList = "menuItem menuTitleItem";
		if (isset($_POST['updated']) && $qc == $_POST['updated']) {
			$classList .= " updated";
		}
		if (isset($_POST['selected']) && $qc == $_POST['selected']) {
			$classList .= " selected";
		}
		echo "<span id='".$qc."' class='$classList' data-text='".$title."' data-price='".$price."' data-mods='X'><span class='menuItemPrice'>". currencyPrint($price) ."</span><span class='menuItemTitle'>".$title."</span>";
		echo "</span>";

	}

	unset($_POST['updated']);
	require_once "../Resources/php/display.php";
?>
</form>
</body>
</html>