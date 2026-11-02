<?php
if (session_id() == '') {
    session_start();
}

// 1. Kiểm tra quyền Admin
if (!isset($_SESSION['id']) || !isset($_SESSION['user']) || !isset($_SESSION['phanquyen']) || $_SESSION['phanquyen'] != 1) {
    header('location:../login/login.php');
    exit();
}

if (!isset($p)) {
    include_once("../class/classtmdt.php");
    $p = new csdltmdt();
}

// Danh sách trạng thái chuẩn trong CSDL
$trangThaiLabel = array(
    0 => 'Chờ xử lý',
    1 => 'Đang giao',
    2 => 'Đã giao'
);

// 2. Danh sách các năm thực tế có trong CSDL (dùng cho bộ lọc và biểu đồ)
$danhSachNam = $p->layDanhSachNamDonHang();
$namMacDinh = !empty($danhSachNam) ? $danhSachNam[0] : intval(date('Y'));

// 3. Xử lý CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG bằng phương thức POST
if (isset($_POST['btnCapNhatTT'])) {
    $iddh_capnhat = isset($_POST['iddh']) ? intval($_POST['iddh']) : 0;
    $tt_moi = isset($_POST['trangthai_moi']) ? intval($_POST['trangthai_moi']) : -1;

    // Giữ lại các tham số lọc hiện tại khi chuyển hướng
    $params = array('page=dathang');
    if (isset($_POST['kieu_thoigian'])) $params[] = "kieu_thoigian=" . urlencode($_POST['kieu_thoigian']);
    if (isset($_POST['loc_ngay']))       $params[] = "loc_ngay=" . urlencode($_POST['loc_ngay']);
    if (isset($_POST['loc_thang']))      $params[] = "loc_thang=" . urlencode($_POST['loc_thang']);
    if (isset($_POST['loc_nam']))        $params[] = "loc_nam=" . urlencode($_POST['loc_nam']);
    if (isset($_POST['loc_trangthai']))  $params[] = "loc_trangthai=" . urlencode($_POST['loc_trangthai']);
    if (isset($_POST['kieuBD']))         $params[] = "kieuBD=" . urlencode($_POST['kieuBD']);
    if (isset($_POST['namBD']))          $params[] = "namBD=" . urlencode($_POST['namBD']);
    if (isset($_POST['trang_dh']))       $params[] = "trang_dh=" . intval($_POST['trang_dh']);
    if (isset($_POST['xemdon']) && intval($_POST['xemdon']) > 0) {
        $params[] = "xemdon=" . intval($_POST['xemdon']);
    }
    $redirect_url = "admin.php?" . implode("&", $params);

    if ($iddh_capnhat <= 0) {
        echo "<script>alert('Mã đơn hàng không hợp lệ!'); window.location.href='" . $redirect_url . "';</script>";
        exit();
    } else if (!array_key_exists($tt_moi, $trangThaiLabel)) {
        echo "<script>alert('Trạng thái được chọn không hợp lệ!'); window.location.href='" . $redirect_url . "';</script>";
        exit();
    } else {
        $link = mysql_connect("localhost", "usertmdt", "passtmdt");
        mysql_select_db("tmdt_db", $link);
        $check_dh = mysql_query("SELECT iddh FROM dathang WHERE iddh = $iddh_capnhat LIMIT 1", $link);
        if (!$check_dh || mysql_num_rows($check_dh) == 0) {
            echo "<script>alert('Đơn hàng $iddh_capnhat không tồn tại trong hệ thống!'); window.location.href='" . $redirect_url . "';</script>";
            exit();
        } else {
            if ($p->capNhatTrangThaiDonHang($iddh_capnhat, $tt_moi) == 1) {
                echo "<script>alert('Cập nhật trạng thái đơn hàng $iddh_capnhat thành công!'); window.location.href='" . $redirect_url . "';</script>";
                exit();
            } else {
                echo "<script>alert('Lỗi cập nhật trạng thái đơn hàng!'); window.location.href='" . $redirect_url . "';</script>";
                exit();
            }
        }
    }
}

// =======================================================
// KHU VỰC 1: XỬ LÝ DỮ LIỆU BỘ LỌC, THỐNG KÊ VÀ DANH SÁCH
// =======================================================
$kieuThoiGian = isset($_GET['kieu_thoigian']) ? trim($_GET['kieu_thoigian']) : 'all';
$locNgay      = isset($_GET['loc_ngay']) ? trim($_GET['loc_ngay']) : date('Y-m-d');
$locThang     = isset($_GET['loc_thang']) ? intval($_GET['loc_thang']) : intval(date('n'));
$locNam       = isset($_GET['loc_nam']) ? intval($_GET['loc_nam']) : $namMacDinh;

$trangThaiLoc = -1;
if (isset($_GET['loc_trangthai']) && $_GET['loc_trangthai'] !== '-1' && $_GET['loc_trangthai'] !== '') {
    $trangThaiLoc = intval($_GET['loc_trangthai']);
}

// Truy vấn danh sách đơn hàng theo điều kiện lọc kết hợp
$danhsach = $p->xemDSDatHangAdminFilter($kieuThoiGian, $locNgay, $locThang, $locNam, $trangThaiLoc);

// Tính toán thống kê doanh thu và số đơn theo phạm vi lọc
$tongSoDon = count($danhsach);
$tongDoanhThu = 0;
foreach ($danhsach as $dh) {
    $tongDoanhThu += $dh['tongtien'];
}

// Phân trang danh sách đơn hàng: 5 đơn hàng mỗi trang
$limit_dh = 5;
$tongSoTrangDH = ceil($tongSoDon / $limit_dh);
$trangHienTaiDH = isset($_GET['trang_dh']) ? intval($_GET['trang_dh']) : 1;
if ($trangHienTaiDH < 1) $trangHienTaiDH = 1;
if ($tongSoTrangDH > 0 && $trangHienTaiDH > $tongSoTrangDH) $trangHienTaiDH = $tongSoTrangDH;

$offset_dh = ($trangHienTaiDH - 1) * $limit_dh;
$danhsachTrang = array_slice($danhsach, $offset_dh, $limit_dh);

// Xác định nhãn cho kết quả lọc
$nhanThoiGian = 'Tất cả';
if ($kieuThoiGian == 'ngay') {
    $nhanThoiGian = 'Ngày ' . date('d/m/Y', strtotime($locNgay));
} else if ($kieuThoiGian == 'thang') {
    $nhanThoiGian = 'Tháng ' . $locThang . '/' . $locNam;
} else if ($kieuThoiGian == 'nam') {
    $nhanThoiGian = 'Năm ' . $locNam;
}

$nhanTrangThai = 'Tất cả trạng thái';
if ($trangThaiLoc >= 0 && isset($trangThaiLabel[$trangThaiLoc])) {
    $nhanTrangThai = $trangThaiLabel[$trangThaiLoc];
}

// Xử lý xem chi tiết 1 đơn hàng
$iddhXem = 0;
$donHangInfo = null;
$chiTietSP = array();
if (isset($_GET['xemdon']) && intval($_GET['xemdon']) > 0) {
    $iddhXem = intval($_GET['xemdon']);
    $donHangInfo = $p->layThongTinDonHang($iddhXem);
    if ($donHangInfo) {
        $chiTietSP = $p->xemChiTietDonHang($iddhXem);
    }
}

// =======================================================
// KHU VỰC 2: XỬ LÝ DỮ LIỆU BIỂU ĐỒ SẢN PHẨM BÁN ĐƯỢC
// (Độc lập với bộ lọc danh sách đơn hàng)
// =======================================================
$kieuBD = (isset($_GET['kieuBD']) && $_GET['kieuBD'] == 'nam') ? 'nam' : 'thang';
$namBD  = isset($_GET['namBD']) ? intval($_GET['namBD']) : $namMacDinh;

if ($kieuBD == 'thang') {
    $dataRawBD = $p->thongKeSoLuongTheoThang($namBD);
    $labelsBD  = array();
    $valuesBD  = array();
    for ($m = 1; $m <= 12; $m++) {
        $labelsBD[] = "Tháng " . $m;
        $valuesBD[] = isset($dataRawBD[$m]) ? $dataRawBD[$m] : 0;
    }
    $tieuDeBD = "Số lượng sản phẩm bán được theo từng tháng trong năm " . $namBD;
} else {
    $dataRawBD = $p->thongKeSoLuongTheoNam();
    $labelsBD  = array();
    $valuesBD  = array();
    if (!empty($dataRawBD)) {
        foreach ($dataRawBD as $nam => $sl) {
            $labelsBD[] = "Năm " . $nam;
            $valuesBD[] = $sl;
        }
    }
    $tieuDeBD = "Số lượng sản phẩm bán được theo từng năm";
}

// Chuỗi tham số hiện tại để giữ lại bộ lọc khi thao tác
$queryLocHienTai = "&kieu_thoigian=" . urlencode($kieuThoiGian) .
                   "&loc_ngay=" . urlencode($locNgay) .
                   "&loc_thang=" . urlencode($locThang) .
                   "&loc_nam=" . urlencode($locNam) .
                   "&loc_trangthai=" . urlencode($trangThaiLoc) .
                   "&kieuBD=" . urlencode($kieuBD) .
                   "&namBD=" . urlencode($namBD);
?>

<!-- NẠP THƯ VIỆN CHART.JS (OFFLINE TỪ LOCAL) -->
<script src="../js/Chart.min.js"></script>

<!-- ======================================================= -->
<!-- TIÊU ĐỀ TRANG VÀ TIÊU ĐỀ KHU VỰC 1 -->
<!-- ======================================================= -->
<h3 align="center">QUẢN LÝ BÁN HÀNG</h3>
<h4 align="center">THỐNG KÊ DOANH THU VÀ DANH SÁCH ĐƠN HÀNG</h4>

<!-- 3.2. BỘ LỌC ĐƠN HÀNG VÀ DOANH THU -->
<div align="center" style="margin-bottom: 15px;">
    <form method="GET" action="admin.php">
        <input type="hidden" name="page" value="dathang">
        <!-- Giữ lại cấu hình biểu đồ độc lập -->
        <input type="hidden" name="kieuBD" value="<?php echo htmlspecialchars($kieuBD); ?>">
        <input type="hidden" name="namBD" value="<?php echo htmlspecialchars($namBD); ?>">

        <table border="0" cellpadding="5" cellspacing="0" style="display: inline-block;">
            <tr>
                <td><b>Thời gian:</b></td>
                <td>
                    <select name="kieu_thoigian" id="kieu_thoigian" onchange="chuyenKieuThoiGian(this.value)">
                        <option value="all" <?php if ($kieuThoiGian == 'all') echo 'selected'; ?>>Tất cả</option>
                        <option value="ngay" <?php if ($kieuThoiGian == 'ngay') echo 'selected'; ?>>Theo ngày</option>
                        <option value="thang" <?php if ($kieuThoiGian == 'thang') echo 'selected'; ?>>Theo tháng</option>
                        <option value="nam" <?php if ($kieuThoiGian == 'nam') echo 'selected'; ?>>Theo năm</option>
                    </select>
                </td>

                <td id="box_ngay" style="display: <?php echo ($kieuThoiGian == 'ngay') ? 'table-cell' : 'none'; ?>;">
                    Ngày: <input type="date" name="loc_ngay" value="<?php echo htmlspecialchars($locNgay); ?>">
                </td>

                <td id="box_thang" style="display: <?php echo ($kieuThoiGian == 'thang') ? 'table-cell' : 'none'; ?>;">
                    Tháng: 
                    <select name="loc_thang">
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?php echo $m; ?>" <?php if ($m == $locThang) echo 'selected'; ?>>Tháng <?php echo $m; ?></option>
                        <?php endfor; ?>
                    </select>
                </td>

                <td id="box_nam" style="display: <?php echo ($kieuThoiGian == 'thang' || $kieuThoiGian == 'nam') ? 'table-cell' : 'none'; ?>;">
                    Năm: 
                    <select name="loc_nam">
                        <?php foreach ($danhSachNam as $y): ?>
                            <option value="<?php echo $y; ?>" <?php if ($y == $locNam) echo 'selected'; ?>><?php echo $y; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>

                <td><b>Trạng thái:</b></td>
                <td>
                    <select name="loc_trangthai">
                        <option value="-1" <?php if ($trangThaiLoc == -1) echo 'selected'; ?>>Tất cả</option>
                        <option value="0" <?php if ($trangThaiLoc === 0) echo 'selected'; ?>>Chờ xử lý</option>
                        <option value="1" <?php if ($trangThaiLoc === 1) echo 'selected'; ?>>Đang giao</option>
                        <option value="2" <?php if ($trangThaiLoc === 2) echo 'selected'; ?>>Đã giao</option>
                    </select>
                </td>

                <td>
                    <input type="submit" value="Lọc">
                    <a href="admin.php?page=dathang">[Đặt lại]</a>
                </td>
            </tr>
        </table>
    </form>
</div>

<script>
function chuyenKieuThoiGian(kieu) {
    var boxNgay = document.getElementById('box_ngay');
    var boxThang = document.getElementById('box_thang');
    var boxNam = document.getElementById('box_nam');

    if (kieu === 'ngay') {
        boxNgay.style.display = 'table-cell';
        boxThang.style.display = 'none';
        boxNam.style.display = 'none';
    } else if (kieu === 'thang') {
        boxNgay.style.display = 'none';
        boxThang.style.display = 'table-cell';
        boxNam.style.display = 'table-cell';
    } else if (kieu === 'nam') {
        boxNgay.style.display = 'none';
        boxThang.style.display = 'none';
        boxNam.style.display = 'table-cell';
    } else {
        boxNgay.style.display = 'none';
        boxThang.style.display = 'none';
        boxNam.style.display = 'none';
    }
}
</script>

<!-- 3.3. BẢNG THỐNG KÊ DOANH THU THEO BỘ LỌC -->
<table border="1" align="center" cellpadding="6" cellspacing="0" width="800">
    <tr align="center">
        <td width="50%">
            <b>Tổng doanh thu (<?php echo htmlspecialchars($nhanTrangThai); ?>)</b><br>
            <small>Thời gian: <?php echo htmlspecialchars($nhanThoiGian); ?></small>
        </td>
        <td width="50%">
            <b>Tổng số đơn hàng (<?php echo htmlspecialchars($nhanTrangThai); ?>)</b><br>
            <small>Thời gian: <?php echo htmlspecialchars($nhanThoiGian); ?></small>
        </td>
    </tr>
    <tr align="center">
        <td><b><?php echo number_format($tongDoanhThu, 0, ',', '.'); ?> USD</b></td>
        <td><b><?php echo $tongSoDon; ?> đơn</b></td>
    </tr>
</table>

<div style="margin: 15px 0;"></div>

<!-- 3.4. BẢNG DANH SÁCH ĐƠN HÀNG -->
<?php if ($tongSoDon == 0): ?>
    <p align="center"><b>Không có đơn hàng phù hợp.</b></p>
<?php else: ?>
    <table border="1" align="center" cellpadding="5" cellspacing="0" width="800">
        <tr>
            <th>Mã đơn hàng</th>
            <th>Khách hàng</th>
            <th>Ngày đặt</th>
            <th>Tổng tiền (USD)</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
        </tr>
        <?php foreach ($danhsachTrang as $dh):
            $tt = intval($dh['trangthai']);
            $label = isset($trangThaiLabel[$tt]) ? $trangThaiLabel[$tt] : 'Không rõ';
        ?>
        <tr>
            <td align="center"><?php echo $dh['iddh']; ?></td>
            <td><?php echo htmlspecialchars($dh['hodem'] . ' ' . $dh['ten']); ?></td>
            <td align="center"><?php echo $dh['ngaydathang']; ?></td>
            <td align="right"><?php echo number_format($dh['tongtien'], 0, ',', '.'); ?> USD</td>
            <td align="center">
                <b><?php echo $label; ?></b><br>
                <!-- Form chuyển đổi trạng thái đơn hàng bằng POST -->
                <form method="POST" action="" style="margin: 4px 0 0 0;">
                    <input type="hidden" name="iddh" value="<?php echo $dh['iddh']; ?>">
                    <input type="hidden" name="kieu_thoigian" value="<?php echo htmlspecialchars($kieuThoiGian); ?>">
                    <input type="hidden" name="loc_ngay" value="<?php echo htmlspecialchars($locNgay); ?>">
                    <input type="hidden" name="loc_thang" value="<?php echo htmlspecialchars($locThang); ?>">
                    <input type="hidden" name="loc_nam" value="<?php echo htmlspecialchars($locNam); ?>">
                    <input type="hidden" name="loc_trangthai" value="<?php echo htmlspecialchars($trangThaiLoc); ?>">
                    <input type="hidden" name="kieuBD" value="<?php echo htmlspecialchars($kieuBD); ?>">
                    <input type="hidden" name="namBD" value="<?php echo htmlspecialchars($namBD); ?>">
                    <input type="hidden" name="trang_dh" value="<?php echo $trangHienTaiDH; ?>">
                    <?php if ($iddhXem > 0): ?>
                        <input type="hidden" name="xemdon" value="<?php echo $iddhXem; ?>">
                    <?php endif; ?>

                    <select name="trangthai_moi">
                        <?php foreach ($trangThaiLabel as $val => $lbl): ?>
                            <option value="<?php echo $val; ?>" <?php if ($tt == $val) echo 'selected'; ?>>
                                <?php echo $lbl; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="submit" name="btnCapNhatTT" value="Cập nhật">
                </form>
            </td>
            <td align="center">
                <a href="admin.php?page=dathang&xemdon=<?php echo $dh['iddh'] . $queryLocHienTai . '&trang_dh=' . $trangHienTaiDH; ?>">Xem chi tiết</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <?php if ($tongSoTrangDH > 1): ?>
    <div align="center" style="margin-top: 10px;">
        <b>Trang: </b>
        <?php for ($t = 1; $t <= $tongSoTrangDH; $t++): ?>
            <?php if ($t == $trangHienTaiDH): ?>
                <b>[<?php echo $t; ?>]</b>
            <?php else: ?>
                <a href="admin.php?page=dathang<?php echo $queryLocHienTai; ?>&trang_dh=<?php echo $t; ?>">[<?php echo $t; ?>]</a>
            <?php endif; ?>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
<?php endif; ?>

<!-- 3.5. CHI TIẾT ĐƠN HÀNG (Hiển thị ngay dưới danh sách khi chọn Xem chi tiết) -->
<?php if ($iddhXem > 0): ?>
    <div style="margin: 25px 0 10px 0;"></div>
    <hr width="800">

    <?php if (!$donHangInfo): ?>
        <p align="center"><b>Đơn hàng <?php echo $iddhXem; ?> không tồn tại trong hệ thống.</b></p>
        <p align="center">
            <a href="admin.php?page=dathang<?php echo $queryLocHienTai . '&trang_dh=' . $trangHienTaiDH; ?>">[Đóng thông báo]</a>
        </p>
    <?php else: ?>
        <h4 align="center">A. THÔNG TIN ĐƠN HÀNG <?php echo $iddhXem; ?></h4>
        <table border="1" align="center" cellpadding="5" cellspacing="0" width="800">
            <tr>
                <td width="20%"><b>Mã đơn hàng:</b></td>
                <td width="30%"><?php echo $donHangInfo['iddh']; ?></td>
                <td width="20%"><b>Ngày đặt:</b></td>
                <td width="30%"><?php echo $donHangInfo['ngaydathang']; ?></td>
            </tr>
            <tr>
                <td><b>Khách hàng:</b></td>
                <td><?php echo htmlspecialchars($donHangInfo['hodem'] . ' ' . $donHangInfo['ten']); ?></td>
                <td><b>Trạng thái:</b></td>
                <td><b><?php echo isset($trangThaiLabel[$donHangInfo['trangthai']]) ? $trangThaiLabel[$donHangInfo['trangthai']] : 'Không rõ'; ?></b></td>
            </tr>
            <tr>
                <td><b>Email:</b></td>
                <td><?php echo htmlspecialchars($donHangInfo['email']); ?></td>
                <td><b>Số điện thoại:</b></td>
                <td><?php echo htmlspecialchars($donHangInfo['dienthoai']); ?></td>
            </tr>
            <tr>
                <td><b>Địa chỉ nhận hàng:</b></td>
                <td><?php echo htmlspecialchars($donHangInfo['diachinhanhang']); ?></td>
                <td><b>Tổng tiền:</b></td>
                <td><b><?php echo number_format($donHangInfo['tongtien'], 0, ',', '.'); ?> USD</b></td>
            </tr>
        </table>

        <div style="margin: 15px 0;"></div>
        <h4 align="center">B. DANH SÁCH SẢN PHẨM TRONG ĐƠN HÀNG</h4>
        <?php if (empty($chiTietSP)): ?>
            <p align="center">Đơn hàng không có sản phẩm chi tiết.</p>
        <?php else: ?>
            <table border="1" align="center" cellpadding="5" cellspacing="0" width="800">
                <tr>
                    <th width="40">STT</th>
                    <th>Tên sản phẩm</th>
                    <th width="80">Số lượng</th>
                    <th width="120">Đơn giá (USD)</th>
                    <th width="120">Thành tiền (USD)</th>
                </tr>
                <?php
                $sttSP = 1;
                $tongDonSP = 0;
                foreach ($chiTietSP as $sp):
                    $tt_sp = $sp['dongia'] * $sp['soluong'];
                    $tongDonSP += $tt_sp;
                ?>
                <tr>
                    <td align="center"><?php echo $sttSP++; ?></td>
                    <td><?php echo htmlspecialchars($sp['tensp']); ?></td>
                    <td align="center"><?php echo $sp['soluong']; ?></td>
                    <td align="right"><?php echo number_format($sp['dongia'], 0, ',', '.'); ?> USD</td>
                    <td align="right"><?php echo number_format($tt_sp, 0, ',', '.'); ?> USD</td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="4" align="right"><b>Tổng tiền:</b></td>
                    <td align="right"><b><?php echo number_format($tongDonSP, 0, ',', '.'); ?> USD</b></td>
                </tr>
            </table>
        <?php endif; ?>

        <div align="center" style="margin: 10px;">
            <a href="admin.php?page=dathang<?php echo $queryLocHienTai . '&trang_dh=' . $trangHienTaiDH; ?>">[Đóng chi tiết]</a>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- ======================================================= -->
<!-- KHU VỰC 2: BIỂU ĐỒ SẢN PHẨM BÁN ĐƯỢC -->
<!-- ======================================================= -->
<div style="margin: 30px 0 20px 0;">
    <hr width="800">
</div>

<h3 align="center">BIỂU ĐỒ SẢN PHẨM BÁN ĐƯỢC</h3>

<div align="center" style="margin-bottom: 15px;">
    <form method="GET" action="admin.php" style="margin: 0;">
        <input type="hidden" name="page" value="dathang">
        <!-- Giữ nguyên cấu hình bộ lọc danh sách khi người dùng xem biểu đồ -->
        <input type="hidden" name="kieu_thoigian" value="<?php echo htmlspecialchars($kieuThoiGian); ?>">
        <input type="hidden" name="loc_ngay" value="<?php echo htmlspecialchars($locNgay); ?>">
        <input type="hidden" name="loc_thang" value="<?php echo htmlspecialchars($locThang); ?>">
        <input type="hidden" name="loc_nam" value="<?php echo htmlspecialchars($locNam); ?>">
        <input type="hidden" name="loc_trangthai" value="<?php echo htmlspecialchars($trangThaiLoc); ?>">
        <input type="hidden" name="trang_dh" value="<?php echo $trangHienTaiDH; ?>">
        <?php if ($iddhXem > 0): ?>
            <input type="hidden" name="xemdon" value="<?php echo $iddhXem; ?>">
        <?php endif; ?>

        <b>Chế độ xem:</b>
        <label>
            <input type="radio" name="kieuBD" value="thang" <?php if ($kieuBD == 'thang') echo 'checked'; ?> onclick="document.getElementById('box_namBD').style.display='inline';">
            Theo tháng trong năm
        </label>
        &nbsp;&nbsp;
        <label>
            <input type="radio" name="kieuBD" value="nam" <?php if ($kieuBD == 'nam') echo 'checked'; ?> onclick="document.getElementById('box_namBD').style.display='none';">
            Theo từng năm
        </label>
        &nbsp;&nbsp;

        <span id="box_namBD" style="display: <?php echo ($kieuBD == 'thang') ? 'inline' : 'none'; ?>;">
            <b>Chọn năm:</b>
            <select name="namBD">
                <?php foreach ($danhSachNam as $y): ?>
                    <option value="<?php echo $y; ?>" <?php if ($y == $namBD) echo 'selected'; ?>><?php echo $y; ?></option>
                <?php endforeach; ?>
            </select>
        </span>
        &nbsp;
        <input type="submit" value="Xem biểu đồ">
    </form>
</div>

<table border="1" align="center" cellpadding="5" cellspacing="0" width="800">
    <tr>
        <td align="center">
            <b><?php echo $tieuDeBD; ?></b><br>
            <div style="width: 760px; max-width: 100%; margin: 10px auto;">
                <canvas id="bieuDoSanPham" width="760" height="300"></canvas>
            </div>
        </td>
    </tr>
</table>

<script>
var ctx = document.getElementById('bieuDoSanPham').getContext('2d');
var chartData = {
    labels: <?php echo json_encode($labelsBD); ?>,
    datasets: [{
        label: 'Số lượng sản phẩm bán được (chiếc)',
        data: <?php echo json_encode($valuesBD); ?>,
        backgroundColor: 'rgba(54, 162, 235, 0.6)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1
    }]
};

var myChart = new Chart(ctx, {
    type: 'bar',
    data: chartData,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true,
                    stepSize: 1,
                    precision: 0
                },
                scaleLabel: {
                    display: true,
                    labelString: 'Số lượng (chiếc)'
                }
            }],
            xAxes: [{
                scaleLabel: {
                    display: true,
                    labelString: '<?php echo ($kieuBD == "thang") ? "Các tháng trong năm " . $namBD : "Các năm"; ?>'
                }
            }]
        },
        legend: {
            display: true,
            position: 'top'
        }
    }
});
</script>
