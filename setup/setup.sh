#!/usr/bin/env bash

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[1;34m'
NC='\033[0m'

if [ -z "$1" ]; then
    echo -e "Usage: setup.sh <parameters>"
else
    case "$1" in
        --install | -i)
            echo -e "   "
            echo -e "   ${YELLOW}+=============================================================+"
            echo -e "   |                                                             |"
            echo -e "   |              ${BLUE}You are about to run an .sh script${YELLOW}             |"
            echo -e "   |                  ${BLUE}in your host's environment${YELLOW}                 |"
            echo -e "   |                                                             |"
            echo -e "   +=============================================================+"
            echo -e "   "
            echo -e "   ${YELLOW}+=============================================================+"
            echo -e "   |                                                             |"
            echo -e "   | ${BLUE}This includes:${YELLOW}                                              |"
            echo -e "   ${YELLOW}| ├──${BLUE} Generating a custom .env file in downloaded repo${YELLOW}        |"
            echo -e "   ${YELLOW}| ├──${BLUE} Downloading github repository with portal template:${YELLOW}     |"
            echo -e "   ${YELLOW}| |${BLUE}   https://github.com/jstpp/ESITDefaultTemplate.git${YELLOW}        |"
            echo -e "   ${YELLOW}| ├──${BLUE} Adjusting file permissions to the content of this${YELLOW}       |"
            echo -e "   ${YELLOW}| |${BLUE}   repo${YELLOW}                                                    |"
            echo -e "   ${YELLOW}| ├──${BLUE} Creating directories for stuff like storage etc.${YELLOW}        |"
            echo -e "   ${YELLOW}| ├──${BLUE} Running docker compose build (--force-recreate --build)${YELLOW} |"
            echo -e "   |                                                             |"
            echo -e "   +=============================================================+"
            echo -e "   "
            echo -e "   ${BLUE}If you don't feel comfortable with it, or you don't"
            echo -e "   have ${YELLOW}openssl installed${BLUE} feel free to set the app up manually.${NC}"
            echo -e "   "
            sleep 5

            SCRIPT_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )

            if [ ! -f "$SCRIPT_DIR/../.env" ]; then
                "$SCRIPT_DIR/generate_env.sh" "$SCRIPT_DIR"
            else
                echo ".env already exists. Keeping existing credentials."
            fi

            # Download default portal template
            rm -Rf "$SCRIPT_DIR/../public/portal"
            rm -Rf "$SCRIPT_DIR/../include/portal"
            git clone https://github.com/jstpp/ESITDefaultTemplate.git "$SCRIPT_DIR/../public/portal"
            cp -r "$SCRIPT_DIR/../public/portal/img/"* "$SCRIPT_DIR/../public/img/"

            find "$SCRIPT_DIR/../setup"   -type d -exec chmod 0755 {} +
            find "$SCRIPT_DIR/../setup"   -type f -exec chmod 0744 {} +

            find "$SCRIPT_DIR/../include" -type d -exec chmod 0755 {} +
            find "$SCRIPT_DIR/../include" -type f -exec chmod 0644 {} +

            find "$SCRIPT_DIR/../public"  -type d -exec chmod 0755 {} +
            find "$SCRIPT_DIR/../public"  -type f -exec chmod 0644 {} +

            find "$SCRIPT_DIR/../locale"  -type d -exec chmod 0755 {} +
            find "$SCRIPT_DIR/../locale"  -type f -exec chmod 0644 {} +

            find "$SCRIPT_DIR/../worker"  -type d -exec chmod 0755 {} +
            find "$SCRIPT_DIR/../worker"  -type f -exec chmod 0644 {} +

            find "$SCRIPT_DIR/../public/portal"  -type d -exec chmod 0755 {} +
            find "$SCRIPT_DIR/../public/portal"  -type f -exec chmod 0644 {} +

            # Create missing directories
            mkdir "$SCRIPT_DIR/../public/img/articles/"
            mkdir "$SCRIPT_DIR/../public/img/articles/header/"
            mkdir "$SCRIPT_DIR/../public/img/articles/content/"
            mkdir "$SCRIPT_DIR/../public/include/resources/"
            mkdir "$SCRIPT_DIR/../public/include/js/"
            mkdir "$SCRIPT_DIR/../public/img/problemsets/"
            mkdir "$SCRIPT_DIR/../public/img/problemsets/header/"
            mkdir "$SCRIPT_DIR/../public/img/plugins/"
            mkdir "$SCRIPT_DIR/../include/plugins/"
            mkdir "$SCRIPT_DIR/../include/worker/alg"
            mkdir "$SCRIPT_DIR/../include/worker/ctf"
            mkdir "$SCRIPT_DIR/../include/worker/solutions"
            mkdir "$SCRIPT_DIR/../include/storage"
            mkdir "$SCRIPT_DIR/../include/storage/video"

            chmod -R 0750 "$SCRIPT_DIR/../public/img"
            chmod -R 0750 "$SCRIPT_DIR/../public/include/resources"
            chmod -R 0755 "$SCRIPT_DIR/../public/include/js"
            chmod -R 0750 "$SCRIPT_DIR/../public/img/plugins/"
            chmod -R 0775 "$SCRIPT_DIR/../include/plugins/"
            chmod -R 0750 "$SCRIPT_DIR/../include/worker/alg"
            chmod -R 0750 "$SCRIPT_DIR/../include/worker/ctf"
            chmod -R 0750 "$SCRIPT_DIR/../include/worker/solutions"
            chmod -R 0750 "$SCRIPT_DIR/../include/storage/video"

            # Run docker
            H_UID=$(id -u) H_GID=$(id -g) docker compose -f "$SCRIPT_DIR/../compose.yaml" up --force-recreate --build
            return
            ;;

        --purge | -p)
            echo -e "   "
            echo -e "   ${YELLOW}+=============================================================+"
            echo -e "   |                                                             |"
            echo -e "   |              ${BLUE}You are about to run an .sh script${YELLOW}             |"
            echo -e "   |                  ${BLUE}in your host's environment${YELLOW}                 |"
            echo -e "   |                                                             |"
            echo -e "   +=============================================================+"
            echo -e "   "
            echo -e "   ${YELLOW}+=============================================================+"
            echo -e "   |                                                             |"
            echo -e "   | ${BLUE}This includes:${YELLOW}                                              |"
            echo -e "   ${YELLOW}| ├──${BLUE} Removing files from specified directories${YELLOW}               |"
            echo -e "   |                                                             |"
            echo -e "   +=============================================================+"
            echo -e "   "
            echo -e "   ${BLUE}If you don't feel comfortable with it, feel free to set"
            echo -e "   the app up manually.${NC}"
            echo -e "   "
            sleep 5

            SCRIPT_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )

            rm -r "$SCRIPT_DIR/../worker/solutions/"*
            rm -r "$SCRIPT_DIR/../worker/inout/"*
            rm -r "$SCRIPT_DIR/../worker/logs/"*
            rm -r "$SCRIPT_DIR/../include/worker/alg/"*
            rm -r "$SCRIPT_DIR/../include/worker/ctf/"*
            rm -r "$SCRIPT_DIR/../include/worker/solutions/"*

            return
            ;;

        --regenerate-env | -e)
            echo -e "   "
            echo -e "   ${YELLOW}+=============================================================+"
            echo -e "   |                                                             |"
            echo -e "   |              ${BLUE}You are about to run an .sh script${YELLOW}             |"
            echo -e "   |                  ${BLUE}in your host's environment${YELLOW}                 |"
            echo -e "   |                                                             |"
            echo -e "   +=============================================================+"
            echo -e "   "
            echo -e "   ${YELLOW}+=============================================================+"
            echo -e "   |                                                             |"
            echo -e "   | ${BLUE}This includes:${YELLOW}                                              |"
            echo -e "   ${YELLOW}| ├──${BLUE} Generating new .env file                         ${YELLOW}       |"
            echo -e "   |                                                             |"
            echo -e "   +=============================================================+"
            echo -e "   "
            echo -e "   ${BLUE}If you don't feel comfortable with it, feel free to set"
            echo -e "   the app up manually.${NC}"
            echo -e "   "
            sleep 5

            SCRIPT_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )
            "$SCRIPT_DIR/generate_env.sh" "$SCRIPT_DIR"

            return
            ;;
    esac
fi