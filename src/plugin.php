<?php

namespace Opb\DevSymlinks;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;

class SymlinkPlugin implements PluginInterface, EventSubscriberInterface
{
    protected $composer;
    protected $io;

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    public function deactivate(Composer $composer, IOInterface $io)
    {
    }

    public function uninstall(Composer $composer, IOInterface $io)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            ScriptEvents::PRE_INSTALL_CMD => 'configureSymlinks',
            ScriptEvents::PRE_UPDATE_CMD => 'configureSymlinks',
        ];
    }

    public function configureSymlinks(Event $event)
    {
        $env = getenv('APP_ENV');
        $extra = $this->composer->getPackage()->getExtra();
        $devSymlinkEnvs = $extra['dev-symlink-envs'];
        $useSymlinks = in_array($env, $devSymlinkEnvs);

        $this->io->write(
            sprintf(
                "Dev Symlink plugin - current env %s %s found within [%s] so we %s change the 'symlink' option to 'true' in all repositories of 'type': 'path'",
                $env,
                $useDevSymlinks ? "IS" : "IS NOT",
                implode(", ", $devSymlinkEnvs),
                $useDevSymlinks ? "WILL" : "WILL NOT",
            )
        );

        if(!$useSymlinks){
            return;
        }

        // If we get here we are setting symlink option to true
        
        $repositories = $this->composer->getRepositoryManager()->getRepositories();
        
        foreach ($repositories as $repository) {
            if (method_exists($repository, 'getRepoConfig')) {
                $config = $repository->getRepoConfig();
                
                if (isset($config['type']) && $config['type'] === 'path') {
                    $config['options']['symlink'] = true;
                    $repository->setRepoConfig($config);
                    
                    $this->io->write(
                        sprintf(
                            "Dev Symlink plugin - repository <info>%s</info> symlink option set to <info>%s</info> as environment is <info>%s</info>",
                            $config['url'],
                            "'true'",
                            $env
                        )
                    );
                }
            }
        }
    }
}
