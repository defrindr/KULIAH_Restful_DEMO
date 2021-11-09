-- Adminer 4.6.2 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `master_posisi`;
CREATE TABLE `master_posisi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_posisi` varchar(100) NOT NULL,
  `flag` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama_posisi` (`nama_posisi`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `master_posisi` (`id`, `nama_posisi`, `flag`) VALUES
(5,	'KEPALA SEKOLAH',	1),
(7,	'GURU',	1),
(9,	'STAFF KEBERSIHAN',	1),
(12,	'KEPSEK',	1);

DROP TABLE IF EXISTS `pegawai`;
CREATE TABLE `pegawai` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_pegawai` varchar(50) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `posisi_pegawai` int(11) NOT NULL,
  `alamat_pegawai` text NOT NULL,
  `status_pegawai` int(11) NOT NULL COMMENT '0:tidak_aktif;1:aktif',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `pegawai` (`id`, `nama_pegawai`, `jenis_kelamin`, `posisi_pegawai`, `alamat_pegawai`, `status_pegawai`) VALUES
(1,	'DEFRI INDRA',	'L',	5,	'Ponorogo',	0),
(2,	'DEFRI INDRA',	'L',	5,	'Ponorogo',	0),
(3,	'DEFRI INDRA Mahardika',	'L',	5,	'Ponorogo',	0),
(4,	'DEFRI INDRA Maharidka',	'L',	5,	'Ponorogo',	0);

-- 2021-11-09 05:35:38
