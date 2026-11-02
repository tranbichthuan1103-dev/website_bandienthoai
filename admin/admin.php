<?php
session_start();
if (isset($_SESSION['id']) && isset($_SESSION['user']) && isset($_SESSION['pass']) && isset($_SESSION['phanquyen']) && $_SESSION['phanquyen'] == 1) {
  require_once("../classlogin/clslogin.php");
  $q = new login();
  $q->confirmlogin($_SESSION['id'], $_SESSION['user'], $_SESSION['pass'], $_SESSION['phanquyen']);
} else {
  header('location:../login/login.php');
  exit();
}

require_once("../class/classtmdt.php");
define('IN_ADMIN', true);
$p = new csdltmdt();
?>
<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <title>TRANG QUẢN TRỊ</title>
</head>

<body>
  <div align="center" style="margin-top: 10px; margin-bottom: 20px;">
    <a href="admin.php?page=sanpham">Quản lý sản phẩm</a> |
    <a href="admin.php?page=dathang">Quản lý bán hàng</a> |
    <a href="../login/logout.php">Đăng xuất</a>
  </div>

  <hr>

  <?php
  $page = isset($_GET['page']) ? $_GET['page'] : 'sanpham';

  switch ($page) {
    case 'dathang':
      include("danhsachdathang.php");
      break;

    case 'sanpham':
    case 'ds':
    case 'them':
    default:
      include("danhsachsanpham.php");
      break;
  }
  ?>
</body>

</html>