<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        return view('auth/login', ['is_register' => false]);
    }

    public function processLogin()
    {
        $session = session();
        $model = new UserModel();
        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');
        
        $data = $model->where('username', $username)->first();
        
        if($data){
            $pass = $data['password'];
            $verify_pass = password_verify($password, $pass);
            if($verify_pass){
                $normalizedRole = in_array($data['role'], ['admin', 'super_admin'], true) ? 'admin' : 'user';
                $ses_data = [
                    'id'       => $data['id'],
                    'username' => $data['username'],
                    'role'     => $normalizedRole,
                    'logged_in'=> TRUE
                ];
                $session->set($ses_data);
                if ($normalizedRole === 'user') {
                    return redirect()->to('/perusahaan');
                }
                return redirect()->to('/dashboard');
            }else{
                $session->setFlashdata('msg', 'Password Salah');
                return redirect()->to('/auth/login');
            }
        }else{
            $session->setFlashdata('msg', 'Username tidak ditemukan');
            return redirect()->to('/auth/login');
        }
    }

    public function register()
    {
        return view('auth/login', ['is_register' => true]);
    }

    public function processRegister()
    {
        $rules = [
            'username' => 'required|min_length[4]|max_length[100]|is_unique[users.username]',
            'password' => 'required|min_length[6]',
            'confpassword' => 'matches[password]'
        ];
          
        if($this->validate($rules)){
            $model = new UserModel();
            $username = $this->request->getVar('username');
            $data = [
                'username' => $username,
                'name'     => $username,
                'role'     => 'user',
                'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT)
            ];
            $model->save($data);
            return redirect()->to('/auth/login')->with('success', 'Registrasi berhasil. Silakan login.');
        }else{
            $data['validation'] = $this->validator;
            $data['is_register'] = true;
            return view('auth/login', $data);
        }
    }
    
    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/auth/login');
    }
}
