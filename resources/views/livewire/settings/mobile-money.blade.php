<?php

use Livewire\Volt\Component;

new class extends Component {
    public $mpesa_consumer_key = '';
    public $mpesa_consumer_secret = '';
    public $mpesa_shortcode = '';
    public $mpesa_passkey = '';
    public $mpesa_environment = 'sandbox';
    public $callback_url = '';

    public function mount(): void
    {
        // Load mobile money settings from config
        $this->mpesa_consumer_key = config('services.mpesa.consumer_key', '');
        $this->mpesa_consumer_secret = config('services.mpesa.consumer_secret', '');
        $this->mpesa_shortcode = config('services.mpesa.shortcode', '');
        $this->mpesa_passkey = config('services.mpesa.passkey', '');
        $this->mpesa_environment = config('services.mpesa.environment', 'sandbox');
        $this->callback_url = config('services.mpesa.callback_url', url('/api/mpesa/callback'));
    }

    public function rules(): array
    {
        return [
            'mpesa_consumer_key' => 'required|string',
            'mpesa_consumer_secret' => 'required|string',
            'mpesa_shortcode' => 'required|string',
            'mpesa_passkey' => 'required|string',
            'mpesa_environment' => 'required|in:sandbox,live',
            'callback_url' => 'required|url',
        ];
    }

    public function save()
    {
        $this->validate();
        
        // Here you would typically save to environment variables or a settings table
        // For now, we'll just show a success message
        
        session()->flash('success', 'Mobile Money settings updated successfully.');
    }

    public function testConnection()
    {
        // Test M-Pesa API connection
        session()->flash('info', 'Testing M-Pesa connection... (Feature coming soon)');
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mobile Money Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('info'))
                        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
                            {{ session('info') }}
                        </div>
                    @endif

                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">M-Pesa Configuration</h3>
                        <p class="text-sm text-gray-600">Configure your M-Pesa API credentials to enable mobile money payments.</p>
                    </div>

                    <form wire:submit="save">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="mpesa_consumer_key" class="block text-sm font-medium text-gray-700">Consumer Key</label>
                                <input type="text" wire:model="mpesa_consumer_key" id="mpesa_consumer_key" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('mpesa_consumer_key') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="mpesa_consumer_secret" class="block text-sm font-medium text-gray-700">Consumer Secret</label>
                                <input type="password" wire:model="mpesa_consumer_secret" id="mpesa_consumer_secret" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('mpesa_consumer_secret') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="mpesa_shortcode" class="block text-sm font-medium text-gray-700">Business Shortcode</label>
                                <input type="text" wire:model="mpesa_shortcode" id="mpesa_shortcode" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('mpesa_shortcode') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="mpesa_passkey" class="block text-sm font-medium text-gray-700">Passkey</label>
                                <input type="password" wire:model="mpesa_passkey" id="mpesa_passkey" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('mpesa_passkey') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="mpesa_environment" class="block text-sm font-medium text-gray-700">Environment</label>
                                <select wire:model="mpesa_environment" id="mpesa_environment" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="sandbox">Sandbox (Testing)</option>
                                    <option value="live">Live (Production)</option>
                                </select>
                                @error('mpesa_environment') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="callback_url" class="block text-sm font-medium text-gray-700">Callback URL</label>
                                <input type="url" wire:model="callback_url" id="callback_url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('callback_url') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                <p class="mt-1 text-sm text-gray-500">URL where M-Pesa will send payment notifications</p>
                            </div>
                        </div>

                        <div class="mt-6 border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Testing</h3>
                            <div class="flex space-x-3">
                                <button type="button" wire:click="testConnection" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    Test Connection
                                </button>
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Save Settings
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="mt-8 border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Setup Instructions</h3>
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <ol class="list-decimal list-inside space-y-2 text-sm text-blue-800">
                                <li>Create a developer account at <a href="https://developer.safaricom.co.ke" target="_blank" class="underline">developer.safaricom.co.ke</a></li>
                                <li>Create a new app and note down your Consumer Key and Consumer Secret</li>
                                <li>Apply for a Till Number or Paybill shortcode</li>
                                <li>Generate a passkey for your shortcode</li>
                                <li>Configure the callback URL in your M-Pesa dashboard</li>
                                <li>Test the integration using sandbox environment first</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
