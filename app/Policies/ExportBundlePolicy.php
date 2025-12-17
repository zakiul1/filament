<?php

namespace App\Policies;

use App\Models\ExportBundle;
use App\Models\User;

class ExportBundlePolicy
{
    private function isAdmin(User $user): bool
    {
        return $user->hasRole('SUPER_ADMIN') || $user->hasRole('ADMIN');
    }

    public function generate(User $user, ExportBundle $bundle): bool
    {
        return $this->isAdmin($user) && !$bundle->isClosed() && !$bundle->isLocked() && !$bundle->isSubmitted();
    }

    public function lock(User $user, ExportBundle $bundle): bool
    {
        return $this->isAdmin($user) && !$bundle->isClosed() && !$bundle->isLocked() && !$bundle->isSubmitted();
    }

    public function unlock(User $user, ExportBundle $bundle): bool
    {
        return $user->hasRole('SUPER_ADMIN') && !$bundle->isClosed() && !$bundle->isSubmitted();
    }

    public function submitToBank(User $user, ExportBundle $bundle): bool
    {
        return $this->isAdmin($user) && !$bundle->isClosed() && $bundle->isLocked() && !$bundle->isSubmitted();
    }

    public function unsubmitFromBank(User $user, ExportBundle $bundle): bool
    {
        return $user->hasRole('SUPER_ADMIN') && !$bundle->isClosed() && $bundle->isSubmitted();
    }

    public function downloadBankAck(User $user, ExportBundle $bundle): bool
    {
        return $this->isAdmin($user);
    }

    public function print(User $user, ExportBundle $bundle): bool
    {
        return $this->isAdmin($user); // print allowed even when closed
    }

    // ✅ Step 8
    public function close(User $user, ExportBundle $bundle): bool
    {
        return $this->isAdmin($user) && !$bundle->isClosed() && $bundle->isSubmitted();
    }

    public function reopen(User $user, ExportBundle $bundle): bool
    {
        return $user->hasRole('SUPER_ADMIN') && $bundle->isClosed();
    }
}