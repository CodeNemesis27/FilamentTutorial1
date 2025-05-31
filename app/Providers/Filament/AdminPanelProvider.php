<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use CharrafiMed\GlobalSearchModal\GlobalSearchModalPlugin;
use Filament\Support\Enums\MaxWidth;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->brandName('CodeNemesis')
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => '#000000',
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Shop')
                    ->collapsible(false),
            ])
            ->plugins([
                GlobalSearchModalPlugin::make()
                    ->slideOver()
                    ->maxWidth(MaxWidth::Small)
                    ->placeholder('Type to search...')
                    ->highlightQueryStyles([
                        'background-color' => 'yellow',
                        'font-weight' => 'bold',
                    ])
            ])
            ->navigationItems([
                NavigationItem::make('Google')
                    ->url('https://google.com', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-link')
                    ->group('External')
                    ->sort(2),

                NavigationItem::make('Youtube')
                    ->url('https://youtube.com', shouldOpenInNewTab: true)
                    ->icon('heroicon-o-link')
                    ->group('External')
                    ->sort(2)
            ])
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
