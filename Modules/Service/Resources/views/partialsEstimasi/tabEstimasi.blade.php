<ul class="nav nav-pills">
    <li class="nav-item">
        <a class="nav-link {{ request()->is('service/penerimaanServis') ? 'active' : '' }}" aria-current="page"
            href="{{ url('service/penerimaanServis') }}">Servis</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->is('service/estimasiServis') ? 'active' : '' }}" href="{{ url('service/estimasiServis') }}">Estimasi Servis</a>
    </li>
</ul>
