<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-md">
                <div class="p-4 flex items-center">
                    <img src="{{ $post->user->photo_profile ?? 'https://placehold.co/50' }}"
                        class="rounded-full h-10 w-10 mr-2" alt="avatar">
                    <span class="font-bold">{{ $post->user->name ?? 'Pengguna' }}</span>
                </div>
                @if ($post->type === 'image')
                    <img src="{{ $post->link }}" class="w-full h-auto" alt="{{ $post->caption }}">
                @elseif ($post->type === 'video')
                    <video src="{{ $post->link }}" class="w-full h-auto" controls></video>
                @endif
                <div class="p-4">
                    <p class="text-sm mt-1">
                        <span class="font-bold me-1">{{ $post->user->name ?? 'Pengguna' }}</span>
                        {{ $post->caption }}
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md mt-4 p-4">
                <form action="{{ route('comments.storeWeb') }}" method="POST">
                    @csrf
                    <input type="hidden" name="post_id" value="{{ $post->id }}">
                    <div class="flex items-center">
                        <textarea name="comment" rows="1"
                            class="block w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            placeholder="Tambahkan komentar..."
                            required></textarea>
                        <button type="submit"
                            class="ml-4 inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">Kirim</button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow-md mt-4 p-4">
                <h3 class="font-bold mb-3">Komentar</h3>
                @forelse($post->comments as $comment)
                    <div class="flex items-start space-x-3 mb-3">
                        <img src="{{ $comment->user->photo_profile ?? 'https://placehold.co/40' }}"
                            class="h-10 w-10 rounded-full" alt="avatar">
                        <div class="flex-1">
                            <p class="text-sm">
                                <span class="font-bold">{{ $comment->user->name ?? 'Pengguna' }}</span>
                                {{ $comment->comment }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Belum ada komentar.</p>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
