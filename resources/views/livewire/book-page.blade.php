<div class="p-8">
    <div class="relative">
        <livewire:book-search :page="$page" :book="$book" :book_slug="$book_slug" />
    </div>

    <div class="px-40 pt-11">
        <h1 class="text-xl text-center mb-4 text-blue-600 font-bold">{{ $book_name }}</h1>
        {!! $page_content !!}
        <h2 class="text-center mb-2">Page: {{ $page }}</h2>
    </div>

</div>
