<?php
class login
{
	private function connect()
	{
		$con = mysql_connect("localhost", "usertmdt", "passtmdt");
		if (!$con) {
			echo 'Không kết nối được csdl';
			exit();
		} else {
			mysql_select_db('tmdt_db');
			mysql_query("SET NAMES UTF8");
			return $con;
		}
	}

	public function mylogin($user, $pass)
	{
		$link = $this->connect();
		$user = mysql_real_escape_string($user, $link);
		$pass = md5($pass);
		$sql = "SELECT iduser, usename, password, hodem, ten, phanquyen FROM taikhoan WHERE usename='$user' AND password='$pass' LIMIT 1";
		$ketqua = mysql_query($sql, $link);
		$i = mysql_num_rows($ketqua);
		if ($i == 1) {
			$row = mysql_fetch_array($ketqua);
			$id = $row['iduser'];
			$user = $row['usename'];
			$pass = $row['password'];
			$phanquyen = intval($row['phanquyen']);
			$hodem = $row['hodem'];
			$ten = $row['ten'];

			if (session_id() == '') {
				session_start();
			}

			// Cập nhật lần đăng nhập cuối
			mysql_query("UPDATE taikhoan SET `landang-nhapcuoi`=NOW() WHERE iduser='$id'", $link);

			if ($phanquyen == 1) {
				// 1: Admin
				$_SESSION['id'] = $id;
				$_SESSION['user'] = $user;
				$_SESSION['pass'] = $pass;
				$_SESSION['phanquyen'] = 1;
				$_SESSION['hodem'] = $hodem;
				$_SESSION['ten'] = $ten;

				header('location:../admin/admin.php');
				exit();
			} else if ($phanquyen == 0) {
				// 0: Khách hàng
				// Tìm thông tin tương ứng trong bảng khachhang
				$sql_kh = "SELECT idkh, email, password, hodem, ten, diachi, diachinhanhang, dienthoai 
				           FROM khachhang 
				           WHERE email='$user' OR email='{$user}@gmail.com' OR (hodem='$hodem' AND ten='$ten') LIMIT 1";
				$ketqua_kh = mysql_query($sql_kh, $link);
				if (mysql_num_rows($ketqua_kh) == 1) {
					$row_kh = mysql_fetch_array($ketqua_kh);
					$_SESSION['idkh'] = $row_kh['idkh'];
					$_SESSION['email'] = $row_kh['email'];
					$_SESSION['pass'] = $row_kh['password'];
					$_SESSION['hodem'] = $row_kh['hodem'];
					$_SESSION['ten'] = $row_kh['ten'];
					$_SESSION['diachi'] = $row_kh['diachi'];
					$_SESSION['diachinhanhang'] = $row_kh['diachinhanhang'];
					$_SESSION['dienthoai'] = $row_kh['dienthoai'];
				} else {
					// Nếu chưa có trong bảng khachhang, thêm vào để đảm bảo quan hệ khóa ngoại (dathang)
					$sql_ins = "INSERT INTO khachhang (email, password, hodem, ten) 
					            VALUES ('$user', '$pass', '$hodem', '$ten')";
					mysql_query($sql_ins, $link);
					$new_idkh = mysql_insert_id($link);

					$_SESSION['idkh'] = ($new_idkh > 0) ? $new_idkh : $id;
					$_SESSION['email'] = $user;
					$_SESSION['pass'] = $pass;
					$_SESSION['hodem'] = $hodem;
					$_SESSION['ten'] = $ten;
					$_SESSION['diachi'] = '';
					$_SESSION['diachinhanhang'] = '';
					$_SESSION['dienthoai'] = '';
				}

				$_SESSION['id'] = $id;
				$_SESSION['user'] = $user;
				$_SESSION['phanquyen'] = 0;

				header('location:../index.php');
				exit();
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}

	public function confirmlogin($id, $user, $pass, $phanquyen)
	{
		$link = $this->connect();
		$id = mysql_real_escape_string($id, $link);
		$user = mysql_real_escape_string($user, $link);
		$pass = mysql_real_escape_string($pass, $link);
		$phanquyen = intval($phanquyen);

		// Chỉ cho phép admin (phanquyen = 1) truy cập trang admin
		$sql = "SELECT iduser FROM taikhoan WHERE iduser='$id' AND usename='$user' AND password='$pass' AND phanquyen=1 LIMIT 1";
		$ketqua = mysql_query($sql, $link);
		$i = mysql_num_rows($ketqua);
		if ($i != 1) {
			header('location:../login/login.php');
			exit();
		}
	}

	// Hàm confirm login cho khách hàng
	public function confirmloginkhachhang($idkh, $email, $pass)
	{
		$link = $this->connect();
		$idkh = mysql_real_escape_string($idkh, $link);
		$email = mysql_real_escape_string($email, $link);
		$pass = mysql_real_escape_string($pass, $link);

		$sql = "SELECT idkh FROM khachhang 
                WHERE idkh='$idkh' AND email='$email' AND password='$pass' LIMIT 1";
		$ketqua = mysql_query($sql, $link);
		$i = mysql_num_rows($ketqua);
		if ($i != 1) {
			$sql_tk = "SELECT iduser FROM taikhoan WHERE usename='$email' AND password='$pass' AND phanquyen=0 LIMIT 1";
			$ketqua_tk = mysql_query($sql_tk, $link);
			if (mysql_num_rows($ketqua_tk) != 1) {
				$redirect = file_exists('login/login.php') ? 'login/login.php' : '../login/login.php';
				header('location:' . $redirect);
				exit();
			}
		}
	}

	// Hàm login cho khách hàng tương thích ngược
	public function loginkhachhang($email, $pass)
	{
		return $this->mylogin($email, $pass);
	}
}
?>