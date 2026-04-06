# Plugins
## Extension points
### What is it?
Extension points are fragments of application that can be used by plugins to serve content. To use extension point in Your plugin create `<extension_point_name>.php` file inside `include` directory of Your plugin's directory. For instance: /include/plugins/`ESITGamificationPlugin/include/settings_configuration.php` is a extension point for plugin's settings in configuration page (`/app/index.php?p=admin`).

### How does extension point is triggered?
Check out `include_plugins_for()` function in [core](../core.php) file.

### List of currently available extension points
| Extension point name | Destination | Description |
| --- | --- | --- |
| `core` | `/app/core.php` **(entire app)** | Modify core functions and global mechanisms |
| `settings_configuration` | `/app/index.php?p=admin` | Plugin's settings in configuration page |
| `dashboard_side_panel` | `/app/index.php?p=dashboard` | Side panel of dashboard |
| `users_settings_personalization` | `/app/index.php?p=settings` | Personal settings of appearance etc. |
| `diagnostics` | `/app/index.php?p=diagnostics` | Add new card to diagnostics page |
| `vertical_menu` | `/app/menus/vertical.php` | Create your own category in vertical menu |
| `vertical_menu_administration` | `/app/menus/vertical.php` | Add new option to `administration` category of vertical menu |
| `vertical_menu_problemsets` | `/app/menus/vertical.php` | Add new option to `problemsets` category of vertical menu |
| `horizontal_menu` | `/app/menus/horizontal.php` | Add new element to horizontal menu |

> [!note] 
> The list of available extension points will be expanded in future updates.

> [!important] 
> Be careful when using `core` extension point. It affects entire app.

