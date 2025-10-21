<?php
// Requer o arquivo de funções e componentes
require_once 'functions.php';


if (!is_admin()) {
    redirect('admin_login');
}

$db_connected = is_db_connected();
$products = get_products(null);
$edit_product = null;
if (isset($_GET['edit_id'])) {
    $edit_product = get_product_by_id(intval($_GET['edit_id']));
}

$admin_msg = '';
if (isset($_SESSION['admin_msg'])) {
    $admin_msg = $_SESSION['admin_msg'];
    unset($_SESSION['admin_msg']);
}

$categories = ['abelhas' => 'Abelhas Sem Ferrão', 'mel' => 'Mel e Derivados', 'marcenaria' => 'Marcenaria'];
?>
<main class="flex-grow max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8 admin-only">
    <h1 class="text-4xl font-extrabold text-natugral-green mb-8 border-b-4 border-natugral-yellow pb-2">Gestão de Produtos</h1>

    <?php if (!$db_connected): ?>
         <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p class="font-bold">ATENÇÃO!</p>
            <p>O Banco de Dados está desconectado. A edição e listagem de produtos só funcionarão quando o site for carregado no servidor do InfinityFree.</p>
        </div>
    <?php endif; ?>

    <?php if ($admin_msg): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p><?php echo $admin_msg; ?></p>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Formulário de Adição/Edição -->
        <div class="lg:col-span-1 bg-white p-6 rounded-xl shadow-xl h-fit sticky top-20">
            <h2 class="text-2xl font-bold text-natugral-brown mb-4"><?php echo $edit_product ? 'Editar Produto: ' . htmlspecialchars($edit_product['name']) : 'Adicionar Novo Produto'; ?></h2>
            <form action="index.php" method="POST" class="space-y-4">
                <input type="hidden" name="admin_action" value="<?php echo $edit_product ? 'edit_product' : 'add_product'; ?>">
                <?php if ($edit_product): ?>
                    <input type="hidden" name="product_id" value="<?php echo $edit_product['id']; ?>">
                <?php endif; ?>

                <input type="text" name="name" placeholder="Nome do Produto" value="<?php echo htmlspecialchars($edit_product['name'] ?? ''); ?>" required class="w-full p-3 border rounded-lg focus:border-natugral-green" <?= !$db_connected ? 'disabled' : '' ?>>
                <textarea type="text" name="description" rows="3" placeholder="Descrição Detalhada" required class="w-full p-3 border rounded-lg focus:border-natugral-green" <?= !$db_connected ? 'disabled' : '' ?>><?php echo htmlspecialchars($edit_product['description'] ?? ''); ?></textarea>
                
                <select name="category" required class="w-full p-3 border rounded-lg focus:border-natugral-green" <?= !$db_connected ? 'disabled' : '' ?>>
                    <option value="">Selecione a Categoria</option>
                    <?php foreach ($categories as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php echo (isset($edit_product) && $edit_product['category'] == $value) ? 'selected' : ''; ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="url" name="image_url" placeholder="URL da Imagem (Link)" value="<?php echo htmlspecialchars($edit_product['image_url'] ?? ''); ?>" class="w-full p-3 border rounded-lg focus:border-natugral-green" <?= !$db_connected ? 'disabled' : '' ?>>
                <p class="text-xs text-gray-500 mt-1">Dica: Para imagens locais, suba via FTP e use o caminho: /pasta/nome_do_arquivo.jpg</p>
                <input type="number" name="price" placeholder="Preço (0.00)" step="0.01" min="0" value="<?php echo htmlspecialchars($edit_product['price'] ?? ''); ?>" required class="w-full p-3 border rounded-lg focus:border-natugral-green" <?= !$db_connected ? 'disabled' : '' ?>>
                <input type="number" name="stock" placeholder="Estoque" min="0" value="<?php echo htmlspecialchars($edit_product['stock'] ?? ''); ?>" required class="w-full p-3 border rounded-lg focus:border-natugral-green" <?= !$db_connected ? 'disabled' : '' ?>>
                
                <div class="flex items-center">
                    <input type="checkbox" id="is_promo" name="is_promo" value="1" <?php echo (isset($edit_product) && $edit_product['is_promo']) ? 'checked' : ''; ?> class="w-4 h-4 text-natugral-green border-gray-300 rounded focus:ring-natugral-green" <?= !$db_connected ? 'disabled' : '' ?>>
                    <label for="is_promo" class="ml-2 text-sm font-medium text-gray-700">Item em Promoção/Destaque</label>
                </div>

                <button type="submit" class="w-full bg-natugral-green text-white font-bold py-3 rounded-lg hover:bg-natugral-brown transition duration-300" <?= !$db_connected ? 'disabled' : '' ?>>
                    <?php echo $edit_product ? 'Salvar Edição' : 'Adicionar Produto'; ?>
                </button>
                <?php if ($edit_product): ?>
                    <a href="index.php?page=admin_products" class="block w-full text-center mt-2 text-sm text-gray-600 hover:text-red-500">Cancelar Edição</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Tabela de Produtos Existentes -->
        <div class="lg:col-span-2 overflow-x-auto">
            <h2 class="text-2xl font-bold text-natugral-brown mb-4">Lista de Produtos (<?php echo count($products); ?>)</h2>
            <table class="min-w-full bg-white rounded-xl shadow-xl">
                <thead class="bg-natugral-yellow text-natugral-brown">
                    <tr>
                        <th class="py-3 px-4 text-left">Nome</th>
                        <th class="py-3 px-4 text-left">Categoria</th>
                        <th class="py-3 px-4 text-right">Preço</th>
                        <th class="py-3 px-4 text-center">Estoque</th>
                        <th class="py-3 px-4 text-center">Promo</th>
                        <th class="py-3 px-4 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($db_connected): ?>
                        <?php foreach ($products as $p): ?>
                        <tr class="border-b hover:bg-gray-50 <?php echo $p['stock'] < 5 ? 'bg-orange-100/50' : ''; ?>">
                            <td class="py-3 px-4 font-semibold text-gray-800"><?php echo htmlspecialchars($p['name']); ?></td>
                            <td class="py-3 px-4 text-gray-600"><?php echo htmlspecialchars($categories[$p['category']] ?? $p['category']); ?></td>
                            <td class="py-3 px-4 text-right text-natugral-green">R$ <?php echo number_format($p['price'], 2, ',', '.'); ?></td>
                            <td class="py-3 px-4 text-center <?php echo $p['stock'] < 5 && $p['stock'] > 0 ? 'text-orange-500 font-bold' : ($p['stock'] == 0 ? 'text-red-600 font-bold' : ''); ?>">
                                <?php echo $p['stock']; ?>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <?php if ($p['is_promo']): ?>
                                    <i data-lucide="zap" class="w-5 h-5 text-red-500 mx-auto"></i>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 text-center flex space-x-2 justify-center">
                                <a href="index.php?page=admin_products&edit_id=<?php echo $p['id']; ?>" class="text-blue-500 hover:text-blue-700 transition">
                                    <i data-lucide="pencil" class="w-5 h-5"></i>
                                </a>
                                <form action="index.php" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir <?php echo htmlspecialchars($p['name']); ?>?');" class="inline">
                                    <input type="hidden" name="admin_action" value="delete_product">
                                    <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                                    <button type="submit" class="text-red-500 hover:text-red-700 transition">
                                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="py-4 px-4 text-center text-gray-500">Nenhum produto carregado (Banco de Dados desconectado).</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
