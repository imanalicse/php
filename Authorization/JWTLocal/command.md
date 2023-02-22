#### OpenSSL is one of the most popular libraries for key creation and management
## generate private key
openssl genrsa -out config/jwt.key 1024
## generate public key
openssl rsa -in config/jwt.key -outform PEM -pubout -out config/jwt.pem

#### Or 
## Generate a private key
openssl genpkey -algorithm RSA -out private_key.pem -pkeyopt rsa_keygen_bits:2048
## Derive the public key from the private key
openssl rsa -pubout -in private_key.pem -out public_key.pem