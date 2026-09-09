<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;
use App\Services\PpidAdminService;

class UserPolicy
{
    public function __construct(private PpidAdminService $admins) {}

    public function viewAdminManagement(User $actor): bool
    {
        return $this->admins->canManagePpidAdmins($actor);
    }

    public function createAdmin(User $actor): bool
    {
        return $this->admins->canManagePpidAdmins($actor);
    }

    public function updateAdmin(User $actor, User $target): bool
    {
        if ($this->admins->isProtectedAdmin($target)) {
            return false;
        }

        if (! $this->admins->canManagePpidAdmins($actor)) {
            return false;
        }

        return $target->hasRole(UserRole::AdminPpid);
    }

    public function deleteAdmin(User $actor, User $target): bool
    {
        if ($this->admins->isProtectedAdmin($target)) {
            return false;
        }

        if ($actor->id === $target->id) {
            return false;
        }

        if (! $this->admins->canManagePpidAdmins($actor)) {
            return false;
        }

        return $target->hasRole(UserRole::AdminPpid);
    }

    public function disableAdmin(User $actor, User $target): bool
    {
        return $this->deleteAdmin($actor, $target);
    }

    public function removeRole(User $actor, User $target): bool
    {
        if ($this->admins->isProtectedAdmin($target)) {
            return false;
        }

        return $actor->canDo('manage-users') && $actor->id !== $target->id;
    }

    public function update(User $actor, User $target): bool
    {
        if ($actor->id === $target->id) {
            return true;
        }

        if ($this->admins->isProtectedAdmin($target)) {
            return false;
        }

        if ($this->updateAdmin($actor, $target)) {
            return true;
        }

        return $actor->canDo('manage-users');
    }

    public function delete(User $actor, User $target): bool
    {
        if ($this->admins->isProtectedAdmin($target)) {
            return false;
        }

        if ($actor->id === $target->id) {
            return false;
        }

        if ($this->deleteAdmin($actor, $target)) {
            return true;
        }

        return $actor->canDo('manage-users');
    }
}
