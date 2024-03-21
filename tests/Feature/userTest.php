<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Costumer;

class CreateUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se ao criar um usuário, também é criado um cliente (Costumer).
     *
     * @return void
     */
    public function test_create_user_creates_costumer()
    {
        // Definir dados de usuário
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        // Chamar a função de criação de usuário do controlador
        $response = $this->post('/register', $userData);

        // Verificar se o usuário foi criado com sucesso
        $this->assertDatabaseHas('users', ['email' => $userData['email']]); // Verifica se o usuário foi criado no banco de dados

        // Obter o usuário recém-criado do banco de dados
        $user = User::where('email', $userData['email'])->first();

        // Verificar se um cliente (Costumer) correspondente foi criado
        $this->assertNotNull($user->costumer); // Verifica se o usuário possui um cliente associado
        $this->assertEquals($userData['name'], $user->costumer->name); // Verifica se o nome do cliente está correto

    }
}
