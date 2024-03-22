<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $imagePath = '/var/www/html/brandImages/ford.png';
        $storagePath = Storage::disk('public')->putFile('brand_images', $imagePath);

        $newBrand = new Brand();
        $newBrand->name = 'Ford';
        $newBrand->image = $storagePath; // Define o caminho completo da imagem
        $newBrand->save();
        
        $imagePath = '/var/www/html/brandImages/hyundai.png';
        $storagePath = Storage::disk('public')->putFile('brand_images', $imagePath);

        $newBrand = new Brand();
        $newBrand->name = 'Hyundai';
        $newBrand->image = $storagePath; // Define o caminho completo da imagem
        $newBrand->save();

        $imagePath = '/var/www/html/brandImages/volkswagen.png';
        $storagePath = Storage::disk('public')->putFile('brand_images', $imagePath);

        $newBrand = new Brand();
        $newBrand->name = 'Volkwsagen';
        $newBrand->image = $storagePath; // Define o caminho completo da imagem
        $newBrand->save();

    }
}
