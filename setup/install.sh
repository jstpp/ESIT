SCRIPT_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )

# Download default portal template
git clone https://github.com/jstpp/ESITDefaultTemplate.git ./public/portal

# Create missing files
chmod -R 0777 "$SCRIPT_DIR/../public/img"
mkdir "$SCRIPT_DIR/../public/img/articles/"
mkdir "$SCRIPT_DIR/../public/img/articles/header/"
mkdir "$SCRIPT_DIR/../public/include/resources/"
chmod -R 0777 "$SCRIPT_DIR/../public/include/resources"
mkdir "$SCRIPT_DIR/../public/img/problemsets/"
mkdir "$SCRIPT_DIR/../public/img/problemsets/header/"
mkdir "$SCRIPT_DIR/../public/img/plugins/"
mkdir "$SCRIPT_DIR/../include/plugins/"
mkdir "$SCRIPT_DIR/../include/worker/alg"
chmod -R 0777 "$SCRIPT_DIR/../include/worker/alg"
mkdir "$SCRIPT_DIR/../include/worker/ctf"
chmod -R 0777 "$SCRIPT_DIR/../include/worker/ctf"
mkdir "$SCRIPT_DIR/../include/worker/solutions"
chmod -R 0777 "$SCRIPT_DIR/../include/worker/solutions"

# Run docker
docker compose -f "$SCRIPT_DIR/../compose.yaml" up --force-recreate --build