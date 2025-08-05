<?php

use Livewire\Volt\Component;
use App\Models\Router;

new class extends Component {
    public Router $router;
    public $testResult = null;
    public $testing = false;

    public function mount(Router $router): void
    {
        $this->router = $router;
    }

    public function testConnection()
    {
        $this->testing = true;
        $this->testResult = null;

        try {
            // Simple ping test
            $response = @file_get_contents("http://{$this->router->ip_address}", false, stream_context_create([
                'http' => [
                    'timeout' => 5
                ]
            ]));
            
            if ($response !== false) {
                $this->testResult = [
                    'success' => true,
                    'message' => 'Router is reachable and responding.',
                    'response_time' => '< 5s'
                ];
                
                // Update last seen
                $this->router->update(['last_seen_at' => now()]);
            } else {
                $this->testResult = [
                    'success' => false,
                    'message' => 'Router is not responding. Please check the IP address and network connectivity.',
                    'response_time' => 'Timeout'
                ];
            }
        } catch (\Exception $e) {
            $this->testResult = [
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage(),
                'response_time' => 'Error'
            ];
        }

        $this->testing = false;
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Test Router Connection') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-4">Router Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg">
                            <div>
                                <strong>Name:</strong> {{ $router->name }}
                            </div>
                            <div>
                                <strong>IP Address:</strong> {{ $router->ip_address }}
                            </div>
                            <div>
                                <strong>Location:</strong> {{ $router->location ?? 'Not set' }}
                            </div>
                            <div>
                                <strong>Status:</strong> 
                                <span class="px-2 py-1 text-xs rounded-full {{ $router->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $router->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <div>
                                <strong>Last Seen:</strong> {{ $router->last_seen_at ? $router->last_seen_at->diffForHumans() : 'Never' }}
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <button wire:click="testConnection" @disabled($testing) class="bg-blue-500 hover:bg-blue-700 disabled:bg-blue-300 text-white font-bold py-2 px-4 rounded">
                            @if($testing)
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Testing Connection...
                            @else
                                Test Connection
                            @endif
                        </button>
                    </div>

                    @if($testResult)
                        <div class="mb-6">
                            <h4 class="text-lg font-medium mb-3">Test Results</h4>
                            <div class="p-4 rounded-lg {{ $testResult['success'] ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
                                <div class="flex items-center mb-2">
                                    @if($testResult['success'])
                                        <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span class="text-green-800 font-medium">Connection Successful</span>
                                    @else
                                        <svg class="h-5 w-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <span class="text-red-800 font-medium">Connection Failed</span>
                                    @endif
                                </div>
                                <p class="{{ $testResult['success'] ? 'text-green-700' : 'text-red-700' }}">
                                    {{ $testResult['message'] }}
                                </p>
                                <p class="text-sm {{ $testResult['success'] ? 'text-green-600' : 'text-red-600' }} mt-1">
                                    Response Time: {{ $testResult['response_time'] }}
                                </p>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('routers.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Back to Routers
                        </a>
                        <a href="{{ route('routers.edit', $router) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Edit Router
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
