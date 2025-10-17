<?php
// Requer o arquivo de funções e componentes
require_once 'functions.php';
?>
<main class="flex-grow max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
    <h1 class="text-5xl font-extrabold text-natugral-brown mb-8 border-b-4 border-natugral-yellow pb-2">Contato e Localização</h1>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        <!-- Coluna de Contato -->
        <div class="bg-white p-8 rounded-xl shadow-lg">
            <h2 class="text-3xl font-semibold text-natugral-green mb-6">Fale Conosco Diretamente</h2>
            <div class="space-y-4 text-lg">
                <div class="flex items-center">
                    <i data-lucide="map-pin" class="w-6 h-6 mr-3 text-natugral-yellow"></i>
                    <p class="text-gray-700 font-semibold">Endereço:</p>
                    <p class="ml-2 text-natugral-brown">R. Virgílio Arrais, 215 - Crato - CE, 63109-120</p>
                </div>
                <div class="flex items-center">
                    <i data-lucide="phone" class="w-6 h-6 mr-3 text-natugral-yellow"></i>
                    <p class="text-gray-700 font-semibold">Telefone/WhatsApp:</p>
                    <a href="https://wa.me/558899998-0476" target="_blank" class="ml-2 text-natugral-green hover:underline">(88)99998-0476</a>
                </div>
                <div class="flex items-center">
                    <i data-lucide="mail" class="w-6 h-6 mr-3 text-natugral-yellow"></i>
                    <p class="text-gray-700 font-semibold">E-mail:</p>
                    <a href="mailto:natugral6@gmail.com" class="ml-2 text-natugral-green hover:underline">natugral6@gmail.com</a>
                </div>
            </div>

            <div class="mt-8 border-t pt-6">
                <h3 class="text-2xl font-semibold text-natugral-brown mb-4">Ou Envie uma Mensagem:</h3>
                <!-- Formulário de Contato Básico (não usa o DB de consultas) -->
                <form action="index.php?page=contato" method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="submit_contact">
                    <input type="text" name="name" placeholder="Seu Nome" required class="w-full p-3 border border-gray-300 rounded-lg focus:border-natugral-green">
                    <input type="email" name="email" placeholder="Seu E-mail" required class="w-full p-3 border border-gray-300 rounded-lg focus:border-natugral-green">
                    <input type="text" name="subject" placeholder="Assunto" required class="w-full p-3 border border-gray-300 rounded-lg focus:border-natugral-green">
                    <textarea name="message" rows="4" placeholder="Sua Mensagem" required class="w-full p-3 border border-gray-300 rounded-lg focus:border-natugral-green"></textarea>
                    <button type="submit" class="w-full bg-natugral-green text-white font-bold py-3 rounded-lg hover:bg-natugral-brown transition duration-300">
                        Enviar Mensagem
                    </button>
                </form>
                <?php 
                // Lógica de feedback de envio de contato (simples)
                ?>
            </div>
        </div>

        <!-- Coluna do Mapa (Google Store Locator) -->
        <div class="map-container rounded-xl shadow-lg overflow-hidden">
            <h2 class="text-xl font-bold text-natugral-green p-3 bg-natugral-light border-b">Onde Estamos (Crato, CE)</h2>
            <style>
                .map-container > gmpx-store-locator {
                    width: 100%;
                    height: 100%;
                    --gmpx-color-primary: #187A4F;
                    --gmpx-fixed-panel-width-row-layout: 28.5em;
                    --gmpx-fixed-panel-height-column-layout: 65%;
                    --gmpx-font-family-base: "Inter", sans-serif;
                }
            </style>
            <script>
                const CONFIGURATION_MAP = {
                    "locations": [
                        {"title":"Natugral","address1":"R. Virgílio Arrais","address2":"215 - Granjeiro, Crato - CE, 63109-120, Brasil","coords":{"lat":-7.27128731440225,"lng":-39.43568002023773},"placeId":"ChIJpeXcIyWboQcRdddOwHNmH2w"}
                    ],
                    "mapOptions": {"center":{"lat":-7.271287,"lng":-39.435680},"fullscreenControl":true,"mapTypeControl":false,"streetViewControl":false,"zoom":14,"zoomControl":true,"maxZoom":17,"mapId":""},
                    "mapsApiKey": "YOUR_API_KEY_HERE", // CHAVE A SER INSERIDA
                    "capabilities": {"input":true,"autocomplete":true,"directions":true,"distanceMatrix":true,"details":true,"actions":false}
                };

                document.addEventListener('DOMContentLoaded', async () => {
                    const locator = document.querySelector('gmpx-store-locator');
                    if (locator) {
                        await customElements.whenDefined('gmpx-store-locator');
                        locator.configureFromQuickBuilder(CONFIGURATION_MAP);
                    }
                });
            </script>
            <script type="module" src="https://ajax.googleapis.com/ajax/libs/@googlemaps/extended-component-library/0.6.11/index.min.js"></script>
            <gmpx-api-loader key="YOUR_API_KEY_HERE" solution-channel="GMP_QB_locatorplus_v11_cABCDE"></gmpx-api-loader>
            <gmpx-store-locator map-id="DEMO_MAP_ID" class="h-full"></gmpx-store-locator>
        </div>
    </div>
</main>
