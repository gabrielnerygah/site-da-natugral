<?php

// Inclui a configuração
require_once 'config.php';

// Inicializa a sessão para controle de login (movido para aqui)
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
        // Tenta a conexão, suprimindo o erro de rede (Warning) com o operador @
        // e capturando a exceção (Fatal Error) para evitar a interrupção do script.
        $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        // Verifica a falha de conexão (incluindo o caso de DNS não resolvido)
        if ($conn->connect_error) {
            return false;
        }
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (mysqli_sql_exception $e) {
        // Captura e silencia a exceção (Fatal Error) causada pela falha de DNS
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
 * Redireciona para uma página.
 * @param string $page
 */
function redirect($page) {
    header("Location: index.php?page=" . $page);
    exit;
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
    if (!is_db_connected()) return;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                redirect('admin_dashboard');
            } else {
                $_SESSION['login_error'] = "Usuário ou senha inválidos.";
            }
        } else {
            $_SESSION['login_error'] = "Usuário ou senha inválidos.";
        }
        $stmt->close();
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

            if ($action === 'add_product') {
                $stmt = $conn->prepare("INSERT INTO products (name, description, category, price, stock, is_promo) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssdii", $name, $description, $category, $price, $stock, $is_promo);
            } else { // edit_product
                $id = intval($_POST['product_id']);
                $stmt = $conn->prepare("UPDATE products SET name=?, description=?, category=?, price=?, stock=?, is_promo=? WHERE id=?");
                $stmt->bind_param("sssdiii", $name, $description, $category, $price, $stock, $is_promo, $id);
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
