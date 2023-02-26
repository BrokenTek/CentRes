<!DOCTYPE html>
<html lang='en'>
<head>

<!-- <IfModule mod_mime.c>
    AddType application/manifest+json   webmanifest
</IfModule> -->
    <meta charset='utf-8' />
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" href="../Resources/CSS/tableStyles.css" />
    <script src="../Resources/JavaScript/SvgManipulation.js"></script>
    <!-- Will Need To Change CSS File Path Later -->

    <script>
        function clickVerify() {
            alert("Javascript TEST: You clicked on the table styled with a green color using external CSS");
        }


    </script>

</head>

<body>

    <link rel="stylesheet" href="styles.css"></style>
    
    <svg viewBox="0 0 1280 720">

            <svg onpointerdown='clickVerify()' id="L01" class="longtable" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect class="open" width="5%" height="5%" fill="grey" stroke="black" stroke-opacity="0.75" />                  
            </svg>


    </svg>
</body>
</html>

