<?php
class csdltmdt
{
	private function connect()
	{
		$con = mysql_connect("localhost", "usertmdt", "passtmdt");
		if (!$con) {
			echo 'Không kết nối được csdl';
			exit();
		} else {
			mysql_select_db('tmdt_db');
			mysql_query("SET NAMES utf8");
			mysql_query("SET CHARACTER SET utf8");
			return $con;
		}
	}


	public function xemdscongty($sql)
	{
		$link = $this->connect();
		$ketqua = mysql_query($sql, $link);
		$i = mysql_num_rows($ketqua);
		if ($i > 0) {
			while ($row = mysql_fetch_array($ketqua)) {
				$idcty = $row['idcty'];
				$ten = $row['tencty'];
				echo '<a href="?id=' . $idcty . '">' . $ten . '</a>';
				echo '<br>';
			}
		} else {
			echo 'Không co dữ liệu.';
		}
	}

	public function xemdssanpham($sql)
	{
		$link = $this->connect();
		$ketqua = mysql_query($sql, $link);
		$i = mysql_num_rows($ketqua);
		if ($i > 0) {
			while ($row = mysql_fetch_array($ketqua)) {
				$idsp = $row['idsp'];
				$tensp = $row['tensp'];
				$gia = $row['gia'];
				$hinh = $row['hinh'];
				echo '<a href="sanpham/chitietsanpham.php?id=' . $idsp . '" style="text-decoration:none; color:inherit;">
						<div class="sanpham">
							<div class="sanpham_hinh"><img src ="img/' . $hinh . '"/></div>
							<div class="sanpham_ten">' . $tensp . '</div>
							<div class="sanpham_gia">' . $gia . ' USD</div>
						</div>
					</a>';
			}
		} else {
			echo 'Không co dữ liệu.';
		}
	}
	public function uploadfile($name, $tmp_name, $folder)
	{
		$newname = $folder . "/" . $name;
		if (move_uploaded_file($tmp_name, $newname)) {
			return 1;
		} else {
			return 0;
		}
	}
	public function themxoasua($sql)
	{
		$link = $this->connect();
		if (mysql_query($sql, $link)) {
			return 1;
		} else {
			return 0;
		}
	}

	public function combobox_congty($sql)
	{
		$link = $this->connect();
		$ketqua = mysql_query($sql, $link);
		$i = mysql_num_rows($ketqua);
		if ($i > 0) {
			echo '<select name="congty" id="congty">';
			echo '<option value="0">Mời bạn chọn công ty</option>';
			while ($row = mysql_fetch_array($ketqua)) {
				$idcty = $row['idcty'];
				$tencty = $row['tencty'];
				echo '<option value="' . $idcty . '">' . $tencty . '</option>';
			}
			echo '</select>';
		}
		mysql_close($link);
	}
	public function chitietsanpham($idsp)
	{
		$link = $this->connect();
		$idsp = intval($idsp);
		$ketqua = mysql_query("SELECT * FROM sanpham WHERE idsp='$idsp' limit 1", $link);
		if ($ketqua && mysql_num_rows($ketqua) > 0) {
			return mysql_fetch_array($ketqua);
		} else {
			return 0;
		}
	}

	// Lấy hoặc tạo giỏ hàng đang hoạt động (trangthai=0) cho khách hàng
	public function layGiohangHoatDong($idkh)
	{
		$link = $this->connect();
		$idkh = intval($idkh);
		$ketqua = mysql_query("SELECT idgiohang FROM giohang WHERE idkh='$idkh' AND trangthai=0 LIMIT 1", $link);
		if ($ketqua && mysql_num_rows($ketqua) > 0) {
			$row = mysql_fetch_array($ketqua);
			return $row['idgiohang'];
		} else {
			mysql_query("INSERT INTO giohang (idkh, trangthai) VALUES ('$idkh', 0)", $link);
			return mysql_insert_id($link);
		}
	}

	// Thêm sản phẩm vào giỏ hàng, nếu đã có thì cộng số lượng
	public function themSanPhamVaoGiohang($idkh, $idsp, $soluong, $gia)
	{
		$link = $this->connect();
		$idgiohang = $this->layGiohangHoatDong($idkh);
		$idsp    = intval($idsp);
		$soluong = intval($soluong);
		$gia     = floatval($gia);
		$ketqua = mysql_query("SELECT idchitiet, soluong FROM chitietgiohang WHERE idgiohang='$idgiohang' AND idsp='$idsp' LIMIT 1", $link);
		if ($ketqua && mysql_num_rows($ketqua) > 0) {
			$row = mysql_fetch_array($ketqua);
			$soluongmoi = $row['soluong'] + $soluong;
			$idchitiet  = $row['idchitiet'];
			mysql_query("UPDATE chitietgiohang SET soluong='$soluongmoi' WHERE idchitiet='$idchitiet'", $link);
		} else {
			mysql_query("INSERT INTO chitietgiohang (idgiohang, idsp, soluong, gia) VALUES ('$idgiohang', '$idsp', '$soluong', '$gia')", $link);
		}
		return 1;
	}

	// Lấy danh sách sản phẩm trong giỏ hàng của khách hàng
	public function xemChiTietGiohang($idkh)
	{
		$link = $this->connect();
		$idkh = intval($idkh);
		$sql = "SELECT ctg.idchitiet, ctg.idsp, ctg.soluong, ctg.gia, sp.tensp, sp.hinh
				FROM chitietgiohang ctg
				INNER JOIN giohang gh ON ctg.idgiohang = gh.idgiohang
				INNER JOIN sanpham sp ON ctg.idsp = sp.idsp
				WHERE gh.idkh='$idkh' AND gh.trangthai=0";
		$ketqua = mysql_query($sql, $link);
		$result = array();
		if ($ketqua && mysql_num_rows($ketqua) > 0) {
			while ($row = mysql_fetch_array($ketqua)) {
				$result[] = $row;
			}
		}
		return $result;
	}

	// Xóa 1 sản phẩm khỏi giỏ hàng
	public function xoaSanPhamKhoiGiohang($idchitiet)
	{
		$link = $this->connect();
		$idchitiet = intval($idchitiet);
		if (mysql_query("DELETE FROM chitietgiohang WHERE idchitiet='$idchitiet'", $link)) {
			return 1;
		}
		return 0;
	}

	// Cập nhật số lượng sản phẩm trong giỏ hàng
	public function capNhatSoluongGiohang($idchitiet, $soluong)
	{
		$link = $this->connect();
		$idchitiet = intval($idchitiet);
		$soluong   = intval($soluong);
		if ($soluong <= 0) {
			return $this->xoaSanPhamKhoiGiohang($idchitiet);
		}
		if (mysql_query("UPDATE chitietgiohang SET soluong='$soluong' WHERE idchitiet='$idchitiet'", $link)) {
			return 1;
		}
		return 0;
	}

	// Đếm số dòng sản phẩm trong giỏ hàng (để hiện số lượng trên menu)
	public function demSanPhamTrongGiohang($idkh)
	{
		$link = $this->connect();
		$idkh = intval($idkh);
		$sql = "SELECT COUNT(*) as total FROM chitietgiohang ctg
				INNER JOIN giohang gh ON ctg.idgiohang = gh.idgiohang
				WHERE gh.idkh='$idkh' AND gh.trangthai=0";
		$ketqua = mysql_query($sql, $link);
		if ($ketqua && mysql_num_rows($ketqua) > 0) {
			$row = mysql_fetch_array($ketqua);
			return $row['total'];
		}
		return 0;
	}

	// Lấy thông tin khách hàng theo idkh
	public function layThongTinKhachHang($idkh)
	{
		$link = $this->connect();
		$idkh = intval($idkh);
		$ketqua = mysql_query("SELECT * FROM khachhang WHERE idkh='$idkh' LIMIT 1", $link);
		if ($ketqua && mysql_num_rows($ketqua) > 0) {
			return mysql_fetch_array($ketqua);
		}
		return 0;
	}

	// Xác nhận đặt hàng: tạo đơn hàng từ giỏ hàng đang active
	public function xacNhanDatHang($idkh, $diachinhanhang)
	{
		$link = $this->connect();
		$idkh = intval($idkh);
		$diachinhanhang = mysql_real_escape_string($diachinhanhang, $link);

		// Lấy giỏ hàng đang active
		$ketqua = mysql_query("SELECT idgiohang FROM giohang WHERE idkh='$idkh' AND trangthai=0 LIMIT 1", $link);
		if (!$ketqua || mysql_num_rows($ketqua) == 0) return 0;
		$row = mysql_fetch_array($ketqua);
		$idgiohang = $row['idgiohang'];

		// Kiểm tra giỏ hàng có sản phẩm không
		$ctg = mysql_query("SELECT * FROM chitietgiohang WHERE idgiohang='$idgiohang'", $link);
		if (!$ctg || mysql_num_rows($ctg) == 0) return 0;

		// Cập nhật địa chỉ nhận hàng
		mysql_query("UPDATE khachhang SET diachinhanhang='$diachinhanhang' WHERE idkh='$idkh'", $link);

		// Tạo đơn hàng mới
		mysql_query("INSERT INTO dathang (idkh, ngaydathang, trangthai) VALUES ('$idkh', NOW(), 0)", $link);
		$iddh = mysql_insert_id($link);

		// Chép chi tiết giỏ hàng vào dathang_chitiet
		while ($item = mysql_fetch_array($ctg)) {
			$idsp    = intval($item['idsp']);
			$soluong = intval($item['soluong']);
			$dongia  = floatval($item['gia']);
			mysql_query("INSERT INTO dathang_chitiet (iddh, idsp, soluong, dongia, giamgia)
						 VALUES ('$iddh', '$idsp', '$soluong', '$dongia', 0)", $link);
		}

		// Đánh dấu giỏ hàng đã đặt
		mysql_query("UPDATE giohang SET trangthai=1 WHERE idgiohang='$idgiohang'", $link);

		return $iddh;
	}

	// Lịch sử mua hàng của khách hàng (tổng tiền theo USD)
	public function xemLichSuMuaHang($idkh)
	{
		$link = $this->connect();
		$idkh = intval($idkh);
		$sql = "SELECT dh.iddh, dh.ngaydathang, dh.trangthai,
					   SUM(dc.soluong * dc.dongia) as tongtien
				FROM dathang dh
				INNER JOIN dathang_chitiet dc ON dh.iddh = dc.iddh
				WHERE dh.idkh='$idkh'
				GROUP BY dh.iddh
				ORDER BY dh.iddh ASC";
		$ketqua = mysql_query($sql, $link);
		$result = array();
		if ($ketqua && mysql_num_rows($ketqua) > 0) {
			while ($row = mysql_fetch_array($ketqua)) {
				$result[] = $row;
			}
		}
		return $result;
	}

	// Chi tiết sản phẩm trong 1 đơn hàng
	public function xemChiTietDonHang($iddh)
	{
		$link = $this->connect();
		$iddh = intval($iddh);
		$sql = "SELECT dc.idsp, dc.soluong, dc.dongia, sp.tensp, sp.hinh
				FROM dathang_chitiet dc
				INNER JOIN sanpham sp ON dc.idsp = sp.idsp
				WHERE dc.iddh='$iddh'";
		$ketqua = mysql_query($sql, $link);
		$result = array();
		if ($ketqua && mysql_num_rows($ketqua) > 0) {
			while ($row = mysql_fetch_array($ketqua)) {
				$result[] = $row;
			}
		}
		return $result;
	}

	// Admin: danh sách tất cả đơn hàng
	public function xemDSDatHangAdmin($trangthai = -1)
	{
		$link = $this->connect();
		$where = "";
		$trangthai = intval($trangthai);
		if ($trangthai >= 0) {
			$where = " WHERE dh.trangthai = '$trangthai' ";
		}
		$sql = "SELECT dh.iddh, dh.ngaydathang, dh.trangthai,
					   kh.hodem, kh.ten, kh.email, kh.dienthoai, kh.diachinhanhang,
					   SUM(dc.soluong * dc.dongia) as tongtien
				FROM dathang dh
				INNER JOIN khachhang kh ON dh.idkh = kh.idkh
				INNER JOIN dathang_chitiet dc ON dh.iddh = dc.iddh
				$where
				GROUP BY dh.iddh
				ORDER BY dh.iddh ASC";
		$ketqua = mysql_query($sql, $link);
		$result = array();
		if ($ketqua && mysql_num_rows($ketqua) > 0) {
			while ($row = mysql_fetch_array($ketqua)) {
				$result[] = $row;
			}
		}
		return $result;
	}

	// Thông tin 1 đơn hàng (thông tin chung, khách hàng, tổng tiền)
	public function layThongTinDonHang($iddh)
	{
		$link = $this->connect();
		$iddh = intval($iddh);
		$sql = "SELECT dh.iddh, dh.ngaydathang, dh.trangthai,
					   kh.hodem, kh.ten, kh.email, kh.dienthoai, kh.diachinhanhang,
					   SUM(dc.soluong * dc.dongia) as tongtien
				FROM dathang dh
				INNER JOIN khachhang kh ON dh.idkh = kh.idkh
				INNER JOIN dathang_chitiet dc ON dh.iddh = dc.iddh
				WHERE dh.iddh = '$iddh'
				GROUP BY dh.iddh LIMIT 1";
		$res = mysql_query($sql, $link);
		if ($res && mysql_num_rows($res) > 0) {
			return mysql_fetch_assoc($res);
		}
		return null;
	}

	// Lọc danh sách đơn hàng cho admin kết hợp thời gian và trạng thái
	public function xemDSDatHangAdminFilter($loai_time = 'all', $ngay = '', $thang = 0, $nam = 0, $trangthai = -1)
	{
		$link = $this->connect();
		$whereClauses = array();

		$trangthai = intval($trangthai);
		if ($trangthai >= 0) {
			$whereClauses[] = "dh.trangthai = '$trangthai'";
		}

		if ($loai_time == 'ngay' && !empty($ngay)) {
			$ngay_esc = mysql_real_escape_string($ngay, $link);
			$whereClauses[] = "DATE(dh.ngaydathang) = '$ngay_esc'";
		} else if ($loai_time == 'thang' && intval($thang) > 0 && intval($nam) > 0) {
			$thang_esc = intval($thang);
			$nam_esc = intval($nam);
			$whereClauses[] = "MONTH(dh.ngaydathang) = '$thang_esc' AND YEAR(dh.ngaydathang) = '$nam_esc'";
		} else if ($loai_time == 'nam' && intval($nam) > 0) {
			$nam_esc = intval($nam);
			$whereClauses[] = "YEAR(dh.ngaydathang) = '$nam_esc'";
		}

		$where = "";
		if (count($whereClauses) > 0) {
			$where = " WHERE " . implode(" AND ", $whereClauses) . " ";
		}

		$sql = "SELECT dh.iddh, dh.ngaydathang, dh.trangthai,
					   kh.hodem, kh.ten, kh.email, kh.dienthoai, kh.diachinhanhang,
					   SUM(dc.soluong * dc.dongia) as tongtien
				FROM dathang dh
				INNER JOIN khachhang kh ON dh.idkh = kh.idkh
				INNER JOIN dathang_chitiet dc ON dh.iddh = dc.iddh
				$where
				GROUP BY dh.iddh
				ORDER BY dh.iddh ASC";
		$ketqua = mysql_query($sql, $link);
		$result = array();
		if ($ketqua && mysql_num_rows($ketqua) > 0) {
			while ($row = mysql_fetch_assoc($ketqua)) {
				$result[] = $row;
			}
		}
		return $result;
	}

	// Admin: cập nhật trạng thái đơn hàng
	public function capNhatTrangThaiDonHang($iddh, $trangthai)
	{
		$link = $this->connect();
		$iddh      = intval($iddh);
		$trangthai = intval($trangthai);
		if (mysql_query("UPDATE dathang SET trangthai='$trangthai' WHERE iddh='$iddh'", $link)) {
			return 1;
		}
		return 0;
	}


	// Lấy danh sách các năm có đơn hàng trong CSDL
	public function layDanhSachNamDonHang()
	{
		$link = $this->connect();
		$sql = "SELECT DISTINCT YEAR(ngaydathang) AS nam FROM dathang ORDER BY nam DESC";
		$res = mysql_query($sql, $link);
		$years = array();
		if ($res && mysql_num_rows($res) > 0) {
			while ($row = mysql_fetch_assoc($res)) {
				$years[] = intval($row['nam']);
			}
		}
		if (empty($years)) {
			$years[] = intval(date('Y'));
		}
		return $years;
	}

	// Thống kê doanh thu theo ngày
	public function thongKeDoanhThuNgay($ngay)
	{
		$link = $this->connect();
		$ngay = mysql_real_escape_string($ngay, $link);
		$sql = "SELECT COUNT(DISTINCT dh.iddh) AS sodon, 
					   SUM(dc.soluong * dc.dongia) AS tongtien
				FROM dathang dh
				INNER JOIN dathang_chitiet dc ON dh.iddh = dc.iddh
				WHERE DATE(dh.ngaydathang) = '$ngay'";
		$res = mysql_query($sql, $link);
		$row = mysql_fetch_assoc($res);
		return array(
			'sodon' => ($row && $row['sodon']) ? intval($row['sodon']) : 0,
			'tongtien' => ($row && $row['tongtien']) ? floatval($row['tongtien']) : 0
		);
	}

	// Thống kê doanh thu theo tháng
	public function thongKeDoanhThuThang($thang, $nam)
	{
		$link = $this->connect();
		$thang = intval($thang);
		$nam = intval($nam);
		$sql = "SELECT COUNT(DISTINCT dh.iddh) AS sodon, 
					   SUM(dc.soluong * dc.dongia) AS tongtien
				FROM dathang dh
				INNER JOIN dathang_chitiet dc ON dh.iddh = dc.iddh
				WHERE MONTH(dh.ngaydathang) = '$thang' AND YEAR(dh.ngaydathang) = '$nam'";
		$res = mysql_query($sql, $link);
		$row = mysql_fetch_assoc($res);
		return array(
			'sodon' => ($row && $row['sodon']) ? intval($row['sodon']) : 0,
			'tongtien' => ($row && $row['tongtien']) ? floatval($row['tongtien']) : 0
		);
	}

	// Thống kê doanh thu theo năm
	public function thongKeDoanhThuNam($nam)
	{
		$link = $this->connect();
		$nam = intval($nam);
		$sql = "SELECT COUNT(DISTINCT dh.iddh) AS sodon, 
					   SUM(dc.soluong * dc.dongia) AS tongtien
				FROM dathang dh
				INNER JOIN dathang_chitiet dc ON dh.iddh = dc.iddh
				WHERE YEAR(dh.ngaydathang) = '$nam'";
		$res = mysql_query($sql, $link);
		$row = mysql_fetch_assoc($res);
		return array(
			'sodon' => ($row && $row['sodon']) ? intval($row['sodon']) : 0,
			'tongtien' => ($row && $row['tongtien']) ? floatval($row['tongtien']) : 0
		);
	}

	// Thống kê tổng doanh thu toàn bộ hệ thống
	public function thongKeTongDoanhThuToanBo()
	{
		$link = $this->connect();
		$sql = "SELECT COUNT(DISTINCT dh.iddh) AS sodon, 
					   SUM(dc.soluong * dc.dongia) AS tongtien
				FROM dathang dh
				INNER JOIN dathang_chitiet dc ON dh.iddh = dc.iddh";
		$res = mysql_query($sql, $link);
		$row = mysql_fetch_assoc($res);
		return array(
			'sodon' => ($row && $row['sodon']) ? intval($row['sodon']) : 0,
			'tongtien' => ($row && $row['tongtien']) ? floatval($row['tongtien']) : 0
		);
	}

	// Thống kê số lượng sản phẩm bán được theo từng tháng trong năm
	public function thongKeSoLuongTheoThang($nam)
	{
		$link = $this->connect();
		$nam = intval($nam);
		$data = array();
		for ($m = 1; $m <= 12; $m++) {
			$data[$m] = 0;
		}
		$sql = "SELECT MONTH(dh.ngaydathang) AS thang, SUM(dc.soluong) AS tong_soluong
				FROM dathang dh
				INNER JOIN dathang_chitiet dc ON dh.iddh = dc.iddh
				WHERE YEAR(dh.ngaydathang) = '$nam'
				GROUP BY MONTH(dh.ngaydathang)";
		$res = mysql_query($sql, $link);
		if ($res && mysql_num_rows($res) > 0) {
			while ($row = mysql_fetch_assoc($res)) {
				$thang = intval($row['thang']);
				$data[$thang] = intval($row['tong_soluong']);
			}
		}
		return $data;
	}

	// Thống kê số lượng sản phẩm bán được theo từng năm có trong CSDL
	public function thongKeSoLuongTheoNam()
	{
		$link = $this->connect();
		$sql = "SELECT YEAR(dh.ngaydathang) AS nam, SUM(dc.soluong) AS tong_soluong
				FROM dathang dh
				INNER JOIN dathang_chitiet dc ON dh.iddh = dc.iddh
				GROUP BY YEAR(dh.ngaydathang)
				ORDER BY nam ASC";
		$res = mysql_query($sql, $link);
		$data = array();
		if ($res && mysql_num_rows($res) > 0) {
			while ($row = mysql_fetch_assoc($res)) {
				$nam = intval($row['nam']);
				$data[$nam] = intval($row['tong_soluong']);
			}
		}
		return $data;
	}
}
?>