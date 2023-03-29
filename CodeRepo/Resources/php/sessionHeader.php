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

          function logout() {
            varSet("logoutUsername", USERNAME);
            let frm = document.getElementsByTagName("form")[0];
            frm.setAttribute("action","../LoginView/LoginView.php");
            frm.submit();
          }
          
        const COOKIE_NAME = "804288a34eb7a49b349be68fc6437621cbf25e10d82f4268bb795eca277adedb6a3367add5bfb7cbffb50df150e2e78d26b276f37d32d96cd76746065df58a30cde25c4d9803aa7214dc8f6a985bf8643c341f229b5834964b0f371915d5677e4b579fbab42844cd63ddc3148e4250591277cfc521906bc30cfedd765974c2009ae5fe451ab1890e5ebbfa120ad18934c972618dbe3e";
        const SESSION_VALUE = decodeURIComponent(getCookieValue(COOKIE_NAME));
        function validateSession() {
            try {
                if (!varSet("username", "' .$GLOBALS['username']. '", "ifrSessionInfo", true)) {
                    updateDisplay("ifrSessionInfo");
                    if (varGet("sessionValue", "ifrSessionInfo") != SESSION_VALUE) {
                        location.replace("../LoginView/loginView.php");
                    }
                }
            }
            catch (err) { }
            setTimeout(validateSession, 1000);
        }
        setTimeout(validateSession, 1000);
        // https://stackoverflow.com/questions/10730362/get-cookie-by-name
        function getCookieValue(name) {
            var match = document.cookie.match(new RegExp("(^| )" + name + "=([^;]+)"));
            if (match) {
                return match[2];
            }
            else{
                return undefined;
            }
        }
       
        
        </script>
    ');

    echo('<link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
    
        <div id="sessionHeader">
            <img src="../Resources/Images/centresLogo.png" id="lgoSession" width=50 height=50>
            <div id="sessionDetails">' .$GLOBALS['username']. '</div>');

    if (($GLOBALS['role'] & 8) == 8) {
        echo("<div id='managementNavigation'>
                <select name='mgrNavDest' id='managementNavigationSelector' onchange='navigateAway()'>
                    <option value=''>Navigate To</option>
                    <option id='mgrNavHostView' value='../HostView/HostView.php'>Host&nbsp;View</option>
                    <option id='mgrNavIPW' value = '../ManagerView/InventoryPopularityWindow.php'>Inventory/Popularity&nbsp;Window</option>
                    <option id='mgrNavEmpRoster' value='../ManagerView/EmployeeRoster.php'>Employee&nbsp;Roster</option>
                    <option id='mgrNavMenuEditor' value=''>Menu&nbsp;Editor</option>
                    <option id='mgrNavResetDB' value='../Configuration/resetDB.php'>DEBUG:&nbsp;Reset&nbsp;DB</option>
                </select>
            </div>
        ");
    }

    echo('<input type="button" class="button" id="btnLogout" onpointerdown="logout()" value="Logout">
        </div>');
    echo("<iframe id='ifrSessionInfo' src='../Resources/php/sessionInfo.php' style='display: none;'></iframe>");
?>


