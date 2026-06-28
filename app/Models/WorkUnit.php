<?php

namespace App\Models;

use Juniyasyos\IamClient\Models\UnitKerja;

class WorkUnit extends UnitKerja
{
    protected $table = 'work_units';

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}