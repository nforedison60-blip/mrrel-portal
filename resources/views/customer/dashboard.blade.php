<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Customer Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <h3 class="text-lg font-semibold mb-6">Welcome back! 👋</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                        {{-- Service Requests --}}
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                            <div class="text-sm text-blue-600 font-medium">My Requests</div>
                            <div class="text-3xl font-bold text-blue-900 mt-2">{{ $serviceRequestCount }}</div>
                            <a href="{{ route('customer.requests.index') }}" class="text-sm text-blue-600 hover:underline mt-2 inline-block">
                                View all →
                            </a>
                        </div>

                        {{-- Quotes --}}
                        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                            <div class="text-sm text-green-600 font-medium">Quotes</div>
                            <div class="text-3xl font-bold text-green-900 mt-2">{{ $quoteCount }}</div>
                            <a href="{{ route('customer.quotes.index') }}" class="text-sm text-green-600 hover:underline mt-2 inline-block">
                                View all →
                            </a>
                        </div>

                        {{-- Bookings --}}
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                            <div class="text-sm text-purple-600 font-medium">Bookings</div>
                            <div class="text-3xl font-bold text-purple-900 mt-2">{{ $bookingCount }}</div>
                            <a href="{{ route('customer.bookings.index') }}" class="text-sm text-purple-600 hover:underline mt-2 inline-block">
                                View all →
                            </a>
                        </div>

                        {{-- Shortlist / Properties --}}
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-6">
                            <div class="text-sm text-orange-600 font-medium">Shortlist</div>
                            <div class="text-3xl font-bold text-orange-900 mt-2">{{ $propertyCount }}</div>
                            <a href="{{ route('customer.shortlist.index') }}" class="text-sm text-orange-600 hover:underline mt-2 inline-block">
                                View all →
                            </a>
                        </div>

                    </div>

                    <div class="mt-8">
                        <a href="{{ route('customer.requests.create') }}"
                           class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-3 rounded-lg">
                            + New Request
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>