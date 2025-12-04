    <div class="row">
        @foreach ($menuList as $i)
            <div class="col-6 col-md-2">
                <a href="{{ url('') }}{{ $i->route ?: str_replace(' ', '_', $i->group_menu) . '/' . str_replace(' ', '_', $i->name) }}"
                    class="btn text-center menu-list " style="width: 100%" alt=" {{ $i->name }}">
                    <h5 style=" margin-top:17px"><i class="{{ $i->icon ?: 'fa fa-user' }}"
                            style="font-size: 40px;color: #{{ rand(100000, 999999) }};"></i>
                    </h5>
                    <span style="font-size:10px; color: #{{ rand(100000, 999999) }};min-width:100%">
                        <b> {{ strlen($i->name) > 24 ? substr($i->name, 0, 24) . '...' : $i->name }}</b>
                    </span>
                </a>
            </div>
        @endforeach
    </div>
