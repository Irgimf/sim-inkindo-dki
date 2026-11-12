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

        $relativePath = trim($subFolder, '/') . '/' . $newName;
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

        $relativePath = trim($subFolder, '/') . '/' . $newName;
        $this->syncFileToInkindoWeb($relativePath);

        return $relativePath;
    }
}
