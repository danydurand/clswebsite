<?php

namespace App\Models\Traits;

trait HandleActive
{

    public function setActive(bool $value)
    {
        $this->is_active = $value;
        $this->save();
    }

    public function activate()
    {
        $this->is_active = true;
        $this->save();
    }

    public function deactivate()
    {
        $this->is_active = false;
        $this->save();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }



}
