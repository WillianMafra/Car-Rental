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

    public function filter($filter, $column){
        $condition = explode(':', $filter);
        $this->model = $this->model->where($column, $condition[0], $condition[1]);
    }

    public function filterRelationatedColumns($relation, $filter, $column){
        $condition = explode(':', $filter);
        $this->model = $this->model->whereHas($relation, function ($query) use ($condition, $column) {
            $query->where($column, $condition[0], $condition[1]);
        });
    }
    
    public function getResults(){
        return $this->model->get();
    }

    public function getPaginatedResults($quantity){
        return $this->model->paginate($quantity);
    }

}

?>