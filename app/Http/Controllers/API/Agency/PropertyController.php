<?php

namespace App\Http\Controllers\API\Agency;

use App\Http\Controllers\Controller;
use App\Http\Resources\APIResponse;
use Illuminate\Support\Facades\Auth;
use App\Model\Property\Property;
use App\Enums\Status;
use App\Enums\MarketStatus;
use Validator;

class PropertyController extends Controller
{
    public function summary()
    {
        $user = Auth::user();
        $agencyId = $user->agency->id;
        $list = Property::with(['analytics'])
            ->whereIn('status', [Status::Draft, Status::Published])
            ->where('agency_id', $agencyId)->get();
        $clicks = 0;
        foreach ($list as $p) {
            $clicks += $p->analytics->clickout;
        }
        return APIResponse::success([
            'active' => collect($list)->where('status', Status::Published)->count(),
            'pause' => collect($list)->where('status', Status::Draft)->count(),
            'total' => collect($list)->count(),
            'sync' => collect($list)->where('sync_id', '!=', NULL)->count(),
            'sold' => collect($list)->where('market_status', MarketStatus::Sold)->count(),
            'clicks' => $clicks,
        ]);
    }
}
