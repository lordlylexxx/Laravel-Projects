<?php

use App\Models\CentralLandingPlan;
use App\Models\Tenant;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $details = Tenant::getPlanDetails();

        CentralLandingPlan::query()->each(function (CentralLandingPlan $plan) use ($details): void {
            if (is_string($plan->title) && $plan->title !== '') {
                return;
            }
            $key = (string) $plan->tenant_plan_key;
            $name = (string) ($details[$key]['name'] ?? 'Plan');
            $plan->update(['title' => $name]);
        });
    }

    public function down(): void
    {
        // Intentionally left blank: do not strip admin-set titles.
    }
};
