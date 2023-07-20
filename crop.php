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
if (empty($_GET["cropId"])) {
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
if (!empty($_GET["plotName"])) {
  $plot_name = $_GET['plotName'];
  $plot_size = $_GET['plotSize'];
  $plot_id = $_GET['cropId'];
  $query = "UPDATE tbl_229
              SET plot_name = '$plot_name', plot_size = '$plot_size'
              WHERE plot_id = $plot_id;";

  $result = mysqli_query($connection, $query);
  if (!$result) {
    header("Location: login.php");
    die("DB query failed.");
  }
  $_SESSION['success'] =1;
}
if (!empty($_GET["selectYes"])) {
  $plot_id = $_GET['cropId'];
  $query = "DELETE FROM tbl_229
              WHERE plot_id = $plot_id;";

  $result = mysqli_query($connection, $query);
  if (!$result) {
    header("Location: login.php");
    die("DB query failed.");
  }
  $_SESSION['success'] =2;
  header("Location: index.php");
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
if (!empty($_GET["cropId"])) {
  $plot_id = $_GET["cropId"];
  $query = "SELECT * FROM tbl_229 WHERE plot_id = '$plot_id'";
  $result = mysqli_query($connection, $query);
  $row = mysqli_fetch_assoc($result);
}
if (isset($_SESSION['success']) && $_SESSION['success'] == 1) {
  $dataUpdatedClass = 'data-updated-show';
} else {
  $dataUpdatedClass = 'data-updated-hide';
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
  <script defer="" src="js/scriptsC.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4"></script>
  <title>Agritech</title>
</head>

<body class="wrapper">
  <header>
    <label id="userNameToShowSmall">  &nbsp; &nbsp;Hi, <?php echo $_SESSION["user_name"]; ?></label>
    <div class="logo">
      <a href="index.php" class="logo-link" title="logo"></a>
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
                  echo '<a class="nav-link" aria-current="page" href="newPlot.php">New Plot</a>';
                } else {
                  echo '<a class="nav-link" aria-current="page" href="#">History</a>';
                }
                ?>
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
        <li class="breadcrumb-item"><a href="index.html?prodId=1">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Crop</li><br>
      </ol>
    </nav>
    <div class="upper-main row ">
      <div class="col-12 col-sm-6 col-md-3">
        <a id="btn1" href="#" class="btn btnCrop btn-primary btn-lg active upperGraphButtons" role="button" aria-pressed="true">EXCEPTIONS</a>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <a id="btn2" href="#" class="btn btnCrop btn-primary btn-lg active upperGraphButtons" role="button" aria-pressed="true">REPORTS</a>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <?php if ($_SESSION["user_type"] == "farmer") {
          echo '<a id="btn3" href="newPlot.php? cropId= ' . $row["plot_id"] . ' " class="btn btnCrop btn-primary btn-lg active upperGraphButtons" role="button" aria-pressed="true" >NEW PLOT</a>';
        } else {
          echo '<a id="btn3" href="newPanelty.php?cropId= ' . $row["plot_id"] . '" class="btn btnCrop btn-primary btn-lg active upperGraphButtons" role="button" aria-pressed="true" >NEW PENALTY</a>';
        } ?>
      </div>
      <div class="col-12 col-sm-6 col-md-3">
        <a id="btn4" href="#" class="btn btnCrop btn-primary btn-lg active upperGraphButtons" role="button" aria-pressed="true">CONTACTS</a>
      </div>
    </div>


    <div class="my-chart">
      <h2>Total Use</h2>
      <h2 class="responsive">Plot Name:<span><?php echo $row["plot_name"] ?></span></h2>
      <h2 class="responsive">Pest Size: <span><?php echo $row["plot_size"] ?></span></h2>
      <section>
        <!-- Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModal" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="editModal">Edit Plot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form action="#" method="GET" id="frm">

                  <div class="form-outline mb-4">
                    <label class="form-label">Plot Name:</label>
                    <input type="text" class="form-control" name="plotName" placeholder="name" required>
                  </div>

                  <div class="form-outline mb-4">
                    <label class="form-label">Plot Size:</label>
                    <input type="number" class="form-control" name="plotSize" placeholder="size" min=1 required>
                  </div>

                  <input type="text" name="cropId" <?php echo 'value="' . $row["plot_id"] . '"'; ?> hidden>
                  <div class="modal-footer">
                    <button type="button" id="ModalBtnN" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="ModalBtnY" class="btn btn-primary">Save changes</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

        <div class="modal fade" id="removeModal" tabindex="-1" aria-labelledby="removeModal" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="removeModal">Remove Plot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form method="GET" id="frm">
                  <div class="form-outline mb-4">
                    <label class="form-label">Are You Sure You Want To Delete This Plot?</label>
                  </div>
                  <input type="text" name="cropId" <?php echo 'value="' . $row["plot_id"] . '"'; ?> hidden>
                  <input type="text" name="selectYes" value="1" hidden>
                  <div class="modal-footer">
                    <button type="button" id="ModalBtnN" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="ModalBtnY" class="btn btn-primary">Save changes</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

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
                  <input type="text" name="cropId" <?php echo 'value="' . $row["plot_id"] . '"'; ?> hidden>
                  <div class="modal-footer">
                    <button type="button" id="ModalBtnN" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="ModalBtnY" class="btn btn-primary">Save changes</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </section>
      <section id="dataUpdatedP" class="<?php echo $dataUpdatedClass; ?>">Data updated successfully!!</section>
      <div class="dropdown">
        <?php if ($_SESSION["user_type"] == "farmer") {
          echo '
                <button type="button" class="btn modalBtn btn-primary" data-bs-toggle="modal" data-bs-target="#removeModal">
                <i class="fa fa-trash-o" aria-hidden="true"></i>
                </button>
                <button type="button" class="btn modalBtn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal">
                <i class="fa fa-pencil" aria-hidden="true"></i>
                </button>';
        }
        ?>
        <i id="downloadBtn" class="fa fa-download btn" aria-hidden="true"></i>
        <button class="btn btn-secondary dropdown-toggle" type="button" id="timeRangeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
          All Times
        </button>
        <ul class="dropdown-menu" aria-labelledby="timeRangeDropdown">
          <li><a class="dropdown-item" href="#" data-time-range="All-Times">All Times</a></li>
          <li><a class="dropdown-item" href="#" data-time-range="months">Months</a></li>
          <li><a class="dropdown-item" href="#" data-time-range="days">Days</a></li>
        </ul>
      </div>
      <canvas id="myChart" class="hide-chart"></canvas>
      <canvas id="myChart2" class="hide-chart2"></canvas>
  </main>
</body>

</html>