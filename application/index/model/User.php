<?php

namespace app\index\model;

use think\Model;

class User extends Model
{
    public function inter()
    {
        return $this->hasMany('Interview', 'uid');
    }
}
