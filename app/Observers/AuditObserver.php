<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AuditObserver
{
    public function created($model)
    {
        $this->log('create', $model);
    }

    public function updated($model)
    {
        $this->log('update', $model);
    }

    public function deleted($model)
    {
        $this->log('delete', $model);
    }

    private function log(string $action, $model)
    {
        AuditLog::create([
            'user_id'     => Auth::id(),
            'table_name'  => $model->getTable(),
            'action'      => $action,
            'entity_id'   => $model->id,
            'logged_at'   => now(),
            'changes_json'=> $action === 'update'
                                ? $model->getChanges()
                                : $model->getAttributes(),
        ]);
    }
}
