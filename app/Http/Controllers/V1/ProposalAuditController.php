<?php

namespace App\Http\Controllers\V1;

use App\Models\ProposalAudit;
use Illuminate\Http\Request;

class ProposalAuditController extends Controller
{

    public function show(Request $request){

        $found = ProposalAudit::find($request->route('id'), ['*']);

        if (!$found) {
            return response()->json([
                'error' => "A auditoria não existe."
            ], 404);
        }

        return response()->json($found,200);

    }

}
