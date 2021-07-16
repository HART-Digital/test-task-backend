<?php

use App\Enums\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteAdminColumnInUsersTable extends Migration
{
    private const ADMIN_ROLE_ID = Role::ADMIN;
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $adminIds = DB::table('users')->where('admin', true)->pluck('id')->toArray();

        $userRoleInsert = array_map(function ($id) {
            return [
                'user_id' => $id,
                'role_id' => self::ADMIN_ROLE_ID,
            ];
        }, $adminIds);

        DB::table('user_role')->insert($userRoleInsert);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('admin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('admin')->default(false);
        });
    }
}
