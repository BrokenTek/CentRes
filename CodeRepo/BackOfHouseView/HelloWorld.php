<!-- DISPLAY TEMPLATE
This template includes starter code that allows
you to use display.php and displayInterface.js -->


<!-- ensures you are logged in before rendering page, and are logged in under the correct role.
If you aren't logged in, it will reroute to the login page.
If you are logged in but don't have the correct role to view this page,
you'll be routed to whatever the home page is for your specified role level -->
<!-- CHANGE 255 TO THE ALLOWED ROLE LEVEL FOR THE PAGE -->
<?php header("Access-Control-Allow-Origin: *"); ?>
<!DOCTYPE html>
<?php require_once '../Resources/php/connect_disconnect.php'; ?>
<html>
    <head>
        <script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script> 
        <script>
            function helloWorld() {
                /*
                if (document.getElementById("getMe") != null) {
                    try {
                        alert(document.getElementById("getMe"));
                        document.getElementById("screech").setAttribute("value", document.getElementById("getMe").getAttribute("getMeVal"));
                        
                        updateDisplay();
                    }
                    catch (err) {
                        alert(err);
                    }
                }
                else {
                    setTimeout(helloWorld, 1000);
                }
                */
            }
        </script>
    </head>
    <body id="sessionForm" onload="helloWorld()">
        <h1>Hello World</h1>
        <form id="consternation" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
              <p>this is already here</p>
              <button id="hello">Hi</button> 
              
              <?php if (isset($_POST['upset'])) {
                echo("<h1>upset retained</h1>");
              } ?> 
              <?php require_once '../Resources/php/display.php'; ?>  
        </form>
    </body>
</html>