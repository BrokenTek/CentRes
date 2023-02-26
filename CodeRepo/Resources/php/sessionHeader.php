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
        <script>
            const USERNAME = "' .$GOLBALS['username']. '";
            const USER_ID = "' .$GOLBALS['userId']. '";
            const FIRST_NAME = "' .$GOLBALS['firstName']. '";
            const LAST_NAME = "' .$GOLBALS['lastName']. '";
            const ROLE = ' .$GOLBALS['role']. ';'
            .$roleStr.
        '</script>
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
    
        <div class="sessionHeader">
            <img src="../Resources/Images/centresLogo.png" id="lgoSession" width=50 height=50>
            <div id="sessionDetails">' .$GOLBALS['username']. '</div>
            <button type="button" id="btnLogout" onclick="logout()">Logout</button>
        </div>');
?>