# Installation example
Create user `esit-user` and add it to docker group:
```bash
adduser esit-user && usermod -aG docker esit-user
```

...and download repository:
```bash
git clone https://github.com/jstpp/esit.git && cd esit
```
> [!tip]
>
> If You face permission issues, chown all files to Your `esit-user` for:
> ```bash
> sudo chown 1001:1001 -R /www/esit_testing/*
> ```
> replace `/www/esit_testing` with path to Your folder.

Check your uid and gid:
```bash
id -u && id -g
```

<small>Output:</small>
```bash
1001
1001
```
Now, start docker compose with predefined user-id and group-id:
```bash
UID="1001" GID="1001" docker compose up
```
The installation may take some time - be prepared for it.

After the end of the installation, service should be available on `localhost:80` unless You don't change it in proper files.

# Files in `/setup` directory
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