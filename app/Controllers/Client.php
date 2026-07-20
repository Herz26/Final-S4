<?php

namespace App\Controllers;

use App\Models\AccountModel;
use App\Models\TransactionModel;
use App\Models\OperatorFeeModel;
use App\Models\OperationTypeModel;
use App\Models\ClientModel;
use App\Models\TransferModel;

class Client extends BaseController
{
    public function dashboard()
    {
        $clientId = session()->get('client_id');
        $accountModel = new AccountModel();
        $account = $accountModel->where('client_id', $clientId)->first();

        return view('client/dashboard', ['account' => $account]);
    }

    public function depot()
    {
        if ($this->request->getMethod() === 'POST') {
            $amount = (float) $this->request->getPost('amount');

            if ($amount <= 0) {
                return redirect()->back()->with('error', 'Le montant doit être positif.');
            }

            $clientId = session()->get('client_id');
            $accountModel = new AccountModel();
            $account = $accountModel->where('client_id', $clientId)->first();

            $accountModel->update($account['id'], ['solde' => $account['solde'] + $amount]);

            $transactionModel = new TransactionModel();
            $transactionModel->insert([
                'account_id'       => $account['id'],
                'operation_type_id' => 1,
                'amount'            => $amount,
                'fee'               => 0.00,
                'total_debited'     => $amount,
                'description'       => 'Dépôt',
                'created_at'        => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/client/dashboard')->with('success', 'Dépôt effectué avec succès.');
        }

        return view('client/depot');
    }

    public function retrait()
    {
        if ($this->request->getMethod() === 'POST') {
            $amount = (float) $this->request->getPost('amount');

            if ($amount <= 0) {
                return redirect()->back()->with('error', 'Le montant doit être positif.');
            }

            $clientId = session()->get('client_id');
            $accountModel = new AccountModel();
            $account = $accountModel->where('client_id', $clientId)->first();

            $operatorId = session()->get('operator_id');
            $feeModel = new OperatorFeeModel();
            $fee = $feeModel->where('operator_id', $operatorId)
                ->where('operation_type_id', 2)
                ->where('min_amount <=', $amount)
                ->where('max_amount >=', $amount)
                ->first();

            if (!$fee) {
                return redirect()->back()->with('error', 'Aucun barème de frais trouvé pour ce montant.');
            }

            $total = $amount + $fee['fee'];

            if ($account['solde'] < $total) {
                return redirect()->back()->with('error', 'Solde insuffisant. Vous avez besoin de ' . $total . ' Ar (montant + frais).');
            }

            $accountModel->update($account['id'], ['solde' => $account['solde'] - $total]);

            $transactionModel = new TransactionModel();
            $transactionModel->insert([
                'account_id'       => $account['id'],
                'operation_type_id' => 2,
                'amount'            => $amount,
                'fee'               => $fee['fee'],
                'total_debited'     => $total,
                'description'       => 'Retrait',
                'created_at'        => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/client/dashboard')->with('success', 'Retrait effectué avec succès. Frais: ' . $fee['fee'] . ' Ar');
        }

        return view('client/retrait');
    }

    public function transfert()
    {
        if ($this->request->getMethod() === 'POST') {
            $toPhone = $this->request->getPost('to_phone');
            $amount = (float) $this->request->getPost('amount');

            if ($amount <= 0) {
                return redirect()->back()->with('error', 'Le montant doit être positif.');
            }

            $clientModel = new ClientModel();
            $toClient = $clientModel->where('phone', $toPhone)->first();

            if (!$toClient) {
                return redirect()->back()->with('error', 'Destinataire non trouvé.');
            }

            if ($toClient['id'] == session()->get('client_id')) {
                return redirect()->back()->with('error', 'Vous ne pouvez pas transférer à vous-même.');
            }

            $clientId = session()->get('client_id');
            $accountModel = new AccountModel();
            $fromAccount = $accountModel->where('client_id', $clientId)->first();
            $toAccount = $accountModel->where('client_id', $toClient['id'])->first();

            $operatorId = session()->get('operator_id');
            $feeModel = new OperatorFeeModel();
            $fee = $feeModel->where('operator_id', $operatorId)
                ->where('operation_type_id', 3)
                ->where('min_amount <=', $amount)
                ->where('max_amount >=', $amount)
                ->first();

            if (!$fee) {
                return redirect()->back()->with('error', 'Aucun barème de frais trouvé pour ce montant.');
            }

            $total = $amount + $fee['fee'];

            if ($fromAccount['solde'] < $total) {
                return redirect()->back()->with('error', 'Solde insuffisant. Vous avez besoin de ' . $total . ' Ar (montant + frais).');
            }

            $accountModel->update($fromAccount['id'], ['solde' => $fromAccount['solde'] - $total]);
            $accountModel->update($toAccount['id'], ['solde' => $toAccount['solde'] + $amount]);

            $transactionModel = new TransactionModel();
            $transactionId = $transactionModel->insert([
                'account_id'       => $fromAccount['id'],
                'operation_type_id' => 3,
                'amount'            => $amount,
                'fee'               => $fee['fee'],
                'total_debited'     => $total,
                'description'       => 'Transfert vers ' . $toPhone,
                'created_at'        => date('Y-m-d H:i:s'),
            ]);

            $transferModel = new TransferModel();
            $transferModel->insert([
                'transaction_id'  => $transactionId,
                'from_client_id'  => $clientId,
                'to_client_id'    => $toClient['id'],
                'from_phone'      => session()->get('client_phone'),
                'to_phone'        => $toPhone,
                'created_at'      => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/client/dashboard')->with('success', 'Transfert effectué avec succès vers ' . $toPhone . '. Frais: ' . $fee['fee'] . ' Ar');
        }

        return view('client/transfert');
    }

    public function historique()
    {
        $clientId = session()->get('client_id');
        $accountModel = new AccountModel();
        $account = $accountModel->where('client_id', $clientId)->first();

        $transactionModel = new TransactionModel();
        $transactions = $transactionModel->select('transactions.*, operation_types.name as operation_name')
            ->join('operation_types', 'operation_types.id = transactions.operation_type_id')
            ->where('account_id', $account['id'])
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('client/historique', ['transactions' => $transactions]);
    }
}
