

<!DOCTYPE html>
<?php require_once 'connect_disconnect.php'; ?>
<html>
    <head>
    </head>
    <body>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            
            <?php
                if (isset($_POST['username'])) {
                    $cookie_name = "804288a34eb7a49b349be68fc6437621cbf25e10d82f4268bb795eca277adedb6a3367add5bfb7cbffb50df150e2e78d26b276f37d32d96cd76746065df58a30cde25c4d9803aa7214dc8f6a985bf8643c341f229b5834964b0f371915d5677e4b579fbab42844cd63ddc3148e4250591277cfc521906bc30cfedd765974c2009ae5fe451ab1890e5ebbfa120ad18934c972618dbe3e";
                    $sql = "SELECT accessToken FROM Employees WHERE userName = '" .$_POST['username']. "';";
                    $result = connection()->query($sql);
                    $_POST['sessionValue'] = connection()->query($sql)->fetch_assoc()['accessToken'];
                }
                else {
                    unset($_POST['sessionValue']);
                }
            ?>
            <?php require_once 'display.php'; ?>
           
        </form>
    </body>
</html>