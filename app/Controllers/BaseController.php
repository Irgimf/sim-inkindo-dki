<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
    }

    /**
     * Resolve the public asset directory of the sibling inkindo-dki-web application.
     */
    protected function getInkindoWebPublicPath(): string
    {
        return dirname(rtrim(ROOTPATH, '/\\'))
            . DIRECTORY_SEPARATOR . 'inkindo-dki-web'
            . DIRECTORY_SEPARATOR . 'public'
            . DIRECTORY_SEPARATOR;
    }

    /**
     * Copy an uploaded public asset to the sibling inkindo-dki-web/public directory.
     */
    protected function syncFileToInkindoWeb(string $relativePath): void
    {
        $normalizedRel = str_replace('/', DIRECTORY_SEPARATOR, ltrim($relativePath, '/\\'));
        $source = FCPATH . $normalizedRel;
        if (!is_file($source)) {
            return;
        }

        $webPublicRoot = $this->getInkindoWebPublicPath();
        $relDir = dirname($normalizedRel);
        $targetDir = ($relDir === '.' || $relDir === '') ? rtrim($webPublicRoot, '/\\') : $webPublicRoot . $relDir;
        $target = $webPublicRoot . $normalizedRel;

        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0777, true);
        }
        @copy($source, $target);
    }

    /**
     * Redirect non-admin users away from Admin CMS actions.
     */
    protected function ensureAdmin()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard');
        }
        return null;
    }

    /**
     * Save a cropped/encoded base64 image into public/cms_uploads and sync it to inkindo-dki-web.
     */
    protected function handleBase64ImageUpload(string $base64Data, string $subFolder = 'cms_uploads', string $prefix = 'banner_'): ?string
    {
        if (!preg_match('/^data:image\/(jpeg|jpg|png|webp);base64,(.+)$/i', trim($base64Data), $matches)) {
            return null;
        }

        $ext = strtolower($matches[1]);
        if ($ext === 'jpeg') {
            $ext = 'jpg';
        }
        $binary = base64_decode($matches[2], true);
        if ($binary === false || strlen($binary) < 64) {
            return null;
        }

        $destDir = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $subFolder);
        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }

        $newName = $prefix . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $fullPath = $destDir . DIRECTORY_SEPARATOR . $newName;
        if (file_put_contents($fullPath, $binary) === false) {
            return null;
        }

        $optimizedPath = $this->optimizeToWebp($fullPath);
        $finalName = basename($optimizedPath);
        $relativePath = trim($subFolder, '/') . '/' . $finalName;
        $this->syncFileToInkindoWeb($relativePath);

        return $relativePath;
    }

    /**
     * Upload a multipart form file into public/ and sync it to inkindo-dki-web.
     */
    protected function handleSingleUpload(string $fieldName, array $allowedExts, string $subFolder = 'cms_uploads', string $prefix = 'cms_'): ?string
    {
        $file = $this->request->getFile($fieldName);
        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return null;
        }

        $ext = strtolower($file->getExtension() ?: pathinfo($file->getClientName(), PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExts, true)) {
            return null;
        }

        $destDir = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $subFolder);
        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }

        $newName = $prefix . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $file->move($destDir, $newName);

        $fullPath = $destDir . DIRECTORY_SEPARATOR . $newName;
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            $fullPath = $this->optimizeToWebp($fullPath);
        }

        $relativePath = trim($subFolder, '/') . '/' . basename($fullPath);
        $this->syncFileToInkindoWeb($relativePath);

        return $relativePath;
    }

    /**
     * Convert and compress JPG/PNG/WebP images to WebP (quality 88, max width 1920px)
     * while preserving visual quality and transparency.
     */
    protected function optimizeToWebp(string $fullPath, int $maxWidth = 1920, int $quality = 88): string
    {
        if (!is_file($fullPath) || !function_exists('imagewebp')) {
            return $fullPath;
        }

        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return $fullPath;
        }

        $img = null;
        if ($ext === 'jpg' || $ext === 'jpeg') {
            $img = @imagecreatefromjpeg($fullPath);
            if ($img && function_exists('exif_read_data')) {
                $exif = @exif_read_data($fullPath);
                $orientation = (int) ($exif['Orientation'] ?? 1);
                if ($orientation === 3) {
                    $img = imagerotate($img, 180, 0);
                } elseif ($orientation === 6) {
                    $img = imagerotate($img, -90, 0);
                } elseif ($orientation === 8) {
                    $img = imagerotate($img, 90, 0);
                }
            }
        } elseif ($ext === 'png') {
            $img = @imagecreatefrompng($fullPath);
            if ($img) {
                if (function_exists('imagepalettetotruecolor')) {
                    @imagepalettetotruecolor($img);
                }
                imagealphablending($img, true);
                imagesavealpha($img, true);
            }
        } elseif ($ext === 'webp') {
            $img = @imagecreatefromwebp($fullPath);
            if ($img) {
                imagealphablending($img, true);
                imagesavealpha($img, true);
            }
        }

        if (!$img) {
            return $fullPath;
        }

        $origW = imagesx($img);
        $origH = imagesy($img);
        if ($origW > $maxWidth && $origH > 0) {
            $newW = $maxWidth;
            $newH = (int) round(($origH / $origW) * $newW);
            $resized = imagecreatetruecolor($newW, $newH);
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
            imagefilledrectangle($resized, 0, 0, $newW, $newH, $transparent);
            imagecopyresampled($resized, $img, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
            imagedestroy($img);
            $img = $resized;
        }

        $webpPath = preg_replace('/\.(jpe?g|png|webp)$/i', '.webp', $fullPath);
        if (!$webpPath) {
            imagedestroy($img);
            return $fullPath;
        }

        $tempPath = $webpPath . '.tmp.webp';
        $saved = @imagewebp($img, $tempPath, $quality);
        imagedestroy($img);

        if (!$saved || !is_file($tempPath) || filesize($tempPath) === 0) {
            @unlink($tempPath);
            return $fullPath;
        }

        // Jika file awal sudah WebP dan hasil re-encode lebih besar, pertahankan file awal
        if ($ext === 'webp' && filesize($tempPath) >= filesize($fullPath)) {
            @unlink($tempPath);
            return $fullPath;
        }

        if ($webpPath !== $fullPath && is_file($fullPath)) {
            @unlink($fullPath);
        }
        @rename($tempPath, $webpPath);

        return is_file($webpPath) ? $webpPath : $fullPath;
    }
}
