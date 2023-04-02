<!-- DISPLAY TEMPLATE
This template includes starter code that allows
you to use display.php and displayInterface.js -->


<!-- ensures you are logged in before rendering page, and are logged in under the correct role.
If you aren't logged in, it will reroute to the login page.
If you are logged in but don't have the correct role to view this page,
you'll be routed to whatever the home page is for your specified role level -->
<!-- CHANGE 255 TO THE ALLOWED ROLE LEVEL FOR THE PAGE -->

<!DOCTYPE html>
<?php require_once '../../Resources/php/connect_disconnect.php'; ?>
<html>
    <head>
        <!-- gives you access to varSet, varGet, varRem, 
        varClr, updateDisplay, rememberScrollPosition, and forgetScrollPosition -->
        <script src="../../Resources/JavaScript/displayInterface.js" type="text/javascript"></script> 
        
        <!-- demonstration on how to use varGet, varSet, updateDisplay for just this page -->
        <!-- remove this script tag -->
        <script>
            function allElementsLoaded() {
               
            }

            function updateATGC() {
                varSet("atgHash", document.getElementById("atgHash").value, "ifrAtgc");
                varSet("activeGroupId", document.getElementById("activeGroupId").value, "ifrAtgc");
                varSet("ticketItemNumber", document.getElementById("ticketItemNumber").value, "ifrAtgc");
                updateDisplay("ifrAtgc");
            } 

            //Place your JavaScript Code here
        </script>
        <style>
            #sessionForm {
                display: grid;
                grid-template-columns: min-content 1fr;
                grid-auto-rows: 1fr;
                grid-column-gap: 2rem;
            }
            form {
                display: grid;
                grid-template-columns: min-content min-content;
                grid-auto-rows: min-content;
                grid-gap: .5rem;
            }
            iframe {
                min-width: 100%;
                height: 25rem;;
            }
        </style>
    </head>
    <body id="sessionForm" onload="allElementsLoaded()">
        <!-- this form submits to itself -->
        <form onsubmit="event.preventDefault();">
            <label>activeGroupId</label>
            <input type='text' id='activeGroupId'>
            <label>atgHash</label>
            <input type='text' id='atgHash'>     
            <label>ticketItemNumber</label>
            <input type='text' id='ticketItemNumber'>
            <button onclick="updateATGC()">Update ATGC</button>     
        </form>
        <iframe id='ifrAtgc' src='../activeTicketGroupConnector.php'></iframe>
    </body>
</html>