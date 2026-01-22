<?php
session_start();
require_once 'api/db.php';

// Busca todos os produtos
$sql = "SELECT * FROM products WHERE status = 'Ativo'";
$result = $conn->query($sql);
?>
<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/png" href="/logo-thunder.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Thunder Store | Loja de Free Fire</title>
    <meta name="description" content="Sua loja confiável para produtos de Free Fire. Diamantes, contas e muito mais com entrega imediata." />
    <!-- <link rel="stylesheet" crossorigin href="/assets/index-R2RkWoEQ.css"> -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'ff-orange': '#FF9900',
                        'ff-black': '#111111',
                        'ff-gray': '#1F1F1F',
                        'ff-red': '#DC2626',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        html { scroll-behavior: smooth; }
        body { background-color: #000; color: white; font-family: 'Inter', sans-serif; }
    </style>
  </head>
  <body class="bg-black text-white min-h-screen">
    
    <!-- Navbar -->
    <nav class="bg-black/90 backdrop-blur-sm border-b border-red-900/30 fixed w-full z-50 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex-shrink-0 flex items-center gap-3 cursor-pointer" onclick="window.location.href='/'">
                    <img src="/logo-thunder.png" alt="Thunder Store" class="h-10 w-auto drop-shadow-[0_0_10px_rgba(220,38,38,0.5)]">
                    <span class="font-bold text-2xl tracking-tighter text-white">
                        THUNDER <span class="text-red-600 drop-shadow-[0_0_10px_rgba(220,38,38,0.8)]">STORE</span>
                    </span>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-8">
                        <a href="/" class="text-gray-300 hover:text-red-500 hover:scale-105 transition-all duration-300 px-3 py-2 rounded-md text-sm font-medium">INÍCIO</a>
                        <a href="#produtos" class="text-gray-300 hover:text-red-500 hover:scale-105 transition-all duration-300 px-3 py-2 rounded-md text-sm font-medium">PRODUTOS</a>
                        <a href="/carrinho.php" class="text-gray-300 hover:text-red-500 hover:scale-105 transition-all duration-300 px-3 py-2 rounded-md text-sm font-medium">CARRINHO</a>
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <span class="text-gray-300 text-sm">Olá, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            <a href="#" onclick="logout()" class="text-gray-300 hover:text-red-500 text-sm">Sair</a>
                        <?php else: ?>
                            <a href="/login.php" class="text-gray-300 hover:text-red-500 text-sm">Login</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative pt-20 pb-16 md:pt-32 md:pb-24 overflow-hidden">
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-gradient-to-b from-red-900/20 via-black to-black"></div>
        </div>
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight mb-8">
                DOMINE O <span class="text-red-600 drop-shadow-[0_0_15px_rgba(220,38,38,0.6)]">JOGO</span>
            </h1>
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-400">
                As melhores ferramentas para você alcançar o topo. Segurança, qualidade e suporte 24/7.
            </p>
            <div class="mt-10 flex justify-center gap-4">
                <a href="#produtos" class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-full text-white bg-red-600 hover:bg-red-700 md:text-lg transition-all duration-300 hover:shadow-[0_0_20px_rgba(220,38,38,0.5)] transform hover:scale-105">
                    VER PRODUTOS
                </a>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <div id="produtos" class="py-16 bg-black">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-12"><span class="text-red-600">NOSSOS</span> PRODUTOS</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php while($product = $result->fetch_assoc()): ?>
                <div class="group bg-zinc-900/50 border border-zinc-800 hover:border-red-600/50 rounded-xl overflow-hidden transition-all duration-300 hover:shadow-[0_0_30px_rgba(220,38,38,0.2)] hover:-translate-y-2">
                    <div class="relative h-48 overflow-hidden">
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent opacity-80"></div>
                        <div class="absolute bottom-4 left-4">
                            <h3 class="text-xl font-bold text-white group-hover:text-red-500 transition-colors"><?php echo htmlspecialchars($product['name']); ?></h3>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="flex text-yellow-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            </div>
                            <span class="text-xs text-gray-500 font-medium">5.0 (2k+ reviews)</span>
                        </div>
                        
                        <p class="text-gray-400 text-sm mb-6 line-clamp-3">
                            <?php echo htmlspecialchars($product['description']); ?>
                        </p>
                        
                        <a href="/comprar.php?game=<?php echo $product['slug']; ?>" class="block w-full text-center bg-white/5 hover:bg-red-600 text-white font-bold py-3 rounded-lg border border-white/10 hover:border-red-500 transition-all duration-300">
                            VER PLANOS
                        </a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-black border-t border-red-900/20 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <img src="/logo-thunder.png" alt="Logo" class="h-12 w-auto mx-auto mb-6 opacity-50 grayscale hover:grayscale-0 transition-all duration-500">
            <p class="text-gray-500 text-sm">© 2024 Thunder Store. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script>
        async function logout() {
            const formData = new FormData();
            formData.append('action', 'logout');
            await fetch('/api/auth.php', { method: 'POST', body: formData });
            location.reload();
        }
    </script>
  </body>
</html>
