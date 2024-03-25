<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

  /**
     * @OA\Info(
     *      version="1.0.0",
     *      title="Car Rental Api",
     *      description="Descrição da Sua API",
     *      @OA\Contact(
     *          email="willnmafra@gmail.com"
     *      ),
     *      @OA\License(
     *          name="MIT License",
     *      )
     * )
     */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
