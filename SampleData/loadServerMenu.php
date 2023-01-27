<?php 
	echo '
	<details>
		<summary>Entrees</summary>
		<ol>
			<li id="BRG" data-text="Royal Burger" data-price="8888.88"><span>Royal Burger</span><span>$8,888.88</span></li>
			<li id = "HDG" data-text="Hot Dog" data-price="9.87"><span>Hot Dog</span><span>$9.87</span></li>
		</ol>
	</details>
	<details>
		<summary id="DRK">Drinks</summary>
		<details>
			<summary id="DRK-N">Non-Alcoholic</summary>
				<ol>
					<li id="PEPSI" data-text="Pepsi" data-price="2.99"><span>Pepsi</span><span>$2.99</span></li>
					<li id = "COKE" data-text="Pepsi" data-price="3.25"><span>Coke</span><span>$3.25</span></li>
					<li id = "SWTT" data-text="Sweet Tea" data-price="4.00"><span>Sweet Tea</span><span>$4.00</span></li>
				</ol>
				<details>
					<summary id="CLD">Cold Drinks</summary>
					<ol>
						<li id="FRL" data-text="Frozen Lemonade" data-price="2.99"><span>Frozen Lemonade</span><span>$2.99</span></li>
						<li id ="RBF" data-text="Rootbeer Float" data-price="4.85"><span>Rootbeer Float</span><span>$4.85</span></li>
					</ol>
				</details>
		</details>
		<details>
			<summary id="DRK-A">Alcoholic</summary>
				<ol>
					<li data-text="Rum" data-price="4,00"><span>Rum</span><span>$4.00</span></li>
					<li data-text="Gin" data-price="6.00"><span>Gin</span><span>$6.00</span></li>
					<li data-text="Whiskey" data-price="6.50"><span>Whiskey</span><span>$6.50</span></li>
				</ol>
		</details>
		
	</details>
	';
	

?>