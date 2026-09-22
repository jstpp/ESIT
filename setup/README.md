# Installation example & config details
Create user `esit-user` and add it to docker group:
```bash
adduser esit-user && usermod -aG docker esit-user
```

...and download repository:
```bash
git clone https://github.com/jstpp/esit.git && cd esit
```
then:
```bash
docker compose up
```

The installation may take some time - be prepared for it.

After the end of the installation, service should be available on `localhost:80` unless You don't change it in proper files.

## Files in `/setup` directory
### Configuration files
| File | Container | Description |
| --- | --- | --- |
| `db.sql` | `mysql` | mySQL database scheme |
| `default.conf.example` | `web` | default nginx configuration file for our site |
| `nginx.conf.example` | `web` | default general nginx configuration file |
| `fastcgi-php.conf.example` | `web` | some PHP config variables |
| `definitions.rabbitmq.json.example` | `rabbitmq` | rabbitmq definitions for setup |
| `rabbitmq-enabled-plugins.example` | `rabbitmq` | plugins enabled for broker |
| `rabbitmq.config.example` | `rabbitmq` | default broker configuration |

### Dockerfiles
| File | Container |
| --- | --- |
| `mysql-dockerfile` | `mysql` |
| `php-dockerfile` | `php-fpm` |
| `web-dockerfile` | `web` |
| `worker-dockerfile` | `worker` |

## Config variables

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