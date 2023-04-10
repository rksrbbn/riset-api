<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Permission;
use App\Models\Role;

class CreatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description');
            $table->timestamps();
        });

        $createPost = new Permission();
        $createPost->name = 'create-post';
        $createPost->description = 'Create Post';
        $createPost->save();

        $editPost = new Permission();
        $editPost->name = 'edit-post';
        $editPost->description = 'Edit Post';
        $editPost->save();

        $deletePost = new Permission();
        $deletePost->name = 'delete-post';
        $deletePost->description = 'Delete Post';
        $deletePost->save();

        // $adminRole = Role::where('name', 'admin')->first();
        // $adminRole->permissions()->attach([
        //     Permission::where('name', 'create-post')->first()->id,
        //     Permission::where('name', 'edit-post')->first()->id,
        //     Permission::where('name', 'delete-post')->first()->id,
        // ]);

        // $userRole = Role::where('name', 'user')->first();
        // $userRole->permissions()->attach([
        //     Permission::where('name', 'create-post')->first()->id,
        // ]);

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissions');
    }
}
