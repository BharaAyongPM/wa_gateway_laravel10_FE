<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZIEZIE Unofficially Whatsapp Gateway - Solusi Otomatisasi WhatsApp untuk Bisnis Anda</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #22c55e 0%, #3b82f6 100%);
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .pricing-card:hover {
            transform: scale(1.03);
        }
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>
<body class="font-sans antialiased text-gray-800">
    <!-- Header/Navigation -->
    <header class="fixed w-full bg-white shadow-sm z-50">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center">
                <img src="/images/logoziezie.png" width="100" height="100">
            </div>
            <nav class="hidden md:flex space-x-8">
                <a href="#features" class="font-medium hover:text-green-600 transition">Fitur</a>
                <a href="#pricing" class="font-medium hover:text-green-600 transition">Harga</a>
                <a href="#technology" class="font-medium hover:text-green-600 transition">Teknologi</a>
                <a href="#faq" class="font-medium hover:text-green-600 transition">FAQ</a>
            </nav>
            <div class="flex items-center space-x-4">
                <a href="{{ route('login') }}" class="hidden md:block px-4 py-2 font-medium hover:text-green-600 transition">Masuk</a>
                <a href="{{ route('register') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition">Daftar Gratis</a>
                <button class="md:hidden text-gray-600">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="gradient-bg text-white pt-32 pb-20">
        <div class="container mx-auto px-4 flex flex-col md:flex-row items-center">
            <div class="md:w-1/2 mb-10 md:mb-0">
                <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">Otomatisasi WhatsApp Bisnis Anda dengan ZIEZIE Gateway</h1>
                <p class="text-xl mb-8 opacity-90">Solusi lengkap untuk UMKM, startup, dan developer dalam mengelola pesan WhatsApp secara efisien dan hemat biaya.</p>
                <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                    <a href="#" class="px-8 py-3 bg-white text-green-600 font-bold rounded-lg text-center hover:bg-gray-100 transition">Mulai Trial 5 Hari</a>
                    <a href="#" class="px-8 py-3 border-2 border-white text-white font-bold rounded-lg text-center hover:bg-white hover:bg-opacity-10 transition">Lihat Demo</a>
                </div>
                 <p class="text-xl mb-8 opacity-90">Unofficially Whatsapp Gateway</p>
            </div>
            <div class="md:w-1/2 flex justify-center">
               <img src="/images/logoz.png" alt="WhatsApp Automation" class="w-full max-w-md floating">

            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold mb-4">Fitur Unggulan ZIEZIE</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Platform lengkap dengan segala yang Anda butuhkan untuk otomatisasi WhatsApp bisnis</p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card bg-white p-8 rounded-xl shadow-md transition duration-300">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-robot text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Auto Reply Cerdas</h3>
                    <p class="text-gray-600">Balasan otomatis berbasis keyword untuk menangani pertanyaan pelanggan 24/7 tanpa operator.</p>
                </div>
                
                <!-- Feature 2 -->
                <div class="feature-card bg-white p-8 rounded-xl shadow-md transition duration-300">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-bullhorn text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Broadcast Message</h3>
                    <p class="text-gray-600">Kirim pesan massal ke ribuan kontak sekaligus dengan jadwal yang bisa diatur.</p>
                </div>
                
                <!-- Feature 3 -->
                <div class="feature-card bg-white p-8 rounded-xl shadow-md transition duration-300">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-mobile-alt text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Multi Device</h3>
                    <p class="text-gray-600">Kelola beberapa nomor WhatsApp dalam satu dashboard untuk tim yang lebih produktif.</p>
                </div>
                
                <!-- Feature 4 -->
                <div class="feature-card bg-white p-8 rounded-xl shadow-md transition duration-300">
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-chart-line text-yellow-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Dashboard Analytics</h3>
                    <p class="text-gray-600">Pantau statistik pengiriman pesan, respon pelanggan, dan kinerja tim secara real-time.</p>
                </div>
                
                <!-- Feature 5 -->
                <div class="feature-card bg-white p-8 rounded-xl shadow-md transition duration-300">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-code text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">API Integration</h3>
                    <p class="text-gray-600">Integrasikan dengan sistem Anda melalui API untuk pengiriman pesan terprogram.</p>
                </div>
                
                <!-- Feature 6 -->
                <div class="feature-card bg-white p-8 rounded-xl shadow-md transition duration-300">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-shield-alt text-indigo-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Keamanan Data</h3>
                    <p class="text-gray-600">Data Anda aman dengan enkripsi dan sistem backup otomatis setiap hari.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold mb-4">Pilih Paket yang Tepat</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Berlangganan fleksibel dengan fitur sesuai kebutuhan bisnis Anda</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <!-- Basic Plan -->
                <div class="pricing-card bg-white border border-gray-200 rounded-xl shadow-sm p-8 transition duration-300">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold mb-2">Basic</h3>
                        <p class="text-gray-600">Untuk bisnis kecil yang baru memulai</p>
                    </div>
                    <div class="mb-6">
                        <span class="text-4xl font-bold">Rp40k</span>
                        <span class="text-gray-600">/bulan</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <span>1 Device aktif</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <span>Kirim pesan teks & OTP</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <span>Limit 1.000 pesan/bulan</span>
                        </li>
                        <li class="flex items-center text-gray-400">
                            <i class="fas fa-times-circle mr-2"></i>
                            <span>Kirim gambar/PDF</span>
                        </li>
                        <li class="flex items-center text-gray-400">
                            <i class="fas fa-times-circle mr-2"></i>
                            <span>Auto-reply khusus</span>
                        </li>
                    </ul>
                    <a href="#" class="block w-full py-3 px-4 text-center bg-gray-100 text-gray-800 font-medium rounded-lg hover:bg-gray-200 transition">Pilih Paket</a>
                </div>
                
                <!-- Standard Plan (Popular) -->
                <div class="pricing-card bg-white border-2 border-green-500 rounded-xl shadow-lg p-8 transition duration-300 relative">
                    <div class="absolute top-0 right-0 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-bl-lg rounded-tr-lg">POPULER</div>
                    <div class="mb-6">
                        <h3 class="text-xl font-bold mb-2">Standard</h3>
                        <p class="text-gray-600">Untuk bisnis yang sedang berkembang</p>
                    </div>
                    <div class="mb-6">
                        <span class="text-4xl font-bold">Rp75k</span>
                        <span class="text-gray-600">/bulan</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <span>2 Device aktif</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <span>Kirim pesan teks, gambar & PDF</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <span>Unlimited pesan</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <span>Basic auto-reply</span>
                        </li>
                        <li class="flex items-center text-gray-400">
                            <i class="fas fa-times-circle mr-2"></i>
                            <span>Auto-reply khusus per user</span>
                        </li>
                    </ul>
                    <a href="#" class="block w-full py-3 px-4 text-center bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">Pilih Paket</a>
                </div>
                
                <!-- Premium Plan -->
                <div class="pricing-card bg-white border border-gray-200 rounded-xl shadow-sm p-8 transition duration-300">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold mb-2">Premium</h3>
                        <p class="text-gray-600">Untuk bisnis besar dengan kebutuhan kompleks</p>
                    </div>
                    <div class="mb-6">
                        <span class="text-4xl font-bold">Rp100k</span>
                        <span class="text-gray-600">/bulan</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <span>5 Device aktif</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <span>Semua jenis pesan & media</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <span>Unlimited pesan</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <span>Auto-reply khusus per user</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <span>Prioritas support</span>
                        </li>
                    </ul>
                    <a href="#" class="block w-full py-3 px-4 text-center bg-gray-800 text-white font-medium rounded-lg hover:bg-gray-900 transition">Pilih Paket</a>
                </div>
            </div>
            
            <div class="mt-12 text-center">
                <p class="text-gray-600">Setiap user baru mendapatkan <span class="font-bold text-green-600">Trial 5 hari</span> dengan 1 device aktif dan fitur terbatas.</p>
            </div>
        </div>
    </section>

    <!-- Technology Section -->
    <section id="technology" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold mb-4">Teknologi Canggih di Balik ZIEZIE</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">Dibangun dengan teknologi terbaik untuk performa optimal</p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 max-w-5xl mx-auto">
                <div class="bg-white p-6 rounded-xl shadow-sm text-center">
                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fab fa-laravel text-blue-600 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-2">Laravel </h3>
                    <p class="text-gray-600 text-sm">Backend yang powerful dan aman untuk sistem berlangganan</p>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow-sm text-center">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fab fa-node-js text-green-600 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-2">Node.js</h3>
                    <p class="text-gray-600 text-sm">WhatsApp Engine berbasis WhatsApp Web.js</p>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow-sm text-center">
                    <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-database text-yellow-600 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-2">MySQL</h3>
                    <p class="text-gray-600 text-sm">Database relational yang handal untuk penyimpanan data</p>
                </div>
                
                <div class="bg-white p-6 rounded-xl shadow-sm text-center">
                    <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-credit-card text-purple-600 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-2">Midtrans</h3>
                    <p class="text-gray-600 text-sm">Integrasi pembayaran otomatis yang aman</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 gradient-bg text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-6">Siap Mengotomatisasi Bisnis Anda?</h2>
            <p class="text-xl mb-8 max-w-2xl mx-auto">Daftar sekarang dan dapatkan trial 5 hari gratis untuk mencoba semua fitur ZIEZIE WhatsApp Gateway.</p>
            <a href="#" class="inline-block px-8 py-3 bg-white text-green-600 font-bold rounded-lg hover:bg-gray-100 transition">Mulai Sekarang - Gratis!</a>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-20 bg-white">
        <div class="container mx-auto px-4 max-w-3xl">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold mb-4">Pertanyaan yang Sering Diajukan</h2>
                <p class="text-xl text-gray-600">Temukan jawaban untuk pertanyaan umum tentang ZIEZIE WhatsApp Gateway</p>
            </div>
            
            <div class="space-y-4">
                <!-- FAQ 1 -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button class="faq-toggle w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 transition">
                        <span class="text-lg font-medium">Bagaimana cara memulai trial 5 hari?</span>
                        <i class="fas fa-chevron-down transition-transform"></i>
                    </button>
                    <div class="faq-content hidden px-6 pb-6 pt-0">
                        <p class="text-gray-600">Anda cukup mendaftar akun baru di ZIEZIE, lalu sistem akan otomatis mengaktifkan trial selama 5 hari dengan 1 device aktif dan fitur terbatas. Tidak perlu kartu kredit untuk memulai trial.</p>
                    </div>
                </div>
                
                <!-- FAQ 2 -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button class="faq-toggle w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 transition">
                        <span class="text-lg font-medium">Apakah nomor WhatsApp saya aman?</span>
                        <i class="fas fa-chevron-down transition-transform"></i>
                    </button>
                    <div class="faq-content hidden px-6 pb-6 pt-0">
                        <p class="text-gray-600">Ya, sangat aman. Kami menggunakan teknologi WhatsApp Web.js yang resmi dari WhatsApp. Data login disimpan secara terenkripsi dan hanya Anda yang memiliki akses ke nomor Anda.</p>
                    </div>
                </div>
                
                <!-- FAQ 3 -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button class="faq-toggle w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 transition">
                        <span class="text-lg font-medium">Bagaimana cara upgrade paket?</span>
                        <i class="fas fa-chevron-down transition-transform"></i>
                    </button>
                    <div class="faq-content hidden px-6 pb-6 pt-0">
                        <p class="text-gray-600">Anda bisa upgrade paket kapan saja melalui dashboard. Pilih paket yang diinginkan, sistem akan menghitung prorata selisih harga, lalu lakukan pembayaran. Upgrade akan aktif segera setelah pembayaran dikonfirmasi.</p>
                    </div>
                </div>
                
                <!-- FAQ 4 -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button class="faq-toggle w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 transition">
                        <span class="text-lg font-medium">Apakah tersedia dokumentasi API?</span>
                        <i class="fas fa-chevron-down transition-transform"></i>
                    </button>
                    <div class="faq-content hidden px-6 pb-6 pt-0">
                        <p class="text-gray-600">Ya, kami menyediakan dokumentasi API lengkap yang bisa diakses setelah login. Dokumentasi mencakup semua endpoint untuk mengirim pesan, cek status device, atur auto reply, dan lainnya.</p>
                    </div>
                </div>
                
                <!-- FAQ 5 -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button class="faq-toggle w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 transition">
                        <span class="text-lg font-medium">Bagaimana jika melebihi limit pesan?</span>
                        <i class="fas fa-chevron-down transition-transform"></i>
                    </button>
                    <div class="faq-content hidden px-6 pb-6 pt-0">
                        <p class="text-gray-600">Untuk paket Basic, jika melebihi limit 1.000 pesan/bulan, Anda bisa upgrade ke paket higher atau membeli tambahan kuota pesan. Sistem akan memberi notifikasi ketika mendekati limit.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white pt-16 pb-8">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8 mb-12">
                <div>
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 rounded-full bg-green-500 flex items-center justify-center text-white font-bold text-xl mr-2">Z</div>
                        <span class="text-xl font-bold">ZIEZIE</span>
                    </div>
                    <p class="text-gray-400">Solusi otomatisasi WhatsApp untuk UMKM, startup, dan developer dengan fitur lengkap dan harga terjangkau.</p>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold mb-4">Perusahaan</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Tentang Kami</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Karir</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Blog</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Kontak</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold mb-4">Produk</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Fitur</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Harga</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">API</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition">Status</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-bold mb-4">Hubungi Kami</h3>
                    <ul class="space-y-2">
                        <li class="flex items-center">
                            <i class="fas fa-envelope text-gray-400 mr-2"></i>
                            <span class="text-gray-400">support@ziezie.com</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fab fa-whatsapp text-gray-400 mr-2"></i>
                            <span class="text-gray-400">+62 812-3456-7890</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                            <span class="text-gray-400">Jakarta, Indonesia</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 mb-4 md:mb-0">© 2023 ZIEZIE WhatsApp Gateway. All rights reserved.</p>
                <div class="flex space-x-6">
                    <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // FAQ Toggle Functionality
        document.querySelectorAll('.faq-toggle').forEach(button => {
            button.addEventListener('click', () => {
                const content = button.nextElementSibling;
                const icon = button.querySelector('i');
                
                // Toggle content
                content.classList.toggle('hidden');
                
                // Rotate icon
                if (content.classList.contains('hidden')) {
                    icon.style.transform = 'rotate(0deg)';
                } else {
                    icon.style.transform = 'rotate(180deg)';
                }
            });
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>