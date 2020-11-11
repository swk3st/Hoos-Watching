<?php

require_once("include/db_interface.php");
require_once("include/title-funcs.php");
require_once("include/user-funcs.php");
require_once("include/util.php");

$title = null;
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['tconst'])) {
        $title = title_get_info($_GET['tconst']);
    }
}


$HEADER_INFO = array(
    "Hoo's Watching | " . $title['primaryTitle'],
    $title['primaryTitle'] . " <small class='text-muted'> <a href=\"./index.php\">Hoo's Watching</a></small> ",
    "Hoo's Watching | " . $title['primaryTitle']
);
include("include/boilerplate/head.php");
?>

<link href="https://res.cloudinary.com/dxfq3iotg/raw/upload/v1557937989/lib.css" rel="stylesheet" type="text/css">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://res.cloudinary.com/dxfq3iotg/raw/upload/v1557937989/lib.js"></script>
<div class="page-content page-container" id="page-content">
<link rel="stylesheet" href="assets/css/watchlist.css">
    <div class="padding">
        <div class="row container d-flex justify-content-center">
            <div class="col-lg-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Title</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Hitesh Chauhan</td>
                                        <td>Engine</td>
                                    </tr>
                                    <tr>
                                        <td>Samso Palto</td>
                                        <td>Brakes</td>
                                    </tr>
                                    <tr>
                                        <td>Tiplis mang</td>
                                        <td>Window</td>
                                    </tr>
                                    <tr>
                                        <td>Pter parker</td>
                                        <td>Head light</td>
                                    </tr>
                                    <tr>
                                        <td>Ankit Dave</td>
                                        <td>Back light</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>