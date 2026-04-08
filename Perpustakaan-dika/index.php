<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-b from-slate-50 to-slate-100">
    <!-- Navbar -->
<header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            <!-- Logo -->
            <div class="flex items-center gap-3">
                <img src="img/logosipus.png" class="h-10 w-10">
                <span class="font-bold text-lg text-slate-800">SIPUS</span>
            </div>

            <!-- Menu -->
            <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-600">
                <a href="#features" class="hover:text-indigo-600 transition">Fitur</a>
                <a href="#why-us" class="hover:text-indigo-600 transition">Kenapa Kami</a>
                <a href="#footer" class="hover:text-indigo-600 transition">Kontak</a>
            </nav>

            <!-- Button -->
            <div class="flex items-center gap-3">
                <a href="auth/login.php"
                   class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition">
                    Login
                </a>
            </div>

        </div>
    </div>
</header>
    <main>
        <!-- Hero Section -->
        <section class="bg-white py-24 lg:py-32">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <span class="text-sm font-bold text-indigo-600 uppercase tracking-widest block mb-4">Sistem Perpustakaan Profesional</span>
                        <h1 class="text-4xl lg:text-5xl font-bold text-slate-900 mb-6 leading-tight">Kelola peminjaman, data anggota, dan koleksi buku dengan mudah.</h1>
                        <p class="text-lg text-slate-600 mb-8 leading-relaxed">Perpustakaan Digital hadir untuk memudahkan sekolah dan komunitas dalam mengelola seluruh proses perpustakaan secara online dengan tampilan modern dan performa stabil.</p>

                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="auth/login.php" class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition shadow-lg">Masuk Sekarang</a>
                            <a href="#features" class="inline-flex items-center justify-center px-6 py-3 border-2 border-indigo-600 text-indigo-600 font-semibold rounded-lg hover:bg-indigo-50 transition">Pelajari Fitur</a>
                        </div>
                    </div>

                    <div class="flex flex-col items-center justify-center gap-4 p-8 bg-slate-50 rounded-2xl">
                        <img src="img/logosipus.png" alt="SIPUS Logo" class="w-64 h-auto object-contain" />
                        <p class="text-sm text-slate-600 text-center">Logo resmi SIPUS di halaman utama</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="bg-slate-900 text-white py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-14">
                    <p class="text-sm font-bold text-indigo-400 uppercase tracking-widest block mb-2">Fitur Utama</p>
                    <h2 class="text-3xl lg:text-4xl font-bold mb-4">Solusi lengkap untuk perpustakaan digital</h2>
                    <p class="text-lg text-slate-300 max-w-2xl mx-auto">Dari pencatatan buku hingga peminjaman dan pengembalian, semua bisa dikelola dari satu sistem yang intuitif.</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-slate-800 rounded-xl p-8 hover:shadow-2xl transition">
                        <div class="text-4xl mb-4">🔍</div>
                        <h3 class="text-xl font-bold mb-3">Cari & Temukan</h3>
                        <p class="text-slate-300">Fitur pencarian cepat memungkinkan siswa dan guru menemukan buku dengan mudah.</p>
                    </div>
                    <div class="bg-slate-800 rounded-xl p-8 hover:shadow-2xl transition">
                        <div class="text-4xl mb-4">📋</div>
                        <h3 class="text-xl font-bold mb-3">Proses Peminjaman</h3>
                        <p class="text-slate-300">Alur peminjaman yang sederhana, lengkap dengan catatan tanggal kembali.</p>
                    </div>
                    <div class="bg-slate-800 rounded-xl p-8 hover:shadow-2xl transition">
                        <div class="text-4xl mb-4">📊</div>
                        <h3 class="text-xl font-bold mb-3">Laporan & Statistik</h3>
                        <p class="text-slate-300">Data dipresentasikan dalam dashboard ringkas sehingga keputusan lebih cepat.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Us Section -->
        <section id="why-us" class="bg-white py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <p class="text-sm font-bold text-indigo-600 uppercase tracking-widest block mb-2">Kenapa Memilih Kami</p>
                        <h2 class="text-3xl lg:text-4xl font-bold text-slate-900 mb-6">Desain modern dengan pengalaman pengguna yang nyaman</h2>
                        <p class="text-lg text-slate-600 mb-8">Perpustakaan Digital dirancang khusus untuk pendidikan. Antarmuka yang rapi membantu pengguna dari semua tingkat kemampuan untuk melakukan aktivitas perpustakaan tanpa kebingungan.</p>

                        <div class="space-y-4">
                            <div class="flex gap-4 p-5 bg-slate-50 rounded-lg border border-slate-200">
                                <div class="flex-shrink-0"><div class="text-2xl">✓</div></div>
                                <div>
                                    <p class="font-bold text-slate-900">Implementasi mudah</p>
                                    <p class="text-slate-600 text-sm">Sistem bisa langsung digunakan tanpa konfigurasi rumit.</p>
                                </div>
                            </div>
                            <div class="flex gap-4 p-5 bg-slate-50 rounded-lg border border-slate-200">
                                <div class="flex-shrink-0"><div class="text-2xl">✓</div></div>
                                <div>
                                    <p class="font-bold text-slate-900">Berbasis web</p>
                                    <p class="text-slate-600 text-sm">Akses kapan saja dari perangkat apa pun.</p>
                                </div>
                            </div>
                            <div class="flex gap-4 p-5 bg-slate-50 rounded-lg border border-slate-200">
                                <div class="flex-shrink-0"><div class="text-2xl">✓</div></div>
                                <div>
                                    <p class="font-bold text-slate-900">Keamanan data</p>
                                    <p class="text-slate-600 text-sm">Pengelolaan pengguna dan session lebih aman dengan login terproteksi.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-indigo-100 to-blue-100 rounded-2xl overflow-hidden shadow-xl">
                        <div class="h-64 bg-gradient-to-br from-indigo-500 to-blue-600"></div>
                        <div class="p-8">
                            <p class="text-2xl font-bold text-slate-900 mb-4">Antarmuka yang visual dan terstruktur</p>
                            <p class="text-slate-600">Dengan layout modern dan warna profesional, setiap halaman memberikan pengalaman yang lebih nyaman dan profesional bagi pengguna perpustakaan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer id="footer" class="bg-slate-900 text-slate-300 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
            <div class="grid md:grid-cols-3 gap-12">
                <div>
                    <p class="text-lg font-bold text-white mb-4">Perpustakaan Digital</p>
                    <p class="text-sm leading-relaxed">Solusi perpustakaan modern untuk sekolah dan komunitas yang ingin bekerja lebih terstruktur.</p>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-4">Menu</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#features" class="hover:text-indigo-400 transition">Fitur</a></li>
                        <li><a href="#why-us" class="hover:text-indigo-400 transition">Kenapa Kami</a></li>
                        <li><a href="auth/login.php" class="hover:text-indigo-400 transition">Login</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-white mb-4">Hubungi Kami</h4>
                    <p class="text-sm leading-relaxed">Untuk pertanyaan lebih lanjut, hubungi administrator perpustakaan sekolah Anda.</p>
                </div>
            </div>
        </div>
        <div class="border-t border-slate-700 text-center py-6 text-sm">© 2024 Perpustakaan Digital. Semua hak dilindungi.</div>
    </footer>

</body>
</html>
