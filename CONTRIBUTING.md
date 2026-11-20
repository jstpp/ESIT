# Contributing
The goal of this document is to provide everything you need to start contributing to ESIT.

Remember that there are many ways to contribute other than writing code: writing tutorials or blog posts, building the documentation, submitting bug reports and feature requests...

If ESIT does not offer optimized support for your language, you can add your language too! For this purpose you can use for example [poedit desktop app](https://poedit.com/download/).

It is assumed that when contributing, you are familiar with [GitHub](https://docs.github.com/en/) and the [PR (Pull Requests)](https://docs.github.com/en/pull-requests/reference/pull-requests) workflow.

## Table of contents
- [Use of generative AI tools](#use-of-generative-ai-tools)
- [System architecture](#system-architecture)

## Use of generative AI tools
**It is required that you transparently disclose any usage of generative AI tooling (GitHub Copilot, ChatGPT, Claude Code, Cursor, etc.) in contributions, if it was used to generate code.**

## System architecture

```mermaid
flowchart TB
    User([User / Browser])

    subgraph WEB["Web Application Layer"]
        PHP["PHP Frontend Monolith<br/>public/app/index.php"]
        CORE["Core / Routing / Authentication<br/>include/app/core.php"]
    end

    subgraph DATA["Data Layer"]
        DB[("MySQL Database")]
    end

    subgraph QUEUE["Message Broker"]
        MQ[["RabbitMQ"]]
    end

    subgraph EXEC["Execution Layer - Distributed Workers"]
        WORKER["Python Worker Daemon<br/>worker/mq_receiver.py"]

        subgraph SANDBOX["Isolated Execution Environment"]
            JAIL["nsjail / Landlock Sandbox"]
            COMPILER["Language Compilers<br/>Code Execution"]
        end
    end

    User -->|"HTTP Requests"| PHP
    PHP --> CORE
    CORE <-->|"Read / Write"| DB

    CORE -->|"Submit Solution"| MQ
    MQ -->|"Dispatch Task"| WORKER
    WORKER -->|"Execute Securely"| JAIL
    JAIL --> COMPILER

    COMPILER -->|"Execution Result"| WORKER
    WORKER -->|"Return Result"| CORE
    WORKER -->|"Submission Status"| CORE
    CORE -->|"HTTP Response"| User

    style WEB fill:#e8f1ff,stroke:#3973ac,color:#1a1a1a
    style DATA fill:#e8f5e9,stroke:#388e3c,color:#1a1a1a
    style QUEUE fill:#fff3cd,stroke:#d6a329,color:#1a1a1a
    style EXEC fill:#f3e8ff,stroke:#8854b8,color:#1a1a1a
    style SANDBOX fill:#ffe8e8,stroke:#c44d4d,color:#1a1a1a
```