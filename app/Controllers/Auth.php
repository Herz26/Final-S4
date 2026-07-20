<?php

namespace App\Controllers;

use App\Models\OperatorModel;
use App\Models\ClientModel;
use App\Models\AccountModel;

class Auth extends BaseController
{
    public function index()
    {
        $session = session();
        if ($session->get('client_id')) {
            return redirect()->to('/client/dashboard');
        }
        return view('auth/login');
    }

    public function login()
    {
        $session = session();
        $validation = \Config\Services::validation();

        $rules = [
            'phone' => 'required|min_length[8]|max_length[15]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $phone = $this->request->getPost('phone');
        $operatorPrefix = substr($phone, 0, 3);

        $operatorModel = new OperatorModel();
        $operator = $operatorModel->where('prefix', $operatorPrefix)->first();

        if (! $operator) {
            return redirect()->back()->withInput()->with('error', 'Numéro de téléphone invalide : préfixe non reconnu.');
        }

        $clientModel = new ClientModel();
        $client = $clientModel->where('phone', $phone)->first();

        if (! $client) {
            $clientId = $clientModel->insert([
                'phone'       => $phone,
                'operator_id' => $operator['id'],
                'nom'         => 'Client ' . $phone,
                'created_at'  => date('Y-m-d H:i:s'),
            ]);

            $accountModel = new AccountModel();
            $accountModel->insert([
                'client_id'   => $clientId,
                'operator_id' => $operator['id'],
                'solde'       => 0.00,
                'created_at'  => date('Y-m-d H:i:s'),
            ]);
        }

        $session->set([
            'client_id'    => $client ? $client['id'] : $clientId,
            'client_phone' => $phone,
            'operator_id'  => $operator['id'],
        ]);

        return redirect()->to('/client/dashboard');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth');
    }
}
