<x-layout title="FAQ page">
    <h1>Frequently Asked Questions</h1>
    @foreach ($categories as $category)
        <div class="mt-6">
            <h2 class="text-xl font-semibold">{{ $category->name }}</h2>
            <ul class="list-disc list-inside mt-2 space-y-2">
                @foreach ($category->faqs as $faq)
                    <li>
                        <strong>{{ $faq->question }}</strong>
                        <p class="text-gray-600 ml-4">{{ $faq->answer }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    @endforeach
</x-layout>