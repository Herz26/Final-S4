<?php

namespace App\Controllers;

use App\Models\OperatorModel;
use App\Models\ClientModel;
use App\Models\AccountModel;
use App\Models\OperationTypeModel;
use App\Models\OperatorFeeModel;
use App\Models\TransactionModel;
use App\Models\TransferModel;
use App\Models\InterOperatorCommissionModel;

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

    public function commissions()
    {
        $operatorModel = new OperatorModel();
        $operators = $operatorModel->findAll();

        $commissionModel = new InterOperatorCommissionModel();
        $commissions = $commissionModel->select('inter_operator_commissions.*, from_op.name as from_operator_name, to_op.name as to_operator_name')
            ->join('operators as from_op', 'from_op.id = inter_operator_commissions.from_operator_id')
            ->join('operators as to_op', 'to_op.id = inter_operator_commissions.to_operator_id')
            ->orderBy('inter_operator_commissions.from_operator_id', 'ASC')
            ->orderBy('inter_operator_commissions.to_operator_id', 'ASC')
            ->findAll();

        if ($this->request->getMethod() === 'POST') {
            $fromOperatorId = $this->request->getPost('from_operator_id');
            $toOperatorId = $this->request->getPost('to_operator_id');
            $percentage = (float) $this->request->getPost('commission_percentage');

            if ($fromOperatorId == $toOperatorId) {
                return redirect()->back()->with('error', 'Les opérateurs doivent être différents.');
            }

            $existing = $commissionModel->where('from_operator_id', $fromOperatorId)
                ->where('to_operator_id', $toOperatorId)
                ->first();

            if ($existing) {
                $commissionModel->update($existing['id'], ['commission_percentage' => $percentage]);
                return redirect()->to('/operator/commissions')->with('success', 'Commission mise à jour avec succès.');
            }

            $commissionModel->insert([
                'from_operator_id' => $fromOperatorId,
                'to_operator_id' => $toOperatorId,
                'commission_percentage' => $percentage,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/operator/commissions')->with('success', 'Commission ajoutée avec succès.');
        }

        return view('operator/commissions', [
            'operators' => $operators,
            'commissions' => $commissions,
        ]);
    }

    public function gains()
    {
        $operatorId = session()->get('operator_id');

        $transactionModel = new TransactionModel();

        $ownGains = $transactionModel->select('
                operation_types.name as operation_name,
                SUM(transactions.fee) as total_fees
            ')
            ->join('accounts', 'accounts.id = transactions.account_id')
            ->join('operation_types', 'operation_types.id = transactions.operation_type_id')
            ->where('accounts.operator_id', $operatorId)
            ->where('transactions.fee >', 0)
            ->groupBy('transactions.operation_type_id')
            ->findAll();

        $db = \Config\Database::connect();
        $sql = "SELECT to_op.name as operator_name, operation_types.name as operation_name, SUM(transactions.fee) as total_fees
                FROM transactions
                JOIN accounts ON accounts.id = transactions.account_id
                JOIN operators as to_op ON to_op.id = accounts.operator_id
                JOIN operation_types ON operation_types.id = transactions.operation_type_id
                WHERE accounts.operator_id != ? AND transactions.fee > 0
                GROUP BY accounts.operator_id, transactions.operation_type_id";
        $interOperatorGains = $db->query($sql, [$operatorId])->getResultArray();

        return view('operator/gains', [
            'own_gains' => $ownGains,
            'inter_operator_gains' => $interOperatorGains,
        ]);
    }

    public function settlements()
    {
        $operatorId = session()->get('operator_id');

        $db = \Config\Database::connect();

        $outgoingSql = "SELECT to_op.name as operator_name, SUM(transactions.amount) as total_amount
            FROM transactions
            JOIN operation_types ON operation_types.id = transactions.operation_type_id
            JOIN transfers ON transfers.transaction_id = transactions.id
            JOIN accounts AS sender_accounts ON sender_accounts.id = transactions.account_id
            JOIN clients AS to_clients ON to_clients.id = transfers.to_client_id
            JOIN accounts AS to_accounts ON to_accounts.client_id = to_clients.id
            JOIN operators AS to_op ON to_op.id = to_accounts.operator_id
            WHERE operation_types.name = 'transfert'
              AND sender_accounts.operator_id = ?
              AND to_accounts.operator_id != ?
            GROUP BY to_op.name";
        $outgoing = $db->query($outgoingSql, [$operatorId, $operatorId])->getResultArray();

        $incomingSql = "SELECT from_op.name as operator_name, SUM(transactions.amount) as total_amount
            FROM transactions
            JOIN operation_types ON operation_types.id = transactions.operation_type_id
            JOIN transfers ON transfers.transaction_id = transactions.id
            JOIN accounts AS sender_accounts ON sender_accounts.id = transactions.account_id
            JOIN clients AS to_clients ON to_clients.id = transfers.to_client_id
            JOIN accounts AS to_accounts ON to_accounts.client_id = to_clients.id
            JOIN operators AS from_op ON from_op.id = sender_accounts.operator_id
            WHERE operation_types.name = 'transfert'
              AND to_accounts.operator_id = ?
              AND sender_accounts.operator_id != ?
            GROUP BY from_op.name";
        $incoming = $db->query($incomingSql, [$operatorId, $operatorId])->getResultArray();

        $operatorModel = new OperatorModel();
        $operators = $operatorModel->findAll();

        $settlements = [];
        foreach ($operators as $op) {
            if ($op['id'] == $operatorId) {
                continue;
            }
            $out = 0;
            $in = 0;
            foreach ($outgoing as $o) {
                if ($o['operator_name'] == $op['name']) {
                    $out = $o['total_amount'];
                    break;
                }
            }
            foreach ($incoming as $i) {
                if ($i['operator_name'] == $op['name']) {
                    $in = $i['total_amount'];
                    break;
                }
            }
            $net = $in - $out;
            $settlements[] = [
                'operator_name' => $op['name'],
                'outgoing' => $out,
                'incoming' => $in,
                'net' => $net,
            ];
        }

        return view('operator/settlements', ['settlements' => $settlements]);
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
        return redirect()->to('/auth');
    }

    public function logout()
    {
        session()->remove('operator_id');
        session()->remove('operator_name');
        return redirect()->to('/operator/login');
    }
}
