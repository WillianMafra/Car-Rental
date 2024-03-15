<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;


abstract class abstractRepository {

    public $model;
    
    public function __construct(Model $model){
        $this->model = $model;
    }

    public function selectRelationatedColumns($columns){
        $this->model = $this->model->with($columns);
    }
    
    public function selectColumns($columns){
        $this->model = $this->model->selectRaw($columns);
    }

    public function filter($filter){
        $filters = explode(';',$filter);
        foreach($filters as $key => $value){
            $condition = explode(':', $value);
            $this->model = $this->model->where($condition[0], $condition[1], $condition[2]);
        }
    }
    
    public function getResults(){
        return $this->model->get();
    }

}

?>