<?php
    require_once '../Resources/php/connect_disconnect.php';

    $sql = "SELECT id from TicketItems WHERE ticketItemStatus(id) = 'Preparing';";
    
    $itemsToMark = connection()->query($sql);

    if (mysqli_num_rows($itemsToMark) == 0) {
        echo("<h1><b>0</b> ticket items were found that have a status of <b>Pending</b></h1>");
    }
    else {
        while($item = $itemsToMark->fetch_assoc()) {
            $sql = "CALL markTicketItemAsReady(" .$item['id']. ");";
            connection()->query($sql);
            echo("<h1><b>" .$item['id']. "</b> was marked as ready");
        }
    }
?>