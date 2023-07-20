<?php
include "config.php";
$connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

if (mysqli_connect_errno()) {
    header("Location: login.php");
    die("DB connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
}
?>

<?php
session_start();
if (!isset($_SESSION["user_id"]) || empty($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}
if ($_SESSION["user_type"] != 'farmer') {
    header("Location: index.php");
}
$_SESSION['success'] =0;
if (!empty($_GET["newUserName"])) {
    $new_userName = $_GET['newUserName'];
    $_SESSION["user_name"] = $_GET['newUserName'];
    $new_password = $_GET['newPassword'];
    $user_id = intval($_SESSION["user_id"]);
    $query = "UPDATE tbl_229_users
                SET name = '$new_userName', password = '$new_password'
                WHERE ID = $user_id;";

    $result = mysqli_query($connection, $query);
    if (!$result) {
        header("Location: login.php");
        die("DB query failed.");
    }
}
$farmer_id_penalty = $_SESSION["user_id"];
$sql = "SELECT COUNT(*) AS penaltyCount FROM tbl_229_penalty WHERE farmer_id = $farmer_id_penalty";
$result = mysqli_query($connection, $sql);
if (!$result) {
    header("Location: login.php");
    die("DB query failed.");
}
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $penaltyCount = $row["penaltyCount"];
} else {
    $penaltyCount = 0;
}
if (!empty($_POST["inputIrrigation"])) {
    $plot_name = $_POST["plotName"];
    $grain = $_POST["inputGrain"];
    $inputIrrigation = $_POST["inputIrrigation"];
    $summary = $_POST["description"];
    $farmer_id = intval($_SESSION["user_id"]);
    $plot_size = intval($_POST['inputSize']);

    $possibleAVG = ['+12%', '+23%', '+3%', '+19%', '+35%', '-13%'];
    $possibleLevel = ['Medium', 'High', 'Low'];

    $AVG = $possibleAVG[array_rand($possibleAVG)];
    $level = $possibleLevel[array_rand($possibleLevel)];

    $query = "INSERT INTO tbl_229 (user_id, plot_name, AVG, level, crop_type, plot_size, irrigation, summary)
          VALUES ( $farmer_id,'$plot_name', '$AVG', '$level', '$grain', $plot_size, '$inputIrrigation', '$summary')";

    $result = mysqli_query($connection, $query);
    if (!$result) {
        header("Location: login.php");
        die("DB query failed.");
    }
    header("Location: index.php");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Assistant:wght@500&family=Passions+Conflict&display=swap');
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4"></script>
    <title>Agritech</title>
</head>

<body class="wrapper">
    <header>
         <label id="userNameToShowSmall">  &nbsp; &nbsp;Hi, <?php echo $_SESSION["user_name"]; ?></label>
        <div class="logo">
            <a href="index.php" class="logo-link" alt="logo" title="logo"></a>
        </div>
        <div class="navigatin">
            <nav class="navbar navbar-expand-lg bg-body-tertiary">
                <div class="container-fluid">
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link" aria-current="page" href="index.php">Home</a>
                            </li>
                            <li class="nav-item">
                                <?php if ($_SESSION["user_type"] == "farmer") {
                                    echo '<a class="nav-link selectedNav" aria-current="page" href="#">New Plot</a>';
                                } else {
                                    echo '<a class="nav-link" aria-current="page" href="#">History</a>';
                                }
                                ?>
                            </li>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="penaltyList.php">Penalties<?php if ($_SESSION["user_type"] == "farmer") {
                                                                                        echo '<span class="penaltySum">(<span class="penaltySum" id="penalySumNum">' . $penaltyCount . '</span>)</span>';
                                                                                    } ?></a>
                            </li>
                            <li class="nav-item logOutToggle">
                                <a id="logout" href="login.php"><i class="fa fa-sign-out" aria-hidden="true"></i>Logout</a>
                            </li>
                        </ul>
                        <form class="d-flex" role="search">
                            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                            <button class="btn btn-outline-success" type="submit">Search</button>
                        </form>
                    </div>
                </div>
            </nav>
        </div>
        <div class="profilePic">
            <a href="#" id="editProfilePic">
                <img <?php echo 'src=' . $_SESSION['user_img'] . '' ?> alt="profile picture" title="profile picture">
            </a>
        </div>
    </header>
    <main>
        <div class="side-menu">
            <a href="#"><i class="fa fa-envelope-open-o" aria-hidden="true"></i> Messages</a>
            <a href="#"><i class="fa fa-newspaper-o" aria-hidden="true"></i>Articles</a>
            <a href="#"><i class="fa fa-user-o" aria-hidden="true"></i>Profile</a>
            <section class="userTool"><a href="#"><i class="fa fa-address-book-o" aria-hidden="true"></i>Contact us</a><br><a href="#"><i class="fa fa-cog" aria-hidden="true"></i>Settings</a><br><a id="logout" href="login.php"><i class="fa fa-sign-out" aria-hidden="true"></i>Logout</a></section>
            <label id="userNameToShow">Hi, <?php echo $_SESSION["user_name"]; ?></label>
        </div>
        <nav style="--bs-breadcrumb-divider: url(&#34;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Cpath d='M2.5 0L1 1.5 3.5 4 1 6.5 2.5 8l4-4-4-4z' fill='%236c757d'/%3E%3C/svg%3E&#34;);" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Crop</li>
            </ol>
        </nav>
        <div class="newPenalty">
            <div class="newPenaltyH2">New Plot
                <p class="newPlotP">Please fill out the form below to register a new plot</p>
            </div>
            <form action="#" method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-sm-4">
                        <label class="form-label">Plot Name</label>
                        <input type="text" class="form-control input-field" id="plotName" name="plotName" placeholder="name">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Irrigation System</label>
                        <select class="form-control input-field" id="inputIrrigation" name="inputIrrigation">
                            <option value="" disabled selected>Select irrigation system</option>
                            <option value="drip">Drip Irrigation</option>
                            <option value="sprinkler">Sprinkler System</option>
                            <option value="surface">Surface Irrigation</option>
                            <option value="subsurface">Subsurface Drip Irrigation</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <label class="form-label">Type of Grain</label>
                        <input type="text" class="form-control input-field" id="inputGrain" name="inputGrain" placeholder="type of grain" required>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Plot size</label>
                        <input type="number" class="form-control input-field" id="inputSize" name="inputSize" min="1" placeholder="size(m)" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Describe</label>
                    <textarea class="form-control" id="description" name="description" placeholder="More details..."></textarea>
                </div>
                <div class="newPenaltyButton">
                    <div id="BACK" class="col-md-6">
                        <button type="button" class="btn btn-primary" onclick="window.location.href='crop.php'">BACK</button>
                    </div>
                    <div id="SUBMIT" class="col-6 ms-auto">
                        <button type="submit" class="btn btn-primary">SUBMIT</button>
                    </div>
                </div>
            </form>
        </div>
    </main>
    <div class="modal fade" id="editModalProfile" tabindex="-1" aria-labelledby="editModalProfile" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalProfile">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" method="GET" id="frm">

                        <div class="form-outline mb-4">
                            <label class="form-label">User Name:</label>
                            <input type="text" class="form-control" name="newUserName" placeholder="name" required>
                        </div>

                        <div class="form-outline mb-4">
                            <label class="form-label">Password:</label>
                            <input type="password" class="form-control" name="newPassword" placeholder="password" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="ModalBtnN" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="ModalBtnY" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#editProfilePic').on('click', function(event) {
                event.preventDefault(); 
                $('#editModalProfile').modal('show'); 
            });
        });
    </script>
</body>

</html>