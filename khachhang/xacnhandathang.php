<?php
session_start();
include("../class/classtmdt.php");

if (!isset($_SESSION['idkh'])) {
    header('location:../login/login.php');
    exit();
}

$p    = new csdltmdt();
$idkh = $_SESSION['idkh'];

// Xử lý xác nhận đặt hàng
if (isset($_POST['datHang'])) {
    $diachinhanhang = trim($_POST['diachinhanhang']);
    if ($diachinhanhang == '') {
        $loi = 'Vui lòng nhập địa chỉ nhận hàng!';
    } else {
        $iddh = $p->xacNhanDatHang($idkh, $diachinhanhang);
        if ($iddh > 0) {
            header('location:lichsumua.php?success=1');
            exit();
        } else {
            $loi = 'Giỏ hàng trống, không thể đặt hàng!';
        }
    }
}

$danhsach  = $p->xemChiTietGiohang($idkh);
$khachhang = $p->layThongTinKhachHang($idkh);
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Xác Nhận Đặt Hàng</title>
    <link rel="stylesheet" type="text/css" href="../css/css.css">
    <style>
        table { border-collapse: collapse; width: 95%; margin: 10px auto; }
        td, th { border: 1px solid #ccc; padding: 6px; text-align: left; }
        img { max-width: 80px; }
    </style>
</head>
<body>
<div id="container">
    <div id="banner">BANNER</div>
    <div id="main">
        <div id="left">
            <ul style="list-style:none; padding:0; margin:0;">
                <li><a href="../index.php">Trang Chủ</a></li>
                <li><a href="giohang.php">Giỏ Hàng</a></li>
                <li><a href="../login/logout.php">Đăng xuất</a></li>
            </ul>
        </div>
        <div id="right">
            <h3 align="center">XÁC NHẬN ĐẶT HÀNG</h3>

            <?php if (isset($loi)): ?>
                <p><?php echo $loi; ?></p>
            <?php endif; ?>

            <?php if (empty($danhsach)): ?>
                <p align="center">Giỏ hàng trống. <a href="../index.php">Tiếp tục mua sắm</a></p>
            <?php else: ?>

            <h4 align="center">Danh sách sản phẩm</h4>
            <table>
                <tr>
                    <th>Hình</th>
                    <th>Tên sản phẩm</th>
                    <th>Đơn giá (USD)</th>
                    <th>Số lượng</th>
                    <th>Thành tiền (USD)</th>
                </tr>
                <?php
                $tongtien = 0;
                foreach ($danhsach as $item):
                    $thanhtien = $item['gia'] * $item['soluong'];
                    $tongtien += $thanhtien;
                ?>
                <tr>
                    <td><img src="../img/<?php echo $item['hinh']; ?>" /></td>
                    <td><?php echo $item['tensp']; ?></td>
                    <td><?php echo number_format($item['gia'], 0, ',', '.'); ?> USD</td>
                    <td><?php echo $item['soluong']; ?></td>
                    <td><?php echo number_format($thanhtien, 0, ',', '.'); ?> USD</td>
                </tr>
                <?php endforeach; ?>
            </table>
            <p><b>Tổng cộng: <?php echo number_format($tongtien, 0, ',', '.'); ?> USD</b></p>

            <form method="post" action="">
                <p>
                    <label>Địa chỉ nhận hàng:</label><br>
                    <input type="text" name="diachinhanhang" size="60"
                           value="<?php echo isset($khachhang['diachinhanhang']) ? htmlspecialchars($khachhang['diachinhanhang']) : ''; ?>" />
                </p>
                <p>
                    <input type="submit" name="datHang" value="Xác nhận đặt hàng" />
                    &nbsp;&nbsp;
                    <a href="giohang.php">Quay lại giỏ hàng</a>
                </p>
            </form>

            <?php endif; ?>
        </div>
    </div>
    <div id="footer">FOOTER</div>
</div>
</body>
</html>
