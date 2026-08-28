<?php

namespace Tests\Feature;

use App\Models\Block;
use App\Models\User;
use App\Services\ProfilePhotoService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ProfilePhotoSecurityTest extends TestCase
{
    use DatabaseTransactions;

    private const METADATA_MARKER =
        'PROFILE-PHOTO-METADATA-SECRET';

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('public');
    }

    public function test_service_rejects_photo_over_total_pixel_budget_before_decode(): void
    {
        $this->requireGd();

        $photo = $this->syntheticPngUpload(
            3000,
            2500
        );

        try {
            app(ProfilePhotoService::class)->store($photo);
            $this->fail(
                'The oversized image should have been rejected.'
            );
        } catch (ValidationException $exception) {
            $this->assertSame(
                'مجموع ابعاد تصویر نباید بیشتر از ۶ مگاپیکسل باشد.',
                $exception->errors()['profile_picture'][0] ?? null
            );
        }

        $this->assertSame(
            [],
            Storage::disk('local')->allFiles()
        );
    }

    public function test_profile_update_rejects_photo_over_total_pixel_budget(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('profile.edit'))
            ->post(
                route('profile.update'),
                $this->profilePayload($user, [
                    'profile_picture' =>
                        $this->syntheticPngUpload(
                            3000,
                            2500
                        ),
                ])
            )
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHasErrors('profile_picture');

        $this->assertNull(
            $user->fresh()->profile_picture
        );

        $this->assertSame(
            [],
            Storage::disk('local')->allFiles()
        );
    }

    public function test_profile_photo_upload_is_limited_to_two_per_minute(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 2) as $attempt) {
            $this->actingAs($user)
                ->post(
                    route('profile.update'),
                    $this->profilePayload($user, [
                        'profile_picture' =>
                            UploadedFile::fake()->create(
                                'invalid-'.$attempt.'.jpg',
                                1,
                                'image/jpeg'
                            ),
                    ])
                )
                ->assertSessionHasErrors(
                    'profile_picture'
                );
        }

        $this->actingAs($user)
            ->post(
                route('profile.update'),
                $this->profilePayload($user, [
                    'profile_picture' =>
                        UploadedFile::fake()->create(
                            'blocked.jpg',
                            1,
                            'image/jpeg'
                        ),
                ])
            )
            ->assertStatus(429);
    }

    public function test_profile_edits_without_photo_do_not_consume_photo_quota(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 3) as $attempt) {
            $this->actingAs($user)
                ->post(
                    route('profile.update'),
                    $this->profilePayload($user, [
                        'name' => 'Updated user '.$attempt,
                    ])
                )
                ->assertRedirect(route('dashboard'));
        }

        $this->assertSame(
            'Updated user 3',
            $user->fresh()->name
        );
    }

    public function test_registration_photo_upload_is_limited_by_ip(): void
    {
        foreach (range(1, 2) as $attempt) {
            $this->post(route('register'), [
                'profile_picture' =>
                    UploadedFile::fake()->create(
                        'invalid-registration-'.$attempt.'.jpg',
                        1,
                        'image/jpeg'
                    ),
            ])->assertSessionHasErrors('profile_picture');
        }

        $this->post(route('register'), [
            'profile_picture' =>
                UploadedFile::fake()->create(
                    'blocked-registration.jpg',
                    1,
                    'image/jpeg'
                ),
        ])->assertStatus(429);
    }

    public function test_photo_is_reencoded_without_uploaded_metadata(): void
    {
        $this->requireGd();

        $taggedJpeg =
            $this->jpegWithMetadataMarker();

        $temporaryPath = tempnam(
            sys_get_temp_dir(),
            'profile-photo-'
        );

        $this->assertIsString($temporaryPath);
        file_put_contents($temporaryPath, $taggedJpeg);

        try {
            $uploaded = new UploadedFile(
                $temporaryPath,
                'profile.jpg',
                'image/jpeg',
                null,
                true
            );

            $path = app(
                ProfilePhotoService::class
            )->store($uploaded);
        } finally {
            @unlink($temporaryPath);
        }

        Storage::disk('local')->assertExists($path);
        Storage::disk('public')->assertMissing($path);

        $stored = Storage::disk('local')->get($path);

        $this->assertStringNotContainsString(
            self::METADATA_MARKER,
            $stored
        );

        $this->assertSame(
            "\xFF\xD8",
            substr($stored, 0, 2)
        );
    }

    public function test_authenticated_user_can_view_an_unblocked_photo(): void
    {
        $owner = $this->userWithPrivatePhoto();
        $viewer = User::factory()->create();

        $response = $this->actingAs($viewer)
            ->get(route('profile.photo', $owner));

        $response
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff');

        $cacheControl = (string) $response
            ->headers
            ->get('Cache-Control');

        foreach (
            ['private', 'no-store', 'max-age=0']
            as $directive
        ) {
            $this->assertStringContainsString(
                $directive,
                $cacheControl
            );
        }
    }

    public function test_legacy_public_photo_migration_moves_file_and_updates_user(): void
    {
        $this->requireGd();

        $user = User::factory()->create([
            'profile_picture' =>
                'profile_pictures/legacy-photo.jpg',
        ]);

        Storage::disk('public')->put(
            $user->profile_picture,
            $this->jpegWithMetadataMarker()
        );

        $this->artisan(
            'profile-photos:migrate-private'
        )->assertSuccessful();

        $user->refresh();

        $this->assertNotSame(
            'profile_pictures/legacy-photo.jpg',
            $user->profile_picture
        );

        Storage::disk('local')->assertExists(
            $user->profile_picture
        );

        Storage::disk('public')->assertMissing(
            'profile_pictures/legacy-photo.jpg'
        );

        $stored = Storage::disk('local')->get(
            $user->profile_picture
        );

        $this->assertStringNotContainsString(
            self::METADATA_MARKER,
            $stored
        );
    }

    public function test_photo_is_hidden_when_viewer_blocked_owner(): void
    {
        $owner = $this->userWithPrivatePhoto();
        $viewer = User::factory()->create();

        Block::create([
            'blocker_id' => $viewer->id,
            'blocked_id' => $owner->id,
        ]);

        $this->actingAs($viewer)
            ->get(route('profile.photo', $owner))
            ->assertNotFound();
    }

    public function test_photo_is_hidden_when_owner_blocked_viewer(): void
    {
        $owner = $this->userWithPrivatePhoto();
        $viewer = User::factory()->create();

        Block::create([
            'blocker_id' => $owner->id,
            'blocked_id' => $viewer->id,
        ]);

        $this->actingAs($viewer)
            ->get(route('profile.photo', $owner))
            ->assertNotFound();
    }

    public function test_guest_cannot_download_a_profile_photo(): void
    {
        $owner = $this->userWithPrivatePhoto();

        $this->get(route('profile.photo', $owner))
            ->assertRedirect(route('login'));
    }

    public function test_non_admin_cannot_view_non_public_profile_photos(): void
    {
        $viewer = User::factory()->create();

        foreach (
            [
                ['banned' => true],
                ['email_verified_at' => null],
                ['role' => 'admin'],
            ]
            as $attributes
        ) {
            $owner = $this->userWithPrivatePhoto(
                $attributes
            );

            $this->actingAs($viewer)
                ->get(route('profile.photo', $owner))
                ->assertNotFound();
        }
    }

    public function test_admin_can_review_a_photo_despite_a_block(): void
    {
        $owner = $this->userWithPrivatePhoto();

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        Block::create([
            'blocker_id' => $owner->id,
            'blocked_id' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->get(route('profile.photo', $owner))
            ->assertOk();
    }

    /**
     * @param array<string, mixed> $attributes
     */
    private function userWithPrivatePhoto(
        array $attributes = []
    ): User
    {
        $user = User::factory()->create(
            $attributes
        );
        $path = 'profile_pictures/'.$user->id.'.jpg';

        Storage::disk('local')->put(
            $path,
            'private-profile-photo'
        );

        $user->forceFill([
            'profile_picture' => $path,
        ])->save();

        return $user;
    }

    private function requireGd(): void
    {
        $this->assertTrue(
            extension_loaded('gd'),
            'PHP GD extension must be enabled.'
        );
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function profilePayload(
        User $user,
        array $overrides = []
    ): array {
        return array_merge([
            'name' => $user->name,
            'email' => $user->email,
            'city' => $user->city,
            'bio' => $user->bio,
        ], $overrides);
    }

    private function syntheticPngUpload(
        int $width,
        int $height
    ): UploadedFile {
        $header = pack(
            'NNCCCCC',
            $width,
            $height,
            8,
            2,
            0,
            0,
            0
        );

        $png = "\x89PNG\r\n\x1a\n"
            .$this->pngChunk('IHDR', $header)
            .$this->pngChunk('IEND', '');

        return UploadedFile::fake()->createWithContent(
            'oversized.png',
            $png
        );
    }

    private function pngChunk(
        string $type,
        string $data
    ): string {
        return pack('N', strlen($data))
            .$type
            .$data
            .pack('N', crc32($type.$data));
    }

    private function jpegWithMetadataMarker(): string
    {
        $image = imagecreatetruecolor(8, 8);

        if (! $image instanceof \GdImage) {
            $this->fail(
                'Could not create the test JPEG.'
            );
        }

        $color = imagecolorallocate(
            $image,
            236,
            72,
            153
        );

        imagefill($image, 0, 0, $color);

        ob_start();

        try {
            $encoded = imagejpeg(
                $image,
                null,
                90
            );

            $jpeg = ob_get_contents();
        } finally {
            ob_end_clean();
            imagedestroy($image);
        }

        $this->assertTrue($encoded);
        $this->assertIsString($jpeg);

        /*
         * JPEG COM is a valid metadata segment. The security property
         * under test is that re-encoding removes the original segment.
         */
        $comment = self::METADATA_MARKER;
        $segment = "\xFF\xFE"
            .pack('n', strlen($comment) + 2)
            .$comment;

        return substr($jpeg, 0, 2)
            .$segment
            .substr($jpeg, 2);
    }
}
