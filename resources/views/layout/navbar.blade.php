<?php
use App\Models\Module;
use App\Models\Setting;
use Illuminate\Support\Facades\Request;

$AccessList = explode(',', auth()->user()->access);

$logo = Setting::where('parameter', 'company_logo')->first()->value ?: url('/assets/logo.png');
$app_name = Setting::where('parameter', 'app_name')->first()->value ?: 'AppName';
$menu = [];
$menuList = Module::where('isheader', 1)->where('isactive', true)->orderBy('list_no', 'asc')->get();
foreach ($menuList as $i) {
    $submenu = Module::where('isheader', 0)->where('group_id', $i->group_id)->whereIn('id', $AccessList)->whereNot('id', $i->id)->where('isactive', true)->orderBy('list_no', 'asc')->get();

    if (count($submenu) > 0 || in_array($i->id, $AccessList)) {
        $menu[] = [
            'group_id' => $i->group_id,
            'name' => $i->name,
            'icon' => $i->icon,
            'route' => $i->route,
            'child' => $submenu,
        ];
    }
}
$currentRouteName = Request::getPathInfo();
?>

<nav>
    <div class="app-logo">
        <a class="logo d-inline-block" href="/">
            <img alt="#" src="{{ $logo }}">
        </a>

        <span class="bg-light-primary toggle-semi-nav">
            <i class="ti ti-chevrons-right f-s-20"></i>
        </span>
    </div>
    <div class="app-nav" id="app-simple-bar">
        <ul class="main-nav p-0 mt-2">
            @foreach ($menu as $b)
                <?php if(count($b['child']) >= 1){?>
                <li>
                    <a aria-expanded="false" class="" data-bs-toggle="collapse"
                        href="#group-{{ $b['group_id'] }}">
                        <i class="{{ $b['icon'] }}"></i>
                        {{ $b['name'] }}
                        {{-- <span class="badge text-primary-dark bg-primary-300  badge-notification ms-2">4</span> --}}
                    </a>
                    <ul class="collapse" id="group-{{ $b['group_id'] }}">
                        @foreach ($b['child'] as $c)
                            <li><a href="{{ $c['route'] }}"
                                    class="{{ $c['route'] == $currentRouteName && 'active' }}">{{ $c['name'] }}</a>
                            </li>
                        @endForeach
                    </ul>
                </li>
                <?php }else{?>
                <li class="no-sub">
                    <a class="{{ $b['route'] == $currentRouteName && 'active' }}" href="{{ $b['route'] }}">
                        <i class="{{ $b['icon'] }}" style="font-size: 23px">
                        </i> {{ $b['name'] }}
                    </a>
                </li>
                <?php }?>
            @endForeach
        </ul>
        <!-- Sidebar Menu End -->
    </div>
</nav>
