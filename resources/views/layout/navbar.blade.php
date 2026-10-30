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
    <div class="app-logo border-b">
        <a class="logo d-inline-block" href="/"
            style="text-align: center;align-content: center;align-items: center; width: 100%">
            <img alt="#" class="mx-auto" style="align-self: center;" src="{{ $logo }}">
        </a>

        <span class="bg-light-primary toggle-semi-nav">
            <i class="ti ti-chevrons-right f-s-20"></i>
        </span>
    </div>
    <div class="app-nav mt-4 " id="app-simple-bar">
        <ul class="main-nav p-0 mt-2">
            @foreach ($menu as $b)
                <?php
                $hasChildren = count($b['child']) >= 1;
                $childActive = $hasChildren && collect($b['child'])->contains(fn($c) => $c['route'] == $currentRouteName);
                ?>
                @if ($hasChildren)
                    <li class="nav-tile">
                        <a aria-expanded="{{ $childActive ? 'true' : 'false' }}"
                            class="nav-tile-link {{ $childActive ? 'active' : '' }}" data-bs-toggle="collapse"
                            href="#group-{{ $b['group_id'] }}">
                            <i class="{{ $b['icon'] }} nav-tile-icon"></i>
                            <span class="nav-tile-label">{{ $b['name'] }}</span>
                            <i class="ti ti-chevron-down nav-caret"></i>
                        </a>
                        <ul class="collapse {{ $childActive ? 'show' : '' }}" id="group-{{ $b['group_id'] }}">
                            @foreach ($b['child'] as $c)
                                <li><a href="{{ $c['route'] }}"
                                        class="{{ $c['route'] == $currentRouteName ? 'active' : '' }}">{{ $c['name'] }}</a>
                                </li>
                            @endForeach
                        </ul>
                    </li>
                @else
                    <li class="no-sub nav-tile">
                        <a class="nav-tile-link {{ $b['route'] == $currentRouteName ? 'active' : '' }}"
                            href="{{ $b['route'] }}">
                            <i class="{{ $b['icon'] }} nav-tile-icon"></i>
                            <span class="nav-tile-label">{{ $b['name'] }}</span>
                        </a>
                    </li>
                @endif
            @endForeach
        </ul>
        <!-- Sidebar Menu End -->
    </div>
</nav>
