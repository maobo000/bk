<?php

namespace app\admin\model;

use think\Model;

class article extends Model
{

    protected $autoWriteTimestamp = true;
    public function category()
    {

        return $this->belongsTo('article','id');

    }
}
