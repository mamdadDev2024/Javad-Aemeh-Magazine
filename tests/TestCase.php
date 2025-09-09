<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Setup roles and permissions for testing
     */
    protected function setUpRolesAndPermissions(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $roles = ['user', 'admin', 'super admin', 'writer'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Create permissions if needed
        $permissions = [
            'create articles',
            'edit articles',
            'delete articles',
            'approve comments',
            'manage users',
            'manage categories',
            'manage settings'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $adminRole = Role::findByName('admin');
        $superAdminRole = Role::findByName('super admin');
        $writerRole = Role::findByName('writer');

        $writerRole->givePermissionTo(['create articles', 'edit articles']);
        $adminRole->givePermissionTo(['approve comments', 'manage categories']);
        $superAdminRole->givePermissionTo(Permission::all());
    }

    /**
     * Create a user with specific role
     */
    protected function createUserWithRole(string $role, array $attributes = []): \App\Models\User
    {
        $user = \App\Models\User::factory()->create($attributes);
        $user->assignRole($role);
        return $user;
    }

    /**
     * Create test content (magazine with articles)
     */
    protected function createTestMagazineWithArticles(\App\Models\User $user = null): array
    {
        if (!$user) {
            $user = $this->createUserWithRole('writer');
        }

        $magazine = \App\Models\Magazine::factory()->create(['user_id' => $user->id]);
        
        $articles = \App\Models\Article::factory()->count(3)->create([
            'magazine_id' => $magazine->id
        ]);

        return [
            'user' => $user,
            'magazine' => $magazine,
            'articles' => $articles
        ];
    }

    /**
     * Create test categories
     */
    protected function createTestCategories(): \Illuminate\Support\Collection
    {
        return collect([
            \App\Models\Category::factory()->create(['name' => 'Technology']),
            \App\Models\Category::factory()->create(['name' => 'Science']),
            \App\Models\Category::factory()->create(['name' => 'Education']),
        ]);
    }

    /**
     * Assert that a model has specific relationships
     */
    protected function assertHasRelationships($model, array $relationships): void
    {
        foreach ($relationships as $relationship) {
            $this->assertTrue(
                method_exists($model, $relationship),
                "Model " . get_class($model) . " should have {$relationship} relationship"
            );
        }
    }

    /**
     * Assert that a user can perform an action
     */
    protected function assertUserCanPerformAction($user, string $route, array $parameters = [], string $method = 'GET'): void
    {
        $response = $this->actingAs($user);
        
        switch (strtoupper($method)) {
            case 'GET':
                $response = $response->get(route($route, $parameters));
                break;
            case 'POST':
                $response = $response->post(route($route, $parameters));
                break;
            case 'PUT':
                $response = $response->put(route($route, $parameters));
                break;
            case 'DELETE':
                $response = $response->delete(route($route, $parameters));
                break;
        }

        $response->assertStatus(200);
    }

    /**
     * Assert that a user cannot perform an action
     */
    protected function assertUserCannotPerformAction($user, string $route, array $parameters = [], string $method = 'GET'): void
    {
        $response = $this->actingAs($user);
        
        switch (strtoupper($method)) {
            case 'GET':
                $response = $response->get(route($route, $parameters));
                break;
            case 'POST':
                $response = $response->post(route($route, $parameters));
                break;
            case 'PUT':
                $response = $response->put(route($route, $parameters));
                break;
            case 'DELETE':
                $response = $response->delete(route($route, $parameters));
                break;
        }

        $this->assertContains($response->getStatusCode(), [401, 403, 404]);
    }

    /**
     * Create test comments for a model
     */
    protected function createTestComments($commentable, int $count = 3, bool $approved = false): \Illuminate\Support\Collection
    {
        $user = $this->createUserWithRole('user');
        
        return \App\Models\Comment::factory()->count($count)->create([
            'user_id' => $user->id,
            'commentable_id' => $commentable->id,
            'commentable_type' => get_class($commentable),
            'status' => $approved
        ]);
    }

    /**
     * Create test views for a model
     */
    protected function createTestViews($viewable, int $count = 5): \Illuminate\Support\Collection
    {
        $views = collect();
        
        for ($i = 0; $i < $count; $i++) {
            $user = $this->createUserWithRole('user');
            $views->push(\App\Models\View::create([
                'user_id' => $user->id,
                'ip_address' => "192.168.1." . ($i + 1),
                'viewable_id' => $viewable->id,
                'viewable_type' => get_class($viewable)
            ]));
        }

        return $views;
    }

    /**
     * Create test likes for a model
     */
    protected function createTestLikes($likeable, int $count = 3): \Illuminate\Support\Collection
    {
        $users = collect();
        
        for ($i = 0; $i < $count; $i++) {
            $user = $this->createUserWithRole('user');
            $user->like($likeable);
            $users->push($user);
        }

        return $users;
    }

    /**
     * Assert database has polymorphic relationship
     */
    protected function assertDatabaseHasPolymorphicRelation(string $table, $model, array $additionalData = []): void
    {
        $data = array_merge([
            get_class($model) . '_id' => $model->id,
            get_class($model) . '_type' => get_class($model),
        ], $additionalData);

        $this->assertDatabaseHas($table, $data);
    }

    /**
     * Assert model has specific fillable attributes
     */
    protected function assertModelHasFillableAttributes($model, array $expectedFillable): void
    {
        $actualFillable = $model->getFillable();
        
        foreach ($expectedFillable as $attribute) {
            $this->assertContains(
                $attribute,
                $actualFillable,
                "Model " . get_class($model) . " should have '{$attribute}' as fillable attribute"
            );
        }
    }

    /**
     * Assert model has specific hidden attributes
     */
    protected function assertModelHasHiddenAttributes($model, array $expectedHidden): void
    {
        $actualHidden = $model->getHidden();
        
        foreach ($expectedHidden as $attribute) {
            $this->assertContains(
                $attribute,
                $actualHidden,
                "Model " . get_class($model) . " should have '{$attribute}' as hidden attribute"
            );
        }
    }

    /**
     * Assert model has specific casts
     */
    protected function assertModelHasCasts($model, array $expectedCasts): void
    {
        $actualCasts = $model->getCasts();
        
        foreach ($expectedCasts as $attribute => $cast) {
            $this->assertArrayHasKey(
                $attribute,
                $actualCasts,
                "Model " . get_class($model) . " should have cast for '{$attribute}'"
            );
            
            $this->assertEquals(
                $cast,
                $actualCasts[$attribute],
                "Model " . get_class($model) . " should cast '{$attribute}' to '{$cast}'"
            );
        }
    }
}
