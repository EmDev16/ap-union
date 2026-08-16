<x-layout title="Choose visible posts">
    <h1 class="text-2xl font-bold text-gray-900">Kies je zichtbare posts</h1>
    <p class="mt-2 text-gray-700">
        Je profiel is privé: bezoekers en leden die je nog niet aanvaardde zien enkel de {{ $maximum }} posts die je hier kiest.
    </p>

    <form method="POST" action="{{ route('posts.showcase.update') }}" class="mt-6 space-y-3"
        data-showcase-form data-maximum="{{ $maximum }}">
        @csrf
        @method('PATCH')

        @forelse ($posts as $post)
            <label class="flex items-start gap-3 rounded-lg border border-gray-300 bg-white p-4">
                <input type="checkbox" name="posts[]" value="{{ $post->id }}" @checked($post->is_showcased)
                    data-showcase-checkbox class="mt-1">
                <span>
                    <span class="block text-sm text-gray-600">{{ $post->created_at->format('d/m/Y H:i') }}</span>
                    <span class="block text-gray-900">{{ Str::limit($post->content, 160) ?: 'Post zonder tekst' }}</span>
                </span>
            </label>
        @empty
            <p class="text-gray-700">Je hebt nog geen posts.</p>
        @endforelse

        <x-input-error :messages="$errors->get('posts')" />

        <button type="submit" class="rounded bg-indigo-600 px-4 py-2 text-white font-semibold whitespace-nowrap"
            style="background-color:#4f46e5;color:#ffffff;">Bewaren</button>
    </form>

    <script>
        (function () {
            const form = document.querySelector('[data-showcase-form]');

            if (! form) {
                return;
            }

            const maximum = Number(form.dataset.maximum);
            const boxes = Array.from(form.querySelectorAll('[data-showcase-checkbox]'));

            const limit = () => {
                const checked = boxes.filter((box) => box.checked).length;
                boxes.forEach((box) => {
                    box.disabled = ! box.checked && checked >= maximum;
                });
            };

            boxes.forEach((box) => box.addEventListener('change', limit));
            limit();
        })();
    </script>
</x-layout>
