<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AttachUserToRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            DB::beginTransaction();

            $this->attachRoleToUser('art.zhinkin@gmail.com', Role::MANAGER);
            $this->attachRoleToUser('korap702@gmail.com', Role::NINJA);
            $this->attachRoleToUser('skreko21@gmail.com', Role::MANAGER);
            $this->attachRoleToUser('tatyanakurochka8@gmail.com', Role::NINJA);
            $this->attachRoleToUser('urijd8253@gmail.com', Role::NINJA);
            $this->attachRoleToUser('karaban77780@gmail.com', Role::CUTTER);
            $this->attachRoleToUser('sasha.vl.2207@gmail.com', Role::NINJA);
            $this->attachRoleToUser('dflbvdbr@gmail.com', Role::NINJA);
            $this->attachRoleToUser('kolyochek78@gmail.com', Role::CUTTER);
            $this->attachRoleToUser('lera.konko1992@gmail.com', Role::NINJA);
            $this->attachRoleToUser('viktoriyalivial1805@gmail.com', Role::NINJA);
            $this->attachRoleToUser('nastya.plhn96@gmail.com', Role::NINJA);

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        try {
            DB::beginTransaction();

            $this->detachRoleToUser('art.zhinkin@gmail.com', Role::MANAGER);
            $this->detachRoleToUser('korap702@gmail.com', Role::NINJA);
            $this->detachRoleToUser('skreko21@gmail.com', Role::MANAGER);
            $this->detachRoleToUser('tatyanakurochka8@gmail.com', Role::NINJA);
            $this->detachRoleToUser('urijd8253@gmail.com', Role::NINJA);
            $this->detachRoleToUser('karaban77780@gmail.com', Role::CUTTER);
            $this->detachRoleToUser('sasha.vl.2207@gmail.com', Role::NINJA);
            $this->detachRoleToUser('dflbvdbr@gmail.com', Role::NINJA);
            $this->detachRoleToUser('kolyochek78@gmail.com', Role::CUTTER);
            $this->detachRoleToUser('lera.konko1992@gmail.com', Role::NINJA);
            $this->detachRoleToUser('viktoriyalivial1805@gmail.com', Role::NINJA);
            $this->detachRoleToUser('nastya.plhn96@gmail.com', Role::NINJA);

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    public function attachRoleToUser(string $email, int $role)
    {
        $userQuery = User::whereEmail($email);
        if ($userQuery->exists()) {
            $user = $userQuery->first();
            $user->roles()->attach($role);
        }
    }

    public function detachRoleToUser(string $email, int $role)
    {
        $userQuery = User::whereEmail($email);
        if ($userQuery->exists()) {
            $user = $userQuery->first();
            $user->roles()->detach($role);
        }
    }
}
