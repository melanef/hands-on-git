<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\AssetMapper\AssetMapper;
use Symfony\Component\AssetMapper\AssetMapperCompiler;
use Symfony\Component\AssetMapper\AssetMapperDevServerSubscriber;
use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\AssetMapper\AssetMapperRepository;
use Symfony\Component\AssetMapper\Command\AssetMapperCompileCommand;
use Symfony\Component\AssetMapper\Command\DebugAssetMapperCommand;
use Symfony\Component\AssetMapper\Command\ImportMapExportCommand;
use Symfony\Component\AssetMapper\Command\ImportMapRemoveCommand;
use Symfony\Component\AssetMapper\Command\ImportMapRequireCommand;
use Symfony\Component\AssetMapper\Command\ImportMapUpdateCommand;
use Symfony\Component\AssetMapper\Compiler\CssAssetUrlCompiler;
use Symfony\Component\AssetMapper\Compiler\JavaScriptImportPathCompiler;
use Symfony\Component\AssetMapper\Compiler\SourceMappingUrlsCompiler;
use Symfony\Component\AssetMapper\ImportMap\ImportMapManager;
use Symfony\Component\AssetMapper\ImportMap\ImportMapRenderer;
use Symfony\Component\AssetMapper\MapperAwareAssetPackage;
use Symfony\Component\HttpKernel\Event\RequestEvent;

return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('asset_mapper', AssetMapper::class)
            ->args([
                service('asset_mapper.repository'),
                service('asset_mapper_compiler'),
                param('kernel.project_dir'),
                abstract_arg('asset public prefix'),
                abstract_arg('public directory name'),
                abstract_arg('extensions map'),
            ])
        ->alias(AssetMapperInterface::class, 'asset_mapper')
        ->set('asset_mapper.repository', AssetMapperRepository::class)
            ->args([
                abstract_arg('array of asset mapper paths'),
                param('kernel.project_dir'),
            ])
        ->set('asset_mapper.asset_package', MapperAwareAssetPackage::class)
            ->decorate('assets._default_package')
            ->args([
                service('.inner'),
                service('asset_mapper'),
            ])

        ->set('asset_mapper.dev_server_subscriber', AssetMapperDevServerSubscriber::class)
            ->args([
                service('asset_mapper'),
            ])
            ->tag('kernel.event_subscriber', ['event' => RequestEvent::class])

        ->set('asset_mapper.command.compile', AssetMapperCompileCommand::class)
            ->args([
                service('asset_mapper'),
                service('asset_mapper.importmap.manager'),
                service('filesystem'),
                param('kernel.project_dir'),
                abstract_arg('public directory name'),
                param('kernel.debug'),
            ])
            ->tag('console.command')

            ->set('asset_mapper.command.debug', DebugAssetMapperCommand::class)
                ->args([
                    service('asset_mapper'),
                    service('asset_mapper.repository'),
                    param('kernel.project_dir'),
                ])
                ->tag('console.command')

        ->set('asset_mapper_compiler', AssetMapperCompiler::class)
            ->args([
                tagged_iterator('asset_mapper.compiler'),
            ])

        ->set('asset_mapper.compiler.css_asset_url_compiler', CssAssetUrlCompiler::class)
            ->args([
                abstract_arg('strict mode'),
            ])
            ->tag('asset_mapper.compiler')

        ->set('asset_mapper.compiler.source_mapping_urls_compiler', SourceMappingUrlsCompiler::class)
            ->tag('asset_mapper.compiler')

        ->set('asset_mapper.compiler.javascript_import_path_compiler', JavaScriptImportPathCompiler::class)
            ->args([
                abstract_arg('strict mode'),
            ])
            ->tag('asset_mapper.compiler')

        ->set('asset_mapper.importmap.manager', ImportMapManager::class)
            ->args([
                service('asset_mapper'),
                abstract_arg('importmap.php path'),
                abstract_arg('vendor directory'),
                abstract_arg('provider'),
            ])
        ->alias(ImportMapManager::class, 'asset_mapper.importmap.manager')

        ->set('asset_mapper.importmap.renderer', ImportMapRenderer::class)
            ->args([
                service('asset_mapper.importmap.manager'),
                param('kernel.charset'),
                abstract_arg('polyfill URL'),
                abstract_arg('script HTML attributes'),
            ])

        ->set('asset_mapper.importmap.command.require', ImportMapRequireCommand::class)
            ->args([
                service('asset_mapper.importmap.manager'),
                service('asset_mapper'),
            ])
            ->tag('console.command')

        ->set('asset_mapper.importmap.command.remove', ImportMapRemoveCommand::class)
            ->args([service('asset_mapper.importmap.manager')])
            ->tag('console.command')

        ->set('asset_mapper.importmap.command.update', ImportMapUpdateCommand::class)
            ->args([service('asset_mapper.importmap.manager')])
            ->tag('console.command')

        ->set('asset_mapper.importmap.command.export', ImportMapExportCommand::class)
            ->args([service('asset_mapper.importmap.manager')])
            ->tag('console.command')
    ;
};
