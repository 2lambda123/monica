<?php

namespace App\Http\Controllers\Vault\Contact\Modules\Label;

use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\Vault\ManageLabels\CreateLabel;
use App\Services\Contact\AssignLabel\AssignLabel;
use App\Services\Contact\AssignLabel\RemoveLabel;
use App\Http\Controllers\Vault\Contact\Modules\Label\ViewHelpers\ModuleLabelViewHelper;

class ContactModuleLabelController extends Controller
{
    public function store(Request $request, int $vaultId, int $contactId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'bg_color' => 'bg-neutral-200',
            'text_color' => 'text-neutral-800',
        ];

        $label = (new CreateLabel)->execute($data);

        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'contact_id' => $contactId,
            'label_id' => $label->id,
        ];

        $label = (new AssignLabel)->execute($data);

        $contact = Contact::find($contactId);

        return response()->json([
            'data' => ModuleLabelViewHelper::dtoLabel($label, $contact, true),
        ], 201);
    }

    public function update(Request $request, int $vaultId, int $contactId, int $labelId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'contact_id' => $contactId,
            'label_id' => $labelId,
        ];

        $label = (new AssignLabel)->execute($data);
        $contact = Contact::find($contactId);

        return response()->json([
            'data' => ModuleLabelViewHelper::dtoLabel($label, $contact, true),
        ], 200);
    }

    public function destroy(Request $request, int $vaultId, int $contactId, int $labelId)
    {
        $data = [
            'account_id' => Auth::user()->account_id,
            'author_id' => Auth::user()->id,
            'vault_id' => $vaultId,
            'contact_id' => $contactId,
            'label_id' => $labelId,
        ];

        $label = (new RemoveLabel)->execute($data);
        $contact = Contact::find($contactId);

        return response()->json([
            'data' => ModuleLabelViewHelper::dtoLabel($label, $contact, false),
        ], 200);
    }
}
