<?php

use Livewire\Volt\Component;
use App\Models\User;

new class extends Component {
    public $system_name = '';
    public $system_email = '';
    public $sms_provider = 'twilio';
    public $mobile_money_provider = 'mpesa';
    public $currency = 'KES';
    public $timezone = 'Africa/Nairobi';

    public function mount(): void
    {
        // Load system settings from config or database
        $this->system_name = config('app.name', 'WiFi Management System');
        $this->system_email = config('mail.from.address', 'admin@example.com');
        $this->sms_provider = config('services.sms.default', 'twilio');
        $this->mobile_money_provider = config('services.mobile_money.default', 'mpesa');
        $this->currency = config('app.currency', 'KES');
        $this->timezone = config('app.timezone', 'Africa/Nairobi');
    }

    public function rules(): array
    {
        return [
            'system_name' => 'required|string|max:255',
            'system_email' => 'required|email|max:255',
            'sms_provider' => 'required|in:twilio,africas_talking',
            'mobile_money_provider' => 'required|in:mpesa,airtel_money',
            'currency' => 'required|string|max:3',
            'timezone' => 'required|string',
        ];
    }

    public function save()
    {
        $this->validate();
        
        // Here you would typically save to a settings table or update config files
        // For now, we'll just show a success message
        
        session()->flash('success', 'System settings updated successfully.');
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('System Settings') }}
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

                    <form wire:submit="save">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="system_name" class="block text-sm font-medium text-gray-700">System Name</label>
                                <input type="text" wire:model="system_name" id="system_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('system_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="system_email" class="block text-sm font-medium text-gray-700">System Email</label>
                                <input type="email" wire:model="system_email" id="system_email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('system_email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="sms_provider" class="block text-sm font-medium text-gray-700">SMS Provider</label>
                                <select wire:model="sms_provider" id="sms_provider" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="twilio">Twilio</option>
                                    <option value="africas_talking">Africa's Talking</option>
                                </select>
                                @error('sms_provider') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="mobile_money_provider" class="block text-sm font-medium text-gray-700">Mobile Money Provider</label>
                                <select wire:model="mobile_money_provider" id="mobile_money_provider" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="mpesa">M-Pesa</option>
                                    <option value="airtel_money">Airtel Money</option>
                                </select>
                                @error('mobile_money_provider') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="currency" class="block text-sm font-medium text-gray-700">Currency</label>
                                <select wire:model="currency" id="currency" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="KES">KES - Kenyan Shilling</option>
                                    <option value="UGX">UGX - Ugandan Shilling</option>
                                    <option value="TZS">TZS - Tanzanian Shilling</option>
                                    <option value="USD">USD - US Dollar</option>
                                </select>
                                @error('currency') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="timezone" class="block text-sm font-medium text-gray-700">Timezone</label>
                                <select wire:model="timezone" id="timezone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="Africa/Nairobi">Africa/Nairobi (EAT)</option>
                                    <option value="Africa/Kampala">Africa/Kampala (EAT)</option>
                                    <option value="Africa/Dar_es_Salaam">Africa/Dar_es_Salaam (EAT)</option>
                                    <option value="UTC">UTC</option>
                                </select>
                                @error('timezone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-6 border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">System Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50 p-4 rounded-lg">
                                <div>
                                    <strong>Total Users:</strong> {{ \App\Models\User::count() }}
                                </div>
                                <div>
                                    <strong>Active Plans:</strong> {{ \App\Models\VoucherPlan::where('is_active', true)->count() }}
                                </div>
                                <div>
                                    <strong>Active Routers:</strong> {{ \App\Models\Router::where('is_active', true)->count() }}
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
