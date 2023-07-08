/*
SQLyog Ultimate v12.5.1 (64 bit)
MySQL - 10.1.38-MariaDB : Database - db_bllb
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`db_bllb` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `db_bllb`;

/*Table structure for table `data_detail_peminjamans` */

DROP TABLE IF EXISTS `data_detail_peminjamans`;

CREATE TABLE `data_detail_peminjamans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_orders` int(11) DEFAULT NULL,
  `id_produks` int(11) DEFAULT NULL,
  `harga` varchar(255) DEFAULT NULL,
  `jumlah_barang` varchar(11) DEFAULT NULL,
  `status` enum('0','1','2') DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_orders` (`id_orders`),
  KEY `id_produks` (`id_produks`),
  CONSTRAINT `data_detail_peminjamans_ibfk_1` FOREIGN KEY (`id_orders`) REFERENCES `data_peminjamans` (`id`),
  CONSTRAINT `data_detail_peminjamans_ibfk_2` FOREIGN KEY (`id_produks`) REFERENCES `data_produks` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

/*Data for the table `data_detail_peminjamans` */

insert  into `data_detail_peminjamans`(`id`,`id_orders`,`id_produks`,`harga`,`jumlah_barang`,`status`) values 
(14,34,10,'10000','9','0');

/*Table structure for table `data_kategoris` */

DROP TABLE IF EXISTS `data_kategoris`;

CREATE TABLE `data_kategoris` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

/*Data for the table `data_kategoris` */

insert  into `data_kategoris`(`id`,`nama_kategori`,`created_at`,`updated_at`) values 
(1,'Pakaian Bawahan','2021-09-25 12:38:45','2021-09-25 04:38:45'),
(3,'Aksesori','2021-09-25 04:48:25','2021-09-25 04:48:25'),
(4,'Atasan','2021-09-28 04:44:33','2021-09-28 04:44:33'),
(5,'Tari Kreasi Sport','2021-10-15 06:43:13','2021-10-15 06:43:13'),
(6,'Tari Kreasi Casual','2021-10-15 06:43:20','2021-10-15 06:43:20');

/*Table structure for table `data_konsumens` */

DROP TABLE IF EXISTS `data_konsumens`;

CREATE TABLE `data_konsumens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_konsumen` varchar(50) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `id_api` varchar(50) DEFAULT NULL,
  `id_identitas` varchar(255) DEFAULT NULL,
  `telepon` varchar(12) DEFAULT NULL,
  `alamat` varchar(50) DEFAULT NULL,
  `type` enum('line') DEFAULT 'line',
  `status` enum('0','1','2','online') DEFAULT '0',
  `tanggal_daftar` datetime DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

/*Data for the table `data_konsumens` */

insert  into `data_konsumens`(`id`,`nama_konsumen`,`username`,`id_api`,`id_identitas`,`telepon`,`alamat`,`type`,`status`,`tanggal_daftar`,`created_at`,`updated_at`) values 
(4,'Joevanca',NULL,NULL,'1234567891','082123456987','Singaraja',NULL,NULL,NULL,'2021-09-22 05:44:46','2021-09-22 06:02:08'),
(6,'Fajar',NULL,NULL,'9202020101','089212345123','Badung',NULL,NULL,NULL,'2021-09-22 06:04:54','2021-09-22 06:04:54'),
(7,'Dipa',NULL,NULL,'150900293','082312883901','Singaraja',NULL,'0','2021-09-25 11:33:29','2021-09-25 03:33:29','2021-09-25 03:33:29'),
(8,'Satria',NULL,NULL,'190221920192','082134890221','Singaraja','line','0','2021-09-26 19:42:50','2021-09-26 11:42:50','2021-09-26 11:42:50');

/*Table structure for table `data_peminjamans` */

DROP TABLE IF EXISTS `data_peminjamans`;

CREATE TABLE `data_peminjamans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `boking_code` varchar(255) DEFAULT NULL,
  `id_konsumens` int(11) DEFAULT NULL,
  `harga_akhir` varchar(255) DEFAULT NULL,
  `tgl_pinjam` date DEFAULT NULL,
  `tgl_kembali` date DEFAULT NULL,
  `denda` varchar(255) DEFAULT NULL,
  `status` enum('0','1','2') DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_konsumens` (`id_konsumens`),
  CONSTRAINT `data_peminjamans_ibfk_1` FOREIGN KEY (`id_konsumens`) REFERENCES `data_konsumens` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;

/*Data for the table `data_peminjamans` */

insert  into `data_peminjamans`(`id`,`boking_code`,`id_konsumens`,`harga_akhir`,`tgl_pinjam`,`tgl_kembali`,`denda`,`status`) values 
(34,'M-1255734891',4,'180000','2021-10-16','2021-10-18',NULL,'0');

/*Table structure for table `data_produks` */

DROP TABLE IF EXISTS `data_produks`;

CREATE TABLE `data_produks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_produk` varchar(50) DEFAULT NULL,
  `id_kategoris` int(11) DEFAULT NULL,
  `deskripsi` varchar(100) DEFAULT NULL,
  `stok` int(11) DEFAULT NULL,
  `available` int(11) DEFAULT NULL,
  `harga` int(11) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status` enum('0','1') DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_kategoris` (`id_kategoris`),
  CONSTRAINT `data_produks_ibfk_1` FOREIGN KEY (`id_kategoris`) REFERENCES `data_kategoris` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

/*Data for the table `data_produks` */

insert  into `data_produks`(`id`,`nama_produk`,`id_kategoris`,`deskripsi`,`stok`,`available`,`harga`,`foto`,`status`,`created_at`,`updated_at`) values 
(10,'Tari Kreasi Kuning',5,'Pakaian sudah termasuk atasan, bawahan dan aksesoris',10,1,10000,'dataproduk-image/CNDuWBTamMazb8Zoa8pUMSfI4YRKz6ENkxsZMVOP.jpg','0','2021-10-15 14:51:48','2021-10-15 06:51:48'),
(11,'Baju Tari Kreasi Campuran Orange',5,'Pakaian sudah termasuk atasan, bawahan dan aksesoris.',10,10,25000,'dataproduk-image/hZ1cwdblH9yYSVgn9gSWHOnTqevqRcOd2tAE4hwO.jpg','0','2021-10-15 14:50:58','2021-10-15 06:50:58');

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `migrations` */

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`name`,`email`,`email_verified_at`,`password`,`remember_token`,`created_at`,`updated_at`) values 
(0,'Azma Joevanca Valliant Diputra','azma@yahoo.com','2021-09-13 12:13:59','$2y$10$u9gsLtykKHBZ.JO667RHde90thod4sQhB9kzSlSB6kAEzVyJyl29S',NULL,'2021-09-13 12:13:59','2021-09-13 12:13:59'),
(2,'Jaladi','jaladi@email.com','2021-09-14 06:18:13','$2y$10$lmNV2EBpSZEMB2bpUEdSdOxpoELkKMx5ncySJThWd/7yYDZvqlTNG',NULL,'2021-09-14 06:18:13','2021-09-14 06:18:13'),
(3,'Diputra Azma','diputra@email.com','2021-09-14 06:20:18','$2y$10$ME/cFSCtyrOc6H4jvgYkQ.TEGTww2ZtA.PqT332jWgqlH4.g7z22m',NULL,'2021-09-14 06:20:18','2021-09-14 06:20:18'),
(8,'Joevanca','joevanca@yahoo.com',NULL,'$2y$10$OmBoaLnYtJWCoQFEFuy3LeqpzI2mL4hR89xhhNMiwxsNxxPUJ4lkq',NULL,'2021-09-16 09:21:27','2021-09-16 09:21:27'),
(9,'Azma Joevanca Valliant Diputra','azmajoevanca@yahoo.com',NULL,'$2y$10$I7jqi9i3N1G0VL745DEUv.oXYRsBxNkD9lX3C60Mjkph/9tj0W5AC',NULL,'2021-09-20 07:30:00','2021-09-20 07:30:00');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
