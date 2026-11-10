<?php

namespace App\Providers;

use Native\Desktop\Facades\Window;
use Native\Desktop\Contracts\ProvidesPhpIni;
use Native\Desktop\Facades\Menu;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        Menu::create(
            Menu::app(), // macOS only

            // Menu aplikasi kamu
            Menu::make(
                Menu::link(url('/i'), 'Home'),
                Menu::link(url('/login'), 'App Dashboard'),
                Menu::link(url('/portal'), 'Portal'),
            )->label('Menu'),

            Menu::make(
                Menu::link(url('/setting'), 'Setting'),
            )->label('Setting'),

            // Native window controls
            Menu::window('Window'),
        );


        Window::open()
            ->position(100, 100)
            ->minWidth(1280)
            ->minHeight(960)
            ->width(1280)
            ->height(960)
            ->showDevTools(false)

        ;
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
        ];
    }
}
