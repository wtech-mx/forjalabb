<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GoogleDriveGalleryService;
use Illuminate\Http\Client\Response as HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class DriveGalleryController extends Controller
{
    private const DEFAULT_FOLDER_ID = '1QXjXh40eUZHRX2Pkq2ZWRxzP-ZYf1B6i';

    public function index(GoogleDriveGalleryService $drive): View
    {
        $folderId = $this->folderId();

        return view('admin.drive-gallery.index', [
            'folderUrl' => 'https://drive.google.com/drive/folders/'.$folderId,
            'oauthConfigured' => $drive->configured(),
            'driveConnected' => $drive->connected(),
        ]);
    }

    public function connect(Request $request, GoogleDriveGalleryService $drive): RedirectResponse
    {
        if (! $drive->configured()) {
            return to_route('admin.drive-gallery.index')->with('error', 'Faltan las credenciales OAuth de Google Drive en el servidor.');
        }

        $state = bin2hex(random_bytes(32));
        $request->session()->put('google_drive_oauth_state', $state);

        return redirect()->away($drive->authorizationUrl($state));
    }

    public function callback(Request $request, GoogleDriveGalleryService $drive): RedirectResponse
    {
        $expectedState = (string) $request->session()->pull('google_drive_oauth_state', '');
        $receivedState = (string) $request->query('state', '');

        if ($expectedState === '' || $receivedState === '' || ! hash_equals($expectedState, $receivedState)) {
            return to_route('admin.drive-gallery.index')->with('error', 'La conexión con Google expiró o no superó la validación de seguridad. Intenta nuevamente.');
        }

        if ($request->filled('error')) {
            return to_route('admin.drive-gallery.index')->with('error', 'Google Drive no autorizó la conexión.');
        }

        $request->validate(['code' => ['required', 'string']]);

        try {
            $drive->exchangeCode($request->user(), (string) $request->query('code'));

            return to_route('admin.drive-gallery.index')->with('success', 'Google Drive quedó conectado correctamente. Ya puedes cargar imágenes.');
        } catch (Throwable $exception) {
            report($exception);

            return to_route('admin.drive-gallery.index')->with('error', 'No fue posible conectar Google Drive: '.$exception->getMessage());
        }
    }

    public function files(): JsonResponse
    {
        if (blank(config('services.drive_gallery.api_key'))) {
            return response()->json([
                'message' => 'Google Drive no está configurado en este servidor. Agrega GOOGLE_DRIVE_API_KEY al archivo .env y limpia la caché de Laravel.',
            ], 503);
        }

        $folders = [[
            'id' => $this->folderId(),
            'path' => 'Principal',
        ]];
        $visited = [];
        $images = collect();
        $directories = collect();

        while ($folders !== [] && count($visited) < 100) {
            $folder = array_shift($folders);

            if (isset($visited[$folder['id']])) {
                continue;
            }

            $visited[$folder['id']] = true;
            $response = $this->folderContents($folder['id']);

            if (! $response->successful()) {
                return response()->json(['message' => $this->driveError($response)], 502);
            }

            foreach ($response->json('files', []) as $file) {
                if (($file['mimeType'] ?? '') === 'application/vnd.google-apps.folder' && filled($file['id'] ?? null)) {
                    $path = $folder['path'].' / '.$file['name'];
                    $folders[] = ['id' => $file['id'], 'path' => $path];
                    $directories->push(['id' => $file['id'], 'name' => $file['name'], 'path' => $path, 'parent' => $folder['path']]);
                } elseif (str_starts_with($file['mimeType'] ?? '', 'image/')) {
                    $file['folder'] = $folder['path'];
                    $images->push($file);
                }
            }
        }

        $files = $images->map(fn (array $file) => [
                'id' => $file['id'],
                'name' => $file['name'],
                'folder' => $file['folder'],
                'mime_type' => $file['mimeType'],
                'size' => (int) ($file['size'] ?? 0),
                'modified_at' => $file['modifiedTime'] ?? null,
                'thumbnail_url' => 'https://drive.google.com/thumbnail?id='.$file['id'].'&sz=w800',
                'view_url' => $file['webViewLink'] ?? 'https://drive.google.com/file/d/'.$file['id'].'/view',
                'download_url' => route('admin.drive-gallery.download', $file['id']),
            ])->values();

        Cache::put('drive_gallery.image_ids', $files->pluck('id')->all(), now()->addMinutes(15));
        Cache::put('drive_gallery.folder_ids', array_keys($visited), now()->addMinutes(15));

        return response()->json([
            'files' => $files,
            'folders' => $directories->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)->values(),
            'count' => $files->count(),
        ]);
    }

    public function upload(Request $request, GoogleDriveGalleryService $drive): JsonResponse
    {
        $data = $request->validate([
            'folder_id' => ['required', 'string', 'max:200'],
            'images' => ['required', 'array', 'min:1', 'max:20'],
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:20480'],
        ]);
        $allowedFolders = Cache::get('drive_gallery.folder_ids', [$this->folderId()]);

        if (! in_array($data['folder_id'], $allowedFolders, true)) {
            return response()->json(['message' => 'La carpeta seleccionada no pertenece a esta galería.'], 422);
        }

        if (! $drive->connected()) {
            return response()->json(['message' => 'Primero conecta tu cuenta de Google Drive.'], 503);
        }

        try {
            $uploaded = collect($request->file('images'))->map(
                fn ($image) => $drive->upload($image, $data['folder_id'])
            );
            Cache::forget('drive_gallery.image_ids');

            return response()->json([
                'message' => $uploaded->count().' imagen'.($uploaded->count() === 1 ? '' : 'es').' cargada'.($uploaded->count() === 1 ? '' : 's').' correctamente.',
                'count' => $uploaded->count(),
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json(['message' => 'Google Drive rechazó la carga: '.$exception->getMessage()], 502);
        }
    }

    public function download(string $file): StreamedResponse
    {
        $metadata = $this->driveRequest('https://www.googleapis.com/drive/v3/files/'.$file, [
            'fields' => 'id,name,mimeType,parents',
        ]);
        abort_unless($metadata->successful(), 404);
        abort_unless(str_starts_with((string) $metadata->json('mimeType'), 'image/'), 404);
        $allowedIds = Cache::get('drive_gallery.image_ids');

        if (! is_array($allowedIds)) {
            $allowedIds = $this->galleryImageIds();
            Cache::put('drive_gallery.image_ids', $allowedIds, now()->addMinutes(15));
        }

        abort_unless(in_array($file, $allowedIds, true), 404);

        $download = $this->driveRequest('https://www.googleapis.com/drive/v3/files/'.$file, ['alt' => 'media'], true);
        abort_unless($download->successful(), 502);
        $stream = $download->toPsrResponse()->getBody();
        $filename = str_replace(['/', '\\', '"'], '-', (string) $metadata->json('name'));

        return response()->streamDownload(function () use ($stream) {
            while (! $stream->eof()) {
                echo $stream->read(8192);
            }
        }, $filename, ['Content-Type' => (string) $metadata->json('mimeType')]);
    }

    private function driveRequest(string $url, array $query, bool $stream = false): HttpResponse
    {
        $key = config('services.drive_gallery.api_key');
        abort_if(blank($key), 503, 'Falta configurar GOOGLE_DRIVE_API_KEY.');

        return Http::timeout(30)->withOptions(['stream' => $stream])->get($url, $query + ['key' => $key]);
    }

    private function folderContents(string $folderId): HttpResponse
    {
        return $this->driveRequest('https://www.googleapis.com/drive/v3/files', [
            'q' => sprintf("'%s' in parents and trashed = false", $folderId),
            'fields' => 'files(id,name,mimeType,size,modifiedTime,webViewLink)',
            'orderBy' => 'name_natural',
            'pageSize' => 1000,
        ]);
    }

    private function galleryImageIds(): array
    {
        $folderIds = [$this->folderId()];
        $visited = [];
        $imageIds = [];

        while ($folderIds !== [] && count($visited) < 100) {
            $folderId = array_shift($folderIds);
            if (isset($visited[$folderId])) {
                continue;
            }

            $visited[$folderId] = true;
            $response = $this->folderContents($folderId);
            if (! $response->successful()) {
                continue;
            }

            foreach ($response->json('files', []) as $item) {
                if (($item['mimeType'] ?? '') === 'application/vnd.google-apps.folder' && filled($item['id'] ?? null)) {
                    $folderIds[] = $item['id'];
                } elseif (str_starts_with($item['mimeType'] ?? '', 'image/') && filled($item['id'] ?? null)) {
                    $imageIds[] = $item['id'];
                }
            }
        }

        return array_values(array_unique($imageIds));
    }

    private function driveError(HttpResponse $response): string
    {
        return (string) ($response->json('error.message') ?: 'Google Drive no pudo entregar la galería.');
    }

    private function folderId(): string
    {
        return trim((string) config('services.drive_gallery.folder_id')) ?: self::DEFAULT_FOLDER_ID;
    }
}
