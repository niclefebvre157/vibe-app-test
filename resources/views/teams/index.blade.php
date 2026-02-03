<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Teams') }}
            </h2>
            <a href="{{ route('teams.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                {{ __('Create Team') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($teams->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('You are not a member of any teams yet.') }}</p>
                    @else
                        <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($teams as $team)
                                <li class="py-4">
                                    <a href="{{ route('teams.show', $team) }}" class="text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400 font-medium">
                                        {{ $team->name }}
                                    </a>
                                    <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">{{ ucfirst($team->pivot->role) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
