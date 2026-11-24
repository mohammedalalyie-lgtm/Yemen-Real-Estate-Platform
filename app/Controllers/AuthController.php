<?php
namespace App\Controllers;

use App\Config\App;
use App\Models\User;
use App\Models\ActivityLog;
use App\Helpers\Session;
use App\Helpers\Validator;
use App\Helpers\View;

class AuthController
{
    public function showLogin(): void
    {
        if (Session::check()) {
            $this->redirectUserByRole();
            return;
        }

        View::render('auth.login', ['title' => 'تسجيل الدخول | عقار اليمن'], 'auth');
    }

    public function login(): void
    {
        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($login) || empty($password)) {
            Session::setFlash('danger', 'يرجى إدخال البريد الإلكتروني أو رقم الهاتف وكلمة المرور.');
            header('Location: ' . App::baseUrl('login'));
            exit;
        }

        // Brute-force protection counter
        $attempts = (int)Session::get('login_attempts', 0);
        $lastAttempt = (int)Session::get('last_login_attempt', 0);
        if ($attempts >= 5 && (time() - $lastAttempt) < 300) {
            $waitMinutes = ceil((300 - (time() - $lastAttempt)) / 60);
            Session::setFlash('danger', "تم تجاوز عدد محاولات الدخول المسموحة. يرجى الانتظار {$waitMinutes} دقائق قبل المحاولة مجدداً.");
            header('Location: ' . App::baseUrl('login'));
            exit;
        }

        $user = User::authenticate($login, $password);

        if (!$user) {
            Session::set('login_attempts', $attempts + 1);
            Session::set('last_login_attempt', time());
            Session::setFlash('danger', 'بيانات الدخول غير صحيحة أو الحساب غير مفعل.');
            header('Location: ' . App::baseUrl('login'));
            exit;
        }

        // Reset attempts
        Session::remove('login_attempts');
        Session::remove('last_login_attempt');

        // Set session
        Session::setAuth($user);

        // Log login
        ActivityLog::log($user['id'], 'login', 'users', $user['id'], 'تسجيل دخول ناجح إلى النظام');

        Session::setFlash('success', "أهلاً بك مجدداً، {$user['full_name']}!");
        $this->redirectUserByRole();
    }

    public function showRegister(): void
    {
        if (Session::check()) {
            $this->redirectUserByRole();
            return;
        }

        View::render('auth.register', ['title' => 'إنشاء حساب جديد | عقار اليمن'], 'auth');
    }

    public function register(): void
    {
        $validator = new Validator($_POST);
        $isValid = $validator->validate([
            'full_name' => 'required|min:3|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|phone|unique:users,phone',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:customer,owner,broker,office'
        ]);

        if (!$isValid) {
            Session::setFlash('danger', $validator->firstError());
            Session::set('old_input', $_POST);
            header('Location: ' . App::baseUrl('register'));
            exit;
        }

        $userId = User::register($_POST);

        if ($userId) {
            $user = User::find($userId);
            Session::setAuth($user);
            ActivityLog::log($userId, 'register', 'users', $userId, 'تسجيل حساب جديد بنجاح');
            Session::setFlash('success', 'تم إنشاء حسابك بنجاح! مرحباً بك في منصة عقار اليمن.');
            $this->redirectUserByRole();
            return;
        }

        Session::setFlash('danger', 'حدث خطأ أثناء إنشاء الحساب، يرجى المحاولة لاحقاً.');
        header('Location: ' . App::baseUrl('register'));
        exit;
    }

    public function logout(): void
    {
        if (Session::check()) {
            ActivityLog::log(Session::userId(), 'logout', 'users', Session::userId(), 'تسجيل خروج');
        }
        Session::logout();
        Session::setFlash('info', 'تم تسجيل الخروج بنجاح.');
        header('Location: ' . App::baseUrl('login'));
        exit;
    }

    public function showForgot(): void
    {
        View::render('auth.forgot-password', ['title' => 'استعادة كلمة المرور | عقار اليمن'], 'auth');
    }

    private function redirectUserByRole(): void
    {
        $role = Session::role();
        if (in_array($role, ['super_admin', 'admin'], true)) {
            header('Location: ' . App::baseUrl('admin'));
        } elseif (in_array($role, ['owner', 'broker', 'office'], true)) {
            header('Location: ' . App::baseUrl('dashboard'));
        } else {
            header('Location: ' . App::baseUrl(''));
        }
        exit;
    }
}
