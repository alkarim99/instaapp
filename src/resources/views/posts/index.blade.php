@extends('layouts.app')

@section('title', 'Feed Postingan')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 my-5">
        <div class="flex justify-between align-items-center mb-4">
            <h1 class="h3">Feed Postingan</h1>
            <a href="{{ route('posts.create') }}" class="btn btn-primary">
                + Buat Post Baru
            </a>
        </div>

        @if ($posts->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($posts as $post)
                    <div class="bg-white rounded-lg shadow-md p-4">
                        <header class="flex items-center">
                            <img src="{{ $post->user->photo_profile ? url($post->user->photo_profile) : 'https://placehold.co/50' }}" class="rounded-full h-10 w-10 mr-2" alt="avatar">
                            <span class="font-bold">{{ $post->user->name ?? 'Pengguna' }}</span>
                        </header>

                        @if ($post->type === 'image')
                            <img src="{{ $post->link }}" class="w-full h-auto mt-2" alt="{{ $post->caption }}">
                        @elseif($post->type === 'video')
                            <video src="{{ $post->link }}" class="w-full h-auto mt-2" controls></video>
                        @endif

                        <div class="mt-2">
                            <div class="flex items-center mb-2">
                                <button class="text-gray-600 hover:text-gray-900 focus:outline-none focus:text-gray-900 p-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </button>
                                <button class="text-gray-600 hover:text-gray-900 focus:outline-none focus:text-gray-900 p-0 ml-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0-4.418-4.03-8-9-8a9.342 9.342 0 01-2.569.728l-3.982 2.008A9.31 9.31 0 0112 12c0 5.907 4.319 11 9 11 2.29 0 4.923-.576 7.245-1.551l-2.008 3.983z" />
                                    </svg>
                                </button>
                            </div>

                            <p class="font-bold text-sm">
                                {{ $post->total_like ?? 0 }} likes
                            </p>

                            <p class="text-sm mt-1">
                                <span class="font-bold me-1">{{ $post->user->name ?? 'Pengguna' }}</span>
                                {{ $post->caption }}
                            </p>

                            <a href="#" class="text-gray-600 hover:text-gray-900 focus:outline-none focus:text-gray-900 text-sm mt-2">
                                Lihat semua komentar
                            </a>
                        </div>
                    </div>
                @endforeach

                <div class="flex justify-center">
                    {{ $posts->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <p class="text-gray-600">Belum ada postingan. Jadilah yang pertama!</p>
            </div>
        @endif
    </div>
@endsection
