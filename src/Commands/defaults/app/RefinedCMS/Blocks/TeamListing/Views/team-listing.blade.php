@php
    $classes = array_merge($classes, [
      'page__block--bg-'.((isset($content->background_colour) && $content->background_colour) ? $content->background_colour : 'white'),
    ]);

    $data = team()->getAllForFront();
@endphp

<section class="{{ implode(' ', $classes) }}" id="page-block--{{ $page->id }}-{{ $index }}">
    <div class="holder">
        @include('content-templates::includes.heading')
        @include('content-templates::includes.content')

        @if ($data->count())
            <div class="team grid">
                @foreach ($data as $item)
                    <article class="team__item fade-in-up">
                        <a href="/{{ $item->page_url }}">
                            @if ($item->image)
                                <figure class="team__image">
                                    {!! image()->load($item->image)->fit()->width(400)->height(400)->pictureHtml() !!}
                                </figure>
                            @endif
                            <h3 class="team__name">{{ $item->name }}</h3>
                            @if ($item->job_title_1)
                                <h4 class="team__title">{{ $item->job_title_1 }}</h4>
                            @endif
                        </a>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
