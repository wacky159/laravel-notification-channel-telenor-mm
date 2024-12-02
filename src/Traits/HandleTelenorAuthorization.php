<?php

namespace Wacky159\TelenorMM\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

trait HandleTelenorAuthorization
{
    public function handleTelenorCallback(Request $request)
    {
        $code = $request->query('code');
        $scope = $request->query('scope');

        Cache::put('telenor_mm_authorization_code', $code, 86400);
        Cache::put("auth_code_scope_{$scope}", $code, 300);

        return response()->json(['message' => 'Authorization successful']);
    }
}
