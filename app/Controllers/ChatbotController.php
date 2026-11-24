<?php
namespace App\Controllers;

use App\Models\Chatbot;
use App\Helpers\Session;
use App\Helpers\View;

class ChatbotController
{
    public function message(): void
    {
        $input = trim($_POST['message'] ?? '');
        if (empty($input)) {
            View::json(['error' => 'الرسالة فارغة'], 400);
            return;
        }

        $userId = Session::userId();
        $response = Chatbot::processQuery($input, $userId);

        View::json($response);
    }
}
