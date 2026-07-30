<?php

namespace App\Http\Controllers\V1;

use App\Enums\AppErrorType;
use App\Models\ProposalAudit;
use Illuminate\Http\Request;

class ProposalAuditController extends Controller
{

    //acha todas auditorias de uma proposta especifica
    public function show(Request $request){

        $found = ProposalAudit::where('proposal_id','=',$request->route('id'))->get();

        if ($found->isEmpty()) {
            return response()->json([
                'type' => AppErrorType::NOTFOUND->value,
                'error' => "Não há auditorias."
            ], 404);
        }

        return response()->json($found,200);

    }

}
