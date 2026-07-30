<?php

namespace App\Http\Controllers\V1;

use App\Enums\AppErrorType;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:6', 'max:50'],
        ],
        [
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Informe um endereço de e-mail válido.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.string' => 'O campo senha deve ser um texto.',
            'password.min' => 'A senha deve ter pelo menos 6 caracteres.',
            'password.max' => 'A senha não pode ter mais que 50 caracteres.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'type' => AppErrorType::VALIDATION->value,
                'error' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $user = User::where('email', '=', $validated['email'])->first();

        if (!$user) {
            return response()->json(
            [
                'type' => AppErrorType::NOTFOUND->value,
                'error' => "Usuário não existe."
            ], 404);
        }

        if(!Hash::check($request->password, $user->password)){
            return response()->json(
            [
                'type' => AppErrorType::AUTH->value,
                'error' => "Senha Inválida"
            ], 422);
        }

        // Cria um token Sanctum para o usuário
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

}
