<?php

// Inclui a configuração
require_once 'config.php';

// Inicializa a sessão para controle de login
session_start();

// ===============================================
// FUNÇÕES DE CONEXÃO E AJUDA
// ===============================================

/**
 * Conecta ao banco de dados.
 * @return mysqli|false
 */
function db_connect() {
    $conn = false;
    try {
        // Tenta a conexão, usando o operador @ para suprimir warnings de rede
        $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        // Verifica a falha de conexão (incluindo o caso de DNS não resolvido)
        if ($conn->connect_error) {
            // Se a conexão falhar, retorna false em vez de dar Fatal Error
            return false;
        }
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (mysqli_sql_exception $e) {
        // Captura a exceção e retorna false, evitando a quebra do script
        return false;
    }
}

/**
 * Verifica se a conexão com o banco de dados está ativa e válida.
 * @return bool
 */
function is_db_connected() {
    global $conn;
    // Verifica se $conn é uma instância válida de mysqli e se não há erro de conexão pendente
    return $conn instanceof mysqli && !$conn->connect_error;
}

/**
 * Redireciona para uma página e encerra o script.
 * @param string $page
 */
function redirect($page) {
    header("Location: index.php?page=" . $page);
    exit; // É ESSENCIAL INCLUIR O EXIT;
}

// Conexão global (será usada nas funções). Se falhar, $conn será 'false'.
$conn = db_connect();

// ===============================================
// LÓGICA DE AUTENTICAÇÃO E ADMIN
// ===============================================

/**
 * Processa o login do administrador.
 */
function handle_login() {
    global $conn;
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
        
        // 1. Verifica primeiro se o DB está conectado
        if (!is_db_connected()) {
            $_SESSION['login_error'] = "Falha crítica: Não foi possível conectar ao banco de dados para verificar o login.";
            redirect('admin_login');
            return;
        }

        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        // DEBUG TEMPORÁRIO: Bypass do hash para testar se o problema é o dado no DB
        if ($username === 'gestor' && $password === 'natugral2025') {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['user_id'] = 1; // ID de usuário fixo (apenas para debug)
            redirect('admin_dashboard');
            return;
        }
        // FIM DO DEBUG TEMPORÁRIO
        

        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
        
        if (!$stmt) {
             // Checa se a query falhou (ex: tabela 'users' não existe)
            $_SESSION['login_error'] = "Erro do sistema: Tabela de usuários não encontrada. Execute o script database.sql.";
            redirect('admin_login');
            return;
        }
        
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            // 2. Tenta verificar a senha usando o hash
            if (password_verify($password, $user['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                // SUCESSO: Redireciona para o dashboard
                redirect('admin_dashboard'); 
            } else {
                // FALHA: Define a mensagem de erro
                $_SESSION['login_error'] = "Usuário ou senha inválidos.";
            }
        } else {
            // FALHA: Usuário não existe, define a mensagem de erro
            $_SESSION['login_error'] = "Usuário ou senha inválidos.";
        }
        $stmt->close();
        
        // 3. FALHA FINAL: Redireciona de volta para o login para mostrar o erro.
        // A mensagem de erro já foi definida acima.
        redirect('admin_login');
    }
}

/**
 * Processa o logout.
 */
function handle_logout() {
    if (isset($_GET['page']) && $_GET['page'] === 'logout') {
        session_unset();
        session_destroy();
        redirect('home');
    }
}

/**
 * Verifica se o usuário é o administrador logado.
 * @return bool
 */
function is_admin() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// ===============================================
// LÓGICA DE PRODUTOS E CONSULTAS (CRUD HELPERS)
// ===============================================

function get_products($category = null) {
    global $conn;
    if (!is_db_connected()) return [];
    
    $sql = "SELECT * FROM products";
    if ($category) {
        $sql .= " WHERE category = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query($sql);
    }
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function get_products_promo() {
    global $conn;
    if (!is_db_connected()) return [];
    
    $sql = "SELECT * FROM products WHERE is_promo = 1 AND stock > 0 ORDER BY id DESC LIMIT 3";
    $result = $conn->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function get_product_by_id($id) {
    global $conn;
    if (!is_db_connected()) return null;
    
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function get_consultations() {
    global $conn;
    if (!is_db_connected()) return [];
    
    $sql = "SELECT * FROM consultations ORDER BY status ASC, created_at DESC";
    $result = $conn->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

// ===============================================
// LÓGICA DE MANIPULAÇÃO DE DADOS (POST HANDLERS)
// ===============================================

/**
 * Processa o formulário de consulta de compra.
 */
function handle_consultation_request() {
    global $conn;
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_consultation') {
        
        if (!is_db_connected()) {
            $_SESSION['chat_error'] = "O sistema de registro de pedidos está temporariamente indisponível. Por favor, use o WhatsApp para contato imediato.";
            redirect('chat');
            return;
        }
        
        $name = $conn->real_escape_string($_POST['customer_name'] ?? '');
        $email = $conn->real_escape_string($_POST['customer_email'] ?? '');
        $whatsapp = $conn->real_escape_string($_POST['customer_whatsapp'] ?? '');
        $product_interest = $conn->real_escape_string($_POST['product_interest'] ?? 'Consulta Geral');
        $message = $conn->real_escape_string($_POST['message'] ?? '');

        $stmt = $conn->prepare("INSERT INTO consultations (customer_name, customer_email, customer_whatsapp, product_interest, message) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $whatsapp, $product_interest, $message);

        if ($stmt->execute()) {
            $_SESSION['chat_success'] = "Sua consulta foi enviada com sucesso! Entraremos em contato em breve.";
            redirect('chat');
        } else {
            $_SESSION['chat_error'] = "Erro ao registrar a consulta: " . $stmt->error;
        }
        $stmt->close();
    }
}

/**
 * Lógica de manipulação de produtos e consultas do Admin.
 */
function handle_admin_actions() {
    global $conn;
    if (!is_admin() || !is_db_connected()) return;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_action'])) {
        $action = $_POST['admin_action'];

        if ($action === 'add_product' || $action === 'edit_product') {
            $name = $conn->real_escape_string($_POST['name']);
            $description = $conn->real_escape_string($_POST['description']);
            $category = $_POST['category'];
            $price = floatval($_POST['price']);
            $stock = intval($_POST['stock']);
            $is_promo = isset($_POST['is_promo']) ? 1 : 0;
            $image_url = $conn->real_escape_string($_POST['image_url'] ?? ''); // <--- LINHA ADICIONADA
            if ($action === 'add_product') {
                $stmt = $conn->prepare("INSERT INTO products (name, description, category, price, stock, is_promo, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssddiis", $name, $description, $category, $price, $stock, $is_promo, $image_url);
            } else { // edit_product
                $id = intval($_POST['product_id']);
                $stmt = $conn->prepare("UPDATE products SET name=?, description=?, category=?, price=?, stock=?, is_promo=?, image_url=? WHERE id=?");
                $stmt->bind_param("ssddiisi", $name, $description, $category, $price, $stock, $is_promo, $image_url, $id);
            }

            if ($stmt->execute()) {
                $_SESSION['admin_msg'] = "Produto salvo com sucesso!";
            } else {
                $_SESSION['admin_msg'] = "Erro ao salvar produto: " . $conn->error;
            }
            $stmt->close();
            redirect('admin_products');
        } elseif ($action === 'delete_product') {
            $id = intval($_POST['product_id']);
            $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $_SESSION['admin_msg'] = "Produto excluído!";
            $stmt->close();
            redirect('admin_products');
        } elseif ($action === 'update_consultation_status') {
            $id = intval($_POST['consultation_id']);
            $status = $_POST['status'];
            $stmt = $conn->prepare("UPDATE consultations SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $id);
            $stmt->execute();
            $_SESSION['admin_msg'] = "Status da consulta atualizado para: " . $status;
            $stmt->close();
            redirect('admin_consultations');
        }
    }
}

// Processa as ações
handle_login();
handle_logout();
handle_consultation_request();
handle_admin_actions();
