-- phpMyAdmin SQL Dump
-- version 2.11.6
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Jeu 07 Mai 2026 à 01:50
-- Version du serveur: 5.0.51
-- Version de PHP: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Base de données: `tmdt_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `congty`
--

CREATE TABLE `congty` (
  `idcty` int(10) unsigned NOT NULL auto_increment,
  `tencty` varchar(200) collate utf8_unicode_ci NOT NULL,
  `diachi` varchar(200) collate utf8_unicode_ci NOT NULL,
  `dienthoai` text collate utf8_unicode_ci NOT NULL,
  `fax` varchar(100) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`idcty`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Contenu de la table `congty`
--

INSERT INTO `congty` (`idcty`, `tencty`, `diachi`, `dienthoai`, `fax`) VALUES
(1, 'SamSung', '', '021151515', ''),
(2, 'Apple', '', '002154545', '');

-- --------------------------------------------------------

--
-- Structure de la table `dathang`
--

CREATE TABLE `dathang` (
  `iddh` int(11) NOT NULL auto_increment,
  `idkh` int(11) default NULL,
  `id_nhanvien` int(11) default NULL,
  `ngaydathang` datetime default NULL,
  `trangthai` int(11) default '0',
  PRIMARY KEY  (`iddh`),
  KEY `idkh` (`idkh`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Contenu de la table `dathang`
--


-- --------------------------------------------------------

--
-- Structure de la table `dathang_chitiet`
--

CREATE TABLE `dathang_chitiet` (
  `iddh` int(11) NOT NULL default '0',
  `idsp` int(11) NOT NULL default '0',
  `soluong` int(11) default NULL,
  `dongia` float default NULL,
  `giamgia` float default NULL,
  PRIMARY KEY  (`iddh`,`idsp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `dathang_chitiet`
--


-- --------------------------------------------------------

--
-- Structure de la table `khachhang`
--

CREATE TABLE `khachhang` (
  `idkh` int(11) NOT NULL auto_increment,
  `email` varchar(100) collate utf8_unicode_ci NOT NULL,
  `password` varchar(100) collate utf8_unicode_ci NOT NULL,
  `hodem` varchar(50) collate utf8_unicode_ci default NULL,
  `ten` varchar(50) collate utf8_unicode_ci default NULL,
  `diachi` varchar(200) collate utf8_unicode_ci default NULL,
  `diachinhanhang` varchar(200) collate utf8_unicode_ci default NULL,
  `dienthoai` varchar(15) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`idkh`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Contenu de la table `khachhang`
--

INSERT INTO `khachhang` (`idkh`, `email`, `password`, `hodem`, `ten`, `diachi`, `diachinhanhang`, `dienthoai`) VALUES
(1, 'thuanbich1103@gmail.com', 'f448fe4f8c3078b023fd85913d3e6d5f', 'Trần Bích', 'Thuận', '123 Phạm Ngũ Lão, Hạnh Thông, TP HCM', '123 Phạm Ngũ Lão, Hạnh Thông, TP HCM', '031234567');

-- --------------------------------------------------------

--
-- Structure de la table `sanpham`
--

CREATE TABLE `sanpham` (
  `idsp` int(10) unsigned NOT NULL auto_increment,
  `tensp` varchar(100) collate utf8_unicode_ci NOT NULL,
  `gia` float NOT NULL,
  `mota` varchar(500) collate utf8_unicode_ci NOT NULL,
  `hinh` varchar(200) collate utf8_unicode_ci NOT NULL,
  `giamgia` float NOT NULL,
  `idcty` int(11) NOT NULL,
  PRIMARY KEY  (`idsp`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;

--
-- Contenu de la table `sanpham`
--

INSERT INTO `sanpham` (`idsp`, `tensp`, `gia`, `mota`, `hinh`, `giamgia`, `idcty`) VALUES
(1, 'iPhone 12 Pro', 20, 'iPhone xuất xứ từ Mỹ', 'iphone12pro.jpg', 0, 2),
(2, 'SamSung Ultra S24', 21, 'Dòng điện thoại Samsung cao cấp đến từ Hàn Quốc', 'samsung_ultra_S24.jpg', 0, 1),
(3, 'Iphone 17 ProMax', 30, 'Dòng sản phẩm điện thoại thông minh iPhone 17 Pro Max mới nhất', 'iphone17promax.jpg', 0, 2),
(4, 'Samsung Galaxy S26 Ultra', 35, 'Điện thoại Samsung cao cấp hiện đại', 'Samsung Galaxy S26 Ultra.jpg', 0, 1),
(7, 'Samsung Galaxy A17  8GB 128GB', 21, 'Sở hữu thiết kế thanh lịch và hiệu năng ổn định, Galaxy A17 5G là người bạn đồng hành lý tưởng cho những ai cần một chiếc smartphone bền bỉ trong công việc lẫn giải trí hàng ngày. Thiết bị nổi bật với màn hình Super AMOLED 6.7 inch sống động, vi xử lý Exynos 1330 mạnh mẽ cùng kết nối 5G tốc độ cao, mang đến trải nghiệm mượt mà và liền mạch cho người dùng.', 'samsung_galaxy_a17_5g_2.jpg', 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `taikhoan`
--

CREATE TABLE `taikhoan` (
  `iduser` int(10) unsigned NOT NULL auto_increment,
  `usename` varchar(200) collate utf8_unicode_ci NOT NULL,
  `password` varchar(100) collate utf8_unicode_ci NOT NULL,
  `hodem` varchar(100) collate utf8_unicode_ci NOT NULL,
  `ten` varchar(100) collate utf8_unicode_ci NOT NULL,
  `phanquyen` int(11) NOT NULL,
  `landang-nhapcuoi` datetime NOT NULL,
  PRIMARY KEY  (`iduser`),
  UNIQUE KEY `usename` (`usename`),
  KEY `password` (`password`),
  KEY `phanquyen` (`phanquyen`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Contenu de la table `taikhoan`
--

INSERT INTO `taikhoan` (`iduser`, `usename`, `password`, `hodem`, `ten`, `phanquyen`, `landang-nhapcuoi`) VALUES
(1, 'admin', 'e10adc3949ba59abbe56e057f20f883e', 'Admin', 'Chương trình', 2, '2026-04-23 09:52:42');

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `dathang`
--
ALTER TABLE `dathang`
  ADD CONSTRAINT `dathang_ibfk_1` FOREIGN KEY (`idkh`) REFERENCES `khachhang` (`idkh`);

--
-- Contraintes pour la table `dathang_chitiet`
--
ALTER TABLE `dathang_chitiet`
  ADD CONSTRAINT `dathang_chitiet_ibfk_1` FOREIGN KEY (`iddh`) REFERENCES `dathang` (`iddh`);
