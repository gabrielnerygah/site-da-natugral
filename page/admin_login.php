<?php
// Requer o arquivo de funções e componentes
require_once 'functions.php';


if (is_admin()) {
    redirect('admin_dashboard');
}

$db_connected = is_db_connected();
?>
<main class="flex-grow flex items-center justify-center py-16 px-4">
    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-2xl border-t-8 border-natugral-green">
        <h1 class="text-3xl font-bold text-center text-natugral-green mb-6">Login Administrativo</h1>
        <p class="text-sm text-center text-gray-500 mb-8">Acesso restrito para gestão de produtos e pedidos.</p>

        <?php if (isset($_SESSION['login_error'])): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p class="font-bold">Erro de Login</p>
                <p><?php echo $_SESSION['login_error']; unset($_SESSION['login_error']); ?></p>
            </div>
        <?php endif; ?>
        
        <?php if (!$db_connected): ?>
             <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p class="font-bold">Aviso</p>
                <p>O Banco de Dados não está conectado. O login e as áreas administrativas não funcionarão até que o site seja movido para a hospedagem real (InfinityFree).</p>
            </div>
        <?php endif; ?>

        <form action="index.php" method="POST" class="space-y-4">
            <input type="hidden" name="action" value="login">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Usuário:</label>
                <input type="text" id="username" name="username" required class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:border-natugral-green focus:ring focus:ring-natugral-green/50" <?= !$db_connected ? 'disabled' : '' ?>>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Senha:</label>
                <input type="password" id="password" name="password" required class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:border-natugral-green focus:ring focus:ring-natugral-green/50" <?= !$db_connected ? 'disabled' : '' ?>>
            </div>
            <button type="submit" class="w-full bg-natugral-green text-white font-bold py-3 rounded-lg hover:bg-natugral-brown transition duration-300" <?= !$db_connected ? 'disabled' : '' ?>>
                Entrar
            </button>
        </form>
        <p class="mt-6 text-center text-sm text-gray-500">Credenciais iniciais: admin / admin123</p>
    </div>
</main>
