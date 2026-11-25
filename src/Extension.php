<?php

declare(strict_types=1);

namespace Kopling\Contact;

use Kopling\Contact\Command\PruneCommand;
use Kopling\Core\Extend\Icon;
use Kopling\Core\Extend\Permission;
use Kopling\Core\Extend\Ux;
use Kopling\Core\Extend\Ux\ProvidesUxEntries;
use Kopling\Core\Extension\AbstractExtension;
use Kopling\Core\Extension\Contract\ChangesUx;
use Kopling\Core\Extension\Contract\ExtendsPortals;
use Kopling\Core\Extension\Contract\HasAdminSettings;
use Kopling\Core\Extension\Contract\HasCommands;
use Kopling\Core\Extension\Contract\HasIcons;
use Kopling\Core\Extension\Contract\HasPermissions;
use Kopling\Core\Extension\Contract\HasPortals;
use Kopling\Core\Portal\Portal;
use Kopling\Core\Portal\PortalExtension;
use Kopling\Core\Ux\Community\UserMenu;
use Kopling\Core\Ux\Form\Field;
use Kopling\Core\Ux\Form\Input;
use Kopling\Core\Ux\Form\TextArea;
use Kopling\Core\Ux\Portal\Navigation\Item;

class Extension extends AbstractExtension implements ChangesUx, ExtendsPortals, HasAdminSettings, HasCommands, HasIcons, HasPermissions, HasPortals
{
    public const ID = 'kopling-contact';

    public static function name(): string
    {
        return 'Contact';
    }

    public static function description(): string
    {
        return 'A contact form: visitors send a message without needing a mail client, admins read and answer it from the admin panel.';
    }

    /**
     * @return array<Portal>
     */
    public function portals(): array
    {
        return [
            new Portal(
                id: 'contact',
                label: 'Contact',
                path: 'contact',
                layout: 'kopling-contact::layouts.contact',
            ),
        ];
    }

    /**
     * @return array<PortalExtension>
     */
    public function extendsPortals(): array
    {
        return [
            new PortalExtension('kopling-contact::contact')
                ->routes(__DIR__.'/../routes/contact.php'),
            new PortalExtension('kopling-admin::admin')
                ->routes(__DIR__.'/../routes/admin.php'),
        ];
    }

    /**
     * @return array<Permission>
     */
    public function permissions(): array
    {
        return [
            new Permission(
                id: 'read-messages',
                label: __('kopling-contact::permissions.read-messages.label'),
                description: __('kopling-contact::permissions.read-messages.description'),
            ),
        ];
    }

    /**
     * @return array<Field>
     */
    public function adminSettings(): array
    {
        return [
            new Field(
                id: Config::SUBJECTS,
                label: 'Subjects',
                component: TextArea::class,
                default: Config::DEFAULT_SUBJECTS,
                description: 'One per line as "key = label", e.g. "question = A question". Link to a subject with /contact?subject=<key>.',
            ),
            new Field(
                id: Config::NOTIFY_ADDRESS,
                label: 'Notify address',
                component: Input::class,
                default: '',
                description: 'Gets a short notice with a link for every new message, without its content. Leave empty for no mail.',
                data: ['type' => 'email'],
            ),
            new Field(
                id: Config::RETENTION_DAYS,
                label: 'Keep messages for (days)',
                component: Input::class,
                default: (string) Config::DEFAULT_RETENTION_DAYS,
                description: 'kopling:contact:prune removes older messages. Schedule it daily.',
                data: ['type' => 'number'],
            ),
            new Field(
                id: Config::COOLDOWN_MINUTES,
                label: 'Wait after sending (minutes)',
                component: Input::class,
                default: (string) Config::DEFAULT_COOLDOWN_MINUTES,
                description: 'How long the form stays closed in that browser session after a message.',
                data: ['type' => 'number'],
            ),
            new Field(
                id: Config::PER_HOUR,
                label: 'Messages per hour',
                component: Input::class,
                default: (string) Config::DEFAULT_PER_HOUR,
                description: 'Per IP address.',
                data: ['type' => 'number'],
            ),
            new Field(
                id: Config::MAX_LINKS,
                label: 'Links allowed in a message',
                component: Input::class,
                default: (string) Config::DEFAULT_MAX_LINKS,
                description: 'A message with more links is refused.',
                data: ['type' => 'number'],
            ),
        ];
    }

    public function ux(): ProvidesUxEntries
    {
        return Ux::make()
            ->add(Item::class, [
                'label' => __('kopling-contact::messages.admin.nav'),
                'route' => 'kopling-admin::admin/contact',
                'icon' => 'kopling-contact::contact',
            ])
            ->in('kopling-admin::admin.navigation')
            ->as('messages')
            ->when('read-messages')
            ->add(UserMenu::class)
            ->in('kopling-contact::contact.topbar')
            ->as('user-menu');
    }

    /**
     * @return array<class-string>
     */
    public function commands(): array
    {
        return [PruneCommand::class];
    }

    /**
     * @return array<Icon>
     */
    public function icons(): array
    {
        return [
            new Icon(id: 'contact', label: 'Contact', default: 'fas-envelope'),
        ];
    }
}
