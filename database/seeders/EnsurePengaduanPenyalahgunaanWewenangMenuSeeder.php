<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\ServiceLink;
use App\Support\PublicNavigation;
use Illuminate\Database\Seeder;

class EnsurePengaduanPenyalahgunaanWewenangMenuSeeder extends Seeder
{
    public function run(): void
    {
        $label = 'Pengaduan Penyalahgunaan Wewenang';
        $canonical = 'alur-pengajuan-sengketa';

        $candidates = MenuItem::query()
            ->where('route_name', 'services.show')
            ->where(function ($query) use ($label) {
                $query->where('label', $label)
                    ->orWhere('route_params->slug', 'pengaduan-penyalahgunaan-wewenang')
                    ->orWhere('route_params->slug', 'alur-pengajuan-sengketa')
                    ->orWhere('label', 'Tata Cara Pengajuan Permohonan Penyelesaian Sengketa Ke Komisi Informasi');
            })
            ->orderBy('id')
            ->get();

        $keeper = $candidates->first(fn (MenuItem $item) => $item->label === $label) ?? $candidates->first();

        if ($keeper) {
            $keeper->update([
                'label' => $label,
                'route_name' => 'services.show',
                'route_params' => ['slug' => $canonical],
                'is_active' => true,
            ]);

            MenuItem::query()
                ->whereIn('id', $candidates->pluck('id')->reject(fn ($id) => $id === $keeper->id))
                ->delete();
        }

        ServiceLink::query()
            ->where('slug', $canonical)
            ->where('title', 'Tata Cara Pengajuan Permohonan Penyelesaian Sengketa Ke Komisi Informasi')
            ->update([
                'title' => 'Tata Cara Pengaduan Penyalahgunaan Wewenang dan Pelanggaran',
                'excerpt' => 'Informasi tata cara pengaduan dugaan penyalahgunaan wewenang, pelanggaran, pungutan liar, konflik kepentingan, gratifikasi, dan pelanggaran pelayanan publik di lingkungan Poltekkes Kemenkes Banten.',
            ]);

        PublicNavigation::forget();
    }
}
