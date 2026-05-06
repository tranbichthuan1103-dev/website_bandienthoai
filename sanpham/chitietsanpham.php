<?php
include("../class/classtmdt.php");
include("../class/clskhachhang.php");

$q = new csdltmdt();
$p = new khachhang();
?>
<!doctype html>
<html>

<head>
	<<meta charset="utf-8">
    <title>Chi Tiết Sản Phẩm</title>
    <link rel="stylesheet" type="text/css" href="../css/css.css">
</head>

<body>
	<div id="container">
		<div id="banner">BANNER</div>
		<div id="main">
			<div id="left">

				<ul style="list-style:none; padding:0; margin:0;">
					<li><a href="../index.php">Trang Chủ</a></li>
					<li>
						<p>Danh sách công ty</p>

						<?php
						$q->xemdscongty("select * from congty order by tencty asc");
						?>
					</li>
				</ul>

			</div>

			<div id="right">
				<?php
				$idsp = $_REQUEST['id'];
				$chitietsanpham = $p->chitietsanpham($idsp);
				if ($chitietsanpham != 0) {
					echo '<table width="500" border="1" align="center" cellpadding="5" cellspacing="0">
    
					<tr>
						<td colspan="3" align="center" valign="middle">
							<strong>XEM CHI TIẾT SẢN PHẨM</strong>
						</td>
					</tr>

					<tr>
						<td width="215" rowspan="5" align="center" valign="middle">
							<img src="../img/' . $chitietsanpham['hinh'] . '" width="200" height="198" />
						</td>
						<td width="95">Tên sản phẩm</td>
						<td width="152">' . $chitietsanpham['tensp'] . '</td>
					</tr>

					<tr>
						<td>Mô tả</td>
						<td>' . $chitietsanpham['mota'] . '</td>
					</tr>

					<tr>
						<td>Giá</td>
						<td>' . $chitietsanpham['gia'] . '</td>
					</tr>

					<tr>
						<td>Số lượng</td>
						<td>
							<input name="txtsoluong" id="txtsoluong" type="text" value="1" size="2" />
						</td>
					</tr>

					<tr>
						<td colspan="2" align="center" valign="middle">
							<input type="submit" name="sbDathang" id="sbDathang" value="Thêm Vào Giỏ Hàng" />
						</td>
					</tr>
					<tr>
						<td colspan="3" align="center" valign="middle">
							<a align="center" href="../index.php">Quay lại danh sách</a>
						</td>
					</tr>

					</table>
					';

				} else {
					echo 'Không tìm thấy sản phẩm.';
				}
				?>
			</div>
		</div>

		<div id="footer">FOOTER</div>
	</div>
</body>

</html>