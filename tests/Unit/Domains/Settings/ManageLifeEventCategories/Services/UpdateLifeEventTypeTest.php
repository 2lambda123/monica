<?php

namespace Tests\Unit\Domains\Settings\ManageLifeEventCategories\Services;

use App\Exceptions\NotEnoughPermissionException;
use App\Models\Account;
use App\Models\LifeEventCategory;
use App\Models\LifeEventType;
use App\Models\User;
use App\Settings\ManageLifeEventCategories\Services\UpdateLifeEventType;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UpdateLifeEventTypeTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_updates_a_type(): void
    {
        $ross = $this->createAdministrator();
        $category = LifeEventCategory::factory()->create([
            'account_id' => $ross->account_id,
        ]);
        $type = LifeEventType::factory()->create([
            'life_event_category_id' => $category->id,
        ]);
        $this->executeService($ross, $ross->account, $category, $type);
    }

    /** @test */
    public function it_fails_if_wrong_parameters_are_given(): void
    {
        $request = [
            'title' => 'Ross',
        ];

        $this->expectException(ValidationException::class);
        (new UpdateLifeEventType())->execute($request);
    }

    /** @test */
    public function it_fails_if_user_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $ross = $this->createAdministrator();
        $account = Account::factory()->create();
        $category = LifeEventCategory::factory()->create([
            'account_id' => $ross->account_id,
        ]);
        $type = LifeEventType::factory()->create([
            'life_event_category_id' => $category->id,
        ]);
        $this->executeService($ross, $account, $category, $type);
    }

    /** @test */
    public function it_fails_if_category_doesnt_belong_to_account(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $ross = $this->createAdministrator();
        $category = LifeEventCategory::factory()->create();
        $type = LifeEventType::factory()->create([
            'life_event_category_id' => $category->id,
        ]);
        $this->executeService($ross, $ross->account, $category, $type);
    }

    /** @test */
    public function it_fails_if_type_doesnt_belong_to_category(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $ross = $this->createAdministrator();
        $category = LifeEventCategory::factory()->create([
            'account_id' => $ross->account_id,
        ]);
        $type = LifeEventType::factory()->create();
        $this->executeService($ross, $ross->account, $category, $type);
    }

    /** @test */
    public function it_fails_if_user_doesnt_have_right_permission_in_account(): void
    {
        $this->expectException(NotEnoughPermissionException::class);

        $ross = $this->createUser();
        $category = LifeEventCategory::factory()->create([
            'account_id' => $ross->account_id,
        ]);
        $type = LifeEventType::factory()->create([
            'life_event_category_id' => $category->id,
        ]);
        $this->executeService($ross, $ross->account, $category, $type);
    }

    private function executeService(User $author, Account $account, LifeEventCategory $category, LifeEventType $type): void
    {
        $request = [
            'account_id' => $account->id,
            'author_id' => $author->id,
            'life_event_category_id' => $category->id,
            'life_event_type_id' => $type->id,
            'label' => 'type name',
            'can_be_deleted' => true,
        ];

        $type = (new UpdateLifeEventType())->execute($request);

        $this->assertDatabaseHas('life_event_types', [
            'id' => $type->id,
            'life_event_category_id' => $category->id,
            'label' => 'type name',
            'can_be_deleted' => true,
        ]);
    }
}
