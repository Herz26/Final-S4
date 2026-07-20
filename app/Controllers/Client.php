<?php

namespace App\Controllers;

use App\Models\AccountModel;
use App\Models\TransactionModel;
use App\Models\OperatorFeeModel;
use App\Models\OperationTypeModel;
use App\Models\ClientModel;
use App\Models\TransferModel;
use App\Models\InterOperatorCommissionModel;

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
            $includeWithdrawalFee = $this->request->getPost('include_withdrawal_fee') ? true : false;

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

            $transferFee = $fee['fee'];
            $interOperatorCommission = 0;

            $toOperatorId = $toClient['operator_id'];
            if ($toOperatorId != $operatorId) {
                $commissionModel = new InterOperatorCommissionModel();
                $commission = $commissionModel->where('from_operator_id', $operatorId)
                    ->where('to_operator_id', $toOperatorId)
                    ->first();
                if ($commission) {
                    $interOperatorCommission = $amount * $commission['commission_percentage'] / 100;
                    $transferFee += $interOperatorCommission;
                }
            }

            $withdrawalFee = 0;

            if ($includeWithdrawalFee) {
                $toOperatorId = $toClient['operator_id'];
                $withdrawalFeeModel = new OperatorFeeModel();
                $withdrawalFee = $withdrawalFeeModel->where('operator_id', $toOperatorId)
                    ->where('operation_type_id', 2)
                    ->where('min_amount <=', $amount)
                    ->where('max_amount >=', $amount)
                    ->first();
                $withdrawalFee = $withdrawalFee ? $withdrawalFee['fee'] : 0;
            }

            $total = $amount + $transferFee + $withdrawalFee;
            $receivedAmount = $amount + $withdrawalFee;

            if ($fromAccount['solde'] < $total) {
                return redirect()->back()->with('error', 'Votre solde est insuffisant pour cette transaction.');
            }

            $accountModel->update($fromAccount['id'], ['solde' => $fromAccount['solde'] - $total]);
            $accountModel->update($toAccount['id'], ['solde' => $toAccount['solde'] + $receivedAmount]);

            $transactionModel = new TransactionModel();
            $transactionId = $transactionModel->insert([
                'account_id'       => $fromAccount['id'],
                'operation_type_id' => 3,
                'amount'            => $amount,
                'fee'               => $transferFee + $withdrawalFee,
                'total_debited'     => $total,
                'description'       => 'Transfert vers ' . $toPhone . ($includeWithdrawalFee ? ' (frais retrait inclus)' : ''),
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

            $msg = 'Transfert effectué avec succès vers ' . $toPhone . '. Frais: ' . ($transferFee + $withdrawalFee) . ' Ar';
            if ($includeWithdrawalFee) {
                $msg .= ' (dont frais retrait: ' . $withdrawalFee . ' Ar';
                if ($interOperatorCommission > 0) {
                    $msg .= ', dont commission inter-opérateur: ' . number_format($interOperatorCommission, 0, ',', ' ') . ' Ar';
                }
                $msg .= ')';
            } elseif ($interOperatorCommission > 0) {
                $msg .= ' (dont commission inter-opérateur: ' . number_format($interOperatorCommission, 0, ',', ' ') . ' Ar)';
            }

            return redirect()->to('/client/dashboard')->with('success', $msg);
        }

        return view('client/transfert');
    }

    public function transfertMultiple()
    {
        if ($this->request->getMethod() === 'POST') {
            $recipients = $this->request->getPost('recipients');
            $totalAmount = (float) $this->request->getPost('total_amount');
            $includeWithdrawalFee = $this->request->getPost('include_withdrawal_fee') ? true : false;

            if ($totalAmount <= 0) {
                return redirect()->back()->with('error', 'Le montant total doit être positif.');
            }

            if (empty($recipients) || !is_array($recipients)) {
                return redirect()->back()->with('error', 'Veuillez ajouter au moins un destinataire.');
            }

            $validRecipients = array_filter($recipients, function($r) {
                return !empty($r['phone']);
            });

            if (empty($validRecipients)) {
                return redirect()->back()->with('error', 'Veuillez ajouter au moins un destinataire valide.');
            }

            $count = count($validRecipients);
            $amountPerRecipient = floor($totalAmount / $count);
            $remainder = $totalAmount - ($amountPerRecipient * $count);

            if ($amountPerRecipient <= 0) {
                return redirect()->back()->with('error', 'Le montant total est trop faible pour être divisé entre les destinataires.');
            }

            $clientId = session()->get('client_id');
            $accountModel = new AccountModel();
            $fromAccount = $accountModel->where('client_id', $clientId)->first();

            $clientModel = new ClientModel();
            $feeModel = new OperatorFeeModel();

            $totalNeeded = 0;
            $recipientData = [];
            $i = 0;

            foreach ($validRecipients as $recipient) {
                $toPhone = $recipient['phone'] ?? '';
                $amount = $amountPerRecipient + ($i === 0 ? $remainder : 0);
                $i++;

                $toClient = $clientModel->where('phone', $toPhone)->first();
                if (!$toClient) {
                    return redirect()->back()->with('error', 'Destinataire non trouvé : ' . $toPhone);
                }

                if ($toClient['id'] == $clientId) {
                    return redirect()->back()->with('error', 'Vous ne pouvez pas transférer à vous-même (' . $toPhone . ').');
                }

                $toAccount = $accountModel->where('client_id', $toClient['id'])->first();

                $operatorId = session()->get('operator_id');
                $toOperatorId = $toClient['operator_id'];
                $fee = $feeModel->where('operator_id', $operatorId)
                    ->where('operation_type_id', 3)
                    ->where('min_amount <=', $amount)
                    ->where('max_amount >=', $amount)
                    ->first();

                if (!$fee) {
                    return redirect()->back()->with('error', 'Aucun barème de frais trouvé pour le montant ' . $amount . ' vers ' . $toPhone);
                }

                $transferFee = $fee['fee'];
                $interOperatorCommission = 0;

                if ($toOperatorId != $operatorId) {
                    $commissionModel = new InterOperatorCommissionModel();
                    $commission = $commissionModel->where('from_operator_id', $operatorId)
                        ->where('to_operator_id', $toOperatorId)
                        ->first();
                    if ($commission) {
                        $interOperatorCommission = $amount * $commission['commission_percentage'] / 100;
                        $transferFee += $interOperatorCommission;
                    }
                }

                $withdrawalFee = 0;

                if ($includeWithdrawalFee) {
                    $toOperatorId = $toClient['operator_id'];
                    $withdrawalFeeModel = new OperatorFeeModel();
                    $withdrawalFee = $withdrawalFeeModel->where('operator_id', $toOperatorId)
                        ->where('operation_type_id', 2)
                        ->where('min_amount <=', $amount)
                        ->where('max_amount >=', $amount)
                        ->first();
                    $withdrawalFee = $withdrawalFee ? $withdrawalFee['fee'] : 0;
                }

                $total = $amount + $transferFee + $withdrawalFee;
                $receivedAmount = $amount + $withdrawalFee;
                $totalNeeded += $total;

                $recipientData[] = [
                    'toClient' => $toClient,
                    'toAccount' => $toAccount,
                    'amount' => $amount,
                    'transferFee' => $transferFee,
                    'withdrawalFee' => $withdrawalFee,
                    'total' => $total,
                    'receivedAmount' => $receivedAmount,
                    'toPhone' => $toPhone,
                ];
            }

            if ($fromAccount['solde'] < $totalNeeded) {
                return redirect()->back()->with('error', 'Votre solde est insuffisant pour cette transaction.');
            }

            $transactionModel = new TransactionModel();
            $transferModel = new TransferModel();

            foreach ($recipientData as $data) {
                $accountModel->update($fromAccount['id'], ['solde' => $fromAccount['solde'] - $data['total']]);
                $accountModel->update($data['toAccount']['id'], ['solde' => $data['toAccount']['solde'] + $data['receivedAmount']]);

                $transactionId = $transactionModel->insert([
                    'account_id'       => $fromAccount['id'],
                    'operation_type_id' => 3,
                    'amount'            => $data['amount'],
                    'fee'               => $data['transferFee'] + $data['withdrawalFee'],
                    'total_debited'     => $data['total'],
                    'description'       => 'Transfert multiple vers ' . $data['toPhone'] . ($includeWithdrawalFee ? ' (frais retrait inclus)' : ''),
                    'created_at'        => date('Y-m-d H:i:s'),
                ]);

                $transferModel->insert([
                    'transaction_id'  => $transactionId,
                    'from_client_id'  => $clientId,
                    'to_client_id'    => $data['toClient']['id'],
                    'from_phone'      => session()->get('client_phone'),
                    'to_phone'        => $data['toPhone'],
                    'created_at'      => date('Y-m-d H:i:s'),
                ]);
            }

            return redirect()->to('/client/dashboard')->with('success', 'Transfert(s) multiple(s) effectué(s) avec succès.');
        }

        return view('client/transfert_multiple');
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
