<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: ../user/dashboard.php');
    exit;
}
$error = htmlspecialchars($_GET['error'] ?? '', ENT_QUOTES);
$identifier = htmlspecialchars($_GET['identifier'] ?? '', ENT_QUOTES);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Perpustakaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-50 to-blue-50">
    <main class="min-h-screen">
        <section class="flex items-center justify-center min-h-screen px-4">
            <div class="max-w-md w-full">
                
                <!-- Card -->
                <div class="bg-white rounded-2xl shadow-2xl p-8">
                    <!-- Header -->
                    <div class="text-center mb-8">
                        <div class="flex justify-center mb-4">
                            <img src="../img/logosipus.png" alt="SIPUS Logo" class="h-16 w-16" />
                        </div>
                        <h1 class="text-3xl font-bold text-slate-900 mb-2">SIPUS</h1>
                        <p class="text-indigo-600 font-semibold">Sistem Informasi Perpustakaan</p>
                        <p class="text-slate-600 mt-4">Masuk untuk melanjutkan peminjaman buku</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form action="proseslogin.php" method="POST" class="space-y-5">
                        <div>
                            <label for="identifier" class="block text-slate-700 font-semibold mb-2">Email atau Username</label>
                            <input 
                                id="identifier" 
                                name="identifier" 
                                type="text" 
                                value="<?php echo $identifier; ?>" 
                                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="Masukkan email atau username" 
                                required 
                            />
                        </div>
                        <div>
                            <label for="password" class="block text-slate-700 font-semibold mb-2">Kata Sandi</label>
                            <input 
                                id="password" 
                                name="password" 
                                type="password" 
                                class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="Masukkan kata sandi" 
                                required 
                            />
                        </div>
                        <button type="submit" class="w-full px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition duration-200 shadow-md hover:shadow-lg mt-6">
                            Masuk Sekarang
                        </button>
                    </form>

                    <p class="text-center text-slate-600 text-sm mt-6">
                        Belum punya akun? Hubungi administrator perpustakaan untuk pendaftaran.
                    </p>

                    <a href="../index.php"
   class="inline-block px-4 py-2 bg-white border border-slate-300 rounded-lg text-sm text-slate-700 hover:bg-slate-100 transition shadow-sm">
    ← Kembali ke Beranda
</a>
                </div>
                    
                
                <!-- Footer Info -->
                <div class="text-center mt-8">
                    <p class="text-slate-600 text-sm">Perpustakaan Digital SIPUS © 2024</p>
                </div>
            </div>
            
        </section>
    </main>
</body>
</html>
