<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Services\Media\FileHelperService;
use App\Services\Media\ImageRegistryService;
use Illuminate\Http\Request;
use Throwable;

class AdminMediaUploadController extends Controller
{
    protected array $folders = [];

    public function __construct()
    {
        $this->folders = config('media.directories', []);
    }

    public function store(
        Request $request,
        FileHelperService $files,
        ImageRegistryService $registry
    ) {
        $maxFilesPerRequest = max(1, (int) ini_get('max_file_uploads'));
        $maxFileSizeKb = max(1, (int) config('media.request_limits.upload_file_max_kb', 5120));

        $validated = $request->validate([
            'folder' => 'required|in:' . implode(',', array_keys($this->folders)),
            'files' => 'required|array|min:1|max:' . $maxFilesPerRequest,
            'files.*' => 'file|extensions:jpg,jpeg,png,webp,gif,avif|max:' . $maxFileSizeKb,
        ]);

        $uploadedCount = 0;
        $failedCount = 0;
        $failedFiles = [];
        $results = [];

        $uploadedFiles = $request->file('files', []);
        $folder = $this->folders[$validated['folder']];

        \Log::info('Media upload started', [
            'folder' => $validated['folder'],
            'file_count' => count($uploadedFiles),
        ]);

        foreach ($uploadedFiles as $uploadedFile) {
            $filename = $uploadedFile->getClientOriginalName();

            try {
                if (!$uploadedFile->isValid()) {
                    throw new \Exception("File '{$filename}' không hợp lệ: " . $uploadedFile->getErrorMessage());
                }

                $stored = $files->storeUploadedFile($uploadedFile, $folder);
                $image = null;

                if ($validated['folder'] === 'imports') {
                    $results[] = [
                        'id' => md5($stored['relative_path']),
                        'path' => $stored['relative_path'],
                        'type' => 'filesystem_file',
                        'original' => asset($stored['relative_path']),
                    ];
                } else {
                    $finalFilename = basename($stored['relative_path']);
                    $nameWithoutExt = pathinfo($finalFilename, PATHINFO_FILENAME);
                    $baseCompareName = preg_replace('/-\d{5,15}$/', '', $nameWithoutExt);
                    
                    $candidates = \App\Models\Image::where(function($q) use ($baseCompareName, $folder) {
                            $q->where('url', 'like', $folder . '/%' . $baseCompareName . '%')
                              ->orWhere('path', 'like', $folder . '/%' . $baseCompareName . '%');
                        })
                        ->get();
                        
                    $existingImages = [];
                    foreach ($candidates as $candidate) {
                        $dbUrlName = pathinfo(basename($candidate->url ?? ''), PATHINFO_FILENAME);
                        $dbPathName = pathinfo(basename($candidate->path ?? ''), PATHINFO_FILENAME);
                        $cleanDbUrlName = preg_replace('/-\d{5,15}$/', '', $dbUrlName);
                        $cleanDbPathName = preg_replace('/-\d{5,15}$/', '', $dbPathName);
                        
                        if ($cleanDbUrlName === $baseCompareName || $cleanDbPathName === $baseCompareName) {
                            $existingImages[] = $candidate;
                        }
                    }

                    if (count($existingImages) > 0) {
                        $oldPathsToDelete = [];
                        $keptImages = [];
                        $imagesToDeleteFromDb = [];
                        
                        foreach ($existingImages as $existingImage) {
                            $isLinked = $existingImage->entity_id !== null || $existingImage->product_id !== null;
                            if ($isLinked) {
                                $keptImages[] = $existingImage;
                            } else {
                                $imagesToDeleteFromDb[] = $existingImage;
                            }
                        }
                        
                        if (count($keptImages) === 0 && count($imagesToDeleteFromDb) > 0) {
                            $keptImages[] = array_shift($imagesToDeleteFromDb);
                        }
                        
                        foreach ($keptImages as $existingImage) {
                            $oldPath = $existingImage->path ?: $existingImage->url;
                            if ($oldPath && $oldPath !== $stored['relative_path']) {
                                $oldPathsToDelete[$oldPath] = true;
                                if ($existingImage->thumbnail_url) $oldPathsToDelete[$existingImage->thumbnail_url] = true;
                                if ($existingImage->medium_url) $oldPathsToDelete[$existingImage->medium_url] = true;
                            }

                            $existingImage->update([
                                'url' => $stored['relative_path'],
                                'path' => $stored['relative_path'],
                                'size' => $uploadedFile->getSize()
                            ]);
                        }
                        
                        foreach ($imagesToDeleteFromDb as $redundantImage) {
                            $oldPath = $redundantImage->path ?: $redundantImage->url;
                            if ($oldPath && $oldPath !== $stored['relative_path']) {
                                $oldPathsToDelete[$oldPath] = true;
                                if ($redundantImage->thumbnail_url) $oldPathsToDelete[$redundantImage->thumbnail_url] = true;
                                if ($redundantImage->medium_url) $oldPathsToDelete[$redundantImage->medium_url] = true;
                            }
                            $redundantImage->delete();
                        }
                        
                        foreach (array_keys($oldPathsToDelete) as $pathToDelete) {
                             $files->deleteFile($pathToDelete);
                        }

                        $image = $keptImages[0];
                    } else {
                        $image = $registry->registerLooseImage(
                            $stored['relative_path'],
                            [
                                'title' => pathinfo($finalFilename, PATHINFO_FILENAME),
                                'alt' => pathinfo($finalFilename, PATHINFO_FILENAME),
                            ],
                            $validated['folder']
                        );
                    }

                    $results[] = [
                        'id' => $image->id,
                        'path' => $image->path,
                        'type' => 'library_image',
                    ];
                }

                $uploadedCount++;
            } catch (Throwable $exception) {
                $failedCount++;
                $failedFiles[] = [
                    'name' => $filename,
                    'error' => $exception->getMessage(),
                ];
                report($exception);
            }
        }

        $statusCode = $uploadedCount > 0 ? 200 : 422;

        return response()->json([
            'success' => $failedCount === 0 && $uploadedCount > 0,
            'message' => $this->buildUploadMessage($uploadedCount, $failedCount, count($uploadedFiles)),
            'uploaded_count' => $uploadedCount,
            'failed_count' => $failedCount,
            'failed_files' => $failedFiles,
            'items' => $results,
        ], $statusCode);
    }

    protected function buildUploadMessage(int $uploaded, int $failed, int $total): string
    {
        $parts = [];
        if ($uploaded > 0) {
            $parts[] = "Đã upload thành công {$uploaded} ảnh.";
        }
        if ($failed > 0) {
            $parts[] = "{$failed} ảnh upload thất bại.";
        }
        if (empty($parts)) {
            return "Không có file nào được upload.";
        }
        return implode(' ', $parts);
    }
}

