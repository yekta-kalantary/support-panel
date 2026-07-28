<?php

use App\Enums\TicketStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_sequences', function (Blueprint $table): void {
            $table->unsignedSmallInteger('year')->primary();
            $table->unsignedBigInteger('last_number')->default(0);
            $table->timestamps();
        });

        Schema::create('tickets', function (Blueprint $table): void {
            $table->id();
            $table->string('ticket_number', 32)->unique();
            $table->foreignId('customer_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('project_id')->constrained()->restrictOnDelete();
            $table->string('subject', 200);
            $table->string('status', 30)->default(TicketStatus::OPEN->value)->index();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'updated_at']);
            $table->index(['project_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('ticket_sequences');
    }
};
