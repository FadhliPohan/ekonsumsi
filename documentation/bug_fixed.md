# Daftar Perbaikan Bug (Bug Fixes Log)

| Tanggal | Modul / Bagian | Deskripsi Perbaikan |
|---------|-----------------|---------------------|
| 27 April 2026 | Master Data / Makanan | - **Foto tidak tampil**: Memperbaiki masalah symlink `public/storage` yang rusak sehingga foto yang diupload sebelumnya tidak bisa dimuat oleh browser. <br> - **Bug menyimpan foto**: Menambahkan reset form (`$('#form-food')[0].reset()`) pada modal Edit makanan. Hal ini mencegah bug di mana file foto dari makanan sebelumnya yang diedit ikut ter-submit jika user mengedit makanan lain. |
| 27 April 2026 | Sidebar Layout | - **Tampilan Role berantakan**: Menyembunyikan judul menu (`menu-title`) "User Management" dan "Saldo" menggunakan directive `@canany` dan `@can`. Hal ini mencegah munculnya judul menu kosong yang membingungkan bagi pengguna (role) yang tidak memiliki hak akses (permissions) ke dalam menu-menu tersebut. |
