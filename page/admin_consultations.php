<?php
// Requer o arquivo de funções e componentes
require_once 'functions.php';


if (!is_admin()) {
    redirect('admin_login');
}

$db_connected = is_db_connected();
$consultations = get_consultations();
$admin_msg = '';
if (isset($_SESSION['admin_msg'])) {
    $admin_msg = $_SESSION['admin_msg'];
    unset($_SESSION['admin_msg']);
}

$status_map = [
    'Pendente' => 'bg-red-500',
    'Em Contato' => 'bg-orange-500',
    'Vendido' => 'bg-green-500',
    'Cancelado' => 'bg-gray-500'
];
?>
<main class="flex-grow max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8 admin-only">
    <h1 class="text-4xl font-extrabold text-natugral-green mb-8 border-b-4 border-natugral-yellow pb-2">Consultas de Pedidos (Chat)</h1>

    <?php if (!$db_connected): ?>
         <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p class="font-bold">ATENÇÃO!</p>
            <p>O Banco de Dados está desconectado. A lista de consultas só estará disponível quando o site for carregado no servidor do InfinityFree.</p>
        </div>
    <?php endif; ?>

    <?php if ($admin_msg): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p><?php echo $admin_msg; ?></p>
        </div>
    <?php endif; ?>

    <?php if (empty($consultations) && $db_connected): ?>
        <div class="text-center py-20 bg-white rounded-xl shadow-lg">
            <i data-lucide="check-circle" class="w-16 h-16 text-natugral-green mx-auto mb-4"></i>
            <p class="text-xl text-gray-600">Nenhuma consulta pendente ou registrada no momento.</p>
        </div>
    <?php elseif (!$db_connected): ?>
        <!-- Conteúdo estático de aviso já exibido acima -->
    <?php else: ?>
        <div class="overflow-x-auto shadow-xl rounded-xl">
            <table class="min-w-full bg-white">
                <thead class="bg-natugral-yellow text-natugral-brown">
                    <tr>
                        <th class="py-3 px-4 text-left">Cliente</th>
                        <th class="py-3 px-4 text-left">Interesse</th>
                        <th class="py-3 px-4 text-left">Mensagem</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Data</th>
                        <th class="py-3 px-4 text-center">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($consultations as $c): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4">
                            <p class="font-semibold"><?php echo htmlspecialchars($c['customer_name']); ?></p>
                            <a href="mailto:<?php echo htmlspecialchars($c['customer_email']); ?>" class="text-sm text-blue-500 hover:underline"><?php echo htmlspecialchars($c['customer_email']); ?></a>
                            <p class="text-sm text-gray-600 flex items-center"><i data-lucide="whatsapp" class="w-4 h-4 mr-1 text-green-500"></i> <?php echo htmlspecialchars($c['customer_whatsapp']); ?></p>
                        </td>
                        <td class="py-3 px-4 font-medium text-natugral-green"><?php echo htmlspecialchars($c['product_interest']); ?></td>
                        <td class="py-3 px-4 text-sm text-gray-500 max-w-xs overflow-hidden truncate hover:overflow-visible hover:whitespace-normal"><?php echo htmlspecialchars($c['message']); ?></td>
                        <td class="py-3 px-4 text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold text-white <?php echo $status_map[$c['status']] ?? 'bg-gray-400'; ?>">
                                <?php echo $c['status']; ?>
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center text-sm text-gray-500"><?php echo date('d/m H:i', strtotime($c['created_at'])); ?></td>
                        <td class="py-3 px-4 text-center">
                            <form action="index.php" method="POST" class="inline-flex space-x-2">
                                <input type="hidden" name="admin_action" value="update_consultation_status">
                                <input type="hidden" name="consultation_id" value="<?php echo $c['id']; ?>">
                                <select name="status" class="p-1 border rounded-md text-sm focus:border-natugral-green" <?= !$db_connected ? 'disabled' : '' ?>>
                                    <?php foreach (array_keys($status_map) as $status): ?>
                                        <option value="<?php echo $status; ?>" <?php echo ($c['status'] === $status) ? 'selected' : ''; ?>>
                                            <?php echo $status; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="bg-natugral-green text-white p-1 rounded-md hover:bg-natugral-brown transition" <?= !$db_connected ? 'disabled' : '' ?>>
                                    <i data-lucide="refresh-ccw" class="w-5 h-5"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>
