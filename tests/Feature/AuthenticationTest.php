<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    public function testRequiredFieldsForRegistration()
    {
        $this->json('POST', 'api/register_employee', ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJson([
             
                "message" => "The given data was invalid.",
                "errors" => [
                    "name" => ["The name field is required."],
                    "employee_no" => ["The employee_no field is required."],
                    "password" => ["The password field is required."],
                ]
            ]);
    }

    public function testRequiredFieldsForLogin()
    {
        $this->json('POST', 'api/login', ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJson([
             
                "message" => "The given data was invalid.",
                "errors" => [
                    "employee_no" => ["The employee_no field is required."],
                    "password" => ["The password field is required."],
                ]
            ]);
    }

 

    public function testSuccessfulRegistration()
    {
        $userData = [
            "name" => "John Doe",
            "employee_no" => "6669",
            "password" => "demo12345",

        ];

        $this->json('POST', 'api/register_employee', $userData, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure([
                "user" => [
                    'id',
                    'name',
                    'employee_no',
                    'created_at',
                    'updated_at',
                ],
                "message"
            ]);
    }
}