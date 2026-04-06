# Plugins
## Extension points
### What is it?
Extension points are fragments of application that can be used by plugins to serve content. To use extension point in Your plugin create `<extension_point_name>.php` file inside `include` directory of Your plugin's directory. For instance: /include/plugins/`ESITGamificationPlugin/include/settings_configuration.php` is a extension point for plugin's settings in configuration page (`/app/index.php?p=admin`).

### How does extension point is triggered?
Check out `include_plugins_for()` function in [core](../core.php) file.

### List of currently available extension points
| Extension point name | Destination | Description |
| --- | --- | --- |
| `settings_configuration` | `/app/index.php?p=admin` | Plugin's settings in configuration page |
| `dashboard_side_panel` | `/app/index.php?p=dashboard` | Side panel of dashboard |
| `users_settings_personalization` | `/app/index.php?p=settings` | Personal settings of appearance etc. |

> [!note] 
> The list of available extension points will be expanded in future updates.

