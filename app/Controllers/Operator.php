<?php

namespace App\Controllers;

use App\Models\OperatorModel;
use App\Models\ClientModel;
use App\Models\AccountModel;
use App\Models\OperationTypeModel;
use App\Models\OperatorFeeModel;
use App\Models\TransactionModel;
use App\Models\TransferModel;

class Operator extends BaseController
{
    public function index()
    {
        $operatorId = session()->get('operator_id');

        $transactionModel = new TransactionModel();
        $totalTransactions = $transactionModel->countAllResults();

        $clientModel = new ClientModel();
        $totalClients = $clientModel->countAllResults();

        $accountModel = new AccountModel();
        $totalSolde = $accountModel->selectSum('solde')->first()['solde'] ?? 0;

        $operatorModel = new OperatorModel();
        $operators = $operatorModel->findAll();

        return view('operator/dashboard', [
            'total_transactions' => $totalTransactions,
            'total_clients' => $totalClients,
            'total_solde' => $totalSolde,
            'operators' => $operators,
        ]);
    }

    public function prefixes()
    {
        $operatorModel = new OperatorModel();
        $prefixes = $operatorModel->findAll();

        if ($this->request->getMethod() === 'POST') {
            $name = $this->request->getPost('name');
            $prefix = $this->request->getPost('prefix');

            $existing = $operatorModel->where('prefix', $prefix)->first();
            if ($existing) {
                return redirect()->back()->with('error', 'Ce préfixe existe déjà.');
            }

            $operatorModel->insert([
                'name' => $name,
                'prefix' => $prefix,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/operator/prefixes')->with('success', 'Préfixe ajouté avec succès.');
        }

        return view('operator/prefixes', ['prefixes' => $prefixes]);
    }

    public function fees()
    {
        $operatorModel = new OperatorModel();
        $operators = $operatorModel->findAll();

        $operationTypeModel = new OperationTypeModel();
        $types = $operationTypeModel->findAll();

        $operatorFeeModel = new OperatorFeeModel();
        $fees = $operatorFeeModel->select('operator_fees.*, operators.name as operator_name, operation_types.name as operation_name')
            ->join('operators', 'operators.id = operator_fees.operator_id')
            ->join('operation_types', 'operation_types.id = operator_fees.operation_type_id')
            ->orderBy('operator_fees.operator_id', 'ASC')
            ->orderBy('operator_fees.operation_type_id', 'ASC')
            ->orderBy('operator_fees.min_amount', 'ASC')
            ->findAll();

        if ($this->request->getMethod() === 'POST') {
            $operatorId = $this->request->getPost('operator_id');
            $operationTypeId = $this->request->getPost('operation_type_id');
            $minAmount = (float) $this->request->getPost('min_amount');
            $maxAmount = (float) $this->request->getPost('max_amount');
            $fee = (float) $this->request->getPost('fee');

            $operatorFeeModel->insert([
                'operator_id' => $operatorId,
                'operation_type_id' => $operationTypeId,
                'min_amount' => $minAmount,
                'max_amount' => $maxAmount,
                'fee' => $fee,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/operator/fees')->with('success', 'Barème ajouté avec succès.');
        }

        return view('operator/fees', [
            'operators' => $operators,
            'types' => $types,
            'fees' => $fees,
        ]);
    }

    public function operationTypes()
    {
        $operationTypeModel = new OperationTypeModel();
        $types = $operationTypeModel->findAll();

        if ($this->request->getMethod() === 'POST') {
            $name = $this->request->getPost('name');
            $description = $this->request->getPost('description');

            $operationTypeModel->insert([
                'name' => $name,
                'description' => $description,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/operator/operation-types')->with('success', 'Type d\'opération ajouté avec succès.');
        }

        return view('operator/operation_types', ['types' => $types]);
    }

    public function gains()
    {
        $transactionModel = new TransactionModel();
        $gains = $transactionModel->select('
                operators.name as operator_name,
                operation_types.name as operation_name,
                SUM(transactions.fee) as total_fees
            ')
            ->join('accounts', 'accounts.id = transactions.account_id')
            ->join('operators', 'operators.id = accounts.operator_id')
            ->join('operation_types', 'operation_types.id = transactions.operation_type_id')
            ->where('transactions.fee >', 0)
            ->groupBy('accounts.operator_id, transactions.operation_type_id')
            ->findAll();

        return view('operator/gains', ['gains' => $gains]);
    }

    public function comptes()
    {
        $accountModel = new AccountModel();
        $comptes = $accountModel->select('accounts.*, clients.phone, operators.name as operator_name')
            ->join('clients', 'clients.id = accounts.client_id')
            ->join('operators', 'operators.id = accounts.operator_id')
            ->orderBy('accounts.solde', 'DESC')
            ->findAll();

        return view('operator/comptes', ['comptes' => $comptes]);
    }

    public function transactions()
    {
        $transactionModel = new TransactionModel();
        $transactions = $transactionModel->select('transactions.*, clients.phone, operation_types.name as operation_name')
            ->join('accounts', 'accounts.id = transactions.account_id')
            ->join('clients', 'clients.id = accounts.client_id')
            ->join('operation_types', 'operation_types.id = transactions.operation_type_id')
            ->orderBy('transactions.created_at', 'DESC')
            ->findAll();

        return view('operator/transactions', ['transactions' => $transactions]);
    }

    public function login()
    {
        if ($this->request->getMethod() === 'POST') {
            $pin = $this->request->getPost('pin');

            if ($pin === '0000') {
                $operatorModel = new OperatorModel();
                $operator = $operatorModel->first();

                if ($operator) {
                    session()->set([
                        'operator_id' => $operator['id'],
                        'operator_name' => $operator['name'],
                    ]);
                    return redirect()->to('/operator');
                }
            }

            return redirect()->back()->with('error', 'PIN incorrect.');
        }

        return view('operator/login');
    }

    public function logout()
    {
        session()->remove('operator_id');
        session()->remove('operator_name');
        return redirect()->to('/operator/login');
    }
}
