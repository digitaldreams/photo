<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Photo\Models\Album;
use Tests\TestCase;

class AlbumTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function a_user_can_create_photo_album()
    {
        $user = factory(User::class)->create();
        $albumData = factory(Album::class)->make();

        $response = $this->actingAs($user)
            ->followingRedirects()
            ->from(route('photo::albums.create'))
            ->post(route('photo::albums.store'), [
                'name' => $albumData->name,
                'description' => $albumData->description,
            ]);
        $response->assertOk();

        $this->assertDatabaseHas((new Album())->getTable(), [
            'user_id' => $user->id,
            'name' => $albumData->name,
            'description' => $albumData->description,
        ]);
    }

    /**
     * @test
     */
    public function a_user_can_update_his_own_album(): void
    {
        $user = factory(User::class)->create();
        $album = factory(Album::class)->create(['user_id' => $user->id]);
        $albumData = factory(Album::class)->make();

        $response = $this->actingAs($user)
            ->followingRedirects()
            ->from(route('photo::albums.edit', $album->id))
            ->put(route('photo::albums.update', $album->id), [
                'name' => $albumData->name,
                'description' => $albumData->description,
            ]);
        $response->assertOk();

        $this->assertDatabaseHas((new Album())->getTable(), [
            'id' => $album->id,
            'name' => $albumData->name,
            'description' => $albumData->description,
        ]);
    }

    /**
     * @test
     */
    public function a_user_cannot_update_others_album()
    {
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();

        $album = factory(Album::class)->create(['user_id' => $user->id]);
        $albumData = factory(Album::class)->make();

        $response = $this->actingAs($otherUser)
            ->followingRedirects()
            ->from(route('photo::albums.edit', $album->id))
            ->put(route('photo::albums.update', $album->id), [
                'name' => $albumData->name,
                'description' => $albumData->description,
            ]);
        $response->assertForbidden();

        $this->assertDatabaseMissing((new Album())->getTable(), [
            'id' => $album->id,
            'name' => $albumData->name,
            'description' => $albumData->description,
        ]);
    }

    public function a_user_can_see_details_of_his_own_album()
    {

    }

    public function a_user_cannot_see_details_of_others_album()
    {

    }

    /**
     * @test
     */
    public function a_user_can_delete_an_album_he_created(): void
    {
        $user = factory(User::class)->create();

        $album = factory(Album::class)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->followingRedirects()
            ->from(route('photo::albums.index'))
            ->delete(route('photo::albums.destroy', $album->id));

        $response->assertOk();
        $this->assertDatabaseMissing((new Album())->getTable(), [
            'id' => $album->id,
        ]);
    }

    /**
     * @test
     */
    public function a_user_cannot_delete_others_album(): void
    {
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();

        $album = factory(Album::class)->create(['user_id' => $user->id]);

        $response = $this->actingAs($otherUser)
            ->followingRedirects()
            ->from(route('photo::albums.index'))
            ->put(route('photo::albums.destroy', $album->id));

        $response->assertForbidden();
    }

}
