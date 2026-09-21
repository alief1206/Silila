<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatSession;
use App\Models\ChatMessage;

class ChatController extends Controller
{
    // API untuk pengunjung (Public)
    public function startSession(Request $request)
    {
        $request->validate([
            'nik' => 'required',
            'nama' => 'required',
            'koordinat' => 'required'
        ]);

        $session = ChatSession::create([
            'nik' => $request->nik,
            'nama' => $request->nama,
            'koordinat' => $request->koordinat,
            'status' => 'open'
        ]);

        // Simpan sesi di session laravel agar mudah
        session(['chat_session_id' => $session->id]);

        return response()->json(['success' => true, 'session_id' => $session->id]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:chat_sessions,id',
            'message' => 'required',
            'sender_type' => 'required|in:user,admin'
        ]);

        $message = ChatMessage::create([
            'session_id' => $request->session_id,
            'sender_type' => $request->sender_type,
            'message' => $request->message
        ]);

        // Jika pesan dikirim oleh admin, update updated_at di session agar sesi naik ke atas
        if ($request->sender_type == 'admin') {
            $message->session->touch();
        }

        return response()->json(['success' => true, 'message' => $message]);
    }

    public function fetchMessages(Request $request, $sessionId)
    {
        $session = ChatSession::find($sessionId);
        if (!$session) {
            return response()->json(['error' => 'Not found', 'status' => 'closed'], 404);
        }

        $lastId = $request->query('last_id', 0);
        $messages = ChatMessage::where('session_id', $sessionId)
            ->where('id', '>', $lastId)
            ->orderBy('id', 'asc')
            ->get();

        $adminLastReadId = ChatMessage::where('session_id', $sessionId)
            ->where('sender_type', 'user')
            ->where('is_read', true)
            ->max('id') ?? 0;

        $userLastReadId = ChatMessage::where('session_id', $sessionId)
            ->where('sender_type', 'admin')
            ->where('is_read', true)
            ->max('id') ?? 0;

        return response()->json([
            'status' => $session->status, 
            'messages' => $messages,
            'admin_last_read_id' => $adminLastReadId,
            'user_last_read_id' => $userLastReadId
        ]);
    }

    public function markAsRead(Request $request, $sessionId)
    {
        $request->validate([
            'reader_type' => 'required|in:user,admin'
        ]);

        $senderTypeToMark = $request->reader_type === 'user' ? 'admin' : 'user';

        ChatMessage::where('session_id', $sessionId)
            ->where('sender_type', $senderTypeToMark)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }

    // Antarmuka Admin
    public function adminIndex()
    {
        return view('dashboard.chat.index');
    }

    public function adminGetSessions()
    {
        $sessions = ChatSession::with(['messages' => function($query) {
            $query->orderBy('created_at', 'desc')->limit(1);
        }])
        ->where('status', 'open')
        ->orderBy('updated_at', 'desc')
        ->get();

        return response()->json(['sessions' => $sessions]);
    }

    public function adminCloseSession($id)
    {
        $session = ChatSession::findOrFail($id);
        $session->update(['status' => 'closed']);
        return response()->json(['success' => true]);
    }
}
