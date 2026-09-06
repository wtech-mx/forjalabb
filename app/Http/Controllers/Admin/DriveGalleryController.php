<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\Response as HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DriveGalleryController extends Controller
{
    private const DEFAULT_FOLDER_ID = '1QXjXh40eUZHRX2Pkq2ZWRxzP-ZYf1B6i';

    public function index(): View
    {
        $folderId = $this->folderId();

        return view('admin.drive-gallery.index', [
            'folderUrl' => 'https://drive.google.com/drive/folders/'.$folderId,
        ]);
    }

    public function files(): JsonResponse
    {
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

        return response()->json([
            'files' => $files,
            'folders' => $directories->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)->values(),
            'count' => $files->count(),
        ]);
    }

    public function download(string $file): StreamedResponse
    {
        $metadata = $this->driveRequest('https://www.googleapis.com/drive/v3/files/'.$file, [
            'fields' => 'id,name,mimeType,parents',
        ]);
        abort_unless($metadata->successful(), 404);
        abort_unless(str_starts_with((string) $metadata->json('mimeType'), 'image/'), 404);
        abort_unless($this->belongsToGallery($metadata->json('parents', [])), 404);

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

    private function belongsToGallery(array $parentIds): bool
    {
        $root = $this->folderId();
        $visited = [];

        while ($parentIds !== [] && count($visited) < 100) {
            $parentId = array_shift($parentIds);

            if ($parentId === $root) {
                return true;
            }

            if (isset($visited[$parentId])) {
                continue;
            }

            $visited[$parentId] = true;
            $parent = $this->driveRequest('https://www.googleapis.com/drive/v3/files/'.$parentId, ['fields' => 'id,parents']);

            if ($parent->successful()) {
                array_push($parentIds, ...$parent->json('parents', []));
            }
        }

        return false;
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
