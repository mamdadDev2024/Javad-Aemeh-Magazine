<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Comment;
use App\Models\Article;
use App\Models\Magazine;
use App\Models\Category;
use App\Models\Scope;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'user']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'super admin']);
        Role::create(['name' => 'writer']);
    }

    /** @test */
    public function admin_can_view_panel()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.panel'));

        $response->assertStatus(200);
        $response->assertViewIs('admin-panel.panel');
    }

    /** @test */
    public function non_admin_cannot_view_panel()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->get(route('admin.panel'));

        $response->assertStatus(403); // Forbidden
    }

    /** @test */
    public function admin_can_update_user_roles()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($admin)->post(route('admin.update.users'), [
            'statuses' => [$user->id => 1],
            'roles' => [$user->id => Role::where('name', 'writer')->first()->id]
        ]);

        $response->assertRedirect(route('admin.index_users'));
        $user->refresh();
        $this->assertTrue($user->hasRole('writer'));
        $this->assertEquals(1, $user->status);
    }



    /** @test */
    public function admin_can_approve_comment()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $article = Article::factory()->create();
        $comment = Comment::factory()->create([
            'commentable_id' => $article->id,
            'commentable_type' => Article::class,
            'status' => 0
        ]);

        $response = $this->actingAs($admin)->post(route('admin.comment.accept', $comment->id));

        $response->assertRedirect();
        $comment->refresh();
        $this->assertEquals(1, $comment->status);
    }

    /** @test */
    public function admin_can_delete_comment()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $comment = Comment::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.comment.delete', $comment->id));

        $response->assertRedirect();
        $this->assertSoftDeleted($comment);
    }

    /** @test */
    public function admin_can_approve_article()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $article = Article::factory()->create(['status' => 0]);

        $response = $this->actingAs($admin)->post(route('admin.accept_article', $article->id));

        $response->assertRedirect();
        $article->refresh();
        $this->assertEquals(1, $article->status);
    }

    /** @test */
    public function admin_can_delete_magazine()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $magazine = Magazine::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.delete_magazine', $magazine->id));

        $response->assertRedirect();
        $this->assertSoftDeleted($magazine);
    }


    /** @test */
    public function admin_can_approve_all_comments()
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Comment::factory()->create(['status' => 0]);
        Comment::factory()->create(['status' => 0]);

        $response = $this->actingAs($admin)->get(route('admin.comment.accept.all'));

        $response->assertRedirect();
        $this->assertEquals(0, Comment::where('status', 0)->count());
    }

    /** @test */
    public function non_admin_cannot_access_admin_functions()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->get(route('admin.comment.accept.all'));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}