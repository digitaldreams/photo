<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Photo\Models\Photo;
use Tests\TestCase;

class PhotoTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function a_user_can_upload_photo(): void
    {
        $storage = Storage::fake('public');
        $file = UploadedFile::fake()->image('default.jpg');
        $user = factory(User::class)->create();

        $response = $this->followingRedirects()
            ->actingAs($user)
            ->from(route('photo::photos.create'))->post(route('photo::photos.store'), [
                'file'    => $file,
                'caption' => 'This is a testing photo',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas((new Photo())->getTable(), [
            'src' => 'images/this-is-a-testing-photo.jpeg',
        ]);
        $storage->assertExists('images/this-is-a-testing-photo.jpeg');
    }

    /**
     * @test
     */
    public function a_user_can_see_list_of_photos(): void
    {
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)->get(route('photo::photos.index'));

        $response->assertStatus(200)->assertSee('Photos');
    }

    /**
     * @test
     */
    public function a_user_can_see_upload_form(): void
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get(route('photo::photos.create'));
        $response->assertOk()->assertSee('New Photo');
    }

    /**
     * @test
     */
    public function a_user_can_see_details_page(): void
    {
        $user = factory(User::class)->create();
        $photo = factory(Photo::class)->create([
            'user_id' => $user->id,
        ]);
        $response = $this->actingAs($user)->get(route('photo::photos.show', $photo->id));
        $response->assertOk()->assertSee($photo->caption);
    }

    /**
     * @test
     */
    public function a_user_can_not_see_others_photo(): void
    {
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();
        $photo = factory(Photo::class)->create([
            'user_id' => $user->id,
        ]);
        $response = $this->actingAs($otherUser)->get(route('photo::photos.show', $photo->id));
        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function a_user_can_update_his_own_photo(): void
    {
        $storage = Storage::fake('public');
        $file = UploadedFile::fake()->image('default.jpg');
        $user = factory(User::class)->create();

        $photo = factory(Photo::class)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->followingRedirects()
            ->actingAs($user)
            ->from(route('photo::photos.edit', $photo->id))
            ->put(route('photo::photos.update', $photo->id), [
                'file'    => $file,
                'caption' => 'This is a testing photo',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas((new Photo())->getTable(), [
            'id'  => $photo->id,
            'src' => 'images/this-is-a-testing-photo.jpeg',
        ]);
        $storage->assertExists('images/this-is-a-testing-photo.jpeg');
    }

    /**
     * @test
     */
    public function a_user_cannot_update_others_photo()
    {
        $storage = Storage::fake('public');
        $file = UploadedFile::fake()->image('default.jpg');
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();
        $photo = factory(Photo::class)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->followingRedirects()
            ->actingAs($otherUser)
            ->from(route('photo::photos.edit', $photo->id))
            ->put(route('photo::photos.update', $photo->id), [
                'file'    => $file,
                'caption' => 'This is a testing photo',
            ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing((new Photo())->getTable(), [
            'id'  => $photo->id,
            'src' => 'images/this-is-a-testing-photo.jpeg',
        ]);
        $storage->assertMissing('images/this-is-a-testing-photo.jpeg');
    }

    /**
     * @test
     */
    public function a_user_can_delete_his_own_photo()
    {
        $user = factory(User::class)->create();
        $photo = factory(Photo::class)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->followingRedirects()
            ->actingAs($user)
            ->from(route('photo::photos.index'))
            ->delete(route('photo::photos.destroy', $photo->id));

        $response->assertOk();

        $this->assertDatabaseMissing((new Photo())->getTable(), [
            'id' => $photo->id,
        ]);
    }

    /**
     * @test
     */
    public function a_user_cannot_delete_others_photo()
    {
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();

        $photo = factory(Photo::class)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->followingRedirects()
            ->actingAs($otherUser)
            ->from(route('photo::photos.index'))
            ->delete(route('photo::photos.destroy', $photo->id));

        $response->assertForbidden();

        $this->assertDatabaseHas((new Photo())->getTable(), [
            'id' => $photo->id,
        ]);
    }
}
