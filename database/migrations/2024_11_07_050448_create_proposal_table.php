<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('proposal', function (Blueprint $table) {
            $table->id();
            $table->string('proposal_name');
            $table->string('proposal_objective');
            $table->date('proposal_realization');
            $table->decimal('proposal_budget', 10, 2);
            $table->string('proposal_file');
            $table->enum('proposal_status', ['PENDING', 'APPROVED', 'REJECTED']);
            $table->unsignedBigInteger('proposal_approver_id')->nullable();
            $table->index(columns: 'proposal_approver_id');
            $table->foreign('proposal_approver_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('proposal_initiator_id')->nullable();
            $table->index(columns: 'proposal_initiator_id');
            $table->foreign('proposal_initiator_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposal');
    }
};
