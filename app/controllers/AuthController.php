<?php
require_once __DIR__ . '/../models/User.php';

class AuthController extends Controller
{
    public function registerForm(): void
    {
        $this->view('auth/register');
    }

    public function register(): void
    {
        if (!rate_limit('register', 5, 300)) {
            $_SESSION['flash_error'] = 'Demasiados intentos. Intenta más tarde.';
            $this->redirect('/register');
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($name === '' || $email === '' || $password === '') {
            $_SESSION['flash_error'] = 'Completa todos los campos.';
            $this->redirect('/register');
        }

        if (User::findByEmail($email)) {
            $_SESSION['flash_error'] = 'Email ya registrado.';
            $this->redirect('/register');
        }

        $userId = User::create($name, $email, $password, 'client');
        $_SESSION['user'] = ['id' => $userId, 'name' => $name, 'email' => $email, 'role' => 'client'];
        $this->redirect('/menu');
    }

    public function loginForm(): void
    {
        $this->view('auth/login');
    }

    public function login(): void
    {
        if (!rate_limit('login', 8, 300)) {
            $_SESSION['flash_error'] = 'Demasiados intentos. Intenta más tarde.';
            $this->redirect('/login');
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = User::verifyLogin($email, $password);
        if (!$user) {
            $_SESSION['flash_error'] = 'Credenciales inválidas.';
            $this->redirect('/login');
        }

        $_SESSION['user'] = $user;
        if ($user['role'] === 'admin') {
            $this->redirect('/admin');
        }
        if ($user['role'] === 'rider') {
            $this->redirect('/rider');
        }
        $this->redirect('/menu');
    }

    public function logout(): void
    {
        session_destroy();
        $this->redirect('/login');
    }
}