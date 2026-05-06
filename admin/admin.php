<?php
session_start();
if (isset($_SESSION['id']) && isset($_SESSION['user']) && isset($_SESSION['pass']) && isset($_SESSION['phanquyen'])) {
  require_once("../classlogin/clslogin.php");
  $q = new login();
  $q->confirmlogin($_SESSION['id'], $_SESSION['user'], $_SESSION['pass'], $_SESSION['phanquyen']);
} else {
  header('location:../login/login.php');
  exit();
}
?>
<?php
require_once("../class/classtmdt.php");
$p = new csdltmdt();
?>
<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <title>ADMIN</title>
  <style type="text/css">
    #nut {
      text-align: center;
    }
  </style>
</head>

<body>
  <h2 align="center">QUẢN LÝ SẢN PHẨM</h2>

  <div style="text-align:center; margin-bottom:20px;">
    <a href="?page=ds">Danh sách</a> |
    <a href="?page=them">Thêm</a> 
  </div>

  <hr>

  <?php
  $page = isset($_GET['page']) ? $_GET['page'] : 'ds';

  switch ($page) {
    case 'them':
      include("themsanpham.php");
      break;

    case 'ds':
      include("danhsachsanpham.php"); 
      break;
  }
  ?>
</body>

</html>