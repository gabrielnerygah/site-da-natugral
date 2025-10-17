<?php 
// Verifica se o DB está conectado para exibir um alerta no front-end
$db_error = !is_db_connected();

if (is_admin()) {
    redirect('admin_dashboard');
}
?>

<main class="flex-grow flex items-center justify-center py-16 px-4">
    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-2xl border-t-8 border-natugral-green">
        <h1 class="text-3xl font-bold text-center text-natugral-green mb-6">Login Administrativo</h1>
        <p class="text-sm text-center text-gray-500 mb-8">Acesso restrito para gestão de produtos e pedidos.</p>

        <?php if ($db_error): ?>
            <!-- ALERTA DE ERRO DE CONEXÃO CRÍTICA -->
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p class="font-bold">ERRO DE CONEXÃO CRÍTICA</p>
                <p>O site não conseguiu se conectar ao banco de dados. Verifique as credenciais no arquivo <code>config.php</code> ou, se estiver testando localmente, suba os arquivos para a hospedagem (InfinityFree).</p>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['login_error'])): ?>
            <!-- ERRO DE CREDENCIAIS (Usuário/Senha) -->
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p class="font-bold">Falha no Login</p>
                <p><?php echo $_SESSION['login_error']; unset($_SESSION['login_error']); ?></p>
            </div>
        <?php endif; ?>

        <form action="index.php" method="POST" class="space-y-4">
            <input type="hidden" name="action" value="login">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Usuário:</label>
                <input type="text" id="username" name="username" required class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:border-natugral-green focus:ring focus:ring-natugral-green/50">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Senha:</label>
                <input type="password" id="password" name="password" required class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:border-natugral-green focus:ring focus:ring-natugral-green/50">
            </div>
            <button type="submit" class="w-full bg-natugral-green text-white font-bold py-3 rounded-lg hover:bg-natugral-brown transition duration-300" 
                <?php echo $db_error ? 'disabled' : ''; // Desabilita o botão se a conexão falhou ?>>
                <i data-lucide="log-in" class="w-5 h-5 mr-2 inline-block"></i> Entrar
            </button>
        </form>
        <p class="mt-6 text-center text-sm text-gray-500">
            Tente: gestor / natugral2025
        </p>
    </div>
</main>

<script>lucide.createIcons();</script>
