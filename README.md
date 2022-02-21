
# 🥋 CapesLocker

<p align="center">
    <a href="#"><img src="https://raw.githubusercontent.com/Verre2OuiSki/CapesLocker/main/meta/plugin.png" height="auto" width="80%"></img></a>
    <br>
    <h4 align="center">
      A configurable <i>PocketMine-MP</i> plugin allows you to add capes to your server !
    </h4>
    <br>
    <table align="center">
        <thead align="center">
            <tr>
                <td width="33%">
                    <a href="#"><img width="100%" src="https://raw.githubusercontent.com/Verre2OuiSki/CapesLocker/main/meta/cape_list.png"></img></a>
                </td>
                <td width="33%">
                    <a href="#"><img width="100%" src="https://raw.githubusercontent.com/Verre2OuiSki/CapesLocker/main/meta/cape_unlocked_options.png"></img></a>
                </td>
                <td width="33%">
                    <a href="#"><img width="100%" src="https://raw.githubusercontent.com/Verre2OuiSki/CapesLocker/main/meta/cape_locked_options.png"></img></a>
                </td>
            </tr>
        </thead>
        <tbody align="center">
            <tr>
                <td>
                    <u><h4>A capes locker menu !</h4></u>
                    With this menu, see all capes you can have and chose one of them !
                </td>
                <td>
                    <u><h4>A capes locker menu !</h4></u>
                    This is the menu when you select a cape you have unlocked.<br>You can equip it or go back to capes menu
                </td>
                <td>
                    <u><h4>A capes locker menu !</h4></u>
                    This is the menu when you select a locked of your locker.<br>You can't equip it <i>(because you don't unlocked it)</i> but you can go back to capes menu
                </td>
            </tr>
        </tbody>
    </table>
    <table align="center"  width="100%">
        <thead align="center" width="100%">
            <tr>
                <td width="30%">
                    <a href="#"><img width="100%" src="https://raw.githubusercontent.com/Verre2OuiSki/CapesLocker/main/meta/manage_capes_list.png"></img></a>
                </td>
                <td width="30%">
                    <a href="#"><img width="40%" src="https://raw.githubusercontent.com/Verre2OuiSki/CapesLocker/main/meta/cape1.png"></img></a>
                    <a href="#"><img width="40%" src="https://raw.githubusercontent.com/Verre2OuiSki/CapesLocker/main/meta/cape2.png"></img></a>
                </td>
                <td width="30%">
                    <a href="#"><img width="100%" src="https://raw.githubusercontent.com/Verre2OuiSki/CapesLocker/main/meta/manage_capes_options.png"></img></a>
                </td>
            </tr>
        </thead>
        <tbody align="center">
            <tr>
                <td>
                    <u><h4>A capes locker menu !</h4></u>
                    With this menu, see all capes you can have and chose one of them !
                </td>
                <td>
                    Some default capes :D
                </td>
                <td>
                    <u><h4>A capes locker menu !</h4></u>
                    This is the menu when you select a cape you have unlocked.<br>You can equip it or go back to capes menu
                </td>
            </tr>
        </tbody>
    </table>
</p>


# 📜 Features

- Add your own cape
- An intuitive UI
- Permissions for capes
- Add capes to locker without permission
- An API for more possibilities (like capes shop)

For any problems, you can reach me at "Reach ME" tab !



# 💻 Commands

Command | Aliases | Permission | Default | Description
--- | --- | --- | --- | ---
`/capes` |  | capeslocker.command.capes | `true` | Open your capes locker menu !
`/mcapes <player> [cape id] [lock\|unlock]` | `mcapes` | capeslocker.command.managecapes | `op` | Manage capes of a player
`/playerscapescleaner` |  | capeslockers.command.playerscapescleaner | `op` | WARNING ! This command remove all undefined capes in 'capes.json' from capes lockers of players.


# 🤔 How to add your cape ?

Firstly, cape must be a **PNG** file of 64x32 pixels.
Capes file is the property of a vanilla minecraft bedrock cape.

Put the file in the plugin folder located in plugins_data

After that, go to the file `capes.json` and save the desired cape.
```json
"cape_identifier" : {
    "name": "My awesome cape",
    "description": "This cape is part of a tutorial for the CapesLocker plugin!",
    "cape": "file_name",
    "default": true
}
```

`name`          => Cape name show in menu<br>
`description`   => Cape description show in menu<br>
`cape`          => Name of the cape file (without extension .png)<br>
`default`       => Define if this cape is owned by all players<br><br><br>

---

<table>
    <tr>
        <th>
            Colors
        </th>
        <th>
            Meaning
        </th>
        <th rowspan="0" align="center">
            <a href="https://raw.githubusercontent.com/Verre2OuiSki/CapesLocker/main/meta/cape_template.png">
                <p>
                    This is a template, you do not have to use it.
                </p>
            </a>
        </th>
    </tr>
    <tr>
        <td>
            Red
        </td>
        <td>
            Front
        </td>
    </tr>
    <tr>
        <td>
            Yellow
        </td>
        <td>
            Back
        </td>
    </tr>
    <tr>
        <td>
            Cyan
        </td>
        <td>
            Left side
        </td>
    </tr>
    <tr>
        <td>
            Green
        </td>
        <td>
            Right side
        </td>
    </tr>
    <tr>
        <td>
            Blue
        </td>
        <td>
            Top side
        </td>
    </tr>
    <tr>
        <td>
            Pink
        </td>
        <td>
            Bottom side
        </td>
    </tr>
</table>
<a href="https://raw.githubusercontent.com/Verre2OuiSki/CapesLocker/main/meta/cape_template.png">
    <img width="50%" src="https://raw.githubusercontent.com/Verre2OuiSki/CapesLocker/main/meta/cape_template.png"></img>
</a>



# 🧐 How unlock capes ?

> <h4>Capes can be locked and unlocked by 3 ways :</h4>

__**Permissions :**__
Using permissions, you can lock or unlock capes.
Permissions are like this : `capeslocker.cape.cape_identifier`
"cape_identifier" is define in "capes.json"</br></br>

__**In-game Menu :**__
Players who have `capeslocker.command.managecape` permission or OP players, can lock or unlock cape of other players.</br></br>

__**Plugins :**__
CapesLocker has an API for developers. You can see it in detail in the "For developers" tab !



# 💾 Config

```yaml
---
#
#   __      __                ___   ____        _  _____ _    _
#   \ \    / /               |__ \ / __ \      (_)/ ____| |  (_)
#    \ \  / /__ _ __ _ __ ___   ) | |  | |_   _ _| (___ | | ___
#     \ \/ / _ \ '__| '__/ _ \ / /| |  | | | | | |\___ \| |/ / |
#      \  /  __/ |  | | |  __// /_| |__| | |_| | |____) |   <| |
#       \/ \___|_|  |_|  \___|____|\____/ \__,_|_|_____/|_|\_\_|
#

# Do you have a problem ?
# 
# Discord (en) : https://discord.gg/P8R4WhARrY
# Discord (fr) : https://discord.gg/DnmRbAxMbN



# Time in seconds
cape_cooldown: 10
# {cooldown}  Amout of time you must wait before set a new cape
cooldown_message: "§cYou must wait {cooldown} seconds between every cape changed"
# {cape}    The cape name
cape_equiped_message: "You equiped {cape}"
# {cape}    The cape name
locked_cape_message: "You must unlock {cape} before use it"

menu_title: "Capes list"
menu_body: "There is some capes you can unlock and use !"
...
```



# 💻 For developers

This plugin allow you to interact with locker.
Here is some function you can use in your code.

You can get the plugin with `CapesLocker::getInstance()`

If you want more detail, you can check the main file of this plugin

<details>
  <summary>❓ You have some question or issues, you can come on my for help !</summary>
  <br>
  <div align="center">
    <a href="https://discord.gg/P8R4WhARrY">
        <img src="https://img.shields.io/badge/Discord%20%28EN%29-%237289DA.svg?style=for-the-badge&logo=discord&logoColor=white"></img>
    </a>
    <a href="https://twitter.com/Verre2OuiSki">
        <img src="https://img.shields.io/badge/Verre2OuiSki-%231DA1F2.svg?style=for-the-badge&logo=Twitter&logoColor=white"></img>
    </a>
    <a href="https://discord.gg/DnmRbAxMbN">
        <img src="https://img.shields.io/badge/Discord%20%28FR%29-%237289DA.svg?style=for-the-badge&logo=discord&logoColor=white"></img>
    </a>
</div>
  
  
</details>

### Get capes
```php
/**
 * Return all capes
 * @return array
 */
```
`getCapes()`
### Get default capes
```php
/**
 * Return all default capes
 * @return array
 */
```
`getDefaultCapes()`


### Get cape
```php
/**
 * Return cape info
 * @param string $cape_id
 * @return NULL|array
 */
```
`getCapeById(string $cape_id)`


### Get player capes
```php
/**
 * Return unlocked player's capes (default capes isn't include)
 * @param Player $player Player to get his capes
 * @return array
 */
```
`getPlayerCapes($player)`

### Get player permitted capes
```php
/**
 * Return permitted player's capes (default capes isn't include)
 * @param Player $player Player to get his capes
 * @return array
 */
```
`getPlayerPermittedCapes($player)`

### Get all players capes
```php
/**
 * Get alls players capes (default capes and permitted capes isn't include)
 * @return array
 */
```
`getPlayersCapes()`

### Unlock a player cape
```php
/**
 * Unlock a cape for a specific player
 * @param Player $player Player to unlock a cape
 * @param string $cape_id The ID of the cape to unlock
 * @return void
 */
```
`unlockCape( $player, $cape_id )`


### Lock a player cape
```php
/**
 * lock a cape for a specific player
 * @param Player $player Player to lock a cape
 * @param string $cape_id The ID of the cape to lock
 * @return void
 */
```
`lockCape( $player, $cape_id)`


### Set player cape by ID
```php
/**
 * Unlock a cape for a specific player
 * @param Player $player Player to equip the cape with
 * @param string $cape_id The ID of the cape to be equipped
 * @return void
 */
```
`setPlayerCape( $player, $cape_id = null )`


### Test if player have a cape
```php
/**
 * Check if a player have a specific cape
 * @param Player $player Player to check their capes
 * @param string $cape_id The ID of the cape to check
 * @return bool
 */
```
`hasCape( $player, $cape_id )`



# 📫 Reach me

<div align="center">
    <a href="https://discord.gg/P8R4WhARrY">
        <a href="#"><img src="https://img.shields.io/badge/Discord%20%28EN%29-%237289DA.svg?style=for-the-badge&logo=discord&logoColor=white"></img></a>
    </a>
    <a href="https://twitter.com/Verre2OuiSki">
        <a href="#"><img src="https://img.shields.io/badge/Verre2OuiSki-%231DA1F2.svg?style=for-the-badge&logo=Twitter&logoColor=white"></img></a>
    </a>
    <a href="https://discord.gg/DnmRbAxMbN">
        <a href="#"><img src="https://img.shields.io/badge/Discord%20%28FR%29-%237289DA.svg?style=for-the-badge&logo=discord&logoColor=white"></img></a>
    </a>
</div>