-- phpMyAdmin SQL Dump
-- Thêm bảng giỏ hàng vào database `tmdt_db`
--
-- Serveur: localhost
-- Version du serveur: 5.0.51
-- Généré le : Mer 07 Mai 2026
--

-- --------------------------------------------------------

--
-- Structure de la table `giohang`
--

CREATE TABLE `giohang` (
  `idgiohang` int(11) NOT NULL auto_increment,
  `idkh` int(11) NOT NULL,
  `trangthai` tinyint(1) NOT NULL default '0' COMMENT '0: dang chon, 1: da dat hang',
  `ngaytao` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`idgiohang`),
  KEY `idkh` (`idkh`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Contenu de la table `giohang`
--


-- --------------------------------------------------------

--
-- Structure de la table `chitietgiohang`
--

CREATE TABLE `chitietgiohang` (
  `idchitiet` int(11) NOT NULL auto_increment,
  `idgiohang` int(11) NOT NULL,
  `idsp` int(10) unsigned NOT NULL,
  `soluong` int(11) NOT NULL default '1',
  `gia` float NOT NULL COMMENT 'Gia tai thoi diem them vao gio',
  PRIMARY KEY  (`idchitiet`),
  KEY `idgiohang` (`idgiohang`),
  KEY `idsp` (`idsp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Contenu de la table `chitietgiohang`
--


--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `giohang`
--
ALTER TABLE `giohang`
  ADD CONSTRAINT `giohang_ibfk_1` FOREIGN KEY (`idkh`) REFERENCES `khachhang` (`idkh`) ON DELETE CASCADE;

--
-- Contraintes pour la table `chitietgiohang`
--
ALTER TABLE `chitietgiohang`
  ADD CONSTRAINT `chitietgiohang_ibfk_1` FOREIGN KEY (`idgiohang`) REFERENCES `giohang` (`idgiohang`) ON DELETE CASCADE,
  ADD CONSTRAINT `chitietgiohang_ibfk_2` FOREIGN KEY (`idsp`) REFERENCES `sanpham` (`idsp`) ON DELETE CASCADE;
