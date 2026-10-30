# Installation example & config details
Download the repository:
```bash
git clone https://github.com/jstpp/esit.git && cd esit
```
You can customize the [core](/include/app/core.php). You should consider this when running it in production, otherwise, you can ignore it.

Start [installation script](/setup/install.sh):
```bash
source ./setup/install.sh
```

The service should be available on `localhost:80` unless you change it. The first registered user becomes an admin.

<br />
<br />

## Files in `/setup` directory
### Setup scripts
| File | Container | Description |
| --- | --- | --- |
| `install.sh` | `N/A` | script for initial instance setup & installing all required dependencies |
| `composer_setup.sh` | `php-fpm` | script for safe PHP composer installation |

### Configuration files
| File | Container | Description |
| --- | --- | --- |
| `db.sql` | `mysql` | mySQL database scheme |
| `php.ini` | `php-fpm` | additional PHP configuration file |
| `default.conf.example` | `web` | default nginx configuration file for our site |
| `nginx.conf.example` | `web` | default general nginx configuration file |
| `fastcgi-php.conf.example` | `web` | some PHP config variables |
| `definitions.rabbitmq.json.example` | `rabbitmq` | rabbitmq definitions for setup |
| `rabbitmq-enabled-plugins.example` | `rabbitmq` | plugins enabled for broker |
| `rabbitmq.config.example` | `rabbitmq` | default broker configuration |
| `.env.example` | `all containers` | default environment variables including credentials |

### Miscellaneous
| File | Container | Description |
| --- | --- | --- |
| `seccomp.json` | `worker` | seccomp profile for worker |
| `README.md` | `N/A` | this file |


### Dockerfiles
| File | Container |
| --- | --- |
| `mysql-dockerfile` | `mysql` |
| `php-dockerfile` | `php-fpm` |
| `web-dockerfile` | `web` |
| `worker-dockerfile` | `worker` |

<br />
<br />

## Config variables
You may find useful to modify [core](/include/app/core.php) and [.env file](/setup/.env.example). Below, you'll find a list of PHP variables along with descriptions to guide you through the process.

### Database (mariadb) config

| Variable | Type | Description |
| --- | --- | --- |
| `$db_host`| string | Host IP address |
| `$db_username` | string | Your username |
| `$db_password` | string | Your password |
| `$db_database` | string | Name of the database |
| `$db_charset` | string | Charset of the database (`utf8` is default) |

### Broker ([rabbitMQ](https://www.rabbitmq.com/)) config
| Variable | Type | Description |
| --- | --- | --- |
| `$rabbit_mq_host`| string | Host IP address |
| `$rabbit_mq_port` | string | Port for broker |
| `$rabbit_mq_user` | string | Username |
| `$rabbit_mq_password` | string | Password |

### Worker config
| Variable | Type | Description |
| --- | --- | --- |
| `$worker_network_private_key`| string | Key used for API encryption |

### Mailing config
| Variable | Type | Description |
| --- | --- | --- |
| `$mail_name` | string | Name of the sender |
| `$mail_smtp_debug` | integer | SMTP debugging |
| `$mail_smtp_auth` | boolean | Enable SMTP auth |