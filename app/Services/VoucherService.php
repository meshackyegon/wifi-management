<?php

namespace App\Services;

use App\Models\Voucher;
use App\Models\VoucherPlan;
use App\Models\User;
use App\Models\Router;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Carbon\Carbon;

class VoucherService
{
    /**
     * Generate vouchers for a plan
     */
    public function generateVouchers(VoucherPlan $plan, int $quantity, User $user = null, Router $router = null)
    {
        $vouchers = [];
        $totalCommission = 0;

        for ($i = 0; $i < $quantity; $i++) {
            $voucher = new Voucher();
            $voucher->voucher_plan_id = $plan->id;
            $voucher->user_id = $user?->id;
            $voucher->router_id = $router?->id;
            $voucher->price = $plan->price;
            $voucher->commission = $plan->price * (($user?->commission_rate ?? 3) / 100);
            $voucher->generateUniqueCode();
            
            // Set expiration if plan has duration
            if ($plan->duration_hours) {
                $voucher->expires_at = now()->addHours($plan->duration_hours);
            }

            $voucher->save();
            $vouchers[] = $voucher;
            $totalCommission += $voucher->commission;
        }

        // Update user balance if user is provided
        if ($user) {
            $user->updateBalance($totalCommission, "Commission from {$quantity} voucher(s) generated", $vouchers[0]);
        }

        return $vouchers;
    }

    /**
     * Use a voucher
     */
    public function useVoucher(string $code, string $phone = null, string $macAddress = null)
    {
        $voucher = Voucher::where('code', $code)->first();

        if (!$voucher) {
            throw new \Exception('Voucher not found');
        }

        if ($voucher->status !== 'active') {
            throw new \Exception('Voucher is not active');
        }

        if ($voucher->isExpired()) {
            $voucher->markAsExpired();
            throw new \Exception('Voucher has expired');
        }

        $voucher->markAsUsed($phone, $macAddress);

        // Create hotspot user on router if router is available
        if ($voucher->router) {
            $username = $voucher->code;
            $password = $voucher->password ?: $voucher->code;
            $voucher->router->createHotspotUser($username, $password, $voucher->voucherPlan->name);
        }

        return $voucher;
    }

    /**
     * Check voucher status
     */
    public function checkVoucherStatus(string $code)
    {
        $voucher = Voucher::where('code', $code)->with(['voucherPlan', 'router'])->first();

        if (!$voucher) {
            return [
                'found' => false,
                'message' => 'Voucher not found'
            ];
        }

        return [
            'found' => true,
            'voucher' => $voucher,
            'status' => $voucher->status,
            'plan' => $voucher->voucherPlan,
            'remaining_time' => $voucher->remaining_time,
            'remaining_data' => $voucher->remaining_data,
            'expires_at' => $voucher->expires_at,
            'is_expired' => $voucher->isExpired(),
        ];
    }

    /**
     * Print vouchers (mark as printed)
     */
    public function markVouchersAsPrinted(array $voucherIds)
    {
        return Voucher::whereIn('id', $voucherIds)->update([
            'is_printed' => true,
            'printed_at' => now(),
        ]);
    }

    /**
     * Get voucher statistics
     */
    public function getVoucherStats(User $user = null)
    {
        $query = Voucher::query();

        if ($user && !$user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        return [
            'total' => $query->count(),
            'active' => $query->where('status', 'active')->count(),
            'used' => $query->where('status', 'used')->count(),
            'expired' => $query->where('status', 'expired')->count(),
            'revenue' => $query->where('status', 'used')->sum('price'),
            'commission_earned' => $query->where('status', 'used')->sum('commission'),
        ];
    }

    /**
     * Expire old vouchers
     */
    public function expireOldVouchers()
    {
        $expiredCount = Voucher::where('status', 'active')
            ->where('expires_at', '<', now())
            ->update(['status' => 'expired']);

        return $expiredCount;
    }

    /**
     * Generate bulk vouchers from CSV
     */
    public function generateBulkVouchers(array $data, User $user = null)
    {
        $vouchers = [];
        
        foreach ($data as $row) {
            $plan = VoucherPlan::find($row['plan_id']);
            if (!$plan) continue;

            $quantity = $row['quantity'] ?? 1;
            $router = isset($row['router_id']) ? Router::find($row['router_id']) : null;

            $generatedVouchers = $this->generateVouchers($plan, $quantity, $user, $router);
            $vouchers = array_merge($vouchers, $generatedVouchers);
        }

        return $vouchers;
    }
}
