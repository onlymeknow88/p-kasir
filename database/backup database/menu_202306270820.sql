INSERT INTO `p-pos`.menu (nama_menu,class,url,parent_id,menu_kategori_id,aktif,`new`,urut,created_at,updated_at,link,menu_status_id) VALUES
	 ('Aplikasi','far fa-sun','aplikasi',NULL,1,'Y',0,1,'2023-06-04 09:53:17','2023-06-06 09:16:27','#',1),
	 ('Menu','fas fa-clone','aplikasi/menu',1,1,'Y',0,1,'2023-06-05 07:27:36','2023-06-06 12:26:43',NULL,1),
	 ('Dashboard','fas fa-tachometer-alt','dashboard',NULL,8,'Y',0,1,'2023-06-05 11:38:42','2023-06-06 10:49:48',NULL,1),
	 ('User','fas fa-user-friends','user',NULL,8,'Y',0,9,'2023-06-06 00:05:29','2023-06-26 02:23:19',NULL,1),
	 ('Role','fas fa-briefcase','aplikasi/role',1,1,'Y',0,2,'2023-06-06 01:09:53','2023-06-06 12:26:43',NULL,1),
	 ('Setting','fas fa-cogs','setting',1,1,'Y',0,4,'2023-06-07 08:56:09','2023-06-08 04:37:51','#',1),
	 ('Setting Aplikasi','fas fa-cogs','aplikasi/setting/setting-app',15,1,'Y',0,1,'2023-06-07 08:56:36','2023-06-07 09:01:55',NULL,1),
	 ('Setting','fas fa-cogs','setting',NULL,8,'Y',0,7,'2023-06-11 14:17:22','2023-06-26 02:23:19','#',1),
	 ('Refrensi','fas fa-clipboard-list','refrensi',NULL,8,'Y',0,8,'2023-06-11 14:19:23','2023-06-26 02:23:19','#',1),
	 ('Kategori','far fa-folder','kategori',18,8,'Y',0,2,'2023-06-11 14:29:00','2023-06-12 13:12:09',NULL,1);
INSERT INTO `p-pos`.menu (nama_menu,class,url,parent_id,menu_kategori_id,aktif,`new`,urut,created_at,updated_at,link,menu_status_id) VALUES
	 ('Unit','fas fa-ruler-horizontal','unit',18,8,'Y',0,1,'2023-06-11 14:29:58','2023-06-12 13:12:09',NULL,1),
	 ('Jenis Harga','fas fa-clipboard-list','jenis-harga',17,8,'Y',0,1,'2023-06-12 12:04:25','2023-06-13 07:19:36',NULL,1),
	 ('Dokumentasi Transaksi','fas fa-cog','invoice',17,8,'Y',0,2,'2023-06-13 00:47:18','2023-06-13 07:19:36',NULL,1),
	 ('Setting Pajak','fas fa-dollar-sign','pajak',17,8,'Y',0,3,'2023-06-13 07:05:03','2023-06-13 07:18:49',NULL,1),
	 ('Supplier','fas fa-truck','supplier',NULL,8,'Y',0,4,'2023-06-13 07:43:11','2023-06-20 12:07:12',NULL,1),
	 ('Customer','fas fa-users','customer',NULL,8,'Y',0,5,'2023-06-13 09:48:12','2023-06-20 12:07:12',NULL,1),
	 ('Barang','fas fa-box','barang',NULL,8,'Y',0,3,'2023-06-15 07:13:26','2023-06-20 12:07:12',NULL,1),
	 ('Gudang','fas fa-building','gudang',NULL,8,'Y',0,2,'2023-06-20 12:07:07','2023-06-20 12:07:12',NULL,1),
	 ('List Gudang','far fa-building','list-gudang',27,8,'Y',0,1,'2023-06-20 12:35:30','2023-06-26 02:23:19',NULL,1),
	 ('Transfer Barang','fas fa-exchange-alt','transfer-barang',27,8,'Y',0,2,'2023-06-20 12:42:33','2023-06-26 02:23:19',NULL,1);
INSERT INTO `p-pos`.menu (nama_menu,class,url,parent_id,menu_kategori_id,aktif,`new`,urut,created_at,updated_at,link,menu_status_id) VALUES
	 ('Cetak Barcode','fas fa-barcode','barcode-cetak',NULL,8,'Y',0,6,'2023-06-26 02:23:11','2023-06-26 12:51:20',NULL,1);
