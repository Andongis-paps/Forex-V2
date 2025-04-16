<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RsetController extends Controller {
    public function __invoke(Request $request): JsonResponse {
        $BranchID = User::getBranchID();

        $branchRset = DB::connection('laravelsysconfigs')
            ->table('tblxrsetcontroller')
            ->where('BranchID', '=', $BranchID)
            ->exists();

        if (!$branchRset) {
            DB::connection('laravelsysconfigs')->table('tblxrsetcontroller')
                ->insert([
                    'BranchID'  => $BranchID,
                    'RSet'      => 'B',
                    'CreatedAt' => now(),
                    'UpdatedAt' => now(),
                    'CreatedBy' => Auth::id(),
                ]);

            session('Rset') == 'B';

            return response()->json([
                'success' => true,
                'rset'    => 'B',
            ], 200);
        }

        $currentRset = DB::connection('laravelsysconfigs')
            ->table('tblxrsetcontroller')
            ->where('BranchID', '=', $BranchID)
            ->value('RSet');

        session(['Rset' => $currentRset]);

        return response()->json([
            'success' => true,
            'rset'    => $currentRset,
        ], 200);
    }
}
