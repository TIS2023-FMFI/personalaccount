<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financial_operations', function (Blueprint $table) {
            $table->id();
            $table->integer('account_id')->unsigned();
            $table->foreign('account_id')->references("id")->on("accounts");
            $table->string('title');
            $table->date('date');
            $table->integer('operation_type_id')->unsigned();
            $table->foreign('operation_type_id')->references("id")->on("operation_types");
            $table->string('subject');
            $table->float('sum')->unsigned();
            $table->string('attachment');
            $table->boolean('checked');
            $table->integer('sap_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('financial_operations');
    }
};
