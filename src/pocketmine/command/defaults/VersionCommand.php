<?php

/*
 *  _   _    ____     ___
 * | \ | |  / ___|  / ___|
 * |  \| | | |  _  | |
 * | |\  | | |_| | | |___
 * |_| \_|  \____|  \____|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author XinYueNeko
 * @link https://github.com/NewmoomCat
 */

namespace pocketmine\command\defaults;

use pocketmine\command\CommandSender;
use pocketmine\event\TranslationContainer;
use pocketmine\network\protocol\Info as ProtocolInfo;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class VersionCommand extends VanillaCommand {

    /**
     * VersionCommand constructor.
     *
     * @param string $name
     */
    public function __construct($name){
        parent::__construct(
            $name,
            "%pocketmine.command.version.description",
            "%pocketmine.command.version.usage",
            ["ver", "about"]
        );
        $this->setPermission("pocketmine.command.version");
    }

    /**
     * @param CommandSender $sender
     * @param string        $currentAlias
     * @param array         $args
     *
     * @return bool
     */
    public function execute(CommandSender $sender, $currentAlias, array $args){
        if(!$this->testPermission($sender)){
            return \true;
        }

        if(\count($args) === 0){
            $version = implode(",",\pocketmine\MINECRAFT_VERSION);
            $sender->sendMessage("--------------- Server information --------------");
            $sender->sendMessage("Neko's Genisys (" . $sender->getServer()->getName() . ") core server (" . $sender->getServer()->getShortGitCommit() . ")");
            $sender->sendMessage("Made by XinYueNeko");
            $sender->sendMessage("Codename \"". $sender->getServer()->getCodename() . "\" & PHP Version \"" . PHP_VERSION . "\"");
            $sender->sendMessage("MCPE Version: " . $version);
        }else{
            $pluginName = \implode(" ", $args);
            $exactPlugin = $sender->getServer()->getPluginManager()->getPlugin($pluginName);

            if($exactPlugin instanceof Plugin){
                $this->describeToSender($exactPlugin, $sender);

                return \true;
            }

            $found = \false;
            $pluginName = \strtolower($pluginName);
            foreach($sender->getServer()->getPluginManager()->getPlugins() as $plugin){
                if(\stripos($plugin->getName(), $pluginName) !== \false){
                    $this->describeToSender($plugin, $sender);
                    $found = \true;
                }
            }

            if(!$found){
                $sender->sendMessage(new TranslationContainer("pocketmine.command.version.noSuchPlugin"));
            }
        }

        return \true;
    }

    /**
     * @param Plugin        $plugin
     * @param CommandSender $sender
     */
    private function describeToSender(Plugin $plugin, CommandSender $sender){
        $desc = $plugin->getDescription();
        $sender->sendMessage(TextFormat::DARK_GREEN . $desc->getName() . TextFormat::WHITE . " version " . TextFormat::DARK_GREEN . $desc->getVersion());

        if($desc->getDescription() != \null){
            $sender->sendMessage($desc->getDescription());
        }

        if($desc->getWebsite() != \null){
            $sender->sendMessage("Website: " . $desc->getWebsite());
        }

        if(\count($authors = $desc->getAuthors()) > 0){
            if(\count($authors) === 1){
                $sender->sendMessage("Author: " . \implode(", ", $authors));
            }else{
                $sender->sendMessage("Authors: " . \implode(", ", $authors));
            }
        }
    }
}