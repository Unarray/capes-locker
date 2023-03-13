<?php

declare(strict_types=1);

namespace unarray\capeslocker;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use unarray\capeslocker\managers\CapesManager;

class CapesLocker extends PluginBase {

  use SingletonTrait;

  private CapesManager $capesManager;

  public function onLoad(): void {
    self::setInstance($this);
  }
}