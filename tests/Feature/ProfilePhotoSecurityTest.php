<?php

namespace Tests\Feature;

use App\Models\Block;
use App\Models\User;
use App\Services\ProfilePhotoService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
