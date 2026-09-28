SCRIPT_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )

find "$SCRIPT_DIR/../setup"   -type d -exec chmod 0755 {} +
find "$SCRIPT_DIR/../setup"   -type f -exec chmod 0644 {} +

find "$SCRIPT_DIR/../include" -type d -exec chmod 0755 {} +
find "$SCRIPT_DIR/../include" -type f -exec chmod 0644 {} +

find "$SCRIPT_DIR/../public"  -type d -exec chmod 0755 {} +
find "$SCRIPT_DIR/../public"  -type f -exec chmod 0644 {} +

find "$SCRIPT_DIR/../locale"  -type d -exec chmod 0755 {} +
find "$SCRIPT_DIR/../locale"  -type f -exec chmod 0644 {} +

find "$SCRIPT_DIR/../worker"  -type d -exec chmod 0755 {} +
find "$SCRIPT_DIR/../worker"  -type f -exec chmod 0644 {} +

# Download default portal template
rm -Rf "$SCRIPT_DIR/../public/portal"
rm -Rf "$SCRIPT_DIR/../include/portal"
git clone https://github.com/jstpp/ESITDefaultTemplate.git "$SCRIPT_DIR/../public/portal"
chmod -R 0777 "$SCRIPT_DIR/../public/portal"
cp -r "$SCRIPT_DIR/../public/portal/img/"* "$SCRIPT_DIR/../public/img/"
cp -n "$SCRIPT_DIR/../setup/.env.example" "$SCRIPT_DIR/../.env"

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

chmod -R 0777 "$SCRIPT_DIR/../public/img"
chmod -R 0777 "$SCRIPT_DIR/../public/include/resources"
chmod -R 0755 "$SCRIPT_DIR/../public/include/js"
chmod -R 0777 "$SCRIPT_DIR/../public/img/plugins/"
chmod -R 0777 "$SCRIPT_DIR/../include/plugins/"
chmod -R 0777 "$SCRIPT_DIR/../include/worker/alg"
chmod -R 0777 "$SCRIPT_DIR/../include/worker/ctf"
chmod -R 0777 "$SCRIPT_DIR/../include/worker/solutions"

# Run docker
docker compose -f "$SCRIPT_DIR/../compose.yaml" up --force-recreate --build