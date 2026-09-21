<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function respond(Request $request)
    {
        $message = strtolower($request->input('message'));
        $reply = $this->getBotResponse($message);

        return response()->json(['reply' => $reply]);
    }

    private function getBotResponse($message)
    {
        // Simple rule-based chatbot logic
        $message = trim($message);
        
        if (str_contains($message, 'halo') || str_contains($message, 'hai')) {
            return "Halo! Ada yang bisa saya bantu terkait SILILA (Sistem Informasi Lahan Sawah Pertanian)?";
        } elseif (str_contains($message, 'lp2b')) {
            return "LP2B adalah Lahan Pertanian Pangan Berkelanjutan. Anda dapat melihat area yang dilindungi pada peta utama dengan memilih layer LP2B.";
        } elseif (str_contains($message, 'lsd')) {
            return "LSD adalah Lahan Sawah Dilindungi. Layer ini dapat diaktifkan pada peta untuk melihat wilayah sawah yang dilindungi.";
        } elseif (str_contains($message, 'login')) {
            return "Untuk keperluan login admin, silakan menuju halaman /login.";
        } elseif (str_contains($message, 'peta') || str_contains($message, 'map')) {
            return "Peta interaktif kami menampilkan data Geometri, LSD, dan LP2B. Anda dapat menggunakan fitur filter di sebelah kiri layar untuk melihat area yang diinginkan.";
        } else {
            return "Maaf, saya tidak mengerti. Silakan tanyakan hal lain seperti 'Apa itu LP2B?', 'Apa itu LSD?', atau seputar 'Peta'.";
        }
    }
}
