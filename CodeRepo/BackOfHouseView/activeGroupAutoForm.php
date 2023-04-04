<!DOCTYPE html>
<html>
    <head>
        <script>
            function loaded() {
                setTimeout(() => {
                    document.getElementById("frm").submit();
                }, 2000);
            }
        </script>
    </head>
    <body onload="loaded()">
        <form id="frm" action="ifrTicketGroup.php?windowHash=<?php echo($_GET['windowHash']); ?>" method="POST" id="frmATGwindowRequest">
            <label for="windowHash">
            <input type="text" id="windowHash" name="windowHash" value="<?php echo($_GET['windowHash']); ?>">
        </form>
    </body>
</html>