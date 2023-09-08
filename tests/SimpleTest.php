<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

class SimpleTest extends TestCase
{
    public function testSomething(): void
    {
        $this->assertTrue(true);
    }

    // Une méthode utilitaire, son nom ne commence pas par `test`
    private function returnTrue(): bool
    {
        return true;
    }
}
