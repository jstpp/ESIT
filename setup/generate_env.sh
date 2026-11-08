
#!/usr/bin/env bash

set -euo pipefail

# Generate cryptographically secure random passwords
MYSQL_ROOT_PASSWORD=$(openssl rand -hex 32)
MYSQL_PASSWORD=$(openssl rand -hex 32)
RABBITMQ_DEFAULT_PASS=$(openssl rand -hex 32)
NETWORK_PRIVATE_KEY=$(openssl rand -hex 16)
RABBITMQ_DEFAULT_USER="esit_user"


SALT=$(openssl rand 4 | openssl base64 -A)
HASH_BINARY=$(
    {
        printf '%s' "$SALT" | openssl base64 -d -A
        printf '%s' "$RABBITMQ_DEFAULT_PASS"
    } | openssl dgst -sha256 -binary
)
RABBITMQ_PASSWORD_HASH=$(
    {
        printf '%s' "$SALT" | openssl base64 -d -A
        printf '%s' "$HASH_BINARY"
    } | openssl base64 -A
)

cat > "$1/../.env" <<EOF
# mysql container
MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}
MYSQL_DATABASE=esit_db
MYSQL_USER=esit_db
MYSQL_PASSWORD=${MYSQL_PASSWORD}

# rabbitmq container
RABBITMQ_DEFAULT_HOST=rabbitmq
RABBITMQ_DEFAULT_PORT=5672
RABBITMQ_DEFAULT_USER=${RABBITMQ_DEFAULT_USER}
RABBITMQ_DEFAULT_PASS=${RABBITMQ_DEFAULT_PASS}
RABBITMQ_MANAGEMENT.LOAD_DEFINITIONS=/etc/rabbitmq/rabbitmq-definitions.json

# worker container
WORKER_MAX_COMPILATION_MEMORY=1028
WORKER_MAX_COMPILATION_TIME=60

# network settings
NETWORK_PRIVATE_KEY="${NETWORK_PRIVATE_KEY}"
EOF

DEFINITIONS_FILE="$1/definitions.rabbitmq.json.example"

TEMP_FILE=$(mktemp)

openssl rand -hex 4 >/dev/null

sed "s|\"password_hash\": \"[^\"]*\"|\"password_hash\": \"${RABBITMQ_PASSWORD_HASH}\"|" \
    "$DEFINITIONS_FILE" > "$TEMP_FILE"

mv "$TEMP_FILE" "$DEFINITIONS_FILE"
chmod 600 "$1/../.env"

echo "Successfully generated .env and RabbitMQ definitions."
