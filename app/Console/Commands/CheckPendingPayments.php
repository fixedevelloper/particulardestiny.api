<?php

namespace App\Console\Commands;


use App\Http\Services\TransactService;
use Illuminate\Console\Command;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class CheckPendingPayments extends Command
{
    protected $signature = 'payments:check-pending';

    protected $description = 'Vérifie les paiements Tranzak en attente';

    public function handle(TransactService $tranzakService)
    {
        $payments = Payment::where('status', 'pending')
            ->whereNotNull('provider_id')
            ->get();

        $this->info("Paiements à vérifier : " . $payments->count());

        foreach ($payments as $payment) {

            try {

                $status = $tranzakService->collectionStatus([
                    'requestId' => $payment->provider_id
                ]);

                $transactionStatus = $status['status'] ?? null;

                if ($transactionStatus === 'SUCCESSFUL') {

                    DB::transaction(function () use ($payment, $status) {

                        $payment->update([
                            'status' => 'paid',
                            'provider_response' => json_encode($status)
                        ]);

                        $payment->reservation->update([
                            'payment_status' => 'paid',
                            'status' => 'confirmed'
                        ]);

                    });

                    $this->info("Paiement confirmé : {$payment->id}");
                }

                if ($transactionStatus === 'FAILED' || $transactionStatus == 'CANCELLED') {

                    $payment->update([
                        'status' => 'failed',
                        'provider_response' => json_encode($status)
                    ]);
                    $payment->reservation->update([
                        'payment_status' => 'failed',
                        'status' => 'cancelled'
                    ]);
                    $this->warn("Paiement échoué : {$payment->id}");
                }

            } catch (\Exception $e) {

                $this->error("Erreur paiement {$payment->id} : " . $e->getMessage());
            }
        }

        return Command::SUCCESS;
    }
}
