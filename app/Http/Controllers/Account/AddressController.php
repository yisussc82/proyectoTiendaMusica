<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;
use App\Models\ClientAddress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AddressController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', ClientAddress::class);

        return view('account.addresses.index', [
            'addresses' => $request->user()->client->addresses()->orderByDesc('is_default')->get(),
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', ClientAddress::class);

        return view('account.addresses.create', [
            'address' => new ClientAddress,
            'provinces' => $this->provinces(),
        ]);
    }

    public function store(AddressRequest $request): RedirectResponse
    {
        $address = $request->user()->client->addresses()->create($request->validated());

        if ($address->is_default) {
            $address->markAsDefault();
        }

        return redirect()->route('account.addresses.index')
            ->with('flash.banner', __('Address saved.'));
    }

    public function edit(ClientAddress $address): View
    {
        Gate::authorize('update', $address);

        return view('account.addresses.edit', [
            'address' => $address,
            'provinces' => $this->provinces(),
        ]);
    }

    public function update(AddressRequest $request, ClientAddress $address): RedirectResponse
    {
        $address->update($request->validated());

        if ($address->is_default) {
            $address->markAsDefault();
        }

        return redirect()->route('account.addresses.index')
            ->with('flash.banner', __('Address updated.'));
    }

    public function destroy(ClientAddress $address): RedirectResponse
    {
        Gate::authorize('delete', $address);

        $address->delete();

        return redirect()->route('account.addresses.index')
            ->with('flash.banner', __('Address deleted.'));
    }

    private function provinces(): array
    {
        $names = array_keys(config('provinces'));

        return array_combine($names, $names);
    }
}
