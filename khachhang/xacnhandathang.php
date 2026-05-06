<?php
session_start();
include("../class/classtmdt.php");

if (!isset($_SESSION['idkh'])) {
    header('location:../login/loginkhachhang.php');
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
$TY_GIA    = 25000;
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Xác Nhận Đặt Hàng</title>
    <link rel="stylesheet" type="text/css" href="../css/css.css">
    <style>
        table { border-collapse: collapse; width: 90%; margin: 10px auto; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background: #f0f0f0; }
        img { max-width: 70px; }
        .tong { font-weight: bold; font-size: 15px; text-align: right; width: 90%; margin: 8px auto; }
        .form-diachi { width: 90%; margin: 15px auto; }
        .form-diachi label { font-weight: bold; display: block; margin-bottom: 5px; }
        .form-diachi input[type=text] { width: 100%; padding: 6px; box-sizing: border-box; font-size: 14px; }
        .btn-datHang { background: #e44; color: #fff; padding: 8px 20px; border: none; font-size: 15px;
                       cursor: pointer; border-radius: 4px; }
        .loi { color: red; text-align: center; font-weight: bold; }
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
                <p class="loi"><?php echo $loi; ?></p>
            <?php endif; ?>

            <?php if (empty($danhsach)): ?>
                <p align="center">Giỏ hàng trống. <a href="../index.php">Tiếp tục mua sắm</a></p>
            <?php else: ?>

            <h4 align="center">Danh sách sản phẩm</h4>
            <table>
                <tr>
                    <th>Hình</th>
                    <th>Tên sản phẩm</th>
                    <th>Đơn giá (VNĐ)</th>
                    <th>Số lượng</th>
                    <th>Thành tiền (VNĐ)</th>
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
                    <td><?php echo number_format($item['gia'] * $TY_GIA, 0, ',', '.'); ?> VNĐ</td>
                    <td><?php echo $item['soluong']; ?></td>
                    <td><?php echo number_format($thanhtien * $TY_GIA, 0, ',', '.'); ?> VNĐ</td>
                </tr>
                <?php endforeach; ?>
            </table>
            <div class="tong">
                Tổng cộng: <?php echo number_format($tongtien * $TY_GIA, 0, ',', '.'); ?> VNĐ
            </div>

            <form method="post" action="">
                <div class="form-diachi">
                    <label>Địa chỉ nhận hàng:</label>
                    <input type="text" name="diachinhanhang"
                           value="<?php echo isset($khachhang['diachinhanhang']) ? htmlspecialchars($khachhang['diachinhanhang']) : ''; ?>"
                           placeholder="Nhập địa chỉ nhận hàng" />
                    <br><br>
                    <div align="center">
                        <input type="submit" name="datHang" class="btn-datHang" value="Xác nhận đặt hàng" />
                        &nbsp;&nbsp;
                        <a href="giohang.php">Quay lại giỏ hàng</a>
                    </div>
                </div>
            </form>

            <?php endif; ?>
        </div>
    </div>
    <div id="footer">FOOTER</div>
</div>
</body>
</html>
