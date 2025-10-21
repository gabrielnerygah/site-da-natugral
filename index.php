<?php

// ===============================================
// ATIVA DEBUGGING
// ===============================================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define o caminho base do projeto
define('BASE_PATH', __DIR__ . '/');

// Inclui funções e lógica
require_once BASE_PATH . 'functions.php';

// ===============================================
// FUNÇÃO DE DIAGNÓSTICO DE ARQUIVOS
// ===============================================
function run_file_diagnosis()
{
    echo '<div style="background-color: #ffe0e0; border: 1px solid red; padding: 15px; margin: 20px auto; max-width: 800px; font-family: monospace;">';
    echo '<h2>🔴 DIAGNÓSTICO CRÍTICO DE ARQUIVOS (ERRO 404)</h2>';
    echo '<p>O sistema não consegue encontrar os arquivos de página. Abaixo está a lista de arquivos PHP encontrados no diretório raiz:</p>';

    $files = glob(BASE_PATH . '*.php');
    if (empty($files)) {
        echo '<p style="color: red; font-weight: bold;">NENHUM ARQUIVO PHP ENCONTRADO! Verifique se o index.php está na pasta correta.</p>';
        echo '</div>';
        return;
    }

    echo '<ul style="list-style-type: none; padding: 0;">';
    $found_pages = [];
    foreach ($files as $file) {
        $filename = basename($file);
        echo '<li>' . htmlspecialchars($filename) . '</li>';

        // Tentativa de mapear nomes encontrados para os nomes esperados
        if (strpos($filename, 'page/') === 0) {
            $found_pages[] = $filename;
        }
    }
    echo '</ul>';

    echo '<p style="margin-top: 15px;"><strong>Arquivos de Página Encontrados:</strong> ' . implode(', ', $found_pages) . '</p>';
    echo '<p><strong>Ação:</strong> Confirme se o nome desses arquivos corresponde exatamente ao roteador abaixo.</p>';

    echo '</div>';

    // Se o diagnóstico for exibido, encerramos aqui para evitar erros de renderização
    die();
}

// ===============================================
// COMPONENTES (MOVENDO-OS PARA CIMA POR GARANTIA)
// ===============================================

/**
 * Navbar responsiva
 */
function render_navbar()
{
    $menu_items = [
        'home' => ['label' => 'Início', 'icon' => 'home'],
        'abelhas' => ['label' => 'Abelhas Sem Ferrão', 'icon' => 'sun'],
        'mel' => ['label' => 'Mel e Derivados', 'icon' => 'bot-message-square'],
        'marcenaria' => ['label' => 'Marcenaria Artesanal', 'icon' => 'hammer'],
        'sobre' => ['label' => 'Sobre Nós', 'icon' => 'leaf'],
        'contato' => ['label' => 'Contato e Localização', 'icon' => 'map-pin'],
    ];

    ?>
    <header class="sticky top-0 z-50 bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4 md:justify-start md:space-x-10">
                <!-- Logo da Natugral e Link Home -->
                <div class="flex justify-start lg:w-0 lg:flex-1">
                    <a href="index.php?page=home"
                        class="flex items-center text-natugral-green font-extrabold text-2xl tracking-wider min-w-max">
                        <img src="<?= LOGO_FILENAME ?>"
                            onerror="this.onerror=null;this.src='https://placehold.co/40x40/187A4F/FFC300?text=N';"
                            alt="Logo Natugral" class="rounded-full mr-2 w-10 h-10 object-cover">
                        NATUGRAL
                    </a>
                </div>
                <!-- Menu Desktop -->
                <nav class="hidden md:flex space-x-6 lg:space-x-8"> <!-- Aumentando o espaçamento aqui -->
                    <?php foreach ($menu_items as $url => $item): ?>
                        <a href="index.php?page=<?php echo $url; ?>"
                            class="text-base font-medium text-natugral-brown hover:text-natugral-green transition duration-150 ease-in-out flex items-center whitespace-nowrap">
                            <i data-lucide="<?php echo $item['icon']; ?>" class="w-4 h-4 mr-1"></i>
                            <?php echo $item['label']; ?>
                            </a>
                    <?php endforeach; ?>
                    <?php if (is_admin()): ?>
                        <!-- Link ADMIN visível apenas para usuários logados -->
                        <a href="index.php?page=admin_dashboard"
                            class="text-base font-medium text-natugral-yellow hover:text-natugral-brown transition duration-150 ease-in-out flex items-center whitespace-nowrap">
                            <i data-lucide="shield-half" class="w-4 h-4 mr-1"></i> ADMIN
                            </a>
                        <a href="index.php?page=logout"
                            class="text-base font-medium text-red-600 hover:text-red-800 transition duration-150 ease-in-out flex items-center whitespace-nowrap">
                            <i data-lucide="log-out" class="w-4 h-4 mr-1"></i> SAIR
                            </a>
                    <?php else: ?>
                        <a href="index.php?page=admin_login"
                            class="text-base font-medium text-gray-500 hover:text-natugral-green transition duration-150 ease-in-out flex items-center whitespace-nowrap">
                            <i data-lucide="lock" class="w-4 h-4 mr-1"></i> Login Admin
                            </a>
                    <?php endif; ?>
                    </nav>
                <!-- Botão Menu Mobile (Hamburger) -->
                <div class="-mr-2 -my-2 md:hidden">
                    <button id="mobile-menu-btn" type="button"
                        class="bg-natugral-green p-2 inline-flex items-center justify-center rounded-md text-white hover:text-natugral-yellow hover:bg-natugral-brown focus:outline-none focus:ring-2 focus:ring-inset focus:ring-natugral-yellow transition duration-150 ease-in-out">
                        <i data-lucide="menu" class="h-6 w-6"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- Menu Mobile (oculto por padrão) -->
        <nav id="mobile-menu" class="hidden md:hidden absolute w-full bg-white shadow-xl">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <?php foreach ($menu_items as $url => $item): ?>
                    <a href="index.php?page=<?php echo $url; ?>"
                        class="block px-3 py-2 rounded-md text-base font-medium text-natugral-brown hover:bg-natugral-light flex items-center">
                        <i data-lucide="<?php echo $item['icon']; ?>" class="w-5 h-5 mr-2"></i>
                        <?php echo $item['label']; ?>
                    </a>
                <?php endforeach; ?>
                <?php if (is_admin()): ?>
                    <a href="index.php?page=admin_dashboard"
                        class="block px-3 py-2 rounded-md text-base font-medium text-natugral-yellow bg-natugral-green flex items-center">
                        <i data-lucide="shield-half" class="w-5 h-5 mr-2"></i> ADMIN
                    </a>
                    <a href="index.php?page=logout"
                        class="block px-3 py-2 rounded-md text-base font-medium text-red-600 hover:bg-red-50 flex items-center">
                        <i data-lucide="log-out" class="w-5 h-5 mr-2"></i> SAIR
                    </a>
                <?php else: ?>
                    <a href="index.php?page=admin_login"
                        class="block px-3 py-2 rounded-md text-base font-medium text-gray-500 hover:bg-natugral-light flex items-center">
                        <i data-lucide="lock" class="w-5 h-5 mr-2"></i> Login Admin
                    </a>
                <?php endif; ?>
            </div>
        </nav>
        <script>
            document.getElementById('mobile-menu-btn').addEventListener('click', function () {
                var menu = document.getElementById('mobile-menu');
                menu.classList.toggle('hidden');
            });
            lucide.createIcons();
        </script>
    </header>
    <?php
}

/**
 * Footer do site
 */
function render_footer()
{
    ?>
    <footer class="mt-auto bg-natugral-brown text-natugral-light border-t border-natugral-green shadow-xl">
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <!-- Seção 1: Marca e Slogan -->
                <div>
                    <h3 class="text-xl font-bold mb-3 text-natugral-yellow">Natugral</h3>
                    <p class="text-sm">Cultivando o Natural, Criando com Propósito.</p>
                </div>
                <!-- Seção 2: Links Rápidos -->
                <div>
                    <h3 class="text-lg font-semibold mb-3">Produtos</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="index.php?page=abelhas" class="hover:text-natugral-yellow transition">Abelhas Sem
                                Ferrão</a></li>
                        <li><a href="index.php?page=mel" class="hover:text-natugral-yellow transition">Mel e Derivados</a>
                        </li>
                        <li><a href="index.php?page=marcenaria" class="hover:text-natugral-yellow transition">Marcenaria
                                Artesanal</a></li>
                    </ul>
                </div>
                <!-- Seção 3: Informações -->
                <div>
                    <h3 class="text-lg font-semibold mb-3">Empresa</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="index.php?page=sobre" class="hover:text-natugral-yellow transition">Sobre Nós</a></li>
                        <li><a href="index.php?page=contato" class="hover:text-natugral-yellow transition">Contato</a></li>
                        <li><a href="index.php?page=chat" class="hover:text-natugral-yellow transition">Fale com o
                                Vendedor</a></li>
                    </ul>
                </div>
                <!-- Seção 4: Contato -->
                <div>
                    <h3 class="text-lg font-semibold mb-3">Fale Conosco</h3>
                    <p class="text-sm flex items-center mb-2"><i data-lucide="mail" class="w-4 h-4 mr-2"></i>
                        contato@natugral.com.br</p>
                    <p class="text-sm flex items-center"><i data-lucide="phone" class="w-4 h-4 mr-2"></i> (88) 9XXXX-XXXX
                    </p>
                </div>
            </div>
            <div class="mt-8 pt-6 border-t border-natugral-green/50 text-center text-sm">
                &copy; <?php echo date('Y'); ?> Natugral. Todos os direitos reservados.
            </div>
        </div>
        <script>lucide.createIcons();</script>
    </footer>
    <?php
}

/**
 * Componente de Card de Produto.
 */
function render_product_card($product)
{
    $title_icon = [
        'abelhas' => 'sun',
        'mel' => 'bot-message-square',
        'marcenaria' => 'hammer',
    ];

    // Define a fonte da imagem, usando a URL salva ou um placeholder
    $img_src = !empty($product['image_url'])
        ? htmlspecialchars($product['image_url'])
        : "https://placehold.co/400x300/F5F5DC/7F522E?text=" . urlencode($product['name']);

    ?>
    <div
        class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition duration-300 overflow-hidden transform hover:scale-[1.02]">
        <div class="h-48 w-full bg-natugral-light flex items-center justify-center border-b border-natugral-brown/30">
            <img src="<?= $img_src ?>" alt="Imagem de <?php echo htmlspecialchars($product['name']); ?>"
                class="object-cover h-full w-full">
        </div>
        <div class="p-5">
            <h3 class="text-xl font-bold text-natugral-green mb-2 flex items-center">
                <i data-lucide="<?php echo $title_icon[$product['category']] ?? 'package'; ?>"
                    class="w-5 h-5 mr-2 text-natugral-yellow fill-natugral-yellow"></i>
                <?php echo htmlspecialchars($product['name']); ?>
            </h3>
            <p class="text-sm text-gray-600 mb-4 h-12 overflow-hidden">
                <?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

            <div class="flex justify-between items-end">
                <?php if ($product['is_promo']): ?>
                    <div class="text-left">
                        <span class="text-sm text-gray-500 line-through mr-2">R$
                            <?php echo number_format($product['price'] * 1.25, 2, ',', '.'); ?></span>
                        <span class="text-2xl font-extrabold text-red-600">R$
                            <?php echo number_format($product['price'], 2, ',', '.'); ?></span>
                        <span class="block text-xs text-red-600 font-bold">PROMOÇÃO!</span>
                    </div>
                <?php else: ?>
                    <span class="text-2xl font-extrabold text-natugral-brown">R$
                        <?php echo number_format($product['price'], 2, ',', '.'); ?></span>
                <?php endif; ?>

                <a href="index.php?page=chat&product=<?php echo urlencode($product['name']); ?>"
                    class="bg-natugral-green text-white font-bold py-2 px-4 rounded-full shadow-lg hover:bg-natugral-brown transition duration-300 flex items-center">
                    <i data-lucide="message-square-text" class="w-5 h-5 mr-2"></i> Consultar/Comprar
                </a>
            </div>
            <p class="text-xs mt-2 text-right text-gray-500">Estoque:
                <?php echo $product['stock'] > 0 ? $product['stock'] . ' unid.' : 'Esgotado'; ?></p>
        </div>
    </div>
    <?php
}

// ===============================================
// DEFINIÇÃO DE ROTAS
// ===============================================

$page = $_GET['page'] ?? 'home';

// Mapeamento de rotas para nomes de arquivos
$route_map = [
    'home' => 'page/home.php',
    'abelhas' => 'page/products_list.php',
    'mel' => 'page/products_list.php',
    'marcenaria' => 'page/products_list.php',
    'sobre' => 'page/about.php',
    'contato' => 'page/contact.php',
    'chat' => 'page/chat.php',
    'admin_login' => 'page/admin_login.php',
    'admin_dashboard' => 'page/admin_dashboard.php',
    'admin_products' => 'page/admin_products.php',
    'admin_consultations' => 'page/admin_consultations.php',
];

// Redirecionamento de segurança para Admin
if (strpos($page, 'admin_') !== false && !is_admin() && $page !== 'admin_login') {
    redirect('admin_login');
}

if ($page === 'logout') {
    handle_logout(); // Ação é tratada em functions.php e redireciona
}

// Verifica se a página existe no mapeamento
$file_to_include = $route_map[$page] ?? null;

// ===============================================
// LAYOUT HTML
// ===============================================

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Natugral: Mel, Abelhas e Marcenaria Artesanal</title>
    <!-- Favicon (Ícone do Site) -->
    <link rel="icon" type="image/jpeg" href="<?= LOGO_FILENAME ?>">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'natugral-green': '#187A4F', // Verde Principal
                        'natugral-brown': '#7F522E', // Marrom Madeira
                        'natugral-yellow': '#FFC300', // Amarelo Mel
                        'natugral-light': '#F5F5DC', // Bege
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <!-- Incluir a biblioteca de ícones Lucide para um design moderno -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        .map-container {
            width: 100%;
            height: 400px;
        }

        .admin-only {
            display: none;
        }

        <?php if (is_admin()): ?>
            .admin-only {
                display: block;
            }

        <?php endif; ?>
    </style>
</head>

<body class="bg-natugral-light min-h-screen flex flex-col font-sans">

    <?php
    render_navbar();
    ?>

    <?php
    // Lógica de inclusão da página de conteúdo
    if ($file_to_include && file_exists(BASE_PATH . $file_to_include)) {
        // CORREÇÃO CRÍTICA: Uso do BASE_PATH para garantir o caminho absoluto do arquivo
        require BASE_PATH . $file_to_include;
    } else {
        // Se a inclusão falhar, rodamos o diagnóstico para ver o nome real dos arquivos
        run_file_diagnosis();
        // Este die() está dentro da função de diagnóstico, mas para a segurança do script:
        echo '<main class="flex-grow max-w-xl mx-auto py-16 px-4 sm:px-6 lg:px-8 text-center">';
        echo '<i data-lucide="frown" class="w-16 h-16 text-red-500 mx-auto mb-4"></i>';
        echo '<h1 class="text-4xl font-bold text-red-600 mb-4">404 - Página Não Encontrada</h1>';
        echo '<p class="text-gray-600">O endereço que você procurou não existe. <a href="index.php?page=home" class="text-natugral-green hover:underline">Voltar à Home</a></p>';
        echo '</main>';
    }

    ?>

    <?php
    render_footer();

    // Fecha a conexão com o banco de dados (se aberta)
    if ($conn) {
        $conn->close();
    }
    ?>

    <script>lucide.createIcons();</script>

</body>

</html>