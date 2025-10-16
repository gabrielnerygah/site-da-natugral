<?php
// Requer o arquivo de funções e componentes
require_once 'functions.php';


if (!is_admin()) {
    redirect('admin_login');
}

$db_connected = is_db_connected();
global $conn;

// Obter dados básicos para o dashboard
$total_products = $db_connected ? $conn->query("SELECT COUNT(*) FROM products")->fetch_row()[0] : 'N/A';
$pending_consultations = $db_connected ? $conn->query("SELECT COUNT(*) FROM consultations WHERE status = 'Pendente'")->fetch_row()[0] : 'N/A';
$low_stock_count = $db_connected ? $conn->query("SELECT COUNT(*) FROM products WHERE stock < 5 AND stock > 0")->fetch_row()[0] : 'N/A';

// Mensagens de feedback do admin
$admin_msg = '';
if (isset($_SESSION['admin_msg'])) {
    $admin_msg = $_SESSION['admin_msg'];
    unset($_SESSION['admin_msg']);
}

?>
<main class="flex-grow max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8 admin-only">
    <h1 class="text-4xl font-extrabold text-natugral-green mb-8 border-b-4 border-natugral-yellow pb-2">Painel de Administração</h1>

    <?php if ($admin_msg): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p class="font-bold">Sucesso!</p>
            <p><?php echo $admin_msg; ?></p>
        </div>
    <?php endif; ?>
    
    <?php if (!$db_connected): ?>
         <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p class="font-bold">ATENÇÃO!</p>
            <p>O Banco de Dados está desconectado. Os dados exibidos (N/A) e a gestão de produtos/consultas só funcionarão quando o site estiver no servidor do InfinityFree.</p>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <!-- Card de Produtos -->
        <a href="index.php?page=admin_products" class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition duration-300 border-l-8 border-natugral-brown flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium text-gray-500">Produtos Cadastrados</h3>
                <p class="text-4xl font-extrabold text-natugral-brown"><?php echo $total_products; ?></p>
            </div>
            <i data-lucide="package" class="w-12 h-12 text-natugral-brown/50"></i>
        </a>

        <!-- Card de Consultas Pendentes -->
        <a href="index.php?page=admin_consultations" class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition duration-300 border-l-8 border-red-500 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium text-gray-500">Consultas Pendentes</h3>
                <p class="text-4xl font-extrabold text-red-600"><?php echo $pending_consultations; ?></p>
            </div>
            <i data-lucide="bell" class="w-12 h-12 text-red-500/50"></i>
        </a>

        <!-- Card de Estoque Baixo -->
        <a href="index.php?page=admin_products" class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition duration-300 border-l-8 border-orange-500 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-medium text-gray-500">Itens em Estoque Baixo</h3>
                <p class="text-4xl font-extrabold text-orange-600"><?php echo $low_stock_count; ?></p>
            </div>
            <i data-lucide="alert-triangle" class="w-12 h-12 text-orange-500/50"></i>
        </a>
    </div>

    <!-- Links Rápidos -->
    <h2 class="text-2xl font-bold text-natugral-brown mb-4">Ações Rápidas</h2>
    <div class="flex space-x-4">
        <a href="index.php?page=admin_products" class="px-4 py-2 bg-natugral-yellow text-natugral-brown font-semibold rounded-full hover:bg-natugral-yellow/80 transition duration-300 flex items-center">
            <i data-lucide="edit" class="w-5 h-5 mr-2"></i> Gerenciar Produtos
        </a>
        <a href="index.php?page=admin_consultations" class="px-4 py-2 bg-natugral-green text-white font-semibold rounded-full hover:bg-natugral-brown transition duration-300 flex items-center">
            <i data-lucide="message-square-text" class="w-5 h-5 mr-2"></i> Ver Consultas (Chat)
        </a>
    </div>
</main>
