@props([
    'title' => null,
    'subtitle' => null,
    'headers' => [],
    'createRoute' => null,
    'createText' => 'Nuevo'
])

<div class="table-wrapper">

    <!-- HEADER -->
    @if($title || $createRoute)
        <div class="table-header">

            <div>
                @if($title)
                    <h2 class="title">{{ $title }}</h2>
                @endif

                @if($subtitle)
                    <p class="subtitle">{{ $subtitle }}</p>
                @endif
            </div>

            @if($createRoute)
                <a href="{{ $createRoute }}" class="btn-primary">
                    + {{ $createText }}
                </a>
            @endif

        </div>
    @endif

    <!-- TABLE -->
    <div class="table-card">

        <table class="table">

            <thead>
                <tr>
                    @foreach($headers as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                {{ $slot }}
            </tbody>

        </table>

    </div>

</div>

