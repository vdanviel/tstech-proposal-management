<?php

namespace App\Http\Controllers\V1;

use App\Enums\AppErrorType;
use App\Enums\ProposalAuditEvent;
use App\Enums\ProposalOrigin;
use App\Enums\ProposalStatus;
use App\Models\Client;
use App\Models\Proposal;
use App\Models\ProposalAudit;
use Dedoc\Scramble\Attributes\HeaderParameter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProposalController extends Controller
{

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'integer|required',
            'perPage' => 'integer|required',
            'sort' => 'string',
            'product' => 'string',
            'status' => 'string',
            'origin' => 'string',
            'monthlyOp' => 'string|in:>,<,=,>=,<=',
            'monthlyValue' => 'numeric',
            'clientId' => 'numeric'
        ], [
            'page.required' => 'O campo página é obrigatório.',
            'page.integer' => 'O campo página deve ser um número inteiro.',
            'perPage.required' => 'O campo itens por página é obrigatório.',
            'perPage.integer' => 'O campo itens por página deve ser um número inteiro.',
            'sort.string' => 'O campo ordenação deve ser um texto.',
            'product.string' => 'O campo produto deve ser um texto.',
            'status.string' => 'O campo status deve ser um texto.',
            'origin.string' => 'O campo origem deve ser um texto.',
            'monthlyOp.string' => 'O campo operador mensal deve ser um texto.',
            'monthlyOp.in' => 'O operador mensal deve ser um dos seguintes: >, <, =, >=, <=.',
            'monthlyValue.numeric' => 'O campo valor mensal deve ser numérico.',
            'clientId.numeric' => 'O campo ID do cliente deve ser numérico.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'type' => AppErrorType::VALIDATION->value,
                'error' => $validator->errors()
            ], 422);
        }

        $page = $request->query('page', 1);
        $perPage = $request->query('perPage', 15);
        $sort = $request->query('sort', 'asc');
        $product = $request->query('product');
        $status = $request->query('status');
        $origin = $request->query('origin');
        $monthlyOp = $request->query('monthlyOp');
        $monthlyValue = $request->query('monthlyValue');
        $clientId = $request->query('clientId');

        $proposal = new Proposal();
        $query = $proposal->newQueryWithoutScopes();

        if ($product) {
            $query->where('product', 'like', "%{$product}%");
        }

        if ($status) {
            $query->where('status', '=', $status);
        }

        if ($origin) {
            $query->where('origin', '=', $origin);
        }

        if ($clientId) {
            $query->where('client_id', '=', $clientId);
        }

        if ($monthlyOp && $monthlyValue !== null) {
            $query->where('monthly_value', $monthlyOp, $monthlyValue);
        }

        $query->orderBy('created_at', $sort === 'desc' ? 'desc' : 'asc');

        $paginator = $query->paginate(perPage: $perPage, page: $page);

        return response()->json([
            "data" => $paginator->items(),
            "current" => $paginator->currentPage(),
            "perPage" => $paginator->count(),
            "lastPage" => $paginator->lastPage()
        ], 200);
    }

    #[HeaderParameter('Idempotency-Key', description: 'API key for idempotency.', type: 'string', required: false)]
    public function store(Request $request ){

        $validator = Validator::make($request->all(),
            [
                'clientId' => 'integer|required',
                'product' => 'string|required',
                'monthlyValue' => 'decimal:2|required',
                'origin' => [Rule::enum(ProposalOrigin::class), 'required'],
            ],
            [
                'clientId.required' => 'O campo ID do cliente é obrigatório.',
                'clientId.integer' => 'O campo ID do cliente deve ser um número inteiro.',
                'product.required' => 'O campo produto é obrigatório.',
                'product.string' => 'O campo produto deve ser um texto.',
                'monthlyValue.required' => 'O campo valor mensal é obrigatório.',
                'monthlyValue.decimal' => 'O campo valor mensal deve ter exatamente 2 casas decimais.',
                'origin.required' => 'O campo origem é obrigatório.',
                'origin.enum' => 'As origens aceitas são: ' . implode(', ', array_map(fn($c) => $c->value, ProposalOrigin::cases())) . '.'
            ]
        );

        if ($validator->fails()) {
            return response()->json(
            [
                'type' => AppErrorType::VALIDATION->value,
                'error' => $validator->errors()
            ], 422);
        }

        $validatedInputs = $validator->validate();

        //verificar se cliente existe...
        $client = Client::find($validatedInputs['clientId'], ['*']);

        if (!$client) {
            return response()->json([
                'type' => AppErrorType::NOTFOUND->value,
                'error' => "O cliente não existe."
            ], 404);
        }

        $proposal = new Proposal();

        $proposal->client_id = $validatedInputs['clientId'];
        $proposal->product = $validatedInputs['product'];
        $proposal->status = ProposalStatus::DRAFT;
        $proposal->monthly_value = $validatedInputs['monthlyValue'];
        $proposal->origin = $validatedInputs['origin'];

        $proposal->save();

        //cria auditoria...
        $user = $request->user();
        ProposalAudit::create([
            'proposal_id' => $proposal->id,
            'actor' => explode(" ", $user->name)[0] . ":" . $user->id,
            'event' => ProposalAuditEvent::CREATED->value,
            'payload' => $proposal->toJson()
        ]);

        return response()->json($proposal, 201);

    }

    #[HeaderParameter('Idempotency-Key', description: 'API key for idempotency.', type: 'string', required: false)]
    public function update(Request $request){

        $validator = Validator::make($request->all(),
            [
                'clientId' => 'integer|required',
                'product' => 'string|required',
                'monthlyValue' => 'decimal:2|required',
                'origin' => [Rule::enum(ProposalOrigin::class), 'required'],
            ],
            [
                'clientId.required' => 'O campo ID do cliente é obrigatório.',
                'clientId.integer' => 'O campo ID do cliente deve ser um número inteiro.',
                'product.required' => 'O campo produto é obrigatório.',
                'product.string' => 'O campo produto deve ser um texto.',
                'monthlyValue.required' => 'O campo valor mensal é obrigatório.',
                'monthlyValue.decimal' => 'O campo valor mensal deve ter exatamente 2 casas decimais.',
                'origin.required' => 'O campo origem é obrigatório.',
                'origin.enum' => 'As origens aceitas são: ' . implode(', ', array_column(ProposalOrigin::cases(), 'value')) . '.'
            ]
        );

        if ($validator->fails()) {
            return response()->json(
            [
                'type' => AppErrorType::VALIDATION->value,
                'error' => $validator->errors()
            ], 422);
        }

        $validatedInputs = $validator->validate();

        //atualiza dados...
        $proposal = Proposal::find($request->route('id'), ['*']);

        if (!$proposal) {
            return response()->json([
                'type' => AppErrorType::NOTFOUND->value,
                'error' => "A proposta não existe."
            ], 404);
        }

        //verificar se cliente existe...
        $client = Client::find($validatedInputs['clientId'] , ['*']);

        //verificar se cliente existe...
        if (!$client) {
            return response()->json([
                'type' => AppErrorType::NOTFOUND->value,
                'error' => "O cliente não existe."
            ], 404);
        }

        $proposal->client_id = $client->id;
        $proposal->product = $validatedInputs['product'];
        $proposal->monthly_value = $validatedInputs['monthlyValue'];
        $proposal->origin = $validatedInputs['origin'];

        $proposal->save();

        //cria auditoria...
        $user = $request->user();
        ProposalAudit::create([
            'proposal_id' => $proposal->id,
            'actor' => explode(" ", $user->name)[0] . ":" . $user->id,
            'event' => ProposalAuditEvent::UPDATED_FIELDS->value,
            'payload' => $proposal->toJson()
        ]);

        return response()->json($proposal, 200);

    }

    public function show(Request $request){

        $found = Proposal::find($request->route('id'), ['*']);

        if (!$found) {
            return response()->json([
                'error' => "A proposta não existe."
            ], 404);
        }

        return response()->json($found,200);

    }

    #[HeaderParameter('Idempotency-Key', description: 'API key for idempotency.', type: 'string', required: false)]
    public function submit(Request $request){

        $proposal = Proposal::find($request->route('id'), ['*']);

        if (!$proposal) {
            return response()->json([
                'type' => AppErrorType::NOTFOUND->value,
                'error' => "A proposta não existe."
            ], 404);
        }

        $able = $proposal->status->ableToTransitionStatus(ProposalStatus::SUBMITTED);

        if (!$able) {
            return response()->json([
                'type' => AppErrorType::INVALID_TRANSITION->value,
                'error' => "Não é possível enviar uma proposta com status atual '{$proposal->status->value}'."
            ], 422);
        }

        $proposal->status = ProposalStatus::SUBMITTED;
        $proposal->save();

        //cria auditoria...
        $user = $request->user();
        ProposalAudit::create([
            'proposal_id' => $proposal->id,
            'actor' => explode(" ", $user->name)[0] . ":" . $user->id,
            'event' => ProposalAuditEvent::STATUS_CHANGED->value,
            'payload' => $proposal->toJson()
        ]);

        return response()->json($proposal, 200);

    }

    #[HeaderParameter('Idempotency-Key', description: 'API key for idempotency.', type: 'string', required: false)]
    public function approve(Request $request){

        $proposal = Proposal::find($request->route('id'), ['*']);

        if (!$proposal) {
            return response()->json([
                'type' => AppErrorType::NOTFOUND->value,
                'error' => "A proposta não existe."
            ], 404);
        }

        $able = $proposal->status->ableToTransitionStatus(ProposalStatus::APPROVED);

        if (!$able) {
            return response()->json([
                'type' => AppErrorType::INVALID_TRANSITION->value,
                'error' => "Não é possível enviar uma proposta com status atual '{$proposal->status->value}'."
            ], 422);
        }

        $proposal->status = ProposalStatus::APPROVED;
        $proposal->save();

        //cria auditoria...
        $user = $request->user();
        ProposalAudit::create([
            'proposal_id' => $proposal->id,
            'actor' => explode(" ", $user->name)[0] . ":" . $user->id,
            'event' => ProposalAuditEvent::STATUS_CHANGED->value,
            'payload' => $proposal->toJson()
        ]);

        return response()->json($proposal, 200);

    }

    #[HeaderParameter('Idempotency-Key', description: 'API key for idempotency.', type: 'string', required: false)]
    public function reject(Request $request){

        $proposal = Proposal::find($request->route('id'), ['*']);

        if (!$proposal) {
            return response()->json([
                'type' => AppErrorType::NOTFOUND->value,
                'error' => "A proposta não existe."
            ], 404);
        }

        $able = $proposal->status->ableToTransitionStatus(ProposalStatus::REJECTED);

        if (!$able) {
            return response()->json([
                'type' => AppErrorType::INVALID_TRANSITION->value,
                'error' => "Não é possível enviar uma proposta com status atual '{$proposal->status->value}'."
            ], 422);
        }

        $proposal->status = ProposalStatus::REJECTED;
        $proposal->save();

        //cria auditoria...
        $user = $request->user();
        ProposalAudit::create([
            'proposal_id' => $proposal->id,
            'actor' => explode(" ", $user->name)[0] . ":" . $user->id,
            'event' => ProposalAuditEvent::STATUS_CHANGED->value,
            'payload' => $proposal->toJson()
        ]);

        return response()->json($proposal, 200);

    }

    #[HeaderParameter('Idempotency-Key', description: 'API key for idempotency.', type: 'string', required: false)]
    public function cancel(Request $request){

        $proposal = Proposal::find($request->route('id'), ['*']);

        if (!$proposal) {
            return response()->json([
                'type' => AppErrorType::NOTFOUND->value,
                'error' => "A proposta não existe."
            ], 404);
        }

        $able = $proposal->status->ableToTransitionStatus(ProposalStatus::CANCELED);

        if (!$able) {
            return response()->json([
                'type' => AppErrorType::INVALID_TRANSITION->value,
                'error' => "Não é possível enviar uma proposta com status atual '{$proposal->status->value}'."
            ], 422);
        }

        //salva o estado antes de remover...
        $proposal->status = ProposalStatus::CANCELED;
        $proposal->save();

        //cria auditoria...
        $user = $request->user();
        ProposalAudit::create([
            'proposal_id' => $proposal->id,
            'actor' => explode(" ", $user->name)[0] . ":" . $user->id,
            'event' => ProposalAuditEvent::STATUS_CHANGED->value,
            'payload' => $proposal->toJson()
        ]);

        //remove com soft delete...
        $proposal->delete();

        return response()->json($proposal, 200);

    }

}
