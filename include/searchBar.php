<!DOCTYPE HTML>

<html>

<head>
    <meta http-equiv="Content-Type" contents="text/html; charset=utf-8">

    <title> Search Bar </title>

    <link rel="stylesheet" href="https://cdnjs.cloudfare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css">

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <link href="assets\php\searchBarStyle.css" rel="stylesheet">

    <script src="searchJSFunc.js"></script>

</head>
    <body>
        <div class="container">
            <hl class="title">What are you searching for? </hl>
            <div class="form-group">
                <div class="dropdown">
                    <div class="default-option"> Category </div>
                    <div class="dropdown-list">
                        <ul>
                            <li> Movie </li>
                            <li> TV Show </li>
                            <li> Actor </li>
                            <li> Director </li>
                        </ul>
                    </div>
                </div>

                    <div class="search">
                        <input type="text" class="search-input" placeholder="Search for Movies, TV shows, actors, directors...">
                    </div>

                    <button class="btn" type="submit"><i class="fas fa-search"></i></button>
            </div>
        </div>
    </body>

</html>