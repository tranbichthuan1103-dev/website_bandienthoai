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

}
?>