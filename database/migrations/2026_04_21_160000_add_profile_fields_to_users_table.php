<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->default('')->after('name');
            $table->string('last_name')->default('')->after('first_name');
            $table->string('nickname')->nullable()->after('last_name');
        });

        DB::table('users')
            ->select(['id', 'name', 'email'])
            ->orderBy('id')
            ->get()
            ->each(function (object $user): void {
                [$firstName, $lastName] = $this->splitName($user->name);

                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'nickname' => $this->uniqueNicknameFor($user),
                    ]);
            });

        Schema::table('users', function (Blueprint $table) {
            $table->unique('nickname');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['nickname']);
            $table->dropColumn(['first_name', 'last_name', 'nickname']);
        });
    }

    /**
     * Split the legacy full name into first and last name.
     *
     * @return array{0: string, 1: string}
     */
    private function splitName(string $name): array
    {
        $parts = preg_split('/\s+/', trim($name), 2, PREG_SPLIT_NO_EMPTY) ?: [];

        $firstName = $parts[0] ?? 'User';
        $lastName = $parts[1] ?? '';

        return [$firstName, $lastName];
    }

    /**
     * Generate a unique nickname for legacy users.
     */
    private function uniqueNicknameFor(object $user): string
    {
        $base = Str::of($user->email)
            ->before('@')
            ->replaceMatches('/[^A-Za-z0-9._-]+/', '')
            ->trim()
            ->value();

        $base = $base !== '' ? Str::limit($base, 42, '') : 'user';
        $nickname = $base;
        $suffix = 1;

        while (DB::table('users')
            ->where('nickname', $nickname)
            ->where('id', '!=', $user->id)
            ->exists()) {
            $nickname = Str::limit($base, 42, '').$suffix;
            $suffix++;
        }

        return $nickname;
    }
};
