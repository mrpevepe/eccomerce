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
        // Verifica se a tabela 'users' já existe antes de tentar modificá-la
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                // Adiciona ou modifica colunas conforme necessário
                if (!Schema::hasColumn('users', 'phone')) {
                    $table->string('phone', 20)->nullable()->after('name');
                }
                if (!Schema::hasColumn('users', 'address_id')) {
                    $table->unsignedBigInteger('address_id')->nullable()->after('password');
                }
                if (!Schema::hasColumn('users', 'role')) {
                    $table->enum('role', ['client', 'admin'])->default('client')->after('address_id');
                }
                // Garante que email_verified_at e remember_token estejam presentes
                if (!Schema::hasColumn('users', 'email_verified_at')) {
                    $table->timestamp('email_verified_at')->nullable()->after('email');
                }
                if (!Schema::hasColumn('users', 'remember_token')) {
                    $table->string('remember_token', 100)->nullable()->after('role');
                }
            });
        } else {
            // Caso a tabela não exista, cria com todos os campos
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name', 255);
                $table->string('phone', 20)->nullable();
                $table->string('email', 255)->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password', 255);
                $table->unsignedBigInteger('address_id')->nullable();
                $table->enum('role', ['client', 'admin'])->default('client');
                $table->string('remember_token', 100)->nullable();
                $table->timestamps();
            });
        }

        // Criação das tabelas password_reset_tokens e sessions (mantidas do default)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverte as alterações na tabela users
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('users', 'address_id')) {
                $table->dropColumn('address_id');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            // Não remove email_verified_at e remember_token para manter compatibilidade com o default
        });

        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};