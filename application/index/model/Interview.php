<?php

namespace app\index\model;

use think\Model;

class Interview extends Model
{
    public function author()
    {
        return $this->belongsTo('User', 'uid');
    }
}
