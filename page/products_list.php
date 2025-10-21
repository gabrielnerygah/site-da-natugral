<?php
// Requer o arquivo de funções e componentes
require_once 'functions.php';
// Nota: assumindo que render_product_card está no functions.php ou index.php para simplicidade

// Parâmetros de URL
$category = $_GET['page'] ?? 'home';
$titles = [
    'abelhas' => 'Abelhas Sem Ferrão (Meliponicultura)',
    'mel' => 'Mel Puro e Derivados (Meliponas e Apis)',
    'marcenaria' => 'Marcenaria Artesanal (Brinquedos e Móveis)',
];

// Garante que o título e a categoria (string minúscula) sejam corretos
$title = $titles[$category] ?? 'Nossos Produtos';
$products = get_products($category);
?>

<main class="flex-grow max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
    <h1 class="text-5xl font-extrabold text-natugral-brown mb-4 border-b-4 border-natugral-yellow pb-2"><?php echo $title; ?></h1>
    <p class="text-lg text-gray-600 mb-10">Explore nossa seleção de produtos artesanais e naturais, colhidos e criados com o máximo respeito pela natureza.</p>

    <?php if (empty($products)): ?>
        <div class="text-center py-20 bg-white rounded-xl shadow-lg">
            <i data-lucide="package-x" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
            <p class="text-xl text-gray-600">Nenhum produto encontrado nesta categoria no momento.</p>
            <p class="text-sm text-gray-500 mt-2">Volte em breve ou <a href="index.php?page=contato" class="text-natugral-green hover:underline">entre em contato</a> para consultar disponibilidade futura.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            <?php foreach ($products as $product): ?>
                <?php 
                // Função auxiliar para renderizar o card (esta função deve estar definida no index.php ou components.php)
                render_product_card($product); 
                ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>