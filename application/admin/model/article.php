<?php

namespace app\admin\model;

use think\Model;

class article extends Model
{
    public function category()
    {
        return $this->belongsTo('article','id');

    }
}
