# generate private key
openssl genrsa -out config/jwt.key 1024
# generate public key
openssl rsa -in config/jwt.key -outform PEM -pubout -out config/jwt.pem