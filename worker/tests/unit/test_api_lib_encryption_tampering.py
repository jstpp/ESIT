from api import lib


key = "0123456789abcdef0123456789abcdef"
encrypted = lib.encrypt("ESIT AES-GCM integrity test", key)

assert encrypted is not False, "Encryption unexpectedly failed"

tampered = list(encrypted)
tampered[0] = bytes([tampered[0][0] ^ 1]) + tampered[0][1:]

decrypted = lib.decrypt(tampered, key)

assert decrypted is False, (
    "AES-GCM accepted a modified ciphertext"
)
