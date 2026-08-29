<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\WhatsappWebService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

class WhatsappController extends Controller
{
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
