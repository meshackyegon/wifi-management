<?php

use Livewire\Volt\Component;
use App\Models\Router;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public function with(): array
    {
        return [
            'routers' => Router::orderBy('name')->paginate(10),
        ];
    }

    public function delete($id)
    {
        $router = Router::findOrFail($id);
        $router->delete();
        
        session()->flash('success', 'Router deleted successfully.');
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Routers') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Manage Routers</h3>
                        <a href="{{ route('admin.routers.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Add New Router
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Name</th>
                                    <th class="py-2 px-4 border-b text-left">IP Address</th>
                                    <th class="py-2 px-4 border-b text-left">Location</th>
                                    <th class="py-2 px-4 border-b text-left">Status</th>
                                    <th class="py-2 px-4 border-b text-left">Last Seen</th>
                                    <th class="py-2 px-4 border-b text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($routers as $router)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-2 px-4 border-b font-medium">{{ $router->name }}</td>
                                        <td class="py-2 px-4 border-b">{{ $router->ip_address }}</td>
                                        <td class="py-2 px-4 border-b">{{ $router->location ?? 'Not set' }}</td>
                                        <td class="py-2 px-4 border-b">
                                            <span class="px-2 py-1 text-xs rounded-full {{ $router->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $router->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            {{ $router->last_seen_at ? $router->last_seen_at->diffForHumans() : 'Never' }}
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('admin.routers.test', $router) }}" class="text-green-600 hover:text-green-900">Test</a>
                                                <a href="{{ route('admin.routers.edit', $router) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                                <button wire:click="delete({{ $router->id }})" wire:confirm="Are you sure you want to delete this router?" class="text-red-600 hover:text-red-900">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-4 px-4 text-center text-gray-500">
                                            No routers found. <a href="{{ route('admin.routers.create') }}" class="text-blue-600">Add one now</a>.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $routers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
