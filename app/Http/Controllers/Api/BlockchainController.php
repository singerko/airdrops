<?php
// app/Http/Controllers/Api/BlockchainController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blockchain;

class BlockchainController extends Controller
{
    public function index()
    {
        $blockchains = Blockchain::active()
            ->ordered()
            ->withCount(['airdrops'])
            ->get();

        return response()->json($blockchains);
    }
}
