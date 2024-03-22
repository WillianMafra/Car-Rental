<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\carModel;

class CarModelSeeder extends Seeder
{
    public function run()
    {
        $imagePath = '/var/www/html/CarImages/ford_ka_1_0.png';
        $storagePath = Storage::disk('public')->putFile('car_images', $imagePath);

        $carModel = new carModel();
        $carModel->brand_id = 1;
        $carModel->name = 'Ford Ka 1.0';
        $carModel->image = $storagePath; // Define o caminho completo da imagem
        $carModel->doors = 4;
        $carModel->seats = 5;
        $carModel->air_bag = fake()->boolean();
        $carModel->abs = fake()->boolean();
        $carModel->save();


        $imagePath = '/var/www/html/CarImages/ford_ka_sedan_1_0.png';
        $storagePath = Storage::disk('public')->putFile('car_images', $imagePath);

        $carModel = new carModel();
        $carModel->brand_id = 1;
        $carModel->name = 'Ford Ka Sedan 1.0';
        $carModel->image = $storagePath; // Define o caminho completo da imagem
        $carModel->doors = 4;
        $carModel->seats = 5;
        $carModel->air_bag = fake()->boolean();
        $carModel->abs = fake()->boolean();
        $carModel->save();


        $imagePath = '/var/www/html/CarImages/hyundai_hb20_1_0.png';
        $storagePath = Storage::disk('public')->putFile('car_images', $imagePath);

        $carModel = new carModel();
        $carModel->brand_id = 2;
        $carModel->name = 'Hyundai HB20 1.0';
        $carModel->image = $storagePath; // Define o caminho completo da imagem
        $carModel->doors = 4;
        $carModel->seats = 5;
        $carModel->air_bag = fake()->boolean();
        $carModel->abs = fake()->boolean();
        $carModel->save();
        
        $imagePath = '/var/www/html/CarImages/hyundai_hb20s_1_0.png';
        $storagePath = Storage::disk('public')->putFile('car_images', $imagePath);

        $carModel = new carModel();
        $carModel->brand_id = 2;
        $carModel->name = 'Hyundai HB20S 1.0';
        $carModel->image = $storagePath; // Define o caminho completo da imagem
        $carModel->doors = 4;
        $carModel->seats = 5;
        $carModel->air_bag = fake()->boolean();
        $carModel->abs = fake()->boolean();
        $carModel->save();

        $imagePath = '/var/www/html/CarImages/volkswagen_gol_1_0.png';
        $storagePath = Storage::disk('public')->putFile('car_images', $imagePath);

        $carModel = new carModel();
        $carModel->brand_id = 3;
        $carModel->name = 'Volkswagen Gol 1.0';
        $carModel->image = $storagePath; // Define o caminho completo da imagem
        $carModel->doors = 4;
        $carModel->seats = 5;
        $carModel->air_bag = fake()->boolean();
        $carModel->abs = fake()->boolean();
        $carModel->save();

        $imagePath = '/var/www/html/CarImages/volkswagen_gol_1_6.png';
        $storagePath = Storage::disk('public')->putFile('car_images', $imagePath);

        $carModel = new carModel();
        $carModel->brand_id = 3;
        $carModel->name = 'Volkswagen Gol 1.6';
        $carModel->image = $storagePath; // Define o caminho completo da imagem
        $carModel->doors = 4;
        $carModel->seats = 5;
        $carModel->air_bag = fake()->boolean();
        $carModel->abs = fake()->boolean();
        $carModel->save();

        $imagePath = '/var/www/html/CarImages/volkswagen_polo_1_0.png';
        $storagePath = Storage::disk('public')->putFile('car_images', $imagePath);

        $carModel = new carModel();
        $carModel->brand_id = 3;
        $carModel->name = 'Volkswagen Polo 1.0';
        $carModel->image = $storagePath; // Define o caminho completo da imagem
        $carModel->doors = 4;
        $carModel->seats = 5;
        $carModel->air_bag = fake()->boolean();
        $carModel->abs = fake()->boolean();
        $carModel->save();

    }

    
}