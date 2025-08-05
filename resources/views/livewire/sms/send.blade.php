<?php

use Livewire\Volt\Component;
use App\Models\User;
use App\Models\SmsLog;
use App\Services\SmsPasswordResetService;

new class extends Component {
    public $recipient_type = 'single';
    public $phone_number = '';
    public $role_filter = '';
    public $message = '';
    public $message_type = 'general';

    public function rules()
    {
        $rules = [
            'message' => ['required', 'string', 'max:160'],
            'message_type' => ['required', 'string', 'in:general,voucher,password_reset,notification'],
        ];

        if ($this->recipient_type === 'single') {
            $rules['phone_number'] = ['required', 'string', 'regex:/^\+?[1-9]\d{1,14}$/'];
        } elseif ($this->recipient_type === 'role') {
            $rules['role_filter'] = ['required', 'exists:roles,name'];
        }

        return $rules;
    }

    public function send()
    {
        $this->validate();

        $recipients = $this->getRecipients();
        $sentCount = 0;
        $failedCount = 0;

        foreach ($recipients as $phoneNumber) {
            try {
                $smsService = new SmsPasswordResetService();
                $response = $smsService->sendSms($phoneNumber, $this->message);

                SmsLog::create([
                    'phone_number' => $phoneNumber,
                    'message' => $this->message,
                    'type' => $this->message_type,
                    'status' => 'sent',
                    'response' => is_array($response) ? json_encode($response) : $response,
                ]);

                $sentCount++;
            } catch (\Exception $e) {
                SmsLog::create([
                    'phone_number' => $phoneNumber,
                    'message' => $this->message,
                    'type' => $this->message_type,
                    'status' => 'failed',
                    'response' => $e->getMessage(),
                ]);

                $failedCount++;
            }
        }

        if ($sentCount > 0) {
            session()->flash('success', "SMS sent successfully to {$sentCount} recipient(s)." . 
                ($failedCount > 0 ? " {$failedCount} failed." : ''));
        } else {
            session()->flash('error', "Failed to send SMS to any recipients.");
        }

        $this->reset(['phone_number', 'message', 'role_filter']);
    }

    private function getRecipients()
    {
        switch ($this->recipient_type) {
            case 'single':
                return [$this->phone_number];
            
            case 'role':
                return User::role($this->role_filter)
                    ->whereNotNull('phone')
                    ->pluck('phone')
                    ->toArray();
            
            case 'all':
                return User::whereNotNull('phone')
                    ->pluck('phone')
                    ->toArray();
            
            default:
                return [];
        }
    }

    public function with()
    {
        return [
            'roles' => \Spatie\Permission\Models\Role::all(),
            'recipientCount' => $this->getRecipientCount(),
        ];
    }

    private function getRecipientCount()
    {
        switch ($this->recipient_type) {
            case 'single':
                return 1;
            
            case 'role':
                if (!$this->role_filter) return 0;
                return User::role($this->role_filter)
                    ->whereNotNull('phone')
                    ->count();
            
            case 'all':
                return User::whereNotNull('phone')->count();
            
            default:
                return 0;
        }
    }

    public function updatedRecipientType()
    {
        $this->reset(['phone_number', 'role_filter']);
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Send SMS') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium">Send SMS Message</h3>
                        <p class="text-sm text-gray-600">Send SMS messages to individual users, specific roles, or all users.</p>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form wire:submit="send" class="space-y-6">
                        <!-- Recipient Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Send To</label>
                            <div class="space-y-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" wire:model.live="recipient_type" value="single" class="form-radio">
                                    <span class="ml-2">Single Phone Number</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" wire:model.live="recipient_type" value="role" class="form-radio">
                                    <span class="ml-2">Users by Role</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" wire:model.live="recipient_type" value="all" class="form-radio">
                                    <span class="ml-2">All Users</span>
                                </label>
                            </div>
                        </div>

                        <!-- Single Phone Number -->
                        @if($recipient_type === 'single')
                            <div>
                                <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                                <input 
                                    type="text" 
                                    id="phone_number"
                                    wire:model="phone_number" 
                                    placeholder="+256701234567"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                >
                                @error('phone_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <!-- Role Filter -->
                        @if($recipient_type === 'role')
                            <div>
                                <label for="role_filter" class="block text-sm font-medium text-gray-700">Select Role</label>
                                <select 
                                    id="role_filter"
                                    wire:model.live="role_filter" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                    required
                                >
                                    <option value="">Choose a role...</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                    @endforeach
                                </select>
                                @error('role_filter')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <!-- Recipient Count -->
                        @if($recipientCount > 0)
                            <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-blue-800">
                                            {{ $recipientCount }} recipient(s) will receive this message
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Message Type -->
                        <div>
                            <label for="message_type" class="block text-sm font-medium text-gray-700">Message Type</label>
                            <select 
                                id="message_type"
                                wire:model="message_type" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            >
                                <option value="general">General</option>
                                <option value="voucher">Voucher Related</option>
                                <option value="password_reset">Password Reset</option>
                                <option value="notification">Notification</option>
                            </select>
                            @error('message_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Message -->
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                            <textarea 
                                id="message"
                                wire:model="message" 
                                rows="4"
                                maxlength="160"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Enter your SMS message (max 160 characters)"
                                required
                            ></textarea>
                            <div class="mt-1 text-sm text-gray-500">
                                {{ strlen($message) }}/160 characters
                            </div>
                            @error('message')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('sms.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" 
                                    @if($recipientCount === 0) disabled @endif>
                                Send SMS
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
