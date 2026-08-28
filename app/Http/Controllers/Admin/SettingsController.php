<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DatabaseBackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SettingsController extends Controller
{
    public function index(): View
    {
        return view('admin.settings.index', [
            'databaseName' => config('database.connections.mysql.database'),
            'driver' => config('database.default'),
        ]);
    }

    public function download(DatabaseBackupService $backups): BinaryFileResponse|RedirectResponse
    {
        try {
            $path = $backups->create();

            return response()->download($path, basename($path), ['Content-Type' => 'application/sql'])->deleteFileAfterSend(true);
        } catch (RuntimeException $exception) {
            report($exception);

            return back()->withErrors(['backup' => $exception->getMessage()]);
        }
    }

    public function restore(Request $request, DatabaseBackupService $backups): RedirectResponse
    {
        $data = $request->validate([
            'database_backup' => ['required', 'file', 'extensions:sql', 'max:524288'],
            'confirmation' => ['required', 'in:RESTAURAR'],
        ], [
            'database_backup.extensions' => 'El respaldo debe ser un archivo .sql.',
            'database_backup.max' => 'El respaldo no puede superar 512 MB.',
            'confirmation.in' => 'Escribe RESTAURAR exactamente para confirmar.',
        ]);

        $uploadedPath = $data['database_backup']->getRealPath();

        try {
            $safetyBackup = $backups->create('antes-de-restaurar');
            $backups->restore($uploadedPath);
            DB::purge();

            return redirect()->route('admin.settings.index')->with(
                'status',
                'Base de datos restaurada. Se guardo un respaldo de seguridad en '.basename($safetyBackup).'.'
            );
        } catch (RuntimeException $exception) {
            report($exception);

            return back()->withInput()->withErrors(['restore' => $exception->getMessage()]);
        }
    }
}
