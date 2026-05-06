<?php
if (session_id() == '') session_start();
if (!isset($_SESSION['id']) || !isset($_SESSION['user'])) {
    header('location:../login/login.php');
    exit();
}
if (!isset($p)) {
    include_once("../class/classtmdt.php");
    $p = new csdltmdt();
}

$TY_GIA = 25000;

// Cập nhật trạng thái đơn hàng
if (isset($_GET['capnhat']) && isset($_GET['iddh']) && isset($_GET['tt'])) {
    $p->capNhatTrangThaiDonHang(intval($_GET['iddh']), intval($_GET['tt']));
    header('location:danhsachdathang.php');
    exit();
}

// Xem chi tiết đơn hàng
$chiTiet = array();
$iddhXem = 0;
if (isset($_GET['xemdon'])) {
    $iddhXem = intval($_GET['xemdon']);
    $chiTiet = $p->xemChiTietDonHang($iddhXem);
}

$danhsach = $p->xemDSDatHangAdmin();

$trangThaiLabel = array(0 => 'Chờ xử lý', 1 => 'Đang giao', 2 => 'Đã giao');
$trangThaiColor = array(0 => '#e6a817', 1 => '#2196F3', 2 => '#4caf50');
?>
<style>
    table { border-collapse: collapse; width: 97%; margin: 10px auto; }
    th, td { border: 1px solid #ccc; padding: 7px; text-align: center; font-size: 13px; }
    th { background: #f0f0f0; }
    img { max-width: 55px; }
    .trangthai { font-weight: bold; padding: 2px 8px; border-radius: 4px; color: #fff; font-size: 12px; }
    select.tt-select { font-size: 12px; padding: 2px; }
    .box-tongtke { background: #fff8e1; border: 1px solid #f0c040; border-radius: 6px;
                   padding: 12px 20px; width: 97%; margin: 10px auto; box-sizing: border-box; }
    .box-tongtke .so { font-size: 20px; font-weight: bold; color: #c00; }
</style>

<h3 align="center">DANH SÁCH ĐẶT HÀNG</h3>

<?php
$tongTatCa = 0;
foreach ($danhsach as $dh) $tongTatCa += $dh['tongtien'];
?>
<div class="box-tongtke">
    <b>Tổng số đơn:</b> <span class="so"><?php echo count($danhsach); ?></span> &nbsp;&nbsp;|&nbsp;&nbsp;
    <b>Tổng doanh thu:</b>
    <span class="so"><?php echo number_format($tongTatCa * $TY_GIA, 0, ',', '.'); ?> VNĐ</span>
</div>

<?php if (empty($danhsach)): ?>
    <p align="center">Chưa có đơn hàng nào.</p>
<?php else: ?>
<table>
    <tr>
        <th>Mã ĐH</th>
        <th>Khách hàng</th>
        <th>Email</th>
        <th>Điện thoại</th>
        <th>Địa chỉ nhận</th>
        <th>Ngày đặt</th>
        <th>Tổng tiền (VNĐ)</th>
        <th>Trạng thái</th>
        <th>Chi tiết</th>
    </tr>
    <?php foreach ($danhsach as $dh):
        $tt    = intval($dh['trangthai']);
        $label = isset($trangThaiLabel[$tt]) ? $trangThaiLabel[$tt] : 'Không rõ';
        $color = isset($trangThaiColor[$tt]) ? $trangThaiColor[$tt] : '#999';
    ?>
    <tr>
        <td>#<?php echo $dh['iddh']; ?></td>
        <td><?php echo $dh['hodem'] . ' ' . $dh['ten']; ?></td>
        <td><?php echo $dh['email']; ?></td>
        <td><?php echo $dh['dienthoai']; ?></td>
        <td><?php echo $dh['diachinhanhang']; ?></td>
        <td><?php echo $dh['ngaydathang']; ?></td>
        <td><?php echo number_format($dh['tongtien'] * $TY_GIA, 0, ',', '.'); ?> VNĐ</td>
        <td>
            <span class="trangthai" style="background:<?php echo $color; ?>"><?php echo $label; ?></span>
            <br>
            <select class="tt-select" onchange="window.location='danhsachdathang.php?capnhat=1&iddh=<?php echo $dh['iddh']; ?>&tt='+this.value">
                <option value="">-- Đổi --</option>
                <?php foreach ($trangThaiLabel as $val => $lbl): ?>
                    <option value="<?php echo $val; ?>" <?php if ($tt == $val) echo 'selected'; ?>>
                        <?php echo $lbl; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
        <td><a href="danhsachdathang.php?xemdon=<?php echo $dh['iddh']; ?>">Xem</a></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<?php if ($iddhXem > 0): ?>
<hr>
<h4 align="center">Chi tiết đơn hàng #<?php echo $iddhXem; ?></h4>
<?php if (empty($chiTiet)): ?>
    <p align="center">Không có dữ liệu.</p>
<?php else: ?>
<table>
    <tr>
        <th>Hình</th>
        <th>Tên sản phẩm</th>
        <th>Đơn giá (VNĐ)</th>
        <th>Số lượng</th>
        <th>Thành tiền (VNĐ)</th>
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
        <td><?php echo number_format($sp['dongia'] * $TY_GIA, 0, ',', '.'); ?> VNĐ</td>
        <td><?php echo $sp['soluong']; ?></td>
        <td><?php echo number_format($tt_sp * $TY_GIA, 0, ',', '.'); ?> VNĐ</td>
    </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="4" align="right"><b>Tổng đơn:</b></td>
        <td><b><?php echo number_format($tongDon * $TY_GIA, 0, ',', '.'); ?> VNĐ</b></td>
    </tr>
</table>
<?php endif; ?>
<?php endif; ?>
