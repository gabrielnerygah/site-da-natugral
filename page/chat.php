<?php
// Requer o arquivo de funções e componentes
require_once 'functions.php';

$product_interest = $_GET['product'] ?? 'Consulta Geral (Sem produto específico)';

// Mensagens de feedback
$feedback_class = '';
$feedback_message = '';
if (isset($_SESSION['chat_success'])) {
    $feedback_class = 'bg-green-100 border-green-400 text-green-700';
    $feedback_message = $_SESSION['chat_success'];
    unset($_SESSION['chat_success']);
} elseif (isset($_SESSION['chat_error'])) {
    $feedback_class = 'bg-red-100 border-red-400 text-red-700';
    $feedback_message = $_SESSION['chat_error'];
    unset($_SESSION['chat_error']);
}

$db_warning = !is_db_connected();
?>
<main class="flex-grow max-w-xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
    <div class="bg-white p-8 rounded-xl shadow-2xl border-t-8 border-natugral-green">
        <h1 class="text-4xl font-extrabold text-natugral-brown mb-2">Finalizar Pedido por Chat</h1>
        <p class="text-lg text-gray-600 mb-6">Devido à natureza dos nossos produtos (como abelhas e logística de marcenaria), o fechamento é feito diretamente com o administrador para garantir o melhor atendimento e frete.</p>

        <?php if ($db_warning): ?>
            <div class="border px-4 py-3 rounded relative mb-4 bg-red-100 border-red-400 text-red-700">
                <p class="font-bold">Aviso:</p>
                <p>O registro de consultas por formulário está indisponível (DB Desconectado). Use o **WhatsApp** abaixo para contato imediato.</p>
            </div>
        <?php elseif ($feedback_message): ?>
            <div class="border px-4 py-3 rounded relative mb-4 <?php echo $feedback_class; ?>">
                <p class="font-bold"><?php echo $feedback_message; ?></p>
            </div>
        <?php endif; ?>

        <form action="index.php?page=chat" method="POST" class="space-y-4">
            <input type="hidden" name="action" value="submit_consultation">

            <!-- Produto de Interesse (Pré-preenchido) -->
            <div class="p-3 bg-natugral-light rounded-lg border-l-4 border-natugral-yellow">
                <label class="block text-sm font-medium text-natugral-green">Produto de Interesse:</label>
                <input type="text" name="product_interest" value="<?php echo htmlspecialchars($product_interest); ?>" readonly class="w-full p-1 bg-natugral-light border-0 font-bold text-lg">
            </div>

            <input type="text" name="customer_name" placeholder="Seu Nome Completo" required class="w-full p-3 border border-gray-300 rounded-lg focus:border-natugral-green" <?= $db_warning ? 'disabled' : '' ?>>
            <input type="email" name="customer_email" placeholder="Seu E-mail" required class="w-full p-3 border border-gray-300 rounded-lg focus:border-natugral-green" <?= $db_warning ? 'disabled' : '' ?>>
            <input type="text" name="customer_whatsapp" placeholder="WhatsApp (DDD + Número) - Preferencial" required class="w-full p-3 border border-gray-300 rounded-lg focus:border-natugral-green" <?= $db_warning ? 'disabled' : '' ?>>
            <textarea name="message" rows="4" placeholder="Detalhes (Ex: 'Gostaria de 2 potes de mel Jataí e o cavalinho de madeira')" required class="w-full p-3 border border-gray-300 rounded-lg focus:border-natugral-green" <?= $db_warning ? 'disabled' : '' ?>></textarea>

            <button type="submit" class="w-full bg-natugral-green text-white font-bold py-3 rounded-lg shadow-md hover:bg-natugral-brown transition duration-300 flex items-center justify-center" <?= $db_warning ? 'disabled' : '' ?>>
                <i data-lucide="send" class="w-5 h-5 mr-2"></i> Enviar Pedido de Consulta
            </button>
        </form>

        <div class="mt-8 pt-4 border-t text-center">
            <p class="text-gray-600 mb-3">Ou se preferir, chame diretamente no WhatsApp:</p>
            <!-- Botão WhatsApp direto com mensagem pré-preenchida -->
            <a href="https://wa.me/5588999999999?text=Ol%C3%A1%2C%20tenho%20interesse%20em%20consultar%20a%20compra%20do%20produto%3A%20<?php echo urlencode($product_interest); ?>"
               target="_blank" class="inline-flex items-center bg-green-500 text-white font-bold py-3 px-6 rounded-full shadow-lg hover:bg-green-600 transition duration-300">
                <i data-lucide="message-circle" class="w-6 h-6 mr-2"></i> Chamar no WhatsApp
            </a>
        </div>
    </div>
</main>
