<x-layout>
    <form action="">
        <div class="col-span-full">
            <label for="about" class="block text-lg font-medium text-black">New idea</label>
            <div class="mt-2">
                <textarea id="about" name="about" rows="3"
                    class="block w-full bg-white/5 px-3 py-1.5 text-base text-black outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6"></textarea>
            </div>
            <p class="mt-3 text-sm/6 text-gray-400">Write a few sentences about yourself.</p>
        </div>
        <div class="mt-6 flex items-center justify-end gap-x-6">
            <button type="submit"
                class="bg-indigo-500 px-3 py-2 text-sm font-semibold text-black focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">Save</button>
        </div>
    </form>

    @if (count($ideas) > 0)
        <div class="mt-6">
            <h2 class="text-lg font-medium text-black">Your Ideas</h2>
            <ul class="mt-4 space-y-4">
                @foreach ($ideas as $idea)
                    <li class="text-black">{{ $idea->description }}</li>
                @endforeach
            </ul>
        </div>
    @else
        <p class="text-black">No ideas yet.</p>
    @endif
</x-layout>