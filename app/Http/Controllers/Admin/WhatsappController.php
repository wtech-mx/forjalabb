<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\WhatsappWebService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class WhatsappController extends Controller
{
    public function index(): View
    {
        return view('admin.whatsapp.index');
    }

    public function chats(WhatsappWebService $whatsapp): JsonResponse
    {
        try {
            return response()->json($whatsapp->chats());
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 503);
        }
    }

    public function messages(Request $request, WhatsappWebService $whatsapp): JsonResponse
    {
        $data = $request->validate(['chat_id' => ['required', 'string', 'max:100']]);
        try {
            return response()->json($whatsapp->messages($data['chat_id']));
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 503);
        }
    }

    public function sendChat(Request $request, WhatsappWebService $whatsapp): JsonResponse
    {
        $data = $request->validate([
            'chat_id' => ['required', 'string', 'max:100'],
            'message' => ['required', 'string', 'max:4096'],
        ]);
        try {
            return response()->json($whatsapp->sendToChat($data['chat_id'], $data['message']));
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 503);
        }
    }

    public function status(WhatsappWebService $whatsapp): JsonResponse
    {
        return response()->json($whatsapp->status());
    }

    public function send(Request $request, Order $order, WhatsappWebService $whatsapp): RedirectResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:4096'],
        ]);

        try {
            $whatsapp->send($data['phone'], $data['message']);
            return back()->with('status', 'Mensaje de WhatsApp enviado correctamente.');
        } catch (RuntimeException $exception) {
            return back()->withInput()->withErrors(['whatsapp' => $exception->getMessage()]);
        }
    }

    public function logout(WhatsappWebService $whatsapp): RedirectResponse
    {
        try {
            $whatsapp->logout();
            return back()->with('status', 'La cuenta de WhatsApp fue desconectada.');
        } catch (RuntimeException $exception) {
            return back()->withErrors(['whatsapp' => $exception->getMessage()]);
        }
    }
}
