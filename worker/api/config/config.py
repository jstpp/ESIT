import os

from dotenv import load_dotenv

load_dotenv()

# General settings
network_private_key = os.getenv("NETWORK_PRIVATE_KEY")
web_url = os.getenv("NETWORK_WEB_URL")

# Performance settings
worker_max_compilation_memory = int(os.getenv("WORKER_MAX_COMPILATION_MEMORY"))
worker_max_compilation_time = int(os.getenv("WORKER_MAX_COMPILATION_TIME"))

# RabbitMQ credentials
rabbitmq_host = os.getenv("RABBITMQ_DEFAULT_HOST")
rabbitmq_port = os.getenv("RABBITMQ_DEFAULT_PORT")
rabbitmq_user = os.getenv("RABBITMQ_DEFAULT_USER")
rabbitmq_password = os.getenv("RABBITMQ_DEFAULT_PASS")
