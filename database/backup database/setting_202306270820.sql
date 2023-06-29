INSERT INTO `p-pos`.setting (`type`,param,value,created_at,updated_at) VALUES
	 ('app','background_logo','transparent',NULL,NULL),
	 ('app','favicon','logo_aplikasi_2.png',NULL,NULL),
	 ('app','footer_app','&copy; {{ YEAR }} &lt;a href=&quot;https://p-pos.test&quot; target=&quot;_blank&quot;&gt;www.Koperasi.com&lt;/a&gt;',NULL,NULL),
	 ('app','footer_login','&copy; {{ YEAR }} &lt;a href=&quot;https://p-pos.test&quot; target=&quot;_blank&quot;&gt;www.Koperasi.com&lt;/a&gt;',NULL,NULL),
	 ('app','judul_web','Admin Template Koperasi',NULL,NULL),
	 ('app','logo_app','logo_aplikasi_3.png',NULL,NULL),
	 ('app','logo_login','logo_aplikasi_1.png',NULL,NULL),
	 ('invoice','footer_text','Terima kasih telah berbelanja di tempat kami. Kepuasan Anda adalah tujuan kami.',NULL,NULL),
	 ('invoice','jml_digit','6',NULL,NULL),
	 ('invoice','logo','logo_invoice.png',NULL,NULL);
INSERT INTO `p-pos`.setting (`type`,param,value,created_at,updated_at) VALUES
	 ('invoice','no_invoice','{{ nomor }}/INV/JWD/{{ tahun }}',NULL,NULL),
	 ('nota_retur','jml_digit','6',NULL,NULL),
	 ('nota_retur','no_nota_retur','{{ nomor }}/NR/JWD/{{ tahun }}',NULL,NULL),
	 ('nota_transfer','jml_digit','6',NULL,NULL),
	 ('nota_transfer','no_nota_transfer','{{ nomor }}/NR/JWD/{{ tahun }}',NULL,NULL),
	 ('pajak','display_text','Pajak',NULL,NULL),
	 ('pajak','status','aktif',NULL,NULL),
	 ('pajak','tarif','10',NULL,NULL);
