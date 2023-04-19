
<?php require_once '../Resources/PHP/sessionLogic.php'; restrictAccess(255, $GLOBALS['role']); ?>
<?php require_once '../Resources/PHP/currencyFormatter.php'; ?>

<!DOCTYPE html>
<html>
    <head>
		<meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
		<script src="../Resources/JavaScript/display.js"></script>
        <link rel="stylesheet" href="../Resources/CSS/menuStyle.css">
		<link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
		<script>
			const menuObjectTest = new Event("menuObjectLoaded", {bubbles: true});
			function createMenuSelectEventHandlers() {
				
				var menuItemSelected = function(event) {
					event.stopPropagation();
					varRem("selectedMenuCategory");
					varSet("selectedMenuItem", this.id);
					
					let parentCategoryId = document.selectMenuObject(this.id);
					dispatchJSONeventCall("menuItemSelected", {"menuItemId": this.id, "parentCategoryId": parentCategoryId});
				};

				var elements = document.getElementsByClassName("menuItem");
				if (elements != null) {
					for (var i = 0; i < elements.length; i++) {
	    				elements[i].addEventListener('pointerdown', menuItemSelected);
					}
				}

				var menuCategorySelected = function() {
					event.stopPropagation();
					if (ignoreCount > 0) {
						ignoreCount--;
						return;
					}
					varRem("selectedMenuItem");
					varSet("selectedMenuCategory", this.id);
					let parentCategoryId = document.selectMenuObject(this.id);
					if (this.hasAttribute("open")) {
						varSet("selectedMenuCategory", this.id);
						dispatchJSONeventCall("menuCategorySelected", {"menuCategoryId": this.id, "parentCategoryId": parentCategoryId});
					}
					else {
						dispatchJSONeventCall("menuDeselected", {});
						varRem("selectedMenuCategory");
					}		
				};

				var elements = document.getElementsByClassName("menuCategory");
				if (elements != null) {
					for (var i = 0; i < elements.length; i++) {
	    				elements[i].addEventListener('toggle', menuCategorySelected);
					}
				}

				var updatedObjectId = varGetOnce("updated");
				if (updatedObjectId !== undefined) {
					let updatedMenuObject = document.querySelector("#" + updatedObjectId);
					if (updatedMenuObject != null) {
						document.selectMenuObject(updatedMenuObject);
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

				//document.dispatchEvent(menuObjectTest);
				//alert("menuDispatched");
				
			}
			addEventListener('load', createMenuSelectEventHandlers);

			let ignoreCount = 0;
			document.selectMenuObject = function(menuObjectId) {
				var forceOpen = false;
				if (this.menuObjectId !== undefined) {
					menuObjectId = this.menuObjectId;
					forceOpen = true;
				}
				let menuObject = document.getElementById(menuObjectId); {
					if (menuObject == null) {
						return null;
					}
				}
				let myNode = menuObject;
				let myParent = null;
				if (menuObject.hasAttribute("open")) {
					menuObject.setAttribute("keepopen", "");
				}
				menuObject = menuObject.parentNode;
				while (menuObject != null) {
					if (menuObject.nodeType === Node.ELEMENT_NODE && menuObject.tagName === "DETAILS") {
						if (myParent == null) {
							myParent = menuObject;
						}
						menuObject.setAttribute("keepopen", "");
					}
					menuObject = menuObject.parentNode;
				}
				let openDetails = document.querySelectorAll('details');
				for (let i = 0; i < openDetails.length; i++) {
					with (openDetails[i]) {
						if (hasAttribute("keepopen") && !hasAttribute("open")) {
							ignoreCount += 1;
							setAttribute("open", "");
						}
						else if (!hasAttribute("keepopen") && hasAttribute("open")) {
							ignoreCount += 1
							removeAttribute("open");
						}
						else if (hasAttribute("keepopen")) {
							removeAttribute("keepopen");
						}
					}
				}
				if (myNode.hasAttribute("open")) {
					myNode.removeAttribute("keepopen");
				}
				ignoreToggle = false;
				if (forceOpen) {
					myNode.setAttribute("open","");
				}
				if (myParent == null) {
					return null;
				}
				return myParent.id;
			}

			

		</script>
	</head>
	<body>
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
<?php 
require_once '../Resources/PHP/dbConnection.php';

//To prevent infinite recursion produced by manager adding a parent menu object to a child, each printed
//menu item/category will increment a value by 1. If the value exceeds $menuObjectCount, execution will stop.
$sql = "SELECT COUNT(*) as menuObjectCount FROM MenuAssociations";
$menuObjectCount = connection()->query($sql)->fetch_assoc()['menuObjectCount'];

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
		if ($GLOBALS['menuObjectCount'] == 0) {
			die("Menu Recursion Detected!");
		}
		$GLOBALS['menuObjectCount'] -= 1;
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
				// ** NEEDS TO PASS IN CALCULATED price AND THE CALCULATED MODS STR (COMMA DELIMINATED) **
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
		if ($GLOBALS['menuObjectCount'] == 0) {
			die("Menu Recursion Detected!");
		}
		$GLOBALS['menuObjectCount'] -= 1;

		$classList = "menuItem menuTitleItem";
		if (isset($_POST['updated']) && $qc == $_POST['updated']) {
			$classList .= " updated";
		}
		if (isset($_POST['selected']) && $qc == $_POST['selected']) {
			$classList .= " selected";
		}
		echo "<span id='".$qc."' class='$classList' data-text='".$title."' data-price='".$price."' data-mods='X'><span class='menuItemPrice'>". currencyFormat($price) ."</span><span class='menuItemTitle'>".$title."</span>";
		echo "</span>";

	}
	require_once "../Resources/PHP/display.php";
?>
</form>
</body>
</html>