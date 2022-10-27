<?php

namespace App\Vault\ManageVaultSettings\Services;

use App\Interfaces\ServiceInterface;
use App\Models\Tag;
use App\Services\BaseService;
use Illuminate\Support\Str;

class UpdateTag extends BaseService implements ServiceInterface
{
    /**
     * Get the validation rules that apply to the service.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'account_id' => 'required|integer|exists:accounts,id',
            'author_id' => 'required|integer|exists:users,id',
            'vault_id' => 'required|integer|exists:vaults,id',
            'tag_id' => 'required|integer|exists:tags,id',
            'name' => 'required|string|max:255',
        ];
    }

    /**
     * Get the permissions that apply to the user calling the service.
     *
     * @return array
     */
    public function permissions(): array
    {
        return [
            'author_must_belong_to_account',
            'author_must_be_vault_editor',
            'vault_must_belong_to_account',
        ];
    }

    /**
     * Update a tag.
     *
     * @param  array  $data
     * @return Tag
     */
    public function execute(array $data): Tag
    {
        $this->validateRules($data);

        $tag = Tag::where('vault_id', $data['vault_id'])
            ->findOrFail($data['tag_id']);

        $tag->name = $data['name'];
        $tag->slug = Str::slug($data['name'], '-');
        $tag->save();

        return $tag;
    }
}
