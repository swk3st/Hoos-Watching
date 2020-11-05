<?php

require_once("include/db_interface.php");

?>


<html>

<head>
</head>

<body>
    <?php
        $top_five = get_top_five_movies();

        echo json_encode($top_five);
    ?>
</body>

</html>