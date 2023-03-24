<html>
    <head>
        <base href="http://localhost/CentRes/CodeRepo/">
        <script>
            var resetSuccessful = false;
            function allElementsLoaded() {
                if (resetSuccessful) {
                    //document.getElementById("frmToLogin").submit();
                }
            }
        </script>
    </head>
    <body onload="allElementsLoaded()">
        <form action="LoginView/login.php" method="POST" id="frmToLogin">
                <input type="hidden" name="message" value="Database Successfully Reset.">
        </form>
    <?php
    try {
        $mysql_host = "localhost";
        $mysql_database = "Centres";
        $mysql_user = "scott";
        $mysql_password = "tiger";
        $dbh = new PDO("mysql:host=$mysql_host;dbname=$mysql_database", $mysql_user, $mysql_password);
        $dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        
        //associations_dict;

        // Delete the tables and add each back with root as the only record in the menucategories records.
        $query = "DELETE FROM MenuAssociations;
        DELETE FROM MenuItems;
        DELETE FROM MenuCategories;
        DELETE FROM MenuModificationItems;
        DELETE FROM MenuModificationCategories;
        INSERT INTO menucategories (quickCode, title)
        VALUES ('root','root');";
        $dbh->exec($query);

        // Add root categories
        $root_categories = array('Drinks', 'Appetizers', 'Sides', 'Entrees', 'Desserts');

        $i = 1;
        foreach ($root_categories as $category) {
            $stmt = $dbh->prepare("INSERT INTO menucategories (quickCode, title) VALUES (:primary_key, :category_name)");
            $stmt->bindValue(':primary_key', 'C'.$i);
            $stmt->bindValue(':category_name', $category);
            $stmt->execute();
            $stmt_assoc = $dbh->prepare("INSERT INTO menuassociations (parentQuickCode, childQuickCode) VALUES (:parentQuickCode, :childQuickCode)");
            $stmt_assoc->bindValue(':parentQuickCode', 'root');
            $stmt_assoc->bindValue(':childQuickCode', 'C'.$i);
            $stmt_assoc->execute();
            $i++;
        }
        /////////   ASSOCIATIONS CAN BE MADE IN THE SAME BLOCK
        // $i = 1;
        // foreach($root_categories as $category) {
        //     $stmt_assoc = $dbh->prepare("INSERT INTO menuassociations (parentQuickCode, childQuickCode) VALUES (:parentQuickCode, :childQuickCode)");
        //     $stmt_assoc->bindValue(':parentQuickCode', 'root');
        //     $stmt_assoc->bindValue(':childQuickCode', 'C'.$i);
        //     $stmt_assoc->execute();
        //     $i++;
        // }

        // Define sub category arrays
        $sub_drinks = array('Alcoholic', 'Beverages','Sodas','Hot Beverages');
        $sub_apps = array('Fried','Salads','Shareable');
        $sub_entrees = array('Grilled', 'Burgers' ,'Seafood', 'Steak' ,'Sandwiches');
        $sub_desserts = array('Ice Cream','Pie','Freshly Baked', 'Freshly Baked');


    // THIS IS WHERE ASSOCIATIONS NEED TO BE MADE. THE PARENT IS THE QUICKCODE ASSOCIATED WITH THE CATEGORY ABOVE IT. SUB CATEGORIES WILL BE USED FOR ALL MENU ITEMS
        $i = 1;
        foreach ($sub_drinks as $drink) {
            $stmt = $dbh->prepare("INSERT INTO menucategories (quickCode, title) VALUES (:primary_key, :category_name)");
            $stmt->bindValue(':primary_key', 'S'.$i);
            $stmt->bindValue(':category_name', $drink);
            $stmt->execute();

            $stmt = $dbh->prepare("INSERT INTO menuassociations (parentQuickCode, childQuickCode) VALUES (:parentQuickCode, :childQuickCode)");
            $stmt->bindValue(':parentQuickCode', 'C1');         // Associating 'Alcoholic' with 'Drinks'
            $stmt->bindValue(':childQuickCode', 'S'.$i);
            $stmt->execute();
            $i++;
        }
        // $i_next = $i;
        
        // $i = 1;
        // foreach ($sub_drinks as $drink) {
        //     $stmt = $dbh->prepare("INSERT INTO menuassociations (parentQuickCode, childQuickCode) VALUES (:parentQuickCode, :childQuickCode)");
        //     $stmt->bindValue(':parentQuickCode', 'root');
        //     $stmt->bindValue(':childQUickCode', 'S'.$i);
        //     $stmt->execute();
        //     $i++;
        // }


        foreach ($sub_apps as $app) {
            $stmt = $dbh->prepare("INSERT INTO menucategories (quickCode, title) VALUES (:primary_key, :category_name)");
            $stmt->bindValue(':primary_key', 'S'.$i);
            $stmt->bindValue(':category_name', $app);
            $stmt->execute();
            $i++;
        }

        foreach ($sub_entrees as $entree) {
            $stmt = $dbh->prepare("INSERT INTO menucategories (quickCode, title) VALUES (:primary_key, :category_name)");
            $stmt->bindValue(':primary_key', 'S'.$i);
            $stmt->bindValue(':category_name', $entree);
            $stmt->execute();
            $i++;
        }

        foreach ($sub_desserts as $dessert) {
            $stmt = $dbh->prepare("INSERT INTO menucategories (quickCode, title) VALUES (:primary_key, :category_name)");
            $stmt->bindValue(':primary_key', 'S'.$i);
            $stmt->bindValue(':category_name', $dessert);
            $stmt->execute();
            $i++;
        }

        $alcoholic_beverages = array(
            'Heineken' => 6,
            'Budweiser' => 5,
            'Corona Extra' => 6,
            'Stella Artois' => 5.50,
            'Pabst Blue Ribbon' => 4,
            'Miller Lite' => 4.50,
            'Guinness Draught' => 6,
            'Modelo Especial' => 5.50,
            'Samuel Adams Boston Lager' => 6,
            'Coors Light' => 4.50,
            'Yuengling Lager' => 5.50,
            'Sierra Nevada Pale Ale' => 5.75,
            'Newcastle Brown Ale' => 5,
            'Dogfish Head 60 Minute IPA' => 6.25,
            'Lagunitas IPA' => 6
        );

        $i = 1;
        foreach ($alcoholic_beverages as $name => $price) {
            $stmt = $dbh->prepare("INSERT INTO  menuitems (quickCode, title, price)  VALUES (:primary_key, :name, :price)");
            $stmt->bindValue(':primary_key', 'I'.$i);
            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':price', $price);
            $stmt->execute();
            $i++;
        }

        $sodas = array(
            'Coca-Cola' => 3.50,
            'Coke Zero' => 3.50,
            'Diet Coke' => 3.50,
            'Sprite' => 3.50,
            'Fanta Orange' => 3.50,
            'Fanta Grape' => 3.50,
            'Barqs Root Beer' => 3.50,
            'Seagrams Ginger Ale' => 4,
            'Fresca' => 3.50,
            'Mello Yello' => 3.50
        );

        foreach ($sodas as $name => $price) {
            $stmt = $dbh->prepare("INSERT INTO  menuitems (quickCode, title, price) VALUES (:primary_key, :name, :price)");
            $stmt->bindValue(':primary_key', 'I'.$i);
            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':price', $price);
            $stmt->execute();
            $i++;
        }

        $fried = array(
            'Mozzarella Sticks' => 7,
            'Onion Ring Tower' => 10.50,
            'Loaded Fries' => 7.50,
            'Buffalo Cauliflower' => 8.50
        );
        
        
        foreach ($fried as $name => $price) {
            $stmt = $dbh->prepare("INSERT INTO  menuitems (quickCode, title, price) VALUES (:primary_key, :name, :price)");
            $stmt->bindValue(':primary_key', 'I'.$i);
            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':price', $price);
            $stmt->execute();
            $i++;
        }

        $sides_root = array(
            'French Fries' => 3.50,
            'Onion Rings' => 3.50,
            'Fresh Chips' => 4,
            'Seasonable Veggies' => 4.25,
            'Rice Pilaf' => 4,
            'Mashed Potatos' => 4,
            'Baked Potato' => 6.50
        );
        
        
        foreach ($sides_root as $name => $price) {
            $stmt = $dbh->prepare("INSERT INTO  menuitems (quickCode, title, price) VALUES (:primary_key, :name, :price)");
            $stmt->bindValue(':primary_key', 'I'.$i);
            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':price', $price);
            $stmt->execute();
            $i++;
        }

        
        $grilled = array(
            'Blackened Chicken' => 10, 
            'Fajita Veggies Array' => 9.50, 
            'Honey Glazed Chicken' => 10.75
        );

        foreach ($grilled as $title => $price) {
            $stmt = $dbh->prepare("INSERT INTO menuitems (quickCode, title, price) VALUES (:primary_key, :title, :price)");
            $stmt->bindValue(':primary_key', 'I'.$i);
            $stmt->bindValue(':title', $title);
            $stmt->bindValue(':price', $price);
            $stmt->execute();
            $i++;
        }

        $burgers = array('Royal Burger' => 12.50,
        'Mushroom And Swiss' => 10.75,
        'Carolina Burger' => 11,
        'Bacon Cheeseburger' => 12
        );

        foreach ($burgers as $title => $price) {
            $stmt = $dbh->prepare("INSERT INTO menuitems (quickCode, title, price) VALUES (:primary_key, :title, :price)");
            $stmt->bindValue(':primary_key', 'I'.$i);
            $stmt->bindValue(':title', $title);
            $stmt->bindValue(':price', $price);
            $stmt->execute();
            $i++;
        }

        $grilled = array('Blackened Chicken' => 10, 
        'Fajita Veggies Array' => 9.50, 
        'Honey Glazed Chicken' => 10.75
        );
 
        foreach ($grilled as $title => $price) {
            $stmt = $dbh->prepare("INSERT INTO menuitems (quickCode, title, price) VALUES (:primary_key, :title, :price)");
            $stmt->bindValue(':primary_key', 'I'.$i);
            $stmt->bindValue(':title', $title);
            $stmt->bindValue(':price', $price);
            $stmt->execute();
            $i++;
        }

        $steak = array('Ribeye' => 16, 
        'Filet Mignon' => 18.50, 
        'T-Bone' => 15, 
        'Top Sirloin' => 17.75
        );

        foreach ($steak as $title => $price) {
            $stmt = $dbh->prepare("INSERT INTO menuitems (quickCode, title, price) VALUES (:primary_key, :title, :price)");
            $stmt->bindValue(':primary_key', 'I'.$i);
            $stmt->bindValue(':title', $title);
            $stmt->bindValue(':price', $price);
            $stmt->execute();
            $i++;
        }

        $sandwiches = array('Fresh Pesto Flatbread' => 10, 
        'Royal BLT' => 11.50, 
        'Buffalo Chicken Sandwich' => 10.75,
        'Grilled Chicken Sandwich' => 12
        );

        foreach ($sandwiches as $title => $price) {
            $stmt = $dbh->prepare("INSERT INTO menuitems (quickCode, title, price) VALUES (:primary_key, :title, :price)");
            $stmt->bindValue(':primary_key', 'I'.$i);
            $stmt->bindValue(':title', $title);
            $stmt->bindValue(':price', $price);
            $stmt->execute();
            $i++;
        }

        $ice_cream = array('Chocolate' => 6, 
        'Vanilla' => 6, 
        'Moose Tracks' => 6, 
        'Oreo' => 6, 
        'Cookie Dough' => 6, 
        'Pecans and Pralines' => 6.25
        );

        foreach ($ice_cream as $title => $price) {
            $stmt = $dbh->prepare("INSERT INTO menuitems (quickCode, title, price) VALUES (:primary_key, :title, :price)");
            $stmt->bindValue(':primary_key', 'I'.$i);
            $stmt->bindValue(':title', $title);
            $stmt->bindValue(':price', $price);
            $stmt->execute();
            $i++;
        }


        $pie = array('Apple Pie' => 6.50, 
        'Cherry Pie' => 6.50, 
        'Keylime Pie' => 6.50
        );

        foreach ($pie as $title => $price) {
            $stmt = $dbh->prepare("INSERT INTO menuitems (quickCode, title, price) VALUES (:primary_key, :title, :price)");
            $stmt->bindValue(':primary_key', 'I'.$i);
            $stmt->bindValue(':title', $title);
            $stmt->bindValue(':price', $price);
            $stmt->execute();
            $i++;
        }

        $freshly_baked = array('Cinnamon Bun' => 6.25, 
        'Apple Tart' => 6, 
        'Slice Of Cake' => 5.50, 
        'Chocolate Chip Cookie'=> 4.50, 
        'Sugar Cookie'=> 4.25,
        'Oatmeal Cookie'=> 4.50
        );

        foreach ($freshly_baked as $title => $price) {
            $stmt = $dbh->prepare("INSERT INTO menuitems (quickCode, title, price) VALUES (:primary_key, :title, :price)");
            $stmt->bindValue(':primary_key', 'I'.$i);
            $stmt->bindValue(':title', $title);
            $stmt->bindValue(':price', $price);
            $stmt->execute();
            $i++;
        }

        // Successfully added all categories, sub categories, and menuitms.

        // Add menuassoctionas for quickCode assotiations because everything must 
        //  reinvent the wheel. -_-



        echo "Successfully Added Menu: TEMP";
    
    } catch(Expection $e) {
        print("<h1>☒ UNSUCCESSFUL!: " .$e->getMessage(). "</h1>");
    }

    ?>
    </body>
</html>



<!-- 


    // Recreate/Update the database tables strucuture
    $query = file_get_contents("CentResCreateDB.txt");
    $dbh->exec($query);
    print("<h1>☑ Database Structure Updated</h1>");


 -->