<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (! $session->get('logged_in')) {
            return redirect()->to('/auth/login');
        }

        $allowedRoles = is_array($arguments) ? $arguments : [];
        if (empty($allowedRoles)) {
            return;
        }

        $role = (string) $session->get('role');
        if (! in_array($role, $allowedRoles, true)) {
            if ($role === 'mitra') {
                return redirect()->to('/mitra');
            }
            if ($role === 'user') {
                return redirect()->to('/perusahaan');
            }
            return redirect()->to('/auth/login');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
