<?php

namespace App\Http\Controllers\V1;

use App\Enums\AppErrorType;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{

    public function store(Request $request){

        $validator = Validator::make($request->all(),
            [
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:client,email',
                'document' => 'required|string',
            ],
            [
                'name.required'     => 'O campo nome é obrigatório.',
                'name.string'       => 'O campo nome deve ser um texto.',
                'name.max'          => 'O campo nome não pode ter mais de 255 caracteres.',
                'email.required'    => 'O campo e-mail é obrigatório.',
                'email.email'       => 'Informe um endereço de e-mail válido.',
                'email.unique'      => 'Já existe um cliente com este e-mail.',
                'document.required' => 'O campo documento é obrigatório.',
                'document.string'   => 'O campo documento deve ser um texto.',
                'password.min'      => 'A senha deve ter pelo menos 8 caracteres.',
            ]
        );

        if ($validator->fails()) {
            return response()->json(
            [
                'type' => AppErrorType::VALIDATION->value,
                'error' => $validator->errors()
            ], 422);
        }

        $validatedData = $validator->validated();

        $client = new Client();

        $client->name = $validatedData['name'];
        $client->email = $validatedData['email'];
        $client->document = preg_replace('/\D/', '', $validatedData['document']);//tirar mascara do documento

        $client->save();

        return response()->json($client, 201);

    }

    public function show(Request $request){

        $client = Client::find($request->route('id'), ['id','name', 'email', 'document', 'created_at', 'updated_at']);

        if (!$client) {
            return response()->json([
                'type' => AppErrorType::NOTFOUND->value,
                'error' => 'Cliente não encontrado.'
            ], 404);
        }

        return response()->json($client, 200);

    }

}
