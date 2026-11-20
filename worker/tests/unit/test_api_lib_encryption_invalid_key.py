from api import lib


valid_key = "0123456789abcdef0123456789abcdef"

assert lib.encrypt("test", "not-hex") is False, (
    "Encryption should reject a non-hexadecimal key"
)

assert lib.encrypt("test", "0123456789abcdef") is False, (
    "Encryption should reject a key with an incorrect length"
)

encrypted = lib.encrypt("test", valid_key)
assert encrypted is not False, "Control encryption unexpectedly failed"

assert lib.decrypt(encrypted, "not-hex") is False, (
    "Decryption should reject a non-hexadecimal key"
)

assert lib.decrypt(
    encrypted,
    "abcdef0123456789abcdef0123456789",
) is False, "Decryption should reject a different key"
