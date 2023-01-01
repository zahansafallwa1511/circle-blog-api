<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use App\Http\Requests\PageRequest;

class ApiController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    //method to allow pagination to Eloquent builder
    public function indexHelper($query, PageRequest $pageRequest){
        $perPage = (int) $pageRequest->get('per_page');
        if($query instanceof \Illuminate\Database\Eloquent\Builder){
            return $this->eloquentIndexHelper($query, $perPage);
        }
        else if(is_string($query)){
            return $this->modelIndexHelper($query, $perPage);
        }
    }
    //paginate eloquent builder
    public function eloquentIndexHelper(\Illuminate\Database\Eloquent\Builder $builder, $perPage = null){
        return $builder->paginate($perPage);
    }
    //paginate from model class name
    public function modelIndexHelper($model, $perPage = null){
        return $model::paginate($perPage);
    }
}