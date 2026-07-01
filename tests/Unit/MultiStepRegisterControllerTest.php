<?php

namespace Tests\Unit;

use App\Http\Controllers\Auth\MultiStepRegisterController;
use PHPUnit\Framework\TestCase;

class MultiStepRegisterControllerTest extends TestCase
{
    public function test_generates_password_from_last_name_and_random_numbers(): void
    {
        $password = MultiStepRegisterController::generateSystemPassword('Maria Dela Cruz');

        $this->assertMatchesRegularExpression('/^[a-z]{4}\d{4}$/', strtolower($password));
        $this->assertSame(8, strlen($password));
    }

    public function test_generates_password_with_fallback_for_short_last_name(): void
    {
        $password = MultiStepRegisterController::generateSystemPassword('Juan');

        $this->assertMatchesRegularExpression('/^[a-z]{4}\d{4}$/', strtolower($password));
        $this->assertSame(8, strlen($password));
    }
}
