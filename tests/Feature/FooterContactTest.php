<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FooterContactTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    #[Test]
    public function public_footer_shows_official_contact_and_campus_addresses(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('PPID-FOOTER-CONTACT-DEPLOY-20260907-A', false);
        $response->assertSee('https://poltekkesbanten.ac.id/', false);
        $response->assertSee('poltekkesbanten.ac.id', false);
        $response->assertSee('humas@poltekkesbanten.ac.id', false);
        $response->assertSee('mailto:humas@poltekkesbanten.ac.id', false);
        $response->assertSee('+62 895-0839-4483', false);
        $response->assertSee('Dias Arliadini', false);
        $response->assertSee('https://wa.me/6289508394483', false);
        $response->assertSee('Senin–Jumat, 08.00–15.30 WIB', false);
        $response->assertSee('Jl. Syekh Moh. Nawawi Albantani No.12', false);
        $response->assertSee('Jl. Dr. Sitanala (Komplek SPK), RT.002/RW.003, Karang Sari, Kec. Neglasari, Kota Tangerang, Banten 15121', false);
        $response->assertSee('Jl. Raya Rangkasbitung Pandeglang No.421', false);
        $response->assertDontSee('RJRP+697', false);
        $response->assertDontSee('persis samping RSUP Dr. Sitanala', false);
        $response->assertSee('target="_blank"', false);
        $response->assertSee('rel="noopener noreferrer"', false);

        $response->assertDontSee('[TODO:', false);
        $response->assertDontSee('email-ppid@poltekkesbanten.ac.id', false);
        $response->assertDontSee('[TODO: nomor telepon]', false);
        $response->assertDontSee('[TODO: nomor WhatsApp]', false);
        $response->assertDontSee('alamat Kampus Serang', false);
        $response->assertDontSee('alamat Kampus Tangerang', false);
        $response->assertDontSee('alamat Kampus Rangkasbitung', false);
        $response->assertDontSee('08.00–15.30 WIB (CONTOH)', false);
        $response->assertDontSee('ppid.yankes@gmail.com', false);

        $contact = $this->get('/kontak');
        $contact->assertOk();
        $contact->assertSee('Jl. Dr. Sitanala (Komplek SPK), RT.002/RW.003, Karang Sari, Kec. Neglasari, Kota Tangerang, Banten 15121', false);
        $contact->assertSee('Jl. Syekh Moh. Nawawi Albantani No.12', false);
        $contact->assertSee('Jl. Raya Rangkasbitung Pandeglang No.421', false);
        $contact->assertDontSee('RJRP+697', false);
        $contact->assertDontSee('persis samping RSUP Dr. Sitanala', false);
    }
}
