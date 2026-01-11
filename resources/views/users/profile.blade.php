@extends('layouts.app')

@section('title', 'User Profile')

@section('content')
<div class="container py-6">
    <div class="col-12">
        <x-widget title="Profile">

            <!-- Header -->
            <div class="flex flex-col md:flex-row items-center gap-4 mb-6">
                <!-- Avatar -->
                <div class="w-24 h-24 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden">
                    @if(auth()->user()->profile_image_url)
                        <img src="{{ auth()->user()->profile_image_url }}" alt="Profile Image" class="w-full h-full object-cover">
                    @else
                        <i class="bi bi-person-fill text-gray-400 text-3xl"></i>
                    @endif
                </div>

                <!-- User Info -->
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h2>
                    <p class="text-gray-500">{{ auth()->user()->email }}</p>
                    <span class="inline-block mt-1 px-2 py-1 text-sm rounded-full bg-blue-100 text-blue-700">
                        {{ ucfirst(auth()->user()->role) }}
                    </span>

                    <!-- Profile Actions -->
                    <div class="mt-3 flex gap-3">
                        <a href="{{ route('users.edit', auth()->user()->id) }}" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                            Edit Profile
                        </a>

                        <form class="logout-form" method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Other Info -->
            <div class="space-y-2 text-gray-700">
                <div><strong>First Name:</strong> {{ auth()->user()->first_name }}</div>
                <div><strong>Last Name:</strong> {{ auth()->user()->last_name }}</div>
                <div><strong>Email:</strong> {{ auth()->user()->email }}</div>
                <div><strong>Role:</strong> {{ ucfirst(auth()->user()->role) }}</div>
                <div><strong>Gender:</strong> {{ auth()->user()->gender ?? 'Not specified' }}</div>
                <div><strong>Last Login:</strong> {{ auth()->user()->last_login?->format('D, M d Y H:i') ?? 'Never' }}</div>
            </div>

        </x-widget>
    </div>
</div>
@endsection
