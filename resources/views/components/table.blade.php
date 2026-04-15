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

<style>
.table-wrapper {
    display:flex;
    flex-direction:column;
    gap:14px;
}

/* HEADER */
.table-header {
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.title {
    font-size:22px;
    font-weight:700;
    color:#0f172a;
}

.subtitle {
    font-size:13px;
    color:#64748b;
}

/* CARD */
.table-card {
    background:white;
    border-radius:14px;
    box-shadow:0 10px 25px rgba(0,0,0,0.06);
    overflow:hidden;
}

/* TABLE */
.table {
    width:100%;
    border-collapse:collapse;
    font-size:14px;
}

.table thead {
    background:#0f172a;
    color:white;
}

.table th,
.table td {
    padding:14px;
    text-align:left;
}

.table tbody tr {
    border-bottom:1px solid #e2e8f0;
    transition:0.2s;
}

.table tbody tr:hover {
    background:#f8fafc;
}

/* BUTTON */
.btn-primary {
    background:linear-gradient(90deg,#2563eb,#3b82f6);
    color:white;
    padding:10px 14px;
    border-radius:10px;
    text-decoration:none;
    font-weight:500;
    box-shadow:0 6px 16px rgba(37,99,235,0.25);
    transition:0.2s;
}

.btn-primary:hover {
    transform:translateY(-1px);
}
</style>