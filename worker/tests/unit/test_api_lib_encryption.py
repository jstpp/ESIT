from api import lib

encrypted = lib.encrypt("123^_#gęślą我+=,./", "0123456789abcdef0123456789abcdef")
decrypted = lib.decrypt(encrypted, "0123456789abcdef0123456789abcdef")

assert (
    decrypted == "123^_#gęślą我+=,./"
), decrypted  # f"{decrypted}/=123^_#gęślą我+=,./"
