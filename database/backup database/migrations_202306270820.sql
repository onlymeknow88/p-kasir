INSERT INTO `p-pos`.migrations (migration,batch) VALUES
	 ('2014_10_12_000000_create_users_table',1),
	 ('2014_10_12_100000_create_password_resets_table',1),
	 ('2014_10_12_200000_add_two_factor_columns_to_users_table',1),
	 ('2019_08_19_000000_create_failed_jobs_table',1),
	 ('2019_12_14_000001_create_personal_access_tokens_table',1),
	 ('2023_05_27_132736_create_sessions_table',1),
	 ('2023_05_27_234834_create_menu_kategori_table',1),
	 ('2023_05_27_235842_create_menu_table',1),
	 ('2023_05_27_235850_create_role_table',1),
	 ('2023_05_27_235908_create_menu_role_table',1);
INSERT INTO `p-pos`.migrations (migration,batch) VALUES
	 ('2023_05_30_022957_create_permission_table',1),
	 ('2023_05_30_061338_add_link_to_menu_table',1),
	 ('2023_05_30_125522_add_menu_id_to_role_table',1),
	 ('2023_05_30_233653_create_menu_status_table',1),
	 ('2023_05_30_233847_add_menu_status_id_to_menu_table',1),
	 ('2023_06_07_084230_create_setting_table',1),
	 ('2023_06_10_071113_add_username_to_users_table',1),
	 ('2023_06_11_143350_create_unit_table',1),
	 ('2023_06_11_150653_add_keterangan_to_unit_table',1),
	 ('2023_06_12_011650_create_kategori_table',1);
INSERT INTO `p-pos`.migrations (migration,batch) VALUES
	 ('2023_06_12_120812_create_jenis_harga_table',1),
	 ('2023_06_13_074543_create_supplier_table',1),
	 ('2023_06_13_094230_create_customer_table',1),
	 ('2023_06_15_064826_create_barang_table',2),
	 ('2023_06_16_115622_create_gudang_table',3),
	 ('2023_06_16_115704_create_file_picker_table',4),
	 ('2023_06_16_115808_create_barang_image_table',5),
	 ('2023_06_16_115843_create_barang_table',5),
	 ('2023_06_16_115933_create_barang_harga_table',6),
	 ('2023_06_16_152539_create_barang_adjusment_stok_table',6);
INSERT INTO `p-pos`.migrations (migration,batch) VALUES
	 ('2023_06_20_130220_create_transfer_barang_table',7),
	 ('2023_06_21_110732_create_transfer_barang_detail_table',8);
