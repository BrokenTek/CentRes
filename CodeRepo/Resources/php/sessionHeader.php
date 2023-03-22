<?php
    

    $rolesString = "const ROLES = {};";
    $sql = "SELECT * FROM LoginRouteTable;";
    $roles = connection()->query($sql);
    if (mysqli_num_rows($roles) > 0) {
        $rol = $roles->fetch_assoc();
        $roleStr = "const ROLES = {" .$rol['id']. ": '" .$rol['title']. "'";
        while($rol = $roles->fetch_assoc()) {
            $roleStr .= ", " .$rol['id']. ": '" .$rol['title']. "'";
        }
        $roleStr .= "};";
    }


    echo('
        <script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script>
        <script>
            const USERNAME = "' .$GLOBALS['username']. '";
            const USER_ID = "' .$GLOBALS['userId']. '";
            const FIRST_NAME = "' .$GLOBALS['firstName']. '";
            const LAST_NAME = "' .$GLOBALS['lastName']. '";
            const ROLE = ' .$GLOBALS['role']. ';'
            .$roleStr.
        ' function navigateAway() {
            var value = document.getElementById("managementNavigationSelector").value;
            if (value != "") {
                location.replace(value);
            }
            document.getElementById("managementNavigationSelector").selectedIndex = 0;    
          }
        </script>
    ');

    echo('<link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
        <script>
            function logout() {
                let div = document.createElement("input");
                div.setAttribute("type","hidden");
                div.setAttribute("name","logoutUsername");
                div.setAttribute("value",USERNAME);
                let frm = document.getElementsByTagName("form")[0];
                frm.append(div);
                frm.setAttribute("action","../LoginView/login.php");
                frm.submit();
            }
        </script>
    
        <div id="sessionHeader">
            <img src="../Resources/Images/centresLogo.png" id="lgoSession" width=50 height=50>
            <div id="sessionDetails">' .$GLOBALS['username']. '</div>');

    if (($GLOBALS['role'] & 8) == 8) {
        echo("<div id='managementNavigation'>
                <select name='mgrNavDest' id='managementNavigationSelector' onchange='navigateAway()'>
                    <option value=''>Navigate To</option>
                    <option id='mgrNavHostView' value='../HostView/HostView.php'>Host&nbsp;View</option>
                    <option id='mgrNavIPW' value = '../ManagerView/InventoryPopularityWindow.php'>Inventory/Popularity&nbsp;Window</option>
                    <option id='mgrNavEmpRoster' value=''>Employee&nbsp;Roster</option>
                    <option id='mgrNavMenuEditor' value=''>Menu&nbsp;Editor</option>
                </select>
            </div>
        ");
    }

    echo('<button type="button" id="btnLogout" onclick="logout()">Logout</button>
        </div>');
?>