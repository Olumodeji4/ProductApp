<?php
include_once "inc/autoload.php"; // auto load all classes used in our project

// single route entry to our application (Frontend design pattern)
use app\Request;
use app\Router;

$request = new Request();
$router = new Router();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Product Web App</title>
    <!-- Required meta tags -->
    <meta name="content-type" charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="assets/css/app.scss">
</head>
<body>
<?php
$router->get($request);
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.1.min.js"
        integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>

<script src="assets/js/app.js"></script>
</body>
</html>