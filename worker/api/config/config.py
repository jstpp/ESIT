import os
from dotenv import load_dotenv, dotenv_values
load_dotenv()

# General settings
network_private_key = os.getenv("NETWORK_PRIVATE_KEY")

# Performance settings
worker_max_compilation_memory = int(os.getenv("WORKER_MAX_COMPILATION_MEMORY"))
worker_max_compilation_time = int(os.getenv("WORKER_MAX_COMPILATION_TIME"))

# RabbitMQ credentials
rabbitmq_user = os.getenv("RABBITMQ_DEFAULT_USER")
rabbitmq_password = os.getenv("RABBITMQ_DEFAULT_PASS")
