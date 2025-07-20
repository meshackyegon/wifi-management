<?php

use Livewire\Volt\Component;
use App\Models\Router;

new class extends Component {
    public Router $router;
    public $name = '';
    public $ip_address = '';
    public $location = '';
    public $secret = '';
    public $api_username = '';
    public $api_password = '';
    public $is_active = true;

    public function mount(Router $router): void
    {
        $this->router = $router;
        $this->name = $router->name;
        $this->ip_address = $router->ip_address;
        $this->location = $router->location;
        $this->secret = $router->secret;
        $this->api_username = $router->api_username;
        $this->api_password = $router->api_password;
        $this->is_active = $router->is_active;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip|unique:routers,ip_address,' . $this->router->id,
            'location' => 'nullable|string|max:255',
            'secret' => 'required|string|max:255',
            'api_username' => 'nullable|string|max:255',
            'api_password' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ];
    }

    public function save()
    {
        $this->validate();

        $this->router->update([
            'name' => $this->name,
            'ip_address' => $this->ip_address,
            'location' => $this->location,
            'secret' => $this->secret,
            'api_username' => $this->api_username,
            'api_password' => $this->api_password,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'Router updated successfully.');
        
        return redirect()->route('admin.routers.index');
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Router') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form wire:submit="save">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Router Name</label>
                                <input type="text" wire:model="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="ip_address" class="block text-sm font-medium text-gray-700">IP Address</label>
                                <input type="text" wire:model="ip_address" id="ip_address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('ip_address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                                <input type="text" wire:model="location" id="location" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('location') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="secret" class="block text-sm font-medium text-gray-700">Secret Key</label>
                                <input type="password" wire:model="secret" id="secret" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('secret') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="api_username" class="block text-sm font-medium text-gray-700">API Username</label>
                                <input type="text" wire:model="api_username" id="api_username" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('api_username') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="api_password" class="block text-sm font-medium text-gray-700">API Password</label>
                                <input type="password" wire:model="api_password" id="api_password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('api_password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" wire:model="is_active" id="is_active" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">Active</label>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <a href="{{ route('admin.routers.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update Router
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
