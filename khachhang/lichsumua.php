<?php
session_start();
include("../class/classtmdt.php");

if (!isset($_SESSION['idkh'])) {
    header('location:../login/login.php');
    exit();
}

$p      = new csdltmdt();
$idkh   = $_SESSION['idkh'];

$danhsachDH = $p->xemLichSuMuaHang($idkh);

// Trạng thái đơn hàng
$trangThaiLabel = array(
    0 => 'Chờ xử lý',
    1 => 'Đang giao',
    2 => 'Đã giao'
);
$trangThaiColor = array(
    0 => '#e6a817',
    1 => '#2196F3',
    2 => '#4caf50'
);

// Xem chi tiết 1 đơn hàng
$chiTiet  = array();
$iddhXem  = 0;
if (isset($_GET['xemdon'])) {
    $iddhXem = intval($_GET['xemdon']);
    $chiTiet = $p->xemChiTietDonHang($iddhXem);
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lịch Sử Mua Hàng</title>
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
                <li><strong>Lịch sử mua hàng</strong></li>
                <li><a href="../login/logout.php">Đăng xuất</a></li>
            </ul>
        </div>
        <div id="right">
            <h3 align="center">LỊCH SỬ MUA HÀNG</h3>

            <?php if (isset($_GET['success'])): ?>
                <p align="center" style="color:green; font-weight:bold;">
                    Đặt hàng thành công! Cảm ơn bạn đã mua hàng.
                </p>
            <?php endif; ?>

            <?php
            // ---- THỐNG KÊ TỔNG ----
            $tongTatCa   = 0;
            $soDonHang   = count($danhsachDH);
            foreach ($danhsachDH as $dh) {
                $tongTatCa += $dh['tongtien'];
            }
            ?>
            <p><b>Tổng số đơn hàng:</b> <?php echo $soDonHang; ?> đơn &nbsp;|&nbsp;
               <b>Tổng tiền đã chi:</b> <?php echo number_format($tongTatCa, 0, ',', '.'); ?> USD</p>

            <?php if (empty($danhsachDH)): ?>
                <p align="center">Bạn chưa có đơn hàng nào. <a href="../index.php">Mua sắm ngay</a></p>
            <?php else: ?>

            <table>
                <tr>
                    <th>Mã ĐH</th>
                    <th>Ngày đặt</th>
                    <th>Tổng tiền (USD)</th>
                    <th>Trạng thái</th>
                    <th>Chi tiết</th>
                </tr>
                <?php foreach ($danhsachDH as $dh):
                    $tt    = intval($dh['trangthai']);
                    $label = isset($trangThaiLabel[$tt]) ? $trangThaiLabel[$tt] : 'Không rõ';
                    $color = isset($trangThaiColor[$tt]) ? $trangThaiColor[$tt] : '#999';
                ?>
                <tr>
                    <td><?php echo $dh['iddh']; ?></td>
                    <td><?php echo $dh['ngaydathang']; ?></td>
                    <td><?php echo number_format($dh['tongtien'], 0, ',', '.'); ?> USD</td>
                    <td><?php echo $label; ?></td>
                    <td><a href="lichsumua.php?xemdon=<?php echo $dh['iddh']; ?>">Xem</a></td>
                </tr>
                <?php endforeach; ?>
            </table>

            <?php endif; ?>

            <?php if ($iddhXem > 0): ?>
            <hr>
            <h4 align="center">Chi tiết đơn hàng <?php echo $iddhXem; ?></h4>
            <?php if (empty($chiTiet)): ?>
                <p align="center">Không có dữ liệu.</p>
            <?php else: ?>
            <table>
                <tr>
                    <th>Hình</th>
                    <th>Tên sản phẩm</th>
                    <th>Đơn giá (USD)</th>
                    <th>Số lượng</th>
                    <th>Thành tiền (USD)</th>
                </tr>
                <?php
                $tongDon = 0;
                foreach ($chiTiet as $sp):
                    $tt_sp = $sp['dongia'] * $sp['soluong'];
                    $tongDon += $tt_sp;
                ?>
                <tr>
                    <td><img src="../img/<?php echo $sp['hinh']; ?>" /></td>
                    <td><?php echo $sp['tensp']; ?></td>
                    <td><?php echo number_format($sp['dongia'], 0, ',', '.'); ?> USD</td>
                    <td><?php echo $sp['soluong']; ?></td>
                    <td><?php echo number_format($tt_sp, 0, ',', '.'); ?> USD</td>
                </tr>
                <?php endforeach; ?>
            </table>
            <p><b>Tổng đơn: <?php echo number_format($tongDon, 0, ',', '.'); ?> USD</b></p>
            <?php endif; ?>
            <?php endif; ?>

        </div>
    </div>
    <div id="footer">FOOTER</div>
</div>
</body>
</html>
