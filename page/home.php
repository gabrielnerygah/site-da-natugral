<?php
// Requer o arquivo de funções e componentes
require_once 'functions.php';


$promo_products = get_products_promo();
?>
<main class="flex-grow">
    <!-- Banner Principal -->
    <div class="relative bg-natugral-green/90 text-white pt-24 pb-32 overflow-hidden shadow-2xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h1 class="text-6xl font-extrabold tracking-tight sm:text-7xl lg:text-8xl text-natugral-yellow drop-shadow-lg">
                NATUGRAL
            </h1>
            <p class="mt-6 text-xl text-natugral-light max-w-3xl mx-auto">
                Abelhas Nativas, Mel Puro e Marcenaria Artesanal: Cultivando o Natural, Criando com Propósito.
            </p>
            <div class="mt-10 flex flex-col sm:flex-row justify-center space-y-4 sm:space-y-0 sm:space-x-6">
                <a href="index.php?page=abelhas" class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-full text-natugral-green bg-natugral-yellow hover:bg-yellow-500 shadow-xl transition duration-300 transform hover:scale-105">
                    Conheça Nossas Abelhas
                </a>
                <a href="index.php?page=marcenaria" class="inline-flex items-center justify-center px-8 py-3 border-2 border-natugral-light text-base font-medium rounded-full text-white hover:bg-natugral-brown/30 shadow-xl transition duration-300 transform hover:scale-105">
                    Ver Itens de Marcenaria
                </a>
            </div>
        </div>
         <!-- Detalhe da Logo (Para um toque visual) -->
        <div class="absolute inset-0 opacity-5 flex items-center justify-center">
            <img src="<?= LOGO_FILENAME ?>" onerror="this.onerror=null;this.src='https://placehold.co/300x300/187A4F/FFC300?text=N';" alt="Logo Natugral Detalhe" class="w-72 h-72 rounded-full object-cover">
        </div>
    </div>

    <!-- Seção: Nossos Pilares -->
    <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl font-extrabold text-natugral-brown text-center mb-12">Nossos Pilares de Criação</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-xl shadow-md text-center border-t-4 border-natugral-yellow">
                <i data-lucide="sun" class="w-12 h-12 text-natugral-yellow mx-auto mb-4"></i>
                <h3 class="text-2xl font-bold text-natugral-green mb-2">Abelhas Nativas</h3>
                <p class="text-gray-600">Preservação e meliponicultura: oferecemos colônias saudáveis de abelhas sem ferrão (Jataí, Mandaçaia e mais) e todo o conhecimento para manejo.</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-md text-center border-t-4 border-natugral-yellow">
                <i data-lucide="bot-message-square" class="w-12 h-12 text-natugral-yellow mx-auto mb-4"></i>
                <h3 class="text-2xl font-bold text-natugral-green mb-2">Sabor Genuíno</h3>
                <p class="text-gray-600">Mel puro das melhores floradas. Sabores raros de meliponas e o tradicional apis. O alimento mais completo da natureza, direto para sua mesa.</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-md text-center border-t-4 border-natugral-yellow">
                <i data-lucide="hammer" class="w-12 h-12 text-natugral-yellow mx-auto mb-4"></i>
                <h3 class="text-2xl font-bold text-natugral-green mb-2">Arte em Madeira</h3>
                <p class="text-gray-600">Marcenaria artesanal com foco em utilidade e estética. Brinquedos educativos, móveis decorativos e caixas de abelhas robustas.</p>
            </div>
        </div>
    </div>

    <!-- Seção: Produtos em Destaque (Promoções) -->
    <?php if (!empty($promo_products)): ?>
    <div class="bg-natugral-green py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-extrabold text-natugral-yellow text-center mb-12">Destaques da Semana!</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($promo_products as $product): ?>
                    <?php render_product_card($product); ?>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-10">
                <a href="index.php?page=mel" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-full text-natugral-green bg-white hover:bg-natugral-light shadow-xl transition duration-300">
                    Ver Todos os Produtos
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>
</main>
