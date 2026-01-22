<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Thunder Store</title>
    <!-- <link rel="stylesheet" href="/assets/index-R2RkWoEQ.css"> -->
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
    <link rel="icon" type="image/png" href="/logo-thunder.png" />
    <style>
        body { background-color: #000; color: white; font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-black text-white min-h-screen flex flex-col">
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
                        <a href="/#produtos" class="text-gray-300 hover:text-red-500 hover:scale-105 transition-all duration-300 px-3 py-2 rounded-md text-sm font-medium">PRODUTOS</a>
                        <a href="/carrinho.php" class="text-gray-300 hover:text-red-500 hover:scale-105 transition-all duration-300 px-3 py-2 rounded-md text-sm font-medium">CARRINHO</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex-grow flex items-center justify-center pt-24 px-4">
        <div class="max-w-md w-full bg-zinc-900/80 border border-red-900/30 p-8 rounded-xl shadow-2xl backdrop-blur-md">
            <h2 class="text-3xl font-bold text-center mb-8">Login</h2>
            <form id="loginForm" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-400">Email</label>
                    <input type="email" id="email" name="email" required class="mt-1 block w-full px-3 py-2 bg-black border border-red-900/30 rounded-md text-white focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-400">Senha</label>
                    <input type="password" id="password" name="password" required class="mt-1 block w-full px-3 py-2 bg-black border border-red-900/30 rounded-md text-white focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-colors">
                </div>
                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-transform transform hover:scale-105">
                    Entrar
                </button>
            </form>
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-400">Não tem uma conta? <a href="/cadastro.php" class="text-red-500 hover:text-red-400">Cadastre-se</a></p>
            </div>
            <div id="message" class="mt-4 text-center text-sm font-medium hidden"></div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'login');

            try {
                const response = await fetch('/api/auth.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                const msgDiv = document.getElementById('message');
                msgDiv.textContent = data.message;
                msgDiv.classList.remove('hidden');
                
                if (data.success) {
                    msgDiv.className = 'mt-4 text-center text-sm font-medium text-green-500';
                    setTimeout(() => window.location.href = '/', 1500);
                } else {
                    msgDiv.className = 'mt-4 text-center text-sm font-medium text-red-500';
                }
            } catch (error) {
                console.error('Erro:', error);
            }
        });
    </script>
</body>
</html>
