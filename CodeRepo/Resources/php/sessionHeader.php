<?php

    // check corresponding accessToken in database exists and is valid
    $sql = "SELECT sessionRole('" .$_COOKIE[$cookie_name].  "') AS sessionRole;";
    $role = connection()->query($sql)->fetch_assoc()['sessionRole'];
    
    // session validated and session role determined.... get the session username
    $sql = "SELECT sessionUsername('" .$_COOKIE[$cookie_name].  "') AS sessionUsername;";
    $uname = connection()->query($sql)->fetch_assoc()['sessionUsername'];
    
    //$get First and Last Name
    $sql = "SELECT firstName, lastName FROM Employees where userName = '" .$uname. "';";
    $row = connection()->query($sql)->fetch_assoc();
    $fname = $row['firstName'];
    $lname = $row['lastName'];
    
    echo('
        <script>
            const USERNAME = "' .$uname. '";
            const FIRST_NAME = "' .$fname. '";
            const LAST_NAME = "' .$lname. '";
            const ROLE = ' .$role. ';
        </script>'
    );

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
            <div id="sessionDetails">' .$username. '</div>
            <button type="button" id="btnLogout" onclick="logout()">Logout</button>
        </div>');
?>