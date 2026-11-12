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
}
