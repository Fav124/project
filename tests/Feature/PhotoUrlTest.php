<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhotoUrlTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that Santri model has photo_url attribute
     */
    public function test_santri_has_photo_url_attribute()
    {
        $santri = \App\Models\Santri::factory()->create([
            'photo_path' => 'santri-photos/test.jpg'
        ]);

        $this->assertNotNull($santri->photo_url);
        $this->assertStringContainsString('storage/santri-photos/test.jpg', $santri->photo_url);
    }

    /**
     * Test that Medicine model has photo_url attribute
     */
    public function test_medicine_has_photo_url_attribute()
    {
        $medicine = \App\Models\Medicine::factory()->create([
            'photo' => 'medicines/test.jpg'
        ]);

        $this->assertNotNull($medicine->photo_url);
        $this->assertStringContainsString('storage/medicines/test.jpg', $medicine->photo_url);
    }

    /**
     * Test that Santri model returns null for photo_url when photo_path is null
     */
    public function test_santri_photo_url_is_null_when_no_photo()
    {
        $santri = \App\Models\Santri::factory()->create([
            'photo_path' => null
        ]);

        $this->assertNull($santri->photo_url);
    }

    /**
     * Test that Medicine model returns null for photo_url when photo is null
     */
    public function test_medicine_photo_url_is_null_when_no_photo()
    {
        $medicine = \App\Models\Medicine::factory()->create([
            'photo' => null
        ]);

        $this->assertNull($medicine->photo_url);
    }
}