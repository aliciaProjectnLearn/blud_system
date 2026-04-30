# Sistem BLUD Sekolah

## Deskripsi

Sistem BLUD Sekolah adalah aplikasi berbasis web yang dikembangkan untuk membantu pengelolaan layanan usaha sekolah yang berada di bawah pengelolaan **Badan Layanan Umum Daerah (BLUD)**. Sistem ini dirancang untuk mengintegrasikan beberapa layanan usaha sekolah dalam satu platform sehingga lebih mudah diakses, dikelola, dan dimonitor.

Melalui sistem ini, pengguna hanya perlu melakukan **satu kali pendaftaran akun** untuk dapat mengakses berbagai layanan yang tersedia dalam aplikasi BLUD.

## Layanan yang Tersedia

Aplikasi ini terdiri dari tiga modul utama layanan:

1. **Sistem Booking Lapangan Futsal**
   Digunakan oleh pengguna untuk melakukan pemesanan jadwal penggunaan lapangan futsal secara online.

2. **Sistem Booking Jasa Cuci AC**
   Digunakan untuk melakukan pemesanan layanan pembersihan dan perawatan AC.

3. **Sistem Sewa Ruko Kantin**
   Digunakan untuk mengelola proses penyewaan ruko kantin yang tersedia di lingkungan sekolah.

## Struktur Pengguna Sistem

Sistem ini memiliki beberapa jenis pengguna dengan hak akses yang berbeda, yaitu:

* **Super Admin**
  Bertanggung jawab mengelola seluruh sistem BLUD, termasuk pengelolaan pengguna dan pengawasan seluruh layanan yang tersedia.

* **Admin Layanan**
  Setiap layanan memiliki admin tersendiri yang bertugas mengelola operasional layanan masing-masing, seperti:

  * Admin Booking Lapangan Futsal
  * Admin Booking Cuci AC
  * Admin Sewa Ruko Kantin

* **User / Pengguna**
  Pengguna umum yang dapat melakukan pendaftaran akun dan menggunakan layanan yang tersedia dalam sistem.

## Tujuan Pengembangan

Pengembangan sistem ini bertujuan untuk:

* Meningkatkan efisiensi pengelolaan layanan usaha sekolah.
* Mempermudah proses pemesanan layanan secara online.
* Mengintegrasikan beberapa layanan dalam satu platform terpusat.
* Mendukung digitalisasi pengelolaan usaha sekolah berbasis BLUD.

## Teknologi yang Digunakan

Beberapa teknologi yang digunakan dalam pengembangan sistem ini antara lain:

* **Laravel** sebagai framework backend
* **MySQL** sebagai sistem manajemen basis data
* **HTML, CSS, dan JavaScript** untuk pengembangan antarmuka pengguna

## Struktur Pengembangan

Repository ini menggunakan strategi branching sebagai berikut:

* `main` : branch utama yang digunakan untuk versi stabil atau produksi.
* `develop` : branch utama untuk pengembangan fitur.

## Catatan

Proyek ini dikembangkan sebagai bagian dari kegiatan **Praktik Kerja Lapangan (PKL)** dan bertujuan untuk membantu digitalisasi layanan usaha sekolah melalui sistem berbasis web.
