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
		$pass = md5($pass);
		$sql = " select iduser, usename,password, phanquyen from taikhoan where usename='$user' and password='$pass' limit 1";
		$ketqua = mysql_query($sql, $link);
		$i = mysql_num_rows($ketqua);
		if ($i == 1) {
			while ($row = mysql_fetch_array($ketqua)) {
				$id = $row['iduser'];
				$user = $row['usename'];
				$pass = $row['password'];
				$phanquyen = $row['phanquyen'];
				session_start();
				$_SESSION['id'] = $id;
				$_SESSION['user'] = $user;
				$_SESSION['pass'] = $pass;
				$_SESSION['phanquyen'] = $phanquyen;
				header('location:../admin/admin.php');
			}
		} else {
			return 0;
		}
	}
	public function confirmlogin($id, $user, $pass, $phanquyen)
	{
		$link = $this->connect();
		$sql = " select iduser from taikhoan where $id='$id' and usename='$user' and password='$pass' and phanquyen= '$phanquyen' limit 1";
		$ketqua = mysql_query($sql, $link);
		$i = mysql_num_rows($ketqua);
		if ($i != 1) {
			header('location:../login');
		}

	}

	// Hàm login cho khách hàng
	public function loginkhachhang($email, $pass)
	{
		$link = $this->connect();
		$pass = md5($pass);
		$email = mysql_real_escape_string($email, $link);
		$sql = "SELECT idkh, email, password, hodem, ten, diachi, diachinhanhang, dienthoai 
                FROM khachhang 
                WHERE email='$email' and password='$pass' limit 1";
		$ketqua = mysql_query($sql, $link);
		$i = mysql_num_rows($ketqua);

		if ($i == 1) {
			while ($row = mysql_fetch_array($ketqua)) {
				$idkh = $row['idkh'];
				$email = $row['email'];
				$pass = $row['password'];
				$hodem = $row['hodem'];
				$ten = $row['ten'];
				$diachi = $row['diachi'];
				$diachinhanhang = $row['diachinhanhang'];
				$dienthoai = $row['dienthoai'];

				session_start();
				$_SESSION['idkh'] = $idkh;
				$_SESSION['email'] = $email;
				$_SESSION['pass'] = $pass;
				$_SESSION['hodem'] = $hodem;
				$_SESSION['ten'] = $ten;
				$_SESSION['diachi'] = $diachi;
				$_SESSION['diachinhanhang'] = $diachinhanhang;
				$_SESSION['dienthoai'] = $dienthoai;

				header('location:../index.php');
			}
		} else {
			return 0;
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
                WHERE idkh='$idkh' and email='$email' and password='$pass' limit 1";
		$ketqua = mysql_query($sql, $link);
		$i = mysql_num_rows($ketqua);
		if ($i != 1) {
			header('location:../login/login.php');
		}
	}
}
?>