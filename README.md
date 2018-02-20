docker build -t messagebus-image .
docker run -itd --rm --name messagebus -v "$PWD":/var/www/ messagebus-image