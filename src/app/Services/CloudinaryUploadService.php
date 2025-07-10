<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Resize;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Exception;

class CloudinaryUploadService
{
    private $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key' => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => [
                'secure' => true
            ]
        ]);
    }

    /**
     * Upload image ke Cloudinary
     * 
     * @param UploadedFile $file
     * @param string $folder
     * @param array $options
     * @return array
     */
    public function uploadImage(UploadedFile $file, string $folder = 'images', array $options = [])
    {
        try {
            // Validasi file image
            if (!$this->isValidImage($file)) {
                throw new Exception('File harus berupa gambar yang valid');
            }

            // Default options untuk image
            $defaultOptions = [
                'folder' => $folder,
                'resource_type' => 'image',
                // 'format' => 'auto',
                // 'quality' => 'auto:good',
                // 'fetch_format' => 'auto',
                // 'transformation' => [
                //     'width' => 1920,
                //     'height' => 1080,
                //     'crop' => 'limit'
                // ]
            ];

            // Merge dengan options yang diberikan
            $uploadOptions = array_merge($defaultOptions, $options);

            // Upload ke Cloudinary
            $result = $this->cloudinary->uploadApi()->upload(
                $file->getPathname(),
                $uploadOptions
            );

            return [
                'success' => true,
                'public_id' => $result['public_id'],
                'url' => $result['secure_url'],
                'format' => $result['format'],
                'width' => $result['width'],
                'height' => $result['height'],
                'bytes' => $result['bytes'],
                'created_at' => $result['created_at']
            ];
        } catch (Exception $e) {
            Log::error('Cloudinary Image Upload Error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Upload video ke Cloudinary
     * 
     * @param UploadedFile $file
     * @param string $folder
     * @param array $options
     * @return array
     */
    public function uploadVideo(UploadedFile $file, string $folder = 'videos', array $options = [])
    {
        try {
            // Validasi file video
            if (!$this->isValidVideo($file)) {
                throw new Exception('File harus berupa video yang valid');
            }

            // Default options untuk video
            $defaultOptions = [
                'folder' => $folder,
                'resource_type' => 'video',
                // 'format' => 'auto',
                // 'quality' => 'auto:good',
                // 'transformation' => [
                //     'width' => 1280,
                //     'height' => 720,
                //     'crop' => 'limit',
                //     'video_codec' => 'auto'
                // ]
            ];

            // Merge dengan options yang diberikan
            $uploadOptions = array_merge($defaultOptions, $options);

            // Upload ke Cloudinary
            $result = $this->cloudinary->uploadApi()->upload(
                $file->getPathname(),
                $uploadOptions
            );

            return [
                'success' => true,
                'public_id' => $result['public_id'],
                'url' => $result['secure_url'],
                'format' => $result['format'],
                'width' => $result['width'] ?? null,
                'height' => $result['height'] ?? null,
                'bytes' => $result['bytes'],
                'duration' => $result['duration'] ?? null,
                'created_at' => $result['created_at']
            ];
        } catch (Exception $e) {
            Log::error('Cloudinary Video Upload Error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Upload file dengan deteksi otomatis (image/video)
     * 
     * @param UploadedFile $file
     * @param string $folder
     * @param array $options
     * @return array
     */
    public function uploadFile(UploadedFile $file, string $folder = 'uploads', array $options = [])
    {
        if ($this->isValidImage($file)) {
            return $this->uploadImage($file, $folder . '/images', $options);
        } elseif ($this->isValidVideo($file)) {
            return $this->uploadVideo($file, $folder . '/videos', $options);
        } else {
            return [
                'success' => false,
                'error' => 'Format file tidak didukung. Hanya mendukung gambar dan video.'
            ];
        }
    }

    /**
     * Hapus file dari Cloudinary
     * 
     * @param string $publicId
     * @param string $resourceType
     * @return array
     */
    public function deleteFile(string $publicId, string $resourceType = 'image')
    {
        try {
            $result = $this->cloudinary->uploadApi()->destroy(
                $publicId,
                ['resource_type' => $resourceType]
            );

            return [
                'success' => $result['result'] === 'ok',
                'result' => $result['result']
            ];
        } catch (Exception $e) {
            Log::error('Cloudinary Delete Error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate URL dengan transformasi
     * 
     * @param string $publicId
     * @param array $transformations
     * @return string
     */
    public function getTransformedUrl(string $publicId, array $transformations = [])
    {
        try {
            return $this->cloudinary->image($publicId)
                ->addTransformation($transformations)
                ->toUrl();
        } catch (Exception $e) {
            Log::error('Cloudinary Transform URL Error: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Validasi apakah file adalah gambar yang valid
     * 
     * @param UploadedFile $file
     * @return bool
     */
    private function isValidImage(UploadedFile $file): bool
    {
        $allowedMimes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/bmp',
            'image/svg+xml',
            'image/webp'
        ];

        return in_array($file->getMimeType(), $allowedMimes);
    }

    /**
     * Validasi apakah file adalah video yang valid
     * 
     * @param UploadedFile $file
     * @return bool
     */
    private function isValidVideo(UploadedFile $file): bool
    {
        $allowedMimes = [
            'video/mp4',
            'video/avi',
            'video/quicktime',
            'video/x-msvideo',
            'video/x-ms-wmv',
            'video/3gpp',
            'video/webm',
            'video/ogg'
        ];

        return in_array($file->getMimeType(), $allowedMimes);
    }

    /**
     * Upload multiple files
     * 
     * @param array $files
     * @param string $folder
     * @param array $options
     * @return array
     */
    public function uploadMultipleFiles(array $files, string $folder = 'uploads', array $options = [])
    {
        $results = [];

        foreach ($files as $index => $file) {
            if ($file instanceof UploadedFile) {
                $results[$index] = $this->uploadFile($file, $folder, $options);
            } else {
                $results[$index] = [
                    'success' => false,
                    'error' => 'File tidak valid'
                ];
            }
        }

        return $results;
    }
}
