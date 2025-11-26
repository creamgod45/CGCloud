<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class SimpleTest extends TestCase
{
    /**
     * A simple test to demonstrate testing in this project.
     */
    public function test_string_manipulation(): void
    {
        // Simple string manipulation test
        $string = 'CGCloud';
        $this->assertEquals('CGCloud', $string);
        $this->assertStringContainsString('Cloud', $string);
        $this->assertNotEquals('Other', $string);

        // Array test
        $array = ['file', 'cloud', 'storage'];
        $this->assertCount(3, $array);
        $this->assertContains('cloud', $array);
        $this->assertNotContains('video', $array);
    }
}
