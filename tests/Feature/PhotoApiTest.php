<?php


namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Photo\Models\Photo;
use Tests\TestCase;

class PhotoApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function a_user_can_see_list_of_photos(): void
    {

        $user = factory(User::class)->create();
        $photos = factory(Photo::class, 5)->create(['user_id' => $user->id]);
        $response = $this->actingAs($user)->getJson(route('photo::api.photos.index'));

        $response->assertOk();
    }

    /**
     * @test
     */
    public function a_user_can_upload_a_photo(): void
    {
        $storage = Storage::fake('public');
        $file = UploadedFile::fake()->image('default.jpg');
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->postJson(route('photo::api.photos.store'), [
            'file' => $file,
            'caption' => 'This is a testing photo',
        ]);
        $response->assertCreated();

        $this->assertDatabaseHas((new Photo())->getTable(), [
            'src' => 'images/this-is-a-testing-photo.jpeg',
        ]);
        $storage->assertExists('images/this-is-a-testing-photo.jpeg');
    }


}
