<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRedirectsTable extends Migration
{
    public function up()
    {
        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            // Defina as colunas necessárias para a tabela redirects
            $table->string('url');
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('redirects');
    }
}
