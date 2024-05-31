<x-layout >

    <x-breadcrumb breadcrumbsName="home" />

    @if (session('status'))
        <x-flash-messages message="{{ session('status')['message'] }}" class="{{ session('status')['type'] }}" ></x-flash-messages>
    @endif

    <x-post-filters :postAuthors="$postAuthors" />

    <x-posts-table />

</x-layout>

<x-modal />
