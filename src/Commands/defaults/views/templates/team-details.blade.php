@extends('layouts.index')

@section('meta-description')
    <meta name="description" content="{{ $page->excerpt }}"/>
@stop

@section('template')
    @php(app(\RefinedDigital\CMS\Modules\Core\Aggregates\AssetAggregate::class)->addStyle('team.css'))

    <section class="page__block page__team-details">
        <div class="holder holder--small">
            <article class="team-details">
                @if ($page->image)
                    <figure class="team-details__image fade-in-up">
                        {!! image()->load($page->image)->fit()->width(600)->height(600)->pictureHtml() !!}
                    </figure>
                @endif

                <header class="team-details__header">
                    <h1 class="heading fade-in-up">{{ $page->name }}</h1>
                    @if ($page->job_title_1)
                        <h2 class="heading--title fade-in-up">{{ $page->job_title_1 }}</h2>
                    @endif
                    @if ($page->job_title_2)
                        <h3 class="heading--title fade-in-up">{{ $page->job_title_2 }}</h3>
                    @endif
                </header>

                @if ($page->content)
                    <div class="team-details__content cont fade-in-up">
                        {!! $page->content !!}
                    </div>
                @endif

                @if ($page->email || $page->phone || $page->mobile)
                    <footer class="team-details__footer fade-in-up">
                        @if ($page->email)
                            {!! help()->encodeEmail($page->email, 'Email', 'button') !!}
                        @endif
                        @if ($page->phone || $page->mobile)
                            {!! help()->encodePhone($page->mobile ?: $page->phone, 'Call', 'button') !!}
                        @endif
                    </footer>
                @endif
            </article>
        </div>
    </section>
@stop
